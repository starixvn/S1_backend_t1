<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_external_uniqueid_to_isb_raw_game_logs_20190408 extends CI_Migration {

	public function up() {
		$this->dbforge->add_column('isb_raw_game_logs', array(
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			)
		));

        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex('isb_raw_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
	}

	public function down() {
	}
}