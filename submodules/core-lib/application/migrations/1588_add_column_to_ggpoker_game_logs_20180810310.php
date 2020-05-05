<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_ggpoker_game_logs_20180810310 extends CI_Migration {

    private $tableName = 'ggpoker_game_logs';

    public function up() {
        $fields = array(
            'rake_or_fee' => array(
                'type' => 'double',
                'null' => true,
            ),
            'profit_and_loss_poker' => array(
                'type' => 'double',
                'null' => true,
            ),
            'profit_and_loss_side_game' => array(
                'type' => 'double',
                'null' => true,
            ),
            'fish_buffet_reward' => array(
                'type' => 'double',
                'null' => true,
            ),
            'network_give_away' => array(
                'type' => 'double',
                'null' => true,
            ),
            'network_paid' => array(
                'type' => 'double',
                'null' => true,
            ),
            'brand_promotion' => array(
                'type' => 'double',
                'null' => true,
            ),
            'tournament_over_lay' => array(
                'type' => 'double',
                'null' => true,
            ),
            //converted data
            'converted_rake_or_fee' => array(
                'type' => 'double',
                'null' => true,
            ),
            'converted_profit_and_loss_poker' => array(
                'type' => 'double',
                'null' => true,
            ),
            'converted_profit_and_loss_side_game' => array(
                'type' => 'double',
                'null' => true,
            ),
            'converted_fish_buffet_reward' => array(
                'type' => 'double',
                'null' => true,
            ),
            'converted_network_give_away' => array(
                'type' => 'double',
                'null' => true,
            ),
            'converted_network_paid' => array(
                'type' => 'double',
                'null' => true,
            ),
            'converted_brand_promotion' => array(
                'type' => 'double',
                'null' => true,
            ),
            'converted_tournament_over_lay' => array(
                'type' => 'double',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'rake_or_fee');
        $this->dbforge->drop_column($this->tableName, 'profit_and_loss_poker');
        $this->dbforge->drop_column($this->tableName, 'profit_and_loss_side_game');
        $this->dbforge->drop_column($this->tableName, 'fish_buffet_reward');
        $this->dbforge->drop_column($this->tableName, 'network_give_away');
        $this->dbforge->drop_column($this->tableName, 'network_paid');
        $this->dbforge->drop_column($this->tableName, 'brand_promotion');
        $this->dbforge->drop_column($this->tableName, 'tournament_over_lay');
        //drop converted data
        $this->dbforge->drop_column($this->tableName, 'converted_rake_or_fee');
        $this->dbforge->drop_column($this->tableName, 'converted_profit_and_loss_poker');
        $this->dbforge->drop_column($this->tableName, 'converted_profit_and_loss_side_game');
        $this->dbforge->drop_column($this->tableName, 'converted_fish_buffet_reward');
        $this->dbforge->drop_column($this->tableName, 'converted_network_give_away');
        $this->dbforge->drop_column($this->tableName, 'converted_network_paid');
        $this->dbforge->drop_column($this->tableName, 'converted_brand_promotion');
        $this->dbforge->drop_column($this->tableName, 'converted_tournament_over_lay');
    }
}
