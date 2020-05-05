<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_whitelabel_gamelogs_201706151947 extends CI_Migration {

	private $tableName = 'whitelabel_game_logs';

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
			'UserName' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'betOption' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'marketType' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'hdp' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'odds' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'league' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'match' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'winlostDate' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'liveScore' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'htScore' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'ftScore' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'customeizedBetType' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'refNo' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'sportType' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'orderTime' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'oddsStyle' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'stake' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'actualStake' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'winlose' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'turnover' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'isLive' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'Ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'accountId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'tableName' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'ProductType' => array(
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
