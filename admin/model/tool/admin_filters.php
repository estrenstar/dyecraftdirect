<?php
class ModelToolAdminFilters extends Model {
	
  private $token;
  
  public function __construct($registry) {
		parent::__construct($registry);
    
    $this->token = isset($this->session->data['user_token']) ? 'user_token='.$this->session->data['user_token'] : 'token='.$this->session->data['token'];
	}
  
	public function getOrderFilters() {
    $clearUrl = $this->url->link('sale/order', $this->token);
    
    $orderListFilters = [];
    
    $orderListFilters[] = ['name' => 'filter_order_id', 'type' => 'text', 'placeholder' => $this->language->get('entry_order_id'), 'tooltip' => "Order ID or Invoice number"];
    $orderListFilters[] = ['name' => 'filter_customer', 'type' => 'text', 'placeholder' => $this->language->get('entry_customer'), 'tooltip' => 'Name or email'];
    $orderListFilters[] = ['name' => 'filter_order_status_id', 'type' => 'OrderStatus', 'placeholder' => $this->language->get('entry_order_status')];
    $orderListFilters[] = ['name' => 'filter_total', 'type' => 'text', 'placeholder' => $this->language->get('entry_total'), 'tooltip' => "Exact number: 100\nRange: 10-100\nHigher than: >100\nLower than: <100"];
    $orderListFilters[] = ['name' => 'filter_date_added', 'type' => 'date', 'placeholder' => $this->language->get('entry_date_added')];
    $orderListFilters[] = ['name' => 'filter_date_modified', 'type' => 'date', 'placeholder' => $this->language->get('entry_date_modified')];
    
    $orderListExtraFilters = [];
    
    $orderListExtraFilters[] = ['name' => 'filter_payment_method', 'type' => 'PaymentMethod', 'placeholder' => 'Payment method'];
    $orderListExtraFilters[] = ['name' => 'filter_shipping_method', 'type' => 'ShippingMethod', 'placeholder' => 'Shipping method'];
    
    $extraFiltersActive = false;
    
    foreach ($orderListExtraFilters as $listFilter) {
      if (!empty($this->request->get[$listFilter['name']])) {
        $extraFiltersActive = true;
      }
    }
    
    $output = '<tr class="admin_filters">';
    $output .= '<td></td>';
    foreach ($orderListFilters as $listFilter) {
      $output .= $this->getFilterHtml($listFilter);
    }
    
    $output .= $this->getButtons($clearUrl, !empty($orderListExtraFilters));
    
    $output .= '</tr>';
    
    if (!empty($orderListExtraFilters)) {
      $output .= '<tr class="'.(!$extraFiltersActive ? 'noborder' : '' ).'">';
      $output .= '<td colspan="'.(count($orderListFilters)+2).'" style="padding:0"><div class="extraFilters" '.(!$extraFiltersActive ? 'style="display:none"' : '' ).'>';
      
      foreach ($orderListExtraFilters as $listFilter) {
        $output .= $this->getExtraFilterHtml($listFilter);
      }
      
      $output .= '</div></td>';
      $output .= '</tr>';
    }
    
    return $output;
  }
  
