<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_game_description_201510251808 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		$this->dbforge->modify_column($this->tableName, array(
			'no_cash_back' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
			'void_bet' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
		));

		$this->dbforge->add_column($this->tableName, array(
			'english_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
		));

		//copy english_name
		$this->lang->load('main', 'english');
		// $this->db->from($this->tableName);
		$qry = $this->db->get($this->tableName);
		$data = array();
		//set english_name
		$rows = $qry->result();
		foreach ($rows as $row) {
			$data[] = array(
				'id' => $row->id,
				'english_name' => lang($row->game_name),
			);
		}

		$this->db->update_batch($this->tableName, $data, 'id');

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'english_name');
	}
}