<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_index_to_game_provider_auth_201510161531 extends CI_Migration {

	public function up() {
		//delete duplicate username
		$sql = <<<EOD

		SELECT game_provider_id, login_name, min(id) as id FROM game_provider_auth
group by game_provider_id, login_name
having count(id)>1

EOD;

		$qry = $this->db->query($sql);
		if ($qry && $qry->num_rows() > 0) {
			$this->db->trans_start();
			$rlt = $qry->result();
			foreach ($rlt as $row) {
				$this->db->where('game_provider_id', $row->game_provider_id)
					->where('login_name', $row->login_name)
					->where('id !=', $row->id)
					->delete('game_provider_auth');
			}

			$this->db->trans_complete();
		}

		$this->db->query('create unique index idx_un_provider_id_login_name on game_provider_auth(game_provider_id,login_name)');
		$this->db->query('create index idx_login_name on game_provider_auth(login_name)');
		$this->db->query('create index idx_player_id on game_provider_auth(player_id)');
		$this->db->query('create index idx_external_account_id on game_provider_auth(external_account_id)');
	}

	public function down() {
		$this->db->query('drop index idx_un_provider_id_login_name on game_provider_auth');
		$this->db->query('drop index idx_login_name on game_provider_auth');
		$this->db->query('drop index idx_player_id on game_provider_auth');
		$this->db->query('drop index idx_external_account_id on game_provider_auth');
	}
}

///END OF FILE//////////