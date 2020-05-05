<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_gd_game_logs_201712131836 extends CI_Migration {

    private $tableName = 'gd_game_logs';

    public function up() {
        $fields = array(
            'extra' => array(
                'type' => 'text',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'extra');
    }
}
