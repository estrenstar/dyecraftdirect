<?php
//==============================================================================
// UPS Pro v2025-6-25
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
// 
// All code within this file is copyright Clear Thinking, LLC.
// You may not copy or reuse code within this file without written permission.
//==============================================================================

//namespace Opencart\Admin\Controller\Extension\UpsPro\Shipping;
//class UpsPro extends \Opencart\System\Engine\Controller {

class ControllerExtensionShippingUpsPro extends Controller {
	
	private $type = 'shipping';
	private $name = 'ups_pro';
	
	//==============================================================================
	// uninstall()
	//==============================================================================
	public function uninstall() {
		if (version_compare(VERSION, '4.0', '>=')) {
			$this->load->model('setting/event');
			$this->model_setting_event->deleteEventByCode($this->name);
		}
	}
	
	//==============================================================================
	// index()
	//==============================================================================
	public function index() {
		$data = array(
			'type'			=> $this->type,
			'name'			=> $this->name,
			'autobackup'	=> false,
			'save_type'		=> 'keepediting',
			'permission'	=> $this->hasPermission('modify'),
		);
		
		$this->loadSettings($data);
		
		// Add Event hooks
		if (version_compare(VERSION, '4.0', '>=')) {
			$this->load->model('setting/event');
			if (!empty($this->request->get['save'])) {
				$this->model_setting_event->deleteEventByCode($this->name);
			}
			$event = $this->model_setting_event->getEventByCode($this->name);
			
			if (empty($event)) {
				$extension_base = 'extension/' . $this->name . '/' . $this->type . '/' . $this->name;
				$separator = (version_compare(VERSION, '4.0.2.0', '<')) ? '|' : '.';
				
				$triggers = array(
					'admin/view/sale/order_info/before'	=> $extension_base . $separator . 'addButtonEvent',
					'admin/view/sale/order_list/before'	=> $extension_base . $separator . 'addButtonEvent',
				);
				
				foreach ($triggers as $trigger => $action) {
					$this->model_setting_event->addEvent(array(
						'code'			=> $this->name,
						'description'	=> 'Event hook for the ' . $data['heading_title'] . ' extension by Clear Thinking',
						'trigger'		=> $trigger,
						'action'		=> $action,
						'status'		=> 1,
						'sort_order'	=> 0,
					));
				}
			}
		}
		
		// Check if the length and weight classes are set correctly
		$warnings = array();
		
		$default_length_class = $this->db->query("SELECT * FROM " . DB_PREFIX . "length_class WHERE length_class_id = " . (int)$this->config->get('config_length_class_id'))->row;
		if ($default_length_class['value'] != 1) {
			$warnings[] = 'Your default length class is not set to 1.0000, which may cause conversion issues. You should fix this in <a target="_blank" href="' . $this->url->link('localisation/length_class', 'token=' . $data['token'], 'SSL') . '">System > Settings > Length Classes</a>.';
		}
		
		$default_weight_class = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class WHERE weight_class_id = " . (int)$this->config->get('config_weight_class_id'))->row;
		if ($default_weight_class['value'] != 1) {
			$warnings[] = 'Your default weight class is not set to 1.0000, which may cause conversion issues. You should fix this in <a target="_blank" href="' . $this->url->link('localisation/weight_class', 'token=' . $data['token'], 'SSL') . '">System > Settings > Weight Classes</a>.';
		}
		
		if ($warnings) {
			$data['warning'] = implode('<br><br>', $warnings);
		}
		
		//------------------------------------------------------------------------------
		// Data Arrays
		//------------------------------------------------------------------------------
		$data['customer_group_array'] = array(0 => $data['text_guests']);
		$this->load->model((version_compare(VERSION, '2.1', '<') ? 'sale' : 'customer') . '/customer_group');
		foreach ($this->{'model_' . (version_compare(VERSION, '2.1', '<') ? 'sale' : 'customer') . '_customer_group'}->getCustomerGroups() as $customer_group) {
			$data['customer_group_array'][$customer_group['customer_group_id']] = $customer_group['name'];
		}
		
		$data['geo_zone_array'] = array(0 => $data['text_everywhere_else']);
		$this->load->model('localisation/geo_zone');
		foreach ($this->model_localisation_geo_zone->getGeoZones() as $geo_zone) {
			$data['geo_zone_array'][$geo_zone['geo_zone_id']] = $geo_zone['name'];
		}
		
		$config_language = ($this->config->get('config_language_admin')) ? $this->config->get('config_language_admin') : $this->config->get('config_language');
		$data['language_array'] = array($config_language => '');
		$data['language_flags'] = array();
		$this->load->model('localisation/language');
		foreach ($this->model_localisation_language->getLanguages() as $language) {
			$data['language_array'][$language['code']] = $language['name'];
			if (version_compare(VERSION, '2.2', '<')) {
				$data['language_flags'][$language['code']] = 'view/image/flags/' . $language['image'];
			} elseif (empty($language['extension'])) {
				$data['language_flags'][$language['code']] = 'language/' . $language['code'] . '/' . $language['code'] . '.png';
			} else {
				$data['language_flags'][$language['code']] = '../extension/' . $language['extension'] . '/admin/language/' . $language['code'] . '/' . $language['code'] . '.png';
			}
		}
		
		$data['order_status_array'] = array(0 => $data['text_no_change']);
		$this->load->model('localisation/order_status');
		foreach ($this->model_localisation_order_status->getOrderStatuses() as $order_status) {
			$data['order_status_array'][$order_status['order_status_id']] = $order_status['name'];
		}
		
		$data['store_array'] = array(0 => $this->config->get('config_name'));
		$store_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY name");
		foreach ($store_query->rows as $store) {
			$data['store_array'][$store['store_id']] = $store['name'];
		}
		
		$data['tax_class_array'] = array(0 => $data['text_none']);
		$this->load->model('localisation/tax_class');
		foreach ($this->model_localisation_tax_class->getTaxClasses() as $tax_class) {
			$data['tax_class_array'][$tax_class['tax_class_id']] = $tax_class['title'];
		}
		
		$data['product_fields'] = array(0 => $data['text_none']);
		$product_column_query = $this->db->query("DESCRIBE " . DB_PREFIX . "product");
		$fields_to_skip = array('date_added', 'date_available', 'date_modified', 'height', 'image', 'length', 'length_class_id', 'manufacturer_id', 'minimum', 'points', 'price', 'product_id', 'quantity', 'shipping', 'sort_order', 'status', 'stock_status_id', 'subtract', 'tax_class_id', 'viewed', 'weight', 'weight_class_id', 'width');
		foreach ($product_column_query->rows as $column) {
			if (in_array($column['Field'], $fields_to_skip)) continue;
			$data['product_fields'][$column['Field']] = $column['Field'];
		}
		asort($data['product_fields']);
		
		//------------------------------------------------------------------------------
		// Extensions Settings
		//------------------------------------------------------------------------------
		$data['settings'] = array();
		
		$data['settings'][] = array(
			'type'		=> 'tabs',
			'tabs'		=> array('extension_settings', 'restrictions', 'ups_settings', 'ups_services', 'adjustments', 'shipping_labels', 'testing_mode'),
		);
		$data['settings'][] = array(
			'key'		=> 'extension_settings',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'status',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_enabled'], 0 => $data['text_disabled']),
			'default'	=> 1,
		);
		$data['settings'][] = array(
			'key'		=> 'check_for_updates',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'heading',
			'type'		=> 'multilingual_text',
			'default'	=> 'UPS',
		);
		$data['settings'][] = array(
			'key'		=> 'sort_order',
			'type'		=> 'text',
			'default'	=> 1,
			'class'		=> 'short',
		);
		$data['settings'][] = array(
			'key'		=> 'tax_class_id',
			'type'		=> 'select',
			'options'	=> $data['tax_class_array'],
		);
		$data['settings'][] = array(
			'key'		=> 'rate_sorting',
			'type'		=> 'select',
			'options'	=> array('name' => $data['text_sort_by_name'], 'price_asc' => $data['text_sort_by_price_ascending'], 'price_desc' => $data['text_sort_by_price_descending']),
			'default'	=> 'price_asc',
		);
		$data['settings'][] = array(
			'key'		=> 'boxes_text',
			'type'		=> 'multilingual_text',
			'default'	=> '[boxes] Boxes:',
		);
		$data['settings'][] = array(
			'key'		=> 'weight_text',
			'type'		=> 'multilingual_text',
			'default'	=> '[weight]',
		);
		$data['settings'][] = array(
			'key'		=> 'signature_required_text',
			'type'		=> 'multilingual_text',
			'default'	=> 'Signature Required',
		);
		
		// Estimated Delivery Settings
		$data['settings'][] = array(
			'key'		=> 'estimated_delivery',
			'type'		=> 'heading',
		);
		
		$days_of_the_week = array(
			'Sunday'	=> $data['text_sunday'],
			'Monday'	=> $data['text_monday'],
			'Tuesday'	=> $data['text_tuesday'],
			'Wednesday'	=> $data['text_wednesday'],
			'Thursday'	=> $data['text_thursday'],
			'Friday'	=> $data['text_friday'],
			'Saturday'	=> $data['text_saturday'],
		);
		$data['settings'][] = array(
			'key'		=> 'shipping_days',
			'type'		=> 'checkboxes',
			'options'	=> $days_of_the_week,
			'default'	=> array_keys($days_of_the_week),
		);
		
		$data['settings'][] = array(
			'key'		=> 'estimated_delivery_text',
			'type'		=> 'multilingual_text',
			'default'	=> 'Estimated delivery by [date]',
		);
		$data['settings'][] = array(
			'key'		=> 'estimated_delivery_format',
			'type'		=> 'text',
			'default'	=> 'D. M. j, Y',
		);
		$data['settings'][] = array(
			'key'		=> 'estimated_delivery_padding',
			'type'		=> 'text',
			'class'		=> 'short',
		);
		$data['settings'][] = array(
			'key'		=> 'cutoff_time',
			'type'		=> 'text',
			'default'	=> '14:00',
			'attributes'=> array('style' => 'width: 55px !important'),
		);
		$data['settings'][] = array(
			'key'		=> 'holiday_dates',
			'type'		=> 'textarea',
			'attributes'=> array('style' => 'height: 200px !important; width: 150px !important'),
		);
		
		//------------------------------------------------------------------------------
		// Restrictions
		//------------------------------------------------------------------------------
		$data['settings'][] = array(
			'key'		=> 'restrictions',
			'type'		=> 'tab',
		);
		$data['settings'][] = array(
			'key'		=> 'restrictions',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'stores',
			'type'		=> 'checkboxes',
			'options'	=> $data['store_array'],
			'default'	=> array_keys($data['store_array']),
		);
		$data['settings'][] = array(
			'key'		=> 'geo_zones',
			'type'		=> 'checkboxes',
			'options'	=> $data['geo_zone_array'],
			'default'	=> array_keys($data['geo_zone_array']),
		);
		$data['settings'][] = array(
			'key'		=> 'customer_groups',
			'type'		=> 'checkboxes',
			'options'	=> $data['customer_group_array'],
			'default'	=> array_keys($data['customer_group_array']),
		);
		
		//------------------------------------------------------------------------------
		// UPS Settings
		//------------------------------------------------------------------------------
		if (!empty($data['saved']['access_token']))		$data['saved']['live_access_token'] = $data['saved']['access_token'];
		
		$data['settings'][] = array(
			'key'		=> 'ups_settings',
			'type'		=> 'tab',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> $data['help_ups_settings'],
		);
		$data['settings'][] = array(
			'key'		=> 'ups_settings',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'test_access_token',
			'type'		=> 'hidden',
		);
		$data['settings'][] = array(
			'key'		=> 'live_access_token',
			'type'		=> 'hidden',
		);
		$data['settings'][] = array(
			'key'		=> 'mode',
			'type'		=> 'select',
			'options'	=> array('test' => $data['text_test'], 'live' => $data['text_live']),
			'default'	=> 'test',
		);
		$data['settings'][] = array(
			'key'		=> 'client_id',
			'type'		=> 'text',
		);
		$data['settings'][] = array(
			'key'		=> 'client_secret',
			'type'		=> 'text',
		);
		$data['settings'][] = array(
			'key'		=> 'account_number',
			'type'		=> 'text',
			'class'		=> 'medium',
		);
		$data['settings'][] = array(
			'key'		=> 'store_address',
			'type'		=> 'html',
			'content'	=> '
				<input type="text" class="form-control" name="address" placeholder="' . $data['placeholder_street_address'] . '" value="' . (!empty($data['saved']['address']) ? $data['saved']['address'] : '') . '" style="width: 203px !important" /><br>
				<input type="text" class="form-control" name="city" placeholder="' . $data['placeholder_city'] . '" value="' . (!empty($data['saved']['city']) ? $data['saved']['city'] : '') . '" style="width: 140px !important" />
				<input type="text" class="form-control" name="state" placeholder="' . $data['placeholder_state'] . '" value="' . (!empty($data['saved']['state']) ? $data['saved']['state'] : '') . '" style="width: 60px !important" maxlength="5" /><br>
				<input type="text" class="form-control" name="postcode" placeholder="' . $data['placeholder_postcode'] . '" value="' . (!empty($data['saved']['postcode']) ? $data['saved']['postcode'] : '') . '" style="width: 100px !important" />
				<input type="text" class="form-control" name="country" placeholder="' . $data['placeholder_country'] . '" value="' . (!empty($data['saved']['country']) ? $data['saved']['country'] : '') . '" style="width: 70px !important" maxlength="2" />
			',
		);
		
		// Rate Settings
		$data['settings'][] = array(
			'key'		=> 'rate_settings',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'insurance',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'customer_classification',
			'type'		=> 'select',
			'options'	=> array(
				'00'	=> $data['text_rates_associated'],
				'01'	=> $data['text_daily_rates'],
				'04'	=> $data['text_retail_rates'],
				'05'	=> $data['text_regional_rates'],
				'06'	=> $data['text_general_list_rates'],
				'53'	=> $data['text_standard_list_rates'],
			),
			'default'	=> '00',
		);
		$data['settings'][] = array(
			'key'		=> 'pickup_type',
			'type'		=> 'select',
			'options'	=> array('none' => $data['text_no_pickup_type'], '01' => $data['text_daily_pickup'], '03' => $data['text_customer_counter']),
			'default'	=> '01',
		);
		$data['settings'][] = array(
			'key'		=> 'signature_type',
			'type'		=> 'select',
			'options'	=> array(
				0	=> $data['text_no_signature_required'],
				1	=> $data['text_signature_required'],
				2	=> $data['text_adult_signature_required'],
			),
			'after'		=> $data['text_signature_type_help'],
		);
		$data['settings'][] = array(
			'key'		=> 'signature_choice',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'negotiated_rates',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		
		// Package Settings
		$data['settings'][] = array(
			'key'		=> 'package_settings',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'weight_units',
			'type'		=> 'select',
			'options'	=> array('lb' => $data['text_pounds'], 'kg' => $data['text_kilograms']),
			'default'	=> 'lb',
		);
		$data['settings'][] = array(
			'key'		=> 'dimension_units',
			'type'		=> 'select',
			'options'	=> array('in' => $data['text_inches'], 'cm' => $data['text_centimeters']),
			'default'	=> 'in',
		);
		$data['settings'][] = array(
			'key'		=> 'avoid_long_boxes',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 1,
		);
		$data['settings'][] = array(
			'key'		=> 'avoid_oversize_fees',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 1,
		);
		$data['settings'][] = array(
			'key'		=> 'weight_limit',
			'type'		=> 'text',
			'class'		=> 'short',
			'default'	=> 150,
		);
		$data['settings'][] = array(
			'key'		=> 'divide_heavy_products',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'packaging_method',
			'type'		=> 'select',
			'options'	=> array('individual' => $data['text_individual_per_item'], 'algorithm' => $data['text_use_packing_algorithm']),
			'default'	=> 'algorithm',
			'after'		=> $data['text_packaging_method_help'],
		);
		
		//------------------------------------------------------------------------------
		// UPS Services
		//------------------------------------------------------------------------------
		$data['settings'][] = array(
			'key'		=> 'ups_services',
			'type'		=> 'tab',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info text-center">' . $data['help_ups_services'] . '</div>',
		);
		
		$geo_zones = $data['geo_zone_array'];
		$geo_zones[0] = $data['text_default_geo_zones'];
		
		$services = array('03', '12', '02', '59', '01', '13', '14', '92', '93', '11', '65', '07', '08', '54');
		
		foreach ($services as $service) {
			$data['settings'][] = array(
				'type'		=> 'heading',
				'text'		=> $data['text_service_' . $service],
			);
			$data['settings'][] = array(
				'key'		=> 'services_' . $service,
				'type'		=> 'multilingual_text',
				'title'		=> $data['entry_service_name'],
				'default'	=> $data['text_service_' . $service],
			);
			$data['settings'][] = array(
				'key'		=> $service . '_geo_zone',
				'type'		=> 'select',
				'title'		=> $data['entry_service_geo_zone'],
				'options'	=> $geo_zones,
			);
			$data['settings'][] = array(
				'key'		=> $service . '_total_limit',
				'type'		=> 'text',
				'title'		=> $data['entry_service_total_limit'],
				'attributes'=> array('style' => 'width: 100px !important'),
			);
			$data['settings'][] = array(
				'key'		=> $service . '_weight_limit',
				'type'		=> 'text',
				'title'		=> $data['entry_service_weight_limit'],
				'attributes'=> array('style' => 'width: 100px !important'),
			);
			$data['settings'][] = array(
				'key'		=> $service . '_rate_adjustment',
				'type'		=> 'text',
				'title'		=> $data['entry_rate_adjustment'],
				'attributes'=> array('style' => 'width: 100px !important'),
			);
		}
		
		//------------------------------------------------------------------------------
		// Adjustments
		//------------------------------------------------------------------------------
		$data['settings'][] = array(
			'key'		=> 'adjustments',
			'type'		=> 'tab',
		);
		
		// Domestic Adjustments
		$data['settings'][] = array(
			'key'		=> 'domestic_adjustments',
			'type'		=> 'heading',
		);
		
		if (version_compare(VERSION, '4.1', '<')) {
			$default_country_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE country_id = " . (int)$this->config->get('config_country_id'));
		} else {
			$default_country_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country c LEFT JOIN " . DB_PREFIX . "country_description cd ON (cd.country_id = c.country_id) WHERE c.country_id = " . (int)$this->config->get('config_country_id') . " AND cd.language_id = " . (int)$this->config->get('config_language_id'));
		}
		
		if ($default_country_query->num_rows) {
			$default_country = $default_country_query->row['name'];
		} else {
			$default_country = '';
		}

		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info text-center">' . $data['help_domestic'] . ' ' . $default_country . '.</div>',
		);
		$data['settings'][] = array(
			'key'		=> 'domestic_set_box_dimensions',
			'type'		=> 'textarea',
			'attributes'=> array('style' => 'font: 14px monospace !important; height: 250px !important; width: 250px !important;'),
		);
		$data['settings'][] = array(
			'key'		=> 'domestic_item_dimension',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 55px !important'),
		);
		$data['settings'][] = array(
			'key'		=> 'domestic_item_weight',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 55px !important'),
		);
		$data['settings'][] = array(
			'key'		=> 'domestic_box_weight',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 55px !important'),
		);
		$data['settings'][] = array(
			'key'		=> 'domestic_rate_adjustment',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 55px !important'),
		);
		
		// International Adjustments
		$data['settings'][] = array(
			'key'		=> 'international_adjustments',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info text-center">' . $data['help_international'] . '</div>',
		);
		$data['settings'][] = array(
			'key'		=> 'international_set_box_dimensions',
			'type'		=> 'textarea',
			'attributes'=> array('style' => 'font: 14px monospace !important; height: 250px !important; width: 250px !important;'),
		);
		$data['settings'][] = array(
			'key'		=> 'international_item_dimension',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 55px !important'),
		);
		$data['settings'][] = array(
			'key'		=> 'international_item_weight',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 55px !important'),
		);
		$data['settings'][] = array(
			'key'		=> 'international_box_weight',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 55px !important'),
		);
		$data['settings'][] = array(
			'key'		=> 'international_rate_adjustment',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 55px !important'),
		);
		
		//------------------------------------------------------------------------------
		// Shipping Labels
		//------------------------------------------------------------------------------
		$data['settings'][] = array(
			'key'		=> 'shipping_labels',
			'type'		=> 'tab',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info text-center">' . $data['help_for_shipping_labels'] . '</div>',
		);
		$data['settings'][] = array(
			'key'		=> 'shipping_labels',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'shipping_labels',
			'type'		=> 'select',
			'options'	=> array(0 => $data['text_no'], 'order_page' => $data['text_yes_order_info_page'], 'both_pages' => $data['text_yes_both_pages']),
			'default'	=> 'order_page',
		);
		
		$default_services = array(0 => $data['text_do_not_allow']);
		foreach ($services as $service) {
			$default_services[$service] = $data['text_service_' . $service];
		}
		
		$data['settings'][] = array(
			'key'		=> 'default_service',
			'type'		=> 'select',
			'options'	=> $default_services,
		);
		$data['settings'][] = array(
			'key'		=> 'shipfrom_name',
			'type'		=> 'text',
			'default'	=> $this->config->get('config_name'),
		);
		$data['settings'][] = array(
			'key'		=> 'attention_name',
			'type'		=> 'text',
			'default'	=> $this->config->get('config_owner'),
		);
		$data['settings'][] = array(
			'key'		=> 'phone_number',
			'type'		=> 'text',
			'class'		=> 'medium',
		);
		$data['settings'][] = array(
			'key'		=> 'shipping_order_status',
			'type'		=> 'select',
			'options'	=> $data['order_status_array'],
			'default'	=> 3,
		);
		
		// International Invoice Settings
		$data['settings'][] = array(
			'key'		=> 'international_invoice_settings',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'invoice_tax_id',
			'type'		=> 'text',
			'class'		=> 'medium',
		);
		$data['settings'][] = array(
			'key'		=> 'invoice_commodity_code',
			'type'		=> 'text',
			'class'		=> 'medium',
		);
		$data['settings'][] = array(
			'key'		=> 'invoice_commodity_code_field',
			'type'		=> 'select',
			'options'	=> $data['product_fields'],
		);
		$data['settings'][] = array(
			'key'		=> 'invoice_country_code_field',
			'type'		=> 'select',
			'options'	=> $data['product_fields'],
		);
		$data['settings'][] = array(
			'key'		=> 'invoice_comments',
			'type'		=> 'textarea',
			'attributes'=> array('maxlength' => '150'),
		);
		$data['settings'][] = array(
			'key'		=> 'invoice_declaration_statement',
			'type'		=> 'textarea',
			'attributes'=> array('maxlength' => '550'),
			'default'	=> 'I hereby certify that the information on this invoice is true and correct and that the contents and value of this shipment are as stated above.',
		);
		
		// E-mail Settings
		$data['settings'][] = array(
			'key'		=> 'email_settings',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'copy_emails',
			'type'		=> 'text',
		);
		$data['settings'][] = array(
			'key'		=> 'customer_email',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 1,
		);
		$data['settings'][] = array(
			'key'		=> 'customer_subject',
			'type'		=> 'multilingual_text',
			'default'	=> '[store_name] Order #[order_id] has shipped!',
		);
		$data['settings'][] = array(
			'key'		=> 'customer_message',
			'type'		=> 'multilingual_textarea',
			'class'		=> 'editor',
			'default'	=> '
<p style="text-align: center"><img src="' . HTTP_CATALOG . 'image/' . $this->config->get('config_logo') . '" /></p>
<br>
<p>Hello [shipping_firstname]! Your order is on its way to you. If you ordered multiple items, be aware that they might come in separate shipments.</p>
<br>
<p><a target="_blank" style="background: #2BF; border-radius: 5px; color: white; font-size: 14px; padding: 10px 20px;" href="[tracking_url]">Track Your Order</a></p>
<br>
<p>Thanks again for your purchase!</p>
<p>[store_name]<br>[store_url]</p>
			',
		);
		
		//------------------------------------------------------------------------------
		// Testing Mode
		//------------------------------------------------------------------------------
		$data['settings'][] = array(
			'key'		=> 'testing_mode',
			'type'		=> 'tab',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info text-center pad-bottom">' . $data['testing_mode_help'] . '</div>',
		);
		
		$filepath = DIR_LOGS . $this->name . '.messages';
		$testing_mode_log = '';
		$refresh_or_download_button = '<a class="btn btn-info" onclick="refreshLog()"><i class="fa fa-refresh fa-sync-alt pad-right-sm"></i> ' . $data['button_refresh_log'] . '</a>';
		
		if (file_exists($filepath)) {
			$filesize = filesize($filepath);
			if ($filesize > 999999) {
				$testing_mode_log = $data['standard_testing_mode'];
				$refresh_or_download_button = '<a class="btn btn-info" href="index.php?route=' . $data['extension_route'] . '/downloadLog&token=' . $data['token'] . '"><i class="fa fa-download pad-right-sm"></i> ' . $data['button_download_log'] . ' (' . round($filesize / 1000000, 1) . ' MB)</a>';
			} else {
				$testing_mode_log = html_entity_decode(file_get_contents($filepath), ENT_QUOTES, 'UTF-8');
			}
		}
		
		$data['settings'][] = array(
			'key'		=> 'testing_mode',
			'type'		=> 'heading',
			'buttons'	=> $refresh_or_download_button . ' <a class="btn btn-danger" onclick="clearLog()"><i class="fa fa-trash-o fa-trash-alt pad-right-sm"></i> ' . $data['button_clear_log'] . '</a>',
		);
		$data['settings'][] = array(
			'key'		=> 'testing_mode',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_enabled'], 0 => $data['text_disabled']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'testing_messages',
			'type'		=> 'textarea',
			'class'		=> 'nosave',
			'attributes'=> array('style' => 'width: 100% !important; height: 400px; font-size: 12px !important'),
			'default'	=> htmlentities($testing_mode_log),
		);
		
		//------------------------------------------------------------------------------
		// end settings
		//------------------------------------------------------------------------------
		
		$this->document->setTitle($data['heading_title']);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		if (version_compare(VERSION, '4.0', '<')) {
			$template_file = DIR_TEMPLATE . 'extension/' . $this->type . '/' . $this->name . '.twig';
		} elseif (defined('DIR_EXTENSION')) {
			$template_file = DIR_EXTENSION . $this->name . '/admin/view/template/' . $this->type . '/' . $this->name . '.twig';
		}
		
		if (is_file($template_file)) {
			extract($data);
			
			ob_start();
			if (version_compare(VERSION, '4.0', '<')) {
				require(class_exists('VQMod') ? \VQMod::modCheck(modification($template_file)) : modification($template_file));
			} else {
				require(class_exists('VQMod') ? \VQMod::modCheck($template_file) : $template_file);
			}
			$output = ob_get_clean();
			
			if (version_compare(VERSION, '3.0', '>=')) {
				$output = str_replace(array('&token=', '&amp;token='), '&user_token=', $output);
			}
			
			if (version_compare(VERSION, '4.0', '>=')) {
				$separator = (version_compare(VERSION, '4.0.2.0', '<')) ? '|' : '.';
				$output = str_replace($data['extension_route'] . '/', $data['extension_route'] . $separator, $output);
			}
			
			echo $output;
		} else {
			echo 'Error loading template file: ' . $template_file;
		}
	}
	
