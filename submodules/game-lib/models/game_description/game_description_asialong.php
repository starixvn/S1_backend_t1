<?php
trait game_description_asialong {

    public function sync_game_description_asialong(){
        $cnt=0;
        $cntInsert = 0;
        $cntUpdate = 0;

        $db_true = 1;
        $db_false = 0;
        $api_id = ASIALONG_API;
        $now = $this->utils->getNowForMysql();

        $game_type_code_default = "asialong_game";
        $game_type_code_unknown = "asialong_game_unknown";

        // sync game type first
        // use game_type_code as key
        $game_types = [
            $game_type_code_default => [
                'game_platform_id'     => $api_id,
                'game_type'            => '_json:{"1":"Lottery","2":"彩票游戏","3":"Lottery","4":"Lottery","5":"Lottery"}',
                'game_type_lang'       => '_json:{"1":"Lottery","2":"彩票游戏","3":"Lottery","4":"Lottery","5":"Lottery"}',
                'game_type_code'       => $game_type_code_default,
                'flag_show_in_site'    => $db_true,
                'auto_add_new_game'    => $db_true,
                'auto_add_to_cashback' => $db_true,
                'status'               => $db_true,
                'updated_at'           => $now,
            ],
            $game_type_code_unknown => [
                'game_platform_id'     => $api_id,
                'game_type'            => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_type_lang'       => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                "game_type_code"       => $game_type_code_unknown,
                'flag_show_in_site'    => $db_true,
                'auto_add_new_game'    => $db_true,
                'auto_add_to_cashback' => $db_true,
                'status'               => $db_true,
                'updated_at'           => $now,
            ],

        ];

        $this->load->model(['game_type_model']);
        $gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);
        $this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

        $game_descriptions = array(
            [
                'game_name'         => '_json:{"1":"PK10","2":"北京赛车","3":"PK10","4":"PK10","5":"PK10"}',
                'game_code'         => 'pk',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'pk',
                'english_name'      => 'PK10',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_default],
            ],
            [
                'game_name'         => '_json:{"1":"Bull Fighting","2":"红火牛","3":"Bull Fighting","4":"Bull Fighting","5":"Bull Fighting"}',
                'game_code'         => 'rc',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'rc',
                'english_name'      => 'Bull Fighting',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_default],
            ],
            [
                'game_name'         => '_json:{"1":"SSC","2":"时时彩","3":"SSC","4":"SSC","5":"SSC"}',
                'game_code'         => 'ct',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'ct',
                'english_name'      => 'SSC',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_default],
            ],
            [
                'game_name' => '_json:{"1":"unknown","2":"不明","3":"unknown","4":"unknown","5":"unknown"}',
                'game_code' => 'unknown',
                'game_platform_id' => $api_id,
                'external_game_id' => 'unknown',
                'english_name' => 'unknown',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_unknown],
            ],
        );

        $this->load->model(['game_description_model']);

        $success=$this->game_description_model->syncGameDescription($game_descriptions);

        return $success;
    }

}
