<?php
trait game_description_ebet_kuma {

	// public function sync_game_description_ebet_kuma(){
	// 	$cnt=0;
	// 	$cntInsert = 0;
	// 	$cntUpdate = 0;

	// 	$db_true = 1;
	// 	$db_false = 0;
	// 	$api_id = EBET_KUMA_API;
	// 	$now = $this->utils->getNowForMysql();

	// 	$game_type_code_slot_game = "ebet_kuma_slot_game";
	// 	$game_type_code_unknown = "ebet_kuma_unknown";

	// 	// sync game type first
	// 	// use game_type_code as key
	// 	$game_types = [
	// 		  $game_type_code_slot => [
 //                'game_platform_id' => $api_id,
 //                'game_type' => '_json:{"1":"Slot Games","2":"老虎機","3":"Slot Games","4":"Slot Games","5":"Slot Games"}',
 //                'game_type_lang' => '_json:{"1":"Slot Games","2":"老虎機","3":"Slot Games","4":"Slot Games","5":"Slot Games"}',
 //                'game_type_code' => $game_type_code_slot_game,
 //                'flag_show_in_site' => $db_true,
 //                'auto_add_new_game' => $db_true,
 //                'auto_add_to_cashback' => $db_true,
 //                'status' => $db_true,
 //                'updated_at' => $now,
 //            ],
	// 		 $game_type_code_unknown => [
 //                'game_platform_id' => $api_id,
 //                'game_type' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
 //                'game_type_lang' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
 //                "game_type_code" => $game_type_code_unknown,
 //                'flag_show_in_site' => $db_true,
 //                'auto_add_new_game' => $db_true,
 //                'auto_add_to_cashback' => $db_true,
 //                'status'=>$db_true,
 //                'updated_at'=>$now,
 //            ],

	// 	];


	// 	$this->load->model(['game_type_model']);
	// 	$gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);
	// 	$this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

