<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_daily_player_settlement_201712111630 extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('agency_daily_player_settlement', array(
            # Records the basis amount determined by setting
            # Used to calculate rolling for all up-level agents
            'rolling_basis_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0.0,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column('agency_daily_player_settlement', 'rolling_basis_amount');
    }
}