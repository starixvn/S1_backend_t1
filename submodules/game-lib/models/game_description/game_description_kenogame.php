<?php
trait game_description_kenogame {

    public function sync_game_description_kenogame (){
        $db_true = 1;
        $db_false = 0;
        $api_id = KENOGAME_API;
        $now = $this->utils->getNowForMysql();

        $game_type_code_lottery     = SYNC::TAG_CODE_LOTTERY;
        $game_type_code_unknown     = SYNC::TAG_CODE_UNKNOWN_GAME;

        $game_types = [
            $game_type_code_lottery => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"KENO","2":"KENO","3":"KENO","4":"KENO"}',
                'game_type_lang' => '_json:{"1":"KENO","2":"KENO","3":"KENO","4":"KENO"}',
                "game_type_code" => $game_type_code_lottery,
                "game_tag_code" => $game_type_code_lottery,
            ],
            $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_type_lang' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                "game_type_code" => $game_type_code_unknown,
                "game_tag_code" => $game_type_code_unknown,
            ],
        ];

        $this->load->model(['game_type_model']);
        //this code is checking the game type table with game
        //also insertion of new game type based on game_type_code
        $gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);

        $this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

        $game_descriptions = array(
            # ================================================================ Kenogame list ============================================================
            [
                'game_name' => '_json:{"1":"Super PK10","2":"超级奇乐 PK10","3":"Super PK10","4":"Super PK10","5":"Super PK10"}',
                'game_code' => 'PK10 SUP',
                'game_platform_id' => $api_id,
                'external_game_id' => 'PK10 SUP',
                'english_name' => 'Super PK10',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Beijing PK10","2":"超级时时彩 PK10","3":"Beijing PK10","4":"Beijing PK10","5":"Beijing PK10"}',
                'game_code' => 'PK10 PEK',
                'game_platform_id' => $api_id,
                'external_game_id' => 'PK10 PEK',
                'english_name' => 'Beijing PK10',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Super Keno","2":"超级时时彩 SSC","3":"Super Keno","4":"Super Keno","5":"Super Keno"}',
                'game_code' => 'SSC SUP',
                'game_platform_id' => $api_id,
                'external_game_id' => 'SSC SUP',
                'english_name' => 'Super Keno',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Fucai 3D","2":"新疆 SSC","3":"Fucai 3D","4":"Fucai 3D","5":"Fucai 3D"}',
                'game_code' => 'SSC 3D',
                'game_platform_id' => $api_id,
                'external_game_id' => 'SSC 3D',
                'english_name' => 'Fucai 3D',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Xinjiang SSC","2":"新疆 SSC","3":"Xinjiang SSC","4":"Xinjiang SSC","5":"Xinjiang SSC"}',
                'game_code' => 'SSC XJ',
                'game_platform_id' => $api_id,
                'external_game_id' => 'SSC XJ',
                'english_name' => 'Xinjiang SSC',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Tianjing SSC","2":"天津 SSC","3":"Tianjing SSC","4":"Tianjing SSC","5":"Tianjing SSC"}',
                'game_code' => 'SSC TSN',
                'game_platform_id' => $api_id,
                'external_game_id' => 'SSC TSN',
                'english_name' => 'Tianjing SSC',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Shanghai SSC","2":"上海 SSC","3":"Shanghai SSC","4":"Shanghai SSC","5":"Shanghai SSC"}',
                'game_code' => 'SSC SHA',
                'game_platform_id' => $api_id,
                'external_game_id' => 'SSC SHA',
                'english_name' => 'Shanghai SSC',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Chongqing SSC","2":"重庆 SSC","3":"Chongqing SSC","4":"Chongqing SSC","5":"Chongqing SSC"}',
                'game_code' => 'SSC CKG',
                'game_platform_id' => $api_id,
                'external_game_id' => 'SSC CKG',
                'english_name' => 'Chongqing SSC',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Super Keno","2":"斯洛伐克 Keno","3":"Super Keno","4":"Super Keno","5":"Super Keno"}',
                'game_code' => 'KENO SUP',
                'game_platform_id' => $api_id,
                'external_game_id' => 'KENO SUP',
                'english_name' => 'Super Keno',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Slovakia","2":"斯洛伐克 Keno","3":"Slovakia","4":"Slovakia","5":"Slovakia"}',
                'game_code' => 'KENO SK',
                'game_platform_id' => $api_id,
                'external_game_id' => 'KENO SK',
                'english_name' => 'Slovakia',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Beijing Keno","2":"北京 Keno","3":"Beijing Keno","4":"Beijing Keno","5":"Beijing Keno"}',
                'game_code' => 'KENO PEK',
                'game_platform_id' => $api_id,
                'external_game_id' => 'KENO PEK',
                'english_name' => 'Beijing Keno',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Canada Keno","2":"加拿大 Keno","3":"Canada Keno","4":"Canada Keno","5":"Canada Keno"}',
                'game_code' => 'KENO CAN',
                'game_platform_id' => $api_id,
                'external_game_id' => 'KENO CAN',
                'english_name' => 'Canada Keno',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Canada West Keno","2":"加拿大西部 Keno","3":"Canada West Keno","4":"Canada West Keno","5":"Canada West Keno"}',
                'game_code' => 'KENO CAW',
                'game_platform_id' => $api_id,
                'external_game_id' => 'KENO CAW',
                'english_name' => 'Canada West Keno',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
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

            #====================================End Game list=========================================
        );

        $this->load->model(['game_description_model']);

        $success=$this->game_description_model->syncGameDescription($game_descriptions);

        return $success;
    }

}
