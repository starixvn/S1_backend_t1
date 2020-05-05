<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_mglapis_duplicate_game_type_201607210948 extends CI_Migration {
	private $tableName = 'game_type';
	public function up() {
		$data = array(
			'game_type_lang' => 'mglapis_table_premier_game',
		);
		$this->db->where(array('game_type' => 'MG-Lapis Table Premier Game'));
		$this->db->update($this->tableName, $data);
	}

	public function down() {
		# no rollback action required
	}
}