<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 *
 *
 */
class Random_bonus_history extends BaseModel {

	protected $tableName = 'random_bonus_history';
	const BONUS_MODE_1_OR_50 = 1;

	function __construct() {
		parent::__construct();
	}

	/**
	 * @param int playerId
	 *
	 * @return void
	 */
	function addToRandomBonusHistory($playerId, $depositTransactionId, $bonusTransactionId,
		$depositAmount, $randomBonusAmount, $randomRate, $bonus_mode = null) {
		//write to random history
		$randomBonusHistoryData = array(
			'player_id' => $playerId,
			'deposit_transaction_id' => $depositTransactionId,
			'bonus_transaction_id' => $bonusTransactionId,
			'deposit_amount' => $depositAmount,
			'bonus_amount' => $randomBonusAmount,
			'random_rate' => $randomRate,
			'created_at' => $this->utils->getNowForMysql(),
			'updated_at' => $this->utils->getNowForMysql(),
			'bonus_mode' => $bonus_mode,
		);

		$this->insertRow($randomBonusHistoryData);
	}

	/**
	 * @param int playerId
	 *
	 * @return boolean
	 */
	function isPlayerBonusExistsTodayForBonusModeCounting($playerId) {
		$this->load->model(array('promorules'));
		$this->db->select('id')->from($this->tableName);
		$this->db->where('player_id', $playerId);
		$this->db->where('created_at >=', $this->utils->getTodayForMysql() . " 00:00:00");
		$this->db->where('created_at <=', $this->utils->getTodayForMysql() . " 23:59:59");
		$this->db->where('bonus_mode', Promorules::RANDOM_BONUS_MODE_COUNTING);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}

/////end of file///////