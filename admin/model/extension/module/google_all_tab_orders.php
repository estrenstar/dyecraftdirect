<?php

if (version_compare(preg_replace('/[^0-9.].*$/', '', VERSION), '2', '<')) {
    require_once(DIR_SYSTEM . 'library/google_marketing_tools/data_layer.php');
}

use google_marketing_tools\data_layer;

class ModelExtensionModuleGoogleAllTabOrders extends ModelExtensionModuleGoogleAll
{
    /**
     * Default items per page (From admin config)
     * 
     * @var int
     */
    private $items_per_page;

    /**
     * Extension version
     * 
     * @var string
     */
    private $extension_version;

    /**
     * Constructor
     * 
     * @param Registry $registry
     */
    public function __construct( $registry ) {
        parent::__construct($registry);

        if (version_compare(VERSION, '2.2', '>=')) {
            $this->registry->set('tax', new Cart\Tax($registry));
        } else {
            if (!class_exists('Tax')) {
                require_once(DIR_SYSTEM . 'library/tax.php');
            }

            if (version_compare(preg_replace('/[^0-9.].*$/', '', VERSION), '2', '<')) {
                // Solve issue with null customer library
                require_once(DIR_SYSTEM . 'library/customer.php');
                
                $this->registry->set('customer', new Customer($registry));    
            }

            $this->registry->set('tax', new Tax($registry));
        }

        // Load languages
        $this->load->language($this->real_extension_type.'/google_all_tab_orders');
        $this->load->language($this->real_extension_type.'/google_all_general_texts');

        if (version_compare(VERSION, '2.0.3.1', '<')) {
            $this->items_per_page = 20;
        } else {
            $this->items_per_page = intval($this->config->get('config_limit_admin'));
        }

        $this->extension_version = $registry->has('gmt_extension_version') ? $this->gmt_extension_version : date('Ymdhis');
        
        $this->download_path    = HTTPS_CATALOG . 'gmt_orders_export/';
        $this->path_tmp         = substr(DIR_APPLICATION, 0, strrpos(DIR_APPLICATION, '/', -2)).'/' . 'gmt_orders_export/';
    }

    /**
     * Get columns for the orders table
     * 
     * @param bool $is_export - Flag to check if the columns are for export
     * @return array
     */
    private function _get_orders_table_head( $is_export = false ) {
        $columns = array(
            $this->_lang('column_order_id'),
            $this->_lang('column_valid_status'),
            $this->_lang('column_date_added'),
            $this->_lang('column_client_id'),
            $this->_lang('column_subtotal'),
            $this->_lang('column_tax'),
            $this->_lang('column_shipping'),
            $this->_lang('column_total'),
            $this->_lang('column_payment_method'),
            $this->_lang('column_g_ads'),
            $this->_lang('column_gcl_dc'),
            $this->_lang('column_fb_ads'),
            $this->_lang('column_fbcl_id'),
            $this->_lang('column_bing_ads'),
            $this->_lang('column_msclk_id'),
            $this->_lang('column_consent'),
            $this->_lang('column_tracked'),
            $this->_lang('column_cancelled'),
            " "
        );

        if (!$is_export) {
            unset($columns[array_search($this->_lang('column_client_id'), $columns)]);
            unset($columns[array_search($this->_lang('column_gcl_dc'), $columns)]);
            unset($columns[array_search($this->_lang('column_fbcl_id'), $columns)]);
            unset($columns[array_search($this->_lang('column_msclk_id'), $columns)]);
        }

        return $columns;
    }

    /**
     * Get tab fields
     * 
     * Called from the main controller
     * 
     * @return array
     */
    public function get_fields() {
        $this->document->addStyle($this->api_url.'opencart_admin/common/css/jquery-confirm.min.css?v='.$this->extension_version);
        $this->document->addStyle($this->api_url.'opencart_admin/common/css/jquery.toast.min.css?v='.$this->extension_version);
        $this->document->addStyle('view/stylesheet/devmanextensions/google_marketing_tools/tab_orders.css?v='.$this->extension_version);
        $this->document->addScript(HTTPS_CATALOG.'/catalog/view/javascript/devmanextensions_gmt/gmt-library.js?v='.$this->extension_version);
        $this->document->addScript($this->api_url.'opencart_admin/common/js/jquery-confirm.min.js?v='.$this->extension_version);
        $this->document->addScript($this->api_url.'opencart_admin/common/js/jquery.toast.min.js?v='.$this->extension_version);
        $this->document->addScript('view/javascript/devmanextensions/google_marketing_tools/tab_orders.js?v='.$this->extension_version);

        $theads = $this->_get_orders_table_head();

        $data = $this->_get_orders_data();

        $has_orders         = isset($data['orders']) ? true : false;
        $toot_content_align = $has_orders ? 'right' : 'center';
        $tfoot_content      = $has_orders ? $this->_get_pagination($data['total_pages']) : $this->_lang('no_orders');

        $modal = $this->model_extension_devmanextensions_tools->get_remodal('gmt-order-info', '', '', array('button_cancel' => false, 'button_confirm' => false));
        
        $filter = $this->_get_filters_form();
        $popup_download_message = $this->_get_popup_download_message();

        $fields = array(
            array(
                'type'          => 'html_hard',
                'html_code'     => $filter . $popup_download_message,
                'multi_store'   => false
            ),
            array(
                'type'          => 'table',
                'name'          => 'orders',
                'class'         => 'gmt-orders',
                'id'            => 'gmtOrders',
                'theads'        => $theads,
                'data'          => $has_orders ? $data['orders'] : array(),
                'tfoot'         => "<tr><td colspan=\"15\" style=\"text-align:{$toot_content_align};\">{$tfoot_content}</td></tr>",
                'multi_store'   => false
            ),
            array(
                'type'          => 'html_hard',
                'html_code'     => $modal,
                'multi_store'   => false
            )
        );

        return $fields;
    }

    /**
     * Send custom js variables to view
     * 
     * Called from the main controller
     * 
     * @param array $variables
     * @return array
     */
    public function _send_custom_variables_to_view( $variables ) {
        $variables['link_ajax_get_orders']      = htmlspecialchars_decode($this->url->link($this->real_extension_type.'/'.$this->extension_name.'&ajax_function=ajax_get_orders', $this->token_name.'='.$this->session->data[$this->token_name], 'SSL'));        
        $variables['link_ajax_get_order']       = htmlspecialchars_decode($this->url->link($this->real_extension_type.'/'.$this->extension_name.'&ajax_function=ajax_get_order', $this->token_name.'='.$this->session->data[$this->token_name], 'SSL'));
        $variables['link_ajax_get_order_info']  = htmlspecialchars_decode($this->url->link($this->real_extension_type.'/'.$this->extension_name.'&ajax_function=ajax_get_order_info', $this->token_name.'='.$this->session->data[$this->token_name], 'SSL'));
        $variables['link_ajax_save_store_id']   = htmlspecialchars_decode($this->url->link($this->real_extension_type.'/'.$this->extension_name.'&ajax_function=ajax_save_store_id', $this->token_name.'='.$this->session->data[$this->token_name], 'SSL'));
        $variables['link_ajax_export_orders']   = htmlspecialchars_decode($this->url->link($this->real_extension_type.'/'.$this->extension_name.'&ajax_function=ajax_export_orders', $this->token_name.'='.$this->session->data[$this->token_name], 'SSL'));

        return $variables;
    }

    /**
     * Check AJAX function
     * 
     * Called from the main controller
     * 
     * @param string $function_name
     * @return void
     */
    public function _check_ajax_function( $function_name ) {
        if ($function_name === 'ajax_get_orders') {
            $this->_ajax_get_orders();
        } elseif ($function_name == 'ajax_get_order_info') {
            $this->_ajax_get_order_info();
        } elseif ($function_name == 'ajax_get_order') {
            $this->_ajax_get_order();
        } elseif ($function_name == 'ajax_save_store_id') {
            $this->_ajax_save_store_id();
        } elseif ($function_name == 'ajax_export_orders') {
            $this->_ajax_export_orders();
        }
    }

    /**
     * AJAX action to get orders
     * 
     * @return void
     */
    private function _ajax_get_orders() {
        $data = $this->_get_orders_data();

        if (empty($data)) {
            $data = array( 
                'message' => $this->_lang('text_no_match')
            );
        }

        header('X-Robots-Tag: noindex, nofollow', true);
        header('Content-Type: application/json', true);
        
        echo json_encode($data);
        die;
    }

    /**
     * AJAX action to get order
     * 
     * @return void
     */
    private function _ajax_get_order() {
        if (isset($this->request->get['order_id'])) {
            $order_id           = (int)$this->request->get['order_id'];
            $gmt_data           = $this->_get_gmt_info($order_id);
            $get_original_data  = isset($this->request->get['get_original_data']) ? (bool)$this->request->get['get_original_data'] : false;

            $gmt_info    = is_array($gmt_data) && isset($gmt_data['gmt_info']) && !empty($gmt_data['gmt_info']) ? json_decode($gmt_data['gmt_info'], true) : array();
            $gmt_version = isset($gmt_info['gmt_version']) && !empty($gmt_info['gmt_version']) ? $gmt_info['gmt_version'] : '0.0.0';

            if (version_compare($gmt_version, '13.4.1', '>=')) {
                $order_info = $this->_get_order($order_id);
                $gmt_info   = $this->_build_datalayer_from_gmt_info($order_info, $get_original_data);
            }

            $gmt_data = [
                'gmt_info'      => $gmt_info,
                'gmt_consent'   => is_array($gmt_data) && isset($gmt_data['gmt_consent']) ? json_decode($gmt_data['gmt_consent'], true) : array()
            ];

            header('X-Robots-Tag: noindex, nofollow', true);
            header('Content-Type: application/json', true);

            echo json_encode($gmt_data);
            die;
        }
    }

