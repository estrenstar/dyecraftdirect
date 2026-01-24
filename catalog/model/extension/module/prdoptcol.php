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
	private $modsprtor = '/';
	
	private $evntcode = 'prdoptcol';
	private $error = array();
	private $status = '';
	private $setting = array();

	public function __construct($registry) {		
		parent::__construct($registry);		
		ini_set("serialize_precision", -1);
		
		if(substr(VERSION,0,3)=='2.3') {
			$this->modpath = 'extension/module/prdoptcol';
			$this->modvar = 'model_extension_module_prdoptcol';
		}
		if(substr(VERSION,0,3)=='3.0') {			
			$this->modpath = 'extension/module/prdoptcol';
			$this->modvar = 'model_extension_module_prdoptcol';
			$this->modname = 'module_prdoptcol';
		} 
		if(substr(VERSION,0,3)=='4.0') {
			$this->modpath = 'extension/prdoptcol/module/prdoptcol';
			$this->modvar = 'model_extension_prdoptcol_module_prdoptcol';
			$this->modname = 'module_prdoptcol';
			$this->modsprtor = '.';
			$this->divcls = 'row mb-3';
			$this->lblcls = 'col-form-label';
			$this->wellcls = 'form-control';
			$this->grpcls = 'input-group-text';
			$this->slctcls = 'form-select';			
		}
		
		$ismulti_store = 1;
		if($ismulti_store) {
			$this->setting = $this->getSetting();
			$this->status = ($this->config->get($this->modname.'_status') && $this->setting['status']) ? true : false;	
		} else {
			$this->status = $this->config->get($this->modname.'_status');
		}
 	}
	public function getcache() {
		$json = array();
		
		if($this->status && !empty($this->request->post['product_ids'])) {
			$json = array('grid' => array(), 'prdpg' => array());
			
			$this->load->model('tool/image');
			$this->load->model('catalog/product');						
			
			$pids = array_unique($this->request->post['product_ids']);
			if($pids) {
				$disptypegrd = $this->setting['disptypegrd'] == 1 ? ' circle ' : ' square ';
				$disptypeprd = $this->setting['disptypeprd'] == 1 ? ' circle ' : ' square ';
				
				foreach ($pids as $pid) {
					$pq = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product` WHERE product_id = '".(int)$pid."' ");
					$pinfo = $pq->row;
					
					$orgimg_pth = (!empty($pinfo['image']) && is_file(DIR_IMAGE . $pinfo['image'])) ? $pinfo['image'] : 'placeholder.png';
					$orgimggrd = $this->model_tool_image->resize($orgimg_pth, $this->setting['gridw'], $this->setting['gridh']);
					$orgimgprd = $this->model_tool_image->resize($orgimg_pth, $this->setting['prdw'], $this->setting['prdh']);
					$orgimgpopprd = $this->model_tool_image->resize($orgimg_pth, $this->setting['popw'], $this->setting['poph']);
				
					if(substr(VERSION,0,3)=='4.0') {
						$podata = $this->model_catalog_product->getOptions($pid);
					} else {
						$podata = $this->model_catalog_product->getProductOptions($pid);
					}
					
					foreach ($podata as $option) {
						if(!empty($option['prdoptcolflag']) && $option['prdoptcolflag'] == 1) {
							$rstbtntxt_grid = sprintf('<span poid=%s" class="label label-danger badge bg-secondary prdoptcol_gridbtnrst">%s</span>', $option['product_option_id'], $this->setting['rstbtntxt'], $this->setting['rstbtntxt']); 
							
							$rstbtntxt_prdpg = sprintf('<span poid=%s" class="label label-danger badge bg-secondary prdoptcol_prdpg_btnrst">%s</span>', $option['product_option_id'], $this->setting['rstbtntxt'], $this->setting['rstbtntxt']); 	
							
							$pov_data = array();
								
foreach ($option['product_option_value'] as $pov) {
	$pov['price'] = $this->getprice($pov, $pinfo);	
	
	if($pov['price']) { 
		$pov['name'] .= sprintf("(%s%s)", $pov['price_prefix'], $pov['price']);
	}
		
	$swpimg_pth = (!empty($pov['prdoptcolimg']) && is_file(DIR_IMAGE . $pov['prdoptcolimg'])) ? $pov['prdoptcolimg'] : false;
	$colimg_pth = (!empty($pov['prdoptcolimg']) && is_file(DIR_IMAGE . $pov['prdoptcolimg'])) ? $pov['prdoptcolimg'] : 'placeholder.png';
	$ptrnimg_pth = (!empty($pov['prdoptcolptrn']) && is_file(DIR_IMAGE . $pov['prdoptcolptrn'])) ? $pov['prdoptcolptrn'] : 'placeholder.png';
	
	$addimg1_pth = (!empty($pov['prdoptcoladd1']) && is_file(DIR_IMAGE . $pov['prdoptcoladd1'])) ? $pov['prdoptcoladd1'] : false;
	$addimg2_pth = (!empty($pov['prdoptcoladd2']) && is_file(DIR_IMAGE . $pov['prdoptcoladd2'])) ? $pov['prdoptcoladd2'] : false;
	$addimg3_pth = (!empty($pov['prdoptcoladd3']) && is_file(DIR_IMAGE . $pov['prdoptcoladd3'])) ? $pov['prdoptcoladd3'] : false;
	$addimg4_pth = (!empty($pov['prdoptcoladd4']) && is_file(DIR_IMAGE . $pov['prdoptcoladd4'])) ? $pov['prdoptcoladd4'] : false;
	$addimg5_pth = (!empty($pov['prdoptcoladd5']) && is_file(DIR_IMAGE . $pov['prdoptcoladd5'])) ? $pov['prdoptcoladd5'] : false;
	$addimg6_pth = (!empty($pov['prdoptcoladd6']) && is_file(DIR_IMAGE . $pov['prdoptcoladd6'])) ? $pov['prdoptcoladd6'] : false;
	
		
	//PRODUCT PAGE	
	$addimg = array();
	$addimgpop = array();
	$addimg[] = $addimg1_pth ? $this->model_tool_image->resize($addimg1_pth, $this->setting['prdaddw'], $this->setting['prdaddh']) : '';
	$addimg[] = $addimg2_pth ? $this->model_tool_image->resize($addimg2_pth, $this->setting['prdaddw'], $this->setting['prdaddh']) : '';
	$addimg[] = $addimg3_pth ? $this->model_tool_image->resize($addimg3_pth, $this->setting['prdaddw'], $this->setting['prdaddh']) : '';
	$addimg[] = $addimg4_pth ? $this->model_tool_image->resize($addimg4_pth, $this->setting['prdaddw'], $this->setting['prdaddh']) : '';
	$addimg[] = $addimg5_pth ? $this->model_tool_image->resize($addimg5_pth, $this->setting['prdaddw'], $this->setting['prdaddh']) : '';
	$addimg[] = $addimg6_pth ? $this->model_tool_image->resize($addimg6_pth, $this->setting['prdaddw'], $this->setting['prdaddh']) : '';
	
	$addimgpop[] = $addimg1_pth ? $this->model_tool_image->resize($addimg1_pth, $this->setting['popw'], $this->setting['poph']) : '';
	$addimgpop[] = $addimg2_pth ? $this->model_tool_image->resize($addimg2_pth, $this->setting['popw'], $this->setting['poph']) : '';
	$addimgpop[] = $addimg3_pth ? $this->model_tool_image->resize($addimg3_pth, $this->setting['popw'], $this->setting['poph']) : '';
	$addimgpop[] = $addimg4_pth ? $this->model_tool_image->resize($addimg4_pth, $this->setting['popw'], $this->setting['poph']) : '';
	$addimgpop[] = $addimg5_pth ? $this->model_tool_image->resize($addimg5_pth, $this->setting['popw'], $this->setting['poph']) : '';
	$addimgpop[] = $addimg6_pth ? $this->model_tool_image->resize($addimg6_pth, $this->setting['popw'], $this->setting['poph']) : '';
	
	$pov_data[] = array(
		'product_option_value_id' => $pov['product_option_value_id'],
		'option_value_id' => $pov['option_value_id'],
		'name' => $pov['name'],
		'quantity' => $pov['quantity'],
		'priceex' => $pov['price'],
		'price' => $pov['price'],
		'price_prefix' => $pov['price_prefix'],
		'subtract' => $pov['subtract'],
		'weight' => $pov['weight'],
		'weight_prefix' => $pov['weight_prefix'],
		
		'prdoptcolptrn' => $pov['prdoptcolptrn'],
		'prdoptcolimg' => $pov['prdoptcolimg'],
		'prdoptcolcde' => $pov['prdoptcolcde'],
		
		'orgimggrd' => $orgimggrd,
		'swpimggrd' => $swpimg_pth ? $this->model_tool_image->resize($swpimg_pth, $this->setting['gridw'], $this->setting['gridh']) : '',
		'colimggrd' => $this->model_tool_image->resize($colimg_pth, $this->setting['gridcolw'], $this->setting['gridcolh']),
		'ptrnimggrd' => $this->model_tool_image->resize($ptrnimg_pth, $this->setting['gridcolw'], $this->setting['gridcolh']),
		'colcdegrd' => !empty($pov['prdoptcolcde']) ? $pov['prdoptcolcde'] : false,
		
		'orgimgprd' => $orgimgprd,
		'orgimgpopprd' => $orgimgpopprd,
		'swpimgprd' => $swpimg_pth ? $this->model_tool_image->resize($swpimg_pth, $this->setting['prdw'], $this->setting['prdh']) : '',
		'swpimgpopprd' => $swpimg_pth ? $this->model_tool_image->resize($swpimg_pth, $this->setting['popw'], $this->setting['poph']) : '',
		'colimgprd' => $this->model_tool_image->resize($colimg_pth, $this->setting['prdcolw'], $this->setting['prdcolh']),
		'ptrnimgprd' => $this->model_tool_image->resize($ptrnimg_pth, $this->setting['prdcolw'], $this->setting['prdcolh']),
		'colcdeprd' => !empty($pov['prdoptcolcde']) ? $pov['prdoptcolcde'] : false,
		
		'addimg' => $addimg ? implode(",",$addimg) : false,
		'addimgpop' => $addimgpop ? implode(",",$addimgpop) : false,																		
	);
}
														

$option['type'] = $option['type'] == 'checkbox' ? 'checkbox' : 'radio';
 							
if($this->setting['dispgrd']) {
	$grid_html = array();
	$grid_html[] = sprintf('<div class="'.$this->divcls.' %s">', ($option['required'] ? ' required' : ''));
	$grid_html[] = sprintf('<label class="'.$this->lblcls.'" style="text-align: left;">%s %s</label>',$option['name'], $rstbtntxt_grid);
	
	$grid_html[] = sprintf('<input type="hidden" class="prdoptcol_orgimggrd" value="%s" />', $orgimggrd);
	
	$grid_html[] = sprintf('<div id="input-option%s">', $option['product_option_id']);
	foreach ($pov_data as $ov) {
		$grid_html[] = '<div class="prdoptcol_radio_griddiv">';
		if($ov['prdoptcolptrn']) {
			$grid_html[] = sprintf('<img src="%s" class="prdoptcol_atgrid %s" data-toggle="tooltip" data-bs-toggle="tooltip" title="%s" style="width:%spx;height:%spx;"/>', $ov['ptrnimggrd'], $disptypegrd, $ov['name'], $this->setting['gridcolw'], $this->setting['gridcolh']);
		
		} else if($ov['prdoptcolcde']) {
			$grid_html[] = sprintf('<span class="prdoptcol_atgrid %s" data-toggle="tooltip" data-bs-toggle="tooltip" title="%s" style="width:%spx;height:%spx; background:%s"></span>', $disptypegrd, $ov['name'], $this->setting['gridcolw'], $this->setting['gridcolh'], $ov['colcdegrd']);
		
		} else if($ov['prdoptcolimg']) {
			$grid_html[] = sprintf('<img src="%s" class="prdoptcol_atgrid %s" data-toggle="tooltip" data-bs-toggle="tooltip" title="%s"  style="width:%spx;height:%spx;"/>', $ov['colimggrd'], $disptypegrd, $ov['name'], $this->setting['gridcolw'], $this->setting['gridcolh']);
		} else {
			$grid_html[] = sprintf('<span class="prdoptcol_atgrid square">%s</span>', $ov['name']);
		}
		
		$grid_html[] = sprintf('<input class="prdoptcol_radio_input" type="%s" name="option[%s]" value="%s" />', $option['type'], $option['product_option_id'], $ov['product_option_value_id']);
		
		$grid_html[] = sprintf('<input type="hidden" class="prdoptcol_swpimggrd" value="%s" />', $ov['swpimggrd']);
		
		$grid_html[] = '</div>';
	}
	
	$grid_html[] = sprintf('</div></div>'); 

	$json['grid'][$pid][] = join($grid_html);
}	
		
		
					
$prdpg_html = array();
$prdpg_html[] = sprintf('<div class="'.$this->divcls.' %s">', ($option['required'] ? ' required' : ''));
$prdpg_html[] = sprintf('<label class="'.$this->lblcls.'" style="text-align: left;">%s %s</label>',$option['name'], $rstbtntxt_prdpg);

$prdpg_html[] = sprintf('<input type="hidden" class="prdoptcol_orgimgprd" value="%s" />', $orgimgprd);
$prdpg_html[] = sprintf('<input type="hidden" class="prdoptcol_orgimgpopprd" value="%s" />', $orgimgpopprd);

$prdpg_html[] = sprintf('<div id="input-option%s" style="width:100%%;">', $option['product_option_id']);
foreach ($pov_data as $ov) {
	$optname = sprintf('option[%s]',$option['product_option_id']);
	if($option['type'] == 'checkbox') {
		$optname = sprintf('option[%s][]',$option['product_option_id']);
	}
	
	$prdpg_html[] = '<div class="prdoptcol_radio_prdpgdiv">';
	if($ov['prdoptcolptrn']) {
		$prdpg_html[] = sprintf('<img src="%s" class="prdoptcol_atprd %s" data-toggle="tooltip" data-bs-toggle="tooltip" title="%s" style="width:%spx;height:%spx;"/>', $ov['ptrnimgprd'], $disptypeprd, $ov['name'], $this->setting['prdcolw'], $this->setting['prdcolh']);
	
	} else if($ov['prdoptcolcde']) {
		$prdpg_html[] = sprintf('<span class="prdoptcol_atprd %s" data-toggle="tooltip" data-bs-toggle="tooltip" title="%s" style="width:%spx;height:%spx; background:%s"></span>', $disptypeprd, $ov['name'], $this->setting['prdcolw'], $this->setting['prdcolh'], $ov['colcdeprd']);
	
	} else if($ov['prdoptcolimg']) {
		$prdpg_html[] = sprintf('<img src="%s" class="prdoptcol_atprd %s" data-toggle="tooltip" data-bs-toggle="tooltip" title="%s"  style="width:%spx;height:%spx;"/>', $ov['colimgprd'], $disptypeprd, $ov['name'], $this->setting['prdcolw'], $this->setting['prdcolh']);
	
	} else {
		$prdpg_html[] = sprintf('<span class="prdoptcol_atprd square">%s</span>', $ov['name']);
	}
	
	$prdpg_html[] = sprintf('<input class="prdoptcol_radio_input" type="%s" name="%s" value="%s" />', $option['type'], $optname, $ov['product_option_value_id']);
	
	$prdpg_html[] = sprintf('<input type="hidden" class="prdoptcol_swpimgprd" value="%s" />', $ov['swpimgprd']);
	$prdpg_html[] = sprintf('<input type="hidden" class="prdoptcol_swpimgpopprd" value="%s" />', $ov['swpimgpopprd']);
	if($ov['addimg']) {
		$prdpg_html[] = sprintf('<input type="hidden" class="prdoptcol_addimg" value="%s" />', $ov['addimg']);
	}
	if($ov['addimgpop']) {
		$prdpg_html[] = sprintf('<input type="hidden" class="prdoptcol_addimgpop" value="%s" />', $ov['addimgpop']);
	}
	$prdpg_html[] = '</div>';
}

$prdpg_html[] = sprintf('</div></div>'); 

$json['prdpg'][$pid][] = join($prdpg_html);


						}
					}					
				}
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	public function getProductOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order");

		foreach ($product_option_query->rows as $product_option) {
			$pov_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$pov_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'name'                    => $product_option_value['name'],
					'image'                   => $product_option_value['image'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix'],
					'prdoptcolimg' => isset($product_option_value['prdoptcolimg']) ? $product_option_value['prdoptcolimg'] : '',
					'prdoptcolcde' => isset($product_option_value['prdoptcolcde']) ? $product_option_value['prdoptcolcde'] : '',
					'prdoptcolptrn' => isset($product_option_value['prdoptcolptrn']) ? $product_option_value['prdoptcolptrn'] : '',
					'prdoptcoladd1' => isset($product_option_value['prdoptcoladd1']) ? $product_option_value['prdoptcoladd1'] : '',
					'prdoptcoladd2' => isset($product_option_value['prdoptcoladd2']) ? $product_option_value['prdoptcoladd2'] : '',
					'prdoptcoladd3' => isset($product_option_value['prdoptcoladd3']) ? $product_option_value['prdoptcoladd3'] : '',
					'prdoptcoladd4' => isset($product_option_value['prdoptcoladd4']) ? $product_option_value['prdoptcoladd4'] : '',
					'prdoptcoladd5' => isset($product_option_value['prdoptcoladd5']) ? $product_option_value['prdoptcoladd5'] : '',
					'prdoptcoladd6' => isset($product_option_value['prdoptcoladd6']) ? $product_option_value['prdoptcoladd6'] : '',
				);
			}

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'prdoptcolflag' => isset($product_option['prdoptcolflag']) ? $product_option['prdoptcolflag'] : 0,
				'product_option_value' => $pov_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}

		return $product_option_data;
	}
	private function getprice($rs, $pinfo) {		
		$price = false;
		if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$rs['price']) {
			$price = $this->currency->format($this->tax->calculate($rs['price'], $pinfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);		
		} 
		return $price;
	}	
	public function getSetting() {		
		$storeid = (int)$this->config->get('config_store_id');
		$cgid = (int)$this->config->get('config_customer_group_id');
		$langid = (int)$this->config->get('config_language_id');
 		$setting = $this->config->get($this->modname.'_setting');
				
 		$setting['status'] = (!isset($setting[$storeid]['status'])) ? false : $setting[$storeid]['status'];
		$setting['rstbtntxt'] = (!isset($setting[$storeid]['rstbtntxt'][$langid])) ? 'Reset ' : $setting[$storeid]['rstbtntxt'][$langid];
		$setting['dispgrd'] = (!isset($setting[$storeid]['dispgrd'])) ? false : $setting[$storeid]['dispgrd'];
		$setting['disptypegrd'] = (!isset($setting[$storeid]['disptypegrd'])) ? false : $setting[$storeid]['disptypegrd'];
		$setting['gridw'] = (!isset($setting[$storeid]['gridw'])) ? false : $setting[$storeid]['gridw'];
		$setting['gridh'] = (!isset($setting[$storeid]['gridh'])) ? false : $setting[$storeid]['gridh'];
		$setting['gridcolw'] = (!isset($setting[$storeid]['gridcolw'])) ? false : $setting[$storeid]['gridcolw'];
		$setting['gridcolh'] = (!isset($setting[$storeid]['gridcolh'])) ? false : $setting[$storeid]['gridcolh'];
		
		$setting['disptypeprd'] = (!isset($setting[$storeid]['disptypeprd'])) ? false : $setting[$storeid]['disptypeprd'];
		$setting['prdw'] = (!isset($setting[$storeid]['prdw'])) ? false : $setting[$storeid]['prdw'];
		$setting['prdh'] = (!isset($setting[$storeid]['prdh'])) ? false : $setting[$storeid]['prdh'];
		$setting['popw'] = (!isset($setting[$storeid]['popw'])) ? false : $setting[$storeid]['popw'];
		$setting['poph'] = (!isset($setting[$storeid]['poph'])) ? false : $setting[$storeid]['poph'];
		$setting['prdaddw'] = (!isset($setting[$storeid]['prdaddw'])) ? false : $setting[$storeid]['prdaddw'];
		$setting['prdaddh'] = (!isset($setting[$storeid]['prdaddh'])) ? false : $setting[$storeid]['prdaddh'];
		$setting['prdcolw'] = (!isset($setting[$storeid]['prdcolw'])) ? false : $setting[$storeid]['prdcolw'];
		$setting['prdcolh'] = (!isset($setting[$storeid]['prdcolh'])) ? false : $setting[$storeid]['prdcolh'];
		
 		return $setting;		
	}
	public function loadjscss() {
		if($this->status) {
			$ocstr = substr(VERSION,0,3)=='4.0' ? 'extension/prdoptcol/' : '';
			$this->document->addScript($ocstr.'catalog/view/javascript/prdoptcol.js?vr='.rand());
			$this->document->addStyle($ocstr.'catalog/view/javascript/prdoptcol.css?vr='.rand());			
		}			
	}
}