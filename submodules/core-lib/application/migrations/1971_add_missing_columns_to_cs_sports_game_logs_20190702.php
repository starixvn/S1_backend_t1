<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_missing_columns_to_cs_sports_game_logs_20190702 extends CI_Migration {

    public function up() {

        $cs_sports_game_logs_fields = array(
            'odds' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );
        $this->dbforge->add_column('cs_sports_game_logs', $cs_sports_game_logs_fields);
    }

    public function down() {
        $this->dbforge->drop_column('cs_sports_game_logs', 'odds');
    }
}