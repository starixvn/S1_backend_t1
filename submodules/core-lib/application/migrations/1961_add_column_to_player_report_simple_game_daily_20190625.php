<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_player_report_simple_game_daily_20190625 extends CI_Migration {

    public function up() {
        $fields = array(
            'game_username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
        );

        $this->dbforge->add_column('player_report_simple_game_daily', $fields);

    }

    public function down() {
        $this->dbforge->drop_column('player_report_simple_game_daily', 'game_username');
    }
}
