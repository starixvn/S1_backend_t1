<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_cashback_request_201706201455 extends CI_Migration {

    private $tableName = 'cashback_request';

    public function up() {
        $fields = array(
            'regenerate_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'regenerate_datetime');
    }
}