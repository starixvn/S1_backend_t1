<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_on_ld_lottery_game_logs_20180925 extends CI_Migration {

    public function up() {
        $this->db->query('create index idx_round_key on ld_lottery_game_logs(round_id, platform_account_id)');
    }

    public function down() {
        $this->db->query('drop index idx_round_key on ld_lottery_game_logs');
    }
}
