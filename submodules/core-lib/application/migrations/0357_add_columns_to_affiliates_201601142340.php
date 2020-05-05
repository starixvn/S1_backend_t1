<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_affiliates_201601142340 extends CI_Migration {

	private $tableName = 'affiliates';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'temp_status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
		));

		$this->db->query("update affiliates set temp_status=case status when '0' then 0 when '1' then 1 when '2' then 2 end");

		$this->dbforge->drop_column($this->tableName, 'status');

		$this->dbforge->add_column($this->tableName, array(
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
		));

		$this->db->query('update affiliates set status=temp_status');

		$this->dbforge->drop_column($this->tableName, 'temp_status');
	}

	public function down() {
		// $this->dbforge->drop_column($this->tableName, array('enabled_withdrawal', 'enabled_deposit'));
	}
}