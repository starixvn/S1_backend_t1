<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_default_payment_flag_to_banktype_201511121242 extends CI_Migration {

	private $tableName = 'banktype';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'default_payment_flag' => array(
				'type' => 'INT',
				'null' => true,
				'default' => MANUAL_ONLINE_PAYMENT,
			),
		));

		$this->db->query('update banktype set default_payment_flag=' . AUTO_ONLINE_PAYMENT . ' where external_system_id is not null');

	}

	public function down() {

		$this->dbforge->drop_column($this->tableName, 'default_payment_flag');
	}
}