	//==============================================================================
	// Helper functions
	//==============================================================================
	private function hasPermission($permission) {
		if (version_compare(VERSION, '2.3', '<')) {
			return $this->user->hasPermission($permission, $this->type . '/' . $this->name);
		} elseif (version_compare(VERSION, '4.0', '<')) {
			return $this->user->hasPermission($permission, 'extension/' . $this->type . '/' . $this->name);
		} else {
			return $this->user->hasPermission($permission, 'extension/' . $this->name . '/' . $this->type . '/' . $this->name);
		}
	}
	
	private function loadLanguage($path) {
		$_ = array();
		$language = array();
		if (version_compare(VERSION, '2.2', '<')) {
			$admin_language = $this->db->query("SELECT * FROM " . DB_PREFIX . "language WHERE `code` = '" . $this->db->escape($this->config->get('config_admin_language')) . "'")->row['directory'];
		} elseif (version_compare(VERSION, '4.0', '<')) {
			$admin_language = $this->config->get('config_admin_language');
		} else {
			$admin_language = $this->config->get('config_language_admin');
		}
		foreach (array('english', 'en-gb', $admin_language) as $directory) {
			$file = DIR_LANGUAGE . $directory . '/' . $directory . '.php';
			if (file_exists($file)) require(class_exists('VQMod') ? \VQMod::modCheck($file) : $file);
			$file = DIR_LANGUAGE . $directory . '/default.php';
			if (file_exists($file)) require(class_exists('VQMod') ? \VQMod::modCheck($file) : $file);
			$file = DIR_LANGUAGE . $directory . '/' . $path . '.php';
			if (file_exists($file)) require(class_exists('VQMod') ? \VQMod::modCheck($file) : $file);
			$file = DIR_LANGUAGE . $directory . '/extension/' . $path . '.php';
			if (file_exists($file)) require(class_exists('VQMod') ? \VQMod::modCheck($file) : $file);
			if (defined('DIR_EXTENSION')) {
				$file = DIR_EXTENSION . 'opencart/admin/language/' . $directory . '/' . $path . '.php';
				if (file_exists($file)) require(class_exists('VQMod') ? \VQMod::modCheck($file) : $file);
				$explode = explode('/', $path);
				$file = DIR_EXTENSION . $explode[1] . '/admin/language/' . $directory . '/' . $path . '.php';
				if (file_exists($file)) require(class_exists('VQMod') ? \VQMod::modCheck($file) : $file);
				$file = DIR_EXTENSION . $this->name . '/admin/language/' . $directory . '/' . $path . '.php';
				if (file_exists($file)) require(class_exists('VQMod') ? \VQMod::modCheck($file) : $file);
			}
			$language = array_merge($language, $_);
		}
		return $language;
	}
	
