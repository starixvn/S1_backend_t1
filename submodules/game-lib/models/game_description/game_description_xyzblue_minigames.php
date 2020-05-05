<?php
trait game_description_xyzblue_minigames {

    public function sync_game_description_xyzblue_minigames(){
        $cnt=0;
        $cntInsert = 0;
        $cntUpdate = 0;

        $db_true = 1;
        $db_false = 0;
        $api_id = XYZBLUE_API;

        $game_type_code_ladder_game = "xyzblue_ladder_game";
        $game_type_code_dice_game = "xyzblue_dice_game";
        $game_type_code_dragon_tiger_game = "xyzblue_dragon_tiger_game";
        $game_type_code_even_odd_game = "xyzblue_even_odd_game";
        $game_type_code_table_and_cards = "xyzblue_table_and_cards";
        $game_type_code_unknown = "xyzblue_unknown";

        // sync game type first
        // use game_type_code as key
        $game_types = [
            $game_type_code_ladder_game => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Ladder Game","2":"Ladder Game","3":"Ladder Game","4":"Ladder Game","5":"Ladder Game"}',
                'game_type_code' => $game_type_code_ladder_game,
                'status' => $db_true,
                'game_tag_code' => SYNC::TAG_CODE_SPORTS
            ],
            $game_type_code_table_and_cards => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Table and Cards","2":"牌桌&牌游戏","3":"Table and Cards","4":"Table and Cards","5":"Table and Cards"}',
                'game_type_code' => $game_type_code_table_and_cards,
                'status' => $db_true,
                'game_tag_code' => SYNC::TAG_CODE_LIVE_DEALER
            ],
            $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown","5":"Unknown"}',
                "game_type_code" => $game_type_code_unknown,
                'flag_show_in_site' => $db_false,
                'status'=>$db_true,
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
            #====================================start Ladder Game list=========================================
            [
                'game_name' => '_json:{"1":"Ladder - football","2":"Ladder - football","3":"Ladder - football","4":"Ladder - football","5":"Ladder - football"}',
                'game_code' => '1',
                'html_five_enabled' => $db_true,
                'game_platform_id' => $api_id,
                'external_game_id' => '1',
                'english_name' => 'Ladder - football',
                'flash_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_ladder_game],
                'attributes'   => 'soccer'
            ],
            [
                'game_name' => '_json:{"1":"Ladder - basketball","2":"Ladder - basketball","3":"Ladder - basketball","4":"Ladder - basketball","5":"Ladder - basketball"}',
                'game_code' => '2',
                'html_five_enabled' => $db_true,
                'game_platform_id' => $api_id,
                'external_game_id' => '2',
                'english_name' => 'Ladder - basketball',
                'flash_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_ladder_game],
                'attributes'   => 'basketball'
            ],
            [
                'game_name' => '_json:{"1":"Ladder - baseball","2":"Ladder - baseball","3":"Ladder - baseball","4":"Ladder - baseball","5":"Ladder - baseball"}',
                'game_code' => '3',
                'html_five_enabled' => $db_true,
                'game_platform_id' => $api_id,
                'external_game_id' => '3',
                'english_name' => 'Ladder - baseball',
                'flash_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_ladder_game],
                'attributes'   => 'baseball'
            ],
            [
                'game_name' => '_json:{"1":"Ladder - cricket","2":"Ladder - cricket","3":"Ladder - cricket","4":"Ladder - cricket","5":"Ladder - cricket"}',
                'game_code' => '4',
                'html_five_enabled' => $db_true,
                'game_platform_id' => $api_id,
                'external_game_id' => '4',
                'english_name' => 'Ladder - cricket',
                'flash_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_ladder_game],
                'attributes'   => 'cricket'
            ],
            #====================================End Game list=========================================

            #====================================Start Dice Game list=========================================
            [
                'game_name' => '_json:{"1":"Dice Game","2":"Dice Game","3":"Dice Game","4":"Dice Game","5":"Dice Game"}',
                'game_code' => '5',
                'html_five_enabled' => $db_true,
                'game_platform_id' => $api_id,
                'external_game_id' => '5',
                'english_name' => 'Dice Game',
                'flash_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_table_and_cards],
                'attributes'   => 'dice'
            ],
            ##====================================End Game list=========================================

            ##====================================Start Dragon Tiger Game list=========================================
            [
                'game_name' => '_json:{"1":"Dragon Tiger","2":"Dragon Tiger","3":"Dragon Tiger","4":"Dragon Tiger","5":"Dragon Tiger"}',
                'game_code' => '6',
                'html_five_enabled' => $db_true,
                'game_platform_id' => $api_id,
                'external_game_id' => '6',
                'english_name' => 'Dragon Tiger',
                'flash_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_table_and_cards],
                'attributes'   => 'dragontiger'
            ],
            ##====================================End Game list=========================================

            ##====================================Start Even/Odd Game list=========================================
            [
                'game_name' => '_json:{"1":"Even/Odd","2":"Even/Odd","3":"Even/Odd","4":"Even/Odd","5":"Even/Odd"}',
                'game_code' => '7',
                'html_five_enabled' => $db_true,
                'game_platform_id' => $api_id,
                'external_game_id' => '7',
                'english_name' => 'Even/Odd',
                'flash_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_table_and_cards],
                'attributes'   => 'oddeven'
            ],
            ##====================================End Game list=========================================

            ##================================================== Unknown Game ========================================
            [
                'game_name' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown"}',
                'game_code' => 'unknown',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_unknown],
                // 'game_type' => Table Game
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
