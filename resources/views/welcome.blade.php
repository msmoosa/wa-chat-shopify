<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    </head>
    <body>
        <!-- You are: (shop domain name) -->
        <p>You are: {{ $shopDomain ?? Auth::user()->name }}</p>

        <h1>Products</h1>
        @if (!empty($error)) {{$error}} @endif
        @foreach ($products as $product)
            <p>{{ $product['title'] }}</p>
        @endforeach

        <ui-title-bar title="Products">
        <button onclick="console.log('Secondary action')">Secondary action</button>
        <button variant="primary" onclick="console.log('Primary action')">
            Primary action
        </button>
        </ui-title-bar>
    </body>
</html>
