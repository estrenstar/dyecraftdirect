<?php
if($this->GMTMaster && $this->GMTMaster->gmt_is_enabled()) {
    $gmt_data = $this->DataLayer->get_datalayer_data();

    $gmt_data['fb_pixel_track_info'] = $this->FacebookApiConversions->set_track_info_api($gmt_data);

    if (isset($this->session->data['gmt_logged']) && isset($gmt_data['account_logged_event'])) {
        unset($this->session->data['gmt_logged']);
    }

    if (!empty($gmt_data['order_data'])) {
        $gmt_data['general_data']['clientId'] = $this->GMTMaster->get_client_id();

        $email = '';
        $customer_info = $this->GMTMaster->get_customer_info();

        if (!empty($customer_info['email'])) {
            $email = $customer_info['email'];
        } elseif (!empty($this->session->data['gmt_customer_email'])) {
            $email = $this->session->data['gmt_customer_email'];
        }

        $gmt_data['general_data']['email'] = $email;

        // Remove order from datalayer if his status is not valid for avoid the conversion.
        if (!$gmt_data['order_data']['order']['valid_order_status']) {
            unset($gmt_data['order_data']);
        }
    }

    $assets_js_url = HTTPS_SERVER .(defined('_JEXEC') ? 'components/com_opencart/catalog/view/javascript/devmanextensions_gmt' : 'catalog/view/javascript/devmanextensions_gmt');

    // Get Google tag manager code
    $head_gmt = $this->GoogleTagManager->get_code_head($gmt_data);

    $head_gmt .= $this->GoogleTagManager->get_google_consent_compatibility_cookie_script();

    $head_gmt .= '<script type="text/javascript" nitro-exclude="" src="'.$assets_js_url.'/data-layer-events.js?v='.$this->GMTMaster->get_extension_version().'"></script>';

    $head_gmt .= '<script type="text/javascript" nitro-exclude="">
                    var scriptElement = document.createElement("script");
                    scriptElement.src = "'.$assets_js_url.'/gmt-library.js?v='.$this->GMTMaster->get_extension_version().'";
                    scriptElement.setAttribute("nitro-exclude", "");

                    var _GoogleMarketingTools;
                    
                    var datalayerDataGMT = '.json_encode($gmt_data, JSON_UNESCAPED_UNICODE).';

                    
                    scriptElement.onload = function() {
                        if (typeof GoogleMarketingTools === "undefined") {
                            console.error("GoogleMarketingTools error: JS Library GoogleMarketingTools not loaded.");
                        } else {
                            var google_marketing_tools_settings = {
                                "lang" : gmt_language,
                                "isDebugEnabled": is_debug_enabled,
                                "isGmtAdmin": is_gmt_admin,
                                "trackApiUrl": track_api_url,
                                "callAddonsUrl": call_addons_url,
                                "isGoogleConsentEnabled": is_google_consent_enabled,
                                "cookiebotCompatibility": gc_settings.gdpr_cookiebot_status,
                                "googleConsentSettings": {
                                    "acceptanceWithPageChange": gc_settings.gdpr_acceptance_with_page_change,
                                    "acceptanceWithScroll": gc_settings.gdpr_acceptance_with_scroll,
                                    "buttonConfiguration": gc_settings.gdpr_button_configuration,
                                    "consentPopupPosition": gc_settings.gdpr_popup_position,
                                    "consentLockScreen": gc_settings.gdpr_lock_screen,
                                    "popupDelayTime": gc_settings.gdpr_popup_delay_time,
                                    "consentPopupButtonConfigPosition": gc_settings.gdpr_button_position,
                                    "mainColor": gc_settings.gdpr_bar_main_color,
                                    "configurationButtonSvgColor": gc_settings.gdpr_button_icon_color,
                                    "configurationButtonColor": gc_settings.gdpr_bar_background_button_configure,
                                    "configurationButtonTextColor": gc_settings.gdpr_bar_text_button_configure,
                                    "refuseButtonBackgroundColor": gc_settings.gdpr_bar_background_button_refuse,
                                    "refuseButtonFontColor": gc_settings.gdpr_bar_text_button_refuse,
                                    "popupBackground": gc_settings.gdpr_bar_background,
                                    "popupFontColor": gc_settings.gdpr_bar_color,
                                    "buttonAcceptAllFontColor": gc_settings.gdpr_bar_button_accept_all_color,                                    
                                    "acceptSelectionButtonBackgroundColor": gc_settings.gdpr_bar_button_accept_selection_color,
                                    "acceptSelectionButtonFontColor": gc_settings.gdpr_bar_text_button_accept_selection,
                                    "necessaryCheckboxColor": gc_settings.gdpr_bar_necessary_checkbox_color,
                                    "preferencesCheckboxColor": gc_settings.gdpr_bar_preferences_checkbox_color,
                                    "statisticsCheckboxColor": gc_settings.gdpr_bar_statistics_checkbox_color,
                                    "marketingCheckboxColor": gc_settings.gdpr_bar_marketing_checkbox_color,                                    
                                    "languages": gc_settings.languages
                                }
                            };
                        
                            _GoogleMarketingTools = new GoogleMarketingTools(google_marketing_tools_settings);
                                    
                            var xhr = new XMLHttpRequest();
                            var url = dataLayer_setup_link;  // URL del servidor para recibir la petición
                            xhr.open("POST", url, true);
                            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  // Indicar que el contenido será un formulario codificado
                            
                            // Crear los parámetros que se enviarán
                            var params = "datalayerDataGMT=" + encodeURIComponent(JSON.stringify(datalayerDataGMT));
                            
                            xhr.responseType = "json";
                            
                            xhr.onload = function() {
                                if (xhr.readyState === xhr.DONE && xhr.status === 200) {
                                    var result = xhr.response;
                                    googleMarketingTools_Start(result);
                                    _GoogleMarketingTools.googleSetTrackSstInfo(result);
                                }
                            };
                            
                            xhr.send(params);  // Enviar los datos con POST
                        }
                    };
                
                    document.head.appendChild(scriptElement);
                </script>';

    // Custom code
    $head_gmt .= $this->GoogleTagManager->get_custom_code();

    $data_to_view['head_gmt'] = $head_gmt;
    $data_to_view['body_gmt'] = $this->GoogleTagManager->get_code_body();

    // Debug mode
    if($this->GMTMaster->debug_enabled()) {
        $data_to_view['body_gmt'] .= $this->GMTMaster->_load_controller("extension/module/gmt_debug");
    }

    // JooCart compatibility
    if (defined('_JEXEC')) {
        $document = JFactory::getDocument();

        foreach ($data_to_view as $data_name => $value) {
            if ($data_name == 'head_gmt') {
                $document->addCustomTag($value);

                unset($data_to_view[$data_name]);
            }
        }
    }

    //Send data to view
    foreach ($data_to_view as $data_name => $value) {
        if (version_compare(VERSION, '2.0.0.0', '>=')) {
            $data[$data_name] = $value;
        } else {
            $this->data[$data_name] = $value;
        }
    }
}
?>