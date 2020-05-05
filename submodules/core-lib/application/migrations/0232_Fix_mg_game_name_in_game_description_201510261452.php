<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_mg_game_name_in_game_description_201510261452 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		$data = array(
			array(
				'game_code' => 'terminator2',
				'english_name' => 'Terminator II',
				'external_game_id' => 'Terminator II',
			),
			array(
				'game_code' => 'girlswithgunsfrozendawn',
				'english_name' => 'Girls With Guns II - Frozen Dawn',
				'external_game_id' => 'Girls With Guns II - Frozen Dawn',
			),
			array(
				'game_code' => 'avalon2',
				'english_name' => 'Bonus Game - Avalon II',
				'external_game_id' => 'Bonus Game - Avalon II',
			),
			array(
				'game_code' => 'untamedcrownedeagle',
				'english_name' => 'Untamed - Crowned Eagle',
				'external_game_id' => 'Untamed - Crowned Eagle',
			),
			array(
				'game_code' => 'immortalromance',
				'english_name' => 'Immortal Romance Video Slot',
				'external_game_id' => 'Immortal Romance Video Slot',
			),
			array(
				'game_code' => 'rubyburningdesirev90',
				'english_name' => 'Burning Desire 90',
				'external_game_id' => 'Burning Desire 90',
			),
			array(
				'game_code' => 'springbreak',
				'english_name' => 'Feature Slot-Spring Break',
				'external_game_id' => 'Feature Slot-Spring Break',
			),
			array(
				'game_code' => 'drwattsup',
				'english_name' => 'Dr Watts Up',
				'external_game_id' => 'Dr Watts Up',
			),
			array(
				'game_code' => 'rubydeckthehallsv90',
				'english_name' => 'Deck the Halls 90',
				'external_game_id' => 'Deck the Halls 90',
			),
			array(
				'game_code' => 'retroreelsdiamondglitz',
				'english_name' => 'Retro Reels - Diamond Glitz 2',
				'external_game_id' => 'Retro Reels - Diamond Glitz 2',
			),
			array(
				'game_code' => 'moonshinev90',
				'english_name' => 'Moonshine 90',
				'external_game_id' => 'Moonshine 90',
			),
			array(
				'game_code' => 'girlswithguns',
				'english_name' => 'Girls With Guns - Jungle Heat',
				'external_game_id' => 'Girls With Guns - Jungle Heat',
			),
			array(
				'game_code' => 'fatladysings',
				'english_name' => 'Fat Ladyings 90',
				'external_game_id' => 'Fat Ladyings 90',
			),
			array(
				'game_code' => 'fruits',
				'english_name' => 'Fruit',
				'external_game_id' => 'Fruit',
			),
			array(
				'game_code' => 'rubyfoamyfortunes',
				'english_name' => 'Scratch Card-Foamy Fortunes',
				'external_game_id' => 'Scratch Card-Foamy Fortunes',
			),
			array(
				'game_code' => 'fruitfiesta',
				'english_name' => 'Fruit Fiesta',
				'external_game_id' => 'Fruit Fiesta',
			),
			array(
				'game_code' => 'mermaidsmillions',
				'english_name' => 'Mermaids Million',
				'external_game_id' => 'Mermaids Million',
			),
			array(
				'game_code' => 'breakaway',
				'english_name' => 'Break Away 90',
				'external_game_id' => 'Break Away 90',
			),
			array(
				'game_code' => 'untamedbengaltiger',
				'english_name' => 'Untamed - Bengal Tiger',
				'external_game_id' => 'Untamed - Bengal Tiger',
			),
			array(
				'game_code' => 'whatonearth',
				'english_name' => 'What on Earth 90',
				'external_game_id' => 'What on Earth 90',
			),
			array(
				'game_code' => 'untamedgiantpanda',
				'english_name' => 'Untamed - Giant Panda',
				'external_game_id' => 'Untamed - Giant Panda',
			),
			array(
				'game_code' => 'untamedgiantpandav90',
				'english_name' => 'Untamed - Giant Panda 90',
				'external_game_id' => 'Untamed - Giant Panda 90',
			),
			array(
				'game_code' => 'cashsplash',
				'english_name' => 'Cash Splash',
				'external_game_id' => 'Cash Splash',
			),
			array(
				'game_code' => 'cashsplash5reel',
				'english_name' => 'Cash Splash 5 Reel',
				'external_game_id' => 'Cash Splash 5 Reel',
			),
			array(
				'game_code' => 'santaswildride',
				'english_name' => "Santa's Wild Ride",
				'external_game_id' => "Santa's Wild Ride",
			),
			array(
				'game_code' => 'tombraiderv90',
				'english_name' => 'Tomb Raider 90',
				'external_game_id' => 'Tomb Raider 90',
			),
			array(
				'game_code' => 'thunderstruckv90',
				'english_name' => 'Thunderstruck2 90',
				'external_game_id' => 'Thunderstruck2 90',
			),
			array(
				'game_code' => 'rubybigkahunasnakesandladders',
				'english_name' => 'Big Kahuna - Snakes and Ladders',
				'external_game_id' => 'Big Kahuna - Snakes and Ladders',
			),
			array(
				'game_code' => 'untamedcrownedeagle',
				'english_name' => 'Untamed - Crowned Eagle',
				'external_game_id' => 'Untamed - Crowned Eagle',
			),
			array(
				'game_code' => 'wheelofwealthsev90',
				'english_name' => 'Wheel of WealthE 90',
				'external_game_id' => 'Wheel of WealthE 90',
			),
			array(
				'game_code' => 'goldfactoryv90',
				'english_name' => 'Gold Factory 90',
				'external_game_id' => 'Gold Factory 90',
			),
			array(
				'game_code' => '5reeldrive',
				'english_name' => '5ReelDrive',
				'external_game_id' => '5ReelDrive',
			),
			array(
				'game_code' => 'majormillions5reel',
				'english_name' => 'Major Millions 5 Reel',
				'external_game_id' => 'Major Millions 5 Reel',
			),
			array(
				'game_code' => 'rubyvegassingledeckblackjackgold',
				'english_name' => 'Vegas Single Deck Blackjack GOLD',
				'external_game_id' => 'Vegas Single Deck Blackjack GOLD',
			),
			array(
				'game_code' => 'majormillions',
				'english_name' => 'Major Million',
				'external_game_id' => 'Major Million',
			),
			array(
				'game_code' => 'starscapev90',
				'english_name' => 'Starscape 90',
				'external_game_id' => 'Starscape 90',
			),
			array(
				'game_code' => 'sterlingsilver3d',
				'english_name' => 'Sterling Silver',
				'external_game_id' => 'Sterling Silver',
			),
		);

		$this->db->update_batch($this->tableName, $data, 'game_code');

		//delete same game code: rrqueenofhearts
		$this->db->delete($this->tableName, array('game_code' => 'rrqueenofhearts'));
		//insert
		$this->db->insert($this->tableName, array('game_code' => 'rrqueenofhearts', 'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.RRQueenOfHearts', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
			'english_name' => 'Rhyming Reels Queen of Hearts 96', 'external_game_id' => 'Rhyming Reels Queen of Hearts 96'));

		//Boogie Monsters
		$this->db->insert($this->tableName, array(
			'game_code' => 'boogiemonsters', 'english_name' => 'Boogie Monsters', 'external_game_id' => 'Boogie Monsters',
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.boogiemonsters', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Boogie Monsters 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'boogiemonstersv90', 'english_name' => 'Boogie Monsters 90', 'external_game_id' => 'Boogie Monsters 90',
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.boogiemonstersv90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Gold Ahoy
		$this->db->insert($this->tableName, array(
			'game_code' => 'goldahoy', 'english_name' => 'Gold Ahoy', 'external_game_id' => 'Gold Ahoy',
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.goldahoy', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Break da Bank Again
		$this->db->insert($this->tableName, array(
			'game_code' => 'breakdabankagain', 'english_name' => 'Break da Bank Again', 'external_game_id' => 'Break da Bank Again',
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.MSBreakDaBankAgain', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Golden Era
		$this->db->insert($this->tableName, array(
			'game_code' => 'goldenera', 'english_name' => 'Golden Era', 'external_game_id' => 'Golden Era',
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.goldenera', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Rhyming Reels Old King Cole 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'rhymingreelsoldkingcole90', 'english_name' => 'Rhyming Reels Old King Cole 90', 'external_game_id' => 'Rhyming Reels Old King Cole 90',
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.rhymingreelsoldkingcole90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Wild Catch
		$this->db->insert($this->tableName, array(
			'game_code' => 'wildcatch', 'english_name' => 'Wild Catch', 'external_game_id' => 'Wild Catch',
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.wildcatch', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//First Past the Post 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'firstpastthepostv90', 'english_name' => 'First Past the Post 90', 'external_game_id' => 'First Past the Post 90',
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.firstpastthepostv90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Sweet Harvest
		$this->db->insert($this->tableName, array(
			'game_code' => 'sweetharvest', 'english_name' => 'Sweet Harvest', 'external_game_id' => 'Sweet Harvest',
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.sweetharvest', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Dragon Lady 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'dragonladyv90', 'english_name' => 'Dragon Lady 90', 'external_game_id' => 'Dragon Lady 90',
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.dragonladyv90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Dragons Loot 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'dragonslootv90', 'english_name' => 'Dragons Loot 90', 'external_game_id' => 'Dragons Loot 90',
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.dragonslootv90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Wowpot
		$this->db->insert($this->tableName, array(
			'game_code' => 'wowpot', 'english_name' => 'Wowpot', 'external_game_id' => 'Wowpot',
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.wowpot', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Cricket Star
		$this->db->insert($this->tableName, array(
			'game_code' => 'cricketstar', 'english_name' => 'Cricket Star', 'external_game_id' => 'Cricket Star',
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.cricketstar', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Girls With Guns - Jungle Heat 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'girlswithgunsjungleheatv90', 'english_name' => 'Girls With Guns - Jungle Heat 90', 'external_game_id' => 'Girls With Guns - Jungle Heat 90',
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.girlswithgunsjungleheatv90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Age of Discovery 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'ageofdiscovery90', 'english_name' => 'Age of Discovery 90', 'external_game_id' => 'Age of Discovery 90',
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.ageofdiscovery90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Cash Splash 5
		$this->db->insert($this->tableName, array(
			'game_code' => 'cashsplash5', 'english_name' => 'Cash Splash 5', 'external_game_id' => 'Cash Splash 5',
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.cashsplash5', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Lion's Pride
		$this->db->insert($this->tableName, array(
			'game_code' => 'lionspride', 'english_name' => "Lion's Pride", 'external_game_id' => "Lion's Pride",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.lionspride', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Mystic Dreams 96
		$this->db->insert($this->tableName, array(
			'game_code' => 'mysticdreams96', 'english_name' => "Mystic Dreams 96", 'external_game_id' => "Mystic Dreams 96",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.mysticdreams96', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Party Island
		$this->db->insert($this->tableName, array(
			'game_code' => 'partyisland', 'english_name' => "Party Island", 'external_game_id' => "Party Island",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.partyisland', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Summer Holiday
		$this->db->insert($this->tableName, array(
			'game_code' => 'summerholiday', 'english_name' => "Summer Holiday", 'external_game_id' => "Summer Holiday",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.summerholiday', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Tomb Raider Secret of the Sword (Tomb Raider Secret of the Sword)
		$this->db->insert($this->tableName, array(
			'game_code' => 'tombraidersecretofthesword', 'english_name' => "Tomb Raider Secret of the Sword (Tomb Raider Secret of the Sword)", 'external_game_id' => "Tomb Raider Secret of the Sword (Tomb Raider Secret of the Sword)",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.tombraidersecretofthesword', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Treasure Palace
		$this->db->insert($this->tableName, array(
			'game_code' => 'treasurepalace', 'english_name' => "Treasure Palace", 'external_game_id' => "Treasure Palace",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.treasurepalace', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//American Roulette Gold
		$this->db->insert($this->tableName, array(
			'game_code' => 'americanroulettegold', 'english_name' => "American Roulette Gold", 'external_game_id' => "American Roulette Gold",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.americanroulettegold', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//European Roulette Advanced
		$this->db->insert($this->tableName, array(
			'game_code' => 'europeanrouletteadvanced', 'english_name' => "European Roulette Advanced", 'external_game_id' => "European Roulette Advanced",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.europeanrouletteadvanced', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
		//Beach Babes
		$this->db->insert($this->tableName, array(
			'game_code' => 'beachbabes', 'english_name' => "Beach Babes", 'external_game_id' => "Beach Babes",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.beachbabes', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

	}

	public function down() {
		$codes = array('rrqueenofhearts', 'boogiemonsters', 'boogiemonstersv90', 'goldahoy', 'breakdabankagain',
			'goldenera', 'rhymingreelsoldkingcole90', 'wildcatch', 'firstpastthepostv90', 'sweetharvest',
			'dragonladyv90', 'dragonslootv90', 'wowpot', 'cricketstar', 'girlswithgunsjungleheatv90', 'ageofdiscovery90',
			'cashsplash5', 'lionspride', 'mysticdreams96', 'partyisland', 'summerholiday',
			'tombraidersecretofthesword', 'treasurepalace', 'americanroulettegold', 'europeanrouletteadvanced',
			'beachbabes');
		$this->db->where_in('game_code', $codes);
		$this->db->where('game_platform_id', MG_API);
		$this->db->delete($this->tableName);
	}
}