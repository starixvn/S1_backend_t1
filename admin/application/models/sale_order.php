<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Create Sale Order for deposit
 *
 * General behaviors include
 * * Create deposit order
 * * create sale order
 * * update sale order
 * * add reason to certain sale order
 * * process declined sale order
 * * process approved sale order
 * * get member group bonus for a certain player
 * * check sale order status
 * * declined certain sale order
 * * approved certain sale order
 * * update status to (settled, canceled, failed, declined)
 * * update extra information
 * * get sale orders filtered by payment kind and date range
 * * get sale order details
 * * check member deposit bonus
 * * get sale order reports
 * * get newest sale orders
 *
 * @category Payment Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Sale_order extends BaseModel {

	protected $tableName = 'sale_orders';

	const PAYMENT_KIND_DEPOSIT = 1;
	//external system id, manual cannot map to any api id, so it is 0
	const SYSTEM_ID_MANUAL = 0;

	//status starts from 3, processing=pending
	const STATUS_PROCESSING = 3;
	//callback from browser, not finish, but can count as deposit
	const STATUS_BROWSER_CALLBACK = 4;
	//callback from server, finished the order, or manually
	const STATUS_SETTLED = 5;
	//cancelled
	const STATUS_CANCELLED = 6;
	//order failed
	const STATUS_FAILED = 7;
	//declined
	const STATUS_DECLINED = 8;
	//checking
	const STATUS_CHECKING = 9;

	const VIEW_STATUS_APPROVED = 'approved';
	const VIEW_STATUS_REQUEST = 'request';
	const VIEW_STATUS_DECLINED = 'declined';

	const VIEW_STATUS_APPROVED_ALL = 'approvedAll';
	const VIEW_STATUS_APPROVED_TODAY = 'approvedToday';
	const VIEW_STATUS_REQUEST_ALL = 'requestAll';
	const VIEW_STATUS_REQUEST_TODAY = 'requestToday';
	const VIEW_STATUS_REQUEST_BANKDEPOSIT = 'requestBankDeposit';
	const VIEW_STATUS_REQUEST_3RDPARTY= 'request3rdParty';
	const VIEW_STATUS_DECLINED_ALL = 'declinedAll';
	const VIEW_STATUS_DECLINED_TODAY = 'declinedToday';

	const PLAYER_DEPOSIT_METHOD_UNSPECIFIED = 0;
	const PLAYER_DEPOSIT_METHODS = array(
		'Transfer via ATM' => 1,
		'Transfer via Internet Banking' => 2,
		'Transfer over the counter' => 3,
		'Transfer via Phone Banking' => 4,
		'Transfer via Mobile Banking' => 5,
		'Transfer via Alipay' => 6,
	);

	const COLUMN_CREATED_AT					= 'created_at';
	const COLUMN_UPDATED_AT					= 'updated_at';
	const COLUMN_PROCESS_TIME				= 'process_time';
	const COLUMN_PLAYER_DEPOSIT_TIME		= 'player_deposit_time';
	const COLUMN_PROCESSED_CHECKING_TIME	= 'processed_checking_time';
	const COLUMN_PROCESSED_APPROVED_TIME	= 'processed_approved_time';

    //Deposit status history
    //1.Create Order
	const DEPOSIT_STATUS_CREATE_ORDER = 100;
    //2.Submit Order
    const DEPOSIT_STATUS_SUBMIT_ORDER = 200;
    //3.Redirect Type
    const DEPOSIT_STATUS_RERIERCT_URL = 300;                //REIRECT_TYPE_URL
    const DEPOSIT_STATUS_RERIERCT_FORM = 301;	            //REIRECT_TYPE_FORM
    const DEPOSIT_STATUS_RERIERCT_DIRECT_PAY = 302;         //REIRECT_TYPE_DIRECT_PAY
    const DEPOSIT_STATUS_RERIERCT_QRCODE = 303;             //REIRECT_TYPE_QRCODE
    const DEPOSIT_STATUS_RERIERCT_QRCODE_MODAL = 304;       //REIRECT_TYPE_QRCODE_MODAL
    const DEPOSIT_STATUS_RERIERCT_STATIC = 305;		        //REIRECT_TYPE_STATIC
    const DEPOSIT_STATUS_RERIERCT_ERROR = 306;			    //REIRECT_TYPE_ERROR
    const DEPOSIT_STATUS_RERIERCT_ERROR_MODAL = 307;	    //REIRECT_TYPE_ERROR_MODAL
    const DEPOSIT_STATUS_RERIERCT_HTML = 308;	    //REIRECT_TYPE_HTML
    //4.Get Callback
	const DEPOSIT_STATUS_GET_CALLBACK = 400;
	const DEPOSIT_STATUS_RESEND_CALLBACK = 401;
    //5.Check Callback Order
    const DEPOSIT_STATUS_CHECK_CALLBACK_ORDER_FAILED = 500;
    //6.Approve Sale Order
    const DEPOSIT_STATUS_APPROVE_SALE_ORDER = 600;

	function __construct() {
		parent::__construct();
	}

	public function getSaleOrderById($id) {
		if (!empty($id)) {
			return $this->getOneRowById($id);
		}
		return null;
	}

	/**
	 * detail: get order status
	 *
	 * @param int $id sale order id
	 * @return array
	 */
	public function getSaleOrderStatusById($id) {
		$this->db->select('status');
		// $this->db->from($this->tableName);
		$this->db->where('id', $id);
		$qry = $this->db->get($this->tableName, 1);
		return $this->getOneRowOneField($qry, 'status');
	}

    /**
     * detail: get the status of last sale order
     * @param int $playerId
     * @return array
     */
    public function getLastSalesOrderByPlayerId($playerId) {
        $this->db->select('*');
        $this->db->from($this->tableName);
        $this->db->where('player_id', $playerId);
        $this->db->order_by('id', 'desc');
        $qry = $this->db->get();
        return $this->getOneRow($qry);
    }

    /**
     * detail: update the status base on sale order id
     * @param int $saleOrderID
     * @return array
     */
    public function updateLastSalesOrderToRejectByID($saleOrderID) {
        $this->db->where('id', $saleOrderID);
        $this->db->update($this->tableName, array('status' => 8));
    }

	/**
	 * detail: create new deposit order
	 *
	 * note: collection account: payment_account_id
	 * 		 player deposit account info: playerBankDetailsId, depositTransactionCode, depositSlipPath
	 * 		 promotion info: player_promo_id
	 * 		 ip info: ip, geo_location
	 * 		 status: status
	 *
	 * @param int $paymentKind sale order payment kind
	 * @param int $payment_account_id payment account id
	 * @param int $player_id sale order player id
	 * @param float $amount sale order amount
	 * @param string $currency sale order currency
	 * @param int $player_promo_id sale order player promo id
	 * @param string $ip sale order ip
	 * @param string $geo_location sale order gelo location
	 * @param int $playerBankDetailsId sale order player bank details id
	 * @param string $depositTransactionCode sale order deposit transaction code
	 * @param string $depositSlipPath sale order deposit slip path
	 * @param string $notes sale order notes
	 * @param int $status sale order status
	 * @param int $sub_wallet_id sale order sub wallet id
	 * @param int $group_level_id sale order group level id
	 * @param date $depositDatetime sale order deposit date time
	 * @param string $depositReferenceNo sale order daposit ref number
	 * @param int $pendingDepositWalletType sale order pending deposit wallet type
	 * @param int $depositMethod deposit method
	 * @param boolean $is_mobile if is mobile
	 *
	 * @return array
	 */
	public function createDepositOrder($paymentKind, $payment_account_id, $playerId, $amount, $currency,
		$player_promo_id, $ip, $geo_location, $playerBankDetailsId, $depositTransactionCode = null,
		$depositSlipPath = null, $notes = null, $status = self::STATUS_PROCESSING,
		$sub_wallet_id = null, $group_level_id = null, $depositDatetime = null, $depositReferenceNo = null,
		$pendingDepositWalletType = null, $depositMethod = self::PLAYER_DEPOSIT_METHOD_UNSPECIFIED,
		$is_mobile=false,$player_submit_datetime=null, $promo_info=null,$fullName=null,$depositAccountNo=null,
		$playerDepositMethod = null,$secureId = null, $created_at = null, $timeout_at = null, $mode_of_deposit = null,
		$reason=null, $show_reason_to_player=null) {
		//load system id from payment account id
		$this->load->model(array('payment_account', 'wallet_model', 'banktype', 'playerbankdetails', 'player_model', 'player_promo'));
		$paymentAccount = $this->payment_account->getPaymentAccount($payment_account_id);
		$playerPayment = $this->playerbankdetails->getPlayerBankDetailsById($playerBankDetailsId);
		$depositMethod = ($playerDepositMethod >= 0) ? $playerDepositMethod : $depositMethod;

		if (empty($paymentAccount)) {
			throw new Exception("can't find payment account info");
		}
		$paymentType = $this->banktype->getBankTypeById($paymentAccount->payment_type_id);
		// $playerPaymentType = null;
		$playerBankAccountFullName = null;
		$playerBankAccountNumber = null;
		$playerBankAddress = null;
		$playerBankName = null;
		if (!empty($playerPayment)) {
			$playerPaymentType = $this->banktype->getBankTypeById($playerPayment->bankTypeId);
			if (empty($playerPaymentType) || empty($paymentType)) {
				throw new Exception("can't find payment type");
			}
			$playerBankName = $playerPaymentType->bankName;
			$playerBankAccountFullName = $playerPayment->bankAccountFullName;
			$playerBankAccountNumber = $playerPayment->bankAccountNumber;
			$playerBankAddress = $playerPayment->bankAddress;
		}

		$playerBankAccountFullName = ($fullName) ? $fullName: $playerBankAccountFullName;
		$playerBankAccountNumber = ($depositAccountNo) ? $depositAccountNo: $playerBankAccountNumber;

		$systemId = $paymentAccount->external_system_id;
		$paymentFlag = $paymentAccount->flag;
		if (empty($systemId)) {
			//empty
			$systemId = 0;
		}
		if (empty($player_promo_id)) {
			$player_promo_id = null;
		}
		$promo_rules_id=null;
		$promo_cms_id=null;
		if(isset($promo_info['promo_rules_id'])){
			$promo_rules_id=$promo_info['promo_rules_id'];
		}
		if(isset($promo_info['promo_cms_id'])){
			$promo_cms_id=$promo_info['promo_cms_id'];
		}
		//load main wallet
		$wallet = $this->wallet_model->getMainWalletBy($playerId);
		$walletId = null;
		if ($wallet) {
			$walletId = $wallet->playerAccountId;
		}
		$timeout = $this->config->item('deposit_timeout_seconds');
        $created_at = $created_at ? $created_at : $this->utils->getNowForMysql();
        $timeout_at = $timeout_at ?  $timeout_at : $this->utils->getNowAdd($timeout);
		// $defaultCurrency = $this->config->item('default_currency');
        $mode_of_deposit = $mode_of_deposit ?: null;
		// $this->startTrans();
		//player bank info: playerbankdetails

		$secureId = $secureId ? $secureId : $this->getSecureId($this->tableName, 'secure_id', true, 'D');

		if($is_mobile===null){
			$is_mobile=$this->utils->is_mobile();
		}

		// OGP-12220
		$browser_user_agent = $this->agent->agent_string();

		$saleOrder = array(
			'secure_id' => $secureId,

			'payment_account_id' => $payment_account_id,
			'payment_flag' => $paymentFlag,
			'system_id' => $systemId,
			'player_id' => $playerId,
			'amount' => $this->convertCurrency($amount, $currency),
			'player_promo_id' => $player_promo_id,
			'payment_kind' => $paymentKind,
			'ip' => $ip,
			'geo_location' => $geo_location,
			'is_mobile' => $is_mobile,

			'timeout' => $timeout,
			'timeout_at' => $timeout_at,
			'wallet_id' => $walletId,
			'currency' => $currency,
			'original_amount' => $amount,
			'payment_type_name' => $paymentType->bankName,
			'payment_account_name' => $paymentAccount->payment_account_name,
			'payment_account_number' => $paymentAccount->payment_account_number,
			'payment_branch_name' => $paymentAccount->payment_branch_name,
			'account_image_filepath' => $paymentAccount->account_image_filepath,

			'player_bank_details_id' => $playerBankDetailsId,
			'player_payment_type_name' => $playerBankName,
			'player_payment_account_name' => $playerBankAccountFullName,
			'player_payment_account_number' => $playerBankAccountNumber,
			'player_payment_branch_name' => $playerBankAddress,

			'player_deposit_transaction_code' => $depositTransactionCode,
			'player_deposit_slip_path' => $depositSlipPath,
			'player_deposit_time' => $depositDatetime,
			'player_deposit_reference_no' => $depositReferenceNo,
			'player_deposit_method' => $depositMethod,

			'status' => $status,
			'notes' => $notes,
			'created_at' => $created_at,
			'updated_at' => $this->utils->getNowForMysql(),
			'sub_wallet_id' => $sub_wallet_id,
			'group_level_id' => $group_level_id,
			'pending_deposit_wallet_type' => $pendingDepositWalletType,
			'player_submit_datetime' => $player_submit_datetime,

			'promo_rules_id'=>$promo_rules_id,
			'promo_cms_id'=>$promo_cms_id,

			'player_mode_of_deposit'=>$mode_of_deposit,
			'reason'=>$reason,
			'show_reason_to_player'=>$show_reason_to_player,
			// OGP-12220
			'browser_user_agent' => $browser_user_agent,
		);

		$this->utils->debug_log('--- postManualDeposit --- saleOrder', $saleOrder);

		$this->db->insert($this->tableName, $saleOrder);

		$saleOrder['id'] = $this->db->insert_id();

		//add deposit count
		$this->player_model->incTotalDepositCount($playerId);

		// $this->endTrans();

		// if ($this->isErrorInTrans()) {
		// 	return false;
		// }

		return $saleOrder;
	}

	public function saveFakeSaleOrderToDepositOrder($saleOrder) {

		//check player id, amount, secure id, remove id
		if(isset($saleOrder['id'])){
			unset($saleOrder['id']);
		}

		if(empty($saleOrder['player_id']) || empty($saleOrder['amount']) || empty($saleOrder['secure_id']) ){
			$this->utils->error_log('create sale order failed');
			return false;
		}

		$playerId=$saleOrder['player_id'];

		$this->db->insert($this->tableName, $saleOrder);

		$saleOrder['id'] = $this->db->insert_id();

		//add deposit count
		$this->player_model->incTotalDepositCount($playerId);

		return $saleOrder;
	}

	/**
	 * detail: create fake new deposit order
	 *
	 * note: collection account: payment_account_id
	 * 		 player deposit account info: playerBankDetailsId, depositTransactionCode, depositSlipPath
	 * 		 promotion info: player_promo_id
	 * 		 ip info: ip, geo_location
	 * 		 status: status
	 *
	 * @param int $paymentKind sale order payment kind
	 * @param int $payment_account_id payment account id
	 * @param int $player_id sale order player id
	 * @param float $amount sale order amount
	 * @param string $currency sale order currency
	 * @param int $player_promo_id sale order player promo id
	 * @param string $ip sale order ip
	 * @param string $geo_location sale order gelo location
	 * @param int $playerBankDetailsId sale order player bank details id
	 * @param string $depositTransactionCode sale order deposit transaction code
	 * @param string $depositSlipPath sale order deposit slip path
	 * @param string $notes sale order notes
	 * @param int $status sale order status
	 * @param int $sub_wallet_id sale order sub wallet id
	 * @param int $group_level_id sale order group level id
	 * @param date $depositDatetime sale order deposit date time
	 * @param string $depositReferenceNo sale order daposit ref number
	 * @param int $pendingDepositWalletType sale order pending deposit wallet type
	 * @param int $depositMethod deposit method
	 * @param boolean $is_mobile if is mobile
	 *
	 * @return array
	 */
	public function createFakeDepositOrder($paymentKind, $payment_account_id, $playerId, $amount, $currency,
		$player_promo_id, $ip, $geo_location, $playerBankDetailsId, $depositTransactionCode = null,
		$depositSlipPath = null, $notes = null, $status = self::STATUS_PROCESSING,
		$sub_wallet_id = null, $group_level_id = null, $depositDatetime = null, $depositReferenceNo = null,
		$pendingDepositWalletType = null, $depositMethod = self::PLAYER_DEPOSIT_METHOD_UNSPECIFIED, $is_mobile=false,$player_submit_datetime=null,$fullName=null,$depositAccountNo=null,$playerDepositMethod=null) {
		//load system id from payment account id
		$this->load->model(array('payment_account', 'wallet_model', 'banktype', 'playerbankdetails', 'player_model', 'player_promo'));
		$paymentAccount = $this->payment_account->getPaymentAccount($payment_account_id);
		$playerPayment = $this->playerbankdetails->getPlayerBankDetailsById($playerBankDetailsId);
		$depositMethod = $playerDepositMethod ? $playerDepositMethod : $depositMethod;
		if (empty($paymentAccount)) {
			throw new Exception("can't find payment account info");
		}
		$paymentType = $this->banktype->getBankTypeById($paymentAccount->payment_type_id);
		// $playerPaymentType = null;
		$playerBankAccountFullName = null;
		$playerBankAccountNumber = null;
		$playerBankAddress = null;
		$playerBankName = null;
		if (!empty($playerPayment)) {
			$playerPaymentType = $this->banktype->getBankTypeById($playerPayment->bankTypeId);
			if (empty($playerPaymentType) || empty($paymentType)) {
				throw new Exception("can't find payment type");
			}
			$playerBankName = $playerPaymentType->bankName;
			$playerBankAccountFullName = $playerPayment->bankAccountFullName;
			$playerBankAccountNumber = $playerPayment->bankAccountNumber;
			$playerBankAddress = $playerPayment->bankAddress;
		}

		$playerBankAccountFullName = ($fullName) ? $fullName: $playerBankAccountFullName;
		$playerBankAccountNumber = ($depositAccountNo) ? $depositAccountNo: $playerBankAccountNumber;

		$systemId = $paymentAccount->external_system_id;
		$paymentFlag = $paymentAccount->flag;
		if (empty($systemId)) {
			//empty
			$systemId = 0;
		}
		if (empty($player_promo_id)) {
			$player_promo_id = null;
		}
		//load main wallet
		$wallet = $this->wallet_model->getMainWalletBy($playerId);
		$walletId = null;
		if ($wallet) {
			$walletId = $wallet->playerAccountId;
		}
		$timeout = $this->config->item('deposit_timeout_seconds');
		// $defaultCurrency = $this->config->item('default_currency');

		// $this->startTrans();
		//player bank info: playerbankdetails

		$secureId = $this->getSecureId($this->tableName, 'secure_id', true, 'D');

		if($is_mobile===null){
			$is_mobile=$this->utils->is_mobile();
		}

		// OGP-12220
		$browser_user_agent = $this->agent->agent_string();

		$saleOrder = array(
			'secure_id' => $secureId,

			'payment_account_id' => $payment_account_id,
			'payment_flag' => $paymentFlag,
			'system_id' => $systemId,
			'player_id' => $playerId,
			'amount' => $this->convertCurrency($amount, $currency),
			'player_promo_id' => $player_promo_id,
			'payment_kind' => $paymentKind,
			'ip' => $ip,
			'geo_location' => $geo_location,
			'is_mobile' => $is_mobile,

			'timeout' => $timeout,
			'timeout_at' => $this->utils->getNowAdd($timeout),
			'wallet_id' => $walletId,
			'currency' => $currency,
			'original_amount' => $amount,
			'payment_type_name' => $paymentType->bankName,
			'payment_account_name' => $paymentAccount->payment_account_name,
			'payment_account_number' => $paymentAccount->payment_account_number,
			'payment_branch_name' => $paymentAccount->payment_branch_name,
			'account_image_filepath' => $paymentAccount->account_image_filepath,

			'player_bank_details_id' => $playerBankDetailsId,
			'player_payment_type_name' => $playerBankName,
			'player_payment_account_name' => $playerBankAccountFullName,
			'player_payment_account_number' => $playerBankAccountNumber,
			'player_payment_branch_name' => $playerBankAddress,

			'player_deposit_transaction_code' => $depositTransactionCode,
			'player_deposit_slip_path' => $depositSlipPath,
			'player_deposit_time' => $depositDatetime,
			'player_deposit_reference_no' => $depositReferenceNo,
			'player_deposit_method' => $depositMethod,

			'status' => $status,
			'notes' => $notes,
			'created_at' => $this->utils->getNowForMysql(),
			'updated_at' => $this->utils->getNowForMysql(),
			'sub_wallet_id' => $sub_wallet_id,
			'group_level_id' => $group_level_id,
			'pending_deposit_wallet_type' => $pendingDepositWalletType,
			'player_submit_datetime' => $player_submit_datetime,

			//OGP-12220
			'browser_user_agent' => $browser_user_agent,
		);

		// $this->db->insert($this->tableName, $saleOrder);

		// $saleOrder['id'] = $this->db->insert_id();

		//add deposit count
		// $this->player_model->incTotalDepositCount($playerId);

		// $this->endTrans();

		// if ($this->isErrorInTrans()) {
		// 	return false;
		// }

		return $saleOrder;
	}

	/**
	 * detail: creating new sale order
	 *
	 * @param int $systemId sale order system id
	 * @param int $playerId sale order player id
	 * @param float $amount sale order amount
	 * @param int $paymentKind sale order payment king
	 * @param int $status sale order status
	 * @param string $notes sale order notes
	 * @param int $player_promo_id sale order player promo id
	 * @param string $currency sale order currency
	 * @param int $payment_account_id sale order payment account id
	 * @param datetime $date sale order date
	 * @param string $directPayExtraInfo sale order direct pay data info
	 * @param int $subWalletId sale order sub wallet id
	 * @param int $group_level_id sale order group level id
	 * @param boolean $is_mobile if is mobile
	 *
	 * @return int
	 */
	public function createSaleOrder( $systemId // #1
		, $playerId // #2
		, $amount // #3
		, $paymentKind // #4
		, $status = self::STATUS_NORMAL // #5
		, $notes = null // #6
		, $player_promo_id = null // #7
		, $currency = null // #8
		, $payment_account_id = null // #9
		, $date = null // #10
		, $directPayExtraInfo = null // #11
		, $subWalletId = null // #12
		, $group_level_id = null // #13
		, $is_mobile=false // #14
		,$player_deposit_reference_no = null // #15
		, $deposit_time = null // #16
		, $promo_info=null // #17
		, $playerDepositMethod = null // #18
	) {

		$this->load->model(array('wallet_model', 'payment_account', 'banktype'));
		$secureId = $this->getSecureId($this->tableName, 'secure_id', true, 'D');

		$dwIp = $this->input->ip_address();
		$geolocation = $this->utils->getGeoplugin($dwIp);
		$geo_location = $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'];
		$timeout = $this->config->item('deposit_timeout_seconds');
		//load main wallet
		$wallet = $this->wallet_model->getMainWalletBy($playerId);
		$walletId = null;
		if ($wallet) {
			$walletId = $wallet->playerAccountId;
		}
		if (empty($systemId)) {
			//empty
			$systemId = 0;
		}
		if (empty($player_promo_id)) {
			$player_promo_id = null;
		}
		$promo_rules_id=null;
		$promo_cms_id=null;
		if(isset($promo_info['promo_rules_id'])){
			$promo_rules_id=$promo_info['promo_rules_id'];
		}
		if(isset($promo_info['promo_cms_id'])){
			$promo_cms_id=$promo_info['promo_cms_id'];
		}
    	if($is_mobile===null){
        	$is_mobile=$this->utils->is_mobile();
    	}

		$paymentBankName = null;
		$paymentAccountName = null;
		$paymentAccountNumber = null;
		$paymentBranchName = null;

		$paymentAccount = $this->payment_account->getPaymentAccount($payment_account_id);
		if ($paymentAccount) {
			$paymentType = $this->banktype->getBankTypeById($paymentAccount->payment_type_id);
			$paymentBankName = $paymentType->bankName;
			$paymentAccountName = $paymentAccount->payment_account_name;
			$paymentAccountNumber = $paymentAccount->payment_account_number;
			$paymentBranchName = $paymentAccount->payment_branch_name;
		}

		if(empty($playerDepositMethod)){
			$playerDepositMethod=0;
		}

		// OGP-12220
		$browser_user_agent = $this->agent->agent_string();

		$this->db->insert($this->tableName, [
			'secure_id' => $secureId,
			'system_id' => $systemId,
			'player_id' => $playerId,
			'amount' => $amount,
			'payment_kind' => $paymentKind,
			'status' => $status,
			'notes' => $notes,
			'player_promo_id' => $player_promo_id,
			'created_at' => $date ?: $this->utils->getNowForMysql(),
			'updated_at' => $date ?: $this->utils->getNowForMysql(),
			'ip' => $dwIp,
			'geo_location' => $geo_location,
			'is_mobile' => $is_mobile,

			'timeout' => $timeout,
			'timeout_at' => $this->utils->getNowAdd($timeout),
			'currency' => $currency,
			'wallet_id' => $walletId,

			'payment_account_id' => $payment_account_id,
			'payment_type_name' => $paymentBankName,
			'payment_account_name' => $paymentAccountName,
			'payment_account_number' => $paymentAccountNumber,
			'payment_branch_name' => $paymentBranchName,

			'direct_pay_extra_info' => json_encode($directPayExtraInfo),

			'sub_wallet_id' => $subWalletId,
			'group_level_id' => $group_level_id,
			'player_deposit_reference_no' => $player_deposit_reference_no,
			'player_deposit_time' => $deposit_time,
			'player_deposit_method' => $playerDepositMethod,

			'promo_rules_id'=>$promo_rules_id,
			'promo_cms_id'=>$promo_cms_id,
			// OGP-12220
			'browser_user_agent' => $browser_user_agent,

		]);

		return $this->db->insert_id();
	}

	/**
	 * detail: add reason to a certain sale order
	 *
	 * @param int $id sale order id
	 * @param string $reason
	 *
	 * @return boolean
	 */
	public function appendReason($id, $reason) {
		$sql = "update sale_orders set reason=concat(ifnull(reason,''),' | ',?) where id=?";
		return $this->runRawUpdateInsertSQL($sql, array($reason, $id));
	}

    /**
     * detail: add note to sale orders notes action log
     *
     * @param int $id sale order id
     * @param string $note
     *
     * @return boolean
     */
    public function appendNotes($id, $notes) {
		$this->load->model(array('users','sale_orders_notes'));
        $this->sale_orders_notes->add($notes, Users::SUPER_ADMIN_ID, Sale_orders_notes::ACTION_LOG, $id);
    }

    /**
	 * detail: method for adding sale_orders_notes and sale_orders_timelog
	 *
	 * @param int $saleOrderId sale_orders_notes field
	 * @param int $adminUserId sale_orders_notes field
	 * @param string $note sale_orders_notes field
	 * @param string $beforeStatus sale_orders_timelog field
	 * @param string $afterStatus sale_orders_timelog field
	 * @param string $urer_type sale_orders_timelog field
	 *
	 * @return boolean
	 */
	public function addSaleOrderNotes($saleOrderId, $adminUserId, $note, $beforeStatus = '', $afterStatus = '', $urer_type = Sale_orders_timelog::ADMIN_USER, $note_type = Sale_orders_notes::ACTION_LOG) {
		$this->load->model(array('sale_orders_notes','sale_orders_timelog'));
		$this->sale_orders_timelog->add($saleOrderId, $urer_type, $adminUserId, array('before_status' => $beforeStatus, 'after_status' => $afterStatus));
		$this->sale_orders_notes->add($note, $adminUserId, $note_type, $saleOrderId);
	}

	/**
	 * detail: update the sale order result
	 *
	 * @param int $id sale order id
	 * @param string $reason
	 * @param int $show_reason_to_player sale order show reason to order
	 * @param int $newStatus sale order status
	 * @param string $extra_info sale order extra information
	 *
	 * @return boolean
	 */
	public function updateSaleOrderResult($id, $actionLog, $show_reason_to_player, $newStatus, &$extra_info=null) {
		$this->load->model(array('users', 'sale_orders_timelog'));
		$this->load->library('authentication');
        $adminUserId = null;
        if (method_exists($this->authentication, 'getUserId')) {
            $adminUserId = $this->authentication->getUserId();
        }
        if (empty($adminUserId)) {
            //get super admin
            $adminUserId = $this->users->getSuperAdminId();
        }

		$rlt = false;
		$saleOrder = $this->getSaleOrderWithPlayerById($id);
		//only change processing
		if ($saleOrder && $saleOrder->status == self::STATUS_PROCESSING || $saleOrder->status == self::STATUS_CHECKING) {
			$this->load->model(array('player_model','sale_orders_notes'));
			// $this->startTrans();

			$this->utils->debug_log('update sale order status to ' . $newStatus);
			if($newStatus == self::STATUS_BROWSER_CALLBACK) {
				$newStatus = self::STATUS_PROCESSING;
				$this->utils->debug_log('===========directly update sale order status from BROWSER_CALLBACK to PROCESSING, newStatus: ' . $newStatus);
			}
			$this->updateRow($id, array(
				// 'reason' => $reason,
				'show_reason_to_player' => $show_reason_to_player,
				'status' => $newStatus,
				'processed_by' => $adminUserId,
				'processed_approved_time' => $this->utils->getNowForMysql(),
				'player_submit_datetime' => $this->utils->getNowForMysql(),
				'updated_at' => $this->utils->getNowForMysql(),
				'process_time' => $this->utils->getNowForMysql(),
			));

			# add action log to sale_orders_notes content
			$this->sale_orders_notes->add($actionLog, Users::SUPER_ADMIN_ID, Sale_orders_notes::ACTION_LOG, $saleOrder->id);
			$this->sale_orders_timelog->add($saleOrder->id, Sale_orders_timelog::ADMIN_USER, $adminUserId, array('before_status' => $saleOrder->status, 'after_status' => $newStatus));

			if ($newStatus == self::STATUS_SETTLED) {
				//approve
                $this->load->model(array('sale_orders_status_history'));
                $this->sale_orders_status_history->createSaleOrderStatusHistory($id,Sale_order::DEPOSIT_STATUS_APPROVE_SALE_ORDER);
				$this->utils->debug_log(self::DEBUG_TAG, 'sever callback processApproveSaleOrder', $id);
				$rlt = $this->processApproveSaleOrder($saleOrder, $extra_info);

				if($rlt){
					$this->utils->debug_log(self::DEBUG_TAG, 'incApprovedDepositCount ', $saleOrder->player_id);
					$this->player_model->incApprovedDepositCount($saleOrder->player_id);
					$this->load->model(array('payment_account'));
					$this->payment_account->updateDeposit($saleOrder->payment_account_id);
				}
			}
			else if($newStatus == self::STATUS_BROWSER_CALLBACK) {
				$this->utils->debug_log(self::DEBUG_TAG, 'browser callback but not processApproveSaleOrder', $id);
				$rlt = false;
			}
			else if ($newStatus == self::STATUS_DECLINED) {

				$this->utils->debug_log(self::DEBUG_TAG, 'processDeclineSaleOrder', $id);
				$rlt = $this->processDeclineSaleOrder($saleOrder);
				$this->utils->debug_log(self::DEBUG_TAG, 'incDeclinedDepositCount ', $saleOrder->player_id);
				if($rlt){
					$this->player_model->incDeclinedDepositCount($saleOrder->player_id);
				}
			}
			else {
				$rlt = true;
			}

			// $this->endTrans();
			// $rlt = $this->succInTrans();
		} else {
			log_message('error', '[updateSaleOrderResult] lost sale order:' . $id . ' status:' . $newStatus);
			$this->utils->debug_log('approve sale order ' . $id);
		}
		return $rlt;
	}

	/**
	 * @deprecated
	 */
	private function processDeclineSaleOrder($saleOrder) {
		// $this->load->model(array('player_promo'));
		// $playerpromoId = $saleOrder->player_promo_id;

		// if (!empty($playerpromoId)) {
		// 	$this->utils->debug_log(self::DEBUG_TAG, 'declinePlayerPromotion', $saleOrder->id, $playerpromoId);
		// 	$this->player_promo->declinePlayerPromotion($playerpromoId);
		// }

		return true;
	}

	private function processPlayerPromotion($saleOrder){
		$success=false;
		$message=null;
		// $playerPromoId=$saleOrder->player_promo_id;
		// if(empty($playerPromoId)){
			//try promo rule and promo cms
			$promo_rules_id=$saleOrder->promo_rules_id;
			$promo_cms_id=$saleOrder->promo_cms_id;
			$this->utils->debug_log('processPlayerPromotion-1', $saleOrder->promo_rules_id, $saleOrder->promo_cms_id);
            if(empty($promo_cms_id)){
                $this->load->model(['player_promo']);
                $player_promo_info = $this->player_promo->getPlayerPromo($saleOrder->player_promo_id);
                $this->utils->debug_log('processPlayerPromotion-2', $player_promo_info);
                if(!empty($player_promo_info)){
                    $promo_rules_id = $player_promo_info->promorulesId;
                    $promo_cms_id = $player_promo_info->promoCmsSettingId;
                    $this->utils->debug_log('processPlayerPromotion-3', $promo_rules_id, $promo_cms_id);
                }
            }
			$this->load->model(['promorules']);

			list($promorule, $promoCmsSettingId)=$this->promorules->getByCmsPromoCodeOrId($promo_cms_id);
			if(!empty($promorule) && !empty($promoCmsSettingId)){
                $extra_info = [
                    'saleOrder' => $saleOrder
                ];
				list($success, $message)=$this->promorules->triggerPromotionFromDeposit($saleOrder->player_id, $promorule,
					$promoCmsSettingId, false, $saleOrder->player_promo_id, $extra_info);
			}

		// }
		return [$success, $message];
	}

	/**
	 * detail: process all approved sale orders
	 *
	 * @param object $saleOrder sale order information
	 * @param string $extra_info sale order extra information
	 *
	 * @return boolean
	 */
	private function processApproveSaleOrder($saleOrder, &$extra_info=null) {
		$this->load->model(array('sale_order', 'wallet_model', 'transactions', 'player_promo', 'promorules',
			'withdraw_condition', 'operatorglobalsettings', 'player_model', 'payment_account', 'users'));

		if(!is_array($extra_info)){
			$extra_info=[];
		}

		//ignore promotion, check withdraw condition
		$only_add_balance=isset($extra_info['only_add_balance']) && $extra_info['only_add_balance'];

		$approve_promotion=true;
		if(isset($extra_info['approve_promotion'])){
			$approve_promotion=$extra_info['approve_promotion'];
		}


		$success=true;
		$message=null;

		$unfinished_is_false=false;
		$clean_condition=true;
		$success=$this->withdraw_condition->autoCheckWithdrawConditionAndMoveBigWallet($saleOrder->player_id, $message,
			null, $unfinished_is_false, $clean_condition, $saleOrder->secure_id);
		if(!$success){
			$extra_info['error_message']='Auto Check Withdraw Condition Failed';
		}

        $this->utils->debug_log('success', $success, 'message', $message, $saleOrder->id, $saleOrder->secure_id,'player_id', $saleOrder->player_id);

		$playerId = $saleOrder->player_id;

		$this->utils->debug_log(self::DEBUG_TAG, 'incMainWallet', $saleOrder->player_id, $saleOrder->amount);

		$depositAmount = $saleOrder->amount;
		//add to balance and create transaction
		$loggedAdminUserId = method_exists($this->authentication, 'getUserId') ? $this->authentication->getUserId() : Users::SUPER_ADMIN_ID;
		$this->utils->debug_log('createDepositTransaction', $saleOrder->id, $loggedAdminUserId);
		$depositTransId = $this->transactions->createDepositTransaction($saleOrder, $loggedAdminUserId, null, Transactions::MANUAL, null);
		$success=!!$depositTransId;
		if(!$success){
			$extra_info['error_message']='Create deposit transaction failed';
			$this->utils->error_log('create deposit transaction failed', $saleOrder->id, $depositTransId);
			return $success;
		}
		//update transaction id to sale order
		$this->utils->debug_log(self::DEBUG_TAG, 'updateTransactionId', $saleOrder->id, $depositTransId);
		$saleOrder->transaction_id = $depositTransId;
		$this->updateTransactionId($saleOrder->id, $depositTransId);


		if($only_add_balance){
			//ignore promotion
		}else{
            $disabled_promotion = FALSE;
            if($this->player_model->isDisabledPromotion($playerId)){
                $this->utils->debug_log('disabled promotion on player id:' . $playerId);
                $disabled_promotion = TRUE;
            }

            $appliedPromo = FALSE;

			//process auto deposit bonus
			$payment_account_id=$saleOrder->payment_account_id;
			$payment_account=$this->payment_account->getPaymentAccount($payment_account_id);
			if(!$disabled_promotion){
				if($payment_account){
					//promocms_id
					$promocms_id=$payment_account->promocms_id;
					if(!empty($promocms_id)){
                        $extra_info['is_payment_account_promo'] = TRUE;
                        $extra_info['ignore_bind_transaction_with_promo'] = TRUE;

						//add promotion
						try{
							$promorule=$this->promorules->getPromoruleByPromoCms($promocms_id);
							$preapplication=false;
							//pass transaction id
							$extra_info['depositAmount']=$depositAmount;
		                    $extra_info['depositTranId']=$depositTransId;

							//release bonus
							list($succ, $msg)=$this->promorules->triggerPromotionFromDeposit($playerId, $promorule, $promocms_id,
								$preapplication, null, $extra_info);
							$this->utils->debug_log('apply promo result on order:'.$saleOrder->id, $succ, $msg);
							if($succ){
								$appliedPromo=true;
							} elseif ($approve_promotion) {
								$extra_info['apply_promo_success']=$succ;
								$extra_info['apply_promo_message']=lang($msg);
								$this->utils->error_log('apply promo failed on deposit', $succ, $msg);
							}
						}catch(WrongBonusException $e){
							$this->utils->error_log($e);
						}
                        $extra_info['is_payment_account_promo'] = FALSE;
					}

				}else{
					//lost $payment_account
					$this->utils->error_log('lost payment_account by id ', $payment_account_id);
				}
			}

			if(!$appliedPromo){
				//if no any promo , just add withdraw condition
				$nonPromoWithdrawSettingVal = $this->operatorglobalsettings->getSettingIntValue('non_promo_withdraw_setting');
				$withdrawBetAmtCondition = $saleOrder->amount * $nonPromoWithdrawSettingVal;

				$this->utils->debug_log('createWithdrawConditionForDepositOnly', $depositTransId, $withdrawBetAmtCondition, $nonPromoWithdrawSettingVal . '*' . $saleOrder->amount);
				$this->withdraw_condition->createWithdrawConditionForDepositOnly($saleOrder, $depositTransId, $withdrawBetAmtCondition, $nonPromoWithdrawSettingVal);
			}

			//get player info
			$player = $saleOrder->player;

			$this->utils->debug_log(self::DEBUG_TAG, 'existsDisabledRefereeBy', $player->playerId);
			if ($this->player_model->existsDisabledRefereeBy($player->playerId)) {
				$friend_referral_settings = $this->operatorglobalsettings->getFriendReferralSettings();
				$totalDepositAmount = $this->transactions->getPlayerTotalDeposits($player->playerId);

				$this->utils->debug_log(self::DEBUG_TAG, 'check rules of friend_referral_settings', $player->playerId, $friend_referral_settings, $totalDepositAmount, $player->totalBettingAmount);
				if ($totalDepositAmount >= $friend_referral_settings->ruleInDeposit && $player->totalBettingAmount >= $friend_referral_settings->ruleInBet) {

					$this->utils->debug_log(self::DEBUG_TAG, 'enablePlayerReferral', $player->playerId);
					$this->player_model->enablePlayerReferral($player->playerId);
				}
			}
		}

		$this->utils->debug_log(self::DEBUG_TAG, 'checkPaymentAccountLimit to disable', $saleOrder->payment_account_id);
		$paymentAccount = $this->payment_account->checkPaymentAccountLimit($saleOrder->payment_account_id);

		# Upon sales order approval, create transaction fee as defined in payment_account
		$transFee = $this->payment_account->getTransactionFee($saleOrder->payment_account_id, $saleOrder->amount);
		if($this->utils->compareResultFloat($transFee, '>=', 0.01)) { # 0.01 to workaround floating point inaccuracy
			$this->transactions->createTransactionFee($transFee, "deposit", $loggedAdminUserId, $player->playerId,
			$saleOrder->transaction_id, null, null, $saleOrder->id, Transactions::FEE_FOR_PLAYER, Transactions::PROGRAM);
		}

		return $success;
	}

    public function approveSaleOrderSubWalletWithLock($id, &$extra_info = NULL){
        $this->load->model(array('wallet_model', 'users'));

        if(!isset($extra_info['approve_SubWallet']) || !$extra_info['approve_SubWallet']){
            return TRUE;
        }

        $adminUserId = NULL;
        if(method_exists($this->authentication, 'getUserId')){
            $adminUserId = $this->authentication->getUserId();
        }

        if(empty($adminUserId)){
            //get super admin
            $adminUserId = $this->users->getSuperAdminId();
        }

        $saleOrder = $this->getSaleOrderWithPlayerById($id);
        if (empty($saleOrder)){
            return FALSE;
        }

        $rlt = FALSE;

        $playerId = $saleOrder->player_id;

        $transfer_to = $saleOrder->sub_wallet_id;
        if(Wallet_model::MAIN_WALLET_ID === (int)$transfer_to){
            return TRUE;
        }

        if ($this->utils->existsSubWallet($transfer_to)) {
            // $wallet_type='bonus';
            $username = $this->player_model->getUsernameById($playerId);
            $transfer_from = Wallet_model::MAIN_WALLET_ID;

			$rlt = $this->utils->transferWallet($playerId, $username, $transfer_from, $transfer_to, $saleOrder->amount, $adminUserId);
            // $rlt = $this->utils->transferWalletWithoutLock($playerId, $username, $transfer_to, $transfer_from, $transfer_to, $saleOrder->amount, $adminUserId);

            $success = $rlt['success'];

            $rlt = $success;

            $this->utils->debug_log('transfer to subwallet result', $rlt, $playerId);
        } else {
            $this->utils->error_log('transfer subwallet failed, does not exist', $transfer_to);
        }

        if(!$rlt){
            $extra_info['error_message'] = 'notify.approvedSaleOrderSubWalletFailed';
        }

        return $rlt;
    }

    public function approveSaleOrderPlayerPromotionWithLock($id, &$extra_info = NULL){
        $this->load->model(['player_model', 'users']);

        $adminUserId = NULL;
        if(method_exists($this->authentication, 'getUserId')){
            $adminUserId = $this->authentication->getUserId();
        }

        if(empty($adminUserId)){
            //get super admin
            $adminUserId = $this->users->getSuperAdminId();
        }

        $saleOrder = $this->getSaleOrderWithPlayerById($id);
        if(empty($saleOrder)){
            return FALSE;
        }

        $playerId = $saleOrder->player_id;

        $rlt = FALSE;

        $approve_promotion = TRUE;
        if(isset($extra_info['approve_promotion'])){
            $approve_promotion = $extra_info['approve_promotion'];
        }

        //player self pick promotion from deposit
        if(!$approve_promotion && !empty($saleOrder->promo_rules_id) && !empty($saleOrder->promo_cms_id)){
            $approve_promotion = TRUE;
        }

        $disabled_promotion = FALSE;
        if($this->player_model->isDisabledPromotion($playerId)){
            $this->utils->debug_log('disabled promotion on player id:' . $playerId);
            $disabled_promotion = TRUE;
        }

        $appliedPromo = FALSE;
        $this->utils->debug_log('approve_promotion', $approve_promotion, 'disabled_promotion', $disabled_promotion);
        if(!$disabled_promotion && $approve_promotion){
        	$msg=null;
        	$succ=$this->lockAndTransForPlayerBalance($playerId, function()
        			use($saleOrder, &$msg){
	            list($succ, $msg) = $this->processPlayerPromotion($saleOrder);
	            return $succ;
        	});

            log_message('debug', 'process promotion on sale order:' . $saleOrder->id,
                ['succ' => $succ, 'msg' => $msg,
                    'promo_rules_id' => $saleOrder->promo_rules_id,
                    'promo_cms_id' => $saleOrder->promo_cms_id,
                    'player_promo_id' => $saleOrder->player_promo_id]);

            $extra_info['apply_promo_success'] = $succ;
            $extra_info['apply_promo_message'] = lang($msg);

            $appliedPromo = $succ;
        }else{
            $appliedPromo = TRUE;
        }

        if($appliedPromo){
            $rlt = TRUE;
        }

        return $rlt;
    }

    /**
	 * detail: get the player bonuses
	 *
	 * @param int $player_id player id
	 * @return array
	 */
	private function getMemberGroupBonus($player_id) {
		$this->db->select('vipsettingcashbackrule.*')->from('vipsettingcashbackrule');
		$this->db->join('player', 'player.levelId = vipsettingcashbackrule.vipsettingcashbackruleId', 'left');
		$this->db->where('player.playerId', $player_id);
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * detail: checking sale order status
	 *
	 * @param int $id sale order id
	 * @param int $type
	 * @return boolean
	 */
	public function checkingSaleOrder($id, $type) {

		// $this->startTrans();
		if ($type) {
			return $this->updateRow($id, array('status' => self::STATUS_PROCESSING,
				'processed_by' => $this->authentication->getUserId(),
				'processed_checking_time' => $this->utils->getNowForMysql(),
				'updated_at' => $this->utils->getNowForMysql(),
				'process_time' => $this->utils->getNowForMysql(),
			));
		}
		$oldStatus = $this->getStatus($id);

		if ($oldStatus == self::STATUS_PROCESSING) {
			return $this->updateRow($id, array('status' => self::STATUS_CHECKING,
				'processed_by' => $this->authentication->getUserId(),
				'processed_checking_time' => $this->utils->getNowForMysql()));
		}

		return false;
	}

	/**
	 * detail: declined a certain sale order with respective reason
	 *
	 * @param int $id sale order id
	 * @param string $reason sale order reason
	 * @param int $show_reason_to_player sale order show reason to player field
	 *
	 * @return boolean
	 */
	public function declineSaleOrder($id, $reason, $show_reason_to_player) {
		return $this->updateSaleOrderResult($id, $reason, $show_reason_to_player, self::STATUS_DECLINED);
	}

	/**
	 * detail: call bank function for browser request
	 *
	 * @param int $id sale order id
	 * @param string $reason sale order reason
	 * @param int $show_reason_to_player sale order show reason to player field
	 * @return boolean
	 */
	public function browserCallbackSaleOrder($id, $reason = null, $show_reason_to_player = null) {
		return $this->updateSaleOrderResult($id, $reason, $show_reason_to_player, self::STATUS_BROWSER_CALLBACK);
	}

	/**
	 * detail: approved a certain sale order record
	 *
	 * @param int $id sale order id
	 * @param string $reason sale order reason
	 * @param int $show_reason_to_player sale order show reason to player field\
	 * @param string $extra_info sale order extra information
	 *
	 * @return boolean
	 */
	public function approveSaleOrder($id, $reason = null, $show_reason_to_player = null, &$extra_info=null) {
		return $this->updateSaleOrderResult($id, $reason, $show_reason_to_player, self::STATUS_SETTLED, $extra_info);
	}

	//====set status===============================================

	/**
	 * detail: set the status for the browser request
	 *
	 * @param int $id sale order id
	 *
	 * @return boolean
	 */
	public function setStatusToBrowserCallback($id) {
		return $this->setStatus($id, self::STATUS_BROWSER_CALLBACK);
	}

	/**
	 * detail: set the sale order status to settled
	 *
	 * @param int $id sale order id
	 *
	 * @return boolean
	 */
	public function setStatusToSettled($id) {
		return $this->setStatus($id, self::STATUS_SETTLED);
	}

	/**
	 * detail: set the sale order status to cancelled
	 *
	 * @param int $id sale order id
	 *
	 * @return boolean
	 */
	public function setStatusToCancelled($id) {
		return $this->setStatus($id, self::STATUS_CANCELLED);
	}

	/**
	 * detail: set the sale order status to failed
	 *
	 * @param int $id sale order id
	 *
	 * @return boolean
	 */
	public function setStatusToFailed($id) {
		return $this->setStatus($id, self::STATUS_FAILED);
	}

	/**
	 * detail: set the sale order status to declined
	 *
	 * @param int $id sale order id
	 *
	 * @return boolean
	 */
	public function setStatusToDeclined($id) {
		return $this->setStatus($id, self::STATUS_DECLINED);
	}

	/**
	 * detail: a reusable method to change the certain status providing the given parameter
	 *
	 * @param int $id sale order id
	 * @param int $status sale order status
	 *
	 * @return boolean
	 */
	public function setStatus($id, $status) {
		$this->utils->debug_log('set status to sale order, order id', $id, 'status', $status);

		return $this->db->update($this->tableName, array('status' => $status), array('id' => $id));
	}
	//====set status===============================================

	/**
	 * detail: get the status of a certain sale order
	 *
	 * @param int $id sale order id
	 * @return int
	 */
	public function getStatus($id) {
		$this->db->select('status')->from($this->tableName)->where('id', $id);
		return $this->runOneRowOneField('status');
	}

	/**
	 * detail: update information of a certain sale order (external_order_id, bank_order_id, status_payment_gateway, status_bank, response_result_id)
	 *
	 * note: parameters with out default values are mandatory
	 *
	 * @param int $id sale order id
	 * @param int $externalOrderId sale order external order id field
	 * @param int $bankOrderId sale order bank order id field
	 * @param string $statusPaymentGateway sale order status payment gateway field
	 * @param string $statusBank sale order status bank field
	 * @param int $response_result_id sale order response result id field
	 *
	 * @return boolean
	 */
	public function updateExternalInfo($id, $externalOrderId, $bankOrderId = null, $statusPaymentGateway = null, $statusBank = null, $response_result_id = null) {
		$updateFields = [
			'external_order_id' => $externalOrderId,
			'bank_order_id' => $bankOrderId,
			'status_payment_gateway' => $statusPaymentGateway,
			'status_bank' => $statusBank,
			'response_result_id' => $response_result_id,
		];

		# Null result will not overwrite existing data
		foreach ($updateFields as $key => $value) {
			if(is_null($value)){
				unset($updateFields[$key]);
			}
		}

		return $this->db->where('id', $id)->update($this->tableName, $updateFields);
	}

	# Saves QRCode link to sales order
	public function updateQRCodeLink($id, $qrCodeLink) {
		return $this->db->where('id', $id)->update($this->tableName,
			array('account_image_filepath' => $qrCodeLink)
		);
	}

	/**
	 * detail: check if the sale order is approved
	 *
	 * @param int $status sale order status field
	 *
	 * @return int
	 */
	public function isApproved($status) {
		return $status == self::STATUS_BROWSER_CALLBACK || $status == self::STATUS_SETTLED;
	}

	/**
	 * detail: check if the sale order is declined
	 *
	 * @param int $status sale order status field
	 *
	 * @return int
	 */
	public function isDeclined($status) {
		return $status == self::STATUS_DECLINED || $status == self::STATUS_CANCELLED || $status == self::STATUS_FAILED;
	}

	/**
	 * detail: get the corresponding status value/text
	 *
	 * @param int $status
	 *
	 * @return string
	 */
	public function getStatusText($status) {
		switch ($status) {
		case self::STATUS_PROCESSING:
			return self::VIEW_STATUS_REQUEST;
			break;

		case self::STATUS_BROWSER_CALLBACK:
		case self::STATUS_SETTLED:
			return self::VIEW_STATUS_APPROVED;
			break;

		case self::STATUS_DECLINED:
			return self::VIEW_STATUS_DECLINED;
			break;
		}
		return self::VIEW_STATUS_REQUEST;
	}

	/**
	 * detail: get the corresponding where clause for a certain status
	 *
	 * @param string $statusStr
	 *
	 * @return void
	 */
	private function initStatusWhere($statusStr) {
		if ($statusStr == self::VIEW_STATUS_REQUEST || $statusStr == self::VIEW_STATUS_REQUEST_ALL) {
			$this->db->where('(' . $this->tableName . '.status=' . self::STATUS_PROCESSING .' or ' .$this->tableName . '.status=' .self::STATUS_CHECKING .')');
		} else if ($statusStr == self::VIEW_STATUS_APPROVED) {
			$this->db->where('(' . $this->tableName . '.status=' . self::STATUS_BROWSER_CALLBACK .' or ' .$this->tableName . '.status=' .self::STATUS_SETTLED .')');
		} else if ($statusStr == self::VIEW_STATUS_DECLINED) {
			$this->db->where($this->tableName . '.status', self::STATUS_DECLINED);
		}
	}

	/**
	 * detail: get the corresponding where clause for a certain date ranges
	 *
	 * @param string $dateRangeValueStart
	 * @param string $dateRangeValueEnd
	 *
	 * @return void
	 */
	private function initDateRangeWhere($dateRangeValueStart, $dateRangeValueEnd, $dateColumn= self::COLUMN_CREATED_AT) {

		if ($dateRangeValueStart != '') {
			$this->db->where("{$this->tableName}.{$dateColumn} >= ", $dateRangeValueStart);
			// $this->db->where($this->tableName . '.created_at >= ', $dateRangeValueStart);
		}
		if ($dateRangeValueEnd != '') {
			$this->db->where("{$this->tableName}.{$dateColumn} <= ", $dateRangeValueEnd);
			// $this->db->where($this->tableName . '.created_at <= ', $dateRangeValueEnd);
		}
	}

	public function countDepositRequests($depositCountList, $hide_timeout = null) {
		$PAY_MGMT_DEPOSIT_THIS_WEEK		= 1;
		$PAY_MGMT_DEPOSIT_THIS_MONTH	= 2;
		$PAY_MGMT_DEPOSIT_THIS_YEAR		= 3;
		$PAY_MGMT_DEPOSIT_TOTAL_ALL		= 4;

		// Determine date range when counting all requests by operator_settings
		$depositStartDate = '';
		$depositEndDate = '';
		switch ($depositCountList) {
			case $PAY_MGMT_DEPOSIT_THIS_WEEK :
				$depositStartDate = date("Y-m-d",strtotime('monday this week')). ' 00:00:00';
				$depositEndDate = date("Y-m-d",strtotime('sunday this week')). ' 23:59:59';
				break;
			case $PAY_MGMT_DEPOSIT_THIS_MONTH :
				$depositStartDate = date('Y-m-01 00:00:00',strtotime('this month'));
				$depositEndDate = date('Y-m-t 12:59:59',strtotime('this month'));
				break;
			case $PAY_MGMT_DEPOSIT_THIS_YEAR :
				$depositStartDate = date('Y-01-01 00:00:00',strtotime('this year'));
				$depositEndDate = date('Y-12-t 12:59:59',strtotime('this year'));
				break;
			case $PAY_MGMT_DEPOSIT_TOTAL_ALL : default :
				break;
		}

		$this->utils->debug_log(__METHOD__, 'deposit_interval', [ $depositStartDate, $depositEndDate ]);

		$start_today = date('Y-m-d 00:00:00');
		$end_today = date('Y-m-d 23:59:59');

		$rcount = [];
		// OGP-11757
		// Pending all-time (ignore start/end dates)
		$rcount['deposit_request_cnt'] = $this->countSaleOrders(self::PAYMENT_KIND_DEPOSIT, self::VIEW_STATUS_REQUEST_ALL,
           $depositStartDate, $depositEndDate, null, null, self::COLUMN_CREATED_AT, null);
		// Pending Today: LOCAL_BANK_OFFLINE + MANUAL_ONLINE_PAYMENT
		$rcount['deposit_request_cnt_today_manual'] = $this->countSaleOrders(self::PAYMENT_KIND_DEPOSIT, self::VIEW_STATUS_REQUEST,
            $start_today, $end_today, LOCAL_BANK_OFFLINE, null, self::COLUMN_CREATED_AT);
		// Pending Today: AUTO_ONLINE_PAYMENT
		$rcount['deposit_request_cnt_today_auto'] = $this->countSaleOrders(self::PAYMENT_KIND_DEPOSIT, self::VIEW_STATUS_REQUEST,
            $start_today, $end_today, AUTO_ONLINE_PAYMENT, null, self::COLUMN_CREATED_AT);


		$rcount['deposit_approved_cnt'] = $this->countSaleOrders(self::PAYMENT_KIND_DEPOSIT, self::VIEW_STATUS_APPROVED, $depositStartDate, $depositEndDate, null, null, self::COLUMN_UPDATED_AT);
		$rcount['deposit_approved_cnt_today'] = $this->countSaleOrders(self::PAYMENT_KIND_DEPOSIT, self::VIEW_STATUS_APPROVED, $start_today, $end_today, null, null, self::COLUMN_UPDATED_AT);

		$rcount['deposit_declined_cnt'] = $this->countSaleOrders(self::PAYMENT_KIND_DEPOSIT, self::VIEW_STATUS_DECLINED, $depositStartDate, $depositEndDate, null, null, self::COLUMN_UPDATED_AT);
		$rcount['deposit_declined_cnt_today'] = $this->countSaleOrders(self::PAYMENT_KIND_DEPOSIT, self::VIEW_STATUS_DECLINED, $start_today, $end_today, null, null, self::COLUMN_UPDATED_AT);

		$rcount['total_deposit_request_cnt_today_auto_and_manual'] = $rcount['deposit_request_cnt_today_manual'] + $rcount['deposit_request_cnt_today_auto'];

		foreach ($rcount as & $rc) {
			$rc = $this->utils->formatInt($rc);
		}

		return $rcount;
	}

	/**
	 * detail: count all the records of sale orders filtered by date, payment kind, status
	 *
	 * note: parameters with out default values are mandatory
	 *
	 * @param int $paymentKind sale order payment kind field
	 * @param string $statusStr
	 * @param string $dateRangeValueStart
	 * @param string $dateRangeValueEnd
	 *
	 * @return int
	 */
	public function countSaleOrders($paymentKind, $statusStr, $dateRangeValueStart = '', $dateRangeValueEnd = '', $paymentType = '', $approvedOnly = false, $dateRangeUsingField = null, $hide_timeout = null) {
		$this->load->model(array('payment_account'));

		$this->db->select('COUNT(sale_orders.id) as numrows');
		$this->db->from($this->tableName);
		$this->db->join('payment_account', 'payment_account.id=sale_orders.payment_account_id', 'left');
		$this->db->join('player', 'player.playerId=sale_orders.player_id', 'left');
		$this->initStatusWhere($statusStr);
		$this->initDateRangeWhere($dateRangeValueStart, $dateRangeValueEnd, $dateRangeUsingField);

		if ($paymentType == AUTO_ONLINE_PAYMENT) {
			$this->db->where('payment_account.flag',AUTO_ONLINE_PAYMENT);
		}
		elseif ($paymentType == LOCAL_BANK_OFFLINE) {
			//LOCAL_BANK_OFFLINE means LOCAL_BANK_OFFLINE, MANUAL_ONLINE_PAYMENT both
			$this->db->where('(payment_account.flag = ' . LOCAL_BANK_OFFLINE .' or payment_account.flag = ' . MANUAL_ONLINE_PAYMENT .')');
		}
		$this->db->where('payment_account.flag IS NOT NULL', null, false);
		$this->db->where('payment_account.status !=', Payment_account::STATUS_DELETE);

		$this->db->where('sale_orders.payment_kind', $paymentKind);

		# Exclude player conditions
		$this->db->where("player.deleted_at IS NULL");

		$count = $this->runOneRowArray();

		return  $count['numrows'];
	}

	public function countSaleOrdersByPlayerId($player_id) {
		$sql = "SELECT COUNT(id) as totalDepositCount FROM sale_orders WHERE player_id = ? AND status = ?";
		$query = $this->db->query($sql, array($player_id, self::STATUS_SETTLED));

		return $query->first_row()->totalDepositCount;
	}

	public function getLastSaleOrderUpdatedAt($player_id) {
		$sql = "SELECT updated_at FROM sale_orders WHERE player_id = ? AND status = ? ORDER BY updated_at DESC Limit 1";
		$query = $this->db->query($sql, array($player_id, self::STATUS_SETTLED));

		return $query->first_row()->updated_at;
	}

    public function countSaleOrdersForNotifcation($paymentKind, $statusStr, $dateRangeValueStart = '', $dateRangeValueEnd = '', $paymentType = '', $approvedOnly = false, $dateRangeUsingField = null) {
        $this->db->join('payment_account', 'payment_account.id=sale_orders.payment_account_id', 'left');
        $this->db->join('player', 'player.playerId=sale_orders.player_id', 'left');
        $this->initStatusWhere($statusStr);
        $this->initDateRangeWhere($dateRangeValueStart, $dateRangeValueEnd,
            $dateRangeUsingField);

        if ($paymentType == AUTO_ONLINE_PAYMENT) {
            $this->db->where('payment_account.flag',AUTO_ONLINE_PAYMENT);
        }
        elseif ($paymentType == LOCAL_BANK_OFFLINE || $paymentType == MANUAL_ONLINE_PAYMENT) {
            //LOCAL_BANK_OFFLINE means LOCAL_BANK_OFFLINE, MANUAL_ONLINE_PAYMENT both
            $this->db->where_in('payment_account.flag', [LOCAL_BANK_OFFLINE, MANUAL_ONLINE_PAYMENT]);
        }

        $this->db->where('payment_account.flag IS NOT NULL', null, false);

        $this->db->where('payment_kind', $paymentKind);
        if ($statusStr == Sale_order::VIEW_STATUS_REQUEST) {
            $this->db->where('timeout_at >', $this->utils->getNowForMysql());
            // $this->db->or_where('sale_orders.timeout_at IS NULL)', null, false);
        }

        if( $approvedOnly ) {
            $this->db->where('sale_orders.status IN (' . self::STATUS_BROWSER_CALLBACK . ',' . self::STATUS_SETTLED . ')');
        }

        # Exclude player conditions
        $this->db->where("player.deleted_at IS NULL");

        $count = $this->db->count_all_results($this->tableName);

        return $count;
    }

	/**
	 * detail: get sale order information filtered by date, payment kind, status
	 *
	 * note: parameters with out default values are mandatory
	 *
	 * @param int $paymentKind sale order payment kind field
	 * @param string $statusStr
	 * @param string $dateRangeValueStart
	 * @param string $dateRangeValueEnd
	 * @param int $limit
	 * @param int $offset
	 * @param boolean $excludeTimeout
	 *
	 * @return array
	 */
	public function getSaleOrders($paymentKind, $statusStr, $dateRangeValueStart, $dateRangeValueEnd, $limit, $offset = 0, $excludeTimeout = false) {
		$this->db->select('sale_orders.secure_id as secure_id, sale_orders.id as id, player.username, concat(playerdetails.firstName,", ",playerdetails.lastName) as realname, vipsettingcashbackrule.vipLevelName as level_name, ' .
			' sale_orders.amount, sale_orders.currency, sale_orders.payment_type_name, sale_orders.payment_account_name, sale_orders.payment_account_number, ' .
			' sale_orders.created_at, sale_orders.updated_at, sale_orders.ip, sale_orders.geo_location, ' .
			' sale_orders.player_payment_type_name, sale_orders.player_payment_account_name, sale_orders.player_payment_account_number, sale_orders.player_deposit_transaction_code, sale_orders.player_deposit_slip_path, ' .
			' sale_orders.status, adminusers.username as processed_by_admin, sale_orders.process_time, sale_orders.player_deposit_slip_path, sale_orders.timeout_at', false);
		$this->db->from($this->tableName);
		$this->db->join('player', 'player.playerId=sale_orders.player_id', 'left');
		$this->db->join('playerdetails', 'playerdetails.playerId=sale_orders.player_id', 'left');
		// $this->db->join('playerlevel', 'playerlevel.playerId=sale_orders.player_id', 'left');
		$this->db->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId=player.levelId', 'left');
		$this->db->join('adminusers', 'sale_orders.processed_by=adminusers.userId', 'left');

		# MODIFY WHERE STATEMENT
		if (!empty($dateRangeValueStart)) {
			$this->initDateRangeWhere($dateRangeValueStart, $dateRangeValueEnd);
			//$dateRangeValueStart = $dateRangeValueStart . ' 00:00:00';
			//$dateRangeValueEnd = $dateRangeValueEnd . ' 23:59:59';
			// $this->db->where('sale_orders.updated_at >= ', $dateRangeValueStart);
			// $this->db->where('sale_orders.updated_at <= ', $dateRangeValueEnd);
		}

		$this->initStatusWhere($statusStr);
		//$this->initDateRangeWhere($dateRangeValueStart, $dateRangeValueEnd);

		$this->db->where('payment_kind', $paymentKind);

		if ($excludeTimeout && $statusStr == self::VIEW_STATUS_REQUEST) {
			// $this->db->where('(sale_orders.payment_flag = ' . Payment_account::FLAG_AUTO_ONLINE_PAYMENT . ' OR timeout_at > \'' . $this->utils->getNowForMysql() . '\')', null, false);
			$this->db->where('timeout_at > ', $this->utils->getNowForMysql());
		}

		return $this->runMultipleRow();
	}

	/**
	 * detail: get the sale order information for a certion record
	 *
	 * note: $id is mandatory
	 *
	 * @param int $id sale order id
	 *
	 * @return array
	 */
	public function getSaleOrderDetailById($id) {
		$this->db->select('
			sale_orders.secure_id as secure_id,
			sale_orders.player_deposit_slip_path as deposit_slip,
			sale_orders.player_deposit_reference_no  as reference_no,
			sale_orders.player_deposit_time as player_deposit_time,
			payment_account.flag as player_deposit_method,
			payment_account.notes as collection_account_note,
			sale_orders.timeout_at,
			sale_orders.transaction_id as transaction_id,
			sale_orders.player_submit_datetime as player_deposit_datetime,
			player_attached_proof_file.file_name as deposit_receipt_file_name,
			(SELECT amount FROM transactions WHERE related_trans_id = transaction_id AND transaction_type = 3 ) as transaction_fee ,
			sale_orders.bank_order_id as bank_order_id,
			sale_orders.external_order_id as external_order_id,
			sale_orders.id as id,
			player.username,
			concat(COALESCE(playerdetails.firstName,""), " ", COALESCE(playerdetails.lastName,"")) as realname,
			player.createdOn as member_since,
			sale_orders.amount,
			sale_orders.currency,
			sale_orders.payment_type_name,
			sale_orders.payment_account_name,
			sale_orders.payment_account_number,
			sale_orders.payment_account_id,
			sale_orders.created_at,
			sale_orders.updated_at,
			sale_orders.ip,
			sale_orders.geo_location,
			concat(sale_orders.ip," ", sale_orders.geo_location) as loc_info,
			sale_orders.player_payment_type_name,
			sale_orders.player_payment_account_name,
			sale_orders.player_payment_account_number,
			sale_orders.player_deposit_transaction_code,
			sale_orders.player_payment_branch_name,
			sale_orders.status,
			adminusers.userId as processed_by_userid,
			adminusers.username as processed_by_admin,
			sale_orders.process_time,
			sale_orders.processed_approved_time,
			sale_orders.processed_checking_time,
			sale_orders.player_deposit_slip_path,
			player.approved_deposit_count,"" as promo_name,
			0 as promo_bonus_amount,
			sale_orders.player_id,
			sale_orders.reason ,
			sale_orders.sub_wallet_id,
			sale_orders.player_promo_id,
			sale_orders.group_level_id as group_level_id,
			sale_orders.player_mode_of_deposit as player_mode_of_deposit,
			sale_orders.system_id
		', false);
		$this->db->from($this->tableName);
		$this->db->join('player', 'player.playerId = sale_orders.player_id', 'left');
		$this->db->join('playerdetails', 'playerdetails.playerId = sale_orders.player_id', 'left');
		$this->db->join('adminusers', 'sale_orders.processed_by = adminusers.userId', 'left');
		$this->db->join('payment_account', 'sale_orders.payment_account_id = payment_account.id', 'left');
		$this->db->join('player_attached_proof_file', 'sale_orders.id = player_attached_proof_file.sales_order_id', 'left');

		$this->db->where($this->tableName . '.id', $id);

		return $this->runOneRow();
	}

	/**
	 * detail: update the transaction id of a certain sale order record
	 *
	 * @param int $saleOderId sale order id
	 * @param int $depositTransId
	 *
	 * @return boolean
	 */
	public function updateTransactionId($saleOrderId, $depositTransId) {
		return $this->updateRow($saleOrderId, array('transaction_id' => $depositTransId));
	}

	/**
	 * detail: get sale order with player for a certain sale order record
	 *
	 * @param int $saleOrderId sale order id field
	 *
	 * @return array
	 */
	public function getSaleOrderWithPlayerById($saleOrderId) {
		$saleOrder = $this->getSaleOrderById($saleOrderId);
		if ($saleOrder) {
			$this->load->model(array('player_model'));
			$saleOrder->player = $this->player_model->getPlayerById($saleOrder->player_id);
		}
		return $saleOrder;
	}

	/**
	 *
	 * detail: checking member deposit bunoses
	 *
	 * @param object $depositDetails
	 * @param int $adminUserId
	 *
	 * @return boolean
	 */
	public function checkMemberDepositBonus($depositDetails, $adminUserId = null) {
		$this->load->model(array('player_model', 'wallet_model', 'transactions', 'withdraw_condition', 'group_level'));
		$appliedPromo=false;

		// $depositDetails = $this->getSaleOrderById($orderId);
		$playerId = $depositDetails->player_id;
		$depositCnt = $this->transactions->countDepositByPlayer($depositDetails->player_id);
		$bonusInfo = $this->player_model->getMemberGroupBonus($depositDetails->player_id);
		$firstDepositCnt = 1;

		$depositCntType = $depositCnt <= $firstDepositCnt ? Group_level::DEPOSIT_FIRST : Group_level::DEPOSIT_SUCCEEDING;

		$this->utils->debug_log('playerId', $playerId, 'depositCnt', $depositCnt, 'bonusInfo', count($bonusInfo), 'depositCntType', $depositCntType);

		if ($bonusInfo['bonus_mode_deposit'] == Group_level::BONUS_MODE_ENABLE) {
			$admin = $adminUserId ? $adminUserId : 1;
			if ($depositCntType == Group_level::DEPOSIT_FIRST) {
				if ($bonusInfo['firsttime_dep_type'] == Group_level::BONUS_TYPE_FIXAMOUNT) {
					$firstTimeDepositBonus = $bonusInfo['firsttime_dep_bonus'];
				} else {
					$firstTimeDepositBonus = $depositDetails->amount * $bonusInfo['firsttime_dep_bonus'] / 100;
					$firstTimeDepositBonus = $firstTimeDepositBonus >= $bonusInfo['firsttime_dep_percentage_upto'] ? $bonusInfo['firsttime_dep_percentage_upto'] : $firstTimeDepositBonus;
				}
				if ($firstTimeDepositBonus) {
					$beforeBalance = $this->wallet_model->getMainWalletBalance($depositDetails->player_id);
					$totalBeforeBalance = $this->wallet_model->getTotalBalance($playerId);
					$note = 'admin user: ' . $admin . ', added first time deposit bonus amount of: ' . $depositDetails->amount . ' to ' . $depositDetails->player_id;
					$this->utils->debug_log('first time deposit', $note);
					$bonusTransId = $this->transactions->createDepositBonusTransaction($admin, $depositDetails->player_id,
						$firstTimeDepositBonus, $beforeBalance, $note, $depositDetails->id, Transactions::MANUAL, $totalBeforeBalance);
					if(empty($bonusTransId)){
						return false;
					}

					$withdrawalCondition = $bonusInfo['firsttime_dep_withdraw_condition'] * ($firstTimeDepositBonus + $depositDetails->amount);
					$this->withdraw_condition->createWithdrawConditionForMemberGroupDepositBonus($depositDetails->player_id, $bonusTransId, $withdrawalCondition,
						$depositDetails->amount, $firstTimeDepositBonus, $bonusInfo['firsttime_dep_withdraw_condition']);
					$appliedPromo=true;
					//bonus save to main wallet, move to transaction
					// $this->wallet_model->incMainWallet($depositDetails->player_id, $firstTimeDepositBonus);
				} else {
					$this->utils->debug_log('ignore firstTimeDepositBonus', $firstTimeDepositBonus);
				}
			} else if ($depositCntType == Group_level::DEPOSIT_SUCCEEDING) {
				if ($bonusInfo['succeeding_dep_type'] == Group_level::BONUS_TYPE_FIXAMOUNT) {
					$succeedingDepositBonus = $bonusInfo['succeeding_dep_bonus'];
				} else {
					$succeedingDepositBonus = $depositDetails->amount * $bonusInfo['succeeding_dep_bonus'] / 100;
					$succeedingDepositBonus = $succeedingDepositBonus >= $bonusInfo['succeeding_dep_percentage_upto'] ? $bonusInfo['succeeding_dep_percentage_upto'] : $succeedingDepositBonus;
				}
				if ($succeedingDepositBonus) {
					$beforeBalance = $this->wallet_model->getMainWalletBalance($depositDetails->player_id);
					$totalBeforeBalance = $this->wallet_model->getTotalBalance($playerId);
					$note = 'admin user: ' . $admin . ', added succeeding deposit bonus amount (' . $succeedingDepositBonus . ') of: ' . $depositDetails->amount . ' to ' . $depositDetails->player_id;
					$this->utils->debug_log('succeeding deposit', $succeedingDepositBonus, $note);
					$bonusTransId = $this->transactions->createDepositBonusTransaction($admin, $depositDetails->player_id,
						$succeedingDepositBonus, $beforeBalance, $note, $depositDetails->id, Transactions::MANUAL, $totalBeforeBalance);
					if(empty($bonusTransId)){
						return false;
					}

					$withdrawalCondition = $bonusInfo['succeeding_dep_withdraw_condition'] * ($succeedingDepositBonus + $depositDetails->amount);
					$this->withdraw_condition->createWithdrawConditionForMemberGroupDepositBonus($depositDetails->player_id, $bonusTransId, $withdrawalCondition,
						$depositDetails->amount, $succeedingDepositBonus, $bonusInfo['succeeding_dep_withdraw_condition']);

					$appliedPromo=true;

					//bonus save to main wallet , move to transaction
					// $this->wallet_model->incMainWallet($depositDetails->player_id, $succeedingDepositBonus);
				} else {
					$this->utils->debug_log('ignore succeedingDepositBonus', $succeedingDepositBonus);
				}
			}
		}

		return 	$appliedPromo;
	}

	/**
	 * detail: check sale order if exists
	 *
	 * @param int $orderId sale order id
	 * @return array
	 */
	public function existsSaleOrder($orderId) {
		$this->db->from($this->tableName)->where('id', $orderId);
		return $this->runExistsResult();
	}

	/**
	 * detail: update player promo using transaction id
	 *
	 * @param int $tranId sale transaction id
	 * @param int $playerPromoId sale order player promo id
	 * @return boolean
	 */
	public function updatePlayerPromoIdByTranId($tranId, $playerPromoId) {
		$this->db->set('player_promo_id', $playerPromoId)
			->where('player_promo_id is null', null, false)
			->where('transaction_id', $tranId);

		return $this->runAnyUpdate($this->tableName);
	}

	/**
	 * detail: update player promo id of a certain sale order
	 *
	 * @param int $orderIds sale order id
	 * @param int $playerPromoId player promo id
	 * @return boolean
	 */
	public function updatePlayerPromoId($orderIds, $playerPromoId) {
		$this->db->set('player_promo_id', $playerPromoId)->where_in('id', $orderIds)->where('player_promo_id is null', null, false);

		return $this->runAnyUpdate($this->tableName);
	}

	/**
	 * detail: get all valid orders for a certain player and date range
	 *
	 * @param int $playerId player id
	 * @param string $startDate
	 * @param string $endDate
	 * @return array
	 */
	public function getValidOrders($playerId, $startDate, $endDate = null) {
		if (empty($endDate)) {
			$endDate = $this->getNowForMysql();
		}
		//search
		$this->db->from($this->tableName)->where('player_id', $playerId);
		$this->initDateRangeWhere($startDate, $endDate);

		return $this->runMultipleRow();
	}

	const DEBUG_TAG = '[sale_order]';

	/**
	 * detail: get newest sale orders
	 *
	 * @param array $rows
	 * @return array
	 */
	public function selectNewestSaleOrders($rows) {
		$today = date("Y-m-d H:i:s");
		$sql = 'SELECT * FROM sale_orders  WHERE created_at <= ? order by created_at DESC LIMIT ? ';
		$query = $this->db->query($sql, array($today, $rows));
		return array(
			'total' => $query->num_rows(),
			'data' => $query->result_array(),
		);
	}

	/**
	 * detail: update sale order amount to fix value
	 *
	 * @param int $id sale order id
	 * @param float $amount sale order amount
	 * @param string $notes sale order notes
	 * @return boolean
	 */
	public function fixOrderAmount($id, $amount, $notes) {
		$this->load->model(array('sale_orders_notes'));
		if(isset($id)){
			$this->sale_orders_notes->add($notes, Users::SUPER_ADMIN_ID, Sale_orders_notes::ACTION_LOG, $id);
		}
		$this->utils->debug_log('fixOrderAmount:'.$id, $amount, $notes);
		$this->db->set('amount', $amount)->where('id', $id);
		return $this->runAnyUpdate($this->tableName);
	}

	/**
	 * detail: clear/remove promo id's of certain player
	 *
	 * @param int $playerPromoId sale order player promo id
	 * @return boolean
	 */
	public function clearPlayerPromoId($playerPromoId) {
		$this->db->set('player_promo_id', 'null', false)->where('player_promo_id', $playerPromoId);
		return $this->runAnyUpdate('sale_orders');
	}

	/**
	 * detail: get sale order record by sercure id
	 *
	 * @param int $id sale order id
	 * @return array
	 */
	public function getSaleOrderBySecureId($id) {
		$this->db->from('sale_orders')->where('secure_id', $id);
		return $this->runOneRow();
	}

	public function getSaleOrderByBankOrderId($id) {
		$this->db->from('sale_orders')->where('RIGHT(bank_order_id, 23)=', $id);
		return $this->runOneRow();
	}

	public function getAggregatedPaymentPercentage() {
		//TODO
		$sql = "SELECT
			  payment_account_name AS name,
			  FORMAT(
			    (
			      SUM(amount) /
			      (SELECT
			        SUM(amount)
			      FROM
			        `sale_orders`)
			    ) * 100,
			    2
			  ) AS y
			FROM
			  `sale_orders`
			GROUP BY payment_account_id ";
		$query = $this->db->query($sql);
		$returnRow = $query->result_array();
		foreach($returnRow as $index => $row){
			$returnRow[$index]['y'] = floatval($row['y']);
		}
		return $returnRow;
	}

	public function updatePlayerBankDetailsId($orderId, $playerBankDetailsId){

		$this->load->model(['playerbankdetails', 'banktype']);
		$playerPayment = $this->playerbankdetails->getPlayerBankDetailsById($playerBankDetailsId);

		if (!empty($playerPayment)) {
			$playerBankName=null;
			$playerPaymentType = $this->banktype->getBankTypeById($playerPayment->bankTypeId);
			if(!empty($playerPaymentType)){
				$playerBankName = $playerPaymentType->bankName;
			}
			$playerBankAccountFullName = $playerPayment->bankAccountFullName;
			$playerBankAccountNumber = $playerPayment->bankAccountNumber;
			$playerBankAddress = $playerPayment->bankAddress;

			$this->db->set(
				['player_bank_details_id' => $playerBankDetailsId,
				'player_payment_type_name' => $playerBankName,
				'player_payment_account_name' => $playerBankAccountFullName,
				'player_payment_account_number' => $playerBankAccountNumber,
				'player_payment_branch_name' => $playerBankAddress])->where('id', $orderId);

			// $this->db->update();
			// $this->db->set('player_bank_details_id', $playerBankDetailsId)->where('id', $orderId);
			return $this->runAnyUpdate('sale_orders');
		}

		return false;
	}

	/**
	 * detail: check sale order if exists
	 *
	 * @param int $orderId sale order id
	 * @return array
	 */
	public function existsSaleOrderByExternalOrderId($external_order_id) {
		$this->db->from($this->tableName)->where('external_order_id', $external_order_id);
		return $this->runExistsResult();
	}

	public function getSaleOrderByExternalOrderId($external_order_id) {
		$this->db->from($this->tableName)->where('external_order_id', $external_order_id);
		return $this->runOneRow();
	}

	//exception_order
	public function createExceptionDeposit($external_system_id, $amount, $external_order_id, $external_order_datetime, $response_result_id,
			$player_bank_name, $player_bank_account_name, $player_bank_account_number, $player_bank_address,
			$collection_bank_name, $collection_bank_account_name, $collection_bank_account_number, $collection_bank_address,
			$params, $saleOrderId=null,$withdrawalOrderId=null,$remarks=null){

		$data=[
			'external_system_id'=>$external_system_id,
			'amount'=>$amount,
			'external_order_id'=>$external_order_id,
			'external_order_datetime'=>$external_order_datetime,
			'response_result_id'=>$response_result_id,
			'player_bank_name'=>$player_bank_name,
			'player_bank_account_name'=>$player_bank_account_name,
			'player_bank_account_number'=>$player_bank_account_number,
			'player_bank_address'=>$player_bank_address,
			'collection_bank_name'=>$collection_bank_name,
			'collection_bank_account_name'=>$collection_bank_account_name,
			'collection_bank_account_number'=>$collection_bank_account_number,
			'collection_bank_address'=>$collection_bank_address,
			'created_at'=>$this->utils->getNowForMysql(),
			'response_content'=> $this->utils->encodeJson($params),
			'sale_order_id'=>$saleOrderId,
			'withdrawal_order_id'=>$withdrawalOrderId,
			'remarks'=>$remarks,
		];

		return $this->insertData('exception_order', $data);
	}

	public function getPromoRuleFromPaymentAccount($payment_account_id){
		$this->load->model(['payment_account', 'promorules']);
		$result = null;
		$payment_account = $this->payment_account->getPaymentAccount($payment_account_id);
		if($payment_account){
			//promocms_id
			$promocms_id = $payment_account->promocms_id;
			if(!empty($promocms_id)){
				//add promotion
				$promorule = $this->promorules->getPromoruleByPromoCms($promocms_id);
				$result = [
					'promoName' => $promorule['promoName'],
					'promoDescription' => $promorule['promoDesc'],
					'bonusAmount' => null,
					'withdrawConditionAmount'=>null,
					'release_to_wallet'=>null
				];
			}
		} else {
			//lost $payment_account
			$this->utils->error_log('lost payment_account by id ', $payment_account_id);
		}

		return $result;
	}

	public function getApprovedThirdPartyDeposit(){
		$qobj = $this->db->select('sale_orders.id, sale_orders.payment_type_name, player.username, player.playerId')
						 ->from($this->tableName)
						 ->join('player', 'player.playerId = sale_orders.player_id')
						 ->join('payment_account', 'payment_account.id = sale_orders.payment_account_id')
						 ->where('payment_account.flag', AUTO_ONLINE_PAYMENT)
						 ->where('(sale_orders.status = "' . self::STATUS_SETTLED . '" OR sale_orders.status = "' . self::STATUS_BROWSER_CALLBACK . '")')

						 ->get();

		return $qobj->result_array();
	}

	public function userLockDeposit($salesOrderId, $userId){
		$this->db->set('locked_user_id', $userId)->where('id', $salesOrderId);

		return $this->runAnyUpdate('sale_orders');
	}

	public function checkDepositLocked($salesOrderId) {
		$this->db->from('sale_orders');
		$this->db->where('id', $salesOrderId);
		$this->db->where('status', self::STATUS_PROCESSING);

		$lockedUserId =$this->runOneRowOneField('locked_user_id');
		return $lockedUserId;
	}

	public function userUnlockDeposit($salesOrderId){

		$this->db->set('locked_user_id', null)->where('id', $salesOrderId);

		return $this->runAnyUpdate('sale_orders');
	}

	// recheck if there is done locked transactions
	public function unlockDoneTransaction($salesOrderId) {
		$this->db->from('sale_orders');
		$this->db->where('id', $salesOrderId);
		$this->db->where_in('status', [self::STATUS_SETTLED, self::STATUS_DECLINED]);

		$lockedUserId =$this->runOneRowOneField('locked_user_id');
		if ($lockedUserId) {
			$this->userUnlockDeposit($salesOrderId);
		}
	}

	public function batchUnlockTransactions($saleOrdersIds) {
		if(!empty($saleOrdersIds)) {
			$this->db->set('locked_user_id', null);
			$this->db->where_in('id', $saleOrdersIds);
			$this->db->update($this->tableName);
		}
	}

	public function generateSecureId() {
 		return $this->getSecureId($this->tableName, 'secure_id', true, 'D');
 	}

    public function getSecureId($tableName, $fldName, $needUnique = true, $prefix = null, $random_length = 12) {
        if($this->utils->isEnabledFeature('enable_change_deposit_transaction_ID_start_with_date')){
            $random_length = $this->utils->getConfig('get_secureid_random_length');
            $prefix = $prefix.date('Ymd');  //ex: D20180331
            $secureId = parent::getSecureId($tableName, $fldName,$needUnique,$prefix,$random_length);
            return $secureId;
        }else{
            return parent::getSecureId($tableName, $fldName, $needUnique, $prefix);
        }
    }

	public function getSaleOrderByPlayerId($playerId, $date_from = null, $date_to = null){
 		$this->db->where('player_id',$playerId);

		if(!empty($date_from)){
			$this->db->where('player_submit_datetime >=', $date_from);
		}

		if(!empty($date_to)){
			$this->db->where('player_submit_datetime <=', $date_to);
		}

 		$query = $this->db->get($this->tableName);
 		return $query->result_array();
 	}

    public function updateSaleOrderTransactionID($saleOrderID, $transactionID) {
        $this->db->where('id', $saleOrderID);
        $this->db->set('transaction_id', $transactionID);
        $this->db->update($this->tableName);
    }

    public function getLastUnfinishedManuallyDeposit($playerId){
        $this->db->select('*')
             ->from($this->tableName)
             ->where('player_id', $playerId)
             ->where('system_id', self::SYSTEM_ID_MANUAL)
             ->where('status', self::STATUS_PROCESSING)
             ->where('(timeout_at > "'.$this->utils->getNowForMysql().'" OR timeout_at IS NULL)')
             ->order_by('created_at','desc')
             ->limit(1);
        return $this->runOneRowArray();
    }

    public function adjustOrderDateTime($orderId, $requestDateTime, $approvedDateTime){
    	$success=false;
    	if(!empty($orderId)){
    		$this->db->where('id', $orderId)->set('created_at', $requestDateTime)
    		    ->set('processed_approved_time', $approvedDateTime)
    		    ->set('timeout_at', $approvedDateTime)
    		    ->set('process_time', $approvedDateTime);
    		$success=$this->runAnyUpdate('sale_orders');

    		//update transactions
    		$this->db->where('order_id', $orderId)
    		    ->set('created_at', $approvedDateTime)
    		    ->set('updated_at', $approvedDateTime)
    			->set('trans_date', substr($approvedDateTime, 0, 10))
    			->set('trans_year', substr($approvedDateTime, 0, 4))
    			->set('trans_year_month', substr($approvedDateTime, 0, 4).substr($approvedDateTime, 5, 2));
    		$success=$this->runAnyUpdate('transactions');
    	}
    	return $success;
    }

	public function createSimpleSaleOrder($playerId, $amount,
		$notes , $date = null, $payment_account_id = null) {

		$this->load->model(array('wallet_model', 'payment_account', 'banktype'));
		$secureId = $this->getSecureId($this->tableName, 'secure_id', true, 'D');

		$paymentKind=self::PAYMENT_KIND_DEPOSIT;
		$status = self::STATUS_PROCESSING;
		$currency = $this->utils->getConfig('default_currency');

		// $dwIp = $this->input->ip_address();
		// $geolocation = $this->utils->getGeoplugin($dwIp);
		// $geo_location = $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'];
		$timeout = $this->utils->getConfig('deposit_timeout_seconds');
		//load main wallet
		// $wallet = $this->wallet_model->getMainWalletBy($playerId);
		// $walletId = null;
		// if ($wallet) {
		// 	$walletId = $wallet->playerAccountId;
		// }
		// if (empty($systemId)) {
		// 	//empty
		// 	$systemId = 0;
		// }
		// if (empty($player_promo_id)) {
		// 	$player_promo_id = null;
		// }
		// $promo_rules_id=null;
		// $promo_cms_id=null;
		// if(isset($promo_info['promo_rules_id'])){
		// 	$promo_rules_id=$promo_info['promo_rules_id'];
		// }
		// if(isset($promo_info['promo_cms_id'])){
		// 	$promo_cms_id=$promo_info['promo_cms_id'];
		// }
    	// if($is_mobile===null){
     //    	$is_mobile=$this->utils->is_mobile();
    	// }

		$paymentBankName = null;
		$paymentAccountName = null;
		$paymentAccountNumber = null;
		$paymentBranchName = null;

		$paymentAccount = $this->payment_account->getPaymentAccount($payment_account_id);
		if ($paymentAccount) {
			$paymentType = $this->banktype->getBankTypeById($paymentAccount->payment_type_id);
			$paymentBankName = $paymentType->bankName;
			$paymentAccountName = $paymentAccount->payment_account_name;
			$paymentAccountNumber = $paymentAccount->payment_account_number;
			$paymentBranchName = $paymentAccount->payment_branch_name;
		}

		$this->db->insert($this->tableName, array(
			'secure_id' => $secureId,
			// 'system_id' => $systemId,
			'player_id' => $playerId,
			'amount' => $amount,
			'payment_kind' => $paymentKind,
			'status' => $status,
			'notes' => $notes,
			'created_at' => $date ?: $this->utils->getNowForMysql(),
			'updated_at' => $date ?: $this->utils->getNowForMysql(),
			// 'ip' => $dwIp,
			// 'geo_location' => $geo_location,
			// 'is_mobile' => $is_mobile,

			'timeout' => $timeout,
			'timeout_at' => $this->utils->getNowAdd($timeout),
			'currency' => $currency,
			// 'wallet_id' => $walletId,

			'payment_account_id' => $payment_account_id,
			'payment_type_name' => $paymentBankName,
			'payment_account_name' => $paymentAccountName,
			'payment_account_number' => $paymentAccountNumber,
			'payment_branch_name' => $paymentBranchName,

		)
		);

		return $this->db->insert_id();
	}

	public function existsUnfinishedManuallyDeposit($playerId){
    	$this->db->select('created_at, id')->from('sale_orders')->where('player_id', $playerId)
		    ->where('system_id', self::SYSTEM_ID_MANUAL)
		    ->where('status', self::STATUS_PROCESSING)
		    ->where('(timeout_at > "'.$this->utils->getNowForMysql().'" OR timeout_at IS NULL)')
		    ->limit(1);
    	return $this->runExistsResult();
    }

    public function getLastManuallyDeposit($playerId){
        $this->db->select('*')->from('sale_orders')->where('player_id', $playerId)
            ->where('system_id', self::SYSTEM_ID_MANUAL)
            ->order_by('id', 'desc')
            ->limit(1);
        return $this->runOneRowArray();
    }

    public function getSecureIdByTransactionId($transaction_id){
    	$this->db->select('secure_id');
    	$this->db->where('transaction_id', $transaction_id);
    	$this->db->from($this->tableName);
    	return $this->runOneRowOneField('secure_id');
    }

    /**
     * detail: update the detail status base on sale order id
     * @param int $orderId sale order id
     * @return array
     */
    public function updateSaleOrderDetailStatusById($orderId, $status) {
        $this->db->where('id', $orderId);
        $this->db->update($this->tableName, array('detail_status' => $status));
    }

    public function updateSaleOrderDirectPayExtraInfoById($orderId, $info) {
        $this->db->where('id', $orderId);
        $this->db->update($this->tableName, array('direct_pay_extra_info' => $info));
    }

    public function isDisabledPaymentAccountByOrderId($orderId){
    	$this->db->select('payment_account.status')->from($this->tableName)
    	  ->join('payment_account', 'payment_account.id=sale_orders.payment_account_id')
    	  ->where('sale_orders.id', $orderId);

    	//if lost,empty or another status means disabled
    	return $this->runOneRowOneField('status')!=self::STATUS_NORMAL;
    }

    /**
     * overview : checkNoteExist
     * details : check if sale_order->reason exist
     * @param orderId, msg
     * @return boolean
     */
    public function checkNoteExist($orderId, $msg){
    	$order = $this->CI->sale_order->getSaleOrderById($orderId);
        if(strpos($msg, "valid hour") !== false){
            $msg = "Wrong callback, callback time over valid hour.";
        }

        if(strpos($order->reason, $msg) !== false){
            return true;
        }
        return false;
    }

	/**
	 * Get sale order note by id
	 * @param  int $sale_order_id
	 * @return string
	 *
	 */
	public function getSaleOrderReason($sale_order_id) {
		$row = null;
		if ($sale_order_id) {
			$this->db->select('reason')->from($this->tableName)->where('id', $sale_order_id);
			return $this->runOneRowOneField('reason');
		}
		return $row;
	}

    public function setPlayerNotify($player_id, $saleOrderID, $is_notify) {
        $this->db->where('player_id', $player_id);
        $this->db->where('id', $saleOrderID);
        $this->db->set('is_notify', $is_notify);
        return $this->db->update($this->tableName);
    }

	public function getSaleOrderInfoById($id) {
		if (!empty($id)) {
			return $this->getOneRowArrayById($id);
		}
		return null;
	}
}

/////end of file///////
