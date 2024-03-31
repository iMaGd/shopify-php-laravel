<?php

namespace App\Shopify;

use App\Models\User;
use App\Application\App;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Shopify\Auth\OAuth as ShopifyOAuth;
use Shopify\Context;
use Shopify\Utils;
use Shopify\Exception\CookieSetException;
use Shopify\Exception\PrivateAppException;
use Shopify\Exception\SessionStorageException;
use Shopify\Exception\UninitializedContextException;

class OAuth
{

    /**
     * Performs the OAuth callback steps, checking the returned parameters and fetching the access token,
     * preparing the session for further usage. If successful, will be redirected to shopify embedded url
     *
     * @return mixed
     */
    public function callback() {
        try {
            $this->user();
            return redirect( Utils::getEmbeddedAppUrl( request()->query("host", null) ) );

        } catch ( \Exception $e ) {
            Log::info( $e->getMessage() . " " . $e->getLine() );
            return null;
        }
    }

    /**
     * Performs the OAuth callback steps, checking the returned parameters and fetching the access token,
     * preparing the session for further usage. If successful, the updated user is returned.
     *
     * @return User|null
     */
    public function user() {

        try {
            // Stores OAuth cookie in response cookie
            $session = ShopifyOAuth::callback(
                request()->cookie(),
                request()->query(),
                ['App\Shopify\CookieHelper', 'saveShopifyCookie']
            );

            // Create user and store shop info in the session
            $shopInfo = App::shopify()->api()->getShopDetails( $session->getShop(), $session->getAccessToken() );

            // create and store user info
            $user = $this->updateOrCreateUser( $shopInfo );

            if( $user ){
                $shopInfo['user_id'] = $user->id;
                App::shopify()->sessionRepository()->storeShopInfo( $session->getId(), $shopInfo );
                Auth::login( $user );
            }

            return $user;

        } catch ( \Exception $e ) {
            Log::info( $e->getMessage() . " " . $e->getLine() );
            return null;
        }
    }

    private function updateOrCreateUser( array $shopInfo = [] ){
        return User::updateOrCreate([
            'email' => "shopify@{$shopInfo['domain']}"
        ], [
            'name'  => $shopInfo['shop_owner'],
            'password' => Str::password(12),
            'email_verified_at' => now()
        ]);
    }

    /**
     * Initializes an OAuth process and returns the shopify authorization url
     *
     * @param bool $isOnline
     *
     * @return RedirectResponse|Redirector|null
     */
    public function redirect( bool $isOnline = false )
    {
        $shop = Utils::sanitizeShopDomain( request()->query("shop") );

        try{
            if ( Context::$IS_EMBEDDED_APP && request()->query("embedded", false) === "1" ) {
                $redirectUrl = $this->getClientSideAuthConsentUrl( $shop, request()->query() );
            } else {
                $redirectUrl = $this->getServerSideAuthConsentUrl( $shop, $isOnline );
            }
        } catch ( \Exception $e ) {
            Log::info( $e->getMessage() . " " . $e->getLine() );
            return null;
        }

        return redirect( $redirectUrl );
    }

    /**
     * Get server side auth url
     *
     * @throws CookieSetException
     * @throws UninitializedContextException
     * @throws PrivateAppException
     * @throws SessionStorageException
     */
    private function getServerSideAuthConsentUrl(string $shop, bool $isOnline): string
    {
        return ShopifyOAuth::begin(
            $shop,
            config('shopify.route.auth_callback'),
            $isOnline,
            ['App\Shopify\CookieHelper', 'saveShopifyCookie'],
        );
    }

    /**
     * Retrieves client side redirect URL
     *
     * @param $shop
     * @param array $query
     * @return string
     */
    private function getClientSideAuthConsentUrl( $shop, array $query ): string
    {
        $appHost = Context::$HOST_NAME;

        $redirectUri = urlencode("https://$appHost/". config('shopify.route.auth_redirect') ."?shop=$shop");
        $queryString = http_build_query( array_merge( $query, ["redirectUri" => $redirectUri] ) );

        return "/exitiframe?$queryString";
    }

}
