<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unknown_game_type_and_game_description_for_ebet_mg_20170814 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	private $tableName = 'game_description';

	public function up() {
	

		$this->db->trans_start();

			$data = array(
				array(
					'game_type' => 'EBET MG UNKNOWN',
					'game_type_lang' => 'EBET MG UNKNOWN',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_FALSE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"EBET MG UNKNOWN GAME","2":"EBET MG 未知游戏"}',	
							'english_name' => 'EBET MG UNKNOWN GAME',
							'external_game_id' => 'unknown',
							'game_code' => 'unknown'
						)
					)
				)
			);

			$game_description_list = array();
			foreach ($data as $game_type) {
				$this->db->insert('game_type', array(
					'game_platform_id' => EBET_MG_API,
					'game_type' => $game_type['game_type'],
					'game_type_lang' => $game_type['game_type_lang'],
					'status' => $game_type['status'],
					'flag_show_in_site' => $game_type['flag_show_in_site'],
				));

				$game_type_id = $this->db->insert_id();
				foreach ($game_type['game_description_list'] as $game_description) {
					$game_description_list[] = array_merge(array(
						'game_platform_id' => EBET_MG_API,
						'game_type_id' => $game_type_id,
					), $game_description);
				}
			}

			$this->db->insert_batch('game_description', $game_description_list);

			$fields = array(
				'win_amnt' => array(
					'type' => 'DOUBLE',
					'null' => true,
				),
				'win_balance' => array(
					'type' => 'DOUBLE',
					'null' => true,
				),
				'win_transTime' => array(
					'type' => 'TIMESTAMP',
					'null' => true,
				),
			);

			$this->dbforge->add_column('ebetmg_game_logs', $fields);


		$this->db->trans_complete();
	
	}

	public function down() {
		$this->db->trans_start();
		$this->db->delete('game_type', array('game_platform_id' =>  EBET_MG_API));
		$this->db->delete('game_description', array('game_platform_id' =>  EBET_MG_API));

		$this->dbforge->drop_column('ebetmg_game_logs', 'win_amnt');
		$this->dbforge->drop_column('ebetmg_game_logs', 'win_balance');
		$this->dbforge->drop_column('ebetmg_game_logs', 'win_transTime');
		$this->db->trans_complete();
	}
}