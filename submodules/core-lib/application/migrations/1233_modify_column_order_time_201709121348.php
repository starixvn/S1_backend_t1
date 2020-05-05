<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_order_time_201709121348 extends CI_Migration {

    private $tableName = 'lebo_game_logs';

    public function up() {

        if ( $this->db->field_exists('order_time', $this->tableName) )
        {
            $fields = array(
                    'order_time' => array(
                        'type' => 'VARCHAR',
                        'constraint' => '100',
                        'null' => false
                        )
                    );

            $this->dbforge->modify_column($this->tableName, $fields);
        }
    }

    public function down() {

        if ( $this->db->field_exists('order_time', $this->tableName) )
        {
            $fields = array(
                    'order_time' => array(
                        'type' => 'TIMESTAMP',
                        'null' => true,
                        ),
                    );

            $this->dbforge->modify_column($this->tableName, $fields);
        }
    }
}
