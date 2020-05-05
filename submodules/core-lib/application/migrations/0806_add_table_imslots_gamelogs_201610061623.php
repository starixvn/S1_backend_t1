<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_imslots_gamelogs_201610061623 extends CI_Migration {

	private $tableName = 'imslots_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'PlayerId' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => false,
			),

			'Username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'StartTime' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'EndTime' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'MemberCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => false,
			),
			'MerchantName' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => false,
			),
			'ProviderName' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'GameCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true
			),
			'RoundId' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true
			),
			'CurrencyCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true
			),
			'BetAmount' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'WinAmount'=>array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'GambleAmount'=>array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'GambleWinAmount'=>array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'ProgressiveShare'=>array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'ProgressiveWin'=>array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'GameName'=>array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'IMGameCode'=>array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
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