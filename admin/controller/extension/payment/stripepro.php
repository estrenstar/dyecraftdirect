<?php
class ControllerExtensionPaymentStripePro extends Controller {
	
	private $error = array();

	public function install() {
		$this->load->model('extension/payment/stripepro');
		$this->model_extension_payment_stripepro->install();
	}

	public function uninstall() {
		$this->load->model('extension/payment/stripepro');
		$this->model_extension_payment_stripepro->uninstall();
	}

	public function index() {

		$this->load->language('extension/payment/stripepro');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_stripepro', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment/stripepro', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payments'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/stripepro', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/stripepro', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		if (isset($this->request->post['payment_stripepro_title'])) {
			$data['payment_stripepro_title'] = $this->request->post['payment_stripepro_title'];
		} elseif ($this->config->has('payment_stripepro_title')) {
			$data['payment_stripepro_title'] = $this->config->get('payment_stripepro_title');
		} else {
			$data['payment_stripepro_title'] = array();
		}

		if (isset($this->request->post['payment_stripepro_environment'])) {
			$data['payment_stripepro_environment'] = $this->request->post['payment_stripepro_environment'];
		} elseif ($this->config->has('payment_stripepro_environment')) {
			$data['payment_stripepro_environment'] = $this->config->get('payment_stripepro_environment');
		} else {
			$data['payment_stripepro_environment'] = 'test';
		}
		
		if (isset($this->request->post['payment_stripepro_test_public_key'])) {
			$data['payment_stripepro_test_public_key'] = $this->request->post['payment_stripepro_test_public_key'];
		} else if($this->config->has('payment_stripepro_test_public_key')){
			$data['payment_stripepro_test_public_key'] = $this->config->get('payment_stripepro_test_public_key');
		} else {
			$data['payment_stripepro_test_public_key'] = '';
		}

		if (isset($this->request->post['payment_stripepro_test_secret_key'])) {
			$data['payment_stripepro_test_secret_key'] = $this->request->post['payment_stripepro_test_secret_key'];
		} else if($this->config->has('payment_stripepro_test_secret_key')){
			$data['payment_stripepro_test_secret_key'] = $this->config->get('payment_stripepro_test_secret_key');
		} else {
			$data['payment_stripepro_test_secret_key'] = '';
		}

		if (isset($this->request->post['payment_stripepro_live_public_key'])) {
			$data['payment_stripepro_live_public_key'] = $this->request->post['payment_stripepro_live_public_key'];
		} else if($this->config->has('payment_stripepro_live_public_key')){
			$data['payment_stripepro_live_public_key'] = $this->config->get('payment_stripepro_live_public_key');
		} else {
			$data['payment_stripepro_live_public_key'] = '';
		}

		if (isset($this->request->post['payment_stripepro_live_secret_key'])) {
			$data['payment_stripepro_live_secret_key'] = $this->request->post['payment_stripepro_live_secret_key'];
		} else if($this->config->has('payment_stripepro_live_secret_key')){
			$data['payment_stripepro_live_secret_key'] = $this->config->get('payment_stripepro_live_secret_key');
		} else {
			$data['payment_stripepro_live_secret_key'] = '';
		}

		if (isset($this->request->post['payment_stripepro_order_success_status_id'])) {
			$data['payment_stripepro_order_success_status_id'] = $this->request->post['payment_stripepro_order_success_status_id'];
		} else if($this->config->has('payment_stripepro_order_success_status_id')){
			$data['payment_stripepro_order_success_status_id'] = $this->config->get('payment_stripepro_order_success_status_id');
		} else {
			$data['payment_stripepro_order_success_status_id'] = '';
		}

		if (isset($this->request->post['payment_stripepro_order_failed_status_id'])) {
			$data['payment_stripepro_order_failed_status_id'] = $this->request->post['payment_stripepro_order_failed_status_id'];
		} else if($this->config->has('payment_stripepro_order_failed_status_id')){
			$data['payment_stripepro_order_failed_status_id'] = $this->config->get('payment_stripepro_order_failed_status_id');
		} else {
			$data['payment_stripepro_order_failed_status_id'] = '';
		}

