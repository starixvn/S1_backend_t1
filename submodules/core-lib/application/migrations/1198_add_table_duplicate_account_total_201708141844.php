<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_duplicate_account_total_201708141844 extends CI_Migration {

    private $tableName = 'duplicate_account_total';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'playerid' => array(
                'type' => 'INT',
                'null' => false
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'total_rate' => array(
                'type' => 'INT',
                'null' => false
            ),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
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
