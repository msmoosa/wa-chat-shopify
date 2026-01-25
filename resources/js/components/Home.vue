<template>
    <s-page>

        <s-section heading="Whatsapp Button">
            <s-text-field :value="config.phone" @input="config.phone = $event.target.value" label="Whatsapp number"
                name="phone" placeholder="Enter phone number" />
            <s-text tone="critical" v-if="isInvalidWhatsappNumber()">
                Enter a valid whatsapp number starting with + or 00</s-text>
            <s-switch :disabled="isInvalidWhatsappNumber()" :checked="config.isEnabled"
                @input="config.isEnabled = $event.target.checked" label="Enable Whatsapp Button" :details="config.phone
                    ? 'Show the whatsapp button on your store' : 'Please enter a valid whatsapp number'" />
        </s-section>
        <s-section heading="Appearance">
            <s-color-field label="Color" placeholder="Select a color" :value="config.color"
                @input="config.color = $event.target.value"></s-color-field>

            <s-select label="Position" :value="config.position" @input="config.position = $event.target.value">
                <s-option value="bottom-left">Bottom Left</s-option>
                <s-option value="bottom-right">Bottom Right</s-option>
            </s-select>

        </s-section>

        <s-box slot="aside">
            <s-section heading="Preview">
                <div id="was-preview-background">
                    <div id="was-button-container" :style="'background: ' + config.color + '; '
                        + (isLeftPosition() ? 'left: 0' : 'right:0')">
                        <div id="was-icon"
                            style="background: white; -webkit-mask-image: url(https://cdn.shopify.com/s/files/1/0460/1839/6328/files/waiconmask.svg?v=1623288530); -webkit-mask-size: cover;">
                        </div>
                    </div>
                </div>
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
            config: {
                phone: '',
                isEnabled: false,
                position: 'bottom-right',
                color: '#42D74C',
            }
        }
    },
    methods: {
        isValidWhatsappNumber() {
            const phone = this.config.phone;
            return phone.length >= 10 && (phone.startsWith('+') || phone.startsWith('00'));
        },
        isInvalidWhatsappNumber() {
            return !this.isValidWhatsappNumber();
        },
        isLeftPosition() {
            return this.config.position == 'bottom-left';
        }
    }
}
</script>
<style>
#was-button-container {
    line-height: 0;
    background: green;
    width: 64px;
    height: 64px;
    margin: 20px;
    position: absolute;
    top: 0;
    border-radius: 32px;
    display: inline-block !important
}

#was-icon {
    height: 42px;
    width: 42px;
    margin: 11px;
}

#was-preview-background {
    background: #f0f0f0;
    padding: 20px;
    border-radius: 10px;
    position: relative;
    display: block;
    height: 80px;
}
</style>