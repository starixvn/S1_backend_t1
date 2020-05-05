<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_md5_sum_to_rtg_game_logs_20181201 extends CI_Migration {

	public function up() {
		$fields = array(
			'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
			)
		);
		$this->dbforge->add_column('rtg_game_logs', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('rtg_game_logs', 'md5_sum');
	}
}
