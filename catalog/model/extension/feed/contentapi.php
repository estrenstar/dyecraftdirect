<?php
class ModelExtensionFeedContentapi extends Model {

	private $setting = [];

	public function getFeed($feed_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "contentapi WHERE feed_id = '" . (int)$feed_id . "' AND status = '1'");

		$this->setting = $query->row;

		$arrays = ['categorybl','productbl','manufacturerbl','cron_action'];
		foreach ($arrays as $field) {
			if (!empty($this->setting[$field])) {
				$this->setting[$field] = json_decode($this->setting[$field], true);
			}
		}

		return $query->row;
	}

	public function getPreData($product_id, $offerId) {
		$query = $this->db->query("SELECT status, stock_status_id, ".$this->db->escape($offerId)." FROM " . DB_PREFIX . "product WHERE product_id = '".(int)$product_id."'");
		return $query->row;
	}

	public function getProductCondition($table, $field, $product_id) {
		$query = $this->db->query("SELECT `".$field."` FROM `" . $table . "` WHERE product_id = '".(int)$product_id."'");
		return $query->num_rows?$query->row[$field]:'';
	}

	public function getTotalProducts($data) {
		$products = [];

		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total";

		if (!empty($data['select']) && $data['select']=='filter') {

			if (!empty($data['categories']) ) {
				if (!empty($data['sub_category'])) {
					$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
				} else {
					$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
				}

				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
				
			} else {
				$sql .= " FROM " . DB_PREFIX . "product p";
			}

			$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

			if (!empty($data['filtera']) ) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_attribute pa on p.product_id = pa.product_id";
			}

			if (!empty($data['filter']) ) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_option_value pov on p.product_id = pov.product_id";
			}

			if (!empty($data['filtervf']) ) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf on p.product_id = pf.product_id";
			}

			$sql .= " WHERE p.status = '1' AND p2s.store_id = '" . (int)$data['store_id'] . "'";

			if (!empty($data['categories'])) {
				if (!empty($data['sub_category'])) {
					$sql .= " AND cp.path_id IN ('" . implode("','", $data['categories']) . "')";
				} else {
					$sql .= " AND p2c.category_id IN ('" . implode("','", $data['categories']) . "')";
				}
			}

			if (!empty($data['manufacturers'])) {
				$sql .= " AND p.manufacturer_id IN ('" . implode("','", $data['manufacturers']) . "')";
			}

			if (!empty($data['suppler'])) {
				$sql .= " AND p.suppler_code = '" . $this->db->escape($data['suppler']) . "'";
			}

			if (!empty($data['filtera']) ) {
				$attributes = [];
				$attributes_values = [];
				$attr = [];
				foreach ($data['filtera'] as $key => $item) {
					if ($item != '' && $data['filterva'][$key] != '') {
						$attr[] = "(pa.attribute_id = '" . $item . "' AND pa.text = '" . $data['filterva'][$key] . "')";
					}
				}
				$count_attr = count($attr);
				if ($count_attr) {
					$attr_sql = implode(' OR ', $attr);
					$sql .= 'AND (' . $attr_sql . ')';
				}
			}

			if (!empty($data['filter']) ) {
				$options = [];
				$options_values = [];
				foreach ($data['filter'] as $key => $item) {
					if ($item != '') {
						if ($data['filterv'][$key] != '') {
							$options[] = $item;
							$options_values[] = $data['filterv'][$key];
						}
					}
				}
				
				if ($options && $options_values) {
					$options = implode(',', $options);
					$options_values = implode(',', $options_values);
					$sql .= " AND pov.option_id IN ($options) AND pov.option_value_id IN ($options_values)";
				}
			}

			if (!empty($data['filtervf']) ) {
				$filters_values = [];
				foreach ($data['filtervf'] as $key => $item) {
					if ($item != '') {
						$filters_values[] = $item;
					}
				}
				
				if ($filters_values) {
					$filters_values = implode(',', $filters_values);
					$sql .= " AND pf.filter_id IN ($filters_values)";
				}
			}

		} else {
			$sql .= " FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p2s.store_id = '" . (int)$data['store_id'] . "'";
		}

		if (!empty($data['minimum'])) {
			$sql .= " AND p.quantity >= " . (float)$data['minimum'];
		}

		if (!empty($data['price_from'])) {
			$sql .= " AND p.price >= " . (float)$data['price_from'];
		}

		if (!empty($data['price_to'])) {
			$sql .= " AND p.price <= " . (float)$data['price_to'];
		}

		if (empty($data['noimage'])) {
			$sql .= " AND p.image <> ''";
		}

		if (!empty($count_attr)) {
			$sql .= " HAVING COUNT(DISTINCT pa.attribute_id) = " . $count_attr;
		}

		$query = $this->db->query($sql);

		if($query->num_rows) {
			return $query->row['total'];
		} else {
			return false;
		}
	}

	public function getProducts($data = []) {
		$products = [];

		$sql = "SELECT p.product_id";

		if (!empty($data['select']) && $data['select']=='filter') {

			if (!empty($data['categories']) ) {
				if (!empty($data['sub_category'])) {
					$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
				} else {
					$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
				}

				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
				
			} else {
				$sql .= " FROM " . DB_PREFIX . "product p";
			}

			$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

			if (!empty($data['filtera']) ) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_attribute pa on p.product_id = pa.product_id";
			}

			if (!empty($data['filter']) ) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_option_value pov on p.product_id = pov.product_id";
			}

			if (!empty($data['filtervf']) ) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf on p.product_id = pf.product_id";
			}

			$sql .= " WHERE p.status = '1' AND p2s.store_id = '" . (int)$data['store_id'] . "'";

			if (!empty($data['categories'])) {
				if (!empty($data['sub_category'])) {
					$sql .= " AND cp.path_id IN ('" . implode("','", $data['categories']) . "')";
				} else {
					$sql .= " AND p2c.category_id IN ('" . implode("','", $data['categories']) . "')";
				}
			}

			if (!empty($data['manufacturers'])) {
				$sql .= " AND p.manufacturer_id IN ('" . implode("','", $data['manufacturers']) . "')";
			}

			if (!empty($data['suppler'])) {
				$sql .= " AND p.suppler_code = '" . $this->db->escape($data['suppler']) . "'";
			}

			if (!empty($data['filtera']) ) {
				$attributes = [];
				$attributes_values = [];
				$attr = [];
				foreach ($data['filtera'] as $key => $item) {
					if ($item != '' && $data['filterva'][$key] != '') {
						$attr[] = "(pa.attribute_id = '" . $item . "' AND pa.text = '" . $data['filterva'][$key] . "')";
					}
				}
				$count_attr = count($attr);
				if ($count_attr) {
					$attr_sql = implode(' OR ', $attr);
					$sql .= 'AND (' . $attr_sql . ')';
				}
			}

			if (!empty($data['filter']) ) {
				$options = [];
				$options_values = [];
				foreach ($data['filter'] as $key => $item) {
					if ($item != '') {
						if ($data['filterv'][$key] != '') {
							$options[] = $item;
							$options_values[] = $data['filterv'][$key];
						}
					}
				}
				
				if ($options && $options_values) {
					$options = implode(',', $options);
					$options_values = implode(',', $options_values);
					$sql .= " AND pov.option_id IN ($options) AND pov.option_value_id IN ($options_values)";
				}
			}

			if (!empty($data['filtervf']) ) {
				$filters_values = [];
				foreach ($data['filtervf'] as $key => $item) {
					if ($item != '') {
						$filters_values[] = $item;
					}
				}
				
				if ($filters_values) {
					$filters_values = implode(',', $filters_values);
					$sql .= " AND pf.filter_id IN ($filters_values)";
				}
			}

		} else {
			$sql .= " FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p2s.store_id = '" . (int)$data['store_id'] . "'";
		}

		if (!empty($data['minimum'])) {
			$sql .= " AND p.quantity >= " . (float)$data['minimum'];
		}

		if (!empty($data['price_from'])) {
			$sql .= " AND p.price >= " . (float)$data['price_from'];
		}

		if (!empty($data['price_to'])) {
			$sql .= " AND p.price <= " . (float)$data['price_to'];
		}

		if (empty($data['noimage'])) {
			$sql .= " AND p.image <> ''";
		}

		if (!empty($data['cron_action'])) {
			$now = time();
			switch ($data['frequency']) {
				case '1':
					$date = date('Y-m-d H:i:s', strtotime('-1 day', $now));
					break;

				case '2':
					$date = date('Y-m-d H:i:s', strtotime('-1 week', $now));
					break;

				case '3':
					$date = date('Y-m-d H:i:s', strtotime('-1 month', $now));
					break;
				
				default:
					$date = date('Y-m-d H:i:s', strtotime('-1 hour', $now));
					break;
			}

			if (in_array('add_product', $data['cron_action']) || in_array('edit_product', $data['cron_action'])) {
				$sql .= " AND (";
				if (in_array('add_product', $data['cron_action'])) {
					$sql .= "(p.date_added <= NOW() AND p.date_added >= '".$date."')";
				}
				if (in_array('add_product', $data['cron_action']) AND in_array('edit_product', $data['cron_action'])) {
					$sql .= " || ";
				}
				if (in_array('edit_product', $data['cron_action'])) {
					$sql .= "(p.date_modified <= NOW() AND p.date_modified >= '".$date."')";
				}
				$sql .= ")";
			}
		}

		$sql .= " GROUP BY p.product_id";

		if (!empty($count_attr)) {
			$sql .= " HAVING COUNT(DISTINCT pa.attribute_id) = " . $count_attr;
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if (!isset($data['start']) || $data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		if ($query->num_rows) {
			foreach ($query->rows as $row) {
				$products[] = $row['product_id'];
			}
		}

		return $products;
	}

	public function getBlacklistProducts($feed_id) {
		$products = [];

		if (empty($this->setting)) {
			$this->getFeed($feed_id);
		}

		if (!empty($this->setting['categorybl']) || !empty($this->setting['manufacturerbl'])) {
			$data = [
				'categories' => $this->setting['categorybl'],
				'sub_category' => $this->setting['sub_categorybl'],
				'manufacturers' => $this->setting['manufacturerbl']
			];
			$sql = "SELECT p.product_id";

			if (!empty($data['categories']) ) {
				if (!empty($data['sub_category'])) {
					$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
				} else {
					$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
				}
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product p";
			}

			$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

			$sql .= " WHERE p.status = '1' AND p2s.store_id = '" . (int)$this->setting['store_id'] . "'";

			if (!empty($data['categories'])) {
				if (!empty($data['sub_category'])) {
					$sql .= " AND cp.path_id IN ('" . implode("','", $data['categories']) . "')";
				} else {
					$sql .= " AND p2c.category_id IN ('" . implode("','", $data['categories']) . "')";
				}
			}

			if (!empty($data['manufacturers'])) {
				$sql .= " AND p.manufacturer_id IN ('" . implode("','", $data['manufacturers']) . "')";
			}

			$sql .= " GROUP BY p.product_id";

			$query = $this->db->query($sql);

			if ($query->num_rows) {
				foreach ($query->rows as $row) {
					$products[] = $row['product_id'];
				}
			}
		}

		if (!empty($this->setting['productbl'])) {
			$products = array_merge($products, $this->setting['productbl']);
		}

		return $products;
	}

	public function getCategories() {
		$categories = [];
		$query = $this->db->query("SELECT google_category_id, category_id FROM `" . DB_PREFIX . "contentapi_category_to_category`");
		if ($query->num_rows) {
			foreach ($query->rows as $row) {
				$categories[$row['category_id']] = $row['google_category_id'];
			}
		}

		return $categories;
	}

	public function getProductFields() {
		$fields = [];
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product`");
		if ($query->num_rows) {
			foreach ($query->rows as $row) {
				$fields[] = $row['Field'];
			}
		}
		return $fields;
	}

	public function getProductSpecialDate($product_id) {
		$result = [];
		$query = $this->db->query("SELECT date_start, date_end FROM " . DB_PREFIX . "product_special WHERE product_id = '".(int)$product_id."' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");
		if ($query->num_rows) {
			$result = $query->row;
		}
		return $result;
	}
}