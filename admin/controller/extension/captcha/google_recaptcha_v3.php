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

//namespace Opencart\Admin\Controller\Extension\GoogleRecaptchaV3\Captcha;
//class GoogleRecaptchaV3 extends \Opencart\System\Engine\Controller {

class ControllerExtensionCaptchaGoogleRecaptchaV3 extends Controller {
	
	private $type = 'captcha';
	private $name = 'google_recaptcha_v3';
	
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
		
		//------------------------------------------------------------------------------
		// Data Arrays
		//------------------------------------------------------------------------------
		$data['customer_group_array'] = array(0 => $data['text_guests']);
		$this->load->model((version_compare(VERSION, '2.1', '<') ? 'sale' : 'customer') . '/customer_group');
		foreach ($this->{'model_' . (version_compare(VERSION, '2.1', '<') ? 'sale' : 'customer') . '_customer_group'}->getCustomerGroups() as $customer_group) {
			$data['customer_group_array'][$customer_group['customer_group_id']] = $customer_group['name'];
		}
		
		$data['language_array'] = array($this->config->get('config_language') => '');
		$data['language_flags'] = array();
		$this->load->model('localisation/language');
		foreach ($this->model_localisation_language->getLanguages() as $language) {
			$data['language_array'][$language['code']] = $language['name'];
			$data['language_flags'][$language['code']] = (version_compare(VERSION, '2.2', '<')) ? 'view/image/flags/' . $language['image'] : 'language/' . $language['code'] . '/' . $language['code'] . '.png';
		}
		
		//------------------------------------------------------------------------------
		// Extensions Settings
		//------------------------------------------------------------------------------
		$data['settings'] = array();
		
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info text-center pad-bottom">' . $data['help_signup'] . '</div>',
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
			'key'		=> 'site_key',
			'type'		=> 'text',
		);
		$data['settings'][] = array(
			'key'		=> 'secret_key',
			'type'		=> 'text',
		);
		$data['settings'][] = array(
			'key'		=> 'error_message',
			'type'		=> 'multilingual_text',
			'default'	=> 'You have been detected as a bot. Please clear your cookies and reload the page, or fill out the captcha above.',
		);
		$data['settings'][] = array(
			'key'		=> 'customer_groups',
			'type'		=> 'checkboxes',
			'options'	=> $data['customer_group_array'],
			'default'	=> array_keys($data['customer_group_array']),
		);
		$data['settings'][] = array(
			'key'		=> 'ips_to_ignore',
			'type'		=> 'textarea',
			'attributes'=> array('style' => 'width: 250px !important'),
		);
		$data['settings'][] = array(
			'key'		=> 'testing_mode',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
		);
		
		// Score Thresholds
		$data['settings'][] = array(
			'key'		=> 'score_thresholds',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info text-center pad-bottom">' . $data['help_score_thresholds'] . '</div><hr>',
		);
		$data['settings'][] = array(
			'key'		=> 'account_registration_score',
			'type'		=> 'text',
			'class'		=> 'short',
			'default'	=> '0.5',
		);
		$data['settings'][] = array(
			'key'		=> 'contact_page_score',
			'type'		=> 'text',
			'class'		=> 'short',
			'default'	=> '0.5',
		);
		
		if (version_compare(VERSION, '2.2', '>=')) {
			$data['settings'][] = array(
				'key'		=> 'guest_checkout_score',
				'type'		=> 'text',
				'class'		=> 'short',
				'default'	=> '0.5',
			);
		}
		
		$data['settings'][] = array(
			'key'		=> 'product_reviews_score',
			'type'		=> 'text',
			'class'		=> 'short',
			'default'	=> '0.5',
		);
		$data['settings'][] = array(
			'key'		=> 'returns_score',
			'type'		=> 'text',
			'class'		=> 'short',
			'default'	=> '0.5',
		);
		$data['settings'][] = array(
			'key'		=> 'default_score',
			'type'		=> 'text',
			'class'		=> 'short',
			'default'	=> '0.5',
		);
		
		// Score Thresholds
		$data['settings'][] = array(
			'key'		=> 'recaptcha_v2_fallback',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info text-center pad-bottom">' . $data['help_recaptcha_v2_fallback'] . '</div><hr>',
		);
		$data['settings'][] = array(
			'key'		=> 'v2_site_key',
			'type'		=> 'text',
		);
		$data['settings'][] = array(
			'key'		=> 'v2_secret_key',
			'type'		=> 'text',
		);
		$data['settings'][] = array(
			'key'		=> 'v2_heading',
			'type'		=> 'multilingual_text',
			'default'	=> 'Captcha',
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
				$rules[$matches[1]][] = $matches[2];
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
			$line .= str_replace(array("\t", "\n"), array('    ', '\n'), $value) . "\n";
			
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
			$second_zip = $temp_directory . 'OpenCart 4.0 Versions.zip';
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
}
?>