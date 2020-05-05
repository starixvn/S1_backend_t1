<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ab_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "ab_game_logs";

	public function insertAbGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	public function syncToAbGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	public function isUniqueIdAlreadyExists($uniqueId) {
		$qry = $this->db->get_where($this->tableName, array('betNum' => $uniqueId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	// public function getAbGameLogStatistics($dateFrom, $dateTo) {
	public function getGameLogStatistics($dateFrom, $dateTo) {

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
	ab.gameType as game_code,
	gd.game_type_id,
	gd.void_bet as void_bet,
    game_provider_auth.login_name as gameUsername,
    ab.validAmount,
    ab.winOrLoss,
    ab.betNum,
    ab.betAmount,
    ab.gameRoundId,
    ab.gameRoundStartTime,
    ab.gameRoundEndTime,
    ab.extra
FROM 
	ab_game_logs as ab
LEFT JOIN 
	game_description as gd ON ab.gameType = gd.external_game_id and gd.void_bet!=1 and gd.game_platform_id = ?
JOIN 
	game_provider_auth 
	ON 
		ab.client = game_provider_auth.login_name COLLATE utf8_unicode_ci AND 
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
		$this->db->select('betNum')->from($this->tableName)->where_in('betNum', array_column($rows,'betNum'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if ( ! empty($existsRow)) {
			$existsId = array_column($existsRow, 'betNum');
			$availableRows = array();
			foreach ($rows as $row) {
				$uniqueId = $row['betNum'];
				if ( ! in_array($uniqueId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
		}
		return $availableRows;
	}

	public function getExistingBetIds($betIds) {
        $this->db->select('id, betNum')->from($this->tableName)->where_in('betNum', $betIds);
        return $this->runMultipleRowArray();
    }

}

///END OF FILE///////