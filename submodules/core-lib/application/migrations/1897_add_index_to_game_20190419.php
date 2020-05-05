<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_game_20190419 extends CI_Migration {

    public function up() {

        $this->load->model(['player_model']);
        $this->player_model->addIndex('mgplus_idr1_game_logs','idx_gameendtimeutc' , 'gameendtimeutc');
        $this->player_model->addIndex('mgplus_idr1_game_logs','idx_gamestarttimeutc' , 'gamestarttimeutc');
        $this->player_model->addIndex('mgplus_idr2_game_logs','idx_gameendtimeutc' , 'gameendtimeutc');
        $this->player_model->addIndex('mgplus_idr2_game_logs','idx_gamestarttimeutc' , 'gamestarttimeutc');
        $this->player_model->addIndex('mgplus_idr3_game_logs','idx_gameendtimeutc' , 'gameendtimeutc');
        $this->player_model->addIndex('mgplus_idr3_game_logs','idx_gamestarttimeutc' , 'gamestarttimeutc');
        $this->player_model->addIndex('mgplus_idr4_game_logs','idx_gameendtimeutc' , 'gameendtimeutc');
        $this->player_model->addIndex('mgplus_idr4_game_logs','idx_gamestarttimeutc' , 'gamestarttimeutc');
        $this->player_model->addIndex('mgplus_myr2_game_logs','idx_gameendtimeutc' , 'gameendtimeutc');
        $this->player_model->addIndex('mgplus_myr2_game_logs','idx_gamestarttimeutc' , 'gamestarttimeutc');
        $this->player_model->addIndex('mgplus_thb2_game_logs','idx_gameendtimeutc' , 'gameendtimeutc');
        $this->player_model->addIndex('mgplus_thb2_game_logs','idx_gamestarttimeutc' , 'gamestarttimeutc');
        $this->player_model->addIndex('mgplus_vnd2_game_logs','idx_gameendtimeutc' , 'gameendtimeutc');
        $this->player_model->addIndex('mgplus_vnd2_game_logs','idx_gamestarttimeutc' , 'gamestarttimeutc');
        $this->player_model->addIndex('mgplus_cny2_game_logs','idx_gameendtimeutc' , 'gameendtimeutc');
        $this->player_model->addIndex('mgplus_cny2_game_logs','idx_gamestarttimeutc' , 'gamestarttimeutc');

        $this->player_model->addIndex('game_provider_auth','idx_agent_id' , 'agent_id');

    }

    public function down() {
    }
}

///END OF FILE//////////