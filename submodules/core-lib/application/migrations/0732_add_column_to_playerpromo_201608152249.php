<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_playerpromo_201608152249 extends CI_Migration {
	public function up() {
		$fields = array(
			'note' => array(
				'type' => 'TEXT',
				'null' => true
			),
			'betTimes' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
		);
		$this->dbforge->add_column('playerpromo', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('playerpromo', 'note');
		$this->dbforge->drop_column('playerpromo', 'betTimes');
	}
}
