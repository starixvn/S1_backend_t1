<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promocmssetting_201701160921 extends CI_Migration {

    private $tableName = 'promocmssetting';

    public function up() {
        $fields = array(
            'hide_on_player' => array(
                'type' => 'INT',
                'null'=>true,
                'default'=>0,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'hide_on_player');
    }
}