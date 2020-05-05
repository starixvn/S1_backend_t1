<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_external_game_id_to_kycard_game_logs_20190513 extends CI_Migration {

    private $tableName = 'kycard_game_logs';

    public function up() {
        $field = array(
           'external_game_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            )
        );

        if(!$this->db->field_exists('external_game_id', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $field);
        }

    }

    public function down() {
        if($this->db->field_exists('external_game_id', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'external_game_id');
        }
    }
}
