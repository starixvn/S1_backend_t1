<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Rename_column_from_currentbalance_to_amount_changed_on_balanceadjustmenthistory extends CI_Migration {

	public function up() {
		$fields = array(
		        'currentBalance' => array(
		                'name' => 'amountChanged',
						'type' => 'DOUBLE',
						'null' => false,
		        ),
		);
		$this->dbforge->modify_column('balanceadjustmenthistory', $fields);
	}

	public function down() {
		$fields = array(
		        'amountChanged' => array(
		                'name' => 'currentBalance',
						'type' => 'DOUBLE',
						'null' => false,
		        ),
		);
		$this->dbforge->modify_column('balanceadjustmenthistory', $fields);
	}
}