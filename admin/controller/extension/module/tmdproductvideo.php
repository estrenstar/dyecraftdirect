<?php
//lib
require_once(DIR_SYSTEM.'library/tmd/system.php');
//lib
class ControllerExtensionModuleTmdproductvideo extends Controller {
	private $error = array();
public function install()
	{
	$this->load->model('extension/tmdproductvideo');
	$this->model_extension_tmdproductvideo->install();
	}	
	public function uninstall()
	{
	$this->load->model('extension/tmdproductvideo');
	$this->model_extension_tmdproductvideo->uninstall();
	}
	public function index() {
		$this->registry->set('tmd', new TMD($this->registry));
		$keydata=array(
		'code'=>'tmdkey_tmdproductvideo',
		'eid'=>'MTQ4NzI=',
		'route'=>'extension/module/tmdproductvideo',
		);
		$tmdproductvideo=$this->tmd->getkey($keydata['code']);
		$data['getkeyform']=$this->tmd->loadkeyform($keydata);

		$this->load->language('extension/module/tmdproductvideo');

		$this->document->setTitle($this->language->get('heading_title1'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
				if(isset($this->request->post['tmdproductvideo_status']))
			{
				$status=$this->request->post['tmdproductvideo_status'];
			}
			
			$postdata['module_tmdproductvideo_status']=$status;

			$this->model_setting_setting->editSetting('module_tmdproductvideo', $postdata);

			$this->model_setting_setting->editSetting('tmdproductvideo', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_title'] = $this->language->get('text_title');
		$data['text_tabtitle'] = $this->language->get('text_tabtitle');
		$data['text_intab'] = $this->language->get('text_intab');
		$data['text_outtab'] = $this->language->get('text_outtab');
		$data['text_inrelated'] = $this->language->get('text_inrelated');
		$data['entry_showvideo'] = $this->language->get('entry_showvideo');

		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->session->data['warning'])) {
			$data['error_warning'] = $this->session->data['warning'];
		
			unset($this->session->data['warning']);
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/tmdproductvideo', 'user_token=' . $this->session->data['user_token'], true)
		);
		
		$data['showvideos'] = array();

		$data['showvideos'][] = array(
			'text'  => $this->language->get('text_intab'),
			'value' => '1'
		);
		
		$data['showvideos'][] = array(
			'text'  => $this->language->get('text_outtab'),
			'value' => '0'
		);
		
		$data['showvideos'][] = array(
			'text'  => $this->language->get('text_inrelated'),
			'value' => '2'
		);

		$data['action'] = $this->url->link('extension/module/tmdproductvideo', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['tmdproductvideo_status'])) {
			$data['tmdproductvideo_status'] = $this->request->post['tmdproductvideo_status'];
		} else {
			$data['tmdproductvideo_status'] = $this->config->get('tmdproductvideo_status');
		}

		if (isset($this->request->post['tmdproductvideo_title'])) {
			$data['tmdproductvideo_title'] = $this->request->post['tmdproductvideo_title'];
		} else {
			$data['tmdproductvideo_title'] = $this->config->get('tmdproductvideo_title');
		}

		if (isset($this->request->post['tmdproductvideo_tabtitle'])) {
			$data['tmdproductvideo_tabtitle'] = $this->request->post['tmdproductvideo_tabtitle'];
		} else {
			$data['tmdproductvideo_tabtitle'] = $this->config->get('tmdproductvideo_tabtitle');
		}

		if (isset($this->request->post['tmdproductvideo_showintab'])) {
			$data['tmdproductvideo_showintab'] = $this->request->post['tmdproductvideo_showintab'];
		} else {
			$data['tmdproductvideo_showintab'] = $this->config->get('tmdproductvideo_showintab');
		}


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/tmdproductvideo', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/tmdproductvideo')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		$tmdproductvideo=$this->config->get('tmdkey_tmdproductvideo');
		if (empty(trim($tmdproductvideo))) {			
		$this->session->data['warning'] ='Module will Work after add License key!';
		$this->response->redirect($this->url->link('extension/module/tmdproductvideo', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		return !$this->error;
	}
	
	public function keysubmit() {
		$json = array(); 
		
      	if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$keydata=array(
			'code'=>'tmdkey_tmdproductvideo',
			'eid'=>'MTQ4NzI=',
			'route'=>'extension/module/tmdproductvideo',
			'moduledata_key'=>$this->request->post['moduledata_key'],
			);
			$this->registry->set('tmd', new TMD($this->registry));
            $json=$this->tmd->matchkey($keydata);       
		} 
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}