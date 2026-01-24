<?php
class ControllerExtensionFeedContentapi extends Controller {

	private $error = [];
	private $token_var;
	private $extension_var;
	private $prefix;
	private $confirm = false;
	private $event_action = 'extension/feed/contentapi/eventProduct';
	private $_events = [
		[
			'code'		=> 'contentapi_add_product',
			'trigger'	=> 'admin/model/catalog/product/addProduct/after'
		],
		[
			'code'		=> 'contentapi_edit_product',
			'trigger'	=> 'admin/model/catalog/product/editProduct/after'
		],
		[
			'code'		=> 'contentapi_delete_product',
			'trigger'	=> 'admin/model/catalog/product/deleteProduct/before'
		]
	];

	public function __construct($registry) {
		parent::__construct($registry);
		$this->token_var = (version_compare(VERSION, '3.0', '>=')) ? 'user_token' : 'token';
		$this->extension_var = (version_compare(VERSION, '3.0', '>=')) ? 'marketplace' : 'extension';
		$this->prefix = (version_compare(VERSION, '3.0', '>=')) ? 'feed_' : '';
	}

	public function install() {
		$this->load->model('extension/feed/contentapi');
		$this->model_extension_feed_contentapi->install();

		if (version_compare(VERSION, '3.0', '>=')) {
			$this->load->model('setting/event');
			foreach ($this->_events as $event) {
				if (!$this->model_setting_event->getEventByCode($event['code'])) {
					$this->model_setting_event->addEvent($event['code'], $event['trigger'], $this->event_action);
				}
			}
		} else {
			$this->load->model('extension/event');
			foreach ($this->_events as $event) {
				if (!$this->model_extension_event->getEvent($event['code'], $event['trigger'], $this->event_action)) {
					$this->model_extension_event->addEvent($event['code'], $event['trigger'], $this->event_action);
				}
			}
		}
	}

	public function uninstall() {
		$this->load->model('extension/feed/contentapi');
		$this->model_extension_feed_contentapi->uninstall();

		if (version_compare(VERSION, '3.0', '>=')) {
			$this->load->model('setting/event');
			foreach ($this->_events as $event) {
				$this->model_setting_event->deleteEventByCode($event['code']);
			}
		} else {
			$this->load->model('extension/event');
			foreach ($this->_events as $event) {
				$this->model_extension_event->deleteEvent($event['code']);
			}
		}
	}

	public function index() {
		$data = $this->load->language('extension/feed/contentapi');

		$heading_title = $this->language->get('heading_title');
		$this->document->setTitle($heading_title);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if ($this->confirm) {
				$this->model_setting_setting->editSetting($this->prefix . 'contentapi', $this->request->post);
				$this->session->data['success'] = $this->language->get('text_success');
			}
			if (isset($this->request->post['apply'])) {
				$this->response->redirect($this->url->link('extension/feed/contentapi', $this->token_var . '=' . $this->session->data[$this->token_var], true));
			} else {
				$this->response->redirect($this->url->link($this->extension_var . '/extension', $this->token_var . '=' . $this->session->data[$this->token_var] . '&type=feed', true));
			}
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (!$this->_isCurl()) {
			$data['error_curl'] = $this->language->get('error_curl');
		} else {
			$data['error_curl'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->token_var . '=' . $this->session->data[$this->token_var], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link($this->extension_var . '/extension', $this->token_var . '=' . $this->session->data[$this->token_var] . '&type=feed', true)
		];

		$data['breadcrumbs'][] = [
			'text' => $heading_title,
			'href' => $this->url->link('extension/feed/contentapi', $this->token_var . '=' . $this->session->data[$this->token_var], true)
		];

		$data['prefix'] = $this->prefix;
		$data['token_var'] = $this->token_var;
		$data[$this->token_var] = $this->session->data[$this->token_var];
		$data['clear'] = $this->url->link('extension/feed/contentapi/clearlog', $this->token_var . '=' . $this->session->data[$this->token_var], true);
		$data['action'] = $this->url->link('extension/feed/contentapi', $this->token_var . '=' . $this->session->data[$this->token_var], true);
		$data['cancel'] = $this->url->link($this->extension_var . '/extension', $this->token_var . '=' . $this->session->data[$this->token_var] . '&type=feed', true);
		$data['list'] = $this->url->link('extension/feed/contentapi/listing', $this->token_var . '=' . $this->session->data[$this->token_var], true);
		
		$fields = ['status','license','prefix'];
		foreach ($fields as $field) {
			if (isset($this->request->post[$this->prefix . 'contentapi_' . $field])) {
				$data[$this->prefix . 'contentapi_' . $field] = $this->request->post[$this->prefix . 'contentapi_' . $field];
			} else {
				$data[$this->prefix . 'contentapi_' . $field] = $this->config->get($this->prefix . 'contentapi_' . $field);
			}
		}

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/feed/contentapi/setting', $data));
	}

	public function eventProduct(&$route, &$args, &$instance_id=0) {

		if ($this->config->get($this->prefix . 'contentapi_status')) {

			if ($this->request->server['HTTPS']) {
				$server = HTTPS_CATALOG;
			} else {
				$server = HTTP_CATALOG;
			}

			$this->load->model('extension/feed/contentapi');
			$feeds = $this->model_extension_feed_contentapi->getFeeds(['status'=>1]);

			foreach ($feeds as $feed) {
				$feed['action'] = json_decode($feed['action'], true);
				if (!empty($feed['action'])) {
					
					if (empty($instance_id)) { 
						$instance_id = $args[0];
					}

					if ($route == 'catalog/product/addProduct' && in_array('add_product', $feed['action'])) {
						if ((isset($args[0]['status']) && $args[0]['status']==0)) {
							$data = [];
						} else {
							$data = [
								'product_id' => $instance_id,
								'request_type' => 'add'
							];
						}
					} elseif ($route == 'catalog/product/editProduct' && in_array('edit_product', $feed['action'])) {
						if ((isset($args[1]['status']) && $args[1]['status']==0)) {
							$data = [
								'product_id' => $instance_id,
								'request_type' => 'delete'
							];
						} else {
							$data = [
								'product_id' => $instance_id,
								'request_type' => 'edit'
							];
						}
					} elseif ($route == 'catalog/product/deleteProduct' && in_array('delete_product', $feed['action'])) {
						$data = [
							'product_id' => $instance_id,
							'request_type' => 'delete'
						];
					}

					if (!empty($data)) {
						$data['token'] = md5($feed['merchantid']);

						$url = $server.'index.php?route=extension/feed/contentapi&feed_id=' . $feed['feed_id'];

						$ch = curl_init($url);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
						curl_exec($ch);
						curl_close($ch);
					}
				}
			}
		}
	}

	public function add() {
		$this->load->language('extension/feed/contentapi');

		$this->load->model('extension/feed/contentapi');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$feed_id = $this->model_extension_feed_contentapi->addFeed($this->request->post);
			$this->saveKey($feed_id);
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->post['apply'])) {
				$this->response->redirect($this->url->link('extension/feed/contentapi/edit', $this->token_var . '=' . $this->session->data[$this->token_var] . $url . '&feed_id=' . $feed_id, true));
			} else {
				$this->response->redirect($this->url->link('extension/feed/contentapi/listing', $this->token_var . '=' . $this->session->data[$this->token_var] . $url, true));
			}
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('extension/feed/contentapi');

