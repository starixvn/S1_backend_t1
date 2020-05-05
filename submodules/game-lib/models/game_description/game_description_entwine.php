<?php
trait game_description_entwine {

    public function sync_game_description_entwine(){
        $cnt=0;
        $cntInsert = 0;
        $cntUpdate = 0;

        $db_true = 1;
        $db_false = 0;
        $api_id = ENTWINE_API;
        $now = $this->utils->getNowForMysql();

        $game_type_code_live_dealer = SYNC::TAG_CODE_LIVE_DEALER;
        $game_type_code_unknown     = SYNC::TAG_CODE_UNKNOWN_GAME;

        // sync game type first
        // use game_type_code as key
        $game_types = [
            $game_type_code_live_dealer => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Live Game","2":"真人游戏","3":"Live Game","4":"Live Game","5":"Live Game"}',
                'game_type_lang' => '_json:{"1":"Live Game","2":"真人游戏","3":"Live Game","4":"Live Game","5":"Live Game"}',
                'game_type_code' => $game_type_code_live_dealer,
                'game_tag_code' => SYNC::TAG_CODE_CASINO,
            ],
            $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_type_lang' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                "game_type_code" => $game_type_code_unknown,
                'game_tag_code' => SYNC::TAG_CODE_UNKNOWN_GAME,
            ],
        ];


        $this->load->model(['game_type_model']);
        $gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);
        // echo "<pre>";
        // print_r($gameTypeCodeMaps);
        // die();
        // $this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

        $game_descriptions = array(
            [
                "game_name" => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'english_name' => "Unknown",
                'game_code' => "unknown",
                'external_game_id' => "unknown",
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_unknown],
            ],
            [
                "game_name" => '_json:{"1":"Baccarat","2":"百家乐","3":"Baccarat","4":"Baccarat","5":"Baccarat"}',
                'english_name' => "Baccarat",
                'game_code' => "90091",
                'external_game_id' => "90091",
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                "game_name" => '_json:{"1":"Baccarat non-commision","2":"免佣百家乐","3":"Baccarat non-commision","4":"Baccarat non-commision","5":"Baccarat non-commision"}',
                'english_name' => "Baccarat non-commision",
                'game_code' => "90092",
                'external_game_id' => "90092",
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                "game_name" => '_json:{"1":"Roulette","2":"轮盘","3":"Roulette","4":"Roulette","5":"Roulette"}',
                'english_name' => "Roulette",
                'game_code' => "50002",
                'external_game_id' => "50002",
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                "game_name" => '_json:{"1":"RNG Roulette","2":"电子轮盘","3":"RNG Roulette","4":"RNG Roulette","5":"RNG Roulette"}',
                'english_name' => "RNG Roulette",
                'game_code' => "51002",
                'external_game_id' => "51002",
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                "game_name" => '_json:{"1":"Sicbo","2":"骰宝","3":"Sicbo","4":"Sicbo","5":"Sicbo"}',
                'english_name' => "Sicbo",
                'game_code' => "60001",
                'external_game_id' => "60001",
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                "game_name" => '_json:{"1":"RNG Sicbo","2":"电子骰宝","3":"RNG Sicbo","4":"RNG Sicbo","5":"RNG Sicbo"}',
                'english_name' => "RNG Sicbo",
                'game_code' => "61001",
                'external_game_id' => "61001",
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_code' => '10001',
                'game_name' => '_json:{"1":"Baccarat Traditional","2":"Baccarat Traditional","3":"Baccarat Traditional","4":"Baccarat Traditional","5":"Baccarat Traditional"}',
                'english_name' => "Baccarat Traditional",
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_code' => '10002',
                'game_name' => '_json:{"1":"Baccarat Pair","2":"Baccarat Pair","3":"Baccarat Pair","4":"Baccarat Pair","5":"Baccarat Pair"}',
                'english_name' => "Baccarat Pair",
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_code' => '20001',
                'game_name' => '_json:{"1":"Dragon/Tiger","2":"Dragon/Tiger","3":"Dragon/Tiger","4":"Dragon/Tiger","5":"Dragon/Tiger"}',
                'english_name' => "Dragon/Tiger",
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_code' => '30001',
                'game_name' => '_json:{"1":"VIP Baccarat","2":"VIP Baccarat","3":"VIP Baccarat","4":"VIP Baccarat","5":"VIP Baccarat"}',
                'english_name' => "VIP Baccarat",
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_code' => '50001',
                'game_name' => '_json:{"1":"Asian Roulette","2":"Asian Roulette","3":"Asian Roulette","4":"Asian Roulette","5":"Asian Roulette"}',
                'english_name' => "Asian Roulette",
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_code' => '50002',
                'game_name' => '_json:{"1":"International Roulette","2":"International Roulette","3":"International Roulette","4":"International Roulette","5":"International Roulette"}',
                'english_name' => "International Roulette",
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_code' => '50003',
                'game_name' => '_json:{"1":"Roulette","2":"Roulette","3":"Roulette","4":"Roulette","5":"Roulette"}',
                'english_name' => "Roulette",
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_code' => '60001',
                'game_name' => '_json:{"1":"Sic Bo","2":"Sic Bo","3":"Sic Bo","4":"Sic Bo","5":"Sic Bo"}',
                'english_name' => "Sic Bo",
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_code' => '90001',
                'game_name' => '_json:{"1":"Super Baccarat","2":"Super Baccarat","3":"Super Baccarat","4":"Super Baccarat","5":"Super Baccarat"}',
                'english_name' => "Super Baccarat",
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_code' => '90002',
                'game_name' => '_json:{"1":"Super 6 Baccarat","2":"Super 6 Baccarat","3":"Super 6 Baccarat","4":"Super 6 Baccarat","5":"Super 6 Baccarat"}',
                'english_name' => "Super 6 Baccarat",
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_code' => '90003',
                'game_name' => '_json:{"1":"Dragon Bonus Baccarat","2":"Dragon Bonus Baccarat","3":"Dragon Bonus Baccarat","4":"Dragon Bonus Baccarat","5":"Dragon Bonus Baccarat"}',
                'english_name' => "Dragon Bonus Baccarat",
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_code' => '90004',
                'game_name' => '_json:{"1":"Points Baccarat","2":"Points Baccarat","3":"Points Baccarat","4":"Points Baccarat","5":"Points Baccarat"}',
                'english_name' => "Points Baccarat",
            ],
            [
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'game_code' => '100001',
                'game_name' => '_json:{"1":"Blackjack","2":"Blackjack","3":"Blackjack","4":"Blackjack","5":"Blackjack"}',
                'english_name' => "Blackjack",
            ],
            #====================================End Game list=========================================
        );

        $this->load->model(['game_description_model']);

        $success=$this->game_description_model->syncGameDescription($game_descriptions);

        // foreach ($game_descriptions as $game_list) {

        //     try {
        //         $game_code_exist = $this->db->select('COUNT(id) as count')
        //                         ->where('game_code', $game_list['game_code'])
        //                         ->where('game_platform_id', $api_id)
        //                         ->get('game_description');

        //         if( $game_code_exist->result_array()[0]['count'] == 0 ){

        //             echo "================================================= Insert ===========================================" . $game_code_exist->result_array()[0]['count'];
        //             $game_list['created_on'] =  $now;
        //             $this->db->insert('game_description', $game_list);
        //             $cntInsert++;
        //         }else{
        //             echo "================================================= Update ===========================================";
        //             $game_list['updated_at'] =  $now;
        //             $this->db->where('game_code', $game_list['game_code']);
        //             $this->db->where('game_platform_id', $api_id);
        //             $this->db->update('game_description', $game_list);
        //             $cntUpdate++;
        //         }
        //     echo "<pre>";
        //     print_r($game_list);

        //     } catch (Exception $e) {
        //         return "Error query";
        //     }

        //     $cnt++;
        // }
        return $success;
    }

}