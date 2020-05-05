<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Transactions_library
 *
 * Transactions library
 *
 * @package     Transactions_library
 * @author      kaiser.dapar
 * @version     1.0.0
 * @see         http://ogdev.ddns.net:8090/display/OG/Transactions
 */
class Transactions_library {

	/*
		 * 1    deposit
		 * 2    withdrawal
		 * 3    fee_for_player
		 * 4    fee_for_operator
		 * 5    transfer_to_sub_wallet_from_main_wallet
		 * 6    transfer_from_sub_wallet_to_main_wallet
		 * 7    manual_add_balance
		 * 8    manual_subtract_balance
		 * 9    add_bonus
		 * 10   subtract_bonus,
		 * 11   manual_add_balance_on_sub_wallet
		 * 12   manual_subtract_balance_on_sub_wallet
		 * 13   auto_add_cashback_to_balance
		 * 14   member_group_deposit_bonus
		 * 15   player_refer_bonus
	*/
	private $allowed_transaction_type = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17];

	/*
		 * 1    admin
		 * 2    player
		 * 3    affiliate
	*/
	private $allowed_from_type = [1, 2, 3];

	/*
		 * 1    admin
		 * 2    player
		 * 3    affiliate
	*/
	private $allowed_to_type = [1, 2, 3];

	/*
		 * 1    approved
		 * 2    declined
	*/
	private $allowed_status = [1, 2];

	/*
		 * 1    manual
		 * 2    program
	*/
	private $allowed_flag = [1, 2];

	public function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->model(['transactions', 'users', 'player', 'affiliate']);
	}

	/**
	 *
	 * Create Transaction
	 *
	 * @param float $amount,
	 * @param integer $transaction_type,
	 * @param integer $from_id,
	 * @param integer $from_type,
	 * @param integer $to_id,
	 * @param integer $to_type,
	 * @param string $external_transaction_id
	 * @param integer $response_result_id
	 * @param string $note
	 * @param float $before_balance
	 * @param float $after_balance
	 * @param integer $sub_wallet_id
	 * @param integer $status
	 * @param integer $flag
	 * @param string $created_at
	 *
	 * @return Transaction Array | false
	 *
	 */
	// public function saveTransaction($data) {

	// 	# Extract Data
	// 	$amount = isset($data['amount']) ? $data['amount'] : null;
	// 	$transaction_type = isset($data['transaction_type']) ? $data['transaction_type'] : null;
	// 	$from_id = isset($data['from_id']) ? $data['from_id'] : null;
	// 	$from_type = isset($data['from_type']) ? $data['from_type'] : null;
	// 	$to_id = isset($data['to_id']) ? $data['to_id'] : null;
	// 	$to_type = isset($data['to_type']) ? $data['to_type'] : null;
	// 	$external_transaction_id = isset($data['external_transaction_id']) ? $data['external_transaction_id'] : null;
	// 	$response_result_id = isset($data['response_result_id']) ? $data['response_result_id'] : null;
	// 	$note = isset($data['note']) ? $data['note'] : null;
	// 	$before_balance = isset($data['before_balance']) ? $data['before_balance'] : 0;
	// 	$after_balance = isset($data['after_balance']) ? $data['after_balance'] : 0;
	// 	$sub_wallet_id = isset($data['sub_wallet_id']) ? $data['sub_wallet_id'] : null;
	// 	$status = isset($data['status']) ? $data['status'] : 1;
	// 	$flag = isset($data['flag']) ? $data['flag'] : 1;
	// 	$promo_category = isset($data['promo_category']) ? $data['promo_category'] : null;
	// 	$created_at = isset($data['created_at']) ? $data['created_at'] : $this->ci->utils->getNowForMysql();
	// 	$total_before_balance = isset($data['total_before_balance']) ? $data['total_before_balance'] : null;
	// 	$display_name = isset($data['display_name']) ? $data['display_name'] : null;

	// 	# Validation
	// 	if (!isset($amount, $transaction_type, $from_id, $from_type, $to_id, $to_type, $status, $flag, $created_at)) {
	// 		log_message('error', 'Incomplete Parameters');
	// 		throw new Exception('Incomplete Parameters', 1);
	// 	} else if (!in_array($transaction_type, $this->allowed_transaction_type)) {
	// 		log_message('error', 'Transaction Type does not exist');
	// 		throw new Exception('Transaction Type does not exist', 1);
	// 	} elseif (!in_array($from_type, $this->allowed_from_type)) {
	// 		log_message('error', 'From Type does not exist');
	// 		throw new Exception('From Type does not exist', 1);
	// 	} elseif (!in_array($to_type, $this->allowed_to_type)) {
	// 		log_message('error', 'To Type does not exist');
	// 		throw new Exception('To Type does not exist', 1);
	// 	} elseif (!in_array($status, $this->allowed_status)) {
	// 		log_message('error', 'Status does not exist');
	// 		throw new Exception('Status does not exist', 1);
	// 	} elseif (!in_array($flag, $this->allowed_flag)) {
	// 		log_message('error', 'Flag does not exist');
	// 		throw new Exception('Flag does not exist', 1);
	// 	}

	// 	$transaction = [
	// 		'amount' => $amount,
	// 		'transaction_type' => $transaction_type,
	// 		'from_id' => $from_id,
	// 		'from_type' => $from_type,
	// 		'to_id' => $to_id,
	// 		'to_type' => $to_type,
	// 		'external_transaction_id' => $external_transaction_id,
	// 		'response_result_id' => $response_result_id,
	// 		'note' => $note,
	// 		'before_balance' => $before_balance,
	// 		'after_balance' => $after_balance,
	// 		'sub_wallet_id' => $sub_wallet_id,
	// 		'status' => $status,
	// 		'flag' => $flag,
	// 		'created_at' => $created_at,
	// 		'promo_category' => $promo_category,
	// 		'total_before_balance' => $total_before_balance,
	// 		'display_name' => $display_name,
	// 	];

	// 	$id = $this->ci->transactions->saveTransaction($transaction);
	// 	if (!$id) {
	// 		return false;
	// 	}

	// 	$transaction['id'] = $id;

	// 	return $transaction;
	// }

	/**
	 *
	 * Create Transaction
	 *
	 * @param float $amount,
	 * @param integer $transaction_type,
	 * @param integer $from_id,
	 * @param integer $from_type,
	 * @param integer $to_id,
	 * @param integer $to_type,
	 * @param string $external_transaction_id
	 * @param integer $response_result_id
	 * @param string $note
	 * @param float $before_balance
	 * @param float $after_balance
	 * @param integer $sub_wallet_id
	 * @param integer $status
	 * @param integer $flag
	 * @param string $created_at
	 *
	 * @return Transaction Array | false
	 *
	 * public function saveTransaction($data) {
	 *     try {
	 *
	 *         # Data Extraction
	 *         $amount                  = isset($data['amount']) ? $data['amount'] : null;
	 *         $transaction_type        = isset($data['transaction_type']) ? $data['transaction_type'] : null;
	 *         $from_id                 = isset($data['from_id']) ? $data['from_id'] : null;
	 *         $to_id                   = isset($data['to_id']) ? $data['to_id'] : null;
	 *         $external_transaction_id = isset($data['external_transaction_id']) ? $data['external_transaction_id'] : null;
	 *         $response_result_id      = isset($data['response_result_id']) ? $data['response_result_id'] : null;
	 *         $note                    = isset($data['note']) ? $data['note'] : null;
	 *         $before_balance          = isset($data['before_balance']) ? $data['before_balance'] : null;
	 *         $after_balance           = isset($data['after_balance']) ? $data['after_balance'] : null;
	 *         $sub_wallet_id           = isset($data['sub_wallet_id']) ? $data['sub_wallet_id'] : null;
	 *         $status                  = isset($data['status']) ? $data['status'] : 1;
	 *         $flag                    = isset($data['flag']) ? $data['flag'] : 1;
	 *         $created_at              = isset($data['created_at']) ? $data['created_at'] : $this->ci->utils->getNowForMysql();
	 *
	 *         # Data Validation
	 *         if ( ! isset($amount,$transaction_type,$from_id,$from_type,$to_id,$to_type,$status,$flag,$created_at)) {
	 *             throw new Exception('Incomplete Parameters', 1);
	 *         } elseif ( ! in_array($status, $this->allowed_status)) {
	 *             throw new Exception('Status does not exist', 1);
	 *         } elseif ( ! in_array($flag, $this->allowed_flag)) {
	 *             throw new Exception('Flag does not exist', 1);
	 *         }
	 *
	 *         $transaction = [
	 *             'amount'                  => $amount,
	 *             'transaction_type'        => $transaction_type,
	 *             'from_id'                 => $from_id,
	 *             'to_id'                   => $to_id,
	 *             'external_transaction_id' => $external_transaction_id,
	 *             'response_result_id'      => $response_result_id,
	 *             'note'                    => $note,
	 *             'before_balance'          => $before_balance,
	 *             'after_balance'           => $after_balance,
	 *             'sub_wallet_id'           => $sub_wallet_id,
	 *             'status'                  => $status,
	 *             'flag'                    => $flag,
	 *             'created_at'              => $created_at,
	 *         ];
	 *         # TODO(KAISER):
	 *         switch (variable) {
	 *             case 1: #deposit
	 *                 $transaction['from_type'] => $from_type,
	 *                 $transaction['to_type']   => $to_type,
	 *                 break;
	 *             case 2: #withdrawal
	 *                 $transaction['from_type'] => $from_type,
	 *                 $transaction['to_type']   => $to_type,
	 *                 break;
	 *             case 3: #fee_for_player
	 *                 $transaction['from_type'] => $from_type,
	 *                 $transaction['to_type']   => $to_type,
	 *                 break;
	 *             case 4: #fee_for_operator
	 *                 $transaction['from_type'] => $from_type,
	 *                 $transaction['to_type']   => $to_type,
	 *                 break;
	 *             case 5: #transfer_to_sub_wallet_from_main_wallet
	 *                 $transaction['from_type'] => $from_type,
	 *                 $transaction['to_type']   => $to_type,
	 *                 break;
	 *             case 6: #transfer_from_sub_wallet_to_main_wallet
	 *                 $transaction['from_type'] => $from_type,
	 *                 $transaction['to_type']   => $to_type,
	 *                 break;
	 *             case 7: #manual_add_balance
	 *                 $transaction['from_type'] => $from_type,
	 *                 $transaction['to_type']   => $to_type,
	 *                 break;
	 *             case 8: #manual_subtract_balance
	 *                 $transaction['from_type'] => $from_type,
	 *                 $transaction['to_type']   => $to_type,
	 *                 break;
	 *             case 9: #add_bonus
	 *                 $transaction['from_type'] => $from_type,
	 *                 $transaction['to_type']   => $to_type,
	 *                 break;
	 *             case 10: #subtract_bonus
	 *                 $transaction['from_type'] => $from_type,
	 *                 $transaction['to_type']   => $to_type,
	 *                 break;
	 *             case 11: #manual_add_balance_on_sub_wallet
	 *                 $transaction['from_type'] => $from_type,
	 *                 $transaction['to_type']   => $to_type,
	 *                 break;
	 *             case 12: #manual_subtract_balance_on_sub_wallet
	 *                 $transaction['from_type'] => $from_type,
	 *                 $transaction['to_type']   => $to_type,
	 *                 break;
	 *             default: throw new Exception('Transaction Type does not exist', 1);
	 *         }
	 *
	 *         if ( ! ($id = $this->ci->transactions->saveTransaction($transaction)) {
	 *             throw new Exception("Failed to save transaction in transactions table", 1);
	 *         }
	 *
	 *         $transaction['id'] = $id;
	 *         return $transaction;
	 *
	 *     } catch (Exception $e) {
	 *
	 *         log_message('error', 'Transactions_library->saveTransaction(): ' . $e->getMessage());
	 *         return false;
	 *
	 *     }
	 * }
	 */

	public function getTransactionList($criteria_1 = [], $criteria_2 = [], $offset = 0, $limit = 999999, $orderby = 'transactions.id', $direction = 'desc') {
		$list = $this->ci->transactions->getTransactionList($criteria_1, $criteria_2, $offset, $limit, $orderby, $direction);

		foreach ($list as &$list_item) {

			# FROM AND TO
			foreach (['from', 'to'] as $type) {
				switch ($list_item[$type . '_type']) {
					case Transactions::ADMIN:
						$list_item[$type] = $this->ci->users->selectUsersById($list_item[$type . '_id'])['username'];
						break;
					case Transactions::PLAYER:
						$list_item[$type] = $this->ci->player->getPlayerById($list_item[$type . '_id'])['username'];
						break;
					case Transactions::AFFILIATE:
						$list_item[$type] = $this->ci->affiliate->getAffiliateById($list_item[$type . '_id'])['username'];
						break;
					default:
						$list_item[$type] = '';
						break;
				}
			}

			# SUB WALLET
			// if (!empty($list_item['sub_wallet_id'])) {

			// 	$subwalletAccount = $this->ci->player->getSubwalletAccount($list_item['sub_wallet_id']);
			// 	if (!$subwalletAccount) {
			// 		$list_item['sub_wallet'] = @$subwalletAccount['game'];
			// 	}
			// }
			//$list_item['sub_wallet'] = $list_item['sub_wallet_id'] ? $this->ci->player->getSubwalletAccount($list_item['sub_wallet_id'])['game'] : '';

		}

		return $list;
	}

	public function getTransactionCount($criteria) {
		$count = $this->ci->transactions->getTransactionCount($criteria);
		return $count;
	}

	public function getUsername($type, $id) {
		switch ($type) {
			case Transactions::ADMIN:
				$user = $this->ci->users->selectUsersById($id);
				$username = isset($user['username']) ? $user['username'] : lang('lang.norecord');
				break;
			case Transactions::PLAYER:
				$player = $this->ci->player->getPlayerById($id);
				$username = isset($player['username']) ? $player['username'] : lang('lang.norecord');
				break;
			case Transactions::AFFILIATE:
				$affiliate = $this->ci->affiliate->getAffiliateById($id);
				$username = isset($affiliate['username']) ? $affiliate['username'] : lang('lang.norecord');
				break;
			default:
				$username = '';
				break;
		}
		return $username;
	}

}
