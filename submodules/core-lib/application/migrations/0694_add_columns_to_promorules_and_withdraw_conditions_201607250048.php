<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_promorules_and_withdraw_conditions_201607250048 extends CI_Migration {

	public function up() {
		$fields = array(
			'releaseToSubWallet' => array(
				'type' => 'INT',
				'null' => true,
			),
        );

		$this->dbforge->add_column('promorules', $fields);

		$fields = array(
			'promorules_json' => array(
				'type' => 'TEXT',
				'null' => true,
			),
        );

		$this->dbforge->add_column('withdraw_conditions', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('promorules', 'releaseToSubWallet');
		$this->dbforge->drop_column('withdraw_conditions', 'promorules_json');
	}
}
