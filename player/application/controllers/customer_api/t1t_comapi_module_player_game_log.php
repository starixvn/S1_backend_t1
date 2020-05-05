<?php

trait t1t_comapi_module_player_game_log {

	/**
	 * Returns player's detailed game logs.
	 * Note: This is a administration-level API method, requires no player token.
	 * Not intended for directly use of players.
	 *
	 * @uses	string		POST:username		Effective username
	 * @uses	datetime	POST:time_from		Datetime of query start.  Defaults to begin of today.
	 * @uses	datetime	POST:time_to		Datetime of query end.  Defaults to end of today.
	 * @uses	int			POST:timezone		Timezone.  Defaults to system timezone (+8 for CST)
	 * @uses	int			POST:limit			Limit, for paging use
	 * @uses	int			POST:offset			Offset, for paging use
	 * @uses	string		POST:game_provider	Game provider, like AG, PT.  Caseless
	 * @uses	string		POST:game_code		Game code, caseless
	 * @uses	string		POST:flag			1 for game logs, 2 for transactions (subwallet transfers)
	 *
	 * @return	array 		Array of game log rows
	 */
	public function getPlayerGameLogs() {
		$api_key = $this->input->post('api_key');
	    if (!$this->__checkKey($api_key)) { return; }

	    try {
	    	$username		= $this->input->post('username'		, true);
	    	$raw_time_from	= $this->input->post('time_from'	, true);
			$raw_time_to	= $this->input->post('time_to'		, true);
			$timezone		= intval($this->input->post('timezone'	, true));
			$limit 			= intval($this->input->post('limit'		, true));
			$offset 		= intval($this->input->post('offset'	, true));
			$game_provider	= $this->input->post('game_provider'	, true);
			$game_code		= $this->input->post('game_code'		, true);
			$flag			= intval($this->input->post('flag'		, true));

			$request = [ 'api_key' => $api_key, 'username' => $username, 'time_from' => $raw_time_from, 'time_to' => $raw_time_to, 'timezone' => $timezone, 'limit' => $limit, 'offset' => $offset, 'flag' => $flag ];

			$this->utils->debug_log(__FUNCTION__, 'Request', $request);

			$player_id = $this->player_model->getPlayerIdByUsername($username);

			if (empty($player_id)) {
				throw new Exception('Invalid username', self::CODE_INVALID_USER);
			}

			// time_from, time_to
			$dt_from	= strtotime(empty($raw_time_from) ? 'today 00:00' : $raw_time_from);
			$dt_to		= strtotime(empty($raw_time_to) ? 'today 23:59:59' : $raw_time_to);

			// timezone correction
			$tz_default = intval(date('Z')) / 3600;
			if (!empty($timezone)) {
				$tz_offset = ($timezone - $tz_default) * 3600;
				$dt_from += $tz_offset;
				$dt_to += $tz_offset;
			}

			$time_from	= date('c', $dt_from);
			$time_to	= date('c', $dt_to);

			// limit, offset
			if (empty($limit)) { $limit = 30; }

			// flag
			if (!in_array($flag, [ 1, 2 ])) { $flag = 0; }

			// game_provider, game_code
			$game_provider	= preg_replace('/\W/u', '', $game_provider);
			$game_code		= preg_replace('/\W/u', '', $game_code);

			$res = $this->game_logs->getGameLogs_api($player_id, $time_from, $time_to, $flag, $game_provider, $game_code, $limit, $offset);

			$ret = [
			    'success'   => true,
			    'code'      => self::CODE_SUCCESS,
			    'mesg'      => "Player game logs retrieved successfully",
			    'result'    => $res
			];

			$this->utils->debug_log(__METHOD__, 'response', $res, $request);
		}
		catch (Exception $ex) {
			$this->utils->debug_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $request);

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

} // End of trait t1t_comapi_module_player_game_log