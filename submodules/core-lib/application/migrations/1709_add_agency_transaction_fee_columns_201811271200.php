<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_agency_transaction_fee_columns_201811271200 extends CI_Migration {

    public function up() {
        $fields = [
            'deposit_fee' => [
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ],
            'withdraw_fee' => [
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ],
        ];

        if(!$this->db->field_exists('deposit_fee', 'agency_agents')){
            $this->dbforge->add_column('agency_agents', $fields);
        }

        $fields = [
            'deposit_fee' => [
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ],
            'withdraw_fee' => [
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ],
            'deposit_fee_total' => [
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ],
            'withdraw_fee_total' => [
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ],
        ];

        if(!$this->db->field_exists('deposit_fee', 'agency_daily_player_settlement')){
            $this->dbforge->add_column('agency_daily_player_settlement', $fields);
        }

        $fields = [
            'deposit_fee' => [
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ],
            'withdraw_fee' => [
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ],
            'deposit_fee_total' => [
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ],
            'withdraw_fee_total' => [
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ],
        ];

        if(!$this->db->field_exists('deposit_fee', 'agency_wl_settlement')){
            $this->dbforge->add_column('agency_wl_settlement', $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('deposit_fee', 'agency_agents')){
            $this->dbforge->drop_column('agency_agents', 'deposit_fee');
            $this->dbforge->drop_column('agency_agents', 'withdraw_fee');
        }

        if($this->db->field_exists('deposit_fee', 'agency_daily_player_settlement')){
            $this->dbforge->drop_column('agency_daily_player_settlement', 'deposit_fee');
            $this->dbforge->drop_column('agency_daily_player_settlement', 'withdraw_fee');
            $this->dbforge->drop_column('agency_daily_player_settlement', 'deposit_fee_total');
            $this->dbforge->drop_column('agency_daily_player_settlement', 'withdraw_fee_total');
        }

        if($this->db->field_exists('deposit_fee', 'agency_wl_settlement')){
            $this->dbforge->drop_column('agency_wl_settlement', 'deposit_fee');
            $this->dbforge->drop_column('agency_wl_settlement', 'withdraw_fee');
            $this->dbforge->drop_column('agency_wl_settlement', 'deposit_fee_total');
            $this->dbforge->drop_column('agency_wl_settlement', 'withdraw_fee_total');
        }
    }
}
