<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_register_201709031349 extends CI_Migration {

    private $tableName = 'game_provider_auth';

    public function up() {
        $fields = array(
            'register' => array(
                'name'=>'register',
                'type' => 'INT',
                'null' => true,
                'default' => 0,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
        // $this->dbforge->drop_column($this->tableName, 'regenerate_datetime');
    }
}