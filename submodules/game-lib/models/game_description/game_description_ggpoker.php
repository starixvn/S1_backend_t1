<?php
trait game_description_ggpoker {
    public function sync_game_description_ggpoker(){
        $api_id = GGPOKER_GAME_API;
        $this->ggpoker_game_list($api_id);
    }
    public function sync_game_description_ggpoker_t1(){
        $api_id = T1GGPOKER_GAME_API;
        $this->ggpoker_game_list($api_id);
    }

    public function ggpoker_game_list($api_id){
        $cnt=0;
        $cntInsert = 0;
        $cntUpdate = 0;

        $db_true = 1;
        $db_false = 0;
        // $api_id = GGPOKER_GAME_API;
        $now = $this->utils->getNowForMysql();

        $game_type_code_poker    = SYNC::TAG_CODE_POKER;
        $game_type_code_unknown  = SYNC::TAG_CODE_UNKNOWN_GAME;


        // sync game type first
        // use game_type_code as key
        $game_types = [
            $game_type_code_poker => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Poker"}',
                'game_type_lang' => '_json:{"1":"Poker"}',
                'game_type_code' => $game_type_code_poker,
                'game_tag_code' => SYNC::TAG_CODE_POKER
            ],
            $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown"}',
                'game_type_lang' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown"}',
                "game_type_code" => $game_type_code_unknown,
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
                'game_name' => '_json:{"1":"GG Poker","2":"GG Poker","3":"GG Poker","4":"GG Poker","5":"GG Poker"}',
                'game_code' => 'GG Poker',
                'game_platform_id' => $api_id,
                'external_game_id' => 'GG Poker',
                'english_name' => 'GG Poker',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_poker],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_false,
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
                'flag_show_in_site'=>$db_true,
                'updated_at'=>$now,
            ],

            #====================================End Game list=========================================
        );

        $this->load->model(['game_description_model']);

        $success=$this->game_description_model->syncGameDescription($game_descriptions);

        return $success;
    }

}
