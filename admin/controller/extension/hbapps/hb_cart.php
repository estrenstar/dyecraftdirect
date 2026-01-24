<?php

define('EXTENSION_VERSION','3.1.8');

class ControllerExtensionHbappsHbCart extends Controller {
	
	protected $registry;
	private $error = array(); 
	
	public function __construct($registry) {
		$this->registry = $registry;
		if (version_compare(VERSION,'3.0.0.0','>=' )) {
			$this->hb_template_folder 		= 'oc3';
			$this->hb_extension_base 		= 'marketplace/extension';
			$this->hb_token_name 			= 'user_token';
			$this->hb_template_extension 	= '';
			$this->hb_extension_route 		= 'extension/hbapps';
		}else if (version_compare(VERSION,'2.2.0.0','<=' )) {
			$this->hb_template_folder 		= 'oc2';
			$this->hb_extension_base 		= 'extension/hbapps';
			$this->hb_token_name 			= 'token';
			$this->hb_template_extension 	= '.tpl';
			$this->hb_extension_route 		= 'hbapps';
		}else{
			$this->hb_template_folder 		= 'oc2';
			$this->hb_extension_base 		= 'extension/extension';
			$this->hb_token_name 			= 'token';
			$this->hb_template_extension 	= '';
			$this->hb_extension_route 		= 'extension/hbapps';
		}

		if (!isset($_SESSION))  { 
			session_start(); 
		} 
		$_SESSION["hbfm_access_key"]  	= $this->session->data[$this->hb_token_name];
		$_SESSION["hbfm_store_url"]		= HTTPS_CATALOG;

		$this->load->model('extension/hbapps/hb_cart');
		
		$this->load->language($this->hb_extension_route.'/hb_cart');

		$this->email_type_id = 7;
	}

