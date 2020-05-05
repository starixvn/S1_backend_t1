<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_game_maintenance_schedule_201811131425 extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'start_date' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'end_date' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'note' => array(
				'type' => 'VARCHAR',
				'constraint' => '1000',
				'null' => true,
			),
			'status' => array(
				'type' => 'tinyint',
				'constraint' => '4',
				'default' => '0',
				'comment' => '1 - Pending, 2 - In Maintenance, 3 - Maintenance Done, 4 - Cancelled',
				'null' => false,
			),
			'last_edit_user' => array(
				'type' => 'INT',
				'null' => true,
			),
			'created_by' => array(
                 'type' => 'INT',
                 'null' => false,
             ),
			'created_at ' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('created_by');
		$this->dbforge->add_key('updated_at');
		$this->dbforge->create_table('game_maintenance_schedule');
	}

	public function down() {
		$this->dbforge->drop_table('game_maintenance_schedule');
	}
}
