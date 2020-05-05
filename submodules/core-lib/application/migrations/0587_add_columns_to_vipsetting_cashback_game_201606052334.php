<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_vipsetting_cashback_game_201606052334 extends CI_Migration {

	public function up() {

		$data = array(
			'game_platform_percentage' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'game_type_percentage' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'game_desc_percentage' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
		);

		$this->dbforge->add_column('vipsetting_cashback_game', $data);
	}

	public function down() {
		$this->dbforge->drop_column('vipsetting_cashback_game', 'game_platform_percentage');
		$this->dbforge->drop_column('vipsetting_cashback_game', 'game_type_percentage');
		$this->dbforge->drop_column('vipsetting_cashback_game', 'game_desc_percentage');
	}
}
