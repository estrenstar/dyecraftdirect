<?php
class ControllerExtensionFeedContentapi extends Controller {

	private $service;
	private $domain;
	private $config_domain;
	private $lang_prefix = [];
	private $setting = [];
	private $blocked = [];
	private $google_categories = [];
	private $fields = [];
	private $prefix = 'module_';

	public function __construct($registry) {
		parent::__construct($registry);

		set_time_limit(0);
		ini_set('memory_limit', '4096M');

		if ($this->request->server['HTTPS']) {
			$this->config_domain = $this->domain = $this->config->get('config_ssl');
		} else {
			$this->config_domain = $this->domain = $this->config->get('config_url');
		}

		if (version_compare(VERSION, '3.0', '>=')) {
			if (!$this->config->get('module_seoproml_status')) {
				$this->lang_prefix = $this->config->get('feed_contentapi_prefix');
			}
		} else {
			if (!$this->config->get('seoproml_status')) {
				$this->lang_prefix = $this->config->get('contentapi_prefix');
			}
		}

		$this->load->model('extension/feed/contentapi');
		$this->fields = $this->model_extension_feed_contentapi->getProductFields();
		$this->google_categories = $this->cache->get('contentapi.categories');
		if (empty($this->google_categories)) {
			$this->google_categories = $this->model_extension_feed_contentapi->getCategories();
			$this->cache->set('contentapi.categories', $this->google_categories);
		}
	}

	protected function authorize() {
		require_once DIR_SYSTEM . 'library/contentapi/vendor/autoload.php';
		$client = new Google_Client();
		$client->setAuthConfig(DIR_CONFIG . 'content_api_'.$this->setting['feed_id'].'.json');
		$client->setScopes(Google_Service_ShoppingContent::CONTENT);  
		$this->service = new Google_Service_ShoppingContent($client);
		if ($this->setting['monolog']) {
			$logHandler = new Monolog\Handler\StreamHandler(DIR_LOGS . 'contentapi_'.$this->setting['feed_id'].'.log');
			$logHandler->setFormatter(new Monolog\Formatter\LineFormatter(null,null,true));
			$monolog = new Monolog\Logger('http-log');
			$monolog->pushHandler($logHandler);
			$clientHandler = $client->getHttpClient()->getConfig('handler');
			$clientHandler->push(GuzzleHttp\Middleware::log($monolog, new GuzzleHttp\MessageFormatter(GuzzleHttp\MessageFormatter::DEBUG)));
		}
	}

	protected function getFeedSetting($feed_id) {
		$this->setting = $this->model_extension_feed_contentapi->getFeed($feed_id);
		if (!empty($this->setting)) {
			$arrays = ['stock_assign','condition_assign','filter','filterv','filtera','filterva','filterf','filtervf','custom_label','shipping','category','product','manufacturer','categorybl','productbl','manufacturerbl','cron_action','destination','excluded','store_code','dimension_assign','weight_assign','settings'];
			foreach ($arrays as $field) {
				if (!empty($this->setting[$field])) {
					$this->setting[$field] = json_decode($this->setting[$field], true);
				} else {
					$this->setting[$field] = [];
				}
			}

			if (empty($this->setting['channel'])) {
				$this->setting['channel'] = 'online';
			}

			if (empty($this->setting['store_id'])) {
				$this->setting['store_id'] = 0;
			} else {
				$this->load->model('setting/store');
				$multistores = $this->model_setting_store->getStores();
				foreach ($multistores as $multistore) {
					if ($multistore['store_id'] == $this->setting['store_id']) {
						$this->setStore($multistore);
						if ($this->request->server['HTTPS']) {
							$this->domain = $multistore['ssl'];
						} else {
							$this->domain = $multistore['url'];
						}
						break;
					}
				}
			}
			
			$this->load->model('localisation/language');
			$language = $this->model_localisation_language->getLanguage($this->setting['language']);
			if (!empty($language)) {
				$this->setting['content_language'] = substr($language['code'], 0, 2);
				if ($this->setting['content_language'] == 'ua') $this->setting['content_language'] = 'uk';
				$this->setting['language_code'] = $language['code'];
			}
		} else {
			exit('Feed is disabled');
		}
	}

