<?php
    class ModelExtensionModuleGoogleAllTabWorkspace extends ModelExtensionModuleGoogleAll
    {
        private $extension_version;

        public function __construct($registry) {
            parent::__construct($registry);
            $this->load->language($this->real_extension_type.'/google_all_tab_workspace');

            $this->extension_version = $registry->has('gmt_extension_version') ? $this->gmt_extension_version : date('Ymdhis');
        }

        public function get_fields() {
            $this->document->addStyle($this->api_url.'opencart_admin/google_marketing_tools/css/tab_workspace.css?v='.$this->extension_version);
            $this->document->addScript($this->api_url.'opencart_admin/google_marketing_tools/js/tab_workspace.js?v='.$this->extension_version);

            $fields = array(
                array(
                    'type'              => 'html_hard',
                    'skip_for_multistore' => true,
                    'html_code'         => '<div class="alert alert-info">'.$this->_lang('workspace_explain').'</div>'
                ),

                // License Order ID
                array(
                    'label'             => '<img class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/oc.jpg">'.$this->_lang('license_id'),
                    'help'              => $this->_lang('license_id_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_license_id'),
                    'name'              => 'license_id'
                ),

                // GTM Container ID
                array(
                    'label'             => '<img class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/gtm.jpg">'.$this->_lang('container_id'),
                    'help'              => $this->_lang('container_id_help'),
                    'type'              => 'text',
                    'name'              => 'container_id_workspace'
                ),
                
                // Include shipping cost in total
                array(
                    'label'             => $this->_lang('include_shipping_cost_in_total_label'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_include_shipping_cost_in_total'),
                    'name'              => 'include_shipping_cost_in_total'
                ),

                // GA4
                array(
                    'label'             => '<img class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/ga4.jpg">'.$this->_lang('ga4_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_ga4_status'),
                    'name'              => 'ga4_status',
                    'class_container'   => 'toogle_main_field',
                ),

                array(
                    'label'             => $this->_lang('ga4_measurement_id'),
                    'type'              => 'text',
                    'help'              => $this->_lang('ga4_measurement_id_help'),
                    'help_bottom'       => $this->_lang('ga4_measurement_id_measurement_id_tutorial'),
                    'value'             => $this->config->get('google_ga4_measurement_id'),
                    'name'              => 'ga4_measurement_id',
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_ga4_status',
                ),

                // GA4 - Enhanced Ecommerce
                array(
                    'label'             => '<img class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/ee.jpg">'.$this->_lang('enhanced_ecommerce_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_enhanced_ecommerce_status'),
                    'name'              => 'enhanced_ecommerce_status'
                ),    
                 
                // Google Ads conversion (compatible with Enhanced Conversions)
                array(
                    'label'             => '<img class="tag_icon go" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/gads.png">'.$this->_lang('google_ads_conversion_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_google_ads_conversion_status'),
                    'name'              => 'google_ads_conversion_status',
                    'class_container'   => 'toogle_main_field'
                ),

                array(
                    'label'             => $this->_lang('google_ads_conversion_id'),
                    'help'              => $this->_lang('google_ads_conversion_id_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_google_ads_conversion_id'),
                    'name'              => 'google_ads_conversion_id',
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_google_ads_conversion_status'
                ),             

                array(
                    'label'             => $this->_lang('google_ads_conversion_label'),
                    'help'              => $this->_lang('google_ads_conversion_label_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_google_ads_conversion_label'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_google_ads_conversion_status',
                    'name'              => 'google_ads_conversion_label'
                ),
               
                // Google Ads custom secondary conversion (compatible with Enhanced Conversions)
                array(
                    'label'             => '<img class="tag_icon go" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/gads.png">'.$this->_lang('google_ads_cs_conversion_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_google_ads_cs_conversion_status'),
                    'name'              => 'google_ads_cs_conversion_status',
                    'class_container'   => 'toogle_main_field',
                ),

                array(
                    'label'             => $this->_lang('google_ads_cs_conversion_id'),
                    'help'              => $this->_lang('google_ads_cs_conversion_id_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_google_ads_cs_conversion_id'),
                    'name'              => 'google_ads_cs_conversion_id',
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_google_ads_cs_conversion_status',
                ),

                array(
                    'label'             => $this->_lang('google_ads_cs_conversion_label'),
                    'help'              => $this->_lang('google_ads_cs_conversion_label_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_google_ads_cs_conversion_label'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_google_ads_cs_conversion_status',
                    'name'              => 'google_ads_cs_conversion_label'
                ),

                array(
                    'label'             => $this->_lang('google_ads_cs_conversion_route'),
                    'help'              => $this->_lang('google_ads_cs_conversion_route_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_google_ads_cs_conversion_route'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_google_ads_cs_conversion_status',
                    'name'              => 'google_ads_cs_conversion_route'
                ),

                array(
                    'label'             => $this->_lang('google_ads_cs_conversion_value'),
                    'help'              => $this->_lang('google_ads_cs_conversion_value_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_google_ads_cs_conversion_value'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_google_ads_cs_conversion_status',
                    'name'              => 'google_ads_cs_conversion_value'
                ),
               
                // Google Ads custom tertiary conversion (compatible with Enhanced Conversions)
                array(
                    'label'             => '<img class="tag_icon go" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/gads.png">'.$this->_lang('google_ads_ct_conversion_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_google_ads_ct_conversion_status'),
                    'name'              => 'google_ads_ct_conversion_status',
                    'class_container'   => 'toogle_main_field',
                ),

                array(
                    'label'             => $this->_lang('google_ads_ct_conversion_id'),
                    'help'              => $this->_lang('google_ads_ct_conversion_id_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_google_ads_ct_conversion_id'),
                    'name'              => 'google_ads_ct_conversion_id',
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_google_ads_ct_conversion_status',
                ),

                array(
                    'label'             => $this->_lang('google_ads_ct_conversion_label'),
                    'help'              => $this->_lang('google_ads_ct_conversion_label_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_google_ads_ct_conversion_label'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_google_ads_ct_conversion_status',
                    'name'              => 'google_ads_ct_conversion_label'
                ),

                array(
                    'label'             => $this->_lang('google_ads_ct_conversion_route'),
                    'help'              => $this->_lang('google_ads_ct_conversion_route_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_google_ads_ct_conversion_route'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_google_ads_ct_conversion_status',
                    'name'              => 'google_ads_ct_conversion_route'
                ),

                array(
                    'label'             => $this->_lang('google_ads_ct_conversion_value'),
                    'help'              => $this->_lang('google_ads_ct_conversion_value_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_google_ads_ct_conversion_value'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_google_ads_ct_conversion_status',
                    'name'              => 'google_ads_ct_conversion_value'
                ),

                // Add to cart conversion
                array(
                    'label'             => '<img class="tag_icon go" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/gads.png">'.$this->_lang('add_to_cart_conversion_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_add_to_cart_conversion_status'),
                    'name'              => 'add_to_cart_conversion_status',
                    'class_container'   => 'toogle_main_field',
                ),
                array(
                    'label'             => $this->_lang('add_to_cart_conversion_id'),
                    'help_bottom'       => $this->_lang('add_to_cart_conversion_id_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_add_to_cart_conversion_id'),
                    'name'              => 'add_to_cart_conversion_id',
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_add_to_cart_conversion_status',
                ),
                array(
                    'label'             => $this->_lang('add_to_cart_conversion_label'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_add_to_cart_conversion_label'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_add_to_cart_conversion_status',
                    'name'              => 'add_to_cart_conversion_label'
                ),
                
                // Google Reviews
                array(
                    'label'             => '<img class="tag_icon go" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/greviews.png">'.$this->_lang('google_reviews_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_google_reviews_status'),
                    'name'              => 'google_reviews_status',
                    'class_container'   => 'toogle_main_field',
                ),

                array(
                    'label'             => $this->_lang('google_reviews_merchant_id'),
                    'help'              => $this->_lang('google_reviews_merchant_id_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_google_reviews_merchant_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_google_reviews_status',
                    'name'              => 'google_reviews_merchant_id'
                ),

                array(
                    'label'             => $this->_lang('google_reviews_style'),
                    'help'              => $this->_lang('google_reviews_style_help'),
                    'type'              => 'select',
                    'options'           => [
                        'CENTER_DIALOG'         => $this->_lang('google_reviews_style_center_dialog'),
                        'BOTTOM_RIGHT_DIALOG'   => $this->_lang('google_reviews_style_bottom_right_dialog'),
                        'BOTTOM_LEFT_DIALOG'    => $this->_lang('google_reviews_style_bottom_left_dialog'),
                        'TOP_RIGHT_DIALOG'      => $this->_lang('google_reviews_style_top_right_dialog'),
                        'TOP_LEFT_DIALOG'       => $this->_lang('google_reviews_style_top_left_dialog'),
                        'BOTTOM_TRAY'           => $this->_lang('google_reviews_style_bottom_tray'),
                    ],
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_google_reviews_status',
                    'name'              => 'google_reviews_style'
                ),

                array(
                    'label'             => $this->_lang('google_reviews_delivery_days'),
                    'help'              => $this->_lang('google_reviews_delivery_days_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_google_reviews_delivery_days'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_google_reviews_status',
                    'name'              => 'google_reviews_delivery_days',
                    'default'           => 3
                ),
                
                // Google Ads Remarketing
                array(
                    'label'             => '<img class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/dynamic_remarketing.jpg">'.$this->_lang('dynamic_remarketing_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_dynamic_remarketing_status'),
                    'name'              => 'dynamic_remarketing_status',
                    'class_container'   => 'toogle_main_field',
                ),

                array(
                    'label'             => $this->_lang('dynamic_remarketing_id'),
                    'help'              => $this->_lang('dynamic_remarketing_id_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_dynamic_remarketing_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_dynamic_remarketing_status',
                    'name'              => 'dynamic_remarketing_id'
                ),

                array(
                    'label'             => $this->_lang('dynamic_remarketing_label'),
                    'help'              => $this->_lang('dynamic_remarketing_label_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_dynamic_remarketing_label'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_dynamic_remarketing_status',
                    'name'              => 'dynamic_remarketing_label'
                ),

                array(
                    'label'             => $this->_lang('dynamic_remarketing_id_prefix'),
                    'help'              => $this->_lang('dynamic_remarketing_id_prefix_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_dynamic_remarketing_id_prefix'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_dynamic_remarketing_status',
                    'name'              => 'dynamic_remarketing_id_prefix'
                ),

                array(
                    'label'             => $this->_lang('dynamic_remarketing_id_sufix'),
                    'help'              => $this->_lang('dynamic_remarketing_id_sufix_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_dynamic_remarketing_id_sufix'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_dynamic_remarketing_status',
                    'name'              => 'dynamic_remarketing_id_sufix'
                ),

                array(
                    'label'             => $this->_lang('dynamic_remarketing_dynx'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_dynamic_remarketing_dynx'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_dynamic_remarketing_status',
                    'name'              => 'dynamic_remarketing_dynx',
                ),

                // Google Reviews bagde
                array(
                    'label'             => '<img class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/dynamic_remarketing.jpg">'.$this->_lang('google_reviews_badge_code_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_google_reviews_badge_code_status'),
                    'name'              => 'google_reviews_badge_code_status',
                    'class_container'   => 'toogle_main_field',
                ),

                array(
                    'label'             => $this->_lang('google_reviews_badge_id'),
                    'help'              => $this->_lang('google_reviews_badge_id_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_google_reviews_badge_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_google_reviews_badge_code_status',
                    'name'              => 'google_reviews_badge_id'
                ),

                array(
                    'label'             => $this->_lang('google_reviews_badge_code_style'),
                    'type'              => 'select',
                    'options'           => array(
                        'BOTTOM_RIGHT'      => $this->_lang('google_reviews_badge_code_style_bottom_right'),
                        'BOTTOM_LEFT'       => $this->_lang('google_reviews_badge_code_style_bottom_left'),
                    ),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_google_reviews_badge_code_status',
                    'name'              => 'google_reviews_badge_code_style'
                ),
            
                // Rich Snippets
                array(
                    'label'             => '<img class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/rich_snippets.jpg">'.$this->_lang('rich_snippets_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_rich_snippets_status'),
                    'name'              => 'rich_snippets_status',
                ),

                // Facebook API Conversions
                array(
                    'label'             => '<img class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/facebook_pixel.jpg">'.$this->_lang('facebook_pixel_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_facebook_pixel_status'),
                    'class_container'   => 'toogle_main_field',
                    'name'              => 'facebook_pixel_status',
                ),
                array(
                    'label'             => $this->_lang('facebook_pixel_id'),
                    'help'              => $this->_lang('facebook_pixel_id_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_facebook_pixel_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_facebook_pixel_status',
                    'name'              => 'facebook_pixel_id'
                ),

                // TikTok Pixel
                array(
                    'label'             => '<img class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/tiktok_pixel.jpg">'.$this->_lang('tiktok_pixel_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_tiktok_pixel_status'),
                    'class_container'   => 'toogle_main_field',
                    'name'              => 'tiktok_pixel_status',
                ),

                array(
                    'label'             => $this->_lang('tiktok_pixel_id'),
                    'help'              => $this->_lang('tiktok_pixel_id_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_tiktok_pixel_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_tiktok_pixel_status',
                    'name'              => 'tiktok_pixel_id'
                ),

                // Hotjar
                array(
                    'label'             => '<img class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/hotjar.jpg">'.$this->_lang('hotjar_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_hotjar_status'),
                    'class_container'   => 'toogle_main_field',
                    'name'              => 'hotjar_status',
                ),

                array(
                    'label'             => $this->_lang('hotjar_site_id'),
                    'help'              => $this->_lang('hotjar_site_id_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_hotjar_site_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_hotjar_status',
                    'name'              => 'hotjar_site_id'
                ),

                // Pinterest Pixel
                array(
                    'label'             => '<img class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/pinterest.jpg">'.$this->_lang('pinterest_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_pinterest_status'),
                    'class_container'   => 'toogle_main_field',
                    'name'              => 'pinterest_status',
                ),

                array(
                    'label'             => $this->_lang('pinterest_id'),
                    'help'              => $this->_lang('pinterest_id_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_pinterest_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_pinterest_status',
                    'name'              => 'pinterest_id'
                ),

                // Crazyegg Tracking
                array(
                    'label'             => '<img class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/crazyegg.jpg">'.$this->_lang('crazyegg_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_crazyegg_status'),
                    'class_container'   => 'toogle_main_field',
                    'name'              => 'crazyegg_status',
                ),

                array(
                    'label'             => $this->_lang('crazyegg_id'),
                    'help'              => $this->_lang('crazyegg_id_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_crazyegg_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_crazyegg_status',
                    'name'              => 'crazyegg_id'
                ),

                // Criteo OneTag
                array(
                    'label'             => '<img class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/criteo.jpg">'.$this->_lang('criteo_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_criteo_status'),
                    'class_container'   => 'toogle_main_field',
                    'name'              => 'criteo_status',
                ),

                array(
                    'label'             => $this->_lang('criteo_id'),
                    'help'              => $this->_lang('criteo_id_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_criteo_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_criteo_status',
                    'name'              => 'criteo_id'
                ),

                // Bing Ads
                array(
                    'label'             => '<img class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/bing_ads.jpg">'.$this->_lang('bing_ads_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_bing_ads_status'),
                    'class_container'   => 'toogle_main_field',
                    'name'              => 'bing_ads_status',
                ),

                array(
                    'label'             => $this->_lang('bing_ads_tag_id'),
                    'help'              => $this->_lang('bing_ads_tag_id_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_bing_ads_tag_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_bing_ads_status',
                    'name'              => 'bing_ads_tag_id'
                ),

                // Dognet
                array(
                    'label'             => '<img class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/dognet.jpg">'.$this->_lang('dognet_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_dognet_status'),
                    'class_container'   => 'toogle_main_field',
                    'name'              => 'dognet_status',
                ),

                array(
                    'label'             => $this->_lang('dognet_account_id'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_dognet_account_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_dognet_status',
                    'name'              => 'dognet_account_id'
                ),

                array(
                    'label'             => $this->_lang('dognet_campaign_id'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_dognet_campaign_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_dognet_status',
                    'name'              => 'dognet_campaign_id'
                ),

                // Yahoo - Native & Search Dot
                array(
                    'label'             => '<img class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/yahoo.jpg">'.$this->_lang('yahoo_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('yahoo_status'),
                    'class_container'   => 'toogle_main_field',
                    'name'              => 'yahoo_status',
                ),

                array(
                    'label'             => $this->_lang('yahoo_project_id'),
                    'help'              => $this->_lang('yahoo_project_id_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_yahoo_project_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_yahoo_status',
                    'name'              => 'yahoo_project_id'
                ),

                array(
                    'label'             => $this->_lang('yahoo_pixel_id'),
                    'help'              => $this->_lang('yahoo_pixel_id_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_yahoo_pixel_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_yahoo_status',
                    'name'              => 'yahoo_pixel_id'
                ),

                // Domodi Pixel
                array(
                    'label'             => '<img style="width: 30px;" class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/domodi.png">'.$this->_lang('domodi_pixel_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_domodi_pixel_status'),
                    'class_container'   => 'toogle_main_field',
                    'name'              => 'domodi_pixel_status',
                ),

                array(
                    'label'             => $this->_lang('domodi_pixel_id'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_domodi_pixel_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_domodi_pixel_status',
                    'name'              => 'domodi_pixel_id'
                ),

                // Stileo Pixel 2.0
                array(
                    'label'             => '<img style="width: 30px;" class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/stileo.png">'.$this->_lang('stileo_pixel_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_stileo_pixel_status'),
                    'class_container'   => 'toogle_main_field',
                    'name'              => 'stileo_pixel_status',
                ),

                array(
                    'label'             => $this->_lang('stileo_pixel_id'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_stileo_pixel_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_stileo_pixel_status',
                    'name'              => 'stileo_pixel_id'
                ),

                // Skroutz Analytics
                array(
                    'label'             => '<img style="width: 30px;" class="tag_icon" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/skroutz_analytics.png">'.$this->_lang('skroutz_analytics_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_skroutz_analytics_status'),
                    'class_container'   => 'toogle_main_field',
                    'name'              => 'skroutz_analytics_status'
                ),

                array(
                    'label'             => $this->_lang('skroutz_analytics_shop_id'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_skroutz_analytics_shop_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_skroutz_analytics_status',
                    'name'              => 'skroutz_analytics_shop_id'
                ),
                
                // Klaviyo
                array(
                    'label'             => '<img class="tag_icon go" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/klaviyo.png">'.$this->_lang('klaviyo_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_klaviyo_status'),
                    'class_container'   => 'toogle_main_field',
                    'name'              => 'klaviyo_status',
                ),

                array(
                    'label'             => $this->_lang('klaviyo_api_key'),
                    'help'              => $this->_lang('klaviyo_api_key_help'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_klaviyo_api_key'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_klaviyo_status',
                    'name'              => 'klaviyo_api_key'
                ),

                // LinkedIn Insights
                array(
                    'label'             => '<img class="tag_icon go" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/linkedin.png">'.$this->_lang('linkedin_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_linkedin_status'),
                    'class_container'   => 'toogle_main_field',
                    'name'              => 'linkedin_status'
                ),  
                
                array(
                    'label'             => $this->_lang('linkedin_partner_id'),
                    'type'              => 'text',
                    'placeholder'       => '_linkedin_partner_id',
                    'value'             => $this->config->get('google_linkedin_partner_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_linkedin_status',
                    'name'              => 'linkedin_partner_id'
                ),

                array(
                    'label'             => $this->_lang('linkedin_purcharse_conversion_id'),
                    'type'              => 'text',
                    'placeholder'       => $this->_lang('linkedin_conversion_id_placeholder'),
                    'value'             => $this->config->get('google_linkedin_purcharse_conversion_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_linkedin_status',
                    'name'              => 'linkedin_purcharse_conversion_id'
                ),

                array(
                    'label'             => $this->_lang('linkedin_add_to_cart_conversion_id'),
                    'type'              => 'text',
                    'placeholder'       => $this->_lang('linkedin_conversion_id_placeholder'),
                    'value'             => $this->config->get('google_linkedin_add_to_cart_conversion_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_linkedin_status',
                    'name'              => 'linkedin_add_to_cart_conversion_id'
                ),

                array(
                    'label'             => $this->_lang('linkedin_wishlist_conversion_id'),
                    'type'              => 'text',
                    'placeholder'       => $this->_lang('linkedin_conversion_id_placeholder'),
                    'value'             => $this->config->get('google_linkedin_wishlist_conversion_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_linkedin_status',
                    'name'              => 'linkedin_wishlist_conversion_id'
                ),

                array(
                    'label'             => $this->_lang('linkedin_promotion_click_conversion_id'),
                    'type'              => 'text',
                    'placeholder'       => $this->_lang('linkedin_conversion_id_placeholder'),
                    'value'             => $this->config->get('google_linkedin_promotion_click_conversion_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_linkedin_status',
                    'name'              => 'linkedin_promotion_click_conversion_id'
                ),

                array(
                    'label'             => $this->_lang('linkedin_search_conversion_id'),
                    'type'              => 'text',
                    'placeholder'       => $this->_lang('linkedin_conversion_id_placeholder'),
                    'value'             => $this->config->get('google_linkedin_search_conversion_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_linkedin_status',
                    'name'              => 'linkedin_search_conversion_id'
                ),

                array(
                    'label'             => $this->_lang('linkedin_start_checkout_conversion_id'),
                    'type'              => 'text',
                    'placeholder'       => $this->_lang('linkedin_conversion_id_placeholder'),
                    'value'             => $this->config->get('google_linkedin_start_checkout_conversion_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_linkedin_status',
                    'name'              => 'linkedin_start_checkout_conversion_id'
                ),

                // Twitter Pixel
                array(
                    'label'             => '<img class="tag_icon go" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/twitter.png">'.$this->_lang('twitter_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_twitter_status'),
                    'class_container'   => 'toogle_main_field',
                    'name'              => 'twitter_status',
                ),

                array(
                    'label'             => $this->_lang('twitter_add_to_cart_event_id'),
                    'help_bottom'       => $this->_lang('twitter_add_to_cart_event_id_help'),
                    'type'              => 'text',
                    'placeholder'       => $this->_lang('twitter_event_id_placeholder'),
                    'value'             => $this->config->get('google_twitter_add_to_cart_event_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_twitter_status',
                    'name'              => 'twitter_add_to_cart_event_id'
                ),
                
                array(
                    'label'             => $this->_lang('twitter_wishlist_event_id'),
                    'help_bottom'       => $this->_lang('twitter_wishlist_event_id_help'),
                    'type'              => 'text',
                    'placeholder'       => $this->_lang('twitter_event_id_placeholder'),
                    'value'             => $this->config->get('google_twitter_wishlist_event_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_twitter_status',
                    'name'              => 'twitter_wishlist_event_id'
                ),
                
                array(
                    'label'             => $this->_lang('twitter_checkout_initiated_event_id'),
                    'help_bottom'       => $this->_lang('twitter_checkout_initiated_event_id_help'),
                    'type'              => 'text',
                    'placeholder'       => $this->_lang('twitter_event_id_placeholder'),
                    'value'             => $this->config->get('google_twitter_checkout_initiated_event_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_twitter_status',
                    'name'              => 'twitter_checkout_initiated_event_id'                    
                ),
                
                array(
                    'label'             => $this->_lang('twitter_content_view_event_id'),
                    'help_bottom'       => $this->_lang('twitter_content_view_event_id_help'),
                    'type'              => 'text',
                    'placeholder'       => $this->_lang('twitter_event_id_placeholder'),
                    'value'             => $this->config->get('google_twitter_content_view_event_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_twitter_status',
                    'name'              => 'twitter_content_view_event_id'
                ),
                
                array(
                    'label'             => $this->_lang('twitter_purcharse_event_id'),
                    'help_bottom'       => $this->_lang('twitter_purcharse_event_id_help'),
                    'type'              => 'text',
                    'placeholder'       => $this->_lang('twitter_event_id_placeholder'),
                    'value'             => $this->config->get('google_twitter_purcharse_event_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_twitter_status',
                    'name'              => 'twitter_purcharse_event_id'
                ),
                
                array(
                    'label'             => $this->_lang('twitter_search_event_id'),
                    'help_bottom'       => $this->_lang('twitter_search_event_id_help'),
                    'type'              => 'text',
                    'placeholder'       => $this->_lang('twitter_event_id_placeholder'),
                    'value'             => $this->config->get('google_twitter_search_event_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_twitter_status',
                    'name'              => 'twitter_search_event_id'
                ),  
                
                array(
                    'label'             => $this->_lang('twitter_pixel_id'),
                    'label'             => $this->_lang('twitter_pixel_id'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_twitter_pixel_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_twitter_status',
                    'name'              => 'twitter_pixel_id'
                ),                

                // Mailchimp
                array(
                    'label'             => '<img class="tag_icon go" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/mailchimp.png">'.$this->_lang('mailchimp_status'),
                    'type'              => 'boolean',
                    'value'             => $this->config->get('google_mailchimp_status'),
                    'class_container'   => 'toogle_main_field',
                    'name'              => 'mailchimp_status',
                ),

                array(
                    'label'             => $this->_lang('mailchimp_dc_or_server'),
                    'help'              => $this->_lang('mailchimp_dc_or_server_help'),
                    'type'              => 'text',
                    'placeholder'       => $this->_lang('mailchimp_dc_placeholder'),
                    'value'             => $this->config->get('google_mailchimp_dc_or_server'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_mailchimp_status',
                    'name'              => 'mailchimp_dc_or_server'
                ),

                array(
                    'label'             => $this->_lang('mailchimp_api_key'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_mailchimp_api_key'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_mailchimp_status',
                    'name'              => 'mailchimp_api_key'
                ),
                
                array(
                    'label'             => $this->_lang('mailchimp_list_id'),
                    'type'              => 'text',
                    'value'             => $this->config->get('google_mailchimp_list_id'),
                    'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_mailchimp_status',
                    'name'              => 'mailchimp_list_id'
                ),
                
                // Generate Workspace
                array(
                    'type'              => 'button',
                    'label'             => $this->_lang('generate_workspace'),
                    'text'              => '<i class="fa fa-floppy-o"></i> '.$this->_lang('generate_workspace'),
                    'onclick'           => 'generate_workspace();'
                ),                      
            );

            return $fields;
        }

        public function _send_custom_variables_to_view($variables) {
            $variables['link_get_workspace'] = $this->api_url.'gmt/get_workspace';
            return $variables;
        }

        public function _check_ajax_function($function_name) {
            if($function_name == '') {
                
            }
        }
    }
?>