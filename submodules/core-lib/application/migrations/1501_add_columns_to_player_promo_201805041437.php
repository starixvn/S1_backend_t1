<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_player_promo_201805041437 extends CI_Migration {

	private $tableName = 'playerpromo';

	public function up() {

		$fields = array(
			'finish_max_limit_withdrawal' => array(
				'type' => 'TINYINT',
				'null' => true,
				'default' => 0,
			),

		);

		$this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'finish_max_limit_withdrawal');
	}
}
