<?php
trait game_description_idn {

	public function sync_game_description_idn(&$cnt=0){

		// $cnt=0;

		$success=true;

		$api_id=IDN_API;
		$db_true=1;
		$db_false=0;
		$now=$this->utils->getNowForMysql();

		$game_type_code_idn=SYNC::TAG_CODE_TABLE_GAMES;
		$game_type_code_unknown='unknown';

		//sync game type first
		//use game_type_code as key
		$game_types = [
			$game_type_code_idn => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"IDN","2":"IDN"}',
				'game_type_lang' => '_json:{"1":"IDN","2":"IDN"}',
				'game_type_code' => $game_type_code_idn,
				'status'=>$db_true,
				'updated_at'=>$now,
                'game_tag_code' => SYNC::TAG_CODE_TABLE_GAMES
			],
			$game_type_code_unknown => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"Unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Unknown","2":"不明"}',
				'game_type_code' => $game_type_code_unknown,
				'status'=>$db_true,
				'updated_at'=>$now,
                'game_tag_code' => SYNC::TAG_CODE_UNKNOWN_GAME
			]
		];

		$this->load->model(['game_type_model']);
		$gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);

		$this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

		if(empty($gameTypeCodeMaps)){
			return false;
		}

		$game_descriptions = [

			[
				'game_platform_id' => $api_id,
				'game_code' => 'EBN',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_idn],
				'game_name' => '_json:{"1":"Ceme","2":"Ceme"}',
				"english_name" => "Ceme",
				'external_game_id' => 'EBN',
				'status'=> $db_true,
				'flag_show_in_site'=>$db_true,
				'updated_at'=>$now,
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => 'DMM',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_idn],
				'game_name' => '_json:{"1":"Domino","2":"Domino"}',
				"english_name" => "Domino",
				'external_game_id' => 'DMM',
				'status'=> $db_true,
				'flag_show_in_site'=>$db_true,
				'updated_at'=>$now,
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => 'TXH',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_idn],
				'game_name' => '_json:{"1":"Texas poker","2":"Texas poker"}',
				"english_name" => "Texas poker",
				'external_game_id' => 'TXH',
				'status'=> $db_true,
				'flag_show_in_site'=>$db_true,
				'updated_at'=>$now,
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => 'LPK',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_idn],
				'game_name' => '_json:{"1":"Live Poker","2":"Live Poker"}',
				"english_name" => "Live Poker",
				'external_game_id' => 'LPK',
				'status'=> $db_true,
				'flag_show_in_site'=>$db_true,
				'updated_at'=>$now,
			],
            [
                'game_platform_id' => $api_id,
                'game_code' => 'BTM',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_idn],
                'game_name' => '_json:{"1":"Ceme Keliling","2":"Ceme Keliling"}',
                "english_name" => "Ceme Keliling",
                'external_game_id' => 'BTM',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_true,
                'updated_at'=>$now,
            ],

			//===unknown======================================================================
			[
				'game_platform_id' => $api_id,
				'game_code' => 'unknown',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_unknown],
				'game_name' => '_json:{"1":"Unknown","2":"不明"}',
				"english_name" => "Unknown",
				'external_game_id' => 'unknown',
				'status'=> $db_true,
				'flag_show_in_site'=>$db_false,
				'updated_at'=>$now,
			],

		];

		$this->load->model(['game_description_model']);

		$success=$this->game_description_model->syncGameDescription($game_descriptions);

		return $success;
	}

}
