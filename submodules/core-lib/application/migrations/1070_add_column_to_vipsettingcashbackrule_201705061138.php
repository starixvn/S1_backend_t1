<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_vipsettingcashbackrule_201705061138 extends CI_Migration {

	public function up() {
		$fields = array(
			'can_cashback' => array(
				'type' => 'ENUM("true","false")',
				'default' => 'false',
				'null' => false,
			),
		);
		$this->dbforge->add_column('vipsettingcashbackrule', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('vipsettingcashbackrule', 'can_cashback');
	}
}