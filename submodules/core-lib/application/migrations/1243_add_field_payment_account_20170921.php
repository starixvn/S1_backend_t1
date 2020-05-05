<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_field_payment_account_20170921 extends CI_Migration {

	private $tableName = 'payment_account';

	public function up() {

		$dailyDeposit = array(
			'daily_deposit_amount' => array(
				'type' => 'double',
				'default' => 0
			)
			 
		);
		$totalDeposit = array(
		 
			'total_deposit_amount' => array(
				'type' => 'double',
				'default' => 0
			)
		);

		if (!$this->db->field_exists('daily_deposit_amount', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $dailyDeposit);
		}
		if (!$this->db->field_exists('total_deposit_amount', $this->tableName)) {
			$this->dbforge->add_column($this->tableName, $totalDeposit);	
		}
	
	}

    public function down(){
		if (!$this->db->field_exists('daily_deposit_amount', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, $dailyDeposit);
		}
		if (!$this->db->field_exists('total_deposit_amount', $this->tableName)) {
			$this->dbforge->drop_column($this->tableName, $totalDeposit);	
		}
    }
}