<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_more_fields_on_player_report_hourly_table_20181012 extends CI_Migration {

    private $tblName = 'player_report_hourly';

    public function up() {

        $exist_fields = $this->db->list_fields($this->tblName);

        $fields = array(
            'winning_bets' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'lost_bets' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'tie_bets' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'total_odds_bets' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'total_odds_real_bets' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'total_real_bets' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'total_live_bets' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'total_live_real_bets' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'live_winning_bets' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'live_lost_bets' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'live_tie_bets' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'live_gain_sum' => array(
                'type' => 'DOUBLE',
                'default' => 0,
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

        $drop_columns = ['winning_bets', 'lost_bets', 'tie_bets', 'total_odds_bets', 'total_odds_real_bets', 'total_real_bets',
                'total_live_bets', 'total_live_real_bets', 'live_winning_bets', 'live_lost_bets', 'live_tie_bets', 'live_gain_sum'];

        foreach ($drop_columns as $value) {
            if (in_array($value, $exist_fields)) {
                $this->dbforge->drop_column($this->tblName, $value);
            }
        }
    }

}