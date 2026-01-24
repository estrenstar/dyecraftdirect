<?php  
class ControllerExtensionModuleHbCart extends Controller {
	protected $registry;

	public function __construct($registry) {
		$this->registry = $registry;
		if (version_compare(VERSION,'2.2.0.0','<' )) {
			$this->user = new User($this->registry);
		}else{
			$this->user = new Cart\User($this->registry);
		}
	}

	public function preview(){
		$this->load->model('extension/module/hb_cart');
		
		if (isset($this->request->get['template_id'])) {
			$template_id = (int)$this->request->get['template_id'];
		}else{
			$template_id = '0';
		}
		
		if ($this->user->isLogged()) {
			$query = $this->db->query("SELECT  * FROM `".DB_PREFIX."hb_cart` GROUP BY session_id ORDER BY RAND() LIMIT 1");
			if ($query->row){		
				$compose = array(
					'customer_id' 		=> $query->row['customer_id'],
					'session_id'		=> $query->row['session_id'],
					'template_id'		=> $template_id,
					'reminder'			=> false,
					'preview'			=> true
				);

				$email_data = $this->model_extension_module_hb_cart->compose_abandoned_cart_email($compose);

				$result = isset($email_data['content']) ? $email_data['content'] : 'No Email Content';
			}else{
				$result = 'NO ELIGIBLE CART FOUND FOR PREVIEW';
			}

			echo $result;
		}else{
			die('You are not authorized to view this!');
		}
	}	


	public function admin_send(){	
		if (!$this->user->isLogged()) {
			die('Invalid User');
		}	
		
		$this->load->model('extension/module/hb_cart');
		
		$session_id = $this->request->get['session_id'];
		
		if (isset($this->request->get['store_id'])) {
			$store_id = (int)$this->request->get['store_id'];
		}else{
			$store_id = 0;
		}
		
		if (isset($this->request->get['customer_id'])) {
			$customer_id = (int)$this->request->get['customer_id'];
		}else{
			$customer_id = 0;
		}
		
		if (isset($this->request->get['reminder'])) {
			$reminder = (int)$this->request->get['reminder'];
		}else{
			$reminder = 0;
		}
		
		if (isset($this->request->get['preview'])) {
			$preview = true;
		}else{
			$preview = false;
		}

		$compose = array(
			'customer_id' 		=> $customer_id,
			'session_id'		=> $session_id,
			'reminder'			=> $reminder,
			'preview'			=> false,
			'send_preview'		=> $preview
		);
		
		$this->load->model('setting/setting');
		$extn_info = $this->model_setting_setting->getSetting('hb_cart', (int)$store_id);
		$hb_cart_email_guest = isset($extn_info['hb_cart_email_guest'])? $extn_info['hb_cart_email_guest']: false;
				
		if ($preview === true){
			$email_data = $this->model_extension_module_hb_cart->compose_abandoned_cart_email($compose);
			echo '<div style="text-align:center;background-color: #777;padding: 10px;color: #fff;" title="Email Subject">'.$email_data['subject'].'</div>';
			echo $email_data['content'];
		}else{
			if ($customer_id == 0 and $hb_cart_email_guest == 1) {
				$json['warning'] = 'This operation is aborted because you have chosen not to send email to guest customer.';
			}else{
				$this->model_extension_module_hb_cart->compose_abandoned_cart_email($compose);
				$json['success'] = 'Email sent successfully';
			}
			$this->response->setOutput(json_encode($json));
		}
	}

