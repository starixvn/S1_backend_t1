<?php

/**
 * Bank Account Management
 *
 * Bank Account Management Controller for FHI OG System
 *
 * @deprecated
 *
 * @author  ASRII
 *
 */

class Bankaccount_Management extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('date_helper', 'url'));
		$this->load->library(array('permissions', 'excel', 'form_validation', 'template', 'bankaccount_manager', 'pagination', 'form_validation', 'report_functions'));

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
			'management' => 'Payment Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => $action,
			'description' => $description,
			'logDate' => $today,
			'status' => '0',
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
		$this->template->add_css('resources/css/bankaccount_management/style.css');
		$this->template->add_js('resources/js/payment_management/bankaccount_management.js');
		$this->template->add_js('resources/js/jquery.numeric.min.js');

		// $this->template->add_js('resources/js/moment.min.js');
		// $this->template->add_js('resources/js/daterangepicker.js');
		$this->template->add_js('resources/js/chosen.jquery.min.js');
		$this->template->add_js('resources/js/summernote.min.js');
		// $this->template->add_js('resources/js/bootstrap-datetimepicker.js');

		$this->template->add_js('resources/js/jquery.dataTables.min.js');
		$this->template->add_js('resources/js/dataTables.responsive.min.js');

		$this->template->add_css('resources/css/jquery.dataTables.css');
		$this->template->add_css('resources/css/dataTables.responsive.css');

		// $this->template->add_css('resources/css/daterangepicker-bs3.css');
		$this->template->add_css('resources/css/font-awesome.min.css');
		$this->template->add_css('resources/css/chosen.min.css');
		$this->template->add_css('resources/css/summernote.css');

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
		$this->loadTemplate('Bank Account Management', '', '', 'payment');

		$message = lang('con.bnk01');
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
		redirect(BASEURL . 'bankaccount_management/viewBankAccountManager', 'refresh');
	}

	/**
	 * view bank account manager
	 *
	 * @return Array
	 */

	public function viewBankAccountManager() {
		// if(!$this->permissions->checkPermissions('bankaccount_settings')){
		//     $this->error_access();
		// } else {

		if (($this->session->userdata('sidebar_status') == NULL)) {
			$this->session->set_userdata(array('sidebar_status' => 'active'));
		}

		$sort = "bankName";

		$data['count_all'] = count($this->bankaccount_manager->getAllOtcPaymentMethod($sort, null, null));
		$config['base_url'] = "javascript:get_bankaccount_pages(";
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
		$data['playerGroup'] = $this->bankaccount_manager->getPlayerGroup();

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['banks'] = $this->bankaccount_manager->getAllOtcPaymentMethod($sort, null, null);

		foreach ($this->bankaccount_manager->getAllPlayerLevels() as $level) {
			$data['levels'][$level['vipsettingcashbackruleId']] = $level['groupName'] . ' ' . $level['vipLevel'];
		}

		//export report permission checking
		if (!$this->permissions->checkPermissions('export_report')) {
			$data['export_report_permission'] = FALSE;
		} else {
			$data['export_report_permission'] = TRUE;
		}
		// var_dump($data['banks']);exit();

		$this->loadTemplate('Payment Management', '', '', 'payment');
		$this->template->write_view('sidebar', 'payment_management/sidebar');
		$this->template->write_view('main_content', 'payment_management/bankaccount/view_bankaccount_manager', $data);
		$this->template->render();
		//}
	}

	public function get_bankaccount_pages($segment) {
		$sort = "otcPaymentMethodId";

		$data['count_all'] = count($this->bankaccount_manager->getAllOtcPaymentMethod($sort, null, null));
		$config['base_url'] = "javascript:get_bankaccount_pages(";
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
		$data['banks'] = $this->bankaccount_manager->getAllOtcPaymentMethod($sort, $config['per_page'], $segment);

		$this->load->view('payment_management/bankaccount/ajax_view_bankaccount_setting_list', $data);
	}

	/**
	 * sort bank account
	 *
	 * @param 	sort
	 * @return	void
	 */
	public function sortBankAccount($sort) {
		$data['count_all'] = count($this->bankaccount_manager->getAllOtcPaymentMethod($sort, null, null));
		$config['base_url'] = "javascript:get_bankaccount_pages(";
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
		$data['banks'] = $this->bankaccount_manager->getAllOtcPaymentMethod($sort, $config['per_page'], null);

		$this->load->view('payment_management/bankaccount/ajax_view_bankaccount_setting_list', $data);
	}

	/**
	 * search bank account
	 *
	 *
	 * @return	redirect page
	 */
	public function searchBankAccount($search = '') {
		$data['count_all'] = count($this->bankaccount_manager->searchBankAccountList($search, null, null));
		$config['base_url'] = "javascript:get_bankaccount_pages(";
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
		$data['banks'] = $this->bankaccount_manager->searchBankAccountList($search, $config['per_page'], null);

		//export report permission checking
		if (!$this->permissions->checkPermissions('export_report')) {
			$data['export_report_permission'] = FALSE;
		} else {
			$data['export_report_permission'] = TRUE;
		}

		$this->load->view('payment_management/bankaccount/ajax_view_bankaccount_setting_list', $data);
	}

	/**
	 * export report to excel
	 *
	 * @return	excel format
	 */
	public function exportToExcel() {
		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'Bank Account Setting Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Exported Bank Account List',
			'description' => "User " . $this->authentication->getUsername() . " exported Bank Account List",
			'logDate' => date("Y-m-d H:i:s"),
			'status' => '0',
		);

		$this->report_functions->recordAction($data);

		$result = $this->bankaccount_manager->getBankAccountListToExport();
		//var_dump($result);exit();
		$this->excel->to_excel($result, 'bankaccountlist-excel');
	}

	/**
	 *add bank account
	 *
	 * @param $bankAccountId int
	 * @return	rendered template
	 */
	public function bankAccountBackupManager($bankAccountId, $bankName) {
		// if(!$this->permissions->checkPermissions('cms_addBankAccountBackup_settings')){
		//     $this->error_access();
		// } else {
		$data['bankAccountId'] = $bankAccountId;
		$data['bankName'] = $bankName;

		$this->loadTemplate('Payment Management', '', '', 'payment');
		$this->template->write_view('sidebar', 'payment_management/sidebar');
		$this->template->write_view('main_content', 'payment_management/bankaccount/view_bankaccountbackup_manager', $data);
		$this->template->render();
		// }
	}

	/**
	 * add/edit Bank account setting
	 *
	 * @return	array
	 */
	public function addBankAccount() {
		$this->form_validation->set_rules('bankName', 'Bank Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('branchName', 'Branch Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('accountNumber', 'Account Number', 'trim|required|xss_clean');
		$this->form_validation->set_rules('accountName', 'Account Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('transactionFee', 'Transaction Fee', 'trim|required|xss_clean|numeric');
		$this->form_validation->set_rules('dailyMaxDepositAmount', 'Daily Max Deposit Amount', 'trim|required|xss_clean|numeric');
		$this->form_validation->set_rules('description', 'Description', 'trim|required|xss_clean');
		//$this->form_validation->set_rules('playerLevels', 'Player Levels' , 'trim|required|xss_clean');
		//var_dump($playerLevels);exit();
		if ($this->form_validation->run() == false) {
			$message = lang('con.bnk02');
			$this->alertMessage(2, $message);
			$this->viewBankAccountManager();
		} else {
			$lastOrderCnt = $this->bankaccount_manager->getBankAccountLastRankOrder();
			//var_dump($lastOrderCnt[0]['accountOrder']);exit();
			$accountOrder = $lastOrderCnt[0]['accountOrder'] + 1;
			$bankName = $this->input->post('bankName');
			$branchName = $this->input->post('branchName');
			$accountNumber = $this->input->post('accountNumber');
			$accountName = $this->input->post('accountName');
			$dailyMaxDepositAmount = $this->input->post('dailyMaxDepositAmount');
			$description = $this->input->post('description');
			$otcPaymentMethodId = $this->input->post('otcPaymentMethodId');
			$playerLevels = $this->input->post('playerLevels');
			$bankAccountId = $this->input->post('bankAccountId');
			$transactionFee = $this->input->post('transactionFee');
			$today = date("Y-m-d H:i:s");

			if ($bankAccountId) {

				$data = array(
					'bankName' => $bankName,
					'branchName' => $branchName,
					'accountNumber' => $accountNumber,
					'accountName' => $accountName,
					'transactionFee' => $transactionFee,
					'accountOrder' => $accountOrder,
					'dailyMaxDepositAmount' => $dailyMaxDepositAmount,
					'description' => $description,
					'updatedOn' => $today,
					'updatedBy' => $this->authentication->getUserId(),
				);

				$this->bankaccount_manager->editBankAccount($data, $playerLevels, $bankAccountId);
				$message = lang('con.bnk03') . " <b>" . $bankName . "</b> " . lang('con.bnk04');
				$this->saveAction('Edit Bank Account', "User " . $this->authentication->getUsername() . " has edited Bank Account " . $bankName . ".");
			} else {

				$data = array(
					'bankName' => $bankName,
					'branchName' => $branchName,
					'accountNumber' => $accountNumber,
					'accountName' => $accountName,
					'dailyMaxDepositAmount' => $dailyMaxDepositAmount,
					'description' => $description,
					'accountOrder' => $accountOrder,
					'transactionFee' => $transactionFee,
					'createdOn' => $today,
					'createdBy' => $this->authentication->getUserId(),
					'status' => 'active',
				);

				$this->bankaccount_manager->addBankAccount($data, $playerLevels);
				$message = lang('con.bnk03') . " <b>" . $bankName . "</b> " . lang('con.bnk05');
				$this->saveAction('Add Bank Account ', "User " . $this->authentication->getUsername() . " has added new bank account " . $bankName . ".");
			}

			$this->alertMessage(1, $message);
			redirect(BASEURL . 'bankaccount_management/viewBankAccountManager', 'refresh');
		}
	}

	/**
	 * add/edit Bank account setting
	 *
	 * @return	array
	 */
	public function addBankAccountBackup() {
		$this->form_validation->set_rules('bankName', 'Bank Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('branchName', 'Branch Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('accountNumber', 'Account Number', 'trim|required|xss_clean');
		$this->form_validation->set_rules('accountName', 'Account Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('dailyMaxDepositAmount', 'Daily Max Deposit Amount', 'trim|required|xss_clean|numeric');
		$this->form_validation->set_rules('description', 'Description', 'trim|required|xss_clean');
		//var_dump($playerLevels);exit();
		if ($this->form_validation->run() == false) {
			$this->viewBankAccountManager();
		} else {
			$lastOrderCnt = $this->bankaccount_manager->getBankAccountBackupLastRankOrder();
			//var_dump($lastOrderCnt[0]['accountOrder']);exit();
			$accountOrder = $lastOrderCnt[0]['accountOrder'] + 1;
			$bankName = $this->input->post('bankName');
			$branchName = $this->input->post('branchName');
			$accountNumber = $this->input->post('accountNumber');
			$accountName = $this->input->post('accountName');
			$description = $this->input->post('description');
			$bankAccountId = $this->input->post('bankAccountId');
			$bankAccountBackupId = $this->input->post('bankAccountBackupId');
			$today = date("Y-m-d H:i:s");

			if ($bankAccountBackupId) {

				$data = array(
					'bankName' => $bankName,
					'branchName' => $branchName,
					'accountNumber' => $accountNumber,
					'accountName' => $accountName,
					'accountOrder' => $accountOrder,
					'dailyMaxDepositAmount' => $dailyMaxDepositAmount,
					'description' => $description,
					'updatedOn' => $today,
					'updatedBy' => $this->authentication->getUserId(),
				);

				$this->bankaccount_manager->editBankAccountBackup($data, $bankAccountId);
				$message = lang('con.bnk03') . " <b>" . $bankName . "</b> " . lang('con.bnk04');
				$this->saveAction('Edit Bank Account', "User " . $this->authentication->getUsername() . " has edited Bank Account " . $bankName . ".");
			} else {

				$data = array(
					'bankName' => $bankName,
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

				$this->bankaccount_manager->addBankAccountBackup($data);
				$message = lang('con.bnk06') . " <b>" . $bankName . "</b> " . lang('con.bnk05');
				$this->saveAction('Add Bank Account Backup', "User " . $this->authentication->getUsername() . " has added new bank account backup " . $bankName . ".");
			}

			$this->alertMessage(1, $message);
			redirect(BASEURL . 'bankaccount_management/viewBankAccountManager');
		}
	}

	/**
	 * get bank account details
	 *
	 * @param 	bankAccountId
	 * @return	redirect
	 */
	public function getBankAccountDetails($bankAccountId) {
		echo json_encode($this->bankaccount_manager->getBankAccountDetails($bankAccountId));
	}

	/**
	 * Delete bank account
	 *
	 * @param 	int
	 * @return	redirect
	 */
	public function deleteSelectedBankAccount() {
		$bankaccount = $this->input->post('bankaccount');
		$today = date("Y-m-d H:i:s");

		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'Bank account Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Delete Selected Bank Account',
			'description' => "User " . $this->authentication->getUsername() . " deleted selected bank account.",
			'logDate' => $today,
			'status' => '0',
		);

		$this->report_functions->recordAction($data);

		if ($bankaccount != '') {
			foreach ($bankaccount as $bankaccountId) {
				$this->bankaccount_manager->deleteBankAccounts($bankaccountId);
				$this->bankaccount_manager->deleteBankAccountItem($bankaccountId);
			}

			$message = lang('con.bnk07');
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect(BASEURL . 'bankaccount_management/viewBankAccountManager');
		} else {
			$message = lang('con.bnk08');
			$this->alertMessage(2, $message);
			redirect(BASEURL . 'bankaccount_management/viewBankAccountManager');
		}
	}

	/**
	 * Delete VIP group level
	 *
	 * @param 	vipgrouplevelId
	 * @return	redirect
	 */
	public function deleteBankAccountItem($vipgrouplevelId) {
		// if(!$this->permissions->checkPermissions('delete_bankaccount')){
		// 	$this->error_access();
		// } else {
		$this->bankaccount_manager->deleteBankAccountItem($vipgrouplevelId);

		$today = date("Y-m-d H:i:s");
		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'VIP Setting Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Delete Chat History',
			'description' => "User " . $this->authentication->getUsername() . " deleted vip group level",
			'logDate' => $today,
			'status' => '0',
		);

		$this->report_functions->recordAction($data);

		$message = lang('con.bnk09');
		$this->alertMessage(1, $message);
		redirect(BASEURL . 'bankaccount_management/viewBankAccountManager', 'refresh');
		//}
	}

	/**
	 * activate vip group
	 *
	 * @param 	bankAccountId
	 * @param 	status
	 * @return	redirect
	 */
	public function activateBankAccount($bankAccountId, $status) {
		$data['otcPaymentMethodId'] = $bankAccountId;
		$data['status'] = $status;
		$data['updatedOn'] = date("Y-m-d H:i:s");
		$data['updatedBy'] = $this->authentication->getUserId();

		$this->bankaccount_manager->activateBankAccount($data);

		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'Bank Account Setting Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Update status of bank account id: ' . $bankAccountId . 'to status:' . $status,
			'description' => "User " . $this->authentication->getUsername() . " edit bank account status to " . $status,
			'logDate' => date("Y-m-d H:i:s"),
			'status' => '0',
		);

		$this->report_functions->recordAction($data);

		redirect(BASEURL . 'bankaccount_management/viewBankAccountManager');
	}
}