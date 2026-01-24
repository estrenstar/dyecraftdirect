<?php
class ControllerExtensionTotalshipinschk extends Controller {
	private $modpath = 'total/shipinschk'; 
	private $modvar = 'model_total_shipinschk';
	public function __construct($registry) {		
		parent::__construct($registry);		
		ini_set("serialize_precision", -1);		
		if(substr(VERSION,0,3)=='2.3' || substr(VERSION,0,3)=='3.0') {
			$this->modpath = 'extension/total/shipinschk';
			$this->modvar = 'model_extension_total_shipinschk';
		}
		if(substr(VERSION,0,3)=='4.0') {
			$this->modpath = 'extension/shipinschk/total/shipinschk';
			$this->modvar = 'model_extension_shipinschk_total_shipinschk';
		}
 	}
	public function getcache() {
		$this->load->model($this->modpath);
		$this->{$this->modvar}->getcache();
	}
	public function ocxresponse(){
		$json = array();
		$json['xtotals'] = $this->load->controller($this->config->get('xtensions_controller_path').'xcvc/xtotals');
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	public function save() {
		$this->load->model($this->modpath);
		$this->{$this->modvar}->save();
	}
	public function saveinhist(&$route, &$args) {
		if(isset($this->session->data['order_id'])) { 
			$this->load->model($this->modpath);
			$this->{$this->modvar}->saveinhist($this->session->data['order_id']);
		} else {
			$this->load->model($this->modpath);
			$this->{$this->modvar}->saveinhist($args[0]);
		}
 	}
	public function loadjscss(&$route, &$data, &$output = '') {
		$this->load->model($this->modpath);
		$this->{$this->modvar}->loadjscss();
	}
}