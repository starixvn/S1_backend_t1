<?php
trait game_description_ezugi {

    public function sync_game_description_ezugi(){
        // $cnt=0;
        $success=true;
        $api_id=EZUGI_API;
        $db_true=1;
        $db_false=0;
        $now=$this->utils->getNowForMysql();

        //===game types======================================
        $game_type_code_live_dealer = SYNC::TAG_CODE_LIVE_DEALER;
        $game_type_code_unknown     = SYNC::TAG_CODE_UNKNOWN_GAME;
        $game_type_code_lottery     = SYNC::TAG_CODE_LOTTERY;

        //sync game type first
        //game_type_code from bbin document
        //use game_type_code as key
        $game_types = [
            $game_type_code_live_dealer => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Live Games","2":"真人游戏","3":"Live Games","4":"Live Games","5":"Live Games"}',
                'game_type_lang' => '_json:{"1":"Live Games","2":"真人游戏","3":"Live Games","4":"Live Games","5":"Live Games"}',
                'game_type_code' => $game_type_code_live_dealer,
                'game_tag_code' => SYNC::TAG_CODE_LIVE_DEALER
            ],
            $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_type_lang' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_type_code' => $game_type_code_unknown,
                'game_tag_code' => SYNC::TAG_CODE_UNKNOWN_GAME
            ],
            $game_type_code_lottery => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Lottery","2":"彩票游戏","3":"Lottery","4":"Lottery"}',
                'game_type_lang' => '_json:{"1":"Lottery","2":"彩票游戏","3":"Lottery","4":"Lottery"}',
                "game_type_code" => $game_type_code_lottery,
                'game_tag_code' => SYNC::TAG_CODE_LOTTERY
            ],
        ];
        echo "<pre>";
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
                'game_name' => '_json:{"1":"EZUGI UNKNOWN GAME","2":"EZUGI 未知游戏"}',
                'game_code' => 'unknown',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'EZUGI UNKNOWN GAME',
                'external_game_id' => 'unknown',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'game_name' => '_json:{"1":"Lottery OMS","2":"彩票OMS"}',
                'game_code' => '34',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'Lottery OMS',
                'external_game_id' => '34',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"American Roulette","2":"美式轮盘赌"}',
                'game_code' => '31',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'American Roulette',
                'external_game_id' => '31',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"Baccarat Dragon Bonus","2":"Baccarat Dragon Bonus"}',
                'game_code' => '26',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'Baccarat Dragon Bonus',
                'external_game_id' => '26',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"Baccarat No Commission","2":"无佣百家乐"}',
                'game_code' => '25',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'Baccarat No Commission',
                'external_game_id' => '25',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"Baccarat Dragon Tiger","2":"Baccarat Dragon Tiger"}',
                'game_code' => '24',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'Baccarat Dragon Tiger',
                'external_game_id' => '24',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"Baccarat Insurance","2":"Baccarat Insurance"}',
                'game_code' => '23',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'Baccarat Insurance',
                'external_game_id' => '23',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"Baccarat multi-seat","2":"Baccarat multi-seat"}',
                'game_code' => '22',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'Baccarat multi-seat',
                'external_game_id' => '22',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"Baccarat super 6","2":"Baccarat super 6"}',
                'game_code' => '21',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'Baccarat super 6',
                'external_game_id' => '21',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"Baccarat KO","2":"Baccarat KO"}',
                'game_code' => '20',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'Baccarat KO',
                'external_game_id' => '20',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"Casino Holdem","2":"Casino Holdem"}',
                'game_code' => '15',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'Casino Holdem',
                'external_game_id' => '15',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"Sic Bo","2":"骰宝"}',
                'game_code' => '14',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'Sic Bo',
                'external_game_id' => '14',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"American Hybrid Blackjack","2":"American Hybrid Blackjack"}',
                'game_code' => '11',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'American Hybrid Blackjack',
                'external_game_id' => '11',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"American Blackjack","2":"美式21点"}',
                'game_code' => '10',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'American Blackjack',
                'external_game_id' => '10',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"Sedie","2":"Sedie"}',
                'game_code' => '9',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'Sedie',
                'external_game_id' => '9',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"Wheel Of Dice","2":"Wheel Of Dice"}',
                'game_code' => '8',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'Wheel Of Dice',
                'external_game_id' => '8',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"Auto Roulette","2":"自動輪盤"}',
                'game_code' => '7',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'Auto Roulette',
                'external_game_id' => '7',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"Keno","2":"基诺"}',
                'game_code' => '6',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'Keno',
                'external_game_id' => '6',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"Hybrid Blackjack","2":"Hybrid 21点"}',
                'game_code' => '5',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'Hybrid Blackjack',
                'external_game_id' => '5',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"Lottery","2":"彩票"}',
                'game_code' => '4',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'Lottery',
                'external_game_id' => '4',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"Roulette","2":"轮盘"}',
                'game_code' => '3',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'Roulette',
                'external_game_id' => '3',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"Baccarat","2":"百家乐"}',
                'game_code' => '2',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'Baccarat',
                'external_game_id' => '2',
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_name' => '_json:{"1":"BlackJack","2":"21点"}',
                'game_code' => '1',
                'dlc_enabled' => $db_true,
                'mobile_enabled' => $db_true,
                'english_name' => 'BlackJack',
                'external_game_id' => '1',
            ],


        );

        $this->load->model(['game_description_model']);

        $success=$this->game_description_model->syncGameDescription($game_descriptions);

        return $success;
    }

}
