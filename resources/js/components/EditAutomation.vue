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
            <s-text-field :value="automation.name" @input="automation.name = $event.target.value"
                label="Automation Name" placeholder="Enter automation name" />
            <s-select :value="automation.trigger" @input="automation.trigger = $event.target.value" label="Trigger">
                <s-option value="abandoned_checkout">Abandoned Checkout</s-option>
                <s-option value="order_created">Order Created</s-option>
                <s-option value="order_fulfilled">Order Fulfilled</s-option>
            </s-select>
            <s-switch :checked="automation.is_active" @input="automation.is_active = $event.target.value"
                label="Enable Automation" />
        </s-section>

        <div class="steps-container">
            <div class="step-trigger">
                <s-heading>Trigger
                </s-heading>
                <s-text>{{ automation.trigger }}</s-text>
            </div>
            <template v-for="step in automation.steps">
                <div class="step-divider"></div>
                <div class="step-item" :class="step.type === 'send_sms' ? 'step-send_sms' : 'step-send_whatsapp'">
                    <div class="step-item-actions" gap="small">
                        <s-button icon="edit" commandFor="sms-step-modal" command="--show"
                            @click="editingStep = step"></s-button>
                        <s-button icon="delete" @click="removeStep(step)"></s-button>
                    </div>
                    <div class="step-wait_time">Wait {{ getWaitTime(step) }}</div>
                    <div class="step-item-content">
                        <div>
                            <s-heading size="small">{{ step.type ===
                                'send_sms' ? 'Send SMS' : 'Send WhatsApp message' }}</s-heading>
                            <span class="step-item-content-description">
                                {{ step.config.message }}
                            </span>
                        </div>
                    </div>
                </div>
            </template>
            <!-- add a button to add a new step -->
            <div class="step-divider"></div>
            <s-button commandFor="step-type-modal">Add Step</s-button>
        </div>

        <s-modal id="step-type-modal" heading="Choose Step Type">
            <s-paragraph>Select the type of step you want to add.</s-paragraph>
            <s-select :value="newStepType" @input="newStepType = $event.target.value">
                <s-option value="send_sms">Send SMS</s-option>
                <s-option value="send_whatsapp">Send WhatsApp</s-option>
            </s-select>
            <s-button slot="primary-action" variant="primary" commandFor="step-type-modal" command="--hide"
                @click="addStep()">
                Add Step
            </s-button>
        </s-modal>

        <s-modal id="sms-step-modal" heading="Send SMS">
            <div v-if="editingStep">
                <s-paragraph>Enter the message you want to send.</s-paragraph>
                <div v-if="editingStep">
                    <s-select label="Wait for" :value="editingStep.wait_time"
                        @input="editingStep.wait_time = $event.target.value">
                        <s-option value="0">0 minutes</s-option>
                        <s-option value="15">15 minutes</s-option>
                        <s-option value="30">30 minutes</s-option>
                        <s-option value="60">60 minutes</s-option>
                        <s-option value="120">2 hours</s-option>
                        <s-option value="180">3 hours</s-option>
                        <s-option value="360">6 hours</s-option>
                        <s-option value="720">12 hours</s-option>
                        <s-option value="1400">1 day</s-option>
                        <s-option value="4200">3 days</s-option>
                        <s-option value="8400">7 days</s-option>
                    </s-select>
                </div>

                <div style="margin-top: 10px">
                    <s-text-area :value="editingStep.config.message"
                        @input="editingStep.config.message = $event.target.value" label="Message to send"
                        placeholder="Enter message" rows="3" />
                </div>
            </div>


            <s-button slot="primary-action" variant="primary" commandFor="sms-step-modal" command="--hide">
                Save Step
            </s-button>
        </s-modal>
    </s-page>
</template>
<style scoped>
.steps-container {
    /* container that centers div with items in column and adds space between them */
    display: flex;
    flex-direction: column;
    align-items: center;
}

.step-trigger {
    display: inline-block;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    background-color: #c2c4ff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
}

.step-item {
    display: inline-block;
    width: 300px;
    background-color: #c2c4ff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    position: relative;
}

.step-divider {
    height: 20px;
    width: 1px;
    background-color: blue;
}

.step-send_sms {
    background-color: #f4e543;
}

.step-send_whatsapp {
    background-color: greenyellow
}

.step-wait_time {
    padding: 10px;
    border-bottom: 1px solid #817b7b;
}

.step-item-actions {
    position: absolute;
    top: -10px;
    right: -10px;
    z-index: 1000;
}

.step-item-content {
    padding: 10px;
}
</style>
<script>
export default {
    name: 'EditAutomation',
    data() {
        return {
            automation: {},
            newStepType: 'send_sms',
            loading: false,
            saving: false,
            isEditMode: false,
            editingStep: null,
        };
    },
    mounted() {
        const automationId = this.$route.params.id;
        this.isEditMode = !!automationId;

        if (this.isEditMode) {
            this.loadAutomation(automationId);
        } else {
            this.automation = {
                name: 'New Automation',
                trigger: 'abandoned_checkout',
                is_active: true,
                steps: []
            };
        }
    },
    methods: {
        async loadAutomation(id) {
            this.loading = true;
            try {
                const response = await fetch(`/api/automations/${id}`, {
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
                    this.automation = result.data;
                } else {
                    throw new Error("Failed to load automation");
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
        addStep() {
            if (!this.automation.steps) {
                this.automation.steps = [];
            }
            this.automation.steps.push({
                type: this.newStepType,
                step_order: this.automation.steps.length + 1,
                wait_time: "15", // default wait time is 15 minutes
                config: {
                    message: 'Hi {customer_name}! We noticed you left something in your cart.'
                        + ' Here\'s a link to complete the purchase {checkout_url}'
                },
            });
        },
        removeStep(step) {
            this.automation.steps = this.automation.steps.filter(s => s !== step);
        },
        getWaitTime(step) {
            const waitTime = step.wait_time;
            switch (parseInt(waitTime)) {
                case 0:
                    return '0 minutes';
                case 15:
                    return '15 minutes';
                case 30:
                    return '30 minutes';
                case 60:
                    return '1 hour';
                case 120:
                    return '2 hours';
                case 180:
                    return '3 hours';
                case 360:
                    return '6 hours';
                case 720:
                    return '12 hours';
                case 1400:
                    return '1 day';
                case 4200:
                    return '3 days';
                case 8400:
                    return '7 days';
                default:
                    return 'Unknown';
            }
        }
    },
};
</script>
