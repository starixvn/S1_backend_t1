<?php
trait game_description_ebet {

    public function sync_game_description_ebet(){
        $api_id = EBET_API;
        $this->sync_gamelist_ebet($api_id);
     }

    public function sync_game_description_ebet_t1(){
        $api_id = T1EBET_API;
        $this->sync_gamelist_ebet($api_id);
     }

    public function sync_gamelist_ebet($api_id){

        $db_true = 1;
        $db_false = 0;
        $now = $this->utils->getNowForMysql();

        $game_type_code_live = SYNC::TAG_CODE_LIVE_DEALER;
        $game_type_code_unknown = SYNC::TAG_CODE_UNKNOWN_GAME;

        $game_types = [
            $game_type_code_live => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Live Games","2":"真人游戏","3":"Live Games","4":"Live Games","5":"Live Games"}',
                'game_type_lang' => '_json:{"1":"Live Games","2":"真人游戏","3":"Live Games","4":"Live Games","5":"Live Games"}',
                'game_type_code' => $game_type_code_live,
                'updated_at' => $now,
                'game_tag_code' => SYNC::TAG_CODE_LIVE_DEALER
            ],
            $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_type_lang' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                "game_type_code" => $game_type_code_unknown,
                'updated_at'=>$now,
                'game_tag_code' => SYNC::TAG_CODE_UNKNOWN_GAME
            ],
        ];
        echo "<pre>";
        $this->load->model(['game_type_model']);
        $gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);
        $this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

        $game_descriptions = array(
            [
                'game_code' => '1',
                'game_name' => '_json:{"1":"Baccarat","2":"百家乐"}',
                'english_name' => 'Baccarat',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live],
                'game_platform_id' => $api_id,
                'html_five_enabled' => $db_true,
            ],
            [
                'game_code' => '2',
                'game_name' => '_json:{"1":"Dragon Tiger","2":"龙虎"}',
                'english_name' => 'Dragon Tiger',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live],
                'game_platform_id' => $api_id,
                'html_five_enabled' => $db_true,
            ],
            [
                'game_code' => '3',
                'game_name' => '_json:{"1":"Sicbo","2":"骰宝"}',
                'english_name' => 'Sicbo',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live],
                'game_platform_id' => $api_id,
                'html_five_enabled' => $db_true,
            ],
            [
                'game_code' => '4',
                'game_name' => '_json:{"1":"Roulette Wheel","2":"轮盘"}',
                'english_name' => 'Roulette Wheel',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live],
                'game_platform_id' => $api_id,
                'html_five_enabled' => $db_true,

            ],
            [
                'game_code' => '5',
                'game_name' => '_json:{"1":"Fruit Machine","2":"水果机"}',
                'english_name' => 'Fruit Machine',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live],
                'game_platform_id' => $api_id,
                'html_five_enabled' => $db_true,
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
        );

        $this->load->model(['game_description_model']);

        $success=$this->game_description_model->syncGameDescription($game_descriptions);

        return $success;
    }

}