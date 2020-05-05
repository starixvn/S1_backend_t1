<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

// require_once dirname(__FILE__) . '/modules/balance_history_module.php';
require_once dirname(__FILE__) . '/modules/big_wallet_module.php';
require_once dirname(__FILE__) . '/modules/transfer_request_model_module.php';

/**
 * it's playeraccount
 *
 * General behaviors include
 * * get sub wallet records
 * * get wallet account details
 * * get sub wallet record for a certain group
 * * get frozen amount for a certain player
 * * get main wallet of a certain player
 * * get overall total main wallet balance
 * * update the sub wallet record
 * * add new sub wallet record
 * * sync record of sub wallet
 * * increase/decrease frozen amount
 * * increase/decrease main wallet
 * * increase/decrease sub wallet
 * * get main wallet balance of a certain player
 * * declined a certain withdrawal request
 * * get withdrawal request record for a certain player
 * * cancell all withdrawal requests
 * * update withdrawal request status
 * * paid withdrawal for a certain wallet account
 * * transfer wallet amount from player to another player
 * * get balance details of a certain player
 *
 * @category Payment Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Wallet_model extends BaseModel {

	// use balance_history_module;
	use big_wallet_module;
	use transfer_request_model_module;

	const SUSPICIOUS_ALL=1;
	const SUSPICIOUS_TRANSFER_IN_ONLY=2;
	const SUSPICIOUS_TRANSFER_OUT_ONLY=3;

	const TYPE_WALLET = 'wallet';
	const TYPE_MAINWALLET = 'wallet';
	const TYPE_SUBWALLET = 'subwallet';
	const TYPE_CASHBACKWALLET = 'cashbackwallet';
	const TYPE_BATCH = 'batch';

    const REQUEST_STATUS          = 'request';
    const PENDING_REVIEW_STATUS   = 'pending_review';
    const PAY_PROC_STATUS         = 'payProc';
    const DECLINED_STATUS         = 'declined';
    const PAID_STATUS             = 'paid';
    const LOCK_API_UNKNOWN_STATUS = 'lock_api_unknown';
    #not in use
    const CHECKING_STATUS       = 'checking';
    const APPROVED_STATUS       = 'approved';

	const TYPE_OF_PLAYER_REAL = 'real';
	const TYPE_OF_PLAYER_DEMO = 'demo';

	const STATUS_LIVE = 0;

	const MAIN_WALLET_ID = 0;

	const STATUS_TRANSFER_REQUEST = 3;
	const STATUS_TRANSFER_SUCCESS = 4;
	const STATUS_TRANSFER_FAILED = 5;

	const BALANCE_ACTION_REFRESH = 1001;
	const BALANCE_ACTION_WITHDRAW = 1002;
	const BALANCE_ACTION_MOVE_TO_REAL = 1003;

	const BIG_WALLET_SUB_TYPE_REAL = 'real';
	const BIG_WALLET_SUB_TYPE_REAL_BONUS = 'real_for_bonus';
	const BIG_WALLET_SUB_TYPE_BONUS = 'bonus';
	const BIG_WALLET_SUB_TYPE_WIN_REAL = 'win_real';
	const BIG_WALLET_SUB_TYPE_WIN_BONUS = 'win_bonus';
	const BIG_WALLET_SUB_TYPE_FROZEN = 'frozen';
	const BIG_WALLET_SUB_TYPE_CASHBACK = 'cashback';

	const WALLET_NO_ENOUGH_BALANCE = 6;
	const WALLET_FAILED_TRANSFER = 7;
	const WALLET_INVALID_TRANSFER_AMOUNT = 8;

	//atomic type
	const BIG_WALLET_SUB_TYPE_ALL = array(
		self::BIG_WALLET_SUB_TYPE_REAL,
		self::BIG_WALLET_SUB_TYPE_BONUS,
		self::BIG_WALLET_SUB_TYPE_REAL_BONUS,
		// self::BIG_WALLET_SUB_TYPE_CASHBACK,
		self::BIG_WALLET_SUB_TYPE_WIN_REAL,
		self::BIG_WALLET_SUB_TYPE_WIN_BONUS,
		self::BIG_WALLET_SUB_TYPE_FROZEN,
		self::BIG_WALLET_SUB_TYPE_CASHBACK,
	);

	const WALLET_STRUCTURE = [
		'id' => 0,
		'real' => 0,
		'real_for_bonus' => 0,
		'bonus' => 0,
		// 'cashback'=> 0,
		'win_real' => 0,
		'win_bonus' => 0,
		'withdrawable' => 0,
		'frozen' => 0,
		// 'frozen_detail' =>[
		// 	'real'=>0,
		// 	'real_for_bonus'=> 0,
		// 	'bonus'=>0,
		// 	'win_real'=> 0,
		// 	'win_bonus'=> 0,
		// ],
		// 'real_limit'=>0,
		// 'bonus_limit'=>0,
		'total_nofrozen' => 0,
		'total' => 0,
	];

	const BIG_WALLET_TEMPLATE = [
		'main' => self::WALLET_STRUCTURE,
		'sub' => [
		],
		// 'frozen'=> 0,
		'last_update' => null,

		'total_real' => 0,
		'total_real_for_bonus' => 0,
		'total_bonus' => 0,
		// 'total_cashback'=>0,
		'total_win_real' => 0,
		'total_win_bonus' => 0,
		'total_withdrawable' => 0,
		'total_frozen' => 0,
		'total_nofrozen' => 0,
		//included frozen
		'total' => 0,
	];

	const BALANCE_DETAILS_TEMPLATE = [
		"frozen" => 0,
		"main_wallet" => 0,
		"sub_wallet" => [
		],
		"total_balance" => 0,
	];

	public function __construct() {
		parent::__construct();
	}

	protected $tableName = "playeraccount";

	/**
	 * detail: get wallet account details
	 *
	 * @param int $walletAccountId wallet account id field
	 * @return array
	 */
	public function getWalletAccountBy($walletAccountId) {
		$row = null;
		if ($walletAccountId) {
			$this->db->where('walletAccountId', $walletAccountId);
			$qry = $this->db->get('walletaccount');
			$row = $this->getOneRow($qry);
		}
		return $row;
	}

	/**
	 * detail: get sub wallet records
	 *
	 * @param int $playerId player account player id
	 * @param int $subwalletId player account sub wallet id
	 * @return array
	 */
	public function getSubWalletBy($playerId, $subwalletId) {
		$row = null;
		if ($playerId && $subwalletId) {
			$this->db->where('playerId', $playerId);
			$this->db->where('typeId', $subwalletId);
			$this->db->where('type', self::TYPE_SUBWALLET);
			$qry = $this->db->get($this->tableName);
			$row = $this->getOneRow($qry);
		}
		return $row;
	}

	/**
	 * detail: get sub wallet record for a certain group
	 *
	 * @param int $playerId player account player id field
	 * @return array
	 */
	public function getGroupedSubWalletBy($playerId) {
		$this->db->select('external_system.system_code game');
		$this->db->select('playeraccount.currency');
		$this->db->select_sum('playeraccount.totalBalanceAmount');
		$this->db->join('external_system', 'external_system.id = playeraccount.typeId');
		$this->db->where('playeraccount.playerId', $playerId);
		$this->db->where('playeraccount.type', self::TYPE_SUBWALLET);
		$this->db->group_by('playeraccount.typeId');
		$this->db->group_by('playeraccount.currency');
		$qry = $this->db->get($this->tableName);

		if ($qry->num_rows() > 0) {
			foreach ($qry->result_array() as $row) {
				$row['totalBalanceAmount'] = $this->utils->floorCurrencyForShow($row['totalBalanceAmount']);
				$data[] = $row;
			}
			return $data;
		}
	}

	/**
	 * detail: get frozen amount for a certain player
	 *
	 * @param int $playerId
	 * @return array
	 */
	public function getPlayerFrozenAmount($playerId) {

		return $this->utils->floorCurrencyForShow($this->getFrozenOnBigWalletById($playerId));

		// $this->db->select('frozen');
		// $this->db->where('playerId', $playerId);
		// $qry = $this->db->get('player');
		// return $this->utils->floorCurrencyForShow($this->getOneRow($qry)->frozen);
	}

	/**
	 * detail: get main wallet of a certain player
	 *
	 * @param int $playerId player account player id
	 * @return array
	 */
	public function getMainWalletBy($playerId) {
		$row = null;
		if ($playerId) {
			// $bigWallet=$this->getBigWalletByPlayerId($playerId);

			// $row= (object) ['totalBalanceAmount'=>$bigWallet['main']['total_nofrozen'], 'playerId'=> $playerId ];

			$this->db->where('playerId', $playerId);
			// $this->db->where('typeId', $subwalletId);
			$this->db->where('type', self::TYPE_MAINWALLET);
			$qry = $this->db->get($this->tableName);
			$row = $this->getOneRow($qry);
		}
		return $row;
	}

	/**
	 * detail: get overall total main wallet balance
	 *
	 * @return string
	 */
	public function totalMainWalletBalance() {
		$this->db->select('SUM(totalBalanceAmount) total');
		$this->db->from($this->tableName);
		$this->db->where('type', self::TYPE_MAINWALLET);
		$query = $this->db->get();
		return $this->getOneRowOneField($query, 'total');
	}

	/**
	 * detail: update the sub wallet record
	 *
	 * @param int $id wallet id
	 * @param int $playerId wallet player id
	 * @param int $subwalletId
	 * @param double $balanceAmount
	 * @return boolean
	 */
	public function updateSubWallet($id, $playerId, $subwalletId, $balanceAmount) {
		if ($id) {
			// $data = array("totalBalanceAmount" => $balanceAmount);
			// $this->db->where('playerAccountId', $id)->set("totalBalanceAmount", $balanceAmount);
			// $this->db->update($this->tableName, $data);
			// $success=$this->runAnyUpdateWithoutResult($this->tableName);

			$this->utils->debug_log('update sub wallet', $playerId, $subwalletId);

			$success = $this->updateSubOnBigWalletByPlayerId($playerId, $subwalletId, self::BIG_WALLET_SUB_TYPE_REAL, $balanceAmount);

			return $success;
		} else {
			$this->utils->error_log('lost player id and subWalletId', $playerId, $subwalletId);
		}

		return false;
	}

	/**
	 * detail: add new sub wallet record
	 *
	 * @param int $playerId player acount player id field
	 * @param int $subwalletId
	 * @param double $balanceAmount
	 * @return boolean
	 */
	public function insertSubWallet($playerId, $subwalletId, $balanceAmount) {
		if ($playerId && $subwalletId) {
			$data = array(
				'playerId' => $playerId,
				'totalBalanceAmount' => $balanceAmount,
				'currency' => $this->utils->getDefaultCurrency(),
				'type' => self::TYPE_SUBWALLET,
				'typeOfPlayer' => self::TYPE_OF_PLAYER_REAL,
				'typeId' => $subwalletId,
				'status' => self::STATUS_LIVE,
			);
			$success = $this->insertData($this->tableName, $data);

			//update big wallet too
			$success = $this->updateSubOnBigWalletByPlayerId($playerId, $subwalletId, self::BIG_WALLET_SUB_TYPE_REAL, $balanceAmount);
			//echo $this->db->last_query(); exit;
			// return true;
			return $success;
		} else {
			$this->utils->error_log('lost player id and subWalletId', $playerId, $subwalletId);
		}

		return false;
	}

	/**
	 * detail: sync record of sub wallet
	 *
	 * @param int $playerId
	 * @param int $subwalletId
	 * @param double $amount
	 * @return boolean
	 */
	public function syncSubWallet($playerId, $subwalletId, $balance) {
		$success = true;
		// $this->utils->debug_log(self::DEBUG_TAG, 'syncSubWallet', 'playerId', $playerId, 'subwalletId', $subwalletId, 'balance', $balance);
		$subwallet = $this->getSubWalletBy($playerId, $subwalletId);
		// $this->utils->debug_log('subwallet', $subwallet, 'playerId', $playerId, 'subWalletId', $subwalletId, 'balance', $balance);
		if ($subwallet) {
			//update
			$success = $this->updateSubWallet($subwallet->playerAccountId, $playerId, $subwalletId, $balance);
		} else {
			//insert
			$success = $this->insertSubWallet($playerId, $subwalletId, $balance);
		}

		return $success;
	}

	/**
	 * detail: increase frozen amount
	 *
	 * @param int $playerId
	 * @param double $incAmount
	 * @return boolean
	 */
	public function incFrozen($playerId, $incAmount) {
		$success = false;
		if ($playerId && $incAmount > 0) {

			// $this->db->set('frozen', 'frozen + ' . $incAmount, false);
			// $this->db->where('playerId', $playerId);
			// $success=$this->runAnyUpdateWithoutResult('player');

			$success = $this->incFrozenOnBigWallet($playerId, $incAmount);

		} else {
			$this->utils->error_log('wrong playerId or incAmount');
		}

		return $success;
	}

	/**
	 * detail: decrease frozen amount
	 *
	 * @param int $playerId
	 * @param double $decAmount
	 * @return boolean
	 */
	public function decFrozen($playerId, $decAmount) {
		$success = false;
		if ($playerId && $decAmount > 0) {

			// $this->db->set('frozen', 'frozen - ' . $decAmount, false);
			// $this->db->where('playerId', $playerId);
			// $success=$this->runAnyUpdateWithoutResult('player');

			$success = $this->decFrozenOnBigWallet($playerId, $decAmount);

		} else {
			$this->utils->error_log('wrong playerId or decAmount');
		}

		return $success;
	}

	/**
	 * detail: increase main wallet
	 *
	 * @param int $playerId
	 * @param double $incAmount
	 * @return boolean
	 */
	public function incMainWallet($playerId, $incAmount) {
		if ($playerId && $incAmount > 0) {
			$success = $this->incMainOnBigWallet($playerId, self::BIG_WALLET_SUB_TYPE_REAL, $incAmount);
			return $success;
		} else {
			$this->utils->error_log('wrong playerId or incAmount');
		}

		return false;
	}

	public function checkAndDecMainWallet($playerId, $decAmount) {
		$total = $this->getMainWalletBalance($playerId);
		if ($this->utils->compareResultFloat($total, '>=', $decAmount)) {
			//dec to main wallet
			return $this->decMainWallet($playerId, $decAmount);
		} else {
			$this->utils->error_log('playerId ' . $playerId . ' has no available balance ' . $decAmount);
		}
		return false;
	}

	/**
	 * detail: decrease main wallet
	 *
	 * @param int $playerId
	 * @param double $decAmount
	 * @return boolean
	 */
	public function decMainWallet($playerId, $decAmount) {
		if ($playerId && $decAmount > 0) {
			// $this->db->set('totalBalanceAmount', 'totalBalanceAmount-' . $decAmount, false);
			// $this->db->where('playerId', $playerId);
			// $this->db->where('type', self::TYPE_MAINWALLET);
			// // $this->db->update($this->tableName);
			// $success=$this->runAnyUpdateWithoutResult($this->tableName);

			$success = $this->decMainOnBigWallet($playerId, self::BIG_WALLET_SUB_TYPE_REAL, $decAmount);

			//if it's not 0 then update active status
			// if ($decAmount > 0) {
			// 	$this->load->model(array('player_model'));
			// 	$bal = $this->getTotalBalance($playerId);
			// 	$this->player_model->checkActivePlayer($playerId, $bal);
			// }
			return $success;
		} else {
			$this->utils->error_log('wrong playerId or decAmount');
		}

		return false;
	}

	/**
	 * detail: increase sub wallet
	 *
	 * @param int $playerId
	 * @param int $subWalletId
	 * @param double $incAmount
	 * @return boolean
	 */
	public function incSubWallet($playerId, $subWalletId, $incAmount) {
		$subwallet = $this->getSubWalletBy($playerId, $subWalletId);
		if ($subwallet && $incAmount > 0) {
			// $this->db->set('totalBalanceAmount', 'totalBalanceAmount+' . $incAmount, false);
			// $this->db->where('playerAccountId', $subwallet->playerAccountId);
			// // $this->db->update($this->tableName);
			// // return $this->db->affected_rows();
			// $success=$this->runAnyUpdateWithoutResult($this->tableName);
			$success = $this->incSubOnBigWallet($playerId, $subWalletId, self::BIG_WALLET_SUB_TYPE_REAL, $incAmount);

			return $success;
		} else {
			$this->utils->error_log('wrong subwallet or incAmount');
			return false;
		}

	}

	/**
	 * detail: decrease sub wallet
	 *
	 * @param int $playerId
	 * @param int $subWalletId
	 * @param double $decAmount
	 * @return boolean
	 */
	public function decSubWallet($playerId, $subWalletId, $decAmount) {
		$subwallet = $this->getSubWalletBy($playerId, $subWalletId);
		if ($subwallet && $decAmount > 0) {
			// $this->db->set('totalBalanceAmount', 'totalBalanceAmount-' . $decAmount, false);
			// $this->db->where('playerAccountId', $subwallet->playerAccountId);

			// $success=$this->runAnyUpdateWithoutResult($this->tableName);
			$success = $this->decSubOnBigWallet($playerId, $subWalletId, self::BIG_WALLET_SUB_TYPE_REAL, $decAmount);

			return $success;

		} else {
			$this->utils->error_log('wrong subwallet or decAmount');
			return false;
		}

	}

	/**
	 * detail: get main wallet balance of a certain player
	 *
	 * @param int $playerId wallet player id
	 * @return int
	 */
	public function getMainWalletBalance($playerId) {
		$row = $this->getMainWalletBy($playerId);
		if ($row) {
			return $this->utils->floorCurrencyForShow($row->totalBalanceAmount);
		}
		return 0;
	}

	/**
	 * @deprecated
	 */
	public function updatePlayerAccountAddCashback($player_id, $cashback, $type) {
		// get updated player account
		// $player = null;
		// if ($player_id && $cashback) {
		// 	$this->db->where('playerId', $player_id);
		// 	$this->db->where('type', $type);
		// 	$qry = $this->db->get($this->tableName);
		// 	$player = $this->getOneRow($qry);

		// 	// add cashback to totalbalanceamount
		// 	// $player_data = array(
		// 	// 	'totalBalanceAmount' => $player->totalBalanceAmount + $cashback,
		// 	// );

		// 	// set return data
		// 	$result = array(
		// 		'before_balance' => $player->totalBalanceAmount,
		// 		'after_balance' => $player->totalBalanceAmount + $cashback,
		// 	);

		// 	// update player account with cashback
		// 	$this->db->set('totalBalanceAmount', 'totalBalanceAmount+' . $cashback, false);
		// 	$this->db->where('playerId', $player_id)->where('type', $type)
		// 		->update($this->tableName);

		// 	// check if player account changed
		// 	if ($this->db->affected_rows() > 0) {
		// 		return $result;
		// 	}
		// }
		// return false;
	}

	/**
	 * detail: declined a certain withdrawal request
	 *
	 * @param int $adminUserId
	 * @param int $walletAccountId
	 * @param string $reason
	 * @param boolean $showDeclinedReason
	 * @return Boolean
	 */
	public function declineWithdrawalRequest($adminUserId, $walletAccountId, $reason = null, $showDeclinedReason = false, $declinedCategoryId = null) {

		$success = true;
		// $this->startTrans();

		// if ($this->db->affected_rows() == '1') {
		$sql = "SELECT w.playerId, w.amount, w.dwStatus FROM walletaccount w WHERE w.walletAccountId = ? AND w.dwStatus NOT IN (?,?,?) LIMIT 1";
		$query = $this->db->query($sql, array($walletAccountId, self::DECLINED_STATUS, self::CHECKING_STATUS, self::PAID_STATUS));

		if ($query && $query->num_rows() > 0) {
			$record = $query->result()[0];

			$this->utils->debug_log('record', $record);

			$playerId = $record->playerId;
			$amount = $record->amount;
			//update status
			$dataRequest = array('processedBy' => $adminUserId, 'walletAccountId' => $walletAccountId,
				'processDateTime' => $this->utils->getNowForMysql(),
				'showNotesFlag' => $showDeclinedReason, 'dwStatus' => self::DECLINED_STATUS);

			if ($this->utils->isEnabledFeature('enable_withdrawal_declined_category') && !empty($declinedCategoryId) ){
				$dataRequest['withdrawal_declined_category_id'] = $declinedCategoryId;
				$this->utils->debug_log('declinedCategoryId waletmodel ', $dataRequest['withdrawal_declined_category_id']);
			}

			//get playerId by walletAccountId
			$this->db->where('walletAccountId', $walletAccountId);
			$this->db->update('walletaccount', $dataRequest);

			$this->utils->debug_log('set to decliend', $dataRequest, 'walletAccountId', $walletAccountId);

			$this->addWalletaccountNotes($walletAccountId, $adminUserId, $reason, $record->dwStatus, self::DECLINED_STATUS);

			//clear frozen
			// $this->db->set('frozen', 'frozen-' . $amount, false)
			// 	->where('playerId', $playerId)
			// 	->update('player');

			$success = $this->moveFrozenToRealOnBigWallet($playerId, $amount);
			if (!$success) {
				$this->utils->error_log('decline withdraw condition failed', $playerId, $amount);
			}

			// $success=$this->incMainWallet($playerId, $amount);

		} else {
			$this->utils->error_log('not found or invalid status', $walletAccountId);
		}

		// $this->endTrans();
		// return $this->succInTrans();
		return $success;
	}

	/**
	 * detail: get withdrawal request record for a certain player
	 *
	 * @param int $playerId player account player id
	 * @param string $dwStatus walletaccount dwstatus field
	 * @return array
	 */
	public function getWithdrawalRequestsByPlayerId($playerId, $dwStatus = null) {

		$this->db->select('walletaccount.walletAccountId');
		$this->db->select('walletaccount.amount');
		$this->db->from('walletaccount');
		$this->db->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId');
		$this->db->where('playeraccount.playerId', $playerId);
		$this->db->where('playeraccount.type', self::TYPE_WALLET);

		if ($dwStatus) {
			$this->db->where('walletaccount.dwStatus', $dwStatus);
		}

		$query = $this->db->get();

		if ($query && $query->num_rows() > 0) {
			return $query->result_array();
		}

		return array();
	}

	/**
	 * detail: update withdrawal request status
	 *
	 * @param int $adminUserId admin id
	 * @param int $walletAccountId wallet account id
	 * @param string $toStatus new status value
	 * @param string $reason
	 * @param string $showNotesFlag wallet account show notes flag field
	 */
	public function updateWithdrawalRequestStatus($adminUserId, $walletAccountId, $toStatus, $actionlogNotes = null, $showNotesFlag = null) {
		if (strpos($toStatus, 'CS') !== 0 && $toStatus != 'payProc') {
			$this->utils->debug_log("Unable to update to a non-custom-stage status: [$toStatus]");
			return;
		}

		// ensure the original status is correct
		$this->db->select(array(
			'playeraccount.playerId',
			'walletaccount.amount',
			'playeraccount.totalBalanceAmount',
			'walletaccount.before_balance',
			'walletaccount.after_balance',
			'walletaccount.dwStatus',
		));
		$this->db->from('playeraccount');
		$this->db->join('walletaccount', 'walletaccount.playerAccountId = playeraccount.playerAccountId');
		$this->db->where('walletaccount.walletAccountId', $walletAccountId);
		$this->db->where('playeraccount.type', self::TYPE_WALLET);
		$this->db->where_not_in('walletaccount.dwStatus', array(self::APPROVED_STATUS, self::DECLINED_STATUS, self::CHECKING_STATUS, self::PAID_STATUS));
		// $this->db->limit(1);
		$this->limitOneRow();
		$query = $this->db->get();
		$record = $query->row();

		if (!$record) {
			$this->utils->debug_log("Unable to update the withdrawal request [$walletAccountId]");
			return;
		}

		$this->db->where('walletAccountId', $walletAccountId);
		$this->db->update('walletaccount', array(
			'processedBy' => $adminUserId,
			'processDateTime' => $this->utils->getNowForMysql(),
			'dwStatus' => $toStatus,
			'showNotesFlag' => $showNotesFlag,
		));

		$this->addWalletaccountNotes($walletAccountId, $adminUserId, $actionlogNotes, $record->dwStatus, $toStatus);
		return $this->db->affected_rows() > 0;
	}


	public function updateWithdrawalRequestStatusToUnknownStatus($adminUserId, $walletAccountId, $reason = null) {
		$this->utils->debug_log("===========updateWithdrawalRequestStatusToApiLocked start", $adminUserId, $walletAccountId);
		// ensure the original status is correct
		$this->db->select(array(
			'playeraccount.playerId',
			'walletaccount.amount',
			'playeraccount.totalBalanceAmount',
			'walletaccount.before_balance',
			'walletaccount.after_balance',
			'walletaccount.dwStatus',
		));
		$this->db->from('playeraccount');
		$this->db->join('walletaccount', 'walletaccount.playerAccountId = playeraccount.playerAccountId');
		$this->db->where('walletaccount.walletAccountId', $walletAccountId);
		$this->db->where('playeraccount.type', self::TYPE_WALLET);
		$this->db->where_not_in('walletaccount.dwStatus', array(self::APPROVED_STATUS, self::DECLINED_STATUS, self::CHECKING_STATUS, self::PAID_STATUS));
		// $this->db->limit(1);
		$this->limitOneRow();
		$query = $this->db->get();
		$record = $query->row();

		if (!$record) {
			$this->utils->debug_log("Unable to update the withdrawal request [$walletAccountId]");
			return;
		}

		$this->db->where('walletAccountId', $walletAccountId);
		$this->db->update('walletaccount', array(
			'processedBy' => $adminUserId,
			'processDateTime' => $this->utils->getNowForMysql(),
			'dwStatus' => self::LOCK_API_UNKNOWN_STATUS,
			// 'showNotesFlag' => $showNotesFlag,
		));

		// $this->addTransactionNotes($walletAccountId, $adminUserId, $reason, $record->dwStatus, $toStatus);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * detail: add reason to a certain user
	 *
	 * @param int $walletAccountId wallet account id field
	 * @param int $adminUserId
	 * @param string $reason
	 * @return boolean
	 */
	public function appendReason($walletAccountId, $adminUserId, $reason) {
		$this->addWalletaccountNotes($walletAccountId, $adminUserId, $reason);
	}

	/**
	 * detail: method for adding notes
	 *
	 * @param int $walletAccountId transaction note field
	 * @param int $adminUserId transaction note field
	 * @param string $note transaction note field
	 * @param string $beforeStatus transaction note field
	 * @param string $afterStatus transaction note field
	 *
	 * @return boolean
	 */
	public function addWalletaccountNotes($walletAccountId, $adminUserId, $note, $beforeStatus = '', $afterStatus = '', $urer_type = 1) {
		$this->load->model(array('walletaccount_timelog','walletaccount_notes'));
		$this->walletaccount_timelog->add($walletAccountId, $urer_type, $adminUserId, array('before_status' => $beforeStatus, 'after_status' => $afterStatus));
		$this->walletaccount_notes->add($note, Users::SUPER_ADMIN_ID, Walletaccount_notes::ACTION_LOG, $walletAccountId);
	}

	/**
	 * detail: today's processing withdraw for a certain player
	 *
	 * @param int $playerId walletaccount playerId field
	 * @return array
	 */
	public function countTodayProcessingWithdraw($playerId) {
		$date = $this->utils->getTodayForMysql();

		$this->db->select('count(walletAccountId) as cnt, sum(amount) as amount', false, null)->from('walletaccount')
			->where('playerId', $playerId)
			->where('dwStatus', self::PAY_PROC_STATUS)
			->where('date_format(processDatetime,"%Y-%m-%d")', $date);

		$row = $this->runOneRow();
		$cnt = 0;
		$amount = 0;
		if (!empty($row)) {
			$cnt = $row->cnt;
			$amount = $row->amount;
		}
		return array($cnt, $amount);
	}

	/**
	 * detail: submit withdrawal record to API
	 *
	 * @param int $adminUserId
	 * @param int $walletAccountId wallet account field
	 * @param string $reason
	 *
	 * @return boolean
	 */
	public function submitWithdrawalToAPI($adminUserId, $walletAccountId, $reason = null,
		$transaction_fee = null, $showNotesFlag = null, $withdrawApi = null) {
		$this->load->model(array('transactions', 'users'));
		$this->db->select(array(
			'walletaccount.playerId',
			'walletaccount.amount',
			// 'playeraccount.totalBalanceAmount',
			'walletaccount.before_balance',
			'walletaccount.after_balance',
		));
		$this->db->from('walletaccount');
		// $this->db->join('walletaccount', 'walletaccount.playerAccountId = playeraccount.playerAccountId');
		$this->db->where('walletaccount.walletAccountId', $walletAccountId);
		// $this->db->where('playeraccount.type', self::TYPE_WALLET);
		// $this->db->where('walletaccount.dwStatus', self::PAY_PROC_STATUS);

		$dwStatus = array('request');
		for ($i = 0; $i < CUSTOM_WITHDRAWAL_PROCESSING_STAGES; $i++) {
			array_push($dwStatus, 'CS' . $i);
		}
		$this->db->where_in('walletaccount.dwStatus', $dwStatus);

		// $this->utils->debug_log($this->db->last_query());
		$this->limitOneRow();
		$query = $this->db->get();
		$record = $query->row();

		$this->utils->printLastSQL();
        $result = array('success' => false, 'err_msg' => '');
		if ($record) {

			$playerId = $record->playerId;
			$amount = $record->amount;
			$beforeBalance = $record->before_balance;
			$afterBalance = $record->after_balance;

			# START OG-998
			$user_details = $this->users->selectUsersById($adminUserId);
			$maxApprovedAmount = $user_details['maxWidAmt'];
			$approvedAmount = $user_details['approvedWidAmt'];
			$approvedAmount = $approvedAmount + $amount;
			# OG-1902 - DEFAULT (NULL or ZERO) CAN'T WITHDRAW
			if ($maxApprovedAmount == 0 || (0 < $maxApprovedAmount && $maxApprovedAmount < $approvedAmount)) {
			    #OGP-6253
                $result ['err_msg'] = lang('pay.maxWithdrawApprovedReached');
				return $result;
			}
			# END OG-998

			//check active status
			// if ($amount > 0) {
			// 	$this->load->model(array('player_model'));
			// 	$bal = $this->getTotalBalance($playerId);
			// 	$this->player_model->checkActivePlayer($playerId, $bal);
			// }

			// update status
			$this->db->where('walletAccountId', $walletAccountId);
			$this->db->update('walletaccount', array(
				'processedBy' => $adminUserId,
				'walletAccountId' => $walletAccountId,
				'processDateTime' => $this->utils->getNowForMysql(),
				'dwStatus' => self::PAY_PROC_STATUS,
				'showNotesFlag' => $showNotesFlag,
				'paymentAPI' => $withdrawApi,
				#'transaction_id' => $transaction_id  # This will be updated during API callback
				'transaction_fee' => $transaction_fee,
			));

			$this->utils->debug_log('update withdrawal to pay proc :' . $walletAccountId);
		} else {
			$this->utils->debug_log('donot find withdrawal :' . $walletAccountId);
		}
        $result['success'] = true;
		return $result;
	}

	public function copyWithdrawal($oldWalletAccountId, $realAmount, &$newCode) {

		$newCode = $this->getRandomTransactionCode();

		$sql = <<<EOD
	insert into walletaccount(playerAccountId, playerPromoId, walletType, amount, processedBy,
processDatetime, notes, showNotesFlag, dwMethod, dwStatus,
dwDateTime, transactionType, localBankType, thirdpartyPaymentMethod, cashcard_sn,
cashcard_pin, dwIp, dwLocation, transactionCode, status,
dwCount, is_checking, before_balance, after_balance, processed_checking_time,
playerId, transaction_id, transaction_fee, paymentAPI)
	select playerAccountId, playerPromoId, walletType, ?, processedBy,
processDatetime, notes, showNotesFlag, dwMethod, dwStatus,
dwDateTime, transactionType, localBankType, thirdpartyPaymentMethod, cashcard_sn,
cashcard_pin, dwIp, dwLocation, ?, status,
dwCount, is_checking, before_balance, after_balance, processed_checking_time,
playerId, transaction_id, transaction_fee, paymentAPI from walletaccount where walletAccountId= ?
EOD;

		$this->runRawUpdateInsertSQL($sql, [$realAmount, $newCode, $oldWalletAccountId]);

		return $this->db->insert_id();

	}

	public function minusAmountOnWithdrawal($oldWalletAccountId, $realAmount) {

		$this->db->set('amount', 'amount-' . $realAmount, false)
			->where('walletAccountId', $oldWalletAccountId);

		return $this->runAnyUpdate('walletaccount');

	}

	/**
	 * detail: callback method from the API when the result is failed
	 *
	 * @param string $transactionCode
	 * @param string $message
	 * @return void
	 */
	public function withdrawalAPIReturnFailure($transactionCode, $message) {
		$this->utils->debug_log("Withdrawal API return failure: $transactionCode, $message");

		$this->db->select('dwStatus')->from('walletaccount')->where('transactionCode', $transactionCode);
		$dwStatus = $this->runOneRowOneField('dwStatus');
		if ($dwStatus == self::PAID_STATUS || $dwStatus == self::DECLINED_STATUS || $dwStatus == self::CHECKING_STATUS) {
			$this->utils->error_log("duplicate withdraw [$transactionCode]");
			return true;
		}

		# get the walletAccountId using transactionCode
		$this->db->where('transactionCode', $transactionCode);
		// $this->db->where('dwStatus', self::PAY_PROC_STATUS); # only allow update for payProc status order
		$query = $this->db->get('walletaccount');
		$row = $query->row_array();
		if (!$row || empty($row['walletAccountId'])) {
			$this->utils->error_log("No record with status=payProc found for transactionCode [$transactionCode]");
			return;
		}
		$adminUserId = $row['processedBy'];
		$walletAccountId = $row['walletAccountId'];
		$reason = $message;
		$showDeclinedReason = true;

		$success = $this->declineWithdrawalRequest($adminUserId, $walletAccountId, $reason, $showDeclinedReason);

		if ($success && $this->utils->isEnabledFeature('enable_sms_withdrawal_prompt_action_declined')) {

			$this->load->model(['cms_model', 'queue_result', 'player_model']);
			$this->load->library(["lib_queue", "sms/sms_sender"]);

			$player = $this->player_model->getPlayerInfoDetailById($row['playerId']);
			$mobileNumIsVeridied = $player['verified_phone'];

			if($mobileNumIsVeridied) {
				$isUseQueueToSend    = $this->utils->isEnabledFeature('enabled_send_sms_use_queue_server');
				$dialingCode = $player['dialing_code'];
				$mobileNum = !empty($dialingCode)? $dialingCode.'|'.$player['contactNumber'] : $player['contactNumber'];
				$smsContent = $this->cms_model->getManagerContent(Cms_model::SMS_MSG_WITHDRAWAL_DECLINE);

				if ($isUseQueueToSend) {
					$callerType = Queue_result::CALLER_TYPE_ADMIN;
					$caller = $adminUserId;
					$state  = null;
					$this->lib_queue->addRemoteSMSJob($mobileNum, $smsContent, $callerType, $caller, $state, null);
				} else {
					$this->sms_sender->send($mobileNum, $smsContent);
				}
			}
		}

		# When API failed, do not decline withdrawal request, add a note instead
		return $success;
	}

	/**
	 * detail: This function is called when withdrawal API returns success status
	 * 		   It performs the remaining transaction before changing to paid status
	 *
	 * @param string $transactionCode
	 * @return boolean
	 */
	public function withdrawalAPIReturnSuccess($transactionCode, $reason = null, $transaction_fee = null, $realAmount = null, $declineRest = true) {
		$this->utils->debug_log("Withdrawal API return success: $transactionCode");

		$this->db->select('dwStatus')->from('walletaccount')->where('transactionCode', $transactionCode);
		$dwStatus = $this->runOneRowOneField('dwStatus');
		if ($dwStatus == self::PAID_STATUS || $dwStatus == self::DECLINED_STATUS || $dwStatus == self::CHECKING_STATUS) {
			$this->utils->error_log("duplicate withdraw [$transactionCode]");
			return true;
		}

		$this->load->model(['withdraw_condition', 'transactions']);

		$this->db->select(array(
			'playeraccount.playerId',
			'walletaccount.walletAccountId',
			'walletaccount.transaction_fee',
			'walletaccount.amount',
			'walletaccount.dwStatus',
			'walletaccount.processedBy', # $adminUserId
			'playeraccount.totalBalanceAmount',
			'walletaccount.before_balance',
			'walletaccount.after_balance',
		));
		$this->db->from('playeraccount');
		$this->db->join('walletaccount', 'walletaccount.playerAccountId = playeraccount.playerAccountId');
		$this->db->where('playeraccount.type', self::TYPE_WALLET);
		// $this->db->where('dwStatus', self::PAY_PROC_STATUS); # only allow update for payProc status order
		$this->db->where('walletaccount.transactionCode', $transactionCode); # transactionCode is also unique identifier

		$this->limitOneRow();
		$query = $this->db->get();
		$record = $query->row();

		if (!$record) {
			$this->utils->error_log("No record for transactionCode [$transactionCode]");
			return false;
		}

		$playerId = $record->playerId;
		$adminUserId = $record->processedBy;
		$walletAccountId = $record->walletAccountId;
		$amount = $record->amount;
		$totalBeforeBalance = $this->getTotalBalance($playerId);

		$isSplitAmount = false;
		if ($realAmount) {
			$isSplitAmount = $realAmount < $amount;
		}

		$this->utils->debug_log('isSplitAmount', $isSplitAmount);

		if ($isSplitAmount) {
			//create new withdrawal order
			//change to new withdrawal order
			//minus old order amount
			$oldWalletAccountId = $walletAccountId;
			$this->addWalletaccountNotes($oldWalletAccountId, $adminUserId, 'pay ' . $realAmount . ' successfully', $record->dwStatus, $record->dwStatus);

			$newCode = null;
			$walletAccountId = $this->copyWithdrawal($oldWalletAccountId, $realAmount, $newCode);
			$reason .= ' create a new withdrawal ' . $newCode . ' from ' . $transactionCode;

			$this->minusAmountOnWithdrawal($oldWalletAccountId, $realAmount);

			$amount = $realAmount;

			$this->utils->debug_log('change to new withdrawal for split ', $walletAccountId, 'old', $oldWalletAccountId);

			if ($declineRest) {
				$decline_rlt = $this->declineWithdrawalRequest($adminUserId, $oldWalletAccountId, 'decline old withdrawal', true);
				$this->utils->debug_log('decline old withdrawal', $oldWalletAccountId, 'decline_rlt', $decline_rlt);
			}
		}

		# Create transaction and change the status to paid
		$transaction_id = $this->transactions->createWithdrawTransaction($playerId, $adminUserId, $walletAccountId, $amount,
			$totalBeforeBalance);
		if(empty($transaction_id)){
			$message=lang('Withdraw failed');
			$this->utils->error_log('create transaction failed', $playerId, $adminUserId, $walletAccountId, $amount);
			return false;
		}

		if (!empty($reason)) {
			$this->addWalletaccountNotes($walletAccountId, $adminUserId, $reason, $record->dwStatus, self::PAID_STATUS);
		}

		$this->utils->debug_log("Updating walletAccountId: [$walletAccountId]");

		$this->db->where('walletAccountId', $walletAccountId);
		$this->db->update('walletaccount', array(
			'processedBy' => $adminUserId,
			'processDateTime' => $this->utils->getNowForMysql(),
			'dwStatus' => self::PAID_STATUS,
			'transaction_id' => $transaction_id,
		));

		if ($this->utils->isEnabledFeature('enabled_auto_clear_withdraw_condition')) {
			// Patch for OGP-13520 incorrect transaction record canceled cashback withdrawal condition
			# Get payment detail
			$transactionDetail = $this->withdraw_condition->getWithdrawalTransactionDetail($walletAccountId);
			$applyWithdrawDatetime = isset($transactionDetail[0]['dwDateTime']) ? $transactionDetail[0]['dwDateTime'] : null;
			$this->withdraw_condition->disablePlayerWithdrawalCondition( $playerId // #1
										, Withdraw_condition::REASON_AFBW // #2
										, Withdraw_condition::DETAIL_STATUS_FINISHED_BETTING_AMOUNT_WHEN_WITHDRAW // #3
										, $applyWithdrawDatetime // #4
									);
		}

		if ($this->utils->isEnabledFeature('enable_sms_withdrawal_prompt_action_success')) {

			$this->load->model(['cms_model', 'queue_result', 'player_model']);
			$this->load->library(["lib_queue", "sms/sms_sender"]);

			$player = $this->player_model->getPlayerInfoDetailById($playerId);
			$mobileNumIsVeridied = $player['verified_phone'];

			if($mobileNumIsVeridied){
				$isUseQueueToSend = $this->utils->isEnabledFeature('enabled_send_sms_use_queue_server');
				$dialingCode = $player['dialing_code'];
				$mobileNum = !empty($dialingCode)? $dialingCode.'|'.$player['contactNumber'] : $player['contactNumber'];
				$smsContent = $this->cms_model->getManagerContent(Cms_model::SMS_MSG_WITHDRAWAL_SUCCESS);
				if ($isUseQueueToSend) {
					$callerType = Queue_result::CALLER_TYPE_ADMIN;
					$caller = $adminUserId;
					$state  = null;
					$this->lib_queue->addRemoteSMSJob($mobileNum, $smsContent, $callerType, $caller, $state, null);
				} else {
					$this->sms_sender->send($mobileNum, $smsContent);
				}
			}
		}

		# Process transaction fee
		if ($transaction_fee === null) {
			$transaction_fee = $record->transaction_fee;
		}
		if (!empty($transaction_fee) && $transaction_fee > 0) {
			$transaction = "Withdrawal";
			$this->transactions->createTransactionFee($transaction_fee, $transaction, $adminUserId, $playerId, $transaction_id, $walletAccountId);
		}

		$this->users->incApprovedWithdrawalAmount($adminUserId, $amount);
		return true;
	}

	/**
	 * detail: get wallet account using transaction code
	 *
	 * @param string $transactionCode
	 * @return array
	 */
	public function getWalletAccountByTransactionCode($transactionCode) {
		$this->db->from('walletaccount')->where('transactionCode', $transactionCode);
		// $query = $this->db->get('walletaccount');
		// return $query->row_array();
		return $this->runOneRowArray();
	}

	/**
	 * detail: get wallet account using extra_info
	 *
	 * @param string $extra_info
	 * @return array
	 */
	public function getWalletAccountByExtraInfo($extra_info) {
		$this->db->from('walletaccount')->where('extra_info', $extra_info);
		return $this->runOneRowArray();
	}

	# Stores information in the extra_info field
	public function setExtraInfoByTransactionCode($transactionCode, $extraInfoStr) {
		$this->db->where('transactionCode', $transactionCode);
		$this->db->update('walletaccount', array(
			'extra_info' => $extraInfoStr,
		));
	}

	public function requestWithdrawal($adminUserId, $walletAccountId, $reason = null, $showNotesFlag = null) {
		//ONLY FOR REQUEST
		$this->db->select(array(
			'walletaccount.amount',
			'walletaccount.before_balance',
			'walletaccount.after_balance',
			'walletaccount.dwStatus',
		));
		$this->db->from('walletaccount');
		$this->db->where('walletaccount.walletAccountId', $walletAccountId);
		$this->db->where('walletaccount.dwStatus', self::LOCK_API_UNKNOWN_STATUS);

		// $this->db->limit(1);
		$this->limitOneRow();
		$query = $this->db->get();
		$record = $query->row();

		if ($record) {
			$this->db->where('walletAccountId', $walletAccountId);
			$this->db->update('walletaccount', array(
				'processedBy' => $adminUserId,
				'walletAccountId' => $walletAccountId,
				'processDateTime' => $this->utils->getNowForMysql(),
				'dwStatus' => self::REQUEST_STATUS,
				'showNotesFlag' => $showNotesFlag
			));
		}
		else {
			return false;
		}

		return true;
	}

	/**
	 * detail: Process withdrawal for a certain user and wallet account
	 *
	 * @param int $adminUserId
	 * @param int $walletAccountId
	 * @param int $apiId
	 * @param string $reason
	 * @param string $transaction_fee
	 * @param int $showNotesFlag
	 *
	 * @return boolean
	 */
	public function payProcWithdrawal($adminUserId, $walletAccountId, $apiId = -1, $reason = null, $transaction_fee = null, $showNotesFlag = null) {
		//ONLY FOR REQUEST
		$this->db->select(array(
			'playeraccount.playerId',
			'walletaccount.amount',
			'playeraccount.totalBalanceAmount',
			'walletaccount.before_balance',
			'walletaccount.after_balance',
			'walletaccount.dwStatus',
		));
		$this->db->from('playeraccount');
		$this->db->join('walletaccount', 'walletaccount.playerAccountId = playeraccount.playerAccountId');
		$this->db->where('walletaccount.walletAccountId', $walletAccountId);
		$this->db->where('playeraccount.type', self::TYPE_WALLET);

		$dwStatus = array('request', 'approved');
		for ($i = 0; $i < CUSTOM_WITHDRAWAL_PROCESSING_STAGES; $i++) {
			array_push($dwStatus, 'CS' . $i);
		}
		$this->db->where_in('walletaccount.dwStatus', $dwStatus);

		// $this->db->limit(1);
		$this->limitOneRow();
		$query = $this->db->get();
		$record = $query->row();

		if ($record) {
			$this->db->where('walletAccountId', $walletAccountId);
			$this->db->update('walletaccount', array(
				'processedBy' => $adminUserId,
				'walletAccountId' => $walletAccountId,
				'processDateTime' => $this->utils->getNowForMysql(),
				'dwStatus' => self::PAY_PROC_STATUS,
				'showNotesFlag' => $showNotesFlag,
				'paymentAPI' => $apiId,
			));

			$this->addWalletaccountNotes($walletAccountId, $adminUserId, $reason, $record->dwStatus, self::PAY_PROC_STATUS);
		}

		return true;
	}

	/**
	 * detail: paid withdrawal for a certain wallet account
	 *
	 * @param int $adminUserId
	 * @param int $walletAccountId wallet account field
	 * @param string $reason wallet account field
	 * @param string $transaction_fee wallet account field
	 * @param int $showNotesFlag wallet account field
	 * @param boolean $force
	 */
	public function paidWithdrawal($adminUserId, $walletAccountId, $reason = null,
		$transaction_fee = null, $showNotesFlag = null, $force = false, &$message = null) {
		# LOAD MODELS AND LIBRARIES
		$this->load->model(array('transactions', 'users', 'withdraw_condition'));

		//ONLY FOR REQUEST
		$this->db->select(array(
			'playeraccount.playerId',
			'walletaccount.amount',
			'playeraccount.totalBalanceAmount',
			'walletaccount.before_balance',
			'walletaccount.after_balance',
			'walletaccount.dwStatus',
		));
		$this->db->from('playeraccount');
		$this->db->join('walletaccount', 'walletaccount.playerAccountId = playeraccount.playerAccountId');
		$this->db->where('walletaccount.walletAccountId', $walletAccountId);
		$this->db->where('playeraccount.type', self::TYPE_WALLET);

		if ($force) {
			$dwStatus = [self::PAID_STATUS];
			$this->db->where_not_in('walletaccount.dwStatus', $dwStatus);
		} else {
			$dwStatus = array('request', 'approved', 'payProc', 'pending_review', 'lock_api_unknown');
			for ($i = 0; $i < CUSTOM_WITHDRAWAL_PROCESSING_STAGES; $i++) {
				array_push($dwStatus, 'CS' . $i);
			}
			$this->db->where_in('walletaccount.dwStatus', $dwStatus);
		}

		$this->limitOneRow();
		$record = $this->runOneRow();

		$this->utils->debug_log('load walletaccount', 'status', $dwStatus, $record);

		if ($record) {

			$this->load->model(array('player_model'));

			$playerId = $record->playerId;
			$amount = $record->amount;
			$beforeBalance = $record->before_balance;
			$afterBalance = $record->after_balance;

			# START OG-998
			$user_details = $this->users->selectUsersById($adminUserId);
			$maxApprovedAmount = $user_details['maxWidAmt'];
			$approvedAmount = $user_details['approvedWidAmt'];
			$approvedAmount = $approvedAmount + $amount;
			# OG-1902 - DEFAULT (NULL or ZERO) CAN'T WITHDRAW
			if ($maxApprovedAmount == 0 || (0 < $maxApprovedAmount && $maxApprovedAmount < $approvedAmount)) {
				$this->utils->debug_log('maxApprovedAmount < approvedAmount', $maxApprovedAmount, '<', $approvedAmount);
				$message = lang('Max approved amount of logged user is less than current withdrawal amount');
				return false;
			}
			# END OG-998

			//check active status
			// if ($amount > 0) {
			// 	$bal = $this->getTotalBalance($playerId);
			// 	$this->player_model->checkActivePlayer($playerId, $bal);
			// }

			// if($this->utils->compareResultFloat($amount, '>' ,0)){
			$transaction_id = $this->transactions->createWithdrawTransaction($playerId, $adminUserId, $walletAccountId, $amount, null);
			if(empty($transaction_id)){
				$message=lang('Withdraw failed');
				$this->utils->error_log('create transaction failed', $playerId, $adminUserId, $walletAccountId, $amount);
				return false;
			}
			// }else{
			$this->utils->debug_log('ignore 0 transaction_fee');
			// }

			//enable cashback by flag
//			$enable_cashback_rlt = $this->player_model->enableCashbackByFlagOnPlayer($playerId);
//			$this->utils->debug_log('enable_cashback_rlt player_id', $playerId, 'enable_cashback_rlt', $enable_cashback_rlt);

			//echo "<pre>";print_r($rtl);exit;
			// approve
			$this->db->where('walletAccountId', $walletAccountId);
			$this->db->update('walletaccount', array(
				'processedBy' => $adminUserId,
				'walletAccountId' => $walletAccountId,
				'processDateTime' => $this->utils->getNowForMysql(),
				'dwStatus' => self::PAID_STATUS,
				// 'notes' => $reason,
				'showNotesFlag' => $showNotesFlag,
				'transaction_id' => $transaction_id,
			));

			$this->userUnlockWithdrawal($walletAccountId);

			$this->addWalletaccountNotes($walletAccountId, $adminUserId, $reason, $record->dwStatus, self::PAID_STATUS);

			if (!empty($transaction_fee)) {
				$related_trans_id = $transaction_id;
				$transaction = "Withdrawal";
				$this->transactions->createTransactionFee($transaction_fee, $transaction, $adminUserId, $playerId, $related_trans_id, $walletAccountId);
			}

			$this->users->incApprovedWithdrawalAmount($adminUserId, $amount);

		}

		return true;

	}

	/**
	 * detail: get player wallet using the player account type
	 *
	 * @param int $player_id player account field
	 * @param int $type_id player account field
	 *
	 * @return array
	 */
	public function getPlayerWalletByType($player_id, $type_id) {
		$this->db->from($this->tableName)
			->where_in('type', array(self::TYPE_MAINWALLET, self::TYPE_SUBWALLET))
			->where('playerId', $player_id)
			->where('typeId', $type_id);

		$row = $this->runOneRow();
		if (empty($row)) {
			//create new wallet
			$data = array(
				'playerId' => $player_id,
				'totalBalanceAmount' => 0,
				'currency' => $this->utils->getDefaultCurrency(),
				'type' => $type_id == self::MAIN_WALLET_ID ? self::TYPE_MAINWALLET : self::TYPE_SUBWALLET,
				'typeOfPlayer' => self::TYPE_OF_PLAYER_REAL,
				'typeId' => $type_id,
				'status' => self::STATUS_LIVE,
			);
			$this->insertData($this->tableName, $data);

			$this->db->from($this->tableName)
				->where_in('type', array(self::TYPE_MAINWALLET, self::TYPE_SUBWALLET))
				->where('playerId', $player_id)
				->where('typeId', $type_id);
			$row = $this->runOneRow();

		}

		return $row;
		// $query = $this->db->query("SELECT * FROM playeraccount where type IN ('wallet', 'subwallet') and playerId = '" . $player_id . "' and typeId = '" . $game_id . "'");

		// return $query->row_array();
	}

	/**
	 * detail: transfer wallet amount from player to another player
	 *
	 * note: only main to sub or sub to main
	 *
	 * @param int $gamePlatromId external system field
	 * @param int $player_id wallet account field
	 * @param int $transfer_from
	 * @param int $transfer_to
	 * @param double $amount wallet account field
	 * @param int $external_transaction_id external system field
	 * @param int $walletType
	 * @param string $message
	 * @param double $originTransferAmount
	 *
	 * @return boolean
	 */
	public function transferWalletAmount($gamePlatformId, $player_id, $transfer_from, $transfer_to,
		$amount, $external_transaction_id = null, $walletType = null, &$message = null,
		$originTransferAmount = null, $ignore_promotion_check = false, $reason = null, $is_manual_adjustment = null, $process_user_id = null, &$err_code) {
		$this->load->model('transactions');
		$transactionType = null;
		if ($transfer_to == self::MAIN_WALLET_ID) {
			$transactionType = Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET;
		} else if ($transfer_from == self::MAIN_WALLET_ID) {
			$transactionType = Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET;
		}

		if ($gamePlatformId && !empty($transactionType)) {

			$this->load->model(array('player_model', 'promorules', 'external_system', 'transactions', 'http_request', 'game_provider_auth'));

			$sys = $this->external_system->getSystemById($gamePlatformId);
			$systemName = $sys->system_code;

			$note = null;
			$beforeBalance = null;
			$afterBalance = null;
			$playerAccountId = null;
			$note_reason = sprintf('<i>Reason:</i> %s <br>',$reason);
			if ($transactionType == Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET) {
				// save http_request (cookies, referer, user-agent)
				$note = 'transfer from main wallet to subwallet (' . $systemName . '), amount is ' . $amount . ' , playerId is ' . $player_id;
				$note = (trim($reason) != '')  ? ($note_reason . sprintf('<i>Normal Note:</i> %s <br>',$note)) : $note;
				$wallet = $this->getPlayerWalletByType($player_id, $transfer_to);
				$walletIncId = $wallet->playerAccountId;
				$playerAccountId = $walletIncId;

				$mainWallet = $this->getPlayerWalletByType($player_id, $transfer_from);
				$walletDecId = $mainWallet->playerAccountId;
				//only for main wallet
				// $beforeBalance = $mainWallet->totalBalanceAmount;
				// $afterBalance = $mainWallet->totalBalanceAmount - $amount;

			} else if ($transactionType == Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET) {
				// save http_request (cookies, referer, user-agent)
				$note = 'transfer from subwallet (' . $systemName . ') to main wallet , amount is ' . $amount . ' , playerId is ' . $player_id;
				$note = (trim($reason) != '')  ? ($note_reason . sprintf('<i>Normal Note:</i> %s <br>',$note)) : $note;
				$mainWallet = $this->getPlayerWalletByType($player_id, $transfer_to);
				$walletIncId = $mainWallet->playerAccountId;
				//only for main wallet
				// $beforeBalance = $mainWallet->totalBalanceAmount;
				// $afterBalance = $mainWallet->totalBalanceAmount + $amount;

				$wallet = $this->getPlayerWalletByType($player_id, $transfer_from);
				$walletDecId = $wallet->playerAccountId;
				$playerAccountId = $walletDecId;

			}

			$totalBeforeBalance = null; // $this->getTotalBalance($player_id);
			// $this->utils->debug_log('player_id', $player_id, 'totalBeforeBalance', $totalBeforeBalance);

			if( $this->utils->compareResultFloat($amount, "<=", 0)) {
				$this->utils->error_log('keep always positive number', $player_id, $amount, $transfer_to, $transfer_from);
				$amount = 0;
			}

			$rlt = $this->transactions->createTransferWalletTransaction($player_id, $transactionType, $playerAccountId,
				$gamePlatformId, $walletIncId, $walletDecId, $amount, $external_transaction_id,
				$note, $totalBeforeBalance, $beforeBalance, $afterBalance, $transfer_from, $transfer_to,
				$walletType, $originTransferAmount, $ignore_promotion_check, null, false, $is_manual_adjustment, $process_user_id, $err_code);
			$transferTransId = $rlt;
			$this->utils->debug_log('createTransferWalletTransaction', $player_id, 'rlt', $rlt);

			return $rlt;
		}

		return false;

	}

	/**
	 * detail: check if the wallet account balance is zero
	 *
	 * @param int $playerId wallet account field
	 * @return boolean
	 */
	public function isPlayerWalletAccountZero($playerId) {

		$totalBalance = $this->utils->roundCurrencyForShow($this->getTotalBalance($playerId));
		return $totalBalance <= 0.01;
		//check player wallet account (main/sub)
		// $playerMainWalletBalance = $this->getMainWalletBalance($playerId);

		// if ($playerMainWalletBalance != 0) {
		// 	return false;
		// } else {
		// 	$subwallet = $this->getAllPlayerAccountByPlayerId($playerId);
		// 	foreach ($subwallet as $key) {
		// 		if ($key['totalBalanceAmount'] != 0) {
		// 			return false;
		// 		}
		// 	}
		// }
		// return true;
	}

	/**
	 * detail: get all player accounts by player id
	 *
	 * @param int $playerId external system player id
	 * @return array
	 */
	public function getAllPlayerAccountByPlayerId($player_id) {

		$this->utils->debug_log("Sync wallet Player id ===================>", $player_id);

		$this->syncAllWalletsByGamePlatformAndPlayerId($player_id);
		$sql = <<<EOD
SELECT p.*, sys.system_code as game, sys.category, sys.amount_float
FROM external_system as sys
join playeraccount AS p on p.typeId=sys.id
WHERE p.type = ?
AND p.playerId = ?
and sys.status=?
EOD;


		$query = $this->db->query($sql, array('subwallet', $player_id, self::STATUS_NORMAL));

		$rows = $query->result_array();

		if(empty($rows)){
		    return [];
        }

		//remove disabled list
		$wallets = array();
		foreach ($rows as $row) {
			if (!$this->utils->isDisabledApi($row['typeId'])) {
				$wallets[] = $row;
			}
		}

        $game_wallet_settings = $this->operatorglobalsettings->getGameWalletSettings();

        $sort = 0;
        foreach($wallets as $key => $wallet_data){
            $wallet_id = $wallet_data['typeId'];
            if(!isset($game_wallet_settings[$wallet_id])){
                continue;
            }

            $sort = ($game_wallet_settings[$wallet_id]['sort'] > $sort) ? $game_wallet_settings[$wallet_id]['sort'] : $sort;
            $wallet_data['sort'] = $game_wallet_settings[$wallet_id]['sort'];
            $wallet_data['enabled_on_desktop'] = $game_wallet_settings[$wallet_id]['enabled_on_desktop'];
            $wallet_data['enabled_on_mobile'] = $game_wallet_settings[$wallet_id]['enabled_on_mobile'];

            $wallets[$key] = $wallet_data;
        }

        foreach($wallets as $key => $wallet_data){
            $sort++;

            $wallet_data['sort'] = (isset($wallet_data['sort'])) ? $wallet_data['sort'] : $sort;
            $wallet_data['enabled_on_desktop'] = (isset($wallet_data['enabled_on_desktop'])) ? $wallet_data['enabled_on_desktop'] : TRUE;
            $wallet_data['enabled_on_mobile'] = (isset($wallet_data['enabled_on_mobile'])) ? $wallet_data['enabled_on_mobile'] : TRUE;

            $wallets[$key] = $wallet_data;
        }

        uasort($wallets, function($entry1, $entry2){
            if ($entry1['sort'] == $entry2['sort']) {
                return 0;
            }
            return ($entry1['sort'] < $entry2['sort']) ? -1 : 1;
        });

        $is_mobile = $this->utils->is_mobile();
        $subwallets = [];
        foreach($wallets as $wallet_data){
            if($is_mobile){
                if(!$wallet_data['enabled_on_mobile']) continue;
            }else{
                if(!$wallet_data['enabled_on_desktop']) continue;
            }

            $subwallets[] = $wallet_data;
        }

		return $subwallets;
	}

	/**
	 * detail: sum up all sub wallet record in a certain player
	 *
	 * @param int $playerId player account field
	 * @return double
	 */
	public function sumSubWalletBy($playerId) {
		$amount = 0;
		if ($playerId) {
			$this->db->select('sum(totalBalanceAmount) as amount')
				->from($this->tableName)
				->where('playerId', $playerId)
			// ->where('typeId', $subwalletId)
				->where('type', self::TYPE_SUBWALLET);

			$amount = $this->runOneRowOneField('amount');
		}
		return $amount;
	}

	/**
	 * detail: get wallet total balance of a certain player
	 *
	 * @param int $playerId wallet account field
	 * @param boolean $addFrozen
	 *
	 * @return double
	 */
	public function getTotalBalance($playerId, $addFrozen = true) {
		($this->utils->isEnabledFeature('show_total_balance_without_pending_withdraw_request ')) ? $addFrozen = false : "";

		//load it from big wallet
		$bigWallet = $this->getBigWalletByPlayerId($playerId);

		if ($addFrozen) {
			return $bigWallet['total'];
		} else {
			return $bigWallet['total_nofrozen'];
		}

		// $this->load->model(array('player_model'));
		// $sumBal = 0;
		// $manager = $this->utils->loadGameManager();

		// $balance_from_api = $this->config->item('balance_from_api');
		// if ($balance_from_api) {
		// 	//get balance from api
		// 	$balances = $manager->queryBalanceOnAllPlatformsByPlayerId($playerId);
		// 	$subwallets = array();
		// 	foreach ($balances as $apiId => $apiRlt) {
		// 		$sumBal += floatval($apiRlt['balance']);
		// 		// $subwallets[$apiId] = $this->utils->roundCurrencyForShow($apiRlt['balance']);
		// 	}
		// } else {
		// 	//get from wallet
		// 	$sumBal = $this->sumSubWalletBy($playerId);
		// }

		// if ($addFrozen) {
		// 	$frozen = $this->player_model->getPendingBalanceById($playerId);
		// 	$this->utils->debug_log('addFrozen', $addFrozen, 'playerId', $playerId, 'frozen', $frozen);
		// 	if ($frozen > 0) {
		// 		$sumBal += $frozen;
		// 	}
		// }

		// $mainBal = $this->getMainWalletBalance($playerId);
		// $this->utils->debug_log('playerId', $playerId, 'mainBal', $mainBal, 'sumBal', $sumBal);
		// return $mainBal + $sumBal;
	}

	const PLAYERACCOUNT_TYPE_BATCH = 'batch';
	const PLAYERACCOUNT_TYPE_WALLET = 'wallet';
	const PLAYERACCOUNT_TYPE_SUBWALLET = 'subwallet';
	const PLAYERACCOUNT_TYPE_CASHBACKWALLET = 'cashbackwallet';
	const PLAYERACCOUNT_TYPE_AFFILIATE = 'affiliate';
	const DEFAULT_PLAYERACCOUNT_BATCHPASSWORD = 0;
	const DEFAULT_PLAYERACCOUNT_STATUS = 0;

	/**
	 * detail: sync all wallet account records
	 *
	 * @param int $playerId wallet account field
	 * @param double $mainBalance
	 * @param string $currency
	 * @return void
	 */
	public function syncAllWallet($playerId, $mainBalance, $currency) {

		return $this->updateMainOnBigWalletByPlayerId($playerId, 'real', $mainBalance);

		// $walletData = array(
		// 	'typeOfPlayer' => 'real',
		// 	'currency' => $currency,
		// 	'batchPassword' => '',
		// 	'status' => self::DEFAULT_PLAYERACCOUNT_STATUS,
		// 	'playerId' => $playerId,
		// 	'totalBalanceAmount' => $mainBalance,
		// );

		// $playerAccounts = array();

		// $this->db->select('playerAccountId')->from('playeraccount')
		// 	->where('playerId', $playerId)
		// 	->where('type', self::PLAYERACCOUNT_TYPE_WALLET);

		// if (!$this->runExistsResult()) {
		// 	$playerAccounts[] = array_merge($walletData, array(
		// 		'type' => self::PLAYERACCOUNT_TYPE_WALLET,
		// 		'typeId' => 0,
		// 	));
		// } else {
		// 	//update
		// 	$this->db->where('playerId', $playerId)
		// 		->where('type', self::PLAYERACCOUNT_TYPE_WALLET)
		// 		->update('playeraccount', array('totalBalanceAmount' => $mainBalance));
		// }

		// $this->db->select('playerAccountId')->from('playeraccount')
		// 	->where('playerId', $playerId)
		// 	->where('type', self::PLAYERACCOUNT_TYPE_CASHBACKWALLET);

		// if (!$this->runExistsResult()) {
		// 	$playerAccounts[] = array_merge($walletData, array(
		// 		'type' => self::PLAYERACCOUNT_TYPE_CASHBACKWALLET,
		// 		'typeId' => null, # NOTE: NULL IN PLAYER, 0 IN BATCH
		// 	));
		// }

		// if (!empty($playerAccounts)) {
		// 	$this->db->insert_batch('playeraccount', $playerAccounts);
		// }

		// $list = $this->utils->getAllCurrentGameSystemList();

		// foreach ($list as $gameId) {
		// 	$this->syncSubWallet($playerId, $gameId, 0);

		// 	// $playerAccounts[] = array_merge($walletData, array(
		// 	// 	'type' => self::PLAYERACCOUNT_TYPE_SUBWALLET,
		// 	// 	'typeId' => $gameId,
		// 	// ));
		// }

	}

	const DEBUG_TAG = 'Wallet_model';

	/**
	 * detail: get newest withdrawals
	 *
	 * @param array $rows
	 * @return array
	 */
	public function selectNewestWithdrawals($rows) {
		$today = date("Y-m-d H:i:s");
		$sql = 'SELECT * FROM walletaccount  WHERE processDatetime <= ? AND transactionType = ?  order by processDatetime DESC LIMIT ? ';
		$query = $this->db->query($sql, array($today, 'withdrawal', $rows));
		return array(
			'total' => $query->num_rows(),
			'data' => $query->result_array(),
		);
	}

	/*
		 * detail: get sub wallet KV
		 *
		 * @return array
	*/
	public function getSubwalletKV() {
		$this->db->from('external_system')->where('status', self::STATUS_NORMAL)
			->where('system_type', SYSTEM_GAME_API);
		$rows = $this->runMultipleRow();

		return $this->convertRowsToKV($rows, 'id', 'system_code', false, true);
	}

	/*
		 * detail: get sub wallet Map
		 *
		 * @return array
	*/
	public function getSubwalletMap() {
		$this->db->from('external_system')->where('status', self::STATUS_NORMAL)
			->where('system_type', SYSTEM_GAME_API);
		$rows = $this->runMultipleRow();
		$rlt = array();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$rlt[$row->id] = $row->system_code;
			}
		}
		return $rlt;
	}

	/**
	 * detail: get a sub wallet for certain player account
	 *
	 * @param int $player_id
	 * @param int $game_id
	 */
	public function getPlayerAccountBySubWallet($player_id, $game_id) {
		if ($game_id == 0) {
			//main wallet
			$total = $this->getMainWalletTotalNofrozenOnBigWalletByPlayer($player_id);
		} else {
			//get from big wallet
			$total = $this->getSubWalletTotalNofrozenOnBigWalletByPlayer($player_id, $game_id);
		}

		return ['totalBalanceAmount' => $total];
		// $query = $this->db->query("SELECT * FROM playeraccount where type IN ('wallet', 'subwallet') and playerId = '" . $player_id . "' and typeId = '" . $game_id . "'");

		// return $query->row_array();
	}

	/**
	 * detail: sync wallet account records
	 *
	 * @return string
	 */
	public function syncAllWalletsByGamePlatformAndPlayerId($playerId) {
		//sync wallet by external system
		$sql = <<<EOD
select playerId, group_concat(typeId) as gameIds from playeraccount
where type='subwallet'
and playerId=?
group by playerId
EOD;

		$cnt = 0;
		$failedIds = array();
		$rows = $this->runRawSelectSQL($sql, [$playerId]);
		$gameApiArr = $this->utils->getAllCurrentGameSystemList();
		if(!empty($rows)){
			foreach ($rows as $row) {
				$playerId = $row->playerId;
				$gameIdStr = $row->gameIds;
				$gameIdArr = explode(',', $gameIdStr);
				$diffArr = array_diff($gameApiArr, $gameIdArr);
				if (!empty($diffArr)) {
					foreach ($diffArr as $subwalletId) {
						if ($this->safeCreateSubWallet($playerId, $subwalletId)) {
							$cnt++;
						} else {
							$failedIds[] = $playerId . '-' . $subwalletId;
						}
					}
				}
			}
		}

		return $this->utils->debug_log("total player count", count($rows), "count insert subwallet", $cnt, "failedIds", $failedIds);
	}

	/**
	 * detail: sync wallet account records
	 *
	 * @return string
	 */