	// 	$game_descriptions = array(
	// 		#====================================slots=========================================
 //            [
 //                'game_name' => '_json:{"1":"Big Tits Heaven","2":"乳姬无双","3":"Big Tits Heaven","4":"Big Tits Heaven","5":"Big Tits Heaven"}',
 //                'game_code' => '1011',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => '1011',
 //                'english_name' => 'Big Tits Heaven"',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_true,
 //                'html_five_enabled' => $db_true,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slot],
 //            ],
 //            [
 //                'game_name' => '_json:{"1":"Sexy Clinic","2":"性爱诊疗室","3":"Sexy Clinic","4":"Sexy Clinic","5":"Sexy Clinic"}',
 //                'game_code' => '1012',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => '1012',
 //                'english_name' => 'Sexy Clinic"',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_true,
 //                'html_five_enabled' => $db_true,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slot],
 //            ],
 //            [
 //                'game_name' => '_json:{"1":"Filthy Academic","2":"淫乱学园","3":"Filthy Academic","4":"Filthy Academic","5":"Filthy Academic"}',
 //                'game_code' => '1013',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => '1013',
 //                'english_name' => 'Filthy Academic"',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_true,
 //                'html_five_enabled' => $db_true,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slot],
 //            ],
 //            [
 //                'game_name' => '_json:{"1":"Asuka’s Orgasm Climax","2":"明日花潮吹大作战","3":"Asuka’s Orgasm Climax","4":"Asuka’s Orgasm Climax","5":"Asuka’s Orgasm Climax"}',
 //                'game_code' => '1014',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => '1014',
 //                'english_name' => 'Asuka’s Orgasm Climax',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_true,
 //                'html_five_enabled' => $db_true,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slot],
 //            ],
 //            [
 //                'game_name' => '_json:{"1":"Pleasure with Yuya","2":"快感‧三上悠亚","3":"Pleasure with Yuya","4":"Pleasure with Yuya","5":"Pleasure with Yuya"}',
 //                'game_code' => '1015',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => '1015',
 //                'english_name' => 'Pleasure with Yuya',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_true,
 //                'html_five_enabled' => $db_true,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slot],
 //            ],
 //            [
 //                'game_name' => '_json:{"1":"College Students’ B/D","2":"女大生调教日记","3":"College Students’ B/D","4":"College Students’ B/D","5":"College Students’ B/D"}',
 //                'game_code' => '1016',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => '1016',
 //                'english_name' => 'College Students’ B/D',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_true,
 //                'html_five_enabled' => $db_true,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slot],
 //            ],
 //            [
 //                'game_name' => '_json:{"1":"Unlimited Internal Cum Shot","2":"忧の无限中出约会","3":"Unlimited Internal Cum Shot","4":"Unlimited Internal Cum Shot","5":"Unlimited Internal Cum Shot"}',
 //                'game_code' => '1017',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => '1017',
 //                'english_name' => 'Unlimited Internal Cum Shot',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_true,
 //                'html_five_enabled' => $db_true,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slot],
 //            ],
 //            [
 //                'game_name' => '_json:{"1":"Rion’s Incredible Breasts","2":"神之乳RION","3":"Rion’s Incredible Breasts","4":"Rion’s Incredible Breasts","5":"Rion’s Incredible Breasts"}',
 //                'game_code' => '1018',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => '1018',
 //                'english_name' => 'Rion’s Incredible Breasts',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_true,
 //                'html_five_enabled' => $db_true,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slot],
 //            ],
 //            [
 //                'game_name' => '_json:{"1":"Estrus Temptation","2":"旬果的发情诱惑","3":"Estrus Temptation","4":"Estrus Temptation","5":"Estrus Temptation"}',
 //                'game_code' => '1019',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => '1019',
 //                'english_name' => 'Estrus Temptation',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_true,
 //                'html_five_enabled' => $db_true,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slot],
 //            ],
 //            [
 //                'game_name' => '_json:{"1":"Tofu beauties Sexy Imagination","2":"豆腐西施淫乱觉醒","3":"Tofu beauties Sexy Imagination","4":"Tofu beauties Sexy Imagination","5":"Tofu beauties Sexy Imagination"}',
 //                'game_code' => '1020',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => '1020',
 //                'english_name' => 'Tofu beauties Sexy Imagination',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_true,
 //                'html_five_enabled' => $db_true,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slot],
 //            ],
 //            [
 //                'game_name' => '_json:{"1":"G-Cup Club","2":"G奶俱乐部","3":"G-Cup Club","4":"G-Cup Club","5":"G-Cup Club"}',
 //                'game_code' => '1021',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => '1021',
 //                'english_name' => 'G-Cup Club',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_true,
 //                'html_five_enabled' => $db_true,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slot],
 //            ],
 //            [
 //                'game_name' => '_json:{"1":"Queen of PAPAPA","2":"女王啪啪啪","3":"Queen of PAPAPA","4":"Queen of PAPAPA","5":"Queen of PAPAPA"}',
 //                'game_code' => '1022',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => '1022',
 //                'english_name' => 'Queen of PAPAPA',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_true,
 //                'html_five_enabled' => $db_true,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slot],
 //            ],
 //            [
 //                'game_name' => '_json:{"1":"Matsuoka China’s Classroom","2":"千菜的调教教室","3":"Matsuoka China’s Classroom","4":"Matsuoka China’s Classroom","5":"Matsuoka China’s Classroom"}',
 //                'game_code' => '1023',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => '1023',
 //                'english_name' => 'Matsuoka China’s Classroom',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_true,
 //                'html_five_enabled' => $db_true,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slot],
 //            ],
 //            [
 //                'game_name' => '_json:{"1":"Swapping apartment","2":"换妻公寓","3":"Swapping apartment","4":"Swapping apartment","5":"Swapping apartment"}',
 //                'game_code' => '1024',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => '1024',
 //                'english_name' => 'Swapping apartment',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_true,
 //                'html_five_enabled' => $db_true,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slot],
 //            ],
 //            [
 //                'game_name' => '_json:{"1":"The passion of the room","2":"和室里的激情","3":"The passion of the room","4":"The passion of the room","5":"The passion of the room"}',
 //                'game_code' => '1025',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => '1025',
 //                'english_name' => 'The passion of the room',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_true,
 //                'html_five_enabled' => $db_true,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slot],
 //            ],
 //            [
 //                'game_name' => '_json:{"1":"High tide dessert house","2":"高潮甜点屋","3":"High tide dessert house","4":"High tide dessert house","5":"High tide dessert house"}',
 //                'game_code' => '1026',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => '1026',
 //                'english_name' => 'High tide dessert house',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_true,
 //                'html_five_enabled' => $db_true,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slot],
 //            ],
 //            [
 //                'game_name' => '_json:{"1":"Double Queens Fruit Party","2":"香姬水果盘","3":"Double Queens Fruit Party","4":"Double Queens Fruit Party","5":"Double Queens Fruit Party"}',
 //                'game_code' => '1027',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => '1027',
 //                'english_name' => 'Double Queens Fruit Party',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_true,
 //                'html_five_enabled' => $db_true,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slot],
 //            ],
 //            [
 //                'game_name' => '_json:{"1":"Paradise BAR","2":"极乐BAR","3":"Paradise BAR","4":"Paradise BAR","5":"Paradise BAR"}',
 //                'game_code' => '1028',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => '1028',
 //                'english_name' => 'Paradise BAR',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_true,
 //                'html_five_enabled' => $db_true,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slot],
 //            ],
 //            [
 //                'game_name' => '_json:{"1":"UNKNOWN GAME","2":"KUMA 不明游戏","3":"UNKNOWN GAME","4":"UNKNOWN GAME","5":"UNKNOWN GAME"}',
 //                'game_code' => 'unknown',
 //                'game_platform_id' => $api_id,
 //                'external_game_id' => 'unknown',
 //                'english_name' => 'UNKNOWN GAME',
 //                'status' => $db_true,
 //                'flag_show_in_site' => $db_false,
 //                'game_type_id' => $gameTypeCodeMaps[$game_type_code_unknown],
 //            ],
	// 	);

	// 	$this->load->model(['game_description_model']);

	// 	$success=$this->game_description_model->syncGameDescription($game_descriptions);

	// 	return $success;
	// }

}
