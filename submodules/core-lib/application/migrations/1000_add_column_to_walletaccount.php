<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_walletaccount extends CI_Migration {

	public function up() {
		//change column
		$this->dbforge->modify_column('migrations', ['version'=>[
			'type'=>'INT'
		]]);

		$fields = array(
			'extra_info' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('walletaccount', $fields);

	}

	public function down() {
		$this->dbforge->drop_column('walletaccount', 'extra_info');
	}
}