    /**
     * AJAX action to get order info
     * 
     * @return void
     */
    private function _ajax_get_order_info() {
        if (isset($this->request->get['order_id'])) {
            $order_id   = (int)$this->request->get['order_id'];
            $order      = $this->_get_gmt_info_for_remodal($order_id);

            $gmt_info    = is_array($order) && isset($order['gmt_info']) && !empty($order['gmt_info']) ? json_decode($order['gmt_info'], true) : array();
            $gmt_version = isset($gmt_info['gmt_version']) && !empty($gmt_info['gmt_version']) ? $gmt_info['gmt_version'] : '0.0.0';

            // Since version 13.4.1, order data is no longer saved in gmt_info. Instead, it's obtained directly from the order table
            if (version_compare($gmt_version, '13.4.1', '>=')) {
                $order_info = $this->_get_order($order_id);
                $gmt_info   = $this->_build_datalayer_from_gmt_info($order_info);
            }

            $order_data     = isset($gmt_info['order_data']['order']) ? $gmt_info['order_data']['order'] : array();
            $customer_data  = isset($gmt_info['order_data']['customer']['general']) ? $gmt_info['order_data']['customer']['general'] : array();

            $email          = isset($customer_data['email']) ? $customer_data['email'] : '';
            $first_name     = isset($customer_data['firstname']) ? $customer_data['firstname'] : '';
            $last_name      = isset($customer_data['lastname']) ? $customer_data['lastname'] : '';
            $full_name      = !empty($first_name) ? "{$first_name} {$last_name}" : '-';
            $consent        = isset($order['gmt_consent']) && !empty($order['gmt_consent']) ? json_decode($order['gmt_consent'], true) : array();
            $tracked_info   = isset($order['tracked_info']) && !empty($order['tracked_info']) ? json_decode($order['tracked_info'], true) : array();
            $cancelled_info = isset($order['cancelled_info']) && !empty($order['cancelled_info']) ? json_decode($order['cancelled_info'], true) : array();

            $total          = isset($order_data['total']) ? $order_data['total'] : 0;
            $subtotal       = isset($order_data['subtotal']) ? $order_data['subtotal'] : 0;
            $tax            = isset($order_data['tax']) ? $order_data['tax'] : 0;
            $shipping       = isset($order_data['shipping']) ? $order_data['shipping'] : 0;

            $general_data   = isset($gmt_info['general_data']) ? $gmt_info['general_data'] : array();

            $data_table = [
                $this->_lang('column_info_order_id')         => isset($gmt_info['order_id']) ? $gmt_info['order_id'] : '-',
                $this->_lang('column_info_valid_status')     => isset($order_data['valid_order_status']) && $order_data['valid_order_status'] ? $this->_lang('text_yes') : $this->_lang('text_no'),
                $this->_lang('column_info_client_id')        => isset($general_data['clientId']) && !empty($general_data['clientId']) ? $general_data['clientId'] : '',
                $this->_lang('column_info_store_id')         => isset($order['store_id']) ? $order['store_id'] : '-',
                $this->_lang('column_info_completed')        => isset($order_data['date_added']) && !empty($order_data['date_added']) ? $order_data['date_added'] : '',
                $this->_lang('column_info_subtotal')         => $this->_format_price_with_currency($subtotal, $order['currency_code']),
                $this->_lang('column_info_tax')              => $this->_format_price_with_currency($tax, $order['currency_code']),
                $this->_lang('column_info_shipping')         => $this->_format_price_with_currency($shipping, $order['currency_code']),
                $this->_lang('column_info_total')            => $this->_format_price_with_currency($total, $order['currency_code']),
                $this->_lang('column_info_payment_method')   => isset($order_data['payment_method']) ? $order_data['payment_method'] : '',
                $this->_lang('column_info_client_name')      => $full_name,
                $this->_lang('column_info_client_email')     => '<a href="mailto:'.$email.'">'.$email.'</a>',
                $this->_lang('column_info_gads')             => isset($general_data['gclDc']) && $general_data['gclDc'] ? $this->_lang('text_yes') : $this->_lang('text_no'),
                $this->_lang('column_info_gcl_dc')           => isset($general_data['gclDc']) && !empty($general_data['gclDc']) ? $general_data['gclDc'] : '',
                $this->_lang('column_info_fb_ads')           => isset($general_data['fbclid']) && $general_data['fbclid'] ? $this->_lang('text_yes') : $this->_lang('text_no'),
                $this->_lang('column_info_fbcl_id')          => isset($general_data['fbclid']) && !empty($general_data['fbclid']) ? $general_data['fbclid'] : '',
                $this->_lang('column_info_bing_ads')         => isset($general_data['msclkid']) && $general_data['msclkid'] ? $this->_lang('text_yes') : $this->_lang('text_no'),
                $this->_lang('column_info_msclk_id')         => isset($general_data['msclkid']) && !empty($general_data['msclkid']) ? $general_data['msclkid'] : '',
                $this->_lang('column_info_consent')          => $this->_can_track_the_conversion($consent) ? $this->_lang('text_yes') : $this->_lang('text_no'),
                $this->_lang('column_info_tracked')          => isset($tracked_info['date']) ? $tracked_info['date'] : '<span class="text-warning">'.$this->_lang('text_not_set').'</span>',
                $this->_lang('column_info_cancelled')        => isset($cancelled_info['date']) ? $cancelled_info['date'] : '<span class="text-warning">'.$this->_lang('text_not_set').'</span>'
            ];

            $html = '<h2 class="text-start" style="font-size: 22px; font-style: normal; color: #000;">'.$this->_lang('text_modal_title').'</h2></span>';
            $html .= '<table class="table table-bordered table-hover table-striped"><tbody>';
                foreach ($data_table as $name => $value) {
                    $html .= '<tr>';
                        $html .= '<td class="left text-left"><b>'.$name.'</b></td>';
                        $html .= '<td class="left text-left">'.$value.'</td>';
                    $html .= '</tr>';
                }
            $html .= '</tbody></table>';

            if (!empty($consent)) {
                $html .= '<div class="left text-left"><span"><h2 class="left text-left" style="font-size: 22px; font-style: normal; color: #000;">'.$this->_lang('text_consent_cookies').'</h2></span>';
                $html .= $this->_pretty_print_json($consent).'</div>';
            }

            $html .= '<div class="left text-left"><span"><h2 class="left text-left" style="font-size: 22px; font-style: normal; color: #000;">'.$this->_lang('text_data').'</h2></span>';
            $html .= $this->_pretty_print_json($gmt_info).'</div>';

            echo $html;
            die;
        }
    }

    /**
     * AJAX action to save the store ID
     * 
     * @return void
     */
    private function _ajax_save_store_id() {
        if (isset($this->request->post['store_id'])) {
            $this->session->data['gmt_selected_store_id'] = (int)$this->request->post['store_id'];

            header('X-Robots-Tag: noindex, nofollow', true);
            header('Content-Type: application/json', true);
            
            echo json_encode(['success' => 'true']);
            die;
        }
    }

    /**
     * AJAX action to export orders to a xlsx file
     * 
     * @return void
     */
    private function _ajax_export_orders() {
        if (isset($this->request->post['export_orders'])) {
            $this->load->model('extension/module/google_all_export_orders');
            $this->model_extension_module_google_all_export_orders->create_file();
            
            $columns = array();
            $theads  = $this->_get_orders_table_head(true);

            foreach ($theads as $column_key) {
                if (!($column_key == " ")) {
                    $columns[$column_key] = array(
                        'custom_name' => $column_key,
                        'bg_color'    => '30c5f0'
                    );
                }
            }

            $this->model_extension_module_google_all_export_orders->create_file();
            $this->model_extension_module_google_all_export_orders->insert_columns($columns);

            $orders = $this->_get_orders_data(true);
            $orders = isset($orders['orders']) ? $orders['orders'] : array();

            if (!empty($orders)) {
                $final_orders = array();

                foreach ($orders as $order) {
                    $final_orders[] = array(
                        $theads[0] => $order[0],
                        $theads[1] => $order[1],
                        $theads[2] => $order[2],
                        $theads[3] => $order[3],
                        $theads[4] => $order[4],
                        $theads[5] => $order[5],
                        $theads[6] => $order[6],
                        $theads[7] => $order[7],
                        $theads[8] => $order[8],
                        $theads[9] => $order[9],
                        $theads[10] => $order[10],
                        $theads[11] => $order[11],
                        $theads[12] => $order[12],
                        $theads[13] => $order[13],
                        $theads[14] => $order[14],
                        $theads[15] => $order[15],
                        $theads[16] => $order[16],
                        $theads[17] => $order[17]
                    ); 
                }
    
                $this->model_extension_module_google_all_export_orders->insert_data($columns, $final_orders);
                
                $download_path = $this->download_path . $this->filename;
                
                echo json_encode(array(
                    'redirect'  => $download_path,
                    'message'   => $this->_lang('text_download')
                ));
            } else {
                $this->writer->close();
                $this->_clean_temp_folder();

                echo json_encode(array(
                    'error' => $this->_lang('text_download_error')
                ));
            }
            
            die;
        }
    }

    
    /**
     * Can track the conversion
     * 
     * Checks if the order can be tracked according to the user's cookie settings.
     *
     * @param array $cookies_config
     * @return bool
     */
    private function _can_track_the_conversion( $cookies_config ) {
        $can_track = true;

        $marketing_cookies = isset($cookies_config['marketing']) && !empty($cookies_config['marketing']) ? $cookies_config['marketing'] : array();

        foreach ($marketing_cookies as $cookie_value) {
            if ($cookie_value !== 'granted') {
                $can_track = false;
            }

            if (!$can_track) {
                break;
            }
        }

        return $can_track;
    }

