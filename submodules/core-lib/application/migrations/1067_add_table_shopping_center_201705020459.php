<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_shopping_center_201705020459 extends CI_Migration {

	public function up() {
		$fields = array(
			"id" => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => true,
			),
			'shop_code' => array(
				'type' => 'VARCHAR',
				'constraint' => 64,
				'null' => true,
			),
			'title' => array(
				'type' => 'VARCHAR',
				'constraint' => 64,
				'null' => false,
			),
			'short_description' => array(
				'type' => 'VARCHAR',
				'constraint' => 300,
				'null' => true,
			),
			'details' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'requirements' => array(
				'type' => 'VARCHAR',
				'constraint' => 300,
				'null' => true,
			),

			'tag_as_new' => array(
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
			'created_by' => array(
				'type' => 'INT',
				'null' => true,
			),
			'updated_by' => array(
				'type' => 'INT',
				'null' => true,
			),
			'show_datetime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'hide_datetime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('shopping_center');

	}

	public function down() {
		$this->dbforge->drop_table('shopping_center');
	}
}