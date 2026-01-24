<?php
class ModelExtensionModuleprdoptcol extends Model {
	private $divcls = 'form-group';
	private $lblcls = 'control-label';
	private $wellcls = 'well well-sm';
	private $grpcls = 'input-group-addon';
	private $slctcls = 'form-control';
	
	private $modpath = 'module/prdoptcol'; 
	private $modvar = 'model_module_prdoptcol';
	private $modname = 'prdoptcol';
	private $modurl = 'extension/module';
	private $modsprtor = '/';
	
	private $evntcode = 'prdoptcol';
	private $error = array();
	private $token = '';
	private $pglimit = 50;
	private $urlval = '';
	private $urlfilter = array();
	
	public function __construct($registry) {		
		parent::__construct($registry);			
		ini_set("serialize_precision", -1);
		
		if(isset($this->session->data['token'])) { $this->token = 'token=' . $this->session->data['token']; }
		if(isset($this->session->data['user_token'])) { $this->token = 'user_token=' . $this->session->data['user_token']; }
		
		$this->pglimit = $this->config->get('config_limit_admin') ? $this->config->get('config_limit_admin') : $this->config->get('config_pagination_admin');
		
		foreach($this->urlfilter as $urlval) {
			if (isset($this->request->get[$urlval])) {
				$this->urlval .= '&'.$urlval.'=' . urlencode(html_entity_decode($this->request->get[$urlval], ENT_QUOTES, 'UTF-8'));
			}			
		}
		
		if (isset($this->request->get['sort'])) { $this->urlval .= '&sort=' . $this->request->get['sort']; }
		if (isset($this->request->get['order'])) { $this->urlval .= '&order=' . $this->request->get['order'];}
		if (isset($this->request->get['page'])) { $this->urlval .= '&page=' . $this->request->get['page'];}
		
		if(substr(VERSION,0,3)=='2.3') {
			$this->modpath = 'extension/module/prdoptcol';
			$this->modvar = 'model_extension_module_prdoptcol';
			$this->modurl = 'extension/extension';
		}
		if(substr(VERSION,0,3)=='3.0') {			
			$this->modpath = 'extension/module/prdoptcol';
			$this->modvar = 'model_extension_module_prdoptcol';
			$this->modurl = 'marketplace/extension'; 
			$this->modname = 'module_prdoptcol';			
		}
		if(substr(VERSION,0,3)=='4.0') {
			$this->modpath = 'extension/prdoptcol/module/prdoptcol';
			$this->modvar = 'model_extension_prdoptcol_module_prdoptcol';
			$this->modurl = 'marketplace/extension'; 
			$this->modname = 'module_prdoptcol';
			$this->modsprtor = '.';
			$this->divcls = 'row mb-3';
			$this->lblcls = 'col-form-label';
			$this->wellcls = 'form-control';
			$this->grpcls = 'input-group-text';
			$this->slctcls = 'form-select';
		}
 	}
	public function index() {
		$data = $this->load->language($this->modpath);
		$lang = $this->load->language($this->modpath);
		
		$data['langs'] = $this->getLang();
		$data['stores'] = $this->getStores();
		$data['cgs'] = $this->getCustomerGroups();
		$data['ordsts'] = $this->getOrderStatuses();		

		$this->document->setTitle($data['heading_title']);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->modifypermission() && substr(VERSION,0,3)!='4.0') {
			$this->load->model('setting/setting');
 			$this->model_setting_setting->editSetting($this->modname, $this->request->post);
 			$this->session->data['success'] = $this->language->get('text_success');
 			$this->response->redirect($this->url->link($this->modpath, $this->token, true));
		}
 
