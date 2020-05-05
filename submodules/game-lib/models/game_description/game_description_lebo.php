<?php
trait game_description_lebo {

	public function sync_game_description_lebo(){
		$cnt=0;
		$cntInsert = 0;
		$cntUpdate = 0;

		$db_true = 1;
		$db_false = 0;
		$api_id = LEBO_GAME_API;
		$now = $this->utils->getNowForMysql();

        $game_type_code_lottery     = SYNC::TAG_CODE_LOTTERY;
        $game_type_code_unknown     = SYNC::TAG_CODE_UNKNOWN_GAME;

		// sync game type first
		// use game_type_code as key
        $game_types = [
            $game_type_code_lottery => [
                'game_platform_id'     => $api_id,
                'game_type'            => '_json:{"1":"Lottery","2":"彩票游戏","3":"Lottery","4":"Lottery","5":"Lottery"}',
                'game_type_lang'       => '_json:{"1":"Lottery","2":"彩票游戏","3":"Lottery","4":"Lottery","5":"Lottery"}',
                'game_type_code'       => $game_type_code_lottery,
                'game_tag_code'        => $game_type_code_lottery,
            ],
            $game_type_code_unknown => [
                'game_platform_id'     => $api_id,
                'game_type'            => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_type_lang'       => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                "game_type_code"       => $game_type_code_unknown,
                "game_tag_code"        => $game_type_code_unknown,
            ],

		];


		$this->load->model(['game_type_model']);
		$gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);
		$this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

