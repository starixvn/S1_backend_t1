<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Extreme_live_gaming_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "extreme_live_gaming_game_logs";

	/**
	 * overview : check if idx already exist
	 *
	 * @param  int		$idx
	 *
	 * @return boolean
	 */
	public function isRowIdAlreadyExists($transactionReferenceId) {
		$qry = $this->db->get_where($this->tableName, array('transactionReferenceId' => $transactionReferenceId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	public function getStakeByReferenceId($referenceId){
		$qry = $this->db->get_where($this->tableName, array('bet_referenceId' => $referenceId,
															'transactionType' => "NrgsB2bRound_Stake"
																));
		return $this->getOneRow($qry);
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {

		return 1;
	}

}

///END OF FILE///////