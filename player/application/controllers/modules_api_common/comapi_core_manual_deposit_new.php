<?php

/**
 * Api_common core module: new manual deposit series
 * Built 711/2019
 * @see		api_common.php
 */
trait comapi_core_manual_deposit_new {

	/**
	 * Returns account information specified by bankTypeId
	 * OGP-13251
	 *
	 * @uses    string  POST:api_key        api key given by system
	 * @uses    string  POST:username       Player username
	 * @uses    string  POST:token          Effective token for player
	 * @uses    int     POST:bankTypeId     == payment_account.bankTypeId
	 * @uses    int     POST:is_mobile		Mobile flag
	 *
	 * @return  JSON
	 */
	public function manualDepositForm() {
		$api_key = $this->input->post('api_key');
		if (!$this->__checkKey($api_key)) { return; }

		try {
			// Read arguments
			$token          = $this->input->post('token', true);
			$username       = $this->input->post('username', true);
			$bankTypeId		= (int) $this->input->post('bankTypeId', true);
			$pay_acc_id		= (int) $this->input->post('pay_acc_id', true);
			$is_mobile		= !empty($this->input->post('is_mobile', true));

			$request        = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token, 'bankTypeId' => $bankTypeId, 'pay_acc_id' => $pay_acc_id, 'is_mobile' => $is_mobile ];
			$this->comapi_log(__METHOD__, 'request', $request);

			// Check player username
			$player_id  = $this->player_model->getPlayerIdByUsername($username);
			if (empty($player_id)) {
				throw new Exception('Player username invalid', self::CODE_COMMON_INVALID_USERNAME);
			}

			// Check player token
			if (!$this->__isLoggedIn($player_id, $token)) {
				throw new Exception(lang('Token invalid or player not logged in'), self::CODE_COMMON_INVALID_TOKEN);
			}

			$dep_list = $this->comapi_lib->depcat_deposit_paycats($player_id);

			if (empty($this->comapi_lib->depcat_pay_account_by_type_bankTypeId($player_id, $bankTypeId, Comapi_lib::DEPCAT_MANUAL, $is_mobile))) {
				throw new Exception(lang("bankTypeId '{$bankTypeId}' not a valid manual payment or not available for player"), self::CODE_MDN_BANKTYPEID_NOT_VALID_MANU_PAY);
			}

			// OGP-14655: Use pay_acc_id as secondary ident for coll accounts other than bankTypeId
			$dep_acc = $this->comapi_lib->depcat_manu_account_info($bankTypeId, $player_id, 'generate secure_id', $pay_acc_id);

			if (!empty($dep_acc['code'])) {
				throw new Exception($dep_acc['mesg'], $dep_acc['code']);
			}

			$ret = [
				'success'   => true,
				'code'      => 0,
				'mesg'      => lang('Manual deposit info retrieved successfully'),
				'result'    => $dep_acc['result']
			];
			$this->comapi_log(__METHOD__, 'Successful response', $ret);
		}
		catch (Exception $ex) {
			$ex_log = [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ];
			$this->comapi_log(__METHOD__, 'Exception', $ex_log);

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
	} // End function manualDepositForm()

