<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_player_201607232011 extends CI_Migration {

	private $tableName = 'player';

	public function up() {
		$fields = array(
			'tracking_source_code' => array(
				'type' => 'VARCHAR',
                'constraint' => '200',
				'null' => true,
			),
			'tracking_code' => array(
				'type' => 'VARCHAR',
                'constraint' => '200',
				'null' => true,
			),
        );

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'tracking_source_code');
		$this->dbforge->drop_column($this->tableName, 'tracking_code');
	}
}
