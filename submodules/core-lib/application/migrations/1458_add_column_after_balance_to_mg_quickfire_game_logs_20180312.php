<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_after_balance_to_mg_quickfire_game_logs_20180312 extends CI_Migration {

    private $tableName = 'mg_quickfire_game_logs';

    public function up() {

        if ( ! $this->db->field_exists('after_balance', $this->tableName)) {
            $fields = array(
                'after_balance' => array(
                    'type' => 'DOUBLE',
                    'null' => true
                )
            );
            $this->dbforge->add_column($this->tableName, $fields);
        }

        if ( ! $this->db->field_exists('created_at', $this->tableName)) {
            $fields = array(
                'created_at' => array(
                    'type' => 'DATETIME',
                    'null' => true
                )
            );
            $this->dbforge->add_column($this->tableName, $fields);
        }

    }

    public function down() {
        
        if ($this->db->field_exists('after_balance', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'after_balance');
        }

        if ($this->db->field_exists('created_at', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'created_at');
        }

    }

}