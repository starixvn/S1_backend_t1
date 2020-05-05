<?php

trait player_cashback_module {


	private function convertDateList($date_type, $fetch_cashback_on_admin = false){
		$dateList=[];

		$this->load->model(['group_level']);

		//search from cashback settings
		$cashback_settings=$this->group_level->getCashbackSettings();
		$first_time=$cashback_settings->fromHour.':00:00';
		$last_time=$cashback_settings->toHour.':59:59';

		$this->utils->debug_log('convertDateList realtime cashback', $date_type, $first_time, $last_time);

		//only allow one day, for 12 to 12
		if($this->utils->getConfig('only_allow_one_day_on_player_cashback_request')){

			// check if cashback process on admin
			if($fetch_cashback_on_admin == 'true') {
				$cashback_time = $date_type;
				$dateList[]=[
						'start'=>$cashback_time.' '.$first_time,
						'end'=>$cashback_time.' '.$last_time
				];

				$this->utils->debug_log('manually calculate cashback on admin', $dateList);
			} else {
				$now =(new \DateTime())->format('H');
				$first_time="12:00:00";
				$last_time = "11:59:59";

				// ignore if use in admin
				if($now>=12) {
					$dateList[]=[
							'start'=>$this->utils->getTodayForMysql().' '.$first_time,
							'end'=>$this->utils->getNowForMysql(),
					];

				}else{
					$dateList[]=[
							'start'=>$this->utils->getYesterdayForMysql().' '.$first_time,
							'end'=>$this->utils->getTodayForMysql().' '.$last_time,
					];
				}
				$this->utils->debug_log('XPJ ONLY convertDateList realtime cashback dateList', $dateList);
			}

			return $dateList;
		}

		if($date_type=='today'){
			// $startDatetime=$this->utils->getTodayForMysql().' '.Utils::FIRST_TIME;
			// $endDatetime=$this->utils->getNowForMysql();
			if($first_time=='00:00:00'){

				$dateList[]=[
					'start'=>$this->utils->getTodayForMysql().' '.$first_time,
					'end'=>$this->utils->getNowForMysql(),
				];

			}else{

				if(date('H:i:s')>=$last_time){
					//today
					$dateList[]=[
						'start'=>$this->utils->getYesterdayForMysql().' '.$first_time,
						'end'=>$this->utils->getTodayForMysql().' '.$last_time,
					];
					//tomorrow
					$dateList[]=[
						'start'=>$this->utils->getTodayForMysql().' '.$first_time,
						'end'=>$this->utils->getNowForMysql(),
					];

				}else{
					//today only
					$dateList[]=[
						'start'=>$this->utils->getYesterdayForMysql().' '.$first_time,
						'end'=>$this->utils->getNowForMysql(),
					];

				}

			}
		}else if($date_type=='yesterday'){
			// $startDatetime=$this->utils->getYesterdayForMysql().' '.Utils::FIRST_TIME;
			// $endDatetime=$this->utils->getYesterdayForMysql().' '.Utils::LAST_TIME;
			if($first_time=='00:00:00'){

				$dateList[]=[
					'start'=>$this->utils->getYesterdayForMysql().' '.$first_time,
					'end'=>$this->utils->getYesterdayForMysql().' '.$last_time,
				];

			}else{

				$dateList[]=[
					'start'=>$this->utils->getTheDayBeforeYesterdayForMysql().' '.$first_time,
					'end'=>$this->utils->getYesterdayForMysql().' '.$last_time,
				];

			}

		}else if($date_type=='both'){
			// $startDatetime=$this->utils->getYesterdayForMysql().' '.Utils::FIRST_TIME;
			// $endDatetime=$this->utils->getNowForMysql();
			if($first_time=='00:00:00'){
				$dateList[]=[
					'start'=>$this->utils->getYesterdayForMysql().' '.$first_time,
					'end'=>$this->utils->getYesterdayForMysql().' '.$last_time,
				];
				$dateList[]=[
					'start'=>$this->utils->getTodayForMysql().' '.$first_time,
					'end'=>$this->utils->getNowForMysql(),
				];
			}else{

				// $dateList[]=[
				// 	'start'=>$this->utils->getTheDayBeforeYesterdayForMysql().' '.$first_time,
				// 	'end'=>$this->utils->getNowForMysql(),
				// ];

				//yesterday
				$dateList[]=[
					'start'=>$this->utils->getTheDayBeforeYesterdayForMysql().' '.$first_time,
					'end'=>$this->utils->getYesterdayForMysql().' '.$last_time,
				];

				if(date('H:i:s')>=$last_time){

					//today
					$dateList[]=[
						'start'=>$this->utils->getYesterdayForMysql().' '.$first_time,
						'end'=>$this->utils->getTodayForMysql().' '.$last_time,
					];
					//tomorrow
					$dateList[]=[
						'start'=>$this->utils->getTodayForMysql().' '.$first_time,
						'end'=>$this->utils->getNowForMysql(),
					];

				}else{

					//today only
					$dateList[]=[
						'start'=>$this->utils->getYesterdayForMysql().' '.$first_time,
						'end'=>$this->utils->getNowForMysql(),
					];

				}

			}
		} else if($fetch_cashback_on_admin == 'true') {
			$cashback_time = $date_type;
			$dateList[]=[
				'start'=>$cashback_time.' '.$first_time,
				'end'=>$cashback_time.' '.$last_time
			];
		}

		$this->utils->debug_log('convertDateList realtime cashback dateList', $dateList);

		return $dateList;
	}