    /**
     * Get consent config in HTML
     * 
     * Generates an HTML string representing the consent configuration for cookies.
     *
     * @param array $cookies_config An associative array where keys are cookie categories and values are arrays of cookie consent statuses.
     * @return string The generated HTML string with the consent status for each cookie category.
     */
    private function _get_consent_config_html( $cookies_config ) {
        $html = '';
        $categories = array();

        if (empty($cookies_config)) {
            return $this->_lang('text_unknown');
        }

        foreach ($cookies_config as $category => $cookies) {
            $accepted_category = true;

            foreach ($cookies as $cookie_value) {
                if ($cookie_value !== 'granted') {
                    $accepted_category = false;
                }
            }

            $categories[$category] = $accepted_category;
        }
        
        foreach ($categories as $category => $accepted) {
            $accepted = $accepted ? $this->_lang('text_yes') : $this->_lang('text_no');
            $html .= ucwords($category).": {$accepted} </br>";
        }

        return $html;
    }

    /**
     * Get filters form in HTML
     * 
     * @return string
     */
    private function _get_filters_form() {
        $filter = '<div id="gmtFilterOrders">';
            $filter .= '<button class="btn-filter-orders btn btn-primary dropdown-toggle" type="button" id="filterDropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-filter"></i>' . $this->_lang('text_filter_orders') . '</button>';
            $filter .= '<button class="btn-export-orders btn btn-warning" type="button" id="gmtExportOrders"><i class="fa fa-download"></i>'. $this->_lang('text_export_orders') . '</button>';
            
            $filter .= '<div class="dropdown-menu pull-right" aria-labelledby="filterDropdownMenu">';

                /* Marketing Platform Filter */
                $filter .= '<div class="dropdown-item">';
                    $filter .= '<h2 class="filter-menu-title" for="filter_marketing_plataform">' . $this->_lang('text_marketing_platform') . '</h3>';

                    /* Bing Ads */
                    $filter .= '<div class="filter-section">';
                        $filter .= '<h3 class="filter-title">'.$this->_lang('text_bing_ads').'</h3>';

                        $filter .= '<div class="filter-options">';
                            $filter .= '<input type="radio" id="filterBingAdsBoth" name="filter_bing_ads" checked value="filter_both">';
                            $filter .= '<label for="filterBingAdsBoth">' . $this->_lang('text_both') . '</label>';

                            $filter .= '<input type="radio" id="filterBingAdsYes" name="filter_bing_ads" value="filter_yes">';
                            $filter .= '<label for="filterBingAdsYes">' . $this->_lang('text_yes') . '</label>';

                            $filter .= '<input type="radio" id="filterBingAdsNo" name="filter_bing_ads" value="filter_no">';
                            $filter .= '<label for="filterBingAdsNo">' . $this->_lang('text_no') . '</label>';
                        $filter .= '</div>';
                    $filter .= '</div>';

                    /* Google Ads */
                    $filter .= '<div class="filter-section">';
                        $filter .= '<h3 class="filter-title">'.$this->_lang('text_google_ads').'</h4>';

                        $filter .= '<div class="filter-options">';
                            $filter .= '<input type="radio" id="filterGoogleAdsBoth" name="filter_google_ads" checked value="filter_both">';
                            $filter .= '<label for="filterGoogleAdsBoth">' . $this->_lang('text_both') . '</label>';

                            $filter .= '<input type="radio" id="filterGoogleAdsYes" name="filter_google_ads" value="filter_yes">';
                            $filter .= '<label for="filterGoogleAdsYes">' . $this->_lang('text_yes') . '</label>';

                            $filter .= '<input type="radio" id="filterGoogleAdsNo" name="filter_google_ads" value="filter_no">';
                            $filter .= '<label for="filterGoogleAdsNo">' . $this->_lang('text_no') . '</label>';
                        $filter .= '</div>';
                    $filter .= '</div>';

                    /* Facebook Ads */
                    $filter .= '<div class="filter-section">';
                        $filter .= '<h2 class="filter-title">'.$this->_lang('text_facebook_ads').'</h2>';

                        $filter .= '<div class="filter-options">';
                            $filter .= '<input type="radio" id="filterFacebookAdsBoth" name="filter_facebook_ads" checked value="filter_both">';
                            $filter .= '<label for="filterFacebookAdsBoth">' . $this->_lang('text_both') . '</label>';

                            $filter .= '<input type="radio" id="filterFacebookAdsYes" name="filter_facebook_ads" value="filter_yes">';
                            $filter .= '<label for="filterFacebookAdsYes">' . $this->_lang('text_yes') . '</label>';

                            $filter .= '<input type="radio" id="filterFacebookAdsNo" name="filter_facebook_ads" value="filter_no">';
                            $filter .= '<label for="filterFacebookAdsNo">' . $this->_lang('text_no') . '</label>';
                        $filter .= '</div>';
                    $filter .= '</div>';
                $filter .= '</div>';
                
                $filter .= '<hr/>';
                
                /* Marketing Cookies Filter */
                $filter .= '<div class="dropdown-item">';
                    $filter .= '<h2 class="filter-menu-title">' . $this->_lang('text_cookies_marketing') . '</h2>';

                    $filter .= '<div class="filter-options">';
                        $filter .= '<input type="radio" id="filterMarketingCookiesBoth" name="filter_marketing_cookies" checked value="filter_both">';
                        $filter .= '<label for="filterMarketingCookiesBoth">' . $this->_lang('text_both') . '</label>';

                        $filter .= '<input type="radio" id="filterMarketingCookiesYes" name="filter_marketing_cookies" value="filter_yes">';
                        $filter .= '<label for="filterMarketingCookiesYes">' . $this->_lang('text_yes') . '</label>';

                        $filter .= '<input type="radio" id="filterMarketingCookiesNo" name="filter_marketing_cookies" value="filter_no">';
                        $filter .= '<label for="filterMarketingCookiesNo">' . $this->_lang('text_no') . '</label>';
                    $filter .= '</div>';
                $filter .= '</div>';
                    
                $filter .= '<hr/>';

                $filter .= '<div class="dropdown-item filter-date">';
                    $filter .= '<h2 class="filter-menu-title">' . $this->_lang('text_created_date') . '</h2>';

                    $filter .= '<div class="filter-options">';
                        
                        $filter .= '<div class="filter-date-wrap">';
                            $filter .= '<label for="filterDateCreatedFrom">' . $this->_lang('text_from') . '</label>';
                            
                            $filter .= '<div class="input-group date">';
                                $filter .= '<input type="text" name="filter_date_created_from" value="" placeholder="' . $this->_lang('text_from') . '" data-date-format="YYYY-MM-DD" id="filterDateCreatedFrom" class="form-control" />';
                                
                                $filter .= '<span class="input-group-btn">';
                                    $filter .= '<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>';
                                $filter .= '</span>';
                            $filter .= '</div>';
                        $filter .= '</div>';

                        $filter .= '<div class="filter-date-wrap">';
                            $filter .= '<label for="filterDateCreatedTo">' . $this->_lang('text_to') . '</label>';
                            
                            $filter .= '<div class="input-group date">';
                                $filter .= '<input type="text" name="filter_date_created_to" value="" placeholder="' . $this->_lang('text_to') . '" data-date-format="YYYY-MM-DD" id="filterDateCreatedTo" class="form-control" />';
                                
                                $filter .= '<span class="input-group-btn">';
                                    $filter .= '<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>';
                                $filter .= '</span>';
                            $filter .= '</div>';
                        $filter .= '</div>';

                    $filter.= '</div>';
                $filter .= '</div>';

                $filter .= '<hr/>';

                /* Filter Button */
                $filter .= '<div class="dropdown-item dropdown-footer">';
                    $filter .= '<button class="btn btn-primary" id="btn-order-filter"><i class="fa fa-filter"></i>'. $this->_lang('text_filter') . '</button>';
                $filter .= '</div>';
            $filter .= '</div>';
        $filter .= '</div>';

        return $filter;
    }

    /**
     * Get download message popup
     * 
     * @return string
     */
    private function _get_popup_download_message() {
        return '<div class="modal" tabindex="-1" role="dialog" id="messageModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">' . $this->_lang('text_message') . '</h5>
                    </div>
                    <div class="modal-body">
                    <p id="message" style="display:inline-block;"></p> 
                    <button type="button" data-dismiss="modal" class="btn btn-danger pull-right" aria-label="Close">' . $this->_lang('text_popup_button') .'</button>
                    </div>
                </div>
            </div>
        </div>';
    }