  public function getProductFilters() {
    $clearUrl = $this->url->link('catalog/product', $this->token);
    
    $productListFilters = [];
    
    $productListFilters[] = '';
    $productListFilters[] = ['name' => 'filter_name', 'type' => 'text', 'placeholder' => $this->language->get('entry_name')];
    $productListFilters[] = ['name' => 'filter_model', 'type' => 'text', 'placeholder' => $this->language->get('entry_model')];
    $productListFilters[] = ['name' => 'filter_price', 'type' => 'text', 'placeholder' => $this->language->get('entry_price'), 'tooltip' => "Exact number: 100\nRange: 10-100\nHigher than: >100\nLower than: <100"];
    $productListFilters[] = ['name' => 'filter_quantity', 'type' => 'text', 'placeholder' => $this->language->get('entry_quantity'), 'tooltip' => "Exact number: 100\nRange: 10-100\nHigher than: >100\nLower than: <100"];
    $productListFilters[] = ['name' => 'filter_status', 'type' => 'Status', 'placeholder' => $this->language->get('entry_status')];
    
    $productListExtraFilters = [];
    
    $productListExtraFilters[] = ['name' => 'filter_category', 'type' => 'text', 'placeholder' => 'Category'];
    $productListExtraFilters[] = ['name' => 'filter_manufacturer', 'type' => 'text', 'placeholder' => 'Manufacturer'];
    
    if ($this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'import_batch'")->row) {
      if ($this->db->query("SELECT import_batch FROM " . DB_PREFIX . "product WHERE import_batch <> '' LIMIT 1")->rows) {
        $productListExtraFilters[] = ['name' => 'filter_import_batch', 'type' => 'ImportBatch', 'placeholder' => 'Import label'];
      }
    }
    
    $extraFiltersActive = false;
    
    foreach ($productListExtraFilters as $listFilter) {
      if (!empty($this->request->get[$listFilter['name']])) {
        $extraFiltersActive = true;
      }
    }
    
    $output = '<tr class="admin_filters'.(!empty($productListExtraFilters) ? ' noborder' : '') .'">';
    $output .= '<td></td>';
    foreach ($productListFilters as $listFilter) {
      $output .= $this->getFilterHtml($listFilter);
    }
    
    $output .= $this->getButtons($clearUrl, !empty($productListExtraFilters), $extraFiltersActive);
    
    $output .= '</tr>';
    
    if (!empty($productListExtraFilters)) {
      $output .= '<tr class="'.(!$extraFiltersActive ? 'noborder' : '' ).'">';
      $output .= '<td colspan="'.(count($productListFilters)+2).'" style="padding:0"><div class="extraFilters" '.(!$extraFiltersActive ? 'style="display:none"' : '' ).'>';
      
      foreach ($productListExtraFilters as $listFilter) {
        $output .= $this->getExtraFilterHtml($listFilter);
      }
      
      $output .= '</div></td>';
      $output .= '</tr>';
    }
    
    return $output;
  }
   
  public function getCustomerFilters() {
    $clearUrl = $this->url->link('customer/customer', $this->token);
    
    $customerListFilters = [];
    
    $customerListFilters[] = ['name' => 'filter_name', 'type' => 'text', 'placeholder' => $this->language->get('entry_name')];
    $customerListFilters[] = ['name' => 'filter_email', 'type' => 'text', 'placeholder' => $this->language->get('entry_email')];
    $customerListFilters[] = ['name' => 'filter_customer_group_id', 'type' => 'CustomerGroup', 'placeholder' => $this->language->get('entry_customer_group')];
    $customerListFilters[] = ['name' => 'filter_status', 'type' => 'Status', 'placeholder' => $this->language->get('entry_status')];
    $customerListFilters[] = ['name' => 'filter_ip', 'type' => 'text', 'placeholder' => $this->language->get('entry_ip')];
    $customerListFilters[] = ['name' => 'filter_date_added', 'type' => 'date', 'placeholder' => $this->language->get('entry_date_added')];
    
    $customerListExtraFilters = [];
    
    $output = '<tr class="admin_filters'.(!empty($customerListExtraFilters) ? ' noborder' : '') .'">';
    $output .= '<td></td>';
    
    foreach ($customerListFilters as $listFilter) {
      $output .= $this->getFilterHtml($listFilter);
    }
    
    $output .= $this->getButtons($clearUrl, !empty($customerListExtraFilters));
    
    $output .= '</tr>';
    
    if (!empty($customerListExtraFilters)) {
      $output .= '<tr class="noborder">';
      $output .= '<td colspan="'.(count($customerListFilters)+2).'" style="padding:0"><div class="extraFilters" style="display:none">';
      
      foreach ($customerListExtraFilters as $listFilter) {
        $output .= $this->getExtraFilterHtml($listFilter);
      }
      
      $output .= '</div></td>';
      $output .= '</tr>';
    }
    
    return $output;
  }
  
