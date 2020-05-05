<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_payment_account_201708230001 extends CI_Migration {

	private $tableName = 'payment_account';

	public function up() {
		$fields = array(
			'daily_deposit_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'total_deposit_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),			
		);

		if (!$this->db->field_exists('daily_deposit_amount', $this->tableName))
		{
			$this->dbforge->add_column($this->tableName, $fields);
		}
	}

	public function down() {
		if ($this->db->field_exists('daily_deposit_amount', $this->tableName))
		{
		$this->dbforge->drop_column($this->tableName, 'daily_deposit_amount');
		$this->dbforge->drop_column($this->tableName, 'total_deposit_amount');
		}
	}
}

////END OF FILE////
