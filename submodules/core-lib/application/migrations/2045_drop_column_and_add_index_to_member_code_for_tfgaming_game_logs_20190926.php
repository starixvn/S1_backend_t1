<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_drop_column_and_add_index_to_member_code_for_tfgaming_game_logs_20190926 extends CI_Migration {
    
    private $tableName = 'tfgaming_esports_game_logs';

    public function up() {

        if($this->db->table_exists($this->tableName)){
            $this->player_model->addUniqueIndex($this->tableName, 'idx_member_code', 'member_code');
        }
        if($this->db->field_exists('type', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'type');
        }
    }

    public function down() {

    }
}