	private function getTableRowNumbers(&$data, $table, $sorting) {
		$groups = array();
		$rules = array();
		
		foreach ($data['saved'] as $key => $setting) {
			if (preg_match('/' . $table . '_(\d+)_' . $sorting . '/', $key, $matches)) {
				$groups[$setting][] = $matches[1];
			}
			if (preg_match('/' . $table . '_(\d+)_rule_(\d+)_type/', $key, $matches)) {
				$rules[$matches[1]][$setting . $matches[2]] = $matches[2];
			}
		}
		
		if (empty($groups)) $groups = array('' => array('1'));
		ksort($groups, defined('SORT_NATURAL') ? SORT_NATURAL : SORT_REGULAR);
		
		foreach ($rules as $key => $rule) {
			ksort($rules[$key], defined('SORT_NATURAL') ? SORT_NATURAL : SORT_REGULAR);
		}
		
		$data['used_rows'][$table] = array();
		$rows = array();
		foreach ($groups as $group) {
			foreach ($group as $num) {
				$data['used_rows'][preg_replace('/module_(\d+)_/', '', $table)][] = $num;
				$rows[$num] = (empty($rules[$num])) ? array() : $rules[$num];
			}
		}
		sort($data['used_rows'][$table]);
		
		return $rows;
	}
	
