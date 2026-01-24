<?php
class ControllerExtensionModuleHbDownload extends Controller {
	protected $registry;
	private $error = array(); 
	
	public function __construct($registry) {
		$this->registry = $registry;
		$this->hb_extension_version	= '3.1.0';		
		$this->doc_link = 'https://www.huntbee.com/resources/docs/downloads-manager';

		$this->load->model('extension/module/hb_download');		
		$this->load->language('extension/module/hb_download');
	}
	
	public function index() {
		$data['extension_version'] =  $this->hb_extension_version;
		
		$data['store_id'] = 0;
		
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_download', $this->request->post, $data['store_id']);	
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/module/hb_download', 'user_token=' . $this->session->data['user_token'].'&store_id='.$data['store_id'], true));
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
	
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
   		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/module/hb_download', 'user_token=' . $this->session->data['user_token'].'&store_id='.$data['store_id'], true)
   		);
		
		$data['action'] = $this->url->link('extension/module/hb_download', 'user_token=' . $this->session->data['user_token'].'&store_id='.$data['store_id'], true);		
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		$data['clear']	= $this->url->link('extension/module/hb_download/clear_logs', 'user_token=' . $this->session->data['user_token'], true);

		$data['user_token'] = $this->session->data['user_token'];
		$data['doc_link']	= $this->doc_link;		
		
		$store_info = $this->model_setting_setting->getSetting('hb_download', $data['store_id']);

		//settings		
		$data['hb_download_sort']	= isset($store_info['hb_download_sort']) ? $store_info['hb_download_sort'] : 'd.date_modified';
		$data['hb_download_order']	= isset($store_info['hb_download_order']) ? $store_info['hb_download_order'] : 'DESC';

