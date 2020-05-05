<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';

/**
 * General behaviors include
 * * List of dispatch account group manager
 * * Exporting dispatch account group manage list through excel
 * * Adding group manager
 * * Modifying dispatch account group manager details
 * * Increasing/Decreasing group level count
 *
 * @category Payment Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Dispatch_account_management extends BaseController {

	const ACTION_MANAGEMENT_TITLE = 'Dispatch Account Management';

	function __construct() {
		parent::__construct();

		$this->load->helper(array('date_helper', 'url'));
		$this->load->library(array('form_validation', 'template', 'pagination', 'permissions', 'report_functions', 'payment_manager', 'depositpromo_manager'));
		$this->load->model(array('dispatch_account'));
		$this->permissions->checkSettings();
		$this->permissions->setPermissions();
	}

	/**
	 * Loads template for view based on regions in
	 * config > template.php
	 *
	 */
	private function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->add_css('resources/css/general/style.css');
		$this->template->add_css('resources/css/payment_account_management/style.css');
		$this->template->add_js('resources/js/jquery.numeric.min.js');
		$this->template->add_css('resources/css/datatables.min.css');
		$this->template->add_js('resources/js/datatables.min.js');
		$this->template->add_js('resources/js/dataTables.responsive.min.js');
		$this->template->add_css('resources/css/chosen.min.css');
		$this->template->add_js('resources/js/chosen.jquery.min.js');
		$this->template->add_js('resources/js/select2.min.js');
		$this->template->add_css('resources/css/select2.min.css');
		$this->template->add_js('resources/js/payment_management/dispatch_account_management.js');

		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('userId', $this->authentication->getUserId());
	}

	/**
	 * Shows Error message if user can't access the page
	 *
	 * @return	rendered Template
	 */
	private function error_access() {
		$this->loadTemplate(lang('Player Management'), '', '', 'player');
		$systemUrl = $this->utils->activeSystemSidebar();
		$data['redirect'] = $systemUrl;
		$message = lang('con.vsm01');
		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		$this->template->write_view('main_content', 'error_page', $data);
		$this->template->render();
	}

	/**
	 * Index Page for dispatch account management
	 *
	 */
	public function index() {
		redirect('dispatch_account_management/dispatchAccountGroupList');
	}

	/**
	 * view dispatch account group list
	 *
	 * @return	redered template
	 */
	public function dispatchAccountGroupList() {
		if (!$this->permissions->checkPermissions('dispatch_account')) {
			$this->error_access();
		} else {
			$sort = "id";
			$data['data'] = $this->dispatch_account->getDispatchAccountGroupList($sort, null, null);
			$this->utils->debug_log('============dispatchAccountGroupList data', $data['data']);
			$data['min_member_limit'] = ($this->CI->config->item('dispatch_account_min_member_limit')) ? $this->CI->config->item('dispatch_account_min_member_limit') : 100;

			$this->loadTemplate(lang('pay.dispatch_account'), '', '', 'system');
			$this->template->write_view('sidebar', 'system_management/sidebar');
			$this->template->write_view('main_content', 'payment_management/dispatch_account/dispatch_account_group_list', $data);
			$this->template->render();
		}
	}

	/**
	 * view dispatch account level list
	 *
	 * @param 	group_id
	 * @return	rendered template
	 */
	public function getDispatchAccountLevelList($group_id) {
		if (!$this->permissions->checkPermissions('dispatch_account')) {
			$this->error_access();
		} else {
			$this->loadTemplate(lang('pay.dispatch_account'), '', '', 'system');
			$data['datas'] = $this->dispatch_account->getDispatchAccountLevelListByGroupId($group_id);
			$data['group_id'] = $group_id;

			$this->template->write_view('sidebar', 'system_management/sidebar');
			$this->template->write_view('main_content', 'payment_management/dispatch_account/dispatch_account_level_list', $data);
			$this->template->render();
		}
	}

	/**
	 * get dispatch account group level
	 * @param 	int
	 * @return	loaded view page
	 */
	public function getDispatchAccountLevel($level_id) {
		if (!$this->permissions->checkPermissions('dispatch_account')) {
			$this->error_access();
		} else {
			$this->load->model(array('payment_account'));
			$active_payment_accounts = $this->payment_account->getActveDepositPaymentAccountWithBankTypeName(false);

			$data['level_id'] = $level_id;
			$data['data'] = $this->dispatch_account->getDispatchAccountLevelDetailsById($level_id);
			$data['correspond_payment_accounts'] = $this->dispatch_account->getLevelPaymentAccountsByLevelId($level_id);
			$data['form'] = &$form;
			$data['payment_account_list'] = array_column($active_payment_accounts, 'payment_account_full_name', 'payment_account_id');
			$data['min_member_limit'] = ($this->CI->config->item('dispatch_account_min_member_limit')) ? $this->CI->config->item('dispatch_account_min_member_limit') : 100;

			$this->loadTemplate(lang('pay.dispatch_account'), '', '', 'system');
			$this->template->write_view('sidebar', 'system_management/sidebar');
			$this->template->write_view('main_content', 'payment_management/dispatch_account/dispatch_account_level_details', $data);
			$this->template->render();
		}
	}

	public function getDispatchLevelPlayerList($level_id) {
		if (!$this->permissions->checkPermissions('dispatch_account')) {
			$this->error_access();
		} else {
			$this->load->model(array('payment_account', 'group_level'));
			$active_payment_accounts = $this->payment_account->getActveDepositPaymentAccountWithBankTypeName(false);

			$data['level_id'] = $level_id;
			$data['data'] = $this->dispatch_account->getDispatchAccountLevelDetailsById($level_id);
			$data['correspond_payment_accounts'] = $this->dispatch_account->getLevelPaymentAccountsByLevelId($level_id);
			$data['form'] = &$form;
			$data['payment_account_list'] = array_column($active_payment_accounts, 'payment_account_full_name', 'payment_account_id');

			$this->loadTemplate(lang('pay.dispatch_account'), '', '', 'system');
			$this->template->write_view('sidebar', 'system_management/sidebar');
			$this->template->write_view('main_content', 'payment_management/dispatch_account/dispatch_account_level_player_list', $data);
			$this->template->render();
		}
	}

	public function setPlayersToLevel($level_id=null) {
		if (!$this->permissions->checkPermissions('dispatch_account')) {
			$this->error_access();
		} else {
			if(empty($level_id)){
				$error_message = lang("dispatch_account_batch.empty_level_id.message");
				$this->returnJsonResult(array('error' => true, 'error_message' => $error_message));
				return;
			}

			$usernames = $this->input->post('new_players');
			$fail_list = [];
			$success_count = 0;
			$failed_count = 0;
			foreach ($usernames as $key => $username) {
				if(empty($username)){
					continue;
				}

				$player = $this->player_model->getPlayerByUsername($username);
				if(empty($player->playerId)){
					$error_message = lang("dispatch_account_batch.player_not_found.message");
					$fail_list[$key] = ['username' => $username, 'reason' => $error_message];
					$failed_count ++;
					continue;
				}

				# regard success but won't do anything when player already in this level
				if($player->dispatch_account_level_id == $level_id) {
					$success_count ++;
					continue;
				}
				$success = $this->player_model->adjustDispatchAccountLevel($player->playerId, $level_id);
				if(!$success){
					$fail_list[$key] = ['username' => $username, 'reason' => $error_message];
					$failed_count ++;
					continue;
				}
				$success_count ++;
			}
			$this->returnJsonResult(array('error' => false, 'fail_list' => $fail_list, 'success_count' => $success_count, 'failed_count' => $failed_count));
			return;
		}
	}

	/**
	 * get dispatch account group details
	 *
	 * @param 	int
	 * @return	redirect
	 */
	public function getDispatchAccountGroupDetails($group_id) {
		echo json_encode($this->dispatch_account->getDispatchAccountGroupDetails($group_id));
	}

	/**
	 * add dispatch account group
	 *
	 * @return	rendered template
	 */
	public function addDispatchAccountGroup() {
		$this->form_validation->set_rules('group_name', 'Group Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('group_level_count', 'Group Level Count', 'trim|required|xss_clean|is_numeric');
		$this->form_validation->set_rules('group_description', 'Group Description', 'trim|xss_clean');

		if($this->input->post('group_id')) {
			$group_id = $this->input->post('group_id');
		}

		if(!isset($group_id)) {
			$min_member_limit = ($this->CI->config->item('dispatch_account_min_member_limit')) ? $this->CI->config->item('dispatch_account_min_member_limit') : 100;
			$this->form_validation->set_rules('level_member_limit', 'Level Member Limit', 'trim|required|xss_clean|is_numeric|greater_than_equal_to['.$min_member_limit.']');
			$this->form_validation->set_rules('level_single_max_deposit', 'Level Single Max Deposit', 'trim|required|xss_clean|is_numeric');
			$this->form_validation->set_rules('level_total_deposit', 'Level Total Deposit', 'trim|required|xss_clean|is_numeric');
			$this->form_validation->set_rules('level_deposit_count', 'Level Deposit Count', 'trim|required|xss_clean|is_numeric');
			$this->form_validation->set_rules('level_total_withdraw', 'Level Total Withdraw', 'trim|required|xss_clean|is_numeric');
			$this->form_validation->set_rules('level_withdraw_count', 'Level Withdraw Count', 'trim|required|xss_clean|is_numeric');
		}

		$single_max_deposit = $this->input->post('level_single_max_deposit');
		$total_deposit = $this->input->post('level_total_deposit');
		$deposit_count = $this->input->post('level_deposit_count');
		$total_withdraw = $this->input->post('level_total_withdraw');
		$withdraw_count = $this->input->post('level_withdraw_count');
		$upgrade_condition = $single_max_deposit+$total_deposit+$deposit_count+$total_withdraw+$withdraw_count;

		if ($this->form_validation->run() == false) {
			$message = lang('con.vsm03');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		} elseif ($upgrade_condition == 0 && !isset($group_id)) {
			$message = lang('dispatch_account_batch.condition_setting.message');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		} else {
			$group_name = $this->input->post('group_name');
			$group_level_count = $this->input->post('group_level_count');
			$group_description = $this->input->post('group_description');
			$level_observation_period = $this->input->post('level_observation_period');
			$level_member_limit = $this->input->post('level_member_limit');
			$level_single_max_deposit = $this->input->post('level_single_max_deposit');
			$level_total_deposit = $this->input->post('level_total_deposit');
			$level_deposit_count = $this->input->post('level_deposit_count');
			$level_total_withdraw = $this->input->post('level_total_withdraw');
			$level_withdraw_count = $this->input->post('level_withdraw_count');

			$today = $this->utils->getNowForMysql();

			if(isset($group_id)) {
				$data = array(
					'group_name' => ucfirst($group_name),
					'group_description' => $group_description,
					'updated_at' => $today,
				);
				$this->dispatch_account->editDispatchAccountGroup($data, $group_id);
				$message = lang('con.vsm04') . " <b>" . lang($group_name) . "</b> " . lang('con.vsm05');

				$data = array(
					'username' => $this->authentication->getUsername(),
					'management' => 'Edit Dispatch Account Management',
					'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
					'action' => 'Edit Dispatch Account Group Name: ' . $group_name,
					'description' => "User " . $this->authentication->getUsername() . " edit dispatch account group id: " . $group_id,
					'logDate' => $today,
					'status' => 0,
				);

				$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Edit Dispatch Account Group Name: ' . $group_name,
					"User " . $this->authentication->getUsername() . " edit dispatch account group id: " . $group_id);
			} else {
				if ($this->input->post('group_level_count') <= 0) {
					$message = lang('con.vsm07');
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
					redirect('dispatch_account_management/dispatchAccountGroupList');
				} else {
					$data = array(
						'group_name' => $group_name,
						'group_level_count' => $group_level_count,
						'group_description' => $group_description,
						'level_member_limit' => $level_member_limit,
						'level_single_max_deposit' => $level_single_max_deposit,
						'level_total_deposit' => $level_total_deposit,
						'level_deposit_count' => $level_deposit_count,
						'level_total_withdraw' => $level_total_withdraw,
						'level_withdraw_count' => $level_withdraw_count,
						'created_at' => $today,
						'updated_at' => $today,
						'level_observation_period' => $level_observation_period,
					);
					$this->dispatch_account->addDispatchAccountGroup($data);
					$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Add Dispatch Account Group Name: ' . $group_name,
						"User " . $this->authentication->getUsername() . " add new dispatch account");
					$message = lang('con.vsm04') . " <b>" . lang($group_name) . "</b> " . lang('con.vsm08');
				}
			}

			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		}

		redirect('dispatch_account_management/dispatchAccountGroupList');
	}

	/**
	 * Delete dispatch account group
	 *
	 * @param 	int
	 * @return	redirect
	 */
	public function fakeDeleteDispatchAccountGroup($group_id) {
		$level_list = $this->dispatch_account->getDispatchAccountLevelListByGroupId($group_id);
		$level_id_list = array();
		$player_list = array();
		$message = '';

		if(!$level_list) {
			$this->dispatch_account->setDispatchAccountGroupDisabled($group_id, $level_id_list);
		}
		else {
			foreach ($level_list as $level) {
				$level_id_list[] = $level['id'];
				$player_list_in_level = $this->dispatch_account->getPlayerListInLevelByLevelId($level['id']);
				if(count($player_list_in_level) > 0) {
					$player_list[$level['level_name']] = array();
					foreach ($player_list_in_level as $player) {
						$player_list[$level['level_name']][] = $player;
					}
				}
			}
			$this->utils->debug_log('============fakeDeleteDispatchAccountGroup player_list', $player_list);

			if(!empty($player_list)) {
				$message = lang('Cannot delete beacause there are still players in the levels of this group. ');
				$message .= lang('Player List: ').'<br>';
				foreach ($player_list as $level_name => $username_list) {
					$message .= $level_name.": ";
					foreach ($username_list as $row) {
						$message .= $row['username'].', ';
					}
					$message .= '<br>';
				}

				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				redirect('dispatch_account_management/dispatchAccountGroupList');
			}
			else {
				$this->dispatch_account->setDispatchAccountGroupDisabled($group_id, $level_id_list);
			}
		}

		$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Delete dispatch account group', "User " . $this->authentication->getUsername() . " delete dispatch account group id: " . $group_id);

		$message = lang('dispatch_account_group.successfully_deleted');
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		redirect('dispatch_account_management/dispatchAccountGroupList');
	}

	/**
	 * edit dispatch account group
	 *
	 * @return	rendered template
	 */
	public function editDispatchAccountLevel() {
		if($this->input->post('level_id')) {
			$level_id = $this->input->post('level_id');
		}
		$level_order = $this->dispatch_account->getDispatchAccountLevelDetailsById($level_id)['level_order'];
		$is_reset_level = ($level_order == 0) ? TRUE :FALSE;


		$min_member_limit = ($this->CI->config->item('dispatch_account_min_member_limit')) ? $this->CI->config->item('dispatch_account_min_member_limit') : 100;
		if(isset($level_id)) {
			$this->form_validation->set_rules('level_name', 'Level Name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('paymentAccounts[]', 'Payment Accounts', 'trim|xss_clean');
			$this->form_validation->set_rules('level_observation_period', 'Level Observation Period', 'trim|required|xss_clean|is_numeric');
			if(!$is_reset_level){
				$this->form_validation->set_rules('level_member_limit', 'Level Member Limit', 'trim|required|xss_clean|is_numeric|greater_than_equal_to['.$min_member_limit.']');
				$this->form_validation->set_rules('level_single_max_deposit', 'Level Single Max Deposit', 'trim|xss_clean|is_numeric');
				$this->form_validation->set_rules('level_total_deposit', 'Level Total Deposit', 'trim|xss_clean|is_numeric');
				$this->form_validation->set_rules('level_deposit_count', 'Level Deposit Count', 'trim|xss_clean|is_numeric');
				$this->form_validation->set_rules('level_total_withdraw', 'Level Total Withdraw', 'trim|xss_clean|is_numeric');
				$this->form_validation->set_rules('level_withdraw_count', 'Level Withdraw Count', 'trim|xss_clean|is_numeric');
			}
		}

		$single_max_deposit = $this->input->post('level_single_max_deposit');
		$total_deposit = $this->input->post('level_total_deposit');
		$deposit_count = $this->input->post('level_deposit_count');
		$total_withdraw = $this->input->post('level_total_withdraw');
		$withdraw_count = $this->input->post('level_withdraw_count');
		$upgrade_condition = $single_max_deposit+$total_deposit+$deposit_count+$total_withdraw+$withdraw_count;

		if ($this->form_validation->run() == false) {
			$message = lang('con.vsm03');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			redirect('dispatch_account_management/getDispatchAccountLevel/'.$level_id);
		} elseif ($upgrade_condition == 0 && !$is_reset_level) {
			$message = lang('dispatch_account_batch.condition_setting.message');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			redirect('dispatch_account_management/getDispatchAccountLevel/'.$level_id);
		} else {
			$group_id = $this->input->post('group_id');
			$level_name = $this->input->post('level_name');
			$payment_accounts 	= $this->input->post('paymentAccounts') ? : array();
			$level_observation_period = $this->input->post('level_observation_period');
			$level_member_limit = $this->input->post('level_member_limit') ? : '0';
			$level_single_max_deposit = $this->input->post('level_single_max_deposit') ? : '0';
			$level_total_deposit = $this->input->post('level_total_deposit') ? : '0';
			$level_deposit_count = $this->input->post('level_deposit_count') ? : '0';
			$level_total_withdraw = $this->input->post('level_total_withdraw') ? : '0';
			$level_withdraw_count = $this->input->post('level_withdraw_count') ? : '0';

			$today = $this->utils->getNowForMysql();
			$this->utils->debug_log('============editDispatchAccountLevel payment_accounts', $payment_accounts);
			if(!isset($level_id)) {
			} else {
				$data = array(
					'level_name' => $level_name,
					'payment_accounts' => $payment_accounts,
					'level_member_limit' => $level_member_limit,
					'level_single_max_deposit' => $level_single_max_deposit,
					'level_total_deposit' => $level_total_deposit,
					'level_deposit_count' => $level_deposit_count,
					'level_total_withdraw' => $level_total_withdraw,
					'level_withdraw_count' => $level_withdraw_count,
					'updated_at' => $today,
					'level_observation_period' => $level_observation_period
				);
				$this->utils->debug_log('============editDispatchAccountLevel data', $data);
				$editDispatchAccount = $this->dispatch_account->editDispatchAccountLevel($data, $level_id);
				$this->utils->debug_log('============editDispatchAccountLevel editDispatchAccount', $editDispatchAccount);
				$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Edit Dispatch Account Level Name',
					"User " . $this->authentication->getUsername() . " edit dispatch Account levle id: " . $level_id);
			}

			if($editDispatchAccount){
				$message = lang('con.vsm04') . " <b>" . lang($level_name) . "</b> " . lang('con.vsm08');
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			}else{
				$message = lang('con.vsm04') . " <b>" . lang($level_name) . "</b> " . lang('con.editfailed');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			}
		}

		redirect('dispatch_account_management/getDispatchAccountLevelList/'.$group_id);
	}

	/**
	 * create new dispatch account level
	 *
	 * @param 	array
	 */
	public function increaseDispatchAccountLevel($group_id) {
		$new_level_id = $this->dispatch_account->copyDispatchAccountLevelByGroupId($group_id);
		if(!$new_level_id) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('dispatch_account_level.added_failed'));
		}
		else{
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('dispatch_account_level.successfully_added'));
		}

		redirect('/dispatch_account_management/getDispatchAccountLevelList/' . $group_id);
	}

	/**
	 * Delete dispatch account level
	 * @param 	int
	 * @return	loaded view page
	 */
	public function deleteDispatchAccountLevel($level_id, $group_id) {
		$player_list_in_level = $this->dispatch_account->getPlayerListInLevelByLevelId($level_id);

		if(count($player_list_in_level) <= 0) {
			$this->utils->debug_log('============deleteDispatchAccountLevel', $level_id, $group_id);
			$result = $this->dispatch_account->setDispatchAccountLevelDisabledByLevelId($level_id, $group_id);
		}
		else {
			$message = lang('Cannot delete beacause there are still players in this level. ');
			$message .= lang('Player List: ').'<br>';
			foreach ($player_list_in_level as $player) {
				$message .= $player['username'].'<br>';
			}

			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		}

		redirect('/dispatch_account_management/getDispatchAccountLevelList/' . $group_id);
	}

	/**
	 * get the payment accounts belongs to the dispatch account level
	 *
	 * @param 	level_id
	 * @return	redirect
	 */
	public function getCorrespondPaymentAccounts($level_id) {
		$this->returnJsonResult($this->dispatch_account->getCorrespondPaymentAccountsByLevelId($level_id));
	}

	public function refreshPlayersDispatchAccountLevel() {
		$success = true;
		$is_blocked=false;

		$this->triggerGenerateCommandEvent('refreshPlayersDispatchAccountLevel', [], $is_blocked);

		$this->returnJsonResult(array(
			'success' => true,
			'msg' => lang('dispatch_account_level.running_refresh_players_dispatch_account_level')
		));
	}

	public function checkIsDispatchAccountLevelMemberFull($level_id, $level_member_limit=null) {
		$current_level_member_count = $this->player_model->getPlayerCountByDispatchAccountLevelId($level_id);

		if(is_null($level_member_limit)) {
			$current_level = $this->dispatch_account->getDispatchAccountLevelDetailsById($level_id);
			if(!empty($current_level)) {
				$level_member_limit = $current_level['level_member_limit'];
			}
			else {
				return false;
			}
		}

		return $current_level_member_count >= $level_member_limit ? true : false;
	}

	/**
	 * get player list in the level
	 * @param 	level_id
	 * @return	json
	 */
	public function getPlayerListInLevel($level_id) {
		$player_list_in_level = $this->dispatch_account->getPlayerListInLevelByLevelId($level_id);
		$this->returnJsonResult($player_list_in_level);
	}

	/**
	 * reset players of the level to the same group reset level
	 * @param 	level_id
	 * @return	loaded view page
	 */
	public function setPlayersToResetLevel($level_id) {
		$result = false;
		$error_message = '';
		$player_list_in_level = $this->dispatch_account->getPlayerListInLevelByLevelId($level_id);
		$level = $this->dispatch_account->getDispatchAccountLevelDetailsById($level_id);

		if(!empty($level)) {
			$group_id = $level['group_id'];
			$reset_level_of_group = $this->dispatch_account->getZeroOrderLevelByGroupId($group_id);

			if(!empty($reset_level_of_group)) {
				foreach ($player_list_in_level as $player) {
					$this->player_model->adjustDispatchAccountLevel($player['playerId'], $reset_level_of_group['id']);
				}

				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Successfully set players to reset level.'));
				redirect('dispatch_account_management/getDispatchAccountLevelList/'.$group_id);
			}
			else {
				$error_message = lang('No Reset Level In This Group.');
			}
		}
		else {
			$error_message = lang('reset failed due to cannot find the current level.');
		}

		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $error_message);
		redirect('dispatch_account_management/dispatchAccountGroupList');
	}

	/**
	 * export report to excel
	 *
	 *
	 * @return	excel format
	 */
	public function exportToExcel() {
		$this->load->library('excel');
	}
}
