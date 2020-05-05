<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Sagaming_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "sagaming_game_logs";

    public function checkGameId($gameId,$betId,$gameUsername){
        $this->db->select("extra")
                 ->where("GameID", $gameId)
                 ->where("Username", $gameUsername);
        $result = $this->db->get($this->tableName);

        $isExist = null;
        $extra = $result->row_array();

        if(!empty($extra)){
            $extra = json_decode($extra['extra']);

            if(!empty($extra)){
                 foreach ($extra as $key => $value) {
                    if($key == $betId){
                        $isExist = true;
                        continue;
                    }
                }
            }
        }

        return $isExist;
    }

    public function isGameIdAlreadyExist($gameId,$gameUsename){
        $this->db->select("extra")
                 ->where("GameId", $gameId)
                 ->where("Username", $gameUsename);
        $result = $this->db->get($this->tableName);
        $data = $result->row_array();

        return $data;
    }

    /**
     * overview : update game logs
     *
     * @param  array    $data
     * @return boolean
     */
    public function updateGameLogs($data,$gameUsername) {
        $this->db->where('GameID', $data['GameID']);
        $this->db->where('Username', $gameUsername);
        return $this->db->update($this->tableName, $data);
    }

	public function getAvailableRows($rows) {
		$this->db->select('BetID')->from($this->tableName)->where_in('BetID', array_column($rows, 'BetID'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'BetID');
			$availableRows = array();
			foreach ($rows as $row) {
				$TicketId = $row['BetID'];
				if (!in_array($TicketId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
		}
		return $availableRows;
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {

		$select = '
                  sagaming_game_logs.id as id,
                  sagaming_game_logs.PlayerId,
				  sagaming_game_logs.UserName,
				  sagaming_game_logs.external_uniqueid,
				  sagaming_game_logs.BetTime AS game_date,
				  sagaming_game_logs.GameType AS game_type,
				  sagaming_game_logs.extGameCode AS game_code,
				  sagaming_game_logs.response_result_id,
				  sagaming_game_logs.ResultAmount AS result_amount,
				  sagaming_game_logs.BetAmount,
				  sagaming_game_logs.GameID,
				  sagaming_game_logs.BetID,
                  sagaming_game_logs.BetType,
                  sagaming_game_logs.GameType,
                  sagaming_game_logs.extra,
				  game_description.id AS game_description_id,
				  game_description.game_name AS game,
				  game_description.game_code,
				  game_description.game_type_id,
				  game_description.void_bet,
				  game_type.game_type';

		$this->db->select($select,false);
		$this->db->from('sagaming_game_logs');
		$this->db->join('game_description', 'sagaming_game_logs.extGameCode = game_description.game_code AND game_description.game_platform_id = "'.SA_GAMING_API.'" AND game_description.void_bet != 1', 'LEFT');
		$this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
		$this->db->where('sagaming_game_logs.BetTime >= "'.$dateFrom.'" AND sagaming_game_logs.BetTime <= "' . $dateTo . '"');
		$qobj = $this->db->get();

		return $qobj->result_array();
	}

}

///END OF FILE///////