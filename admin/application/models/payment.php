<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Payment
 *
 * This model represents payment. It operates the following tables:
 * - payment deposit and withdrawal
 *
 * @author	ASRII
 */

class Payment extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	/**
	 * Will get request deposit transaction
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function getDepositRequestTransaction($sort_by, $limit, $offset = 0) {
		//var_dump($sort_by);exit();
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
		//var_dump($sort_by);exit();
		$this->db->select('walletaccount.*,
						   player.username AS playerName,
						   playerdetails.firstName,
						   playerdetails.lastName,
						   playeraccount.currency,
						   player.playerId,
						   paymentmethod.paymentMethodName,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   localbankdepositdetails.bankAccountId,
						   otcpaymentmethod.bankName AS depositedToBankName,
						   otcpaymentmethod.accountNumber AS depositedToAcctNo,
						   otcpaymentmethod.branchName AS depositedToBranchName,
						   otcpaymentmethod.accountName AS depositedToAcctName,
						   localbankdepositdetails.depositSlipName,
						   localbankdepositdetails.transactionFee,
						   playerbankdetails.bankAccountFullName,
						   playerbankdetails.bankAccountNumber,
						   playerbankdetails.branch,
						   promorules.promorulesId,
						   promorules.promoName,
						   playerpromo.playerpromoId,
						   playerpromo.bonusAmount
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId', 'left')
			->join('player', 'player.playerId = playeraccount.playerId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('paymentmethod', 'paymentmethod.paymentMethodId = walletaccount.dwMethod', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->join('localbankdepositdetails', 'localbankdepositdetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('otcpaymentmethod', 'otcpaymentmethod.otcPaymentMethodId = localbankdepositdetails.bankAccountId', 'left')
			->join('playerbankdetails', 'playerbankdetails.playerBankDetailsId = localbankdepositdetails.playerBankDetailsId', 'left')
			->join('playerpromo', 'playerpromo.playerpromoId = walletaccount.playerPromoId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left');

		$this->db->where('walletaccount.dwMethod <', 2);
		$this->db->where('walletaccount.transactionType', $sort_by['transactionType']);
		$this->db->where('walletaccount.dwStatus', $sort_by['dwStatus']);

		if ($sortPlayerLvl != '') {
			$this->db->where('vipsetting.vipSettingId', $sortPlayerLvl);
		}
		if ($sortPaymentMethod != '') {
			$this->db->where('walletaccount.localBankType', $sortPaymentMethod);
		}
		if ($sortDateRangeValueStart != '') {
			$this->db->where('walletaccount.dwDateTime >= ', $sortDateRangeValueStart . ' 00:00:00');
		}
		if ($sortDateRangeValueEnd != '') {
			$this->db->where('walletaccount.dwDateTime <= ', $sortDateRangeValueEnd . ' 23:59:59');
		}

		$this->db->order_by('walletaccount.dwDateTime', 'desc');

		if ($limit != null) {
			$this->db->limit($limit, $offset);
		}

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['playerActivePromo'] = $this->getPlayerActivePromo($row['playerId']);
				//$row['transactionFee'] = $this->getTrasancationFeeSetting();
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get request 3rd party deposit transaction
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function get3rdPartyDepositRequestTransaction($sort_by, $limit, $offset = 0) {
		//var_dump($sort_by);exit();
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
		//var_dump($sort_by);exit();
		$this->db->select('walletaccount.*,
						   player.username AS playerName,
						   playeraccount.currency,
						   player.playerId,
						   playerdetails.firstName,
						   playerdetails.lastName,
						   paymentmethod.paymentMethodName,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   paypalpaymentmethoddetails.paypalMerchantAccount,
						   paypalpaymentmethoddetails.transactionFee,
						   netellerdepositdetails.merchantAccount AS netellerMerchantAccount,
						   promorules.promorulesId,
						   promorules.promoName,
						   playerpromo.playerpromoId,
						   playerpromo.bonusAmount
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId', 'left')
			->join('player', 'player.playerId = playeraccount.playerId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId')
			->join('paymentmethod', 'paymentmethod.paymentMethodId = walletaccount.dwMethod', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->join('paypalpaymentmethoddetails', 'paypalpaymentmethoddetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('paypalsettings', 'paypalsettings.paypalSettingsId = walletaccount.walletAccountId', 'left')
			->join('netellerdepositdetails', 'netellerdepositdetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerpromo', 'playerpromo.playerpromoId = walletaccount.playerPromoId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->order_by('walletaccount.dwDateTime', 'desc');

		$this->db->where('walletaccount.dwMethod >', 1);
		$this->db->where('walletaccount.thirdpartyPaymentMethod', 'auto');
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

		$this->db->order_by('walletaccount.dwDateTime', 'desc');

		if ($limit != null) {
			$this->db->limit($limit, $offset);
		}

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				//$row['paymentmethoddetails']  = $this->getPaymentMethodDetails($row['paymentMethodId'],$row['walletAccountId']);
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get manual request 3rd party deposit transaction
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function getManual3rdPartyDepositRequestTransaction($sort_by, $limit, $offset = 0) {
		//var_dump($sort_by);exit();
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
		//var_dump($sort_by);exit();
		//$this->db->select('walletaccount.*')->from('walletaccount');
		$this->db->select('walletaccount.*,
						   player.username,
						   playerdetails.firstName,
						   playerdetails.lastName,
						   playeraccount.currency,
						   player.playerId,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   manualthirdpartydepositdetails.depositTo,
						   manualthirdpartydepositdetails.depositSlipName,
						   manualthirdpartydepositdetails.transacRefCode,
						   manualthirdpartydepositdetails.depositDateTime,
						   manualthirdpartydepositdetails.depositorName,
						   manualthirdpartydepositdetails.depositorAccount,
						   manualthirdpartydepositdetails.transactionFee,
						   promorules.promorulesId,
						   promorules.promoName,
						   playerpromo.playerpromoId,
						   playerpromo.bonusAmount
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId', 'left')
			->join('player', 'player.playerId = playeraccount.playerId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->join('manualthirdpartydepositdetails', 'manualthirdpartydepositdetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerpromo', 'playerpromo.playerpromoId = walletaccount.playerPromoId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->order_by('walletaccount.dwDateTime', 'desc');

		$this->db->where('walletaccount.dwMethod >', 1);
		$this->db->where('walletaccount.thirdpartyPaymentMethod', 'manual');
		$this->db->where('walletaccount.transactionType', $sort_by['transactionType']);
		$this->db->where('walletaccount.dwStatus', $sort_by['dwStatus']);

		if ($sortPlayerLvl != '') {
			$this->db->where('vipsetting.vipSettingId', $sortPlayerLvl);
		}
		// if($sortPaymentMethod != ''){
		// 	$this->db->where('walletaccount.dwMethod', $sortPaymentMethod);
		// }
		if ($sortDateRangeValueStart != '') {
			$this->db->where('walletaccount.dwDateTime >= ', $sortDateRangeValueStart . ' 00:00:00');
		}
		if ($sortDateRangeValueEnd != '') {
			$this->db->where('walletaccount.dwDateTime <= ', $sortDateRangeValueEnd . ' 23:59:59');
		}

		$this->db->order_by('walletaccount.dwDateTime', 'desc');

		if ($limit != null) {
			$this->db->limit($limit, $offset);
		}

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {

				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get manual request 3rd party deposit transaction
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function reviewManualThirdPartyDepositRequest($walletAccountId, $paymentMethodId) {

		$this->db->select('walletaccount.*,
						   player.email,
						   player.createdOn,
						   player.username AS playerName,
						   playeraccount.currency,
						   player.playerId,
						   playerdetails.*,
						   playeraccount.totalBalanceAmount AS currentBalAmount,
						   playeraccount.currency AS currentBalCurrency,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   manualthirdpartydepositdetails.depositTo AS paymentMethodName,
						   manualthirdpartydepositdetails.depositSlipName,
						   manualthirdpartydepositdetails.transacRefCode,
						   manualthirdpartydepositdetails.depositDateTime,
						   manualthirdpartydepositdetails.depositorName,
						   manualthirdpartydepositdetails.depositorAccount,
						   manualthirdpartydepositdetails.transactionFee,
						   promorules.promorulesId,
						   promorules.promoName,
						   playerpromo.playerpromoId,
						   playerpromo.bonusAmount
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId', 'left')
			->join('player', 'player.playerId = playeraccount.playerId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->join('manualthirdpartydepositdetails', 'manualthirdpartydepositdetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerpromo', 'playerpromo.playerpromoId = walletaccount.playerPromoId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->order_by('walletaccount.dwDateTime', 'desc');

		$this->db->where('walletaccount.walletAccountId', $walletAccountId);
		$this->db->where('walletaccount.dwMethod >', 1);
		$this->db->where('walletaccount.thirdpartyPaymentMethod', 'manual');

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['dwDateTime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dwDateTime']));
				$row['birthdate'] = mdate('%M %d, %Y', strtotime($row['birthdate']));
				$row['createdOn'] = mdate('%M %d, %Y', strtotime($row['createdOn']));
				$row['processDatetime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['processDatetime']));
				$row['playerName'] = ucwords($row['playerName']);
				$row['playerActivePromo'] = $this->getPlayerActivePromo($row['playerId']);
				$row['depositCnt'] = $this->getPlayerTotalDepositCnt($row['playerId']);
				//$row['transacHistory'] = $this->getPlayerTransactionHistory($row['playerAccountId']);
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get request withdrawal transaction
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function getWithdrawalRequestTransaction($sort_by, $limit, $offset = 0) {

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
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId', 'left')
			->join('player', 'player.playerId = playeraccount.playerId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId')
			->join('paymentmethod', 'paymentmethod.paymentMethodId = walletaccount.dwMethod', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->join('localbankwithdrawaldetails', 'localbankwithdrawaldetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerbankdetails', 'playerbankdetails.playerBankDetailsId = localbankwithdrawaldetails.playerBankDetailsId', 'left')
			->join('playerpromo', 'playerpromo.playerpromoId = walletaccount.playerPromoId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->join('banktype', 'banktype.bankTypeId = playerbankdetails.bankTypeId', 'left');

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
		$this->db->order_by('walletaccount.dwDateTime', 'desc');
		if ($limit != null) {
			$this->db->limit($limit, $offset);
		}

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['subwalletBalanceAmount'] = $this->getPlayerSubWalletBalance($row['playerId']);
				$row['subwalletBalanceAmountAG'] = $this->getPlayerSubWalletBalanceAG($row['playerId']);
				$row['cashbackwalletBalanceAmount'] = $this->getPlayerCashbackWalletBalance($row['playerId']);
				//$row['playerPromoActive'] = $this->getPlayerPromoActive($row['playerId']);
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get request withdrawal transaction
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function getWithdrawalDeclinedTransaction($sort_by, $limit, $offset = 0) {

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
			->join('localbankwithdrawaldetails', 'localbankwithdrawaldetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerbankdetails', 'playerbankdetails.playerBankDetailsId = localbankwithdrawaldetails.playerBankDetailsId', 'left')
			->join('playerpromo', 'playerpromo.playerpromoId = walletaccount.playerPromoId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->join('banktype', 'banktype.bankTypeId = playerbankdetails.bankTypeId', 'left')
			->join('adminusers', 'adminusers.userId = walletaccount.processedBy', 'left');

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

		$this->db->limit($limit, $offset);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['subwalletBalanceAmount'] = $this->getPlayerSubWalletBalance($row['playerId']);
				$row['subwalletBalanceAmountAG'] = $this->getPlayerSubWalletBalanceAG($row['playerId']);
				$row['cashbackwalletBalanceAmount'] = $this->getPlayerCashbackWalletBalance($row['playerId']);
				$row['playerPromoActive'] = $this->getPlayerPromoActive($row['playerId']);
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get player details
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function getPlayerDetails($playerId) {
		$this->db->select('player.playerId,
						   player.username,
						   player.createdOn,
						   playerdetails.firstname,
						   playerdetails.lastname,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   ')
			->from('playeraccount')
			->join('player', 'player.playerId = playeraccount.playerId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');

		$this->db->where('playeraccount.type', 'subwallet');
		$this->db->where('playeraccount.typeId', 1);
		$this->db->where('player.playerId', $playerId);

		$query = $this->db->get();
		//var_dump($query->result_array());exit();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['createdOn'] = mdate('%M %d, %Y', strtotime($row['createdOn']));
				$data[] = $row;
			}
		}
		return $data;
	}

	/**
	 * Will get player balance
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function getPlayerSubWalletBalance($playerId) {
		$this->db->select('playeraccount.totalBalanceAmount	AS subwalletBalanceAmount
						   ')
			->from('playeraccount')
			->join('player', 'player.playerId = playeraccount.playerId', 'left');

		$this->db->where('playeraccount.type', 'subwallet');
		$this->db->where('playeraccount.typeId', 1);
		$this->db->where('player.playerId', $playerId);

		$query = $this->db->get();
		//var_dump($query->result_array());exit();
		return $query->result_array();
	}

	/**
	 * Will get player balance
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function getPlayerSubWalletBalanceAG($playerId) {
		$this->db->select('playeraccount.totalBalanceAmount	AS subwalletBalanceAmount
						   ')
			->from('playeraccount')
			->join('player', 'player.playerId = playeraccount.playerId', 'left');

		$this->db->where('playeraccount.type', 'subwallet');
		$this->db->where('playeraccount.typeId', 2);
		$this->db->where('player.playerId', $playerId);

		$query = $this->db->get();
		//var_dump($query->result_array());exit();
		return $query->result_array();
	}

	/**
	 * Will get player sub wallet balance
	 *
	 * @param 	playerId int
	 * @param 	gameType int
	 * @return 	array
	 */
	public function getPlayerCurrentSubWalletBalance($playerId, $gameType) {
		$this->db->select('playeraccount.totalBalanceAmount	AS subwalletBalanceAmount
						   ')
			->from('playeraccount')
			->join('player', 'player.playerId = playeraccount.playerId', 'left');

		$this->db->where('playeraccount.type', 'subwallet');
		$this->db->where('playeraccount.typeId', $gameType);
		$this->db->where('player.playerId', $playerId);

		$query = $this->db->get();
		//var_dump($query->result_array());exit();
		return $query->row_array();
	}

	/**
	 * Will get player wallet balance
	 *
	 * @param 	playerId int
	 * @return 	array
	 */
	public function getPlayerCurrentWalletBalance($playerId) {
		$this->db->select('
						   playeraccount.totalBalanceAmount	AS balance,
						   playeraccount.typeId as gameProvideTypeId,
						   playeraccount.type as walletType,
						   game.game
						 ')
			->from('playeraccount');
		$this->db->join('player', 'player.playerId = playeraccount.playerId', 'left');
		$this->db->join('game', 'game.gameId = playeraccount.typeId', 'left');
		$this->db->where('player.playerId', $playerId);
		$this->db->order_by('playeraccount.playerAccountId');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * Will get player balance
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function getPlayerCashbackWalletBalance($playerId) {
		$this->db->select('playeraccount.totalBalanceAmount	AS cashbackwalletBalanceAmount
						   ')
			->from('playeraccount')
			->join('player', 'player.playerId = playeraccount.playerId', 'left');

		$this->db->where('playeraccount.type', 'cashbackwallet');
		$this->db->where('playeraccount.typeOfPlayer', 'real');
		$this->db->where('player.playerId', $playerId);

		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * Will get player balance
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function getPlayerMainWalletBalance($playerId) {
		$this->db->select('playeraccount.totalBalanceAmount	AS mainwalletBalanceAmount
						   ')
			->from('playeraccount')
			->join('player', 'player.playerId = playeraccount.playerId', 'left');

		$this->db->where('playeraccount.type', 'wallet');
		$this->db->where('player.playerId', $playerId);

		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * Will get player bonus amount
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function getPlayerBonusAmount($playerpromoId) {
		$this->db->select('bonusAmount')->from('playerpromo');
		$this->db->where('playerpromoId', $playerpromoId);

		$query = $this->db->get();
		//var_dump($query->result_array());exit();
		return $query->result_array();
	}

	/**
	 * Will get player balance
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function getPlayerTransactionLog($playerId) {
		$this->db->select('player.username,
						   player.email,
						   player.createdOn,
						   player.playerId,
						   adminusers.username AS processedByAdmin,
						   playerdetails.*,
						   playeraccount.totalBalanceAmount as totalBalanceAmt,
						   playeraccount.currency,
						   playeraccount.playerAccountId,
						   paymentMethod.paymentMethodName,
						   walletaccount.*')
			->from('player')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('playeraccount', 'playeraccount.playerId = player.playerId', 'left')
			->join('walletaccount', 'walletaccount.playerAccountId = playeraccount.playerAccountId', 'left')
			->join('adminusers', 'adminusers.userId = walletAccount.processedBy', 'left')
			->join('paymentMethod', 'paymentmethod.paymentMethodId = walletAccount.dwMethod', 'left');

		$this->db->where('player.status', 0);
		$this->db->where('player.playerId', $playerId);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['firstName'] = $row['firstName'] ? ucwords($row['firstName']) : '';
				$row['lastName'] = $row['lastName'] ? ucwords($row['lastName']) : '';
				$row['createdOn'] = mdate('%M %d, %Y', strtotime($row['createdOn']));
				$row['birthdate'] = mdate('%M %d, %Y', strtotime($row['birthdate']));
				$row['processDatetime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['processDatetime']));
				$row['dwStatus'] = strtoupper($row['dwStatus']);
				$row['transactionType'] = strtoupper($row['transactionType']);
				$row['dwDateTime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dwDateTime']));
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get approved/declined deposit transaction
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function getDepositApprovedDeclinedTransaction($sort_by, $limit, $offset = 0) {

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
						   player.username AS playerName,
						   playerdetails.firstName,
						   playerdetails.lastName,
						   adminusers.username AS processedByAdmin,
						   playeraccount.currency,
						   playeraccount.totalBalanceAmount,
						   player.playerId,
						   paymentmethod.paymentMethodName,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   localbankdepositdetails.depositSlipName,
						   localbankdepositdetails.transactionFee,
						   otcpaymentmethod.bankName AS depositedToBankName,
						   otcpaymentmethod.accountNumber AS depositedToAcctNo,
						   otcpaymentmethod.branchName AS depositedToBranchName,
						   otcpaymentmethod.accountName AS depositedToAcctName,
						   playerbankdetails.bankAccountFullName,
						   playerbankdetails.bankAccountNumber,
						   playerbankdetails.branch,
						   promorules.promorulesId,
						   promorules.promoName,
						   playerpromo.playerpromoId,
						   playerpromo.bonusAmount
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId', 'left')
			->join('player', 'player.playerId = playeraccount.playerId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('adminusers', 'adminusers.userId = walletaccount.processedBy', 'left')
			->join('paymentmethod', 'paymentmethod.paymentMethodId = walletaccount.dwMethod', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->join('localbankdepositdetails', 'localbankdepositdetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('otcpaymentmethod', 'otcpaymentmethod.otcPaymentMethodId = localbankdepositdetails.bankAccountId', 'left')
			->join('playerbankdetails', 'playerbankdetails.playerBankDetailsId = localbankdepositdetails.playerBankDetailsId', 'left')
			->join('playerpromo', 'playerpromo.playerpromoId = walletaccount.playerPromoId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->order_by('walletaccount.dwDateTime', 'desc');

		$this->db->where('walletaccount.dwMethod <', 2);
		$this->db->where('walletaccount.transactionType', $sort_by['transactionType']);
		$this->db->where('walletaccount.dwStatus', $sort_by['dwStatus']);

		if ($sortPlayerLvl != '') {
			$this->db->where('vipsetting.vipSettingId', $sortPlayerLvl);
		}
		if ($sortPaymentMethod != '') {
			$this->db->where('walletaccount.localBankType', $sortPaymentMethod);
		}
		if ($sortDateRangeValueStart != '') {
			$this->db->where('walletaccount.dwDateTime >= ', $sortDateRangeValueStart . ' 00:00:00');
		}
		if ($sortDateRangeValueEnd != '') {
			$this->db->where('walletaccount.dwDateTime <= ', $sortDateRangeValueEnd . ' 23:59:59');
		}

		$this->db->limit($limit, $offset);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get 3rd party approved/declined deposit transaction
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function get3rdPartyDepositApprovedDeclinedTransaction($sort_by, $limit, $offset = 0) {

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
						   player.username AS playerName,
						   playerdetails.firstName,
						   playerdetails.lastName,
						   adminusers.username AS processedByAdmin,
						   playeraccount.currency,
						   playeraccount.totalBalanceAmount,
						   player.playerId,
						   paymentmethod.paymentMethodName,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   paypalpaymentmethoddetails.paypalMerchantAccount,
						   netellerdepositdetails.merchantAccount AS netellerMerchantAccount,
						   promorules.promorulesId,
						   promorules.promoName,
						   playerpromo.playerpromoId,
						   playerpromo.bonusAmount
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId', 'left')
			->join('player', 'player.playerId = playeraccount.playerId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('adminusers', 'adminusers.userId = walletaccount.processedBy', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->join('manualthirdpartydepositdetails', 'manualthirdpartydepositdetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('paymentmethod', 'paymentmethod.paymentMethodId = walletaccount.dwMethod', 'left')
			->join('paypalpaymentmethoddetails', 'paypalpaymentmethoddetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('netellerdepositdetails', 'netellerdepositdetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerpromo', 'playerpromo.playerpromoId = walletaccount.playerPromoId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->order_by('walletaccount.dwDateTime', 'desc');

		$this->db->where('walletaccount.dwMethod >', 1);
		$this->db->where('walletaccount.thirdpartyPaymentMethod', 'auto');
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

		$this->db->limit($limit, $offset);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get 3rd party approved/declined deposit transaction
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function getManual3rdPartyDepositApprovedDeclinedTransaction($sort_by, $limit, $offset = 0) {

		$sortPlayerLvl = '';
		if (isset($sort_by['playerLevel'])) {
			$playerLevelVal = $sort_by['playerLevel'];
			$sortPlayerLvl = $playerLevelVal;
		}

		$sortPaymentMethod = '';
		if (isset($sort_by['paymentmethod'])) {
			$paymentMethodVal = $sort_by['paymentmethod'];
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
						   player.username,
						   playerdetails.firstName,
						   playerdetails.lastName,
						   adminusers.username AS processedByAdmin,
						   playeraccount.currency,
						   playeraccount.totalBalanceAmount,
						   player.playerId,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   manualthirdpartydepositdetails.depositTo,
						   manualthirdpartydepositdetails.depositSlipName,
						   manualthirdpartydepositdetails.transacRefCode,
						   manualthirdpartydepositdetails.depositDateTime,
						   manualthirdpartydepositdetails.depositorName,
						   manualthirdpartydepositdetails.depositorAccount,
						   manualthirdpartydepositdetails.transactionFee,
						   promorules.promorulesId,
						   promorules.promoName,
						   playerpromo.playerpromoId,
						   playerpromo.bonusAmount
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId', 'left')
			->join('player', 'player.playerId = playeraccount.playerId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('adminusers', 'adminusers.userId = walletaccount.processedBy', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->join('manualthirdpartydepositdetails', 'manualthirdpartydepositdetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerpromo', 'playerpromo.playerpromoId = walletaccount.playerPromoId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->order_by('walletaccount.dwDateTime', 'desc');

		$this->db->where('walletaccount.dwMethod >', 1);
		$this->db->where('walletaccount.thirdpartyPaymentMethod', 'manual');
		$this->db->where('walletaccount.transactionType', $sort_by['transactionType']);
		$this->db->where('walletaccount.dwStatus', $sort_by['dwStatus']);

		if ($sortPlayerLvl != '') {
			$this->db->where('vipsetting.vipSettingId', $sortPlayerLvl);
		}
		// if($sortPaymentMethod != ''){
		// 	$this->db->where('walletaccount.dwMethod', $sortPaymentMethod);
		// }
		if ($sortDateRangeValueStart != '') {
			$this->db->where('walletaccount.dwDateTime >= ', $sortDateRangeValueStart . ' 00:00:00');
		}
		if ($sortDateRangeValueEnd != '') {
			$this->db->where('walletaccount.dwDateTime <= ', $sortDateRangeValueEnd . ' 23:59:59');
		}

		$this->db->limit($limit, $offset);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get approved/declined withdrawal transaction ----------------------------------------------------
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function getWithdrawalApprovedDeclinedTransaction($sort_by, $limit, $offset = 0) {

		$sortPlayerLvl = '';
		if (isset($sort_by['playerLevel'])) {
			$playerLevelVal = $sort_by['playerLevel'];
			$sortPlayerLvl = $playerLevelVal;
		}

		$sortPaymentMethod = '';
		if (isset($sort_by['paymentmethod'])) {
			$paymentMethodVal = $sort_by['paymentmethod'];
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
						   player.username AS playerName,
						   adminusers.username AS processedByAdmin,
						   playeraccount.currency,
						   playeraccount.totalBalanceAmount,
						   player.playerId,
						   paymentmethod.paymentMethodName,
						   vipsetting.groupName as vipGroupName,
						   vipsettingcashbackrule.vipLevel AS vipGroupLevel
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId')
			->join('player', 'player.playerId = playeraccount.playerId')
			->join('adminusers', 'adminusers.userId = walletaccount.processedBy')
			->join('paymentmethod', 'paymentmethod.paymentMethodId = walletaccount.dwMethod', 'left')
			->join('playervipgroup', 'playervipgroup.playerId = player.playerId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = playervipgroup.playervipgrouplevelId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = vipsetting.vipSettingId', 'left')
			->order_by('walletaccount.dwDateTime', 'desc');

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

		$this->db->limit($limit, $offset);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get deposit/withdrawal count
	 *
	 * @param $transactionType - int
	 * @param $dwStatus - int
	 * @param $dateRangeValueStart - datetime
	 * @param $dateRangeValueEnd - datetime
	 * @return array
	 */
	public function getDWCount($transactionType, $dwStatus, $dateRangeValueStart = '', $dateRangeValueEnd = '') {
		$this->db->select('transactionType')
            ->from('walletaccount')
            ->join('player', 'player.playerId = walletaccount.playerId', 'left')
            ->where('transactionType', $transactionType)
            ->where('dwStatus', $dwStatus)
            ->where('walletaccount.status', 0);
		if ($dateRangeValueStart != '') {
			$this->db->where('walletaccount.dwDateTime >= ', $dateRangeValueStart . ' 00:00:00');
		}
		if ($dateRangeValueEnd != '') {
			$this->db->where('walletaccount.dwDateTime <= ', $dateRangeValueEnd . ' 23:59:59');
		}
		$this->db->where('walletaccount.dwMethod <', 2);
		$this->db->where('walletaccount.transactionType', $transactionType);
		$this->db->where('walletaccount.dwStatus', $dwStatus)
            ->where('player.deleted_at IS NULL');

		$query = $this->db->get();

		return $query->num_rows();
	}

	/**
	 * Will get deposit/withdrawal count, grouped by status
	 *
	 * @param $transactionType - int
	 * @param $dwStatus - int
	 * @param $dateRangeValueStart - datetime
	 * @param $dateRangeValueEnd - datetime
	 * @return array
	 */
	public function getDWCountAllStatus($transactionType, $dateRangeValueStart = '', $dateRangeValueEnd = '') {
		$this->db->select(array('dwStatus', 'count(walletAccountId) as count'))
            ->from('walletaccount')
            ->join('player', 'player.playerId = walletaccount.playerId', 'left')
            ->where('walletaccount.transactionType', $transactionType)
            ->where('walletaccount.status', 0);
		if ($dateRangeValueStart != '') {
			$this->db->where("(processDatetime = '0000-00-00' AND dwDateTime >= '$dateRangeValueStart 00:00:00' OR processDatetime >= '$dateRangeValueStart 00:00:00')");
		}
		if ($dateRangeValueEnd != '') {
			$this->db->where("(processDatetime = '0000-00-00' AND dwDateTime <= '$dateRangeValueEnd 23:59:59' OR processDatetime <= '$dateRangeValueEnd 23:59:59')");
		}
		$this->db->where('dwMethod <', 2)
            ->where('transactionType', $transactionType)
            ->where('player.deleted_at IS NULL')
            ->group_by('dwStatus');

		$query = $this->db->get();
		$this->utils->debug_log('Payment::getDWCountAllStatus:last sql', $transactionType, $dateRangeValueStart, $dateRangeValueEnd, $this->db->last_query());
		return $query->result_array();
	}

	/**
	 * Will get 3rdParty deposit/withdrawal count
	 *
	 * @param $transactionType - int
	 * @param $dwStatus - int
	 * @param $dateRangeValueStart - datetime
	 * @param $dateRangeValueEnd - datetime
	 * @return array
	 */

	public function get3rdPartyDWCount($transactionType, $dwStatus, $method, $dateRangeValueStart, $dateRangeValueEnd) {
		$this->db->select('transactionType')
			->from('walletaccount')
			->where('transactionType', $transactionType)
			->where('dwStatus', $dwStatus)
			->where('thirdpartyPaymentMethod', $method)
			->where('status', 0);
		if ($dateRangeValueStart != '') {
			$this->db->where('walletaccount.dwDateTime >= ', $dateRangeValueStart . ' 00:00:00');
		}
		if ($dateRangeValueEnd != '') {
			$this->db->where('walletaccount.dwDateTime <= ', $dateRangeValueEnd . ' 23:59:59');
		}
		$this->db->where('walletaccount.dwMethod >', 1);
		$this->db->where('walletaccount.transactionType', $transactionType);
		$this->db->where('walletaccount.dwStatus', $dwStatus);

		$query = $this->db->get();
		return $query->num_rows();
	}

	/**
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function getDepositWithdrawalTransactionDetail($walletAccountId, $paymentMethodId) {
		//echo $walletAccountId,$paymentMethodId;
		$this->db->select('walletaccount.*,
						   SUM(walletaccount.amount) AS playerTotalBalanceAmount,
						   player.email,
						   player.createdOn,
						   player.username AS playerName,
						   player.playerId,
						   playerdetails.*,
						   playeraccount.totalBalanceAmount AS currentBalAmount,
						   playeraccount.currency AS currentBalCurrency,
						   playerpromo.playerId AS promoPlayerId,
						   playerpromo.promoStatus AS playerPromoStatus,
						   playerpromo.bonusAmount AS playerPromoBonusAmount,
						   paymentmethod.paymentMethodId,
						   paymentmethod.paymentMethodName,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   localbankdepositdetails.depositSlipName,
						   localbankdepositdetails.transacRefCode,
						   otcpaymentmethod.bankName AS depositedToBankName,
						   otcpaymentmethod.accountNumber AS depositedToAcctNo,
						   otcpaymentmethod.branchName AS depositedToBranchName,
						   otcpaymentmethod.accountName AS depositedToAcctName,
						   otcpaymentmethod.transactionFee,
						   playerbankdetails.bankAccountFullName,
						   playerbankdetails.bankAccountNumber,
						   playerbankdetails.branch,
						   banktype.bankName,
						   promorules.promorulesId,
						   promorules.promoName,
						   playerpromo.playerpromoId
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId')
			->join('player', 'player.playerId = playeraccount.playerId')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('paymentmethod', 'paymentmethod.paymentMethodId = walletaccount.dwMethod', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->join('localbankdepositdetails', 'localbankdepositdetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('otcpaymentmethod', 'otcpaymentmethod.otcPaymentMethodId = localbankdepositdetails.bankAccountId', 'left')
			->join('playerbankdetails', 'playerbankdetails.playerBankDetailsId = localbankdepositdetails.playerBankDetailsId', 'left')
			->join('playerpromo', 'playerpromo.playerpromoId = walletaccount.playerPromoId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->join('banktype', 'banktype.bankTypeId = playerbankdetails.bankTypeId', 'left');

		$this->db->where('walletaccount.walletAccountId', $walletAccountId);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['dwDateTime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dwDateTime']));
				$row['birthdate'] = mdate('%M %d, %Y', strtotime($row['birthdate']));
				$row['createdOn'] = mdate('%M %d, %Y', strtotime($row['createdOn']));
				$row['processDatetime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['processDatetime']));
				$row['playerName'] = ucwords($row['playerName']);
				//$row['promoStartTimestamp'] = mdate('%M %d, %Y',strtotime($row['promoStartTimestamp']));
				//$row['promoEndTimestamp'] = mdate('%M %d, %Y',strtotime($row['promoEndTimestamp']));
				//$row['transacHistory'] = $this->getPlayerTransactionHistory($row['playerAccountId']);
				$row['paymentmethoddetails'] = $this->getPaymentMethodDetails($row['paymentMethodId'], $row['walletAccountId']);
				$row['playerActivePromo'] = $this->getPlayerActivePromo($row['playerId']);
				$row['depositCnt'] = $this->getPlayerTotalDepositCnt($row['playerId']);
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get player balance
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function getPlayerActivePromo($playerId) {
		$this->db->select('playerpromo.dateProcessed as dateJoined,
						   playerpromo.bonusAmount,
						   promorules.promoName,
						   promorules.promoCode,
						   promorules.promorulesId
						   ')
			->from('playerpromo')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left');
		$this->db->where('playerpromo.promoStatus', 0);
		$this->db->where('playerpromo.transactionStatus', 1);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['dateJoined'] = mdate('%M %d, %Y', strtotime($row['dateJoined']));
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	public function getPlayerTotalDepositCnt($playerId) {
		$this->db->select('dwCount')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId')
			->join('player', 'player.playerId = playeraccount.playerId');
		$this->db->where('player.playerId', $playerId);
		$this->db->order_by('walletaccount.dwCount', 'desc');
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * Will get request withdrawal transaction
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
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
			->join('localbankwithdrawaldetails', 'localbankwithdrawaldetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerbankdetails', 'playerbankdetails.playerBankDetailsId = localbankwithdrawaldetails.playerBankDetailsId', 'left')
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
				$row['subwalletBalanceAmountAG'] = $this->getPlayerSubWalletBalanceAG($row['playerId']);
				$row['cashbackwalletBalanceAmount'] = $this->getPlayerCashbackWalletBalance($row['playerId']);
				//$row['playerPromoActive'] = $this->getPlayerPromoActive($row['playerId']);
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	// public function getManualThirdPartyDepositWithdrawalTransactionDetail($walletAccountId, $paymentMethodId) {
		//echo $walletAccountId,$paymentMethodId;
		// $this->db->select('walletaccount.*,
		// 				   SUM(walletaccount.amount + playerpromo.bonusAmount) AS playerTotalBalanceAmount,
		// 				   player.email,
		// 				   player.createdOn,
		// 				   player.username AS playerName,
		// 				   playerdetails.*,
		// 				   playeraccount.totalBalanceAmount AS currentBalAmount,
		// 				   playeraccount.currency AS currentBalCurrency,
		// 				   playerpromo.playerPromoId,
		// 				   playerpromo.playerId AS promoPlayerId,
		// 				   playerpromo.status AS playerPromoStatus,
		// 				   playerpromo.bonusAmount AS playerPromoBonusAmount,
		// 				   mkt_promo.*,
		// 				   mkt_currency.currencyCode AS promoCurrency,
		// 				   paymentmethod.paymentMethodId,
		// 				   paymentmethod.paymentMethodName,
		// 				   vipsettingcashbackrule.vipLevel,
		// 				   vipsettingcashbackrule.vipLevelName,
		// 				   vipsetting.groupName
		// 				   ')
		// 	->from('walletaccount')
		// 	->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId')
		// 	->join('player', 'player.playerId = playeraccount.playerId')
		// 	->join('playerpromo', 'playerpromo.playerPromoId = walletaccount.playerPromoId', 'left')
		// 	->join('mkt_promo', 'mkt_promo.promoId = playerpromo.playerpromoId', 'left')
		// 	->join('mkt_promocurrency', 'mkt_promocurrency.promoId = mkt_promo.promoId', 'left')
		// 	->join('mkt_currency', 'mkt_promocurrency.currencyId = mkt_currency.currencyId', 'left')
		// 	->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
		// 	->join('paymentmethod', 'paymentmethod.paymentMethodId = walletaccount.dwMethod', 'left')
		// 	->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
		// 	->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
		// 	->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');

		// $this->db->where('walletaccount.walletAccountId', $walletAccountId);

		// $query = $this->db->get();

		// if ($query->num_rows() > 0) {
		// 	foreach ($query->result_array() as $row) {
		// 		$row['dwDateTime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dwDateTime']));
		// 		$row['birthdate'] = mdate('%M %d, %Y', strtotime($row['birthdate']));
		// 		$row['createdOn'] = mdate('%M %d, %Y', strtotime($row['createdOn']));
		// 		$row['processDatetime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['processDatetime']));
		// 		$row['playerName'] = ucwords($row['playerName']);
		// 		$row['promoStartTimestamp'] = mdate('%M %d, %Y', strtotime($row['promoStartTimestamp']));
		// 		$row['promoEndTimestamp'] = mdate('%M %d, %Y', strtotime($row['promoEndTimestamp']));
		// 		$row['transacHistory'] = $this->getPlayerTransactionHistory($row['playerAccountId']);
		// 		$row['paymentmethoddetails'] = $this->getPaymentMethodDetails($row['paymentMethodId'], $row['walletAccountId']);
		// 		$data[] = $row;
		// 	}
		// 	//var_dump($data);exit();
		// 	return $data;
		// }
		// return false;
	// }

	/**
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */
	public function getWithdrawalTransactionDetail($walletAccountId) {
		$this->db->select('walletaccount.*,
						   player.email,
						   player.createdOn,
						   player.username AS playerName,
						   playerdetails.*,
						   playeraccount.totalBalanceAmount AS currentBalAmount,
						   playeraccount.currency AS currentBalCurrency,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   banktype.bankTypeId,
						   banktype.bankName,
						   playerbankdetails.bankAccountFullName,
						   playerbankdetails.bankAccountNumber,
						   playerbankdetails.branch,
						   playerbankdetails.phone as bankPhone,
						   playerbankdetails.bankAddress,
						   adminusers.username as processedByAdmin
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId')
			->join('player', 'player.playerId = playeraccount.playerId')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->join('localbankwithdrawaldetails', 'localbankwithdrawaldetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerbankdetails', 'playerbankdetails.playerBankDetailsId = localbankwithdrawaldetails.playerBankDetailsId', 'left')
			->join('adminusers', 'adminusers.userId = walletaccount.processedBy', 'left')
			->join('banktype', 'banktype.bankTypeId = playerbankdetails.bankTypeId', 'left');

		$this->db->where('walletaccount.walletAccountId', $walletAccountId);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				//format number
				$row['totalBalance'] = $row['currentBalAmount'];
				$row['currentBalAmount'] = $this->utils->formatCurrency($row['currentBalAmount']);
				$row['bankName'] = lang($row['bankName']);
				$row['firstName'] = $row['firstName'] ? ucwords($row['firstName']) : '';
				$row['lastName'] = $row['lastName'] ? ucwords($row['lastName']) : '';
				$row['dwDateTime'] = $row['dwDateTime'];
				$row['createdOn'] = $row['createdOn'];
				$row['processDatetime'] = $row['processDatetime'];
				$row['playerName'] = ucwords($row['playerName']);
				$row['subwalletBalanceAmount'] = $this->getSubWalletBalance($row['playerId']);
				foreach ($row['subwalletBalanceAmount'] as &$subwallet) {
					$row['totalBalance'] += $subwallet['totalBalanceAmount'];
					$subwallet['totalBalanceAmount'] = $this->utils->formatCurrency($subwallet['totalBalanceAmount']);
				}
				$row['totalBalance'] = $this->utils->formatCurrency($row['totalBalance']);
				$row['cashbackwalletBalanceAmount'] = $this->getPlayerCashbackWalletBalance($row['playerId']);
				$row['withdrawCondition'] = $this->getPlayerWithdrawalCondition($row['playerId']);
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * MOVED TO withdraw_condition
	 * Will get player withdrawal condition
	 *
	 * @param 	playerId int
	 * @return	array
	 */
	public function getPlayerWithdrawalCondition($playerId) {
		$this->db->distinct()->select('promorules.promorulesId,
						   promorules.promoName,
						   promorules.promoCode,
						   promorules.promoType,
						   promorules.nonDepositPromoType,
						   promorules.depositConditionDepositAmount,
						   promorules.depositConditionType,
						   promorules.nonfixedDepositMinAmount,
						   promorules.nonfixedDepositMaxAmount,
						   promorules.depositConditionNonFixedDepositAmount,
						   promorules.nonfixedDepositAmtCondition,
						   promorules.nonfixedDepositAmtConditionRequiredDepositAmount,
						   promorules.bonusReleaseRule,
						   promorules.bonusAmount as promorulesBonusAmount,
						   promorules.depositPercentage,
						   promorules.maxBonusAmount,
						   promorules.withdrawRequirementRule,
						   promorules.withdrawRequirementBetAmount,
						   promorules.withdrawRequirementBetCntCondition,
						   promorules.withdrawRequirementConditionType,
						   promorules.promoType,
						   player.username,
						   player.playerId,
						   playerpromo.playerpromoId,
						   playerpromo.bonusAmount,
						   playerpromo.withdrawalStatus,
						   playerpromo.promoStatus,
						   withdraw_conditions.id as withdrawConditionId,
						   withdraw_conditions.condition_amount as conditionAmount,
						   withdraw_conditions.started_at,
						   withdraw_conditions.source_type,
						   promotype.promoTypeName,
						   promotype.promoTypeDesc,
						   transactions.promo_category,
						   transactions.amount as walletDepositAmount')
			->from('withdraw_conditions')
			->join('promorules', 'promorules.promorulesId = withdraw_conditions.promotion_id', 'left')
			->join('sale_orders', 'sale_orders.wallet_id = withdraw_conditions.source_id', 'left')
			->join('transactions', 'transactions.id = withdraw_conditions.source_id', 'left')
			->join('promotype', 'promotype.promotypeId = transactions.promo_category', 'left')
			->join('playerpromo', 'playerpromo.playerpromoId = sale_orders.player_promo_id', 'left')
			->join('player', 'player.playerId = withdraw_conditions.player_id', 'left');
		$this->db->where('withdraw_conditions.player_id', $playerId);
		$this->db->where('withdraw_conditions.status', 1); //1-active
		$this->db->order_by("withdraw_conditions.started_at", "desc");

		$query = $this->db->get();

		// $this->utils->printLastSQL();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				//check if deposit promo and non deposit promo (email,registration,mobile)
				if (($row['promoType'] == 1 && $row['nonDepositPromoType'] < 4) || ($row['promoType'] == 0)) {
					$row['currentBet'] = $this->getPlayerCurrentBet($row['username'], $row['started_at'], $row['promorulesId'], $row['playerId']);
				} else {
					$row['currentBet'] = $this->getPlayerCurrentBet($row['username'], $row['started_at'], null, $row['playerId']);
				}

				$row['unfinished'] = $row['conditionAmount'] - $row['currentBet'][0]['totalBetAmount'];

				// $row['started_at'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['started_at']));
				$row['promoName'] == null ? $row['promoName'] = '' : $row['promoName'];
				$row['promoCode'] == null ? $row['promoCode'] = '' : $row['promoCode'];
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get player promo exist and active
	 *
	 * @param 	playerId int
	 * @return	array
	 */
	public function getPlayerPromoActive($playerId) {
		$this->db->select('promorules.promorulesId,
						   promorules.promoName,
						   promorules.promoCode,
						   player.username,
						   player.playerId,
						   playerpromo.playerpromoId,
						   playerpromo.bonusAmount,
						   playerpromo.withdrawalStatus,
						   playerpromo.dateProcessed as dateJoined,
						   playerpromo.promoStatus')
			->from('playerpromo')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->join('player', 'player.playerId = playerpromo.playerId', 'left');
		$this->db->where('playerpromo.playerId', $playerId);
		$this->db->where('playerpromo.promoStatus', 0); //0-active
		$this->db->where('playerpromo.transactionStatus', 1); //approved status

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['currentBet'] = $this->getPlayerCurrentBet($row['username'], $row['dateJoined'], null, $row['playerId']);
				$row['dateJoined'] = mdate('%M %d, %Y', strtotime($row['dateJoined']));
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * MOVED TO game_logs
	 *
	 * @param 	playerName str
	 * @param 	dateJoined datetime
	 * @return	array
	 */
	public function getPlayerCurrentBet($playerName, $dateJoined, $promoId = null, $playerId = null) {

		$playerGames = null;
		if ($promoId) {
			$playerGames = $this->getPlayerGames($promoId);
		}

		$this->db->select('sum(bet_amount) as totalBetAmount')
			->from('game_logs')
			->where('end_at >=', $dateJoined)
			->where('end_at <=', $this->utils->getNowForMysql())
			->where('player_id', $playerId)
		;
		if ($promoId) {
			// $playerGames = $this->getPlayerGames($promoId);
			$this->db->where_in('game_description_id', $playerGames);
		}

		$qry = $this->db->get();
		$rows = array(array('totalBetAmount' => 0));
		if ($qry->num_rows() > 0) {
			$rows = $qry->result_array();
		}

		return $rows;

		// $date_joined = date("Y-m-d", strtotime($dateJoined));
		// $date_now = date("Y-m-d");
		// $date_time = date('Y-m-d H:i:s');

// 		if ($promoId) {
		// 			$playerGames = $this->getPlayerGames($promoId);
		// 			$player_games = implode(',', $playerGames);
		// 			/*$qry = $this->db->query("SELECT SUM(betting_amount) as totalBetAmount
		// 			FROM total_player_game_day
		// 			WHERE date >= '" . date("Y-m-d", strtotime($dateJoined)) . "'
		// 			AND date <= '" . date('Y-m-d') . "'
		// 			AND player_id = '" . $playerId . "'
		// 			AND game_description_id IN (" . implode(',', $playerGames) . ")");*/

// 			$qry = <<<EOD
		// SELECT SUM(betting_amount) as totalBetAmount
		// 	FROM total_player_game_day
		// 	WHERE date >= '$date_joined'
		// 	AND date <= '$date_now'
		// 	AND player_id = ?
		// 	AND game_description_id IN ($player_games)
		// EOD;

// 			$qry = $this->db->query($qry, array($playerId));

// 			if ($qry && $qry->num_rows() > 0) {
		// 				return $qry->result();
		// 			}
		// 		} else {
		// 			$this->db->select('SUM(bet) as totalBetAmount')->from('gameapirecord');
		// 			$this->db->where('gamedate >=', $dateJoined);
		// 			$this->db->where('gamedate <=', date('Y-m-d H:i:s'));
		// 			$this->db->where('playername', $playerName);
		// 			$query = $this->db->get();
		// 			$dateWithHour = date('Y-m-dH');

// 			$qry = <<<EOD
		// SELECT SUM(betting_amount) as totalBetAmount
		// 	FROM total_player_game_hour
		// 	WHERE date >= '$date_joined'
		// 	AND date <= '$date_time'
		// 	AND player_id = '$playerId'
		// EOD;

// 			$query = $this->db->query($qry, array());

// 			if ($query->num_rows() > 0) {
		// 				foreach ($query->result_array() as $row) {
		// 					$row['totalBetAmount'] == null ? $row['totalBetAmount'] = 0 : $row['totalBetAmount'] = $row['totalBetAmount'];
		// 					$data[] = $row;
		// 				}
		// 			}

// 			// log_message('error', var_export($data, true));
		// 			return $data;
		// }

	}

	// private function getPlayerGames($promoId) {
	// 	$this->db->select('game_description_id')->from('promorulesgamebetrule');
	// 	$this->db->where('promoruleId', $promoId);
	// 	$qry = $this->db->get();
	// 	if ($qry && $qry->num_rows() > 0) {
	// 		foreach ($qry->result_array() as $row) {
	// 			$data[] = $row['game_description_id'];
	// 		}
	// 		return $data;
	// 	}

	// 	return false;
	// }

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
			foreach ($query->result_array() as $row) {
				if (isset($row['transactionDatetime'])) {
					$row['transactionDatetime'] = mdate('%M %d, %Y', strtotime($row['transactionDatetime']));
				}
				$data[] = $row;
			}

			return $data;
		}
		return false;
	}

	/**
	 * Will get player promo status
	 *
	 * @param $playerId - int
	 * @return Boolean
	 */

	public function getPlayerPromoStatus($status) {
		switch ($status) {
		case 0:
			return 'active';
			break;
		case 1:
			return 'expired';
			break;
		case 2:
			return 'cancelled';
			break;
		default:
			# code...
			break;
		}
	}

	/**
	 * Will get deposit approved details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function getDepositApprovedTransactionDetail($walletAccountId) {
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
						   playerpromo.bonusAmount AS playerPromoBonusAmount,
						   paymentmethod.paymentMethodId,
						   paymentmethod.paymentMethodName,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   localbankdepositdetails.depositSlipName,
						   localbankdepositdetails.transacRefCode,
						   otcpaymentmethod.bankName AS depositedToBankName,
						   otcpaymentmethod.accountNumber AS depositedToAcctNo,
						   otcpaymentmethod.branchName AS depositedToBranchName,
						   otcpaymentmethod.accountName AS depositedToAcctName,
						   otcpaymentmethod.transactionFee,
						   playerbankdetails.bankAccountFullName,
						   playerbankdetails.bankAccountNumber,
						   playerbankdetails.branch,
						   banktype.bankName,
						   promorules.promorulesId,
						   promorules.promoName,
						   playerpromo.playerpromoId,
						   playerpromo.bonusAmount
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId', 'left')
			->join('player', 'player.playerId = playeraccount.playerId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('playerpromo', 'playerpromo.playerPromoId = walletaccount.playerPromoId', 'left')
			->join('adminusers', 'adminusers.userId = walletaccount.processedBy', 'left')
			->join('paymentmethod', 'paymentmethod.paymentMethodId = walletaccount.dwMethod', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->join('localbankdepositdetails', 'localbankdepositdetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('otcpaymentmethod', 'otcpaymentmethod.otcPaymentMethodId = localbankdepositdetails.bankAccountId', 'left')
			->join('playerbankdetails', 'playerbankdetails.playerBankDetailsId = localbankdepositdetails.playerBankDetailsId', 'left')
			->join('banktype', 'banktype.bankTypeId = playerbankdetails.bankTypeId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->order_by('walletaccount.dwDateTime', 'desc');

		$this->db->where('walletaccount.walletAccountId', $walletAccountId);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['firstName'] = $row['firstName'] ? ucwords($row['firstName']) : '';
				$row['lastName'] = $row['lastName'] ? ucwords($row['lastName']) : '';

				$row['dwDateTime'] = mdate('%M %d, %Y %H:%i:%s', strtotime($row['dwDateTime']));
				$row['birthdate'] = mdate('%M %d, %Y', strtotime($row['birthdate']));
				$row['createdOn'] = mdate('%M %d, %Y', strtotime($row['createdOn']));
				$row['processDatetime'] = mdate('%M %d, %Y %H:%i:%s', strtotime($row['processDatetime']));
				$row['playerName'] = ucwords($row['playerName']);
				//$row['promoStartTimestamp'] = mdate('%M %d, %Y',strtotime($row['promoStartTimestamp']));
				//$row['promoEndTimestamp'] = mdate('%M %d, %Y',strtotime($row['promoEndTimestamp']));
				//$row['transacHistory'] = $this->getPlayerTransactionHistory($row['playerAccountId']);
				$row['paymentmethoddetails'] = $this->getPaymentMethodDetails($row['paymentMethodId'], $row['walletAccountId']);
				$row['compensationfeedetails'] = $this->getTransactionFeeDetails($row['walletAccountId']);
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get transaction fee details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function getTransactionFeeDetails($walletAccountId) {
		$this->db->select('*')->from('bankcompensationfeehistory');
		$this->db->where('bankcompensationfeehistory.walletAccountId', $walletAccountId);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get deposit approved details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function getManualThirdPartyDepositApprovedTransactionDetail($walletAccountId) {
		$this->db->select('walletaccount.*,
						   player.email,
						   player.createdOn,
						   player.username AS playerName,
						   playeraccount.currency,
						   player.playerId,
						   playerdetails.*,
						   playeraccount.totalBalanceAmount AS currentBalAmount,
						   playeraccount.currency AS currentBalCurrency,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   manualthirdpartydepositdetails.depositTo AS paymentMethodName,
						   manualthirdpartydepositdetails.depositSlipName,
						   manualthirdpartydepositdetails.transacRefCode,
						   manualthirdpartydepositdetails.depositDateTime,
						   manualthirdpartydepositdetails.depositorName,
						   manualthirdpartydepositdetails.depositorAccount,
						   manualthirdpartydepositdetails.transactionFee,
						   adminusers.username as processedByAdmin,
						   promorules.promorulesId,
						   promorules.promoName,
						   playerpromo.playerpromoId,
						   playerpromo.bonusAmount
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId', 'left')
			->join('player', 'player.playerId = playeraccount.playerId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->join('manualthirdpartydepositdetails', 'manualthirdpartydepositdetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerpromo', 'playerpromo.playerpromoId = walletaccount.playerPromoId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->join('adminusers', 'adminusers.userId = walletaccount.processedBy', 'left')
			->order_by('walletaccount.dwDateTime', 'desc');

		$this->db->where('walletaccount.walletAccountId', $walletAccountId);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['firstName'] = $row['firstName'] ? ucwords($row['firstName']) : '';
				$row['lastName'] = $row['lastName'] ? ucwords($row['lastName']) : '';

				$row['dwDateTime'] = mdate('%M %d, %Y %H:%i:%s', strtotime($row['dwDateTime']));
				$row['birthdate'] = mdate('%M %d, %Y', strtotime($row['birthdate']));
				$row['createdOn'] = mdate('%M %d, %Y', strtotime($row['createdOn']));
				$row['processDatetime'] = mdate('%M %d, %Y %H:%i:%s', strtotime($row['processDatetime']));
				$row['playerName'] = ucwords($row['playerName']);
				$row['transacHistory'] = $this->getPlayerTransactionHistory($row['playerAccountId']);
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get deposit approved details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function getManualThirdPartyDepositDeclinedTransactionDetail($walletAccountId) {
		$this->db->select('walletaccount.*,
						   player.email,
						   player.createdOn,
						   player.username AS playerName,
						   playeraccount.currency,
						   player.playerId,
						   playerdetails.*,
						   playeraccount.totalBalanceAmount AS currentBalAmount,
						   playeraccount.currency AS currentBalCurrency,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   manualthirdpartydepositdetails.depositTo AS paymentMethodName,
						   manualthirdpartydepositdetails.depositSlipName,
						   manualthirdpartydepositdetails.transacRefCode,
						   manualthirdpartydepositdetails.depositDateTime,
						   manualthirdpartydepositdetails.depositorName,
						   manualthirdpartydepositdetails.depositorAccount,
						   manualthirdpartydepositdetails.transactionFee,
						   adminusers.username as processedByAdmin,
						   promorules.promorulesId,
						   promorules.promoName,
						   playerpromo.playerpromoId,
						   playerpromo.bonusAmount
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId', 'left')
			->join('player', 'player.playerId = playeraccount.playerId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->join('manualthirdpartydepositdetails', 'manualthirdpartydepositdetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('adminusers', 'adminusers.userId = walletaccount.processedBy', 'left')
			->join('playerpromo', 'playerpromo.playerpromoId = walletaccount.playerPromoId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->order_by('walletaccount.dwDateTime', 'desc');

		$this->db->where('walletaccount.walletAccountId', $walletAccountId);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['firstName'] = $row['firstName'] ? ucwords($row['firstName']) : '';
				$row['lastName'] = $row['lastName'] ? ucwords($row['lastName']) : '';

				$row['dwDateTime'] = mdate('%M %d, %Y %H:%i:%s', strtotime($row['dwDateTime']));
				$row['birthdate'] = mdate('%M %d, %Y', strtotime($row['birthdate']));
				$row['createdOn'] = mdate('%M %d, %Y', strtotime($row['createdOn']));
				$row['processDatetime'] = mdate('%M %d, %Y %H:%i:%s', strtotime($row['processDatetime']));
				$row['playerName'] = ucwords($row['playerName']);
				$row['transacHistory'] = $this->getPlayerTransactionHistory($row['playerAccountId']);
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get deposit approved details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

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
						   banktype.bankName,
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
			->join('localbankwithdrawaldetails', 'localbankwithdrawaldetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerbankdetails', 'playerbankdetails.playerBankDetailsId = localbankwithdrawaldetails.playerBankDetailsId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->join('banktype', 'banktype.bankTypeId = playerbankdetails.bankTypeId', 'left');

		$this->db->where('walletaccount.walletAccountId', $walletAccountId);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['firstName'] = $row['firstName'] ? ucwords($row['firstName']) : '';
				$row['lastName'] = $row['lastName'] ? ucwords($row['lastName']) : '';
				$row['dwDateTime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dwDateTime']));
				$row['createdOn'] = mdate('%M %d, %Y', strtotime($row['createdOn']));
				$row['processDatetime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['processDatetime']));
				$row['playerName'] = ucwords($row['playerName']);
				$row['paymentmethoddetails'] = $this->getPaymentMethodDetails($row['paymentMethodId'], $row['walletAccountId']);
				$row['subwalletBalanceAmount'] = $this->getSubWalletBalance($row['playerId']);
				$row['totalBalance']=$row['currentBalAmount'];
				foreach ($row['subwalletBalanceAmount'] as &$subwallet) {
					$row['totalBalance'] += $subwallet['totalBalanceAmount'];
					$subwallet['totalBalanceAmount'] = $this->utils->formatCurrency($subwallet['totalBalanceAmount']);
				}
				$row['totalBalance'] = $this->utils->formatCurrency($row['totalBalance']);

				// $row['subwalletBalanceAmount'] = $this->getPlayerSubWalletBalance($row['playerId']);
				// $row['subwalletBalanceAmountAG'] = $this->getPlayerSubWalletBalanceAG($row['playerId']);
				$row['cashbackwalletBalanceAmount'] = $this->getPlayerCashbackWalletBalance($row['playerId']);
				$row['playerPromoActive'] = $this->getPlayerPromoActive($row['playerId']);
				$row['bankName'] = lang("bank_type".$row['bankTypeId']);//lang get bank type
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get deposit declined details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function getDepositDeclinedTransactionDetail($walletAccountId) {
		$this->db->select('walletaccount.*,
						   SUM(walletaccount.amount) AS playerTotalBalanceAmount,
						   player.email,
						   player.createdOn,
						   player.username AS playerName,
						   adminusers.username AS processedByAdmin,
						   playerdetails.*,
						   playeraccount.totalBalanceAmount AS currentBalAmount,
						   playeraccount.currency AS currentBalCurrency,
						   paymentmethod.paymentMethodId,
						   paymentmethod.paymentMethodName,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   localbankdepositdetails.depositSlipName,
						   localbankdepositdetails.transacRefCode,
						   netellerdepositdetails.merchantAccount AS netellerMerchantAccount,
						   otcpaymentmethod.bankName AS depositedToBankName,
						   otcpaymentmethod.accountNumber AS depositedToAcctNo,
						   otcpaymentmethod.branchName AS depositedToBranchName,
						   otcpaymentmethod.accountName AS depositedToAcctName,
						   otcpaymentmethod.transactionFee,
						   playerbankdetails.bankAccountFullName,
						   playerbankdetails.bankAccountNumber,
						   playerbankdetails.branch,
						   banktype.bankName,
						   promorules.promorulesId,
						   promorules.promoName,
						   playerpromo.playerpromoId,
						   playerpromo.bonusAmount
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId')
			->join('player', 'player.playerId = playeraccount.playerId')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('adminusers', 'adminusers.userId = walletaccount.processedBy', 'left')
			->join('paymentmethod', 'paymentmethod.paymentMethodId = walletaccount.dwMethod', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->join('localbankdepositdetails', 'localbankdepositdetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('netellerdepositdetails', 'netellerdepositdetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('otcpaymentmethod', 'otcpaymentmethod.otcPaymentMethodId = localbankdepositdetails.bankAccountId', 'left')
			->join('playerbankdetails', 'playerbankdetails.playerBankDetailsId = localbankdepositdetails.playerBankDetailsId', 'left')
			->join('playerpromo', 'playerpromo.playerpromoId = walletaccount.playerPromoId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->join('banktype', 'banktype.bankTypeId = playerbankdetails.bankTypeId', 'left');

		$this->db->where('walletaccount.walletAccountId', $walletAccountId);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['firstName'] = $row['firstName'] ? ucwords($row['firstName']) : '';
				$row['lastName'] = $row['lastName'] ? ucwords($row['lastName']) : '';
				$row['dwDateTime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dwDateTime']));
				$row['createdOn'] = mdate('%M %d, %Y', strtotime($row['createdOn']));
				$row['birthdate'] = mdate('%M %d, %Y', strtotime($row['birthdate']));
				$row['processDatetime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['processDatetime']));
				$row['playerName'] = ucwords($row['playerName']);
				//$row['transacHistory'] = $this->getPlayerTransactionHistory($row['playerAccountId']);
				$row['paymentmethoddetails'] = $this->getPaymentMethodDetails($row['paymentMethodId'], $row['walletAccountId']);
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get deposit declined details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function reviewAuto3rdPartyDepositDeclined($walletAccountId) {
		$this->db->select('walletaccount.*,
						   player.createdOn,
						   player.username AS playerName,
						   adminusers.username AS processedByAdmin,
						   playerdetails.firstName,
						   playerdetails.lastName,
						   playeraccount.totalBalanceAmount AS currentBalAmount,
						   playeraccount.currency AS currentBalCurrency,
						   playerpromo.playerPromoId,
						   playerpromo.playerId AS promoPlayerId,
						   playerpromo.status AS playerPromoStatus,
						   playerpromo.bonusAmount AS playerPromoBonusAmount,
						   paymentmethod.paymentMethodId,
						   paymentmethod.paymentMethodName,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   netellerdepositdetails.merchantAccount AS netellerMerchantAccount,
						   promorules.promorulesId,
						   promorules.promoName,
						   playerpromo.bonusAmount,
						   paypalpaymentmethoddetails.paypalMerchantAccount,
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId')
			->join('player', 'player.playerId = playeraccount.playerId')
			->join('playerpromo', 'playerpromo.playerPromoId = walletaccount.playerPromoId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('adminusers', 'adminusers.userId = walletaccount.processedBy', 'left')
			->join('paymentmethod', 'paymentmethod.paymentMethodId = walletaccount.dwMethod', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->join('paypalpaymentmethoddetails', 'paypalpaymentmethoddetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('netellerdepositdetails', 'netellerdepositdetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerpromo', 'playerpromo.playerpromoId = walletaccount.playerPromoId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left');

		$this->db->where('walletaccount.walletAccountId', $walletAccountId);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['firstName'] = $row['firstName'] ? ucwords($row['firstName']) : '';
				$row['lastName'] = $row['lastName'] ? ucwords($row['lastName']) : '';
				$row['dwDateTime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dwDateTime']));
				$row['createdOn'] = mdate('%M %d, %Y', strtotime($row['createdOn']));
				$row['processDatetime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['processDatetime']));
				$row['playerName'] = ucwords($row['playerName']);
				$row['paymentmethoddetails'] = $this->getPaymentMethodDetails($row['paymentMethodId'], $row['walletAccountId']);
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
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
						   promorules.bonusAmount,
						   banktype.bankName,
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
			->join('localbankwithdrawaldetails', 'localbankwithdrawaldetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerbankdetails', 'playerbankdetails.playerBankDetailsId = localbankwithdrawaldetails.playerBankDetailsId', 'left')
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
				$row['totalBalance']=$row['currentBalAmount'];
				foreach ($row['subwalletBalanceAmount'] as &$subwallet) {
					$row['totalBalance'] += $subwallet['totalBalanceAmount'];
					$subwallet['totalBalanceAmount'] = $this->utils->formatCurrency($subwallet['totalBalanceAmount']);
				}
				$row['totalBalance'] = $this->utils->formatCurrency($row['totalBalance']);

				$row['cashbackwalletBalanceAmount'] = $this->getPlayerCashbackWalletBalance($row['playerId']);
				$row['playerPromoActive'] = $this->getPlayerPromoActive($row['playerId']);
				$row['bankName'] = lang($row['bankName']);
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will approve/decline deposit request
	 *
	 * @param $dataRequest - array
	 * @param $dataPlayer - array
	 * @return Bool - TRUE or FALSE
	 */

	public function approveDeclinedDepositRequest($dataRequest, $dataPlayer = array()) {
		//get playerId by walletAccountId
		$this->db->where('walletAccountId', $dataRequest['walletAccountId']);
		$this->db->update('walletaccount', $dataRequest);

		if ($this->db->affected_rows() == '1') {
			$sql = "SELECT p.playerId, p.frozen,w.amount
                        FROM player p
                        LEFT JOIN playeraccount pc
                        ON p.playerId = pc.playerId
                        LEFT JOIN walletaccount w
                        ON w.playerAccountId = pc.playerAccountId
                        WHERE w.walletAccountId = ?
                        AND pc.type ='wallet'
                        LIMIT 1
                        ";
			$query = $this->db->query($sql, array($dataRequest['walletAccountId']));
			$result = $query->result_array();
			if ($result) {
				//update player  frozen
				$record = $result[0];
				$frozen = $record['frozen'];
				$newFrozen = $frozen - $record['amount'];
				$newFrozen = ($newFrozen) ? $newFrozen : 0;
				$player_data = array(
					'frozen' => $newFrozen,
				);
				$this->db->where('playerId', $record['playerId']);

				$this->db->update('player', $player_data);

				$this->updatePlayerMainWalletBalance($record['playerId'], $record['amount']);
			}

			return TRUE;
		}

		return FALSE;
	}

	public function updatePlayerMainWalletBalance($playerId = 54, $frozenAmount = 100) {
		$query = $this->db->select('totalBalanceAmount')->from('playeraccount');
		$this->db->where('type', 'wallet');
		$this->db->where('playerId', $playerId);
		$query = $this->db->get();
		$currentMainWalletBalance = $query->row_array()['totalBalanceAmount'];

		$newMainWalletBalance = $currentMainWalletBalance + $frozenAmount;

		$player_data = array(
			'totalBalanceAmount' => $newMainWalletBalance,
		);
		$this->db->where('type', 'wallet');
		$this->db->where('playerId', $playerId);
		$this->db->update('playeraccount', $player_data);
		return $query->result_array();
	}

	/**
	 * Will approve deposit request
	 *
	 * @param $dataRequest - array
	 * @return Bool - TRUE or FALSE
	 */

	public function approveDepositRequest($dataRequest) {
		$this->db->where('walletAccountId', $dataRequest['walletAccountId']);
		$this->db->update('walletaccount', $dataRequest);

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Will get ranking settings
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function getRankingSettings($limit, $offset = 0) {
		$this->db->select('rankinglevelsetting.*, concat(rankinglevelsetting.rankingLevelGroup,\' \',rankinglevelsetting.rankingLevel) as rankinglevelname,
						   adminusers.username AS setByName', false)
			->join('adminusers', 'adminusers.userId = rankinglevelsetting.setBy')
			->from('rankinglevelsetting')->where('rankinglevelsetting.status', 0)
			->order_by('rankinglevelsetting.rankingLevelGroup', 'ASC')
			->order_by('rankinglevelsetting.rankingLevel', 'ASC');

		$this->db->limit($limit, $offset);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Edit ranking level setting
	 *
	 * @param $id - int
	 * @return Bool - TRUE or FALSE
	 */

	public function editRankingLevelSetting($id, $form_data) {
		//var_dump($form_data); exit();
		$this->db->where('rankingLevelSettingId', $id);
		$this->db->update('rankinglevelsetting', $form_data);

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Deletes ranking level setting
	 *
	 * @param $rankingId - int
	 * @return Bool - TRUE or FALSE
	 */

	public function deleteRankingLevelSetting($id) {
		$this->db->delete('rankinglevelsetting', array('rankingLevelSettingId' => $id));

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function getRankingList() {
		$this->db->select('*')->from('mkt_level');
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * check if ranking already exists
	 *
	 * @param $rankingSettingId - int
	 * @return Bool - TRUE or FALSE
	 */

	public function isRankingAlreadyExists($rankingLevelGroup, $rankingLevel) {
		$where = "rankingLevelGroup = '" . $rankingLevelGroup . "' AND rankingLevel = " . $rankingLevel;
		$this->db->select('*')->where($where);
		$query = $this->db->get('rankinglevelsetting');

		if ($query->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function getRankingLevelSettingsDetail($transactionId) {
		$this->db->select('*')->from('rankinglevelsetting');

		$this->db->where('rankingLevelSettingId', $transactionId);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function getPlayerTransactionHistory($playerAccountId) {
		$this->db->select('walletaccount.*,
						   player.playerId,
						   player.email,
						   player.createdOn,
						   player.username AS playerName,
						   adminusers.username AS processedByAdmin,
						   playerdetails.*,
						   playeraccount.currency,
						   playeraccount.totalBalanceAmount,
						   paymentmethod.paymentMethodName,
						   mkt_promo.promoName
						   ')
			->from('walletaccount')
			->join('paymentmethod', 'paymentmethod.paymentMethodId = walletaccount.dwMethod', 'left')
			->join('playerpromo', 'playerpromo.playerPromoId = walletaccount.playerPromoId', 'left')
			->join('adminusers', 'adminusers.userId = walletaccount.processedBy', 'left')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId', 'left')
			->join('player', 'player.playerId = playeraccount.playerId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('mkt_promo', 'mkt_promo.promoId = playerpromo.playerpromoId', 'left');

		$this->db->where('walletaccount.playerAccountId', $playerAccountId);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['dwDateTime'] = mdate('%M %d, %Y %H:%i:%s', strtotime($row['dwDateTime']));
				$row['createdOn'] = mdate('%M %d, %Y', strtotime($row['createdOn']));
				$row['birthdate'] = mdate('%M %d, %Y', strtotime($row['birthdate']));
				$row['processDatetime'] = mdate('%M %d, %Y %H:%i:%s', strtotime($row['processDatetime']));
				$row['playerName'] = ucwords($row['playerName']);
				$row['processedByAdmin'] = '<i>No Record</i>';
				$row['dwStatus'] = strtoupper($row['dwStatus']);
				$row['transactionType'] = strtoupper($row['transactionType']);

				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get player balance
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function viewPlayerBalance($sort_by, $limit, $offset) {
		$sortPlayerLvl = '';
		if (isset($sort_by['playerLevel'])) {
			$playerLevelVal = $sort_by['playerLevel'];
			$sortPlayerLvl = $playerLevelVal;
		}

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$query = "SELECT player.playerId, player.username, playerdetails.firstname, playerdetails.lastname, playeraccount.totalBalanceAmount as mainwalletBalanceAmount, playeraccount.currency, playeraccount.playerAccountId, vipsettingcashbackrule.vipLevel, vipsettingcashbackrule.vipLevelName, vipsetting.groupName
			FROM player
			LEFT JOIN playerdetails ON playerdetails.playerId = player.playerId
			LEFT JOIN playeraccount ON playeraccount.playerId = player.playerId
			LEFT JOIN playerlevel ON playerlevel.playerId = player.playerId
			LEFT JOIN vipsettingcashbackrule ON vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId
			LEFT JOIN vipsetting ON vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId
			WHERE player.status = '0'
		    AND playeraccount.type ='wallet'
			AND playeraccount.typeOfPlayer = 'real'
			$limit
			$offset
        ";

		$query = $this->db->query("$query");

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				//$row['cashbackwalletBalanceAmount'] = $this->getPlayerCashbackWalletBalance($row['playerId']);
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get adjustment history (old version)
	 *
	 * @return	$array
	 */
	public function viewAdjustmentHistory($player_id = null, $start_date = null, $end_date = null, $limit = null) {
		$this->db->select(array('bah.*', 'p.username', 'au.username adminname'));
		$this->db->from('balanceadjustmenthistory bah');
		$this->db->join('player p', 'bah.playerId = p.playerId', 'left');
		$this->db->join('adminusers au', 'bah.adjustedBy = au.userId', 'left');
		if ($player_id) {
			$this->db->where('bah.playerId', $player_id);
		}
		if ($start_date) {
			$this->db->where('bah.adjustedOn >=', $start_date);
		}
		if ($end_date) {
			$this->db->where('bah.adjustedOn <=', $end_date);
		}
		$this->db->order_by('bah.adjustedOn', 'DESC');
		if ($limit) {
			$this->db->limit($limit);
		}
		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * Retrieve adjustment history (new / latest)
	 * This version uses the transactions table instead of balanceadjustmenthistory
	 *
	 * @param  int $player_id
	 * @param  string $start_date
	 * @param  string $end_date
	 * @param  int $limit
	 * @return array result set
	 */
	public function viewAdjustmentHistoryV2($player_id = null, $start_date = null, $end_date = null, $limit = null) {
		$this->db->select(array('tr.*', 'p.username', 'au.username adminname', 'es.system_code walletType'));
		$this->db->from('transactions tr');
		$this->db->join('player p', 'tr.to_id = p.playerId', 'left');
		$this->db->join('adminusers au', 'tr.process_user_id = au.userId', 'left');
		$this->db->join('external_system es', 'es.id = tr.sub_wallet_id', 'left');
		if ($player_id) {
			$this->db->where('tr.to_id', $player_id);
		}
		if ($start_date) {
			$this->db->where('tr.created_at >=', $start_date);
		}
		if ($end_date) {
			$this->db->where('tr.created_at <=', $end_date);
		}


		$this->db->where('tr.is_manual_adjustment', Transactions::MANUALLY_ADJUSTED);

		$this->db->order_by('tr.created_at', 'DESC');
		if ($limit) {
			$this->db->limit($limit);
		}
		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * Will get adjustment history Tab
	 *
	 * @return	$array
	 */
	public function viewAdjustmentHistoryTab($player_id = null, $where = null, $values = null) {
		$this->db->select('bah.adjustedOn, bah.playerId, bah.walletType, bah.adjustmentType, bah.amountChanged, bah.oldBalance, bah.newBalance, bah.adjustedBy, bah.reason, p.username as "username", au.username as "adjusted by", es.system_code as "esSysCode", es.system_type as "esSysType", es.status as "esStatus", es.id as "esID"');
		$this->db->from('balanceadjustmenthistory bah');
		$this->db->join('player p', 'bah.playerId = p.playerId', 'left');
		$this->db->join('adminusers au', 'bah.adjustedBy = au.userId', 'left');
		$this->db->join('external_system es', 'bah.walletType = es.id', 'left');
		$this->db->where('bah.playerId', $player_id);
        $this->db->where($where['0'], $values['0']);
        $this->db->where($where['1'], $values['1']);
		$this->db->order_by('bah.adjustedOn', 'DESC');
		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * Will get adjustment history Tab
	 *
	 * @return	$array
	 */
	public function viewAdjustmentHistoryTabV2($player_id = null, $where = null, $values = null) {
		$this->db->select('tr.created_at, tr.to_id, tr.transaction_type, tr.amount, tr.before_balance, tr.after_balance, tr.from_id, tr.note, tr.to_username, tr.from_username,  p.username, au.username, es.system_code, es.status, es.id');
		$this->db->from('transactions tr');
		$this->db->join('player p', 'tr.to_id = p.playerId', 'left');
		$this->db->join('adminusers au', 'tr.process_user_id = au.userId', 'left');
		$this->db->join('external_system es', 'tr.sub_wallet_id = es.id', 'left');
		$this->db->where('tr.to_id', $player_id);

		if($where != null && is_array($where) && $values != null && is_array($values)){

			foreach ($where as $key => $value) {
        		$this->db->where($where[$key], $values[$key]);
			}
		}

		$this->db->where('tr.is_manual_adjustment', Transactions::MANUALLY_ADJUSTED);
		$this->db->order_by('tr.created_at', 'DESC');

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * Export adjustment history
	 *
	 * @return	$array
	 */
	public function exportAdjustmentHistoryToExcel() {
		$start_date = $this->session->userdata('start_date');
		$end_date = $this->session->userdata('end_date');

		$where = null;
		if (!empty($start_date)) {
			$where = "WHERE bah.adjustedOn BETWEEN '" . $start_date . " 00:00:00' AND '" . $end_date . " 23:59:59'";
		}

		$query = "SELECT bah.adjustedOn, p.username, CONCAT(pd.firstname, ' ', pd.lastname) as playername,
			case bah.walletType
		        when 0 then 'Main Wallet'
		        when 1 then 'PT Wallet'
		        when 2 then 'AG Wallet'
		    end as walletType,
			bah.currentBalance, bah.newBalance, bah.reason, au.username as adminname
			FROM balanceadjustmenthistory as bah
			LEFT JOIN player as p
			ON bah.playerId = p.playerId
			LEFT JOIN playerdetails as pd
			ON p.playerId = pd.playerId
			LEFT JOIN adminusers as au
			ON bah.adjustedBy = au.userId";

		$query = $this->db->query("$query $where");

		return $query;
	}

	/**
	 * Will get deposit declined details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function getPlayerBalanceDetails($playerId) {
		$this->db->select('
						   playeraccount.totalBalanceAmount,
						   playeraccount.currency,
						   playeraccount.playerAccountId,
						   player.username,
						   player.email,
						   player.createdOn,
						   player.playerId,
						   playerdetails.*,
						   ')
			->from('playeraccount')
			->join('player', 'player.playerId = playeraccount.playerId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left');
		$this->db->where('playeraccount.playerId', $playerId);
		$this->db->where('player.status', 0);
		$this->db->where('playeraccount.type', 'wallet');

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['firstName'] = ucwords($row['firstName']);
				$row['lastName'] = ucwords($row['lastName']);
				$row['createdOn'] = mdate('%M %d, %Y', strtotime($row['createdOn']));
				$row['birthdate'] = mdate('%M %d, %Y', strtotime($row['birthdate']));
				$row['subwalletBalanceAmount'] = $this->getPlayerSubWalletBalance($row['playerId']);
				$row['cashbackwalletBalanceAmount'] = $this->getPlayerCashbackWalletBalance($row['playerId']);
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get player total balance
	 *
	 * @param 	playerId int
	 * @return 	int
	 */
	public function getPlayerTotalBalance($player_id) {
		$query = "SELECT SUM(wa.amount) AS deposit
									FROM playeraccount AS pa
									LEFT JOIN walletaccount AS wa
									ON pa.playeraccountid = wa.playeraccountid
									LEFT JOIN walletaccountdetails AS wad
									ON wa.walletaccountid = wad.walletaccountid
									WHERE pa.playerid = '" . $player_id . "' AND dwStatus = 'approved' AND transactionType = 'deposit' AND wa.status = '0'";
		$query_deposit = $this->db->query("$query");

		$query = "SELECT SUM(wa.amount) AS withdrawal
										FROM playeraccount AS pa
										LEFT JOIN walletaccount AS wa
										ON pa.playeraccountid = wa.playeraccountid
										LEFT JOIN walletaccountdetails AS wad
										ON wa.walletaccountid = wad.walletaccountid
										WHERE pa.playerid = '" . $player_id . "' AND dwStatus = 'approved' AND transactionType = 'withdrawal' AND wa.status = '0'";
		$query_withdrawal = $this->db->query("$query");

		$deposit = $query_deposit->row_array();
		$withdrawal = $query_withdrawal->row_array();

		$totalBalance = $deposit['deposit'] - $withdrawal['withdrawal'];

		if (!$totalBalance) {
			return false;
		} else {
			return $totalBalance;
		}
	}

	/**
	 * Will set player main wallet new balance amount
	 *
	 * @param $dataRequest - array
	 * @return Bool - TRUE or FALSE
	 */

	public function setPlayerNewBalAmount($playerId, $data) {
		$this->db->where('playerId', $playerId);
		$this->db->where('type', 'wallet');
		$this->db->update('playeraccount', $data);

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Will set player new balance amount
	 *
	 * @param $dataRequest - array
	 * @return Bool - TRUE or FALSE
	 */

	public function setPlayerNewMainWalletBalAmount($playerId, $data) {
		$this->db->where('playerId', $playerId);
		$this->db->where('type', 'wallet');
		$this->db->update('playeraccount', $data);
		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Will set player new balance amount
	 *
	 * @param $dataRequest - array
	 * @return Bool - TRUE or FALSE
	 */
	public function setPlayerNewSubWalletBalAmount($playerId, $data, $gameType) {
		$this->db->where('playerId', $playerId);
		$this->db->where('type', 'subwallet');
		$this->db->where('typeId', $gameType);
		$this->db->update('playeraccount', $data);

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Will get player mainwallet balance
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getMainWalletBalance($player_id) {
		$qry = "SELECT SUM(totalBalanceAmount) AS totalBalanceAmount FROM playeraccount WHERE TYPE IN ('wallet') AND playerId = '" . $player_id . "'";
		$query = $this->db->query("$qry");

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
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
	 * Will set player new balance amount
	 *
	 * @param $dataRequest - array
	 * @return Bool - TRUE or FALSE
	 */

	public function setPlayerNewSubWalletBalAmountAG($playerId, $data) {
		$this->db->where('playerId', $playerId);
		$this->db->where('type', 'subwallet');
		$this->db->where('typeId', '2');
		$this->db->update('playeraccount', $data);

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Will set player new balance amount
	 *
	 * @param $dataRequest - array
	 * @return Bool - TRUE or FALSE
	 */

	public function setPlayerNewCashbackWalletBalAmount($playerId, $data) {
		$this->db->where('playerId', $playerId);
		$this->db->where('type', 'cashbackwallet');
		$this->db->update('playeraccount', $data);

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Will add adjustment history
	 *
	 * @param $data - array
	 * @return Bool - TRUE or FALSE
	 */

	public function addPlayerBalAdjustmentHistory($data) {
		$this->db->insert('balanceadjustmenthistory', $data);

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Will add adjustment history
	 *
	 * @param $data - array
	 * @return Bool - TRUE or FALSE
	 */

	public function saveToAdjustmentHistory($data) {
		$this->db->insert('balanceadjustmenthistory', $data);

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Will saveCashbackHistory
	 *
	 * @param $data - array
	 * @return Bool - TRUE or FALSE
	 */

	public function saveCashbackHistory($data) {
		$this->db->insert('playercashback', $data);

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Will add ranking level settings
	 *
	 * @param $data - array
	 * @return Bool - TRUE or FALSE
	 */

	public function addRankingLevelSetting($data) {
		$this->db->insert('rankinglevelsetting', $data);

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Will search player balance
	 *
	 * @param 	sort_by array
	 * @param 	limit int
	 * @return 	array
	 */
	public function playerBalanceListSearch($sort_by, $limit, $offset = 0) {
		//var_dump($sort_by);exit();
		$searchVal = '';
		if (isset($sort_by['searchVal'])) {
			$sortSearchVal = $sort_by['searchVal'];
			$searchVal = $sortSearchVal;
		}

		$this->db->select('player.playerId,
						   player.username,
						   playerdetails.firstname,
						   playerdetails.lastname,
						   playeraccount.totalBalanceAmount as mainwalletBalanceAmount,
						   playeraccount.currency,
						   playeraccount.playerAccountId,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName
						  ')
			->from('player')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('playeraccount', 'playeraccount.playerId = player.playerId', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$this->db->where('player.status', 0);
		$this->db->where('playeraccount.type', 'wallet');
		$this->db->where('playeraccount.typeOfPlayer', 'real');
		$this->db->limit($limit, $offset);

		if ($sort_by['searchVal'] != '') {
			$this->db->where('player.username', $sort_by['searchVal']);
		}
		if ($sort_by['playerLevel'] != '') {
			$this->db->where('vipsetting.vipSettingId', $sort_by['playerLevel']);
		}
		if ($sort_by['orderByField'] != '') {
			if ($sort_by['orderByField'] == 'firstname') {
				$this->db->order_by('playerdetails.firstname', $sort_by['orderBy']);
			} elseif ($sort_by['orderByField'] == 'lastname') {
				$this->db->order_by('playerdetails.lastname', $sort_by['orderBy']);
			} elseif ($sort_by['orderByField'] == 'userName') {
				$this->db->order_by('player.username', $sort_by['orderBy']);
			} elseif ($sort_by['orderByField'] == 'playerLevel') {
				$this->db->order_by('playerlevel.playerId', $sort_by['orderBy']);
			}
		}

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				if ($row['playerId']) {
					$row['amount'] = $this->getPlayerTotalBalance($row['playerId']) == 0 ? 0 : $this->getPlayerTotalBalance($row['playerId']);
				}

				$data[] = $row;

			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will approved bonus request
	 *
	 * @param $data - array
	 * @return Bool - TRUE or FALSE
	 */
	public function approveBonusRequest($playerPromoId, $data) {
		$this->db->where('playerpromoId', $playerPromoId);
		$this->db->update('playerpromo', $data);

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Will clear bonus status
	 *
	 * @param $playerpromoId - int
	 * @param $data - array
	 * @return Bool - TRUE or FALSE
	 *
	 */
	public function clearPlayerDepositBonus($playerpromoId, $data) {
		$this->db->where('source_id', $playerpromoId);
		$this->db->where('source_type', 'player_promotion');
		$this->db->update('withdraw_conditions', $data);

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Will get deposit/withdrawal detail
	 *
	 * @param 	$walletId int
	 * @return	$array
	 */
	public function getTransactionDetails($walletAccountId, $dwStatus) {
		$this->db->select('*')->from('walletaccount');

		$this->db->where('walletaccount.walletAccountId', $walletAccountId);
		$this->db->where('walletaccount.dwStatus', $dwStatus);
		$query = $this->db->get();

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
	 * Will get deposit/withdrawal detail
	 *
	 * @param 	$walletId int
	 * @return	$array
	 */
	public function getPlayerCashbackPercentage($playerId, $gameType) {
		$this->db->select('vipsettingcashbackbonuspergame.percentage')->from('vipsettingcashbackbonuspergame')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = vipsettingcashbackbonuspergame.vipsettingcashbackruleId')
			->join('playerlevel', 'playerlevel.playerGroupId = vipsettingcashbackrule.vipsettingcashbackruleId')
			->join('player', 'player.playerId = playerlevel.playerId');

		$this->db->where('vipsettingcashbackbonuspergame.gameType', $gameType);
		$this->db->where('player.playerId', $playerId);
		$query = $this->db->get();

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
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function getOTCPaymentMethodDetails($id) {
		$this->db->select('*')->from('otcpaymentmethod');
		$this->db->where('otcPaymentMethodId', $id);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function changeStatusPaymentMethod($data, $id) {
		$this->db->where('otcPaymentMethodId', $id);
		$this->db->update('otcpaymentmethod', $data);
	}

	/**
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function editPaymentMethod($data, $id) {
		$this->db->where('otcPaymentMethodId', $id);
		$this->db->update('otcpaymentmethod', $data);
	}

	/**
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function insertPaymentMethod($data) {
		$this->db->insert('otcpaymentmethod', $data);
	}

	/**
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function deletePaymentMethod($id) {
		$this->db->where('otcPaymentMethodId', $id);
		$this->db->delete('otcpaymentmethod');
	}

	/**
	 * Will get all otc payment method
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function getFriendReferralDetails($limit, $offset) {
		$this->db->select('*')
			->from('player')
			->join('playerfriendreferral', 'player.playerId = playerfriendreferral.playerId')
			->join('playerfriendreferraldetails', 'playerfriendreferral.referralId = playerfriendreferraldetails.referralId');

		$this->db->where('playerfriendreferraldetails.status', '0');
		$this->db->limit($limit, $offset);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * get all player account by playerId
	 *
	 * @param  int
	 * @return  array
	 */
	public function getAllPlayerAccountByPlayerId($player_id) {
		$qry = "SELECT *,
			(SELECT g.game FROM game as g where g.gameId = p.typeId) as game
			FROM playeraccount as p where type = 'subwallet' and playerId = ?";
		$query = $this->db->query($qry, array($player_id));

		return $query->result_array();
	}

	/**
	 * get list of games
	 *
	 * @return  array
	 */
	public function getGames() {
		$query = $this->db->query("SELECT * FROM game");

		return $query->result_array();
	}

	/**
	 * get list of subwallet request
	 *
	 * @return  array
	 */
	// public function getAllSubWallet() {
	// 	$query = $this->db->query("SELECT swd.amount, swd.subWalletDetailsId, swd.requestDatetime, swd.playerId,
	// 		(SELECT p.username FROM player as p where p.playerId = swd.playerId) as username,
	// 		(SELECT pa.currency FROM playeraccount as pa where pa.playerId = swd.playerId and pa.type = 'wallet') as currency,
	// 		(SELECT g.game FROM playeraccount as pa LEFT JOIN game as g ON g.gameId = pa.typeId where pa.playerAccountId = swd.transferFrom) as transferFrom,
	// 		(SELECT g.game FROM playeraccount as pa LEFT JOIN game as g ON g.gameId = pa.typeId where pa.playerAccountId = swd.transferTo) as transferTo,
	// 		swd.transferFrom as playerAccountFrom, swd.transferTo as playerAccountTo
	// 		FROM subwalletdetails as swd
	// 		where swd.processStatus = 'request'
	// 	");

	// 	return $query->result_array();
	// }

	/**
	 * update subwallet details
	 *
	 * @return  array
	 * @return  int
	 * @return  array
	 */
	// public function updateSubWalletDetails($data, $subwallet_details_id) {
	// $this->db->where('subWalletDetailsId', $subwallet_details_id);
	// $this->db->update('subwalletdetails', $data);
	// }

	/**
	 * Will set player new balance amount by playerAccountId
	 *
	 * @param $dataRequest - array
	 * @return Bool - TRUE or FALSE
	 */

	public function setPlayerNewBalAmountByPlayerAccountId($playerAccountId, $data) {
		$this->db->where('playerAccountId', $playerAccountId);
		$this->db->update('playeraccount', $data);

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * get balace by playerAccountId
	 *
	 * @return  array
	 */
	public function getBalanceByPlayerAccountId($player_account_id) {
		$query = $this->db->query("SELECT * FROM playeraccount where playerAccountId = '" . $player_account_id . "'");

		$result = $query->row_array();

		return $result['totalBalanceAmount'];
	}

	public function getPaypalSettings($limit, $offset) {

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = '';
		}
		$qry = "SELECT * FROM paypalsettings ORDER BY status DESC $limit $offset";
		$query = $this->db->query("$qry");

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	public function getNetellerSettings($limit, $offset) {

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = '';
		}
		$qry = "SELECT * FROM netellersettings ORDER BY status DESC $limit $offset";
		$query = $this->db->query("$qry");

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	public function getSkrillSettings($limit, $offset) {

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = '';
		}
		$qry = "SELECT * FROM skrillsettings ORDER BY status DESC $limit $offset";
		$query = $this->db->query("$qry");

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function getPaypalSettingsDetails($id) {
		$this->db->select('*')->from('paypalsettings');
		$this->db->where('paypalSettingsId', $id);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function getNetellerSettingsDetails($id) {
		$this->db->select('*')->from('netellersettings');
		$this->db->where('netellerSettingsId', $id);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function getSkrillSettingsDetails($id) {
		$this->db->select('*')->from('skrillsettings');
		$this->db->where('skrillSettingsId', $id);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will edit paypal settings
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function editPaypalSettings($data, $id) {
		$this->db->where('paypalSettingsId', $id);
		$this->db->update('paypalsettings', $data);
	}

	/**
	 * Will edit neteller setings
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function editNetellerSettings($data, $id) {
		$this->db->where('netellerSettingsId', $id);
		$this->db->update('netellersettings', $data);
	}

	/**
	 * Will edit neteller setings
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function editSkrillSettings($data, $id) {
		$this->db->where('skrillSettingsId', $id);
		$this->db->update('skrillsettings', $data);
	}

	/**
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function insertPaypalSettings($data) {
		$this->db->insert('paypalsettings', $data);
	}

	/**
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function deletePaypalSettings($id) {
		$this->db->where('paypalSettingsId', $id);
		$this->db->delete('paypalsettings');
	}

	/**
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function changeStatusPaypalSettings($data, $id) {
		$this->db->where('paypalSettingsId', $id);
		$this->db->update('paypalsettings', $data);
	}

	public function getPaypalSettingActive() {
		$qry = "SELECT * FROM paypalsettings where status = 1";
		$query = $this->db->query("$qry");

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	public function getNetellerSettingActive() {
		$qry = "SELECT * FROM netellersettings where status = 1";
		$query = $this->db->query("$qry");

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	/**
	 * get userId if its using or child of roleId
	 *
	 * @param	int
	 * @return 	array
	 */
	public function getAllFriendReferralRequest($sort, $limit, $offset) {

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = '';
		}
		$qry = "SELECT p.*, pfr.*, pb.username as inviter, pfrd.*
									FROM player AS p
									INNER JOIN playerfriendreferral AS pfr ON p.playerId = pfr.invitedPlayerId
									LEFT OUTER JOIN player AS pb
										ON pfr.playerId = pb.playerId
									INNER JOIN playerfriendreferraldetails AS pfrd ON pfr.referralId = pfrd.referralId
									INNER JOIN playeraccount as pa ON p.playerId = pa.playerId
									WHERE pa.type = 'wallet' AND pfrd.status = 1 ORDER BY $sort ASC $limit $offset";
		$query = $this->db->query("$qry");

		return $query->result_array();
	}

	/**
	 * get userId if its using or child of roleId
	 *
	 * @param	int
	 * @return 	array
	 */
	public function getFriendReferralDetailsById($referralId) {
		$qry = "SELECT p.*, pfr.*, pb.username as inviter, pfrd.*
									FROM player AS p
									INNER JOIN playerfriendreferral AS pfr ON p.playerId = pfr.invitedPlayerId
									LEFT OUTER JOIN player AS pb
										ON pfr.playerId = pb.playerId
									INNER JOIN playerfriendreferraldetails AS pfrd ON pfr.referralId = pfrd.referralId
									INNER JOIN playeraccount as pa ON p.playerId = pa.playerId
									WHERE pa.type = 'wallet' AND pfrd.referralId = '" . $referralId . "'";
		$query = $this->db->query("$qry");

		return $query->row_array();
	}

	/**
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function updateFriendReferralDetails($data, $id) {
		$this->db->where('referralId', $id);
		$this->db->update('playerfriendreferraldetails', $data);
	}

	/**
	 * Will search players based on the passed parameters
	 *
	 * @param 	string
	 * @param 	int
	 * @param 	int
	 * @param 	string
	 * @return	array
	 */
	public function searchPlayerReferralList($search, $limit, $offset, $type) {

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$qry = "SELECT pfr.*, p.*, pb.username as inviter, pfrd.*
									FROM player AS p
									INNER JOIN playerfriendreferral AS pfr ON p.playerId = pfr.invitedPlayerId
									LEFT OUTER JOIN player AS pb
										ON pfr.playerId = pb.playerId
									INNER JOIN playerfriendreferraldetails AS pfrd ON pfr.referralId = pfrd.referralId
									INNER JOIN playeraccount as pa ON p.playerId = pa.playerId
									WHERE pa.type = 'wallet' AND p.username LIKE '%" . $search . "%' AND pfrd.status = '1' $limit $offset";
		$query = $this->db->query("$qry");

		return $query->result_array();
	}

	/**
	 * Will get dwCount in walletaccount table
	 *
	 * @param 	int
	 * @return	int
	 */
	public function getDWCountById($playerId) {
		$qry = "SELECT wa.dwCount FROM player as p
			LEFT JOIN playeraccount as pa
			ON p.playerId = pa.playerId
			LEFT JOIN walletaccount as wa
			ON pa.playerAccountId = wa.playerAccountId
			WHERE p.playerId = '" . $playerId . "'
			ORDER BY wa.dwCount DESC LIMIT 1";
		$query = $this->db->query("$qry");

		return $query->row_array();
	}

	/**
	 * get list of subwallet per player
	 *
	 * @return  array
	 */
	public function getSubWalletByPlayer($player_id, $limit, $offset) {
		// if ($limit != null) {
		// 	$limit = "LIMIT " . $limit;
		// }

		// if ($offset != null && $offset != 'undefined') {
		// 	$offset = "OFFSET " . $offset;
		// } else {
		// 	$offset = ' ';
		// }
		// $qry = "SELECT swd.amount, swd.subWalletDetailsId, swd.requestDatetime, swd.playerId,
		// 	(SELECT p.username FROM player as p where p.playerId = swd.playerId) as username,
		// 	(SELECT pa.currency FROM playeraccount as pa where pa.playerId = swd.playerId and pa.type = 'wallet') as currency,
		// 	(SELECT g.game FROM playeraccount as pa LEFT JOIN game as g ON g.gameId = pa.typeId where pa.playerAccountId = swd.transferFrom) as transferFrom,
		// 	(SELECT g.game FROM playeraccount as pa LEFT JOIN game as g ON g.gameId = pa.typeId where pa.playerAccountId = swd.transferTo) as transferTo,
		// 	swd.transferFrom as playerAccountFrom, swd.transferTo as playerAccountTo
		// 	FROM subwalletdetails as swd
		// 	where swd.playerId = '" . $player_id . "'
		// 	AND swd.processStatus = 'transferred'
		// 	$limit
		// 	$offset";
		// $query = $this->db->query("$qry");

		// return $query->result_array();
	}

	/**
	 * get payment history
	 *
	 * @return  array
	 */
	public function getPaymentHistoryByPlayer($player_id, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}
		$qry = "SELECT wa.amount, wa.localBankType, wa.dwDateTime, wa.notes, wa.transactionType, wa.dwStatus
			FROM walletaccount as wa
			LEFT JOIN playeraccount as pa
			ON wa.playerAccountId = pa.playerAccountId
			WHERE pa.playerId = '" . $player_id . "'
			$limit
			$offset";
		$query = $this->db->query("$qry");

		return $query->result_array();
	}

	/**
	 * get bank history
	 *
	 * @return  array
	 */
	public function getBankHistoryByPlayer($player_id, $where = null, $values = null, $limit = null, $offset = null) {
		$this->db->select(array(
			'bt.bankName',
			'pbh.changes',
			'pbh.createdOn',
			'pbd.dwBank',
			'pbh.operator',
			'bt.bankName',
		));
		$this->db->from('playerbankhistory pbh');
		$this->db->join('playerbankdetails pbd', 'pbh.playerBankDetailsId = pbd.playerBankDetailsId', 'left');
		$this->db->join('banktype bt', 'pbd.bankTypeId = bt.bankTypeId', 'left');
		$this->db->where('pbd.playerId', $player_id);
		$this->db->where($where['0'], $values['0']);
        $this->db->where($where['1'], $values['1']);
		if ($limit) {
			if ($offset) {
				$thid->db->limit($limit, $offset);
			} else {
				$thid->db->limit($limit);
			}
		}
		$query = $this->db->get();
		$result =  $query->result_array();
		$result = json_decode(json_encode($result),true);
        return $result;
	}

	/**
	 * save bank history
	 *
	 * @param 	array
	 * @return  none
	 */
	public function saveBankHistoryByPlayer($data) {
		$this->db->insert('playerbankhistory', $data);
	}

	function calculateBankDeposit($bankAccountId) {
		$this->db->select('SUM(walletaccount.amount) AS totalBankDeposit')->from('walletaccount')
			->join('localbankdepositdetails', 'localbankdepositdetails.walletAccountId = walletaccount.walletAccountId');
		$this->db->where('localbankdepositdetails.bankAccountId', $bankAccountId);
		$this->db->where('walletaccount.transactionType', 'deposit');
		$this->db->where('walletaccount.dwStatus', 'approved');

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}

			return $data;
		}
		return false;
	}

	/**
	 * export player balance
	 *
	 * @param 	$sort_by array
	 * @return	$array
	 */
	public function exportPlayerBalanceToExcel($sort_by) {
		$searchVal = '';
		if (isset($sort_by['searchVal'])) {
			$sortSearchVal = $sort_by['searchVal'];
			$searchVal = $sortSearchVal;
		}

		$this->db->select('player.playerId,
						   player.username,
						   playerdetails.firstname,
						   playerdetails.lastname,
						   playeraccount.totalBalanceAmount as mainwalletBalanceAmount,
						   playeraccount.currency,
						   playeraccount.playerAccountId,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName
						  ')
			->from('player')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('playeraccount', 'playeraccount.playerId = player.playerId', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$this->db->where('player.status', 0);
		$this->db->where('playeraccount.type', 'wallet');
		$this->db->where('playeraccount.typeOfPlayer', 'real');

		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query;
		}
		return false;
	}

	/**
	 * Will display cashcard deposit list
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function getCashcardDepositRequestTransaction($sort_by, $limit, $offset) {
		//var_dump($sort_by);exit();
		$sortPlayerLvl = '';
		if (isset($sort_by['playerLevel'])) {
			$playerLevelVal = $sort_by['playerLevel'];
			$sortPlayerLvl = $playerLevelVal;
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
		//var_dump($sort_by);exit();
		$this->db->select('walletaccount.*,
						   player.username AS playerName,
						   playerdetails.firstName,
						   playerdetails.lastName,
						   playeraccount.currency,
						   player.playerId,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   promorules.promorulesId,
						   promorules.promoName,
						   playerpromo.playerpromoId,
						   playerpromo.bonusAmount,
						   adminusers.username AS processedByAdmin,
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId', 'left')
			->join('player', 'player.playerId = playeraccount.playerId', 'left')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->join('playerpromo', 'playerpromo.playerpromoId = walletaccount.playerPromoId', 'left')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->join('adminusers', 'adminusers.userId = walletaccount.processedBy', 'left')
			->order_by('walletaccount.dwDateTime', 'desc');

		$this->db->where('walletaccount.dwMethod', 5);
		$this->db->where('walletaccount.transactionType', $sort_by['transactionType']);
		$this->db->where('walletaccount.dwStatus', $sort_by['dwStatus']);

		if ($sortPlayerLvl != '') {
			$this->db->where('vipsetting.vipSettingId', $sortPlayerLvl);
		}
		if ($sortDateRangeValueStart != '') {
			$this->db->where('walletaccount.dwDateTime >= ', $sortDateRangeValueStart . ' 00:00:00');
		}
		if ($sortDateRangeValueEnd != '') {
			$this->db->where('walletaccount.dwDateTime <= ', $sortDateRangeValueEnd . ' 23:59:59');
		}

		$this->db->order_by('walletaccount.dwDateTime', 'desc');

		if ($limit != null) {
			$this->db->limit($limit, $offset);
		}

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will display cashcard dwCount
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function getCashcardDWCount($transactionType, $dwStatus, $dateRangeValueStart, $dateRangeValueEnd) {
		$this->db->select('transactionType')->from('walletaccount')->where('transactionType', $transactionType)->where('dwStatus', $dwStatus)->where('status', 0);
		if ($dateRangeValueStart != '') {
			$this->db->where('walletaccount.dwDateTime >= ', $dateRangeValueStart . ' 00:00:00');
		}
		if ($dateRangeValueEnd != '') {
			$this->db->where('walletaccount.dwDateTime <= ', $dateRangeValueEnd . ' 23:59:59');
		}
		$this->db->where('walletaccount.dwMethod', 5);
		$this->db->where('walletaccount.transactionType', $transactionType);
		$this->db->where('walletaccount.dwStatus', $dwStatus);

		$query = $this->db->get();
		return $query->num_rows();
	}

	/**
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function saveTransactionFee($data, $id) {
		$this->db->where('transactionfeesettingId', $id);
		$this->db->update('transactionfeesetting', $data);
	}

	/**
	 * Will saveTransactionFeeHistory
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function saveTransactionFeeHistory($data) {
		$this->db->insert('bankcompensationfeehistory', $data);
	}

	/**
	 * Gets previous balance set amount
	 *
	 * @param $transactionId - int
	 * @return array
	 */
	public function getTrasancationFeeSetting() {
		$this->db->select('*')->from('transactionfeesetting');

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * get player sub wallet id
	 *
	 * @param 	playerId int
	 * @param 	type int
	 * @return	array
	 */
	public function getSubWalletAccountId($playerId, $subwalletType) {
		$this->db->select('playerAccountId')->from('playeraccount');
		$this->db->where('playerId', $playerId);
		$this->db->where('typeId', $subwalletType);
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * Will save player withdrawal condition
	 *
	 * @param $conditionData array
	 * @return null
	 */
	public function savePlayerWithdrawalCondition($conditionData) {
		$this->db->insert('withdraw_conditions', $conditionData);
	}

	/**
	 * set withdraw condition to inactive
	 *
	 * @return  array
	 */
	public function inactiveWithdrawCondition($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('withdraw_conditions', $data);
	}

	/**
	 * updateWithdrawalToChecking
	 *
	 * @param $data - array
	 * @param $id - int
	 * @return array
	 */
	public function updateWithdrawalToChecking($id, $data) {
		$this->db->trans_start();
		$this->db->where('walletAccountId', $id);
		$this->db->update('walletaccount', $data);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			return false;
		}
		return true;
	}
}

/* End of file payment.php */
/* Location: ./application/models/payment.php */
