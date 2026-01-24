<template>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Welcome</h1>
                <p class="text-xl text-gray-600 mb-8">You are: {{ shopDomain }}</p>
                
                <div class="mt-8">
                    <router-link
                        to="/products"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                    >
                        View Products
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Home',
    props: {
        shopDomain: {
            type: String,
            default: ''
        }
    }
}
</script>
