<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unique_index_to_remaining_gamelogs_table_20181227 extends CI_Migration {
	public function up() {
        $this->load->model('player_model'); # Any model class will do
        # HABA GAMELOGS
        $this->player_model->dropIndex('haba88_game_logs', 'idx_haba88_game_logs_external_uniqueid');
        $this->player_model->addIndex('haba88_game_logs', 'idx_haba88_game_logs_external_uniqueid', 'external_uniqueid',true);

        # PT GAMELOGS
        $this->player_model->dropIndex('pt_game_logs', 'idx_pt_game_logs_external_uniqueid');
        $this->player_model->addIndex('pt_game_logs', 'idx_pt_game_logs_external_uniqueid', 'external_uniqueid',true);
        
        # MG GAMELOGS
        $this->player_model->dropIndex('mg_game_logs', 'idx_external_uniqueid');
        $this->player_model->addIndex('mg_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        
        # ISB GAMELOGS
        $this->player_model->dropIndex('isb_game_logs', 'idx_external_uniqueid');
        $this->player_model->addIndex('isb_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);

        # AGIN GAMELOGS
        $this->player_model->dropIndex('agin_game_logs', 'idx_external_uniqueid');
        $this->player_model->addIndex('agin_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        
        # EZUGI GAMELOGS
        $this->player_model->addIndex('ezugi_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);

        # PRAGMATICPLAY GAMELOGS
        $this->player_model->addIndex('pragmaticplay_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);

        # PNG GAMELOGS
        $this->player_model->addIndex('png_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);

        # SUNCITY GAMELOGS
        $this->player_model->dropIndex('suncity_game_logs', 'external_uniqueid');
        $this->player_model->addIndex('suncity_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);

	}

	public function down() {
	}
}

///END OF FILE//////////