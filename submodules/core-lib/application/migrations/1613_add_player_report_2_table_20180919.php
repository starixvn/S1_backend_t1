<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_player_report_2_table_20180919 extends CI_Migration {

    private $tblName = 'player_report_2';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'player_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'player_username' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'player_realName' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'level_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'level_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'tag_ids' => array(
                'type' => 'INT',
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
                'constraint' => '10',
                'null' => true,
            ),
            'agent_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'agent_username' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'affiliate_id' => array(
                'type' => 'INT',
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
            'deposit_bonus' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'cashback' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'referral_bonus' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'manual_bonus' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'subtract_bonus' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'total_bonus' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'first_deposit' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'second_deposit' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'total_deposit' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'total_deposit_times' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'total_withdrawal' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'total_deposit_withdraw' => array(
                'type' => 'DOUBLE',
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
            'game_bet_details' => array(
                'type' => 'VARCHAR',
                'constraint' => '1000',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'deleted_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tblName);

    }

    public function down() {
        $this->dbforge->drop_table($this->tblName);
    }

}