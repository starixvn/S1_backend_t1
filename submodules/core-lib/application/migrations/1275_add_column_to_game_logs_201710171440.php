<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_game_logs_201710171440 extends CI_Migration {

    private $tableName = 'game_logs';

    public function up() {
        $field = array(
            'odds' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'bet_for_cashback' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'real_betting_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
        );
        $this->dbforge->add_column('game_logs', $field);

        $fields = array(
            'bet_for_cashback' => array(
                'type' => 'DOUBLE',
                'null' => true,
            )
        );

        $this->dbforge->add_column('total_player_game_minute', $fields);
        $this->dbforge->add_column('total_player_game_hour', $fields);
        $this->dbforge->add_column('total_player_game_day', $fields);
        $this->dbforge->add_column('total_player_game_month', $fields);
        $this->dbforge->add_column('total_player_game_year', $fields);

        $this->dbforge->add_column('total_operator_game_minute', $fields);
        $this->dbforge->add_column('total_operator_game_hour', $fields);
        $this->dbforge->add_column('total_operator_game_day', $fields);
        $this->dbforge->add_column('total_operator_game_month', $fields);
        $this->dbforge->add_column('total_operator_game_year', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('game_logs', 'odds');
        $this->dbforge->drop_column('game_logs', 'bet_for_cashback');
        $this->dbforge->drop_column('game_logs', 'real_betting_amount');

        $this->dbforge->drop_column('total_player_game_minute', 'bet_for_cashback');
        $this->dbforge->drop_column('total_player_game_hour', 'bet_for_cashback');
        $this->dbforge->drop_column('total_player_game_day', 'bet_for_cashback');
        $this->dbforge->drop_column('total_player_game_month', 'bet_for_cashback');
        $this->dbforge->drop_column('total_player_game_year', 'bet_for_cashback');

        $this->dbforge->drop_column('total_operator_game_minute', 'bet_for_cashback');
        $this->dbforge->drop_column('total_operator_game_hour', 'bet_for_cashback');
        $this->dbforge->drop_column('total_operator_game_day', 'bet_for_cashback');
        $this->dbforge->drop_column('total_operator_game_month', 'bet_for_cashback');
        $this->dbforge->drop_column('total_operator_game_year', 'bet_for_cashback');

    }
}
