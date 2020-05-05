<?php
trait game_description_ipm_v2_sportsbook {

    public function sync_game_description_ipm_v2_sportsbook(){
        $api_id=IPM_V2_SPORTS_API;
        $this->sync_ipm_v2_game_list($api_id);
    }

    public function sync_game_description_ipm_v2_sportsbook_t1(){
        $api_id=T1IPM_V2_SPORTS_API;
        $this->sync_ipm_v2_game_list($api_id);
    }

	public function sync_ipm_v2_game_list($api_id){
        $cnt=0;
        $cntInsert = 0;
        $cntUpdate = 0;

        $db_true = 1;
        $db_false = 0;
        $now = $this->utils->getNowForMysql();

        $game_type_code_sports    = SYNC::TAG_CODE_SPORTS;
        $game_type_code_unknown   = SYNC::TAG_CODE_UNKNOWN_GAME;

        // sync game type first
        // use game_type_code as key
        $game_types = [
            $game_type_code_sports => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Sports","2":"街机游戏","3":"Sports","4":"Sports"}',
                "game_type_code" => $game_type_code_sports,
            ],
            $game_type_code_unknown => [
                'game_platform_id' => $api_id,
                'game_type' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown"}',
                'game_type_code'=>$game_type_code_unknown,
            ],
        ];

