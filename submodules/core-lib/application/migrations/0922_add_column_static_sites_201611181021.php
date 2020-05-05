<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_static_sites_201611181021 extends CI_Migration {

	private $tableName = 'static_sites';

	public function up() {
		$fields = array(
			'fav_icon_filepath' => array(
				'type' => 'VARCHAR',
				'constraint'=>'200',
				'null' => true
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'fav_icon_filepath');

	}
}
