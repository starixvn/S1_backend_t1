<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_mg_game_name_in_game_description_201510261744 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		$data = array(
			array(
				'game_code' => 'breakaway',
				'english_name' => 'Break Away',
				'external_game_id' => 'Break Away',
			),
			array(
				'game_code' => 'rrjackandjill',
				'english_name' => 'RR Jack and Jill 96',
				'external_game_id' => 'RR Jack and Jill 96',
			),
			array(
				'game_code' => 'immortalromancev90',
				'english_name' => 'Immortal Romance 90',
				'external_game_id' => 'Immortal Romance 90',
			),
		);
		$this->db->update_batch($this->tableName, $data, 'game_code');

	}

	public function down() {
	}
}