<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_unique_index_to_accounting_transaction_id_vivo_gaming_game_logs_20190923 extends CI_Migration {

	private $tableName = 'vivo_gaming_idr1_game_logs';

	public function up() {

		if($this->db->table_exists($this->tableName)){
			# Add Index
	        $this->load->model('player_model');

            # remove unique index in table_round_id
            $this->player_model->dropIndex($this->tableName, 'idx_vivogaming_table_round_id', 'table_round_id');
            
            # add unique index accounting_transaction_id
            $this->player_model->addUniqueIndex($this->tableName, 'idx_vivogaming_accounting_transaction_id', 'accounting_transaction_id');
		}
	}

	public function down() {

    }
}
