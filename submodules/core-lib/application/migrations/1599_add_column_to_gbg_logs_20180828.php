<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_gbg_logs_20180828 extends CI_Migration {

    private $tableName = 'gbg_logs';

    public function up() {
        $fields = array(
            'generated_by' => array(
                'type' => 'INT',
                'null' => true,
                'default' => 0,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'generated_by');
    }
}