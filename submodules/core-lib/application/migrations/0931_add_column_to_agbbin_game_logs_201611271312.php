<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agbbin_game_logs_201611271312 extends CI_Migration {

	private $tableName = 'agbbin_game_logs';

	public function up() {
		$fields = array(
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);

		$fields = array(
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		);

		$this->dbforge->add_column('agin_game_logs', $fields);

		$fields = array(
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'creationtime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'result' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'beforecredit' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		);

		$this->dbforge->add_column('agshaba_game_logs', $fields);
	}

	public function down() {

		$this->dbforge->drop_column($this->tableName, 'updated_at');
		$this->dbforge->drop_column('agin_game_logs', 'updated_at');
		$this->dbforge->drop_column('agshaba_game_logs', 'updated_at');
		$this->dbforge->drop_column('agshaba_game_logs', 'creationtime');
		$this->dbforge->drop_column('agshaba_game_logs', 'result');
		$this->dbforge->drop_column('agshaba_game_logs', 'beforecredit');

	}
}
