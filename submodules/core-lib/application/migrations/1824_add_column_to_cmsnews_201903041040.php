<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_cmsnews_201903041040 extends CI_Migration {

    private $tableName = 'cmsnews';

    public function up() {

        if(!$this->db->field_exists('is_daterange', $this->tableName)){
            $this->dbforge->add_column($this->tableName, [
                'is_daterange' => [
                    'type' => 'INT',
                    'default' => 0,
                ]
            ]);
        }

        if(!$this->db->field_exists('start_date', $this->tableName)){
            $this->dbforge->add_column($this->tableName, [
                'start_date' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ]
            ]);
        }

        if(!$this->db->field_exists('end_date', $this->tableName)){
            $this->dbforge->add_column($this->tableName, [
                'end_date' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ]
            ]);
        }
    }

    public function down() {
        if($this->db->field_exists('is_daterange', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'is_daterange');
        }
        if($this->db->field_exists('start_date', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'start_date');
        }
        if($this->db->field_exists('end_date', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'end_date');
        }
    }
}