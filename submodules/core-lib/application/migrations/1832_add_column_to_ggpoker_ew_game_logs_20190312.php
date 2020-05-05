<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_ggpoker_ew_game_logs_20190312 extends CI_Migration {

    private $tableName='ggpoker_ew_game_logs';

    public function up() {
        $fields = array(
            'profitAndLossPokerCashback' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'convertedProfitAndLossPokerCashback' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'profitAndLossPokerCashback');
        $this->dbforge->drop_column($this->tableName, 'convertedProfitAndLossPokerCashback');
    }
}