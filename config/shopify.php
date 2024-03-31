<?php


return [
    /*
    |--------------------------------------------------------------------------
    | Shopify App Name
    |--------------------------------------------------------------------------
    |
    | This option simply lets you display your app's name.
    |
    */
    'app_name' => env('SHOPIFY_APP_NAME', 'Shopify App'),

    /*
    |--------------------------------------------------------------------------
    | Shopify API Key
    |--------------------------------------------------------------------------
    |
    | This option is for the app's API key.
    |
    */
    'api_client_id' => env('SHOPIFY_APP_CLIENT_ID', ''),
    /*
    |--------------------------------------------------------------------------
    | Shopify API Secret
    |--------------------------------------------------------------------------
    |
    | This option is for the app's API secret.
    |
    */
    'api_client_secret' => env('SHOPIFY_APP_CLIENT_SECRET', ''),
    'api_version' => env('SHOPIFY_API_VERSION', '2024-01'),

    /*
    |--------------------------------------------------------------------------
    | Shopify API Scopes
    |--------------------------------------------------------------------------
    |
    | This option is for the scopes your application needs in the API.
    |
    */
    'api_scope' => env('SHOPIFY_API_SCOPES', ''),

    /*
    |--------------------------------------------------------------------------
    | Shopify APP Host URL
    |--------------------------------------------------------------------------
    |
    | while using ngrok or expose for tunneling, we need to define shared public domain as host here
    |
    */
    'host_url' => env('SHOPIFY_HOST_URL', env('APP_URL') ),


    'app_embedded' => env('SHOPIFY_APP_EMBEDDED', 1 ),

    /*
    |--------------------------------------------------------------------------
    | Route names
    |--------------------------------------------------------------------------
    |
    | This option allows you to override the package's built-in route names.
    | This can help you avoid collisions with your existing route names.
    |
    */
    'route' => [
        'auth_redirect' => env('SHOPIFY_ROUTE_AUTH_REDIRECT', 'auth/shopify/redirect'),
        'auth_callback' => env('SHOPIFY_ROUTE_AUTH_CALLBACK', 'auth/shopify/callback'),
        'webhook'       => env('SHOPIFY_ROUTE_WEBHOOK'      , 'webhook'),
    ]

];
