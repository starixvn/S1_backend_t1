<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_set_duplicated_mg_clientid_to_null_201511141834 extends CI_Migration {

	public function up() {
		$this->db->where('clientid', 'null')
			->update('game_description', array('clientid' => null));
		$this->db->where('moduleid', 'null')
			->update('game_description', array('moduleid' => null));
		//delete duplicate clientid
		$sql = <<<EOD

SELECT clientid, moduleid, min(id) as id FROM game_description
where game_platform_id=?
group by moduleid,clientid
having count(id)>1

EOD;

		$qry = $this->db->query($sql, array(MG_API));
		if ($qry && $qry->num_rows() > 0) {
			$this->db->trans_start();
			$rlt = $qry->result();
			foreach ($rlt as $row) {
				$this->db->where('clientid', $row->clientid)
					->where('moduleid', $row->moduleid)
					->where('game_platform_id', MG_API)
					->where('id !=', $row->id)
					->update('game_description', array('clientid' => null, 'moduleid' => null));
			}

			$this->db->trans_complete();
		}

		$this->db->query('create unique index idx_un_pcmid on game_description(game_platform_id,clientid,moduleid)');
	}

	public function down() {
		$this->db->query('drop index idx_un_pcmid on game_description');
	}
}

///END OF FILE//////////