<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_game_description_201801161740 extends CI_Migration {

    private $tableName = 'game_description';

    public function up() {
        $fields = array(
            'enabled_on_android' => array(
                'type' => 'boolean',
                'null' => true,
            ),
            'enabled_on_ios' => array(
                'type' => 'boolean',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'enabled_on_android');
        $this->dbforge->drop_column($this->tableName, 'enabled_on_ios');
    }
}