	//==============================================================================
	// loadSettings()
	//==============================================================================
	private $encryption_key = '';
	
	public function loadSettings(&$data) {
		$backup_type = (empty($data)) ? 'manual' : 'auto';
		if ($backup_type == 'manual' && !$this->hasPermission('modify')) {
			return;
		}
		
		$this->cache->delete($this->name);
		unset($this->session->data[$this->name]);
		$code = (version_compare(VERSION, '3.0', '<') ? '' : $this->type . '_') . $this->name;
		
		// Set URL data
		$data['token'] = $this->session->data[version_compare(VERSION, '3.0', '<') ? 'token' : 'user_token'];
		$data['exit'] = $this->url->link((version_compare(VERSION, '3.0', '<') ? 'extension' : 'marketplace') . '/' . (version_compare(VERSION, '2.3', '<') ? '' : 'extension&type=') . $this->type . '&token=' . $data['token'], '', 'SSL');
		$data['extension_route'] = 'extension/' . (version_compare(VERSION, '4.0', '<') ? '' : $this->name . '/') . $this->type . '/' . $this->name;
		
		// Load saved settings
		$data['saved'] = array();
		$settings_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `key` ASC");
		
		foreach ($settings_query->rows as $setting) {
			$key = str_replace($code . '_', '', $setting['key']);
			$value = $setting['value'];
			if ($setting['serialized']) {
				$value = (version_compare(VERSION, '2.1', '<')) ? unserialize($setting['value']) : json_decode($setting['value'], true);
			}
			
			$data['saved'][$key] = $value;
			
			if (is_array($value)) {
				foreach ($value as $num => $value_array) {
					foreach ($value_array as $k => $v) {
						$data['saved'][$key . '_' . $num . '_' . $k] = $v;
					}
				}
			}
		}
		
		// Load language and run standard checks
		$data = array_merge($data, $this->loadLanguage($this->type . '/' . $this->name));
		
		if (ini_get('max_input_vars') && ((ini_get('max_input_vars') - count($data['saved'])) < 50)) {
			$data['warning'] = $data['standard_max_input_vars'];
		}
		
		// Modify files according to OpenCart version
		if ($this->type == 'total') {
			if (version_compare(VERSION, '2.2', '<')) {
				$filepath = DIR_CATALOG . 'model/' . $this->type . '/' . $this->name . '.php';
				file_put_contents($filepath, str_replace('public function getTotal($total) {', 'public function getTotal(&$total_data, &$order_total, &$taxes) {' . "\n\t\t" . '$total = array("totals" => &$total_data, "total" => &$order_total, "taxes" => &$taxes);', file_get_contents($filepath)));
			} elseif (defined('DIR_EXTENSION')) {
				$filepath = DIR_EXTENSION . $this->name . '/catalog/model/' . $this->type . '/' . $this->name . '.php';
				file_put_contents($filepath, str_replace('public function getTotal($total_input) {', 'public function getTotal(&$total_data, &$taxes, &$order_total) {', file_get_contents($filepath)));
			}
		}
		
		if (version_compare(VERSION, '2.3', '>=')) {
			$filepaths = array(
				DIR_APPLICATION . 'controller/' . $this->type . '/' . $this->name . '.php',
				DIR_CATALOG . 'controller/' . $this->type . '/' . $this->name . '.php',
				DIR_CATALOG . 'model/' . $this->type . '/' . $this->name . '.php',
			);
			foreach ($filepaths as $filepath) {
				if (file_exists($filepath)) {
					rename($filepath, str_replace('.php', '.php-OLD', $filepath));
				}
			}
		}
		
		if (version_compare(VERSION, '4.0', '>=')) {
			$extension_install_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension_install WHERE `code` = '" . $this->db->escape($this->name) . "'");
			if ($extension_install_query->row['version'] == 'unlicensed') {
				$this->db->query("UPDATE " . DB_PREFIX . "extension_install SET version = '" . $this->db->escape($data['version']) . "' WHERE `code` = '" . $this->db->escape($this->name) . "'");
			}
		}
		
		// Set save type and skip auto-backup if not needed
		if (!empty($data['saved']['autosave'])) {
			$data['save_type'] = 'auto';
		}
		
		if ($backup_type == 'auto' && empty($data['autobackup'])) {
			return;
		}
		
		// Create settings auto-backup file
		$manual_filepath = DIR_LOGS . $this->name . $this->encryption_key . '.backup';
		$auto_filepath = DIR_LOGS . $this->name . $this->encryption_key . '.autobackup';
		$filepath = ($backup_type == 'auto') ? $auto_filepath : $manual_filepath;
		if (file_exists($filepath)) unlink($filepath);
		
		file_put_contents($filepath, 'SETTING	NUMBER	SUB-SETTING	SUB-NUMBER	SUB-SUB-SETTING	VALUE' . "\n", FILE_APPEND|LOCK_EX);
		
		foreach ($data['saved'] as $key => $value) {
			if (is_array($value)) continue;
			
			$parts = explode('|', preg_replace(array('/_(\d+)_/', '/_(\d+)/'), array('|$1|', '|$1'), $key));
			
			$line = '';
			for ($i = 0; $i < 5; $i++) {
				$line .= (isset($parts[$i]) ? $parts[$i] : '') . "\t";
			}
			$line .= str_replace(array("\t", "\n"), array('	', '\n'), $value) . "\n";
			
			file_put_contents($filepath, $line, FILE_APPEND|LOCK_EX);
		}
		
		$data['autobackup_time'] = date('Y-M-d @ g:i a');
		$data['backup_time'] = (file_exists($manual_filepath)) ? date('Y-M-d @ g:i a', filemtime($manual_filepath)) : '';
		
		if ($backup_type == 'manual') {
			echo $data['autobackup_time'];
		}
	}
	
	//==============================================================================
	// saveSettings()
	//==============================================================================
	public function saveSettings() {
		if (!$this->hasPermission('modify')) {
			echo 'PermissionError';
			return;
		}
		
		$this->cache->delete($this->name);
		unset($this->session->data[$this->name]);
		$code = (version_compare(VERSION, '3.0', '<') ? '' : $this->type . '_') . $this->name;
		
		if ($this->request->get['saving'] == 'manual') {
			$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($code) . "' AND `key` != '" . $this->db->escape($this->name . '_module') . "'");
		}
		
		$module_id = 0;
		$modules = array();
		$module_instance = false;
		
		foreach ($this->request->post as $key => $value) {
			if (strpos($key, 'module_') === 0) {
				$parts = explode('_', $key, 3);
				$module_id = $parts[1];
				$modules[$parts[1]][$parts[2]] = $value;
				if ($parts[2] == 'module_id') $module_instance = true;
			} else {
				$key = (version_compare(VERSION, '3.0', '<') ? '' : $this->type . '_') . $this->name . '_' . $key;
				
				if ($this->request->get['saving'] == 'auto') {
					$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($code) . "' AND `key` = '" . $this->db->escape($key) . "'");
				}
				
				$this->db->query("
					INSERT INTO " . DB_PREFIX . "setting SET
					`store_id` = 0,
					`code` = '" . $this->db->escape($code) . "',
					`key` = '" . $this->db->escape($key) . "',
					`value` = '" . $this->db->escape(stripslashes(is_array($value) ? implode(';', $value) : $value)) . "',
					`serialized` = 0
				");
			}
		}
		
		foreach ($modules as $module_id => $module) {
			$module_code = (version_compare(VERSION, '4.0', '<')) ? $this->name : $this->name . '.' . $this->name;
			if (!$module_id) {
				$this->db->query("
					INSERT INTO " . DB_PREFIX . "module SET
					`name` = '" . $this->db->escape($module['name']) . "',
					`code` = '" . $this->db->escape($module_code) . "',
					`setting` = ''
				");
				$module_id = $this->db->getLastId();
				$module['module_id'] = $module_id;
			}
			$module_settings = (version_compare(VERSION, '2.1', '<')) ? serialize($module) : json_encode($module);
			$this->db->query("
				UPDATE " . DB_PREFIX . "module SET
				`name` = '" . $this->db->escape($module['name']) . "',
				`code` = '" . $this->db->escape($module_code) . "',
				`setting` = '" . $this->db->escape($module_settings) . "'
				WHERE module_id = " . (int)$module_id . "
			");
		}
	}
	
	//==============================================================================
	// deleteSetting()
	//==============================================================================
	public function deleteSetting() {
		if (!$this->hasPermission('modify')) {
			echo 'PermissionError';
			return;
		}
		$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : $this->type . '_';
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($prefix . $this->name) . "' AND `key` = '" . $this->db->escape($prefix . $this->name . '_' . str_replace('[]', '', $this->request->get['setting'])) . "'");
	}
	