		$data['error_warning'] = '';
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		}
		
		$data['text_success'] = '';
		if (isset($this->session->data['success'])) {
			$data['text_success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link($this->modpath, $this->token, true)
		);
		
		$data['action'] = $this->url->link($this->modpath, $this->token, true);
		$data['cancel'] = $this->url->link($this->modurl, $this->token, true); 
				 
		if(substr(VERSION,0,3)=='4.0') {
			$data['action'] = $this->url->link($this->modpath. $this->modsprtor.'save', $this->token);
			$data['cancel'] = $this->url->link($this->modurl, $this->token);
		}
		
		$html = array();
		
		$data[$this->modname.'_status'] = $this->setcnfgvalue($this->modname.'_status');
		$data[$this->modname.'_setting'] = $this->setcnfgvalue($this->modname.'_setting');
		if(empty($data[$this->modname.'_setting'])) {
			$data[$this->modname.'_setting'] = array();
		}
		
		foreach($data['stores'] as $store) {
			if(! isset($data[$this->modname.'_setting'][$store['store_id']]['status']) ) {
				$data[$this->modname.'_setting'][$store['store_id']]['status'] = 0;
			}	
			if(! isset($data[$this->modname.'_setting'][$store['store_id']]['dispgrd']) ) {
				$data[$this->modname.'_setting'][$store['store_id']]['dispgrd'] = 0;
			}	
			if(! isset($data[$this->modname.'_setting'][$store['store_id']]['disptypegrd']) ) {
				$data[$this->modname.'_setting'][$store['store_id']]['disptypegrd'] = 0;
			}
			if(! isset($data[$this->modname.'_setting'][$store['store_id']]['gridw']) ) {
				$data[$this->modname.'_setting'][$store['store_id']]['gridw'] = 200;
			}
			if(! isset($data[$this->modname.'_setting'][$store['store_id']]['gridh']) ) {
				$data[$this->modname.'_setting'][$store['store_id']]['gridh'] = 200;
			}
			if(! isset($data[$this->modname.'_setting'][$store['store_id']]['gridcolw']) ) {
				$data[$this->modname.'_setting'][$store['store_id']]['gridcolw'] = 30;
			}
			if(! isset($data[$this->modname.'_setting'][$store['store_id']]['gridcolh']) ) {
				$data[$this->modname.'_setting'][$store['store_id']]['gridcolh'] = 30;
			}	
			
			if(! isset($data[$this->modname.'_setting'][$store['store_id']]['disptypeprd']) ) {
				$data[$this->modname.'_setting'][$store['store_id']]['disptypeprd'] = 0;
			}
			if(! isset($data[$this->modname.'_setting'][$store['store_id']]['prdw']) ) {
				$data[$this->modname.'_setting'][$store['store_id']]['prdw'] = 228;
			}
			if(! isset($data[$this->modname.'_setting'][$store['store_id']]['prdh']) ) {
				$data[$this->modname.'_setting'][$store['store_id']]['prdh'] = 228;
			}
			if(! isset($data[$this->modname.'_setting'][$store['store_id']]['popw']) ) {
				$data[$this->modname.'_setting'][$store['store_id']]['popw'] = 500;
			}
			if(! isset($data[$this->modname.'_setting'][$store['store_id']]['poph']) ) {
				$data[$this->modname.'_setting'][$store['store_id']]['poph'] = 500;
			}
			if(! isset($data[$this->modname.'_setting'][$store['store_id']]['prdaddw']) ) {
				$data[$this->modname.'_setting'][$store['store_id']]['prdaddw'] = 74;
			}
			if(! isset($data[$this->modname.'_setting'][$store['store_id']]['prdaddh']) ) {
				$data[$this->modname.'_setting'][$store['store_id']]['prdaddh'] = 74;
			}
			if(! isset($data[$this->modname.'_setting'][$store['store_id']]['prdcolw']) ) {
				$data[$this->modname.'_setting'][$store['store_id']]['prdcolw'] = 40;
			}
			if(! isset($data[$this->modname.'_setting'][$store['store_id']]['prdcolh']) ) {
				$data[$this->modname.'_setting'][$store['store_id']]['prdcolh'] = 40;
			}
			
			foreach($data['langs'] as $lng) {
				if(! isset($data[$this->modname.'_setting'][$store['store_id']]['rstbtntxt'][$lng['language_id']]) ) {
					$data[$this->modname.'_setting'][$store['store_id']]['rstbtntxt'][$lng['language_id']] = 'Reset';
				}
			}						
						
			if(substr(VERSION,0,3)=='4.0') {
				$html[] = sprintf('<div class="card"><div class="card-body"><h3 class="card-title">%s</h3>', $store['name']);
			} else {
				$html[] = sprintf('<div class="panel panel-primary"><div class="panel-heading">%s</div><div class="panel-body">', $store['name']);
			}
			
			$name = sprintf($this->modname.'_setting[%s][%s]', $store['store_id'], 'status');
			$val = $data[$this->modname.'_setting'][$store['store_id']]['status'];
			$html[] = $this->get_RDO_html($name, $val, $data['entry_status'], array(1=>$data['text_yes'], 0=>$data['text_no'])); 
			
 			$name = sprintf($this->modname.'_setting[%s][%s]', $store['store_id'], 'rstbtntxt');
			$val = $data[$this->modname.'_setting'][$store['store_id']]['rstbtntxt'];
			$html[] = $this->get_InpTxt_LANG_html($name, $val, $lang['entry_rstbtntxt']);
			
			
			$html[] = sprintf('<hr><h3>%s</h3>', $lang['txt_grid']);
			$name = sprintf($this->modname.'_setting[%s][%s]', $store['store_id'], 'dispgrd');
			$val = $data[$this->modname.'_setting'][$store['store_id']]['dispgrd'];
			$html[] = $this->get_RDO_html($name, $val, $lang['entry_dispgrd'], array(1=>$data['text_yes'], 0=>$data['text_no']));
			
			$name = sprintf($this->modname.'_setting[%s][%s]', $store['store_id'], 'disptypegrd');
			$val = $data[$this->modname.'_setting'][$store['store_id']]['disptypegrd'];
			$html[] = $this->get_RDO_html($name, $val, $lang['entry_disptype'], array(1=>$data['txt_circle'], 0=>$data['txt_square']));
			
			//Product Image
			$name = sprintf($this->modname.'_setting[%s][%s]', $store['store_id'], 'gridw');
			$val = $data[$this->modname.'_setting'][$store['store_id']]['gridw'];
			$html[] = $this->get_InpTxt_html($name, $val, $lang['entry_gridwh'] . $lang['txt_width']);
			
			$name = sprintf($this->modname.'_setting[%s][%s]', $store['store_id'], 'gridh');
			$val = $data[$this->modname.'_setting'][$store['store_id']]['gridh'];
			$html[] = $this->get_InpTxt_html($name, $val, $lang['entry_gridwh'] . $lang['txt_height']);
			
			//Color Image
			$name = sprintf($this->modname.'_setting[%s][%s]', $store['store_id'], 'gridcolw');
			$val = $data[$this->modname.'_setting'][$store['store_id']]['gridcolw'];
			$html[] = $this->get_InpTxt_html($name, $val, $lang['entry_gridcolwh'] . $lang['txt_width']);
			
			$name = sprintf($this->modname.'_setting[%s][%s]', $store['store_id'], 'gridcolh');
			$val = $data[$this->modname.'_setting'][$store['store_id']]['gridcolh'];
			$html[] = $this->get_InpTxt_html($name, $val, $lang['entry_gridcolwh'] . $lang['txt_height']);
			
			
			$html[] = sprintf('<hr><h3>%s</h3>', $lang['txt_prd']);
			
			$name = sprintf($this->modname.'_setting[%s][%s]', $store['store_id'], 'disptypeprd');
			$val = $data[$this->modname.'_setting'][$store['store_id']]['disptypeprd'];
			$html[] = $this->get_RDO_html($name, $val, $lang['entry_disptype'], array(1=>$data['txt_circle'], 0=>$data['txt_square']));
			//Product Image
			$name = sprintf($this->modname.'_setting[%s][%s]', $store['store_id'], 'prdw');
			$val = $data[$this->modname.'_setting'][$store['store_id']]['prdw'];
			$html[] = $this->get_InpTxt_html($name, $val, $lang['entry_prdwh'] . $lang['txt_width']);
			
			$name = sprintf($this->modname.'_setting[%s][%s]', $store['store_id'], 'prdh');
			$val = $data[$this->modname.'_setting'][$store['store_id']]['prdh'];
			$html[] = $this->get_InpTxt_html($name, $val, $lang['entry_prdwh'] . $lang['txt_height']);
			
			//Popup Image
			$name = sprintf($this->modname.'_setting[%s][%s]', $store['store_id'], 'popw');
			$val = $data[$this->modname.'_setting'][$store['store_id']]['popw'];
			$html[] = $this->get_InpTxt_html($name, $val, $lang['entry_popwh'] . $lang['txt_width']);
			
			$name = sprintf($this->modname.'_setting[%s][%s]', $store['store_id'], 'poph');
			$val = $data[$this->modname.'_setting'][$store['store_id']]['poph'];
			$html[] = $this->get_InpTxt_html($name, $val, $lang['entry_popwh'] . $lang['txt_height']);
			
			//Additional Image
			$name = sprintf($this->modname.'_setting[%s][%s]', $store['store_id'], 'prdaddw');
			$val = $data[$this->modname.'_setting'][$store['store_id']]['prdaddw'];
			$html[] = $this->get_InpTxt_html($name, $val, $lang['entry_prdaddwh'] . $lang['txt_width']);
			
			$name = sprintf($this->modname.'_setting[%s][%s]', $store['store_id'], 'prdaddh');
			$val = $data[$this->modname.'_setting'][$store['store_id']]['prdaddh'];
			$html[] = $this->get_InpTxt_html($name, $val, $lang['entry_prdaddwh'] . $lang['txt_height']);
			
			//Color Image
			$name = sprintf($this->modname.'_setting[%s][%s]', $store['store_id'], 'prdcolw');
			$val = $data[$this->modname.'_setting'][$store['store_id']]['prdcolw'];
			$html[] = $this->get_InpTxt_html($name, $val, $lang['entry_prdcolwh'] . $lang['txt_width']);
			
			$name = sprintf($this->modname.'_setting[%s][%s]', $store['store_id'], 'prdcolh');
			$val = $data[$this->modname.'_setting'][$store['store_id']]['prdcolh'];
			$html[] = $this->get_InpTxt_html($name, $val, $lang['entry_prdcolwh'] . $lang['txt_height']);
			
			$html[] = '</div></div>';
		}
		
		$data['fields_html'] = join($html);
			
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		
/***************************************** HTML VIEW *************************************************/		
		$html = array();
		
		$html[] = $data['header'];
		$html[] = $data['column_left'];
		
		$html[] = '<div id="content">  <div class="page-header">  <div class="container-fluid">';
		if(substr(VERSION,0,3)=='4.0') {
			$html[] = sprintf('<div class="float-end"> <button type="submit" form="form-prdoptcol" class="btn btn-primary"><i class="fa-solid fa-save"></i></button> <a href="%s" class="btn btn-light"><i class="fa-solid fa-reply"></i></a></div>', $data['cancel']);
		} else {
			$html[] = sprintf('<div class="pull-right"> <button type="submit" form="form-prdoptcol" class="btn btn-primary"><i class="fa fa-save"></i></button> <a href="%s" class="btn btn-default"><i class="fa fa-reply"></i></a></div>', $data['cancel']);			
		}
		
		$html[] = sprintf('<h1>%s</h1>', $data['heading_title']);
		
		if(substr(VERSION,0,3)=='4.0') {
			$html[] = '<ol class="breadcrumb">';
			foreach ($data['breadcrumbs'] as $breadcrumb) {
				$html[] = sprintf('<li class="breadcrumb-item"><a href="%s">%s</a></li>',$breadcrumb['href'], $breadcrumb['text']);
			}
			$html[] = '</ol>';
		} else {
			$html[] = '<ul class="breadcrumb">';
			foreach ($data['breadcrumbs'] as $breadcrumb) {
				$html[] = sprintf('<li><a href="%s">%s</a></li>',$breadcrumb['href'], $breadcrumb['text']);
			}
			$html[] = '</ul>';
		}
		
		$html[] = '</div> </div>';
		
		$html[] = '<div class="container-fluid">';
		if(substr(VERSION,0,3)=='4.0') {
			//
		} else {
			if ($data['error_warning']) {
				$html[] = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> %s <button type="button" class="close" data-dismiss="alert">&times;</button> </div>', $data['error_warning']);
			}
			if ($data['text_success']) {
				$html[] = sprintf('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> %s <button type="button" class="close" data-dismiss="alert">&times;</button> </div>', $data['text_success']);
			}
		}
		
		if(substr(VERSION,0,3)=='4.0') {
			$html[] = sprintf('<div class="card"> <div class="card-header"> <i class="fa-solid fa-pencil"></i> %s </div> <div class="card-body">', $data['text_edit']);
		} else {
			$html[] = sprintf('<div class="panel panel-default"> <div class="panel-heading"> <h3 class="panel-title"><i class="fa fa-pencil"></i> %s </h3> </div> <div class="panel-body">', $data['text_edit']);
		}
		
		$chk = ''; $slct0 = ''; $slct1 = '';
		if($data[$this->modname.'_status']) {
			$chk = 'checked'; $slct1 = 'selected';
		} else {
			$chk = ''; $slct0 = 'selected';
		}
		if(substr(VERSION,0,3)=='4.0') {
			$html[] = sprintf('<form action="%s" method="post" id="form-prdoptcol" data-oc-toggle="ajax">', $data['action']);			
			$html[] = sprintf('<div class="row mb-3"> <label for="input-status" class="col-sm-2 col-form-label">%s</label>', $data['entry_status']);
			$html[] = sprintf('<div class="col-sm-10"> <div class="form-check form-switch form-switch-lg"> 
			<input type="hidden" name="%s_status" value="0"/> 
			<input type="checkbox" name="%s_status" value="1" id="input-status" class="form-check-input" %s/> 
			</div> </div> </div>', $this->modname, $this->modname, $chk);
		} else {
			$html[] = sprintf('<form action="%s" method="post" enctype="multipart/form-data" id="form-prdoptcol" class="form-horizontal">', $data['action']);
			$html[] = sprintf('<div class="form-group"> <label class="col-sm-2 control-label" for="input-status">%s</label>', $data['entry_status']);
			$html[] = sprintf('<div class="col-sm-10"> <select name="%s_status" id="input-status" class="form-control">', $this->modname);
			$html[] = sprintf('<option value="1" %s>%s</option> <option value="0" %s>%s</option>', $slct1, $data['text_enabled'], $slct0, $data['text_disabled']);
			$html[] = '</select> </div> </div>';
		}

		$html[] = $data['fields_html'];
		
		$html[] = ' </form> </div> </div> </div> </div>';
		
		$html[] = $data['footer'];
		
		$this->response->setOutput(join($html));
	}
	public function save() {
		$this->load->language($this->modpath);
 		$json = array();

		if (!$this->user->hasPermission('modify', $this->modpath)) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/setting');
 			$this->model_setting_setting->editSetting($this->modname, $this->request->post);
 			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	public function addmenu($data) {
		$menunm = 'Product Option Color Image';
		$menulink = substr(VERSION,0,3)=='4.0' ? 'extension/prdoptcol/extension/prdoptcol' : 'extension/prdoptcol';		
		if ($this->user->hasPermission('access',  $menulink) && $this->config->get($this->modname.'_status')) {
			$urllink = $this->url->link($menulink, $this->token, true);	
			if(substr(VERSION,0,3)=='2.2') {
				$data['text_module'] .= sprintf('</a></li> <li><a href="%s">%s',$urllink, $menunm);
			} else{				
				foreach ($data['menus'] as &$menu) {
					if (stristr($menu['id'],'extension')) {
						$menu['children'][] = array(
							'name'     => $menunm,
							'href'     => $urllink,
							'children' => array()
						);
					}
				}
			}
		}
		return $data;
	}
	public function getmenuhtmloc2() {
		$menunm = 'Product Option Color Image';
		$menulink = substr(VERSION,0,3)=='4.0' ? 'extension/prdoptcol/extension/prdoptcol' : 'extension/prdoptcol';		
		if ($this->user->hasPermission('access',  $menulink) && $this->config->get($this->modname.'_status')) {
			$urllink = $this->url->link($menulink, $this->token, true);	
			if(substr(VERSION,0,3)<='2.2') {
				return sprintf('</a></li> <li><a href="%s">%s',$urllink, $menunm);
			}
		}
		return '';
	}
	public function install() { 		
		$query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_option` LIKE 'prdoptcolflag' ");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `".DB_PREFIX."product_option` ADD `prdoptcolflag` tinyint(1) ");
		}
		
		$query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_option_value` LIKE 'prdoptcolimg' ");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `".DB_PREFIX."product_option_value` ADD `prdoptcolimg` varchar(255) ");			
		}
		$query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_option_value` LIKE 'prdoptcolcde' ");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `".DB_PREFIX."product_option_value` ADD `prdoptcolcde` varchar(50) ");			
		}
		$query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_option_value` LIKE 'prdoptcolptrn' ");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `".DB_PREFIX."product_option_value` ADD `prdoptcolptrn` varchar(255) ");			
		}
		
		$query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_option_value` LIKE 'prdoptcoladd1' ");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `".DB_PREFIX."product_option_value` ADD `prdoptcoladd1` varchar(255) ");			
		}
		$query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_option_value` LIKE 'prdoptcoladd2' ");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `".DB_PREFIX."product_option_value` ADD `prdoptcoladd2` varchar(255) ");			
		}
		$query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_option_value` LIKE 'prdoptcoladd3' ");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `".DB_PREFIX."product_option_value` ADD `prdoptcoladd3` varchar(255) ");			
		}
		$query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_option_value` LIKE 'prdoptcoladd4' ");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `".DB_PREFIX."product_option_value` ADD `prdoptcoladd4` varchar(255) ");			
		}
		$query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_option_value` LIKE 'prdoptcoladd5' ");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `".DB_PREFIX."product_option_value` ADD `prdoptcoladd5` varchar(255) ");			
		}
		$query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_option_value` LIKE 'prdoptcoladd6' ");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `".DB_PREFIX."product_option_value` ADD `prdoptcoladd6` varchar(255) ");			
		}
		
		$viewtmp = (substr(VERSION,0,3)=='2.2' || substr(VERSION,0,3)=='2.3') ? '*/template/' : '';
		
		$this->addtoevent('admin/view/catalog/product_form/after', $this->modsprtor. 'getflaghtmlJS');
		$this->addtoevent('catalog/controller/common/header/before', $this->modsprtor. 'loadjscss');
 	}
	public function uninstall() {
		if(substr(VERSION,0,3)=='2.2' || substr(VERSION,0,3)=='2.3') {
			$this->load->model('extension/event');
			$this->model_extension_event->deleteEvent($this->evntcode);
		}
		if(substr(VERSION,0,3)=='3.0' || substr(VERSION,0,3)=='4.0') {			
			$this->load->model('setting/event');
			$this->model_setting_event->deleteEventByCode($this->evntcode);
		}
	}
	public function addtoevent($taregt, $func) {
		$taregt = (stristr($taregt, '/before') && substr(VERSION,0,3)!='2.2') ? str_replace('*/template/','',$taregt) : $taregt;
		
		if(substr(VERSION,0,3)=='2.2' || substr(VERSION,0,3)=='2.3') {
			$this->load->model('extension/event');
			$this->model_extension_event->addEvent($this->evntcode, $taregt, $this->modpath. $func);
		}
		if(substr(VERSION,0,3)=='3.0') {		
			$this->load->model('setting/event');	
			$this->model_setting_event->addEvent($this->evntcode, $taregt, $this->modpath. $func);
		}
		if(substr(VERSION,0,3)=='4.0') {
			$this->load->model('setting/event');
			$comval = array('code'=> $this->evntcode, 'description' => '', 'status'=>1, 'sort_order'=>1);
			$this->model_setting_event->addEvent(array_merge($comval, array('trigger' => $taregt, 'action' => $this->modpath. $func)));
		}
	}	
	public function loadjscss() {
		if($this->config->get($this->modname.'_status')) {
			$ocstr = substr(VERSION,0,3)=='4.0' ? '../extension/prdoptcol/admin/' : '';
			$this->document->addScript($ocstr.'view/javascript/prdoptcol.js?vr='.rand());
			$this->document->addStyle($ocstr.'view/javascript/prdoptcol.css?vr='.rand());
		}					
	}
	public function getoptflaghtml($op, $oprow1) {
		$html = array();
		if($this->config->get($this->modname.'_status')) {
			$lang = $this->load->language($this->modpath.'lang');
			
			$q = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option WHERE product_option_id = '" . (int)$op['product_option_id'] . "'");
			
			$prdoptcolflag_val = 0;
			if(isset($q->row['prdoptcolflag'])) { 
				$prdoptcolflag_val = $q->row['prdoptcolflag'];
			} else if(isset($op['prdoptcolflag'])) { 
				$prdoptcolflag_val = $op['prdoptcolflag'];
			}
 			
			$html[] = $this->get_RDO_html('product_option['.$oprow1.'][prdoptcolflag]', $prdoptcolflag_val, $lang['text_opcolimg'], array(1=>$lang['text_yes'], 0=>$lang['text_no']));
		}
		
		return join($html);
	}
	public function getoptvalhtml($op, $oprow1, $pov, $povrow) {
		$html = array();
		
		if($this->config->get($this->modname.'_status')) {
			$lang = $this->load->language($this->modpath.'lang');
			
			$this->load->model('tool/image');
			$placeholder = $this->model_tool_image->resize('no_image.png', 40, 40);	
			
			$q=$this->db->query("SELECT * FROM ". DB_PREFIX ."product_option_value WHERE product_option_value_id = '" .(int)$pov['product_option_value_id']. "'");
			$rs = $q->row;
			if(isset($rs['prdoptcolimg'])) {
				// DO nothing
			} else if (isset($this->request->post['product_option'])) {
				$rs['prdoptcolimg'] = $pov['prdoptcolimg'];
				$rs['prdoptcolcde'] = $pov['prdoptcolcde'];
				$rs['prdoptcolptrn'] = $pov['prdoptcolptrn'];
				$rs['prdoptcoladd1'] = $pov['prdoptcoladd1'];
				$rs['prdoptcoladd2'] = $pov['prdoptcoladd2'];
				$rs['prdoptcoladd3'] = $pov['prdoptcoladd3'];
				$rs['prdoptcoladd4'] = $pov['prdoptcoladd4'];
				$rs['prdoptcoladd5'] = $pov['prdoptcoladd5'];
				$rs['prdoptcoladd6'] = $pov['prdoptcoladd6'];			
			} else {
				$rs['prdoptcolimg'] = '';
				$rs['prdoptcolcde'] = '';
				$rs['prdoptcolptrn'] = '';
				$rs['prdoptcoladd1'] = '';
				$rs['prdoptcoladd2'] = '';
				$rs['prdoptcoladd3'] = '';
				$rs['prdoptcoladd4'] = '';
				$rs['prdoptcoladd5'] = '';
				$rs['prdoptcoladd6'] = '';
			}
			//echo "<pre>";print_r($rs);exit;
			
			if(isset($rs['prdoptcolimg'])) {
				$html[] = '<div class="prdoptcol_maindiv" style="display: none;">';
				
				$name = sprintf('product_option[%s][product_option_value][%s][prdoptcolptrn]', $oprow1, $povrow);
				$icon_thumb = $rs['prdoptcolptrn'] ? $this->model_tool_image->resize($rs['prdoptcolptrn'], 40, 40) : $placeholder;
				$html[] = $this->get_IMGUPLD_html($name, $rs['prdoptcolptrn'], $icon_thumb, 'prdoptcolptrn'.rand(), $placeholder, $lang['entry_colptrn']); 
				
				$name = sprintf('product_option[%s][product_option_value][%s][prdoptcolcde]', $oprow1, $povrow);
				$html[] = $this->get_InpTxt_colcode_html($name, $rs['prdoptcolcde'], $lang['entry_colcde']);	
				
				$name = sprintf('product_option[%s][product_option_value][%s][prdoptcolimg]', $oprow1, $povrow);
				$icon_thumb = $rs['prdoptcolimg'] ? $this->model_tool_image->resize($rs['prdoptcolimg'], 40, 40) : $placeholder;
				$html[] = $this->get_IMGUPLD_html($name, $rs['prdoptcolimg'], $icon_thumb, 'prdoptcolimg'.rand(), $placeholder, $lang['entry_colimg']); 
				
				$html[] = '<div class="prdoptcol_addimg_cls"><label>'.$lang['entry_coladd'].'</label>';
				
				$name = sprintf('product_option[%s][product_option_value][%s][prdoptcoladd1]', $oprow1, $povrow);
				$icon_thumb = $rs['prdoptcoladd1'] ? $this->model_tool_image->resize($rs['prdoptcoladd1'], 40, 40) : $placeholder;
				$html[] = $this->get_IMGUPLD_html($name, $rs['prdoptcoladd1'], $icon_thumb, 'prdoptcoladd1'.rand(), $placeholder, ''); 
				
				$name = sprintf('product_option[%s][product_option_value][%s][prdoptcoladd2]', $oprow1, $povrow);
				$icon_thumb = $rs['prdoptcoladd2'] ? $this->model_tool_image->resize($rs['prdoptcoladd2'], 40, 40) : $placeholder;
				$html[] = $this->get_IMGUPLD_html($name, $rs['prdoptcoladd2'], $icon_thumb, 'prdoptcoladd2'.rand(), $placeholder, ''); 
				
				$name = sprintf('product_option[%s][product_option_value][%s][prdoptcoladd3]', $oprow1, $povrow);
				$icon_thumb = $rs['prdoptcoladd3'] ? $this->model_tool_image->resize($rs['prdoptcoladd3'], 40, 40) : $placeholder;
				$html[] = $this->get_IMGUPLD_html($name, $rs['prdoptcoladd3'], $icon_thumb, 'prdoptcoladd3'.rand(), $placeholder, ''); 
				
				$name = sprintf('product_option[%s][product_option_value][%s][prdoptcoladd4]', $oprow1, $povrow);
				$icon_thumb = $rs['prdoptcoladd4'] ? $this->model_tool_image->resize($rs['prdoptcoladd4'], 40, 40) : $placeholder;
				$html[] = $this->get_IMGUPLD_html($name, $rs['prdoptcoladd4'], $icon_thumb, 'prdoptcoladd4'.rand(), $placeholder, ''); 
				
				$name = sprintf('product_option[%s][product_option_value][%s][prdoptcoladd5]', $oprow1, $povrow);
				$icon_thumb = $rs['prdoptcoladd5'] ? $this->model_tool_image->resize($rs['prdoptcoladd5'], 40, 40) : $placeholder;
				$html[] = $this->get_IMGUPLD_html($name, $rs['prdoptcoladd5'], $icon_thumb, 'prdoptcoladd5'.rand(), $placeholder, ''); 
				
				$name = sprintf('product_option[%s][product_option_value][%s][prdoptcoladd6]', $oprow1, $povrow);
				$icon_thumb = $rs['prdoptcoladd6'] ? $this->model_tool_image->resize($rs['prdoptcoladd6'], 40, 40) : $placeholder;
				$html[] = $this->get_IMGUPLD_html($name, $rs['prdoptcoladd6'], $icon_thumb, 'prdoptcoladd6'.rand(), $placeholder, ''); 
				
				$html[] = '</div> </div>';
			}
		}

		return join($html);
	}
	public function getflaghtmlJS() {
		$html = array();
		$lang = $this->load->language($this->modpath.'lang');
		
		$this->load->model('tool/image');
		$placeholder = $this->model_tool_image->resize('no_image.png', 40, 40);
		
		$html[] = '<script type="text/javascript">
		function prdoptcol_getflaghtmlJS(rs, oprow) {
			html = \'\';
			if(rs[\'type\'] == \'select\' || rs[\'type\'] == \'radio\' || rs[\'type\'] == \'checkbox\') { 
				html = \'<div class="'.$this->divcls.' prdoptcolflag">\';
				html += \'<label class="col-sm-2 '.$this->lblcls.'">'.$lang['text_opcolimg'].'</label>\';
				html += \'<div class="col-sm-10">\';
				html += \'<label class="radio-inline"> <input type="radio" name="product_option[\' + oprow + \'][prdoptcolflag]" value="1"> '.$lang['text_yes'].' </label>\';
				html += \'<label class="radio-inline"> <input type="radio" name="product_option[\' + oprow + \'][prdoptcolflag]" value="0"> '.$lang['text_no'].' </label>\';
				html += \'</div>\';
				html += \'</div>\';
			}
			return html;
		}
		
		function prdoptcol_getoptvalhtml_JS(oprow, povrow) { html = \'\'; ';
			$imgupload = '<div class="prdoptcol_maindiv" style="display: none;">' . 
			$this->get_IMGUPLD_html('product_option[\'+oprow+\'][product_option_value][\'+povrow+\'][prdoptcolptrn]', '', $placeholder, 'prdoptcolptrn'.rand().'\'+parseInt(oprow)+parseInt(povrow)+\'', $placeholder, $lang['entry_colptrn']); 
			$html[] = 'html += \''.$imgupload.'\';';
			
			$colortxtbox = $this->get_InpTxt_colcode_html('product_option[\'+oprow+\'][product_option_value][\'+povrow+\'][prdoptcolcde]', '', $lang['entry_colcde']); 
			$html[] = 'html += \''.$colortxtbox.'\';';
			
			$imgupload = $this->get_IMGUPLD_html('product_option[\'+oprow+\'][product_option_value][\'+povrow+\'][prdoptcolimg]', '', $placeholder, 'prdoptcolimg'.rand().'\'+parseInt(oprow)+parseInt(povrow)+\'', $placeholder, $lang['entry_colimg']); 
			$html[] = 'html += \''.$imgupload.'\';';						
			
			$imgupload = '<div class="prdoptcol_addimg_cls"><label>'.$lang['entry_coladd'].'</label>' . 
			$this->get_IMGUPLD_html('product_option[\'+oprow+\'][product_option_value][\'+povrow+\'][prdoptcoladd1]', '', $placeholder, 'prdoptcoladd1'.rand().'\'+parseInt(oprow)+parseInt(povrow)+\'', $placeholder, ''); 
			$html[] = 'html += \''.$imgupload.'\';';
			
			$imgupload = $this->get_IMGUPLD_html('product_option[\'+oprow+\'][product_option_value][\'+povrow+\'][prdoptcoladd2]', '', $placeholder, 'prdoptcoladd2'.rand().'\'+parseInt(oprow)+parseInt(povrow)+\'', $placeholder, ''); 
			$html[] = 'html += \''.$imgupload.'\';';
			
			$imgupload = $this->get_IMGUPLD_html('product_option[\'+oprow+\'][product_option_value][\'+povrow+\'][prdoptcoladd3]', '', $placeholder, 'prdoptcoladd3'.rand().'\'+parseInt(oprow)+parseInt(povrow)+\'', $placeholder, ''); 
			$html[] = 'html += \''.$imgupload.'\';';
			
			$imgupload = $this->get_IMGUPLD_html('product_option[\'+oprow+\'][product_option_value][\'+povrow+\'][prdoptcoladd4]', '', $placeholder, 'prdoptcoladd4'.rand().'\'+parseInt(oprow)+parseInt(povrow)+\'', $placeholder, ''); 
			$html[] = 'html += \''.$imgupload.'\';';
			
			$imgupload = $this->get_IMGUPLD_html('product_option[\'+oprow+\'][product_option_value][\'+povrow+\'][prdoptcoladd5]', '', $placeholder, 'prdoptcoladd5'.rand().'\'+parseInt(oprow)+parseInt(povrow)+\'', $placeholder, ''); 
			$html[] = 'html += \''.$imgupload.'\';';
			
			$imgupload = $this->get_IMGUPLD_html('product_option[\'+oprow+\'][product_option_value][\'+povrow+\'][prdoptcoladd6]', '', $placeholder, 'prdoptcoladd6'.rand().'\'+parseInt(oprow)+parseInt(povrow)+\'', $placeholder, '').'</div></div>'; 
			$html[] = 'html += \''.$imgupload.'\';
			 
			return html;	
		}';
		
		$trgt_opt_ele = substr(VERSION,0,3)=='4.0' ? 'fieldset' : 'div.tab-pane'; 
		$html[] = 'setInterval(function(){
			$(\'input[name*=prdoptcolflag]:checked\').each(function () {
				if ($(this).val() == 1) {
					$(this).closest(\''.$trgt_opt_ele.'\').find(\'.prdoptcol_maindiv\').show();
				} else {
					$(this).closest(\''.$trgt_opt_ele.'\').find(\'.prdoptcol_maindiv\').hide();
				}
			});
		}, 100);
			
		</script>'; 
		
		$html[] = '<style>.prdoptcol_maindiv label + div{display:inline-block}
		.prdoptcol_addimg_cls label{display:block}
		.prdoptcol_addimg_cls div{display:inline-block}</style>';
		
		return join($html);
	}
	
			
	
	
	
