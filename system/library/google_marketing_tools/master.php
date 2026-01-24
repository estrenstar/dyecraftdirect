<?php
	namespace google_marketing_tools;
	class Master extends \Controller 
	{
		/**
		 * Store ID
		 * 
		 * @var int|string
		 */
		public $id_store;

		/**
		 * Currency code
		 * 
		 * @var string
		 */
		public $currency_code;

		/**
		 * Cart products
		 * 
		 * @var array
		 */
		public $cart_products;

		/**
		 * Count products on the cart
		 * 
		 * @var int
		 */
		public $cart_units;

		/**
		 * Language code
		 * 
		 * @var string
		 */
		public $language_code;

		/**
		 * Language ID
		 * 
		 * @var int
		 */
		public $language_id;

		/**
		 * Search
		 * 
		 * @var string
		 */
		public $search;

		/**
		 * Tag
		 * 
		 * @var string
		 */
		public $tag;
		
		/**
		 * Description
		 * 
		 * @var string
		 */
		public $description;

		/**
		 * Assets path
		 * 
		 * @var string
		 */
		public $assets_path;

		/**
		 * Route ID
		 * 
		 * @var string
		 */
		public $route_id;

		/**
		 * Route
		 * 
		 * @var string
		 */
		public $route;

		/**
		 * Filter data
		 * 
		 * @var array
		 */
		public $filter_data;

		/**
		 * Categories IDs
		 * 
		 * @var array
		 */
		public $categories_id;

		/**
		 * Information ID
		 * 
		 * @var string
		 */
		public $information_id;

		/**
		 * Manufacturer ID
		 * 
		 * @var string
		 */
		public $manufacturer_id;

		/**
		 * List ID
		 * 
		 * @var string
		 */
		public $listid;

		/**
		 * Server
		 * 
		 * @var string
		 */
		public $server;

		/**
		 * Scritps
		 * 
		 * @var array
		 */
		public $scripts = array();

		/**
		 * Urls
		 * 
		 * @var array
		 */
		public $urls;

		/**
		 * Seo Isense Bag Pack
		 * 
		 * @var bool
		 */
		public $seo_isense_bag_pack;

		/**
		 * Extension version
		 * 
		 * @var string $extension_version 
		 */
		private $extension_version;

		/**
		 * Constructor
		 * 
		 * @param Registry $registry
		 */
		public function __construct( $registry ) {
			$this->registry = $registry;

			$this->id_store 			= $this->config->get('config_store_id');
            $this->is_oc_3x 			= version_compare(VERSION, '3', '>=');
            $this->currency_code 		= version_compare(VERSION, '2.2.0.0', '<') ? $this->currency->getCode() : $this->session->data['currency'];
            $this->cart_products 		= $this->cart->getProducts();
			$this->cart_units 			= $this->cart->countProducts();
            $this->language_code		= $this->language->get('code');
			if($this->is_oc_3x) $this->detectLanguage();
		    $this->language_id 			= $this->config->get('config_language_id');
		    $this->search 				= !empty($this->request->get['search']) && is_string($this->request->get['search']) ? addslashes($this->request->get['search']) : (!empty($this->request->get['filter_name']) && is_string($this->request->get['filter_name']) ? addslashes($this->request->get['filter_name']) : '');
		    $this->tag 					= !empty($this->request->get['tag']) && is_string($this->request->get['tag']) ? addslashes($this->request->get['tag']) : $this->search;
		    $this->description 			= !empty($this->request->get['description']) && is_string($this->request->get['description']) ? addslashes($this->request->get['description']) : '';
			$this->information_id   	= array_key_exists('information_id', $this->request->get) ? $this->request->get['information_id'] : false;
		    $this->user 				= version_compare(VERSION, '2.1.0.3', '>') ? new \Cart\User($registry) : new \User($registry);
			$this->seo_isense_bag_pack 	= $this->db->query("SELECT * FROM information_schema.tables WHERE table_schema = '".DB_DATABASE."' AND table_name = '".DB_PREFIX."seo_url_alias'")->num_rows;
			$this->extension_version	= $this->config->has('google_all_google_version') ? $this->config->get('google_all_google_version') : date('Ymdhis');

            if ($this->currency_code == '') {
            	$this->currency_code =  $this->config->get('config_currency');
			}

			//mlseo - Compatibility
			if ($this->config->get('mlseo_enabled') && $this->search == '' && !empty($this->request->get['_route_']) && strpos($this->request->get['_route_'], 'search/') !== false) {
				$search = str_replace("search/", "", $this->request->get['_route_']);
				$search = str_replace(".html", "", $search);
				
				$this->search = $search;
			}

		    $this->load_models();

		    $this->assets_path = DIR_SYSTEM.'assets/gmt_includes/';

		    if(is_file($this->assets_path.'library_master_begin_construct.php'))
                require($this->assets_path.'library_master_begin_construct.php');

			// Custom url for checkout cart
			if ($this->config->get("google_all_custom_url_checkout_cart_".$this->id_store) != null) {  
				$custom_url_checkout_cart = explode(",", $this->config->get("google_all_custom_url_checkout_cart_".$this->id_store));
				foreach ($custom_url_checkout_cart as $key => $url) {
					$custom_url_checkout_cart[$key] = trim($url);
				}

				if (empty($custom_url_checkout_cart[0]) && count($custom_url_checkout_cart) == 1) {
					$custom_url_checkout_cart = array();
				}
			}
			else {
				$custom_url_checkout_cart = array();
			}

			// Custom url for checkout checkout
			if ($this->config->get("google_all_custom_url_checkout_checkout_".$this->id_store) != null) {
				$custom_url_checkout_checkout = explode(",", $this->config->get("google_all_custom_url_checkout_checkout_".$this->id_store));
				foreach ($custom_url_checkout_checkout as $key => $url) {
					$custom_url_checkout_checkout[$key] = trim($url);
				}

				if (empty($custom_url_checkout_checkout[0]) && count($custom_url_checkout_checkout) == 1) {
					$custom_url_checkout_checkout = array();
				}
			}
			else {
				$custom_url_checkout_checkout = array();
			}			

			// Custom url for checkout success
			if ($this->config->get("google_all_custom_url_checkout_success_".$this->id_store) != null) {
				$custom_url_checkout_success = explode(",", $this->config->get("google_all_custom_url_checkout_success_".$this->id_store));
				foreach ($custom_url_checkout_success as $key => $url) {
					$custom_url_checkout_success[$key] = trim($url);
				}

				if (empty($custom_url_checkout_success[0]) && count($custom_url_checkout_success) == 1) {
					$custom_url_checkout_success = array();
				}
			}	
			else {
				$custom_url_checkout_success = array();
			}		

			$this->urls = [
				'checkout_checkout_urls'            => array_merge(array('simplecheckout', 'checkout', 'quickcheckout', 'quick_checkout/checkout', 'checkout/checkout', 'supercheckout/supercheckout', 'simplecheckout/', 'simplecheckout', 'simple-checkout/', 'extension/quickcheckout/checkout', 'quickcheckout/checkout', 'tk_checkout/checkout', 'extension/payment/klarna_checkout'), $custom_url_checkout_checkout),
				'checkout_cart_urls'               => array_merge(array('checkout/cart'), $custom_url_checkout_cart),
				'checkout_success_urls'             => array_merge(array('checkout/success', 'supercheckout/success', 'checkout-success/', 'success', 'checkout/successmbway', 'extension/svea/success', 'payment/kandh/confirm', 'tk_checkout/success', 'extension/payment/klarna_checkout/confirmation'), $custom_url_checkout_success),
				'product_product_urls'              => array('product/product'),
				'product_wishlist_urls'             => array('product/wishlist'),
				'product_special_urls'              => array('product/special'),
				'product_search_urls'               => array('product/search'),
				'product_category_urls'             => array('product/category'),
				'product_manufacturer_info_urls'    => array('product/manufacturer/info'),
				'product_manufacturer_urls'         => array('product/manufacturer'),
				'information_information_urls'      => array('information/information'),
				'account_register_urls'             => array('account/register'),
				'account_login_urls'                => array('account/login'),
				'account_account_urls'              => array('account/account'),
				'account_password_urls'             => array('account/password'),
				'account_address_urls'              => array('account/address'),
				'account_forgotten_urls'            => array('account/forgotten'),
				'account_wishlist_urls'             => array('account/wishlist'),
				'account_order_urls'                => array('account/order'),
				'account_download_urls'             => array('account/download'),
				'account_recurring_urls'            => array('account/recurring'),
				'account_reward_urls'               => array('account/reward'),
				'account_return_urls'               => array('account/return'),
				'account_transaction_urls'          => array('account/transaction'),
				'account_newsletter_urls'           => array('account/newsletter'),
				'account_voucher_urls'              => array('account/voucher'),
				'account_logout_urls'               => array('account/logout'),
				'account_success_urls'              => array('account/success'),
				'affiliate_register_urls'           => array('affiliate/register'),
				'affiliate_login_urls'              => array('affiliate/login'),
				'affiliate_success_urls'            => array('affiliate/success'),
				'information_contact_urls'          => array('information/contact'),
				'information_contact_success_urls'  => array('information/contact/success'),
				'information_sitemap_urls'          => array('information/sitemap'),
				'products_lists_urls'               => array('product/search', 'product/category', 'product/manufacturer/info')
			];

            if(is_file($this->assets_path.'urls_compatibility.php'))
                require($this->assets_path.'urls_compatibility.php');

            $this->set_route_and_route_id();

		    $category_id = 0;
            $this->categories_id = array();

		    if (!empty($this->request->get['path'])) {
			    $categories_id = !empty($this->request->get['path']) ? explode('_', $this->request->get['path']) : array();
			    $this->categories_id = $categories_id;
			    
				$categories_id_temp = $categories_id;
			    $current_category_id = !empty($categories_id_temp) ? array_pop($categories_id_temp) : 0;

			    $category_id = !empty($this->request->get['category_id']) ? $this->request->get['category_id'] : $current_category_id;
			} elseif (!empty($this->request->get["_route_"])) {
				$categories_id = $this->startPathCategoryFromSEOURL($this->request->get["_route_"]);

				$this->categories_id = $categories_id;
				$category_id = !empty($categories_id[0]) ? $categories_id[0] : '';
			}

			$manufacturer_id = '';
			if ($this->route == 'product/manufacturer/info' && array_key_exists('manufacturer_id', $this->request->get)) {
			    $manufacturer_id = $this->request->get['manufacturer_id'];
			}

			$this->manufacturer_id = $manufacturer_id;
			$this->information_id = array_key_exists('information_id', $this->request->get) ? $this->request->get['information_id'] : false;

		    $sort = !empty($this->request->get['sort']) ? $this->request->get['sort'] : 'p.sort_order';
		    $order = !empty($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
		    $page = !empty($this->request->get['page']) && is_numeric($this->request->get['page']) ? $this->request->get['page'] : 1;
		    $limit = !empty($this->request->get['limit']) && is_numeric($this->request->get['limit']) ? $this->request->get['limit'] : ($this->config->get('config_product_limit') ? $this->config->get('config_product_limit') : 20);

		    $this->filter_data = array(
		      'filter_name'         => $this->search,
		      'filter_tag'          => $this->tag,
		      'filter_description'  => $this->description,
		      'filter_category_id'  => $category_id,
		      'filter_sub_category' => !empty($this->request->get['sub_category']) ? 1 : '',
		      'sort'                => $sort,
		      'order'               => $order,
		      'start'               => ($page - 1) * $limit,
		      'limit'               => $limit
		    );
			
		}

		private function detectLanguage() {
			//Fix for change url from header
				if(isset($this->request->post['code']) || isset($this->request->post['redirect'])) {
					$this->session->data['url_for_ignore_detect_language'] = $this->request->post['redirect'];
					return;
				}
				$current_url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				if(!empty($this->session->data['url_for_ignore_detect_language']) && $current_url == $this->session->data['url_for_ignore_detect_language'])
					return;
				$this->session->data['url_for_ignore_detect_language'] = '';

			$request_language_id = null;
			$request_language_code = '';
			$active_language_id = $this->config->get('config_language_id');

			if (isset($this->request->get['_route_'])) {
				$parts = $parts = explode('/', $this->request->get['_route_']);
				$keyword = end($parts);
			} 	else {
				$keyword = '';
			}

			if ($keyword || $this->request->server['REQUEST_URI'] == '/') {
				$query = $this->db->query("SELECT language_id  FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $this->db->escape($keyword) . "' AND store_id = '" . (int)$this->id_store . "' LIMIT 1");
				if ($query->row) {
					$request_language_id = (int)$query->row['language_id'];
					if ($this->request->server['REQUEST_URI'] == '/') $request_language_id = $this->config->get('config_language_id');
					$query = $this->db->query("SELECT code FROM " . DB_PREFIX . "language WHERE language_id = '" . (int)$request_language_id . "' AND status = '1' LIMIT 1");

					if ($query->row) {
						$request_language_code = $query->row['code'];
						$this->session->data['language'] = $request_language_code;
					}
				}
			}

			if (isset($this->session->data['language'])) {
				$query = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language WHERE code = '" . (int)$this->session->data['language'] . "' AND status = '1' LIMIT 1");
				if ($query->num_rows) {
					$active_language_id = (int)$query->row['language_id'];
				}
			}

			if($request_language_id  && $request_language_code && $active_language_id != $request_language_id) {
				$language = new \Language($request_language_code);
				$language->load($request_language_code);
				$this->registry->set('language', $language);
				$this->config->set('config_language_id', $request_language_id);
				$this->registry->set('language', $language);
			}
		}

		public function gmt_is_enabled() {
		    if (defined('JOURNAL3_ACTIVE') && !empty($this->request->get['popup'])) {
		        return false;
			}

		    $gmt_status = $this->config->get('google_all_tag_manager_status_'.$this->id_store);

			//Fix for wrong current_view
			if(!empty($_SERVER['REQUEST_URI']) && (strpos($_SERVER['REQUEST_URI'], 'swiper.jquery.js.map') !== false || strpos($_SERVER['REQUEST_URI'], 'icomoon.woff') !== false))
				$gmt_status = false;

		    if($gmt_status) {
		        $identification = $this->config->get('google_all_container_id_'.$this->id_store);
				$is_gtm = strpos($identification, 'GTM') !== false;

				if(!$is_gtm) {
					return false;
				}

				return true;
            }
		}

		public function get_extension_version() {
			return $this->extension_version;
		}

		public function get_store_name() {
            $meta_title = version_compare(VERSION, '2', '>=') ? $this->config->get('config_meta_title') : $this->config->get('config_name');

            if(!is_array($meta_title))
                return $meta_title;
            else {
                if(array_key_exists($this->language_id, $meta_title))
                    return $meta_title[$this->language_id];
            }

            return '';
        }

		public function get_gtm_id() {
            $gtm_id = explode("|", str_replace(' ', '', $this->config->get('google_all_container_id_'.$this->id_store)));
            return $gtm_id;
        }

        public function get_user_id() {
		    $user_id  = $this->customer->isLogged() ? $this->customer->getId() : 0;
		    return $user_id;
        }

        public function get_current_view() {
            if($this->route == 'common/home')
				return 'homepage';
			elseif(in_array($this->route, $this->_get_urls('product_product_urls')))
				return 'product';
			elseif(in_array($this->route, $this->_get_urls('product_wishlist_urls')))
				return 'wishlist';
			elseif($this->is_checkout_checkout())
				return 'checkout';
			elseif($this->is_checkout_success())
                return 'purchase';
			elseif($this->is_checkout_cart())
				return 'cart';
			elseif(in_array($this->route, $this->_get_urls('product_special_urls')))
				return 'special';
			elseif(in_array($this->route, $this->_get_urls('product_search_urls')))
				return 'search';
			elseif(in_array($this->route, $this->_get_urls('product_category_urls')))
				return 'category';
			elseif(in_array($this->route, $this->_get_urls('product_manufacturer_info_urls')))
				return 'manufacturer';
			elseif(in_array($this->route, $this->_get_urls('product_manufacturer_urls')))
				return 'manufacturer_list';
			elseif(in_array($this->route, $this->_get_urls('information_information_urls')))
				return 'information';
			elseif(in_array($this->route, $this->_get_urls('account_register_urls')))
				return 'account_register';
			elseif(in_array($this->route, $this->_get_urls('account_login_urls')))
				return 'account_login';
			elseif(in_array($this->route, $this->_get_urls('account_account_urls')))
				return 'account_account';
			elseif(in_array($this->route, $this->_get_urls('account_password_urls')))
				return 'account_password';
			elseif(in_array($this->route, $this->_get_urls('account_address_urls')))
				return 'account_address';
			elseif(in_array($this->route, $this->_get_urls('account_forgotten_urls')))
				return 'account_forgotten';
			elseif(in_array($this->route, $this->_get_urls('account_wishlist_urls')))
				return 'account_wishlist';
			elseif(in_array($this->route, $this->_get_urls('account_order_urls')))
				return 'account_order';
			elseif(in_array($this->route, $this->_get_urls('account_download_urls')))
				return 'account_download';
			elseif(in_array($this->route, $this->_get_urls('account_recurring_urls')))
				return 'account_recurring';
			elseif(in_array($this->route, $this->_get_urls('account_reward_urls')))
				return 'account_reward';
			elseif(in_array($this->route, $this->_get_urls('account_return_urls')))
				return 'account_return';
			elseif(in_array($this->route, $this->_get_urls('account_transaction_urls')))
				return 'account_transaction';
			elseif(in_array($this->route, $this->_get_urls('account_newsletter_urls')))
				return 'account_newsletter';
			elseif(in_array($this->route, $this->_get_urls('account_voucher_urls')))
				return 'account_voucher';
			elseif(in_array($this->route, $this->_get_urls('account_logout_urls')))
				return 'account_logout';
			elseif(in_array($this->route, $this->_get_urls('account_success_urls')))
				return 'account_success';
			elseif(in_array($this->route, $this->_get_urls('affiliate_register_urls')))
				return 'affiliate_register';
			elseif(in_array($this->route, $this->_get_urls('affiliate_login_urls')))
				return 'affiliate_login';
			elseif(in_array($this->route, $this->_get_urls('affiliate_success_urls')))
				return 'affiliate_success';
			elseif(in_array($this->route, $this->_get_urls('information_contact_urls')))
				return 'contact';
			elseif(in_array($this->route, $this->_get_urls('information_contact_success_urls')))
				return 'contact_success';
			elseif(in_array($this->route, $this->_get_urls('information_sitemap_urls')))
				return 'sitemap';
			else
				return htmlspecialchars($this->route, ENT_QUOTES, 'UTF-8');
        }

        public function get_current_list($product_details) {
		    $route_name = $this->get_current_view();
		    $routes_need_more_data = array('product', 'search', 'category', 'manufacturer', 'information');

		    if(in_array($route_name, $routes_need_more_data)) {
		        if($route_name == 'product') {
		            $product_details = !empty($product_details['product']) ? $product_details['product'] : '';

		            if(empty($product_details)) {
		                $product_id = $this->get_current_product_id();
		                if($product_id) {
                            $model = $this->get_product_data($product_id, array('model'));
                            $product_details = array(
                                'model' => !empty($model['model']) ? $model['model'] : '',
                                'name' => $this->get_product_name($product_id)
                            );
                        }
                    } else {
						$model = $product_details['sku'];
					}

		            if(!empty($product_details)) {
                        $route_name = $route_name . ': ' . $product_details['model'] . ' - ' . $product_details['name'];
                    }
                } else if($route_name == 'manufacturer' && $this->route_id) {
		            $route_name = $route_name.': '.$this->get_manufacturer_name($this->route_id);
                } else if($route_name == 'category' && $this->categories_id) {
		            $category_names = implode (' > ', $this->get_category_names_array_category_view(true));
		            $route_name = $route_name.': '.$category_names;
                } else if($route_name == 'search' && $this->search) {
		            $route_name = $route_name.': '.trim(ucfirst(strtolower($this->search)));
                } else if($route_name == 'information' && $this->route_id) {
		            $route_name = $route_name.': '.$this->get_information_title($this->route_id);
                }
            }

		    return str_replace("'", "\'", $route_name);
        }

        public function get_manufacturer_name($manufacturer_id) {
		    $manufacturer_name = $this->db->query('SELECT `name` FROM '. DB_PREFIX . 'manufacturer WHERE manufacturer_id = '.(int)$manufacturer_id);

		    if($manufacturer_name->num_rows == 1) {
		        return $manufacturer_name->row['name'];
			}

		    return '';
        }

        public function get_category_names_array_category_view($category_reverse = false) {
			$array_categories = array();
			$temp_categories = /*$category_reverse ? array_reverse($this->categories_id) :*/ $this->categories_id;
			
			foreach ($temp_categories as $key3 => $cat_id) {
				$category_info = $this->model_catalog_category->getCategory($cat_id);

				if(!empty($category_info['name']))
					$array_categories[] = $category_info['name'];
			}

			return $array_categories;
		}

		public function get_information_title($information_id) {
			if(!is_numeric($information_id)) {
				return '';
			}

            $information = $this->db->query('SELECT `title` FROM '. DB_PREFIX .'information_description WHERE information_id = '.$information_id.' AND language_id = '.$this->language_id);
            
			if($information->num_rows == 1) {
		        return $information->row['title'];
			}

		    return '';
        }

        public function load_models() {
			$this->load->model('tool/image');
		    $this->load->model('catalog/product');
			$this->load->model('catalog/category');
			$this->load->model('catalog/review');
			$this->load->model('checkout/order');
        }

		/**
		 * Get urls
		 * 
		 * @param string $url_key
		 * @return array
		 */
		private function _get_urls( $url_key = '' ) {
			if( empty($url_key) ) {
				return array();
			}

			if( $url_key === 'all' ) {
				return $this->urls;
			}

			return array_key_exists($url_key, $this->urls) ? $this->urls[$url_key] : array();
		}

        public function _load_controller($controller_name, $data = array()) {
            $controller_explode = explode('/', $controller_name);
            $filename = explode('.', end($controller_explode));

            if (count($filename) != 1) {
                array_pop($controller_explode);
                $controller_name = implode('/', $controller_explode).'/'.$filename[0];
            }

			$default_data = array('return' => false);

			$data = array_merge($default_data, $data);

            if(version_compare(VERSION, '2', '>=')) {
                return $this->load->controller($controller_name, $data);
			} else {
                return $this->getChild($controller_name, $data);
			}
        }

        public function set_route_and_route_id() {
			//JooCart compatibility
				if(!empty($this->request->get["option"]) && $this->request->get["option"] == 'com_opencart') {
					$jooCart = $unset_route = true;
					$view = isset($this->request->get['view']) ? $this->request->get['view'] : '';

					if ($view == 'categories' && !empty($this->request->get["path"])) { // Categoy view
						$this->request->get["_route_"] = $this->request->get["route"];
					} elseif ($view == 'categories' && empty($this->request->get["path"])) { // Product view
						$this->request->get["_route_"] = "product/product";
					} elseif ($view == 'cart') {
						$this->request->get["_route_"] = "checkout/cart";
					} elseif ($view == 'checkout') {
						$this->request->get["_route_"] = "checkout/checkout";
					} elseif ($view == 'home' && empty($this->request->get["route"])) {
						$this->request->get["_route_"] = "checkout/success";
					}
				}

		    if(empty($this->request->get["_route_"])) {
			    $this->route_id = '';
                $this->route = !empty($this->request->get["route"]) ? $this->request->get["route"] : 'common/home';
            } else {
		        $route_string = strlen($this->request->get["_route_"]) > 1 ? rtrim($this->request->get["_route_"], '/') : $this->request->get["_route_"];
                $route = $this->translateSEOURL($route_string, true);
                
                if(is_array($route)) {
                    $this->route = $route['route'];
                    $this->route_id = $route['id'];
                } else {
                    $this->route = $route;
                    $this->route_id = '';
                }
            }

			if (!empty($unset_route)) {
				unset($this->request->get["_route_"]);
			}
        }

        public function translateSEOURL($seo_url, $get_id = false) {
		    if(!empty($this->request->get['product_id']))
		        return 'product/product';

			$parts = explode('/', $seo_url);


            if(empty($parts[0]) && count($parts) > 1) {
                unset($parts[0]);
                $parts = array_values($parts);
            }

			//Devman Extensions - info@devmanextensions.com - 2017-07-29 11:18:09 - Cart compatibility
			if((!empty($parts[0]) && $parts[0] == 'cart') || (!empty($parts[1]) && empty($parts[0]) && $parts[1] == 'cart'))
				return 'checkout/cart';

			//First force the fixed urls.
				$urls = $this->_get_urls('checkout_cart_urls');
				if(in_array($seo_url, $urls))
					return 'checkout/cart';

				$urls = $this->_get_urls('checkout_checkout_urls');
				if(in_array($seo_url, $urls))
					return 'checkout/checkout';

				$urls = $this->_get_urls('checkout_success_urls');
				if(in_array($seo_url, $urls))
					return 'checkout/success';

			//Devman Extensions - info@devmanextensions.com - 2017-06-17 12:22:06 - Checkout compatibilities
				$checkout_checkout_urls = $this->_get_urls('checkout_checkout_urls');
				$custom_checkout = (!empty($parts[1]) && in_array($parts[1], $checkout_checkout_urls)) || (count($parts) == 1 && in_array($parts[0], $checkout_checkout_urls));
				if($custom_checkout || (count($parts) == 2 && empty($parts[0]) && $parts[1] == 'checkout'))
					return 'checkout/checkout';

			//Devman Extensions - info@devmanextensions.com - 2017-07-29 12:34:02 - Checkout success compatibility
			if(!empty($parts[0]) && $parts[0] == 'success' && !empty($parts[1]) && $parts[1] == 'checkout')
				return 'checkout/success';

			//Devman Extensions - info@devmanextensions.com - 28/5/21 13:42 - Array reverse for start check seo URL from last param.
                $parts = array_reverse($parts);

            //$parts = array_reverse($parts);
			foreach ($parts as $part) {
				if(!empty($part))
				{
					//Devman Extensions - info@devmanextensions.com - 2017-06-26 19:11:53 - Compatibility with some seo external extensions
						if (!empty($part) && strpos($part, '=') === false) {
							if($part == 'search')
								return 'product/search';
						}
					//END

					if(!$this->is_oc_3x)
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($part) . "'");
					else
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $this->db->escape($part) . "' AND language_id = ".$this->config->get('config_language_id')." AND store_id = ".$this->id_store);

					/*SEO BACKPACK ISENSE EXTENSION COMPATIBILITY - INSERT INTO oc_setting (`code`, `key`, `value`, `serialized`) VALUES ('google', 'google_backpack_isense_compatibility', '1', 0);*/
						if (empty($query->num_rows) && $this->seo_isense_bag_pack) {
							$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url_alias WHERE keyword = '" . $this->db->escape($part) . "'");
						}
					//END

					if ($query->num_rows) {

						$url = explode('=', $query->row['query']);

						if ($url[0] == 'product_id')
							return 'product/product';
						elseif ($url[0] == 'category_id')
							return 'product/category';
						elseif ($url[0] == 'manufacturer_id') {
						    if($get_id) {
						        return array(
						            'id' => array_key_exists(1, $url) ? $url[1] : '',
                                    'route' => 'product/manufacturer/info'
                                );
                            }
                            return 'product/manufacturer/info';
                        }
						elseif ($url[0] == 'information_id') {
						    if($get_id) {
						        return array(
						            'id' => array_key_exists(1, $url) ? $url[1] : '',
                                    'route' => 'information/information'
                                );
                            }
                            return 'information/information';
                        }

					}
				}
			}
			//Devman Extensions - info@devmanextensions.com - 2017-08-01 16:58:55 - If not found any seo url, return "Other"
			return $seo_url;
		}

        public function set_script($html_position, $script, $begin_array = false) {
		    if ($begin_array) {
		        $copy_scripts = array_key_exists($html_position, $this->scripts) ? $this->scripts[$html_position] : array();
		        $new_scripts = array();
		        $new_scripts[] = $script;
		        
				foreach ($copy_scripts as $code) {
		            $new_scripts[] = $code;
		        }

		        $this->scripts[$html_position] = $new_scripts;
            } else {
                $this->scripts[$html_position][] = $script;
			}
        }

        public function get_scripts($html_position = null, $string_format = true) {
		    if(!$html_position)
		        return $this->scripts;

		    if(!array_key_exists($html_position, $this->scripts))
		        return false;

		    if(!$string_format)
		        return $this->scripts[$html_position];

		    if($string_format && is_array($this->scripts[$html_position])) {
		        $string = '';
                foreach ($this->scripts[$html_position] as $id_code => $cod) {
                    $string .= $cod;
                }
                return $string;
            }
            return false;
        }

		public function is_products_list_view() {
			return in_array($this->route, $this->_get_urls('products_lists_urls'));
		}

		public function is_checkout_cart() {
			return in_array($this->route, $this->_get_urls('checkout_cart_urls'));
		}

		public function is_checkout_checkout() {
			return in_array($this->route, $this->_get_urls('checkout_checkout_urls'));
		}

		public function is_checkout_success() {
			return in_array($this->route, $this->_get_urls('checkout_success_urls'));
		}

        public function get_real_product_identificator($product, $product_identificator = 'product_id') {
			if(empty($product_identificator))
				return $product['product_id'];

		    if($product_identificator != 'product_id') {
                if(!array_key_exists($product_identificator, $product))
                    $identificator = $this->get_product_data($product['product_id'], array($product_identificator))[$product_identificator];
                else
                    $identificator = $product[$product_identificator];
            }

		    return !empty($identificator) ? $identificator : $product['product_id'];
        }

        public function get_current_product_id() {
		    $prod_id = isset($this->request->get) && array_key_exists('product_id', $this->request->get) ? $this->request->get['product_id'] : false;
		    return $prod_id;
        }

        public function get_product_data($product_id, $data) {
		    if(empty($product_id) || empty($data) || (count($data) == 1 && empty($data[0])))
		        return array();
		    $product_id = (int)$product_id;
		    $sql = "SELECT `".implode("` , `", $data)."` FROM `".DB_PREFIX."product` WHERE product_id = ".(int)$product_id;
		    $result = $this->db->query($sql);
		    $result = !empty($result->row) ? $result->row : array();
		    return $result;
        }

        public function get_product_field($product, $product_identificator = 'product_id') {
			if(!is_array($product))
				return '';

		    $product_identificator = empty($product_identificator) ? 'product_id' : $product_identificator;
		    if($product_identificator != 'product_id' && is_array($product)) {
                if(!array_key_exists($product_identificator, $product))
                    $identificator = $this->get_product_data($product['product_id'], array($product_identificator))[$product_identificator];
                else
                    $identificator = $product[$product_identificator];
            }

		    return !empty($identificator) ? $identificator : (!empty($product['product_id']) ? $product['product_id'] : '');
        }

        public function get_order_product_variant($order_id, $product_id) {
		    $variant = '';

            $options = $this->db->query('SELECT ord_opti.* FROM ' . DB_PREFIX . 'order_option ord_opti INNER JOIN '.DB_PREFIX.'order_product ord_prod ON (ord_prod.product_id = '.(int)$product_id.' AND ord_prod.order_product_id = ord_opti.order_product_id) WHERE ord_opti.order_id = '.(int)$order_id);
            foreach ($options->rows as $key => $opt) {
                $variant .= $opt['name'] . ': ' . $opt['value'];
                if ($key + 1 < count($options->rows))
                    $variant .= ' - ';
            }

            return $variant;
        }

        public function get_order_shipping_cost($order_info) {
            $shipping = 0;

            foreach ($order_info['totals'] as $key => $ord) {
            	if ($ord['code'] == 'shipping') {
				  	$shipping += $ord['value'];
			  	}
            }

            return $shipping;
        }

        public function get_order_tax_cost($order_info) {
		    $tax = 0;

            foreach ($order_info['totals'] as $key => $ord) {
              	if ($ord['code'] == 'tax') {
					$tax += $ord['value'];
			  	}
            }

            return $tax;
        }

        public function get_product_name($product_id) {
		    $product_id = (int)$product_id;
		    $sql = "SELECT `name` FROM `".DB_PREFIX."product_description` WHERE product_id = ".(int)$product_id." AND language_id = ".(int)$this->language_id;
		    $result = $this->db->query($sql);
		    return array_key_exists('name', $result->row) ? $result->row['name'] : '';
        }

        public function validate_order_status($order_id) {
		    $order_status_id = $this->getOrderStatus($order_id);

		    $order_statuses_valid = $this->config->get('google_all_positive_conversion_status_id_'.$this->id_store);

            if (empty($order_statuses_valid)) {
                return true;
			} else {
                if (in_array($order_status_id, $order_statuses_valid)) {
                    return true;
				}
                
				return false;
            }

            return false;
        }

		public function get_product_category_name($product_id) {
			$this->load->model('catalog/product');
			$product_categories = $this->model_catalog_product->getCategories($product_id);
			$this->load->model('catalog/category');

			if(!empty($product_categories[0]['category_id'])) {
				$category_info = $this->model_catalog_category->getCategory($product_categories[0]['category_id']);
			}

			return !empty($category_info['name']) ? str_replace("'", "\'", $category_info['name']) : '';
		}

		public function get_total_price_products($products) {
			$total = 0;
			foreach ($products as $key => $pro) {
			    $quantity = array_key_exists('quantity', $pro) ? $pro['quantity'] : 1;

			    if(!empty($pro['product']['prices'])) {
			        $total += $pro['product']['prices']['price']['price'];
				} else {
				    $total += $this->get_product_price($pro['product_id'], true)*$quantity;
				}
			}

			return $total;
		}

		public function get_product_ids_array($products, $force_index = '') {
			$product_ids = array();

            if (is_array($products)) {
				foreach ($products as $key => $prod) {
                    if(!empty($force_index)) {
                        $product_ids[] = $prod['product'][$force_index];
					} else {
                        $product_ids[] = $prod['product']['product_id'];
					}
                }
			}

			return $product_ids;
		}

		public function get_product_price($product_info, $from_product_id = false, $currency = false, $force_taxes = false) {
		    if(!is_array($product_info) && !$from_product_id)
		        return 0;

		    if($force_taxes) {
		        if (!array_key_exists('tax_class_id', $product_info))
                    $tax_class_id = $this->get_product_tax_class_id($product_info['product_id']);
		        else
		            $tax_class_id = $product_info['tax_class_id'];
            }

		    $final_price = 0;

		    if ($from_product_id)
                $product_info = $this->model_catalog_product->getProduct($product_info);

			if(empty($product_info['product_id']))
				return 0;

		    $product_from_order = array_key_exists('order_product_id', $product_info) && !empty($product_info['order_product_id']);

		    if($product_from_order) {
		        //$total = $this->db->query('SELECT `total` FROM '.DB_PREFIX.'order_product WHERE order_product_id='.(int)$product_info['order_product_id'])->row['total'];
				$total = $product_info['price'];

		        if(!empty($total)) {
		            if($force_taxes)
		                $total = $this->tax->calculate($total, $tax_class_id, $this->config->get('config_tax'));

                    $final_price = $this->format_price($total, true);
                }
            }

            if($currency) {
                $product_info = $this->model_catalog_product->getProduct($product_info['product_id']);
                $base_price = !empty($product_info['special']) ? $product_info['special'] : $product_info['price'];
                $force_currency_price = $this->format_price($this->tax->calculate($base_price, $product_info['tax_class_id'], $this->config->get('config_tax')), true, false, $currency);
                return $force_currency_price;
            }

            if (!$product_from_order && !empty($product_info) && is_array($product_info)) {
                if (!array_key_exists('tax_class_id', $product_info))
                    $product_info['tax_class_id'] = $this->get_product_tax_class_id($product_info['product_id']);

                if(!is_numeric($product_info['price'])) {
                    $product_info = $this->model_catalog_product->getProduct($product_info['product_id']);
                }

                $price = 0;

                if (!empty($product_info['special']))
                    $price = !is_numeric($product_info['special']) ? $product_info['special'] : round($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), 2);
                elseif (!empty($product_info['price']))
                    $price = !is_numeric($product_info['price']) ? $product_info['price'] : round($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), 2);

                $final_price = $this->format_price($price, true);
            }

            return $final_price;
		}

		public function get_product_image_url($product_info) {
			$width = 500;
			$height = 500;

			if(version_compare(VERSION, '2.1.0.2.1', '<='))
			{
				$width = $this->config->get('config_image_thumb_width');
				$height = $this->config->get('config_image_thumb_height');
			}
			elseif(version_compare(VERSION, '2.1.0.2.1', '>') && version_compare(VERSION, '2.3.0.2', '<='))
			{
				$width = $this->config->get($this->config->get('config_theme') . '_image_thumb_width');
				$height = $this->config->get($this->config->get('config_theme') . '_image_thumb_height');
			}
			else
			{
				$width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width');
				$height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height');
			}

			$width = empty($width) ? 500 : $width;
			$height = empty($height) ? 500 : $height;

			return $this->model_tool_image->resize($product_info['image'], $width, $height);
		}

		public function get_product_url($product_id) {
            return $this->url->link('product/product','product_id=' . $product_id);
        }

        public function get_product_prices($pro) {
		    if(!array_key_exists("tax_class_id", $pro) && !empty($pro['product_id'])) {
		        $product_info = $this->db->query("SELECT price, tax_class_id FROM ".DB_PREFIX."product WHERE product_id = ".$pro['product_id'])->row;
		        $pro['price'] = $product_info['price'];
		        $pro['special'] = !empty($pro['special']) ? $pro['special'] : 0;
		        //CREO QUE TENGO QUE HACER UNA QUERY A PRODUCT_SPECIAL... MAL.
				$pro['tax_class_id'] = $product_info["tax_class_id"];
            }

            $final_price = !empty($pro['special']) ? $pro['special'] : $pro['price'];
            $product_price = $this->format_price($pro['price']);
            $price_price_with_taxes = $this->format_price($this->tax->calculate($pro['price'], $pro['tax_class_id'], true));
            $product_special = !empty($pro['special']) ? $this->format_price($pro['special']) : 0;
            $product_special_with_taxes = !empty($pro['special']) ? $this->format_price($this->tax->calculate($pro['special'], $pro['tax_class_id'], true)) : 0;
            $price = !empty($pro['special']) ? $product_special : $product_price;
            $price_with_taxes = !empty($pro['special']) ? $product_special_with_taxes : $price_price_with_taxes;
            $price_eur = $this->format_price($final_price, true, false, "EUR");
            $price_eur_with_taxes = $this->format_price($this->tax->calculate($final_price, $pro['tax_class_id'], $this->config->get('config_tax')), true, false, "EUR");

            return array(
                "price" => array(
                    'price' => $price_with_taxes,
                    'without_tax' => $price,
                    'taxes' => $this->format_price($price_with_taxes-$price)
                ),
                "base_price" => array(
                    'price' => $price_price_with_taxes,
                    'without_tax' => $product_price,
                    'taxes' => $this->format_price($price_price_with_taxes-$product_price)
                ),
                "special" => array(
                    'price' => $product_special_with_taxes,
                    'without_tax' => $product_special,
                    'taxes' => $this->format_price($product_special_with_taxes-$product_special)
                ),
                "price_euro" => array(
                    "price" => $price_eur_with_taxes,
                    "without_tax" => $price_eur,
                    'taxes' => $this->format_price($price_eur_with_taxes-$price_eur)
                )
            );
        }

        public function format_price($price, $currency = true, $thousands = false, $currency_code = false) {
		    if($currency_code && $this->db->query("SELECT currency_id FROM " . DB_PREFIX . "currency WHERE code = '".$currency_code."'")->num_rows != 1)
                return false;

		    if(!$currency_code)
		        $currency_code = $this->currency_code;

		    $price = $this->currency->format($price, $currency_code, '', false);

			/*$decimal_separator = !$thousands ? '.' : ',';
			$thousands_separator = !$thousands ? '' : '.';
			return number_format((float)$price, 2, $decimal_separator, $thousands_separator);*/
			return $this->format_number($price);
		}

		public function format_number($number) {
		    return number_format((float)$number, 2, '.', '');
        }

		public function get_client_ip() {
            $ip = '';
            if (isset($_SERVER['HTTP_CLIENT_IP']))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            else if(isset($_SERVER['HTTP_X_FORWARDED']))
                $ip = $_SERVER['HTTP_X_FORWARDED'];
            else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
                $ip = $_SERVER['HTTP_FORWARDED_FOR'];
            else if(isset($_SERVER['HTTP_FORWARDED']))
                $ip = $_SERVER['HTTP_FORWARDED'];
            else if(isset($_SERVER['REMOTE_ADDR']))
                $ip = $_SERVER['REMOTE_ADDR'];
            else
                $ip = '';

            if (strpos($ip, ', ') !== false) {
                $ip = explode(", ", $ip)[0];
            }
            return $ip;
        }

        public function get_page_url() {
            $url = !empty($_SERVER['HTTPS']) ? "https://" : "http://";
            $url .= $_SERVER['HTTP_HOST'];
            $url .= $_SERVER['REQUEST_URI'];
            return $url;
        }

		public function get_product_manufacturer($product_id) {
		    if(empty($product_id))
		        return '';

			$sql = "SELECT ma.name FROM " . DB_PREFIX . "product pr LEFT JOIN " . DB_PREFIX . "manufacturer ma ON (pr.manufacturer_id = ma.manufacturer_id) WHERE pr.product_id = ".(int)$product_id;
			$results = $this->db->query($sql);

			return !empty($results->row['name']) ? $results->row['name'] : '';
		}

		public function get_product_tax_class_id($product_id) {
		    if(empty($product_id))
		        return '';
			$sql = "SELECT pr.tax_class_id FROM " . DB_PREFIX . "product pr WHERE pr.product_id = ".(int)$product_id;
			$results = $this->db->query($sql);

			return !empty($results->row['tax_class_id']) ? $results->row['tax_class_id'] : '';
		}

		public function get_option_id_from_product_option_value_id($product_option_value_id) {
		    $product_option_value_id = (int)$product_option_value_id;
		    $option_id = $this->db->query("SELECT option_id FROM ".DB_PREFIX."product_option_value WHERE product_option_value_id = ".$product_option_value_id);
		    $option_id = !empty($option_id->row['option_id']) ? $option_id->row['option_id'] : '';
		    return $option_id;
        }

        public function get_option_value_name_from_product_option_value_id($product_option_value_id) {
		    $product_option_value_id = (int)$product_option_value_id;
		    $option_value_name = $this->db->query("SELECT ovd.name  FROM ".DB_PREFIX."product_option_value pov LEFT JOIN ".DB_PREFIX."option_value_description ovd ON(pov.option_value_id = ovd.option_value_id) WHERE product_option_value_id = ".$product_option_value_id);
		    $option_value_name = !empty($option_value_name->row['name']) ? $option_value_name->row['name'] : '';
		    return $option_value_name;
        }

        public function get_product_options($product_id) {
		    $product_options = $this->model_catalog_product->getProductOptions($product_id);
		    $options_formated = array();

		    foreach ($product_options as $key => $prod_opt) {
		        $options_formated[$prod_opt['option_id']] = array(
		            'name' => $prod_opt['name'],
                     'option_values' => array()
                );

		        $opt_vals = array();
				if(version_compare(VERSION, '2', '>=')) {
					foreach ($prod_opt['product_option_value'] as $key2 => $opt_val) {
						$opt_vals[$opt_val['option_value_id']] = $opt_val['name'];
					}
				} else {
					foreach ($prod_opt['option_value'] as $key2 => $opt_val) {
						$opt_vals[$opt_val['option_value_id']] = $opt_val['name'];
					}
				}
                $options_formated[$prod_opt['option_id']]['option_values'] = $opt_vals;
		    }
		    return $options_formated;
        }

		public function get_product_variant($product_id, $options_choosed) {
			$product_options = $this->model_catalog_product->getProductOptions($product_id);
			$product_options = $this->format_options($product_options);

			foreach ($options_choosed as $product_option_id => $option_value_id) {

				if(is_array($option_value_id))
					$option_values_id = $option_value_id;
				else
					$option_values_id = array($option_value_id);

				foreach ($option_values_id as $key => $opt_val_id) {

					$key_exists = array_key_exists($product_option_id, $product_options) && array_key_exists($opt_val_id, $product_options[$product_option_id]);

					if($key_exists && !empty($product_options[$product_option_id][$opt_val_id]))
						return $product_options[$product_option_id][$opt_val_id];
				}

			}

			return '';
		}

		public function get_product_variant_order_success($options) {
			foreach ($options as $key => $opt) {
				return $opt['name'].(!empty($opt['value']) ? ': '.$opt['value'] : '');
			}
		}

		public function get_customer_info() {
		    $customer_data = array();

		    if($this->customer->isLogged()) {

		        $customer_id = $this->customer->getId();
                $customer_group_id = version_compare(VERSION, '2', '>=') ? $this->customer->getGroupId() : $this->customer->getCustomerGroupId();

                $customer_data = array(
                    'customer_id' => $this->customer->getId(),
                    'firstname' => $this->customer->getFirstName(),
                    'lastname' => $this->customer->getLastName(),
                    'email' => $this->customer->getEmail(),
                    'phone' => $this->customer->getTelephone(),
                );

                if ($address_id = $this->customer->getAddressId()) {
                    $address = $this->db->query("SELECT city, zone_id, address_1, postcode, country_id FROM " . DB_PREFIX . "address WHERE address_id = " . $address_id)->row;
                    if(!empty($address['zone_id'])) {
                        $zone = $this->db->query("SELECT name, code FROM " . DB_PREFIX . "zone WHERE zone_id = " . (int)$address['zone_id'])->row;
                        $address['zone'] = !empty($zone['name']) ? $zone['name'] : '';
                        $address['zone_code'] = !empty($zone['code']) ? $zone['code'] : '';

                        $country = $this->db->query("SELECT name, iso_code_2 FROM " . DB_PREFIX . "country WHERE country_id = " . (int)$address['country_id'])->row;
                        $address['country'] = !empty($country['name']) ? $country['name'] : '';
                        $address['iso_code_2'] = !empty($country['iso_code_2']) ? $country['iso_code_2'] : '';
                    }

					$address['zone'] = !empty($zone['name']) ? $zone['name'] : '';

                } else if(!empty($this->session->data['payment_address']))
                    $address = $this->session->data['payment_address'];

            } else if(!empty($this->session->data['guest'])) {

                $customer_data = array(
                    'firstname' => !empty($this->session->data['guest']['firstname']) ? $this->session->data['guest']['firstname'] : '',
                    'lastname' => !empty($this->session->data['guest']['lastname']) ? $this->session->data['guest']['lastname'] : '',
                    'email' => !empty($this->session->data['guest']['email']) ? $this->session->data['guest']['email'] : '',
                    'phone' => !empty($this->session->data['guest']['telephone']) ? $this->session->data['guest']['telephone'] : '',
                );

                $address = !empty($this->session->data['payment_address']) ? $this->session->data['payment_address'] : false;
            } else if(!empty($this->session->data['payment_address'])) {
                $customer_data = array(
                    'firstname' => !empty($this->session->data['payment_address']['firstname']) ? $this->session->data['payment_address']['firstname'] : '',
                    'lastname' => !empty($this->session->data['payment_address']['lastname']) ? $this->session->data['payment_address']['lastname'] : '',
                    'email' => '',
                    'phone' => '',
                );

                $address = $this->session->data['payment_address'];
            }

            if(!empty($address)) {
				if(!empty($address['address_1']))
                    $customer_data['address'] = $address['address_1'];
                if(!empty($address['city']))
                    $customer_data['city'] = $address['city'];
                if(!empty($address['zone']))
                    $customer_data['zone'] = $address['zone'];
                if(!empty($address['zone_code']))
                    $customer_data['zone_code'] = $address['zone_code'];
                if(!empty($address['postcode']))
                    $customer_data['postcode'] = $address['postcode'];
                if(!empty($address['country']))
                    $customer_data['country'] = $address['country'];
                if(!empty($address['iso_code_2']))
                    $customer_data['country_code'] = $address['iso_code_2'];
                if(!empty($customer_id))
                    $customer_data['customer_id'] = $customer_id;
                if(!empty($customer_group_id))
                    $customer_data['customer_group_id'] = $customer_group_id;
            }

            if(!empty($customer_data)) {
		        foreach ($customer_data as $key => $val) {
		            $customer_data[$key] = $val ? trim($val) : '';
		        }
            }

            if(empty($customer_data['email']) && !empty($this->session->data['order_id'])) {
		        $query = $this->db->query("SELECT email FROM `".DB_PREFIX."order` WHERE order_id = ".(int)$this->session->data['order_id']);
		        $customer_data["email"] = !empty($query->row['email']) ? $query->row['email'] : '';
            }

            if(!empty($customer_data["email"])) {
		        $this->session->data['gmt_customer_email'] = $customer_data["email"];
            }

            return $customer_data;
        }

		public function format_options($options) {
			$final_options = array();

			foreach ($options as $key => $opt) {
				if(!isset($final_options[$opt['product_option_id']]))
					$final_options[$opt['product_option_id']] = array();

				$option_values = version_compare(VERSION, '2.0.0.0', '>=') ? $opt['product_option_value'] : $opt['option_value'];

				if(!empty($option_values))
				{
					foreach ($option_values as $key2 => $optval) {
						$final_options[$opt['product_option_id']][$optval['product_option_value_id']] = $opt['name'].': '.$optval['name'];
					}
				}
			}

			return $final_options;
		}

		public function get_cart_products() {
			$products = $this->cart->getProducts();

			//Check for specials
			foreach ($products as $key => $product) {
				if(!empty($product['special']) && !empty($product['price_base'])) {
					$products[$key]['price'] = $product['price_base'];
				}
			}

			return $products;
		}

		public function startPathCategoryFromSEOURL($seo_url) {
			$parts = explode('/', $seo_url);
			//$parts = array_reverse($parts);

			$categories_id = array();

			foreach ($parts as $part) {
				if(!$this->is_oc_3x)
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($part) . "'");
				else
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $this->db->escape($part) . "' AND language_id = ".$this->config->get('config_language_id')." AND store_id = ".$this->id_store);

				if ($query->num_rows) {
					$url = explode('=', $query->row['query']);
					if ($url[0] == 'category_id')
						$categories_id[] = $url[1];
				}
			}

			return $categories_id;
		}

		public function getOrder($order_id) {
		    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = " . (int)$order_id);

			return !empty($query->rows) ? $query->rows[0] : array();
        }

        public function getOrderTotals($order_id) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");

			return $query->rows;
		}

		public function getOrderSubtotal($order_id) {
			$query = $this->db->query("SELECT `value` FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' and code = 'sub_total'");

			return $query->num_rows ? $this->format_price($query->row['value']) : 0;
		}

		public function getOrderTotalCustom($order_id, $total_codes) {
            $condition_in = "'".implode("', '", $total_codes)."'";
            $result = $this->db->query("SELECT SUM(`value`) as total FROM ".DB_PREFIX."order_total WHERE order_id = ".(int)$order_id." AND `code` IN (".$condition_in.")");
            return $result->row['total'];
        }

		public function getOrderProducts($order_id) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

			return $query->rows;
		}

		public function getOrderStatus($order_id) {
		    $query = $this->db->query("SELECT order_status_id FROM `" . DB_PREFIX . "order` WHERE order_id = " . (int)$order_id);

			return !empty($query->row) ? $query->row['order_status_id'] : '';
        }

		public function get_language_code() {
			return explode("-", $this->language_code)[0];
		}

		public function get_gcl_dc() {
			if (isset($_GET['gclid']) && empty($_COOKIE['_gcl_aw'])) {
				$_COOKIE['_gcl_aw'] = $_GET['gclid'];
			}

			if (!empty($_COOKIE['_gcl_aw'])) {
				return '_gcl_aw|'.$_COOKIE['_gcl_aw'];
			} elseif (!empty($_COOKIE['_gcl_dc'])) {
				return '_gcl_dc|'.$_COOKIE['_gcl_dc'];
			} elseif (!empty($_COOKIE['_gcl_gb'])) {
				return '_gcl_gb|'.$_COOKIE['_gcl_gb'];
			}

			return '';
		}

		public function get_fbclid() {
			$fbc = '';

			if (isset($_COOKIE['_fbc']) && !empty($_COOKIE['_fbc'])) {
				$fbc = $_COOKIE['_fbc'];
			} else if (isset($_GET['fbclid']) && !empty($_GET['fbclid'])) {
				$fbclid = $_GET['fbclid'];
				// Formatear el fbc
				$fbc = 'fb.1.' . time() . '.' . $fbclid;
				setcookie('_fbc', $fbc, time() + 90 * 24 * 60 * 60, '/'); // Duración de 90 días
			}

			return $fbc;
		}

		public function get_ttclid() {
			if (isset($this->request->get['ttclid']) && !empty($this->request->get['ttclid'])) {
				return $this->request->get['ttclid'];
			}
			
			return isset($_COOKIE['ttclid']) && !empty($_COOKIE['ttclid']) ? $_COOKIE['ttclid'] : '';
		}

		public function get_ttp() {
			return isset($_COOKIE['_ttp']) && !empty($_COOKIE['_ttp']) ? $_COOKIE['_ttp'] : '';
		}

		public function get_msclkid() {
			if (!empty($_GET['msclkid'])) {
				return $_GET['msclkid'];
			} elseif (!empty($_COOKIE['uetmsclkid'])) {
				return $_COOKIE['uetmsclkid'];
			} else {
				return '';
			}
		}

		public function get_client_id() {

			if(!empty($this->session->data['ga4_client_id']))
				return $this->session->data['ga4_client_id'];

			$client_id = '';
			$cookie_name = '_ga';

			if (isset($_COOKIE[$cookie_name])) {
				$cookie_value = $_COOKIE[$cookie_name];

				$parts = explode('.', $cookie_value);

				if (count($parts) > 2) {
					$client_id = (int)$parts[2].'.'.(int)$parts[3]; // Extract the client_id
				}
			}

			$this->session->data['ga4_client_id'] = $client_id;

			return $this->session->data['ga4_client_id'];
		}

		public static function get_google_consent_cookies() {
			$consent_config = array();
			$google_consent_v2_categories = array(
				'necessary'     => array('security_storage'),
				'preferences'   => array('functionality_storage', 'personalization_storage'),
				'statistics'    => array('analytics_storage'),
				'marketing'     => array('ad_storage', 'ad_user_data', 'ad_personalization')
			);
	
			foreach ($google_consent_v2_categories as $category => $cookies) {
				$category_conf = array();
	
				foreach( $cookies as $cookie_name ) {
					$category_conf[$cookie_name] = isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : 'granted';
				}
	
				$consent_config[$category] = $category_conf;
			}
	
			return $consent_config;
		}
	
		public static function can_track_the_conversion( $cookies_config ) {
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

		// Function to detect the traffic source
		public function detectTrafficSource() {
			$client_id = $this->get_client_id();

			// ⭐ IMPORTANTE: Inicializar estructura completa si no existe
			if (!isset($this->session->data['traffic_source'])) {
				$this->session->data['traffic_source'] = array(
					'source' => '(direct)',
					'medium' => '(none)',
					'campaign' => '(not set)',
					'term' => '',
					'content' => '',
					'client_id' => $client_id,
					'timestamp' => time()
				);
			}

			// Actualizar client_id siempre
			$this->session->data['traffic_source']['client_id'] = $client_id;

			// Obtener referrer y URL actual
			$referrer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
			$currentUrl = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

			// Parsear parámetros de la URL
			$queryParams = [];
			$query_string = parse_url($currentUrl, PHP_URL_QUERY);
			if ($query_string) {
				parse_str($query_string, $queryParams);
			}

			// ⭐ DETECCIÓN DE NUEVA SESIÓN DE CAMPAÑA
			$has_new_campaign = false;
			$new_source = null;
			$new_medium = null;
			$new_campaign = null;

			// Prioridad 1: Parámetros UTM completos
			if (!empty($queryParams['utm_source']) && !empty($queryParams['utm_medium'])) {
				$has_new_campaign = true;
				$new_source = $queryParams['utm_source'];
				$new_medium = $queryParams['utm_medium'];
				$new_campaign = !empty($queryParams['utm_campaign']) ? $queryParams['utm_campaign'] : '(not set)';
			}
			// Prioridad 2: GCLID (Google Ads)
			elseif (!empty($queryParams['gclid'])) {
				$has_new_campaign = true;
				$new_source = 'google';
				$new_medium = 'cpc';
				$new_campaign = !empty($queryParams['utm_campaign']) ? $queryParams['utm_campaign'] : '(cpc)';

				// Guardar gclid para tracking
				$this->session->data['gclid'] = $queryParams['gclid'];
				$this->session->data['gclid_timestamp'] = time();
			}
			// Prioridad 3: FBCLID (Facebook Ads)
			elseif (!empty($queryParams['fbclid'])) {
				$has_new_campaign = true;
				$new_source = 'facebook';
				$new_medium = 'cpc';
				$new_campaign = !empty($queryParams['utm_campaign']) ? $queryParams['utm_campaign'] : '(cpc)';

				// Guardar fbclid para tracking
				$this->session->data['fbclid'] = $queryParams['fbclid'];
				$this->session->data['fbclid_timestamp'] = time();
			}

			// ⭐ ACTUALIZAR SOLO SI HAY NUEVA CAMPAÑA O ES PRIMERA VISITA
			if ($has_new_campaign) {
				// Nueva campaña detectada - actualizar todo
				$this->session->data['traffic_source']['source'] = $new_source;
				$this->session->data['traffic_source']['medium'] = $new_medium;
				$this->session->data['traffic_source']['campaign'] = $new_campaign;
				$this->session->data['traffic_source']['term'] = !empty($queryParams['utm_term']) ? $queryParams['utm_term'] : '';
				$this->session->data['traffic_source']['content'] = !empty($queryParams['utm_content']) ? $queryParams['utm_content'] : '';
				$this->session->data['traffic_source']['timestamp'] = time();

				// Guardar click IDs si existen
				if (!empty($queryParams['gclid'])) {
					$this->session->data['traffic_source']['gclid'] = $queryParams['gclid'];
				}
				if (!empty($queryParams['fbclid'])) {
					$this->session->data['traffic_source']['fbclid'] = $queryParams['fbclid'];
				}
			}
			// Si no hay campaña nueva pero es direct y tenemos referrer
			elseif ($this->session->data['traffic_source']['source'] === '(direct)' && !empty($referrer)) {
				$referrer_host = parse_url($referrer, PHP_URL_HOST);
				$current_host = $_SERVER['HTTP_HOST'];

				// Solo actualizar si el referrer es externo
				if ($referrer_host !== $current_host) {
					// Detectar fuente basada en referrer
					$detected = $this->detectSourceFromReferrer($referrer, $referrer_host);

					$this->session->data['traffic_source']['source'] = $detected['source'];
					$this->session->data['traffic_source']['medium'] = $detected['medium'];
					$this->session->data['traffic_source']['campaign'] = '(not set)';
					$this->session->data['traffic_source']['timestamp'] = time();
				}
			}

			// ⭐ VALIDAR PERSISTENCIA DE CLICK IDS
			// Si tenemos gclid/fbclid guardados y son recientes (menos de 2 horas)
			if (empty($has_new_campaign)) {
				if (!empty($this->session->data['gclid']) &&
					(time() - $this->session->data['gclid_timestamp']) < 7200 &&
					$this->session->data['traffic_source']['source'] === '(direct)') {

					$this->session->data['traffic_source']['source'] = 'google';
					$this->session->data['traffic_source']['medium'] = 'cpc';
					$this->session->data['traffic_source']['campaign'] = '(cpc)';
				}
				elseif (!empty($this->session->data['fbclid']) &&
					(time() - $this->session->data['fbclid_timestamp']) < 7200 &&
					$this->session->data['traffic_source']['source'] === '(direct)') {

					$this->session->data['traffic_source']['source'] = 'facebook';
					$this->session->data['traffic_source']['medium'] = 'cpc';
					$this->session->data['traffic_source']['campaign'] = '(cpc)';
				}
			}

			// ⭐ GUARDAR INFORMACIÓN ADICIONAL
			$this->session->data['traffic_source']['page_referrer'] = $referrer;
			$this->session->data['traffic_source']['landing_page'] = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$this->session->data['traffic_source']['last_updated'] = time();

			return $this->session->data['traffic_source'];
		}

		private function detectSourceFromReferrer($referrer, $referrer_host) {
			// Motores de búsqueda
			if (strpos($referrer_host, 'google.') !== false) {
				return ['source' => 'google', 'medium' => 'organic'];
			}
			elseif (strpos($referrer_host, 'bing.') !== false) {
				return ['source' => 'bing', 'medium' => 'organic'];
			}
			elseif (strpos($referrer_host, 'yahoo.') !== false) {
				return ['source' => 'yahoo', 'medium' => 'organic'];
			}
			elseif (strpos($referrer_host, 'duckduckgo.') !== false) {
				return ['source' => 'duckduckgo', 'medium' => 'organic'];
			}
			// Redes sociales
			elseif (strpos($referrer_host, 'facebook.') !== false || strpos($referrer_host, 'fb.') !== false) {
				// Detectar si es Facebook Ads
				if (strpos($referrer, 'fbclid=') !== false ||
					strpos($referrer_host, 'l.facebook.com') !== false ||
					strpos($referrer_host, 'lm.facebook.com') !== false) {
					return ['source' => 'facebook', 'medium' => 'cpc'];
				}
				return ['source' => 'facebook', 'medium' => 'social'];
			}
			elseif (strpos($referrer_host, 'instagram.') !== false) {
				return ['source' => 'instagram', 'medium' => 'social'];
			}
			elseif (strpos($referrer_host, 'twitter.') !== false || strpos($referrer_host, 'x.com') !== false) {
				return ['source' => 'twitter', 'medium' => 'social'];
			}
			elseif (strpos($referrer_host, 'linkedin.') !== false) {
				return ['source' => 'linkedin', 'medium' => 'social'];
			}
			elseif (strpos($referrer_host, 'pinterest.') !== false) {
				return ['source' => 'pinterest', 'medium' => 'social'];
			}
			elseif (strpos($referrer_host, 'youtube.') !== false) {
				return ['source' => 'youtube', 'medium' => 'social'];
			}
			// Email providers
			elseif (strpos($referrer_host, 'mail.google.') !== false ||
				strpos($referrer_host, 'mail.yahoo.') !== false ||
				strpos($referrer_host, 'outlook.') !== false) {
				return ['source' => 'email', 'medium' => 'email'];
			}
			// Referral genérico
			else {
				return ['source' => $referrer_host, 'medium' => 'referral'];
			}
		}

		public function save_datalayer_data( $data ) {
			if (is_array($data) && !empty($data)) {
				$order_id = isset($data['order_id']) ? $data['order_id'] : 0;

				$consent_config = self::get_google_consent_cookies();
		
				if ($order_id) {					
					$this->db->query("INSERT INTO `" . DB_PREFIX . "order_google_all` SET `order_id` = '" . (int)$order_id . "', `gmt_data` = '" . $this->db->escape(json_encode($data)) . "', `consent`='" . $this->db->escape(json_encode($consent_config)) . "'");
				}
			}
		}

        public function string_encrypt($string) {
            $encrypt_method = "AES-128-CTR";
            $iv_length = openssl_cipher_iv_length($encrypt_method);
            $options = 0;
            $vector = '1548556498215783';
            $key = "JDURY3HFYRHF";

            $string = openssl_encrypt($string, $encrypt_method,
                        $key, $options, $vector);

            return $string;
        }

        public function string_decrypt($string) {
            $encrypt_method = "AES-128-CTR";
            $iv_length = openssl_cipher_iv_length($encrypt_method);
            $options = 0;
            $vector = '1548556498215783';
            $key = "JDURY3HFYRHF";

            $string = openssl_decrypt($string, $encrypt_method,
                        $key, $options, $vector);

            return $string;
        }

        public function generate_uuid() {
		    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
		        mt_rand( 0, 0xffff ),
		        mt_rand( 0, 0x0fff ) | 0x4000,
		        mt_rand( 0, 0x3fff ) | 0x8000,
		        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		    );
		}

		public function debug_enabled() {
		    return $this->config->get('google_all_debug_mode_'.$this->id_store) && $this->user->isLogged();
        }
	}
?>
