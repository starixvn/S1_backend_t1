<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_freespins_details_in_booming_seamless_game_logs_20190911 extends CI_Migration {
	private $tableName = 'boomingseamless_game_logs';

    public function up() {

        $update_fields = array(
	        'freespins_details' => array(
                'name' => 'freespins_details',
				'type' => 'TEXT',
	        ),
        );

        if($this->db->field_exists('freespins_details', $this->tableName)) {
            $this->dbforge->modify_column($this->tableName, $update_fields); 
        }
    }

    public function down() {
    }
}
