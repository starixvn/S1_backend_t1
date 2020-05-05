<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_pt_v2_game_logs_20191108 extends CI_Migration {

	private $tableName = 'pt_v2_game_logs';

    public function up() {

        $fields = array(
            'exit_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ),
            'bonus_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('exit_type', $this->tableName) && !$this->db->field_exists('bonus_type', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('exit_type', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'exit_type');
        }
        if($this->db->field_exists('bonus_type', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'bonus_type');
        }
    }
}