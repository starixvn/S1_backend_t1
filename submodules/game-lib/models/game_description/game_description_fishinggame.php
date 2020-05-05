<?php
trait game_description_fishinggame {

    public function sync_game_description_fishinggame(){
        $api_id = FISHINGGAME_API;
        $this->fishinggame_game_list($api_id);
    }

    public function sync_game_description_fishinggame_t1(){
        $api_id = T1GG_API;
        $this->fishinggame_game_list($api_id);
    }

    public function fishinggame_game_list($api_id,$game_type_codes = null){

        $db_true = 1;
        $db_false = 0;
        $now = $this->utils->getNowForMysql();

        $game_type_code_fishinggame = SYNC::TAG_CODE_FISHING_GAME;
        $game_type_code_unknown = SYNC::TAG_CODE_UNKNOWN_GAME;

        // sync game type first
        // use game_type_code as key
        $game_types = [
            $game_type_code_fishinggame => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Fishing Game","2":"Fishing Game","3":"Fishing Game","4":"Fishing Game","5":"Fishing Game"}',
                'game_type_lang' => '_json:{"1":"Fishing Game","2":"Fishing Game","3":"Fishing Game","4":"Fishing Game","5":"Fishing Game"}',
                'game_type_code' => $game_type_code_fishinggame,
                'flag_show_in_site' => $db_true,
                'auto_add_new_game' => $db_true,
                'auto_add_to_cashback' => $db_true,
                'status' => $db_true,
                'updated_at' => $now,
                'game_tag_code' => $game_type_code_fishinggame
            ],
            $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_type_lang' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                "game_type_code" => $game_type_code_unknown,
                'flag_show_in_site' => $db_true,
                'auto_add_new_game' => $db_true,
                'auto_add_to_cashback' => $db_true,
                'status'=>$db_true,
                'updated_at'=>$now,
                'game_tag_code' => $game_type_code_unknown
            ],
        ];


        $this->load->model(['game_type_model']);
        $gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);
        // echo "<pre>";
        // print_r($gameTypeCodeMaps);
        // die();
        $this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

        $game_descriptions = array(
            # ========================================== Blackjack ============================================================
            [
                'game_name' => '_json:{"1":"King of Match","2":"单挑王","3":"King of Match","4":"King of Match","5":"King of Match"}',
                'game_code' => '103',
                'game_platform_id' => $api_id,
                'external_game_id' => '103',
                'english_name' => 'King of Match',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_fishinggame],
            ],
            [
                'game_name' => '_json:{"1":"Fish Word","2":"不明","3":"Fish Word","4":"Fish Word","5":"Fish Word"}',
                'game_code' => '101',
                'game_platform_id' => $api_id,
                'external_game_id' => '101',
                'english_name' => 'Fish Word',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_fishinggame],
            ],
            [
                'game_name' => '_json:{"1":"Fruit machine","2":"水果机","3":"Fruit machine","4":"Fruit machine","5":"Fruit machine"}',
                'game_code' => '102',
                'game_platform_id' => $api_id,
                'external_game_id' => '102',
                'english_name' => 'Fruit machine',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'flag_show_in_site' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_fishinggame],
            ],
            [
                'game_name' => '_json:{"1":"Golden & Silver","2":"金鲨银鲨","3":"Golden & Silver","4":"Golden & Silver","5":"Golden & Silver"}',
                'game_code' => '104',
                'game_platform_id' => $api_id,
                'external_game_id' => '104',
                'english_name' => 'Golden & Silver',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'flag_show_in_site' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_fishinggame],
            ],
            [
                'game_name' => '_json:{"1":"Lucky five","2":"金鲨银鲨","3":"Lucky five","4":"Lucky five","5":"Lucky five"}',
                'game_code' => '105',
                'game_platform_id' => $api_id,
                'external_game_id' => '105',
                'english_name' => 'Lucky five',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'flag_show_in_site' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_fishinggame],
            ],
            [
                'game_name' => '_json:{"1":"Big fish eat small fish","2":"大鱼吃小鱼","3":"Big fish eat small fish","4":"Big fish eat small fish","5":"Big fish eat small fish"}',
                'game_code' => '106',
                'game_platform_id' => $api_id,
                'external_game_id' => '106',
                'english_name' => 'Big fish eat small fish',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'flag_show_in_site' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_fishinggame],
            ],
            [
                'game_name' => '_json:{"1":"Shoot the gantry","2":"射龙门","3":"Shoot the gantry","4":"Shoot the gantry","5":"Shoot the gantry"}',
                'game_code' => '107',
                'game_platform_id' => $api_id,
                'external_game_id' => '107',
                'english_name' => 'Shoot the gantry',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'flag_show_in_site' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_fishinggame],
            ],
            [
                'game_name' => '_json:{"1":"DiamondDeal","2":"钻石迷城","3":"DiamondDeal","4":"DiamondDeal","5":"DiamondDeal"}',
                'game_code' => '108',
                'game_platform_id' => $api_id,
                'external_game_id' => '108',
                'english_name' => 'DiamondDeal',
                'mobile_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'flag_show_in_site' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_fishinggame],
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