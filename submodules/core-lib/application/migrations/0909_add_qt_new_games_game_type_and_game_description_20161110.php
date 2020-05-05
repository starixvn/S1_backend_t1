<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_qt_new_games_game_type_and_game_description_20161110 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {
		// $this->db->trans_start();
		// $data = array(
		// 	array(
		// 		'game_type' => '_json:{"1":"5 Reel","2":"5转轴"}',
		// 		'game_type_lang' => '_json:{"1":"5 Reel","2":"5转轴"}',
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_TRUE,
		// 		'game_description_list' => array(
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'QS-dragonshrine',
		// 				'game_name' => '_json:{"1":"Dragon Shrine","2":"龙神殿"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'QS-dragonshrine',
		// 				'english_name' => 'Dragon Shrine',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'ELK-wildtoro',
		// 				'game_name' => '_json:{"1":"Wild Toro","2":"卑鄙的斗牛士"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_FALSE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'ELK-wildtoro',
		// 				'english_name' => 'Wild Toro',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'OGS-bloodlorevampireclan',
		// 				'game_name' => '_json:{"1":"Blood Lore Vampire Clan","2":"吸血鬼家族"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'OGS-bloodlorevampireclan',
		// 				'english_name' => 'Blood Lore Vampire Clan',)
		// 			,
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'OGS-charmsandwitches',
		// 				'game_name' => '_json:{"1":"Charms and Witches","2":"女巫的魔咒"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'OGS-charmsandwitches',
		// 				'english_name' => 'Charms and Witches',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'OGS-starquest',
		// 				'game_name' => '_json:{"1":"Star Quest","2":"星际探索"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_FALSE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'OGS-starquest',
		// 				'english_name' => 'Star Quest',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'OGS-froggrog',
		// 				'game_name' => '_json:{"1":"Frog Grog","2":"格罗格醉蛙"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'OGS-froggrog',
		// 				'english_name' => 'Frog Grog',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'HAB-oceanscall',
		// 				'game_name' => '_json:{"1":"Ocean’s Call","2":"海洋之音"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'HAB-oceanscall',
		// 				'english_name' => 'Ocean’s Call',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'ELK-bloopers',
		// 				'game_name' => '_json:{"1":"Bloopers","2":"幕后花絮"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_FALSE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'ELK-bloopers',
		// 				'english_name' => 'Bloopers',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'ELK-championsgoal',
		// 				'game_name' => '_json:{"1":"Champions Goal","2":"足球宝贝"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_FALSE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'ELK-championsgoal',
		// 				'english_name' => 'Champions Goal',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'ELK-djwild',
		// 				'game_name' => '_json:{"1":"DJ Wild","2":"狂野DJ"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_FALSE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'ELK-djwild',
		// 				'english_name' => 'DJ Wild',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'ELK-electricsam',
		// 				'game_name' => '_json:{"1":"Electric Sam","2":"美女和野兽"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_FALSE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'ELK-electricsam',
		// 				'english_name' => 'Electric Sam',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'ELK-poltava',
		// 				'game_name' => '_json:{"1":"Poltava","2":"波尔塔瓦之战"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_FALSE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'ELK-poltava',
		// 				'english_name' => 'Poltava',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'ELK-tacobrothers',
		// 				'game_name' => '_json:{"1":"Taco Brothers","2":"塔科兄弟"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_FALSE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'ELK-tacobrothers',
		// 				'english_name' => 'Taco Brothers',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'ELK-thelab',
		// 				'game_name' => '_json:{"1":"The Lab","2":"神奇实验室"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_FALSE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'ELK-thelab',
		// 				'english_name' => 'The Lab',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'QS-geniestouch',
		// 				'game_name' => '_json:{"1":"Genie’s Touch","2":"阿拉丁神灯"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'QS-geniestouch',
		// 				'english_name' => 'Genie’s Touch',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'QS-jewelblast',
		// 				'game_name' => '_json:{"1":"Jewel Blast","2":"宝石爆炸"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'QS-jewelblast',
		// 				'english_name' => 'Jewel Blast',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'QS-kingcolossus',
		// 				'game_name' => '_json:{"1":"King Colossus","2":"国王巨像"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'QS-kingcolossus',
		// 				'english_name' => 'King Colossus',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'QS-secondstrike',
		// 				'game_name' => '_json:{"1":"Second Strike","2":"连环炮"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'QS-secondstrike',
		// 				'english_name' => 'Second Strike',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'OGS-fiestacubana',
		// 				'game_name' => '_json:{"1":"Fiesta Cubana","2":"热辣古巴"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'OGS-fiestacubana',
		// 				'english_name' => 'Fiesta Cubana',
		// 			),
		// 		),
		// 	),
		// 	array(
		// 		'game_type' => '_json:{"1":"Baccarat","2":"百家乐"}',
		// 		'game_type_lang' => '_json:{"1":"Baccarat","2":"百家乐"}',
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_TRUE,
		// 		'game_description_list' => array(
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'EHAB-baccarat',
		// 				'game_name' => '_json:{"1":"Baccarat","2":"美国百家乐"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'EHAB-baccarat',
		// 				'english_name' => 'Baccarat',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'HAB-baccarat',
		// 				'game_name' => '_json:{"1":"Baccarat Zero Commission","2":"免佣百家乐"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'HAB-baccarat',
		// 				'english_name' => 'Baccarat Zero Commission',
		// 			),
		// 		),
		// 	),
		// 	array(
		// 		'game_type' => '_json:{"1":"Blackjack","2":"二十一点"}',
		// 		'game_type_lang' => '_json:{"1":"Blackjack","2":"二十一点"}',
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_TRUE,
		// 		'game_description_list' => array(
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'HAB-3handblackjack',
		// 				'game_name' => '_json:{"1":"3 Hand Blackjack","2":"三手黑杰克"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'HAB-3handblackjack',
		// 				'english_name' => '3 Hand Blackjack',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'HAB-3handblackjackdoubleexposure',
		// 				'game_name' => '_json:{"1":"3 Hand Blackjack Double Exposure","2":"三手黑杰克双重曝光"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'HAB-3handblackjackdoubleexposure',
		// 				'english_name' => '3 Hand Blackjack Double Exposure',
		// 			),
		// 		),
		// 	),
		// 	array(
		// 		'game_type' => '_json:{"1":"Poker","2":"扑克"}',
		// 		'game_type_lang' => '_json:{"1":"Poker","2":"扑克"}',
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_TRUE,
		// 		'game_description_list' => array(
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'HAB-caribbeanholdem',
		// 				'game_name' => '_json:{"1":"Caribbean Holdem","2":"赌场德州扑克"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'HAB-caribbeanholdem',
		// 				'english_name' => 'Caribbean Holdem',
		// 			),
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'HAB-caribbeanstud',
		// 				'game_name' => '_json:{"1":"Caribbean Stud","2":"加勒比扑克"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'HAB-caribbeanstud',
		// 				'english_name' => 'Caribbean Stud',
		// 			),
		// 		),
		// 	),
		// 	array(
		// 		'game_type' => '_json:{"1":"Roulette","2":"轮盘"}',
		// 		'game_type_lang' => '_json:{"1":"Roulette","2":"轮盘"}',
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_TRUE,
		// 		'game_description_list' => array(
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'HAB-europeanroulette',
		// 				'game_name' => '_json:{"1":"European Roulette","2":"欧洲轮盘"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'HAB-europeanroulette',
		// 				'english_name' => 'European Roulette',
		// 			),
		// 		),
		// 	),
		// 	array(
		// 		'game_type' => '_json:{"1":"Sic Bo","2":"骰宝"}',
		// 		'game_type_lang' => '_json:{"1":"Sic Bo","2":"骰宝"}',
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_TRUE,
		// 		'game_description_list' => array(
		// 			array(
		// 				'game_platform_id' => QT_API,
		// 				'game_code' => 'HAB-sicbo',
		// 				'game_name' => '_json:{"1":"Sic Bo","2":"骰宝"}',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'flash_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'external_game_id' => 'HAB-europeanroulette',
		// 				'english_name' => 'Sic Bo',
		// 			),
		// 		),
		// 	),
		// );

		// $game_description_list = array();
		// foreach ($data as $game_type) {

		// 	$game_type_exist = $this->db->select('COUNT(1) as count')
		// 						 	->where('game_type_lang', $game_type['game_type_lang'])
		// 						 	->where('game_platform_id', QT_API)
		// 						 	->get('game_type')
		// 				 		 	->row();

		// 	if( $game_type_exist->count <= 0 ){

		// 		$this->db->insert('game_type', array(
		// 			'game_platform_id' => QT_API,
		// 			'game_type' => $game_type['game_type'],
		// 			'game_type_lang' => $game_type['game_type_lang'],
		// 			'status' => $game_type['status'],
		// 			'flag_show_in_site' => $game_type['flag_show_in_site'],
		// 		));

		// 	}

		// 	$game_type_id = $this->db->insert_id();

		// 	foreach ($game_type['game_description_list'] as $game_description) {

		// 		$game_desc_exist = $this->db->select('COUNT(1) as count')
		// 						 	->where('game_code', $game_description['game_code'])
		// 						 	->where('game_platform_id', QT_API)
		// 						 	->get('game_description')
		// 				 		 	->row();

		// 		if( $game_desc_exist->count > 0 ) continue;

		// 		$game_description_list[] = array_merge(array(
		// 			'game_platform_id' => QT_API,
		// 			'game_type_id' => $game_type_id,
		// 		), $game_description);
		// 	}

		// }

		// if( ! empty( $game_description_list ) ) $this->db->insert_batch('game_description', $game_description_list);

		// $this->db->trans_complete();

	}
	public function down() {

		// $this->db->trans_start();
		// $this->db->delete('game_type', array('game_platform_id' => QT_API));
		// $this->db->delete('game_description', array('game_platform_id' => QT_API));
		// $this->db->trans_complete();

	}
}