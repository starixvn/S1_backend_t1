<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_aff_tracking_code_history_201606281646 extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'affiliate_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'user_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'tracking_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('aff_tracking_code_history');
	}

	public function down() {
		$this->dbforge->drop_table('aff_tracking_code_history');
	}
}
