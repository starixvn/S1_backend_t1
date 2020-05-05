<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_ab_game_logs_201802041142 extends CI_Migration {

	private $tableName = 'ab_game_logs';

	public function up() {
		$fields = array(

			'appType' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			);

		$this->dbforge->add_column($this->tableName, $fields);

        $fields = array(
                'commission' => array(
                        'type' => 'DOUBLE',
                        'null' => true,
                )
        );

        $this->dbforge->modify_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'appType');
	}
}
