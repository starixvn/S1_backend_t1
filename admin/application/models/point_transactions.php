<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 * * Create Point Transaction
 * * Load template
 * * Direct payment
 * * Redirect to live chat
 * * Admin service
 * * Payment transaction
 *
 * @category Player Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Point_transactions extends BaseModel {

	protected $tableName = 'point_transactions';
	protected $idField = 'id';

	# TRANSACTION_TYPE
	const DEPOSIT_POINT = 1;
	const BET_POINT = 2;
	const WIN_POINT = 3;
	const LOSS_POINT = 4;
	const DEDUCT_POINT = 5;

	# FROM/TO
	const ADMIN = 1;
	const PLAYER = 2;
	const AFFILIATE = 3;

	# STATUS
	const APPROVED = 1;
	const DECLINED = 2;

	# FLAG
	const MANUAL = 1;
	const PROGRAM = 2;

	# FROM/TO IS FOR SYSTEM (e.g. CRONJOBS)
	const SYSTEM_ID = 1;

	# ADD/DEDUCT
	const ADD = 1;
	const DEDUCT = 2;

	/**
	 * overview : Point_transactions constructor.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * overview : create point transaction
	 *
	 * @param $fromId
	 * @param $playerId
	 * @param $point
	 * @param $beforePointBalance
	 * @param $newPointBalance
	 * @param null $saleOrderId
	 * @param null $playerPromoId
	 * @param null $transaction_type
	 * @param null $additionNote
	 * @param null $promo_category
	 * @param null $action
	 * @return bool
	 */
	public function createPointTransaction(
		$fromId,
		$playerId,
		$point,
		$beforePointBalance,
		$newPointBalance,
		$saleOrderId = null,
		$playerPromoId = null,
		$transaction_type = null,
		$additionNote = null,
		$promo_category = null,
		$action = self::ADD
	) {

		if ($playerId && $point) {

			$this->load->model(array('wallet_model'));
			// $totalBeforePointBalance = $this->wallet_model->getTotalBalance($playerId);

			$note = $fromId . ' add point ' . $point . ' to ' . $playerId;

			if (!empty($playerPromoId)) {
				$note = $note . ' because player promotion: ' . $playerPromoId;
			}

			if (!empty($additionNote)) {
				$note = $note . ' ' . $additionNote;
			}

			if (empty($fromId)) {
				$fromId = self::SYSTEM_ID;
				$fromType = self::SYSTEM_ID; //cronjob
				$flag = self::PROGRAM;
			} else {
				$fromType = self::ADMIN; //admin user
				$flag = self::MANUAL;
			}

			$transactionDetails = array(
				'point' => $point,
				'transaction_type' => $transaction_type,
				'from_id' => $fromId,
				'from_type' => $fromType,
				'to_id' => $playerId,
				'to_type' => self::PLAYER,
				'note' => $note,
				'before_balance' => $beforePointBalance,
				'after_balance' => $beforePointBalance + $point,
				'status' => self::APPROVED,
				'flag' => $flag, //manual or program
				'created_at' => $this->utils->getNowForMysql(),
				'updated_at' => $this->utils->getNowForMysql(),
				'order_id' => $saleOrderId,
				'player_promo_id' => $playerPromoId,
				'total_before_balance' => $newPointBalance,
				'promo_category' => $promo_category,
				'action' => $action,
			);
			$rtn_id = $this->insertRow($transactionDetails);
			return $rtn_id;
		}
		return false;
	}

	/**
	 * overview : get all player points
	 *
	 * @param int		$playerId
	 * @param int		$limit
	 * @param int		$offset
	 * @param string	$search
	 * @param bool|false $isCount
	 * @return null
	 */
	public function pointsHistory($playerId, $limit, $offset, $search, $isCount = false) {

		if ($isCount) {
			$this->db->select('count(pt.id) as cnt');
		} else {
			$this->db->select('pt.id,
				pt.created_at,
				pt.point, pt.note,
				pt.before_balance,
				pt.after_balance,
				pt.payment_account_id,
				pa.payment_account_name,
				es.system_code,
				pt.sub_wallet_id,
				pt.transaction_type,
				pt.flag,
                pt.status,
                pt.action,
                pcs.promoName'
			);
		}

		if (isset($search['from'], $search['to'])) {
			$this->db->where("pt.created_at BETWEEN '" . $search['from'] . "' AND '" . $search['to'] . "'");

		}

		if (isset($limit, $offset)) {
			$this->db->limit($limit, $offset);
		}

		$this->db->from('point_transactions pt');
		$this->db->join('external_system es', 'es.id = pt.sub_wallet_id', 'left');
		$this->db->join('payment_account pa', 'pa.id = pt.payment_account_id', 'left');
		$this->db->join('playerpromo pp', 'pp.playerpromoId = pt.player_promo_id', 'left');
		$this->db->join('promocmssetting pcs', 'pcs.promoCmsSettingId = pp.promoCmsSettingId', 'left');
		$this->db->order_by('pt.created_at', 'desc');
		$query = $this->db->get();

		if ($isCount) {
			return $this->getOneRowOneField($query, 'cnt');

		} else {

			return $query->result_array();

		}

	}

	/**
	 * over view get player point total
	 * @param  [type] $playerId [description]
	 * @return [type] float     [description]
	 */
	public function pointTotal($playerId) {
		$this->db->select('pt.id,
			pt.created_at,
			pt.point, pt.note,
			pt.before_balance,
			pt.after_balance,
			pt.payment_account_id,
			pt.sub_wallet_id,
			pt.transaction_type,
			pt.flag,
	        pt.status,
	        pt.action'
		);

		$this->db->limit(0, 1);
		$this->db->from('point_transactions pt');
		$this->db->order_by('pt.created_at', 'desc');
		return $this->getOneRowOneField($query, 'after_balance');
	}

	public function getPlayerTotalPoints($playerId) {
		$this->db->select('
				pt.id,
				sum(pt.point) as points,
				pt.transaction_type,
				pt.action,
				pt.to_id
			');

		$this->db->from($this->tableName . ' as pt');
		$this->db->group_by('pt.transaction_type');
		$this->db->where('pt.transaction_type !=', self::DEDUCT_POINT);
		$playerId ? $this->db->where('pt.to_id', $playerId) : "";
		$query = $this->db->get();
		// var_dump($this->getMultipleRowArray($query));exit();

		// $totalDeductPts = $this->getPlayerTotalDeductedPoints($playerId)['points'];
		// var_dump($totalDeductPts);exit();
		return $this->getMultipleRowArray($query);
	}

	public function getPlayerTotalDeductedPoints($playerId) {
		$this->db->select('
				sum(pt.point) as points,
			');

		$this->db->from($this->tableName . ' as pt');
		$this->db->group_by('pt.transaction_type');
		$this->db->where('pt.transaction_type', self::DEDUCT_POINT);
		$playerId ? $this->db->where('pt.to_id', $playerId) : "";
		$query = $this->db->get();
		$res = $this->getOneRowArray($query);
		// var_dump($this->db->last_query());exit();
		return $res;
	}

}

/* End of file Point Transactions.php */
/* Location: ./application/models/point_transactions.php */
