<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_fields_on_player_report_hourly_table_20181008 extends CI_Migration {

    private $tblName = 'player_report_hourly';

    public function up() {

        $exist_fields = $this->db->list_fields($this->tblName);

        $fields = array(
            'player_username' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'player_realName' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'contactNumber' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'gender' => array(
                'type' => 'VARCHAR',
                'constraint' => '6',
                'null' => true,
            ),
            'agent_username' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'affiliate_username' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'registered_by' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'registrationIP' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'last_login_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'last_login_date' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'last_logout_date' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'registered_date' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'total_bets' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'bet_times' => array(
                'type' => 'INT',
                'default' => 0
            ),
            'referral_bonus' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'subtract_bonus' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'deposit_bonus' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'deposit_times' => array(
                'type' => 'INT',
                'default' => 0,
            ),
            'payout' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'payout_rate' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'deleted_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );

        foreach ($fields as $key => $value) {
            if (!in_array($key, $exist_fields)) {
                $this->dbforge->add_column($this->tblName, array($key=>$value));
            }
        }

    }

    public function down(){

        $exist_fields = $this->db->list_fields($this->tblName);

        $drop_columns = ['player_username', 'player_realName', 'email', 'contactNumber', 'gender', 'agent_username',
                'affiliate_username', 'registered_by', 'registrationIP', 'last_login_ip', 'last_login_date', 'last_logout_date',
                'registered_date', 'total_bets', 'bet_times', 'referral_bonus', 'subtract_bonus', 'deposit_bonus', 'deposit_times',
                'payout', 'payout_rate', 'game_bet_details', 'deleted_at'];

        foreach ($drop_columns as $value) {
            if (in_array($value, $exist_fields)) {
                $this->dbforge->drop_column($this->tblName, $value);
            }
        }
    }

}