<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_table_round_id_to_varchar_in_vivo_gaming_idr1_game_logs_20191028 extends CI_Migration {

    private $tableName = 'vivo_gaming_idr1_game_logs';

    public function up() {

        $update_fields = array(
            'table_round_id' => array(
                'name' => 'table_round_id',
                'type' => 'VARCHAR',
                'constraint' => '13',
            ),
        );

        if($this->db->table_exists($this->tableName)){
            
            if($this->db->field_exists('table_round_id', $this->tableName)) {

                $this->dbforge->modify_column($this->tableName, $update_fields); 
                
            }

        }
    }

    public function down() {
    }
}
