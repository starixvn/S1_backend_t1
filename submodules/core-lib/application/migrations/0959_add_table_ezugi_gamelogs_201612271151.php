<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ezugi_gamelogs_201612271151 extends CI_Migration {

	private $tableName = 'ezugi_game_logs';

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
			'BetTypeID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'ezugiID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'ezugiID4' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'RoundID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'ServerID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'TableID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'UID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'UID2' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'OperatorID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'OperatorID2' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'SessionCurrency' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'SkinID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'BetSequenceID' => array(
				'type' => 'VARCHAR',
				
				'constraint' => '11',
				'null' => true,
			),
			'Bet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'Win' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'Bankroll' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'GameString' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'GameString2' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'SeatID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'BetStatusID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'BrandID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'RoundDateTime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'ActionID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'BetType' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'PlatformID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'DateInserted' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'GameTypeID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'BFTransactionFound' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'GameTypeName' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'DealerID' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'ErrorCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'originalErrorCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'TransactionID' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
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
			)
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
		$this->load->model(['player_model']);
		$this->player_model->addIndex($this->tableName, 'idx_UID', 'UID');
		$this->player_model->addIndex($this->tableName, 'idx_RoundDateTime', 'RoundDateTime');
		$this->player_model->addIndex($this->tableName, 'idx_GameTypeName', 'GameTypeName');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
