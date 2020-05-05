<?php
trait game_description_finance {

    public function sync_game_description_finance(){

        $db_true = 1;
        $db_false = 0;
        $api_id = FINANCE_API;
        $now = $this->utils->getNowForMysql();

        $game_type_code_live_dealer = SYNC::TAG_CODE_LIVE_DEALER;
        $game_type_code_unknown     = SYNC::TAG_CODE_UNKNOWN_GAME;

        $game_types = [
            $game_type_code_live_dealer => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Finance Game","2":"Finance Game","3":"Finance Game","4":"Finance Game","5":"Finance Game"}',
                'game_type_lang' => '_json:{"1":"Finance Game","2":"Finance Game","3":"Finance Game","4":"Finance Game","5":"Finance Game"}',
                'game_type_code' => $game_type_code_live_dealer,
                'game_tag_code' => $game_type_code_live_dealer
            ],
            $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_type_lang' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                "game_type_code" => $game_type_code_unknown,
                'game_tag_code' => $game_type_code_unknown
            ],
        ];

        $this->load->model(['game_type_model']);
        $gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);
        $this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

        $game_descriptions = array(
            [
                'game_name' => '_json:{"1":"Finance Game","2":"Finance Game","3":"Finance Game","4":"Finance Game","5":"Finance Game"}',
                'game_code' => 'finance',
                'game_platform_id' => $api_id,
                'external_game_id' => 'finance',
                'english_name' => 'Finance Game',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                'game_name' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_code' => 'unknown',
                'game_platform_id' => $api_id,
                'external_game_id' => 'unknown',
                'english_name' => 'Unknown',
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