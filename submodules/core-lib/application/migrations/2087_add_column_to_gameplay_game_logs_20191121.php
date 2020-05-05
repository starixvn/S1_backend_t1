<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_gameplay_game_logs_20191121 extends CI_Migration {

	private $tableName = 'gameplay_game_logs';

    public function up() {

        $fields = array(
            'round_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'fround' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'rebate_amount' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('round_id', $this->tableName) && !$this->db->field_exists('fround', $this->tableName) && !$this->db->field_exists('rebate_amount', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('round_id', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'round_id');
        }
        if($this->db->field_exists('fround', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'fround');
        }
        if($this->db->field_exists('rebate_amount', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'rebate_amount');
        }
    }
}