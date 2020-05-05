<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Bs_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "bs_game_logs";

	public function insertBsGameLogs($data) {
		unset($data['hash']);
		return $this->db->insert($this->tableName, $data);
	}

	public function syncToBsGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	public function isUniqueIdAlreadyExists($uniqueId) {
		$qry = $this->db->get_where($this->tableName, array('TRANSACTIONID' => $uniqueId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	public function getBsGameLogStatistics($dateFrom, $dateTo) {

       // [histories] => Array
       //  (
       //      [0] => Array
       //          (
       //              [betAmount] => 20
       //              [betNum] => 1971985498814619
       //              [betTime] => 2016-04-01 21:23:06
       //              [betType] => 1001
       //              [client] => ogtest9867
       //              [commission] => 100
       //              [currency] => CNY
       //              [exchangeRate] => 1
       //              [gameResult] => {303,108,-1},{407,302,-1}
       //              [gameRoundEndTime] => 2016-04-01 21:23:18
       //              [gameRoundId] => 197198549
       //              [gameRoundStartTime] => 2016-04-01 21:22:28
       //              [gameType] => 101
       //              [state] => 0
       //              [tableName] => B003
       //              [validAmount] => 19
       //              [winOrLoss] => 19
       //          )
       //      [1] => Array
       //          (
       //              [betAmount] => 20
       //              [betNum] => 1971986147612193
       //              [betTime] => 2016-04-01 21:23:45
       //              [betType] => 1002
       //              [client] => ogtest9867
       //              [commission] => 100
       //              [currency] => CNY
       //              [exchangeRate] => 1
       //              [gameResult] => {413,112,-1},{108,213,-1}
       //              [gameRoundEndTime] => 2016-04-01 21:24:24
       //              [gameRoundId] => 197198614
       //              [gameRoundStartTime] => 2016-04-01 21:23:34
       //              [gameType] => 101
       //              [state] => 0
       //              [tableName] => B003
       //              [validAmount] => 20
       //              [winOrLoss] => -20
       //          )
       //  )

		$sql = <<<EOD
SELECT 
	gd.id as game_description_id, 
	gd.game_name as game, 
	gd.game_code as game_code, 
	gd.game_type_id,
	gd.void_bet as void_bet
FROM 
	ab_game_logs as ab
LEFT JOIN 
	game_description as gd ON ab.gameType = gd.external_game_id and gd.void_bet!=1 and gd.game_platform_id = ?
JOIN 
	game_provider_auth 
	ON 
		ab.client = game_provider_auth.login_name AND 
		game_provider_auth.game_provider_id = ?
WHERE
	ab.gameRoundEndTime >= ? AND ab.gameRoundEndTime <= ?
EOD;
	
		$query = $this->db->query($sql, array(
			AB_API,
			AB_API,
			$dateFrom,
			$dateTo,
		));

		return $this->getMultipleRow($query);
	}

	public function getAvailableRows($rows) {
		$this->db->select('gameRoundId')->from($this->tableName)->where_in('gameRoundId', array_column($rows,'gameRoundId'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if ( ! empty($existsRow)) {
			$existsId = array_column($existsRow, 'gameRoundId');
			$availableRows = array();
			foreach ($rows as $row) {
				$uniqueId = $row['gameRoundId'];
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