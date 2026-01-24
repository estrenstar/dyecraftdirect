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

//namespace Opencart\Catalog\Model\Extension\UpsPro\Shipping;
//class UpsPro extends \Opencart\System\Engine\Model {

class ModelExtensionShippingUpsPro extends Model {
	
	private $type = 'shipping';
	private $name = 'ups_pro';
	private $testing_mode;
	
	//==============================================================================
	// getQuote()
	//==============================================================================
	public function getQuote($address, $order_info = array()) {
		if (empty($address['postcode']) || empty($address['iso_code_2'])) return;
		
		$settings = $this->getSettings();
		
		if (empty($order_info)) {
			$this->logMessage("\n" . '------------------------------ Starting Test ' . date('Y-m-d G:i:s') . ' ------------------------------');
		}
		
		// Set variables
		$currency = (!empty($order_info['currency_code'])) ? $order_info['currency_code'] : $this->session->data['currency'];
		$main_currency = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `key` = 'config_currency' AND store_id = 0 ORDER BY setting_id DESC LIMIT 1")->row['value'];
		
		$customer_group_id = (!empty($order_info['customer_group_id'])) ? $order_info['customer_group_id'] : $this->customer->getGroupId();
		$language = (!empty($this->session->data['language'])) ? $this->session->data['language'] : $this->config->get('config_language');
		$store_id = (!empty($order_info['store_id'])) ? $order_info['store_id'] : $this->config->get('config_store_id');
		
		// Set geo zones
		$geo_zones = array();
		$geo_zones_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE country_id = " . (int)$address['country_id'] . " AND (zone_id = 0 OR zone_id = " . (int)$address['zone_id'] . ")");
		foreach ($geo_zones_query->rows as $geo_zone) $geo_zones[] = $geo_zone['geo_zone_id'];
		if (empty($geo_zones)) $geo_zones = array(0);
		
		$default_country_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE country_id = " . (int)$this->config->get('config_country_id'));
		if ($default_country_query->num_rows) {
			$request_type = ($address['iso_code_2'] == $default_country_query->row['iso_code_2']) ? 'domestic' : 'international';
		} else {
			$request_type = 'domestic';
		}
		
		// Override country codes for certain countries
		if ($address['iso_code_2'] == 'XK') {
			$address['iso_code_2'] = 'KV';
		}
		
		// Check restrictions
		if (empty($settings['status']) && empty($order_info)) {
			$this->logMessage('Error: the extension is disabled');
			return;
		}
		
		if (!isset($settings['stores']) || !array_intersect(array((int)$store_id), explode(';', $settings['stores']))) {
			$this->logMessage('Error: store with id ' . (int)$store_id . ' is not eligible based on the Restrictions set up');
			return;
		}
		
		if (!isset($settings['geo_zones']) || !array_intersect($geo_zones, explode(';', $settings['geo_zones']))) {
			$this->logMessage('Error: geo zones ' . implode(', ', $geo_zones) . ' are not eligible based on the Restrictions set up');
			return;
		}
		
		if (!isset($settings['customer_groups']) || !array_intersect(array((int)$customer_group_id), explode(';', $settings['customer_groups']))) {
			$this->logMessage('Error customer group with id ' . (int)$customer_group_id . ' is not eligible based on the Restrictions set up');
			return;
		}
		
		// Check weight and length classes
		$weight_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class_description WHERE unit = '" . $this->db->escape($settings['weight_units']) . "' OR unit = '" . $this->db->escape($settings['weight_units']) . "s'");
		if (!$weight_query->num_rows) {
			$this->logMessage('Error: you do not have a weight class with units of "' . $settings['weight_units'] . '". Add a weight class with the appropriate units, then try again.');
			return;
		}
		$weight_class_id = $weight_query->row['weight_class_id'];
		
		$length_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "length_class_description WHERE unit = '" . $this->db->escape($settings['dimension_units']) . "' OR unit = '" . $this->db->escape($settings['dimension_units']) . "s'");
		if (!$length_query->num_rows) {
			$this->logMessage('Error: you do not have a length class with units of "' . $settings['dimension_units'] . '". Add a length class with the appropriate units, then try again.');
			return;
		}
		$length_class_id = $length_query->row['length_class_id'];
		
		// Set up cart data
		$upper_weight_limit = (!empty($settings['weight_limit'])) ? max(1, $settings['weight_limit']) : 150;
		
		$cart_criteria = array(
			'length',
			'width',
			'height',
			'total',
			'weight',
		);
		foreach ($cart_criteria as $spec) {
			${$spec.'s'} = array();
		}
		
		$longest_length = 0;
		$longest_width = 0;
		$longest_height = 0;
		
		$products = (!empty($order_info['products'])) ? $order_info['products'] : $this->cart->getProducts();
		
