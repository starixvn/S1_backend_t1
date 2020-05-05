<?php

/**
 * Api_common core module: accounts
 * Separated 4/01/2020
 *
 * This trait has following member methods:
 * 		// add account
 * 		public function addPlayerDepositAccount()
 * 		public function addPlayerWithdrawAccount()
 * 		protected function _addPlayerAccount_general($mode)
 *   	// list account
 * 		public function queryDepositBank()
 * 		public function listPlayerDepositAccounts()
 * 		public function listPlayerWithdrawAccounts()
 * 		// remove account (not implemented)
 * 		public function removePlayerWithdrawAccount()
 * 		public function removePlayerDepositAccount()
 * 		// set default
 * 		public function setPlayerWithdrawAccountDefault()
 *
 * @see		api_common.php
 */
trait comapi_core_accounts {

    /**
     * Adds deposit account for player
     * See _addPlayerAccount_general() below for detailed POST arguments
     * @see 	comapi_core_accounts::_addPlayerAccount_general()
     * @return	JSON	General JSON return object [ success, code, message, result ]
     */
    public function addPlayerDepositAccount() {
    	$this->_addPlayerAccount_general('deposit');
    }

    /**
     * Adds withdrawal account for player
     * See _addPlayerAccount_general() below for detailed POST arguments
     * @see 	comapi_core_accounts::_addPlayerAccount_general()
     * @return	JSON	General JSON return object [ success, code, message, result ]
     */
    public function addPlayerWithdrawAccount() {
    	$this->_addPlayerAccount_general('withdraw');
    }

