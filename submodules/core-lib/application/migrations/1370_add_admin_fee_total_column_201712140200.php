<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_admin_fee_total_column_201712140200 extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('agency_wl_settlement', array(
            'admin_total' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0.0,
            ),
        ));
        $this->dbforge->add_column('agency_daily_player_settlement', array(
            'admin_total' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0.0,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column('agency_wl_settlement', 'admin_total');
        $this->dbforge->drop_column('agency_daily_player_settlement', 'admin_total');
    }
}