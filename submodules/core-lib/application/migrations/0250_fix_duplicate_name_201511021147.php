<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_fix_duplicate_name_201511021147 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {
		$sql = <<<EOD
SELECT max(id) as id from game_description
where game_platform_id=6
group by external_game_id
having count(id)>1
EOD;

		$qry = $this->db->query($sql);

		$ids = array();
		foreach ($qry->result() as $row) {
			$ids[] = $row->id;
		}
		$this->db->set('external_game_id', 'concat(external_game_id," move")', false)
			->where_in('id', $ids);
		$this->db->update($this->tableName);

	}

	public function down() {
	}
}