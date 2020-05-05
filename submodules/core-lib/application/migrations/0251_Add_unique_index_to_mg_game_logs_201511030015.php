<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_unique_index_to_mg_game_logs_201511030015 extends CI_Migration {

	public function up() {
		//delete duplicate username
		$sql = <<<EOD

SELECT row_id, max(id) as id FROM mg_game_logs
group by row_id
having count(id)>1

EOD;

		$qry = $this->db->query($sql);
		if ($qry && $qry->num_rows() > 0) {
			// $this->db->trans_start();
			$rlt = $qry->result();
			foreach ($rlt as $row) {
				$this->db->where('row_id', $row->row_id)
					->where('id !=', $row->id)
					->delete('mg_game_logs');
			}

			// $this->db->trans_complete();
		}

		$this->db->query('drop index idx_row_id on mg_game_logs');
		$this->db->query('create unique index idx_row_id on mg_game_logs(row_id)');
	}

	public function down() {
		// $this->db->query('drop index idx_row_id on mg_game_logs');
	}
}

///END OF FILE//////////