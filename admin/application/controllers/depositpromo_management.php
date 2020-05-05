<?php

/**
 *  Marketing Management - Deposit Promo
 *
 * Marketing Management Controller for FHI OG System
 *
 * @deprecated
 *
 * @author  ASRII
 *
 */

class Depositpromo_Management extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('date_helper','url'));
		$this->load->library(array('permissions','excel', 'form_validation', 'template', 'depositpromo_manager', 'pagination', 'form_validation', 'report_functions'));

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
			'management' => 'Marketing Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => $action,
			'description' => $description,
			'logDate' => $today,
			'status' => 0
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
   		$this->template->add_css('resources/css/depositpromo_management/style.css');
   		$this->template->add_js('resources/js/marketing_management/depositpromo_management.js');
   		$this->template->add_js('resources/js/jquery.numeric.min.js');

		$this->template->add_js('resources/js/moment.min.js');
		$this->template->add_js('resources/js/daterangepicker.js');
		$this->template->add_js('resources/js/chosen.jquery.min.js');
		$this->template->add_js('resources/js/summernote.min.js');
		$this->template->add_js('resources/js/bootstrap-datetimepicker.js');

		$this->template->add_css('resources/css/daterangepicker-bs3.css');
		$this->template->add_css('resources/css/font-awesome.min.css');
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
		$this->loadTemplate('Marketing Management', '', '', 'payment');

		$message = lang('con.d01');
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
		redirect(BASEURL . 'depositpromo_management/viewDepositPromoManager', 'refresh');
	}

	/**
       * view deposit promo manager
       *
       * @return Array
       */

	public function viewDepositPromoManager() {
		// if(!$this->permissions->checkPermissions('depositpromo_settings')){
        //     $this->error_access();
        // } else {

			$sort = "promoName";

			$data['form'] = &$form;
			$data['playerGroup'] = $this->depositpromo_manager->getPlayerGroup();

			$data['depositpromo'] = $this->depositpromo_manager->getAllDepositPromo($sort, null, null);

			foreach ($this->depositpromo_manager->getAllPlayerLevels() as $level) {
				$data['levels'][$level['vipsettingcashbackruleId']] = $level['groupName'].' '.$level['vipLevel'];
			}

			//export report permission checking
			if(!$this->permissions->checkPermissions('export_report')){
				$data['export_report_permission'] = FALSE;
			} else {
				$data['export_report_permission'] = TRUE;
			}

			$this->loadTemplate('Marketing Management', '', '', 'marketing');
			$this->template->write_view('sidebar', 'marketing_management/sidebar');
			$this->template->write_view('main_content', 'marketing_management/depositpromo/view_depositpromo_manager', $data);
			$this->template->render();
		//}
	}

	public function get_depositpromo_pages($segment) {
		$sort = "otcPaymentMethodId";

		$data['count_all'] = count($this->depositpromo_manager->getAllOtcPaymentMethod($sort, null, null));
		$config['base_url'] = "javascript:get_depositpromo_pages(";
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
		$data['depositpromo'] = $this->depositpromo_manager->getAllOtcPaymentMethod($sort, null, $segment);

		$this->load->view('marketing_management/depositpromo/ajax_view_depositpromo_setting_list', $data);
	}

	/**
	 * sort deposit promo
	 *
	 * @param 	sort
	 * @return	void
	 */
	public function sortDepositPromo($sort) {
		$data['count_all'] = count($this->depositpromo_manager->getAllOtcPaymentMethod($sort, null, null));
		$config['base_url'] = "javascript:get_depositpromo_pages(";
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
		$data['depositpromo'] = $this->depositpromo_manager->getAllOtcPaymentMethod($sort, null, null);

		$this->load->view('marketing_management/depositpromo/ajax_view_depositpromo_setting_list', $data);
	}

	/**
	 * search deposit promo
	 *
	 *
	 * @return	redirect page
	 */
	public function searchDepositPromo($search='') {
		$data['count_all'] = count($this->depositpromo_manager->searchDepositPromoList($search, null, null));
		$config['base_url'] = "javascript:get_depositpromo_pages(";
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
		$data['depositpromo'] = $this->depositpromo_manager->searchDepositPromoList($search, null, null);

		//export report permission checking
		if(!$this->permissions->checkPermissions('export_report')){
			$data['export_report_permission'] = FALSE;
		} else {
			$data['export_report_permission'] = TRUE;
		}

		$this->load->view('marketing_management/depositpromo/ajax_view_depositpromo_setting_list', $data);
	}

	/**
	 * export report to excel
	 *
	 *
	 * @return	excel format
	 */
    public function exportToExcel(){
    	$data = array(
				'username' => $this->authentication->getUsername(),
				'management' => 'Marketing Deposit Promo Management',
				'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
				'action' => 'Exported Deposit Promo List',
				'description' => "User " . $this->authentication->getUsername() . " exported Deposit Promo List",
				'logDate' => date("Y-m-d H:i:s"),
				'status' => 0
			);

    	$this->report_functions->recordAction($data);

	    $result = $this->depositpromo_manager->getDepositPromoListToExport();
	    //var_dump($result);exit();
	    // $this->excel->to_excel($result, 'depositpromolist-excel');
	    $d = new DateTime();
		$this->utils->create_excel($result, 'depositpromolist_' . $d->format('Y_m_d_H_i_s').'_'.rand(1,999));
    }

    /**
	 *add deposit promo
	 *
	 * @param $depositPromoId int
	 * @return	rendered template
	 */
    public function depositPromoBackupManager($depositPromoId,$promoName){
    	// if(!$this->permissions->checkPermissions('cms_addDepositPromoBackup_settings')){
        //     $this->error_access();
        // } else {
            $data['depositPromoId'] = $depositPromoId;
            $data['depositPromo'] = $depositPromo;

			$this->loadTemplate('Payment Management', '', '', 'payment');
			$this->template->write_view('sidebar', 'marketing_management/sidebar');
			$this->template->write_view('main_content', 'marketing_management/depositpromo/view_depositpromobackup_manager', $data);
			$this->template->render();
        // }
    }

	/**
	 * add/edit Deposit Promo setting
	 *
	 * @return	array
	 */
    public function addDepositPromo() {

    	$this->form_validation->set_rules('promoName', 'Promo Name' , 'trim|required|xss_clean');
    	$this->form_validation->set_rules('promoPeriodStart', 'Promo Period Start' , 'trim|required|xss_clean');
    	$this->form_validation->set_rules('promoPeriodEnd', 'Promo Period End' , 'trim|required|xss_clean');
    	$this->form_validation->set_rules('requiredDepositAmount', 'Required Deposit Amount' , 'trim|required|is_numeric');
    	$this->form_validation->set_rules('bonusAmount', 'Bonus Amount' , 'trim|required|xss_clean|is_numeric');
    	//$this->form_validation->set_rules('maxDepositAmount', 'Max Deposit Amount' , 'trim|required|xss_clean|is_numeric');
    	$this->form_validation->set_rules('totalBetsAmount', 'Total Bets Amount' , 'trim|required|xss_clean|is_numeric');
    	$this->form_validation->set_rules('expirationDayCnt', 'Expiration Day' , 'trim|required|xss_clean|is_numeric');
    	$this->form_validation->set_rules('bonusAmountRuleType', 'Bonus Amount Rule' , 'trim|required|xss_clean');
    	//$this->form_validation->set_rules('playerLevels', 'Player Levels' , 'trim|required|xss_clean');
    	//var_dump($playerLevels);exit();
    	$promoCode = $this->getPromoCode();
    	$promoName = $this->input->post('promoName');
		$promoPeriodStart = $this->input->post('promoPeriodStart');
		$promoPeriodEnd = $this->input->post('promoPeriodEnd');
		$requiredDepositAmount = $this->input->post('requiredDepositAmount');
		$bonusAmount = $this->input->post('bonusAmount');
		$bonusAmountRuleType = $this->input->post('bonusAmountRuleType');
		//$maxDepositAmount = $this->input->post('maxDepositAmount');
		$maxBonusAmount = $this->input->post('maxBonusAmount');
		$totalBetsAmount = $this->input->post('totalBetsAmount');
		$expirationDayCnt = $this->input->post('expirationDayCnt');
		$playerLevels = $this->input->post('playerLevels');
		$depositPromoId = $this->input->post('depositPromoId');
		$today = date("Y-m-d H:i:s");

    	//check if amount rule is fix or percentage
		if($bonusAmountRuleType == 0){
			$this->form_validation->set_rules('maxBonusAmount', 'Max Bonus Amount' , 'trim|required|xss_clean|is_numeric');
		}
    	if($this->form_validation->run() == false) {
    		$message = lang('con.d02');
			$this->alertMessage(2, $message);
    		$this->viewDepositPromoManager();
    	} else {
    		if($depositPromoId) {

	    		$data = array(
	    				'promoName' => $promoName,
	    				'promoPeriodStart' => $promoPeriodStart,
	    				'promoPeriodEnd' => date('Y-m-d', strtotime($promoPeriodEnd)) . ' 23:59:59',
	    				'requiredDepositAmount' => $requiredDepositAmount,
	    				'bonusAmount' => $bonusAmount,
	    				'bonusAmountRuleType' => $bonusAmountRuleType,
	    				//'maxDepositAmount' => $maxDepositAmount,
	    				'maxBonusAmount' => $maxBonusAmount,
	    				'totalBetRequirement' => $totalBetsAmount,
	    				'expirationDayCnt' => $expirationDayCnt,
	    				'updatedOn' => $today,
	    				'updatedBy' => $this->authentication->getUserId(),
	    			);

    			$this->depositpromo_manager->editDepositPromo($data, $playerLevels, $depositPromoId);
    			$message = lang('con.d03') . " <b>" . $depositPromo . "</b> " . lang('con.d04');
				$this->saveAction('Edit Deposit Promo', "User " . $this->authentication->getUsername() . " has edited Deposit Promo " . $depositPromo . ".");
    		} else {

	    		$data = array(
	    				'promoName' => $promoName,
	    				'promoPeriodStart' => $promoPeriodStart,
	    				'promoPeriodEnd' => date('Y-m-d', strtotime($promoPeriodEnd)) . ' 23:59:59',
	    				'requiredDepositAmount' => $requiredDepositAmount,
	    				'bonusAmount' => $bonusAmount,
	    				'bonusAmountRuleType' => $bonusAmountRuleType,
	    				//'maxDepositAmount' => $maxDepositAmount,
	    				'promoCode' => $promoCode,
	    				'maxBonusAmount' => $maxBonusAmount,
	    				'totalBetRequirement' => $totalBetsAmount,
	    				'expirationDayCnt' => $expirationDayCnt,
	    				'createdOn' => $today,
	    				'createdBy' => $this->authentication->getUserId(),
	    				'status' => 'active'
	    			);

    			$this->depositpromo_manager->addDepositPromo($data,$playerLevels);
    			$message = lang('con.d03') . " <b>" . $depositPromo . "</b> " . lang('con.d05');
				$this->saveAction('Add Deposit Promo ', "User " . $this->authentication->getUsername() . " has added new deposit promo " . $depositPromo . ".");
    		}

    		$this->alertMessage(1, $message);
    		redirect(BASEURL . 'depositpromo_management/viewDepositPromoManager');
    	}
    }

    public function getPromoCode(){
    	$promoCode = $this->depositpromo_manager->generateRandomCode();
    	if($this->depositpromo_manager->isPromoCodeExists($promoCode)){
    		$this->getPromoCode();
    	}else{
    		return $promoCode;
    	}
    }

    /**
	 * add/edit Deposit Promo setting
	 *
	 * @return	array
	 */
    public function addDepositPromoBackup() {
    	$this->form_validation->set_rules('depositPromo', 'Bank Name' , 'trim|required|xss_clean');
    	$this->form_validation->set_rules('branchName', 'Branch Name' , 'trim|required|xss_clean');
    	$this->form_validation->set_rules('accountNumber', 'Account Number' , 'trim|required|xss_clean');
    	$this->form_validation->set_rules('accountName', 'Account Name' , 'trim|required|xss_clean');
    	$this->form_validation->set_rules('dailyMaxDepositAmount', 'Daily Max Deposit Amount' , 'trim|required|xss_clean|numeric');
    	$this->form_validation->set_rules('description', 'Description' , 'trim|required|xss_clean');
    	//var_dump($playerLevels);exit();
    	if($this->form_validation->run() == false) {
    		$this->viewDepositPromoManager();
    	} else {
    		$lastOrderCnt = $this->depositpromo_manager->getDepositPromoBackupLastRankOrder();
    		//var_dump($lastOrderCnt[0]['accountOrder']);exit();
    		$accountOrder = $lastOrderCnt[0]['accountOrder'] + 1;
    		$depositPromo = $this->input->post('depositPromo');
    		$branchName = $this->input->post('branchName');
    		$accountNumber = $this->input->post('accountNumber');
    		$accountName = $this->input->post('accountName');
    		$description = $this->input->post('description');
    		$depositPromoId = $this->input->post('depositPromoId');
    		$depositPromoBackupId = $this->input->post('depositPromoBackupId');
    		$today = date("Y-m-d H:i:s");

    		if($depositPromoBackupId) {

	    		$data = array(
	    				'depositPromo' => $depositPromo,
	    				'branchName' => $branchName,
	    				'accountNumber' => $accountNumber,
	    				'accountName' => $accountName,
	    				'accountOrder' => $accountOrder,
	    				'dailyMaxDepositAmount' => $dailyMaxDepositAmount,
	    				'description' => $description,
	    				'updatedOn' => $today,
	    				'updatedBy' => $this->authentication->getUserId(),
	    			);

    			$this->depositpromo_manager->editDepositPromoBackup($data, $depositPromoId);
    			$message = lang('con.d03') . " <b>" . $depositPromo . "</b> " . lang('con.d04');
				$this->saveAction('Edit Deposit Promo', "User " . $this->authentication->getUsername() . " has edited Deposit Promo " . $depositPromo . ".");
    		} else {

	    		$data = array(
	    				'depositPromo' => $depositPromo,
	    				'branchName' => $branchName,
	    				'accountNumber' => $accountNumber,
	    				'accountName' => $accountName,
	    				'dailyMaxDepositAmount' => $dailyMaxDepositAmount,
	    				'description' => $description,
	    				'accountOrder' => $accountOrder,
	    				'createdOn' => $today,
	    				'createdBy' => $this->authentication->getUserId(),
	    				'status' => 'active'
	    			);

    			$this->depositpromo_manager->addDepositPromoBackup($data);
    			$message = lang('con.d06') . " <b>" . $depositPromo . "</b> " . lang('con.d05');
				$this->saveAction('Add Deposit Promo Backup', "User " . $this->authentication->getUsername() . " has added new deposit promo backup " . $depositPromo . ".");
    		}

    		$this->alertMessage(1, $message);
    		redirect(BASEURL . 'depositpromo_management/viewDepositPromoManager');
    	}
    }

    /**
	 * get deposit promo details
	 *
	 * @param 	depositPromoId
	 * @return	redirect
	 */
	public function getDepositPromoDetails($depositPromoId) {
    	echo json_encode($this->depositpromo_manager->getDepositPromoDetails($depositPromoId));
	}

	/**
	 * Delete deposit promo
	 *
	 * @param 	int
	 * @return	redirect
	 */
	public function deleteSelectedDepositPromo() {
		$depositpromo = $this->input->post('depositpromo');
		$today = date("Y-m-d H:i:s");

		$data = array(
				'username' => $this->authentication->getUsername(),
				'management' => 'Deposit Promo Management',
				'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
				'action' => 'Delete Selected Deposit Promo',
				'description' => "User " . $this->authentication->getUsername() . " deleted selected deposit promo.",
				'logDate' => $today,
				'status' => 0
			);

		$this->report_functions->recordAction($data);

		if($depositpromo != '') {
			foreach ($depositpromo as $depositpromoId) {
				$this->depositpromo_manager->deleteDepositPromos($depositpromoId);
				$this->depositpromo_manager->deleteDepositPromoItem($depositpromoId);
			}

			$message = lang('con.d07');
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect(BASEURL . 'depositpromo_management/viewDepositPromoManager');
		} else {
			$message = lang('con.d08');
			$this->alertMessage(2, $message);
			redirect(BASEURL . 'depositpromo_management/viewDepositPromoManager');
		}
	}

	/**
	 * Delete VIP group level
	 *
	 * @param 	vipgrouplevelId
	 * @return	redirect
	 */
	public function deleteDepositPromoItem($depositpromoId) {
		// if(!$this->permissions->checkPermissions('delete_depositpromo')){
		// 	$this->error_access();
		// } else {
			$this->depositpromo_manager->deleteDepositPromoItem($depositpromoId);

			$today = date("Y-m-d H:i:s");
			$data = array(
					'username' => $this->authentication->getUsername(),
					'management' => 'Marketing Management',
					'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
					'action' => 'Deleted Promo Rule item id: '.$depositpromoId,
					'description' => "User " . $this->authentication->getUsername() . " deleted promo",
					'logDate' => $today,
					'status' => 0
				);

			$this->report_functions->recordAction($data);

			$message = lang('con.d09');
			$this->alertMessage(1, $message);
			redirect(BASEURL . 'depositpromo_management/viewDepositPromoManager', 'refresh');
		//}
	}

	/**
	 * activate vip group
	 *
	 * @param 	depositPromoId
	 * @param 	status
	 * @return	redirect
	 */
	public function activateDepositPromo($depositPromoId,$status) {
		$data['depositpromoId'] = $depositPromoId;
		$data['status'] = $status;
		$data['updatedOn'] = date("Y-m-d H:i:s");
		$data['updatedBy'] = $this->authentication->getUserId();

		$this->depositpromo_manager->activateDepositPromo($data);

		$data = array(
				'username' => $this->authentication->getUsername(),
				'management' => 'Deposit Promo Setting Management',
				'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
				'action' => 'Update status of deposit promo id: '.$depositPromoId. 'to status:'.$status,
				'description' => "User " . $this->authentication->getUsername() . " edit deposit promo status to ".$status,
				'logDate' => date("Y-m-d H:i:s"),
				'status' => 0
			);

		$this->report_functions->recordAction($data);

		redirect(BASEURL . 'depositpromo_management/viewDepositPromoManager');
	}
}