<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_remove_unique_index_of_table_round_id_of_vivo_gaming_idr_game_logs_20200227 extends CI_Migration {

    private $tableName='vivo_gaming_idr1_game_logs';

    public function up() {
        if($this->utils->table_really_exists($this->tableName)){
			# Add Index
	        $this->load->model('player_model');

            # remove unique index in table_round_id
            $this->player_model->dropIndex($this->tableName, 'idx_vivogaming_table_round_id', 'table_round_id');
        }
    }

    public function down() {
    }
}