<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_bet_status_in_booming_seamless_game_logs_20190920 extends CI_Migration {

	private $tableName = 'boomingseamless_game_logs';

    public function up() {

        $fields = array(
            'bet_status' => array(
                'type' => 'BOOLEAN',
                'null' => true,
                'default' => 0,
            ),
        );

        if(!$this->db->field_exists('bet_status', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('bet_status', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'bet_status');
        }
    }
}