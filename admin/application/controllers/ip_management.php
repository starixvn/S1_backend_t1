<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';
/**
 * IP Management
 *
 * IP Management Controller for FHI OG System
 *
 * General behaviors include
 * * update currency
 * * Add/update/delete domains
 * * Update setting for the duplicate account setting of fields
 * * Update the field permissions for the player form
 * * Update the field permissions for the affiliate form
 * * Lists all IP Addresses
 * * Add/Update/Delete a certain IP Address
 * * Able to Block and Allow a certain IP Address
 * * Able to Enable IP Whitelisting
 *
 * @category ip_management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Ip_management extends BaseController {
	const IPRULES_SETTING = 'ip_rules';
	function __construct() {
		parent::__construct();

		$this->load->helper('url');
		$this->load->library(array('permissions', 'template', 'form_validation', 'report_functions', 'salt', 'ip_manager'));
		$this->load->model(array('ip'));

		$this->permissions->checkSettings();
		$this->permissions->setPermissions();
	}

	/**
	 * save action to Logs
	 *
	 * @return	rendered Template
	 */
	// protected function saveAction($action, $description) {
	// 	$today = date("Y-m-d H:i:s");

	// 	$data = array(
	// 		'username' => $this->authentication->getUsername(),
	// 		'management' => 'System Management',
	// 		'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
	// 		'action' => $action,
	// 		'description' => $description,
	// 		'logDate' => $today,
	// 		'status' => '0',
	// 		'ip' => $this->utils->getIP(),
	// 	);

	// 	$this->report_functions->recordAction($data);
	// }

	/**
	 * set message for users
	 *
	 * @param	int
	 * @return  set session user data
	 */
	// protected function alertMessage($type, $message) {
	// 	switch ($type) {
	// 	case '1':
	// 		$show_message = array(
	// 			'result' => 'success',
	// 			'message' => $message,
	// 		);
	// 		$this->session->set_userdata($show_message);
	// 		break;

	// 	case '2':
	// 		$show_message = array(
	// 			'result' => 'danger',
	// 			'message' => $message,
	// 		);
	// 		$this->session->set_userdata($show_message);
	// 		break;

	// 	case '3':
	// 		$show_message = array(
	// 			'result' => 'warning',
	// 			'message' => $message,
	// 		);
	// 		$this->session->set_userdata($show_message);
	// 		break;
	// 	}
	// }

	/**
	 * overview : loads template
	 *
	 * detail : Loads template for view based on regions in config > template.php
	 *
	 * @param  string 	$title
	 * @param  string 	$description
	 * @param  string 	$keywords
	 * @param  string 	$activenav
	 * @return rendered template
	 */
	private function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->add_js('resources/js/system_management/ip_management.js');

		// $this->template->add_js('resources/js/datatables.min.js');
		// $this->template->add_js('resources/js/jquery.dataTables.min.js');
		// $this->template->add_js('resources/js/dataTables.responsive.min.js');

		$this->template->add_css('resources/css/general/style.css');
		//$this->template->add_css('resources/css/jquery.dataTables.css');
		//$this->template->add_css('resources/css/dataTables.responsive.css');
		// $this->template->add_css('resources/css/datatables.min.css');

		$this->loadThirdPartyToTemplate('datatables');

		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('userId', $this->authentication->getUserId());

		$this->template->write_view('sidebar', 'system_management/sidebar');
	}

	/**
	 * overview : error access
	 *
	 * detail : show error message if user can't access the page
	 */
	private function error_access() {
		$this->loadTemplate('IP Management', '', '', 'system');
		$systemUrl = $this->utils->activeSystemSidebar();
		$data['redirect'] = $systemUrl;

		$message = lang('con.i01');
		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		$this->template->write_view('main_content', 'error_page', $data);
		$this->template->render();
	}

	/**
	 * overview
	 *
	 * detail: Index Page of User Management
	 * @return	void
	 */
	public function index() {
		redirect(BASEURL . 'ip_management/viewList');
	}


	/**
	 * overview : view IP's
	 *
	 * detail : view all IP either blocked/not-blocked IP
	 * @return render template
	 */
	public function viewList() {
		if (!$this->permissions->checkPermissions('ip')) {
			$this->error_access();
		} else {
			$this->loadTemplate('IP Management', '', '', 'system');

			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}
			// For crumbs
			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active', 'system_crumb' => 'active'));
			}

			$data['ip'] = $this->ip_manager->getAllIp();

			$data['ipList'] = $this->checkIpList();
			$data['isMyIpExists'] = $this->ip_manager->checkIfIpExists($this->input->ip_address());

			$this->template->write_view('main_content', 'system_management/view_list', $data);
			$this->template->render();
		}
	}


	/**
	 * overview : check ip list
	 *
	 * detail : get if ipList will be use in config.xml
	 * @return  array
	 */
	public function checkIpList() {
		// $xml = new DOMDocument();
		// $xml->load(BASEPATH . "config.xml");

		// $nodes = $xml->getElementsByTagName("ipList");
		// $node = $nodes->item(0);

		$this->load->model('operatorglobalsettings');
		$data = $this->operatorglobalsettings->getOperatorGlobalSetting(self::IPRULES_SETTING);
		return $data[0]['value'];
	}

	/**
	 * overview : add ip
	 *
	 * detail : post add ip
	 * @return void
	 */
	public function addIp() {
		if (!$this->permissions->checkPermissions('ip')) {
			$this->error_access();
		} else {
			$type_of_action = $this->input->post('type_of_action');
			$today = date("Y-m-d H:i:s"); //will get the date based on the httpd.conf's timezone.

			if ($type_of_action != 'Submit') {
				$ip_name = $this->input->ip_address();
				$result = $this->checkIfIpExists($ip_name);

				if ($result == FALSE) {
					$message = $ip_name . " " . lang('con.i02');
				} else {
					$data = array(
						'ipName' => $ip_name,
						'createTime' => $today,
						'createPerson' => $this->authentication->getUserId(),
					);

					$this->ip_manager->addIp($data);

					$this->saveAction(lang('system.addedIp'), lang('system.addedIp') . ' - ' . $ip_name);

					$message = lang('con.i03') . ": " . $ip_name;
				}

				$this->alertMessage(1, $message); //will set and send message to the user
				redirect('ip_management/viewList'); //redirect to addRole
			} else {
				$this->form_validation->set_rules('ip_name', 'IP Address', 'trim|required|xss_clean|callback_checkIfIpExists');

				if ($this->form_validation->run() == FALSE) {
					$this->viewList(); //redirect to addRole
				} else {
					$ip_name = $this->input->post('ip_name');
					$remarks = $this->input->post('remarks');

					$data = array(
						'ipName' => $ip_name,
						'remarks' => $remarks,
						'createTime' => $today,
						'createPerson' => $this->authentication->getUserId(),
					);

					$this->ip_manager->addIp($data);

					$this->saveAction(lang('system.addedIp'), lang('system.addedIp') . ' - ' . $ip_name);

					$message = lang('con.i03') . ": " . $ip_name;
					$this->alertMessage(1, $message); //will set and send message to the user
					redirect('ip_management/viewList'); //redirect to addRole
				}
			}
		}
	}


	/**
	 * overview : check if ip exist
	 *
	 * detail : check if ip address exists
	 * @param  string 	$ip
	 * @return bool
	 */
	public function checkIfIpExists($ip) {
		$result = $this->ip_manager->checkIfIpExists($ip);

		if ($result) {
			$this->form_validation->set_message('checkIfIpExists', 'The %s already exists');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	 * post delete ip
	 *
	 * @param int
	 */
	// public function deleteIp($ip_id) {
	// 	if (!$this->permissions->checkPermissions('ip')) {
	// 		$this->error_access();
	// 	} else {
	// 		$ipDetails = $this->ip->getIPDetails($ip_id);
	// 		$this->ip_manager->deleteIp($ip_id);

	// 		$this->saveAction(lang('system.deleteIp'), lang('system.deleteIp') . " - " . $ipDetails['ipName']);

	// 		$message = lang('con.i04');
	// 		$this->alertMessage(1, $message); //will set and send message to the user
	// 		redirect(BASEURL . 'ip_management/viewList', 'refresh'); //redirect to addRole
	// 	}
	// }

	/**
	 * overview : lock ip
	 *
	 * detail : post lock ip
	 * @param  int 	$ip_id
	 * @param  int 	$lock
	 * @return void
	 */
	public function lockIp($ip_id, $lock) {
		if (!$this->permissions->checkPermissions('ip')) {
			$this->error_access();
		} else {
			$status = $lock == 1 ? lang('system.blockIp') : lang('system.allowIp');
			$data = array(
				"status" => $lock,
			);

			$this->ip_manager->lockIp($ip_id, $data);

			$this->saveAction($status, $status . " - " . $ip_name);

			$this->alertMessage(1, $status); //will set and send message to the user
			redirect(BASEURL . 'ip_management/viewList', 'refresh'); //redirect to addRole
		}
	}

	/**
	 * overview : edit ip
	 *
	 * @param  int 	$ip_id
	 * @return render template
	 */
	public function editIp($ip_id) {
		if (!$this->permissions->checkPermissions('ip')) {
			$this->error_access();
		} else {
			$this->loadTemplate('IP Management', '', '', 'system');

			$data['ip'] = $this->ip_manager->getAllIp();
			$data['ip_by_id'] = $this->ip_manager->getIpById($ip_id);
			$data['ipList'] = $this->checkIpList();
			$data['isMyIpExists'] = $this->ip_manager->checkIfIpExists($this->input->ip_address());
			$this->template->write_view('main_content', 'system_management/view_list', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : verify edit ip
	 *
	 * detail : verifies the edited ip
	 * @param  int 	$ip_id
	 * @return void
	 */
	public function verifyEditIp($ip_id) {
		$type_of_action = $this->input->post('type_of_action');
		$oldIp = $this->ip->getIPDetails($ip_id);
		if ($type_of_action == 'Submit') {
			$this->form_validation->set_rules('ip_name', 'IP Name', 'trim|required|xss_clean');

			if ($this->form_validation->run() == FALSE) {
				$this->viewList(); //redirect to addRole
			} else {
				$ip_name = $this->input->post('ip_name');
				$remarks = $this->input->post('remarks');

				$data = array(
					'ipName' => $ip_name,
					'remarks' => $remarks,
				);

				$this->ip_manager->editIp($ip_id, $data);
			}

			$this->saveAction(lang('system.editIp'), lang('system.editIp') . " - [" . lang('adjustmenthistory.title.beforeadjustment') . '] ' . $oldIp['ipName'] . ' [' . lang('adjustmenthistory.title.afteradjustment') . '] ' . $ip_name);

			$message = lang('con.i09') . ": " . $ip_name;
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect(BASEURL . 'ip_management/viewList', 'refresh'); //redirect to addRole
		}

		redirect(BASEURL . 'ip_management/viewList', 'refresh'); //redirect to addRole
	}

	/**
	 * overview : check ip
	 *
	 * detail : check ip process
	 * @return void
	 */
	public function checkIp() {
		if (!empty($this->input->post('ip'))) {
			$type_of_action = $this->input->post('type_of_action');
			$ip = implode(",", $this->input->post('ip'));

			switch ($type_of_action) {
			case 'Delete':
				$this->deleteCheckIp($ip);
				break;

			case 'Freeze':
				$this->lockCheckIp($ip, '1');
				break;

			case 'UnFreeze':
				$this->lockCheckIp($ip, '0');
				break;

			default:
				break;
			}
		} else {
			$message = lang('con.i10');
			$this->alertMessage(2, $message); //will set and send message to the user
			redirect(BASEURL . 'ip_management/viewList', 'refresh');
		}
	}

	/**
	 * check ip process
	 *
	 */
	/**
	 * overview : manage ip
	 *
	 * detail : check ip process
	 * @param  int 	$id
	 * @param  int 	$status
	 * @return void
	 */
	public function manageIp($id, $status) {
		if (!$this->permissions->checkPermissions('ip')) {
			$this->error_access();
		} else {
			$ipDetails = $this->ip->getIPDetails($id);

			if ($status == Ip::STATUS_DELETE) {
				$this->saveAction(lang('system.deleteIp'), lang('system.deleteIp') . " - " . $ipDetails['ipName']);
				$message = lang('system.message.ipmanagement.delete.success');
				$this->ip->deleteIp($id);
			} else {
				$this->ip->editIp($id, array('status' => $status));
				$ipDetails = $this->ip->getIPDetails($id);

				if ($status == Ip::STATUS_ALLOW) {
					$this->saveAction(lang('system.allowIp'), lang('system.allowIp') . " - " . $ipDetails['ipName']);
					$message = lang('system.message.ipmanagement.allow.success');

				} elseif ($status == Ip::STATUS_BLOCK) {
					$this->saveAction(lang('system.blockIp'), lang('system.blockIp') . " - " . $ipDetails['ipName']);
					$message = lang('system.message.ipmanagement.block.success');
				}
			}

			$this->alertMessage(1, $message);

			redirect(BASEURL . 'ip_management/viewList', 'refresh');
		}
	}

	/**
	 * overview: delete check ip
	 *
	 * @param  string 	$ip
	 * @return void
	 */
	public function deleteCheckIp($ip) {
		if (!$this->permissions->checkPermissions('ip')) {
			$this->error_access();
		} else {
			$ip_list = explode(',', $ip);

			foreach ($ip_list as $value) {
				$ipDetails = $this->ip->getIPDetails($value);
				$this->saveAction(lang('system.deleteIp'), lang('system.deleteIp') . " - " . $ipDetails['ipName']);
				$this->ip->deleteIp($value);
			}

			//$this->saveAction('Delete IP', "User " . $this->authentication->getUsername() . " deleted an IP");

			$message = lang('con.i04');
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect(BASEURL . 'ip_management/viewList', 'refresh'); //redirect to addRole
		}
	}

	/**
	 * overview : check locked ip
	 *
	 * @param  string 	$ip
	 * @param  int 		$lock
	 * @return void
	 */
	public function lockCheckIp($ip, $lock) {
		if (!$this->permissions->checkPermissions('ip')) {
			$this->error_access();
		} else {
			$ip_list = explode(',', $ip);
			$status = $lock == 1 ? lang('system.blockIp') : lang('system.allowIp');

			$data = array(
				"status" => $lock,
			);

			foreach ($ip_list as $value) {
				$this->ip->lockIp($value, $data);
				$ipDetails = $this->ip->getIPDetails($value);
				$this->saveAction($status, $status . " - " . $ipDetails['ipName']);
			}

			if ($lock == 1) {
				$message = lang('system.blockIp');
			} else {
				$message = lang('system.allowIp');
			}

			$this->alertMessage(1, $message); //will set and send message to the user
			redirect(BASEURL . 'ip_management/viewList', 'refresh'); //redirect to addRole
		}
	}

	/**
	 * overview : set ip list
	 *
	 * detail : able/disable ip white listing
	 * @param  array 	$result
	 * @return void
	 */
	public function setIpList($result) {
		$this->load->model(array('operatorglobalsettings', 'ip'));
		$this->load->library(['authentication']);
		if ($result == 'true') {
			if (!$this->ip->checkIfIpExists($this->utils->getIP())) {

				$data = array(
					'ipName' => $this->utils->getIP(),
					'createTime' => $this->utils->getNowForMysql(),
					'createPerson' => $this->authentication->getUserId(),
				);

				$this->ip->addIp($data);

				$this->saveAction(lang('system.enableIpWhitelisting'), lang('system.enableIpWhitelisting'));
				$message = '[' . lang('system.enableIpWhitelisting') . '] ' . lang('con.i03') . ": " . $this->utils->getIP();
			} else {
				$this->saveAction(lang('system.enableIpWhitelisting'), lang('system.enableIpWhitelisting'));
				$message = lang('system.enableIpWhitelisting');
			}
		} else {
			$this->saveAction(lang('system.disableIpWhitelisting'), lang('system.disableIpWhitelisting'));
			$message = lang('system.disableIpWhitelisting');
		}

		$data = array(
			'name' => self::IPRULES_SETTING,
			'value' => $result,
		);
		// echo "<pre>"; print_r($date); exit;
		$this->operatorglobalsettings->setOperatorGlobalSetting($data);

		$this->alertMessage(1, $message); //will set and send message to the user
		redirect('ip_management/viewList', 'refresh');
	}

	/**
	 * overview : get description key
	 * @return array
	 */
	protected function getDeskeyOG() {
		return $this->config->item('DESKEY_OG');
	}

}

/* End of file ip_management.php */
/* Location: ./application/controllers/ip_management.php */