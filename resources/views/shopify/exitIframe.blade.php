<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="shopify-api-key" content="{{ config( 'shopify.api_client_id' ) }}" />
    <script src="https://unpkg.com/@shopify/app-bridge@3"></script>
</head>
<body>
<script>
    // the following script redirect the app out of iframe to shopify authorization page
    // for getting a new offline accessToken
    document.addEventListener('DOMContentLoaded', function() {
        const params = new URLSearchParams( location.search );
        const redirectUri = decodeURIComponent( params.get("redirectUri") );
        const url = new URL( redirectUri );

        if ( window.top === window.self ) {
            // If the current window is the 'parent', change the URL by setting location.href
            window.top.location.href = redirectUri;
        } else {
            const AppBridge = window['app-bridge'];
            const createApp = AppBridge.default;
            const app = createApp({
                apiKey: "{{ $shopifyApiKey }}",
                host: (new URLSearchParams(location.search)).get("host")
            });

            const redirect = AppBridge.actions.Redirect.create(app);
            if (
                [location.hostname, "admin.shopify.com"].includes(url.hostname) ||
                url.hostname.endsWith(".myshopify.com")
            ) {
                redirect.dispatch( AppBridge.actions.Redirect.Action.REMOTE, redirectUri );
            }
        }
    });
</script>
</body>
