<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_kyc_status_20170404 extends CI_Migration {

	private $kycStatus = 'kyc_status';
	private $kycPlayer = 'kyc_player';

	public function up() {
		$fields = array(
			"id" => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => true,
			),
			'rate_code' => array(
				'type' => 'VARCHAR',
				'constraint' => 3,
				'null' => false,
			),
			'description_english' => array(
				'type' => 'VARCHAR',
				'constraint' => 150,
				'null' => true,
			),
			'description_chinese' => array(
				'type' => 'VARCHAR',
				'constraint' => 150,
				'null' => true,
			),
			'description_indonesian' => array(
				'type' => 'VARCHAR',
				'constraint' => 150,
				'null' => true,
			),
			'description_vietnamese' => array(
				'type' => 'VARCHAR',
				'constraint' => 150,
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

		$this->dbforge->create_table($this->kycStatus);

		//kyc_player
		$fields = array(
			"id" => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => true,
			),
			"player_id" => array(
				'type' => 'INT',
				'null' => false,
			),
			"kyc_status" => array(
				'type' => 'VARCHAR',
				'constraint' => 120,
				'null' => true,
			),
			"auto_generated" => array(
				'type' => 'INT',
				'default' => '0',
				'null' => false,
			),
			"generated_by" => array(
				'type' => 'INT',
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

		$this->dbforge->create_table($this->kycPlayer);

	}

	public function down() {
		$this->dbforge->drop_table($this->kycStatus);
		$this->dbforge->drop_table($this->kycPlayer);
	}
}