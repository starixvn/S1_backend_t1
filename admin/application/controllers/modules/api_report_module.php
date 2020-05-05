<?php
trait api_report_module {

	public function gameReports($player_id = null) {
		# Overwrite playerId with the logged-in player id when called from player domain
        if(!$this->isLoggedAdminUser()) {
			# Check if request if from player site
			$player_id = $this->authentication->getPlayerId();
        }

		$this->load->model(array('report_model'));
		$request = $this->input->post();
        $is_export = false;
  		$result = $this->report_model->gameReports($request, $player_id, $is_export);
		$this->returnJsonResult($result);
	}

	public function vrGameReports($player_id = null) {
		# Overwrite playerId with the logged-in player id when called from player domain
        if(!$this->isLoggedAdminUser()) {
			$player_id = $this->authentication->getPlayerId();
        }

		$this->load->model(array('report_model'));
		$request = $this->input->post();
        $is_export = false;
  		$result = $this->report_model->vrGameReports($request, $player_id, $is_export);
		$this->returnJsonResult($result);
	}

	public function oneworksGameReports($player_id = null) {
		# Overwrite playerId with the logged-in player id when called from player domain
        if(!$this->isLoggedAdminUser()) {
			$player_id = $this->authentication->getPlayerId();
        }

		$this->load->model(array('report_model'));
		$request = $this->input->post();
        $is_export = false;
  		$result = $this->report_model->oneworksGameReports($request, $player_id, $is_export);
		$this->returnJsonResult($result);
	}

	public function playerReports() {
		$this->load->library(array('permissions'));
		$this->permissions->setPermissions();

		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$permissions = $this->getContactPermissions();
		$is_export = false;
		$result = $this->report_model->player_reports($request, $permissions, $is_export);

		$this->returnJsonResult($result);
	}

    public function playerReports2() {
        $this->load->library(array('permissions'));
        $this->permissions->setPermissions();

        $this->load->model(array('report_model'));

        $request = $this->input->post();
        $permissions = $this->getContactPermissions();
        $is_export = false;
        $result = $this->report_model->player_reports_2($request, $permissions, $is_export);
        $this->returnJsonResult($result);
    }


    public function playerGradeReports() {
		$this->load->model(array('report_model'));
		$request = $this->input->post();
		$is_export = false;
		$result = $this->report_model->playerGradeReports($request, $is_export);
		$this->returnJsonResult($result);
	}

	public function communicationPreferenceReports() {
		$this->load->model(array('report_model'));
		$request = $this->input->post();
		$is_export = false;
		$result = $this->report_model->communicationPreferenceReports($request, $is_export);
		$this->returnJsonResult($result);
	}

	/**
	 * Return Income Access Signup Report
	 *
	 * @param void
	 * @return json
	 */
	public function incomeAccessSignupReports() {
		$this->load->model(array('report_model'));
		$request = $this->input->post();
		$is_export = false;

		$result = $this->report_model->incomeAccessSignupReports($request, $is_export);
		$this->returnJsonResult($result);
	}

	/**
	 * Return Income Access Sales Report
	 *
	 * @param void
	 * @return json
	 */
	public function incomeAccessSalesReports() {
		$this->load->model(array('report_model'));
		$request = $this->input->post();
		$is_export = false;

		$result = $this->report_model->incomeAccessSalesReports($request, $is_export);
		$this->returnJsonResult($result);
	}