        $this->load->model(['game_type_model']);
        // //this code is checking the game type table with game
        // //also insertion of new game type based on game_type_code
        $gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);
        // $this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

		$game_descriptions = array(
			[
                'game_code' => 'Soccer',
                'external_game_id' => 'Soccer',
                'english_name' => 'Soccer',
                'game_name' => '_json:{"1":"Soccer","2":"足球","3":"Soccer","4":"Soccer","5":"Soccer"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Basketball',
                'external_game_id' => 'Basketball',
                'english_name' => 'Basketball',
                'game_name' => '_json:{"1":"Basketball","2":"篮球","3":"Basketball","4":"Basketball","5":"Basketball"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Tennis',
                'external_game_id' => 'Tennis',
                'english_name' => 'Tennis',
                'game_name' => '_json:{"1":"Tennis","2":"网球","3":"Tennis","4":"Tennis","5":"Tennis"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'MotorRacing',
                'external_game_id' => 'MotorRacing',
                'english_name' => 'Motor Racing',
                'game_name' => '_json:{"1":"Motor Racing","2":"赛摩多车","3":"Motor Racing","4":"Motor Racing","5":"Motor Racing"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Golf',
                'external_game_id' => 'Golf',
                'english_name' => 'Golf',
                'game_name' => '_json:{"1":"Golf","2":"高尔夫球","3":"Golf","4":"Golf","5":"Golf"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Soccer(HT)',
                'external_game_id' => 'Soccer(HT)',
                'english_name' => 'Soccer (HT)',
                'game_name' => '_json:{"1":"Soccer (HT)","2":"足球（HT）","3":"Soccer (HT)","4":"Soccer (HT)","5":"Soccer (HT)"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Football',
                'external_game_id' => 'Football',
                'english_name' => 'Football',
                'game_name' => '_json:{"1":"Football","2":"足球","3":"Football","4":"Football","5":"Football"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Hockey',
                'external_game_id' => 'Hockey',
                'english_name' => 'Hockey',
                'game_name' => '_json:{"1":"Hockey","2":"曲棍球","3":"Hockey","4":"Hockey","5":"Hockey"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Baseball',
                'external_game_id' => 'Baseball',
                'english_name' => 'Baseball',
                'game_name' => '_json:{"1":"Baseball","2":"棒球","3":"Baseball","4":"Baseball","5":"Baseball"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Volleyball',
                'external_game_id' => 'Volleyball',
                'english_name' => 'Volleyball',
                'game_name' => '_json:{"1":"Volleyball","2":"排球","3":"Volleyball","4":"Volleyball","5":"Volleyball"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Badminton',
                'external_game_id' => 'Badminton',
                'english_name' => 'Badminton',
                'game_name' => '_json:{"1":"Badminton","2":"羽球","3":"Badminton","4":"Badminton","5":"Badminton"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Snooker',
                'external_game_id' => 'Snooker',
                'english_name' => 'Snooker',
                'game_name' => '_json:{"1":"Snooker","2":"斯诺克","3":"Snooker","4":"Snooker","5":"Snooker"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Boxing',
                'external_game_id' => 'Boxing',
                'english_name' => 'Boxing',
                'game_name' => '_json:{"1":"Boxing","2":"拳击","3":"Boxing","4":"Boxing","5":"Boxing"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Rugby',
                'external_game_id' => 'Rugby',
                'english_name' => 'Rugby',
                'game_name' => '_json:{"1":"Rugby","2":"橄榄球","3":"Rugby","4":"Rugby","5":"Rugby"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Cricket',
                'external_game_id' => 'Cricket',
                'english_name' => 'Cricket',
                'game_name' => '_json:{"1":"Cricket","2":"板球","3":"Cricket","4":"Cricket","5":"Cricket"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Handball',
                'external_game_id' => 'Handball',
                'english_name' => 'Handball',
                'game_name' => '_json:{"1":"Handball","2":"手球","3":"Handball","4":"Handball","5":"Handball"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'FinancialBets',
                'external_game_id' => 'FinancialBets',
                'english_name' => 'FinancialBets',
                'game_name' => '_json:{"1":"FinancialBets","2":"金融投资","3":"FinancialBets","4":"FinancialBets","5":"FinancialBets"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Futsal',
                'external_game_id' => 'Futsal',
                'english_name' => 'Futsal',
                'game_name' => '_json:{"1":"Futsal","2":"五人制足球","3":"Futsal","4":"Futsal","5":"Futsal"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Asian9Ball',
                'external_game_id' => 'Asian9Ball',
                'english_name' => 'Asian9Ball',
                'game_name' => '_json:{"1":"Asian9Ball","2":"亚洲9球","3":"Asian9Ball","4":"Asian9Ball","5":"Asian9Ball"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Billiard',
                'external_game_id' => 'Billiard',
                'english_name' => 'Billiard',
                'game_name' => '_json:{"1":"Billiard","2":"台球","3":"Billiard","4":"Billiard","5":"Billiard"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Darts',
                'external_game_id' => 'Darts',
                'english_name' => 'Darts',
                'game_name' => '_json:{"1":"Darts","2":"飞镖","3":"Darts","4":"Darts","5":"Darts"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'WaterPolo',
                'external_game_id' => 'WaterPolo',
                'english_name' => 'WaterPolo',
                'game_name' => '_json:{"1":"WaterPolo","2":"水球","3":"WaterPolo","4":"WaterPolo","5":"WaterPolo"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Olympic',
                'external_game_id' => 'Olympic',
                'english_name' => 'Olympic',
                'game_name' => '_json:{"1":"Olympic","2":"奥林匹克","3":"Olympic","4":"Olympic","5":"Olympic"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Cycling',
                'external_game_id' => 'Cycling',
                'english_name' => 'Cycling',
                'game_name' => '_json:{"1":"Cycling","2":"自行车","3":"Cycling","4":"Cycling","5":"Cycling"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'BeachVolleyball',
                'external_game_id' => 'BeachVolleyball',
                'english_name' => 'Beach Volleyball',
                'game_name' => '_json:{"1":"Beach Volleyball","2":"沙滩排球","3":"Beach Volleyball","4":"Beach Volleyball","5":"Beach Volleyball"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'FieldHockey',
                'external_game_id' => 'FieldHockey',
                'english_name' => 'Field Hockey',
                'game_name' => '_json:{"1":"Field Hockey","2":"曲棍球","3":"Field Hockey","4":"Field Hockey","5":"Field Hockey"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'TableTennis',
                'external_game_id' => 'TableTennis',
                'english_name' => 'Table Tennis',
                'game_name' => '_json:{"1":"Table Tennis","2":"乒乓球","3":"Table Tennis","4":"Table Tennis","5":"Table Tennis"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Athletics',
                'external_game_id' => 'Athletics',
                'english_name' => 'Athletics',
                'game_name' => '_json:{"1":"Athletics","2":"田径","3":"Athletics","4":"Athletics","5":"Athletics"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Archery',
                'external_game_id' => 'Archery',
                'english_name' => 'Archery',
                'game_name' => '_json:{"1":"Archery","2":"射箭","3":"Archery","4":"Archery","5":"Archery"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'WeightLifting',
                'external_game_id' => 'WeightLifting',
                'english_name' => 'Weight Lifting',
                'game_name' => '_json:{"1":"Weight Lifting","2":"举重","3":"Weight Lifting","4":"Weight Lifting","5":"Weight Lifting"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Canoeing',
                'external_game_id' => 'Canoeing',
                'english_name' => 'Canoeing',
                'game_name' => '_json:{"1":"Canoeing","2":"划独木舟","3":"Canoeing","4":"Canoeing","5":"Canoeing"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Gymnastics',
                'external_game_id' => 'Gymnastics',
                'english_name' => 'Gymnastics',
                'game_name' => '_json:{"1":"Gymnastics","2":"体操","3":"Gymnastics","4":"Gymnastics","5":"Gymnastics"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Equestrian',
                'external_game_id' => 'Equestrian',
                'english_name' => 'Equestrian',
                'game_name' => '_json:{"1":"Equestrian","2":"马术","3":"Equestrian","4":"Equestrian","5":"Equestrian"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Triathlon',
                'external_game_id' => 'Triathlon',
                'english_name' => 'Triathlon',
                'game_name' => '_json:{"1":"Triathlon","2":"铁人三项","3":"Triathlon","4":"Triathlon","5":"Triathlon"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Swimming',
                'external_game_id' => 'Swimming',
                'english_name' => 'Swimming',
                'game_name' => '_json:{"1":"Swimming","2":"游泳","3":"Swimming","4":"Swimming","5":"Swimming"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Fencing',
                'external_game_id' => 'Fencing',
                'english_name' => 'Fencing',
                'game_name' => '_json:{"1":"Fencing","2":"击剑","3":"Fencing","4":"Fencing","5":"Fencing"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Judo',
                'external_game_id' => 'Judo',
                'english_name' => 'Judo',
                'game_name' => '_json:{"1":"Judo","2":"柔道","3":"Judo","4":"Judo","5":"Judo"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'M.Pentathlon',
                'external_game_id' => 'M.Pentathlon',
                'english_name' => 'M. Pentathlon',
                'game_name' => '_json:{"1":"M. Pentathlon","2":"五项全能","3":"M. Pentathlon","4":"M. Pentathlon","5":"M. Pentathlon"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Rowing',
                'external_game_id' => 'Rowing',
                'english_name' => 'Rowing',
                'game_name' => '_json:{"1":"Rowing","2":"划船","3":"Rowing","4":"Rowing","5":"Rowing"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Sailing',
                'external_game_id' => 'Sailing',
                'english_name' => 'Sailing',
                'game_name' => '_json:{"1":"Sailing","2":"帆船","3":"Sailing","4":"Sailing","5":"Sailing"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Shooting',
                'external_game_id' => 'Shooting',
                'english_name' => 'Shooting',
                'game_name' => '_json:{"1":"Shooting","2":"射击","3":"Shooting","4":"Shooting","5":"Shooting"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'Taekwondo',
                'external_game_id' => 'Taekwondo',
                'english_name' => 'Taekwondo',
                'game_name' => '_json:{"1":"Taekwondo","2":"跆拳道","3":"Taekwondo","4":"Taekwondo","5":"Taekwondo"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'VirtualSoccer',
                'external_game_id' => 'VirtualSoccer',
                'english_name' => 'Virtual Soccer',
                'game_name' => '_json:{"1":"Virtual Soccer","2":"虚拟足球r","3":"Virtual Soccer","4":"Virtual Soccer","5":"Virtual Soccer"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'VirtualBasketball',
                'external_game_id' => 'VirtualBasketball',
                'english_name' => 'Virtual Basketball',
                'game_name' => '_json:{"1":"Virtual Basketball","2":"虚拟篮球","3":"Virtual Basketball","4":"Virtual Basketball","5":"Virtual Basketball"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_sports],
            ],
            [
                'game_code' => 'unknown',
                'external_game_id' => 'unknown',
                'english_name' => 'Unknown',
                'game_name' => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"Unknown"}',
                'game_platform_id' => $api_id,
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_unknown],
            ],
		);

        $this->load->model(['game_description_model']);

        $success=$this->game_description_model->syncGameDescription($game_descriptions);
	}

}
