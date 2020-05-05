<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_player_password_history_2006110918142 extends CI_Migration {

    public function up() {
        $this->db->query('create index idx_player_id on player_password_history(player_id)');
    }

    public function down() {
        $this->db->query('drop index idx_player_id on player_password_history');
    }
}

///END OF FILE//////////