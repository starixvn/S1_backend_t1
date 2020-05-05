<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_column_on_player_ip_last_request_201608062227 extends CI_Migration {

	public function up() {
		$fields = array(
			'ip' => array(
				'type' => 'VARCHAR',
				'constraint'=> 50,
				'null' => true,
			),
		);
		$this->dbforge->modify_column('player_ip_last_request', $fields);

		$fields = array(
			'ip' => array(
				'type' => 'VARCHAR',
				'constraint'=> 50,
				'null' => true,
			),
		);
		$this->dbforge->modify_column('http_request', $fields);
	}

	public function down() {
		// $this->dbforge->drop_column('player_ip_last_request', 'after_balance');
	}
}