<?php
class ModelExtensionModuleHbDownload extends Model {
	public function install(){
		$this->db->query("INSERT INTO `".DB_PREFIX."setting` (`code`, `key`, `value`, `serialized`) VALUES ('module_hb_download','module_hb_download_status', '1','0')");

		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'date_modified'");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD `date_modified` DATETIME NOT NULL AFTER `date_added`");
			$this->db->query("UPDATE `" . DB_PREFIX . "download` SET date_modified = now()");
		}

		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'download_count'");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD `download_count` INT(11) NOT NULL DEFAULT '0' AFTER `mask`");
		}

		$this->installOcmod();
	}
	
	public function uninstall(){
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'huntbee_download_manager'");		
		$this->db->query("DELETE FROM `".DB_PREFIX."setting` WHERE `key` = 'module_hb_download_status'");

		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'date_modified'");
		if($query->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` DROP `date_modified`");
		}

		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'download_count'");
		if($query->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` DROP `download_count`");
		}
	}

	public function update(){
		$this->installOcmod();
	}

	public function installOcmod(){
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'huntbee_download_manager'");

		$ocmod_filename = 'ocmod_download_manager_3xxx.txt';
		$ocmod_name = 'Download Manager [3xxx]';		
		
		$ocmod_version = $this->hb_extension_version;
		$ocmod_code    = 'huntbee_download_manager';	
		$ocmod_author  = 'HuntBee OpenCart Services';
		$ocmod_link    = 'https://www.huntbee.com/';
		
		$file = DIR_APPLICATION . 'view/template/extension/module/ocmod/' . $ocmod_filename;
		if (file_exists($file)) {
			$ocmod_xml = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
			$ocmod_xml = str_replace('{version}', $ocmod_version, $ocmod_xml);
			$ocmod_xml = str_replace('{name}', $ocmod_name, $ocmod_xml);
			$ocmod_xml = str_replace('{code}', $ocmod_code, $ocmod_xml);
			$this->db->query("INSERT INTO " . DB_PREFIX . "modification SET code = '" . $this->db->escape($ocmod_code) . "', name = '" . $this->db->escape($ocmod_name) . "', author = '" . $this->db->escape($ocmod_author) . "', version = '" . $this->db->escape($ocmod_version) . "', link = '" . $this->db->escape($ocmod_link) . "', xml = '" . $this->db->escape($ocmod_xml) . "', status = '1', date_added = NOW()");
		}
	}

	public function getDownload($download_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "download d LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE d.download_id = '" . (int)$download_id . "' AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getDownloads($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "download d LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE dd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['search'])) {
			$sql .= " AND (dd.name LIKE '%".$this->db->escape($data['search'])."%')";
		}

		if (isset($data['sort'])) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY dd.name";
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

	public function getTotalDownloads($data) {
		$sql = "SELECT count(*) as total FROM " . DB_PREFIX . "download d LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE dd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['search'])) {
			$sql .= " AND (dd.name LIKE '%".$this->db->escape($data['search'])."%')";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function addDownload($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "download SET filename = '" . $this->db->escape($data['filename']) . "', mask = '" . $this->db->escape($data['mask']) . "', date_added = NOW(), date_modified = NOW()");

		$download_id = $this->db->getLastId();

		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();

		foreach ($languages as $language) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "download_description SET download_id = '" . (int)$download_id . "', language_id = '" . (int)$language['language_id'] . "', name = '" . $this->db->escape($data['description']) . "'");
		}

		return $download_id;
	}

	public function editDownload($download_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "download SET filename = '" . $this->db->escape($data['filename']) . "', mask = '" . $this->db->escape($data['mask']) . "', date_modified = now() WHERE download_id = '" . (int)$download_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "download_description WHERE download_id = '" . (int)$download_id . "'");

		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();

		foreach ($languages as $language) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "download_description SET download_id = '" . (int)$download_id . "', language_id = '" . (int)$language['language_id'] . "', name = '" . $this->db->escape($data['description']) . "'");
		}
	}

	public function deleteDownload($download_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "download WHERE download_id = '" . (int)$download_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "download_description WHERE download_id = '" . (int)$download_id . "'");
	}

	public function getProductsByDownloadId($download_id) {
		$query = $this->db->query("SELECT p.product_id, pd.name, p.model FROM `" . DB_PREFIX . "product` p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_download p2d ON (p.product_id = p2d.product_id) WHERE p2d.download_id = '" . (int)$download_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		if ($query->num_rows) {
			return $query->rows;
		} else {
			return array();
		}
	}

	public function getGhostFiles() {
		$downloadFolder = DIR_DOWNLOAD;
		$filesInFolder = array_diff(scandir($downloadFolder), array('.', '..', 'index.html', '.htaccess', 'index.php'));

		$query = $this->db->query("SELECT filename FROM " . DB_PREFIX . "download");
		$assignedFiles = array_column($query->rows, 'filename');
		$unassignedFiles = array_diff($filesInFolder, $assignedFiles);

		return $unassignedFiles;
	}
}
?>