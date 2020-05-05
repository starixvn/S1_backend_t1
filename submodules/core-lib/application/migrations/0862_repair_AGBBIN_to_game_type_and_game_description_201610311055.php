<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_repair_AGBBIN_to_game_type_and_game_description_201610311055 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {

		// $this->db->trans_start();

		// $this->db->delete('game_type', array('game_platform_id' => AGBBIN_API, 'game_type !='=> 'unknown'));
		//    $this->db->delete('game_description', array('game_platform_id' => AGBBIN_API,'game_name !='=> 'agbbin.unknown'));

		//    $data = array(

		// 	    	array(
		//            	    'game_type' => 'AG BB Sports',
		// 				'game_type_lang' => 'agbbin_sports',
		// 				'status' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'game_description_list' => array(

		// 					array('game_name' => '_json:{"1":"Football","2":"足球"}',
		// 						'english_name' => 'Football',
		// 						'external_game_id' => 'FT',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Basketball","2":"篮球"}',
		// 						'english_name' => 'Basketball',
		// 						'external_game_id' => 'BK',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"American Football","2":"美式足球"}',
		// 						'english_name' => 'American Football',
		// 						'external_game_id' => 'FB',
		// 						'game_code' => ''
		// 						),

		// 					array('game_name' => '_json:{"1":"IceHockey","2":"冰球"}',
		// 						'english_name' => 'IceHockey',
		// 						'external_game_id' => 'IH',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Tennis","2":"网球"}',
		// 						'english_name' => 'Tennis',
		// 						'external_game_id' => 'TN',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Other","2":"其他"}',
		// 						'english_name' => 'Other',
		// 						'external_game_id' => 'F1',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Outright","2":"冠军赛"}',
		// 						'english_name' => 'Outright',
		// 						'external_game_id' => 'SP',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Combo Parlay","2":"混合过关"}',
		// 						'english_name' => 'Combo Parlay',
		// 						'external_game_id' => 'CB',
		// 						'game_code' => ''
		// 						),
		// 					),
		// 				),

		// 	    		array(
		//            	    'game_type' => 'AG BB Live Dealer',
		// 				'game_type_lang' => 'agbbin_live_dealer',
		// 				'status' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'game_description_list' => array(

		// 					array('game_name' => '_json:{"1":"Baccarat","2":"百家乐"}',
		// 						'english_name' => 'Baccarat',
		// 						'external_game_id' => '3001',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Mahjong Tiles","2":"二八杠"}',
		// 						'english_name' => 'Mahjong Tiles',
		// 						'external_game_id' => '3002',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Dragon Tiger","2":"龙虎斗"}',
		// 						'english_name' => 'Dragon Tiger',
		// 						'external_game_id' => '3003',
		// 						'game_code' => ''
		// 						),

		// 					array('game_name' => '_json:{"1":"3 Face","2":"三公"}',
		// 						'english_name' => '3 Face',
		// 						'external_game_id' => '3005',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Wenzhou Pai Gow","2":"温州牌九"}',
		// 						'english_name' => 'Wenzhou Pai Gow',
		// 						'external_game_id' => '3006',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Roulette","2":"轮盘"}',
		// 						'english_name' => 'Roulette',
		// 						'external_game_id' => '3007',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Sic Bo","2":"骰宝"}',
		// 						'english_name' => 'Sic Bo',
		// 						'external_game_id' => '3008',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Texas Hold \' em","2":"德州扑克"}',
		// 						'english_name' => 'Texas Hold \' em',
		// 						'external_game_id' => '3010',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Se Die","2":"色碟"}',
		// 						'english_name' => 'Se Die',
		// 						'external_game_id' => '3011',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Bull Bull","2":"牛牛"}',
		// 						'english_name' => 'Bull Bull',
		// 						'external_game_id' => '3012',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Unlimited Blackjack","2":"无限 21 点"}',
		// 						'english_name' => 'Unlimited Blackjack',
		// 						'external_game_id' => '3014',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Fan Tan","2":"番摊"}',
		// 						'english_name' => 'Fan Tan',
		// 						'external_game_id' => '3015',
		// 						'game_code' => ''
		// 						),
		// 					),
		// 				),

		// 				array(
		//            	    'game_type' => 'AG BB Casino',
		// 				'game_type_lang' => 'agbbin_casino',
		// 				'status' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'game_description_list' => array(

		// 					array('game_name' => '_json:{"1":"Alien War","2":"惑星战记"}',
		// 						'english_name' => 'Alien War',
		// 						'external_game_id' => '5005',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Staronic","2":"Staronic"}',
		// 						'english_name' => 'Staronic',
		// 						'external_game_id' => '5006',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Fruits Boom","2":"激爆水果盘"}',
		// 						'english_name' => 'Fruits Boom',
		// 						'external_game_id' => '5007',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Monkey GoGo","2":"猴子爬树"}',
		// 						'english_name' => 'Monkey GoGo',
		// 						'external_game_id' => '5008',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"King Kong","2":"金刚爬楼"}',
		// 						'english_name' => 'King Kong',
		// 						'external_game_id' => '5009',
		// 						'game_code' => ''
		// 						),
		// 				    array('game_name' => '_json:{"1":"Galaxy II","2":"外星战记"}',
		// 						'english_name' => 'Galaxy II',
		// 						'external_game_id' => '5010',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Galaxy","2":"外星争霸"}',
		// 						'english_name' => 'Galaxy',
		// 						'external_game_id' => '5012',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Classic","2":"传统"}',
		// 						'english_name' => 'Classic',
		// 						'external_game_id' => '5013',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Jungle","2":"丛林"}',
		// 						'english_name' => 'Jungle',
		// 						'external_game_id' => '5014',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"FIFA2010","2":"FIFA2010"}',
		// 						'english_name' => 'FIFA2010',
		// 						'external_game_id' => '5015',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Prehistoric Jungle","2":"史前丛林冒险"}',
		// 						'english_name' => 'Prehistoric Jungle',
		// 						'external_game_id' => '5016',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Star Wars","2":"星际大战"}',
		// 						'english_name' => 'Star Wars',
		// 						'external_game_id' => '5017',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Monkey King","2":"齐天大圣"}',
		// 						'english_name' => 'Monkey King',
		// 						'external_game_id' => '5018',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Fruit Paradise","2":"水果乐园"}',
		// 						'english_name' => 'Fruit Paradise',
		// 						'external_game_id' => '5019',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Tropical Fruit","2":"热带风情"}',
		// 						'english_name' => 'Tropical Fruit',
		// 						'external_game_id' => '5020',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"White Snake","2":"法海斗白蛇"}',
		// 						'english_name' => 'White Snake',
		// 						'external_game_id' => '5025',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"London 2012","2":"2012 伦敦奥运"}',
		// 						'english_name' => 'London 2012',
		// 						'external_game_id' => '5026',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Kung Fu Loung","2":"功夫龙"}',
		// 						'english_name' => 'Kung Fu Loung',
		// 						'external_game_id' => '5027',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Moon Festival Party","2":"中秋月光派对"}',
		// 						'english_name' => 'Moon Festival Party',
		// 						'external_game_id' => '5028',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"X\'mas Party","2":"圣诞派对"}',
		// 						'english_name' => 'X\'mas Party',
		// 						'external_game_id' => '5029',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Chinese Mammon","2":"幸运财神"}',
		// 						'english_name' => 'Chinese Mammon',
		// 						'external_game_id' => '5030',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Joker Poker","2":"王牌 5PK"}',
		// 						'english_name' => 'Joker Poker',
		// 						'external_game_id' => '5034',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Caribbean Poker","2":"加勒比扑克"}',
		// 						'english_name' => 'Caribbean Poker',
		// 						'external_game_id' => '5035',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Fish-Prawn-Crab Dice","2":"鱼虾蟹"}',
		// 						'english_name' => 'Fish-Prawn-Crab Dice',
		// 						'external_game_id' => '5039',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Deuces Wild","2":"D百搭二王"}',
		// 						'english_name' => 'Deuces Wild',
		// 						'external_game_id' => '5040',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"7PK","2":"7PK"}',
		// 						'english_name' => '7PK',
		// 						'external_game_id' => '5041',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Lost Battlefield","2":"异星战场"}',
		// 						'english_name' => 'Lost Battlefield',
		// 						'external_game_id' => '5042',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Diamond Fruits","2":"钻石水果盘"}',
		// 						'english_name' => 'Diamond Fruits',
		// 						'external_game_id' => '5043',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"STAR 97II","2":"明星 97II"}',
		// 						'english_name' => 'STAR 97II',
		// 						'external_game_id' => '5044',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Zombie Land","2":"尸乐园"}',
		// 						'english_name' => 'Zombie Land',
		// 						'external_game_id' => '5047',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Spy Crisis","2":"特务危机"}',
		// 						'english_name' => 'Spy Crisis',
		// 						'external_game_id' => '5048',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Sex And Zen","2":"玉蒲团"}',
		// 						'english_name' => 'Sex And Zen',
		// 						'external_game_id' => '5049',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"War Lady","2":"战火佳人"}',
		// 						'english_name' => 'War Lady',
		// 						'external_game_id' => '5050',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Star97","2":"明星 97"}',
		// 						'english_name' => 'Star97',
		// 						'external_game_id' => '5057',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Crazy Fruit","2":"疯狂水果盘"}',
		// 						'english_name' => 'Crazy Fruit',
		// 						'external_game_id' => '5058',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Fantastic Animals 5th","2":"动物奇观五"}',
		// 						'english_name' => 'Fantastic Animals 5th',
		// 						'external_game_id' => '5060',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Super 7","2":"超级 7"}',
		// 						'english_name' => 'Super 7',
		// 						'external_game_id' => '5061',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Dragon Must Die","2":"龙在囧途"}',
		// 						'english_name' => 'Dragon Must Die',
		// 						'external_game_id' => '5062',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Slot Cool Fruit","2":"水果拉霸"}',
		// 						'english_name' => 'Slot Cool Fruit',
		// 						'external_game_id' => '5063',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Slot Poker","2":"扑克拉霸"}',
		// 						'english_name' => 'Slot Poker',
		// 						'external_game_id' => '5064',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Slot Mahjong Ball","2":"筒子拉霸"}',
		// 						'english_name' => 'Slot Mahjong Ball',
		// 						'external_game_id' => '5065',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Slot Soccer","2":"足球拉霸"}',
		// 						'english_name' => 'Slot Soccer',
		// 						'external_game_id' => '5066',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"A Chinese Odyssey","2":"中国奥德赛"}',
		// 						'english_name' => 'A Chinese Odyssey',
		// 						'external_game_id' => '5067',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Kuso Circus","2":"搞笑马戏团"}',
		// 						'english_name' => ' Kuso Circus',
		// 						'external_game_id' => '5068',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Golden Wheel","2":"黄金大转轮"}',
		// 						'english_name' => 'Golden Wheel',
		// 						'external_game_id' => '5070',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"BaccaratWheel","2":"百家乐大转轮"}',
		// 						'english_name' => 'BaccaratWheel',
		// 						'external_game_id' => '5073',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Lucky Number","2":"数字大转轮"}',
		// 						'english_name' => 'Lucky Number',
		// 						'external_game_id' => '5076',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Fruit","2":"水果大转轮"}',
		// 						'english_name' => 'Fruit',
		// 						'external_game_id' => '5077',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Chess Wheel","2":"象棋大转轮"}',
		// 						'english_name' => 'Chess Wheel',
		// 						'external_game_id' => '5078',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"3D Lucky Number","2":"3D 数字大转轮"}',
		// 						'english_name' => '3D Lucky Number',
		// 						'external_game_id' => '5079',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Lottery Wheel","2":"乐透转轮"}',
		// 						'english_name' => 'Lottery Wheel',
		// 						'external_game_id' => '5080',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Guess Train","2":"钻石列车"}',
		// 						'english_name' => ' Guess Train',
		// 						'external_game_id' => '5083',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Monster Legend","2":"圣兽传说"}',
		// 						'english_name' => 'Monster Legend',
		// 						'external_game_id' => '5084',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Ocean Party","2":"海底派对"}',
		// 						'english_name' => 'Ocean Party',
		// 						'external_game_id' => '5086',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Casino War","2":"斗大"}',
		// 						'english_name' => ' Casino War',
		// 						'external_game_id' => '5088',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Red Dog","2":"红狗"}',
		// 						'english_name' => 'Red Dog',
		// 						'external_game_id' => '5089',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Dynasty Warlord","2":"三国拉霸"}',
		// 						'english_name' => 'Dynasty Warlord',
		// 						'external_game_id' => '5091',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"The Legend and the Hero","2":"封神榜"}',
		// 						'english_name' => 'The Legend and the Hero',
		// 						'external_game_id' => '5092',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Jin Ping Mai","2":"金瓶梅"}',
		// 						'english_name' => ' Jin Ping Mai',
		// 						'external_game_id' => '5093',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Jin Ping Mai 2","2":"金瓶梅 2"}',
		// 						'english_name' => 'Jin Ping Mai 2',
		// 						'external_game_id' => '5094',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Cock Fighting","2":"斗鸡"}',
		// 						'english_name' => 'Cock Fighting',
		// 						'external_game_id' => '5095',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"European Roulette","2":"欧式轮盘"}',
		// 						'english_name' => 'European Roulette',
		// 						'external_game_id' => '5105',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"THREE KINGDOMS","2":"三国"}',
		// 						'english_name' => 'THREE KINGDOMS',
		// 						'external_game_id' => '5016',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"American Roulette","2":"美式轮盘"}',
		// 						'english_name' => 'American Roulette',
		// 						'external_game_id' => '5107',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Jackpot Roulette","2":"奖池轮盘"}',
		// 						'english_name' => 'Jackpot Roulette',
		// 						'external_game_id' => '5108',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"French Roulette","2":"法式轮盘"}',
		// 						'english_name' => 'French Roulette',
		// 						'external_game_id' => '5109',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Classic BlackJack","2":"经典 21 点"}',
		// 						'english_name' => 'Classic BlackJack',
		// 						'external_game_id' => '5115',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Spanish BlackJack","2":"西班牙 21 点"}',
		// 						'english_name' => 'Spanish BlackJack',
		// 						'external_game_id' => '5116',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Vegas BlackJack","2":"维加斯 21 点"}',
		// 						'english_name' => 'Vegas BlackJack',
		// 						'external_game_id' => '5117',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Bonus BlackJack","2":"奖金 21 点"}',
		// 						'english_name' => 'Bonus BlackJack',
		// 						'external_game_id' => '5118',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Royal Texas Hold\'em","2":"皇家德州扑克"}',
		// 						'english_name' => 'Royal Texas Hold\'em',
		// 						'external_game_id' => '5131',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Flaming Mountain","2":"火焰山"}',
		// 						'english_name' => 'Flaming Mountain',
		// 						'external_game_id' => '5201',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Pandora\'s Box","2":"月光宝盒"}',
		// 						'english_name' => 'Pandora\'s Box',
		// 						'external_game_id' => '5202',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Forever Love","2":"爱你一万年"}',
		// 						'english_name' => 'Forever Love',
		// 						'external_game_id' => '5203',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"2014 FIFA","2":"2014 FIFA"}',
		// 						'english_name' => '2014 FIFA',
		// 						'external_game_id' => '5204',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Chinese Hero Lovers","2":"天山侠侣传"}',
		// 						'english_name' => 'Chinese Hero Lovers',
		// 						'external_game_id' => '5401',
		// 						'game_code' => ''
		// 						),

		// 					array('game_name' => '_json:{"1":"Let\'s go Night-market !","2":"夜市人生"}',
		// 						'english_name' => 'Let\s go Night-market !',
		// 						'external_game_id' => '5402',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Legend of 7 Swords","2":"七剑传说"}',
		// 						'english_name' => 'Legend of 7 Swords',
		// 						'external_game_id' => '5403',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Beach Volleyball","2":"沙滩排球"}',
		// 						'english_name' => 'Beach Volleyball',
		// 						'external_game_id' => '5404',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"King of Hidden Weapon","2":"暗器之王"}',
		// 						'english_name' => 'King of Hidden Weapon',
		// 						'external_game_id' => '5405',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Starship27","2":"神舟 27"}',
		// 						'english_name' => 'Starship27',
		// 						'external_game_id' => '5406',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Don´t call me Little Red","2":"大红帽与小野狼"}',
		// 						'english_name' => 'Don´t call me Little Red',
		// 						'external_game_id' => '5407',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Adventure of Mystical Land","2":"秘境冒险"}',
		// 						'english_name' => 'Adventure of Mystical Land',
		// 						'external_game_id' => '5601',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"LinkGem","2":"连连看"}',
		// 						'english_name' => 'LinkGem',
		// 						'external_game_id' => '5701',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"I Am Rich","2":"发达啰"}',
		// 						'english_name' => 'I Am Rich',
		// 						'external_game_id' => '5703',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Bull Fight","2":"斗牛"}',
		// 						'english_name' => 'Bull Fight',
		// 						'external_game_id' => '5704',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Treasure Pot","2":"聚宝盆"}',
		// 						'english_name' => 'Treasure Pot',
		// 						'external_game_id' => '5705',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Chocolate Passion","2":"浓情巧克力"}',
		// 						'english_name' => 'Chocolate Passion',
		// 						'external_game_id' => '5706',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"GOLDEN JAGUAR","2":"金钱豹"}',
		// 						'english_name' => 'GOLDEN JAGUAR',
		// 						'external_game_id' => '5707',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Dolphin Reef","2":"海豚世界"}',
		// 						'english_name' => 'Dolphin Reef',
		// 						'external_game_id' => '5801',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Achilles","2":"阿基里斯"}',
		// 						'english_name' => 'Achilles',
		// 						'external_game_id' => '5802',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Aztec\'s Treasure","2":"阿兹特克宝藏"}',
		// 						'english_name' => 'Aztec\'s Treasure',
		// 						'external_game_id' => '5803',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Big Shot","2":"大明星"}',
		// 						'english_name' => 'Big Shot',
		// 						'external_game_id' => '5804',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Caesar\'s Empire","2":"凯萨帝国"}',
		// 						'english_name' => 'Caesar\'s Empire',
		// 						'external_game_id' => '5805',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Enchanced Garden","2":"奇幻花园"}',
		// 						'english_name' => 'Enchanced Garden',
		// 						'external_game_id' => '5806',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Ronin","2":"浪人武士"}',
		// 						'english_name' => 'Ronin',
		// 						'external_game_id' => '5808',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Tally Ho","2":"空战英豪"}',
		// 						'english_name' => 'Tally Ho',
		// 						'external_game_id' => '5809',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Victory","2":"航海时代"}',
		// 						'english_name' => 'Victory',
		// 						'external_game_id' => '5810',
		// 						'game_code' => ''
		// 						),

		// 					array('game_name' => '_json:{"1":"A Night Out","2":"狂欢夜"}',
		// 						'english_name' => 'A Night Out',
		// 						'external_game_id' => '5811',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Football","2":"国际足球"}',
		// 						'english_name' => 'Footbal',
		// 						'external_game_id' => '5821',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Big Prosperity","2":"发大财"}',
		// 						'english_name' => 'Big Prosperity',
		// 						'external_game_id' => '5823',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Mystic Dragon","2":"恶龙传说"}',
		// 						'english_name' => 'Mystic Dragon',
		// 						'external_game_id' => '5824',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Golden Lotus","2":"金莲"}',
		// 						'english_name' => 'Golden Lotus',
		// 						'external_game_id' => '5825',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Pay Dirt","2":"金矿工"}',
		// 						'english_name' => 'Pay Dirt',
		// 						'external_game_id' => '5826',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Sea Captain","2":"老船长"}',
		// 						'english_name' => 'Sea Captain',
		// 						'external_game_id' => '5827',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"T-Rex","2":"霸王龙"}',
		// 						'english_name' => 'T-Rex',
		// 						'external_game_id' => '5828',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Golden Tour","2":"高球之旅"}',
		// 						'english_name' => 'Golden Tour',
		// 						'external_game_id' => '5831',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Highway Kings","2":"高速卡车"}',
		// 						'english_name' => 'Highway Kings',
		// 						'external_game_id' => '5832',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Silent Samurai","2":"沉默武士"}',
		// 						'english_name' => 'Silent Samurai',
		// 						'external_game_id' => '5833',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Happy Golden Ox Of Happiness","2":"喜福牛年"}',
		// 						'english_name' => 'Happy Golden Ox Of Happiness',
		// 						'external_game_id' => '5835',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Triple Twister","2":"龙卷风"}',
		// 						'english_name' => 'Triple Twister',
		// 						'external_game_id' => '5836',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Happy Golden Monkey Of Happiness","2":"喜福猴年"}',
		// 						'english_name' => 'Happy Golden Monkey Of Happiness',
		// 						'external_game_id' => '5837',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Duo Bao","2":"连环夺宝"}',
		// 						'english_name' => 'Duo Bao',
		// 						'external_game_id' => '5901',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Candy Party","2":"糖果派对"}',
		// 						'english_name' => 'Candy Party',
		// 						'external_game_id' => '5902',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Tomb of Dragon Emperor","2":"秦皇秘宝"}',
		// 						'english_name' => 'Tomb of Dragon Emperor',
		// 						'external_game_id' => '5903',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Pop Bomber","2":"轰炸机"}',
		// 						'english_name' => 'Pop Bomber',
		// 						'external_game_id' => '5904',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Mahjong Bingo","2":"麻将连环宝"}',
		// 						'english_name' => 'Mahjong Bingo',
		// 						'external_game_id' => '5905',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"JACKPOT","2":"JACKPOT"}',
		// 						'english_name' => 'JACKPOT',
		// 						'external_game_id' => '5888',
		// 						'game_code' => ''
		// 						),
		// 					),
		// 				),

		// 				array(
		//            	    'game_type' => 'AG BB Lottery',
		// 				'game_type_lang' => 'agbbin_lottery',
		// 				'status' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'game_description_list' => array(

		// 					array('game_name' => '_json:{"1":"Mark Six","2":"六合彩"}',
		// 						'english_name' => 'Mark Six',
		// 						'external_game_id' => 'LT',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"3D Lotto","2":"3D 彩"}',
		// 						'english_name' => '3D Lotto',
		// 						'external_game_id' => 'BJ3D',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Sports Lotto","2":"排列三"}',
		// 						'english_name' => 'Sports Lotto',
		// 						'external_game_id' => 'PL3D',
		// 						'game_code' => ''
		// 						),

		// 					array('game_name' => '_json:{"1":"BB Raiden PK","2":"BB 雷电 PK"}',
		// 						'english_name' => 'BB Raiden PK',
		// 						'external_game_id' => 'RDPK',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Ladder Game","2":"梯子游戏"}',
		// 						'english_name' => 'Ladder Game',
		// 						'external_game_id' => 'LDDR',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"BB PK3","2":"BB PK3"}',
		// 						'english_name' => 'BB PK3',
		// 						'external_game_id' => 'BBPK',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"BB3D","2":"BB3D"}',
		// 						'english_name' => 'BB3D',
		// 						'external_game_id' => 'BB3D',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"BB KENO","2":"BB 快乐彩"}',
		// 						'english_name' => 'BB KENO',
		// 						'external_game_id' => 'BBKN',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"BB Running","2":"BB 滚球王"}',
		// 						'english_name' => 'BB Running',
		// 						'external_game_id' => 'BBRB',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"BB Bacca Lotto-A","2":"BB 百家彩票-A"}',
		// 						'english_name' => 'BB Bacca Lotto-A',
		// 						'external_game_id' => 'BCRA',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"BB Bacca Lotto-B","2":"BB 百家彩票-B"}',
		// 						'english_name' => 'BB Bacca Lotto-B',
		// 						'external_game_id' => 'BCRB',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"BB Bacca Lotto-C","2":"BB 百家彩票-C"}',
		// 						'english_name' => 'BB Bacca Lotto-C',
		// 						'external_game_id' => 'BCRC',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"BB Bacca Lotto-D","2":"BB 百家彩票-D"}',
		// 						'english_name' => 'BBB Bacca Lotto-D',
		// 						'external_game_id' => 'BCRD',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"BB Bacca Lotto-E","2":"BB 百家彩票-E"}',
		// 						'english_name' => 'BB Bacca Lotto-E',
		// 						'external_game_id' => 'BCRE',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Shanghai Lotto","2":"上海时时乐"}',
		// 						'english_name' => 'Shanghai Lotto',
		// 						'external_game_id' => 'SH3D',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Chongqing Lotto","2":"重庆时时彩"}',
		// 						'english_name' => 'Chongqing Lotto',
		// 						'external_game_id' => 'CQSC',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Tianjin Lotto","2":"天津时时彩"}',
		// 						'english_name' => 'Tianjin Lotto',
		// 						'external_game_id' => 'TJSC',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Xinjiang Lotto","2":"新疆时时彩"}',
		// 						'english_name' => 'Xinjiang Lotto',
		// 						'external_game_id' => 'XJSC',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Chongqing Fortune Farm","2":"重庆幸运农场"}',
		// 						'english_name' => 'Chongqing Fortune Farm',
		// 						'external_game_id' => 'CQSF',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Guangxi Happy 10","2":"广西十分彩"}',
		// 						'english_name' => 'Guangxi Happy 10',
		// 						'external_game_id' => 'GXSF',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Tianjin Happy 10","2":"天津十分彩"}',
		// 						'english_name' => 'Tianjin Happy 10',
		// 						'external_game_id' => 'TJSF',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Beijing PK10","2":"北京 PK 拾"}',
		// 						'english_name' => 'Beijing PK10',
		// 						'external_game_id' => 'BJPK',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"BB Quick Keno","2":"快速彩票"}',
		// 						'english_name' => 'BB Quick Keno',
		// 						'external_game_id' => 'BBQK',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Beijing Keno","2":"北京快乐 8"}',
		// 						'english_name' => 'Beijing Keno',
		// 						'external_game_id' => 'BJKN',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"BC Keno","2":"加拿大卑斯"}',
		// 						'english_name' => 'BC Keno',
		// 						'external_game_id' => 'CAKN',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Guangdong 11/5","2":"广东 11 选 5"}',
		// 						'english_name' => 'Guangdong 11/5',
		// 						'external_game_id' => 'GDE5',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Jiangxi 11/5","2":"江西 11 选 5"}',
		// 						'english_name' => 'Jiangxi 11/5',
		// 						'external_game_id' => 'JXE5',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Shandong 11/5","2":"山东十一运夺金"}',
		// 						'english_name' => 'Shandong 11/5',
		// 						'external_game_id' => 'SDE5',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Chongqing Wild Card","2":"重庆百变王牌"}',
		// 						'english_name' => 'Chongqing Wild Card',
		// 						'external_game_id' => 'CQWC',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Jilin Fast 3","2":"吉林快 3"}',
		// 						'english_name' => 'Jilin Fast 3',
		// 						'external_game_id' => 'JLQ3',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Jiangsu Fast 3","2":"江苏快 3"}',
		// 						'english_name' => 'Jiangsu Fast 3',
		// 						'external_game_id' => 'JSQ3',
		// 						'game_code' => ''
		// 						),
		// 					array('game_name' => '_json:{"1":"Anhui Fast 3","2":"安徽快 3"}',
		// 						'english_name' => 'Anhui Fast 3',
		// 						'external_game_id' => 'AHQ3',
		// 						'game_code' => ''
		// 						),
		// 					),
		// 				),
		// 			array(
		//            	    'game_type' => 'AG 3D Casino',
		// 				'game_type_lang' => 'agbbin_3d_casino',
		// 				'status' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'game_description_list' => array(

		// 					array('game_name' => '_json:{"1":"Fishjoy:anger airspace","2":"捕鱼空战版"}',
		// 						'english_name' => 'Fishjoy:anger airspace',
		// 						'external_game_id' => '15022',
		// 						'game_code' => ''
		// 						),
		// 					),
		// 				),

		// 			array(
		//            	    'game_type' => 'AG BB Fishing Game',
		// 				'game_type_lang' => 'agbbin_fishing_game',
		// 				'status' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'game_description_list' => array(

		// 					array('game_name' => '_json:{"1":"Fishing Legend","2":"钓鱼传奇"}',
		// 						'english_name' => 'Fishing Legend',
		// 						'external_game_id' => '30101',
		// 						'game_code' => ''
		// 						),
		// 					),
		// 				)
		//    );

		// $game_description_list = array();

		// foreach ($data as $game_type) {

		// 	$this->db->insert('game_type', array(
		// 		'game_platform_id' => AGBBIN_API,
		// 		'game_type' => $game_type['game_type'],
		// 		'game_type_lang' => $game_type['game_type_lang'],
		// 		'status' => $game_type['status'],
		// 		'flag_show_in_site' => $game_type['flag_show_in_site'],
		// 		));

		// 	$game_type_id = $this->db->insert_id();
		// 	foreach ($game_type['game_description_list'] as $game_description) {
		// 		$game_description_list[] = array_merge(array(
		// 			'game_platform_id' => AGBBIN_API,
		// 			'game_type_id' => $game_type_id,
		// 			), $game_description);
		// 	}

		// }

		// $this->db->insert_batch('game_description', $game_description_list);
		// $this->db->trans_complete();

	}

	public function down() {
		// $this->db->trans_start();
		// $this->db->delete('game_type', array('game_platform_id' => AGBBIN_API, 'game_type !='=> 'unknown'));
		// $this->db->delete('game_description', array('game_platform_id' => AGBBIN_API,'game_name !='=> 'agbbin.unknown'));
		// $this->db->trans_complete();
	}
}