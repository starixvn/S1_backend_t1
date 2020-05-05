<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_on_update_for_asialong_game_logs_201804241630 extends CI_Migration {

    public function up() {
        $this->db->query("ALTER TABLE asialong_game_logs MODIFY created_at DATETIME DEFAULT CURRENT_TIMESTAMP");
        $this->db->query("ALTER TABLE asialong_game_logs MODIFY updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    }

    public function down() {
    }
}