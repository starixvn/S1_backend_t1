<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_externalcommontokens_201606270316 extends CI_Migration {

	public function up() {
		$fields = array(
			'game_platform_id' => array(
				'type' => 'int',
				'null' => true,
			),
		);
		$this->dbforge->add_column('external_common_tokens', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('external_common_tokens', 'game_platform_id');
	}
}