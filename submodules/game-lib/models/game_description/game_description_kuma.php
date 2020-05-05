<?php
trait game_description_kuma {

    public function sync_game_description_ebet_kuma(){
        $api_id = EBET_KUMA_API;
        $this->kuma_game_list($api_id);
    }

	public function sync_game_description_kuma(){
        $api_id = KUMA_API;
        $this->kuma_game_list($api_id);
    }

    public function kuma_game_list($api_id){
		$api_id = KUMA_API;
		$db_true = '1';
		$db_false=0;
		$now=$this->utils->getNowForMysql();

		$cnt=0;
		$success=true;

        $game_type_code_slots       = SYNC::TAG_CODE_SLOT;
        $game_type_code_unknown     = SYNC::TAG_CODE_UNKNOWN_GAME;

		//sync game type first
		//game_type_code from bbin document
		//use game_type_code as key
		$game_types = [
			$game_type_code_slots => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"KUMA SLOTS","2":"KUMA 老虎机"}',
				'game_type_lang' => '_json:{"1":"KUMA SLOTS","2":"KUMA 老虎机"}',
				"game_type_code" => $game_type_code_slots,
                'game_tag_code' => SYNC::TAG_CODE_SLOT,
			],
			$game_type_code_unknown => [
				'game_platform_id' => $api_id,
				'game_type' => '_json:{"1":"KUMA UNKNOWN GAME","2":"KUMA 老虎机"}',
				'game_type_lang' => '_json:{"1":"KUMA UNKNOWN GAME","2":"KUMA 不明游戏"}',
				"game_type_code" => $game_type_code_unknown,
                'game_tag_code' => SYNC::TAG_CODE_UNKNOWN_GAME,
			],

		];


        $this->load->model(['game_type_model']);
        $gameTypeCodeMaps=$this->game_type_model->syncGameTypes($game_types);
        $this->utils->debug_log('gameTypeCodeMaps', $gameTypeCodeMaps);

		$game_descriptions = array(
                       [
                'game_platform_id' => $api_id,
                'game_code' => '1021',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'game_name' => '_json:{"1":"G-Cup Club","2":"G奶俱乐部"}',
                'english_name' => "G-Cup Club",
                'external_game_id' => '1021',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'updated_at' => $now ,
            ],
                   [
                'game_platform_id' => $api_id,
                'game_code' => '1022',
                'game_type_id' =>  $gameTypeCodeMaps[$game_type_code_slots],
                'game_name' => '_json:{"1":"Queen of PAPAPA","2":"女王啪啪啪"}',
                'english_name' => "Queen of PAPAPA",
                'external_game_id' => '1022',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'updated_at' => $now ,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '1023',
                'game_type_id' =>  $gameTypeCodeMaps[$game_type_code_slots],
                'game_name' => '_json:{"1":"Matsuoka China’s Classroom","2":"千菜的调教教室"}',
                'english_name' => "Matsuoka China’s Classroom",
                'external_game_id' => '1023',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'updated_at' => $now ,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '1024',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'game_name' => '_json:{"1":"Swapping apartment","2":"换妻公寓"}',
                'english_name' => "Swapping apartment",
                'external_game_id' => '1024',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'updated_at' => $now ,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '1025',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'game_name' => '_json:{"1":"The passion of the room","2":"和室里的激情"}',
                'english_name' => "The passion of the room",
                'external_game_id' => '1025',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'updated_at' => $now ,
            ],
            [
                'game_platform_id' => $api_id,
                'game_code' => '1026',
                'game_type_id' => $gameTypeCodeMaps[$game_type_code_slots],
                'game_name' => '_json:{"1":"High tide dessert house","2":"高潮甜点屋"}',
                'english_name' => "High tide dessert house",
                'external_game_id' => '1025',
                'status' => $db_true,
                'flag_show_in_site' => $db_true,
                'updated_at' => $now ,
            ]
        );

		foreach ($game_descriptions as $game_list) {

			$game_code_exist = $this->db->select('COUNT(id) as count')
										->where('game_code', $game_list['game_code'])
										->where('game_platform_id', KUMA_API)
										->get('game_description')
										->row();

			if( $game_code_exist->count <= 0 ){
				$this->db->insert('game_description', $game_list);
			} else {
				$this->db->where('game_code', $game_list['game_code']);
				$this->db->where('game_platform_id', KUMA_API);
				$this->db->update('game_description', $game_list);
			}
			$cnt++;

		}

		return $cnt;
	}

	public function delete_inserted_game(){
		$game_codes = array('1021','1022','1023','1024','1025','1026');

        $this->db->trans_start();
        foreach ($game_codes as $game_code) {
            $this->db->where('game_code', $game_code);
            $this->db->delete('game_description');
        }
        $this->db->trans_complete();
	}

}
