<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_depositSuccesionPeriod_to_promorules extends CI_Migration {

	public function up() {
		$fields = array(
			'depositSuccesionPeriod' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('promorules', $fields, 'depositSuccesionCnt');
	}

	public function down() {
		$this->dbforge->drop_column('promorules', 'depositSuccesionPeriod');
	}
}