<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_operator_settings_20160527 extends CI_Migration {

	private $tableName = 'operator_settings';

	public function up() {
		// $this->db->insert($this->tableName, array('name' => 'reg.siteName', 'value' => '_json:' . json_encode(array(
		// 	'1' => 'Paramount Casino',
		// 	'2' => '佳宝娱乐',
		// ))));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('name' => 'reg.siteName'));
	}
}