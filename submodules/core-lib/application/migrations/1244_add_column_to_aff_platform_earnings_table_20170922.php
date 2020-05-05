<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_aff_platform_earnings_table_20170922 extends CI_Migration {

	public function up() {

		$fields = array(
			'game_platform_bonus_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'game_platform_cashback_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'game_platform_transaction_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		);

		$this->dbforge->drop_column('affiliate_game_platform_earnings', 'game_platform_other_fees');
		$this->dbforge->add_column('affiliate_game_platform_earnings', $fields);
		$this->db->empty_table('affiliate_game_platform_earnings');

	}

	public function down() {

		$this->dbforge->drop_column('affiliate_game_platform_earnings', 'game_platform_bonus_fee');
		$this->dbforge->drop_column('affiliate_game_platform_earnings', 'game_platform_cashback_fee');
		$this->dbforge->drop_column('affiliate_game_platform_earnings', 'game_platform_transaction_fee');

		$fields = array(
			'game_platform_other_fees' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		);

		$this->dbforge->add_column('affiliate_game_platform_earnings', $fields);
	}
}
