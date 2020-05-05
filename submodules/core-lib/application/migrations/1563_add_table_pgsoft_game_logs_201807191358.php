<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_pgsoft_game_logs_201807191358 extends CI_Migration {

	private $tableName = 'pgsoft_game_logs';

	public function up() {

		if($this->db->table_exists('pgsoft_game_logs')){
			return;
		}

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'betid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'parentbetid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'playername' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'platform' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'bettype' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'transactiontype' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'betamount' => array(
				'type' => 'double',
				'null' => true,
			),
			'winamount' => array(
				'type' => 'double',
				'null' => true,
			),
			'jackpotrtpcontributionamount' => array(
				'type' => 'double',
				'null' => true,
			),
			'jackpotwinamount' => array(
				'type' => 'double',
				'null' => true,
			),
			'balancebefore' => array(
				'type' => 'double',
				'null' => true,
			),
			'balanceafter' => array(
				'type' => 'double',
				'null' => true,
			),
			'rowversion' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'bettime' => array(
                'type' => 'DATETIME',
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
			),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            )
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('external_uniqueid');
		$this->dbforge->add_key('gameid');
		$this->dbforge->add_key('playername');
		$this->dbforge->add_key('bettime');
		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
