<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 *
 */
class Migration_Add_icon_to_payment_account_and_sites_201510231403 extends CI_Migration {

	private $tableName = 'payment_account';

	public function up() {
		$fields = array(
			'account_icon_filepath' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);

		$fields = array(
			'logo_icon_filepath' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'logo_icon_horizontal_filepath' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
		);
		$this->dbforge->add_column('static_sites', $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'account_icon_filepath');
		$this->dbforge->drop_column('static_sites', 'logo_icon_filepath');
	}
}

///END OF FILE/////