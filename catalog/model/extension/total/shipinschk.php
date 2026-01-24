<?php
class ModelExtensionTotalshipinschk extends Model {
	private $divcls = 'form-group';
	private $lblcls = 'control-label';
	private $wellcls = 'well well-sm';
	private $grpcls = 'input-group-addon';
	private $slctcls = 'form-control';
	
	private $modpath = 'total/shipinschk'; 
	private $modvar = 'model_total_shipinschk';
	private $modname = 'shipinschk';
	private $modsprtor = '/';
	
	private $evntcode = 'shipinschk';
	private $error = array();
	private $status = '';
	private $setting = array();

	public function __construct($registry) {		
		parent::__construct($registry);		
		ini_set("serialize_precision", -1);
		
		if(substr(VERSION,0,3)=='2.3') {
			$this->modpath = 'extension/total/shipinschk';
			$this->modvar = 'model_extension_total_shipinschk';
		}
		if(substr(VERSION,0,3)=='3.0') {			
			$this->modpath = 'extension/total/shipinschk';
			$this->modvar = 'model_extension_total_shipinschk';
			$this->modname = 'total_shipinschk';
		} 
		if(substr(VERSION,0,3)=='4.0') {
			$this->modpath = 'extension/shipinschk/total/shipinschk';
			$this->modvar = 'model_extension_shipinschk_total_shipinschk';
			$this->modname = 'total_shipinschk';
			$this->modsprtor = '.';
			$this->divcls = 'row mb-3';
			$this->lblcls = 'col-form-label';
			$this->wellcls = 'form-control';
			$this->grpcls = 'input-group-text';
			$this->slctcls = 'form-select';			
		}
		
		$ismulti_store = 0;
		if($ismulti_store) {
			$this->setting = $this->getSetting();
			$this->status = ($this->config->get($this->modname.'_status') && $this->setting['status']) ? true : false;	
		} else {
			$this->status = $this->config->get($this->modname.'_status');
			$this->setting = $this->getSetting();
		}
 	}
	//oc2.0 and 2.1 public function getTotal(&$total_data, &$total, &$taxes)
	//oc3.X, 2.3 and 2.2 public function getTotal($total)
	//oc4.X public function getTotal(array &$totals, array &$taxes, float &$total)

	public function getTotal($total) {
 		$lang = $this->load->language($this->modpath);
 		
		if(isset($this->session->data['shipinschk']['radio']) && $this->session->data['shipinschk']['radio'] == 1) {
			$fee = $this->session->data['shipinschk']['fee'];
			$ratetype = $this->session->data['shipinschk']['ratetype'];
			$tax_class_id = $this->session->data['shipinschk']['tax_class_id'];
						
			if($fee) {
				if(substr(VERSION,0,3)=='4.0') {
					$totals[] = array(
						'extension'  => 'shipinschk',
						'code'       => 'shipinschk',
						'title'      => $lang['text_order_total'],
						'value'      => $fee,
						'sort_order' => (int)$this->setting['sort_order'],
					);
					
					if ($tax_class_id) {
						$tax_rates = $this->tax->getRates($fee, $tax_class_id);
		
						foreach ($tax_rates as $tax_rate) {
							if (!isset($taxes[$tax_rate['tax_rate_id']])) {
								$taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
							} else {
								$taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
							}
						}
					}
	
					$total += abs($fee);
					
				} else if(substr(VERSION,0,3)=='3.0' || substr(VERSION,0,3)=='2.3' || substr(VERSION,0,3)=='2.2') {
					$total['totals'][] = array(
						'code'       => 'shipinschk',
						'title'      => $lang['text_order_total'],
						'value'      => $fee,
						'sort_order' => $this->setting['sort_order'],
					);
					
					if ($tax_class_id) {
						$tax_rates = $this->tax->getRates($fee, $tax_class_id);
		
						foreach ($tax_rates as $tax_rate) {
							if (!isset($total['taxes'][$tax_rate['tax_rate_id']])) {
								$total['taxes'][$tax_rate['tax_rate_id']] = $tax_rate['amount'];
							} else {
								$total['taxes'][$tax_rate['tax_rate_id']] += $tax_rate['amount'];
							}
						}
					}
	
					$total['total'] += abs($fee);
				} else {
					$total_data[] = array(
						'code'       => 'shipinschk',
						'title'      => $lang['text_order_total'],
						'value'      => $fee,
						'sort_order' => $this->setting['sort_order'],
					);
					
					if ($tax_class_id) {
						$tax_rates = $this->tax->getRates($fee, $tax_class_id);
		
						foreach ($tax_rates as $tax_rate) {
							if (!isset($taxes[$tax_rate['tax_rate_id']])) {
								$taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
							} else {
								$taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
							}
						}
					}
	
					$total += abs($fee);
				} 
			}
		}
	}
	public function getcache() {
		$json['shipinschkradio'] = isset($this->session->data['shipinschk']['radio']) ? $this->session->data['shipinschk']['radio'] : 0;
		
$address = isset($this->session->data['shipping_address']['country_id']) ? $this->session->data['shipping_address'] : array('country_id'=>0, 'zone_id'=>0);
		
		$rowval = array();
		
		foreach ($this->setting['setting'] as $row) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$row['geo_zone_id'] . "' AND (country_id = '" . (int)$address['country_id'] . "' OR country_id = '0') AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

			if ( (!$row['geo_zone_id'] || $query->num_rows) &&
				(float)$row['minttl'] <= $this->cart->getSubTotal() && 
				(int)$row['customer_group_id'] == (int)$this->config->get('config_customer_group_id') && 
				(int)$row['store_id'] == (int)$this->config->get('config_store_id')				
				) {
				$rowval = $row; break;
			}
		} 		
		//$this->log->write($rowval);
		
