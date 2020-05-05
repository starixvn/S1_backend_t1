<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_banktype_201511100134 extends CI_Migration {

	public function up() {
		$this->dbforge->add_column('banktype', array(
			'show_on_player' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 1,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column('banktype', 'show_on_player');
	}
}