		$data['header'] 		= $this->load->controller('common/header');
		$data['column_left'] 	= $this->load->controller('common/column_left');
		$data['footer'] 		= $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/oc3/hb_download', $data));
	}

	public function list() {  
		$page 	= (isset($this->request->get['page']))? $this->request->get['page'] : 1;

		if (isset($this->request->get['sort'])){
			$sort = $this->request->get['sort'];
		}elseif ($this->config->get('hb_download_sort')){
			$sort = $this->config->get('hb_download_sort');
		}else{
			$sort = 'dd.name';
		}

		if (isset($this->request->get['order'])){
			$order = $this->request->get['order'];
		}elseif ($this->config->get('hb_download_order')){
			$order = $this->config->get('hb_download_order');
		}else{
			$order = 'ASC';
		}

		$search = (isset($this->request->get['search']))? $this->request->get['search'] : '';
		
		$limit = ($this->config->get('config_limit_admin')) ? $this->config->get('config_limit_admin') : 50;

		$filter_data = array(
			'start' 		=> ($page - 1) * $limit,
			'limit' 		=> $limit,
			'search'		=> strtolower($search),
			'sort'			=> $sort,
			'order'			=> $order
		);

		$data['user_token'] = $this->session->data['user_token'];	
		
		$download_total = $this->model_extension_module_hb_download->getTotalDownloads($data); 		
		$results = $this->model_extension_module_hb_download->getDownloads($filter_data);

		$data['downloads'] = [];

		foreach ($results as $result) {
			$products = $this->model_extension_module_hb_download->getProductsByDownloadId($result['download_id']);

			$data['downloads'][] = array(
				'download_id' 		=> $result['download_id'],
				'name'        		=> $result['name'],
				'products'  		=> $products,
				'download_count' 	=> $result['download_count'],
				'date_added'  		=> date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified' 	=> date($this->language->get('date_format_short'), strtotime($result['date_modified']))
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $download_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/module/hb_download/list', 'user_token=' . $this->session->data['user_token'] . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$data['type'] = 'product';

		$this->response->setOutput($this->load->view('extension/module/oc3/hb_download_list', $data));
	}

	public function ghost(){
		$data['user_token'] = $this->session->data['user_token'];

		$data['ghost_files'] = $this->model_extension_module_hb_download->getGhostFiles();

		$this->response->setOutput($this->load->view('extension/module/oc3/hb_download_ghost', $data));
	}

	public function form(){
		$data['user_token'] = $this->session->data['user_token'];

		$download_id = (isset($this->request->get['download_id']))? $this->request->get['download_id'] : 0;
		$data['download_id'] = $download_id;

		if ($download_id > 0) {
			$download_info = $this->model_extension_module_hb_download->getDownload($download_id);

			$data['description'] = $download_info['name'];
			$data['filename'] = $download_info['filename'];
			$data['mask'] = $download_info['mask'];
		}else{
			$data['description'] = '';
			$data['filename'] = '';
			$data['mask'] = '';
		}
		
		$this->response->setOutput($this->load->view('extension/module/oc3/hb_download_form', $data));
	}

	public function product_form(){
		$data['user_token'] = $this->session->data['user_token'];

		$download_id = (isset($this->request->get['download_id']))? $this->request->get['download_id'] : 0;

		$data['download_id'] = $download_id;

		if ($download_id > 0) {
			$products = $this->model_extension_module_hb_download->getProductsByDownloadId($download_id);
		}else{
			$products = array();
		}

		$data['products'] = [];

		foreach ($products as $product){
			$data['products'][] = array(
				'product_id' => $product['product_id'],
				'name'       => $product['name']
			);
		}		

		$this->response->setOutput($this->load->view('extension/module/oc3/hb_download_product_form', $data));
	}

	public function save_product(){
		$json =[];

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$download_id = (isset($this->request->post['download_id']))? $this->request->post['download_id'] : 0;

			$this->log->write($this->request->post);

			if ($download_id > 0) {
				$products = (isset($this->request->post['product_related'])) ? $this->request->post['product_related'] : [];

				if ($products) {
					foreach ($products as $product_id) {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "' AND download_id = '".(int)$download_id."'");
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
					}
				}else{
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE download_id = '".(int)$download_id."'");
				}				

				$json['success'] = $this->language->get('success_product_updated');
			}else{
				$json['error'] = $this->language->get('error_incorrect_download_id');
			}
		}else{
			$json['error'] = $this->error['warning'];
		}				

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function save(){
		$json =[];

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$download_id = (isset($this->request->post['download_id']))? $this->request->post['download_id'] : 0;

			if ($download_id > 0) {
				$this->model_extension_module_hb_download->editDownload($download_id, $this->request->post);
			}else{
				$this->model_extension_module_hb_download->addDownload($this->request->post);
			}

			$json['success'] = $this->language->get('text_success');

		}else{
			$json['error'] = $this->error['warning'];
		}				

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
			
	public function delete(){
		$json = [];
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateDelete()) {
			$selected = (isset($this->request->post['selected']))? $this->request->post['selected'] : [];
			foreach ($selected as $download_id) {
				$this->model_extension_module_hb_download->deleteDownload($download_id);
			}

			$json['success'] = $this->language->get('text_success');
		}else{
			$json['error'] = $this->error['warning'];
		}				

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function clear_ghost_files(){
		$json = [];

		if (!$this->validate()) {
			$json['error'] = $this->error['warning'];
		}

		if (!$json) {
			$path    = DIR_DOWNLOAD;
			$files = scandir($path);
			$files = array_diff(scandir($path), array('.', '..', 'index.html'));
			
			foreach ($files as $file) {
				$query = $this->db->query("SELECT filename FROM ".DB_PREFIX."download WHERE filename = '".$this->db->escape($file)."' LIMIT 1");
				if (!$query->row) {
					$removal = DIR_DOWNLOAD.$file; 
					unlink($removal);
				}
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));		
	}

	public function install(){
		$this->model_extension_module_hb_download->install();
	}
	
	public function uninstall(){
		$this->model_extension_module_hb_download->uninstall();
	}
	
	public function update(){
		$this->model_extension_module_hb_download->update();
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/hb_download')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}	

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/download')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['description']) < 3) || (utf8_strlen($this->request->post['description']) > 64)) {
			$this->error['warning'] = $this->language->get('error_name');
		}

		if ((utf8_strlen($this->request->post['filename']) < 3) || (utf8_strlen($this->request->post['filename']) > 128)) {
			$this->error['warning'] = $this->language->get('error_filename');
		}

		if (!is_file(DIR_DOWNLOAD . $this->request->post['filename'])) {
			$this->error['warning'] = $this->language->get('error_exists');
		}

		if ((utf8_strlen($this->request->post['mask']) < 3) || (utf8_strlen($this->request->post['mask']) > 128)) {
			$this->error['warning'] = $this->language->get('error_mask');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/download')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['selected'])) {
			$this->error['warning'] = $this->language->get('error_selected');
		}else{
			$this->load->model('catalog/product');

			foreach ($this->request->post['selected'] as $download_id) {
				$product_total = $this->model_catalog_product->getTotalProductsByDownloadId($download_id);

				if ($product_total) {
					$this->error['warning'] = sprintf($this->language->get('error_product'), $product_total);
				}
			}
		}	

		return !$this->error;
	}
	
}
?>