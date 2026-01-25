<template>
    <s-page>
        <s-section v-if="loading" heading="Loading">
            <s-text><s-spinner accessibilityLabel="Loading" size="large-100"></s-spinner></s-text>
        </s-section>

        <template v-else>
            <s-section heading="Whatsapp Button">
                <s-text-field :value="config.phoneNumber" @input="config.phoneNumber = $event.target.value"
                    label="Whatsapp number" name="phone" placeholder="Enter phone number" />
                <s-text tone="critical" v-if="isInvalidWhatsappNumber()">
                    Enter a valid whatsapp number with country code</s-text>
                <s-switch :disabled="isInvalidWhatsappNumber()" :checked="config.isEnabled"
                    @input="config.isEnabled = $event.target.checked" label="Enable Whatsapp Button" :details="config.phone
                        ? 'Show the whatsapp button on your store' : 'Please enter a valid whatsapp number'" />
            </s-section>
            <s-section heading="Appearance">
                <s-color-field label="Button Background Color" placeholder="Select a color"
                    :value="config.buttonBackgroundColor"
                    @input="config.buttonBackgroundColor = $event.target.value"></s-color-field>

                <s-color-field label="Button Icon Color" placeholder="Select a color" :value="config.buttonIconColor"
                    @input="config.buttonIconColor = $event.target.value"></s-color-field>

                <s-select label="Position" :value="config.buttonPosition"
                    @input="config.buttonPosition = $event.target.value">
                    <s-option value="bottom-left">Bottom Left</s-option>
                    <s-option value="bottom-right">Bottom Right</s-option>
                </s-select>

                <s-select label="Design" name="designType" :value="config.designType"
                    @input="config.designType = $event.target.value">
                    <s-option value="icon">Icon only</s-option>
                    <s-option value="icon-with-text">Icon with Text</s-option>
                </s-select>

                <template v-if="config.designType === 'icon-with-text'">
                    <s-text-field label="Store name" :value="config.buttonTextContent"
                        @input="config.buttonTextContent = $event.target.value" />

                    <s-color-field label="Button Text Color" placeholder="Select a color"
                        :value="config.buttonTextColor"
                        @input="config.buttonTextColor = $event.target.value"></s-color-field>

                </template>


            </s-section>
        </template>

        <s-box slot="aside" v-if="!loading">
            <s-section heading="Preview">
                {{ config }}
                <br />
                <div id="was-preview-background">
                    <template v-if="config.designType === 'icon'">
                        <div class="was-button-container" :style="'background: ' + config.buttonBackgroundColor + '; '
                            + (isLeftPosition() ? 'left: 0' : 'right:0')">
                            <div id="was-icon"
                                :style="'background: ' + config.buttonIconColor + '; -webkit-mask-image: url(https://cdn.shopify.com/s/files/1/0460/1839/6328/files/waiconmask.svg?v=1623288530); -webkit-mask-size: cover;'">
                            </div>
                        </div>
                    </template>
                    <template v-if="config.designType === 'icon-with-text'">
                        <div class="was-button-container was-icon-button" :style="'background: ' + config.buttonBackgroundColor + '; color: ' + config.buttonTextColor + ';'
                            + 'color: ' + config.buttonTextColor + ';'
                            + (isLeftPosition() ? 'left: 0' : 'right:0')">
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
        isValidWhatsappNumber() {
            const phone = this.config.phoneNumber || '';
            return phone.length >= 10 && (phone.startsWith('+') || phone.startsWith('00'));
        },
        isInvalidWhatsappNumber() {
            return !this.isValidWhatsappNumber();
        },
        isLeftPosition() {
            return this.config.buttonPosition == 'bottom-left';
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
</style>