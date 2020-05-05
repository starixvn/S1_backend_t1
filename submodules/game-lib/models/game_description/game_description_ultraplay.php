<?php
trait game_description_ultraplay {

    public function sync_game_description_ultraplay(){
        $cnt=0;
        $cntInsert = 0;
        $cntUpdate = 0;

        $db_true = 1;
        $db_false = 0;
        $api_id = ULTRAPLAY_API;
        $now = $this->utils->getNowForMysql();

        $game_type_code_sport = SYNC::TAG_CODE_SPORTS;
        $game_type_code_casino = SYNC::TAG_CODE_UNKNOWN_GAME;
        $game_type_code_unknown = "white_label_unknown";


        // sync game type first
        // use game_type_code as key
        $game_types = [
            $game_type_code_sport => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Sports Book"}',
                'game_type_lang' => '_json:{"1":"Sports Book"}',
                'game_type_code' => $game_type_code_sport,
                'updated_at' => $now,
                'game_tag_code' => SYNC::TAG_CODE_SPORTS
            ],
            $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown"}',
                'game_type_lang' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown"}',
                "game_type_code" => $game_type_code_unknown,
                'updated_at'=>$now,
                'game_tag_code' => SYNC::TAG_CODE_UNKNOWN_GAME
            ],
        ];


        $this->load->model(['game_type_model']);
        //this code is checking the game type table with game
        //also insertion of new game type based on game_type_code
        // 'game_type_id' => $gameTypeCodeMaps[$game_type_code_table_game],
        $gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);
        // echo "<pre>";
        // print_r($gameTypeCodeMaps);
        // die();
        $this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

        $game_descriptions = array(
            #================================================== Sports Game ========================================
           [
                'game_name'=>'_json:{"1":"Soccer","2":"Soccer"}',
                'english_name'=> 'Soccer',
                'game_platform_id' => $api_id,
                'game_code' => 'Soccer',
                'external_game_id' => 'Soccer',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Basketball","2":"Basketball"}',
                'english_name'=> 'Basketball',
                'game_platform_id' => $api_id,
                'game_code' => 'Basketball',
                'external_game_id' => 'Basketball',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Baseball","2":"Baseball"}',
                'english_name'=> 'Baseball',
                'game_platform_id' => $api_id,
                'game_code' => 'Baseball',
                'external_game_id' => 'Baseball',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Tennis","2":"Tennis"}',
                'english_name'=> 'Tennis',
                'game_platform_id' => $api_id,
                'game_code' => 'Tennis',
                'external_game_id' => 'Tennis',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Ice Hockey","2":"Ice Hockey"}',
                'english_name'=> 'Ice Hockey',
                'game_platform_id' => $api_id,
                'game_code' => 'Ice Hockey',
                'external_game_id' => 'Ice Hockey',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Volleyball","2":"Volleyball"}',
                'english_name'=> 'Volleyball',
                'game_platform_id' => $api_id,
                'game_code' => 'Volleyball',
                'external_game_id' => 'Volleyball',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"American Football","2":"American Football"}',
                'english_name'=> 'American Football',
                'game_platform_id' => $api_id,
                'game_code' => 'American Football',
                'external_game_id' => 'American Football',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Rugby League","2":"Rugby League"}',
                'english_name'=> 'Rugby League',
                'game_platform_id' => $api_id,
                'game_code' => 'Rugby League',
                'external_game_id' => 'Rugby League',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Rugby Union","2":"Rugby Union"}',
                'english_name'=> 'Rugby Union',
                'game_platform_id' => $api_id,
                'game_code' => 'Rugby Union',
                'external_game_id' => 'Rugby Union',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Badminton","2":"Badminton"}',
                'english_name'=> 'Badminton',
                'game_platform_id' => $api_id,
                'game_code' => 'Badminton',
                'external_game_id' => 'Badminton',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Formula 1","2":"Formula 1"}',
                'english_name'=> 'Formula 1',
                'game_platform_id' => $api_id,
                'game_code' => 'Formula 1',
                'external_game_id' => 'Formula 1',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Waterpolo","2":"Waterpolo"}',
                'english_name'=> 'Waterpolo',
                'game_platform_id' => $api_id,
                'game_code' => 'Waterpolo',
                'external_game_id' => 'Waterpolo',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Darts","2":"Darts"}',
                'english_name'=> 'Darts',
                'game_platform_id' => $api_id,
                'game_code' => 'Darts',
                'external_game_id' => 'Darts',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"E-Sports","2":"E-Sports"}',
                'english_name'=> 'E-Sports',
                'game_platform_id' => $api_id,
                'game_code' => 'E-Sports',
                'external_game_id' => 'E-Sports',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"MMA","2":"MMA"}',
                'english_name'=> 'MMA',
                'game_platform_id' => $api_id,
                'game_code' => 'MMA',
                'external_game_id' => 'MMA',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Boxing","2":"Boxing"}',
                'english_name'=> 'Boxing',
                'game_platform_id' => $api_id,
                'game_code' => 'Boxing',
                'external_game_id' => 'Boxing',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Handball","2":"Handball"}',
                'english_name'=> 'Handball',
                'game_platform_id' => $api_id,
                'game_code' => 'Handball',
                'external_game_id' => 'Handball',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Beach Volley","2":"Beach Volley"}',
                'english_name'=> 'Beach Volley',
                'game_platform_id' => $api_id,
                'game_code' => 'Beach Volley',
                'external_game_id' => 'Beach Volley',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Snooker","2":"Snooker"}',
                'english_name'=> 'Snooker',
                'game_platform_id' => $api_id,
                'game_code' => 'Snooker',
                'external_game_id' => 'Snooker',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Futsal","2":"Futsal"}',
                'english_name'=> 'Futsal',
                'game_platform_id' => $api_id,
                'game_code' => 'Futsal',
                'external_game_id' => 'Futsal',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Beach Soccer","2":"Beach Soccer"}',
                'english_name'=> 'Beach Soccer',
                'game_platform_id' => $api_id,
                'game_code' => 'Beach Soccer',
                'external_game_id' => 'Beach Soccer',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Chess","2":"Chess"}',
                'english_name'=> 'Chess',
                'game_platform_id' => $api_id,
                'game_code' => 'Chess',
                'external_game_id' => 'Chess',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Golf","2":"Golf"}',
                'english_name'=> 'Golf',
                'game_platform_id' => $api_id,
                'game_code' => 'Golf',
                'external_game_id' => 'Golf',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Table tennis","2":"Table tennis"}',
                'english_name'=> 'Table tennis',
                'game_platform_id' => $api_id,
                'game_code' => 'Table tennis',
                'external_game_id' => 'Table tennis',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Aussie rules","2":"Aussie rules"}',
                'english_name'=> 'Aussie rules',
                'game_platform_id' => $api_id,
                'game_code' => 'Aussie rules',
                'external_game_id' => 'Aussie rules',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Bandy","2":"Bandy"}',
                'english_name'=> 'Bandy',
                'game_platform_id' => $api_id,
                'game_code' => 'Bandy',
                'external_game_id' => 'Bandy',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Cricket","2":"Cricket"}',
                'english_name'=> 'Cricket',
                'game_platform_id' => $api_id,
                'game_code' => 'Cricket',
                'external_game_id' => 'Cricket',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Alpine Skiing","2":"Alpine Skiing"}',
                'english_name'=> 'Alpine Skiing',
                'game_platform_id' => $api_id,
                'game_code' => 'Alpine Skiing',
                'external_game_id' => 'Alpine Skiing',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Cycling","2":"Cycling"}',
                'english_name'=> 'Cycling',
                'game_platform_id' => $api_id,
                'game_code' => 'Cycling',
                'external_game_id' => 'Cycling',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Field Hockey","2":"Field Hockey"}',
                'english_name'=> 'Field Hockey',
                'game_platform_id' => $api_id,
                'game_code' => 'Field Hockey',
                'external_game_id' => 'Field Hockey',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Horse Racing","2":"Horse Racing"}',
                'english_name'=> 'Horse Racing',
                'game_platform_id' => $api_id,
                'game_code' => 'Horse Racing',
                'external_game_id' => 'Horse Racing',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Biathlon","2":"Biathlon"}',
                'english_name'=> 'Biathlon',
                'game_platform_id' => $api_id,
                'game_code' => 'Biathlon',
                'external_game_id' => 'Biathlon',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Ski Jumping","2":"Ski Jumping"}',
                'english_name'=> 'Ski Jumping',
                'game_platform_id' => $api_id,
                'game_code' => 'Ski Jumping',
                'external_game_id' => 'Ski Jumping',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Curling","2":"Curling"}',
                'english_name'=> 'Curling',
                'game_platform_id' => $api_id,
                'game_code' => 'Curling',
                'external_game_id' => 'Curling',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Cross Country","2":"Cross Country"}',
                'english_name'=> 'Cross Country',
                'game_platform_id' => $api_id,
                'game_code' => 'Cross Country',
                'external_game_id' => 'Cross Country',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Other Sports","2":"Other Sports"}',
                'english_name'=> 'Other Sports',
                'game_platform_id' => $api_id,
                'game_code' => 'Other Sports',
                'external_game_id' => 'Other Sports',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Virtual Tennis","2":"Virtual Tennis"}',
                'english_name'=> 'Virtual Tennis',
                'game_platform_id' => $api_id,
                'game_code' => 'Virtual Tennis',
                'external_game_id' => 'Virtual Tennis',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Virtual Soccer","2":"Virtual Soccer"}',
                'english_name'=> 'Virtual Soccer',
                'game_platform_id' => $api_id,
                'game_code' => 'Virtual Soccer',
                'external_game_id' => 'Virtual Soccer',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Softball","2":"Softball"}',
                'english_name'=> 'Softball',
                'game_platform_id' => $api_id,
                'game_code' => 'Softball',
                'external_game_id' => 'Softball',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Squash","2":"Squash"}',
                'english_name'=> 'Squash',
                'game_platform_id' => $api_id,
                'game_code' => 'Squash',
                'external_game_id' => 'Squash',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Athletics","2":"Athletics"}',
                'english_name'=> 'Athletics',
                'game_platform_id' => $api_id,
                'game_code' => 'Athletics',
                'external_game_id' => 'Athletics',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Volleyball (Points)","2":"Volleyball (Points)"}',
                'english_name'=> 'Volleyball (Points)',
                'game_platform_id' => $api_id,
                'game_code' => 'Volleyball (Points)',
                'external_game_id' => 'Volleyball (Points)',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Virtual Basketball","2":"Virtual Basketball"}',
                'english_name'=> 'Virtual Basketball',
                'game_platform_id' => $api_id,
                'game_code' => 'Virtual Basketball',
                'external_game_id' => 'Virtual Basketball',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Sailing","2":"Sailing"}',
                'english_name'=> 'Sailing',
                'game_platform_id' => $api_id,
                'game_code' => 'Sailing',
                'external_game_id' => 'Sailing',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Other","2":"Other"}',
                'english_name'=> 'Other',
                'game_platform_id' => $api_id,
                'game_code' => 'Other',
                'external_game_id' => 'Other',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"ESport Street Fighter V","2":"ESport Street Fighter V"}',
                'english_name'=> 'ESport Street Fighter V',
                'game_platform_id' => $api_id,
                'game_code' => 'ESport Street Fighter V',
                'external_game_id' => 'ESport Street Fighter V',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Alpine","2":"Alpine"}',
                'english_name'=> 'Alpine',
                'game_platform_id' => $api_id,
                'game_code' => 'Alpine',
                'external_game_id' => 'Alpine',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            [
                'game_name'=>'_json:{"1":"Biathlon","2":"Biathlon"}',
                'english_name'=> 'Biathlon',
                'game_platform_id' => $api_id,
                'game_code' => 'Biathlon',
                'external_game_id' => 'Biathlon',
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sport],
            ],
            #================================================== Unknown Game ========================================
            [
                'game_name' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown"}',
                'game_code' => 'unknown',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_unknown],
                // 'game_type' => Table Game
                'external_game_id' => 'unknown',
                'english_name' => 'Unknown',
                'game_platform_id' => $api_id,
                'status'=> $db_true,
                'updated_at'=>$now,
            ],

            #====================================End Game list=========================================
        );

        $this->load->model(['game_description_model']);

        $success=$this->game_description_model->syncGameDescription($game_descriptions);

        return $success;
    }

}
