<?php

require_once dirname(__FILE__) . '/BaseController.php';

/**
 * Game API
 *
 * General behaviors include
 * * Loads template
 * * Displays game api's
 * * Gets System Types
 * * Gets Api Detail
 * * Add/update/delete game api's
 * * Add extra information or credentials of the api
 * * Synchronizes game logs
 * * Activate/Deactivate api status
 *
 * @category System Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Game_Api extends BaseController {

	function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->library(array('permissions', 'form_validation', 'template', 'pagination', 'report_functions', 'player_manager', 'duplicate_account', 'utils'));
        $this->load->model('external_system');
		$this->permissions->checkSettings();
		$this->permissions->setPermissions(); //will set the permission for the logged in user
	}

	/**
	 * overview : load template
	 *
	 * detail : Loads template for view based on regions in config > template.php
	 * @param  string 	$title
	 * @param  string 	$description
	 * @param  string 	$keywords
	 * @param  string 	$activenav
	 * @return void
	 */
	private function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->add_js('resources/js/game_api/game_api.js');
		//$this->template->add_js('resources/js/jquery.dataTables.min.js');
		//$this->template->add_js('resources/js/dataTables.responsive.min.js');
		$this->template->add_js('resources/js/datatables.min.js');
		// $this->template->add_js('resources/js/moment.min.js');
		$this->template->add_js('resources/js/highlight.pack.js');
		$this->template->add_js('resources/js/ace/ace.js');
		$this->template->add_js('resources/js/ace/mode-json.js');
		$this->template->add_js('resources/js/ace/theme-tomorrow.js');

		$this->template->add_css('resources/css/general/style.css');
		$this->template->add_css('resources/css/datatables.min.css');
		//$this->template->add_css('resources/css/jquery.dataTables.css');
		//$this->template->add_css('resources/css/dataTables.responsive.css');
		$this->template->add_css('resources/css/hljs.tomorrow.css');

		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('userId', $this->authentication->getUserId());
		$this->template->write_view('sidebar', 'system_management/sidebar');
	}

	/**
	 * Will redirect to another sidebar if the permission was disabled
	 *
	 * Created by Mark Andrew Mendoza (andrew.php.ph) 
	 */
	private function error_redirection(){
		$this->loadTemplate('Game API Management', '', '', 'system');
		$systemUrl = $this->utils->activeSystemSidebar();
		$data['redirect'] = $systemUrl;

		$message = lang('con.usm01');
		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		
		$this->template->write_view('main_content', 'error_page', $data);
		$this->template->render();
	}

	/**
	 * oveview : view game api
	 *
	 * detail: get all game api
	 * @return void
	 */
	public function viewGameApi() {
		if (!$this->permissions->checkPermissions('game_api')) {
			$this->error_redirection();
		} else {
			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			// sets the history for breadcrumbs
			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active'));
			}

			$this->history->setHistory('header_system.system_word23', 'game_api/viewGameApi');

			$loaded = $this->session->userdata('loaded');

			if ($loaded == NULL) {
				$data = array(
					'system_name_gapi' => TRUE,
					'note_gapi' => TRUE,
					'last_sync_datetime_gapi' => TRUE,
					'last_sync_id_gapi' => TRUE,
					'last_sync_details_gapi' => TRUE,
					'system_type_gapi' => TRUE,
					'live_url_gapi' => TRUE,
					'sandbox_url_gapi' => TRUE,
					'live_key_gapi' => TRUE,
					'live_secret_gapi' => TRUE,
					'sandbox_key_gapi' => TRUE,
					'live_mode_gapi' => TRUE,
					'sandbox_secret_gapi' => TRUE,
					'second_url_gapi' => TRUE,
					'sandbox_account_gapi' => TRUE,
					'live_account_gapi' => TRUE,
					'system_code_gapi' => TRUE,
					'status_gapi' => TRUE,
					'class_name_gapi' => TRUE,
					'local_path_gapi' => TRUE,
					'manager_gapi' => TRUE,
					'game_platform_rate' => TRUE,
					'extra_info' => TRUE,
					'sandbox_extra_info' => TRUE,
					'created_on' => TRUE,
				);
			} else {
				$data = array(
					'system_name_gapi' => ($this->session->userdata('system_name_gapi')) ? TRUE : FALSE,
					'note_gapi' => ($this->session->userdata('note_gapi')) ? TRUE : FALSE,
					'last_sync_datetime_gapi' => ($this->session->userdata('last_sync_datetime_gapi')) ? TRUE : FALSE,
					'last_sync_id_gapi' => ($this->session->userdata('last_sync_id_gapi')) ? TRUE : FALSE,
					'last_sync_details_gapi' => ($this->session->userdata('last_sync_details_gapi')) ? TRUE : FALSE,
					'system_type_gapi' => ($this->session->userdata('system_type_gapi')) ? TRUE : FALSE,
					'live_url_gapi' => ($this->session->userdata('live_url_gapi')) ? TRUE : FALSE,
					'sandbox_url_gapi' => ($this->session->userdata('sandbox_url_gapi')) ? TRUE : FALSE,
					'live_key_gapi' => ($this->session->userdata('live_key_gapi')) ? TRUE : FALSE,
					'live_secret_gapi' => ($this->session->userdata('live_secret_gapi')) ? TRUE : FALSE,
					'sandbox_key_gapi' => ($this->session->userdata('sandbox_key_gapi')) ? TRUE : FALSE,
					'live_mode_gapi' => ($this->session->userdata('live_mode_gapi')) ? TRUE : FALSE,
					'sandbox_secret_gapi' => ($this->session->userdata('sandbox_secret_gapi')) ? TRUE : FALSE,
					'second_url_gapi' => ($this->session->userdata('second_url_gapi')) ? TRUE : FALSE,
					'sandbox_account_gapi' => ($this->session->userdata('sandbox_account_gapi')) ? TRUE : FALSE,
					'live_account_gapi' => ($this->session->userdata('live_account_gapi')) ? TRUE : FALSE,
					'system_code_gapi' => ($this->session->userdata('system_code_gapi')) ? TRUE : FALSE,
					'status_gapi' => ($this->session->userdata('status_gapi')) ? TRUE : FALSE,
					'class_name_gapi' => ($this->session->userdata('class_name_gapi')) ? TRUE : FALSE,
					'local_path_gapi' => ($this->session->userdata('local_path_gapi')) ? TRUE : FALSE,
					'manager_gapi' => ($this->session->userdata('manager_gapi')) ? TRUE : FALSE,
					'game_platform_rate' => ($this->session->userdata('game_platform_rate')) ? TRUE : FALSE,
					'extra_info' => ($this->session->userdata('extra_info')) ? TRUE : FALSE,
					'sandbox_extra_info' => ($this->session->userdata('sandbox_extra_info')) ? TRUE : FALSE,
					'created_on' => ($this->session->userdata('created_on')) ? TRUE : FALSE,
				);
			}

			$this->session->set_userdata($data);

			$user_id = $this->authentication->getUserId();
			$data['currentUser'] = $user_id;
			$data['roles'] = $this->rolesfunctions->getAllRolesByUser($user_id, null, null);

			$data['const_unlocked'] = 1;
			$data['const_locked'] = 2;

			$this->load->model('external_system');
			$gameApis = $this->external_system->getAllSytemGameApi();

			$constants = get_defined_constants(true)['user'];
			foreach ($constants as $key => $value) {
				if (substr($key, -3) == 'API' && $key != 'SYSTEM_GAME_API') {
					if (strrpos($key, 'PAYMENT')) {
						$data['api_types'][lang('system.word95')]['id'] = SYSTEM_PAYMENT;
						$data['api_types'][lang('system.word95')]['list'][$key] = $value;
					} else {
						$data['api_types'][lang('system.word94')]['id'] = SYSTEM_GAME_API;
						$data['api_types'][lang('system.word94')]['list'][$key] = $value;
					}
				}
			}

			ksort($data['api_types'][lang('system.word94')]['list']);

			$data['gameApis'] = json_decode(json_encode($gameApis), true);

			$this->loadTemplate('System Management', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/view_game_api', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : get system types
	 *
	 * detail : Load thru ajax
	 * @return array
	 */
	public function getSystemTypes() {

		$array = array();
		$est = $this->config->item('external_system_types');
		for ($i = 0; $i < count($est); $i++) {
			if ($i == 0) {
				$array[0]['id'] = $est[0];
				$array[0]['system_type'] = "SYSTEM GAME API";
			}
			if ($i == 1) {
				$array[1]['id'] = $est[1];
				$array[1]['system_type'] = "SYSTEM PAYMENT";
			}

		}
		$data['sytemTypes'] = $array;
		$arr = array('status' => 'success', 'data' => $data);
		//echo json_encode($arr);
		$this->returnJsonResult($arr);
	}


	/**
	 * overview : get api detail
	 *
	 * @param  int 	$apiId
	 *
	 * @return array
	 */
	public function getApiDetail($apiId) {
		$this->load->model('external_system');
		//echo json_encode($this->external_system->getPredefinedSystemById($apiId));
		$this->returnJsonResult($this->external_system->getPredefinedSystemById($apiId));
	}

	/**
	 * overview : edit game api
	 *
	 * detail : Load thru ajax
	 * @param  int 	$gameApiId
	 * @return array
	 */
	public function editGameApi($gameApiId) {

		$this->load->model('external_system');

		$array = array();
		$est = $this->config->item('external_system_types');
		for ($i = 0; $i < count($est); $i++) {
			if ($i == 0) {
				$array[0]['id'] = $est[0];
				$array[0]['system_type'] = lang('sys.game.api');
			}
			if ($i == 1) {
				$array[1]['id'] = $est[1];
				$array[1]['system_type'] = lang('sys.payment.api');
			}

		}
		$data['sytemTypes'] = $array;
		$data['gameApi'] = $this->external_system->getSystemById($gameApiId);

		$arr = array('status' => 'success', 'data' => $data);
		//echo json_encode($arr);
		$this->returnJsonResult($arr);
	}

	/**
	 * overview : disable able game api
	 */
	/**
	public function disableAbleGameApi() {

		$this->load->model('external_system');

		$this->form_validation->set_rules('id', 'Id', 'trim|xss_clean');
		$this->form_validation->set_rules('status', 'Status', 'trim|numeric|xss_clean');

		if ($this->form_validation->run() == false) {
			$arr = array('status' => 'error', 'msg' => validation_errors());
			echo json_encode($arr);

		} else {
			$paymentApiId = $this->input->post('id');
			$data = array(
				'status' => $this->input->post('status'),
			);

			if ($this->external_system->disableAbleGameApi($data, $paymentApiId)) {
				$this->alertMessage(1, lang('sys.ga.succsaved'));
				$arr = array('status' => 'success');
				echo json_encode($arr);
			} else {
				$arr = array('status' => 'failed');
				echo json_encode($arr);
				$this->alertMessage(2, lang('sys.ga.erroccured'));
			}
		}

	}

	/**
	 * overview : add game api
	 *
	 * detail : Adds Banktype through ajax
	 * @return array
	 */
	public function addGameApi() {

		$this->load->model('external_system');

		$this->form_validation->set_rules('new_id', 'ID', 'trim|required|xss_clean');
		$this->form_validation->set_rules('system_name', 'System Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('note', 'Note', 'trim|xss_clean');
		$this->form_validation->set_rules('last_sync_datetime', 'Last Sync Datetime', 'trim|xss_clean');
		$this->form_validation->set_rules('last_sync_id', 'Last Sync Id', 'trim|xss_clean');
		$this->form_validation->set_rules('last_sync_details', 'Last Sync Details', 'trim|xss_clean');
        $this->form_validation->set_rules('system_type', 'System Type', 'trim|xss_clean');
        $this->form_validation->set_rules('category', 'Category', 'trim|xss_clean');
        $this->form_validation->set_rules('amount_float', 'Amount Float', 'trim|xss_clean');
		$this->form_validation->set_rules('live_url', 'Live Url', 'trim|xss_clean');
		$this->form_validation->set_rules('sandbox_url', 'Sanbox Url', 'trim|xss_clean');
		$this->form_validation->set_rules('live_key', 'Live Key', 'trim|xss_clean');
		$this->form_validation->set_rules('live_secret', 'Live Secret', 'trim|xss_clean');
		$this->form_validation->set_rules('sandbox_key', 'Sandbox key', 'trim|xss_clean');
		$this->form_validation->set_rules('sandbox_secret', 'Sandbox secret', 'trim|xss_clean');
		$this->form_validation->set_rules('live_mode', 'Live Mode', 'trim|xss_clean');
		$this->form_validation->set_rules('second_url', 'Second Url', 'trim|xss_clean');
		$this->form_validation->set_rules('sandbox_account', 'Sandbox Account', 'trim|xss_clean');
		$this->form_validation->set_rules('live_account', 'Live Account', 'trim|xss_clean');
		$this->form_validation->set_rules('system_code', 'System Code', 'trim|xss_clean');
		$this->form_validation->set_rules('status', 'Status', 'trim|xss_clean');
		$this->form_validation->set_rules('class_name', 'Class Name', 'trim|xss_clean');
		$this->form_validation->set_rules('local_path', 'Local Path', 'trim|xss_clean');
		$this->form_validation->set_rules('manager', 'Manager', 'trim|xss_clean');
		$this->form_validation->set_rules('game_platform_rate', 'Game Platform Rate', 'trim|xss_clean');
		$this->form_validation->set_rules('extra_info', 'Extra Info', 'trim');
		$this->form_validation->set_rules('sandbox_extra_info', 'Sandbox Extra Info', 'trim');

		$platformpath = ($this->input->post('local_path') && $this->input->post('local_path') == 'game_platform') ? GAMEPLATFROMPATH : GAMEPLATFROMPATH_T1GAMES;

		if ($this->form_validation->run() == false) {

			$arr = array('status' => 'error', 'msg' => validation_errors());
			//echo json_encode($arr);
            $this->returnJsonResult($arr);
		} else if (file_exists($platformpath . "/" . $this->input->post('class_name') . '.php' ) == false) {
			$arr = array('status' => 'error', 'msg' => lang('Class file is required'));
            $this->returnJsonResult($arr);

		} else {

			$data = array(
				'id' => $this->input->post('new_id'),
				'system_name' => $this->input->post('system_name'),
				'note' => $this->input->post('note'),
				'last_sync_datetime' => $this->input->post('last_sync_datetime'),
				'last_sync_id' => $this->input->post('last_sync_id'),
				'last_sync_details' => $this->input->post('last_sync_details'),
                'system_type' => $this->input->post('system_type'),
                'category' => $this->input->post('category'),
                'amount_float' => $this->input->post('amount_float'),
				'live_url' => $this->input->post('live_url'),
				'sandbox_url' => $this->input->post('sandbox_url'),
				'live_key' => $this->input->post('live_key'),
				'live_secret' => $this->input->post('live_secret'),
				'sandbox_key' => $this->input->post('sandbox_key'),
				'live_mode' => $this->input->post('live_mode'),
				'sandbox_secret' => $this->input->post('sandbox_secret'),
				'second_url' => $this->input->post('second_url'),
				'sandbox_account' => $this->input->post('sandbox_account'),
				'live_account' => $this->input->post('live_account'),
				'system_code' => $this->input->post('system_code'),
				'status' => $this->input->post('status'),
				'class_name' => $this->input->post('class_name'),
				'local_path' => $this->input->post('local_path'),
				'manager' => $this->input->post('manager'),
				'game_platform_rate' => $this->input->post('game_platform_rate'),
				'extra_info' => $this->input->post('extra_info'),
				'sandbox_extra_info' => $this->input->post('sandbox_extra_info'),
			);

			$result = $this->external_system->addGameApi($data);
			if ($result['success'] === true) {
				$data= (array)$this->external_system->getSystemById($result['api_id']);
				$data['action'] = External_system::GAME_API_HISTORY_ACTION_ADD;
				$data['updated_at']= $this->utils->getNowForMysql();
				$data['game_platform_id'] = $result['api_id'];
				unset($data['id']);
				$data['user_id'] = $this->authentication->getUserId();
				$this->external_system->addToGameApiHistory($data);
				$this->alertMessage(1, lang('sys.ga.succsaved'));
				$arr = array('status' => 'success');
				//echo json_encode($arr);
                $this->returnJsonResult($arr);
            } else {
				$arr = array('status' => 'failed');
				//echo json_encode($arr);
                $this->returnJsonResult($arr);
                $this->alertMessage(2, lang('sys.ga.erroccured'));
			}
		}

	}

	/**
	 * overview : update game api
	 *
	 * detail : Updates Banktype through ajax
	 * @return  array
	 */
	public function updateGameApi() {

		$this->load->model('external_system');

		$this->form_validation->set_rules('new_id', 'ID', 'trim|required|xss_clean');
		$this->form_validation->set_rules('system_name', 'System Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('note', 'Note', 'trim|xss_clean');
		$this->form_validation->set_rules('last_sync_datetime', 'Last Sync Datetime', 'trim|xss_clean');
		$this->form_validation->set_rules('last_sync_id', 'Last Sync Id', 'trim|xss_clean');
		$this->form_validation->set_rules('last_sync_details', 'Last Sync Details', 'trim|xss_clean');
        $this->form_validation->set_rules('system_type', 'System Type', 'trim|xss_clean');
        $this->form_validation->set_rules('category', 'Category', 'trim|xss_clean');
        $this->form_validation->set_rules('amount_float', 'Amount Float', 'trim|xss_clean');
		$this->form_validation->set_rules('live_url', 'Live Url', 'trim|xss_clean');
		$this->form_validation->set_rules('sandbox_url', 'Sanbox Url', 'trim|xss_clean');
		$this->form_validation->set_rules('live_key', 'Live Key', 'trim|xss_clean');
		$this->form_validation->set_rules('live_secret', 'Live Secret', 'trim|xss_clean');
		$this->form_validation->set_rules('sandbox_key', 'Sandbox key', 'trim|xss_clean');
		$this->form_validation->set_rules('sandbox_secret', 'Sandbox secret', 'trim|xss_clean');
		$this->form_validation->set_rules('live_mode', 'Live Mode', 'trim|xss_clean');
		$this->form_validation->set_rules('second_url', 'Second Url', 'trim|xss_clean');
		$this->form_validation->set_rules('sandbox_account', 'Sandbox Account', 'trim|xss_clean');
		$this->form_validation->set_rules('live_account', 'Live Account', 'trim|xss_clean');
		$this->form_validation->set_rules('system_code', 'System Code', 'trim|xss_clean');
		$this->form_validation->set_rules('status', 'Status', 'trim|xss_clean');
		$this->form_validation->set_rules('class_name', 'Class Name', 'trim|xss_clean');
		$this->form_validation->set_rules('local_path', 'Local Path', 'trim|xss_clean');
		$this->form_validation->set_rules('manager', 'Manager', 'trim|xss_clean');
		$this->form_validation->set_rules('game_platform_rate', 'Game Platform Rate', 'trim|xss_clean');
		$this->form_validation->set_rules('extra_info', 'Extra Info', 'trim');
		$this->form_validation->set_rules('sandbox_extra_info', 'Sandbox Extra Info', 'trim');

		$platformpath = ($this->input->post('local_path') && $this->input->post('local_path') == 'game_platform') ? GAMEPLATFROMPATH : GAMEPLATFROMPATH_T1GAMES;

		if ($this->form_validation->run() == false) {

			$arr = array('status' => 'error', 'msg' => validation_errors());
			//echo json_encode($arr);
            $this->returnJsonResult($arr);

		} else if (file_exists($platformpath . "/" . $this->input->post('class_name') . '.php' ) == false) {
			$arr = array('status' => 'error', 'msg' => lang('Class file is required'));
            $this->returnJsonResult($arr);

		} else {
			$gameApiId = $this->input->post('id');
			$data = array(
				'id' => $this->input->post('new_id'),
				'system_name' => $this->input->post('system_name'),
				'note' => $this->input->post('note'),
				'last_sync_datetime' => $this->input->post('last_sync_datetime'),
				'last_sync_id' => $this->input->post('last_sync_id'),
				'last_sync_details' => $this->input->post('last_sync_details'),
                'system_type' => $this->input->post('system_type'),
                'category' => $this->input->post('category'),
                'amount_float' => $this->input->post('amount_float'),
				'live_url' => $this->input->post('live_url'),
				'sandbox_url' => $this->input->post('sandbox_url'),
				'live_key' => $this->input->post('live_key'),
				'live_secret' => $this->input->post('live_secret'),
				'sandbox_key' => $this->input->post('sandbox_key'),
				'live_mode' => $this->input->post('live_mode'),
				'sandbox_secret' => $this->input->post('sandbox_secret'),
				'second_url' => $this->input->post('second_url'),
				'sandbox_account' => $this->input->post('sandbox_account'),
				'live_account' => $this->input->post('live_account'),
				'system_code' => $this->input->post('system_code'),
				'status' => $this->input->post('status'),
				'class_name' => $this->input->post('class_name'),
				'local_path' => $this->input->post('local_path'),
				'manager' => $this->input->post('manager'),
				'game_platform_rate' => $this->input->post('game_platform_rate'),
				'extra_info' => $this->input->post('extra_info'),
				'sandbox_extra_info' => $this->input->post('sandbox_extra_info'),
				'updated_at'=> $this->utils->getNowForMysql(),
			);

			if ($this->external_system->updateGameApi($data, $gameApiId)) {
				$data= (array)$this->external_system->getSystemById($gameApiId);
				$data['action'] = External_system::GAME_API_HISTORY_ACTION_UPDATE;
				$data['game_platform_id'] = $data['id'];
				unset($data['id']);
				$data['user_id'] = $this->authentication->getUserId();
				$this->external_system->addToGameApiHistory($data);
				$this->alertMessage(1, lang('sys.ga.succsaved'));
				$arr = array('status' => 'success');
				//echo json_encode($arr);
                $this->returnJsonResult($arr);
            } else {
				$arr = array('status' => 'failed');
				//echo json_encode($arr);
                $this->returnJsonResult($arr);
                $this->alertMessage(2, lang('sys.ga.erroccured'));
			}
		}
	}

	/**
	 * overview : delete Game api
	 *
	 * detail : Deletes GameApi
	 * @return  array
	 */
	// public function deleteGameApi() {
	// 	$this->load->model('external_system');
	// 	$ids = $this->input->post("forDeletes");
	// 	if ($this->external_system->deleteGameApi($ids)) {
	// 		$this->alertMessage(1, lang('sys.ga.succsaved'));
	// 		$arr = array('status' => 'success');
	// 		//echo json_encode($arr);
 //            $this->returnJsonResult($arr);
 //        } else {
	// 		$arr = array('status' => 'failed');
	// 		//echo json_encode($arr);
 //            $this->returnJsonResult($arr);
 //            $this->alertMessage(2, lang('sys.ga.erroccured'));
	// 	}

	// }

	/**
	 * overview : post change colums
	 *
	 * detail : change for number of columns in banktype table
	 * @return void
	 */
	public function postChangeColumns() {

		$data = array(
			'system_name_gapi' => $this->input->post('system_name'),
			'note_gapi' => $this->input->post('note'),
			'last_sync_datetime_gapi' => $this->input->post('last_sync_datetime'),
			'last_sync_id_gapi' => $this->input->post('last_sync_id'),
			'last_sync_details_gapi' => $this->input->post('last_sync_details'),
			'system_type_gapi' => $this->input->post('system_type'),
			'live_url_gapi' => $this->input->post('live_url'),
			'sandbox_url_gapi' => $this->input->post('sandbox_url'),
			'live_key_gapi' => $this->input->post('live_key'),
			'live_secret_gapi' => $this->input->post('live_secret'),
			'sandbox_key_gapi' => $this->input->post('sandbox_key'),
			'live_mode_gapi' => $this->input->post('live_mode'),
			'sandbox_secret_gapi' => $this->input->post('sandbox_secret'),
			'second_url_gapi' => $this->input->post('second_url'),
			'sandbox_account_gapi' => $this->input->post('sandbox_account'),
			'live_account_gapi' => $this->input->post('live_account'),
			'system_code_gapi' => $this->input->post('system_code'),
			'status_gapi' => $this->input->post('status'),
			'class_name_gapi' => $this->input->post('class_name'),
			'local_path_gapi' => $this->input->post('local_path'),
			'manager_gapi' => $this->input->post('manager'),
			'extra_info' => $this->input->post('extra_info'),
			'sandbox_extra_info' => $this->input->post('sandbox_extra_info'),
		);

		$this->session->set_userdata($data);
		$this->session->set_userdata('loaded', 'loaded');
		redirect('game_api/viewGameApi');
	}

	/**
	 * overview : synchronize game log
	 *
	 * @return array
	 */
	// public function syncGameLog() {
	// 	$platformId = $this->input->post('platformId');
	// 	$timeFrom = $this->input->post('timeFrom');
	// 	$timeTo = $this->input->post('timeTo');
	// 	$username = $this->input->post('username');

	// 	try {
	// 		new DateTime($timeFrom);
	// 		new DateTime($timeTo);
	// 	} catch (Exception $e) {
	// 		$this->utils->debug_log(DateTime::getLastErrors());
	// 		$errorMsg = $e->getMessage();
	// 		$jsonResult = array('status' => 'fail', 'msg' => 'Invalid datetime');
	// 		$this->returnJsonResult($jsonResult);
	// 		return;
	// 	}

	// 	$this->load->model('external_system');
	// 	$this->utils->debug_log('Calling sync_game_platform...');
	// 	$cmd = $this->runAsyncCommandLine('rebuild_single_game_by_timelimit', array($platformId, $timeFrom, $timeTo, $username));
	// 	// $res = shell_exec($cmd);
	// 	$this->utils->debug_log('sync_game_platform cmd ', $cmd);
	// 	if (empty($cmd)) {
	// 		$jsonResult = array('status' => 'fail', 'msg' => $cmd);
	// 	} else {
	// 		$jsonResult = array('status' => 'success');
	// 		// $this->external_system->setLastSyncDate($platformId);
	// 	}

	// 	$this->returnJsonResult($jsonResult);
	// }

	// Copied from sync_game_records.php
	/**
	 * overview : get commandline
	 *
	 * @param  string 	$func
	 * @param  array 	$args
	 * @return path
	 */
	// private function getCommandLine($func, $args) {
	// 	$og_home = realpath(dirname(__FILE__) . "/../../");
	// 	$PHP = '/usr/bin/php';

	// 	$argStr = '';
	// 	if (!empty($args)) {
	// 		foreach ($args as $val) {
	// 			$argStr .= ' "' . $val . '"';
	// 		}
	// 	}

	// 	$cmd = $PHP . ' ' . $og_home . '/shell/ci_cli.php cli/sync_game_records/' . $func . $argStr;

	// 	return $cmd;
	// }

	public function disableAbleGameApi() {

		$this->load->model('external_system');

		$this->form_validation->set_rules('id', 'Id', 'trim|xss_clean');
		$this->form_validation->set_rules('status', 'Status', 'trim|numeric|xss_clean');

		if ($this->form_validation->run() == false) {
			$arr = array('status' => 'error', 'msg' => validation_errors());
			echo json_encode($arr);

		} else {
			$apiId = $this->input->post('id');
			$data = array(
				'status' => $this->input->post('status'),
				'updated_at' => $this->utils->getNowForMysql(),
			);

			$isSeamLessGame=false;
			$availApi=false;
			if($apiId){

				//check seamless first
				$api=$this->utils->loadExternalSystemLibObject($apiId);
				if(!empty($api)){
					$isSeamLessGame=$api->isSeamLessGame();
					$availApi=true;
				}

			}

			if($availApi && !$isSeamLessGame && $this->external_system->disableAbleGameApi($data, $apiId)) {

				$data= (array)$this->external_system->getSystemById($apiId);
				if($data['status'] == 1){
					$data['action'] = External_system::GAME_API_HISTORY_ACTION_UNBLOCKED;
				}else{
					$data['action'] = External_system::GAME_API_HISTORY_ACTION_BLOCKED;
				}
				$data['game_platform_id'] = $data['id'];
				unset($data['id']);
				$data['user_id'] = $this->authentication->getUserId();
				$this->external_system->addToGameApiHistory($data);

				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('sys.ga.succsaved'));
				$arr = array('status' => 'success');
				echo json_encode($arr);
			} else {
				$arr = array('status' => 'failed');
				echo json_encode($arr);
				$msg='';
				if($isSeamLessGame){
					$msg=lang('Cannot disable seamless game');
				}else{
					$msg=lang('sys.ga.erroccured');
				}

				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $msg);
			}
		}

	}

    public function gameMaintenanceMode() {
        if (!$this->permissions->checkPermissions('game_api_maintenance')) {
            $this->error_access();
        }else{

            $this->form_validation->set_rules('id', 'Id', 'trim|xss_clean');
            $this->form_validation->set_rules('maintenance_mode', 'Maintenance', 'trim|numeric|xss_clean');

            if ($this->form_validation->run() == false) {
                $arr = array('status' => 'error', 'msg' => validation_errors());
                $this->returnJsonResult($arr);
            } else {
                $gameApiId = $this->input->post('id');
                $data = array(
                    'maintenance_mode' =>  $this->input->post('maintenance_mode'),
                    'updated_at' => $this->utils->getNowForMysql(),
                );

                if ($this->external_system->setToMaintenanceOrPauseMode($data, $gameApiId)) {
                	$data= (array)$this->external_system->getSystemById($gameApiId);
                	if($data['maintenance_mode'] == 1){
                		$data['action'] = External_system::GAME_API_HISTORY_ACTION_UNDER_MAINTENANCE;
                	}else{
                		$data['action'] = External_system::GAME_API_HISTORY_ACTION_FINISH_MAINTENANCE;
                	}
                	$data['game_platform_id'] = $data['id'];
					unset($data['id']);
					$data['user_id'] = $this->authentication->getUserId();
			    	$this->external_system->addToGameApiHistory($data);
                    $this->alertMessage(1, lang('sys.ga.succsaved'));
                    $arr = array('status' => 'success');
                    $this->returnJsonResult($arr);
                } else {
                    $arr = array('status' => 'failed');
                    $this->returnJsonResult($arr);
                    $this->alertMessage(2, lang('sys.ga.erroccured'));
                }
            }
        }
    }

    public function gamePauseSync() {

        $this->form_validation->set_rules('id', 'Id', 'trim|xss_clean');
        $this->form_validation->set_rules('pause_sync', 'Pause Syncing', 'trim|numeric|xss_clean');

        if ($this->form_validation->run() == false) {
            $arr = array('status' => 'error', 'msg' => validation_errors());
            $this->returnJsonResult($arr);
        } else {
            $gameApiId = $this->input->post('id');
            $data = array(
                'pause_sync' =>  $this->input->post('pause_sync'),
                'updated_at' => $this->utils->getNowForMysql(),
            );

            if ($this->external_system->setToMaintenanceOrPauseMode($data, $gameApiId)) {
            	$data= (array)$this->external_system->getSystemById($gameApiId);
            	$data['updated_at'] = $this->utils->getNowForMysql();
				if($data['pause_sync'] == 1){
					$data['action'] = External_system::GAME_API_HISTORY_ACTION_PAUSED_SYNC;
				}else{
					$data['action'] = External_system::GAME_API_HISTORY_ACTION_RESUMED_SYNC;
				}
				$data['game_platform_id'] = $data['id'];
				unset($data['id']);
				$data['user_id'] = $this->authentication->getUserId();
				$this->external_system->addToGameApiHistory($data);
                $this->alertMessage(1, lang('sys.ga.succsaved'));
                $arr = array('status' => 'success');
                $this->returnJsonResult($arr);
            } else {
                $arr = array('status' => 'failed');
                $this->returnJsonResult($arr);
                $this->alertMessage(2, lang('sys.ga.erroccured'));
            }
        }
    }


    /**
	 * oveview : view game maintenance schedule
	 *
	 * detail: get all game maintenance schedule
	 * @return void
	 */
	public function viewGameMaintenanceSchedule() {
		if (!$this->permissions->checkPermissions('game_maintenance_schedule')) {
			$this->error_redirection();
		}else{
				if (($this->session->userdata('sidebar_status') == NULL)) {
					$this->session->set_userdata(array('sidebar_status' => 'active'));
				}

				// sets the history for breadcrumbs
				if (($this->session->userdata('well_crumbs') == NULL)) {
					$this->session->set_userdata(array('well_crumbs' => 'active'));
				}

				$data = array();
				$data['conditions']['game_platform_id']=$this->input->get('game_platform_id');
				if(!empty($data['conditions']['game_platform_id']) && !is_array($data['conditions']['game_platform_id'])){
					$data['conditions']['game_platform_id']=$data['conditions']['game_platform_id'];
				}
				
				$data['gameapis'] = $this->external_system->getAllGameApis();
				$this->template->add_js('resources/js/bootstrap-notify.min.js');
				$this->template->add_js('resources/js/select2.min.js');
				$this->template->add_css('resources/css/select2.min.css');
				$this->template->add_css('resources/css/game_description/game_description.css');
				$this->loadTemplate('System Management', '', '', 'system');
				$this->template->write_view('main_content', 'system_management/game_maintenance_schedule', $data);
				$this->template->render();
		}
	}

	/**
	 * overview : get game maintenance scheduled
	 *
	 * detail : Load thru ajax
	 * @return array
	 */
	public function gameMaintenanceSchedule()
	{
		$this->load->model(array('report_model'));
		$request = $this->input->post();
		$result = [];
		$is_export = false;
		if ($this->permissions->checkPermissions('game_maintenance_schedule')){
			$result = $this->report_model->getGameMaintenanceSchedule($request,$is_export);
		}else{
			return $this->error_access();
		}
		$this->returnJsonResult($result);
	}

	/**
	 * overview : Adds  Game Maintenance Schedule
	 */
	public function addGameMaintenanceSchedule()
	{
		if (!$this->permissions->checkPermissions('maintenance_schedule_control')) {
			$this->error_access();
		}else{
			$this->load->model('external_system');
			$gameplatformid = $this->input->post('gameplatformid');
			$startdate = $this->input->post('startdate');
			$enddate = $this->input->post('enddate');
			$note = $this->input->post('note');
			$data = array(
				'game_platform_id' => $gameplatformid,
				'start_date' => $startdate,
				'end_date' => $enddate,
				'note' => $note,
				'status' => 1,
				'last_edit_user' => $this->authentication->getUserId(),
				'created_by' => $this->authentication->getUserId(),
				'created_at' => $this->utils->getNowForMysql(),
				'updated_at' => $this->utils->getNowForMysql(),
			);
				if($startdate >= $enddate){
					$result = array('status' => 'failed');
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('sys.gm.invaliddaterange'));
				}else{
					if(!empty($gameplatformid) || !empty($note)){
						$count = $this->external_system->validateEntryGameMaintenanceSchedule($gameplatformid,$startdate,$enddate);
						if($count > 0){
							$result = array('status' => 'failed');
							$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('sys.gm.duplicatemaintenance'));
						}else{
							if ($this->external_system->addNewGameMaintenanceSchedule($data)){
								$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('sys.gd25'));
								$result = array('status' => 'success');
							}
						}
					}else{
						$result = array('status' => 'failed');
						$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('sys.gm.emptyfield'));
					}
				}
				$this->returnJsonResult($result);
			}
	}

	public function getDetailGameMaintenanceSchedule()
	{
		 $id = $this->input->post('id');
		 $this->load->model(array('external_system'));
		 if ($this->permissions->checkPermissions('game_maintenance_schedule')){
			$result = $this->external_system->getGameMaintenanceScheduleById($id);
		 }else{
		 	return $this->error_access();
		 }
		 $this->returnJsonResult($result);
	}

	/**
	 * overview : Edit game maintenance schedule details
	 */
	public function editGameMaintenanceSchedule()
	{
		if (!$this->permissions->checkPermissions('maintenance_schedule_control')) {
			$this->error_access();
		}else{
			$id = $this->input->post('gamemaintenanceid');
			$gameplatformid = $this->input->post('e_gameplatformid');
			$startdate = $this->input->post('e_startdate');
			$enddate = $this->input->post('e_enddate');
			$note = $this->input->post('e_note');
			$data = array(
				'game_platform_id' => $gameplatformid,
				'start_date' => $startdate,
				'end_date' => $enddate,
				'note' => $note,
				'last_edit_user' => $this->authentication->getUserId(),
				'updated_at' => $this->utils->getNowForMysql(),
			);
				if($startdate >= $enddate){
					$result = array('status' => 'failed');
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('sys.gm.invaliddaterange'));
				}else{
					if(!empty($gameplatformid) || !empty($note) ){
						$count = $this->external_system->validateEntryGameMaintenanceSchedule($gameplatformid,$startdate,$enddate);
						if($count > 0){
							$result = array('status' => 'failed');
							$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('sys.gm.duplicatemaintenance'));
						}else{
							if($this->external_system->editDetailsGameMaintenanceSchedule($data,$id)){
								$result = array('status' => 'success');
								$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('sys.gd25'));
							}
						}
					}else{
						$result = array('status' => 'failed');
						$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('sys.gm.emptyfield'));
					}
				}
			$this->returnJsonResult($result);
		}
	}

	/**
	 * overview : update game maintenance schedule status
	 */
	public function updateGameMaintenanceScheduleStatus()
	{
		if (!$this->permissions->checkPermissions('maintenance_schedule_control')) {
			$this->error_access();
		}else{
			$id = $this->input->post('id');
			$data = array(
				'status' => $this->input->post('status'),
				'last_edit_user' => $this->authentication->getUserId(),
				'updated_at' => $this->utils->getNowForMysql()
			);
			$gameApiId = $this->external_system->getGameApiIdGameMaintenanceSchedule($id);
			if($this->input->post('counter') == 1){
                $data2 = array(
                    'maintenance_mode' =>  External_system::MAINTENANCE_FINISH
                );
                $this->external_system->setToMaintenanceOrPauseMode($data2,$gameApiId);
			}
			if($this->external_system->editDetailsGameMaintenanceSchedule($data,$id)){
				$result = array('status' => 'success');
				$this->returnJsonResult($result);
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('sys.gd25'));
			}
		}
	}

	public function viewGameApiUpdateHistory() {
		if (!$this->permissions->checkPermissions('game_api_history')) {
			$this->error_redirection();
		} else {

			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			// sets the history for breadcrumbs
			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active'));
			}

			$this->history->setHistory('header_system.system_word23', 'game_api/viewGameApiUpdateHistory');

			$constants = get_defined_constants(true)['user'];
			$game_api_type = [];
			foreach ($constants as $key => $value) {
				if (substr($key, -3) == 'API' && $key != 'SYSTEM_GAME_API') {
					if (!strrpos($key, 'PAYMENT')) {
						$game_api_type[$key] = $value;
					}
				}
			}
			$data['game_apis_arr'] = $game_api_type;	
			
			$data['conditions'] = $this->safeLoadParams(array(
				'date_from' => $this->utils->getTodayForMysql() . ' 00:00:00',
				'date_to' => $this->utils->getTodayForMysql() . ' 23:59:59',
				'search_by'=> '',
				'enable_date' => '',
				'action' => '',
				'game_platform_id' => '',

			));


			$this->loadTemplate('System Management', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/view_game_api_update_history', $data);
			$this->template->render();
		}
	}


}

/* End of file game_api.php */
/* Location: ./application/controllers/game_api.php */
