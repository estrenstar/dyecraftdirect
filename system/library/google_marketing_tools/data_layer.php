<?php
	namespace google_marketing_tools;
	class data_layer {
        private $gmt_master;
        private $gmt_data = array();
        private $session;
        private $db;

        public function __construct( $registry ) {
            $this->gmt_master = $registry->get('GMTMaster');
            $this->session = $registry->get('session');
            $this->db = $registry->get('db');
        }

	    public function get_datalayer_data() {
            $gmt_data = array();

            $product_details = $this->get_data('product_details');

		    $gmt_data['general_data'] = array(
	            'current_view'          => $this->gmt_master->get_current_view(),
                'current_list'          => $this->gmt_master->get_current_list($product_details),
                'current_currency'      => $this->gmt_master->currency_code,
                'current_categories'    => $this->gmt_master->get_category_names_array_category_view(),
                'store_name'            => $this->gmt_master->get_store_name(),
                'coupon'              => isset($this->session->data['coupon']) ? $this->session->data['coupon'] : '',
                'debug_enabled'         => $this->gmt_master->debug_enabled(),
            );

		    $gmt_data = array_merge($gmt_data, $this->get_data());

		    //Set purchase data
		    $gmt_data = array_merge($gmt_data, $this->get_purchase_data());

		    return $gmt_data;
        }

        public function format_cart_products($products) {
	        foreach ($products as $key => $prod) {
	            $products[$key] = $this->format_product_details($prod, false);
	        }

	        return $products;
        }

		public function format_product_details($pro, $add_reviews = true) {
			if (empty($pro) || empty($pro['product_id'])) {
				return false;
            }

			$temp_pro = array();
            $return = array();
            $temp_pro['product_id'] = $pro['product_id'];

            //Add alternative product ids.
            $temp_pro = array_merge($temp_pro, $this->_add_alternative_product_ids($pro));

            $temp_pro['prices'] = $this->gmt_master->get_product_prices($pro);
            $temp_pro['image_url'] = $this->gmt_master->get_product_image_url($pro);
            $temp_pro['url'] = !empty($pro['href']) ? $pro['href'] : $this->gmt_master->get_product_url($pro['product_id']);
            $temp_pro['priceValidUntil'] = date('Y-12-31', strtotime('+2 years'));
            $temp_pro['sort_description'] = !empty($pro['meta_description']) ? $pro['meta_description'] : '';
            $temp_pro['name'] = $pro['name'];
            $temp_pro['manufacturer'] = array_key_exists("manufacturer", $pro) ? $pro['manufacturer'] : $this->gmt_master->get_product_manufacturer($pro['product_id']);
            $temp_pro['category'] = $this->gmt_master->get_product_category_name($pro['product_id']);
            $temp_pro['model'] = !empty($pro['model']) ? $pro['model'] : '';
            $temp_pro['sku'] = !empty($pro['sku']) ? $pro['sku'] : '';
            $temp_pro['mpn'] = !empty($pro['mpn']) ? $pro['mpn'] : '';
            $temp_pro['availability'] = !empty($pro['quantity']) || $this->gmt_master->config->get("config_stock_checkout") ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock';

            $temp_pro['sizes_domodi'] = '';
            $domodi_sizes_config = $this->gmt_master->config->get("google_all_domodi_size_options_".$this->gmt_master->config->get('config_store_id'));
            if(!empty($domodi_sizes_config)) {
                $product_options = $this->gmt_master->get_product_options($pro['product_id']);

                if(!empty($product_options)) {
                    $domodi_sizes = array();

                    foreach ($product_options as $option_id => $option_data) {
                        if (in_array($option_id, $domodi_sizes_config)) {
                            $domodi_sizes = array_merge($domodi_sizes, array_values($option_data['option_values']));
                        }
                    }

                    $temp_pro['sizes_domodi'] = !empty($domodi_sizes) ? json_encode($domodi_sizes) : '';
                }
            }

            //From cart
            $temp_pro['variant'] = !empty($pro['option']) ?  $this->gmt_master->get_product_variant_order_success($pro['option']) : '';
            $temp_pro['stock'] = !empty($pro['quantity']) ? $pro['quantity'] : 0;
            $temp_pro['quantity'] = !empty($pro['quantity']) ? $pro['quantity'] : 0;
            $temp_pro['minimum'] = !empty($pro['minimum']) ? $pro['minimum'] : 1;

            $pro = $temp_pro;

            if($add_reviews) {
                $reviews = $this->gmt_master->model_catalog_review->getReviewsByProductId($pro['product_id'], 0, 20);

                $count_reviews = !empty($reviews) ? count($reviews) : 0;
                $pro['ratingValue'] = 0;
                $pro['reviewCount'] = $count_reviews;

                if (!empty($reviews)) {
                    $total = 0;
                    foreach ($reviews as $key => $review) {
                        $total += $review['rating'];
                    }
                    $medium = $total / $count_reviews;
                    $pro['ratingValue'] = number_format($medium, 2) . '/5';
                }

                $return['reviews'] = $reviews;
            }

            $return['product'] = $pro;

            return $return;
		}

		public function _add_alternative_product_ids($pro) {
	        $temp_pro = array();
	        $temp_pro['product_id_ee'] = $this->gmt_master->get_product_field($pro, $this->gmt_master->config->get('google_all_google_ee_product_id_like_'.$this->gmt_master->config->get('config_store_id')));
            $temp_pro['product_id_gdr_1'] = $this->gmt_master->get_product_field($pro, $this->gmt_master->config->get('google_all_google_product_id_like_'.$this->gmt_master->config->get('config_store_id')));
            $temp_pro['product_id_gdr_2'] = $this->gmt_master->get_product_field($pro, $this->gmt_master->config->get('google_all_dynamic_remarketing_dynx2_'.$this->gmt_master->config->get('config_store_id')));
            $temp_pro['product_id_fb'] = $this->gmt_master->get_product_field($pro, $this->gmt_master->config->get('google_all_fb_pixel_id_like_'.$this->gmt_master->config->get('config_store_id')));
            $temp_pro['product_id_gb'] = $this->gmt_master->get_product_field($pro, $this->gmt_master->config->get('google_all_google_reviews_gtin_'.$this->gmt_master->config->get('config_store_id')));
            
            return $temp_pro;
        }

		public function format_products_listed($results, $products_formatted, $customized_list = '') {
            if (!is_array($products_formatted)) {
                return array();
            }

            $products_formatted = array_values($products_formatted);

            if (empty($products_formatted)) {
                return array();
            }

	        if (is_object($results)) {
	            $results = json_decode(json_encode($results), true);
            }

            $final_products = array();
		    $results = array_values($results);
		    
            foreach ($results as $key => $prod) {
                if (empty($products_formatted[$key]) || !is_array($products_formatted[$key]) || !is_array($prod) || empty($prod['product_id'])) {
                    continue;
                }

		        $prod_combined = array_merge($products_formatted[$key], $prod);
		        $product_formatted = $this->format_product_details($prod_combined, false);
		        $product_formatted['product']['position'] = $key+1;
		        
                if (!empty($customized_list)) {
		            $product_formatted['product']['list'] = $customized_list;
                }
                
		        $final_products[] = $product_formatted;
		    }

		    return $final_products;
        }
        
        public function format_promotions_listed($promotions, $setting_name = '') {
            $final_promotions = array();

            foreach ($promotions as $key => $promo) {
                if(!empty($promo['result_copy']['banner_id'])) {
                    $final_promotions[] = array(
                        'id' => $promo['result_copy']['banner_image_id'],
                        'name' => $promo['title'],
                        'url' => $promo['link'],
                        'position' => $key+1,
                        'list' => !empty($promo['result_copy']['name']) ? $promo['result_copy']['name'] : ''
                    );
                }
            }
            
            return $final_promotions;
        }
        
		public function get_purchase_data(){
		    $return     = array();
	        $gmt_data   = $this->get_data();

            if (empty($gmt_data['order_id']) && !empty($_GET['force_order_id'])) {
                $session_data   = $this->gmt_master->session->data;
                $previous_id    = !empty($session_data['force_order_id_previous']) ? $session_data['force_order_id_previous'] : '';
                $current_id     = $_GET['force_order_id'];

                if (empty($previous_id) || $previous_id != $current_id) {
                    $gmt_data['order_id'] = $this->gmt_master->db->escape($current_id);
                    
                    $this->gmt_master->session->data['force_order_id_previous'] = $current_id;
                }
            }

            if (!empty($_GET['order_id']) && !empty($this->gmt_master->session->data['order_tracked']) && $this->gmt_master->session->data['order_tracked'] != $_GET['order_id']) {
                $this->gmt_master->session->data['order_tracked'] = $gmt_data['order_id'] = $_GET['order_id'];
            }

            $is_purchase_view = array_key_exists('order_id', $gmt_data) && !empty($gmt_data['order_id']) && $this->gmt_master->get_current_view() == 'purchase';
            
            if (!$is_purchase_view) {
                return array();
            }

            $order_id = $gmt_data['order_id'];

            if ($is_purchase_view) {
                $return['order_data'] = $this->get_order_data($order_id);
            }

            return $return;
        }

        public function get_order_data( $order_id ) {
            $order_info         = $this->gmt_master->getOrder($order_id);
            $valid_order_status = $this->gmt_master->validate_order_status($order_id);

            $order_info['totals'] = $this->gmt_master->getOrderTotals($order_id);
            $order_info['products'] = $this->gmt_master->getOrderProducts($order_id);

            // Format totals
            $shipping = 0;
            $tax = 0;
            $subtotal = 0;
            $coupon = '';
            $coupon_total = 0;

            foreach ($order_info['totals'] as $key => $ord) {
                if ($ord['code'] == 'sub_total') {
                    $subtotal += $ord['value'];
                } elseif ($ord['code'] == 'shipping') {
                    $shipping += $ord['value'];
                } elseif ($ord['code'] == 'tax') {
                    $tax += $ord['value'];
                } elseif ($ord['code'] == 'coupon') {
                    $coupon_total += abs($ord['value']);
                    $coupon = $ord['title'];
                }
            }
            
            $order_info['shipping'] = $shipping = $this->gmt_master->format_price($shipping, true);
            $order_info['tax']      = $tax = $this->gmt_master->format_price($tax, true);
            $order_info['subtotal'] = $subtotal = $this->gmt_master->format_price($subtotal, true);
            $order_info['total']    = $total = $this->gmt_master->format_price($order_info['total'], true);
            $coupon_total           = $this->gmt_master->format_price($coupon_total, true);
            $order_info['coupon']   = $coupon;

            $order_data = array();

            // Order general data
            $order_data['order'] = array(
                'valid_order_status'    => $valid_order_status,
                'id'                    => $order_info['order_id'],
                'total'                 => $total,
                'subtotal'              => $subtotal,
                'revenue'               => $this->gmt_master->format_number($total - (float)$tax),
                'shipping'              => $shipping,
                'payment_method'        => $order_info['payment_method'],
                'payment_code'          => $order_info['payment_code'],
                'shipping_method'       => $order_info['shipping_method'],
                'shipping_code'         => $order_info['shipping_code'],
                'tax'                   => $tax,
                'coupon'                => $coupon,
                'coupon_total'          => $coupon_total,
                'quantity_total'        => 0,
                'date_added'            => date("Y-m-d H:i:s")
            );

            $total = $this->gmt_master->format_price($this->gmt_master->getOrderTotalCustom($order_info['order_id'], array('sub_total','shipping','tax')), true);

            // Order products
            $quantity_total = 0;
            $order_data['products'] = array();

            foreach ($order_info['products'] as $key => $product) {
                $prod = array(
                    'product_id'        => $product['product_id'],
                    'name'              => $product['name'],
                    'price'             => $this->gmt_master->get_product_price($product),
                    'price_with_tax'    => $this->gmt_master->get_product_price($product, false, false, true),
                    'price_eur'         => $this->gmt_master->get_product_price($product, false, 'EUR'),
                    'brand'             => $this->gmt_master->get_product_manufacturer($product['product_id']),
                    'category'          => $this->gmt_master->get_product_category_name($product['product_id']),
                    'variant'           => $this->gmt_master->get_order_product_variant($order_info['order_id'], $product['product_id']),
                    'quantity'          => $product['quantity'],
                    'list'              => 'purchase',
                    'position'          => ($key + 1)
                );

                $quantity_total += $product['quantity'];

                $prod = array_merge($prod, $this->_add_alternative_product_ids($prod));

                $order_data['products'][] = $prod;
            }

            $order_data['order']['quantity_total'] = $quantity_total;

            //If empty shipping address, complete it with payment address
            $order_info['shipping_country_id'] = !empty($order_info['shipping_country_id']) ? $order_info['shipping_country_id'] : $order_info['payment_country_id'];
            $order_info['shipping_zone_id'] = !empty($order_info['shipping_zone_id']) ? $order_info['shipping_zone_id'] : $order_info['payment_zone_id'];
            $order_info['shipping_address_1'] = !empty($order_info['shipping_address_1']) ? $order_info['shipping_address_1'] : $order_info['payment_address_1'];
            $order_info['shipping_postcode'] = !empty($order_info['shipping_postcode']) ? $order_info['shipping_postcode'] : $order_info['payment_postcode'];
            $order_info['shipping_city'] = !empty($order_info['shipping_city']) ? $order_info['shipping_city'] : $order_info['payment_city'];
            $order_info['shipping_zone'] = !empty($order_info['shipping_zone']) ? $order_info['shipping_zone'] : $order_info['payment_zone'];
            $order_info['shipping_country'] = !empty($order_info['shipping_country']) ? $order_info['shipping_country'] : $order_info['payment_country'];

            // Order customer data
            $country_info   = $this->gmt_master->db->query("SELECT * FROM ".DB_PREFIX."country WHERE country_id = ".(int)$order_info['payment_country_id'])->row;
            $zone_info      = $this->gmt_master->db->query("SELECT * FROM ".DB_PREFIX."zone WHERE zone_id = ".(int)$order_info['payment_zone_id'])->row;

            $country_info_shipping  = $this->gmt_master->db->query("SELECT * FROM ".DB_PREFIX."country WHERE country_id = ".(int)$order_info['shipping_country_id'])->row;
            $zone_info_shipping     = $this->gmt_master->db->query("SELECT * FROM ".DB_PREFIX."zone WHERE zone_id = ".(int)$order_info['shipping_zone_id'])->row;

            $customer_info = $this->gmt_master->get_customer_info();

            $customer_id = !empty($customer_info['customer_id']) ? (int)$customer_info['customer_id'] : 0;
            $email = $this->db->escape($order_info['email']);

            $sql_where = $customer_id > 0
                ? "(customer_id = $customer_id OR (customer_id = 0 AND email = '$email'))"
                : "(customer_id = 0 AND email = '$email')";

            $query = $this->db->query("SELECT COUNT(*) AS total_orders FROM `" . DB_PREFIX . "order` WHERE order_status_id > 0 AND $sql_where");
            $lifetime_orders = (int)$query->row['total_orders'];

            $query = $this->db->query("SELECT SUM(total) AS total_spent FROM `" . DB_PREFIX . "order` WHERE order_status_id > 0 AND $sql_where");
            $lifetime_value = (float)$query->row['total_spent'];

            $order_data['customer'] = array(
                'general' => array(
                    'email'                 => $order_info['email'],
                    'phone'                 => $order_info['telephone'],
                    'firstname'             => $order_info['firstname'],
                    'lastname'              => $order_info['lastname'],
                    'customer_id'           => !empty($customer_info['customer_id']) ? $customer_info['customer_id'] : 0,
                    'customer_group_id'     => !empty($customer_info['customer_group_id']) ? $customer_info['customer_group_id'] : 0,
                    'lifetime_orders'       => $lifetime_orders,
                    'lifetime_value'       => $lifetime_value
                ),
                'payment' => array(
                    'address'               => $order_info['payment_address_1'],
                    'postcode'              => $order_info['payment_postcode'],
                    'city'                  => $order_info['payment_city'],
                    'zone'                  => $order_info['payment_zone'],
                    'zone_code'             => !empty($zone_info['code']) ? $zone_info['code'] : '',
                    'country'               => $order_info['payment_country'],
                    'country_iso_code_2'    => !empty($country_info['iso_code_2']) ? $country_info['iso_code_2'] : '',
                    'country_iso_code_3'    => !empty($country_info['iso_code_3']) ? $country_info['iso_code_3'] : '',
                ),
                'shipping' => array(
                    'address'               => $order_info['shipping_address_1'],
                    'postcode'              => $order_info['shipping_postcode'],
                    'city'                  => $order_info['shipping_city'],
                    'zone'                  => $order_info['shipping_zone'],
                    'zone_code'             => !empty($zone_info_shipping['code']) ? $zone_info_shipping['code'] : '',
                    'country'               => $order_info['shipping_country'],
                    'country_iso_code_2'    => !empty($country_info_shipping['iso_code_2']) ? $country_info_shipping['iso_code_2'] : '',
                    'country_iso_code_3'    => !empty($country_info_shipping['iso_code_3']) ? $country_info_shipping['iso_code_3'] : '',
                ),
                'ads_enhanced_encoded' => array(
                    'email'                 => self::ads_hash(self::ads_format_email($order_info['email'])),
                    'phone'                 => self::ads_hash(self::ads_format_phone($order_info['telephone'], !empty($country_info_shipping['iso_code_2']) ? $country_info_shipping['iso_code_2'] : '')),
                    'firstname'             => self::ads_hash($order_info['firstname']),
                    'lastname'              => self::ads_hash($order_info['lastname']),
                    'address'               => self::ads_hash($order_info['shipping_address_1']),
                ),
                'tiktok_identify' => array(
                    'email'                 => self::ads_hash(self::ads_format_email($order_info['email'])),
                    'phone_number'          => self::ads_hash(self::ads_format_phone($order_info['telephone'], !empty($country_info_shipping['iso_code_2']) ? $country_info_shipping['iso_code_2'] : '')),
                    'external_id'           => hash('SHA256', trim($this->session->getId()))
                )
            );

            // Save cliente email in session for recover it in datalayer initialization
            $this->gmt_master->session->data['gmt_customer_email'] = $order_info['email'];

            return $order_data;
        }

        public static function ads_format_email($email) {
            $normalized_email = strtolower($email);
            $email_parts = explode('@', $normalized_email);

            if (count($email_parts) > 1 && preg_match('/^(gmail|googlemail)\.com\s*/', $email_parts[1]) ) {
                // Removes any '.' characters from the portion of the email address before the domain
                // if the domain is gmail.com or googlemail.com.
                $email_parts[0] = str_replace(".", "", $email_parts[0]);
                $normalized_email = sprintf('%s@%s', $email_parts[0], $email_parts[1]);
            }

            $normalized = strtolower($normalized_email);

            // Removes leading, trailing, and intermediate spaces.
            $normalized = str_replace(' ', '', $normalized);

            return $normalized;

            /*
            $email = explode("@", $email);
            if(count($email) == 2) {
                $email[1] = str_replace(".","",$email[1]);
                $email = $email[0].'@'.$email[1];
            } else {
                $email = '';
            }

            return $email;*/
        }

        public static function ads_format_phone($phone, $country_code) {
            $phone = (string)$phone;

            $countries_code = array('AD'=>'+376','AE'=>'+971','AF'=>'+93','AG'=>'+1 (268)','AI'=>'+1 (264)','AL'=>'+355','AM'=>'+374','AO'=>'+244','AR'=>'+54','AT'=>'+43','AU'=>'+61','AW'=>'+297','AZ'=>'+994','BA'=>'+387','BB'=>'+1 (246)','BD'=>'+880','BE'=>'+32','BF'=>'+226','BG'=>'+359','BH'=>'+973','BI'=>'+257','BJ'=>'+229','BL'=>'+590','BM'=>'+1 (441)','BN'=>'+673','BO'=>'+591','BQ'=>'+599','BR'=>'+55','BS'=>'+1 (242)','BT'=>'+975','BW'=>'+267','BY'=>'+375','BZ'=>'+501','CA'=>'+1','CC'=>'+61','CD'=>'+243','CF'=>'+236','CG'=>'+242','CH'=>'+41','CI'=>'+225','CK'=>'+682','CL'=>'+56','CM'=>'+237','CN'=>'+86','CO'=>'+57','CR'=>'+506','CU'=>'+53','CV'=>'+238','CW'=>'+599','CX'=>'+61','CY'=>'+357','CZ'=>'+420','DE'=>'+49','DJ'=>'+253','DK'=>'+45','DM'=>'+1 (767)','DO'=>'+1 (809, 829, 849)','DZ'=>'+213','EC'=>'+593','EE'=>'+372','EG'=>'+20','EH'=>'+212','ER'=>'+291','ES'=>'+34','ET'=>'+251','FI'=>'+358','FJ'=>'+679','FK'=>'+500','FM'=>'+691','FO'=>'+298','FR'=>'+33','GA'=>'+241','GB'=>'+44','GD'=>'+1 (473)','GE'=>'+995','GF'=>'+594','GG'=>'+44 (0)','GH'=>'+233','GI'=>'+350','GL'=>'+299','GM'=>'+220','GN'=>'+224','GP'=>'+590','GQ'=>'+240','GR'=>'+30','GT'=>'+502','GU'=>'+1 (671)','GW'=>'+245','GY'=>'+592','HK'=>'+852','HN'=>'+504','HR'=>'+385','HT'=>'+509','HU'=>'+36','ID'=>'+62','IE'=>'+353','IL'=>'+972','IM'=>'+44 (0)','IN'=>'+91','IO'=>'+246','IQ'=>'+964','IR'=>'+98','IS'=>'+354','IT'=>'+39','JE'=>'+44 (0)','JM'=>'+1 (876)','JO'=>'+962','JP'=>'+81','KE'=>'+254','KG'=>'+996','KH'=>'+855','KI'=>'+686','KM'=>'+269','KN'=>'+1 (869)','KP'=>'+850','KR'=>'+82','KW'=>'+965','KY'=>'+1 (345)','KZ'=>'+7 (6, 7)','LA'=>'+856','LB'=>'+961','LC'=>'+1 (758)','LI'=>'+423','LK'=>'+94','LR'=>'+231','LS'=>'+266','LT'=>'+370','LU'=>'+352','LV'=>'+371','LY'=>'+218','MA'=>'+212','MC'=>'+377','MD'=>'+373','ME'=>'+382','MF'=>'+590','MG'=>'+261','MH'=>'+692','MK'=>'+389','ML'=>'+223','MM'=>'+95','MN'=>'+976','MO'=>'+853','MP'=>'+1 (670)','MQ'=>'+596','MR'=>'+222','MS'=>'+1 (664)','MT'=>'+356','MU'=>'+230','MV'=>'+960','MW'=>'+265','MX'=>'+52','MY'=>'+60','MZ'=>'+258','NA'=>'+264','NC'=>'+687','NE'=>'+227','NF'=>'+672','NG'=>'+234','NI'=>'+505','NL'=>'+31','NO'=>'+47','NP'=>'+977','NR'=>'+674','NU'=>'+683','NZ'=>'+64','OM'=>'+968','PA'=>'+507','PE'=>'+51','PF'=>'+689','PG'=>'+675','PH'=>'+63','PK'=>'+92','PL'=>'+48','PM'=>'+508','PR'=>'+1 (787, 939)','PS'=>'+970','PT'=>'+351','PW'=>'+680','PY'=>'+595','QA'=>'+974','RE'=>'+262','RO'=>'+40','RS'=>'+381','RU'=>'+7','RW'=>'+250','SA'=>'+966','SB'=>'+677','SC'=>'+248','SD'=>'+249','SE'=>'+46','SG'=>'+65','SH'=>'+290','SI'=>'+386','SJ'=>'+47','SK'=>'+421','SL'=>'+232','SM'=>'+378','SN'=>'+221','SO'=>'+252','SR'=>'+597','SS'=>'+211','ST'=>'+239','SV'=>'+503','SX'=>'+1 (721)','SY'=>'+963','SZ'=>'+268','TC'=>'+1 (649)','TD'=>'+235','TG'=>'+228','TH'=>'+66','TJ'=>'+992','TK'=>'+690','TL'=>'+670','TM'=>'+993','TN'=>'+216','TO'=>'+676','TR'=>'+90','TT'=>'+1 (868)','TV'=>'+688','TW'=>'+886','TZ'=>'+255','UA'=>'+380','UG'=>'+256','US'=>'+1','UY'=>'+598','UZ'=>'+998','VA'=>'+379','VC'=>'+1 (784)','VE'=>'+58','VG'=>'+1 (284)','VI'=>'+1 (340)','VN'=>'+84','VU'=>'+678','WF'=>'+681','WS'=>'+685','XK'=>'+383','YE'=>'+967','YT'=>'+262','ZA'=>'+27','ZM'=>'+260','ZW'=>'+263');

            $prefix = !empty($countries_code[$country_code]) ? $countries_code[$country_code] : '';

            $phone = str_replace(" ", "", $phone);

            if (is_numeric($phone) && (strlen($phone) == 9 || (strlen($phone) == 10 && $phone[0] == 0))) {
                if(!empty($prefix)) {
                    $phone = $prefix.ltrim($phone, '0');
                } else {
                    $phone = $prefix.$phone;
                }
            } elseif (is_numeric($phone) && strlen($phone) == 11 && $phone[0] == 0 && $phone[1] == 0) {
                if (!empty($prefix)) {
                    $phone = $prefix.ltrim($phone, '00');
                } else {
                    $phone = '';
                }
            } elseif (strlen($phone) == 12 && $phone[0] == '+') {

            } else {
                $phone = $prefix.$phone;
            }

            return $phone;
        }

        public static function ads_hash($string) {
            if(!empty($string)) {
                return hash('SHA256', strtolower(trim($string)));
            }

            return '';
        }

        public function set_data($id, $data) {
            $this->gmt_data[$id] = $data;
        }

        public function add_data($id, $data) {
		    $current_data = $this->get_data($id);

		    if (empty($current_data)) {
                $this->gmt_data[$id] = $data;
            } else {
		        if(is_array($data)) {
		            foreach ($data as $key => $dat) {
		                $this->gmt_data[$id][] = $dat;
		            }
                } else {
		            $this->gmt_data[$id][] = $data;
                }
            }
        }

        public function get_data($id = '') {
		    if (empty($id)) {
		        return $this->gmt_data;
            }

            $data = array_key_exists($id, $this->gmt_data) ? $this->gmt_data[$id] : false;
            
            return $data;
        }
	}
?>