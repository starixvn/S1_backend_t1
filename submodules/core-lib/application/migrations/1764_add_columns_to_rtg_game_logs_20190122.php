<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_rtg_game_logs_20190122 extends CI_Migration {

    private $tableName = 'rtg_game_logs';

    public function up() {
        $fields = array(
            'jackpot_contribution_maxi' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'jackpot_contribution_rdm' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'jackpot_contribution_ssd' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'jackpot_win_grand' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'jackpot_win_maxi' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'jackpot_win_rdm' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'jackpot_win_ssd' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'insurance_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'valid_bet' => array(
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),
            'side_bet_jackpot_total_contribution' => array(   # The total amount the player will contribute with each spin/hand if they opt in to Side bet Jackpot
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'side_bet_jackpot_payout' => array(  # The amount of payout that will receive by the player when they hit the Side bet Jackpot
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'side_bet_jackpot_game_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null'=> true
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'jackpot_contribution_maxi');
        $this->dbforge->drop_column($this->tableName, 'jackpot_contribution_rdm');
        $this->dbforge->drop_column($this->tableName, 'jackpot_contribution_ssd');
        $this->dbforge->drop_column($this->tableName, 'jackpot_win_grand');
        $this->dbforge->drop_column($this->tableName, 'jackpot_win_maxi');
        $this->dbforge->drop_column($this->tableName, 'jackpot_win_rdm');
        $this->dbforge->drop_column($this->tableName, 'jackpot_win_ssd');
        $this->dbforge->drop_column($this->tableName, 'insurance_amount');
        $this->dbforge->drop_column($this->tableName, 'valid_bet');
        $this->dbforge->drop_column($this->tableName, 'side_bet_jackpot_total_contribution');
        $this->dbforge->drop_column($this->tableName, 'side_bet_jackpot_payout');
        $this->dbforge->drop_column($this->tableName, 'side_bet_jackpot_game_id');
    }
}