// 	public function syncAllWalletsByGamePlatform() {
// 		//sync wallet by external system
// 		$sql = <<<EOD
// select playerId, group_concat(typeId) as gameIds from playeraccount
// where type='subwallet'
// group by playerId
// EOD;

// 		$cnt = 0;
// 		$failedIds = array();
// 		$rows = $this->runRawSelectSQL($sql);
// 		$gameApiArr = $this->utils->getAllCurrentGameSystemList();
// 		foreach ($rows as $row) {
// 			$playerId = $row->playerId;
// 			$gameIdStr = $row->gameIds;
// 			$gameIdArr = explode(',', $gameIdStr);
// 			$diffArr = array_diff($gameApiArr, $gameIdArr);
// 			if (!empty($diffArr)) {
// 				foreach ($diffArr as $subwalletId) {
// 					if ($this->safeCreateSubWallet($playerId, $subwalletId)) {
// 						$cnt++;
// 					} else {
// 						$failedIds[] = $playerId . '-' . $subwalletId;
// 					}
// 				}
// 			}
// 		}

// 		return $this->utils->debug_log("total player count", count($rows),
// 			"count insert subwallet", $cnt, "failedIds", $failedIds);

// 	}

	/**
	 * detail: create sub wallet safely
	 *
	 * @param int $playerId player account field
	 * @param int $subwalletId
	 * @return array
	 */
	public function safeCreateSubWallet($playerId, $subwalletId) {
		$subwallet = $this->getSubWalletBy($playerId, $subwalletId);
		// $this->utils->debug_log('subwallet', $subwallet, 'playerId', $playerId, 'subWalletId', $subwalletId, 'balance', $balance);
		if ($subwallet) {
			//update
			// $this->updateSubWallet($subwallet->playerAccountId, $balance);
			return true;
		} else {
			//insert
			return $this->insertSubWallet($playerId, $subwalletId, 0);
		}
	}

	public function getAvailableBalancePlayerNames($gamePlatformId) {
		$sql = <<<EOD
select distinct player.username
from playeraccount join player on playeraccount.playerId=player.playerId
where playeraccount.typeId=? and playeraccount.type=?
and playeraccount.totalBalanceAmount>0
EOD;

		$rlt = array();
		$rows = $this->runRawSelectSQL($sql, array($gamePlatformId, self::TYPE_SUBWALLET));
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$rlt[] = $row->username;
			}
		}

		return $rlt;
	}

	/**
	 * detail: get balance details of a certain player
	 *
	 * @param int $playerId wallet player id
	 * @return string
	 */
	public function getBalanceDetails($playerId) {
		//load it from big wallet
		$bigWallet = $this->getBigWalletByPlayerId($playerId);
		$balanceDetails = $this->convertBigWalletToBalanceDetails($bigWallet);
		$balanceDetails["big_wallet"] = $bigWallet;
		return $balanceDetails;

		// $this->load->model(array('player_model'));
		// $sumBal = 0;
		// $manager = $this->utils->loadGameManager();

		// $balance_from_api = $this->config->item('balance_from_api');
		// if ($balance_from_api) {
		// 	//get balance from api
		// 	$balances = $manager->queryBalanceOnAllPlatformsByPlayerId($playerId);
		// 	$subwallets = array();
		// 	foreach ($balances as $apiId => $apiRlt) {
		// 		$sumBal += floatval($apiRlt['balance']);
		// 		// $subwallets[$apiId] = $this->utils->roundCurrencyForShow($apiRlt['balance']);
		// 	}
		// } else {
		// 	$sumBal = $this->sumSubWalletBy($playerId);
		// }

		// // if ($addFrozen) {
		// $frozen = $this->player_model->getPendingBalanceById($playerId);
		// // $this->utils->debug_log('playerId', $playerId, 'frozen', $frozen);
		// if ($frozen > 0) {
		// 	$sumBal += $frozen;
		// }
		// // }

		// $mainBal = $this->getMainWalletBalance($playerId);
		// // $this->utils->debug_log('playerId', $playerId, 'mainBal', $mainBal, 'sumBal', $sumBal);
		// $totalBal = $mainBal + $sumBal;
		// //get from wallet
		// $subWalletInfo = $this->getGroupedSubWalletBy($playerId);
		// return array(
		// 	"main_wallet" => $mainBal,
		// 	"sub_wallet" => $subWalletInfo,
		// 	"frozen" => $frozen,
		// 	"total_balance" => $totalBal,
		// 	"big_wallet"=> $this->getBigWalletByPlayerId($playerId),
		// );
	}

	/**
	 * detail: get both main and sub balance of players
	 *
	 * @return array
	 */
	public function getAvailableSubOrMainBalancePlayers() {
		$sql = <<<EOD
select distinct player.username, player.playerId, player.password
from playeraccount join player on playeraccount.playerId=player.playerId
where playeraccount.totalBalanceAmount>0.01
EOD;

		// $rlt = array();
		$rows = $this->runRawSelectSQL($sql);
		// if (!empty($rows)) {
		// 	foreach ($rows as $row) {
		// 		$rlt[] = $row;
		// 	}
		// }

		return $rows;
	}

	/**
	 * detail: get player id from wallet account table
	 *
	 * @param int $walletAccountId wallet account field
	 * @return array
	 */
	public function getPlayerIdFromWalletAccount($walletAccountId) {
		$this->db->select(array(
			'playeraccount.playerId',
		));
		$this->db->from('playeraccount');
		$this->db->join('walletaccount', 'walletaccount.playerAccountId = playeraccount.playerAccountId');
		$this->db->where('walletaccount.walletAccountId', $walletAccountId);

		return $this->runOneRowOneField('playerId');
	}

	const RECORD_TYPE_BEFORE = 1;
	const RECORD_TYPE_AFTER = 2;

	// const ACTION_TYPE_DEPOSIT = 1;
	// const ACTION_TYPE_WITHDRAW = 2;
	// const ACTION_TYPE_PROMOTION = 3;
	// const ACTION_TYPE_TRANSFER_FROM_MAIN_TO_SUB = 4;
	// const ACTION_TYPE_TRANSFER_FROM_SUB_TO_MAIN = 5;
	// const ACTION_TYPE_CASHBACK = 6;

	const USER_TYPE_PLAYER = 1;
	const USER_TYPE_AFF = 2;
	// const USER_TYPE_ADMIN = 3;
	const USER_TYPE_AGENT = 4;

	/**
	 * detail: get all withdrawals of a certain player
	 *
	 * @param int $playerId player account field
	 * @param string $limit
	 * @param string $offset
	 * @param array $search
	 * @return array
	 */
	public function getAllWithdrawalsWLimit($playerId, $limit, $offset, $search, $count = false) {
		//$query = $this->db->query("SELECT * FROM playeraccount as pa inner join walletaccount as wa on pa.playerAccountId = wa.playerAccountId where playerId = '" . $player_id . "' AND transactionType = 'withdrawal' AND type = 'wallet'");

		$this->db->select('walletaccount.dwDateTime,
    					   walletaccount.processDatetime,
    					   walletaccount.transactionCode,
    					   walletaccount.dwMethod,
    					   walletaccount.amount,
    					   walletaccount.notes,
    					   walletaccount.showNotesFlag,
    					   walletaccount.transactionCode,
    					   walletaccount.transactionType,
    					   walletaccount.dwStatus,
    					   walletaccount.is_checking,
    					   walletaccount.playerAccountId,
    					   walletaccount.walletAccountId,
						  ')
			->from('playeraccount')
			->join('walletaccount', 'walletaccount.playerAccountId = playeraccount.playerAccountId', 'left')
			->order_by('walletaccount.walletAccountId', 'desc');
		if (!$count)
			$this->db->limit($limit, $offset);
		$this->db->where('playeraccount.playerId', $playerId);
		$this->db->where('walletaccount.transactionType', 'withdrawal');
		$this->db->where('playeraccount.type', 'wallet');

		if (!empty($search['from'])) {
			$this->db->where("dwDatetime BETWEEN '" . $search['from'] . "' AND '" . $search['to'] . "'");
		}

		$query = $this->db->get();
		$result = $query->result_array();
		$this->load->model('transaction_notes');
		$i = 0;
		foreach ($result as $queryi) {
			$result[$i]['note'] = $this->transaction_notes->getNotesByTransaction(Transaction_notes::TRANS_WITHDRAWAL, $queryi['walletAccountId']);
			$i++;
		}
		return $result;
	}

	const MIN_WALLET_AMOUNT = 0.01;

	/**
	 *
	 * detail: get player available balances
	 *
	 * @return array
	 */
	public function getAvailBalancePlayerList() {
		$this->db->distinct()->select('playeraccount.playerId, player.username, player.big_wallet')->from('playeraccount')
			->join('player', 'player.playerId=playeraccount.playerId')
			->where('player.deleted_at is null', null, false)
			->where('player.blocked', 0)
			->where_in('playeraccount.type', array(self::TYPE_MAINWALLET, self::TYPE_SUBWALLET))
			->where('playeraccount.totalBalanceAmount >=', self::MIN_WALLET_AMOUNT);

		// select count(*),sum(totalBalanceAmount) from playeraccount where typeId=1 and totalBalanceAmount>=0.01

		return $this->runMultipleRow();
	}

	/**
	 * detail: clear duplicate record from wallet
	 *
	 * @param string $dupTable
	 * @return int
	 */
	public function clearDupWallet($dupTable) {

		$this->db->from($dupTable)->order_by('playerId,type,typeId');
		$rows = $this->runMultipleRowArray();
		$cnt = 0;
		if (!empty($rows)) {
			$keepArr = array();
			foreach ($rows as $row) {
				# code...
				$cnt++;
				$amount = $row['totalBalanceAmount'];
				$key = $row['player'] . '-' . $row['type'] . '-' . $row['typeId'];
				if ($amount > 0) {
					//use it
					$keepArr[$key] = $row['playerAccountId'];
				} else if (isset($keepArr[$key]) && $keepArr[$key] > $row['playerAccountId']) {
					//keep min
					$keepArr[$key] = $row['playerAccountId'];
				}
			}
			$keepIds = array_values($keepArr);

			foreach ($rows as $row) {
				if (!in_array($row['playerAccountId'], $keepIds)) {
					//delete it and set status
					$this->db->where('playerAccountId', $row['playerAccountId']);
					$this->db->delete('playeraccount');
					//1=deleted
					$this->db->set('status', 1)->where('playerAccountId', $row['playerAccountId'])
						->update($dupTable);
				}
			}
		}

		return $cnt;
	}

	/**
	 * detail: get random transaction code
	 *
	 * @return string
	 */
	public function getRandomTransactionCode() {

		return $this->getTransactionCode('walletaccount', 'transactionCode', true, 'W');

		// $seed = str_split('abcdefghijklmnopqrstuvwxyz'
		// 	. '0123456789'); // and any other characters
		// shuffle($seed); // probably optional since array_is randomized; this may be redundant
		// foreach (array_rand($seed, 14) as $k) {
		// 	$transaction_code .= $seed[$k];
		// }

		// $transaction_code = 'W'.random_string('numeric',16);
		// return $transaction_code;
	}

    public function getTransactionCode($tableName, $fldName, $needUnique = true, $prefix = null) {
        if($this->utils->isEnabledFeature('enable_change_withdrawal_transaction_ID_start_with_date')){
            $random_length = $this->utils->getConfig('get_secureid_random_length');
            $prefix = $prefix.date('Ymd');  //ex: W20180330
            $transactionCode = parent::getSecureId($tableName, $fldName,$needUnique,$prefix, $random_length);
            return $transactionCode;
        }else{
            return parent::getSecureId($tableName, $fldName, $needUnique, $prefix);
        }
    }

	public function checkAndSyncAllWallets($playerId) {

		$defCurrency = $this->utils->getDefaultCurrency();
		$defBal = 0;

		$walletData = array(
			'typeOfPlayer' => 'real',
			'currency' => $defCurrency,
			'batchPassword' => '',
			'status' => self::DEFAULT_PLAYERACCOUNT_STATUS,
			'playerId' => $playerId,
			'totalBalanceAmount' => $defBal,
		);

		$this->db->select('playerAccountId')->from('playeraccount')
			->where('playerId', $playerId)
			->where('type', self::TYPE_MAINWALLET)
			->where('typeId', self::MAIN_WALLET_ID);

		if (!$this->runExistsResult()) {
			$data = array_merge($walletData, array(
				'type' => self::TYPE_MAINWALLET,
				'typeId' => self::MAIN_WALLET_ID,
			));
			$this->insertData('playeraccount', $data);
		}

		$list = $this->utils->getAllCurrentGameSystemList();

		foreach ($list as $gameId) {
			$subwallet = $this->getSubWalletBy($playerId, $gameId);
			// $this->utils->debug_log('subwallet', $subwallet, 'playerId', $playerId, 'subWalletId', $subwalletId, 'balance', $balance);
			if (!$subwallet) {
				//insert
				$this->insertSubWallet($playerId, $gameId, $defBal);
			}
		}

		return true;
	}

	/**
	 * detail: create all wallet by player id
	 * ONLY FOR REGISTER
	 *
	 * @param int $playerId player account player id
	 * @return boolean
	 */
	public function initCreateAllWalletForRegister($playerId) {

		$defCurrency = $this->utils->getDefaultCurrency();
		$defBal = 0;

		$walletData = array(
			'typeOfPlayer' => 'real',
			'currency' => $defCurrency,
			'batchPassword' => '',
			'status' => self::DEFAULT_PLAYERACCOUNT_STATUS,
			'playerId' => $playerId,
			'totalBalanceAmount' => $defBal,
		);

		$this->db->select('playerAccountId')->from('playeraccount')
			->where('playerId', $playerId)
			->where('type', self::TYPE_MAINWALLET)
			->where('typeId', self::MAIN_WALLET_ID);

		if (!$this->runExistsResult()) {
			$data = array_merge($walletData, array(
				'type' => self::TYPE_MAINWALLET,
				'typeId' => self::MAIN_WALLET_ID,
			));
			$this->insertData('playeraccount', $data);
		}

		$list = $this->utils->getAllCurrentGameSystemList();

		foreach ($list as $gameId) {
			$subwallet = $this->getSubWalletBy($playerId, $gameId);
			// $this->utils->debug_log('subwallet', $subwallet, 'playerId', $playerId, 'subWalletId', $subwalletId, 'balance', $balance);
			if (!$subwallet) {
				//insert
				$this->insertSubWallet($playerId, $gameId, $defBal);
			}
		}

		return true;
	}

	/**
	 * detail: update main wallet record
	 *
	 * @param int $playerId wallet player id
	 * @param double $balanceAmount wallet total balance amount
	 * @return string
	 */
	public function updateMainWallet($playerId, $balanceAmount) {
		if ($playerId) {
			$this->db->where('playerId', $playerId)
				->where('type', self::TYPE_MAINWALLET)
				->where('typeId', self::MAIN_WALLET_ID)
				->set("totalBalanceAmount", $balanceAmount);
			$success = $this->runAnyUpdateWithoutResult('playeraccount');

			return $success;
		}

		return null;
	}

	/**
	 * detail: add new withdrawal
	 *
	 * @param array $walletAccountData
	 * @param array $localBankWithdrawalDetails
	 * @param int $playerId wallet player id
	 *
	 * @return int
	 */
	public function newWithdrawal($walletAccountData, $localBankWithdrawalDetails, $playerId) {
		if (!isset($walletAccountData['browser_user_agent'])) {
			$walletAccountData['browser_user_agent'] = $this->agent->agent_string();
		}

		$result = $this->db->insert('walletaccount', $walletAccountData);
		$walletAccountId = $this->db->insert_id();

		if(empty($walletAccountId)) {
			$this->utils->error_log("Error: failed to insert into walletaccount", $walletAccountData);
			return false;
		}

		# Format of transaction code: Wxxxxxxxx, unique
		$transactionCode = $this->getRandomTransactionCode(); // 'W'.sprintf("%'.08d", $walletAccountId).random_string('numeric',8);
		$this->db->where('walletAccountId', $walletAccountId);
		$this->db->update('walletaccount', array('transactionCode' => $transactionCode,'processDateTime' => $this->utils->getNowForMysql()));

		if ($localBankWithdrawalDetails) {
			$localBankWithdrawalDetails['walletAccountId'] = $walletAccountId;
			$this->db->insert('localbankwithdrawaldetails', $localBankWithdrawalDetails);
		}

		$success = $this->incFrozenOnBigWallet($playerId, $walletAccountData['amount']);
		if (!$success) {
			$this->utils->error_log('freeze failed', $playerId, $walletAccountData);
			return false;
		}

		$this->load->model(array('wallet_model'));
		//record balance history
		$this->recordPlayerAfterActionWalletBalanceHistory(Wallet_model::BALANCE_ACTION_WITHDRAW,
			$playerId, null, null, $walletAccountData['amount'], null, null,
			null, $walletAccountId, null);

		return $walletAccountId;

	}

	/**
	 * detail: just translate the wallet
	 *
	 * @param int $wallet_type
	 * @return string
	 */
	public function translateWallet($wallet_type) {
		$name = null;
		$map = $this->getSubwalletMap();
		if ($wallet_type == '0') {
			//main wallet
			$name = lang('Main Wallet');
		} else {
			if (isset($map[$wallet_type])) {
				$name = lang($map[$wallet_type]);
			}
		}
		return $name;
	}

	/**
	 * Get payment method details
	 *
	 * $paymentMethod int
	 * $walletAccountId int
	 * @return	$array
	 */
	public function getPaymentMethodDetails($paymentMethod, $walletAccountId) {
		//echo $paymentMethod;exit();
		switch ($paymentMethod) {
		case 1:
			$this->db->select('otcpaymentmethoddetails.*,
								   otcpaymentmethod.accountName,
								   otcpaymentmethod.accountNumber,
								   otcpaymentmethod.transactionFee,
								   otcpaymentmethod.bankName')
				->from('otcpaymentmethoddetails')
				->join('otcpaymentmethod', 'otcpaymentmethod.otcPaymentMethodId = otcpaymentmethoddetails.otcPaymentMethodId');
			$this->db->where('otcpaymentmethoddetails.walletAccountId', $walletAccountId);
			break;
		case 2:
			$this->db->select('*')->from('paypalpaymentmethoddetails');
			$this->db->where('paypalpaymentmethoddetails.walletAccountId', $walletAccountId);
			break;
		case 5:
			$this->db->select('*')->from('netellerdepositdetails');
			$this->db->where('netellerdepositdetails.walletAccountId', $walletAccountId);
			break;
		default:
			return false;
			break;
		}

		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			// foreach ($query->result_array() as $row) {
			// if (isset($row['transactionDatetime'])) {
			// $row['transactionDatetime'] = mdate('%M %d, %Y', strtotime($row['transactionDatetime']));
			// }
			// $data[] = $row;
			// }

			return $query->result_array();
		}
		return false;
	}

	/**
	 * Will get getWithdrawalDeclinedTransactionDetail
	 *
	 * @param $transactionId - int
	 * @return array
	 */
	public function getWithdrawalDeclinedTransactionDetail($walletAccountId) {
		$this->load->model(array('transaction_notes','walletaccount_notes'));

		$this->db->select('walletaccount.*,
						   SUM(walletaccount.amount + playerpromo.bonusAmount) AS playerTotalBalanceAmount,
						   player.email,
						   player.createdOn,
						   player.username AS playerName,
						   adminusers.username AS processedByAdmin,
						   playerdetails.*,
						   playeraccount.totalBalanceAmount AS currentBalAmount,
						   playeraccount.currency AS currentBalCurrency,
						   playerpromo.playerPromoId,
						   playerpromo.playerId AS promoPlayerId,
						   playerpromo.promoStatus AS playerPromoStatus,
						   playerpromo.bonusAmount AS playerPromoBonusAmount,
						   paymentmethod.paymentMethodId,
						   paymentmethod.paymentMethodName,
						   withdrawaldetails.accountName,
						   withdrawaldetails.accountID,
						   withdrawaldetails.email AS accountEmail,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   playerbankdetails.bankAccountFullName AS detailsBankAccountFullName,
						   playerbankdetails.bankAccountNumber AS detailsBankAccountNumber,
						   playerbankdetails.branch AS detailsBranch,
						   playerbankdetails.bankAddress AS detailsBankAddress,
						   playerbankdetails.phone AS detailsBankPhone,
						   promorules.promorulesId,
						   promorules.bonusAmount,
						   banktype.bankName AS banktypeBankName,
						   transactions.id AS  trans_id,
						   (SELECT amount FROM transactions WHERE related_trans_id = trans_id AND transaction_type = 3 ) as transaction_fee
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId', 'left')
			->join('player', 'player.playerId = playeraccount.playerId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('adminusers', 'adminusers.userId = walletaccount.processedBy', 'left')
			->join('paymentmethod', 'paymentmethod.paymentMethodId = walletaccount.dwMethod', 'left')
			->join('withdrawaldetails', 'withdrawaldetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
		// ->join('localbankwithdrawaldetails', 'localbankwithdrawaldetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerbankdetails', 'playerbankdetails.playerBankDetailsId = walletaccount.player_bank_details_id', 'left')
			->join('playerpromo', 'playerpromo.playerpromoId = walletaccount.playerPromoId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->join('banktype', 'banktype.bankTypeId = playerbankdetails.bankTypeId', 'left')
			->join('transactions', 'transactions.id = walletaccount.transaction_id', 'left');

		$this->db->where('walletaccount.walletAccountId', $walletAccountId);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['firstName'] = $row['firstName'] ? ucwords($row['firstName']) : '';
				$row['lastName'] = $row['lastName'] ? ucwords($row['lastName']) : '';
				// $row['dwDateTime'] = mdate('%M %d, %Y %H:%i:%s', strtotime($row['dwDateTime']));
				// $row['createdOn'] = mdate('%M %d, %Y', strtotime($row['createdOn']));
				// $row['birthdate'] = mdate('%M %d, %Y', strtotime($row['birthdate']));
				// $row['processDatetime'] = mdate('%M %d, %Y %H:%i:%s', strtotime($row['processDatetime']));
				$row['playerName'] = ucwords($row['playerName']);
				$row['paymentmethoddetails'] = $this->getPaymentMethodDetails($row['paymentMethodId'], $row['walletAccountId']);

				$row['subwalletBalanceAmount'] = $this->getSubWalletBalance($row['playerId']);
				$row['totalBalance'] = $row['currentBalAmount'];
				foreach ($row['subwalletBalanceAmount'] as &$subwallet) {
					$row['totalBalance'] += $subwallet['totalBalanceAmount'];
					$subwallet['totalBalanceAmount'] = $this->utils->formatCurrencyNoSym($subwallet['totalBalanceAmount']);
				}
				$row['totalBalance'] = $this->utils->formatCurrencyNoSym($row['totalBalance']);

				$row['cashbackwalletBalanceAmount'] = 0; // $this->getPlayerCashbackWalletBalance($row['playerId']);
				$row['playerPromoActive'] = null; //$this->getPlayerPromoActive($row['playerId']);
				$row['bankName'] = lang($row['bankName']);
				$row['walletAccountInternalNotes'] = $this->formatPaymentNotes($this->walletaccount_notes->getWalletAccountNotes(Walletaccount_notes::INTERNAL_NOTE, $walletAccountId));
				$row['walletAccountExternalNotes'] = $this->formatPaymentNotes($this->walletaccount_notes->getWalletAccountNotes(Walletaccount_notes::EXTERNAL_NOTE, $walletAccountId));
				$row['currentBalCurrency'] = $this->utils->getCurrentCurrency()['currency_code'];
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 *
	 * overview: depoist and withdrawal Notes
	 *
	 * detail: to format the transaction notes based on the given parameter array
	 *
	 * @param array $transactionNotes
	 * @return string
	 *
	 */

	private function formatPaymentNotes($paymentNotes) {
		$noteString = '';
		if(!empty($paymentNotes)){
			foreach ($paymentNotes as $aNote) {
				if($aNote['status_name'] == null){
					$aNote['status_name'] = lang('no status');
				}
				$noteString .= sprintf("[%s] %s_%s: %s\n", $aNote['created_at'], $aNote['creater_name'], $aNote['status_name'], $aNote['content']);
			}
		}
		$this->utils->debug_log("Formatted Payment Notes: ", $noteString);
		return $noteString;
	}


	/**
	 * Will get player subwallet balance
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getSubWalletBalance($player_id) {
		$qry = "SELECT totalBalanceAmount AS totalBalanceAmount,typeId FROM playeraccount WHERE TYPE IN ('subwallet') AND playerId = '" . $player_id . "'";
		$query = $this->db->query("$qry");

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * remove all duplicate player account
	 * @return array [total count, failed count]
	 */
// 	public function remove_dup_playeraccount() {

// 		$cnt = 0;
// 		$failed_cnt = 0;

// 		$sql = <<<EOD
// drop table if exists dup_playeraccount
// EOD;

// 		$this->db->query($sql);

// 		$sql = <<<EOD
// create table dup_playeraccount
// select type, typeId, playerId from playeraccount
// group by type, typeId, playerId
// having count(playerAccountId)>1
// EOD;

// 		$this->db->query($sql);

// 		$this->db->query("create index idx_dup on dup_playeraccount(type, typeId, playerId)");
// 		// $this->db->query("create index idx_id on dup_playeraccount(playerAccountId)");

// 		$sql = <<<EOD
// select playeraccount.* from dup_playeraccount join playeraccount
// on dup_playeraccount.type=playeraccount.type and dup_playeraccount.typeId=playeraccount.typeId
// and dup_playeraccount.playerId=playeraccount.playerId
// order by playeraccount.playerId, playeraccount.type, playeraccount.typeId
// EOD;
// 		$rows = $this->runRawSelectSQLArray($sql);

// 		$playerChanged = true;
// 		foreach ($rows as $row) {
// 			$playerId = $row['playerId'];
// 			$type = $row['type'];
// 			$typeId = $row['typeId'];
// 			$totalBalanceAmount = $row['totalBalanceAmount'];

// 			if ($playerChanged) {
// 				$oldPlayerId = $playerId;
// 			}
// 		}

// 	}

	/**
	 *
	 * detail: get player available balances
	 *
	 * @return array
	 */
	public function getAvailBalanceActivePlayerList($customApi=null) {
		$this->db->distinct()->select('playeraccount.playerId as playerId, player.username as username')->from('playeraccount')
			->join('player', 'player.playerId=playeraccount.playerId')
			->where('player.deleted_at is null', null, false)
		// ->where('player.blocked', 0)
			->where_in('playeraccount.type', array(self::TYPE_SUBWALLET))
			->where('playeraccount.totalBalanceAmount <', 1)
			->where('playeraccount.totalBalanceAmount >=', 0);
			if(!empty($customApi)){
				$this->db->where('playeraccount.typeId = ', $customApi);
			}
		// select count(*),sum(totalBalanceAmount) from playeraccount where typeId=1 and totalBalanceAmount>=0.01

		return $this->runMultipleRowArray();
	}

	public function checkUserWithdrawalAmount($adminUserId, $walletAccountId) {

		$this->load->model(['users']);

		$this->db->select(array(
			'walletaccount.playerId',
			'walletaccount.amount',
		));
		$this->db->from('walletaccount')->where('walletaccount.walletAccountId', $walletAccountId);

		$amount = floatval($this->runOneRowOneField('amount'));

		$this->db->select_sum('amount')->from('walletaccount')
		    ->where('dwStatus', self::APPROVED_STATUS);

		$todayApprovedAmount=floatval($this->runOneRowOneField('amount'));

		$user_details = $this->users->selectUsersById($adminUserId);
		$maxDailyApprovedAmount = floatval($user_details['maxWidAmt']); //daily
		$maxAmountEverySingle = floatval($user_details['singleWidAmt']); //single withdrawal

		if($maxDailyApprovedAmount<=0){
			$maxDailyApprovedAmount=PHP_INT_MAX;
		}
		if($maxAmountEverySingle<=0){
			$maxAmountEverySingle=PHP_INT_MAX;
		}

		$this->utils->debug_log('compare withdrawal amount', $adminUserId, $walletAccountId,
			$maxDailyApprovedAmount, $todayApprovedAmount, $amount, $maxAmountEverySingle);

		return $maxAmountEverySingle>=$amount && $maxDailyApprovedAmount >= $todayApprovedAmount+$amount;

	}

	public function lockWithdrawal($walletAccountId) {
		$this->db->set('lock_manually_opt', self::DB_TRUE)->where('walletAccountId', $walletAccountId);

		return $this->runAnyUpdate('walletaccount');
	}

	public function unlockWithdrawal($walletAccountId) {
		$this->db->set('lock_manually_opt', self::DB_FALSE)->where('walletAccountId', $walletAccountId);

		return $this->runAnyUpdate('walletaccount');
	}

	public function isLockedForManual($walletAccountId, $adminUserId) {

		$this->db->select('locked_user_id')->from('walletaccount')->where('walletAccountId', $walletAccountId);

		$row = $this->runOneRowArray();

		return !empty($row['locked_user_id']) && $row['locked_user_id'] != $adminUserId;
	}

	public function isAvailableWithdrawal($walletAccountId, &$status) {
		$this->db->from('walletaccount')->where('walletAccountId', $walletAccountId);

		$status = $this->runOneRowOneField('dwStatus');

		return !in_array($status, [self::PAID_STATUS, self::CHECKING_STATUS, self::DECLINED_STATUS]);
	}

	public function userLockWithdrawal($walletAccountId, $userId) {
		$this->db->set('locked_user_id', $userId)->where('walletAccountId', $walletAccountId);

		return $this->runAnyUpdate('walletaccount');
	}

	public function checkWithdrawLocked($walletAccountId) {
		$this->db->from('walletaccount')->where('walletAccountId', $walletAccountId);

		$lockedUserId = $this->runOneRowOneField('locked_user_id');

		return $lockedUserId;
	}

	public function userUnlockWithdrawal($walletAccountId) {
		$this->db->set('locked_user_id', null)->set('lock_manually_opt', self::DB_FALSE)->where('walletAccountId', $walletAccountId);

		return $this->runAnyUpdate('walletaccount');
	}

	public function batchUnlockWithdrawTransaction($walletAccountIds) {
		if (!empty($walletAccountIds)) {
			$this->db->set('locked_user_id', null);
			$this->db->where_in('walletAccountId', $walletAccountIds);
			$this->db->update('walletaccount');
		}
	}

	public function getWithdrawalApprovedTransactionDetail($walletAccountId) {
		$this->db->select('walletaccount.*,
						   SUM(walletaccount.amount + playerpromo.bonusAmount) AS playerTotalBalanceAmount,
						   player.email,
						   player.createdOn,
						   player.username AS playerName,
						   adminusers.username AS processedByAdmin,
						   playerdetails.*,
						   playeraccount.totalBalanceAmount AS currentBalAmount,
						   playeraccount.currency AS currentBalCurrency,
						   playerpromo.playerPromoId,
						   playerpromo.playerId AS promoPlayerId,
						   playerpromo.promoStatus AS playerPromoStatus,
						   playerpromo.bonusAmount AS playerPromoBonusAmount,
						   paymentmethod.paymentMethodId,
						   paymentmethod.paymentMethodName,
						   withdrawaldetails.accountName,
						   withdrawaldetails.accountID,
						   withdrawaldetails.email AS accountEmail,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   playerbankdetails.bankAccountFullName,
						   playerbankdetails.bankAccountNumber,
						   playerbankdetails.branch,
						   promorules.promorulesId,
						   promorules.promoName,
						   banktype.bankTypeId,
						   banktype.bankName AS bank_name,
						   banktype.bank_code AS bankCode,
						   adminusers.username AS processedByAdmin,
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId', 'left')
			->join('player', 'player.playerId = playeraccount.playerId', 'left')
			->join('playerpromo', 'playerpromo.playerPromoId = walletaccount.playerPromoId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('adminusers', 'adminusers.userId = walletaccount.processedBy', 'left')
			->join('paymentmethod', 'paymentmethod.paymentMethodId = walletaccount.dwMethod', 'left')
			->join('withdrawaldetails', 'withdrawaldetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
		// ->join('localbankwithdrawaldetails', 'localbankwithdrawaldetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerbankdetails', 'playerbankdetails.playerBankDetailsId = walletaccount.player_bank_details_id', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->join('banktype', 'banktype.bankTypeId = playerbankdetails.bankTypeId', 'left');

		$this->db->where('walletaccount.walletAccountId', $walletAccountId);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['firstName'] = $row['firstName'] ? ucwords($row['firstName']) : '';
				$row['lastName'] = $row['lastName'] ? ucwords($row['lastName']) : '';
				// $row['dwDateTime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dwDateTime']));
				// $row['createdOn'] = mdate('%M %d, %Y', strtotime($row['createdOn']));
				// $row['processDatetime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['processDatetime']));
				$row['playerName'] = ucwords($row['playerName']);
				$row['paymentmethoddetails'] = $this->getPaymentMethodDetails($row['paymentMethodId'], $row['walletAccountId']);
				$row['subwalletBalanceAmount'] = $this->getSubWalletBalance($row['playerId']);
				$row['totalBalance'] = $row['currentBalAmount'];
				foreach ($row['subwalletBalanceAmount'] as &$subwallet) {
					$row['totalBalance'] += $subwallet['totalBalanceAmount'];
					$subwallet['totalBalanceAmount'] = $this->utils->formatCurrency($subwallet['totalBalanceAmount']);
				}
				$row['totalBalance'] = $this->utils->formatCurrency($row['totalBalance']);

				// $row['subwalletBalanceAmount'] = $this->getPlayerSubWalletBalance($row['playerId']);
				// $row['subwalletBalanceAmountAG'] = $this->getPlayerSubWalletBalanceAG($row['playerId']);
				$row['cashbackwalletBalanceAmount'] = 0; // $this->getPlayerCashbackWalletBalance($row['playerId']);
				$row['playerPromoActive'] = null; // $this->getPlayerPromoActive($row['playerId']);
                $row['bankName'] = !empty($row['bank_name']) ? lang($row['bank_name']) : lang($row['bankName']);
				$row['isCrypto'] = 0;

				$banktype = $this->banktype->getBankTypeById($row['bankTypeId']);
				if($this->utils->isCryptoCurrency($banktype)){
					$row['isCrypto'] = 1;
				}

				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	public function getWithdrawalApprovedTransaction($sort_by, $limit, $offset = 0) {

		$sortPlayerLvl = '';
		if (isset($sort_by['playerLevel'])) {
			$playerLevelVal = $sort_by['playerLevel'];
			$sortPlayerLvl = $playerLevelVal;
		}

		$sortPaymentMethod = '';
		if (isset($sort_by['paymentMethod'])) {
			$paymentMethodVal = $sort_by['paymentMethod'];
			$sortPaymentMethod = $paymentMethodVal;
		}

		$sortDateRangeValueStart = '';
		if (isset($sort_by['dateRangeValueStart'])) {
			$dateRangeValueStartVal = $sort_by['dateRangeValueStart'];
			$sortDateRangeValueStart = $dateRangeValueStartVal;
		}

		$sortDateRangeValueEnd = '';
		if (isset($sort_by['dateRangeValueEnd'])) {
			$dateRangeValueEndVal = $sort_by['dateRangeValueEnd'];
			$sortDateRangeValueEnd = $dateRangeValueEndVal;
		}

		$this->db->select('walletaccount.*,
						   player.playerId,
						   player.username,
						   playerdetails.firstname,
						   playerdetails.lastname,
						   playeraccount.currency,
						   playeraccount.totalBalanceAmount as mainwalletBalanceAmount,
						   player.playerId,
						   paymentmethod.paymentMethodName,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   playerbankdetails.bankAccountFullName,
						   playerbankdetails.bankAccountNumber,
						   playerbankdetails.branch,
						   promorules.promorulesId,
						   promorules.promoName,
						   playerpromo.playerpromoId,
						   playerpromo.bonusAmount,
						   banktype.bankName,
						   adminusers.username AS processedByAdmin,
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId', 'left')
			->join('player', 'player.playerId = playeraccount.playerId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId')
			->join('paymentmethod', 'paymentmethod.paymentMethodId = walletaccount.dwMethod', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
		// ->join('localbankwithdrawaldetails', 'localbankwithdrawaldetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerbankdetails', 'playerbankdetails.playerBankDetailsId = walletaccount.player_bank_details_id', 'left')
			->join('playerpromo', 'playerpromo.playerpromoId = walletaccount.playerPromoId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->join('banktype', 'banktype.bankTypeId = playerbankdetails.bankTypeId', 'left')
			->join('adminusers', 'adminusers.userId = walletaccount.processedBy');

		$this->db->where('walletaccount.transactionType', $sort_by['transactionType']);
		$this->db->where('walletaccount.dwStatus', $sort_by['dwStatus']);

		if ($sortPlayerLvl != '') {
			$this->db->where('vipsetting.vipSettingId', $sortPlayerLvl);
		}
		if ($sortPaymentMethod != '') {
			$this->db->where('walletaccount.dwMethod', $sortPaymentMethod);
		}
		if ($sortDateRangeValueStart != '') {
			$this->db->where('walletaccount.dwDateTime >= ', $sortDateRangeValueStart . ' 00:00:00');
		}
		if ($sortDateRangeValueEnd != '') {
			$this->db->where('walletaccount.dwDateTime <= ', $sortDateRangeValueEnd . ' 23:59:59');
		}

		if ($limit != null) {
			$this->db->limit($limit, $offset);
		}

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['subwalletBalanceAmount'] = $this->getPlayerSubWalletBalance($row['playerId']);
				$row['subwalletBalanceAmountAG'] = ''; // $this->getPlayerSubWalletBalanceAG($row['playerId']);
				$row['cashbackwalletBalanceAmount'] = ''; // $this->getPlayerCashbackWalletBalance($row['playerId']);
				//$row['playerPromoActive'] = $this->getPlayerPromoActive($row['playerId']);
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	public function getMaxBalancePlayerList($customApi=null, $max_bal=1, $min_bal=null) {
		$this->db->distinct()->select('playeraccount.playerId as playerId, player.username as username, playeraccount.typeId')->from('playeraccount')
			->join('player', 'player.playerId=playeraccount.playerId')
			// ->where('player.deleted_at is null', null, false)
		// ->where('player.blocked', 0)
			->where_in('playeraccount.type', array(self::TYPE_SUBWALLET))
			->where('playeraccount.totalBalanceAmount <', $max_bal);
		if(!empty($customApi)){
			$this->db->where('playeraccount.typeId = ', $customApi);
		}
		if($min_bal!==null){
			$this->db->where('playeraccount.totalBalanceAmount >', $min_bal);
		}else{
			$this->db->where('playeraccount.totalBalanceAmount >', 0);
		}
		// select count(*),sum(totalBalanceAmount) from playeraccount where typeId=1 and totalBalanceAmount>=0.01

		return $this->runMultipleRowArray();
	}

	public function importBalanceAsDeposit($playerId, $amount, $withdraw_condition_times, $adjust_time, &$message){

		$success=false;
		$this->load->model(['sale_order', 'transactions']);

		$defaultCurrency = $this->utils->getConfig('default_currency');
		$requestDateTime=$this->utils->formatDateTimeForMysql(new DateTime($adjust_time));
		$approvedDateTime=$this->utils->formatDateTimeForMysql(new DateTime($adjust_time));
		//create sale_orders
		$orderId=$this->sale_order->createSimpleSaleOrder($playerId, $amount,
			'create ' . $amount . ' from importer', $requestDateTime);

		if(!empty($orderId)){
			//approve sale order
			$show_reason_to_player=false;
			$reason='from importer';
			$success=$this->sale_order->approveSaleOrder($orderId, $reason, $show_reason_to_player);
			//update approve time
			$success=$this->sale_order->adjustOrderDateTime($orderId, $requestDateTime, $approvedDateTime);
		}else{
			$success=false;
		}

		return $success;
	}

	public function updateWalletaccountBankdetails() {
    	$query = $this->db->query("
            SELECT
                a.walletAccountId,
                c.bankAccountFullName,
                c.bankAccountNumber,
                d.bankName,
                c.bankAddress,
                c.city as bankCity,
                c.province as bankProvince,
                c.branch as bankBranch
            FROM walletaccount a
            LEFT JOIN localbankwithdrawaldetails b ON a.walletAccountId = b.walletAccountId
            LEFT JOIN playerbankdetails c  ON b.playerBankDetailsId = c.playerBankDetailsId
            LEFT JOIN banktype d  ON c.bankTypeId = d.bankTypeId
        ");

       $result =  $query->result_array();

       if (!empty($result)) {
            $this->db->update_batch('walletaccount', $result, 'walletAccountId');
            return true;
        }
        return false;
    }

	public function getPlayerBalanceInDB($api=null, $playerId) {

		$this->db->select('playeraccount.playerId as playerId, player.username as username, SUM(playeraccount.totalBalanceAmount) AS balance')->from('playeraccount')
				->join('player', 'player.playerId=playeraccount.playerId')
				->where_in('playeraccount.type', array(self::TYPE_SUBWALLET))
				->where('playeraccount.totalBalanceAmount >', 0);

		if (!empty($api)){
			$this->db->where('playeraccount.typeId = ', $api);
		}

		if (!empty($playerId)){
			$this->db->where('playeraccount.playerId = ', $playerId);
		}

		return $this->runOneRowArray();
	}

	public function getRequestSecureId($walletAccountId){
		$this->db->select('transactionCode');
		$this->db->from('walletaccount');
		$this->db->where('walletAccountId', $walletAccountId);
		$query = $this->db->get();
		return $this->getOneRowOneField($query, 'transactionCode');
	}

	public function getTransactionCodeByTransactionId($transaction_id){
		$this->db->select('transactionCode');
		$this->db->where('transaction_id', $transaction_id);
		$this->db->from('walletaccount');
		return $this->runOneRowOneField('transactionCode');
	}

	/**
	 * detail: get wallet account details
	 *
	 * @param int $walletAccountId wallet account id field
	 * @return array
	 */
	public function getWalletAccountObject($walletAccountId) {
		if (!empty($walletAccountId)) {
			$this->db->from('walletaccount')->where('walletAccountId', $walletAccountId);
			// $qry = $this->db->get('walletaccount');
			// $row = $this->getOneRow($qry);
			return $this->runOneRowArray();
		}
		return null;
	}

	public function getWalletAccountStatus($walletAccountId) {
		$row = null;
		if ($walletAccountId) {
			$this->db->select('dwStatus')->from('walletaccount')->where('walletAccountId', $walletAccountId);
			return $this->runOneRowOneField('dwStatus');
		}
		return $row;
	}

	/**
	 * Get wallet account note by id
	 * @param  int $walletAccountId
	 * @return string
	 *
	 * Created By : Frans Eric Dela Cruz (frans.php.ph) 11-26-2018
	 */
	public function getWalletAccountNote($walletAccountId) {
		$row = null;
		if ($walletAccountId) {
			$this->db->select('notes')->from('walletaccount')->where('walletAccountId', $walletAccountId);
			return $this->runOneRowOneField('notes');
		}
		return $row;
	}
	/**
	 *
     * @param  string $mode last_one_hour/available/all
	 * @param  double $minAmount
	 * @return array
	 */
	public function getPlayerListUnbuffered($mode, $minAmount=self::MIN_WALLET_AMOUNT) {

		if($mode=='available'){
			$this->db->distinct()->select('playeraccount.playerId, player.username, playeraccount.typeId as subWalletId, playeraccount.totalBalanceAmount as amount')
			    ->from('playeraccount')
				->join('player', 'player.playerId=playeraccount.playerId')
				->where('player.deleted_at is null', null, false)
				->where('player.blocked', 0)
				->where('playeraccount.type', self::TYPE_SUBWALLET)
				->where('playeraccount.totalBalanceAmount >=', $minAmount);
		}else if($mode=='all'){
			$this->db->distinct()->select('playeraccount.playerId, player.username, playeraccount.typeId as subWalletId, playeraccount.totalBalanceAmount as amount')
			    ->from('playeraccount')
				->join('player', 'player.playerId=playeraccount.playerId')
				->where('player.deleted_at is null', null, false)
				->where('player.blocked', 0)
				->where('playeraccount.type', self::TYPE_SUBWALLET);
		}else if($mode=='last_one_hour'){
			$d=new DateTime();
			$now=$d->format('YmdH');
			$d->modify('-1 hour');
			$lastHour=$d->format('YmdH');
			//load from last hour total hour
			$this->db->distinct()->select('playeraccount.playerId, player.username, playeraccount.typeId as subWalletId, playeraccount.totalBalanceAmount as amount')
			    ->from('playeraccount')
				->join('player', 'player.playerId=playeraccount.playerId')
				->join('total_player_game_hour', 'total_player_game_hour.player_id=playeraccount.playerId')
				->where('player.deleted_at is null', null, false)
				->where('player.blocked', 0)
				->where('playeraccount.type', self::TYPE_SUBWALLET)
				->where('playeraccount.totalBalanceAmount >=', $minAmount)
				->where('total_player_game_hour.date_hour >=', $lastHour)
				->where('total_player_game_hour.date_hour <=', $now);
		}

		return $this->runMultipleRowArrayUnbuffered();
	}

	public function getTransIdByWalletAccountId($walletAccountId) {
		if (!empty($walletAccountId)) {
			$this->db->from('transaction_id')->where('walletAccountId', $walletAccountId);
			return $this->runOneRowOneField('transaction_id');
		}
		return null;
	}

	public function getWalletAccountInfoById($walletAccountId) {
		if (!empty($walletAccountId)) {
			$this->db->from('walletaccount')->where('walletAccountId', $walletAccountId);
			// $qry = $this->db->get('walletaccount');
			// $row = $this->getOneRow($qry);
			return $this->runOneRowArray();
		}
		return null;
	}

	public function getPlayerNamesByGamePlatformId($gamePlatformId) {

		$this->db->select('player.username');
		$this->db->from('playeraccount');
		$this->db->join('player', 'player.playerId = playeraccount.playerId');
		$this->db->where('playeraccount.typeId', $gamePlatformId);
		$this->db->where('playeraccount.type', self::TYPE_SUBWALLET);
		$query = $this->db->get();
		$rows =   $query->result_array();
		$playerNames = [];
		if (!empty($rows)) {
			foreach ($rows as $row) {
				array_push($playerNames, $row['username']) ;
			}
		}

		return $playerNames;
	}

	public function getPlayerIdsByCreatedOn($from_date, $to_date) {
		$this->db->select('playeraccount.playerId, player.total_total_nofrozen, sum(playeraccount.totalBalanceAmount) totalBalanceAmount');
		$this->db->from('playeraccount');
		$this->db->join('player','playeraccount.playerId = player.playerId');
		$this->db->where("player.createdOn >= '$from_date'");
		$this->db->where("player.createdOn <= '$to_date'");
		$this->db->where("(playeraccount.type = 'wallet' or playeraccount.type = 'subwallet')");
		$this->db->group_by('playeraccount.playerId');
		$this->db->having('(ROUND(sum(playeraccount.totalBalanceAmount),2)-player.total_total_nofrozen) <>', 0);
		return $this->runMultipleRowArray();
	}

}

///END OF FILE//////////////
