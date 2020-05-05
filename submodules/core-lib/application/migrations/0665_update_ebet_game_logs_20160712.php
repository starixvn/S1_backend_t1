<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_ebet_game_logs_20160712 extends CI_Migration {

	public function up() {

		$fields = array(
			'realBet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		);
		
		$this->dbforge->add_column('ebet_game_logs', $fields);

		$fields = array(
			'betMap' => array(
				'name' => 'betMap',
				'type' => 'TEXT',
				'null' => true,
			),
			'judgeResult' => array(
				'name' => 'judgeResult',
				'type' => 'TEXT',
				'null' => true,
			),
			'bankerCards' => array(
				'name' => 'bankerCards',
				'type' => 'TEXT',
				'null' => true,
			),
			'playerCards' => array(
				'name' => 'playerCards',
				'type' => 'TEXT',
				'null' => true,
			),
			'allDices' => array(
				'name' => 'allDices',
				'type' => 'TEXT',
				'null' => true,
			),
		);
		$this->dbforge->modify_column('ebet_game_logs', $fields);

	}

	public function down() {
		$this->dbforge->drop_column('ebet_game_logs', 'realBet');
		$fields = array(
			'betMap' => array(
				'name' => 'betMap',
				'type' => 'VARCHAR',
				'constraint' => '2000',
				'null' => true,
			),
			'judgeResult' => array(
				'name' => 'judgeResult',
				'type' => 'VARCHAR',
				'constraint' => '2000',
				'null' => true,
			),
			'bankerCards' => array(
				'name' => 'bankerCards',
				'type' => 'VARCHAR',
				'constraint' => '2000',
				'null' => true,
			),
			'playerCards' => array(
				'name' => 'playerCards',
				'type' => 'VARCHAR',
				'constraint' => '2000',
				'null' => true,
			),
			'allDices' => array(
				'name' => 'allDices',
				'type' => 'VARCHAR',
				'constraint' => '2000',
				'null' => true,
			),
		);
		$this->dbforge->modify_column('ebet_game_logs', $fields);
	}



}