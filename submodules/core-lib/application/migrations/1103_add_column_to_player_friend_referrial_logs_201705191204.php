<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_player_friend_referrial_logs_201705191204 extends CI_Migration {

	private $tableName = 'player_friend_referrial_logs';

	public function up() {
		$fields = array(
			'note' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'updated_by' => array(
				'type' => 'INT',
				'null' => false,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}
	public function down() {
		$this->dbforge->drop_column($this->tableName, 'note');
		$this->dbforge->drop_column($this->tableName, 'updated_by');
	}
}