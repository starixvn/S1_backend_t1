<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_gd_in_game_type_and_game_description_201605021149 extends CI_Migration {
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;
	private $tableName = 'game_description';

	public function up() {
		// $sql = "SELECT game_platform_id FROM game_type where game_platform_id = " . GD_API;
		// $query = $this->db->query($sql);
		// $result = $query->row_array();

		// if (!$result) {

		// 	$this->db->trans_start();

		// 	//insert to game_description
		// 	$data = array(
		// 		array(
		// 			'game_type' => 'Video Slots',
		// 			'game_type_lang' => 'gd_videoslots',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_TRUE,
		// 			'game_description_list' => array(
		// 					//Video Slot
		// 					array('game_name' => 'gd.RNG14408',
		// 					'english_name' => 'Rising Gems',
		// 					'external_game_id' => 'RNG14408',
		// 					'game_code' => 'RNG14408'
		// 					),
		// 					array('game_name' => 'gd.RNG14410',
		// 					'english_name' => 'Shougen War',
		// 					'external_game_id' => 'RNG14410',
		// 					'game_code' => 'RNG14410'
		// 					),
		// 				),
		// 			),
		// 		array(
		// 		    'game_type' => 'Video Pokers',
		// 			'game_type_lang' => 'gd_videopokers',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_TRUE,
		// 			'game_description_list' => array(
		// 					//Video Poker
		// 					array('game_name' => 'gd.RNG14385',
		// 					'english_name' => 'Ten Or Better',
		// 					'external_game_id' => 'RNG14385',
		// 					'game_code' => 'RNG14385'
		// 					),
		// 					array('game_name' => 'gd.RNG14380',
		// 					'english_name' => 'Jack or Better',
		// 					'external_game_id' => 'RNG14380',
		// 					'game_code' => 'RNG14380'
		// 					),
		// 					array('game_name' => 'gd.RNG14381',
		// 					'english_name' => 'Joker Poker',
		// 					'external_game_id' => 'RNG14381',
		// 					'game_code' => 'RNG14381'
		// 					),
		// 				),
		// 			),
		// 		array(
		// 			'game_type' => 'Table Games',
		// 			'game_type_lang' => 'gd_tablegames',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_TRUE,
		// 			'game_description_list' => array(
		// 					//Table Game
		// 					array('game_name' => 'gd.RNG14415',
		// 					'english_name' => 'Baccarat',
		// 					'external_game_id' => 'RNG14415',
		// 					'game_code' => 'RNG14415'
		// 					),
		// 					array('game_name' => 'gd.RNG14417',
		// 					'english_name' => 'Belangkai',
		// 					'external_game_id' => 'RNG14417',
		// 					'game_code' => 'RNG14417'
		// 					),
		// 					array('game_name' => 'gd.RNG14416',
		// 					'english_name' => 'Blackjack',
		// 					'external_game_id' => 'RNG14416',
		// 					'game_code' => 'RNG14416'
		// 					),
		// 					array('game_name' => 'gd.RNG14419',
		// 					'english_name' => 'Caribbean Poker',
		// 					'external_game_id' => 'RNG14419',
		// 					'game_code' => 'RNG14419'
		// 					),
		// 					array('game_name' => 'gd.RNG14418',
		// 					'english_name' => 'Cup of Hope',
		// 					'external_game_id' => 'RNG14418',
		// 					'game_code' => 'RNG14418'
		// 					),
		// 					array('game_name' => 'gd.RNG14420',
		// 					'english_name' => 'Dragon Tiger',
		// 					'external_game_id' => 'RNG14420',
		// 					'game_code' => 'RNG14420'
		// 					),
		// 					array('game_name' => 'gd.RNG14421',
		// 					'english_name' => 'Hulu Cock',
		// 					'external_game_id' => 'RNG14421',
		// 					'game_code' => 'RNG14421'
		// 					),
		// 					array('game_name' => 'gd.RNG14425',
		// 					'english_name' => 'StoneScissorsPaper',
		// 					'external_game_id' => 'RNG14425',
		// 					'game_code' => 'RNG14425'
		// 					),
		// 					array('game_name' => 'gd.RNG14422',
		// 					'english_name' => 'Roulette',
		// 					'external_game_id' => 'RNG14422',
		// 					'game_code' => 'RNG14422'
		// 					),
		// 					array('game_name' => 'gd.RNG14424',
		// 					'english_name' => 'Sedie',
		// 					'external_game_id' => 'RNG14424',
		// 					'game_code' => 'RNG14424'
		// 					),
		// 					array('game_name' => 'gd.RNG14423',
		// 					'english_name' => 'Sicbo',
		// 					'external_game_id' => 'RNG14423',
		// 					'game_code' => 'RNG14423'
		// 					),
		// 				),
		// 			),

		// 		array(
		// 			'game_type' => 'Slot Games',
		// 			'game_type_lang' => 'gd_slotgames',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_TRUE,
		// 			'game_description_list' => array(
		// 				// Slot Game
		// 				array('game_name' => 'gd.RNG4583',
		// 				'english_name' => 'All For One',
		// 				'external_game_id' => 'RNG4583',
		// 				'game_code' => 'RNG4583'
		// 				),
		// 				array('game_name' => 'gd.RNG4625',
		// 				'english_name' => 'Arctic Wonders',
		// 				'external_game_id' => 'RNG4625',
		// 				'game_code' => 'RNG4625'
		// 				),
		// 				array('game_name' => 'gd.RNG4498',
		// 				'english_name' => 'Aztlan\'s Gold',
		// 				'external_game_id' => 'RNG4498',
		// 				'game_code' => 'RNG4498'
		// 				),
		// 				array('game_name' => 'gd.RNG4569',
		// 				'english_name' => 'Barnstormer Bucks',
		// 				'external_game_id' => 'RNG4569',
		// 				'game_code' => 'RNG4569'
		// 				),
		// 				array('game_name' => 'gd.RNG4572',
		// 				'english_name' => 'Bikini Island',
		// 				'external_game_id' => 'RNG4572',
		// 				'game_code' => 'RNG4572'
		// 				),
		// 				array('game_name' => 'gd.RNG4620',
		// 				'english_name' => 'Blackbeards Bounty',
		// 				'external_game_id' => 'RNG4620',
		// 				'game_code' => 'RNG4620'
		// 				),
		// 				array('game_name' => 'gd.RNG6455',
		// 				'english_name' => 'Carnival Cash',
		// 				'external_game_id' => 'RNG6455',
		// 				'game_code' => 'RNG6455'
		// 				),
		// 				array('game_name' => 'gd.RNG4608',
		// 				'english_name' => 'Cash Reef',
		// 				'external_game_id' => 'RNG4608',
		// 				'game_code' => 'RNG4608'
		// 				),
		// 				array('game_name' => 'gd.RNG4581',
		// 				'english_name' => 'Cashosaurus',
		// 				'external_game_id' => 'RNG4581',
		// 				'game_code' => 'RNG4581'
		// 				),
		// 				array('game_name' => 'gd.RNG4612',
		// 				'english_name' => 'Disco Funk',
		// 				'external_game_id' => 'RNG4612',
		// 				'game_code' => 'RNG4612'
		// 				),
		// 				array('game_name' => 'gd.RNG4578',
		// 				'english_name' => 'Dr Feelgood',
		// 				'external_game_id' => 'RNG4578',
		// 				'game_code' => 'RNG4578'
		// 				),
		// 				array('game_name' => 'gd.RNG4586',
		// 				'english_name' => 'Dragon Castle',
		// 				'external_game_id' => 'RNG4586',
		// 				'game_code' => 'RNG4586'
		// 				),
		// 				array('game_name' => 'gd.RNG4502',
		// 				'english_name' => 'Dragons Realm',
		// 				'external_game_id' => 'RNG4502',
		// 				'game_code' => 'RNG4502'
		// 				),
		// 				array('game_name' => 'gd.RNG4504',
		// 				'english_name' => 'Egyptian Dreams',
		// 				'external_game_id' => 'RNG4504',
		// 				'game_code' => 'RNG4504'
		// 				),
		// 				array('game_name' => 'gd.RNG4609',
		// 				'english_name' => 'Flying High',
		// 				'external_game_id' => 'RNG4609',
		// 				'game_code' => 'RNG4609'
		// 				),
		// 				array('game_name' => 'gd.RNG4573',
		// 				'english_name' => 'Frontier Fortunes',
		// 				'external_game_id' => 'RNG4573',
		// 				'game_code' => 'RNG4573'
		// 				),
		// 				array('game_name' => 'gd.RNG4570',
		// 				'english_name' => 'Golden Unicorn',
		// 				'external_game_id' => 'RNG4570',
		// 				'game_code' => 'RNG4570'
		// 				),
		// 				array('game_name' => 'gd.RNG4606',
		// 				'english_name' => 'Grape Escape',
		// 				'external_game_id' => 'RNG4606',
		// 				'game_code' => 'RNG4606'
		// 				),
		// 				array('game_name' => 'gd.RNG4598',
		// 				'english_name' => 'Haunted House',
		// 				'external_game_id' => 'RNG4598',
		// 				'game_code' => 'RNG4598'
		// 				),
		// 				array('game_name' => 'gd.RNG4497',
		// 				'english_name' => 'Indian Cash Catcher',
		// 				'external_game_id' => 'RNG4497',
		// 				'game_code' => 'RNG4497'
		// 				),
		// 				array('game_name' => 'gd.RNG4615',
		// 				'english_name' => 'Jungle Rumble',
		// 				'external_game_id' => 'RNG4615',
		// 				'game_code' => 'RNG4615'
		// 				),
		// 				array('game_name' => 'gd.RNG4626',
		// 				'english_name' => 'King Tut\'s Tomb',
		// 				'external_game_id' => 'RNG4626',
		// 				'game_code' => 'RNG4626'
		// 				),
		// 				array('game_name' => 'gd.RNG4585',
		// 				'english_name' => 'Little Green Money',
		// 				'external_game_id' => 'RNG4585',
		// 				'game_code' => 'RNG4585'
		// 				),
		// 				array('game_name' => 'gd.RNG4613',
		// 				'english_name' => 'Monster Mash Cash',
		// 				'external_game_id' => 'RNG4613',
		// 				'game_code' => 'RNG4613'
		// 				),
		// 				array('game_name' => 'gd.RNG4599',
		// 				'english_name' => 'Mr Bling',
		// 				'external_game_id' => 'RNG4599',
		// 				'game_code' => 'RNG4599'
		// 				),
		// 				array('game_name' => 'gd.RNG4503',
		// 				'english_name' => 'Mummy Money',
		// 				'external_game_id' => 'RNG4503',
		// 				'game_code' => 'RNG4503'
		// 				),
		// 				array('game_name' => 'gd.RNG4602',
		// 				'english_name' => 'Mystic Fortune',
		// 				'external_game_id' => 'RNG4602',
		// 				'game_code' => 'RNG4602'
		// 				),
		// 				array('game_name' => 'gd.RNG4603',
		// 				'english_name' => 'Pamper Me',
		// 				'external_game_id' => 'RNG4603',
		// 				'game_code' => 'RNG4603'
		// 				),
		// 				array('game_name' => 'gd.RNG4499',
		// 				'english_name' => 'Pirate\'s Plunder',
		// 				'external_game_id' => 'RNG4499',
		// 				'game_code' => 'RNG4499'
		// 				),
		// 				array('game_name' => 'gd.RNG4623',
		// 				'english_name' => 'Pool Shark',
		// 				'external_game_id' => 'RNG4623',
		// 				'game_code' => 'RNG4623'
		// 				),
		// 				array('game_name' => 'gd.RNG4582',
		// 				'english_name' => 'Pucker Up Prince',
		// 				'external_game_id' => 'RNG4582',
		// 				'game_code' => 'RNG4582'
		// 				),
		// 				array('game_name' => 'gd.RNG4590',
		// 				'english_name' => 'Queen of Queens',
		// 				'external_game_id' => 'RNG4590',
		// 				'game_code' => 'RNG4590'
		// 				),
		// 				array('game_name' => 'gd.RNG4501',
		// 				'english_name' => 'Queen of Queens II',
		// 				'external_game_id' => 'RNG4501',
		// 				'game_code' => 'RNG4501'
		// 				),
		// 				array('game_name' => 'gd.RNG4618',
		// 				'english_name' => 'Ride Em Cowboy',
		// 				'external_game_id' => 'RNG4618',
		// 				'game_code' => 'RNG4618'
		// 				),
		// 				array('game_name' => 'gd.RNG4629',
		// 				'english_name' => 'Rodeo Drive',
		// 				'external_game_id' => 'RNG4629',
		// 				'game_code' => 'RNG4629'
		// 				),
		// 				array('game_name' => 'gd.RNG4601',
		// 				'english_name' => 'Shaolin Fortunes',
		// 				'external_game_id' => 'RNG4601',
		// 				'game_code' => 'RNG4601'
		// 				),
		// 				array('game_name' => 'gd.RNG4594',
		// 				'english_name' => 'Shaolin Fortunes 100',
		// 				'external_game_id' => 'RNG4594',
		// 				'game_code' => 'RNG4594'
		// 				),
		// 				array('game_name' => 'gd.RNG4580',
		// 				'english_name' => 'Shogun\'s Land',
		// 				'external_game_id' => 'RNG4580',
		// 				'game_code' => 'RNG4580'
		// 				),
		// 				array('game_name' => 'gd.RNG4588',
		// 				'english_name' => 'SOS',
		// 				'external_game_id' => 'RNG4588',
		// 				'game_code' => 'RNG4588'
		// 				),
		// 				array('game_name' => 'gd.RNG4575',
		// 				'english_name' => 'Space Fortune',
		// 				'external_game_id' => 'RNG4575',
		// 				'game_code' => 'RNG4575'
		// 				),
		// 				array('game_name' => 'gd.RNG4621',
		// 				'english_name' => 'Super Strike',
		// 				'external_game_id' => 'RNG4621',
		// 				'game_code' => 'RNG4621'
		// 				),
		// 				array('game_name' => 'gd.RNG4584',
		// 				'english_name' => 'The Big Deal',
		// 				'external_game_id' => 'RNG4584',
		// 				'game_code' => 'RNG4584'
		// 				),
		// 				array('game_name' => 'gd.RNG4574',
		// 				'english_name' => 'Tower Of Pizza',
		// 				'external_game_id' => 'RNG4574',
		// 				'game_code' => 'RNG4574'
		// 				),
		// 				array('game_name' => 'gd.RNG4627',
		// 				'english_name' => 'Viking\'s Plunder',
		// 				'external_game_id' => 'RNG4627',
		// 				'game_code' => 'RNG4627'
		// 				),
		// 				array('game_name' => 'gd.RNG4616',
		// 				'english_name' => 'Weird Science',
		// 				'external_game_id' => 'RNG4616',
		// 				'game_code' => 'RNG4616'
		// 				),
		// 				array('game_name' => 'gd.RNG4630',
		// 				'english_name' => 'Zeus',
		// 				'external_game_id' => 'RNG4630',
		// 				'game_code' => 'RNG4630'
		// 				),
		// 				array('game_name' => 'gd.RNG4579',
		// 				'english_name' => 'Sir Blingalot',
		// 				'external_game_id' => 'RNG4579',
		// 				'game_code' => 'RNG4579'
		// 				),
		// 				array('game_name' => 'gd.RNG4576',
		// 				'english_name' => 'Double O Dollars',
		// 				'external_game_id' => 'RNG4576',
		// 				'game_code' => 'RNG4576'
		// 				),
		// 				array('game_name' => 'gd.RNG4596',
		// 				'english_name' => 'Sky\'s the Limit',
		// 				'external_game_id' => 'RNG4596',
		// 				'game_code' => 'RNG4596'
		// 				),
		// 				array('game_name' => 'gd.RNG6433',
		// 				'english_name' => 'Treasure Diver',
		// 				'external_game_id' => 'RNG6433',
		// 				'game_code' => 'RNG6433'
		// 				),
		// 				array('game_name' => 'gd.RNG9497',
		// 				'english_name' => 'Kane\'s Inferno',
		// 				'external_game_id' => 'RNG9497',
		// 				'game_code' => 'RNG9497'
		// 				),
		// 				array('game_name' => 'gd.RNG17334',
		// 				'english_name' => 'Galactic Cash',
		// 				'external_game_id' => 'RNG17334',
		// 				'game_code' => 'RNG17334'
		// 				),
		// 				array('game_name' => 'gd.RNG17335',
		// 				'english_name' => 'Buggy Bonus',
		// 				'external_game_id' => 'RNG17335',
		// 				'game_code' => 'RNG17335'
		// 				),
		// 				array('game_name' => 'gd.RNG1376',
		// 				'english_name' => 'Three Kingdom',
		// 				'external_game_id' => 'RNG1376',
		// 				'game_code' => 'RNG1376'
		// 				),
		// 				array('game_name' => 'gd.RNG8808',
		// 				'english_name' => 'Three Kingdom2',
		// 				'external_game_id' => 'RNG8808',
		// 				'game_code' => 'RNG8808'
		// 				),
		// 				array('game_name' => 'gd.RNG8809',
		// 				'english_name' => 'West Journey',
		// 				'external_game_id' => 'RNG8809',
		// 				'game_code' => 'RNG8809'
		// 				),
		// 				array('game_name' => 'gd.RNG11698',
		// 				'english_name' => 'Lucky Five',
		// 				'external_game_id' => 'RNG11698',
		// 				'game_code' => 'RNG11698'
		// 				),
		// 				array('game_name' => 'gd.RNG14386',
		// 				'english_name' => 'Amazing Thailand',
		// 				'external_game_id' => 'RNG14386',
		// 				'game_code' => 'RNG14386'
		// 				),
		// 				array('game_name' => 'gd.RNG14388',
		// 				'english_name' => 'Chinese Zodiac',
		// 				'external_game_id' => 'RNG14388',
		// 				'game_code' => 'RNG14388'
		// 				),
		// 				array('game_name' => 'gd.RNG14389',
		// 				'english_name' => 'Dino Golden',
		// 				'external_game_id' => 'RNG14389',
		// 				'game_code' => 'RNG14389'
		// 				),
		// 				array('game_name' => 'gd.RNG14390',
		// 				'english_name' => 'Dragon Gold',
		// 				'external_game_id' => 'RNG14390',
		// 				'game_code' => 'RNG14390'
		// 				),
		// 				array('game_name' => 'gd.RNG14391',
		// 				'english_name' => 'Emperor Gate',
		// 				'external_game_id' => 'RNG14391',
		// 				'game_code' => 'RNG14391'
		// 				),
		// 				array('game_name' => 'gd.RNG14395',
		// 				'english_name' => 'Father vs Zombies',
		// 				'external_game_id' => 'RNG14395',
		// 				'game_code' => 'RNG14395'
		// 				),
		// 				array('game_name' => 'gd.RNG14393',
		// 				'english_name' => 'Fa Da Cai',
		// 				'external_game_id' => 'RNG14393',
		// 				'game_code' => 'RNG14393'
		// 				),
		// 				array('game_name' => 'gd.RNG14392',
		// 				'english_name' => 'Foot Ball',
		// 				'external_game_id' => 'RNG14392',
		// 				'game_code' => 'RNG14392'
		// 				),
		// 				array('game_name' => 'gd.RNG14394',
		// 				'english_name' => 'Fruity Futti',
		// 				'external_game_id' => 'RNG14394',
		// 				'game_code' => 'RNG14394'
		// 				),
		// 				array('game_name' => 'gd.RNG14406',
		// 				'english_name' => 'Pharaoh',
		// 				'external_game_id' => 'RNG14406',
		// 				'game_code' => 'RNG14406'
		// 				),
		// 				array('game_name' => 'gd.RNG14396',
		// 				'english_name' => 'Great China',
		// 				'external_game_id' => 'RNG14396',
		// 				'game_code' => 'RNG14396'
		// 				),
		// 				array('game_name' => 'gd.RNG14397',
		// 				'english_name' => 'Great Stars',
		// 				'external_game_id' => 'RNG14397',
		// 				'game_code' => 'RNG14397'
		// 				),
		// 				array('game_name' => 'gd.RNG14398',
		// 				'english_name' => 'Ice Land',
		// 				'external_game_id' => 'RNG14398',
		// 				'game_code' => 'RNG14398'
		// 				),
		// 				array('game_name' => 'gd.RNG14399',
		// 				'english_name' => 'Indian Myth',
		// 				'external_game_id' => 'RNG14399',
		// 				'game_code' => 'RNG14399'
		// 				),
		// 				array('game_name' => 'gd.RNG14400',
		// 				'english_name' => 'Japan Fortune',
		// 				'external_game_id' => 'RNG14400',
		// 				'game_code' => 'RNG14400'
		// 				),
		// 				array('game_name' => 'gd.RNG14403',
		// 				'english_name' => 'Lava Island',
		// 				'external_game_id' => 'RNG14403',
		// 				'game_code' => 'RNG14403'
		// 				),
		// 				array('game_name' => 'gd.RNG14402',
		// 				'english_name' => 'Lion Heart',
		// 				'external_game_id' => 'RNG14402',
		// 				'game_code' => 'RNG14402'
		// 				),
		// 				array('game_name' => 'gd.RNG14401',
		// 				'english_name' => 'Lion Emperor',
		// 				'external_game_id' => 'RNG14401',
		// 				'game_code' => 'RNG14401'
		// 				),
		// 				array('game_name' => 'gd.RNG14405',
		// 				'english_name' => 'Monster Tunnel',
		// 				'external_game_id' => 'RNG14405',
		// 				'game_code' => 'RNG14405'
		// 				),
		// 				array('game_name' => 'gd.RNG14387',
		// 				'english_name' => 'Big Foot',
		// 				'external_game_id' => 'RNG14387',
		// 				'game_code' => 'RNG14387'
		// 				),
		// 				array('game_name' => 'gd.RNG14407',
		// 				'english_name' => 'Pets World',
		// 				'external_game_id' => 'RNG14407',
		// 				'game_code' => 'RNG14407'
		// 				),
		// 				array('game_name' => 'gd.RNG14411',
		// 				'english_name' => 'Spartan',
		// 				'external_game_id' => 'RNG14411',
		// 				'game_code' => 'RNG14411'
		// 				),
		// 				array('game_name' => 'gd.RNG14409',
		// 				'english_name' => 'Stone Ages',
		// 				'external_game_id' => 'RNG14409',
		// 				'game_code' => 'RNG14409'
		// 				),
		// 				array('game_name' => 'gd.RNG14404',
		// 				'english_name' => 'Magic Hammer',
		// 				'external_game_id' => 'RNG14404',
		// 				'game_code' => 'RNG14404'
		// 				),
		// 				array('game_name' => 'gd.RNG14412',
		// 				'english_name' => 'The Song of China',
		// 				'external_game_id' => 'RNG14412',
		// 				'game_code' => 'RNG14412'
		// 				),
		// 				array('game_name' => 'gd.RNG14413',
		// 				'english_name' => 'Wong Choy',
		// 				'external_game_id' => 'RNG14413',
		// 				'game_code' => 'RNG14413'
		// 				),
		// 				array('game_name' => 'gd.RNG14414',
		// 				'english_name' => 'Wu Fu Men',
		// 				'external_game_id' => 'RNG14414',
		// 				'game_code' => 'RNG14414'
		// 				),
		// 			),
		// 		),
		// 		array(
		// 			'game_type' => 'Gamble',
		// 			'game_type_lang' => 'gd_gamble',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_TRUE,
		// 			'game_description_list' => array(

		// 				//Gamble
		// 				array('game_name' => 'gd.RNG4563',
		// 				'english_name' => 'Gamble - Beat the Dealer',
		// 				'external_game_id' => 'RNG4563',
		// 				'game_code' => 'RNG4563'
		// 				),
		// 			),
		// 		),
		// 		array(
		// 			'game_type' => 'Slot Games',
		// 			'game_type_lang' => 'gd_slotgames',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_TRUE,
		// 			'game_description_list' => array(

		// 				//Arcade
		// 				array('game_name' => 'gd.RNG14374',
		// 				'english_name' => 'Animal Paradise',
		// 				'external_game_id' => 'RNG14374',
		// 				'game_code' => 'RNG14374'
		// 				),
		// 				array('game_name' => 'gd.RNG14376',
		// 				'english_name' => 'Derby bicycle',
		// 				'external_game_id' => 'RNG14376',
		// 				'game_code' => 'RNG14376'
		// 				),
		// 				array('game_name' => 'gd.RNG14378',
		// 				'english_name' => 'Derby Express',
		// 				'external_game_id' => 'RNG14378',
		// 				'game_code' => 'RNG14378'
		// 				),
		// 				array('game_name' => 'gd.RNG14379',
		// 				'english_name' => 'Derby Night',
		// 				'external_game_id' => 'RNG14379',
		// 				'game_code' => 'RNG14379'
		// 				),
		// 				array('game_name' => 'gd.RNG14377',
		// 				'english_name' => 'Derby dog',
		// 				'external_game_id' => 'RNG14377',
		// 				'game_code' => 'RNG14377'
		// 				),
		// 				array('game_name' => 'gd.RNG14375',
		// 				'english_name' => 'Derby bike',
		// 				'external_game_id' => 'RNG14375',
		// 				'game_code' => 'RNG14375'
		// 				),
		// 				array('game_name' => 'gd.RNG14383',
		// 				'english_name' => 'Lucky Baby',
		// 				'external_game_id' => 'RNG14383',
		// 				'game_code' => 'RNG14383'
		// 				),
		// 				array('game_name' => 'gd.RNG14382',
		// 				'english_name' => 'Journey To The West',
		// 				'external_game_id' => 'RNG14382',
		// 				'game_code' => 'RNG14382'
		// 				),
		// 				array('game_name' => 'gd.RNG14384',
		// 				'english_name' => 'Monkey Thunderbolt',
		// 				'external_game_id' => 'RNG14384',
		// 				'game_code' => 'RNG14384'
		// 				),
		// 		 	),
		// 		),
		// 		array(
		// 			'game_type' => 'unknown',
		// 			'game_type_lang' => 'gd.unknown',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_FALSE,
		// 			'game_description_list' => array(
		// 				array('game_name' => 'gd.unknown',
		// 					'english_name' => 'Unknown GD Game',
		// 					'external_game_id' => 'unknown',
		// 					'game_code' => 'unknown',
		// 				),
		// 			),
		// 		),
		// 	);

		// 	$game_description_list = array();
		// 	foreach ($data as $game_type) {
		// 		$this->db->insert('game_type', array(
		// 			'game_platform_id' => GD_API,
		// 			'game_type' => $game_type['game_type'],
		// 			'game_type_lang' => $game_type['game_type_lang'],
		// 			'status' => $game_type['status'],
		// 			'flag_show_in_site' => $game_type['flag_show_in_site'],
		// 		));

		// 		$game_type_id = $this->db->insert_id();
		// 		foreach ($game_type['game_description_list'] as $game_description) {
		// 			$game_description_list[] = array_merge(array(
		// 				'game_platform_id' => GD_API,
		// 				'game_type_id' => $game_type_id,
		// 			), $game_description);
		// 		}
		// 	}

		// 	$this->db->insert_batch('game_description', $game_description_list);
		// 	$this->db->trans_complete();
		// }
	}

	public function down() {
		// $this->db->trans_start();
		// $this->db->delete('game_type', array('game_platform_id' => GD_API));
		// $this->db->delete('game_description', array('game_platform_id' => GD_API));
		// $this->db->trans_complete();
	}
}