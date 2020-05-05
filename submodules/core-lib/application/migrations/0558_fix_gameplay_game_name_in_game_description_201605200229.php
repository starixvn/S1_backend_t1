<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_fix_gameplay_game_name_in_game_description_201605200229 extends CI_Migration {
	private $tableName = 'game_description';
	public function up() {
		$this->db->trans_start();
		//fix typo error
		$data = array(
			array(
				'game_name' => 'gameplay.4beauties',
				'english_name' => '4 beauties',
			),
			array(
				'game_name' => 'gameplay.qixi',
				'english_name' => 'Qixi',
			),
			array(
				'game_name' => 'gameplay.sevenwonders',
				'english_name' => 'Seven Wonders',
			),
			array(
				'game_name' => 'gameplay.kpop',
				'english_name' => 'Kpop',
			),
			array(
				'game_name' => 'gameplay.blackjack',
				'english_name' => 'Black Jack Single',
			),
			array(
				'game_name' => 'gameplay.bikinibeach_hd',
				'english_name' => 'Bikini Beach',
			),
			array(
				'game_name' => 'gameplay.forbiddenchamber',
				'english_name' => 'Forbidden Chamber',
			),
			array(
				'game_name' => 'gameplay.florasecret',
				'english_name' => 'Flora Secret',
			),
			array(
				'game_name' => 'gameplay.nutcracker',
				'english_name' => 'Nutcracker',
			),
			array(
				'game_name' => 'gameplay.piratestreasure',
				'english_name' => 'Pirates Treasure',
			),
			array(
				'game_name' => 'gameplay.monkeyking_hd',
				'english_name' => 'Monkey King',
			),
			array(
				'game_name' => 'gameplay.redchamber',
				'english_name' => 'Red Chamber',
			),
			array(
				'game_name' => 'gameplay.threekingdoms_hd',
				'english_name' => 'Three Kingdoms',
			),
		);
		$this->db->update_batch($this->tableName, $data, 'game_name');

		$data = array(
			array(
				'game_name' => 'gameplay.games.102',
				'game_code' => '102',
			),
		);
		$this->db->update_batch($this->tableName, $data, 'game_name');

		$this->db->trans_complete();
	}

	public function down() {
		$this->db->trans_start();
		//fix typo error
		$data = array(
			array(
				'game_name' => 'gameplay.4beauties',
				'english_name' => 'Four beauties',
			),
			array(
				'game_name' => 'gameplay.qixi',
				'english_name' => 'Qi Xi',
			),
			array(
				'game_name' => 'gameplay.sevenwonders',
				'english_name' => '7 Wonders',
			),
			array(
				'game_name' => 'gameplay.kpop',
				'english_name' => 'K-Pop',
			),
			array(
				'game_name' => 'gameplay.blackkack',
				'english_name' => 'Blackjack',
			),
			array(
				'game_name' => 'gameplay.bikinibeach_hd',
				'english_name' => 'Bikini Beach HD',
			),
			array(
				'game_name' => 'gameplay.forbiddenchamber',
				'english_name' => 'The Forbidden Chamber',
			),
			array(
				'game_name' => 'gameplay.florasecret',
				'english_name' => 'Flora\'s Secret',
			),
			array(
				'game_name' => 'gameplay.nutcracker',
				'english_name' => 'The Nutcracker',
			),
			array(
				'game_name' => 'gameplay.piratestreasure',
				'english_name' => 'Pirate\'s Treasure',
			),
			array(
				'game_name' => 'gameplay.monkeyking_hd',
				'english_name' => 'The Monkey King HD',
			),
			array(
				'game_name' => 'gameplay.redchamber',
				'english_name' => 'The Red Chamber',
			),
			array(
				'game_name' => 'gameplay.threekingdoms_hd',
				'english_name' => 'Three Kingdoms HD',
			),
		);
		$this->db->update_batch($this->tableName, $data, 'game_name');
		$this->db->trans_complete();
	}
}