  public function getButtons($clearUrl, $hasExtraFilters, $extraFiltersActive = false) {
    $output = '';
    
    $buttonsCount = 1;
    
    if (count($this->request->get) > 2) {
      $buttonsCount++;
    }
    
    if ($hasExtraFilters) {
      $buttonsCount++;
    }
    
    if ($buttonsCount > 1) {
      $output .= '<td class="text-right" style="white-space:nowrap"><div class="btn-group">';
      $output .= '<button type="button" id="button-filter" class="btn btn-default"><i class="fa fa-filter"></i> '. $this->language->get('button_filter').'</button>';
      if ($hasExtraFilters) {
        $output .= '<button type="button" id="button-extra-filter" class="btn btn-default" style="padding:8px 11px" ><i class="fa fa-caret-'.(!$extraFiltersActive ? 'down' : 'up').'"></i></button>';
      }
      if (count($this->request->get) > 2) {
        $output .= '<a href="'.$clearUrl.'" class="btn btn-default" style="padding:8px 8px" title="Clear"><i class="fa fa-eraser"></i></button>';
      }
      $output .= '</div></td>';
    } else {
      $output .= '<td class="text-right"><button type="button" id="button-filter" class="btn btn-default"><i class="fa fa-filter"></i> '. $this->language->get('button_filter').'</button></td>';
    }
    
    return $output;
  }
  
	public function getExtraFilterHtml($filter) {
    $output = $this->getFilterHtml($filter);
    
    $output = str_replace('<td>', '<div class="form-group"><label>'.$filter['placeholder'].'</label>', $output);
    $output = str_replace('</td>', '</div>', $output);
    
    return $output;
  }
  
	public function getFilterHtml($filter) {
    $output = '';
    $value = '';
    
    if (!empty($filter['name']) && isset($this->request->get[$filter['name']])) {
      $value = $this->request->get[$filter['name']];
    }
    
    if (empty($filter['type'])) {
      $output .= '<td></td>';
    } else if ($filter['type'] == 'text') {
      $output .= '<td><input type="text" name="'.$filter['name'].'" value="'.$value.'" placeholder="'. $filter['placeholder'].'" id="input-'.$filter['name'].'" '.(isset($filter['tooltip']) ? ' title="'.$filter['tooltip'].'"' : '').' class="form-control"/></td>';
    } else {
      $output .= $this->{'getFilterType'.$filter['type']}($filter, $value);
    }
    
    return $output;
  }
  
  public function getFilterTypeStatus($filter, $value) {
    $output = '';
    
    $output .= '<td>';
    $output .= '<select name="'.$filter['name'].'" id="input-'.$filter['name'].'" class="form-control">';
    $output .= '  <option value=""></option>';
    $output .= '  <option value="1" '.(!empty($value) ? 'selected="selected"' : '').'>'.$this->language->get('text_enabled').'</option>';
    $output .= '  <option value="0" '.($value === '0' ? 'selected="selected"' : '').'>'.$this->language->get('text_disabled').'</option>';
    $output .= '</select>';
    $output .= '</td>';
    
    return $output;
  }
  
