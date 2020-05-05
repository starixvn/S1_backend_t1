<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_column_code_data_new_player_tutorial_201705131848 extends CI_Migration {

	public function up() {
		$datas = [
			"4" =>array(
				"id" => "4",
				'code' => "memberCenter",
			),
		]; 

		$this->db->update_batch('new_player_tutorial', $datas, 'id');
	}

	public function down() {
		$datas = [
			"4" =>array(
				"id" => "4",
				'code' => "fundManagement",
			),
		]; 

		$this->db->update_batch('new_player_tutorial', $datas, 'id');
	}
}