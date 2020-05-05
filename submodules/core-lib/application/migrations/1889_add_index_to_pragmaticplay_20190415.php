<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_pragmaticplay_20190415 extends CI_Migration {

	public function up() {

		$this->load->model(['player_model']);
		$this->player_model->addIndex('pragmaticplay_idr1_game_logs','idx_end_date' , 'end_date');
		$this->player_model->addIndex('pragmaticplay_idr2_game_logs','idx_end_date' , 'end_date');
		$this->player_model->addIndex('pragmaticplay_idr3_game_logs','idx_end_date' , 'end_date');
		$this->player_model->addIndex('pragmaticplay_idr4_game_logs','idx_end_date' , 'end_date');
		$this->player_model->addIndex('pragmaticplay_idr5_game_logs','idx_end_date' , 'end_date');
		$this->player_model->addIndex('pragmaticplay_myr1_game_logs','idx_end_date' , 'end_date');
		$this->player_model->addIndex('pragmaticplay_myr2_game_logs','idx_end_date' , 'end_date');
		$this->player_model->addIndex('pragmaticplay_thb1_game_logs','idx_end_date' , 'end_date');
		$this->player_model->addIndex('pragmaticplay_thb2_game_logs','idx_end_date' , 'end_date');
		$this->player_model->addIndex('pragmaticplay_vnd1_game_logs','idx_end_date' , 'end_date');
		$this->player_model->addIndex('pragmaticplay_vnd2_game_logs','idx_end_date' , 'end_date');
		$this->player_model->addIndex('pragmaticplay_cny1_game_logs','idx_end_date' , 'end_date');
		$this->player_model->addIndex('pragmaticplay_cny2_game_logs','idx_end_date' , 'end_date');

	}

	public function down() {
	}
}

///END OF FILE//////////