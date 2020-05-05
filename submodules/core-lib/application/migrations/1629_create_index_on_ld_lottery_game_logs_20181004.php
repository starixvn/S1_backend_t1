<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_on_ld_lottery_game_logs_20181004 extends CI_Migration {

    private $tableName = 'ld_lottery_game_logs';

    public function up() {
        $this->load->model('player_model'); # Any model class will do

        # drop existing index
        $this->player_model->dropIndex($this->tableName, 'idx_round_key','round_id,platform_account_id');

        $this->player_model->addIndex($this->tableName, 'idx_round_key', 'round_id,platform_account_id,lotto_name');
    }

    public function down() {
    }
}
