<?php
    class ModelExtensionModuleGoogleAllTabGeneral extends ModelExtensionModuleGoogleAll
    {
        private $extension_version;

        public function __construct($registry) {
            parent::__construct($registry);
            $this->load->language($this->real_extension_type.'/google_all_tab_general');

            $this->extension_version = $registry->has('gmt_extension_version') ? $this->gmt_extension_version : date('Ymdhis');

            $this->load->model('localisation/order_status');
            $order_statuses = array();
            $order_statuses_temp = $this->model_localisation_order_status->getOrderStatuses();
            foreach ($order_statuses_temp as $key => $ord) {
                $order_statuses[$ord['order_status_id']] = $ord['name'];
            }
            $this->order_statuses = $order_statuses;

            $this->product_identificators = array(
                'product_id' => 'Product ID',
                'model' => 'Model',
                'sku' => 'SKU',
                'upc' => 'UPC',
                'ean' => 'EAN',
                'jan' => 'JAN',
                'isbn' => 'ISBN',
                'mpn' => 'MPN'
            );
        }

        public function get_fields() {
            $this->document->addStyle($this->api_url.'opencart_admin/google_marketing_tools/css/tab_general.css?v='.$this->extension_version);
            $this->document->addScript($this->api_url.'opencart_admin/google_marketing_tools/js/tab_general.js?v='.$this->extension_version);

            $fields = array(
                array(
                    'text' => '<img class="tag_icon_legend" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/gtm.png">'.$this->_lang('legend_general'),
                    'type' => 'legend',
                ),

                array(
                    'label' => $this->_lang('status'),
                    'type' => 'boolean',
                    'help_bottom' => $this->_lang('help'),
                    'name' => 'tag_manager_status',
                    'class_container' => 'toogle_main_field',
                    'after' => '<input type="hidden" name="google_all_google_version" value="'.$this->_lang('extension_version').'">',
                ),

                array(
                    'label' => $this->_lang('container_id'),
                    'help' => $this->_lang('container_id_help'),
                    'type' => 'text',
                    'class_container' => 'toogle_seconday_field '.$this->extension_group_config.'_tag_manager_status',
                    'name' => 'container_id',
                ),

                array(
                    'text' => '<img class="tag_icon_legend" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/gaee.png">'.$this->_lang('google_analytics_ee_legend'),
                    'type' => 'legend',
                ),

                array(
                    'label' => $this->_lang('google_ee_product_id_like'),
                    'help' => $this->_lang('google_ee_product_id_like_help'),
                    'type' => 'select',
                    'options' => $this->product_identificators,
                    'name' => 'google_ee_product_id_like',
                ),

                array(
                    'text' => '<img class="tag_icon_legend" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/gads.png">'.$this->_lang('google_ads_legend'),
                    'type' => 'legend',
                ),

                array(
                    'label' => $this->_lang('google_product_id_like'),
                    'help' => $this->_lang('google_product_id_like_help'),
                    'type' => 'select',
                    'options' => $this->product_identificators,
                    'name' => 'google_product_id_like',
                ),

                array(
                    'label' => $this->_lang('dynamic_remarketing_dynx2'),
                    'type' => 'select',
                    'options' => array(
                        'product_id' => $this->_lang('dynamic_remarketing_dynx2_product_id'),
                        'model' => $this->_lang('dynamic_remarketing_dynx2_model'),
                        'sku' => $this->_lang('dynamic_remarketing_dynx2_sku'),
                        'upc' => $this->_lang('dynamic_remarketing_dynx2_upc'),
                        'ean' => $this->_lang('dynamic_remarketing_dynx2_ean'),
                        'jan' => $this->_lang('dynamic_remarketing_dynx2_jan'),
                        'isbn' => $this->_lang('dynamic_remarketing_dynx2_isbn'),
                        'mpn' => $this->_lang('dynamic_remarketing_dynx2_mpn'),
                        'location' => $this->_lang('dynamic_remarketing_dynx2_location'),
                    ),
                    'value' => $this->config->get('google_dynamic_remarketing_dynx2'),
                    'name' => 'dynamic_remarketing_dynx2',
                ),

                array(
                    'text' => '<img class="tag_icon_legend" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/facebook_pixel.jpg">'.$this->_lang('fb_pixel_legend'),
                    'type' => 'legend',
                ),

                array(
                    'label' => $this->_lang('google_product_id_like'),
                    'help' => $this->_lang('google_product_id_like_help'),
                    'type' => 'select',
                    'options' => $this->product_identificators,
                    'name' => 'fb_pixel_id_like',
                ),

                // Google Reviews - Configuration 
                array(
                    'text' => '<img class="tag_icon_legend" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/greviews.png">'.$this->_lang('google_reviews_legend'),
                    'type' => 'legend',
                ),
                array(
                    'label' => $this->_lang('google_reviews_gtin'),
                    'help' => $this->_lang('google_reviews_gtin_help'),
                    'type' => 'select',
                    'options' => array(
                        'product_id'    => $this->_lang('google_reviews_gtin_product_id'),
                        'mpn'           => $this->_lang('google_reviews_gtin_mpn'),
                        'model'         => $this->_lang('google_reviews_gtin_model'),
                        'ean'           => $this->_lang('google_reviews_gtin_ean'),
                        'upc'           => $this->_lang('google_reviews_gtin_upc'),
                    ),
                    'name' => 'google_reviews_gtin'
                ),

                array(
                    'text' => '<img class="tag_icon_legend" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/2performant.png">'.$this->_lang('2performant_legend'),
                    'type' => 'legend',
                ),

                array(
                    'label' => $this->_lang('2performant_status'),
                    'type' => 'boolean',
                    'class' => 'container_config 2performant_status',
                    'name' => '2performant_status',
                    'class_container' => 'toogle_main_field',
                ),

                array(
                    'label' => $this->_lang('2performant_campaign_id'),
                    'help' => $this->_lang('2performant_campaign_id_help'),
                    'type' => 'text',
                    'class_container' => 'toogle_seconday_field '.$this->extension_group_config.'_2performant_status',
                    'name' => '2performant_campaign_id'
                ),

                array(
                    'label' => $this->_lang('2performant_confirm_id'),
                    'help' => $this->_lang('2performant_confirm_id_help'),
                    'type' => 'text',
                    'class_container' => 'toogle_seconday_field '.$this->extension_group_config.'_2performant_status',
                    'name' => '2performant_confirm_id'
                ),

                array(
                    'text' => '<img class="tag_icon_legend" src="'.$this->api_url.'opencart_admin/google_marketing_tools/img/icons/domodi.png">'.$this->_lang('domodi_legend'),
                    'type' => 'legend',
                ),

                array(
                    'label' => $this->_lang('domodi_size_options'),
                    'help' => $this->_lang('domodi_size_options_help'),
                    'type' => 'select',
                    'multiple' => true,
                    'class' => 'container_config domodi_size_options',
                    'options' => $this->model_extension_devmanextensions_tools->get_options_select_format(),
                    'name' => 'domodi_size_options',
                ),

                array(
                    'text' => '<img class="tag_icon_legend" src="'.$this->api_url.'opencart_admin/common/img/devman_only_face.png">'.$this->_lang('another_configuration_legend'),
                    'type' => 'legend',
                ),

                array(
                    'label' => $this->_lang('debug_mode'),
                    'help' => sprintf($this->_lang('debug_mode_help'), 'google_marketing_tools.log'),
                    'type' => 'boolean',
                    'name' => 'debug_mode',
                ),

                array(
                    'label' => $this->_lang('cache_compatibility'),
                    'help'  => $this->_lang('cache_compatibility_help'),
                    'type'  => 'boolean',
                    'name'  => 'cache_compatibility', 
                ),

                array(
                    'label' => $this->_lang('positive_conversion_status_id'),
                    'help_bottom' => $this->_lang('positive_conversion_status_id_help'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->order_statuses,
                    'name' => 'positive_conversion_status_id'
                ),

                array(
                    'label' => $this->_lang('custom_url_checkout_cart'),
                    'help_bottom' => $this->_lang('custom_url_checkout_cart_help'),
                    'type' => 'text',
                    'name' => 'custom_url_checkout_cart',
                ),

                array(
                    'label' => $this->_lang('custom_url_checkout_checkout'),
                    'help_bottom' => $this->_lang('custom_url_checkout_checkout_help'),
                    'type' => 'text',
                    'name' => 'custom_url_checkout_checkout',
                ),

                array(
                    'label' => $this->_lang('custom_url_checkout_success'),
                    'help_bottom' => $this->_lang('custom_url_checkout_success_help'),
                    'type' => 'text',
                    'name' => 'custom_url_checkout_success',
                )
            );

            return $fields;
        }

        public function _send_custom_variables_to_view($variables) {
            return $variables;
        }

        public function _check_ajax_function($function_name) {
            if($function_name == 'xxx') {
                
            }
        }

        public function generate_gtm_head_code($gtm) {
            return "<!-- Google Tag Manager -->
            <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','$gtm');</script>
            <!-- End Google Tag Manager -->";
        }

        public function generate_gtm_body_code($gtm) {
            return "<!-- Google Tag Manager (noscript) -->
            <noscript><iframe src=\"https://www.googletagmanager.com/ns.html?id=$gtm\"
            height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
            <!-- End Google Tag Manager (noscript) -->";
        }
        
        public function _get_product_manufacturer($product_id) {
            $sql = "SELECT ma.name FROM " . DB_PREFIX . "product pr LEFT JOIN " . DB_PREFIX . "manufacturer ma ON (pr.manufacturer_id = ma.manufacturer_id) WHERE pr.product_id = ".$product_id;
            $results = $this->db->query($sql);

            return !empty($results->row['name']) ? $results->row['name'] : '';
        }

        public function _get_product_category_name($product_id) {
            $this->load->model('catalog/product');
            $product_categories = $this->model_catalog_product->getProductCategories($product_id);
            $this->load->model('catalog/category');

            if(!empty($product_categories[0]['category_id']))
                $category_info = $this->model_catalog_category->getCategory($product_categories[0]['category_id']);

            return !empty($category_info['name']) ? str_replace("'", "\'", $category_info['name']) : '';
        }
        public function _get_product_variant_order_success($options){
            foreach ($options as $key => $opt) {
                return $opt['name'].(!empty($opt['value']) ? ': '.$opt['value'] : '');
            }
        }

        public function _format_price($price, $currency_code, $thousands = false)
        {
            $price = $this->currency->format($price, $currency_code, '', false);

            $decimal_separator = !$thousands ? '.' : ',';
            $thousands_separator = !$thousands ? '' : '.';
            return number_format((float)$price, 2, $decimal_separator, $thousands_separator);
        }

        public function get_order_data($order_id) {
            $this->load->model('sale/order');
            $order_data = $this->model_sale_order->getOrder(trim($order_id));

            if(empty($order_data))
                return '';

            $order_totals = $this->model_sale_order->getOrderTotals($order_id);
            $order_products = $this->model_sale_order->getOrderProducts($order_id);
            $order_data['totals'] = $order_totals;
            $order_data['products'] = $order_products;

            return $order_data;
        }

    }
?>