<?php
trait game_description_rwb {

    public function sync_game_description_rwb($type = 'new'){
        $api_id= RWB_API;
        $db_true=1;
        $db_false=0;
        $now=$this->utils->getNowForMysql();

        $game_type_code_sports  = SYNC::TAG_CODE_SPORTS;
        $game_type_code_unknown = SYNC::TAG_CODE_UNKNOWN_GAME;


        //sync game type first
        //use game_type_code as key
        $game_types = [
            $game_type_code_sports => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Sports","2":"街机游戏","3":"Sports","4":"Sports","5":"Sports"}',
                'game_type_code' => $game_type_code_sports,
                'game_tag_code' => $game_type_code_sports,
            ],
            $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_type_code' => $game_type_code_unknown,
                'game_tag_code' => $game_type_code_unknown,
            ]
        ];

        echo "<pre>";
        $this->load->model(['game_type_model']);
        $gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);

        $this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

        $game_descriptions=[
            [
                'game_platform_id' => $api_id,
                'game_code' => '0',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Combo Parlay","2":"Combo Parlay","3":"Combo Parlay","4":"Combo Parlay","5":"Combo Parlay"}',
                "english_name" => "Combo Parlay",
                'external_game_id' => '0',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '1',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Soccer","2":"Soccer","3":"Soccer","4":"Soccer","5":"Soccer"}',
                "english_name" => "Soccer",
                'external_game_id' => 'Soccer',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '2',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Handball","2":"Handball","3":"Handball","4":"Handball","5":"Handball"}',
                "english_name" => "Handball",
                'external_game_id' => 'Handball',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '3',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Basketball","2":"Basketball","3":"Basketball","4":"Basketball","5":"Basketball"}',
                "english_name" => "Basketball",
                'external_game_id' => 'Basketball',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '4',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Tennis","2":"Tennis","3":"Tennis","4":"Tennis","5":"Tennis"}',
                "english_name" => "Tennis",
                'external_game_id' => 'Tennis',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '5',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"E sports","2":"E sports","3":"E sports","4":"E sports","5":"E sports"}',
                "english_name" => "E sports",
                'external_game_id' => 'E sports',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '6',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Snooker","2":"Snooker","3":"Snooker","4":"Snooker","5":"Snooker"}',
                "english_name" => "Snooker",
                'external_game_id' => 'Snooker',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '7',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Cricket","2":"Cricket","3":"Cricket","4":"Cricket","5":"Cricket"}',
                "english_name" => "Cricket",
                'external_game_id' => 'Cricket',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '8',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Baseball","2":"Baseball","3":"Baseball","4":"Baseball","5":"Baseball"}',
                "english_name" => "Baseball",
                'external_game_id' => 'Baseball',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '9',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Golf","2":"Golf","3":"Golf","4":"Golf","5":"Golf"}',
                "english_name" => "Golf",
                'external_game_id' => 'Golf',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '10',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Boxing","2":"Boxing","3":"Boxing","4":"Boxing","5":"Boxing"}',
                "english_name" => "Boxing",
                'external_game_id' => 'Boxing',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '11',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"American Football","2":"American Football","3":"American Football","4":"American Football","5":"American Football"}',
                "english_name" => "American Football",
                'external_game_id' => 'American Football',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '12',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Volleyball","2":"Volleyball","3":"Volleyball","4":"Volleyball","5":"Volleyball"}',
                "english_name" => "Volleyball",
                'external_game_id' => 'Volleyball',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '13',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Ice Hockey","2":"Ice Hockey","3":"Ice Hockey","4":"Ice Hockey","5":"Ice Hockey"}',
                "english_name" => "Ice Hockey",
                'external_game_id' => 'Ice Hockey',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '14',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Rugby Union","2":"Rugby Union","3":"Rugby Union","4":"Rugby Union","5":"Rugby Union"}',
                "english_name" => "Rugby Union",
                'external_game_id' => 'Rugby Union',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '15',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Curling","2":"Curling","3":"Curling","4":"Curling","5":"Curling"}',
                "english_name" => "Curling",
                'external_game_id' => 'Curling',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '16',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Table Tennis","2":"Table Tennis","3":"Table Tennis","4":"Table Tennis","5":"Table Tennis"}',
                "english_name" => "Table Tennis",
                'external_game_id' => 'Table Tennis',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '17',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Cycling","2":"Cycling","3":"Cycling","4":"Cycling","5":"Cycling"}',
                "english_name" => "Cycling",
                'external_game_id' => 'Cycling',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '18',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Aussie Rules","2":"Aussie Rules","3":"Aussie Rules","4":"Aussie Rules","5":"Aussie Rules"}',
                "english_name" => "Aussie Rules",
                'external_game_id' => 'Aussie Rules',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '19',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"MMA","2":"MMA","3":"MMA","4":"MMA","5":"MMA"}',
                "english_name" => "MMA",
                'external_game_id' => 'MMA',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '20',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Rugby League","2":"Rugby League","3":"Rugby League","4":"Rugby League","5":"Rugby League"}',
                "english_name" => "Rugby League",
                'external_game_id' => 'Rugby League',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '21',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Bandy","2":"Bandy","3":"Bandy","4":"Bandy","5":"Bandy"}',
                "english_name" => "Bandy",
                'external_game_id' => 'Bandy',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '22',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Badminton","2":"Badminton","3":"Badminton","4":"Badminton","5":"Badminton"}',
                "english_name" => "Badminton",
                'external_game_id' => 'Badminton',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '23',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Darts","2":"Darts","3":"Darts","4":"Darts","5":"Darts"}',
                "english_name" => "Darts",
                'external_game_id' => 'Darts',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '24',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Futsal","2":"Futsal","3":"Futsal","4":"Futsal","5":"Futsal"}',
                "english_name" => "Futsal",
                'external_game_id' => 'Futsal',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '25',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Beach Volleyball","2":"Beach Volleyball","3":"Beach Volleyball","4":"Beach Volleyball","5":"Beach Volleyball"}',
                "english_name" => "Beach Volleyball",
                'external_game_id' => 'Beach Volleyball',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],[
                'game_platform_id' => $api_id,
                'game_code' => '27',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Formula 1","2":"Formula 1","3":"Formula 1","4":"Formula 1","5":"Formula 1"}',
                "english_name" => "Formula 1",
                'external_game_id' => 'Formula 1',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],[
                'game_platform_id' => $api_id,
                'game_code' => '28',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Water Polo","2":"Water Polo","3":"Water Polo","4":"Water Polo","5":"Water Polo"}',
                "english_name" => "Water Polo",
                'external_game_id' => 'Water Polo',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],[
                'game_platform_id' => $api_id,
                'game_code' => '32',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Ski Jumping","2":"Ski Jumping","3":"Ski Jumping","4":"Ski Jumping","5":"Ski Jumping"}',
                "english_name" => "Ski Jumping",
                'external_game_id' => 'Ski Jumping',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],[
                'game_platform_id' => $api_id,
                'game_code' => '33',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Chess","2":"Chess","3":"Chess","4":"Chess","5":"Chess"}',
                "english_name" => "Chess",
                'external_game_id' => 'Chess',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],[
                'game_platform_id' => $api_id,
                'game_code' => '34',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
                'game_name' => '_json:{"1":"Alpine Skiing","2":"Alpine Skiing","3":"Alpine Skiing","4":"Alpine Skiing","5":"Alpine Skiing"}',
                "english_name" => "Alpine Skiing",
                'external_game_id' => 'Alpine Skiing',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],




            [
                'game_platform_id' => $api_id,
                'game_code' => 'unknown',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_unknown],
                'game_name' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                "english_name" => "Unknown",
                'external_game_id' => 'unknown',
                'status'=> $db_true,
                'flag_show_in_site'=>$db_false,
                'updated_at'=>$now,
            ],
        ];
        $this->load->model(['game_description_model']);

        // $success=$this->game_description_model->syncGameDescription($game_descriptions);

        $data = array();
        $cntInsert = 0;
        $cntUpdate = 0;
        foreach ($game_descriptions as $game_list) {


            if($type == 'new'){
                $game_code_exist = $this->db->select('COUNT(id) as count')
                                ->where('game_code', $game_list['game_code'])
                                ->where('game_platform_id', $api_id)
                                ->get('game_description')
                                ->row();
            } else {
                $game_code_exist = $this->db->select('COUNT(id) as count')
                                ->where('game_code',"ibc.games." .  $game_list['game_code'])
                                ->where('game_platform_id', $api_id)
                                ->get('game_description')
                                ->row();
            }

            if( $game_code_exist->count <= 0 ){
                echo "==============================================>Insert<===========================================";
                $game_list['created_on']= $now;
                $this->db->insert('game_description', $game_list);
                $cntInsert++;
            }else{
                echo "==============================================>Update<===========================================";
                $game_list['updated_at']= $now;
                if($type == 'new'){
                    $this->db->where('game_code', $game_list['game_code']);
                }else{
                    $this->db->where('game_code', "ibc.games.".$game_list['game_code']);
                }
                $this->db->where('game_platform_id', $api_id);
                $this->db->update('game_description', $game_list);
                $cntUpdate++;
            }
            print_r($game_list);
            // $cnt++;
        }

        echo "Total Inserted Games: " . $cntInsert;
        echo "Total Updated Games: " . $cntUpdate;
    }

}