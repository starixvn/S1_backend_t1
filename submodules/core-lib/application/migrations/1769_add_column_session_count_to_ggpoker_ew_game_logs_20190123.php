<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_session_count_to_ggpoker_ew_game_logs_20190123 extends CI_Migration {

    private $tableName = 'ggpoker_ew_game_logs';

    public function up() {
        $fields = array(
            'sessionCount' => array(
                'type' => 'INT',
                'null' => true,
            )
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {}
}