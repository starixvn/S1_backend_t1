<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Impt_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "impt_game_logs";

	public function insertImptGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	public function syncToImptGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	public function isUniqueIdAlreadyExists($uniqueId) {
		$qry = $this->db->get_where($this->tableName, array('GameCode' => $uniqueId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	public function getImptGameLogStatistics($dateFrom, $dateTo) {

		$sql = <<<EOD
SELECT
impt.GameDate,
impt.Bet,
impt.Win,
impt.GameCode,
impt.PlayerName,
impt.GameType,
impt.GameName,
impt.SessionId,
impt.Balance,
impt.ProgressiveWin,
impt.ProgressiveBet,
impt.response_result_id,
impt.external_uniqueid,
impt.gameshortcode,
game_provider_auth.player_id,
gd.id as game_description_id,
gd.game_name as game,
gd.game_type_id,
gd.void_bet as void_bet
FROM impt_game_logs as impt
LEFT JOIN game_description as gd ON gd.game_code = impt.gameshortcode and gd.game_platform_id = ?
JOIN game_provider_auth ON impt.PlayerName = game_provider_auth.login_name AND game_provider_auth.game_provider_id = ?
WHERE impt.GameDate >= ? AND impt.GameDate <= ?
EOD;

		$query = $this->db->query($sql, array(
			IMPT_API,
			IMPT_API,
			$dateFrom,
			$dateTo,
		));

		return $this->getMultipleRow($query);
	}

	public function getAvailableRows($rows) {
		$this->db->select('GameCode')->from($this->tableName)->where_in('GameCode', array_column($rows, 'GameCode'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'GameCode');
			$availableRows = array();
			foreach ($rows as $row) {
				$uniqueId = $row['GameCode'];
				if (!in_array($uniqueId, $existsId)) {
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