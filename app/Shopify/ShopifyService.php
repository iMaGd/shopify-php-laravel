<?php

namespace App\Shopify;

use Illuminate\Support\Facades\Log;
use Shopify\Context;
use Shopify\Exception\MissingArgumentException;

class ShopifyService
{

    public function __construct()
    {
        try {
            Context::initialize(
                config('shopify.api_client_id'),
                config('shopify.api_client_secret'),
                config('shopify.api_scope'),
                config('shopify.host_url'),
                new SessionRepository(),
                config('shopify.api_version'),
                config('shopify.app_embedded')
            );
        } catch (MissingArgumentException $e) {
            Log::debug( 'Could not initialize shopify : ' . $e->getMessage() );
        }
    }

    /**
     * API Instance
     *
     * @return API
     */
    public function api(): API
    {
        return app('shopify.service.api');
    }

    /**
     * Auth Instance
     *
     * @return Auth
     */
    public function auth(): Auth
    {
        return app('shopify.service.auth');
    }

    /**
     * OAuth Instance
     *
     * @return OAuth
     */
    public function OAuth(): OAuth
    {
        return app('shopify.service.oauth');
    }

    /**
     * SessionRepository instance
     *
     * @return SessionRepository
     */
    public function sessionRepository(): SessionRepository
    {
        return app('shopify.model.session.repo');
    }

}
