<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_transaction_notes_table_201608290240 extends CI_Migration {

	private $tableName = 'transaction_notes';

	public function up() {
		$fields=array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'note' => array(
                'type' => 'VARCHAR',
                'constraint' => '4000',
                'null' => false,
            ),
            'create_date' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
			'transaction' => array(
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => false,
            ),
            'transaction_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => false,
            ),
            'admin_user_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => false,
            ),
            'before_status' => array(
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => true,
            ),
            'after_status' => array(
                'type' => 'VARCHAR',
                'constraint' => '45',
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
