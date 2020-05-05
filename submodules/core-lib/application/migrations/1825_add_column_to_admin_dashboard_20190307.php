<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_admin_dashboard_20190307 extends CI_Migration {

    private $tableName = 'admin_dashboard';

    public function up() {

        $fields = array(
            'top_bet_amount_players_today' => array(
                'type' => 'text',
                'null' => true,
            ),
            'top_bet_amount_games_today' => array(
                'type' => 'text',
                'null' => true,
            ),
           
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'top_bet_amount_players_today');
        $this->dbforge->drop_column($this->tableName, 'top_bet_amount_games_today');
    }
}