	//==============================================================================
	// checkVersion()
	//==============================================================================
	public function checkVersion() {
		$data = $this->loadLanguage($this->type . '/' . $this->name);
		
		$curl = curl_init('https://www.getclearthinking.com/downloads/checkVersion?extension=' . urlencode($data['heading_title']));
		curl_setopt_array($curl, array(
			CURLOPT_CONNECTTIMEOUT	=> 10,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_TIMEOUT			=> 10,
		));
		$response = curl_exec($curl);
		curl_close($curl);
		
		echo $response;
	}
	
	//==============================================================================
	// update()
	//==============================================================================
	public function update() {
		$data = $this->loadLanguage($this->type . '/' . $this->name);
		
		$curl = curl_init('https://www.getclearthinking.com/downloads/update?extension=' . urlencode($data['heading_title']) . '&domain=' . $this->request->server['HTTP_HOST'] . '&key=' . $this->request->post['license_key']);
		curl_setopt_array($curl, array(
			CURLOPT_CONNECTTIMEOUT	=> 10,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_TIMEOUT			=> 10,
		));
		$response = curl_exec($curl);
		curl_close($curl);
		
		if (strpos($response, '<i') === 0) {
			echo $response;
			return;
		}
		
		$first_zip = DIR_DOWNLOAD . 'clearthinking.zip';
		$file = fopen($first_zip, 'w+');
		fwrite($file, $response);
		fclose($file);
		
		$temp_directory = DIR_DOWNLOAD . 'clearthinking/';
		$zip = new \ZipArchive();

		if ($zip->open($first_zip) === true) {
			$zip->extractTo($temp_directory);
			$zip->close();
		} else {
			echo 'Invalid zip archive';
			return;
		}
		
		@unlink($first_zip);
		
		if (version_compare(VERSION, '2.0', '<')) {
			$second_zip = $temp_directory . 'OpenCart 1.5 Versions.zip';
		} elseif (version_compare(VERSION, '2.3', '<')) {
			$second_zip = $temp_directory . 'OpenCart 2.0-2.2 Versions.ocmod.zip';
		} elseif (version_compare(VERSION, '4.0', '<')) {
			$second_zip = $temp_directory . 'OpenCart 2.3-3.0 Versions.ocmod.zip';
		} else {
			$second_zip = $temp_directory . 'OpenCart 4 Versions.zip';
		}
		
		$zip = new \ZipArchive();
		
		if (version_compare(VERSION, '4.0', '<')) {
			if ($zip->open($second_zip) === true) {
				$admin_directory = basename(DIR_APPLICATION);
				
				for ($i = 0; $i < $zip->numFiles; $i++) {
					$filepath = str_replace(array('upload/', 'admin/'), array('', $admin_directory . '/'), $zip->getNameIndex($i));
					
					if (strpos($filepath, '.txt')) {
						continue;
					}
					
					if ($filepath === 'install.xml') {
						$xml = $zip->getFromIndex($i);
						
						foreach (array('name', 'code', 'version', 'author', 'link') as $tag) {
							$first_explosion = explode('<' . $tag . '>', $xml);
							$second_explosion = explode('</' . $tag . '>', $first_explosion[1]);
							${'xml_'.$tag} = $second_explosion[0];
						}
						
						$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE code = '" . $this->db->escape($xml_code) . "'");
						
						$this->db->query("INSERT INTO " . DB_PREFIX . "modification SET code = '" . $this->db->escape($xml_code) . "', name = '" . $this->db->escape($xml_name) . "', author = '" . $this->db->escape($xml_author) . "', version = '" . $this->db->escape($xml_version) . "', link = '" . $this->db->escape($xml_link) . "', xml = '" . $this->db->escape($xml) . "', status = 1, date_added = NOW()");
						
						continue;
					}
					
					$full_filepath = DIR_APPLICATION . '../' . $filepath;
					
					if (!strpos($filepath, '.')) {
						if (!is_dir($full_filepath)) {
							mkdir($full_filepath, 0777);
						}
						continue;
					}
					
					file_put_contents($full_filepath, $zip->getFromIndex($i));
				}
				
				$zip->close();
			} else {
				echo 'Invalid zip archive';
				return;
			}
		} else {
			if ($zip->open($second_zip) === true) {
				$zip->extractTo($temp_directory);
				$zip->close();
			} else {
				echo 'Invalid zip archive';
				return;
			}
			
			$third_zip = $temp_directory . $this->name . '.ocmod.zip';
			$zip = new \ZipArchive();
			
			if ($zip->open($third_zip) === true) {
				$zip->extractTo(DIR_EXTENSION . $this->name . '/');
				$zip->close();
			} else {
				echo 'Invalid zip archive';
				return;
			}
			
			@unlink($third_zip);
		}
		
		@array_map('unlink', array_filter((array)glob($temp_directory . '*')));
		@rmdir($temp_directory);
		
		echo 'success';
	}
	
	//==============================================================================
	// refreshLog()
	//==============================================================================
	public function refreshLog() {
		$data = $this->loadLanguage($this->type . '/' . $this->name);
		
		if (!$this->hasPermission('modify')) {
			echo $data['standard_error'];
			return;
		}
		
		$filepath = DIR_LOGS . $this->name . '.messages';
		
		if (file_exists($filepath)) {
			if (filesize($filepath) > 999999) {
				echo $data['standard_testing_mode'];
			} else {
				echo html_entity_decode(file_get_contents($filepath), ENT_QUOTES, 'UTF-8');
			}
		}
	}
	
	//==============================================================================
	// downloadLog()
	//==============================================================================
	public function downloadLog() {
		$file = DIR_LOGS . $this->name . '.messages';
		if (!file_exists($file) || !$this->hasPermission('access')) {
			return;
		}
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename=' . $this->name . '.' . date('Y-n-d') . '.log');
		header('Content-Length: ' . filesize($file));
		header('Content-Transfer-Encoding: binary');
		header('Content-Type: text/plain');
		header('Expires: 0');
		header('Pragma: public');
		readfile($file);
	}
	
	//==============================================================================
	// clearLog()
	//==============================================================================
	public function clearLog() {
		$data = $this->loadLanguage($this->type . '/' . $this->name);
		
		if (!$this->hasPermission('modify')) {
			echo $data['standard_error'];
			return;
		}
		
		file_put_contents(DIR_LOGS . $this->name . '.messages', '');
	}
	
	//==============================================================================
	// loadCatalogModel()
	//==============================================================================
	private function loadCatalogModel() {
		if (version_compare(VERSION, '4.0', '<')) {
			$model_file = DIR_CATALOG . 'model/extension/' . $this->type . '/' . $this->name . '.php';
			require_once(class_exists('VQMod') ? \VQMod::modCheck(modification($model_file)) : modification($model_file));
			return new ModelExtensionShippingUpsPro($this->registry);
		} elseif (defined('DIR_EXTENSION')) {
			$model_file = DIR_EXTENSION . $this->name . '/catalog/model/' . $this->type . '/' . $this->name . '.php';
			require_once(class_exists('VQMod') ? \VQMod::modCheck($model_file) : $model_file);
			return new \Opencart\Catalog\Model\Extension\UpsPro\Shipping\UpsPro($this->registry);
		}
	}
	
	//==============================================================================
	// logMessage()
	//==============================================================================
	private function logMessage($message) {
		$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : $this->type . '_';
		if ($this->config->get($prefix . $this->name . '_testing_mode')) {
			$filepath = DIR_LOGS . $this->name . '.messages';
			if (is_file($filepath) && filesize($filepath) > 50000000) {
				file_put_contents($filepath, '');
			}
			$message = print_r($message, true);
			$message = preg_replace('/Array\s+\(/', '(', $message);
			$message = preg_replace('/\n\n/', "\n", $message);
			file_put_contents($filepath, $message . "\n\n", FILE_APPEND|LOCK_EX);
		}
	}
	
	//==============================================================================
	// createShippingLabel()
	//==============================================================================
	public function createShippingLabel() {
		$data = array('autobackup' => false);
		$this->loadSettings($data);
		$settings = $data['saved'];
		
		if (!$this->hasPermission('modify') || !$settings['shipping_labels']) {
			echo 'PermissionError';
			return;
		}
		
		// Check size of the "comment" column in the "order_history" table
		$order_history_table = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "order_history WHERE Field = 'comment'");
		if (strtoupper($order_history_table->row['Type']) == 'TEXT') {
			$this->db->query("ALTER TABLE " . DB_PREFIX . "order_history MODIFY `comment` MEDIUMTEXT NOT NULL");
		}
		
		// Get order info
		$ups_pro = $this->loadCatalogModel();
		$order_id = $this->request->post['order_id'];
		
		$this->load->model('sale/order');
		$order = $this->model_sale_order->getOrder($order_id);
		
		// Check for previous label creation
		$label_created_count = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_history WHERE order_id = " . (int)$order_id . " AND `comment` LIKE '%Created via UPS Pro%'")->num_rows;
		$label_canceled_count = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_history WHERE order_id = " . (int)$order_id . " AND `comment` LIKE '%Canceled via UPS Pro%'")->num_rows;
		
		if ($label_created_count > $label_canceled_count) {
			echo 'Error: A shipping label has already been created for this order.';
			return;
		}
		
		// Get UPS service code
		if (version_compare(VERSION, '4.0.2.0', '<')) {
			$shipping_code = explode('.', $order['shipping_code']);
			$shipping_method = explode(' (', $order['shipping_method']);
		} else {
			$order['shipping_code'] = $order['shipping_method']['code'];
			$shipping_code = explode('.', $order['shipping_method']['code']);
			$shipping_method = explode(' (', $order['shipping_method']['name']);
		}
		
