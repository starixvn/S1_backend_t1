<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_on_transfer_conditions_201903041200 extends CI_Migration {

    private $tableName = 'transfer_conditions';

    public function up() {
        $fields = [
            'condition_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '19,6',
                'null' => TRUE,
                'default' => '0',
            ],
        ];

        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {}
}