<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_remove_unique_index_to_fg_game_logs_notunique_20181227 extends CI_Migration {
	public function up() {
        $this->load->model('player_model'); # Any model class will do

        # FG ENTAPLAY GAMELOGS
        $this->player_model->dropIndex('fg_entaplay_game_logs', 'idx_trans_id');
        $this->player_model->addIndex('fg_entaplay_game_logs', 'idx_trans_id', 'trans_id');
        
        # FG GAMELOGS
        $this->player_model->dropIndex('fg_game_logs', 'idx_trans_id');
        $this->player_model->addIndex('fg_game_logs', 'idx_trans_id', 'trans_id');
	}

	public function down() {
	}
}

///END OF FILE//////////