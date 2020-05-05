<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_balance_history_201604151249 extends CI_Migration {

	private $tableName = 'balance_history';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'total_balance' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		));

		# Change the field length first before making it an index
		$this->db->query('create index idx_player_id on balance_history(player_id)');
		$this->db->query('create index idx_aff_id on balance_history(aff_id)');
		$this->db->query('create index idx_transaction_id on balance_history(transaction_id)');
		$this->db->query('create index idx_created_at on balance_history(created_at)');
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'total_balance');
		$this->db->query('drop index idx_player_id on balance_history');
		$this->db->query('drop index idx_aff_id on balance_history');
		$this->db->query('drop index idx_transaction_id on balance_history');
		$this->db->query('drop index idx_created_at on created_at');
	}
}
