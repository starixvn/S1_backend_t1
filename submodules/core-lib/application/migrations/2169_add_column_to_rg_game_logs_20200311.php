<?php

defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_add_column_to_rg_game_logs_20200311 extends CI_Migration
{
	private $tableName = 'rg_game_logs';

    public function up() {

        $fields = array(
            'bet_details' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('bet_details', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('bet_details', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'bet_details');
        }
    }
}