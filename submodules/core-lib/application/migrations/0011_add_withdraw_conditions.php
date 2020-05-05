<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_withdraw_conditions extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'source_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'source_type' => array(
				'type' => 'INT',
				'null' => false,
			),
			'started_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'condition_amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'promotion_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('withdraw_conditions');
	}

	public function down() {
		$this->dbforge->drop_table('withdraw_conditions');
	}
}
