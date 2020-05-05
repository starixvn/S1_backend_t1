<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_index_to_vipsetting_cashback_game_201511211913 extends CI_Migration {

	public function up() {
		$this->db->query('create index idx_game_description_id on vipsetting_cashback_game(game_description_id)');
		$this->db->query('create index idx_vipsetting_cashbackrule_id on vipsetting_cashback_game(vipsetting_cashbackrule_id)');
	}

	public function down() {
		$this->db->query('drop index idx_game_description_id on vipsetting_cashback_game');
		$this->db->query('drop index idx_vipsetting_cashbackrule_id on vipsetting_cashback_game');
	}
}

///END OF FILE//////////