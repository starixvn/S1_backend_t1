<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_index_201511091221 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {
		$this->db->query('create index idx_total_player_game_year_player_id on total_player_game_year(player_id)');

		//Buffet Bonanza 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'buffetbonanza90', 'english_name' => "Buffet Bonanza 90", 'external_game_id' => "Buffet Bonanza 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.buffetbonanza90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Get Rocked 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'getrocked90', 'english_name' => "Get Rocked 90", 'external_game_id' => "Get Rocked 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.getrocked90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Path of the Penguin
		$this->db->insert($this->tableName, array(
			'game_code' => 'pathofthepenguin', 'english_name' => "Path of the Penguin", 'external_game_id' => "Path of the Penguin",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.pathofthepenguin', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Reel Gems 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'reelgems90', 'english_name' => "Reel Gems 90", 'external_game_id' => "Reel Gems 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.reelgems90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Wealthpa 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'wealthpa90', 'english_name' => "Wealthpa 90", 'external_game_id' => "Wealthpa 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.wealthpa90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Bars & Stripes 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'barsstripes90', 'english_name' => "Bars & Stripes 90", 'external_game_id' => "Bars & Stripes 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.barsstripes90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Alley Cats 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'alleycats90', 'english_name' => "Alley Cats 90", 'external_game_id' => "Alley Cats 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.alleycats90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//CashOccino 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'cashoccino90', 'english_name' => "CashOccino 90", 'external_game_id' => "CashOccino 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.cashoccino90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Big Top 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'bigtop90', 'english_name' => "Big Top 90", 'external_game_id' => "Big Top 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.bigtop90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Crazy Chameleons 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'crazychameleons90', 'english_name' => "Crazy Chameleons 90", 'external_game_id' => "Crazy Chameleons 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.crazychameleons90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Genies Gems
		$this->db->insert($this->tableName, array(
			'game_code' => 'geniesgems', 'english_name' => "Genies Gems", 'external_game_id' => "Genies Gems",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.geniesgems', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Thunderstruck 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'thunderstruck90', 'english_name' => "Thunderstruck 90", 'external_game_id' => "Thunderstruck 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.thunderstruck90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Hot Shot 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'hotshot90', 'english_name' => "Hot Shot 90", 'external_game_id' => "Hot Shot 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.hotshot90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Nutty Squirrel
		$this->db->insert($this->tableName, array(
			'game_code' => 'nuttysquirrel', 'english_name' => "Nutty Squirrel", 'external_game_id' => "Nutty Squirrel",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.nuttysquirrel', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Quest for Beer 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'questforbeer90', 'english_name' => "Quest for Beer 90", 'external_game_id' => "Quest for Beer 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.questforbeer90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Mystic Dreams 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'mysticdreams90', 'english_name' => "Mystic Dreams 90", 'external_game_id' => "Mystic Dreams 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.mysticdreams90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Celtic Crown 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'celticcrown90', 'english_name' => "Celtic Crown 90", 'external_game_id' => "Celtic Crown 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.celticcrown90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Love Potion 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'lovepotion90', 'english_name' => "Love Potion 90", 'external_game_id' => "Love Potion 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.lovepotion90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Lions Pride 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'lionspride90', 'english_name' => "Lions Pride 90", 'external_game_id' => "Lions Pride 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.lionspride90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Dance of the Masai 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'danceofthemasai90', 'english_name' => "Dance of the Masai 90", 'external_game_id' => "Dance of the Masai 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.danceofthemasai90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//The Great Galaxy Grab 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'thegreatgalaxygrab90', 'english_name' => "The Great Galaxy Grab 90", 'external_game_id' => "The Great Galaxy Grab 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.thegreatgalaxygrab90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Dragons Myth
		$this->db->insert($this->tableName, array(
			'game_code' => 'dragonsmyth', 'english_name' => "Dragons Myth", 'external_game_id' => "Dragons Myth",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.dragonsmyth', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

	}

	public function down() {
		$this->db->query('drop index idx_total_player_game_year_player_id on total_player_game_year');
		$codes = array('buffetbonanza90', 'getrocked90', 'pathofthepenguin', 'reelgems90', 'wealthpa90',
			'barsstripes90', 'alleycats90', 'cashoccino90', 'bigtop90', 'crazychameleons90',
			'geniesgems', 'thunderstruck90', 'hotshot90', 'nuttysquirrel', 'questforbeer90',
			'mysticdreams90', 'celticcrown90', 'lovepotion90', 'lionspride90', 'danceofthemasai90',
			'thegreatgalaxygrab90', 'dragonsmyth');
		$this->db->where_in('game_code', $codes);
		$this->db->where('game_platform_id', MG_API);
		$this->db->delete($this->tableName);

	}
}

///END OF FILE//////////