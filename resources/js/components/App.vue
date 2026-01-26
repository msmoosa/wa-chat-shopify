<template>
    <div id="app">
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
