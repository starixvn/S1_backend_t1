<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_favorite_game_20170524 extends CI_Migration {

	private $tableName = 'favorite_game';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => false,
			),
			'image' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => false,
			),
			'url' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => false,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'game_type_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'game_description_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'created_at' => array(
				'type' => 'TIMESTAMP',
				'null' => false,
			),
			
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->tableName);
		$this->db->query('ALTER TABLE favorite_game ADD UNIQUE INDEX index2 (player_id ASC, url ASC)');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
