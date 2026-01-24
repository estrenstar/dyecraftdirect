<?php 
class ControllerExtensionFeedUksbGoogleMerchant extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('extension/uksb_licensing/uksb_licensing');
		
		$data['regerror_email'] = $this->language->get('regerror_email');
		$data['regerror_orderid'] = $this->language->get('regerror_orderid');
		$data['regerror_noreferer'] = $this->language->get('regerror_noreferer');
		$data['regerror_localhost'] = $this->language->get('regerror_localhost');
		$data['regerror_licensedupe'] = $this->language->get('regerror_licensedupe');
		$data['regerror_quote_msg'] = $this->language->get('regerror_quote_msg');
		$data['license_purchase_thanks'] = $this->language->get('license_purchase_thanks');
		$data['license_registration'] = $this->language->get('license_registration');
		$data['license_opencart_email'] = $this->language->get('license_opencart_email');
		$data['license_opencart_orderid'] = $this->language->get('license_opencart_orderid');
		$data['check_email'] = $this->language->get('check_email');
		$data['check_orderid'] = $this->language->get('check_orderid');
		$data['server_error_curl'] = $this->language->get('server_error_curl');

 		if(isset($this->request->get['emailmal'])){
			$data['emailmal'] = true;
		}

 		if(isset($this->request->get['clear'])){
			$data['clearform'] = true;
		}

		if(isset($this->request->get['regerror'])){
		    if($this->request->get['regerror']=='emailmal'){
		    	$this->error['warning'] = $this->language->get('regerror_email');
		    }elseif($this->request->get['regerror']=='orderidmal'){
		    	$this->error['warning'] = $this->language->get('regerror_orderid');
		    }elseif($this->request->get['regerror']=='noreferer'){
		    	$this->error['warning'] = $this->language->get('regerror_noreferer');
		    }elseif($this->request->get['regerror']=='localhost'){
		    	$this->error['warning'] = $this->language->get('regerror_localhost');
		    }elseif($this->request->get['regerror']=='licensedupe'){
		    	$this->error['warning'] = $this->language->get('regerror_licensedupe');
		    }
		}

		$data['extension_id'] = '2500';
		$data['home'] = 'https://www.secureserverssl.co.uk/opencart-extensions/google-merchant/';
		$domainssl = explode("//", HTTPS_SERVER);
		$domainnonssl = explode("//", HTTP_SERVER);
		$domain = ($domainssl[1] != '' ? $domainssl[1] : $domainnonssl[1]);
		$data['aurl'] = (HTTPS_SERVER !='' ? HTTPS_SERVER : HTTP_SERVER);
        $data['auri'] = (HTTPS_CATALOG !='' ? HTTPS_CATALOG : HTTP_CATALOG) . substr($_SERVER['REQUEST_URI'], 1);

		$data['licensed'] = @file_get_contents($data['home'] . 'licensed.php?domain=' . $domain . '&extension=' . $data['extension_id']);

		if(!$data['licensed'] || $data['licensed'] == ''){
			if(extension_loaded('curl')) {
		        $post_data = array('domain' => $domain, 'extension' => $data['extension_id']);
		        $curl = curl_init();
		        curl_setopt($curl, CURLOPT_HEADER, false);
		        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		        curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
		        $follow_allowed = ( ini_get('open_basedir') || ini_get('safe_mode')) ? false : true;
		        if ($follow_allowed) {
		            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		        }
		        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 9);
		        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
		        curl_setopt($curl, CURLOPT_AUTOREFERER, true); 
		        curl_setopt($curl, CURLOPT_VERBOSE, 1);
		        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		        curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
		        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		        curl_setopt($curl, CURLOPT_URL, $data['home'] . 'licensed.php');
		        curl_setopt($curl, CURLOPT_POST, true);
		        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
		        $data['licensed'] = curl_exec($curl);
		        curl_close($curl);
		    }else{
		        $data['licensed'] = 'curl';
		    }
		}
		
		$data['licensed_md5'] = md5($data['licensed']);

		$data['entry_free_support'] = $this->language->get('entry_free_support');
		$order_details = @file_get_contents($data['home'] . 'order_details.php?domain=' . $domain . '&extension=' . $data['extension_id']);
		$order_data = json_decode($order_details, true);

		if(!is_array($order_data) || $order_data == ''){
			if(extension_loaded('curl')) {
		        $post_data2 = array('domain' => $domain, 'extension' => $data['extension_id']);
		        $curl2 = curl_init();
		        curl_setopt($curl2, CURLOPT_HEADER, false);
		        curl_setopt($curl2, CURLINFO_HEADER_OUT, true);
		        curl_setopt($curl2, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
		        $follow_allowed2 = ( ini_get('open_basedir') || ini_get('safe_mode')) ? false : true;
		        if ($follow_allowed2) {
		            curl_setopt($curl2, CURLOPT_FOLLOWLOCATION, 1);
		        }
		        curl_setopt($curl2, CURLOPT_CONNECTTIMEOUT, 9);
		        curl_setopt($curl2, CURLOPT_TIMEOUT, 60);
		        curl_setopt($curl2, CURLOPT_AUTOREFERER, true); 
		        curl_setopt($curl2, CURLOPT_VERBOSE, 1);
		        curl_setopt($curl2, CURLOPT_SSL_VERIFYHOST, false);
		        curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
		        curl_setopt($curl2, CURLOPT_FORBID_REUSE, false);
		        curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
		        curl_setopt($curl2, CURLOPT_URL, $data['home'] . 'order_details.php');
		        curl_setopt($curl2, CURLOPT_POST, true);
		        curl_setopt($curl2, CURLOPT_POSTFIELDS, http_build_query($post_data2));
		        $order_data = json_decode(curl_exec($curl2), true);
		        curl_close($curl2);
		    }else{
				$order_data['status'] = 'disabled';
		    }
		}

		if($order_data['status'] == 'enabled' && $order_data['registered_date'] != ''){
			$data['text_deregister'] = $this->language->get('text_deregister');
			$data['help_deregister'] = $this->language->get('help_deregister');
			$data['link_deregister'] = $this->url->link('extension/feed/uksb_google_merchant/deregister', 'domain=' . urlencode($domain) . '&extension_id=' . $data['extension_id'] . '&user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['text_deregister'] = '';
			$data['help_deregister'] = '';
			$data['link_deregister'] = '';
		}

		if(isset($order_data['status']) && $order_data['status'] == 'enabled'){
			$isSecure = false;
			if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
				$isSecure = true;
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
				$isSecure = true;
			}

			$data['support_status'] = 'enabled';
			$data['support_order_id'] = $order_data['order_id'];
			$data['support_extension_name'] = $order_data['extension_name'];
			$data['support_domain'] = $order_data['domain'];
			$data['support_username'] = $order_data['username'];
			$data['support_email'] = $order_data['email'];
			$data['support_registered_date'] = strftime('%Y-%m-%d', $order_data['registered_date']);
			$data['support_order_date'] = strftime('%Y-%m-%d', ($order_data['order_date'] + 31536000));

			if((time() - $order_data['order_date']) > 31536000){
				$data['text_free_support_remaining'] = sprintf($this->language->get('text_free_support_expired'), 1, ($isSecure ? 1 : 0), urlencode($domain) , $data['extension_id'] , $this->session->data['user_token']);
			}else{
				$data['text_free_support_remaining'] = sprintf($this->language->get('text_free_support_remaining'), 366 - ceil((time() - $order_data['order_date']) / 86400));
			}
		}else{
			$data['support_status'] = 'disabled';
			$data['text_free_support_remaining'] = sprintf($this->language->get('text_free_support_remaining'), 'unknown');
		}

		
		$this->load->language('extension/feed/uksb_google_merchant');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('feed_uksb_google_merchant', $this->request->post);				
			
			if($this->request->post['feed_uksb_google_merchant_logging']){
				$uksb_google_log = new Log('uksb_google.log');
				if($this->request->post['feed_uksb_google_merchant_logging'] != $this->config->get('feed_uksb_google_merchant_logging')){
					$uksb_google_log->write("Success - Feed Logging Enabled" . "\n");
				}
				$uksb_google_log->write("Success - Feed Settings Were Modified/Saved" . "\n");
			}

			if (isset($this->request->post['savecontinue'])) {
				if ($this->request->post['savecontinue']) {
					$this->response->redirect($this->url->link('extension/feed/uksb_google_merchant', 'user_token=' . $this->session->data['user_token'], true));
				}
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true));
		}

		$data['config_cron'] = $this->config->get('feed_uksb_google_merchant_cron');
		$data['config_language'] = $this->config->get('config_language');
		$data['config_currency'] = $this->config->get('config_currency');

        $data['clear_log'] = $this->url->link('extension/feed/uksb_google_merchant/clearlog', 'user_token=' . $this->session->data['user_token'], true);
        $data['viewlog'] = (isset($this->request->get['viewlog'])?1:0);

        $file = DIR_LOGS . 'uksb_google.log';

        if (file_exists($file)) {
        	$data['log'] = '';

			$logdata = explode("\n",htmlentities(file_get_contents($file, FILE_USE_INCLUDE_PATH, null)));

			foreach(array_reverse($logdata) as $value) { 
			    $data['log'] .= $value . "\n";
			}
        } else {
            $data['log'] = '';
        }        

		$data['uksbhome'] = 'https://www.secureserverssl.co.uk/opencart-extensions/google-merchant/';
		$this->load->model('tool/image');
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

 		if(isset($this->request->get['clear'])){
			$data['clearform'] = true;
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
 		if (isset($this->error['duplicate'])) {
			$data['error_duplicate'] = $this->error['duplicate'];
		} else {
			$data['error_duplicate'] = '';
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/feed/uksb_google_merchant', 'user_token=' . $this->session->data['user_token'], true)
		);
				
		$data['action'] = $this->url->link('extension/feed/uksb_google_merchant', 'user_token=' . $this->session->data['user_token'], true);
		$data['action_bulk_update'] = $this->url->link('extension/feed/uksb_google_merchant/bulk_update', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true);
		
		if (isset($this->request->post['feed_uksb_google_merchant_status'])) {
			$data['feed_uksb_google_merchant_status'] = $this->request->post['feed_uksb_google_merchant_status'];
		} else {
			$data['feed_uksb_google_merchant_status'] = $this->config->get('feed_uksb_google_merchant_status');
		}
				
		if (isset($this->request->post['feed_uksb_google_merchant_secure_code'])) {
			$data['feed_uksb_google_merchant_secure_code'] = $this->request->post['feed_uksb_google_merchant_secure_code'];
		} else {
			if(!$this->config->get('feed_uksb_google_merchant_secure_code')||$this->config->get('feed_uksb_google_merchant_secure_code')==''){
				$data['feed_uksb_google_merchant_secure_code'] = $this->generateRandomString();
			}else{
				$data['feed_uksb_google_merchant_secure_code'] = $this->config->get('feed_uksb_google_merchant_secure_code');
			}
		}
				
		if (isset($this->request->post['feed_uksb_google_merchant_characters'])) {
			$data['feed_uksb_google_merchant_characters'] = $this->request->post['feed_uksb_google_merchant_characters'];
		} else {
			$data['feed_uksb_google_merchant_characters'] = $this->config->get('feed_uksb_google_merchant_characters');
		}
		
		if (isset($this->request->post['feed_uksb_google_merchant_cron'])) {
			$data['feed_uksb_google_merchant_cron'] = $this->request->post['feed_uksb_google_merchant_cron'];
		} else {
			$data['feed_uksb_google_merchant_cron'] = $this->config->get('feed_uksb_google_merchant_cron');
		}
		
		if (isset($this->request->post['feed_uksb_google_merchant_product_type'])) {
			$data['feed_uksb_google_merchant_product_type'] = $this->request->post['feed_uksb_google_merchant_product_type'];
		} else {
			$data['feed_uksb_google_merchant_product_type'] = $this->config->get('feed_uksb_google_merchant_product_type');
		}
		

		if (isset($this->request->post['feed_uksb_google_merchant_google_category_gb'])) {
			$data['feed_uksb_google_merchant_google_category_gb'] = $this->request->post['feed_uksb_google_merchant_google_category_gb'];
			$data['feed_uksb_google_merchant_google_category_us'] = $this->request->post['feed_uksb_google_merchant_google_category_us'];
			$data['feed_uksb_google_merchant_google_category_au'] = $this->request->post['feed_uksb_google_merchant_google_category_au'];
			$data['feed_uksb_google_merchant_google_category_fr'] = $this->request->post['feed_uksb_google_merchant_google_category_fr'];
			$data['feed_uksb_google_merchant_google_category_de'] = $this->request->post['feed_uksb_google_merchant_google_category_de'];
			$data['feed_uksb_google_merchant_google_category_it'] = $this->request->post['feed_uksb_google_merchant_google_category_it'];
			$data['feed_uksb_google_merchant_google_category_nl'] = $this->request->post['feed_uksb_google_merchant_google_category_nl'];
			$data['feed_uksb_google_merchant_google_category_es'] = $this->request->post['feed_uksb_google_merchant_google_category_es'];
			$data['feed_uksb_google_merchant_google_category_pt'] = $this->request->post['feed_uksb_google_merchant_google_category_pt'];
			$data['feed_uksb_google_merchant_google_category_cz'] = $this->request->post['feed_uksb_google_merchant_google_category_cz'];
			$data['feed_uksb_google_merchant_google_category_jp'] = $this->request->post['feed_uksb_google_merchant_google_category_jp'];
			$data['feed_uksb_google_merchant_google_category_dk'] = $this->request->post['feed_uksb_google_merchant_google_category_dk'];
			$data['feed_uksb_google_merchant_google_category_no'] = $this->request->post['feed_uksb_google_merchant_google_category_no'];
			$data['feed_uksb_google_merchant_google_category_pl'] = $this->request->post['feed_uksb_google_merchant_google_category_pl'];
			$data['feed_uksb_google_merchant_google_category_ru'] = $this->request->post['feed_uksb_google_merchant_google_category_ru'];
			$data['feed_uksb_google_merchant_google_category_sv'] = $this->request->post['feed_uksb_google_merchant_google_category_sv'];
			$data['feed_uksb_google_merchant_google_category_tr'] = $this->request->post['feed_uksb_google_merchant_google_category_tr'];
		} else {
			$data['feed_uksb_google_merchant_google_category_gb'] = $this->config->get('feed_uksb_google_merchant_google_category_gb');
			$data['feed_uksb_google_merchant_google_category_us'] = $this->config->get('feed_uksb_google_merchant_google_category_us');
			$data['feed_uksb_google_merchant_google_category_au'] = $this->config->get('feed_uksb_google_merchant_google_category_au');
			$data['feed_uksb_google_merchant_google_category_fr'] = $this->config->get('feed_uksb_google_merchant_google_category_fr');
			$data['feed_uksb_google_merchant_google_category_de'] = $this->config->get('feed_uksb_google_merchant_google_category_de');
			$data['feed_uksb_google_merchant_google_category_it'] = $this->config->get('feed_uksb_google_merchant_google_category_it');
			$data['feed_uksb_google_merchant_google_category_nl'] = $this->config->get('feed_uksb_google_merchant_google_category_nl');
			$data['feed_uksb_google_merchant_google_category_es'] = $this->config->get('feed_uksb_google_merchant_google_category_es');
			$data['feed_uksb_google_merchant_google_category_pt'] = $this->config->get('feed_uksb_google_merchant_google_category_pt');
			$data['feed_uksb_google_merchant_google_category_cz'] = $this->config->get('feed_uksb_google_merchant_google_category_cz');
			$data['feed_uksb_google_merchant_google_category_jp'] = $this->config->get('feed_uksb_google_merchant_google_category_jp');
			$data['feed_uksb_google_merchant_google_category_dk'] = $this->config->get('feed_uksb_google_merchant_google_category_dk');
			$data['feed_uksb_google_merchant_google_category_no'] = $this->config->get('feed_uksb_google_merchant_google_category_no');
			$data['feed_uksb_google_merchant_google_category_pl'] = $this->config->get('feed_uksb_google_merchant_google_category_pl');
			$data['feed_uksb_google_merchant_google_category_ru'] = $this->config->get('feed_uksb_google_merchant_google_category_ru');
			$data['feed_uksb_google_merchant_google_category_sv'] = $this->config->get('feed_uksb_google_merchant_google_category_sv');
			$data['feed_uksb_google_merchant_google_category_tr'] = $this->config->get('feed_uksb_google_merchant_google_category_tr');
		}
		
		if (isset($this->request->post['feed_uksb_google_merchant_id_field'])) {
			$data['feed_uksb_google_merchant_id_field'] = $this->request->post['feed_uksb_google_merchant_id_field'];
		} else {
			$data['feed_uksb_google_merchant_id_field'] = $this->config->get('feed_uksb_google_merchant_id_field');
		}
		
		if (isset($this->request->post['feed_uksb_google_merchant_title_field'])) {
			$data['feed_uksb_google_merchant_title_field'] = $this->request->post['feed_uksb_google_merchant_title_field'];
		} else {
			$data['feed_uksb_google_merchant_title_field'] = $this->config->get('feed_uksb_google_merchant_title_field');
		}
		
		if (isset($this->request->post['feed_uksb_google_merchant_description_field'])) {
			$data['feed_uksb_google_merchant_description_field'] = $this->request->post['feed_uksb_google_merchant_description_field'];
		} else {
			$data['feed_uksb_google_merchant_description_field'] = $this->config->get('feed_uksb_google_merchant_description_field');
		}
		
		if (isset($this->request->post['feed_uksb_google_merchant_mpn'])) {
			$data['feed_uksb_google_merchant_mpn'] = $this->request->post['feed_uksb_google_merchant_mpn'];
		} else {
			$data['feed_uksb_google_merchant_mpn'] = $this->config->get('feed_uksb_google_merchant_mpn');
		}
		
		if (isset($this->request->post['feed_uksb_google_merchant_g_gtin'])) {
			$data['feed_uksb_google_merchant_g_gtin'] = $this->request->post['feed_uksb_google_merchant_g_gtin'];
		} else {
			$data['feed_uksb_google_merchant_g_gtin'] = $this->config->get('feed_uksb_google_merchant_g_gtin');
		}
		
		if (isset($this->request->post['feed_uksb_google_merchant_stock_checkout'])) {
			$data['feed_uksb_google_merchant_stock_checkout'] = $this->request->post['feed_uksb_google_merchant_stock_checkout'];
		} else {
			$data['feed_uksb_google_merchant_stock_checkout'] = $this->config->get('feed_uksb_google_merchant_stock_checkout');
		}
		
		if (isset($this->request->post['feed_uksb_google_merchant_micro_data'])) {
			$data['feed_uksb_google_merchant_micro_data'] = $this->request->post['feed_uksb_google_merchant_micro_data'];
		} else {
			$data['feed_uksb_google_merchant_micro_data'] = $this->config->get('feed_uksb_google_merchant_micro_data');
		}
		


		if (isset($this->request->post['feed_uksb_google_merchant_logging'])) {
			$data['feed_uksb_google_merchant_logging'] = $this->request->post['feed_uksb_google_merchant_logging'];
		} else {
			$data['feed_uksb_google_merchant_logging'] = $this->config->get('feed_uksb_google_merchant_logging');
		}
		
		if (isset($this->request->post['feed_uksb_google_merchant_defaults_on_google'])) {
			$data['feed_uksb_google_merchant_defaults_on_google'] = $this->request->post['feed_uksb_google_merchant_defaults_on_google'];
		} else {
			$data['feed_uksb_google_merchant_defaults_on_google'] = $this->config->get('feed_uksb_google_merchant_defaults_on_google');
		}
		
		if (isset($this->request->post['feed_uksb_google_merchant_defaults_shipping_label'])) {
			$data['feed_uksb_google_merchant_defaults_shipping_label'] = $this->request->post['feed_uksb_google_merchant_defaults_shipping_label'];
		} else {
			$data['feed_uksb_google_merchant_defaults_shipping_label'] = $this->config->get('feed_uksb_google_merchant_defaults_shipping_label');
		}
		
		if (isset($this->request->post['feed_uksb_google_merchant_defaults_identifier_exists'])) {
			$data['feed_uksb_google_merchant_defaults_identifier_exists'] = $this->request->post['feed_uksb_google_merchant_defaults_identifier_exists'];
		} else {
			$data['feed_uksb_google_merchant_defaults_identifier_exists'] = $this->config->get('feed_uksb_google_merchant_defaults_identifier_exists');
		}
		
		if (isset($this->request->post['feed_uksb_google_merchant_defaults_condition'])) {
			$data['feed_uksb_google_merchant_defaults_condition'] = $this->request->post['feed_uksb_google_merchant_defaults_condition'];
		} else {
			$data['feed_uksb_google_merchant_defaults_condition'] = $this->config->get('feed_uksb_google_merchant_defaults_condition');
		}

		$this->load->model('extension/feed/uksb_google_merchant');
		
		$data['other_languages'] = $this->model_extension_feed_uksb_google_merchant->getLanguages();
		$data['other_currencies'] = $this->model_extension_feed_uksb_google_merchant->getCurrencies();

		$data['data_feed'] = '';
		if(!$this->config->get('feed_uksb_google_merchant_cron')){
			$data['data_feed'] = HTTP_CATALOG.'index.php?route=extension/feed/uksb_google_merchant';
		}else{
			$data['data_feed'] = HTTP_CATALOG.'uksb_feeds/';
			$data['data_cron_path'] = HTTP_CATALOG.'index.php?route=extension/feed/uksb_google_merchant&mode=cron';
		}
		
		$this->load->model('setting/store');

		if($this->model_setting_store->getTotalStores()>0){
			$stores = $this->model_setting_store->getStores();
			$stores = array_reverse($stores);
			
			foreach($stores as $store){
				if(!$this->config->get('feed_uksb_google_merchant_cron')){
					$data['data_feed'] .= '^'.$store['url'].'index.php?route=extension/feed/uksb_google_merchant';
				}else{
					$data['data_feed'] .= '^'.$store['url'].'uksb_feeds/';
					$data['data_cron_path'] .= '^'.$store['url'].'index.php?route=extension/feed/uksb_google_merchant&mode=cron';
				}
			}
		}
		
		$this->model_extension_feed_uksb_google_merchant->checkInstallState();
		
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['user_token'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('extension/feed/uksb_google_merchant', $data));
	} 
	
	public function clearlog(){
		$handle = fopen(DIR_LOGS . 'uksb_google.log', 'w+');
		fclose($handle);
		
		$this->response->redirect($this->url->link('extension/feed/uksb_google_merchant', 'user_token=' . $this->session->data['user_token'] . '&viewlog=1', true));
	}

	public function deregister() {
		$data['home'] = 'https://www.secureserverssl.co.uk/opencart-extensions/google-merchant/';
		$data['deregistered'] = @file_get_contents($data['home'] . 'deregister.php?domain=' . $this->request->get['domain'] . '&extension_id=' . $this->request->get['extension_id']);

		if(!$data['deregistered'] || $data['deregistered'] == ''){
			if(extension_loaded('curl')) {
		        $post_data = array('domain' => $this->request->get['domain'], 'extension_id' => $this->request->get['extension_id']);
		        $curl = curl_init();
		        curl_setopt($curl, CURLOPT_HEADER, false);
		        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		        curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
		        $follow_allowed = ( ini_get('open_basedir') || ini_get('safe_mode')) ? false : true;
		        if ($follow_allowed) {
		            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		        }
		        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 9);
		        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
		        curl_setopt($curl, CURLOPT_AUTOREFERER, true); 
		        curl_setopt($curl, CURLOPT_VERBOSE, 1);
		        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		        curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
		        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		        curl_setopt($curl, CURLOPT_URL, $data['home'] . 'deregister.php');
		        curl_setopt($curl, CURLOPT_POST, true);
		        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
		        $data['deregistered'] = curl_exec($curl);
		        curl_close($curl);
		    }else{
		        $data['deregistered'] = '';
		    }
		}

		if($data['deregistered'] == 'deregistered'){
			$this->load->model('extension/feed/uksb_google_merchant');
			$this->model_extension_feed_uksb_google_merchant->deregister();
		}

		$this->response->redirect($this->url->link('extension/feed/uksb_google_merchant', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function generateRandomString($length = 10) {
	    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/feed/uksb_google_merchant')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['feed_uksb_google_merchant_status'])&&$this->request->post['feed_uksb_google_merchant_mpn']==$this->request->post['feed_uksb_google_merchant_g_gtin']) {
			$this->error['duplicate'] = $this->language->get('error_duplicate');
		}

		return !$this->error;
	}	
	
	public function bulk_update(){
		$json = array();
		$this->load->language('extension/feed/uksb_google_merchant');

		if (!$this->user->hasPermission('modify', 'extension/feed/uksb_google_merchant')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!isset($json['error'])) {
			if(isset($this->request->post['x_g_on_google']) && isset($this->request->post['x_g_shipping_label']) && isset($this->request->post['x_g_promotion_id']) && isset($this->request->post['x_g_expiry_date']) && isset($this->request->post['x_g_identifier_exists']) && isset($this->request->post['x_g_condition']) && isset($this->request->post['x_g_brand']) && isset($this->request->post['x_g_product_type']) && isset($this->request->post['x_google_categories']) && isset($this->request->post['x_g_multipack']) && isset($this->request->post['x_g_is_bundle']) && isset($this->request->post['x_g_adult']) && isset($this->request->post['x_g_energy_efficiency_class']) && isset($this->request->post['x_g_unit_pricing_measure']) && isset($this->request->post['x_g_unit_pricing_base_measure']) && isset($this->request->post['x_g_gender']) && isset($this->request->post['x_g_age_group']) && isset($this->request->post['x_g_size_type']) && isset($this->request->post['x_g_size_system']) && isset($this->request->post['x_variants']) && isset($this->request->post['x_g_custom_label_0']) && isset($this->request->post['x_g_custom_label_1']) && isset($this->request->post['x_g_custom_label_2']) && isset($this->request->post['x_g_custom_label_3']) && isset($this->request->post['x_g_custom_label_4']) && isset($this->request->post['x_g_adwords_redirect'])){
				$json['error'] = $this->language->get('error_bulk_ignored');
			}elseif(!isset($json['error'])){
				if (!isset($this->request->post['x_g_on_google'])) {
		      		$data['g_on_google'] = $this->request->post['g_on_google'];
		    	}
				
				if (!isset($this->request->post['x_g_shipping_label'])) {
		      		$data['g_shipping_label'] = $this->request->post['g_shipping_label'];
		    	}
				
				if (!isset($this->request->post['x_g_promotion_id'])) {
		      		$data['g_promotion_id'] = $this->request->post['g_promotion_id'];
		    	}
				
				if (!isset($this->request->post['x_g_expiry_date'])) {
		      		$data['g_expiry_date'] = $this->request->post['g_expiry_date'];
		    	}

				if (!isset($this->request->post['x_g_identifier_exists'])) {
		      		$data['g_identifier_exists'] = $this->request->post['g_identifier_exists'];
		    	}
						
				if (!isset($this->request->post['x_g_condition'])) {
		      		$data['g_condition'] = $this->request->post['g_condition'];
		    	}

				if (!isset($this->request->post['x_g_brand'])) {
		      		$data['g_brand'] = $this->request->post['g_brand'];
		    	}

				if (!isset($this->request->post['x_g_product_type'])) {
		      		$data['g_product_type'] = $this->request->post['g_product_type'];
		    	}

				if (!isset($this->request->post['x_google_categories'])) {
					$data['google_category_gb'] = $this->request->post['google_category_gb'];
					$data['google_category_us'] = $this->request->post['google_category_us'];
					$data['google_category_au'] = $this->request->post['google_category_au'];
					$data['google_category_fr'] = $this->request->post['google_category_fr'];
					$data['google_category_de'] = $this->request->post['google_category_de'];
					$data['google_category_it'] = $this->request->post['google_category_it'];
					$data['google_category_nl'] = $this->request->post['google_category_nl'];
					$data['google_category_es'] = $this->request->post['google_category_es'];
					$data['google_category_pt'] = $this->request->post['google_category_pt'];
					$data['google_category_cz'] = $this->request->post['google_category_cz'];
					$data['google_category_jp'] = $this->request->post['google_category_jp'];
					$data['google_category_dk'] = $this->request->post['google_category_dk'];
					$data['google_category_no'] = $this->request->post['google_category_no'];
					$data['google_category_pl'] = $this->request->post['google_category_pl'];
					$data['google_category_ru'] = $this->request->post['google_category_ru'];
					$data['google_category_sv'] = $this->request->post['google_category_sv'];
					$data['google_category_tr'] = $this->request->post['google_category_tr'];
				}

				if (!isset($this->request->post['x_g_multipack'])) {
		      		$data['g_multipack'] = $this->request->post['g_multipack'];
		    	}
						
				if (!isset($this->request->post['x_g_is_bundle'])) {
		      		$data['g_is_bundle'] = $this->request->post['g_is_bundle'];
		    	}
						
				if (!isset($this->request->post['x_g_adult'])) {
		      		$data['g_adult'] = $this->request->post['g_adult'];
		    	}

				if (!isset($this->request->post['x_g_energy_efficiency_class'])) {
		      		$data['g_energy_efficiency_class'] = $this->request->post['g_energy_efficiency_class'];
		    	}

				if (!isset($this->request->post['x_g_unit_pricing_measure'])) {
		      		$data['g_unit_pricing_measure'] = $this->request->post['g_unit_pricing_measure'];
		    	}
						
				if (!isset($this->request->post['x_g_unit_pricing_base_measure'])) {
		      		$data['g_unit_pricing_base_measure'] = $this->request->post['g_unit_pricing_base_measure'];
		    	}

				if (!isset($this->request->post['x_g_gender'])) {
		      		$data['g_gender'] = $this->request->post['g_gender'];
		    	}

				if (!isset($this->request->post['x_g_age_group'])) {
		      		$data['g_age_group'] = $this->request->post['g_age_group'];
		    	}

				if (!isset($this->request->post['x_g_size_type'])) {
		      		$data['g_size_type'] = $this->request->post['g_size_type'];
		    	}

				if (!isset($this->request->post['x_g_size_system'])) {
		      		$data['g_size_system'] = $this->request->post['g_size_system'];
		    	}

				if (!isset($this->request->post['x_variants'])) {
					$variants = $this->request->post['variant'];

					$data['variants'] = array();

					foreach ($variants as $variant) {
						if ($variant['v_images'] && file_exists(DIR_IMAGE . $variant['v_images'])) {
							$v_images = $variant['v_images'];
						} else {
							$v_images = 'no_image.jpg';
						}

						$data['variants'][] = array(
							'g_colour'               => $variant['g_colour'],
							'g_size'                 => $variant['g_size'],
							'g_material'             => $variant['g_material'],
							'g_pattern'              => $variant['g_pattern'],
							'v_prices'               => $variant['v_prices'],
							'v_images'               => $v_images
						);
					}
			    }
									
				if (!isset($this->request->post['x_g_custom_label_0'])) {
		      		$data['g_custom_label_0'] = $this->request->post['g_custom_label_0'];
		    	}
						
				if (!isset($this->request->post['x_g_custom_label_1'])) {
		      		$data['g_custom_label_1'] = $this->request->post['g_custom_label_1'];
		    	}
						
				if (!isset($this->request->post['x_g_custom_label_2'])) {
		      		$data['g_custom_label_2'] = $this->request->post['g_custom_label_2'];
		    	}
						
				if (!isset($this->request->post['x_g_custom_label_3'])) {
		      		$data['g_custom_label_3'] = $this->request->post['g_custom_label_3'];
		    	}
						
				if (!isset($this->request->post['x_g_custom_label_4'])) {
		      		$data['g_custom_label_4'] = $this->request->post['g_custom_label_4'];
		    	}			

				if (!isset($this->request->post['x_g_adwords_redirect'])){
					$data['g_adwords_redirect'] = $this->request->post['g_adwords_redirect'];
				}

				if (isset($this->request->post['product_manufacturers'])){
					$data['product_manufacturers'] = $this->request->post['product_manufacturers'];
				}

				if (isset($this->request->post['product_categories'])){
					$data['product_categories'] = $this->request->post['product_categories'];
				}

				if (isset($this->request->post['product_products'])){
					$data['product_products'] = $this->request->post['product_products'];
				}

				$this->load->model('extension/feed/uksb_google_merchant');

				$json['updated'] = sprintf($this->language->get('text_bulk_updated'), $this->model_extension_feed_uksb_google_merchant->update($data));
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function addProduct($route, $data, $product_id){
		$this->load->model('extension/feed/uksb_google_merchant');
		$this->model_extension_feed_uksb_google_merchant->addProduct($product_id, $this->request->post);
	}
	
	public function editProduct($route, $data){
		$this->load->model('extension/feed/uksb_google_merchant');
		$this->model_extension_feed_uksb_google_merchant->editProduct((isset($this->request->get['product_id']) ? $this->request->get['product_id'] : (isset($this->request->post['product_id']) ? $this->request->post['product_id'] : $product_id)), $this->request->post);
	}
	
	public function deleteProduct($route){
		$this->load->model('extension/feed/uksb_google_merchant');

		foreach ($this->request->post['selected'] as $product_id) {
			$this->model_extension_feed_uksb_google_merchant->deleteProduct($product_id);
		}
	}
	
	public function addCategory($route, $data, $category_id){
		$this->load->model('extension/feed/uksb_google_merchant');
		$this->model_extension_feed_uksb_google_merchant->addCategory($category_id, $this->request->post);
	}
	
	public function editCategory($route, $data){
		$this->load->model('extension/feed/uksb_google_merchant');
		$this->model_extension_feed_uksb_google_merchant->editCategory((isset($this->request->get['category_id']) ? $this->request->get['category_id'] : (isset($this->request->post['category_id']) ? $this->request->post['category_id'] : $category_id)), $this->request->post);
	}
	
	public function deleteCategory($route){
		$this->load->model('extension/feed/uksb_google_merchant');

		foreach ($this->request->post['selected'] as $category_id) {
			$this->model_extension_feed_uksb_google_merchant->deleteCategory($category_id);
		}
	}

	public function install() {
		$this->load->model('extension/feed/uksb_google_merchant');
		$this->model_extension_feed_uksb_google_merchant->preInstall();

		$this->load->model('setting/event');
		$this->model_setting_event->addEvent('uksb_google_merchant', 'admin/model/catalog/product/addProduct/after', 'extension/feed/uksb_google_merchant/addProduct');
		$this->model_setting_event->addEvent('uksb_google_merchant', 'admin/model/catalog/product/editProduct/after', 'extension/feed/uksb_google_merchant/editProduct');
		$this->model_setting_event->addEvent('uksb_google_merchant', 'admin/model/catalog/product/deleteProduct/after', 'extension/feed/uksb_google_merchant/deleteProduct');
		$this->model_setting_event->addEvent('uksb_google_merchant', 'admin/model/catalog/category/addCategory/after', 'extension/feed/uksb_google_merchant/addCategory');
		$this->model_setting_event->addEvent('uksb_google_merchant', 'admin/model/catalog/category/editCategory/after', 'extension/feed/uksb_google_merchant/editCategory');
		$this->model_setting_event->addEvent('uksb_google_merchant', 'admin/model/catalog/category/deleteCategory/after', 'extension/feed/uksb_google_merchant/deleteCategory');
		
		$this->model_extension_feed_uksb_google_merchant->install();
		
		$data['redirect'] = 'extension/feed/uksb_google_merchant';
	    $this->load->controller('marketplace/modification/refresh', $data);
	}
     
	public function uninstall() {
		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('uksb_google_merchant');
	}
}