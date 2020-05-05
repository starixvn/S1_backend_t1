<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Transactions
 *
 * General behaviors include
 * * add a record for new transaction
 * * get agent bonuses for a certain player
 * * create admin to agent transaction
 * * create agent to sub-agent transaction
 * * create sub agent to agent transaction
 * * create agent to player transaction
 * * create deposit bonus/deposit/withdraw transaction
 * * create referral transaction for a certain player
 * * create transfer wallet transaction for a a certain player
 * * create adjustment for a certain transaction
 * * get total daily deposit of a certain payment account id
 * * pay monthly earnings of a certain player
 * * get total deposit withdrawal bonus cashback of a certain player
 *
 * @category Payment Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Transactions extends BaseModel {

	protected $tableName = 'transactions';

	protected $idField = 'id';

	# TRANSACTION_TYPE
	const DEPOSIT = 1;
	const WITHDRAWAL = 2;
	const FEE_FOR_PLAYER = 3;
	const FEE_FOR_OPERATOR = 4;

	const TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET = 5;
	const TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET = 6;
	const DEPOSIT_TO_SUB_WALLET=self::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET;
	const WITHDRAW_FROM_SUB_WALLET=self::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET;

	const MANUAL_ADD_BALANCE = 7;
	const MANUAL_SUBTRACT_BALANCE = 8;
	const ADD_BONUS = 9;
	const SUBTRACT_BONUS = 10;

	const MANUAL_ADD_BALANCE_ON_SUB_WALLET = 11;
	const MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET = 12;
	const MANUALLY_DEPOSIT_TO_SUB_WALLET=self::MANUAL_ADD_BALANCE_ON_SUB_WALLET;
	const MANUALLY_WITHDRAW_FROM_SUB_WALLET=self::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET;

	const AUTO_ADD_CASHBACK_TO_BALANCE = 13;
	const MEMBER_GROUP_DEPOSIT_BONUS = 14;
	const PLAYER_REFER_BONUS = 15;
	const AFFILIATE_MONTHLY_EARNINGS = 16;
	const ADMIN_ADD_BALANCE_TO_AFFILIATE = 17;
	const ADMIN_SUBTRACT_BALANCE_TO_AFFILIATE = 18;
	const RANDOM_BONUS = 19;
	const DEPOSIT_TO_AFFILIATE = 20;
	const WITHDRAW_FROM_AFFILIATE = 21;
	const TRANSFER_TO_MAIN_FROM_BALANCE_AFFILIATE = 22;
	const TRANSFER_TO_BALANCE_FROM_MAIN_AFFILIATE = 23;
	const FROM_ADMIN_TO_AGENT = 24;
	const FROM_AGENT_TO_ADMIN = 25;
	const FROM_AGENT_TO_SUB_AGENT = 26;
	const FROM_SUB_AGENT_TO_AGENT = 27;
	const FROM_AGENT_TO_PLAYER = 28;
	const FROM_PLAYER_TO_AGENT = 29;
	const CASHBACK = 30;
	const BIRTHDAY_BONUS = 31;
	const INTERNALWITHDRAWAL = 32;
	const LIVECHAT_TIP = 33;
	const TRANSFER_TO_MAIN_FROM_BALANCE_AGENT = 34;
	const TRANSFER_TO_BALANCE_FROM_MAIN_AGENT = 35;
	const ADMIN_ADD_BALANCE_TO_AGENT = 36;
	const ADMIN_SUBTRACT_BALANCE_FROM_AGENT = 37;
	const DEPOSIT_TO_AGENT = 38;
	const WITHDRAW_FROM_AGENT = 39;
	const AGENT_SETTLEMENT = 40;
	const MANUAL_ADD_SEAMLESS_BALANCE = 41;
	const MANUAL_SUBTRACT_SEAMLESS_BALANCE = 42;

	const CSV_TYPE_BATCH_ADD_BONUS=1;

	# FROM/TO
	const ADMIN = 1;
	const PLAYER = 2;
	const AFFILIATE = 3;
	const AGENT = 4;
	//livechat
	const LIVECHAT_ADMIN = 5;

	# STATUS
	const APPROVED = 1;
	const DECLINED = 2;
	const PENDING = 3;

	# FLAG
	const MANUAL = 1;
	const PROGRAM = 2;

	# FROM/TO IS FOR SYSTEM (e.g. CRONJOBS)
	const SYSTEM_ID = 1;

	# IS MANUAL ADJUSTMENT
	const MANUALLY_ADJUSTED = 1;

	#TOTAL CASHBACK DAY
	const TOTAL_CASHBACK_SAME_DAY    = 1;
	const TOTAL_CASHBACK_PLUS_1_DAY  = 2;

	public function __construct() {
		parent::__construct();
	}

	// add_new_transaction {{{2
	/**
	 * detail: add a record for new transaction
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function add_new_transaction($data) {
		$data['status'] = self::APPROVED; //approved
		$data['created_at'] = $this->utils->getNowForMysql();
		$data['updated_at'] = $this->utils->getNowForMysql();
		$data['trans_date'] = $this->utils->getTodayForMysql();
		$data['trans_year_month'] = $this->utils->getThisYearMonthForMysql();
		$data['trans_year'] = $this->utils->getThisYearForMysql();
		$data['ip_used'] = $this->input->ip_address();

		$this->utils->debug_log('add_new_transaction', $data);
		return $this->insertRow($data);
	} // add_new_transaction  }}}2
	// get_agent_bonuses {{{2

	/**
	 * detail: get agent bonuses
	 *
	 * @param array $player_ids
	 * @param string $start_date
	 * @param string $end_date
	 * @return array
	 */
	public function get_agent_bonuses($player_ids, $start_date = null, $end_date = null) {
		$str = '(CASE WHEN transaction_type = ' . Transactions::ADD_BONUS . ' THEN amount ELSE 0 END)';
		$this->db->select_sum($str, 'bonuses');

		$this->db->from($this->tableName);

		$this->db->where('to_type', Transactions::PLAYER);
		$this->db->where_in('to_id', $player_ids);

		if ($start_date) {
			$this->db->where('created_at >=', $start_date);
		}
		if ($end_date) {
			$this->db->where('created_at <= ', $end_date);
		}

		$query = $this->db->get();
		$result = $query->result_array();

		return $result;
	} // get_agent_bonuses  }}}2
	// get_agent_rebates {{{2
	/**
	 * detail: get agent rebates
	 *
	 * @param array $player_ids
	 * @param string $start_date
	 * @param string $end_date
	 * @return array
	 */
	public function get_agent_rebates($player_ids, $start_date = null, $end_date = null) {
		$select = '(CASE WHEN transaction_type = ' . Transactions::AUTO_ADD_CASHBACK_TO_BALANCE . ' THEN amount ELSE 0 END)';
		$this->db->select_sum($select, 'rebates');

		$this->db->from($this->tableName);
		$this->db->where('to_type', Transactions::PLAYER);
		$this->db->where_in('to_id', $player_ids);

		if ($start_date) {
			$this->db->where('created_at >=', $start_date);
		}
		if ($end_date) {
			$this->db->where('created_at <= ', $end_date);
		}

		$query = $this->db->get();
		$result = $query->result_array();

		return $result;
	} // get_agent_rebates  }}}2
	// }}}1

	/**
	 * detail: create admin to agent transaction
	 *
	 * @param int $adminId
	 * @param int $agentId
	 * @param double $amount
	 * @param string $extraNotes
	 * @param string $datetime
	 * @param int $flag
	 * @return int
	 */
	public function createAdminToAgentTransaction($adminId, $agentId, $amount, $extraNotes = null, $datetime = null, $flag = self::MANUAL) {
		if (empty($adminId) || empty($agentId) || $amount <= 0) {
			$this->utils->error_log('failed createAdminToAgentTransaction', $adminId, $agentId, $amount, $extraNotes);
			return false;
		}

		$this->load->model(array('agency_model', 'users'));
		$transaction_type = self::FROM_ADMIN_TO_AGENT;

		$agent = $this->agency_model->get_agent_by_id($agentId);

		if (empty($datetime)) {
			$datetime = new DateTime();
		}
		if (is_string($datetime)) {
			$datetime = new DateTime($datetime);
		}
		$trans_date = $this->utils->formatDateForMysql($datetime);
		$trans_year_month = $this->utils->formatYearMonthForMysql($datetime);
		$trans_year = $this->utils->formatYearForMysql($datetime);

		$adminUserId = $adminUserId ? $adminUserId : 1;

		$from_username = $this->users->getUsernameById($adminUserId);
		$to_username = $agent['agent_name'];

		$note = 'admin ' . $adminId . '(' . $from_username . ') to agency ' . $agentId . '(' . $to_username . ') ' . $extraNotes;

		$beforeBalanceDetails = $this->agency_model->getBalanceDetails($agentId);
		$this->agency_model->inc_credit($agentId, $amount);
		$afterBalanceDetails = $this->agency_model->getBalanceDetails($agentId);

		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);

		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $adminId,
			'from_type' => self::ADMIN, //admin
			'from_username' => $from_username,
			'to_id' => $agentId,
			'to_type' => self::AGENT, //agent
			'to_username' => $to_username,
			'note' => $note,
			'status' => self::APPROVED, //approved
			'flag' => $flag, //manual
			'created_at' => $this->utils->formatDateTimeForMysql($datetime),
			'updated_at' => $this->utils->getNowForMysql(),
			'trans_date' => $trans_date,
			'trans_year_month' => $trans_year_month,
			'trans_year' => $trans_year,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);

		$rtn_id = $this->insertRow($transactionDetails);

		$afterHistoryId = $this->recordAgentAfterActionWalletBalanceHistory($transaction_type, $agentId, $rtn_id, $amount);
		$this->updateBalanceHistoryTransactionId($afterHistoryId, $rtn_id);

		return $rtn_id;
	}

	public function createAgentToAdminTransaction($adminId, $agentId, $amount, $datetime = null) {
	}

	/**
	 * detrail: create agent to sub-agent
	 *
	 * @param  int $agentId
	 * @param  int $subAgentId
	 * @param  double $amount
	 * @param  string $extraNotes
	 * @param  string $datetime
	 * @param  int $flag
	 *
	 * @return int
	 */
	public function createAgentToSubAgentTransaction($agentId, $subAgentId, $amount, $extraNotes = null, $datetime = null, $flag = self::MANUAL) {
		if (empty($agentId) || empty($subAgentId) || $amount <= 0) {
			$this->utils->error_log('failed createAgentToSubAgentTransaction', $agentId, $subAgentId, $amount, $extraNotes);
			return false;
		}

		$this->load->model(array('agency_model'));
		$transaction_type = self::FROM_AGENT_TO_SUB_AGENT;

		$agent = $this->agency_model->get_agent_by_id($agentId);
		$sub_agent = $this->agency_model->get_agent_by_id($subAgentId);

		if (empty($datetime)) {
			$datetime = new DateTime();
		}
		if (is_string($datetime)) {
			$datetime = new DateTime($datetime);
		}
		$trans_date = $this->utils->formatDateForMysql($datetime);
		$trans_year_month = $this->utils->formatYearMonthForMysql($datetime);
		$trans_year = $this->utils->formatYearForMysql($datetime);

		$from_username = $agent['agent_name'];
		$to_username = $sub_agent['agent_name'];

		$note = 'amount ' . $amount . ' from agent ' . $agentId . '(' . $from_username . ') to sub-agent ' . $subAgentId . '(' . $to_username . ') ' . $extraNotes;

		$beforeBalanceDetails = $this->agency_model->getBalanceDetails($agentId);
		$this->agency_model->inc_credit($subAgentId, $amount);
		$this->agency_model->dec_credit($agentId, $amount);
		$afterBalanceDetails = $this->agency_model->getBalanceDetails($agentId);

		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);

		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $agentId, // $this->authentication->getUserId(),
			'from_type' => self::AGENT, //admin
			'from_username' => $from_username,
			'to_id' => $subAgentId,
			'to_type' => self::AGENT, //agent
			'to_username' => $to_username,
			'note' => $note,
			'status' => self::APPROVED, //approved
			'flag' => $flag, //manual
			'created_at' => $this->utils->formatDateTimeForMysql($datetime),
			'updated_at' => $this->utils->getNowForMysql(),
			'trans_date' => $trans_date,
			'trans_year_month' => $trans_year_month,
			'trans_year' => $trans_year,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);

		$rtn_id = $this->insertRow($transactionDetails);

		$afterHistoryId = $this->recordAgentAfterActionWalletBalanceHistory($transaction_type,
			$agentId, $rtn_id, $amount);
		$this->updateBalanceHistoryTransactionId($afterHistoryId, $rtn_id);

		$afterHistoryId = $this->recordAgentAfterActionWalletBalanceHistory($transaction_type,
			$subAgentId, $rtn_id, $amount);

		return $rtn_id;
	}

	/**
	 * detrail: create sub agent to agent
	 *
	 * @param  int $agentId
	 * @param  int $subAgentId
	 * @param  double $amount
	 * @param  string $extraNotes
	 * @param  string $datetime
	 * @param  int $flag
	 *
	 * @return int
	 */
	public function createSubAgentToAgentTransaction($agentId, $subAgentId, $amount, $extraNotes = null, $datetime = null, $flag = self::MANUAL) {
		if (empty($agentId) || empty($subAgentId) || $amount <= 0) {
			$this->utils->error_log('failed createSubAgentToAgentTransaction', $agentId, $subAgentId, $amount, $extraNotes);
			return false;
		}

		$this->load->model(array('agency_model'));
		$transaction_type = self::FROM_SUB_AGENT_TO_AGENT;

		$agent = $this->agency_model->get_agent_by_id($agentId);
		$sub_agent = $this->agency_model->get_agent_by_id($subAgentId);

		if (empty($datetime)) {
			$datetime = new DateTime();
		}
		if (is_string($datetime)) {
			$datetime = new DateTime($datetime);
		}
		$trans_date = $this->utils->formatDateForMysql($datetime);
		$trans_year_month = $this->utils->formatYearMonthForMysql($datetime);
		$trans_year = $this->utils->formatYearForMysql($datetime);

		$from_username = $sub_agent['agent_name'];
		$to_username = $agent['agent_name'];

		$note = 'amount ' . $amount . ' from sub-agent ' . $subAgentId . '(' . $from_username . ') to agent ' . $agentId . '(' . $to_username . ') ' . $extraNotes;

		$beforeBalanceDetails = $this->agency_model->getBalanceDetails($agentId);
		$this->agency_model->inc_credit($agentId, $amount);
		$this->agency_model->dec_credit($subAgentId, $amount);
		$afterBalanceDetails = $this->agency_model->getBalanceDetails($agentId);

		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);

		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $subAgentId, // $this->authentication->getUserId(),
			'from_type' => self::AGENT, //admin
			'from_username' => $from_username,
			'to_id' => $agentId,
			'to_type' => self::AGENT, //agent
			'to_username' => $to_username,
			'note' => $note,
			'status' => self::APPROVED, //approved
			'flag' => $flag, //manual
			'created_at' => $this->utils->formatDateTimeForMysql($datetime),
			'updated_at' => $this->utils->getNowForMysql(),
			'trans_date' => $trans_date,
			'trans_year_month' => $trans_year_month,
			'trans_year' => $trans_year,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);

		$rtn_id = $this->insertRow($transactionDetails);
		//record from agent first
		$afterHistoryId = $this->recordAgentAfterActionWalletBalanceHistory($transaction_type,
			$subAgentId, $rtn_id, $amount);
		$this->updateBalanceHistoryTransactionId($afterHistoryId, $rtn_id);

		$afterHistoryId = $this->recordAgentAfterActionWalletBalanceHistory($transaction_type,
			$agentId, $rtn_id, $amount);

		return $rtn_id;
	}

	/**
	 * detail: create agent to player transaction
	 *
	 * @param  int $agentId
	 * @param  int $playerId
	 * @param  double $amount
	 * @param  string $extraNotes
	 * @param  string $datetime
	 * @param  int $flag
	 *
	 * @return int
	 */
	public function createAgentToPlayerTransaction($agentId, $playerId, $amount, $extraNotes = null, $datetime = null, $flag = self::MANUAL) {
		if (empty($agentId) || empty($playerId) || $amount <= 0) {
			$this->utils->error_log('failed createAgentToPlayerTransaction', $agentId, $playerId, $amount, $extraNotes);
			return false;
		}

		$this->load->model(array('wallet_model', 'player_model', 'agency_model'));

		if (empty($datetime)) {
			$datetime = new DateTime();
		}
		if (is_string($datetime)) {
			$datetime = new DateTime($datetime);
		}
		$trans_date = $this->utils->formatDateForMysql($datetime);
		$trans_year_month = $this->utils->formatYearMonthForMysql($datetime);
		$trans_year = $this->utils->formatYearForMysql($datetime);

		// $beforeHistoryId = $this->recordPlayerBeforeActionWalletBalanceHistory($adjustment_type,
		// 	$playerId, null, 0, $amount, null, null);

		$agent_info = $this->agency_model->get_agent_by_id($agentId);

		if($this->utils->compareResultCurrency($agent_info['available_credit'], '<', $amount)){

			$this->utils->error_log('no enough balance, agent id:'.$agentId, $agent_info['available_credit'], $amount);
			return false;
		}

		$beforeBalanceDetails = $this->agency_model->get_credit_details($agentId);

		$trans_type = self::FROM_AGENT_TO_PLAYER;
		$this->agency_model->dec_credit($agentId, $amount);

		$afterBalanceDetails = $this->agency_model->get_credit_details($agentId);

		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);

		$from_username = $agent_info['agent_name'];
		$to_username = $this->player_model->getUsernameById($playerId);

		$note = 'amount ' . $amount . ' from agent ' . $agentId . '(' . $from_username . ') to player ' . $playerId . '(' . $to_username . ') ' . $extraNotes;

		// $note = sprintf('trans type:%s; <b>%s</b> credit from agent: <b>%s</b>\'s credit (<b>%s</b> to <b>%s</b>)',
		//           $trans_type, number_format($amount, 2), $from_username, number_format($beforeBalanceDetails['total_balance'], 2),
		//           number_format($afterBalanceDetails['total_balance'], 2));

		// $flag = self::MANUAL;
		$transaction = array(
			'amount' => $amount,
			'transaction_type' => $trans_type,
			'from_id' => $agentId,
			'from_type' => Transactions::AGENT,
			'from_username' => $from_username,
			'to_id' => $playerId,
			'to_type' => Transactions::PLAYER,
			'to_username' => $to_username,
			'note' => $note,
			// 'before_balance' => $beforeBalanceDetails['main_wallet'],
			// 'after_balance' => $afterBalanceDetails['main_wallet'],
			// 'sub_wallet_id' => $subWalletId,
			'status' => Transactions::APPROVED,
			'flag' => $flag,
			'created_at' => $this->utils->formatDateTimeForMysql($datetime),
			'updated_at' => $this->utils->getNowForMysql(),
			// 'promo_category' => null,
			// 'balance_history_id' => $beforeHistoryId,
			// 'display_name' => $show_in_front_end,
			'trans_date' => $trans_date,
			'trans_year_month' => $trans_year_month,
			'trans_year' => $trans_year,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);

		$rtn_id = $this->insertRow($transaction);
		if($this->utils->getConfig('record_balance_history_when_create_transactions')){
			//record from agent first
			$afterHistoryId = $this->recordAgentAfterActionWalletBalanceHistory($trans_type,
				$agentId, $rtn_id, $amount);
			$this->updateBalanceHistoryTransactionId($afterHistoryId, $rtn_id);
		}

		return $rtn_id;
	}

	/**
	 * detail: create player to agent transaction
	 *
	 * @param  int $agentId
	 * @param  int $playerId
	 * @param  double $amount
	 * @param  string $extraNotes
	 * @param  string $datetime
	 * @param  int $flag
	 *
	 * @return int
	 */
	public function createPlayerToAgentTransaction($agentId, $playerId, $amount, $extraNotes = null, $datetime = null, $flag = self::MANUAL) {
		if (empty($agentId) || empty($playerId) || $amount <= 0) {
			$this->utils->error_log('failed createAgentToPlayerTransaction', $agentId, $playerId, $amount, $extraNotes);
			return false;
		}

		$this->load->model(array('wallet_model', 'player_model', 'agency_model'));

		if (empty($datetime)) {
			$datetime = new DateTime();
		}
		if (is_string($datetime)) {
			$datetime = new DateTime($datetime);
		}
		$trans_date = $this->utils->formatDateForMysql($datetime);
		$trans_year_month = $this->utils->formatYearMonthForMysql($datetime);
		$trans_year = $this->utils->formatYearForMysql($datetime);

		// $beforeHistoryId = $this->recordPlayerBeforeActionWalletBalanceHistory($adjustment_type,
		// 	$playerId, null, 0, $amount, null, null);

		$agent_info = $this->agency_model->get_agent_by_id($agentId);

		$beforeBalanceDetails = $this->agency_model->get_credit_details($agentId);

		$trans_type = self::FROM_PLAYER_TO_AGENT;
		$this->agency_model->inc_credit($agentId, $amount);

		$afterBalanceDetails = $this->agency_model->get_credit_details($agentId);

		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);

		$from_username = $this->player_model->getUsernameById($playerId);
		$to_username = $agent_info['agent_name'];

		$note = 'amount ' . $amount . ' from player ' . $playerId . '(' . $from_username . ') to agent ' . $agentId . '(' . $to_username . ') ' . $extraNotes;

		// $note = sprintf('trans type:%s; <b>%s</b> credit from agent: <b>%s</b>\'s credit (<b>%s</b> to <b>%s</b>)',
		//           $trans_type, number_format($amount, 2), $from_username, number_format($beforeBalanceDetails['total_balance'], 2),
		//           number_format($afterBalanceDetails['total_balance'], 2));

		// $flag = self::MANUAL;
		$transaction = array(
			'amount' => $amount,
			'transaction_type' => $trans_type,
			'from_id' => $playerId,
			'from_type' => Transactions::PLAYER,
			'from_username' => $from_username,
			'to_id' => $agentId,
			'to_type' => Transactions::AGENT,
			'to_username' => $to_username,
			'note' => $note,
			// 'before_balance' => $beforeBalanceDetails['main_wallet'],
			// 'after_balance' => $afterBalanceDetails['main_wallet'],
			// 'sub_wallet_id' => $subWalletId,
			'status' => Transactions::APPROVED,
			'flag' => $flag,
			'created_at' => $this->utils->formatDateTimeForMysql($datetime),
			'updated_at' => $this->utils->getNowForMysql(),
			// 'promo_category' => null,
			// 'balance_history_id' => $beforeHistoryId,
			// 'display_name' => $show_in_front_end,
			'trans_date' => $trans_date,
			'trans_year_month' => $trans_year_month,
			'trans_year' => $trans_year,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);

		$rtn_id = $this->insertRow($transaction);
		if($this->utils->getConfig('record_balance_history_when_create_transactions')){
			//record from agent first
			$afterHistoryId = $this->recordAgentAfterActionWalletBalanceHistory($trans_type,
				$agentId, $rtn_id, $amount);
			$this->updateBalanceHistoryTransactionId($afterHistoryId, $rtn_id);
		}

		return $rtn_id;
	}

	/**
	 * detail: create bonus transaction
	 *
	 * @param int $adminUserId
	 * @param int $playerId
	 * @param double $amount
	 * @param double $beforeBalance
	 * @param int $playerPromoId
	 * @param int $depositTranId
	 * @param int $flag
	 * @param double $totalBeforeBalance
	 * @param int $transaction_type
	 * @param string $additionNote
	 * @param int $promo_category
	 * @param int $sub_wallet_id
	 * @param string $extra_info
	 *
	 * @return int or boolean
	 */
	public function createBonusTransaction($adminUserId, $playerId, $amount, $beforeBalance,
		$playerPromoId, $depositTranId = null, $flag = self::MANUAL, $totalBeforeBalance = null,
		$transaction_type = self::ADD_BONUS, $additionNote = null, $promo_category = null, $sub_wallet_id = null, &$extra_info = null) {

		if ($playerId && $amount) {
			$this->load->model(array('wallet_model', 'users', 'player_model', 'sale_order'));

			$adminUserId = $adminUserId ? $adminUserId : 1;

			$from_username = $this->users->getUsernameById($adminUserId);
			$to_username = $this->player_model->getUsernameById($playerId);

			$note = $adminUserId . ' add bonus ' . $amount . ' to ' . $playerId . ' ';

			if (!empty($playerPromoId)) {
				$note = $note . ' because player promotion:' . $playerPromoId;
			}

			if (!empty($additionNote)) {
				$note = $note . ' ' . $additionNote;
			}

			$saleOrderId = $this->getSaleOrderId($depositTranId);
			$saleOrder = $this->sale_order->getSaleOrderById($saleOrderId);
			$depositAmount = null;
			if ($saleOrder) {
				$depositAmount = $saleOrder->amount;
			}

			$beforeBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);

			if ($this->utils->isEnabledPromotionRule('only_real_when_release_bonus')) {
				//real money, like old
				$success = $this->wallet_model->incMainDepositOnBigWallet($playerId, $amount);
			} else {
				if (isset($extra_info['release_to_real']) && $extra_info['release_to_real']) {
					//real money, like old
					$success = $this->wallet_model->incMainDepositOnBigWallet($playerId, $amount);
				} else {
					//move depositAmount to real_for_bonus, add amount to bonus
					$success = $this->wallet_model->incMainBonusOnBigWallet($playerId, $amount, $depositAmount);
				}
			}
			if (!$success) {
				return $success;
			}
			$afterBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);
			$transactionDetails = array(
				'amount' => $amount,
				'transaction_type' => $transaction_type,
				'from_id' => $adminUserId,
				'from_type' => self::ADMIN, //admin
				'from_username' => $from_username,
				'to_id' => $playerId,
				'to_type' => self::PLAYER, //player
				'to_username' => $to_username,
				'note' => $note,
				'status' => self::APPROVED, //approved
				'flag' => $flag, //manual
				'created_at' => $this->utils->getNowForMysql(),
				'updated_at' => $this->utils->getNowForMysql(),
				'order_id' => $saleOrderId,
				'player_promo_id' => $playerPromoId,
				'promo_category' => $promo_category,
				'trans_date' => $this->utils->getTodayForMysql(),
				'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
				'trans_year' => $this->utils->getThisYearForMysql(),
				'sub_wallet_id' => $sub_wallet_id,

				'before_balance' => $beforeBalanceDetails['total_balance'],
				'after_balance' => $afterBalanceDetails['total_balance'],
				'total_before_balance' => $beforeBalanceDetails['total_balance'],
				'changed_balance' => $this->utils->encodeJson(array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount)),
			);
			$rtn_id = $this->insertRow($transactionDetails);

			$afterHistoryId = $this->recordPlayerAfterActionWalletBalanceHistory($transaction_type,
				$playerId, null, $rtn_id, $amount, $saleOrderId, $playerPromoId);
			$this->updateBalanceHistoryTransactionId($afterHistoryId, $rtn_id);

			return $rtn_id;
		}
		return false;
	}

	/**
	 * detail: create deposit bonus transaction
	 *
	 * @param int $adminUserId current logged in
	 * @param int $playerId transaction to_id  field
	 * @param double $amount transaction amount field
	 * @param double $beforeBalance
	 * @param string $note
	 * @param int $saleOrderId
	 * @param int $flag
	 * @param double $totalBeforeBalance
	 *
	 * @return int or Boolean
	 */
	public function createDepositBonusTransaction($adminUserId, $playerId, $amount, $beforeBalance,
		$note = null, $saleOrderId = null, $flag = self::MANUAL, $totalBeforeBalance = null) {

		if ($playerId && $amount) {
			$this->load->model(array('wallet_model', 'promorules', 'player_promo', 'users', 'player_model'));
			if ($totalBeforeBalance == null) {
				$totalBeforeBalance = $this->wallet_model->getTotalBalance($playerId);
			}

			$adminUserId = $adminUserId ? $adminUserId : 1;
			$promorulesId = $this->promorules->getSystemManualPromoRuleId();
			$promoCmsSettingId = $this->promorules->getSystemManualPromoCMSId();
			$playerBonusAmount = $amount;
			$player_promo_id = $this->player_promo->approvePromoToPlayer($playerId, $promorulesId, $playerBonusAmount,
				$promoCmsSettingId, $adminUserId);

			$transaction_type = self::MEMBER_GROUP_DEPOSIT_BONUS;

			$from_username = $this->users->getUsernameById($adminUserId);
			$to_username = $this->player_model->getUsernameById($playerId);

			$beforeBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);
			$success=$this->wallet_model->incMainBonusOnBigWallet($playerId, $amount);
			if(!$success){
				$this->utils->error_log('incMainBonusOnBigWallet failed', $playerId, $amount);
				return $success;
			}
			$afterBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);

			$transactionDetails = array(
				'amount' => $amount,
				'transaction_type' => $transaction_type,
				'from_id' => $adminUserId,
				'from_type' => self::ADMIN, //admin
				'from_username' => $from_username,
				'to_id' => $playerId,
				'to_type' => self::PLAYER, //player
				'to_username' => $to_username,
				'note' => $note,
				'status' => self::APPROVED, //approved
				'flag' => $flag, //manual
				'created_at' => $this->utils->getNowForMysql(),
				'updated_at' => $this->utils->getNowForMysql(),
				'order_id' => $saleOrderId,
				'player_promo_id' => $player_promo_id,
				'trans_date' => $this->utils->getTodayForMysql(),
				'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
				'trans_year' => $this->utils->getThisYearForMysql(),

				'before_balance' => $beforeBalanceDetails['total_balance'],
				'after_balance' => $afterBalanceDetails['total_balance'],
				'total_before_balance' => $beforeBalanceDetails['total_balance'],
				'changed_balance' => $this->utils->encodeJson(array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount)),
			);
			$rtn_id = $this->insertRow($transactionDetails);

			$afterHistoryId = $this->recordPlayerAfterActionWalletBalanceHistory($transaction_type,
				$playerId, null, $rtn_id, $amount, $saleOrderId, $player_promo_id);
			$this->updateBalanceHistoryTransactionId($afterHistoryId, $rtn_id);

			return $rtn_id;
		}
		return false;
	}

	/**
	 * detail: create deposit transaction
	 *
	 * note: all parameters without default value are mandatory
	 *
	 * @param array $saleOrder
	 * @param int $adminUserId current logged user
	 * @param double $beforeBalance
	 * @param int $flag
	 * @param double $totalBeforeBalance
	 *
	 * @return Boolean
	 */
	public function createDepositTransaction($saleOrder, $adminUserId, $beforeBalance,
		$flag = self::MANUAL, $totalBeforeBalance = null) {
		if ($saleOrder) {
			$playerId = $saleOrder->player_id;
			$amount = $saleOrder->amount;
			$this->load->model(array('wallet_model', 'player_model', 'users'));
			// if ($totalBeforeBalance == null) {
			// 	$totalBeforeBalance = $this->wallet_model->getTotalBalance($playerId);
			// }
			$adminUserId = $adminUserId ? $adminUserId : 1;

			$from_username = $this->users->getUsernameById($adminUserId);
			$to_username = $this->player_model->getUsernameById($playerId);

			$transaction_type = self::DEPOSIT;
			// $beforeHistoryId = $this->recordPlayerBeforeActionWalletBalanceHistory($transaction_type,
			// 	$playerId, null, 0, $amount, $saleOrder->id);

			$beforeBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);
			$success=$this->wallet_model->incMainDepositOnBigWallet($playerId, $amount);
			if(!$success){
				return $success;
			}
			$afterBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);

			$transactionDetails = array('amount' => $amount,
				'transaction_type' => $transaction_type, //deposit
				'from_id' => $adminUserId, // $this->authentication->getUserId(),
				'from_type' => self::ADMIN, //admin
				'from_username' => $from_username,
				'to_id' => $playerId,
				'to_type' => self::PLAYER, //player
				'to_username' => $to_username,
				'note' => $adminUserId . ' deposit ' . $amount . ' to ' . $playerId,
				'status' => self::APPROVED, //approved
				'flag' => $flag, //manual
				'created_at' => $this->utils->getNowForMysql(),
				'updated_at' => $this->utils->getNowForMysql(),
				'order_id' => $saleOrder->id,
				'request_secure_id' => $saleOrder->secure_id,
				'payment_account_id' => $saleOrder->payment_account_id,
				// 'balance_history_id' => $beforeHistoryId,
				'trans_date' => $this->utils->getTodayForMysql(),
				'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
				'trans_year' => $this->utils->getThisYearForMysql(),

				'before_balance' => $beforeBalanceDetails['total_balance'],
				'after_balance' => $afterBalanceDetails['total_balance'],
				'total_before_balance' => $beforeBalanceDetails['total_balance'],
				'changed_balance' => $this->utils->encodeJson(array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount)),
			);
			$rlt = $this->insertRow($transactionDetails);

			// $this->updateChangedBalance($rlt, array('before' => $beforeBalanceDetails,
			// 	'after' => $afterBalanceDetails, 'amount' => $amount));

			if($this->utils->getConfig('record_balance_history_when_create_transactions')){
				$afterHistoryId = $this->recordPlayerAfterActionWalletBalanceHistory($transaction_type,
					$playerId, null, $rlt, $amount, $saleOrder->id);
				$this->updateBalanceHistoryTransactionId($afterHistoryId, $rlt);
			}

			// $this->load->model('daily_player_trans');
			// $this->daily_player_trans->update_today($transactionDetails);
			//update player total deposit amount
			if($this->utils->getConfig('update_total_deposit_when_create_transactions')){
				$this->player_model->updateTotalDepositAmount($playerId);
			}


			// -- updatePlayersTotalDepositCount
			$this->utils->debug_log('START RUNNING: updatePlayersTotalDepositCount');
			$updatePlayersTotalDepositCount = $this->player_model->updatePlayersTotalDepositCount($playerId);

			$this->utils->debug_log('RESULT OF updatePlayersTotalDepositCount: Total count of players updated = '.$updatePlayersTotalDepositCount);

			$this->utils->debug_log('END RUNNING: updatePlayersTotalDepositCount');


			// -- updatePlayersFirstDeposit
			$this->utils->debug_log('START RUNNING: updatePlayersFirstDeposit');
			$updatePlayersFirstDeposit = $this->player_model->updatePlayersFirstDeposit($playerId);

			$this->utils->debug_log('RESULT OF updatePlayersFirstDeposit: Total count of players updated = '.$updatePlayersFirstDeposit);

			$this->utils->debug_log('END RUNNING: updatePlayersFirstDeposit');


			// -- updatePlayersSecondDeposit
			$this->utils->debug_log('START RUNNING: updatePlayersSecondDeposit');
			$updatePlayersSecondDeposit = $this->player_model->updatePlayersSecondDeposit($playerId);

			$this->utils->debug_log('RESULT OF updatePlayersSecondDeposit: Total count of players updated = '.$updatePlayersSecondDeposit);

			$this->utils->debug_log('END RUNNING: updatePlayersSecondDeposit');

			return $rlt;
		}
		return false;
	}

	/**
	 * detail: create withdraw transaction
	 *
	 * @param int $playerId transaction to_id field
	 * @param int $adminUserId current logged user
	 * @param int $walletAccountId
	 * @param double $amount
	 * @param double $totalBeforeBalance
	 * @param double $beforeBalance
	 * @param double $afterBalance
	 *
	 * @return Boolean
	 */
	public function createWithdrawTransaction($playerId, $adminUserId, $walletAccountId, $amount,
		$totalBeforeBalance = null, $beforeBalance = null, $afterBalance = null) {

		$this->load->model(array('wallet_model', 'users', 'player_model'));

		// if ($totalBeforeBalance == null) {
		// 	$totalBeforeBalance = $this->wallet_model->getTotalBalance($playerId);
		// }

		$from_username = $this->users->getUsernameById($adminUserId);
		$to_username = $this->player_model->getUsernameById($playerId);

		$transaction_type = self::WITHDRAWAL;
		// $beforeHistoryId = $this->recordPlayerBeforeActionWalletBalanceHistory($transaction_type,
		// 	$playerId, null, 0, $amount, null, null, null, $walletAccountId);

		$beforeBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);
		// clear frozen
		$success = $this->wallet_model->decFrozenOnBigWallet($playerId, $amount);
		if (!$success) {
			$this->utils->error_log('not enough frozen', $playerId, 'amount', $amount, $beforeBalanceDetails);
			return $success;
		}
		// $this->db->set('frozen', 'frozen - ' . $amount, false);
		// $this->db->where('playerId', $playerId);
		// $this->db->update('player');
		$afterBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);

		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => Transactions::WITHDRAWAL,
			'from_id' => $adminUserId,
			'from_type' => Transactions::ADMIN,
			'from_username' => $from_username,
			'to_id' => $playerId,
			'to_type' => Transactions::PLAYER,
			'to_username' => $to_username,
			'note' => 'approved withdrawal ' . $amount . ' to playerId:' . $playerId,
			'status' => Transactions::APPROVED,
			'flag' => Transactions::MANUAL,
			'created_at' => $this->utils->getNowForMysql(),
			'trans_date' => $this->utils->getTodayForMysql(),
			'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
			'trans_year' => $this->utils->getThisYearForMysql(),

			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'total_before_balance' => $beforeBalanceDetails['total_balance'],
			'changed_balance' => $this->utils->encodeJson(array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount)),
			'request_secure_id' => $this->wallet_model->getRequestSecureId($walletAccountId),
		);

		$rlt = $this->insertRow($transactionDetails);
		// $this->updateChangedBalance($rlt, array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount));

		#issue #150
		if ($rlt) {
			$this->updatePlayerWithdrawalInfo($playerId);
		}

		if($this->utils->getConfig('record_balance_history_when_create_transactions')){
			$afterHistoryId = $this->recordPlayerAfterActionWalletBalanceHistory($transaction_type,
				$playerId, null, $rlt, $amount, null, null, null, $walletAccountId);
			$this->updateBalanceHistoryTransactionId($afterHistoryId, $rlt);
		}

		return $rlt;
	}

	public function updatePlayerWithdrawalInfo($playerId) {
		$this->db->select('count(*) as approvedWithdrawCount, sum(amount) as approvedWithdrawAmount');
		$this->db->from('transactions');
		$where = array('to_id' => $playerId, 'transaction_type' => self::WITHDRAWAL);
		$this->db->where($where);
		$result = $this->runOneRow();

		$this->load->model(array('player_model'));
		$update_result = $this->player_model->updateApprovedWithdrawAmountAndCount($playerId, $result->approvedWithdrawCount, $result->approvedWithdrawAmount);

		if ($update_result) {
			$this->utils->debug_log('Update player withdraw info is success!');
		}

		return $update_result;
	}

	/**
	 * detail: create referral transaction for a certain player
	 *
	 * note: all parameters without default value are mandatory
	 *
	 * @param int $playerId transaction to_id field
	 * @param int $adminUserId current logged user
	 * @param double $amount
	 * @param double $totalBeforeBalance
	 * @param double $beforeBalance
	 * @param double $afterBalance
	 *
	 * @return Boolean
	 */
	public function createPlayerReferralTransaction($playerId, $adminUserId, $amount, $totalBeforeBalance = null, $beforeBalance = null, $afterBalance = null, $extra_note = null) {

		$this->load->model(array('wallet_model', 'users', 'player_model'));

		$from_username = $this->users->getUsernameById($adminUserId);
		$to_username = $this->player_model->getUsernameById($playerId);

		$transaction_type = self::PLAYER_REFER_BONUS;
		$beforeBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);
		$success=$this->wallet_model->incMainBonusOnBigWallet($playerId, $amount);
		if(!$success){
			$this->utils->error_log('incMainBonusOnBigWallet failed', $playerId, 'amount', $amount);
			return $success;
		}
		$afterBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);
		$note = sprintf('%s referral bonus for referring %s', $this->utils->formatCurrencyNoSym($amount), $playerId);
		if (!empty($extra_note)) {
			$note = $extra_note;
		}
		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $adminUserId,
			'from_type' => Transactions::ADMIN,
			'from_username' => $from_username,
			'to_id' => $playerId,
			'to_type' => Transactions::PLAYER,
			'to_username' => $to_username,
			'note' => $note,
			'status' => Transactions::APPROVED,
			'flag' => Transactions::MANUAL,
			'created_at' => $this->utils->getNowForMysql(),
			'trans_date' => $this->utils->getTodayForMysql(),
			'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
			'trans_year' => $this->utils->getThisYearForMysql(),
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'total_before_balance' => $beforeBalanceDetails['total_balance'],
			'changed_balance' => $this->utils->encodeJson(array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount)),
		);

		$rlt = $this->insertRow($transactionDetails);

		$afterHistoryId = $this->recordPlayerAfterActionWalletBalanceHistory($transaction_type, $playerId, null, $rlt, $amount, null, null, null, null);
		$this->updateBalanceHistoryTransactionId($afterHistoryId, $rlt);
		return $rlt;
	}

	/**
	 * detail: create transfer wallet transaction for a a certain player
	 *
	 * note: all parameters without default value are mandatory
	 *
	 * @param int $playerId transaction to_id field
	 * @param int $transaction_type transaction field
	 * @param int $playerAccountId
	 * @param int $gamePlatformId transaction sub_wallet_id field
	 * @param int $walletIncId
	 * @param int $walletDecId
	 * @param double $amount transaction field
	 * @param int $external_transaction_id transaction field
	 * @param string $note transaction field
	 * @param double $totalBeforeBalance
	 * @param double $beforeBalance
	 * @param double $afterBalance
	 * @param int $transfer_from
	 * @param int $transfer_to
	 * @param int $walletType
	 * @param double $originTransferAmount
	 *
	 * @return Boolean
	 */
	public function createTransferWalletTransaction($playerId, $transaction_type, $playerAccountId,
		$gamePlatformId, $walletIncId, $walletDecId, $amount, $external_transaction_id,
		$note, $totalBeforeBalance, $beforeBalance, $afterBalance, $transfer_from, $transfer_to,
		$walletType = null, $originTransferAmount = null, $ignore_promotion_check = false, $transfer_request_id=null,
		$only_add_main_wallet=false, $is_manual_adjustment = null, $process_user_id = null, &$err_code) {

		$this->load->model(array('wallet_model', 'users', 'player_model'));

		// if ($totalBeforeBalance == null) {
		// 	$totalBeforeBalance = $this->wallet_model->getTotalBalance($playerId);
		// }

		// $from_username = $this->users->getUsernameById($adminUserId);
		$player_username = $this->player_model->getUsernameById($playerId);

		// $beforeHistoryId = $this->recordPlayerBeforeActionWalletBalanceHistory($transaction_type,
		// 	$playerId, null, 0, $amount, null, null, $subWalletId, null, $gamePlatformId);

		$beforeBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);

		$success = $this->wallet_model->transferOnBigWallet($playerId, $amount, $transfer_to, $transfer_from, $walletType, $err_code);

		if (!$success) {
			$this->utils->error_log('transferOnBigWallet failed', $playerId, $amount, $transfer_to, $transfer_from, $walletType);
			// throw new Exception('transfer failed '.$playerId);
			return $success;
		}

		//try transfer originTransferAmount
		if ($walletType == 'bonus') {
			//move real to real_for_bonus
			$success = $this->wallet_model->transferInOneWallet($playerId, $gamePlatformId, 'real', 'real_for_bonus', $originTransferAmount);
			if (!$success) {

				$this->utils->error_log('transferInOneWallet failed', $playerId, $amount, $transfer_to, $transfer_from, $walletType, $originTransferAmount);

			}
		}

		$afterBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);

		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $playerId,
			'from_type' => Transactions::PLAYER,
			'from_username' => $player_username,
			'to_id' => $playerId,
			'to_type' => Transactions::PLAYER,
			'to_username' => $player_username,
			'external_transaction_id' => $external_transaction_id,
			'sub_wallet_id' => $gamePlatformId,
			'note' => $note,
			'status' => Transactions::APPROVED,
			'flag' => Transactions::MANUAL,
			'ignore_promotion_check' => $ignore_promotion_check ? self::DB_TRUE : self::DB_FALSE,

			'created_at' => $this->utils->getNowForMysql(),
			'trans_date' => $this->utils->getTodayForMysql(),
			'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
			'trans_year' => $this->utils->getThisYearForMysql(),

			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'total_before_balance' => $beforeBalanceDetails['total_balance'],
			'changed_balance' => $this->utils->encodeJson(array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount)),
			'transfer_request_id' => $transfer_request_id,
			'is_manual_adjustment' => $is_manual_adjustment,
			'process_user_id' => $is_manual_adjustment == Transactions::MANUALLY_ADJUSTED ? $process_user_id : null
		);

		$rlt = $this->insertRow($transactionDetails);

		// $this->updateChangedBalance($rlt, array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount));

		if($this->utils->getConfig('record_balance_history_when_create_transactions')){
			$afterHistoryId = $this->recordPlayerAfterActionWalletBalanceHistory($transaction_type,
				$playerId, null, $rlt, $amount, null, null, $playerAccountId, null, $gamePlatformId);
			$this->updateBalanceHistoryTransactionId($afterHistoryId, $rlt);
		}

		return $rlt;
	}

	/**
	 * detail: create adjustment for a certain transaction
	 *
	 * note: all parameters without default value are mandatory
	 *
	 * @param int $adjustment_type
	 * @param int $adminUserId current logged user
	 * @param int $playerId transaction to_id field
	 * @param double $amount transaction field
	 * @param double $beforeBalance
	 * @param string $note transaction field
	 * @param double $totalBeforeBalance
	 * @param int $promo_category
	 * @param int $show_in_front_end
	 * @param string $reason
	 * @param int $promoRuleId
	 *
	 * @return Boolean
	 */
	public function createAdjustmentTransaction($adjustment_type, $adminUserId, $playerId, $amount,
		$beforeBalance, $note = null, $totalBeforeBalance = null,
		$promo_category = null, $show_in_front_end = null, $reason = null, $promoRuleId = null,$adjustment_category_id = null , $is_manual_adjustment = null) {

		if ($playerId && $amount) {
			$this->load->model(array('wallet_model', 'promorules', 'player_promo', 'users', 'player_model'));
			$from_username = $this->users->getUsernameById($adminUserId);
			$to_username = $this->player_model->getUsernameById($playerId);

			$flag = self::MANUAL;

			$beforeBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);
			switch ($adjustment_type) {
			case Transactions::MANUAL_ADD_BALANCE:
				if(!$this->wallet_model->incMainManuallyOnBigWallet($playerId, $amount)){
					return false;
				}
				break;
			case Transactions::SUBTRACT_BONUS:
			case Transactions::MANUAL_SUBTRACT_BALANCE:
				if (!$this->wallet_model->decMainManuallyOnBigWallet($playerId, $amount)) {
					return false;
				}
				break;
			case Transactions::AUTO_ADD_CASHBACK_TO_BALANCE:
				if (!$this->wallet_model->incMainCashbackOnBigWallet($playerId, $amount)){
					return false;
				}
				break;
			case Transactions::ADD_BONUS:
				if (!$this->wallet_model->incMainBonusOnBigWallet($playerId, $amount)){
					return false;
				}
				break;
			}
			$afterBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);

            $note_reason = sprintf('<i>Reason:</i> %s <br>',$reason);
            $note = (trim($reason) != '')  ? ($note_reason . sprintf('<i>Normal Note:</i> %s <br>',$note)) : $note;

			$transaction = array(
				'amount' => $amount,
				'transaction_type' => $adjustment_type,
				'from_id' => $adminUserId,
				'from_type' => Transactions::ADMIN,
				'from_username' => $from_username,
				'to_id' => $playerId,
				'to_type' => Transactions::PLAYER,
				'to_username' => $to_username,
				'note' => $note,
				'status' => Transactions::APPROVED,
				'flag' => $flag,
				'created_at' => $this->utils->getNowForMysql(),
				'updated_at' => $this->utils->getNowForMysql(),
				'promo_category' => $promo_category,
				'display_name' => $show_in_front_end == '1' ? $reason : null,
				'trans_date' => $this->utils->getTodayForMysql(),
				'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
				'trans_year' => $this->utils->getThisYearForMysql(),

				'before_balance' => $beforeBalanceDetails['total_balance'],
				'after_balance' => $afterBalanceDetails['total_balance'],
				'total_before_balance' => $beforeBalanceDetails['total_balance'],
				'changed_balance' => $this->utils->encodeJson(array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount)),
				'adjustment_category_id' => $adjustment_category_id,
				'is_manual_adjustment' => $is_manual_adjustment,
				'process_user_id' => $is_manual_adjustment == Transactions::MANUALLY_ADJUSTED ? $adminUserId : null
			);

			$rtn_id = $this->insertRow($transaction);
			if($this->utils->isEnabledFeature('auto_add_reason_in_adjustment_main_wallet_to_player_notes')){
				$this->player_model->addPlayerNote($playerId, $adminUserId, $reason);
			}

			$afterHistoryId = $this->recordPlayerAfterActionWalletBalanceHistory($adjustment_type,
				$playerId, null, $rtn_id, $amount, null, null);
			$this->updateBalanceHistoryTransactionId($afterHistoryId, $rtn_id);

			$transaction['id'] = $rtn_id;
			return $transaction;
		}
		return false;
	}

	/**
	 * note: all parameters without default value are mandatory
	 *
	 * @param int $playerId transaction to_id field
	 * @param double $amount transaction field
	 * @param int $adminUserId current logged user
	 * @param string $note transaction field
	 * @param int $external_transaction_id transaction field
	 *
	 * @return Boolean
	 */
	public function createDecTransaction($playerId, $amount, $adminUserId, $note = null, $external_transaction_id = null) {

		if ($playerId && $amount) {
			if(!empty($external_transaction_id)){

				//check unique external_transaction_id
				$this->db->select('id')->from('transactions')->where('external_transaction_id', $external_transaction_id);
				if ($this->runExistsResult()) {
					//check double
					return false;
				}

			}

			$this->load->model(array('wallet_model', 'promorules', 'player_promo', 'users', 'player_model'));
			// if ($totalBeforeBalance == null) {
			// 	$totalBeforeBalance = $this->wallet_model->getTotalBalance($playerId);
			// }

			// $beforeHistoryId = $this->recordPlayerBeforeActionWalletBalanceHistory($adjustment_type,
			// 	$playerId, null, 0, $amount, null, null);

			$from_username = $this->users->getUsernameById($adminUserId);
			$to_username = $this->player_model->getUsernameById($playerId);

			$flag = self::MANUAL;

			$adjustment_type = self::MANUAL_SUBTRACT_BALANCE;

			$beforeBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);
			if (!$this->wallet_model->decMainManuallyOnBigWallet($playerId, $amount)) {
				return false;
			}
			$afterBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);

			$this->utils->debug_log('note', $note);

			$transaction = array(
				'amount' => $amount,
				'transaction_type' => $adjustment_type,
				'from_id' => $adminUserId,
				'from_type' => Transactions::ADMIN,
				'from_username' => $from_username,
				'to_id' => $playerId,
				'to_type' => Transactions::PLAYER,
				'to_username' => $to_username,
				'note' => $note,
				'external_transaction_id' => $external_transaction_id,
				// 'sub_wallet_id' => $wallet_type ? $wallet_type : null,
				'status' => Transactions::APPROVED,
				'flag' => $flag,
				'created_at' => $this->utils->getNowForMysql(),
				'updated_at' => $this->utils->getNowForMysql(),
				// 'promo_category' => $promo_category,
				// 'display_name' => $show_in_front_end == '1' ? $reason : null,
				// 'balance_history_id' => $beforeHistoryId,
				'trans_date' => $this->utils->getTodayForMysql(),
				'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
				'trans_year' => $this->utils->getThisYearForMysql(),

				'before_balance' => $beforeBalanceDetails['total_balance'],
				'after_balance' => $afterBalanceDetails['total_balance'],
				'total_before_balance' => $beforeBalanceDetails['total_balance'],
				'changed_balance' => $this->utils->encodeJson(array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount)),
			);

			$rtn_id = $this->insertRow($transaction);

			// $this->updateChangedBalance($rtn_id, array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount));

			$afterHistoryId = $this->recordPlayerAfterActionWalletBalanceHistory($adjustment_type,
				$playerId, null, $rtn_id, $amount, null, null);
			$this->updateBalanceHistoryTransactionId($afterHistoryId, $rtn_id);

			$transaction['id'] = $rtn_id;
			// $this->load->model('daily_player_trans');
			// $this->daily_player_trans->update_today($transactionDetails);
			return $transaction;
		}
		return false;
	}

	/**
	 * detail: create cashback transaction for a certain player
	 */
	public function createCashbackTransaction($cashback_request, $admin_user_id) {

		if ($cashback_request) {
			$player_id = $cashback_request->player_id;
			$amount = $cashback_request->request_amount;

			$this->load->model(array('wallet_model', 'player_model', 'users'));

			$admin_user_id = $admin_user_id ? $admin_user_id : 1;

			$from_username = $this->users->getUsernameById($admin_user_id);
			$to_username = $this->player_model->getUsernameById($player_id);
			$transaction_type = self::CASHBACK;

			$beforeBalanceDetails = $this->wallet_model->getBalanceDetails($player_id);
			// clear frozen
			$success = $this->wallet_model->incMainCashbackOnBigWallet($player_id, $amount);

			if (!$success) {
				$this->utils->error_log('cashback fail', $cashback_request->id, 'amount', $amount, $beforeBalanceDetails);
				return false;
			}

			$afterBalanceDetails = $this->wallet_model->getBalanceDetails($player_id);

			$transactionDetails = array(
				'amount' => $amount,
				'transaction_type' => $transaction_type,
				'from_id' => $admin_user_id,
				'from_type' => self::ADMIN,
				'from_username' => $from_username,
				'to_id' => $player_id,
				'to_type' => self::PLAYER,
				'to_username' => $to_username,
				'note' => 'Add Cashback ' . $amount . ' to ' . $player_id . ' for ' . $cashback_request->request_datetime,
				'sub_wallet_id' => 0,
				'status' => self::APPROVED,
				'flag' => self::MANUAL,
				'created_at' => $this->utils->getNowForMysql(),
				'external_transaction_id' => $cashback_request->id,
				'trans_date' => $this->utils->getTodayForMysql(),
				'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
				'trans_year' => $this->utils->getThisYearForMysql(),

				'before_balance' => $beforeBalanceDetails['total_balance'],
				'after_balance' => $afterBalanceDetails['total_balance'],
				'total_before_balance' => $beforeBalanceDetails['total_balance'],
				'changed_balance' => $this->utils->encodeJson(array(
					'before' => $beforeBalanceDetails,
					'after' => $afterBalanceDetails,
					'amount' => $amount)
				),
			);

			$this->insertRow($transactionDetails);

			// $this->updateChangedBalance($rlt, array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $cashback_amount));

			$afterHistoryId = $this->recordPlayerAfterActionWalletBalanceHistory($transaction_type,
				$player_id, null, $success, $amount);
			$this->updateBalanceHistoryTransactionId($afterHistoryId, $success);

			return true;
		}

		return false;
	}

	/**
	 * detail: insert/add auto cashback transaction
	 *
	 * note: all parameters are mandatory
	 *
	 * @param int $playerId transaction to_id field
	 * @param double $cashback_amount transaction field
	 * @param int $from_id transaction field
	 * @param string $total_date this is a external_transaction_id field
	 *
	 * @return Boolean
	 */
	public function insertAutoCashbackTransaction($player_id, $cashback_amount, $from_id, $total_date) {
		// load model
		$this->load->model(array('wallet_model', 'player_model', 'users'));
		$type_id = self::PLAYER;

		$from_id = $from_id ? $from_id : 1;

		$transaction_type = self::AUTO_ADD_CASHBACK_TO_BALANCE;

		$from_username = $this->users->getUsernameById($from_id);
		$to_username = $this->player_model->getUsernameById($player_id);

		$beforeBalanceDetails = $this->wallet_model->getBalanceDetails($player_id);
		$rlt = $this->wallet_model->incMainCashbackOnBigWallet($player_id, $cashback_amount);
		$afterBalanceDetails = $this->wallet_model->getBalanceDetails($player_id);

		if ($rlt) {

			$data = array(
				'amount' => $cashback_amount,
				'transaction_type' => $transaction_type,
				'from_id' => $from_id,
				'from_type' => self::ADMIN,
				'from_username' => $from_username,
				'to_id' => $player_id,
				'to_type' => self::PLAYER,
				'to_username' => $to_username,
				'note' => 'Auto Cashback ' . $cashback_amount . ' to ' . $player_id . ' for ' . $total_date,
				'sub_wallet_id' => 0,
				'status' => self::APPROVED,
				'flag' => self::PROGRAM,
				'created_at' => $this->utils->getNowForMysql(),
				'external_transaction_id' => $total_date,
				'trans_date' => $this->utils->getTodayForMysql(),
				'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
				'trans_year' => $this->utils->getThisYearForMysql(),

				'before_balance' => $beforeBalanceDetails['total_balance'],
				'after_balance' => $afterBalanceDetails['total_balance'],
				'total_before_balance' => $beforeBalanceDetails['total_balance'],
				'changed_balance' => $this->utils->encodeJson(array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $cashback_amount)),
			);
			$rlt = $this->insertRow($data);

			// $this->updateChangedBalance($rlt, array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $cashback_amount));

			$afterHistoryId = $this->recordPlayerAfterActionWalletBalanceHistory($transaction_type,
				$player_id, null, $rlt, $cashback_amount);
			$this->updateBalanceHistoryTransactionId($afterHistoryId, $rlt);
		}else{
			$this->utils->error_log('incMainCashbackOnBigWallet failed', $player_id, 'cashback_amount', $cashback_amount);
		}

		return $rlt;
	}

	/**
	 * detail: get all transactions with filtered by corresponding parameters
	 *
	 * @param array $criteria_1
	 * @param array $criteria_2
	 * @param int $offset
	 * @param int $limit
	 * @param string $orderby
	 * @param string $direction
	 *
	 * @return array
	 */
	public function getTransactionList($criteria_1 = array(), $criteria_2 = array(), $offset = 0, $limit = 5, $orderby = 'transactions.id', $direction = 'desc') {
		$this->db->from('transactions')->limit($limit, $offset);
		$this->db->order_by($orderby, $direction);

		if ($criteria_1) {
			$this->db->where($criteria_1);
		}

		if ($criteria_2) {
			if (is_array($criteria_2)) {
				foreach ($criteria_2 as $key => $value) {
					$this->db->where($key, $value, false);
				}
			} else {
				$this->db->where($criteria_2);
			}
		}

		return $this->db->get()->result_array();
	}

	/**
	 * detail: get transaction count with filtered by corresponding parameters
	 *
	 * @param array $criteria_1
	 * @param array $criteria_2
	 *
	 * @return array
	 */
	public function getTransactionCount($criteria_1 = array(), $criteria_2 = array()) {
		$this->db->from($this->tableName);

		if ($criteria_1) {
			$this->db->where($criteria_1);
		}

		if ($criteria_2) {
			if (is_array($criteria_2)) {
				foreach ($criteria_2 as $key => $value) {
					$this->db->where($key, $value, false);
				}
			} else {
				$this->db->where($criteria_2);
			}
		}

		return $this->db->count_all_results();
	}

	/**
	 * detail: get transaction minimum and maximum
	 *
	 * @param string $field
	 * @param array $criteria_1
	 * @param array $criteria_2
	 *
	 * @return array
	 */
	public function getTransactionMinMax($field = 'created_at', $criteria_1 = array(), $criteria_2 = array()) {
		$this->db->select_min($field, 'first');
		$this->db->select_max($field, 'last');
		$this->db->from('transactions');

		if ($criteria_1) {
			$this->db->where($criteria_1);
		}

		if ($criteria_2) {
			if (is_array($criteria_2)) {
				foreach ($criteria_2 as $key => $value) {
					$this->db->where($key, $value, false);
				}
			} else {
				$this->db->where($criteria_2);
			}
		}

		return $this->db->get()->row_array();
	}

	/**
	 * detail: get total deposit filtered by payment account id
	 *
	 * @param int $paymentAccountId
	 *
	 * @return string
	 */
	public function totalDepositByPaymentAccountId($paymentAccountId) {
		$this->db->select('sum(amount) as total_deposit', false)->from($this->tableName)
			->where('payment_account_id', $paymentAccountId)
			->where('transaction_type', self::DEPOSIT);

		$this->addWhereApproved();

		return $this->runOneRowOneField('total_deposit');
	}

	/**
	 * detail: get the total deposit of current day
	 *
	 * @return double or string
	 */
	public function getTotalDepositsToday($player_ids = null, $base_date = null) {
		// OGP-15002 Adding arg base_date
		$base_date_ts = strtotime("{$base_date} +0 day");

		$this->db->select('sum(amount) as total', false)
		    ->from($this->tableName)
		    ->where('transaction_type', self::DEPOSIT)
		    ->where('created_at >=', date('Y-m-d 00:00:00', $base_date_ts))
		    ->where('created_at <=', date('Y-m-d 23:59:59', $base_date_ts));
        if($player_ids){
            $this->db->where_in('to_id', $player_ids);
        }

		$this->addWhereApproved();

		return $this->runOneRowOneField('total');
	}

	/**
	 * Returns all-time sum of all deposit amounts, OGP-12377
	 * @param	none
	 * @return	double	Sum of transactions.amount where transaction_type = Transactions::DEPOSIT
	 */
	public function getTotalDepositsAllTime() {
		$this->db->select('sum(amount) as total', false)
		    ->from($this->tableName)
		    ->where('transaction_type', self::DEPOSIT)
		;

		$this->addWhereApproved();

		return floatval($this->runOneRowOneField('total'));
	}

	/**
	 * detail: get the total withdrawal of current day
	 *
	 * @return double or string
	 */
	public function getTotalWithdrawalsToday($player_ids = null, $base_date = null) {
		// OGP-15002 Adding arg base_date
		$base_date_ts = strtotime("{$base_date} +0 day");

		$this->db->select('sum(amount) as total', false)
		    ->from($this->tableName)
		    ->where('transaction_type', self::WITHDRAWAL)
		    ->where('created_at >=', date('Y-m-d 00:00:00', $base_date_ts))
		    ->where('created_at <=', date('Y-m-d 23:59:59', $base_date_ts));
        if($player_ids){
            $this->db->where_in('to_id', $player_ids);
        }
		$this->addWhereApproved();

		return $this->runOneRowOneField('total');
	}

	/**
	 * Returns all-time sum of all withdrawal amounts, OGP-12377
	 * @param	none
	 * @return	double	Sum of transactions.amount where transaction_type = Transactions::WITHDRAWAL
	 */
	public function getTotalWithdrawalsAllTime() {
		$this->db->select('sum(amount) as total', false)
		    ->from($this->tableName)
		    ->where('transaction_type', self::WITHDRAWAL)
		;

		$this->addWhereApproved();

		return floatval($this->runOneRowOneField('total'));
	}

	/**
	 * detail: get the highest total of deposit
	 *
	 * @param Boolean $current_day
	 * @param int $limit
	 *
	 * @return array
	 */
	public function getTopDepositSum($current_day = false, $limit = 10, $total_today = false, $base_date = null) {
		// OGP-15002 Adding arg base_date
		$base_date_ts = strtotime("{$base_date} +0 day");

		$this->db->select('player.playerid');
		$this->db->select('player.username');
        if(!$total_today){
            $this->db->select('transactions.amount as amount');
        }else{
            $this->db->select('sum(transactions.amount) as amount');
        }
		$this->db->select('transactions.created_at');
		$this->db->select('sale_orders.payment_flag');
		$this->db->select('payment_account.flag');
		$this->db->from('player');
		$this->db->where('transactions.to_type', self::PLAYER);
		$this->db->join($this->tableName, 'player.playerId = transactions.to_id', 'left');
		$this->db->join('sale_orders', 'sale_orders.transaction_id = transactions.id', 'left');
		$this->db->join('payment_account', 'payment_account.id = sale_orders.payment_account_id', 'left');
		$this->db->where('transactions.transaction_type', self::DEPOSIT);
		$this->db->where('transactions.status', self::APPROVED);
		if ($current_day) {
			$this->db->where('transactions.created_at >=', date('Y-m-d 00:00:00', $base_date_ts));
			$this->db->where('transactions.created_at <=', date('Y-m-d 23:59:59', $base_date_ts));
		}
        if($total_today){
            $this->db->group_by('player.playerid');
        }
		$this->db->order_by('amount', 'desc');
		$this->db->limit($limit);
        //$this->utils->debug_log('the data ---->', $this->db->_compile_select());
        $query = $this->db->get();
		$result = $query->result();
		foreach ($result as &$row) {
			//$row->deposit_method = empty($row->payment_flag) ? lang('lang.norecyet') : lang('sale_orders.payment_flag.' . $row->payment_flag);
			$row->deposit_method = $row->flag;
		}
		return $result;

	}

	public function getTopDeposits($start_date = NULL, $end_date = NULL, $limit = 10) {

		$this->db->select('p.username');
		$this->db->select_sum('t.amount', 'amount');
		$this->db->from('transactions as t');
		$this->db->join('player as p', 'p.playerId = t.to_id');
		$this->db->where('t.to_type', self::PLAYER);
		$this->db->where('t.status', self::APPROVED);
		$this->db->where('t.transaction_type', self::DEPOSIT);

		if ($start_date) {
			$start_date = date('Y-m-d 00:00:00', strtotime($start_date));
			$this->db->where('t.created_at >=', $start_date);
		}

		if ($end_date) {
			$end_date = date('Y-m-d 23:59:59', strtotime($end_date));
			$this->db->where('t.created_at <=', $end_date);
		}

		$this->db->group_by('t.to_id');
		$this->db->order_by('amount', 'desc');
		$this->db->limit($limit);

		$query = $this->db->get();

		return $query->result_array();
	}

	public function getTopWithdraws($start_date = NULL, $end_date = NULL, $limit = 10) {

		$this->db->select('p.username');
		$this->db->select_sum('t.amount', 'amount');
		$this->db->from('transactions as t');
		$this->db->join('player as p', 'p.playerId = t.to_id');
		$this->db->where('t.to_type', self::PLAYER);
		$this->db->where('t.status', self::APPROVED);
		$this->db->where('t.transaction_type', self::WITHDRAWAL);

		if ($start_date) {
			$this->db->where('t.created_at >=', $start_date);
		}

		if ($end_date) {
			$this->db->where('t.created_at <=', $end_date);
		}

		$this->db->group_by('t.to_id');
		$this->db->order_by('amount', 'desc');
		$this->db->limit($limit);

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * detail: get the highest deposit count
	 *
	 * @param Boolean $current_day
	 * @param int $limit
	 *
	 * @return array
	 */
	public function getTopDepositCount($current_day = false, $limit = 10, $base_date = null) {
		// OGP-15002 Adding arg base_date
		$base_date_ts = strtotime("{$base_date} +0 day");

		$this->db->select('player.playerid');
		$this->db->select('player.username');
		$this->db->select('count(1) as count', false);
		$this->db->select('sum(transactions.amount) as total', false);
		$this->db->from('player');
		$this->db->where('transactions.to_type', self::PLAYER);
		$this->db->join($this->tableName, 'player.playerId = transactions.to_id', 'left');
		$this->db->where('transactions.transaction_type', self::DEPOSIT);
		$this->db->where('transactions.status', self::APPROVED);
		if ($current_day) {
			$this->db->where('created_at >=', date('Y-m-d 00:00:00', $base_date_ts));
			$this->db->where('created_at <=', date('Y-m-d 23:59:59', $base_date_ts));
		}
		$this->db->group_by('player.playerId');
		$this->db->order_by('count', 'desc');
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * detial: get last deposit or base on the give number of days
	 *
	 * @param int $number_of_days
	 *
	 * @return array
	 */
	public function getLastDeposits($number_of_days = 7, $base_date = null, $disp_date = null) {
		if (empty($disp_date) && !empty($base_date)) {
			$disp_date = $base_date;
		}

		$this->db->select('DATE(created_at) date');
		$this->db->select('sum(transactions.amount) as total', false);
		$this->db->select('count(1) as count', false);
		$this->db->from($this->tableName);
		$this->db->where('transactions.transaction_type', self::DEPOSIT);
		$this->db->where('transactions.status', self::APPROVED);
		// OGP-15002 add argument base_date
		// $this->db->where('created_at >=', date('Y-m-d 00:00:00', strtotime('-6 days')));
		// $this->db->where('created_at <=', date('Y-m-d 23:59:59'));
		$this->db->where('created_at >=', date('Y-m-d 00:00:00', strtotime("{$base_date} -6 days")));
		$this->db->where('created_at <=', date('Y-m-d 23:59:59', strtotime("{$base_date} +0 days")));
		$this->db->group_by('DATE(created_at)');
		$this->db->order_by('date', 'desc');
		$query = $this->db->get();
		$result = $query->result();

		$data = array();
		foreach ($result as $row) {
			$data[$row->date] = $row;
		}

		$ret = array();
		for ($i = 0; $i < $number_of_days; $i++) {
			$date = date('Y-m-d', strtotime("{$base_date} -{$i} days"));
			$disp_date_i = date('Y-m-d', strtotime("{$disp_date} -{$i} days"));
			$ret[$disp_date_i] = isset($data[$date]) ? $data[$date] : null;
		}

		return $ret;
	}

	/**
	 * detail: get last withdraws or based on the given days
	 *
	 * @param int $number_of_days
	 *
	 * @return array
	 */
	public function getLastWithdraws($number_of_days = 7, $base_date = null, $disp_date = null) {
		if (empty($disp_date) && !empty($base_date)) {
			$disp_date = $base_date;
		}

		$this->db->select('DATE(created_at) date');
		$this->db->select('sum(transactions.amount) as total', false);
		$this->db->select('count(distinct transactions.to_id) as count', false);
		$this->db->from($this->tableName);
		$this->db->where('transactions.to_type', self::PLAYER);
		$this->db->where('transactions.transaction_type', self::WITHDRAWAL);
		$this->db->where('transactions.status', self::APPROVED);
		// OGP-15002 add argument base_date
		// $this->db->where('created_at >=', date('Y-m-d 00:00:00', strtotime('-6 days')));
		// $this->db->where('created_at <=', date('Y-m-d 23:59:59'));
		$this->db->where('created_at >=', date('Y-m-d 00:00:00', strtotime("{$base_date} -6 days")));
		$this->db->where('created_at <=', date('Y-m-d 23:59:59', strtotime("{$base_date} +0 days")));
		$this->db->group_by('DATE(created_at)');
		$this->db->order_by('date', 'desc');
		$query = $this->db->get();
		$result = $query->result();

		$data = array();
		foreach ($result as $row) {
			$data[$row->date] = $row;
		}

		$ret = array();
		for ($i = 0; $i < $number_of_days; $i++) {
			$date = date('Y-m-d', strtotime("{$base_date} -{$i} days"));
			$disp_date_i = date('Y-m-d', strtotime("{$disp_date} -{$i} days"));
			$ret[$disp_date_i] = isset($data[$date]) ? $data[$date] : null;
		}

		return $ret;
	}

	/**
	 * detail: get total deposited player
	 *
	 * @return int
	 */
	public function getTotalDepositedPlayer($player_ids = null, $base_date = null) {
		// OGP-15002 Adding arg base_date
		$base_date_ts = strtotime("{$base_date} +0 day");

		$this->db->select('COUNT(distinct to_id) count')
            ->from('transactions')
		    ->where('to_type', Transactions::PLAYER)
		    ->where('transaction_type', Transactions::DEPOSIT)
		    ->where('status', Transactions::APPROVED)
		    ->where('created_at >=', date('Y-m-d 00:00:00', $base_date_ts))
		    ->where('created_at <=', date('Y-m-d 23:59:59', $base_date_ts));
        if($player_ids){ $this->db->where_in('to_id', $player_ids); }
		return $this->runOneRowOneField('count');
	}

	/**
	 * detail: get today total deposit count
	 *
	 * @return int
	 */
	public function getTodayTotalDepositCount($player_ids = null, $base_date = null) {
		// OGP-15002 Adding arg base_date
		$base_date_ts = strtotime("{$base_date} +0 day");

		$this->db->select('COUNT(to_id) count')
		    ->from('transactions')
		    ->where('to_type', Transactions::PLAYER)
		    ->where('transaction_type', Transactions::DEPOSIT)
		    ->where('status', Transactions::APPROVED)
		    ->where('created_at >=', date('Y-m-d 00:00:00', $base_date_ts))
		    ->where('created_at <=', date('Y-m-d 23:59:59', $base_date_ts));
        if($player_ids){ $this->db->where_in('to_id', $player_ids); }
		return $this->runOneRowOneField('count');
	}

	/**
	 * detail: get total withdrawed player
	 *
	 * @return int
	 */
	public function getTotalWithdrawedPlayer($player_ids = null, $base_date = null) {
		// OGP-15002 Adding arg base_date
		$base_date_ts = strtotime("{$base_date} +0 day");

		$this->db->select('COUNT(distinct to_id) count')
		    ->from('transactions')
		    ->where('to_type', Transactions::PLAYER)
		    ->where('transaction_type', Transactions::WITHDRAWAL)
		    ->where('status', Transactions::APPROVED)
		    ->where('created_at >=', date('Y-m-d 00:00:00', $base_date_ts))
		    ->where('created_at <=', date('Y-m-d 23:59:59', $base_date_ts));
        if($player_ids){ $this->db->where_in('to_id', $player_ids); }
		return $this->runOneRowOneField('count');
	}

	/**
	 * detail: get total withdraw count
	 *
	 * @return int
	 */
	public function getTodayTotalWithdrawCount($player_ids = null, $base_date = null) {
		// OGP-15002 Adding arg base_date
		$base_date_ts = strtotime("{$base_date} +0 day");

		$this->db->select('COUNT(to_id) count')
            ->from('transactions')
		    ->where('to_type', Transactions::PLAYER)
		    ->where('transaction_type', Transactions::WITHDRAWAL)
		    ->where('status', Transactions::APPROVED)
		    ->where('created_at >=', date('Y-m-d 00:00:00', $base_date_ts))
		    ->where('created_at <=', date('Y-m-d 23:59:59', $base_date_ts));
        if($player_ids){ $this->db->where_in('to_id', $player_ids); }
		return $this->runOneRowOneField('count');
	}

	/**
	 * detail: get total deposit of a certain player
	 *
	 * @param int $playerId transation to_id field
	 * @return double or int
	 */
	public function getPlayerTotalDeposits($playerId, $date_from = null, $date_to = null) {
		$this->db->select('sum(amount) as total_deposit', false)->from($this->tableName)
			->where('to_id', $playerId)
			->where('to_type', self::PLAYER)
			->where('transaction_type', self::DEPOSIT);

        if(!empty($date_from)){
            $this->db->where('created_at >=', $date_from);
        }

        if(!empty($date_to)){
            $this->db->where('created_at <=', $date_to);
        }

		$this->addWhereApproved();

		return $this->runOneRowOneField('total_deposit');
	}

	/**
	 * detail: get total deposit count of a certain player
	 *
	 * @param int $playerId transation to_id field
	 * @return int
	 */
	public function getPlayerTotalDepositCount($playerId, $date_from = null, $date_to = null) {
		$this->db->select('count(id) as deposit_count', false)->from($this->tableName)
			->where('to_id', $playerId)
			->where('to_type', self::PLAYER)
			->where('transaction_type', self::DEPOSIT);

        if(!empty($date_from)){
            $this->db->where('created_at >=', $date_from);
        }

        if(!empty($date_to)){
            $this->db->where('created_at <=', $date_to);
        }

		$this->addWhereApproved();

		return $this->runOneRowOneField('deposit_count');
	}

	/**
	 * detail: get single max deposit amount of a certain player
	 *
	 * @param int $playerId transation to_id field
	 * @return int
	 */
	public function getPlayerSingleMaxDeposit($playerId, $date_from = null, $date_to = null) {
		$this->db->select('max(amount) as single_max_deposit', false)->from($this->tableName)
			->where('to_id', $playerId)
			->where('to_type', self::PLAYER)
			->where('transaction_type', self::DEPOSIT);

        if(!empty($date_from)){
            $this->db->where('created_at >=', $date_from);
        }

        if(!empty($date_to)){
            $this->db->where('created_at <=', $date_to);
        }

		$this->addWhereApproved();

		return $this->runOneRowOneField('single_max_deposit');
	}

    /**
     * detail: get total transfer of a certain player
     *
     * @param int $playerId
     * @return double or int
     */
    public function getPlayerTotalTransferBalance($playerId, $date_from = null, $date_to = null, $transaction_type = null) {
        $this->db->select('sum(amount) as total_deposit', false)
                ->from($this->tableName)
                ->where('to_id', $playerId)
                ->where('to_type', self::PLAYER);

        if(!empty($transaction_type)){
            $this->db->where('transaction_type', $transaction_type);
        }else{
            $this->db->where('transaction_type', self::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET);
        }

        if(!empty($date_from)){
            $this->db->where('created_at >=', $date_from);
        }

        if(!empty($date_to)){
            $this->db->where('created_at <=', $date_to);
        }

        $this->addWhereApproved();
        return $this->runOneRowOneField('total_deposit');
    }

	/**
	 * detail: get total withdraws of a certain player
	 *
	 * @param int $playerId transation to_id field
	 * @return double or int
	 */
	public function getPlayerTotalWithdrawals($playerId, $isToday = false, $date_from = null, $date_to = null) {
		$this->db->select('sum(amount) as total_withdrawal', false)->from($this->tableName)
			->where('to_id', $playerId)
			->where('to_type', self::PLAYER)
			->where('transaction_type', self::WITHDRAWAL);

		if ($isToday) {
			$this->db->where('trans_date', date('Y-m-d'));
		}

		//added by jhunel 4-14-2017
		if (!empty($date_from) && !empty($date_to)) {

			$this->db->where('created_at >=', $date_from);
			$this->db->where('created_at <=', $date_to);

		}

		$this->addWhereApproved();

		return $this->runOneRowOneField('total_withdrawal');
	}

	/**
	 * detail: get total withdraw count of a certain player
	 *
	 * @param int $playerId transation to_id field
	 * @return double or int
	 */
	public function getPlayerTotalWithdrawalCount($playerId, $isToday = false, $date_from = null, $date_to = null) {
		$this->db->select('count(id) as withdrawal_count', false)->from($this->tableName)
			->where('to_id', $playerId)
			->where('to_type', self::PLAYER)
			->where('transaction_type', self::WITHDRAWAL);

		if ($isToday) {
			$this->db->where('trans_date', date('Y-m-d'));
		}

		//added by jhunel 4-14-2017
		if (!empty($date_from) && !empty($date_to)) {

			$this->db->where('created_at >=', $date_from);
			$this->db->where('created_at <=', $date_to);

		}

		$this->addWhereApproved();

		return $this->runOneRowOneField('withdrawal_count');
	}

	public function getTransaction($id) {
		return $this->getOneRowById($id);
	}

	public function getTransactionByPlayerPromoId($saleOrderId, $playerPromoId) {
		$this->db->from($this->tableName)->where('order_id', $saleOrderId)->where('player_promo_id', $playerPromoId);
		return $this->runOneRow();
	}

	// CASHBACK TRANSACTIONS ---------------------------------------------------------------------------------------

	/**
	 * detail: check if the cashback transaction is exist of a certain player and date
	 *
	 * @param int $player_id transaction to_id field
	 * @param string $date transaction external_transaction_id field
	 * @return Boolean
	 */
	public function existsAutoCashbackTransaction($player_id, $date) {
		$this->db->from($this->tableName)
			->where('transaction_type', self::AUTO_ADD_CASHBACK_TO_BALANCE)
			->where('external_transaction_id', $date)
			->where('to_id', $player_id)
			->where('to_type', self::PLAYER);

		$this->addWhereApproved();

		return $this->runExistsResult();
	}

	/**
	 * detail: get total daily deposit of a certain payment account id
	 *
	 * @param int $paymentAccountId transactions payment account id
	 * @return double
	 */
	public function getTotalDailyDeposit($paymentAccountId) {
		$this->db->select_sum('amount');
		$this->db->from('transactions');
		$this->db->where('payment_account_id', $paymentAccountId);
		$this->db->where('transaction_type', self::DEPOSIT);
		$this->db->where('status', self::APPROVED);
		$this->db->where('trans_date', date('Y-m-d'));
		$this->db->group_by('payment_account_id');
		$this->db->limit(1);
		// $query = $this->db->get();
		// return @floatval($query->row()->amount);

		$amount = $this->runOneRowOneField('amount');
		return floatval($amount);
	}

	/**
	 * detail: get total deposit of a certain payment account id
	 *
	 * @param int $paymentAccountId transaction payment account id field
	 * @return double
	 */
	public function getTotalDeposit($paymentAccountId) {
		$this->db->select_sum('amount');
		$this->db->from('transactions');
		$this->db->where('payment_account_id', $paymentAccountId);
		$this->db->where('transaction_type', self::DEPOSIT);
		$this->db->where('status', self::APPROVED);
		$this->db->group_by('payment_account_id');
		$this->db->limit(1);

		$amount = $this->runOneRowOneField('amount');
		return floatval($amount);

		// $query = $this->db->get();

		// return @floatval($query->row()->amount);
	}

	public function getCashbackHistoryWLimit($playerId, $limit, $offset, $search, $is_count = false) {
		if ($is_count) {
			$this->db->select('count(id) as cnt');
		} else {
			$this->db->select('amount , created_at as receivedOn');
		}

		$this->db->from($this->tableName)
			->where('to_id', $playerId)
			->where('to_type', self::PLAYER)
			->where('transaction_type', self::AUTO_ADD_CASHBACK_TO_BALANCE)
		;

		$this->addWhereApproved();

		if (!empty($search['from'])) {
			$this->db->where("created_at >= ", $search['from']);
			$this->db->where("created_at <= ", $search['to']);
			// $this->db->where("external_transaction_id >= '" . $search['from'] . "' AND '" . $search['to'] . "'");
		}

		if (!$is_count) {
			$this->db->limit($limit, $offset);
		}

		$this->db->order_by('created_at', 'desc');
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			// foreach ($query->result_array() as $row) {
			// 	$data[] = $row;
			// }
			// //var_dump($data);exit();
			// return $data;
			if ($is_count) {
				return $this->getOneRowOneField($query, 'cnt');
			} else {
				return $query->result_array();
			}
		}
		return null;
	}

	/**
	 * Get latest entries of cashback history
	 *
	 * @param	int		$playerId	== player.playerId
	 * @param	integer	$pagelen	count of entries (rows) per page
	 * @param	integer $page		page number, starts at 0
	 * @used-by	Api::cashbackHistory_json()
	 *
	 * @return	array 	array of [ amount, received_at, status ] from transactions
	 */
	public function getLatestCashbackHistory($playerId, $pagelen = 10, $page = 0) {
		$this->db->select('amount , created_at as received_at, status');

		$this->db->from($this->tableName)
			->where('to_id', $playerId)
			->where('to_type', self::PLAYER)
			->where('transaction_type', self::AUTO_ADD_CASHBACK_TO_BALANCE)
			->order_by('created_at', 'desc')
			->limit($pagelen, $page * $pagelen)
		;

		// $this->addWhereApproved();

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return null;
	}

	/**
	 * detail: count deposit of a certain player
	 *
	 * @param int $playerId transaction to_id
	 * @param string $periodFrom transaction created_at
	 * @param string $periodTo transaction created_at
	 */
	public function countDepositByPlayerId($playerId, $periodFrom, $periodTo) {
		$this->db->select('count(id) as cnt', false)->from($this->tableName)
			->where('transaction_type', self::DEPOSIT)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId)
			->where('created_at >=', $periodFrom)
			->where('created_at <=', $periodTo);

		$this->addWhereApproved();

		return $this->runOneRowOneField('cnt');
	}

	/**
	 * detail: total all cashback of a certain player
	 *
	 * @param int $playerId transaction to_id field
	 * @return double
	 */
	public function sumCashback($playerId) {
		$this->db->select_sum('amount')->from($this->tableName)
			->where('transaction_type', self::AUTO_ADD_CASHBACK_TO_BALANCE)
			->where('to_id', $playerId)
			->where('to_type', self::PLAYER);

		$this->addWhereApproved();

		return $this->runOneRowOneField('amount');
	}

	// AFFILIATE MONTHLY EARNINGS -------------------------------------------------------------------------------------

	/**
	 * detail: pay monthly earnings of a certain player
	 *
	 * @param int $id transaction to_id field
	 * @param double $balance
	 * @param string $yearmonth
	 * @param string $notes transaction field
	 * @param string $created_at transaction field
	 *
	 * @return array
	 */
	public function payMonthlyEarnings($id, $balance, $yearmonth, $notes, $created_at) {
		// Transaction table
		$transactions = array(
			'amount' => $balance,
			'transaction_type' => self::AFFILIATE_MONTHLY_EARNINGS,
			'from_id' => 0,
			'from_type' => self::ADMIN,
			'to_id' => $id,
			'to_type' => self::AFFILIATE,
			'note' => $notes,
			'created_at' => $created_at,
		);

		$this->db->insert('transactions', $transactions);

		// Monthly earnings table
		$earnings = array(
			'paid_flag' => 1,
			'manual_flag' => 1,
			'note' => $notes,
			'updated_at' => $created_at,
		);

		// pay all previous unpaid

		$this->db->where('affiliate_id', $id);
		$this->db->where('year_month <=', $yearmonth);
		$this->db->where('balance >', 0);
		$this->db->where('paid_flag', 0);
		$this->db->update('monthly_earnings', $earnings);

		// adjust all next unpaid

		$this->db->where('affiliate_id', $id);
		$this->db->where('year_month >', $yearmonth);
		$this->db->where('balance >', 0);
		$this->db->where('paid_flag', 0);
		$result = $this->db->get('monthly_earnings');

		if ($result->num_rows() > 0) {
			foreach ($result->result() as $e) {
				$this->adjustMonthlyEarnings($e->affiliate_id, $balance, $e->year_month, $notes, $created_at, true);
			}
		}
	}

	/**
	 * detail: pay all earnings
	 *
	 * @param string $year_month
	 * @param double $min_amount
	 *
	 * @return Boolean
	 */
	public function payAllEarnigns($year_month, $min_amount) {
		$this->db->where('year_month', $year_month);
		$this->db->where('paid_flag', 0);
		$this->db->where('balance >=', $min_amount);
		$result = $this->db->get('monthly_earnings');

		if ($result->num_rows() > 0) {
			foreach ($result->result() as $me) {
				$id = $me->affiliate_id;
				$balance = $me->balance;
				$yearmonth = $year_month;
				$notes = "";
				$created_at = date('Y-m-d H-m-s');
				$this->payMonthlyEarnings($id, $balance, $yearmonth, $notes, $created_at);
			}
			return true;
		} else {
			return false;
		}

	}

	/**
	 * detail: adjust monthly earnings
	 *
	 * @param int $id transaction to_id field
	 * @param double $balance transaction field
	 * @param string $yearmonth
	 * @param string $notes transaction field
	 * @param string $created_at transaction field
	 * @param Boolean $deduct
	 *
	 * @return Boolean
	 */
	public function adjustMonthlyEarnings($id, $balance, $yearmonth, $notes, $created_at, $deduct = false) {
		// Transactions table
		if (isSet($_POST['balance']) && ($_POST['balance'] > 0)) {
			$transactions = array(
				'amount' => $balance,
				'transaction_type' => self::AFFILIATE_MONTHLY_EARNINGS,
				'from_id' => 0,
				'from_type' => self::ADMIN,
				'to_id' => $id,
				'to_type' => self::AFFILIATE,
				'note' => $notes,
				'created_at' => $created_at,
			);
		} else {
			$transactions = array(
				'amount' => $balance * -1,
				'transaction_type' => self::AFFILIATE_MONTHLY_EARNINGS,
				'from_id' => $id,
				'from_type' => self::AFFILIATE,
				'to_id' => 0,
				'to_type' => self::ADMIN,
				'note' => $notes,
				'created_at' => $created_at,
			);

		}

		// $this->db->insert('transactions', $transactions);

		// Monthly earnings table
		$earnings = array(
			'paid_flag' => 1,
			'manual_flag' => 1,
			'note' => $notes,
			'updated_at' => $created_at,
		);

		// pay all previous unpaid

		$this->db->where('affiliate_id', $id);
		$this->db->where('year_month <=', $yearmonth);
		$this->db->where('balance >', 0);
		$this->db->where('paid_flag', 0);
		$this->db->update('monthly_earnings', $earnings);

		// adjust all next unpaid

		$this->db->where('affiliate_id', $id);
		$this->db->where('year_month >', $yearmonth);
		$this->db->where('balance >', 0);
		$this->db->where('paid_flag', 0);
		$result = $this->db->get('monthly_earnings');

		if ($result->num_rows() > 0) {
			foreach ($result->result() as $e) {
				$this->adjustMonthlyEarnings($e->affiliate_id, $balance, $e->year_month, $notes, $created_at, true);
			}
		}

		// Monthly earning table
		if ($deduct) {
			$this->db->where('affiliate_id', $id);
			$this->db->where('year_month', $yearmonth);
			$this->db->where('paid_flag', 0);
			$result = $this->db->get('monthly_earnings');

			if ($result->num_rows() > 0) {
				$balance = $result->result()[0]->balance - $balance;
			}

			$adjustment = array(
				'type' => 2,
				'paid_flag' => 0,
				'manual_flag' => 1,
				'note' => $notes,
				'updated_at' => $created_at,
				'balance' => $balance,
				'affiliate_id' => $id,
				'year_month' => $yearmonth,
			);
		} else {
			$adjustment = array(
				'type' => 2,
				'paid_flag' => 0,
				'manual_flag' => 1,
				'note' => $notes,
				'updated_at' => $created_at,
				'balance' => $balance,
				'affiliate_id' => $id,
				'year_month' => $yearmonth,
			);
		}

		$this->db->insert('monthly_earnings', $adjustment);
		$lastId = $this->db->insert_id();

		$earnings = array(
			'type' => 1,
			'paid_flag' => 1,
			'manual_flag' => 1,
			'note' => $notes,
			'updated_at' => $created_at,
		);

		$this->db->where('affiliate_id', $id);
		$this->db->where('id <', $lastId);
		$this->db->where('year_month', $yearmonth);
		$this->db->update('monthly_earnings', $earnings);
	}

	protected function addWhereApproved() {
		$this->db->where('status', self::APPROVED);
	}

	/**
	 * detail: get total deposit of a certain player
	 *
	 * @param int $playerId transaction to_id field
	 * @param string $year
	 * @param string $month
	 *
	 * @return double or string
	 */
	public function getTotalDepositByPlayer($playerId, $year = null, $month = null) {
		$this->db->select_sum('amount')
			->from($this->tableName)
			->where('transaction_type', self::DEPOSIT)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId);

		$this->addWhereApproved();

		if (!empty($year) && !empty($month)) {
			$this->db->where('date_format(created_at,"%Y%m")', $this->utils->getStringYearMonth($year, $month));
		}

		return $this->runOneRowOneField('amount');
	}

	/**
	 * detail: get all daily transactions
	 *
	 * @param int $transaction_types
	 * @param string $start
	 * @param string $end
	 *
	 * @return array
	 */
	public function getAllDailyTransactions($transaction_types, $start, $end) {

		$str = implode(',', $transaction_types);
		$sql = <<<EOD
 SELECT
 transactions.created_at,
 transactions.transaction_type,
 (CASE transactions.from_type WHEN 1 THEN fromUser.username WHEN 2 THEN fromPlayer.username WHEN 3 THEN fromAffiliate.username ELSE NULL END) from_username,
 (CASE transactions.to_type WHEN 1 THEN toUser.username WHEN 2 THEN toPlayer.username WHEN 3 THEN toAffiliate.username ELSE NULL END) to_username,
 external_system.system_code AS subwallet,
 promotype.promoTypeName,
 transactions.flag,
 transactions.external_transaction_id AS external_transaction_id,
 transactions.note ,
 transactions.amount,
 transactions.before_balance,
 transactions.after_balance,
 transactions.total_before_balance
FROM (transactions)
LEFT JOIN adminusers fromUser ON transactions.from_type = 1 AND fromUser.userId = transactions.from_id
LEFT JOIN player fromPlayer ON transactions.from_type = 2 AND fromPlayer.playerId = transactions.from_id
LEFT JOIN affiliates fromAffiliate ON transactions.from_type = 3 AND fromAffiliate.affiliateId = transactions.from_id
LEFT JOIN adminusers toUser ON transactions.to_type = 1 AND toUser.userId = transactions.to_id
LEFT JOIN player toPlayer ON transactions.to_type = 2 AND toPlayer.playerId = transactions.to_id
LEFT JOIN affiliates toAffiliate ON transactions.to_type = 3 AND toAffiliate.affiliateId = transactions.to_id
LEFT JOIN external_system ON external_system.id = transactions.sub_wallet_id
LEFT JOIN promotype ON promotype.promotypeId = transactions.promo_category
LEFT JOIN sale_orders ON sale_orders.transaction_id = transactions.id
WHERE transactions.created_at>=? and transactions.created_at<=?
 AND transactions.transaction_type IN ({$str})
and transactions.status = ?
ORDER BY transactions.created_at ASC
EOD;

		$query = $this->db->query($sql, array($start, $end, self::APPROVED));
		//echo $this->db->last_query();
		if ($query->num_rows() > 0) {
			return array(
				'total' => $query->num_rows(),
				'data' => $query->result_array(),
			);
		}

	}

	/**
	 * detail: get transaction summary
	 *
	 * @param int $transaction_types
	 * @param string $start
	 * @param string $end
	 *
	 * @return array
	 */
	public function getEachSummary($transaction_types, $start, $end) {

		$str = implode(',', $transaction_types);

		$sql = <<<EOD
SELECT sale_orders.payment_type_name, sale_orders.payment_account_name, SUM(transactions.amount) total_amount
FROM (transactions)
LEFT JOIN sale_orders ON sale_orders.transaction_id = transactions.id
WHERE transactions.created_at>=? and transactions.created_at<=?
AND transactions.transaction_type  IN  ({$str})
AND transactions.status=?
GROUP BY sale_orders.payment_account_id
EOD;

		$query = $this->db->query($sql, array($start, $end, self::APPROVED));
		if ($query->num_rows() > 0) {
			return array(
				'total' => $query->num_rows(),
				'data' => $query->result_array(),
			);
		}

	}

	/**
	 * detial: get each bonus salary
	 *
	 * @param int $transation_types
	 * @param string $start
	 * @param string $end
	 *
	 * @return array
	 */
	public function getEachBonusSummary($transaction_types, $start, $end) {

		$str = implode(',', $transaction_types);

		$sql = <<<EOD
SELECT transactions.transaction_type, external_system.system_code, SUM(transactions.amount) total_amount FROM (transactions)
LEFT JOIN sale_orders ON sale_orders.transaction_id = transactions.id
LEFT JOIN external_system ON external_system.id = transactions.sub_wallet_id
WHERE transactions.created_at>=? and transactions.created_at<=?
AND transactions.transaction_type  IN  ({$str})
AND transactions.status=?
GROUP BY transactions.transaction_type, transactions.sub_wallet_id
EOD;

		$query = $this->db->query($sql, array($start, $end, self::APPROVED));
		if ($query->num_rows() > 0) {
			return array(
				'total' => $query->num_rows(),
				'data' => $query->result_array(),
			);
		}

	}

	/**
	 * detail: get total balance on each transaction type
	 *
	 * @param int $transaction_type
	 * @param string $start
	 * @param string $end
	 *
	 * @return array
	 */
	public function getTotalsPerTransactionType($transaction_types, $start, $end) {

		$str = implode(',', $transaction_types);

		$sql = <<<EOD
SELECT SUM(total_amount) AS total_amount FROM
(SELECT to_id as user, transaction_type,
(CASE transactions.from_type WHEN 1 THEN fromUser.username WHEN 2 THEN fromPlayer.username WHEN 3 THEN fromAffiliate.username ELSE NULL END) from_username,
(CASE transactions.to_type WHEN 1 THEN toUser.username WHEN 2 THEN toPlayer.username WHEN 3 THEN toAffiliate.username ELSE NULL END) to_username,
SUM(amount) as total_amount
FROM transactions
LEFT JOIN adminusers fromUser ON transactions.from_type = 1 AND fromUser.userId = transactions.from_id
LEFT JOIN player fromPlayer ON transactions.from_type = 2 AND fromPlayer.playerId = transactions.from_id
LEFT JOIN affiliates fromAffiliate ON transactions.from_type = 3 AND fromAffiliate.affiliateId = transactions.from_id
LEFT JOIN adminusers toUser ON transactions.to_type = 1 AND toUser.userId = transactions.to_id
LEFT JOIN player toPlayer ON transactions.to_type = 2 AND toPlayer.playerId = transactions.to_id
LEFT JOIN affiliates toAffiliate ON transactions.to_type = 3 AND toAffiliate.affiliateId = transactions.to_id
WHERE transactions.created_at>=? and transactions.created_at<=?
AND transactions.transaction_type IN  ({$str})
AND transactions.status=?
GROUP BY to_id) AS A ;
EOD;

		$query = $this->db->query($sql, array($start, $end, self::APPROVED));
		$total_amount = $query->row_array()['total_amount'];
		if ($total_amount > 0) {
			return $total_amount;
		} else {
			return 0;
		}

	}

	/**
	 * detiail: get transactions summary records
	 *
	 * @param int $transaction_types
	 * @param string $start
	 * @param string $end
	 *
	 * @return array or string
	 */
	public function getTransactionsSummary($transaction_types, $start, $end) {

		$str = implode(',', $transaction_types);

		$sql = <<<EOD
SELECT transactions.transaction_type, external_system.system_code, SUM(transactions.amount) total_amount FROM (transactions)
LEFT JOIN sale_orders ON sale_orders.transaction_id = transactions.id
LEFT JOIN external_system ON external_system.id = transactions.sub_wallet_id
WHERE transactions.created_at>=? and transactions.created_at<=?
AND transactions.transaction_type  IN  ({$str})
AND transactions.status = ?
GROUP BY transactions.transaction_type, transactions.sub_wallet_id
EOD;

		$query = $this->db->query($sql, array($start, $end, Transactions::APPROVED));

		$this->utils->printLastSQL();

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return null;
		}

	}

	/**
	 * detail: select the newest transaction from the array
	 *
	 * @param array $rows
	 *
	 * @return array
	 */
	public function selectNewestTransactions($rows) {
		$today = date("Y-m-d H:i:s");
		$sql = 'SELECT * FROM transactions  WHERE created_at <= ? order by created_at DESC LIMIT ? ';
		$query = $this->db->query($sql, array($today, $rows));
		return array(
			'total' => $query->num_rows(),
			'data' => $query->result_array(),
		);
	}

	/**
	 * detail: select the newest withdrawals from the array
	 *
	 * @param array $rows
	 *
	 * @return array
	 */
	public function selectNewestWithdrawals($rows) {
		$today = date("Y-m-d H:i:s");
		$sql = 'SELECT * FROM transactions  WHERE created_at <= ? AND transaction_type = ?  order by created_at DESC LIMIT ? ';
		$query = $this->db->query($sql, array($today, self::WITHDRAWAL, $rows));
		return array(
			'total' => $query->num_rows(),
			'data' => $query->result_array(),
		);
	}

	/**
	 * detail: get player total for a certain total
	 *
	 * @param int $playerId transaction to_id
	 * @param string $from transaction created_at
	 * @param string $to transaction created_at
	 *
	 * @return array
	 */
	public function getPlayerTotalsByPlayers($playerIds, $from = null, $to = null) {
		$totals = array();

		if (!empty($playerIds)) {

			$this->db->select('transaction_type, sum(amount) as total_amount', false)->from($this->tableName)
				->where('to_type', self::PLAYER);
			if ($from != null && $to != null) {
				$this->db->where('created_at >=', $from)
					->where('created_at <=', $to);
			}
			$this->db->group_by('transaction_type');

			if (is_array($playerIds)) {
				$this->db->where_in('to_id', $playerIds);
			} else {
				$this->db->where('to_id', $playerIds);
			}

			$this->addWhereApproved();

			$rows = $this->runMultipleRow();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					if (isset($totals[$row->transaction_type])) {
						$totals[$row->transaction_type] += $row->total_amount;
					} else {
						$totals[$row->transaction_type] = $row->total_amount;
					}
				}
			}
		}
		return $totals;
	}

	/**
	 * detial: get player totals
	 *
	 * @return int or double
	 */
	public function getPlayerTotals() {
		$totals = array();
		$this->db->select('transaction_type, amount', false)->from($this->tableName);
		$this->addWhereApproved();
		$rows = $this->runMultipleRow();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				if (isset($totals[$row->transaction_type])) {
					$totals[$row->transaction_type] += $row->amount;
				} else {
					$totals[$row->transaction_type] = $row->amount;
				}
			}
		}
		return $totals;
	}

	/**
	 * detail: get the total balance deposit withdrawal bonus cashback of a certain player
	 *
	 * @param int $playerId transaction to_id field
	 * @param string $from
	 * @param string $to
	 *
	 * @return array
	 */
	public function getTotalBalDepositWithdrawalBonusCashbackByPlayers($playerIds, $from = null, $to = null) {
		$totalDeposit = 0;
		$totalWithdrawal = 0;
		$totalBonus = 0;
		$totalCashback = 0;
		$totalAddBal = 0;
		$totalSubtractBal = 0;
		$totalBirthdayBonus = 0;

		$totals = $this->getPlayerTotalsByPlayers($playerIds, $from, $to);

		if (!empty($totals)) {
			foreach ($totals as $transType => $amount) {
				switch ($transType) {
				case self::DEPOSIT:
					$totalDeposit += $amount;
					break;
				case self::MANUAL_ADD_BALANCE:
					$totalAddBal += $amount;
					break;
				case self::WITHDRAWAL:
					$totalWithdrawal += $amount;
					break;
				case self::MANUAL_SUBTRACT_BALANCE:
					$totalSubtractBal += $amount;
					break;
				case self::BIRTHDAY_BONUS:
				case self::ADD_BONUS:
				case self::SUBTRACT_BONUS:
				case self::MEMBER_GROUP_DEPOSIT_BONUS:
				case self::PLAYER_REFER_BONUS:
					if ($transType == self::SUBTRACT_BONUS) {
						$totalBonus -= $amount;
					} else {
						$totalBonus += $amount;
					}
					break;
				case self::AUTO_ADD_CASHBACK_TO_BALANCE:
					$totalCashback += $amount;
					break;
				case self::BIRTHDAY_BONUS:
					$totalBirthdayBonus += $amount;
					break;
				}
			}
		}

		return array($totalDeposit, $totalWithdrawal, $totalBonus, $totalCashback, $totalAddBal, $totalSubtractBal);
	}

	/**
	 * detail: get total deposit withdrawal bonus cashback of a certain player
	 *
	 * @param int $playerIds transaction to_id field
	 * @param string $from transaction created_t
	 * @param string $to transaction created_at
	 *
	 * @return array
	 */
	public function getTotalDepositWithdrawalBonusCashbackByPlayers($playerIds, $from = null, $to = null, $add_manual=false) {
		// OGP-1614: $add_manual used to default to true, enabling execution of 'add_manual' code section below.
		// In this way, MANUAL_ADD_BALANCE amounts are added $totalDeposit, and calculated $totalDeposit does not
		// match that shown in SBE player info.  This was doubly checked by summation of deposit transaction,
		// which agrees to total deposit in player info.  Now default for $add_manual was changed to false.
		$totalDeposit = 0;
		$totalWithdrawal = 0;
		$totalBonus = 0;
		$totalCashback = 0;
		$totalBirthdayBonus = 0;

		$totals = $this->getPlayerTotalsByPlayers($playerIds, $from, $to);

		if (!empty($totals)) {
			foreach ($totals as $transType => $amount) {
				switch ($transType) {
				case self::DEPOSIT:
					$totalDeposit += $amount;
					break;
				case self::WITHDRAWAL:
					$totalWithdrawal += $amount;
					break;
				case self::BIRTHDAY_BONUS:
				case self::ADD_BONUS:
				case self::SUBTRACT_BONUS:
				case self::MEMBER_GROUP_DEPOSIT_BONUS:
				case self::PLAYER_REFER_BONUS:
					if ($transType == self::SUBTRACT_BONUS) {
						$totalBonus -= $amount;
					} else {
						$totalBonus += $amount;
					}
					break;
				case self::AUTO_ADD_CASHBACK_TO_BALANCE:
					$totalCashback += $amount;
					break;
				}

				if($add_manual){

					if($transType==self::MANUAL_ADD_BALANCE){
						$totalDeposit += $amount;
					}elseif($transType==self::MANUAL_SUBTRACT_BALANCE){
						$totalWithdrawal += $amount;
					}

				}

			}
		}

		return array($totalDeposit, $totalWithdrawal, $totalBonus, $totalCashback);
	}

	/**
	 * detail: get total deposit withdrawal bonus cashback of a certain player
	 *
	 * @param int $playerIds transaction to_id field
	 * @param string $from transaction created_t
	 * @param string $to transaction created_at
	 *
	 * @return array
	 */
	public function getTotalDepositBonusAndBirthdayByPlayers($playerIds, $from = null, $to = null) {
		$totalDeposit = 0;
		$totalBirthdayBonus = 0;
		$totalBonus = 0;
		$availableCashback = 0;

		$totals = $this->getPlayerTotalsByPlayers($playerIds, $from, $to);
		if (!empty($totals)) {
			foreach ($totals as $transType => $amount) {
				switch ($transType) {
				case self::DEPOSIT:
					$totalDeposit += $amount;
					break;
				case self::BIRTHDAY_BONUS:
					$totalBirthdayBonus += $amount;
					break;
				case self::BIRTHDAY_BONUS:
				case self::ADD_BONUS:
				case self::SUBTRACT_BONUS:
				case self::MEMBER_GROUP_DEPOSIT_BONUS:
				case self::PLAYER_REFER_BONUS:
					if ($transType == self::SUBTRACT_BONUS) {
						$totalBonus -= $amount;
					} else {
						$totalBonus += $amount;
					}
					break;
				case self::AUTO_ADD_CASHBACK_TO_BALANCE:
					$availableCashback += $amount;
					break;
				}
			}
		}

		return array(self::DEPOSIT => $totalDeposit, self::BIRTHDAY_BONUS => $totalBirthdayBonus, "totalBonus" => $totalBonus, "availableCashback" => $availableCashback);
	}

	/**
	 * detail: get total deposit withdrawal bonus cashback
	 */
	public function getTotalDepositWithdrawalBonusCashback() {
		$totalDeposit = 0;
		$totalWithdrawal = 0;
		$totalBonus = 0;
		$totalCashback = 0;

		$totals = $this->getPlayerTotals();
		if (!empty($totals)) {
			foreach ($totals as $transType => $amount) {
				switch ($transType) {
				case self::DEPOSIT:
				case self::MANUAL_ADD_BALANCE:
					$totalDeposit += $amount;
					break;
				case self::WITHDRAWAL:
				case self::MANUAL_SUBTRACT_BALANCE:
					$totalWithdrawal += $amount;
					break;
				case self::BIRTHDAY_BONUS:
				case self::ADD_BONUS:
				case self::SUBTRACT_BONUS:
				case self::MEMBER_GROUP_DEPOSIT_BONUS:
				case self::PLAYER_REFER_BONUS:
					if ($transType == self::SUBTRACT_BONUS) {
						$totalBonus -= $amount;
					} else {
						$totalBonus += $amount;
					}
					break;
				case self::AUTO_ADD_CASHBACK_TO_BALANCE:
					$totalCashback += $amount;
					break;
				}
			}
		}

		return array($totalDeposit, $totalWithdrawal, $totalBonus, $totalCashback);
	}

	/**
	 * detail: get total deposit withdrawal bonus for a certain player
	 *
	 * @param int $playerIds transaction to_id field
	 * @param string $from transaction created_at
	 * @param string $to transaction created_at
	 *
	 * @return array
	 */
	public function getPlayerTotalDepositWithdrawalBonusByPlayers($playerIds, $from, $to) {

		$totalDeposit = 0;
		$totalWithdrawal = 0;
		$totalBonus = 0;

		$totals = $this->getPlayerTotalsByPlayers($playerIds, $from, $to);
		if (!empty($totals)) {
			foreach ($totals as $transType => $amount) {
				switch ($transType) {
				case self::DEPOSIT:
				case self::MANUAL_ADD_BALANCE:
					$totalDeposit += $amount;
					break;
				case self::WITHDRAWAL:
				case self::MANUAL_SUBTRACT_BALANCE:
					$totalWithdrawal += $amount;
					break;
				case self::BIRTHDAY_BONUS:
				case self::ADD_BONUS:
				case self::SUBTRACT_BONUS:
				case self::MEMBER_GROUP_DEPOSIT_BONUS:
				case self::PLAYER_REFER_BONUS:
					if ($transType == self::SUBTRACT_BONUS) {
						$totalBonus -= $amount;
					} else {
						$totalBonus += $amount;
					}
					break;
				}
			}
		}
		return array($totalDeposit, $totalWithdrawal, $totalBonus);
	}

	/**
	 * detail: get the total deposit withdrawal bonus for a certain player and date range
	 *
	 * @param int $playerId transaction to_id
	 * @param string $from
	 * @param string $to
	 *
	 * @return array
	 */
	public function getPlayerTotalDepositWithdrawalBonusByDatetime($playerId, $from, $to) {
		return $this->getPlayerTotalDepositWithdrawalBonusByPlayers(array($playerId), $from, $to);
	}

	/**
	 * detail: get total deposit for a certain player and date range
	 *
	 * @param int $playerId transaction to_id field
	 * @param string $from transaction created_at
	 * @param string $to transaction created_at
	 *
	 * @return double or int
	 */
	public function getPlayerTotalDepositsByDatetime($playerId, $from, $to) {
		$this->db->select('sum(amount) as total', false)->from($this->tableName)
			->where('to_id', $playerId)
			->where('to_type', self::PLAYER)
			->where('transaction_type', self::DEPOSIT)
			->where('created_at >=', $from)
			->where('created_at <=', $to);

		$this->addWhereApproved();

		return $this->runOneRowOneField('total');
	}

	/**
	 * detail: calculate the total of transaction fees for a certain player
	 *
	 * @param int $player_id transaction to_id field
	 * @param string $start_date transaction created_at
	 * @param string $end_date transaction created_at
	 *
	 * @return double or sting
	 */
	public function sumTransactionFee($players_id, $start_date, $end_date) {
		if (!empty($players_id)) {
			$this->db->select_sum('amount')->from($this->tableName)
				->where('created_at >=', $start_date)
				->where('created_at <=', $end_date)
				->where_in('transaction_type', array(self::FEE_FOR_PLAYER, self::FEE_FOR_OPERATOR))
				->where('to_type', self::PLAYER)
				->where_in('to_id', $players_id);

			$amount = $this->runOneRowOneField('amount');
			return $amount !== NULL ? $amount : 0;
		} else {
			return 0;
		}
	}

    public function sumTransactionsDepositOrWithdrawal($players_id, $start_date, $end_date, $type = null) {
        if (!empty($players_id)) {
            $this->db
                ->select('sum(transactions.amount) as amount')
                ->from($this->tableName)
                ->where('created_at >=', $start_date)
                ->where('created_at <=', $end_date)
                ->where('to_type', self::PLAYER)
                ->where_in('to_id', $players_id);
            if(!empty($type)){
                $this->db->where('transaction_type', $type);
            }
            $this->addWhereApproved();

            $amount = $this->runOneRowOneField('amount');
            return $amount !== NULL ? $amount : 0;
        } else {
            return 0;
        }
    }

    /**
	 * detail: total of deposit and withdraw for a certain player and date range
	 *
	 * @param int $players_id transaction to_id field
	 * @param string $start_date transaction created_at
	 * @param string $end_date transaction created_at
	 *
	 * @return double or string
	 */
	public function sumDepositAndWithdraw($players_id, $start_date, $end_date) {
		$this->db->select_sum('amount')->from($this->tableName)
			->where('created_at >=', $start_date)
			->where('created_at <=', $end_date)
			->where_in('transaction_type', array(self::DEPOSIT, self::WITHDRAWAL))
			->where('to_type', self::PLAYER)
			->where_in('to_id', $players_id);

		return $this->runOneRowOneField('amount');
	}

	/**
	 * detail: count available deposit for pick up bonus for a certain player
	 *
	 * @param int $playerId transaction to_id field
	 *
	 * @return int
	 */
	public function countAvailableDepositForPickupBonus($playerId) {
		$this->db->select("count(distinct tr.id) as cnt", null)->from("transactions as tr");
		$this->db->join('random_bonus_history as rbh', 'rbh.deposit_transaction_id = tr.id', 'left');
		$this->db->where("tr.to_id", $playerId);
		$this->db->where("tr.to_type", self::PLAYER);
		$this->db->where("rbh.id IS NULL");
		$this->db->where("transaction_type", self::DEPOSIT);
		// $this->db->order_by('tr.created_at', 'desc');

		return $this->runOneRowOneField('cnt');
	}

	/**
	 * detail: get available deposit for pick up bonus for a certain player
	 *
	 * @param int $playerId transaction to_id field
	 *
	 * @return array
	 */
	public function getAvailableDepositForPickupBonus($playerId) {
		$this->db->select("tr.id as deposit_transaction_id,
						   tr.to_id,
						   tr.amount,
						   rbh.id,
						   rbh.bonus_mode")->from("transactions as tr");
		$this->db->join('random_bonus_history as rbh', 'rbh.deposit_transaction_id = tr.id', 'left');
		$this->db->where("tr.to_id", $playerId);
		$this->db->where("tr.to_type", self::PLAYER);
		$this->db->where("rbh.id IS NULL");
		$this->db->where("transaction_type", self::DEPOSIT);

		$this->db->order_by('tr.created_at', 'desc');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * detail: count deposits for a certain player
	 *
	 * @param int @playerId transaction to_id field
	 * @return int
	 */
	public function countDepositByPlayer($playerId) {
		$this->db->select('count(amount) as cnt')
			->from($this->tableName)
			->where('transaction_type', self::DEPOSIT)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId);

		$this->addWhereApproved();

		return $this->runOneRowOneField('cnt');
	}

	/**
	 * detail: get deposits for pick up for a certain player
	 *
	 * @param int $playerId transaction to_id field
	 * @return array
	 */
	public function getAnyDepositForPickupBonus($playerId, $limit = -1) {
		$this->db->select("tr.id as deposit_transaction_id,tr.to_id,tr.amount")
			->from("transactions as tr");
		$this->db->where("tr.to_id", $playerId);
		$this->db->where("tr.to_type", self::PLAYER);
		$this->db->where("transaction_type", self::DEPOSIT);

		$this->addWhereApproved();
		if ($limit > 0) {
			$this->db->limit($limit);
		}

		$this->db->order_by('tr.created_at');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * detail: count random bonuses
	 *
	 * @param int $promo_category_id transaction promo_category
	 * @return int
	 */
	public function countRandomBonus($promo_category_id = null) {
		$this->db->select('count(id) as cnt', null, false)->from($this->tableName)
			->where('transaction_type', self::RANDOM_BONUS);

		$this->addWhereApproved();

		if (!empty($promo_category_id)) {
			$this->where('promo_category', $promo_category_id);
		}

		return $this->runOneRowOneField('cnt');
	}

	/**
	 * detail: check if the deposit is exist for a certain player
	 *
	 * @param int $playerId transaction to_id field
	 * @return Boolean
	 */
	public function existsDepositByPlayer($playerId) {
		$this->db->from($this->tableName)
			->where('transaction_type', self::DEPOSIT)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId);

		$this->addWhereApproved();
		$this->limitOneRow();

		return $this->runExistsResult();
	}

	/**
	 * detail: sum of all deposits of a certain player
	 *
	 * @param int $playerId transaction to_id field
	 * @param string $periodFrom transaction created_at
	 * @param string $periodTo transaction created_at
	 *
	 * @return double or string
	 */
	public function sumDepositByPlayerId($playerId, $periodFrom, $periodTo) {
		$this->db->select('sum(amount) as sum_amount', false)->from($this->tableName)
			->where('transaction_type', self::DEPOSIT)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId)
			->where('created_at >=', $periodFrom)
			->where('created_at <=', $periodTo);

		$this->addWhereApproved();

		return $this->runOneRowOneField('sum_amount');
	}

	/**
	 * detail: get last withdraw date and time for a certain player
	 *
	 * @param int $playerId transaction to_id field
	 * @return string
	 */
	public function getLastWithdrawDatetime($playerId) {
		$this->db->select()->from($this->tableName)->where('transaction_type', self::WITHDRAWAL)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId);

		$this->addWhereApproved();

		$this->db->order_by('transactions.created_at', 'desc');

		$this->limitOneRow();

		return $this->runOneRowOneField('created_at');
	}

	/**
	 * detail: sum of deposit amount for a certain player
	 *
	 * @param int $playerId transaction to_id field
	 * @param string $from_datetime transaction created_at
	 * @param string $to_datetime transaction created_at
	 * @param double $min_amount transaction amount field
	 *
	 * @return double
	 */
	public function sumDepositAmount($playerId, $from_datetime, $to_datetime, $min_amount) {
		$this->db->select('sum(amount) as sum_amount', false)->from($this->tableName)
			->where('transaction_type', self::DEPOSIT)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId)
			->where('created_at >=', $from_datetime)
			->where('created_at <=', $to_datetime)
			->where('amount >=', $min_amount);

		$this->addWhereApproved();

		return $this->runOneRowOneField('sum_amount');

	}

	/**
	 * detail: sum withdraw amounts for a certain player
	 *
	 * @param int $playerId transaction to_id field
	 * @param string $from_datetime transaction created_at
	 * @param string $to_datetime transaction created_at
	 * @param double $min_amount transaction amount
	 *
	 * @return double
	 */
	public function sumWithdrawAmount($playerId, $from_datetime, $to_datetime, $min_amount) {
		$this->db->select('sum(amount) as sum_amount', false)->from($this->tableName)
			->where('transaction_type', self::WITHDRAWAL)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId)
			->where('created_at >=', $from_datetime)
			->where('created_at <=', $to_datetime)
			->where('amount >=', $min_amount);

		$this->addWhereApproved();

		return $this->runOneRowOneField('sum_amount');

	}

	public function getFirstDeposit($playerId, $from_datetime = NULL){
        $this->db->from($this->tableName)->where('transaction_type', self::DEPOSIT)
            ->where('to_type', self::PLAYER)
            ->where('to_id', $playerId);

        if(NULL !== $from_datetime){
            $this->db->where('created_at >=', $from_datetime);
        }

        $this->addWhereApproved();

        $this->db->order_by('transactions.created_at', 'asc');

        $this->limitOneRow();

        return $this->runOneRow();
	}

	/**
	 * detail: get first deposit amount for a certain player
	 *
	 * @param int $playerId transaction to_id field
	 * @param string $from_datetime transaction created_at field
	 *
	 * @return double
	 */
	public function getFirstDepositAmount($playerId, $from_datetime = NULL) {
		$row = $this->getFirstDeposit($playerId, $from_datetime);

		return (empty($row)) ? NULL : $row->amount;
	}

	/**
	 * detail: get the last deposit amount of a certain player
	 *
	 * @param int $playerId  transaction to_id
	 * @return double
	 */
	public function getLastDepositAmount($playerId) {
		$this->db->from($this->tableName)->where('transaction_type', self::DEPOSIT)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId);

		$this->addWhereApproved();

		$this->db->order_by('transactions.created_at', 'desc');

		$this->limitOneRow();

		return $this->runOneRowOneField('amount');
	}

	/**
	 * detail: get the last deposit date of a certain player
	 *
	 * @param int $playerId  transaction to_id
	 * @return double
	 */
	public function getLastDepositDate($playerId) {
		$this->db->from($this->tableName)->where('transaction_type', self::DEPOSIT)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId);

		$this->addWhereApproved();

		$this->db->order_by('transactions.created_at', 'desc');

		$this->limitOneRow();

		return $this->runOneRowOneField('created_at');
	}

	/**
	 * detail: update promo id for a certain player
	 *
	 * @param int $tranId transaction id
	 * @param int $playerPromoId transaction player_promo_id
	 * @param int $promoCategory transaction promo_category
	 *
	 * @return Boolean
	 */
	public function updatePlayerPromoId($tranId, $playerPromoId, $promoCategory = null) {
		$this->db->set('player_promo_id', $playerPromoId)->set('promo_category', $promoCategory)->where('id', $tranId);
		return $this->runAnyUpdate('transactions');
	}

	public function clearPlayerPromoId($playerPromoId) {
		$this->db->set('player_promo_id', 'null', false)->set('promo_category', 'null', false)->where('player_promo_id', $playerPromoId);
		return $this->runAnyUpdate('transactions');
	}

	/**
	 * detail: get all the valid deposit for a certain player
	 *
	 * @param int $playerId transaction to_id field
	 * @param string $periodFrom transaction created_at field
	 * @param string $periodTo transaction created_at field
	 *
	 * @return array
	 */
	public function getListValidDepositByPlayerId($playerId, $periodFrom, $periodTo,
		$minAmount, $maxAmount, $playerPromoIds = null) {
		$this->db->from($this->tableName)
			->where('transaction_type', self::DEPOSIT)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId)
			->where('created_at >=', $periodFrom)
			->where('created_at <=', $periodTo);

		if (!empty($playerPromoIds)) {
			$this->db->where('(player_promo_id is null or player_promo_id not in (' . implode(',', $playerPromoIds) . ') )', null, false);
			// $this->db->or_where_not_in('player_promo_id', $playerPromoIds);
		} else {
			$this->db->where('player_promo_id is null', null, false);
		}

		if ($minAmount !== null && $maxAmount != null) {
			$this->db->where('amount >=', $minAmount)->where('amount <=', $maxAmount);
		}

		$this->addWhereApproved();

		$search_transaction_order = $this->utils->getPromotionRuleSetting('search_transaction_order', 'desc');

		$this->db->order_by('created_at', $search_transaction_order);
		// $this->limitOneRow();

		return $this->runMultipleRow();
	}

	/**
	 * detail: get all deposits for a certain player
	 *
	 * @param int $playerId transaction to_id field
	 * @param string $periodFrom transaction created_at field
	 * @param string $periodTo transaction created_at field
	 * @param double $minAmount transaction amount field
	 * @param double $maxAmount transaction amount field
	 *
	 * @return array
	 */
	public function getListDepositByPlayerId($playerId, $periodFrom, $periodTo, $minAmount, $maxAmount) {
		$this->db->select('id,amount')->from($this->tableName)
			->where('transaction_type', self::DEPOSIT)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId)
			->where('created_at >=', $periodFrom)
			->where('created_at <=', $periodTo);

		if ($minAmount >= 0 && $maxAmount > 0) {
			$this->db->where('amount >=', $minAmount)->where('amount <=', $maxAmount);
		}

		$this->addWhereApproved();
		$this->db->order_by('created_at', 'asc');
		// $this->limitOneRow();

		return $this->runMultipleRow();
	}

	public function getTransactionAmount($id) {
		$amt = 0;
		$row = $this->getOneRowById($id);
		if ($row) {
			$amt = $row->amount;
		}
		return $amt;
	}

	/**
	 * detail: get adjustment history for a certain player
	 *
	 * @param int $playerId transaction to_id field
	 * @param int $limit
	 * @param int $offset
	 * @param array $search
	 *
	 * @return array or Boolean
	 */
	public function getPlayerAdjustmentHistoryWLimit($playerId, $limit, $offset, $search) {
		$this->db->select('*')
			->from('transactions')
			->where_in('transaction_type', array(
				self::DEPOSIT, self::WITHDRAWAL, self::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET,
				self::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET, self::MANUAL_ADD_BALANCE_ON_SUB_WALLET,
				self::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET, self::ADD_BONUS,
				self::PLAYER_REFER_BONUS, self::MEMBER_GROUP_DEPOSIT_BONUS, self::AUTO_ADD_CASHBACK_TO_BALANCE,
				self::RANDOM_BONUS,
			))
			->where('((to_id=' . $playerId . ' and to_type=' . self::PLAYER . ') or (from_id=' . $playerId . ' and from_type=' . self::PLAYER . '))', null, false);

		if (!empty($search['from'])) {
			$this->db->where("created_at >=", $search['from'])->where("created_at <=", $search['to']);
		}
		$this->db->order_by('created_at', 'desc');

		$query = $this->db->get(null, $limit, $offset);

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return FALSE;
	}

	/**
	 * detail: get total balance form information or parameter provided
	 *
	 * @param array $info
	 * @return string or double
	 */
	public function getTotalBalanceFrom($info) {
		//first check big wallet
		if (isset($info['big_wallet']['total'])) {
			return $info['big_wallet']['total'];
		}

		if (isset($info['total_balance'])) {
			return $info['total_balance'];
		}

		return null;
	}

	/**
	 * detail: update change balance for a certain transaction record
	 *
	 * @param int $transId transaction id
	 * @param array $changeBal
	 *
	 * @return Boolean
	 */
	public function updateChangedBalance($transId, $changedBal) {
		//update before balanace and after balance
		$beforeBalance = $this->getTotalBalanceFrom($changedBal['before']);
		$afterBalance = $this->getTotalBalanceFrom($changedBal['after']);

		$this->db->set('changed_balance', json_encode($changedBal))
			->where('id', $transId);

		if ($beforeBalance !== null) {
			$this->db->set('before_balance', $beforeBalance);
		}

		if ($afterBalance !== null) {
			$this->db->set('after_balance', $afterBalance);
		}

		return $this->runAnyUpdate('transactions');
	}

	/**
	 * detail: count deposits for a certain player and dates
	 *
	 * @param int $playerId transaction to_id field
	 * @param string $date transaction created_at field
	 *
	 * @return int
	 */
	public function countDepositByPlayerAndDate($playerId, $date = null) {
		$this->db->select('count(amount) as cnt')
			->from($this->tableName)
			->where('transaction_type', self::DEPOSIT)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId);

		if ($date) {
			$this->db->where('date_format(created_at,"%Y-%m-%d")', $date);
		}

		$this->addWhereApproved();

		return $this->runOneRowOneField('cnt');
	}

	/**
	 * detail: get all transfer history for a certain player
	 *
	 * @param int $player_Id transaction to_id
	 * @param int $limit
	 * @param int $offset
	 * @param array $search
	 *
	 * @return array
	 */
	public function getAllTransferHistoryByPlayerIdWLimit($player_id, $limit, $offset, $search) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		if (!empty($search['from'])) {
			$search = "AND tran.created_at BETWEEN '" . $search['from'] . "' AND '" . $search['to'] . "'";
		}

		$query = $this->db->query("SELECT tran.amount, tran.created_at as requestDateTime,
			if(transaction_type=5, 'Main', es.system_code ) as transferFrom,
			if(transaction_type=5, es.system_code, 'Main' ) as transferTo
			FROM transactions as tran
			left join external_system as es on es.id=tran.sub_wallet_id
			where tran.from_type=? and tran.from_id=?
			and tran.transaction_type in (?,?)
			$search
			order by tran.created_at desc
			$limit
			$offset
		", array(self::PLAYER, $player_id,
			self::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET,
			self::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET));

		return $query->result_array();
	}

	/**
	 * detail: update external transaction id
	 *
	 * @param int $tranId transaction id
	 * @param int $external_transaction_id transaction external_transaction_id
	 *
	 * @return Boolean
	 */
	public function updateExternalTransactionId($tranId, $external_transaction_id) {
		$this->db->set('external_transaction_id', $external_transaction_id)->where('id', $tranId);

		return $this->runAnyUpdate('transactions');
	}

	/**
	 * detail: update request secure id
	 *
	 * @param int $tranId transaction id
	 * @param string request_secure_id
	 *
	 * @return Boolean
	 */
	public function updateRequestSecureId($tranId, $request_secure_id) {
		$this->db->set('request_secure_id', $request_secure_id)->where('id', $tranId);

		return $this->runAnyUpdate('transactions');
	}

	/**
	 * detail: get response_result_id
	 *
	 * @param int $request_secure_id
	 *
	 * @return int
	 */
	public function getResponseResultIdBySecureId($request_secure_id) {
		$this->db->select('response_result_id');
		$this->db->where('secure_id', $request_secure_id);
		$this->db->from('transfer_request');
		return $this->runOneRowOneField('response_result_id');
	}

	/**
	 * detail: today's withdraw for a certain player
	 *
	 * @param int $playerId transaction to_id field
	 * @return array
	 */
	public function count_today_withdraw($playerId) {
		$date = $this->utils->getTodayForMysql();

		$this->db->select('count(id) as cnt, sum(amount) as amount', null, false)->from('transactions')
			->where('to_id', $playerId)->where('to_type', self::PLAYER)
			->where('transaction_type', self::WITHDRAWAL)
			->where('date_format(created_at,"%Y-%m-%d")', $date);

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
	 * detail: total today's deposit amount for a certain player
	 *
	 * @param int $playerId transaction to_id field
	 * @return Boolean
	 */
	public function sumDepositAmountToday($playerId) {
		$today = $this->utils->getTodayForMysql();

		return $this->sumDepositAmount($playerId, $today . ' 00:00:00', $today . ' 23:59:59', 0);
	}

	public function existsTransByTypesAfter($from_datetime, $playerId, $disable_after_type, $filter_status = NULL) {
		$this->db->from('transactions')->where_in('transaction_type', $disable_after_type)
			->where('created_at >', $from_datetime)->where('to_type', self::PLAYER)
			->where('to_id', $playerId);
		if(!empty($filter_status)){
		    $this->db->where_in('status', $filter_status);
        }
        $this->db->limit(1);

		$rlt = $this->runExistsResult();

		$this->utils->printLastSQL();

		return $rlt;
	}

	/**
	 * detail: get available deposit for a certain dates
	 *
	 * @param  int $playerId transaction to_id field
	 * @param  string $fromDatetime transaction created_at
	 * @param  string $toDatetime transaction created_at
	 * @param  double $min transaction amount
	 * @param  double $max transaction amount
	 * @param  int $times
	 * @param  array  $disable_after_type
	 *
	 * @return array or Boolean
	 */
	public function getAvailableDepositInfoByDate($playerId, $fromDatetime, $toDatetime,
		$min, $max, $times, $disable_after_type = [], $orderBy='asc') {

		$rows = $this->listDepositByDate($playerId, $fromDatetime, $toDatetime, $orderBy);

		$this->utils->debug_log('disable_after_type', $disable_after_type);
		if (!empty($rows) && count($rows) >= $times) {
			//by times
			if ($times < 0) {
				//any available
				foreach ($rows as $row) {
					$this->utils->debug_log('isAppliedPromo', $this->isAppliedPromo($row), 'transaction', $row->id, 'player_promo_id', $row->player_promo_id,
						'amount', $row->amount, 'min', $min, 'max', $max);

					$selected = false;
					if (($row->amount >= $min || $min === null || $min <= 0) && ($row->amount <= $max || $max === null || $max <= 0)
						&& !$this->isAppliedPromo($row)) {

						$selected = true;

						if (!empty($disable_after_type)) {
							if ($this->existsTransByTypesAfter($row->created_at, $playerId, $disable_after_type)) {
								$selected = false;
							}
						}
					}

					if ($selected) {
						return $row;
					}

				}
			} else {
				if ($times == 0 || $times=='first') {
					$row = $rows[0];
				} else if($times=='last'){
					$row = $rows[count($rows)-1];
				}else{
					$row = $rows[$times - 1];
				}
				$this->utils->debug_log('isAppliedPromo', $this->isAppliedPromo($row), 'transaction', $row->id, 'player_promo_id', $row->player_promo_id,
					'amount', $row->amount, 'min', $min, 'max', $max);
				if (($row->amount >= $min || $min === null || $min <= 0) && ($row->amount <= $max || $max === null || $max <= 0)) {
				    if(!$this->isAppliedPromo($row)){
                        if (!empty($disable_after_type) && $times > 1) {
                            if ($this->existsTransByTypesAfter($row->created_at, $playerId, $disable_after_type)) {
                                return null;
                            }
                        }
                        return $row;
                    }
				}
			}
		}

		return null;

	}

	/**
	 * detail: deposit list for a certain player and date
	 *
	 * @param int $playerId transaction to_id field
	 * @param string $fromDatetime transaction created_at
	 * @param string $toDatetime transaction created_at
	 * @param string $order_by asc/desc
	 *
	 * @return array
	 */
	public function listDepositByDate($playerId, $fromDatetime, $toDatetime, $order_by='asc') {
		if(empty($order_by)){
			$order_by='asc';
		}
		$this->db->from($this->tableName)
			->where('transaction_type', self::DEPOSIT)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId);

		if (!empty($fromDatetime) && !empty($toDatetime)) {
			$this->db->where('created_at >=', $fromDatetime)
				->where('created_at <=', $toDatetime);
		}

		$this->addWhereApproved();

		$this->db->order_by('created_at', $order_by);

		$rows = $this->runMultipleRow();
		// $this->utils->printLastSQL();

		return $rows;
	}

	//for transfer to sub wallet

	/**
	 * detail: get available transfer records for a certain player
	 *
	 * @param int $playerId transaction to_id field
	 * @param string $fromDatetime transaction created_at
	 * @param string $toDatetime transaction created_at
	 * @param double $min trans amount
	 * @param double $max trans amount
	 * @param Boolean $times
	 * @param array $disable_after_type
	 *
	 * @return array or string
	 */
	public function getAvailableTransferInfoByDate($playerId, $fromDatetime, $toDatetime,
		$min, $max, $times, $disable_after_type = []) {

		$rows = $this->listTransferByDate($playerId, $fromDatetime, $toDatetime);

		$this->utils->debug_log('disable_after_type', $disable_after_type);
		if (!empty($rows) && count($rows) >= $times) {
			//by times
			if ($times < 0) {
				//any available
				foreach ($rows as $row) {
					$this->utils->debug_log('isAppliedPromo', $this->isAppliedPromo($row), 'transaction', $row->id, 'player_promo_id', $row->player_promo_id,
						'amount', $row->amount, 'min', $min, 'max', $max);

					$selected = false;
					if (($row->amount >= $min || $min === null || $min <= 0) && ($row->amount <= $max || $max === null || $max <= 0)
						&& !$this->isAppliedPromo($row)) {

						$selected = true;

						if (!empty($disable_after_type)) {
							if ($this->existsTransByTypesAfter($row->created_at, $playerId, $disable_after_type)) {
								$selected = false;
							}
						}
					}

					if ($selected) {
						return $row;
					}

				}
			} else {
				$row = $rows[$times - 1];
				$this->utils->debug_log('isAppliedPromo', $this->isAppliedPromo($row), 'transaction', $row->id, 'player_promo_id', $row->player_promo_id,
					'amount', $row->amount, 'min', $min, 'max', $max);
				if (($row->amount >= $min || $min === null || $min <= 0) && ($row->amount <= $max || $max === null || $max <= 0)
					&& !$this->isAppliedPromo($row)) {

					if (!empty($disable_after_type)) {
						if ($this->existsTransByTypesAfter($row->created_at, $playerId, $disable_after_type)) {
							return null;
						}
					}
					return $row;
				}
			}
		}

		return null;

	}

	/**
	 * detail: get transfer list for a certain player and dates
	 *
	 * @param int $playerId trans to_id field
	 * @param string $fromDatetime trans created_at
	 * @param string $toDatetime trans created_at
	 *
	 * @return array
	 */
	public function listTransferByDate($playerId, $fromDatetime, $toDatetime) {
		$this->db->from($this->tableName)
			->where('transaction_type', self::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId);

		if (!empty($fromDatetime) && !empty($toDatetime)) {
			$this->db->where('created_at >=', $fromDatetime)
				->where('created_at <=', $toDatetime);
		}

		$this->addWhereApproved();

		$this->db->order_by('created_at', 'asc');

		$rows = $this->runMultipleRow();
		// $this->utils->printLastSQL();

		return $rows;
	}

	/**
	 * detail: get available deposit record for a certain player, base on the frequency setting
	 *
	 * @param int $playerId trans to_id field
	 * @param string $frequency
	 * @param string $today
	 * @param double $min trans amount
	 * @param double $max trans amount
	 * @param Boolean $times
	 * @param array $disable_after_type
	 *
	 * @return array
	 */
	public function getAvailableDepositInfoByFreq($playerId, $frequency, $today,
		$min, $max, $times, $disable_after_type = []) {
		//default is today
		$fromDate = $today . ' 00:00:00';
		$toDate = $today . ' 23:59:59';
		switch ($frequency) {
		case self::FREQUENCRY_ALL:
			$fromDate = null;
			$toDate = null;
			break;
		case self::FREQUENCRY_WEEKLY:
			list($fromDate, $toDate) = $this->utils->getFromToByWeek($today);
			$fromDate = $fromDate . ' 00:00:00';
			$toDate = $toDate . ' 23:59:59';
			break;

		case self::FREQUENCRY_MONTHLY:
			list($fromDate, $toDate) = $this->utils->getFromToByMonth($today);
			$fromDate = $fromDate . ' 00:00:00';
			$toDate = $toDate . ' 23:59:59';
			break;
		}

		return $this->getAvailableDepositInfoByDate($playerId, $fromDate, $toDate,
			$min, $max, $times, $disable_after_type);

	}

	/**
	 * detail: deposit list for a certain player base on the frequency setting
	 *
	 * @param int $playerId trans to_id field
	 * @param string $frequency
	 * @param string $today
	 *
	 * @return array
	 */
	public function listDepositByFreq($playerId, $frequency, $today) {
		//default is today
		$fromDate = $today . ' 00:00:00';
		$toDate = $today . ' 23:59:59';
		switch ($frequency) {
		case self::FREQUENCRY_ALL:
			$fromDate = null;
			$toDate = null;
			break;
		case self::FREQUENCRY_WEEKLY:
			list($fromDate, $toDate) = $this->utils->getFromToByWeek($today);
			$fromDate = $fromDate . ' 00:00:00';
			$toDate = $toDate . ' 23:59:59';
			break;

		case self::FREQUENCRY_MONTHLY:
			list($fromDate, $toDate) = $this->utils->getFromToByMonth($today);
			$fromDate = $fromDate . ' 00:00:00';
			$toDate = $toDate . ' 23:59:59';
			break;
		}

		return $this->listDepositByDate($playerId, $fromDate, $toDate);
	}

	public function isAppliedPromo($tran) {
	    if(is_array($tran)){
            return !empty($tran) && isset($tran['player_promo_id']) && !empty($tran['player_promo_id']);
        }else{
            return !empty($tran) && isset($tran->player_promo_id) && !empty($tran->player_promo_id);
        }
	}

	/**
	 * detail: create affiliate transaction adjustment
	 *
	 * @param int $trans_type
	 * @param int $adminUserId current logged user
	 * @param int $affiliateId trans to_Id field
	 * @param double $amount trans amount
	 * @param double $beforeBalance  trans amount
	 * @param int $show_in_front_end trans display_name
	 * @param string $reason
	 *
	 * @return int or Boolean
	 */
	public function createAdjustmentAffiliateTransaction($trans_type, $adminUserId, $affiliateId, $amount,
		$beforeBalance, $show_in_front_end = null, $reason = null) {

		if ($affiliateId && $amount) {
			$this->load->model(array('wallet_model', 'promorules', 'player_promo', 'users', 'player_model', 'affiliatemodel'));

			// $beforeHistoryId = $this->recordPlayerBeforeActionWalletBalanceHistory($adjustment_type,
			// 	$playerId, null, 0, $amount, null, null);

			if ($trans_type == Transactions::ADMIN_SUBTRACT_BALANCE_TO_AFFILIATE) {
				$afterBalance = $beforeBalance - $amount;
			} else {
				$afterBalance = $beforeBalance + $amount;
			}

			$beforeBalanceDetails = $this->affiliatemodel->getBalanceDetails($affiliateId);
			$this->affiliatemodel->updateAffiliateBalance(array('balance' => $afterBalance), $affiliateId);
			$afterBalanceDetails = $this->affiliatemodel->getBalanceDetails($affiliateId);

			$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);

			$from_username = $this->users->getUsernameById($adminUserId);
			$to_username = $this->affiliatemodel->getUsernameById($affiliateId);

			$note = sprintf('%s <b>%s</b> credit to affiliate id: <b>%s</b>\'s credit (<b>%s</b> to <b>%s</b>) by <b>%s</b>',
				$trans_type, number_format($beforeBalance, 2), $affiliateId, number_format($beforeBalance, 2), number_format($beforeBalance + $amount, 2), $from_username);

			$flag = self::MANUAL;
			$transaction = array(
				'amount' => $amount,
				'transaction_type' => $trans_type,
				'from_id' => $adminUserId,
				'from_type' => Transactions::ADMIN,
				'from_username' => $from_username,
				'to_id' => $affiliateId,
				'to_type' => Transactions::AFFILIATE,
				'to_username' => $to_username,
				'note' => $note,
				'sub_wallet_id' => null,
				'status' => Transactions::APPROVED,
				'flag' => $flag,
				'created_at' => $this->utils->getNowForMysql(),
				'updated_at' => $this->utils->getNowForMysql(),
				'promo_category' => null,
				// 'balance_history_id' => $beforeHistoryId,
				'display_name' => $show_in_front_end,
				'trans_date' => $this->utils->getTodayForMysql(),
				'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
				'trans_year' => $this->utils->getThisYearForMysql(),

				'before_balance' => $beforeBalanceDetails['total_balance'],
				'after_balance' => $afterBalanceDetails['total_balance'],
				'changed_balance' => $this->utils->encodeJson($changedBal),
			);

			$rtn_id = $this->insertRow($transaction);
			// $this->updateBalanceHistoryTransactionId($beforeHistoryId, $rtn_id);

			// $beforeBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);

			// $afterBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);
			// $this->updateChangedBalance($rtn_id, array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount));

			// $afterHistoryId = $this->recordPlayerAfterActionWalletBalanceHistory($adjustment_type,
			// 	$playerId, null, $rtn_id, $amount, null, null);

			// $transaction['id'] = $rtn_id;
			// $this->load->model('daily_player_trans');
			// $this->daily_player_trans->update_today($transactionDetails);
			// return $transaction;
			return $rtn_id;
		}
		return false;
	}

	/**
	 * detail: deposit to affiliate
	 *
	 * @param int $affId trans to_id field
	 * @param double $amount trans amount
	 * @param string $extraNotes trans note
	 * @param int $adminUserId current logged user
	 * @param int $flag
	 * @param string $dateTime
	 *
	 * @return int
	 */
	public function depositToAff($affId, $amount, $extraNotes, $adminUserId = 1, $flag = self::MANUAL, $dateTime = null) {
		$this->load->model(array('affiliatemodel'));
		$transaction_type = self::DEPOSIT_TO_AFFILIATE;
		if (empty($dateTime)) {
			$dateTime = new DateTime();
		}
		if (is_string($dateTime)) {
			$dateTime = new DateTime($dateTime);
		}

		$note = 'deposit ' . $amount . ' to aff ' . $affId . ' ' . $extraNotes;

		$beforeBalanceDetails = $this->affiliatemodel->getBalanceDetails($affId);
		$success=$this->affiliatemodel->incMainWallet($affId, $amount);
		if(!$success){
			$this->utils->error_log('affiliate incMainWallet failed', $affId, $amount);
			return $success;
		}
		$afterBalanceDetails = $this->affiliatemodel->getBalanceDetails($affId);

		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);

		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $adminUserId, // $this->authentication->getUserId(),
			'from_type' => self::ADMIN, //admin
			'to_id' => $affId,
			'to_type' => self::AFFILIATE, //player
			'note' => $note,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'status' => self::APPROVED, //approved
			'flag' => $flag, //manual
			'created_at' => $this->utils->formatDateTimeForMysql($dateTime),
			'updated_at' => $this->utils->getNowForMysql(),
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);

		$rtn_id = $this->insertRow($transactionDetails);

		return $rtn_id;
	}

	public function manualAddBalanceAff($affId, $amount, $extraNotes, $adminUserId = 1, $flag = self::MANUAL, $dateTime = null) {
		$this->load->model(array('affiliatemodel'));
		if (empty($dateTime)) {
			$dateTime = new DateTime();
		}
		if (is_string($dateTime)) {
			$dateTime = new DateTime($dateTime);
		}
		$note = 'manual add ' . $amount . ' to aff ' . $affId . ' main wallet: ' . $extraNotes;
		$beforeBalanceDetails = $this->affiliatemodel->getBalanceDetails($affId);
		$success=$this->affiliatemodel->incMainWallet($affId, $amount);
		if(!$success){
			$this->utils->error_log('affiliate incMainWallet failed', $affId, $amount);
			return $success;
		}
		$afterBalanceDetails = $this->affiliatemodel->getBalanceDetails($affId);
		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);
		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => self::ADMIN_ADD_BALANCE_TO_AFFILIATE,
			'from_id' => $adminUserId,
			'from_type' => self::ADMIN,
			'to_id' => $affId,
			'to_type' => self::AFFILIATE,
			'note' => $note,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'status' => self::APPROVED,
			'flag' => $flag,
			'created_at' => $this->utils->formatDateTimeForMysql($dateTime),
			'updated_at' => $this->utils->getNowForMysql(),
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);
		$rtn_id = $this->insertRow($transactionDetails);
		return $rtn_id;
	}

	public function manualSubtractBalanceAff($affId, $amount, $extraNotes, $adminUserId = 1, $flag = self::MANUAL, $dateTime = null) {
		$this->load->model(array('affiliatemodel'));
		if (empty($dateTime)) {
			$dateTime = new DateTime();
		}
		if (is_string($dateTime)) {
			$dateTime = new DateTime($dateTime);
		}
		$note = 'manual subtract ' . $amount . ' to aff ' . $affId . ' main wallet: ' . $extraNotes;
		$beforeBalanceDetails = $this->affiliatemodel->getBalanceDetails($affId);
		$success=$this->affiliatemodel->decMainWallet($affId, $amount);
		if(!$success){
			$this->utils->error_log('affiliate decMainWallet failed', $affId, $amount);
			return $success;
		}
		$afterBalanceDetails = $this->affiliatemodel->getBalanceDetails($affId);
		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);
		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => self::ADMIN_SUBTRACT_BALANCE_TO_AFFILIATE,
			'from_id' => $adminUserId,
			'from_type' => self::ADMIN,
			'to_id' => $affId,
			'to_type' => self::AFFILIATE,
			'note' => $note,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'status' => self::APPROVED,
			'flag' => $flag,
			'created_at' => $this->utils->formatDateTimeForMysql($dateTime),
			'updated_at' => $this->utils->getNowForMysql(),
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);
		$rtn_id = $this->insertRow($transactionDetails);
		return $rtn_id;
	}

	/**
	 * detail: withdraw from affiliate
	 *
	 * @param int $affId trans to_id field
	 * @param double $amount trans amount
	 * @param string $extraNotes trans note
	 * @param int $adminUserId current logged user
	 * @param int $flag
	 * @param string $dateTime
	 * @param string $walletType
	 *
	 * @return int
	 */
	public function withdrawFromAff($affId, $amount, $extraNotes, $adminUserId = 1, $flag = self::MANUAL, $dateTime = null, $walletType = 'main') {
		$this->load->model(array('affiliatemodel'));
		$transaction_type = self::WITHDRAW_FROM_AFFILIATE;
		if (empty($dateTime)) {
			$dateTime = new DateTime();
		}
		if (is_string($dateTime)) {
			$dateTime = new DateTime($dateTime);
		}

		$note = 'withdraw ' . $amount . ' from aff ' . $affId . ' ' . $walletType . ' wallet , reason: ' . $extraNotes;

		$beforeBalanceDetails = $this->affiliatemodel->getBalanceDetails($affId);
		if ($walletType == 'main') {
			$success=$this->affiliatemodel->decMainWallet($affId, $amount);
			if(!$success){
				$this->utils->error_log('affiliate decMainWallet failed', $affId, $amount);
				return $success;
			}
		} else {
			$success=$this->affiliatemodel->decBalanceWallet($affId, $amount);
			if(!$success){
				$this->utils->error_log('affiliate decBalanceWallet failed', $affId, $amount);
				return $success;
			}
		}

		$afterBalanceDetails = $this->affiliatemodel->getBalanceDetails($affId);

		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);

		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $adminUserId, // $this->authentication->getUserId(),
			'from_type' => self::ADMIN, //admin
			'to_id' => $affId,
			'to_type' => self::AFFILIATE, //player
			'note' => $note,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'status' => self::APPROVED, //approved
			'flag' => $flag, //manual
			'created_at' => $this->utils->formatDateTimeForMysql($dateTime),
			'updated_at' => $this->utils->getNowForMysql(),
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);

		$rtn_id = $this->insertRow($transactionDetails);

		return $rtn_id;
	}

	/**
	 * detail: withdraw from affiliate frozen
	 *
	 * @param int $affId trans to_id field
	 * @param double $amount trans amount
	 * @param string $extraNotes trans note
	 * @param int $adminUserId current logged user
	 * @param int $flag
	 * @param string $dateTime
	 *
	 * @return int
	 */
	public function withdrawFromAffFrozen($affId, $amount, $extraNotes, $adminUserId = 1, $flag = self::MANUAL, $dateTime = null) {
		$this->load->model(array('affiliatemodel'));
		$transaction_type = self::WITHDRAW_FROM_AFFILIATE;
		if (empty($dateTime)) {
			$dateTime = new DateTime();
		}
		if (is_string($dateTime)) {
			$dateTime = new DateTime($dateTime);
		}

		$note = 'withdraw ' . $amount . ' from aff ' . $affId . ' ' . $extraNotes;

		$beforeBalanceDetails = $this->affiliatemodel->getBalanceDetails($affId);

		// $this->affiliatemodel->decMainWallet($affId, $amount);
		//clear frozen
		$this->db->set('frozen', 'frozen-' . $amount, false)
			->where('affiliateId', $affId);
		$this->runAnyUpdate('affiliates');

		$afterBalanceDetails = $this->affiliatemodel->getBalanceDetails($affId);

		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);

		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $adminUserId, // $this->authentication->getUserId(),
			'from_type' => self::ADMIN, //admin
			'to_id' => $affId,
			'to_type' => self::AFFILIATE, //player
			'note' => $note,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'status' => self::APPROVED, //approved
			'flag' => $flag, //manual
			'created_at' => $this->utils->formatDateTimeForMysql($dateTime),
			'updated_at' => $this->utils->getNowForMysql(),
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);

		$rtn_id = $this->insertRow($transactionDetails);

		return $rtn_id;
	}

	/**
	 * detail: transfer balance from main to affilaite
	 *
	 * @param int $affId trans to_id field
	 * @param double $amount trans amount
	 * @param string $extraNotes trans note
	 * @param int $adminUserId current logged user
	 * @param int $flag
	 *
	 * @return int
	 */
	public function affTransferToBalanceFromMain($affId, $extraNotes, $adminUserId = 1, $flag = self::MANUAL, $amount = null) {
		$this->load->model(array('affiliatemodel'));
		$transaction_type = self::TRANSFER_TO_BALANCE_FROM_MAIN_AFFILIATE;

		$note = 'transfer ' . $amount . ' to balance from main on aff ' . $affId . ' ' . $extraNotes;

		$beforeBalanceDetails = $this->affiliatemodel->getBalanceDetails($affId);

		//really transfer
		if ($amount === null) {
			$amount = $beforeBalanceDetails['main_wallet'];
			// $this->db->set('wallet_hold', 'wallet_balance', false);
			// $this->runAnyUpdate('affiliates');
			// $this->db->set('wallet_balance', 0);
			// $rlt = $this->runAnyUpdate('affiliates');
		}
		$this->db->set('wallet_hold', 'wallet_hold+' . $amount, false);
		$this->db->set('wallet_balance', 'wallet_balance-' . $amount, false);
		$this->db->where('affiliateId', $affId);
		$rlt = $this->runAnyUpdate('affiliates');

		$afterBalanceDetails = $this->affiliatemodel->getBalanceDetails($affId);

		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);

		//record
		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $adminUserId, // $this->authentication->getUserId(),
			'from_type' => self::ADMIN, //admin
			'to_id' => $affId,
			'to_type' => self::AFFILIATE, //player
			'note' => $note,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'status' => self::APPROVED, //approved
			'flag' => $flag, //manual
			'created_at' => $this->utils->getNowForMysql(),
			'updated_at' => $this->utils->getNowForMysql(),
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);

		$rtn_id = $this->insertRow($transactionDetails);

		return $rtn_id;
	}

	/**
	 * detail: transfer affiliate balance to main
	 *
	 * @param int $affId trans to_id
	 * @param string $extraNotes trans note
	 * @param int $adminUserId current logged user
	 * @param int $flag trans flag
	 * @param double $amount trans amount
	 *
	 * @return int
	 */
	public function affTransferFromBalanceToMain($affId, $extraNotes, $adminUserId = 1, $flag = self::MANUAL, $amount = null) {
		$this->load->model(array('affiliatemodel'));
		$transaction_type = self::TRANSFER_TO_MAIN_FROM_BALANCE_AFFILIATE;

		$note = 'transfer ' . $amount . ' to main from balance on aff ' . $affId . ' ' . $extraNotes;

		$beforeBalanceDetails = $this->affiliatemodel->getBalanceDetails($affId);

		if ($amount === null) {
			$amount = $beforeBalanceDetails['hold'];
			// $this->db->set('wallet_balance', 'wallet_hold', false);
			// $this->runAnyUpdate('affiliates');
			// $this->db->set('wallet_hold', 0);
			// $rlt = $this->runAnyUpdate('affiliates');
		}
		$this->db->set('wallet_hold', 'wallet_hold-' . $amount, false);
		$this->db->set('wallet_balance', 'wallet_balance+' . $amount, false);
		$this->db->where('affiliateId', $affId);
		$rlt = $this->runAnyUpdate('affiliates');

		$afterBalanceDetails = $this->affiliatemodel->getBalanceDetails($affId);

		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);

		//record
		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $adminUserId, // $this->authentication->getUserId(),
			'from_type' => self::ADMIN, //admin
			'to_id' => $affId,
			'to_type' => self::AFFILIATE, //player
			'note' => $note,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'status' => self::APPROVED, //approved
			'flag' => $flag, //manual
			'created_at' => $this->utils->getNowForMysql(),
			'updated_at' => $this->utils->getNowForMysql(),
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);

		$rtn_id = $this->insertRow($transactionDetails);

		return $rtn_id;
	}

	/**
	 * detail: get transaction for a certain affiliate
	 *
	 * @param int $affId
	 * @return array
	 */
	public function getAffTransactions($affId) {
		$this->db->from('transactions')->where('to_type', self::AFFILIATE)
			->where('to_id', $affId)->where_in('transaction_type',
			array(self::DEPOSIT_TO_AFFILIATE, self::WITHDRAW_FROM_AFFILIATE,
				self::TRANSFER_TO_BALANCE_FROM_MAIN_AFFILIATE, self::TRANSFER_TO_MAIN_FROM_BALANCE_AFFILIATE,
			))->order_by('id', 'DESC');
		$this->addWhereApproved();
		return $this->runMultipleRowArray();
	}

	/**
	 * detail: save affiliate transaction datas
	 *
	 * @param double $amount trans amount
	 * @param int $transaction_type trans transaction_type
	 * @param int $from_id trans from_id
	 * @param int $from_type trans from_type
	 * @param int $to_id trans to_id
	 * @param int $to_type trans to_type
	 * @param int $sub_wallet_id trans sub_wallet_id
	 * @param string $note trans note
	 * @param double $beforeBalance
	 * @param double $afterBalance
	 * @param double $totalBeforeBalance
	 *
	 * @return array
	 */
	public function saveAffiliateTransaction(
		$amount,
		$transaction_type,
		$from_id,
		$from_type,
		$to_id,
		$to_type,
		$sub_wallet_id,
		$note,
		$beforeBalance = null,
		$afterBalance = null,
		$totalBeforeBalance = null
	) {
		return $this->insertRow(array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $from_id,
			'from_type' => $from_type,
			'to_id' => $to_id,
			'to_type' => $to_type,
			'sub_wallet_id' => $sub_wallet_id,
			'note' => $note,
			'before_balance' => $beforeBalance,
			'after_balance' => $afterBalance,
			'status' => Transactions::APPROVED,
			'flag' => Transactions::MANUAL,
			'total_before_balance' => $totalBeforeBalance,
			'created_at' => $this->utils->getNowForMysql(),
		));
	}

	/**
	 * detail: get last transfer for a certain player
	 *
	 * @param int $playerId trans to_id field
	 * @return array
	 */
	public function getLastTransferDatetime($playerId) {
		$this->db->select()->from($this->tableName)->where_in('transaction_type',
			array(self::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET, self::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET))
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId);

		$this->addWhereApproved();

		$this->db->order_by('transactions.created_at', 'desc');

		$this->limitOneRow();

		return $this->runOneRowOneField('created_at');
	}

	/**
	 * detail: create transation fee
	 *
	 * @param double $amount trans amount
	 * @param string $transaction
	 * @param int $adminUserId current logged user
	 * @param int $playerId trans to_id
	 * @param int $related_trans_id trans field
	 * @param int $walletAccountId trans field
	 * @param double $totaBeforeBalance trans field
	 * @param int $saleOrderId trans field
	 * @param int $transaction_type trans field
	 * @param int $flag trans field
	 *
	 * @return array
	 */
	public function createTransactionFee($amount, $transaction, $adminUserId, $playerId, $related_trans_id, $walletAccountId = null, $totalBeforeBalance = null, $saleOrderId = null, $transaction_type = self::FEE_FOR_PLAYER, $flag = self::MANUAL) {

		if ($amount <= 0) {
			return false;
		}

		$adminUserId = $adminUserId ? $adminUserId : 1;
		$from_username = $this->users->getUsernameById($adminUserId);
		$to_username = $this->player_model->getUsernameById($playerId);
		$from_username = $this->users->getUsernameById($adminUserId);
		$note = $from_username . ' add ' . $transaction . ' Transaction Fee : ' . $amount . ' to ' . $to_username;

		// if ($totalBeforeBalance == null) {
		// 	$totalBeforeBalance = $this->wallet_model->getTotalBalance($playerId);
		// }
		$beforeBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);
		if ($this->utils->getConfig('minus_transaction_fee_from_deposit') && !empty($saleOrderId)) {
			//dec to main wallet
			$success=$this->wallet_model->checkAndDecMainWallet($playerId, $amount);

			if(!$success){

				return false;
			}

		}

		if ($this->utils->getConfig('minus_transaction_fee_from_withdraw') && !empty($walletAccountId)) {

			$success=$this->wallet_model->checkAndDecMainWallet($playerId, $amount);

			if(!$success){

				return false;
			}

		}
		$afterBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);

		// $beforeHistoryId = $this->recordPlayerBeforeActionWalletBalanceHistory($transaction_type,
		// 	$playerId, null, 0, $amount, $saleOrderId, null, null, $walletAccountId);

		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $adminUserId, // $this->authentication->getUserId(),
			'from_type' => self::ADMIN, //admin
			'from_username' => $from_username,
			'to_id' => $playerId,
			'to_type' => self::PLAYER, //player
			'to_username' => $to_username,
			'note' => $note,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'status' => self::APPROVED, //approved
			'flag' => $flag, //manual
			'created_at' => $this->utils->getNowForMysql(),
			'updated_at' => $this->utils->getNowForMysql(),
			'total_before_balance' => $afterBalanceDetails['total_balance'],
			// 'balance_history_id' => $beforeHistoryId,
			'trans_date' => $this->utils->getTodayForMysql(),
			'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
			'trans_year' => $this->utils->getThisYearForMysql(),
			'related_trans_id' => $related_trans_id,
			'changed_balance' => $this->utils->encodeJson(array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount)),
		);
		$rlt = $this->insertRow($transactionDetails);

		$afterHistoryId = $this->recordPlayerAfterActionWalletBalanceHistory($transaction_type,
			$playerId, null, $rlt, $amount);
		$this->updateBalanceHistoryTransactionId($afterHistoryId, $rlt);

		return $rlt;
	}

	/**
	 * detail: make up transfer transaction
	 *
	 * @param int $playerId trans to_id
	 * @param int $transaction_type
	 * @param int $subWalletId trans field
	 * @param int $gamePlatformId trans field
	 * @param double $amount trans field
	 * @param string $note trans field
	 * @param Boolean $really_fix_balance
	 * @param string $dateTime trans field
	 * @param int $extername_transaction_id trans field
	 *
	 * @return array
	 */
	public function makeUpTransferTransaction($playerId, $transaction_type, $subWalletId, $gamePlatformId,
		$amount, $note, $really_fix_balance, $dateTime = null, $external_transaction_id = null,
		$adjustment_category_id = null, $transfer_request_id=null) {

		if (empty($dateTime)) {
			$dateTime = new DateTime();
		}
		if (is_string($dateTime)) {
			$dateTime = new DateTime($dateTime);
		}

		$player_username = $this->player_model->getUsernameById($playerId);

		$beforeBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);
		//add to main wallet or sub wallet
		if ($really_fix_balance) {

			//only add
			if ($transaction_type == self::MANUAL_ADD_BALANCE_ON_SUB_WALLET || $transaction_type == self::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET) {
				//add to sub wallet
				$success=$this->wallet_model->incSubWallet($playerId, $gamePlatformId, $amount);
				if(!$success){
					$this->utils->error_log('incSubWallet failed', $playerId, $amount);
					return $success;
				}
			} else if ($transaction_type == self::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET || $transaction_type == self::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET) {
				//add to main wallet
				$success=$this->wallet_model->incMainWallet($playerId, $amount);
				if(!$success){
					$this->utils->error_log('incMainWallet failed', $playerId, $amount);
					return $success;
				}
			}

		}
		$afterBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);

		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $playerId,
			'from_type' => Transactions::PLAYER,
			'from_username' => $player_username,
			'to_id' => $playerId,
			'to_type' => Transactions::PLAYER,
			'to_username' => $player_username,
			'external_transaction_id' => $external_transaction_id,
			'sub_wallet_id' => $subWalletId,
			'note' => $note,
			'before_balance' => $beforeBalanceDetails['main_wallet'],
			'after_balance' => $afterBalanceDetails['main_wallet'],
			'status' => Transactions::APPROVED,
			'flag' => Transactions::MANUAL,
			'created_at' => $this->utils->formatDateTimeForMysql($dateTime),
			'updated_at' => $this->utils->getNowForMysql(),
			'total_before_balance' => $afterBalanceDetails['total_balance'],
			'trans_date' => $this->utils->getTodayForMysql(),
			'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
			'trans_year' => $this->utils->getThisYearForMysql(),
			'changed_balance' => $this->utils->encodeJson(array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount)),
			'adjustment_category_id' => $adjustment_category_id,
			'transfer_request_id' => $transfer_request_id,
		);

		$rlt = $this->insertRow($transactionDetails);

		return $rlt;
	}

	/**
	 * detail: create deposit transaction of certain agent
	 *
	 * @param int $playerId trans to_id field
	 * @param double $amount trans field
	 * @param int $agentId
	 * @param int $subWalletId
	 * @param int $flag
	 *
	 * @return array or Boolean
	 */
	public function createDepositTransactionByAgent($playerId, $amount, $agentId, $subWalletId = null, $flag = self::MANUAL) {
		if (!empty($playerId) && !empty($agentId)) {
			$this->load->model(array('wallet_model', 'player_model', 'users', 'agency_model'));

			// $totalBeforeBalance = $this->wallet_model->getTotalBalance($playerId);
			// $beforeBalance = $this->wallet_model->getMainWalletBalance($playerId);
			// $adminUserId = $adminUserId ? $adminUserId : 1;

			$agent_info = $this->agency_model->get_agent_by_id($agentId);
			$from_username = $agent_info['agent_name'];
			$to_username = $this->player_model->getUsernameById($playerId);

			$beforeBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);
			$success=$this->wallet_model->incMainWallet($playerId, $amount);
			if(!$success){
				$this->utils->error_log('incMainWallet failed', $playerId, $amount);
				return $success;
			}
			$afterBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);

			$transaction_type = self::DEPOSIT;
			// $beforeHistoryId = $this->recordPlayerBeforeActionWalletBalanceHistory($transaction_type,
			// 	$playerId, null, 0, $amount);

			$transactionDetails = array('amount' => $amount,
				'transaction_type' => $transaction_type, //deposit
				'from_id' => $agentId, // $this->authentication->getUserId(),
				'from_type' => self::AGENT, //admin
				'from_username' => $from_username,
				'to_id' => $playerId,
				'to_type' => self::PLAYER, //player
				'to_username' => $to_username,
				'note' => $from_username . ' deposit ' . $amount . ' to ' . $to_username,
				'sub_wallet_id' => $subWalletId,
				'status' => self::APPROVED, //approved
				'flag' => $flag, //manual
				'created_at' => $this->utils->getNowForMysql(),
				'updated_at' => $this->utils->getNowForMysql(),
				// 'order_id' => $saleOrder->id,
				// 'payment_account_id' => $saleOrder->payment_account_id,
				// 'balance_history_id' => $beforeHistoryId,
				'trans_date' => $this->utils->getTodayForMysql(),
				'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
				'trans_year' => $this->utils->getThisYearForMysql(),

				'before_balance' => $beforeBalanceDetails['total_balance'],
				'after_balance' => $afterBalanceDetails['total_balance'],
				'total_before_balance' => $beforeBalanceDetails['total_balance'],
				'changed_balance' => $this->utils->encodeJson(array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount)),
			);
			$rlt = $this->insertRow($transactionDetails);

			// $this->updateChangedBalance($rlt, array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount));

			if($this->utils->getConfig('record_balance_history_when_create_transactions')){
				$afterHistoryId = $this->recordPlayerAfterActionWalletBalanceHistory($transaction_type,
					$playerId, null, $rlt, $amount);
				$this->updateBalanceHistoryTransactionId($afterHistoryId, $rlt);
			}

			// $this->load->model('daily_player_trans');
			// $this->daily_player_trans->update_today($transactionDetails);
			//update player total deposit amount
			if($this->utils->getConfig('update_total_deposit_when_create_transactions')){
				$this->player_model->updateTotalDepositAmount($playerId);
			}

			// -- updatePlayersTotalDepositCount
			$this->utils->debug_log('START RUNNING: updatePlayersTotalDepositCount');
			$updatePlayersTotalDepositCount = $this->player_model->updatePlayersTotalDepositCount($playerId);

			$this->utils->debug_log('RESULT OF updatePlayersTotalDepositCount: Total count of players updated = '.$updatePlayersTotalDepositCount);

			$this->utils->debug_log('END RUNNING: updatePlayersTotalDepositCount');


			// -- updatePlayersFirstDeposit
			$this->utils->debug_log('START RUNNING: updatePlayersFirstDeposit');
			$updatePlayersFirstDeposit = $this->player_model->updatePlayersFirstDeposit($playerId);

			$this->utils->debug_log('RESULT OF updatePlayersFirstDeposit: Total count of players updated = '.$updatePlayersFirstDeposit);

			$this->utils->debug_log('END RUNNING: updatePlayersFirstDeposit');


			// -- updatePlayersSecondDeposit
			$this->utils->debug_log('START RUNNING: updatePlayersSecondDeposit');
			$updatePlayersSecondDeposit = $this->player_model->updatePlayersSecondDeposit($playerId);

			$this->utils->debug_log('RESULT OF updatePlayersSecondDeposit: Total count of players updated = '.$updatePlayersSecondDeposit);

			$this->utils->debug_log('END RUNNING: updatePlayersSecondDeposit');

			return $rlt;
		}
		return false;
	}

	/**
	 * detail: create withdraw transaction of a certain agent
	 *
	 * @param int $playerId trans to_id
	 * @param double $amount trans field
	 * @param int $agentId
	 * @param int $subWalletId
	 * @param int $flag
	 *
	 * @return array or Boolean
	 */
	public function createWithdrawTransactionByAgent($playerId, $amount, $agentId, $subWalletId = null, $flag = self::MANUAL) {

		$this->utils->debug_log('createWithdrawTransactionByAgent player id', $playerId, 'amount', $amount, 'agentId', $agentId);

		if (!empty($playerId) && !empty($agentId)) {
			$this->load->model(array('wallet_model', 'player_model', 'users', 'agency_model'));

			// $totalBeforeBalance = $this->wallet_model->getTotalBalance($playerId);
			// $beforeBalance = $this->wallet_model->getMainWalletBalance($playerId);
			// $adminUserId = $adminUserId ? $adminUserId : 1;

			$agent_info=$this->agency_model->get_agent_by_id($agentId);
			$to_username = $this->player_model->getUsernameById($playerId);
			$from_username = $agent_info['agent_name'];

			$beforeBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);
			$success=$this->wallet_model->decMainWallet($playerId, $amount);

			if(!$success){
				$this->utils->error_log('decMainWallet failed', $playerId, $amount);
				return false;
			}

			$afterBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);

			$transaction_type = self::WITHDRAWAL;
			// $beforeHistoryId = $this->recordPlayerBeforeActionWalletBalanceHistory($transaction_type,
			// 	$playerId, null, 0, $amount);

			$transactionDetails = array('amount' => $amount,
				'transaction_type' => $transaction_type, //deposit
				'from_id' => $agentId, // $this->authentication->getUserId(),
				'from_type' => self::AGENT, //admin
				'from_username' => $from_username,
				'to_id' => $playerId,
				'to_type' => self::PLAYER, //player
				'to_username' => $to_username,
				'note' => $to_username . ' withdraw ' . $amount . ' from ' . $from_username,
				'sub_wallet_id' => $subWalletId,
				'status' => self::APPROVED, //approved
				'flag' => $flag, //manual
				'created_at' => $this->utils->getNowForMysql(),
				'updated_at' => $this->utils->getNowForMysql(),
				// 'order_id' => $saleOrder->id,
				// 'payment_account_id' => $saleOrder->payment_account_id,
				// 'balance_history_id' => $beforeHistoryId,
				'trans_date' => $this->utils->getTodayForMysql(),
				'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
				'trans_year' => $this->utils->getThisYearForMysql(),

				'before_balance' => $beforeBalanceDetails['total_balance'],
				'after_balance' => $afterBalanceDetails['total_balance'],
				'total_before_balance' => $beforeBalanceDetails['total_balance'],
				'changed_balance' => $this->utils->encodeJson(array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount)),
			);
			$rlt = $this->insertRow($transactionDetails);

			// $this->updateChangedBalance($rlt, array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount));

			if($this->utils->getConfig('record_balance_history_when_create_transactions')){
				$afterHistoryId = $this->recordPlayerAfterActionWalletBalanceHistory($transaction_type,
					$playerId, null, $rlt, $amount);
				$this->updateBalanceHistoryTransactionId($afterHistoryId, $rlt);
			}

			// $this->load->model('daily_player_trans');
			// $this->daily_player_trans->update_today($transactionDetails);
			//update player total deposit amount
			if($this->utils->getConfig('update_total_deposit_when_create_transactions')){
				$this->player_model->updateTotalDepositAmount($playerId);
			}

			if ($rlt) {
				$this->updatePlayerWithdrawalInfo($playerId);
			}


			return $rlt;
		}
		return false;
	}

	/**
	 * detail: get referral bonus lists of a certain player
	 *
	 * @param int $playerId trans to_id
	 * @param string $from trans created_at
	 * @param string $to trans created_at
	 *
	 * @return array
	 */
	public function getReferralBonusList($playerId, $from, $to) {
		$this->db->select('created_at transactionDatetime, to_username username, 3 status, amount, to_id playerId')
			->from('transactions')->where('to_id', $playerId)
			->where('to_type', self::PLAYER)
			->where('transaction_type', self::PLAYER_REFER_BONUS)
			->where('created_at >=', $from)
			->where('created_at <=', $to);

		return $this->runMultipleRowArray();
	}

	/**
	 * detail: get sale order id of a certain transaction id
	 *
	 * @param int $depositTranId
	 * @return int
	 */
	public function getSaleOrderId($depositTranId) {
		$saleOrderId = null;
		if (!empty($depositTranId)) {
			$this->db->select('order_id')->from('transactions')
				->where('id', $depositTranId)->where('transaction_type', self::DEPOSIT);
			return $this->runOneRowOneField('order_id');
		}
		return $saleOrderId;
	}

	public function convertSubwalletId() {
		// $apis=$this->utils->getAllCurrentGameSystemList();

		$sql = <<<EOD
update transactions join playeraccount on transactions.sub_wallet_id= playeraccount.playerAccountId
set transactions.sub_wallet_id=playeraccount.typeId where sub_wallet_id is not null and sub_wallet_id!=0
EOD;

		return $this->runRawUpdateInsertSQL($sql);
	}

	/**
	 * detail: get all transfer transactions for a certain player
	 *
	 * @param int $playerId trans to_id
	 * @param int $sub_wallet_id trans field
	 * @param string $periodFrom trans created_at
	 * @param string $periodTo trans created_at
	 *
	 * @return array
	 */
	public function getListTransferTrans($playerId, $sub_wallet_id_or_arr, $periodFrom, $periodTo) {

		$this->db->select('id,amount')->from($this->tableName)
			->where('transaction_type', self::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId)
			->where('ignore_promotion_check', self::DB_FALSE)
			->where('created_at >=', $periodFrom)
			->where('created_at <=', $periodTo);

		if (is_array($sub_wallet_id_or_arr)) {
			$this->db->where_in('sub_wallet_id', $sub_wallet_id_or_arr);
		} else {
			$this->db->where('sub_wallet_id', $sub_wallet_id_or_arr);
		}

		// if (!empty($playerPromoIds)) {
		// 	$this->db->where('(player_promo_id is null or player_promo_id not in (' . implode(',', $playerPromoIds) . ') )', null, false);
		// 	// $this->db->or_where_not_in('player_promo_id', $playerPromoIds);
		// } else {
		// 	$this->db->where('player_promo_id is null', null, false);
		// }

		// if ($minAmount !==null && $maxAmount !=null) {
		// 	$this->db->where('amount >=', $minAmount)->where('amount <=', $maxAmount);
		// }

		$this->addWhereApproved();
		$this->db->order_by('created_at', 'asc');
		// $this->limitOneRow();

		return $this->runMultipleRow();
	}

	public function isOnlyFirstDeposit($playerId, $from = null, $to = null) {
		$this->db->select('count(id) cnt', false)->from('transactions')->where('transaction_type', self::DEPOSIT)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId);
		if (!empty($from) && !empty($to)) {
			$this->db->where('created_at >=', $from)
				->where('created_at <=', $to);
		}
		$this->addWhereApproved();

		return $this->runOneRowOneField('cnt') == 1;
	}

	/**
	 * detail: get todays withdraw for a certain player
	 *
	 * @param int $playerId trans to_id field
	 * @return array
	 */
	public function get_today_withdraw($playerId) {
		$date = $this->utils->getTodayForMysql();
		$sql = "SELECT sum(amount) as trans_amount, count(id) as trans_count FROM transactions WHERE to_type=? and to_id= ? AND created_at >= ? and created_at <=? AND transaction_type= ? ";
		$qry = $this->db->query($sql, [self::PLAYER, $playerId, $date . ' 00:00:00', $date . ' 23:59:59', self::WITHDRAWAL]);
		return $this->getOneRow($qry);
	}

	/**
	 * detail: check if player has any deposit done
	 *
	 * @param int $playerId trans to_id
	 * @param string $from trans created_at
	 * @param string $to trans created_at
	 *
	 * @return Boolean or int
	 */
	public function hasAnyDeposit($playerId, $from = null, $to = null) {
		$this->db->select('id')->from('transactions')->where('transaction_type', self::DEPOSIT)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId);
		if (!empty($from) && !empty($to)) {
			$this->db->where('created_at >=', $from)
				->where('created_at <=', $to);
		}
		$this->addWhereApproved();
		$this->db->limit(1);

		return $this->runExistsResult();
	}

	/**
	 * detail: get deposit list of a certain player
	 *
	 * @param int $playerId trans to_id field
	 * @param string $periodFrom
	 * @param string $periodTo
	 *
	 * @return array
	 */
	public function getDepositListBy($playerId, $periodFrom, $periodTo) {
		$this->db->select('created_at, amount')->from($this->tableName)
			->where('transaction_type', self::DEPOSIT)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId)
			->where('created_at >=', $periodFrom)
			->where('created_at <=', $periodTo);

		$this->addWhereApproved();
		$this->db->order_by('created_at', 'asc');
		// $this->limitOneRow();

		return $this->runMultipleRow();
	}

	/**
	 * Re-calculate figures for admin dashboard (/home) and store to database
	 * Date arguments added in OGP-15002
	 *
	 * @param	array 	$dates	array arg, may contain following fields:
	 *                      data_base		datestring	recalc base date for most dashboard blocks
	 *                      data_range_from	datestring	from date for revenue chart
	 *                      data_range_to	datestring	to date for revenue chart
	 * @see		Home::index()
	 * @see		Transactions::getDashboard()
	 *
	 * @return [type]        [description]
	 */
	public function syncDashboard($dates = []) {
		// OGP-15002: data generation for week revenue/active players charts
		$date_base			= isset($dates['date_base']) ? date('Y-m-d', strtotime($dates['date_base'])) : $this->utils->getTodayForMysql();
		$date_base_sub_1	= date('Y-m-d', strtotime("{$date_base} -1 day"));

		$time_base			= isset($dates['date_base']) ? $dates['date_base'] : $this->utils->getNowForMysql();

        $date_range_to		= isset($dates['date_range_to']) ? date('Y-m-d', strtotime($dates['date_range_to'])) : date('Y-m-d', strtotime($date_base));
        $date_range_from	= isset($dates['date_range_from']) ? date('Y-m-d', strtotime($dates['date_range_from'])) : date('Y-m-d', strtotime($date_base . '-6 day'));

		$date_disp			= isset($dates['date_disp']) ? date('Y-m-d', strtotime($dates['date_disp'])) : $date_base;

		$this->utils->debug_log(__METHOD__, 'dates', [ 'date_base' => $date_base, 'date_range_from' => $date_range_from, 'date_range_to' => $date_range_to ]);

		// The main event

		$this->load->model(['player_model', 'wallet_model', 'transactions', 'total_player_game_hour', 'total_player_game_day', 'report_model']);

		$this->load->library([ 'report_functions' ]);

		$number_arr = ['today_member_count', 'yesterday_member_count', 'today_deposit_sum', 'today_deposited_player', 'today_deposit_count',
			'today_withdrawal_sum', 'today_withdrawed_player', 'today_withdraw_count', 'all_member_count', 'all_member_deposited', 'all_member_balance',
			'count_player_session'];

		# TODAY NEW REGISTERED
		$data['today_member_count'] = $this->player_model->totalRegisteredPlayersByDate($date_base);
		# YESTERDAY NEW REGISTERED
		$data['yesterday_member_count'] = $this->player_model->totalRegisteredPlayersByDate($date_base_sub_1);

		# TODAY SUM OF DEPOSIT
		$data['today_deposit_sum'] = $this->transactions->getTotalDepositsToday(null, $date_base);
		# TODAY DEPOSITED PLAYER
		$data['today_deposited_player'] = $this->transactions->getTotalDepositedPlayer(null, $date_base);
		# TODAY DEPOSIT COUNT
		$data['today_deposit_count'] = $this->transactions->getTodayTotalDepositCount(null, $date_base);
		# TODAY SUM OF WITHDRAWAL
		$data['today_withdrawal_sum'] = $this->transactions->getTotalWithdrawalsToday(null, $date_base);
		# TODAY WITHDRAWED PLAYER
		$data['today_withdrawed_player'] = $this->transactions->getTotalWithdrawedPlayer(null, $date_base);
		# TODAY WITHDRAW COUNT
		$data['today_withdraw_count'] = $this->transactions->getTodayTotalWithdrawCount(null, $date_base);

		# COUNT MEMBER
		$data['all_member_count'] = $this->player_model->totalRegisteredPlayers(array('deleted_at' => NULL));
		// Count of players who have deposited (not amount)
		$data['all_member_deposited'] = $this->player_model->totalPlayerDeposited();
		$data['all_member_balance'] = $this->wallet_model->totalMainWalletBalance();
		//get active players last hour
		// $data['count_player_session'] = $this->player_model->countPlayerSession(new DateTime("{$time_base} -1 hour"));
		$data['count_player_session'] = $this->total_player_game_hour->getActivePlayersCountByHour(date('YmdH', strtotime("{$time_base} -1 hour")));

		# DEPOSIT COUNT TOP 10 TODAY
		$data['today_last_deposit_list'] = json_encode($this->transactions->getTopDepositCount(true, 10, $date_base));
		# DEPOSIT MAX TOP 10 TODAY
		$data['today_max_deposit_list'] = json_encode($this->transactions->getTopDepositSum(true, 10, false, $date_base));
		# Total deposit amount top 10 today
		$data['today_total_deposit_list'] = json_encode($this->transactions->getTopDepositSum(true, 10, true, $date_base));
		# DEPOSIT COUNT TOP 10 ALL
		$data['all_max_deposit_list'] = json_encode($this->transactions->getTopDepositCount());
		# LAST 7 DAYS DEPOSIT
		$data['weekly_deposit_list'] = json_encode($this->transactions->getLastDeposits(7, $date_base, $date_disp));
		# LAST 7 DAYS WITHDRAWAL
		$data['weekly_withdraw_list'] = json_encode($this->transactions->getLastWithdraws(7, $date_base, $date_disp));
		# LAST 7 DAYS REGISTERED MEMBER
		$data['weekly_member_list'] = json_encode($this->player_model->getLastMembers(7, $date_base, $date_disp));

		$data['total_all_balance_include_subwallet'] = $this->player_model->getPlayersTotalBallanceIncludeSubwallet();

		// OGP-11382
		// Top 10 players today, by bet amount
		$data['top_bet_amount_players_today'] = json_encode($this->total_player_game_hour->topPlayersByBetAmountWithinDate("{$date_base} 00:00:00", "{$date_base} 23:59:59"));
		// Top 10 games today, by bet amount
		$data['top_bet_amount_games_today'] = json_encode($this->total_player_game_hour->topGamesByBetAmountWithinDate("{$date_base} 00:00:00", "{$date_base} 23:59:59"));

		// OGP-12377
		// All-time total bet amount
		$data['total_bet_amount_all_time'] = $this->player_model->getTotalBettingAmountAllTime();
		// All-time total deposit amount
		$data['total_deposit_amount_all_time'] = $this->transactions->getTotalDepositsAllTime();
		// All-time total withdrawal amount
		$data['total_withdraw_amount_all_time'] = $this->transactions->getTotalWithdrawalsAllTime();


		$other_data = [
			'total_active_players'		=> $this->total_player_game_day->getTodayActivePlayers(null, $date_base) ,
			'top_withdrawal_count'		=> $this->transactions->getTodayTopWithdraw(10, false, $date_base) ,
			'today_total_withdrawal_list'		=> $this->transactions->getTodayTopWithdraw(10, 'by amount', $date_base) ,
			'total_active_players_yesterday'	=> $this->total_player_game_day->getActivePlayersCountByDate($date_base_sub_1) ,
			'active_players_by_gametype'	=> $this->report_model->dashboard_daily_active_players($date_base) ,
			'summary_week'					=> $this->report_functions->dashboard_revenue_chart($date_range_from, $date_range_to, $date_disp) ,
			'date_base'			=> $time_base ,
			'date_range_from'	=> $date_range_from ,
			'date_range_to'		=> $date_range_to ,
			'date_disp'			=> $date_disp ,
		];

		$data['other_dashboard_fields'] = json_encode($other_data);

		#TODAY'S TRANSACTIONS
		$start_date	= "{$date_base} 00:00:00";
		$end_date	= "{$date_base} 23:59:59";

		$this->utils->debug_log(__METHOD__, ['start_date' => $start_date, 'end_date' => $end_date]);

		$transactions = $this->transactions->getTransactionsSummary(
			array(
				Transactions::DEPOSIT,
				Transactions::WITHDRAWAL,
				Transactions::FEE_FOR_PLAYER,
				Transactions::FEE_FOR_OPERATOR,
				Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET,
				Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET,
				Transactions::MANUAL_ADD_BALANCE,
				Transactions::MANUAL_SUBTRACT_BALANCE,
				Transactions::ADD_BONUS,
				Transactions::SUBTRACT_BONUS,
				Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET,
				Transactions::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET,
				Transactions::AUTO_ADD_CASHBACK_TO_BALANCE,
				Transactions::MEMBER_GROUP_DEPOSIT_BONUS,
				Transactions::PLAYER_REFER_BONUS,
				Transactions::AFFILIATE_MONTHLY_EARNINGS,
				Transactions::ADMIN_ADD_BALANCE_TO_AFFILIATE,
				Transactions::ADMIN_SUBTRACT_BALANCE_TO_AFFILIATE,
			),
			$start_date, $end_date);

		$data['transactions'] = !empty($transactions) ? json_encode($transactions) : json_encode(array());

		foreach ($number_arr as $fld) {
			if (empty($data[$fld])) {
				$data[$fld] = 0;
			}
		}

		$this->db->select('id')->from('admin_dashboard')->limit(1);
		$id = $this->runOneRowOneField('id');
		if (!empty($id)) {

			$data['updated_at'] = $this->utils->getNowForMysql();
			$this->db->where('id', $id)->set($data);
			return $this->runAnyUpdate('admin_dashboard');
		} else {
			$data['created_at'] = $this->utils->getNowForMysql();
			return $this->insertData('admin_dashboard', $data);
		}
	} // End function syncDashboard()

	public function getDashboard() {
		if ($this->utils->getConfig('admin_dashboard_cache_use_redis')) {
			$redis_key_dashboard = '_sbe-admin_dashboard';
			$redis_ttl_dashboard = $this->utils->getConfig('redis_ttl_dashboard') ?: 60;

			$this->utils->debug_log(__METHOD__, "redis lifetime", $redis_ttl_dashboard);

			$row_json = $this->utils->readRedis($redis_key_dashboard);
			if (!empty($row_json)) {
				$this->utils->debug_log(__METHOD__, "redis key {$redis_key_dashboard} found, using value from redis");
				$row = json_decode($row_json, 'as array');

				return $row;
			}

			$this->utils->debug_log(__METHOD__, "redis key {$redis_key_dashboard} not found, refreshing from db");
		}

		$this->db->from('admin_dashboard')->limit(1);

		$row = $this->runOneRowArray();
		$list_arr = ['today_last_deposit_list', 'today_max_deposit_list', 'today_total_deposit_list', 'all_max_deposit_list',
			'weekly_deposit_list', 'weekly_withdraw_list', 'weekly_member_list', 'transactions', 'top_bet_amount_players_today', 'top_bet_amount_games_today'];

		foreach ($list_arr as $fld) {
			if (empty($row[$fld])) {
				$row[$fld] = [];
			} else {
				$row[$fld] = $this->utils->decodeJson($row[$fld]);
			}
		}

		// Setup default value for other_fields
		$other_fields = [
			'total_active_players'		=> 0 ,
			'top_withdrawal_count'		=> [] ,
			'total_active_players_yesterday'	=> 0 ,
			'active_players_by_gametype' => [
				'morris' => [ [ 'label' => "Not available", 'value' => 0 ] ] ,
				'cat_cues' => 'others' ,
			] ,
			'summary_week' => [
				'sums' => [ 'amount_bet' => 0, 'gross_profit' => 0 ] ,
				'chart_figs' => [ ["x"], ["amount_bet"], ["gross_profit"], ["ratio_bet"], ["ratio_profit"] ]
			] ,
			'date_base'			=> null ,
			'date_range_from'	=> null ,
			'date_range_to'		=> null
		];

		// Fetch and merge value of other_fields from db if available
		if (!empty($row['other_dashboard_fields'])) {
			$other_fields_db = json_decode($row['other_dashboard_fields'], 'as array');
			$other_fields = array_merge($other_fields, $other_fields_db);
		}

		// Combine main row and other_fields
		unset($row['other_dashboard_fields']);
		$row = array_merge($row, $other_fields);

		if ($this->utils->getConfig('admin_dashboard_cache_use_redis')) {
			$row_json = json_encode($row);
			$this->utils->writeRedis($redis_key_dashboard, $row_json, $redis_ttl_dashboard);
		}

		return $row;
	}

	public function getDashboardLastUpdateTime() {
		$this->db->from('admin_dashboard')
			->select("updated_at,
				coalesce(other_dashboard_fields->>'$.date_base', '') AS date_base, coalesce(other_dashboard_fields->>'$.date_range_from', '') AS date_range_from, coalesce(other_dashboard_fields->>'$.date_range_to', '') AS date_range_to, coalesce(other_dashboard_fields->>'$.date_disp', '') AS date_disp", false)
			->limit(1)
			->order_by('updated_at', 'desc')
		;

		// $res = $this->runOneRowOneField('updated_at');
		$res = $this->runOneRowArray();

		return $res;
	}

	/**
	 * detail: check the status of a transaction
	 *
	 * @param int $external_tran_id
	 * @return array
	 */
	public function checkStatusTransactionBy($external_tran_id) {
		$this->db->select('status')->from('transactions')->where('external_transaction_id', $external_tran_id);

		return $this->runOneRowOneField('status');
	}

	/**
	 * detail: sum of the first time deposit record of a players
	 *
	 * @param array $playerIdArr
	 */
	public function sumFirstTimeDepositInfo($playerIdArr) {
		$ftdNumber = 0;
		$ftdAmount = 0;
		$playerIdArr = $this->utils->clearIdArray($playerIdArr);
		if (!empty($playerIdArr)) {

			$this->db->select("to_id as player_id, min(concat(created_at,'|',amount)) as first_time", false)
				->from('transactions')->where_in('to_id', $playerIdArr)
				->where('transaction_type', self::DEPOSIT)
				->where('to_type', self::PLAYER)
				->where('status', self::APPROVED)
				->group_by('to_id');

			$rows = $this->runMultipleRowArray();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					$first_time = $row['first_time'];
					$player_id = $row['player_id'];

					$this->utils->debug_log('player_id', $player_id, 'first_time', $first_time);
					//extract
					$arr = explode('|', $first_time);
					$amt = @$arr[1];
					$ftdNumber++;
					$ftdAmount += $amt;
				}
			}
		}

		return [$ftdNumber, $ftdAmount];
	}

	public function getLastDepositInfoByFreq($playerId, $frequency, $today) {
		//default is today
		$fromDate = $today . ' 00:00:00';
		$toDate = $today . ' 23:59:59';
		switch ($frequency) {
		case self::FREQUENCRY_ALL:
			$fromDate = null;
			$toDate = null;
			break;
		case self::FREQUENCRY_WEEKLY:
			list($fromDate, $toDate) = $this->utils->getFromToByWeek($today);
			$fromDate = $fromDate . ' 00:00:00';
			$toDate = $toDate . ' 23:59:59';
			break;

		case self::FREQUENCRY_MONTHLY:
			list($fromDate, $toDate) = $this->utils->getFromToByMonth($today);
			$fromDate = $fromDate . ' 00:00:00';
			$toDate = $toDate . ' 23:59:59';
			break;
		}

		return $this->getLastDepositInfoByDate($playerId, $fromDate, $toDate);
	}

	public function getLastDepositInfoByDate($playerId, $fromDatetime, $toDatetime) {
		$this->db->from($this->tableName)
			->where('transaction_type', self::DEPOSIT)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId);

		if (!empty($fromDatetime) && !empty($toDatetime)) {
			$this->db->where('created_at >=', $fromDatetime)
				->where('created_at <=', $toDatetime);
		}

		$this->addWhereApproved();

		$this->db->order_by('created_at', 'desc')->limit(1);

		$row = $this->runOneRowArray();
		if (!empty($row)) {

			//query order
			$this->db->select('id')->from('transactions')
				->where('transaction_type', self::DEPOSIT)
				->where('to_type', self::PLAYER)
				->where('to_id', $playerId);

			if (!empty($fromDatetime) && !empty($toDatetime)) {
				$this->db->where('created_at >=', $fromDatetime)
					->where('created_at <=', $toDatetime);
			}

			$this->addWhereApproved();

			$this->db->order_by('created_at', 'asc');
			$rows = $this->runMultipleRowArray();
			$cnt = 0;
			$row['transOrder'] = 0;
			foreach ($rows as $orderRow) {
				if ($orderRow['id'] == $row['id']) {
					$cnt++;
					$row['transOrder'] = $cnt;
					break;
				}
			}
		}

		return $row;
	}

	public function searchLastTransfer($playerId) {
		$this->db->from($this->tableName)
			->where('transaction_type', self::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId);

		$this->addWhereApproved();

		$this->db->order_by('id', 'desc');

		$this->db->limit(1);

		$transRow = $this->runOneRowArray();

		return $transRow;
	}

	public function getTotalDailyDepositWithCache($paId) {
		$cacheKey = 'sum_' . $this->utils->getTodayForMysql() . '_deposit_' . $paId;

		$amtTxt = $this->utils->getTextFromCache($cacheKey);
		if ($amtTxt !== 'null' && empty($amtTxt)) {
			//load from db
			$amount = $this->getTotalDailyDeposit($paId);
			//save to cache
			$this->utils->saveTextToCache($cacheKey, empty($amount) ? 'null' : $amount, 60);
		} else {
			if ($amtTxt === 'null') {
				$amtTxt = 0;
			}
			$amount = floatval($amtTxt);
		}

		return $amount;
	}

	public function getTotalDepositWithCache($paId) {
		$cacheKey = 'sum_deposit_' . $paId;

		$amtTxt = $this->utils->getTextFromCache($cacheKey);
		if ($amtTxt !== 'null' && empty($amtTxt)) {
			//load from db
			$amount = $this->getTotalDeposit($paId);
			//save to cache
			$this->utils->saveTextToCache($cacheKey, empty($amount) ? 'null' : $amount, 3600);
		} else {
			if ($amtTxt === 'null') {
				$amtTxt = 0;
			}
			$amount = floatval($amtTxt);
		}

		return $amount;
	}

	/**
	 * Returns list of today's top players by withdrawal 'count'
	 * @param  integer $limit       [description]
	 * @param  boolean $total_today [description]
	 * @param  [type]  $adate       [description]
	 * @return [type]               [description]
	 */
	public function getTodayTopWithdraw($limit = 10, $total_today = false, $adate = null) {
		// OGP-15002 add date argument
		$today = !empty($adate) ? $adate : $this->utils->getTodayForMysql();

		$start_today = $today . ' 00:00:00';
		$end_today = $today . ' 23:59:59';

		$this->db->select("p.playerid, p.username as username,t.to_id, sum(t.amount) as total_withdraw_amount, count(1) as count , t.status");
		$this->db->from("transactions as t");
		$this->db->join("player as p", "p.playerId = t.to_id");
		$this->db->where("t.status", self::APPROVED);
		$this->db->where("t.to_type", self::PLAYER);
		$this->db->where("t.transaction_type", self::WITHDRAWAL);
		$this->db->where('t.created_at >=', $start_today);
		$this->db->where('t.created_at <=', $end_today);
		$this->db->group_by("t.to_id");
        if($total_today){
            $this->db->order_by("total_withdraw_amount", "desc");
        }else{
            $this->db->order_by("count", "desc");
        }
		$this->db->limit($limit);
		$query = $this->db->get();
		// $this->utils->printLastSQL();
		// echo $this->db->last_query();exit;
		return $query->result_array();
	}

	public function get_players_with_minimum_deposit($deposit_amount, $from = NULL, $to = NULL, $players_id = NULL) {

		if(empty($deposit_amount)){
			$deposit_amount=0;
		}

		$this->db->select('IF(transactions.from_type = ' . self::PLAYER . ', from_id, IF(transactions.to_type = ' . self::PLAYER . ', to_id, 0)) as player_id', false);
		$this->db->select_sum('amount', 'deposit_amount');
		$this->db->from($this->tableName);

		if ($from != null) {
			$this->db->where('created_at >=', $from);
		}

		if ($to != null) {
			$this->db->where('created_at <=', $to);
		}
		$this->addWhereApproved();
		$this->db->where('transaction_type IN (' . self::DEPOSIT . ',' . self::MANUAL_ADD_BALANCE . ')');
		if (!empty($players_id)) {
			$this->db->where('IF (transactions.to_type = ' . self::PLAYER . ' AND to_id IN (' . implode(',', $players_id) . '), true, IF (transactions.from_type = ' . self::PLAYER . ' AND from_id IN (' . implode(',', $players_id) . '),true, false))', NULL, FALSE);
		} else {
			$this->db->where('IF (transactions.to_type = ' . self::PLAYER . ', true, IF (transactions.from_type = ' . self::PLAYER . ', true, false))', NULL, FALSE);
		}

		$this->db->group_by('player_id');
		$this->db->having('player_id !=', 0);
		$this->db->having('deposit_amount >=', $deposit_amount);

		$rows = $this->runMultipleRowArray() ?: array();

		return array_column($rows, 'player_id');
	}

	public function get_players_with_minimum_deposit_and_minimum_deposit_count($deposit_amount, $deposit_count, $from = NULL, $to = NULL, $players_id = NULL) {

		$this->db->select('IF(transactions.from_type = ' . self::PLAYER . ', from_id, IF(transactions.to_type = ' . self::PLAYER . ', to_id, 0)) as player_id', false);
		$this->db->select_sum('amount', 'deposit_amount');
		$this->db->from($this->tableName);

		if ($from != null) {
			$this->db->where('created_at >=', $from);
		}

		if ($to != null) {
			$this->db->where('created_at <=', $to);
		}
		$this->addWhereApproved();
		$this->db->where('transaction_type IN (' . self::DEPOSIT . ',' . self::MANUAL_ADD_BALANCE . ')');
		if (!empty($players_id)) {
			$this->db->where('IF (transactions.to_type = ' . self::PLAYER . ' AND to_id IN (' . implode(',', $players_id) . '), true, IF (transactions.from_type = ' . self::PLAYER . ' AND from_id IN (' . implode(',', $players_id) . '),true, false))', NULL, FALSE);
		} else {
			$this->db->where('IF (transactions.to_type = ' . self::PLAYER . ', true, IF (transactions.from_type = ' . self::PLAYER . ', true, false))', NULL, FALSE);
		}

		$this->db->where('amount >=', $deposit_count);
		$this->db->group_by('player_id');
		$this->db->having('player_id !=', 0);
		$this->db->having('deposit_amount >=', $deposit_amount);

		$rows = $this->runMultipleRowArray() ?: array();

		return array_column($rows, 'player_id');
	}
	/**
	 * detail: get total deposit for a certain player and date range
	 *
	 * @param int $playerId transaction to_id field
	 * @param string $from transaction created_at
	 * @param string $to transaction created_at
	 *
	 * @return double or int
	 */
	public function getPlayerDepositsTrasactionList($playerId) {
		$this->db->select('pa.*');
		$this->db->from($this->tableName . ' AS t');
		$this->db->where('t.to_id', $playerId)
			->where('t.to_type', self::PLAYER)
			->where('t.transaction_type', self::DEPOSIT)
			->where('t.status', self::APPROVED);
		$this->db->join('payment_account AS pa', 't.payment_account_id = pa.id', 'left');
		$query = $this->db->get();
		$payment_method = array();
		$payment_flag_key = array();
		$account_name = array();
		$paymentAccountFlags = $this->utils->getPaymentAccountAllFlagsKV();

		foreach ($query->result_array() as $key => $value) {
			$payment_method[] = @$paymentAccountFlags[$value['flag']];
			$payment_flag_key[] = $value['flag'];
			$account_name[] = $value['payment_account_name'];
		}

		$response = array(
			'payment_method' => $payment_method,
			'payment_flag_key' => $payment_flag_key,
			'account_name' => $account_name,
		);

		return $response;
	}

	/**
	 * detail: get total cashback of a certain player
	 *
	 * @param int $playerId transation to_id field
	 * @return double or int
	 */
	public function getPlayerTotalCashback($playerId, $date_from = null, $date_to = null) {
		$this->db->select('sum(amount) as total_deposit', false)->from($this->tableName)
			->where('to_id', $playerId)
			->where('to_type', self::PLAYER)
			->where('transaction_type', self::CASHBACK);

		if (!empty($date_from) && !empty($date_to)) {

			$this->db->where('created_at >=', $date_from);
			$this->db->where('created_at <=', $date_to);

		}

		$this->addWhereApproved();

		return $this->runOneRowOneField('total_deposit');
	}

    /**
     * detail: create pending deposit transaction
     *
     * @param array $saleOrder
     * @param int $adminUserId current logged user
     * @param int $flag
     *
     * @return Boolean
     */
    public function createPendingDepositTransaction($saleOrder, $adminUserId, $flag = self::MANUAL) {
        if ($saleOrder) {
            $this->load->model(array('wallet_model', 'player_model', 'users', 'sale_order'));

            $playerId = $saleOrder['player_id'];
            $amount = $saleOrder['amount'];
            $adminUserId = $adminUserId ? $adminUserId : 1;
            $from_username = $this->users->getUsernameById($adminUserId);
            $to_username = $this->player_model->getUsernameById($playerId);
            $transaction_type = self::DEPOSIT;

            $beforeBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);
            // Disabled these. Due to this transaction is for pending deposits
            // $this->wallet_model->incMainDepositOnBigWallet($playerId, $amount);
            // $afterBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);

            $transactionDetails = array('amount' => $amount,
                'transaction_type' => $transaction_type, //deposit
                'from_id' => $adminUserId, // $this->authentication->getUserId(),
                'from_type' => self::ADMIN, //admin
                'from_username' => $from_username,
                'to_id' => $playerId,
                'to_type' => self::PLAYER, //player
                'to_username' => $to_username,
                'note' => $adminUserId . ' deposit pending ' . $amount . ' to ' . $playerId,
                'status' => self::PENDING, //pending
                'flag' => $flag, //manual
                'created_at' => $this->utils->getNowForMysql(),
                'updated_at' => $this->utils->getNowForMysql(),
                'order_id' => $saleOrder['id'],
                'payment_account_id' => $saleOrder['payment_account_id'],
                // 'balance_history_id' => $beforeHistoryId,
                'trans_date' => $this->utils->getTodayForMysql(),
                'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
                'trans_year' => $this->utils->getThisYearForMysql(),

                'before_balance' => $beforeBalanceDetails['total_balance'],
                // 'after_balance' => $afterBalanceDetails['total_balance'],
                'total_before_balance' => $beforeBalanceDetails['total_balance'],
                // 'changed_balance' => $this->utils->encodeJson(array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount)),
            );

            $rlt = $this->insertRow($transactionDetails);
            $this->sale_order->updateSaleOrderTransactionID($saleOrder['id'], $rlt);

            // $this->updateChangedBalance($rlt, array('before' => $beforeBalanceDetails,
            //  'after' => $afterBalanceDetails, 'amount' => $amount));

            $afterHistoryId = $this->recordPlayerAfterActionWalletBalanceHistory($transaction_type,
                $playerId, null, $rlt, $amount, $saleOrder['id']);
            $this->updateBalanceHistoryTransactionId($afterHistoryId, $rlt);

            // $this->load->model('daily_player_trans');
            // $this->daily_player_trans->update_today($transactionDetails);
            //update player total deposit amount
            // $this->player_model->updateTotalDepositAmount($playerId);
            return $rlt;
        }
        return false;
    }

    /**
     * detail: create declined deposit transaction
     *
     * @param array $saleOrder
     * @param int $adminUserId current logged user
     * @param int $flag
     *
     * @return Boolean
     */
    public function createDeclinedDepositTransaction($saleOrder, $adminUserId, $flag = self::MANUAL) {
        if ($saleOrder) {
            $this->load->model(array('wallet_model', 'player_model', 'users', 'sale_order'));

            $playerId = $saleOrder->player_id;
            $amount = $saleOrder->amount;
            $adminUserId = $adminUserId ? $adminUserId : 1;
            $from_username = $this->users->getUsernameById($adminUserId);
            $to_username = $this->player_model->getUsernameById($playerId);
            $transaction_type = self::DEPOSIT;

            $beforeBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);
            // Disabled these. Due to this transaction is for pending deposits
            // $this->wallet_model->incMainDepositOnBigWallet($playerId, $amount);
            // $afterBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);

            $transactionDetails = array('amount' => $amount,
                'transaction_type' => $transaction_type, //deposit
                'from_id' => $adminUserId, // $this->authentication->getUserId(),
                'from_type' => self::ADMIN, //admin
                'from_username' => $from_username,
                'to_id' => $playerId,
                'to_type' => self::PLAYER, //player
                'to_username' => $to_username,
                'note' => $adminUserId . ' deposit declined ' . $amount . ' to ' . $playerId,
                'status' => self::DECLINED, //declined
                'flag' => $flag, //manual
                'created_at' => $this->utils->getNowForMysql(),
                'updated_at' => $this->utils->getNowForMysql(),
                'order_id' => $saleOrder->id,
                'payment_account_id' => $saleOrder->payment_account_id,
                // 'balance_history_id' => $beforeHistoryId,
                'trans_date' => $this->utils->getTodayForMysql(),
                'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
                'trans_year' => $this->utils->getThisYearForMysql(),

                'before_balance' => $beforeBalanceDetails['total_balance'],
                // 'after_balance' => $afterBalanceDetails['total_balance'],
                'total_before_balance' => $beforeBalanceDetails['total_balance'],
                // 'changed_balance' => $this->utils->encodeJson(array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount)),
            );

            $rlt = $this->insertRow($transactionDetails);
            $this->sale_order->updateSaleOrderTransactionID($saleOrder->id, $rlt);

            // $this->updateChangedBalance($rlt, array('before' => $beforeBalanceDetails,
            //  'after' => $afterBalanceDetails, 'amount' => $amount));

            $afterHistoryId = $this->recordPlayerAfterActionWalletBalanceHistory($transaction_type,
                $playerId, null, $rlt, $amount, $saleOrder->id);
            $this->updateBalanceHistoryTransactionId($afterHistoryId, $rlt);

            // $this->load->model('daily_player_trans');
            // $this->daily_player_trans->update_today($transactionDetails);
            //update player total deposit amount
            // $this->player_model->updateTotalDepositAmount($playerId);
            return $rlt;
        }
        return false;
    }

	public function createTransactionInternalWithdrawal($amount, $adminUserId, $transaction_type = self::INTERNALWITHDRAWAL, $flag = self::MANUAL) {
		$adminUserId = $adminUserId ? $adminUserId : 1;
		$from_username = $this->users->getUsernameById($adminUserId);
		$note = $from_username . ' add Internal Withdrawal Amount : ' . $amount;
		$beforeBalanceDetails = $this->wallet_model->getBalanceDetails($adminUserId);
		$afterBalanceDetails = $this->wallet_model->getBalanceDetails($adminUserId);
		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $adminUserId, // $this->authentication->getUserId(),
			'from_type' => self::ADMIN, //admin
			'from_username' => $from_username,
			'to_id' => $adminUserId,
			'to_type' => self::ADMIN, //player
			'to_username' => $from_username,
			'note' => $note,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'status' => self::APPROVED, //approved
			'flag' => $flag, //manual
			'created_at' => $this->utils->getNowForMysql(),
			'updated_at' => $this->utils->getNowForMysql(),
			'total_before_balance' => $afterBalanceDetails['total_balance'],
			'trans_date' => $this->utils->getTodayForMysql(),
			'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
			'trans_year' => $this->utils->getThisYearForMysql(),
			'changed_balance' => $this->utils->encodeJson(array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount)),
		);
		$rlt = $this->insertRow($transactionDetails);

		$afterHistoryId = $this->recordPlayerAfterActionWalletBalanceHistory($transaction_type,
			$adminUserId, null, $rlt, $amount);
		$this->updateBalanceHistoryTransactionId($afterHistoryId, $rlt);

		return $rlt;
	}

	public function getTotalDepositWithdrawalByPaymentAccount($bankTypeId, $transactionType, $from, $to) {
		$this->db->select('sum(transactions.amount) as total', false)->from($this->tableName)
			->join('payment_account', 'transactions.payment_account_id = payment_account.id')
			->join('banktype', 'payment_account.payment_type_id = banktype.bankTypeId', 'left')
			->where('transactions.transaction_type', $transactionType)
			->where('transactions.status', self::APPROVED)
			->where('banktype.bankTypeId', $bankTypeId)
			->where('transactions.created_at >= ', $from)
			->where('transactions.created_at <= ', $to);

		return $this->runOneRowOneField('total');
	}

	public function getTotalManualAdjustmentByWalletId($from, $to, $sub_wallet_id) {
		$incSQL = "SELECT sum(amount) AS total FROM transactions WHERE created_at >= '$from' AND created_at <= '$to' AND sub_wallet_id = $sub_wallet_id AND transaction_type = " . self::MANUAL_ADD_BALANCE_ON_SUB_WALLET;
		$this->utils->debug_log('------------------------------------------getTotalManualAdjustmentByWalletId incSQL : ' . $incSQL);
		$incResult = $this->db->query($incSQL);
		$incManual = $this->getOneRowOneField($incResult, 'total');
		$incManual = (empty($incManual)?0:$incManual);
		$decSQL = "SELECT sum(amount) AS total FROM transactions WHERE created_at >= '$from' AND created_at <= '$to' AND sub_wallet_id = $sub_wallet_id AND transaction_type = " . self::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET;
		$this->utils->debug_log('------------------------------------------getTotalManualAdjustmentByWalletId decSQL : ' . $decSQL);
		$decResult = $this->db->query($decSQL);
		$decManual = $this->getOneRowOneField($decResult, 'total');
		$decManual = (empty($decManual)?0:$decManual);
		$this->utils->debug_log('------------------------------------------getTotalManualAdjustmentByWalletId -> incManual : '.$incManual.' decManual : ' . $decManual);
		return $incManual - $decManual;
	}

	public function getTotalBonusByWalletId($from, $to, $sub_wallet_id) {
		$incSQL = "SELECT sum(amount) AS total FROM transactions WHERE created_at >= '$from' AND created_at <= '$to' AND sub_wallet_id = $sub_wallet_id AND transaction_type in (" . self::ADD_BONUS . ", " . self::MEMBER_GROUP_DEPOSIT_BONUS . ", " . self::PLAYER_REFER_BONUS . ", " . self::RANDOM_BONUS . ", " . self::BIRTHDAY_BONUS . ")";
		$incResult = $this->db->query($incSQL);
		$incBonus = $this->getOneRowOneField($incResult, 'total');
		$incBonus = (empty($incBonus)?0:$incBonus);
		$decSQL = "SELECT sum(amount) AS total FROM transactions WHERE created_at >= '$from' AND created_at <= '$to' AND sub_wallet_id = $sub_wallet_id AND transaction_type in (" . self::SUBTRACT_BONUS . ")";
		$decResult = $this->db->query($decSQL);
		$decBonus = $this->getOneRowOneField($decResult, 'total');
		$decBonus = (empty($decBonus)?0:$decBonus);
		return $incBonus - $decBonus;
	}

	public function getLastTransactions($limit = 10) {
		$this->db->select('created_at as date');
		$this->db->select('to_username as username');
		$this->db->select('transaction_type as type');
		$this->db->select('amount');
		$this->db->where('status', self::APPROVED);
		$this->db->where_in('transaction_type', array(self::DEPOSIT, self::WITHDRAWAL));
		$this->db->where('to_type', self::PLAYER);
		$this->db->order_by('created_at', 'DESC');
		$query = $this->db->get($this->tableName, $limit);
		return $query->result_array();
	}

	public function countDepositAmount($playerId, $from_datetime, $to_datetime, $min_amount) {
		$this->db->select('count(id) as cnt', false)->from($this->tableName)
			->where('transaction_type', self::DEPOSIT)
			->where('status', self::APPROVED)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId)
			->where('created_at >=', $from_datetime)
			->where('created_at <=', $to_datetime)
			->where('amount >=', $min_amount);

		$this->addWhereApproved();

		return $this->runOneRowOneField('cnt');
	}

	public function countWithdrawAmount($playerId, $from_datetime, $to_datetime, $min_amount) {
		$this->db->select('count(id) as cnt', false)->from($this->tableName)
			->where('transaction_type', self::WITHDRAWAL)
			->where('status', self::APPROVED)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId)
			->where('created_at >=', $from_datetime)
			->where('created_at <=', $to_datetime)
			->where('amount >=', $min_amount);

		$this->addWhereApproved();
		return $this->runOneRowOneField('cnt');
	}

	/**
	 * detail: make up transfer transaction
	 *
	 * @param int $playerId trans to_id
	 * @param int $transaction_type
	 * @param double $amount trans field
	 * @param string $note trans field
	 * @param string $dateTime trans field
	 * @param string $operatorName trans field
	 *
	 * @return array
	 */
	public function livechatTransferTransaction($playerId, $transaction_type, $amount, $note, $dateTime = null, $operatorName) {

		if (empty($dateTime)) {
			$dateTime = new DateTime();
		}
		if (is_string($dateTime)) {
			$dateTime = new DateTime($dateTime);
		}

		$player_username = $this->player_model->getUsernameById($playerId);

		$beforeBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);

		//minus to main wallet
		$success=$this->wallet_model->decMainWallet($playerId, $amount);

		if(!$success){

			return false;
		}

		$afterBalanceDetails = $this->wallet_model->getBalanceDetails($playerId);

		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $playerId,
			'from_type' => Transactions::PLAYER,
			'from_username' => $player_username,
			'to_id' => 0,
			'to_type' => Transactions::LIVECHAT_ADMIN,
			'to_username' => $operatorName,
			'external_transaction_id' => NULL,
			'sub_wallet_id' => NULL,
			'note' => $note,
			'before_balance' => $beforeBalanceDetails['main_wallet'],
			'after_balance' => $afterBalanceDetails['main_wallet'],
			'status' => Transactions::APPROVED,
			'flag' => Transactions::MANUAL,
			'created_at' => $this->utils->formatDateTimeForMysql($dateTime),
			'updated_at' => $this->utils->getNowForMysql(),
			'total_before_balance' => $afterBalanceDetails['total_balance'],
			'trans_date' => $this->utils->getTodayForMysql(),
			'trans_year_month' => $this->utils->getThisYearMonthForMysql(),
			'trans_year' => $this->utils->getThisYearForMysql(),
			'changed_balance' => $this->utils->encodeJson(array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount)),
		);

		$rlt = $this->insertRow($transactionDetails);

		return $rlt;
	}

	public function getDepositWithoutApplyingPromoList($playerId, $fromDatetime, $toDatetime){

		$this->db->from($this->tableName)
			->where('transaction_type', self::DEPOSIT)
			->where('to_type', self::PLAYER)
			->where('to_id', $playerId)
			->where('player_promo_id is null', null, false);

		if (!empty($fromDatetime) && !empty($toDatetime)) {
			$this->db->where('created_at >=', $fromDatetime)
				->where('created_at <=', $toDatetime);
		}

		$this->addWhereApproved();

		$this->db->order_by('created_at', 'desc');

		$rows = $this->runMultipleRowArray();

		return $rows;
	}


    // ===== Agency Transactions =====
	/**
	 * detail: deposit to agent
	 *
	 * @param int $agent_id trans to_id field
	 * @param double $amount trans amount
	 * @param string $extraNotes trans note
	 * @param int $adminUserId current logged user
	 * @param int $flag
	 * @param string $dateTime
	 *
	 * @return int
	 */
	public function depositToAgent($agent_id, $amount, $extraNotes, $adminUserId = 1, $flag = self::MANUAL, $dateTime = null) {
		$this->load->model(array('agency_model'));
		$transaction_type = self::DEPOSIT_TO_AGENT;
		if (empty($dateTime)) {
			$dateTime = new DateTime();
		}
		if (is_string($dateTime)) {
			$dateTime = new DateTime($dateTime);
		}

		$note = 'deposit ' . $amount . ' to agent ' . $agent_id . ', reason: ' . $extraNotes;

		$beforeBalanceDetails = $this->agency_model->get_agent_balance($agent_id);
		$success=$this->agency_model->incMainWallet($agent_id, $amount);
		if(!$success){
			$this->utils->error_log('agent incMainWallet failed', $agent_id, $amount);
			return $success;
		}
		$afterBalanceDetails = $this->agency_model->get_agent_balance($agent_id);

		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);

		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $adminUserId, // $this->authentication->getUserId(),
			'from_type' => self::ADMIN, //admin
			'to_id' => $agent_id,
			'to_type' => self::AGENT, //player
			'note' => $note,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'status' => self::APPROVED, //approved
			'flag' => $flag, //manual
			'created_at' => $this->utils->formatDateTimeForMysql($dateTime),
			'updated_at' => $this->utils->getNowForMysql(),
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);

		$rtn_id = $this->insertRow($transactionDetails);

		return $rtn_id;
	}

	public function manualAddBalanceAgent($agent_id, $amount, $extraNotes, $adminUserId = 1, $flag = self::MANUAL, $dateTime = null) {
		$this->load->model(array('agency_model'));
		if (empty($dateTime)) {
			$dateTime = new DateTime();
		}
		if (is_string($dateTime)) {
			$dateTime = new DateTime($dateTime);
		}
		$note = 'manual add ' . $amount . ' to agent ' . $agent_id . ' main wallet: ' . $extraNotes;
		$beforeBalanceDetails = $this->agency_model->get_agent_balance($agent_id);
		$success=$this->agency_model->incMainWallet($agent_id, $amount);
		if(!$success){
			$this->utils->error_log('agent incMainWallet failed', $agent_id, $amount);
			return $success;
		}
		$afterBalanceDetails = $this->agency_model->get_agent_balance($agent_id);
		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);
		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => self::ADMIN_ADD_BALANCE_TO_AGENT,
			'from_id' => $adminUserId,
			'from_type' => self::ADMIN,
			'to_id' => $agent_id,
			'to_type' => self::AGENT,
			'note' => $note,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'status' => self::APPROVED,
			'flag' => $flag,
			'created_at' => $this->utils->formatDateTimeForMysql($dateTime),
			'updated_at' => $this->utils->getNowForMysql(),
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);
		$rtn_id = $this->insertRow($transactionDetails);
		return $rtn_id;
	}

	public function manualSubtractBalanceAgent($agent_id, $amount, $extraNotes, $adminUserId = 1, $flag = self::MANUAL, $dateTime = null) {
		$this->load->model(array('agency_model'));
		if (empty($dateTime)) {
			$dateTime = new DateTime();
		}
		if (is_string($dateTime)) {
			$dateTime = new DateTime($dateTime);
		}
		$note = 'manual subtract ' . $amount . ' from agent ' . $agent_id . ' main wallet: ' . $extraNotes;
		$beforeBalanceDetails = $this->agency_model->get_agent_balance($agent_id);
		$success=$this->agency_model->decMainWallet($agent_id, $amount);
		if(!$success){
			$this->utils->error_log('agent decMainWallet failed', $agent_id, $amount);
			return $success;
		}
		$afterBalanceDetails = $this->agency_model->get_agent_balance($agent_id);
		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);
		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => self::ADMIN_SUBTRACT_BALANCE_FROM_AGENT,
			'from_id' => $adminUserId,
			'from_type' => self::ADMIN,
			'to_id' => $agent_id,
			'to_type' => self::AGENT,
			'note' => $note,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'status' => self::APPROVED,
			'flag' => $flag,
			'created_at' => $this->utils->formatDateTimeForMysql($dateTime),
			'updated_at' => $this->utils->getNowForMysql(),
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);
		$rtn_id = $this->insertRow($transactionDetails);
		return $rtn_id;
	}

	/**
	 * detail: withdraw from agent
	 *
	 * @param int $agent_id trans to_id field
	 * @param double $amount trans amount
	 * @param string $extraNotes trans note
	 * @param int $adminUserId current logged user
	 * @param int $flag
	 * @param string $dateTime
	 * @param string $walletType
	 *
	 * @return int
	 */
	public function withdrawFromAgent($agent_id, $amount, $extraNotes, $adminUserId = 1, $flag = self::MANUAL, $dateTime = null, $walletType = 'main') {
		$this->load->model(array('agency_model'));
		$transaction_type = self::WITHDRAW_FROM_AGENT;
		if (empty($dateTime)) {
			$dateTime = new DateTime();
		}
		if (is_string($dateTime)) {
			$dateTime = new DateTime($dateTime);
		}

		$note = 'withdraw ' . $amount . ' from agent ' . $agent_id . ' ' . $walletType . ' wallet , reason: ' . $extraNotes;

		$beforeBalanceDetails = $this->agency_model->get_agent_balance($agent_id);
		if ($walletType == 'main') {
			$success=$this->agency_model->decMainWallet($agent_id, $amount);
			if(!$success){
				$this->utils->error_log('agent decMainWallet failed', $agent_id, $amount);
				return $success;
			}
		} else {
			$success=$this->agency_model->decBalanceWallet($agent_id, $amount);
			if(!$success){
				$this->utils->error_log('agent decBalanceWallet failed', $agent_id, $amount);
				return $success;
			}
		}

		$afterBalanceDetails = $this->agency_model->get_agent_balance($agent_id);

		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);

		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $adminUserId, // $this->authentication->getUserId(),
			'from_type' => self::ADMIN, //admin
			'to_id' => $agent_id,
			'to_type' => self::AGENT, //player
			'note' => $note,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'status' => self::APPROVED, //approved
			'flag' => $flag, //manual
			'created_at' => $this->utils->formatDateTimeForMysql($dateTime),
			'updated_at' => $this->utils->getNowForMysql(),
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);

		$rtn_id = $this->insertRow($transactionDetails);

		return $rtn_id;
	}

	/**
	 * detail: withdraw from agent frozen
	 *
	 * @param int $agent_id trans to_id field
	 * @param double $amount trans amount
	 * @param string $extraNotes trans note
	 * @param int $adminUserId current logged user
	 * @param int $flag
	 * @param string $dateTime
	 *
	 * @return int
	 */
	public function withdrawFromAgentFrozen($agent_id, $amount, $extraNotes, $adminUserId = 1, $flag = self::MANUAL, $dateTime = null) {
		$this->load->model(array('agency_model'));
		$transaction_type = self::WITHDRAW_FROM_AGENT;
		if (empty($dateTime)) {
			$dateTime = new DateTime();
		}
		if (is_string($dateTime)) {
			$dateTime = new DateTime($dateTime);
		}

		$note = 'withdraw ' . $amount . ' from agent ' . $agent_id . ' ' . $extraNotes;

		$beforeBalanceDetails = $this->agency_model->get_agent_balance($agent_id);

		//clear frozen
		$this->db->set('frozen', 'frozen-' . $amount, false)
			->where('agent_id', $agent_id);
		$this->runAnyUpdate('agency_agents');

		$afterBalanceDetails = $this->agency_model->get_agent_balance($agent_id);

		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);

		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $adminUserId, // $this->authentication->getUserId(),
			'from_type' => self::ADMIN, //admin
			'to_id' => $agent_id,
			'to_type' => self::AGENT, //player
			'note' => $note,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'status' => self::APPROVED, //approved
			'flag' => $flag, //manual
			'created_at' => $this->utils->formatDateTimeForMysql($dateTime),
			'updated_at' => $this->utils->getNowForMysql(),
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);

		$rtn_id = $this->insertRow($transactionDetails);

		return $rtn_id;
	}

	/**
	 * detail: transfer balance from main to agentilaite
	 *
	 * @param int $agent_id trans to_id field
	 * @param double $amount trans amount
	 * @param string $extraNotes trans note
	 * @param int $opUserId current logged user
	 * @param string type of operator which can be amdin or agent
	 * @param int $flag
	 *
	 * @return int
	 */
	public function agentTransferToBalanceFromMain($agent_id, $extraNotes, $opUserId = 1, $opType = 'admin', $flag = self::MANUAL, $amount = null) {
		$this->load->model(array('agency_model'));
		$transaction_type = self::TRANSFER_TO_BALANCE_FROM_MAIN_AGENT;

		$note = 'transfer ' . $amount . ' to balance from main on agent ' . $agent_id . ' ' . $extraNotes;

		$beforeBalanceDetails = $this->agency_model->get_agent_balance($agent_id);

		//really transfer
		if ($amount === null) {
			$amount = $beforeBalanceDetails['main_wallet'];
		}
		$this->db->set('wallet_hold', 'wallet_hold+' . $amount, false);
		$this->db->set('wallet_balance', 'wallet_balance-' . $amount, false);
		$this->db->where('agent_id', $agent_id);
		$rlt = $this->runAnyUpdate('agency_agents');

		$afterBalanceDetails = $this->agency_model->get_agent_balance($agent_id);

		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);

        if ($opType == 'admin') {
            $fromType = self::ADMIN;
        } else {
            $fromType = self::AGENT;
        }
		//record
		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $opUserId, // $this->authentication->getUserId(),
			'from_type' => $fromType,
			'to_id' => $agent_id,
			'to_type' => self::AGENT, //player
			'note' => $note,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'status' => self::APPROVED, //approved
			'flag' => $flag, //manual
			'created_at' => $this->utils->getNowForMysql(),
			'updated_at' => $this->utils->getNowForMysql(),
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);

		$rtn_id = false;
		if($amount > 0){
			$rtn_id = $this->insertRow($transactionDetails);
		}
		return $rtn_id;
	}

	/**
	 * detail: transfer agent balance to main
	 *
	 * @param int $agent_id trans to_id
	 * @param string $extraNotes trans note
	 * @param int $opUserId current logged user
	 * @param string type of operator which can be amdin or agent
	 * @param int $flag trans flag
	 * @param double $amount trans amount
	 *
	 * @return int
	 */
	public function agentTransferFromBalanceToMain($agent_id, $extraNotes, $opUserId = 1, $opType = 'admin', $flag = self::MANUAL, $amount = null) {
		$this->load->model(array('agency_model'));
		$transaction_type = self::TRANSFER_TO_MAIN_FROM_BALANCE_AGENT;

		$note = 'transfer ' . $amount . ' to main from balance on agent ' . $agent_id . ' ' . $extraNotes;

		$beforeBalanceDetails = $this->agency_model->get_agent_balance($agent_id);
		if ($amount === null) {
			$amount = $beforeBalanceDetails['hold'];
			// $this->db->set('wallet_balance', 'wallet_hold', false);
			// $this->runAnyUpdate('agents');
			// $this->db->set('wallet_hold', 0);
			// $rlt = $this->runAnyUpdate('agents');
		}
		$this->db->set('wallet_hold', 'wallet_hold-' . $amount, false);
		$this->db->set('wallet_balance', 'wallet_balance+' . $amount, false);
		$this->db->where('agent_id', $agent_id);
		$rlt = $this->runAnyUpdate('agency_agents');

		$afterBalanceDetails = $this->agency_model->get_agent_balance($agent_id);

		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);

        if ($opType == 'admin') {
            $fromType = self::ADMIN;
        } else {
            $fromType = self::AGENT;
        }
		//record
		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => $transaction_type,
			'from_id' => $opUserId, // $this->authentication->getUserId(),
			'from_type' => $fromType,
			'to_id' => $agent_id,
			'to_type' => self::AGENT, //player
			'note' => $note,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'status' => self::APPROVED, //approved
			'flag' => $flag, //manual
			'created_at' => $this->utils->getNowForMysql(),
			'updated_at' => $this->utils->getNowForMysql(),
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);

		$rtn_id = $this->insertRow($transactionDetails);

		return $rtn_id;
	}

	/**
	 * detail: create agent to player transaction
	 *
	 * @param  int $agentId
	 * @param  int $playerId
	 * @param  double $amount
	 * @param  string $extraNotes
	 * @param  string $datetime
	 * @param  int $flag
	 *
	 * @return int
	 */
	public function agentTransferBalanceToBindingPlayer($agentId, $playerId, $amount, $wallet_type = 'main', $extraNotes = null, $datetime = null, $flag = self::MANUAL) {
        if (empty($agentId) || empty($playerId)
            || empty($amount) || $amount <= 0
            || ($wallet_type != 'main' && $wallet_type != 'hold')) {
            $this->utils->error_log('failed agentTransferBalanceToBindingPlayer ', $agentId, $playerId, $amount, $wallet_type, $extraNotes);
			return false;
		}

		$this->load->model(array('wallet_model', 'player_model', 'agency_model'));

		if (empty($datetime)) {
			$datetime = new DateTime();
		}
		if (is_string($datetime)) {
			$datetime = new DateTime($datetime);
		}
		$trans_date = $this->utils->formatDateForMysql($datetime);
		$trans_year_month = $this->utils->formatYearMonthForMysql($datetime);
		$trans_year = $this->utils->formatYearForMysql($datetime);
		$beforeBalanceDetails = $this->agency_model->get_agent_balance($agentId);
		$agent_info = $this->agency_model->get_agent_by_id($agentId);

        if ($wallet_type == 'main') {
            $wallet_name = 'wallet_balance';
        } else {
            $wallet_name = 'wallet_hold';
        }
        if($this->utils->compareResultCurrency($agent_info[$wallet_name], '<', $amount)){
			$this->utils->error_log('no enough balance, agent id:'.$agentId, $agent_info[$wallet_name], $amount);
			return false;
		}
		$trans_type = self::FROM_AGENT_TO_PLAYER;

        if($wallet_type == 'main') {
            $success=$this->agency_model->decMainWallet($agentId, $amount);
			if(!$success){
				$this->utils->error_log('agent decMainWallet failed', $agent_id, $amount);
				return $success;
			}
        } else {
            $success=$this->agency_model->decBalanceWallet($agentId, $amount);
			if(!$success){
				$this->utils->error_log('agent decBalanceWallet failed', $agent_id, $amount);
				return $success;
			}
        }

		$afterBalanceDetails = $this->agency_model->get_agent_balance($agentId);
		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);

		$from_username = $agent_info['agent_name'];
		$to_username = $this->player_model->getUsernameById($playerId);

		$note = 'transfer amount ' . $amount . ' from agent ' . $agentId . '(' . $from_username . ') to main wallet of the binding player ' . $playerId . '(' . $to_username . ') ' . $extraNotes;
		$transaction = array(
			'amount' => $amount,
			'transaction_type' => $trans_type,
			'from_id' => $agentId,
			'from_type' => Transactions::AGENT,
			'from_username' => $from_username,
			'to_id' => $playerId,
			'to_type' => Transactions::PLAYER,
			'to_username' => $to_username,
			'note' => $note,
			// 'before_balance' => $beforeBalanceDetails['main_wallet'],
			// 'after_balance' => $afterBalanceDetails['main_wallet'],
			// 'sub_wallet_id' => $subWalletId,
			'status' => Transactions::APPROVED,
			'flag' => $flag,
			'created_at' => $this->utils->formatDateTimeForMysql($datetime),
			'updated_at' => $this->utils->getNowForMysql(),
			// 'promo_category' => null,
			// 'balance_history_id' => $beforeHistoryId,
			// 'display_name' => $show_in_front_end,
			'trans_date' => $trans_date,
			'trans_year_month' => $trans_year_month,
			'trans_year' => $trans_year,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);

		$rtn_id = $this->insertRow($transaction);
		//record from agent first
		$afterHistoryId = $this->recordAgentAfterActionWalletBalanceHistory($trans_type,
			$agentId, $rtn_id, $amount);
		$this->updateBalanceHistoryTransactionId($afterHistoryId, $rtn_id);

		return $rtn_id;
	}

	/**
	 * detail: create agent to player transaction
	 *
	 * @param  int $agentId
	 * @param  int $playerId
	 * @param  double $amount
	 * @param  string $extraNotes
	 * @param  string $datetime
	 * @param  int $flag
	 *
	 * @return int
	 */
	public function agentTransferBalanceFromBindingPlayer($agentId, $playerId, $amount, $wallet_type = 'main', $extraNotes = null, $datetime = null, $flag = self::MANUAL) {
        if (empty($agentId) || empty($playerId)
            || empty($amount) || $amount <= 0
            || ($wallet_type != 'main' && $wallet_type != 'hold')) {
            $this->utils->error_log('failed agentTransferBalanceFromBindingPlayer ', $agentId, $playerId, $amount, $wallet_type, $extraNotes);
			return false;
		}

		$this->load->model(array('wallet_model', 'player_model', 'agency_model'));

		if (empty($datetime)) {
			$datetime = new DateTime();
		}
		if (is_string($datetime)) {
			$datetime = new DateTime($datetime);
		}
		$trans_date = $this->utils->formatDateForMysql($datetime);
		$trans_year_month = $this->utils->formatYearMonthForMysql($datetime);
		$trans_year = $this->utils->formatYearForMysql($datetime);
		$beforeBalanceDetails = $this->agency_model->get_agent_balance($agentId);
		$agent_info = $this->agency_model->get_agent_by_id($agentId);

		$trans_type = self::FROM_PLAYER_TO_AGENT;

        if($wallet_type == 'main') {
            $success=$this->agency_model->incMainWallet($agentId, $amount);
			if(!$success){
				$this->utils->error_log('agent incMainWallet failed', $agentId, $amount);
				return $success;
			}
        } else {
            $success=$this->agency_model->incBalanceWallet($agentId, $amount);
			if(!$success){
				$this->utils->error_log('agent incBalanceWallet failed', $agentId, $amount);
				return $success;
			}
        }

		$afterBalanceDetails = $this->agency_model->get_agent_balance($agentId);
		$changedBal = array('before' => $beforeBalanceDetails, 'after' => $afterBalanceDetails, 'amount' => $amount);

		$to_username = $agent_info['agent_name'];
		$from_username = $this->player_model->getUsernameById($playerId);

		$note = 'transfer amount ' . $amount . ' to agent ' . $agentId . '(' . $to_username . ') from main wallet of the binding player ' . $playerId . '(' . $from_username . ') ' . $extraNotes;

		$transaction = array(
			'amount' => $amount,
			'transaction_type' => $trans_type,
			'from_type' => Transactions::PLAYER,
			'from_id' => $playerId,
			'from_username' => $from_username,
			'to_type' => Transactions::AGENT,
			'to_id' => $agentId,
			'to_username' => $to_username,
			'note' => $note,
			// 'before_balance' => $beforeBalanceDetails['main_wallet'],
			// 'after_balance' => $afterBalanceDetails['main_wallet'],
			// 'sub_wallet_id' => $subWalletId,
			'status' => Transactions::APPROVED,
			'flag' => $flag,
			'created_at' => $this->utils->formatDateTimeForMysql($datetime),
			'updated_at' => $this->utils->getNowForMysql(),
			// 'promo_category' => null,
			// 'balance_history_id' => $beforeHistoryId,
			// 'display_name' => $show_in_front_end,
			'trans_date' => $trans_date,
			'trans_year_month' => $trans_year_month,
			'trans_year' => $trans_year,
			'before_balance' => $beforeBalanceDetails['total_balance'],
			'after_balance' => $afterBalanceDetails['total_balance'],
			'changed_balance' => $this->utils->encodeJson($changedBal),
		);

		$rtn_id = $this->insertRow($transaction);
		//record from agent first
		$afterHistoryId = $this->recordAgentAfterActionWalletBalanceHistory($trans_type,
			$agentId, $rtn_id, $amount);
		$this->updateBalanceHistoryTransactionId($afterHistoryId, $rtn_id);

		return $rtn_id;
	}

	/**
	 * detail: get transaction for a certain agent
	 *
	 * @param int $agent_id
	 * @return array
	 */
    public function getAgentTransactions($agent_id, $limit = 0) {
    	$limitSql='';
        if($limit > 0) {
    		$limitSql='LIMIT '.$limit;
    	}
    	$sql=<<<EOD
SELECT * FROM (transactions)
WHERE ((to_type = ? AND to_id = ? ) OR (from_type = ? AND from_id = ?))
AND transaction_type IN (?, ?, ?, ?, ?, ?, ?, ?)
AND status = ?
ORDER BY created_at DESC
$limitSql
EOD;

		$params=[
			self::AGENT, $agent_id, self::AGENT, $agent_id,
			self::DEPOSIT_TO_AGENT, self::WITHDRAW_FROM_AGENT,
            self::ADMIN_SUBTRACT_BALANCE_FROM_AGENT, self::ADMIN_ADD_BALANCE_TO_AGENT,
            self::TRANSFER_TO_BALANCE_FROM_MAIN_AGENT, self::TRANSFER_TO_MAIN_FROM_BALANCE_AGENT,
            self::AGENT_SETTLEMENT, self::FROM_AGENT_TO_PLAYER, self::APPROVED,
        ];
        return $this->runRawSelectSQLArray($sql, $params);
    }

    public function updateTransactionAmount($transId, $amount){
    	$this->db->set('amount', $amount)->where('id', $transId);
    	return $this->runAnyUpdate('transactions');
    }

    public function existsWithdrawl($playerId){
    	$this->db->from('transactions')->where(
    		[
				'transaction_type' => Transactions::WITHDRAWAL,
				'to_id' => $playerId,
				'to_type' => Transactions::PLAYER,
			]
		);

		return $this->runExistsResult();
	}

    public function existsDeposit($playerId){
    	$this->db->from('transactions')->where(
    		[
				'transaction_type' => Transactions::DEPOSIT,
				'to_id' => $playerId,
				'to_type' => Transactions::PLAYER,
			]
		);

		return $this->runExistsResult();
	}

	public function createSimpleDepositTransaction($playerId, $adminUserId, $amount, $datetime) {
		$this->load->model(array('wallet_model', 'player_model', 'users'));

		$adminUserId = $adminUserId ? $adminUserId : 1;

		$from_username = $this->users->getUsernameById($adminUserId);
		$to_username = $this->player_model->getUsernameById($playerId);

		$transaction_type = self::DEPOSIT;

		$transactionDetails = array('amount' => $amount,
			'transaction_type' => $transaction_type, //deposit
			'from_id' => $adminUserId, // $this->authentication->getUserId(),
			'from_type' => self::ADMIN, //admin
			'from_username' => $from_username,
			'to_id' => $playerId,
			'to_type' => self::PLAYER, //player
			'to_username' => $to_username,
			'note' => $from_username . ' deposit ' . $amount . ' to ' . $to_username,
			'status' => self::APPROVED, //approved
			'flag' => self::MANUAL, //manual
			'order_id' => null,
			'payment_account_id' => null,
			// 'balance_history_id' => $beforeHistoryId,
			'updated_at' => $datetime,
			'created_at' => $datetime,
			'trans_date' => substr($datetime, 0, 10),
			'trans_year_month' => substr($datetime, 0, 4).substr($datetime, 5, 2),
			'trans_year' => substr($datetime, 0, 4),

			'before_balance' => 0,
			'after_balance' => 0,
			'total_before_balance' => 0,
			'changed_balance'=>null,
		);
		$rlt = $this->insertRow($transactionDetails);

		return $rlt;
	}

	public function createSimpleWithdrawTransaction($playerId, $adminUserId, $amount, $datetime) {
		$this->load->model(array('wallet_model', 'users', 'player_model'));

		$from_username = $this->users->getUsernameById($adminUserId);
		$to_username = $this->player_model->getUsernameById($playerId);

		$transaction_type = self::WITHDRAWAL;

		$transactionDetails = array(
			'amount' => $amount,
			'transaction_type' => Transactions::WITHDRAWAL,
			'from_id' => $adminUserId,
			'from_type' => Transactions::ADMIN,
			'from_username' => $from_username,
			'to_id' => $playerId,
			'to_type' => Transactions::PLAYER,
			'to_username' => $to_username,
			'note' => $from_username.' approved withdrawal ' . $amount . ' to playerId:' . $to_username,
			'status' => Transactions::APPROVED,
			'flag' => Transactions::MANUAL,

			'updated_at' => $datetime,
			'created_at' => $datetime,
			'trans_date' => substr($datetime, 0, 10),
			'trans_year_month' => substr($datetime, 0, 4).substr($datetime, 5, 2),
			'trans_year' => substr($datetime, 0, 4),

			'before_balance' => 0,
			'after_balance' => 0,
			'total_before_balance' => 0,
			'changed_balance'=>null,
		);

		$rlt = $this->insertRow($transactionDetails);

		return $rlt;
	}

	public function updateTransactionNoteByExternalTransactionId($external_transaction_id, $note){
		$this->db->set('note', $note)->where('external_transaction_id', $external_transaction_id);
    	return $this->runAnyUpdate('transactions');
	}

    public function AdvancedPromoVerifyList($playerId, $other_contidtions) {
        if(isset($other_contidtions['exclude_first_deposit'])){
            $row = $this->getFirstDeposit($playerId);
            if(!empty($row)){
                $other_contidtions['start_transaction_id'] = $row->id;
            }
        }

        $sql = "SELECT * FROM transactions WHERE (from_id = ? OR to_id = ?)";
        $params = [$playerId, $playerId];

        if(isset($other_contidtions['start_transaction_id'])){
            $sql .= ' AND id > ?';
            $params[] = $other_contidtions['start_transaction_id'];
        }

        if(isset($other_contidtions['from_date'])){
            $sql .= 'AND created_at >= ?';
            $params[] = $other_contidtions['from_date'];
        }

        if(isset($other_contidtions['to_date'])){
            $sql .= ' AND created_at <= ?';
            $params[] = $other_contidtions['to_date'];
        }

        if(isset($other_contidtions['status'])){
            $sql .= ' AND status = ?';
            $params[] = $other_contidtions['status'];
        }else{
            $sql .= ' AND status = ?';
            $params[] = self::APPROVED;
        }


        if(isset($other_contidtions['order_by'])){
            $params[] = $other_contidtions['status'];
            $order_by_column = (isset($other_contidtions['order_by']['column'])) ? $other_contidtions['order_by']['column'] : 'id';
            $order_by_direct = (isset($other_contidtions['order_by']['dir'])) ? $other_contidtions['order_by']['dir'] : 'ASC';
            $sql .= 'ORDER BY ' . $order_by_column . ' ' . $order_by_direct;
        }else{
            $this->db->order_by('id', 'ASC');
        }

        $query = $this->db->query($sql, $params);
        $results = [
            'records' => [],
            'flags' => [

            ]
        ];

        if(empty($query)){
            return $results;
        }

        $rows = $query->result_object();

        foreach($rows as $row){
            if(isset($other_contidtions['fetch_type'])){
                if($row->transaction_type == $other_contidtions['fetch_type']){
                    $results['records'][] = $row;
                }
            }else{
                if($row->transaction_type == self::DEPOSIT){
                    if(isset($other_contidtions['min_deposit_amount']) && $row->amount < $other_contidtions['min_deposit_amount']){
                        continue;
                    }
                    $results['records'][] = $row;
                }
            }
        }

        return $results;
    }

	public function count_deposit_by_day($playerId, $periodFrom, $periodTo, $other_contidtion = NULL){
        $other_contidtion = (empty($other_contidtion)) ? [] : $other_contidtion;
        $other_contidtion['from_date'] = $periodFrom;
        $other_contidtion['to_date'] = $periodTo;
	    $deposit_list = $this->AdvancedPromoVerifyList($playerId, $other_contidtion);
        $deposit_list = (empty($deposit_list)) ? [] : $deposit_list;

        $results = [];

        $result_template = [
            'records' => [],
            'times' => 0,
            'total_amount' => 0
        ];

        $begin = new DateTime($periodFrom);
        $end = new DateTime($periodTo);

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        foreach ($period as $dt) {
            if(!isset($results[$dt->format("Y-m-d")])){
                $results[$dt->format("Y-m-d")] = json_decode(json_encode($result_template), TRUE);
            }
        }

        if(empty($deposit_list['records'])){
            return $results;
        }

        foreach($deposit_list['records'] as $deposit_entry){
            $created_at_date = date('Y-m-d', strtotime($deposit_entry->created_at));
            $result = $results[$created_at_date];

            $result['records'][] = (array)$deposit_entry;
            $result['times'] = count($result['records']);
            $result['total_amount'] += $deposit_entry->amount;

            $results[$created_at_date] = $result;
        }

        return $results;
    }

    public function getTransactionsByTransactionTypesAndDay($transactionTypes,$fields,$day){
    	$selected_fields = implode(',', $fields);
    	$this->db->select($selected_fields);
    	$this->db->where_in('transaction_type', $transactionTypes);
    	$this->db->where('trans_date', $day);
    	$query = $this->db->get($this->tableName);
    	return $query->result_array();
    }

    public function getGamelogsTransactions($game_platform_id,$from,$to){
    	$main_to_sub = Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET;
    	$sub_to_main = Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET;
    	$game_logs_main_to_sub = Game_logs::TRANS_TYPE_MAIN_WALLET_TO_SUB_WALLET;
    	$game_logs_sub_to_main = Game_logs::TRANS_TYPE_SUB_WALLET_TO_MAIN_WALLET;
    	$sql = <<<EOD
    	SELECT sub_wallet_id as game_platform_id, to_id as player_id, created_at as start_at, created_at as end_at, '1' as has_both_side, response_result_id, after_balance, amount as trans_amount, if(transaction_type = {$main_to_sub},  {$game_logs_main_to_sub}, {$game_logs_sub_to_main} ) as trans_type, request_secure_id from transactions where status = 1  AND created_at >= '{$from}' AND created_at <= '{$to}' AND sub_wallet_id = {$game_platform_id} AND transaction_type  IN ({$main_to_sub}, {$sub_to_main})
EOD;
    	return $this->runRawSelectSQL($sql);
    }

    public function getManualSubtractBalanceTagsBy($transId){
    	$this->db->select("manual_subtract_balance_tag.adjust_tag_name")->from('manual_subtract_balance_tag')
    		->join('transactions_tag', 'transactions_tag.msbt_id = manual_subtract_balance_tag.id')
    		->where('transactions_tag.rtn_id', $transId);
    	return $this->runMultipleRowArray();
    }

	/**
	 * overview :  get total cashback day default is +1 day
	 *
	 * @param int	   $total_cashback_date_type
	 * @param datetime $date
	 * @return bool
	 *
	 * Created by Frans Eric Dela Cruz (frans.php.ph) 2018-10-31
	 */
	public function getTotalCashBackDate($total_cashback_date_type, $date) {

		switch ($total_cashback_date_type) {

			case self::TOTAL_CASHBACK_SAME_DAY:
				$date = date('Y-m-d H:i:s', strtotime($date));
				break;
			case self::TOTAL_CASHBACK_PLUS_1_DAY:
				$date = date('Y-m-d H:i:s', strtotime($date . ' +1 day'));
				break;
			default:
				$lang = date('Y-m-d H:i:s', strtotime($date . ' +1 day'));
				break;
		}

		return $date;
	}

	public function getTransactionBySaleOrderId($saleOrderId) {
		if (!empty($saleOrderId)) {
			$this->db->from('transactions')
				->where('order_id', $saleOrderId)->where('transaction_type', self::DEPOSIT);
			return $this->runOneRow();
		}
		return null;
	}

	public function getTransIdBySaleOrderId($saleOrderId) {
		if (!empty($saleOrderId)) {
			$this->db->select('id')->from('transactions')
				->where('order_id', $saleOrderId)->where('transaction_type', self::DEPOSIT);
			return $this->runOneRowOneField('id');
		}
		return null;
	}

	public function getPaymentAccountIdBySaleOrderId($saleOrderId) {
		$transaction = $this->getTransactionBySaleOrderId($saleOrderId);
		if(!is_null($transaction)) {
			return $transaction->payment_account_id;
		}
		return null;
	}

	public function getTransactionInfoById($transId) {
		if (!empty($transId)) {
			$this->db->from('transactions')->where('id', $transId);
			return $this->runOneRowArray();
		}
		return null;
	}

	public function checkAndAddUploadCSVHistory($admin_user_id, $csv_filename, $csv_fullpath, $application_type, &$exists){
		//upload_csv_file_history
		$success=true;
		//search csv file first
		$this->db->from('upload_csv_file_history')->where('csv_filename', $csv_filename)->where('application_type', $application_type);
		$exists=$this->runExistsResult();

		if(!$exists){
			//insert
			$data=[
				'csv_filename'=>$csv_filename,
				'csv_fullpath'=>$csv_fullpath,
				'application_type'=>$application_type,
				'uploaded_by'=>$admin_user_id,
				'created_at'=>$this->utils->getNowForMysql(),
				'updated_at'=>$this->utils->getNowForMysql(),
			];
			$success=$this->insertData('upload_csv_file_history', $data);
		}

		return $success;
	}

	public function addPlayerBalAdjustmentHistory($data){
		$this->db->insert('balanceadjustmenthistory', $data);

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}
}

/* End of file Transactions.php */
/* Location: ./application/models/transactions.php */
