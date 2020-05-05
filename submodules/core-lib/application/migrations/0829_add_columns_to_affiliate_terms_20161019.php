<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_affiliate_terms_20161019 extends CI_Migration {

	private $tableName = 'affiliate_terms';

	public function up() {
		$fields = array(
			'operator_settings' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
			'commission_setup' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
			'sub_affiliate_settings' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
        );

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'operator_settings');
		$this->dbforge->drop_column($this->tableName, 'commission_setup');
		$this->dbforge->drop_column($this->tableName, 'sub_affiliate_settings');
	}
}
