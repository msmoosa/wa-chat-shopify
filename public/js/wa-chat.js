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
    }

    init();
})();