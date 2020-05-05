<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_bbin_game_code_in_game_description_201511290159 extends CI_Migration {
	const FLAG_TRUE = 1;
	private $tableName = 'game_description';

	public function up() {
		// $sys = $this->config->item('external_system_map');
		// if (array_key_exists(BBIN_API, $sys)) {
		$this->db->trans_start();
		//fix typo error
		$data = array(
			array(
				'game_code' => 'BJ3D',
				'game_name' => 'bbin.3DLotto',
			),
			array(
				'game_code' => 'PL3D',
				'game_name' => 'bbin.SportsLotto',
			),
			array(
				'game_code' => 'BB3D',
				'game_name' => 'bbin.BB3D',
			),
			array(
				'game_code' => 'BBKN',
				'game_name' => 'bbin.BBKeno',
			),
			array(
				'game_code' => 'SH3D',
				'game_name' => 'bbin.ShanghaiLotto',
			),
			array(
				'game_code' => 'CQSC',
				'game_name' => 'bbin.ChongqingLotto',
			),
			array(
				'game_code' => 'TJSC',
				'game_name' => 'bbin.TianjinLotto',
			),
		);
		$this->db->update_batch($this->tableName, $data, 'game_name');

		//insert to game_description
		$data = array(
			array('game_platform_id' => BBIN_API,
				'game_type_id' => 34,
				'game_name' => 'bbin.BBPK3',
				'english_name' => 'BB PK3',
				'external_game_id' => 'BBPK',
				'game_code' => 'BBPK',
				'flash_enabled' => self::FLAG_TRUE,
				'flag_show_in_site' => self::FLAG_TRUE,
				'status' => self::FLAG_TRUE),
			array('game_platform_id' => BBIN_API,
				'game_type_id' => 34,
				'game_name' => 'bbin.xinjiang',
				'english_name' => 'Xinjiang Lotto',
				'external_game_id' => 'XJSC',
				'game_code' => 'XJSC',
				'flash_enabled' => self::FLAG_TRUE,
				'flag_show_in_site' => self::FLAG_TRUE,
				'status' => self::FLAG_TRUE),
		);

		$this->db->insert_batch('game_description', $data);
		$this->db->trans_complete();
		// }
	}

	public function down() {
		$codes = array('BJ3D', 'PL3D', 'BB3D', 'BBKN', 'SH3D', 'CQSC', 'TJSC', 'BBPK', 'XJSC');
		$this->db->where_in('game_code', $codes);
		$this->db->where('game_platform_id', BBIN_API);
		$this->db->delete($this->tableName);
	}
}