/*************************************************** INPUT FORMS ***************************************************/	
	public function get_InpTxt_html($name, $val, $entry, $help = '') {
		return sprintf('<div class="'.$this->divcls.'"> <label class="col-sm-2 '.$this->lblcls.'">%s</label><div class="col-sm-10"> <input type="text" name="%s" value="%s" class="form-control"/> %s </div> </div>', $entry, $name, $val, $help);
	}
	public function get_InpTxt_colcode_html($name, $val, $entry, $help = '') {
		return sprintf('<div class="'.$this->divcls.'"> <div class="col-sm-12"> <input type="text" name="%s" value="%s" class="form-control" placeholder="%s"/> %s </div> </div>', $name, $val, $entry, $help);
	}
	public function get_InpDATE_html($name, $val, $entry, $help = '') {
		return sprintf('<div class="'.$this->divcls.'"> <label class="col-sm-2 '.$this->lblcls.'">%s</label><div class="col-sm-10"> <input type="text" name="%s" value="%s" class="form-control date" data-date-format="YYYY-MM-DD"/> %s </div> </div>', $entry, $name, $val, $help);
	}
	public function get_InpTxt_LANG_html($name, $val, $entry, $help = '') {
		$input = array();
		foreach($this->getLang() as $lng) {
			$input[] = sprintf(' <img src="%s" style=" position: absolute;"/> <input style=" padding-left: 30px;" type="text" name="%s" value="%s" class="form-control"/> ', $lng['imgsrc'], sprintf($name.'[%s]', $lng['language_id']), (isset($val[$lng['language_id']]) ? $val[$lng['language_id']] : ''));
		}		
		return sprintf('<div class="'.$this->divcls.'"> <label class="col-sm-2 '.$this->lblcls.'">%s</label><div class="col-sm-10"> %s %s</div> </div>', $entry, join($input), $help);
	}
	public function get_RDO_html($name, $val, $entry, $looparr) {		
		$input = array();
		foreach($looparr as $ky => $op) {
			$sel = $val == $ky ? 'checked="checked"' : '';
			$input[] = sprintf('<label class="radio-inline"> <input type="radio" name="%s" value="%s" %s/> %s </label>', $name, $ky, $sel, $op);
		}
		return sprintf('<div class="'.$this->divcls.'"> <label class="col-sm-2 '.$this->lblcls.'">%s</label><div class="col-sm-10"> %s</div> </div>', $entry, join($input));
	}
	public function get_InpTxt_CHKBOXWELL_html($name, $val, $looparr, $loopky, $loopval, $entry, $help = '') {
		$input = array();
		foreach ($looparr as $rs) {
			$chk = in_array($rs[$loopky], $val) ? 'checked' : '';
			$input[] = sprintf('<div class="checkbox"> <label> <input type="checkbox" name="%s[]" value="%s" %s/>%s</label></div>', $name, $rs[$loopky], $chk, $rs[$loopval]);
		}
		$chkall = '<a class="badge bg-secondary" onclick="$(this).parent().find(\':checkbox\').prop(\'checked\', true);">Check All</a> / <a class="badge bg-secondary" onclick="$(this).parent().find(\':checkbox\').prop(\'checked\', false);">Uncheck All</a>';
		
		return sprintf('<div class="'.$this->divcls.'"> <label class="col-sm-2 '.$this->lblcls.'">%s</label><div class="col-sm-10"> <div class="'.$this->wellcls.'" style="height: 150px; overflow: auto;"> %s </div> %s </div> %s </div>', $entry, join($input), $chkall, $help);		
	}
	public function get_pcm_autocmp_html($name, $looparr, $loopky, $loopval, $entry, $help = '') {
		$html = array();
		
		if(substr(VERSION,0,3)=='4.0') { 
			$html[] = sprintf('<div class="'.$this->divcls.'i" style="width: 48%%; float:left; margin: 5px;"> <label class="col-sm-i '.$this->lblcls.'" style="text-align:left">%s</label><div class="col-sm-i"> <input type="text" name="%s" value="" id="input-%s" list="input-%s" data-oc-target="autocomplete-%s" class="form-control" autocomplete="off"/> <ul id="autocomplete-%s" class="dropdown-menu"></ul> <div class="input-group"> <div class="form-control p-0" style="height: 150px; overflow: auto;"> <table id="tbl-%s" class="table table-sm m-0"> <tbody> ', $entry, $name, $name, $name, $name, $name, $name);
					
			foreach ($looparr as $rs) {
				$html[] = sprintf('<tr id="tr-%s-%s"> <td>%s<input type="hidden" name="%s[]" value="%s"/></td> <td class="text-end"><button type="button" class="btn btn-danger btn-sm"><i class="fas fa-minus-circle"></i></button></td> </tr>', $name, $rs[$loopky], $rs[$loopval], $name, $rs[$loopky] );
			}
			
			$html[] = '</tbody> </table> </div> </div> </div> </div>';
		} else {
			$html[] = sprintf('<div class="'.$this->divcls.'i" style="width: 48%%; float:left; margin: 5px;"> <label class="col-sm-i '.$this->lblcls.'">%s</label><div class="col-sm-i"> <input type="text" name="%s" value="" id="input-%s" class="form-control" /> <div id="%s" class="'.$this->wellcls.'" style="height: 150px; overflow: auto;"> ', $entry, $name, $name, $name);
		
			foreach ($looparr as $rs) {
				$html[] = sprintf('<div id="%s-%s"><i class="fa fa-minus-circle"></i> %s <input type="hidden" name="%s[]" value="%s" /> </div>', $name, $rs[$loopky], $rs[$loopval], $name, $rs[$loopky] );
			}
			
			$html[] = '</div> </div> </div>';
		} 
		
		return join($html);
	}
	public function get_InpTxt_LANG_ULTAB_withID_html($name, $val, $langid, $entry, $help = '', $istxtarea = 0) {
		$html = array();
		
		if($istxtarea == 1) {
			if(substr(VERSION,0,3)=='4.0') { 
				$html[] = sprintf('<div class="'.$this->divcls.'"> <label class="col-sm-2 '.$this->lblcls.'">%s</label> <div class="col-sm-10"> <textarea name="%s[%s]" class="form-control summernote" data-oc-toggle="ckeditor" data-lang="ckeditor"> %s </textarea> </div> </div>', $entry, $name, $langid, $val); 	
			} else {
				$html[] = sprintf('<div class="'.$this->divcls.'"> <label class="col-sm-2 '.$this->lblcls.'">%s</label> <div class="col-sm-10"> <textarea name="%s[%s]" class="form-control summernote" data-toggle="summernote" data-lang="summernote"> %s </textarea> </div> </div>', $entry, $name, $langid, $val); 	
			}
		} else {
			$html[] = sprintf('<div class="'.$this->divcls.'"> <label class="col-sm-2 '.$this->lblcls.'">%s</label> <div class="col-sm-10"> <input type="text" name="%s[%s]" value="%s" placeholder="%s" class="form-control" /> </div> </div>', $entry, $name, $langid, $val, $entry); 	
		}			
		 
		return join($html);
	}
	public function get_InpTxt_LANG_ULTAB_html($name, $val, $entry, $help = '', $istxtarea = 0) {
		$html = array();
		
		if($istxtarea == 1) {
			if(substr(VERSION,0,3)=='4.0') { 
				$html[] = sprintf('<div class="'.$this->divcls.'"> <label class="col-sm-2 '.$this->lblcls.'">%s</label> <div class="col-sm-10"> <textarea name="%s" class="form-control summernote" data-oc-toggle="ckeditor" data-lang="ckeditor"> %s </textarea> </div> </div>', $entry, $name, $val); 	
			} else {
				$html[] = sprintf('<div class="'.$this->divcls.'"> <label class="col-sm-2 '.$this->lblcls.'">%s</label> <div class="col-sm-10"> <textarea name="%s" class="form-control summernote" data-toggle="summernote" data-lang="summernote"> %s </textarea> </div> </div>', $entry, $name, $val); 	
			}
		} else {
			$html[] = sprintf('<div class="'.$this->divcls.'"> <label class="col-sm-2 '.$this->lblcls.'">%s</label> <div class="col-sm-10"> <input type="text" name="%s" value="%s" placeholder="%s" class="form-control" /> </div> </div>', $entry, $name, $val, $entry); 	
		}			
		 
		return join($html);
	}
	public function get_IMGUPLD_LANG_html($name, $hidval, $imgsrc, $id, $plcholder, $entry, $help = '') {
		$html = array();
		$html[] = sprintf('<div class="'.$this->divcls.'"> <label class="col-sm-2 '.$this->lblcls.'">%s</label><div class="col-sm-10"> ', $entry);
				
		$langs = $this->getLang();
		
		foreach($langs as $lng) {
			$vl = $hidval[$lng['language_id']];			
			$nm = sprintf($name.'[%s]', $lng['language_id']);
			$src = $imgsrc[$lng['language_id']];	
						
			$html[] = sprintf('<div class="col-sm-1"><img src="'.$lng['imgsrc'].'"/></div> <div class="col-sm-11">');
			
			if(substr(VERSION,0,3)=='4.0') { 
				$html[] = '<div class="row"> <div class="col-sm-4 col-md-3 mb-3"> <div id="image" class="card image">';
				$html[] = sprintf('<img src="%s" alt="" title="" id="thumb-%s" data-oc-placeholder="%s" class="card-img-top"/> <input type="hidden" name="%s" value="%s" id="input-%s"/>', $src, $id.$lng['language_id'], $plcholder, $nm, $vl, $id.$lng['language_id']);
				$html[] = sprintf('<div class="card-body"> <button type="button" data-oc-toggle="image" data-oc-target="#input-%s" data-oc-thumb="#thumb-%s" class="btn btn-primary btn-sm btn-block"><i class="fa-solid fa-pencil"></i> </button> <button type="button" data-oc-toggle="clear" data-oc-target="#input-%s" data-oc-thumb="#thumb-%s" class="btn btn-warning btn-sm btn-block"><i class="fa-regular fa-trash-can"></i> </button>', $id.$lng['language_id'], $id.$lng['language_id'], $id.$lng['language_id'], $id.$lng['language_id']);
				$html[] = ' </div> </div> </div> </div> ';
			} else {
				$html[] = sprintf('<a href="" id="thumb-%s" data-toggle="image" class="img-thumbnail"><img src="%s" data-placeholder="%s" /></a> <input type="hidden" name="%s" value="%s" id="input-%s" />', $id.$lng['language_id'], $src, $plcholder, $nm, $vl, $id.$lng['language_id']);			
			}
			
			$html[] = '</div>';
		}
		
		$html[] = sprintf(' %s </div> </div>', $help);
		
		return join($html);				
	}
	public function get_IMGUPLD_html($name, $hidval, $imgsrc, $id, $plcholder, $entry, $help = '') {
		if(substr(VERSION,0,3)=='4.0') { 
			$html = array();
			$html[] = sprintf('<div> <label>%s</label><div>', $entry);
			$html[] = '<div class="row"> <div class="col-sm-4 col-md-3 mb-3" style="width:100px;"> <div id="image" class="card image" style="width:40px;height:40px;float: left;;">';
			$html[] = sprintf('<img width="40px" src="%s" alt="" title="" id="thumb-%s" data-oc-placeholder="%s" class="card-img-top"/> <input type="hidden" name="%s" value="%s" id="input-%s"/></div>', $imgsrc, $id, $plcholder, $name, $hidval, $id);
			$html[] = sprintf('<div class="card-body" style=" padding: 0; margin: 0;"> <button style="display: block;" type="button" data-oc-toggle="image" data-oc-target="#input-%s" data-oc-thumb="#thumb-%s" class="btn btn-info btn-sm btn-block"><i class="fa-solid fa-pencil"></i> </button> <button style="display: block;" type="button" data-oc-toggle="clear" data-oc-target="#input-%s" data-oc-thumb="#thumb-%s" class="btn btn-warning btn-sm btn-block"><i class="fa-regular fa-trash-can"></i> </button>', $id, $id, $id, $id);
			$html[] = ' </div> </div> </div> </div></div>';
			
			return join($html);
		} else {
			return sprintf('<div> <label>%s</label><div> <a href="" id="thumb-%s" data-toggle="image" class="img-thumbnail"><img width="40px" src="%s" data-placeholder="%s" /></a> <input type="hidden" name="%s" value="%s" id="input-%s" /> %s </div> </div>', $entry, $id, $imgsrc, $plcholder, $name, $hidval, $id, $help);			
		}
	}
	
	
	
	
