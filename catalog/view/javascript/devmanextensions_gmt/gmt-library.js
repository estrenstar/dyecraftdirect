!function (e, t) { "object" == typeof exports && "undefined" != typeof module ? module.exports = t() : "function" == typeof define && define.amd ? define(t) : (e = e || self, function () { var n = e.GMTGCCookies, o = e.GMTGCCookies = t(); o.noConflict = function () { return e.GMTGCCookies = n, o } }()) }(this, (function () { "use strict"; function e(e) { for (var t = 1; t < arguments.length; t++) { var n = arguments[t]; for (var o in n) e[o] = n[o] } return e } return function t(n, o) { function r(t, r, i) { if ("undefined" != typeof document) { "number" == typeof (i = e({}, o, i)).expires && (i.expires = new Date(Date.now() + 864e5 * i.expires)), i.expires && (i.expires = i.expires.toUTCString()), t = encodeURIComponent(t).replace(/%(2[346B]|5E|60|7C)/g, decodeURIComponent).replace(/[()]/g, escape); var c = ""; for (var u in i) i[u] && (c += "; " + u, !0 !== i[u] && (c += "=" + i[u].split(";")[0])); return document.cookie = t + "=" + n.write(r, t) + c } } return Object.create({ set: r, get: function (e) { if ("undefined" != typeof document && (!arguments.length || e)) { for (var t = document.cookie ? document.cookie.split("; ") : [], o = {}, r = 0; r < t.length; r++) { var i = t[r].split("="), c = i.slice(1).join("="); try { var u = decodeURIComponent(i[0]); if (o[u] = n.read(c, u), e === u) break } catch (e) { } } return e ? o[e] : o } }, remove: function (t, n) { r(t, "", e({}, n, { expires: -1 })) }, withAttributes: function (n) { return t(this.converter, e({}, this.attributes, n)) }, withConverter: function (n) { return t(e({}, this.converter, n), this.attributes) } }, { attributes: { value: Object.freeze(o) }, converter: { value: Object.freeze(n) } }) }({ read: function (e) { return '"' === e[0] && (e = e.slice(1, -1)), e.replace(/(%[\dA-F]{2})+/gi, decodeURIComponent) }, write: function (e) { return encodeURIComponent(e).replace(/%(2[346BF]|3[AC-F]|40|5[BDE]|60|7[BCD])/g, decodeURIComponent) } }, { path: "/" }) }));

