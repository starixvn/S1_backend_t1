<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorules_20171219 extends CI_Migration {

    private $tableName = 'promorules';

    public function up() {
        $fields = array(
            'language' => array(
                'type' => 'INT',
                'null' => false,
                'default' => 0,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'language');
    }
}
