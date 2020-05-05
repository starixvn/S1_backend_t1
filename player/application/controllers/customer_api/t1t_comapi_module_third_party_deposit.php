<?php
// die(getcwd());
require_once( dirname(__FILE__) . '/../modules/player_deposit_module.php');


trait t1t_comapi_module_third_party_deposit {

	use player_deposit_module;

	/**
	 * Returns useable third party payments for given player
	 * @uses	string	POST:api_key	The api_key, as md5 sum. Required.
	 * @uses	string	POST:username	Player username.  Required.
	 * @uses	string	POST:token		Effective token for player. Required.
	 *
	 * @return	JSON	JSON array of third party payment rows.  Fields in the rows are:
	 *                   	bankTypeId			The system ID for 3rd party payment
	 *                   	bank_name_json		JSON string of multi-language payment name
	 *                   	bank_name_local		Localized bank name
	 *                   	bank_code			bank_code
	 *                   	enabled_deposit		1 if enabled, otherwise 0.
	 */
	public function listThirdPartyPayments() {
		$api_key = $this->input->post('api_key');

		if (!$this->__checkKey($api_key)) { return; }

		$res = null;

		try {
			$username	= trim($this->input->post('username'));
			$token		= trim($this->input->post('token'));
			$is_mobile	= !empty($this->input->post('is_mobile'));
			$full_debug	= !empty($this->input->post('full_debug'));
			$creds = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
			$this->utils->debug_log(__FUNCTION__, 'request', $creds);

			// Check for username
			$player_id = $this->player_model->getPlayerIdByUsername($username);
			if (empty($player_id)) {
				throw new Exception('Player unknown', self::CODE_TPD_PLAYER_UNKNOWN);
			}

			// Check if player is logged in
			$player_login = $this->_isPlayerLoggedIn($player_id, $token);
			if ($player_login['code'] != 0) {
				throw new Exception($player_login['mesg'], $player_login['code']);
			}

			$payment_list_for_player = $this->get_thirdPartyPayments($player_id, $is_mobile, false, $full_debug);

			$ident = json_encode([ 'username' => $username, 'is_mobile' => $is_mobile ]);
			$ret_mesg = "List of third party payments fetched successfully";

			$ret = [
			    'success'   => true,
			    'code'      => self::CODE_SUCCESS,
			    'mesg'      => $ret_mesg ,
			    'result'    => ['list' => $payment_list_for_player , 'is_mobile' => $is_mobile , 'username' => $username ]
			];

			$this->utils->debug_log(__FUNCTION__, 'response', $ret, $creds);
		}
		catch (Exception $ex) {
			$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $creds);

			$ret = [
			    'success'   => false,
			    'code'      => $ex->getCode(),
			    'mesg'      => $ex->getMessage(),
			    'result'    => $res
			];
		}
		finally {
			$this->returnApiResponseByArray($ret);
		}
    } // End function listThirdPartyPayments()


	/**
     * Rebuilt based on Payment_account::getAvailableDefaultCollectionAccount()
     * @param  int		$player_id		== player.playerId
     * @param  boolean	$id_as_index	When true, output array is indexed by bankTypeId;
     *                              	flat array otherwise.  (Default: false)
     * @param  boolean	$full_debug		Output all fields of payments for debug.  (Default: false)
     * @return array 	Array of third payments
     */
    protected function get_thirdPartyPayments($player_id, $mobile = false, $id_as_index = false, $full_debug = false) {
    	$this->load->model(['payment_account', 'banktype']);

		// OGP-8150: get desktop/mobile payments by option
		// $payments = $this->utils->getDepositMenuList();
		$this->load->model(array());
		// OGP-8202: Use Payment_account::getAvailableDefaultCollectionAccount() instead
		// $payments = $this->payment_account->getAvailableDefaultCollectionAccount($player_id, Payment_account::FLAG_AUTO_ONLINE_PAYMENT, null, true, $mobile);
		$payments = $this->payment_account->getAvailableDefaultCollectionAccount($player_id, Payment_account::FLAG_AUTO_ONLINE_PAYMENT, null, true, $mobile ? 1 : -1);

		$payment_list = $payments[Payment_account::FLAG_AUTO_ONLINE_PAYMENT]['list'];

		$payment_list = array_filter($payment_list, function(& $p) use ($player_id) {
			$names = json_decode(preg_replace('/^_json:/', '', $p->payment_type), 'as array');
			$p->bank_name_local = $names[2];
			return $p->flag == AUTO_ONLINE_PAYMENT && $p->status = 'active'
				&& $this->payment_account->existsAvailableAccount($player_id, $p->flag, $p->payment_type_id, null, $p->only_allow_affiliate);
		});

		// If full_debug, return $payments unabridged
		if ($full_debug) {
			return $payments;
		}

		// If not full_debug, pick only selected fields
		$pay_res = [];
		$pay_sortkeys = [];

		foreach ($payment_list as $num => $p) {
			$bankTypeId = $p->payment_type_id;
			$deposit_limits = $this->get_depositLimits($player_id, $bankTypeId);

			$pay_row = [
				'bankTypeId' 			=> $bankTypeId ,
				// 'bank_flag'				=> $p->payment_flag ,
				'bank_name_json'		=> $p->payment_type ,
				'bank_name_local'		=> $p->bank_name_local ,
				'daily_deposit_max'		=> $p->max_deposit_daily ,
				'daily_deposit_amount'	=> $p->daily_deposit_amount ,
				'minDeposit'			=> $deposit_limits['minDeposit'] ,
				'maxDeposit'			=> $deposit_limits['maxDeposit'] ,
				'order'					=> $p->payment_order
			];
			// If id_as_index == true, make array indexed by bankTypeId (payment_type_id), or make it a flat array
			if ($id_as_index) {
				$pay_res[$p->payment_type_id] = $pay_row;
			}
			else {
				$pay_res[] = $pay_row;
			}
			// Collect payment_order for sorting
			$pay_sortkeys[] = $p->payment_order;
		}

		// If id_as_index == true, return the array as it is, or sort by bankTypeId before return
		if (!$id_as_index) { array_multisort($pay_sortkeys, $pay_res); }

		return $pay_res;
    } // End function get_thirdPartyPayments()

    /**
     * Returns deposit form related information for given third party payment
     * Interface method; see worker method get_thirdPartyDepositForm() for details
     * @uses	string	POST:api_key		The api_key, as md5 sum. Required.
	 * @uses	string	POST:username		Player username.  Required.
	 * @uses	string	POST:token			Effective token for player. Required.
	 * @uses	int		POST:bankTypeId		bankTypeId returned by listThirdPartyPayments.
	 *
     * @return	JSON	JSON array of fields and related info for third party payment form.
     */
	public function thirdPartyDepositForm() {
		$api_key = $this->input->post('api_key');

		if (!$this->__checkKey($api_key)) { return; }

		$res = null;

		try {
			$username	= trim($this->input->post('username'));
			$token		= trim($this->input->post('token'));
			$bankTypeId	= intval($this->input->post('bankTypeId'));
			$is_mobile		= !empty($this->input->post('is_mobile'));
			$creds = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token, 'bankTypeId' => $bankTypeId ];
			$this->utils->debug_log(__FUNCTION__, 'request', $creds);

			// Check for username
			$player_id = $this->player_model->getPlayerIdByUsername($username);
			if (empty($player_id)) {
				throw new Exception('Player unknown', self::CODE_TPD_PLAYER_UNKNOWN);
			}

			// Check if player is logged in
			$player_login = $this->_isPlayerLoggedIn($player_id, $token);
			if ($player_login['code'] != 0) {
				throw new Exception($player_login['mesg'], $player_login['code']);
			}

			// $payment_list_for_player = $this->get_thirdPartyPayments($player_id, $is_mobile, 'bankTypeId as index');
			// if (!isset($payment_list_for_player[$bankTypeId])) {
			// 	throw new Exception("Third party payment (bankTypeId=$bankTypeId) not available for player" , self::CODE_TPD_PAYMENT_UNAVAILABLE_FOR_PLAYER);
			// }

			if (!$this->comapi_lib->depcat_pay_account_by_type_bankTypeId($player_id, $bankTypeId, Comapi_lib::DEPCAT_AUTO, $is_mobile)) {
				throw new Exception("Third party payment (bankTypeId=$bankTypeId) not available for player" , self::CODE_TPD_PAYMENT_UNAVAILABLE_FOR_PLAYER);
			}

			$data_form = $this->get_thirdPartyDepositForm($player_id, $bankTypeId);

			// Formatting fields in playerInputInfo
			foreach ($data_form['playerInputInfo'] as & $pinfo) {
				// Input field label
				$pinfo['label'] = lang($pinfo['label_lang']);
				unset($pinfo['label_lang']);

				// bank_list
				if ($pinfo['name'] == 'bank_list') {
					// Conceal extra details
					unset($pinfo['external_system_id']);
					unset($pinfo['bank_list']);
					unset($pinfo['bank_list_default']);
					// Field name used for post
					$pinfo['field_name'] = 'bank';
					// If list item has no member (should be at least with bank_name, bank_id), fill it with a bank name
					foreach ($pinfo['bank_tree'] as $key => & $item) {
						if (count($item) == 0) {
							$item['bank_name'] = lang("comapi_bank_{$key}");
						}
					}
				}
				else {
					$pinfo['field_name'] = $pinfo['name'];
					unset($pinfo['name']);
				}
			}

			// Only return selected fields
			$data_ret = [
				'payment_name'	=> $data_form['paymentGatewayName'] ,
				'default_fields' => [
					'bankTypeId'	=> $data_form['bankTypeId'] ,
					'deposit_from'	=> $data_form['defaultPaymentGateway'] ,
					'minDeposit'	=> $data_form['minDeposit'] ,
					'maxDeposit'	=> $data_form['maxDeposit'] ,
				] ,
				'player_input_fields'	=> $data_form['playerInputInfo'] ,
				'is_mobile'				=> $is_mobile ,
				'username'				=> $username
			];

			// $ident = json_encode([ 'username' => $username, 'mobile' => $mobile ]);
			$ret_mesg = "Third party payment form info returned";

			// -- POINT OF EXECUTION SUCCESS ------

			$ret = [
			    'success'   => true,
			    'code'      => self::CODE_SUCCESS,
			    'mesg'      => $ret_mesg ,
			    'result'    => $data_ret
			];

			$this->utils->debug_log(__FUNCTION__, 'response', $ret, $creds);
		}
		catch (Exception $ex) {
			$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $creds);

			$ret = [
			    'success'   => false,
			    'code'      => $ex->getCode(),
			    'mesg'      => $ex->getMessage(),
			    'result'    => $res
			];
		}
		finally {
			$this->returnApiResponseByArray($ret);
		}
    } // End function thirdPartyDepositForm()

    /**
     * Worker method for thirdPartyDepositForm(), prepares form for given third party payment
     * Ported from player_deposit_module::auto_payment()
     * @param	int		$player_id		== player.playerId
     * @param	int		$bankTypeId		bankTypeId as received by thirdPartyDepositForm()
     * @return	array 	Array of various deposit form related information
     */
    protected function get_thirdPartyDepositForm($player_id, $bankTypeId) {
		if(!$this->checkAgencyCreditMode($player_id)){
			throw new Exception("Player is under agency credits" , self::CODE_TPD_PLAYER_UNDER_AGENCY_CREDITS);
		}

		$this->load->model(array('payment_account', 'banktype', 'external_system', 'bank_list',
		'transactions', 'group_level', 'promorules', 'vipsetting', 'wallet_model', 'player_model'));
		// $this->load->helper(array('form'));

		// $data['flag'] = $flag;
		$data['currentLang'] = $this->language_function->getCurrentLanguage();

		// Code to determine deposit limits is wrapped as get_depositLimits()
		// So it is reusable in get_thirdPartyDepositRequest
		$deposit_limits = $this->get_depositLimits($player_id, $bankTypeId);

		$this->log(__FUNCTION__, $deposit_limits);
		$data = array_merge($data, $deposit_limits);

		$defaultPaymentGateway = $data['defaultPaymentGateway'];

		$api = $this->utils->loadExternalSystemLibObject($defaultPaymentGateway);
		if (!$api) {
			throw new Exception("Error loading payment gateway API" , self::CODE_TPD_ERROR_LOADING_PAYMENT_API);
		}

		$playerInputInfo = $api->getPlayerInputInfo();
		$data['playerInputInfo'] = $playerInputInfo;
		$data['show_debug_warning'] = $this->config->item('debug_bofo') && $defaultPaymentGateway == BOFO_PAYMENT_API;
		$data['isPopupNewWindowOnDeposit']=$api->isPopupNewWindowOnDeposit();

		// list($bankList, $bankTree) = $this->bank_list->getBankTypeTree($defaultPaymentGateway);
		// $data['bankTree'] = $bankTree;
		// $data['bankList'] = $bankList;
		$data['defaultPaymentGateway'] = $defaultPaymentGateway;

		// if ($this->utils->getPlayerCenterTemplate() == 'webet' || !array_key_exists('paymentGatewayName', $data)) {
		// 	$this->loadTemplate(lang('header.deposit'));
		// } else {
		// 	$this->loadTemplate(lang('header.deposit') . ' - ' . $data['paymentGatewayName']);
		// }

		if ($this->utils->isEnabledFeature('use_self_pick_subwallets')) {
			$data['subwallets'] = $this->wallet_model->getSubwalletMap();
			$this->utils->debug_log('subwallets', $data['subwallets']);
		}
		if ($this->utils->isEnabledFeature('use_self_pick_group')) {
			$vipsettings = $this->group_level->getAllCanJoinIn();
			if (!empty($vipsettings)) {
				foreach ($vipsettings as $vipsetting) {
					if (!isset($data['vipsettings'][$vipsetting['vipSettingId']])) {
						$data['vipsettings'][$vipsetting['vipSettingId']]['name'] = $vipsetting['groupName'];
						$data['vipsettings'][$vipsetting['vipSettingId']]['description'] = $vipsetting['groupDescription'];
					}
					$data['vipsettings'][$vipsetting['vipSettingId']]['list'][] = $vipsetting;
				}
			}
		}
		$data['avail_promocms_list']=$this->promorules->getAvailPromoOnDeposit($player_id);

		$site = 'http://' . $this->makeFrontUrl();
		$data['site'] = $site;

		// $data['player'] = $this->player_model->getPlayerInfoDetailById($player_id);
		// player_deposit_module::getDepositLocalBank()
		$data['paymentTypes'] = $this->getDepositLocalBank($player_id);

		# Show the 'payment stopped' message if api is not available
		// REDUNDANT - CONSIDER REMOVING
		if(!$api->isAvailable()) {
			$data['exists_payment_account'] = false;
		}

		$this->utils->verbose_log('avail_promocms_list', $data['avail_promocms_list']);

		return $data;

    } // End function get_thirdPartyDepositForm()

    protected function get_depositLimits($player_id, $bankTypeId) {
    	$this->load->model([ 'payment_account', 'group_level', 'banktype' ]);

    	$data = [];

		// Determine payment account
		$paymentAccount = $this->payment_account->getAvailableAccount($player_id, Payment_account::FLAG_AUTO_ONLINE_PAYMENT, $bankTypeId);

		$depositRule = $this->group_level->getPlayerDepositRule($player_id);
		$depositRuleMinDeposit = isset($depositRule[0]['minDeposit']) ? $depositRule[0]['minDeposit'] : 0;
		$depositRuleMaxDeposit = isset($depositRule[0]['maxDeposit']) ? $depositRule[0]['maxDeposit'] : 0;

		$banktype = $this->banktype->getBankTypeById($bankTypeId);
		if ($banktype && $banktype->external_system_id) {
			$defaultPaymentGateway = $banktype->external_system_id;
			$defaultPaymentGatewayName = lang($banktype->bankName);
		}
		$data['bankTypeId'] = $bankTypeId;

		if ($paymentAccount) {
			$defaultPaymentGateway = $paymentAccount->external_system_id;
			$data['defaultPaymentGateway'] = $defaultPaymentGateway;
			$data['exists_payment_account'] = true;
			//change to payment account name
			if(isset($defaultPaymentGatewayName)) {
				$data['paymentGatewayName'] = $defaultPaymentGatewayName;
			}

			$paymentAccountMinDeposit = 0;
			$paymentAccountMaxDeposit = $paymentAccount->max_deposit_daily;

			# If defined min/max deposit per transaction, use them
			if ($paymentAccount->max_deposit_trans > 0 && $paymentAccount->max_deposit_trans < $paymentAccountMaxDeposit) {
				$paymentAccountMaxDeposit = $paymentAccount->max_deposit_trans;
			}

			$paymentAccountMinDeposit = $paymentAccount->min_deposit_trans;
		} else {
			$data['exists_payment_account'] = false;
			if(isset($defaultPaymentGatewayName)) {
				$data['paymentGatewayName'] = $defaultPaymentGatewayName;
			}
			$paymentAccountMinDeposit=0;
			$paymentAccountMaxDeposit = 0;
		}

		if(isset($bankTypeId)){
			$data['minDeposit'] = $paymentAccountMinDeposit;
			$data['maxDeposit'] = $paymentAccountMaxDeposit;
		} else {
			# Determine overall min/max deposit to be used on the form
			# Note: value = 0 means no limit
			if ($paymentAccountMinDeposit > $depositRuleMinDeposit) {
				$data['minDeposit'] = $paymentAccountMinDeposit;
			} elseif ($depositRuleMinDeposit > 0) {
				$data['minDeposit'] = $depositRuleMinDeposit;
			} else {
				$data['minDeposit'] = 0;
			}

			# depositRuleMaxDeposit is valid and in effect
			if ($depositRuleMaxDeposit > 0 && $depositRuleMaxDeposit < $paymentAccountMaxDeposit) {
				$data['maxDeposit'] = $depositRuleMaxDeposit;
			} elseif ($paymentAccountMaxDeposit > 0) { # paymentAccountMaxDeposit is valid
				$data['maxDeposit'] = $paymentAccountMaxDeposit;
			} else {
				$data['maxDeposit'] = 0;
			}
		}

		// $this->log(__FUNCTION__, [ 'paymentAccount' => [ 'max' => $paymentAccountMaxDeposit , 'min' => $paymentAccountMinDeposit ] , 'depositRule' => [ 'max' => $depositRuleMaxDeposit , 'min' => $depositRuleMinDeposit ] , 'determined' => [ 'max' => $data['maxDeposit'] , 'min' => $data['minDeposit'] ] ] );

		// Consider group_level (redundant, seek to remove)
		$dep_rule = $this->group_level->getPlayerDepositRule($player_id);
		$player_dep_min = $dep_rule[0]['minDeposit'] ?: 0.0;
		$player_dep_max = $dep_rule[0]['maxDeposit'] ?: 0.0;

		if ($player_dep_min > $data['minDeposit']) {
			$data['minDeposit'] = $player_dep_min;
		}

		if ($player_dep_max < $data['maxDeposit'] && $player_dep_max > 0) {
			$data['maxDeposit'] = $player_dep_max;
		}

		$this->log(__FUNCTION__, [ 'paymentAccount' => [ 'max' => $paymentAccountMaxDeposit , 'min' => $paymentAccountMinDeposit ] , 'depositRule' => [ 'max' => $depositRuleMaxDeposit , 'min' => $depositRuleMinDeposit ] , 'determined' => [ 'max' => $data['maxDeposit'] , 'min' => $data['minDeposit'] ] ] );

		return $data;

    } // End function get_depositLimits

    /**
     * Receives payment form provided by thirdPartyDepositForm() and returns third party payment
     * URL and related information
     * Interface method; See get_thirdPartyDepositRequest() and wrapper_goPayment() for details
     * @uses	string	POST:api_key		The api_key, as md5 sum. Required.
	 * @uses	string	POST:username		Player username.  Required.
	 * @uses	string	POST:token			Effective token for player. Required.
	 * @uses	int		POST:bankTypeId		bankTypeId for given third party payment
	 * @uses	int		POST:deposit_from	deposit_from, supplied by thirdPartyDepositForm().
	 * @uses	string	POST:bank			bank code, supplied by thirdPartyDepositForm().
	 * @uses	string	POST:bank_type		bank_type code, supplied by thirdPartyDepositForm().
	 * @uses	decimal	POST:deposit_amount	The amount of deposit
	 *
     * @return	JSON	JSON array of third party payment call related
     */
    public function thirdPartyDepositRequest() {
		$api_key = $this->input->post('api_key');

		if (!$this->__checkKey($api_key)) { return; }

		$res = null;

		try {
			$username		= trim($this->input->post('username'));
			$token			= trim($this->input->post('token'));
			$bankTypeId		= intval($this->input->post('bankTypeId'));
			$deposit_from	= intval($this->input->post('deposit_from'));
			$bank			= trim($this->input->post('bank'));
			$bank_type		= trim($this->input->post('bank_type'));
			$deposit_amount	= floatval($this->input->post('deposit_amount'));
			$is_mobile		= !empty($this->input->post('is_mobile'));
			$minDeposit		= floatval($this->input->post('minDeposit'));
			$maxDeposit		= floatval($this->input->post('maxDeposit'));
			$promo_cms_id	= intval($this->input->post('promo_cms_id'));

			$creds = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token, 'bankTypeId' => $bankTypeId , 'deposit_from' => $deposit_from , 'bank' => $bank , 'bank_type' => $bank_type , 'deposit_amount' => $deposit_amount ];

			$this->utils->debug_log(__FUNCTION__, 'request', $creds);

			// Check for username
			$player_id = $this->player_model->getPlayerIdByUsername($username);
			if (empty($player_id)) {
				throw new Exception('Player unknown', self::CODE_TPD_PLAYER_UNKNOWN);
			}

			// Check if player is logged in
			$player_login = $this->_isPlayerLoggedIn($player_id, $token);
			if ($player_login['code'] != 0) {
				throw new Exception($player_login['mesg'], $player_login['code']);
			}

			// Verify bankTypeId against player_id
			if (!$this->comapi_lib->depcat_pay_account_by_type_bankTypeId($player_id, $bankTypeId, Comapi_lib::DEPCAT_AUTO, $is_mobile)) {
				throw new Exception("Third party payment (bankTypeId=$bankTypeId) not available for player" , self::CODE_TPD_PAYMENT_UNAVAILABLE_FOR_PLAYER);
			}
			// $payment_list_for_player = $this->get_thirdPartyPayments($player_id, $is_mobile, 'bankTypeId as index');
			// if (!isset($payment_list_for_player[$bankTypeId])) {
			// 	throw new Exception("Third party payment (bankTypeId=$bankTypeId) not available for player" , self::CODE_TPD_PAYMENT_UNAVAILABLE_FOR_PLAYER);
			// }
			// $this->log(__FUNCTION__, [ 'bankTypeId' => $bankTypeId, 'payment_list_for_player' => $payment_list_for_player ]);

			if (!$this->comapi_lib->depcat_pay_account_by_type_bankTypeId($player_id, $bankTypeId, Comapi_lib::DEPCAT_AUTO, $is_mobile)) {
				throw new Exception("Third party payment (bankTypeId=$bankTypeId) not available for player" , self::CODE_TPD_PAYMENT_UNAVAILABLE_FOR_PLAYER);
			}

			// Verify deposit_from against bankTypeId
			$data_form = $this->get_thirdPartyDepositForm($player_id, $bankTypeId);
			if ($deposit_from != $data_form['defaultPaymentGateway']) {
				throw new Exception("Wrong value for deposit_from ({$deposit_from}), does not match bankTypeId ({$bankTypeId})", self::CODE_TPD_WRONG_VALUE_FOR_DEPOSIT_FROM);
			}

			if ($minDeposit != $data_form['minDeposit'] || $maxDeposit != $data_form['maxDeposit']) {
				throw new Exception("Wrong value for minDeposit or maxDeposit", self::CODE_TPD_WRONG_VALUE_FOR_MIN_MAX_DEPOSIT);
			}

			$deposit_time = $this->utils->getNowForMysql();

			$data_ret = $this->get_thirdPartyDepositRequest($player_id, $bankTypeId, $deposit_from, $deposit_amount, $bank, $bank_type, $deposit_time, null, $promo_cms_id);

			if ($data_ret['success'] != true) {
				throw new Exception("{$data_ret['mesg']}[gtpdr]", $data_ret['code']);
			}

			// $ident = json_encode([ 'username' => $username, 'is_mobile' => $is_mobile ]);
			$ret_mesg = "Third party payment submit info retrieved";

			$ret_result = $data_ret['result'];
			$ret_result['is_mobile'] = $is_mobile;
			$ret_result['username'] = $username;

			// -- POINT OF EXECUTION SUCCESS ------

			$ret = [
			    'success'   => true,
			    'code'      => self::CODE_SUCCESS,
			    'mesg'      => $ret_mesg ,
			    'result'    => $ret_result
			];

			$this->utils->debug_log(__FUNCTION__, 'response', $ret, $creds);
		}
		catch (Exception $ex) {
			$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $creds);

			$ret = [
			    'success'   => false,
			    'code'      => $ex->getCode(),
			    'mesg'      => $ex->getMessage(),
			    'result'    => $res
			];
		}
		finally {
			$this->returnApiResponseByArray($ret);
		}

    } // End function thirdPartyDepositRequest()

    /**
     * Ported from player_deposit_module::autoDeposit3rdParty()
     * @return [type] [description]
     */
    protected function get_thirdPartyDepositRequest($player_id, $bankTypeId, $deposit_from, $deposit_amount, $bank, $bank_type = null, $deposit_time = null, $player_deposit_reference_no = null, $promo_cms_id = null, $sub_wallet_id = null) {
    	$this->load->library(['authentication']);
    	$this->load->model(array('group_level', 'transactions', 'payment_account'));

    	try {
	    	$player = $this->player_functions->getPlayerById($player_id);

	    	$data['depositRule'] = $this->group_level->getPlayerDepositRule($player_id);

	  //   	if(isset($bankTypeId)){
			// 	$paymentAccount = $this->payment_account->getAvailableAccount($player_id, Payment_account::FLAG_AUTO_ONLINE_PAYMENT, $bankTypeId);
			// 	$minDeposit = $paymentAccount->min_deposit_trans;
			// 	$maxDeposit = $paymentAccount->max_deposit_trans;
			// } else {
			// 	$minDeposit = $data['depositRule'][0]['minDeposit'];
			// 	$maxDeposit = $data['depositRule'][0]['maxDeposit'];
			// }

			$deposit_limits = $this->get_depositLimits($player_id, $bankTypeId);

			$minDeposit = $deposit_limits['minDeposit'];
			$maxDeposit = $deposit_limits['maxDeposit'];

			$this->log(__FUNCTION__, [ 'depositRule' => [ 'min' => $data['depositRule'][0]['minDeposit'] , 'max' => $data['depositRule'][0]['maxDeposit'] ] , 'from_get_depositLimits' => [ 'min' => $minDeposit , 'max' => $maxDeposit ] ]);

			$maxDepositDaily=0;

			// $paymentSystemId = $this->input->post('deposit_from');
			$paymentSystemId = $deposit_from;
			$api = $this->utils->loadExternalSystemLibObject($paymentSystemId);
			// $deposit_amount = null;
			// if ($api) {
			// 	$deposit_amount = $api->getAmount($this->getInputGetAndPost());
			// }
			if (!$api) {
				throw new Exception("Cannot load payment API ($paymentSystemId)", self::CODE_TPD_ERROR_LOADING_PAYMENT_API);
			}

			if($bankTypeId){
				$ignore_amount_limit_for_loadcard = $paymentSystemId &&
				$this->utils->getConfig('ignore_amount_limit_for_loadcard');
			} else{
				$ignore_amount_limit_for_loadcard = $paymentSystemId == LOADCARD_PAYMENT_API &&
				$this->utils->getConfig('ignore_amount_limit_for_loadcard');
			}

			if (!$ignore_amount_limit_for_loadcard && $minDeposit > $deposit_amount) {
				throw new Exception("Deposit amount lower than minimum for single deposit ($deposit_amount/$minDeposit)", self::CODE_TPD_AMOUNT_HIT_TRANS_MIN);
			}

			$this->load->model(array('player_model', 'bank_list'));

			//check if deposit reached max limit
			if ($maxDeposit > 0) {
				if ($deposit_amount > $maxDeposit) {
					throw new Exception("Deposit amount hit the maximum for single deposit ({$deposit_amount}/{$maxDeposit})", self::CODE_TPD_AMOUNT_HIT_TRANS_MAX);
				}
			}

			if ($maxDepositDaily > 0) {
				//get players deposit total deposit
				$playerTotalDailyDeposit = $this->transactions->sumDepositAmountToday($player_id);


				if (($playerTotalDailyDeposit + $deposit_amount) >= $maxDepositDaily) {
					throw new Exception("Today's deposit amount hit daily max ({$playerTotalDailyDeposit}/{$maxDepositDaily})", self::CODE_TPD_AMOUNT_HIT_DAILY_MAX);
				}
			}

			$this->log(__FUNCTION__, [ 'deposit_amount' => $deposit_amount , 'minDeposit' => $minDeposit , 'ignore_amount_limit_for_loadcard' => $ignore_amount_limit_for_loadcard , 'maxDeposit' => $maxDeposit , 'maxDepositDaily' => $maxDepositDaily ]);

			$deposit_from = $paymentSystemId;

			// $sub_wallet_id = $this->input->post('sub_wallet_id');
			// $promo_cms_id=$this->input->post('promo_cms_id');
			// $sub_wallet_id = 0;
			// $promo_cms_id = 0;
			$error=null;
			$player_promo_id =null;
			// $player_promo_id = $this->process_player_promo($player_id, $promo_cms_id, $deposit_amount, $sub_wallet_id, $error);
			list($promo_cms_id, $promo_rules_id) = $this->comapi_lib->process_promo_rules($player_id, $promo_cms_id, $deposit_amount, $error, $sub_wallet_id);
			$promo_info = [
				'promo_rules_id'	=> $promo_rules_id ,
				'promo_cms_id'		=> $promo_cms_id
			];

			if(!empty($error)){
				throw new Exception("Error while processing deposit ($error)", self::CODE_TPD_OTHER_DEPOSIT_ERROR);
			}


			//group level id
			// $group_level_id = $this->input->post('group_level_id');
			$vipInfo = $this->group_level->getPlayerGroupLevelInfo($player_id);
			$group_level_id = isset($vipInfo['levelId']) ? $vipInfo['levelId'] : null;

			// $player_deposit_reference_no = $this->input->post('player_deposit_reference_no');
			// $deposit_time = $this->input->post('deposit_time');

			// $bankId = $this->input->post('bank_type');
			// $bankShortCode = $this->input->post('bank');
			$bankId = $bank_type;
			$bankShortCode = $bank;
			$this->log(__FUNCTION__, 'bank info', [ 'bankId' => $bankId, 'bankShortCode' => $bankShortCode ]);
			if (empty($bankId) && !empty($bankShortCode)) {
				$bankId = $this->bank_list->getIdByShortcode($bankShortCode);
			}

			$extra_info_order=$this->getInputGetAndPost();
			//remove iovation
			unset($extra_info_order['ioBB']);
			unset($extra_info_order['fpBB']);

			// $orderId = $api->createSaleOrder($player_id, $deposit_amount, $player_promo_id,
			// 	$extra_info_order, $sub_wallet_id, $group_level_id, $this->utils->is_mobile(), $player_deposit_reference_no, $deposit_time);

			$orderId = $api->createSaleOrder($player_id, $deposit_amount, $player_promo_id,
				$extra_info_order, $sub_wallet_id, $group_level_id, true, $player_deposit_reference_no, $deposit_time, $promo_info);

			$this->saveHttpRequest($player_id, Http_request::TYPE_DEPOSIT);

			// if( $this->input->is_ajax_request() ){
			// 	$this->returnJsonResult(array('status' => 'success', 'msg' => ''));
			// 	return;
			// }

			// if (!empty($bankId)) {
			// $bankId = $this->input->post('bank');

			$pay_ret = $this->wrapper_goPayment($deposit_from, $deposit_amount, $player_id,
				$player_promo_id, $bankId, $orderId);

			if ($pay_ret['success'] != true) {
				throw new Exception("{$pay_ret['mesg']}[wgp]", $pay_ret['code']);
			}

			$ret = [ 'success' => true, 'code' => 0, 'mesg' => '', 'result' => $pay_ret['result'] ];

		}
		catch (Exception $ex) {
			$this->log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);
			$ret = [ 'success' => false, 'code' => $ex->getCode(), 'mesg' => $ex->getMessage() ];
		}
		finally {
			return $ret;
		}
    }

    /**
     * Wrapper for $api->generatePaymentUrlForm()
     * Combined BaseController::goPayment(), BaseController::getPaymentUrl(),
     * Utils::getPaymentUrl(), Redirect::payment()
     * @param  [type] $deposit_from    [description]
     * @param  [type] $deposit_amount  [description]
     * @param  [type] $player_id       [description]
     * @param  [type] $player_promo_id [description]
     * @param  [type] $bankId          [description]
     * @param  [type] $player          [description]
     * @return [type]                  [description]
     */
    protected function wrapper_goPayment($systemId, $amount, $playerId, $player_promo_id, $bankId, $orderId) {

		// Synopsis:
    	// BaseController::goPayment($deposit_from, $deposit_amount, $player_id,
    	// 		$player_promo_id, $bankId, $orderId)
    	// == BaseController::getPaymentUrl($deposit_from, $deposit_amount, $player_id,
    	//  	$player_promo_id, $bankId, $orderId)
    	// == Utils::getPaymentUrl('', $deposit_from, $deposit_amount, $player_id,
    	// 		$player_promo_id, true, $bankId, $orderId)
    	// ===> Builds URL: /redirect/payment/{$deposit_from}/{$deposit_amount}/{$player_id}/
    	// 		{$player_promo_id}/true/$bankId/$orderId
    	// ===> equals Invoking Redirect::payment($deposit_from, $deposit_amount, $player_id,
    	// 		$player_promo_id, true, $bankId, $orderId)

	    try {
	    	$this->load->model([ 'sale_orders_status_history', 'sale_order' ]);

	    	$enabledSecondUrl = true;

	    	$api = $this->utils->loadExternalSystemLibObject($systemId);

	    	if (!$api) {
	    		throw new Exception("Error loading payment API ($systemId)", self::CODE_TPD_ERROR_LOADING_PAYMENT_API);
	    	}

			if ($player_promo_id == '0') {
				$player_promo_id = null;
			}
			if ($bankId == '0') {
				$bankId = null;
			}

			$this->log(__FUNCTION__, 'invocation', [ 'playerId' => $playerId, 'player_promo_id' => $player_promo_id, 'enabledSecondUrl' => $enabledSecondUrl, 'bankId' => $bankId, 'orderId' => $orderId ]);

			if (!$this->sale_order->existsSaleOrder($orderId)) {
				throw new Exception("Order not found ({$orderId})", self::CODE_TPD_ORDER_NOT_FOUND);
			}

			$saleOrder = $this->sale_order->getSaleOrderById($orderId);
			if($saleOrder->player_id != $playerId) {
				$this->log(__FUNCTION__, 'Wrong player_id', [ 'player_id_expected' => $saleOrder->player_id, 'player_id_provided' => $playerId, 'secure_id' => $saleOrder->secure_id ]);
				throw new Exception("Provided player_id does not match order (provided: {$playerId}; by order: {$saleOrder->player_id}; secure_id: {$saleOrder->secure_id})", self::CODE_TPD_PLAYER_ID_DOES_NOT_MATCH_ORDER);
			}

			// Skip sessionPlayerId checks (Redirect::payment() 167-173)
			// for it is covered in thirdPartyDepositRequest() checks

			if($saleOrder->system_id != $systemId) {
				$this->log(__FUNCTION__, 'Wrong system_id', [ 'system_id_expected' => $saleOrder->system_id, 'system_id_provided' => $systemId, 'secure_id' => $saleOrder->secure_id ]);
				throw new Exception("Provided system_id does not match order (provided: {$systemId}; by order: {$saleOrder->system_id}; secure_id: {$saleOrder->secure_id})", self::CODE_TPD_SYSTEM_ID_DOES_NOT_MATCH_ORDER);
			}

			if ( abs($saleOrder->amount - number_format(floatval($amount), 2, '.', '') ) > 1 ) {
				$this->log(__FUNCTION__, 'Wrong amount', [ 'amount_expected' => $saleOrder->amount, 'amount_provided' => $amount, 'secure_id' => $saleOrder->secure_id ]);
				throw new Exception("Provided amount does not match order (provided: {$amount}; by order: {$saleOrder->amount}; secure_id: {$saleOrder->secure_id})", self::CODE_TPD_AMOUNT_DOES_NOT_MATCH_ORDER);
			}

			$info = $api->getInfoByEnv();

			// Removed most of enableSecondBool if structure (Redirect::payment() 189-207)
			// For most - if not all - checking is no longer performed
			// if (empty($orderId)) {
   			//		throw new Exception("OrderId missing", self::CODE_TPD_ORDER_ID_MISSING);
			// }
			// $enableSecondBool = ($enabledSecondUrl == 'true');
			$enableSecondBool = $enabledSecondUrl;


			$this->sale_orders_status_history->createSaleOrderStatusHistory($orderId,Sale_order::DEPOSIT_STATUS_CREATE_ORDER);

			$pay_ret = $api->generatePaymentUrlForm($orderId, $playerId, $amount,
				new DateTime(), $player_promo_id, $enableSecondBool, $bankId);

			$use_second_url = $api->shouldRedirect(true);

			if(!$use_second_url) {
				$this->sale_orders_status_history->createSaleOrderStatusHistory($orderId,Sale_order::DEPOSIT_STATUS_SUBMIT_ORDER);
			}

			if (!$pay_ret) {
				$this->log(__FUNCTION__, "generatePaymentUrlForm return empty");
				throw new Exception("generatePaymentUrlForm return empty", self::CODE_TPD_GENPAYURLFORM_RETURN_EMPTY);
			}

			$pay_ret_type = isset($pay_ret['type']) ? $pay_ret['type'] : null;

			// Failure - bailing out
			// if ($pay_ret['success'] != true && ($pay_ret_type == Abstract_payment_api::REDIRECT_TYPE_ERROR || $pay_ret_type == Abstract_payment_api::REDIRECT_TYPE_STATIC)) {
			if ($pay_ret['success'] != true && in_array($pay_ret_type, [
					Abstract_payment_api::REDIRECT_TYPE_ERROR ,
					Abstract_payment_api::REDIRECT_TYPE_ERROR_MODAL ,
					Abstract_payment_api::REDIRECT_TYPE_STATIC ,
				])) {

				// Log status change on error
				$this->wrapper_createSaleOrderStatusHistory($orderId, $pay_ret_type);

				$this->log(__FUNCTION__, "Payment failed: {$pay_ret['message']}");
				throw new Exception("Payment failed: {$pay_ret['message']}", self::CODE_TPD_PAYMENT_FAILED);
			}

			// If success, add additional details
			$pay_ret_type_text = [
				Abstract_payment_api::REDIRECT_TYPE_ERROR_MODAL		=> 'modal' ,
				Abstract_payment_api::REDIRECT_TYPE_ERROR			=> 'error' ,
				Abstract_payment_api::REDIRECT_TYPE_URL				=> 'URL' ,
				Abstract_payment_api::REDIRECT_TYPE_HTML			=> 'HTML' ,
				Abstract_payment_api::REDIRECT_TYPE_FORM			=> 'form' ,
				Abstract_payment_api::REDIRECT_TYPE_DIRECT_PAY		=> 'direct_pay' ,
				Abstract_payment_api::REDIRECT_TYPE_QRCODE			=> 'qrcode' ,
				Abstract_payment_api::REDIRECT_TYPE_QRCODE_MODAL	=> 'qrcode' ,
				Abstract_payment_api::REDIRECT_TYPE_STATIC			=> 'static' ,
				// Abstract_payment_api::REDIRECT_TYPE_QRCODE_SIMPLE => 'qrcode_simple'
			];

			// Unrecognized type - exception
			if (!isset($pay_ret_type_text[$pay_ret_type])) {
				throw new Exception("Illegal return type ($pay_ret_type)", self::CODE_TPD_ILLEGAL_RETURN_TYPE);
			}
			$pay_ret['type_text'] =  $pay_ret_type_text[$pay_ret_type];
			$pay_ret['subtype_text'] = null;

			// Log status change on success
			$this->wrapper_createSaleOrderStatusHistory($orderId, $pay_ret_type);

			switch ($pay_ret_type) {
				case Abstract_payment_api::REDIRECT_TYPE_URL :
					break;
				case Abstract_payment_api::REDIRECT_TYPE_HTML :
					break;
				case Abstract_payment_api::REDIRECT_TYPE_FORM :
					list($html, $formId) = $api->createHtmlForm($pay_ret);

					$response = 'content.redirected.player_center.api';
					$response_result_id = $api->submitPreprocess($html, $response, $pay_ret['url'], $response , array('errCode' => null, 'error' => null, 'statusCode' => null), $saleOrder->secure_id);

					$pay_ret['html'] = htmlspecialchars($html);
					$pay_ret['formId'] = $formId;
					break;
				case Abstract_payment_api::REDIRECT_TYPE_DIRECT_PAY :
					$pay_ret['url'] = "/redirect/direct_pay/{$pay_ret['systemId']}/{pay_ret['orderId']}";
					break;
				case Abstract_payment_api::REDIRECT_TYPE_QRCODE :
				case Abstract_payment_api::REDIRECT_TYPE_QRCODE_MODAL :
					if (isset($pay_ret['url'])) {
						$pay_ret['subtype'] = 'url';
					}
					else if (isset($pay_ret['base64_url'])) {
						$pay_ret['subtype'] = 'base64_url';
					}
					else if (isset($pay_ret['base64'])) {
						$pay_ret['subtype'] = 'base64';
					}
					else if (isset($pay_ret['image_url'])) {
						$pay_ret['subtype'] = 'image_url';
					}
					break;
				// (OBSOLETE) Other cases (URL, QRCODE_SIMPLE, QRCODE, STATIC): no additional information
				// OGP-17170: Build support for REDIRECT_TYPE_STATIC for SS_ALIPAY_BANKCARD
				case Abstract_payment_api::REDIRECT_TYPE_STATIC :
					if (isset($pay_ret['data'])) {
						$data_translated = [];
						foreach ($pay_ret['data'] as $key => $val) {
							$data_translated[lang($key)] = $val;
						}
						$pay_ret['data'] = $data_translated;
					}
					break;

				default :
			}

			$pay_ret['secure_id']		= $saleOrder->secure_id;
			$pay_ret['account_name']	= lang($saleOrder->payment_type_name);
			$pay_ret['amount']			= number_format(floatval($amount), 2);
			$pay_ret['time_request']	= $saleOrder->created_at;

			$ret = [ 'success' => true, 'code' => 0, 'mesg' => '', 'result' => $pay_ret ];
		}
		catch (Exception $ex) {
			$this->log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);
			$ret = [ 'success' => false, 'code' => $ex->getCode(), 'mesg' => $ex->getMessage() ];
		}
		finally {
			return $ret;
		}
    }

    protected function wrapper_createSaleOrderStatusHistory($orderId, $pay_ret_type) {
    	$this->load->model([ 'sale_orders_status_history' ]);
		$status_map = [
			// error status classes
			Abstract_payment_api::REDIRECT_TYPE_ERROR => Sale_order::DEPOSIT_STATUS_RERIERCT_ERROR ,
			Abstract_payment_api::REDIRECT_TYPE_ERROR_MODAL => Sale_order::DEPOSIT_STATUS_RERIERCT_ERROR_MODAL ,
			// normal status classes
			Abstract_payment_api::REDIRECT_TYPE_URL => Sale_order::DEPOSIT_STATUS_RERIERCT_URL ,
			Abstract_payment_api::REDIRECT_TYPE_HTML => Sale_order::DEPOSIT_STATUS_RERIERCT_FORM ,
			Abstract_payment_api::REDIRECT_TYPE_FORM => Sale_order::DEPOSIT_STATUS_RERIERCT_FORM ,
			Abstract_payment_api::REDIRECT_TYPE_DIRECT_PAY => Sale_order::DEPOSIT_STATUS_RERIERCT_DIRECT_PAY ,
			Abstract_payment_api::REDIRECT_TYPE_QRCODE => Sale_order::DEPOSIT_STATUS_RERIERCT_QRCODE ,
			Abstract_payment_api::REDIRECT_TYPE_QRCODE_MODAL => Sale_order::DEPOSIT_STATUS_RERIERCT_QRCODE_MODAL ,
			Abstract_payment_api::REDIRECT_TYPE_STATIC => Sale_order::DEPOSIT_STATUS_RERIERCT_STATIC ,
		];
		if (isset($status_map[$pay_ret_type])) {
			$this->sale_orders_status_history->createSaleOrderStatusHistory($orderId, $status_map[$pay_ret_type]);
			$this->utils->debug_log(__METHOD__, 'Logging', [ 'orderId' => $orderId, 'pay_ret_type' => $pay_ret_type, 'status' => $status_map[$pay_ret_type] ]);
		}
		else {
			$this->utils->debug_log(__METHOD__, 'Unknown pay_ret_type', [ 'orderId' => $orderId, 'pay_ret_type' => $pay_ret_type ]);
		}
    }

 	/**
     * Returns available deposit methods,
     * OGP-10251
     *
     * @see		views/stable_center2/cashier/deposit_sidebar.php
     * @see		views/stable_center2/mobile/cashier/deposit_sidebar.php
     *
     * @return	JSON	Standard JSON return structure
     */
    public function listDepositMethods() {
    	$api_key = $this->input->post('api_key');

		if (!$this->__checkKey($api_key)) { return; }

		$res = null;

		try {
			$this->load->model([ 'payment_account' ]);

			$token		= $this->input->post('token'	, true);
			$username	= $this->input->post('username'	, true);
			$is_mobile	= !empty($this->input->post('is_mobile'));

			$creds = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token, 'is_mobile' => $is_mobile ];
			$this->utils->debug_log(__METHOD__, 'request', $creds);

			$player_id	= $this->player_model->getPlayerIdByUsername($username);
			if (empty($player_id)) {
				throw new Exception(lang('Player username invalid'), self::CODE_INVALID_USER);
			}

			// Check player token
			$logchk = $this->_isPlayerLoggedIn($player_id, $token);
			if ($logchk['code'] != 0) {
				throw new Exception($logchk['mesg'], $logchk['code']);
			}

			// Read all payment accounts
			$pay_accounts_all = $this->payment_account->getAvailableDefaultCollectionAccount($player_id, null, null, true, $is_mobile);

			// Merge accounts type=1 (MANUAL_ONLINE_PAYMENT) and type=3 (LOCAL_BANK_OFFLINE)
			// as manual payment accounts
			$pay_accounts_manu = [];
			if (isset($pay_accounts_all[MANUAL_ONLINE_PAYMENT]) && $pay_accounts_all[MANUAL_ONLINE_PAYMENT]['enabled']) {
				$pay_accounts_manu = array_merge($pay_accounts_manu, $pay_accounts_all[MANUAL_ONLINE_PAYMENT]['list']);
			}
			if (isset($pay_accounts_all[LOCAL_BANK_OFFLINE]) && $pay_accounts_all[LOCAL_BANK_OFFLINE]['enabled']) {
				$pay_accounts_manu = array_merge($pay_accounts_manu, $pay_accounts_all[LOCAL_BANK_OFFLINE]['list']);
			}

			$pay_accounts_manu_prime = [];
			foreach ($pay_accounts_manu as $acc) {
				$deposit_limits = $this->get_depositLimits($player_id, $acc->bankTypeId);
				$acc_prime = [
					'bankTypeId' 			=> $acc->bankTypeId ,
					// 'bank_flag'				=> $acc->payment_flag ,
					'bank_name_json'		=> $acc->payment_type ,
					'bank_name_local'		=> lang($acc->payment_type) ,
					'daily_deposit_max'		=> $acc->max_deposit_daily ,
					'daily_deposit_amount'	=> $acc->daily_deposit_amount ,
					'minDeposit'			=> $deposit_limits['minDeposit'] ,
					'maxDeposit'			=> $deposit_limits['maxDeposit'] ,
					'order'					=> $acc->payment_order
				];
				$pay_accounts_manu_prime[] = $acc_prime;
			}

			// Use type=2 (AUTO_ONLINE_PAYMENT) as auto payment accounts
			$pay_accounts_auto = [];
			if (isset($pay_accounts_all[AUTO_ONLINE_PAYMENT]) && $pay_accounts_all[AUTO_ONLINE_PAYMENT]['enabled']) {
				// $pay_accounts_auto = $pay_accounts_all[AUTO_ONLINE_PAYMENT]['list'];
				$pay_accounts_auto = $this->get_thirdPartyPayments($player_id, $is_mobile);
			}

			$this->utils->debug_log(__METHOD__, 'pay_accounts_auto_raw', $pay_accounts_all[AUTO_ONLINE_PAYMENT]['list']);

			$avail_deposit_methods = [
				'third_party' => [
					'enabled'	=> !empty($pay_accounts_auto) ,
					'list'		=> $pay_accounts_auto ,
					'method'	=> !empty($pay_accounts_auto) ? 'listThirdPartyPayments' : null
				] ,
				'manual' => [
					'enabled'	=> !empty($pay_accounts_manu_prime) ,
					'list'		=> $pay_accounts_manu_prime ,
					'method'	=> !empty($pay_accounts_manu_prime) ? 'manualDeposit' : null
				] ,
			];

			$ret = [
			    'success'   => true,
			    'code'      => self::CODE_SUCCESS,
			    'mesg'      => "Deposit methods retrieved successfully",
			    'result'    => [
			    	'deposit_methods' => $avail_deposit_methods ,
				]
			];

			$this->utils->debug_log(__METHOD__, 'response', $res, $creds);
		}
		catch (Exception $ex) {
			$this->utils->debug_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $creds);

			$ret = [
			    'success'   => false,
			    'code'      => $ex->getCode(),
			    'mesg'      => $ex->getMessage(),
			    'result'    => $res
			];
		}
		finally {
			$this->returnApiResponseByArray($ret);
		}
    } // End function listDepositMethods()


} // End of trait t1t_comapi_module_third_party_deposit