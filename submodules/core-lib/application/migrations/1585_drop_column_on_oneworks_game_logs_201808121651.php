<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_drop_column_on_oneworks_game_logs_201808121651 extends CI_Migration {

    public function up() {

        if ($this->db->field_exists('createdAt', 'oneworks_game_logs')){
            $this->dbforge->drop_column('oneworks_game_logs', 'createdAt');
        }
        if ($this->db->field_exists('updatedAt', 'oneworks_game_logs')){
            $this->dbforge->drop_column('oneworks_game_logs', 'updatedAt');
        }

    }

    public function down() {
    }
}
