<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_time_index_on_isb_idr_cny_thb_vnd_tables_20190916 extends CI_Migration {

	public function up() {

		$this->load->model(['player_model']);

		$tables = [

			'isb_cny1_game_logs',
			'isb_cny2_game_logs',
			'isb_cny3_game_logs',
			'isb_cny4_game_logs',

			'isb_idr1_game_logs',
			'isb_idr2_game_logs',
			'isb_idr3_game_logs',
			'isb_idr4_game_logs',

			'isb_idr5_game_logs',
			'isb_idr6_game_logs',
			'isb_idr7_game_logs',

			'isb_myr1_game_logs',
			'isb_myr2_game_logs',
			'isb_myr3_game_logs',
			'isb_myr4_game_logs',

			'isb_thb1_game_logs',
			'isb_thb2_game_logs',
			'isb_thb3_game_logs',
			'isb_thb4_game_logs',

			'isb_vnd1_game_logs',
			'isb_vnd2_game_logs',
			'isb_vnd3_game_logs',
			'isb_vnd4_game_logs',
			'isb_vnd5_game_logs',

		];

		foreach ($tables as $table) {
			if($this->db->table_exists($table)){
				$this->player_model->addIndex($table,'idx_time' , 'time');
			}
		}	
	}


	public function down() {
		//
	}
}
