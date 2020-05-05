<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_transfer_request_201605221705 extends CI_Migration {

	private $tableName = 'transfer_request';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'from_wallet_type_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'to_wallet_type_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'user_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 3,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}

////END OF FILE////