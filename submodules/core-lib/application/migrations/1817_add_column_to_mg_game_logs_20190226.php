<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_mg_game_logs_20190226 extends CI_Migration {

    private $tableName='mg_game_logs';

    public function up() {
        $fields = array(
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'md5_sum');
    }
}