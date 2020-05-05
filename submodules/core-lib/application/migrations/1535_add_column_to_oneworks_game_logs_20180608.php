<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_oneworks_game_logs_20180608 extends CI_Migration {

    private $tableName = 'oneworks_game_logs';

    public function up() {
        $fields = array(
            'cash_out_data' => array(
                'type' => 'text',
                'null' => true,
            )
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'cash_out_data');
    }
}
