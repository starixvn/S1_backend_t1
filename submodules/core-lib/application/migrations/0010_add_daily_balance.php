<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_daily_balance extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => false,
			),
			'balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'type' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => false,
			),
			'sub_wallet_id	' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => true,
			),
			'updated_at' => array(
				'type' => 'DATE',
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('daily_balance');
	}

	public function down() {
		$this->dbforge->drop_table('daily_balance');
	}
}
