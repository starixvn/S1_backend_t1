<?php
trait game_description_hrcc {

	public function sync_game_description_hrcc(){

		$cnt=0;

		$game_descriptions = array(
			array( 
				'game_platform_id' => HRCC_API, 
				'game_code' => 'unknown', 
				'game_name' => '_json:{"1":"Unknown HRCC Game","2":"Unknown HRCC Game"}', 
				'english_name' => 'Unknown HRCC Game',
			),
		);

		$data = array();

		foreach ($game_descriptions as $game_list) {

			$game_code_exist = $this->db->select('COUNT(id) as count')
							 	->where('game_code', $game_list['game_code'])
							 	->where('game_platform_id', HRCC_API)
							 	->get('game_description')
					 		 	->row();

			if( $game_code_exist->count <= 0 ) continue;

			$this->db->where('game_code', $game_list['game_code']);
			$this->db->where('game_platform_id', HRCC_API);
			$this->db->update('game_description', $game_list);
			$cnt++;

		}

		return $cnt;
	}

}
