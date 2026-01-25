<template>
    <s-page title="Products">
        <s-section>
            <!-- Error Banner -->
            <s-banner
                v-if="error"
                tone="critical"
                title="Error loading products"
                :dismissible="false"
            >
                {{ error }}
            </s-banner>
            
            <!-- Products List -->
            <s-box v-if="products && products.length > 0" padding="base">
                <s-stack direction="vertical" gap="base">
                    <s-text variant="headingMd" as="h3">Product List</s-text>
                    
                    <s-box
                        v-for="product in products"
                        :key="product.id"
                        padding="base"
                        background="surface"
                        border-radius="base"
                    >
                        <s-stack direction="vertical" gap="tight">
                            <s-stack direction="inline" gap="base" justifyContent="spaceBetween" alignItems="center">
                                <s-text variant="bodyMd" fontWeight="semibold">
                                    {{ product.title }}
                                </s-text>
                                <s-badge
                                    :tone="product.status === 'active' ? 'success' : 'info'"
                                >
                                    {{ product.status || 'Unknown' }}
                                </s-badge>
                            </s-stack>
                            
                            <s-stack direction="vertical" gap="extraTight">
                                <s-text v-if="product.vendor" variant="bodySm" tone="subdued">
                                    Vendor: {{ product.vendor }}
                                </s-text>
                                <s-text v-if="product.product_type" variant="bodySm" tone="subdued">
                                    Type: {{ product.product_type }}
                                </s-text>
                            </s-stack>
                        </s-stack>
                    </s-box>
                </s-stack>
            </s-box>
            
            <!-- Empty State -->
            <s-box v-else-if="!error" padding="base">
                <s-stack direction="vertical" gap="base" alignItems="center">
                    <s-text variant="headingMd" as="h3">No products found</s-text>
                    <s-text variant="bodyMd" tone="subdued">
                        There are no products available at this time.
                    </s-text>
                </s-stack>
            </s-box>
        </s-section>
    </s-page>
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
