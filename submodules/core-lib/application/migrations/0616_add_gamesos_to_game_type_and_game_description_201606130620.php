<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_gamesos_to_game_type_and_game_description_201606130620 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {

// 		$this->db->trans_start();

// 		$data = array(
// 			array(
// 				'game_type' => 'Popular',
// 				'game_type_lang' => 'gamesos_popular_games',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 						array('game_name' => '双重奖励老虎机',
// 							'english_name' => 'SL Double Bonus Slots',
// 							'external_game_id' => 'xc_doublebonusslots',
// 							'game_code' => 'xc_doublebonusslots'
// 							),
// 							array('game_name' => '黄金海',
// 							'english_name' => 'SL Sea of Gold',
// 							'external_game_id' => 'xc_seaofgoldslots',
// 							'game_code' => 'xc_seaofgoldslots'
// 							),
// 							array('game_name' => '财富大冒险',
// 							'english_name' => 'AG Cash out Fortune',
// 							'external_game_id' => 'xc_fortunecashout',
// 							'game_code' => 'xc_fortunecashout'
// 							),
// 							array('game_name' => '玛雅幸运轮',
// 							'english_name' => 'SL Maya Wheel of Luck',
// 							'external_game_id' => 'xc_mayawheelofluckslots',
// 							'game_code' => 'xc_mayawheelofluckslots'
// 							),
// 							array('game_name' => '战东风',
// 							'english_name' => 'SL East Wind Battle',
// 							'external_game_id' => 'xc_eastwindbattleslots',
// 							'game_code' => 'xc_eastwindbattleslots'
// 							),
// 							array('game_name' => '水牛河畔',
// 							'english_name' => 'SL By The Rives Of Buffalo',
// 							'external_game_id' => 'xc_buffaloslot',
// 							'game_code' => 'xc_buffaloslot'
// 							),
// 							array('game_name' => '阿兹特克老虎机',
// 							'english_name' => 'SL New Aztec Slots',
// 							'external_game_id' => 'xc_aztecslotsnew',
// 							'game_code' => 'xc_aztecslotsnew'
// 							),
// 							array('game_name' => '狂野大中华',
// 							'english_name' => 'SL China Megawild',
// 							'external_game_id' => 'xc_wildchinaslots',
// 							'game_code' => 'xc_wildchinaslots'
// 							),
// 							array('game_name' => '纽约黑帮',
// 							'english_name' => 'SL New York Gangs',
// 							'external_game_id' => 'xc_gangsterslots',
// 							'game_code' => 'xc_gangsterslots'
// 							),
// 							array('game_name' => '极地故事',
// 							'english_name' => 'SL Polar Tale',
// 							'external_game_id' => 'xc_polartaleslots',
// 							'game_code' => 'xc_polartaleslots'
// 							),
// 							array('game_name' => '午夜幸运天',
// 							'english_name' => 'SL Midnight Lucky Sky',
// 							'external_game_id' => 'xc_firecrackerslots',
// 							'game_code' => 'xc_firecrackerslots'
// 							),
// 							array('game_name' => '卡拉OK之星',
// 							'english_name' => 'SL Karaoke Star Slots',
// 							'external_game_id' => 'xc_karaokeslots',
// 							'game_code' => 'xc_karaokeslots'
// 							),
// 							array('game_name' => '足球杯赛',
// 							'english_name' => 'SL Football Cup',
// 							'external_game_id' => 'xc_footballcupslots',
// 							'game_code' => 'xc_footballcupslots'
// 							),
// 							array('game_name' => '金条老虎机',
// 							'english_name' => 'SL Gold in Bars',
// 							'external_game_id' => 'xc_goldinbarsslots',
// 							'game_code' => 'xc_goldinbarsslots'
// 							),
// 							array('game_name' => '夏日梦想',
// 							'english_name' => 'SL Summer Dream',
// 							'external_game_id' => 'xc_summerdreamslots',
// 							'game_code' => 'xc_summerdreamslots'
// 							),
// 							array('game_name' => '午夜派对',
// 							'english_name' => 'SL Party Night',
// 							'external_game_id' => 'xc_partynightslots',
// 							'game_code' => 'xc_partynightslots'
// 							),
// 							array('game_name' => '神秘老虎机',
// 							'english_name' => 'SL Mystic Slots',
// 							'external_game_id' => 'xc_mysticslots',
// 							'game_code' => 'xc_mysticslots'
// 							),
// 							array('game_name' => '独行酒吧',
// 							'english_name' => 'SL Maverick saloon',
// 							'external_game_id' => 'xc_mavericksaloonslots',
// 							'game_code' => 'xc_mavericksaloonslots'
// 							),
// 							array('game_name' => '管道精灵',
// 							'english_name' => 'SL Pipezillas Slots',
// 							'external_game_id' => 'xc_pipezillasslots',
// 							'game_code' => 'xc_pipezillasslots'
// 							),
// 							array('game_name' => '吸血鬼杀手',
// 							'english_name' => 'SL Vampire Slayers',
// 							'external_game_id' => 'xc_vampireslayersslots ',
// 							'game_code' => 'xc_vampireslayersslots '
// 							),
// 							array('game_name' => '小鸡快跑老虎机',
// 							'english_name' => 'SL Run Chicken Run',
// 							'external_game_id' => 'xc_henhouseslots ',
// 							'game_code' => 'xc_henhouseslots '
// 							),
// 							array('game_name' => '宝石与城展',
// 							'english_name' => 'SL Gems and the City',
// 							'external_game_id' => 'xc_gemsandthecityslots',
// 							'game_code' => 'xc_gemsandthecityslots'
// 							),
// 							array('game_name' => '不间断聚会',
// 							'english_name' => 'SL Non-Stop Party',
// 							'external_game_id' => 'xc_nonstoppartyslots',
// 							'game_code' => 'xc_nonstoppartyslots'
// 							),
// 							array('game_name' => '热带古巴',
// 							'english_name' => 'SL Cubana-Tropicana',
// 							'external_game_id' => 'xc_cubanatropicanaslots',
// 							'game_code' => 'xc_cubanatropicanaslots'
// 							),
// 							array('game_name' => '爱琴海群岛',
// 							'english_name' => 'SL Archipelago',
// 							'external_game_id' => 'xc_archipelagoslots',
// 							'game_code' => 'xc_archipelagoslots'
// 							),
// 							array('game_name' => '多彩水果',
// 							'english_name' => 'SL Freaky Fruits',
// 							'external_game_id' => 'xc_freakyfruitsslots',
// 							'game_code' => 'xc_freakyfruitsslots'
// 							),
// 							array('game_name' => '摇滚老鼠',
// 							'english_name' => 'SL Rock the Mouse',
// 							'external_game_id' => 'xc_rockthemouseslots ',
// 							'game_code' => 'xc_rockthemouseslots '
// 							),
// 							array('game_name' => '魔罐',
// 							'english_name' => 'SL Magic Pot',
// 							'external_game_id' => 'xc_magicpotslots',
// 							'game_code' => 'xc_magicpotslots'
// 							),
// 							array('game_name' => '奇异牛仔',
// 							'english_name' => 'SL Freaky Cowboys',
// 							'external_game_id' => 'xc_freakycowboysslots ',
// 							'game_code' => 'xc_freakycowboysslots '
// 							),
// 							array('game_name' => '奇异土匪',
// 							'english_name' => 'SL Freaky Bandits ',
// 							'external_game_id' => 'xc_freakybanditsslots ',
// 							'game_code' => 'xc_freakybanditsslots '
// 							),
// 							array('game_name' => '多彩体操馆',
// 							'english_name' => 'SL Freaky Gym',
// 							'external_game_id' => 'xc_freakygymslots',
// 							'game_code' => 'xc_freakygymslots'
// 							),
// 							array('game_name' => '图腾探秘',
// 							'english_name' => 'SL Totem Quest',
// 							'external_game_id' => 'xc_totemquestslots',
// 							'game_code' => 'xc_totemquestslots'
// 							),
// 							array('game_name' => '恐怖屋',
// 							'english_name' => 'SL House of Scare',
// 							'external_game_id' => 'xc_houseofscareslots',
// 							'game_code' => 'xc_houseofscareslots'
// 							),
// 							array('game_name' => '黄金印度',
// 							'english_name' => 'SL Golden India Slots',
// 							'external_game_id' => 'xc_goldenindiaslots',
// 							'game_code' => 'xc_goldenindiaslots'
// 							),
// 							array('game_name' => '艳后宝藏',
// 							'english_name' => 'SL Cleopatra Treasure',
// 							'external_game_id' => 'xc_cleopatratreasureslots',
// 							'game_code' => 'xc_cleopatratreasureslots'
// 							),
// 							array('game_name' => '多彩狂野西部',
// 							'english_name' => 'SL Freaky Wild West',
// 							'external_game_id' => 'xc_freakywildwestslots',
// 							'game_code' => 'xc_freakywildwestslots'
// 							),
// 							array('game_name' => '慷慨的圣诞老人',
// 							'english_name' => 'SL Generous Santa',
// 							'external_game_id' => 'xc_generoussantaslots',
// 							'game_code' => 'xc_generoussantaslots'
// 							),
// 							array('game_name' => '生死战地老虎机',
// 							'english_name' => 'SL Battleground Spins',
// 							'external_game_id' => 'xc_battlegroundslots',
// 							'game_code' => 'xc_battlegroundslots'
// 							),
// 							array('game_name' => '旋转世界',
// 							'english_name' => 'SL Spin the World',
// 							'external_game_id' => 'xc_spintheworldslots',
// 							'game_code' => 'xc_spintheworldslots'
// 							),
// 							array('game_name' => '爱的日子',
// 							'english_name' => 'SL Jour de l\'Amour',
// 							'external_game_id' => 'xc_jourdelamourslots',
// 							'game_code' => 'xc_jourdelamourslots'
// 							),
// 							array('game_name' => '海盗老虎机',
// 							'english_name' => 'SL Pirate Slots',
// 							'external_game_id' => 'xc_pirateslots',
// 							'game_code' => 'xc_pirateslots'
// 							),
// 							array('game_name' => '亚特兰蒂斯潜水',
// 							'english_name' => 'SL Atlantis Dive',
// 							'external_game_id' => 'xc_atlantisdiveslots',
// 							'game_code' => 'xc_atlantisdiveslots'
// 							),
// 							array('game_name' => '世界杯足球老虎机',
// 							'english_name' => 'SL World-Cup Soccer Spins',
// 							'external_game_id' => 'xc_soccerslots',
// 							'game_code' => 'xc_soccerslots'
// 							),
// 							array('game_name' => '复活节盛宴',
// 							'english_name' => 'SL Easter Feast',
// 							'external_game_id' => 'xc_easterfeastslots',
// 							'game_code' => 'xc_easterfeastslots'
// 							),
// 							array('game_name' => '农场老虎机',
// 							'english_name' => 'SL Farm Slot',
// 							'external_game_id' => 'xc_farmslots',
// 							'game_code' => 'xc_farmslots'
// 							),
// 							array('game_name' => '疯狂赛车',
// 							'english_name' => 'SL Freaky Cars',
// 							'external_game_id' => 'xc_freakycarsslots',
// 							'game_code' => 'xc_freakycarsslots'
// 							),
// 							array('game_name' => '水果色拉老虎机',
// 							'english_name' => 'SL Fruit Salad Jackpot',
// 							'external_game_id' => 'xc_fruitsaladjackpotslots',
// 							'game_code' => 'xc_fruitsaladjackpotslots'
// 							),
// 							array('game_name' => '热 7 老虎机',
// 							'english_name' => 'SL Hot 7\'s',
// 							'external_game_id' => 'xc_hot7sslots',
// 							'game_code' => 'xc_hot7sslots'
// 							),
// 							array('game_name' => '墨西哥老虎机',
// 							'english_name' => 'SL Mexican Slots',
// 							'external_game_id' => 'xc_mexicoslots',
// 							'game_code' => 'xc_mexicoslots'
// 							),
// 							array('game_name' => '奥林匹克老虎机',
// 							'english_name' => 'SL Olympic Slots',
// 							'external_game_id' => 'xc_olympicslots',
// 							'game_code' => 'xc_olympicslots'
// 							),
// 							array('game_name' => '星际强盗',
// 							'english_name' => 'SL Space Robbers',
// 							'external_game_id' => 'xc_spacerobbersslots',
// 							'game_code' => 'xc_spacerobbersslots'
// 							),
// 							array('game_name' => '呜-呜老虎机',
// 							'english_name' => 'SL Choo-Choo Slots',
// 							'external_game_id' => 'xc_choochooslots',
// 							'game_code' => 'xc_choochooslots'
// 							)
// 						),
// 					),
// array(
// 	'game_type' => 'Arcades',
// 	'game_type_lang' => 'gamesos_arcades_games',
// 	'status' => self::FLAG_TRUE,
// 	'flag_show_in_site' => self::FLAG_TRUE,
// 	'game_description_list' => array(

// 					array('game_name' => '棒球刮刮乐',
// 					'english_name' => 'AG Basebull Scratch ',
// 					'external_game_id' => 'xc_basebullscratch',
// 					'game_code' => 'xc_basebullscratch'
// 					),
// 					array('game_name' => '宾果刮刮乐',
// 					'english_name' => 'AG Bingo Scratch',
// 					'external_game_id' => 'xc_bingoscratch',
// 					'game_code' => 'xc_bingoscratch'
// 					),
// 					array('game_name' => '热带古巴刮刮乐游戏',
// 					'english_name' => 'AG Cubana-Tropicana Scratch',
// 					'external_game_id' => 'xc_cubanatropicanascratch',
// 					'game_code' => 'xc_cubanatropicanascratch'
// 					),
// 					array('game_name' => '复活节刮刮乐',
// 					'english_name' => 'AG Easter Feast Scratch',
// 					'external_game_id' => 'xc_easterfeastscratch',
// 					'game_code' => 'xc_easterfeastscratch'
// 					),
// 					array('game_name' => '足球杯赛刮刮乐',
// 					'english_name' => 'AG Football Cup Scratch',
// 					'external_game_id' => 'xc_footballcupscratch',
// 					'game_code' => 'xc_footballcupscratch'
// 					),
// 					array('game_name' => '方程式刮刮乐',
// 					'english_name' => 'AG Formula Scratch ',
// 					'external_game_id' => 'xc_formulascratch ',
// 					'game_code' => 'xc_formulascratch '
// 					),
// 					array('game_name' => '多彩猜豆',
// 					'english_name' => 'AG Freaky Thimbles ',
// 					'external_game_id' => 'xc_freakythimblesscratch',
// 					'game_code' => 'xc_freakythimblesscratch'
// 					),
// 					array('game_name' => '黑帮财宝',
// 					'english_name' => 'AG Gangsters\' Loot ',
// 					'external_game_id' => 'xc_gangstersscratch',
// 					'game_code' => 'xc_gangstersscratch'
// 					),
// 					array('game_name' => '黄金 999.9',
// 					'english_name' => 'AG Gold 999.9 ',
// 					'external_game_id' => 'xc_goldenscratch',
// 					'game_code' => 'xc_goldenscratch'
// 					),
// 					array('game_name' => '鬼屋刮刮乐',
// 					'english_name' => 'AG House of Scare Scratch',
// 					'external_game_id' => 'xc_houseofscarescratch',
// 					'game_code' => 'xc_houseofscarescratch'
// 					),
// 					array('game_name' => '爱的日子刮刮乐游戏',
// 					'english_name' => 'AG Jour de l\'Amour Scratch',
// 					'external_game_id' => 'xc_jourdelamourscratch',
// 					'game_code' => 'xc_jourdelamourscratch'
// 					),
// 					array('game_name' => '基诺',
// 					'english_name' => 'AG Keno',
// 					'external_game_id' => 'xc_keno',
// 					'game_code' => 'xc_keno'
// 					),
// 					array('game_name' => '魔罐刮刮乐',
// 					'english_name' => 'AG Magic Pot Scratch',
// 					'external_game_id' => 'xc_magicpotscratch',
// 					'game_code' => 'xc_magicpotscratch'
// 					),
// 					array('game_name' => '怪兽刮刮乐',
// 					'english_name' => 'AG Monsters Scratch ',
// 					'external_game_id' => 'xc_monstersscratch ',
// 					'game_code' => 'xc_monstersscratch '
// 					),
// 					array('game_name' => '月球刮刮乐',
// 					'english_name' => 'AG Moonapolis ',
// 					'external_game_id' => 'xc_moonapolisscratch',
// 					'game_code' => 'xc_moonapolisscratch'
// 					),
// 					array('game_name' => '不间断聚会',
// 					'english_name' => 'AG Non-Stop Party Scratch',
// 					'external_game_id' => 'xc_nonstoppartyscratch',
// 					'game_code' => 'xc_nonstoppartyscratch'
// 					),
// 					array('game_name' => '泰迪刮刮乐',
// 					'english_name' => 'AG Teddy Scratch ',
// 					'external_game_id' => 'xc_teddyscratch ',
// 					'game_code' => 'xc_teddyscratch '
// 					),
// 					array('game_name' => '足球刮刮乐',
// 					'english_name' => 'AG Soccer Scratch ',
// 					'external_game_id' => 'xc_soccerscratch ',
// 					'game_code' => 'xc_soccerscratch '
// 					),
// 					array('game_name' => '闪耀王后',
// 					'english_name' => 'AG Sparkle Ladies ',
// 					'external_game_id' => 'xc_sparkleladiesscratch',
// 					'game_code' => 'xc_sparkleladiesscratch'
// 					),
// 					array('game_name' => '黑与白',
// 					'english_name' => 'XG Black and White',
// 					'external_game_id' => 'xc_blackandwhite',
// 					'game_code' => 'xc_blackandwhite'
// 					),
// 					array('game_name' => '曲棍球随手射击',
// 					'english_name' => 'XG Hockey Potshot',
// 					'external_game_id' => 'xc_potshot',
// 					'game_code' => 'xc_potshot'
// 					),
// 					array('game_name' => '即开彩票',
// 					'english_name' => 'XG Instant Lotto',
// 					'external_game_id' => 'xc_lotto',
// 					'game_code' => 'xc_lotto'
// 					),
// 					array('game_name' => 'Jackpot7 老虎机',
// 					'english_name' => 'XG Jackpot7',
// 					'external_game_id' => 'xc_jackpot7',
// 					'game_code' => 'xc_jackpot7'
// 					),
// 					array('game_name' => '幸运转盘',
// 					'english_name' => 'XG Lucky Wheel',
// 					'external_game_id' => 'xc_luckywheel',
// 					'game_code' => 'xc_luckywheel'
// 					),
// 					array('game_name' => '足球点射',
// 					'english_name' => 'XG Soccer Shot',
// 					'external_game_id' => 'xc_soccershot',
// 					'game_code' => 'xc_soccershot'
// 					),
// 				),
// 			),
// array(
// 	'game_type' => 'Card Games',
// 	'game_type_lang' => 'gamesos_card_games',
// 	'status' => self::FLAG_TRUE,
// 	'flag_show_in_site' => self::FLAG_TRUE,
// 	'game_description_list' => array(

// 						array('game_name' => '百家乐',
// 						'english_name' => 'CG Baccarat',
// 						'external_game_id' => 'xc_baccarat',
// 						'game_code' => 'xc_baccarat'
// 						),
// 						array('game_name' => ' 点21',
// 						'english_name' => 'CG Blackjack',
// 						'external_game_id' => 'xc_bj',
// 						'game_code' => 'xc_bj'
// 						),
// 						array('game_name' => '经典21 点',
// 						'english_name' => 'CG Blackjack Classic',
// 						'external_game_id' => 'xc_blackjack',
// 						'game_code' => 'xc_blackjack'
// 						),
// 						array('game_name' => '累积二十一点',
// 						'english_name' => 'CG Blackjack Progressive',
// 						'external_game_id' => 'xc_bj_progressive',
// 						'game_code' => 'xc_bj_progressive'
// 						),
// 						array('game_name' => '经典累积二十一点',
// 						'english_name' => 'CG Blackjack Progressive Classic',
// 						'external_game_id' => 'xc_blackjack_progressive',
// 						'game_code' => 'xc_blackjack_progressive'
// 						),
// 						array('game_name' => '投降 21 点',
// 						'english_name' => 'CG Blackjack Surrender',
// 						'external_game_id' => 'xc_bj_s',
// 						'game_code' => 'xc_bj_s'
// 						),
// 						array('game_name' => '经典换牌21点',
// 						'english_name' => 'CG Blackjack Surrender Classic',
// 						'external_game_id' => 'xc_blackjack_s',
// 						'game_code' => 'xc_blackjack_s'
// 						),
// 						array('game_name' => '换牌21点',
// 						'english_name' => 'CG Blackjack Switch',
// 						'external_game_id' => 'xc_bj_switch',
// 						'game_code' => 'xc_bj_switch'
// 						),
// 						array('game_name' => '换牌21点经典',
// 						'english_name' => 'CG Blackjack Switch Classic',
// 						'external_game_id' => 'xc_blackjack_switch',
// 						'game_code' => 'xc_blackjack_switch'
// 						),
// 						array('game_name' => '赌场德州扑克',
// 						'english_name' => 'CG Casino Hold\'em',
// 						'external_game_id' => 'xc_hold_em',
// 						'game_code' => 'xc_hold_em'
// 						),
// 						array('game_name' => '全亮二十一点！',
// 						'english_name' => 'CG Face Up 21 Blackjack',
// 						'external_game_id' => 'xc_bj_faceup',
// 						'game_code' => 'xc_bj_faceup'
// 						),
// 						array('game_name' => '全亮二十一点！经典',
// 						'english_name' => 'CG Face Up 21 Blackjack Classic',
// 						'external_game_id' => 'xc_blackjack_faceup',
// 						'game_code' => 'xc_blackjack_faceup'
// 						),
// 						array('game_name' => '绿洲扑克（Oasis Poker）',
// 						'english_name' => 'CG Oasis Poker',
// 						'external_game_id' => 'xc_poker_oasis',
// 						'game_code' => 'xc_poker_oasis'
// 						),
// 						array('game_name' => '牌九扑克',
// 						'english_name' => 'CG Pai Gow Poker',
// 						'external_game_id' => 'xc_paigowpoker',
// 						'game_code' => 'xc_paigowpoker'
// 						),
// 						array('game_name' => '完美对子二十一点',
// 						'english_name' => 'CG Perfect Pairs Blackjack',
// 						'external_game_id' => 'xc_bj_perfectpairs',
// 						'game_code' => 'xc_bj_perfectpairs'
// 						),
// 						array('game_name' => '完美对子二十一点经典',
// 						'english_name' => 'CG Perfect Pairs Blackjack Classic',
// 						'external_game_id' => 'xc_blackjack_perfectpairs',
// 						'game_code' => 'xc_blackjack_perfectpairs'
// 						),
// 						array('game_name' => '英式二十一点',
// 						'english_name' => 'CG Pontoon',
// 						'external_game_id' => 'xc_bj_pontoon',
// 						'game_code' => 'xc_bj_pontoon'
// 						),
// 						array('game_name' => '英式二十一点经典',
// 						'english_name' => 'CG Pontoon Classic',
// 						'external_game_id' => 'xc_blackjack_pontoon',
// 						'game_code' => 'xc_blackjack_pontoon'
// 						),
// 						array('game_name' => '俄罗斯扑克（Russian Poker）',
// 						'english_name' => 'CG Russian Poker',
// 						'external_game_id' => 'xc_poker_russian',
// 						'game_code' => 'xc_poker_russian'
// 						),
// 						array('game_name' => '西班牙二十一点',
// 						'english_name' => 'CG Spanish Blackjack',
// 						'external_game_id' => 'xc_bj_spanish',
// 						'game_code' => 'xc_bj_spanish'
// 						),
// 						array('game_name' => '西班牙二十一点经典',
// 						'english_name' => 'CG Spanish Blackjack Classic',
// 						'external_game_id' => 'xc_blackjack_spanish',
// 						'game_code' => 'xc_blackjack_spanish'
// 						),
// 						array('game_name' => 'VIP百家乐',
// 						'english_name' => 'CG VIP Multi Hand Baccarat',
// 						'external_game_id' => 'xc_mhbaccarat',
// 						'game_code' => 'xc_mhbaccarat'
// 						)
// 		            ),
// 			   ),
// array(
// 	'game_type' => 'Video Pokers',
// 	'game_type_lang' => 'gamesos_video_pokers_games',
// 	'status' => self::FLAG_TRUE,
// 	'flag_show_in_site' => self::FLAG_TRUE,
// 	'game_description_list' => array(

// 					array('game_name' => '竞技场扑克',
// 					'english_name' => 'VP Coliseum Poker',
// 					'external_game_id' => 'xc_coliseum',
// 					'game_code' => 'xc_coliseum'
// 					),
// 					array('game_name' => '10 行竞技场扑克',
// 					'english_name' => 'VP Coliseum Poker 10 Lines ',
// 					'external_game_id' => 'xc_coliseum10l',
// 					'game_code' => 'xc_coliseum10l'
// 					),
// 					array('game_name' => '25 行竞技场扑克',
// 					'english_name' => 'VP Coliseum Poker 25 Lines ',
// 					'external_game_id' => 'xc_coliseum25l',
// 					'game_code' => 'xc_coliseum25l'
// 					),
// 					array('game_name' => '4 线竞技场扑克',
// 					'english_name' => 'VP Coliseum Poker 4 Lines ',
// 					'external_game_id' => 'xc_coliseum4l',
// 					'game_code' => 'xc_coliseum4l'
// 					),
// 					array('game_name' => '50 行竞技场扑克',
// 					'english_name' => 'VP Coliseum Poker 50 Lines ',
// 					'external_game_id' => 'xc_coliseum50l',
// 					'game_code' => 'xc_coliseum50l'
// 					),
// 					array('game_name' => '万能两点',
// 					'english_name' => 'VP Deuces Wild',
// 					'external_game_id' => 'xc_deuceswild',
// 					'game_code' => 'xc_deuceswild'
// 					),
// 					array('game_name' => '10 行万能两点',
// 					'english_name' => 'VP Deuces Wild 10 Lines ',
// 					'external_game_id' => 'xc_deuceswild10l',
// 					'game_code' => 'xc_deuceswild10l'
// 					),
// 					array('game_name' => '25 行万能两点',
// 					'english_name' => 'VP Deuces Wild 25 Lines ',
// 					'external_game_id' => 'xc_deuceswild25l',
// 					'game_code' => 'xc_deuceswild25l'
// 					),
// 					array('game_name' => '4 线万能两点',
// 					'english_name' => 'VP Deuces Wild 4 Lines',
// 					'external_game_id' => 'xc_deuceswild4l',
// 					'game_code' => 'xc_deuceswild4l'
// 					),
// 					array('game_name' => '50 行万能两点',
// 					'english_name' => 'VP Deuces Wild 50 Lines ',
// 					'external_game_id' => 'xc_deuceswild50l',
// 					'game_code' => 'xc_deuceswild50l'
// 					),
// 					array('game_name' => '花牌和 A',
// 					'english_name' => 'VP Face the Ace',
// 					'external_game_id' => 'xc_faceace',
// 					'game_code' => 'xc_faceace'
// 					),
// 					array('game_name' => '10 行花牌和 A',
// 					'english_name' => 'VP Face the Ace 10 Lines ',
// 					'external_game_id' => 'xc_faceace10l',
// 					'game_code' => 'xc_faceace10l'
// 					),
// 					array('game_name' => '25 行花牌和 A',
// 					'english_name' => 'VP Face the Ace 25 Lines ',
// 					'external_game_id' => 'xc_faceace25l',
// 					'game_code' => 'xc_faceace25l'
// 					),
// 					array('game_name' => '4 行花牌和 A',
// 					'english_name' => 'VP Face the Ace 4 Lines ',
// 					'external_game_id' => 'xc_faceace4l',
// 					'game_code' => 'xc_faceace4l'
// 					),
// 					array('game_name' => '50 行花牌和 A',
// 					'english_name' => 'VP Face the Ace 50 Lines ',
// 					'external_game_id' => 'xc_faceace50l',
// 					'game_code' => 'xc_faceace50l'
// 					),
// 					array('game_name' => '五张 A',
// 					'english_name' => 'VP Five Aces',
// 					'external_game_id' => 'xc_fiveaces',
// 					'game_code' => 'xc_fiveaces'
// 					),
// 					array('game_name' => '好过杰克',
// 					'english_name' => 'VP Jacks or Better',
// 					'external_game_id' => 'xc_jacksorbetter',
// 					'game_code' => 'xc_jacksorbetter'
// 					),
// 					array('game_name' => '10 行好过杰克',
// 					'english_name' => 'VP Jacks or Better 10 Lines ',
// 					'external_game_id' => 'xc_jacksorbetter10l',
// 					'game_code' => 'xc_jacksorbetter10l'
// 					),
// 					array('game_name' => '25 行好过杰克',
// 					'english_name' => 'VP Jacks or Better 25 Lines ',
// 					'external_game_id' => 'xc_jacksorbetter25l',
// 					'game_code' => 'xc_jacksorbetter25l'
// 					),
// 					array('game_name' => '4 线好过杰克',
// 					'english_name' => 'VP Jacks or Better 4 Lines ',
// 					'external_game_id' => 'xc_jacksorbetter4l',
// 					'game_code' => 'xc_jacksorbetter4l'
// 					),
// 					array('game_name' => '50 行好过杰克',
// 					'english_name' => 'VP Jacks or Better 50 Lines ',
// 					'external_game_id' => 'xc_jacksorbetter50l',
// 					'game_code' => 'xc_jacksorbetter50l'
// 					),
// 					array('game_name' => '万能小丑',
// 					'english_name' => 'VP Joker Wild',
// 					'external_game_id' => 'xc_jokerwild',
// 					'game_code' => 'xc_jokerwild'
// 					),
// 					array('game_name' => '10 线万能小丑',
// 					'english_name' => 'VP Joker Wild 10 Lines ',
// 					'external_game_id' => 'xc_jokerwild10l',
// 					'game_code' => 'xc_jokerwild10l'
// 					),
// 					array('game_name' => '25 线万能小丑',
// 					'english_name' => 'VP Joker Wild 25 Lines ',
// 					'external_game_id' => 'xc_jokerwild25l',
// 					'game_code' => 'xc_jokerwild25l'
// 					),
// 					array('game_name' => '4 线万能小丑',
// 					'english_name' => 'VP Joker Wild 4 Lines ',
// 					'external_game_id' => 'xc_jokerwild4l',
// 					'game_code' => 'xc_jokerwild4l'
// 					),
// 					array('game_name' => '50 线万能小丑',
// 					'english_name' => 'VP Joker Wild 50 Lines ',
// 					'external_game_id' => 'xc_jokerwild50l',
// 					'game_code' => 'xc_jokerwild50l'
// 					),
// 					array('game_name' => '冲击波扑克',
// 					'english_name' => 'VP Shockwave Poker',
// 					'external_game_id' => 'xc_shockwave',
// 					'game_code' => 'xc_shockwave'
// 					),
// 		),
// ),
// array(
// 	'game_type' => 'Table Games',
// 	'game_type_lang' => 'gamesos_table_games',
// 	'status' => self::FLAG_TRUE,
// 	'flag_show_in_site' => self::FLAG_TRUE,
// 	'game_description_list' => array(

// 					array('game_name' => '美式轮盘赌',
// 					'english_name' => 'TG American Roulette',
// 					'external_game_id' => 'xc_americanroulette',
// 					'game_code' => 'xc_americanroulette'
// 					),
// 					array('game_name' => '掷骰子',
// 					'english_name' => 'TG Craps',
// 					'external_game_id' => 'xc_craps',
// 					'game_code' => 'xc_craps'
// 					),
// 					array('game_name' => '欧式轮盘赌',
// 					'english_name' => 'TG European Roulette',
// 					'external_game_id' => 'xc_euroulette',
// 					'game_code' => 'xc_euroulette'
// 					),
// 					array('game_name' => '轮盘赌专家',
// 					'english_name' => 'TG European Roulette PRO',
// 					'external_game_id' => 'xc_euroulettepro',
// 					'game_code' => 'xc_euroulettepro'
// 					),
// 					array('game_name' => '骰宝',
// 					'english_name' => 'TG Sic Bo',
// 					'external_game_id' => 'xc_sic_bo',
// 					'game_code' => 'xc_sic_bo'
// 					),
// 				),
// 			),
//     		array(
//     			'game_type' => 'Others',
//     			'game_type_lang' => 'gamesos_others_games',
//     			'status' => self::FLAG_TRUE,
//     			'flag_show_in_site' => self::FLAG_TRUE,
//     			'game_description_list' => array(


//     			array('game_name' => '百家乐',
//     			'english_name' => 'MG Baccarat',
//     			'external_game_id' => 'xc_minibaccarat',
//     			'game_code' => 'xc_minibaccarat'
//     			),
//     			array('game_name' => '宾果刮刮乐',
//     			'english_name' => 'MG Bingo Scratch',
//     			'external_game_id' => 'xc_minibingoscratch',
//     			'game_code' => 'xc_minibingoscratch'
//     			),
//     			array('game_name' => '点21',
//     			'english_name' => 'MG Blackjack',
//     			'external_game_id' => 'xc_miniblackjack',
//     			'game_code' => 'xc_miniblackjack'
//     			),
//     			array('game_name' => '累积二十一点',
//     			'english_name' => 'MG Blackjack Progressive',
//     			'external_game_id' => 'xc_miniblackjack_progressive',
//     			'game_code' => 'xc_miniblackjack_progressive'
//     			),
//     			array('game_name' => '投降 21 点',
//     			'english_name' => 'MG Blackjack Surrender',
//     			'external_game_id' => 'xc_miniblackjack_s',
//     			'game_code' => 'xc_miniblackjack_s'
//     			),
//     			array('game_name' => '赌场德州扑克',
//     			'english_name' => 'MG Casino Hold\'em',
//     			'external_game_id' => 'xc_minihold_em',
//     			'game_code' => 'xc_minihold_em'
//     			),
//     			array('game_name' => '竞技场扑克',
//     			'english_name' => 'MG Coliseum Poker',
//     			'external_game_id' => 'xc_minicoliseum',
//     			'game_code' => 'xc_minicoliseum'
//     			),
//     			array('game_name' => '万能两点',
//     			'english_name' => 'MG Deuces Wild ',
//     			'external_game_id' => 'xc_minideuceswild',
//     			'game_code' => 'xc_minideuceswild'
//     			),
//     			array('game_name' => '欧式轮盘赌',
//     			'english_name' => 'MG European Roulette',
//     			'external_game_id' => 'xc_minieuroulette',
//     			'game_code' => 'xc_minieuroulette'
//     			),
//     			array('game_name' => '花牌和 A',
//     			'english_name' => 'MG Face the Ace',
//     			'external_game_id' => 'xc_minifaceace',
//     			'game_code' => 'xc_minifaceace'
//     			),
//     			array('game_name' => '全亮二十一点！',
//     			'english_name' => 'MG Face Up 21 Blackjack',
//     			'external_game_id' => 'xc_miniblackjack_faceup',
//     			'game_code' => 'xc_miniblackjack_faceup'
//     			),
//     			array('game_name' => '五张 A',
//     			'english_name' => 'MG Five Aces',
//     			'external_game_id' => 'xc_minifiveaces',
//     			'game_code' => 'xc_minifiveaces'
//     			),
//     			array('game_name' => '水果色拉老虎机',
//     			'english_name' => 'MG Fruit Salad Jackpot',
//     			'external_game_id' => 'xc_minifruitsaladjackpotslots',
//     			'game_code' => 'xc_minifruitsaladjackpotslots'
//     			),
//     			array('game_name' => '金条老虎机',
//     			'english_name' => 'MG Gold in Bars',
//     			'external_game_id' => 'xc_minigoldinbarsslots',
//     			'game_code' => 'xc_minigoldinbarsslots'
//     			),
//     			array('game_name' => '热 7 老虎机',
//     			'english_name' => 'MG Hot 7\'s',
//     			'external_game_id' => 'xc_minihot7sslots',
//     			'game_code' => 'xc_minihot7sslots'
//     			),
//     			array('game_name' => '好过杰克',
//     			'english_name' => 'MG Jacks or Better ',
//     			'external_game_id' => 'xc_minijacksorbetter',
//     			'game_code' => 'xc_minijacksorbetter'
//     			),
//     			array('game_name' => '万能小丑',
//     			'english_name' => 'MG Joker Wild',
//     			'external_game_id' => 'xc_minijokerwild',
//     			'game_code' => 'xc_minijokerwild'
//     			),
//     			array('game_name' => '幸运转盘',
//     			'english_name' => 'MG Lucky Wheel',
//     			'external_game_id' => 'xc_miniluckywheel',
//     			'game_code' => 'xc_miniluckywheel'
//     			),
//     			array('game_name' => '迷你麻将',
//     			'english_name' => 'MG Mahjong Videoslot',
//     			'external_game_id' => 'xc_minimahjongvideoslots',
//     			'game_code' => 'xc_minimahjongvideoslots'
//     			),
//     			array('game_name' => '墨西哥老虎机',
//     			'english_name' => 'MG Mexican Slots ',
//     			'external_game_id' => 'xc_minimexicoslots',
//     			'game_code' => 'xc_minimexicoslots'
//     			),
//     			array('game_name' => '牌九扑克',
//     			'english_name' => 'MG Pai Gow Poker',
//     			'external_game_id' => 'xc_minipaigowpoker',
//     			'game_code' => 'xc_minipaigowpoker'
//     			),
//     			array('game_name' => '完美对子二十一点',
//     			'english_name' => 'MG Perfect Pairs Blackjack',
//     			'external_game_id' => 'xc_miniblackjack_perfectpairs',
//     			'game_code' => 'xc_miniblackjack_perfectpairs'
//     			),
//     			array('game_name' => '海盗老虎机',
//     			'english_name' => 'MG Pirate Slots ',
//     			'external_game_id' => 'xc_minipirateslots',
//     			'game_code' => 'xc_minipirateslots'
//     			),
//     			array('game_name' => '英式二十一点',
//     			'english_name' => 'MG Pontoon',
//     			'external_game_id' => 'xc_miniblackjack_pontoon',
//     			'game_code' => 'xc_miniblackjack_pontoon'
//     			),
//     			array('game_name' => '西班牙二十一点',
//     			'english_name' => 'MG Spanish Blackjack',
//     			'external_game_id' => 'xc_miniblackjack_spanish',
//     			'game_code' => 'xc_miniblackjack_spanish'
//     			),
//     		),

//     	));

//     $game_description_list = array();
//     foreach ($data as $game_type) {

//     	$this->db->insert('game_type', array(
//     		'game_platform_id' => GAMESOS_API,
//     		'game_type' => $game_type['game_type'],
//     		'game_type_lang' => $game_type['game_type_lang'],
//     		'status' => $game_type['status'],
//     		'flag_show_in_site' => $game_type['flag_show_in_site'],
//     		));

//     	$game_type_id = $this->db->insert_id();
//     	foreach ($game_type['game_description_list'] as $game_description) {
//     		$game_description_list[] = array_merge(array(
//     			'game_platform_id' => GAMESOS_API,
//     			'game_type_id' => $game_type_id,
//     			), $game_description);
//     	}

//     }

//     $this->db->insert_batch('game_description', $game_description_list);
//     $this->db->trans_complete();

}

public function down() {
	$this->db->trans_start();
	$this->db->delete('game_type', array('game_platform_id' => GAMESOS_API, 'game_type !='=> 'unknown'));
	$this->db->delete('game_description', array('game_platform_id' => GAMESOS_API,'game_name !='=> 'gamesos.unknown'));
	$this->db->trans_complete();
}
}