<?php
trait game_description_isb_freespin {

	private function updateOldGameTypes(){
		//game_type to game_type_code
		$gameTypes=[
			'_json:{"1":"Slots Game","2":"老虎机游戏"}'=>'Slots Game',
			'_json:{"1":"Mechanical Slot Game","2":"转轴老虎机游戏"}'=>'Mechanical Slot Game',
			'_json:{"1":"Branded Slot Game","2":"品牌老虎机游戏"}'=>'Branded Slot Game',
			'_json:{"1":"Table Game","2":"桌面游戏"}'=>'Table Game',
			'_json:{"1":"Video Poker Game","2":"视频扑克"}'=>'Video Poker Game',
			'_json:{"1":"Slots Html5 Game","2":"HTML5老虎机游戏"}'=>'Slots Html5 Game',
			'_json:{"1":"Mechanical Slots Html5 Game","2":"HTML5转轴老虎机游戏"}'=>'Mechanical Slots Html5 Game',
			'_json:{"1":"Branded Slots Html5 Game","2":"HTML5品牌老虎机游戏"}'=>'Branded Slots Html5 Game',
			'_json:{"1":"Table Html5 Game","2":"HTML5桌面游戏"}'=>'Table Html5 Game',
			'_json:{"1":"Video Poker Html5 Game","2":"HTML5视频扑克游戏"}'=>'Video Poker Html5 Game',
			'unknown'=>'Unknown',
		];
		
		$api_id=ISB_API;

		$this->load->model(['game_type_model']);

		foreach ($gameTypes as $game_type => $game_type_code) {

			$this->db->select('id')->from('game_type')->where('game_type', $game_type)
			    ->where('game_platform_id', $api_id)->where('game_type_code is null', null, false);

			$id = $this->game_type_model->runOneRowOneField('id');

			if( empty($id) ) continue;

			$this->db->update('game_type',['game_type_code'=>$game_type_code], ['id'=>$id]);
		}

	}

