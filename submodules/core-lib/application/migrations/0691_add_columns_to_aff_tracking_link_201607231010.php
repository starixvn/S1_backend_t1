<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_aff_tracking_link_201607231010 extends CI_Migration {

	private $tableName = 'aff_tracking_link';

	public function up() {
		$fields = array(
			'tracking_source_code' => array(
				'type' => 'VARCHAR',
                'constraint' => '200',
				'null' => true,
			),
			'deleted_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
        );

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'tracking_source_code');
		$this->dbforge->drop_column($this->tableName, 'deleted_at');
	}
}
