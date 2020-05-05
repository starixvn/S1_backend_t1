<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_ebetbbtech_game_logs_20180110 extends CI_Migration {

	public function up() {

		$this->load->model(['player_model']);
		$this->player_model->addIndex('ebetbbtech_game_logs','idx_response_result_id' , 'response_result_id');
		$this->player_model->addIndex('ebetbbtech_game_logs','idx_external_uniqueid' , 'external_uniqueid');
		$this->player_model->addIndex('ebetbbtech_game_logs','idx_round_id' , 'roundId');
	}

	public function down() {
		
		$this->load->model(['player_model']);
		$this->player_model->dropIndex('ebetbbtech_game_logs','idx_response_result_id');
		$this->player_model->dropIndex('ebetbbtech_game_logs','idx_external_uniqueid');
		$this->player_model->dropIndex('ebetbbtech_game_logs','idx_round_id');
	}
}

///END OF FILE//////////