  public function getFilterTypePaymentMethod($filter, $value) {
    $getRealMethods = false;
    
    $output = '';
    
    if ($getRealMethods) {
      $shippingQuery = $this->db->query("SELECT DISTINCT payment_method FROM `" . DB_PREFIX . "order` WHERE payment_method <> '' GROUP BY payment_method")->rows;
      
      foreach ($shippingQuery as $shipping) {
        $payment_methods[$shipping['payment_method']] = $shipping['payment_method'];
      }
    } else {
      $this->load->model('setting/extension');
      $paymentQuery = $this->model_setting_extension->getInstalled('payment');
      
      foreach ($paymentQuery as $payment) {
        $this->load->language('extension/shipping/' . $payment, 'extension');
        
        $payment_methods[$payment] = $this->language->get('extension')->get('heading_title');
      }
    }
    
    $output .= '<td>';
    $output .= '<select name="'.$filter['name'].'" id="input-'.$filter['name'].'" class="form-control">';
    $output .= '  <option value=""></option>';
    
    foreach ($payment_methods as $val => $name) {
      $output .= '  <option value="'.$val.'" '.($value == $val ? 'selected="selected"' : '').'>'.$name.'</option>';
    }
    
    $output .= '</select>';
    $output .= '</td>';
    
    return $output;
  }
  
  public function getFilterTypeShippingMethod($filter, $value) {
    $getRealMethods = false;
    
    $output = '';
    
    if ($getRealMethods) {
      $shippingQuery = $this->db->query("SELECT DISTINCT shipping_method FROM `" . DB_PREFIX . "order` WHERE shipping_method <> '' GROUP BY shipping_method")->rows;
      
      foreach ($shippingQuery as $shipping) {
        $shipping_methods[$shipping['shipping_method']] = $shipping['shipping_method'];
      }
    } else {
      $this->load->model('setting/extension');
      $shippingQuery = $this->model_setting_extension->getInstalled('shipping');
      
      foreach ($shippingQuery as $shipping) {
        $this->load->language('extension/shipping/' . $shipping, 'extension');
        
        $shipping_methods[$shipping] = $this->language->get('extension')->get('heading_title');
      }
    }
    
    $output .= '<td>';
    $output .= '<select name="'.$filter['name'].'" id="input-'.$filter['name'].'" class="form-control">';
    $output .= '  <option value=""></option>';
    
    foreach ($shipping_methods as $val => $name) {
      $output .= '  <option value="'.$val.'" '.($value == $val ? 'selected="selected"' : '').'>'.$name.'</option>';
    }
    
    $output .= '</select>';
    $output .= '</td>';
    
    return $output;
  }
  
  public function getFilterTypeOrderStatus($filter, $value) {
    $output = '';
    
    $this->load->model('localisation/order_status');
		$order_statuses = $this->model_localisation_order_status->getOrderStatuses();
    
    $output .= '<td>';
    if (true) {
      $output .= '<select name="'.$filter['name'].'" id="input-'.$filter['name'].'" class="gkdSelect" multiple>';
    } else {
      $output .= '<select name="'.$filter['name'].'" id="input-'.$filter['name'].'" class="form-control">';
    }
    
    $output .= '  <option value=""></option>';
    $output .= '  <option value="0" '.($value == '0' ? 'selected="selected"' : '').'>'.$this->language->get('text_missing').'</option>';
    
    foreach ($order_statuses as $order_status) {
      $output .= '  <option value="'.$order_status['order_status_id'].'" '.(isset($order_status['color']) && $order_status['color'] != '#000000' ? 'style="color:'.$order_status['color'].'"' : '').' '.(in_array($order_status['order_status_id'], explode(',', $value)) ? 'selected="selected"' : '').'>'.$order_status['name'].'</option>';
    }
    
    $output .= '</select>';
    $output .= '</td>';
    
    return $output;
  }
  
  public function getFilterTypeCustomerGroup($filter, $value) {
    $output = '';
    
    $this->load->model('customer/customer_group');
		$customer_groups = $this->model_customer_customer_group->getCustomerGroups();
    
    $output .= '<td>';
    $output .= '<select name="'.$filter['name'].'" id="input-'.$filter['name'].'" class="form-control">';
    $output .= '  <option value=""></option>';
    
    foreach ($customer_groups as $group) {
      $output .= '  <option value="'.$group['customer_group_id'].'" '.($value == $group['customer_group_id'] ? 'selected="selected"' : '').'>'.$group['name'].'</option>';
    }
    
    $output .= '</select>';
    $output .= '</td>';
    
    return $output;
  }
  
