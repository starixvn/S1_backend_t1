<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_column_daily_currency_20171124 extends CI_Migration {

    private $tableName = 'daily_currency';

    public function up() {
        //modify column
        $fields = array(
           'current_rate' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
           'api_response' => array(
                'type' => 'TEXT',
                'null' => true,
            )
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
        $fields = array(
            'current_rate' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'api_response' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }
}