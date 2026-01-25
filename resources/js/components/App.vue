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
    height: 100vh;
    overflow: hidden;
}
</style>
