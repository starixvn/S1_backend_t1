<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_daily_player_settlement_201712110200 extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('agency_wl_settlement', array(
            'bonuses_total' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0.0,
            ),
            'rebates_total' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0.0,
            ),
            'transactions_total' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0.0,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column('agency_wl_settlement', 'bonuses_total');
        $this->dbforge->drop_column('agency_wl_settlement', 'rebates_total');
        $this->dbforge->drop_column('agency_wl_settlement', 'transactions_total');
    }
}