(function (window, document) {
    var GoogleMarketingTools = function (settings) {
        this.isDebugEnabled = settings.isDebugEnabled || false;
        this.isGmtAdmin = settings.isGmtAdmin;
        this.trackApiUrl = settings.trackApiUrl;
        this.callAddonsUrl = settings.callAddonsUrl;
        this.isGoogleConsentEnabled = this.isGmtAdmin ? false : settings.isGoogleConsentEnabled;
        this.cookiebotCompatibility = settings.cookiebotCompatibility;

        this.ssTrackInfo = [];
        this.productsListed = {};
        this.promotionsListed = {};

        this.cookiesCategories = {
            necessary: ['security_storage'],
            preferences: ['functionality_storage', 'personalization_storage'],
            statistics: ['analytics_storage'],
            marketing: ['ad_storage', 'ad_user_data', 'ad_personalization']
        };

        this._eventAClicks();

        if (this.isGoogleConsentEnabled) {
            var googleConsentSettings = settings.googleConsentSettings;

            this.gmtgcAcceptanceWithPageChange = googleConsentSettings.acceptanceWithPageChange;
            this.gmtgcAcceptanceWithScroll = googleConsentSettings.acceptanceWithScroll;
            this.gmtgcButtonConfiguration = googleConsentSettings.buttonConfiguration;
            this.gmtgcConsentPopupPosition = googleConsentSettings.consentPopupPosition;
            this.gmtgcConsentLockScreen = googleConsentSettings.consentLockScreen;
            this.gmtgcPopupDelayTime = googleConsentSettings.popupDelayTime;
            this.gmtgcConsentPopupButtonConfigPosition = googleConsentSettings.consentPopupButtonConfigPosition;
            this.gmtgcMainColor = googleConsentSettings.mainColor;
            this.gmtgcConfigurationButtonSvgColor = googleConsentSettings.configurationButtonSvgColor;
            this.gmtgcConfigurationButtonColor = googleConsentSettings.configurationButtonColor;
            this.gmtgcConfigurationButtonTextColor = googleConsentSettings.configurationButtonTextColor;
            this.gmtgcRefuseButtonBackgroundColor = googleConsentSettings.refuseButtonBackgroundColor;
            this.gmtgcRefuseButtonFontColor = googleConsentSettings.refuseButtonFontColor;
            this.gmtgcPopupBackground = googleConsentSettings.popupBackground;
            this.gmtgcPopupFontColor = googleConsentSettings.popupFontColor;
            this.gmtgcButtonAcceptAllFontColor = googleConsentSettings.buttonAcceptAllFontColor;
            this.gmtgcAcceptSelectionButtonBackgroundColor = googleConsentSettings.acceptSelectionButtonBackgroundColor;
            this.gmtgcAcceptSelectionButtonFontColor = googleConsentSettings.acceptSelectionButtonFontColor;
            this.gmtgcNecessaryCheckboxColor = googleConsentSettings.necessaryCheckboxColor;
            this.gmtgcPreferencesCheckboxColor = googleConsentSettings.preferencesCheckboxColor;
            this.gmtgcStatisticsCheckboxColor = googleConsentSettings.statisticsCheckboxColor;
            this.gmtgcMarketingCheckboxColor = googleConsentSettings.marketingCheckboxColor;
            this.gmtgcLanguages = googleConsentSettings.languages;

            var language_code = typeof settings.lang !== 'undefined' ? settings.lang : '';

            if (typeof this.gmtgcLanguages[language_code] == "undefined") {
                let index = 0;
                var languagesOrdered = {};

                for (const key in this.gmtgcLanguages) {
                    if (this.gmtgcLanguages.hasOwnProperty(key)) {
                        languagesOrdered[index++] = this.gmtgcLanguages[key];
                    }
                }

                this.gmtgcLangText = languagesOrdered[0];
            } else {
                this.gmtgcLangText = this.gmtgcLanguages[language_code];
            }

            var linkPolicies = this.gmtgcLangText['link_href'];

            var instance = this;

            Object.values(instance.cookiesCategories).flat().forEach(function (cookieName) {
                if (instance.cookiesCategories['necessary'].includes(cookieName)) {
                    GMTGCCookies.set(cookieName, 'granted', { expires: 365 });
                } else if (typeof GMTGCCookies.get(cookieName) === 'undefined') {
                    GMTGCCookies.set(cookieName, 'denied', { expires: 365 });
                }
            });

            if (linkPolicies != window.location.href) {
                setTimeout(() => { this.googleConsentInit(); }, this.gmtgcPopupDelayTime);
            }
        } else {
            if (this.cookiebotCompatibility) {
                const cookiesTem = this.cookiesCategories;

                Object.values(cookiesTem).flat().forEach(function (cookieName) {
                    if (cookiesTem['necessary'].includes(cookieName)) {
                        GMTGCCookies.set(cookieName, 'granted', { expires: 365 });
                    } else if (typeof GMTGCCookies.get(cookieName) === 'undefined') {
                        GMTGCCookies.set(cookieName, 'denied', { expires: 365 });
                    }
                });
            } else if (!this.isGmtAdmin) {
                GMTGCCookies.set('acceptedGoogleConsent', 'Yes', { expires: 365 });

                Object.values(this.cookiesCategories).flat().forEach(function (cookieName) {
                    GMTGCCookies.set(cookieName, 'granted', { expires: 365 });
                });
            }

            var instance = this;
            instance.checkConsentStatus();
        }
    };

    GoogleMarketingTools.prototype.start = function () {/* constructor */ };

    GoogleMarketingTools.prototype.init = function (json) {
        this._setVideoEvent();
        dataLayer.push({ 'event': 'Set general data', 'general_data': json });
    }

    GoogleMarketingTools.prototype.setProducts = function (json) {
        this.productsListed = json;
        dataLayer.push({ 'event': 'Set products listed', 'products_listed': json });
    }

    GoogleMarketingTools.prototype.setFacebookPixelTrack = function (json) {
        dataLayer.push({ 'event': 'Set facebook pixel data', 'fb_pixel_track_info': json });
    }

    GoogleMarketingTools.prototype.setCartProducts = function (json) {
        dataLayer.push({ 'event': 'Set cart products', 'cart_products': json });
    }

    GoogleMarketingTools.prototype.checkIsNormalPage = function (json) {
        dataLayer.push({ 'event': 'Check is normal page' });
    }

    GoogleMarketingTools.prototype.trackSearchPage = function (stringSearched) {
        var productListed = this._getDataLayerVariable('products_listed');

        if (productListed) {
            this._launchEvent('Search', 'string_searched', stringSearched);
        } else {
            console.error('GoogleMarketingTools error: For launch trackSearch, before you have to call to GoogleMarketingTools.setProducts');
        }
    }

    GoogleMarketingTools.prototype.trackCategoryPage = function (currentCategories) {
        var productListed = this._getDataLayerVariable('products_listed');

        if (productListed) {
            this._launchEvent('Category', 'current_categories', currentCategories);
        } else {
            console.error('GoogleMarketingTools error: For launch trackCategory, before you have to call to GoogleMarketingTools.setProducts');
        }
    }

    GoogleMarketingTools.prototype.trackCartPage = function () {
        var cartProducts = this._getDataLayerVariable('cart_products');

        if (cartProducts) {
            this._launchEvent('Cart');
        } else {
            console.error('GoogleMarketingTools error: For launch trackCartPage, before you have to call to GoogleMarketingTools.setCartProducts');
        }
    }

    GoogleMarketingTools.prototype.trackCheckoutPage = function () {
        var cartProducts = this._getDataLayerVariable('cart_products');

        if (cartProducts) {
            this._launchEvent('Checkout start');
        } else {
            console.error('GoogleMarketingTools error: For launch trackCheckoutPage, before you have to call to GoogleMarketingTools.setCartProducts');
        }
    }

    GoogleMarketingTools.prototype.trackPurchase = function (json) {
        this._launchEvent('Purchase', 'order_data', json);

        if (!this.isGmtAdmin) {
            var requestData = {
                order_id: json.order.id ?? 0,
                action: 'sent'
            };

            const xhr = new XMLHttpRequest();

            xhr.open('POST', this.trackApiUrl, true);
            xhr.setRequestHeader('Content-Type', 'application/json');

            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);

                        if (response.error) {
                            console.error('GoogleMarketingTools Error - Error saving if the purchase was tracked:', xhr.status, xhr.statusText);
                        }
                    } else {
                        console.error('GoogleMarketingTools Error - Error saving if the purchase was tracked:', xhr.status, xhr.statusText);
                    }
                }
            };

            xhr.send(JSON.stringify(requestData));
        }
    }

    GoogleMarketingTools.prototype.trackHomePage = function () {
        this._launchEvent('Home page');
    }

    GoogleMarketingTools.prototype.trackAccountRegistered = function (json) {
        this._launchEvent('Account registered', 'account_info', json);
    }

    GoogleMarketingTools.prototype.trackPromotionsListed = function (json) {
        this.promotionsListed = json;
        this._launchEvent('Promotions', 'promotions_listed', json);
    }

    GoogleMarketingTools.prototype.trackProductPage = function (json) {
        this._launchEvent('Set Product Details', 'product_details', json, true);
        this._launchEvent('Product', 'product_details', json);
    }

    GoogleMarketingTools.prototype.trackAddToCart = function (json) {
        this._cartEvent(json, 'Add to cart', 'addToCart');
    }

    GoogleMarketingTools.prototype.trackAddToWishlist = function (json) {
        this._cartEvent(json, 'Add to wish list', 'addToWishlist');
    }

    GoogleMarketingTools.prototype.trackRemoveFromCart = function (json) {
        this._cartEvent(json, 'Remove from cart', 'removeFromCart');
    }

    GoogleMarketingTools.prototype.trackPromotionClick = function (promotionId) {
        this._launchEvent('Promotion click', 'promotionClicked', { 'promotion_id': promotionId });
    }

    GoogleMarketingTools.prototype.trackProductClick = function (productId) {
        this._launchEvent('Product click', 'productClicked', { 'product_id': productId });
    }

    GoogleMarketingTools.prototype.trackPaymentInformation = function (json) {
        this._launchEvent('Set payment', 'payment_info', json);
    }

    GoogleMarketingTools.prototype.trackShippingInfo = function (json) {
        this._launchEvent('Set shipping', 'shipping_info', json);
    }

    GoogleMarketingTools.prototype.trackContactFormSubmit = function (json) {
        this._launchEvent('Contact form', 'account_info', json);
    }

    GoogleMarketingTools.prototype.trackLogin = function (json) {
        this._launchEvent('Account logged', 'account_info', json);
    }

    GoogleMarketingTools.prototype._launchEvent = function (eventName, variableName, json, skipAllTagsFired = false) {
        var instance = this;
        var varName = 'all_tags_fired';
        var allowTrack = false;

        if (typeof dataLayer !== 'undefined') {
            if (!skipAllTagsFired) {
                Object.keys(dataLayer).map(function (objectKey, index) {
                    var value = dataLayer[objectKey];

                    if (typeof value[varName] !== 'undefined') {
                        allowTrack = true;
                    }
                });
            } else {
                allowTrack = true;
            }

            if (allowTrack) {
                object_track = {};
                object_track[variableName] = json;
                object_track["event"] = eventName;

                dataLayer.push(object_track);
            } else {
                setTimeout(function () {
                    instance._launchEvent(eventName, variableName, json);
                }, 500);
            }
        } else {
            setTimeout(function () {
                instance._launchEvent(eventName, variableName, json);
            }, 500)
        }
    }

    GoogleMarketingTools.prototype._cartEvent = function (json, eventName, variableName) {
        var object = {};

        object.id = typeof json['id'] != 'undefined' ? json['id'] : '';
        object.id_ee = typeof json['id_ee'] != 'undefined' ? json['id_ee'] : '';
        object.id_gdr_1 = typeof json['id_gdr_1'] != 'undefined' ? json['id_gdr_1'] : '';
        object.id_gdr_2 = typeof json['id_gdr_2'] != 'undefined' ? json['id_gdr_2'] : '';
        object.id_fb = typeof json['id_fb'] != 'undefined' ? json['id_fb'] : '';
        object.price = typeof json['price'] != 'undefined' ? json['price'] : '';
        object.price_eur = typeof json['price_eur'] != 'undefined' ? json['price_eur'] : '';
        object.name = typeof json['name'] != 'undefined' ? json['name'] : '';
        object.category = typeof json['category'] != 'undefined' ? json['category'] : '';
        object.brand = typeof json['brand'] != 'undefined' ? json['brand'] : '';
        object.quantity = typeof json['quantity'] != 'undefined' ? json['quantity'] : 1;
        object.variant = typeof json['variant'] != '' ? json['variant'] : '';
        object.fb_event_id = typeof json['fb_event_id'] != '' ? json['fb_event_id'] : '';

        this._launchEvent(eventName, variableName, object);
    }

    GoogleMarketingTools.prototype._uuidv4 = function () {
        // TODO: Check arrow function
        return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
            (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
        );
    }

    GoogleMarketingTools.prototype._eventAClicks = function () {
        var instance = this;

        document.addEventListener('click', function (event) {
            if (typeof this.productsListed !== 'undefined' || typeof this.promotionsListed !== 'undefined') {
                var targetLink = event.target.closest('a');

                if (targetLink) {
                    var link = event.target.closest('a').href.replace(/\&/g, '&amp;');

                    if (typeof this.productsListed !== 'undefined') {
                        Object.keys(this.productsListed).forEach(key => {
                            var prod = this.productsListed[key];
                            var url = prod.product.url;

                            if (url != '' && url != '' && (link.includes(url) || url.includes(link))) {
                                instance.trackProductClick(prod.product.product_id);
                            }
                        });
                    }

                    if (typeof this.promotionsListed !== 'undefined') {
                        Object.keys(this.promotionsListed).forEach(key => {
                            var prom = this.promotionsListed[key];
                            var url = prom.url;

                            if (url != '' && url != '' && (link.includes(url) || url.includes(link))) {
                                instance.trackPromotionClick(prom.id);
                            }
                        });
                    }
                }
            }
        });
    }

    GoogleMarketingTools.prototype._getDataLayerVariable = function (varName) {
        if (typeof dataLayer === 'undefined') {
            return false;
        }

        var variable = false;

        Object.keys(dataLayer).map(function (objectKey, index) {
            var value = dataLayer[objectKey];

            if (typeof value[varName] !== 'undefined') {
                variable = value[varName];
            }
        });

        return variable;
    }

    GoogleMarketingTools.prototype._setVideoEvent = function () {
        var i, frames;
        frames = document.getElementsByTagName('iframe');

        for (i = 0; i < frames.length; ++i) {
            var iframe = frames[i];
            var src = iframe.src;

            if (src.includes("player.vimeo")) {
                const player = new Vimeo.Player(iframe);

                player.on('play', function () {
                    player.getVideoTitle().then(function (title) {
                        dataLayer.push({
                            'videoPlay': title,
                            'event': 'Video play'
                        });
                    });
                });
            }
        }
    }

    GoogleMarketingTools.prototype.googleSetTrackSstInfo = function (result) {
        if (result) {
            this.ssTrackInfo = result;
        }
    }

    GoogleMarketingTools.prototype.googleConsentInit = function (json) {
        this.checkConsentStatus();

        this.googleConsentGmtgcSetupStyles();
        this.googleConsentGmtgcSetupPopup();
        this.googleConsentHandleOnCheckedCheckbox();

        this.approveCookiesIfIgnoreTheConsentPopup();
    }

    GoogleMarketingTools.prototype.checkConsentStatus = function () {
        if (GMTGCCookies.get('acceptedGoogleConsent') == 'Yes' && !this.isGmtAdmin) {
            var instance = this;
            instance.googleConsentEventUpdateGoogleConsent(false);
        }
    }

    GoogleMarketingTools.prototype.approveCookiesIfIgnoreTheConsentPopup = function () {
        if (GMTGCCookies.get('acceptedGoogleConsent') !== 'Yes' && !this.isGmtAdmin) {
            var instance = this;
            var scrollAmount = 0;

            if (this.gmtgcAcceptanceWithPageChange) {
                window.addEventListener('beforeunload', function () {
                    if (GMTGCCookies.get('acceptedGoogleConsent') !== 'Yes') {
                        GMTGCCookies.set('acceptedGoogleConsent', 'Yes', { expires: 365 });

                        Object.values(instance.cookiesCategories).flat().forEach(function (cookieName) {
                            GMTGCCookies.set(cookieName, 'granted', { expires: 365 });
                        });

                        instance.googleConsentEventUpdateGoogleConsent();
                    }
                });
            }

            if (this.gmtgcAcceptanceWithScroll) {
                window.addEventListener('scroll', function () {
                    if (GMTGCCookies.get('acceptedGoogleConsent') !== 'Yes') {
                        scrollAmount += window.scrollY;

                        if (scrollAmount > 5000) {
                            GMTGCCookies.set('acceptedGoogleConsent', 'Yes', { expires: 365 });

                            Object.values(instance.cookiesCategories).flat().forEach(function (cookieName) {
                                GMTGCCookies.set(cookieName, 'granted', { expires: 365 });
                            });

                            instance.googleConsentEventUpdateGoogleConsent();
                        }
                    }
                });
            }
        }
    }

    GoogleMarketingTools.prototype.googleConsentHandleOnCheckedCheckbox = function () {
        var categoryCheckboxes = document.querySelectorAll('div.gmtgc_consent_popup input.gmtgc_checkbox');

        categoryCheckboxes.forEach(function (categoryCheckbox) {
            categoryCheckbox.addEventListener('change', function () {
                var label = this.closest('.gmtgc_checkbox_container');

                if (this.checked) {
                    label.classList.add('checked');
                } else {
                    label.classList.remove('checked');
                }
            });

            var label = categoryCheckbox.closest('.gmtgc_checkbox_container');

            if (categoryCheckbox.checked) {
                label.classList.add('checked');
            } else {
                label.classList.remove('checked');
            }

            if (categoryCheckbox.disabled) {
                label.classList.add('disabled');
                categoryCheckbox.classList.add('disabled');
            } else {
                label.classList.remove('disabled');
            }
        });
    }

    GoogleMarketingTools.prototype.googleConsentGmtgcCheckConsent = function () {
        if (GMTGCCookies.get('acceptedGoogleConsent') !== 'Yes') {
            this.googleConsentOpenConsentPopup();
        } else {
            this.googleConsentShowConfigureButton();

            document.querySelector('div.gmtgc_block_screen').style.display = 'none';

            for (var [category, cookies] of Object.entries(this.cookiesCategories)) {
                var input = document.querySelector('div.gmtgc_consent_popup input[name="' + category + '"]');

                var isChecked = input.checked;

                if (category === 'necessary') {
                    input.checked = true;
                } else {
                    input.checked = false;

                    for (var i = 0; i < cookies.length; i++) {
                        var cookieName = cookies[i];

                        if (typeof GMTGCCookies.get(cookieName) !== 'undefined' && GMTGCCookies.get(cookieName) === 'granted') {
                            input.checked = true;
                            break;
                        }
                    }
                }

                if (isChecked !== input.checked) {
                    var event = new Event('change');
                    input.dispatchEvent(event);
                }
            };
        }
    }

    GoogleMarketingTools.prototype.googleConsentAcceptAllCookies = function () {
        GMTGCCookies.set('acceptedGoogleConsent', 'Yes', { expires: 365 });

        for (var [category, cookies] of Object.entries(this.cookiesCategories)) {
            var checkboxCookie = document.querySelector('div.gmtgc_configure_panel input[name="' + category + '"]');
            checkboxCookie.checked = true;

            var event = new Event('change');
            checkboxCookie.dispatchEvent(event);

            for (var i = 0; i < cookies.length; i++) {
                var cookieName = cookies[i];

                GMTGCCookies.remove(cookieName);
                GMTGCCookies.set(cookieName, 'granted', { expires: 365 });
            }
        }

        this.googleConsentEventUpdateGoogleConsent();
        this.googleConsentCloseConsentPopup();
    }

    GoogleMarketingTools.prototype.googleConsentAcceptCurrentSetting = function () {
        for (var [category, cookies] of Object.entries(this.cookiesCategories)) {
            var checkboxCookie = document.querySelector('div.gmtgc_configure_panel input[name="' + category + '"]');

            for (var i = 0; i < cookies.length; i++) {
                var cookieName = cookies[i];
                GMTGCCookies.remove(cookieName);

                if (category === 'necessary' || checkboxCookie.checked) {
                    GMTGCCookies.set(cookieName, 'granted', { expires: 365 });
                } else {
                    GMTGCCookies.set(cookieName, 'denied', { expires: 365 });
                }
            }
        }

        this.googleConsentEventUpdateGoogleConsent();
        this.googleConsentCloseConsentPopup();
    }

    GoogleMarketingTools.prototype.googleConsentRefuseConsent = function () {
        for (var [category, cookies] of Object.entries(this.cookiesCategories)) {
            var checkboxCookie = document.querySelector('div.gmtgc_configure_panel input[name="' + category + '"]');

            for (var i = 0; i < cookies.length; i++) {
                var cookieName = cookies[i];
                GMTGCCookies.remove(cookieName);

                if (category === 'necessary') {
                    GMTGCCookies.set(cookieName, 'granted', { expires: 365 });
                } else {
                    GMTGCCookies.set(cookieName, 'denied', { expires: 365 });
                    checkboxCookie.checked = false;

                    var event = new Event('change');
                    checkboxCookie.dispatchEvent(event);
                }
            }
        }

        GMTGCCookies.set('acceptedGoogleConsent', 'Yes', { expires: 365 });

        this.googleConsentEventUpdateGoogleConsent();
        this.googleConsentCloseConsentPopup();
    }

    GoogleMarketingTools.prototype.googleConsentCloseConsentPopup = function () {
        document.querySelector('div.gmtgc_block_screen').style.display = "none";
        document.querySelector('div.gmtgc_consent_popup').style.display = "none";

        this.googleConsentShowConfigureButton();
    }

    GoogleMarketingTools.prototype.googleConsentOpenConsentPopup = function () {
        var popup = document.querySelector('div.gmtgc_consent_popup');
        popup.classList.add("gmtgc_" + this.gmtgcConsentPopupPosition);

        document.querySelector('div.gmtgc_configure_panel').style.display = "none";

        if (this.gmtgcConsentLockScreen) {
            document.querySelector('div.gmtgc_block_screen').style.display = "block";
        }

        document.querySelector('div.gmtgc_consent_popup').style.display = "block";

        this.googleConsentCloseConsentButton();
        this.googleConsentCheckConsentPopupHeight();
    }

    GoogleMarketingTools.prototype.googleConsentCheckConsentPopupHeight = function () {
        var consent_popup = document.querySelector('div.gmtgc_consent_popup');
        var popup_height = consent_popup.offsetHeight;
        var window_height = window.innerHeight;

        consent_popup.style.height = "auto";

        if (popup_height > window_height) {
            var new_height = window_height - 40;

            consent_popup.style.height = new_height + "px";
            consent_popup.style["overflow-y"] = "scroll";
        } else {
            consent_popup.style.height = "auto";
            consent_popup.style["overflow-y"] = "visible";
        }
    }

    GoogleMarketingTools.prototype.googleConsentCloseConsentButton = function () {
        document.querySelector('div.gmtgc_consent_popup_button_config').style.display = "none";
        document.querySelector('button.gmtgc_configuration').style.display = "block";
        document.querySelector('button.gmtgc_accept_selection').style.display = "none";
    }

    GoogleMarketingTools.prototype.googleConsentShowConfigureButton = function () {
        if (this.gmtgcButtonConfiguration) {
            document.querySelector('div.gmtgc_consent_popup_button_config').classList.add("gmtgc_" + this.gmtgcConsentPopupButtonConfigPosition);
            document.querySelector('div.gmtgc_consent_popup_button_config').style.display = "block";
        }
    }

    GoogleMarketingTools.prototype.googleConsentConfigureConsent = function (fromButton = false) {
        fromButton = typeof fromButton == 'undefined' || fromButton === false ? false : true;

        var configureButton = document.querySelector('button.gmtgc_configuration');
        var acceptSelectionButton = document.querySelector('button.gmtgc_accept_selection');

        if (fromButton) {
            configureButton.style.display = "none";
            acceptSelectionButton.style.display = "block";
            document.querySelector('div.gmtgc_configure_panel').style.display = "block";
        } else {
            configureButton.style.display = "block";
            acceptSelectionButton.style.display = "none";
            document.querySelector('div.gmtgc_configure_panel').style.display = "none";
        }

        this.googleConsentCheckConsentPopupHeight();
    }

    GoogleMarketingTools.prototype.googleConsentEventUpdateGoogleConsent = function (callAddons) {
        if (typeof callAddons == 'undefined') {
            callAddons = true;
        }

        GMTGCCookies.set('acceptedGoogleConsent', 'Yes', { expires: 365 });

        if (typeof dataLayer == 'undefined') {
            console.error("GoogleMarketingTools error: Google tag manager tag not found.");
        }

        var googleConsentUpdate = {};

        Object.values(this.cookiesCategories).flat().forEach(function (cookieName) {
            googleConsentUpdate[cookieName] = GMTGCCookies.get(cookieName);
        });

        dataLayer.push({
            "event": "googleConsentUpdate",
            "googleConsentUpdate": googleConsentUpdate
        });

        // Send conversion to server side addons
        if (callAddons) {
            this.sendConversionToServerSideAddons(this.ssTrackInfo);
        }
    }

    GoogleMarketingTools.prototype.sendConversionToServerSideAddons = function (trackInfo) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', this.callAddonsUrl, true);
        xhr.setRequestHeader("Content-Type", "application/json");

        xhr.onload = function () {
            if (!(xhr.readyState === xhr.DONE && xhr.status == 200)) {
                console.log("error " + xhr.status);
            }
        }

        xhr.onerror = function () {
            console.log('connection error');
        }

        xhr.send(JSON.stringify(trackInfo));
    }

    GoogleMarketingTools.prototype.googleConsentGmtgcSetupPopup = function () {
        var gmtgc_lang_button_refuse_text = this.gmtgcLangText['button_refuse_text'];

        var popup_code = `<div class="gmtgc_block_screen"></div><div class="gmtgc_consent_popup_button_config" onclick="_GoogleMarketingTools.googleConsentOpenConsentPopup();" title="gmtgc_lang_button_configure_title" style="display: none;"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="cookie-bite" class="svg-inline--fa fa-cookie-bite fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M510.52 255.82c-69.97-.85-126.47-57.69-126.47-127.86-70.17 0-127-56.49-127.86-126.45-27.26-4.14-55.13.3-79.72 12.82l-69.13 35.22a132.221 132.221 0 0 0-57.79 57.81l-35.1 68.88a132.645 132.645 0 0 0-12.82 80.95l12.08 76.27a132.521 132.521 0 0 0 37.16 72.96l54.77 54.76a132.036 132.036 0 0 0 72.71 37.06l76.71 12.15c27.51 4.36 55.7-.11 80.53-12.76l69.13-35.21a132.273 132.273 0 0 0 57.79-57.81l35.1-68.88c12.56-24.64 17.01-52.58 12.91-79.91zM176 368c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32zm32-160c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32zm160 128c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32z"></path></svg></div>
                            <div class="gmtgc_consent_popup" style="display: none;">
                                <span class="gmtgc_title">gmtgc_lang_popup_title</span>
                                <span class="gmtgc_description">gmtgc_lang_popup_explain</span>
                                <a class="gmtgc_link" href="gmtgc_lang_link_href">gmtgc_lang_link_title</a>
                                <div style="clear: both;"></div>
                                <button class="gmtgc_button gmtgc_accept_all" onclick="_GoogleMarketingTools.googleConsentAcceptAllCookies();">gmtgc_lang_button_accept_all</button>
                                <button class="gmtgc_button gmtgc_accept_selection" onclick="_GoogleMarketingTools.googleConsentAcceptCurrentSetting();">gmtgc_lang_button_accept_selection</button>
                                <button class="gmtgc_button gmtgc_configuration" onclick="_GoogleMarketingTools.googleConsentConfigureConsent(true);">gmtgc_lang_button_configure</button>`;

        if (gmtgc_lang_button_refuse_text != '') {
            popup_code += `<button class="gmtgc_button gmtgc_refuse" onclick="_GoogleMarketingTools.googleConsentRefuseConsent();">gmtgc_lang_button_refuse_text</button>`;
        }

        popup_code += `<div class="gmtgc_configure_panel" style="display: none;">
                                    <label class="gmtgc_checkbox_container necessary_checkbox">
                                        <div class="gmtgc_api_text">
                                            <label>gmtgc_lang_necessary_label</label>
                                            <p>gmtgc_lang_necessary_explain</p>
                                        </div>
                                        <div class="consent_checkbox">
                                            <input name="necessary" type="checkbox" class="gmtgc_checkbox" checked="checked" disabled>
                                            <div class="gmtgc_checkbox_visual_indicator"><div></div></div>
                                        </div>
                                    </label>

                                    <label class="gmtgc_checkbox_container preferences_checkbox">
                                        <div class="gmtgc_api_text">
                                            <label>gmtgc_lang_preferences_label</label>
                                            <p>gmtgc_lang_preferences_explain</p>
                                        </div>
                                        <div class="consent_checkbox">
                                            <input name="preferences" type="checkbox" class="gmtgc_checkbox" checked="checked">
                                            <div class="gmtgc_checkbox_visual_indicator"><div></div></div>
                                        </div>
                                    </label>
                                
                                    <label class="gmtgc_checkbox_container statistics_checkbox">
                                        <div class="gmtgc_api_text">
                                            <label>gmtgc_lang_statistics_label</label>
                                            <p>gmtgc_lang_statistics_explain</p>
                                        </div>
                                        <div class="consent_checkbox">
                                            <input name="statistics" type="checkbox" class="gmtgc_checkbox" checked="checked">
                                            <div class="gmtgc_checkbox_visual_indicator"><div></div></div>
                                        </div>
                                    </label>
        
                                    <label class="gmtgc_checkbox_container marketing_checkbox">
                                        <div class="gmtgc_api_text">
                                            <label>gmtgc_lang_marketing_label</label>
                                            <p>gmtgc_lang_marketing_explain</p>
                                        </div>
                                        <div class="consent_checkbox">
                                            <input name="marketing" type="checkbox" class="gmtgc_checkbox" checked="checked">
                                            <div class="gmtgc_checkbox_visual_indicator"><div></div></div>
                                        </div>
                                    </label>
                                </div>
                            </div>`;

        var langVariables = this.gmtgcLangText;

        for (var [key, value] of Object.entries(langVariables)) {
            key = 'gmtgc_lang_' + key;
            popup_code = popup_code.replace(key, value);
        }

        //document.getElementsByTagName("body")[0].innerHTML += popup_code;        
        var container = document.createElement('div');

        container.className = 'gmt_google_consent_container';
        container.innerHTML = popup_code;

        this.tryAppendGoogleConsentPopup(container);
    }

    GoogleMarketingTools.prototype.tryAppendGoogleConsentPopup = function (container) {
        var instance = this;

        if (document.body) {
            document.body.appendChild(container);

            instance.googleConsentGmtgcCheckConsent();
            window.addEventListener('resize', instance.googleConsentCheckConsentPopupHeight);
        } else {
            setTimeout(function () {
                instance.tryAppendGoogleConsentPopup(container);
            }, 250);
        }
    }

    GoogleMarketingTools.prototype.googleConsentGmtgcSetupStyles = function () {
        var styleElement = document.createElement('style');
        var stylesCode = `
                        :root {
                            --gmtgc_main_color: #`+ this.gmtgcMainColor + `;
                            --gmtgc_configuration_button_color: #`+ this.gmtgcConfigurationButtonColor + `;
                            --gmtgc_configuration_button_text_color: #`+ this.gmtgcConfigurationButtonTextColor + `;
                            --gmtgc_refuse_button_background_color: #`+ this.gmtgcRefuseButtonBackgroundColor + `;
                            --gmtgc_refuse_button_font_color: #`+ this.gmtgcRefuseButtonFontColor + `;
                            --gmtgc_popup_background: #`+ this.gmtgcPopupBackground + `;
                            --gmtgc_popup_font_color: #`+ this.gmtgcPopupFontColor + `;
                            --gmtgc_button_accept_all_font_color: #`+ this.gmtgcButtonAcceptAllFontColor + `;
                            --gmtgc_button_accept_selection_background_color: #`+ this.gmtgcAcceptSelectionButtonBackgroundColor + `;
                            --gmtgc_button_accept_selection_font_color: #`+ this.gmtgcAcceptSelectionButtonFontColor + `;
                            --gmtgc_configuration_button_svg_color: #`+ this.gmtgcConfigurationButtonSvgColor + `;
                            --gmtgc_necessary_checkbox_color: #`+ this.gmtgcNecessaryCheckboxColor + `;
                            --gmtgc_preferences_checkbox_color: #`+ this.gmtgcPreferencesCheckboxColor + `;
                            --gmtgc_statistics_checkbox_color: #`+ this.gmtgcStatisticsCheckboxColor + `;
                            --gmtgc_marketing_checkbox_color: #`+ this.gmtgcMarketingCheckboxColor + `;
                        }
                        div.gmtgc_block_screen {
                            position: fixed;
                            left: 0px;
                            top: 0px;
                            height: 100%;
                            width: 100%;
                            z-index: 9999999998;
                            background: rgba(0, 0, 0, 0.30);
                            display: none;
                        }
                        div.gmtgc_consent_popup {
                            padding: 25px;
                            border-radius: 4px;
                            max-width: 450px;
                            background: var(--gmtgc_popup_background);
                            font-size: 12px;
                            position: fixed;
                            z-index: 9999999999;
                            box-shadow: 0px 0px 7px rgb(0 0 0 / 30%);
                            color: var(--gmtgc_popup_font_color);
                            border-top: 8px solid var(--gmtgc_main_color);
                        }
                        
                        div.gmtgc_consent_popup.gmtgc_middle {
                            top: 50%;
                            left: 50%;
                            margin-right: -50%;
                            transform: translate(-50%, -50%);
                        }
        
                        div.gmtgc_consent_popup p {
                            margin: 0px;
                        }
        
                        div.gmtgc_consent_popup.gmtgc_top_left {
                            top: 10px;
                            left: 10px;
                        }
                        div.gmtgc_consent_popup.gmtgc_top_right {
                            top: 10px;
                            right: 10px;
                        }
                        div.gmtgc_consent_popup.gmtgc_bottom_left {
                            bottom: 10px;
                            left: 10px;
                        }
                        div.gmtgc_consent_popup.gmtgc_bottom_right {
                            bottom: 10px;
                            right: 10px;
                        }
        
                        span.gmtgc_title {
                            font-size: 16px;
                            font-weight: bold;
                            width: 100%;
                            position: relative;
                            float: left;
                            margin-bottom: 6px;
                        }
                        span.gmtgc_description {
                            line-height: 14px;
                            display: block;
                            margin: 10px 0px;
                        }
                        a.gmtgc_link {
                            border: none;
                            color: var(--gmtgc_main_color);
                            font-weight: bold;
                            display: block;
                            float: left;
                            margin-bottom: 10px;
                        }
        
                        button.gmtgc_button {
                            display: block;
                            border-radius: 4px;
                            text-align: center;
                            width: 100%;
                            height: auto;
                            padding-top: 14px;
                            padding-bottom: 14px;
                            font-weight: bold;
                            font-size: 13px;
                            line-height: 20px;
                            border: none;
                        }
                        button.gmtgc_button:hover {
                            cursor: pointer;
                        }
                        button.gmtgc_button.gmtgc_accept_all {
                            background: var(--gmtgc_main_color);
                            color: var(--gmtgc_button_accept_all_font_color);
                            margin: 15px 0px 8px 0px;
                        }
            
                        button.gmtgc_button.gmtgc_refuse {
                            margin-top: 8px;
                        }
        
                        button.gmtgc_button.gmtgc_configuration {
                            color: var(--gmtgc_configuration_button_text_color);
                            background: var(--gmtgc_configuration_button_color);
                            margin: 15px 0px 15px 0px;
                        }

                        button.gmtgc_button.gmtgc_accept_selection {
                            color: var(--gmtgc_button_accept_selection_font_color);
                            background: var(--gmtgc_button_accept_selection_background_color);
                            margin: 15px 0px 15px 0px;
                        }

                        button.gmtgc_button.gmtgc_refuse {
                            background: var(--gmtgc_refuse_button_background_color);
                            color: var(--gmtgc_refuse_button_font_color);
                        }
        
                        div.gmtgc_api_text,div.consent_checkbox{
                            position: relative;
                            float: left;
                        }

                        div.gmtgc_api_text {
                            padding-right: 5%;
                            width: 90%;
                            text-align: left;
                            box-sizing: border-box;
                        }
        
                        div.consent_checkbox {
                            width: 10%;
                        }
        
                        /* Custom checkboxes*/
                            label.gmtgc_checkbox_container {
                                position: relative;
                                float: left;
                                width: 100%;
                                margin-bottom: 0px;
                                padding-top: 7px;
                                border-bottom: 1px solid var(--gmtgc_main_color);
                                padding-bottom: 12px;
                            }
        
                            label.gmtgc_checkbox_container:first-child {
                                border-top: 1px solid var(--gmtgc_main_color);
                                margin-top: 15px;
                            }
        
                            label.gmtgc_checkbox_container:hover {
                                cursor: pointer;
                            }
        
                            label.gmtgc_checkbox_container label {
                                padding: 0px;
                                margin: 0px;
                                line-height: 10px;
                                font-size: 13px;
                                font-weight: bold;
                                margin-bottom: 5px;
                            }
                            label.gmtgc_checkbox_container div.text_info {
                                font-size: 12px;
                            }
                            label.gmtgc_checkbox_container input[type="checkbox"] {
                                position: absolute;
                                opacity: 0;
                                left: -28px;
                            }

                            label.gmtgc_checkbox_container.checked.disabled div.gmtgc_checkbox_visual_indicator {
                                opacity: 0.5;
                            }
        
                            label.gmtgc_checkbox_container div.gmtgc_checkbox_visual_indicator {
                                vertical-align: middle;
                                width: 40px;
                                height: 20px;
                                border: 1px solid rgba(0,0,0,.4);
                                border-radius: 999px;
                                background-color: rgba(0, 0, 0, 0.1);
                                -webkit-transition-duration: .4s;
                                -webkit-transition-property: background-color, box-shadow;
                                box-shadow: inset 0 0 0 0px rgba(0,0,0,0.4);
                                margin-top: 3px;
                                float: right;
                                display: block !important;
                            }
        
                            label.gmtgc_checkbox_container.checked div.gmtgc_checkbox_visual_indicator {
                                width: 40px;
                                background-position: 0 0;
                            }

                            label.gmtgc_checkbox_container.necessary_checkbox.checked div.gmtgc_checkbox_visual_indicator {
                                background-color: var(--gmtgc_necessary_checkbox_color);
                                border: 1px solid var(--gmtgc_necessary_checkbox_color);
                                box-shadow: inset 0 0 0 10px var(--gmtgc_necessary_checkbox_color);
                            }

                            label.gmtgc_checkbox_container.preferences_checkbox.checked div.gmtgc_checkbox_visual_indicator {
                                background-color: var(--gmtgc_preferences_checkbox_color);
                                border: 1px solid var(--gmtgc_preferences_checkbox_color);
                                box-shadow: inset 0 0 0 10px var(--gmtgc_preferences_checkbox_color);
                            }

                            label.gmtgc_checkbox_container.statistics_checkbox.checked div.gmtgc_checkbox_visual_indicator {
                                background-color: var(--gmtgc_statistics_checkbox_color);
                                border: 1px solid var(--gmtgc_statistics_checkbox_color);
                                box-shadow: inset 0 0 0 10px var(--gmtgc_statistics_checkbox_color);
                            }

                            label.gmtgc_checkbox_container.marketing_checkbox.checked div.gmtgc_checkbox_visual_indicator {
                                background-color: var(--gmtgc_marketing_checkbox_color);
                                border: 1px solid var(--gmtgc_marketing_checkbox_color);
                                box-shadow: inset 0 0 0 10px var(--gmtgc_marketing_checkbox_color);
                            }

                            label.gmtgc_checkbox_container.disabled.checked div.gmtgc_checkbox_visual_indicator {
                                cursor: not-allowed;
                            }
        
                            label.gmtgc_checkbox_container div.gmtgc_checkbox_visual_indicator > div {
                                float: left;
                                width: 18px;
                                height: 18px;
                                border-radius: inherit;
                                background: #fff;
                                -webkit-transition-timing-function: cubic-bezier(.54,1.85,.5,1);
                                -webkit-transition-duration: 0.4s;
                                -webkit-transition-property: transform, background-color, box-shadow;
                                -moz-transition-timing-function: cubic-bezier(.54,1.85,.5,1);
                                -moz-transition-duration: 0.4s;
                                -moz-transition-property: transform, background-color;
                                box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3), 0px 0px 0 1px rgba(0, 0, 0, 0.4);
                                pointer-events: none;
                                margin-top: 0px;
                                margin-left: 1px;
                                display: block !important;
                            }
        
                            label.gmtgc_checkbox_container.checked div.gmtgc_checkbox_visual_indicator > div {
                                -webkit-transform: translate3d(20px, 0, 0);
                                -moz-transform: translate3d(20px, 0, 0);
                                background-color: #fff;
                            }

                            label.gmtgc_checkbox_container.necessary_checkbox.checked div.gmtgc_checkbox_visual_indicator > div {
                                box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3), 0px 0px 0 1px var(--gmtgc_necessary_checkbox_color);
                            }

                            label.gmtgc_checkbox_container.preferences_checkbox.checked div.gmtgc_checkbox_visual_indicator > div {
                                box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3), 0px 0px 0 1px var(--gmtgc_preferences_checkbox_color);
                            }

                            label.gmtgc_checkbox_container.statistics_checkbox.checked div.gmtgc_checkbox_visual_indicator > div {
                                box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3), 0px 0px 0 1px var(--gmtgc_statistics_checkbox_color);
                            }

                            label.gmtgc_checkbox_container.marketing_checkbox.checked div.gmtgc_checkbox_visual_indicator > div {
                                box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3), 0px 0px 0 1px var(--gmtgc_marketing_checkbox_color);
                            }
                        /* Custom checkboxes*/
        
                        /* Config button*/
                            div.gmtgc_consent_popup_button_config {
                                width: 0;
                                height: 0;
                                position: fixed;
                                z-index: 9999999;
                            }
                            div.gmtgc_consent_popup_button_config:hover {
                                cursor: pointer;
                            }
        
                            div.gmtgc_consent_popup_button_config svg {
                                width: 26px;
                                height: 26px;
                                position: relative;
                                color: var(--gmtgc_configuration_button_svg_color);
                            }
        
                            div.gmtgc_consent_popup_button_config.gmtgc_bottom_left svg, div.gmtgc_consent_popup_button_config.gmtgc_bottom_right svg {
                                top: 39px;
                            }
                            div.gmtgc_consent_popup_button_config.gmtgc_bottom_left svg {
                                left: 11px;
                            }
                            div.gmtgc_consent_popup_button_config.gmtgc_bottom_right svg {
                                right: 30px;
                            }
                            div.gmtgc_consent_popup_button_config.gmtgc_top_left svg, div.gmtgc_consent_popup_button_config.gmtgc_top_right svg {
                                bottom: 68px;
                            }
                            div.gmtgc_consent_popup_button_config.gmtgc_top_left svg {
                                left: 7px;
                            }
                            div.gmtgc_consent_popup_button_config.gmtgc_top_right svg {
                                right: 31px;
                            }
        
                            div.gmtgc_consent_popup_button_config.gmtgc_top_left,
                            div.gmtgc_consent_popup_button_config.gmtgc_top_right {
                                border-top: 75px solid var(--gmtgc_main_color);
                                top: 0px;
                            }
        
                            div.gmtgc_consent_popup_button_config.gmtgc_top_left{
                                border-right: 75px solid transparent;
                                left: 0px;
                            }
        
                            div.gmtgc_consent_popup_button_config.gmtgc_top_right{
                                border-left: 75px solid transparent;
                                right: 0px;
                            }
        
                            div.gmtgc_consent_popup_button_config.gmtgc_bottom_left,
                            div.gmtgc_consent_popup_button_config.gmtgc_bottom_right {
                                border-bottom: 75px solid var(--gmtgc_main_color);
                                bottom: 0px;
                            }
        
                            div.gmtgc_consent_popup_button_config.gmtgc_bottom_left{
                                border-right: 75px solid transparent;
                                left: 0px;
                            }
        
        
                            div.gmtgc_consent_popup_button_config.gmtgc_bottom_right{
                                border-left: 75px solid transparent;
                                right: 0px;
                            }
                        /* Config button*/
                        
                        /* Bottom FULL mode */
                        div.gmtgc_consent_popup.gmtgc_bottom_full {
                            bottom: 0px !important;
                            left: 0px !important;
                            width: 100% !important;
                            max-width: 100% !important;
                            padding: 17px 15% !important;
                            border-top: none !important;
                        }
                        
                        div.gmtgc_consent_popup.gmtgc_bottom_full button.gmtgc_button {
                            position: relative;
                            width: auto !important;
                            padding: 14px 32px;
                            margin-top: 0px !important;
                            float: left !important;
                        }
                        
                        div.gmtgc_consent_popup.gmtgc_bottom_full button.gmtgc_button.gmtgc_accept_all,
                        div.gmtgc_consent_popup.gmtgc_bottom_full button.gmtgc_accept_selection {
                            float: left;
                        }
                        
                        div.gmtgc_consent_popup.gmtgc_bottom_full button.gmtgc_accept_selection {
                            margin-left: 10px !important;
                        }
                        
                        div.gmtgc_consent_popup.gmtgc_bottom_full button.gmtgc_button.gmtgc_refuse {
                            float: right !important;
                        }
                        
                        div.gmtgc_consent_popup.gmtgc_bottom_full button.gmtgc_button.gmtgc_configuration {
                            margin-left: 10px !important;
                        }
                        
                        div.gmtgc_consent_popup.gmtgc_bottom_full label.gmtgc_checkbox_container {
                            position: relative !important;
                            float: left !important;
                            border-bottom: none !important;
                            border-top: 1px solid var(--gmtgc_main_color);
                        }
                        
                        div.gmtgc_consent_popup.gmtgc_bottom_full label.gmtgc_checkbox_container:first-child {
                            margin-top: 0px !important;
                        }
                        
                        div.gmtgc_consent_popup.gmtgc_bottom_full div.gmtgc_configure_panel {                            
                            position: relative;
                        }
                        
                        @media (max-width: 675px) {
                            div.gmtgc_consent_popup.gmtgc_bottom_full div.gmtgc_configure_panel {
                                margin-top: 15px !important;
                            }
                        
                            div.gmtgc_consent_popup.gmtgc_bottom_full {
                                padding: 17px 15px !important;
                            }
                        }
                        
                        @media (max-width: 450px) {
                            div.gmtgc_consent_popup.gmtgc_bottom_full button.gmtgc_button.gmtgc_configuration,
                            div.gmtgc_consent_popup.gmtgc_bottom_full button.gmtgc_button {
                                width: 100% !important;
                                margin-left: 0px !important;
                                margin-bottom: 7px !important;
                            }
                        }

                        /* Bottom FULL mode */
                    `;

        styleElement.appendChild(document.createTextNode(stylesCode));
        document.head.appendChild(styleElement);
    }

    if (typeof window.GoogleMarketingTools === 'undefined') {
        window.GoogleMarketingTools = GoogleMarketingTools;
    }
})(window, document);
