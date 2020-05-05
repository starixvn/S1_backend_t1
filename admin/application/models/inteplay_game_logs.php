<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Inteplay_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "inteplay_game_logs";

	public function insertInteplayGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	public function syncToInteplayGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	public function isUniqueIdAlreadyExists($uniqueId) {
		$qry = $this->db->get_where($this->tableName, array('gameSetKey' => $uniqueId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	public function getInteplayGameLogStatistics($dateFrom, $dateTo) {

		$sql = <<<EOD
SELECT 
	inteplay.createTimeStr, 
	inteplay.totalBet, 
	inteplay.totalWinLose, 
	inteplay.gameKey, 
	inteplay.gameSetKey, 
	inteplay.playname, 
	gd.id as game_description_id, 
	gd.game_name as game, 
	gd.game_code as game_code, 
	gd.game_type_id,
	gd.void_bet as void_bet
FROM 
	inteplay_game_logs as inteplay
LEFT JOIN 
	game_description as gd ON inteplay.gameKey = gd.external_game_id and gd.void_bet!=1 and gd.game_platform_id = ?
JOIN 
	game_provider_auth 
	ON 
		inteplay.playname = game_provider_auth.login_name AND 
		game_provider_auth.game_provider_id = ?
WHERE
	inteplay.createTimeStr >= ? AND inteplay.createTimeStr <= ?
EOD;

		$query = $this->db->query($sql, array(
			INTEPLAY_API,
			INTEPLAY_API,
			$dateFrom,
			$dateTo,
		));

		return $this->getMultipleRow($query);
	}

	public function getAvailableRows($rows) {
		$this->db->select('gameSetKey')->from($this->tableName)->where_in('gameSetKey', array_column($rows,'gameSetKey'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if ( ! empty($existsRow)) {
			$existsId = array_column($existsRow, 'gameSetKey');
			$availableRows = array();
			foreach ($rows as $row) {
				$uniqueId = $row['gameSetKey'];
				if ( ! in_array($uniqueId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
		}
		return $availableRows;
	}

}

///END OF FILE///////