	public function index() {   
		$data['extension_version'] =  EXTENSION_VERSION;
		
		if (isset($this->request->get['store_id'])){
			$data['store_id'] = (int)$this->request->get['store_id'];
		}else{
			$data['store_id'] = 0;
		}
		
		if (!isset($this->session->data['currency'])){
			$this->session->data['currency'] = $this->config->get('config_currency');
		}
		
		$this->load->language($this->hb_extension_route.'/hb_cart');
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();
		
		$this->load->model('setting/setting');
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_cart', $this->request->post, $this->request->get['store_id']);	
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_cart', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true));
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		
		$text_strings = array(
				'heading_title','text_extension',
				'tab_customer','tab_guest', 'tab_unidentified', 'tab_graph','tab_setup','tab_log','tab_unsubscriber', 'tab_template',
				'tab_setting_basic','tab_setting_template','tab_setting_sms','tab_setting_coupon','tab_setting_popup','tab_setting_cron','tab_setting_analytics','tab_admin_setting',
				'text_search_label','text_list_columns',
				'text_subject','text_body','text_enable_extension','text_enable_reminder','text_number_reminder','text_order_status','text_show_records','text_trigger','text_reminder_gap','text_passkey','text_enable_log','text_image_dimension','text_keep_guest_cart','text_email_guest','text_status',
				'text_cron_command', 'text_cron_command_instruction',
				'text_remove_outstock_products','text_first_template','text_reminder_template','text_auto_coupon','text_delete_coupon','text_coupon_rule','text_coupon_validity','text_coupon_products','text_coupon_freeshipping','text_campaign',
				'text_sms_template1','text_sms_template2','text_optional_apps','text_et_connected','text_install_et','text_sms_connected','text_install_sms',
				'button_save', 'button_cancel', 'button_create_template', 'button_clear_logs',
		);
		
		foreach ($text_strings as $text) {
			$data[$text] = $this->language->get($text);
		}
		
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true)
   		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbapps', true)
		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link($this->hb_extension_route.'/hb_cart', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true)
   		);
		
		$data['action'] = $this->url->link($this->hb_extension_route.'/hb_cart', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true);
		$data['cancel'] 				= $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbapps', true);
		
		$data['create_template'] 	= $this->url->link($this->hb_extension_route.'/hb_cart/create_template', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true);
		$data['preview_link']		= HTTPS_CATALOG.'index.php?route=extension/module/hb_cart/preview';
		$data['preview_link'] 		= html_entity_decode($data['preview_link'], ENT_QUOTES, 'UTF-8');
		$data['clear'] 				= $this->url->link($this->hb_extension_route.'/hb_cart/clear_logs', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true);
		
		$data[$this->hb_token_name] 	= $this->session->data[$this->hb_token_name];
		$data['base_route'] 			= $this->hb_extension_route;
		
		$extn_info = $this->model_setting_setting->getSetting('hb_cart', $data['store_id']);		
	
		//SETUP
		$data['email_templates'] = $this->model_extension_hbapps_hb_cart->getEmailTemplates($this->email_type_id, $data['store_id']);

		//basic
		$data['hb_cart_status'] 			= isset($extn_info['hb_cart_status'])?$extn_info['hb_cart_status']:'';
		$data['hb_cart_enable'] 			= isset($extn_info['hb_cart_enable'])?$extn_info['hb_cart_enable']:'';
		$data['hb_cart_webrun_enable'] 		= isset($extn_info['hb_cart_webrun_enable'])?$extn_info['hb_cart_webrun_enable']:'';
		$data['hb_cart_log_enable'] 		= isset($extn_info['hb_cart_log_enable'])?$extn_info['hb_cart_log_enable']:'';
		$data['hb_cart_remove_oos_product'] 			= isset($extn_info['hb_cart_remove_oos_product'])?$extn_info['hb_cart_remove_oos_product']:'';

		$data['hb_cart_clear_guest'] 		= isset($extn_info['hb_cart_clear_guest'])?$extn_info['hb_cart_clear_guest']:'INTERVAL 15 DAY';
		$data['hb_cart_inactive_guest'] 	= isset($extn_info['hb_cart_inactive_guest'])?$extn_info['hb_cart_inactive_guest']:'6';

		$data['hb_cart_complete_status'] 	= isset($extn_info['hb_cart_complete_status'])?$extn_info['hb_cart_complete_status']: array();

		$data['hb_cart_ip_matching'] 		= isset($extn_info['hb_cart_ip_matching'])?$extn_info['hb_cart_ip_matching']:'';

		//email template
		$data['hb_cart_trigger'] 			= isset($extn_info['hb_cart_trigger'])?$extn_info['hb_cart_trigger']:'6';
		$data['hb_cart_enable_reminder'] 	= isset($extn_info['hb_cart_enable_reminder'])?$extn_info['hb_cart_enable_reminder']:'';
		$data['hb_cart_reminder_count'] 	= isset($extn_info['hb_cart_reminder_count'])?$extn_info['hb_cart_reminder_count']:'1';
		$data['hb_cart_reminder_gap'] 		= isset($extn_info['hb_cart_reminder_gap'])?$extn_info['hb_cart_reminder_gap']:'1';
		$data['hb_cart_email_guest'] 		= isset($extn_info['hb_cart_email_guest'])?$extn_info['hb_cart_email_guest']:'';

		$data['hb_cart_img_width_first'] 		= isset($extn_info['hb_cart_img_width_first'])?$extn_info['hb_cart_img_width_first']:'80';
		$data['hb_cart_img_height_first'] 		= isset($extn_info['hb_cart_img_height_first'])?$extn_info['hb_cart_img_height_first']:'80';

		$data['hb_cart_img_width_reminder'] 		= isset($extn_info['hb_cart_img_width_reminder'])?$extn_info['hb_cart_img_width_reminder']:'80';
		$data['hb_cart_img_height_reminder'] 		= isset($extn_info['hb_cart_img_height_reminder'])?$extn_info['hb_cart_img_height_reminder']:'80';

		//sms template
		$data['hb_cart_sms_first_status'] 			= isset($extn_info['hb_cart_sms_first_status'])?$extn_info['hb_cart_sms_first_status']:'';
		$data['hb_cart_sms_reminder_status'] 		= isset($extn_info['hb_cart_sms_reminder_status'])?$extn_info['hb_cart_sms_reminder_status']:'';

		//coupon
		$data['hb_cart_coupon'] 				= isset($extn_info['hb_cart_coupon'])?$extn_info['hb_cart_coupon']:'';
		$data['hb_cart_coupon_delete'] 			= isset($extn_info['hb_cart_coupon_delete'])?$extn_info['hb_cart_coupon_delete']:'';
		$data['hb_cart_coupon_products'] 		= isset($extn_info['hb_cart_coupon_products'])?$extn_info['hb_cart_coupon_products']:'';
		$data['hb_cart_coupon_freeshipping'] 	= isset($extn_info['hb_cart_coupon_freeshipping'])?$extn_info['hb_cart_coupon_freeshipping']:'';
		$data['hb_cart_coupon_rules'] 			= isset($extn_info['hb_cart_coupon_rules'])?$extn_info['hb_cart_coupon_rules']:'0:10:p';
		$data['hb_cart_coupon_2_rules'] 		= isset($extn_info['hb_cart_coupon_2_rules'])?$extn_info['hb_cart_coupon_2_rules']:'0:15:p';
		$data['hb_cart_coupon_validity'] 		= isset($extn_info['hb_cart_coupon_validity'])?$extn_info['hb_cart_coupon_validity']:'1';
		$data['hb_cart_coupon2_validity'] 		= isset($extn_info['hb_cart_coupon2_validity'])?$extn_info['hb_cart_coupon2_validity']:'2';

		//exit intent
		$data['hb_cart_exit_pop'] 			= isset($extn_info['hb_cart_exit_pop'])?$extn_info['hb_cart_exit_pop']:'';
		$data['hb_cart_magnific_lib'] 		= isset($extn_info['hb_cart_magnific_lib'])?$extn_info['hb_cart_magnific_lib']:'';

		//cron
		$data['hb_cart_pass_key'] 			= isset($extn_info['hb_cart_pass_key'])?$extn_info['hb_cart_pass_key']: md5(rand());

		//analytics		
		$data['hb_cart_campaign'] 			= isset($extn_info['hb_cart_campaign'])?$extn_info['hb_cart_campaign']:'&utm_source=huntbee_extension&utm_medium=email&utm_campaign=hb_abandoned_cart_email';

		//language based feilds
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		foreach ($data['languages'] as $language){
			$language_id = $language['language_id'];

			$data['hb_cart_email_first'][$language_id] = isset($extn_info['hb_cart_email_first'.$language_id]) ? $extn_info['hb_cart_email_first'.$language_id]: '0';
			$data['hb_cart_email_reminder'][$language_id] = isset($extn_info['hb_cart_email_reminder'.$language_id]) ? $extn_info['hb_cart_email_reminder'.$language_id]: '0';
			
			$data['hb_cart_sms_first'][$language_id] = isset($extn_info['hb_cart_sms_first'.$language_id]) ? $extn_info['hb_cart_sms_first'.$language_id]: '{firstname}, Did you forget to checkout {product_name}';
			$data['hb_cart_sms_reminder'][$language_id] = isset($extn_info['hb_cart_sms_reminder'.$language_id]) ? $extn_info['hb_cart_sms_reminder'.$language_id]: '{firstname}, {product_name} is selling fast: Complete your purchase now.';
			
			$data['hb_cart_exit_intent_block'][$language_id] = isset($extn_info['hb_cart_exit_intent_block'.$language_id]) ? $extn_info['hb_cart_exit_intent_block'.$language_id]: '<p align="center" style="font-size:36px; color:#FF3333; line-height: 35px;"><b>Do not miss your 25% discount</b></p><br><p align="center" style="font-size:22px; line-height: 35px;">If you place your order today using the special discount code you will get 25% off <br> Hurry up! </p><br><p align="center" style="font-size:28px; line-height: 35px;">Your coupon code is:</p><br><p align="center" style="font-size:36px; color:#FF3333; line-height: 35px;"><b>CHECKOUTNOW</b></p><style>.hbcart-white-popup{background-image: url(image/catalog/hb_cart/bg-3.jpg) !important; background-size: cover;</style>';
		}

		$data['interval']	= array('INTERVAL 1 HOUR','INTERVAL 12 HOUR','INTERVAL 1 DAY','INTERVAL 4 DAY','INTERVAL 15 DAY','INTERVAL 1 MONTH','INTERVAL 3 MONTH','INTERVAL 6 MONTH');

		$cartitem_folder_path    			= DIR_CATALOG."view/theme/default/template/extension/module/".$this->hb_template_folder."/abandoned_cart_items_templates";
		$data['cartitems_template_lists'] 	= $this->model_extension_hbapps_hb_cart->simple_layout_templates($cartitem_folder_path);
		$data['hb_cart_items_template'] 	= isset($extn_info['hb_cart_items_template'])?$extn_info['hb_cart_items_template']:'';

		$reviews_folder_path    			= DIR_CATALOG."view/theme/default/template/extension/module/".$this->hb_template_folder."/abandoned_cart_reviews_templates";
		$data['reviews_template_lists'] 	= $this->model_extension_hbapps_hb_cart->simple_layout_templates($reviews_folder_path);
		$data['hb_cart_reviews_template'] 		= isset($extn_info['hb_cart_reviews_template'])?$extn_info['hb_cart_reviews_template']:'';
		
		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if ($data['store_id'] == 0){ 
			$data['store_url'] = HTTPS_CATALOG;
		}else{
			 $results = $this->model_setting_store->getStore($data['store_id']);
			 $data['store_url'] = $results['url'];
		}
		$data['hb_cart_cron'] =  'wget --quiet --delete-after "'.$data['store_url'].'index.php?route=extension/module/hb_cart/runcron&passkey='.$data['hb_cart_pass_key'].'"';
		
		//stats		
		$data['total_records'] 			= $this->model_extension_hbapps_hb_cart->getTotalLogRecords($data['store_id']);
		$data['total_responded'] 		= $this->model_extension_hbapps_hb_cart->getTotalLogRecordsResponded($data['store_id']);
		$data['total_not_responded'] 	= (int)$data['total_records'] - (int)$data['total_responded'];

		$columns = $this->model_extension_hbapps_hb_cart->table_columns();
		foreach ($columns as $column) {
			$data['hb_cart_col'][$column] 		= isset($extn_info['hb_cart_col_'.$column])?$extn_info['hb_cart_col_'.$column]:'';
			$data['show'][$column] 				= isset($extn_info['hb_cart_col_'.$column])?$extn_info['hb_cart_col_'.$column]:false;

			$data['table_columns'][] = array(
				'column_id'		=> $column,
				'column_name'	=> $this->language->get('text_'.$column),
			);
		}

		//LINK OPTIONAL APPS
		if ($this->model_extension_hbapps_hb_cart->isExtensionInstalled('email_templates')) {
			$data['et_extn_linked'] = true;
		}else{
			$data['et_extn_linked'] = false;
		}

		if ($this->model_extension_hbapps_hb_cart->isExtensionInstalled('opencart_sms')) {
			$data['sms_extn_linked'] = true;
		}else{
			$data['sms_extn_linked'] = false;
		}
							
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbapps/hb_cart/'.$this->hb_template_folder.'/hb_cart'.$this->hb_template_extension, $data));
	}

	public function logs(){
		if (!file_exists(DIR_LOGS . 'huntbee_abandoned_cart_logs')) {
			mkdir(DIR_LOGS . 'huntbee_abandoned_cart_logs', 0777, true);
		}

		$file = DIR_LOGS . 'huntbee_abandoned_cart_logs/abandoned_cart_logs.txt';
		if (file_exists($file)) {
			$data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
		}else{
			$data['log'] = '';
		}
		$this->response->setOutput($this->load->view('extension/hbapps/hb_cart/'.$this->hb_template_folder.'/hb_cart_worklog'.$this->hb_template_extension, $data));
	}

	public function clear_logs() {
		if (isset($this->request->get['store_id'])){
			$data['store_id'] = (int)$this->request->get['store_id'];
		}else{
			$data['store_id'] = 0;
		}

		if (!$this->validate()) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$file = DIR_LOGS . 'huntbee_abandoned_cart_logs/abandoned_cart_logs.txt';

			$handle = fopen($file, 'w+');

			fclose($handle);

			$this->session->data['success'] =  $this->language->get('text_success_logs');
		}

		$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_cart', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true));
	}
	
	//TEMPLATES SECTION START
	public function templates() {  	
		$store_id 	= (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id']: 0;
		$page 		= (isset($this->request->get['page'])) ? (int)$this->request->get['page']: 1;
		$search 	= (isset($this->request->get['search'])) ? $this->request->get['search']: '';
		
		$data = array(
			'start' 	=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 	=> $this->config->get('config_limit_admin'),
			'store_id'	=> $store_id,
			'email_type_id'	=> $this->email_type_id,
			'search'	=> $search
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] 		= $this->hb_extension_route;	
		
		$reports_total 		= $this->model_extension_hbapps_hb_cart->getTotalTemplates($data); 		
		$records 			= $this->model_extension_hbapps_hb_cart->getTemplates($data);
		$data['records'] 	= array();
		
		foreach ($records as $record) {
			$data['records'][] = array(
				'id' 			=> $record['id'],
				'label' 		=> $record['template_label'],
				'edit'			=> $this->url->link($this->hb_extension_route.'/hb_cart/edit_template', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&id='.$record['id'], true),
				'date_added' 	=> $record['date_added']
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_cart/templates', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$text_strings = array(
			'text_id','text_label','text_date_added','text_action','text_no_records','button_edit','button_preview','button_delete',
		);
		
		foreach ($text_strings as $text) {
			$data[$text] = $this->language->get($text);
		}

		$this->response->setOutput($this->load->view('extension/hbapps/hb_cart/'.$this->hb_template_folder.'/hb_cart_templates'.$this->hb_template_extension, $data));
	}

	public function create_template(){	
		$store_id = (isset($this->request->get['store_id']))? (int)$this->request->get['store_id']:'0';
		$last_inserted_id = $this->model_extension_hbapps_hb_cart->addTemplateInstance('NEW LABEL EN',$this->email_type_id, $store_id);
		$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_cart/edit_template', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&id='.$last_inserted_id, true));
	}
	
	public function delete_template(){
		$id = trim($this->request->post['id']);
		$this->model_extension_hbapps_hb_cart->deleteTemplate($id);
		$json['success'] = $this->language->get('text_template_deleted');
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function edit_template(){
		if (isset($this->request->get['store_id'])){
			$data['store_id'] = (int)$this->request->get['store_id'];
		}else{
			$data['store_id'] = 0;
		}
		
		if (isset($this->request->get['id'])){
			$data['template_id'] = (int)$this->request->get['id'];
		}else{
			$data['template_id'] = 0;
		}
		
		$template_id = $data['template_id'];
		
		$this->load->model('tool/image');
		
		$this->document->setTitle($this->language->get('heading_title_edit'));
		
		$text_strings = array(
				'heading_title','heading_title_edit',
				'button_short_codes','button_generate','button_refresh','button_desktop','button_tablet','button_mobile',
				'tab_email_template','tab_email_option','tab_email_preview',
				'text_label','text_email_content','text_layouts','text_width','text_color1',
				'text_color2','text_color3','text_color4','text_content_sample',
				'text_sender_name','text_sender_email','text_bcc','text_reply_to','text_subject',
				'text_email_preview',
				'button_save','button_cancel',
		);
		
		foreach ($text_strings as $text) {
			$data[$text] = $this->language->get($text);
		}
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true)
   		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=module', true)
		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link($this->hb_extension_route.'/hb_cart', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true)
   		);
		
		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title_edit'),
			'href'      => $this->url->link($this->hb_extension_route.'/hb_cart/edit_template', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&id='.$template_id, true)
   		);
				
		$data['cancel'] = $this->url->link($this->hb_extension_route.'/hb_cart', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] .'&store_id='.$data['store_id']. '&type=module', true);
		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;
		
		$data['simple_layouts'] 	= $this->model_extension_hbapps_hb_cart->simple_layout_templates('view/template/extension/hbapps/hb_cart/simple_email_layouts');
		$data['simple_contents'] 	= $this->model_extension_hbapps_hb_cart->simple_layout_templates('view/template/extension/hbapps/hb_cart/simple_email_contents');
		
		$this->load->model('setting/setting');
		$store_info = $this->model_setting_setting->getSetting('config', $data['store_id']);
		
		//get data
		$result = $this->model_extension_hbapps_hb_cart->getTemplate($template_id);
		$email_options = array();
		$data['label'] = '';
		if ($result){
			$data['label'] = $result['template_label'];
			$result['email_options'] = trim(preg_replace('/\s+/', ' ', $result['email_options']));
			$email_options = json_decode($result['email_options'], true);
		}

		$data['email_body'] 	= (isset($result['draft_body']))? html_entity_decode($result['draft_body']) : '';

		$data['email_subject'] 	= (isset($email_options['email_subject']))? $email_options['email_subject'] : 'Enter Email Subject here!!!';
		$data['sender_name'] 	= (isset($email_options['sender_name']))? $email_options['sender_name'] : $store_info['config_name'];
		$data['sender_email'] 	= (isset($email_options['sender_email']))? $email_options['sender_email'] : $store_info['config_email'];
		$data['email_replyto'] 	= (isset($email_options['email_replyto']))? $email_options['email_replyto'] : '';
		$data['email_bcc'] 		= (isset($email_options['email_bcc']))? $email_options['email_bcc'] : '';
		
		//preview
		$data['preview_link']		= HTTPS_CATALOG.'index.php?route=extension/module/hb_cart/preview&template_id='.$data['template_id'];
		$data['preview_link'] 		= html_entity_decode($data['preview_link'], ENT_QUOTES, 'UTF-8');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbapps/hb_cart/'.$this->hb_template_folder.'/hb_cart_template_edit'.$this->hb_template_extension, $data));
	}
	
	public function update_label(){
		$id 	= (int)$this->request->get['id'];
		$label 	= trim($this->request->post['label']);
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->db->query("UPDATE `" . DB_PREFIX . "hb_build_template` SET `template_label` = '" . $this->db->escape($label). "' WHERE id = '".(int)$id."'");
			$json['success'] = $this->language->get('text_template_label_updated');
		}else{
			$json['warning'] =  $this->language->get('error_permission');
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function savecontents(){
		$template_id 	= (int)$this->request->get['template_id'];
		//$draft_head 	= '<!DOCTYPE html><html><head><title>{email_subject}</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1"><meta http-equiv="X-UA-Compatible" content="IE=edge" /></head><body>';
		$draft_head		= '';
		$draft_body 	= $this->request->post['draft_body'];
	
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->db->query("UPDATE " . DB_PREFIX . "hb_build_template SET `draft_head` = '" . $this->db->escape(html_entity_decode($draft_head)). "', `draft_body` = '" . $this->db->escape(html_entity_decode($draft_body)). "', date_modified = now() WHERE `id` = '".(int)$template_id."'");
			$json['success'] = $this->language->get('text_content_saved');
		}else{
			$json['warning'] =  $this->language->get('error_permission');
		}
		
		$this->response->setOutput(json_encode($json));
	}

	public function saveemailoptions(){
		$template_id = (int)$this->request->get['template_id'];
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->db->query("UPDATE " . DB_PREFIX . "hb_build_template SET `email_options` = '" . $this->db->escape(json_encode($this->request->post)). "', date_modified = now() WHERE `id` = '".(int)$template_id."'");
			$json['success'] = $this->language->get('text_options_saved');
		}else{
			$json['warning'] =  $this->language->get('error_permission');
		}
		
		$this->response->setOutput(json_encode($json));
	}

	public function loadSimpleLayout(){
		$json['layout'] = ' ';
		
		$data = $this->request->post;
		$this->load->model('setting/setting');
		$config 			= $this->model_setting_setting->getSetting('config', (int)$data['store_id']);
		$data['logo'] 		= isset($config['config_logo'])? HTTPS_CATALOG.'image/'.$config['config_logo'] : HTTPS_CATALOG.'image/catalog/logo.png';
		$data['store_url'] 	= HTTPS_CATALOG;

		$file = 'view/template/extension/hbapps/hb_cart/simple_email_layouts/'.$data['selected_template'].'.txt';
		if (file_exists($file)) {
			$json['layout'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
			
			$content_file = 'view/template/extension/hbapps/hb_cart/simple_email_contents/'.$data['email_type'].'.txt';
			if (file_exists($file)) {
				$content = file_get_contents($content_file, FILE_USE_INCLUDE_PATH, null);
			}else{
				$content = '';
			}

			$json['layout'] = str_replace('{content}', $content, $json['layout']);
			
			foreach ($data as $key => $value){
				$json['layout'] = str_replace('{'.$key.'}',$value,$json['layout']);
			}
			
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));	
	}

	//TEMPLATES SECTION ENDS
	
	public function cartrecords() {  
		$store_id = (int)$this->request->get['store_id'];
		
		if (version_compare(VERSION,'2.1.0.0', '<')) {
			$this->load->model('sale/customer');
		}else{
			$this->load->model('customer/customer');
		}
		$this->load->model('tool/image');
		
		if (isset($this->request->get['type'])) {
			$type = $this->request->get['type'];
		} else {
			$type = 'customer';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data = array(
			'start' 	=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 	=> $this->config->get('config_limit_admin'),
			'store_id'	=> $store_id
		);
		
		$text_strings = array('column_customer_id','column_cart','column_work_log','column_action', 'text_first_email_sent', 'text_email_viewed','text_reminder','text_last_visited','text_date_active','text_date_modified','text_time_inactive');
		
		foreach ($text_strings as $text) {
			$data[$text] = $this->language->get($text);
		}

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] 		= $this->hb_extension_route;	
		
		$this->load->model('setting/setting');
		$extn_info = $this->model_setting_setting->getSetting('hb_cart', $store_id);	

		$data['hb_cart_trigger'] 			=  isset($extn_info['hb_cart_trigger'])?$extn_info['hb_cart_trigger']:'6';
		$data['hb_cart_complete_status'] 	= isset($extn_info['hb_cart_complete_status'])?$extn_info['hb_cart_complete_status']:array();

		//$abandoned_time = date('Y-m-d H:i:s', strtotime('-'.$data['hb_cart_trigger'].' hour'));
		
		$data['type'] = $type;

		if ($data['type'] == 'guest') {
			$reports_total 	= $this->model_extension_hbapps_hb_cart->getTotalGuestCarts($store_id); 		
			$results 		= $this->model_extension_hbapps_hb_cart->getGuestCarts($data);
		}elseif ($data['type'] == 'unidentified') {
			$reports_total 	= $this->model_extension_hbapps_hb_cart->getTotalUnidentifiedCarts($store_id); 		
			$results 		= $this->model_extension_hbapps_hb_cart->getUnidentifiedCarts($data);
		}else{
			$reports_total 	= $this->model_extension_hbapps_hb_cart->getTotalCustomerCarts($store_id); 		
			$results 		= $this->model_extension_hbapps_hb_cart->getCustomerCarts($data);
		}
		
		$data['records'] = array();

		foreach ($results as $result){
			$cartitems = array();
			$carts = $this->model_extension_hbapps_hb_cart->getProducts($result['session_id'], $result['customer_id'], $result['store_id']);
			foreach ($carts as $cart){
				$cartitems[] = array(
					'image' 	=>  $this->model_tool_image->resize($cart['image'],30,30),
					'quantity' 	=> $cart['quantity'],
					'option' 	=> $cart['option'],
					'name' 		=> $cart['name'],
					'model' 	=> $cart['model'],
					'total' 	=> $this->currency->format($cart['total'],$cart['currency'])
				);
			} 

			$time_query = $this->db->query("SELECT CURRENT_TIMESTAMP");
			$time1 = $time_query->row['CURRENT_TIMESTAMP'];
			$time2 = $result['date_added'];

			$inactive_time = round((strtotime($time1) - strtotime($time2)) / 60 / 60, 1);

			if ($data['type'] == 'customer' || $data['type'] == 'guest'){
				$customer_info = array();
				if ($result['customer_id'] > 0) {
					if (version_compare(VERSION,'2.1.0.0', '<')) {
						$customer_info = $this->model_sale_customer->getCustomer($result['customer_id']);
					}else{
						$customer_info = $this->model_customer_customer->getCustomer($result['customer_id']);
					}
					$customer_link = $this->url->link('customer/customer/edit', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name]. '&customer_id='. $result['customer_id'], true);
				}else{
					$customer_info 		= $this->model_extension_hbapps_hb_cart->getGuestInfo($result['session_id']);
					$customer_link		= '#';
				}
				

				$data['records'][] = array(
					'cart_id' 			=> $result['cart_id'],
					'customer_id' 		=> $result['customer_id'],
					'session_id' 		=> $result['session_id'],
					'customer_info' 	=> $customer_info,
					'customer_link'		=>$customer_link,
					'ip' 				=> $result['ip'],
					'language_id' 		=> $result['language_id'],
					'cart' 				=> $cartitems,
					'first_email' 		=> $result['first_email'],
					'email_sent' 		=> ($result['first_email'] == 1)? '<span class="label label-success"><i class="fa fa-check"></i> Sent </span>' : '',
					'reminder' 			=> $result['reminder'],
					'email_viewed' 		=> ($result['email_viewed'] == 1)? '<span class="label label-success"><i class="fa fa-check"></i> Viewed </span>' : '',
					'last_visited' 		=> $result['last_visited'],
					'is_abandoned' 		=> ($inactive_time > $data['hb_cart_trigger'])? true:false,
					'hours_inactive'	=> $inactive_time,
					'customer_purchases'	=> $this->model_extension_hbapps_hb_cart->validateCustomerPurchases($customer_info['email'],$data['hb_cart_complete_status']),
					'customer_since'		=> $this->model_extension_hbapps_hb_cart->getCustomerDays($customer_info['email']),
					'date_added' 		=> date('d-M-Y h:i a', strtotime($result['date_added'])),
					'date_modified' 	=> ($result['date_modified'] == '' || strtotime($result['date_modified']) < 1) ? '' : date('d-M-Y h:i a', strtotime($result['date_modified']))
				);
			}else{
				//UNIDENTIFIED CARTS
				$data['records'][] = array(
					'cart_id' 			=> $result['cart_id'],
					'customer_id' 		=> $result['customer_id'],
					'session_id' 		=> $result['session_id'],
					'ip' 				=> $result['ip'],
					'language_id' 		=> $result['language_id'],
					'cart' 				=> $cartitems,
					'last_visited' 		=> $result['last_visited'],
					'is_abandoned' 		=> ($inactive_time > $data['hb_cart_trigger'])? true:false,
					'hours_inactive'	=> $inactive_time,
					'date_added' 		=> date('d-M-Y h:i a', strtotime($result['date_added'])),
					'date_modified' 	=> ($result['date_modified'] == '' || strtotime($result['date_modified']) < 1) ? '' : date('d-M-Y h:i a', strtotime($result['date_modified']))
				);
			}
			
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_cart/cartrecords', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type='.$data['type'].'&store_id='.$store_id.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbapps/hb_cart/'.$this->hb_template_folder.'/hb_cart_records'.$this->hb_template_extension, $data));
	}
	
	public function unsubscriber() {  
		$store_id = (int)$this->request->get['store_id'];
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data = array(
			'start' 	=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 	=> $this->config->get('config_limit_admin'),
			'store_id'	=> $store_id
		);

		$text_strings = array('column_id','column_email','column_ip','column_date_added', 'column_action');
		
		foreach ($text_strings as $text) {
			$data[$text] = $this->language->get($text);
		}
		
		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	

		$reports_total = $this->model_extension_hbapps_hb_cart->getTotalUnsubscribe($store_id); 		
		$results = $this->model_extension_hbapps_hb_cart->getUnsubscribe($data);

		$data['records'] = array();
		foreach ($results as $result){			
			$data['records'][] = array(
				'id' 			=> $result['id'],
				'email' 		=> $result['email'],
				'ip'	 		=> $result['ip'],
				'date_added' 	=> $result['date_added']
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_cart/unsubscriber', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&store_id='.$store_id.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbapps/hb_cart/'.$this->hb_template_folder.'/hb_cart_unsubscriber'.$this->hb_template_extension, $data));
	}
	
	public function cartlogs() {  
		$store_id = (int)$this->request->get['store_id'];

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin'),
			'store_id'=> $store_id
		);
		
		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		$data['total_unique_emails'] 	= $this->model_extension_hbapps_hb_cart->getTotalLogUniqueEmails($store_id);
		$data['total_notified'] 		= $this->model_extension_hbapps_hb_cart->getTotalLogRecords($store_id);
		$data['total_reminder_email'] 	= $this->model_extension_hbapps_hb_cart->getTotalLogReminderEmail($store_id);
		$data['total_email_viewed'] 	= $this->model_extension_hbapps_hb_cart->getTotalLogEmailViewed($store_id);
		$data['total_responded'] 		= $this->model_extension_hbapps_hb_cart->getTotalLogRecordsResponded($store_id);
		$data['total_orders'] 			= $this->model_extension_hbapps_hb_cart->getTotalLogOrders($store_id);
		$data['total_sales'] 			= $this->model_extension_hbapps_hb_cart->getTotalSales($store_id);
		$data['total_sales'] 			= $this->currency->format($data['total_sales'],$this->config->get('config_currency'));	
		
		$data['conversion_rate'] = '';
		if ($data['total_notified'] <> 0){
			$data['conversion_rate']	= ($data['total_responded']/$data['total_notified']) * 100;
			$data['conversion_rate'] 	= round($data['conversion_rate']);
		}
		
		
		$reports_total = $this->model_extension_hbapps_hb_cart->getTotalCartLog($store_id); 		
		$results = $this->model_extension_hbapps_hb_cart->getCartLog($data);
		$data['records'] = array();
		foreach ($results as $result){			
			$data['records'][] = array(
				'id' 			=> $result['id'],
				'email' 		=> $result['email'],
				'first_email' 	=> ($result['first_email'] == 1)? '<span class="text-success"><i class="fa fa-check"></i></span>' : '<span class="text-danger"><i class="fa fa-times"></i></span>',
				'reminder' 		=> $result['reminder'],
				'email_viewed' 	=> ($result['email_viewed'] == 1)? '<span class="text-success"><i class="fa fa-check"></i></span>' : '<span class="text-danger"><i class="fa fa-times"></i></span>',
				'conversion' 	=> ($result['responded'] == 1)? '<span class="text-success"><i class="fa fa-check"></i></span>' : '<span class="text-danger"><i class="fa fa-times"></i></span>',
				'totals' 		=> $this->currency->format($result['totals'],$this->config->get('config_currency')),
				'order_id' 		=> $result['order_id'],
				'date_added' 	=> $result['date_added'],
				'date_modified' => $result['date_modified']
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_cart/cartlogs', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&store_id='.$store_id.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbapps/hb_cart/'.$this->hb_template_folder.'/hb_cart_email_logs'.$this->hb_template_extension, $data));
	}
	
	public function deletecart(){
		$this->db->query("DELETE FROM `" . DB_PREFIX . "hb_cart` WHERE `session_id` = '".$this->db->escape($this->request->post['session_id'])."'");
		
		if (version_compare(VERSION,'2.1.0.0', '>')) {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "cart` WHERE `session_id` = '".$this->db->escape($this->request->post['session_id'])."'");
		}
		
		$json['success'] = 'User Cart Cleared';
		$this->response->setOutput(json_encode($json));
	}
	
	public function delete_unsubscriber(){
		if (isset($this->request->post['id'])) {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "hb_email_unsubscribe` WHERE `id` = '".(int)$this->request->post['id']."'");

			$json['success'] = 'Unsubscriber Deleted';
		}else{
			$json['warning'] = 'Invalid Input';
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function chart() {
		$this->load->language('dashboard/chart');
		
		$json = array();

		$json['carts'] = array();
		$json['products'] = array();
		$json['xaxis'] = array();

		$json['carts']['label'] = 'Carts';
		$json['products']['label'] = 'Products';
		$json['carts']['data'] = array();
		$json['products']['data'] = array();

		if (isset($this->request->get['range'])) {
			$range = $this->request->get['range'];
		} else {
			$range = 'day';
		}

		switch ($range) {
			default:
			case 'day':
				$results = $this->model_extension_hbapps_hb_cart->getTotalCartsByDay();

				foreach ($results as $key => $value) {
					$json['carts']['data'][] = array($key, $value['total']);
				}
				
				$results = $this->model_extension_hbapps_hb_cart->getTotalCartsProductsByDay();

				foreach ($results as $key => $value) {
					$json['products']['data'][] = array($key, $value['total']);
				}

				for ($i = 0; $i < 24; $i++) {
					$json['xaxis'][] = array($i, $i);
				}
				break;

			case 'month':
				$results = $this->model_extension_hbapps_hb_cart->getTotalCartsByMonth();

				foreach ($results as $key => $value) {
					$json['carts']['data'][] = array($key, $value['total']);
				}
				
				$results = $this->model_extension_hbapps_hb_cart->getTotalCartsProductsByMonth();

				foreach ($results as $key => $value) {
					$json['products']['data'][] = array($key, $value['total']);
				}
				
				for ($i = 1; $i <= date('t'); $i++) {
					$date = date('Y') . '-' . date('m') . '-' . $i;

					$json['xaxis'][] = array(date('j', strtotime($date)), date('d', strtotime($date)));
				}
				break;
			case 'year':
				$results = $this->model_extension_hbapps_hb_cart->getTotalCartsByYear();

				foreach ($results as $key => $value) {
					$json['carts']['data'][] = array($key, $value['total']);
				}
				
				$results = $this->model_extension_hbapps_hb_cart->getTotalCartsProductsByYear();

				foreach ($results as $key => $value) {
					$json['products']['data'][] = array($key, $value['total']);
				}
				
				for ($i = 1; $i <= 12; $i++) {
					$json['xaxis'][] = array($i, date('M', mktime(0, 0, 0, $i)));
				}
				break;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', $this->hb_extension_route.'/hb_cart')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
	
	public function install(){
		$this->model_extension_hbapps_hb_cart->install();
		//$this->model_extension_hbapps_hb_cart->copyoldercarts();
		$data['success'] = 'This extension has been installed successfully';
	}
	
	public function uninstall(){
		$this->model_extension_hbapps_hb_cart->uninstall();
		$data['success'] = 'This extension is uninstalled successfully';
	}

	public function update(){
		$this->model_extension_hbapps_hb_cart->update();
		return true;
	}
	
	public function copycarts(){
		$this->model_extension_hbapps_hb_cart->copyoldercarts();
		$this->session->data['success'] = 'Previous Customer Carts added to this extension';
		$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_cart', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$this->request->get['store_id'], true));
	}
	
}
?>