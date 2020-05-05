<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_http_request_201708171542 extends CI_Migration {

    private $tableName = 'http_request';

    /**
     * Daily 0
     * Weekly 1-Mon, 2-Tues etc..
     */
    public function up() {
        $this->dbforge->add_column($this->tableName, array(
            'browser_type' => array(
                'type' => 'INT',
                'null' => true,
                'default' => 0,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'browser_type');
    }
}