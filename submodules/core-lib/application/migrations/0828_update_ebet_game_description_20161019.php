<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_ebet_game_description_20161019 extends CI_Migration {

    public function up() {
        $this->db->query('UPDATE game_description INNER JOIN game_type ON game_type.game_type = game_description.game_name SET game_description.game_type_id = game_type.id WHERE game_description.game_platform_id = 53');
    }

    public function down() {
    }

}