<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_sms_queue_list_20171101 extends CI_Migration {

	private $tableName = 'sms_queue_list';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'sender_num' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'recipient_num' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
            ),
            'context' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => false,
            ),
            'cdt' => array(
				'type' => 'DATETIME',
   				'null' => true,
			),
			'isqueue' => array(
				'type' => 'INT',
   				'null' => true,
			),
			'status' => array(
				'type' => 'INT',
   				'null' => true,
			)
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
        $this->dbforge->drop_table($this->tableName, true);
	}
}