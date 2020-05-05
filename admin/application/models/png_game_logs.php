<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Png_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "png_game_logs";

	public function insertBatchToPNGStreamGameLogs($data) {
		return $this->db->insert_batch("png_stream_game_logs", $data);
	}

	public function getAvailableRows($rows) {
		$this->db->select('TransactionId')->from($this->tableName)->where_in('TransactionId', array_column($rows,'TransactionId'));
		$existsRow = $this->runMultipleRowArray();

	    if (empty($existsRow)) {
	   		return $rows;
	    }

		$existsId = array_column($existsRow, 'TransactionId');
        $availableRows = array();
        foreach ($rows as $row) {
            $snId = $row['TransactionId'];
            if (!in_array($snId, $existsId)) {
                $availableRows[] = $row;
            }
        }
        return $availableRows;
	}

	/**
	 * overview : check if RoundId already exist
	 *
	 * @param  int		$RoundId
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($TransactionId) {
		$qry = $this->db->get_where($this->tableName, array('TransactionId' => $TransactionId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * overview : update game logs
	 *
	 * @param  array	$data
	 *
	 * @return boolean
	 */
	function updateGameLogs($data) {
		$this->db->where('TransactionId', $data['TransactionId']);
		return $this->db->update($this->tableName, $data);
	}
	function getOriginalGameLogsByRoundId($roundId,$type) {

		$select = 'png_game_logs.PlayerId,
				  png_game_logs.UserName,
				  png_game_logs.external_uniqueid,
				  png_game_logs.TransactionId,
				  png_game_logs.Time,
				  png_game_logs.GameId,
				  png_game_logs.response_result_id,
				  png_game_logs.Amount,
                  png_game_logs.RoundId,
                  png_game_logs.RoundLoss,
                  png_game_logs.MessageType,
                  png_game_logs.Balance,
                  png_game_logs.Status,
				  game_description.id AS game_description_id,
				  game_description.game_name AS game,
				  game_description.game_code,
				  game_description.game_type_id,
				  game_description.void_bet,
				  game_type.game_type';

		$this->db->select($select,false);
		$this->db->from('png_game_logs');
		$this->db->join('game_description', 'png_game_logs.GameId = game_description.external_game_id AND game_description.game_platform_id = "'.PNG_API.'" AND game_description.void_bet != 1', 'LEFT');
		$this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
		$this->db->where('png_game_logs.RoundId = "'.$roundId.'"');
		$this->db->where('png_game_logs.MessageType = "'.$type.'"');
		$qobj = $this->db->get();

		return $qobj->row_array();
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {

	}

	public function getRoundIdsByDate($dateFrom, $dateTo){
		$rounIds = [];
		$this->db->select('RoundId');
		$this->db->from('png_game_logs');
		$this->db->where('Time >=', $dateFrom);
		$this->db->where('Time <=', $dateTo);
		$query = $this->db->get();
		$results = $query->result_array();
		foreach ($results as $result) {
			array_push($rounIds, $result['RoundId']);
		}
		$this->utils->debug_log('PNG getRoundIdsByDate roundids', $rounIds);
		return $rounIds;
	}

}

///END OF FILE///////