<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_status_to_game_logs_20181114 extends CI_Migration {

    private $tableName = 'game_logs';

    public function up() {
        if(!$this->db->field_exists('status', $this->tableName)){
            $fields = array(
                'status' => array(
                    'type' => 'INT',
                    'null' => false,
                    'default' => 1,
                ),
            );
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('status', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'status');
        }
    }
}