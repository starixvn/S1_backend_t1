<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_lang_to_queue_result_201705190010 extends CI_Migration {

	private $tableName = 'queue_results';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'lang' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
		]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'lang');
	}
}
