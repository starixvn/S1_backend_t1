<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_cashback_request_201706211345 extends CI_Migration {

    private $tableName = 'cashback_request';

    public function up() {
        $fields = array(
            'parent_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'parent_paid_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'parent_id');
        $this->dbforge->drop_column($this->tableName, 'parent_paid_amount');
    }
}