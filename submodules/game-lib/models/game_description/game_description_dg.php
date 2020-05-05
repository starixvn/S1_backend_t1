<?php
trait game_description_dg {

    public function sync_all_dg_games(){
        $this->sync_game_description_dg();
        $this->sync_game_description_dg_t1();
    }

    public function sync_game_description_dg_t1(){
        $api_id = T1DG_API;
        $this->dg_game_list($api_id);
    }

    public function sync_game_description_dg(){
        $api_id = DG_API;
        $this->dg_game_list($api_id);
    }

	public function dg_game_list($api_id){
		$cnt=0;
		$cntInsert = 0;
		$cntUpdate = 0;

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
                'game_type' => '_json:{"1":"Casino","2":"赌场游戏","3":"Casino","4":"Casino","5":"Casino"}',
                'game_type_lang' => '_json:{"1":"Casino","2":"赌场游戏","3":"Casino","4":"Casino","5":"Casino"}',
                'game_type_code' => $game_type_code_live_dealer,
                'game_tag_code' => SYNC::TAG_CODE_LIVE_DEALER
            ],
			 $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_type_lang' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                "game_type_code" => $game_type_code_unknown,
                'game_tag_code' => SYNC::TAG_CODE_UNKNOWN_GAME
            ],

		];


		$this->load->model(['game_type_model']);
		$gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);
		$this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

		$game_descriptions = array(
			#====================================Casino=========================================
            [
                'game_name' => '_json:{"1":"Baccarat","2":"百家乐","3":"Baccarat","4":"Baccarat","5":"Baccarat"}',
                'game_code' => '11',
                'game_platform_id' => $api_id,
                'external_game_id' => '11',
                'english_name' => 'Baccarat',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                'game_name' => '_json:{"1":"Dragon Tiger","2":"龙虎","3":"Dragon Tiger","4":"Dragon Tiger","5":"Dragon Tiger"}',
                'game_code' => '13',
                'game_platform_id' => $api_id,
                'external_game_id' => '13',
                'english_name' => 'Dragon Tiger',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                'game_name' => '_json:{"1":"Roulette","2":"轮盘","3":"Roulette","4":"Roulette","5":"Roulette"}',
                'game_code' => '14',
                'game_platform_id' => $api_id,
                'external_game_id' => '14',
                'english_name' => 'Roulette',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                'game_name' => '_json:{"1":"Sic Bo","2":"骰宝","3":"Sic Bo","4":"Sic Bo","5":"Sic Bo"}',
                'game_code' => '15',
                'game_platform_id' => $api_id,
                'external_game_id' => '15',
                'english_name' => 'Sic Bo',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                'game_name' => '_json:{"1":"Bull","2":"Bull","3":"Bull","4":"Bull","5":"Bull"}',
                'game_code' => '17',
                'game_platform_id' => $api_id,
                'external_game_id' => '17',
                'english_name' => 'Bull',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                'game_name' => '_json:{"1":"Bull","2":"Bull","3":"Bull","4":"Bull","5":"Bull"}',
                'game_code' => '17',
                'game_platform_id' => $api_id,
                'external_game_id' => '17',
                'english_name' => 'Bull',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                'game_name' => '_json:{"1":"Casino Hold\'em","2":"Casino Hold\'em","3":"Casino Hold\'em","4":"Casino Hold\'em","5":"Casino Hold\'em"}',
                'game_code' => '19',
                'game_platform_id' => $api_id,
                'external_game_id' => '19',
                'english_name' => 'Casino Hold\'em',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                'game_name' => '_json:{"1":"Bid Baccarat","2":"Bid Baccarat","3":"Bid Baccarat","4":"Bid Baccarat","5":"Bid Baccarat"}',
                'game_code' => '18',
                'game_platform_id' => $api_id,
                'external_game_id' => '18',
                'english_name' => 'Bid Baccarat',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                'game_name' => '_json:{"1":"UNKNOWN GAME","2":"不明游戏","3":"UNKNOWN GAME","4":"UNKNOWN GAME","5":"UNKNOWN GAME"}',
                'game_code' => 'unknown',
                'game_platform_id' => $api_id,
                'external_game_id' => 'unknown',
                'english_name' => 'UNKNOWN GAME',
                'status' => $db_true,
                'flag_show_in_site' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_unknown],
            ],
		);

		$this->load->model(['game_description_model']);

		$success=$this->game_description_model->syncGameDescription($game_descriptions);

		return $success;
	}

}
