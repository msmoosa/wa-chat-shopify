<template>
    <s-page title="Products">
        <s-section>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <s-heading>Abandoned Carts</s-heading>
                <s-button @click="getCheckouts()" :loading="state === 'loading'">Refresh</s-button>
            </div>
            <s-text>Look at your abandoned carts and recover them.</s-text>
            <s-table :data="abandonedCarts" :columns="columns">
                <s-table-header-row>
                    <s-table-header>Name</s-table-header>
                    <s-table-header>Amount</s-table-header>
                    <s-table-header>Created At</s-table-header>
                    <s-table-header>Message</s-table-header>
                    <s-table-header>Checkout Id</s-table-header>
                    <s-table-header>Tags</s-table-header>
                    <s-table-header>Action</s-table-header>
                </s-table-header-row>
                <s-table-body>
                    <s-table-row v-for="checkout in checkouts" :key="checkout.id">
                        <s-table-cell>{{ checkout.customer_name }}</s-table-cell>
                        <s-table-cell>{{ checkout.total_price }}</s-table-cell>
                        <s-table-cell>{{ new Date(checkout.checkout_created_at).toLocaleString() }}</s-table-cell>
                        <s-table-cell>{{ checkout.is_sent ? 'Sent' : 'Not Sent' }}</s-table-cell>
                        <s-table-cell>{{ checkout.shopify_checkout_id }}</s-table-cell>
                        <s-table-cell>{{ checkout.tags }}</s-table-cell>
                        <s-table-cell>
                            <s-select v-if="!!checkout.phone_number" label="Date range"
                                @change="sendMessage(checkout, $event.target.value)">
                                <s-option>Send Message</s-option>
                                <s-option value="reminder1">Reminder 1</s-option>
                                <s-option value="discount">Discount</s-option>
                            </s-select>
                            <s-text v-else>No phone number</s-text>
                        </s-table-cell>
                    </s-table-row>
                </s-table-body>
            </s-table>
        </s-section>
    </s-page>
</template>
<script>
export default {
    name: 'AbandonedCarts',
    props: {
        shopDomain: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            checkouts: [],
            state: 'loading'
        }
    },
    mounted() {
        this.getCheckouts();
    },
    methods: {
        async getCheckouts() {
            try {
                this.state = 'loading';
                const response = await fetch('/api/abandonedCheckouts', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                if (!response.ok) {
                    throw new Error('Failed to load abandoned checkouts');
                }
                const result = await response.json();
                if (result.success && result.data) {
                    this.checkouts = result.data;
                    this.state = 'loaded';
                } else {
                    throw new Error('Failed to load abandoned checkouts');
                }
            } catch (error) {
                console.error('Error loading abandoned checkouts:', error);
                this.state = 'error';
            }
        }, async sendMessage(checkout, templateId) {
            if (!templateId) {
                return;
            }
            alert(templateId);
            try {
                const response = await fetch(`/api/checkouts/${checkout.id}/send-message`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                // open a window to send message to whatsapp with template content
                let phoneNumber = checkout.phone_number.replace(/^00/, '').replace(/^\+/, '');
                window.open(`https://wa.me/${phoneNumber}?text=${templateId}`, '_blank');
            } catch (error) {
                console.error('Error sending message:', error);
            }
        }
    },
}
</script>
