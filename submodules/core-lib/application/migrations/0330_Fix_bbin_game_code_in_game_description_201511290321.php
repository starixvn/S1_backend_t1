<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_bbin_game_code_in_game_description_201511290321 extends CI_Migration {
	const FLAG_TRUE = 1;
	private $tableName = 'game_description';

	public function up() {
		$sys = $this->config->item('external_system_map');
		// if (array_key_exists(BBIN_API, $sys)) {
		$this->db->trans_start();
		//fix typo error
		$data = array(
			array(
				'game_code' => 'JXSC',
				'game_name' => 'bbin.JiangxiLotto',
			),
		);
		$this->db->update_batch($this->tableName, $data, 'game_name');

		$this->db->insert_batch('game_description', $data);
		$this->db->trans_complete();
		// }
	}

	public function down() {
		$codes = array('JXSC');
		$this->db->where_in('game_code', $codes);
		$this->db->where('game_platform_id', BBIN_API);
		$this->db->delete($this->tableName);
	}
}