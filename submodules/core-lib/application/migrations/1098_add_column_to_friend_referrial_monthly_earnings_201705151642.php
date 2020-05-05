<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_friend_referrial_monthly_earnings_201705151642 extends CI_Migration {

	public function up() {
		$fields = array(
			'total_bets' => array(
				'type' => 'DOUBLE',
				'default' => 0,
			),

		);

		$this->dbforge->add_column('friend_referrial_monthly_earnings', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('friend_referrial_monthly_earnings', 'total_bets');
	}
}
