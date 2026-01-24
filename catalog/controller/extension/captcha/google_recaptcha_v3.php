<?php
//==============================================================================
// Google reCAPTCHA v3 v2024-10-09
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
// 
// All code within this file is copyright Clear Thinking, LLC.
// You may not copy or reuse code within this file without written permission.
//==============================================================================

//namespace Opencart\Catalog\Controller\Extension\GoogleRecaptchaV3\Captcha;
//class GoogleRecaptchaV3 extends \Opencart\System\Engine\Controller {

class ControllerExtensionCaptchaGoogleRecaptchaV3 extends Model {
	
	private $type = 'captcha';
	private $name = 'google_recaptcha_v3';
	
	//==============================================================================
	// index()
	//==============================================================================
	public function index($error = array()) {
		$data['settings'] = $this->getSettings();
		$data['type'] = $this->type;
		$data['name'] = $this->name;
		
		// Check customer groups and IPs to ignore
		$customer_group_id = (int)$this->customer->getGroupId();
		$customer_groups = explode(';', $data['settings']['customer_groups']);
		
		if (!in_array($customer_group_id, $customer_groups)) {
			return;
		}
		
		$ips_to_ignore = array_map('trim', explode("\n", $data['settings']['ips_to_ignore']));
		
		if (in_array($this->request->server['REMOTE_ADDR'], $ips_to_ignore)) {
			return;
		}
		
		// Clear response data
		$this->session->data['recaptcha_response'] = array();
		
		// Set necessary data
		$data['id'] = rand();
		$data['route'] = (!empty($this->request->get['route'])) ? str_replace(array('|', '.'), '/', $this->request->get['route']) : '';
		$data['checkout_page'] = (substr($data['route'], 0, 9) == 'checkout/');
		$data['error_captcha'] = (!empty($error['captcha'])) ? $error['captcha'] : '';
		
		// Render
		$theme = (version_compare(VERSION, '2.2', '<')) ? $this->config->get('config_template') : $this->config->get('theme_default_directory');
		$template = (file_exists(DIR_TEMPLATE . $theme . '/template/extension/' . $this->type . '/' . $this->name . '.twig')) ? $theme : 'default';
		
		if (version_compare(VERSION, '4.0', '<')) {
			$template_file = DIR_TEMPLATE . $template . '/template/extension/' . $this->type . '/' . $this->name . '.twig';
		} elseif (defined('DIR_EXTENSION')) {
			$template_file = DIR_EXTENSION . $this->name . '/catalog/view/template/' . $this->type . '/' . $this->name . '.twig';
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
			
			if (version_compare(VERSION, '4.0', '>=')) {
				$separator = (version_compare(VERSION, '4.0.2.0', '<')) ? '|' : '.';
				$output = str_replace($settings['extension_route'] . '/', $settings['extension_route'] . $separator, $output);
			}
			
			return $output;
		} else {
			return 'Error loading template file: ' . $template_file;
		}
	}
	
