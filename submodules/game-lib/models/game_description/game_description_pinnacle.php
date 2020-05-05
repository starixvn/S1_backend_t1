<?php
trait game_description_pinnacle {

	public function sync_game_description_pinnacle(){
		$cnt=0;
		$cntInsert = 0;
		$cntUpdate = 0;

		$db_true = 1;
		$db_false = 0;
		$api_id = PINNACLE_API;
		$now = $this->utils->getNowForMysql();

        $game_type_code_sport = SYNC::TAG_CODE_SPORTS;
        $game_type_code_unknown = SYNC::TAG_CODE_UNKNOWN_GAME;


		// sync game type first
		// use game_type_code as key
		$game_types = [
            $game_type_code_sport => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Sports Book","2":"Sports Book","3":"Sports Book","4":"Sports Book","5":"Sports Book"}',
                'game_type_lang' => '_json:{"1":"Sports Book","2":"Sports Book","3":"Sports Book","4":"Sports Book","5":"Sports Book"}',
                'game_type_code' => $game_type_code_sport,
                'game_tag_code' => SYNC::TAG_CODE_SPORTS
            ],
            $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_type_lang' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown","5":"Unknown"}',
                "game_type_code" => $game_type_code_unknown,
                'game_tag_code' => SYNC::TAG_CODE_UNKNOWN_GAME
            ],
		];


		$this->load->model(['game_type_model']);
		$gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);
		$this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

		$game_descriptions = array(
            #================================================== Sports Game ========================================
            [
                'game_name' => '_json:{"1":"Soccer","2":"Soccer","3":"Soccer","4":"Soccer","5":"Soccer"}',
                'game_code' => 'Soccer',
                'game_platform_id' => $api_id,
                'external_game_id' => 'Soccer',
                'english_name' => 'Soccer',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
            ],
            [
                'game_name' => '_json:{"1":"Tennis","2":"Tennis","3":"Tennis","4":"Tennis","5":"Tennis"}',
                'game_code' => 'Tennis',
                'game_platform_id' => $api_id,
                'external_game_id' => 'Tennis',
                'english_name' => 'Tennis',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
            ],
            [
                'game_name' => '_json:{"1":"Basketball","2":"Basketball","3":"Basketball","4":"Basketball","5":"Basketball"}',
                'game_code' => 'Basketball',
                'game_platform_id' => $api_id,
                'external_game_id' => 'Basketball',
                'english_name' => 'Basketball',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
            ],
            [
                'game_name' => '_json:{"1":"Football","2":"Football","3":"Football","4":"Football","5":"Football"}',
                'game_code' => 'Football',
                'game_platform_id' => $api_id,
                'external_game_id' => 'Football',
                'english_name' => 'Football',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
            ],
            [
                'game_name' => '_json:{"1":"Baseball","2":"Baseball","3":"Baseball","4":"Baseball","5":"Baseball"}',
                'game_code' => 'Baseball',
                'game_platform_id' => $api_id,
                'external_game_id' => 'Baseball',
                'english_name' => 'Baseball',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
            ],
            [
                'game_name' => '_json:{"1":"Golf","2":"Golf","3":"Golf","4":"Golf","5":"Golf"}',
                'game_code' => 'Golf',
                'game_platform_id' => $api_id,
                'external_game_id' => 'Golf',
                'english_name' => 'Golf',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
            ],
            [
                'game_name' => '_json:{"1":"Hockey","2":"Hockey","3":"Hockey","4":"Hockey","5":"Hockey"}',
                'game_code' => 'Hockey',
                'game_platform_id' => $api_id,
                'external_game_id' => 'Hockey',
                'english_name' => 'Hockey',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
            ],
            [
                'game_name' => '_json:{"1":"Volleyball","2":"Volleyball","3":"Volleyball","4":"Volleyball","5":"Volleyball"}',
                'game_code' => 'Volleyball',
                'game_platform_id' => $api_id,
                'external_game_id' => 'Volleyball',
                'english_name' => 'Volleyball',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
            ],
            [
                'game_name' => '_json:{"1":"Rugby league","2":"Rugby league","3":"Rugby league","4":"Rugby league","5":"Rugby league"}',
                'game_code' => 'Rugby league',
                'game_platform_id' => $api_id,
                'external_game_id' => 'Rugby league',
                'english_name' => 'Rugby league',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
            ],
            [
                'game_name' => '_json:{"1":"Mixed martial arts","2":"Mixed martial arts","3":"Mixed martial arts","4":"Mixed martial arts","5":"Mixed martial arts"}',
                'game_code' => 'Mixed martial arts',
                'game_platform_id' => $api_id,
                'external_game_id' => 'Mixed martial arts',
                'english_name' => 'Mixed martial arts',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
            ],
            [
                'game_name' => '_json:{"1":"Handball","2":"Handball","3":"Handball","4":"Handball","5":"Handball"}',
                'game_code' => 'Handball',
                'game_platform_id' => $api_id,
                'external_game_id' => 'Handball',
                'english_name' => 'Handball',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
            ],
            [
                'game_name' => '_json:{"1":"E-sports","2":"E-sports","3":"E-sports","4":"E-sports","5":"E-sports"}',
                'game_code' => 'E-sports',
                'game_platform_id' => $api_id,
                'external_game_id' => 'E-sports',
                'english_name' => 'E-sports',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
            ],
            [
                'game_name' => '_json:{"1":"Aussie rules","2":"Aussie rules","3":"Aussie rules","4":"Aussie rules","5":"Aussie rules"}',
                'game_code' => 'Aussie rules',
                'game_platform_id' => $api_id,
                'external_game_id' => 'Aussie rules',
                'english_name' => 'Aussie rules',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
            ],
            [
                'game_name' => '_json:{"1":"Boxing","2":"Boxing","3":"Boxing","4":"Boxing","5":"Boxing"}',
                'game_code' => 'Boxing',
                'game_platform_id' => $api_id,
                'external_game_id' => 'Boxing',
                'english_name' => 'Boxing',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
            ],
            [
                'game_name' => '_json:{"1":"Rugby union","2":"Rugby union","3":"Rugby union","4":"Rugby union","5":"Rugby union"}',
                'game_code' => 'Rugby union',
                'game_platform_id' => $api_id,
                'external_game_id' => 'Rugby union',
                'english_name' => 'Rugby union',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
            ],
            [
                'game_name' => '_json:{"1":"Snooker","2":"Snooker","3":"Snooker","4":"Snooker","5":"Snooker"}',
                'game_code' => 'Snooker',
                'game_platform_id' => $api_id,
                'external_game_id' => 'Snooker',
                'english_name' => 'Snooker',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
            ],
            [
                'game_name' => '_json:{"1":"Cycling","2":"Cycling","3":"Cycling","4":"Cycling","5":"Cycling"}',
                'game_code' => 'Cycling',
                'game_platform_id' => $api_id,
                'external_game_id' => 'Cycling',
                'english_name' => 'Cycling',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
            ],
            [
                'game_name' => '_json:{"1":"Mix Parlay","2":"Mix Parlay","3":"Mix Parlay","4":"Mix Parlay","5":"Mix Parlay"}',
                'game_code' => 'MIX_PARLAY',
                'game_platform_id' => $api_id,
                'external_game_id' => 'MIX_PARLAY',
                'english_name' => 'Mix Parlay',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
            ],
            [
                'game_name' => '_json:{"1":"unknown","2":"不明","3":"unknown","4":"unknown","5":"unknown"}',
                'game_code' => 'unknown',
                'game_platform_id' => $api_id,
                'external_game_id' => 'unknown',
                'english_name' => 'unknown',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_unknown],
            ],
            #====================================End Game list=========================================
		);

		$this->load->model(['game_description_model']);

		$success=$this->game_description_model->syncGameDescription($game_descriptions);

		return $success;
	}

}
