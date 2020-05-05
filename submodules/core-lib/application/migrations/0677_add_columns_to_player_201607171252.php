<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_player_201607171252 extends CI_Migration {

	public function up() {
		//drop main_balance and total_balance
		$this->dbforge->drop_column('player', 'main_balance');
		$this->dbforge->drop_column('player', 'total_balance');

		//use old frozen as main frozen
		$fields = array(
			'main_real' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'main_bonus' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'main_cashback' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'main_win_real' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'main_win_bonus' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'main_withdrawable' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'main_total_nofrozen' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'main_total' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'total_real' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'total_bonus' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'total_cashback' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'total_win_real' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'total_win_bonus' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'total_withdrawable' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'total_frozen' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'total_total_nofrozen' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'total_total' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
		);


		$this->dbforge->add_column('player', $fields);

	}

	public function down() {
		//keep old frozen
		$this->dbforge->drop_column('player', 'main_real');
		$this->dbforge->drop_column('player', 'main_bonus');
		$this->dbforge->drop_column('player', 'main_cashback');
		$this->dbforge->drop_column('player', 'main_win_real');
		$this->dbforge->drop_column('player', 'main_win_bonus');
		$this->dbforge->drop_column('player', 'main_withdrawable');
		$this->dbforge->drop_column('player', 'main_total_nofrozen');
		$this->dbforge->drop_column('player', 'main_total');

		$this->dbforge->drop_column('player', 'total_real');
		$this->dbforge->drop_column('player', 'total_bonus');
		$this->dbforge->drop_column('player', 'total_cashback');
		$this->dbforge->drop_column('player', 'total_win_real');
		$this->dbforge->drop_column('player', 'total_win_bonus');
		$this->dbforge->drop_column('player', 'total_withdrawable');
		$this->dbforge->drop_column('player', 'total_frozen');
		$this->dbforge->drop_column('player', 'total_total_nofrozen');
		$this->dbforge->drop_column('player', 'total_total');

		$fields = array(
			'total_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'main_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		);

		$this->dbforge->add_column('player', $fields);

	}

}