<template>
    <s-page :title="isEditMode ? 'Edit Automation' : 'Create Automation'">
        <s-section>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <s-heading>{{ isEditMode ? 'Edit Automation' : 'Create Automation' }}</s-heading>
                <s-box>
                    <s-button @click="goBack">Cancel</s-button>
                    <s-button variant="primary" @click="saveAutomation" :loading="saving"
                        :disabled="!automation.name || !automation.trigger" style="margin-left: 8px;">
                        {{ isEditMode ? 'Save' : 'Create' }}
                    </s-button>
                </s-box>
            </div>

            <s-text tone="subdued">
                {{ isEditMode ? 'Update your automation settings.'
                    : 'Create automated workflows to send messages tocustomers based on checkout events.' }}
            </s-text>
        </s-section>

        <s-box v-if="loading" padding="large">
            <s-spinner accessibilityLabel="Loading" size="large-100"></s-spinner>
        </s-box>

        <template v-else>
            {{ automation }}
            <s-section heading="Basic Information">
                <s-text-field :value="automation.name" @input="automation.name = $event.target.value"
                    label="Automation Name" placeholder="Enter automation name" />
                <s-select :value="automation.trigger" @input="automation.trigger = $event.target.value" label="Trigger">
                    <s-option value="abandoned_checkout">Abandoned Checkout</s-option>
                    <s-option value="order_created">Order Created</s-option>
                    <s-option value="order_updated">Order Updated</s-option>
                    <s-option value="checkout_created">Checkout Created</s-option>
                    <s-option value="checkout_updated">Checkout Updated</s-option>
                </s-select>
                <s-switch :checked="automation.is_active" @input="automation.is_active = $event.target.value"
                    label="Enable Automation" />
            </s-section>

            <s-section v-if="isEditMode" heading="Steps">
                <s-text tone="subdued">
                    Automation steps will be configured here. (Coming soon)
                </s-text>
            </s-section>
        </template>
    </s-page>
</template>

<script>
export default {
    name: 'EditAutomation',
    data() {
        return {
            automation: {
                name: '',
                trigger: 'abandoned_checkout',
                is_active: true,
            },
            loading: false,
            saving: false,
            isEditMode: false,
        };
    },
    mounted() {
        const automationId = this.$route.params.id;
        this.isEditMode = !!automationId;

        if (this.isEditMode) {
            this.loadAutomation(automationId);
        }
    },
    methods: {
        async loadAutomation(id) {
            this.loading = true;
            try {
                const response = await fetch(`/api/automations`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error('Failed to load automation');
                }

                const result = await response.json();
                if (result.success && result.data) {
                    const automation = result.data.find(a => a.id === parseInt(id));
                    if (automation) {
                        this.automation = {
                            name: automation.name,
                            trigger: typeof automation.trigger === 'string' ? automation.trigger : automation.trigger.value,
                            is_active: automation.is_active,
                        };
                    } else {
                        this.$router.push('/automations');
                    }
                }
            } catch (error) {
                console.error('Error loading automation:', error);
                this.$router.push('/automations');
            } finally {
                this.loading = false;
            }
        },
        async saveAutomation() {
            if (!this.automation.name || !this.automation.trigger) {
                return;
            }

            this.saving = true;
            try {
                const url = this.isEditMode
                    ? `/api/automations/${this.$route.params.id}`
                    : '/api/automations';

                const method = this.isEditMode ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(this.automation),
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error(`Failed to ${this.isEditMode ? 'update' : 'create'} automation`);
                }

                const result = await response.json();
                if (result.success) {
                    this.$router.push('/automations');
                }
            } catch (error) {
                console.error(`Error ${this.isEditMode ? 'updating' : 'creating'} automation:`, error);
            } finally {
                this.saving = false;
            }
        },
        goBack() {
            this.$router.push('/automations');
        },
    },
};
</script>
