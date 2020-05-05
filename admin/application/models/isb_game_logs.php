<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Isb_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "isb_raw_game_logs";

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	function isRoundIdAlreadyExists($roundid) {
		$qry = $this->db->get_where($this->tableName, array('roundid' => $roundid));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	function isWinAmountExists($roundid) {
		$qry = $this->db->get_where($this->tableName, array('roundid' => $roundid, 'win_flag' => true));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	function getResultAmount($roundid) {
		$this->db->select('result_amount,amount as bet_amount,win_flag')->from($this->tableName);
		$this->db->where('roundid', $roundid);
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($rowId) {
		$qry = $this->db->get_where($this->tableName, array('roundid' => $rowId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	function updateBetRow($row) {
		$this->db->where('roundid', $row['roundid']);
		$this->db->where_in('type', array('WIN','FREE_ROUND_WIN'));
		$this->db->set('result_amount', 'result_amount + ' . $row['amount'], false);
		$this->db->update('isb_raw_game_logs', array(
			'result_time' => $row['time'],
			'result_balance' => $row['balance'],
			'jpw' => $row['jpw'],
			'jpw_jpc' => $row['jpw_jpc'],
		));
	}

	function updateRound($roundid) {
		# GET RESULT AMOUNT AND LAST TRANSACTION ID
		$this->db->select_max('transactionid');
		$this->db->select_sum('amount');
		$this->db->where('roundid', $roundid);
		$this->db->where_in('type', array('WIN','FREE_ROUND_WIN'));
		$query = $this->db->get('isb_raw_game_logs');
		$row = $query->row_array();

		$transactionid = $row['transactionid'];

		if ($transactionid) {

			$result_amount = $row['amount'];

			# GET LAST TRANSACTION
			$query = $this->db->get_where('isb_raw_game_logs', array('transactionid' => $transactionid), 1);
			$row = $query->row_array();

			$result_time = $row['time'];
			$result_balance = $row['balance'];
			$jpw = $row['jpw'];
			$jpw_jpc = $row['jpw_jpc'];

			# UPDATE BET RECORD
			$this->db->where_in('type',array('BET','FREE_ROUND_BET'));
			$this->db->where('roundid', $roundid);
			$this->db->update('isb_raw_game_logs', array(
				'result_amount' => $result_amount,
				'result_time' => $result_time,
				'result_balance' => $result_balance,
				'jpw' => $jpw,
				'jpw_jpc' => $jpw_jpc,
				'roundid' => $roundid,
			));

		}

		// $this->db->set('result_amount', 'result_amount + ' . $row['amount'], false);
		// $this->db->update('isb_raw_game_logs', array(
			// 'result_time' => $row['time'],
			// 'result_balance' => $row['balance'],
			// 'jpw' => $row['jpw'],
			// 'jpw_jpc' => $row['jpw_jpc'],
		// ));
	}

	// function updateGameLogs($data) {
	// 	$this->db->where('roundid', $data['roundid']);
	// 	return $this->db->update($this->tableName, $data);
	// }

	function getIsbGameLogStatistics($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT isb.id,
isb.playerid as player_name,
isb.amount as bet_amount,
isb.result_amount,
isb.gameid as gameshortcode,
isb.time as transaction_time,
isb.response_result_id,
isb.external_uniqueid,
gd.id as game_description_id,
gd.game_name as game,
gd.game_code,
gd.game_type_id,
gt.game_type
FROM isb_game_logs as isb
LEFT JOIN game_description as gd ON isb.gameid = gd.external_game_id COLLATE utf8_unicode_ci and gd.game_platform_id = ?
LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
JOIN game_provider_auth as gp ON isb.playerid = gp.login_name COLLATE utf8_unicode_ci and game_provider_id=?
WHERE
isb.time >= ? AND isb.time <= ?
EOD;
		// $this->utils->debug_log($sql);
		$query = $this->db->query($sql, array(
			ISB_API,
			ISB_API,
			$dateFrom,
			$dateTo,
		));
		// echo $this->db->last_query();exit();
		return $this->getMultipleRow($query);
	}

	// public function getAvailableRows($rows) {
	// 	if (!empty($rows)) {
	// 		$arr = array();
	// 		foreach ($rows as $row) {
	// 			$uniqueId = $row['roundid'];
	// 			$arr[] = $uniqueId;
	// 		}

	// 		$this->db->select('roundid')->from($this->tableName)->where_in('roundid', $arr);
	// 		$this->db->where('win_flag', true);
	// 		$existsRow = $this->runMultipleRow();
	// 		// $this->utils->printLastSQL();
	// 		$availableRows = null;
	// 		if (!empty($existsRow)) {
	// 			$existsId = array();
	// 			foreach ($existsRow as $row) {
	// 				$existsId[] = $row->roundid;
	// 			}
	// 			$availableRows = array();
	// 			foreach ($rows as $row) {
	// 				$uniqueId = $row['roundid'];
	// 				if (!in_array($uniqueId, $existsId)) {
	// 					$availableRows[] = $row;
	// 				}
	// 			}
	// 		} else {
	// 			//add all
	// 			$availableRows = $rows;
	// 		}
	// 		return $availableRows;
	// 	} else {
	// 		return null;
	// 	}

	// }

	function getGameLogStatistics($dateFrom, $dateTo) {

		$this->db->select('player.playerId as player_id');
		$this->db->select('player.username as player_username');
		$this->db->select('isb_raw_game_logs.playerid as game_username');
		$this->db->select('isb_raw_game_logs.gameid as external_game_id');
		$this->db->select('isb_raw_game_logs.roundid as external_uniqueid');
		$this->db->select('isb_raw_game_logs.time as start_at');
		// $this->db->select('isb_raw_game_logs.result_time as end_at');
		$this->db->select('IFNULL(isb_raw_game_logs.result_time,isb_raw_game_logs.time ) AS end_at');//check if result_time is null and pass time as end_at
		$this->db->select('(isb_raw_game_logs.amount/100) as bet_amount');
		$this->db->select('((isb_raw_game_logs.result_amount-isb_raw_game_logs.amount)/100) as result_amount');
        // $this->db->select('(isb_raw_game_logs.result_balance/100) as after_balance');
		$this->db->select('(isb_raw_game_logs.result_balance/100) as after_balance');
        $this->db->select('isb_raw_game_logs.response_result_id');
		$this->db->select('isb_raw_game_logs.status');
		$this->db->where_in('isb_raw_game_logs.type', array('BET','FREE_ROUND_BET'));

		// $this->db->select(array(
		// 	"player.playerId as player_id",
		// 	"player.username as player_username",
		// 	"isb_raw_game_logs.playerid as game_username",
		// 	"isb_raw_game_logs.gameid as external_game_id",
		// 	"isb_raw_game_logs.roundid as external_uniqueid",
		// 	"MIN(isb_raw_game_logs.time) as start_at",
		// 	"MAX(isb_raw_game_logs.time) as end_at",
		// 	"SUM(IF(type='BET',isb_raw_game_logs.amount,0))/100 as bet_amount",
		// 	"SUM(IF(type='WIN',isb_raw_game_logs.amount,-isb_raw_game_logs.amount))/100 as result_amount",
		// 	"substring_index(MAX(CONCAT_WS('|', isb_raw_game_logs.time, isb_raw_game_logs.balance)),'|',-1)/100 as after_balance",
		// ))->group_by(array('game_username','external_game_id','external_uniqueid'));

		$this->db->from('isb_raw_game_logs');
		$this->db->join('game_provider_auth', 'isb_raw_game_logs.playerid = game_provider_auth.login_name');
		$this->db->join('player', 'game_provider_auth.player_id = player.playerId');
        $this->db->where('isb_raw_game_logs.status !=', 'ACTIVE');

		if ($dateFrom) {
			$this->db->where('time >=', $dateFrom);
		}

		if ($dateTo) {
			$this->db->where('time <=', $dateTo);
		}

        $data = $this->runMultipleRow();
		return $data;
	}

	#Note i override this, it hangs (uniqueid so long)when im using the parent function- raw sql is efficient:aris
	public function getAvailableRows($rows) {
 		$availableRows = array();
		if(isset($rows)){
			foreach ($rows as $row) { 
				if(isset($row['uniqueid'])){
 					$sql = "SELECT uniqueid FROM {$this->tableName}  WHERE  uniqueid = ? ";
					$query = $this->db->query($sql, array($row['uniqueid']));
					if($query->num_rows() == 0){ 
				    	array_push($availableRows, $row);
				    }
				}
			}
		}
		return $availableRows;
	}
   
	public function updateBetStatus($uniqueid, $status){

		$sql = "SELECT uniqueid FROM {$this->tableName} WHERE  uniqueid = ? AND status = 'ACTIVE' ";
		$query = $this->db->query($sql, array($uniqueid));
		if($query->num_rows() > 0){ 
		   # UPDATE BET RECORD
			$this->db->where('uniqueid', $uniqueid);
			$this->db->update($this->tableName, array(
				'uniqueid' => $uniqueid,
				'status' => $status
			));
		}

		return true;
	}

}

///END OF FILE///////
