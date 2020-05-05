<?php
trait game_description_extreme_live_gaming {

    public function sync_game_description_extreme_live_gaming(){
        $api_id = EXTREME_LIVE_GAMING_API;

        $game_types = [
            'game_type_code_live_dealer'   => SYNC::TAG_CODE_LIVE_DEALER,
            'game_type_code_unknown' => SYNC::TAG_CODE_UNKNOWN_GAME,
        ];

        $this->extreme_game_list($api_id,$game_types);
    }

	public function extreme_game_list($api_id,$game_types){
		$cnt=0;
		$cntInsert = 0;
		$cntUpdate = 0;

		$db_true = 1;
		$db_false = 0;

        $game_type_code_live_dealer   = $game_types['game_type_code_live_dealer'];
        $game_type_code_unknown = $game_types['game_type_code_unknown'];

		// sync game type first
		// use game_type_code as key
		$game_types = [

            $game_type_code_live_dealer => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Live Game","2":"真人游戏","3":"Live Game","4":"Live Game","5":"Live Game"}',
                'game_type_lang' => '_json:{"1":"Live Game","2":"真人游戏","3":"Live Game","4":"Live Game","5":"Live Game"}',
                'game_type_code' => $game_type_code_live_dealer,
                'flag_show_in_site' => $db_false,
                'status'=>$db_true,
                'game_tag_code' => SYNC::TAG_CODE_LIVE_DEALER
            ],
            $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown","5":"Unknown"}',
                "game_type_code" => $game_type_code_unknown,
                'flag_show_in_site' => $db_false,
                'status'=>$db_true,
                'game_tag_code' => SYNC::TAG_CODE_UNKNOWN_GAME
            ],
		];


		$this->load->model(['game_type_model']);
		//this code is checking the game type table with game
		//also insertion of new game type based on game_type_code
		// 'game_type_id' => $gameTypeCodeMaps[$game_type_code_table_game],
		$gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);
		// echo "<pre>";
		// print_r($gameTypeCodeMaps);
		// die();
		$this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

		$game_descriptions = array(
			[
                'game_name' => '_json:{"1":"Extreme Live Blackjack","2":"Extreme Live Blackjack","3":"Extreme Live Blackjack","4":"Extreme Live Blackjack","5":"Extreme Live Blackjack"}',
                'game_code' => 'Extreme Live Blackjack',
                'html_five_enabled' => $db_false,
                'game_platform_id' => $api_id,
                'external_game_id' => 'Extreme Live Blackjack',
                'english_name' => 'Extreme Live Blackjack',
                'flash_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            #================================================== Unknown Game ========================================
            [
                'game_name' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown"}',
                'game_code' => 'unknown',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_unknown],
                // 'game_type' => Table Game
                'external_game_id' => 'unknown',
                'english_name' => 'Unknown',
                'game_platform_id' => $api_id,
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
            ],

            #====================================End Game list=========================================
		);

		$this->load->model(['game_description_model']);

		$success=$this->game_description_model->syncGameDescription($game_descriptions);

		return $success;
	}

}
