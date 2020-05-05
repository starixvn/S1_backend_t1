<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_birthday_bonus_withdraw_condition_to_vipsettingcashbackrule_201706140545 extends CI_Migration {

	public function up() {
		$fields = array(
			'birthday_bonus_withdraw_condition' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		);

		$this->dbforge->add_column('vipsettingcashbackrule', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('vipsettingcashbackrule', 'birthday_bonus_withdraw_condition');
	}
}