	protected function getFeedProducts($filter_data) {
		if ($this->setting['select'] == 'products') {
			$products = $this->setting['product'];
		} else {
			$products = $this->model_extension_feed_contentapi->getProducts($filter_data);
			$blacklist_products = $this->model_extension_feed_contentapi->getBlacklistProducts($this->setting['feed_id']);
					
			if (!empty($products) && !empty($blacklist_products)) {
				$products = array_diff($products, $blacklist_products);
			}
			unset($blacklist_products, $filter_data);
		}

		return $products;
	}

	protected function getFilterData() {
		return [
			'select' => $this->setting['select'],
			'categories' => $this->setting['category'],
			'sub_category' => $this->setting['sub_category'],
			'manufacturers' => $this->setting['manufacturer'],
			'filter' => $this->setting['filter'],
			'filterv' => $this->setting['filterv'],
			'filtera' => $this->setting['filtera'],
			'filterva' => $this->setting['filterva'],
			'filtervf' => $this->setting['filtervf'],
			'price_from' => $this->setting['price_from'],
			'price_to' => $this->setting['price_to'],
			'minimum' => $this->setting['minimum'],
			'noimage' => isset($this->setting['noimage'])?$this->setting['noimage']:'',
			'suppler' => $this->setting['suppler'],
			'store_id' => $this->setting['store_id']
		];
	}

	public function index() {
		// This is handler for event "add/edit product"
		if (!empty($this->request->get['feed_id'])) {
			$this->getFeedSetting($this->request->get['feed_id']);
		} else {
			exit('Empty Feed ID');
		}

		if (!empty($this->request->post['product_id']) && !empty($this->request->post['request_type']) && !empty($this->request->post['token']) && $this->request->post['token'] == md5($this->setting['merchantid'])) {
			$product_id = $this->request->post['product_id'];
			$request_type = (string)$this->request->post['request_type'];

			$filter_data = $this->getFilterData();
			$products = $this->getFeedProducts($filter_data);

			if (!empty($products) && in_array($product_id, $products)) {
				unset($products);
				$this->sendRequest($product_id, $request_type);
			}
		}
	}

