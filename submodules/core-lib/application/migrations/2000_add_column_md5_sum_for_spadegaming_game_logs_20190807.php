<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_md5_sum_for_spadegaming_game_logs_20190807 extends CI_Migration {
    
    private $tableName = 'spadegaming_game_logs';

    public function up() {
        $fields = array(
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP' => array(
                'null' => true,
            ),
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' => array(
                'null' => true,
            ),
        );
        if(!$this->db->field_exists('md5_sum', $this->tableName) && !$this->db->field_exists('created_at', $this->tableName) && !$this->db->field_exists('updated_at', $this->tableName)){
             $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('md5_sum', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'md5_sum');
        }
        if($this->db->field_exists('created_at', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'created_at');
        }
        if($this->db->field_exists('updated_at', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'updated_at');
        }
    }
}
