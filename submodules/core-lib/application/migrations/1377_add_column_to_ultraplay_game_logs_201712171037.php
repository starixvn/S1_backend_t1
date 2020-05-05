<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_ultraplay_game_logs_201712171037 extends CI_Migration {

    private $tableName = 'ultraplay_game_logs';

    public function up() {
        $fields = array(
            'game_code' => array(
                'type' => 'varchar',
                'constraint' => 100,
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'game_code');
    }
}
