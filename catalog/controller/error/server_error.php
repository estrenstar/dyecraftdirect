<?php
class ControllerErrorServerError extends Controller {
	public function index() {
		$this->load->language('error/not_found');

		$this->document->setTitle($this->language->get('heading_title'));
		
		// Add robots meta tag to prevent indexing 5xx error pages
		$this->document->addMeta('robots', array('content' => 'noindex,follow'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => 'Server Error',
			'href' => $this->url->link('error/server_error')
		);

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
		$this->response->addHeader('X-Robots-Tag: noindex,follow');

		$this->response->setOutput($this->load->view('error/not_found', $data));
	}
}
