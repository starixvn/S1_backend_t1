<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_unique_index_to_role_function_201604251916 extends CI_Migration {

	public function up() {
		//delete duplicate username
		$sql = <<<EOD

SELECT roleId,funcId, max(id) as id FROM rolefunctions
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
					->delete('rolefunctions');
			}

			// $this->db->trans_complete();
		}

		$this->db->query('create unique index idx_roleid_funcid on rolefunctions(roleId, funcId)');
	}

	public function down() {
		$this->db->query('drop index idx_roleid_funcid on rolefunctions');
	}
}

///END OF FILE//////////