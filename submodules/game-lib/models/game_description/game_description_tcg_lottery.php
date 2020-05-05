<?php
trait game_description_tcg_lottery {

    public function sync_game_description_tcg_lottery(){
        $api_id = TCG_API;
        $this->tcg_lottery_game_list($api_id);
    }

    public function tcg_lottery_game_list($api_id){

        $db_true = 1;
        $db_false = 0;
        $now = $this->utils->getNowForMysql();

        $game_type_code_lottery= SYNC::TAG_CODE_LOTTERY;
        $game_type_code_unknown = SYNC::TAG_CODE_UNKNOWN_GAME;

        $game_types = [
            $game_type_code_lottery => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Lottery Game","2":"彩票游戏","3":"Lottery Game","4":"Lottery Game","5":"Lottery Game"}',
                'game_type_lang' => '_json:{"1":"Lottery Game","2":"彩票游戏","3":"Lottery Game","4":"Lottery Game","5":"Lottery Game"}',
                'game_type_code' => $game_type_code_lottery,
                'game_tag_code' => $game_type_code_lottery,
            ],
            $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_type_lang' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_type_code' => $game_type_code_unknown,
                'game_tag_code' => $game_type_code_unknown,
            ],
        ];


        $this->load->model(['game_type_model']);
        $gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);

        $this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

        # TO CHANGE ENGLISH NAME
        $game_descriptions = array(
            [
                'game_name' => '_json:{"1":"TXFFC","2":"腾讯分分彩","3":"TXFFC","4":"TXFFC","5":"TXFFC"}',
                'game_code' => 'TXFFC',
                'game_platform_id' => $api_id,
                'external_game_id' => 'TXFFC',
                'english_name' => 'TXFFC',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],

            [
                'game_name' => '_json:{"1":"B2BCFFC","2":"天成分分彩","3":"B2BCFFC","4":"B2BCFFC","5":"B2BCFFC"}',
                'game_code' => 'B2BCFFC',
                'game_platform_id' => $api_id,
                'external_game_id' => 'B2BCFFC',
                'english_name' => 'B2BCFFC',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],

            [
                'game_name' => '_json:{"1":"B2BCSSCC","2":"天成五分彩","3":"B2BCSSCC","4":"B2BCSSCC","5":"B2BCSSCC"}',
                'game_code' => 'B2BCSSCC',
                'game_platform_id' => $api_id,
                'external_game_id' => 'B2BCSSCC',
                'english_name' => 'B2BCSSCC',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],

            [
                'game_name' => '_json:{"1":"CQSSC","2":"重庆时时彩","3":"CQSSC","4":"CQSSC","5":"CQSSC"}',
                'game_code' => 'CQSSC',
                'game_platform_id' => $api_id,
                'external_game_id' => 'CQSSC',
                'english_name' => 'CQSSC',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],

            [
                'game_name' => '_json:{"1":"XJSSC","2":"新疆时时彩","3":"XJSSC","4":"XJSSC","5":"XJSSC"}',
                'game_code' => 'XJSSC',
                'game_platform_id' => $api_id,
                'external_game_id' => 'XJSSC',
                'english_name' => '新疆时时彩',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],
            [
                'game_name' => '_json:{"1":"TCFFC","2":"天秤分分彩","3":"TCFFC","4":"TCFFC","5":"TCFFC"}',
                'game_code' => 'TCFFC',
                'game_platform_id' => $api_id,
                'external_game_id' => 'TCFFC',
                'english_name' => 'TCFFC',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],

            [
                'game_name' => '_json:{"1":"B2BC11X5","2":"天成十一选五","3":"B2BC11X5","4":"B2BC11X5","5":"B2BC11X5"}',
                'game_code' => 'B2BC11X5',
                'game_platform_id' => $api_id,
                'external_game_id' => 'B2BC11X5',
                'english_name' => 'B2BC11X5',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],

            [
                'game_name' => '_json:{"1":"SD11X5","2":"山东十一选五","3":"SD11X5","4":"SD11X5","5":"SD11X5"}',
                'game_code' => 'SD11X5',
                'game_platform_id' => $api_id,
                'external_game_id' => 'SD11X5',
                'english_name' => 'SD11X5',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],
            [
                'game_name' => '_json:{"1":"JX11X5","2":"江西十一选五","3":"JX11X5","4":"JX11X5","5":"JX11X5"}',
                'game_code' => 'JX11X5',
                'game_platform_id' => $api_id,
                'external_game_id' => 'JX11X5',
                'english_name' => 'JX11X5',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],
            [
                'game_name' => '_json:{"1":"TC11X5","2":"双鱼11选5","3":"TC11X5","4":"TC11X5","5":"TC11X5"}',
                'game_code' => 'TC11X5',
                'game_platform_id' => $api_id,
                'external_game_id' => 'TC11X5',
                'english_name' => 'TC11X5',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],

            [
                'game_name' => '_json:{"1":"SH11X5","2":"上海十一选五","3":"SH11X5","4":"SH11X5","5":"SH11X5"}',
                'game_code' => 'SH11X5',
                'game_platform_id' => $api_id,
                'external_game_id' => 'SH11X5',
                'english_name' => 'SH11X5',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],

            [
                'game_name' => '_json:{"1":"SH11X5","2":"福彩3D","3":"SH11X5","4":"SH11X5","5":"SH11X5"}',
                'game_code' => 'FC3D',
                'game_platform_id' => $api_id,
                'external_game_id' => 'FC3D',
                'english_name' => 'SH11X5',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],
            [
                'game_name' => '_json:{"1":"TCP3P5","2":"体彩P3P5","3":"TCP3P5","4":"TCP3P5","5":"TCP3P5"}',
                'game_code' => 'TCP3P5',
                'game_platform_id' => $api_id,
                'external_game_id' => 'TCP3P5',
                'english_name' => 'TCP3P5',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],

            [
                'game_name' => '_json:{"1":"HK6","2":"香港⑥合彩","3":"HK6","4":"HK6","5":"HK6"}',
                'game_code' => 'HK6',
                'game_platform_id' => $api_id,
                'external_game_id' => 'HK6',
                'english_name' => 'HK6',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],
            [
                'game_name' => '_json:{"1":"B2BCLHC","2":"天成六合彩","3":"B2BCLHC","4":"B2BCLHC","5":"B2BCLHC"}',
                'game_code' => 'B2BCLHC',
                'game_platform_id' => $api_id,
                'external_game_id' => 'B2BCLHC',
                'english_name' => 'B2BCLHC',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],

            [
                'game_name' => '_json:{"1":"BJPK10","2":"北京赛车PK10","3":"BJPK10","4":"BJPK10","5":"BJPK10"}',
                'game_code' => 'BJPK10',
                'game_platform_id' => $api_id,
                'external_game_id' => 'BJPK10',
                'english_name' => 'BJPK10',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],

            [
                'game_name' => '_json:{"1":"B2BCPK10","2":"天成PK10","3":"B2BCPK10","4":"B2BCPK10","5":"B2BCPK10"}',
                'game_code' => 'B2BCPK10',
                'game_platform_id' => $api_id,
                'external_game_id' => 'B2BCPK10',
                'english_name' => 'B2BCPK10',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],
            [
                'game_name' => '_json:{"1":"XYPK10","2":"幸运飞艇","3":"XYPK10","4":"XYPK10","5":"XYPK10"}',
                'game_code' => 'XYPK10',
                'game_platform_id' => $api_id,
                'external_game_id' => 'XYPK10',
                'english_name' => 'XYPK10',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],
            [
                'game_name' => '_json:{"1":"CA28","2":"加拿大28","3":"CA28","4":"CA28","5":"CA28"}',
                'game_code' => 'CA28',
                'game_platform_id' => $api_id,
                'external_game_id' => 'CA28',
                'english_name' => 'CA28',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],

            [
                'game_name' => '_json:{"1":"TW28","2":"台湾28","3":"TW28","4":"TW28","5":"TW28"}',
                'game_code' => 'TW28',
                'game_platform_id' => $api_id,
                'external_game_id' => 'TW28',
                'english_name' => 'TW28',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],
            [
                'game_name' => '_json:{"1":"B2BC28","2":"天成28","3":"B2BC28","4":"B2BC28","5":"B2BC28"}',
                'game_code' => 'B2BC28',
                'game_platform_id' => $api_id,
                'external_game_id' => 'B2BC28',
                'english_name' => 'B2BC28',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],
            [
                'game_name' => '_json:{"1":"BJLK28","2":"北京幸运28","3":"BJLK28","4":"BJLK28","5":"BJLK28"}',
                'game_code' => 'BJLK28',
                'game_platform_id' => $api_id,
                'external_game_id' => 'BJLK28',
                'english_name' => 'BJLK28',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],
            [
                'game_name' => '_json:{"1":"SHK3","2":"上海快三","3":"SHK3","4":"SHK3","5":"SHK3"}',
                'game_code' => 'SHK3',
                'game_platform_id' => $api_id,
                'external_game_id' => 'SHK3',
                'english_name' => 'SHK3',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],
            [
                'game_name' => '_json:{"1":"B2BCK3","2":"天成快三","3":"B2BCK3","4":"B2BCK3","5":"B2BCK3"}',
                'game_code' => 'B2BCK3',
                'game_platform_id' => $api_id,
                'external_game_id' => 'B2BCK3',
                'english_name' => 'B2BCK3',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],
            [
                'game_name' => '_json:{"1":"HUBK3","2":"湖北快三","3":"HUBK3","4":"HUBK3","5":"HUBK3"}',
                'game_code' => 'HUBK3',
                'game_platform_id' => $api_id,
                'external_game_id' => 'HUBK3',
                'english_name' => 'HUBK3',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],
            [
                'game_name' => '_json:{"1":"GXK3","2":"广西快三","3":"GXK3","4":"GXK3","5":"GXK3"}',
                'game_code' => 'GXK3',
                'game_platform_id' => $api_id,
                'external_game_id' => 'GXK3',
                'english_name' => 'GXK3',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'enabled_on_android' => $db_true,
                'enabled_on_ios' => $db_true
            ],
        );

        $this->load->model(['game_description_model']);

        $success=$this->game_description_model->syncGameDescription($game_descriptions);

        return $success;
    }

}