    /**
     * General method for adding player bank account
     * Worker method for addPlayerWithdrawAccount/addPlayerDepositAccount, do not call directly
     * OGP-13942: Incorporate field checks set in Player Center Financial Account Settings of SBE
     * @see		comapi_core_accounts::addPlayerWithdrawAccount()
     * @see		comapi_core_accounts::addPlayerDepositAccount()
     * @see     SBE/payment_management/viewPlayerCenterFinancialAccountSettings
     *
     * @param	string	$mode			account mode, 'deposit' or 'withdraw'
     *
     * @uses	string	POST: api_key		api key given by system
     * @uses	string	POST: username		Player username
     * @uses	string	POST: token			Player's login token
     * @uses	int		POST: bankTypeId	bankTypeId listed in API queryDepositWithdrawalAvailableBank return
     * @uses 	numeric	POST: bankAccNum	Bank account number
     * @uses 	string	POST: bankAccName	Bank account name
     * @uses 	string	POST: bankAddress	Branch address
     * @uses 	string	POST: province		Province the branch is in
     * @uses 	string	POST: city			City the branch is in
     * @uses 	string	POST: branch		Branch name
     * @uses 	string	POST: phone			Branch phone
     *
     * @return	JSON	General JSON return object [ success, code, message, result ]
     */
    protected function _addPlayerAccount_general($mode) {
    	$api_key = $this->input->post('api_key');

		if (!$this->__checkKey($api_key)) { return; }

		$res = null;

		try {
			$this->load->model([ 'playerbankdetails' , 'banktype', 'operatorglobalsettings' ]);

			$token					= $this->input->post('token'		, true);
    		$username				= $this->input->post('username'		, true);

			$post_keys = [
				'bankTypeId'			=> 'bankTypeId'		,
				'bankAccountNumber'		=> 'bankAccNum'		,
				'bankAccountFullName' 	=> 'bankAccName'	,
				'bankAddress'			=> 'bankAddress'	,
				'province'				=> 'province' 		,
				'city'					=> 'city' 			,
				'branch'				=> 'branch' 		,
				'phone'					=> 'phone'
			];

			$args = [];
			foreach ($post_keys as $arg_key => $key) {
				$args[$arg_key] = trim($this->input->post($key, true));
			}

			$request = [ 'api_key' => $api_key, 'token' => $token, 'username' => $username, 'mode' => $mode, 'args' => $args ];
			$this->comapi_log(__METHOD__, 'request', $request);

            // 0a: Username check
			$player_id	= $this->player_model->getPlayerIdByUsername($username);
    		if (empty($player_id)) {
    			throw new Exception(lang('Player username invalid'), self::CODE_COMMON_INVALID_USERNAME);
    		}

            // 0b: Token check
    		if (!$this->__isLoggedIn($player_id, $token)) {
    			throw new Exception(lang('Token invalid or player not logged in'), self::CODE_COMMON_INVALID_TOKEN);
    		}

            // 1: Number of player's accounts
            $rules_fao = $this->comapi_lib->fin_acc_rules_others();
            // $this->comapi_log(__METHOD__, 'fa rules-others', $rules_fao);

            // 1a: Withdraw mode, check for number of withdraw accounts
            if ($mode == 'withdraw' && $rules_fao['account_max_count_withdraw'] > 0) {
                $this->comapi_log(__METHOD__, 'account_max_count_withdraw', $rules_fao['account_max_count_withdraw']);
                // Suppress exception CODE_LPWA_NO_WITHDRAW_ACCOUNT from Comapi_lib - 8/30/2019
                $wd_accounts = $this->comapi_lib->player_withdraw_accounts($player_id, false, 'probe_only');
                // OGP-15852: Add empty() tests for php 7.2
                if (!empty($wd_accounts) && count($wd_accounts) >= $rules_fao['account_max_count_withdraw']) {
                    $res = [ 'max_num_of_withdraw_accounts' => $rules_fao['account_max_count_withdraw'] ];
                    throw new Exception(sprintf(lang("Player already had max number (%d) of withdraw accounts, cannot add any more"), $rules_fao['account_max_count_withdraw']), self::CODE_PAO_TOO_MANY_ACCOUNTS);
                }
            }
            // 1b: Deposit mode, check for number of deposit accounts
            else if ($mode == 'deposit' && $rules_fao['account_max_count_deposit'] > 0) {
                $this->comapi_log(__METHOD__, 'account_max_count_deposit', $rules_fao['account_max_count_deposit']);
                $dp_accounts = $this->playerbankdetails->getDepositBankDetails($player_id);
                // OGP-15852: Add empty() tests for php 7.2
                if (!empty($dp_accounts) && count($dp_accounts) >= $rules_fao['account_max_count_deposit']) {
                    $res = [ 'max_num_of_deposit_accounts' => $rules_fao['account_max_count_deposit'] ];
                     throw new Exception(sprintf(lang("Player already had max number (%d) of deposit accounts, cannot add any more"), $rules_fao['account_max_count_deposit']), self::CODE_PAO_TOO_MANY_ACCOUNTS);
                }
            }

    		// 2: bankTypeId
    		$bank = $this->banktype->getBankTypeById($args['bankTypeId']);
    		if (empty($bank)) {
    			throw new Exception(lang('bankTypeId invalid, not listed in system supported bank list'), self::CODE_PAO_BANKTYPEID_INVALID);
    		}

    		// 3: Check for bankAccountNumber, use rules_faa['regex_accnum'] to match
            $rules_faa = $this->comapi_lib->fin_acc_rules_acc();
            $this->comapi_log(__METHOD__, 'fa rules-accounts', $rules_faa);
            if (!preg_match($rules_faa['regex_accnum'], $args['bankAccountNumber'])) {
    			throw new Exception(sprintf(lang('bankAccNum empty or does not comply with the format.  The format is: %s'), $rules_faa['format_accnum']), self::CODE_PAO_BANKACCNUM_INVALID);
    		}

    		// 4: Check for bankAccountFullName by $rules_faa options
    		$player_details = $this->player_model->getAllPlayerDetailsById($player_id);
            // 4a: if bankAccountFullName empty
            if (empty($args['bankAccountFullName'])) {
                throw new Exception(lang('bankAccName empty'), self::CODE_PAO_BANKACCNAME_INVALID);
            }
            // 4b: !account_name_modify_allowed && (player_details.first_name not set || player_details.first_name != bankAccountFullName)
            else if (!$rules_faa['account_name_allow_modify_by_players'] &&
                ( empty($player_details['firstName']) || $player_details['firstName'] != $args['bankAccountFullName'] ) ) {
        		throw new Exception(lang('bankAccName invalid, must be exactly the same as your registered real name'), self::CODE_PAO_BANKACCNAME_INVALID);
            }
            // 4c: account_name_modify_allowed
            else if ($rules_faa['account_name_allow_modify_by_players']) {
                // bankAccountFullName should be non-empty here; so do nothing
            }

            // 5: Check fields by $rules_faa['field_required'] (converted into 'required_fields')
            foreach ($rules_faa['required_fields'] as $rf_key) {
                if (!isset($args[$rf_key]) || empty($args[$rf_key])) {
                    throw new Exception(sprintf(lang("Required field (%s) absent"), $rf_key), self::CODE_PAO_BANKACC_FIELD_MISSING);
                }
            }

    		// Build insertset now - other fields are all optional
    		$dwBank = '';
    		$mesg_success = '';
    		switch ($mode) {
    			case 'deposit' :
    				$dwBank = Playerbankdetails::DEPOSIT_BANK;
    				$mesg_success = lang('Deposit account added successfully');
    				break;
    			case 'withdraw' : default :
    				$dwBank = Playerbankdetails::WITHDRAWAL_BANK;
    				$mesg_success = lang('Withdrawal account added successfully');
    				break;
    		}

    		// Check duplicate
    		$not_unique = $this->playerbankdetails->checkUniqueBankAccountNumber($args['bankAccountNumber'], $dwBank, $player_id, $args['bankTypeId']);
    		if ($not_unique) {
    			throw new Exception(lang('Bank account already exists'), self::CODE_PAO_BANKACC_ALREADY_EXISTS);
			}

    		$args_full = array_merge($args , [
    			'playerId'		=> $player_id ,
    			'isRemember'	=> Playerbankdetails::REMEMBERED,
				'dwBank'		=> $dwBank,
				'status'		=> Playerbankdetails::STATUS_ACTIVE
    		]);

    		$new_pbd_id = $this->playerbankdetails->addBankDetailsByWithdrawal($args_full);

    		if ($new_pbd_id <= 0) {
    			throw new Exception(lang('Error adding new bank account'), self::CODE_PAO_ERROR_ADDING_ACCOUNT);
    		}

    		$add_res = array_merge([ 'playerBankDetailsId' => $new_pbd_id], $args);

			$ret = [
			    'success'   => true,
			    'code'      => self::CODE_SUCCESS,
			    'mesg'      => $mesg_success,
			    'result'    => $add_res
			];

			$this->comapi_log(__METHOD__, 'response', $ret);
		}
		catch (Exception $ex) {
			$this->comapi_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);

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
    }

