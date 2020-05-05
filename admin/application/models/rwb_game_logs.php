<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Rwb_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "rwb_game_logs";
	const STATUS_REFUND = 4;
	const PENDING_REFUND = 0;
	const REFUNDED = 1;

	/**
	 * overview : check if bet_id already exist
	 *
	 * @param  int		$bet_id
	 *
	 * @return boolean
	 */
	public function isRowIdAlreadyExists($bet_id) {
		$qry = $this->db->get_where($this->tableName, array('bet_id' => $bet_id));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * overview : get refund logs
	 *
	 * @param  int $limit
	 *
	 * @return array
	 */
	public function getRefundLogs($limit) {
		// return $startDate;
		$this->db->select('id, user_id,stake')
			->from($this->tableName)
				->where('settle_status',self::STATUS_REFUND)
					->where('refunded',self::PENDING_REFUND)
						->limit($limit);
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * overview : update game logs
	 *
	 * @param  array	$data
	 *
	 * @return boolean
	 */
	public function updateGameLogs($data) {
		$this->db->where('bet_id', $data['bet_id']);
		return $this->db->update($this->tableName, $data);
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {
		$select = 'rwb_game_logs.user_id,
				  rwb_game_logs.bet_id,
				  rwb_game_logs.status,
				  rwb_game_logs.stake AS bet_amount,
				  (rwb_game_logs.payout - rwb_game_logs.stake) AS result_amount,
    			  rwb_game_logs.payout,
    			  rwb_game_logs.potential_payout,
    			  rwb_game_logs.bet_time,
    			  rwb_game_logs.settle_time,
    			  rwb_game_logs.settle_status,
    			  rwb_game_logs.ip_address,
    			  rwb_game_logs.selections,
    			  rwb_game_logs.external_uniqueid,
    			  rwb_game_logs.response_result_id,
    			  rwb_game_logs.created_at';

		$this->db->select($select,false);
		$this->db->from('rwb_game_logs');
		$this->db->where('rwb_game_logs.bet_time >= "'.$dateFrom.'" AND rwb_game_logs.bet_time <= "' . $dateTo . '"');
		$this->db->or_where('rwb_game_logs.settle_time >= "'.$dateFrom.'" AND rwb_game_logs.settle_time <= "' . $dateTo . '"');
		$qobj = $this->db->get();
		return $qobj->result_array();
	}

	public function setTransactionToRefunded($transactionId) {
		if (empty($transactionId)) {	return;		}
		
		$this->db->where('id', $transactionId);
		return $this->runUpdate(array('refunded' => self::REFUNDED));
	}
}

///END OF FILE///////