	//==============================================================================
	// checkToken()
	//==============================================================================
	public function checkToken() {
		if (empty($this->request->post['recaptcha_token'])) return;
		
		$settings = $this->getSettings();
		$language = (!empty($this->session->data['language'])) ? $this->session->data['language'] : $this->config->get('config_language');
		
		$curl = curl_init('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($settings['secret_key']) . '&response=' . $this->request->post['recaptcha_token'] . '&remoteip=' . $this->request->server['REMOTE_ADDR']);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = json_decode(curl_exec($curl), true);
		curl_close($curl);
		
		if (!empty($settings['v2_site_key']) && !empty($response['action']) && $this->validate($response)) {
			echo '<fieldset id="google-recaptcha-v2-' . $this->request->post['id'] . '" style="min-height: 140px"><legend>' . html_entity_decode($settings['v2_heading_' . $language], ENT_QUOTES, 'UTF-8') . '</legend><script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl"></script><div class="g-recaptcha" data-sitekey="' . $settings['v2_site_key'] . '" data-expired-callback="grecaptcha.reset()"></div><br></fieldset>';
		} else {
			$this->session->data['recaptcha_response'] = $response;
		}
		
		if ($settings['testing_mode']) {
			$output = 'GOOGLE RECAPTCHA V3 RESULT\nid: ' . $this->request->post['id'];
			foreach ($response as $key => $value) {
				$output .= '\n' . $key . ': ' . (is_array($value) ? $value[0] : $value);
			}
			echo "<script>console.log('" . $output . "')</script>";
		}
	}
	
	//==============================================================================
	// validate()
	//==============================================================================
	public function validate($response = array()) {
		// Get response
		if (empty($response)) {
			$response = (!empty($this->session->data['recaptcha_response'])) ? $this->session->data['recaptcha_response'] : array();
		}
		
		// Check customer groups and IPs to ignore
		if (!empty($response['action'])) {
			$route = explode('/', $response['action']);
		} else {
			if (empty($this->request->get['route'])) {
				return;
			} else {
				$route = explode('/', $this->request->get['route']);
			}
		}
		
		if (version_compare(VERSION, '4.0', '>=')) {
			$separator = (version_compare(VERSION, '4.0.2.0', '<')) ? '|' : '.';
			$explode = explode($separator, $route[1]);
			$route[1] = $explode[0];
		}
		
		$settings = $this->getSettings();
		$language = (!empty($this->session->data['language'])) ? $this->session->data['language'] : $this->config->get('config_language');
		$customer_group_id = (int)$this->customer->getGroupId();
		$customer_groups = explode(';', $settings['customer_groups']);
		
		if (!in_array($customer_group_id, $customer_groups)) {
			return;
		}
		
		$ips_to_ignore = array_map('trim', explode("\n", $settings['ips_to_ignore']));
		
		if (in_array($this->request->server['REMOTE_ADDR'], $ips_to_ignore)) {
			return;
		}
		
		// Validate captcha
		$error_message = '';
		
		if (empty($response)) {
			// reCAPTCHA v2
			if (empty($this->request->post['g-recaptcha-response'])) $this->request->post['g-recaptcha-response'] = '';
			
			$curl = curl_init('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($settings['v2_secret_key']) . '&response=' . $this->request->post['g-recaptcha-response'] . '&remoteip=' . $this->request->server['REMOTE_ADDR']);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$response = json_decode(curl_exec($curl), true);
			curl_close($curl);
			
			if (empty($response['success'])) {
				$error_message = html_entity_decode($settings['error_message_' . $language], ENT_QUOTES, 'UTF-8');
			}
		} else {
			// reCAPTCHA v3						
			if (empty($response['success']) || empty($response['action'])) {
				$response_failure = true;
				$action_failure = true;
				$score = 0;
			} else {
				$response_failure = false;
				$action_failure = false;
				$score = $settings['default_score'];
				
				if ($route[1] == 'register' || $route[1] == 'checkout' && version_compare(VERSION, '4.0', '>=')) {
					$score = $settings['account_registration_score'];
					$action_failure = ($response['action'] != 'account/register' && strpos($response['action'], 'checkout') === false);
				} elseif ($route[1] == 'contact') {
					$score = $settings['contact_page_score'];
					$action_failure = ($response['action'] != 'information/contact');
				} elseif ($route[1] == 'guest' && version_compare(VERSION, '2.2', '>=') || $route[1] == 'checkout' && version_compare(VERSION, '4.0', '>=')) {
					$score = $settings['guest_checkout_score'];
					$action_failure = (strpos($response['action'], 'checkout') === false);
				} elseif ($route[1] == 'product') {
					$score = $settings['product_reviews_score'];
					$action_failure = ($response['action'] != 'product/product');
				} elseif ($route[1] == 'return' || $route[1] == 'returns') {
					$score = $settings['returns_score'];
					$action_failure = ($response['action'] != 'account/return/add' && $response['action'] != 'account/returns/add');
				}
			}
			
			if ($response_failure || $action_failure || (!empty($response['score']) && $response['score'] < $score)) {
				$error_message = html_entity_decode($settings['error_message_' . $language], ENT_QUOTES, 'UTF-8');
			}
		}
		
		if (empty($error_message)) {
			return '';
		} elseif ($route[0] == 'checkout') {
			return '<div class="alert alert-danger">' . $error_message . '</div>';
		} else {
			return $error_message;
		}
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
		
		return $settings;
	}
}
?>