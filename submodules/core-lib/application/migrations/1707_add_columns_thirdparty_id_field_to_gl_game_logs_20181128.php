<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_thirdparty_id_field_to_gl_game_logs_20181128 extends CI_Migration {

	public function up() {
		$fields = array(
			'thirdparty_id' => array(
				'type' => 'BIGINT',
				'null' => true,
			),
			'bingo_time' => array(
				'type' => 'DATETIME',
                'null' => true,
			),
			'belong_date' => array(
				'type' => 'DATE',
                'null' => true,
			),
			'one_price' => array(
				'type' => 'BIGINT',
				'null' => true,
			),
			'animal_code_key' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('gl_game_logs', $fields);

		$fields_update = array(
			'pointinfo' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'scode' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'animal_code' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		); 
		$this->dbforge->modify_column('gl_game_logs', $fields_update);
	}

	public function down() {
		$this->dbforge->drop_column('gl_game_logs', 'thirdparty_id');
		$this->dbforge->drop_column('gl_game_logs', 'bingo_time');
		$this->dbforge->drop_column('gl_game_logs', 'belong_date');
		$this->dbforge->drop_column('gl_game_logs', 'one_price');
		$this->dbforge->drop_column('gl_game_logs', 'animal_code_key');

		$fields = array(
			'pointinfo' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'scode' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'animal_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
		);
		$this->dbforge->modify_column('gl_game_logs', $fields);
	}
}
