(function() {
var shopDomain;
    var isDebug = true; // window.location.search.includes('isDebug=true');
    var apiBaseUrl = 'https://wa-chat.test/external';
    var shop = null;

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
            logError('Whatsapp config not found');
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
        html += '<style>.was-button-container{background:green;cursor:pointer;width:64px;height:64px;margin:20px;position:absolute;bottom:0;border-radius:32px;display:inline-block!important}.was-icon-button{width:auto!important;height:auto!important;line-height:auto!important;font-size:16px;padding:10px;color:#fff}#was-icon{height:42px;width:42px;margin:11px}#was-icon-button-icon{height:14px;padding-right: 4px}</style>';
        if (config.design === 'icon') {
        html += '\n<div class="was-button-container" style="background: buttonBackgroundColor;; '
                        + ((config.position === 'bottom-left') ? 'left: 0' : 'right: 0') + '">'
                        + '<div id="was-icon" style="background: buttonIconColor; -webkit-mask-image: url(https://cdn.shopify.com/s/files/1/0460/1839/6328/files/waiconmask.svg?v=1623288530); -webkit-mask-size: cover;"></div>'
                    + '</div>' 
            + '</div>';
        } else {
            html += '<div class="was-button-container was-icon-button" style="background: buttonBackgroundColor; '
                        + 'color: buttonTextColor; '
                        + ((config.position === 'bottom-left') ? 'left: 0' : 'right: 0') + '">'
                        + '<img id="was-icon-button-icon" src="https://cdn.shopify.com/s/files/1/0460/1839/6328/files/waiconmask.svg?v=1623288530" alt="Whatsapp Icon" />'
                        + config.buttonTextContent + '</div>';
        }

        html = Utilities.replaceBulk(html, config);
        
        // Insert HTML directly into body using insertAdjacentHTML
        document.body.insertAdjacentHTML('beforeend', html);
        document.getElementsByClassName('was-button-container')[0].addEventListener('click', function() {
            log('Whatsapp button clicked');
            // strip + and 00 from phone number beginning
            const phone = config.phone.replace(/^00/, '').replace(/^\+/, '');
            window.open('https://wa.me/' + phone, '_blank');
        });
    }

    init();
})();