<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_scode_raw_to_gl_game_logs_20190625 extends CI_Migration {

	public function up() {
		$fields = array(
			'scode_raw' => array(
				'type' => 'text',
				'null' => TRUE,
			),
		);

		$this->dbforge->add_column('gl_game_logs', $fields);

	}

	public function down() {
		$this->dbforge->drop_column('gl_game_logs', 'scode_raw');
	}
}