		if ($shipping_code[0] == $this->name) {
			$service_code = str_replace(array($this->name . '_', '-NO-SIGNATURE'), '', $shipping_code[1]);
		} else {
			if (empty($settings['default_service'])) {
				echo 'Error: This order did not use UPS Pro to calculate its shipping.';
				return;
			} else {
				$service_code = $settings['default_service'];
			}
		}
		
		// Get products and options
		if (version_compare(VERSION, '4.0', '<')) {
			$order['products'] = $this->model_sale_order->getOrderProducts($order_id);
		} else {
			$order['products'] = $this->model_sale_order->getProducts($order_id);
		}
		
		foreach ($order['products'] as &$order_product) {
			$product_info = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$order_product['product_id'])->row;
			$order_product = array_merge($product_info, $order_product);
			
			if (version_compare(VERSION, '4.0', '<')) {
				$order_product['option'] = $this->model_sale_order->getOrderOptions($order_id, $order_product['order_product_id']);
			} else {
				$order_product['option'] = $this->model_sale_order->getOptions($order_id, $order_product['order_product_id']);
			}
			
			foreach ($order_product['option'] as &$option) {
				$option_info = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_value_id = " . (int)$option['product_option_value_id'])->row;
				
				if (!empty($option_info['weight'])) {
					$option_weight = ($option_info['weight_prefix'] == '+') ? $option_info['weight'] : -$option_info['weight'];
					$order_product['weight'] += $option_weight;
				}
				
				// factor in custom option dimensions
				if (isset($option_info['length']) && $option_info['length'] > 0) $option['length'] = $option_info['length'];
				if (isset($option_info['width']) && $option_info['width'] > 0) $option['width'] = $option_info['width'];
				if (isset($option_info['height']) && $option_info['height'] > 0) $option['height'] = $option_info['height'];
				if (isset($option_info['size']) && $option_info['size'] > 0) $option['length'] = $option_info['size'];
			}
			
