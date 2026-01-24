<?php
    class ModelExtensionModuleGoogleAllTabFeed extends ModelExtensionModuleGoogleAll 
    {   
        private $extension_version;
        
        public function __construct($registry) {
            parent::__construct($registry);
            $this->load->language($this->real_extension_type.'/google_all_tab_feed');

            $this->extension_version = $registry->has('gmt_extension_version') ? $this->gmt_extension_version : date('Ymdhis');

            $this->ie_categories = array(
                '' => $this->_lang('select_empty'),
                'products' => $this->_lang('feed_i_want_products'),
                'reviews' => $this->_lang('feed_i_want_reviews'),
            );

            $this->filter_conditionals_number = array(
                '>=' => '≥',
                '<=' => '≤',
                '>' => '&gt;',
                '<' => '&lt;',
                '=' => '=',
                '!=' => '≠',
            );

            $this->filter_conditionals_string = array(
                'like' => $this->_lang('feed_filters_conditional_contain'),
                'not_like' => $this->_lang('feed_filters_conditional_not_contain'),
                '=' => $this->_lang('feed_filters_conditional_is_exactly'),
                '!=' => $this->_lang('feed_filters_conditional_is_not_exactly'),
            );

            $this->filter_conditionals_date = array(
                '>=' => '≥',
                '<=' => '≤',
                '>' => '&gt;',
                '<' => '&lt;',
                '=' => '=',
                '!=' => '≠',
                'like' => $this->_lang('feed_filters_conditional_contain'),
                'not_like' => $this->_lang('feed_filters_conditional_not_contain'),
                'years_ago' => $this->_lang('feed_filters_conditional_years_ago'),
                'months_ago' => $this->_lang('feed_filters_conditional_months_ago'),
                'days_ago' => $this->_lang('feed_filters_conditional_days_ago'),
                'hours_ago' => $this->_lang('feed_filters_conditional_hours_ago'),
                'minutes_ago' => $this->_lang('feed_filters_conditional_minutes_ago'),
            );

            $this->filter_conditionals_boolean = array(
                '1' => $this->_lang('feed_filters_conditional_is_yes'),
                '0' => $this->_lang('feed_filters_conditional_is_no'),
            );

            $this->filter_field_types = array(
                'number',
                'string',
                'date',
                'boolean',
            );

            $this->conditionals = array(
                'AND' => $this->_lang('feed_filters_main_conditional_and'),
                'OR' => $this->_lang('feed_filters_main_conditional_or'),
            );

            $this->pickup_method_options = array(
                '' => ' - None - ',
                'buy' => 'buy',
                'reserve' => 'reserve',
                'ship to store' => 'ship to store',
                'not supported' => 'not supported',
            );

            $this->pickup_sla_options = array(
                '' => ' - None - ',
                'same day' => 'same day',
                'next day' => 'next day',
                '2-day' => '2-day',
                '3-day' => '3-day',
                '4-day' => '4-day',
                '5-day' => '5-day',
                '6-day' => '6-day',
                '7-day' => '7-day',
                'multi-week' => 'multi-week',
            );

            $this->product_identificators = array(
                'product_id' => $this->_lang('feed_product_identificator_product_id'),
                'model' => $this->_lang('feed_product_identificator_model'),
                'sku' => $this->_lang('feed_product_identificator_sku'),
                'upc' => $this->_lang('feed_product_identificator_upc'),
                'ean' => $this->_lang('feed_product_identificator_ean'),
                'jan' => $this->_lang('feed_product_identificator_jan'),
                'isbn' => $this->_lang('feed_product_identificator_isbn'),
                'mpn' => $this->_lang('feed_product_identificator_mpn'),
            );

            $this->custom_label_get_value_from_select = [
                ''                              => $this->_lang('text_none'),
                'main_product_category'         => $this->_lang('feed_exclusive_merchant_center_custom_label_get_value_from_opt_1'),
                'last_product_category'         => $this->_lang('feed_exclusive_merchant_center_custom_label_get_value_from_opt_2'),
                'product_price_raw_format'      => $this->_lang('feed_exclusive_merchant_center_custom_label_get_value_from_opt_3'),
                'stock_value_identification'    => $this->_lang('feed_exclusive_merchant_center_custom_label_get_value_from_opt_4')
            ];

            $this->languages_select = $this->model_extension_devmanextensions_tools->get_languages_select_format();
            $this->currencies_select = $this->model_extension_devmanextensions_tools->get_currencies_select_format();
            $this->customer_groups_select = $this->model_extension_devmanextensions_tools->get_customer_group_select_format();
            $this->stores_select = $this->model_extension_devmanextensions_tools->get_stores_select_format();
            $this->attributes_select = $this->model_extension_devmanextensions_tools->get_attributes_select_format();
            $this->filters_select = $this->model_extension_devmanextensions_tools->get_filters_select_format();
            $this->options_select = $this->model_extension_devmanextensions_tools->get_options_select_format();
            $this->profiles_select = $this->{$this->model_feed}->get_profiles('', '', true);
            $this->stock_statuses_select = $this->model_extension_devmanextensions_tools->getStockStatusesSelect();
            $tax_classes_select = array();
            $tax_classes_select[] = $this->_lang('feed_tax_to_apply_default');
            $taxes = $this->model_extension_devmanextensions_tools->getTaxClassesSelect(false);
            foreach ($taxes as $key => $val) {
                $tax_classes_select[$key] = $val;
            }
            $this->tax_classes_select = $tax_classes_select;
        }

        public function get_fields() {
            $this->document->addScript($this->api_url.'opencart_admin/google_marketing_tools/js/tab_feed.js?v='.$this->extension_version);
            $this->document->addStyle($this->api_url.'opencart_admin/google_marketing_tools/css/tab_feed.css?v='.$this->extension_version);

            $categories_select_format = $this->get_all_categories_branches_select();
            $manufacturers_select_format = $this->get_all_manufacturers_import_format(true);

            $dir_catalog = str_replace('/catalog', '', DIR_CATALOG);
            $catalog_folder_split = explode('/', $dir_catalog);
            $folders = array();
            foreach ($catalog_folder_split as $key => $folder) {
                if($folder != '')
                    $folders[] = $folder;

                if(count($folders) == 2)
                    break;
            }
            $final_out_folder = '/'.implode('/', $folders).'/';

            $fields = array(
                array(
                    'type' => 'html_hard',
                    'html_code' => '<div class="alert alert-info" style="margin-bottom: 15px;">'.$this->_lang('feed_google_taxonomy').'</div>',
                ),
                array(
                    'type' => 'html_hard',
                    'html_code' => '<div class="alert alert-info" style="margin-bottom: 0px;">'.$this->_lang('feed_feeds_url').'</div>'.$this->model_extension_devmanextensions_tools->get_remodal('feed_feed_urls', $this->language->get('feed_feed_urls_remodal_title'), $this->get_feed_urls_remodal_content(), array('button_cancel' => false, 'button_confirm' => false, '')),
                ),
                array(
                    'type' => 'legend',
                    'text' => '<i class="fa fa-rss"></i>'.$this->_lang('feed_legend_text'),
                    'remove_border_button' => true,
                ),
                array(
                    'label' => $this->_lang('feed_select_text'),
                    'type' => 'select',
                    'options' => $this->profiles_select,
                    'name' => 'feed_config_profiles',
                    'onchange' => 'feed_load($(this))',
                    'columns' => 3
                ),

                array(
                    'type' => 'html_code',
                    'html_code' => '- '.$this->_lang('feed_or').' -',
                    'columns' => 3,
                    'class_container' => 'or',
                ),

                array(
                    'type' => 'html_code',
                    'label' => false,
                    'html_code' => '
                        <a class="feed_create_link" href="javascript:{}" onclick="feed_create()"><i class="fa fa-rss"></i>'.$this->_lang('feed_create_export_text').'</a><input type="hidden" name="feed_id" value="">
                    ',
                    'columns' => 3
                ),

                array(
                    'type' => 'legend',
                    'text' => '<i class="fa fa-download"></i>'.$this->_lang('feed_legend_text_export'),
                    'remove_border_button' => true,
                    'class_container' => 'feed_configuration_value',
                    'style' => 'margin-top: 17px;'
                ),

                array(
                    'label' => $this->_lang('feed_file_destiny'),
                    'type' => 'select',
                    'options' => array(
                        'download' => $this->_lang('feed_file_download'),
                        'server' => $this->_lang('feed_file_destiny_server'),
                        'external_server' => $this->_lang('feed_file_destiny_external_server'),
                    ),
                    'name' => 'feed_config_file_destiny',
                    'class_container' => 'feed_configuration_value file_destiny configuration',
                    'onchange' => 'check_destiny($(this).val())'
                ),

                array(
                    'label' => $this->_lang('feed_file_destiny_server_path'),
                    'type' => 'text',
                    'name' => 'feed_config_file_destiny_server_path',
                    'class_container' => 'feed_configuration_value server configuration',
                    'after' => '<a href="javascript:{}" data-remodal-target="feed_export_file_destiny_server_path">'.$this->_lang('feed_file_destiny_server_path_remodal_link').'</a>'.$this->model_extension_devmanextensions_tools->get_remodal('feed_export_file_destiny_server_path', $this->_lang('feed_file_destiny_server_path_remodal_title'), sprintf($this->_lang('feed_file_destiny_server_path_remodal_description'), $dir_catalog,$dir_catalog,$dir_catalog,$dir_catalog,$final_out_folder), array('button_cancel' => false, 'button_confirm' => false, '')),
                ),

                array(
                    'label' => $this->_lang('feed_file_destiny_server_file_name'),
                    'help' => $this->_lang('feed_file_destiny_server_file_name_help'),
                    'type' => 'text',
                    'name' => 'feed_config_file_destiny_server_file_name',
                    'class_container' => 'feed_configuration_value server configuration',
                ),

                array(
                    'label' => $this->_lang('feed_file_destiny_server_file_name_sufix'),
                    'type' => 'select',
                    'options' => array(
                        '' => $this->_lang('feed_file_destiny_server_file_name_sufix_none'),
                        'date' => $this->_lang('feed_file_destiny_server_file_name_sufix_date'),
                        'datetime' => $this->_lang('feed_file_destiny_server_file_name_sufix_datetime'),
                    ),
                    'name' => 'feed_config_file_destiny_server_file_name_sufix',
                    'class_container' => 'feed_configuration_value server configuration',
                ),


                array(
                    'label' => $this->_lang('feed_ftp_passive_mode'),
                    'help' => $this->_lang('feed_ftp_passive_mode_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_ftp_passive_mode',
                    'class_container' => 'feed_configuration_value ftp configuration'
                ),

                array(
                    'label' => $this->_lang('feed_ftp_host'),
                    'type' => 'text',
                    'name' => 'feed_config_ftp_host',
                    'class_container' => 'feed_configuration_value ftp configuration'
                ),

                array(
                    'label' => $this->_lang('feed_ftp_username'),
                    'type' => 'text',
                    'name' => 'feed_config_ftp_username',
                    'class_container' => 'feed_configuration_value ftp configuration'
                ),

                array(
                    'label' => $this->_lang('feed_ftp_password'),
                    'type' => 'text',
                    'name' => 'feed_config_ftp_password',
                    'class_container' => 'feed_configuration_value ftp configuration'
                ),

                array(
                    'label' => $this->_lang('feed_ftp_port'),
                    'help' => $this->_lang('feed_ftp_port_help'),
                    'type' => 'text',
                    'name' => 'feed_config_ftp_port',
                    'class_container' => 'feed_configuration_value ftp configuration',
                    'default' => '21'
                ),

                array(
                    'label' => $this->_lang('feed_ftp_path'),
                    'help' => $this->_lang('feed_ftp_path_help'),
                    'type' => 'text',
                    'name' => 'feed_config_ftp_path',
                    'class_container' => 'feed_configuration_value ftp configuration',
                ),

                array(
                    'label' => $this->_lang('feed_ftp_file'),
                    'help' => $this->_lang('feed_ftp_file_help'),
                    'type' => 'text',
                    'name' => 'feed_config_ftp_file',
                    'class_container' => 'feed_configuration_value ftp configuration',
                ),

                array(
                    'type' => 'legend',
                    'text' => '<i class="fa fa-rss"></i>'.$this->_lang('feed_type_legend'),
                    'remove_border_button' => true,
                    'class_container' => 'feed_configuration_value',
                ),

                array(
                    'label' => $this->_lang('feed_type'),
                    'type' => 'select',
                    'options' => array(
                        '' => '',
                        'google_merchant' => $this->_lang('feed_type_google_merchant'),
                        'google_merchant_local' => $this->_lang('feed_type_google_merchant_local'),
                        'google_adwords' => $this->_lang('feed_type_google_adwords'),
                        'google_reviews' => $this->_lang('feed_type_google_reviews'),
                        'facebook' => $this->_lang('feed_type_facebook'),
                        'twenga' => $this->_lang('feed_type_twenga'),
                        'bestprice' => $this->_lang('feed_type_bestprice'),
                        'criteo' => $this->_lang('feed_type_criteo'),
                        'bing' => $this->_lang('feed_type_bing'),
                        'glami_ro' => $this->_lang('feed_type_glami_ro'),
                        'google_promotion' => $this->_lang('feed_type_google_promotion'),
                        'kelkoo' => $this->_lang('feed_type_kelkoo'),
                        //'shareasale' => $this->_lang('feed_type_shareasale'),
                        'shopalike' => $this->_lang('feed_type_shopalike'),
                        'shopmania' => $this->_lang('feed_type_shopmania'),
                        'wish' => $this->_lang('feed_type_wish'),
                        'geizhals' => $this->_lang('feed_type_geizhals'),
                        //'ceneo' => $this->_lang('feed_type_ceneo'),
                        'heureka' => $this->_lang('feed_type_heureka'),
                        'nokaut' => $this->_lang('feed_type_nokaut'),
                        //'okazje_info' => $this->_lang('feed_type_okazje_info'),
                        'skroutz' => $this->_lang('feed_type_skroutz'),
                        //'itscope' => $this->_lang('feed_type_itscope'),
                        'trovaprezzi_it' => $this->_lang('feed_type_trovaprezzi_it'),
                        'compari_ro' => $this->_lang('feed_type_compari_ro'),
                        'ceneje' => $this->_lang('feed_type_ceneje'),
                        'domodi' => $this->_lang('feed_type_domodi'),
                        //'akakce' => $this->_lang('feed_type_akakce'),
                        //'hepsiburada' => $this->_lang('feed_type_hepsiburada'),
                    ),
                    'name' => 'feed_config_file_format',
                    'class_container' => 'feed_configuration_value',
                    'onchange' => 'check_feed_type($(this).val())',
                ),

                array(
                    'type' => 'legend',
                    'text' => '<i class="fa fa-cog"></i>'.$this->_lang('feed_general_configuration'),
                    'remove_border_button' => true,
                    'class_container' => 'feed_configuration_product also_in_google_reviews',
                ),

                array(
                    'label' => $this->_lang('feed_language'),
                    'type' => 'select',
                    'options' => $this->languages_select,
                    'name' => 'feed_config_language_id',
                    'class_container' => 'feed_configuration_product also_in_google_reviews',
                    'columns' => 2
                ),

                array(
                    'label' => $this->_lang('feed_currency'),
                    'type' => 'select',
                    'options' => $this->currencies_select,
                    'name' => 'feed_config_currency_code',
                    'class_container' => 'feed_configuration_product',
                    'columns' => 2
                ),

                array(
                    'label' => $this->_lang('feed_customer_group_special'),
                    'help' => $this->_lang('feed_customer_group_special_help'),
                    'type' => 'select',
                    'options' => $this->customer_groups_select,
                    'name' => 'feed_config_customer_group_id_special',
                    'class_container' => 'feed_configuration_product',
                    'columns' => 2
                ),

                array(
                    'label' => $this->_lang('feed_customer_group_discount'),
                    'help' => $this->_lang('feed_customer_group_discount_help'),
                    'type' => 'select',
                    'options' => $this->customer_groups_select,
                    'name' => 'feed_config_customer_group_id_discount',
                    'class_container' => 'feed_configuration_product',
                    'columns' => 2
                ),

                array(
                    'label' => $this->_lang('feed_store'),
                    'type' => 'select',
                    'options' => $this->stores_select,
                    'name' => 'feed_config_store_id',
                    'class_container' => 'feed_configuration_product also_in_google_reviews',
                    'columns' => 2
                ),

                array(
                    'label' => $this->_lang('feed_product_identificator'),
                    'type' => 'select',
                    'options' => $this->product_identificators,
                    'name' => 'feed_config_product_identificator',
                    'class_container' => 'feed_configuration_product',
                    'columns' => 2
                ),

                array(
                    'label' => $this->_lang('feed_thumb_width'),
                    'help' => $this->_lang('feed_thumb_width_help'),
                    'type' => 'text',
                    'default' => 600,
                    'name' => 'feed_config_thumb_width',
                    'class_container' => 'feed_configuration_product exclude_in_google_merchant_local',
                    'columns' => 2
                ),

                array(
                    'label' => $this->_lang('feed_thumb_height'),
                    'help' => $this->_lang('feed_thumb_height_help'),
                    'type' => 'text',
                    'default' => 600,
                    'name' => 'feed_config_thumb_height',
                    'class_container' => 'feed_configuration_product exclude_in_google_merchant_local',
                    'columns' => 2
                ),

                array(
                    'label' => $this->_lang('feed_price_tax'),
                    'type' => 'boolean',
                    'name' => 'feed_config_price_tax',
                    'class_container' => 'feed_configuration_product',
                    'columns' => 4
                ),

                array(
                    'label' => $this->_lang('feed_tax_to_apply'),
                    'type' => 'select',
                    'name' => 'feed_config_tax_to_apply',
                    'options' => $this->tax_classes_select,
                    'class_container' => 'feed_configuration_product',
                    'columns' => 4
                ),

                array(
                    'label' => $this->_lang('feed_show_products_out_of_stock'),
                    'help' => $this->_lang('feed_show_products_out_of_stock_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_show_products_out_of_stock',
                    'class_container' => 'feed_configuration_product',
                    'columns' => 4
                ),

                array(
                    'label' => $this->_lang('feed_config_product_link_purchase'),
                    'help' => $this->_lang('feed_config_product_link_purchase_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_product_link_purchase',
                    'class_container' => 'feed_configuration_product',
                    'columns' => 4
                ),

                array(
                    'label' => $this->_lang('feed_split_options'),
                    'type' => 'boolean',
                    'name' => 'feed_config_split_options',
                    'class_container' => 'feed_configuration_product toogle_main_field',
                    'after' => '<br><br>'.$this->model_extension_devmanextensions_tools->get_remodal('split_options', $this->_lang('profile_import_split_options_remodal_title'), $this->_lang('profile_import_split_options_remodal_description'), array('button_cancel' => false, 'link' => 'profile_import_split_options_link_title')),
                ),

                array(
                    'label' => $this->_lang('feed_split_options_mask'),
                    'help' => $this->_lang('feed_split_options_mask_help'),
                    'placeholder' => '{product_name} - {size}, {color}',
                    'type' => 'text',
                    'name' => 'feed_config_split_options_mask',
                    'class_container' => 'feed_configuration_product toogle_seconday_field '.$this->extension_group_config.'_feed_config_split_options',
                ),

                array(
                    'label' => $this->_lang('feed_split_options_size'),
                    'type' => 'select',
                    'options' => $this->options_select,
                    'name' => 'feed_config_split_options_size',
                    'multiple' => true,
                    'class_container' => 'feed_configuration_product toogle_seconday_field '.$this->extension_group_config.'_feed_config_split_options',
                ),

                array(
                    'label' => $this->_lang('feed_split_options_color'),
                    'type' => 'select',
                    'options' => $this->options_select,
                    'name' => 'feed_config_split_options_color',
                    'multiple' => true,
                    'class_container' => 'feed_configuration_product toogle_seconday_field '.$this->extension_group_config.'_feed_config_split_options',
                ),

                array(
                    'label' => $this->_lang('feed_split_discounts'),
                    'help' => $this->_lang('feed_split_discounts_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_split_discounts',
                    'class_container' => 'feed_configuration_product toogle_main_field',
                ),

                array(
                    'label' => $this->_lang('feed_split_discounts_mask'),
                    'help' => $this->_lang('feed_split_discounts_mask_help'),
                    'placeholder' => '{product_name} x{discount_number}',
                    'type' => 'text',
                    'name' => 'feed_config_split_discounts_mask',
                    'class_container' => 'feed_configuration_product toogle_seconday_field '.$this->extension_group_config.'_feed_config_split_discounts',
                ),

                array(
                    'type' => 'html_hard',
                    'html_code' => '<div style="clear:both;"></div>'
                ),


            //-----------------------Bestprice--------------------------
                array(
                    'type' => 'legend',
                    'text' => '<i class="fa fa-rss"></i>' . $this->_lang('feed_exclusive_bestprice_legend'),
                    'remove_border_button' => true,
                    'class_container' => 'feed_configuration_exclusive bestprice',
                    'style' => 'margin-top: 17px;'
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bestprice_availability_in_stock'),
                    'type' => 'text',
                    'name' => 'feed_config_exclusive_bestprice_availability_in_stock',
                    'class_container' => 'feed_configuration_exclusive bestprice',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bestprice_availability_out_of_stock'),
                    'type' => 'text',
                    'name' => 'feed_config_exclusive_bestprice_availability_out_of_stock',
                    'class_container' => 'feed_configuration_exclusive bestprice',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bestprice_shipping'),
                    'type' => 'text',
                    'name' => 'feed_config_exclusive_bestprice_shipping',
                    'class_container' => 'feed_configuration_exclusive bestprice',
                ),

            //-----------------------Bestprice End--------------------------


            //-----------------------Kelkoo--------------------------
            array(
                'type' => 'legend',
                'text' => '<i class="fa fa-rss"></i>' . $this->_lang('feed_exclusive_kelkoo_legend'),
                'remove_border_button' => true,
                'class_container' => 'feed_configuration_exclusive kelkoo',
                'style' => 'margin-top: 17px;'
            ),

            array(
                'label' => $this->_lang('feed_exclusive_delivery_cost_default'),
                'type' => 'text',
                'name' => 'feed_config_exclusive_kelkoo_shipping_cost_default',
                'class_container' => 'feed_configuration_exclusive kelkoo',
            ),

            array(
                'label' => $this->_lang('feed_exclusive_delivery_time_default'),
                'type' => 'text',
                'name' => 'feed_config_exclusive_kelkoo_shipping_time_default',
                'class_container' => 'feed_configuration_exclusive kelkoo',
            ),

            //-----------------------Kelkoo End--------------------------

            //-----------------------Kelkoo--------------------------
            array(
                'type' => 'legend',
                'text' => '<i class="fa fa-rss"></i>' . $this->_lang('feed_exclusive_compari_ro_legend'),
                'remove_border_button' => true,
                'class_container' => 'feed_configuration_exclusive compari_ro',
                'style' => 'margin-top: 17px;'
            ),

            array(
                'label' => $this->_lang('feed_exclusive_delivery_cost_default'),
                'type' => 'text',
                'name' => 'feed_config_exclusive_compari_ro_shipping_cost_default',
                'class_container' => 'feed_configuration_exclusive compari_ro',
            ),

            array(
                'label' => $this->_lang('feed_exclusive_delivery_time_default'),
                'type' => 'text',
                'name' => 'feed_config_exclusive_compari_ro_shipping_time_default',
                'class_container' => 'feed_configuration_exclusive compari_ro',
            ),

            //-----------------------Kelkoo End--------------------------


            //-----------------------Trovaprezzi-------------------------

            array(
                'type' => 'legend',
                'text' => '<i class="fa fa-rss"></i>' . $this->_lang('feed_exclusive_trovapretzzi_legend'),
                'remove_border_button' => true,
                'class_container' => 'feed_configuration_exclusive trovaprezzi_it',
                'style' => 'margin-top: 17px;'
            ),

            array(
                'label' => $this->_lang('feed_exclusive_trovapretzzi_shipping_cost_default'),
                'type' => 'text',
                'name' => 'feed_config_exclusive_trovapretzzi_shipping_cost_default',
                'class_container' => 'feed_configuration_exclusive trovaprezzi_it',
            ),

            //-----------------------Trovaprezzi End-------------------------

            //-----------------------Shopmania-------------------------

            array(
                'type' => 'legend',
                'text' => '<i class="fa fa-rss"></i>' . $this->_lang('feed_exclusive_shopmania_legend'),
                'remove_border_button' => true,
                'class_container' => 'feed_configuration_exclusive shopmania',
                'style' => 'margin-top: 17px;'
            ),

            array(
                'label' => $this->_lang('feed_exclusive_delivery_cost_default'),
                'type' => 'text',
                'name' => 'feed_config_exclusive_shopmania_shipping_cost_default',
                'class_container' => 'feed_configuration_exclusive shopmania',
            ),

            //-----------------------Shopmania End---------------------

            //-----------------------Wish---------------------
            array(
                'type' => 'legend',
                'text' => '<i class="fa fa-rss"></i>' . $this->_lang('feed_exclusive_wish_legend'),
                'remove_border_button' => true,
                'class_container' => 'feed_configuration_exclusive wish',
                'style' => 'margin-top: 17px;'
            ),

            array(
                'label' => $this->_lang('feed_exclusive_delivery_cost_default'),
                'type' => 'text',
                'name' => 'feed_config_exclusive_wish_shipping_cost_default',
                'class_container' => 'feed_configuration_exclusive wish',
            ),
            //-----------------------Wish End-----------------


            //-----------------------Ceneje---------------------
            array(
                'type' => 'legend',
                'text' => '<i class="fa fa-rss"></i>' . $this->_lang('feed_exclusive_ceneje_legend'),
                'remove_border_button' => true,
                'class_container' => 'feed_configuration_exclusive ceneje',
                'style' => 'margin-top: 17px;'
            ),

            array(
                'label' => $this->_lang('feed_exclusive_delivery_cost_default'),
                'type' => 'text',
                'name' => 'feed_config_exclusive_ceneje_shipping_cost_default',
                'class_container' => 'feed_configuration_exclusive ceneje',
            ),
            //-----------------------Ceneje End-----------------


            //-----------------------Glarmiro--------------------------------
            array(
                'type' => 'legend',
                'text' => '<i class="fa fa-rss"></i>' . $this->_lang('feed_exclusive_glami_ro_legend'),
                'remove_border_button' => true,
                'class_container' => 'feed_configuration_exclusive glami_ro',
                'style' => 'margin-top: 17px;'
            ),

            array(
                'label' => $this->_lang('feed_exclusive_delivery_date_default'),
                'help' => $this->_lang('feed_exclusive_glami_ro_delivery_date_help'),
                'type' => 'text',
                'name' => 'feed_config_exclusive_glami_ro_delivery_date_default',
                'class_container' => 'feed_configuration_exclusive glami_ro',
            ),

//            array(
//                'label' => $this->_lang('feed_exclusive_glami_ro_size'),
//                'type' => 'boolean',
//                'name' => 'feed_config_glami_ro_tag_size',
//                'class_container' => 'feed_configuration_exclusive glami_ro toogle_main_field',
//            ),

            array(
                'label' => $this->_lang('feed_exclusive_glami_ro_size_fixed_value'),
                'help' => $this->_lang('feed_exclusive_glami_ro_size_fixed_value_help'),
                'type' => 'text',
                'name' => 'feed_config_glami_ro_tag_size_fixed_value',
                'class_container' => 'feed_configuration_exclusive glami_ro',
            ),

            array(
                'label' => $this->_lang('feed_exclusive_glami_ro_size_in_attribute'),
                'type' => 'select',
                'multiple' => true,
                'options' => $this->attributes_select,
                'name' => 'feed_config_glami_ro_tag_size_in_attribute',
                'class_container' => 'feed_configuration_exclusive glami_ro',
            ),

            array(
                'label' => $this->_lang('feed_exclusive_glami_ro_size_in_filter'),
                'type' => 'select',
                'multiple' => true,
                'options' => $this->filters_select,
                'name' => 'feed_config_glami_ro_tag_size_in_filter',
                'class_container' => 'feed_configuration_exclusive glami_ro',
            ),

            array(
                'label' => $this->_lang('feed_exclusive_glami_ro_size_in_option'),
                'type' => 'select',
                'multiple' => true,
                'options' => $this->options_select,
                'name' => 'feed_config_glami_ro_tag_size_in_option',
                'class_container' => 'feed_configuration_exclusive glami_ro',
            ),

            //-----------------------Glarmiro End--------------------------------

            //-----------------------Domodi--------------------------------
            array(
                'type' => 'legend',
                'text' => '<i class="fa fa-rss"></i>' . $this->_lang('feed_exclusive_domodi_legend'),
                'remove_border_button' => true,
                'class_container' => 'feed_configuration_exclusive domodi',
                'style' => 'margin-top: 17px;'
            ),

            array(
                'label' => $this->_lang('feed_exclusive_domodi_size_fixed_value'),
                'help' => $this->_lang('feed_exclusive_domodi_size_fixed_value_help'),
                'type' => 'text',
                'name' => 'feed_config_domodi_tag_size_fixed_value',
                'class_container' => 'feed_configuration_exclusive domodi',
            ),

            array(
                'label' => $this->_lang('feed_exclusive_domodi_size_in_attribute'),
                'type' => 'select',
                'multiple' => true,
                'options' => $this->attributes_select,
                'name' => 'feed_config_domodi_tag_size_in_attribute',
                'class_container' => 'feed_configuration_exclusive domodi',
            ),

            array(
                'label' => $this->_lang('feed_exclusive_domodi_size_in_filter'),
                'type' => 'select',
                'multiple' => true,
                'options' => $this->filters_select,
                'name' => 'feed_config_domodi_tag_size_in_filter',
                'class_container' => 'feed_configuration_exclusive domodi',
            ),

            array(
                'label' => $this->_lang('feed_exclusive_domodi_size_in_option'),
                'type' => 'select',
                'multiple' => true,
                'options' => $this->options_select,
                'name' => 'feed_config_domodi_tag_size_in_option',
                'class_container' => 'feed_configuration_exclusive domodi',
            ),

            array(
                'label' => $this->_lang('feed_exclusive_domodi_color_fixed_value'),
                'help' => $this->_lang('feed_exclusive_domodi_color_fixed_value_help'),
                'type' => 'text',
                'name' => 'feed_config_domodi_tag_color_fixed_value',
                'class_container' => 'feed_configuration_exclusive domodi',
            ),

            array(
                'label' => $this->_lang('feed_exclusive_domodi_color_in_attribute'),
                'type' => 'select',
                'multiple' => true,
                'options' => $this->attributes_select,
                'name' => 'feed_config_domodi_tag_color_in_attribute',
                'class_container' => 'feed_configuration_exclusive domodi',
            ),

            array(
                'label' => $this->_lang('feed_exclusive_domodi_color_in_filter'),
                'type' => 'select',
                'multiple' => true,
                'options' => $this->filters_select,
                'name' => 'feed_config_domodi_tag_color_in_filter',
                'class_container' => 'feed_configuration_exclusive domodi',
            ),

            array(
                'label' => $this->_lang('feed_exclusive_domodi_color_in_option'),
                'type' => 'select',
                'multiple' => true,
                'options' => $this->options_select,
                'name' => 'feed_config_domodi_tag_color_in_option',
                'class_container' => 'feed_configuration_exclusive domodi',
            ),

            array(
                'label' => $this->_lang('feed_exclusive_domodi_gender_fixed_value'),
                'help' => $this->_lang('feed_exclusive_domodi_gender_fixed_value_help'),
                'type' => 'text',
                'name' => 'feed_config_domodi_tag_gender_fixed_value',
                'class_container' => 'feed_configuration_exclusive domodi',
            ),

            array(
                'label' => $this->_lang('feed_exclusive_domodi_gender_in_attribute'),
                'type' => 'select',
                'multiple' => true,
                'options' => $this->attributes_select,
                'name' => 'feed_config_domodi_tag_gender_in_attribute',
                'class_container' => 'feed_configuration_exclusive domodi',
            ),

            array(
                'label' => $this->_lang('feed_exclusive_domodi_gender_in_filter'),
                'type' => 'select',
                'multiple' => true,
                'options' => $this->filters_select,
                'name' => 'feed_config_domodi_tag_gender_in_filter',
                'class_container' => 'feed_configuration_exclusive domodi',
            ),

            array(
                'label' => $this->_lang('feed_exclusive_domodi_gender_in_option'),
                'type' => 'select',
                'multiple' => true,
                'options' => $this->options_select,
                'name' => 'feed_config_domodi_tag_gender_in_option',
                'class_container' => 'feed_configuration_exclusive domodi',
            ),

            //-----------------------Glarmiro End--------------------------------


            //-----------------------skroutz--------------------------------

            array(
                'type' => 'legend',
                'text' => '<i class="fa fa-rss"></i>' . $this->_lang('feed_exclusive_skroutz_legend'),
                'remove_border_button' => true,
                'class_container' => 'feed_configuration_exclusive skroutz',
                'style' => 'margin-top: 17px;'
            ),

            //-----------------------skroutz End--------------------------------

            //----------------------- Exclusive merchant center --------------------------------
                array(
                    'type' => 'legend',
                    'text' => '<i class="fa fa-rss"></i>'.$this->_lang('feed_exclusive_google_merchant_local_legend'),
                    'remove_border_button' => true,
                    'class_container' => 'feed_configuration_exclusive google_merchant_local',
                    'style' => 'margin-top: 17px;'
                ),


            //----------------------- END Exclusive merchant center --------------------------------
            //----------------------- Exclusive merchant center --------------------------------
                array(
                    'type' => 'legend',
                    'text' => '<i class="fa fa-rss"></i>'.$this->_lang('feed_exclusive_merchant_center_legend'),
                    'remove_border_button' => true,
                    'class_container' => 'feed_configuration_exclusive google_merchant',
                    'style' => 'margin-top: 17px;'
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_size'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_size_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_tag_size',
                    'class_container' => 'feed_configuration_exclusive google_merchant skroutz facebook trovaprezzi_it toogle_main_field',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_size_fixed_value'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_size_fixed_value_help'),
                    'type' => 'text',
                    'name' => 'feed_config_tag_size_fixed_value',
                    'class_container' => 'feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_size',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_size_in_attribute'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->attributes_select,
                    'name' => 'feed_config_tag_size_in_attribute',
                    'class_container' => 'feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_size',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_size_in_filter'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->filters_select,
                    'name' => 'feed_config_tag_size_in_filter',
                    'class_container' => 'feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_size',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_size_in_option'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->options_select,
                    'name' => 'feed_config_tag_size_in_option',
                    'class_container' => 'feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_size',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_color'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_color_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_tag_color',
                    'class_container' => 'feed_configuration_exclusive google_merchant skroutz facebook trovaprezzi_it toogle_main_field',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_color_fixed_value'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_color_fixed_value_help'),
                    'type' => 'text',
                    'name' => 'feed_config_tag_color_fixed_value',
                    'class_container' => 'feed_configuration_exclusive google_merchant skroutz trovaprezzi_it feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_color',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_color_in_attribute'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->attributes_select,
                    'name' => 'feed_config_tag_color_in_attribute',
                    'class_container' => 'feed_configuration_exclusive google_merchant skroutz trovaprezzi_it feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_color',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_color_in_filter'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->filters_select,
                    'name' => 'feed_config_tag_color_in_filter',
                    'class_container' => 'feed_configuration_exclusive google_merchant skroutz trovaprezzi_it feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_color',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_color_in_option'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->options_select,
                    'name' => 'feed_config_tag_color_in_option',
                    'class_container' => 'feed_configuration_exclusive google_merchant skroutz trovaprezzi_it feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_color',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_map_option_values'),
                    'type' => 'textarea',
                    'name' => 'feed_config_map_option_values',
                    'class_container' => 'feed_configuration_exclusive skroutz toogle_main_field',
                    'after' => '<a href="javascript:{}" data-remodal-target="feed_exclusive_map_option_values">'.$this->_lang('feed_exclusive_map_option_values_link').'</a>'.$this->model_extension_devmanextensions_tools->get_remodal('feed_exclusive_map_option_values', $this->_lang('feed_exclusive_map_option_values'), $this->_lang('feed_exclusive_map_option_values_help'), array('button_cancel' => false, 'button_confirm' => false, '')),
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_material'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_material_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_tag_material',
                    'class_container' => 'feed_configuration_exclusive google_merchant toogle_main_field',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_material_fixed_value'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_material_fixed_value_help'),
                    'type' => 'text',
                    'name' => 'feed_config_tag_material_fixed_value',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_material',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_material_in_attribute'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->attributes_select,
                    'name' => 'feed_config_tag_material_in_attribute',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_material',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_material_in_filter'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->filters_select,
                    'name' => 'feed_config_tag_material_in_filter',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_material',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_material_in_option'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->options_select,
                    'name' => 'feed_config_tag_material_in_option',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_material',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_gender'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_gender_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_tag_gender',
                    'class_container' => 'feed_configuration_exclusive google_merchant facebook toogle_main_field',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_gender_fixed_value'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_gender_fixed_value_help'),
                    'type' => 'text',
                    'name' => 'feed_config_tag_gender_fixed_value',
                    'class_container' => 'feed_configuration_exclusive google_merchant facebook feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_gender',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_gender_in_attribute'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->attributes_select,
                    'name' => 'feed_config_tag_gender_in_attribute',
                    'class_container' => 'feed_configuration_exclusive google_merchant facebook feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_gender',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_gender_in_filter'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->filters_select,
                    'name' => 'feed_config_tag_gender_in_filter',
                    'class_container' => 'feed_configuration_exclusive google_merchant facebook feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_gender',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_gender_in_option'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->options_select,
                    'name' => 'feed_config_tag_gender_in_option',
                    'class_container' => 'feed_configuration_exclusive google_merchant facebook feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_gender',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_age_group'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_age_group_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_tag_age_group',
                    'class_container' => 'feed_configuration_exclusive google_merchant facebook toogle_main_field',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_age_group_fixed_value'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_age_group_fixed_value_help'),
                    'type' => 'text',
                    'name' => 'feed_config_tag_age_group_fixed_value',
                    'class_container' => 'feed_configuration_exclusive google_merchant facebook feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_age_group',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_age_group_in_attribute'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->attributes_select,
                    'name' => 'feed_config_tag_age_group_in_attribute',
                    'class_container' => 'feed_configuration_exclusive google_merchant facebook feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_age_group',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_age_group_in_filter'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->filters_select,
                    'name' => 'feed_config_tag_age_group_in_filter',
                    'class_container' => 'feed_configuration_exclusive google_merchant facebook feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_age_group',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_age_group_in_option'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->options_select,
                    'name' => 'feed_config_tag_age_group_in_option',
                    'class_container' => 'feed_configuration_exclusive google_merchant facebook feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_age_group',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_0'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_custom_label_0_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_tag_custom_label_0',
                    'class_container' => 'feed_configuration_exclusive google_merchant toogle_main_field',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_0_fixed_value'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_custom_label_0_fixed_value_help'),
                    'type' => 'text',
                    'name' => 'feed_config_tag_custom_label_0_fixed_value',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_0',
                ),
                
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_0_get_value_from'),
                    'type' => 'select',	
                    'options' => $this->custom_label_get_value_from_select,
                    'name' => 'feed_config_tag_custom_label_0_in_getvaluefrom',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_0'
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_0_in_attribute'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->attributes_select,
                    'name' => 'feed_config_tag_custom_label_0_in_attribute',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_0',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_0_in_filter'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->filters_select,
                    'name' => 'feed_config_tag_custom_label_0_in_filter',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_0',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_0_in_option'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->options_select,
                    'name' => 'feed_config_tag_custom_label_0_in_option',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_0',
                ),
                
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_1'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_custom_label_1_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_tag_custom_label_1',
                    'class_container' => 'feed_configuration_exclusive google_merchant toogle_main_field',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_1_fixed_value'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_custom_label_1_fixed_value_help'),
                    'type' => 'text',
                    'name' => 'feed_config_tag_custom_label_1_fixed_value',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_1',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_1_get_value_from'),
                    'type' => 'select',	
                    'options' => $this->custom_label_get_value_from_select,
                    'name' => 'feed_config_tag_custom_label_1_in_getvaluefrom',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_1'
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_1_in_attribute'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->attributes_select,
                    'name' => 'feed_config_tag_custom_label_1_in_attribute',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_1',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_1_in_filter'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->filters_select,
                    'name' => 'feed_config_tag_custom_label_1_in_filter',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_1',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_1_in_option'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->options_select,
                    'name' => 'feed_config_tag_custom_label_1_in_option',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_1',
                ),
                
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_2'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_custom_label_2_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_tag_custom_label_2',
                    'class_container' => 'feed_configuration_exclusive google_merchant toogle_main_field',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_2_fixed_value'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_custom_label_2_fixed_value_help'),
                    'type' => 'text',
                    'name' => 'feed_config_tag_custom_label_2_fixed_value',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_2',
                ),
                
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_2_get_value_from'),
                    'type' => 'select',	
                    'options' => $this->custom_label_get_value_from_select,
                    'name' => 'feed_config_tag_custom_label_2_in_getvaluefrom',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_2'
                ),
                
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_2_in_attribute'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->attributes_select,
                    'name' => 'feed_config_tag_custom_label_2_in_attribute',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_2',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_2_in_filter'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->filters_select,
                    'name' => 'feed_config_tag_custom_label_2_in_filter',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_2',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_2_in_option'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->options_select,
                    'name' => 'feed_config_tag_custom_label_2_in_option',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_2',
                ),
                
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_3'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_custom_label_3_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_tag_custom_label_3',
                    'class_container' => 'feed_configuration_exclusive google_merchant toogle_main_field',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_3_fixed_value'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_custom_label_3_fixed_value_help'),
                    'type' => 'text',
                    'name' => 'feed_config_tag_custom_label_3_fixed_value',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_3',
                ),
                
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_3_get_value_from'),
                    'type' => 'select',	
                    'options' => $this->custom_label_get_value_from_select,
                    'name' => 'feed_config_tag_custom_label_3_in_getvaluefrom',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_3'
                ),
                
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_3_in_attribute'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->attributes_select,
                    'name' => 'feed_config_tag_custom_label_3_in_attribute',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_3',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_3_in_filter'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->filters_select,
                    'name' => 'feed_config_tag_custom_label_3_in_filter',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_3',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_3_in_option'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->options_select,
                    'name' => 'feed_config_tag_custom_label_3_in_option',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_3',
                ),
                
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_4'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_custom_label_4_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_tag_custom_label_4',
                    'class_container' => 'feed_configuration_exclusive google_merchant toogle_main_field',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_4_fixed_value'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_custom_label_4_fixed_value_help'),
                    'type' => 'text',
                    'name' => 'feed_config_tag_custom_label_4_fixed_value',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_4',
                ),
                
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_4_get_value_from'),
                    'type' => 'select',	
                    'options' => $this->custom_label_get_value_from_select,
                    'name' => 'feed_config_tag_custom_label_4_in_getvaluefrom',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_4'
                ),
                
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_4_in_attribute'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->attributes_select,
                    'name' => 'feed_config_tag_custom_label_4_in_attribute',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_4',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_4_in_filter'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->filters_select,
                    'name' => 'feed_config_tag_custom_label_4_in_filter',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_4',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_custom_label_4_in_option'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->options_select,
                    'name' => 'feed_config_tag_custom_label_4_in_option',
                    'class_container' => 'feed_configuration_exclusive google_merchant feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_tag_custom_label_4',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_store_code'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_store_code_help'),
                    'type' => 'text',
                    'name' => 'feed_config_store_code',
                    'class_container' => 'feed_configuration_exclusive google_merchant google_merchant_local feed_configuration_product',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_pickup_method'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_pickup_method_help'),
                    'type' => 'select',
                    'options' => $this->pickup_method_options,
                    'name' => 'feed_config_pickup_method',
                    'class_container' => 'feed_configuration_exclusive google_merchant google_merchant_local feed_configuration_product',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_pickup_sla'),
                    'help' => $this->_lang('feed_exclusive_merchant_center_pickup_sla_help'),
                    'type' => 'select',
                    'options' =>  $this->pickup_sla_options,
                    'name' => 'feed_config_pickup_sla',
                    'class_container' => 'feed_configuration_exclusive google_merchant google_merchant_local feed_configuration_product',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_availability_backorder'),
                    'type' => 'select',
                    'options' => $this->stock_statuses_select,
                    'name' => 'feed_config_availability_backorder',
                    'class_container' => 'feed_configuration_exclusive google_merchant toogle_main_field feed_configuration_product'
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_merchant_center_availability_preorder'),
                    'type' => 'select',
                    'options' =>  $this->stock_statuses_select,
                    'name' => 'feed_config_availability_preorder',
                    'class_container' => 'feed_configuration_exclusive google_merchant toogle_main_field feed_configuration_product'
                ),
            //----------------------- END Exclusive merchant center --------------------------------
                array(
                    'type' => 'legend',
                    'text' => '<i class="fa fa-rss"></i>'.$this->_lang('feed_exclusive_bing_legend'),
                    'remove_border_button' => true,
                    'class_container' => 'feed_configuration_exclusive bing',
                    'style' => 'margin-top: 17px;'
                ),

            array(
                'label' => $this->_lang('feed_exclusive_delivery_cost_default'),
                'type' => 'text',
                'name' => 'feed_config_exclusive_bing_shipping_cost_default',
                'class_container' => 'feed_configuration_exclusive bing',
            ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_size'),
                    'help' => $this->_lang('feed_exclusive_bing_size_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_bing_tag_size',
                    'class_container' => 'feed_configuration_exclusive bing toogle_main_field',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_size_type'),
                    'type' => 'select',
                    'options' => array(
                        'regular' => 'regular',
                        'petite' => 'petite',
                        'plus' => 'plus',
                        'big and tall' => 'big and tall',
                        'maternity' => 'maternity',
                    ),
                    'name' => 'feed_config_bing_tag_size_type',
                    'class_container' => 'feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_size',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_size_system'),
                    'type' => 'select',
                    'options' => array(
                        'DE' => 'DE',
                        'FR' => 'FR',
                        'AU' => 'AU',
                        'US' => 'US',
                        'UK' => 'UK',
                    ),
                    'name' => 'feed_config_bing_tag_size_system',
                    'class_container' => 'feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_size',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_size_fixed_value'),
                    'help' => $this->_lang('feed_exclusive_bing_size_fixed_value_help'),
                    'type' => 'text',
                    'name' => 'feed_config_bing_tag_size_fixed_value',
                    'class_container' => 'feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_size',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_size_in_attribute'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->attributes_select,
                    'name' => 'feed_config_bing_tag_size_in_attribute',
                    'class_container' => 'feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_size',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_size_in_filter'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->filters_select,
                    'name' => 'feed_config_bing_tag_size_in_filter',
                    'class_container' => 'feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_size',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_size_in_option'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->options_select,
                    'name' => 'feed_config_bing_tag_size_in_option',
                    'class_container' => 'feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_size',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_color'),
                    'help' => $this->_lang('feed_exclusive_bing_color_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_bing_tag_color',
                    'class_container' => 'feed_configuration_exclusive bing toogle_main_field',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_color_fixed_value'),
                    'help' => $this->_lang('feed_exclusive_bing_color_fixed_value_help'),
                    'type' => 'text',
                    'name' => 'feed_config_bing_tag_color_fixed_value',
                    'class_container' => 'feed_configuration_exclusive bing feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_color',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_color_in_attribute'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->attributes_select,
                    'name' => 'feed_config_bing_tag_color_in_attribute',
                    'class_container' => 'feed_configuration_exclusive bing feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_color',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_bing_color_in_filter'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->filters_select,
                    'name' => 'feed_config_bing_tag_color_in_filter',
                    'class_container' => 'feed_configuration_exclusive bing feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_color',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_bing_color_in_option'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->options_select,
                    'name' => 'feed_config_bing_tag_color_in_option',
                    'class_container' => 'feed_configuration_exclusive bing feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_color',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_material'),
                    'help' => $this->_lang('feed_exclusive_bing_material_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_bing_tag_material',
                    'class_container' => 'feed_configuration_exclusive bing toogle_main_field',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_material_fixed_value'),
                    'help' => $this->_lang('feed_exclusive_bing_material_fixed_value_help'),
                    'type' => 'text',
                    'name' => 'feed_config_bing_tag_material_fixed_value',
                    'class_container' => 'feed_configuration_exclusive bing feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_material',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_material_in_attribute'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->attributes_select,
                    'name' => 'feed_config_bing_tag_material_in_attribute',
                    'class_container' => 'feed_configuration_exclusive bing feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_material',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_bing_material_in_filter'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->filters_select,
                    'name' => 'feed_config_bing_tag_material_in_filter',
                    'class_container' => 'feed_configuration_exclusive bing feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_material',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_bing_material_in_option'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->options_select,
                    'name' => 'feed_config_bing_tag_material_in_option',
                    'class_container' => 'feed_configuration_exclusive bing feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_material',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_gender'),
                    'help' => $this->_lang('feed_exclusive_bing_gender_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_bing_tag_gender',
                    'class_container' => 'feed_configuration_exclusive bing toogle_main_field',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_gender_fixed_value'),
                    'help' => $this->_lang('feed_exclusive_bing_gender_fixed_value_help'),
                    'type' => 'text',
                    'name' => 'feed_config_bing_tag_gender_fixed_value',
                    'class_container' => 'feed_configuration_exclusive bing feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_gender',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_gender_in_attribute'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->attributes_select,
                    'name' => 'feed_config_bing_tag_gender_in_attribute',
                    'class_container' => 'feed_configuration_exclusive bing feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_gender',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_bing_gender_in_filter'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->filters_select,
                    'name' => 'feed_config_bing_tag_gender_in_filter',
                    'class_container' => 'feed_configuration_exclusive bing feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_gender',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_bing_gender_in_option'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->options_select,
                    'name' => 'feed_config_bing_tag_gender_in_option',
                    'class_container' => 'feed_configuration_exclusive bing feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_gender',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_age_group'),
                    'help' => $this->_lang('feed_exclusive_bing_age_group_help'),
                    'type' => 'boolean',
                    'name' => 'feed_config_bing_tag_age_group',
                    'class_container' => 'feed_configuration_exclusive bing toogle_main_field',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_age_group_fixed_value'),
                    'help' => $this->_lang('feed_exclusive_bing_age_group_fixed_value_help'),
                    'type' => 'text',
                    'name' => 'feed_config_bing_tag_age_group_fixed_value',
                    'class_container' => 'feed_configuration_exclusive bing feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_age_group',
                ),

                array(
                    'label' => $this->_lang('feed_exclusive_bing_age_group_in_attribute'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->attributes_select,
                    'name' => 'feed_config_bing_tag_age_group_in_attribute',
                    'class_container' => 'feed_configuration_exclusive bing feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_age_group',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_bing_age_group_in_filter'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->filters_select,
                    'name' => 'feed_config_bing_tag_age_group_in_filter',
                    'class_container' => 'feed_configuration_exclusive bing feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_age_group',
                ),
                array(
                    'label' => $this->_lang('feed_exclusive_bing_age_group_in_option'),
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $this->options_select,
                    'name' => 'feed_config_bing_tag_age_group_in_option',
                    'class_container' => 'feed_configuration_exclusive bing feed_configuration_product toogle_seconday_field conditional_feed_select '.$this->extension_group_config.'_feed_config_bing_tag_age_group',
                ),
                array(
                    'type' => 'legend',
                    'text' => '<i class="fa fa-filter"></i>'.$this->_lang('feed_products_quick_filter'),
                    'remove_border_button' => true,
                    'class_container' => 'feed_configuration_product also_in_google_reviews',
                    'style' => 'margin-top: 17px;'
                ),

                 array(
                    'label' => $this->_lang('feed_products_quick_filter_categories'),
                    'help' => $this->_lang('feed_products_quick_filter_categories_help'),
                    'type' => 'select',
                    'name' => 'feed_config_quick_filter_category_ids',
                    'multiple' => true,
                    'all_options' => true,
                    'options' => $categories_select_format,
                    'class_container' => 'feed_configuration_product also_in_google_reviews',
                    'columns' => 3
                ),

                array(
                    'label' => $this->_lang('feed_products_quick_filter_manufacturers'),
                    'help' => $this->_lang('feed_products_quick_filter_manufacturers_help'),
                    'type' => 'select',
                    'name' => 'feed_config_quick_filter_manufacturer_ids',
                    'multiple' => true,
                    'all_options' => true,
                    'options' => $manufacturers_select_format,
                    'class_container' => 'feed_configuration_product also_in_google_reviews',
                    'columns' => 3
                ),

                array(
                    'label' => $this->_lang('feed_products_quick_filter_excluded'),
                    'help' => $this->_lang('feed_products_quick_filter_excluded_help'),
                    'type' => 'products_autocomplete',
                    'name' => 'feed_config_quick_filter_excluded_ids',
                    'class_container' => 'feed_configuration_product also_in_google_reviews',
                    'columns' => 3
                ),

                array(
                    'type' => 'legend',
                    'text' => '<i class="fa fa-filter"></i>'.$this->_lang('feed_filters'),
                    'remove_border_button' => true,
                    'class_container' => 'feed_configuration_product also_in_google_reviews',
                    'style' => 'margin-top: 17px;'
                ),

                array(
                    'type' => 'html_hard',
                    'html_code' => '<div class="feed_configuration_product filters_configuration also_in_google_reviews"></div>',
                    'class_container' => 'feed_configuration_product filters_configuration also_in_google_reviews'
                ),

                array(
                    'type' => 'legend',
                    'text' => '<i class="fa fa-save"></i>'.$this->_lang('feed_save_legend'),
                    'class_container' => 'feed_configuration_save',
                    'remove_border_button' => true,
                ),

                array(
                    'label' => $this->_lang('feed_feed_name'),
                    'type' => 'text',
                    'name' => 'feed_config_feed_name',
                    'class_container' => 'feed_configuration_save feed_name'
                ),


                array(
                    'type' => 'button',
                    'label' => $this->_lang('save_feed'),
                    'text' => '<i class="fa fa-floppy-o"></i> '.$this->_lang('save_feed'),
                    'onclick' => 'feed_save();',
                    'class_container' => 'feed_configuration_save button_save_profile'
                ),
                array(
                    'type' => 'button',
                    'class' => 'danger',
                    'label' => $this->_lang('delete_feed'),
                    'text' => '<i class="fa fa-trash"></i> '.$this->_lang('delete_feed'),
                    'onclick' => 'feed_delete();',
                    'class_container' => 'feed_configuration_save button_delete_profile'
                ),
            );
            return $fields;
        }

        public function get_filters_from_profile($feed_id) {
            if(empty($feed_id))
                return array();

            $profile = $this->{$this->model_feed}->load($feed_id, true);
            return array_key_exists('profile', $profile) && array_key_exists('export_filter', $profile['profile']) ? $profile['profile']['export_filter'] : array();
        }

        public function get_select_from_database_fields($database_fields, $empty_value = '') {
            $fields_to_select = array();
            if(!empty($empty_value)) {
                $fields_to_select[''] = $empty_value;
            }
            foreach ($database_fields as $table_name => $fields) {
                $table_name_formatted = $this->get_legible_database_field_name($table_name);
                foreach ($fields as $field_name => $field_info) {
                    $field_name_formatted = $this->get_legible_database_field_name($field_name);
                    $type = $field_info['type'];
                    $final_name = $table_name_formatted.' - '.$field_name_formatted.' ('.$type.')';
                    $fields_to_select[$table_name.'-'.$field_name.'-'.$type] = $final_name;
                }
            }

            return $fields_to_select;
        }

        public function get_feed_filters_html($database_fields) {
            $feed_id = array_key_exists('feed_id', $this->request->post) && !empty($this->request->post['feed_id']) ? $this->request->post['feed_id'] : '';
            $config_filters = $this->get_filters_from_profile($feed_id);
            $filters_num = !empty($config_filters['filters']) ? count($config_filters['filters']) : 0;
            $main_conditional = !empty($config_filters) && array_key_exists('main_conditional', $config_filters['config']) ? $config_filters['config']['main_conditional'] : 'AND';

            $fields_to_select = $this->get_select_from_database_fields($database_fields);

            $button_add_filter = '<a href="javascript:{}" onclick="feed_add_filter($(this));" class="button" title="'.$this->_lang('feed_filters_add_filter').'"><i class="fa fa-plus-square" aria-hidden="true"></i></a>';
            $button_remove_filter = '<a href="javascript:{}" onclick="feed_remove_filter($(this));" class="button danger" title="'.$this->_lang('feed_filters_remove_filter').'"><i class="fa fa-minus-square" aria-hidden="true"></i></a>';

            $html = '
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <td colspan="3" style="text-align:right;">'.$this->_lang('feed_filters_add_filter').'</td>
                        <td style="width: 68px;">'.$button_add_filter.'</td>
                    </tr>
                    <tr>
                        <td>'.$this->_lang('feed_filters_thead_field').'</td>
                        <td>'.$this->_lang('feed_filters_thead_condition').'</td>
                        <td>'.$this->_lang('feed_filters_thead_value').'</td>
                        <td>'.$this->_lang('feed_filters_thead_actions').'</td>
                    </tr>
                </thead>
                <tbody>';
                    $html .= $this->get_filter_row('replace_by_number', $fields_to_select, array(), $button_remove_filter, true, $filters_num);
                    if(!empty($config_filters['filters'])) {
                        foreach ($config_filters['filters'] as $key => $config_filter) {
                            $html .= $this->get_filter_row($key, $fields_to_select, $config_filter, $button_remove_filter);
                        }
                    }
                $html .= '</tbody>
                <tfoot style="display:none;">
                    <tr>
                        <td colspan="2">'.$this->_lang('feed_filters_main_conditional').'</td>
                        <td colspan="2">'.$this->select_constructor('export_filter[main_conditional]', $this->conditionals, $main_conditional).'
                    </tr>
                </tfoot>
            </table>
            ';

            return $html;
        }

        public function get_products_autocomplete($product_ids) {
            $array_return = array(
                'html' => ''
            );

            if(!empty($this->request->post['product_ids'])) {

                $template_html = '<div id="element-PRODUCT_ID"><i class="fa fa-minus-circle"></i> PRODUCT_NAME<input type="hidden" name="google_all_feed_config_quick_filter_excluded_ids[]" value="PRODUCT_ID"></div>';

                foreach ($this->request->post['product_ids'] as $product_id) {
                    $prod_desc = $this->db->query("SELECT name FROM ".DB_PREFIX."product_description WHERE language_id = ".(int)$this->config->get("config_language_id")." AND product_id = ".(int)$product_id);
                    if(!empty($prod_desc->row['name'])) {
                        $template_edited = str_replace("PRODUCT_ID", $product_id, $template_html);
                        $template_edited = str_replace("PRODUCT_NAME", $prod_desc->row['name'], $template_edited);
                        $array_return['html'] .= $template_edited;
                    }

                }

            }

            echo json_encode($array_return); die;
        }

        function get_filter_row($number, $fields_to_select, $filter_config = array(), $button_remove_filter = '', $is_model = false, $filter_number = false) {
            $extra = array('class' => 'selectpicker form-control conditional field', 'onchange' => 'feed_filter_reset_profile($(this).closest(\'tr\'))');

            $value = !empty($filter_config) && array_key_exists('field', $filter_config) ? $filter_config['field'] : '';
            $field_select = $this->select_constructor('export_filter['.$number.'][field]', $fields_to_select, $value, $extra);

            $conditionals_selects = '';
            foreach ($this->filter_field_types as $key => $type) {
                $value = !empty($filter_config) && array_key_exists($type, $filter_config['conditional']) ? $filter_config['conditional'][$type] : '';
                $extra = array('class' => 'selectpicker form-control conditional '.$type);
                $conditionals_selects .= $this->select_constructor('export_filter['.$number.'][conditional]['.$type.']', $this->{'filter_conditionals_'.$type}, $value, $extra);

            }

            $value = !empty($filter_config) && array_key_exists('value', $filter_config) ? $filter_config['value'] : '';
            $value_inputs = '<input name="export_filter['.$number.'][value]" type="text" class="form-control" value="'.$value.'">';

            return '<tr'.($is_model ? ' data-filterNumber="'.$filter_number.'" class="filter_model"': '').'>
                            <td class="fields">'.$field_select.'</td>
                            <td class="conditionals">'.$conditionals_selects.'</td>
                            <td class="values">'.$value_inputs.'</td>
                            <td class="remove">'.$button_remove_filter.'</td>
                        </tr>';
        }

        function get_field_html($label, $field, $extra_config = array()) {
            $form_group_class = array_key_exists('class', $extra_config) ? $extra_config['class'] : '';
            $label_class = array_key_exists('class_label', $extra_config) ? $extra_config['class_label'] : '';
            $label_size = array_key_exists('label_size', $extra_config) ? $extra_config['label_size'] : 2;
            $content_size = 12 - $label_size;

            $field_html = '<div class="form-group '.$form_group_class.'">
                <label class="col-md-'.$label_size.' '.$label_class.' control-label">'.$label.'</label>
                <div class="col-md-'.$content_size.'">'.$field.'</div>
            </div>';

            return $field_html;
        }

        function get_feed_urls_remodal_content() {
            $profiles = $this->{$this->model_feed}->get_profiles_for_url_and_crons();

            $html_profile_urls = '';

            foreach ($profiles as $key => $prof) {
                $html_profile_urls .= '<h3 style="text-align: left;">'.$prof['name'].'</h3>';
                $html_profile_urls .= '<ul>';
                $html_profile_urls .= '<li><b>Direct URL: </b><a href="javascript:{}" onclick="copy_to_clickboard(\''.$prof['url_direct'].'\')">'.$this->_lang('feed_copy_to_clickboard').'</a> - '.$this->_lang('feed_or').' - <a href="'.$prof['url_direct'].'" target="_blank">'.$this->_lang('feed_open_open_now').'</a></li>';
                $html_profile_urls .= '<li><b>CRON URL: </b>'.($prof['url_cron'] ? '<a href="javascript:{}" onclick="copy_to_clickboard(\''.$prof['url_cron'].'\')">'.$this->_lang('feed_copy_to_clickboard').'</a>' : $this->_lang('feed_feed_urls_remodal_cron_incompatible')).'</li>';
                $html_profile_urls .= '</ul>';
            }

            $html_profile_urls .= '<br><b>CRON COMMAND EXAMPLE:</b><br><br>
                <span class="cmin">*</span> <span class="chou">*</span> <span class="cmon">*</span> <span class="cmont">*</span> <span class="cwee">*</span> <span class="cpat">[PATH_TO_PHP]</span> <span class="curl">/public_html/gmt_cron.php feed_id=X</span><br>
                <span class="cmin">|</span> <span class="chou">|</span> <span class="cmon">|</span> <span class="cmont">|</span> <span class="cwee">|</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="cpat">|</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="curl">|</span><br>
                <span class="cmin">|</span> <span class="chou">|</span> <span class="cmon">|</span> <span class="cmont">|</span> <span class="cwee">|</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="cpat">|</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="curl">|------- CRON URL COPIED</span><br>
                <span class="cmin">|</span> <span class="chou">|</span> <span class="cmon">|</span> <span class="cmont">|</span> <span class="cwee">|</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="cpat">|------ Path to PHP (example "/usr/bin/php")</span><br>
                <span class="cmin">|</span> <span class="chou">|</span> <span class="cmon">|</span> <span class="cmont">|</span> <span class="cwee">|------ DAY OF WEEK (0 - 6) (Sunday = 6)</span><br>
                <span class="cmin">|</span> <span class="chou">|</span> <span class="cmon">|</span> <span class="cmont">|------ MONTH (1 - 12)</span><br>
                <span class="cmin">|</span> <span class="chou">|</span> <span class="cmon">|------ DAY OF MONTH (1 - 31)</span><br>
                <span class="cmin">|</span> <span class="chou">|------ HOUR (0 - 23)</span><br>
                <span class="cmin">|------ MIN (0 - 59)</span>';
            
            return $html_profile_urls;
        }

        function _check_ajax_function($function_name) {
            if(in_array($function_name, array('get_filters_html'))) {
                $category = 'products';
                $this->load->model('extension/module/google_all_database');
                $tables = $this->model_extension_module_google_all_database->get_database($category, array('is_filter' => true));
                $columns_html = $this->get_feed_filters_html($tables[$category]);
                $array_return = array('html' => $columns_html);
                echo json_encode($array_return); die;
            } elseif($function_name == 'feed_save') {
                try {
                    $this->{$this->model_feed}->save();
                } catch (Exception $e) {
                    $array_return = array();
                    $array_return['error'] = true;
                    $array_return['message'] = $e->getMessage();
                    echo json_encode($array_return); die;
                }
            }elseif($function_name == 'feed_delete') {
                $this->{$this->model_feed}->delete();
            }elseif($function_name == 'feed_load') {
                $this->{$this->model_feed}->load();
            }elseif($function_name == 'get_products_autocomplete') {
                $this->get_products_autocomplete($this->request->post);
            }
        }

        function _send_custom_variables_to_view($variables) {
            $variables['get_filters_html_url'] = htmlspecialchars_decode($this->url->link($this->real_extension_type.'/'.$this->extension_name.'&ajax_function=get_filters_html', $this->token_name.'=' . $this->session->data[$this->token_name], 'SSL'));
            $variables['feed_save_url'] = htmlspecialchars_decode($this->url->link($this->real_extension_type.'/'.$this->extension_name.'&ajax_function=feed_save', $this->token_name.'=' . $this->session->data[$this->token_name], 'SSL'));
            $variables['feed_load_url'] = htmlspecialchars_decode($this->url->link($this->real_extension_type.'/'.$this->extension_name.'&ajax_function=feed_load', $this->token_name.'=' . $this->session->data[$this->token_name], 'SSL'));
            $variables['feed_delete_url'] = htmlspecialchars_decode($this->url->link($this->real_extension_type.'/'.$this->extension_name.'&ajax_function=feed_delete', $this->token_name.'=' . $this->session->data[$this->token_name], 'SSL'));
            $variables['get_products_autocomplete_url'] = htmlspecialchars_decode($this->url->link($this->real_extension_type.'/'.$this->extension_name.'&ajax_function=get_products_autocomplete', $this->token_name.'=' . $this->session->data[$this->token_name], 'SSL'));
            $variables['clipboard_copied'] = $this->_lang('feed_clipboard_copied');
            return $variables;
        }
    }
?>