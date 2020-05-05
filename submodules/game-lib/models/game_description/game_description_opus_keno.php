<?php
trait game_description_opus_keno {

	public function get_game_type_ids_opus_keno(){
		$game_types = array('OPUS KENO');

		$game_type_id = [];
		foreach ($game_types as $gameType) {
			$this->db->select('*')
			 	 ->where('game_platform_id',OPUS_KENO_API)
			 	 ->like('game_type',$gameType);
			$query = $this->db->get('game_type');

			$game_type_id[$gameType] = $query->row()->id;
		}
		return $game_type_id;

	}

	/*
	This function is applicable for updating game type
	Inserting game type is not yet implemented
	 */
	public function adjust_old_game_type_opus_keno(){
		$game_type_code_keno_game = "opus_keno";

		$now = $this->utils->getNowForMysql();

		$db_true = 1;
		$game_type_id = $this->get_game_type_ids_opus_keno();

		$old_game_type = [
			[
	            'game_type' => '_json:{"1":"Opus Keno","2":"Opus Keno","3":"Opus Keno","4":"Opus Keno"}',
				'game_type_lang' => '_json:{"1":"Opus Keno","2":"Opus Keno","3":"Opus Keno","4":"Opus Keno"}',
	            'game_type_code'=>$game_type_code_keno_game,
	            'id'=>$game_type_id['OPUS KENO'],
	            
	        ],
		];

		foreach ($old_game_type as $game_type) {

			$game_type['updated_at'] = $now;

			$this->db->where("game_platform_id",OPUS_KENO_API);
			$this->db->where("id",$game_type['id']);
			$this->db->update("game_type",$game_type);

			$this->utils->debug_log("Adjusted Game Type ===============================>", $game_type);
		}
	}

}
