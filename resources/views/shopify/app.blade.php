<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <meta name="shopify-api-key" content="{{ config( 'shopify.api_client_id' ) }}" />
    <meta name="app-api-token"   content="{{ $apiToken }}" />
    <meta name="csrf-token"      content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'App') }}</title>
    <script src="https://cdn.shopify.com/shopifycloud/app-bridge.js"></script>
</head>
<body>
    <ui-nav-menu>
        <a href="/dashboard">Dashboard</a>
        <a href="/editor">Editor</a>
    </ui-nav-menu>

    <ui-title-bar title="{{ $name }}">
        <button variant="breadcrumb">{{ config('shopify.app_name', 'App') }}</button>
        <button onclick="console.log('Secondary action')">Secondary</button>
        <section label="More">
            <button onclick="console.log('Secondary Action 1')">3rd Action</button>
            <button onclick="console.log('Secondary Action 2')">4th Action</button>
        </section>
        <button variant="primary" onclick="console.log('Primary action')">Primary</button>
    </ui-title-bar>

    <button id="open-picker">Open Products</button>

    <script>
        document.getElementById('open-picker').addEventListener('click', async () => {
            const selected = await shopify.resourcePicker({type: 'product'});
            console.log(selected);
        });
    </script>
</body>
