<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_random_bonus_history_201602040742 extends CI_Migration {

	private $tableName = 'random_bonus_history';
	const NORMAL = 1;
	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'deposit_transaction_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'bonus_transaction_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'deposit_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bonus_amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => self::NORMAL,
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