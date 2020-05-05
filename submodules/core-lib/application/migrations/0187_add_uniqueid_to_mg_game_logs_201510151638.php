<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_uniqueid_to_mg_game_logs_201510151638 extends CI_Migration {

	private $tableName = 'mg_game_logs';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
		));

		$this->dbforge->modify_column($this->tableName, array(
			'row_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => false,
			),
			'session_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => false,
			),
			'response_result_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		));

		//mg
		$this->db->query('create unique index idx_uniqueid on mg_game_logs(uniqueid)');
		$this->db->query('create index idx_external_uniqueid on mg_game_logs(external_uniqueid)');
	}

	public function down() {

		//mg
		$this->db->query('drop index idx_pt_game_logs_uniqueid on mg_game_logs');
		$this->db->query('drop index idx_pt_game_logs_external_uniqueid on mg_game_logs');

		$this->dbforge->drop_column($this->tableName, 'account_image_filepath');
	}
}

///END OF FILE//////////