		if (isset($this->request->post['payment_stripepro_store_card'])) {
			$data['payment_stripepro_store_card'] = $this->request->post['payment_stripepro_store_card'];
		} else if($this->config->has('payment_stripepro_store_card')){
			$data['payment_stripepro_store_card'] = (int)$this->config->get('payment_stripepro_store_card');
		} else {
			$data['payment_stripepro_store_card'] = 0;
		}

		if (isset($this->request->post['payment_stripepro_store_card_prechecked'])) {
			$data['payment_stripepro_store_card_prechecked'] = $this->request->post['payment_stripepro_store_card_prechecked'];
		} else if($this->config->has('payment_stripepro_store_card_prechecked')){
			$data['payment_stripepro_store_card_prechecked'] = (int)$this->config->get('payment_stripepro_store_card_prechecked');
		} else {
			$data['payment_stripepro_store_card_prechecked'] = 0;
		}

		if (isset($this->request->post['payment_stripepro_debug'])) {
			$data['payment_stripepro_debug'] = $this->request->post['payment_stripepro_debug'];
		} else if($this->config->has('payment_stripepro_debug')){
			$data['payment_stripepro_debug'] = (int)$this->config->get('payment_stripepro_debug');
		} else {
			$data['payment_stripepro_debug'] = 0;
		}

		if (isset($this->request->post['payment_stripepro_status'])) {
			$data['payment_stripepro_status'] = $this->request->post['payment_stripepro_status'];
		} else if($this->config->has('payment_stripepro_status')){
			$data['payment_stripepro_status'] = (int)$this->config->get('payment_stripepro_status');
		} else {
			$data['payment_stripepro_status'] = 0;
		}

		if (isset($this->request->post['payment_stripepro_sort_order'])) {
			$data['payment_stripepro_sort_order'] = $this->request->post['payment_stripepro_sort_order'];
		} else if($this->config->has('payment_stripepro_sort_order')){
			$data['payment_stripepro_sort_order'] = (int)$this->config->get('payment_stripepro_sort_order');
		} else {
			$data['payment_stripepro_sort_order'] = 0;
		}

