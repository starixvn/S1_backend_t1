<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_bbin_game_name_in_game_description_201511120806 extends CI_Migration {
	const GAMETYPE_SPORT_GAMES = 33;
	const GAMETYPE_LOTTERY_GAMES = 34;
	const GAMETYPE_3DHALL_GAMES = 35;
	const GAMETYPE_LIVE_GAMES = 36;
	const GAMETYPE_CASINO_GAMES = 37;
	const FLAG_TRUE = 1;

	private $tableName = 'game_description';

	public function up() {
		$sys = $this->config->item('external_system_map');
		if (array_key_exists(BBIN_API, $sys)) {
			$this->db->trans_start();
			//fix typo error
			$data = array(
				array(
					'game_code' => '5021',
					'game_name' => 'bbin.7PK',
					'english_name' => '7PK',
				),
				array(
					'game_code' => '5023',
					'game_name' => 'bbin.7CardStudPoker',
				),
			);
			$this->db->update_batch($this->tableName, $data, 'game_code');

			//delete double game
			$this->db->delete($this->tableName, array('game_code' => 'LT', 'game_platform_id' => BBIN_API));

			//insert to game_description
			$data = array(
				array('game_platform_id' => BBIN_API,
					'game_type_id' => self::GAMETYPE_LOTTERY_GAMES,
					'game_name' => 'bbin.MarkSix',
					'english_name' => 'Mark Six',
					'external_game_id' => 'LT',
					'game_code' => 'LT',
					'flash_enabled' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'status' => self::FLAG_TRUE),
			);

			$this->db->insert_batch('game_description', $data);
			$this->db->trans_complete();
		}
	}

	public function down() {
		$codes = array('5021', '5023');
		$this->db->where_in('game_code', $codes);
		$this->db->where('game_platform_id', BBIN_API);
		$this->db->delete($this->tableName);
	}
}