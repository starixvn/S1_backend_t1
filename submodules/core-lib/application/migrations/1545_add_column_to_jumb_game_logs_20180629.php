<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_jumb_game_logs_20180629 extends CI_Migration {

    private $tableName = 'jumb_game_logs';

    public function up() {
        $fields = array(
            'gType' => array(
                'type' => 'SMALLINT',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'gType');
    }
}