	public function popup(){
		$store_id = $this->config->get('config_store_id');
		
		$data['exit_intent'] 	= false;
		$data['html'] 			= '';
		
		if ($this->config->get('hb_cart_exit_pop') and ($this->cart->hasProducts())) {
			$data['exit_intent'] 		= true;
			$data['html'] 	= html_entity_decode($this->config->get('hb_cart_exit_intent_block'.$this->config->get('config_language_id')), ENT_QUOTES, 'UTF-8');
		}
		
		if (version_compare(VERSION,'2.2.0.0','<')){
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/module/'.TEMPLATE_FOLDER.'/hb_cart_popup.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/extension/module/'.TEMPLATE_FOLDER.'/hb_cart_popup.tpl', $data);
			} else {
				return $this->load->view('default/template/extension/module/'.TEMPLATE_FOLDER.'/hb_cart_popup.tpl', $data);
			}
		}else{
			return $this->load->view('extension/module/'.TEMPLATE_FOLDER.'/hb_cart_popup', $data);
		}
	}

	public function unsubscribe(){
		$this->load->model('extension/module/hb_cart');
		
		$this->load->language('extension/module/hb_cart');

		$store_id = (int)$this->config->get('config_store_id');
		
		if (isset($this->request->get['email'])) {
			$email = $this->request->get['email'];
		}else{
			$email = '';
		}
		
		if (isset($this->request->get['key'])) {
			$authkey = urldecode($this->request->get['key']);
		}else{
			$authkey = 0;
		}
		
		$this->load->model('setting/setting');
		$extn_info = $this->model_setting_setting->getSetting('hb_cart', (int)$store_id);
		
		$hb_cart_log_id = $this->model_extension_module_hb_cart->decrypt($extn_info['hb_cart_pass_key'], $authkey);	
		
		$data['heading_title'] = $this->language->get('text_unsubscribe_page_heading');
		
		
		$this->document->setTitle($data['heading_title']);
		
		$this->load->language('account/newsletter');
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_newsletter'),
			'href' => $this->url->link('account/newsletter', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_unsubscribe_page_heading'),
			'href' => $this->url->link('extension/module/hb_cart/unsubscribe', '', true)
		);
		
		$data['button_continue'] = $this->language->get('button_continue');
		
		$data['text_message'] = $this->language->get('text_invalid_request');
		
		$result = $this->db->query("SELECT * FROM `".DB_PREFIX."hb_cart_log` WHERE `id` = '".(int)$hb_cart_log_id."' AND `email` = '".$this->db->escape($email)."'");
		if (($result->num_rows > 0) and $email <> '') {
			if ($this->model_extension_module_hb_cart->is_unsubscribed($email) == false) {
				$this->model_extension_module_hb_cart->unsubscribe($email, $store_id);
				$data['text_message'] = $this->language->get('text_unsubscribe_success');
			}else{
				$data['text_message'] = $this->language->get('text_unsubscribe_already');
			}
		}
				
		$data['continue'] = $this->url->link('common/home');
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		if (version_compare(VERSION,'2.2.0.0','<')){
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/success.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/common/success.tpl', $data));
			}
		}else{
			$this->response->setOutput($this->load->view('common/success', $data));
		}
	}

	public function checkout(){
		$this->load->model('extension/module/hb_cart');
		
		$store_id = $this->config->get('config_store_id');
		
		if (isset($this->request->get['key1'])) {
			$hb_cart_log_id = urldecode($this->request->get['key1']);
		}else{
			$hb_cart_log_id = '';
		}
		
		if (isset($this->request->get['key2'])) {
			$customer_id = urldecode($this->request->get['key2']);
		}else{
			$customer_id = 0;
		}
		
		$this->load->model('setting/setting');
		$extn_info = $this->model_setting_setting->getSetting('hb_cart', (int)$store_id);
		
		$hb_cart_log_id = $this->model_extension_module_hb_cart->decrypt($extn_info['hb_cart_pass_key'], $hb_cart_log_id);	
		$customer_id = $this->model_extension_module_hb_cart->decrypt($extn_info['hb_cart_pass_key'], $customer_id);
		
		$validate = $this->db->query("SELECT * FROM `".DB_PREFIX."hb_cart_log` WHERE id = '".(int)$hb_cart_log_id."' AND customer_id = '".(int)$customer_id."' LIMIT 1");
		if ($validate->row) {
			$this->session->data['hb_cart_log_id'] = $hb_cart_log_id;
			
			if ($this->cart->hasProducts()){
				$this->response->redirect($this->url->link('checkout/checkout', '', true));
			}else{
				if ($this->customer->isLogged()) {
					$this->response->redirect($this->url->link('checkout/checkout', '', true));
				}else{
					$customer_email = false;
					if ($customer_id > 0 ) {
						$customer_email = $this->db->query("SELECT email FROM `".DB_PREFIX."customer` WHERE customer_id = '".(int)$customer_id."' LIMIT 1");
						if ($customer_email->row) {
							$customer_email = $customer_email->row['email'];
							$this->customer->login($customer_email,'',true);
							$this->response->redirect($this->url->link('checkout/checkout', '', true));
						}
					}
					if ($customer_email == false or $customer_id == 0) {
						$cart_data = json_decode($validate->row['cart_data'],true);
						foreach ($cart_data as $cart) {
							$this->cart->add($cart['product_id'], $cart['quantity'], $cart['option'], $cart['recurring_id']);
							$this->response->redirect($this->url->link('checkout/checkout', '', true));
						}
					}
				}
			}
		}
		
		$this->response->redirect($this->url->link('checkout/checkout', '', true));
	}

	public function email_viewed(){
		if (isset($this->request->get['id'])) {
			header("Content-Type: image/png"); // it will return image 
            readfile("image/catalog/transparent.png");
			
			$id = (int)$this->request->get['id'];
			
			$this->db->query("UPDATE `".DB_PREFIX."hb_cart_log` SET `email_viewed` = 1 WHERE id = '".(int)$id."'");
			$this->db->query("UPDATE `".DB_PREFIX."hb_cart` SET `email_viewed` = 1 WHERE `hb_cart_log_id` = '".(int)$id."'");
		}
	}

	public function runcron(){
		if (!$this->authenticate()) {
			die('Invalid User');
		}
		
		$this->load->model('extension/module/hb_cart');
		
		if (isset($this->request->get['store_id'])) {
			$store_id = (int)$this->request->get['store_id'];
		}else{
			$store_id = (int)$this->config->get('config_store_id');
		}
		
		if ($this->config->get('hb_cart_status') and $this->config->get('hb_cart_enable')) {
			$this->model_extension_module_hb_cart->addlog("Cron Job Triggered");
			$this->model_extension_module_hb_cart->runscript($store_id);
		}
	}

	private function authenticate() {
		$this->load->model('extension/module/hb_cart');
		$passkey 		= (isset($this->request->get['passkey']))? $this->request->get['passkey'] : '';
		$db_passkey 	= $this->config->get('hb_cart_pass_key');
		if ($passkey == $db_passkey) {
			return TRUE;
		} else {
			$this->model_extension_module_hb_cart->addlog("Incorrect Pass Key: ".$passkey);
			return FALSE;
		}	
	}	

}
?>