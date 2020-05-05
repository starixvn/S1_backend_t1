<?php
trait game_description_og {

    public function sync_game_description_og(){
        $api_id = OG_API;
        $this->sync_gamelist_og($api_id);
    }

    public function sync_game_description_og_t1(){
        $api_id = T1OG_API;
        $this->sync_gamelist_og($api_id);
    }

	public function sync_gamelist_og($api_id){
		$cnt=0;

		$db_true = 1;
		$db_false = 0;
		$now = $this->utils->getNowForMysql();

		$game_type_code_live_dealer = SYNC::TAG_CODE_LIVE_DEALER;
        $game_type_code_unknown     = SYNC::TAG_CODE_UNKNOWN_GAME;


		// sync game type first
		// use game_type_code as key
		$game_types = [
			$game_type_code_live_dealer => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"OG live Casino","2":"OG live Casino","3":"OG live Casino","4":"OG live Casino"}',
				'game_type_lang' => '_json:{"1":"OG live Casino","2":"OG live Casino","3":"OG live Casino","4":"OG live Casino"}',
				"game_type_code" => $game_type_code_live_dealer,
				'updated_at'=>$now,
                'game_tag_code' => SYNC::TAG_CODE_LIVE_DEALER
			],
			$game_type_code_unknown => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown"}',
				'game_type_lang' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown"}',
				"game_type_code" => $game_type_code_unknown,
				'updated_at'=>$now,
                'game_tag_code' => SYNC::TAG_CODE_UNKNOWN_GAME
			],
		];


		$this->load->model(['game_type_model']);
		$gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);

		$this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

		$game_descriptions = array(
            #================================================== OG live Casino ========================================
            [
                'game_name' => '_json:{"1":"Baccarat","2":"百家樂 ","3":"Baccarat", "4":"Baccarat"}',
                'game_code' => '11',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'external_game_id' => '11',
                'english_name' => 'Baccarat',
                'game_platform_id' => $api_id,
                'status'=> $db_true,
                'flag_show_in_site'=>$db_true,
                'updated_at'=>$now,
            ],
            [
                'game_name' => '_json:{"1":"Dragon","2":"龍虎 ","3":"Dragon", "4":"Dragon"}',
                'game_code' => '12',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'external_game_id' => '12',
                'english_name' => 'Dragon',
                'game_platform_id' => $api_id,
                'status'=> $db_true,
                'flag_show_in_site'=>$db_true,
                'updated_at'=>$now,
            ],
            [
                'game_name' => '_json:{"1":"Roulette","2":"輪盤 ","3":"Roulette", "4":"Roulette"}',
                'game_code' => '13',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'external_game_id' => '13',
                'english_name' => 'Roulette',
                'game_platform_id' => $api_id,
                'status'=> $db_true,
                'flag_show_in_site'=>$db_true,
                'updated_at'=>$now,
            ],
            [
                'game_name' => '_json:{"1":"Sic Bo","2":"骰寶 ","3":"Sic Bo", "4":"Sic Bo"}',
                'game_code' => '14',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'external_game_id' => '14',
                'english_name' => 'Sic Bo',
                'game_platform_id' => $api_id,
                'status'=> $db_true,
                'flag_show_in_site'=>$db_true,
                'updated_at'=>$now,
            ],
            [
                'game_name' => '_json:{"1":"Fantan","2":"番攤 ","3":"Fantan", "4":"Fantan"}',
                'game_code' => '16',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'external_game_id' => '16',
                'english_name' => 'Fantan',
                'game_platform_id' => $api_id,
                'status'=> $db_true,
                'flag_show_in_site'=>$db_true,
                'updated_at'=>$now,
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
				'flag_show_in_site'=>$db_true,
				'updated_at'=>$now,
            ],

            #====================================End Game list=========================================
		);

		$this->load->model(['game_description_model']);

		$success=$this->game_description_model->syncGameDescription($game_descriptions);

		return $success;
	}

}
