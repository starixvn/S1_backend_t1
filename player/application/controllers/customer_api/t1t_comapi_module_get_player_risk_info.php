<?php

trait t1t_comapi_module_get_player_risk_info {

	/**
	 * Returns player's combined risk information
	 * OGP-12222
	 * Note: This is an administration-level API method, requires no player token.
	 * Not intended for players' directly use.
	 *
	 * @return	array 	assoc array of categoried risk information
	 */
	public function getPlayerRiskInfo() {
		$api_key = $this->input->post('api_key');
	    if (!$this->__checkKey($api_key)) { return; }

	    $rinfo_res = null;

	    try {
	    	$username		= $this->input->post('username'		, true);

			$request = [ 'api_key' => $api_key, 'username' => $username ];

			$this->utils->debug_log(__FUNCTION__, 'Request', $request);

			$this->load->model(['player_model', 'comapi_reports' ]);

			$player_id = $this->player_model->getPlayerIdByUsername($username);

			if (empty($player_id)) {
				throw new Exception('Invalid username', self::CODE_INVALID_USER);
			}

			$rinfo_res = $this->comapi_reports->getPlayerRiskInfo($player_id);

			$ret = [
			    'success'   => true,
			    'code'      => self::CODE_SUCCESS,
			    'mesg'      => "Player risk info retrieved successfully",
			    'result'    => $rinfo_res
			];

			$this->utils->debug_log(__METHOD__, 'response', $rinfo_res, $request);
		}
		catch (Exception $ex) {
			$this->utils->debug_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $request);

			$ret = [
			    'success'   => false,
			    'code'      => $ex->getCode(),
			    'mesg'      => $ex->getMessage(),
			    'result'    => $rinfo_res
			];
		}
		finally {
			$this->returnApiResponseByArray($ret);
		}
	}

	public function getPlayerDepositHistory() {
		$api_key = $this->input->post('api_key');
	    if (!$this->__checkKey($api_key)) { return; }

	    $rinfo_res = null;

	    try {
	    	$username		= $this->input->post('username' 	, true);
	    	$raw_time_from	= $this->input->post('time_from'	, true);
			$raw_time_to	= $this->input->post('time_to'		, true);
			$limit 			= intval($this->input->post('limit'		, true));
			$offset 		= intval($this->input->post('offset'	, true));
			$filter			= intval($this->input->post('filter'	, true));

			$request = [ 'api_key' => $api_key, 'username' => $username, 'filter' => $filter, 'time_from' => $raw_time_from, 'time_to' => $raw_time_to, 'limit' => $limit, 'offset' => $offset ];

			$this->utils->debug_log(__FUNCTION__, 'Request', $request);

			$this->load->model(['player_model', 'comapi_reports' ]);

			$player_id = $this->player_model->getPlayerIdByUsername($username);

			if (empty($player_id)) {
				throw new Exception('Invalid username', self::CODE_INVALID_USER);
			}

			// time_from, time_to
			$dt_from	= date('c', strtotime(empty($raw_time_from) ? 'today 00:00' : $raw_time_from));
			$dt_to		= date('c', strtotime(empty($raw_time_to) ? 'today 23:59:59' : $raw_time_to));

			// limit
			if (empty($limit)) { $limit = 10; }

			// filter
			if (empty($filter)) { $filter = 1; }

			$rinfo_res = $this->comapi_reports->getPlayerDepositHistory($player_id, $filter, $dt_from, $dt_to, $limit, $offset);

			$ret = [
			    'success'   => true,
			    'code'      => self::CODE_SUCCESS,
			    'mesg'      => "Player deposit history retrieved successfully",
			    'result'    => $rinfo_res
			];

			$this->utils->debug_log(__METHOD__, 'response', $rinfo_res, $request);
		}
		catch (Exception $ex) {
			$this->utils->debug_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $request);

			$ret = [
			    'success'   => false,
			    'code'      => $ex->getCode(),
			    'mesg'      => $ex->getMessage(),
			    'result'    => $rinfo_res
			];
		}
		finally {
			$this->returnApiResponseByArray($ret);
		}
	}

	public function getPlayerWithdrawHistory() {
		$api_key = $this->input->post('api_key');
	    if (!$this->__checkKey($api_key)) { return; }

	    $rinfo_res = null;

	    try {
	    	$username		= $this->input->post('username' 	, true);
	    	$raw_time_from	= $this->input->post('time_from'	, true);
			$raw_time_to	= $this->input->post('time_to'		, true);
			$limit 			= intval($this->input->post('limit'		, true));
			$offset 		= intval($this->input->post('offset'	, true));
			// $filter			= intval($this->input->post('filter'	, true));

			$request = [ 'api_key' => $api_key, 'username' => $username, 'time_from' => $raw_time_from, 'time_to' => $raw_time_to, 'limit' => $limit, 'offset' => $offset ];

			$this->utils->debug_log(__FUNCTION__, 'Request', $request);

			$this->load->model(['player_model', 'comapi_reports' ]);

			$player_id = $this->player_model->getPlayerIdByUsername($username);

			if (empty($player_id)) {
				throw new Exception('Invalid username', self::CODE_INVALID_USER);
			}

			// time_from, time_to
			$dt_from	= date('c', strtotime(empty($raw_time_from) ? 'today 00:00' : $raw_time_from));
			$dt_to		= date('c', strtotime(empty($raw_time_to) ? 'today 23:59:59' : $raw_time_to));

			// limit
			if (empty($limit)) { $limit = 10; }

			// filter
			// if (empty($filter)) { $filter = 1; }

			$rinfo_res = $this->comapi_reports->getPlayerWithdrawHistory($player_id, $dt_from, $dt_to, $limit, $offset);

			$ret = [
			    'success'   => true,
			    'code'      => self::CODE_SUCCESS,
			    'mesg'      => "Player withdraw history retrieved successfully",
			    'result'    => $rinfo_res
			];

			$this->utils->debug_log(__METHOD__, 'response', $rinfo_res, $request);
		}
		catch (Exception $ex) {
			$this->utils->debug_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $request);

			$ret = [
			    'success'   => false,
			    'code'      => $ex->getCode(),
			    'mesg'      => $ex->getMessage(),
			    'result'    => $rinfo_res
			];
		}
		finally {
			$this->returnApiResponseByArray($ret);
		}
	}

} // End trait t1t_comapi_module_get_player_info {