 		$json['tbl'] = array();

		if($rowval) { 			
			$lang = $this->load->language($this->modpath);
			 						
 			$json['rowval'] = $rowval;
			
			if($rowval['ratetype'] == 1) {
				$json['rate'] = ($this->cart->getSubTotal() * (float)$rowval['rate']) / 100;
			} else {
				$json['rate'] = (float)$rowval['rate'];
			}
					
			$lang['text_yes'] = sprintf($lang['text_yes'], $this->getcurval($json['rate']));
			
			$json['tbl'][] = '<table class="table shipinschk_table"><tr><td>';
			
			$json['tbl'][] = sprintf('%s', $lang['text_msg_ins']);
			
			$sel1 = $json['shipinschkradio'] == 1 ? 'checked' : '';
			$sel0 = $json['shipinschkradio'] == 0 ? 'checked' : '';
			
			$json['tbl'][] = sprintf('<div class="shipinschkradio"><label class="control-label">%s</label> <label class="radio-inline"> <input type="radio" name="shipinschkradio" value="1" %s> %s </label> <label class="radio-inline"> <input type="radio" name="shipinschkradio" value="0" %s> %s </label> </div>',$lang['text_ask_ins'], $sel1, $lang['text_yes'], $sel0, $lang['text_no']);
			
			$json['tbl'][] = sprintf('<input type="hidden" name="shipinschk_fee" value="%s">', $json['rate']);
			$json['tbl'][] = sprintf('<input type="hidden" name="shipinschk_ratetype" value="%s">', $rowval['ratetype']);
			$json['tbl'][] = sprintf('<input type="hidden" name="shipinschk_tax_class_id" value="%s">', $rowval['tax_class_id']);
			
 			$json['tbl'][] = '</td></tr></table>';					
		}
		$json['tbl'] = join($json['tbl']);
		//$this->log->write($json['tbl']);
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	public function save() {
		$lang = $this->load->language($this->modpath);
		$json['msg'] = '';
		
		if(isset($this->request->post['shipinschkradio']) && $this->request->post['shipinschkradio'] == 0) {
			$this->request->post['shipinschkselect'] = '';
		}
		
		$this->session->data['shipinschk']['radio'] = isset($this->request->post['shipinschkradio']) ? $this->request->post['shipinschkradio'] : 0;
		$this->session->data['shipinschk']['fee'] = isset($this->request->post['shipinschk_fee']) ? $this->request->post['shipinschk_fee'] : 0;
		$this->session->data['shipinschk']['ratetype'] = isset($this->request->post['shipinschk_ratetype']) ? $this->request->post['shipinschk_ratetype'] : 0;
		$this->session->data['shipinschk']['tax_class_id'] = isset($this->request->post['shipinschk_tax_class_id']) ? $this->request->post['shipinschk_tax_class_id'] : 0; 
		
		if($this->session->data['shipinschk']['radio'] == 1) {
			$json['msg'] = sprintf('<div class="label label-success">%s</div>', $lang['text_msg_success']);
		}
		if($this->session->data['shipinschk']['radio'] == 0) {
			$json['msg'] = sprintf('<div class="label label-warning">%s</div>', $lang['text_msg_remove']);
		}
		
  		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	
	}
	public function saveinhist($order_id = 0) {
		if($order_id) { 
			$ordq = $this->db->query("select * from `" . DB_PREFIX . "order` where order_id = '" . (int)$order_id . "' ");
			
			$q = $this->db->query("select * from `" . DB_PREFIX . "order_total` where order_id = '" . (int)$order_id . "' and code like 'shipinschk' ");
			
 			if(!empty($q->row['value'])) {
				$fname = $ordq->row['payment_firstname'] ? $ordq->row['payment_firstname'] : $ordq->row['shipping_firstname'];
				$lname = $ordq->row['payment_lastname'] ? $ordq->row['payment_lastname'] : $ordq->row['shipping_lastname'];
				$this->db->query("INSERT INTO " . DB_PREFIX . "shipinschkhistory SET insamount = '".$this->db->escape($q->row['value'])."', order_id = '".$this->db->escape($order_id)."', orderamount = '".$this->db->escape($ordq->row['total'])."', customer_id = '".$this->db->escape($ordq->row['customer_id'])."', customer_name = '".$this->db->escape($fname . ' ' . $lname)."', email = '".$this->db->escape($ordq->row['email'])."', telephone = '".$this->db->escape($ordq->row['telephone'])."', customer_group_id = '".$this->db->escape($ordq->row['customer_group_id'])."', store_id = '".$this->db->escape($ordq->row['store_id'])."', date_added = NOW()");
			}			
		}
 	}
	public function getcurval($rate) {
		return $this->currency->format($rate, $this->session->data['currency']);
	}	
	public function getSetting() {		
		$storeid = (int)$this->config->get('config_store_id');
		$cgid = (int)$this->config->get('config_customer_group_id');
		$langid = (int)$this->config->get('config_language_id');
 		
		$setting = array();		
 		$setting['setting'] = $this->config->get($this->modname.'_setting');	
		$setting['sort_order'] = $this->config->get($this->modname.'_sort_order');
		
 		return $setting;		
	}
	public function loadjscss() {
		if($this->status) {
			$ocstr = substr(VERSION,0,3)=='4.0' ? 'extension/shipinschk/' : '';
			$this->document->addScript($ocstr.'catalog/view/javascript/shipinschk.js?vr='.rand());
			$this->document->addStyle($ocstr.'catalog/view/javascript/shipinschk.css?vr='.rand());			
		}			
	}
}