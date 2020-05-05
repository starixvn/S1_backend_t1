<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_deposit_comm_to_agency_settlement_201812261630 extends CI_Migration {

    private $tableName = 'agency_daily_player_settlement';

    public function up() {

        $fields = [
            // Deposit commission amount
            'deposit_comm' => [
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0
            ],
            'deposit_comm_total' => [
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0
            ]
        ];

        $this->dbforge->add_column('agency_daily_player_settlement', $fields);
        $this->dbforge->add_column('agency_wl_settlement', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('agency_daily_player_settlement', array('deposit_comm', 'deposit_comm_total'));
        $this->dbforge->drop_column('agency_wl_settlement', array('deposit_comm', 'deposit_comm_total'));
    }
}
