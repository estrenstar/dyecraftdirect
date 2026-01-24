<?php
class ControllerExtensionshipinschkhistory extends Controller {
	private $modpath = 'extension/shipinschkhistory';
	private $modvar = 'model_extension_shipinschkhistory';
	public function __construct($registry) {
		parent::__construct($registry);
		ini_set("serialize_precision", -1);	
		if(substr(VERSION,0,3)=='4.0') {
			$this->modpath = 'extension/shipinschk/extension/shipinschkhistory';			
			$this->modvar = 'model_extension_shipinschk_extension_shipinschkhistory';
		}
 	} 
	public function index() {
		$lang = $this->load->language($this->modpath);
 		$this->document->setTitle($this->language->get('heading_title'));
 		$this->load->model($this->modpath);
 		$this->getList();
	}
	public function delete() {
		$this->load->model($this->modpath);
		$this->{$this->modvar}->deleteshipinschkhistory();
		$this->getList();		
	}
	protected function getList() {
		$this->load->model($this->modpath);
		$this->{$this->modvar}->getListHTML();
	}
}