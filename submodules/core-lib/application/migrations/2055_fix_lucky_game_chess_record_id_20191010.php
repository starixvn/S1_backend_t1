<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_fix_lucky_game_chess_record_id_20191010 extends CI_Migration {
    private $tableName = 'lucky_game_game_logs';

    public function up() {

        $update_fields = array(
            'recordid' => array(
                'name' => 'recordid',
                'type' => 'VARCHAR',
                'constraint' => '200',
            ),
        );

        if($this->db->field_exists('recordid', $this->tableName)) {
            $this->dbforge->modify_column($this->tableName, $update_fields); 
        }
    }

    public function down() {

    }
}
