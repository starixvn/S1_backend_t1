<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_withdrawal_condition_to_friendreferralsettings_20151109 extends CI_Migration {

	public function up() {
		$this->dbforge->add_column('friendreferralsettings'	, array(
			'withdrawalCondition' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column('friendreferralsettings', 'withdrawalCondition');
	}
}