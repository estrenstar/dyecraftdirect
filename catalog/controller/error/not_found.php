<?php
class ControllerErrorNotFound extends Controller {
	public function index() {
		$this->load->language('error/not_found');

		$this->document->setTitle($this->language->get('heading_title'));
		
		// Add robots meta tag to prevent indexing 404 pages
		$this->document->addMeta('robots', array('content' => 'noindex,follow'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if (isset($this->request->get['route'])) {
			$url_data = $this->request->get;

			unset($url_data['_route_']);

			$route = $url_data['route'];

			unset($url_data['route']);

			$url = '';

			if ($url_data) {
				$url = '&' . urldecode(http_build_query($url_data, '', '&'));
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link($route, $url, $this->request->server['HTTPS'])
			);
		}

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		// Log 404 errors for monitoring
		if (isset($this->request->server['REQUEST_URI'])) {
			$this->load->model('tool/error_log');
			$error_data = array(
				'route' => isset($this->request->get['route']) ? $this->request->get['route'] : 'unknown',
				'url' => $this->request->server['REQUEST_URI'],
				'referrer' => isset($this->request->server['HTTP_REFERER']) ? $this->request->server['HTTP_REFERER'] : '',
				'user_agent' => isset($this->request->server['HTTP_USER_AGENT']) ? substr($this->request->server['HTTP_USER_AGENT'], 0, 255) : '',
				'date_added' => date('Y-m-d H:i:s')
			);
		}

		$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');
		$this->response->addHeader('X-Robots-Tag: noindex,follow');

		$this->response->setOutput($this->load->view('error/not_found', $data));
	}
}