<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_remove_column_playerid_from_png_stream_game_logs_201804061549 extends CI_Migration {

	public function up() {
		$this->dbforge->drop_column('png_stream_game_logs', 'PlayerId');
	}

	public function down() {
		$fields = array(
			'PlayerId' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			)
		);
		$this->dbforge->add_column('png_stream_game_logs', $fields);
	}
}