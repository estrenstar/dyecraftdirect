<?php
class ControllerToolAdminFilters extends Controller {
  
  private $token;
  
  public function __construct($registry) {
		parent::__construct($registry);
    
    $this->token = isset($this->session->data['user_token']) ? 'user_token='.$this->session->data['user_token'] : 'token='.$this->session->data['token'];
	}
  
	public function index() {}
  
	public function autocomplete_order_id() {
    $json = array();
    
    $q = isset($this->request->get['q']) ? $this->request->get['q'] : '';
    
    $this->load->model('sale/order');
    
    $filter_data = array(
      'filter_order_id'   => $q,
      'start'            => 0,
      'limit'            => 5
    );
    
    $results = $this->model_sale_order->getOrders($filter_data);

    foreach ($results as $result) {
      $item = $this->model_sale_order->getOrder($result['order_id']);
      
      if ($item['invoice_no']) {
        $order_id = $item['invoice_prefix'].$item['invoice_no'];
      } else {
        $order_id = $item['order_id'];
      }
      
      $json[] = array(
        'label'       => '<b>'.$order_id.'</b><br>'.$result['customer'],
        'value'       => $order_id,
        'link'        => str_replace('&amp;', '&', $this->url->link('sale/order/info', $this->token.'&order_id='.$item['order_id'])),
      );
    }
    
    // sort results
    if (true) {
      $sort_order = array();

      foreach ($json as $key => $value) {
        $sort_order[$key] = $value['value'];
      }

      array_multisort($sort_order, SORT_DESC, $json);
    }
    
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
  
  public function autocomplete_customer() {
		$json = array();

    $q = isset($this->request->get['q']) ? trim($this->request->get['q']) : '';
    
    $this->load->model('customer/customer');

    $filter_data = array(
      'filter_name'      => $q,
      'start'            => 0,
      'limit'            => 5
    );

    //$results = $this->model_customer_customer->getCustomers($filter_data);
    
    $results = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer c WHERE (CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($q) . "%' OR CONCAT(c.lastname, ' ', c.firstname) LIKE '%" . $this->db->escape($q) . "%') OR c.email LIKE '%" . $this->db->escape($q) . "%' LIMIT 5")->rows;
    
    foreach ($results as $result) {
      $json[] = array(
        'label'       => '<b>'.$result['firstname'].' '.$result['lastname'].'</b><br>'.$result['email'],
        'value'       => $result['firstname'].' '.$result['lastname'],
        'link'        => str_replace('&amp;', '&', $this->url->link('sale/order', $this->token.'&filter_customer='.$result['firstname'].' '.$result['lastname'])),
      );
    }

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['label'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
  
  public function autocomplete_manufacturer() {
		$json = array();

    $q = isset($this->request->get['q']) ? trim($this->request->get['q']) : '';
    
    $this->load->model('catalog/manufacturer');

    $filter_data = array(
      'filter_name'      => $q,
      'start'            => 0,
      'limit'            => 5
    );

    $results = $this->model_catalog_manufacturer->getManufacturers($filter_data);
    
    $results = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "manufacturer WHERE name LIKE '%" . $this->db->escape($q) . "%' LIMIT 5")->rows;
    
    foreach ($results as $result) {
      $json[] = array(
        'label'       => $result['name'],
        'value'       => $result['name'],
        'link'        => str_replace('&amp;', '&', $this->url->link('catalog/product', $this->token.'&filter_manufacturer='.$result['name'])),
      );
    }

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['label'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
  
  
  public function autocomplete_category() {
    $json = array();

    $q = isset($this->request->get['q']) ? trim($this->request->get['q']) : '';
    
    if ($q == '') {
      
    }
    
    $this->load->model('catalog/category');

    $filter_data = array(
      'filter_name' => $q,
      'sort'        => 'name',
      'order'       => 'ASC',
      'start'       => 0,
      'limit'       => 5
    );

    $results = $this->model_catalog_category->getCategories($filter_data);

    foreach ($results as $result) {
      $json[] = array(
        'label'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
        'value'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
        'link'        => str_replace('&amp;', '&', $this->url->link('catalog/product', $this->token.'&filter_category='.$result['name'])),
      );
    }

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['label'];
		}

		array_multisort($sort_order, SORT_ASC, $json);
    
    $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  
}