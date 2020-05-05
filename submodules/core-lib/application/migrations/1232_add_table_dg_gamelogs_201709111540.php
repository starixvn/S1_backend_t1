<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_dg_gamelogs_201709111540 extends CI_Migration {

	private $tableName = 'dg_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'dg_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'tableId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'shoeId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'playId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'lobbyId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gameType' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gameId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'memberId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'parentId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'betTime' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'calTime' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'winOrLoss' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'balanceBefore' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'betPoints' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'betPointsz' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'availableBet' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'userName' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'result' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'betDetail' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'ext' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'isRevocation' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'currencyId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'deviceType' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'roadid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'pluginid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
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
