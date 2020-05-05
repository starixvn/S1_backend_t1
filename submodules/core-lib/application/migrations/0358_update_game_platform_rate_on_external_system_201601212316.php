<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_game_platform_rate_on_external_system_201601212316 extends CI_Migration {

	private $tableName = 'external_system';

	public function up() {

		$this->db->where('game_platform_rate', 0)->update($this->tableName, array('game_platform_rate' => 100));
		$this->db->where('game_platform_rate', 0)->update('external_system_list', array('game_platform_rate' => 100));

	}

	public function down() {

	}
}