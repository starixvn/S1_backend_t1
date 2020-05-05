<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_total_cashback_player_game_daily_201804190421 extends CI_Migration {

    private $tableName = 'total_cashback_player_game_daily';

    public function up() {

        // apply only for referral cashback
        // player_id will be referrer who will recieve the cashback
        if ( ! $this->db->field_exists('invited_player_id', $this->tableName)) {
            $fields = array(
                'invited_player_id' => array(
                    'type' => 'INT',
                    'null' => true,
                ),
            );
            $this->dbforge->add_column($this->tableName, $fields);
        }

    }

    public function down() {

        if ($this->db->field_exists('invited_player_id', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'invited_player_id');
        }

    }

}
