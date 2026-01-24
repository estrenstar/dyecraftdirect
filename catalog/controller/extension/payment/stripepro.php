<?php
class ControllerExtensionPaymentStripePro extends Controller {

	private $order_info = null; // Order Array
	private $payment_intent = null; // Stripe PaymentIntent Object
	private $_request = null; // request data received from client side

	public function index() {

		// load language
		$this->load->language('extension/payment/stripepro');

		// load model
		$this->load->model('extension/payment/stripepro');

		// load language variables
		$data['text_pay_with_card'] = $this->language->get('text_pay_with_card');
		$data['text_or_pay_with_card'] = $this->language->get('text_or_pay_with_card');
		$data['button_submit_payment'] = $this->language->get('button_submit_payment');

		if ($this->request->server['HTTPS']) {
			$data['store_url'] = HTTPS_SERVER;
		} else {
			$data['store_url'] = HTTP_SERVER;
		}

		if($this->config->get('payment_stripepro_environment') == 'live') {
			$data['payment_stripepro_public_key'] = $this->config->get('payment_stripepro_live_public_key');
			$data['test_mode'] = false;
		} else {
			$data['payment_stripepro_public_key'] = $this->config->get('payment_stripepro_test_public_key');
			$data['test_mode'] = true;
		}

		if ($this->customer->isLogged() && $this->config->get('payment_stripepro_store_card')) {
			$data['store_card_allow'] = true;
			$data['store_card_prechecked'] = $this->config->get('payment_stripepro_store_card_prechecked');
			$data['cards'] = $this->model_extension_payment_stripepro->getCards($this->customer->getId());
		} else {
			$data['store_card_allow'] = false;
			$data['store_card_prechecked'] = false;
			$data['cards'] = array();
		}

		// get order info
		$this->load->model('checkout/order');
		$this->order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		// amount to charge for the order
		$amount = $this->currency->format($this->order_info['total'], $this->order_info['currency_code'], $this->order_info['currency_value'], false);

		// multiple by 100 to get value in cents
		$data['amount'] = $amount * 100;
		$data['order_id'] = $this->order_info['order_id'];
		$data['currency'] = $this->order_info['currency_code'];

		// we will use this owner info to send Stripe from client side
		$data['billing_details'] = array(
										'name' => $this->order_info['payment_firstname'] . ' ' . $this->order_info['payment_lastname'],
										'email' => $this->order_info['email'],
										'address' => array(
											'line1'	=> $this->order_info['payment_address_1'],
											'line2'	=> $this->order_info['payment_address_2'],
											'city'	=> $this->order_info['payment_city'],
											'state'	=> $this->order_info['payment_zone'],
											'postal_code' => $this->order_info['payment_postcode'],
											'country' => $this->order_info['payment_iso_code_2']
										)
									);

		// get current language code for locale, this will let Stripe JS elements to know which languages to use for its own elements
		$data['locale'] = $this->language->get('code');

		// handles the XHR request for client side
		$data['action'] = $this->url->link('extension/payment/stripepro/confirm', 'format=json', true);

		// load stripe libraries
		$this->load->model('extension/payment/stripepro');
		$this->model_extension_payment_stripepro->initStripe();

		// Create the PaymentIntent for Apple & Android Pay
		$params = $this->getPaymentIntentParams();
		$payment_intent = \Stripe\PaymentIntent::create($params);
		$data['client_secret'] = $payment_intent->client_secret;

		// render the template
		return $this->load->view('extension/payment/stripepro', $data);
	}


