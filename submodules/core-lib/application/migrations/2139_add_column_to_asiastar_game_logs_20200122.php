<?php

defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_add_column_to_asiastar_game_logs_20200122 extends CI_Migration
{
	private $tableName = 'asiastar_game_logs';

    public function up() {

        $fields = array(
            'revenue' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
        );


        if(!$this->db->field_exists('revenue', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('revenue', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'revenue');
        }
    }
}