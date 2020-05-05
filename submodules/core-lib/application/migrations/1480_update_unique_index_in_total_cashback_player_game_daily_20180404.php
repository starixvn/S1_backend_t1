<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_unique_index_in_total_cashback_player_game_daily_20180404 extends CI_Migration {

    private $tableName = 'total_cashback_player_game_daily';

    public function up() {

        $this->load->model(['player_model']);
        // drop exist idx_player_game_date
        if ( $this->player_model->existsIndex($this->tableName, 'idx_player_game_date')) {
            $this->db->query('drop index idx_player_game_date on total_cashback_player_game_daily');
        }

        // update existing idx. will add cashback_type (normal or referral)
        $this->db->query('create unique index idx_unique_record on total_cashback_player_game_daily(player_id,game_description_id,total_date, cashback_type)');
    }

    public function down() {
        $this->db->query('drop index idx_unique_record on total_cashback_player_game_daily');
    }

}