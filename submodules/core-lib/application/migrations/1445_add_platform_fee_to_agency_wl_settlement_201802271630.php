<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_platform_fee_to_agency_wl_settlement_201802271630 extends CI_Migration {

    private $tableName = 'agency_wl_settlement';

    public function up() {
        $fields = array(
            'platform_fee' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0
            ),
        );
        $this->dbforge->add_column('agency_wl_settlement', $fields);
        $this->dbforge->add_column('agency_daily_player_settlement', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('agency_wl_settlement', 'platform_fee');
        $this->dbforge->drop_column('agency_daily_player_settlement', 'platform_fee');
    }
}
