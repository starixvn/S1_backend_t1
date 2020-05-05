<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_round_id_in_redtigerseamless_game_logs_20191008 extends CI_Migration {
    private $tableName = 'redtigerseamless_game_logs';

    public function up() {

        $update_fields = array(
            'round_id' => array(
                'name' => 'round_id',
                'type' => 'VARCHAR',
                'constraint' => '200',
            ),
        );

        if($this->db->field_exists('round_id', $this->tableName)) {
            $this->dbforge->modify_column($this->tableName, $update_fields); 
        }
    }

    public function down() {
    }
}
