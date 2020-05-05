<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_pinnacle_game_logs_201707212040 extends CI_Migration {

	private $tableName = 'pinnacle_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'playerId' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => false,
			),
			'userName' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'wagerId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'eventName' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'parentEventName' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'headToHead' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'wagerDateFm' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'eventDateFm' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'homeTeam' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'awayTeam' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'selection' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'handicap' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'odds' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'oddsFormat' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'betType' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'league' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'stake' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'sport' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'currencyCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'inplayScore' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'inPlay' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'homePitcher' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'awayPitcher' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'homePitcherName' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'awayPitcherName' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'period' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'cancellationStatus' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'parlaySelections' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'category' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'toWin' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'toRisk' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'product' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'parlayMixOdds' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'competitors' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'userCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'winLoss' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'scores' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'result' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
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
