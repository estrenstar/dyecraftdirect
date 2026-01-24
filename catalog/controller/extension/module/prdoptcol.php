<?php
class ControllerExtensionModuleprdoptcol extends Controller {
	private $modpath = 'module/prdoptcol'; 
	private $modvar = 'model_module_prdoptcol';
	public function __construct($registry) {		
		parent::__construct($registry);		
		ini_set("serialize_precision", -1);		
		if(substr(VERSION,0,3)=='2.3' || substr(VERSION,0,3)=='3.0') {
			$this->modpath = 'extension/module/prdoptcol';
			$this->modvar = 'model_extension_module_prdoptcol';
		}
		if(substr(VERSION,0,3)=='4.0') {
			$this->modpath = 'extension/prdoptcol/module/prdoptcol';
			$this->modvar = 'model_extension_prdoptcol_module_prdoptcol';
		}
 	}
	public function getcache() {
		$this->load->model($this->modpath);
		$this->{$this->modvar}->getcache();
	}
	public function loadjscss(&$route, &$data, &$output = '') {
		$this->load->model($this->modpath);
		$this->{$this->modvar}->loadjscss();
	}
}