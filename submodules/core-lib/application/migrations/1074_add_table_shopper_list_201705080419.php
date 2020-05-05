<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_shopper_list_201705080419 extends CI_Migration {

	public function up() {
		$fields = array(
			"id" => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => true,
			),
			'shopping_item_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'player_username' => array(
				'type' => 'VARCHAR',
				'constraint' => 64,
				'null' => false,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'required_points' => array(
				'type' => 'INT',
				'null' => true,
			),
			'status' => array(
				'type' => 'INT',
				'null' => true,
			),
			'notes' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'processed_by' => array(
				'type' => 'INT',
				'null' => true,
			),
			'application_datetime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'processed_datetime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('shopper_list');

	}

	public function down() {
		$this->dbforge->drop_table('shopper_list');
	}
}