<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_vipsettingcashbackrule_201602220729 extends CI_Migration {

	private $tableName = 'vipsettingcashbackrule';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'bet_convert_rate' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'deposit_convert_rate' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'bet_convert_rate');
		$this->dbforge->drop_column($this->tableName, 'deposit_convert_rate');
	}
}

///END OF FILE//////////