	public function confirm() {
		
		$json = array('error' => 'Server did not get valid request to process.');

		// load model
		$this->load->model('extension/payment/stripepro');

		// load stripe libraries
		$this->load->model('extension/payment/stripepro');
		$this->model_extension_payment_stripepro->initStripe();

		try{

			// need an order_id in session to process
			if(!isset($this->session->data['order_id'])){
				$this->model_extension_payment_stripepro->log(__FILE__, __LINE__, "Session Data ", $this->session->data);
				throw new Exception("Your order seems lost in session. We did not charge your payment. Please contact administrator for more information.");
			}

			// retrieve json from POST body
			$json_str = file_get_contents('php://input');
			$this->_request = json_decode($json_str);
			
			// get order info
			$this->load->model('checkout/order');
			$this->order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

			if(empty($this->order_info)){
				$this->model_extension_payment_stripepro->log(__FILE__, __LINE__, "Order Data ", $this->order_info);
				throw new Exception("Your order seems lost before payment. We did not charge your payment. Please contact administrator for more information.");
			}

			// if payment method id sent from client side (stripe UI)
			// then generate the payment intent
			if (isset($this->_request->payment_method_id)) {

				// create payment intent for payment method
				$this->createPaymentIntent($this->_request->payment_method_id);
			}

			// else if payment intent id sent from client side (stripe UI)
			// then retrieve the intent and charge it
			else if (isset($this->_request->payment_intent_id)) {

				// retrieve payment intent
				$this->setPaymentIntent($this->_request->payment_intent_id);

				// confirm the payment if requires
				if("automatic" != $this->payment_intent->capture_method || "succeeded" != $this->payment_intent->status) {
					$this->payment_intent->confirm();
				}
			}


			// check if intent is found to get in action
			if(!empty($this->payment_intent)) {

				// check if intent required any further action
				if (($this->payment_intent->status == 'requires_source_action' || $this->payment_intent->status == 'requires_action') && $this->payment_intent->next_action->type == 'use_stripe_sdk') {

					// Tell the client to handle the action
					$json = array(
						'requires_action' => true,
						'payment_intent_client_secret' => $this->payment_intent->client_secret
					);
				}

				// payment is success, no further action required
				else if ($this->payment_intent->status == 'succeeded') {
					// The payment didn’t need any additional actions and completed!
					// Handle post-payment fulfillment

					// get order info
					$this->load->model('checkout/order');
					$this->order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

					// charge this customer and update order accordingly
					$charge_result = $this->chargeAndUpdateOrder($this->payment_intent, $this->order_info);

					// set redirect to success or failure page as per payment charge status
					if($charge_result) {
						$json = array('success' => $this->url->link('checkout/success', '', true));
					} else {
						$json = array('error' => 'Payment could not be completed. Please try again.');
					}

				} else {
					// Unexpected Payment Intent Status
					$json = array('error' => 'Invalid PaymentIntent Status ('.$this->payment_intent->status.')');
				}
			}

		} catch (\Stripe\Error\Base $e) {
			// get the error message need to display
			$json = array('error' => $e->getMessage());
			
			$this->model_extension_payment_stripepro->log($e->getFile(), $e->getLine(), "Stripe Pro Exception caught in confirm() method", $e->getMessage());

		} catch (\Exception $e) {
			// get the error message to display to client
			$json = array('error' => $e->getMessage());

			$this->model_extension_payment_stripepro->log($e->getFile(), $e->getLine(), "Exception caught in confirm() method", $e->getMessage());

		}
		
		// send the response to client
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		return;
	}

	/**
	 * get params to create Stripe Payment Intent
	 * @params $payment_method_id
	 * @returns array
	 */
	private function getPaymentIntentParams($payment_method_id = null){

		// amount to charge for the order
		$amount = $this->currency->format($this->order_info['total'], $this->order_info['currency_code'], $this->order_info['currency_value'], false);

		// multiple by 100 to get value in cents
		$amount = $amount * 100;

		$params = array(
					'amount' => $amount,
					'currency' => strtolower($this->order_info['currency_code']),
					'description' => "Charge for Order #".$this->order_info['order_id'],
					'shipping' => array(
										'name' => $this->order_info['shipping_firstname'] . ' ' . $this->order_info['shipping_lastname'],
										'address' => array(
											'line1'	=> $this->order_info['shipping_address_1'],
											'line2'	=> $this->order_info['shipping_address_2'],
											'city'	=> $this->order_info['shipping_city'],
											'state'	=> $this->order_info['shipping_zone'],
											'postal_code' => $this->order_info['shipping_postcode'],
											'country' => $this->order_info['shipping_iso_code_2'],
										)
									),
					'metadata' => array(
										'OrderId' => $this->order_info['order_id'],
										'FirstName' => $this->order_info['payment_firstname'],
										'LastName' => $this->order_info['payment_lastname'],
										'Address' => $this->order_info['payment_address_1'] . ', ' . $this->order_info['payment_address_2'],
										'City' => $this->order_info['payment_city'],
										'Province' => $this->order_info['payment_zone'],
										'PostalCode' => $this->order_info['payment_postcode'],
										'Country' => $this->order_info['payment_iso_code_2'],
										'Email' => $this->order_info['email'],
										'Phone' => $this->order_info['telephone'],
									),
				);

		if(!empty($payment_method_id)) {
			$params['confirmation_method'] = 'manual';
			$params['confirm'] = true;
			$params['payment_method'] = $payment_method_id;
		}

		return $params;
	}


