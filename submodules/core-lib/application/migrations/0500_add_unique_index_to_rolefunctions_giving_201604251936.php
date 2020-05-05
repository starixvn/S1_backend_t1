<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unique_index_to_rolefunctions_giving_201604251936 extends CI_Migration {

	public function up() {
		//delete duplicate username
		$sql = <<<EOD

SELECT roleId,funcId, max(id) as id FROM rolefunctions_giving
group by roleId,funcId
having count(id)>1

EOD;

		$qry = $this->db->query($sql);
		if ($qry && $qry->num_rows() > 0) {
			// $this->db->trans_start();
			$rlt = $qry->result();
			foreach ($rlt as $row) {
				$this->db->where('roleId', $row->roleId)
					->where('funcId', $row->funcId)
					->where('id !=', $row->id)
					->delete('rolefunctions_giving');
			}

			// $this->db->trans_complete();
		}

		$this->db->query('create unique index idx_roleid_funcid on rolefunctions_giving(roleId, funcId)');
	}

	public function down() {
		$this->db->query('drop index idx_roleid_funcid on rolefunctions_giving');
	}
}

///END OF FILE//////////