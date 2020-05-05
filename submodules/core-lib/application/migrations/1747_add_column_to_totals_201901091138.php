<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_totals_201901091138 extends CI_Migration {

    public function up() {
        $fields = array(
           'currency_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null'=> true
            ),
        );

        $this->dbforge->add_column('total_player_game_minute', $fields);
        $this->dbforge->add_column('total_player_game_hour', $fields);
        $this->dbforge->add_column('total_player_game_day', $fields);
        $this->dbforge->add_column('total_player_game_month', $fields);
        $this->dbforge->add_column('total_player_game_year', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('total_player_game_minute', 'currency_key');
        $this->dbforge->drop_column('total_player_game_hour', 'currency_key');
        $this->dbforge->drop_column('total_player_game_day', 'currency_key');
        $this->dbforge->drop_column('total_player_game_month', 'currency_key');
        $this->dbforge->drop_column('total_player_game_year', 'currency_key');
    }
}
