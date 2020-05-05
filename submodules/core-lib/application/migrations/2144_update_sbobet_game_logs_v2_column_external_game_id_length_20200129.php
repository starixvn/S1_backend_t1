<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_sbobet_game_logs_v2_column_external_game_id_length_20200129 extends CI_Migration {
    public function up() {
        //modify column size
        $fields = array(
            'external_game_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '60',
                'null' => true,
            ),
        );
        if($this->db->field_exists('external_game_id', 'sbobet_game_logs_v2')){
            $this->dbforge->modify_column('sbobet_game_logs_v2', $fields);
        }
    }

    public function down() {
        // not able to rollback due to data truncation
    }
}
