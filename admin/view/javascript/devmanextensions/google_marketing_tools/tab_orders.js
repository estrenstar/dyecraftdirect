var GmtTabOrders = (function () {
    let range = 5;
    let currentPage = 1;
    let firstRender = true;

    function updatePagination(numPages) {
        let html = '';
        let startPage = Math.max(1, currentPage - range);
        let endPage = Math.min(numPages, currentPage + range);

        // Adjust the range if it’s near the start or end
        if (currentPage - startPage < range) {
            endPage = Math.min(numPages, startPage + 2 * range);
        }

        if (endPage - currentPage < range) {
            startPage = Math.max(1, endPage - 2 * range);
        }

        if (numPages > 1) {
            for (let i = startPage; i <= endPage; i++) {
                var page_item_class = i === currentPage ? 'gmt-page-item active' : 'gmt-page-item';
                html += `<li class="${page_item_class}"><button class="gmt-page-link" data-page="${i}">${i}</button></li>`;
            }
        }

        $("#gmtOrdersPagination").html(html);
    }

    async function updateTable($this, pageNum, buttonHtml, gmtI18n) {
        let totalPages;
        let tableBody = $('table#gmtOrders tbody');

        try {
            let response = await $.ajax({
                url: link_ajax_get_orders + '&page=' + pageNum,
                method: 'post',
                data: getFilterData(),
                dataType: 'json',
                beforeSend: function () {
                    $this.prop('disabled', true);

                    if ($this.hasClass('btn-filter-orders')) {
                        $this.html('<i class="fa fa-spinner fa-spin"></i>' + gmtI18n.textFilterOrders);
                    } else {
                        $this.addClass('loading').html('<div class="gmt-loader"></div>');
                    }
                },
                complete: function () {
                    $this.prop('disabled', false);
                    $this.html(buttonHtml);
                }
            });

            let html = '';
            let orders = response['orders'];

            if (!response['message']) {
                let countOrders = orders.length;

                for (let i = 0; i < countOrders; i++) {
                    let order = orders[i];

                    html += '<tr>' +
                        '<td><span>' + order[0] + '</span></td>' +
                        '<td><span>' + order[1] + '</span></td>' +
                        '<td><span>' + order[2] + '</span></td>' +
                        '<td><span>' + order[3] + '</span></td>' +
                        '<td><span>' + order[4] + '</span></td>' +
                        '<td><span>' + order[5] + '</span></td>' +
                        '<td><span>' + order[6] + '</span></td>' +
                        '<td><span>' + order[7] + '</span></td>' +
                        '<td><span>' + order[8] + '</span></td>' +
                        '<td><span>' + order[9] + '</span></td>' +
                        '<td><span>' + order[10] + '</span></td>' +
                        '<td><span>' + order[11] + '</span></td>' +
                        '<td><span>' + order[12] + '</span></td>' +
                        '<td><span>' + order[13] + '</span></td>' +
                        '<td><span>' + order[14] + '</span></td>' +
                        '</tr>';
                }
            } else {
                html += '<tr><td colspan="15"><span>' + response['message'] + '</span></td></tr>';
            }

            tableBody.html(html);
            totalPages = response['total_pages'];
        } catch (error) {
            console.log(`${error}\r\n${error.statusText}\r\n${error.responseText}`);
        }

        return totalPages;
    }

    function handlePagination() {
        $("#gmtOrdersPagination").on('click', '.gmt-page-link', async function (e) {
            e.preventDefault();

            let $this = $(this);
            let pageNum = parseInt($(this).data('page'));
            const buttonHtml = $this.html();

            if (currentPage !== pageNum) {
                let countPages = await updateTable($this, pageNum, buttonHtml);
                currentPage = pageNum;

                updatePagination(countPages);
            }
        });
    }

    async function getOrderData(orderId, getOriginalData = false) {
        const url = `${link_ajax_get_order}&order_id=${orderId}&get_original_data=${getOriginalData ? 1 : 0}`;

        try {
            const response = await $.get(url);
            return response;
        } catch (error) {
            console.error(`Error fetching order data: ${error.statusText}`);
            throw error;
        }
    }

    function disabledCookies() {
        if (typeof GMTGCCookies !== 'undefined') {
            GMTGCCookies.remove('acceptedGoogleConsent');
            GMTGCCookies.remove('_gcl_aw');
            GMTGCCookies.remove('security_storage');
            GMTGCCookies.remove('functionality_storage');
            GMTGCCookies.remove('personalization_storage');
            GMTGCCookies.remove('analytics_storage');
            GMTGCCookies.remove('ad_storage');
            GMTGCCookies.remove('ad_user_data');
            GMTGCCookies.remove('ad_personalization');
        }
    }

    function trackOrder(data, type, orderId) {
        if (typeof _GoogleMarketingTools === 'undefined') {
            alert('_GoogleMarketingTools object is not defined. Contact support team');
            console.error('_GoogleMarketingTools object is not defined');

            return false;
        }

        let gmtInfo = data.gmt_info;

        if (type !== 'sent') {
            gmtInfo.fb_pixel_track_info.value *= -1;
            gmtInfo.order_data.order.total *= -1;
            gmtInfo.order_data.order.subtotal *= -1;
            gmtInfo.order_data.order.revenue *= -1;
            gmtInfo.order_data.order.tax *= -1;
            gmtInfo.order_data.order.shipping *= -1;
        }

        _GoogleMarketingTools.init(gmtInfo.general_data);

        _GoogleMarketingTools.setFacebookPixelTrack(gmtInfo.fb_pixel_track_info);

        // Allow Google Consent Config
        GMTGCCookies.set('acceptedGoogleConsent', 'yes', { expires: 365 });
        GMTGCCookies.set('_gcl_aw', 'granted', { expires: 365 });
        GMTGCCookies.set('security_storage', 'granted', { expires: 365 });
        GMTGCCookies.set('functionality_storage', 'granted', { expires: 365 });
        GMTGCCookies.set('personalization_storage', 'granted', { expires: 365 });
        GMTGCCookies.set('analytics_storage', 'granted', { expires: 365 });
        GMTGCCookies.set('ad_storage', 'granted', { expires: 365 });
        GMTGCCookies.set('ad_user_data', 'granted', { expires: 365 });
        GMTGCCookies.set('ad_personalization', 'granted', { expires: 365 });

        if (firstRender) {
            var googleConsentUpdate = {};

            googleConsentUpdate = {
                security_storage: true,
                functionality_storage: true,
                personalization_storage: true,
                analytics_storage: true,
                ad_storage: true,
                ad_user_data: true,
                ad_personalization: true
            };

            dataLayer.push({
                "event": "googleConsentUpdate",
                "googleConsentUpdate": googleConsentUpdate
            });

            firstRender = false;
        }

        setTimeout(function () {
            _GoogleMarketingTools.trackPurchase(gmtInfo.order_data);

            // Send conversion to server side addons
            _GoogleMarketingTools.sendConversionToServerSideAddons(gmtInfo);
        }, 1000);

        setTimeout(function () {
            // We deny cookies to prevent the main tags from popping up on future visits
            GMTGCCookies.remove('acceptedGoogleConsent');
            GMTGCCookies.remove('_gcl_aw');
            GMTGCCookies.remove('security_storage');
            GMTGCCookies.remove('functionality_storage');
            GMTGCCookies.remove('personalization_storage');
            GMTGCCookies.remove('analytics_storage');
            GMTGCCookies.remove('ad_storage');
            GMTGCCookies.remove('ad_user_data');
            GMTGCCookies.remove('ad_personalization');
        }, 1500);

        var requestData = {
            order_id: orderId,
            action: type == 'sent' ? 'sent' : 'cancel',
            from_admin: true,
            order_total: gmtInfo.order_data.order.total
        }

        const xhr = new XMLHttpRequest();

        xhr.open('post', trackApiUrl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
        xhr.responseType = 'text';

        xhr.onload = function () {
            if (xhr.readyState === xhr.DONE && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                const elementId = type === 'sent' ? 'sent_' + orderId : 'cancel_' + orderId;

                if (response.date.length) {
                    $('#' + elementId).removeClass('text-warning').html(response.date);
                }
            }
        }

        xhr.send(JSON.stringify(requestData));

        return true;
    }

    function handleSendOrder(gmtI18n) {
        $('body').on('click', '.btn-send-order', async function (e) {
            e.preventDefault();

            const $this = $(this);
            const orderId = $this.data('order-id');
            const buttonHtml = $this.html();
            let message = $this.data('message');
            const isOrderModified = $this.data('is-modified');

            $.confirm({
                title: gmtI18n.textSendConversion,
                content: message,
                escapeKey: 'cancel',
                boxWidth: '400px',
                useBootstrap: false,
                icon: 'fa fa-warning',
                theme: isOrderModified ? 'devman-light-2' : 'devman-light',
                draggable: false,
                buttons: {
                    cancel: {
                        text: gmtI18n.textNo,
                        keys: ['escape'],
                        btnClass: 'btn-dark',
                        action: function () { }
                    },
                    confirm: {
                        text: isOrderModified ? gmtI18n.textYesSentOriginalOrder : gmtI18n.textYes,
                        keys: isOrderModified ? ['o'] : ['enter'],
                        btnClass: 'btn-green',
                        action: async function () {
                            this.buttons.confirm.disable();

                            $this.html('<div class="gmt-loader"></div>');

                            try {
                                const orderData = await getOrderData(orderId, isOrderModified ? true : false);
                                trackOrder(orderData, 'sent', orderId);

                                // Show message
                                showSuccessToast(gmtI18n.textSuccess, gmtI18n.textConversionSent);
                            } catch (error) {
                                showErrorToast(gmtI18n.textError, error);
                            }

                            $this.html(buttonHtml);

                            return false;
                        }
                    },
                    sendCurrentOrder: {
                        text: isOrderModified ? gmtI18n.textYesSentCurrentOrder : '',
                        keys: ['c'],
                        btnClass: 'btn-green',
                        isHidden: !isOrderModified,
                        action: async function () {
                            this.buttons.confirm.disable();

                            $this.html('<div class="gmt-loader"></div>');

                            try {
                                const orderData = await getOrderData(orderId);
                                trackOrder(orderData, 'sent', orderId);

                                // Show message
                                showSuccessToast(gmtI18n.textSuccess, gmtI18n.textConversionSent);
                            } catch (error) {
                                showErrorToast(gmtI18n.textError, error);
                            }

                            $this.html(buttonHtml);

                            return false;
                        }
                    }
                }
            });

            return false;
        });
    }

    function showSuccessToast(headerText, message) {
        $.toast({
            heading: headerText,
            text: message,
            icon: 'success',
            position: 'top-right',
            bgColor: '#2ecc71',
            loader: false
        });
    }

    function showErrorToast(headerText, message) {
        $.toast({
            heading: headerText,
            text: message,
            icon: 'error',
            position: 'top-right',
            bgColor: '#2ecc71',
            loader: false
        });
    }

    function handleCancelOrder(gmtI18n) {
        $('body').on('click', '.btn-cancel-order', async function (e) {
            e.preventDefault();

            let $this = $(this);
            const orderId = $this.data('order-id');
            const buttonHtml = $this.html();
            let message = $this.data('message');
            const isOrderModified = $this.data('is-modified');

            $.confirm({
                title: gmtI18n.textCancelConversion,
                content: message,
                boxWidth: '400px',
                useBootstrap: false,
                icon: 'fa fa-warning',
                theme: isOrderModified ? 'devman-light-2' : 'devman-light',
                draggable: false,
                buttons: {
                    cancel: {
                        text: gmtI18n.textNo,
                        keys: ['escape'],
                        btnClass: 'btn-dark',
                        action: function () { }
                    },
                    confirm: {
                        text: isOrderModified ? gmtI18n.textYesSentOriginalOrder : gmtI18n.textYes,
                        keys: isOrderModified ? ['o'] : ['enter'],
                        btnClass: 'btn-green',
                        action: async function () {
                            this.buttons.confirm.disable();

                            $this.html('<div class="gmt-loader"></div>');

                            try {
                                const orderData = await getOrderData(orderId, isOrderModified ? true : false);
                                trackOrder(orderData, 'cancel', orderId);

                                // Show message
                                showSuccessToast(gmtI18n.textSuccess, gmtI18n.textConversionCancelled);
                            } catch (error) {
                                showErrorToast(gmtI18n.textError, error);
                            }

                            $this.html(buttonHtml);

                            return false;
                        }
                    },
                    sendCurrentOrder: {
                        text: isOrderModified ? gmtI18n.textYesSentCurrentOrder : '',
                        keys: ['c'],
                        btnClass: 'btn-green',
                        isHidden: !isOrderModified,
                        action: async function () {
                            this.buttons.confirm.disable();

                            $this.html('<div class="gmt-loader"></div>');

                            try {
                                const orderData = await getOrderData(orderId);
                                trackOrder(orderData, 'ancel', orderId);

                                // Show message
                                showSuccessToast(gmtI18n.textSuccess, gmtI18n.textConversionCancelled);
                            } catch (error) {
                                showErrorToast(gmtI18n.textError, error);
                            }

                            $this.html(buttonHtml);

                            return false;
                        }
                    }
                }
            });

            return false;
        });
    }

    function handleViewOrderInfo() {
        $('body').on('click', '.btn-order-info', function (e) {
            e.preventDefault();

            var orderId = $(this).attr('data-order-id');
            var modal = $('.gmt-order-info');

            $.ajax({
                url: link_ajax_get_order_info + '&order_id=' + orderId,
                dataType: 'html',
                success: function (data) {
                    modal.html(data);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(`${thrownError}\r\n${xhr.statusText}\r\n${xhr.responseText}`);
                }
            });
        });
    }

    function handleChangeStore() {
        $(document).on('gmtStoreChanged', function (e, storeId) {
            $.ajax({
                url: link_ajax_save_store_id,
                type: 'POST',
                data: { store_id: storeId },
                beforeSend: function () {
                    ajax_loading_open();
                },
                success: function (response) {
                    location.reload();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(`${thrownError}\r\n${xhr.statusText}\r\n${xhr.responseText}`);
                    ajax_loading_close();
                }
            });
        });
    }

    function getFilterData() {
        var filterGoogleAds = $('input[name="filter_google_ads"]:checked').val();
        var filterFacebookAds = $('input[name="filter_facebook_ads"]:checked').val();
        var filterBingAds = $('input[name="filter_bing_ads"]:checked').val();

        var filterMarketingPlataform = {
            filter_google_ads: filterGoogleAds,
            filter_facebook_ads: filterFacebookAds,
            filter_bing_ads: filterBingAds
        };

        var filterMarketingCookies = $('input[name="filter_marketing_cookies"]:checked').val();

        var filter_date_created_from = $('input[name="filter_date_created_from"]').val();
        var filter_date_created_to = $('input[name="filter_date_created_to"]').val();

        var filterDateCreated = {
            from: filter_date_created_from,
            to: filter_date_created_to
        };

        return {
            filter_marketing_plataform: filterMarketingPlataform,
            filter_marketing_cookies: filterMarketingCookies,
            filter_date_created: filterDateCreated
        };
    }

    function handleFilter(gmtI18n) {
        $('#btn-order-filter').on('click', async function (e) {
            e.preventDefault();

            let $btnFilterOrders = $('#filterDropdownMenu');
            let pageNum = 1;
            const buttonHtml = $btnFilterOrders.html();

            let countPages = await updateTable($btnFilterOrders, pageNum, buttonHtml, gmtI18n);
            currentPage = pageNum;

            updatePagination(countPages);
        });
    }

    function handleExport(gmtI18n) {
        $(document).on('click', '#gmtExportOrders', function (e) {
            e.preventDefault();

            $this = $(this);
            const buttonHtml = $this.html();
            var filters = getFilterData();
            filters.export_orders = '1';

            $.ajax({
                url: link_ajax_export_orders,
                method: 'post',
                data: filters,
                dataType: 'json',
                beforeSend: function () {
                    $this.prop('disabled', true);
                    $this.html('<i class="fa fa-spinner fa-spin"></i>' + gmtI18n.textExportOrders);
                },
                complete: function () {
                    $this.prop('disabled', false);
                    $this.html(buttonHtml);
                },
                success: function (json) {
                    if (json.redirect) {
                        window.location = json.redirect;

                        /* $('#message').html(json.message);
                        $('#messageModal').modal('show');

                        setTimeout(function () {
                            $('#messageModal').modal('hide');
                        }, 5000); */
                    } else {
                        $('#message').html(json.error);
                        $('#messageModal').modal('show');

                        setTimeout(function () {
                            $('#messageModal').modal('hide');
                        }, 20000);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(`${thrownError}\r\n${xhr.statusText}\r\n${xhr.responseText}`);
                }
            });
        });
    }

    function handleDropdownMenuOpen() {
        $('.dropdown-menu').on('click', function (e) {
            if (!$(e.target).is('#btn-order-filter')) {
                e.stopPropagation();
            }
        });
    }

    function initDatetimePicker() {
        $('.date').datetimepicker({
            language: 'en-gb',
            pickTime: false
        });
    }

    return {
        init: function () {
            const defaultTexts = {
                'textYes': 'Yes',
                'textNo': 'No',
                'textYesSentOriginalOrder': 'Yes - Sent original order',
                'textYesSentCurrentOrder': 'Yes - Sent current order',
                'textError': 'An error has ocurred',
                'textSuccess': 'Success',
                'textConversionSent': 'Conversion sent',
                'textConversionCancelled': 'Conversion cancelled',
                'textSendConversion': 'Send conversion',
                'textCancelConversion': 'Cancel conversion',
                'textFilterOrders': 'Filter Orders',
                'textExportOrders': 'Export Orders'
            };

            const gmtI18nTexts = typeof gmtI18n !== 'undefined' ? gmtI18n : defaultTexts;

            disabledCookies();
            handlePagination();
            handleSendOrder(gmtI18nTexts);
            handleCancelOrder(gmtI18nTexts);
            handleViewOrderInfo();
            handleChangeStore();
            handleFilter(gmtI18nTexts);
            handleExport(gmtI18nTexts);
            handleDropdownMenuOpen();
            initDatetimePicker();
        }
    };
})();

$(function () {
    GmtTabOrders.init();
});
