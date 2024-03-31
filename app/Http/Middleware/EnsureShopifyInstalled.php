<?php

namespace App\Http\Middleware;

use App\Application\App;
use App\Shopify\Auth;
use Closure;
use Shopify\Utils;
use App\Application\Depicter;
use App\Models\ShopifySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class EnsureShopifyInstalled
{
    /**
     * Check if the app is already installed on the store or not
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle( Request $request, Closure $next )
    {
        try {
            // only process if it's the main shopify installation route
            if( $request->path() !== config('shopify.route.auth_redirect') ){
                return $next( $request );
            }

            // skip if is not a request from shopify
            if( ! $shop = $request->query('shop') ){
                return $next( $request );
            }

            App::shopify();

            $shopDomain = Utils::sanitizeShopDomain( $shop );

            // Delete any previously created OAuth sessions that were not completed (don't have an access token)
            ShopifySession::where('shop', $shopDomain)->where('access_token', '')->orWhereNull('access_token')->delete();

            $appInstalled = $shopDomain && ShopifySession::where('shop', $shopDomain )->where( 'access_token', '<>', null )->exists();

            // If not start before, start shopify installation flow
            if( ! $appInstalled ){
                return App::shopify()->OAuth()->redirect();
            }

        } catch ( \Exception $e ) {
            Log::debug( __CLASS__ . "| " . $e->getMessage() . " " . $e->getLine() );
        }

        return $next( $request );
    }
}
