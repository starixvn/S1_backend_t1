<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_deleted_to_vipsettings_201606061309 extends CI_Migration {

	public function up() {
		$fields = array(
			'deleted' => array(
				'type' => 'INT',
				'constraint' => 1,
				'default' => '0',
			),
		);
		$this->dbforge->add_column('vipsetting', $fields);
		$this->dbforge->add_column('vipsetting_cashback_game', $fields);
		$this->dbforge->add_column('vipsettingcashbackbonuspergame', $fields);
		$this->dbforge->add_column('vipsettingcashbackrule', $fields);
		// echo $this->db->last_query();
	}

	public function down() {
		$this->dbforge->drop_column('vipsetting', 'deleted');
		$this->dbforge->drop_column('vipsetting_cashback_game', 'deleted');
		$this->dbforge->drop_column('vipsettingcashbackbonuspergame', 'deleted');
		$this->dbforge->drop_column('vipsettingcashbackrule', 'deleted');
	}
}