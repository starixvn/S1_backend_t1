<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_wallet_hold_to_affiliate_201606071407 extends CI_Migration {

	private $tableName = 'affiliates';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'wallet_hold' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
		]);

		// $this->load->model(array('roles'));
		// $this->roles->initFunction('affiliate_deposit_to_hold', 'Deposit to balance wallet', 134, 48, true);
		// $this->roles->initFunction('affiliate_withdraw_from_hold', 'Withdraw from balance wallet', 135, 48, true);
	}

	public function down() {
		$this->load->model(array('roles'));
		$this->dbforge->drop_column($this->tableName, 'wallet_hold');
		// $this->roles->deleteFunction(134);
		// $this->roles->deleteFunction(135);
	}
}