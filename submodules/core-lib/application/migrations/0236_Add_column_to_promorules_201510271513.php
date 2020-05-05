<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_promorules_201510271513 extends CI_Migration {

	private $tableName = 'promorules';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'nonfixedDepositMinAmount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'nonfixedDepositMaxAmount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		));

		$data = array(
			array(
				'game_code' => 'rubyprimepropertyv90',
				'english_name' => 'Prime Property 90',
				'external_game_id' => 'Prime Property 90',
			),
		);
		$this->db->update_batch('game_description', $data, 'game_code');

		//Spring Break 90
		$this->db->insert('game_description', array(
			'game_code' => 'springbreakv90', 'english_name' => "Spring Break 90", 'external_game_id' => "Spring Break 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.springbreakv90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'nonfixedDepositMinAmount');
		$this->dbforge->drop_column($this->tableName, 'nonfixedDepositMaxAmount');
	}
}