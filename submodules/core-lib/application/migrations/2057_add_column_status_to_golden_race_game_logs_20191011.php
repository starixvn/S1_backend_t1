<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_status_to_golden_race_game_logs_20191011 extends CI_Migration {

    private $tableName = 'golden_race_game_logs';

    public function up() {

        $fields = array(
            'status' => array(
                'type' => 'SMALLINT',
                'null' => true,
            )
        );

        if(!$this->db->field_exists('status', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('status', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'status');
        }
    }
}