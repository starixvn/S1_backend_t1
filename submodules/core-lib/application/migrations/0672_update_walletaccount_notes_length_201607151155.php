<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_walletaccount_notes_length_201607151155 extends CI_Migration {

	public function up() {
		$fields = array(
			'notes' => array(
				'type' => 'varchar',
				'constraint' => '2000',
			),
		);

		$this->dbforge->modify_column('walletaccount', $fields);
	}

	public function down() {

	}
}