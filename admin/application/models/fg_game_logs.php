<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Fg_game_logs
 *
 * General behaviors include :
 *
 * * Add API game logs
 * * Check if row id already exist
 * * Get last version key
 * * Update game logs
 * * Get game logs statistics
 * * Get available rows
 *
 * @category Game Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Fg_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "fg_game_logs";

	/**
	 * overview : insert fg game logs
	 *
	 * @param  array	$data
	 * @return boolean
	 */
	public function insertFGGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * overview : check if trans id already exist
	 * @param  int		$game_tran_id
	 * @return boolean
	 */
	function isTransIdAlreadyExists($game_tran_id,$get_data = false) {
		 $this->db->select('*')
                 ->where('game_tran_id',$game_tran_id);
        $query = $this->db->get($this->tableName);

        return $query->row_array();
	}

    function checkGameTransactionId($transaction_id,$game_transaction_id){

        $this->db->select('extra')
                 ->where('game_tran_id',$game_transaction_id);
        $query = $this->db->get($this->tableName);

        $extra = $query->row_array();

        $isExist = null;
        if(!empty($extra['extra'])){
            $extra = json_decode($extra['extra']);
            foreach ($extra as $key => $value) {
                if(isset($isExist)) continue;
                $isExist = ($key == $transaction_id);
            }
        }
        return $isExist;
    }

	/**
	 * overview : check if win amount exist
	 *
	 * @param  int		$game_tran_id
	 * @return boolean
	 */
	function isWinAmountExists($game_tran_id) {
		$qry = $this->db->get_where($this->tableName, array('game_tran_id' => $game_tran_id, 'win_flag' => true));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * overview : get result amount
	 *
	 * @param  int		$game_tran_id
	 * @return boolean
	 */
	function getResultAmount($game_tran_id) {
		$this->db->select('result_amount,amount as bet_amount')->from($this->tableName);
		$this->db->where('game_tran_id', $game_tran_id);
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * overview : update game logs
	 *
	 * @param  array	$data
	 * @return boolean
	 */
	function updateGameLogs($data) {
		$this->db->where('game_tran_id', $data['game_tran_id']);
		return $this->db->update($this->tableName, $data);
	}

	/**
	 * overview : get fg game log statistics
	 *
	 * @param datetime	$dateFrom
	 * @param datetime	$dateTo
	 * @return array
	 */
	function getFGGameLogStatistics($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT fg.id,
	   fg.user_id as username,
	   fg.trans_id,
       fg.amount as bet_amount,
       fg.balance,
       fg.result_amount,
       fg.game_id as gameshortcode,
       fg.external_uniqueid,
       fg.response_result_id,
       fg.date_time,
       fg.platform_code,
       fg.win_flag,
       fg.extra,
       fg.game_tran_id,
	   gd.id as game_description_id,
	   gd.game_name as game,
	   gd.game_code as game_code,
	   gd.game_type_id,
	   gp.player_id,
	   gt.game_type
FROM fg_game_logs as fg
LEFT JOIN game_description as gd ON fg.game_id = gd.external_game_id and gd.game_platform_id = ?
LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
JOIN game_provider_auth as gp ON fg.user_id = gp.login_name and game_provider_id=?
WHERE
fg.date_time >= ? AND fg.date_time <= ?
EOD;

		// $this->utils->debug_log($sql);
		$query = $this->db->query($sql, array(
			FG_API,
			FG_API,
			$dateFrom,
			$dateTo,
		));
		return $this->getMultipleRow($query);
	}

	/**
	 * overview : get available rows
	 *
	 * @param $rows
	 * @return array|null
	 */
	public function getAvailableRows($rows) {
		if (!empty($rows)) {
			$arr = array();
			foreach ($rows as $row) {
				$uniqueId = $row['id'];
				$arr[] = $uniqueId;
			}

			$this->db->select('trans_id')->from($this->tableName)->where_in('trans_id', $arr);
			$existsRow = $this->runMultipleRow();
			// $this->utils->printLastSQL();
			$availableRows = null;
			if (!empty($existsRow)) {
				$existsId = array();
				foreach ($existsRow as $row) {
					$existsId[] = $row->trans_id;
				}
				$availableRows = array();
				foreach ($rows as $row) {
					$uniqueId = $row['id'];
					if (!in_array($uniqueId, $existsId)) {
						$availableRows[] = $row;
					}
				}
			} else {
				//add all
				$availableRows = $rows;
			}
			return $availableRows;
		} else {
			return null;
		}

	}

}

///END OF FILE///////
