<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_gsag_game_logs_20160413 extends CI_Migration {

	private $tableName = 'gsag_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => FALSE,
				'auto_increment' => TRUE,
			),
			'OpCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'MemberId' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'dataType' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'billNo' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'playerName' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'agentCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'gameCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'netAmount' => array(
				'type' => 'DOUBLE',
                'null' => TRUE,
			),
			'betTime' => array(
				'type' => 'TIMESTAMP',
                'null' => TRUE,
			),
			'gameType' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'betAmount' => array(
				'type' => 'DOUBLE',
                'null' => TRUE,
			),
			'validBetAmount' => array(
				'type' => 'DOUBLE',
                'null' => TRUE,
			),
			'flag' => array(
				'type' => 'INT',
                'null' => TRUE,
			),
			'playType' => array(
				'type' => 'INT',
                'null' => TRUE,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'tableCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'loginIP' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'recalcuTime' => array(
				'type' => 'TIMESTAMP',
                'null' => TRUE,
			),
			'platformType' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'remark' => array(
				'type' => 'VARCHAR',
				'constraint' => '2000',
                'null' => TRUE,
			),
			'round' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'result' => array(
				'type' => 'VARCHAR',
				'constraint' => '2000',
                'null' => TRUE,
			),
			'beforeCredit' => array(
				'type' => 'DOUBLE',
                'null' => TRUE,
			),
			'deviceType' => array(
				'type' => 'INT',
                'null' => TRUE,
			),
			'begintime' => array(
				'type' => 'TIMESTAMP',
                'null' => TRUE,
			),
			'closetime' => array(
				'type' => 'TIMESTAMP',
                'null' => TRUE,
			),
			'dealer' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'shoecode' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'bankerPoint' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'cardnum' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'pair' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'dragonpoint' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'tigerpoint' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'cardlist' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'vid' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'datecreated' => array(
				'type' => 'TIMESTAMP',
                'null' => TRUE,
			),
			'CasinoTypeName' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->tableName);
		$this->db->query('ALTER TABLE `gsag_game_logs` ADD UNIQUE INDEX (`billNo`)');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}