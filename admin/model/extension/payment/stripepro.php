<?php
class ModelExtensionPaymentStripePro extends Model {

	public function install() {

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "stripepro_card` (
			`id` varchar(32) PRIMARY KEY,
			`customer_id` INT(11) NOT NULL,
			`payment_method_id` varchar(255) NOT NULL COMMENT 'Source or Payment Method Id',
			`last4` VARCHAR(4) NOT NULL,
			`exp_year` VARCHAR(5) NOT NULL,
			`exp_month` VARCHAR(5) NOT NULL,
			`brand` VARCHAR(50) NOT NULL,
			`fingerprint` VARCHAR(50) NOT NULL COMMENT 'Uniquely identifies this particular card number',
			`environment` varchar(5) NOT NULL DEFAULT 'test'
			)");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "stripepro_customer` (
			`id` varchar(32) PRIMARY KEY,
			`customer_id` INT(11) NOT NULL,
			`stripe_customer_id` varchar(255) NOT NULL,
			`environment` varchar(5) NOT NULL DEFAULT 'test'
			)");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "stripepro_order` (
			`id` varchar(32) PRIMARY KEY,
			`order_id` INT(11) NOT NULL,
			`stripe_charge_id` varchar(255) NOT NULL,
			`environment` varchar(5) NOT NULL DEFAULT 'test'
			)");
	}

	public function uninstall() {
		// $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "stripepro_order`");
		// $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "stripepro_card`");
		// $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "stripepro_customer`");
	}


	public function getOrder($order_id){
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "stripepro_order` WHERE `order_id` = '" . $order_id . "' LIMIT 1");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return false;
		}
	}
}
