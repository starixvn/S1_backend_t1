<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_totals_201703041313 extends CI_Migration {

    private $tables=[
        'total_operator_game_day',
        'total_operator_game_hour',
        'total_operator_game_minute',
        'total_operator_game_month',
        'total_operator_game_year',
        'total_player_game_day',
        'total_player_game_hour',
        'total_player_game_minute',
        'total_player_game_month',
        'total_player_game_year',
    ];

    public function up() {
        $fields=array(
            'real_betting_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
        );

        foreach ($this->tables as $table_name) {
            $this->dbforge->add_column($table_name, $fields);
        }
    }

    public function down() {
        foreach ($this->tables as $table_name) {
            $this->dbforge->drop_column($table_name, 'real_betting_amount');
        }
    }

}

///END OF FILE//////////////////