    /**
     * removes player's deposit bank account
     * See _removePlayerAccount_general() for detailed POST arguments
     * @see		comapi_core_accounts::_removePlayerAccount_general()
     * @return	JSON	General JSON return object [ success, code, message, result ]
     */
    public function removePlayerWithdrawAccount() {
    	$this->_removePlayerAccount_general('withdraw');
    }

    /**
     * removes player's withdrawal bank account
     * See _removePlayerAccount_general() for detailed POST arguments
     * @see		comapi_core_accounts::_removePlayerAccount_general()
     * @return	JSON	General JSON return object [ success, code, message, result ]
     */
    public function removePlayerDepositAccount() {
    	$this->_removePlayerAccount_general('deposit');
    }

    /**
     * General method for adding player bank account
     * Worker method for removePlayerWithdrawAccount/removePlayerDepositAccount
     * OGP-16986
     * @see		comapi_core_accounts::addPlayerWithdrawAccount()
     * @see		comapi_core_accounts::addPlayerDepositAccount()
     *
     * @param	string	$mode			account mode, 'deposit' or 'withdraw'
     *
     * @uses	string	POST: api_key		api key given by system
     * @uses	string	POST: username		Player username
     * @uses	string	POST: token			Player's login token
     * @uses	int		POST: bankDetailsId	bankDetailsId listed in API listPlayerDepositAccount (queryDepositBank)/ listPlayerWithdrawAccount return
     * @return	JSON
     */
    protected function _removePlayerAccount_general($mode) {
    	$api_key = $this->input->post('api_key');

		if (!$this->__checkKey($api_key)) { return; }

		$res = null;
		$rmv_res = null;
		try {
			$this->load->model([ 'playerbankdetails' , 'banktype' ]);

			$token			= trim($this->input->post('token'			, true));
    		$username		= trim($this->input->post('username'		, true));
    		$bankDetailsId	= (int) $this->input->post('bankDetailsId'	, true);
    		$request = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token, 'mode' => $mode, 'bankDetailsId' => $bankDetailsId ];
            $this->comapi_log(__METHOD__, 'request', $request);

    		// 1: Check player username
    		$player_id	= $this->player_model->getPlayerIdByUsername($username);
    		if (empty($player_id)) {
    			throw new Exception('Player username invalid', self::CODE_COMMON_INVALID_USERNAME);
    		}

    		// 2: Check token
    		if (!$this->__isLoggedIn($player_id, $token)) {
    			throw new Exception('Token invalid or player not logged in', self::CODE_COMMON_INVALID_TOKEN);
    		}

    		// 3: Prevent malformed access
    		if (!in_array($mode, [ 'deposit', 'withdraw' ])) {
    			$this->comapi_log(__METHOD__, 'Exception', 'mode invalid');
    			throw new Exception(lang('Account removal failed'), self::CODE_PRO_ACC_REMOVAL_FAILED);
    		}

    		// 4: Check bankDetailsId
			$player_accs = $this->comapi_lib->player_bank_accounts($player_id, $mode, 'id_only');
			$this->comapi_log(__METHOD__, 'Removing bank account', [ 'bankDetailsId' => $bankDetailsId, 'player_accs' => $player_accs ]);

			if (!in_array($bankDetailsId, $player_accs)) {
				throw new Exception(lang('bankDetailsId invalid or does not belong to the player'), self::CODE_PRO_BANKDETAILSID_INVALID);
			}

			$acc_is_default = $this->comapi_lib->is_player_default_account($player_id, $bankDetailsId, $mode);

			$this->comapi_log(__METHOD__, [ 'acc_is_default' => $acc_is_default, 'player_id' => $player_id, 'bankDetailsId' => $bankDetailsId ]);

			// If all set
			$rmv_res = $this->playerbankdetails->deletePlayerBankInfo($bankDetailsId);

			if (!$rmv_res) {
				$this->comapi_log(__METHOD__, 'Exception', 'deletePlayerBankInfo failed', [ 'rmv_res' => $rmv_res ]);
				throw new Exception(lang('Account removal failed'), self::CODE_PRO_ACC_REMOVAL_FAILED);
			}

			// Set new default if available
			$player_accs = array_diff($player_accs, [ $bankDetailsId ]);
			if ($acc_is_default && count($player_accs) > 0) {
				sort($player_accs);
				$new_default_acc_id = reset($player_accs);
				$acc_flags = [ 'deposit' => Playerbankdetails::DEPOSIT_BANK, 'withdraw' => Playerbankdetails::WITHDRAWAL_BANK ];

				$sd_res = $this->playerbankdetails->setPlayerDefaultBank($player_id, $acc_flags[$mode], $new_default_acc_id);
				$this->comapi_log(__METHOD__, 'setting new default bank', [ 'player_id' => $player_id, 'mode' => $mode, 'bankDetailsId' => $new_default_acc_id, 'set_result' => $sd_res ]);
			}

			// Success return
    		$ret = [
			    'success'   => true,
			    'code'      => self::CODE_SUCCESS,
			    'mesg'      => lang('Account successfully removed') ,
			    'result'    => null
			];

			$this->comapi_log(__METHOD__, 'response', $ret);
		}
		catch (Exception $ex) {
			$this->comapi_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);

