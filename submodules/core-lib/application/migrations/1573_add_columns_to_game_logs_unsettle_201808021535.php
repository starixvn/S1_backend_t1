<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_game_logs_unsettle_201808021535 extends CI_Migration {

	public function up() {

		if(!$this->db->field_exists('ip_address', 'game_logs_unsettle')){
			$addFields=[
				'ip_address' => [
	                'type' => 'VARCHAR',
	                'constraint' => '100',
	                'null' => true
	            ],
			];
	        $this->dbforge->add_column('game_logs_unsettle', $addFields);
		}

	}

	public function down() {
		//don't drop
	}

}
