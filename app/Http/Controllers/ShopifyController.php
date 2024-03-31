<?php

namespace App\Http\Controllers;

use App\Application\App;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;


class ShopifyController extends Controller
{
    /**
     * Starter point of Shopify App
     *
     * @param Request $request
     *
     * @return Application|View
     */
    public function redirect( Request $request )
    {
         return $this->app( $request );
    }

    /**
     * Process callback request and store session and cookies and user info
     *
     * @param  Request $request
     * @return mixed
     */
    public function callback( Request $request )
    {
        return App::shopify()->OAuth()->callback();
    }

    /**
     * App start page
     *
     * @param Request $request
     * @param string $name
     *
     * @return Application|View
     */
    public function app( Request $request, string $name = 'Home' )
    {
        return view('shopify/app', $this->defaultViewParams( $request, [ 'name' => $name ] ));
    }

    /**
     * App dashboard
     *
     * @param Request $request
     *
     * @return Application|View
     */
    public function dashboard( Request $request )
    {
        return view('shopify/app', $this->defaultViewParams( $request, [ 'name' => "Dashboard" ] ));
    }

    /**
     * App editor
     *
     * @param Request $request
     *
     * @return Application|View
     */
    public function editor( Request $request )
    {
        return view('shopify/app', $this->defaultViewParams( $request, [ 'name' => "Editor" ] ));
    }

    /**
     * App setting page
     *
     * @param Request $request
     *
     * @return Application|View
     */
    public function setting( Request $request )
    {
        return view('shopify/app', $this->defaultViewParams( $request, [ 'name' => "Setting" ] ));
    }

    /**
     * App ExitIframe
     *
     * @param Request $request
     *
     * @return Application|View
     */
    public function exitIframe( Request $request )
    {
        return view('shopify/exitIframe', $this->defaultViewParams( $request, [ 'name' => "EditIframe" ] ));
    }

    /**
     * Default view params
     *
     * @param Request $request
     * @param array   $default
     *
     * @return array
     */
    public function defaultViewParams( Request $request, array $default = [] ){
        $shopifySession = $request->attributes->get('shopifySession', false);

        return array_merge( $default, [
            'isShopify'     => (bool) $shopifySession,
            'apiToken'      => $request->attributes->get( 'sanctumToken' ) // sanctum personal access token
        ]);
    }
}