	/**
	 * 1. check if pending request count from player is zero
	 * 2. add cashbach request when there is no pending request from player
	 *
	 * @return bool
	 */
	public function cashbackRequest($player_id_input=null, $manually_calc_cashback_on_admin = null) {

		if(!$this->utils->isEnabledFeature('enabled_realtime_cashback')){

			$data['message']=lang('xpj.cashback.pending_msg');
			$data['msg']=lang('xpj.cashback.pending_msg');

			if($manually_calc_cashback_on_admin == 'true') {
				$this->load->view('player_management/view_cashback_error', $data);
			} else {
				// $this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_cashback_error', $data);
				$this->returnJsonResult($data);
			}
			return;
		}

		$cashbackSettings=$this->group_level->getCashbackSettings();
		$payTimeHour=$cashbackSettings->payTimeHour;

		$payDateTime=new DateTime($this->utils->getTodayForMysql().' '.$payTimeHour);
		$disable_start_datetime=$payDateTime->modify('-10 minutes');
		$payDateTime=new DateTime($this->utils->getTodayForMysql().' '.$payTimeHour);
		$disable_end_datetime=$payDateTime->modify('+20 minutes');

		$now=$this->utils->getNowForMysql();
		$disable_request= $now>=$disable_start_datetime && $now<=$disable_end_datetime;

		$this->load->model(['Group_level', 'cashback_request']);

		if ( !empty($player_id_input) && $this->isLoggedAdminUser() ) {
            $player_id = $player_id_input;
            $data["is_adminuser"] = true;
		}
        else{
			$player_id = $this->authentication->getPlayerId();
        }

		$date_type=$this->input->post('date_type');
		$startDatetime=$endDatetime=null;

		$dateList=$this->convertDateList($date_type, $manually_calc_cashback_on_admin);

		if(empty($dateList)){
			//wrong type
			$data['message']=lang('Wrong date type');
			$data['msg']=lang('Wrong date type');

			if($manually_calc_cashback_on_admin == 'true') {
				$this->load->view('player_management/view_cashback_error', $data);
			} else {
				// $this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_cashback_error', $data);
				$this->returnJsonResult($data);
			}
			return;
		}

		$realtime_cashback_perm=$this->cashback_request->checkPermissionForCashback($player_id);
		$can_user_cashback =!$disable_request && $realtime_cashback_perm;
		$data = [];

		$this->utils->debug_log('can_user_cashback', $can_user_cashback, 'disable_request', $disable_request, 'realtime_cashback_perm', $realtime_cashback_perm);

		if ($can_user_cashback) {
		  $lockedKey = null;
		  $locked=$this->lockResourceBy($player_id, Utils::LOCK_ACTION_CASHBACK_REQUEST, $lockedKey);
		  if($locked){

			try{

				$pending_request_count = $this->cashback_request->getPendingCashbackRequestCount($player_id);

				$this->utils->debug_log('pending_request_count : ' . $pending_request_count);

				if ($pending_request_count <= 0) {
					// $request_datetime = date('Y-m-d H:i:s');

					//$player_id, $start, $end, $cashback_game_platform = null, $create_request=false, $notes
					$create_request=true;
					$cashback_game_platform=null;
					$notes='request from player';


					foreach ($dateList as $dateItem) {

						$cashbackRequestData=null;
						$this->group_level->process_cashback_amount($player_id, $dateItem['start'], $dateItem['end'],
							$cashback_game_platform, $create_request, $notes, $cashbackRequestData);

						// if (!empty($cashbackRequestData)) {

						// $data['cashback_request'] =$cashbackRequestData;
						// }else{
						// $data['msg'] = lang('xpj.cashback.pending');

						// }
					}

					$data['msg'] = lang('xpj.cashback.pending');

				}else{
					$data['msg'] = lang('xpj.cashback.pending_cashback_not_finished');
				}
			}finally{
				if($lockedKey){
					$this->releaseResourceBy($player_id, Utils::LOCK_ACTION_CASHBACK_REQUEST, $lockedKey);
				}
			}

			if($manually_calc_cashback_on_admin == 'true') {
				$this->load->view('player_management/view_cashback_request', $data);
				} else {
					// $this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_cashback_request', $data);
					$this->returnJsonResult($data);
				}

			}else{
				$data['msg'] = lang('xpj.cashback.lock_failed');

				if($manually_calc_cashback_on_admin == 'true') {
				  $this->load->view('player_management/view_cashback_error', $data);
				} else {
				  // return $this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_cashback_error', $data);
				  $this->returnJsonResult($data);
				}

			}
		} else {
            $data['msg'] = lang('xpj.cashback.can_not_cashback');
			if($manually_calc_cashback_on_admin == 'true') {
				$this->load->view('player_management/view_cashback_error', $data);
			} else {
				// $this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_cashback_error', $data);
				$this->returnJsonResult($data);
			}
		}
	}

