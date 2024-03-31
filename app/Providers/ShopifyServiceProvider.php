<?php

namespace App\Providers;

use App\Shopify\API;
use App\Shopify\Auth;
use App\Shopify\OAuth;
use App\Shopify\SessionRepository;
use App\Shopify\ShopifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

class ShopifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        App::bind( 'shopify.manager', function() {
            return new ShopifyService();
        });
        App::bind( 'shopify.service.auth', function() {
            return new Auth();
        });
        App::bind( 'shopify.service.oauth', function() {
            return new OAuth();
        });
        App::bind( 'shopify.service.api', function() {
            return new API();
        });
        App::bind( 'shopify.model.session.repo', function() {
            return new SessionRepository();
        });
    }

    public function boot(){

        // Attempt to retrieve sanctum access token from a query string as well
        Sanctum::$accessTokenRetrievalCallback = function ( Request $request ){

            if( $sanctumToken = $request->bearerToken() ){
                $request->attributes->set( 'sanctumToken', $sanctumToken );
                return $sanctumToken;
            }

            if( $sanctumToken = $request->query( 'xrf' ) ){
                $request->attributes->set( 'sanctumToken', $sanctumToken );
            }

            return $sanctumToken;
        };
    }

}
