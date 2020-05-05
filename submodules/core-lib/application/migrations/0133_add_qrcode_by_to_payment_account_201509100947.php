<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * OG-698
 *
 *
 */
class Migration_Add_qrcode_by_to_payment_account_201509100947 extends CI_Migration {

	private $tableName = 'payment_account';

	public function up() {
		$fields = array(
			'account_image_filepath' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'account_image_filepath');
	}
}

///END OF FILE/////