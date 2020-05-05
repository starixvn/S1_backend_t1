<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_metadata_setting_20180423 extends CI_Migration {

    private $tableName = 'metadata_setting';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'uri_string' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'title' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'keyword' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'description' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'created_by' => array(
				'type' => 'INT',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'updated_by' => array(
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
