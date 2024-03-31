<?php

namespace App\Application;

use App\Shopify\ShopifyService;

class App extends \Illuminate\Support\Facades\App
{
    public static function shopify(): ShopifyService{
        return app('shopify.manager');
    }
}
