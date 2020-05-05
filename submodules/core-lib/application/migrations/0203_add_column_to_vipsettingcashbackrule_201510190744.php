<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_vipsettingcashbackrule_201510190744 extends CI_Migration {

	private $tableName = 'vipsettingcashbackrule';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'cashback_percentage' => array(
				'type' => 'INT',
				'null' => true,
			),
			'cashback_maxbonus' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'cashback_percentage');
		$this->dbforge->drop_column($this->tableName, 'cashback_maxbonus');
	}
}