		$this->load->model('extension/feed/contentapi');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_feed_contentapi->editFeed($this->request->get['feed_id'], $this->request->post);
			$this->saveKey($this->request->get['feed_id']);
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->post['apply'])) {
				$this->response->redirect($this->url->link('extension/feed/contentapi/edit', $this->token_var . '=' . $this->session->data[$this->token_var] . $url . '&feed_id=' . $this->request->get['feed_id'], true));
			} else {
				$this->response->redirect($this->url->link('extension/feed/contentapi/listing', $this->token_var . '=' . $this->session->data[$this->token_var] . $url, true));
			}
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('extension/feed/contentapi');

		$this->load->model('extension/feed/contentapi');

		if (isset($this->request->post['selected']) && $this->user->hasPermission('modify', 'extension/feed/contentapi')) {
			foreach ($this->request->post['selected'] as $feed_id) {
				$this->model_extension_feed_contentapi->deleteFeed($feed_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/feed/contentapi/listing', $this->token_var . '=' . $this->session->data[$this->token_var] . $url, true));
		}

		$this->getList();
	}

	public function listing () {
		$this->load->language('extension/feed/contentapi');

		$this->document->setTitle($this->language->get('heading_title_list'));

		$this->load->model('extension/feed/contentapi');

		$this->model_extension_feed_contentapi->updateDB();
		
		$this->getList();
	}

	protected function getList() {

		$this->load->model('localisation/language');
		$this->load->model('setting/store');

		$data = $this->load->language('extension/feed/contentapi');
		$data['heading_title'] = $this->language->get('heading_title_list');

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'feed_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->token_var . '=' . $this->session->data[$this->token_var], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', $this->token_var . '=' . $this->session->data[$this->token_var] . '&type=feed', true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/feed/contentapi', $this->token_var . '=' . $this->session->data[$this->token_var] . $url, true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title_list'),
			'href' => $this->url->link('extension/feed/contentapi/listing', $this->token_var . '=' . $this->session->data[$this->token_var] . $url, true)
		];

		$data['add'] = $this->url->link('extension/feed/contentapi/add', $this->token_var . '=' . $this->session->data[$this->token_var] . $url, true);
		$data['delete'] = $this->url->link('extension/feed/contentapi/delete', $this->token_var . '=' . $this->session->data[$this->token_var] . $url, true);

		$data['feeds'] = [];

		$filter_data = [
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		];

		$total_feeds = $this->model_extension_feed_contentapi->getTotalFeeds($filter_data);
		$results = $this->model_extension_feed_contentapi->getFeeds($filter_data);

		foreach ($results as $result) {
			$language_data = $this->model_localisation_language->getLanguage($result['language']);
			if (isset($result['store_id'])) {
				$store_data = $this->model_setting_store->getStore($result['store_id']);
			}
			$data['feeds'][] = [
				'feed_id'     => $result['feed_id'],
				'label'       => $result['label'],
				'channel'     => $result['channel'],
				'store'       => empty($store_data['name']) ? strip_tags($this->language->get('text_default')) : $store_data['name'],
				'language'    => $language_data['name'],
				'currency'    => $result['currency'],
				'custom_label'=> trim(str_replace(['[',']','"'], '', $result['custom_label']), ','),
				'status'      => $result['status'],
				'edit'        => $this->url->link('extension/feed/contentapi/edit', $this->token_var . '=' . $this->session->data[$this->token_var] . '&feed_id=' . $result['feed_id'] . $url, true),
				'delete'      => $this->url->link('extension/feed/contentapi/delete', $this->token_var . '=' . $this->session->data[$this->token_var] . '&feed_id=' . $result['feed_id'] . $url, true)
			];
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = [];
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/feed/contentapi/listing', $this->token_var . '=' . $this->session->data[$this->token_var] . '&sort=name' . $url, true);
		$data['sort_status'] = $this->url->link('extension/feed/contentapi/listing', $this->token_var . '=' . $this->session->data[$this->token_var] . '&sort=status' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['copy'] = $this->url->link('extension/feed/contentapi/copy', $this->token_var . '=' . $this->session->data[$this->token_var] . $url, true);
		$data['settings'] = $this->url->link('extension/feed/contentapi', $this->token_var . '=' . $this->session->data[$this->token_var] . $url, true);

		$pagination = new Pagination();
		$pagination->total = $total_feeds;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/feed/contentapi/listing', $this->token_var . '=' . $this->session->data[$this->token_var] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($total_feeds) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total_feeds - $this->config->get('config_limit_admin'))) ? $total_feeds : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total_feeds, ceil($total_feeds / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data[$this->token_var] = $this->session->data[$this->token_var];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/feed/contentapi/listing', $data));
	}

	protected function getForm() {

		$data = $this->load->language('extension/feed/contentapi');
		$data['heading_title'] = $this->language->get('heading_title_form');

		$this->document->setTitle($this->language->get('heading_title_form'));

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		if (isset($this->error['key'])) {
			$data['error_key'] = $this->error['key'];
		} else {
			$data['error_key'] = '';
		}
		if (isset($this->error['merchantid'])) {
			$data['error_merchantid'] = $this->error['merchantid'];
		} else {
			$data['error_merchantid'] = '';
		}
		if (isset($this->error['label'])) {
			$data['error_label'] = $this->error['label'];
		} else {
			$data['error_label'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->token_var . '=' . $this->session->data[$this->token_var], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link($this->extension_var . '/extension', $this->token_var . '=' . $this->session->data[$this->token_var] . '&type=feed', true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/feed/contentapi', $this->token_var . '=' . $this->session->data[$this->token_var], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title_list'),
			'href' => $this->url->link('extension/feed/contentapi/listing', $this->token_var . '=' . $this->session->data[$this->token_var], true)
		];

		$data['prefix'] = $this->prefix;
		$data['token_var'] = $this->token_var;
		$data[$this->token_var] = $this->session->data[$this->token_var];
		$data['cancel'] = $this->url->link('extension/feed/contentapi/listing', $this->token_var . '=' . $this->session->data[$this->token_var] . '&type=feed', true);

		if (isset($this->request->get['feed_id'])) {
			$data['clear'] = $this->url->link('extension/feed/contentapi/clearlog', $this->token_var . '=' . $this->session->data[$this->token_var] . '&feed_id=' . (int)$this->request->get['feed_id'], true);
			$data['save'] = $this->url->link('extension/feed/contentapi/edit', $this->token_var . '=' . $this->session->data[$this->token_var] . '&feed_id=' . (int)$this->request->get['feed_id'], true);
		} else {
			$data['clear'] = '';
			$data['save'] = $this->url->link('extension/feed/contentapi/add', $this->token_var . '=' . $this->session->data[$this->token_var], true);
		}

		if (isset($this->request->get['feed_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$feed_info = $this->model_extension_feed_contentapi->getFeed($this->request->get['feed_id']);
		} else {
			$feed_info = [];
		}

		$arrays = ['stock_assign','condition_assign','filter','filterv','filtera','filterva','filterf','filtervf','custom_label','shipping','destination','excluded','action','cron_action','store_code','dimension_assign','weight_assign','settings'];
		foreach ($arrays as $field) {
			if (isset($this->request->post[$field])) {
				$data[$field] = $this->request->post[$field];
			} elseif (!empty($feed_info[$field])) {
				$data[$field] = json_decode($feed_info[$field], true);
			} else {
				$data[$field] = [];
			}
		}

		$data['settings_data'] = ['images','attr_description','product_detail','product_highlight','multipack','pause','structured_title','ai_title','structured_description','ai_description'];

		$data['filterv_name'] = [];
		if (!empty($data['filterv'])) {
			$this->load->model('catalog/option');
			foreach ($data['filterv'] as $key => $value) {
				$option_value_data = $this->model_catalog_option->getOptionValue($value);
				if (!empty($option_value_data)) {
					$data['filterv_name'][$key] = $option_value_data['name'];
				}
			}
		}

		$data['filtervf_name'] = [];
		if (!empty($data['filtervf'])) {
			$this->load->model('catalog/filter');
			foreach ($data['filtervf'] as $key => $value) {
				$filter_value_data = $this->model_catalog_filter->getFilter($value);
				if (!empty($filter_value_data)) {
					$data['filtervf_name'][$key] = $filter_value_data['name'];
				}
			}
		}
	
		$fields = ['status','debug','frequency','language','currency','label','merchantid','offerid','gtin','include_tax','product_type','prefix','monolog','tracking','price_from','price_to','sub_category','sub_categorybl','select','product_name','product_description','striptags','name_prefix','name_suffix','mpn','minimum','adult','stock','condition','condition_field','limit','preorder','channel','suppler','dimension','weight','condition_table','store_id','noimage'];
		foreach ($fields as $field) {
			if (isset($this->request->post[$field])) {
				$data[$field] = $this->request->post[$field];
			} elseif (!empty($feed_info[$field])) {
				$data[$field] = $feed_info[$field];
			} else {
				$data[$field] = '';
			}
		}

		$data['fields'] = $this->model_extension_feed_contentapi->getProductFields();

		if (!empty($this->request->get['feed_id']) && file_exists(DIR_CONFIG . 'content_api_'.$this->request->get['feed_id'].'.json')) {
			$data['key'] = file_get_contents(DIR_CONFIG . 'content_api_'.$this->request->get['feed_id'].'.json');
		} else {
			$data['key'] = '';
		}

		if (empty($data['sub_category'])) {
			$data['sub_category'] = 0;
		}

		if (empty($data['sub_categorybl'])) {
			$data['sub_categorybl'] = 0;
		}

		if (empty($data['select'])) {
			$data['select'] = 'all';
		}

		if (empty($data['limit'])) {
			$data['limit'] = 50;
		}

		if (!empty($feed_info['merchantid'])) {
			$data['update_token'] = md5($feed_info['merchantid']);
		} else {
			$data['update_token'] = '';
		}
		
		$this->load->model('catalog/product');

		if (isset($this->request->post['product'])) {
			$product = $this->request->post['product'];
		} elseif (!empty($feed_info['product'])) {
			$product = json_decode($feed_info['product'], true);
		} else {
			$product = [];
		}

		$data['product_selected'] = [];
		if (!empty($product)) {
			foreach ($product as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);
				if ($product_info) {
					$data['product_selected'][] = [
						'product_id' => $product_info['product_id'],
						'name' => $product_info['name']
					];
				}
			}
		}

		if (isset($this->request->post['productbl'])) {
			$productbl = $this->request->post['productbl'];
		} elseif (!empty($feed_info['productbl'])) {
			$productbl = json_decode($feed_info['productbl'], true);
		} else {
			$productbl = [];
		}

		$data['productbl_selected'] = [];
		if (!empty($productbl)) {
			foreach ($productbl as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);
				if ($product_info) {
					$data['productbl_selected'][] = [
						'product_id' => $product_info['product_id'],
						'name' => $product_info['name']
					];
				}
			}
		}

		$this->load->model('catalog/category');
		$data['categories'] = $this->model_catalog_category->getCategories(['sort'=>'name']);

		if (isset($this->request->post['category'])) {
			$category = $this->request->post['category'];
		} elseif (!empty($feed_info['category'])) {
			$category = json_decode($feed_info['category'], true);
		} else {
			$category = [];
		}

		$data['category_selected'] = [];
		if (!empty($category)) {
			foreach ($category as $category_id) {
				$category_info = $this->model_catalog_category->getCategory($category_id);
				if ($category_info) {
					$data['category_selected'][] = $category_info['category_id'];
				}
			}
		}

		if (isset($this->request->post['categorybl'])) {
			$categorybl = $this->request->post['categorybl'];
		} elseif (!empty($feed_info['categorybl'])) {
			$categorybl = json_decode($feed_info['categorybl'], true);
		} else {
			$categorybl = [];
		}

		$data['categorybl_selected'] = [];
		if (!empty($categorybl)) {
			foreach ($categorybl as $category_id) {
				$category_info = $this->model_catalog_category->getCategory($category_id);
				if ($category_info) {
					$data['categorybl_selected'][] = [
						'category_id' => $category_info['category_id'],
						'name' => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
					];
				}
			}
		}

		$this->load->model('catalog/manufacturer');
		$data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers(['sort'=>'name']);

		if (isset($this->request->post['manufacturers'])) {
			$manufacturer = $this->request->post['manufacturers'];
		} elseif (!empty($feed_info['manufacturer'])) {
			$manufacturer = json_decode($feed_info['manufacturer'], true);
		} else {
			$manufacturer = [];
		}

		$data['manufacturer_selected'] = [];
		if (!empty($manufacturer)) {
			foreach ($manufacturer as $manufacturer_id) {
				$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);
				if ($manufacturer_info) {
					$data['manufacturer_selected'][] = $manufacturer_info['manufacturer_id'];
				}
			}
		}

		if (isset($this->request->post['manufacturerbl'])) {
			$manufacturerbl = $this->request->post['manufacturerbl'];
		} elseif (!empty($feed_info['manufacturerbl'])) {
			$manufacturerbl = json_decode($feed_info['manufacturerbl'], true);
		} else {
			$manufacturerbl = [];
		}

		$data['manufacturerbl_selected'] = [];
		if (!empty($manufacturerbl)) {
			foreach ($manufacturerbl as $manufacturer_id) {
				$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);
				if ($manufacturer_info) {
					$data['manufacturerbl_selected'][] = [
						'manufacturer_id' => $manufacturer_info['manufacturer_id'],
						'name' => $manufacturer_info['name']
					];
				}
			}
		}

		$this->load->model('catalog/option');
		$data['options'] = $this->model_catalog_option->getOptions();

		$this->load->model('catalog/attribute');
		$data['attributes'] = $this->model_catalog_attribute->getAttributes();

		$this->load->model('catalog/filter');
		$data['filters'] = $this->model_catalog_filter->getFilterGroups();

		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();

		if ($this->request->server['HTTPS']) {
			$server = HTTPS_CATALOG;
		} else {
			$server = HTTP_CATALOG;
		}

		$data['server'] = $server;

		if (empty($data['store_id'])) {
			if ($this->request->server['HTTPS']) {
				$domain = HTTPS_CATALOG;
			} else {
				$domain = HTTP_CATALOG;
			}
		} else {
			$store = $this->model_setting_store->getStore($data['store_id']);
			if ($this->request->server['HTTPS']) {
				$domain = $store['ssl'];
			} else {
				$domain = $store['url'];
			}
		}

		if (isset($this->request->get['feed_id'])) {
			$data['feed_id'] = $this->request->get['feed_id'];
			$data['cron_link'] = $domain . 'index.php?route=extension/feed/contentapi/cron&key=' . md5($data['merchantid']) . '&feed_id=' . (int)$this->request->get['feed_id'] . '&updates=1';
			$data['cron_all_link'] = $domain . 'index.php?route=extension/feed/contentapi/cron&key=' . md5($data['merchantid']). '&feed_id=' . (int)$this->request->get['feed_id'];
			$data['cron_cli'] = 'php -q ' . str_replace('/system/','/', DIR_SYSTEM) . 'contentapi_cli.php key=' . md5($data['merchantid']) . ' feed_id=' . (int)$this->request->get['feed_id'] . ' updates=1';
			$data['cron_all_cli'] = 'php -q ' . str_replace('/system/','/', DIR_SYSTEM) . 'contentapi_cli.php key=' . md5($data['merchantid']) . ' feed_id=' . (int)$this->request->get['feed_id'];
		} else {
			$data['feed_id'] = 0;
			$data['cron_link'] = '';
			$data['cron_all_link'] = '';
			$data['cron_cli'] = '';
			$data['cron_all_cli'] = '';
		}

		$data['log'] = '';

		if (isset($this->request->get['feed_id'])) {
			$file = DIR_LOGS . 'contentapi_'.$this->request->get['feed_id'].'.log';

			if (file_exists($file)) {
				$size = filesize($file);

				if ($size >= 5242880) {
					$suffix = ['B','KB','MB','GB','TB','PB','EB','ZB','YB'];

					$i = 0;
					while (($size / 1024) > 1) {
						$size = $size / 1024;
						$i++;
					}

					$data['error_warning'] = sprintf($this->language->get('error_log_size'), basename($file), round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i]);
				} else {
					$data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
				}
			}
		}

		$data['default_statuses'] = ['in_stock','out_of_stock','preorder','backorder','not_upload'];

		$data['default_conditions'] = ['new','used','refurbished'];

		$this->load->model('localisation/stock_status');
		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		$this->load->model('localisation/currency');
		$data['currencies'] = $this->model_localisation_currency->getCurrencies();

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['countries'] = ["AF" => "Afghanistan","AX" => "Aland Islands","AL" => "Albania","DZ" => "Algeria","AS" => "American Samoa","AD" => "Andorra","AO" => "Angola","AI" => "Anguilla","AQ" => "Antarctica","AG" => "Antigua and Barbuda","AR" => "Argentina","AM" => "Armenia","AW" => "Aruba","AU" => "Australia","AT" => "Austria","AZ" => "Azerbaijan","BS" => "Bahamas","BH" => "Bahrain","BD" => "Bangladesh","BB" => "Barbados","BY" => "Belarus","BE" => "Belgium","BZ" => "Belize","BJ" => "Benin","BM" => "Bermuda","BT" => "Bhutan","BO" => "Bolivia","BA" => "Bosnia and Herzegovina","BW" => "Botswana","BV" => "Bouvet Island","BR" => "Brazil","BQ" => "British Antarctic Territory","IO" => "British Indian Ocean Territory","VG" => "British Virgin Islands","BN" => "Brunei","BG" => "Bulgaria","BF" => "Burkina Faso","BI" => "Burundi","KH" => "Cambodia","CM" => "Cameroon","CA" => "Canada","CT" => "Canton and Enderbury Islands","CV" => "Cape Verde","KY" => "Cayman Islands","CF" => "Central African Republic","TD" => "Chad","CL" => "Chile","CN" => "China","CX" => "Christmas Island","CC" => "Cocos [Keeling] Islands","CO" => "Colombia","KM" => "Comoros","CG" => "Congo - Brazzaville","CD" => "Congo - Kinshasa","CK" => "Cook Islands","CR" => "Costa Rica","HR" => "Croatia","CU" => "Cuba","CY" => "Cyprus","CZ" => "Czech Republic","CI" => "Côte d’Ivoire","DK" => "Denmark","DJ" => "Djibouti","DM" => "Dominica","DO" => "Dominican Republic","NQ" => "Dronning Maud Land","DD" => "East Germany","EC" => "Ecuador","EG" => "Egypt","SV" => "El Salvador","GQ" => "Equatorial Guinea","ER" => "Eritrea","EE" => "Estonia","ET" => "Ethiopia","FK" => "Falkland Islands","FO" => "Faroe Islands","FJ" => "Fiji","FI" => "Finland","FR" => "France","GF" => "French Guiana","PF" => "French Polynesia","TF" => "French Southern Territories","FQ" => "French Southern and Antarctic Territories","GA" => "Gabon","GM" => "Gambia","GE" => "Georgia","DE" => "Germany","GH" => "Ghana","GI" => "Gibraltar","GR" => "Greece","GL" => "Greenland","GD" => "Grenada","GP" => "Guadeloupe","GU" => "Guam","GT" => "Guatemala","GG" => "Guernsey","GN" => "Guinea","GW" => "Guinea-Bissau","GY" => "Guyana","HT" => "Haiti","HM" => "Heard Island and McDonald Islands","HN" => "Honduras","HK" => "Hong Kong SAR China","HU" => "Hungary","IS" => "Iceland","IN" => "India","ID" => "Indonesia","IR" => "Iran","IQ" => "Iraq","IE" => "Ireland","IM" => "Isle of Man","IL" => "Israel","IT" => "Italy","JM" => "Jamaica","JP" => "Japan","JE" => "Jersey","JT" => "Johnston Island","JO" => "Jordan","KZ" => "Kazakhstan","KE" => "Kenya","KI" => "Kiribati","XK" => "Kosovo","KW" => "Kuwait","KG" => "Kyrgyzstan","LA" => "Laos","LV" => "Latvia","LB" => "Lebanon","LS" => "Lesotho","LR" => "Liberia","LY" => "Libya","LI" => "Liechtenstein","LT" => "Lithuania","LU" => "Luxembourg","MO" => "Macau SAR China","MK" => "Macedonia","MG" => "Madagascar","MW" => "Malawi","MY" => "Malaysia","MV" => "Maldives","ML" => "Mali","MT" => "Malta","MH" => "Marshall Islands","MQ" => "Martinique","MR" => "Mauritania","MU" => "Mauritius","YT" => "Mayotte","FX" => "Metropolitan France","MX" => "Mexico","FM" => "Micronesia","MI" => "Midway Islands","MD" => "Moldova","MC" => "Monaco","MN" => "Mongolia","ME" => "Montenegro","MS" => "Montserrat","MA" => "Morocco","MZ" => "Mozambique","MM" => "Myanmar [Burma]","NA" => "Namibia","NR" => "Nauru","NP" => "Nepal","NL" => "Netherlands","AN" => "Netherlands Antilles","NT" => "Neutral Zone","NC" => "New Caledonia","NZ" => "New Zealand","NI" => "Nicaragua","NE" => "Niger","NG" => "Nigeria","NU" => "Niue","NF" => "Norfolk Island","KP" => "North Korea","VD" => "North Vietnam","MP" => "Northern Mariana Islands","NO" => "Norway","OM" => "Oman","PC" => "Pacific Islands Trust Territory","PK" => "Pakistan","PW" => "Palau","PS" => "Palestinian Territories","PA" => "Panama","PZ" => "Panama Canal Zone","PG" => "Papua New Guinea","PY" => "Paraguay","YD" => "People\'s Democratic Republic of Yemen","PE" => "Peru","PH" => "Philippines","PN" => "Pitcairn Islands","PL" => "Poland","PT" => "Portugal","PR" => "Puerto Rico","QA" => "Qatar","RO" => "Romania","RW" => "Rwanda","RE" => "Réunion","BL" => "Saint Barthélemy","SH" => "Saint Helena","KN" => "Saint Kitts and Nevis","LC" => "Saint Lucia","MF" => "Saint Martin","PM" => "Saint Pierre and Miquelon","VC" => "Saint Vincent and the Grenadines","WS" => "Samoa","SM" => "San Marino","SA" => "Saudi Arabia","SN" => "Senegal","RS" => "Serbia","CS" => "Serbia and Montenegro","SC" => "Seychelles","SL" => "Sierra Leone","SG" => "Singapore","SK" => "Slovakia","SI" => "Slovenia","SB" => "Solomon Islands","SO" => "Somalia","ZA" => "South Africa","GS" => "South Georgia and the South Sandwich Islands","KR" => "South Korea","ES" => "Spain","LK" => "Sri Lanka","SD" => "Sudan","SR" => "Suriname","SJ" => "Svalbard and Jan Mayen","SZ" => "Swaziland","SE" => "Sweden","CH" => "Switzerland","SY" => "Syria","ST" => "São Tomé and Príncipe","TW" => "Taiwan","TJ" => "Tajikistan","TZ" => "Tanzania","TH" => "Thailand","TL" => "Timor-Leste","TG" => "Togo","TK" => "Tokelau","TO" => "Tonga","TT" => "Trinidad and Tobago","TN" => "Tunisia","TR" => "Turkey","TM" => "Turkmenistan","TC" => "Turks and Caicos Islands","TV" => "Tuvalu","UM" => "U.S. Minor Outlying Islands","PU" => "U.S. Miscellaneous Pacific Islands","VI" => "U.S. Virgin Islands","UG" => "Uganda","UA" => "Ukraine","AE" => "United Arab Emirates","GB" => "United Kingdom","US" => "United States","UY" => "Uruguay","UZ" => "Uzbekistan","VU" => "Vanuatu","VA" => "Vatican City","VE" => "Venezuela","VN" => "Vietnam","WK" => "Wake Island","WF" => "Wallis and Futuna","EH" => "Western Sahara","YE" => "Yemen","ZM" => "Zambia","ZW" => "Zimbabwe"];

		$data['supplers'] = $this->model_extension_feed_contentapi->getSupplers();

		$this->load->model('localisation/length_class');
		$data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();

		$this->load->model('localisation/weight_class');
		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/feed/contentapi/feed', $data));
	}

	public function copy() {
		$this->load->language('extension/feed/contentapi');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/feed/contentapi');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $feed_id) {
				$new_feed_id = $this->model_extension_feed_contentapi->copyFeed($feed_id);
				if (file_exists(DIR_CONFIG . 'content_api_'.$feed_id.'.json')) {
					copy(DIR_CONFIG . 'content_api_'.$feed_id.'.json', DIR_CONFIG . 'content_api_'.$new_feed_id.'.json');
				}
			}

			$this->session->data['success'] = $this->language->get('text_success_copy');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/feed/contentapi/listing', $this->token_var . '=' . $this->session->data[$this->token_var] . $url, true));
		}

		$this->getList();
	}

	protected function saveKey($feed_id) {
		file_put_contents(DIR_CONFIG . 'content_api_'.$feed_id.'.json', html_entity_decode($this->request->post['key']));
	}

	public function clearlog () {
		$this->load->language('extension/feed/contentapi');

		if (!$this->user->hasPermission('modify', 'extension/feed/contentapi')) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$file = DIR_LOGS . 'contentapi_'.$this->request->get['feed_id'].'.log';

			$handle = fopen($file, 'w+');

			fclose($handle);

			$this->session->data['success'] = $this->language->get('text_clear_success');
		}

		$url = '';

		if (isset($this->request->get['feed_id'])) {
			$url .= '&feed_id=' . (int)$this->request->get['feed_id'];
		}

		$this->response->redirect($this->url->link('extension/feed/contentapi/edit', $this->token_var . '=' . $this->session->data[$this->token_var] . $url, true));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/feed/contentapi')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!empty($this->request->post[$this->prefix.'contentapi_license']) && !empty($_SERVER['SERVER_NAME'])) {   
			$domain = preg_replace('/^www\./', '', $_SERVER['SERVER_NAME']);
			if ($this->request->server['HTTPS']) {
				$server = HTTPS_SERVER;
			} else {
				$server = HTTP_SERVER;
			}
			$parse_domain = parse_url($server);
			$config_domain = preg_replace('/^www\./', '', $parse_domain['host']);
			if ($domain == $config_domain) {
				if (filter_input(INPUT_POST, $this->prefix.'contentapi_license', FILTER_SANITIZE_SPECIAL_CHARS)!=hash('sha256', 'contentapi'.$domain.base64_decode('RGFSeU5hMw=='))) {
					$this->error['warning'] = $this->language->get('error_license3');
				} else {
					$this->confirm = true;
				}
			} else {
				$this->error['warning'] = $this->language->get('error_license2') . ' '.$domain.' ('.$config_domain.')';
			}
		} else {
			$this->error['warning'] = $this->language->get('error_license1');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'extension/feed/contentapi')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/feed/contentapi')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['key']) || !$this->_isJson($this->request->post['key'])) {
			$this->error['key'] = $this->language->get('error_key');
		}

		if (empty($this->request->post['merchantid'])) {
			$this->error['merchantid'] = $this->language->get('error_merchantid');
		}

		if (strpos($this->request->post['merchantid'], ' ') !== false) {
			$this->error['merchantid'] = $this->language->get('error_merchantid_space');
		}

		if (empty($this->request->post['label'])) {
			$this->error['label'] = $this->language->get('error_label');
		}

		$not_allowed = [' ','_','.',',','!','@','#','№','$','%','^','&','*','(',')','"',"'",'=','`','~',';','?','<','>','{','}','[',']','/','\\','|','+'];
		if (!$this->_isLatin($this->request->post['label']) || $this->_str_contain($this->request->post['label'], $not_allowed)) {
			$this->error['label'] = $this->language->get('error_label_char');
		}

		if (strlen($this->request->post['label']) > 20) {
			$this->error['label'] = $this->language->get('error_label_length');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function _str_contain($haystack, $needles, $offset = 0) {
		foreach($needles as $needle) {
			if (strpos($haystack, $needle, $offset) !== false) {
				return true;
			}
		}

		return false;
	}

	protected function _isLatin($str) {
		return (strlen($str) == strlen(utf8_decode($str))) ? true : false;
	}

	protected function _isJson($string) {
		$string = html_entity_decode($string);
		return is_string($string) && is_array(json_decode($string, true)) ? true : false;
	}

	protected function _isCurl() {
		return function_exists('curl_version');
	}

	public function import() {
		$this->load->language('extension/feed/contentapi');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/feed/contentapi')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
				$filename = basename(html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8'));

				if (utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)) != 'txt') {
					$json['error'] = $this->language->get('error_filetype');
				}

				if ($this->request->files['file']['type'] != 'text/plain') {
					$json['error'] = $this->language->get('error_filetype');
				}

				if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
					$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
				}
			} else {
				$json['error'] = $this->language->get('error_upload');
			}
		}

		if (!$json) {
			$json['success'] = $this->language->get('text_success');

			$this->load->model('extension/feed/contentapi');

			$content = file_get_contents($this->request->files['file']['tmp_name']);

			$this->model_extension_feed_contentapi->import($content);

			unlink($this->request->files['file']['tmp_name']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function category() {
		$data = $this->load->language('extension/feed/contentapi');

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['google_categories'] = [];

		$this->load->model('extension/feed/contentapi');

		$results = $this->model_extension_feed_contentapi->getCategories(['start'=>($page - 1) * 10, 'limit'=>10]);

		foreach ($results as $result) {
			$data['google_categories'][] = [
				'google_category_id' => $result['google_category_id'],
				'google_category'    => $result['google_category'],
				'category_id'        => $result['category_id'],
				'category'           => $result['category']
			];
		}

		$category_total = $this->model_extension_feed_contentapi->getTotalCategories();

		$pagination = new Pagination();
		$pagination->total = $category_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('extension/feed/contentapi/category', $this->token_var . '=' . $this->session->data[$this->token_var] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($category_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($category_total - 10)) ? $category_total : ((($page - 1) * 10) + 10), $category_total, ceil($category_total / 10));

		$this->response->setOutput($this->load->view('extension/feed/contentapi/category', $data));
	}

	public function addCategory() {
		$this->load->language('extension/feed/contentapi');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/feed/contentapi')) {
			$json['error'] = $this->language->get('error_permission');
		} elseif (!empty($this->request->post['google_category_id']) && !empty($this->request->post['category_id'])) {
			$this->load->model('extension/feed/contentapi');

			$this->model_extension_feed_contentapi->addCategory($this->request->post);

			$json['success'] = $this->language->get('text_success');
			$this->cache->delete('contentapi.categories');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function removeCategory() {
		$this->load->language('extension/feed/contentapi');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/feed/contentapi')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('extension/feed/contentapi');

			$this->model_extension_feed_contentapi->deleteCategory($this->request->post['category_id']);

			$json['success'] = $this->language->get('text_success');
			$this->cache->delete('contentapi.categories');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function autocomplete() {
		$json = [];

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/feed/contentapi');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			$filter_data = [
				'filter_name' => html_entity_decode($filter_name, ENT_QUOTES, 'UTF-8'),
				'start'       => 0,
				'limit'       => 35
			];

			$results = $this->model_extension_feed_contentapi->getGoogleBaseCategories($filter_data);

			foreach ($results as $result) {
				$json[] = [
					'google_category_id' => $result['google_category_id'],
					'name'                    => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				];
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getFilters() {
		$json = array();

		if (isset($this->request->get['filter_group_id'])) {
			$this->load->model('catalog/filter');

			$filters = $this->model_catalog_filter->getFilterDescriptions((int)$this->request->get['filter_group_id']);

			foreach ($filters as $filter) {
				if (!empty($filter['filter_description'][$this->config->get('config_language_id')]['name'])) {
					$json[] = array(
						'filter_id' => $filter['filter_id'],
						'name'      => strip_tags(html_entity_decode($filter['filter_description'][$this->config->get('config_language_id')]['name'], ENT_QUOTES, 'UTF-8'))
					);
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}