			$order_product['weight'] = $order_product['weight'] * $order_product['quantity'];
		}
		
		// Get package data from front-end
		$address = array(
			'address_1'		=> $order['shipping_address_1'],
			'address_2'		=> $order['shipping_address_2'],
			'city'			=> $order['shipping_city'],
			'postcode'		=> $order['shipping_postcode'],
			'zone_id'		=> $order['shipping_zone_id'],
			'zone_code'		=> $order['shipping_zone_code'],
			'country_id'	=> $order['shipping_country_id'],
			'iso_code_2'	=> $order['shipping_iso_code_2'],
		);
		
		$curl_data = $ups_pro->getQuote($address, $order);
		
		if (empty($curl_data['RateRequest'])) {
			echo 'Error: No package data could be generated for order ' . $order_id;
			return;
		}
		
		// Set additional curl data
		unset($curl_data['RateRequest']['PickupType']);
		unset($curl_data['RateRequest']['Shipment']['ShipFrom']);
		unset($curl_data['RateRequest']['Shipment']['DeliveryTimeInformation']);
		
		$curl_data['ShipmentRequest'] = $curl_data['RateRequest'];
		unset($curl_data['RateRequest']);
		
		$curl_data['ShipmentRequest']['Shipment']['Description'] = 'Order ' . $order_id;
		$curl_data['ShipmentRequest']['Shipment']['Service']['Code'] = $service_code;
		$curl_data['ShipmentRequest']['Shipment']['Service']['Description'] = substr($shipping_method[0], 0, 35);
		
		$curl_data['ShipmentRequest']['Shipment']['Shipper']['Name'] = substr($settings['shipfrom_name'], 0, 35);
		$curl_data['ShipmentRequest']['Shipment']['Shipper']['AttentionName'] = substr($settings['attention_name'], 0, 35);
		$curl_data['ShipmentRequest']['Shipment']['Shipper']['Phone']['Number'] = substr(!empty($settings['phone_number']) ? $settings['phone_number'] : $this->config->get('config_telephone'), 0, 15);
		$curl_data['ShipmentRequest']['Shipment']['Shipper']['TaxIdentificationNumber'] = substr($settings['invoice_tax_id'], 0, 15);
		
		$curl_data['ShipmentRequest']['Shipment']['ShipTo']['Name'] = substr($order['shipping_company'] ? $order['shipping_company'] : $order['shipping_firstname'] . ' ' . $order['shipping_lastname'], 0, 35);
		$curl_data['ShipmentRequest']['Shipment']['ShipTo']['AttentionName'] = substr($order['shipping_firstname'] . ' ' . $order['shipping_lastname'], 0, 35);
		$curl_data['ShipmentRequest']['Shipment']['ShipTo']['Phone']['Number'] = substr($order['telephone'], 0, 15);
		$curl_data['ShipmentRequest']['Shipment']['ShipTo']['EMailAddress'] = substr($order['email'], 0, 50);
		
		$curl_data['ShipmentRequest']['Shipment']['PaymentInformation']['ShipmentCharge'][] = array(
			'Type'			=> '01',
			'BillShipper'	=> array(
				'AccountNumber'	=> $settings['account_number'],
			),
		);
		
		foreach ($curl_data['ShipmentRequest']['Shipment']['Package'] as &$shipment_package) {
			$shipment_package['Packaging'] = $shipment_package['PackagingType'];
			unset($shipment_package['PackagingType']);
		}
		
		if (!empty($settings['signature_choice']) && !strpos($shipping_method[0], $settings['signature_required_text_' . $order['language_code']])) {
			unset($curl_data['ShipmentRequest']['Shipment']['ShipmentServiceOptions']['DeliveryConfirmation']);
			foreach ($curl_data['ShipmentRequest']['Shipment']['Package'] as $key => $package) {
				unset($curl_data['ShipmentRequest']['Shipment']['Package'][$key]['PackageServiceOptions']['DeliveryConfirmation']);
			}
		}
		
		/*
		$curl_data['ShipmentRequest']['LabelSpecification']['LabelImageFormat']['Code'] ='ZPL';
		$curl_data['ShipmentRequest']['LabelSpecification']['LabelStockSize']['Height'] ='6';
		$curl_data['ShipmentRequest']['LabelSpecification']['LabelStockSize']['Width'] ='4';
		*/
		
		/*
		if ($order['shipping_iso_code_2'] == 'NO') {
			$curl_data['ShipmentRequest']['Shipment']['ShipFrom'] = array(
				'Name'		=> substr($settings['shipfrom_name'], 0, 30),
				'Address'	=> $curl_data['ShipmentRequest']['Shipment']['Shipper']['Address'],
			);
			
			$curl_data['ShipmentRequest']['Shipment']['ShipFrom']['VendorInfo'] = array(
				'VendorCollectIDTypeCode'	=> '0357',
				'VendorCollectIDNumber'		=> 'Your VOEC Number',
				'ConsigneeType'				=> '02',
			);
		}
		*/
		
		// Set data to retrieve international documents
		if ($curl_data['ShipmentRequest']['Shipment']['ShipTo']['Address']['CountryCode'] != $settings['country']) {
			$curl_data['ShipmentRequest']['Shipment']['ShipmentServiceOptions']['InternationalForms']['FormType'] = '01';
			$curl_data['ShipmentRequest']['Shipment']['ShipmentServiceOptions']['InternationalForms']['Comments'] = substr($settings['invoice_comments'], 0, 150);
			$curl_data['ShipmentRequest']['Shipment']['ShipmentServiceOptions']['InternationalForms']['Contacts']['SoldTo'] = $curl_data['ShipmentRequest']['Shipment']['ShipTo'];
			$curl_data['ShipmentRequest']['Shipment']['ShipmentServiceOptions']['InternationalForms']['CurrencyCode'] = $order['currency_code'];
			$curl_data['ShipmentRequest']['Shipment']['ShipmentServiceOptions']['InternationalForms']['DeclarationStatement'] = substr($settings['invoice_declaration_statement'], 0, 550);
			$curl_data['ShipmentRequest']['Shipment']['ShipmentServiceOptions']['InternationalForms']['InvoiceNumber'] = $order_id;
			$curl_data['ShipmentRequest']['Shipment']['ShipmentServiceOptions']['InternationalForms']['InvoiceDate'] = date('Ymd');
			$curl_data['ShipmentRequest']['Shipment']['ShipmentServiceOptions']['InternationalForms']['ReasonForExport'] = 'SALE';
			$curl_data['ShipmentRequest']['Shipment']['ShipmentServiceOptions']['InternationalForms']['TermsOfShipment'] = 'DDU';
			
			/*
			if (in_array($order['shipping_iso_code_2'], array('AT','BE','BG','CY','CZ','DE','DK','EE','ES','FI','FR','GR','HR','HU','IE','IT','LT','LU','LV','MT','NL','NO','PL','PT','RO','SE','SI','SK'))) {
				$curl_data['ShipmentRequest']['Shipment']['ShipmentServiceOptions']['InternationalForms']['TermsOfShipment'] = 'DAP';
				$curl_data['ShipmentRequest']['Shipment']['PaymentInformation']['ShipmentCharge'][] = array(
					'Type'			=> '01',
					'BillShipper'	=> array(
						'AccountNumber'	=> $settings['account_number'],
					),
				);
			} else {
				$curl_data['ShipmentRequest']['Shipment']['ShipmentServiceOptions']['InternationalForms']['TermsOfShipment'] = 'DDP';
				$curl_data['ShipmentRequest']['Shipment']['PaymentInformation']['ShipmentCharge'][] = array(
					'Type'			=> '02',
					'BillShipper'	=> array(
						'AccountNumber'	=> $settings['account_number'],
					),
				);
			}
			*/
			
			$curl_data['ShipmentRequest']['Shipment']['ShipmentServiceOptions']['InternationalForms']['Product'] = array();
			
			foreach ($order['products'] as $product) {
				$product_info = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$product['product_id'])->row;
				
				if (!empty($settings['invoice_commodity_code_field']) && strlen($product_info[$settings['invoice_commodity_code_field']]) >= 6) {
					$commodity_code = substr($product_info[$settings['invoice_commodity_code_field']], 0, 15);
				} elseif (!empty($settings['invoice_commodity_code']) && strlen($settings['invoice_commodity_code']) >= 6) {
					$commodity_code = trim($settings['invoice_commodity_code']);
				} else {
					$commodity_code = '';
				}
				
				if (!empty($settings['invoice_country_code_field']) && !empty($product_info[$settings['invoice_country_code_field']])) {
					$origin_country_code = $product_info[$settings['invoice_country_code_field']];
				} else {
					$origin_country_code = $settings['country'];
				}
				
				$curl_data['ShipmentRequest']['Shipment']['ShipmentServiceOptions']['InternationalForms']['Product'][] = array(
					'CommodityCode'		=> $commodity_code,
					'Description' 		=> array(substr($product['name'], 0, 35)),
					'PartNumber'		=> substr($product['model'], 0, 35),
					'OriginCountryCode'	=> $origin_country_code,
					'Unit'				=> array(
						'Number'			=> (string)$product['quantity'],
						'UnitOfMeasurement'	=> array('Code' => 'PCS'),
						'Value'				=> (string)number_format($product['price'] * $order['currency_value'], 2, '.', ''),
					),
				);
			}
		}
		
		/*
		// Check for dangerous goods
		$perfumes = 0;
		$hairsprays = 0;
		$deosprays = 0;
		$total_dangerous_weight = 0;
		$additional_description = array();
		
		foreach ($order['products'] as $product) {
			$product_info = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$product['product_id'])->row;
			
			if (!empty($settings['invoice_commodity_code_field']) && strlen($product_info[$settings['invoice_commodity_code_field']]) >= 6) {
				$commodity_code = substr($product_info[$settings['invoice_commodity_code_field']], 0, 15);
			} else {
				$commodity_code = '';
			}
			
			if ($commodity_code == '3303001000' || $commodity_code == '3303009000') {
				$perfumes += $product['quantity'];
				$total_dangerous_weight += $product_info['weight'] * $product['quantity'];
				$additional_description[] = 'PERFUME';
			}
			
			$hairspray_category_query = $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = " . (int)$product['product_id'] . " AND (category_id = 137 OR category_id = 314)");
			if ($hairspray_category_query->num_rows) {
				$hairsprays += $product['quantity'];
				$total_dangerous_weight += $product_info['weight'] * $product['quantity'];
				$additional_description[] = 'HAIRSPRAY';
			}
			
			$deospray_category_query = $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = " . (int)$product['product_id'] . " AND category_id = 789");
			if ($deospray_category_query->num_rows) {
				$deosprays += $product['quantity'];
				$total_dangerous_weight += $product_info['weight'] * $product['quantity'];
				$additional_description[] = 'DEOSPRAY';
			}
		}
			
		if ($perfumes || $hairsprays || $deosprays) {
			$curl_data['ShipmentRequest']['Shipment']['Description'] = 'ID8000, Consumer Commodity';
			
			foreach ($curl_data['ShipmentRequest']['Shipment']['Package'] as $index => $package) {
				$curl_data['ShipmentRequest']['Shipment']['Package'][$index]['PackageServiceOptions']['HazMat'] = array(array(
					'PackagingTypeQuantity'			=> $perfumes + $hairsprays + $deosprays,
					'HazardLabelRequired'			=> 'LIMITED QUANTITY',
					'ClassDivisionNumber'			=> '9',
					'Quantity'						=> round($total_dangerous_weight, 1),
					'UOM'							=> 'ML',
					'PackagingType'					=> 'FIBERBOARD BOX',
					'IDNumber'						=> 'ID8000',
					'ProperShippingName'			=> 'CONSUMER COMMODITY',
					'AdditionalDescription'			=> implode(' AND ', $additional_description),
					'PackagingInstructionCode'		=> 'Y963',
					'EmergencyPhone'				=> '46700500075',
					'EmergencyContact'				=> 'Simon Jonsson',
					'RegulationSet'					=> 'IATA',
					'TransportationMode'			=> 'PAX',
					'CommodityRegulatedLevelCode'	=> 'LQ',
				));
				
				$hazmat_info = array('OuterPackagingType' => 'FIBERBOARD BOX');
				
				if ($perfumes && $hairsprays || $perfumes && $deosprays || $hairsprays && $deosprays) {
					$hazmat_info['AllPackedInOneIndicator'] = '1';
					$hazmat_info['QValue'] = '1.0';
				}
				
				$curl_data['ShipmentRequest']['Shipment']['Package'][$index]['HazMatPackageInformation'] = $hazmat_info;
			}
		}
		*/
		
		// Send request and return response
		$response = $ups_pro->curlRequest('POST', 'api/shipments/v1/ship', $curl_data);
		
		//$this->logMessage($curl_data);
		//$this->logMessage($response);
		
		if (!empty($response['error'])) {
			echo $response['error'];
			return;
		}
		
		// Get shipment information
		if (!empty($settings['negotiated_rates']) && !empty($response['ShipmentResponse']['ShipmentResults']['NegotiatedRateCharges'])) {
			$shipment_charge = $response['ShipmentResponse']['ShipmentResults']['NegotiatedRateCharges']['TotalCharge']['MonetaryValue'];
		} else {
			$shipment_charge = $response['ShipmentResponse']['ShipmentResults']['ShipmentCharges']['TotalCharges']['MonetaryValue'];
		}
		
		$shipment_charge = $this->currency->format($shipment_charge, $response['ShipmentResponse']['ShipmentResults']['ShipmentCharges']['TotalCharges']['CurrencyCode']);
		$shipment_number = $response['ShipmentResponse']['ShipmentResults']['ShipmentIdentificationNumber'];
		
		$packages = array();
		
		foreach ($curl_data['ShipmentRequest']['Shipment']['Package'] as $package) {
			$package_text = $package['Dimensions']['Length'] . ' x ' . $package['Dimensions']['Width'] . ' x ' . $package['Dimensions']['Height'] . ' ' . strtolower($package['Dimensions']['UnitOfMeasurement']['Code']);
			$package_text .= ' (' . round($package['PackageWeight']['Weight'], 2) . ' ' . strtolower($package['PackageWeight']['UnitOfMeasurement']['Code']) . ')';
			$packages[] = $package_text;
		}
		
		$trackings = array();
		
		if (isset($response['ShipmentResponse']['ShipmentResults']['PackageResults']['TrackingNumber'])) {
			$package_results = array($response['ShipmentResponse']['ShipmentResults']['PackageResults']);
		} else {
			$package_results = $response['ShipmentResponse']['ShipmentResults']['PackageResults'];
		}
		
		foreach ($package_results as $index => $package_result) {
			$trackings[] = array(
				'number'	=> $package_result['TrackingNumber'],
				'url'		=> 'https://wwwapps.ups.com/WebTracking/processInputRequest?requester=ST/trackdetails&InquiryNumber1=' . $package_result['TrackingNumber'],
				'image'		=> $package_result['ShippingLabel']['GraphicImage'],
			);
		}
		
		// Add order history note(s)
		if (!empty($settings['shipping_order_status'])) {
			$order_status_id = $settings['shipping_order_status'];
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = " . (int)$order_status_id . " WHERE order_id = " . (int)$order_id);
		} else {
			$order_status_id = $order['order_status_id'];
		}
		
		$b = '<b style="display: inline-block; line-height: 2; width: 150px;">';
		$cancel_and_send_buttons = '<a class="btn btn-danger" style="margin-top: 5px" onclick="cancelUpsShipment($(this), \'' . $shipment_number . '\')">Cancel Shipment</a> <a class="btn btn-success" style="margin-top: 5px" onclick="sendUpsCustomerEmail($(this))">Send Customer E-mail</a>';
		$comment = '<script>function viewShippingLabel(imageData) { newWindow = window.open(); newWindow.document.write(\'<img src="data:image/gif;base64,\' + imageData + \'" />\'); }</script>';
		$comment .= '<script>function viewShippingInvoice(pdfData) { newWindow = window.open(); newWindow.document.write(\'<embed type="application/pdf" width="100%" height="100%" src="data:application/pdf;base64,\' + pdfData + \'" />\'); }</script>';
		
		if (count($packages) > 1) {
			$comment .= '<b style="line-height: 2">' . count($packages) . ' Shipping Labels Created via UPS Pro</b><br>';
		} else {
			$comment .= '<b style="line-height: 2">Shipping Label Created via UPS Pro</b><br>';
		}
		
		$comment .= $b . 'Customer E-mail Sent:</b>' . ($settings['customer_email'] ? 'Yes' : 'No') . '<br>';
		$comment .= $b . 'Shipment Charge:</b>' . $shipment_charge . '<br>';
		$comment .= $b . 'Shipment Number:</b>' . $shipment_number . '<br>';
		
		if (!empty($response['ShipmentResponse']['ShipmentResults']['Form']['Image']['GraphicImage'])) {
			$comment .= $b . 'UPS Forms:</b><a style="cursor: pointer" class="shipping-invoice" onclick="viewShippingInvoice(\'' . $response['ShipmentResponse']['ShipmentResults']['Form']['Image']['GraphicImage'] . '\')">View Invoice & Customs Forms</a><br>';
		}
		
		$full_comment = $comment;
		
		if (count($packages) > 1) {
			$comment .= $cancel_and_send_buttons;
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = " . (int)$order_id . ", order_status_id = " . (int)$order_status_id . ", notify = 0, comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
		}
		
		foreach ($packages as $index => $package) {
			if (count($packages) > 1) {
				$comment = '';
				$comment .= $b . 'Package ' . ($index + 1) . ' of ' . count($packages) . ':</b>' . $package . '<br>';
			} else {
				$comment .= $b . 'Package:</b>' . $package . '<br>';
			}
			
			$comment .= $b . 'Tracking Number:</b><a target="_blank" alt="' . $trackings[$index]['url'] . '" href="' . $trackings[$index]['url'] . '">' . $trackings[$index]['number'] . '</a><br>';
			$comment .= '<a class="btn btn-primary" style="margin-top: 5px" onclick="viewShippingLabel(\'' . $trackings[$index]['image'] . '\')">View Shipping Label</a> ';
			
			$full_comment .= '<hr>' . $comment;
			
			if (count($packages) == 1) {
				$comment .= $cancel_and_send_buttons;
			}
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = " . (int)$order_id . ", order_status_id = " . (int)$order_status_id . ", notify = 0, comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
		}
		
		// Send out e-mails
		if ($settings['customer_email']) {
			$this->sendCustomerEmail(false);
		}
		
		if ($settings['copy_emails']) {
			$copy_emails = explode(',', $settings['copy_emails']);
			
			foreach ($copy_emails as $copy_email) {
				$this->sendEmail(trim($copy_email), 'UPS Shipping Label Generated for Order #' . $order_id, $full_comment);
			}
		}
		
		// Return data
		if (count($packages) == 1) {
			echo $comment;
		} else {
			echo '<b>Multiple Shipping Labels Created</b><br><br>See the order history for details.';
		}
		
		if (!empty($settings['shipping_order_status'])) {
			echo "
				<script>
					$('select[name=order_status_id]').val('" . $settings['shipping_order_status'] . "');
					$('input[name=notify]').attr('checked', false);
					$('textarea[name=comment]').val('Updating reward points and other front-end processes');
					$('#button-history').click();
				</script>
			";
		}
	}
	
	//==============================================================================
	// sendCustomerEmail()
	//==============================================================================
	public function sendCustomerEmail($add_order_history_note = true) {
		$data = array('autobackup' => false);
		$this->loadSettings($data);
		$settings = $data['saved'];
		
		$order_id = $this->request->post['order_id'];
		
		$this->load->model('sale/order');
		$order = $this->model_sale_order->getOrder($order_id);
		
		// Get product data
		if (version_compare(VERSION, '4.0', '<')) {
			$order['products'] = $this->model_sale_order->getOrderProducts($order_id);
		} else {
			$order['products'] = $this->model_sale_order->getProducts($order_id);
		}
		
		foreach ($order['products'] as &$order_product) {
			$product_info = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$order_product['product_id'])->row;
			$order_product = array_merge($product_info, $order_product);
			
			if (version_compare(VERSION, '4.0', '<')) {
				$order_product['option'] = $this->model_sale_order->getOrderOptions($order_id, $order_product['order_product_id']);
			} else {
				$order_product['option'] = $this->model_sale_order->getOptions($order_id, $order_product['order_product_id']);
			}
		}
		
		// Get tracking numbers
		$tracking_numbers = array();
		$order_history_notes = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_history WHERE order_id = " . (int)$order_id . " AND `comment` LIKE '%InquiryNumber1=%' AND `comment` NOT LIKE '%display: none%'")->rows;
		
		foreach ($order_history_notes as $order_history_note) {
			$explode1 = explode('InquiryNumber1=', $order_history_note['comment']);
			$explode2 = explode('"', $explode1[1]);
			$tracking_numbers[] = $explode2[0];
		}
		
		// Set up shortcodes
		$tracking_url = 'https://wwwapps.ups.com/WebTracking/processInputRequest?requester=ST/trackdetails';
		
		foreach ($tracking_numbers as $index => $tracking_number) {
			$tracking_url .= '&InquiryNumber' . ($index + 1) . '=' . $tracking_number;
		}
		
		$replace = array('[tracking_numbers]', '[tracking_url]');
		$with = array(implode(', ', $tracking_numbers), $tracking_url);
		
		foreach ($order as $key => $value) {
			if ($key == 'products') {
				continue;
			}
			if (is_array($value)) {
				$value = json_encode($value);
			}
			$replace[] = '[' . $key . ']';
			$with[] = $value;
		}
		
		$product_names = array();
		
		foreach ($order['products'] as $product) {
			$options = array();
			foreach ($product['option'] as $option) {
				$options[] = $option['name'] . ': ' . $option['value'];
			}
			$product_name = $product['name'] . ($options ? ' (' . implode(', ', $options) . ')' : '');
			$product_names[] = $product['quantity'] . ' x ' . html_entity_decode($product_name, ENT_QUOTES, 'UTF-8');
		}
		
		$replace[] = '[products]';
		$with[] = implode('<br>', $product_names);
		
		// Send e-mail
		$subject = html_entity_decode($settings['customer_subject_' . $order['language_code']], ENT_QUOTES, 'UTF-8');
		$subject = str_replace($replace, $with, $subject);
		
		$message = html_entity_decode($settings['customer_message_' . $order['language_code']], ENT_QUOTES, 'UTF-8');
		$message = str_replace($replace, $with, $message);
		
		$this->sendEmail($order['email'], $subject, $message);
		
		// Add order history note
		if ($add_order_history_note) {
			$comment = '<b>UPS Pro Customer E-mail Sent</b>';
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = " . (int)$order_id . ", order_status_id = " . (int)$order['order_status_id'] . ", notify = 0, comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
		}
	}
		
	//==============================================================================
	// cancelShipment()
	//==============================================================================
	public function cancelShipment() {
		$data = array('autobackup' => false);
		$this->loadSettings($data);
		$settings = $data['saved'];
		
		if (!$this->hasPermission('modify') || !$settings['shipping_labels']) {
			echo 'PermissionError';
			return;
		}
		
		// UPS only allows a few specific test shipment numbers to be voided
		if ($settings['mode'] == 'test') {
			$this->request->post['shipment_number'] = '1ZISDE016691676846';
		}
		
		// Get order info
		$order_id = $this->request->post['order_id'];
		
		$this->load->model('sale/order');
		$order = $this->model_sale_order->getOrder($order_id);
		
		// Send request
		$ups_pro = $this->loadCatalogModel();
		
		$response = $ups_pro->curlRequest('DELETE', 'api/shipments/v1/void/cancel/' . $this->request->post['shipment_number']);
		
		if (!empty($response['error'])) {
			echo $response['error'];
			return;
		}
		
		// Add order history note
		$comment = '<b style="line-height: 2">Shipment ' . $this->request->post['shipment_number'] . ' Canceled via UPS Pro</b>';
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = " . (int)$order_id . ", order_status_id = " . (int)$order['order_status_id'] . ", notify = 0, comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
		$this->db->query("UPDATE " . DB_PREFIX . "order_history SET comment = REPLACE(comment, 'margin-top: 5px\" onclick=\"', 'display: none\" onclick=\"') WHERE order_id = " . (int)$order_id);
	}
	
	//==============================================================================
	// sendEmail()
	//==============================================================================
	private function sendEmail($to, $subject, $message) {
		$mail_options = array(
			'parameter'		=> $this->config->get('config_mail_parameter'),
			'smtp_hostname'	=> $this->config->get('config_mail_smtp_hostname'),
			'smtp_username' => $this->config->get('config_mail_smtp_username'),
			'smtp_password' => html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8'),
			'smtp_port'		=> $this->config->get('config_mail_smtp_port'),
			'smtp_timeout'	=> $this->config->get('config_mail_smtp_timeout'),
		);
		
		if (version_compare(VERSION, '2.0.2', '<')) {
			$mail = new Mail($this->config->get('config_mail'));
		} elseif (version_compare(VERSION, '4.0.2.0', '<')) {
			if (version_compare(VERSION, '3.0', '<')) {
				$mail = new Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
			} elseif (version_compare(VERSION, '4.0', '<')) {
				$mail = new Mail($this->config->get('config_mail_engine'));
			} else {
				$mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'));
			}
			
			foreach ($mail_options as $key => $value) {
				$mail->{$key} = $value;
			}
		} else {
			$mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'), $mail_options);
		}
		
		$mail->setSubject($subject);
		$mail->setHtml($message);
		$mail->setText(strip_tags(str_replace('<br>', "\n", $message)));
		$mail->setSender(str_replace(array(',', '&'), array('', 'and'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')));
		$mail->setFrom($this->config->get('config_email'));
		$mail->setTo($to);
		$mail->send();
	}
	
	//==============================================================================
	// Event hooks
	//==============================================================================
	public function addButtonEvent(&$route = '', &$input = array()) {
		$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : $this->type . '_';
		if (!$this->hasPermission('modify') || !$this->config->get($prefix . $this->name . '_shipping_labels')) {
			return;
		}
		
		$data = $this->loadLanguage($this->type . '/' . $this->name);
		$script_html = '<script id="ups-pro-script" src="../extension/' . $this->name . '/admin/view/javascript/' . $this->name . '.js"></script>';
		
		// order list page
		if (!empty($input['orders']) && $this->config->get($prefix . $this->name . '_shipping_labels') == 'both_pages') {
			$input['results'] .= $script_html;
			
			foreach ($input['orders'] as &$order) {
				$order['view'] = 'javascript:void(0)" class="btn btn-warning" data-bs-toggle="tooltip" title="' . $data['button_create_ups_shipping_label'] . '" onclick="createUpsLabel($(this), ' . $order['order_id'] . ')" ><i class="fa fa-truck"></i></a> <a href="' . $order['view'];
			}
		}
		
		// order info page
		if (!empty($input['invoice'])) {
			$input['invoice'] = 'javascript:void(0)" class="btn btn-warning" data-bs-toggle="tooltip" title="' . $data['button_create_ups_shipping_label'] . '" onclick="createUpsLabel($(this))"><i class="fa fa-truck"></i></a> ' . $script_html . ' <a href="' . $input['invoice'];
		}
	}
	
	public function addButton() {
		$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : $this->type . '_';
		if (!$this->hasPermission('modify') || !$this->config->get($prefix . $this->name . '_shipping_labels')) {
			return '';
		}
		
		$data = $this->loadLanguage($this->type . '/' . $this->name);
		
		if (version_compare(VERSION, '2.0', '<')) {
			echo '<a class="button" onclick="createUpsLabel($(this))">' . $data['button_create_ups_shipping_label'] . '</a>';
		} elseif (version_compare(VERSION, '4.0', '<')) {
			echo ' <a class="btn btn-warning" data-toggle="tooltip" title="' . $data['button_create_ups_shipping_label'] . '" onclick="createUpsLabel($(this))"><i class="fa fa-truck"></i></a> ';
		}
	}
}
?>