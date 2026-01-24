<?php  
#this file is common to all 2xxx and 3xxx
if (!defined('TEMPLATE_FOLDER')) {
	if (version_compare(VERSION,'3.0.0.0','>=' )) {
		define('TEMPLATE_FOLDER', 'oc3');
	}else{
		define('TEMPLATE_FOLDER', 'oc2');
	}
}

class ModelExtensionModuleHbCart extends Controller {	

	public function abandoned_cart(){
		//HB ABANDONED CART
		if ($this->config->get('hb_cart_status')) {
			$cookie_expiration_time = time() + (90 * 24 * 60 * 60);  // for 3 month
			
			if (!$this->customer->isLogged()) {
				$guest_session_id = $this->session->getId();
				
				if (!isset($this->request->cookie['hb_session_id'])) {
					setcookie("hb_session_id", $guest_session_id, $cookie_expiration_time, "", "", "", "true");
				}
				
				if (isset($this->session->data['guest']['email']) and !empty($this->session->data['guest']['email']) ) {

					$cart_guest = $this->db->query("SELECT * FROM `".DB_PREFIX."cart_guest` WHERE session_id = '".$this->db->escape($guest_session_id)."' LIMIT 1");
					if ($cart_guest->num_rows > 0 ) {
						$this->db->query("UPDATE `".DB_PREFIX."cart_guest` SET guest_details = '".$this->db->escape(json_encode($this->session->data['guest']))."' WHERE session_id = '". $this->db->escape($guest_session_id) ."'");
					}else{
						$this->db->query("INSERT INTO `".DB_PREFIX."cart_guest` (`session_id`, `guest_details`,`store_id`) VALUES ('".$this->db->escape($guest_session_id)."', '".$this->db->escape(json_encode($this->session->data['guest']))."', '" . (int)$this->config->get('config_store_id') . "')");
					}
					
					setcookie("hb_email", $this->session->data['guest']['email'], $cookie_expiration_time, "", "", "", "true");    
					
				}
				
				if (isset($this->request->cookie['hb_session_id'])) {
					//update hb_session_id with new session_id and copy saved carts
					$previous_cookie = $this->request->cookie['hb_session_id'];
			
					if ($this->session->getId() != $previous_cookie) {
						$this->db->query("UPDATE `".DB_PREFIX."hb_cart` SET session_id = '".$this->db->escape($this->session->getId())."' WHERE session_id = '".$this->db->escape($previous_cookie)."'");
						$this->db->query("UPDATE `".DB_PREFIX."cart` SET session_id = '".$this->db->escape($this->session->getId())."' WHERE session_id = '".$this->db->escape($previous_cookie)."'");
						$this->db->query("UPDATE `".DB_PREFIX."cart_guest` SET session_id = '".$this->db->escape($this->session->getId())."', date_modified = now() WHERE session_id = '".$this->db->escape($previous_cookie)."'");
						setcookie("hb_session_id", $this->session->getId(), $cookie_expiration_time, "", "", "", "true");
					}					
					
				}
				
				if ($this->config->get('hb_cart_ip_matching')) {
					if (!empty($_SERVER['HTTP_CLIENT_IP']))  {
						$ip_address = $_SERVER['HTTP_CLIENT_IP'];
					}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 	{
						$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
					}else {
						$ip_address = $_SERVER['REMOTE_ADDR'];
					}

					$ip_data = $this->db->query("SELECT session_id FROM `".DB_PREFIX."hb_cart` WHERE ip = '".$this->db->escape($ip_address)."' LIMIT 1");
					
					if ($ip_data->row) {
						$ip_session_id = $ip_data->row['session_id'];
						$this->db->query("UPDATE `".DB_PREFIX."hb_cart` SET session_id = '".$this->db->escape($this->session->getId())."' WHERE session_id = '".$this->db->escape($ip_session_id)."'");
						$this->db->query("UPDATE `".DB_PREFIX."cart` SET session_id = '".$this->db->escape($this->session->getId())."' WHERE session_id = '".$this->db->escape($ip_session_id)."'");
						$this->db->query("UPDATE `".DB_PREFIX."cart_guest` SET session_id = '".$this->db->escape($this->session->getId())."', date_modified = now() WHERE session_id = '".$this->db->escape($ip_session_id)."'");
						setcookie("hb_session_id", $this->session->getId(), $cookie_expiration_time, "", "", "", "true");						
					}
				}

				
				if (isset($this->request->cookie['hb_email'])) {
					if (filter_var($this->request->cookie['hb_email'], FILTER_VALIDATE_EMAIL)) {
						$guest_email = array('email'	=> $this->request->cookie['hb_email']);
						$cart_guest = $this->db->query("SELECT * FROM `".DB_PREFIX."cart_guest` WHERE session_id = '".$this->db->escape($guest_session_id)."' LIMIT 1");
						if ($cart_guest->num_rows == 0 ) {
							$this->db->query("INSERT INTO `".DB_PREFIX."cart_guest` (`session_id`, `guest_details`,`store_id`) VALUES ('".$this->db->escape($this->session->getId())."', '".$this->db->escape(json_encode($guest_email))."', '" . (int)$this->config->get('config_store_id') . "')");
						}
					}
				}
			}
			
			if (!isset($this->request->cookie['hb_email']) and $this->customer->isLogged()){
				$cookie_expiration_time = time() + (30 * 24 * 60 * 60);  // for 1 month
				setcookie("hb_email", $this->customer->getEmail(), $cookie_expiration_time, "", "", "", "true"); 
			}
			
			
		}
		//HB ABANDONED CART
	}

	public function last_visited(){
		if ($this->cart->hasProducts()){
			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$current_url = (isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] === 'on' ? "https" : "http").'://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$current_url = '';
			}

