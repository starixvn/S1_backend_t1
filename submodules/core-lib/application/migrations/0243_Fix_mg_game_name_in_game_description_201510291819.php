<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_mg_game_name_in_game_description_201510291819 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		$data = array(
			array(
				'game_code' => 'carnavalv90',
				'english_name' => 'Carnaval 90',
				'external_game_id' => 'Carnaval 90',
			),
			array(
				'game_code' => 'lotsofloot',
				'english_name' => 'Lotsaloot',
				'external_game_id' => 'Lotsaloot',
			),
		);
		$this->db->update_batch($this->tableName, $data, 'game_code');

	}

	public function down() {

	}
}