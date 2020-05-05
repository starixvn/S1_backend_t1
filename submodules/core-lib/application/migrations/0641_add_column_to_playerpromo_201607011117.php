<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_playerpromo_201607011117 extends CI_Migration {

	public function up() {
		$fields = array(
			'withdrawConditionAmount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
		);
		$this->dbforge->add_column('playerpromo', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('playerpromo', 'withdrawConditionAmount');
	}
}