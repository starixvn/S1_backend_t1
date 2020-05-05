<?php
trait game_description_ld_lottery {

    public function sync_game_description_ld_lottery(){

        $db_true = 1;
        $db_false = 0;
        $api_id = LD_LOTTERY_API;
        $now = $this->utils->getNowForMysql();

        $game_type_code_lottery     = SYNC::TAG_CODE_LOTTERY;
        $game_type_code_unknown     = SYNC::TAG_CODE_UNKNOWN_GAME;

        $game_types = [
            $game_type_code_lottery => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Lottery Game","2":"彩票游戏","3":"Lottery Game","4":"Lottery Game","5":"Lottery Game"}',
                'game_type_lang' => '_json:{"1":"Lottery Game","2":"彩票游戏","3":"Lottery Game","4":"Lottery Game","5":"Lottery Game"}',
                'game_type_code' => $game_type_code_lottery,
                'updated_at' => $now,
                'game_tag_code' => SYNC::TAG_CODE_LOTTERY
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

        $this->load->model(['game_type_model']);
        $gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);
        $this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

        $game_descriptions = array(
            [
                'game_name' => '_json:{"1":"北京PK拾","2":"北京PK拾"}',
                'game_code' => '北京PK拾',
                'game_platform_id' => $api_id,
                'external_game_id' => '北京PK拾',
                'english_name' => '北京PK拾',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"北京赛车","2":"北京赛车"}',
                'game_code' => '北京赛车',
                'game_platform_id' => $api_id,
                'external_game_id' => '北京赛车',
                'english_name' => '北京赛车',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"天津时时彩","2":"天津时时彩"}',
                'game_code' => '天津时时彩',
                'game_platform_id' => $api_id,
                'external_game_id' => '天津时时彩',
                'english_name' => '天津时时彩',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"广西快乐10分","2":"广西快乐10分"}',
                'game_code' => '广西快乐10分',
                'game_platform_id' => $api_id,
                'external_game_id' => '广西快乐10分',
                'english_name' => '广西快乐10分',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"新疆时时彩","2":"新疆时时彩"}',
                'game_code' => '新疆时时彩',
                'game_platform_id' => $api_id,
                'external_game_id' => '新疆时时彩',
                'english_name' => '新疆时时彩',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"极速三分彩","2":"极速三分彩"}',
                'game_code' => '极速三分彩',
                'game_platform_id' => $api_id,
                'external_game_id' => '极速三分彩',
                'english_name' => '极速三分彩',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"极速分分彩","2":"极速分分彩"}',
                'game_code' => '极速分分彩',
                'game_platform_id' => $api_id,
                'external_game_id' => '极速分分彩',
                'english_name' => '极速分分彩',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"极速快乐10分","2":"极速快乐10分"}',
                'game_code' => '极速快乐10分',
                'game_platform_id' => $api_id,
                'external_game_id' => '极速快乐10分',
                'english_name' => '极速快乐10分',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"极速赛车","2":"极速赛车"}',
                'game_code' => '极速赛车',
                'game_platform_id' => $api_id,
                'external_game_id' => '极速赛车',
                'english_name' => '极速赛车',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"极速飞艇","2":"极速飞艇"}',
                'game_code' => '极速飞艇',
                'game_platform_id' => $api_id,
                'external_game_id' => '极速飞艇',
                'english_name' => '极速飞艇',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"腾讯分分彩","2":"腾讯分分彩"}',
                'game_code' => '腾讯分分彩',
                'game_platform_id' => $api_id,
                'external_game_id' => '腾讯分分彩',
                'english_name' => '腾讯分分彩',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"重庆时时彩","2":"重庆时时彩"}',
                'game_code' => '重庆时时彩',
                'game_platform_id' => $api_id,
                'external_game_id' => '重庆时时彩',
                'english_name' => '重庆时时彩',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],

            [
                'game_name' => '_json:{"1":"LD Lottery Game","2":"LD Lottery Game","3":"LD Lottery Game","4":"LD Lottery Game","5":"LD Lottery Game"}',
                'game_code' => 'ld_lottery',
                'game_platform_id' => $api_id,
                'external_game_id' => 'ld_lottery',
                'english_name' => 'LD Lottery Game',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
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
        );

        $this->load->model(['game_description_model']);

        $success=$this->game_description_model->syncGameDescription($game_descriptions);

        return $success;
    }

}