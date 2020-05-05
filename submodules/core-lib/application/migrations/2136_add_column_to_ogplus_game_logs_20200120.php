<?php

defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_add_column_to_ogplus_game_logs_20200120 extends CI_Migration
{
	private $tableName = 'ogplus_game_logs';

    public function up() {

        $fields = array(
            'gameusername' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('gameusername', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
            
            # add index
            $this->player_model->addIndex($this->tableName, 'idx_gameusername', 'gameusername');
        }
    }

    public function down() {
        if($this->db->field_exists('gameusername', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'gameusername');
        }
    }
}