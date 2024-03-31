<?php

namespace App\Http\Middleware;

use App\Application\App;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Shopify\Clients\Graphql;
use Shopify\Utils;

class AttemptShopifyAuthentication
{

    public const TEST_GRAPHQL_QUERY = <<<QUERY
    {
        shop {
            name
        }
    }
    QUERY;

    /**
     * Attempts to authenticate a shopify user and load corresponding shopify session
     *
     * @param Request $request
     * @param Closure $next
     * @param string $accessMode
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function handle( Request $request, Closure $next, string $accessMode = 'offline' )
    {
        // skip auth for shopify auth callback
        if( $request->path() === config('shopify.route.auth_callback') ){
            App::shopify();
            return $next( $request );
        }

        // if it's a shopify user set the session and initialize shopify service
        if( $request->user() ){
            if( $shop = $request->user()?->shopSession?->shop ){
                App::shopify();
                $session = Utils::loadOfflineSession( $shop );
                // set the corresponding shopify session in request
                $request->attributes->set( 'shopifySession', $session );
            }
            Log::debug('Shopify USER already Authenticated!');

            return $next( $request );
        }

        // skip if is not a valid shopify request
        if( ! $request->query('shop') ||
            ! Utils::validateHmac( $request->all(), config( 'shopify.api_client_secret') )
        ){
            return $next( $request );
        }

        App::shopify();

        if( ! $shop = Utils::sanitizeShopDomain( $request->query('shop', '') ) ){
            return $next( $request );
        }

        $session = Utils::loadOfflineSession( $shop );

        // if( $this->maybeAuthenticateByIdToken( $request, $session, $shop) ){
        //    return $next( $request );
        // }

        if ( $session && $session->isValid() ) {
            // Make a request to ensure the access token is still valid. Otherwise, re-authenticate the user.
            $client = new Graphql( $session->getShop(), $session->getAccessToken() );
            $response = $client->query(self::TEST_GRAPHQL_QUERY);

            // set the shopify session and authenticate the user
            if ( $response->getStatusCode() === 200 ) {
                Log::debug('Shopify USER authenticated by offline access token');
                $request->attributes->set( 'shopifySession', $session );
                if( $userId = $session->getOnlineAccessInfo()?->getId() ?? false ){
                    Auth::loginUsingId( $userId );
                    $request->attributes->set( 'sanctumToken', App::shopify()->auth()->getAccessTokenForUser() );
                }

                return $next( $request );
            }
        }

        return App::shopify()->OAuth()->redirect();
    }


    private function maybeAuthenticateByIdToken( Request $request, $session, $shop )
    {
        try {
            // Try to authorize with id_token
            if ($jwt = $request->query('id_token')) {
                $payload = Utils::decodeSessionToken($jwt);

                if (!$shop && $session) {
                    $shop = $session->getShop();
                }
                // check shop domain in payload with requested one
                if (Utils::sanitizeShopDomain($payload['dest']) === $shop) {

                    if ($session && $session->isValid()) {
                        Log::debug('User Authenticated by shopify ID token');
                        $request->attributes->set('shopifySession', $session);
                        if ($userId = $session->getOnlineAccessInfo()?->getId() ?? false) {
                            Auth::loginUsingId($userId);
                            $request->attributes->set('sanctumToken', App::shopify()->auth()->getAccessTokenForUser());
                        }

                        return true;
                    }
                }
            }

        } catch (\Exception $e) {
            // if the jwt is expired, an error exception will be thrown
            Log::debug('JWT is expired.');
        }

        return false;
    }
}
