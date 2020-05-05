<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_total_cashback_player_game_daily_201804021813 extends CI_Migration {

    private $tableName = 'total_cashback_player_game_daily';

    public function up() {

        if ( ! $this->db->field_exists('cashback_type', $this->tableName)) {
            $fields = array(
                'cashback_type' => array(
                    'type' => 'INT',
                    'default' => 1, // 1-normal cashback   2-friend referral
                    'null' => false,
                ),
            );
            $this->dbforge->add_column($this->tableName, $fields);
        }

    }

    public function down() {

        if ($this->db->field_exists('cashback_type', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'cashback_type');
        }

    }

}
