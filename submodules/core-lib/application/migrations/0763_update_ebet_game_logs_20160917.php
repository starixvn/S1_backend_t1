<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_ebet_game_logs_20160917 extends CI_Migration {

	public function up() {

		$fields = array(
			'origCreateTime' => array(
				'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
			),
			'origPayoutTime' => array(
				'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
			),
		);
		
		$this->dbforge->add_column('ebet_game_logs', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('ebet_game_logs', 'origCreateTime');
		$this->dbforge->drop_column('ebet_game_logs', 'origPayoutTime');
	}



}