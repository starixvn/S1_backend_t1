<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_vr_game_logs_column_20171219 extends CI_Migration {

    CONST TableName = 'vr_game_logs';

    public function up() {

        $fields = array(
                'number' => array(
                        'type' => 'VARCHAR',
                        'constraint' => '500',
                        'null' => true,
                )
        );

        $this->dbforge->modify_column(self::TableName, $fields);
    }

    public function down() {
         $fields = array(
                'number' => array(
                        'type' => 'VARCHAR',
                        'constraint' => '100',
                        'null' => true,
                )
        );

        $this->dbforge->modify_column(self::TableName, $fields);
    }
}