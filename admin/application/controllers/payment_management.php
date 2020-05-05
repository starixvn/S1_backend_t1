<?php

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/modules/withdrawal_process_flow_module.php';
require_once dirname(__FILE__) . '/modules/transfer_request_module.php';

/**
 * Payment Management
 *
 *
 * General behaviors include
 * * add/edit new bank type
 * * able to activate/deactivate bank type status
 * * able to change the withdrawal status to deactivate/normal
 * * able to change the deposit status to deactivate/normal
 * * able to search data on the entire lists
 * * Lists all transfer request
 * * able to filter transfer request
 * * displays the count of (Transfer Request, Transfer success, Transfer Failed)
 * * Loads Template
 * * Displays Transactions
 * * View Adjustment transaction_list
 * * Exports data to Excel
 *
 * @category payment_management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 *
 */
class Payment_management extends BaseController {

	use withdrawal_process_flow_module;
	use transfer_request_module;

	function __construct() {
		parent::__construct();
		$this->load->helper(array('date_helper', 'url'));
		$this->load->library(array('permissions', 'form_validation', 'template', 'payment_manager', 'pagination', 'marketing_functions', 'form_validation', 'player_manager', 'user_functions', 'report_functions', 'salt', 'excel', 'bankaccount_manager', 'thirdpartyaccount_manager', 'og_utility', 'marketing_manager', 'promo_library'));
		$this->load->model(array('player_model', 'wallet_model', 'payment'));

		$this->permissions->checkSettings();
		$this->permissions->setPermissions(); //will set the permission for the logged in user
	}

	const ACTION_LOG_TITLE = 'Payment Management';

	const DEPOSIT_FIRST = 1;
	const DEPOSIT_SUCCEEDING = 2;

	const WITHDRAW_CONDITION_DEPOSIT_BONUS_BET_TIMES_DEDUCT_DEPOSIT='1';
	const WITHDRAW_CONDITION_ONLY_DEPOSIT='2';
	const WITHDRAW_CONDITION_NONE='3';

	const DEFAULT_DEPOSIT_COUNT_SETTING = 2;

	const DEPOSIT_THIS_WEEK = 1;
	const DEPOSIT_THIS_MONTH = 2;
	const DEPOSIT_THIS_YEAR = 3;
	const DEPOSIT_TOTAL_ALL = 4;

	const DECLINED_WITHDRAW_REQUEST = 101;
	const DECLINED_WITHDRAW_PAID = 102;
	const DECLINED_WITHDRAW_BATCH = 103;

	const FINANCIAL_ACCOUNT_SETTING_OTHERS_LIST = array(
		'bank_number_validator_mode',
		'financial_account_enable_deposit_bank',
		'financial_account_require_deposit_bank_account',
		'financial_account_deposit_account_limit',
		'financial_account_max_deposit_account_number',
		'financial_account_can_be_withdraw_and_deposit',
		'financial_account_deposit_account_default_unverified',
		'financial_account_withdraw_account_limit',
		'financial_account_max_withdraw_account_number',
		'financial_account_allow_edit',
		'financial_account_one_account_per_institution',
		'financial_account_allow_delete',
		'financial_account_withdraw_account_default_unverified',
	);

	/**
	 * Loads template for view based on regions in
	 * config > template.php
	 *
	 */
	private function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('userId', $this->authentication->getUserId());

		$this->template->add_js('resources/js/datatables.min.js');
        $this->template->add_js('resources/js/dataTables.responsive.min.js');
        $this->template->add_js('resources/js/dataTables.order.dom-checkbox.js');
		$this->template->add_css('resources/css/datatables.min.css');

		$this->template->add_js('resources/js/jquery.numeric.min.js');
		$this->template->add_js('resources/js/clipboard.min.js');
		$this->template->add_css('resources/css/general/style.css');
		$this->template->add_css('resources/css/jquery-checktree.css');

