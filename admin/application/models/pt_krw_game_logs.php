<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class PT_krw_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "pt_krw_game_logs";

	public function insertPTGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	public function syncToPTGameLogs($data) {
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

	function getPTGameLogStatistics($dateFrom, $dateTo, $playerName = null) {
		// AND gameshortcode IN ('pba','sb','rop','ro','ro_g','ro3d','rodz','rodz_g','rom','rop_g','rouk','7bal','bal','bjl','plba','rofl','rol','sbl','vbal')
		//playerName is game username
		$searchPlayername = '';
		if (!empty($playerName)) {
			$searchPlayername = "and pt_krw_game_logs.playername=?";
		}
		$sql = <<<EOD
SELECT pt_krw_game_logs.id as id,
pt_krw_game_logs.playername,
pt_krw_game_logs.gamename,
pt_krw_game_logs.uniqueid,
pt_krw_game_logs.gameshortcode,pt_krw_game_logs.gamecode,
pt_krw_game_logs.bet,
pt_krw_game_logs.gamedate,
pt_krw_game_logs.external_uniqueid,
pt_krw_game_logs.gametype,
pt_krw_game_logs.gameid,
pt_krw_game_logs.response_result_id,
game_description.id as game_description_id,
game_description.game_type_id as game_type_id,

pt_krw_game_logs.balance AS after_balance,

bet AS bet_amount,
pt_krw_game_logs.playername AS player_username ,
pt_krw_game_logs.gamedate AS start_at ,
IF( bet>=win && win>0,1 ,0) AS has_both_side,

win - bet AS result_amount,

game_provider_auth.player_id

FROM pt_krw_game_logs

left JOIN game_description ON (pt_krw_game_logs.gameshortcode = game_description.game_code )
and game_description.game_platform_id=? and game_description.void_bet!=1

JOIN game_provider_auth ON pt_krw_game_logs.playername = game_provider_auth.login_name
  and game_provider_auth.game_provider_id = ?

WHERE gamedate >= ?
AND gamedate <= ?

$searchPlayername

EOD;

// 		$sql = <<<EOD
		// SELECT pt_krw_game_logs.id as id,pt_krw_game_logs.playername,pt_krw_game_logs.gamename,pt_krw_game_logs.gameshortcode,pt_krw_game_logs.gamecode,pt_krw_game_logs.bet,pt_krw_game_logs.gamedate,
		// pt_krw_game_logs.external_uniqueid,pt_krw_game_logs.gametype,pt_krw_game_logs.gameid,pt_krw_game_logs.response_result_id,
		// game_description.id as game_description_id,
		// game_description.game_type_id as game_type_id,
		// MAX(CONCAT(pt_krw_game_logs.gamecode,'-',pt_krw_game_logs.balance)) AS after_balance,
		// SUM(bet) AS bet_amount,
		// MIN(CONCAT(pt_krw_game_logs.gamecode,'-',pt_krw_game_logs.gameshortcode)) AS game ,
		// MIN(pt_krw_game_logs.playername) AS player_username ,
		// MIN(pt_krw_game_logs.gamedate) AS start_at ,
		// MAX(IF( bet>=win && win>0,1 ,0)) AS has_both_side,
		// SUM(win+progressivewin - bet) AS result_amount,
		// game_description.void_bet as void_bet
		// FROM pt_krw_game_logs
		// left JOIN game_description ON (lower(pt_krw_game_logs.gameshortcode) = lower(game_description.game_code) )
		//   and game_description.game_platform_id=? and game_description.void_bet!=1

// JOIN game_provider_auth ON lower(pt_krw_game_logs.playername) = lower(game_provider_auth.login_name)  and game_provider_auth.game_provider_id = ?

// WHERE gamedate >= ?
		// AND gamedate <= ?

// $searchPlayername

// GROUP BY external_uniqueid
		// EOD;

// 		$sql = <<<EOD
		// SELECT pt_krw_game_logs.id as id,pt_krw_game_logs.playername,pt_krw_game_logs.gamename,pt_krw_game_logs.gameshortcode,pt_krw_game_logs.gamecode,pt_krw_game_logs.bet,pt_krw_game_logs.gamedate,
		// 	pt_krw_game_logs.external_uniqueid,pt_krw_game_logs.gametype,pt_krw_game_logs.gameid,pt_krw_game_logs.response_result_id,
		// 	game_description.id as game_description_id,
		// 	game_description.game_type_id as game_type_id,
		// 	MAX(CONCAT(pt_krw_game_logs.gamecode,'-',pt_krw_game_logs.balance)) AS after_balance,
		// 	SUM(IF( bet>=win && win>0, bet-win,bet)) AS bet_amount,
		// 	MIN(CONCAT(pt_krw_game_logs.gamecode,'-',pt_krw_game_logs.gameshortcode)) AS game ,
		// 	MIN(pt_krw_game_logs.playername) AS player_username ,
		// 	MIN(pt_krw_game_logs.gamedate) AS start_at ,
		// 	MAX(IF( bet>=win && win>0,1 ,0)) AS has_both_side,
		// 	SUM(win+progressivewin - bet) AS result_amount
		// 	FROM pt_krw_game_logs
		// 	JOIN game_description ON (lower(pt_krw_game_logs.gameshortcode) = lower(game_description.game_code) ) and game_description.game_platform_id=? and game_description.void_bet!=1

// JOIN game_provider_auth ON lower(pt_krw_game_logs.playername) = lower(game_provider_auth.login_name)  and game_provider_auth.game_provider_id = ?

// 	WHERE gamedate >= ?
		// 	AND gamedate <= ?
		// 	GROUP BY external_uniqueid
		// EOD;

		// $this->utils->debug_log($sql);
		// $params = array(PT_API, PT_API, $dateFrom, $dateTo);
		$params = array($dateFrom, $dateTo);
		if (!empty($playerName)) {
			$params[] = $playerName;
		}

		$query = $this->db->query($sql, $params);
		return $this->getMultipleRow($query);
	}

	public function getAvailableRows($rows) {
		$arr = array();
		foreach ($rows as $row) {
			$uniqueId = $row['GAMECODE'];
			$arr[] = $uniqueId;
		}

		$this->db->select('uniqueid')->from($this->tableName)->where_in('uniqueid', $arr);
		$existsRow = $this->runMultipleRow();

		// $this->utils->printLastSQL();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->uniqueid;
			}
			$availableRows = array();
			foreach ($rows as $row) {
				$uniqueId = $row['GAMECODE'];
				if (!in_array($uniqueId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			//add all
			$availableRows = $rows;
		}

		return $availableRows;
	}
}

///END OF FILE///////