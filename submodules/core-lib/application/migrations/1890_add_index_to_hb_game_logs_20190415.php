<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_hb_game_logs_20190415 extends CI_Migration {

    public function up() {

        $this->load->model(['player_model']);
        $this->player_model->addIndex('haba88_idr1_game_logs','idx_dtstarted' , 'dtstarted');
        $this->player_model->addIndex('haba88_idr1_game_logs','idx_dtcompleted' , 'dtcompleted');
        $this->player_model->addIndex('haba88_idr2_game_logs','idx_dtstarted' , 'dtstarted');
        $this->player_model->addIndex('haba88_idr2_game_logs','idx_dtcompleted' , 'dtcompleted');
        $this->player_model->addIndex('haba88_idr3_game_logs','idx_dtstarted' , 'dtstarted');
        $this->player_model->addIndex('haba88_idr3_game_logs','idx_dtcompleted' , 'dtcompleted');
        $this->player_model->addIndex('haba88_idr4_game_logs','idx_dtstarted' , 'dtstarted');
        $this->player_model->addIndex('haba88_idr4_game_logs','idx_dtcompleted' , 'dtcompleted');
        $this->player_model->addIndex('haba88_myr1_game_logs','idx_dtstarted' , 'dtstarted');
        $this->player_model->addIndex('haba88_myr1_game_logs','idx_dtcompleted' , 'dtcompleted');
        $this->player_model->addIndex('haba88_myr2_game_logs','idx_dtstarted' , 'dtstarted');
        $this->player_model->addIndex('haba88_myr2_game_logs','idx_dtcompleted' , 'dtcompleted');
        $this->player_model->addIndex('haba88_thb1_game_logs','idx_dtstarted' , 'dtstarted');
        $this->player_model->addIndex('haba88_thb1_game_logs','idx_dtcompleted' , 'dtcompleted');
        $this->player_model->addIndex('haba88_vnd1_game_logs','idx_dtstarted' , 'dtstarted');
        $this->player_model->addIndex('haba88_vnd1_game_logs','idx_dtcompleted' , 'dtcompleted');

    }

    public function down() {
    }
}

///END OF FILE//////////