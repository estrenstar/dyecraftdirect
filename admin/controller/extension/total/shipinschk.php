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
	public function index() {
		$this->load->model($this->modpath);
		$this->{$this->modvar}->index();
	}	
	public function save() {
		$this->load->model($this->modpath);
		$this->{$this->modvar}->save();
	}	
	public function install() {
		$this->load->model($this->modpath);
		$this->{$this->modvar}->install();
	}
	public function uninstall() {
		$this->load->model($this->modpath);
		$this->{$this->modvar}->uninstall();
	}
	public function addmenu(&$route, &$data, &$output = '') {
		$this->load->model($this->modpath);
		$data = $this->{$this->modvar}->addmenu($data);		
	}
	public function loadjscss(&$route, &$data, &$output = '') {
		$this->load->model($this->modpath);
		$this->{$this->modvar}->loadjscss();
	}
}