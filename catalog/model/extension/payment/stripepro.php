<?php
class ModelExtensionPaymentStripePro extends Model {
	public function getMethod($address, $total) {
		$this->load->language('extension/payment/stripepro');

		$status = true;

		// Stripe does not allow payment for 0 or less
		if($total <= 0) {
			$status = true;
		}

		$method_data = array();

		if ($status) {

			$title = $this->language->get('text_title_default');

			// load language specific title if added by admin
			$lang_title = $this->config->get('payment_stripepro_title');
			if(isset($lang_title[$this->config->get('config_language_id')])) {
				$title = $lang_title[$this->config->get('config_language_id')];

			}

			$method_data = array(
				'code'       => 'stripepro',
				'title'      => sprintf($this->language->get('text_title'), $title),
				'terms'      => '',
				'sort_order' => $this->config->get('payment_stripepro_sort_order')
			);
		}

		return $method_data;
	}

	public function initStripe() {
		
		try {
			// load stripe libraries
			$this->load->library('stripe');
			
			if ($this->config->get('payment_stripepro_environment') == 'live' || (isset($this->request->request['livemode']) && $this->request->request['livemode'] == "true")) {
				$stripepro_secret_key = $this->config->get('payment_stripepro_live_secret_key');
			} else {
				$stripepro_secret_key = $this->config->get('payment_stripepro_test_secret_key');
			}

			if ($stripepro_secret_key != '' && $stripepro_secret_key != null) {
				\Stripe\Stripe::setApiKey($stripepro_secret_key);
				return true;
			} else {
				throw new Exception("Secret key found empty. Please configure Stripe credentials correctly.");
			}
		} catch(Exception $e){
			$this->load->model('extension/payment/stripepro');
			$this->model_extension_payment_stripepro->log(__FILE__, __LINE__, "Unable to load stripepro libraries", $e->getMessage());

			// throw this error further
			throw new Exception("Something went wrong while loading Stripe libraries.");
		}
	}

	public function log($file, $line, $caption, $message){

		if(!$this->config->get('payment_stripepro_debug')){
			return;
		}

		$iso_time = date('c');
		$filename = 'stripepro-'.strstr($iso_time, 'T', true).'.log';
	
		$log = new Log($filename);
		$msg = "[" . $iso_time . "] ";
		$msg .= "<" . $file . "> ";
		$msg .= "#" . $line . "# ";
		$msg .= "~" . $caption . "~ ";

		if(is_array($message)){
			$msg .= print_r($message, true);
		} else {
			$msg .= PHP_EOL . $message;
		}

		$msg .= PHP_EOL . PHP_EOL;		
		$log->write($msg);
	}

	public function addCustomer($stripe_customer, $customer_id, $environment = 'test') {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "stripepro_customer` SET `id` = UUID(), `customer_id` = '" . (int)$customer_id . "', `stripe_customer_id` = '" . $this->db->escape($stripe_customer['id']) . "', `environment` = '" . $this->db->escape($environment) . "'");
		return $this->db->getLastId();
	}

	public function addOrder($order_info, $stripe_charge_id, $environment = 'test') {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "stripepro_order` SET `id` = UUID(), `order_id` = '" . (int)$order_info['order_id'] . "', `stripe_charge_id` = '" . $stripe_charge_id . "', `environment` = '" . $environment . "'");
		return $this->db->getLastId();
	}

	public function getCustomer($customer_id, $environment = 'test') {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "stripepro_customer` WHERE `customer_id` = '" . (int)$customer_id . "' AND `environment` = '" . $this->db->escape($environment) . "' LIMIT 1");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return false;
		}
	}

	/*
	@return inserted/existed card id
	*/
	public function addCard($payment_method_id, $stripe_card, $customer_id, $environment = 'test') {
		// check if this same card already saved with same customer
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "stripepro_card` WHERE `customer_id` = '" . (int)$customer_id . "' AND `fingerprint` = '" . $this->db->escape($stripe_card['fingerprint']) . "' AND `exp_year` = '" . $this->db->escape($stripe_card['exp_year']) . "' AND `exp_month` = '" . $this->db->escape($stripe_card['exp_month']) . "'");

		if ($query->num_rows) {
			return $query->row['id'];
		} else {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "stripepro_card` SET `id` = UUID(), `customer_id` = '" . (int)$customer_id . "', `payment_method_id` = '" . $this->db->escape($payment_method_id) . "', `environment` = '" . $this->db->escape($environment) . "', `last4` = '" . $this->db->escape($stripe_card['last4']) . "', `brand` = '" . $this->db->escape($stripe_card['brand']) . "', `fingerprint` = '" . $this->db->escape($stripe_card['fingerprint']) . "', `exp_year` = '" . $this->db->escape($stripe_card['exp_year']) . "', `exp_month` = '" . $this->db->escape($stripe_card['exp_month']) . "'");
			return $this->db->getLastId();
		}
	}

	/*
	@return all cards array
	*/
	public function getCards($customer_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "stripepro_card` WHERE `customer_id` = '" . (int)$customer_id . "'");

		if ($query->num_rows) {
			return $query->rows;
		} else {
			return false;
		}
	}

	/*
	@return single card array
	*/
	public function getCard($unique_id, $customer_id){
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "stripepro_card` WHERE `id`='" .$this->db->escape($unique_id). "' AND `customer_id` = '" . (int)$customer_id . "'");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return false;
		}
	}

	/*
	@return single card array
	*/
	public function getCardByFingerprint($fingerprint, $customer_id){
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "stripepro_card` WHERE `fingerprint`='" .$this->db->escape($fingerprint). "' AND `customer_id` = '" . (int)$customer_id . "'");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return false;
		}
	}

	/*
	@return void
	*/
	public function deleteCard($unique_id){

		$card = $this->db->query("SELECT * FROM `" . DB_PREFIX . "stripepro_card` card LEFT JOIN `" . DB_PREFIX . "stripepro_customer` customer ON customer.customer_id=card.customer_id WHERE card.`id`='" .$this->db->escape($unique_id). "' LIMIT 1");

		if(!empty($card)){
			// load stripe library
			$this->initStripe();

			try {
				// detach this payment method
				$payment_method = \Stripe\PaymentMethod::retrieve($card->row['payment_method_id']);
				$payment_method->detach();
			} catch(\Exception $e){
				$this->log($e->getFile(), $e->getLine(), "Exception caught in deleteCard() method", $e->getMessage());
			}

			// delete this card from db
			$this->db->query("DELETE FROM `" . DB_PREFIX . "stripepro_card` WHERE `id`='" . $this->db->escape($unique_id) . "'");
		}
	}
}