        $game_descriptions = array(
                [
                'game_name'         => '_json:{"1":"Shanghai Lotto","2":"上海时时乐","3":"Shanghai Lotto","4":"Shanghai Lotto","5":"Shanghai Lotto"}',
                'game_code'         => 'ssl',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'ssl',
                'english_name'      => 'Shanghai Lotto',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Lotto 3D","2":"福彩3D","3":"Lotto 3D","4":"Lotto 3D","5":"Lotto 3D"}',
                'game_code'         => 'sd',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'sd',
                'english_name'      => 'Lotto 3D',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                    'game_name'     => '_json:{"1":"Sports Lotto","2":"体彩排列三","3":"Sports Lotto","4":"Sports Lotto","5":"Sports Lotto"}',
                'game_code'         => 'ps',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'ps',
                'english_name'      => 'Sports Lotto',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Chongqing Lotto","2":"重庆时时彩","3":"Chongqing Lotto","4":"Chongqing Lotto","5":"Chongqing Lotto"}',
                'game_code'         => 'cqssc',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'cqssc',
                'english_name'      => 'Chongqing Lotto',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Hong Kong Mark Six","2":"香港六合彩","3":"Hong Kong Mark Six","4":"Hong Kong Mark Six","5":"Hong Kong Mark Six"}',
                'game_code'         => 'lhc',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'lhc',
                'english_name'      => 'Hong Kong Mark Six',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Guangdong Happy 10","2":"广东快乐十分","3":"Guangdong Happy 10","4":"Guangdong Happy 10","5":"Guangdong Happy 10"}',
                'game_code'         => 'klsf',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'klsf',
                'english_name'      => 'Guangdong Happy 10',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Tianjin Happy 10","2":"天津快乐十分","3":"Tianjin Happy 10","4":"Tianjin Happy 10","5":"Tianjin Happy 10"}',
                'game_code'         => 'tjklsf',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'tjklsf',
                'english_name'      => 'Tianjin Happy 10',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Tianjin Lotto","2":"天津时时彩","3":"Tianjin Lotto","4":"Tianjin Lotto","5":"Tianjin Lotto"}',
                'game_code'         => 'tjssc',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'tjssc',
                'english_name'      => 'Tianjin Lotto',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"JSKS","2":"江苏快３","3":"JSKS","4":"JSKS","5":"JSKS"}',
                'game_code'         => 'jsks',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'jsks',
                'english_name'      => 'JSKS',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Jilin Fast 3","2":"吉林快３","3":"Jilin Fast 3","4":"Jilin Fast 3","5":"Jilin Fast 3"}',
                'game_code'         => 'jlks',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'jlks',
                'english_name'      => 'Jilin Fast 3',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Beijing PK10","2":"北京赛车","3":"Beijing PK10","4":"Beijing PK10","5":"Beijing PK10"}',
                'game_code'         => 'pk',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'pk',
                'english_name'      => 'Beijing PK10',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Chongqing Happy 10","2":"重慶幸运农场","3":"Chongqing Happy 10","4":"Chongqing Happy 10","5":"Chongqing Happy 10"}',
                'game_code'         => 'cqklsf',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'cqklsf',
                'english_name'      => 'Chongqing Happy 10',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Beijing Happy 8","2":"北京快乐8","3":"Beijing Happy 8","4":"Beijing Happy 8","5":"Beijing Happy 8"}',
                'game_code'         => 'klfp',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'klfp',
                'english_name'      => 'Beijing Happy 8',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Beijing Fast 3","2":"北京快３","3":"Beijing Fast 3","4":"Beijing Fast 3","5":"Beijing Fast 3"}',
                'game_code'         => 'bjks',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'bjks',
                'english_name'      => 'Beijing Fast 3',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Guangxi Happy 10","2":"广西快乐十分","3":"Guangxi Happy 10","4":"Guangxi Happy 10","5":"Guangxi Happy 10"}',
                'game_code'         => 'gxklsf',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'gxklsf',
                'english_name'      => 'Guangxi Happy 10',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"HunanKLSF","2":"湖南快乐十分","3":"HunanKLSF","4":"HunanKLSF","5":"HunanKLSF"}',
                'game_code'         => 'hnklsf',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'hnklsf',
                'english_name'      => 'HunanKLSF',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Shandong 11/5","2":"山东11选5","3":"Shandong 11/5","4":"Shandong 11/5","5":"Shandong 11/5"}',
                'game_code'         => 'sdsyxw',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'sdsyxw',
                'english_name'      => 'Shandong 11/5',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Guangdong 11/5","2":"广东11选5","3":"Guangdong 11/5","4":"Guangdong 11/5","5":"Guangdong 11/5"}',
                'game_code'         => 'gdsyxw',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'gdsyxw',
                'english_name'      => 'Guangdong 11/5',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Jilin 11/5","2":"吉林11选5","3":"Jilin 11/5","4":"Jilin 11/5","5":"Jilin 11/5"}',
                'game_code'         => 'jlsyxw',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'jlsyxw',
                'english_name'      => 'Jilin 11/5',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Xinjiang Lotto","2":"新疆时时彩","3":"Xinjiang Lotto","4":"Xinjiang Lotto","5":"Xinjiang Lotto"}',
                'game_code'         => 'xjssc',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'xjssc',
                'english_name'      => 'Xinjiang Lotto',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Jiangsu Sic Bo","2":"江苏骰宝","3":"Jiangsu Sic Bo","4":"Jiangsu Sic Bo","5":"Jiangsu Sic Bo"}',
                'game_code'         => 'jssb',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'jssb',
                'english_name'      => 'Jiangsu Sic Bo',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Jilin Sic Bo","2":"吉林骰宝","3":"Jilin Sic Bo","4":"Jilin Sic Bo","5":"Jilin Sic Bo"}',
                'game_code'         => 'jlsb',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'jlsb',
                'english_name'      => 'Jilin Sic Bo',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"Lucky Airship","2":"幸运飞艇","3":"Lucky Airship","4":"Lucky Airship","5":"Lucky Airship"}',
                'game_code'         => 'xyft',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'xyft',
                'english_name'      => 'Lucky Airship',
                'status'            => $db_true,
                'flag_show_in_site' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_lottery],
                ],
                [
                'game_name'         => '_json:{"1":"UNKNOWN GAME","2":"不明游戏","3":"UNKNOWN GAME","4":"UNKNOWN GAME","5":"UNKNOWN GAME"}',
                'game_code'         => 'unknown',
                'game_platform_id'  => $api_id,
                'external_game_id'  => 'unknown',
                'english_name'      => 'UNKNOWN GAME',
                'status'            => $db_true,
                'flag_show_in_site' => $db_false,
                'game_type_id'      => $gameTypeCodeMaps[$game_type_code_unknown],
                ],
                );

                $this->load->model(['game_description_model']);

                $success=$this->game_description_model->syncGameDescription($game_descriptions);

                return $success;
    }
}
