<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_mg_game_name_in_game_description_201510261122 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		$data = array(
			array(
				'game_code' => 'MermaidsMillionsV90',
				'english_name' => 'Mermaids Millions 90',
			),
		);

		$this->db->update_batch($this->tableName, $data, 'game_code');

		$this->db->query('update game_description set external_game_id=english_name where game_platform_id=?', array(MG_API));

	}

	public function down() {
	}
}