	/**
	 * create and set Stripe Payment Intent
	 * @params $payment_intent_id PaymentIntent Object
	 * @returns void
	 */
	private function createPaymentIntent($payment_method_id){

		if(empty($this->order_info)){
			throw new Exception("Your order seems lost before payment. We did not charge your payment. Please contact administrator for more information.");
		}

		if(empty($payment_method_id)){
			throw new Exception("Can not retrieve Payemnt Intent. payment_method_id passed is found empty");
		}

		// payment intent params
		$params = $this->getPaymentIntentParams($payment_method_id);
		
		// if saved card is used, then we need to pass customer, who belongs to this card
		/*if(isset($this->_request->using_saved_card) && true == $this->_request->using_saved_card) {
			$params['customer'] = $this->getStripeCustomerId($this->order_info['customer_id']);
		}*/

		// if we have this customer on stripe, then we can pass stripe customer id
		if($cust_id = $this->getStripeCustomerId($this->order_info['customer_id'])){
			$params['customer'] = $cust_id;
		}


		// can we store this payment intent for further payments?
		if($this->isStorePaymentMethodAllowed()) {

			// retrieve payment method for card source to store
			$payment_method = \Stripe\PaymentMethod::retrieve($payment_method_id);

			// check if the same payment method has already available
			$available = $this->model_extension_payment_stripepro->getCardByFingerprint($payment_method->card['fingerprint'], $this->order_info['customer_id']);

			// if not available then attach this payment method to customer
			if(!$available) {
				// // update payment intent and attach this to the customer
				$payment_method->attach(
					array(
						'customer' => $this->getStripeCustomerId($this->order_info['customer_id'])
					)
				);

				// add payment method in database
				$this->model_extension_payment_stripepro->addCard($payment_method_id, $payment_method['card'], $this->order_info['customer_id'], $this->config->get('payment_stripepro_environment'));

				// if we are saving this payment method, then we must pass customer when creating intent
				$params['customer'] = $this->getStripeCustomerId($this->order_info['customer_id']);
			}
		}

		// Create the PaymentIntent
		$this->payment_intent = \Stripe\PaymentIntent::create($params);
	}

	
	/**
	 * set Stripe Payment Intent
	 * @params $payment_intent_id PaymentIntent Object
	 * @returns void
	 */
	private function setPaymentIntent($payment_intent_id){

		if(empty($payment_intent_id)){
			throw new Exception("Can not retrieve Payemnt Intent. payment_intent_id found empty");
		}

		// retrieve payment intent
		$this->payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
	}

	/**
	 * Retrieve Stripe Customer Id from Database
	 * @params $customer_id int
	 * @returns int
	 */
	private function getStripeCustomerId($customer_id) {
		$oc_stripe_customer = $this->model_extension_payment_stripepro->getCustomer($customer_id, $this->config->get('payment_stripepro_environment'));
		return isset($oc_stripe_customer['stripe_customer_id'])? $oc_stripe_customer['stripe_customer_id'] : 0;
	}


