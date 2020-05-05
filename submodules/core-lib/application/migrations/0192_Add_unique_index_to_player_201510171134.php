<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_unique_index_to_player_201510171134 extends CI_Migration {

	public function up() {
		//delete duplicate username
		$sql = <<<EOD

		SELECT username, min(playerId) as id FROM player
group by username
having count(playerId)>1

EOD;

		$qry = $this->db->query($sql);
		if ($qry && $qry->num_rows() > 0) {
			$this->db->trans_start();
			$rlt = $qry->result();
			foreach ($rlt as $row) {
				$this->db->where('username', $row->username)
					->where('playerId !=', $row->playerId)
					->delete('player');
			}

			$this->db->trans_complete();
		}

		$this->db->query('create unique index idx_un_username on player(username)');
	}

	public function down() {
		$this->db->query('drop index idx_un_username on player');
	}
}

///END OF FILE//////////