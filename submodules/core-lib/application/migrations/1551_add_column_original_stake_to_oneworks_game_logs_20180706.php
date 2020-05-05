<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_original_stake_to_oneworks_game_logs_20180706 extends CI_Migration {
	private $tableName = 'oneworks_game_logs';

	public function up() {
		$fields = array(
			'original_stake' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'original_stake');
	}
}