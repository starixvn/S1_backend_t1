<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_vipsettingcashbackrule_201705120503 extends CI_Migration {

	public function up() {
		$fields = array(
			'bonus_mode_birthday' => array(
				'type' => 'INT',
				'null' => TRUE,
			),
			'birthday_bonus_amount' => array(
				'type' => 'DOUBLE',
				'null' => TRUE,
				'default' => 0,
			),
			'birthday_bonus_expiration_datetime' => array(
				'type' => 'DATETIME',
				'null' => TRUE,
			),
		);

		$this->dbforge->add_column('vipsettingcashbackrule', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('vipsettingcashbackrule', 'bonus_mode_birthday');
		$this->dbforge->drop_column('vipsettingcashbackrule', 'birthday_bonus_amount');
		$this->dbforge->drop_column('vipsettingcashbackrule', 'birthday_bonus_expiration_datetime');
	}
}
