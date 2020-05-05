<?php
trait game_description_ebet_ggfishing {

	public function sync_game_description_ebetggfishing(){
		$cnt=0;
		$cntInsert = 0;
		$cntUpdate = 0;

		$db_true = 1;
		$db_false = 0;
		$api_id = EBET_GGFISHING_API;
		$now = $this->utils->getNowForMysql();

		$game_type_code_fishing_game = "mwg_fishing_game";
		$game_type_code_unknown = "mwg_fishing_unknown";

		// sync game type first
		// use game_type_code as key
		$game_types = [
			  $game_type_code_fishing_game => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Fishing game","2":"千炮捕鱼","3":"Fishing game","4":"Fishing game","5":"Fishing game"}',
                'game_type_lang' => '_json:{"1":"Fishing game","2":"千炮捕鱼","3":"Fishing game","4":"Fishing game","5":"Fishing game"}',
                'game_type_code' => $game_type_code_fishing_game,
                'flag_show_in_site' => $db_true,
                'auto_add_new_game' => $db_true,
                'auto_add_to_cashback' => $db_true,
                'status' => $db_true,
                'updated_at' => $now,
            ],
			 $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_type_lang' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                "game_type_code" => $game_type_code_unknown,
                'flag_show_in_site' => $db_true,
                'auto_add_new_game' => $db_true,
                'auto_add_to_cashback' => $db_true,
                'status'=>$db_true,
                'updated_at'=>$now,
            ],

		];


		$this->load->model(['game_type_model']);
		$gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);
		$this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

		$game_descriptions = array(
            [
                'game_name' => '_json:{"1":"Fishing Hunter","2":"千炮捕鱼","3":"Fishing Hunter","4":"Fishing Hunter","5":"Fishing Hunter"}',
                'game_code' => 'imfishing10002',
                'game_platform_id' => $api_id,
                'external_game_id' => 'imfishing10002',
                'english_name' => 'Fishing Hunter',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_fishing_game],
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