	public function get_cashback_stat($player_id_input=null, $fetch_cashback_on_admin = false){

		show_error('Not found', 404);

        $this->utils->debug_log('the player id ----->', $player_id_input, $this->isLoggedAdminUser() );
		if ( !empty($player_id_input) && $this->isLoggedAdminUser() ) {
            $playerId = $player_id_input;
            $data["is_adminuser"] = true;
		}
        else{
		$playerId=$this->authentication->getPlayerId();
        }

		if(empty($playerId)){
			show_error('No permission', 403);
		}

		$this->load->model(['game_logs', 'cashback_request', 'group_level', 'total_cashback_player_game']);

		//get search day
		$cashback_time=$this->input->post('cashback_time');
		$cashback_game_platform=$this->input->post('cashback_game_platform');


		//
		if(empty($cashback_game_platform)){
			$cashback_game_platform=null;
		}

		// if($cashback_time=='yesterday'){
		// 	$from=$this->utils->getYesterdayForMysql().' '. Utils::FIRST_TIME;
		// 	$to=$this->utils->getYesterdayForMysql().' '. Utils::LAST_TIME;
		// }else{
		// 	$from=$this->utils->getTodayForMysql().' '. Utils::FIRST_TIME;
		// 	$to=$this->utils->getTodayForMysql().' '. Utils::LAST_TIME;
		// }
		$date_type=$this->input->post('date_type');
        $this->utils->debug_log('the other data ----->', $date_type, $fetch_cashback_on_admin );

		$dateList=$this->convertDateList($date_type, $fetch_cashback_on_admin);


		// $startDatetime=$endDatetime=null;
		// if($date_type=='today'){
		// 	$startDatetime=$this->utils->getTodayForMysql().' '.Utils::FIRST_TIME;
		// 	$endDatetime=$this->utils->getNowForMysql();

		// }else if($date_type=='yesterday'){
		// 	$startDatetime=$this->utils->getYesterdayForMysql().' '.Utils::FIRST_TIME;
		// 	$endDatetime=$this->utils->getYesterdayForMysql().' '.Utils::LAST_TIME;

		// }else if($date_type=='both'){
		// 	$startDatetime=$this->utils->getYesterdayForMysql().' '.Utils::FIRST_TIME;
		// 	$endDatetime=$this->utils->getNowForMysql();
		// }else{

        $this->utils->debug_log('the $dateList ----->', $dateList);
		if(empty($dateList)){
            $result = []; // return empty data
            $this->returnJsonResult($result);
			return;
		}

//		list($total_bet, $totalWin, $totalLoss)=$this->game_logs->getPlayerTotalBetsWinsLossByDatetime($playerId, $from, $to, $cashback_game_platform);

		$total_available_cashback=0;
		$total_available_bet_amount=0;
		$total_bet=0;

		foreach ($dateList as $dateItem) {
			$startDatetime=$dateItem['start'];
			$endDatetime=$dateItem['end'];

			list($available_cashback, $available_bet_amount, $bet_amount) = $this->group_level->onlyGetCashbackAmount(
				$playerId, $startDatetime, $endDatetime, $cashback_game_platform);
			// list($totalBet, $totalWin, $totalLoss, $totalBet)=$this->total_player_game_hour->getPlayerTotalBetsWinsLossByDatetime(
			// $playerId, $startDatetime, $endDatetime, $cashback_game_platform);

			$total_available_cashback+=$available_cashback;
			$total_available_bet_amount+=$available_bet_amount;
			$total_bet+=$bet_amount;
		}

		// list($cashback_detail, $summary, $mapping) = $this->total_cashback_player_game->getUncashbackBetAmount($playerId, $startDatetime, $endDatetime, $cashback_game_platform);

		// $total_bet = $summary['game'];
		// $available_for_cashback_bet = 0;

		// if(empty($cashback_game_platform)){
		//     foreach ($cashback_detail as $platform => $available_for_cashback_bet_by_platform){
		//         $available_for_cashback_bet += $available_for_cashback_bet_by_platform;
		//     }
		// }else{
		//     foreach ($cashback_detail as $platform => $available_for_cashback_bet_by_platform){
		//         if($cashback_game_platform == $platform){
		//             $available_for_cashback_bet = $available_for_cashback_bet_by_platform;
		//         }
		//     }
		// }

		log_message('debug', 'the params ------>', ['cashback_time'=>$cashback_time,
			'cashback_game_platform'=>$cashback_game_platform, 'startDatetime'=>$startDatetime, 'endDatetime'=>$endDatetime]);

//		$available_for_cashback_bet=0;
//		$available_cashback=0;

		// $calculate_time='';
		// foreach ($dateList as $date) {
		// 	$calculate_time.='<p>'.lang('Start').': '.$date['start'].' '.lang('End').': '.$date['end'].'</p>';
		// }

		$calc_time = [];
		foreach ($dateList as $date) {
			$calc_time[] = [ 'start' => $date['start'] , 'end' => $date['end'] ];
		}

		$result=[
			'total_bet'=>$this->utils->formatCurrencyNoSym($total_bet),
			'available_for_cashback_bet'=>$this->utils->formatCurrencyNoSym($total_available_bet_amount),
			'available_cashback'=>$this->utils->formatCurrencyNoSym($total_available_cashback),
			// 'calculate_time'=>$calculate_time,
			'calculate_time' => $calc_time
			// 'summary'=>$summary,
			// 'cashback_detail'=>$cashback_detail,
		];

		$this->utils->debug_log('get_cashback_stat result ----->', $result);

		$this->returnJsonResult($result);
	}