	public function sync_game_description_isb_freespin(){

		$success=true;

		// $cnt=0;

		$this->updateOldGameTypes();

		$api_id=ISB_API;
		$db_true=1;
		$db_false=0;
		$now=$this->utils->getNowForMysql();

		//===game types======================================
		//game type is platform type
		//AGIN, AG, DSP, AGHH, IPM,
		//BBIN, MG, SABAH, HG, PT,
		//OG, UGS, HUNTER, AGTEX, HB,
		//XTD, PNG, NYX, ENDO, BG,
		//XIN, YOPLAY ,TTG

		$game_type_code_slots_game='Slots Game';
		$game_type_code_mechanical_slot_game='Mechanical Slot Game';
		$game_type_code_branded_slot_game='Branded Slot Game';
		$game_type_code_table_game='Table Game';
		$game_type_code_vid_poker_game='Video Poker Game';
		$game_type_code_slots_mobile_game='Slots Html5 Game';
		$game_type_code_mechanical_slot_mobile_game='Mechanical Slots Html5 Game';
		$game_type_code_branded_slot_mobile_game='Branded Slots Html5 Game';
		$game_type_code_table_mobile_game='Table Html5 Game';
		$game_type_code_vid_poker_mobile_game='Video Poker Html5 Game';
		$game_type_code_unknown='unknown';

		//sync game type first
		//game_type_code from bbin document
		//use game_type_code as key

		$game_types = [
			$game_type_code_slots_game => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"Slots Game","2":"老虎机游戏"}',
			],
			$game_type_code_mechanical_slot_game => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"Mechanical Slot Game","2":"转轴老虎机游戏"}',
			],
			$game_type_code_branded_slot_game => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"Branded Slot Game","2":"品牌老虎机游戏"}',
			],
			$game_type_code_table_game => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"Table Game","2":"桌面游戏"}',
			],
			$game_type_code_vid_poker_game => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"Video Poker Game","2":"视频扑克"}',
			],
			$game_type_code_slots_mobile_game => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"Slots Html5 Game","2":"HTML5老虎机游戏"}',
			],
			$game_type_code_mechanical_slot_mobile_game => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"Mechanical Slots Html5 Game","2":"HTML5转轴老虎机游戏"}',
			],
			$game_type_code_branded_slot_mobile_game => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"Branded Slots Html5 Game","2":"HTML5品牌老虎机游戏"}',
			],
			$game_type_code_table_mobile_game => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"Table Html5 Game","2":"HTML5桌面游戏"}',
			],
			$game_type_code_vid_poker_mobile_game => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"Video Poker Html5 Game","2":"HTML5视频扑克游戏"}',
			],
			$game_type_code_unknown => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"Unknown","2":"不明类型"}',
			],

		];

		$this->load->model(['game_type_model']);
		$gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);

		// $this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);
	
	
		if(empty($gameTypeCodeMaps)){
			return false;
		}

		//===game types======================================

		$game_descriptions = array(
			[
				'game_platform_id' => $api_id,
				'game_code' => '881510',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_branded_slot_game],
				'game_name' => '_json:{"1":"Jackpot Rango","2":"兰戈的奖池"}',
				'attributes' => '{"lines":["1","5","10","15","20","25"],"line_bet":["1","2","3","4","5","6","7","8","9","10"],"coin":[{"currency":"CNY","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"RMB","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"USD","coin_values":["0.02","0.05","0.1","0.2","0.5","1"],"coin_value_default":""}]}',
				'enabled_freespin' => 1
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '881539',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_mechanical_slot_game],
				'game_name' => '_json:{"1":"Spin or Reels HD","2":"旋转卷轴"}',
				'attributes' => '{"lines":["20"],"line_bet":"2","coin":[{"currency":"CNY","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"RMB","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"USD","coin_values":["0.02","0.05","0.1","0.2","0.5","1"],"coin_value_default":""}]}',
				'enabled_freespin' => 1
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '881588',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots_game],
				'game_name' => '_json:{"1":"Wisps","2":"鬼火"}',
				'attributes' => '{"lines":["1"],"line_bet":["1","2","3","4","5"],"coin":[{"currency":"CNY","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"RMB","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"USD","coin_values":["0.02","0.05","0.1","0.2","0.5","1"],"coin_value_default":""}]}',
				'enabled_freespin' => 1
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '881589',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots_game],
				'game_name' => '_json:{"1":"Mega Boy","2":"超级男孩"}',
				'attributes' => '{"lines":["25"],"line_bet":["1"],"coin":[{"currency":"CNY","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"RMB","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"USD","coin_values":["0.02","0.05","0.1","0.2","0.5","1"],"coin_value_default":""}]}',
				'enabled_freespin' => 1
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '896529',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_branded_slot_mobile_game],
				'game_name' => '_json:{"1":"Heavy Metal : Warriors","2":"重量级金属战士"}',
				'attributes' => '{"lines":["1"],"line_bet":["1","2","3","4","5"],"coin":[{"currency":"CNY","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"RMB","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"USD","coin_values":["0.02","0.05","0.1","0.2","0.5","1"],"coin_value_default":""}]}',
				'enabled_freespin' => 1
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '896550',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots_mobile_game],
				'game_name' => '_json:{"1":"MegaBoy","2":"超级男孩"}',
				'attributes' => '{"lines":["25"],"line_bet":["1"],"coin":[{"currency":"CNY","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"RMB","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"USD","coin_values":["0.02","0.05","0.1","0.2","0.5","1"],"coin_value_default":""}]}',
				'enabled_freespin' => 1
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '881547',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_mechanical_slot_game],
				'game_name' => '_json:{"1":"Gold Hold","2":"拥有黄金"}',
				'attributes' => '{"lines":["5"],"line_bet":["1","2","3","4","5","6","7","8","9","10"],"coin":[{"currency":"CNY","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"RMB","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"USD","coin_values":["0.02","0.05","0.1","0.2","0.5","1"],"coin_value_default":""}]}',
				'enabled_freespin' => 1
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '881580',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_mechanical_slot_game],
				'game_name' => '_json:{"1":"Million Cents HD","2":"高清百万分"}',
				'attributes' => '{"lines":["20"],"line_bet":["1","2","3","4","5"],"coin":[{"currency":"CNY","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"RMB","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"USD","coin_values":["0.02","0.05","0.1","0.2","0.5","1"],"coin_value_default":""}]}',
				'enabled_freespin' => 1
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '881581',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots_game],
				'game_name' => '_json:{"1":"The Best Witch","2":"最佳女巫"}',
				'attributes' => '{"lines":["1","2","3","4","5","6","7","8","9","10","11","12","13","14","15"],"line_bet":["1","2","3","4","5","6","7","8","9","10"],"coin":[{"currency":"CNY","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"RMB","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"USD","coin_values":["0.02","0.05","0.1","0.2","0.5","1"],"coin_value_default":""}]}',
				'enabled_freespin' => 1
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '881585',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots_game],
				'game_name' => '_json:{"1":"Scrolls of Ra HD","2":"埃及旋转HD"}',
				'attributes' => '{"lines":["1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20"],"line_bet":["1","2","3","4","5"],"coin":[{"currency":"CNY","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"RMB","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"USD","coin_values":["0.02","0.05","0.1","0.2","0.5","1"],"coin_value_default":""}]}',
				'enabled_freespin' => 1
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '881590',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_branded_slot_game],
				'game_name' => '_json:{"1":"Hansel & Gretel: Witch Hunters","2":"奇幻森林历险记"}',
				'attributes' => '{"lines":["10"],"line_bet":["1","2","3","4","5","6","7","8","9","10"],"coin":[{"currency":"CNY","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"RMB","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"USD","coin_values":["0.02","0.05","0.1","0.2","0.5","1"],"coin_value_default":""}]}',
				'enabled_freespin' => 1
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '881593',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots_game],
				'game_name' => '_json:{"1":"Shaolin Spin","2":"少林旋转"}',
				'attributes' => '{"lines":["1"],"line_bet":["1","2","3","4","5"],"coin":[{"currency":"CNY","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"RMB","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"USD","coin_values":["0.02","0.05","0.1","0.2","0.5","1"],"coin_value_default":""}]}',
				'enabled_freespin' => 1
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '881597',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots_game],
				'game_name' => '_json:{"1":"Cloud Tales","2":"彩云故事"}',
				'attributes' => '{"lines":["9"],"line_bet":["1"],"coin":[{"currency":"CNY","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"RMB","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"USD","coin_values":["0.02","0.05","0.1","0.2","0.5","1"],"coin_value_default":""}]}',
				'enabled_freespin' => 1
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '906536',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots_mobile_game],
				'game_name' => '_json:{"1":"Scrolls of Ra HD","2":"埃及旋转HD"}',
				'attributes' => '{"lines":["1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20"],"line_bet":["1","2","3","4","5"],"coin":[{"currency":"CNY","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"RMB","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"USD","coin_values":["0.02","0.05","0.1","0.2","0.5","1"],"coin_value_default":""}]}',
				'enabled_freespin' => 1
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '896539',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots_mobile_game],
				'game_name' => '_json:{"1":"Shaolin Spin","2":"少林旋转"}',
				'attributes' => '{"lines":["1"],"line_bet":["1","2","3","4","5"],"coin":[{"currency":"CNY","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"RMB","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"USD","coin_values":["0.02","0.05","0.1","0.2","0.5","1"],"coin_value_default":""}]}',
				'enabled_freespin' => 1
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '906544',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots_mobile_game],
				'game_name' => '_json:{"1":"Astro Magic","2":"天文魔术"}',
				'attributes' => '{"lines":["1","2","3","4","5","6","7","8","9"],"line_bet":["1"],"coin":[{"currency":"CNY","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"RMB","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"USD","coin_values":["0.02","0.05","0.1","0.2","0.5","1"],"coin_value_default":""}]}',
				'enabled_freespin' => 1
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '896551',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots_mobile_game],
				'game_name' => '_json:{"1":"Cloud Tales","2":"彩云故事"}',
				'attributes' => '{"lines":["9"],"line_bet":["1"],"coin":[{"currency":"CNY","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"RMB","coin_values":["0.2","0.5","1","2","5","10"],"coin_value_default":""},{"currency":"USD","coin_values":["0.02","0.05","0.1","0.2","0.5","1"],"coin_value_default":""}]}}',
				'enabled_freespin' => 1
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '1607',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_unknown],
				'game_name' => '_json:{"1":"Robo Smash","2":"Robo Smash"}',
				'attributes' => '{"lines":["15"],"line_bet":["1"],"coin":[{"currency":"CNY","coin_values":["0.14","0.35","0.7","1.4","3.5","7"],"coin_value_default":""},{"currency":"RMB","coin_values":["0.02","0.05","0.1","0.2","0.5","0.7","1"],"coin_value_default":""},{"currency":"USD","coin_values":["0.02","0.05","0.1","0.2","0.5","1"],"coin_value_default":""}]}',
				'enabled_freespin' => 1
			],

		);

		$this->load->model(['game_description_model']);

		$success=$this->game_description_model->syncGameDescription($game_descriptions);

		return $success;
	}

}
