<?php

trait game_description_ld_casino {

    public function sync_game_description_ld_casino(){
        $db_true = 1;
        $db_false = 0;
        $api_id = LD_CASINO_API;
        $now = $this->utils->getNowForMysql();

        $game_type_code_live_dealer = SYNC::TAG_CODE_LIVE_DEALER;
        $game_type_code_unknown     = SYNC::TAG_CODE_UNKNOWN_GAME;

        $game_types = [
            $game_type_code_live_dealer => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Live Game","2":"Live Game","3":"Live Game","4":"Live Game","5":"Live Game"}',
                'game_type_lang' => '_json:{"1":"Live Game","2":"Live Game","3":"Live Game","4":"Live Game","5":"Live Game"}',
                'game_type_code' => $game_type_code_live_dealer,
                'game_tag_code' => SYNC::TAG_CODE_LIVE_DEALER,
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
            [
                'game_name' => '_json:{"1":"Baccarat","2":"Baccarat","3":"Baccarat","4":"Baccarat","5":"Baccarat"}',
                'game_code' => '1',
                'game_platform_id' => $api_id,
                'external_game_id' => '1',
                'english_name' => 'Baccarat',
                'flash_enabled' => $db_false,
                'attributes' => 'baccarat',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Sic Bo","2":"Sic Bo","3":"Sic Bo","4":"Sic Bo","5":"Sic Bo"}',
                'game_code' => '2',
                'game_platform_id' => $api_id,
                'external_game_id' => '2',
                'english_name' => 'Sic Bo',
                'flash_enabled' => $db_false,
                'attributes' => 'sic-bo',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Dragon & Tiger","2":"Dragon & Tiger","3":"Dragon & Tiger","4":"Dragon & Tiger","5":"Dragon & Tiger"}',
                'game_code' => '3',
                'game_platform_id' => $api_id,
                'external_game_id' => '3',
                'english_name' => 'Dragon & Tiger',
                'flash_enabled' => $db_false,
                'attributes' => 'dragon-and-tiger',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Roulette","2":"Roulette","3":"Roulette","4":"Roulette","5":"Roulette"}',
                'game_code' => '4',
                'game_platform_id' => $api_id,
                'external_game_id' => '4',
                'english_name' => 'Roulette',
                'flash_enabled' => $db_false,
                'attributes' => 'roulette',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Fan-Tan","2":"Fan-Tan","3":"Fan-Tan","4":"Fan-Tan","5":"Fan-Tan"}',
                'game_code' => '5',
                'game_platform_id' => $api_id,
                'external_game_id' => '5',
                'english_name' => 'Fan-Tan',
                'flash_enabled' => $db_false,
                'attributes' => 'fan-tan',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Niuniu","2":"Niuniu","3":"Niuniu","4":"Niuniu","5":"Niuniu"}',
                'game_code' => '6',
                'game_platform_id' => $api_id,
                'external_game_id' => '6',
                'english_name' => 'Niuniu',
                'flash_enabled' => $db_false,
                'attributes' => 'niuniu',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_code' => 'unknown',
                'game_platform_id' => $api_id,
                'external_game_id' => 'unknown',
                'english_name' => 'Unknown',
                'flash_enabled' => $db_false,
                'attributes' => 'unknown',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_unknown],
                'mobile_enabled' => $db_true,
            ],
        );

        $this->load->model(['game_description_model']);

        $success=$this->game_description_model->syncGameDescription($game_descriptions);

        return $success;
    }

}