	/**
	 * share for player and admin , be careful
	 *
	 * @param  string $player_id_input
	 * @param  string $manually_cal_cashback_on_admin
	 * @return view
	 */
	public function cashback( $player_id_input = null, $manually_cal_cashback_on_admin = 'false') {
		$this->load->model(['group_level', 'cashback_request', 'total_cashback_player_game']);

		$manually_cal_cashback_on_admin=$manually_cal_cashback_on_admin=='true';

		if ( !empty($player_id_input) && $this->isLoggedAdminUser() ) {
            $player_id = $player_id_input;
            $data["is_adminuser"] = true;
            $data["player_id_input"] = $player_id_input;
		}else{
			$player_id = $this->authentication->getPlayerId();
        }
		$data['amount'] = '';

		$data['can_user_cashback'] = $this->cashback_request->checkPermissionForCashback($player_id);

		$last_approved_cashback_request = $this->cashback_request->getLastApprovedCashbackRequest($player_id);

		if (empty($last_approved_cashback_request)) {
			$last_approved_cashback_request = new stdclass;
			$last_approved_cashback_request->request_datetime = '';
			$last_approved_cashback_request->request_amount = '';
		}

		$data['last_approved_cashback_request'] = $last_approved_cashback_request;
		$data['last_pending_cashback_request'] = $this->cashback_request->getLastPendingCashbackRequest($player_id);

		$cashbackSettings=$this->group_level->getCashbackSettings();
		$payTimeHour=$cashbackSettings->payTimeHour;

		$payDateTime=new DateTime($this->utils->getTodayForMysql().' '.$payTimeHour);
		$disable_start_datetime=$this->utils->formatDateTimeForMysql($payDateTime->modify('-10 minutes'));
		$payDateTime=new DateTime($this->utils->getTodayForMysql().' '.$payTimeHour);
		$disable_end_datetime=$this->utils->formatDateTimeForMysql($payDateTime->modify('+20 minutes'));

		$now=$this->utils->getNowForMysql();
		//debug
		// $now='2017-05-29 15:00:00';
		$data['disable_start_datetime']=$disable_start_datetime;
		$data['disable_end_datetime']=$disable_end_datetime;
		$data['disable_request']= $now>=$disable_start_datetime && $now<=$disable_end_datetime;
		$data['disable_hint']=lang('disable.cashback.request.hint').' '.$disable_start_datetime.' - '.$disable_end_datetime;

		$data['day_list']=[
			'today'=>lang('Today').' '.$this->utils->getTodayForMysql(),
			'yesterday'=>lang('Yesterday').' '.$this->utils->getYesterdayForMysql(),
			'both'=>lang('Today And Yesterday'),
		];
		$data['game_platforms']=$this->utils->getGameSystemMap();

		$last_pending_cashback_request = $this->cashback_request->getLastCashbackRequestByStatus($player_id, Cashback_request::PENDING);
		$data['cashback_request']=$last_pending_cashback_request;

		$data['time_start'] = $last_approved_cashback_request->request_datetime ? $last_approved_cashback_request->request_datetime : $this->utils->getYesterdayForMysql() . ' 00:00:00';
		$data['time_end'] = $now;

		$data['player'] = $this->player_model->getPlayerInfoDetailById($player_id);

		// list($data['bet_can_cashback'], $data['summary'], $mapping) = $this->total_cashback_player_game->getUncashbackBetAmount($player_id, $data['time_start'], $data['time_end']);

// 		$data['bet_can_cashback_detail'] = '<table>';

// 		if($data['bet_can_cashback'] && is_array($data['bet_can_cashback'])){
// 			foreach ($data['bet_can_cashback'] as $platform_id => $amount){
// //                $game_description_id = key($amount);
// //                $data['bet_can_cashback_detail'] .= "{$mapping[$game_description_id]} =>  {$amount[$game_description_id]}<br/>";

// 				$data['bet_can_cashback_detail'] .= "<tr><td>{$mapping[$platform_id]}</td><td>{$amount}</td></tr>";
// 			}
// 		}

// 		$data['bet_can_cashback_detail'] .= '</table>';
		// $data['request_amount'] = $this->group_level->onlyGetCashbackAmount($player_id, $data['time_start'], $data['time_end']);

		// $this->utils->debug_log($data);

		// OGP-4654 workaround: this variable is not found anywhere in xpj repo, and error from showing
		// it in view is suppressed by staging/live host error display settings.
		$data['summary_paid'] = null;

		$data['player_id'] = $player_id;

		if($manually_cal_cashback_on_admin){
			$this->load->view('player_management/player_cashback_request', $data);
		}else{
			$this->loadTemplate(lang('Cashback'), '', '', '');
			$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/view_cashback', $data);
			$this->template->render();
		}
	}

	public function viewCashbackReports() {
		$data['report_type'] = 'cashback_request';
		$data['transaction_type'] = 'cashback_request';
		$data['target_url'] = site_url('api/getCashbackRequestRecords');
		$data['report_title'] = lang('xpj.cashback');

		$player_id = $this->authentication->getPlayerId();
		$data['player'] = $this->player_model->getPlayerInfoDetailById($player_id);

		$data['game_platforms']  = $this->external_system->getSystemCodeMapping();
		$data['bounds'] = $this->utils->default_search_datetime_bounds();

		$this->loadTemplate(lang('Transactions'), '', '', '');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/reports_cashback', $data);
		$this->template->render();
	}

}