		// populate errors
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

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = '';
		}

		if (isset($this->error['test_public_key'])) {
			$data['error_test_public_key'] = $this->error['test_public_key'];
		} else {
			$data['error_test_public_key'] = '';
		}

		if (isset($this->error['test_secret_key'])) {
			$data['error_test_secret_key'] = $this->error['test_secret_key'];
		} else {
			$data['error_test_secret_key'] = '';
		}

		if (isset($this->error['live_public_key'])) {
			$data['error_live_public_key'] = $this->error['live_public_key'];
		} else {
			$data['error_live_public_key'] = '';
		}

		if (isset($this->error['live_secret_key'])) {
			$data['error_live_secret_key'] = $this->error['live_secret_key'];
		} else {
			$data['error_live_secret_key'] = '';
		}

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/stripepro', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/stripepro')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		
		if(isset($this->request->post['payment_stripepro_environment'])){

			if($this->request->post['payment_stripepro_environment'] == 'test'){

				if(!isset($this->request->post['payment_stripepro_test_public_key']) || trim($this->request->post['payment_stripepro_test_public_key']) == ''){
					$this->error['test_public_key'] = $this->language->get('error_test_public_key');
				}
				if(!isset($this->request->post['payment_stripepro_test_secret_key']) || trim($this->request->post['payment_stripepro_test_secret_key']) == ''){
					$this->error['test_secret_key'] = $this->language->get('error_test_secret_key');
				}
			
			} else {

				if(!isset($this->request->post['payment_stripepro_live_public_key']) || trim($this->request->post['payment_stripepro_live_public_key']) == ''){
					$this->error['live_public_key'] = $this->language->get('error_live_public_key');
				}
				if(!isset($this->request->post['payment_stripepro_live_secret_key']) || trim($this->request->post['payment_stripepro_live_secret_key']) == ''){
					$this->error['live_secret_key'] = $this->language->get('error_live_secret_key');
				}
			}
		} else {
			$this->error['environment'] = $this->language->get('error_environment');
		}

		foreach($this->request->post['payment_stripepro_title'] as $lang_key=>$lang_val){
			if(empty($lang_val) || trim($lang_val) == ""){
				$this->error['title'][$lang_key] = $this->language->get('error_title');
			}
		}

		return !$this->error;
	}

	public function order() {

		if ($this->config->get('payment_stripepro_status')) {

			$this->load->model('extension/payment/stripepro');

			$stripepro_order = $this->model_extension_payment_stripepro->getOrder($this->request->get['order_id']);

			if (!empty($stripepro_order)) {

				try {
					// load stripe libraries
					$this->load->library('stripe');
			
					if ($stripepro_order['environment'] == 'live') {
						$stripepro_secret_key = $this->config->get('payment_stripepro_live_secret_key');
					} else {
						$stripepro_secret_key = $this->config->get('payment_stripepro_test_secret_key');
					}

					if ($stripepro_secret_key != '' && $stripepro_secret_key != null) {
						\Stripe\Stripe::setApiKey($stripepro_secret_key);

						$payment_intent = \Stripe\PaymentIntent::retrieve($stripepro_order['stripe_charge_id']);
						$balance_transaction = \Stripe\BalanceTransaction::retrieve($payment_intent->charges->data[0]->balance_transaction);

						$data['charge'] = $payment_intent['charges']['data'][0];
						$data['transaction'] = $balance_transaction;

					} else {
						$data['error'] = 'Failed to load stripe libraries!!';
					}

				} catch(\Exception $e) {
					$data['error'] = $e->getMessage();
				}
						
				$data['order_id'] = $this->request->get['order_id'];
				$data['user_token'] = $this->request->get['user_token'];
				$data['stripe_environment'] = $stripepro_order['environment'];

				$this->load->language('extension/payment/stripepro_order');
				return $this->load->view('extension/payment/stripepro_order', $data);
			}
		}
	}

	public function refund() {

		$json = array();
		$json['error'] = false;
		
		if ($this->config->get('payment_stripepro_status')) {

			if (isset($this->request->post['order_id']) && $this->request->post['order_id'] != '' && isset($this->request->post['amount']) && $this->request->post['amount'] != '' && floatval($this->request->post['amount']) > 0) {

				$this->load->model('extension/payment/stripepro');

				$stripepro_order = $this->model_extension_payment_stripepro->getOrder($this->request->post['order_id']);

				if (!empty($stripepro_order)) {

					try {
						// load stripe libraries
						$this->load->library('stripe');
				
						if ($stripepro_order['environment'] == 'live') {
							$stripepro_secret_key = $this->config->get('payment_stripepro_live_secret_key');
						} else {
							$stripepro_secret_key = $this->config->get('payment_stripepro_test_secret_key');
						}

						if ($stripepro_secret_key != '' && $stripepro_secret_key != null) {
							\Stripe\Stripe::setApiKey($stripepro_secret_key);

							$amount_to_refund = $this->request->post['amount'] * 100; // multiply by 100 for cents

							$payment_intent = \Stripe\PaymentIntent::retrieve($stripepro_order['stripe_charge_id']);

							$this->load->model('user/user');
							$user_info = $this->model_user_user->getUser($this->user->getId());

							$payment_intent->charges->data[0]->refund(
								array(
									'amount' => $amount_to_refund,
									 "metadata" => array(
									  	"opencart_user_username" => $user_info['username'],
									  	"opencart_user_id" => $this->user->getId()
									  )
								)
							);

						} else {
							$data['error'] = 'Failed to load stripe libraries!!';
						}

					} catch(\Exception $e) {
						$json = array('error' => true, 'message' => $e->getMessage());
					}
				} else {
					$json = array('error' => true, 'message' => 'Unable to find order data');
				}
			} else {
				$json = array('error' => true, 'message' => 'Invalid request to process refund. Order Id: "'.$this->request->post['order_id'].'", Refund Amount: "'.$this->request->post['amount'].'"');
			}
		} else {
			$json = array('error' => true, 'message' => 'Stripe Pro is not enabled to perform this operation');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}