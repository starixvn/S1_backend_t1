<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_remove_unique_index_refNo_iongaming_id1_game_logs_20191204 extends CI_Migration {

	private $tableName = 'iongaming_idr1_game_logs';

	public function up() {

		if($this->db->table_exists($this->tableName)){
			# Add Index
	        $this->load->model('player_model');

            # remove unique index of refNo field
            $this->player_model->dropIndex($this->tableName, 'idx_iongaming_refNo', 'refNo');
		}
	}

	public function down() {

    }
}
