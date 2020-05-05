<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_lucky_game_game_logs_20190714 extends CI_Migration {

    public function up() {

        $fields = array(
            'username_without_prefix' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );
        $this->dbforge->add_column('lucky_game_game_logs', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('lucky_game_game_logs', 'username_without_prefix');
    }
}