    /**
     * Get orders data
     * 
     * @param bool $is_export
     * @return array
     */
    private function _get_orders_data( $is_export = false ) {
        $data   = array();
        $orders = $this->_get_orders();

        if (empty($orders['orders'])) {
            return array();
        }

        $data['total_pages'] = $orders['total_orders'] > 0 ? ceil($orders['total_orders'] / $this->items_per_page) : 1;

        foreach ($orders['orders'] as $order) {
            $buttons            = '';
            $order_id           = (int)$order['order_id'];
            $total_formatted    = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value']);
            $tracked_info       = isset($order['tracked_info']) && !empty($order['tracked_info']) ? json_decode($order['tracked_info'], true) : array();
            $cancelled_info     = isset($order['cancelled_info']) && !empty($order['cancelled_info']) ? json_decode($order['cancelled_info'], true) : array();

            // Consent column text
            $consent        = isset($order['gmt_consent']) && !empty($order['gmt_consent']) ? json_decode($order['gmt_consent'], true) : array();
            $consent_info   = '<span class="consent-info">%s<i class="fa fa-info-circle" data-toggle="tooltip" data-html="true" title="'.$this->_get_consent_config_html($consent).'"></i></span>';
            
            if (!empty($consent)) {
                if ($this->_can_track_the_conversion($consent)) {
                    $column_consent_text = isset($this->request->post['export_orders']) ? $this->_lang('text_yes') : sprintf($consent_info, $this->_lang('text_yes'));
                } else {
                    $column_consent_text = isset($this->request->post['export_orders']) ? $this->_lang('text_no') : sprintf($consent_info, $this->_lang('text_no'));
                }
            } else {
                $column_consent_text = isset($this->request->post['export_orders']) ? $this->_lang('text_not_set') : sprintf($consent_info, '-');
            }

            if (isset($order['gmt_info']) && !empty($order['gmt_info'])) {
                $order['gmt_info'] = $this->_build_datalayer_from_gmt_info($order);

                $gmt_info           = $order['gmt_info'];
                $is_order_modified  = false;
                $current_currency   = isset($gmt_info['general_data']['current_currency']) ? $gmt_info['general_data']['current_currency'] : '';
                $current_price      = (float)$this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false);
                $original_price     = isset($gmt_info['original_order_data']['order']['total']) ? (float)$gmt_info['original_order_data']['order']['total'] : 0;
                
                /**
                 * Check if the order was modified.
                 * If original_order_data is not defined in gmt_info then the version is < 13.10.0 or there is no original data saved
                 */                
                if (isset($gmt_info['original_order_data']) && $current_price !== $original_price) {
                    $is_order_modified = true;
                    
                    $current_price  = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false);
                    $original_price = $this->_format_price_with_currency($original_price, $current_currency);
                }
                
                // Send menssage confirmation
                $sent_message = '';

                if (empty($tracked_info)) {
                    if ($is_order_modified) {
                        $sent_message = sprintf($this->_lang('text_sent_modified_order'), $order_id, $total_formatted, $original_price, $this->_lang('text_consent_notice'));
                    } else {
                        $sent_message = sprintf($this->_lang('text_sent_order'), $order_id, $total_formatted, $this->_lang('text_consent_notice'));
                    }
                } else {
                    $tracked_order_total = $this->_format_price_with_currency($tracked_info['order_total'], $tracked_info['currency_code']);

                    if ($is_order_modified) {
                        $sent_message = sprintf($this->_lang('text_sent_modified_order_again'), $order_id, $total_formatted, $original_price, $tracked_order_total, $tracked_info['date'], $this->_lang('text_consent_notice'));
                    } else {
                        $sent_message = sprintf($this->_lang('text_sent_order_again'), $order_id, $total_formatted, $tracked_info['date'], $this->_lang('text_consent_notice'));
                    }
                }

                // Cancel message confirmation
                $cancel_message = '';
                
                if (empty($cancelled_info)) {
                    if ($is_order_modified) {
                        $cancel_message = sprintf($this->_lang('text_cancel_modified_order'), $order_id, $total_formatted, $original_price, $this->_lang('text_consent_notice'));
                    } else {
                        $cancel_message = sprintf($this->_lang('text_cancel_order'), $order_id, $total_formatted, $this->_lang('text_consent_notice'));
                    }
                } else {
                    $cancelled_order_total = $this->_format_price_with_currency(abs($cancelled_info['order_total']), $cancelled_info['currency_code']);

                    if ($is_order_modified) {
                        $cancel_message = sprintf($this->_lang('text_cancel_modified_order_again'), $order_id, $total_formatted, $original_price, $cancelled_order_total, $cancelled_info['date'], $this->_lang('text_consent_notice'));
                    } else {
                        $cancel_message = sprintf($this->_lang('text_cancel_order_again'), $order_id, $total_formatted, $cancelled_info['date'], $this->_lang('text_consent_notice'));
                    }
                }

                $buttons .= '<div class="order-actions">';
                    $buttons .= '<button class="btn btn-primary btn-send-order" data-order-id="'.$order_id.'" data-is-modified="'.(int)$is_order_modified.'" data-message="'.$sent_message.'"><i class="fa fa-send"></i></button>';
                    $buttons .= '<button class="btn btn-warning btn-cancel-order" data-order-id="'.$order_id.'" data-is-modified="'.(int)$is_order_modified.'" data-message="'.$cancel_message.'"><i class="fa fa-minus"></i></button>';
                    $buttons .= '<button data-remodal-target="gmt-order-info" class="btn btn-info btn-order-info" data-order-id="'.$order_id.'"><i class="fa fa-info"></i></button>';
                $buttons .= '</div>';
            } else {
                $order['gmt_info'] = array();

                $buttons .= '<div class="order-actions">';
                    $buttons .= $this->_lang('no_info');
                $buttons .= '</div>';
            }

            $column_gmt_tracked         = isset($tracked_info['date']) ? $tracked_info['date'] : $this->_lang('text_not_set');
            $column_gmt_cancelled       = isset($cancelled_info['date']) ? $cancelled_info['date'] : $this->_lang('text_not_set');

            $column_gmt_tracked_class   = !isset($tracked_info['date']) ? 'text-warning' : '';
            $column_gmt_cancelled_class = !isset($cancelled_info['date']) ? 'text-warning' : '';

            $gmt_order_data     = isset($order['gmt_info']['order_data']) ? $order['gmt_info']['order_data'] : array();
            $gmt_general_data   = isset($order['gmt_info']['general_data']) ? $order['gmt_info']['general_data'] : array();

            $total      = isset($gmt_order_data['order']['total']) ? $gmt_order_data['order']['total'] : 0;
            $subtotal   = isset($gmt_order_data['order']['subtotal']) ? $gmt_order_data['order']['subtotal'] : 0;
            $tax        = isset($gmt_order_data['order']['tax']) ? $gmt_order_data['order']['tax'] : 0;
            $shipping   = isset($gmt_order_data['order']['shipping']) ? $gmt_order_data['order']['shipping'] : 0;
            
            $temp_order = array(
                $order['order_id'],
                isset($gmt_order_data['order']['valid_order_status']) && $gmt_order_data['order']['valid_order_status'] ? $this->_lang('text_yes') : $this->_lang('text_no'),
                $order['date_added']
            );
            
            if ($is_export) {
                $temp_order[] = isset($gmt_general_data['clientId']) ? $gmt_general_data['clientId'] : '';
            }

            $temp_order[] = $this->_format_price_with_currency($subtotal, $order['currency_code']);
            $temp_order[] = $this->_format_price_with_currency($tax, $order['currency_code']);
            $temp_order[] = $this->_format_price_with_currency($shipping, $order['currency_code']);
            $temp_order[] = $this->_format_price_with_currency($total, $order['currency_code']);
            $temp_order[] = $order['payment_method'];
            $temp_order[] = isset($gmt_general_data['gclDc']) && $gmt_general_data['gclDc'] ? $this->_lang('text_yes') : $this->_lang('text_no');

            if ($is_export) {
                $temp_order[] = isset($gmt_general_data['gclDc']) && !empty($gmt_general_data['gclDc']) ? $gmt_general_data['gclDc'] : '';
            }

            $temp_order[] = isset($gmt_general_data['fbclid']) && $gmt_general_data['fbclid'] ? $this->_lang('text_yes') : $this->_lang('text_no');
            
            if ($is_export) {
                $temp_order[] = isset($gmt_general_data['fbclid']) && !empty($gmt_general_data['fbclid']) ? $gmt_general_data['fbclid'] : '';
            }

            $temp_order[] = isset($gmt_general_data['msclkid']) && $gmt_general_data['msclkid'] ? $this->_lang('text_yes') : $this->_lang('text_no');
            
            if ($is_export) {
                $temp_order[] = isset($gmt_general_data['msclkid']) && !empty($gmt_general_data['msclkid']) ? $gmt_general_data['msclkid'] : '';
            }

            $temp_order[] = $column_consent_text;
            $temp_order[] = isset($this->request->post['export_orders']) ? $column_gmt_tracked : '<span id="sent_'.$order_id.'" class="'.$column_gmt_tracked_class.'">'.$column_gmt_tracked.'</span>';
            $temp_order[] = isset($this->request->post['export_orders']) ? $column_gmt_cancelled : '<span id="cancel_'.$order_id.'" class="'.$column_gmt_cancelled_class.'">'.$column_gmt_cancelled.'</span>';
            $temp_order[] = $buttons;


