<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_player_201511030543 extends CI_Migration {

	private $tableName = 'player';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'external_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'point' => array(
				'type' => 'INT',
				'null' => true,
			),
			'approvedWithdrawCount' => array(
				'type' => 'INT',
				'null' => true,
			),
			'approvedWithdrawAmount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'codepass' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
		));

		$this->db->query('create index idx_external_id on player(external_id)');

		$this->dbforge->add_column('playerdetails', array(
			'temppass' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
		));

	}

	public function down() {
		$this->db->query('drop index idx_external_id on player');

		$this->dbforge->drop_column($this->tableName, 'approvedWithdrawAmount');
		$this->dbforge->drop_column($this->tableName, 'approvedWithdrawCount');
		$this->dbforge->drop_column($this->tableName, 'external_id');
		$this->dbforge->drop_column($this->tableName, 'point');
		$this->dbforge->drop_column($this->tableName, 'codepass');
		$this->dbforge->drop_column('playerdetails', 'temppass');
	}
}