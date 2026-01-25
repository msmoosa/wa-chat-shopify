<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Shopify Polaris CSS -->
    <script src="https://cdn.shopify.com/shopifycloud/app-bridge.js"></script>
    <script src="https://cdn.shopify.com/shopifycloud/polaris.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div id="app" data-props="{{ json_encode([
            'shop' => Auth::user(),
            'shopDomain' => $shopDomain ?? Auth::user()->name ?? '',
            'products' => $products ?? [],
            'error' => $error ?? ''
        ]) }}"></div>

</body>

</html>