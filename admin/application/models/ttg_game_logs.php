<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Ttg_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "ttg_game_logs";

	public function insertTtgGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	public function syncToTtgGameLogs($data) {
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

	public function getTtgGameLogStatistics($dateFrom, $dateTo) {

		$sql = <<<EOD
SELECT
	ttg.handId as external_uniqueid,
	ttg.playerId gameUsername,
	ttg.game ttg_game,
	ttg.gameshortcode,
	ttg.response_result_id,
	ttg.start_at,
	ttg.transactionDate as end_at,
	ttg.bet,
	ttg.amount as result,
	game_provider_auth.player_id,
	gd.id as game_description_id,
	gd.game_name as game,
	gd.game_code as game_code,
	gd.game_type_id,
	gd.void_bet as void_bet
FROM 
	ttg_game_logs as ttg
LEFT JOIN 
	game_description as gd ON gd.game_code = ttg.gameshortcode COLLATE utf8_unicode_ci and gd.void_bet!=1 and gd.game_platform_id = ?
JOIN 
	game_provider_auth ON ttg.playerId = game_provider_auth.login_name COLLATE utf8_unicode_ci AND game_provider_auth.game_provider_id = ?
WHERE 
	ttg.transactionSubType = 'Resolve' AND 
	ttg.transactionDate >= ? AND ttg.transactionDate <= ?
EOD;

		$query = $this->db->query($sql, array(
			TTG_API,
			TTG_API,
			$dateFrom,
			$dateTo,
		));

		return $this->getMultipleRow($query);
	}

	public function getAvailableRows($rows) {
		$this->db->select('uniqueid')->from($this->tableName)->where_in('uniqueid', array_column($rows, 'uniqueid'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'uniqueid');
			$availableRows = array();
			foreach ($rows as $row) {
				$uniqueId = $row['uniqueid'];
				if (!in_array($uniqueId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
		}
		return $availableRows;
	}

	public function getWager($handId) {
		$this->db->select_sum('amount','bet');
		$this->db->select_min('transactionDate','start_at');
		$this->db->where('transactionSubType', 'Wager');
		$this->db->where('handId', $handId);
		$query = $this->db->get($this->tableName);
		return $query->row_array();
	}

}

///END OF FILE///////