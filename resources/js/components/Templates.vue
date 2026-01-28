<template>
    <s-page title="Templates">
        <s-section>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <s-heading>Templates</s-heading>
                <s-button variant="primary" @click="newTemplate">Add template</s-button>
            </div>

            <s-text tone="subdued">
                Create reusable WhatsApp message templates you can use when contacting customers.<br />
                You can use the following variables in your message:
                {customer_name}, {checkout_url}, {total_price}
            </s-text>
        </s-section>

        <s-section heading="Templates">
            <s-table v-if="templates.length">
                <s-table-header-row>
                    <s-table-header>Title</s-table-header>
                    <s-table-header>Message</s-table-header>
                    <s-table-header></s-table-header>
                </s-table-header-row>
                <s-table-body>
                    <s-table-row v-for="template in templates" :key="template.id">
                        <s-table-cell>
                            <s-text-field :value="template.title"
                                @input="updateLocal(template, 'title', $event.target.value)" />
                        </s-table-cell>
                        <s-table-cell>
                            <s-text-area :value="template.message"
                                @input="updateLocal(template, 'message', $event.target.value)" />
                        </s-table-cell>
                        <s-table-cell>
                            <s-stack alignment="trailing" spacing="tight">
                                <s-button size="micro" @click="save(template)" :loading="savingId === template.id">
                                    Save
                                </s-button>
                                <s-button size="micro" tone="critical" variant="plain" @click="remove(template)"
                                    :loading="deletingId === template.id">
                                    Delete
                                </s-button>
                            </s-stack>
                        </s-table-cell>
                    </s-table-row>
                </s-table-body>
            </s-table>

            <s-text v-else tone="subdued">
                No templates yet. Click "Add template" to create your first one.
            </s-text>
        </s-section>
    </s-page>
</template>

<script>
export default {
    name: 'Templates',
    data() {
        return {
            templates: [],
            savingId: 0,
            deletingId: 0,
        };
    },
    mounted() {
        this.loadTemplates();
    },
    methods: {
        async loadTemplates() {
            try {
                const response = await fetch('/manualtemplates', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error('Failed to load templates');
                }

                const result = await response.json();
                if (result.success && result.data) {
                    this.templates = result.data;
                }
            } catch (error) {
                console.error('Error loading templates:', error);
            }
        },
        newTemplate() {
            this.templates.unshift({
                id: -1,
                title: '',
                message: '',
            });
        },
        updateLocal(template, field, value) {
            template[field] = value;
        },
        async save(template) {
            if (!template.title || !template.message) {
                return;
            }

            this.savingId = template.id;
            try {
                const response = await fetch('/manualtemplates/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        id: template.id,
                        title: template.title,
                        message: template.message,
                    }),
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error('Failed to save template');
                }

                const result = await response.json();
                if (result.success && result.data) {
                    // Replace local template with server version (has id, timestamps, etc.)
                    const index = this.templates.indexOf(template);
                    if (index !== -1) {
                        this.templates.splice(index, 1, result.data);
                    } else {
                        this.loadTemplates();
                    }
                }
            } catch (error) {
                console.error('Error saving template:', error);
            } finally {
                this.savingId = null;
            }
        },
        async remove(template) {
            if (!template.id) {
                // Unsaved template: just remove from list
                this.templates = this.templates.filter(t => t !== template);
                return;
            }

            this.deletingId = template.id;
            try {
                const response = await fetch('/manualtemplates/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ id: template.id }),
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error('Failed to delete template');
                }

                const result = await response.json();
                if (result.success) {
                    this.templates = this.templates.filter(t => t.id !== template.id);
                }
            } catch (error) {
                console.error('Error deleting template:', error);
            } finally {
                this.deletingId = null;
            }
        },
    },
};
</script>
