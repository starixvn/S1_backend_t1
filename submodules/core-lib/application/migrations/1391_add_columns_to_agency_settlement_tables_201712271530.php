<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_agency_settlement_tables_201712271530 extends CI_Migration {

    # These columns will be used to hold bet amounts for display
    # The actual amount will be adjusted based on odds
    public function up() {
        $this->dbforge->add_column('agency_daily_player_settlement', array(
            'bets_display' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0.0,
            ),
            'bets_except_tie_display' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0.0,
            ),
        ));
        $this->dbforge->add_column('agency_wl_settlement', array(
            'bets_display' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0.0,
            ),
            'bets_except_tie_display' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0.0,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column('agency_daily_player_settlement', 'bets_display');
        $this->dbforge->drop_column('agency_daily_player_settlement', 'bets_except_tie_display');
        $this->dbforge->drop_column('agency_wl_settlement', 'bets_display');
        $this->dbforge->drop_column('agency_wl_settlement', 'bets_except_tie_display');
    }
}