  public function getFilterTypeImportBatch($filter, $value) {
    $output = '';
    
    $filter_import_batch = null;
    $importLabels = [];
    
    if ($this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'import_batch'")->row) {
      $importQuery = $this->db->query("SELECT import_batch FROM " . DB_PREFIX . "product WHERE import_batch <> '' GROUP BY import_batch")->rows;
      
      foreach ($importQuery as $importLabel) {
        $importLabels[] = $importLabel['import_batch'];
      }
    }
    
    $output .= '<td>';
    $output .= '<select name="'.$filter['name'].'" id="input-'.$filter['name'].'" class="form-control">';
    $output .= '  <option value="*"></option>';
    
    foreach ($importLabels as $label) {
      $output .= '  <option value="'.$label.'" '.($value == $label ? 'selected="selected"' : '').'>'.$label.'</option>';
    }
    
    $output .= '</select>';
    $output .= '</td>';
    
    return $output;
  }
  
  public function getFilterTypeDate($filter, $value) {
    $output = '';
    
    $output .= '<td><input class="form-control gkdDatePicker" type="text" name="'.$filter['name'].'" value="'.$value.'" placeholder="'. $filter['placeholder'].'" id="input-'.$filter['name'].'"/></td>';
    
    return $output;
  }
  
  public function getScripts($type = '') {
    $data['type'] = $type;
    $data['token'] = $this->token;
    
    return $this->load->view('tool/admin_filters', $data);
  }
  
  public function getProductsQuery($sql, $data) {
    $extraSelect = '';
    $extraJoin = '';
    $extraWhere = '';
    
    if (!empty($this->request->get['filter_manufacturer'])) {
      $extraWhere .= " EXISTS (SELECT 1 FROM " . DB_PREFIX . "manufacturer m WHERE name LIKE '%".$this->db->escape(trim($this->request->get['filter_manufacturer']))."%' AND m.manufacturer_id = p.manufacturer_id) AND ";
    }
    
    if (!empty($this->request->get['filter_category'])) {
      $this->load->model('catalog/category');
      
      $q = trim(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
      
      if (strrpos($q, '>')) {
        $q = trim(substr($q, strrpos($q, '>') + 1));
      }
      
      $q = preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $q); // trim unicode space char
      
      $filter_data = array(
        'filter_name' => $q,
      );
      
      $results = $this->model_catalog_category->getCategories($filter_data);
      
      $filterCategories = array();
      
      foreach ($results as $result) {
        $filterCategories[] = $result['category_id'];
      }
      
      if (!empty($filterCategories)) {
        $extraWhere .= " EXISTS (SELECT 1 FROM " . DB_PREFIX . "product_to_category pc WHERE pc.category_id IN (".$this->db->escape(implode(',', $filterCategories)).") AND pc.product_id = p.product_id) AND ";
      }
    }
    
    $sql = str_replace(' WHERE ', $extraJoin.' WHERE '.$extraWhere, $sql);
    
    return $sql;
  }
  
  public function getOrdersQuery($sql, $data) {
    $extraSelect = '';
    $extraJoin = '';
    $extraWhere = '';
    
    if (!empty($this->request->get['filter_payment_method'])) {
      $extraWhere .= " (payment_code = '".$this->db->escape(trim($this->request->get['filter_payment_method']))."' OR payment_method = '".$this->db->escape(trim($this->request->get['filter_payment_method']))."') AND ";
    }
    
    if (!empty($this->request->get['filter_shipping_method'])) {
      $extraWhere .= " (shipping_code = '".$this->db->escape(trim($this->request->get['filter_shipping_method']))."' OR shipping_method = '".$this->db->escape(trim($this->request->get['filter_shipping_method']))."') AND ";
    }
    
    $sql = str_replace(' WHERE ', $extraJoin.' WHERE '.$extraWhere, $sql);
    
    return $sql;
  }
  
  #extra_functions#
}