		foreach ($products as $product) {
			$product['key'] = $product['product_id'] . '-' . md5(json_encode($product));
			
			if (!$product['shipping']) {
				$this->logMessage($product['name'] . ' (product_id: ' . $product['product_id'] . ') does not require shipping and was ignored' . "\n");
				continue;
			}
			
			if ($settings['divide_heavy_products']) {
				$product_weight = $this->weight->convert($product['weight'], $product['weight_class_id'], $weight_class_id);
				if ($product_weight > $upper_weight_limit) {
					$product['quantity'] *= ceil($product_weight / $upper_weight_limit);
				}
			}
			
			for ($i = 0; $i < $product['quantity']; $i++) {
				$product_key = $product['key'] . '-' . $i;
				
				// compatibility with custom option dimension extensions
				foreach ($product['option'] as $option) {
					if (isset($option['length']) && $option['length'] > 0) $product['length'] = $option['length'];
					if (isset($option['width']) && $option['width'] > 0) $product['width'] = $option['width'];
					if (isset($option['height']) && $option['height'] > 0) $product['height'] = $option['height'];
					
					$option_info = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_value_id = " . (int)$option['product_option_value_id'])->row;
					if (isset($option_info['size']) && $option_info['size'] > 0) $product['length'] = $option_info['size'];
				}
				
				// dimensions
				$length_class_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "length_class WHERE length_class_id = " . (int)$product['length_class_id']);
				if ($length_class_query->num_rows) {
					$dimensions = array(
						$this->length->convert($product['length'], $product['length_class_id'], $length_class_id),
						$this->length->convert($product['width'], $product['length_class_id'], $length_class_id),
						$this->length->convert($product['height'], $product['length_class_id'], $length_class_id),
					);
					rsort($dimensions);
					$lengths[$product_key] = ($dimensions[0] > 0) ? $dimensions[0] : 1;
					$widths[$product_key] = ($dimensions[1] > 0) ? $dimensions[1] : 1;
					$heights[$product_key] = ($dimensions[2] > 0) ? $dimensions[2] : 1;
				} else {
					if ($i == 0) {
						$this->logMessage($product['name'] . ' (product_id: ' . $product['product_id'] . ') does not have a valid length class, which causes a "Division by zero" error, and means it cannot be used for dimension/volume calculations. You can fix this by re-saving the product data.' . "\n");
					}
					$lengths[$product_key] = 1;
					$widths[$product_key] = 1;
					$heights[$product_key] = 1;
				}
				
				if ($lengths[$product_key] > $longest_length) $longest_length = $lengths[$product_key];
				if ($widths[$product_key] > $longest_width) $longest_width = $widths[$product_key];
				if ($heights[$product_key] > $longest_height) $longest_height = $heights[$product_key];
				
				// total
				$totals[$product_key] = $product['price'];
				
				// weight
				$weight_class_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class WHERE weight_class_id = " . (int)$product['weight_class_id']);
				if ($weight_class_query->num_rows) {
					$weights[$product_key] = $this->weight->convert($product['weight'] / $product['quantity'], $product['weight_class_id'], $weight_class_id);
					if ($weights[$product_key] <= 0) {
						$weights[$product_key] = 0.01;
					}
				} else {
					if ($i == 0) {
						$this->logMessage($product['name'] . ' (product_id: ' . $product['product_id'] . ') does not have a valid weight class, which causes a "Division by zero" error, and means it cannot be used for weight calculations. You can fix this by re-saving the product data.' . "\n");
					}
					$weights[$product_key] = 0.01;
				}
			}
		}
		
		// Check if any item is too heavy for the box weight limit
		$original_upper_weight_limit = $upper_weight_limit;
		
		foreach ($weights as $product_key => $product_weight) {
			if ($product_weight > $upper_weight_limit) {
				$this->logMessage('No rates were retrieved because a product weighing ' . $product_weight . ' was more than the box weight limit of ' . $upper_weight_limit);
				return;
			}
		}
		
		// Load packing algorithm
		if (version_compare(VERSION, '2.1', '<')) {
			$this->load->library('laff');
		} elseif (defined('DIR_EXTENSION') && !class_exists('LAFF')) {
			require_once(DIR_EXTENSION . $this->name . '/system/library/laff.php');
		}
		
		// If using set box dimensions, find the smallest eligible box size
		$item_dimension_adjustment = (float)$settings[$request_type . '_item_dimension'];
		
		$box_length = 0;
		$box_width = 0;
		$box_height = 0;
		$number_of_boxes = 999;
		
		$all_items = array();
		foreach ($weights as $key => $item) {
			$all_items[] = array(
				'length'	=> max(0.01, round($lengths[$key] + $item_dimension_adjustment, 1)),
				'width'		=> max(0.01, round($widths[$key] + $item_dimension_adjustment, 1)),
				'height'	=> max(0.01, round($heights[$key] + $item_dimension_adjustment, 1)),
			);
		}
		
		foreach (explode("\n", $settings[$request_type . '_set_box_dimensions']) as $set_box) {
			if (empty($set_box)) {
				continue;
			}
			
			$dimensions_and_weight = explode(',', str_replace(' ', '', strtolower($set_box)));
			$dimensions = explode('x', $dimensions_and_weight[0]);
			$set_weight = (isset($dimensions_and_weight[1])) ? (float)$dimensions_and_weight[1] : $original_upper_weight_limit;
			
			$set_box_length = (float)$dimensions[0];
			$set_box_width = (float)$dimensions[1];
			$set_box_height = (float)$dimensions[2];
			
			// Calculate how many boxes would be generated
			$laff = new \LAFF();
			
			$laff->pack($all_items, array('length' => $set_box_length, 'width' => $set_box_width, 'height' => $set_box_height));
			$container = $laff->get_container_dimensions();
			
			$set_box_count = 1;
			if ($container['height'] > $set_box_height) {
				$set_box_count = ceil($container['height'] / $set_box_height);
			}
			
			$set_box_count = max($set_box_count, ceil(array_sum($weights) / $set_weight));
			
			// Choose box size with the lowest number of boxes
			if ($longest_length <= $set_box_length && $longest_width <= $set_box_width && $longest_height <= $set_box_height && $set_box_count < $number_of_boxes) {
				$box_length = $set_box_length;
				$box_width = $set_box_width ;
				$box_height = $set_box_height;
				$upper_weight_limit = $set_weight;
				$number_of_boxes = $set_box_count;
			}
		}
		
		if ($box_length) {
			$this->logMessage('Using set box of size ' . $box_length . ' x ' . $box_width . ' x ' . $box_height . "\n");
		}
		
		// Set up adjustments
		$item_weight_adjustment = $settings[$request_type . '_item_weight'];
		foreach ($weights as &$weight) {
			$weight_adjustment = (strpos($item_weight_adjustment, '%')) ? $weight * (float)$item_weight_adjustment / 100 : (float)$item_weight_adjustment;
			$weight_adjustment = round($weight_adjustment, 3);
			
			if ($weight_adjustment) {
				if (strpos($item_weight_adjustment, '%')) {
					$this->logMessage('Adjusting item weight by ' . $weight_adjustment . "\n");
				}
				$weight += $weight_adjustment;
			}
		}
		
		if ($item_weight_adjustment && !strpos($item_weight_adjustment, '%')) {
			$this->logMessage('Adjusting all item weights by ' . $item_weight_adjustment . "\n");
		}
		
		if (strpos($settings[$request_type . '_box_weight'], '%')) {
			$box_weight_adjustment = 0;
			$upper_weight_limit -= $upper_weight_limit * (float)$settings[$request_type . '_box_weight'] / 100;
			$this->logMessage('Adjusting all box weights by ' . $settings[$request_type . '_box_weight'] . "\n");
		} else {
			$box_weight_adjustment = (float)$settings[$request_type . '_box_weight'];
			if ($box_weight_adjustment) {
				$this->logMessage('Adjusting all box weights by ' . $box_weight_adjustment . "\n");
			}
		}
		
		// Pack boxes
		arsort($weights);
		if (empty($weights)) return;
		
		$boxes = array();
		
		if ($settings['packaging_method'] == 'individual') {
			foreach ($weights as $key => $individual_weight) {
				$boxes[] = array(
					'length'	=> max(0.01, round($lengths[$key] + $item_dimension_adjustment, 1)),
					'width'		=> max(0.01, round($widths[$key] + $item_dimension_adjustment, 1)),
					'height'	=> max(0.01, round($heights[$key] + $item_dimension_adjustment, 1)),
					'total'		=> $totals[$key],
					'weight'	=> max(0.01, round($individual_weight + $box_weight_adjustment, 1)),
				);
			}
		} else {
			$weights['end'] = 0;
			
			$box_total = 0;
			$box_weight = $box_weight_adjustment;
			$items = array();
			
			foreach ($weights as $key => $individual_weight) {
				if ($box_weight > $box_weight_adjustment && (($box_weight + $individual_weight) > $upper_weight_limit || $key == 'end')) {
					// Calculate box dimensions using algorithm
					$laff = new \LAFF();
					
					if ($box_length) {
						$laff->pack($items, array('length' => $box_length, 'width' => $box_width, 'height' => $box_height));
						$container = $laff->get_container_dimensions();
					} else {
						$laff->pack($items);
						$container = $laff->get_container_dimensions();
						
						// Fix algorithm generating boxes that are super long
						if ($settings['avoid_long_boxes']) {
							$container_volume = $container['length'] * $container['width'] * $container['height'];
							$lowest_volume = 999999;
							
							foreach (array(1, 1.5, 2) as $factor) {
								$cubic_root = $factor * pow($container_volume, 1/3);
								
								$laff = new \LAFF();
								$laff->pack($items, array('length' => max($longest_length, $cubic_root), 'width' => max($longest_width, $cubic_root), 'height' => max($longest_height, $cubic_root)));
								
								$new_container = $laff->get_container_dimensions();
								$new_container['height'] = max($new_container['height'], $longest_height);
								$new_container_volume = $new_container['length'] * $new_container['width'] * $new_container['height'];
								
								if ($new_container_volume < $lowest_volume) {
									$container = $new_container;
									$lowest_volume = $new_container_volume;
								}
							}
						}
					}
					
					// Create additional boxes if generated box height is more than the set box height
					$box_count = 1;
					
					if ($box_height) {
						if ($container['height'] > $box_height) {
							$box_count = ceil($container['height'] / $box_height);
							$box_weight /= $box_count;
						}
						$container['height'] = $box_height;
					}
					
					// Re-order dimensions
					if ($container['width'] > $container['length']) {
						$length = $container['length'];
						$container['length'] = $container['width'];
						$container['width'] = $length;
					}
					
					if ($container['height'] > $container['length']) {
						$length = $container['length'];
						$container['length'] = $container['height'];
						$container['height'] = $container['width'];
						$container['width'] = $length;
					}
					
					// Reduce box size if oversized
					$dimension_factor = ($settings['dimension_units'] == 'cm') ? 2.54 : 1;
					$length_limit = ($settings['avoid_oversize_fees']) ? 96 * $dimension_factor : 108 * $dimension_factor;
					$girth_limit = ($settings['avoid_oversize_fees']) ? 130 * $dimension_factor: 165 * $dimension_factor;
					
					foreach (array('length', 'width', 'height') as $dimension) {
						$girth = $container['length'] + 2 * $container['width'] + 2 * $container['height'];
						
						if ($container['length'] > $length_limit || $girth > $girth_limit) {
							$box_count++;
							$container[$dimension] /= 2;
							$box_weight /= 2;
						}
					}
					
					// Re-order dimensions again
					if ($container['width'] > $container['length']) {
						$length = $container['length'];
						$container['length'] = $container['width'];
						$container['width'] = $length;
					}
					
					if ($container['height'] > $container['length']) {
						$length = $container['length'];
						$container['length'] = $container['height'];
						$container['height'] = $container['width'];
						$container['width'] = $length;
					}
					
					// Add box to boxes array
					for ($i = 0; $i < $box_count; $i++) {
						$boxes[] = array(
							'length'	=> $container['length'],
							'width'		=> $container['width'],
							'height'	=> $container['height'],
							'total'		=> $box_total,
							'weight'	=> $box_weight,
						);
					}
					
					if ($key == 'end') break;
					
					$box_total = 0;
					$box_weight = $box_weight_adjustment;
					$items = array();
				}
				
				if ($key == 'end') break;
				
				$box_total += $totals[$key];
				$box_weight += $individual_weight;
				$items[] = array(
					'length'	=> max(0.01, round($lengths[$key] + $item_dimension_adjustment, 1)),
					'width'		=> max(0.01, round($widths[$key] + $item_dimension_adjustment, 1)),
					'height'	=> max(0.01, round($heights[$key] + $item_dimension_adjustment, 1)),
				);
			}
		}
		
		if (empty($boxes)) {
			$this->logMessage('Error: no boxes were calculated');
			return;
		}
		
		// Adjust box weights by percentage
		if (strpos($settings[$request_type . '_box_weight'], '%')) {
			foreach ($boxes as &$box) {
				$box['weight'] *= 1 + (float)$settings[$request_type . '_box_weight'] / 100;
			}
		}
		
		// Determine whether address is residential or commercial
		if (empty($address['address_1']))	$address['address_1'] = '';
		if (empty($address['city']))		$address['city'] = '';
		if (empty($address['zone_code']))	$address['zone_code'] = '';
		if (empty($address['postcode']))	$address['postcode'] = '';
		if (empty($address['iso_code_2']))	$address['iso_code_2'] = '';
		
		$curl_data = array(
			'XAVRequest'	=> array(
				'AddressKeyFormat'	=> array(
					'AddressLine'			=> substr($address['address_1'], 0, 100),
					'PoliticalDivision2'	=> substr($address['city'], 0, 30),
					'PoliticalDivision1'	=> substr($address['zone_code'], 0, 30),
					'PostcodePrimaryLow'	=> substr($address['postcode'], 0, 10),
					'CountryCode'			=> $address['iso_code_2'],
				),
			),
		);
		
		$address_validation = $this->curlRequest('POST', 'api/addressvalidation/v1/2', $curl_data);
		
		// Set up curl data
		$address_lines = array(substr($address['address_1'], 0, 35));
		
		if (!empty($address['address_2'])) {
			$address_lines[] = substr($address['address_2'], 0, 35);
		}
		
		$address['postcode'] = str_replace(array(' ', '-'), '', $address['postcode']);
		$settings['postcode'] = str_replace(array(' ', '-'), '', $settings['postcode']);
		
		$curl_data = array(
			'RateRequest' => array(
				'CustomerClassification'	=> array(
					'Code'	=> $settings['customer_classification'],
				),
				'Shipment'	=> array(
					'Shipper'	=> array(
						'ShipperNumber'	=> $settings['account_number'],
						'Address'		=> array(
							'AddressLine'		=> substr($settings['address'], 0, 35),
							'City'				=> substr($settings['city'], 0, 30),
							'StateProvinceCode'	=> substr($settings['state'], 0, 5),
							'PostalCode'		=> substr($settings['postcode'], 0, 9),
							'CountryCode'		=> $settings['country'],
						),
					),
					'ShipFrom'	=> array(
						'Address'	=> array(
							'AddressLine'		=> substr($settings['address'], 0, 35),
							'City'				=> substr($settings['city'], 0, 30),
							'StateProvinceCode'	=> substr($settings['state'], 0, 5),
							'PostalCode'		=> substr($settings['postcode'], 0, 9),
							'CountryCode'		=> $settings['country'],
						),
					),
					'ShipTo'	=> array(
						'Address'	=> array(
							'AddressLine'		=> $address_lines,
							'City'				=> substr($address['city'], 0, 30),
							'StateProvinceCode'	=> substr($address['zone_code'], 0, 5),
							'PostalCode'		=> substr($address['postcode'], 0, 9),
							'CountryCode'		=> $address['iso_code_2'],
							'ResidentialAddressIndicator'	=> '',
						),
					),
					'InvoiceLineTotal'		=> array(
						'CurrencyCode'	=> $currency,
						'MonetaryValue'	=> (string)$this->currency->convert(array_sum($totals), $main_currency, $currency),
					),
					'ShipmentTotalWeight'	=> array(
						'UnitOfMeasurement'	=> array(
							'Code'	=> ($settings['weight_units'] == 'lb') ? 'LBS' : 'KGS',
						),
						'Weight'	=> (array_sum($weights) > 0.01) ? (string)round(array_sum($weights), 2) : '0.01',
					),
				),
			),
		);
		
		if ($settings['pickup_type'] != 'none') {
			$curl_data['RateRequest']['PickupType']['Code'] = $settings['pickup_type'];
		}
		
		if (!empty($address_validation['XAVResponse']['AddressClassification']['Description']) && $address_validation['XAVResponse']['AddressClassification']['Description'] == 'Commercial') {
			unset($curl_data['RateRequest']['Shipment']['ShipTo']['Address']['ResidentialAddressIndicator']);
		}
		
		if (!empty($settings['negotiated_rates'])) {
			$curl_data['RateRequest']['Shipment']['ShipmentRatingOptions']['NegotiatedRatesIndicator'] = '1';
		}
		
		// Add packages to curl data
		foreach ($boxes as $box) {
			$package = array(
				'PackagingType'	=> array(
					'Code'	=> '02',
				),
				'Dimensions'	=> array(
					'UnitOfMeasurement'	=> array(
						'Code'	=> ($settings['dimension_units'] == 'in') ? 'IN' : 'CM',
					),
					'Length'	=> (string)round($box['length'], 1),
					'Width'		=> (string)round($box['width'], 1),
					'Height'	=> (string)round($box['height'], 1),
				),
				'PackageWeight'	=> array(
					'UnitOfMeasurement'	=> array(
						'Code'	=> ($settings['weight_units'] == 'lb') ? 'LBS' : 'KGS',
					),
					'Weight'	=> ($box['weight'] > 0.01) ? (string)round($box['weight'], 2) : '0.01',
				),
				/*
				'SimpleRate'	=> array(
					'Code'	=> XS, S, M, L, or XL
				),
				*/
			);
			
			if (!empty($settings['signature_type']) && $request_type == 'domestic') {
				$package['PackageServiceOptions']['DeliveryConfirmation']['DCISType'] = (string)($settings['signature_type'] + 1);
			}
			
			if ($settings['insurance']) {
				$package['PackageServiceOptions'] = array(
					'DeclaredValue'	=> array(
						'CurrencyCode'	=> $currency,
						'MonetaryValue'	=> (string)$this->currency->convert($box['total'], $main_currency, $currency),
					),
				);
			}
			
			$curl_data['RateRequest']['Shipment']['Package'][] = $package;
		}
		
		if (!empty($settings['signature_type']) && $request_type == 'international' && $address['iso_code_2']) {
			$curl_data['RateRequest']['Shipment']['ShipmentServiceOptions']['DeliveryConfirmation']['DCISType'] = (string)$settings['signature_type'];
		}
		
		/*
		if (false) {
			$heading = html_entity_decode($settings['heading_' . $language], ENT_QUOTES, 'UTF-8');
			$too_large = false;

			foreach ($curl_data['RateRequest']['Shipment']['Package'] as $package) {
				if ($package['Dimensions']['Length'] > 108) $too_large = true;
				if ($package['Dimensions']['Width'] > 108) $too_large = true;
				if ($package['Dimensions']['Height'] > 108) $too_large = true;
				if ($package['PackageWeight']['Weight'] > 150) $too_large = true;
				
				$girth = $package['Dimensions']['Length'] + 2 * $package['Dimensions']['Width'] + 2 * $package['Dimensions']['Height'];
				if ($girth > 165) $too_large = true;
			}

			if ($too_large) {
				$quote_data = array();
				
				$quote_data[$this->name . '_xl'] = array(
					'code'			=> $this->name . '.' . $this->name . '_xl',
					'title'			=> 'Your order is too large to ship via UPS and may require truck freight. You can proceed with your order by selecting this option and we will contact you to arrange shipping and payment for any extra shipping charges before the order ships to you.',
					'name'			=> 'Your order is too large to ship via UPS and may require truck freight. You can proceed with your order by selecting this option and we will contact you to arrange shipping and payment for any extra shipping charges before the order ships to you.',
					'cost'			=> 0.00,
					'tax_class_id'	=> $settings['tax_class_id'],
					'text'			=> $this->currency->format(0, $currency, 1),
				);
				
				return array(
					'code'			=> $this->name,
					'title'			=> $heading,
					'name'			=> $heading,
					'quote'			=> $quote_data,
					'sort_order'	=> $settings['sort_order'],
					'error'			=> array(),
				);
			}		
		}
		*/
		
		// Set shipment date
		$shipping_days = explode(';', $settings['shipping_days']);
		
		if (empty($shipping_days)) {
			$this->logMessage('Error: no shipping days are selected');
			return;
		}
		
		$dates_from_ranges = array();
		$holiday_dates = explode("\n", str_replace(array(' ', '20XX', '20xx'), array('', date('Y'), date('Y')), $settings['holiday_dates']));
		
		foreach ($holiday_dates as $holiday_date) {
			if (!strpos($holiday_date, '::')) continue;
			
			$explode = explode('::', $holiday_date);
			$start = strtotime($explode[0]);
			$end = strtotime($explode[1]);
			
			for ($timestamp = $start; $timestamp <= $end; $timestamp += 86400) {
				$dates_from_ranges[] = date('Y-m-d', $timestamp);
			}
		}
		
		$holiday_dates = array_merge($holiday_dates, $dates_from_ranges);
		
		$estimated_delivery_padding = (int)$settings['estimated_delivery_padding'];
		if (!empty($settings['cutoff_time']) && time() >= strtotime($settings['cutoff_time'])) {
			$estimated_delivery_padding++;
		}
		
		$shipment_timestamp = time();
		
		while ($estimated_delivery_padding > 0) {
			$shipment_timestamp += 86400;
			if (in_array(date('l', $shipment_timestamp), $shipping_days) && !in_array(date('Y-m-d', $shipment_timestamp), $holiday_dates)) {
				$estimated_delivery_padding -= 1;
			}
		}
		
		$curl_data['RateRequest']['Shipment']['DeliveryTimeInformation'] = array(
			'PackageBillType'	=> '03',
			'Pickup'			=> array(
				'Date'	=> date('Ymd', $shipment_timestamp),
			),
		);
		
		// Set SurePost data
		$surepost_curl_data = $curl_data;
		
		if ($boxes[0]['weight'] < 1) {
			$surepost_curl_data['RateRequest']['Shipment']['Service']['Code'] = '92';
			$surepost_curl_data['RateRequest']['Shipment']['Package'][0]['PackageWeight']['UnitOfMeasurement']['Code'] = 'OZS';
			$surepost_curl_data['RateRequest']['Shipment']['Package'][0]['PackageWeight']['Weight'] = (string)round($boxes[0]['weight'] * 16, 1);
			
			if ($surepost_curl_data['RateRequest']['Shipment']['Package'][0]['Dimensions']['UnitOfMeasurement']['Code'] == 'CM') {
				$surepost_curl_data['RateRequest']['Shipment']['Package'][0]['Dimensions']['UnitOfMeasurement']['Code'] = 'IN';
				$surepost_curl_data['RateRequest']['Shipment']['Package'][0]['Dimensions']['Length'] = (string)round($boxes[0]['length'] * 0.393701, 1);
				$surepost_curl_data['RateRequest']['Shipment']['Package'][0]['Dimensions']['Width'] = (string)round($boxes[0]['width'] * 0.393701, 1);
				$surepost_curl_data['RateRequest']['Shipment']['Package'][0]['Dimensions']['Height'] = (string)round($boxes[0]['height'] * 0.393701, 1);
			}
		} else {
			$surepost_curl_data['RateRequest']['Shipment']['Service']['Code'] = '93';
		}
		
		unset($surepost_curl_data['RateRequest']['PickupType']);
		unset($surepost_curl_data['RateRequest']['CustomerClassification']);
		foreach ($surepost_curl_data['RateRequest']['Shipment']['Package'] as $key => $value) {
			unset($surepost_curl_data['RateRequest']['Shipment']['Package'][$key]['PackageServiceOptions']);
		}
		
		// Return package info if the function is being called from the admin
		if (!empty($order_info)) {
			if ($order_info['shipping_code'] == $this->name . '.' . $this->name . '_92' || $order_info['shipping_code'] == $this->name . '.' . $this->name . '_93') {
				return $surepost_curl_data;
			} else {
				return $curl_data;
			}
		}
		
		// Send curl request
		$heading = html_entity_decode($settings['heading_' . $language], ENT_QUOTES, 'UTF-8');
		
		$response = $this->curlRequest('POST', 'api/rating/v1/ShopTimeInTransit', $curl_data);
		
		if (!empty($response['error'])) {
			$this->logMessage('UPS ERROR: ' . $response['error']);
			$this->logMessage('Trying without signature options...');
			
			foreach ($curl_data['RateRequest']['Shipment']['Package'] as $key => $value) {
				unset($curl_data['RateRequest']['Shipment']['Package'][$key]['PackageServiceOptions']['DeliveryConfirmation']['DCISType']);
			}
			unset($curl_data['RateRequest']['Shipment']['ShipmentServiceOptions']['DeliveryConfirmation']['DCISType']);
			
			$response = $this->curlRequest('POST', 'api/rating/v1/ShopTimeInTransit', $curl_data);
		}
		
		if (!empty($response['RateResponse']['RatedShipment']['Service'])) {
			// UPS weirdly doesn't return an array if there's only one service
			$response['RateResponse']['RatedShipment'] = array($response['RateResponse']['RatedShipment']);
		}
		
		$this->logMessage('DATA SENT: ' . print_r($curl_data, true));
		$this->logMessage('DATA RECEIVED: ' . print_r($response, true));
		
		if (!empty($response['error'])) {
			return array(
				'code'			=> $this->name,
				'title'			=> $heading,
				'name'			=> $heading,
				'quote'			=> array(),
				'sort_order'	=> $settings['sort_order'],
				'error'			=> $response['error'],
			);
		}
		
		// Send SurePost curl request
		if (!empty($settings['services'][92][$language]) || !empty($settings['services'][93][$language])) {
			$surepost_response = $this->curlRequest('POST', 'api/rating/v1/RateTimeInTransit', $surepost_curl_data);
			
			$this->logMessage('SUREPOST DATA RECEIVED: ' . print_r($surepost_response, true));
			
			if (empty($surepost_response['error'])) {
				$response['RateResponse']['RatedShipment'][] = $surepost_response['RateResponse']['RatedShipment'];
			}
		}
		
		// Sort results if sorting by price
		if (strpos($settings['rate_sorting'], 'price') === 0) {
			$sorting = array();
			foreach ($response['RateResponse']['RatedShipment'] as $key => $value) {
				$sorting[$key] = $value['TotalCharges']['MonetaryValue'];
			}
			
			if (strpos($settings['rate_sorting'], 'desc')) {
				array_multisort($sorting, SORT_DESC, $response['RateResponse']['RatedShipment']);
			} else {
				array_multisort($sorting, SORT_ASC, $response['RateResponse']['RatedShipment']);
			}
		}
		
		// Parse result
		$rates = array();
		
		foreach ($response['RateResponse']['RatedShipment'] as $rated_shipment) {
			if (empty($rated_shipment['Service']['Code']) || empty($settings['services'][$rated_shipment['Service']['Code']][$language])) {
				continue;
			}
			
			// Get package weights
			$rate_weights = array();
			
			if (isset($rated_shipment['RatedPackage']['Weight'])) {
				$rated_packages = array($rated_shipment['RatedPackage']);
			} else {
				$rated_packages = $rated_shipment['RatedPackage'];
			}
			
			foreach ($rated_packages as $rated_package) {
				$rate_weights[] = $rated_package['Weight'];
			}
			
			// Get package costs
			if (!empty($settings['negotiated_rates']) && !empty($rated_shipment['NegotiatedRateCharges'])) {
				$cost = $rated_shipment['NegotiatedRateCharges']['TotalCharge']['MonetaryValue'];
				$shipment_currency = $rated_shipment['NegotiatedRateCharges']['TotalCharge']['CurrencyCode'];
			} else {
				$cost = $rated_shipment['TotalCharges']['MonetaryValue'];
				$shipment_currency = $rated_shipment['TotalCharges']['CurrencyCode'];
			}
			
			// Get signature cost
			$signature_cost = 0;
			$signature_required_text = '';
			
			if (!empty($settings['signature_type']) && !empty($rated_shipment['ServiceOptionsCharges']['MonetaryValue'])) {
				$signature_cost = $rated_shipment['ServiceOptionsCharges']['MonetaryValue'];
				$signature_required_text = ', ' . $settings['signature_required_text_' . $language];
			}
			
			// Generate estimated delivery text
			$estimated_delivery_text = '';
			
			if (!empty($settings['estimated_delivery_text_' . $language]) && !empty($rated_shipment['TimeInTransit']['ServiceSummary']['EstimatedArrival']['Arrival']['Date'])) {
				$arrival_date = $rated_shipment['TimeInTransit']['ServiceSummary']['EstimatedArrival']['Arrival']['Date'];
				$estimated_date = date($settings['estimated_delivery_format'], strtotime($arrival_date));
				$estimated_delivery_text = ' (' . str_replace('[date]', $estimated_date, $settings['estimated_delivery_text_' . $language]) . ')';
			}
			
			// Set rates into array
			$title = $settings['services'][$rated_shipment['Service']['Code']][$language];
			
			if ($settings['signature_choice'] && $signature_cost) {
				$rates[] = array(
					'id'			=> $rated_shipment['Service']['Code'],
					'identifier'	=> $rated_shipment['Service']['Code'] . '-NO-SIGNATURE',
					'cost'			=> $this->currency->convert($cost - $signature_cost, $shipment_currency, $main_currency),
					'title'			=> $title . $estimated_delivery_text,
					'weights'		=> $rate_weights,
				);
			}
			
			$rates[] = array(
				'id'			=> $rated_shipment['Service']['Code'],
				'identifier'	=> $rated_shipment['Service']['Code'],
				'cost'			=> $this->currency->convert($cost, $shipment_currency, $main_currency),
				'title'			=> $title . $signature_required_text . $estimated_delivery_text,
				'weights'		=> $rate_weights,
			);
		}
		
		// Build quote data
		$quote_data = array();
		
		foreach ($rates as $rate) {
			// Check geo zones
			if (!empty($settings[$rate['id'] . '_geo_zone']) && !in_array($settings[$rate['id'] . '_geo_zone'], $geo_zones)) {
				$this->logMessage('Disabling rate "' . $rate['title'] . '" because the geo zone restriction is not met' . "\n");
				continue;
			}
			
			// Check total limits
			if (!empty($settings[$rate['id'] . '_total_limit'])) {
				$cart_total = (float)array_sum($totals);
				$explode = explode('-', $settings[$rate['id'] . '_total_limit']);
				$lower_limit = (isset($explode[1])) ? (float)$explode[0] : 0;
				$upper_limit = (isset($explode[1])) ? (float)$explode[1] : (float)$explode[0];
				
				if ($cart_total < $lower_limit || $cart_total > $upper_limit) {
					$this->logMessage('Disabling rate "' . $rate['title'] . '" because the cart total of ' . $cart_total . ' is outside the total limit of ' . $lower_limit . '-' . $upper_limit . "\n");
					continue;
				}
			}
			
			// Check weight limits
			if (!empty($settings[$rate['id'] . '_weight_limit'])) {
				foreach ($rate['weights'] as $rate_weight) {
					$package_weight = (float)$rate_weight;
					$explode = explode('-', $settings[$rate['id'] . '_weight_limit']);
					$lower_limit = (isset($explode[1])) ? (float)$explode[0] : 0;
					$upper_limit = (isset($explode[1])) ? (float)$explode[1] : (float)$explode[0];
					
					if ($package_weight < $lower_limit || $package_weight > $upper_limit) {
						$this->logMessage('Disabling rate "' . $rate['title'] . '" because the package weight of ' . $package_weight . ' is outside the weight limit of ' . $lower_limit . '-' . $upper_limit . "\n");
						continue 2;
					}
				}
			}
			
			// Add global rate adjustments
			if (!empty($settings[$request_type . '_rate_adjustment'])) {
				if (strpos($settings[$request_type . '_rate_adjustment'], '%')) {
					$rate_adjustment = $rate['cost'] * (float)$settings[$request_type . '_rate_adjustment'] / 100;
				} else {
					$rate_adjustment = (float)$settings[$request_type . '_rate_adjustment'];
				}
				
				$this->logMessage('Adjusting rate "' . $rate['title'] . '" cost by ' . $rate_adjustment . "\n");
				$rate['cost'] += $rate_adjustment;
			}
			
			// Add individual rate adjustments
			if (!empty($settings[$rate['id'] . '_rate_adjustment'])) {
				if (strpos($settings[$rate['id'] . '_rate_adjustment'], '%')) {
					$rate_adjustment = $rate['cost'] * (float)$settings[$rate['id'] . '_rate_adjustment'] / 100;
				} else {
					$rate_adjustment = (float)$settings[$rate['id'] . '_rate_adjustment'];
				}
				
				$this->logMessage('Adjusting rate "' . $rate['title'] . '" cost by ' . $rate_adjustment . "\n");
				$rate['cost'] += $rate_adjustment;
			}
			
			// Set rate into quote data
			$quote_data[$this->name . '_' . $rate['identifier']] = array(
				'code'			=> $this->name . '.' . $this->name . '_' . $rate['identifier'],
				'title'			=> $rate['title'],
				'name'			=> $rate['title'],
				'cost'			=> $rate['cost'],
				'tax_class_id'	=> $settings['tax_class_id'],
				'text'			=> $this->currency->format($this->tax->calculate($rate['cost'], $settings['tax_class_id'], $this->config->get('config_tax')), $currency),
			);
		}
		
		if (empty($rates)) {
			return array();
		}
		
		// Sort quote data if sorting by name
		if ($settings['rate_sorting'] == 'name') {
			$sorting = array();
			foreach ($quote_data as $key => $value) {
				$sorting[$key] = $value['title'];
			}
			array_multisort($sorting, SORT_ASC, $quote_data);
		}
		
		// Return quote data
		$boxes_text = $settings['boxes_text_' . $language];
		$weight_text = $settings['weight_text_' . $language];
		
		if ($boxes_text || $weight_text) {
			$heading .= ' (';
			if ($boxes_text) {
				$heading .= str_replace('[boxes]', count($boxes), $boxes_text);
				$heading = str_replace(array('1 boxes', '1 Boxes'), array('1 box', '1 Box'), $heading);
			}
			if ($boxes_text && $weight_text) {
				$heading .= ' ';
			}
			if ($weight_text) {
				$weight_texts = array();
				foreach ($boxes as $box) {
					$formatted_weight = $this->weight->format($this->weight->convert($box['weight'], $weight_class_id, $this->config->get('config_weight_class_id')), $this->config->get('config_weight_class_id'));
					$weight_texts[] = str_replace('[weight]', $formatted_weight, $weight_text);
				}
				$heading .= implode(', ', $weight_texts);
			}
			$heading .= ')';
		}
		
		return array(
			'code'			=> $this->name,
			'title'			=> $heading,
			'name'			=> $heading,
			'quote'			=> $quote_data,
			'sort_order'	=> $settings['sort_order'],
			'error'			=> '',
		);
	}
	
	//==============================================================================
	// getSettings()
	//==============================================================================
	private function getSettings() {
		$code = (version_compare(VERSION, '3.0', '<') ? '' : $this->type . '_') . $this->name;
		
		$settings = array();
		$settings_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `key` ASC");
		
		foreach ($settings_query->rows as $setting) {
			$value = $setting['value'];
			if ($setting['serialized']) {
				$value = (version_compare(VERSION, '2.1', '<')) ? unserialize($setting['value']) : json_decode($setting['value'], true);
			}
			$split_key = preg_split('/_(\d+)_?/', str_replace($code . '_', '', $setting['key']), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
			
				if (count($split_key) == 1)	$settings[$split_key[0]] = $value;
			elseif (count($split_key) == 2)	$settings[$split_key[0]][$split_key[1]] = $value;
			elseif (count($split_key) == 3)	$settings[$split_key[0]][$split_key[1]][$split_key[2]] = $value;
			elseif (count($split_key) == 4)	$settings[$split_key[0]][$split_key[1]][$split_key[2]][$split_key[3]] = $value;
			else 							$settings[$split_key[0]][$split_key[1]][$split_key[2]][$split_key[3]][$split_key[4]] = $value;
		}
		
		if (version_compare(VERSION, '4.0', '<')) {
			$settings['extension_route'] = 'extension/' . $this->type . '/' . $this->name;
		} else {
			$settings['extension_route'] = 'extension/' . $this->name . '/' . $this->type . '/' . $this->name;
		}
		
		$this->testing_mode = $settings['testing_mode'];
		
		return $settings;
	}
	
	//==============================================================================
	// logMessage()
	//==============================================================================
	private function logMessage($message) {
		if ($this->testing_mode) {
			$filepath = DIR_LOGS . $this->name . '.messages';
			if (is_file($filepath) && filesize($filepath) > 50000000) {
				file_put_contents($filepath, '');
			}
			$message = print_r($message, true);
			$message = preg_replace('/Array\s+\(/', '(', $message);
			$message = preg_replace('/\n\n/', "\n", $message);
			
			// extension-specific
			if (strpos($message, 'DATA RECEIVED') === 0) {
				$message = str_replace("[Code] => 01\n", "[Code] => 01 (UPS Next Day Air)\n", $message);
				$message = str_replace("[Code] => 02\n", "[Code] => 02 (UPS 2nd Day Air)\n", $message);
				$message = str_replace("[Code] => 03\n", "[Code] => 03 (UPS Ground)\n", $message);
				$message = str_replace("[Code] => 07\n", "[Code] => 07 (UPS Worldwide Express)\n", $message);
				$message = str_replace("[Code] => 08\n", "[Code] => 08 (UPS Worldwide Expedited)\n", $message);
				$message = str_replace("[Code] => 11\n", "[Code] => 11 (UPS Standard)\n", $message);
				$message = str_replace("[Code] => 12\n", "[Code] => 12 (UPS 3 Day Select)\n", $message);
				$message = str_replace("[Code] => 13\n", "[Code] => 13 (UPS Next Day Air Saver)\n", $message);
				$message = str_replace("[Code] => 14\n", "[Code] => 14 (UPS Next Day Air Early A.M.)\n", $message);
				$message = str_replace("[Code] => 54\n", "[Code] => 54 (UPS Worldwide Express Plus)\n", $message);
				$message = str_replace("[Code] => 59\n", "[Code] => 59 (UPS 2nd Day Air A.M.)\n", $message);
				$message = str_replace("[Code] => 65\n", "[Code] => 65 (UPS Worldwide Saver)\n", $message);
			}
			// end
			
			file_put_contents($filepath, $message . "\n\n", FILE_APPEND|LOCK_EX);
		}
	}
	
	//==============================================================================
	// curlRequest()
	//==============================================================================
	public function curlRequest($request, $api, $data = array()) {
		$settings = $this->getSettings();
		
		$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : $this->type . '_';
		$access_token_key = $prefix . $this->name . '_' . $settings['mode'] . '_access_token';
		
		// Check if access token is present
		if (empty($settings[$settings['mode'] . '_access_token']) && $api != 'security/v1/oauth/token') {
			$response = $this->curlRequest('POST', 'security/v1/oauth/token', 'grant_type=client_credentials');
			
			if (!empty($response['error'])) {
				return $response;
			} else {
				$settings[$settings['mode'] . '_access_token'] = $response['access_token'];
				
				$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = '" . $this->db->escape($access_token_key) . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET `code` = '" . $this->db->escape($prefix . $this->name) . "', `key` = '" . $this->db->escape($access_token_key) . "', `value` = '" . $this->db->escape($response['access_token']) . "'");
			}
		}
		
		// Set up curl data
		if ($settings['mode'] == 'test') {
			$url = 'https://wwwcie.ups.com/' . $api;
		} else {
			$url = 'https://onlinetools.ups.com/' . $api;
		}
		
		if ($request == 'GET') {
			$curl = curl_init($url . '?' . http_build_query($data));
		} else {
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
			if ($request != 'POST') {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request);
			}
		}
		
		// Set headers
		if ($api == 'security/v1/oauth/token') {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			$headers = array(
				'Authorization: Basic ' . base64_encode(trim($settings['client_id']) . ':' . trim($settings['client_secret'])),
			);
		} else {
			$headers = array(
				'Authorization: Bearer ' . $settings[$settings['mode'] . '_access_token'],
			);
		}
		
		// Execute curl call
		curl_setopt_array($curl, array(
			CURLOPT_CONNECTTIMEOUT	=> 30,
			CURLOPT_FORBID_REUSE	=> true,
			CURLOPT_FRESH_CONNECT	=> true,
			CURLOPT_HEADER			=> false,
			CURLOPT_HTTPHEADER		=> $headers,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_SSL_VERIFYPEER	=> false,
			CURLOPT_TIMEOUT			=> 30
		));
		
		$response = json_decode(curl_exec($curl), true);
		
		// Check if access token needs to be refreshed
		if (!empty($response['response']['errors'][0]['code']) && ($response['response']['errors'][0]['code'] == 10401 || $response['response']['errors'][0]['code'] == 250002)) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = '" . $this->db->escape($access_token_key) . "'");
		}
		
		// Check for errors
		$errors = array();
		
		if (curl_error($curl)) {
			$errors[] = 'CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl);
		} elseif (empty($response)) {
			$errors[] = 'CURL ERROR: Empty Gateway Response';
		}
		curl_close($curl);
		
		if (!empty($response['response']['errors'])) {
			foreach ($response['response']['errors'] as $error) {
				$errors[] = $error['code'] . ': ' . $error['message'];
			}
		}
		
		if ($errors) {
			$response['error'] = 'Error ' . implode('; ', $errors);
		}
		
		// Return response
		return $response;
	}
}
?>