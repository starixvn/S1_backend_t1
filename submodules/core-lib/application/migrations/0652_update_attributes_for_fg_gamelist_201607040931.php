<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_attributes_for_fg_gamelist_201607040931 extends CI_Migration {
	private $tableName = 'game_description';
	public function up() {
		$data = array(
			'attributes' => 'NYX_CAS',
		);
		$this->db->where(array('attributes' => 'NYX_CAX'));
		$this->db->update($this->tableName, $data);
	}

	public function down() {
		# no rollback action required
	}
}