<?php

defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_add_column_to_tcg_game_logs_20200122 extends CI_Migration
{
	private $tableName = 'tcg_game_logs';

    public function up() {

        $fields = array(
            'bettingContent' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'winningContent' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('bettingContent', $this->tableName) && !$this->db->field_exists('winningContent', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('bettingContent', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'bettingContent');
        }
        if($this->db->field_exists('winningContent', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'winningContent');
        }
    }
}