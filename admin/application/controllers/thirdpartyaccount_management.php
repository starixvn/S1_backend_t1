<?php

/**
 * Third Party Account Management
 *
 * Third Party Account Management Controller for FHI OG System
 *
 * @author  ASRII
 *
 */

class Thirdpartyaccount_Management extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('date_helper', 'url'));
		$this->load->library(array('permissions', 'excel', 'form_validation', 'template', 'thirdpartyaccount_manager', 'pagination', 'form_validation', 'report_functions'));

		$this->permissions->checkSettings();
		$this->permissions->setPermissions(); //will set the permission for the logged in user
	}

	/**
	 * save action to Logs
	 *
	 * @return	rendered Template
	 */
	private function saveAction($action, $description) {
		$today = date("Y-m-d H:i:s");

		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => '3rd Party Account Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => $action,
			'description' => $description,
			'logDate' => $today,
			'status' => 0,
		);

		$this->report_functions->recordAction($data);
	}

	/**
	 * set message for users
	 *
	 * @param	int
	 * @param   string
	 * @return  set session user data
	 */
	public function alertMessage($type, $message) {
		switch ($type) {
			case '1':
				$show_message = array(
					'result' => 'success',
					'message' => $message,
				);
				$this->session->set_userdata($show_message);
				break;

			case '2':
				$show_message = array(
					'result' => 'danger',
					'message' => $message,
				);
				$this->session->set_userdata($show_message);
				break;

			case '3':
				$show_message = array(
					'result' => 'warning',
					'message' => $message,
				);
				$this->session->set_userdata($show_message);
				break;
		}
	}

	/**
	 * Loads template for view based on regions in
	 * config > template.php
	 *
	 */
	private function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->add_css('resources/css/thirdpartyaccount_management/style.css');
		$this->template->add_js('resources/js/payment_management/thirdpartyaccount_management.js');
		$this->template->add_js('resources/js/jquery.numeric.min.js');

		// $this->template->add_js('resources/js/moment.min.js');
		// $this->template->add_js('resources/js/daterangepicker.js');
		$this->template->add_js('resources/js/chosen.jquery.min.js');
		$this->template->add_js('resources/js/summernote.min.js');
		// $this->template->add_js('resources/js/bootstrap-datetimepicker.js');

		// $this->template->add_css('resources/css/daterangepicker-bs3.css');
		// $this->template->add_css('resources/css/font-awesome.min.css');
		$this->template->add_css('resources/css/chosen.min.css');
		$this->template->add_css('resources/css/summernote.css');

		$this->template->add_js('resources/js/jquery.dataTables.min.js');
		$this->template->add_js('resources/js/dataTables.responsive.min.js');
		$this->template->add_css('resources/css/jquery.dataTables.css');
		$this->template->add_css('resources/css/dataTables.responsive.css');

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
		$this->loadTemplate('Third Party Account Management', '', '', 'payment');

		$message = lang('con.tpam01');
		$this->alertMessage(2, $message);

		$this->template->render();
	}

	/**
	 * Index Page of Payment Management
	 *
	 *
	 * @return	void
	 */
	public function index() {
		redirect(BASEURL . 'thirdpartyaccount_management/viewThirdPartyAccountManager', 'refresh');
	}

	/**
	 * view thirdparty account manager
	 *
	 * @return Array
	 */

	public function viewThirdPartyAccountManager() {
		// if(!$this->permissions->checkPermissions('thirdPartyaccount_settings')){
		//     $this->error_access();
		// } else {

		$sort = "thirdPartyName";

		$data['count_all'] = count($this->thirdpartyaccount_manager->getAllThirdPartyPaymentMethodAccount($sort, null, null));
		$config['base_url'] = "javascript:get_thirdpartyaccount_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 10;
		$config['num_links'] = 2;

		$config['first_tag_open'] = '<li>';
		$config['last_tag_open'] = '<li>';
		$config['next_tag_open'] = '<li>';
		$config['prev_tag_open'] = '<li>';
		$config['num_tag_open'] = '<li>';

		$config['first_tag_close'] = '</li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_close'] = '</li>';
		$config['num_tag_close'] = '</li>';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$this->pagination->initialize($config);

		$data['form'] = &$form;
		$data['playerGroup'] = $this->thirdpartyaccount_manager->getPlayerGroup();

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['thirdparty'] = $this->thirdpartyaccount_manager->getAllThirdPartyPaymentMethodAccount($sort, null, null);
		//var_dump($data);exit();
		foreach ($this->thirdpartyaccount_manager->getAllPlayerLevels() as $level) {
			$data['levels'][$level['vipsettingcashbackruleId']] = $level['groupName'] . ' ' . $level['vipLevel'];
		}
		//var_dump($data);exit();
		//export report permission checking
		if (!$this->permissions->checkPermissions('export_report')) {
			$data['export_report_permission'] = FALSE;
		} else {
			$data['export_report_permission'] = TRUE;
		}
		// var_dump($data['thirdparty']);exit();
		$this->loadTemplate('3rd Party Account Management', '', '', 'payment');
		$this->template->write_view('sidebar', 'payment_management/sidebar');
		$this->template->write_view('main_content', 'payment_management/thirdparty/view_thirdpartyaccount_manager', $data);
		$this->template->render();
		//}
	}

	public function get_thirdpartyaccount_pages($segment) {
		$sort = "thirdPartyName";

		$data['count_all'] = count($this->thirdpartyaccount_manager->getAllThirdPartyPaymentMethodAccount($sort, null, null));
		$config['base_url'] = "javascript:get_thirdpartyaccount_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 10;
		$config['num_links'] = 2;

		$config['first_tag_open'] = '<li>';
		$config['last_tag_open'] = '<li>';
		$config['next_tag_open'] = '<li>';
		$config['prev_tag_open'] = '<li>';
		$config['num_tag_open'] = '<li>';

		$config['first_tag_close'] = '</li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_close'] = '</li>';
		$config['num_tag_close'] = '</li>';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$this->pagination->initialize($config);

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['thirdparty'] = $this->thirdpartyaccount_manager->getAllThirdPartyPaymentMethodAccount($sort, $config['per_page'], $segment);

		$this->load->view('payment_management/thirdparty/ajax_view_thirdpartyaccount_setting_list', $data);
	}

	/**
	 * sort third party account
	 *
	 * @param 	sort
	 * @return	void
	 */
	public function sortThirdPartyAccount($sort) {
		$data['count_all'] = count($this->thirdpartyaccount_manager->getAllOtcPaymentMethod($sort, null, null));
		$config['base_url'] = "javascript:get_thirdpartyaccount_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 10;
		$config['num_links'] = 2;

		$config['first_tag_open'] = '<li>';
		$config['last_tag_open'] = '<li>';
		$config['next_tag_open'] = '<li>';
		$config['prev_tag_open'] = '<li>';
		$config['num_tag_open'] = '<li>';

		$config['first_tag_close'] = '</li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_close'] = '</li>';
		$config['num_tag_close'] = '</li>';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$this->pagination->initialize($config);

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['thirdparty'] = $this->thirdpartyaccount_manager->getAllOtcPaymentMethod($sort, $config['per_page'], null);

		$this->load->view('payment_management/thirdparty/ajax_view_thirdpartyaccount_setting_list', $data);
	}

	/**
	 * search thirdparty account
	 *
	 *
	 * @return	redirect page
	 */
	public function searchThirdPartyAccount($search = '') {
		$data['count_all'] = count($this->thirdpartyaccount_manager->searchThirdPartyAccountList($search, null, null));
		$config['base_url'] = "javascript:get_thirdpartyaccount_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 10;
		$config['num_links'] = 2;

		$config['first_tag_open'] = '<li>';
		$config['last_tag_open'] = '<li>';
		$config['next_tag_open'] = '<li>';
		$config['prev_tag_open'] = '<li>';
		$config['num_tag_open'] = '<li>';

		$config['first_tag_close'] = '</li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_close'] = '</li>';
		$config['num_tag_close'] = '</li>';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$this->pagination->initialize($config);

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['thirdparty'] = $this->thirdpartyaccount_manager->searchThirdpartyAccountList($search, $config['per_page'], null);

		//export report permission checking
		if (!$this->permissions->checkPermissions('export_report')) {
			$data['export_report_permission'] = FALSE;
		} else {
			$data['export_report_permission'] = TRUE;
		}

		$this->load->view('payment_management/thirdparty/ajax_view_thirdpartyaccount_setting_list', $data);
	}

	/**
	 * export report to excel
	 *
	 *
	 * @return	excel format
	 */
	public function exportToExcel() {
		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'Third Party Account Setting Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Exported ThirdParty Account List',
			'description' => "User " . $this->authentication->getUsername() . " exported Third Party Account List",
			'logDate' => date("Y-m-d H:i:s"),
			'status' => 0,
		);

		$this->report_functions->recordAction($data);

		$result = $this->thirdpartyaccount_manager->getThirdPartyAccountListToExport();
		//var_dump($result);exit();
		$this->excel->to_excel($result, 'thirdpartyaccountlist-excel');
	}

	/**
	 *add thirdparty account
	 *
	 * @param $thirdpartyAccountId int
	 * @return	rendered template
	 */
	public function thirdpartyAccountBackupManager($thirdPartyAccountId, $thirdPartyName) {
		// if(!$this->permissions->checkPermissions('cms_addThirdPartyAccountBackup_settings')){
		//     $this->error_access();
		// } else {
		$data['thirdPartyAccountId'] = $thirdPartyAccountId;
		$data['thirdPartyName'] = $thirdPartyName;

		$this->loadTemplate('3rd Party Account Management', '', '', 'payment');
		$this->template->write_view('sidebar', 'payment_management/sidebar');
		$this->template->write_view('main_content', 'payment_management/thirdpartyaccount/view_thirdpartyaccountbackup_manager', $data);
		$this->template->render();
		// }
	}

	/**
	 * add/edit ThirdParty account setting
	 *
	 * @return	array
	 */
	public function addThirdPartyAccount() {
		$this->form_validation->set_rules('thirdPartyName', 'ThirdParty Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('thirdPartyAccountName', '3rd Party Account Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('transactionFee', 'Transaction Fee', 'trim|required|xss_clean');
		$this->form_validation->set_rules('dailyMaxDepositAmount', 'Daily Max Deposit Amount', 'trim|required|xss_clean|numeric');
		$this->form_validation->set_rules('description', 'Description', 'trim|required|xss_clean');
		$this->form_validation->set_rules('playerLevels', 'Player Levels', 'required');
		if ($this->form_validation->run() == false) {
			$message = lang('con.tpam02');
			$this->alertMessage(2, $message);
			$this->viewThirdPartyAccountManager();
		} else {
			$lastOrderCnt = $this->thirdpartyaccount_manager->getThirdPartyAccountLastRankOrder();
			//var_dump($lastOrderCnt[0]['accountOrder']);exit();
			$accountOrder = $lastOrderCnt[0]['accountOrder'] + 1;
			$thirdPartyName = $this->input->post('thirdPartyName');
			$thirdPartyAccountName = $this->input->post('thirdPartyAccountName');
			$transactionFee = $this->input->post('transactionFee');
			$dailyMaxDepositAmount = $this->input->post('dailyMaxDepositAmount');
			$description = $this->input->post('description');
			$playerLevels = $this->input->post('playerLevels');
			$thirdPartyAccountId = $this->input->post('thirdPartyAccountId');
			$today = date("Y-m-d H:i:s");

			if ($thirdPartyAccountId) {
				//var_dump($thirdPartyAccountId);exit();
				$data = array(
					'thirdPartyName' => $thirdPartyName,
					'thirdPartyAccountName' => $thirdPartyAccountName,
					'transactionFee' => $transactionFee,
					'dailyMaxDepositAmount' => $dailyMaxDepositAmount,
					'description' => $description,
					'dailyMaxDepositAmount' => $dailyMaxDepositAmount,
					'description' => $description,
					'updatedOn' => $today,
					'updatedBy' => $this->authentication->getUserId(),
				);

				$this->thirdpartyaccount_manager->editThirdPartyAccount($data, $playerLevels, $thirdPartyAccountId);
				$message = lang('con.tpam03') . " <b>" . $thirdPartyName . "</b> " . lang('con.tpam04');
				$this->saveAction('Edit ThirdParty Account', "User " . $this->authentication->getUsername() . " has edited ThirdParty Account " . $thirdPartyName . ".");
			} else {

				$data = array(
					'thirdPartyName' => $thirdPartyName,
					'thirdPartyAccountName' => $thirdPartyAccountName,
					'transactionFee' => $transactionFee,
					'dailyMaxDepositAmount' => $dailyMaxDepositAmount,
					'description' => $description,
					'accountOrder' => $accountOrder,
					'createdOn' => $today,
					'createdBy' => $this->authentication->getUserId(),
					'status' => 'active',
				);
				//var_dump($data);exit();
				$this->thirdpartyaccount_manager->addThirdPartyAccount($data, $playerLevels);
				$message = lang('con.tpam03') . " <b>" . $thirdPartyName . "</b> " . lang('con.tpam05');
				$this->saveAction('Add ThirdParty Account ', "User " . $this->authentication->getUsername() . " has added new thirdParty account " . $thirdPartyName . ".");
			}

			$this->alertMessage(1, $message);
			redirect(BASEURL . 'thirdpartyaccount_management/viewThirdPartyAccountManager');
		}
	}

	/**
	 * add/edit Third party account setting
	 *
	 * @return	array
	 */
	public function addThirdPartyAccountBackup() {
		$this->form_validation->set_rules('thirdPartyName', 'ThirdParty Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('branchName', 'Branch Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('accountNumber', 'Account Number', 'trim|required|xss_clean');
		$this->form_validation->set_rules('accountName', 'Account Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('dailyMaxDepositAmount', 'Daily Max Deposit Amount', 'trim|required|xss_clean|numeric');
		$this->form_validation->set_rules('description', 'Description', 'trim|required|xss_clean');
		//var_dump($playerLevels);exit();
		if ($this->form_validation->run() == false) {
			$this->viewThirdPartyAccountManager();
		} else {
			$lastOrderCnt = $this->thirdpartyaccount_manager->getThirdPartyAccountBackupLastRankOrder();
			//var_dump($lastOrderCnt[0]['accountOrder']);exit();
			$accountOrder = $lastOrderCnt[0]['accountOrder'] + 1;
			$thirdPartyName = $this->input->post('thirdPartyName');
			$branchName = $this->input->post('branchName');
			$accountNumber = $this->input->post('accountNumber');
			$accountName = $this->input->post('accountName');
			$description = $this->input->post('description');
			$thirdPartyAccountId = $this->input->post('thirdPartyAccountId');
			$thirdPartyAccountBackupId = $this->input->post('thirdPartyAccountBackupId');
			$today = date("Y-m-d H:i:s");

			if ($thirdPartyAccountBackupId) {

				$data = array(
					'thirdPartyName' => $thirdPartyName,
					'branchName' => $branchName,
					'accountNumber' => $accountNumber,
					'accountName' => $accountName,
					'accountOrder' => $accountOrder,
					'dailyMaxDepositAmount' => $dailyMaxDepositAmount,
					'description' => $description,
					'updatedOn' => $today,
					'updatedBy' => $this->authentication->getUserId(),
				);

				$this->thirdpartyaccount_manager->editThirdPartyAccountBackup($data, $thirdPartyAccountId);
				$message = lang('con.tpam03') . " <b>" . $thirdPartyName . "</b> " . lang('con.tpam04');
				$this->saveAction('Edit Third Party Account', "User " . $this->authentication->getUsername() . " has edited ThirdParty Account " . $thirdPartyName . ".");
			} else {

				$data = array(
					'thirdPartyName' => $thirdPartyName,
					'branchName' => $branchName,
					'accountNumber' => $accountNumber,
					'accountName' => $accountName,
					'dailyMaxDepositAmount' => $dailyMaxDepositAmount,
					'description' => $description,
					'accountOrder' => $accountOrder,
					'createdOn' => $today,
					'createdBy' => $this->authentication->getUserId(),
					'status' => 'active',
				);

				$this->thirdpartyaccount_manager->addThirdPartyAccountBackup($data);
				$message = lang('con.tpam06') . " <b>" . $thirdPartyName . "</b> " . lang('con.tpam05');
				$this->saveAction('Add Third Party Account Backup', "User " . $this->authentication->getUsername() . " has added new thirdParty account backup " . $thirdPartyName . ".");
			}

			$this->alertMessage(1, $message);
			$this->viewThirdPartyAccountManager();
		}
	}

	/**
	 * get thirdParty account details
	 *
	 * @param 	thirdPartyAccountId
	 * @return	redirect
	 */
	public function getThirdPartyAccountDetails($thirdPartyAccountId) {
		echo json_encode($this->thirdpartyaccount_manager->getThirdPartyAccountDetails($thirdPartyAccountId));
	}

	/**
	 * Delete thirdParty account
	 *
	 * @param 	int
	 * @return	redirect
	 */
	public function deleteSelectedThirdPartyAccount() {
		$thirdPartyaccount = $this->input->post('thirdpartyaccount');
		$today = date("Y-m-d H:i:s");

		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'Third Party account Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Delete Selected ThirdParty Account',
			'description' => "User " . $this->authentication->getUsername() . " deleted selected third party account.",
			'logDate' => $today,
			'status' => 0,
		);

		$this->report_functions->recordAction($data);

		if ($thirdPartyaccount != '') {
			foreach ($thirdPartyaccount as $thirdpartyaccountId) {
				$this->thirdpartyaccount_manager->deleteThirdPartyAccounts($thirdpartyaccountId);
				$this->thirdpartyaccount_manager->deleteThirdPartyAccountItem($thirdpartyaccountId);
			}

			$message = lang('con.tpam07');
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect(BASEURL . 'thirdpartyaccount_management/viewThirdPartyAccountManager');
		} else {
			$message = lang('con.tpam08');
			$this->alertMessage(2, $message);
			redirect(BASEURL . 'thirdpartyaccount_management/viewThirdPartyAccountManager');
		}
	}

	/**
	 * Delete VIP group level
	 *
	 * @param 	vipgrouplevelId
	 * @return	redirect
	 */
	public function deleteThirdPartyAccountItem($thirdpartyaccountlevelId) {
		// if(!$this->permissions->checkPermissions('delete_thirdpartyaccount')){
		// 	$this->error_access();
		// } else {
		$this->thirdpartyaccount_manager->deleteThirdPartyAccountItem($thirdpartyaccountlevelId);

		$today = date("Y-m-d H:i:s");
		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'Third Party Account Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Delete third party account',
			'description' => "User " . $this->authentication->getUsername() . " deleted third party account",
			'logDate' => $today,
			'status' => 0,
		);

		$this->report_functions->recordAction($data);

		$message = lang('con.tpam09');
		$this->alertMessage(1, $message);
		redirect(BASEURL . 'thirdpartyaccount_management/viewThirdPartyAccountManager', 'refresh');
		//}
	}

	/**
	 * activate vip group
	 *
	 * @param 	thirdPartyAccountId
	 * @param 	status
	 * @return	redirect
	 */
	public function activateThirdPartyAccount($thirdPartyAccountId, $status) {
		$data['thirdpartypaymentmethodaccountId'] = $thirdPartyAccountId;
		$data['status'] = $status;
		$data['updatedOn'] = date("Y-m-d H:i:s");
		$data['updatedBy'] = $this->authentication->getUserId();

		$this->thirdpartyaccount_manager->activateThirdPartyAccount($data);

		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'Third Party Account Setting Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Update status of third party account id: ' . $thirdPartyAccountId . 'to status:' . $status,
			'description' => "User " . $this->authentication->getUsername() . " edit third party account status to " . $status,
			'logDate' => date("Y-m-d H:i:s"),
			'status' => 0,
		);

		$this->report_functions->recordAction($data);

		redirect(BASEURL . 'thirdpartyaccount_management/viewThirdPartyAccountManager');
	}
}