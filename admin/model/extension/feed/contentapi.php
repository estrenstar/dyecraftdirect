<?php
class ModelExtensionFeedContentapi extends Model {

	private $fields = ['status','action','debug','cron_action','frequency','language','currency','label','merchantid','offerid','gtin','include_tax','shipping','product_type','prefix','monolog','tracking','price_from','price_to','product_name','product_description','striptags','name_prefix','name_suffix','mpn','minimum','adult','stock','stock_assign','condition','condition_field','condition_assign','select','product','category','manufacturer','sub_category','productbl','categorybl','manufacturerbl','sub_categorybl','limit','filter','filterv','filtera','filterva','filterf','filtervf','custom_label','destination','excluded','preorder','channel','store_code','suppler','dimension','dimension_assign','weight','weight_assign','condition_table','store_id','noimage','settings'];

	public function install() {

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "contentapi` (
			`feed_id` INT(11) NOT NULL AUTO_INCREMENT,
			`label` VARCHAR(20) NOT NULL,
			`channel` VARCHAR(6) NOT NULL,
			`store_code` TEXT NOT NULL,
			`merchantid` VARCHAR(64) NOT NULL,
			`store_id` INT(11) NOT NULL DEFAULT '0',
			`language` INT(11) NOT NULL,
			`currency` VARCHAR(3) NOT NULL,
			`custom_label` TEXT NOT NULL,
			`offerid` VARCHAR(10) NOT NULL,
			`prefix` VARCHAR(10) NOT NULL,
			`gtin` VARCHAR(10) NOT NULL,
			`mpn` VARCHAR(10) NOT NULL,
			`tracking` VARCHAR(255) NOT NULL,
			`product_name` VARCHAR(32) NOT NULL,
			`name_prefix` VARCHAR(128) NOT NULL,
			`name_suffix` VARCHAR(128) NOT NULL,
			`product_description` VARCHAR(32) NOT NULL,
			`striptags` TINYINT(1) NOT NULL DEFAULT '0',
			`stock` INT(1) NOT NULL DEFAULT '0',
			`stock_assign` TEXT NOT NULL,
			`preorder` INT(3) NOT NULL,
			`condition` INT(1) NOT NULL DEFAULT '0',
			`condition_field` VARCHAR(128) NOT NULL,
			`condition_table` VARCHAR(128) NOT NULL,
			`condition_assign` TEXT NOT NULL,
			`dimension` INT(1) NOT NULL DEFAULT '0',
			`dimension_assign` TEXT NOT NULL,
			`weight` INT(1) NOT NULL DEFAULT '0',
			`weight_assign` TEXT NOT NULL,
			`product_type` TINYINT(1) NOT NULL DEFAULT '0',
			`shipping` TEXT NOT NULL,
			`adult` TINYINT(1) NOT NULL DEFAULT '0',
			`include_tax` TINYINT(1) NOT NULL DEFAULT '0',
			`limit` INT(11) NOT NULL,
			`minimum` INT(11) NOT NULL,
			`noimage` TINYINT(1) NOT NULL DEFAULT '0',
			`price_from` VARCHAR(16) NOT NULL,
			`price_to` VARCHAR(16) NOT NULL,
			`select` VARCHAR(10) NOT NULL,
			`product` TEXT NOT NULL,
			`category` TEXT NOT NULL,
			`manufacturer` TEXT NOT NULL,
			`sub_category` TINYINT(1) NOT NULL DEFAULT '0',
			`filter` TEXT NOT NULL,
			`filterv` TEXT NOT NULL,
			`filtera` TEXT NOT NULL,
			`filterva` TEXT NOT NULL,
			`filterf` TEXT NOT NULL,
			`filtervf` TEXT NOT NULL,
			`suppler` VARCHAR(16) NOT NULL,
			`productbl` TEXT NOT NULL,
			`categorybl` TEXT NOT NULL,
			`manufacturerbl` TEXT NOT NULL,
			`sub_categorybl` TINYINT(1) NOT NULL DEFAULT '0',
			`destination` TEXT NOT NULL,
			`excluded` TEXT NOT NULL,
			`action` VARCHAR(255) NOT NULL,
			`cron_action` VARCHAR(255) NOT NULL,
			`frequency` INT(1) NOT NULL,
			`debug` TINYINT(1) NOT NULL DEFAULT '0',
			`monolog` TINYINT(1) NOT NULL DEFAULT '0',
			`settings` TEXT NOT NULL,
			`status` TINYINT(1) NOT NULL DEFAULT '1',
			PRIMARY KEY (`feed_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "contentapi_category` (
			`google_category_id` INT(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(255) NOT NULL,
			PRIMARY KEY (`google_category_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "contentapi_category_to_category` (
			`google_category_id` INT(11) NOT NULL,
			`category_id` INT(11) NOT NULL,
			PRIMARY KEY (`google_category_id`, `category_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "contentapi`;");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "contentapi_category`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "contentapi_category_to_category`");
	}

	public function updateDB() {
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "contentapi` LIKE 'channel'");
		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "contentapi` ADD COLUMN channel VARCHAR(6) NOT NULL");
		}
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "contentapi` LIKE 'store_code'");
		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "contentapi` ADD COLUMN store_code TEXT NOT NULL");
		}
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "contentapi` LIKE 'suppler'");
		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "contentapi` ADD COLUMN suppler VARCHAR(255) NOT NULL");
		}
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "contentapi` LIKE 'dimension'");
		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "contentapi` ADD COLUMN dimension INT(1) NOT NULL DEFAULT '0'");
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "contentapi` ADD COLUMN dimension_assign TEXT NOT NULL");
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "contentapi` ADD COLUMN weight INT(1) NOT NULL DEFAULT '0'");
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "contentapi` ADD COLUMN weight_assign TEXT NOT NULL");
		}
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "contentapi` LIKE 'condition_table'");
		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "contentapi` ADD COLUMN condition_table VARCHAR(128) NOT NULL");
		}
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "contentapi` LIKE 'store_id'");
		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "contentapi` ADD COLUMN store_id INT(11) NOT NULL DEFAULT '0'");
		}
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "contentapi` LIKE 'noimage'");
		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "contentapi` ADD COLUMN noimage TINYINT(1) NOT NULL DEFAULT '0'");
		}
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "contentapi` LIKE 'filterf'");
		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "contentapi` ADD COLUMN filterf TEXT NOT NULL");
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "contentapi` ADD COLUMN filtervf TEXT NOT NULL");
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "contentapi` ADD COLUMN excluded TEXT NOT NULL");
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "contentapi` ADD COLUMN settings TEXT NOT NULL");
		}
	}

	public function addFeed($data) {
		$feed_id = 0;
		$values = '';
		foreach ($this->fields as $field) {
			if (!isset($data[$field])) {
				$values .= $field . " = '',";
			} else {
				if (is_array($data[$field])) {
					$values .= "`" . $field . "` = '" . json_encode($data[$field],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_APOS) . "',";
				} else {
					$values .=  "`" . $field . "` = '" . $this->db->escape($data[$field]) . "',";
				}
			}
		}
		
		if (!empty($values)) {
			$values = trim($values, ',');
			$this->db->query("INSERT INTO " . DB_PREFIX . "contentapi SET " . $values);
			$feed_id = $this->db->getLastId();
		}

		return $feed_id;
	}

	public function editFeed($feed_id, $data) {
		$values = '';
		foreach ($this->fields as $field) {
			if (!isset($data[$field])) {
				$values .= $field . " = '',";
			} else {
				if (is_array($data[$field])) {
					$values .= "`" . $field . "` = '" . json_encode($data[$field],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_APOS) . "',";
				} else {
					$values .=  "`" . $field . "` = '" . $this->db->escape($data[$field]) . "',";
				}
			}
		}
		
		if (!empty($values)) {
			$values = trim($values, ',');
			$this->db->query("UPDATE " . DB_PREFIX . "contentapi SET " . $values . " WHERE feed_id = '" . (int)$feed_id . "'");
		}
	}

	public function deleteFeed($feed_id) {

		$this->db->query("DELETE FROM " . DB_PREFIX . "contentapi WHERE feed_id = '" . (int)$feed_id . "'");

	}

	public function getFeed($feed_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "contentapi WHERE feed_id = '" . (int)$feed_id . "'");

		return $query->row;
	}

	public function getFeeds($data = []) {
		$sql = "SELECT * FROM " . DB_PREFIX . "contentapi";

		if (isset($data['status'])) {
			$sql .= " WHERE status = '" . (int)$data['status'] . "'";
		}

		$sql .= " GROUP BY feed_id ORDER BY feed_id";

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

	public function getTotalFeeds() {
		$sql = "SELECT COUNT(DISTINCT feed_id) AS total FROM " . DB_PREFIX . "contentapi";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function copyFeed($feed_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "contentapi WHERE feed_id = '" . (int)$feed_id . "'");

		if ($query->num_rows) {
			$data = $query->row;
			unset($data['feed_id']);
			$data['label'] = $query->row['label'] . '-NEW';
			$data['status'] = '0';

			$new_feed_id = $this->addFeed($data);
			return $new_feed_id;
		}
	}

	public function import($string) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "contentapi_category");

		$lines = explode("\n", $string);

		foreach ($lines as $line) {
			if (substr($line, 0, 1) != '#') {
				$part = explode(' - ', $line, 2);

				if (isset($part[1])) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "contentapi_category SET google_category_id = '" . (int)$part[0] . "', name = '" . $this->db->escape($part[1]) . "'");
				}
			}
		}
	}

	public function getGoogleBaseCategories($data = []) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "contentapi_category` WHERE name LIKE '%" . $this->db->escape($data['filter_name']) . "%' ORDER BY name ASC";

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

	public function addCategory($data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "contentapi_category_to_category WHERE category_id = '" . (int)$data['category_id'] . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "contentapi_category_to_category SET google_category_id = '" . (int)$data['google_category_id'] . "', category_id = '" . (int)$data['category_id'] . "'");
	}

	public function deleteCategory($category_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "contentapi_category_to_category WHERE category_id = '" . (int)$category_id . "'");
	}

	public function getCategories($data = []) {
		$sql = "SELECT google_category_id, (SELECT name FROM `" . DB_PREFIX . "contentapi_category` gbc WHERE gbc.google_category_id = gbc2c.google_category_id) AS google_category, category_id, (SELECT name FROM `" . DB_PREFIX . "category_description` cd WHERE cd.category_id = gbc2c.category_id AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS category FROM `" . DB_PREFIX . "contentapi_category_to_category` gbc2c ORDER BY google_category ASC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 10;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalCategories() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "contentapi_category_to_category`");

		return $query->row['total'];
	}

	public function getSupplers() {
		$check_table = $this->db->query("SELECT * FROM information_schema.tables WHERE table_schema = '" . DB_DATABASE . "' AND table_name = '" . DB_PREFIX . "suppler' LIMIT 1;");
		if ($check_table->num_rows) {
			$query = $this->db->query("SELECT suppler_id, name FROM " . DB_PREFIX . "suppler");
			return $query->rows;
		}
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

}