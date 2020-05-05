<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Agpt_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "agpt_game_logs";

	/**
	 * @param string $data
	 *
	 * @return array
	 */
	function syncToAGPTameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * @param string $uniqueId
	 *
	 * @return boolean
	 */
	function isUniqueIdAlreadyExists($uniqueId) {
		$qry = $this->db->get_where($this->tableName, array('uniqueid' => $uniqueId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	function updateGameLogs($data) {
		$this->db->where('uniqueid', $data['uniqueid']);
		return $this->db->update($this->tableName, $data);
	}

	function getAGPTGameLogStatistics($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT creationtime,
transferamount,
currentamount,
transfertype,
datatype,
gamecode,
tablecode,
validbetamount,
netamount,
bettime,
validbetamount,
external_uniqueid,
beforecredit,
response_result_id,
game_description.game_type_id,
game_description.id as game_description_id,
bettime as bettime_start_at,
netamount as result_amount,
min(gametype) as game_code,
min(gametype) as game_type,
min(gametype) as game,
CONVERT(SUBSTR( MIN(CONCAT(billno,'-',beforecredit) ),16, 20 ) , DECIMAL) + SUM(netamount) AS after_balance,
SUM(validbetamount) AS bet_amount,
MIN(playername) AS playername,
min(flag) as flag
FROM ag_game_logs
left JOIN game_description ON ag_game_logs.gametype = game_description.game_code and game_description.game_platform_id=? and game_description.void_bet!=1
WHERE bettime >= ?
AND bettime <= ?
GROUP BY external_uniqueid
HAVING min(flag) = 1
EOD;

		// $this->utils->debug_log($sql, $dateFrom, $dateTo);
		$query = $this->db->query($sql, array(AGPT_API, $dateFrom, $dateTo));
		return $this->getMultipleRow($query);
	}
}

///END OF FILE///////
