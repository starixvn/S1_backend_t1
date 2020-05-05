<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_image_link_to_sale_orders_201510131621 extends CI_Migration {

	private $tableName = 'sale_orders';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'account_image_filepath' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'account_image_filepath');
	}
}
