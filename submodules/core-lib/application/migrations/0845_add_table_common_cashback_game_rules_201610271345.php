<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_common_cashback_game_rules_201610271345 extends CI_Migration {

	private $tableName = 'common_cashback_game_rules';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'game_description_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'percentage' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default'=>0,
			),
			'maxBonus' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default'=>0,
			),
			'game_platform_percentage' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default'=>0,
			),
			'game_type_percentage' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default'=>0,
			),
			'game_desc_percentage' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default'=>0,
			),
			'deleted' => array(
				'type' => 'INT',
				'constraint' => '1',
				'null' => true,
				'default'=>0,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);

		$this->db->query('create index idx_game_description_id on common_cashback_game_rules(game_description_id)');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