/*************************************************** INPUT FILTERS ***************************************************/
	public function get_InpTxt_FILTER_html($name, $val, $entry) {
		return sprintf('<div class="'.(str_replace('row ','',$this->divcls)).' col-sm-2"> <label class="'.$this->lblcls.'">%s</label><input type="text" name="%s" value="%s" class="form-control"/> </div>', $entry, $name, $val);
	}
	public function get_InpTxt_DATE_FILTER_html($name, $val, $entry) {
		return sprintf('<div class="'.(str_replace('row ','',$this->divcls)).' col-sm-2"> <label class="'.$this->lblcls.'">%s</label><input type="text" name="%s" value="%s" class="form-control date" data-date-format="YYYY-MM-DD"/> </div>', $entry, $name, $val);
	}
	public function get_SELECT_FILTER_html($name, $val, $entry, $looparr) {
		$html = array();
		
		$html[] = sprintf('<div class="'.(str_replace('row ','',$this->divcls)).' col-sm-2"> <label class="'.$this->lblcls.'">%s</label> <select name="%s" class="'.$this->slctcls.'"> <option value=""></option>', $entry, $name);
		foreach($looparr as $ky => $op) {
			$sel = $val === (string)$ky ? 'selected="selected"' : '';
			$html[] = sprintf('<option value="%s" %s>%s</option>', $ky, $sel, $op);
		}
		$html[] = sprintf('</select></div>');
		
		return join($html);
	}
	public function get_SELECT_CGSTORE_FILTER_html($name, $val, $looparr, $loopky, $loopval, $entry) {	
		$html = array();
		
		$html[] = sprintf('<div class="'.(str_replace('row ','',$this->divcls)).' col-sm-2"> <label class="'.$this->lblcls.'">%s</label> <select name="%s" id="%s" class="'.$this->slctcls.'"> <option value=""></option>', $entry, $name, $name);
		foreach ($looparr as $rs) {
			$sel = $val == $rs[$loopky] ? 'selected="selected"' : '';
			$html[] = sprintf('<option value="%s" %s>%s</option>', $rs[$loopky], $sel, $rs[$loopval]);
		}
		$html[] = sprintf('</select></div>');
		
		return join($html);
	}
	public function get_pcm_FILTER_html($name, $val, $nameid, $valid, $entry) {
		if(substr(VERSION,0,3)=='4.0') {
			return sprintf('<div class="'.(str_replace('row ','',$this->divcls)).' col-sm-2"> <label class="'.$this->lblcls.'">%s</label> <input type="text" name="%s" value="%s" class="form-control" id="input-%s" list="input-%s" data-oc-target="autocomplete-%s" autocomplete="off"/> <input type="hidden" name="%s" value="%s" class="form-control"/> <ul id="autocomplete-%s" class="dropdown-menu"></ul> </div>', $entry, $name, $val, $name, $name, $name, $nameid, $valid, $name);	
		} else {
			return sprintf('<div class="'.(str_replace('row ','',$this->divcls)).' col-sm-2"> <label class="'.$this->lblcls.'">%s</label> <input type="text" name="%s" value="%s" class="form-control" id="%s"/> <input type="hidden" name="%s" value="%s" class="form-control"/> </div>', $entry, $name, $val, $name, $nameid, $valid);	
		}
	}
	
	
	
	
	public function getcgstoreList($target, $arr, $id, $nm) {
		$info = array();
		$ids = explode(",",$target);
		if($ids) { 
			foreach ($arr as $arval) {
				if (in_array($arval[$id], $ids)) {
					$info[$arval[$id]] = $arval[$nm];
				}
			}
		}
		return $info;
	}
	public function getprodcatmanList($target_val, $target) {
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
 		$this->load->model('catalog/manufacturer');
		
		$return = array();
		$explode = explode(",",$target_val);
		if($explode) {
			foreach ($explode as $id) {
				if($target == 'product') {
					$info = $this->model_catalog_product->getProduct((int)$id);
					if ($info) {
						$return[$info['product_id']] = $info['name'];
					}
				} else if($target == 'category') {
					$info = $this->model_catalog_category->getCategory((int)$id);;
 					if ($info) {
						$return[$info['category_id']] = ($info['path']) ? $info['path'] . ' &gt; ' . $info['name'] : $info['name'];
					}
				} if($target == 'manufacturer') {
					$info = $this->model_catalog_manufacturer->getManufacturer((int)$id);
 					if ($info) {
						$return[$info['manufacturer_id']] = $info['name'];
					}
				}			
			}
		}
		return $return;
	}
	public function getprodcatmanForm($bginfo, $target_ele, $target) {
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
 		$this->load->model('catalog/manufacturer');
		
		$return_data[0] = array();
		$return_data[1] = array();
 		
  		if (isset($this->request->post[$target_ele])) {
			$return_data[0] = $this->request->post[$target_ele];
		} elseif (!empty($bginfo)) {
			$return_data[0] = ($bginfo[$target_ele]) ? explode(",",$bginfo[$target_ele]) : array();
 		}
		
 		$this->load->model('catalog/product');
		
 		if($return_data[0]) {
 			foreach ($return_data[0] as $id) {
				if($target == 'product') {
					$info = $this->model_catalog_product->getProduct((int)$id);
					if ($info) {
						$return_data[1][] = array(
							'product_id' => $info['product_id'],
							'name'       => $info['name']
						);
					}
				} else if($target == 'category') {
					$info = $this->model_catalog_category->getCategory((int)$id);;
 					if ($info) {
						$return_data[1][] = array(
							'category_id' => $info['category_id'],
							'name' => ($info['path']) ? $info['path'] . ' &gt; ' . $info['name'] : $info['name']
						);
					}					
				} if($target == 'manufacturer') {
					$info = $this->model_catalog_manufacturer->getManufacturer((int)$id);
 					if ($info) {
						$return_data[1][] = array(
							'manufacturer_id' => $info['manufacturer_id'],
							'name' => $info['name'],
						);
					}
				}								
			}
		}
		
		return $return_data[1];
	}
	public function getsummernoteJS() {
		$html = array();
		
		if(substr(VERSION,0,3)=='2.0' || substr(VERSION,0,3)=='2.1') {
			$html[] = '<script type="text/javascript">$(\'.summernote\').summernote({height: 300});</script>';
		}
		if(substr(VERSION,0,3)=='2.3') {
			$html[] = '<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
			<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
			<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>';
		}
		if(substr(VERSION,0,3)=='3.0') {
			$html[] = '<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
			<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
			<script type="text/javascript" src="view/javascript/summernote/summernote-image-attributes.js"></script> 
			<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>';
		}
		if(substr(VERSION,0,3)=='4.0') { 
			$html[] = '<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script>
			<script type="text/javascript" src="view/javascript/ckeditor/adapters/jquery.js"></script>
			<script type="text/javascript">$(\'textarea[data-oc-toggle="ckeditor"]\').ckeditor();</script>';
		}
		
		return join($html);
	}
	



	
	public function setcnfgvalue($postfield) {
		if (isset($this->request->post[$postfield])) {
			return $this->request->post[$postfield];
		} else {
			return $this->config->get($postfield);
		} 	
	}
	public function setpostval($name, $val, $defval) {
		if (isset($this->request->post[$name])) {
			return $this->request->post[$name];
		} elseif (!empty($val) || $val == 0) {
			return $val;
		} else {
			return $defval;
		}		
	}
	protected function validateForm() {
		if (!$this->user->hasPermission('modify', $this->modpath)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}
	protected function modifypermission() {
		if (!$this->user->hasPermission('modify', $this->modpath)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
 	public function getStores() {
		$result = array();
		$result[0] = array('store_id' => '0', 'name' => $this->config->get('config_name'));
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store WHERE 1 ORDER BY store_id");
		if($query->num_rows) { 
			foreach($query->rows as $rs) { 
				$result[$rs['store_id']] = $rs;
			}
		}
		return $result;
	} 
	public function getCustomerGroups() {
 		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_group_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
 		return $query->rows;
	}
	public function getLang() {
 		$lang = array();
		$this->load->model('localisation/language');
  		$languages = $this->model_localisation_language->getLanguages();
		foreach($languages as $language) {
			if(substr(VERSION,0,3)>='3.0' || substr(VERSION,0,3)=='2.3' || substr(VERSION,0,3)=='2.2') {
				$imgsrc = "language/".$language['code']."/".$language['code'].".png";
			} else {
				$imgsrc = "view/image/flags/".$language['image'];
			}
			$lang[] = array("language_id" => $language['language_id'], "name" => $language['name'], "imgsrc" => $imgsrc);
		}
 		return $lang;
	}
	public function getOrderStatuses() {		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name asc"); 
		return $query->rows;		
	}
}