            $data['orders'][] = $temp_order;
        }

        return $data;
    }

    /**
     * Get orders
     * 
     * @return array
     */
    private function _get_orders() {
        $result = array();
        
        $this->load->model('sale/order');

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }

        $filter_data = array(
            'start' => ($page - 1) * $this->items_per_page,
            'limit' => $this->items_per_page,
        );

        if (isset($this->session->data['gmt_selected_store_id'])) {
            $filter_data['filter_store_id'] = $this->session->data['gmt_selected_store_id'];
        }

        $sql = "SELECT o.*, og.ga_client_id, og.consent as gmt_consent, og.gmt_data as gmt_info, og.tracked_info, og.cancelled_info FROM " . $this->escape_database_table_name('order') . " o LEFT JOIN " . $this->escape_database_table_name('order_google_all') . " og ON (o.order_id = og.order_id)";

        if (isset($filter_data['filter_store_id']) && $filter_data['filter_store_id'] !== '') {
			$sql .= " WHERE o.store_id = '" . (int)$filter_data['filter_store_id'] . "' AND o.order_status_id > 0";
		} else {
            $sql .= " WHERE o.store_id = 0 AND o.order_status_id > 0";
        }

        // Filter Platform Marketing
        if (isset($this->request->post['filter_marketing_plataform']) && is_array($this->request->post['filter_marketing_plataform'])) {
            $filter_marketing_plataform = $this->request->post['filter_marketing_plataform'];

            if (isset($filter_marketing_plataform['filter_google_ads'])) {
                if ($filter_marketing_plataform['filter_google_ads'] === 'filter_yes' ) {
                    $filter_data['filter_google_ads'] = " AND og.gmt_data LIKE '%\"gclDc\":%' AND og.gmt_data NOT LIKE '%\"gclDc\":\"\"%'";
                } elseif ($filter_marketing_plataform['filter_google_ads'] === 'filter_no') {
                    $filter_data['filter_google_ads'] = " AND (og.gmt_data NOT LIKE '%\"gclDc\":%' OR og.gmt_data LIKE '%\"gclDc\":\"\"%')";
                }
            }
    
            if (isset($filter_marketing_plataform['filter_facebook_ads'])) {
                if ($filter_marketing_plataform['filter_facebook_ads'] === 'filter_yes' ) {
                    $filter_data['filter_facebook_ads'] = " AND og.gmt_data LIKE '%\"fbclid\":%' AND og.gmt_data NOT LIKE '%\"fbclid\":\"\"%'";
                } elseif ($filter_marketing_plataform['filter_facebook_ads'] === 'filter_no') {
                    $filter_data['filter_facebook_ads'] = " AND (og.gmt_data NOT LIKE '%\"fbclid\":%' OR og.gmt_data LIKE '%\"fbclid\":\"\"%')";
                }
            }

            if (isset($filter_marketing_plataform['filter_bing_ads'])) {
                if ($filter_marketing_plataform['filter_bing_ads'] === 'filter_yes' ) {
                    $filter_data['filter_bing_ads'] = " AND og.gmt_data LIKE '%\"msclkid\":%' AND og.gmt_data NOT LIKE '%\"msclkid\":\"\"%'";
                } elseif ($filter_marketing_plataform['filter_bing_ads'] === 'filter_no') {
                    $filter_data['filter_bing_ads'] = " AND (og.gmt_data NOT LIKE '%\"msclkid\":%' OR og.gmt_data LIKE '%\"msclkid\":\"\"%')";
                }
            }
        }

        // Filter Marketing Cookies
        if (isset($this->request->post['filter_marketing_cookies']) && $this->request->post['filter_marketing_cookies'] !== 'filter_both') {
            $filter_marketing_cookies = $this->request->post['filter_marketing_cookies'];
            
            if ($filter_marketing_cookies === 'filter_yes') {
                $filter_data['filter_gmt_consent'] = " AND (og.consent LIKE '%\"ad_storage\":\"granted\"%' AND og.consent LIKE '%\"ad_user_data\":\"granted\"%' AND og.consent LIKE '%\"ad_personalization\":\"granted\"%')";
            } elseif ($filter_marketing_cookies === 'filter_no') {
                $filter_data['filter_gmt_consent'] = " AND (og.consent IS NULL OR og.consent = '' OR og.consent LIKE '%\"ad_storage\":\"denied\"%' OR og.consent LIKE '%\"ad_user_data\":\"denied\"%' OR og.consent LIKE '%\"ad_personalization\":\"denied\"%')";
            }
        }

        // Filter by Date created
        if (isset($this->request->post['filter_date_created']) && is_array($this->request->post['filter_date_created'])) {
            $filter_date_created = $this->request->post['filter_date_created'];

            if (isset($filter_date_created['from']) && !empty($filter_date_created['from'])) {
                $filter_data['filter_date_created_from'] = " AND o.date_added >= '" . $filter_date_created['from'] . " 00:00:00'";
            }

            if (isset($filter_date_created['to']) && !empty($filter_date_created['to'])) {
                $filter_data['filter_date_created_to'] = " AND o.date_added <= '" . $filter_date_created['to'] . " 23:59:59'";
            }
        }

        if (isset($filter_data['filter_bing_ads'])) {
            $sql .= $filter_data['filter_bing_ads'];
        }

        if (isset($filter_data['filter_google_ads'])) {
            $sql .= $filter_data['filter_google_ads'];
        }

        if (isset($filter_data['filter_facebook_ads'])) {
            $sql .= $filter_data['filter_facebook_ads'];
        }
        
        if (isset($filter_data['filter_gmt_consent'])) {
            $sql .= $filter_data['filter_gmt_consent'];
        }

        if (isset($filter_data['filter_date_created_from'])) {
            $sql .= $filter_data['filter_date_created_from'];
        }

        if (isset($filter_data['filter_date_created_to'])) {
            $sql .= $filter_data['filter_date_created_to'];
        }
        
        $sql .= " ORDER BY order_id DESC";

        if (!isset($this->request->post['export_orders']) && (isset($filter_data['start']) || isset($filter_data['limit']))) {
            if ($filter_data['start'] < 0) {
                $filter_data['start'] = 0;
            }

            if ($filter_data['limit'] < 1) {
                $filter_data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
        }
        
        $query = $this->db->query($sql);

        $result['orders']       = $query->rows;
        $result['total_orders'] = $this->_getTotalOrders($filter_data);

        return $result;
    }

    /**
     * Get order table pagination in HTML
     * 
     * @param int $total_pages
     * @return string
     */
    private function _get_pagination( $total_pages = 1 ) {
        $html = '<ul id="gmtOrdersPagination" class="gmt-pagination">';
            for ($i = 1; $total_pages > 1 && $i <= $total_pages && $i <= 10; $i++) {
                $page_item_class = $i === 1 ? 'gmt-page-item active' : 'gmt-page-item';
                $html .= '<li class="'.$page_item_class.'"><button class="gmt-page-link" data-page="'.$i.'">'.$i.'</button></li>';
            }
        $html .= '</ul>';

        return $html;
    }

    /**
     * Pretty print json
     * 
     * @param array $data
     * @return string
     */
    private function _pretty_print_json( $data ) {
        // Convert to JSON
        array_walk_recursive($data, function (&$value) {
            if (is_string($value)) {
                $value = htmlspecialchars($value);
            }
        });

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
        $json = preg_replace('/"([^"]+)"\s*:\s*/', '<span style="color: brown;">"$1"</span>:', $json);
        $json = preg_replace('/:\s*"([^"]+)"/', ': <span style="color: green;">"$1"</span>', $json);
        $json = preg_replace('/:\s*(true|false)/', ': <span style="color: red;">$1</span>', $json);
        $json = preg_replace('/:\s*(null)/', ': <span style="color: magenta;">$1</span>', $json);
        $json = preg_replace('/:\s*(-?[0-9\.]+)(?=(\s|,|}|$))/m', ': <span style="color: darkorange;">$1</span>', $json);
        $json = str_replace('{', '<span style="color: blue;">{</span>', $json);
        $json = str_replace('}', '<span style="color: blue;">}</span>', $json);
        $json = str_replace('[', '<span style="color: purple;">[</span>', $json);
        $json = str_replace(']', '<span style="color: purple;">]</span>', $json);
    
        return '<pre style="background-color: #f9f9f9; padding: 10px; border-radius: 5px; border: 1px solid #dee2e6;"><code>' . $json . '</code></pre>';
    }

    /**
     * Clean the export temp folder
     * 
     * @return void
     */
    private function _clean_temp_folder() {
        $files = glob($this->path_tmp.'*');

        foreach ($files as $file){
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    /**
     * Get GTM ID from the selected store
     * 
     * @return array
     */
    public function get_gtm_id() {
        $selected_store_id = isset($this->session->data['gmt_selected_store_id']) ? (int)$this->session->data['gmt_selected_store_id'] : 0;
        $container_id = $this->config->get('google_all_container_id_' . $selected_store_id) !== null ? $this->config->get('google_all_container_id_' . $selected_store_id) : '';
        $gtm_id = explode('|', str_replace(' ', '', $container_id));
        
        return $gtm_id;
    }

    /**
     * Get GMT html code to the header
     * 
     * @return string
     */
    public function get_code_head() {
        $gtm_ids = $this->get_gtm_id();

        $code = $this->get_generic_js_variables();

        foreach ($gtm_ids as $gtm) {
            $code .= "<script type=\"text/javascript\" nitro-exclude=\"\">(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','$gtm');</script>";
        }

        return $code;
    }

    /**
     * Get GMT html code to the body
     * 
     * @return string
     */
    public function get_code_body() {
        $gtm_ids = $this->get_gtm_id();
        $code = '';

        foreach ($gtm_ids as $gtm) {
            $code = "<noscript><iframe src=\"https://www.googletagmanager.com/ns.html?id=$gtm\"
                height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>";
        }

        return $code;
    }

    /**
     * Get GMT generic JS variables
     * 
     * @return string
     */
    public function get_generic_js_variables() {
        $i18n_texts = json_encode($this->_get_translated_texts());
        $admin_url  = version_compare(VERSION, '2.3', '<') ? HTTPS_SERVER.'index.php?route=module/google_all' : HTTPS_SERVER.'index.php?route=extension/module/google_all';

        $code = "<script>
            // Google Marketing Tools - Declare basic variables
            var dataLayer = [];
            var isGmtAdmin = ".(defined('HTTP_CATALOG') ? 'true' : 'false').";
            var trackApiUrl = '".HTTPS_CATALOG . 'index.php?route=extension/module/gmt_datalayer/save_order_tracking_status'."';
            var callAddonsUrl = '".HTTPS_CATALOG . 'index.php?route=extension/module/gmt_datalayer/addons'."';
            var facebookTrackUrl = '".HTTPS_CATALOG . 'index.php?route=extension/module/gmt_facebook/track'."';
            var gmtI18n = ".$i18n_texts.";
        </script>";

        return $code;
    }

    /**
     * Get translated texts
     * 
     * @return string
     */
    private function _get_translated_texts() {
        return array(
            'textYes'                   => $this->_lang('text_yes'),
            'textNo'                    => $this->_lang('text_no'),
            'textYesSentOriginalOrder'  => $this->language->get('text_yes_sent_original_order'),
            'textYesSentCurrentOrder'   => $this->language->get('text_yes_sent_current_order'),
            'textError'                 => $this->_lang('text_error'),
            'textSuccess'               => $this->_lang('text_success_2'),
            'textConversionSent'        => $this->_lang('text_conversion_sent'),
            'textConversionCancelled'   => $this->_lang('text_conversion_cancelled'),
            'textSendConversion'        => $this->_lang('text_send_conversion'),
            'textCancelConversion'      => $this->_lang('text_cancel_conversion'),
            'textFilterOrders'          => $this->_lang('text_filter_orders'),
            'textExportOrders'          => $this->_lang('text_export_orders')
        );
    }

    /**
     * Create order_google_all table
     * 
     * @return void
     */
    public function createOrderGoogleAllTable() {
        $this->db->query("CREATE TABLE IF NOT EXISTS ".$this->escape_database_table_name('order_google_all')." (
        ".$this->escape_database_field('id')." int(11) unsigned NOT NULL AUTO_INCREMENT,
        ".$this->escape_database_field('order_id')." int(11) NOT NULL,
        ".$this->escape_database_field('ga_client_id')." varchar(64) NULL,
        ".$this->escape_database_field('consent')." TEXT NULL,
        ".$this->escape_database_field('gmt_data')." TEXT NULL,
        ".$this->escape_database_field('tracked_info')." TEXT NULL,
        ".$this->escape_database_field('cancelled_info')." TEXT NULL,
        PRIMARY KEY (".$this->escape_database_field('id').")        
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->createIndexInOrderGoogleAllTable();
    }

    /**
     * Create index in order_google_all table
     * 
     * Used in the migration process from the main admin controller
     * 
     * @return void
     */
    public function createIndexInOrderGoogleAllTable() {
        $index_query = $this->db->query("SHOW INDEX FROM ".$this->escape_database_table_name('order_google_all')." WHERE Key_name = 'idx_order_id';");

        if (!$index_query->row) {
            $this->db->query("ALTER TABLE ".$this->escape_database_table_name('order_google_all')." ADD INDEX `idx_order_id` (`order_id`);");
        }
    }

    /**
     * Add order data to the order_google_all table 
     *
     * Used in the migration process from the main admin controller
     * 
     * @param int $order_id
     * @param array $data
     * @return int
     */
    public function addOrderData( $order_id, $data = array() ) {
        $this->db->query("INSERT INTO ".$this->escape_database_table_name('order_google_all')." SET `order_id` = '" . (int)$order_id."', `ga_client_id` = '" . $this->db->escape($data['ga_client_id']) . "', `consent` = '" . $this->db->escape($data['consent'] ? json_encode($data['consent']) : '') . "', `gmt_data` = '" . $this->db->escape($data['gmt_data'] ? json_encode($data['gmt_data']) : '') . "', `tracked_info` = '" . $this->db->escape($data['tracked_info'] ? json_encode($data['tracked_info']) : '') . "', `cancelled_info` = '" . $this->db->escape($data['cancelled_info'] ? json_encode($data['cancelled_info']) : '') . "' ");

        $order_google_all_id = $this->db->getLastId();

        return $order_google_all_id;
    }

    /**
     * Delete GMT columns from the order table
     * 
     * Used in the migration process from the main admin controller
     * 
     * @return void
     */
    public function deleteGmtColumnsFromOrderTable() {
        $this->db->query("ALTER TABLE ".$this->escape_database_table_name('order')." DROP COLUMN `ga_client_id`, DROP COLUMN `gmt_consent`, DROP COLUMN `gmt_info`, DROP COLUMN `gmt_tracked`, DROP COLUMN `gmt_cancelled`");
    }

    /**
     * Get GMT data from the order table to migrate
     * 
     * Used in the migration process from the main controller
     * 
     * @return array
     */
    public function getGmtDataFromOrderToMigrate() {
        $query = $this->db->query("SELECT order_id, total, currency_code, currency_value, ga_client_id, gmt_consent, gmt_info, gmt_tracked, gmt_cancelled FROM ".$this->escape_database_table_name('order')." ORDER BY order_id ASC");

        return $query->rows;
    }

    /**
     * Get the total of orders
     * 
     * @param array $data
     * @return int
     */
    private function _getTotalOrders( $data = array() ) {
		$sql = "SELECT COUNT(*) AS total FROM " . $this->escape_database_table_name('order') . " o LEFT JOIN " . $this->escape_database_table_name('order_google_all') . " og ON (o.order_id = og.order_id)";

		if (!empty($data['filter_order_status'])) {
			$implode = array();

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}
		} elseif (isset($data['filter_order_status_id']) && $data['filter_order_status_id'] !== '') {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

        if (isset($data['filter_store_id']) && $data['filter_store_id'] !== '') {
			$sql .= " AND o.store_id = '" . (int)$data['filter_store_id'] . "'";
		} else {
            $sql .= " AND o.store_id = '0'";
        }

		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(o.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if (!empty($data['filter_total'])) {
			$sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
		}

        if (!empty($data['filter_total'])) {
			$sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
		}

        if (isset($data['filter_gmt_consent'])) {
            $sql .= $data['filter_gmt_consent'];
        }

        if (isset($data['filter_bing_ads'])) {
            $sql .= $data['filter_bing_ads'];
        }

        if (isset($data['filter_google_ads'])) {
            $sql .= $data['filter_google_ads'];
        }

        if (isset($data['filter_facebook_ads'])) {
            $sql .= $data['filter_facebook_ads'];
        }

        if (isset($data['filter_date_created_from'])) {
            $sql .= $data['filter_date_created_from'];
        }

        if (isset($data['filter_date_created_to'])) {
            $sql .= $data['filter_date_created_to'];
        }

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

    /**
     * Get gmt info from order data
     * 
     * @param int $order_id
     * @return array
     */
    private function _get_gmt_info( $order_id ) {
        $query = $this->db->query("SELECT gmt_data as gmt_info, consent as gmt_consent FROM " . $this->escape_database_table_name('order_google_all') . " WHERE `order_id` = '" . (int)$order_id . "'");

        return $query->row;
    }

    /**
     * Get GMT info for order detail modal
     * 
     * @param int $order_id
     * @return array
     */
    private function _get_gmt_info_for_remodal( $order_id ) {
        $query = $this->db->query("SELECT o.order_id, o.store_id, o.currency_code, o.currency_value, og.consent as gmt_consent, og.gmt_data as gmt_info, og.tracked_info, og.cancelled_info FROM " . $this->escape_database_table_name('order') . " o LEFT JOIN " . $this->escape_database_table_name('order_google_all') . " og ON (o.order_id = og.order_id) WHERE o.order_id = '" . (int)$order_id . "'");

        return $query->row;
    }

    /**
     * Get order
     * 
     * @param int $order_id
     * @return array
     */
    private function _get_order( $order_id ) {
		$query = $this->db->query("SELECT o.*, og.ga_client_id, og.consent as gmt_consent, og.gmt_data as gmt_info, og.tracked_info, og.cancelled_info FROM " . $this->escape_database_table_name('order') ." o LEFT JOIN " . $this->escape_database_table_name('order_google_all'). " og ON (o.order_id = og.order_id) WHERE o.order_id = '".(int)$order_id."'");

        return $query->row;
    }

    /**
     * Get product tax class ID
     * 
     * @param int $product_id
     * @return int
     */
    private function _get_product_tax_class_id( $product_id ) {
        if (!$product_id) {
            return 0;
        }
        
        $sql = "SELECT pr.tax_class_id FROM `".$this->db_prefix."product` pr WHERE pr.product_id = ".$product_id;
        $results = $this->db->query($sql);

        return !empty($results->row['tax_class_id']) ? (int)$results->row['tax_class_id'] : 0;
    }

    /**
     * Get product manufacturer
     * 
     * @param int $product_id
     * @return string
     */
    private function _get_product_manufacturer( $product_id ) {
        if (!$product_id) {
            return '';
        }

        $sql = "SELECT ma.name FROM `".$this->db_prefix."product` pr LEFT JOIN `".$this->db_prefix."manufacturer` ma ON (pr.manufacturer_id = ma.manufacturer_id) WHERE pr.product_id = ".$product_id;
        $results = $this->db->query($sql);

        return !empty($results->row['name']) ? $results->row['name'] : '';
    }

    /**
     * Get order product variant
     * 
     * @param int $order_id
     * @param int $product_id
     * @return string
     */
    private function _get_order_product_variant( $order_id, $product_id ) {
        $variant = '';

        $options = $this->db->query("SELECT ord_opti.* FROM `".$this->db_prefix."order_option` ord_opti INNER JOIN `".$this->db_prefix."order_product` ord_prod ON (ord_prod.product_id = ".$product_id." AND ord_prod.order_product_id = ord_opti.order_product_id) WHERE ord_opti.order_id = ".$order_id);
        
        $count_rows = count($options->rows);

        foreach ($options->rows as $key => $opt) {
            $variant .= $opt['name'] . ': ' . $opt['value'];

            if ($key + 1 < $count_rows) {
                $variant .= ' - ';
            }
        }

        return $variant;
    }

    /**
     * Get product data
     * 
     * @param int $product_id
     * @param array $data
     * @return array
     */
    private function _get_product_data( $product_id, $data ) {
        if( empty($product_id) || empty($data) || (count($data) == 1 && empty($data[0])) ) {
            return array();
        }

        $data = implode("` , `", $data);
        
        $sql = "SELECT `{$data}` FROM `".$this->db_prefix."product` WHERE product_id = ".$product_id;
        $result = $this->db->query($sql);
        
        $result = !empty($result->row) ? $result->row : array();
        
        return $result;
    }

    /**
     * Build datalayer from GMT Info
     * 
     * @param array $order_info
     * @param bool $only_original_data 
     * @return array
     */
    private function _build_datalayer_from_gmt_info( $order_info, $only_original_data = false ) {
        $datalayer          = array();
        $gmt_info           = isset($order_info['gmt_info']) && !empty($order_info['gmt_info']) ? json_decode($order_info['gmt_info'], true) : array();
        $gmt_version        = isset($gmt_info['gmt_version']) && !empty($gmt_info['gmt_version']) ? $gmt_info['gmt_version'] : '0.0.0';
        $selected_store_id  = isset($this->session->data['gmt_selected_store_id']) ? (int)$this->session->data['gmt_selected_store_id'] : 0;
        
        // In versions prior to 13.4.0, all the datalayer information was stored in gmt_info.
        if (version_compare($gmt_version, '13.4.0', '<=')) {
            return $gmt_info;
        }
       
        // Since version 13.10.0, the data originally used to create the order is once again stored in gmt_info
        $original_order_data = array();
        $order_data = $this->_build_purchase_data_for_datalayer($order_info);

        if (version_compare($gmt_version, '13.10.0', '>=')) {
            $saved_order_data = isset($gmt_info['order_data']) && is_array($gmt_info['order_data']) ? $gmt_info['order_data'] : array();
            
            if (isset($saved_order_data['order']) && isset($saved_order_data['products'])) {
                $original_order_data = array_merge($order_data, $saved_order_data);
            }
        }

        // General Data
        $datalayer['general_data'] = array(
            'current_view'          => 'purchase',
            'current_list'          => 'purchase',
            'current_currency'      => isset($order_info['currency_code']) ? $order_info['currency_code'] : '',
            'current_categories'    => array(),
            'store_name'            => isset($order_info['store_name']) ? $order_info['store_name'] : '',
            'userId'                => isset($gmt_info['userId']) ? $gmt_info['userId'] : 0,
            'gclDc'                 => isset($gmt_info['gclDc']) ? $gmt_info['gclDc'] : '',
            'fbclid'                => isset($gmt_info['fbclid']) ? $gmt_info['fbclid'] : '',
            'msclkid'               => isset($gmt_info['msclkid']) ? $gmt_info['msclkid'] : '',
            'string_searched'       => '',
            'cart_products'         => array(),
            'debug_enabled'         => (bool)$this->config->get('google_all_debug_mode_'.$selected_store_id),
            'event_id'              => isset($gmt_info['event_id']) ? $gmt_info['event_id'] : '',
            'clientId'              => isset($gmt_info['client_id']) ? $gmt_info['client_id'] : '',
            'coupon'                => isset($this->session->data['coupon']) ? $this->session->data['coupon'] : '',
            'email'                 => isset($order_info['email']) ? $order_info['email'] : ''
        );

        // Order ID
        $datalayer['order_id'] = $gmt_info['order_id'];

        // TikTok
        $datalayer['tiktok_identify'] = isset($order_data['customer']) && isset($order_data['customer']['tiktok_identify']) ? $order_data['customer']['tiktok_identify'] : array();

        // Order Data
        $datalayer['order_data'] = $only_original_data && !empty($original_order_data) ? $original_order_data : $order_data;

        // Original Order Data
        if (!$only_original_data && version_compare($gmt_version, '13.10.0', '>=') && !empty($original_order_data)) {
            $datalayer['original_order_data'] = $original_order_data;
        }

        // Facebook API
        $quantity = 0;
        $content_ids = array();

        foreach ($order_data['products'] as $pro) {
            $content_ids[] = $this->_get_real_product_identificator($pro);
            $quantity += $pro['quantity'];
        }

        $email = !empty($order_data['customer']['general']['email']) ? $order_data['customer']['general']['email'] : '';
        $phone = !empty($order_data['customer']['general']['phone']) ? $order_data['customer']['general']['phone'] : '';

        $datalayer['fb_pixel_track_info'] = [
            'event_name'        => 'Purchase',
            'content_type'      => count($order_data['products']) > 1 ? 'product_group' : 'product',
            'currency'          => isset($order_info['currency_code']) ? $order_info['currency_code'] : '',
            'value'             => $order_data['order']['total'],
            'tax'               => $order_data['order']['tax'],
            'shipping_charge'   => $order_data['order']['shipping'],
            'content_ids'       => $content_ids,
            'num_items'         => $quantity,
            'contents'          => $this->_get_contents_array($order_data['products']),
            'extra_info'        => array(
                'email' => $email,
                'phone' => $phone
            ),
            'event_source_url'  => isset($gmt_info['fb_event_source_url']) ? $gmt_info['fb_event_source_url'] : '',
            'external_id'       => isset($gmt_info['fb_external_id']) ? $gmt_info['fb_external_id'] : '',
            'event_id'          => isset($gmt_info['fb_event_id']) ? $gmt_info['fb_event_id'] : ''
        ];
        
        return $datalayer;
    }

    /**
     * Build purchase data for datalayer
     * 
     * @param array $order_info
     * @param bool $order
     * @param bool $products
     * @param bool $customer
     * @return array
     */
    private function _build_purchase_data_for_datalayer( $order_info, $order = true, $products = true, $customer = true ) {
        $data       = array();
        $gmt_info   = isset($order_info['gmt_info']) && !empty($order_info['gmt_info']) ? json_decode($order_info['gmt_info'], true) : array();

        $this->load->model('sale/order');

        if (!is_array($order_info) || !isset($order_info['order_id'])) {
            return $data;
        }

        $order_id = (int)$order_info['order_id'];
        
        if ($order) {
            $order_totals       = $this->model_sale_order->getOrderTotals($order_id);
            $valid_order_status = $this->_validate_order_status($order_info['order_status_id']);

            // Format totals
            $shipping       = 0;
            $tax            = 0;
            $subtotal       = 0;
            $coupon         = '';
            $coupon_total   = 0;

            foreach ($order_totals as $ord_total) {
                if ($ord_total['code'] == 'sub_total') {
                    $subtotal += $ord_total['value'];
                } elseif ($ord_total['code'] == 'shipping') {
                    $shipping += $ord_total['value'];
                } elseif ($ord_total['code'] == 'tax') {
                    $tax += $ord_total['value'];
                } elseif ($ord_total['code'] == 'coupon') {
                    $coupon_total += abs($ord_total['value']);
                    $coupon = $ord_total['title'];
                }
            }

            $shipping       = (float) $this->_format_price($shipping, $order_info['currency_code'], $order_info['currency_value']);
            $tax            = (float) $this->_format_price($tax, $order_info['currency_code'], $order_info['currency_value']);
            $subtotal       = (float) $this->_format_price($subtotal, $order_info['currency_code'], $order_info['currency_value']);
            $total          = (float) $this->_format_price($order_info['total'], $order_info['currency_code'], $order_info['currency_value']);
            $coupon_total   = (float) $this->_format_price($coupon_total, $order_info['currency_code'], $order_info['currency_value']);

            $this->load->model('localisation/country');

            $country = $this->model_localisation_country->getCountry($order_info['shipping_country_id']);
            $shipping_iso_code_2 = isset($country['iso_code_2']) ? $country['iso_code_2'] : '';

            $data['order'] = array(
                'id'                    => (string)$order_id,
                'valid_order_status'    => $valid_order_status,
                'total'                 => $total,
                'subtotal'              => $subtotal,
                'revenue'               => (float) $this->_format_number($total - (float)$tax),
                'shipping'              => $shipping,
                'payment_method'        => $order_info['payment_method'],
                'payment_code'          => $order_info['payment_code'],
                'shipping_method'       => $order_info['shipping_method'],
                'shipping_code'         => $order_info['shipping_code'],
                'shipping_country'      => $shipping_iso_code_2,
                'tax'                   => $tax,
                'coupon'                => $coupon,
                'coupon_total'          => $coupon_total,
                'quantity_total'        => 0,
                'date_added'            => date('Y-m-d H:i:s')
            );
        }

        if ($products) {
            // Order products
            $data['products'] = array();
            
            $quantity_total = 0;
            $order_products = $this->model_sale_order->getOrderProducts($order_id);

            foreach ($order_products as $key => $product) {
                $product_id = (int)$product['product_id'];

                $prod = [
                    'product_id'        => (string)$product_id,
                    'name'              => $product['name'],
                    'price'             => (float)$this->_get_product_price($product),
                    'price_with_tax'    => (float)$this->_get_product_price($product, false, true),
                    'price_eur'         => (float)$this->_get_product_price($product, 'EUR'),
                    'brand'             => $this->_get_product_manufacturer($product_id),
                    'category'          => $this->_get_product_category_name($product_id),
                    'variant'           => $this->_get_order_product_variant($order_id, $product_id),
                    'quantity'          => (string)$product['quantity'],
                    'list'              => 'purchase',
                    'position'          => ($key + 1)
                ];

                $quantity_total += $product['quantity'];

                $prod = array_merge($prod, $this->_add_alternative_product_ids($prod));

                $data['products'][] = $prod;
            }

            $data['order']['quantity_total'] = $quantity_total;
        }

        // Order customer data
        if ($customer) {
            $data['customer'] = array();

            $country_info           = $this->db->query("SELECT * FROM ".$this->db_prefix."country WHERE country_id = ".(int)$order_info['payment_country_id'])->row;
            $zone_info              = $this->db->query("SELECT * FROM ".$this->db_prefix."zone WHERE zone_id = ".(int)$order_info['payment_zone_id'])->row;

            $country_info_shipping  = $this->db->query("SELECT * FROM ".$this->db_prefix."country WHERE country_id = ".(int)$order_info['shipping_country_id'])->row;
            $zone_info_shipping     = $this->db->query("SELECT * FROM ".$this->db_prefix."zone WHERE zone_id = ".(int)$order_info['shipping_zone_id'])->row;
            
            $data['customer'] = [
                'general' => [
                    'email'                 => $order_info['email'],
                    'phone'                 => $order_info['telephone'],
                    'firstname'             => $order_info['firstname'],
                    'lastname'              => $order_info['lastname'],
                    'customer_id'           => (int)$order_info['customer_id'],
                    'customer_group_id'     => (int)$order_info['customer_group_id']
                ],
                'payment' => [
                    'address'               => $order_info['payment_address_1'],
                    'postcode'              => $order_info['payment_postcode'],
                    'city'                  => $order_info['payment_city'],
                    'zone'                  => $order_info['payment_zone'],
                    'zone_code'             => !empty($zone_info['code']) ? $zone_info['code'] : '',
                    'country'               => $order_info['payment_country'],
                    'country_iso_code_2'    => !empty($country_info['iso_code_2']) ? $country_info['iso_code_2'] : '',
                    'country_iso_code_3'    => !empty($country_info['iso_code_3']) ? $country_info['iso_code_3'] : '',
                ],
                'shipping' => [
                    'address'               => $order_info['shipping_address_1'],
                    'postcode'              => $order_info['shipping_postcode'],
                    'city'                  => $order_info['shipping_city'],
                    'zone'                  => $order_info['shipping_zone'],
                    'zone_code'             => !empty($zone_info_shipping['code']) ? $zone_info_shipping['code'] : '',
                    'country'               => $order_info['shipping_country'],
                    'country_iso_code_2'    => !empty($country_info_shipping['iso_code_2']) ? $country_info_shipping['iso_code_2'] : '',
                    'country_iso_code_3'    => !empty($country_info_shipping['iso_code_3']) ? $country_info_shipping['iso_code_3'] : '',
                ],
                'ads_enhanced_encoded' => [
                    'email'                 => data_layer::ads_hash(data_layer::ads_format_email($order_info['email'])),
                    'phone'                 => data_layer::ads_hash(data_layer::ads_format_phone($order_info['telephone'], !empty($country_info_shipping['iso_code_2']) ? $country_info_shipping['iso_code_2'] : '')),
                    'firstname'             => data_layer::ads_hash($order_info['firstname']),
                    'lastname'              => data_layer::ads_hash($order_info['lastname']),
                    'address'               => data_layer::ads_hash($order_info['shipping_address_1'])
                ],
                'tiktok_identify'      => [
                    'email'                 => data_layer::ads_hash(data_layer::ads_format_email($order_info['email'])),
                    'phone_number'          => data_layer::ads_hash(data_layer::ads_format_phone($order_info['telephone'], !empty($country_info_shipping['iso_code_2']) ? $country_info_shipping['iso_code_2'] : '')),
                    'external_id'           => hash('SHA256', trim(isset($gmt_info['session_id']) ? $gmt_info['session_id'] : ''))
                ]
            ];
        }

        return $data;
    }

    /**
     * Validate the order status
     * 
     * @param int $order_status_id
     * @return bool
     */
    private function _validate_order_status( $order_status_id ) {
        $selected_store_id      = isset($this->session->data['gmt_selected_store_id']) ? (int)$this->session->data['gmt_selected_store_id'] : 0;
        $order_statuses_valid   = $this->config->get("google_all_positive_conversion_status_id_{$selected_store_id}");

        if (empty($order_statuses_valid)) {
            return true;
        } else {
            return in_array($order_status_id, $order_statuses_valid);
        }

        return false;
    }

    /**
     * Get product price
     * 
     * @param array $product_info
     * @param string|bool $currency
     * @param bool $force_taxes
     * @return int|float
     */
    private function _get_product_price( $product_info, $currency = false, $force_taxes = false ) {
        $this->load->model('catalog/product');
        
        if (!is_array($product_info)) {
            return 0;
        }

        if ($force_taxes) {
            if (!array_key_exists('tax_class_id', $product_info)) {
                $tax_class_id = $this->_get_product_tax_class_id((int)$product_info['product_id']);
            } else {
                $tax_class_id = (int)$product_info['tax_class_id'];
            }
        }

        $final_price = 0;

        $product_from_order = array_key_exists('order_product_id', $product_info) && !empty($product_info['order_product_id']);

        if ($product_from_order) {
            $total = $product_info['price'];

            if (!empty($total)) {
                if ($force_taxes) {
                    $total = $this->tax->calculate($total, $tax_class_id, $this->config->get('config_tax'));
                }

                $final_price = $this->_format_price($total);
            }
        }

        if ($currency) {
            $product_info = $this->model_catalog_product->getProduct((int)$product_info['product_id']);

            if(empty($product_info))
                return 0;

            $base_price             = !empty($product_info['special']) ? (float)$product_info['special'] : (float)$product_info['price'];
            $force_currency_price   = $this->_format_price($this->tax->calculate($base_price, $product_info['tax_class_id'], $this->config->get('config_tax')), $currency);
            
            return $force_currency_price;
        }

        if (!$product_from_order && !empty($product_info) && is_array($product_info)) {
            if (!array_key_exists('tax_class_id', $product_info)) {
                $product_info['tax_class_id'] = $this->_get_product_tax_class_id((int)$product_info['product_id']);
            }

            if (!is_numeric($product_info['price'])) {
                $product_info = $this->model_catalog_product->getProduct($product_info['product_id']);
            }

            $price = 0;

            if (!empty($product_info['special'])) {
                $price = !is_numeric($product_info['special']) ? $product_info['special'] : round($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), 2);
            } elseif (!empty($product_info['price'])) {
                $price = !is_numeric($product_info['price']) ? $product_info['price'] : round($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), 2);
            }

            $final_price = $this->_format_price($price);
        }

        return $final_price;
    }

    /**
     * Get the product category name
     * 
     * @param int $product_id
     * @return string
     */
    private function _get_product_category_name( $product_id ) {
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');

        $product_categories = $this->model_catalog_product->getProductCategories($product_id);

        if (!empty($product_categories[0])) {
            $category_info = $this->model_catalog_category->getCategory((int)$product_categories[0]);
        }

        return !empty($category_info['name']) ? str_replace("'", "\'", $category_info['name']) : '';
    }

    /**
     * Add alternative product IDs
     * 
     * @param array $product
     * @return array
     */
    private function _add_alternative_product_ids( $product ) {
        $temp_product       = array();
        $selected_store_id  = isset($this->session->data['gmt_selected_store_id']) ? (int)$this->session->data['gmt_selected_store_id'] : 0;
        
        $temp_product['product_id_ee']      = $this->_get_product_field($product, $this->config->get('google_all_google_ee_product_id_like_'.$selected_store_id));
        $temp_product['product_id_gdr_1']   = $this->_get_product_field($product, $this->config->get('google_all_google_product_id_like_'.$selected_store_id));
        $temp_product['product_id_gdr_2']   = $this->_get_product_field($product, $this->config->get('google_all_dynamic_remarketing_dynx2_'.$selected_store_id));
        $temp_product['product_id_fb']      = $this->_get_product_field($product, $this->config->get('google_all_fb_pixel_id_like_'.$selected_store_id));
        $temp_product['product_id_gb']      = $this->_get_product_field($product, $this->config->get('google_all_google_reviews_gtin_'.$selected_store_id));
        
        return $temp_product;
    }

    /**
     * Get product field
     * 
     * @param array $product
     * @param string $product_identificator
     * @return mixed
     */
    private function _get_product_field( $product, $product_identificator = 'product_id' ) {
        if (!is_array($product)) {
            return '';
        }
        
        $product_identificator = empty($product_identificator) ? 'product_id' : $product_identificator;

        if ($product_identificator != 'product_id' && is_array($product)) {
            if (!array_key_exists($product_identificator, $product)) {
                $identificator = $this->_get_product_data((int)$product['product_id'], array($product_identificator))[$product_identificator];
            } else {
                $identificator = $product[$product_identificator];
            }
        }

        return !empty($identificator) ? $identificator : $product['product_id'];
    }

    /**
     * Get real product identificator
     * 
     * @param array $product
     * @param string $product_identificator
     * @return mixed
     */
    private function _get_real_product_identificator( $product, $product_identificator = 'product_id' ) {
        if ($product_identificator != 'product_id') {
            if (!array_key_exists($product_identificator, $product)) {
                $identificator = $this->_get_product_data((int)$product['product_id'], array($product_identificator))[$product_identificator];
            } else {
                $identificator = $product[$product_identificator];
            }
        }

        return !empty($identificator) ? $identificator : $product['product_id'];
    }

    /**
     * Get contents array
     * 
     * @param array $product
     * @return array
     */
    private function _get_contents_array( $products ) {
        $contents = array();

        foreach ($products as $prod) {
            $contents[] = array(
                'id'            => !empty($prod['product']['product_id_fb']) ? $prod['product']['product_id_fb'] : $this->_get_real_product_identificator($prod),
                'quantity'      => 1,
                'item_price'    => !empty($prod['product']['prices']) ? $prod['product']['prices']['price']['price'] : $this->_get_product_price((int)$prod['product_id'], true),
            );
        }

        return $contents;
    }

    /**
     * Format price
     * 
     * @param float $price
     * @param bool|string $currency_code
     * @param float $currency_value
     * @return string
     */
    private function _format_price( $price, $currency_code = false, $currency_value = 0 ) {
        if ($currency_code && $this->db->query("SELECT currency_id FROM " . $this->db_prefix . "currency WHERE code = '".$currency_code."'")->num_rows != 1) {
            return false;
        }

        if (!$currency_code) {
            $currency_code = $this->config->get('config_currency');
        }

        $price = $this->currency->format($price, $currency_code, $currency_value, false);

        return $this->_format_number($price);
    }

    /**
     * Format price with currency
     * 
     * @param float $price
     * @param string $currency_code
     * @return string
     */
    private function _format_price_with_currency( $price, $currency_code = '' ) {
        $price = number_format($price, 2, '.', '');
        
        if (empty($currency_code)) {
            $currency_code = $this->config->get('config_currency');
        }

        $symbol_left  = $this->currency->getSymbolLeft($currency_code);
		$symbol_right = $this->currency->getSymbolRight($currency_code);

        $string = '';

        if ($symbol_left) {
			$string .= $symbol_left;
		}

        $string .= $price;

        if ($symbol_right) {
			$string .= $symbol_right;
		}

        return $string;
    }

    /**
     * Format number
     * 
     * @param float $number
     * @return string
     */
    private function _format_number( $number ) {
        return number_format((float)$number, 2, '.', '');
    }
}
?>