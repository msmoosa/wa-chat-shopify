<template>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Products</h1>
                <p class="text-gray-600">You are: {{ shopDomain }}</p>
            </div>

            <!-- Error Message -->
            <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-800">{{ error }}</p>
            </div>

            <!-- Products List -->
            <div v-if="products && products.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="product in products"
                    :key="product.id"
                    class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
                >
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ product.title }}</h3>
                        <p v-if="product.vendor" class="text-sm text-gray-500 mb-2">Vendor: {{ product.vendor }}</p>
                        <p v-if="product.product_type" class="text-sm text-gray-500 mb-2">Type: {{ product.product_type }}</p>
                        <p v-if="product.status" class="text-xs">
                            <span
                                :class="{
                                    'bg-green-100 text-green-800': product.status === 'active',
                                    'bg-gray-100 text-gray-800': product.status !== 'active'
                                }"
                                class="px-2 py-1 rounded-full"
                            >
                                {{ product.status }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <p class="text-gray-500 text-lg">No products found</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Welcome',
    props: {
        shopDomain: {
            type: String,
            default: ''
        },
        products: {
            type: Array,
            default: () => []
        },
        error: {
            type: String,
            default: ''
        }
    }
}
</script>
