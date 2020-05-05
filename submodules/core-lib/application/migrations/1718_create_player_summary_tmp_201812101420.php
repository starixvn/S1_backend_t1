<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_player_summary_tmp_201812101420 extends CI_Migration {

    private $tableName = 'player_summary_tmp';

    public function up() {
        $fields = array(
            'playerId' => array(
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ),
            'totalBettingAmount' => array(
                'type' => 'DOUBLE',
                'null' => true,
                'default' => 0,
            ),
            'approvedWithdrawAmount' => array(
                'type' => 'DOUBLE',
                'null' => true,
                'default' => 0,
            ),
            'totalDepositAmount' => array(
                'type' => 'DOUBLE',
                'null' => true,
                'default' => 0,
            ),
            'approvedWithdrawCount' => array(
                'type' => 'INT',
                'null' => true,
                'default' => 0,
            ),
            'total_deposit_count' => array(
                'type' => 'INT',
                'null' => true,
                'default' => 0,
            ),
            'first_deposit' => array(
                'type' => 'DOUBLE',
                'null' => true,
                'default' => 0,
            ),
            'second_deposit' => array(
                'type' => 'DOUBLE',
                'null' => true,
                'default' => 0,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('playerId', TRUE);
        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}