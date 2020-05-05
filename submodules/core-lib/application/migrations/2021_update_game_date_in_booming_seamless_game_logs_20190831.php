<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_game_date_in_booming_seamless_game_logs_20190831 extends CI_Migration {
	private $tableName = 'boomingseamless_game_logs';

    public function up() {

        $update_fields = array(
	        'game_date' => array(
                'name' => 'game_date',
				'type' => 'DATETIME',
	        ),
        );

        if($this->db->field_exists('game_date', $this->tableName)) {
            $this->dbforge->modify_column($this->tableName, $update_fields); 
        }
    }

    public function down() {
    }
}
