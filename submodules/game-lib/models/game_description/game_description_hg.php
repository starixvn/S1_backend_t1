<?php
trait game_description_hg {

    public function sync_game_description_hg(){

        $db_true = 1;
        $db_false = 0;
        $api_id = HG_API;
        $now = $this->utils->getNowForMysql();

        $game_type_code_live_dealer             = SYNC::TAG_CODE_LIVE_DEALER;
        $game_type_code_table_and_cards_game    = SYNC::TAG_CODE_TABLE_AND_CARDS;
        $game_type_code_slots                   = SYNC::TAG_CODE_SLOT;
        $game_type_code_unknown                 = SYNC::TAG_CODE_UNKNOWN_GAME;

        $game_types = [
            $game_type_code_live_dealer => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Live Game","2":"Live Game","3":"Live Game","4":"Live Game","5":"Live Game"}',
                'game_type_lang' => '_json:{"1":"Live Game","2":"Live Game","3":"Live Game","4":"Live Game","5":"Live Game"}',
                'game_type_code' => $game_type_code_live_dealer,
                'game_tag_code' => $game_type_code_live_dealer,
            ],
            $game_type_code_table_and_cards_game => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Table and Card Game","2":"Table and Card Game","3":"Table and Card Game","4":"Table and Card Game","5":"Table and Card Game"}',
                'game_type_lang' => '_json:{"1":"Table and Card Game","2":"Table and Card Game","3":"Table and Card Game","4":"Table and Card Game","5":"Table and Card Game"}',
                'game_type_code' => $game_type_code_table_and_cards_game,
                'game_tag_code' => $game_type_code_table_and_cards_game,
            ],
            $game_type_code_slots => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Slots Game","2":"Slots Game","3":"Slots Game","4":"Slots Game","5":"Slots Game"}',
                'game_type_lang' => '_json:{"1":"Slots Game","2":"Slots Game","3":"Slots Game","4":"Slots Game","5":"Slots Game"}',
                'game_type_code' => $game_type_code_slots,
                'game_tag_code' => $game_type_code_slots,
            ],
            $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_type_lang' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_type_code' => $game_type_code_unknown,
                'game_tag_code' => $game_type_code_unknown,
            ],
        ];

        $this->load->model(['game_type_model']);
        $gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);
        $this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

        $game_descriptions = array(
            [
                "game_name" => '_json:{"1" : "Rng Slots", "2" : "火焰777", "3" : "Rng Slots", "4" : "Rng Slots"}',
                "game_code" => '731c0ic7s8el835d',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => '731c0ic7s8el835d',
                "english_name" => 'Rng Slots',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" => '_json:{"1" : "Rng Slots Chance", "2" : "火焰777", "3" : "Rng Slots Chance", "4" : "Rng Slots Chance"}',
                "game_code" => '7k78h0c9eq1wklby',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => '7k78h0c9eq1wklby',
                "english_name" => 'Rng Slots Chance',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" => '_json:{"1" : "Rng Baccarat", "2" : "百家樂", "3" : "Rng Baccarat", "4" : "Rng Baccarat"}',
                "game_code" => '24r2hwo7m6zaknkp',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => '24r2hwo7m6zaknkp',
                "english_name" => 'Rng Baccarat',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng Blackjack", "2" : "二十一點", "3" : "Rng Blackjack", "4" : "Rng Blackjack"}',
                "game_code" => '0suxq7m815j9vaer',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => '0suxq7m815j9vaer',
                "english_name" => 'Rng Blackjack',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng Carebbean Poker", "2" : "加勒比海撲克", "3" : "Rng Carebbean Poker", "4" : "Rng Carebbean Poker"}',
                "game_code" => '6wjz46hvko913lm4',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => '6wjz46hvko913lm4',
                "english_name" => 'Rng Carebbean Poker',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng CashBag", "2" : "現金滿袋", "3" : "Rng CashBag", "4" : "Rng CashBag"}',
                "game_code" => 'hnfd76shwsozrnwj',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'hnfd76shwsozrnwj',
                "english_name" => 'Rng CashBag',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" => '_json:{"1" : "Rng CashParty", "2" : "閃亮派對", "3" : "Rng CashParty", "4" : "Rng CashParty"}',
                "game_code" => 't4fd7tsh5so6rnw8',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 't4fd7tsh5so6rnw8',
                "english_name" => 'Rng CashParty',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" => '_json:{"1" : "Rng Casino War", "2" : "娛樂場之戰", "3" : "Rng Casino War", "4" : "Rng Casino War"}',
                "game_code" => 'ng7cukrwub7xzstj',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'ng7cukrwub7xzstj',
                "english_name" => 'Rng Casino War',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng CherryMania", "2" : "櫻桃工坊", "3" : "Rng CherryMania", "4" : "Rng CherryMania"}',
                "game_code" => 'dphf72iwxhozrnwf',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'dphf72iwxhozrnwf',
                "english_name" => 'Rng CherryMania',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" => '_json:{"1" : "Rng ClassicJackSorBetter", "2" : "經典傑克高手", "3" : "Rng ClassicJackSorBetter", "4" : "Rng ClassicJackSorBetter"}',
                "game_code" => 'k5fd55shwsozrnwo',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'k5fd55shwsozrnwo',
                "english_name" => 'Rng ClassicJackSorBetter',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng ClassicJokerPoker", "2" : "經典小丑撲克", "3" : "Rng ClassicJokerPoker", "4" : "Rng ClassicJokerPoker"}',
                "game_code" => 's4fd34shwsozrnwn',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 's4fd34shwsozrnwn',
                "english_name" => 'Rng ClassicJokerPoker',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng ClassicVideoPoker", "2" : "經典視頻撲克", "3" : "Rng ClassicVideoPoker", "4" : "Rng ClassicVideoPoker"}',
                "game_code" => 'h2fd22shwsozrnww',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'h2fd22shwsozrnww',
                "english_name" => 'Rng ClassicVideoPoker',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng JacksorBetter", "2" : "傑克高手", "3" : "Rng JacksorBetter", "4" : "Rng JacksorBetter"}',
                "game_code" => 'jypd73swxhozrnwx',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'jypd73swxhozrnwx',
                "english_name" => 'Rng JacksorBetter',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng JacksorBetterChance", "2" : "傑克高手", "3" : "Rng JacksorBetterChance", "4" : "Rng JacksorBetterChance"}',
                "game_code" => 'ktpd74swxhozrnwx',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'ktpd74swxhozrnwx',
                "english_name" => 'Rng JacksorBetterChance',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng Jokerpoker", "2" : "小丑撲克", "3" : "Rng Jokerpoker", "4" : "Rng Jokerpoker"}',
                "game_code" => 'bl3bna7a1z18tyir',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'bl3bna7a1z18tyir',
                "english_name" => 'Rng Jokerpoker',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng JokerpokerChance", "2" : "小丑撲克", "3" : "Rng JokerpokerChance", "4" : "Rng JokerpokerChance"}',
                "game_code" => 'pqjhxbnro0prbcv1',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'pqjhxbnro0prbcv1',
                "english_name" => 'Rng JokerpokerChance',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng Videopoker", "2" : "視頻撲克", "3" : "Rng Videopoker", "4" : "Rng Videopoker"}',
                "game_code" => 'wks6njmjoop6pvnu',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'wks6njmjoop6pvnu',
                "english_name" => 'Rng Videopoker',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng VideopokerChance", "2" : "視頻撲克", "3" : "Rng VideopokerChance", "4" : "Rng VideopokerChance"}',
                "game_code" => 'uwpd76swxhozrnwx',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'uwpd76swxhozrnwx',
                "english_name" => 'Rng VideopokerChance',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng GemFortune", "2" : "幸運寶石", "3" : "Rng GemFortune", "4" : "Rng GemFortune"}',
                "game_code" => 'p2jd73uwxhozrnwh',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'p2jd73uwxhozrnwh',
                "english_name" => 'Rng GemFortune',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" => '_json:{"1" : "Rng GoldenGopher", "2" : "黃金地鼠", "3" : "Rng GoldenGopher", "4" : "Rng GoldenGopher"}',
                "game_code" => 'i6ed15twxhozrnwt',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'i6ed15twxhozrnwt',
                "english_name" => 'Rng GoldenGopher',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" => '_json:{"1" : "Rng GoldenSevens", "2" : "金裝三重7", "3" : "Rng GoldenSevens", "4" : "Rng GoldenSevens"}',
                "game_code" => 'tkpd74ywxhozrnwr',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'tkpd74ywxhozrnwr',
                "english_name" => 'Rng GoldenSevens',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" => '_json:{"1" : "Rng DoubleUpPoker", "2" : "日式翻倍撲克", "3" : "Rng DoubleUpPoker", "4" : "Rng DoubleUpPoker"}',
                "game_code" => 'j5ed26rwxhozrnws',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'j5ed26rwxhozrnws',
                "english_name" => 'Rng DoubleUpPoker',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng DoubleUpPokerChance", "2" : "日式翻倍撲克", "3" : "Rng DoubleUpPokerChance", "4" : "Rng DoubleUpPokerChance"}',
                "game_code" => 'k4fd56shwsozrnwj',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'k4fd56shwsozrnwj',
                "english_name" => 'Rng DoubleUpPokerChance',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng Letitride Poker", "2" : "任逍遙撲克", "3" : "Rng Letitride Poker", "4" : "Rng Letitride Poker"}',
                "game_code" => 'ivybhtf7ib2p5uy6',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'ivybhtf7ib2p5uy6',
                "english_name" => 'Rng Letitride Poker',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng Lucky888", "2" : "幸運888", "3" : "Rng Lucky888", "4" : "Rng Lucky888"}',
                "game_code" => 'xwqd89swxzohrnwa',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'xwqd89swxzohrnwa',
                "english_name" => 'Rng Lucky888',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" => '_json:{"1" : "Rng LuckyHarvest", "2" : "幸運大豐收", "3" : "Rng LuckyHarvest", "4" : "Rng LuckyHarvest"}',
                "game_code" => 'oked75twxhozrnwr',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'oked75twxhozrnwr',
                "english_name" => 'Rng LuckyHarvest',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" => '_json:{"1" : "Rng LuckyHarvestChance", "2" : "幸運大豐收", "3" : "Rng LuckyHarvestChance", "4" : "Rng LuckyHarvestChance"}',
                "game_code" => 'rfed76rwxhozrnwt',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'rfed76rwxhozrnwt',
                "english_name" => 'Rng LuckyHarvestChance',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" => '_json:{"1" : "Rng MangoMania", "2" : "芒果工坊", "3" : "Rng MangoMania", "4" : "Rng MangoMania"}',
                "game_code" => 'g3fd6tsh5so6rnw8',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'g3fd6tsh5so6rnw8',
                "english_name" => 'Rng MangoMania',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" => '_json:{"1" : "Rng Oasis Poker", "2" : "綠洲撲克", "3" : "Rng Oasis Poker", "4" : "Rng Oasis Poker"}',
                "game_code" => 'kt4kqo9fo49qxk6l',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'kt4kqo9fo49qxk6l',
                "english_name" => 'Rng Oasis Poker',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng Fruit Slots", "2" : "果園獎金", "3" : "Rng Fruit Slots", "4" : "Rng Fruit Slots"}',
                "game_code" => 'dpcaki3dl49c8h3y',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'dpcaki3dl49c8h3y',
                "english_name" => 'Rng Fruit Slots',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" => '_json:{"1" : "Rng Paigowpoker", "2" : "牌九撲克", "3" : "Rng Paigowpoker", "4" : "Rng Paigowpoker"}',
                "game_code" => 'vici41ubxt8cfuny',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'vici41ubxt8cfuny',
                "english_name" => 'Rng Paigowpoker',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng PinkDiamond", "2" : "粉紅鑽石", "3" : "Rng PinkDiamond", "4" : "Rng PinkDiamond"}',
                "game_code" => 'wefr78shxwozrnwe',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'wefr78shxwozrnwe',
                "english_name" => 'Rng PinkDiamond',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" => '_json:{"1" : "Rng PinkDiamondChance", "2" : "粉紅鑽石", "3" : "Rng PinkDiamondChance", "4" : "Rng PinkDiamondChance"}',
                "game_code" => 'owfd77xwshozrnwd',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'owfd77xwshozrnwd',
                "english_name" => 'Rng PinkDiamondChance',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" => '_json:{"1" : "Rng Reddog", "2" : "紅狗撲克", "3" : "Rng Reddog", "4" : "Rng Reddog"}',
                "game_code" => 'kq120xdsctk9jrrt',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'kq120xdsctk9jrrt',
                "english_name" => 'Rng Reddog',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng RoadToRiches", "2" : "致富之路", "3" : "Rng RoadToRiches", "4" : "Rng RoadToRiches"}',
                "game_code" => 'y5fd8tsh5so6rnw8',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'y5fd8tsh5so6rnw8',
                "english_name" => 'Rng RoadToRiches',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" => '_json:{"1" : "Rng ThreeCardPoker", "2" : "三卡撲克", "3" : "Rng ThreeCardPoker", "4" : "Rng ThreeCardPoker"}',
                "game_code" => 'qwxd78swxhozrnwx',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'qwxd78swxhozrnwx',
                "english_name" => 'Rng ThreeCardPoker',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_table_and_cards_game]
            ],
            [
                "game_name" => '_json:{"1" : "Rng PearlPrincess", "2" : "珍珠公主", "3" : "Rng PearlPrincess", "4" : "Rng PearlPrincess"}',
                "game_code" => 'e7kd2tsh5so6rnw8',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'e7kd2tsh5so6rnw8',
                "english_name" => 'Rng PearlPrincess',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" => '_json:{"1" : "Rng PearlPrincessFree", "2" : "珍珠公主", "3" : "Rng PearlPrincessFree", "4" : "Rng PearlPrincessFree"}',
                "game_code" => 'jz4d1tsh5so6rnw8',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'jz4d1tsh5so6rnw8',
                "english_name" => 'Rng PearlPrincessFree',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" => '_json:{"1" : "Rng MountainOfInferno", "2" : "火焰山", "3" : "Rng MountainOfInferno", "4" : "Rng MountainOfInferno"}',
                "game_code" => 'm9kd2tsh5d06rtw7',
                "html_five_enabled" => $db_true,
                "game_platform_id" => $api_id,
                "external_game_id" => 'm9kd2tsh5d06rtw7',
                "english_name" => 'Rng MountainOfInferno',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],
            [
                "game_name" =>'_json:{"1" : "Rng MountainOfInfernoFree", "2" : "火焰山", "3" : "Rng MountainOfInfernoFree", "4" : "Rng MountainOfInfernoFree"}',
	            "game_code" => 'jz4d1tsh5wo6rkj',
                "game_platform_id" => $api_id,
                "external_game_id" => 'jz4d1tsh5wo6rkj5',
                "english_name" => 'Rng MountainOfInfernoFree',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_slots]
            ],


            // live dealer
            [
                "game_name" => '_json:{"1" : "Casino Baccarat 1", "2" : "百家樂1", "3" : "Casino Baccarat 1", "4" : "Casino Baccarat 1"}',
                "game_code" => 'l8i2hq4jo2hjj9ca',
                "game_platform_id" => $api_id,
                "external_game_id" => 'l8i2hq4jo2hjj9ca',
                "english_name" => 'Casino Baccarat 1',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_live_dealer]
            ],
            [
                "game_name" => '_json:{"1" : "Casino Baccarat 2", "2" : "百家樂2", "3" : "Casino Baccarat 2", "4" : "Casino Baccarat 2"}',
                "game_code" => 'bacdhq2j04hjj8ca',
                "game_platform_id" => $api_id,
                "external_game_id" => 'bacdhq2j04hjj8ca',
                "english_name" => 'Casino Baccarat 2',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_live_dealer]
            ],
            [
                "game_name" => '_json:{"1" : "Casino Baccarat 3", "2" : "百家樂3", "3" : "Casino Baccarat 3", "4" : "Casino Baccarat 3"}',
                "game_code" => 'cfnfubozrmdpyj3h',
                "game_platform_id" => $api_id,
                "external_game_id" => 'cfnfubozrmdpyj3h',
                "english_name" => 'Casino Baccarat 3',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_live_dealer]
            ],
            [
                "game_name" => '_json:{"1" : "Casino NCBaccarat", "2" : "免水百家樂", "3" : "Casino NCBaccarat", "4" : "Casino NCBaccarat"}',
                "game_code" => 'ncbaccj5jr2kplmj',
                "game_platform_id" => $api_id,
                "external_game_id" => 'ncbaccj5jr2kplmj',
                "english_name" => 'Casino NCBaccarat',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_live_dealer]
            ],
            [
                "game_name" => '_json:{"1" : "Casino Roulette", "2" : "輪盤1", "3" : "Casino Roulette", "4" : "Casino Roulette"}',
                "game_code" => 'x9i3jxq3kiyxx670',
                "game_platform_id" => $api_id,
                "external_game_id" => 'x9i3jxq3kiyxx670',
                "english_name" => 'Casino Roulette',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_live_dealer]
            ],
            [
                "game_name" => '_json:{"1" : "Casino Black Jack 1", "2" : "二十一點1", "3" : "Casino Black Jack 1", "4" : "Casino Black Jack 1"}',
                "game_code" => 'bj2m46lf2wguzbya',
                "game_platform_id" => $api_id,
                "external_game_id" => 'bj2m46lf2wguzbya',
                "english_name" => 'Casino Black Jack 1',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_live_dealer]
            ],
            [
                "game_name" => '_json:{"1" : "Casino Black Jack 2", "2" : "二十一點2", "3" : "Casino Black Jack 2", "4" : "Casino Black Jack 2"}',
                "game_code" => 'yfjv7uzd11rq4ecy',
                "game_platform_id" => $api_id,
                "external_game_id" => 'yfjv7uzd11rq4ecy',
                "english_name" => 'Casino Black Jack 2',
                "flash_enabled" => $db_false,
                "game_type_id" => $gameTypeCodeMaps[$game_type_code_live_dealer]
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
        );

        $this->load->model(['game_description_model']);

        $success=$this->game_description_model->syncGameDescription($game_descriptions);

        return $success;
    }

}