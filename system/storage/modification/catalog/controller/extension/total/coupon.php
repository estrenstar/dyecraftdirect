<?php
class ControllerExtensionTotalCoupon extends Controller {
	public function index() {
		// Clear Thinking: Super Coupons
				if ($this->config->get('total_coupon_status') || ($this->config->get('total_super_coupons_status') && $this->config->get('total_super_coupons_cart_page_box'))) {
				// end
			$this->load->language('extension/total/coupon');

			if (isset($this->session->data['coupon'])) {
				$data['coupon'] = $this->session->data['coupon'];
			} else {
				$data['coupon'] = '';
			}

			return $this->load->view('extension/total/coupon', $data);
		}
	}

	public function coupon() {
		$this->load->language('extension/total/coupon');

		$json = array();

		$this->load->model('extension/total/coupon');

		if (isset($this->request->post['coupon'])) {
			$coupon = $this->request->post['coupon'];
		} else {
			$coupon = '';
		}

		$coupon_info = $this->model_extension_total_coupon->getCoupon($coupon);

		if (empty($this->request->post['coupon'])) {
			// Clear Thinking: Super Coupons
				$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : 'total_';
				$this->session->data['success'] = $this->config->get($prefix . 'super_coupons_coupon_cleared_' . $this->session->data['language']);
				$json['redirect'] = $this->url->link('checkout/cart');
				unset($this->session->data['coupon']);
				// end

			unset($this->session->data['coupon']);
		} elseif ($coupon_info) {
			$this->session->data['coupon'] = $this->request->post['coupon'];

			$this->session->data['success'] = $this->language->get('text_success');

			$json['redirect'] = $this->url->link('checkout/cart');
		} else {
			$json['error'] = $this->language->get('error_coupon');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
