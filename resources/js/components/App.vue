<template>
    <div id="app">
        <s-app-nav>
            <s-link href="/" rel="home">Home</s-link>
            <s-link href="/abandoned-carts">Abandoned Carts</s-link>
            <s-link href="/automations">Automations</s-link>
        </s-app-nav>
        <router-view :key="$route.fullPath" />
    </div>
</template>

<script>
export default {
    name: 'App',
    props: {
        shopDomain: {
            type: String,
            default: ''
        },
        appName: {
            type: String,
            default: 'Shopify App'
        }
    }
}
</script>

<style>
#app {
    min-height: 100vh;
}
</style>
