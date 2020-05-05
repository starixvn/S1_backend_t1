<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alert_column_value_to_player_201712221600 extends CI_Migration {

    public function up() {

        $data = array(
            'is_registered_popup_success_done' => 1
        );
        
        $this->db->update('player', $data); 
    }

    public function down() {}
}