<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_index_to_transactions_201510191226 extends CI_Migration {

	public function up() {
		$this->db->query('create index idx_payment_account_id on transactions(payment_account_id)');
		$this->db->query('create index idx_from_id on transactions(from_id)');
		$this->db->query('create index idx_to_id on transactions(to_id)');
		$this->db->query('create index idx_created_at on transactions(created_at)');
	}

	public function down() {
		$this->db->query('drop index idx_payment_account_id on transactions');
		$this->db->query('drop index idx_from_id on transactions');
		$this->db->query('drop index idx_to_id on transactions');
		$this->db->query('drop index idx_created_at on transactions');
	}
}

///END OF FILE//////////