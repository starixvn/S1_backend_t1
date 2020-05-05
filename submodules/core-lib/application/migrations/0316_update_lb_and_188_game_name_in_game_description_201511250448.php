<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_lb_and_188_game_name_in_game_description_201511250448 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		$data = array(
			array(
				'game_platform_id' => '10',
				'game_name' => 'lb.unknown',
			),
			array(
				'game_platform_id' => '11',
				'game_name' => 'one88.unknown',
			),
		);
		$this->db->update_batch($this->tableName, $data, 'game_platform_id');
	}

	public function down() {
		$game_platform_id = array('10', '11');
		$this->db->where_in('game_platform_id', $game_platform_id);
		$this->db->delete($this->tableName);
	}
}