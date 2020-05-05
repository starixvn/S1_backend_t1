<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_yoplay_game_logs_201708101620 extends CI_Migration {

	private $tableName = 'yoplay_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'playerid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'billno' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'productid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'billtime' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'reckontime' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'slottype' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'gametype' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'betIP' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'betIP' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'account' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'cus_account' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'valid_account' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'account_base' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'account_bonus' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'cus_account_base' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'cus_account_bonus' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'flag' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'platformtype' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			)
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
