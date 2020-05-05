<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_gl_game_logs_201902212200 extends CI_Migration {

    private $tableName='gl_game_logs';

    public function up() {
        $fields = array(
            'op_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'op_id');
    }
}