<?php

/**
 * General behaviors include :
 *
 * * Sync total information
 * * Fix yesterday total hours
 * * Validate totals
 * * Rebuild totals
 * * Fix day month and year
 * * Sync all total hours
 * * Sync total statistics
 *
 * @category Command line
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
trait validate_totals_module {

	public function rebuild_month_year_with_player($dateTimeFromStr = null, $dateTimeToStr = null) {
		$this->sync_long_total_info($dateTimeFromStr, $dateTimeToStr, 'true');
	}

	/**
	 * overview : sync long total information
	 * @param date $dateTimeFromStr
	 * @param date $dateTimeToStr
	 * @return mixed
	 */
	public function sync_long_total_info($dateTimeFromStr = null, $dateTimeToStr = null, $update_player='true') {

		$this->utils->debug_log('dateTimeFromStr', $dateTimeFromStr, 'dateTimeToStr', $dateTimeToStr, 'update_player', $update_player);

		$update_player=$update_player=='true';

		$manager = $this->utils->loadGameManager();
		//default for last month
		if (empty($dateTimeFromStr) || empty($dateTimeToStr)) {
			list($dateTimeFromStr, $dateTimeToStr) = $this->utils->getFromToByMonth($this->utils->getYesterdayForMysql());
			$dateTimeFromStr = $dateTimeFromStr . ' 00:00:00';
			$dateTimeToStr = $dateTimeToStr . ' 23:59:59';
		}

		$dateTimeFrom = new \DateTime($dateTimeFromStr);
		$dateTimeTo = new \DateTime($dateTimeToStr);

		$manager->syncLongTotalStatsAll($dateTimeFrom, $dateTimeTo);

		$this->utils->debug_log('sync long total info from', $dateTimeFromStr, 'to', $dateTimeToStr);

		if($update_player){
			$this->load->model(['player_model']);
			// -- OGP-9124
			$this->utils->debug_log('START RUNNING: updatePlayersTotalBettingAmount');
			$updatePlayersTotalBettingAmount = $this->player_model->updatePlayersTotalBettingAmount();

			$this->utils->debug_log('RESULT OF updatePlayersTotalBettingAmount: Total count of players updated = '.$updatePlayersTotalBettingAmount);

			$this->utils->debug_log('END RUNNING: updatePlayersTotalBettingAmount');
		}

	}

	/**
	 * overview : sync total information
	 *
	 * @param $dateTimeFromStr
	 * @param $dateTimeToStr
	 */
	public function sync_total_info($dateTimeFromStr, $dateTimeToStr) {
		$manager = $this->utils->loadGameManager();

		$dateTimeFrom = new \DateTime($dateTimeFromStr);
		$dateTimeTo = new \DateTime($dateTimeToStr);

		$manager->syncTotalStatsAll($dateTimeFrom, $dateTimeTo);

		$msg = $this->utils->debug_log('sync total info from', $dateTimeFromStr, 'to', $dateTimeToStr);
		$this->returnText($msg);
	}

	/**
	 * @deprecated
	 */
	public function validate_daily_balance() {

	}

	/**
	 * overview : get yesterday total hours
	 */
	public function fix_yesterday_hour_totals() {
		$yesterday = $this->utils->getYesterdayForMysql();
		$this->validate_totals($yesterday . ' 00:00:00', $yesterday . ' 23:59:59');
	}

	/**
	 * overview : validate totals
	 * @param date $fromStr
	 * @param date $toStr
	 */
	protected function validate_totals($fromStr = null, $toStr = null) {
		$this->load->model(array('game_logs', 'total_player_game_minute', 'total_player_game_hour', 'total_player_game_day',
			'total_player_game_month', 'total_player_game_year'));
		if (empty($fromStr) || empty($toStr)) {
			$from = new DateTime($this->game_logs->getFirstDatetime());
			$to = $this->utils->getNowDateTime();
			//exclude current hour
			$to = $to->modify('-1 hour');
		} else {
			$from = new DateTime($fromStr);
			$to = new DateTime($toStr);
		}
		$this->utils->debug_log('from', $from, 'to', $to);

		$rlt = "from:" . $this->utils->formatDateTimeForMysql($from) . ", to:" . $this->utils->formatDateTimeForMysql($to) . "\n";

		$fixDays = array();
		$fixMonths = array();
		$fixYears = array();

		list($start, $end, $datehour) = $this->utils->getDateHourSet($from);
		while (!$this->utils->gtEndHour($from, $to)) {
			//rebuild minute
			$this->total_player_game_minute->startTrans();
			$this->total_player_game_minute->sync(new \DateTime($start), new \DateTime($end));
			$rlt = $this->total_player_game_minute->endTransWithSucc();
			$this->utils->debug_log('start', $start, 'end', $end, 'datehour', $datehour, 'rebuild minute result', $rlt);

			list($bet_amount, $result_amount, $win_amount, $loss_amount) = $this->game_logs->sumAmount($start, $end);

			list($bet_amount_compare, $result_amount_compare, $win_amount_compare, $loss_amount_compare) = $this->total_player_game_hour->sumAmount($datehour);

			$betAmountCompareResult = $this->utils->compareResultCurrency($bet_amount, '=', $bet_amount_compare);
			$resultAmountCompareResult = $this->utils->compareResultCurrency($result_amount, '=', $result_amount_compare);
			$winAmountCompareResult = $this->utils->compareResultCurrency($win_amount, '=', $win_amount_compare);
			$lossAmountCompareResult = $this->utils->compareResultCurrency($loss_amount, '=', $loss_amount_compare);
			// $this->utils->debug_log('bet_amount', $bet_amount, 'bet_amount_compare', $bet_amount_compare,
			// 	'result_amount', $result_amount, 'result_amount_compare', $result_amount_compare,
			// 	'bet amount compare', $betAmountCompareResult,
			// 	'result amount compare', $resultAmountCompareResult);

			if (!$betAmountCompareResult || !$resultAmountCompareResult ||
				!$winAmountCompareResult || !$lossAmountCompareResult) {
				//try fix it
				$this->total_player_game_hour->startTrans();

				$count = $this->total_player_game_hour->sync(new DateTime($start), new DateTime($end));
				$this->utils->debug_log('start', $start, 'end', $end, 'datehour', $datehour, 'fix total_player_game_hour', $count);

				$fixResult = $this->total_player_game_hour->endTransWithSucc();
				$rlt .= 'start:' . $start . ', end:' . $end .
					', bet_amount:' . $bet_amount . ', bet_amount_compare:' . $bet_amount_compare .
					', win_amount:' . $win_amount . ', win_amount_compare:' . $win_amount_compare .
					', loss_amount:' . $loss_amount . ', loss_amount_compare:' . $loss_amount_compare .
					', fix result:' . $fixResult . ', count:' . $count .
					"\n";
				$fixDays[] = substr($datehour, 0, 8);
				$fixMonths[] = substr($datehour, 0, 6);
				$fixYears[] = substr($datehour, 0, 4);
			}

			list($start, $end, $datehour) = $this->utils->getNextHour($from);
			$from = new DateTime($start);
		}
		$fixDays = array_unique($fixDays);
		$fixMonths = array_unique($fixMonths);
		$fixYears = array_unique($fixYears);

		$fixRlt = $this->fixDayMonthYear($fixDays, $fixMonths, $fixYears);
		$rlt .= 'days:' . var_export($fixDays, true) . ', months:' . var_export($fixMonths, true) .
		', years:' . var_export($fixYears, true) . "\n";
		$rlt .= 'fix result:' . $fixRlt . "\n";

		$this->returnText($rlt);
	}

	/**
	 * overview : rebuild all total hours and minutes
	 *
	 * @param int $hours
	 */
	public function rebuild_total_minute_hours($hours = 7) {

		$msg = $this->utils->debug_log('hours', $hours);
		$this->returnText($msg);

		$fromDateTime = new DateTime('-' . $hours. ' hours');
		//to now
		$toDateTime = new DateTime('-1 hour');
		//don't sync month, year
		return $this->rebuild_totals($fromDateTime->format('Y-m-d H:').'00:00',
			$toDateTime->format('Y-m-d H:').'59:59', 'true', 'true',
			null, 'false', 'false', 'false');

	}

	/**
	 * overview : rebuild only total hours and minutes
	 *
	 * @param int $hours
	 */
	public function rebuild_total_only_minute_hours_day($fromStr, $toStr) {

		$this->utils->debug_log('fromStr', $fromStr, 'toStr', $toStr);

		//don't sync month, year
		return $this->rebuild_totals($fromStr, $toStr, 'true', 'true', null, 'false', 'false', 'false');

	}

	public function rebuild_total_day_month_year_without_player($fromStr = '_null', $toStr = '_null', $token='_null') {
		return $this->rebuild_totals($fromStr, $toStr, 'false', 'false',
			null, 'true', 'true', 'false');
	}

	/**
	 * overview : rebuild totals
	 * @param date $fromStr
	 * @param date $toStr
	 * @param bool|false $rebuild_hour
	 */
	public function rebuild_totals($fromStr = null, $toStr = null, $rebuild_hour = 'false', $rebuild_minute='false',
		$token=null, $rebuild_month='true', $rebuild_year='true', $update_player='true') {

		$lock_rebuild_reports_range= $this->utils->getConfig('lock_rebuild_reports_range');
		$from_day = $this->utils->formatDateForMysql(new DateTime($fromStr));
		$to_day = $this->utils->formatDateForMysql(new DateTime($toStr));
		if(!empty($lock_rebuild_reports_range)){
			if($this->utils->isRebuildReportDateLocked($lock_rebuild_reports_range,$from_day,$to_day,$rlt)){
				$this->utils->error_log(sprintf(lang("Regenarate All Report has lock - should not be equal or older than  %s  "),$rlt['cutoff_day']));
				return;
			}
		}

		$rebuild_hour=$rebuild_hour=='true';
		$rebuild_minute=$rebuild_minute=='true';
		$rebuild_month=$rebuild_month=='true';
		$rebuild_year=$rebuild_year=='true';
		$update_player=$update_player=='true';
		if($token=='_null' || $token=='null'){
			$token=null;
		}

		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');

		set_time_limit($default_sync_game_logs_max_time_second);

		$this->utils->debug_log('fromStr', $fromStr, 'toStr', $toStr, 'rebuild_hour', $rebuild_hour, 'rebuild_minute', $rebuild_minute,
			'token', $token, 'rebuild_month', $rebuild_month, 'rebuild_year', $rebuild_year, 'update_player', $update_player);

		$this->load->model(array('game_logs', 'total_player_game_minute', 'total_player_game_hour', 'total_player_game_day',
			'total_player_game_month', 'total_player_game_year', 'player_model'));
		if (empty($fromStr) || empty($toStr) || $fromStr == "null" || $toStr == "null" || $fromStr == "_null" || $toStr == "_null") {
			// $from = new DateTime($this->game_logs->getFirstDatetime());
			//last 7 days
			$from = $this->utils->getNowDateTime();
			$from = $from->modify('-7 days');
			$to = $this->utils->getNowDateTime();
			//exclude current hour
			$to = $to->modify('-1 day');
			$to=new DateTime($to->format('Y-m-d').' 23:59:59');
		} else {
			$from = new DateTime($fromStr);
			$to = new DateTime($toStr);
		}
		$msg = $this->utils->debug_log('from', $from, 'to', $to);
		// $this->returnText($msg);

		$fixDays = array();
		$fixMonths = array();
		$fixYears = array();

		list($start, $end, $datehour) = $this->utils->getDateHourSet($from);
		while (!$this->utils->gtEndHour($from, $to)) {

			$fixDays[] = substr($datehour, 0, 8);
			$fixMonths[] = substr($datehour, 0, 6);
			$fixYears[] = substr($datehour, 0, 4);
			$fixResultMinute = false;
			$fixResultHour = false;



			if($rebuild_minute){
				//make half hour
				$halfHour=new DateTime($start);
				$halfHour->modify('+30 minutes');

				if($halfHour->format('Y-m-d H:i:s')<=$end){
					$this->total_player_game_minute->startTrans();
					//rebuild minute first
					$count = $this->total_player_game_minute->sync(new DateTime($start), $halfHour);
					$this->utils->debug_log('start', $start, 'halfHour', $halfHour, 'datehour', $datehour, 'fix total_player_game_minute', $count);
					$fixResultMinute = $this->total_player_game_minute->endTransWithSucc();

					$this->utils->debug_log('commit minute half hour', $fixResultMinute);

					$this->total_player_game_minute->startTrans();
					//rebuild minute first
					$count = $this->total_player_game_minute->sync($halfHour, new DateTime($end));
					$this->utils->debug_log('halfHour', $halfHour, 'end', $end, 'datehour', $datehour, 'fix total_player_game_minute', $count);
					$fixResultMinute = $this->total_player_game_minute->endTransWithSucc();

					$this->utils->debug_log('commit minute', $fixResultMinute);
				}else{
					$this->total_player_game_minute->startTrans();
					//rebuild minute first
					$count = $this->total_player_game_minute->sync(new DateTime($start), new DateTime($end));
					$this->utils->debug_log('start', $start, 'end', $end, 'datehour', $datehour, 'fix total_player_game_minute', $count);
					$fixResultMinute = $this->total_player_game_minute->endTransWithSucc();

					$this->utils->debug_log('commit minute', $fixResultMinute);
				}
			}


			if ($rebuild_hour) {
				$this->total_player_game_hour->startTrans();

				$count = $this->total_player_game_hour->sync(new DateTime($start), new DateTime($end));
				$msg = $this->utils->debug_log('start', $start, 'end', $end, 'datehour', $datehour, 'fix total_player_game_hour', $count);
				// $this->returnText($msg);

				$fixResultHour = $this->total_player_game_hour->endTransWithSucc();

				$msg = $this->utils->debug_log('commit hour', $fixResultHour);
				// $this->returnText($msg);
			}

			list($start, $end, $datehour) = $this->utils->getNextHour($from);
			$from = new DateTime($start);
		}
		$fixDays = array_unique($fixDays);
		$fixMonths = array_unique($fixMonths);
		$fixYears = array_unique($fixYears);

		$msg = $this->utils->debug_log('days', $fixDays, 'months', $fixMonths, 'years', $fixYears);
		// $this->returnText($msg);
		if(!$rebuild_month){
			$fixMonths=null;
		}
		if(!$rebuild_year){
			$fixYears=null;
		}
		$fixRlt = $this->fixDayMonthYear($fixDays, $fixMonths, $fixYears);
		$msg = $this->utils->debug_log('fixDayMonthYear result', $fixRlt);

		if(!empty($token)){
			$result = array('minute'=>$fixResultMinute,'hour'=>$fixResultHour, 'days'=> $fixDays, 'months'=> $fixMonths, 'years'=> $fixYears, 'fixDayMonthYearResult' => $fixRlt);
			$done=true;
			if ($fixRlt) {
				$this->queue_result->appendResult($token, ['request_id'=>_REQUEST_ID, 'result'=> $result ], $done, false);
				$this->utils->debug_Log("remote_rebuild_games_total  token:" . $token . " result: ", $result);

			} else {
				$this->queue_result->appendResult($token, ['request_id'=>_REQUEST_ID, 'result'=> $result ], $done, true);
				$this->utils->error_Log("remote_rebuild_games_total  token:" . $token . " result: ", $result);
			}
		}

		if($update_player){
			// -- OGP-9124
			$this->utils->debug_log('START RUNNING: updatePlayersTotalBettingAmount');
			$updatePlayersTotalBettingAmount = $this->player_model->updatePlayersTotalBettingAmount();

			$this->utils->debug_log('RESULT OF updatePlayersTotalBettingAmount: Total count of players updated = '.$updatePlayersTotalBettingAmount);

			$this->utils->debug_log('END RUNNING: updatePlayersTotalBettingAmount');
		}

		// $this->returnText($msg);
	}

	/**
	 * overview : Fix day month and year
	 * @param $fixDays
	 * @param $fixMonths
	 * @param $fixYears
	 * @return mixed
	 */
	private function fixDayMonthYear($fixDays, $fixMonths, $fixYears) {

		$this->load->model(array('total_player_game_hour', 'total_player_game_day',
			'total_player_game_month', 'total_player_game_year'));

		$this->total_player_game_hour->startTrans();
		if (!empty($fixDays)) {
			// $fixDays = array_unique($fixDays);
			foreach ($fixDays as $day) {
				list($start, $end) = $this->utils->convertDayToStartEnd($day);
				$this->utils->debug_log('fix day', $day, 'start', $start, 'end', $end);
				$this->total_player_game_day->sync(new DateTime($start), new DateTime($end));
			}
		}
		return $this->total_player_game_hour->endTransWithSucc();
		$this->total_player_game_hour->startTrans();
		if (!empty($fixMonths)) {
			// $fixMonths = array_unique($fixMonths);
			foreach ($fixMonths as $month) {
				list($start, $end) = $this->utils->convertMonthToStartEnd($month);
				$this->utils->debug_log('fix month', $month, 'start', $start, 'end', $end);
				$this->total_player_game_month->sync(new DateTime($start), new DateTime($end));
			}
		}
		return $this->total_player_game_hour->endTransWithSucc();
		$this->total_player_game_hour->startTrans();
		if (!empty($fixYears)) {
			// $fixYears = array_unique($fixYears);
			foreach ($fixYears as $year) {
				list($start, $end) = $this->utils->convertYearToStartEnd($year);
				$this->utils->debug_log('fix year', $year, 'start', $start, 'end', $end);
				$this->total_player_game_year->sync(new DateTime($start), new DateTime($end));
			}
		}

		return $this->total_player_game_hour->endTransWithSucc();
	}

	/**
	 * overview : sync total stats
	 *
	 * @param date $dateTimeFromStr
	 * @param date $dateTimeToStr
	 * @return mixed
	 */
	public function syncTotalStats($dateTimeFromStr = null, $dateTimeToStr = null) {
		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');

		set_time_limit($default_sync_game_logs_max_time_second);

		$dateTimeFrom = new \DateTime($dateTimeFromStr);
		$dateTimeTo = new \DateTime($dateTimeToStr);
		list($todayFrom, $todayTo) = $this->utils->getTodayDateTimeRange();
		//set default datetime from to
		if (empty($dateTimeFromStr)) {
			$dateTimeFrom = $todayFrom;
		}
		if (empty($dateTimeToStr)) {
			$dateTimeTo = $todayTo;
		}

		// $this->utils->debug_log('dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo,
		// 	'dateTimeFromStr', $dateTimeFromStr, 'dateTimeToStr', $dateTimeToStr);

		$abstractApi = $this->utils->loadAnyGameApiObject();

		$token = random_string('unique');
		$abstractApi->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo);

		$rlt = $abstractApi->syncTotalStats($token);

		$abstractApi->clearSyncInfo($token);

		return $rlt;
	}

	public function rebuild_player_daily_total_balance($date) {
		if (empty($date)) {
			return false;
		}

		$this->load->model('daily_balance');
		$this->daily_balance->generateDailyTotal($date, true);
	}


	public function batch_update_player_betting_amount(){
		$this->load->model(['player_model']);
		$this->utils->debug_log('START RUNNING: updatePlayersTotalBettingAmount');
		$updatePlayersTotalBettingAmount = $this->player_model->updatePlayersTotalBettingAmount();
		$this->utils->debug_log('RESULT OF updatePlayersTotalBettingAmount: Total count of players updated = '.$updatePlayersTotalBettingAmount);

		$this->utils->debug_log('END RUNNING: updatePlayersTotalBettingAmount');
	}

}

///END OF FILE/////////////////
