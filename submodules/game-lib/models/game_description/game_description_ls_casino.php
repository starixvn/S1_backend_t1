<?php
trait game_description_ls_casino {

    public function sync_game_description_ls_casino(){
        $api_id=LS_CASINO_GAME_API;
        $this->sync_gamelist_ls_casino($api_id);
    }

    public function sync_game_description_ls_casino_t1(){
        $api_id=T1LS_CASINO_GAME_API;
        $this->sync_gamelist_ls_casino($api_id);
    }

    public function sync_gamelist_ls_casino($api_id){

        $success=true;
        $db_true=1;
        $db_false=0;
        $now=$this->utils->getNowForMysql();

        $game_type_code_live_dealer  = SYNC::TAG_CODE_LIVE_DEALER;
        $game_type_code_unknown = SYNC::TAG_CODE_UNKNOWN_GAME;

        $game_types = [
            $game_type_code_live_dealer => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Live Game","2":"真人游戏","3":"Live Game","4":"Live Game","5":"Live Game"}',
                'game_type_lang' => '_json:{"1":"Live Game","2":"真人游戏","3":"Live Game","4":"Live Game","5":"Live Game"}',
                'game_type_code' => $game_type_code_live_dealer,
                'game_tag_code' => $game_type_code_live_dealer,
            ],
            $game_type_code_unknown =>
                [
                    'game_platform_id' => $api_id,
                    'game_type' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                    'game_type_lang' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                    "game_type_code" => $game_type_code_unknown,
                    "game_tag_code" => $game_type_code_unknown,
                    'flag_show_in_site' => $db_true,
                    'updated_at'=>$now,
                ],
            ];

        $cnt=0;
        $this->load->model(['game_type_model']);
        $gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);

        $this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

        if(empty($gameTypeCodeMaps)){
            return false;
        }

     $game_descriptions = array(
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_unknown],
                'game_name' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_code' => 'unknown',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'flag_show_in_site' => $db_false,
                'english_name' => 'Unknown',
                'external_game_id' => 'unknown',
            ],        [
                'game_code' => 1,
                'external_game_id' => 1,
                'game_name' => '_json:{"1":"Blackjack","2":"Blackjack","3":"Blackjack","4":"Blackjack","5":""}',
                'english_name' => 'Blackjack',
                'html_five_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true,
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                'game_code' => 2,
                'external_game_id' => 2,
                'game_name' => '_json:{"1":"Roulette","2":"Roulette","3":"Roulette","4":"Roulette","5":""}',
                'english_name' => 'Roulette',
                'html_five_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true,
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                'game_code' => 3,
                'external_game_id' => 3,
                'game_name' => '_json:{"1":"Baccarat","2":"Baccarat","3":"Baccarat","4":"Baccarat","5":""}',
                'english_name' => 'Baccarat',
                'html_five_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true,
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                'game_code' => 4,
                'external_game_id' => 4,
                'game_name' => '_json:{"1":"Craps","2":"Craps","3":"Craps","4":"Craps","5":""}',
                'english_name' => 'Craps',
                'html_five_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true,
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                'game_code' => 5,
                'external_game_id' => 5,
                'game_name' => '_json:{"1":"DragonTiger","2":"DragonTiger","3":"DragonTiger","4":"DragonTiger","5":""}',
                'english_name' => 'DragonTiger',
                'html_five_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true,
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                'game_code' => 6,
                'external_game_id' => 6,
                'game_name' => '_json:{"1":"AsianBaccarat","2":"AsianBaccarat","3":"AsianBaccarat","4":"AsianBaccarat","5":""}',
                'english_name' => 'AsianBaccarat',
                'html_five_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true,
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                'game_code' => 7,
                'external_game_id' => 7,
                'game_name' => '_json:{"1":"FanTan","2":"FanTan","3":"FanTan","4":"FanTan","5":""}',
                'english_name' => 'FanTan',
                'html_five_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true,
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                'game_code' => 8,
                'external_game_id' => 8,
                'game_name' => '_json:{"1":"AllGames","2":"AllGames","3":"AllGames","4":"AllGames","5":""}',
                'english_name' => 'AllGames',
                'html_five_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true,
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],


        );

        $this->load->model(['game_description_model']);
        $success=$this->game_description_model->syncGameDescription($game_descriptions);

        return $success;
    }

}
