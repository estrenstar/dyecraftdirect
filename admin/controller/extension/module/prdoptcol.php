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
	public function getflaghtmlJS(&$route, &$data, &$output = '') {				
		$this->load->model($this->modpath);
		$findcode = '<body>';
		$replace_code = $this->{$this->modvar}->getflaghtmlJS();	
		$output = str_replace($findcode, $replace_code . $findcode, $output);	
	}
	public function loadjscss(&$route, &$data, &$output = '') {
		$this->load->model($this->modpath);
		$this->{$this->modvar}->loadjscss();
	}
}