	public function updateCatalog() {
		if (!empty($this->request->get['feed_id'])) {
			$this->getFeedSetting($this->request->get['feed_id']);
		} else {
			exit('Empty Feed ID');
		}

		$json = [];
		$json['counter'] = 0;

		if (!empty($this->request->post['token']) && $this->request->post['token'] == md5($this->setting['merchantid'])) {

			if ($this->setting['select']=='products') {

				$products = $this->setting['product'];
				$json['percent'] = 100;

			} else {

				$filter_data = $this->getFilterData();
				$filter_data['limit'] = !empty($this->setting['limit']) ? (int)$this->setting['limit'] : 50;
				
				if ($this->request->get['total']) {
					$count_products = (int)$this->request->get['total'];
				} else {
					$count_products = (int)$this->model_extension_feed_contentapi->getTotalProducts($filter_data);
				}

				if (isset($this->request->get['start'])) {
					$start = $this->request->get['start'];
				} else {
					$start = 0;
				}

				if (isset($this->request->get['counter'])) {
					$json['counter'] = (int)$this->request->get['counter'];
				}

				$limit = $filter_data['limit'];
				if ($limit > $count_products) {
					$limit = $count_products;
				}

				$next_start = $start + $limit;
				
				$json['start'] = $next_start;
				$json['total'] = $count_products;

				if ($limit < $count_products) {
					$filter_data['start'] = $start;
					$json['percent'] = round($next_start / $count_products * 100);
					if ($json['percent'] > 100) {
						$json['percent'] = 100;
					}
				} else {
					$json['percent'] = 100;
				}
			
				$products = $this->getFeedProducts($filter_data);
			}
			
			if (!empty($products)) {
				$counter = $this->sendRequest($products, 'batch');
				unset($products);
				$json['counter'] += $counter;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function cron() {
		if (!empty($this->request->server['argv'])) {
			unset($this->request->server['argv'][0]);
			$argv = implode('&', array_values($this->request->server['argv']));
			parse_str($argv, $this->request->get);
		}
		
		if (!empty($this->request->get['feed_id'])) {
			$this->getFeedSetting($this->request->get['feed_id']);
		} else {
			exit('Empty Feed ID');
		}

		if (!empty($this->request->get['key']) && $this->request->get['key'] == md5($this->setting['merchantid'])) {

			$filter_data = $this->getFilterData();

			if (isset($this->request->get['updates'])) {
				if ($this->setting['cron_action']) {
					$filter_data['cron_action'] = $this->setting['cron_action'];
					$filter_data['frequency'] = $this->setting['frequency'];
					$products = $this->getFeedProducts($filter_data);
				}
			} else {
				$products = $this->getFeedProducts($filter_data);
			}

			if (!empty($products)) {
				$counter = $this->sendRequest($products, 'batch');
				unset($products);
				echo 'Success! Products have been updated: ' . $counter .PHP_EOL;
			} else {
				echo 'There are no products'.PHP_EOL;
			}

		} else {
			echo 'Access denied! Invalid key to access cron script.'.PHP_EOL;
		}
	}

	protected function sendRequest($product_id, $request_type) {
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');

		if ($this->setting['language'] != $this->config->get('config_language_id')) {
			if (!empty($this->session->data['language'])) {
				$cur_language_code = $this->session->data['language'];
			}
			$cur_language_id = $this->config->get('config_language_id');
			$this->setLanguage($this->setting['language'], $this->setting['language_code']);
		}

		$this->authorize();
		
		switch ($request_type) {
			case 'add':
			case 'edit':
				$product_data = $this->createProduct($product_id);
				if (!empty($product_data['product'])) {
					$product = $product_data['product'];
					$this->insertProduct($product);
				}
				if (!empty($product_data['local'])) {
					$this->insertLocalinventory($product_data['local'], $product_data['rest_product_id']);
				}
				break;
			case 'delete':
				$delete_data = $this->model_extension_feed_contentapi->getPreData($product_id, $this->setting['offerid']);
				if (!empty($delete_data[$this->setting['offerid']])) {
					$this->deleteProduct($this->setting['prefix'] . $delete_data[$this->setting['offerid']]);
					$product = 1;
				}
				break;
			case 'batch':
				$product = [];
				$local = [];
				$k = 1;
				foreach ($product_id as $item) {
					$product_data = $this->createProduct($item);
					if (!empty($product_data['product'])) {
						$product[] = $product_data['product'];
					}
					if (!empty($product_data['local'])) {
						$local[$product_data['rest_product_id']] = $product_data['local'];
					}
					if ($k % $this->setting['limit'] == 0) {
						if (!empty($product)) {
							$this->insertProductBatch($product);
							$product = [];
						}
						if (!empty($local)) {
							$this->insertLocalinventoryBatch($local);
							$local = [];
						}
					}
					$k++;
				}
				if (!empty($product)) {
					$this->insertProductBatch($product);
				}
				if (!empty($local)) {
					$this->insertLocalinventoryBatch($local);
				}
				$product = 1;
				break;
		}
		
		if (isset($cur_language_id) && isset($cur_language_code)) {
			$this->setLanguage($cur_language_id, $cur_language_code);
		}

		if (!empty($product)) {
			$counter = 1;
			if ($request_type == 'batch') {
				if (!empty($this->blocked)) {
					$product_id = array_diff($product_id, $this->blocked);
				}
				$counter = count($product_id);
				$product_id = implode(',', $product_id);
			}
			if ($this->setting['debug']) {
				$log = new Log('contentapi_'.$this->setting['feed_id'].'.log');
				$log->write($request_type . ': product_id=' . $product_id);
			}
			return $counter;
		}
	}

	protected function createProduct($product_id) {
		$product = '';
		$local = '';
		$predata = $this->model_extension_feed_contentapi->getPreData($product_id, $this->setting['offerid']);

		if (!empty($predata[$this->setting['offerid']])) {
			$offerId = $this->setting['prefix'] . $predata[$this->setting['offerid']];
			if (empty($predata['status'])) {
				$this->deleteProduct($offerId);
				return [];
			}
		} else {
			return [];
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);
		if (empty($product_info)) return [];

		$availability = 'in_stock';
		if ($this->setting['stock']) {
			switch ($this->setting['stock']) {
				case '1':
					$availability = 'preorder';
					break;

				case '3':
					if ($product_info['quantity'] > 0) {
						$availability = 'in_stock';
					} else {
						if (!empty($predata['stock_status_id']) && isset($this->setting['stock_assign'][$predata['stock_status_id']])) {
							$availability = $this->setting['stock_assign'][$predata['stock_status_id']];
						} else {
							$availability = 'out_of_stock';
						}
					}
					break;
			}
		}

		if ($availability != 'not_upload') {

			$shortcodes = [];
			foreach ($this->fields as $field) {
				if (isset($product_info[$field])) {
					$shortcodes['{'.$field.'}'] = $product_info[$field];
				}
			}
			$shortcodes['{brand}'] = $product_info['manufacturer'];

			$product = new Google_Service_ShoppingContent_Product();
			$product->setOfferId($offerId);

			$attribute_groups = $this->model_catalog_product->getProductAttributes($product_id);

			if (!empty($this->setting['product_name'])&&!empty($product_info[$this->setting['product_name']])) {
				$product_name = $product_info[$this->setting['product_name']];
			} else {
				$product_name = $product_info['name'];
			}
			if (!empty($this->setting['name_prefix'])) {
				$product_name = strtr($this->setting['name_prefix'], $shortcodes) . ' ' . $product_name;
			}
			if (!empty($this->setting['name_suffix'])) {
				$product_name = $product_name . ' ' . strtr($this->setting['name_suffix'], $shortcodes);
			}
			$product_name = mb_substr($product_name, 0, 150);

			if (in_array('ai_title', $this->setting['settings'])) {
				$structuredTitle = new Google_Service_ShoppingContent_ProductStructuredTitle();
				$structuredTitle->setContent($product_name);
				$structuredTitle->setDigitalSourceType('trained_algorithmic_media');
				$product->setStructuredTitle($structuredTitle);
			} elseif (in_array('structured_title', $this->setting['settings'])) {
				$structuredTitle = new Google_Service_ShoppingContent_ProductStructuredTitle();
				$structuredTitle->setContent($product_name);
				$structuredTitle->setDigitalSourceType('default');
				$product->setStructuredTitle($structuredTitle);
			} else {
				$product->setTitle($product_name);
			}

			if (!empty($this->setting['product_description'])&&!empty($product_info[$this->setting['product_description']])) {
				$product_description = $product_info[$this->setting['product_description']];
			} else {
				$product_description = $product_info['description'];
			}
			if ($this->setting['striptags']) {
				$product_description = htmlentities(strip_tags(html_entity_decode($product_description, ENT_QUOTES, 'UTF-8')), ENT_QUOTES);
			} else {
				$product_description = html_entity_decode($product_description, ENT_QUOTES, 'UTF-8');
			}
			if (in_array('attr_description', $this->setting['settings']) && !empty($attribute_groups)) {
				foreach ($attribute_groups as $attribute_group) {
					$product_description .= '<h3>' . $attribute_group['name'] . '</h3>';
					$product_description .= '<ul>';
					foreach ($attribute_group['attribute'] as $attribute) {
						$product_description .= '<li>' . $attribute['name'] . ': ' . $attribute['text'] . '</li>';
					}
					$product_description .= '</ul>';
				}
			}
			//$product_description = mb_substr($product_description, 0, 5000);

			if (in_array('ai_description', $this->setting['settings'])) {
				$structuredDescription = new Google_Service_ShoppingContent_ProductStructuredDescription();
				$structuredDescription->setContent($product_description);
				$structuredDescription->setDigitalSourceType('trained_algorithmic_media');
				$product->setStructuredDescription($structuredDescription);
			} elseif (in_array('structured_description', $this->setting['settings'])) {
				$structuredDescription = new Google_Service_ShoppingContent_ProductStructuredDescription();
				$structuredDescription->setContent($product_description);
				$structuredDescription->setDigitalSourceType('default');
				$product->setStructuredDescription($structuredDescription);
			} else {
				$product->setDescription($product_description);
			}

			unset($product_description,$product_info['description']);

			if (in_array('product_detail', $this->setting['settings']) && !empty($attribute_groups)) {
				$productDetailsArray = [];
				foreach ($attribute_groups as $attribute_group) {
					foreach ($attribute_group['attribute'] as $attribute) {
						$productDetails = new Google_Service_ShoppingContent_ProductProductDetail();
						$productDetails->setSectionName($attribute_group['name']);
						$productDetails->setAttributeName($attribute['name']);
						$productDetails->setAttributeValue($attribute['text']);
						$productDetailsArray[] = $productDetails;
					}
				}
				$product->setProductDetails($productDetailsArray);
			}

			if (in_array('product_highlight', $this->setting['settings']) && $this->config->get($this->prefix.'highlights_status')) {
				$this->load->model('extension/module/highlights');
				$highlights = $this->model_extension_module_highlights->getData($product_id);
				if ($highlights) {
					$highlights_text = [];
					$i = 0;
					foreach ($highlights as $highlight) {
						$highlights_text[] = html_entity_decode($highlight['highlight'], ENT_QUOTES, 'UTF-8');
						$i++;
						if ($i==10) break;
					}
					$product->setProductHighlights($highlights_text);
					unset($highlights_text);
				}
			}

			if (in_array('multipack', $this->setting['settings']) && $product_info['minimum'] > 1) {
				$product->setMultipack($product_info['minimum']);
			}

			if (in_array('pause', $this->setting['settings'])) {
				$product->setPause('ads');
			}

			$canonical_link = $this->getLinkPrefix($this->url->link('product/product', 'product_id='.$product_info['product_id']));
			$tracking_code = '';
			if (!empty($this->setting['tracking'])) {
				$product->setCanonicalLink($canonical_link);
				$tracking_code = strtr($this->setting['tracking'], $shortcodes);
				if (parse_url($canonical_link, PHP_URL_QUERY)) {
					$tracking_code = '&'.$tracking_code;
				} else {
					$tracking_code = '?'.$tracking_code;
				}
			}
			$product_link = $canonical_link . $tracking_code;
			$product->setLink($product_link);

			if (!empty($product_info['image'])) {
				$product->setImageLink($this->domain.'image/'.$product_info['image']);
			} else {
				$product->setImageLink($this->domain.'image/'.'placeholder.png');
			}

			if (in_array('images', $this->setting['settings'])) {
				$product_images = $this->model_catalog_product->getProductImages($product_id);
				if ($product_images) {
					$images_array = [];
					foreach ($product_images as $image) {
						$images_array[] = $this->domain.'image/'.$image['image'];
					}
					$product->setAdditionalImageLinks($images_array);
				}
			}

			if (!empty($this->setting['gtin'])) {
				if ($this->setting['gtin'] == 'no') {
					$product->setIdentifierExists('no');
				} elseif (!empty($product_info[$this->setting['gtin']])) {
					$product->setGtin($product_info[$this->setting['gtin']]);
				}
			}

			$product->setChannel($this->setting['channel']);
			$product->setContentLanguage($this->setting['content_language']);
			$product->setFeedLabel(strtoupper($this->setting['label']));
			
			foreach ($this->setting['custom_label'] as $num => $label) {
				if (!empty($label)) {
					$product->{'setCustomLabel' . $num}(strtr($label, $shortcodes));
				}
			}

			if (!empty($this->setting['destination'])) {
				$product->setIncludedDestinations($this->setting['destination']);
			}
			
			if (!empty($this->setting['excluded'])) {
				$product->setExcludedDestinations($this->setting['excluded']);
			}

			$product->setAvailability($availability);
			if ($availability=='preorder' || $availability=='backorder') {
				if ($this->setting['preorder'] !== '') {
					$product->setAvailabilityDate(date('Y-m-d', strtotime('+'.$this->setting['preorder'].' days')));
				} else {
					$product->setAvailabilityDate($product_info['date_available']);
				}
			}

			$condition = 'new';
			if ($this->setting['condition']) {
				switch ($this->setting['condition']) {
					case '1':
						$condition = 'used';
						break;
					
					case '2':
						$condition = 'refurbished';
						break;

					case '3':
						$condition_field = strtolower($this->setting['condition_field']);
						if ($condition_field) {
							if (empty($this->setting['condition_table']) || $this->setting['condition_table'] == DB_PREFIX.'product') {
								if (!empty($product_info[$condition_field])) {
									$product_condition = $product_info[$condition_field];
								} else {
									$product_condition = '';
								}
							} else {
								$condition_table = strtolower($this->setting['condition_table']);
								$product_condition = $this->model_extension_feed_contentapi->getProductCondition($condition_table, $condition_field, $product_info['product_id']);
							}
							if ($product_condition) {
								foreach ($this->setting['condition_assign'] as $condition_name => $condition_value) {
									if ($product_condition == $condition_value) {
										$condition = $condition_name;
										break;
									}
								}
							}
						}
						break;
				}
			}
			$product->setCondition($condition);

			if (!empty($product_info['manufacturer'])) {
				$product->setBrand($product_info['manufacturer']);
			}

			if (!empty($this->setting['mpn']) && !empty($product_info[$this->setting['mpn']])) {
				$product->setMpn($product_info[$this->setting['mpn']]);
			}

			if ($this->setting['include_tax']) {
				$price_value = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'), '', false);
			} else {
				$price_value = $this->currency->format($product_info['price'], $this->config->get('config_currency'), '', false);
			}
			
			if ($this->config->get('config_currency') !== $this->setting['currency']) {
				$price_value = $this->currency->convert($price_value, $this->config->get('config_currency'), $this->setting['currency']);
			}

			$price = new Google_Service_ShoppingContent_Price();
			$price->setValue($price_value);
			$price->setCurrency($this->setting['currency']);
			$product->setPrice($price);

			if (!is_null($product_info['special']) && (float)$product_info['special'] > 0) {
				if ($this->setting['include_tax']) {
					$special_value = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'), '', false);
				} else {
					$special_value = $this->currency->format($product_info['special'], $this->config->get('config_currency'), '', false);
				}
				
				if ($this->config->get('config_currency') !== $this->setting['currency']) {
					$special_value = $this->currency->convert($special_value, $this->config->get('config_currency'), $this->setting['currency']);
				}

				$special = new Google_Service_ShoppingContent_Price();
				$special->setValue($special_value);
				$special->setCurrency($this->setting['currency']);
				$product->setSalePrice($special);

				$special_dates = $this->model_extension_feed_contentapi->getProductSpecialDate($product_id);
				if ($special_dates && ($special_dates['date_end'] != '0000-00-00' && !is_null($special_dates['date_end']))) {
					$time_zone = $this->config->get('config_timezone') ? $this->config->get('config_timezone') : 'UTC';
					$date_end = date_format(date_create($special_dates['date_end'])->setTimezone(new DateTimeZone($time_zone)), 'c');
					if ($special_dates['date_start'] == '0000-00-00' || is_null($special_dates['date_start'])) {
						$date_start = date_format(date_create(date('Y-m-d'))->setTimezone(new DateTimeZone($time_zone)), 'c');
					} else {
						$date_start =  date_format(date_create($special_dates['date_start'])->setTimezone(new DateTimeZone($time_zone)), 'c');
					}
					$product->setSalePriceEffectiveDate($date_start . '/' . $date_end);
				}
			}

			$categories = $this->model_catalog_product->getCategories($product_id);

			if (!empty($categories)) {
				if ($this->setting['product_type']) {
					foreach ($categories as $category) {
						$path = $this->getPath($category['category_id']);
						if ($path) {
							$string = '';
							foreach (explode('_', $path) as $path_id) {
								$category_info = $this->model_catalog_category->getCategory($path_id);
								if ($category_info) {
									if (!$string) {
										$string = $category_info['name'];
									} else {
										$string .= ' &gt; ' . $category_info['name'];
									}
								}
							}
							$product->setProductTypes($string);
						}
					}
				}

				if (!empty($this->google_categories[$categories[0]['category_id']])) {
					$product->setGoogleProductCategory($this->google_categories[$categories[0]['category_id']]);
				}

				unset($categories);
			}

			if ($this->setting['adult']) {
				$product->setAdult(true);
			}

			if (!empty($this->setting['shipping'])) {
				$shippings = [];
				foreach ($this->setting['shipping'] as $item) {
					$shippingPrice = new Google_Service_ShoppingContent_Price();
					if (!empty($item['upto'])) {
						if (isset($special_value)) {
							$product_price = $special_value;
						} else {
							$product_price = $price_value;
						}
						if ($product_price >= floatval($item['upto'])) {
							$item_price = floatval($item['new_price']);
						} else {
							$item_price = floatval($item['price']);
						}
					} elseif(strpos($item['price'], ':') !== false) {
						$rates = explode(',', $item['price']);
						foreach ($rates as $rate) {
							$data_weight = explode(':', $rate);
							if (isset($data_weight[1]) && $data_weight[0] >= $product_info['weight']) {
								$item_price = floatval($data_weight[1]);
								break;
							} else {
								$item_price = floatval($data_weight[0]);
							}
						}
					} else {
						$item_price = floatval($item['price']);
					}
					$shippingPrice->setValue($item_price);
					$shippingPrice->setCurrency($this->setting['currency']);

					$shipping = new Google_Service_ShoppingContent_ProductShipping();
					$shipping->setPrice($shippingPrice);
					$shipping->setCountry($item['country']);
					if (!empty($item['service'])) {
						$shipping->setService($item['service']);
					}
					$shippings[] = $shipping;
				}

				$product->setShipping($shippings);
			}

			if (!empty($this->setting['weight'])) {
				if ((float)$product_info['weight'] > 0 && !empty($this->setting['weight_assign'])) {
					foreach ($this->setting['weight_assign'] as $weight_class_id => $weight_data) {
						if ($product_info['weight_class_id'] == $weight_class_id) {
							$weight_unit = $weight_data['unit'];
							$weight_multiplier = $weight_data['multiplier']?floatval($weight_data['multiplier']):1;
							break;
						}
					}
					if (isset($weight_unit) && !empty($weight_multiplier)) {
						$product_weight = new Google_Service_ShoppingContent_ProductWeight();
						$product_weight->setValue(floatval($product_info['weight'])*$weight_multiplier);
						$product_weight->setUnit($weight_unit);
						$product->setProductWeight($product_weight);

						$shipping_weight = new Google_Service_ShoppingContent_ProductShippingWeight();
						$shipping_weight->setValue(floatval($product_info['weight'])*$weight_multiplier);
						$shipping_weight->setUnit($weight_unit);
						$product->setShippingWeight($shipping_weight);
					}
				}
			}

			if (!empty($this->setting['dimension'])) {
				if (!empty($this->setting['dimension_assign'])) {
					foreach ($this->setting['dimension_assign'] as $length_class_id => $length_data) {
						if ($product_info['length_class_id'] == $length_class_id) {
							$length_unit = $length_data['unit'];
							$length_multiplier = $length_data['multiplier']?floatval($length_data['multiplier']):1;
							break;
						}
					}
					if (isset($length_unit) && !empty($length_multiplier)) {
						if ((float)$product_info['width'] > 0) {
							$product_width = new Google_Service_ShoppingContent_ProductDimension();
							$product_width->setValue(floatval($product_info['width'])*$length_multiplier);
							$product_width->setUnit($length_unit);
							$product->setProductWidth($product_width);

							$shipping_width = new Google_Service_ShoppingContent_ProductShippingDimension();
							$shipping_width->setValue(floatval($product_info['width'])*$length_multiplier);
							$shipping_width->setUnit($length_unit);
							$product->setShippingWidth($shipping_width);
						}
						if ((float)$product_info['height'] > 0) {
							$product_height = new Google_Service_ShoppingContent_ProductDimension();
							$product_height->setValue(floatval($product_info['height'])*$length_multiplier);
							$product_height->setUnit($length_unit);
							$product->setProductHeight($product_height);

							$shipping_height = new Google_Service_ShoppingContent_ProductShippingDimension();
							$shipping_height->setValue(floatval($product_info['height'])*$length_multiplier);
							$shipping_height->setUnit($length_unit);
							$product->setShippingHeight($shipping_height);
						}
						if ((float)$product_info['length'] > 0) {
							$product_length = new Google_Service_ShoppingContent_ProductDimension();
							$product_length->setValue(floatval($product_info['length'])*$length_multiplier);
							$product_length->setUnit($length_unit);
							$product->setProductLength($product_length);

							$shipping_length = new Google_Service_ShoppingContent_ProductShippingDimension();
							$shipping_length->setValue(floatval($product_info['length'])*$length_multiplier);
							$shipping_length->setUnit($length_unit);
							$product->setShippingLength($shipping_length);
						}
					}
				}
			}

			if ($this->setting['channel'] == 'local' && !empty($this->setting['store_code'])) {
				$local = new Google_Service_ShoppingContent_LocalInventory();
				$local->setPrice($price);
				if (isset($special_value)) {
					$local->setSalePrice($special);
				}
				if ($availability=='preorder' || $availability=='backorder') {
					$local->setAvailability('on_display_to_order');
					$local->setQuantity('1');
				} else {
					if ($this->setting['stock']=='3' && in_array($product_info['quantity'], ['1','2'])) {
						$local->setAvailability('limited_availability');
					} else {
						$local->setAvailability($availability);
					}
				}
			}

		} else {
			$this->blocked[] = $product_id;
		}

		return ['rest_product_id' => $this->buildProductId($offerId), 'product' => $product, 'local' => $local];
	}

	protected function insertProduct(Google_Service_ShoppingContent_Product $product) {
		$this->service->products->insert($this->setting['merchantid'], $product);
	}

	protected function deleteProduct($offerId) {
		$this->service->products->delete($this->setting['merchantid'], $this->buildProductId($offerId));
	}

	protected function insertProductBatch($products) {
		$entries = [];

		foreach ($products as $key => $product) {
			if (!empty($product)) {
				$entry = new Google_Service_ShoppingContent_ProductsCustomBatchRequestEntry();
				$entry->setMethod('insert');
				$entry->setBatchId($key);
				$entry->setProduct($product);
				$entry->setMerchantId($this->setting['merchantid']);
				$entries[] = $entry;
			}
		}

		$batchRequest =	new Google_Service_ShoppingContent_ProductsCustomBatchRequest();
		$batchRequest->setEntries($entries);
		unset($entries);
		$this->service->products->custombatch($batchRequest);
	}

	protected function insertLocalinventory(Google_Service_ShoppingContent_Localinventory $local_product, $product_id) {
		if (!empty($this->setting['store_code'])) {
			foreach ($this->setting['store_code'] as $store_code) {
				$local_product->setStoreCode($store_code);
				$this->service->localinventory->insert($this->setting['merchantid'], $product_id, $local_product);
			}
		}
	}

	protected function insertLocalinventoryBatch($products) {
		if (!empty($this->setting['store_code'])) {
			foreach ($this->setting['store_code'] as $store_code) {
				$entries = [];
				$batch_id = 0;
				foreach ($products as $product_id => $local_product) {
					if (!empty($local_product)) {
						$local_product->setStoreCode($store_code);
						$entry = new Google_Service_ShoppingContent_LocalinventoryCustomBatchRequestEntry();
						$entry->setMethod('insert');
						$entry->setBatchId($batch_id);
						$entry->setProductId($product_id);
						$entry->setMerchantId($this->setting['merchantid']);
						$entry->setLocalInventory($local_product);
						$entries[] = $entry;
						$batch_id++;
					}
				}

				$batchRequest =	new Google_Service_ShoppingContent_LocalinventoryCustomBatchRequest();
				$batchRequest->setEntries($entries);
				unset($entries);
				$this->service->localinventory->custombatch($batchRequest);
			}
		}
	}

	protected function buildProductId($offerId) {
		return sprintf('%s:%s:%s:%s', $this->setting['channel'], $this->setting['content_language'], strtoupper($this->setting['label']), $offerId);
	}

	protected function setStore($store) {
		$this->config->set('config_store_id', $store['store_id']);
		$this->config->set('config_url', $store['url']);
		$this->config->set('config_ssl', $store['ssl']);
	}

	protected function setLanguage($language_id, $code) {
		$this->config->set('config_language', $code);
		$this->config->set('config_language_id', $language_id);
		$this->session->data['language'] = $code;
	}

	protected function getLinkPrefix($link) {
		$link = str_replace($this->config_domain, $this->domain, $link);
		if (!empty($this->lang_prefix[$this->session->data['language']])) {
			$link = str_replace($this->domain, $this->domain . $this->lang_prefix[$this->session->data['language']] . '/', $link);
		}
		return $link;
	}

	protected function getPath($parent_id, $current_path = '') {
		$category_info = $this->model_catalog_category->getCategory($parent_id);

		if ($category_info) {
			if (!$current_path) {
				$new_path = $category_info['category_id'];
			} else {
				$new_path = $category_info['category_id'] . '_' . $current_path;
			}

			$path = $this->getPath($category_info['parent_id'], $new_path);

			return $path ? $path : $new_path;
		}
	}
}