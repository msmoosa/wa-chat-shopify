<template>
    <s-page>
        <s-section v-if="loading" heading="Loading">
            <s-text><s-spinner accessibilityLabel="Loading" size="large-100"></s-spinner></s-text>
        </s-section>

        <template v-else>
            <s-section heading="Whatsapp Button">
                <s-text-field :value="config.phoneNumber" @input="config.phoneNumber = $event.target.value"
                    label="Whatsapp Phone Number" name="phone" placeholder="Enter phone number" />
                <s-text tone="critical" v-if="isInvalidWhatsappNumber(config.phoneNumber)">
                    Enter a valid whatsapp number with country code</s-text>
                <s-switch :disabled="isInvalidWhatsappNumber(config.phoneNumber)" :checked="config.isEnabled"
                    @input="config.isEnabled = $event.target.checked" label="Enable Whatsapp Button" :details="!isInvalidWhatsappNumber(config.phoneNumber)
                        ? 'Show the whatsapp button on your store' : 'Please enter a valid whatsapp number'" />
            </s-section>
            <s-section heading="Appearance">
                <s-color-field label="Button Background Color" placeholder="Select a color"
                    :value="config.buttonBackgroundColor"
                    @input="config.buttonBackgroundColor = $event.target.value"></s-color-field>

                <s-select label="Position" :value="config.buttonPosition"
                    @input="config.buttonPosition = $event.target.value">
                    <s-option value="bottom-left">Bottom Left</s-option>
                    <s-option value="bottom-right">Bottom Right</s-option>
                </s-select>

                <s-select label="Design" name="designType" :value="config.designType"
                    @input="config.designType = $event.target.value">
                    <s-option value="icon">Icon</s-option>
                    <s-option value="icon-gradient">Icon Gradient</s-option>
                    <s-option value="icon-flag">Icon Flag</s-option>
                    <s-option value="icon-with-text">Icon with Text</s-option>
                </s-select>

                <template v-if="config.designType === 'icon-with-text'">
                    <s-text-field label="Store name" :value="config.buttonTextContent"
                        @input="config.buttonTextContent = $event.target.value" />

                    <s-color-field label="Button Text Color" placeholder="Select a color"
                        :value="config.buttonTextColor"
                        @input="config.buttonTextColor = $event.target.value"></s-color-field>

                </template>

                <template v-if="config.designType === 'icon-gradient'">
                    <s-color-field label="Icon Gradient Second Color" placeholder="Select a color"
                        :value="config.iconGradientSecondColor"
                        @input="config.iconGradientSecondColor = $event.target.value"></s-color-field>
                </template>

                <template v-if="config.designType === 'icon-flag'">
                    <s-select label="Flag" :value="config.buttonIconFlag"
                        @input="config.buttonIconFlag = $event.target.value">
                        <s-option value="ind">India</s-option>
                        <s-option value="usa">United States</s-option>
                        <s-option value="uae">United Arab Emirates</s-option>
                    </s-select>

                </template>


            </s-section>
            <s-section heading="Advanced">
                <s-switch label="Enable in Desktop" :checked="config.isEnabledOnDesktop"
                    @input="config.isEnabledOnDesktop = $event.target.checked" />

                <s-text-field label="Button Margin Desktop" :value="config.buttonMarginDesktop"
                    @input="config.buttonMarginDesktop = $event.target.value" />

                <s-switch label="Enable in Mobile" :checked="config.isEnabledOnMobile"
                    @input="config.isEnabledOnMobile = $event.target.checked" />
                <s-text-field label="Button Margin Mobile" :value="config.buttonMarginMobile"
                    @input="config.buttonMarginMobile = $event.target.value" />
            </s-section>

            <s-section heading="Default Message">
                <s-switch label="Enable Default Message" :checked="config.isDefaultMessageEnabled"
                    @input="config.isDefaultMessageEnabled = $event.target.checked" />
                <s-text-field label="Default Message" :value="config.defaultMessageText"
                    @input="config.defaultMessageText = $event.target.value" />
            </s-section>

            <s-section heading="Chat Agents Widget">
                <s-paragraph>Support multiple chat agents by adding their phone numbers below.
                </s-paragraph>
                <s-switch label="Enable Chat Agents Widget" :checked="config.isWidgetEnabled"
                    @input="config.isWidgetEnabled = $event.target.checked" />

                <s-table>
                    <s-table-header-row>
                        <s-table-header>Name</s-table-header>
                        <s-table-header>Role</s-table-header>
                        <s-table-header>Phone Number</s-table-header>
                        <s-table-header>Gender</s-table-header>
                        <s-table-header></s-table-header>
                    </s-table-header-row>
                    <s-table-body>
                        <s-table-row v-for="agent in config.widgetAgents" :key="$index">
                            <s-table-cell><s-text-field :value="agent.name"
                                    @input="agent.name = $event.target.value" /></s-table-cell>
                            <s-table-cell><s-text-field :value="agent.role"
                                    @input="agent.role = $event.target.value" /></s-table-cell>
                            <s-table-cell><s-text-field :value="agent.phoneNumber"
                                    @input="agent.phoneNumber = $event.target.value" />
                                <s-text tone="critical" v-if="isInvalidWhatsappNumber(agent.phoneNumber)">
                                    Enter Valid number</s-text>
                            </s-table-cell>
                            <s-table-cell><s-select :value="agent.gender" @input="agent.gender = $event.target.value">
                                    <s-option value="male">Male</s-option>
                                    <s-option value="female">Female</s-option>
                                </s-select></s-table-cell>
                            <s-table-cell><s-button
                                    @click="removeAgent(agent.phoneNumber)">Remove</s-button></s-table-cell>
                        </s-table-row>
                        <s-table-row>
                            <s-table-cell><s-button @click="addAgent">Add Agent</s-button></s-table-cell>

                        </s-table-row>
                    </s-table-body>
                </s-table>

                <div style="display: flex; flex-direction: row; gap: 20px;margin-top: 20px">
                    <div style="flex: 1;">
                        <s-text-field label="Header Title" :value="config.widgetHeaderTitle"
                            @input="config.widgetHeaderTitle = $event.target.value" />
                        <s-text-field label="Header Description" :value="config.widgetHeaderDescription"
                            @input="config.widgetHeaderDescription = $event.target.value" />
                        <s-color-field label="Header Background Color" :value="config.widgetHeaderBackgroundColor"
                            @input="config.widgetHeaderBackgroundColor = $event.target.value" />
                        <s-color-field label="Header Secondary Color" :value="config.widgetHeaderSecondaryColor"
                            @input="config.widgetHeaderSecondaryColor = $event.target.value" />
                        <s-color-field label="Header Text Color" :value="config.widgetHeaderTextColor"
                            @input="config.widgetHeaderTextColor = $event.target.value" />
                    </div>
                    <div style="flex: 1;">
                        <div style="border: 1px solid #CCC; border-radius: 20px;">
                            <div class="was-agents-header"
                                :style="'background: linear-gradient(to bottom right, ' + config.widgetHeaderBackgroundColor + ', ' + config.widgetHeaderSecondaryColor + ');color: ' + config.widgetHeaderTextColor + ';'">
                                <div class="was-agents-header-title">
                                    {{ config.widgetHeaderTitle }}
                                </div>
                                <div class="was-agents-header-description">
                                    {{ config.widgetHeaderDescription }}
                                </div>
                            </div>
                            <div class="was-agents-widget-container">
                                <div class="was-agent-item" v-for="agent in config.widgetAgents"
                                    :key="agent.phoneNumber">
                                    <div class="was-agent-item-avatar">
                                        <img :src="'/images/' + agent.gender + '-avatar.png'" alt="Agent Icon" />
                                    </div>
                                    <div class="was-agent-item-info">
                                        <div class="was-agent-item-name">{{ agent.name }}</div>
                                        <div class="was-agent-item-role">{{ agent.role }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div style="margin-top:10px">
                    <s-button @click="saveConfig" variant="primary" :loading="saving">Save</s-button>
                </div>




            </s-section>

        </template>

        <s-box slot="aside" v-if="!loading">
            <s-section heading="Preview">
                <!-- {{ config }}
                <br /> -->
                <div id="was-preview-background">
                    <template v-if="config.designType !== 'icon-with-text'">
                        <div :class="config.designType === 'icon-flag' ? 'was-button-container was-icon-flag' : 'was-button-container'"
                            :style="'background: ' + getButtonBackgroundColor() + '; '
                                + (isLeftPosition() ? 'left: 0' : 'right:0') + ';'
                                + 'margin: ' + config.buttonMarginDesktop + 'px;'
                                + 'width: ' + config.buttonIconSize + 'px; height: ' + config.buttonIconSize + 'px;'">
                            <img v-if="config.designType !== 'icon-flag'" id="was-icon"
                                src="https://cdn.shopify.com/s/files/1/0460/1839/6328/files/waiconmask.svg?v=1623288530"
                                alt="Whatsapp Icon" />
                        </div>
                    </template>
                    <template v-if="config.designType === 'icon-with-text'">
                        <div class="was-button-container was-icon-button" :style="'background: ' + config.buttonBackgroundColor + '; color: ' + config.buttonTextColor + ';'
                            + 'color: ' + config.buttonTextColor + ';'
                            + (isLeftPosition() ? 'left: 0' : 'right:0')
                            + 'margin: ' + config.buttonMarginDesktop + 'px;'">
                            <img id="was-icon-button-icon"
                                src="https://cdn.shopify.com/s/files/1/0460/1839/6328/files/waiconmask.svg?v=1623288530"
                                alt="Whatsapp Icon" />
                            {{ config.buttonTextContent }}
                        </div>
                    </template>
                </div>
                <s-button @click="saveConfig" variant="primary" :loading="saving">Save</s-button>
            </s-section>
        </s-box>
    </s-page>

</template>

<script>
export default {
    name: 'Home',
    props: {
        shopDomain: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            config: null,
            loading: true,
            saving: false
        }
    },
    mounted() {
        this.loadShopConfig();
    },
    methods: {
        async saveConfig() {
            try {
                this.saving = true;
                const response = await fetch('/api/shop-config', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(this.config),
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error('Failed to save shop config');
                }

                const result = await response.json();

                if (result.success) {
                    this.loadShopConfig();
                }
            } catch (error) {
                console.error('Error saving shop config:', error);
            } finally {
                this.saving = false;
            }
        },
        async loadShopConfig() {
            try {
                const response = await fetch('/api/shop-config', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error('Failed to load shop config');
                }

                const result = await response.json();

                if (result.success && result.data.whatsapp_config) {
                    // Merge the loaded config with defaults
                    this.config = result.data.whatsapp_config;
                } else {
                    throw new Error('Failed to load shop config');
                }
            } catch (error) {
                console.error('Error loading shop config:', error);
            } finally {
                this.loading = false;
            }
        },
        isValidWhatsappNumber(number) {
            const phone = number || '';
            return phone.length >= 10 && (phone.startsWith('+') || phone.startsWith('00'));
        },
        isInvalidWhatsappNumber(number) {
            return !this.isValidWhatsappNumber(number);
        },
        isLeftPosition() {
            return this.config.buttonPosition == 'bottom-left';
        },
        addAgent() {
            this.config.widgetAgents.push({
                phoneNumber: '',
                name: '',
                role: '',
                gender: 'male'
            });
        },
        removeAgent(phoneNumber) {
            this.config.widgetAgents = this.config.widgetAgents.filter(agent => agent.phoneNumber !== phoneNumber);
        },
        getButtonBackgroundColor() {
            if (this.config.designType === 'icon-gradient') {
                return 'linear-gradient(to bottom right, ' + this.config.buttonBackgroundColor + ', ' + this.config.iconGradientSecondColor + ')';
            }
            if (this.config.designType === 'icon-flag') {
                return 'url(/images/flags/' + this.config.buttonIconFlag + '.png)';
            }
            return this.config.buttonBackgroundColor;
        },
        navigateToAbandonedCarts() {
            this.$router.push('/abandoned-carts');
        }
    }
}
</script>
<style>
.was-button-container {
    background: green;
    width: 64px;
    height: 64px;
    margin: 20px;
    position: absolute;
    bottom: 0;
    border-radius: 32px;
    display: inline-block !important;
    cursor: pointer;
}

.was-icon-button {
    width: auto !important;
    height: auto !important;
    line-height: auto !important;
    font-size: 16px;
    padding: 10px;
    color: white;
}

#was-icon {
    height: 42px;
    width: 42px;
    margin: 11px;
}

#was-icon-button-icon {
    height: 14px;
    padding-right: 4px;
}

#was-preview-background {
    background: #f0f0f0;
    padding: 40px 20px;
    margin-bottom: 10px;
    border-radius: 10px;
    position: relative;
    display: block;
    height: 80px;
}

.was-icon-flag {
    border-radius: 0;
    background-size: cover !important;
}

.was-agents-header {
    padding: 14px;
    border-radius: 20px 20px 0 0
}

.was-agents-header-title {
    font-size: 18px;
    font-weight: 600
}

.was-agents-header-description {
    font-size: 14px
}

.was-agent-item-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    margin-right: 10px
}

.was-agent-item-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover
}

.was-agent-item {
    display: flex;
    cursor: pointer;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #e0e0e0;
    font-size: 16px
}

.was-agent-item-info {
    display: flex;
    flex-direction: column;
    justify-content: center
}
</style>