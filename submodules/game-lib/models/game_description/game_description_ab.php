<?php
trait game_description_ab {

    public function sync_game_description_ab(){
        $api_id = AB_API;
        $this->ab_game_list($api_id);
    }

    public function sync_game_description_ab_t1(){
        $api_id = T1AB_API;
        $this->ab_game_list($api_id);
    }

    public function ab_game_list($api_id){
        $cnt=0;
        $cntInsert = 0;
        $cntUpdate = 0;

        $db_true = 1;
        $db_false = 0;
        $now = $this->utils->getNowForMysql();
        $game_type_code_live    = SYNC::TAG_CODE_LIVE_DEALER;
        $game_type_code_unknown = SYNC::TAG_CODE_UNKNOWN_GAME;

        // sync game type first
        // use game_type_code as key
        $game_types = [
            $game_type_code_live => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Live Games","2":"真人游戏","3":"Live Games","4":"Live Games","5":"Live Games"}',
                'game_type_lang' => '_json:{"1":"Live Games","2":"真人游戏","3":"Live Games","4":"Live Games","5":"Live Games"}',
                'game_type_code' => $game_type_code_live,
                'flag_show_in_site' => $db_true,
                'auto_add_new_game' => $db_true,
                'auto_add_to_cashback' => $db_true,
                'status' => $db_true,
                'updated_at' => $now,
                'game_tag_code' => SYNC::TAG_CODE_LIVE_DEALER
            ],
            // unknown game
            $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_type_lang' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown","5":"Unknown"}',
                "game_type_code" => $game_type_code_unknown,
                'flag_show_in_site' => $db_false,
                'auto_add_new_game' => $db_true,
                'auto_add_to_cashback' => $db_true,
                'status'=>$db_true,
                'updated_at' => $now,
                'game_tag_code' => SYNC::TAG_CODE_UNKNOWN_GAME
            ],
        ];


        $this->load->model(['game_type_model']);
        //this code is checking the game type table with game
        //also insertion of new game type based on game_type_code
        // 'game_type_id' => $gameTypeCodeMaps[$game_type_code_table_game],
        $gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);

        $this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

        $game_descriptions = array(
            [
                'game_name' => '_json:{"1":"Baccarat","2":"百家乐","3":"Baccarat","4":"Baccarat","5":"Baccarat"}',
                'game_code' => '101',
                'game_platform_id' => $api_id,
                'external_game_id' => '101',
                'english_name' => 'Baccarat',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live],
            ],
            [
                'game_name' => '_json:{"1":"VIP Baccarat","2":"VIP 百家乐","3":"VIP Baccarat","4":"VIP Baccarat","5":"VIP Baccarat"}',
                'game_code' => '102',
                'game_platform_id' => $api_id,
                'external_game_id' => '102',
                'english_name' => 'VIP Baccarat',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live],
            ],
            [
                'game_name' => '_json:{"1":"Quick Baccarat","2":"急速百家乐","3":"Quick Baccarat","4":"Quick Baccarat","5":"Quick Baccarat"}',
                'game_code' => '103',
                'game_platform_id' => $api_id,
                'external_game_id' => '103',
                'english_name' => 'Quick Baccarat',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live],
            ],
            [
                'game_name' => '_json:{"1":"BidMe","2":"竞咪百家乐","3":"BidMe","4":"BidMe","5":"BidMe"}',
                'game_code' => '104',
                'game_platform_id' => $api_id,
                'external_game_id' => '104',
                'english_name' => 'BidMe',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live],
            ],
            [
                'game_name' => '_json:{"1":"Sicbo","2":"骰宝","3":"Sicbo","4":"Sicbo","5":"Sicbo"}',
                'game_code' => '201',
                'game_platform_id' => $api_id,
                'external_game_id' => '201',
                'english_name' => 'Sicbo',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live],
            ],
            [
                'game_name' => '_json:{"1":"DragonTiger","2":"龙虎","3":"DragonTiger","4":"DragonTiger","5":"DragonTiger"}',
                'game_code' => '301',
                'game_platform_id' => $api_id,
                'external_game_id' => '301',
                'english_name' => 'DragonTiger',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live],
            ],
            [
                'game_name' => '_json:{"1":"Roulette","2":"轮盘","3":"Roulette","4":"Roulette","5":"Roulette"}',
                'game_code' => '401',
                'game_platform_id' => $api_id,
                'external_game_id' => '401',
                'english_name' => 'Roulette',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live],
            ],

            #================================================== Unknown Game ========================================
            [
                'game_name' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_code' => 'unknown',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_unknown],
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
