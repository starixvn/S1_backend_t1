<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_game_logs_unsettle_201807301754 extends CI_Migration {

	public function up() {

		$this->load->model(['player_model']);
		if(!$this->db->field_exists('bet_at', 'game_logs_unsettle')){
			$addFields=[
				'bet_at' => [
	                'type' => 'DATETIME',
	                'null' => true
	            ],
			];
	        $this->dbforge->add_column('game_logs_unsettle', $addFields);
	        $this->player_model->addIndex('game_logs_unsettle', 'idx_bet_at', 'bet_at');
		}
		if(!$this->db->field_exists('md5_sum', 'game_logs_unsettle')){
			$addFields=[
				'md5_sum' => [
					'type' => 'VARCHAR',
					'constraint' => '32',
					'null' => true,
				],
			];
	        $this->dbforge->add_column('game_logs_unsettle', $addFields);
	        $this->player_model->addIndex('game_logs_unsettle', 'idx_md5_sum', 'md5_sum');
		}

	}

	public function down() {
		//don't drop
	}

}
