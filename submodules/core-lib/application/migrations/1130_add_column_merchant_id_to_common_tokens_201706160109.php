<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_merchant_id_to_common_tokens_201706160109 extends CI_Migration {

	public function up() {
		$fields = array(
			'merchant_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		);

		$this->dbforge->add_column('common_tokens', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('common_tokens', 'merchant_id');
	}
}
