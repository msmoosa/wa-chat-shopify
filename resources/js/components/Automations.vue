<template>
    <s-page title="Automations">
        <s-section>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <s-heading>Automations</s-heading>
                <s-box>
                    <s-button variant="primary" @click="createAutomation">Create Automation</s-button>
                </s-box>
            </div>

            <s-text tone="subdued">
                Create automated workflows to send messages to customers based on checkout events.
            </s-text>
        </s-section>

        <s-section heading="Automations">
            <s-box v-if="loading" padding="large">
                <s-spinner accessibilityLabel="Loading" size="large-100"></s-spinner>
            </s-box>

            <s-table v-else-if="automations.length">
                <s-table-header-row>
                    <s-table-header>Name</s-table-header>
                    <s-table-header>Trigger</s-table-header>
                    <s-table-header>Steps</s-table-header>
                    <s-table-header>Status</s-table-header>
                    <s-table-header>Actions</s-table-header>
                </s-table-header-row>
                <s-table-body>
                    <s-table-row v-for="automation in automations" :key="automation.id">
                        <s-table-cell>
                            <s-text>{{ automation.name }}</s-text>
                        </s-table-cell>
                        <s-table-cell>
                            <s-badge :tone="getTriggerTone(automation.trigger)">
                                {{ formatTrigger(automation.trigger) }}
                            </s-badge>
                        </s-table-cell>
                        <s-table-cell>
                            <s-text>{{ automation.steps?.length || 0 }} step(s)</s-text>
                        </s-table-cell>
                        <s-table-cell>
                            <s-switch :checked="automation.is_active" @input="toggleAutomation(automation)"
                                :loading="updatingId === automation.id" label="" />
                            <s-text tone="subdued" style="margin-left: 8px;">
                                {{ automation.is_active ? 'Active' : 'Inactive' }}
                            </s-text>
                        </s-table-cell>
                        <s-table-cell>
                            <s-stack alignment="trailing" spacing="tight">
                                <s-button size="micro" @click="editAutomation(automation)">
                                    Edit
                                </s-button>
                                <s-button size="micro" tone="critical" variant="plain"
                                    @click="deleteAutomation(automation)" :loading="deletingId === automation.id">
                                    Delete
                                </s-button>
                            </s-stack>
                        </s-table-cell>
                    </s-table-row>
                </s-table-body>
            </s-table>

            <s-box v-else padding="large">
                <s-text tone="subdued">
                    No automations yet. Click "Create Automation" to create your first one.
                </s-text>
            </s-box>
        </s-section>
    </s-page>
</template>

<script>
export default {
    name: 'Automations',
    data() {
        return {
            automations: [],
            loading: false,
            updatingId: null,
            deletingId: null,
        };
    },
    mounted() {
        this.loadAutomations();
    },
    methods: {
        async loadAutomations() {
            this.loading = true;
            try {
                const response = await fetch('/api/automations', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error('Failed to load automations');
                }

                const result = await response.json();
                if (result.success && result.data) {
                    this.automations = result.data;
                }
            } catch (error) {
                console.error('Error loading automations:', error);
            } finally {
                this.loading = false;
            }
        },
        createAutomation() {
            this.$router.push('/automations/new');
        },
        editAutomation(automation) {
            this.$router.push(`/automations/${automation.id}/edit`);
        },
        async toggleAutomation(automation) {
            this.updatingId = automation.id;
            try {
                const response = await fetch(`/api/automations/${automation.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        is_active: !automation.is_active,
                    }),
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error('Failed to update automation');
                }

                const result = await response.json();
                if (result.success && result.data) {
                    // Update local automation
                    const index = this.automations.findIndex(a => a.id === automation.id);
                    if (index !== -1) {
                        this.automations[index] = result.data;
                    }
                }
            } catch (error) {
                console.error('Error updating automation:', error);
                // Revert the switch on error
                automation.is_active = !automation.is_active;
            } finally {
                this.updatingId = null;
            }
        },
        async deleteAutomation(automation) {
            if (!confirm(`Are you sure you want to delete "${automation.name}"?`)) {
                return;
            }

            this.deletingId = automation.id;
            try {
                const response = await fetch(`/api/automations/${automation.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error('Failed to delete automation');
                }

                const result = await response.json();
                if (result.success) {
                    this.automations = this.automations.filter(a => a.id !== automation.id);
                }
            } catch (error) {
                console.error('Error deleting automation:', error);
            } finally {
                this.deletingId = null;
            }
        },
        formatTrigger(trigger) {
            if (typeof trigger === 'string') {
                return trigger.split('_').map(word =>
                    word.charAt(0).toUpperCase() + word.slice(1)
                ).join(' ');
            }
            return trigger;
        },
        getTriggerTone(trigger) {
            const triggerStr = typeof trigger === 'string' ? trigger : trigger.value;
            if (triggerStr === 'abandoned_checkout') return 'critical';
            if (triggerStr === 'order_created') return 'success';
            return 'subdued';
        },
    },
};
</script>