	public function affiliateStatistics() {

		$i = 0;
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'username',
				'select' => 't1.username',
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'realname',
				'select' => 'CONCAT(ifnull(t1.firstName,""), \' \', ifnull(t1.lastName,"") )',
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'subaffiliates',
				'select' => 'COUNT(t2.username)',
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'players',
				'select' => 'COUNT(t3.username)',
				'formatter' => 'defaultFormatter',
			),
		);
		$table = 'affiliates t1';
		$joins = array(
			'affiliates t2' => 't2.parentId = t1.affiliateId',
			'player t3' => 't3.affiliateId = t1.affiliateId',
		);
		$where = array();
		$values = array();
		$group_by = array('t1.username');

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by);
		$this->returnJsonResult($result);
	}

	public function transactionsReport() {

		$i = 0;
		$request = $this->input->get();
		$table = 'report_logs';

		# DEFINE TABLE COLUMNS ########################################################################################################################################################################
		$columns = array(
			array(
				'dt' => $i++,
				'select' => 'created_at',
			),
			array(
				'dt' => $i++,
				'select' => 'filepath',
			),
		);

		# OUTPUT ######################################################################################################################################################################################
		$result = $this->data_tables->get_data($request, $columns, $table);
		$this->returnJsonResult($result);
	}

	public function promotion_report() {
		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$is_export = false;
		$result = $this->report_model->promotionReport($request, $is_export);

		$this->returnJsonResult($result);

	}

	public function cashback_report() {
		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$is_export = false;
		$result = $this->report_model->cashbackReport($request, $is_export);

		$this->returnJsonResult($result);

	}

	/**
	 * OGP-3381
	 * @return [type] [description]
	 */
	public function bonus_games_report() {
		$this->load->model(['report_model']);

		$request = $this->input->post();

		$is_export = false;
		$result = $this->report_model->bonus_games_report($request, $is_export);

		$this->returnJsonResult($result);
	}

	/**
	 * OBSOLETE
	 */
	public function duplicate_account_report() {
		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$is_export = false;

		$result = $this->report_model->duplicateAccountReport($request, $is_export);

		$this->returnJsonResult($result);

	}

	/**
	 * API datatable source, used by /report_management/duplicate_account_report
	 * New duplicate account report system
 	 * @param 	$username	string		== duplicate_account_info.username
 	 * @see		views/report_management/view_duplicate_accounts_report.php
 	 * @see 	Report_management::duplicate_account_report()
 	 *
	 * @return	array 		Standard data source format for Datatable.js
	 */
	public function duplicate_account_total() {
		$this->load->model(['report_model']);

		$request = $this->input->post();

		$result = $this->report_model->duplicateAccountTotal($request);

		$this->returnJsonResult($result);

	}

	/**
	 * API datatable source, used by /report_management/viewDuplicateAccountsDetailByUsername
	 * New duplicate account report system
 	 * @param 	$username	string		== duplicate_account_info.username
 	 * @see		views/report_management/view_duplicate_accounts_details
 	 * @see 	Report_management::duplicate_account_detail_by_username()
 	 *
	 * @return	array 		Standard data source format for Datatable.js
	 */
	public function duplicate_account_info($username) {
		$this->load->model(['report_model']);

		$request = $this->input->post();
		$is_request_for_modal = ($request && isset($request['is_request_for_modal'])) ? $request['is_request_for_modal'] : false;
		$is_count_related_total_rate = ($request && isset($request['is_count_related_total_rate'])) ? $request['is_count_related_total_rate'] : false;
		$result = $this->report_model->duplicateAccountInfo($username, false, $is_request_for_modal, $is_count_related_total_rate);

		$this->returnJsonResult($result);
	}

	/**
	 * API datatable source, used by /report_management/viewDuplicateAccountsDetailByUsername
	 * New duplicate account report system
 	 * @param 	$username	string		== duplicate_account_info.username
 	 * @see		views/report_management/view_duplicate_accounts_details
 	 * @see 	Report_management::duplicate_account_detail_by_username()
 	 *
	 * @return	array 		Standard data source format for Datatable.js
	 */
	public function duplicate_account_info_by_playerid($player_id, $for_player_info = false) {
		$this->load->model(['report_model', 'player_model']);

		$username = $this->player_model->getUsernameById($player_id);

		$request = $this->input->post();
		$is_request_for_modal = ($request && isset($request['is_request_for_modal'])) ? $request['is_request_for_modal'] : false;
		$is_count_related_total_rate = ($request && isset($request['is_count_related_total_rate'])) ? $request['is_count_related_total_rate'] : false;
		$result = $this->report_model->duplicateAccountInfo($username, $for_player_info, $is_request_for_modal, $is_count_related_total_rate);

		$this->returnJsonResult($result);
	}

	public function affiliate_statistics() {
		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$is_export = false;
		$result = $this->report_model->affiliateStatistics($request, $is_export);

		$this->returnJsonResult($result);

	}

	public function affiliate_statistics2() {
		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$is_export = false;
		$result = $this->report_model->affiliateStatistics2($request, $is_export);

		$this->returnJsonResult($result);

	}

	public function affiliate_statistics_for_aff() {
		$this->load->model(array('report_model'));

		$affId = $this->getSessionAffId();
		if(empty($affId)){
			return $this->returnJsonResult(null);
		}

		$request = $this->input->post();
		$is_export = false;
		$result = $this->report_model->affiliateStatisticsForAff($affId, $request, $is_export);

		$this->returnJsonResult($result);

	}

	public function affiliate_earnings() {
		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$is_export = false;
		$result = $this->report_model->affiliateEarnings($request, $is_export);

		$this->returnJsonResult($result);

	}

	public function payment_report() {
		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$is_export = false;
		$result = $this->report_model->payment_report($request, $is_export);

		$this->returnJsonResult($result);

	}

    public function payment_status_history_report() {
        $this->load->model(array('report_model'));

        $request = $this->input->post();
        $is_export = false;
        $result = $this->report_model->payment_status_history_report($request, $is_export);

        $this->returnJsonResult($result);

    }

	public function traffic_statistics_aff() {
		$this->load->model(array('report_model'));

		$affId = $this->getSessionAffId();
		if(empty($affId)){
			return $this->returnJsonResult(null);
		}

		$request = $this->input->post();
		$is_export = false;
		$result = $this->report_model->traffic_statistics_aff($affId, $request, $is_export);

		$this->returnJsonResult($result);

	}

	public function smsVerificationCodeReport() {
		$request = $this->input->post();
		$this->load->model(array('sms_verification'));
		$is_export = false;
		$result = $this->sms_verification->listVerificationCodes($request,$is_export);
		$this->returnJsonResult($result);
	}

	public function affiliate_player_reports() {

		$this->load->model(array('report_model'));

		$affId = $this->getSessionAffId();
		if(empty($affId)){
			return $this->returnJsonResult(null);
		}

		$request = $this->input->post();
		$viewPlayerInfoPerm = true;
		$is_export = false;

		$result = null;
        $result = $this->report_model->get_affiliate_player_report_hourly($affId, $request, $viewPlayerInfoPerm, $is_export);

		$this->returnJsonResult($result);
	}

	public function subaffiliate_reports() {

		$this->load->model(array('report_model'));

		$affId = $this->getSessionAffId();
		$request = $this->input->post();
		$viewPlayerInfoPerm = true;
		$is_export = false;

        if($this->utils->isEnabledFeature('display_sub_affiliate_earnings_report')){
            $result = $this->report_model->get_subaffiliate_earnings($affId, $request, $is_export);
        }else{
            $result = $this->report_model->get_subaffiliate_reports($affId, $request, $viewPlayerInfoPerm, $is_export);
        }

		$this->returnJsonResult($result);
	}

	public function affiliate_game_history($player_id = null) {

		$this->load->model(array('report_model'));

		$affId = $this->getSessionAffId();
		if(empty($affId)){
			return $this->returnJsonResult(null);
		}

		$request = $this->input->post();
		$request['player_id'] = $player_id;
		$viewPlayerInfoPerm = true;
		$is_export = false;

		$result = $this->report_model->get_affiliate_game_history($affId, $request, $viewPlayerInfoPerm, $is_export);

		$this->returnJsonResult($result);
	}

	public function affiliate_credit_transactions() {

		$this->load->model(array('report_model'));

		$affId = $this->getSessionAffId();
		if(empty($affId)){
			return $this->returnJsonResult(null);
		}

		$request = $this->input->post();
		$viewPlayerInfoPerm = true;
		$is_export = false;

		$result = $this->report_model->get_affiliate_credit_transactions($affId, $request, $viewPlayerInfoPerm, $is_export);

		$this->returnJsonResult($result);
	}

	public function exception_order_list(){

		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$is_export = false;

		$result = $this->report_model->exception_order_list($request, $is_export);

		$this->returnJsonResult($result);

	}

	public function aff_earnings() {
		return $this->utils->isEnabledFeature('switch_to_affiliate_daily_earnings')
			? $this->aff_daily_earnings()
			: $this->aff_monthly_earnings();
	}


	public function aff_earnings_3() {
		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$is_export = false;
		$result = $this->report_model->aff_earnings_3($request, $is_export);
		$this->returnJsonResult($result);
	}

	public function aff_user_earnings_3(){
        $this->load->model(array('report_model'));

        $request = $this->input->post();
        $is_export = false;
        $result = $this->report_model->aff_user_earnings_3($request, $is_export);
        $this->returnJsonResult($result);
    }

	public function aff_monthly_earnings() {
		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$is_export = false;
		$result = $this->report_model->aff_monthly_earnings($request, $is_export);

		$this->returnJsonResult($result);

	}

	public function aff_daily_earnings() {
		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$is_export = false;
		$result = $this->report_model->aff_daily_earnings($request, $is_export);

		$this->returnJsonResult($result);

	}

	/**
	 * add by spencer.kuo
	 */
	public function friend_referrial_monthly_earning() {
		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$is_export = false;
		$result = $this->report_model->friend_referrial_monthly_earnings($request, $is_export);

		$this->returnJsonResult($result);
	}

	public function friend_referral_daily_report() {
		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$request['extra_search'][] = array(
			'name' => 'player_id',
			'value' => $this->authentication->getPlayerId()
		);
		for ($i = 0; $i < count($request['extra_search']); $i++) {
			if ($request['extra_search'][$i]['name'] == 'from') {
				$from_date = new DateTime($request['extra_search'][$i]['value']);
				$request['extra_search'][$i]['value'] = $from_date->format('Ymd');
			}
			if ($request['extra_search'][$i]['name'] == 'to') {
				$to_date = new DateTime($request['extra_search'][$i]['value']);
				$request['extra_search'][$i]['value'] = $to_date->format('Ymd');
			}
		}
		$is_export = false;
		$this->utils->debug_log('request : ', $request);
		$result = $this->report_model->friend_referral_daily_report($request, $is_export);

		$this->returnJsonResult($result);
	}



	/** add by spencer.kuo */
	public function checkUserName() {

		if($this->utils->isFromHost('aff')) {
			$this->load->model(array('player','affiliatemodel', 'users'));
		}else{
			$this->load->model(array('player','agency_model', 'affiliatemodel', 'users'));
		}

		$input = $this->input->post();
		$result['username'] = true;
		$result['agent_name'] = true;
		$result['affiliate_username'] = true;
		$result['admin_username'] = true;
		if (!empty($input['username'])) {
			$rows = $this->player->checkUsernameLikeExist($input['username']);
			if (empty($rows))
				$result['username'] = false;
		}
		if(!$this->utils->isFromHost('aff')) {
			if (!empty($input['agent_name'])) {
				$rows = $this->agency_model->get_agent_by_name($input['agent_name']);
				if (empty($rows))
					$result['agent_name'] = false;
			}
		}
		if (!empty($input['affiliate_username'])) {
			$rows = $this->affiliatemodel->getAffiliateByUsername($input['affiliate_username']);
			if (empty($rows))
				$result['affiliate_username'] = false;
		}
		if (!empty($input['admin_username'])) {
			$rows = $this->users->selectUserExist($input['admin_username']);
			if (empty($rows))
				$result['admin_username'] = false;
		}
		$this->returnJsonResult($result);
	}

	public function player_promo_report(){
		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$player_id = $this->authentication->getPlayerId();

		if (empty($player_id)) {
			$this->returnJsonResult($this->data_tables->empty_data($request));
			return;
		}

		$is_export = false;
		$datatable_result = $this->report_model->playerPromoReport($player_id, $request, $is_export);

		$this->returnJsonResult($datatable_result);
	}

	public function task_list(){

		$this->load->model(array('report_model', 'users'));
		$this->load->library(['authentication']);

		$request = $this->input->post();

		$user_id=$this->authentication->getUserId();
		//only me
		$isAdminUser=$this->users->isAdminUser($user_id);

		$is_export = false;
		$datatable_result = $this->report_model->task_list($request, $user_id, $isAdminUser, $is_export);

		$this->returnJsonResult($datatable_result);

	}

	public function response_result_list(){
		$this->load->library(array('permissions', 'authentication'));
		$this->permissions->setPermissions();
		$this->load->model('report_model');
		$request = $this->input->post();

		if (!$this->permissions->checkPermissions('view_resp_result') || !$this->users->isT1User($this->authentication->getUsername())) {
			return show_error('No permission', 403);
		}

		if(isset($request['is_export']) && $request['is_export'] == true)
			$is_export = true;
		else
			$is_export = false;

		$datatable_result = $this->report_model->response_result_list($request, $is_export);
		$this->returnJsonResult($datatable_result);
	}

	public function sms_report_list(){
		$this->load->library(array('permissions'));
		$this->permissions->setPermissions();
		$this->load->model('report_model');
		$request = $this->input->post();

		if (!$this->permissions->checkPermissions('view_sms_report')) {
			return show_error('No permission', 403);
		}

		if(isset($request['is_export']) && $request['is_export'] == true)
			$is_export = true;
		else
			$is_export = false;

		$datatable_result = $this->report_model->sms_report_list($request, $is_export);
		$this->returnJsonResult($datatable_result);
	}

	public function smtp_report_list(){
		$this->load->library(array('permissions'));
		$this->permissions->setPermissions();
		$this->load->model('report_model');
		$request = $this->input->post();

		if (!$this->permissions->checkPermissions('view_smtp_api_report')) {
			return show_error('No permission', 403);
		}

		if(isset($request['is_export']) && $request['is_export'] == true)
			$is_export = true;
		else
			$is_export = false;

		$datatable_result = $this->report_model->smtp_report_list($request, $is_export);
		$this->returnJsonResult($datatable_result);
	}

	public function responsibleGamingReport(){
        $this->load->library(array('permissions'));
        $this->load->model(array('report_model'));
        $this->permissions->setPermissions();


        $request = $this->input->post();
        $viewPlayerInfoPerm = $this->permissions->checkPermissions('responsible_gaming_report');
        $is_export = false;
        $result = $this->report_model->responsibleGamingReport($request, $is_export);


        $this->returnJsonResult($result);


    }

    /**
	 * API datatable source, used by /report_management/player_analysis_report
	 * Player Analysis Report report system
 	 * @param 	$username	Array		== Username that need to run player analysis
 	 *
	 * @return	array 		Standard data source format for Datatable.js
	 */
	public function player_analysis_report() {
		$this->load->model(['report_model']);
		$request = $this->input->post();

		$permissions = $this->getContactPermissions();

		$result = $this->report_model->player_analysis_report($request,$permissions);

		$this->returnJsonResult($result);

	}

	public function super_player_report() {
		$this->load->library(array('permissions'));
		$this->permissions->setPermissions();

		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;

		$result = $this->report_model->super_player_report($request, $is_export);

		$this->returnJsonResult($result);
	}

	public function super_summary_report() {
		$this->load->library(array('permissions'));
		$this->permissions->setPermissions();

		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;
		$result = $this->report_model->super_summary_report($request, $is_export);

		$this->returnJsonResult($result);
	}

	public function super_game_report() {
		$this->load->library(array('permissions'));
		$this->permissions->setPermissions();

		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;
		$result = $this->report_model->super_game_report($request, $is_export);

		$this->returnJsonResult($result);
	}

	public function super_payment_report() {
		$this->load->library(array('permissions'));
		$this->permissions->setPermissions();

		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;
		$result = $this->report_model->super_payment_report($request, $is_export);

		$this->returnJsonResult($result);
	}

	public function super_promotion_report() {
		$this->load->library(array('permissions'));
		$this->permissions->setPermissions();

		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;
		$result = $this->report_model->super_promotion_report($request, $is_export);

		$this->returnJsonResult($result);
	}

	public function super_cashback_report() {
		$this->load->library(array('permissions'));
		$this->permissions->setPermissions();

		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;
		$result = $this->report_model->super_cashback_report($request, $is_export);

		$this->returnJsonResult($result);
	}


	public function report_summary2($dateFrom = null, $dateTo = null, $month_only='false' ,$is_export = false) {

		if($is_export){
			$filenames = $this->input->post('filename');
			$this->utils->recordAction(lang('export_data'), $this->router->fetch_method(), $filenames);
			return true;
		}

		$month_only=$month_only=='true';

		$this->load->model(['report_model']);
		//get from sql
		$data=$this->report_model->report_summary2($dateFrom, $dateTo, $month_only);

		$output['data']=[];

		if (empty($data)) {
			$output['data'] = [
				["total_bet" => 0, "total_win" => 0, "total_loss" => 0, "payout" => 0, "common_date" => date('Y'),
					"total_deposit" => "0", "total_withdraw" => "0", "total_bonus" => "0", "total_cashback" => "0",
					"total_transaction_fee" => "0", "bank_cash_amount" => "0", "total_players" => "0",
					"new_players" => "0", "first_deposit" => 0, "second_deposit" => 0, "slug" => date('Y')],
			];
		} else {
			$output['data']=[];
			foreach ($data as $row) {
				$output['data'][] = [
					"total_bet" => $row['total_bet'],
					"total_win" => $row['total_win'],
					"total_loss" => $row['total_loss'],
					"payout" => $row['total_payout'],
					"common_date" => $row['summary_date'],
					"total_deposit" => $row['total_deposit'],
					"total_withdraw" => $row['total_withdrawal'],
					"total_bonus" => $row['total_bonus'],
					"total_cashback" => $row['total_cashback'],
					"total_transaction_fee" => $row['total_fee'],
					"bank_cash_amount" => $row['total_bank_cash_amount'],
					"total_players" => $row['count_all_players'],
					"new_players" => $row['count_new_player'],
					"first_deposit" => $row['count_first_deposit'],
					"second_deposit" => $row['count_second_deposit'],
					"slug" => $row['summary_date']
				];
			}
		}

		$this->returnJsonResult($output);

	}

	public function sbobetGameReports($player_id = null) {
		# Overwrite playerId with the logged-in player id when called from player domain
		if(!$this->isLoggedAdminUser()) {
			$player_id = $this->authentication->getPlayerId();
		}

		$this->load->model(array('report_model'));
		$request = $this->input->post();
		$is_export = false;
		$result = $this->report_model->sbobetGameReports($request, $player_id, $is_export);
		$this->returnJsonResult($result);
	}

	public function dailyBalance() {
		$this->load->library(array('permissions'));
		$this->load->model(array('report_model'));
		$result = $this->report_model->player_daily_balance($this->input->post(), false);
		$this->returnJsonResult($result);
	}

	public function player_realtime_balance() {
		$this->load->library(array('permissions'));
		$this->load->model(array('report_model'));
		$result = $this->report_model->player_realtime_balance($this->input->post(), false);
		$this->returnJsonResult($result);
	}

	function showActivePlayers() {
        $this->load->library(array('permissions'));
        $this->load->model(array('report_model'));
        $result = $this->report_model->show_active_players($this->input->post(), false);
        $this->returnJsonResult($result);
	}

	public function getGameproviderReport($player_id = null) {
		# Overwrite playerId with the logged-in player id when called from player domain
        if(!$this->isLoggedAdminUser()) {
			$player_id = $this->authentication->getPlayerId();
        }

		$this->load->model(array('report_model'));
		$request = $this->input->post();
        $is_export = false;
  		$result = $this->report_model->getGameproviderReport($request, $player_id, $is_export);
		$this->returnJsonResult($result);
	}

}
////END OF FILE/////
