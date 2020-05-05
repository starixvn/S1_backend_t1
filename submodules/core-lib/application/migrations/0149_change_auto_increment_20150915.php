<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Change_auto_increment_20150915 extends CI_Migration {

	private $tables = [
		'sale_orders',
		'transactions',
		'player',
		'adminusers',
		'affiliates',
		'promorules',
		'promocmssetting',
		'vipsetting',
		'walletaccount',
		'playeraccount',
	];

	public function up() {
		foreach ($this->tables as $table) {
			$this->db->query("ALTER TABLE {$table} AUTO_INCREMENT 16805");
		}
	}

	public function down() {
		// foreach ($this->tables as $table) {
		// 	$fields = $this->db->field_data($table);
		// 	foreach ($fields as $field) {
		// 	   if ($field->primary_key) {
		// 	   		$value = ($this->db->select_max($field->name, 'id')->get($table)->row_array()['id']) + 1;
		// 			$this->db->query("ALTER TABLE {$table} AUTO_INCREMENT {$value}");
		// 	   }
		// 	}
		// }
	}

}