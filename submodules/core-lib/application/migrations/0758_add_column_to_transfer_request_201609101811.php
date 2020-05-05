<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_transfer_request_201609101811 extends CI_Migration {

	public function up() {
		$fields = array(
			'external_transaction_id' => array(
				'type' => 'VARCHAR',
				'constraint'=>'200',
				'null' => true,
			),
		);
		$this->dbforge->add_column('transfer_request', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('transfer_request', 'external_transaction_id');
	}

}
