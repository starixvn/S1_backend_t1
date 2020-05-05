<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_transactions_201605071157 extends CI_Migration {

	private $tableName = 'transactions';

	public function up() {
		$this->load->model('transactions');
		//update trans_date, trans_year_month, trans_year
		$sql = <<<EOD
update transactions set trans_date=date(created_at),
trans_year_month=date_format(created_at,'%Y%m'), trans_year=date_format(created_at,'%Y')
where created_at is not null
EOD;
		$this->db->query($sql);

		//add column
		$this->dbforge->add_column($this->tableName, [
			'to_username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'from_username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
		]);

		$this->db->query('create index idx_to_username on transactions(to_username)');
		$this->db->query('create index idx_from_username on transactions(from_username)');
		//update username
		$type = Transactions::ADMIN;
		$sql = <<<EOD
update transactions set to_username=(select adminusers.username from adminusers where transactions.to_id=adminusers.userId)
where to_type={$type}
EOD;
		$this->db->query($sql);

		$sql = <<<EOD
update transactions set from_username=(select adminusers.username from adminusers where transactions.from_id=adminusers.userId)
where from_type={$type}
EOD;
		$this->db->query($sql);

		$type = Transactions::PLAYER;
		$sql = <<<EOD
update transactions set to_username=(select player.username from player where transactions.to_id=player.playerId)
where to_type={$type}
EOD;
		$this->db->query($sql);

		$sql = <<<EOD
update transactions set from_username=(select player.username from player where transactions.from_id=player.playerId)
where from_type={$type}
EOD;
		$this->db->query($sql);

		$type = Transactions::AFFILIATE;
		$sql = <<<EOD
update transactions set to_username=(select affiliates.username from affiliates where transactions.to_id=affiliates.affiliateId)
where to_type={$type}
EOD;
		$this->db->query($sql);

		$sql = <<<EOD
update transactions set from_username=(select affiliates.username from affiliates where transactions.from_id=affiliates.affiliateId)
where from_type={$type}
EOD;
		$this->db->query($sql);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'to_username');
		$this->dbforge->drop_column($this->tableName, 'from_username');
	}
}