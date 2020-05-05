<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_transfer_request_201707261827 extends CI_Migration {

	private $tableName = 'transfer_request';

	public function up() {
		$fields = array(
			'pending_main_wallet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'pending_main_wallet_amount');
	}
}

////END OF FILE////