	/**
	 * Built in adherence to our type-2 deposit flow, OGP-13326
	 * OGP-16136: Minor refit to cover type-1 deposit flow (2020/02)
	 *
	 * @return  JSON    Standard JSON return structure
	 */
	public function manualDepositRequest() {
		$api_key = $this->input->post('api_key');
		if (!$this->__checkKey($api_key)) { return; }

		try {
			// $this->load->model([  ]);
			// Read arguments
			$token          = trim($this->input->post('token', true));
			$username       = trim($this->input->post('username', true));
			$bankTypeId     = (int) $this->input->post('bankTypeId', true);
			$pay_acc_id		= (int) $this->input->post('pay_acc_id', true);
			$firstName      = $this->input->post('firstName', true);
			$secure_id		= trim($this->input->post('secure_id', true));
			$amount			= floatval($this->input->post('amount', true));
			$promo_cms_id	= (int) $this->input->post('promo_cms_id', true);
			$is_mobile		= !empty($this->input->post('is_mobile', true));
			$playerBankDetailsId	= (int) $this->input->post('playerBankDetailsId', true);
			$mode_of_deposit		= trim($this->input->post('mode_of_deposit', true));
			$deposit_time			= trim($this->input->post('deposit_time', true));

			$request		= [
				'api_key' => $api_key, 'username' => $username, 'token' => $token,
				'bankTypeId' => $bankTypeId, 'pay_acc_id' => $pay_acc_id,
				'firstName' => $firstName,	'secure_id' => $secure_id,
				'amount' => $amount, 'promo_cms_id' => $promo_cms_id, 'is_mobile' => $is_mobile,
				'playerBankDetailsId' => $playerBankDetailsId, 'deposit_time' => $deposit_time, 'mode_of_deposit' => $mode_of_deposit
			];

			$this->comapi_log(__METHOD__, 'request', $request);

			// Check player username
			$player_id  = $this->player_model->getPlayerIdByUsername($username);
			if (empty($player_id)) {
				throw new Exception('Player username invalid', self::CODE_COMMON_INVALID_USERNAME);
			}

			// Check player token
			if (!$this->__isLoggedIn($player_id, $token)) {
				throw new Exception(lang('Token invalid or player not logged in'), self::CODE_COMMON_INVALID_TOKEN);
			}

			$dep_list = $this->comapi_lib->depcat_deposit_paycats($player_id);

			if (empty($this->comapi_lib->depcat_pay_account_by_type_bankTypeId($player_id, $bankTypeId, Comapi_lib::DEPCAT_MANUAL))) {
				throw new Exception(lang('bankTypeId not a valid manual payment or not available for player'), self::CODE_MDN_BANKTYPEID_NOT_VALID_MANU_PAY);
			}

			$secure_id_probe = $this->sale_order->getSaleOrderBySecureId($secure_id);

			if (empty($secure_id) || !empty($secure_id_probe)) {
				throw new Exception(lang('Invalid secure_id'), self::CODE_MDN_INVALID_SECURE_ID);
			}

			$dep_acc = $this->comapi_lib->depcat_manu_account_info($bankTypeId, $player_id, 'generate secure_id', $pay_acc_id);

			if ($dep_acc['code'] != 0) {
				throw new Exception(lang('Payment account not available'), self::CODE_MDN_PAYMENT_ACCOUNT_NOT_ACCESSIBLE);
			}

			// Optional arguments: playerBankDetailsId, mode_of_deposit, deposit_time

			// Check playerBankDetailsId: empty or belongs to player
			if (!empty($playerBankDetailsId) && !$this->playerbankdetails->isValidBankForPlayer($playerBankDetailsId, $player_id)) {
				throw new Exception(lang('playerBankDetailsId invalid, must be active and belongs to deposit player'), self::CODE_MDN_PLAYERBANK_INVALID);
			}

			// Check mode_of_deposit: empty or in $this->config->item('mode_of_deposit')
			$all_modes_of_deposit = $this->config->item('mode_of_deposit');
			if (!empty($mode_of_deposit) && !in_array($mode_of_deposit, $all_modes_of_deposit)) {
				throw new Exception(lang('mode_of_deposit invalid, must be empty or any of following: ') . json_encode($all_modes_of_deposit), self::CODE_MDN_DEPOSIT_METHOD_INVALID);
			}

			// Check deposit_time: empty or valid datetime
			if (!empty($deposit_time)) {
				$dep_time_parsed = strtotime($deposit_time);
				if (empty($dep_time_parsed)) {
					throw new Exception(lang('deposit_time invalid, must be valid datetime'), self::CODE_MDN_DEPOSIT_TIME_INVALID);
				}
			}

			// Build deposit dataset
			$deposit_dataset = $request;
			$deposit_dataset['playerBankDetailsId']	= $playerBankDetailsId;
			$deposit_dataset['mode_of_deposit']		= $mode_of_deposit;
			$deposit_dataset['deposit_datetime']	= $deposit_time;

			unset($deposit_dataset['api_key']);
			unset($deposit_dataset['token']);
			$deposit_dataset['player_id'] = $player_id;
			$deposit_dataset['payment_account_id'] = $dep_acc['result']['payment_account_id'];
			$deposit_dataset['deposit_notes'] = 'by comapi manualDepositRequest';

			$deposit_res = $this->comapi_lib->comapi_manual_deposit($deposit_dataset);

			if ($deposit_res['success'] == false) {
				throw new Exception($deposit_res['mesg'], $deposit_res['code']);
			}

			$this->comapi_log(__METHOD__, 'deposit_res', $deposit_res);

			$ret = [
				'success'   => true,
				'code'      => 0,
				'mesg'      => $deposit_res['mesg'],
				'result'    => isset($res_err) ? $res_err : null
			];
			$this->comapi_log(__METHOD__, 'Successful response', $ret);
		}
		catch (Exception $ex) {
			$ex_log = [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ];
			$this->comapi_log(__METHOD__, 'Exception', $ex_log);

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
	} // End function manualDepositRequest()

	protected function format_last_deposit_status($status, $timeout_at) {
		$this->load->model([ 'sale_order' ]);

		$allow_deposit = false;

		$status_to_text = [
			Sale_order::STATUS_DECLINED	=> 'declined' ,
			Sale_order::STATUS_SETTLED	=> 'success' ,
		];

		if (isset($status_to_text[$status])) {
			$stat_text = $status_to_text[$status];
			$allow_deposit = true;
		}
		else {
			if ($status == Sale_order::STATUS_PROCESSING) {
				$stat_text = strtotime($timeout_at) < time() ? 'timeout' : 'processing';
				$allow_deposit = false;
			}
			else {
				$stat_text = null;
				$allow_deposit = true;
			}
		}

		return [ 'status_text' => $stat_text, 'allow_deposit' => $allow_deposit ];
	}

	public function manualDepositLastResult() {
		$api_key = $this->input->post('api_key');
		if (!$this->__checkKey($api_key)) { return; }

		try {
			$this->load->model([ 'sale_order' ]);
			// Read arguments
			$token          = $this->input->post('token', true);
			$username       = $this->input->post('username', true);

			$request        = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
			$this->comapi_log(__METHOD__, 'request', $request);

			// Check player username
			$player_id  = $this->player_model->getPlayerIdByUsername($username);
			if (empty($player_id)) {
				throw new Exception('Player username invalid', self::CODE_COMMON_INVALID_USERNAME);
			}

			// Check player token
			if (!$this->__isLoggedIn($player_id, $token)) {
				throw new Exception(lang('Token invalid or player not logged in'), self::CODE_COMMON_INVALID_TOKEN);
			}

			$ldeposit = $this->sale_order->getLastManuallyDeposit($player_id);

			$this->comapi_log(__METHOD__, 'ldeposit', $ldeposit);

			if (empty($ldeposit)) {
				throw new Exception(lang('No manual deposit transaction found for player'), self::CODE_MDN_NO_MANUAL_DEPOSIT_FOR_PLAYER);
			}

			$stat_tuple = $this->format_last_deposit_status($ldeposit['status'], $ldeposit['timeout_at']);

			$ld_res = [
				'status'		=> $stat_tuple['status_text'] ,
				'secure_id'		=> $ldeposit['secure_id'] ,
				'bank'			=> lang($ldeposit['payment_type_name']) ,
				'amount'		=> $ldeposit['amount'] ,
				'deposit_time'	=> $ldeposit['created_at'] ,
				// 'allow_deposit'	=> $stat_tuple['allow_deposit'] ,
			];

			$ret = [
				'success'   => true,
				'code'      => 0,
				'mesg'      => lang('Latest deposit info retrieved successfully'),
				'result'    => $ld_res
			];
			$this->comapi_log(__METHOD__, 'Successful response', $ret);
		}
		catch (Exception $ex) {
			$ex_log = [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ];
			$this->comapi_log(__METHOD__, 'Exception', $ex_log);

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
	} // End function manualDepositLastResult()

	/**
	 * Upload endpoint for invoices (or other attachments) for manual deposit
	 * OGP-16735
	 * For ajax operation, use FormData object
	 *   var arg = new FormData($('form#form')[0]);
	 * @uses    string  POST:api_key        api key given by system
     * @uses    string  POST:username       Player username
     * @uses    string  POST:token          Effective token for player
     * @uses    string  POST:secure_id      secure_id of deposit order
     * @uses    image   POST:image[]        Image attachment
     *
	 * @return	JSON
	 */
	public function manualDepositAttUpload() {
		$max_num_images_per_deposit = comapi_lib::MAX_NUM_IMAGES_PER_DEPOSIT;
		$api_key = $this->input->post('api_key');
		if (!$this->__checkKey($api_key)) { return; }

		$ret_data = null;
		try {
			$this->load->model([ 'sale_order' ]);
			// Read arguments
			$token          = $this->input->post('token', true);
			$username       = $this->input->post('username', true);
			$secure_id		= $this->input->post('secure_id', true);

			$request        = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token, 'secure_id' => $secure_id ];
			$this->comapi_log(__METHOD__, 'request', $request);

			// Check player username
			$player_id  = $this->player_model->getPlayerIdByUsername($username);
			if (empty($player_id)) {
				throw new Exception('Player username invalid', self::CODE_COMMON_INVALID_USERNAME);
			}

			// Check player token
			if (!$this->__isLoggedIn($player_id, $token)) {
				throw new Exception(lang('Token invalid or player not logged in'), self::CODE_COMMON_INVALID_TOKEN);
			}

			$sale_order = $this->sale_order->getSaleOrderBySecureId($secure_id);

			if (empty($secure_id) || empty($sale_order)) {
				throw new Exception(lang('Secure_id not found'), self::CODE_MDU_INVALID_SECURE_ID);
			}

			$this->comapi_log(__METHOD__, 'files', $_FILES);

			if (empty($_FILES['image']) || reset($_FILES['image']['error']) == 4) {
				throw new Exception(lang('Upload file not accessible'), self::CODE_MDU_UPLOAD_FILE_NOT_ACCESSIBLE);
			}

			if (count($_FILES['image']['name']) > 1) {
				throw new Exception(lang('Please upload only one image at a time'), self::CODE_MDU_ONLY_ONE_UPLOAD_AT_A_TIME);
			}

			$image = $_FILES['image'];
			$this->comapi_log(__METHOD__, 'image', $image);

			$this->load->model([ 'player_attached_proof_file_model' ]);

			$count_images_uploaded = $this->comapi_lib->get_attach_count_by_sale_order_id($player_id, $sale_order->id);

			if ($count_images_uploaded >= $max_num_images_per_deposit) {
				throw new Exception(lang("Maximum number ({$max_num_images_per_deposit}) for each deposit order is reached"), self::CODE_MDU_MAX_ATT_NUM_PER_ORDER_REACHED);
			}

			$input = array(
                "player_id"       => $player_id,
                "tag"             => player_attached_proof_file_model::Deposit_Attached_Document,
                "sales_order_id"  => $sale_order->id
            );

            $dataset = [
            	'input' => $input ,
            	'image' => $image
            ];

            $upload_resp = $this->player_attached_proof_file_model->upload_deposit_receipt($dataset);

			$this->comapi_log(__METHOD__, 'upload_resp', $upload_resp);

			if ($upload_resp['msg_type'] == BaseController::MESSAGE_TYPE_ERROR) {
				$ret_data = $upload_resp['msg'];
				throw new Exception(lang("Error uploading file"), self::CODE_MDU_ERROR_UPLOADING_FILE);
			}

			$ret = [
				'success'   => true,
				'code'      => 0,
				'mesg'      => lang('Attachment uploaded successfully'),
				// 'result'    => $upload_resp
				'result'    => null
			];
			$this->comapi_log(__METHOD__, 'Successful response', $ret);
		}
		catch (Exception $ex) {
			$ex_log = [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ];
			$this->comapi_log(__METHOD__, 'Exception', $ex_log);

			$ret = [
				'success'   => false,
				'code'      => $ex->getCode(),
				'mesg'      => $ex->getMessage(),
				'result'    => $ret_data ?: null
			];
		}
		finally {
			$this->returnApiResponseByArray($ret);
		}
	} // End function manualDepositAttUpload()

} // End of trait comapi_core_manual_deposit_new