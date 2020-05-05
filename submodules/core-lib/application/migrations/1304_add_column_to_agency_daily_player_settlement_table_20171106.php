<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_daily_player_settlement_table_20171106 extends CI_Migration {

	public function up() {
		
		$fields = array(
			'winning_bets' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		);

		$this->dbforge->add_column('agency_daily_player_settlement', $fields);

		$fields['transactions'] = array(
			'type' => 'DOUBLE',
			'null' => false,
			'default' => 0,
		);

		$fields['admin'] = array(
			'type' => 'DOUBLE',
			'null' => false,
			'default' => 0,
		);

		$this->dbforge->add_column('agency_wl_settlement', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('agency_daily_player_settlement', 'winning_bets');
		$this->dbforge->drop_column('agency_wl_settlement', 'winning_bets');
		$this->dbforge->drop_column('agency_wl_settlement', 'transactions');
		$this->dbforge->drop_column('agency_wl_settlement', 'admin');
	}
	
}