			$ret = [
			    'success'   => false,
			    'code'      => $ex->getCode(),
			    'mesg'      => $ex->getMessage(),
			    'result'    => null
			];
		}
		finally {
			$this->returnApiResponseByArray($ret);
		}
    }

    public function queryDepositBank(){
        $api_key    = $this->input->post('api_key'  , 1);
        $username   = $this->input->post('username' , 1);
        $token      = $this->input->post('token'    , 1);

        if (!$this->__checkKey($api_key)) { return; }

        try {
            $request = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
            $this->comapi_log(__METHOD__, 'request', $request);

            $this->load->model(['player_model', 'playerbankdetails']);

            // Check player username
            $player_id  = $this->player_model->getPlayerIdByUsername($username);
            if (empty($player_id)) {
                throw new Exception('Player username invalid', self::CODE_INVALID_USER);
            }

            // Check player token
            $logcheck = $this->_isPlayerLoggedIn($player_id, $token);
            if ($logcheck['code'] != 0) {
                throw new Exception($logcheck['mesg'], $logcheck['code']);
            }

            $playerBankDetails = $this->playerbankdetails->getDepositBankDetails($player_id);
            $result = array();

            foreach($playerBankDetails as $bankinfo) {
                $BankDetail = $this->playerbankdetails->getBankDetailsById($bankinfo['playerBankDetailsId']);
                $bankinfo['bankName'] = lang($bankinfo['bankName']);
                $bankinfo['bankAccountNumber'] = $BankDetail['bankAccountNumber'];
                $result[] = $bankinfo;
            }

            if (empty($result)) {
                throw new Exception(lang('Query deposit bank details empty'), self::CODE_QUERY_DEPOSITBANK_EMPTY);
            }

            $ret = [
                'success'   => true,
                'code'      => 0,
                'mesg'      => 'Successfully got deposit bank details',
                'result'    => $result
            ];
        }
        catch (Exception $ex) {
            $ex_log = [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ];
            $this->comapi_log(__FUNCTION__, 'Exception', $ex_log);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => null
            ];
        }
        finally {
            // $this->comapi_log(__FUNCTION__, 'Response', $ret);
            $this->returnApiResponseByArray($ret);
        }
    } // End function queryDepositBank()

	public function listPlayerDepositAccounts() {
		$this->queryDepositBank();
	}

    /**
     * Lists player's existing withdrawal accounts.
     *
     * @uses	string	POST: api_key	api key given by system
     * @uses	string	POST: username	Player username
     * @uses	string	POST: token		Effective token
     *
     * @return	JSON	General JSON return object, with result = [  withdrawal account details ]
     */
	public function listPlayerWithdrawAccounts() {
    	$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }

    	try {
    		$this->load->model([ 'player_model', 'playerbankdetails' ]);

    		// Read arguments
    		$token			= $this->input->post('token');
    		$username		= $this->input->post('username');
    		$std_creds 		= [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
    		$this->utils->debug_log(__FUNCTION__, 'request', $std_creds);

    		// Check player username
    		$player_id	= $this->player_model->getPlayerIdByUsername($username);
    		if (empty($player_id)) {
    			throw new Exception('Player username invalid', self::CODE_LPWA_PLAYER_USERNAME_INVALID);
    		}

    		// Check player token
    		if (!$this->__isLoggedIn($player_id, $token)) {
                // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
                throw new Exception('Token invalid or player not logged in', self::CODE_COMMON_INVALID_TOKEN);
    			// throw new Exception('Token invalid or player not logged in', self::CODE_LPWA_PLAYER_TOKEN_INVALID);
    		}

            $wd_accounts = $this->comapi_lib->player_withdraw_accounts($player_id);

	    	$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> 'Successfully got withdraw accounts for player' ,
	    		'result'	=> $wd_accounts
	    	];
    	}
    	catch (Exception $ex) {
	    	$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], ['username' => $username, 'token' => $token]);
	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
	} // End function listPlayerWithdrawAccounts()

    /**
     * Sets default withdraw account for player
     * @uses    string  POST:api_key        api key given by system
     * @uses    string  POST:username       Player username
     * @uses    string  POST:token          Effective token for player
     * @uses    int     POST:bankDetailsId  Must match any playerBankDetailsId returned by listPlayerWithdrawAccounts
     *
     * @return  JSON    Standard JSON return structure [ success, code, mesg, result ]
     */
    public function setPlayerWithdrawAccountDefault() {
        $api_key = $this->input->post('api_key');
        if (!$this->__checkKey($api_key)) { return; }

        try {
            $this->load->model([ 'playerbankdetails' ]);
            // Read arguments
            $token          = $this->input->post('token', true);
            $username       = $this->input->post('username', true);
            $bankDetailsId  = (int) $this->input->post('bankDetailsId', true);

            $request        = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token, 'bankDetailsId' => $bankDetailsId ];
            $this->utils->debug_log(__FUNCTION__, 'request', $request);

            // Check player username
            $player_id  = $this->player_model->getPlayerIdByUsername($username);
            if (empty($player_id)) {
                throw new Exception('Player username invalid', self::CODE_INVALID_USER);
            }

            // Check player token
            $logcheck = $this->_isPlayerLoggedIn($player_id, $token);
            if ($logcheck['code'] != 0) {
                throw new Exception($logcheck['mesg'], $logcheck['code']);
            }

            $player_wd_account_ids = $this->comapi_lib->player_withdraw_accounts($player_id, 'return id only');

            $this->comapi_log(__METHOD__, 'player_wd_account_ids', $player_wd_account_ids);

            if (!in_array($bankDetailsId, $player_wd_account_ids)) {
                throw new Exception("bankDetailsId ({$bankDetailsId}) does not belong to player", self::CODE_PAO_NOT_PLAYERS_WD_ACCOUNT);
            }

            $set_res = $this->playerbankdetails->setPlayerDefaultBank($player_id, Playerbankdetails::WITHDRAWAL_BANK, $bankDetailsId);

            if (!$set_res) {
                throw new Exception("Error while setting default withdraw account", self::CODE_PAO_ERROR_SET_DEFAULT_WD_ACCOUNT);
            }

            $ret = [
                'success'   => true,
                'code'      => 0,
                'mesg'      => lang('Successfully set default withdraw account for player'),
                'result'    => null
            ];
        }
        catch (Exception $ex) {
            $ex_log = [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ];
            $this->comapi_log(__FUNCTION__, 'Exception', $ex_log);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => null
            ];
        }
        finally {
            $this->comapi_log(__FUNCTION__, 'Response', $ret);
            $this->returnApiResponseByArray($ret);
        }
    } // End function setPlayerWithdrawAccountDefault()

} // End trait comapi_core_accounts