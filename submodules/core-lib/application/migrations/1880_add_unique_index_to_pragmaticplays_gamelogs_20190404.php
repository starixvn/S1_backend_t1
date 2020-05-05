<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unique_index_to_pragmaticplays_gamelogs_20190404 extends CI_Migration {
    public function up() {
        $this->load->model('player_model');
        # IDR
        $this->player_model->addIndex('pragmaticplay_idr1_game_logs', 'idx_external_uniqueid', 'external_uniqueid', true);
        $this->player_model->addIndex('pragmaticplay_idr2_game_logs', 'idx_external_uniqueid', 'external_uniqueid', true);
        $this->player_model->addIndex('pragmaticplay_idr3_game_logs', 'idx_external_uniqueid', 'external_uniqueid', true);
        $this->player_model->addIndex('pragmaticplay_idr4_game_logs', 'idx_external_uniqueid', 'external_uniqueid', true);
        # IDR
        $this->player_model->addIndex('pragmaticplay_thb1_game_logs', 'idx_external_uniqueid', 'external_uniqueid', true);
        $this->player_model->addIndex('pragmaticplay_thb2_game_logs', 'idx_external_uniqueid', 'external_uniqueid', true);
        # MYR
        $this->player_model->addIndex('pragmaticplay_myr1_game_logs', 'idx_external_uniqueid', 'external_uniqueid', true);
        $this->player_model->addIndex('pragmaticplay_myr2_game_logs', 'idx_external_uniqueid', 'external_uniqueid', true);
        # VND
        $this->player_model->addIndex('pragmaticplay_vnd1_game_logs', 'idx_external_uniqueid', 'external_uniqueid', true);
        $this->player_model->addIndex('pragmaticplay_vnd2_game_logs', 'idx_external_uniqueid', 'external_uniqueid', true);
        # CNY
        $this->player_model->addIndex('pragmaticplay_cny1_game_logs', 'idx_external_uniqueid', 'external_uniqueid', true);
        $this->player_model->addIndex('pragmaticplay_cny2_game_logs', 'idx_external_uniqueid', 'external_uniqueid', true);
    }

    public function down() {
    }
}

///END OF FILE//////////