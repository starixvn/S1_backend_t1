<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_unique_index_to_game_provider_auth_201510162028 extends CI_Migration {

	public function up() {
		//delete duplicate username
		$sql = <<<EOD

		SELECT game_provider_id, player_id, min(id) as id FROM game_provider_auth
group by game_provider_id, player_id
having count(id)>1

EOD;

		$qry = $this->db->query($sql);
		if ($qry && $qry->num_rows() > 0) {
			$this->db->trans_start();
			$rlt = $qry->result();
			foreach ($rlt as $row) {
				$this->db->where('game_provider_id', $row->game_provider_id)
					->where('player_id', $row->player_id)
					->where('id !=', $row->id)
					->delete('game_provider_auth');
			}

			$this->db->trans_complete();
		}

		$this->db->query('create unique index idx_un_provider_id_player_id on game_provider_auth(game_provider_id,player_id)');
	}

	public function down() {
		$this->db->query('drop index idx_un_provider_id_player_id on game_provider_auth');
	}
}

///END OF FILE//////////