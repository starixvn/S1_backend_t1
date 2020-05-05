<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_player_201610191942 extends CI_Migration {

	public function up() {
		$fields = array(
			'withdraw_password' => array(
				'type' => 'VARCHAR',
				'constraint'=>'200',
				'null' => false,
			),
			'withdraw_password_md5' => array(
				'type' => 'VARCHAR',
				'constraint'=>'200',
				'null' => false,
			),
        );

		$this->dbforge->add_column('player', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('player', 'withdraw_password');
		$this->dbforge->drop_column('player', 'withdraw_password_md5');
	}
}
