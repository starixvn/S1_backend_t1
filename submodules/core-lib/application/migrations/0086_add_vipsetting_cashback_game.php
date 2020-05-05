<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_vipsetting_cashback_game extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'vipsetting_cashbackrule_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_description_id' => array(
				'type' => 'INT',
				'null' => false,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('vipsetting_cashback_game');
	}

	public function down() {
		$this->dbforge->drop_table('vipsetting_cashback_game');
	}
}
