<?php
trait game_description_t1lottery {

    public function sync_game_description_t1lottery_t1(){
        $api_id = T1LOTTERY_EXT_API;
        $this->t1lottery_game_list($api_id);
    }
    public function sync_game_description_t1lottery(){
        $api_id = T1LOTTERY_API;
        $this->t1lottery_game_list($api_id);
    }
    public function t1lottery_game_list($api_id){

        $db_true = 1;
        $db_false = 0;
        $now = $this->utils->getNowForMysql();

        $game_type_code_lottery= SYNC::TAG_CODE_LOTTERY;
        $game_type_code_unknown = SYNC::TAG_CODE_UNKNOWN_GAME;

        // sync game type first
        // use game_type_code as key
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

        $game_descriptions = array(
            [
                'game_name' => '_json:{"1":"Lottery 1","2":"重庆时时彩","3":"Lottery 1","4":"Lottery 1","5":"Lottery 1"}',
                'game_code' => '1',
                'game_platform_id' => $api_id,
                'external_game_id' => '1',
                'english_name' => 'Lottery 1',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Lottery 2","2":"新疆时时彩","3":"Lottery 2","4":"Lottery 2","5":"Lottery 2"}',
                'game_code' => '2',
                'game_platform_id' => $api_id,
                'external_game_id' => '2',
                'english_name' => 'Lottery 2',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Lottery 3","2":"天津时时彩","3":"Lottery 3","4":"Lottery 3","5":"Lottery 3"}',
                'game_code' => '3',
                'game_platform_id' => $api_id,
                'external_game_id' => '3',
                'english_name' => 'Lottery 3',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Lottery 4","2":"香港六合彩","3":"Lottery 4","4":"Lottery 4","5":"Lottery 4"}',
                'game_code' => '4',
                'game_platform_id' => $api_id,
                'external_game_id' => '4',
                'english_name' => 'Lottery 4',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Lottery 5","2":"山东11选5","3":"Lottery 5","4":"Lottery 5","5":"Lottery 5"}',
                'game_code' => '5',
                'game_platform_id' => $api_id,
                'external_game_id' => '5',
                'english_name' => 'Lottery 5',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Lottery 6","2":"江西11选5","3":"Lottery 6","4":"Lottery 6","5":"Lottery 6"}',
                'game_code' => '6',
                'game_platform_id' => $api_id,
                'external_game_id' => '6',
                'english_name' => 'Lottery 6',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Lottery 7","2":"广东11选5","3":"Lottery 7","4":"Lottery 7","5":"Lottery 7"}',
                'game_code' => '7',
                'game_platform_id' => $api_id,
                'external_game_id' => '7',
                'english_name' => 'Lottery 7',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Lottery 8","2":"江苏快3","3":"Lottery 8","4":"Lottery 8","5":"Lottery 8"}',
                'game_code' => '8',
                'game_platform_id' => $api_id,
                'external_game_id' => '8',
                'english_name' => 'Lottery 8',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Lottery 9","2":"安徽快3","3":"Lottery 9","4":"Lottery 9","5":"Lottery 9"}',
                'game_code' => '9',
                'game_platform_id' => $api_id,
                'external_game_id' => '9',
                'english_name' => 'Lottery 9',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Lottery 10","2":"北京赛车","3":"Lottery 10","4":"Lottery 10","5":"Lottery 10"}',
                'game_code' => '10',
                'game_platform_id' => $api_id,
                'external_game_id' => '10',
                'english_name' => 'Lottery 10',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Lottery 11","2":"福彩3D","3":"Lottery 11","4":"Lottery 11","5":"Lottery 11"}',
                'game_code' => '11',
                'game_platform_id' => $api_id,
                'external_game_id' => '11',
                'english_name' => 'Lottery 11',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Lottery 12","2":"排列3","3":"Lottery 12","4":"Lottery 12","5":"Lottery 12"}',
                'game_code' => '12',
                'game_platform_id' => $api_id,
                'external_game_id' => '12',
                'english_name' => 'Lottery 12',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Lottery 13","2":"排列5","3":"Lottery 13","4":"Lottery 13","5":"Lottery 13"}',
                'game_code' => '13',
                'game_platform_id' => $api_id,
                'external_game_id' => '13',
                'english_name' => 'Lottery 13',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Lottery 14","2":"重庆快乐十分","3":"Lottery 14","4":"Lottery 14","5":"Lottery 14"}',
                'game_code' => '14',
                'game_platform_id' => $api_id,
                'external_game_id' => '14',
                'english_name' => 'Lottery 14',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Lottery 15","2":"广东快乐十分","3":"Lottery 15","4":"Lottery 15","5":"Lottery 15"}',
                'game_code' => '15',
                'game_platform_id' => $api_id,
                'external_game_id' => '15',
                'english_name' => 'Lottery 15',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Lottery 16","2":"超级大乐透","3":"Lottery 16","4":"Lottery 16","5":"Lottery 16"}',
                'game_code' => '16',
                'game_platform_id' => $api_id,
                'external_game_id' => '16',
                'english_name' => 'Lottery 16',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Lottery 17","2":"双色球","3":"Lottery 17","4":"Lottery 17","5":"Lottery 17"}',
                'game_code' => '17',
                'game_platform_id' => $api_id,
                'external_game_id' => '17',
                'english_name' => 'Lottery 17',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Lottery 18","2":"樂趣分分彩","3":"Lottery 18","4":"Lottery 18","5":"Lottery 18"}',
                'game_code' => '18',
                'game_platform_id' => $api_id,
                'external_game_id' => '18',
                'english_name' => 'Lottery 18',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
            ],
            [
                'game_name' => '_json:{"1":"Lottery 19","2":"樂趣秒秒彩","3":"Lottery 19","4":"Lottery 19","5":"Lottery 19"}',
                'game_code' => '19',
                'game_platform_id' => $api_id,
                'external_game_id' => '19',
                'english_name' => 'Lottery 19',
                'mobile_enabled' => $db_true,
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
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