	/**
	 * check if customer and configuration allows to save card
	 * @returns boolean
	 */
	private function isStorePaymentMethodAllowed(){
		
		/******************************************************
			save & attach payment method to customer if allowed
		******************************************************/

		// we will attach and save this card if allowed
		if ($this->config->get('payment_stripepro_store_card')
			&& isset($this->_request->store_my_card)
			&& true == $this->_request->store_my_card
			&& (!isset($this->_request->using_saved_card) || true != $this->_request->using_saved_card)) {

			// can not save card without customer id
			if (empty($this->order_info['customer_id'])) {
				return false;
			}

			$customer_id = $this->order_info['customer_id'];
			$stripepro_environment = $this->config->get('payment_stripepro_environment');

			// load required model
			$this->load->model('extension/payment/stripepro');
			$this->load->model('account/customer');

			// get customer id for stripe
			$stripe_customer_id = $this->getStripeCustomerId($customer_id);

			// if this customer is not on stripe
			if (!$stripe_customer_id) {

				// get opencart customer
				$customer_info = $this->model_account_customer->getCustomer($customer_id);

				// prepare customer params for Stripe
				$customer_params = array(
					'email' => $customer_info['email'],
					'metadata' => array(
						'customerId' => $customer_id,
					),
				);

				// create customer on stripe
				$_stripe_customer = \Stripe\Customer::create($customer_params);

				// insert this customer in stripe customer table
				$this->model_extension_payment_stripepro->addCustomer($_stripe_customer, $customer_id, $stripepro_environment);

				// now we have this customer on stripe and in our database
				$stripe_customer_id = $this->getStripeCustomerId($customer_id);
			}
		}

		return !empty($stripe_customer_id)? true : false;
	}


	/**
	 * charge the source and update order accordingly
	 * @params $intent PaymentIntent Object
	 * @params $order_info Order Array
	 * @returns boolean
	 */
	private function chargeAndUpdateOrder($intent, $order_info){

		if(isset($intent->id)) {

			// insert stripe order
			$message = 'Payment Intent ID: '.$intent->id. PHP_EOL .'Status: '. $intent->status;

			$this->load->model('checkout/order');

			// update order statatus & addOrderHistory
			// paid will be true if the charge succeeded, or was successfully authorized for later capture.
			if($intent->status == "succeeded") {
				$this->model_checkout_order->addOrderHistory($this->order_info['order_id'], $this->config->get('payment_stripepro_order_success_status_id'), $message, false);
			} else {
				$this->model_checkout_order->addOrderHistory($this->order_info['order_id'], $this->config->get('payment_stripepro_order_failed_status_id'), $message, false);
			}
			
			// load model
			$this->load->model('extension/payment/stripepro');
			
			// insert order into stripe table
			$this->model_extension_payment_stripepro->addOrder($this->order_info, $intent->id, $this->config->get('payment_stripepro_environment'));

			// charge completed successfully
			return true;
		
		} else {
			// charge could not be completed
			return false;
		}
	}


	/**
	 * Display list of customer's saved cards
	 * @returns void
	 */
	public function saved_cards(){
		
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('extension/payment/stripepro/saved_cards', '', true);
			$this->response->redirect($this->url->link('account/login', '', true));
			return;
		}

		// load language
		$this->load->language('account/account');
		$this->load->language('extension/payment/stripepro');

		$this->document->setTitle($this->language->get('text_saved_cards'));

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_saved_cards'),
			'href' => $this->url->link('extension/payment/stripepro', '', true)
		);

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

		$data['cards'] = array();

		// load model
		$this->load->model('extension/payment/stripepro');
		$cards = $this->model_extension_payment_stripepro->getCards($this->customer->getId());

		if($cards){
			foreach($cards as &$card){
				$card['delete'] = $this->url->link('extension/payment/stripepro/delete_card', 'uid=' . $card['id'], true);
			}

			$data['cards'] = $cards;
		}

		$data['back'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		// render the template
		$this->response->setOutput($this->load->view('extension/payment/stripepro_saved_cards', $data));
	}

	
	/**
	 * Delete customer card
	 * @returns void
	 */
	public function delete_card(){

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('extension/payment/stripepro/saved_cards', '', true);
			$this->response->redirect($this->url->link('account/login', '', true));
			return;
		}

		// load model
		$this->load->model('extension/payment/stripepro');
		$this->model_extension_payment_stripepro->deleteCard($this->request->get['uid']);

		// load language
		$this->load->language('extension/payment/stripepro');
		$this->session->data['success'] = $this->language->get('text_delete');

		// render the template
		$this->response->redirect($this->url->link('extension/payment/stripepro/saved_cards', '', true));
	}
}