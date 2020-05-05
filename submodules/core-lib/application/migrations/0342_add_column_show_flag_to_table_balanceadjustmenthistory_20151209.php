<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_show_flag_to_table_balanceadjustmenthistory_20151209 extends CI_Migration {

	public function up() {
		$fields = array(
			'show_flag' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
		);
		$this->dbforge->add_column('balanceadjustmenthistory', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('balanceadjustmenthistory', $this->up->fields);
	}
	
}
