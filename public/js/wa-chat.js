(function() {
var shopDomain;
    var isDebug = true; // window.location.search.includes('isDebug=true');
    var assetBaseUrl = 'https://halo.appsft.com';
    var apiBaseUrl = assetBaseUrl +'/external';
    var shop = null;
    var config = null;

    function init() {
        if (window.WaChatShopify) {
            logError('Whatsapp Chat script already loaded');
            return;
        }
        window.WaChatShopify = {};
        log('Whatsapp Chat script loaded');

        // get shop domain from url of js
        shopDomain = getShopDomain();
        log('Shop domain: ', shopDomain);

        loadStoreInfo();
    }

    function log(text, data) {
        // check if url has isDebug=true
        if (isDebug) {
            console.log(text, data);
        }
    }

    function logError(text, data) {
        // check if url has isDebug=true
        if (isDebug) {
            console.error(text, data);
        }
    }

    const getScriptUrl = function() {
        for (
            var t = document.getElementsByTagName("script"), n = 0;
            n < t.length;
            n++
        ) {
            var e = t[n].src;
            if (e.indexOf("/wa-chat.js") > -1) return e;
        }
    }

    const getShopDomain = function() {
        if (!!shopDomain) {
            return shopDomain;
        }
        let scriptUrl = getScriptUrl();
        log('Script url: ', scriptUrl);
        return shopUrl = Utilities.parseQueryString(scriptUrl).shop;
    }

    const Utilities = {
        guid: function() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        },
        request: function(config) {
            config.accept = config.accept || 'application/json';
            config.contentType = config.contentType || 'application/json'
            config.method = config.method || 'POST';
            return new Promise(function (success, fail) {
                var request = new XMLHttpRequest();
                request.open(config.method, config.url, true),
                    request.setRequestHeader("Content-Type", config.contentType),
                    request.setRequestHeader("Accept", config.accept),
                    request.send(JSON.stringify(config.data));
                    (request.onload = function () {
                        var response = config.accept.match(/json/)
                            ? JSON.parse(request.responseText)
                            : request.responseText;
                        request.status >= 200 && request.status < 400
                            ? success(response, request.status)
                            : fail(response, request.status);
                    });
            });
        },
        parseQueryString: function(url) {
            let params = new URL(url).search.substring(1);
            for (var n = params.split("&"), e = {}, i = 0; i < n.length; i++) {
                var o = n[i].split("=");
                if (void 0 === e[o[0]]) e[o[0]] = decodeURIComponent(o[1]);
                else if ("string" == typeof e[o[0]]) {
                    var s = [e[o[0]], decodeURIComponent(o[1])];
                    e[o[0]] = s;
                } else e[o[0]].push(decodeURIComponent(o[1]));
            }
            return e;
        },
        replaceBulk( str, obj){
            // function that replaces all the keys in the object with the values in the object
            for (const key in obj) {
                str = str.replace( key, obj[key]);
            }
            return str;
        }
    };

    function loadStoreInfo() {
        Utilities.request({url: apiBaseUrl + '/store?shop=' + shopDomain, method: 'GET'}).then(function(response) {
            shop = response.data;
            config = shop.whatsapp_config;
            onShopLoaded();
        }).catch(function(error) {
            logError('Error loading store info: ', error);
        });
    }

    function onShopLoaded() {
        initWhatsappButton();
    }

    function initWhatsappButton() {
        log('Initializing Whatsapp Button');
        if (!shop.whatsapp_config || !shop.whatsapp_config.isEnabled || !shop.whatsapp_config.phoneNumber) {
            logError('Whatsapp config not found or missing phone number');
            return;
        }

        var config = shop.whatsapp_config;

        if (!config.isEnabledOnDesktop && !isMobile()) {
            log('Whatsapp button not enabled on desktop');
            return;
        }

        if (!config.isEnabledOnMobile && isMobile()) {
            log('Whatsapp button not enabled on mobile');
            return;
        }

        log('Whatsapp config: ', shop.whatsapp_config);
        // create whatsapp button
        createWhatsappButton();
    }

    function createWhatsappButton() {
        log('Creating Whatsapp Button');
        const config = shop.whatsapp_config;
        var html = '<div id="was-widget-container">';
        html += '<style>#was-widget-container{z-index:10000;}.was-button-container{background:green;cursor:pointer;width:64px;height:64px;margin:20px;position:fixed;bottom:0;border-radius:32px;display:inline-block!important}.was-icon-button{width:auto!important;height:auto!important;line-height:auto!important;font-size:16px;padding:10px;color:#fff}#was-icon{height:42px;width:42px;margin:11px}#was-icon-button-icon{height:14px;padding-right:4px}#was-agents-widget-container{height:500px;width:300px;background:#fff;border-radius:20px;box-shadow:0 0 10px 0 rgba(0,0,0,.1);position:fixed;bottom:0;right:0;margin-bottom:90px;margin-right:20px;z-index:1000}.was-agents-header{padding:14px;border-radius:20px 20px 0 0}.was-agents-header-title{font-size:18px;font-weight:600}.was-agents-header-description{font-size:14px}.was-agent-item-avatar{width:50px;height:50px;border-radius:50%;overflow:hidden;margin-right:10px}.was-agent-item-avatar img{width:100%;height:100%;object-fit:cover}.was-agent-item{display:flex;cursor:pointer;align-items:center;padding:10px;border-bottom:1px solid #e0e0e0;font-size:16px}.was-agent-item-info{display:flex;flex-direction:column;justify-content:center}.was-agent-item-name{font-size:18px}.was-agent-item-role{color:#666;font-size:14px}#was-agents-widget-container{display:none}.was-icon-flag{border-radius:0;background-size:cover!important}</style>';
        if (config.designType != 'icon-with-text') {
        html += '\n<div class="was-button-container '+ (config.designType === 'icon-flag' ? 'was-icon-flag' : '') +'" style="background: '+getButtonBackgroundColor()+'; '
                        + ((config.position === 'bottom-left') ? 'left: 0' : 'right: 0') + ';'
                        + 'margin: ' + getButtonMargin(config) + 'px;'
                        + '">'
                        + (config.designType !== 'icon-flag' ? '<div id="was-icon" style="background: buttonIconColor; -webkit-mask-image: url(https://cdn.shopify.com/s/files/1/0460/1839/6328/files/waiconmask.svg?v=1623288530); -webkit-mask-size: cover;"></div>':'')
                    + '</div>' 
            + '</div>';
        } else {
            html += '<div class="was-button-container was-icon-button" style="background: buttonBackgroundColor; '
                        + 'color: buttonTextColor; '
                        + ((config.position === 'bottom-left') ? 'left: 0' : 'right: 0') + ';'
                        + 'margin: ' + getButtonMargin(config) + 'px;'
                        + '">'
                        + '<img id="was-icon-button-icon" src="https://cdn.shopify.com/s/files/1/0460/1839/6328/files/waiconmask.svg?v=1623288530" alt="Whatsapp Icon" />'
                        + config.buttonTextContent + '</div>';
        }

        html += `<div id="was-agents-widget-container">
            <div class="was-agents-header" style="background: linear-gradient(to bottom right, widgetHeaderBackgroundColor, widgetHeaderSecondaryColor); color: widgetHeaderTextColor">
                <div class="was-agents-header-title">widgetHeaderTitle</div>
                <div class="was-agents-header-description">widgetHeaderDescription</div>
            </div>
            <div class="was-agents-list">` +
                getAgentsListHtml() + 
            `</div>
        </div>`;
        html = Utilities.replaceBulk(html, config);
        
        // Insert HTML directly into body using insertAdjacentHTML
        document.body.insertAdjacentHTML('beforeend', html);
        document.getElementsByClassName('was-button-container')[0].addEventListener('click', function() {
            log('Whatsapp button clicked');
            // strip + and 00 from phone number beginning
            if (!config.isWidgetEnabled || config.widgetAgents.length === 0) {
                const whatsappUrl = getWhatsappDeeplink(config.phoneNumber);
                window.open(whatsappUrl, '_blank');
            } else {
                // show widget
                showOrHideChatAgentsWidget();
            }
        });
        const agentsList = document.getElementsByClassName('was-agent-item');
        for (const agent of agentsList) {
            agent.addEventListener('click', function() {
                const phoneNumber = agent.getAttribute('data-phone-number');
                const whatsappUrl = getWhatsappDeeplink(phoneNumber);
                window.open(whatsappUrl, '_blank');
            });
        }
    }

    init();

    function getWhatsappDeeplink(phoneNumber) {
        const config = shop.whatsapp_config;
        const phone = phoneNumber.replace(/^00/, '').replace(/^\+/, '');
        let whatsappUrl = 'https://wa.me/' + phone;
        if (config.isDefaultMessageEnabled) {
            const defaultMessageText = config.defaultMessageText.replace('{pageUrl}', window.location.href);
            whatsappUrl += '?text=' + encodeURIComponent(defaultMessageText);
        }
        return whatsappUrl;
    }

    function getAgentsListHtml() {
        const config = shop.whatsapp_config;
        let html = '';
        for (const agent of config.widgetAgents) {
            html += '<div class="was-agent-item" data-phone-number="' + agent.phoneNumber + '">';
            html += '<div class="was-agent-item-avatar">';
            html += '<img src="'+assetBaseUrl+'/images/' + agent.gender + '-avatar.png" alt="Avatar">';
            html += '</div>';
            html += '<div class="was-agent-item-info">';
            html += '<div class="was-agent-item-name">' + agent.name + '</div>';
            html += '<div class="was-agent-item-role">' + agent.role + '</div>';
            html += '</div>';
            html += '</div>';
        }
        return html;
    }

    function getButtonMargin(config) {
        if (isMobile()) {
            return config.buttonMarginMobile;
        }
        return config.buttonMarginDesktop;
    }

    function isMobile() {
        return window.innerWidth < 768;
    }

    function showOrHideChatAgentsWidget() {
        const wasAgentsWidgetContainer = document.getElementById('was-agents-widget-container');
        if (wasAgentsWidgetContainer.style.display === 'block') {
            log('Hiding Chat Agents Widget');
            wasAgentsWidgetContainer.style.display = 'none';
        } else {
            log('Showing Chat Agents Widget');
            wasAgentsWidgetContainer.style.display = 'block';
        }
        
    }

    function getSupportAgentHtml() {
        const config = shop.whatsapp_config;
        let html = '';
        for (const agent of config.widgetAgents) {
            html += '<div class="was-agent-container">';
            html += '<div class="was-agent-name">' + agent.name + '</div>';
            html += '<div class="was-agent-role">' + agent.role + '</div>';
            html += '</div>';
        }
        return html;
    }

    function getButtonBackgroundColor() {
        if (config.designType === 'icon-gradient') {
            return 'linear-gradient(to bottom right, ' + config.buttonBackgroundColor + ', ' + config.iconGradientSecondColor + ')';
        }
        if (config.designType === 'icon-flag') {
            return 'url(' + assetBaseUrl + '/images/flags/' + config.buttonIconFlag + '.png)';
        }
        return config.buttonBackgroundColor;
    }
})();