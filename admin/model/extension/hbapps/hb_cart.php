<?php
class ModelExtensionHbappsHbCart extends Model {
	public function install(){			
		$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."hb_cart` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `cart_id` int(11) DEFAULT NULL,
		  `hb_cart_log_id` int(11) NOT NULL DEFAULT 0,
		  `customer_id` int(11) NOT NULL,
		  `store_id` int(11) NOT NULL DEFAULT 0,
		  `session_id` varchar(200) NOT NULL,
		  `product_id` int(11) NOT NULL,
		  `recurring_id` int(11) NOT NULL,
		  `option` text NOT NULL,
		  `quantity` int(5) NOT NULL,
		  `language_id` int(11) NOT NULL,
		  `currency` varchar(3) NOT NULL DEFAULT 'USD',
		  `first_email` int(11) NOT NULL DEFAULT 0,
		  `reminder` int(11) NOT NULL DEFAULT 0,
		  `email_viewed` int(11) NOT NULL DEFAULT 0,
		  `last_visited` text NOT NULL,
		  `ip` varchar(40) DEFAULT NULL,
		  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `date_modified` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`)
		  )DEFAULT CHARSET=utf8");
		  
		  $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."hb_cart_log` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `customer_id` int(11) NOT NULL,
		  `session_id` varchar(32) NOT NULL,
		  `email` varchar(200) NOT NULL,
		  `cart_data` text NOT NULL,
		  `first_email` int(11) NOT NULL DEFAULT 0,
		  `reminder` int(11) NOT NULL DEFAULT 0,
		  `email_viewed` int(11) NOT NULL DEFAULT 0,
		  `responded` int(11) NOT NULL DEFAULT 0,
		  `store_id` int(11) NOT NULL,
		  `totals` decimal(15,4) DEFAULT NULL,
		  `order_id` int(11) DEFAULT NULL,
		  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
		  `date_modified` datetime NOT NULL,
		  PRIMARY KEY (`id`)
		  )DEFAULT CHARSET=utf8");
		  
