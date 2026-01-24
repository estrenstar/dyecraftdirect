<?php
class ModelExtensionshipinschkhistory extends Model {
	private $divcls = 'form-group';
	private $lblcls = 'control-label';
	private $wellcls = 'well well-sm';
	private $grpcls = 'input-group-addon';
	private $slctcls = 'form-control';
	
	private $modpath = 'extension/shipinschkhistory'; 
	private $modvar = 'model_extension_shipinschkhistory';
	private $modname = 'shipinschkhistory';
	private $modurl = 'extension/shipinschkhistory';
	private $modsprtor = '/';
	
	private $evntcode = 'shipinschkhistory';	
	private $error = array();
	private $token = '';
	private $pglimit = 50;
	private $urlval = '';
	private $urlfilter = array('filter_insamount', 'filter_order_id', 'filter_orderamount', 'filter_customer_id', 'filter_customer_name', 'filter_email', 'filter_telephone', 'filter_customer_group_id', 'filter_store_id', 'filter_date_added');
	
	public function __construct($registry) {		
		parent::__construct($registry);			
		ini_set("serialize_precision", -1);
		
		if(isset($this->session->data['token'])) { $this->token = 'token=' . $this->session->data['token'] ; }
		if(isset($this->session->data['user_token'])) { $this->token = 'user_token=' . $this->session->data['user_token'] ; }
		
		$this->pglimit = $this->config->get('config_limit_admin') ? $this->config->get('config_limit_admin') : $this->config->get('config_pagination_admin');
		
		foreach($this->urlfilter as $urlval) {
			if (isset($this->request->get[$urlval])) {
				$this->urlval .= '&'.$urlval.'=' . urlencode(html_entity_decode($this->request->get[$urlval], ENT_QUOTES, 'UTF-8'));
			}			
		}
		
		if (isset($this->request->get['sort'])) { $this->urlval .= '&sort=' . $this->request->get['sort']; }
		if (isset($this->request->get['order'])) { $this->urlval .= '&order=' . $this->request->get['order'];}
		if (isset($this->request->get['page'])) { $this->urlval .= '&page=' . $this->request->get['page'];}
			
		if(substr(VERSION,0,3)=='3.0') {			
			$this->modname = 'module_shipinschkhistory';
		} 
		if(substr(VERSION,0,3)=='4.0') {
			$this->modpath = 'extension/shipinschk/extension/shipinschkhistory';
			$this->modvar = 'model_extension_shipinschk_extension_shipinschkhistory';
			$this->modname = 'module_shipinschkhistory';
			$this->modsprtor = '.';
			$this->divcls = 'row mb-3';
			$this->lblcls = 'col-form-label';
			$this->wellcls = 'form-control';
			$this->grpcls = 'input-group-text';
			$this->slctcls = 'form-select';
		}
 	}
	public function deleteshipinschkhistory() {
		$lang = $this->load->language($this->modpath);
		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->post['selected']) && $this->modifypermission()) {
			foreach ($this->request->post['selected'] as $shipinschkhistory_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "shipinschkhistory WHERE shipinschkhistory_id = '" . (int)$shipinschkhistory_id . "'");
			}
			
			$this->session->data['success'] = $lang['text_success'];
 			$url = $this->urlval;
 			$this->response->redirect($this->url->link($this->modpath, $this->token . $url, true));
		}
	}	
	public function getsqlfilter($data = array()) {
		$sql = '';
		if (!empty($data['filter_insamount'])) {
			$sql .= " AND insamount = ".(float)$data['filter_insamount'];
		}
		
		if (!empty($data['filter_order_id'])) {
			$sql .= " AND order_id = ".(int)$data['filter_order_id'];
		}
		if (!empty($data['filter_orderamount'])) {
			$sql .= " AND orderamount = ".(float)$data['filter_orderamount'];
		}
		
		if (!empty($data['filter_customer_id'])) {
			$sql .= " AND customer_id = ".(int)$data['filter_customer_id'];
		}
		if (!empty($data['filter_email'])) {
			$sql .= " AND email LIKE '%" . $this->db->escape($data['filter_email']) . "%'";
		}
		if (!empty($data['filter_customer_name'])) {
			$sql .= " AND customer_name LIKE '" . $this->db->escape($data['filter_customer_name']) . "%'";
		}
		if (!empty($data['filter_telephone'])) {
			$sql .= " AND telephone LIKE '" . $this->db->escape($data['filter_telephone']) . "%'";
		}
		
		if (!empty($data['filter_customer_group_id'])) {
			$sql .= " AND customer_group_id = ".(int)$data['filter_customer_group_id'];
		}
		if (!empty($data['filter_store_id'])) {
			$sql .= " AND store_id = ".(int)$data['filter_store_id'];
		}
		
		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		
		return $sql;
	}
	public function getshipinschkhistory($shipinschkhistory_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "shipinschkhistory WHERE shipinschkhistory_id = '" . (int)$shipinschkhistory_id . "' ");

		return $query->row;
	}
	public function getshipinschkhistorys($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "shipinschkhistory WHERE 1 ";
		
		$sql .= $this->getsqlfilter($data);
		 
		$sql .= " GROUP BY shipinschkhistory_id";

		$sort_data = array(
 			'date_added',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY date_added";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
	public function getTotalshipinschkhistorys($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "shipinschkhistory WHERE 1 ";
  		
		$sql .= $this->getsqlfilter($data);
		  		
		$query = $this->db->query($sql);

		return isset($query->row['total']) ? $query->row['total'] : 0;
	}
	public function getListHTML() {
		$lang = $this->load->language($this->modpath);						
		
		$filter_val = array();
 		foreach($this->urlfilter as $urlval) {
			$filter_val[$urlval] = isset($this->request->get[$urlval]) ? $this->request->get[$urlval] : null;			
		}
		
		$sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'date_added';
		$order = isset($this->request->get['order']) ? $this->request->get['order'] : 'DESC';
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : '1';
		
		$url = $this->urlval;
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->token, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link($this->modpath, $this->token . $url, true)
		);
 
		$data['delete'] = $this->url->link($this->modpath. $this->modsprtor . 'delete', $this->token . $url, true);

		$data['shipinschkhistorys'] = array();

		$filter_data = array();		
		foreach($this->urlfilter as $urlval) {
			$filter_data[$urlval] = isset($filter_val[$urlval]) ? $filter_val[$urlval] : null;
		}
		
		$filter_data['sort'] = $sort;
		$filter_data['order'] = $order;
		$filter_data['start'] = ($page - 1) * $this->pglimit;
		$filter_data['limit'] = $this->pglimit;
  
		$shipinschkhistory_total = $this->getTotalshipinschkhistorys($filter_data);

		$results = $this->getshipinschkhistorys($filter_data);
		
		$data['stores'] = $this->getStores();

		$data['cgs'] = $this->getCustomerGroups();
		
		// Columns HTML
		$html = array();
		$html[] = sprintf('<td class="text-left">%s</td>', $lang['entry_insamount']);
		$html[] = sprintf('<td class="text-left">%s</td>', $lang['entry_order_id']);
		$html[] = sprintf('<td class="text-left">%s</td>', $lang['entry_orderamount']);
		
		$html[] = sprintf('<td class="text-left">%s</td>', $lang['entry_customer_id']);
		$html[] = sprintf('<td class="text-left">%s</td>', $lang['entry_customer_name']);
		$html[] = sprintf('<td class="text-left">%s</td>', $lang['entry_email']);
		$html[] = sprintf('<td class="text-left">%s</td>', $lang['entry_telephone']);
		
		$html[] = sprintf('<td class="text-left">%s</td>', $lang['entry_customer_group']);
		$html[] = sprintf('<td class="text-left">%s</td>', $lang['entry_store']);
		
		$html[] = sprintf('<td class="text-left">%s</td>', $lang['entry_date_added']);
		
		$data['coltd_html'] = join($html);
  
		foreach ($results as $result) {
			$cgname = $this->getCustomerGroupsID($result['customer_group_id']);
			$storename = $this->getStoresID($result['store_id']);
			
			$html = array();
			$html[] = sprintf('<td class="text-left">%s</td>', $result['insamount']);
			$html[] = sprintf('<td class="text-left">%s</td>', $result['order_id']);
			$html[] = sprintf('<td class="text-left">%s</td>', $result['orderamount']);
			
			$html[] = sprintf('<td class="text-left">%s</td>', $result['customer_id']);
			$html[] = sprintf('<td class="text-left">%s</td>', $result['customer_name']);
			$html[] = sprintf('<td class="text-left">%s</td>', $result['email']);
			$html[] = sprintf('<td class="text-left">%s</td>', $result['telephone']);
			
			$html[] = sprintf('<td class="text-left">%s</td>', $cgname);
			$html[] = sprintf('<td class="text-left">%s</td>', $storename);
			
			$html[] = sprintf('<td class="text-left">%s</td>', date($this->language->get('date_format_short'), strtotime($result['date_added'])));
			
 			$data['shipinschkhistorys'][] = array(
			'shipinschkhistory_id' => $result['shipinschkhistory_id'],
			'coltd_val_html' => join($html),
			'delete' => $this->url->link($this->modpath. $this->modsprtor . 'delete', $this->token . '&shipinschkhistory_id=' . $result['shipinschkhistory_id'] . $url, true)
			);
		}
		
		$data['heading_title'] = $this->language->get('heading_title');
 
		$data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
		
		$data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';
		unset($this->session->data['success']);
		
		$data['selected'] = isset($this->request->post['selected']) ? (array)$this->request->post['selected'] : array();


		$url = $this->urlval;
		$url .= $order == 'ASC' ? '&order=DESC' : '&order=ASC'; 

  		$data['sort_status'] = $this->url->link($this->modpath, $this->token . '&sort=status' . $url, true);
 
		$url = $this->urlval;
		
		if(substr(VERSION,0,3)=='4.0') {
			$data['pagination'] = $this->load->controller('common/pagination', array(
				'total' => $shipinschkhistory_total,
				'page'  => $page,
				'limit' => $this->pglimit,
				'url'   => $this->url->link($this->modpath, $this->token . $url . '&page={page}', true)
			));
		} else {
			$pagination = new Pagination();
			$pagination->total = $shipinschkhistory_total;
			$pagination->page = $page;
			$pagination->limit = $this->pglimit;
			$pagination->url = $this->url->link($this->modpath, $this->token . $url . '&page={page}', true);
 			$data['pagination'] = $pagination->render();
		}	
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($shipinschkhistory_total) ? (($page - 1) * $this->pglimit) + 1 : 0, ((($page - 1) * $this->pglimit) > ($shipinschkhistory_total - $this->pglimit)) ? $shipinschkhistory_total : ((($page - 1) * $this->pglimit) + $this->pglimit), $shipinschkhistory_total, ceil($shipinschkhistory_total / $this->pglimit));	
		
		foreach($this->urlfilter as $urlval) {
			$data[$urlval] = isset($filter_val[$urlval]) ? $filter_val[$urlval] : null;
		}
		
		// Filter HTML
		$html = array();		

		$html[] = $this->get_InpTxt_FILTER_html('filter_insamount', $data['filter_insamount'], $lang['entry_insamount']);
		$html[] = $this->get_InpTxt_FILTER_html('filter_order_id', $data['filter_order_id'], $lang['entry_order_id']);
		$html[] = $this->get_InpTxt_FILTER_html('filter_orderamount', $data['filter_orderamount'], $lang['entry_orderamount']);
		
		$html[] = $this->get_InpTxt_FILTER_html('filter_customer_id', $data['filter_customer_id'], $lang['entry_customer_id']);
		$html[] = $this->get_InpTxt_FILTER_html('filter_customer_name', $data['filter_customer_name'], $lang['entry_customer_name']);
		$html[] = $this->get_InpTxt_FILTER_html('filter_email', $data['filter_email'], $lang['entry_email']);
		$html[] = $this->get_InpTxt_FILTER_html('filter_telephone', $data['filter_telephone'], $lang['entry_telephone']);
		
 		$html[] = $this->get_SELECT_CGSTORE_FILTER_html('filter_customer_group_id', $data['filter_customer_group_id'], $data['cgs'], 'customer_group_id', 'name', $lang['entry_customer_group']);
		$html[] = $this->get_SELECT_CGSTORE_FILTER_html('filter_store_id', $data['filter_store_id'], $data['stores'], 'store_id', 'name', $lang['entry_store']);
		$html[] = $this->get_InpTxt_DATE_FILTER_html('filter_date_added', $data['filter_date_added'], $lang['entry_date_added']);
		  		
		$data['filter_html'] = join($html);
  		
		$data['sort'] = $sort;
		$data['order'] = $order;
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		// HTML STARTS
		$html = array();
		$html[] = $data['header'];
		$html[] = $data['column_left'];
		
		$html[] = sprintf('<div id="content"> <div class="page-header"> <div class="container-fluid"> <div class="pull-right float-end"> <button type="button" class="btn btn-danger" onclick="confirm(\'%s\') ? $(\'#form-shipinschkhistory\').submit() : false;"><i class="fa fa-trash-o fas fa-trash-alt"></i></button> </div>', $lang['text_confirm']);
		
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
		$html[] = '</div>  </div>  <div class="container-fluid">';
		
		if ($data['error_warning']) {
			$html[] = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>%s <button type="button" class="close" data-dismiss="alert">&times;</button> </div>', $data['error_warning']);
		}
		if ($data['success']) {
			$html[] = sprintf('<div class="alert alert-success"><i class="fa fa-check-circle"></i>%s <button type="button" class="close" data-dismiss="alert">&times;</button> </div>', $data['success']);
		}
		
		$html[] = sprintf('<div class="panel panel-default card"> <div class="panel-heading card-header"> <h3 class="panel-title"><i class="fa fa-list"></i> %s </h3> </div> <div class="panel-body card-body"> <div class="row"> <div class="filter_div"> <style>
        .form-group { padding: 5px !important; } .col-sm-2 { width: 16%%; font-size:11px; display: inline-block; margin: 2px !important;}
        @media only screen and (max-width: 600px) {
        .col-sm-2 { width: 30%%; display:inline-block }
        }
        </style>', $lang['text_list']);
		
		$html[] = $data['filter_html'];
		
		$html[] = sprintf('<div class="'.$this->divcls.' col-sm-2"> <label class="control-label col-sm-12" for="input-btnfilter">&nbsp;</label> <button type="button" id="button-filter" class="btn btn-primary"><i class="fa fa-filter"></i> %s </button> </div>', $lang['button_filter']);
		
		$html[] = '</div> </div>';
		
		$html[] = sprintf('<form action="%s" method="post" enctype="multipart/form-data" id="form-shipinschkhistory">', $data['delete']);
		
		$html[] = '<div class="table-responsive"> <table class="table table-bordered table-hover"> <thead> <tr>';
		
		$html[] = sprintf('<td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$(\'input[name*=\'selected\']\').prop(\'checked\', this.checked);" /></td>');
		
		$html[] = $data['coltd_html']; 
				
		$html[] = ' </tr> </thead> <tbody>';
		if ($data['shipinschkhistorys']) {
			foreach ($data['shipinschkhistorys'] as $shipinschkhistory) {
				$html[] = sprintf('<tr> <td class="text-center">');
				if (in_array($shipinschkhistory['shipinschkhistory_id'], $data['selected'])) {
					$html[] = sprintf('<input type="checkbox" name="selected[]" value="%s" checked="checked" />', $shipinschkhistory['shipinschkhistory_id']);
				} else {
					$html[] = sprintf('<input type="checkbox" name="selected[]" value="%s"/>', $shipinschkhistory['shipinschkhistory_id']);
				}
				$html[] = '</td>';
				
				$html[] = $shipinschkhistory['coltd_val_html'];
				
 				$html[] = '</tr>';
			}
		} else {
			$html[] = sprintf('<tr> <td class="text-center" colspan="30">%s</td> </tr>', $lang['text_no_results']);
		}
		$html[] = ' </tbody> </table> </div> </form>';
		
		$html[] = sprintf('<div class="row"> <div class="col-sm-6 text-left">%s</div> <div class="col-sm-6 text-right">%s</div>', $data['pagination'], $data['results']);
		$html[] = ' </div> </div> </div> </div> </div>';
        
		
		$html[] = $data['footer'];
		
		$autocmp_url = $this->modsprtor . 'autocomplete&'.$this->token;
		$filter_url = 'index.php?route='.$this->modpath.'&'.$this->token;
		
		$html[] = '<script language="javascript" type="text/javascript">
		function dofilter() {						
			$(\'#button-filter\').on(\'click\', function() {
				var url = \''.$filter_url.'\';
		
				var filter_insamount = $("input[name=\'filter_insamount\']").val();
				if (filter_insamount != "") {
					url += \'&filter_insamount=\' + encodeURIComponent(filter_insamount);
				}
				var filter_order_id = $("input[name=\'filter_order_id\']").val();
				if (filter_order_id != "") {
					url += \'&filter_order_id=\' + encodeURIComponent(filter_order_id);
				}
				var filter_orderamount = $("input[name=\'filter_orderamount\']").val();
				if (filter_orderamount != "") {
					url += \'&filter_orderamount=\' + encodeURIComponent(filter_orderamount);
				}
				
				var filter_customer_id = $("input[name=\'filter_customer_id\']").val();
				if (filter_customer_id != "") {
					url += \'&filter_customer_id=\' + encodeURIComponent(filter_customer_id);
				}
				var filter_customer_name = $("input[name=\'filter_customer_name\']").val();
				if (filter_customer_name != "") {
					url += \'&filter_customer_name=\' + encodeURIComponent(filter_customer_name);
				}
				var filter_email = $("input[name=\'filter_email\']").val();
				if (filter_email != "") {
					url += \'&filter_email=\' + encodeURIComponent(filter_email);
				}
				var filter_telephone = $("input[name=\'filter_telephone\']").val();
				if (filter_telephone != "") {
					url += \'&filter_telephone=\' + encodeURIComponent(filter_telephone);
				}
				
				var filter_date_added = $("input[name=\'filter_date_added\']").val();
				if (filter_date_added != "") {
					url += \'&filter_date_added=\' + encodeURIComponent(filter_date_added);
				}
				var filter_customer_group_id = $("select[name=\'filter_customer_group_id\']").val();
				if (filter_customer_group_id != "") {
					url += \'&filter_customer_group_id=\' + encodeURIComponent(filter_customer_group_id);
				}
 				var filter_store_id = $("select[name=\'filter_store_id\']").val();
				if (filter_store_id != "") {
					url += \'&filter_store_id=\' + encodeURIComponent(filter_store_id);
				} 
				 
				location = url; 
			}); 
		}
		$(document).ready(function() {	
			$(\'.date\').datetimepicker({ pickTime: false });
			dofilter();
		});
		</script>';
		
		$this->response->setOutput(join($html));
 	}
	
	
	
	

/*************************************************** INPUT FORMS ***************************************************/	
	public function get_InpTxt_html($name, $val, $entry, $help = '') {
		return sprintf('<div class="'.$this->divcls.'"> <label class="col-sm-2 '.$this->lblcls.'">%s</label><div class="col-sm-10"> <input type="text" name="%s" value="%s" class="form-control"/> %s </div> </div>', $entry, $name, $val, $help);
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
	public function getStoresID($id) {
		if($id == 0) { return $this->config->get('config_name'); }
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$id . "' ");
 		return isset($query->row['name']) ? $query->row['name'] : '';
	}
	public function getCustomerGroups() {
 		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_group_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
 		return $query->rows;
	}
	public function getCustomerGroupsID($id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_group_description WHERE customer_group_id = '" . (int)$id . "' and language_id = '" . (int)$this->config->get('config_language_id') . "' ");
 		return isset($query->row['name']) ? $query->row['name'] : '';
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