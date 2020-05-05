<?php
trait onesgame_list {

	function insert_onesgame_list() {
		//insert to game_description
		$data = array(
			array(
				'game_type' => 'Slots',
				'game_type_lang' => 'onesgame_slots',
				'status' => self::FLAG_TRUE,
				'flag_show_in_site' => self::FLAG_TRUE,
				'game_description_list' => array(
					array('game_name' => 'onesgame.GS8_500',
						'english_name' => 'Zeus D\'Mighty',
						'external_game_id' => 'GS8_500',
						'game_code' => 'GS8_500',
					),

					array('game_name' => 'onesgame.GS6_400',
						'english_name' => 'Jingle Winnings',
						'external_game_id' => 'GS6_400',
						'game_code' => 'GS6_400',
					),

					array('game_name' => 'onesgame.GS5_302',
						'english_name' => 'Shoot',
						'external_game_id' => 'GS5_302',
						'game_code' => 'GS5_302',
					),

					array('game_name' => 'onesgame.GS5_301',
						'english_name' => 'Tornado Farm Escape',
						'external_game_id' => 'GS5_301',
						'game_code' => 'GS5_301',
					),

					array('game_name' => 'onesgame.GS5_300',
						'english_name' => 'Go Bananas',
						'external_game_id' => 'GS5_300',
						'game_code' => 'GS5_300',
					),

					array('game_name' => 'onesgame.GS5_303',
						'english_name' => 'Phantom Cash',
						'external_game_id' => 'GS5_303',
						'game_code' => 'GS5_303',
					),

					array('game_name' => 'onesgame.GS5_304',
						'english_name' => '3 Tigers',
						'external_game_id' => 'GS5_304',
						'game_code' => 'GS5_304',
					),

					array('game_name' => 'onesgame.GS4_201',
						'english_name' => 'Lucky Angler',
						'external_game_id' => 'GS4_201',
						'game_code' => 'GS4_201',
					),

					array('game_name' => 'onesgame.GS4_200',
						'english_name' => 'Chinese New Year',
						'external_game_id' => 'GS4_200',
						'game_code' => 'GS4_200',
					),

					array('game_name' => 'onesgame.GS3_100',
						'english_name' => 'Santa\’s Surprise',
						'external_game_id' => 'GS3_100',
						'game_code' => 'GS3_100',
					),

					array('game_name' => 'onesgame.GS3_101',
						'english_name' => 'Georgie Porgie',
						'external_game_id' => 'GS3_101',
						'game_code' => 'GS3_101',
					),

					array('game_name' => 'onesgame.GS3_102',
						'english_name' => 'Jack and Jill',
						'external_game_id' => 'GS3_102',
						'game_code' => 'GS3_102',
					),

					array('game_name' => 'onesgame.GS3_103',
						'english_name' => 'Wild Birthday Blast',
						'external_game_id' => 'GS3_103',
						'game_code' => 'GS3_103',
					),

					array('game_name' => 'onesgame.GS3_104',
						'english_name' => 'Karate Pig',
						'external_game_id' => 'GS3_104',
						'game_code' => 'GS3_104',
					),

					array('game_name' => 'onesgame.GS3_105',
						'english_name' => 'Throne of Egypt',
						'external_game_id' => 'GS3_105',
						'game_code' => 'GS3_105',
					),

					array('game_name' => 'onesgame.GS3_106',
						'english_name' => 'Dolphin Quest',
						'external_game_id' => 'GS3_106',
						'game_code' => 'GS3_106',
					),

					array('game_name' => 'onesgame.GS3_107',
						'english_name' => 'Fish Party',
						'external_game_id' => 'GS3_107',
						'game_code' => 'GS3_107',
					),

					array('game_name' => 'onesgame.GS1_0',
						'english_name' => 'Wild Turkey',
						'external_game_id' => 'GS1_0',
						'game_code' => 'GS1_0',
					),

					array('game_name' => 'onesgame.GS1_1',
						'english_name' => 'Jungle Games',
						'external_game_id' => 'GS1_1',
						'game_code' => 'GS1_1',
					),

					array('game_name' => 'onesgame.GS1_2',
						'english_name' => 'Subtopia',
						'external_game_id' => 'GS1_2',
						'game_code' => 'GS1_2',
					),

					array('game_name' => 'onesgame.GS1_3',
						'english_name' => 'Fisticuffs',
						'external_game_id' => 'GS1_3',
						'game_code' => 'GS1_3',
					),

					array('game_name' => 'onesgame.GS1_4',
						'english_name' => 'SouthPark',
						'external_game_id' => 'GS1_4',
						'game_code' => 'GS1_4',
					),

					array('game_name' => 'onesgame.GS1_5',
						'english_name' => 'Trolls',
						'external_game_id' => 'GS1_5',
						'game_code' => 'GS1_5',
					),

					array('game_name' => 'onesgame.GS1_6',
						'english_name' => 'Egyptian Heroes',
						'external_game_id' => 'GS1_6',
						'game_code' => 'GS1_6',
					),

					array('game_name' => 'onesgame.GS1_7',
						'english_name' => 'Flowers',
						'external_game_id' => 'GS1_7',
						'game_code' => 'GS1_7',
					),

					array('game_name' => 'onesgame.GS2_DolphinCash',
						'english_name' => 'Dolphin Cash',
						'external_game_id' => 'GS2_DolphinCash',
						'game_code' => 'GS2_DolphinCash',
					),

					array('game_name' => 'onesgame.GS2_LottoMadness',
						'english_name' => 'Lotto Madness',
						'external_game_id' => 'GS2_LottoMadness',
						'game_code' => 'GS2_LottoMadness',
					),

					array('game_name' => 'onesgame.GS2_HalloweenFortune',
						'english_name' => 'Halloween Fortune',
						'external_game_id' => 'GS2_HalloweenFortune',
						'game_code' => 'GS2_HalloweenFortune',
					),

					array('game_name' => 'onesgame.GS2_WildSpirit',
						'english_name' => 'Wild Spirit',
						'external_game_id' => 'GS2_WildSpirit',
						'game_code' => 'GS2_WildSpirit',
					),

					array('game_name' => 'onesgame.GS2_EasterSurprise',
						'english_name' => 'Easter Surprise',
						'external_game_id' => 'GS2_EasterSurprise',
						'game_code' => 'GS2_EasterSurprise',
					),

					array('game_name' => 'onesgame.GS2_MrCashBack',
						'english_name' => 'MrCashBack',
						'external_game_id' => 'GS2_MrCashBack',
						'game_code' => 'GS2_MrCashBack',
					),

					array('game_name' => 'onesgame.GS2_ANightOut',
						'english_name' => 'ANightOut',
						'external_game_id' => 'GS2_ANightOut',
						'game_code' => 'GS2_ANightOut',
					),

					array('game_name' => 'onesgame.GS2_GreatBlue',
						'english_name' => 'Great Blue',
						'external_game_id' => 'GS2_GreatBlue',
						'game_code' => 'GS2_GreatBlue',
					),

					array('game_name' => 'onesgame.GS2_StashOfTitans',
						'english_name' => 'Stash Of Titans',
						'external_game_id' => 'GS2_StashOfTitans',
						'game_code' => 'GS2_StashOfTitans',
					),

					array('game_name' => 'onesgame.GS2_SantaPaws',
						'english_name' => 'SantaPaws',
						'external_game_id' => 'GS2_SantaPaws',
						'game_code' => 'GS2_SantaPaws',
					),

					array(
						'game_name' => 'onesgame.GS2_GoldFactory',
						'english_name' => 'Gold Factory',
						'external_game_id' => 'GS2_GoldFactory',
						'game_code' => 'GS2_GoldFactory',
					),

					array(
						'game_name' => 'onesgame.GS2_Mermaid',
						'english_name' => 'Mermaid Millions',
						'external_game_id' => 'GS2_Mermaid',
						'game_code' => 'GS2_Mermaid',
					),

					array(
						'game_name' => 'onesgame.GS2_Kathmandu',
						'english_name' => 'Kathmandu',
						'external_game_id' => 'GS2_Kathmandu',
						'game_code' => 'GS2_Kathmandu',
					),

					array('game_name' => 'onesgame.GS2_Avalon',
						'english_name' => 'Avalon',
						'external_game_id' => 'GS2_Avalon',
						'game_code' => 'GS2_Avalon',
					),

					array('game_name' => 'onesgame.GS2_VentureED',
						'english_name' => 'ED\'s Venture',
						'external_game_id' => 'GS2_VentureED',
						'game_code' => 'GS2_VentureED',
					),

					array('game_name' => 'onesgame.GS2_Shangrila',
						'english_name' => 'Shangrila',
						'external_game_id' => 'GS2_Shangrila',
						'game_code' => 'GS2_Shangrila',
					),

					array('game_name' => 'onesgame.GS2_Battleground',
						'english_name' => 'Battleground',
						'external_game_id' => 'GS2_Battleground',
						'game_code' => 'GS2_Battleground',
					),

					array('game_name' => 'onesgame.GS2_Monopoly',
						'english_name' => 'Monopoly',
						'external_game_id' => 'GS2_Monopoly',
						'game_code' => 'GS2_Monopoly',
					),

					array('game_name' => 'onesgame.GS2_Scrooges',
						'english_name' => 'Scrooges',
						'external_game_id' => 'GS2_Scrooges',
						'game_code' => 'GS2_Scrooges',
					),

					array('game_name' => 'onesgame.GS2_IceHockey',
						'english_name' => 'Ice Hockey',
						'external_game_id' => 'GS2_IceHockey',
						'game_code' => 'GS2_IceHockey',
					),

					array('game_name' => 'onesgame.GS2_BustTheBank',
						'english_name' => 'Bust The Bank',
						'external_game_id' => 'GS2_BustTheBank',
						'game_code' => 'GS2_BustTheBank',
					),

					array('game_name' => 'onesgame.GS2_FeatherFrenzy',
						'english_name' => 'Feather Frenzy',
						'external_game_id' => 'GS2_FeatherFrenzy',
						'game_code' => 'GS2_FeatherFrenzy',
					),

					array('game_name' => 'onesgame.GS2_ChoySunDoa',
						'english_name' => 'Choy Sun Doa',
						'external_game_id' => 'GS2_ChoySunDoa',
						'game_code' => 'GS2_ChoySunDoa',
					),

					array('game_name' => 'onesgame.GS2_Lucky88',
						'english_name' => 'Lucky 88',
						'external_game_id' => 'GS2_Lucky88',
						'game_code' => 'GS2_Lucky88',
					),

					array('game_name' => 'onesgame.GS2_IncredibleHulk',
						'english_name' => 'Incredible Hulk',
						'external_game_id' => 'GS2_IncredibleHulk',
						'game_code' => 'GS2_IncredibleHulk',
					),

					array('game_name' => 'onesgame.GS2_JourneyOfTheSun',
						'english_name' => 'Journey Of The Sun',
						'external_game_id' => 'GS2_JourneyOfTheSun',
						'game_code' => 'GS2_JourneyOfTheSun',
					),

					array('game_name' => 'onesgame.GS2_CopsNBandits',
						'english_name' => 'Cops N\' Bandit',
						'external_game_id' => 'GS2_CopsNBandits',
						'game_code' => 'GS2_CopsNBandits',
					),

					array('game_name' => 'onesgame.GS2_Ironman',
						'english_name' => 'Ironman',
						'external_game_id' => 'GS2_Ironman',
						'game_code' => 'GS2_Ironman',
					),

					array('game_name' => 'onesgame.GS2_Ironman2',
						'english_name' => 'Ironman 2',
						'external_game_id' => 'GS2_Ironman2',
						'game_code' => 'GS2_Ironman2',
					),

					array('game_name' => 'onesgame.GS2_WhereTheGold',
						'english_name' => 'Where\'s The Gold',
						'external_game_id' => 'GS2_WhereTheGold',
						'game_code' => 'GS2_WhereTheGold',
					),

					array('game_name' => 'onesgame.GS2_QueenOfNile2',
						'english_name' => 'Queens Of Nile 2',
						'external_game_id' => 'GS2_QueenOfNile2',
						'game_code' => 'GS2_QueenOfNile2',
					),

					array('game_name' => 'onesgame.GS2_TheDarkKnightRises',
						'english_name' => 'The Dark Knight Rises',
						'external_game_id' => 'GS2_TheDarkKnightRises',
						'game_code' => 'GS2_TheDarkKnightRises',
					),

					array('game_name' => 'onesgame.GS2_Xmen',
						'english_name' => 'Xmen',
						'external_game_id' => 'GS2_Xmen',
						'game_code' => 'GS2_Xmen',
					),
				),
			),
			array(
				'game_type' => 'Table',
				'game_type_lang' => 'onesgame_table',
				'status' => self::FLAG_TRUE,
				'flag_show_in_site' => self::FLAG_TRUE,
				'game_description_list' => array(
					array(
						'game_name' => 'onesgame.GS2_SicBo',
						'english_name' => 'SicBo',
						'external_game_id' => 'GS2_SicBo',
						'game_code' => 'GS2_SicBo',
					),

					array('game_name' => 'onesgame.GS2_Roulette',
						'english_name' => 'Roulette',
						'external_game_id' => 'GS2_Roulette',
						'game_code' => 'GS2_Roulette',
					),

					array('game_name' => 'onesgame.GS2_Baccarat',
						'english_name' => 'Baccarat',
						'external_game_id' => 'GS2_Baccarat',
						'game_code' => 'GS2_Baccarat',
					),
				),
			),
			array(
				'game_type' => 'unknown',
				'game_type_lang' => 'onesgame.unknown',
				'status' => self::FLAG_TRUE,
				'flag_show_in_site' => self::FLAG_FALSE,
				'game_description_list' => array(
					array(
						'game_name' => 'unknown',
						'english_name' => 'unknown',
						'external_game_id' => 'unknown',
						'game_code' => 'unknown',
					),
				),
			),

		);

		$game_description_list = array();
		foreach ($data as $game_type) {
			$this->db->insert('game_type', array(
				'game_platform_id' => ONESGAME_API,
				'game_type' => $game_type['game_type'],
				'game_type_lang' => $game_type['game_type_lang'],
				'status' => $game_type['status'],
				'flag_show_in_site' => $game_type['flag_show_in_site'],
			));

			$game_type_id = $this->db->insert_id();
			foreach ($game_type['game_description_list'] as $game_description) {
				$game_description_list[] = array_merge(array(
					'game_platform_id' => ONESGAME_API,
					'game_type_id' => $game_type_id,
				), $game_description);
			}
		}

		$this->db->insert_batch('game_description', $game_description_list);

	}
}
///END OF FILE/////////////