			$this->db->query("UPDATE `".DB_PREFIX."hb_cart` SET last_visited = '".$this->db->escape($current_url)."', date_added = now() WHERE session_id = '".$this->db->escape($this->session->getId())."'");
		}
	}

	public function compose_abandoned_cart_email($compose){
		$basic_details = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hb_cart` WHERE session_id = '" . $this->db->escape($compose['session_id']) . "' AND customer_id = '".(int)$compose['customer_id']."' LIMIT 1");
		if ($basic_details->row) {
			$cart_id 		= (int)$basic_details->row['cart_id'];
			$customer_id 	= (int)$basic_details->row['customer_id'];
			$language_id 	= (int)$basic_details->row['language_id'];
			$store_id 		= (int)$basic_details->row['store_id'];
			$hb_cart_log_id	= (int)$basic_details->row['hb_cart_log_id'];
			
			$this->load->model('setting/setting');
			$extn_info = $this->model_setting_setting->getSetting('hb_cart', (int)$store_id);

			$this->load->model('localisation/language');
			$language_info = $this->model_localisation_language->getLanguage($language_id);
			if ($language_info) {
				$language_code = $language_info['code'];
				$language_directory = $language_info['directory'];
			} else {
				$language_code = $this->config->get('config_language');
				$language_directory = 'english';
			}
			
			$data = array();
		
			if (version_compare(VERSION,'2.0.2.0','<=')) {
				$language = new Language($language_directory);
				$language->load('default');
			}else if ((version_compare(VERSION,'2.0.3.1','>=')) and (version_compare(VERSION,'2.2.0.0','<'))){
				$language = new Language($language_directory);
				$language->load($language_directory);
			}else{
				$language = new Language($language_code);
				$language->load($language_code);
			}
			
			$language->load('extension/module/hb_cart');
			$text_strings = array(
				'text_date_added','text_email','text_telephone','text_ip','text_products','text_product','text_product_image','text_model','text_quantity','text_price','text_order_total','text_total','text_view_item','text_continue_shopping','text_checkout','text_reviews','text_more_reviews','text_view_more',
			);
			
			foreach ($text_strings as $text) {
				$data[$text] = $language->get($text);
			}
			
			$data['https_server'] = HTTPS_SERVER; 

			if ($compose['reminder']){
				$width 	= isset($extn_info['hb_cart_img_width_reminder'])?$extn_info['hb_cart_img_width_reminder']: '80';
				$height = isset($extn_info['hb_cart_img_height_reminder'])?$extn_info['hb_cart_img_height_reminder']: '80';
			}else{
				$width 	= isset($extn_info['hb_cart_img_width_first'])?$extn_info['hb_cart_img_width_first']: '80';
				$height = isset($extn_info['hb_cart_img_height_first'])?$extn_info['hb_cart_img_height_first']: '80';
			}

			$image_dimension = array('w'=> $width, 'h'=> $height);

			$data['products'] = $this->getProducts($compose['session_id'], $image_dimension, $language_id);
			
			if (empty($data['products'])) {
				$this->addlog("Product not in stock or not available for session ID ".$compose['session_id']);
				$email_data['subject'] = 'Product not in stock!';
				$email_data['content'] = 'Email cannot be composed because product not in stock or product not available!';
				return $email_data;
			}

			$data['cart_value'] = 0;
			foreach ($data['products'] as $item){
			    $data['product'] = array(
						'product_id' => $item['product_id'],
        				'name' 		=> $item['name'],
        				'image' 	=> $item['image'],
        				'model' 	=> $item['model'],
        				'href'		=> $item['href'],
        				'option'	=> $item['option'],
        				'price'		=> $item['price'],
						'total'		=> $item['total']
			        );

				$data['cart_value'] += $item['total_value'];

				$data['product_name'] 	= $data['product']['name'];
				$data['product_name_limited'] =  (strlen($data['product']['name']) > 20) ? substr($data['product']['name'], 0, 17) . '...' : $data['product']['name'];
			}
			
			$data['total_product'] = count($data['products']);

			$data['remaining'] 	   = $data['total_product'] - 1;
			if ($data['total_product'] > 1) {
				$data['subject_creative'] 	   =  sprintf($language->get('text_subject_creative_1'), $data['product_name'], $data['remaining']);
			}else{
				$data['subject_creative'] 	   =  sprintf($language->get('text_subject_creative_2'), $data['product_name']);
			}	
			
			
			if ($data['total_product'] == 1 ) {	
				foreach ($data['products'] as $product) {
					$product_id = $product['product_id'];
				}
			}
			
			//PRODUCT REVIEWS
			$data['reviews'] = array();
			if ($data['total_product'] == 1 ) {
				$data['reviews'] = $this->product_reviews($product_id, 3);
			}
			
			//customer details
			$data['firstname'] = $data['lastname'] = $data['email'] = $data['telephone'] = '';
			if ($customer_id > 0) {
				$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
				if ($customer_query->row) {
					$data['firstname'] 			= $customer_query->row['firstname'];
					$data['lastname'] 			= $customer_query->row['lastname'];
					$data['email'] 				= $customer_query->row['email'];
					$data['telephone'] 			= $customer_query->row['telephone'];
					$data['customer_group_id'] 	= $customer_query->row['customer_group_id'];
				}
			}else{
				$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart_guest WHERE session_id = '" . $this->db->escape($compose['session_id']) . "' LIMIT 1");
				if ($customer_query->row) {
					$guest_details = json_decode($customer_query->row['guest_details'], true);
					$data['firstname'] 			= (isset($guest_details['firstname']))? $guest_details['firstname'] : '';
					$data['lastname'] 			= (isset($guest_details['lastname']))? $guest_details['lastname'] : '';
					$data['email'] 				= (isset($guest_details['email']))? $guest_details['email'] : '';
					$data['telephone'] 			= (isset($guest_details['telephone']))? $guest_details['telephone'] : '';
					$data['customer_group_id'] 	= (isset($guest_details['customer_group_id']))? $guest_details['customer_group_id'] : '';
				}
			}

			if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
				//create cart item html
				if (version_compare(VERSION,'2.2.0.0','<' )) {
					$data['cart_items'] = $this->load->view('default/template/extension/module/oc2/abandoned_cart_items_templates/'.$extn_info['hb_cart_items_template'].'.tpl', $data);
				}else{
					$data['cart_items'] = $this->load->view('extension/module/'.TEMPLATE_FOLDER.'/abandoned_cart_items_templates/'.$extn_info['hb_cart_items_template'], $data);
				}
				
				$data['customer_reviews'] = '';
				if (!empty($data['reviews'])) {
					if (version_compare(VERSION,'2.2.0.0','<' )) {
						$data['customer_reviews'] = $this->load->view('default/template/extension/module/oc2/abandoned_cart_reviews_templates/'.$extn_info['hb_cart_reviews_template'].'.tpl', $data);
					}else{
						$data['customer_reviews'] = $this->load->view('extension/module/'.TEMPLATE_FOLDER.'/abandoned_cart_reviews_templates/'.$extn_info['hb_cart_reviews_template'], $data);
					}
				}
				
				//x people bought this in the last 24 hours	
				$data['sold'] = ''; 
				
				if ($data['total_product'] == 1 ) {				
					$data['sold'] = $this->totalproductsold($product_id, 24);
					
					if ($data['sold'] and $data['sold'] > 0) {
						$data['sold'] = sprintf($language->get('text_sold'), $data['sold'], 24);
					}else {
						$data['sold'] = '';
					}			
				}
				//x people bought this in the last 24 hours	

				//x people bought this in total
				$data['total_sold'] = ''; 
				
				if ($data['total_product'] == 1 ) {				
					$data['total_sold'] = $this->totalproductsold($product_id, 0);
					
					if ($data['total_sold'] and $data['total_sold'] > 0) {
						$data['total_sold'] = sprintf($language->get('total_sold'), $data['sold']);
					}else {
						$data['total_sold'] = '';
					}			
				}
				//x people bought this in total
				
				//COUPON CODE
				$data['coupon'] 	= $data['coupon_expiry'] = $data['coupon_discount'] = $data['coupon_minimum'] = '';

				if (isset($extn_info['hb_cart_coupon'])) {
					if ($compose['reminder']) {
						$coupon_data = $this->generate_coupon($extn_info, 2, $data['cart_value'], $data['products'], $cart_id);
					}else{
						$coupon_data = $this->generate_coupon($extn_info, 1, $data['cart_value'], $data['products'], $cart_id);
					}	

					$data['coupon'] 			= $coupon_data['coupon'];
					$data['coupon_expiry'] 		= $coupon_data['coupon_expiry'];
					$data['coupon_discount'] 	= $coupon_data['coupon_discount'];
					$data['coupon_minimum'] 	= $coupon_data['coupon_minimum'];
				}

				if (empty($data['coupon'])) {
					$data['coupon_block_start'] = '<!--';
					$data['coupon_block_end'] 	= '-->';
				}else{
					$data['coupon_block_start'] = '';
					$data['coupon_block_end'] 	= '';
				}

				//COUPON CODE

				if ($this->isExtensionInstalled('email_templates')){
					$this->load->model('extension/module/email_builder');
					$use_email_designer_app = true;
				}else{
					$use_email_designer_app = false;
				}

				$email_enable  = isset($extn_info['hb_cart_enable'])? true: false;

				if ($compose['preview']) {
					$email_template_id = $compose['template_id'];
				}else{
					if ($compose['reminder']) {
						$email_template_id = isset($extn_info['hb_cart_email_reminder'.$language_id])?$extn_info['hb_cart_email_reminder'.$language_id]: '0';
					}else{
						$email_template_id = isset($extn_info['hb_cart_email_first'.$language_id])?$extn_info['hb_cart_email_first'.$language_id]: '0';
					}
				}
				
				if ($email_template_id == 0) {
					$email_enable = false;
				}

				if ($email_template_id > 0) {
					$to = $data['email'];

					if ($use_email_designer_app){
						$template_data 	= $this->model_extension_module_email_builder->builtTemplate($email_template_id);
					}else{
						$template_data 	= $this->getTemplateData($email_template_id);
					}

					//UPDATE HB CART LOG
					$data['email_view_tracking_pixel'] = '';

					if (!isset($compose['send_preview'])){
						$compose['send_preview'] = false;
					}

					if (!$compose['preview'] && !$compose['send_preview']) {
						if ($compose['reminder']){
							$this->db->query("UPDATE " . DB_PREFIX . "hb_cart SET reminder = reminder + 1, date_modified = now() WHERE session_id = '".$this->db->escape($compose['session_id'])."'");
							$this->db->query("UPDATE " . DB_PREFIX . "hb_cart_log SET reminder = reminder + 1, date_modified = now() WHERE id = '".(int)$hb_cart_log_id."'");
						}else{
							$cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "hb_cart WHERE session_id = '" . $this->db->escape($compose['session_id']) . "'");
							foreach ($cart_query->rows as $cart) {
								$cart_data[] = array(
								'product_id' 	=> $cart['product_id'],
								'recurring_id' 	=> $cart['recurring_id'],
								'option' 		=> json_decode($cart['option'], true),
								'quantity' 		=> $cart['quantity']
								);
							}
							
							$log_query = $this->db->query("SELECT id FROM " . DB_PREFIX . "hb_cart_log WHERE session_id = '".$this->db->escape($compose['session_id'])."' AND email = '".$this->db->escape($to)."' LIMIT 1");
							if ($log_query->num_rows > 0) {
								$hb_cart_log_id = $log_query->row['id'];
								$email_enable = false;
							}else{
								$this->db->query("INSERT INTO " . DB_PREFIX . "hb_cart_log (customer_id, session_id, email, cart_data, first_email, reminder, store_id, date_modified) VALUES ('".(int)$customer_id."','".$this->db->escape($compose['session_id'])."', '".$this->db->escape($to)."','".$this->db->escape(json_encode($cart_data))."', '1','0', '".(int)$store_id."',now())");
								$hb_cart_log_id = $this->db->getLastId();
							}
		
							$this->db->query("UPDATE " . DB_PREFIX . "hb_cart SET first_email = 1, hb_cart_log_id = '".(int)$hb_cart_log_id."', date_modified = now() WHERE session_id = '".$this->db->escape($compose['session_id'])."'");
							$this->addlog("--Updated relevant tables and Log inserted. [Reference: ".$hb_cart_log_id."]");
						}

						
						$email_viewed_image_link = $this->url->link('extension/module/hb_cart/email_viewed', 'id=' .(int)$hb_cart_log_id.'&utm_source=huntbee_extension&utm_medium=abandoned_cart_email&utm_campaign=huntbee_abandoned_cart_email_viewed');
						$data['email_view_tracking_pixel'] = '<img src="'.$email_viewed_image_link.'" alt="pixel" width="0.1px" height="0.1px">';
					}
					
					$encrypted_string = $this->encrypt($extn_info['hb_cart_pass_key'],$hb_cart_log_id);

					$data['unsubscribe'] 	= $this->url->link('extension/module/hb_cart/unsubscribe', 'email=' .$data['email'].'&key='.urlencode($encrypted_string).'&utm_source=huntbee_extension&utm_medium=abandoned_cart_email&utm_campaign=huntbee_abandoned_cart_email_unsubscribe');
					$data['checkout']  		= $this->url->link('extension/module/hb_cart/checkout', 'key1='.urlencode($encrypted_string).'&key2='.urlencode($this->encrypt($extn_info['hb_cart_pass_key'],$customer_id)).'&utm_source=huntbee-extension&utm_medium=abandoned_cart_email&utm_campaign=huntbee_abandoned_cart_email_conversion');
					
					//UPDATE HB CART LOG

					if ($template_data) {
						$email_subject = $template_data['email_options']['email_subject'];
						$email_content = $template_data['email_content'];

						//replacing shortcode variables
						foreach ($data as $key => $value) {
							if (!is_array($value)) {
								$email_content 		= str_replace('{'.$key.'}',$value,$email_content);
								$email_subject 		= str_replace('{'.$key.'}',$value,$email_subject);
							}
						}
					}

					//SMS template
					if ($compose['reminder']){
						$sms_template = isset($extn_info['hb_cart_sms_reminder'.$language_id])?$extn_info['hb_cart_sms_reminder'.$language_id]: '';
						$sms_enable  = isset($extn_info['hb_cart_sms_first_status'])? true: false;
					}else{
						$sms_template = isset($extn_info['hb_cart_sms_first'.$language_id])?$extn_info['hb_cart_sms_first'.$language_id]: '';
						$sms_enable  = isset($extn_info['hb_cart_sms_reminder_status'])? true: false;
					}

					$email_data = array(          
						'to'              => $to,             
						'from'            => $template_data['email_options']['sender_email'],
						'store_name'      => $template_data['email_options']['sender_name'],
						'email_replyto'   => $template_data['email_options']['email_replyto'],
						'subject'         => $email_subject,
						'content'         => $email_content,
						'attachments'     => (isset($template_data['email_options']['email_attachments']))? $template_data['email_options']['email_attachments']: '',
						'bcc'             => $template_data['email_options']['email_bcc'],
						'template_id'     => $email_template_id,
						'store_id'        => $template_data['store_id'],
						'type'            => 'account', 
						'cron'            => false
					);
					
					if (isset($compose['send_preview']) && $compose['send_preview']) {
						$this->addlog('Send Email Preview');
						return $email_data;
					}

					if ($compose['preview']) {
						$this->addlog('Email Preview');
						return $email_data;
					}else{

						if ($this->is_unsubscribed($to)) {
							$this->addlog("User email ".$to." is in unsubscription list");
							return false;
						}

						if ($email_enable) {
							if ($use_email_designer_app){		
								$this->model_extension_module_email_builder->sendemail($email_data);
								$this->addlog("Email sent to ".$to." via template designer extn");
							}else{	 //else normal email
								$this->sendemail($email_data);
								$this->addlog("Email sent to ".$to);
							}
						}else{
							$this->addlog("Sending email is not enabled.");
						}
						
						//SMS SEND
						$phonenumber = $data['telephone'];
						if ($sms_enable && !empty($phonenumber)) {
							foreach ($data as $key => $value) {
								if (!is_array($value)) {
									$sms_template 		= str_replace('{'.$key.'}',$value,$sms_template);
								}
							}

							if ($this->isExtensionInstalled('opencart_sms')){
								$sms_config = $this->model_setting_setting->getSetting('hb_sms', $store_id);
								$this->load->library('hbsms');
								$this->hbsms->call_api($phonenumber, $sms_template, '1', $sms_config);
								$this->addlog('SMS sent to '.$phonenumber.' via HuntBee OpenCart SMS');
							}else{
								$this->addlog('OpenCart SMS Extension not installed!');
							}
						}					
					}	
				}else{
					$this->addlog("Invalid Email Template ID");
					$email_data['subject'] = 'No template assigned';
					$email_data['content'] = 'No template assigned';
					return $email_data;
				}
			} else { // VALID EMAIL END
				$this->addlog("Invalid Email: ".$data['email']);
			}
		}else{
			$this->addlog("No data found for Session ID: ".$compose['session_id']);
		}
	}

	public function auto(){
		if ($this->config->get('hb_cart_status') and $this->config->get('hb_cart_enable')) {	
			$store_id = (int)$this->config->get('config_store_id');
			$this->runscript($store_id);
		}
	}

	public function runscript($store_id){	
		if ($this->config->get('hb_cart_coupon_delete')) {
			$this->delete_expired_coupon();
		}
		
		$this->load->model('setting/setting');
		$extn_info = $this->model_setting_setting->getSetting('hb_cart', (int)$store_id);
		
		$trigger_hours 			= isset($extn_info['hb_cart_trigger'])? $extn_info['hb_cart_trigger']:'6';
		$reminder_enabled 		= isset($extn_info['hb_cart_enable_reminder'])? true : false;
		$reminder_count 		= isset($extn_info['hb_cart_reminder_count'])? $extn_info['hb_cart_reminder_count']:'1';
		$reminder_gap 			= isset($extn_info['hb_cart_reminder_gap'])? $extn_info['hb_cart_reminder_gap']:'1';
		$hb_cart_email_only_customer 	= isset($extn_info['hb_cart_email_guest'])? true : false;
		$hb_cart_email_enable 	= isset($extn_info['hb_cart_enable'])? true : false;
		
		if ($hb_cart_email_enable) {
			//INTIAL ABANDONED CART EMAIL
			$query = $this->db->query("SELECT * FROM `".DB_PREFIX."hb_cart` WHERE first_email = 0 AND date_added < DATE_SUB(NOW(), INTERVAL ".(int)$trigger_hours." HOUR) GROUP BY session_id ");
			
			if ($query->rows) {
				foreach ($query->rows as $row) {
					$session_id 	= $row['session_id'];
					$customer_id 	= $row['customer_id'];

					$compose = array(
						'cart_id' 			=> $row['cart_id'],
						'customer_id' 		=> $customer_id,
						'session_id'		=> $session_id,
						'reminder'			=> false,
						'preview'			=> false
					);

					if ($customer_id == 0 && $hb_cart_email_only_customer) {
						$this->addlog("This operation is aborted because you have chosen not to send email to guest customer.");
					}else{
						$guest_data = $this->db->query("SELECT count(*) as total FROM `".DB_PREFIX."cart_guest` WHERE session_id = '".$this->db->escape($session_id)."' LIMIT 1");
						if ($customer_id == 0 and $guest_data->row['total'] == 0) {
							//DO NOTHING
						}else{
							$this->compose_abandoned_cart_email($compose);
						}
					}
				}
			}else{
				$this->addlog("No Eligible Abandoned Cart Initial Email");
			}
			
			//REMINDER ABANDONED CART EMAIL
			if ($reminder_enabled) {
				$query = $this->db->query("SELECT * FROM `".DB_PREFIX."hb_cart` WHERE first_email = 1 AND reminder < '".(int)$reminder_count."' AND date_modified < DATE_SUB(NOW(), INTERVAL ".(int)$reminder_gap." DAY) GROUP BY session_id ");
				
				if ($query->rows) {
					foreach ($query->rows as $row) {
						$session_id 	= $row['session_id'];
						$customer_id 	= $row['customer_id'];

						$compose = array(
							'cart_id' 			=> $row['cart_id'],
							'customer_id' 		=> $customer_id,
							'session_id'		=> $session_id,
							'reminder'			=> true,
							'preview'			=> false
						);

						if ($customer_id == 0 && $hb_cart_email_only_customer) {
							$this->addlog("This operation is aborted because you have chosen not to send email to guest customer.");
						}else{
							$this->compose_abandoned_cart_email($compose);
						}
					}
				}else{
					$this->addlog("No Eligible Reminder Emails");
				}
			}
		}
		
		//echo 'Huntbee abandoned cart email job run successful';
	}

	public function is_unsubscribed($email){
		$query = $this->db->query("SELECT count(*) as count FROM `".DB_PREFIX."hb_email_unsubscribe` WHERE `email` = '".$this->db->escape($email)."' AND email_type_id = 7");
		if ($query->row['count'] > 0){
			return true;
		}else{
			return false;
		}
	}

	public function unsubscribe($email, $store_id){
		$ip = (isset($_SERVER['REMOTE_ADDR']))? $_SERVER['REMOTE_ADDR']:'';
		$this->db->query("INSERT INTO `".DB_PREFIX."hb_email_unsubscribe` (`email`,`ip`,`email_type_id`,`store_id`) VALUES ('".$this->db->escape($email)."','".$this->db->escape($ip)."','7','".(int)$store_id."')");
	}

	public function totalproductsold($product_id, $hours){
		if ($hours == 0) {
			$query = $this->db->query("SELECT sum(b.quantity) as total FROM `" . DB_PREFIX . "order` a, `" . DB_PREFIX . "order_product` b WHERE a.order_id = b.order_id AND b.product_id = '".(int)$product_id."' AND a.order_status_id > 0");
		}else{
			$query = $this->db->query("SELECT sum(b.quantity) as total FROM `" . DB_PREFIX . "order` a, `" . DB_PREFIX . "order_product` b WHERE a.order_id = b.order_id AND b.product_id = '".(int)$product_id."' AND a.order_status_id > 0 AND a.date_added > DATE_SUB(NOW(), INTERVAL ".$hours." HOUR)");
		}
		if ($query->row['total'] > 0) {
			return $query->row['total'];
		}else{
			return false;
		}
	}
	
	public function product_reviews($product_id, $rating = 3){
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "review` WHERE product_id = '".(int)$product_id."' AND status = 1 AND rating >= '".(int)$rating."' ORDER BY rating DESC, date_added DESC LIMIT 5");
		if ($query->rows) {
			foreach ($query->rows as $row) {
				$reviews[] = array(
					'author'	=> $row['author'],
					'text'		=> substr($row['text'],0,300),
					'rating'	=> round($row['rating']),
					'date_added'=> date('d M Y',strtotime($row['date_added'])),
				);
			}
			return $reviews;
		}else{
			return false;
		}
	}

	public function getProducts($session_id, $image_dimension, $language_id = 1) {
		$this->load->model('tool/image');
		
		$product_data = array();

		$cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "hb_cart WHERE session_id = '" . $this->db->escape($session_id) . "'");

		foreach ($cart_query->rows as $cart) {
			$stock = true;

			if ($this->config->get('hb_cart_remove_oos_product')) {
                $product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store p2s LEFT JOIN " . DB_PREFIX . "product p ON (p2s.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p2s.product_id = '" . (int)$cart['product_id'] . "' AND pd.language_id = '" . (int)$language_id . "' AND p.date_available <= NOW() AND p.status = '1' AND p.quantity > 0");
            }else{
                $product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store p2s LEFT JOIN " . DB_PREFIX . "product p ON (p2s.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p2s.product_id = '" . (int)$cart['product_id'] . "' AND pd.language_id = '" . (int)$language_id . "' AND p.date_available <= NOW() AND p.status = '1'");
            }

			if ($product_query->num_rows && ($cart['quantity'] > 0)) {
				$option_price = 0;
				$option_points = 0;
				$option_weight = 0;

				$option_data = array();

				foreach (json_decode($cart['option']) as $product_option_id => $value) {
					$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$cart['product_id'] . "' AND od.language_id = '" . (int)$language_id . "'");

					if ($option_query->num_rows) {
						if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio' || $option_query->row['type'] == 'image') {
							$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$language_id . "'");

							if ($option_value_query->num_rows) {
								if ($option_value_query->row['price_prefix'] == '+') {
									$option_price += $option_value_query->row['price'];
								} elseif ($option_value_query->row['price_prefix'] == '-') {
									$option_price -= $option_value_query->row['price'];
								}

								if ($option_value_query->row['points_prefix'] == '+') {
									$option_points += $option_value_query->row['points'];
								} elseif ($option_value_query->row['points_prefix'] == '-') {
									$option_points -= $option_value_query->row['points'];
								}

								if ($option_value_query->row['weight_prefix'] == '+') {
									$option_weight += $option_value_query->row['weight'];
								} elseif ($option_value_query->row['weight_prefix'] == '-') {
									$option_weight -= $option_value_query->row['weight'];
								}

								if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $cart['quantity']))) {
									$stock = false;
								}

								$option_data[] = array(
									'product_option_id'       => $product_option_id,
									'product_option_value_id' => $value,
									'option_id'               => $option_query->row['option_id'],
									'option_value_id'         => $option_value_query->row['option_value_id'],
									'name'                    => $option_query->row['name'],
									'value'                   => $option_value_query->row['name'],
									'type'                    => $option_query->row['type'],
									'quantity'                => $option_value_query->row['quantity'],
									'subtract'                => $option_value_query->row['subtract'],
									'price'                   => $option_value_query->row['price'],
									'price_prefix'            => $option_value_query->row['price_prefix'],
									'points'                  => $option_value_query->row['points'],
									'points_prefix'           => $option_value_query->row['points_prefix'],
									'weight'                  => $option_value_query->row['weight'],
									'weight_prefix'           => $option_value_query->row['weight_prefix']
								);
							}
						} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
							foreach ($value as $product_option_value_id) {
								$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$language_id . "'");

								if ($option_value_query->num_rows) {
									if ($option_value_query->row['price_prefix'] == '+') {
										$option_price += $option_value_query->row['price'];
									} elseif ($option_value_query->row['price_prefix'] == '-') {
										$option_price -= $option_value_query->row['price'];
									}

									if ($option_value_query->row['points_prefix'] == '+') {
										$option_points += $option_value_query->row['points'];
									} elseif ($option_value_query->row['points_prefix'] == '-') {
										$option_points -= $option_value_query->row['points'];
									}

									if ($option_value_query->row['weight_prefix'] == '+') {
										$option_weight += $option_value_query->row['weight'];
									} elseif ($option_value_query->row['weight_prefix'] == '-') {
										$option_weight -= $option_value_query->row['weight'];
									}

									if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $cart['quantity']))) {
										$stock = false;
									}

									$option_data[] = array(
										'product_option_id'       => $product_option_id,
										'product_option_value_id' => $product_option_value_id,
										'option_id'               => $option_query->row['option_id'],
										'option_value_id'         => $option_value_query->row['option_value_id'],
										'name'                    => $option_query->row['name'],
										'value'                   => $option_value_query->row['name'],
										'type'                    => $option_query->row['type'],
										'quantity'                => $option_value_query->row['quantity'],
										'subtract'                => $option_value_query->row['subtract'],
										'price'                   => $option_value_query->row['price'],
										'price_prefix'            => $option_value_query->row['price_prefix'],
										'points'                  => $option_value_query->row['points'],
										'points_prefix'           => $option_value_query->row['points_prefix'],
										'weight'                  => $option_value_query->row['weight'],
										'weight_prefix'           => $option_value_query->row['weight_prefix']
									);
								}
							}
						} elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
							$option_data[] = array(
								'product_option_id'       => $product_option_id,
								'product_option_value_id' => '',
								'option_id'               => $option_query->row['option_id'],
								'option_value_id'         => '',
								'name'                    => $option_query->row['name'],
								'value'                   => $value,
								'type'                    => $option_query->row['type'],
								'quantity'                => '',
								'subtract'                => '',
								'price'                   => '',
								'price_prefix'            => '',
								'points'                  => '',
								'points_prefix'           => '',
								'weight'                  => '',
								'weight_prefix'           => ''
							);
						}
					}
				}

				$price = $product_query->row['price'];

				// Product Discounts
				$discount_quantity = 0;

				foreach ($cart_query->rows as $cart_2) {
					if ($cart_2['product_id'] == $cart['product_id']) {
						$discount_quantity += $cart_2['quantity'];
					}
				}

				$product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity <= '" . (int)$discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

				if ($product_discount_query->num_rows) {
					$price = $product_discount_query->row['price'];
				}

				// Product Specials
				$product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

				if ($product_special_query->num_rows) {
					$price = $product_special_query->row['price'];
				}

				// Reward Points
				$product_reward_query = $this->db->query("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

				if ($product_reward_query->num_rows) {
					$reward = $product_reward_query->row['points'];
				} else {
					$reward = 0;
				}


				// Stock
				if (!$product_query->row['quantity'] || ($product_query->row['quantity'] < $cart['quantity'])) {
					$stock = false;
				}

				$product_data[] = array(
					'cart_id'         => $cart['cart_id'],
					'product_id'      => $product_query->row['product_id'],
					'name'            => $product_query->row['name'],
					'model'           => $product_query->row['model'],
					'href'			  => $this->url->link('product/product', 'product_id='.$product_query->row['product_id']. html_entity_decode($this->config->get('hb_cart_campaign'), ENT_QUOTES, 'UTF-8')),
					'shipping'        => $product_query->row['shipping'],
					'image'           => $this->model_tool_image->resize($product_query->row['image'], $image_dimension['w'], $image_dimension['h']),
					'option'          => $option_data,
					'quantity'        => $cart['quantity'],
					'stock'           => $stock,
					'price'           => $this->currency->format($this->tax->calculate(($price + $option_price), $product_query->row['tax_class_id'], $this->config->get('config_tax')), $cart['currency']), 
					'total_value'     => $this->tax->calculate((($price + $option_price) * $cart['quantity']), $product_query->row['tax_class_id'], $this->config->get('config_tax')),
					'total'           => $this->currency->format($this->tax->calculate((($price + $option_price) * $cart['quantity']), $product_query->row['tax_class_id'], $this->config->get('config_tax')), $cart['currency']),
					'reward'          => $reward * $cart['quantity'],
					'points'          => ($product_query->row['points'] ? ($product_query->row['points'] + $option_points) * $cart['quantity'] : 0),
					'tax_class_id'    => $product_query->row['tax_class_id']
				);
			}
		}

		return $product_data;
	}

	public function encrypt($key, $value) {
		$method = 'AES-128-CBC';
		$iv_length = openssl_cipher_iv_length($method); // 16
		$iv = openssl_random_pseudo_bytes($iv_length);
		$encrypted = openssl_encrypt($value, $method, hash('sha256', $key, true), OPENSSL_RAW_DATA, $iv);
		$encoded = base64_encode($encrypted) . '|' . base64_encode($iv);
		return strtr($encoded, '+/=', '-_,');
	}
	
	public function decrypt($key, $value) {
		$value = explode('|', strtr($value, '-_,', '+/=') . '|');
		$decoded = base64_decode($value[0]);
		$iv = base64_decode($value[1]);
		$method = 'AES-128-CBC';
		$decrypted = trim(openssl_decrypt($decoded, $method, hash('sha256', $key, true), OPENSSL_RAW_DATA, $iv));
		return $decrypted;
	}

	public function generate_coupon($extn_info, $coupon_rule, $total_cart_value, $products, $cart_id){
		$coupon_data['coupon'] 			= '';
		$coupon_data['coupon_expiry'] 	= '';
		$coupon_data['coupon_discount'] = '';
		$coupon_data['coupon_minimum']	= '';

		if ($coupon_rule == 1) {
			$hb_cart_coupon_rules 		= isset($extn_info['hb_cart_coupon_rules'])?$extn_info['hb_cart_coupon_rules']:'0:10:p';
			$hb_cart_coupon_validity 		= isset($extn_info['hb_cart_coupon_validity'])?$extn_info['hb_cart_coupon_validity']:'1';
		}else{
			$hb_cart_coupon_rules 		= isset($extn_info['hb_cart_coupon_2_rules'])?$extn_info['hb_cart_coupon_2_rules']:'0:15:p';
			$hb_cart_coupon_validity 		= isset($extn_info['hb_cart_coupon2_validity'])?$extn_info['hb_cart_coupon2_validity']:'2';
		}

		$hb_cart_coupon_rules = explode(',',$hb_cart_coupon_rules);

		$discount = 0;
		foreach ($hb_cart_coupon_rules as $rule) {
			$format = $rule;
			$format = explode(':',$format);
			
			if ($total_cart_value < $format[0]) {
				break;
			}

			$cart_value = $format[0];
			$discount 	= $format[1];
			$type 		= strtoupper($format[2]);
		}
		
		if ($discount > 0) {
			$hb_cart_coupon_products 		= isset($extn_info['hb_cart_coupon_products'])? true: false;
			$hb_cart_coupon_freeshipping 	= isset($extn_info['hb_cart_coupon_freeshipping'])? 1: 0;

			$coupon_code = $this->new_coupon_code();

			$name 			= 'HB Cart Coupon - '.$cart_id;
			$logged 		= 0;
			$shipping 		= $hb_cart_coupon_freeshipping;
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "coupon SET name = '" . $this->db->escape($name) . "', code = '" . $this->db->escape($coupon_code) . "', discount = '" . (float)$discount . "', type = '" . $this->db->escape($type) . "', total = '" . (float)$cart_value . "', logged = '" . (int)$logged . "', shipping = '" . (int)$shipping . "', date_start = DATE_SUB(NOW(), INTERVAL 1 DAY), date_end = DATE_ADD(NOW(), INTERVAL " . (int)$hb_cart_coupon_validity . " DAY), uses_total = '1', uses_customer = '1', status = '1', date_added = NOW()");
		
			$coupon_id = $this->db->getLastId();
		
			if (!empty($products) && $hb_cart_coupon_products) {
				foreach ($products as $product) {
					$this->db->query("INSERT INTO ".DB_PREFIX."coupon_product (coupon_id, product_id) VALUES ('".(int)$coupon_id."','".(int)$product['product_id']."')");
				}
			}
			
			$coupon_discount = ($type == 'P')? $discount.'%': $this->currency->format($discount, $this->config->get('config_currency'));

			$coupon_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "coupon WHERE coupon_id = '".(int)$coupon_id."' LIMIT 1");
			if ($coupon_query->row){
				$date_end = $coupon_query->row['date_end'];
			}

			$coupon_data['coupon'] 			= $coupon_code;
			$coupon_data['coupon_expiry'] 	= date('d-m-Y', strtotime($date_end));
			$coupon_data['coupon_discount'] = $coupon_discount;
			$coupon_data['coupon_minimum']	= $this->currency->format($cart_value, $this->config->get('config_currency'));
		}

		return $coupon_data;
	}
	
	public function new_coupon_code(){
		$length = 10; //code length
		do {
			$token = "";
			$codeAlphabet = "ABCDEFGHIJKLMNPQRSTUVWXYZ";
			//$codeAlphabet.= "abcdefghijklmnpqrstuvwxyz";
			$codeAlphabet.= "0123456789";
			$max = strlen($codeAlphabet); 
			
			for ($i=0; $i < $length; $i++) {
				$token .= $codeAlphabet[rand(0, $max-1)];
			}
			
			$new_code = $token;
		
		} while ($this->isNotUniqueCode($new_code) === true);
		
		return $new_code;
	}
	
	public function isNotUniqueCode($code){
		$validate =  $this->db->query("SELECT count(*) as total FROM " . DB_PREFIX . "coupon WHERE `code` = '".$this->db->escape($code)."'");
		if ($validate->row['total'] > 0) {
			return true;
		}else{
			return false;
		}
	}

	public function delete_expired_coupon(){
		$this->db->query("DELETE FROM `".DB_PREFIX."coupon` WHERE date_end < now() - interval 2 day");
	}

	public function addlog($text = ''){
		if ($this->config->get('hb_cart_log_enable')){
			if (!file_exists(DIR_LOGS . 'huntbee_abandoned_cart_logs')) {
				mkdir(DIR_LOGS . 'huntbee_abandoned_cart_logs', 0777, true);
			}

			$file = DIR_LOGS . 'huntbee_abandoned_cart_logs/abandoned_cart_logs.txt';

			if (file_exists($file)) {
				$size = filesize($file);
				if ($size > 5242880){
					$handle = fopen($file, 'w+');
					fclose($handle);
				}
			}

			$fp = fopen($file, 'a');
			fwrite($fp, "\r\n".date('d-M-Y G:i:s A') . ' - ' .$text);
			fclose($fp);
		}
	}

	public function isExtensionInstalled($code){
		$query = $this->db->query("SELECT count(*) as total FROM `".DB_PREFIX."extension` WHERE `code` = '".$this->db->escape($code)."'");	
		if ($query->row['total'] > 0){
			return true;
		}else{
			return false;
		}
	}

	public function getStore($store_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");
		return $query->row;
	}

	public function getTemplateData($template_id){
		$template['email_content'] = 'Template content not set!';
		$results = $this->db->query("SELECT * FROM `".DB_PREFIX."hb_build_template` WHERE `id` = '".(int)$template_id."'");
		$template_data = $results->row;
		
		if (!empty($template_data)) {
			//GET DATA
			$store_id 			= $template_data['store_id'];
			$draft_head 		= $template_data['draft_head'];
			$draft_body 		= $template_data['draft_body'];
			$loaded_template_id = $template_data['loaded_template_id'];
			$editor				= $template_data['editor'];
			
			$current_date 		= date('d-M-Y');
			$draft_body 		= str_replace('{server_date}',$current_date,$draft_body);
			
			$email_options 			= json_decode($template_data['email_options'], true);
			
			$this->load->model('setting/setting');
			$store_config 		= $this->model_setting_setting->getSetting('config', $store_id);
			$config_currency 	= isset($store_config['config_currency']) ? $store_config['config_currency'] : $this->config->get('config_currency');
			$config_tax 		= isset($store_config['config_tax']) ? $store_config['config_tax'] : $this->config->get('config_tax');
			$config_review_status = isset($store_config['config_review_status']) ? $store_config['config_review_status'] : $this->config->get('config_review_status');
			$config_seo_url 		= isset($store_config['config_seo_url']) ? $store_config['config_seo_url'] : $this->config->get('config_seo_url');
			
			$store_name 	= isset($store_config['config_name']) ? $store_config['config_name'] : $this->config->get('config_name');
			$store_ssl 		= isset($store_config['config_url']) ? $store_config['config_url'] : HTTPS_SERVER;
			$store_email 	= isset($store_config['config_email']) ? $store_config['config_email'] : $this->config->get('config_email');

			$draft_body 		= str_replace('{store_email}',$store_email,$draft_body);			
			$draft_body 		= str_replace('{store_name}',$store_name,$draft_body);
			$draft_body 		= str_replace('{store_url}',$store_ssl,$draft_body);
			
			$allowed_store_config = array('config_meta_title','config_meta_description','config_meta_keyword','config_name','config_owner','config_address','config_geocode','config_email','config_telephone','config_fax','config_image','config_open','config_comment');
			foreach ($store_config as $key => $value) {
				if (!is_array($value) & in_array($key,$allowed_store_config)) {
					$draft_body      					= str_replace('{'.$key.'}',$value,$draft_body);
					$draft_head      					= str_replace('{'.$key.'}',$value,$draft_head);
					$email_options['email_subject']     = str_replace('{'.$key.'}',$value,$email_options['email_subject']);
				}
			}
			
			$content = $draft_body;

			$template['store_id']      = $store_id;
			
			if ($email_options) {
				$content = str_replace('{email_subject}', $email_options['email_subject'], $content);
				$template['email_content'] = $content;
				$template['email_options'] = $email_options;
				$template['label']         = $template_data['template_label'];
			}
			
		}	
		return $template;
	}

	public function sendemail($data){
		error_reporting(0);
		$result = false;
		try{
			if (version_compare(VERSION,'2.0.1.1','<=' )) {
				$mail = new Mail($this->config->get('config_mail'));
				$mail->protocol = $this->config->get('config_mail_protocol');
			}else {
				if (version_compare(VERSION,'2.3.0.2','>' )) {
					$mail = new Mail($this->config->get('config_mail_engine'));
				}else{
					$mail = new Mail();
					$mail->protocol = $this->config->get('config_mail_protocol');
				}				
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');			
			}
						
			$mail->setTo($data['to']);
			$mail->setFrom($data['from']);
			$mail->setSender($data['store_name']);
			if ($data['email_replyto']){
				$mail->setReplyTo($data['email_replyto']);
			}
			$mail->setSubject(html_entity_decode($data['subject'], ENT_QUOTES, 'UTF-8'));
			$mail->setHtml(wordwrap($data['content'],50));

			$mail->send();
			if (!empty($data['bcc'])){
				$bccs = explode(',',$data['bcc']);
				foreach ($bccs as $bcc) {
					$mail->setTo($bcc);
					$mail->send();
				}
			}
			$result = 'Email sent to '.$data['to'];
		}catch (Exception $e){
			$this->addlog('Email failed sending to '.$data['to'].'. Issue: '.$e->getMessage());
			$result	= false;
		}

		return $result;
	}

}
?>