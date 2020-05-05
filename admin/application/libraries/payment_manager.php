<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Payment Manager
 *
 * Payment Manager library for FHI OG System
 *
 * @package		Payment Manager
 * @author		ASRII
 * @version		1.0.0
 */

class Payment_manager {
	private $error = array();

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array('transactions_library'));
		$this->ci->load->model(array('payment', 'operatorglobalsettings', 'wallet_model','responsible_gaming'));
	}

	/**
	 * Will get request deposit transaction
	 *
	 * @param 	$sort_by array
	 * @param 	$limit int
	 * @param 	$offset int
	 * @return	$array
	 */
	public function getDepositRequestTransaction($sort_by, $limit, $offset) {
		return $this->ci->payment->getDepositRequestTransaction($sort_by, $limit, $offset);
	}

	/**
	 * Will get request 3rd party deposit transaction
	 *
	 * @param 	$sort_by array
	 * @param 	$limit int
	 * @param 	$offset int
	 * @return	$array
	 */
	public function get3rdPartyDepositRequestTransaction($sort_by, $limit, $offset) {
		return $this->ci->payment->get3rdPartyDepositRequestTransaction($sort_by, $limit, $offset);
	}

	/**
	 * Will get manual request 3rd party deposit transaction
	 *
	 * @param 	$sort_by array
	 * @param 	$limit int
	 * @param 	$offset int
	 * @return	$array
	 */
	public function getManual3rdPartyDepositRequestTransaction($sort_by, $limit, $offset) {
		return $this->ci->payment->getManual3rdPartyDepositRequestTransaction($sort_by, $limit, $offset);
	}

	/**
	 * Will get deposit/withdrawal detail
	 *
	 * @param 	$walletId int
	 * @return	$array
	 */
	public function getTransactionDetails($walletAccountId, $dwStatus) {
		return $this->ci->payment->getTransactionDetails($walletAccountId, $dwStatus);
	}

	/**
	 * Will get request withdrawal transaction
	 *
	 * @param 	$sort_by array
	 * @param 	$limit int
	 * @param 	$offset int
	 * @return	$array
	 */
	public function getWithdrawalRequestTransaction($sort_by, $limit, $offset) {
		return $this->ci->payment->getWithdrawalRequestTransaction($sort_by, $limit, $offset);
	}

	/**
	 * Will get approved/declined deposit transaction
	 *
	 * @param 	$sort_by array
	 * @param 	$limit int
	 * @param 	$offset int
	 * @return	$array
	 */
	public function getDepositApprovedDeclinedTransaction($sort_by, $limit, $offset) {
		return $this->ci->payment->getDepositApprovedDeclinedTransaction($sort_by, $limit, $offset);
	}

	/**
	 * Will get 3rd party approved/declined deposit transaction
	 *
	 * @param 	$sort_by array
	 * @param 	$limit int
	 * @param 	$offset int
	 * @return	$array
	 */
	public function getManual3rdPartyDepositApprovedDeclinedTransaction($sort_by, $limit, $offset) {
		return $this->ci->payment->getManual3rdPartyDepositApprovedDeclinedTransaction($sort_by, $limit, $offset);
	}

	/**
	 * Will get 3rd party approved/declined deposit transaction
	 *
	 * @param 	$sort_by array
	 * @param 	$limit int
	 * @param 	$offset int
	 * @return	$array
	 */
	public function get3rdPartyDepositApprovedDeclinedTransaction($sort_by, $limit, $offset) {
		return $this->ci->payment->get3rdPartyDepositApprovedDeclinedTransaction($sort_by, $limit, $offset);
	}

	/**
	 * Will get approved/declined withdrawal transaction
	 *
	 * @param 	$sort_by array
	 * @param 	$limit int
	 * @param 	$offset int
	 * @return	$array
	 */
	public function getWithdrawalApprovedDeclinedTransaction($sort_by, $limit, $offset) {
		return $this->ci->payment->getWithdrawalApprovedDeclinedTransaction($sort_by, $limit, $offset);
	}

	/**
	 * Will get deposit/withdrawal count
	 *
	 * @param 	$transactionType string
	 * @param 	$dwStatus string
	 * @param 	$dateRangeValueStart datetime
	 * @param 	$dateRangeValueEnd datetime
	 * @return	int
	 */
	public function getDWCount($transactionType, $dwStatus, $dateRangeValueStart = '', $dateRangeValueEnd = '') {
		return $this->ci->payment->getDWCount($transactionType, $dwStatus, $dateRangeValueStart, $dateRangeValueEnd);
	}

	/**
	 * Will get deposit/withdrawal count, grouped by status
	 *
	 * @param 	$transactionType string
	 * @param 	$dwStatus string
	 * @param 	$dateRangeValueStart datetime
	 * @param 	$dateRangeValueEnd datetime
	 * @return	int
	 */
	public function getDWCountAllStatus($transactionType, $dateRangeValueStart = '', $dateRangeValueEnd = '') {
		return $this->ci->payment->getDWCountAllStatus($transactionType, $dateRangeValueStart, $dateRangeValueEnd);
	}

	/**
	 * Will get deposit/withdrawal count
	 *
	 * @param 	$transactionType string
	 * @param 	$dwStatus string
	 * @param 	$dateRangeValueStart datetime
	 * @param 	$dateRangeValueEnd datetime
	 * @return	int
	 */
	public function get3rdPartyDWCount($transactionType, $dwStatus, $method, $dateRangeValueStart, $dateRangeValueEnd) {
		return $this->ci->payment->get3rdPartyDWCount($transactionType, $dwStatus, $method, $dateRangeValueStart, $dateRangeValueEnd);
	}

	/**
	 * Will get transaction details
	 *
	 * @param 	$transactionId int
	 * @return	array
	 */
	public function getDepositWithdrawalTransactionDetail($walletAccountId, $paymentMethodId) {
		return $this->ci->payment->getDepositWithdrawalTransactionDetail($walletAccountId, $paymentMethodId);
	}

	/**
	 * Will get transaction details
	 *
	 * @param 	$transactionId int
	 * @return	array
	 */
	public function reviewManualThirdPartyDepositRequest($walletAccountId, $paymentMethodId) {
		return $this->ci->payment->reviewManualThirdPartyDepositRequest($walletAccountId, $paymentMethodId);
	}

	/**
	 * Will get transaction details
	 *
	 * @param 	$transactionId int
	 * @return	array
	 */
	public function getWithdrawalTransactionDetail($walletAccountId, $playerId = null) {
		// if ($this->getPlayerMainWalletBalance($playerId)[0]['mainwalletBalanceAmount'] < $this->getOperatorGlobalSetting('previous_balance_set_amount')[0]['value']) {
		// 	$this->setPlayerActiveWithdrawalConditionToInactive($playerId);
		// }
		return $this->ci->payment->getWithdrawalTransactionDetail($walletAccountId);
	}

	/**
	 * Will get operator settings for withdrawal condition
	 *
	 * @param 	$transactionId int
	 * @return	array
	 */
	// public function setPlayerActiveWithdrawalConditionToInactive($playerId) {
	// 	$playerWithdrawalCondition = $this->ci->payment->getPlayerWithdrawalCondition($playerId);
	// 	//var_dump($playerWithdrawalCondition);exit();
	// 	if (!empty($playerWithdrawalCondition)) {
	// 		foreach ($playerWithdrawalCondition as $key) {
	// 			$data['status'] = 2;
	// 			$this->ci->payment->inactiveWithdrawCondition($data, $key['withdrawConditionId']);
	// 		}
	// 	}
	// }

	/**
	 * Will get deposit approved transaction details
	 *
	 * @param 	$transactionId int
	 * @return	array
	 */
	public function getDepositApprovedTransactionDetail($walletAccountId) {
		return $this->ci->payment->getDepositApprovedTransactionDetail($walletAccountId);
	}

	/**
	 * Will get deposit approved transaction details
	 *
	 * @param 	$transactionId int
	 * @return	array
	 */
	public function getManualThirdPartyDepositApprovedTransactionDetail($walletAccountId) {
		return $this->ci->payment->getManualThirdPartyDepositApprovedTransactionDetail($walletAccountId);
	}

	/**
	 * Will get deposit approved transaction details
	 *
	 * @param 	$transactionId int
	 * @return	array
	 */
	public function getManualThirdPartyDepositDeclinedTransactionDetail($walletAccountId) {
		return $this->ci->payment->getManualThirdPartyDepositDeclinedTransactionDetail($walletAccountId);
	}

	/**
	 * Will get withdrawal approved transaction details
	 *
	 * @param 	$transactionId int
	 * @return	array
	 */
	public function getWithdrawalApprovedTransaction($sort_by, $limit, $offset) {
		return $this->ci->payment->getWithdrawalApprovedTransaction($sort_by, $limit, $offset);
	}

	/**
	 * Will get withdrawal approved transaction details
	 *
	 * @param 	$transactionId int
	 * @return	array
	 */
	public function getWithdrawalApprovedTransactionDetail($walletAccountId) {
		return $this->ci->payment->getWithdrawalApprovedTransactionDetail($walletAccountId);
	}

	/**
	 * Will get deposit declined transaction details
	 *
	 * @param 	$transactionId int
	 * @return	array
	 */
	public function getDepositDeclinedTransactionDetail($walletAccountId) {
		return $this->ci->payment->getDepositDeclinedTransactionDetail($walletAccountId);
	}

	/**
	 * Will get deposit declined transaction details
	 *
	 * @param 	$transactionId int
	 * @return	array
	 */
	public function reviewAuto3rdPartyDepositDeclined($walletAccountId) {
		return $this->ci->payment->reviewAuto3rdPartyDepositDeclined($walletAccountId);
	}

	/**
	 * Will get withdrawal declined transaction details
	 *
	 * @param 	$transactionId int
	 * @return	array
	 */
	public function getWithdrawalDeclinedTransactionDetail($walletAccountId) {
		return $this->ci->payment->getWithdrawalDeclinedTransactionDetail($walletAccountId);
	}

	/**
	 * Will get withdrawal approved transaction details
	 *
	 * @param 	$transactionId int
	 * @return	array
	 */
	public function getWithdrawalDeclinedTransaction($sort_by, $limit, $offset) {
		return $this->ci->payment->getWithdrawalDeclinedTransaction($sort_by, $limit, $offset);
	}

	/**
	 * Respond to request
	 *
	 * @param 	$dataRequest array
	 * @return	Boolean
	 */
	public function approveDeclinedDepositRequest($dataRequest) {
		return $this->ci->payment->approveDeclinedDepositRequest($dataRequest);
	}

	/**
	 * Respond to request
	 *
	 * @param 	$dataRequest array
	 * @return	Boolean
	 */
	public function approveDepositRequest($dataRequest) {
		return $this->ci->payment->approveDepositRequest($dataRequest);
	}

	// TODO: DUPLICATE WITH approveBonusRequest
	/**
	 * Respond to request
	 *
	 * @param 	$dataRequest array
	 * @return	Boolean
	 */
	public function approvePromoRequest($dataRequest) {
		return $this->ci->payment->approveBonusRequest($dataRequest['playerpromoId'], $dataRequest);
	}

	/**
	 * Gets All Over the counter Payment Method
	 *
	 * @return	array
	 */
	public function getPaymentMethodDetails($id1, $id2) {
		return $this->ci->payment->getPaymentMethodDetails($id1, $id2);
	}

	/**
	 * Gets All Over the counter Payment Method
	 *
	 * @return	array
	 */
	public function getPlayerDetails($playerId) {
		return $this->ci->payment->getPlayerDetails($playerId);
	}

	/**
	 * Will get player balance
	 *
	 * @param 	$sort_by array
	 * @param 	$limit int
	 * @param 	$offset int
	 * @return	$array
	 */
	public function viewPlayerBalance($sort_by, $limit, $offset) {
		return $this->ci->payment->viewPlayerBalance($sort_by, $limit, $offset);
	}

	/**
	 * Export adjustment history
	 *
	 * @return	$array
	 */
	public function exportAdjustmentHistoryToExcel() {
		return $this->ci->payment->exportAdjustmentHistoryToExcel();
	}

	/**
	 * Will get adjustment history
	 *
	 * @return	$array
	 */
	public function viewAdjustmentHistory() {
		return $this->ci->payment->viewAdjustmentHistory();
	}

	/**
	 * Add Ranking Settings
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function getOTCPaymentMethodDetails($id) {
		return $this->ci->payment->getOTCPaymentMethodDetails($id);
	}

	/**
	 * Add Ranking Settings
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function editPaymentMethod($data, $id) {
		return $this->ci->payment->editPaymentMethod($data, $id);
	}

	/**
	 * Add Ranking Settings
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function changeStatusPaymentMethod($data, $id) {
		return $this->ci->payment->changeStatusPaymentMethod($data, $id);
	}

	/**
	 * Add Ranking Settings
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function deletePaymentMethod($id) {
		return $this->ci->payment->deletePaymentMethod($id);
	}

	/**
	 * Will player balance details
	 *
	 * @param 	$playerId int
	 * @return	array
	 */
	public function getPlayerBalanceDetails($playerId) {
		return $this->ci->payment->getPlayerBalanceDetails($playerId);
	}

	/**
	 * Will get player balance details
	 *
	 * @param 	$playerId int
	 * @return	array
	 */
	public function getPlayerMainWalletBalance($playerId) {
		return $this->ci->payment->getPlayerMainWalletBalance($playerId);
	}

	/**
	 * Will get player balance details
	 *
	 * @param 	$playerId int
	 * @return	array
	 */
	public function getPlayerSubWalletBalance($playerId) {
		return $this->ci->payment->getPlayerSubWalletBalance($playerId);
	}

	/**
	 * Will get player balance details
	 *
	 * @param 	$playerId int
	 * @return	array
	 */
	public function getPlayerSubWalletBalanceAG($playerId) {
		return $this->ci->payment->getPlayerSubWalletBalanceAG($playerId);
	}

	/**
	 * Will get player balance details
	 *
	 * @param 	$playerId int
	 * @return	array
	 */
	public function getPlayerCashbackWalletBalance($playerId) {
		return $this->ci->payment->getPlayerCashbackWalletBalance($playerId);
	}

	/**
	 * Will get player balance details
	 *
	 * @param 	$playerId int
	 * @return	array
	 */
	public function getPlayerBonusAmount($playerDepositPromoId) {
		return $this->ci->payment->getPlayerBonusAmount($playerDepositPromoId);
	}

	/**
	 * Will set new balance amount
	 *
	 * @param 	$playerId int
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function setPlayerNewBalAmount($playerId, $data) {
		return $this->ci->payment->setPlayerNewBalAmount($playerId, $data);
	}

	/**
	 * Will set new balance amount
	 *
	 * @param 	$playerId int
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function saveToAdjustmentHistory($data) {
		return $this->ci->payment->saveToAdjustmentHistory($data);
	}

	/**
	 * Will set save cashback history
	 *
	 * @param 	$playerId int
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function saveCashbackHistory($playerId) {
		return $this->ci->payment->saveCashbackHistory($playerId);
	}

	/**
	 * Will set new balance amount
	 *
	 * @param 	$playerId int
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function setPlayerNewMainWalletBalAmount($playerId, $data) {
		return $this->ci->payment->setPlayerNewMainWalletBalAmount($playerId, $data);
	}

	/**
	 * Will set new balance amount
	 *
	 * @param 	$playerId int
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function setPlayerNewSubWalletBalAmount($playerId, $data, $gameType) {
		return $this->ci->payment->setPlayerNewSubWalletBalAmount($playerId, $data, $gameType);
	}

	/**
	 * Will set new balance amount
	 *
	 * @param 	$playerId int
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function setPlayerNewSubWalletBalAmountAG($playerId, $data) {
		return $this->ci->payment->setPlayerNewSubWalletBalAmountAG($playerId, $data);
	}

	/**
	 * Will set new balance amount
	 *
	 * @param 	$playerId int
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function setPlayerNewCashbackWalletBalAmount($playerId, $data) {
		return $this->ci->payment->setPlayerNewCashbackWalletBalAmount($playerId, $data);
	}

	/**
	 * Will add adjustment history
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function addPlayerBalAdjustmentHistory($data) {
		return $this->ci->payment->addPlayerBalAdjustmentHistory($data);
	}

	/**
	 * Will search player balance
	 *
	 * @param 	$sort_by array
	 * @param 	$limit int
	 * @param 	$offset int
	 * @return	$array
	 */
	public function playerBalanceListSearch($sort_by, $limit, $offset) {
		return $this->ci->payment->playerBalanceListSearch($sort_by, $limit, $offset);
	}

	/**
	 * Get Player Transaction History
	 *
	 * @return	$array
	 */
	public function getPlayerTransactionHistory($playerAccountId) {
		return $this->ci->payment->getPlayerTransactionHistory($playerAccountId);
	}

	/**
	 * getPlayerCashbackPercentage
	 *
	 * @return	$array
	 */
	public function getPlayerCashbackPercentage($playerId, $gameType) {
		return $this->ci->payment->getPlayerCashbackPercentage($playerId, $gameType);
	}

	/**
	 * Get Player Transaction Log
	 *
	 * @return	$array
	 */
	public function getPlayerTransactionLog($playerId) {
		return $this->ci->payment->getPlayerTransactionLog($playerId);
	}

	/**
	 * Get Affiliate Payment History
	 *
	 * @return	$array
	 */
	public function getAffiliatePaymentHistory() {
		return $this->ci->payment->getAffiliatePaymentHistory();
	}

	/**
	 * Get Transaction History of Affiliate
	 *
	 * @return	array
	 */
	public function getTransactionHistory($affiliate_id) {
		return $this->ci->payment->getTransactionHistory($affiliate_id);
	}

	/**
	 * Get Affiliates Detail
	 *
	 * @param	int
	 * @param	int
	 * @return	array
	 */
	public function getAffiliatesDetail($affiliateId, $paymentHistoryId) {
		return $this->ci->payment->getAffiliatesDetail($affiliateId, $paymentHistoryId);
	}

	/**
	 * Respond to Affiliate Request Details
	 *
	 * @param	array
	 * @param	int
	 * @return	void
	 */
	public function respondToAffiliateRequest($data, $payment_history_id) {
		$this->ci->payment->respondToAffiliateRequest($data, $payment_history_id);
	}
	// TODO: DUPLICATE WITH APPROVEPROMOREQUEST
	/**
	 * Approved bonus request
	 *
	 * @param	$data array
	 * @param	promoId int
	 * @return	void
	 */
	public function approveBonusRequest($playerPromoId, $data) {
		$this->ci->payment->approveBonusRequest($playerPromoId, $data);
	}

	/**
	 * Affiliate Withdrawal Request count
	 *
	 * @param	status int
	 * @param	$start_today datetime
	 * @return	$end_today  datetime
	 */
	public function getAgentRequestCount($type, $start_today, $end_today) {
		$this->ci->load->model(array('agency_model'));
		return $this->ci->agency_model->getAgentRequestCount($type, $start_today, $end_today);
	}

	/**
	 * Affiliate Withdrawal Request count
	 *
	 * @param	status int
	 * @param	$start_today datetime
	 * @return	$end_today  datetime
	 */
	public function getAffiliateRequestCount($type, $start_today, $end_today) {
		$this->ci->load->model(array('affiliatemodel'));
		return $this->ci->affiliatemodel->getAffiliateRequestCount($type, $start_today, $end_today);
	}

	/**
	 * clear player bonus
	 *
	 * @param	$data array
	 * @param	promoId int
	 * @return	void
	 */
	public function clearPlayerDepositBonus($playerDepositPromoId, $data) {
		$this->ci->payment->clearPlayerDepositBonus($playerDepositPromoId, $data);
	}

	/**
	 * Approved bonus request
	 *
	 * @param	promoId int
	 * @return	void
	 */
	public function getBonusDetail($promoId) {
		$this->ci->payment->getBonusDetail($promoId);
	}

	/**
	 * Approved bonus request
	 *
	 * @param	promoId int
	 * @return	void
	 */
	public function getFriendReferralDetails($limit, $offset) {
		$this->ci->payment->getFriendReferralDetails($limit, $offset);
	}

	/**
	 * get all player account by playerId
	 *
	 * @param  int
	 * @return  array
	 */
	public function getAllPlayerAccountByPlayerId($player_id) {
		return $this->ci->payment->getAllPlayerAccountByPlayerId($player_id);
	}

	/**
	 * get list of games
	 *
	 * @return  array
	 */
	public function getGames() {
		return $this->ci->payment->getGames();
	}

	/**
	 * get list of subwallet request
	 *
	 * @return  array
	 */
	// public function getAllSubWallet() {
	// 	return $this->ci->payment->getAllSubWallet();
	// }

	/**
	 * update subwallet details
	 *
	 * @return  array
	 * @return  int
	 * @return  array
	 */
	// public function updateSubWalletDetails($data, $subwallet_details_id) {
	// 	return $this->ci->payment->updateSubWalletDetails($data, $subwallet_details_id);
	// }

	/**
	 * Will set player new balance amount by playerAccountId
	 *
	 * @param $dataRequest - array
	 * @return Bool - TRUE or FALSE
	 */

	public function setPlayerNewBalAmountByPlayerAccountId($playerAccountId, $data) {
		return $this->ci->payment->setPlayerNewBalAmountByPlayerAccountId($playerAccountId, $data);
	}

	/**
	 * get balace by playerAccountId
	 *
	 * @return  array
	 */
	public function getBalanceByPlayerAccountId($player_account_id) {
		return $this->ci->payment->getBalanceByPlayerAccountId($player_account_id);
	}

	/**
	 * Will randomize alphanumeric and special characters
	 *
	 * @param 	string
	 * @return	string
	 */
	public function generateRandomCode() {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
			. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. '0123456789'); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$generatePromoCode = '';
		foreach (array_rand($seed, 7) as $k) {
			$generatePromoCode .= $seed[$k];
		}

		return $generatePromoCode;
	}

	/**
	 * Will randomize alphanumeric and special characters
	 *
	 * @param 	string
	 * @return	string
	 */
	public function randomizer($name) {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
			. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. '0123456789!@#$%^&*()'
			. $name); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$randomPassword = '';
		foreach (array_rand($seed, 9) as $k) {
			$randomPassword .= $seed[$k];
		}

		return $randomPassword;
	}

	public function getPaypalSettings($limit, $offset) {
		return $this->ci->payment->getPaypalSettings($limit, $offset);
	}

	public function getNetellerSettings($limit, $offset) {
		return $this->ci->payment->getNetellerSettings($limit, $offset);
	}

	public function getSkrillSettings($limit, $offset) {
		return $this->ci->payment->getSkrillSettings($limit, $offset);
	}

	public function getPaypalSettingActive() {
		return $this->ci->payment->getPaypalSettingActive();
	}

	public function getNetellerSettingActive() {
		return $this->ci->payment->getNetellerSettingActive();
	}

	/**
	 * Add Paypal Settings
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function getPaypalSettingsDetails($id) {
		return $this->ci->payment->getPaypalSettingsDetails($id);
	}

	/**
	 * Add Neteller Settings
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function getNetellerSettingsDetails($id) {
		return $this->ci->payment->getNetellerSettingsDetails($id);
	}

	/**
	 * getSkrillSettingsDetails
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function getSkrillSettingsDetails($id) {
		return $this->ci->payment->getSkrillSettingsDetails($id);
	}

	/**
	 * Edit Paypal Settings
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function editPaypalSettings($data, $id) {
		return $this->ci->payment->editPaypalSettings($data, $id);
	}

	/**
	 * Edit Neteller Settings
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function editNetellerSettings($data, $id) {
		return $this->ci->payment->editNetellerSettings($data, $id);
	}

	/**
	 * Edit Skrill Settings
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function editSkrillSettings($data, $id) {
		return $this->ci->payment->editSkrillSettings($data, $id);
	}

	/**
	 * Add Ranking Settings
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function insertPaypalSettings($data) {
		return $this->ci->payment->insertPaypalSettings($data);
	}

	/**
	 * Add Ranking Settings
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function deletePaypalSettings($id) {
		return $this->ci->payment->deletePaypalSettings($id);
	}

	/**
	 * Add Ranking Settings
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function changeStatusPaypalSettings($data, $id) {
		return $this->ci->payment->changeStatusPaypalSettings($data, $id);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function getAllFriendReferralRequest($sort, $limit, $offset) {
		return $this->ci->payment->getAllFriendReferralRequest($sort, $limit, $offset);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function updateFriendReferralDetails($data, $referralId) {
		return $this->ci->payment->updateFriendReferralDetails($data, $referralId);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function getFriendReferralDetailsById($referralId) {
		return $this->ci->payment->getFriendReferralDetailsById($referralId);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function searchPlayerReferralList($search, $limit, $offset, $type) {
		return $this->ci->payment->searchPlayerReferralList($search, $limit, $offset, $type);
	}

	/**
	 * Will get dwCount in walletaccount table
	 *
	 * @param 	int
	 * @return	int
	 */
	public function getDWCountById($playerId) {
		return $this->ci->payment->getDWCountById($playerId);
	}

	/**
	 * Set Player Cashback (daily)
	 *
	 * @param $playerId - int
	 * @param $totalBet - int
	 * @param $gameType - int (1-pt,2-ag)
	 * @return	Boolean
	 */
	public function setPlayerCashback($playerId, $totalBet, $gameType) {
		//get main wallet balance
		$mainwallet = $this->getPlayerMainWalletBalance($playerId);

		//get player cashback percentage
		$playerCashbackPercentage = $this->getPlayerCashbackPercentage($playerId, $gameType);

		//compute cashback amount
		$cashbackAmount = $totalBet * ($playerCashbackPercentage[0]['percentage'] / 100);

		//save transaction details on deposit
		$transactionDetails = array('amount' => $cashbackAmount,
			'transaction_type' => 0, //deposit
			'from_id' => $this->authentication->getUserId(),
			'from_type' => 0, //admin
			'to_id' => $playerId,
			'to_type' => 1, //player
			'note' => 'cashback ' . $cashbackAmount . ' to ' . $playerId,
			'before_balance' => $mainwallet[0]['mainwalletBalanceAmount'],
			'after_balance' => $mainwallet[0]['mainwalletBalanceAmount'] + $cashbackAmount,
			'status' => 0, //approved
		);
		$this->saveTransaction($transactionDetails);

		//save cashback history
		$data = array('playerId' => $playerId,
			'amount' => $cashbackAmount,
			'receivedOn' => date('Y-m-d H:i:s'),
		);
		$this->saveCashbackHistory($data);

		$newBalAmount = $mainwallet[0]['mainwalletBalanceAmount'] + $cashbackAmount;

		$playerAcctdata = array('totalBalanceAmount' => $newBalAmount);
		$this->setPlayerNewBalAmount($playerId, $playerAcctdata);
	}

	/**
	 * get list of subwallet per player
	 *
	 * @return  array
	 */
	public function getSubWalletByPlayer($player_id, $limit, $offset) {
		return $this->ci->payment->getSubWalletByPlayer($player_id, $limit, $offset);
	}

	/**
	 * get payment history
	 *
	 * @return  array
	 */
	public function getPaymentHistoryByPlayer($player_id, $limit, $offset) {
		return $this->ci->payment->getPaymentHistoryByPlayer($player_id, $limit, $offset);
	}

	/**
	 * get bank history
	 *
	 * @return  array
	 */
	public function getBankHistoryByPlayer($player_id, $limit, $offset) {
		return $this->ci->payment->getBankHistoryByPlayer($player_id, $limit, $offset);
	}

	public function calculateBankDeposit($bankAccountId) {
		return $this->ci->payment->calculateBankDeposit($bankAccountId);
	}

	/**
	 * export player balance
	 *
	 * @param 	$sort_by array
	 * @return	$array
	 */
	public function exportPlayerBalanceToExcel($sort_by) {
		return $this->ci->payment->exportPlayerBalanceToExcel($sort_by);
	}

	/**
	 * Will display cashcard deposit list
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function getCashcardDepositRequestTransaction($sort_by, $limit, $offset) {
		return $this->ci->payment->getCashcardDepositRequestTransaction($sort_by, $limit, $offset);
	}

	/**
	 * Will display cashcard dwCount
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function getCashcardDWCount($transactionType, $dwStatus, $dateRangeValueStart, $dateRangeValueEnd) {
		return $this->ci->payment->getCashcardDWCount($transactionType, $dwStatus, $dateRangeValueStart, $dateRangeValueEnd);
	}

	/**
	 * getOperatorGlobalSetting
	 *
	 * @return  array
	 */
	public function getOperatorGlobalSetting($name) {
		return $this->ci->operatorglobalsettings->getOperatorGlobalSetting($name);
	}

	/**
	 * saveTransactionFee
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function setOperatorGlobalSetting($data) {
		return $this->ci->operatorglobalsettings->setOperatorGlobalSetting($data);
	}

	/**
	 * saveTransactionFeeHistory
	 *
	 * @return  void
	 */
	public function saveTransactionFeeHistory($data) {
		return $this->ci->payment->saveTransactionFeeHistory($data);
	}

	/**
	 * saveTransactionDetails
	 *
	 * @param 	$transactionFeeDetails array
	 * @return	Boolean
	 */
	public function saveTransaction($transactionFeeDetails) {
		return $this->ci->transactions_library->saveTransaction($transactionFeeDetails);
	}

	/**
	 * get player sub wallet account id
	 *
	 * @param  playerId
	 * @param  subwalletType
	 * @return int
	 */
	public function getSubWalletAccountId($playerId, $subwalletType) {
		return $this->ci->payment->getSubWalletAccountId($playerId, $subwalletType);
	}

	/**
	 * Will get player sub wallet balance
	 *
	 * @param 	playerId int
	 * @param 	gameType int
	 * @return 	array
	 */
	public function getPlayerCurrentSubWalletBalance($playerId, $gameType) {
		return $this->ci->payment->getPlayerCurrentSubWalletBalance($playerId, $gameType);
	}

	/**
	 * Will get player current bet
	 *
	 * @param 	playerName int
	 * @param 	dateJoined int
	 * @return 	array
	 */
	public function getPlayerCurrentBet($playerName, $dateJoined) {
		return $this->ci->payment->getPlayerCurrentBet($playerName, $dateJoined);
	}

	/**
	 * Will save player withdrawal condition
	 *
	 * @param 	conditionData array
	 * @return 	array
	 */
	public function savePlayerWithdrawalCondition($conditionData) {
		return $this->ci->payment->savePlayerWithdrawalCondition($conditionData);
	}

	/**
	 * Will get player withdrawal condition
	 *
	 * @param 	conditionData array
	 * @return 	array
	 */
	public function getPlayerWithdrawalCondition($playerId) {
		return $this->ci->payment->getPlayerWithdrawalCondition($playerId);
	}

	/**
	 * save player bank changes
	 *
	 * @return  rendered template
	 */
	public function saveBankHistoryByPlayer($data) {
		$this->ci->payment->saveBankHistoryByPlayer($data);
	}

	/**
	 * retrive wallet account object by wallet account id
	 *
	 * @return  wallet account object
	 */
	public function getWalletAccountBy($walletAccountId) {
		return $this->ci->wallet_model->getWalletAccountBy($walletAccountId);
	}

    /**
     * retrive self exclusion account   by responsible gaming
     *
     * @return  self exclusion account number
     */
    public function getRbSelfExAccount() {
        return $this->ci->responsible_gaming->getSFcounts();
    }

}

/* End of file payment_manager.php */
/* Location: ./application/libraries/payment_manager.php */
