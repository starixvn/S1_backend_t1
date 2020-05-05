<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_game_logs_unsettle_201710171848 extends CI_Migration {

    private $tableName = 'game_logs_unsettle';

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
        $this->dbforge->add_column('game_logs_unsettle', $field);

    }

    public function down() {

        $this->dbforge->drop_column('game_logs_unsettle', 'odds');
        $this->dbforge->drop_column('game_logs_unsettle', 'bet_for_cashback');
        $this->dbforge->drop_column('game_logs_unsettle', 'real_betting_amount');

    }
}
