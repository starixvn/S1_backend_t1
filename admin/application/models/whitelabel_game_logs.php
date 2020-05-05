<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Whitelabel_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "whitelabel_game_logs";

	public function getAvailableRows($rows) {

		$this->db->select('refNo')->from($this->tableName)->where_in('refNo', array_column($rows, 'refNo'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'refNo');
			$availableRows = array();
			foreach ($rows as $row) {
				$refNo = $row['refNo'];
				if (!in_array($refNo, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
		}
		return $availableRows;
	}

	/**
	 * overview : check if refNo already exist
	 *
	 * @param  int		$refNo
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($refNo) {
		$qry = $this->db->get_where($this->tableName, array('refNo' => $refNo));
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
		$this->db->where('refNo', $data['refNo']);
		return $this->db->update($this->tableName, $data);
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {

		$select = 'whitelabel_game_logs.PlayerId,
				  whitelabel_game_logs.UserName,
				  whitelabel_game_logs.external_uniqueid,
				  IFNULL(whitelabel_game_logs.doneTime,whitelabel_game_logs.orderTime ) AS game_date,
				  whitelabel_game_logs.ProductType AS game_code,
				  whitelabel_game_logs.response_result_id,
				  whitelabel_game_logs.winlose AS result_amount,
				  whitelabel_game_logs.stake AS BetAmount,
				  whitelabel_game_logs.status,
                  whitelabel_game_logs.refNo,
                  whitelabel_game_logs.actualStake,
                  whitelabel_game_logs.accountId,
                  whitelabel_game_logs.gameId,
                  whitelabel_game_logs.subBet,
                  whitelabel_game_logs.tableName,
                  whitelabel_game_logs.doneTime,
                  whitelabel_game_logs.stake,
                  whitelabel_game_logs.odds,
                  whitelabel_game_logs.match,
                  whitelabel_game_logs.isLive,
                  whitelabel_game_logs.betOption,
                  whitelabel_game_logs.hdp,
                  whitelabel_game_logs.sportType,
				  game_description.id AS game_description_id,
				  game_description.game_name AS game,
				  game_description.game_code,
				  game_description.game_type_id,
				  game_description.void_bet,
				  game_type.game_type';

		$this->db->select($select,false);
		$this->db->from('whitelabel_game_logs');
		$this->db->join('game_description', 'whitelabel_game_logs.ProductType = game_description.game_code AND game_description.game_platform_id = "'.SBOBET_API.'" AND game_description.void_bet != 1', 'LEFT');
		$this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
		$this->db->where('IFNULL(`whitelabel_game_logs`.`doneTime`,`whitelabel_game_logs`.`orderTime`) >= "'.$dateFrom.'" AND IFNULL(`whitelabel_game_logs`.`doneTime`,`whitelabel_game_logs`.`orderTime`) <= "' . $dateTo . '"');
		$qobj = $this->db->get();

		return $qobj->result_array();
	}

}

///END OF FILE///////