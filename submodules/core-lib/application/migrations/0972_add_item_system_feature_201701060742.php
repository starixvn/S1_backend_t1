<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_item_system_feature_201701060742 extends CI_Migration {

	private $tableName = 'system_features';
	const ENABLED = 1;
	public function up() {
		$this->db->insert($this->tableName, array(
			"name" => "notify_affiliate_withdraw",
			"enabled" => self::ENABLED,
		));
	}

	public function down() {
		$this->db->delete($this->tableName, array('name' => "notify_affiliate_withdraw"));
	}
}
