<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_betmaster_game_reference_201706151640 extends CI_Migration {

	private $tableName = 'betmaster_game_reference';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'sport_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
				'null' => true,
			),
			'odds_type_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'odds_type_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'outcome' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'outcome_id' => array(
                'type' => 'INT',
                'null' => true,
			),
            'outcome_description' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
			'type_id' => array(
                'type' => 'INT',
                'null' => true,
			),
			'subtype_id' => array(
                'type' => 'INT',
                'null' => true,
			),

		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
