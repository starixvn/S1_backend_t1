<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_promo_category_to_transactions_201511030943 extends CI_Migration {

	private $tableName = 'transactions';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'promo_category' => array(
				'type' => 'INT',
				'null' => true,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'promo_category');
	}
}