		  $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."cart_guest` (
		   `id` int(11) NOT NULL AUTO_INCREMENT,
		  `session_id` varchar(32) NOT NULL,
		  `guest_details` text NOT NULL,
		  `store_id` int(11) NOT NULL DEFAULT 0,
		  `date_modified` timestamp NOT NULL DEFAULT current_timestamp(),
		  PRIMARY KEY (`id`)
		  )DEFAULT CHARSET=utf8");

		  $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."hb_build_template` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`template_label` varchar(250) NOT NULL,
			`store_id` int(11) NOT NULL DEFAULT '0',
			`loaded_template_id` int(11) NOT NULL,			
			`email_type_id` int(11) NOT NULL,
			`editor` VARCHAR(100) NOT NULL,
			`draft_head` text NOT NULL,
			`draft_body` mediumtext NOT NULL,
			`options` text NOT NULL,
			`email_options` text NOT NULL,
			`cross_selling_options` text NOT NULL,
			`date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`date_modified` datetime DEFAULT NULL,
			PRIMARY KEY (`id`)
		  ) DEFAULT CHARSET=utf8");

		  $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."hb_email_unsubscribe` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`email` varchar(300) NOT NULL,
			`ip` varchar(100) NOT NULL,
			`log_id` int(11) NOT NULL,
			`template_id` int(11) NOT NULL,
			`email_type_id` int(11) NOT NULL,
			`store_id` int(11) NOT NULL,
			`date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
		  ) DEFAULT CHARSET=utf8");

		$table_hb_email_types = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "hb_email_types'");
		
		if ($table_hb_email_types->num_rows){
			$this->db->query("DELETE FROM `" . DB_PREFIX . "hb_email_types` WHERE email_type_id = 7");
			$this->db->query("INSERT INTO `" . DB_PREFIX . "hb_email_types` (`email_type_id`, `label`, `mandatory`) VALUES ('7', 'Abandoned Cart', '0')");
			
			$this->load->model('localisation/language');
			$languages = $this->model_localisation_language->getLanguages();
			
			foreach ($languages as $language){
				$this->db->query("INSERT INTO `" . DB_PREFIX . "hb_email_types_description` (`email_type_id`,`description`,`language_id`) VALUES ('7', 'Abandoned Cart Emails', '".(int)$language['language_id']."')");
			}
		}
		  
		$this->install_ocmod();

		$this->db->query("DELETE FROM `" . DB_PREFIX . "hb_build_template` WHERE email_type_id = 7");
		$this->install_sql();
	}	

	public function install_ocmod(){
		if ((version_compare(VERSION,'2.0.0.0','>=' )) and (version_compare(VERSION,'2.1.0.0','<' ))) {
			$ocmod_filename = 'ocmod_hb_cart_20xx.txt';
			$ocmod_name = 'Abandoned Cart Email [20xx]';
		}elseif ((version_compare(VERSION,'2.1.0.0','>=' )) and (version_compare(VERSION,'2.1.0.2','<=' ))) {
			$ocmod_filename = 'ocmod_hb_cart_21xx.txt';
			$ocmod_name = 'Abandoned Cart Email [21xx]';
		}elseif ((version_compare(VERSION,'2.1.0.2','>' )) and (version_compare(VERSION,'2.2.0.0','<=' ))) {
			$ocmod_filename = 'ocmod_hb_cart_22xx.txt';
			$ocmod_name = 'Abandoned Cart Email [22xx]';
		}else if ((version_compare(VERSION,'2.2.0.0','>' )) and (version_compare(VERSION,'2.3.0.2','<=' ))) {
			$ocmod_filename = 'ocmod_hb_cart_23xx.txt';
			$ocmod_name = 'Abandoned Cart Email [23xx]';
		}else if ((version_compare(VERSION,'3.0.0.0','>' )) and (version_compare(VERSION,'3.0.3.2','<=' ))) {
			$ocmod_filename = 'ocmod_hb_cart_3032.txt';
			$ocmod_name = 'Abandoned Cart Email [3xxx]';
		}else if (version_compare(VERSION,'3.0.3.2','>' )) {
			$ocmod_filename = 'ocmod_hb_cart_3036.txt';
			$ocmod_name = 'Abandoned Cart Email [3xxx]';
		}
		
		$this->db->query("DELETE FROM `" . DB_PREFIX . "modification` WHERE `code` = 'huntbee_abandoned_cart_ocmod'");
		
		$ocmod_version = EXTENSION_VERSION;
		$ocmod_code = 'huntbee_abandoned_cart_ocmod';	
		$ocmod_author = 'HuntBee OpenCart Services';
		$ocmod_link = 'https://www.huntbee.com/';
		
		$file = DIR_APPLICATION . 'view/template/extension/hbapps/hb_cart/ocmod/'.$ocmod_filename;
		if (file_exists($file)) {
			$ocmod_xml = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
			$ocmod_xml = str_replace('{huntbee_version}',$ocmod_version,$ocmod_xml);
			$this->db->query("INSERT INTO " . DB_PREFIX . "modification SET code = '" . $this->db->escape($ocmod_code) . "', name = '" . $this->db->escape($ocmod_name) . "', author = '" . $this->db->escape($ocmod_author) . "', version = '" . $this->db->escape($ocmod_version) . "', link = '" . $this->db->escape($ocmod_link) . "', xml = '" . $this->db->escape($ocmod_xml) . "', status = '1', date_added = NOW()");
		}
	}
	
	public function uninstall() {
		//$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "hb_build_template`"); 
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "hb_cart`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "hb_cart_log`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "hb_cart_unsubscribe`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "cart_guest`");
		
		$this->db->query("DELETE FROM `" . DB_PREFIX . "modification` WHERE `code` = 'huntbee_abandoned_cart_ocmod'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `key` = 'hb_cart_status'");

		$this->db->query("DELETE FROM `" . DB_PREFIX . "hb_build_template` WHERE email_type_id = 7");
	}

	public function update(){
		$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."hb_build_template` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`template_label` varchar(250) NOT NULL,
			`store_id` int(11) NOT NULL DEFAULT '0',
			`loaded_template_id` int(11) NOT NULL,			
			`email_type_id` int(11) NOT NULL,
			`editor` VARCHAR(100) NOT NULL,
			`draft_head` text NOT NULL,
			`draft_body` mediumtext NOT NULL,
			`options` text NOT NULL,
			`email_options` text NOT NULL,
			`cross_selling_options` text NOT NULL,
			`date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`date_modified` datetime DEFAULT NULL,
			PRIMARY KEY (`id`)
		  ) DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."hb_email_unsubscribe` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`email` varchar(300) NOT NULL,
			`ip` varchar(100) NOT NULL,
			`log_id` int(11) NOT NULL,
			`template_id` int(11) NOT NULL,
			`email_type_id` int(11) NOT NULL,
			`store_id` int(11) NOT NULL,
			`date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
		  ) DEFAULT CHARSET=utf8");

		$table_hb_cart_unsubscribe = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "hb_cart_unsubscribe'");
		
		if ($table_hb_cart_unsubscribe->num_rows){
			$this->copy_unsubscribers();
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "hb_cart_unsubscribe`");
		}

		$query_email_type_id = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "hb_build_template` LIKE 'email_type_id'");
		if (!$query_email_type_id->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "hb_build_template` ADD `email_type_id` INT NOT NULL AFTER `loaded_template_id`");
		}

		$table_hb_email_types = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "hb_email_types'");
		if ($table_hb_email_types->num_rows){
			$this->db->query("DELETE FROM `" . DB_PREFIX . "hb_email_types` WHERE email_type_id = 7");
			$this->db->query("INSERT INTO `" . DB_PREFIX . "hb_email_types` (`email_type_id`, `label`, `mandatory`) VALUES ('7', 'Abandoned Cart', '0')");
			
			$this->load->model('localisation/language');
			$languages = $this->model_localisation_language->getLanguages();
			
			foreach ($languages as $language){
				$this->db->query("INSERT INTO `" . DB_PREFIX . "hb_email_types_description` (`email_type_id`,`description`,`language_id`) VALUES ('7', 'Abandoned Cart Emails', '".(int)$language['language_id']."')");
			}
		}	

		$this->install_sql();

		$this->install_ocmod();
	}
	
	public function install_sql() {
		$file = DIR_APPLICATION . 'view/template/extension/hbapps/hb_cart/sql/hb_cart.sql';

		if (file_exists($file)) {
			$lines = file($file);
	
			if ($lines) {
				$sql = '';
	
				foreach($lines as $line) {
					if ($line && (substr($line, 0, 2) != '--') && (substr($line, 0, 1) != '#')) {
						$sql .= $line;
	
						if (preg_match('/;\s*$/', $line)) {
							$sql = str_replace("DROP TABLE IF EXISTS `oc_", "DROP TABLE IF EXISTS `" . DB_PREFIX, $sql);
							$sql = str_replace("CREATE TABLE `oc_", "CREATE TABLE `" . DB_PREFIX, $sql);
							$sql = str_replace("INSERT INTO `oc_", "INSERT INTO `" . DB_PREFIX, $sql);
							$sql = str_replace("{store_url}", HTTPS_CATALOG, $sql);
							$sql = str_replace("{config_email}", $this->config->get('config_email'), $sql);
							$sql = str_replace("{config_name}", $this->config->get('config_name'), $sql);
	
							$this->db->query($sql);
	
							$sql = '';
						}
					}
				}
			}
		}
	}

	public function copyoldercarts(){
		if (version_compare(VERSION,'2.1.0.0', '>')) {
			$records = $this->db->query("SELECT * FROM `" . DB_PREFIX . "cart` WHERE customer_id > 0 AND cart_id NOT IN (SELECT cart_id FROM `" . DB_PREFIX . "hb_cart`)");
			if ($records->rows){
				foreach ($records->rows as $row) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "hb_cart` (`cart_id`, `customer_id`, `store_id`, `session_id`, `product_id`, `recurring_id`, `option`, `quantity`, `language_id`, `currency`) VALUES ('".(int)$row['cart_id']."','".(int)$row['customer_id']."','".(int)$this->config->get('config_store_id')."','".$this->db->escape($row['session_id'])."','".(int)$row['product_id']."','".(int)$row['recurring_id']."','".$this->db->escape($row['option'])."','".(int)$row['quantity']."', '" . (int)$this->config->get('config_language_id') . "','".$this->db->escape($this->config->get('config_currency'))."')"); 
				}
			}
		}
	}

	public function copy_unsubscribers(){
		$records = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hb_cart_unsubscribe`");
		if ($records->rows){
			foreach ($records->rows as $row) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "hb_email_unsubscribe` (`email`, `ip`, `log_id`, `template_id`, `email_type_id`, `store_id`, `date_added`) VALUES ('".$this->db->escape($row['email'])."','".$this->db->escape($row['ip'])."','0','0','7','".(int)$row['store_id']."','".$this->db->escape($row['date_added'])."')"); 
			}
		}
	}

	public function table_columns(){
		$columns = array('customer_name','phone','since','purchases','session','ip','email_viewed','date_active','date_modified', 'time_inactive');
		return $columns;
	}

	public function isExtensionInstalled($code){
		$query = $this->db->query("SELECT count(*) as total FROM `".DB_PREFIX."extension` WHERE `code` = '".$this->db->escape($code)."'");	
		if ($query->row['total'] > 0){
			return true;
		}else{
			return false;
		}
	}
	
	public function validateCustomerPurchases($email, $order_status_id){
		$query = $this->db->query("SELECT sum(total) as total FROM `".DB_PREFIX."order` WHERE `email` = '".$this->db->escape($email)."' AND order_status_id IN ('".implode("','",$order_status_id)."')");
		if ($query->row) {
			$amount = $query->row['total'];
		}else{
			$amount = 0;
		}
		
		return $this->currency->format($amount, $this->config->get('config_currency'));
	}

	public function getCustomerDays($email){
		$query = $this->db->query("SELECT datediff(now(), date_added) as days FROM  `".DB_PREFIX."customer` WHERE `email` = '".$this->db->escape($email)."'");
		if ($query->row) {
			$days = $query->row['days'];
			$start_date = new DateTime(date("Y/m/d"));
			$end_date = new DateTime(date("Y/m/d",strtotime("+$days days")));
			$dd = date_diff($start_date,$end_date);
			return "$dd->y year(s) $dd->m month(s) $dd->d day(s)";			
		}else{
			return $this->language->get('text_guest');
		}
	}
	
	public function getCustomerCarts($data){
		$sql = "SELECT * FROM `".DB_PREFIX."hb_cart` WHERE customer_id <> 0 AND store_id = '".(int)$data['store_id']."'";

		$sql .=  ' GROUP BY customer_id ORDER BY date_added DESC ';
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
	
	public function getTotalCustomerCarts($store_id){
		$sql = "SELECT count(distinct(customer_id)) as total FROM `".DB_PREFIX."hb_cart` WHERE customer_id <> 0 AND store_id = '".(int)$store_id."'";
        $query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getGuestCarts($data){
		$sql = "SELECT *, a.date_modified as last_modified FROM `".DB_PREFIX."hb_cart` a, `".DB_PREFIX."cart_guest` b WHERE a.session_id = b.session_id AND a.store_id = '".(int)$data['store_id']."' AND a.customer_id = 0";

		$sql .=  ' GROUP BY a.session_id ORDER BY a.date_added DESC ';
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
	
	public function getTotalGuestCarts($store_id){
		$sql = "SELECT count(distinct(a.session_id)) as total FROM `".DB_PREFIX."hb_cart` a, `".DB_PREFIX."cart_guest` b WHERE a.session_id = b.session_id AND a.customer_id = 0 AND a.store_id = '".(int)$store_id."'";
        $query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getUnidentifiedCarts($data){
		$sql = "SELECT * FROM `".DB_PREFIX."hb_cart` WHERE session_id NOT IN (SELECT session_id FROM ".DB_PREFIX."cart_guest) AND store_id = '".(int)$data['store_id']."' AND customer_id = 0";

		$sql .=  ' GROUP BY session_id ORDER BY date_added DESC ';
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
	
	
	public function getTotalUnidentifiedCarts($store_id){
		$sql = "SELECT count(distinct(session_id)) as total FROM `".DB_PREFIX."hb_cart` WHERE session_id NOT IN (SELECT session_id FROM ".DB_PREFIX."cart_guest) AND customer_id = 0 AND store_id = '".(int)$store_id."'";
        $query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getTotalAbandonedCarts(){
		$sql = "SELECT count(distinct(session_id)) as total FROM `".DB_PREFIX."hb_cart`";
        $query = $this->db->query($sql);
		return $query->row['total'];
	}


	public function getGuestInfo($session_id){
		$query = $this->db->query("SELECT * FROM `".DB_PREFIX."cart_guest` WHERE session_id = '".$this->db->escape($session_id)."' LIMIT 1");
		if ($query->row['guest_details']) {
			$guest_info = json_decode($query->row['guest_details'], true);
		}else{
			$guest_info = array();
		}
		
		return $guest_info;
	}
	
	public function getUnsubscribe($data){
		$sql = "SELECT * FROM `".DB_PREFIX."hb_email_unsubscribe` WHERE store_id = '".(int)$data['store_id']."' AND email_type_id = 7 ORDER BY date_added DESC";
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
	
	public function getTotalUnsubscribe($store_id){
		$sql = "SELECT count(*) as total FROM `".DB_PREFIX."hb_email_unsubscribe` WHERE store_id = '".(int)$store_id."' AND email_type_id = 7";
        $query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getCartLog($data){
		$sql = "SELECT * FROM `".DB_PREFIX."hb_cart_log` WHERE store_id = '".(int)$data['store_id']."' ORDER BY date_added DESC";
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
	
	public function getTotalCartLog($store_id){
		$sql = "SELECT count(*) as total FROM `".DB_PREFIX."hb_cart_log` WHERE store_id = '".(int)$store_id."'";
        $query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getTotalLogUniqueEmails($store_id){
		$sql = "SELECT count(distinct(email)) as total FROM `".DB_PREFIX."hb_cart_log` WHERE store_id = '".(int)$store_id."'";
        $query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getTotalLogRecords($store_id){
		$sql = "SELECT count(*) as total FROM `".DB_PREFIX."hb_cart_log` WHERE store_id = '".(int)$store_id."'";
        $query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getTotalLogEmailViewed($store_id){
		$sql = "SELECT sum(email_viewed) as total FROM `".DB_PREFIX."hb_cart_log` WHERE store_id = '".(int)$store_id."'";
        $query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getTotalLogRecordsResponded($store_id){
		$sql = "SELECT count(*) as total FROM `".DB_PREFIX."hb_cart_log` WHERE responded = 1 AND store_id = '".(int)$store_id."'";
        $query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getTotalLogFirstEmail($store_id){
		$sql = "SELECT sum(first_email) as total FROM `".DB_PREFIX."hb_cart_log` WHERE first_email = 1 AND store_id = '".(int)$store_id."'";
        $query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getTotalLogReminderEmail($store_id){
		$sql = "SELECT sum(reminder) as total FROM `".DB_PREFIX."hb_cart_log` WHERE store_id = '".(int)$store_id."'";
        $query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getTotalLogOrders($store_id){
		$sql = "SELECT count(distinct(order_id)) as total FROM `".DB_PREFIX."hb_cart_log` WHERE store_id = '".(int)$store_id."'";
        $query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getTotalSales($store_id){
		$sql = "SELECT sum(totals) as total FROM `".DB_PREFIX."hb_cart_log` WHERE store_id = '".(int)$store_id."'";
        $query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getTotalCartsByDay() {
		$cart_data = array();

		for ($i = 0; $i < 24; $i++) {
			$cart_data[$i] = array(
				'hour'  => $i,
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(distinct(session_id)) AS total, HOUR(date_added) AS hour FROM `" . DB_PREFIX . "hb_cart` WHERE DATE(date_added) = DATE(NOW()) GROUP BY HOUR(date_added) ORDER BY date_added ASC");

		foreach ($query->rows as $result) {
			$cart_data[$result['hour']] = array(
				'hour'  => $result['hour'],
				'total' => $result['total']
			);
		}

		return $cart_data;
	}

	public function getTotalCartsByMonth() {
		$cart_data = array();

		for ($i = 1; $i <= date('t'); $i++) {
			$date = date('Y') . '-' . date('m') . '-' . $i;

			$cart_data[date('j', strtotime($date))] = array(
				'day'   => date('d', strtotime($date)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(distinct(session_id)) AS total, date_added FROM `" . DB_PREFIX . "hb_cart` WHERE DATE(date_added) >= '" . $this->db->escape(date('Y') . '-' . date('m') . '-1') . "' GROUP BY DATE(date_added)");

		foreach ($query->rows as $result) {
			$cart_data[date('j', strtotime($result['date_added']))] = array(
				'day'   => date('d', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $cart_data;
	}

	public function getTotalCartsByYear() {
		$cart_data = array();

		for ($i = 1; $i <= 12; $i++) {
			$cart_data[$i] = array(
				'month' => date('M', mktime(0, 0, 0, $i)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(distinct(session_id)) AS total, date_added FROM `" . DB_PREFIX . "hb_cart` WHERE YEAR(date_added) = YEAR(NOW()) GROUP BY MONTH(date_added)");

		foreach ($query->rows as $result) {
			$cart_data[date('n', strtotime($result['date_added']))] = array(
				'month' => date('M', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $cart_data;
	}
	
	public function getTotalCartsProductsByDay() {
		$cart_data = array();

		for ($i = 0; $i < 24; $i++) {
			$cart_data[$i] = array(
				'hour'  => $i,
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT SUM(quantity) AS total, HOUR(date_added) AS hour FROM `" . DB_PREFIX . "hb_cart` WHERE DATE(date_added) = DATE(NOW()) GROUP BY HOUR(date_added) ORDER BY date_added ASC");

		foreach ($query->rows as $result) {
			$cart_data[$result['hour']] = array(
				'hour'  => $result['hour'],
				'total' => $result['total']
			);
		}

		return $cart_data;
	}

	public function getTotalCartsProductsByMonth() {
		$cart_data = array();

		for ($i = 1; $i <= date('t'); $i++) {
			$date = date('Y') . '-' . date('m') . '-' . $i;

			$cart_data[date('j', strtotime($date))] = array(
				'day'   => date('d', strtotime($date)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT SUM(quantity) AS total, date_added FROM `" . DB_PREFIX . "hb_cart` WHERE DATE(date_added) >= '" . $this->db->escape(date('Y') . '-' . date('m') . '-1') . "' GROUP BY DATE(date_added)");

		foreach ($query->rows as $result) {
			$cart_data[date('j', strtotime($result['date_added']))] = array(
				'day'   => date('d', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $cart_data;
	}

	public function getTotalCartsProductsByYear() {
		$cart_data = array();

		for ($i = 1; $i <= 12; $i++) {
			$cart_data[$i] = array(
				'month' => date('M', mktime(0, 0, 0, $i)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT SUM(quantity) AS total, date_added FROM `" . DB_PREFIX . "hb_cart` WHERE YEAR(date_added) = YEAR(NOW()) GROUP BY MONTH(date_added)");

		foreach ($query->rows as $result) {
			$cart_data[date('n', strtotime($result['date_added']))] = array(
				'month' => date('M', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $cart_data;
	}
	
	public function getProducts($session_id, $customer_id, $store_id) {
		$product_data = array();

		$cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "hb_cart WHERE session_id = '" . $this->db->escape($session_id) . "' AND customer_id = '".(int)$customer_id."'");

		foreach ($cart_query->rows as $cart) {
			$stock = true;

			$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store p2s LEFT JOIN " . DB_PREFIX . "product p ON (p2s.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p2s.store_id = '" . (int)$store_id . "' AND p2s.product_id = '" . (int)$cart['product_id'] . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");

			if ($product_query->num_rows && ($cart['quantity'] > 0)) {
				$option_price = 0;
				$option_points = 0;
				$option_weight = 0;

				$option_data = array();

				foreach (json_decode($cart['option']) as $product_option_id => $value) {
					$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$cart['product_id'] . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($option_query->num_rows) {
						if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio' || $option_query->row['type'] == 'image') {
							$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

							if ($option_value_query->num_rows) {
								$option_data[] = array(
									'product_option_id'       => $product_option_id,
									'product_option_value_id' => $value,
									'option_id'               => $option_query->row['option_id'],
									'option_value_id'         => $option_value_query->row['option_value_id'],
									'name'                    => $option_query->row['name'],
									'value'                   => $option_value_query->row['name'],
									'type'                    => $option_query->row['type']
								);
							}
						} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
							foreach ($value as $product_option_value_id) {
								$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

								if ($option_value_query->num_rows) {
									
									$option_data[] = array(
										'product_option_id'       => $product_option_id,
										'product_option_value_id' => $product_option_value_id,
										'option_id'               => $option_query->row['option_id'],
										'option_value_id'         => $option_value_query->row['option_value_id'],
										'name'                    => $option_query->row['name'],
										'value'                   => $option_value_query->row['name'],
										'type'                    => $option_query->row['type']
									);
								}
							}
						} elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
							$option_data[] = array(
								'product_option_id'       => $product_option_id,
								'product_option_value_id' => '',
								'option_id'               => $option_query->row['option_id'],
								'option_value_id'         => '',
								'name'                    => $option_query->row['name'],
								'value'                   => $value,
								'type'                    => $option_query->row['type']
							);
						}
					}
				}

				$price = $product_query->row['price'];

				// Product Discounts
				$discount_quantity = 0;

				foreach ($cart_query->rows as $cart_2) {
					if ($cart_2['product_id'] == $cart['product_id']) {
						$discount_quantity += $cart_2['quantity'];
					}
				}

				$product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity <= '" . (int)$discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

				if ($product_discount_query->num_rows) {
					$price = $product_discount_query->row['price'];
				}

				// Product Specials
				$product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

				if ($product_special_query->num_rows) {
					$price = $product_special_query->row['price'];
				}

				$product_data[] = array(
					'cart_id'         => $cart['cart_id'],
					'product_id'      => $product_query->row['product_id'],
					'name'            => $product_query->row['name'],
					'model'           => $product_query->row['model'],
					'image'           => $product_query->row['image'],
					'option'          => $option_data,
					'quantity'        => $cart['quantity'],
					'price'           => ($price + $option_price),
					'total'           => ($price + $option_price) * $cart['quantity'],
					'currency'         => $cart['currency'],
				);
			} 
		}

		return $product_data;
	}

	//EMAIL TEMPLATES START
	public function simple_layout_templates($template_folder_path){
		$files = array_diff(scandir($template_folder_path), array('.', '..'));
		$data['templates'] = array();
		foreach ($files as $file) {
			$file = str_replace('.txt','',$file);
			$file = str_replace('.tpl','',$file);
			$file = str_replace('.twig','',$file);
			$data['templates'][] = array(
				'label' => str_replace('_',' ',$file),
				'value'	=> $file
			);
		}
		return $data['templates'];
	}

	public function addTemplateInstance($template_label, $email_type_id, $store_id) {
		$email_options = array(
			'sender_name' 	=> $this->config->get('config_name'),
			'sender_email' 	=> $this->config->get('config_email'),
			'email_bcc' 	=> '',
			'email_replyto' => '',
			'email_subject' => 'Enter Subject Line!!!',
			'email_type_id' => $email_type_id,
		);
		$this->db->query("INSERT INTO " . DB_PREFIX . "hb_build_template (`template_label`,`store_id`,`loaded_template_id`, `email_type_id`,`editor`, `email_options`) VALUES ('" . $this->db->escape($template_label). "','".(int)$store_id."','9999', '".(int)$email_type_id."', 'ckeditor', '".$this->db->escape(json_encode($email_options))."')");
		return $this->db->getLastId();
	}

	public function deleteTemplate($id){
		$this->db->query("DELETE FROM `" . DB_PREFIX . "hb_build_template` WHERE `id` = '".(int)$id."'");
	}

	public function getEmailTemplates($email_type_id, $store_id){
		$results = $this->db->query("SELECT * FROM `".DB_PREFIX."hb_build_template` WHERE `store_id` = '".(int)$store_id."' AND email_type_id = '".(int)$email_type_id."' ORDER BY date_modified DESC");
		return $results->rows;
	}

	public function getTemplates($data){
		$sql = "SELECT * FROM `".DB_PREFIX."hb_build_template` WHERE store_id = '".(int)$data['store_id']."' AND email_type_id = '".(int)$data['email_type_id']."'";
		if (!empty($data['search'])) {
			$sql .= " AND `template_label` LIKE '%".$this->db->escape($data['search'])."%'";
		}
		$sql .= " ORDER BY date_added DESC";
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
	
	public function getTotalTemplates($data){
		$sql = "SELECT count(*) as total FROM `".DB_PREFIX."hb_build_template` WHERE store_id = '".(int)$data['store_id']."' AND email_type_id = '".(int)$data['email_type_id']."'";
		if (!empty($data['search'])) {
			$sql .= " AND `template_label` LIKE '%".$this->db->escape($data['search'])."%'";
		}
		$sql .= " ORDER BY date_added DESC";
		$results = $this->db->query($sql);
		return $results->row['total'];
	}
	
	public function getTemplate($id){
		$results = $this->db->query("SELECT * FROM `".DB_PREFIX."hb_build_template` WHERE id = '".(int)$id."' LIMIT 1");
		return $results->row;
	}
	//EMAIL TEMPLATES END


	
}
?>