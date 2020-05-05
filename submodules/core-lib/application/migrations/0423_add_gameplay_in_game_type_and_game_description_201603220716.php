<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_gameplay_in_game_type_and_game_description_201603220716 extends CI_Migration {
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;
	private $tableName = 'game_description';

	public function up() {
		$sys = $this->config->item('external_system_map');
		$this->db->trans_start();

		//insert to game_description
		$data = array(
			array(
				'game_type' => 'Table',
				'game_type_lang' => 'gameplay_table',
				'status' => self::FLAG_TRUE,
				'flag_show_in_site' => self::FLAG_TRUE,
				'game_description_list' => array(
					array('game_name' => 'gameplay.games.1',
						'english_name' => 'Commision Baccarat 1',
						'external_game_id' => '1',
						'game_code' => '1',
					),

					array('game_name' => 'gameplay.games.2',
						'english_name' => 'Commision Baccarat 2',
						'external_game_id' => '2',
						'game_code' => '2',
					),

					array('game_name' => 'gameplay.games.3',
						'english_name' => 'Commision Baccarat 3',
						'external_game_id' => '3',
						'game_code' => '3',
					),

					array('game_name' => 'gameplay.games.101',
						'english_name' => 'NC Baccarat 1',
						'external_game_id' => '101',
						'game_code' => '101',
					),

					array('game_name' => 'gameplay.games.102',
						'english_name' => 'NC Baccarat 2',
						'external_game_id' => '101',
						'game_code' => '101',
					),

					array('game_name' => 'gameplay.games.103',
						'english_name' => 'NC Baccarat 3',
						'external_game_id' => '103',
						'game_code' => '103',
					),

					array('game_name' => 'gameplay.games.4',
						'english_name' => 'Dragon Tiger',
						'external_game_id' => '4',
						'game_code' => '4',
					),

					array('game_name' => 'gameplay.games.5',
						'english_name' => 'Sicbo ',
						'external_game_id' => '5',
						'game_code' => '5',
					),

					array('game_name' => 'gameplay.games.6',
						'english_name' => 'Roulette ',
						'external_game_id' => '6',
						'game_code' => '6',
					),

					array('game_name' => 'gameplay.games.7',
						'english_name' => 'Seven Up Baccarat',
						'external_game_id' => '7',
						'game_code' => '7',
					),

					array('game_name' => 'gameplay.games.8',
						'english_name' => '3 Pictures',
						'external_game_id' => '8',
						'game_code' => '8',
					),

					array('game_name' => 'gameplay.games.9',
						'english_name' => 'Super Color Sicbo',
						'external_game_id' => '9',
						'game_code' => '9',
					),

					array('game_name' => 'gameplay.games.10',
						'english_name' => 'Blackjack ',
						'external_game_id' => '10',
						'game_code' => '10',
					),

					array('game_name' => 'gameplay.games.11',
						'english_name' => 'Tambola',
						'external_game_id' => '11',
						'game_code' => '11',
					),

					array('game_name' => 'gameplay.games.12',
						'english_name' => 'Super Fan Tan',
						'external_game_id' => '12',
						'game_code' => '12',
					),
				),
			),
		);

		$game_description_list = array();
		foreach ($data as $game_type) {
			$this->db->insert('game_type', array(
				'game_platform_id' => GAMEPLAY_API,
				'game_type' => $game_type['game_type'],
				'game_type_lang' => $game_type['game_type_lang'],
				'status' => $game_type['status'],
				'flag_show_in_site' => $game_type['flag_show_in_site'],
			));

			$game_type_id = $this->db->insert_id();
			foreach ($game_type['game_description_list'] as $game_description) {
				$game_description_list[] = array_merge(array(
					'game_platform_id' => GAMEPLAY_API,
					'game_type_id' => $game_type_id,
				), $game_description);
			}
		}

		$this->db->insert_batch('game_description', $game_description_list);
		$this->db->trans_complete();
	}

	public function down() {
		$this->db->trans_start();
		$this->db->delete('game_type', array('game_platform_id' => GAMEPLAY_API, 'game_type' => 'Table'));
		$this->db->delete('game_description', array('game_platform_id' => GAMEPLAY_API, 'game_type_id' => 69));
		$this->db->trans_complete();
	}
}