<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_cms_news_category_20171024 extends CI_Migration {

	private $tableName = 'cmsnewscategory';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'userId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
            ),
            'language' => array(
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => false
            ),
            'date' => array(
				'type' => 'DATETIME',
   				'null' => true,
			),
			'update' => array(
				'type' => 'DATETIME',
   				'null' => true,
			)
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		if (!$this->db->table_exists($this->tableName)) {
			$this->dbforge->drop_table($this->tableName);
		}
	}
}