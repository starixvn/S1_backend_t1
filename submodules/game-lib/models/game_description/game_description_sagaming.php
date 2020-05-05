<?php
trait game_description_sagaming {

    public function sync_game_description_sagaming(){
        $api_id = SA_GAMING_API;
        $this->sagaming_game_list($api_id);
    }

    public function sync_game_description_sagaming_t1(){
        $api_id = T1SA_GAMING_API;
        $this->sagaming_game_list($api_id);
    }

	public function sagaming_game_list($api_id){
		$cnt=0;
		$cntInsert = 0;
		$cntUpdate = 0;

		$db_true = 1;
		$db_false = 0;
		$now = $this->utils->getNowForMysql();

        $game_type_code_live_dealer = SYNC::TAG_CODE_LIVE_DEALER;
        $game_type_code_slots       = SYNC::TAG_CODE_SLOT;
        $game_type_code_lottery     = SYNC::TAG_CODE_LOTTERY;
        $game_type_code_mini_games  = SYNC::TAG_CODE_MINI_GAMES;
        $game_type_code_unknown     = SYNC::TAG_CODE_UNKNOWN_GAME;

		// use game_type_code as key
		$game_types = [
			$game_type_code_slots => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Slot Game","2":"老虎機"}',
                'game_type_lang' => '_json:{"1":"Slot Game","2":"老虎機"}',
                'game_type_code' => $game_type_code_slots,
                'game_tag_code' => SYNC::TAG_CODE_SLOT,
                'updated_at' => $now,
            ],
            $game_type_code_live_dealer => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Live Games","2":"真人游戏","3":"Live Games","4":"Live Games","5":"Live Games"}',
                'game_type_lang' => '_json:{"1":"Live Games","2":"真人游戏","3":"Live Games","4":"Live Games","5":"Live Games"}',
                'game_type_code' => $game_type_code_live_dealer,
                'game_tag_code' => SYNC::TAG_CODE_LIVE_DEALER,
                'updated_at' => $now,
            ],
            $game_type_code_lottery => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Lotto 48/Lotto 48 Novelty Bet","2":"Lotto 48/Lotto 48 Novelty Bet"}',
                'game_type_lang' => '_json:{"1":"Lotto 48/Lotto 48 Novelty Bet","2":"Lotto 48/Lotto 48 Novelty Bet"}',
                'game_type_code' => $game_type_code_lottery,
                'game_tag_code' => SYNC::TAG_CODE_LOTTERY,
                'updated_at' => $now,
            ],
            $game_type_code_mini_games => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Mini game","2":"Mini game"}',
                'game_type_lang' => '_json:{"1":"Mini game","2":"Mini game"}',
                'game_type_code' => $game_type_code_mini_games,
                'game_tag_code' => SYNC::TAG_CODE_MINI_GAMES,
                'updated_at' => $now,
            ],
            $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown"}',
                'game_type_lang' => '_json:{"1":"Unknown","2":"不明类型","3":"Unknown","4":"Unknown"}',
                "game_type_code" => $game_type_code_unknown,
                'game_tag_code' => SYNC::TAG_CODE_UNKNOWN_GAME,
                'updated_at'=>$now,
            ],
		];

        echo "<pre>";
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
            [
                'game_name' => '_json:{"1":"Havoc In Heaven","2":"大闹天宫","3":"Havoc In Heaven","4":"Havoc In Heaven","5":"Havoc In Heaven"}',
                'game_code' => 'EG-SLOT-S001',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-S001',
                'english_name' => 'Havoc In Heaven',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Moon Lady","2":"嫦娥奔月","3":"Moon Lady","4":"Moon Lady","5":"Moon Lady"}',
                'game_code' => 'EG-SLOT-S002',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-S002',
                'english_name' => 'Moon Lady',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"New Year Rich","2":"过大年","3":"New Year Rich","4":"New Year Rich","5":"New Year Rich"}',
                'game_code' => 'EG-SLOT-A001',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-A001',
                'english_name' => 'New Year Rich',
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Three Star God","2":"三星报囍","3":"Three Star God","4":"Three Star God","5":"Three Star God"}',
                'game_code' => 'EG-SLOT-A002',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-A002',
                'english_name' => 'Three Star God',
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Creepy Cuddlers","2":"趣怪丧尸","3":"Creepy Cuddlers","4":"Creepy Cuddlers","5":"Creepy Cuddlers"}',
                'game_code' => 'EG-SLOT-A012',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-A012',
                'english_name' => 'Creepy Cuddlers',
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Fantasy Goddess","2":"梦幻女神","3":"Fantasy Goddess","4":"Fantasy Goddess","5":"Fantasy Goddess"}',
                'game_code' => 'EG-SLOT-A005',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-A005',
                'english_name' => 'Fantasy Goddess',
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Dragon & Tiger","2":"龙虎","3":"Dragon & Tiger","4":"Dragon & Tiger","5":"Dragon & Tiger"}',
                'game_code' => 'EG-SLOT-A004',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-A004',
                'english_name' => 'Dragon & Tiger',
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Innocent Classmates","2":"同校生","3":"Innocent Classmates","4":"Innocent Classmates","5":"Innocent Classmates"}',
                'game_code' => 'EG-SLOT-A009',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-A009',
                'english_name' => 'Innocent Classmates',
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"The Guard","2":"锦衣卫","3":"The Guard","4":"The Guard","5":"The Guard"}',
                'game_code' => 'EG-SLOT-A003',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-A003',
                'english_name' => 'The Guard',
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Funny Farm","2":"欢乐农场","3":"Funny Farm","4":"Funny Farm","5":"Funny Farm"}',
                'game_code' => 'EG-SLOT-A010',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-A010',
                'english_name' => 'Funny Farm',
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Red Dragon","2":"赤龙","3":"Red Dragon","4":"Red Dragon","5":"Red Dragon"}',
                'game_code' => 'EG-SLOT-A011',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-A011',
                'english_name' => 'Red Dragon',
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Wong Fei Hung","2":"黄飞鸿","3":"Wong Fei Hung","4":"Wong Fei Hung","5":"Wong Fei Hung"}',
                'game_code' => 'EG-SLOT-S003',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-S003',
                'english_name' => 'Wong Fei Hung',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"North South Lions","2":"南北狮王","3":"North South Lions","4":"North South Lions","5":"North South Lions"}',
                'game_code' => 'EG-SLOT-A017',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-A017',
                'english_name' => 'North South Lions',
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
               'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Bikini Chaser","2":"比基尼狂热","3":"Bikini Chaser","4":"Bikini Chaser","5":"Bikini Chaser"}',
                'game_code' => 'EG-SLOT-A013',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-A013',
                'english_name' => 'Bikini Chaser',
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Diamond Crush","2":"幸运金钻","3":"Diamond Crush","4":"Diamond Crush","5":"Diamond Crush"}',
                'game_code' => 'EG-SLOT-A014',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-A014',
                'english_name' => 'Diamond Crush',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Tropical Treasure","2":"热带宝藏","3":"Tropical Treasure","4":"Tropical Treasure","5":"Tropical Treasure"}',
                'game_code' => 'EG-SLOT-A016',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-A016',
                'english_name' => 'Tropical Treasure',
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
               'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Fruit Poppers","2":"脆爆水果","3":"Fruit Poppers","4":"Fruit Poppers","5":"Fruit Poppers"}',
                'game_code' => 'EG-SLOT-A015',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-A015',
                'english_name' => 'Fruit Poppers',
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Ji Gong","2":"济公","3":"Ji Gong","4":"Ji Gong","5":"Ji Gong"}',
                'game_code' => 'EG-SLOT-A006',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-A006',
                'english_name' => 'Ji Gong',
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Golden Chicken","2":"运财金鸡","3":"Golden Chicken","4":"Golden Chicken","5":"Golden Chicken"}',
                'game_code' => 'EG-SLOT-A020',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-A020',
                'english_name' => 'Golden Chicken',
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_false,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Beckoning Girls","2":"幸运喵星人","3":"Beckoning Girls","4":"Beckoning Girls","5":"Beckoning Girls"}',
                'game_code' => 'EG-SLOT-S004',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-S004',
                'english_name' => 'Beckoning Girls',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Angels & Demons","2":"魔鬼天使","3":"Angels & Demons","4":"Angels & Demons","5":"Angels & Demons"}',
                'game_code' => 'EG-SLOT-S005',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-S005',
                'english_name' => 'Angels & Demons',
                'flash_enabled' => $db_true,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Cheung Po Tsai","2":"张保仔","3":"Cheung Po Tsai","4":"Cheung Po Tsai","5":"Cheung Po Tsai"}',
                'game_code' => 'EG-SLOT-A018',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-A018',
                'english_name' => 'Cheung Po Tsai',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Volley Beauties","2":"美女沙排","3":"Volley Beauties","4":"Volley Beauties","5":"Volley Beauties"}',
                'game_code' => 'EG-SLOT-S006',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-S006',
                'english_name' => 'Volley Beauties',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Zombie Hunter","2":"丧尸猎人","3":"Zombie Hunter","4":"Zombie Hunter","5":"Zombie Hunter"}',
                'game_code' => 'EG-SLOT-S007',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-SLOT-S007',
                'english_name' => 'Zombie Hunter',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],

            #====================================End Game list=========================================

            //================================= Baccarat ==============
            [
                'game_name' => '_json:{"1":"Baccarat","2":"Baccarat","3":"Baccarat","4":"Baccarat","5":"Baccarat"}',
                'game_code' => 'bac',
                'game_platform_id' => $api_id,
                'external_game_id' => 'bac',
                'english_name' => 'Baccarat',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],

            #====================================End Game list=========================================
            //================================= Dragon Tiger ==============
            [
                'game_name' => '_json:{"1":"Dragon Tiger","2":"Dragon Tiger","3":"Dragon Tiger","4":"Dragon Tiger","5":"Dragon Tiger"}',
                'game_code' => 'dtx',
                'game_platform_id' => $api_id,
                'external_game_id' => 'dtx',
                'english_name' => 'Dragon Tiger',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],

            #====================================End Game list=========================================

            //================================= Sicbo ==============
            [
                'game_name' => '_json:{"1":"Sicbo","2":"Sicbo","3":"Sicbo","4":"Sicbo","5":"Sicbo"}',
                'game_code' => 'sicbo',
                'game_platform_id' => $api_id,
                'external_game_id' => 'sicbo',
                'english_name' => 'Sicbo',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],

            #====================================End Game list=========================================

            //================================= Fan Tan ==============
            [
                'game_name' => '_json:{"1":"Fan Tan","2":"Fan Tan","3":"Fan Tan","4":"Fan Tan","5":"Fan Tan"}',
                'game_code' => 'ftan',
                'game_platform_id' => $api_id,
                'external_game_id' => 'ftan',
                'english_name' => 'Fan Tan',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],

            #====================================End Game list=========================================

            //================================= Roulette ==============
            [
                'game_name' => '_json:{"1":"Roulette","2":"Roulette","3":"Roulette","4":"Roulette","5":"Roulette"}',
                'game_code' => 'rot',
                'game_platform_id' => $api_id,
                'external_game_id' => 'rot',
                'english_name' => 'Roulette',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],

            #====================================End Game list=========================================
            #
            //================================= Lotto 48/Lotto 48 Novelty Bet ==============
            [
                'game_name' => '_json:{"1":"Lotto 48/Lotto 48 Novelty Bet","2":"Lotto 48/Lotto 48 Novelty Bet","3":"Lotto 48/Lotto 48 Novelty Bet","4":"Lotto 48/Lotto 48 Novelty Bet","5":"Lotto 48/Lotto 48 Novelty Bet"}',
                'game_code' => 'lottery',
                'game_platform_id' => $api_id,
                'external_game_id' => 'lottery',
                'english_name' => 'Lotto 48/Lotto 48 Novelty Bet',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],

            #====================================End Game list=========================================

            //================================= Mini game ==============
            [
                'game_name' => '_json:{"1":"Mini game Fafafa","2":"Mini game Fafafa","3":"Mini game Fafafa","4":"Mini game Fafafa","5":"Mini game Fafafa"}',
                'game_code' => 'EG-MINI-B001',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-MINI-B001',
                'english_name' => 'Mini game Fafafa',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_mini_games],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],
            [
                'game_name' => '_json:{"1":"Mini game Lucky Dice","2":"Mini game Lucky Dice","3":"Mini game Lucky Dice","4":"Mini game Lucky Dice","5":"Mini game Lucky Dice"}',
                'game_code' => 'EG-MINI-B002',
                'game_platform_id' => $api_id,
                'external_game_id' => 'EG-MINI-B002',
                'english_name' => 'Mini game Lucky Dice',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_mini_games],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
            ],

            #====================================End Game list=========================================
            #
            //================================= Blackjack ==============
            [
                'game_name' => '_json:{"1":"Blackjack","2":"Blackjack","3":"Blackjack","4":"Blackjack","5":"Blackjack"}',
                'game_code' => 'blackjack',
                'game_platform_id' => $api_id,
                'external_game_id' => 'blackjack',
                'english_name' => 'Blackjack',
                'flash_enabled' => $db_false,
                'html_five_enabled' => $db_true,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_live_dealer],
                'flag_show_in_site' => $db_true,
                'status' =>  $db_true,
                'mobile_enabled' => $db_true,
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

            #================================================== New Game 6/15/2017 ========================================
            [
                'game_name' => '_json:{"1":"Red Chamber","2":"Red Chamber","3":"Red Chamber","4":"Red Chamber"}',
                'game_code' => 'EG-SLOT-A008',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                // 'game_type' => Table Game
                'external_game_id' => 'EG-SLOT-A008',
                'english_name' => 'Red Chamber',
                'game_platform_id' => $api_id,
                'status'=> $db_true,
                'flag_show_in_site'=>$db_true,
                'updated_at'=>$now,
            ],
            [
                'game_name' => '_json:{"1":"Child of Wealth","2":"运财童子","3":"Child of Wealth","4":"Child of Wealth"}',
                'game_code' => 'EG-SLOT-A026',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                // 'game_type' => Table Game
                'external_game_id' => 'EG-SLOT-A026',
                'english_name' => 'Child of Wealth',
                'game_platform_id' => $api_id,
                'status'=> $db_true,
                'flag_show_in_site'=>$db_true,
                'updated_at'=>$now,
            ],
            [
                'game_name' => '_json:{"1":"Prosperity Tree","2":"摇钱树","3":"Prosperity Tree","4":"Prosperity Tree"}',
                'game_code' => 'EG-SLOT-A023',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                // 'game_type' => Table Game
                'external_game_id' => 'EG-SLOT-A023',
                'english_name' => 'Prosperity Tree',
                'game_platform_id' => $api_id,
                'status'=> $db_true,
                'flag_show_in_site'=>$db_true,
                'updated_at'=>$now,
            ],
            [
                'game_name' => '_json:{"1":"Lucky Fa","2":"幸运发","3":"Lucky Fa","4":"Lucky Fa"}',
                'game_code' => 'EG-SLOT-A024',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                // 'game_type' => Table Game
                'external_game_id' => 'EG-SLOT-A024',
                'english_name' => 'Lucky Fa',
                'game_platform_id' => $api_id,
                'status'=> $db_true,
                'flag_show_in_site'=>$db_true,
                'updated_at'=>$now,
            ],
            [
                'game_name' => '_json:{"1":"Saint of Mahjong","2":"幸运发","3":"Saint of Mahjong","4":"Saint of Mahjong"}',
                'game_code' => 'EG-SLOT-A022',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                // 'game_type' => Table Game
                'external_game_id' => 'EG-SLOT-A022',
                'english_name' => 'Saint of Mahjong',
                'game_platform_id' => $api_id,
                'status'=> $db_true,
                'flag_show_in_site'=>$db_true,
                'updated_at'=>$now,
            ],
            [
                'game_name' => '_json:{"1":"Fortune Cat","2":"招财猫","3":"Fortune Cat","4":"Fortune Cat"}',
                'game_code' => 'EG-SLOT-A021',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                // 'game_type' => Table Game
                'external_game_id' => 'EG-SLOT-A021',
                'english_name' => 'Fortune Cat',
                'game_platform_id' => $api_id,
                'status'=> $db_true,
                'flag_show_in_site'=>$db_true,
                'updated_at'=>$now,
            ],
            [
                'game_name' => '_json:{"1":"Super 7","2":"富贵7","3":"Super 7","4":"Super 7"}',
                'game_code' => 'EG-SLOT-C001',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                // 'game_type' => Table Game
                'external_game_id' => 'EG-SLOT-C001',
                'english_name' => 'Super 7',
                'game_platform_id' => $api_id,
                'status'=> $db_true,
                'flag_show_in_site'=>$db_true,
                'updated_at'=>$now,
            ],
            [
                'game_name' => '_json:{"1":"Fortune Lion","2":"鸿运金狮","3":"Fortune Lion","4":"Fortune Lion"}',
                'game_code' => 'EG-SLOT-A025',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                // 'game_type' => Table Game
                'external_game_id' => 'EG-SLOT-A025',
                'english_name' => 'Fortune Lion',
                'game_platform_id' => $api_id,
                'status'=> $db_true,
                'flag_show_in_site'=>$db_true,
                'updated_at'=>$now,
            ],
            [
                'game_name' => '_json:{"1":"Dragon 8","2":"龍8","3":"Dragon 8","4":"Dragon 8"}',
                'game_code' => 'EG-SLOT-C002',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'external_game_id' => 'EG-SLOT-C002',
                'english_name' => 'Dragon 8',
                'game_platform_id' => $api_id,
                'status'=> $db_true,
                'flag_show_in_site'=>$db_true,
                'updated_at'=>$now,
            ],
            [
                'game_name' => '_json:{"1":"World Cup Fever","2":"世界盃狂熱","3":"World Cup Fever","4":"World Cup Fever"}',
                'game_code' => 'EG-SLOT-A031',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'external_game_id' => 'EG-SLOT-A031',
                'english_name' => 'World Cup Fever',
                'game_platform_id' => $api_id,
                'status'=> $db_true,
                'flag_show_in_site'=>$db_true,
                'updated_at'=>$now,
            ],
            [
                'game_name' => '_json:{"1":"Double Happiness","2":"雙囍","3":"Double Happiness","4":"Double Happiness"}',
                'game_code' => 'EG-SLOT-A028',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'external_game_id' => 'EG-SLOT-A028',
                'english_name' => 'Double Happiness',
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
