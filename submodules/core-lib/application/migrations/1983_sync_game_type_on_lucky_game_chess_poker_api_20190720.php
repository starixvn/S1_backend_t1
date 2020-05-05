<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_sync_game_type_on_lucky_game_chess_poker_api_20190720 extends CI_Migration {

    public function up() {
        $this->load->model(['game_type_model']);

        $gamePlatformId=LUCKY_GAME_CHESS_POKER_API;
        $gameTypeList=[
            [
                'game_platform_id'=>$gamePlatformId,
                'game_type_unique_code'=>'lottery',
                'game_type_name_detail'=>buildLangDetail('Lottery', '彩票'),
                'game_type_status'=>Game_type_model::DB_BOOL_MAP[Game_type_model::DB_TRUE],
            ],
        ];
        $this->game_type_model->syncFrom($gamePlatformId, $gameTypeList);
    }

    public function down() {
    }
}