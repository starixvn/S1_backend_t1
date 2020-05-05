<?php
trait game_description_bbin_fix_new_game {

	public function sync_game_description_bbin_fix_new_game(){
		$cnt=0;

		$success=true;

		$api_id=BBIN_API;
		$db_true=1;
		$db_false=0;
		$now=$this->utils->getNowForMysql();

		$game_type_code_slots='5';
		$game_type_code_lottery='12';

		//sync game type first
		//game_type_code from bbin document
		//use game_type_code as key
		$game_types = [
			$game_type_code_slots => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"BB Casino","2":"BB 电子"}',
			],
			$game_type_code_lottery => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"BB Lottery","2":"BB 彩票"}',
			],
		];

		$this->load->model(['game_type_model']);
		$gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);

		$this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

		if(empty($gameTypeCodeMaps)){
			return false;
		}

		$game_descriptions = [
			//======New Games 3/17/2017=========================================================
			[
				'game_platform_id' => $api_id,
				'game_code' => '5069',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
				'game_name' => '_json:{"1":"Fruit Fight","2":"水果擂台"}',
				"english_name" => "Fruit Fight",
				'external_game_id' => '5069',
				'clientid' => null,
				'moduleid' => null,
				'status'=> $db_true,
				'flag_show_in_site'=>$db_false,
				'updated_at'=>$now,
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '5090',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
				'game_name' => '_json:{"1":"Fortune of the Golden Rooster","2":"金鸡报喜"}',
				"english_name" => "Fortune of the Golden Rooster",
				'external_game_id' => '5090',
				'clientid' => null,
				'moduleid' => null,
				'status'=> $db_true,
				'flag_show_in_site'=>$db_false,
				'updated_at'=>$now,
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '5907',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
				'game_name' => '_json:{"1":"Chiu Wei Tai Chiou","2":"趣味台球"}',
				"english_name" => "Chiu Wei Tai Chiou",
				'external_game_id' => '5907',
				'clientid' => null,
				'moduleid' => null,
				'status'=> $db_true,
				'flag_show_in_site'=>$db_false,
				'updated_at'=>$now,
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => 'LKPA',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
				'game_name' => '_json:{"1":"BB Lucky Panda","2":"BB 幸运熊猫"}',
				"english_name" => "BB Lucky Panda",
				'external_game_id' => 'LKPA',
				'clientid' => null,
				'moduleid' => null,
				'status'=> $db_true,
				'flag_show_in_site'=>$db_false,
				'updated_at'=>$now,
			],
			[
				'game_platform_id' => $api_id,
				'game_code' => '3016',
				'game_type_id' => $gameTypeCodeMaps[$game_type_code_lottery],
				'game_name' => '_json:{"1":"Fish Prawn Crab Dice ","2":"鱼虾蟹"}',
				"english_name" => "Fish Prawn Crab Dice ",
				'external_game_id' => '3016',
				'clientid' => null,
				'moduleid' => null,
				'status'=> $db_true,
				'flag_show_in_site'=>$db_false,
				'updated_at'=>$now,
			],
		];

		$this->load->model(['game_description_model']);

		// $success=$this->game_description_model->syncGameDescription($game_descriptions);

		$data = array();

		foreach ($game_descriptions as $game_list) {
			$game_code_exist = $this->db->select('COUNT(id) as count')
							 	->where('game_name', $game_list['game_code'])
							 	->where('game_platform_id', $api_id)
							 	->get('game_description')
					 		 	->row();

			if( $game_code_exist->count <= 0 ) continue;

			$this->db->where('game_name', $game_list['game_code']);
			$this->db->where('game_platform_id', $api_id);
			$this->db->update('game_description', $game_list);
			$cnt++;
		} //

		return $success;
	}
}