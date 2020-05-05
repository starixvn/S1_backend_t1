<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_vipsettingcashbackrule_201606040214 extends CI_Migration {

	private $tableName = 'vipsettingcashbackrule';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'one_withdraw_only' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'one_withdraw_only');
	}
}

///END OF FILE//////////