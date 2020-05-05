<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_uc_game_type_and_game_desc_and_unknown_201610271420 extends CI_Migration {
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;
	private $tableName = 'game_description';

	public function up() {


		// $this->db->trans_start();

		// 	//insert to game_description
		// 	$data = array(
		// 		array(
		// 			'game_type' => 'UC SLOTS',
		// 			'game_type_lang' => 'UC SLOTS',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_TRUE,
		// 			'game_description_list' => array(
		// 				array(
		// 					'game_name' => '_json:{"1":"Nuts Commande","2":"突击队"}',
		// 					'english_name' => 'Nuts Commander',
		// 					'external_game_id' => 'SlotMachine_NutsCommander',
		// 					'game_code' => 'SlotMachine_NutsCommander'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Terracota Wild","2":"兵马俑"}',
		// 					'english_name' => 'Terracota Wilds',
		// 					'external_game_id' => 'SlotMachine_TerracotaWilds',
		// 					'game_code' => 'SlotMachine_TerracotaWilds'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Precious treasures","2":"潜水世界"}',
		// 					'english_name' => 'Precious treasures',
		// 					'external_game_id' => 'SlotMachine_PreciousTreasures',
		// 					'game_code' => 'SlotMachine_PreciousTreasures'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Reel Fighters","2":"街头霸王"}',
		// 					'english_name' => 'Reel Fighters',
		// 					'external_game_id' => 'SlotMachine_ReelFighters',
		// 					'game_code' => 'SlotMachine_ReelFighters'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Live slot","2":"赌场欢乐"}',
		// 					'english_name' => 'Live slot',
		// 					'external_game_id' => 'SlotMachine_LiveSlot',
		// 					'game_code' => 'SlotMachine_LiveSlot'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Reel circus","2":"旋转马戏团"}',
		// 					'english_name' => 'Reel circus',
		// 					'external_game_id' => 'SlotMachine_ReelCircus',
		// 					'game_code' => 'SlotMachine_ReelCircus'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Shogun bots","2":"机械战将"}',
		// 					'english_name' => 'Shogun bots',
		// 					'external_game_id' => 'SlotMachine_ShogunBots',
		// 					'game_code' => 'SlotMachine_ShogunBots'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Diner of fortune","2":"招财饭馆"}',
		// 					'english_name' => 'Diner of fortune',
		// 					'external_game_id' => 'SlotMachine_DinerOfFortune',
		// 					'game_code' => 'SlotMachine_DinerOfFortune'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Strip to win","2":"脱衣舞男"}',
		// 					'english_name' => 'Strip to win',
		// 					'external_game_id' => 'SlotMachine_StripToWin',
		// 					'game_code' => 'SlotMachine_StripToWin'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Forbidden slot","2":"天使与魔鬼之恋"}',
		// 					'english_name' => 'Forbidden slot',
		// 					'external_game_id' => 'SlotMachine_ForbiddenSlot',
		// 					'game_code' => 'SlotMachine_ForbiddenSlot'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Steaming reels","2":"蒸汽朋克"}',
		// 					'english_name' => 'Steaming reels',
		// 					'external_game_id' => 'SlotMachine_SteamingReels',
		// 					'game_code' => 'SlotMachine_SteamingReels'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Wish list","2":"时尚心愿"}',
		// 					'english_name' => 'Wish list',
		// 					'external_game_id' => 'SlotMachine_WishList',
		// 					'game_code' => 'SlotMachine_WishList'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Wealth of monkeys","2":"幸运小猴"}',
		// 					'english_name' => 'Wealth of monkeys',
		// 					'external_game_id' => 'SlotMachine_WealthOfTheMonkey',
		// 					'game_code' => 'SlotMachine_WealthOfTheMonkey'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Loot a fruit","2":"抢掠果实"}',
		// 					'english_name' => 'Loot a fruit',
		// 					'external_game_id' => 'SlotMachine_LootAFruit',
		// 					'game_code' => 'SlotMachine_LootAFruit'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Pond of Koi","2":"锦鲤水池"}',
		// 					'english_name' => 'Pond of Koi',
		// 					'external_game_id' => 'SlotMachine_PondOfKoi',
		// 					'game_code' => 'SlotMachine_PondOfKoi'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Royal win","2":"皇家双赢"}',
		// 					'english_name' => 'Royal win',
		// 					'external_game_id' => 'SlotMachine_RoyalWin',
		// 					'game_code' => 'SlotMachine_RoyalWin'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Scattered skies","2":"宇宙乾坤"}',
		// 					'english_name' => 'Scattered skies',
		// 					'external_game_id' => 'SlotMachine_ScatteredSkies',
		// 					'game_code' => 'SlotMachine_ScatteredSkies'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Scattered to hell","2":"地狱妖怪"}',
		// 					'english_name' => 'Scattered to hell',
		// 					'external_game_id' => 'SlotMachine_ScatteredToHell',
		// 					'game_code' => 'SlotMachine_ScatteredToHell'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Amigos Fiesta","2":"友情盛宴"}',
		// 					'english_name' => 'Amigos Fiesta',
		// 					'external_game_id' => 'SlotMachine_AmigosFiesta',
		// 					'game_code' => 'SlotMachine_AmigosFiesta'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Toys of Joy","2":"娃娃国土"}',
		// 					'english_name' => 'Toys of Joy',
		// 					'external_game_id' => 'SlotMachine_ToysOfJoy',
		// 					'game_code' => 'SlotMachine_ToysOfJoy'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Surprising 7","2":"神妙 7"}',
		// 					'english_name' => 'Surprising 7',
		// 					'external_game_id' => 'SlotMachine_Surprising7',
		// 					'game_code' => 'SlotMachine_Surprising7'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Wild wild spin","2":"牛仔斡"}',
		// 					'english_name' => 'Wild wild spin',
		// 					'external_game_id' => 'SlotMachine_WildWildSpin',
		// 					'game_code' => 'SlotMachine_WildWildSpin'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Exploding Pirates","2":"变形海盗"}',
		// 					'english_name' => 'Exploding Pirates',
		// 					'external_game_id' => 'SlotMachine_ExplodingPirates',
		// 					'game_code' => 'SlotMachine_ExplodingPirates'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Secret Potion","2":"神密药水"}',
		// 					'english_name' => 'Secret Potion',
		// 					'external_game_id' => 'SlotMachine_SecretPotion',
		// 					'game_code' => 'SlotMachine_SecretPotion'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Demi Gods","2":"半神半人"}',
		// 					'english_name' => 'Demi Gods',
		// 					'external_game_id' => 'SlotMachine_DemiGods',
		// 					'game_code' => 'SlotMachine_DemiGods'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Power Pup Heroes","2":"幼仔英雄"}',
		// 					'english_name' => 'Power Pup Heroes',
		// 					'external_game_id' => 'SlotMachine_PowerPupHeroes',
		// 					'game_code' => 'SlotMachine_PowerPupHeroes'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Samurai\'s Path","2":"武道"}',
		// 					'english_name' => 'Samurai\'s Path',
		// 					'external_game_id' => 'SlotMachine_SamuraiPath',
		// 					'game_code' => 'SlotMachine_SamuraiPath'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Stinky socks","2":"臭袜子"}',
		// 					'english_name' => 'Stinky socks',
		// 					'external_game_id' => 'SlotMachine_StinkySocks',
		// 					'game_code' => 'SlotMachine_StinkySocks'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Egyptian Rebirth","2":"重生埃及"}',
		// 					'english_name' => 'Egyptian Rebirth',
		// 					'external_game_id' => 'SlotMachine_EgyptianRebirth',
		// 					'game_code' => 'SlotMachine_EgyptianRebirth'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Irish Charm","2":"仙童之宝"}',
		// 					'english_name' => 'Irish Charm',
		// 					'external_game_id' => 'SlotMachine_IrishCharms',
		// 					'game_code' => 'SlotMachine_IrishCharms'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Santa Wild Helpers","2":"圣诞野性助手"}',
		// 					'english_name' => 'Santa Wild Helpers',
		// 					'external_game_id' => 'SlotMachine_SantaWildHelpers',
		// 					'game_code' => 'SlotMachine_SantaWildHelpers'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Greedy servants","2":"贪婪仆人"}',
		// 					'english_name' => 'Greedy servants',
		// 					'external_game_id' => 'SlotMachine_GreedyServants',
		// 					'game_code' => 'SlotMachine_GreedyServants'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Iron Assassins","2":"杀人狂魔"}',
		// 					'english_name' => 'Iron Assassins',
		// 					'external_game_id' => 'SlotMachine_IronAssassins',
		// 					'game_code' => 'SlotMachine_IronAssassins'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Fire&Ice","2":"冰焰战略"}',
		// 					'english_name' => 'Fire&Ice',
		// 					'external_game_id' => 'SlotMachine_FireIce',
		// 					'game_code' => 'SlotMachine_FireIce'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Forest Harmony","2":"大自然和谐"}',
		// 					'english_name' => 'Forest Harmony',
		// 					'external_game_id' => 'SlotMachine_ForestHarmony',
		// 					'game_code' => 'SlotMachine_ForestHarmony'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"9 Figures Club","2":"豪华派"}',
		// 					'english_name' => '9 Figures Club',
		// 					'external_game_id' => 'SlotMachine_9FiguresClub',
		// 					'game_code' => 'SlotMachine_9FiguresClub'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Blazing Tires","2":"炽烈卡车"}',
		// 					'english_name' => 'Blazing Tires',
		// 					'external_game_id' => 'SlotMachine_BlazingTires',
		// 					'game_code' => 'SlotMachine_BlazingTires'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Candy slot twins","2":"双胞胎糖果"}',
		// 					'english_name' => 'Candy slot twins',
		// 					'external_game_id' => 'SlotMachine_CandySlotTwins',
		// 					'game_code' => 'SlotMachine_CandySlotTwins'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Cats gone wild","2":"狂猫蹦迪"}',
		// 					'english_name' => 'Cats gone wild',
		// 					'external_game_id' => 'SlotMachine_CatsGoneWild',
		// 					'game_code' => 'SlotMachine_CatsGoneWild'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Jade Connection","2":"翡翠玉石"}',
		// 					'english_name' => 'Jade Connection',
		// 					'external_game_id' => 'SlotMachine_JadeConnection',
		// 					'game_code' => 'SlotMachine_JadeConnection'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Master Panda","2":"熊猫厨师"}',
		// 					'english_name' => 'Master Panda',
		// 					'external_game_id' => 'SlotMachine_MasterPanda',
		// 					'game_code' => 'SlotMachine_MasterPanda'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Slot bound","2":"巫婆世纪"}',
		// 					'english_name' => 'Slot bound',
		// 					'external_game_id' => 'SlotMachine_SlotBound',
		// 					'game_code' => 'SlotMachine_SlotBound'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Slotsaurus","2":"恐龙机"}',
		// 					'english_name' => 'Slotsaurus',
		// 					'external_game_id' => 'SlotMachine_Slotosaurus',
		// 					'game_code' => 'SlotMachine_Slotosaurus'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Soccer babes","2":"足球女郎"}',
		// 					'english_name' => 'Soccer babes',
		// 					'external_game_id' => 'SlotMachine_SoccerBabes',
		// 					'game_code' => 'SlotMachine_SoccerBabes'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Tennis Champions","2":"网球王子"}',
		// 					'english_name' => 'Tennis Champions',
		// 					'external_game_id' => 'SlotMachine_TennisChampion',
		// 					'game_code' => 'SlotMachine_TennisChampion'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Year of the monkey","2":"猴年"}',
		// 					'english_name' => 'Year of the monkey',
		// 					'external_game_id' => 'SlotMachine_YearOfTheMonkey',
		// 					'game_code' => 'SlotMachine_YearOfTheMonkey'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Fortune Keepers","2":"财富守护使"}',
		// 					'english_name' => 'Fortune Keepers',
		// 					'external_game_id' => 'SlotMachine_FortuneKeepers',
		// 					'game_code' => 'SlotMachine_FortuneKeepers'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Zombie slot mania","2":"马尼亚僵尸症"}',
		// 					'english_name' => 'Zombie slot mania',
		// 					'external_game_id' => 'SlotMachine_ZombieSlotmania',
		// 					'game_code' => 'SlotMachine_ZombieSlotmania'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"8 Lucky Charms","2":"幸运8"}',
		// 					'english_name' => '8 Lucky Charms',
		// 					'external_game_id' => 'SlotMachine_8LuckyCharms',
		// 					'game_code' => 'SlotMachine_8LuckyCharms'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Atlantic Treasures","2":"亚特兰蒂斯寻宝"}',
		// 					'english_name' => 'Atlantic Treasures',
		// 					'external_game_id' => 'SlotMachine_AtlanticTreasures',
		// 					'game_code' => 'SlotMachine_AtlanticTreasures'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Code Name: Jackpot","2":"代号:头奖"}',
		// 					'english_name' => 'Code Name: Jackpot',
		// 					'external_game_id' => 'SlotMachine_CodeNameJackpot',
		// 					'game_code' => 'SlotMachine_CodeNameJackpot'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Farm of fun","2":"娱乐农场"}',
		// 					'english_name' => 'Farm of fun',
		// 					'external_game_id' => 'SlotMachine_FarmOfFun',
		// 					'game_code' => 'SlotMachine_FarmOfFun'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Bikers Gang","2":"摩托党"}',
		// 					'english_name' => 'Bikers Gang',
		// 					'external_game_id' => 'SlotMachine_BikersGang',
		// 					'game_code' => 'SlotMachine_BikersGang'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Eat them all","2":"食肉植物"}',
		// 					'english_name' => 'Eat them all',
		// 					'external_game_id' => 'SlotMachine_EatThemAll',
		// 					'game_code' => 'SlotMachine_EatThemAll'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Egyptian adventure","2":"冒险埃及"}',
		// 					'english_name' => 'Egyptian adventure',
		// 					'external_game_id' => 'SlotMachine_EgyptianAdventure',
		// 					'game_code' => 'SlotMachine_EgyptianAdventure'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Gangster\'s slot","2":"黑手党"}',
		// 					'english_name' => 'Gangster\'s slot',
		// 					'external_game_id' => 'SlotMachine_GangsterSlots',
		// 					'game_code' => 'SlotMachine_GangsterSlots'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Gods Of Slots","2":"老虎机之神"}',
		// 					'english_name' => 'Gods Of Slots',
		// 					'external_game_id' => 'SlotMachine_GodsOfSlots',
		// 					'game_code' => 'SlotMachine_GodsOfSlots'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Hawaii Vacation","2":"夏威夷假期"}',
		// 					'english_name' => 'Hawaii Vacation',
		// 					'external_game_id' => 'SlotMachine_HawaiiVacation',
		// 					'game_code' => 'SlotMachine_HawaiiVacation'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Lucky Miners","2":"幸运矿工"}',
		// 					'english_name' => 'Lucky Miners',
		// 					'external_game_id' => 'SlotMachine_LuckyMiners',
		// 					'game_code' => 'SlotMachine_LuckyMiners'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Safari Samba","2":"草原桑巴"}',
		// 					'english_name' => 'Safari Samba',
		// 					'external_game_id' => 'SlotMachine_SafariSamba',
		// 					'game_code' => 'SlotMachine_SafariSamba'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Undying Passion","2":"不灭激情"}',
		// 					'english_name' => 'Undying Passion',
		// 					'external_game_id' => 'SlotMachine_UndyingPassion',
		// 					'game_code' => 'SlotMachine_UndyingPassion'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Viking\'s Glory","2":"维京人之荣"}',
		// 					'english_name' => 'Viking\'s Glory',
		// 					'external_game_id' => 'SlotMachine_VikingsGlory',
		// 					'game_code' => 'SlotMachine_VikingsGlory'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Wacky monsters","2":"疯狂怪物"}',
		// 					'english_name' => 'Wacky monsters',
		// 					'external_game_id' => 'SlotMachine_WackyMonsters',
		// 					'game_code' => 'SlotMachine_WackyMonsters'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Year of luck","2":"幸运年"}',
		// 					'english_name' => 'Year of luck',
		// 					'external_game_id' => 'SlotMachine_YearOfLuck',
		// 					'game_code' => 'SlotMachine_YearOfLuck'
		// 				)
		// 			)
		// 		),
		// 		array(
		// 			'game_type' => 'UC UNKNOWN',
		// 			'game_type_lang' => 'UC UNKNOWN',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_FALSE,
		// 			'game_description_list' => array(
		// 				array(
		// 					'game_name' => '_json:{"1":"UC UNKNOWN GAME","2":"UC 未知游戏"}',
		// 					'english_name' => 'UC UNKNOWN GAME',
		// 					'external_game_id' => 'unknown',
		// 					'game_code' => 'unknown'
		// 				)
		// 			)
		// 		)
		// 	);
		// 	$game_description_list = array();
		// 	foreach ($data as $game_type) {
		// 		$this->db->insert('game_type', array(
		// 			'game_platform_id' => UC_API,
		// 			'game_type' => $game_type['game_type'],
		// 			'game_type_lang' => $game_type['game_type_lang'],
		// 			'status' => $game_type['status'],
		// 			'flag_show_in_site' => $game_type['flag_show_in_site'],
		// 		));

		// 		$game_type_id = $this->db->insert_id();
		// 		foreach ($game_type['game_description_list'] as $game_description) {
		// 			$game_description_list[] = array_merge(array(
		// 				'game_platform_id' => UC_API,
		// 				'game_type_id' => $game_type_id,
		// 			), $game_description);
		// 		}
		// 	}

		// 	$this->db->insert_batch('game_description', $game_description_list);
		// 	$this->db->trans_complete();

	}

	public function down() {
		// $this->db->trans_start();
		// $this->db->delete('game_type', array('game_platform_id' =>  UC_API));
		// $this->db->delete('game_description', array('game_platform_id' =>  UC_API));
		// $this->db->trans_complete();
	}
}