		$this->template->add_js('resources/js/payment_management/payment_management.js');
		$this->template->add_css('resources/css/payment_management/style.css');
	}

	/**
	 * detail: Shows Error message if user can't access the page
	 *
	 * @return	rendered Template
	 */
	private function error_access($from = 'payment') {
		$this->loadTemplate(lang('role.72'), '', '', 'payment');
		$paymentUrl = $this->utils->activePaymentSidebar();
		$reportUrl = $this->utils->activeReportSidebar();
		$systemUrl = $this->utils->activeSystemSidebar();
		if($from == 'payment'){
			$data['redirect'] = $paymentUrl;
		}

		elseif($from == 'system'){
			$data['redirect'] = $systemUrl;
		}

		else{
			$data['redirect'] = $reportUrl;
		}

		$message = lang('con.pym01');
		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		$this->template->write_view('main_content', 'error_page', $data);
		$this->template->render();
	}

	/**
	 *
	 * overview: Monthly earnings
	 *
	 * detail: Calculate the monthly earnings result
	 *
	 *
	 */

	public function calculate_monthly_earnings() {

		$this->load->model('affiliate_earnings');

		$payDay = $this->affiliate_earnings->getPayDay();

		if ($payDay > date('t')) {
			$payDay = date('t');
		}

		if ($payDay == date('t')) {
			$this->affiliate_earnings->monthlyEarnings();
		}
	}

	/**
	 *
	 * overview: Player Deposits
	 *
	 * detail: Update all player total deposit amount with correspoding transaction type and status
	 *
	 * note: always double check the query of getting the players before running through the updates
	 *
	 * @return String
	 *
	 */

	public function updatePlayersTotalDepositAmount() {
		$this->load->model('transactions');

		$this->db->select('player.playerId');
		$this->db->select_sum('transactions.amount', 'totalDepositAmount');
		$this->db->from('transactions');
		$this->db->join('player', 'player.playerId = transactions.to_id AND transactions.to_type = ' . Transactions::PLAYER);
		$this->db->where('transactions.transaction_type', Transactions::DEPOSIT);
		$this->db->where('transactions.status', Transactions::APPROVED);
		$this->db->group_by('player.playerId');
		$query = $this->db->get();
		$list = $query->result();

		$this->db->update_batch('player', $list, 'playerId');
		$result = $this->db->affected_rows();
		echo "$result player(s) has been updated";
	}

	/**
	 *
	 * overview: Process active status
	 *
	 * detail: To process active status records by batch
	 *
	 * note: simple run this function
	 *
	 * @return String
	 *
	 */

	public function batch_process_active_status() {
		$this->load->model(array('player_model'));
		$this->startTrans();
		$cnt = $this->player_model->batchProcessActiveStatus();
		$rlt = $this->endTransWithSucc();

		echo 'result: ' . $rlt . ' , cnt: ' . $cnt . "\n";
	}

	/**
	 *
	 * overview: Decoding password
	 *
	 * detail: Decode password using salt library by putting randomize password or which preffered
	 *
	 * note: simple run this method
	 *
	 * @return String
	 *
	 */

	public function decode_pass() {
		$this->load->library(array('salt'));
		$pass = 'OqzfaDnBIKVsOYi/ETnb6A==';
		$password = $this->salt->decrypt($pass, $this->getDeskeyOG());
		echo $password . "\n";
	}

	/**
	 *
	 * overview: Index Page of Payment Management
	 *
	 * @return	void
	 *
	 */
	public function index() {
		redirect('payment_management/deposit_list/request', 'refresh');
	}

	/**
	 * Returns deposit counts for deposit list headers
	 * Invoked by ajax in deposit list view file
	 * OGP-11751
	 * @see		views/payment_management/deposit_list.php
	 * @return	JSON
	 */
	public function deposit_list_header_counts() {
		$count_interval = self::DEFAULT_DEPOSIT_COUNT_SETTING;
		$hide_timeout = $this->input->get('excludeTimeout');
		$deposit_counts = $this->sale_order->countDepositRequests($count_interval, $hide_timeout);
		$this->returnJsonResult($deposit_counts);
	}

	/**
	 *
	 * overview: List Deposit Request
	 *
	 * detail: List all deposit request records
	 *
	 * note: List all deposit request records
	 *
	 * @param string $dwStatus	deposit status
	 *
	 */
	public function deposit_list($dwStatus = Sale_order::VIEW_STATUS_REQUEST, $paymentType = null) {
		if (!$this->permissions->checkPermissions('deposit_list')) {
			$this->error_access();
			return;
		}

		$this->load->model(array('payment_account','users'));

		$firstDayOfMon = $this->utils->getFirstDateOfCurrentMonth();
		$start_today = date("Y-m-d") . ' 00:00:00';
		$end_today = date("Y-m-d") . ' 23:59:59';

		$data['depositCountList']            = self::DEFAULT_DEPOSIT_COUNT_SETTING;
		$data['checking_deposit_locking']    = $this->config->item('checking_deposit_locking');
		$data['auto_checking_request']       = $this->config->item('auto_set_checking_to_request');
		$data['payment_account_list']        = $this->payment_account->getAllPaymentAccountDetails();
		$data['deposit_slip_image_path']     = site_url() . 'upload/deposit_slips/';
		$data['deposit_receipt_image_path']  = $this->utils->getPlayerInternalUrl('admin',PLAYER_INTERNAL_DEPOSIT_RECEIPT_PATH);
		$data['payment_account_flags']       = $this->utils->getPaymentAccountAllFlagsKV();
		$data['defaultSortColumn']           = $this->utils->getDefaultSortColumn('deposit_list');
		$data['aggregatedPaymentPercentage'] = json_encode($this->sale_order->getAggregatedPaymentPercentage());
		$data['users']                       = $this->users->getAllUsernames();
		$data['searchStatus']				 = array(
												Sale_order::STATUS_PROCESSING => lang('deposit_list.st.pending'),
												Sale_order::STATUS_SETTLED => lang('deposit_list.st.approved'),
												Sale_order::STATUS_DECLINED => lang('deposit_list.st.declined')
												);
		$data['conditions']                  = $this->safeLoadParams(array(
			'dwStatus'=>$dwStatus,
			'deposit_date_from' => $firstDayOfMon . ' 00:00:00',
			'deposit_date_to' => $end_today,
			'excludeTimeout' => '',
			'secure_id' => '',
			'username' => '',
			'realname' => '',
			'amount_from' => '',
			'amount_to' => '',
			'external_order_id' => '',
			'bank_order_id' => '',
			'affiliate' => '',
			'processed_by' => '',
			'timezone' => '',
			'payment_flag_1' => '1',
			'payment_flag_2' => '1',
			'payment_flag_3' => '1',
			'date_range' => '1',
			'search_time' => '1',
			'search_status' => $dwStatus,
			'searchBtn' => '0',
			'enable_date' => '1',
		));

		// $dtIndex = $this->input->get('datatable_index');
		// $dtDirection = $this->input->get('datatable_direction');
		// $data['conditions']['datatable_index'] = ($dtIndex) ? $dtIndex : $data['defaultSortColumn'];
		// $data['conditions']['datatable_direction'] = ($dtDirection) ? $dtDirection : 'desc';
		// $data['conditions']['enable_date'] = $this->safeGetParam('enable_date', true, true);
		$data['conditions']['select_all'] = $this->safeGetParam('select_all', true, true);

		if(is_array($data['payment_account_list'])){
			foreach ($data['payment_account_list'] as $payment_account) {
				$id=$payment_account->payment_account_id;
				$key='payment_account_id_'.$id;
				$val=$this->input->get($key);
				if($this->config->item('deposit_list_use_post')){
					$val=$this->input->post($key);
				}
				$data['conditions'][$key]=$data['conditions']['select_all'] ? 'true' : $val;
			}
		}

		$this->loadTemplate(lang('role.72'), '', '', 'payment');
		$this->template->add_css('resources/css/dashboard.css');
		$this->template->add_css('resources/css/ekko-lightbox.min.css');
		$this->template->add_js('resources/js/ekko-lightbox.min.js');
		$this->template->add_js('resources/js/highcharts.js');
		$this->template->write_view('sidebar', 'payment_management/sidebar');
		$this->template->write_view('main_content', 'payment_management/deposit_list', $data);
		$this->template->render();
	}

	/**
	 *
	 * overview: List Deposit Processing
	 *
	 * detail: List all deposit Processing records
	 *
	 * note: List all deposit Processing records
	 *
	 */

	public function getSaleOrderReport($dwStatus = Sale_order::STATUS_PROCESSING) {
		$this->load->model(array('common_category'));
		if (!$this->permissions->checkPermissions('deposit_list')) {
			$this->error_access();
			return;
		}else {

            $data['searchStatus'] = array(
				Sale_order::STATUS_PROCESSING => lang('deposit_list.st.pending'),
				Sale_order::STATUS_SETTLED => lang('deposit_list.st.approved'),
				Sale_order::STATUS_DECLINED => lang('deposit_list.st.declined')
				);

            $start_today = date("Y-m-d") . ' 00:00:00';
            $end_today = date("Y-m-d") . ' 23:59:59';

            $data['conditions'] = $this->safeLoadParams(array(
			'search_status'=>$dwStatus,
            'username' => '',
            'processed_by' => '' ,
            'deposit_date_from' => $start_today,
            'deposit_date_to' => $end_today,

        ));

            $this->utils->debug_log("=================getWithdrawReport conditions: ", $data['conditions']);


            $this->loadTemplate(lang('role.72'), '', '', 'payment');
            $this->template->write_view('sidebar', 'payment_management/sidebar');
            $this->template->write_view('main_content', 'payment_management/view_deposit_report_list', $data);
            $this->template->render();
        }
	}

	/**
	 *
	 * overview: List Withdraw Processing
	 *
	 * detail: List all withdraw Processing records
	 *
	 * note: List all withdraw Processing records
	 *
	 */
	public function getWithdrawReport($status = 'request') {
		$this->load->model(array('common_category'));
		if (!$this->permissions->checkPermissions(array('payment_withdrawal_list'))) {
			$this->error_access();
		}else {
			$data['setting'] = $this->operatorglobalsettings->getCustomWithdrawalProcessingStage();
			$customStageCount = 0;
			for ($i = 0; $i < count($data['setting']); $i++) {
				if (array_key_exists($i, $data['setting'])) {
					$customStageCount += ($data['setting'][$i]['enabled'] ? 1 : 0);
				}
			}


			if ($this->utils->isEnabledFeature('enable_withdrawal_declined_category')) {

				$data['withdrawalDeclinedCategory'] = $this->common_category->getActiveCategoryByType(common_category::CATEGORY_WITHRAWAL_DECLINED);
			}


			#get all search select status
			$allStatus = [];
			$allStatus['request']  = lang('st.pending');
			$allStatus['pending_review'] = lang('st.pending_review');

			if ($customStageCount>0) {
				for ($i = 0; $i < CUSTOM_WITHDRAWAL_PROCESSING_STAGES; $i++) {
					if ($data['setting'][$i]['enabled']) {
						$allStatus['CS'.$i] = lang($data['setting'][$i]['name']);
					}
				}
			}
			$allStatus['payProc']  = lang('st.processing');
			$allStatus['paid']     = lang('st.paid');
			$allStatus['declined'] = lang('st.declined');

			$data['searchStatus'] = $allStatus;
			$start_today = date("Y-m-d") . ' 00:00:00';
			$end_today = date("Y-m-d") . ' 23:59:59';

			$data['conditions'] = $this->safeLoadParams(array(
				'search_status' => $status,
				'username' => '',
				'processed_by' => '' ,
				'withdrawal_date_from' => $start_today,
				'withdrawal_date_to' => $end_today,

			));

			$this->utils->debug_log("=================getWithdrawReport conditions: ", $data['conditions']);

			$this->loadTemplate(lang('role.72'), '', '', 'payment');
			$this->template->write_view('sidebar', 'payment_management/sidebar');
			$this->template->write_view('main_content', 'payment_management/view_withdraw_report_list', $data);
			$this->template->render();
		}
	}


	/**
	 *
	 * overview: Deposit details
	 *
	 * detail: Getting the deposit details
	 *
	 * note: always add a the parameter $id, it will cause an error.
	 *
	 * @param int $id sale order id
	 * @return json
	 */
	public function get_deposit_detail($id, $return_json = true) {
		$rlt = false;
		if ($this->permissions->checkPermissions('deposit_list')) {
			$this->load->model(array('sale_order', 'player_promo', 'player_attached_proof_file_model', 'sale_orders_notes', 'vipsetting'));

			$this->utils->debug_log('load getSaleOrderDetailById start');
			$t = time();
			$saleOrderDetail = $this->sale_order->getSaleOrderDetailById($id);
			$this->utils->debug_log('load getSaleOrderDetailById end', time() - $t);

			if ($saleOrderDetail) {
				$saleOrderDetail->sub_wallet_name = '';
				if ($saleOrderDetail->sub_wallet_id) {
					$gameMap = $this->utils->getGameSystemMap();
					$saleOrderDetail->sub_wallet_name = $gameMap[$saleOrderDetail->sub_wallet_id];
				}
				$saleOrderDetail->amount = $this->utils->formatCurrencyNoSym($saleOrderDetail->amount);
				//translate
				if (!empty($saleOrderDetail->payment_type_name)) {
					$saleOrderDetail->payment_type_name = lang($saleOrderDetail->payment_type_name);
				}
				if (!empty($saleOrderDetail->player_payment_type_name)) {
					$saleOrderDetail->player_payment_type_name = lang($saleOrderDetail->player_payment_type_name);
				}
				$saleOrderDetail->playerActivePromo = $this->player_promo->getAvailPlayerPromoById($saleOrderDetail->player_promo_id, $saleOrderDetail->amount);
				$saleOrderDetail->promotion_account = $this->sale_order->getPromoRuleFromPaymentAccount($saleOrderDetail->payment_account_id);

				$player_level = $this->player_model->getPlayerCurrentLevel($saleOrderDetail->player_id);
				$saleOrderDetail->group_level_name = lang($player_level[0]['groupName']) . ' - ' . lang($player_level[0]['vipLevelName']);

				if(!empty($saleOrderDetail->group_level_id)){
					$request_level = $this->vipsetting->getVipGroupLevelDetails($saleOrderDetail->group_level_id);
					$saleOrderDetail->request_group_level_name = lang($request_level['groupName']) . ' - ' . lang($request_level['vipLevelName']);
				}

                if($this->utils->isEnabledFeature('enable_deposit_upload_documents')) {
				    if($this->utils->isEnabledFeature('only_showing_atm_deposit_upload_attachment_in_deposit_list') && ($saleOrderDetail->player_deposit_method != Payment_account::FLAG_MANUAL_LOCAL_BANK) ){
                        $deposit_receipt_info = null;
                    }else{
                        $deposit_receipt_info = $this->player_attached_proof_file_model->getAttachementRecordInfo($saleOrderDetail->player_id, null, player_attached_proof_file_model::Deposit_Attached_Document, null, false, $saleOrderDetail->id, true, false);
                    }

                    $saleOrderDetail->deposit_receipt_info = !empty($deposit_receipt_info) ? json_encode($deposit_receipt_info) : '';
                }

				# Translate payment method code to word
				switch ($saleOrderDetail->player_deposit_method) {
					case Payment_account::FLAG_MANUAL_LOCAL_BANK:
						$saleOrderDetail->player_deposit_method = lang('pay.local_bank_offline');
						break;
					case Payment_account::FLAG_MANUAL_ONLINE_PAYMENT:
						$saleOrderDetail->player_deposit_method = lang('pay.manual_online_payment');
						break;
					case Payment_account::FLAG_AUTO_ONLINE_PAYMENT:
						$saleOrderDetail->player_deposit_method = lang('pay.auto_online_payment');
						break;
					default:
						$saleOrderDetail->player_deposit_method = lang('pay.auto_online_payment');
						break;
				}

				$saleOrderDetail->sale_order_player_notes   = $this->sale_orders_notes->getBySaleOrdersNotes(Sale_orders_notes::PLAYER_NOTES, $saleOrderDetail->id);
				$saleOrderDetail->sale_order_internal_notes = $this->sale_orders_notes->getBySaleOrdersNotes(Sale_orders_notes::INTERNAL_NOTE, $saleOrderDetail->id);
				$saleOrderDetail->sale_order_external_notes = $this->sale_orders_notes->getBySaleOrdersNotes(Sale_orders_notes::EXTERNAL_NOTE, $saleOrderDetail->id);

				//for feature enable_manual_deposit_input_depositor_name
				if($this->utils->isEnabledFeature('enable_manual_deposit_input_depositor_name')){
					//if player_payment_account_name is not empty
					if(!empty($saleOrderDetail->player_payment_account_name)){
						$saleOrderDetail->realname = $saleOrderDetail->player_payment_account_name;
					}
				}
				$rlt = $saleOrderDetail;
				if(!$return_json) {
				    return $rlt;
                }
			}
		}
		$this->returnJsonResult($rlt);
	}

	/**
	 *
	 * overview: Deposite Columns
	 *
	 * detail: Manage the columns of the deposit lists
	 *
	 * @return void
	 *
	 */
	public function postChangeColumnsDeposit() {
		$depslip = $this->input->post('depslip') ? "checked" : "unchecked";
		$userName = $this->input->post('userName') ? "checked" : "unchecked";
		$realName = $this->input->post('realName') ? "checked" : "unchecked";
		$playerlev = $this->input->post('playerlev') ? "checked" : "unchecked";
		$amt = $this->input->post('amt') ? "checked" : "unchecked";
		$depstatus = $this->input->post('depstatus') ? "checked" : "unchecked";
		$createdon = $this->input->post('createdon') ? "checked" : "unchecked";
		$collection_name = $this->input->post('collection_name') ? "checked" : "unchecked";
		$collection_account_name = $this->input->post('collection_account_name') ? "checked" : "unchecked";
		$deposit_payment_name = $this->input->post('deposit_payment_name') ? "checked" : "unchecked";
		$deposit_payment_account_name = $this->input->post('deposit_payment_account_name') ? "checked" : "unchecked";
		$deposit_payment_account_number = $this->input->post('deposit_payment_account_number') ? "checked" : "unchecked";
		$deposit_transaction_code = $this->input->post('deposit_transaction_code') ? "checked" : "unchecked";
		$promoname = $this->input->post('promoname') ? "checked" : "unchecked";
		$promobonus = $this->input->post('promobonus') ? "checked" : "unchecked";
		$ip = $this->input->post('ip') ? "checked" : "unchecked";
		$updatedon = $this->input->post('updatedon') ? "checked" : "unchecked";
		$timeoutAt = $this->input->post('timeoutAt') ? "checked" : "unchecked";

		$depositCustomColumn = array(
			'depslip' => $depslip,
			'userName' => $userName,
			'realName' => $realName,
			'playerlev' => $playerlev,
			'amt' => $amt,
			'depstatus' => $depstatus,
			'createdon' => $createdon,
			'collection_name' => $collection_name,
			'collection_account_name' => $collection_account_name,
			'deposit_payment_name' => $deposit_payment_name,
			'deposit_payment_account_name' => $deposit_payment_account_name,
			'deposit_payment_account_number' => $deposit_payment_account_number,
			'deposit_transaction_code' => $deposit_transaction_code,
			'deposit_payment_account_number' => $deposit_payment_account_number,
			'promoname' => $promoname,
			'promobonus' => $promobonus,
			'ip' => $ip,
			'updatedon' => $updatedon,
			'timeoutAt' => $timeoutAt,
		);
		$this->session->set_userdata($depositCustomColumn);
		redirect('payment_management/deposit_list/request');
	}

	/**
	 *
	 * overview: Set Deposit as declined
	 *
	 * detail: set the status of deposit request as declined
	 *
	 * note: $saleOrderId is required when calling this function
	 *
	 * @param int $saleOrderId sale order id
	 * @return json
	 *
	 */
	public function set_deposit_declined($saleOrderId) {
		$success = false;
		if ($this->permissions->checkPermissions(array('deposit_list', 'approve_decline_deposit'))) {
			$this->load->model(array('sale_order', 'transactions', 'sale_orders_notes'));
			$ord = $this->sale_order->getSaleOrderById($saleOrderId);
            $loggedAdminUserId = method_exists($this->authentication, 'getUserId') ? $this->authentication->getUserId() : Users::SUPER_ADMIN_ID;

			$lockedKey=null;
			$lock_it = $this->lockPlayerBalanceResource($ord->player_id, $lockedKey);

			if (!$lock_it) {
				$this->returnJsonResult(array('success' => $success));
				return;
			}
			//lock success
			try {
				$actionlogNotes = $this->input->post("actionlogNotes");
				$this->startTrans();

				$this->sale_order->declineSaleOrder($saleOrderId, $actionlogNotes, null);
                $saleOrder = $this->sale_order->getSaleOrderById($saleOrderId);

                $this->transactions->createDeclinedDepositTransaction($saleOrder, $loggedAdminUserId, Transactions::MANUAL);
				$this->saveAction(self::ACTION_LOG_TITLE, 'Declined Deposit/Withdrawal Request', "User " . $this->authentication->getUsername() . " has declined deposit/withdrawal request.");

				$success = $this->endTransWithSucc();
			} finally {
				// release it
				$rlt = $this->releasePlayerBalanceResource($ord->player_id, $lockedKey);
			}
			if($success) {
				$this->sale_order->userUnlockDeposit($saleOrderId);
			}

			if($success){
				$this->triggerDepositEvent($ord->player_id, $saleOrderId, null, null, $ord->payment_account_id, $loggedAdminUserId);
			}

            $this->load->library(['player_notification_library']);
            $this->player_notification_library->danger($ord->player_id, Player_notification::SOURCE_TYPE_DEPOSIT, [
                'player_notify_danger_deposit_title',
                $ord->secure_id,
                $ord->created_at,
                $this->utils->displayCurrency($ord->amount),
                $this->utils->getLiveChatLink(),
                $this->utils->getLiveChatOnClick()
            ], [
                'player_notify_danger_deposit_message',
                $ord->secure_id,
                $ord->created_at,
                $this->utils->displayCurrency($ord->amount),
                $this->utils->getLiveChatLink(),
                $this->utils->getLiveChatOnClick()
            ]);
		}

		$this->returnJsonResult(array('success' => $success));
	}

	/**
 	 *
 	 * overview: approve sale order
 	 *
 	 * detail: update status of sale order, add balance to player, add point to player
 	 *
 	 * note: lock balance by player id, use same DB transaction to make sure data consistency
 	 *
 	 * @param int $saleOrderId            sale order id
 	 * @param string $enabledCompensationVal @deprecated
 	 * @param string $compensationFeeVal     @deprecated
 	 *
 	 */
	public function set_deposit_approved($saleOrderId, $enabledCompensationVal = '', $compensationFeeVal = '') {
		$success = false;
		$error_message= null;
		$promo_result= null;
		if ($this->permissions->checkPermissions(array('deposit_list', 'approve_decline_deposit'))) {

			$this->load->model(array('sale_order', 'promorules'));
			$ord = $this->sale_order->getSaleOrderById($saleOrderId);

            $approvedSubWallet = $this->input->post('approvedSubWallet') == 'true';
            $approve_promotion = $this->input->post('approve_promotion') == 'true';
            $approve_player_group_level_request = $this->input->post('approve_player_group_level_request') === 'true';
			$saleOrder=null;
			$player=null;
			$extra_info=null;
			$adminUserId = $this->authentication->getUserId();
			$lockedKey=null;
			$lock_it = $this->lockPlayerBalanceResource($ord->player_id, $lockedKey);

			if (!$lock_it) {
				$this->returnJsonResult(array('success' => $success));
				return;
			}
			//lock success
			try {
				$this->load->model(array('sale_order', 'withdraw_condition', 'point_transactions', 'player_model', 'group_level', 'sale_orders_notes'));
				$actionlogNotes = $this->input->post("actionlogNotes");
				$this->startTrans();

				$saleOrder = $this->sale_order->getSaleOrderWithPlayerById($saleOrderId);
				$player = $saleOrder->player;

				// clear withdraw condition
				// $this->withdraw_condition->checkAndCleanWithdrawCondition($saleOrder->player_id);
                $extra_info['approve_SubWallet'] = $approvedSubWallet;
                $extra_info['approve_promotion'] = $approve_promotion;
				$extra_info['is_payment_account_promo'] = FALSE;
				$success=$this->sale_order->approveSaleOrder($saleOrderId, $actionlogNotes, null, $extra_info);

				if(!$success && isset($extra_info['error_message'])){
                    $error_message = lang($extra_info['error_message']);
				}

				if($success && isset($extra_info['apply_promo_success'])){
					$promo_result=['apply_promo_success'=>$extra_info['apply_promo_success'],
						'apply_promo_message'=>lang('Deposit Successfully').' '.@$extra_info['apply_promo_message']];
				}

				//get deposit convert rate of this player
				$depositConvertRate = $this->group_level->getVipGroupLevelDetails($player->levelId)['deposit_convert_rate'];

				//point based on convert rate
				$point = $saleOrder->amount * $depositConvertRate / 100;

				//player current point
				$beforePointBalance = $player->point;
				$newPointBalance = $beforePointBalance + $point;
				$this->point_transactions->createPointTransaction($adminUserId, $player->playerId, $point, $beforePointBalance, $newPointBalance, $saleOrder->id, $saleOrder->player_promo_id, Point_transactions::DEPOSIT_POINT);

				//update player point balance
				$this->player_model->updatePlayerPointBalance($player->playerId, $newPointBalance);

				// change player group level
				if ($approve_player_group_level_request && isset($saleOrder->group_level_id) && $saleOrder->group_level_id > 0) {
					$this->load->model('group_level');
					$this->group_level->adjustPlayerLevel($player->playerId, $saleOrder->group_level_id);
				}

				$this->saveAction(self::ACTION_LOG_TITLE, 'Approve Deposit Request', "User " . $this->authentication->getUsername() . " has successfully approve deposit request of " . $player->username . ".");
				if (!$success) {
					//rollback
					$this->rollbackTrans();
					$this->utils->debug_log('rollback transaction , because transfer failed', $saleOrder);
				} else {
					$success = $this->endTransWithSucc();
				}

			} finally {
				// release it
				$rlt = $this->releasePlayerBalanceResource($ord->player_id, $lockedKey);
			}

			if($success){
				$this->triggerDepositEvent($ord->player_id, $ord->id, null, null, $ord->payment_account_id, $adminUserId);
			}

            $this->load->library(['player_notification_library']);
            if($success){
                $this->player_notification_library->success($ord->player_id, Player_notification::SOURCE_TYPE_DEPOSIT, [
                    'player_notify_success_deposit_title',
                    $ord->secure_id,
                    $ord->created_at,
                    $this->utils->displayCurrency($ord->amount),
                    $this->utils->getPlayerHistoryUrl('deposit')
                ], [
                    'player_notify_success_deposit_message',
                    $ord->secure_id,
                    $ord->created_at,
                    $this->utils->displayCurrency($ord->amount),
                    $this->utils->getPlayerHistoryUrl('deposit')
                ]);
            }else{
                $this->player_notification_library->danger($ord->player_id, Player_notification::SOURCE_TYPE_DEPOSIT, [
                    'player_notify_danger_deposit_title',
                    $ord->secure_id,
                    $ord->created_at,
                    $this->utils->displayCurrency($ord->amount),
                    $this->utils->getLiveChatLink(),
                    $this->utils->getLiveChatOnClick()
                ], [
                    'player_notify_danger_deposit_message',
                    $ord->secure_id,
                    $ord->created_at,
                    $this->utils->displayCurrency($ord->amount),
                    $this->utils->getLiveChatLink(),
                    $this->utils->getLiveChatOnClick()
                ]);
            }

            if($success && $extra_info){
                $bonusResult = $this->promorules->releaseToAfterApplyPromo($extra_info);
            }

            if($success){
                $this->sale_order->userUnlockDeposit($saleOrderId);
            }

            if($success){
                $result_approveSubWallet = $this->sale_order->approveSaleOrderSubWalletWithLock($saleOrderId, $extra_info);

                $success = $result_approveSubWallet;
                $error_message = (!empty($extra_info['error_message'])) ? lang($extra_info['error_message']) : NULL;
            }

            if($success){
                $result_approveSaleOrderPromotion = $this->sale_order->approveSaleOrderPlayerPromotionWithLock($saleOrderId, $extra_info);

                $success = $result_approveSaleOrderPromotion;
                $error_message = (!empty($extra_info['apply_promo_message'])) ? lang($extra_info['apply_promo_message']) : NULL;
            }
		}

		$this->returnJsonResult(array('success' => $success, 'error_message'=>$error_message,
			'promo_result'=>$promo_result));
	}

    /**
	 *
	 * overview: check the disbled status of payment account
	 *
	 * @param int $secureId
	 * @return boolean
	 *
	 */

	public function check_paymentAccount_status_disabled($secureId) {
		$this->load->model(array('payment_account'));
		$result['success'] = $this->payment_account->checkPaymentAccountStatusDisabled($secureId);

		return $this->returnJsonResult($result);
	}

    /**
	 *
	 * overview: Withdrawal checking
	 *
	 * detail: it will update all withdrawal to checking
	 *
	 * note: $saleOrderId and $playerId are mandatory, need this once you call this method
	 *
	 * @param int $saleOrderId sale order id
	 * @param int $playerId player id
	 * @return json
	 *
	 */

	public function set_withdrawal_checking($saleOrderId, $playerId) {
		$success = false;
		$this->load->model(array('payment', 'player'));
		$data = array('is_checking' => 'true',
			'processed_checking_time' => $this->utils->getNowForMysql(),
			'processedBy' => $this->authentication->getUserId());
		$playerDetail = $this->player->getPlayerById($playerId);
		if ($this->payment->updateWithdrawalToChecking($saleOrderId, $data)) {
			$this->saveAction(self::ACTION_LOG_TITLE, 'Checking Withdrawal Request', "User " . $this->authentication->getUsername() . "  is checking deposit request of " . $playerDetail['username'] . ".");
			$success = true;
		}
		$this->returnJsonResult(array('success' => $success));
	}

	/**
	 *
	 * overview: Withdrawal request list
	 *
	 * detail: Display the lists of the all withdrawal records with respective status
	 *
	 * @param string $status requet list status
	 * @return load template
	 *
	 */
	public function viewWithdrawalRequestList($status = 'request') {
        $this->load->model('external_system');
		$this->load->library('duplicate_account');
		$this->load->model(array('withdraw_condition','users','common_category'));

		if ($this->permissions->checkPermissions(array('payment_withdrawal_list'))) {
			$firstDayOfMon = $this->utils->getFirstDateOfCurrentMonth();
			$today_date = date("Y-m-d");

			$getRequestCountMon = $this->payment_manager->getDWCount('withdrawal', Wallet_model::REQUEST_STATUS, $firstDayOfMon, $today_date);
			$getRequestCountToday = $this->payment_manager->getDWCount('withdrawal', Wallet_model::REQUEST_STATUS, $today_date, $today_date);
			$getPendingReviewCountMon = $this->payment_manager->getDWCount('withdrawal', Wallet_model::PENDING_REVIEW_STATUS, $firstDayOfMon, $today_date);
			$getPendingReviewCountToday = $this->payment_manager->getDWCount('withdrawal', Wallet_model::PENDING_REVIEW_STATUS, $today_date, $today_date);
			$statusCountResult = $this->payment_manager->getDWCountAllStatus('withdrawal', $firstDayOfMon , $today_date);
			$statusCountTodayResult = $this->payment_manager->getDWCountAllStatus('withdrawal', $today_date, $today_date);

			$data['statusCount'] = array();
			foreach ($statusCountResult as $row) {
				$data['statusCount'][$row['dwStatus']] = intval($row['count']);
			}
			$data['statusCountToday'] = array();
			foreach ($statusCountTodayResult as $row) {
				$data['statusCountToday'][$row['dwStatus']] = intval($row['count']);
			}
			$data['statusCount'][Wallet_model::REQUEST_STATUS] = $getRequestCountMon;
			$data['statusCountToday'][Wallet_model::REQUEST_STATUS] = $getRequestCountToday;
			$data['statusCount'][Wallet_model::PENDING_REVIEW_STATUS] = $getPendingReviewCountMon;
			$data['statusCountToday'][Wallet_model::PENDING_REVIEW_STATUS] = $getPendingReviewCountToday;
			$this->utils->debug_log('Payment_management::viewWithdrawalRequestList', ['all' => $data['statusCount'], 'today' => $data['statusCountToday'] ]);

			$data['game_platforms'] = $this->external_system->getAllActiveSytemGameApi();
			$data['checking_withdrawal_locking'] = $this->config->item('checking_withdrawal_locking');
			$data['withdrawAPIs'] = $this->external_system->getWithdrawPaymentSystemsKV();
			$data['setting'] = $this->operatorglobalsettings->getCustomWithdrawalProcessingStage();
			$customStageCount = 0;
			for ($i = 0; $i < count($data['setting']); $i++) {
				if (array_key_exists($i, $data['setting'])) {
					$customStageCount += ($data['setting'][$i]['enabled'] ? 1 : 0);
				}
			}
			$data['customStageCount'] = $customStageCount;
			$data['canManagePaymentStatus'] = true;

			$start_today = date("Y-m-d") . ' 00:00:00';
			$end_today = date("Y-m-d") . ' 23:59:59';
			$data['users'] = $this->users->getAllUsernames();
			if ($this->utils->isEnabledFeature('enable_withdrawal_declined_category') ){
				$data['withdrawalDeclinedCategory'] = $this->common_category->getActiveCategoryByType(common_category::CATEGORY_WITHRAWAL_DECLINED);
			}

			#get all search select status
			$allStatus = [];
			$allStatus['request']  = lang('st.pending');
			if($this->utils->isEnabledFeature("enable_withdrawal_pending_review") && $this->permissions->checkPermissions('withdraw_pending_review_list')){
				$allStatus['pending_review'] = lang('st.pendingreview');
			}
			if($customStageCount>0){
				for($i = 0; $i < CUSTOM_WITHDRAWAL_PROCESSING_STAGES; $i++) {
					if($data['setting'][$i]['enabled']) {
						$allStatus['CS'.$i] = lang($data['setting'][$i]['name']);
					}
				}
			}
			$allStatus['payProc']  = lang('st.processing');
			$allStatus['paid']     = lang('st.paid');
			$allStatus['declined'] = lang('st.declined');
			$allStatus['lock_api_unknown'] = lang('st.lockedapirequest');

			$data['searchStatus'] = $allStatus;
			# set initial withdraw api filters value
			$withdrawAPIsIds = array_keys($data['withdrawAPIs']);
			array_push($withdrawAPIsIds, 0);

			$data['conditions'] = $this->safeLoadParams(array(
				'dwStatus'=> $status,
				'search_status' => $status,
				'withdrawal_date_from' => $firstDayOfMon . ' 00:00:00',
				'withdrawal_date_to' => $end_today,
				'withdraw_code' => '',
				'withdrawAPI' => $withdrawAPIsIds,
				'amount_from'=> '',
				'amount_to'=> '',
				'username' => '',
				'realname' => '',
				'processed_by' => '' ,
				'enable_date' => '1',
				'date_range' => '1',
				'search_time' => '1',
				'searchBtn' => '0'
				));

			$this->utils->debug_log("=================viewWithdrawalRequestList conditions: ", $data['conditions']);

			$this->loadTemplate(lang('role.72'), '', '', 'payment');
			$this->template->add_css('resources/css/dashboard.css');
			$this->template->write_view('sidebar', 'payment_management/sidebar');
			$this->template->write_view('main_content', 'payment_management/withdrawal_list', $data);
			$this->template->render();
		} else {
			$this->error_access();
		}
	}

	/**
	 *
	 * overview: Get Deposit Request Details
	 *
	 * @param int $walletAccountId wallet account id
	 * @param int $paymentMethodId @deprecated
	 * @return	json
	 *
	 */
	public function reviewDepositRequest($walletAccountId, $paymentMethodId) {
		echo json_encode($this->payment_manager->getDepositWithdrawalTransactionDetail($walletAccountId, $paymentMethodId));
	}

	/**
	 * overview: Get Deposit Request Details
	 *
	 * @param int $walletAccountId wallet account id
	 * @param int $paymentMethodId @deprecated
	 * @return	json
	 *
	 */
	public function reviewManualThirdPartyDepositRequest($walletAccountId, $paymentMethodId) {
		echo json_encode($this->payment_manager->reviewManualThirdPartyDepositRequest($walletAccountId, $paymentMethodId));
	}

	/**
	 * overview: Get Deposit Request Details
	 *
	 * note: FYI, the $walletAccountId variable is actually a payment method id, and $id is the walletAccountId
	 *
	 * @param int $walletAccountId payment method id
	 * @param int $id wallet account id
	 * @return	json
	 */
	public function getPaymentMethodDetails($walletAccountId, $id) {
		echo json_encode($this->payment_manager->getPaymentMethodDetails($walletAccountId, $id));
	}

	/**
	 * overview: Get 3rd party Deposit Request Details
	 *
	 * @return	json
	 * @deprecated
	 *
	 */
	public function getAutoThirdPartyDepositWithdrawalTransactionDetail($walletAccountId, $paymentMethodId) {
		echo json_encode($this->payment_manager->getAutoThirdPartyDepositWithdrawalTransactionDetail($walletAccountId, $paymentMethodId));
	}

	/**
	 * overview: Get Withdrawal Request Details
	 *
	 * detail: reviews withdrawal request for a certain wallet account and player
	 *
	 * note: parameters are both manadatory, error will occur.
	 *
	 * @param int $walletAccountId wallet account id
	 * @param int @playerId player id
	 * @return	json
	 *
	 */
	public function reviewWithdrawalRequest($walletAccountId, $playerId) {
		$this->load->model(array('withdraw_condition', 'transaction_notes', 'wallet_model', 'users', 'walletaccount_notes'));

		$data['transactionDetails'] = $this->withdraw_condition->getWithdrawalTransactionDetail($walletAccountId, $playerId);
		$data['walletAccountInternalNotes'] = $this->formatPaymentNotes($this->walletaccount_notes->getWalletAccountNotes(Walletaccount_notes::INTERNAL_NOTE, $walletAccountId), true);
		$data['walletAccountExternalNotes'] = $this->formatPaymentNotes($this->walletaccount_notes->getWalletAccountNotes(Walletaccount_notes::EXTERNAL_NOTE, $walletAccountId), true);
		$data['hasUnfinishedWithdrawCondition'] = $this->withdraw_condition->checkIfPlayerHasUnfinishedCondition($playerId);
		$data['dailyMaxWithdrawal'] = $this->utils->formatCurrencyNoSym($this->player_model->getPlayerWithdrawalRule($playerId)['dailyMaxWithdrawal']);
		$data['totalWithdrawalToday'] = $this->utils->formatCurrencyNoSym($this->player_model->getPlayerCurrentTotalWithdrawalToday($playerId)[0]['totalWithdrawalToday']);
		$data['withdrawSetting'] = $this->operatorglobalsettings->getCustomWithdrawalProcessingStage();

		$log_data_encode = $data;
		$this->utils->debug_log('========================================log_data_encode',$log_data_encode);
		$this->output->set_header('Access-Control-Allow-Origin: *');
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}

	public function get_withdraw_condition_by_withdraw_id($walletAccountId, $playerId) {
		$this->load->model(array('withdraw_condition', 'transaction_notes'));

		$data['withdrawConditions'] = $this->withdraw_condition->getPlayerWithdrawalCondition($playerId);
		$this->returnJsonResult($data);
	}

	/**
	 *
	 * overview: Transaction Notes
	 *
	 * detail: to format the transaction notes based on the given parameter array
	 *
	 * @param array $transactionNotes
	 * @return string
	 *
	 */
	private function formatTransNotes($transactionNotes) {
		$noteString = '';
		foreach ($transactionNotes as $aNote) {
			$noteString .= sprintf("[%s] %s: %s\n", $aNote['create_date'], $aNote['admin_name'], $aNote['note']);
		}
		$this->utils->debug_log("Formatted transaction notes: ", $noteString);
		return $noteString;
	}

	/**
	 *
	 * overview: depoist and withdrawal Notes
	 *
	 * detail: to format the transaction notes based on the given parameter array
	 *
	 * @param array $transactionNotes
	 * @return string
	 *
	 */
	private function formatPaymentNotes($paymentNotes, $showCustomStage = false) {
		$noteString = '';
		if(!empty($paymentNotes)){
			foreach ($paymentNotes as $aNote) {
				if($showCustomStage){
					if($aNote['status_name'] == null){
						$aNote['status_name'] = lang('no status');
					}
					$noteString .= sprintf("[%s] %s_%s: %s\n", $aNote['created_at'], $aNote['creater_name'], $aNote['status_name'], $aNote['content']);
				}else{
					$noteString .= sprintf("[%s] %s: %s\n", $aNote['created_at'], $aNote['creater_name'], $aNote['content']);
				}
			}
		}
		$this->utils->debug_log("Formatted Payment Notes: ", $noteString);
		return $noteString;
	}

	/**
	 *
	 * overview: Duplicate accounts
	 *
	 * detail: view duplicate accounts
	 *
	 * @param int $player_id player id
	 * @return load template
	 */
	public function viewDuplicateAccounts($player_id) {
		$data['duplicate_accounts'] = $this->getDuplicateAccounts($player_id);

		$this->load->view('payment_management/ajax_view_duplicate_accounts', $data);
	}

	/**
	 *
	 * overview: Duplicate accounts
	 *
	 * detail: view duplicate accounts in details using player id
	 *
	 * @deprecated
	 *
	 * @param int player_id
	 * @return load template
	 */
	public function viewDuplicateAccountsDetail($player_id = null) {
		$this->load->library('duplicate_account');
		if ($player_id != null) {
			$data['title'] = "Duplicate Account List for Player $player_id";
			$data['player_id'] = $player_id;
			$this->load->view('payment_management/ajax_view_duplicate_accounts_detail', $data);
		} else {
			echo "player_id cannot be NULL! Please add player ID to the URL.\n";
		}
	}

	/**
	 *
	 * overview: Duplicate accounts
	 *
	 * detail: view duplicate account using username
	 *
	 * @param string $username player username
	 * @return load template
	 *
	 */
	public function viewDuplicateAccountsDetailByUsername($username = null) {
		$this->load->library('duplicate_account');
		if ($username != null) {
			$this->load->library('player_manager');
			$player_id = $this->player_manager->getPlayerIdByUsername($username);
			if ($player_id) {
				$data['title'] = lang('Duplicate Accounts Details');
				$data['player_id'] = $player_id;
				$data['username'] = $username;
				//$this->load->view('payment_management/ajax_view_duplicate_accounts_detail', $data);
				$this->loadTemplate(lang('Duplicate Accounts Details'), '', '', 'report');
				$this->template->add_js('resources/js/bootstrap-switch.min.js');
				$this->template->add_css('resources/css/bootstrap-switch.min.css');
				$this->template->write_view('sidebar', 'report_management/sidebar');
				$this->template->write_view('main_content', 'payment_management/view_duplicate_accounts_detail', $data);
				$this->template->render();
			}
		} else {
			echo "player_id cannot be NULL! Please add player ID to the URL.\n";
		}
	}

	/**
	 *
	 * overview: Duplicate accounts
	 *
	 * detail: view duplicate accounts in details using player id
	 *
	 * @param int player_id
	 * @return load template
	 */
	public function viewDuplicateAccountsDetailById($player_id) {
		$this->load->library('duplicate_account');
		if ($player_id) {
			$data['title'] = lang('Duplicate Accounts Details');
			$data['player_id'] = $player_id;
			$this->load->view('payment_management/view_duplicate_accounts_detail_fieldset', $data);
		} else {
			echo "player_id cannot be NULL! Please add player ID to the URL.\n";
		}
	}

	public function viewPlayerCenterFinancialAccountSettings($type = 1) {
		if (!$this->permissions->checkPermissions('player_financial_account_rules_setting')) {
			$this->error_access();
		} else {
			$data['type'] = $type;
			$this->loadTemplate(lang('pay.financial_account_setting'), '', '', 'system');
			$this->template->write_view('sidebar', 'system_management/sidebar');
			$this->template->write_view('main_content', 'payment_management/financial_account_setting/view_player_center_financial_account_setting', $data);
			$this->template->render();
		}
	}

	public function changeFinancialAccountSetting($type = 1) {
		if (!$this->permissions->checkPermissions('player_financial_account_rules_setting')) {
			$this->error_access();
		} else {
			if($type == 0){
				$data = $this->operatorglobalsettings->getSystemSettings(self::FINANCIAL_ACCOUNT_SETTING_OTHERS_LIST);
			} else {
				$this->load->model('financial_account_setting');
				$financial_account_rule = $this->financial_account_setting->getPlayerFinancialAccountRulesByPaymentAccountFlag($type);
				$data['payment_type_flag'] = $type;

				if(is_array($financial_account_rule)){
					$data['account_number_min_length'] = $financial_account_rule['account_number_min_length'];
					$data['account_number_max_length'] = $financial_account_rule['account_number_max_length'];
					$data['account_number_only_allow_numeric'] = $financial_account_rule['account_number_only_allow_numeric'];
					$data['account_name_allow_modify_by_players'] = $financial_account_rule['account_name_allow_modify_by_players'];
					$data['field_show'] = explode(',', $financial_account_rule['field_show']);
					$data['field_required'] = explode(',', $financial_account_rule['field_required']);
				}
			}

			switch ($type) {
				case 0:
					$this->load->view('payment_management/financial_account_setting/ajax_financial_account_setting_others', $data);
					break;
				case 1:
					$this->load->view('payment_management/financial_account_setting/ajax_financial_account_setting_bank', $data);
					break;
				case 2:
					$this->load->view('payment_management/financial_account_setting/ajax_financial_account_setting_ewallet', $data);
					break;
				case 3:
					$this->load->view('payment_management/financial_account_setting/ajax_financial_account_setting_crypto', $data);
					break;
				default:
					break;
			}
		}
	}

    public function saveFinancialAccountSetting($type) {
        if (!$this->permissions->checkPermissions('player_financial_account_rules_setting')) {
            $this->error_access();
        } else {
			$this->load->model('financial_account_setting');
			$field_show = is_array($this->input->post('field_show')) ? $this->input->post('field_show') : array();
			$field_required = is_array($this->input->post('field_required')) ? $this->input->post('field_required') : array();
            $data = array(
                'account_number_min_length'            => $this->input->post("length_min"),
                'account_number_max_length'            => $this->input->post("length_max"),
                'account_number_only_allow_numeric'    => $this->input->post("number_only"),
                'account_name_allow_modify_by_players' => $this->input->post("name_edit"),
                'field_show'                           => implode(',', $field_show),
                'field_required'                       => implode(',', $field_required),
            );

            $this->financial_account_setting->updatePlayerFinancialAccountRules($type, $data);

            $message = lang('con.pym05');
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
            redirect('payment_management/viewPlayerCenterFinancialAccountSettings/'.$type);
        }
    }

	public function saveFinancialAccountSettingOthers() {
		if (!$this->permissions->checkPermissions('player_financial_account_rules_setting')) {
			$this->error_access();
		} else {

			$this->load->model('operatorglobalsettings');
			foreach (self::FINANCIAL_ACCOUNT_SETTING_OTHERS_LIST as $key) {
				$settings[$key] = $this->input->post($key);
			}

			$this->operatorglobalsettings->saveSettings($settings);

			$message = lang('con.pym05');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			redirect('payment_management/viewPlayerCenterFinancialAccountSettings/0');
		}
	}

	/**
	 *
	 * detail: Get Duplicate Accounts
	 *
	 * @param int $player_id player id
	 * @return array
	 */
	public function getDuplicateAccounts($player_id) {
		$this->load->library('duplicate_account');

		$duplicates = $this->duplicate_account->scanDuplicateAccount($player_id); // search for duplicate accounts

		$result = $this->duplicate_account->listOfDuplicates($duplicates, $player_id); // create one list and get rating

		return $result;
	}

	/**
	 * detail: Get Deposit Approved Details
	 *
	 * @param int $walletAccountId wallet account id
	 * @return json
	 */
	public function reviewDepositApprovedLocalBank($walletAccountId) {
		echo json_encode($this->payment_manager->getDepositApprovedTransactionDetail($walletAccountId));
	}

	/**
	 * detail: Get Deposit Approved Details
	 *
	 * @param int $walletAccountId wallet account id
	 * @return json
	 */
	public function reviewDepositApproved($walletAccountId) {
		echo json_encode($this->payment_manager->getDepositApprovedTransactionDetail($walletAccountId));
	}

	/**
	 * detail: Get Deposit Approved Details
	 *
	 * @param int $walletAccountId wallet account id
	 * @return json
	 */
	public function reviewManualThirdPartyDepositApproved($walletAccountId) {
		echo json_encode($this->payment_manager->getManualThirdPartyDepositApprovedTransactionDetail($walletAccountId));
	}

	/**
	 * detail: Get Deposit Approved Details
	 *
	 * @param int $walletAccountId wallet account id
	 * @return json
	 */
	public function reviewManualThirdPartyDepositDeclined($walletAccountId) {
		echo json_encode($this->payment_manager->getManualThirdPartyDepositDeclinedTransactionDetail($walletAccountId));
	}

	/**
	 * detail: Get Withdrawal Approved Details
	 *
	 * @param int $walletAccountId wallet account id
	 * @return json
	 */
	public function reviewWithdrawalApproved($walletAccountId) {
		$this->load->model(array('transaction_notes', 'wallet_model','walletaccount_notes'));
		$data = $this->wallet_model->getWithdrawalApprovedTransactionDetail($walletAccountId);

		$data['walletAccountInternalNotes'] = $this->formatPaymentNotes($this->walletaccount_notes->getWalletAccountNotes(Walletaccount_notes::INTERNAL_NOTE, $walletAccountId), true);
		$data['walletAccountExternalNotes'] = $this->formatPaymentNotes($this->walletaccount_notes->getWalletAccountNotes(Walletaccount_notes::EXTERNAL_NOTE, $walletAccountId), true);

		$data['withdrawSetting'] = $this->operatorglobalsettings->getCustomWithdrawalProcessingStage();
		$withdrawAPIs = $this->external_system->getWithdrawPaymentSystemsKV();
		$withdrawAPI_id = $data[0]['paymentAPI'];
		$data['withdraw_method_display'] = array_key_exists($withdrawAPI_id, $withdrawAPIs) ? $withdrawAPIs[$withdrawAPI_id] : lang('Manual Payment');
		$data['withdraw_id_hidden'] = $withdrawAPI_id;

		$this->output->set_header('Access-Control-Allow-Origin: *');
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}

	/**
	 * detail: Get Deposit Declined Details
	 *
	 * @param int $depositRequestId wallet account id
	 * @return json
	 */
	public function reviewDepositDeclined($depositRequestId) {
		$this->returnJsonResult($this->payment_manager->getDepositDeclinedTransactionDetail($depositRequestId));
	}

	/**
	 * detail: Get Deposit Declined Details
	 *
	 * @param int $depositRequestId wallet account id
	 * @return json
	 */
	public function reviewAuto3rdPartyDepositDeclined($depositRequestId) {
		echo json_encode($this->payment_manager->reviewAuto3rdPartyDepositDeclined($depositRequestId));
	}

	/**
	 * detail: Get Withdrawal Declined Details
	 *
	 * @param int $depositRequestId wallet account id
	 * @return json
	 */
	public function reviewWithdrawalDeclined($depositRequestId) {
		$this->load->model(['wallet_model']);

		$this->returnJsonResult($this->wallet_model->getWithdrawalDeclinedTransactionDetail($depositRequestId));
	}

	/**
	 * setPlayerTotalDeposit
	 *
	 * @return	array
	 */
	public function setPlayerTotalDeposit($playerAccountId) {
		$totalDeposit = $this->payment_manager->getPlayerTotalDeposit($playerAccountId);
	}

	/**
	 * overview: Deposit transaction fee
	 *
	 * detail: add deposit transaction fee
	 *
	 * @return json
	 */
	public function addDepositTransactionFee() {
		$this->load->model(array('sale_order', 'player_promo', 'transactions'));
		$transaction = "deposit";
		$adminUserId = $this->authentication->getUserId();
		$transaction_fee = $_GET['transaction_fee'];
		$saleorder_id = $_GET['saleorder_id'];
		$saleOrderDetail = $this->sale_order->getSaleOrderById($saleorder_id);
		if ($saleOrderDetail && !empty($transaction_fee)) {
			$saleOrderDetail->amount = $this->utils->formatCurrencyNoSym($saleOrderDetail->amount);
			//translate
			if (!empty($saleOrderDetail->payment_type_name)) {
				$saleOrderDetail->payment_type_name = lang($saleOrderDetail->payment_type_name);
			}
			if (!empty($saleOrderDetail->player_payment_type_name)) {
				$saleOrderDetail->player_payment_type_name = lang($saleOrderDetail->player_payment_type_name);
			}
			$saleOrderDetail->playerActivePromo = $this->player_promo->getPlayerActivePromo($saleOrderDetail->player_id);
			$rlt = (array) $saleOrderDetail;
			$playerId = $rlt['player_id'];
			$related_trans_id = $rlt['transaction_id'];
			$success=$this->transactions->createTransactionFee($transaction_fee, $transaction, $adminUserId, $playerId, $related_trans_id, null, null, $saleorder_id);
			$result = array('success' => $success);
		} else {
			$result = array('success' => false);
		}
		$this->returnJsonResult($result);
	}

	/**
	 * clearPlayerBonus
	 *
	 * @return	array
	 */
	public function clearPlayerBonus($playerDepositPromoId) {
		if ($playerDepositPromoId != 0) {
			$promoData = array('promoStatus' => 2, //cleared
			);

			$this->payment_manager->clearPlayerDepositBonus($playerDepositPromoId, $promoData);
		}
	}

	public function getPlayerBonusAmount($playerDepositPromoId) {
		$bonusAmount = $this->payment_manager->getPlayerBonusAmount($playerDepositPromoId);
	}

	public function setWithdrawToRequest($walletAccountId, $data = null) {
		$adminUserId = $this->authentication->getUserId();
		$adminUserName = $this->authentication->getUsername();
		$reason = 'Unlock Order by '.$adminUserName. ' successful.';

        $wallet_account = $this->wallet_model->getWalletAccountObject($walletAccountId);
        if(empty($wallet_account)){
            $this->returnJsonResult(['success' => false, 'message'=>lang('Can not find transaction')]);
            return;
        }

        $this->utils->debug_log("=====setWithdrawToRequest transactionCode", $wallet_account['transactionCode'], "adminUserId", "data", $data ,'dwStatus', $wallet_account['dwStatus']);

        $result = $this->wallet_model->requestWithdrawal($adminUserId, $walletAccountId, $reason, false);
        if(!$result) {
			$reason = 'Unlock Order by '.$adminUserName. ' failed.';
        }
        $this->wallet_model->addWalletaccountNotes($walletAccountId,$adminUserId,$reason,$wallet_account['dwStatus'],Wallet_model::REQUEST_STATUS);
        $this->returnJsonResult(['success' => $result, 'message'=>$reason]);
	}

	public function setWithdrawToPayProc($walletAccountId, $withdrawApi = 0, $data = null) {
		if (!$this->permissions->checkPermissions('approve_withdrawal_to_payProc')) {
			$this->utils->error_log('permission failed', 'approve_withdrawal_to_payProc');
			$this->error_access();
			return;
		}

		$actionlogNotes = 'Manual Payment : Set Withdraw To PayProc';
		$adminUserId = $this->authentication->getUserId();
		$transaction_fee = $this->input->post('transaction_fee');
		// $showRemarksToPlayerForPaid = $this->input->post(null');
		$ignoreWithdrawalAmountLimit = $this->input->post('ignoreWithdrawalAmountLimit');
		$ignoreWithdrawalTimesLimit = $this->input->post('ignoreWithdrawalTimesLimit');

        $wallet_account = $this->wallet_model->getWalletAccountObject($walletAccountId);
        if(empty($wallet_account)){
            $this->returnJsonResult(['success' => false, 'message'=>lang('Can not find transaction')]);
            return;
        }

		$this->utils->debug_log("=====setWithdrawToPayProc transactionCode", $wallet_account['transactionCode'], "adminUserId", $adminUserId, "withdrawApi", $withdrawApi, "data", $data);

        $playerId = $wallet_account['playerId'];
        $amount = $wallet_account['amount'];
        $playerWithdrawalRule = $this->utils->getWithdrawMinMax($playerId);
        list($withdrawalProcessingCount, $processingAmount) = $this->wallet_model->countTodayProcessingWithdraw($playerId);
		list($withdrawalPaidCount, $paidAmount) = $this->transactions->count_today_withdraw($playerId);

		#check if withdrawal amount exceeds limit
		$amount_used = $processingAmount + $paidAmount;
		if ($amount + $amount_used > $playerWithdrawalRule['dailyMaxWithdrawalAmount']) {
			if($ignoreWithdrawalAmountLimit && $this->permissions->checkPermissions('ignore_vip_daily_withdrawal_maximum_amount_settings_when_approve')){
				$this->utils->debug_log("=====setWithdrawToPayProc ignoreWithdrawalAmountLimit", $ignoreWithdrawalAmountLimit, "adminUserId", $adminUserId);
			}
			else{
				$result=['success'=>false, 'message'=> lang('notify.56') . ' ( $'. $amount_used . '/ $'.$playerWithdrawalRule['dailyMaxWithdrawalAmount'].' )'];
				$this->returnJsonResult($result);
				return;
			}
		}
        if ($withdrawalProcessingCount + $withdrawalPaidCount >= $playerWithdrawalRule['withdraw_times_limit']) {
			if($ignoreWithdrawalTimesLimit && $this->permissions->checkPermissions('ignore_vip_daily_withdrawal_maximum_times_settings_when_approve')){
				$this->utils->debug_log("=====setWithdrawToPayProc ignoreWithdrawalTimesLimit", $ignoreWithdrawalTimesLimit, "adminUserId", $adminUserId);
			}
			else{
	            $result=['success'=>false, 'message'=>lang('notify.106')];
	            $this->returnJsonResult($result);
	            return;
			}

        }

		if ($withdrawApi > 0) {
			# Trigger the API payment process automatically. This will put the status to payProc
			$this->setWithdrawToPaid($walletAccountId, $withdrawApi, $data);
		} else {
			$this->load->model(array('wallet_model'));
			$this->wallet_model->payProcWithdrawal($adminUserId, $walletAccountId, $withdrawApi, $actionlogNotes, $transaction_fee, null);
		}
	}

	/**
	 * detail: update the wallet to paid
	 *
	 * @param int $walletAccountId wallet account id
	 * @param int $withdrawApi costum
	 * @return json
	 */
	public function setWithdrawToPaid($walletAccountId, $withdrawApi = -1, $data = null, $batchApproved = false) {
		$result = array('success' => false, 'message' => '');
		if (!$this->permissions->checkPermissions('set_withdraw_status_to_paid')
			&& !$this->permissions->checkPermissions('approve_decline_withdraw')) {
			$result=['success'=>false, 'message'=>lang('Sorry, no permission')];
			$this->returnJsonResult($result);
			return;
		}

		$this->load->model(['wallet_model', 'withdraw_condition','system_feature','walletaccount_timelog']);
		$adminUserId = $this->authentication->getUserId();
		//check current user withdrawal amount
		if(!$this->wallet_model->checkUserWithdrawalAmount($adminUserId, $walletAccountId)){
			$result = ['success' => false, 'message'=>lang('No enough withdrawal amount permission')];
			return $this->returnJsonResult($result);
		}

		$controller = $this;
		// $result = array(); # will be passed to closure as reference
        $wallet_account = $this->wallet_model->getWalletAccountObject($walletAccountId);
        if(empty($wallet_account)){
            $this->returnJsonResult(['success' => false]);
            return;
        }

        $playerId = $wallet_account['playerId'];

		$success = $this->lockAndTrans(Utils::LOCK_ACTION_BALANCE, $playerId, function () use ($controller, $walletAccountId, $adminUserId, $withdrawApi, &$result, $playerId, $data, $batchApproved) {
			$result = array('success' => false, 'message' => '');

			if($this->wallet_model->isLockedForManual($walletAccountId, $adminUserId)){
				$result = ['success' => false, 'message'=>lang('this withdrawal has been locked')];

				return $result['success'];
			}

			$transaction_fee = $controller->input->post('transaction_fee') ?: 0;
			// $showRemarksToPlayerForPaid = $controller->input->post('showRemarksToPlayerForPaid');

			# Get payment detail
			$transactionDetail = $controller->withdraw_condition->getWithdrawalTransactionDetail($walletAccountId);
            $applyWithdrawDatetime = isset($transactionDetail[0]['dwDateTime']) ? $transactionDetail[0]['dwDateTime'] : null;

			if (count($transactionDetail) < 1) {
				$controller->utils->error_log("No transaction detail found for walletAccountId [$walletAccountId]");
				$result['message'] = lang('error.withdrawal_failed');
				return $result['success'];
			}

			$isPayProc = ($transactionDetail[0]['dwStatus'] == Wallet_model::PAY_PROC_STATUS);

			$actionlogNotes = $batchApproved ? 'Batch Approved.' : 'Set Withdraw To Paid.';
			# Perform API withdraw
			if ($withdrawApi > 0 && !$isPayProc) {
				# Update status to 'payProc' that we are submitting to API
				$result = $controller->wallet_model->submitWithdrawalToAPI($adminUserId, $walletAccountId, $actionlogNotes, $transaction_fee, null, $withdrawApi);
				if($result['success']){

					# Perform payment using API
					list($loaded, $apiClassName) = $controller->utils->loadExternalSystemLib($withdrawApi);
					if ($loaded) {
						$controller->utils->debug_log('withdraw', $transactionDetail[0]['transactionCode']);
						if(is_null($data)){
							$paymentResult = $controller->$apiClassName->submitWithdrawRequest(
								$transactionDetail[0]['bankTypeId'],
								$transactionDetail[0]['bankAccountNumber'],
								$transactionDetail[0]['bankAccountFullName'],
								$transactionDetail[0]['amount'],
								$transactionDetail[0]['transactionCode']
							);
						}
						else{
							$paymentResult = $controller->$apiClassName->submitWithdrawRequest(
								$transactionDetail[0]['bankTypeId'],
								$transactionDetail[0]['bankAccountNumber'],
								$transactionDetail[0]['bankAccountFullName'],
								$transactionDetail[0]['amount'],
								$transactionDetail[0]['transactionCode'],
								$data
							);
						}

						$controller->utils->debug_log('paymentResult', $paymentResult);

						$result['success'] = $paymentResult['success'];
						$result['message'] = $paymentResult['message'];
						$result['response_result'] = (isset($paymentResult['response_result'])) ? $paymentResult['response_result'] : FALSE;
						$result['lock'] = (isset($paymentResult['lock'])) ? $paymentResult['lock'] : FALSE;

						if($result['success']){
							$this->wallet_model->addWalletaccountNotes($walletAccountId, $adminUserId, $result['message'], $transactionDetail[0]['dwStatus'], Wallet_model::PAY_PROC_STATUS, Walletaccount_timelog::ADMIN_USER);
						}

						$this->withdraw_condition->disablePlayerWithdrawalCondition( $playerId  // #2
														, Withdraw_condition::REASON_AFBW  // #3
														, Withdraw_condition::DETAIL_STATUS_FINISHED_BETTING_AMOUNT_WHEN_WITHDRAW  // #4
														, $applyWithdrawDatetime  // #5
													);

					} else {
						# load API failed, mark payment as failed
						$controller->utils->debug_log("Load API [$withdrawApi] for withdrawal failed, payment is not done through API.");
						$result['message'] = lang('error.withdrawal_failed');
					}

				}else{
					$this->utils->error_log('submitWithdrawalToAPI failed', $adminUserId, $walletAccountId);
				}
			} else {
				# API payment not enabled or already in payProc (API already processed but failed)
				# Set the withdraw request status to 'paid' only when API payment is not enabled
				$message='';
				$result['success'] = $controller->wallet_model->paidWithdrawal($adminUserId, $walletAccountId,
					$actionlogNotes, $transaction_fee, null, false, $message);
				if(!empty($message)){
					$result['message']= $message;
				}

				$this->withdraw_condition->disablePlayerWithdrawalCondition($playerId // #1
												, Withdraw_condition::REASON_AFBW // #2
												, Withdraw_condition::DETAIL_STATUS_FINISHED_BETTING_AMOUNT_WHEN_WITHDRAW // #3
												, $applyWithdrawDatetime // #4
											);
            }

			return $result['success'];
		});

		$controller->utils->debug_log('result', $result);

		if (!$success && empty($result['message']) && isset($result['err_msg'])) {
			if(!isset($result['err_msg'])){
                $result['message'] = lang('error.default.db.message');
            }else{
			    $result['message'] = $result['err_msg'];
            }
		}

		if (!$success) {
			if(isset($result['response_result']) && $result['response_result']){
				$response_result = $result['response_result'];
				list($loaded, $apiClassName) = $controller->utils->loadExternalSystemLib($withdrawApi);
				if ($loaded) {
					$response_result_id = $controller->$apiClassName->submitPreprocess($response_result[0], $response_result[1], $response_result[2], $response_result[3], $response_result[4], $response_result[5]);
					$controller->utils->debug_log('==============setWithdrawToPaid record response_result when fail, response_result_id', $response_result_id);
				}
			}


			$this->utils->error_log('withdraw api failed, keep processing', $walletAccountId, $result);
			# API fail, do not set the order status to fail, but append a message
			# $this->respondToWithdrawalDeclined($walletAccountId, 'api failed');
			$transactionDetail = $controller->withdraw_condition->getWithdrawalTransactionDetail($walletAccountId);
			$this->wallet_model->addWalletaccountNotes($walletAccountId, $adminUserId, lang("Payment Failed") . " - " . $result['message'], $transactionDetail[0]['dwStatus'], Wallet_model::LOCK_API_UNKNOWN_STATUS, Walletaccount_timelog::ADMIN_USER);

			if($result['lock']) {
				$succ = $this->wallet_model->updateWithdrawalRequestStatusToUnknownStatus($adminUserId, $walletAccountId, $result['message']);
				if($succ) {
					$this->saveAction(self::ACTION_LOG_TITLE, 'Modify Witdrawal Request Status', "User " . $this->authentication->getUsername() . " has changed withdrawal request [$walletAccountId] to ".wallet_model::LOCK_API_UNKNOWN_STATUS);
				}
			}
		}

		if ($success && $withdrawApi == -1) {
			# send prompt message when withdrawal order is success
			if ($this->utils->isEnabledFeature('enable_sms_withdrawal_prompt_action_success')) {

				$this->load->model(['cms_model', 'queue_result', 'player_model']);
				$this->load->library(["lib_queue", "sms/sms_sender"]);

				$player = $this->player_model->getPlayerInfoDetailById($playerId);
				$mobileNumIsVeridied = $player['verified_phone'];

				if($mobileNumIsVeridied){
					$isUseQueueToSend = $this->utils->isEnabledFeature('enabled_send_sms_use_queue_server');
					$mobileNum  = $player['contactNumber'];
					$smsContent = $this->cms_model->getManagerContent(Cms_model::SMS_MSG_WITHDRAWAL_SUCCESS);
					if ($isUseQueueToSend) {
						$callerType = Queue_result::CALLER_TYPE_ADMIN;
						$caller = $adminUserId;
						$state  = null;
						$this->lib_queue->addRemoteSMSJob($mobileNum, $smsContent, $callerType, $caller, $state, null);
					} else {
						$this->sms_sender->send($mobileNum, $smsContent);
					}
				}
			}
		}

        $this->load->library(['player_notification_library']);
        if($success){
            $this->player_notification_library->success($wallet_account['playerId'], Player_notification::SOURCE_TYPE_WITHDRAWAL, [
                'player_notify_success_withdrawal_title',
                $wallet_account['transactionCode'],
                $wallet_account['dwDateTime'],
                $this->utils->displayCurrency($wallet_account['amount']),
                $this->utils->getPlayerHistoryUrl('withdrawal')
            ], [
                'player_notify_success_withdrawal_message',
                $wallet_account['transactionCode'],
                $wallet_account['dwDateTime'],
                $this->utils->displayCurrency($wallet_account['amount']),
                $this->utils->getPlayerHistoryUrl('withdrawal')
            ]);
        }else{
            $this->player_notification_library->danger($wallet_account['playerId'], Player_notification::SOURCE_TYPE_WITHDRAWAL, [
                'player_notify_danger_withdrawal_title',
                $wallet_account['transactionCode'],
                $wallet_account['dwDateTime'],
                $this->utils->displayCurrency($wallet_account['amount']),
                $this->utils->getLiveChatLink(),
                $this->utils->getLiveChatOnClick()
            ], [
                'player_notify_danger_withdrawal_message',
                $wallet_account['transactionCode'],
                $wallet_account['dwDateTime'],
                $this->utils->displayCurrency($wallet_account['amount']),
                $this->utils->getLiveChatLink(),
                $this->utils->getLiveChatOnClick()
            ]);
        }

		$this->returnJsonResult($result);
	}

	/**
	 * detail: Respond to Withdrawal approve
	 *
	 * @param int $walletAccountId wallet account id
	 * @param int $playerId player id
	 * @param string $showRemarksToPlayer costum
	 * @param string $nextStatus
	 * @return string
	 */
	public function respondToWithdrawalRequest($walletAccountId, $playerId, $showRemarksToPlayer = '0', $nextStatus, $batchProcess = false) {
        if($this->utils->isEnabledFeature('separate_approve_decline_withdraw_pending_review_and_request_permission')){

        	//next status = approve_withdrawal_to_CS0 and current status is pending_review
        	$dwStatus=$this->wallet_model->getWalletAccountStatus($walletAccountId);

        	$perms=['payment_withdrawal_list', 'approve_withdrawal_to_' . $nextStatus];
        	if($dwStatus==Wallet_model::REQUEST_STATUS && $nextStatus=='approve_withdrawal_to_CS0'){
        		$perms=['payment_withdrawal_list', 'approve_decline_withdraw_pending_request', 'approve_withdrawal_to_' . $nextStatus];
        	}

        	if($dwStatus==Wallet_model::PENDING_REVIEW_STATUS && $nextStatus=='approve_withdrawal_to_CS0'){
        		$perms=['payment_withdrawal_list', 'approve_decline_withdraw_pending_review', 'approve_withdrawal_to_' . $nextStatus];
        	}
        }else{
        	$perms=['payment_withdrawal_list', 'approve_decline_withdraw', 'approve_withdrawal_to_' . $nextStatus];
		}

		if (!$this->permissions->checkPermissions($perms)) {
			$this->utils->error_log('permission failed', $perms);
			$this->returnText(lang('No Permission'));
			return;
		}

		$actionlogNotes = $batchProcess ? 'Batch Process.' : 'Set Withdraw To Custom Stage';

		if (empty($walletAccountId)) {
			$this->utils->error_log('walletAccountId is empty', $walletAccountId, $playerId, $showRemarksToPlayer, $nextStatus);
			$this->returnText(lang('Not Found ID'));
			return;
		}

		$this->load->model(array('wallet_model', 'withdraw_condition', 'system_feature'));

		$playerId = $this->wallet_model->getPlayerIdFromWalletAccount($walletAccountId);
		$walletAccount = $this->wallet_model->getWalletAccountBy($walletAccountId);

		$lockedKey=null;
		$lock_it = $this->lockPlayerBalanceResource($playerId, $lockedKey);

		if (!$lock_it) {
			$this->utils->error_log('lock balance failed', 'id', $playerId);
			$this->returnText(lang('Lock Failed'));
			return;
		}

		//lock success
		try {
			$this->startTrans();

			$user_id = $this->authentication->getUserId();
			$currentStatus = $walletAccount->dwStatus;

			$succ = $this->wallet_model->updateWithdrawalRequestStatus($user_id, $walletAccountId, $nextStatus, $actionlogNotes, $showRemarksToPlayer);
			$this->saveAction(self::ACTION_LOG_TITLE, 'Modify Witdrawal Request Status', "User " . $this->authentication->getUsername() . " has changed withdrawal request [$walletAccountId] from [$currentStatus] to [$nextStatus].");

			$succ = $this->endTransWithSucc() && $succ;
			if ($succ) {
				return $this->returnText('success');
			} else {
				return $this->returnText(lang('Update Status Failed'));
			}

		} finally {
			// release it
			$rlt = $this->releasePlayerBalanceResource($playerId, $lockedKey);
		}
	}

	/**
	 * detail: Respond to Withdrawal declined
	 *
	 * @param int $walletAccountId wallet account id
	 * @param string $showDeclinedReason
	 * @param int $playerpromoId player promo id
	 * @return json
	 */
	public function respondToWithdrawalDeclined($walletAccountId, $showDeclinedReason, $playerpromoId = '', $status = null) {
		$this->load->model(array('wallet_model'));
		//check permission
        if($this->utils->isEnabledFeature('separate_approve_decline_withdraw_pending_review_and_request_permission')){
            $permissions = 'payment_withdrawal_list';
            if($status == Wallet_model::REQUEST_STATUS){
                $permissions = array('payment_withdrawal_list', 'approve_decline_withdraw_pending_request');
            }
            if($status == Wallet_model::PENDING_REVIEW_STATUS) {
                $permissions = array('payment_withdrawal_list', 'approve_decline_withdraw_pending_review');
            }
        }else{
            $permissions = array('payment_withdrawal_list', 'approve_decline_withdraw');
        }

		if (!$this->permissions->checkPermissions($permissions)) {
			$this->utils->error_log('permission failed', $permissions);
			$this->returnText('failed');
			return;
		}

		$notesType = $this->input->get('notesType');
		$this->utils->debug_log('======================== notesType ' . $notesType);
		switch ($notesType) {
			case self::DECLINED_WITHDRAW_REQUEST : //'forRequest':
				$actionlogNotes = 'Set Withdraw To Declined For Request';
				break;

			case self::DECLINED_WITHDRAW_PAID ://'forPaid':
				$actionlogNotes = 'Set Withdraw To Declined For Paid';
				break;

			case self::DECLINED_WITHDRAW_BATCH: //'batchDeclined':
				$actionlogNotes = 'Batch Declined.';
				break;
			default:
				$this->returnJsonResult(['success' => false, 'message'=>lang('Error')]);
				return;
		}

		$succ = false;
        $wallet_account = $this->wallet_model->getWalletAccountObject($walletAccountId);
        if(empty($wallet_account)){
            $this->returnJsonResult(['success' => false]);
            return;
        }

        $playerId = $wallet_account['playerId'];
        $lockedKey=null;
		$lock_it = $this->lockPlayerBalanceResource($playerId, $lockedKey);
		if (!$lock_it) {
			//withdrawl failed
			$this->returnJsonResult(['success' => $succ]);
			return;
		}

		//lock success
		try {

			$adminUserId = $this->authentication->getUserId();
			$adminUsername = $this->authentication->getUsername();

			if($this->wallet_model->isLockedForManual($walletAccountId, $adminUserId)){
				$result = ['success' => false, 'message'=>lang('this withdrawal has been locked')];

				return $this->returnJsonResult($result);
			}

			$this->startTrans();
			$declinedCategoryId = null;
			if ($this->utils->isEnabledFeature('enable_withdrawal_declined_category') ){
				$declinedCategoryId = $this->input->get('declined_category_id');
				$this->utils->debug_log('declinedCategoryId init ', $declinedCategoryId);
			}

			$succ = $this->wallet_model->declineWithdrawalRequest($adminUserId, $walletAccountId, $actionlogNotes, $showDeclinedReason, $declinedCategoryId);

			$this->saveAction(self::ACTION_LOG_TITLE, 'Declined Withdrawal Request', "User " . $adminUsername . " has declined deposit/withdrawal request.");

			$succ = $this->endTransWithSucc() && $succ;
		} finally {
			// release it
			$rlt = $this->releasePlayerBalanceResource($playerId, $lockedKey);
		}

		if($succ) {
			$this->wallet_model->userUnlockWithdrawal($walletAccountId);

			# send prompt message when withdrawal order is decline
			if ($this->utils->isEnabledFeature('enable_sms_withdrawal_prompt_action_declined')) {

				$this->load->model(['cms_model', 'queue_result', 'player_model']);
				$this->load->library(["lib_queue", "sms/sms_sender"]);

				$player = $this->player_model->getPlayerInfoDetailById($playerId);
				$mobileNumIsVeridied = $player['verified_phone'];

				if($mobileNumIsVeridied) {
					$isUseQueueToSend    = $this->utils->isEnabledFeature('enabled_send_sms_use_queue_server');
					$mobileNum  = $player['contactNumber'];
					$smsContent = $this->cms_model->getManagerContent(Cms_model::SMS_MSG_WITHDRAWAL_DECLINE);

					if ($isUseQueueToSend) {
						$callerType = Queue_result::CALLER_TYPE_ADMIN;
						$caller = $adminUserId;
						$state  = null;
						$this->lib_queue->addRemoteSMSJob($mobileNum, $smsContent, $callerType, $caller, $state, null);
					} else {
						$this->sms_sender->send($mobileNum, $smsContent);
					}
				}
			}
		}

        $this->load->library(['player_notification_library']);
        $this->player_notification_library->danger($wallet_account['playerId'], Player_notification::SOURCE_TYPE_WITHDRAWAL, [
            'player_notify_danger_withdrawal_title',
            $wallet_account['transactionCode'],
            $wallet_account['dwDateTime'],
            $this->utils->displayCurrency($wallet_account['amount']),
            $this->utils->getLiveChatLink(),
            $this->utils->getLiveChatOnClick()
        ], [
            'player_notify_danger_withdrawal_message',
            $wallet_account['transactionCode'],
            $wallet_account['dwDateTime'],
            $this->utils->displayCurrency($wallet_account['amount']),
            $this->utils->getLiveChatLink(),
            $this->utils->getLiveChatOnClick()
        ]);

		$this->returnJsonResult(['success' => $succ]);
	}

	/**
	 *
	 * detail: adding notes to a certain transaction
	 *
	 * @param string $transaction
	 * @param int $transactionId transaction id
	 * @return json
	 *
	 */
	public function addDepositNotes($note_type, $saleOrderId) {
		if(isset($saleOrderId)){
			$this->load->model(array('sale_orders_notes','sale_order'));
			$status = $this->sale_order->getStatus($saleOrderId);
			$stage_name = $this->getStageName($status);
			$adminUserId = $this->authentication->getUserId();
			$note = $this->input->post('notes');

			if($note_type == Sale_orders_notes::INTERNAL_NOTE){
				$lastId   = $this->sale_orders_notes->add($note, $adminUserId, $note_type, $saleOrderId, $stage_name);
				$notesArr = $this->sale_orders_notes->getBySaleOrdersNotes($note_type, $saleOrderId);
				$notes    = $this->formatPaymentNotes($notesArr);
			}elseif($note_type == Sale_orders_notes::EXTERNAL_NOTE){
				$lastId   = $this->sale_orders_notes->add($note, $adminUserId, $note_type, $saleOrderId, $stage_name);
				$notesArr = $this->sale_orders_notes->getBySaleOrdersNotes($note_type, $saleOrderId);
				$notes    = $this->formatPaymentNotes($notesArr);
			}else{
				$this->returnJsonResult(array('success'=>false));
			}
			$this->returnJsonResult(array('success' => true, 'notes' => $notes,'notesArr' => $notesArr));
		}else{
			$this->returnJsonResult(array('success'=>false));
		}
	}

	/**
	 *
	 * detail: adding notes to a certain transaction
	 *
	 * @param string $transaction
	 * @param int $transactionId transaction id
	 * @return json
	 *
	 */
	public function addWithdrawalNotes($transaction, $transactionId) {
		if(isset($transactionId)){
			$this->load->model(array('transaction_notes','wallet_model','walletaccount_notes'));
			$adminUserId = $this->authentication->getUserId();
			$note = $this->input->post('notes');
			$note_type = $this->input->post('noteTypes');
			$status = $this->input->post('status');
			$stage_name = $this->getStageName($status);

			$this->wallet_model->userUnlockWithdrawal($transactionId);		// unlock withdrawal

			#save to walletaccount_notes , $transaction = Withdrawal $transactionId = walletAccountId
			if($note_type == Walletaccount_notes::ACTION_LOG){
				$lastId   = $this->walletaccount_notes->add($note, Users::SUPER_ADMIN_ID, $note_type, $transactionId);
				$notesArr = $this->walletaccount_notes->getWalletAccountNotes($note_type, $transactionId);
				$notes    = $this->formatPaymentNotes($notesArr);
				$this->returnJsonResult(array('success' => true, 'notes' => $notes, 'notesArr' => $notesArr, 'ntype' => $note_type));
			}elseif($note_type == Walletaccount_notes::INTERNAL_NOTE){
				$lastId   = $this->walletaccount_notes->add($note, $adminUserId, $note_type, $transactionId, $stage_name);
				$notesArr = $this->walletaccount_notes->getWalletAccountNotes($note_type, $transactionId);
				$notes    = $this->formatPaymentNotes($notesArr, true);
				$this->returnJsonResult(array('success' => true, 'notes' => $notes, 'notesArr' => $notesArr, 'ntype' => $note_type));
			}elseif($note_type == Walletaccount_notes::EXTERNAL_NOTE){
				$lastId   = $this->walletaccount_notes->add($note, $adminUserId, $note_type, $transactionId, $stage_name);
				$notesArr = $this->walletaccount_notes->getWalletAccountNotes($note_type, $transactionId);
				$notes    = $this->formatPaymentNotes($notesArr, true);
				$this->returnJsonResult(array('success' => true, 'notes' => $notes, 'notesArr' => $notesArr, 'ntype' => $note_type));
			}else{
				$this->returnJsonResult(array('success'=>false));
			}

		}else{
			$this->returnJsonResult(array('success'=>false));
		}
	}

	public function getStageName($status){
		$setting = $this->operatorglobalsettings->getCustomWithdrawalProcessingStage();
		$stage_name = '';
		switch($status){
			case 'request':
				$stage_name = lang('pay.penreq');
				break;
			case 'pending_review':
				$stage_name = lang('pay.penreview');
				break;
			case 'payProc':
				$stage_name = lang('pay.processing');
				break;
			case 'paid':
				$stage_name = lang('pay.paid');
				break;
			case 'declined':
				$stage_name = lang('pay.decreq');
				break;
			case 'lock_api_unknown':
				$stage_name = lang('pay.lockapiunknownreq');
				break;
			case '3':
				$stage_name = lang('deposit_list.pending_requests_total');
				break;
			case substr($status,0, 2) == 'CS':
				for($i = 0; $i < CUSTOM_WITHDRAWAL_PROCESSING_STAGES; $i++) {
					if($status == "CS$i") {
						$stage_name = lang($setting[$i]['name']);
					}
				}
				break;
			default:
				break;
		}
		return $stage_name;
	}

	public function getWithdrawalDetialNotes($walletAccountId, $note_type){
		if(isset($walletAccountId)){
			$this->load->model(array('wallet_model','walletaccount_notes'));
			$getWallectAccountInfo = $this->wallet_model->getWalletAccountInfoById($walletAccountId);
			$transactionCode = $getWallectAccountInfo['transactionCode'];

			if($note_type == Walletaccount_notes::INTERNAL_NOTE){
				$noteSubTitle = lang('Internal Note');
			}elseif ($note_type == Walletaccount_notes::EXTERNAL_NOTE){
				$noteSubTitle = lang('External Note');
			}
			$allNotes = $this->walletaccount_notes->getWalletAccountNotes($note_type, $walletAccountId);
			$formatNotes = $this->formatPaymentNotes($allNotes, true);

			$this->returnJsonResult(array('success' => true, 'formatNotes' => $formatNotes, 'transactionCode' => $transactionCode, 'noteSubTitle' => $noteSubTitle));
		}else{
			$this->returnJsonResult(array('success'=>false));
		}
	}

	public function getDepositDetialNotes($saleOrderId, $note_type){
		if(isset($saleOrderId)){
			$this->load->model(array('sale_orders_notes','sale_order'));
			$getSaleOrderInfo = $this->sale_order->getSaleOrderById($saleOrderId);
			$secure_id = $getSaleOrderInfo->secure_id;

			if($note_type == Sale_orders_notes::INTERNAL_NOTE){
				$noteSubTitle = lang('Internal Note');
			}elseif ($note_type == Sale_orders_notes::EXTERNAL_NOTE){
				$noteSubTitle = lang('External Note');
			}

			$allNotes = $this->sale_orders_notes->getBySaleOrdersNotes($note_type, $saleOrderId);
			$formatNotes = $this->formatPaymentNotes($allNotes);
			$this->returnJsonResult(array('success' => true, 'formatNotes' => $formatNotes, 'secure_id' => $secure_id, 'noteSubTitle' => $noteSubTitle));
		}else{
			$this->returnJsonResult(array('success'=>false));
		}
	}

	public function checkWithdrawStatus($walletAccountId, $withdrawApi) {
		$result['success'] = false;
		$this->load->model('withdraw_condition');
		$transactionDetail = $this->withdraw_condition->getWithdrawalTransactionDetail($walletAccountId);

		if (count($transactionDetail) < 1) {
			$this->utils->error_log("No transaction detail found for walletAccountId [$walletAccountId]");
			$result['message'] = lang('error.withdrawal_failed');
			$this->returnJsonResult($result);
		}

		# Check status using API function
		list($loaded, $apiClassName) = $this->utils->loadExternalSystemLib($withdrawApi);
		if ($loaded) {
			$this->utils->debug_log('Checking withdraw status for ', $transactionDetail[0]['transactionCode']);
			$paymentResult = $this->$apiClassName->checkWithdrawStatus($transactionDetail[0]['transactionCode']);

			# Definition of $paymentResult can be found in abstract_payment_api
			$result = $paymentResult;
		} else {
			$this->utils->debug_log("Load API [$withdrawApi] for withdrawal failed, payment is not done through API.");
			$result['message'] = lang('error.withdrawal_failed');
		}
		$this->returnJsonResult($result);
	}

    public function checkDepositStatus($saleOrderId , $system_id, $data = null){
		$this->load->model('sale_orders_notes');
	    $deposit_details = $this->get_deposit_detail($saleOrderId, false);
        $secure_id = $deposit_details->secure_id;
        $result['success'] = false;

        # Check status using API function
        list($loaded, $apiClassName) = $this->utils->loadExternalSystemLib($system_id);
        if ($loaded) {
            $this->utils->debug_log('Checking deposit status for ', $system_id);
            if(!is_null($data)){
            	$paymentResult = $this->$apiClassName->checkDepositStatus($secure_id, $data);
            }
            else{
            	$paymentResult = $this->$apiClassName->checkDepositStatus($secure_id);
            }

            # Definition of $paymentResult can be found in abstract_payment_api
            $result = $paymentResult;
            #add notes to sale_orders_notes action log content
            if(isset($result)){
				$this->sale_orders_notes->add($result['message'], Users::SUPER_ADMIN_ID, Sale_orders_notes::ACTION_LOG, $saleOrderId);
			}
        } else {
            $this->utils->debug_log("Load API [$system_id] for deposit failed, payment is not done through API.");
        }

        $this->returnJsonResult($result);
    }

	/**
	 * detail: export adjustment history report
	 *
	 * @return excel format
	 */
	public function exportAdjustmentHistoryToExcel() {
		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'Payment Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Exported Adjustment History',
			'description' => "User " . $this->authentication->getUsername() . " exported adjustment history.",
			'logDate' => date("Y-m-d H:i:s"),
			'status' => '0',
		);
		$this->report_functions->recordAction($data);

		$result = $this->payment_manager->exportAdjustmentHistoryToExcel();
		$this->excel->to_excel($result, 'adjustmenthistorylist-excel');
	}

	/**
	 * detail: View Player Balance Details
	 *
	 * @param int $playerId player id
	 * @return json
	 */
	public function getPlayerBalanceDetails($playerId) {
		echo json_encode($this->payment_manager->getPlayerBalanceDetails($playerId));
	}

	/**
	 * detail: View Player Transaction Log
	 *
	 * @param int $playerId player id
	 * @return json
	 */
	public function getPlayerTransactionLog($playerId) {
		echo json_encode($this->payment_manager->getPlayerTransactionLog($playerId));
	}

	/**
	 * detail: Set Player Balance
	 *
	 * @param int $playerId player id
	 * @param float $currentBalAmount
	 * @param float $newBalAmount
	 * @return	Boolean
	 */
	public function setPlayerNewBalAmount($playerId, $currentBalAmount, $newBalAmount) {
		$player = $this->player_manager->getPlayerById($playerId);

		$playerAcctdata = array('totalBalanceAmount' => $newBalAmount);

		if ($this->payment_manager->setPlayerNewBalAmount($playerId, $playerAcctdata)) {
			echo json_encode($this->addPlayerBalAdjustmentHistory($playerAccountId, $currentBalAmount, $newBalAmount));
		}

		$this->saveAction(self::ACTION_LOG_TITLE, 'Adjust Balance', "User " . $this->authentication->getUsername() . " has adjusted balance of player " . $player['username'] . ".");
	}

	/**
	 * addPlayerBalAdjustmentHistory
	 *
	 * @param int $playerId player id
	 * @param float $currentBalAmount
	 * @param float $newBalAmount
	 * @return string
	 */
	public function addPlayerBalAdjustmentHistory($playerAcctId, $currentBalAmount, $newBalAmount) {
		$playerAcctdata = array('playerAccountId' => $playerAcctId,
			'currentBalanceAmount' => $currentBalAmount,
			'totalBalanceAmount' => $newBalAmount,
			'setOn' => date("Y-m-d H:i:s"),
			'setBy' => $this->authentication->getUserId());

		if ($this->payment_manager->addPlayerBalAdjustmentHistory($playerAcctdata)) {
			return 'success';
		}
	}

	/**
	 * detail: Get Ranking List
	 *
	 * @return json
	 */
	public function getRankingList() {
		echo json_encode($this->payment_manager->getRankingList());
	}

	/**
	 * detail: Get player transaction history
	 *
	 * @return json
	 */
	public function getPlayerTransactionHistory($playerAccountId) {
		echo json_encode($this->payment_manager->getPlayerTransactionHistory($playerAccountId));
	}

	//--------------------------------------------------------------------------------------------------- bank card management

	/**
	 * detail: Get Ranking Level Setting Details
	 *
	 * @return void
	 */
	public function actionPaymentMethod() {
		$this->form_validation->set_rules('bankName', 'Bank Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('accountNumber', 'Account Number', 'trim|required|xss_clean');
		$this->form_validation->set_rules('accountName', 'Account Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('dailyMaxDepositAmount', 'Daily Max Deposit Amount', 'trim|required|xss_clean|numeric');
		$this->form_validation->set_rules('description', 'Description', 'trim|required|xss_clean');
		$this->form_validation->set_rules('processingTime', 'Processing Time', 'trim|required|xss_clean');

		if ($this->form_validation->run() == false) {
			$this->viewBankManagement();
		} else {

			$bankName = $this->input->post('bankName');
			$accountNumber = $this->input->post('accountNumber');
			$accountName = $this->input->post('accountName');
			$dailyMaxDepositAmount = $this->input->post('dailyMaxDepositAmount');
			$description = $this->input->post('description');
			$processingTime = $this->input->post('processingTime');
			$otcPaymentMethodId = $this->input->post('otcPaymentMethodId');
			$today = date("Y-m-d H:i:s");

			if ($otcPaymentMethodId) {

				$data = array(
					'bankName' => $bankName,
					'accountNumber' => $accountNumber,
					'accountName' => $accountName,
					'dailyMaxDepositAmount' => $dailyMaxDepositAmount,
					'description' => $description,
					'processingTime' => $processingTime,
					'updatedOn' => $today,
					'status' => 0,
				);

				$this->payment_manager->editPaymentMethod($data, $otcPaymentMethodId);
				$message = lang('con.pym10') . " <b>" . $bankName . "</b> " . lang('con.pym11');
				$this->saveAction(self::ACTION_LOG_TITLE, 'Edit Payment Method', "User " . $this->authentication->getUsername() . " has edited payment method " . $bankName . ".");
			} else {

				$data = array(
					'bankName' => $bankName,
					'accountNumber' => $accountNumber,
					'accountName' => $accountName,
					'dailyMaxDepositAmount' => $dailyMaxDepositAmount,
					'description' => $description,
					'processingTime' => $processingTime,
					'createdOn' => $today,
					'updatedOn' => $today,
					'status' => 0,
				);

				$this->payment_manager->insertPaymentMethod($data);
				$message = lang('con.pym10') . " <b>" . $bankName . "</b> " . lang('con.pym12');
				$this->saveAction(self::ACTION_LOG_TITLE, 'Add Payment Method', "User " . $this->authentication->getUsername() . " has added new payment method " . $bankName . ".");
			}

			$this->alertMessage(1, $message);
			redirect('payment_management/viewBankManagement');
		}
	}

	/**
	 * detail: Get Ranking Level Setting Details
	 *
	 * @param int $otcPaymentMethodId OTC Payment method
	 * @return void
	 */
	public function changeStatusPaymentMethod($otcPaymentMethodId) {
		$payment_method = $this->payment_manager->getOTCPaymentMethodDetails($otcPaymentMethodId);

		$data = array(
			'status' => $payment_method[0]['status'] == 0 ? '1' : '0',
		);

		$this->payment_manager->changeStatusPaymentMethod($data, $otcPaymentMethodId);

		$status = $payment_method[0]['status'] == 0 ? 'Locked' : 'Normal';

		$message = lang('con.pym10') . " <b>" . $payment_method[0]['bankName'] . "</b>" . lang('con.pym13') . " " . $status . "";
		$this->alertMessage(1, $message);

		$this->saveAction(self::ACTION_LOG_TITLE, 'Lock/Unlock Payment Method', "User " . $this->authentication->getUsername() . " has " . $status . " payment method " . $payment_method[0]['bankName'] . ".");

		redirect('payment_management/viewBankManagement');
	}

	/**
	 * detail: Get Ranking Level Setting Details
	 *
	 * @param int $otcPaymentMethodId OTC Payment method
	 * @return void
	 */
	public function deletePaymentMethod($otcPaymentMethodId) {
		$payment_method = $this->payment_manager->getOTCPaymentMethodDetails($otcPaymentMethodId);

		$this->payment_manager->deletePaymentMethod($otcPaymentMethodId);

		$message = lang('con.pym10') . " <b>" . $payment_method[0]['bankName'] . "</b> " . lang('con.pym14');
		$this->alertMessage(1, $message);

		$this->saveAction(self::ACTION_LOG_TITLE, 'Delete Payment Method', "User " . $this->authentication->getUsername() . " has delete payment method " . $payment_method[0]['bankName'] . ".");

		redirect('payment_management/viewBankManagement');
	}

	//------------------------------------------------------------------------------------------------- player promo

	/**
	 *
	 * detail: withdraw balance from a certain player account
	 *
	 * @param int $player_account_from player account id
	 * @param float $amount
	 * @return void
	 *
	 */
	public function withdrawFromPlayerAccount($player_account_from, $amount) {
		$balance = $this->payment_manager->getBalanceByPlayerAccountId($player_account_from);

		$data = array(
			'totalBalanceAmount' => $balance - $amount,
		);

		$this->payment_manager->setPlayerNewBalAmountByPlayerAccountId($player_account_from, $data);
	}

	/**
	 *
	 * detail: withdraw balance to a certain player account
	 *
	 * @param int $player_account_to player account id
	 * @param float $amount
	 * @return void
	 *
	 */
	public function depositFromPlayerAccount($player_account_to, $amount) {
		$balance = $this->payment_manager->getBalanceByPlayerAccountId($player_account_to);

		$data = array(
			'totalBalanceAmount' => $balance + $amount,
		);

		$this->payment_manager->setPlayerNewBalAmountByPlayerAccountId($player_account_to, $data);
	}

	/**
	 * overview: Previous Balances Checking Setting
	 *
	 * @return load template
	 */
	public function previousBalanceSetting($from = 'system') {
		if (!$this->permissions->checkPermissions('previous_balances_checking_setting')) {
			$this->error_access($from);
		} else {

			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			$data['previousBalanceSetting'] = $this->payment_manager->getOperatorGlobalSetting('previous_balance_set_amount');
			$subwalletList = [];
			$apis = $this->utils->getActiveGameSystemList();
			foreach ($apis as $api) {
				$subwalletList[$api['id']] = [
					"id" => $api['id'],
					"label" => $api['system_code'],
					"value" => $data['previousBalanceSetting'][0]['value'],
				];
			}
			$data['clear_withdraw_cond_by_subwallet'] = $subwalletList;
			//save back
			$this->operatorglobalsettings->syncSettingJson('clear_withdraw_cond_by_subwallet', $data['clear_withdraw_cond_by_subwallet']);

			$this->loadTemplate(lang('pay.17'), '', '', 'system');
			$this->template->write_view('sidebar', 'system_management/sidebar');
			$this->template->write_view('main_content', 'payment_management/view_previous_balance_setting', $data);
			$this->template->render();
		}
	}

	/**
	 * overview: Save Payment Settings Changes
	 *
	 * @POST $previousBalanceSetAmount
	 * @return	void
	 */
	public function savePreviousBalanceSetting() {
		$previousBalanceSetAmount = $this->input->post('previousBalanceSetAmount');

		$data['value'] = $previousBalanceSetAmount;
		$data['name'] = 'previous_balance_set_amount';
		$this->payment_manager->setOperatorGlobalSetting($data);

		//save by subwallet
		$subwalletList = [];
		$apis = $this->utils->getActiveGameSystemList();
		foreach ($apis as $api) {
			$name = 'clear_withdraw_condition_' . $api['id'];
			$value = $this->input->post($name);
			if (empty($value)) {
				$value = $previousBalanceSetAmount;
			}
			$subwalletList[$api['id']] = [
				"id" => $api['id'],
				"label" => $api['system_code'],
				"value" => $value,
			];
		}
		$this->operatorglobalsettings->syncSettingJson('clear_withdraw_cond_by_subwallet', $subwalletList);

		$this->saveAction(self::ACTION_LOG_TITLE, 'Update Previous Balances Checking Setting', "User " . $this->authentication->getUsername() . " has successfully update previous balance cheking setting.");

		$message = lang('pay.17') . ' ' . lang('con.pym11');
		$this->alertMessage(1, $message);
		redirect('payment_management/previousBalanceSetting');
	}

	/**
	 * overview: Non-promo Withdraw Setting
	 *
	 * detail: display the page for Non-promo Withdraw Setting
	 *
	 * @return load template
	 */
	public function nonPromoWithdrawSetting($from = 'system') {
		if (!$this->permissions->checkPermissions('nonpromo_withdraw_setting')) {
			$this->error_access($from);
		} else {

			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			$data['times'] = $this->payment_manager->getOperatorGlobalSetting('non_promo_withdraw_setting');

			$this->loadTemplate(lang('pay.18'), '', '', 'system');
			$this->template->write_view('sidebar', 'system_management/sidebar');
			$this->template->write_view('main_content', 'payment_management/view_nonpromo_withdraw_setting', $data);
			$this->template->render();
		}
	}

	/**
	 * overview: Non-promo Withdraw Setting Changes
	 *
	 * detail: saving data for non promo withdraw setting
	 *
	 * @POST $time
	 * @return	void
	 */
	public function saveNonPromoWithdrawSetting() {
		$times = $this->input->post('times');

		$data['value'] = $times;
		$data['name'] = 'non_promo_withdraw_setting';
		$this->payment_manager->setOperatorGlobalSetting($data);

		$this->saveAction(self::ACTION_LOG_TITLE, 'Update Non-promo Withdraw Setting', "User " . $this->authentication->getUsername() . " has successfully update non-promo withdraw setting.");

		$message = lang('pay.18') . ' ' . lang('con.pym11');
		$this->alertMessage(1, $message);
		redirect('payment_management/nonPromoWithdrawSetting');
	}

	/**
	 *
	 * @param int $bankAccountId
	 * @return void
	 */
	function calculateBankDeposit($bankAccountId) {
		$this->payment_manager->calculateBankDeposit($bankAccountId);
	}

	public function refreshBalanceInfo($player_id) {
		$this->player_manager->updateBalances($player_id);
		$data['walletAccount'] = $this->payment->getPlayerCurrentWalletBalance($player_id);
		$this->load->view('payment_management/ajax_player_balance_adjustment', $data);
	}

	/**
	 * detail: view transaction list
	 *
	 * @param string $from
	 * @return  rendered UI
	 */
	public function viewTransactionList($from = 'payment') {
		if (!$this->permissions->checkPermissions('report_transactions')) {
			$this->error_access($from);
		} else {

			$this->load->model(array('payment_account', 'affiliatemodel','common_category','player'));

			$transaction_id = $this->input->get('transaction_id');
			$data['transaction_id'] = $transaction_id;
			$data['promo_category_list'] = $this->promorules->getPromoType();
			if ($this->utils->isEnabledFeature('enable_adjustment_category')){
				$data['adjustment_category_list'] = $this->common_category->getActiveCategoryByType(common_category::CATEGORY_ADJUSTMENT);
			}

			$data['payment_account_list'] = $this->payment_account->getAllPaymentAccountDetails();

			$only_master = false;
			$ordered_by_name = true;
			$data['affiliates'] = $this->affiliatemodel->getAllActivtedAffiliates($only_master, $ordered_by_name);
			$data['form']	= &$form;
			$data['tag_list'] = $this->player->getAllTagsOnly();
			if ($from == 'report') {
				$activenav = 'report';
			} else {
				$activenav = 'payment';
			}

			$this->loadTemplate(lang('role.72'), '', '', $activenav);


			$this->template->add_js('resources/js/select2.min.js');
			$this->template->add_js('resources/js/highlight.pack.js');
			$this->template->add_css('resources/css/hljs.tomorrow.css');
			$this->template->add_js('resources/js/chosen.jquery.min.js');
			$this->template->add_css('resources/css/chosen.min.css');
			$this->template->add_css('resources/css/select2.min.css');
			// $this->template->add_css('resources/css/general/fontawesome/build.css');

			if ($from == 'report') {
				$this->template->write_view('sidebar', 'report_management/sidebar', ['active' => 'view_transaction']);
			} else {
				$this->template->write_view('sidebar', 'payment_management/sidebar', ['active' => 'view_transaction']);
			}

			$data['from'] = $from;

			$this->template->write_view('main_content', 'payment_management/view_transaction_list', $data);
			$this->template->render();
		}
	}

	/**
	 * AJAX
	 * Check if username exist
	 *
	 * @return json
	 */
	public function checkUsernames() {

		$this->load->model('affiliate');
		$this->load->model('users');
		$this->load->model('player');
		$this->load->helper('security');

		$username = xss_clean($this->input->post('username'));
		$userGroup = xss_clean($this->input->post('userGroup'));
		$data = '';
		switch ($userGroup) {
		case "affiliates":
			$data = $this->affiliate->checkUsernameIfExist($username);
			break;
		case "adminusers":
			$data = $this->users->checkUsernameIfExist($username);
			break;
		case "player":
			$data = $this->player->checkUsernameIfExist($username);
			break;
		}

		if ($data['isExist']) {
			$arr = array('status' => 'success', 'msg' => '<b>' . $username . '</b>  ' . lang('player.uab09'), 'userdata' => $data);
			echo json_encode($arr);
		} else {
			$data['username'] = $username;
			$arr = array('status' => 'notfound', 'msg' => '<b>' . $username . '</b>  ' . lang('player.uab10'), 'userdata' => $data);
			echo json_encode($arr);
		}
	}

	/**
	 * detail: view transaction list
	 *
	 * @return  rendered UI
	 */
	public function view_adjustment_transaction_list() {
		if (!$this->permissions->checkPermissions('report_transactions')) {
			$this->error_access();
		} else {
			$this->loadTemplate(lang('role.72'), '', '', 'payment');
			// $this->template->add_css('resources/css/general/fontawesome/font-awesome.css');
			$this->template->add_css('resources/css/general/fontawesome/build.css');
			$this->template->write_view('sidebar', 'payment_management/sidebar');
			$this->template->write_view('main_content', 'payment_management/view_adjustment_transaction_list');
			$this->template->render();
		}
	}

	# BANK TYPES ###########################################################################################################################

	/**
	 *
	 * overview: Bank type payment lists
	 *
	 * detail: List of all payment banks
	 *
	 * @return load template
	 *
	 */
	public function bank3rdPaymentList($from = 'system') {
		if (!$this->permissions->checkPermissions('bank/3rd_payment_list')) {
			$this->error_access($from);
		} else {
			$this->load->model(array('banktype', 'users', 'payment_account', 'financial_account_setting'));

			$data['bankTypes'] = $this->banktype->getAllBanktype();
			foreach ($data['bankTypes'] as &$bankType) {
				$bankType['bankName'] = lang($bankType['bankName']);
			}
			$data['payment_type_flags'] = $this->utils->insertEmptyToHeader($this->financial_account_setting->getPaymentTypeAllFlagsKV(), '', lang('select.empty.line'));

			$this->loadTemplate(lang('pay.bt.paneltitle'), '', '', 'system');
			$this->template->write_view('sidebar', 'system_management/sidebar');
			$this->template->write_view('main_content', 'payment_management/view_bank_type_list', $data);
			$this->template->render();
		}
	}

	/**
	 *
	 * detail: adding new bank type form
	 *
	 * @return load template
	 */
	public function newBankType( $playerBankDetailsId = '' ) {
		$this->load->model(['banktype', 'financial_account_setting']);

		if ($bankName = $this->input->post('bankname')) {

			if( ! is_array($bankName) ){
				$bankName = array(
					"1" => $bankName,
					"2" => $bankName,
					"3" => $bankName,
					"4" => $bankName,
					"5" => $bankName
				);
			}

			$bankName = '_json:' . json_encode($bankName);

			$bankCode=$this->input->post('bank_code');
			if(empty($bankCode)){
				$bankCode=null;
			}

			$icon_file_name = null;

			if (!$this->input->post('chkUseDefaultIcon')) {
				if (!empty($_FILES['filBankIcon'])) {
					# upload bank icon
					$upload_response = $this->uploadBankIcon();

					if ($upload_response['status'] == 'success' && isset($upload_response['fileName'])) {
						if (!empty($upload_response['fileName'])) {
							$icon_file_name = $upload_response['fileName'];
						}
					}
				}
			}

			$isBankCodeDuplicate = $this->banktype->checkIfBankCodeExist($bankCode, 'new_id');
			if($isBankCodeDuplicate) {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('pay.bank_code.duplicate'));
				redirect('payment_management/newBankType');
				return;
			}

			$banktypeId = $this->banktype->addBankType(
				$bankName,
				$this->authentication->getUserId(),
				$this->input->post('external_system_id'),
				$bankCode,
				$this->input->post('payment_type_flag'),
				$icon_file_name
			);

			$this->load->model('playerbankdetails');
			if( ! empty( $playerBankDetailsId ) ) $this->playerbankdetails->updateBanktypeId($playerBankDetailsId, $banktypeId);

			if( $this->input->is_ajax_request() ){
				$this->returnJsonResult(array('status' => 'success', 'msg' => $banktypeId));
				return;
			}

			redirect('payment_management/bank3rdPaymentList');
		} else {
			$data['payment_type_flags'] = $this->utils->insertEmptyToHeader($this->financial_account_setting->getPaymentTypeAllFlagsKV(), '', lang('select.empty.line'));
			$this->loadTemplate(lang('pay.bt.paneltitle'), '', '', 'system');
			$this->template->write_view('sidebar', 'system_management/sidebar');
			$this->template->write_view('main_content', 'payment_management/view_bank_type_form', $data);
			$this->template->render();
		}
	}

	/**
	 * detail: update bank type form
	 *
	 * @param int $bankTypeId bank type id
	 * @return load template
	 *
	 */
	public function editBankType($bankTypeId) {
		$this->load->model(['banktype', 'financial_account_setting']);
		if ($bankName = $this->input->post('bankname')) {

			if(!isset($bankName[1]) || empty($bankName[1]) || trim($bankName[1]) == '')
				return redirect('payment_management/bank3rdPaymentList');

			foreach ($bankName as $key => $name) {
				if($key == '1') continue;

				if(empty($name) || trim($name) == '')
					$bankName[$key] = $bankName[1];//Use english language by default
			}

			$bankName = '_json:' . json_encode($bankName);

			$bankCode=$this->input->post('bank_code');
			if(empty($bankCode)){
				$bankCode=null;
			}

			$icon_file_name = null;
			if (!$this->input->post('chkUseDefaultIcon')) {
				if (!empty($_FILES['filBankIcon'])) {
					# upload bank icon
					$upload_response = $this->uploadBankIcon();

					if ($upload_response['status'] == 'success' && isset($upload_response['fileName'])) {
						if (!empty($upload_response['fileName'])) {
							$icon_file_name = $upload_response['fileName'];
						}
					}
				}
			}

			$isBankCodeDuplicate = $this->banktype->checkIfBankCodeExist($bankCode, $this->input->post('bankTypeId'));
			if($isBankCodeDuplicate) {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('pay.bank_code.duplicate'));
				redirect('payment_management/editBankType/'.$this->input->post('bankTypeId'));
				return;
			}

			$this->banktype->updateBankType(
				$this->input->post('bankTypeId'),
				array(
					'bankName' => $bankName,
					'external_system_id' => $this->input->post('external_system_id'),
					'bank_code' => $bankCode,
					'payment_type_flag' => $this->input->post('payment_type_flag'),
					'bankIcon' => $icon_file_name,
				)
			);
			redirect('payment_management/bank3rdPaymentList');
		} else {
			$bankTypeRow = $this->banktype->getBankTypeById($bankTypeId);
			$data['payment_type_flags'] = $this->utils->insertEmptyToHeader($this->financial_account_setting->getPaymentTypeAllFlagsKV(), '', lang('select.empty.line'));

			$data['bankTypeId'] = $bankTypeRow->bankTypeId;
			$data['bankName'] = $bankTypeRow->bankName;
			$data['external_system_id'] = $bankTypeRow->external_system_id;
			$data['bank_code'] = $bankTypeRow->bank_code;
			$data['payment_type_flag'] = $bankTypeRow->payment_type_flag;
			$data['bank_icon'] = $bankTypeRow->bankIcon;

			$this->loadTemplate(lang('pay.bt.paneltitle'), '', '', 'system');
			$this->template->write_view('sidebar', 'system_management/sidebar');
			$this->template->write_view('main_content', 'payment_management/view_bank_type_form', $data);
			$this->template->render();
		}
	}

	/**
	 *
	 * detail: delete bank type
	 *
	 * @param int $bankTypeId bank type id
	 * @return void
	 */
	public function deleteBankType($bankTypeId) {
		$this->alertMessage(2, lang('pay.bt.error.occured'));
		redirect('payment_management/bank3rdPaymentList');
	}

	/**
	 * detail: update the status of the bank type
	 *
	 * @param int $bankTypeId bank type id
	 * @param string $status
	 * @return void
	 */
	public function toggleBanktypeStatus($bankTypeId, $status) {

		$this->load->model(array('banktype', 'payment_account'));

		$payment_account_id = $this->payment_account->getPaymentAccountInfo( $bankTypeId );

		if ($this->banktype->updateBanktype($bankTypeId, array(
			'status' => $status,
		))) {

			//change the status of the collection account for this bank
			if( ! empty($payment_account_id) && urldecode($status) == 'not active' && $payment_account_id->status != BaseModel::STATUS_DISABLED ){
				$this->payment_account->disablePaymentAccount( $payment_account_id->id );
			}elseif( ! empty($payment_account_id) && urldecode($status) == 'active' ){
				$this->payment_account->enablePaymentAccount( $payment_account_id->id );
			}
			//end

			$this->alertMessage(1, lang('pay.bt.successfully.saved'));
		} else {
			$this->alertMessage(2, lang('pay.bt.error.occured'));
		}

		redirect('payment_management/bank3rdPaymentList');
	}

	/**
	 *
	 * @param string $type
	 * @param int $bankTypeId bank type id
	 * @param string $status
	 * @return void
	 */
	public function toggleBankType($type, $bankTypeId, $status) {

		$this->load->model('banktype');

		if ($this->banktype->updateBanktype($bankTypeId, array(
			$type => $status,
		))) {
			$this->alertMessage(1, lang('pay.bt.successfully.saved'));
		} else {
			$this->alertMessage(2, lang('pay.bt.error.occured'));
		}

		redirect('payment_management/bank3rdPaymentList');
	}

	# END BANK TYPES ###########################################################################################################################

	# ADJUST BALANCE ###########################################################################################################################

	/**
	 *
	 * detail: display list of all the member balances
	 *
	 * @return load template
	 */
	public function member_balance() {

		$this->load->model('external_system');

		if ($this->permissions->checkPermissions('payment_player_adjustbalance')) {
			$this->loadTemplate(lang('role.72'), '', '', 'payment');

			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			$itemCnt = 0;
			$sort_by = array();
			$sort_by['transactionType'] = 'deposit';
			$sort_by['dwStatus'] = 'request';

			$this->session->set_userdata('dwStatus', $sort_by['dwStatus']);
			if ($this->session->userdata('itemCnt') != null) {
				$itemCnt = $this->session->userdata('itemCnt');
			} else {
				$itemCnt = 10;
			}

			if ($this->session->userdata('playerLevel')) {
				$sort_by['searchVal'] = $this->session->userdata('searchVal');
			}
			if ($this->session->userdata('currency')) {
				$sort_by['currency'] = $this->session->userdata('currency');
			}
			if ($this->session->userdata('orderBy')) {
				$sort_by['orderBy'] = $this->session->userdata('orderBy');
			}
			if ($this->session->userdata('playerLevel')) {
				$sort_by['playerLevel'] = $this->session->userdata('playerLevel');
			}
			if ($this->session->userdata('paymentMethod')) {
				$sort_by['paymentMethod'] = $this->session->userdata('paymentMethod');
			}

			$data['game_platforms'] = $this->external_system->getAllActiveSytemGameApi();
			$data['playerDetails'] = $this->player_model->getPlayersSubWalletBalance(null, $data['game_platforms']);
			$data['transactionType'] = lang('con.pb');
			$data['games'] = $this->payment_manager->getGames();
			$this->template->write_view('sidebar', 'payment_management/sidebar');
			$this->template->write_view('main_content', 'payment_management/balance_adjustment/member_balance', $data);
			$this->template->render();
		} else {
			$this->error_access();
		}
	}

	/**
	 * overview: Adjustment history
	 *
	 * detail: display the balance history
	 *
	 * @param int $player_id player id
	 * @return load template
	 */
	public function adjustment_history($player_id = null) {

		$this->load->model('external_system');

		if ($this->permissions->checkPermissions('report_adjustment_history')) {
			$start_date = $this->input->get('start_date');
			$end_date = $this->input->get('end_date');
			$limit = $this->input->get('items_per_page');
			$game_platforms = $this->external_system->getAllActiveSytemGameApi();
			$data['player_id'] = $player_id;
			$data['game_platforms'][] = array(
				'system_code' => 'Main',
			);
			foreach ($game_platforms as $game_platform) {
				$data['game_platforms'][$game_platform['id']] = $game_platform;
			}

			$data['adjustment_history'] = $this->payment->viewAdjustmentHistoryV2($player_id, $start_date, $end_date, $limit);

			$this->loadTemplate(lang('role.72'), '', '', 'payment');
			$this->template->write_view('sidebar', 'payment_management/sidebar');
			$this->template->write_view('main_content', 'payment_management/balance_adjustment/adjustment_history', $data);
			$this->template->render();
		}
	}

	/**
	 * overview: balance adjustment
	 *
	 * detail: display the balance adjustment
	 *
	 * @param int $player_id player id
	 * @return load template
	 */
	public function adjust_balance($playerId) {
		if (!$this->permissions->checkPermissions('payment_player_adjustbalance')) {
			$this->error_access();
		} else {
			$this->load->model(array('external_system', 'game_provider_auth', 'player_model', 'payment'));

			$data['playerDetails'] = $this->player_model->getPlayerDetails($playerId);
			$data['game_platforms'] = $this->game_provider_auth->getGamePlatforms($playerId);
			$data['walletAccounts'] = $this->player_model->getPlayersSubWalletBalance($playerId);
			$data['player_id'] = $playerId;

			$bigWallet = $this->utils->getBigWalletByPlayerId($playerId);
			$data['walletAccounts']['total'] = $bigWallet['total_nofrozen'];
			$game_platforms = $data['game_platforms'];
			if(!empty($game_platforms)){
				foreach ($game_platforms as $key => $value) {
					$api = $this->utils->loadExternalSystemLibObject($value['id']);
					$data['game_platforms'][$key]['is_seamless'] = $api->isSeamLessGame();
				}

			}
			$this->loadTemplate(lang('Adjust Balance'), '', '', 'payment');
			$this->template->write_view('sidebar', 'payment_management/sidebar');
			$this->template->write_view('main_content', 'payment_management/balance_adjustment/adjust_balance', $data);
			$this->template->render();
		}
	}

	/**
	 *
	 * @param int $player_id player id
	 * @return load template
	 */
	public function add_withdraw_condition($player_id = null) {
		$this->load->model(array('promorules', 'external_system'));
		$data = array(
			'platform_id' => '0',
			'platform_name' => lang('pay.mainwallt'),
			'player_id' => $player_id,
		);

		$data['promoCmsSettings'] = $this->promorules->getAvailablePromoCMSList();
		$userId=$this->authentication->getUserId();
		$data['double_submit_hidden_field']=$this->initDoubleSubmitAndReturnHiddenFieldForAdmin($userId);

		$this->session->set_flashdata('prevent_refresh', true);

		$this->load->view('payment_management/balance_adjustment/add_withdraw_condition_form', $data);
	}

	/**
	 *
	 * detail: add withdraw condition for a certain player
	 *
	 * @param int $player_id player id
	 * @return void
	 */
	public function add_withdraw_condition_post($player_id) {
		$this->load->model(array('transactions', 'wallet_model', 'withdraw_condition', 'player_promo', 'promorules'));

		$controller = $this;
		$adminId = $this->authentication->getUserId();

		if(!$this->verifyAndResetDoubleSubmitForAdmin($adminId)){
			$message = lang('Please refresh and try, and donot allow double submit');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			redirect('payment_management/adjust_balance/' . $player_id);
			return;
		}

		$success = $this->lockAndTrans(Utils::LOCK_ACTION_WITHDRAW_CONDITION, $player_id, function ()
			use ($controller, $player_id, $adminId) {

				# GET REQUIRED PARAMETERS FROM REQUEST
				$amount                 = $controller->input->post('amount');
				$depositAmtCondition    = $controller->input->post('depositAmtCondition');
				$betTimes               = $controller->input->post('betTimes');
				$reason                 = $controller->input->post('reason');
				$show_in_front_end      = $controller->input->post('show_in_front_end');
				$promoCmsSettingId      = $controller->input->post('promoCmsSettingId');
				$adjustWithdrawCondType = $controller->input->post('adjustWithdrawCondType');
				$make_up_date           = $controller->input->post('make_up_date');

				if ($promoCmsSettingId) {
					list($promorule, $promoCmsSettingId) = $controller->promorules->getByCmsPromoCodeOrId($promoCmsSettingId);
					$promoRuleId = $promorule['promorulesId'];

					$controller->utils->debug_log('promoRuleId', $promoRuleId, 'show_in_front_end', $show_in_front_end, 'amount', $amount, 'reason', $reason);
				}

				$success = true;
				$isDeposit = !empty($depositAmtCondition);
				$bonusTransId = 0;
				$depositTransId = 0;
				$adjustWithdrawCondType = $controller->input->post('adjustWithdrawCondType');
				if ($adjustWithdrawCondType == Payment_management::WITHDRAW_CONDITION_ONLY_DEPOSIT) {
					//Only Calculate Deposit
					$withdrawBetAmtCondition = doubleval($depositAmtCondition) * doubleval($betTimes);
				} elseif ($adjustWithdrawCondType == Payment_management::WITHDRAW_CONDITION_DEPOSIT_BONUS_BET_TIMES_DEDUCT_DEPOSIT) {
					//deduct deposit amount
					$withdrawBetAmtCondition = (doubleval($depositAmtCondition) + doubleval($amount)) * doubleval($betTimes) - doubleval($depositAmtCondition);
				} elseif ($adjustWithdrawCondType == Payment_management::WITHDRAW_CONDITION_NONE) {
					$withdrawBetAmtCondition = doubleval($amount) * doubleval($betTimes);
				} else {
					//normal
					$withdrawBetAmtCondition = (doubleval($depositAmtCondition) + doubleval($amount)) * doubleval($betTimes);
				}
				// $withdrawBetAmtCondition=;

				$this->utils->debug_log('adjust withdraw_conditions player_id', $player_id, 'withdrawBetAmtCondition', $withdrawBetAmtCondition);

				if ($promoCmsSettingId) {
					//make playerpromo
					$playerpromoId = null;
					$playerpromoId = $controller->player_promo->approvePromoToPlayerWithouRelease($player_id, $promoRuleId, $amount,
							$promoCmsSettingId, $adminId, $playerpromoId, $withdrawBetAmtCondition);

					//make withdraw condition
					$controller->withdraw_condition->createWithdrawConditionForPromoruleBonus($isDeposit, $player_id, $bonusTransId,
							$withdrawBetAmtCondition, $depositAmtCondition, $amount, $betTimes, $promorule,
							$depositTransId, $playerpromoId, $make_up_date, $reason);
				} else {
					// cashback withdraw condition (no promo)
					if ($adjustWithdrawCondType == Payment_management::WITHDRAW_CONDITION_ONLY_DEPOSIT) {
						$controller->withdraw_condition->createWithdrawCondForDepositOnly($bonusTransId, $withdrawBetAmtCondition, $betTimes, $player_id, $depositAmtCondition);
					}

					if ($adjustWithdrawCondType == Payment_management::WITHDRAW_CONDITION_NONE) {
						$controller->withdraw_condition->createWithdrawConditionForCashback($bonusTransId, $withdrawBetAmtCondition, $betTimes, $player_id, $amount);
					}
				}

				return $success;
			}
		);

		$rlt = array('success' => $success);

		if (!$rlt['success']) {
			if (isset($rlt['message']) && !empty($rlt['message'])) {
				$message = $rlt['message'];
			} else {
				$message = lang('notify.61');
			}
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('con.pym09'));
		}

		redirect('payment_management/adjust_balance/' . $player_id);
	}

	public function adjust_seamless_balance_form($platform_id, $transaction_type, $player_id){
		if (!$this->permissions->checkPermissions('adjust_manually_seamless_wallet')) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('con.pym01'));
			redirect('payment_management/adjust_balance/' . $player_id);
			return;
		}
		$this->load->model(array('promorules', 'external_system', 'common_category'));
		$platform_name = $platform_id == '0' ? lang('pay.mainwallt') : $this->external_system->getNameById($platform_id) . ' ' . lang('cashier.42');
		$data = array(
			'platform_id' => $platform_id,
			'platform_name' => $platform_name,
			'transaction_type' => $transaction_type,
			'player_id' => $player_id,
		);

		$userId=$this->authentication->getUserId();
		$data['double_submit_hidden_field']=$this->initDoubleSubmitAndReturnHiddenFieldForAdmin($userId);
		$this->session->set_flashdata('prevent_refresh', true);
		$this->load->view('payment_management/balance_adjustment/adjust_seamless_balance_form', $data);
	}

	/**
	 *
	 * detail: view adjust balance form
	 *
	 * @param int $platform_id platform_id
	 * @param string $transation_type
	 * @param int $player_id player id
	 * @return load template
	 */
	public function adjust_balance_form($platform_id, $transaction_type, $player_id = null, $is_own_page = 'false') {
		$this->load->model(array('promorules', 'external_system', 'common_category'));
		$platform_name = $platform_id == '0' ? lang('pay.mainwallt') : $this->external_system->getNameById($platform_id) . ' ' . lang('cashier.42');
		$data = array(
			'platform_id' => $platform_id,
			'platform_name' => $platform_name,
			'transaction_type' => $transaction_type,
			'player_id' => $player_id,
		);
		$data['promoCategory'] = $this->promorules->getAllPromoCategory();
		$data['promoRules'] = $this->promorules->getAvailablePromoruleList();
		$data['manual_subtract_balance_tags'] = $this->player_manager->getAllManualSubtractBalanceTags();
		if($this->utils->isEnabledFeature('enable_adjustment_category')){
			$data['adjustmentCategory'] = $this->common_category->getActiveCategoryByType(common_category::CATEGORY_ADJUSTMENT);
		}

		if($this->utils->isEnabledFeature('only_manually_add_active_promotion')){
			$data['promoCms'] = $this->promorules->getAvailablePromoCMSList();
		}else{
			$data['promoCms'] = $this->promorules->getAllPromoCMSList();
		}

		$userId=$this->authentication->getUserId();
		$data['double_submit_hidden_field']=$this->initDoubleSubmitAndReturnHiddenFieldForAdmin($userId);

		$this->session->set_flashdata('prevent_refresh', true);
		//move to marketing_management

		$data['is_own_page'] = $is_own_page ;
		$this->load->view('payment_management/balance_adjustment/adjust_balance_form', $data);
	}

	public function adjust_seamless_balance_post($game_platform_id, $transaction_type, $player_id){
		if (!$this->permissions->checkPermissions('adjust_manually_seamless_wallet')) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('con.pym01'));
			redirect('payment_management/adjust_balance/' . $player_id);
			return;
		}
		$userId=$this->authentication->getUserId();

		if(!$this->verifyAndResetDoubleSubmitForAdmin($userId)){
			$message = lang('Please refresh and try, and donot allow double submit');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			redirect('payment_management/adjust_balance/' . $player_id);
			return;
		}

		$amount = (float)$this->input->post('amount');
		$reason = $this->input->post('reason');
		$success = false;
		$controller = $this;
		$beforeBalance = $this->wallet_model->getBalanceDetails($player_id);
		$totalBalance = $beforeBalance;
		$message = lang('notify.61');

		if($amount > 0){
			if($transaction_type == Transactions::MANUAL_SUBTRACT_SEAMLESS_BALANCE){
				$success = $this->lockAndTransForPlayerBalance($player_id, function() use($controller, $player_id, $amount, $game_platform_id,&$totalBalance, &$message) {
					$wallet_balance = $controller->CI->player_model->getPlayerSubWalletBalance($player_id, $game_platform_id);
					if($this->utils->compareResultFloat($wallet_balance, '<', $amount)){
						$message = lang('Do not have enough available balance');
						return false;
					} else {
						$result = $controller->wallet_model->decSubWallet($player_id, $game_platform_id, $amount);
						$totalBalance = $this->wallet_model->getBalanceDetails($player_id);
						return $result;
					}

				});
			} elseif($transaction_type == Transactions::MANUAL_ADD_SEAMLESS_BALANCE) {
				$success = $this->lockAndTransForPlayerBalance($player_id, function() use($controller, $player_id, $amount, $game_platform_id, &$totalBalance) {
						$result = $controller->wallet_model->incSubWallet($player_id, $game_platform_id, $amount);
						$totalBalance = $this->wallet_model->getBalanceDetails($player_id);
						return $result;
					});
			}
		}


		if($success){
			$this->payment_manager->addPlayerBalAdjustmentHistory(array(
				'playerId' => $player_id,
				'adjustmentType' => $transaction_type,
				'walletType' => $game_platform_id,
				'amountChanged' => $amount,
				'oldBalance' => $beforeBalance && is_array($beforeBalance) ? $beforeBalance['total_balance'] : null,
				'newBalance' => $totalBalance && is_array($totalBalance) ? $totalBalance['total_balance'] : null,
				'reason' => $reason,
				'adjustedOn' => $this->utils->getDatetimeNow(),
				'adjustedBy' => $userId,
				'show_flag' => false,
			));

			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('con.pym09'));
		}
		else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		}
		redirect('payment_management/adjust_balance/' . $player_id);
	}

	/**
	 *
	 * detail: save/update the balance for a certain player
	 *
	 * @POST $amount
	 * @POST $reason
	 * @POST $show_in_front_end
	 * @param int $gamePlatformId game_platform_id
	 * @param string $transaction_type
	 * @param int $player_id player id
	 * @return void
	 */
	public function adjust_balance_post($gamePlatformId, $transaction_type, $player_id, $is_own_page = 'false') {
		$userId=$this->authentication->getUserId();

		if(!$this->verifyAndResetDoubleSubmitForAdmin($userId)){
			$message = lang('Please refresh and try, and donot allow double submit');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			redirect('payment_management/adjust_balance/' . $player_id);
			return;
		}

		//standalone page
		$is_own_page= $is_own_page=='true';

		# GET PARAMETERS FROM SYSTEM
		$current_timestamp = $this->utils->getNowForMysql();
		$user_id = $this->authentication->getUserId();
		$this->load->model(array('transactions', 'wallet_model', 'response_result','player_model'));
		# GET REQUIRED PARAMETERS FROM REQUEST
		$manual_subtract_balance_tag_id = $this->input->post('manual_subtract_balance_tag_id');
		$amount = $this->input->post('amount');
		$_reason = $this->input->post('reason');

		if(strlen($_reason)>120) {
            $message = lang('Maximum 120 characters (including spaces).');
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            redirect('payment_management/adjust_balance/' . $player_id);
            return;
		}
		$reason = htmlentities($_reason, ENT_QUOTES);

		$show_in_front_end = $this->input->post('show_in_front_end');
		$adjustment_category = null;
		if($this->utils->isEnabledFeature('enable_adjustment_category')){
			$adjustment_category = $this->input->post('adjustment_category_id');
		}

		$promo_category = null;
		$promoRuleId=null;
		$promoCmsSettingId = $this->input->post('promo_cms_id') ;
		if(!empty($promoCmsSettingId)){

			$promoRuleId  = $this->promorules->getPromorulesIdByPromoCmsId($promoCmsSettingId);

	    	if (!empty($promoRuleId)) {
				$promorule = $this->promorules->getPromoRuleRow($promoRuleId);
				$promo_category = $promorule['promoCategory'];
			}

		}

		$isTransfer = $transaction_type == Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET ||
		$transaction_type == Transactions::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET;
		$make_up_only = $this->input->post('make_up_only');

		$this->utils->debug_log('isTransfer', $isTransfer, 'transaction_type', $transaction_type, 'make_up_only', $make_up_only, 'make_up_transfer_record', $this->permissions->checkPermissions('make_up_transfer_record'));

		if ($isTransfer && $make_up_only == 'true' && $this->permissions->checkPermissions('make_up_transfer_record')) {
			//fix balance
			// $this->startTrans();

			// $really_fix_balance = $this->input->post('really_fix_balance') == 'true';
			// $playerAccount = $this->wallet_model->getPlayerWalletByType($player_id, $gamePlatformId);
			// $wallet_id = $playerAccount->playerAccountId;

			// $player_name = $this->player_model->getUsernameById($player_id);
			// $note = 'make up transfer record subwallet:' . $wallet_id . ', amount:' . $amount . ', player id:' . $player_id . '. reason:' . $reason;
			// $transId = $this->transactions->makeUpTransferTransaction($player_id, $transaction_type, $wallet_id, $gamePlatformId,
			// 	$amount, $note, $really_fix_balance, $this->input->post('make_up_date'),null,$adjustment_category);

			// $success = !empty($transId);

			// if ($success) {
			// 	if($this->utils->isEnabledFeature('auto_add_reason_in_adjustment_main_wallet_to_player_notes')){
			// 		$this->player_model->addPlayerNote($player_id, $user_id, $reason);
			// 	}
			// 	//call api to
			// 	if ($really_fix_balance && $transaction_type == Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET) {
			// 		$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
			// 		$apiResult = $api->depositToGame($player_name, $amount);
			// 		$success = $apiResult['success'];
			// 	}
			// }

			// if ($success) {
			// 	$success = $this->endTransWithSucc();
			// } else {
			// 	//rollback
			// 	$this->rollbackTrans();
			// }
			// $rlt = array('success' => $success);
			$rlt = array('success' => false);
		} else {
			$rlt = $this->_commonBalanceAdjusmentLogic($player_id, $gamePlatformId, $transaction_type,
				$amount, $user_id, $reason, $promo_category, $show_in_front_end, $promoRuleId, $promoCmsSettingId, $manual_subtract_balance_tag_id,$adjustment_category);
		}

		if(!isset($rlt['response_result_id']) && $gamePlatformId!='0'){
			$this->utils->error_log('lost response_result_id on game platform', $gamePlatformId);
		}

		if(isset($rlt['reason_id']) && !empty($rlt['reason_id'])){
			$abstractApi=$this->utils->loadAnyGameApiObject();
			$message='API: '.$abstractApi->translateReasonId($rlt['reason_id']);
		}else{
			$responseResData = $this->response_result->getResponseResultById(@$rlt['response_result_id']);
			if (!empty($responseResData)) {
				$message = $responseResData->status_text;
			}
		}

		if (!$rlt['success']) {
			if (isset($rlt['message']) && !empty($rlt['message'])) {
				$message = $rlt['message'];
			} else {
				$message = lang('notify.61');
			}
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('con.pym09'));
		}

		redirect('payment_management/adjust_balance/' . $player_id);
	}

	/**
	 *
	 * @param int $gamePlatformId game_platform_id
	 * @param string $transaction_type
	 * @return load template
	 */
	public function doBatchBalanceAdjustment($gamePlatformId, $transaction_type) {
		$this->load->model('player_model');

		if ($this->session->flashdata('prevent_refresh') == null) {
			redirect('/marketing_management/batchBalanceAdjustment');
		}

		# GET PARAMETERS FROM SYSTEM
		$current_timestamp = $this->utils->getNowForMysql();
		$user_id = $this->authentication->getUserId();

		# GET REQUIRED PARAMETERS FROM REQUEST
		$amount = $this->input->post('amount');
		$_reason = $this->input->post('reason');
        $reason = htmlentities($_reason, ENT_QUOTES);

		$promo_category = null;
		$promoRuleId = $this->input->post('promoRuleId');
		$show_in_front_end = $this->input->post('show_in_front_end');

		$text = file_get_contents($_FILES['usernames']['tmp_name']);
		$usernames = explode("\n", $text);
		$usernames = array_filter($usernames);
		$usernames = array_unique($usernames);

		$result = array('failed_count' => 0, 'success' => 0, 'failed' => []);
		$result['total_count'] = count($usernames);
		$success_users = [];

		foreach ($usernames as $username) {

			$username = trim($username);
			$player_id = $this->player_model->getPlayerIdByUsername($username);

			if ($player_id) {
				$this->startTrans();
				$rlt = $this->_commonBalanceAdjusmentLogic($player_id, $gamePlatformId, $transaction_type,
					$amount, $user_id, $reason, $promo_category, $show_in_front_end, $promoRuleId);
				$success = $this->endTransWithSucc();

				if (!$success || !$rlt['success']) {

					if (isset($rlt['message']) && !empty($rlt['message'])) {
						$message = $rlt['message'];
					} else {
						$message = lang('notify.61');
					}

					$result['failed'][$message][] = $username;
					@$result['failed_count']++;

				} else {
					$success_users[] = $username;
				}

			} else {
				$result['failed']['User does not exist'][] = $username;
				@$result['failed_count']++;
			}
		}

		$result['success_users'] = $success_users;

		$this->loadTemplate('Batch Balance Adjustment', '', '', 'payment');
		$this->template->write_view('sidebar', 'payment_management/sidebar');
		$this->template->write_view('main_content', 'payment_management/batch_balance_adjustment_result', $result);
		$this->template->render();
	}

	private function _commonBalanceAdjusmentLogic($player_id, $wallet_type, $adjustment_type,
		$amount, $user_id, $reason, $promo_category = null, $show_in_front_end = null, $promoRuleId = null, $promoCmsSettingId = null, $manual_subtract_balance_tag_id = null,$adjustment_category = null) {
		$this->load->model(array('transactions', 'external_system', 'player_model', 'users', 'player_promo', 'promorules', 'wallet_model', 'transaction_notes'));

		$current_timestamp = $this->utils->getNowForMysql();
		$deposit_amt_condition = $this->input->post('depositAmtCondition') ? $this->input->post('depositAmtCondition') : null;
        $generate_withdrawal_condition = !!$this->input->post('generate_withdrawal_condition');

		$wallet_name = $wallet_type ? $this->external_system->getNameById($wallet_type) . ' Subwallet' : 'Main Wallet';
		$player_name = $this->player_model->getUsernameById($player_id);
		$user_name = $this->users->selectUsersById($user_id)['username'];

		//set promo category
		$promo_category = null;
		if (!empty($promoRuleId)) {
			$promorule = $this->promorules->getPromoRuleRow($promoRuleId);
			$promo_category = $promorule['promoCategory'];
		}

		$result = array('success' => false);

		switch ($adjustment_type) {

		case Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET:
			$from_id = 0; # main wallet
			$to_id = $wallet_type; # wallet id
		case Transactions::AUTO_ADD_CASHBACK_TO_BALANCE:
		case Transactions::MANUAL_ADD_BALANCE:
		case Transactions::ADD_BONUS:
			$action_name = 'Add';
			break;
		case Transactions::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET:
			$from_id = $wallet_type; # wallet id
			$to_id = 0; # main wallet
		case Transactions::SUBTRACT_BONUS:
		case Transactions::MANUAL_SUBTRACT_BALANCE:
			$action_name = 'Subtract';
			break;

		default:
			return array('success' => false);
			break;
		}


		if (!$deposit_amt_condition) {
			$deposit_amount_note = '';
		} else {
			$deposit_amount_note = 'with deposit condition of ' . $deposit_amt_condition;
		}

		if ($wallet_type) {
			$result = $this->utils->transferWallet($player_id, $player_name, $from_id, $to_id, $amount, $user_id, null, null, false, $reason, Transactions::MANUALLY_ADJUSTED);

			if ($result['success']) {
				$totalBalance = $this->wallet_model->getBalanceDetails($player_id);

				$this->payment_manager->addPlayerBalAdjustmentHistory(array(
					'playerId' => $player_id,
					'adjustmentType' => $adjustment_type,
					'walletType' => $wallet_type ?: 0, # 0 - MAIN WALLET
					'amountChanged' => $amount,
					'oldBalance' => $totalBalance && is_array($totalBalance) ? $totalBalance['total_balance'] : null,
					'newBalance' => $totalBalance && is_array($totalBalance) ? $totalBalance['total_balance'] : null,
					'reason' => $reason,
					'adjustedOn' => $current_timestamp,
					'adjustedBy' => $user_id,
					'show_flag' => $show_in_front_end == '1',
				));
			}

			$note = 'transfer wallet from ' . $from_id . ' to ' . $to_id . ', player:' . $player_name .
				', wallet_name:' . $wallet_name . ', amount:' . $amount;
		} else {
			//only main wallet
			//lock
			$lock_type = Utils::LOCK_ACTION_BALANCE;
			$lockedKey=null;
			$lock_it = $this->lockPlayerBalanceResource($player_id, $lockedKey);
			try {
				if ($lock_it) {

					$this->startTrans();

					$totalBeforeBalance = $this->wallet_model->getTotalBalance($player_id);
					$this->utils->debug_log('player_id', $player_id, 'totalBeforeBalance', $totalBeforeBalance);

					$before_adjustment = $this->player_model->getMainWalletBalance($player_id);
					switch ($adjustment_type) {
					case Transactions::AUTO_ADD_CASHBACK_TO_BALANCE:
						$after_adjustment = $before_adjustment + $amount;
						$action_name = 'Add manually cakback';
						break;
					case Transactions::MANUAL_ADD_BALANCE:
						$after_adjustment = $before_adjustment + $amount;
						$action_name = 'Add';
						break;
					case Transactions::ADD_BONUS:
						$after_adjustment = $before_adjustment + $amount;
						$action_name = 'Add';
						//create player promo
						//promoRuleId
						break;
					case Transactions::SUBTRACT_BONUS:
					case Transactions::MANUAL_SUBTRACT_BALANCE:
						$after_adjustment = $before_adjustment - $amount;
						$action_name = 'Subtract';
						break;
					}

					if ($after_adjustment < 0) {
						return array('success' => false);
					}

					$note = sprintf('%s <b>%s</b> balance to <b>%s</b>\'s <b>%s</b>(<b>%s</b> to <b>%s</b>) by <b>%s</b>, <b>%s</b>',
						$action_name, number_format($amount, 2), $player_name, $wallet_name,
						number_format($before_adjustment, 2), number_format($after_adjustment, 2),
						$user_name, $deposit_amount_note);
					$note_reason = sprintf('<i>Reason:</i> %s <br>',$reason);
					$note = (trim($reason) != '')  ? ($note_reason . sprintf('<i>Normal Note:</i> %s <br>',$note)) : $note;
					$status = $this->input->post('status');
					$betTimes = $this->input->post('betTimes');


					#if want pending, don't create transaction, only create player promo
					if($adjustment_type == Transactions::ADD_BONUS && $status == Player_promo::TRANS_STATUS_REQUEST ){

		             	// request promo
						$this->player_promo->requestPromoToPlayer($player_id, $promoRuleId, $amount, $promoCmsSettingId, $user_id, null, $deposit_amt_condition , Player_promo::TRANS_STATUS_REQUEST, $betTimes, $reason );

						 $result['success'] = $this->endTransWithSucc();
						 return $result;
					}


					$transaction = $this->transactions->createAdjustmentTransaction($adjustment_type,
						$user_id, $player_id, $amount, $before_adjustment, $note, $totalBeforeBalance,
						$promo_category, $show_in_front_end, $reason,null,$adjustment_category,Transactions::MANUALLY_ADJUSTED);


					if (!$transaction) {
						//rollback and quit;
						$this->rollbackTrans();
						return array('success' => false);
					}

					$this->payment_manager->addPlayerBalAdjustmentHistory(array(
						'playerId' => $transaction['to_id'],
						'adjustmentType' => $transaction['transaction_type'],
						'walletType' => 0, # 0 - MAIN WALLET
						'amountChanged' => $transaction['amount'],
						'oldBalance' => $transaction['before_balance'],
						'newBalance' => $transaction['after_balance'],
						'reason' => $reason,
						'adjustedOn' => $transaction['created_at'],
						'adjustedBy' => $transaction['from_id'],
						'show_flag' => $show_in_front_end == '1',
					));

                    switch((int)$adjustment_type){
                        case Transactions::ADD_BONUS:
                            $deductDeposit = $this->input->post('deductDeposit');
                            $deposit_amt_condition = $this->input->post('depositAmtCondition') ? $this->input->post('depositAmtCondition') : null;


                            if ($deductDeposit) {
                                $condition = (($amount + $deposit_amt_condition) * $betTimes) - $deposit_amt_condition;
                            } else {
                                $condition = ($amount + $deposit_amt_condition) * $betTimes;
                            }

                            $promorulesId = empty($promoRuleId) ? $this->promorules->getSystemManualPromoRuleId() : $promoRuleId;

                            $this->payment_manager->savePlayerWithdrawalCondition([
                                'source_id' => $transaction['id'],
                                'source_type' => 4, # manual
                                'started_at' => $current_timestamp,
                                'condition_amount' => $condition,
                                'status' => 1, # enabled
                                'player_id' => $player_id,
                                'promotion_id' => $promorulesId,
                                'bet_times' => $betTimes,
                                'bonus_amount' => $amount,
                                'deposit_amount' => $deposit_amt_condition,
                            ]);

                            //save to player
                            if (!empty($promorulesId)) {
                                //load from
                                $promorules = $this->promorules->getPromoruleById($promorulesId);
                                $promo_category = $promorules['promoCategory'];
                            }
                            $promoCmsSettingId = $this->promorules->getSystemManualPromoCMSId();
                            $playerBonusAmount = $amount;
                            $player_promo_id = $this->player_promo->approvePromoToPlayer($player_id, $promorulesId, $playerBonusAmount,
                                $promoCmsSettingId, $user_id);
                            //update player promo id of transaction
                            $this->transactions->updatePlayerPromoId($transaction['id'], $player_promo_id, $promo_category);
                            break;
                        case Transactions::MANUAL_SUBTRACT_BALANCE:
                            if(!empty($manual_subtract_balance_tag_id)){
                                $tags = explode(',', $manual_subtract_balance_tag_id);
                                foreach($tags as $tag) {
                                    $data = Array(
                                        'rtn_id' => $transaction['id'],
                                        'msbt_id' => $tag,
                                        'created_at' => $transaction['created_at'],
                                        'updated_at' => $transaction['created_at']
                                    );
                                    $this->player_manager->insertTransactionsTag($data);
                                }
                            }
                            break;
                        case Transactions::AUTO_ADD_CASHBACK_TO_BALANCE:
                            $commonSettings = (array)$this->group_level->getCashbackSettings();
                            $commonMaxBonus = $commonSettings['max_cashback_amount'];
                            $withdraw_condition = (isset($commonSettings['withdraw_condition'])) ? (float)$commonSettings['withdraw_condition'] : 0;

                            $withdraw_condition_amount = $amount * $withdraw_condition;

                            if($generate_withdrawal_condition){
                                $this->withdraw_condition->createWithdrawConditionForCashback($transaction['id'], $withdraw_condition_amount, $withdraw_condition, $player_id, $amount);
                            }

                            $adminUserId = $this->authentication->getUserId();
                            $this->transaction_notes->add($reason, $adminUserId, $adjustment_type, $transaction['id']);
                            break;
                        default:
                    }

					$result['success'] = $this->endTransWithSucc();
				}
			} finally {
				// release it
				$rlt = $this->releasePlayerBalanceResource($player_id, $lockedKey);
			}

		}

		if (!$result['success']) {
			$note = $note . ' failed';
		}
		$this->saveAction(self::ACTION_LOG_TITLE, 'Adjust Balance', $note);

		return $result;
	}
	# END ADJUST BALANCE ###########################################################################################################################

	/**
	 * detail: display page for Mass bunos form
	 * @deprecated
	 *
	 * @return load template
	 */
	public function mass_add_bonus() {
		$this->load->model(array('group_level', 'player_model'));
		$this->template->add_css('resources/css/collapse-style.css');
		$this->template->add_css('resources/css/jquery-checktree.css');

		$this->template->add_css('resources/third_party/bootstrap-multiselect-master/docs/css/prettify.css');
		$this->template->add_css('resources/third_party/bootstrap-multiselect-master/dist/css/bootstrap-multiselect.css');

		$this->template->add_js('resources/third_party/bootstrap-multiselect-master/docs/js/prettify.js');
		$this->template->add_js('resources/third_party/bootstrap-multiselect-master/dist/js/bootstrap-multiselect.js');

		$data['memberGroup'] = $this->group_level->getAllMemberGroup();
		$data['playerLvl'] = $this->group_level->getAllPlayerLevelsForSelect();
		$data['players'] = $this->player_model->getPlayersList(); //get all players

		$this->loadTemplate(lang('payment.massBonusAdd'), '', '', 'payment');
		$this->template->write_view('sidebar', 'payment_management/sidebar');
		$this->template->write_view('main_content', 'payment_management/mass_bonus_add/view_mass_bonus_add_form', $data);
		$this->template->render();
	}

	/**
	 * detail: display page for Minimum Withdraw Setting
	 *
	 * @return load template
	 */
	public function minWithdrawSetting($from = 'system') {
		if (!$this->permissions->checkPermissions('minimum_withdraw_settings')) {
			$this->error_access($from);
		} else {
			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			$data['min_withdraw'] = $this->payment_manager->getOperatorGlobalSetting('min_withdraw');

			$this->loadTemplate(lang('pay.minwithsetting'), '', '', 'system');
			$this->template->write_view('sidebar', 'system_management/sidebar');
			$this->template->write_view('main_content', 'payment_management/view_min_withdraw_setting', $data);
			$this->template->render();
		}
	}

	/**
	 *
	 * detail: save minimum withdraw seting datas
	 *
	 * $POST $min_withdraw
	 * @return void
	 */
	public function saveMinWithdrawSetting() {

		$this->load->model(['operatorglobalsettings']);

		$min_withdraw = $this->input->post('min_withdraw');

		$data['name'] = 'min_withdraw';
		$data['value'] = $min_withdraw;
		$this->payment_manager->setOperatorGlobalSetting($data);

		$this->saveAction(self::ACTION_LOG_TITLE, 'Update Minimum Withdraw Setting', "User " . $this->authentication->getUsername() . " has successfully update minimum withdraw setting.");

		$message = lang('pay.minwithsetting') . ' ' . lang('con.pym11');
		$this->alertMessage(1, $message);
		redirect('payment_management/minWithdrawSetting');
	}


	protected function process_player_promo($playerId, $promo_cms_id, $transferAmount=null, $subWalletId=null, &$error=null){
		$this->load->model(['player_promo', 'promorules']);
		list($promorule, $promoCmsSettingId)=$this->promorules->getByCmsPromoCodeOrId($promo_cms_id);

		$player_promo_id=null;
		if(!empty($playerId) && !empty($promo_cms_id) && !empty($promorule)){
			$promorulesId=$promorule['promorulesId'];

			$allowedFlag = $this->promorules->isAllowedPlayer($promorulesId, $promorule, $playerId);
			if(!$allowedFlag){
				$error=lang('notify.35');
				return null;
			}

			//if this promorule is required pre-application
			if($promorule['disabled_pre_application']!='1'){
				//should have approved player promo
				$this->load->model(['player_promo']);
				$player_promo_id=$this->player_promo->getApprovedPlayerPromo($playerId, $promorulesId);
			}else{
				//check sub wallet only
				if($this->promorules->isTransferPromo($promorule)){

					//check
					if ($promorule['depositConditionNonFixedDepositAmount'] == Promorules::NON_FIXED_DEPOSIT_MIN_MAX) {
						if ($transferAmount >= $promorule['nonfixedDepositMinAmount'] && $transferAmount <= $promorule['nonfixedDepositMaxAmount']) {
						} else {
							$error = lang('notify.37');
							return null;
						}
					}

					$trigger_wallets=$promorule['trigger_wallets'];
					$trigger_wallets_arr=[];
					if(!empty($trigger_wallets)){
						$trigger_wallets_arr=explode(',',$trigger_wallets);
					}
					if(!in_array($subWalletId, $trigger_wallets_arr)){
						$this->utils->error_log('subWalletId should be ', $trigger_wallets_arr ,'current',$subWalletId);
						// $message = 'Only trigger on transfer right sub-wallet';
						$error = lang('Must choose correct sub-wallet');
						return null;
					}

				}

				if($this->promorules->isDepositPromo($promorule) && !empty($transferAmount)){
					//check
					if ($promorule['depositConditionNonFixedDepositAmount'] == Promorules::NON_FIXED_DEPOSIT_MIN_MAX) {
						if ($transferAmount >= $promorule['nonfixedDepositMinAmount'] && $transferAmount <= $promorule['nonfixedDepositMaxAmount']) {
						} else {
							$error = lang('notify.37');
							return null;
						}
					}
				}

				//create player promo
				$player_promo_id=$this->player_promo->requestPromoToPlayer($playerId, $promorulesId, null, $promo_cms_id);
			}
		}

		$this->utils->debug_log('process player promo', $player_promo_id, 'player id', $playerId, 'promo_cms_id', $promo_cms_id, 'error', $error);

		return $player_promo_id;
	}

	/**
	 * detail: display form for adding deposit
	 *
	 * @POST $username
	 * @POST $amount
	 * @POST $subwallet
	 * @POST $currency
	 * @POST $payment_account_id
	 * @POST $date
	 * @POST $internal_note
	 * @POST $external_note
	 * @POST $status
	 * @return load template
	 */
	public function newDeposit() {
		if (!$this->permissions->checkPermissions('new_deposit')) {
			return $this->error_access();
		}
		$userId=$this->authentication->getUserId();

		$this->load->library('form_validation');
		$this->load->model(array('payment_account', 'sale_order', 'wallet_model', 'promorules', 'sale_orders_notes', 'sale_orders_timelog'));

		$this->form_validation->set_rules('username', lang('Member Username'), 'trim|xss_clean|required|callback_is_exist[player.username]');
		$this->form_validation->set_rules('amount', lang('Amount'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('account', lang('Account'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('date', lang('Date'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('internal_note', lang('Internal Note'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('external_note', lang('External Note'), 'trim|xss_clean');
		$this->form_validation->set_rules('status', lang('Status'), 'trim|xss_clean|required');

		if ($this->form_validation->run()) {

			$username = $this->input->post('username');
			$playerId = $this->player_model->getPlayerIdByUsername($username);
			$amount = $this->input->post('amount');
			$subwallet = $this->input->post('subwallet');
			$promo_cms_id = $this->input->post('promo_cms_id');
			$paymentKind = Sale_order::PAYMENT_KIND_DEPOSIT;

			$bankAccountOwnerName = $this->input->post('bank_account_owner_name');

			if(!$this->verifyAndResetDoubleSubmitForAdmin($userId)){
				$message = lang('Please refresh and try, and donot allow double submit');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				redirect('/payment_management/newDeposit/'.$username);
				return;
			}

			$actionlogbybankAccountOwnerName = $bankAccountOwnerName." ".sprintf("Manual New Deposit %s to %s", $this->utils->formatCurrencyNoSym($amount), $username);
			$player_promo_id = $this->process_player_promo($playerId, $promo_cms_id, $amount, $subwallet, $error);

			$this->utils->debug_log('process player promo result', $player_promo_id, $error);

			$currency = $this->config->item('default_currency');
			$payment_account_id = $this->input->post('account');
			$paymentAccount = $this->payment_account->getPaymentAccount($payment_account_id);
			$systemId = $paymentAccount->external_system_id;
			$date = $this->input->post('date');
			$internal_note = $this->input->post('internal_note');
			$external_note = $this->input->post('external_note');
			$status = $this->input->post('status');
			$show_reason = false;
			$success = false;

			$lockedKey=null;
			$locked = $this->lockPlayerBalanceResource($playerId, $lockedKey);
			try {
				if ($locked) {
					$this->payment_account->startTrans();

					$saleOrderId = $this->sale_order->createSaleOrder($systemId, $playerId, $amount, $paymentKind,
						Sale_order::STATUS_PROCESSING, null, $player_promo_id, $currency, $payment_account_id, $date, null,
						$subwallet);
					$this->player_model->incTotalDepositCount($playerId);
					$success = !empty($saleOrderId);

					if($success){
						$this->sale_orders_notes->add($actionlogbybankAccountOwnerName, Users::SUPER_ADMIN_ID, Sale_orders_notes::ACTION_LOG, $saleOrderId);
						$this->sale_orders_timelog->add($saleOrderId, Sale_orders_timelog::ADMIN_USER, $userId, array('before_status' => Sale_order::STATUS_PROCESSING, 'after_status' => null));
						if (!empty($internal_note)) {
							$this->sale_orders_notes->add($internal_note, $userId, Sale_orders_notes::INTERNAL_NOTE, $saleOrderId);
						}
						if (!empty($external_note)) {
							$this->sale_orders_notes->add($external_note, $userId, Sale_orders_notes::EXTERNAL_NOTE, $saleOrderId);
						}
					}

					if (empty($status)) {
						$status == Sale_order::STATUS_PROCESSING;
					}

					if ($status == Sale_order::STATUS_PROCESSING) {
						//pending
						$newDepositActionlogPending = sprintf("New Deposit is success processing, status => %s", $status);
						$this->sale_orders_notes->add($newDepositActionlogPending, Users::SUPER_ADMIN_ID, Sale_orders_notes::ACTION_LOG, $saleOrderId);

					} else if ($status == Sale_order::STATUS_SETTLED) {
						$newDepositActionlogApproved = sprintf("New Deposit is success settled, status => %s", $status);
						$this->sale_order->approveSaleOrder($saleOrderId, $newDepositActionlogApproved, $show_reason);
						//transfer to subwallet
						$transfer_to = $subwallet;
						if ($this->utils->existsSubWallet($transfer_to)) {
							$transfer_from = Wallet_model::MAIN_WALLET_ID;
							$rlt = $this->utils->transferWallet($playerId, $username, $transfer_from, $transfer_to, $amount, $userId);

							$this->utils->debug_log('transfer to subwallet failed', $playerId);
						}
					}
				}

				$success = $this->payment_account->endTransWithSucc() && $success;
			} finally {
				// release it
				$rlt = $this->releasePlayerBalanceResource($playerId, $lockedKey);
				$this->utils->debug_log('newDeposit releasePlayerBalance', $playerId, $rlt);
			}

			if($success){
				$this->triggerDepositEvent($playerId, $saleOrderId, null, null, null, $userId);
			}

			if ($success) {
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('new_deposit.success.add').' '.$error);
			} else {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message').' '.$error);
			}

			if ($status == Sale_order::STATUS_PROCESSING) {
				redirect('home/nav/requestToday');
			} else {
				redirect('home/nav/approvedToday');
			}
		}

		$data['account_list'] = $this->payment_account->getAllPaymentAccountDetails(array(
			'banktype.bankName' => 'asc',
			'payment_account.payment_account_name' => 'asc',
		));

		$data['subwallets'] = $this->wallet_model->getSubwalletMap();
		$data['avail_promocms_list']=$this->promorules->getAllPromoOnDeposit();
		$data['double_submit_hidden_field']=$this->initDoubleSubmitAndReturnHiddenFieldForAdmin($userId);

		$this->loadTemplate('Payment Management', '', '', 'payment');
		$this->template->write_view('sidebar', 'payment_management/sidebar');
		$this->template->write_view('main_content', 'payment_management/newDeposit', $data);
		$this->template->render();
	}

	/**
	 * detail: display form for adding withdrawal
	 *
	 * @POST $amount
	 * @POST $date
	 * @POST $username
	 * @POST $reason
	 * @POST $type
	 * @POST $playerBankDetailsId
	 * @POST $bankType
	 * @POST $accountName
	 * @POST $accountNumber
	 * @POST $province
	 * @POST $city
	 * @POST $branch
	 * @POST $status
	 * @return load template
	 */
	public function newWithdrawal($username=null) {
		if (!$this->permissions->checkPermissions('new_withdrawal')) {
			return $this->error_access();
		}
		$userId=$this->authentication->getUserId();

		$this->load->library(array('form_validation'));
		$this->load->model(array('playerbankdetails', 'player_model', 'wallet_model', 'daily_player_trans', 'operatorglobalsettings', 'banktype', 'walletaccount_notes','walletaccount_timelog'));

		$this->form_validation->set_rules('username', 'Member Username', 'trim|xss_clean|required|callback_is_exist[player.username]');
		$this->form_validation->set_rules('amount', 'Amount', "trim|xss_clean|required");
		$this->form_validation->set_rules('date', 'Date', 'trim|xss_clean|required');
		$this->form_validation->set_rules('internal_note', lang('Internal Note'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('external_note', lang('External Note'), 'trim|xss_clean');
		$this->form_validation->set_rules('type', 'Type', 'trim|xss_clean|required');

		if ($this->input->post('type') !== null) {
			switch ($this->input->post('type')) {

			case '0':
				$this->form_validation->set_rules('bankType', 'Bank Name', 'trim|xss_clean|required');
				$this->form_validation->set_rules('accountName', 'Bank Account Full Name', 'trim|xss_clean|required');
				$this->form_validation->set_rules('accountNumber', 'Bank Acount Number', 'trim|xss_clean|required');
				$this->form_validation->set_rules('province', 'Province', 'trim|xss_clean|required');
				$this->form_validation->set_rules('city', 'City', 'trim|xss_clean|required');
				$this->form_validation->set_rules('branch', 'Branch', 'trim|xss_clean|required');
				break;

			case '1':
				$this->form_validation->set_rules('bank', 'Bank', 'trim|xss_clean|required');
				break;

			}
		}

		if ($this->form_validation->run()) {

			$controller=$this;

			$username = $this->input->post('username');
			$playerId = $this->player_model->getPlayerIdByUsername($username);

			if(!$this->verifyAndResetDoubleSubmitForAdmin($userId)){
				$message = lang('Please refresh and try, and donot allow double submit');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				redirect('/payment_management/newWithdrawal/'.$username);
				return;
			}

			$result=['success'=>false, 'message'=>lang('error.default.db.message')];
			$success = $this->lockAndTrans(Utils::LOCK_ACTION_BALANCE, $playerId, function ()
				use ($controller, $playerId, &$result) {

				$userId              = $controller->authentication->getUserId();

				$amount              = $controller->input->post('amount');
				$date                = $controller->input->post('date');
				$internal_note       = $controller->input->post('internal_note');
				$external_note       = $controller->input->post('external_note');
				$type                = $controller->input->post('type');
				$playerBankDetailsId = $controller->input->post('bank');
				$bankType            = $controller->input->post('bankType');
				$accountName         = $controller->input->post('accountName');
				$accountNumber       = $controller->input->post('accountNumber');
				$province            = $controller->input->post('province');
				$city                = $controller->input->post('city');
				$branch              = $controller->input->post('branch');
				$status              = $controller->input->post('status');

				$player                  = $controller->player_model->getPlayerById($playerId);
				$ipAddress               = $controller->utils->getIP();
				$playerAccount           = $controller->wallet_model->getMainWalletBy($playerId);
				$enabled_withdrawal      = $player->enabled_withdrawal;
				$playerMainWalletBalance = $playerAccount->totalBalanceAmount;
				$playerAccountId         = $playerAccount->playerAccountId;
				$geolocation             = $controller->utils->getGeoplugin($ipAddress);
				$transactionCode         = $controller->wallet_model->getRandomTransactionCode();
				$playerBankDetails       = $this->playerbankdetails->getBankList(array('playerBankDetailsId' => $playerBankDetailsId))[0];
				$bankName                = $this->banktype->getBankTypeById($playerBankDetails['bankTypeId'])->bankName;
				$dwLocation              = implode(',', array_values($geolocation));

				if ($enabled_withdrawal == Player_model::WITHDRAWAL_DISABLED) {
					$result['message']=lang("Player Withdrawal is disabled");
					$result['success']=false;
					return $result['success'];
				}

				if ($playerMainWalletBalance < $amount) {
					$result['message']=lang("Withdrawal Amount is greater than Current Balance");
					$result['success']=false;
					return $result['success'];
				}

				$walletAccountData = array(
					'playerAccountId'        => $playerAccountId,
					'walletType'             => Wallet_model::TYPE_MAINWALLET,
					'amount'                 => $amount,
					'dwMethod'               => 1,
					'dwStatus'               => Wallet_model::REQUEST_STATUS,
					'dwDateTime'             => $date,
					'transactionType'        => 'withdrawal',
					'dwIp'                   => $ipAddress,
					'dwLocation'             => $dwLocation,
					'transactionCode'        => $transactionCode,
					'status'                 => '0',
					'before_balance'         => $playerMainWalletBalance,
					'after_balance'          => $playerMainWalletBalance - $amount,
					'playerId'               => $playerId,
					'player_bank_details_id' => $playerBankDetailsId,
					'notes'                  => '',
					'bankAccountFullName'    => $playerBankDetails['bankAccountFullName'],
					'bankAccountNumber'      => $playerBankDetails['bankAccountNumber'],
					'bankName'               => $bankName,
					'bankAddress'            => $playerBankDetails['bankAddress'],
					'bankCity'               => $playerBankDetails['city'],
					'bankProvince'           => $playerBankDetails['province'],
					'bankBranch'             => $playerBankDetails['branch'],
				);

				# OGP-3531
				if($this->system_feature->isEnabledFeature('enable_withdrawal_pending_review')){
					if($this->checkPlayerIfTagIsUnderPendingWithdrawTag($playerId)){
						$walletAccountData['dwStatus'] = Wallet_model::PENDING_REVIEW_STATUS;
					}
				}

				$localBankWithdrawalDetails = array(
					'withdrawalAmount' => $amount,
					'playerBankDetailsId' => $playerBankDetailsId,
					'depositDateTime' => $date,
					'status' => 'active',
				);

				$walletAccountId = $controller->wallet_model->newWithdrawal($walletAccountData, $localBankWithdrawalDetails, $playerId);

				#add notes to walletaccount_notes content
				if(isset($walletAccountId)){
					if (!empty($internal_note)) {
					$this->walletaccount_notes->add($internal_note, $userId, Walletaccount_notes::INTERNAL_NOTE, $walletAccountId);
					}

					if (!empty($external_note)) {
						$this->walletaccount_notes->add($external_note, $userId, Walletaccount_notes::EXTERNAL_NOTE, $walletAccountId);
					}

					$newWithdrawalActionlogPending = sprintf("New Withdrawal is success processing, status => %s", $status);
					$this->wallet_model->addWalletaccountNotes($walletAccountId, $userId, $newWithdrawalActionlogPending, $status, null,Walletaccount_timelog::ADMIN_USER);
				}

				$result['success']=!!$walletAccountId;

				if($result['success']){
					$result['message']=null;
				}

				return $result['success'];

			}); // end of lock and trans

			$this->utils->debug_log('new withdrawal result', $result);

			if($result['success']){
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('New withdrawal has been successfully added'));
				$start_today = date("Y-m-d") . ' 00:00:00';
				$end_today = date("Y-m-d") . ' 23:59:59';
				$status = Wallet_model::REQUEST_STATUS;
				if($this->checkPlayerIfTagIsUnderPendingWithdrawTag($playerId) && $this->system_feature->isEnabledFeature('enable_withdrawal_pending_review')){
					$status = Wallet_model::PENDING_REVIEW_STATUS;
				}
				redirect('payment_management/viewWithdrawalRequestList?dwStatus='.$status.'&enable_date=true&withdrawal_date_from='.$start_today.'&withdrawal_date_to'.$end_today.'&date_range=2&search_status='.$status);
			} else {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $result['message']);
			}

		}

		$data['username']=$username;
		$data['bank_list'] = $this->banktype->getBankTypeKV();
		$data['min_amount'] = $this->operatorglobalsettings->getSettingDoubleValue('min_withdraw');
		$data['double_submit_hidden_field']=$this->initDoubleSubmitAndReturnHiddenFieldForAdmin($userId);

		$this->loadTemplate(lang('Payment Management'), '', '', 'payment');
		$this->template->write_view('sidebar', 'payment_management/sidebar');
		$this->template->write_view('main_content', 'payment_management/newWithdrawal', $data);
		$this->template->render();
	}

	/**
	 * overview: Payment Settings
	 *
	 * @return load template
	 */
	public function defaultCollectionAccount($from = 'system') {
		if (!$this->permissions->checkPermissions('default_collection_account')) {
			return $this->error_access($from);
		}
		if (($this->session->userdata('sidebar_status') == NULL)) {
			$this->session->set_userdata(array('sidebar_status' => 'active'));
		}

		# payment_account_types
		$data['payment_account_types'] = json_decode($this->operatorglobalsettings->getPaymentAccountTypes(true), true);

		# special_payment_list
		$this->load->model('payment_account');
		$special_payment_list = $this->operatorglobalsettings->getSpecialPaymentList();
		$special_payment_list_mobile = $this->operatorglobalsettings->getSpecialPaymentListMobile();
		## Get configured 3rd party payment accounts and index them using payment account ID
		$paymentAccounts = $this->payment_account->getAllPaymentAccountDetails();
		if(is_array($paymentAccounts)){
			foreach ($paymentAccounts as $aPaymentAccount) {
				$data['payment_account_list'][$aPaymentAccount->id] = $aPaymentAccount;
			}
		}

		if (!empty($special_payment_list)) {
			$data['special_payment_list'] = array_map('intval', $special_payment_list);
		} else {
			$data['special_payment_list'] = array();
		}

		if (!empty($special_payment_list_mobile)) {
			$data['special_payment_list_mobile'] = array_map('intval', $special_payment_list_mobile);
		} else {
			$data['special_payment_list_mobile'] = array();
		}

		$this->loadTemplate(lang('Default Collection Account'), '', '', 'system');
		$this->template->write_view('sidebar', 'system_management/sidebar');
		$this->template->write_view('main_content', 'payment_management/view_paymentaccount_setting', $data);
		$this->template->render();
	}

	/**
	 * detail: save data for paymetn account setting
	 *
	 * @return	void
	 */
	public function savePaymentAccountSetting() {
		$selectedPaymentAccountType = $this->input->post('selectedPaymentAccountType');
		$special_payment_list = $this->input->post('special_payment_list');
		$special_payment_list_mobile = $this->input->post('special_payment_list_mobile');

		if (empty($selectedPaymentAccountType)) {
			$selectedPaymentAccountType = [];
		}

		$payment_account_types = "{" .
			'"1" : { "lang_key": "pay.manual_online_payment", "enabled": ' . (in_array("1", $selectedPaymentAccountType) ? 'true' : 'false') . '},' .
			'"2" : { "lang_key": "pay.auto_online_payment", "enabled": ' . (in_array("2", $selectedPaymentAccountType) ? 'true' : 'false') . '},' .
			'"3" : { "lang_key": "pay.local_bank_offline", "enabled": ' . (in_array("3", $selectedPaymentAccountType) ? 'true' : 'false') . '}' .
			"}";

		$this->operatorglobalsettings->setPaymentAccountTypes($payment_account_types, true);
		$this->operatorglobalsettings->setSpecialPaymentList($special_payment_list);
		$this->operatorglobalsettings->setSpecialPaymentListMobile($special_payment_list_mobile);

		$this->saveAction(self::ACTION_LOG_TITLE, 'Update Payment Account Setting', "User " . $this->authentication->getUsername() . " has successfully update payment account setting.");

		$message = lang('report.log06');
		$this->alertMessage(1, $message);
		redirect('payment_management/defaultCollectionAccount');
	}

	/**
	 *
	 * detail: Defines the processing stages under a withdrawal request, User can define how many of these stages are enabled, and the names of these stages
	 *
	 * @return load template
	 */
	public function customWithdrawalProcessingStageSetting($from = 'system') {
		if (!$this->permissions->checkPermissions('withdrawal_workflow')) {
			return $this->error_access($from);
		}
		$data['setting'] = $this->operatorglobalsettings->getCustomWithdrawalProcessingStage();
        $data['withdrawal_setting'] = $this->operatorglobalsettings->getPlayerCenterWithdrawalPageSetting();

		$data['tag_list'] = $this->player->getAllTagsOnly();
		$data['taggedStatus'] = json_decode($this->operatorglobalsettings->getSetting('withdraw_pending_review_tags')->value,true);

		$this->session->userdata('sidebar_status', 'active');

		$this->loadTemplate(lang('pay.payment_account'), '', '', 'system');
		$this->template->write_view('sidebar', 'system_management/sidebar');
		$this->template->write_view('main_content', 'payment_management/view_custom_withdrawal_processing_stages', $data);
		$this->template->render();
	}

    /**
     * detail: save data for player center withdrawal page
     *
     * $POST $showMaxWithdrawalPerTransaction
     * $POST $showDailyMaxWithdrawalAmount
     * @return void
     */
    public function savePlayerCenterWithdrawalPageSetting() {
        $withdrawal_setting = [];
        $withdrawal_setting['showMaxWithdrawalPerTransaction'] = (int)$this->input->post('showMaxWithdrawalPerTransaction');
        $withdrawal_setting['showDailyMaxWithdrawalAmount'] = (int)$this->input->post('showDailyMaxWithdrawalAmount');
        $this->operatorglobalsettings->setPlayerCenterWithdrawalPageSetting($withdrawal_setting);
        redirect('payment_management/customWithdrawalProcessingStageSetting');
    }

	/**
	 * detail: save data for withdrawal processing stage
	 *
	 * $POST $name_payProc
	 * $POST $enabled_payProc
	 * $POST $force_comment
	 * @return void
	 */
	public function saveCustomWithdrawalProcessingStageSetting() {
		$setting = array();
		for ($i = 0; $i < CUSTOM_WITHDRAWAL_PROCESSING_STAGES; $i++) {
			array_push($setting, array(
				"name" => $this->input->post('name_' . $i),
				"enabled" => $this->input->post('enabled_' . $i),
			));
		}
		$setting['payProc']['name'] = lang('pay.processing');
		$setting['payProc']['enabled'] = $this->input->post('enabled_payProc');

		$this->operatorglobalsettings->setCustomWithdrawalProcessingStage($setting);
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('sys.gd25'));
		redirect('payment_management/customWithdrawalProcessingStageSetting');
	}

	public function depositCountSetting() {
		if (!$this->permissions->checkPermissions('deposit_count_setting')) {
			return $this->error_access();
		}
		$this->loadTemplate(lang('role.72'), '', '', 'system');

		if (($this->session->userdata('sidebar_status') == NULL)) {
			$this->session->set_userdata(array('sidebar_status' => 'active'));
		}

		$this->load->model(['operatorglobalsettings']);

		$isSettingExist = $this->operatorglobalsettings->getSetting('deposit_count_list');

		$data['count'] = $isSettingExist->value ? $isSettingExist->value : self::DEFAULT_DEPOSIT_COUNT_SETTING;

		$this->template->write_view('sidebar', 'system_management/sidebar');
		$this->template->write_view('main_content', 'payment_management/view_deposit_count_list', $data);
		$this->template->render();
	}

	public function saveDepositCountList() {
		$this->load->model(array('operatorglobalsettings'));

		$depositCount = $this->input->post('deposit_count');

		$isSettingExist = $this->operatorglobalsettings->getSetting('deposit_count_list');

		if ($isSettingExist) {
			$this->operatorglobalsettings->putSetting('deposit_count_list', $depositCount[0], 'value');
		} else {
			$this->operatorglobalsettings->addRecord([ 'name' => 'deposit_count_list', 'value' => $depositCount[0] ]);
		}

		$this->returnJsonResult([$isSettingExist]);
	}

	public function exception_order_list(){
		if(!$this->permissions->checkPermissions('exception_order_list'))
			return $this->error_access();

		$this->loadTemplate(lang('Payment'), '', '', 'payment');

		if (($this->session->userdata('sidebar_status') == NULL)) {
			$this->session->set_userdata(array('sidebar_status' => 'active'));
		}

		$data=['title'=>lang('Exception Orders')];
		$data['export_report_permission']=$this->permissions->checkPermissions('export_exception_order');
		$data['conditions'] = $this->safeLoadParams(array(
			'player_bank_account_name'=>'',
			'player_bank_account_number' => '',
			'collection_bank_account_name' => '',
			'by_date_from'=>'',
			'by_date_to'=>'',
			'order_secure_id'=>'',
			'withdrawal_order_id'=>'',

		));

		$this->template->write_view('sidebar', 'payment_management/sidebar');
		$this->template->write_view('main_content', 'payment_management/exception_order_list', $data);
		$this->template->render();
	}

	public function lockedWithdrawalList() {
		if(!$this->permissions->checkPermissions('lock_withdrawal_list'))
			return $this->error_access();

		$this->loadTemplate(lang('Locked Withdrawal List'), '', '', 'payment');

		$this->template->write_view('sidebar', 'payment_management/sidebar');
		$this->template->write_view('main_content', 'payment_management/locked_withdrawal_list');
		$this->template->render();
	}

	public function lockedDepositList() {
		if(!$this->permissions->checkPermissions('lock_deposit_list'))
			return $this->error_access();

		$this->loadTemplate(lang('Locked Deposit List'), '', '', 'payment');

		$data['defaultSortColumn'] = $this->utils->getDefaultSortColumn('deposit_list');

		$this->template->write_view('sidebar', 'payment_management/sidebar');
		$this->template->write_view('main_content', 'payment_management/locked_deposit_list', $data);
		$this->template->render();
	}

	public function batchDeposit() {
		if ( ! $this->permissions->checkPermissions('batch_deposit') ) {
			return $this->error_access();
		}

		$this->load->model('payment_account');

		$data['account_list'] = $this->payment_account->getAllPaymentAccountDetails(array(
			'banktype.bankName' => 'asc',
			'payment_account.payment_account_name' => 'asc',
		));

		$this->session->set_userdata('prevent_refresh', true);

		$this->loadTemplate('Payment Management', '', '', 'payment');
		$this->template->write_view('sidebar', 'payment_management/sidebar');
		$this->template->write_view('main_content', 'payment_management/batch_deposit', $data);
		$this->template->render();
	}

	public function postBatchDeposit(){

		if ($this->session->userdata('prevent_refresh') == null) {
			redirect('/payment_management/batchDeposit');
		}

		$this->load->model(array('payment_account', 'sale_orders_notes', 'sale_orders_timelog'));

		$csv = file_get_contents($_FILES['usernames']['tmp_name']);

		$row = explode("\n", $csv);
		$row = array_filter($row);
		$row = array_unique($row);

		$adminUserId=$this->authentication->getUserId();

		$payment_account_id = $this->input->post('account');
		$paymentAccount = $this->payment_account->getPaymentAccount($payment_account_id);
		$systemId = $paymentAccount->external_system_id;
		$date = $this->input->post('date');
		$internal_note = $this->input->post('internal_note');
		$external_note = $this->input->post('external_note');
		$paymentKind = Sale_order::PAYMENT_KIND_DEPOSIT;
		$currency = $this->config->item('default_currency');
		$status = $this->input->post('status');

		$show_reason = false;

		$result = array('failed_count' => 0, 'success' => 0, 'failed' => []);
		$result['total_count'] = count($row);
		$success_users = [];

		foreach ($row as $key => $value) {

			$row_val = explode(',', $value);

			$username = trim($row_val[0]);
			$playerId = $this->player_model->getPlayerIdByUsername($username);

			$amount = trim($row_val[1]);

			if ($playerId) {

				$actionlogNotes = sprintf("Manual Batch Deposit %s to %s", $this->utils->formatCurrencyNoSym($amount), $username);

				$success = false;
				$this->payment_account->startTrans();

				$lockedKey=null;
				$locked = $this->lockPlayerBalanceResource($playerId, $lockedKey);
				try {
					if ($locked) {

						$saleOrderId = $this->sale_order->createSaleOrder($systemId, $playerId, $amount, $paymentKind,
							Sale_order::STATUS_PROCESSING, null, null, $currency, $payment_account_id, $date, null,
							null);
						$this->player_model->incTotalDepositCount($playerId);
						$success = !empty($saleOrderId);

						if($success){
							$this->sale_orders_notes->add($actionlogNotes, Users::SUPER_ADMIN_ID, Sale_orders_notes::ACTION_LOG, $saleOrderId);
							$this->sale_orders_timelog->add($saleOrderId, Sale_orders_timelog::ADMIN_USER, $adminUserId, array('before_status' => Sale_order::STATUS_PROCESSING, 'after_status' => null));
							if (!empty($internal_note)) {
								$this->sale_orders_notes->add($internal_note, $adminUserId, Sale_orders_notes::INTERNAL_NOTE, $saleOrderId);
							}
							if (!empty($external_note)) {
								$this->sale_orders_notes->add($external_note, $adminUserId, Sale_orders_notes::EXTERNAL_NOTE, $saleOrderId);
							}
						}

						if (empty($status)) {
							$status == Sale_order::STATUS_PROCESSING;
						}

						if ($status == Sale_order::STATUS_PROCESSING) {
							$batchDepositActionlogPending = sprintf("Batch Deposit is success processing, status => %s", $status);
							$this->sale_orders_notes->add($batchDepositActionlogPending, Users::SUPER_ADMIN_ID, Sale_orders_notes::ACTION_LOG, $saleOrderId);

						} else if ($status == Sale_order::STATUS_SETTLED) {
							$batchDepositActionlogApproved = sprintf("Batch Deposit is success settled, status => %s", $status);
							$this->sale_order->approveSaleOrder($saleOrderId, $batchDepositActionlogApproved, $show_reason);

						}

					}
				} finally {

					$rlt = $this->releasePlayerBalanceResource($playerId, $lockedKey);

				}

				$success = $this->payment_account->endTransWithSucc() && $success;

				if($success){
					$this->triggerDepositEvent($playerId, $saleOrderId, null, null, null, $adminUserId);
				}

				if ($success) {

					$success_users[] = $username;

				}else{

					$result['failed'][$message][] = $username;
					@$result['failed_count']++;

				}

			} else {
				$result['failed']['User does not exist'][] = $username;
				@$result['failed_count']++;
			}
		}

		$result['success_users'] = $success_users;

		$this->session->unset_userdata('prevent_refresh');

		$this->loadTemplate('Payment Management', '', '', 'payment');
		$this->template->write_view('sidebar', 'payment_management/sidebar');
		$this->template->write_view('main_content', 'payment_management/batch_deposit_result', $result);
		$this->template->render();
	}

	public function userLockWithdrawal($walletAccountId) {
		$this->load->model(array('wallet_model', 'users'));

		$adminUserId = $this->authentication->getUserId();
		$controller = $this;
		$message = '';
		$lock_result=false;

		$lock_withdrawal=$this->lockAndTransForWithdrawLock($walletAccountId, function() use (
			$controller, $walletAccountId, $adminUserId, &$message, &$lock_result) {

			$lockedByUserId = $this->wallet_model->checkWithdrawLocked($walletAccountId);

			if($lockedByUserId) {
				if($adminUserId != $lockedByUserId) {
					$adminUsername = $this->users->getUsernameById($lockedByUserId);  // transaction is locked by this user
					$message = lang('Withdrawal transaction was already locked by').' '.'<b>'.$adminUsername.'</b>';
				}else{
					//locked by self
					$lock_result=true;
				}
			} else {
				$lock_result=$this->wallet_model->userLockWithdrawal($walletAccountId, $adminUserId);
			}

			return true;
		});

		if(!$lock_withdrawal){
			$message=lang('Lock failed');
			$lock_result=false;
		}

		$this->returnJsonResult(['message' => $message, 'lock_result'=>$lock_result]);
	}

	public function userLockDeposit($salesOrderId) {
		$this->load->model(array('sale_order', 'users'));

		$adminUserId = $this->authentication->getUserId();
		$controller = $this;
		$message = '';
		$lock_result=false;

		$this->lockAndTransForDepositLock($salesOrderId, function() use (
				$controller, $salesOrderId, $adminUserId, &$message, &$lock_result) {
			$lockedByUserId = $this->sale_order->checkDepositLocked($salesOrderId);

			if($lockedByUserId) {
				if($adminUserId != $lockedByUserId) {
					$adminUsername = $this->users->getUsernameById($lockedByUserId);  // transaction is locked by this user
					$message = lang('Deposit transaction was already locked by').' '.'<b>'.$adminUsername.'</b>';
				}else{
					//locked by self
					$lock_result=true;
				}
			} else {
				$lock_result=$this->sale_order->userLockDeposit($salesOrderId, $adminUserId);
			}

			return true;
		});

		$this->returnJsonResult(['message' => $message, 'lock_result'=>$lock_result]);
	}

	public function unlockDepositTransaction() {
		$this->load->model(array('sale_order'));
		$controller = $this;
		$salesOrderId = $this->input->post('salesOrderId');

		$this->lockAndTransForDepositLock($salesOrderId, function() use ($controller, $salesOrderId) {
			$controller->sale_order->userUnlockDeposit($salesOrderId);
			return true;
		});
	}

	public function unlockWithdrawTransaction() {
		$this->load->model(array('wallet_model'));
		$controller = $this;
		$walletAccountId = $this->input->post('walletAccountId');

		$this->lockAndTransForDepositLock($walletAccountId, function() use ($controller, $walletAccountId) {
			$controller->wallet_model->userUnlockWithdrawal($walletAccountId);
			return true;
		});
	}

	public function batchUnlockTransaction() {
		$this->load->model(array('sale_order'));
		$this->sale_order->batchUnlockTransactions($this->input->post('saleOrdersId'));
	}

	public function batchUnlockWithdrawTransaction() {
		$this->load->model(array('wallet_model'));
		$this->wallet_model->batchUnlockWithdrawTransaction($this->input->post('walletAccountId'));
	}

	public function newInternalWithdrawal() {
		if(!$this->permissions->checkPermissions('new_internal_withdrawal'))
			return $this->error_access();

		$this->load->library('form_validation');
		$this->load->model(array('payment_account', 'transactions'));

		$this->form_validation->set_rules('amount', lang('Amount'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('account', lang('Account'), 'trim|xss_clean|required');

		if ($this->form_validation->run()) {

			$userId = $this->authentication->getUserId();
			$paymentAccount = $this->payment_account->getPaymentAccount($this->input->post('account'));
			$amount = $this->input->post('amount');
			//create transaction log
			$success = $this->transactions->createTransactionInternalWithdrawal($amount, $userId);

			if ($success) {
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('New Internal Withdrawal Successfully Added'));
			} else {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
			}
		}
		$data['account_list'] = $this->payment_account->getAllPaymentAccountInfo();
		$this->loadTemplate('Payment Management', '', '', 'payment');
		$this->template->write_view('sidebar', 'payment_management/sidebar');
		$this->template->write_view('main_content', 'payment_management/newInternalWithdrawal', $data);
		$this->template->render();
	}

	public function uploadBankIcon() {
		$image = isset($_FILES['filBankIcon']) ? $_FILES['filBankIcon'] : null;

		if(empty($image['name'][0])) {
			return array('status' => 'success');
		}

		$fullpath = $this->utils->getBanIconPath();

		$config = array(
            'allowed_types' => "jpg|jpeg|png|gif",
            'max_size'      => $this->utils->getMaxUploadSizeByte(),
            'overwrite'     => true,
            'remove_spaces' => true,
            'upload_path'   => $fullpath,
        );

		if (!empty($image)) {
	        $this->load->library('multiple_image_uploader');
			$response = $this->multiple_image_uploader->do_multiple_uploads($image, $fullpath, $config);


			if (strtolower($response['status']) == "success") {
				return array('status' => 'success', 'fileName' => $response['filename'][0]);
			}

			if(strtolower($response['status']) == "fail"){
				return array('status' => 'error', 'msg' => $response['message']);
			}
		}

		return false;
	}

    public function delete_deposit_attachment(){
		$this->load->model(array('player_attached_proof_file_model'));
		$playerId = $this->input->post('playerId');
		$tag = $this->input->post('tag');
		$data = [
			'picId' => $this->input->post('picId'),
			'playerId' => $playerId,
		];

		$response = $this->player_attached_proof_file_model->remove_proof_document($data);

		if(!empty($response)){
			$this->alertMessage($response['msg_type'], $response['msg']);
			if($response['status'] == "success"){
				if(!empty($tag)){
						$action = lang('role.238');
						$description = lang('role.238') . ' - '. lang('Image successfully deleted!') . ' - ' .lang('Image Document').' '.lang('upload_tag_'.$tag);

						$playerInfo = $this->player_model->getPlayerDetailsTagsById($playerId);
						if(!empty($playerInfo)){
							$this->savePlayerUpdateLog($playerId, $description, $this->authentication->getUsername());
						}
				}
			}

			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult($response);
				return;
			}
		}
	}

	/**
	 * overview : save log on player update history
	 *
	 * @param int $player_id	player_id
	 * @param $changes
	 * @param $updatedBy
	 */
	public function savePlayerUpdateLog($player_id, $changes, $updatedBy) {
		$data = array(
			'playerId' => $player_id,
			'changes' => $changes,
			'createdOn' => date('Y-m-d H:i:s'),
			'operator' => $updatedBy,
		);
		$this->player_manager->addPlayerInfoUpdates($player_id, $data);
	}

	public function batchRecoverWithdrawCondition() {
		if (!$this->permissions->checkPermissions('recover_withdraw_condition')) {
			$this->error_access();
			return;
		}

		$ids = $this->input->post('withdraw_condition_ids');
		# check if post data is array or not empty
		if (empty($ids) || !is_array($ids)) {
			return;
		}

		# check valid ids
		foreach ($ids as $key => $value) {
			$intValue = intval($value);

			if (empty($intValue)) {
				unset($ids[$key]);
			} else {
				$ids[$key] = $intValue;
			}
		}

		# final check if validated ids is not empty or array
		if (empty($ids) || !is_array($ids)) {
			return;
		}

		$this->load->model(array('withdraw_condition'));
		$this->withdraw_condition->updateStatus($ids, BaseModel::STATUS_NORMAL);
	}

	/**
	 * Displays images of proof slips based on player ID and sales order ID
	 *
	 * @param  string $player_id      Player ID
	 * @param  string $sales_order_id Sales order ID
	 * @return view                   Display
	 */
	public function show_proof_slip($player_id,$sales_order_id, $tag = 'deposit') {
		if (!$this->permissions->checkPermissions('kyc_attached_documents') || !$this->utils->isEnabledFeature('enable_deposit_upload_documents')) {
			$data['permission_verified'] = lang('con.plm01');
			$this->load->view('payment_management/proof_slip_attachments', $data);
		} else {
			$this->load->model(array('player_attached_proof_file_model','player_kyc'));
			$data['playerId'] = $player_id;
			$data['attachment_info'] = $this->player_attached_proof_file_model->getAttachementRecordInfo($player_id,null,'deposit',null,null,$sales_order_id,null,false);
			$this->load->view('payment_management/proof_slip_attachments', $data);
		}
	}
}

/* End of file payment_management.php */
/* Location: ./application/controllers/payment_management.php */
