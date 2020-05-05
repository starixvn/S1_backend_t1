<?php
require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/duplicate_record_whitelist.php';
/**
 * User Management
 *
 * General behaviors include
 * * Lists all user logs
 * * Filtered user log report lists
 * * Add/Update/Delete User details
 * * Lists All system users
 * * Able to reset password of a certain user
 * * Able to view the logs of a certain user
 * * Able to Lock/Unlock a certain user
 * * Filter/Search the user lists
 * * Batch Delete/Lock/Unlock users
 *
 * @category user_Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 *
 */

class User_Management extends BaseController {

	use duplicate_record_whitelist;

	const CURRENCY_ACTIVE = 0;
	const CURRENCY_INACTIVE = 1;
    const LOCK = 2;
    const UNLOCK = 1;

	function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->library(array('permissions', 'form_validation', 'template', 'pagination', 'report_functions', 'player_manager', 'duplicate_account'));

		$this->permissions->checkSettings();
		$this->permissions->setPermissions(); //will set the permission for the logged in user
	}

	/**
	 * overview : alert message
	 *
	 * detail: set message for users
	 * @param int 		$type
	 * @param string 	$message
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
	 * overview : loads template
	 *
	 * detail : Loads template for view based on regions in config > template.php
	 *
	 * @param  string 	$title
	 * @param  string 	$description
	 * @param  string 	$keywords
	 * @param  string 	$activenav
	 * @return load template
	 */
	private function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->add_js('resources/js/system_management/user_management.js');
		$this->template->add_js('resources/js/datatables.min.js');

		$this->template->add_css('resources/css/general/style.css');
		$this->template->add_css('resources/css/datatables.min.css');
		$this->template->add_css('resources/css/system_management/style.css');

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
	 * deatil: Shows Error message if user can't access the page
	 * @return	rendered Template
	 */
	private function error_access() {
		$this->loadTemplate('User Management', '', '', 'system');
		$systemUrl = $this->utils->activeSystemSidebar();
		$data['redirect'] = $systemUrl;

		$message = lang('con.usm01');
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
		redirect('user_management/viewUsers'); //this will redirect to viewUsers instead
	}

	/**
	 * overview : view users
	 *
	 * detail: display the lists of all the users
	 * @return	rendered Template with array of data
	 */
	public function viewUsers() {
		if (!$this->permissions->checkPermissions('view_admin_users')) {
			$this->error_access();
		} else {
			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			// sets the history for breadcrumbs
			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active'));
			}

			$this->history->setHistory('header_system.system_word23', 'user_management/viewUsers');

			$data['export_report_permission'] = $this->permissions->checkPermissions('export_view_users_report');

			$um_data = array(
				"um_username" => null,
				"um_realname" => null,
				"um_department" => null,
				"um_position" => null,
				"um_role" => null,
				"um_create_by" => null,
				"um_login_ip" => null,
				"um_status" => null,
			);
			$this->session->set_userdata($um_data);

			if ($this->session->userdata('u_last_logout_time') == '' && $this->session->userdata('u_create_time') == '') {
				$last_logout_time = "unchecked";
				$create_time = "unchecked";
				$create_by = "unchecked";

				$data = array(
					'u_last_logout_time' => $last_logout_time,
					'u_create_time' => $create_time,
					'u_create_by' => $create_by,
				);
				$this->session->set_userdata($data);
			}

			$number_player_list = '';
			$sort_by = '';
			$in = '';

			if ($this->session->userdata('u_number_player_list')) {
				$number_player_list = $this->session->userdata('u_number_player_list');
			} else {
				$number_player_list = 5;
			}

			if ($this->session->userdata('u_sort_by')) {
				$sort_by = $this->session->userdata('u_sort_by');
			} else {
				$sort_by = 'username';
			}

			if ($this->session->userdata('u_in')) {
				$in = $this->session->userdata('u_in');
			} else {
				$in = 'asc';
			}

			$user_id = $this->authentication->getUserId();
			$user = $this->user_functions->searchUser($user_id);
			$data['currentUsername']=$this->authentication->getUsername();

			$hasRolesAccess = $this->permissions->checkPermissions('admin_manage_user_roles');

			$users = $this->user_functions->getAllUsers($user_id, null, null, $sort_by, $in, $user['roleId'], $hasRolesAccess); // will get all users based on the number set from $config['per_page']

			$data['users'] = array();
			if(!empty($users)) {
				foreach($users as $user) {
					$user['created_by'] = $this->user_functions->getUserById($user['createPerson'])['username'];
					array_push($data['users'], $user);
				}
			}

			$data['user_group'] = $this->users->getAllAdminUsers();

			$data['currentUser'] = $user_id;
			$data['roles'] = $this->rolesfunctions->getAllRolesByUser($user_id, null, null, $hasRolesAccess);

			$data['filter'] = 'ASC';

			$data['const_unlocked'] = 1;
			$data['const_locked'] = 2;

			$data['admin_user_id'] = $this->authentication->getUserId();

			$this->loadTemplate('System Management', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/view_users', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : view logs
	 *
	 * detail: view the lists of logs of all the users
	 * @return load template
	 */
	public function viewLogs() {
		if (!$this->permissions->checkPermissions('user_logs_report')) {
			$this->error_access();
		} else {
			$this->load->model(array('roles'));
			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active', 'system_crumb' => 'active'));
			}

			$data['managements'] = $this->utils->getConfig('management_logs_list');
			$data['roles'] = $this->roles->retrieveAllRoles();

			$this->loadTemplate('System Management', '', '', 'system');
			$this->template->write_view('main_content', 'report_management/view_logs', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : get logs pages
	 *
	 * detail: View a Sorted Users
	 * @param  string 	$segment
	 * @return rendered Template with array of data
	 */
	public function get_log_pages($segment = '') {
		$data['count_all'] = count($this->report_functions->getAllLogs(null, null));
		$config['base_url'] = "javascript:get_log_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '10';
		$config['num_links'] = '1';

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
		$data['logs'] = $this->report_functions->getAllLogs(null, $segment);

		$this->load->view('report_management/ajax_logs', $data);
	}

	/**
	 * overview : post sort page
	 *
	 * detail : set the sorting in the session
	 * @POST $sort_by
	 * @return void
	 */
	public function postSortPage() {
		$sort_by = $this->input->post('sort_by');
		$this->session->set_userdata('u_sort_by', $sort_by);

		$in = $this->input->post('in');
		$this->session->set_userdata('u_in', $in);

		$number_player_list = $this->input->post('number_player_list');
		$this->session->set_userdata('u_number_player_list', $number_player_list);
		redirect(BASEURL . 'user_management/viewUsers');
	}

	/**
	 * overview : post change columns
	 *
	 * detail: manage the columns of the users lists
	 * @POST $realname
	 * @POST $department
	 * @POST $position
	 * @POST $role
	 * @POST $login_ip
	 * @POST $last_login_time
	 * @POST $create_time
	 * @POST $create_by
	 * @POST $status
	 * @return void
	 */
	public function postChangeColumns() {
		$realname = $this->input->post('realname') ? "checked" : "unchecked";
		$department = $this->input->post('department') ? "checked" : "unchecked";
		$position = $this->input->post('position') ? "checked" : "unchecked";
		$role = $this->input->post('role') ? "checked" : "unchecked";
		$login_ip = $this->input->post('login_ip') ? "checked" : "unchecked";
		$last_login_time = $this->input->post('last_login_time') ? "checked" : "unchecked";
		$last_logout_time = $this->input->post('last_logout_time') ? "checked" : "unchecked";
		$create_time = $this->input->post('create_time') ? "checked" : "unchecked";
		$create_by = $this->input->post('create_by') ? "checked" : "unchecked";
		$status = $this->input->post('status') ? "checked" : "unchecked";

		$data = array(
			'u_realname' => $realname,
			'u_department' => $department,
			'u_position' => $position,
			'u_role' => $role,
			'u_login_ip' => $login_ip,
			'u_last_login_time' => $last_login_time,
			'u_last_logout_time' => $last_logout_time,
			'u_create_time' => $create_time,
			'u_create_by' => $create_by,
			'u_status' => $status,

		);
		$this->session->set_userdata($data);
		redirect(BASEURL . 'user_management/viewUsers');
	}

	/**
	 * overview : get user pages
	 *
	 * detail: View a Sorted Users
	 * @param  int 	$segment
	 * @return rendered Template with array of data
	 */
	public function get_user_pages($segment = '') {

		if ($this->session->userdata('u_last_logout_time') == '' && $this->session->userdata('u_create_time') == '' && $this->session->userdata('u_create_by') == '') {
			$last_logout_time = "unchecked";
			$create_time = "unchecked";
			$create_by = "unchecked";

			$data = array(
				'u_last_logout_time' => $last_logout_time,
				'u_create_time' => $create_time,
				'u_create_by' => $create_by,
			);
			$this->session->set_userdata($data);
		}

		$number_player_list = '';
		$sort_by = '';
		$in = '';

		if ($this->session->userdata('u_number_player_list')) {
			$number_player_list = $this->session->userdata('u_number_player_list');
		} else {
			$number_player_list = 5;
		}

		if ($this->session->userdata('u_sort_by')) {
			$sort_by = $this->session->userdata('u_sort_by');
		} else {
			$sort_by = 'username';
		}

		if ($this->session->userdata('u_in')) {
			$in = $this->session->userdata('u_in');
		} else {
			$in = 'asc';
		}

		$user_id = $this->authentication->getUserId();
		$user = $this->user_functions->searchUser($user_id);
		$data['count_all'] = count($this->user_functions->getAllUsers($user_id, null, null, $sort_by, $in, $user['roleId'])); // will return number of users
		$config['base_url'] = "javascript:get_user_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 5;
		$config['num_links'] = 2;
		$config['first_tag_open'] = "<li>";
		$config['last_tag_open'] = $config['next_tag_open'] = $config['prev_tag_open'] = $config['num_tag_open'] = '<li>';
		$config['first_tag_close'] = "</li>";
		$config['last_tag_close'] = $config['next_tag_close'] = $config['prev_tag_close'] = $config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$this->pagination->initialize($config);

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);

		$data['users'] = $this->user_functions->getAllUsers($user_id, null, $segment, $sort_by, $in, $user['roleId']);
		$data['currentUser'] = $user_id;

		$data['filter'] = 'ASC';

		$this->load->view('system_management/view_users_pages', $data);
	}

	/**
	 * overview : view add user
	 *
	 * detail: show the lists of all the role, and link to new user form
	 * @return	rendered Template with array of data
	 */
	public function viewAddUser() {
		if (!$this->permissions->checkPermissions('view_admin_users')) {
			$this->error_access();
		} else {
			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			// sets the history for breadcrumbs
			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active'));
			}
			$this->history->setHistory('header_system.system_word24', 'user_management/viewAddUser');

			$user_id = $this->authentication->getUserId();

			$hasRolesAccess = $this->permissions->checkPermissions('admin_manage_user_roles');

			$data['roles'] = $this->rolesfunctions->getAllRolesByUser($user_id, null, null, $hasRolesAccess);

			$this->loadTemplate('User Management', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/add_users', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : next add user
	 *
	 * detail: View of Next Add Users page
	 * @return	rendered Template with array of data
	 */
	public function nextAddUser() {
		if (validation_errors() == false) {
			$this->form_validation->set_rules('roleId', 'Role', 'trim|required|xss_clean');

			if ($this->form_validation->run() == false) {
				$message = lang('con.usm02');
				$this->alertMessage(2, $message);
				$this->viewAddUser();
			} else {
				$username = '';
				$data['hiddenPassword'] = $this->user_functions->randomizer($username); // will get a randomized password when the page loads
				$roleId = $this->input->post('roleId');

				$data['role'] = $this->rolesfunctions->getRolesById($roleId);

				$this->loadTemplate('User Management', '', '', 'system');
				$this->template->write_view('main_content', 'system_management/next_add_users', $data);
				$this->template->render();
			}
		} else {
			$username = '';
			$data['hiddenPassword'] = $this->user_functions->randomizer($username); // will get a randomized password when the page loads
			$roleId = $this->input->post('roleId');

			$data['role'] = $this->rolesfunctions->getRolesById($roleId);

			$this->loadTemplate('User Management', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/next_add_users', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : view reset password
	 *
	 * detail: View of Reset User's Password page
	 * @param  int 	$userId
	 * @return rendered Template with array of data
	 */
	public function viewResetPassword($userId) {
		if (!$this->permissions->checkPermissions('reset_password')) {
			$this->error_access();
		} else {
			$data['user'] = $this->user_functions->getUserById($userId);

			$data['hiddenPassword'] = $this->user_functions->randomizer($data['user']['username']); //will get randomized password when the page loads
			$this->loadTemplate('User Management', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/reset_password', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : view permission
	 *
	 * detail: View of Permission page
	 * @param  int 	$id 	user id
	 * @return rendered Template with array of data
	 */
	public function viewPermission($id) {
		if (!$this->permissions->checkPermissions('admin_manage_user_roles')) {
			$this->error_access();
		} else {
			$data['role'] = $this->rolesfunctions->getRolesById($id, null, null); // will get all role
			$role_id = $this->user_functions->getRoleId($id); // will get the id of role using the id of the user
			$role_id_give = $this->permissions->getRoleId();

			$data['functions'] = $this->rolesfunctions->getAllFunctionsGiving($role_id_give);

			$data['roles'] = $this->rolesfunctions->getRolesById($role_id['roleId']); // will get specific role
			$data['rolesfunctions'] = $this->rolesfunctions->getRolesFunctionsById($role_id['roleId']); // will get specific function used by role
			$data['rolesfunctions_giving'] = $this->rolesfunctions->getRolesFunctionsGivingIdById($role_id['roleId']);

			$data['user'] = $this->user_functions->searchUser($id);

			$this->loadTemplate('User Management', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/view_permission', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : view user
	 *
	 * detail: View of specific User page
	 * @param  int 	$id 	user id
	 * @return rendered Template with array of data
	 */
	public function viewUser($id) {
		if (!$this->permissions->checkPermissions('admin_manage_user_roles')) {
			$this->error_access();
		} else {
			$this->load->model(array('users'));
			$user_id = $this->authentication->getUserId();
			$data['roles'] = $this->rolesfunctions->getAllRolesByUser($user_id, null, null);
			$data['user'] = $this->users->getUserInfoById($id);

			$this->loadTemplate('User Management', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/view_user', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : view set role
	 *
	 * detail: View of Set Role user page
	 * @param  int 	$id 	user id
	 * @return rendered Template with array of data
	 */
	public function viewSetRole($id) {
		if (!$this->permissions->checkPermissions('admin_manage_user_roles')) {
			$this->error_access();
		} else {
			$user_id = $this->authentication->getUserId();

			$data['user'] = $this->user_functions->getUserById($id);
			$data['userroles'] = $this->user_functions->getUserRole($id);
			$data['roles'] = $this->rolesfunctions->getAllRolesByUser($user_id, null, null);

			$this->loadTemplate('User Management', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/view_set_role', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : view edit user
	 *
	 * detail: View of Edit user page
	 * @param  int 	$id 	user id
	 * @return rendered Template with array of data
	 */
	public function viewEditUser($id) {
		if (!$this->permissions->checkPermissions('admin_manage_user_roles')) {
			$this->error_access();
		} else {
			$hasRolesAccess = $this->permissions->checkPermissions('admin_manage_user_roles');

			$data['user'] = $this->user_functions->searchUser($id);
			$user_id = $this->authentication->getUserId();
			$data['roles'] = $this->rolesfunctions->getAllRolesByUser($user_id, null, null, $hasRolesAccess);
			//echo "<pre>";print_r($data);exit;

			# check if role exist user role exist
			if (isset($data['roleId'])) {
				if (!in_array($data['roleId'], array_column($data['roles'] , 'roleId'))) {
					array_push($data['roles'], array('roleId' => $data['user']['roleId'], 'roleName' => $data['user']['roleName']));
				}
			}

			$this->loadTemplate('User Management', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/edit_user', $data);
			$this->template->render();
		}
	}

	/**
	 *
	 * overview : View of User setting page
	 *
	 * @param  int 	$id 	user id
	 * @return rendered Template with array of data
	 */
	public function viewUserSetting() {
		//check permission
		$loggedUserId=$this->authentication->getUserId();
		if(empty($loggedUserId)){
			redirect('/auth/login');
			return;
		}

		$id=$loggedUserId;

		$data['user'] = $this->user_functions->searchUser($id);
		$data['currentLanguage'] = $this->language_function->getCurrentLanguage();

		$this->load->model('operatorglobalsettings');
		$logoSettings = $this->operatorglobalsettings->getSettingJson('sys_default_logo');
		$data['useSysDefault'] =  $logoSettings['use_sys_default'] ? 'checked' : '';

		$this->loadTemplate('User Management', '', '', 'system');
		$this->template->write_view('main_content', 'system_management/view_user_setting', $data);
		$this->template->render();
	}

	/**
	 * overview : set current language
	 *
	 * detail: setting the language
	 * @param  string 	$language
	 * @return rendered Template with array of data
	 */
	public function setCurrentLanguage($language) {
		$this->language_function->setCurrentLanguage($language);

        $lang=Language_function::ISO2_LANG[$language];
		//set lang cookies
        $this->load->library(['session']);
        $this->session->setLanguageCookie($lang);

		$message = lang('con.usm03');
		$this->alertMessage(1, $message);
		$arr = array('status' => 'success');

		// echo json_encode($arr);
		$this->returnJsonResult($arr);
	}

	/**
	 * overview: Post user inputs to edit user details
	 *
	 * @POST $username
	 * @POST $realname
	 * @POST $department
	 * @POST $position
	 * @POST $email
	 * @POST $wid_amt
	 * @POST $role_id
	 * @POST $password
	 * @POST $single_amt
	 * @param  int 	$id 	user id
	 * @return redirected page
	 */
	public function postEditUser($id) {
		if (!$this->permissions->checkPermissions('admin_manage_user_roles')) {
			$this->error_access();
		}

		$roleId = $this->input->post('role_id');
		$this->form_validation->set_rules('realname', lang('Real Name'), 'trim|required|xss_clean|max_length[16]');
		$this->form_validation->set_rules('department', lang('Deparment'), 'trim|required|xss_clean');
		$this->form_validation->set_rules('position', lang('Position'), 'trim|required|xss_clean');
		$this->form_validation->set_rules('email', lang('Email'), 'trim|required|xss_clean|callback_isUniqueEmail');
		$this->form_validation->set_rules('tele_id', lang('Telemarketing ID'), 'trim|xss_clean');

		if ($this->rolesfunctions->checkRole($roleId) == true) {
			$this->form_validation->set_rules('wid_amt', 'Maximun Withdrawal Amount', 'trim|xss_clean|numeric|callback_checkWidAmtMaxLen|callback_checkWidAmount');
			$this->form_validation->set_rules('single_amt', 'Single Maximun Withdrawal Amount', 'trim|xss_clean|numeric|callback_checkSinAmtMaxLen|callback_checkSinAmount');
		}

		if ($this->form_validation->run() == false) {
			// if satisfies the set rules
			$message = lang('con.usm04');
			$this->alertMessage(2, $message);
			$this->viewEditUser($id);
		} else {
			$username = $this->users->getUsernameById($id);
			if(empty($username)){
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Not found user'));
				$this->index();
				return;
			}

			$realname = $this->input->post('realname');
			$department = $this->input->post('department');
			$position = $this->input->post('position');
			$email = $this->input->post('email');
			$wid_amt = $this->input->post('wid_amt');
			$role_id = $this->input->post('userRole');
			$password = $this->input->post('password');
			$single_amt = $this->input->post('single_amt');
			$today = date("Y-m-d H:i:s");
			$tele_id = $this->input->post('tele_id');

			$data = array(
				// 'username' => $username,
				'realname' => $realname,
				'department' => $department,
				'position' => $position,
				'email' => $email,
				'maxWidAmt' => intval($wid_amt),
				'singleWidAmt' => intval($single_amt),
				'tele_id' => $tele_id,
			);

			$data_role = array(
				'roleId' => $role_id,
				'userId' => $id,
			);
			$this->load->model(['users', 'multiple_db_model']);
			$this->users->dbtransOnly(function()
					use($id, $data, $data_role){

				$result = $this->users->updateUser($id, $data);
				$result_role = $this->users->syncUserRole($id, $data_role);

				return $result && $result_role;
			});

			$this->saveAction('user_management', 'Edit User', $username . "'s account has been edited by " . $this->authentication->getUsername());

			$this->syncUserCurrentToMDBWithLock($id, $username, false);

			$message = lang('con.usm05');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			$this->index();
		}
	}

	/**
	 * overview: Post user inputs to add user details
	 *
	 * @POST $check
	 * @POST $roleId
	 * @POST $password
	 * @POST $username
	 * @POST $realname
	 * @POST $department
	 * @POST $position
	 * @POST $email
	 * @POST $note
	 * @POST $max_wid_amt
	 * @return	redirected page
	 */
	public function postAddUser() {
		$check = $this->input->post('randomPassword');
		$roleId = $this->input->post('roleId');
		$password = '';

		if (!$check) {
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|max_length[34]');
			$this->form_validation->set_rules('cpassword', 'Confirm Password', 'trim|required|xss_clean|max_length[34]|matches[password]');
			$password = $this->input->post('password');
		} else {
			$password = $this->input->post('hiddenPassword');
		}

		if ($this->rolesfunctions->checkRole($roleId) == true) {
			$this->form_validation->set_rules('wid_amt', 'Maximun Withdrawal Amount', 'trim|xss_clean|numeric|callback_checkWidAmtMaxLen|callback_checkWidAmount');
			$this->form_validation->set_rules('single_amt', 'Single Maximun Withdrawal Amount', 'trim|xss_clean|numeric|callback_checkSinAmtMaxLen|callback_checkSinAmount');
		}

		$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|max_length[20]');
		$this->form_validation->set_rules('realname', 'Real Name', 'trim|xss_clean|max_length[16]');
		$this->form_validation->set_rules('department', 'Deparment', 'trim|required|xss_clean');
		$this->form_validation->set_rules('position', 'Position', 'trim|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'trim|xss_clean|callback_isUniqueEmail');
		$this->form_validation->set_rules('note', 'Note', 'trim|xss_clean');
		$this->form_validation->set_rules('roleId', 'Role', 'trim|required|xss_clean');

		if ($this->form_validation->run() == false) {
			$message = lang('con.usm04');
			$this->alertMessage(2, $message);
			$this->nextAddUser();
		} else {
			$username = $this->input->post('username');
			$realname = $this->input->post('realname');
			$department = $this->input->post('department');
			$position = $this->input->post('position');
			$email = $this->input->post('email');
			$note = $this->input->post('note');
			$create_person = $this->authentication->getUserId();
			$max_wid_amt = $this->input->post('wid_amt');
			$status = 1;
			$today = date("Y-m-d H:i:s");
			if(empty($realname)){
				$realname='';
			}
			if(empty($position)){
				$position='';
			}
			if(empty($email)){
				$email='';
			}

			$data = array(
				'username' => $username,
				'realname' => $realname,
				'department' => $department,
				'position' => $position,
				'email' => $email,
				'password' => $password,
				'note' => $note,
				'createPerson' => $create_person,
				'maxWidAmt' => ($max_wid_amt == null) ? 0 : $max_wid_amt,
				'status' => $status,
				'createTime' => $today,
			);

			$this->load->model(['users','multiple_db_model']);
			$checker = $this->users->selectUserExist($username);

			if ($checker) {
				// if user exists
				$message = lang('con.usm06') . " " . $username . " " . lang('con.usm07');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				$this->nextAddUser();
			} else {

				require_once APPPATH . 'libraries/phpass-0.1/PasswordHash.php';

				$userId=null;
				$success=$this->utils->globalLockUserRegistration($username, function()
						use(&$userId, $roleId, $data){

					$hasher = new PasswordHash('8', TRUE);
					$data['password'] = $hasher->HashPassword($data['password']);
					$userId = $this->users->insertData('adminusers', $data);
					$success=!empty($userId);
					if($success){
						$data = array(
							'roleId' => $roleId,
							'userId' => $userId,
						);

						$this->users->addUserRole($data);

						$this->syncUserCurrentToMDB($userId, false);
					}

					return $success;
				});

				if (!$success) {
					$message = lang('con.usm04');
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
					$this->nextAddUser();
				} else {

					$this->saveAction('user_management', 'Add User', $this->authentication->getUsername() . " created a new account " . $username);

					$message = lang('con.usm09');
					$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
					$this->viewUsers();
				}
			}
		}
	}

	/**
	 * overview: callback check Max Withdrawal Amount
	 *
	 * @return	int
	 */
	public function checkWidAmount() {
		$wid_amt = $this->input->post('wid_amt');

		if (!empty($wid_amt) && $wid_amt < 0) {
			$this->form_validation->set_message('checkWidAmount', "Maximum Withdrawal Amount should be >= 0");
			return false;
		}

		return true;
	}

	public function checkSinAmount() {
		$single_amt = $this->input->post('single_amt');
		if (!empty($single_amt) && $single_amt < 0) {
			$this->form_validation->set_message('checkWidAmount', "Max Amount for Every Single Withdrawal should be >= 0");
			return false;
		}

		return true;
	}

	public function checkWidAmtMaxLen() {
		$wid_amt = $this->input->post('wid_amt');
		$maxamt = "";
		for ($i = 1; $i <= 15; $i++)
			$maxamt .= "9";
		if (strlen($wid_amt) > 15) {
			$this->form_validation->set_message('checkWidAmtMaxLen', "Daily Maximum Approval for Withdrawal should be <= " . $maxamt);
			return false;
		}
		return true;
	}

	public function checkSinAmtMaxLen() {
		$single_amt = $this->input->post('single_amt');
		$maxamt = "";
		for ($i = 1; $i <= 9; $i++)
			$maxamt .= "9";
		if (strlen($single_amt) > 9) {
			$this->form_validation->set_message('checkSinAmtMaxLen', "Max Amount for Every Single Withdrawal should be <= " . $maxamt);
			return false;
		}
		return true;
	}

	/**
	 * overview: Post user inputs to reset password of user
	 *
	 * @POST $check
	 * @POST $email
	 * @param  int 	$id 	user id
	 * @return redirected page
	 */
	public function postResetPassword($id) {
		$check = $this->input->post('randomPassword');
		$email = $this->input->post('deleteEmail');

		if (!$check) {
			$this->form_validation->set_rules('npassword', 'New Password', 'trim|required|xss_clean|max_length[34]');
			$this->form_validation->set_rules('ncpassword', 'New Confirm Password', 'trim|required|xss_clean|max_length[34]|matches[npassword]');
			$password = $this->input->post('npassword');
		} else {
			$password = $this->input->post('hiddenPassword');
		}

		if ($this->form_validation->run() == false) {
			$message = lang('con.usm04');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			$this->viewResetPassword($id);
		} else {
			$this->load->model(['multiple_db_model','users']);

			$username=$this->users->getUsernameById($id);
			$today = date("Y-m-d H:i:s");

			if (!$email) {
				$data = array(
					'password' => $password,
				);
			} else {
				$data = array(
					'password' => $password,
					'email' => null,
				);
			}

			$checker = $this->users->selectUserExist($username);
			if (!$checker) {
				// if cannot find user
				$message = lang('con.usm10') . " " . $username . ". " . lang('con.usm11');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				$this->viewResetPassword($id, $username);
			} else {
				$hasher = new PasswordHash('8', TRUE);
				$data['password'] = $hasher->HashPassword($data['password']);
				$result = $this->users->updatePassword($id, $username, $data);

				$this->saveAction('user_management', 'Reset Password', $username . "'s password has been reset by " . $this->authentication->getUsername());

				//syncUserFromCurrentToOtherMDB
				$this->syncUserCurrentToMDBWithLock($id, $username, false);

				$message = "<b>" . $username . "</b>" . lang('con.usm12');
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
				$this->index();
			}
		}
	}

	/**
	 * overview : Post user inputs to delete users
	 *
	 * @param  int 	$userId 	user id
	 * @return redirected page
	 */
	public function postDeleteUsers($userId) {
		$username = $this->user_management->searchUser($userId);
		$this->load->model(['users']);
		$this->users->fakeDeleteUser($userId); //will delete user

		$this->saveAction('user_management', 'Delete User', "Account " . $username . " has been deleted by " . $this->authentication->getUsername());

		$message = lang('con.usm13');
		$this->alertMessage(1, $message); //will set and send message to the user
		$this->index();
	}

	/**
	 * overview : Post user inputs to delete users
	 *
	 * @POST $check
	 * @return	redirected page
	 */
	public function postDeleteUser() {
		if (!$this->permissions->checkPermissions('delete_user')) {
			return $this->error_access();
		}

		$check = $this->input->post('check');
		$today = date("Y-m-d H:i:s");
		$usernames = '';

		if (!empty($check)) {

			$this->load->model(['users']);
			foreach ($check as $userId) {
				$usernames = $usernames . $this->user_functions->searchUser($userId)['username'] . ', ';
				$this->users->fakeDeleteUser($userId);
			}

			$this->saveAction('user_management', 'Delete User', "Accounts " . $usernames . " has been deleted by " . $this->authentication->getUsername());

			$message = lang('con.usm14');
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message); //will set and send message to the user
			$this->index();
		} else {
			$message = lang('con.usm15');
			$this->alertMessage(2, $message);
			$this->index();
		}
	}

	/**
	 * overview : Post user inputs to change status
	 *
	 * @POST $check
	 * @param  int 	$type
	 * @return redirected page
	 */
	public function postStatus($type) {
		$check = $this->input->post('check');
		$today = date("Y-m-d H:i:s");
		$usernames = '';
        $userStatus = $this->user_functions->getUserIdsByStatus($type == self::LOCK ? self::LOCK : self::UNLOCK);

        if(is_array($check) && is_array($userStatus)){
            foreach($check as $id){
                if(in_array($id, $userStatus)){
                    $this->alertMessage(2, $type == self::LOCK ? lang('player.unlock') : lang('player.lock'));
                    $this->index();
                }
            }
        }
		if (!$check && $type != 1) {
			// if not check and not Approve
			$message = lang('con.usm16');
			$this->alertMessage(2, $message);
			$this->index();
		} elseif (!$check) {
			// if not check
			$message = lang('con.usm16');
			$this->alertMessage(2, $message);
			$this->viewSetPermission();
		} else {
			$checkRole = null;

			foreach ($check as $userId) {
				$checkRole = $this->user_functions->getUserRole($userId);
				if (!$checkRole) {
					break;
				} else {
					continue;
				}
			}

			if (!$checkRole) {
				// if not check role
				$message = lang('con.usm17');
				$this->alertMessage(2, $message);
				$this->viewSetPermission();
			} else {
				$data = null;
				$message = null;

				if ($type == 2) {
					// if type is locked
					$data = array('status' => 2);
					$message = lang('con.usm18');
					$status = "Lock";
				} else {
					$data = array('status' => 1);
					$message = lang('con.usm19');
					$status = "Normal";
				}

				foreach ($check as $userId) {
					$usernames = $usernames . $this->user_functions->searchUser($userId)['username'] . ', ';
					$this->utils->debug_log('userId', $userId, 'data', $data);
					$this->user_functions->changeStatus($userId, $data); //this will update the record
				}

				$this->saveAction('user_management', 'Lock/Unlock User', "Accounts " . $usernames . " have changed their status to " . $status . " by " . $this->authentication->getUsername());

				$this->alertMessage(1, $message);
				$this->index();
			}
		}
	}

	/**
	 * overview : Post user inputs to set role
	 *
	 * @param  int 	$id 	user id
	 * @return redirected page
	 */
	public function postSetRole($id) {
		$check = $this->user_functions->getUserRole($id);
		$user = $this->user_functions->searchUser($id);
		$today = date("Y-m-d H:i:s");
		$this->form_validation->set_rules('roleId', 'Role', 'trim|required|xss_clean');

		if ($this->form_validation->run() == false) {
			$message = lang('con.usm20');
			$this->alertMessage(2, $message);
			$this->viewSetRole($id);
		} else {
			$roleId = $this->input->post('roleId');

			if ($check['roleId'] == $roleId) {
				// if user saving the same role they have
				$message = lang('con.usm21');
				$this->alertMessage(2, $message);
				$this->viewSetRole($id);
			} elseif ($check) {
				// if checked
				$data = array(
					'roleId' => $roleId,
				);
				$this->user_functions->updateUserRole($id, $data);

				$this->saveAction('user_management', 'Set Role User', "Account " . $user['username'] . " has updated the role by " . $this->authentication->getUsername());

				$message = lang('con.usm22');
				$this->alertMessage(1, $message);
				$this->viewUsers();
			} else {
				$data = array(
					'roleId' => $roleId,
					'userId' => $id,
				);
				$this->user_functions->addUserRole($data);

				$this->saveAction('user_management', 'Set Role User', "Account " . $user['username'] . " has set to new role by " . $this->authentication->getUsername());

				$message = lang('con.usm23');
				$this->alertMessage(1, $message);
				$this->viewUsers();
			}
		}
	}

	/**
	 * overview: Post user inputs to Approve User
	 *
	 * @param  int 	$id 	user id
	 * @return redirected page
	 */
	public function postApprove($id) {
		$checkRole = $this->user_functions->getUserRole($id);

		if (!$checkRole) {
			// if not checked
			$message = lang('con.usm17');
			$this->alertMessage(2, $message);
			$this->viewSetPermission();
		} else {
			$data = array('status' => 1);
			$this->user_functions->changeStatus($id, $data); //this will update the record

			$message = lang('con.usm24');
			$this->alertMessage(1, $message);
			$this->index();
		}
	}

	/**
	 * overview: Post user inputs to set or save an email
	 *
	 * @param  int 	$id 	user id
	 * @return redirected page
	 */
	public function postSettingEmail($id) {
		$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');

		if ($this->form_validation->run() == false) {
			$message = lang('con.usm04');
			$this->alertMessage(2, $message);
			$this->viewUserSetting();
		} else {
			$email = $this->input->post('email');
			$check = $this->user_functions->checkEmailExist($email);
			if ($check) {
				// if they saving the same  email as their email
				$message = lang('con.usm25');
				$this->alertMessage(2, $message);
				$this->viewUserSetting();
			} else {
				$data = array('email' => $email);

				$this->user_functions->editUser($id, $data);

				$today = date("Y-m-d H:i:s");

				$this->saveAction('user_management', 'User Settings', "User " . $this->authentication->getUsername() . " had changed/updated Email to " . $email);

				$message = lang('con.usm26') . " <b>" . $email . "</b>";
				$this->alertMessage(1, $message);
				$this->viewUserSetting();
			}
		}
	}

	/**
	 * overview: Post user inputs to set or save password
	 *
	 * @POST $opassword
	 * @POST $npassword
	 * @param  int 	$id 	user id
	 * @return redirected page
	 */
	public function postSettingPassword($id) {
		$this->form_validation->set_rules('opassword', 'Old Password', 'trim|required|xss_clean|max_length[34]');
		$this->form_validation->set_rules('npassword', 'New Password', 'trim|required|xss_clean|max_length[34]');
		$this->form_validation->set_rules('ncpassword', 'New Confirm Password', 'trim|required|xss_clean|max_length[34]|matches[npassword]');

		if ($this->form_validation->run() == false) {
			$message = lang('con.usm04');
			$this->alertMessage(2, $message);
			$this->viewUserSetting();
		} else {
			$opassword = $this->input->post('opassword');
			$npassword = $this->input->post('npassword');

			$check = $this->user_functions->checkPassword($id, $opassword);
			if (!$check) {
				// if password is incorrect
				$message = lang('con.usm27');
				$this->alertMessage(2, $message);
				$this->viewUserSetting();
			} else {
				$data = array('password' => $npassword);
				$username = $this->authentication->getUsername();
				$this->user_functions->resetPassword($id, $username, $data);

				$today = date("Y-m-d H:i:s");

				$this->saveAction('user_management', 'User Settings', "User " . $this->authentication->getUsername() . " had changed/updated Password");

				$message = lang('con.usm28');
				$this->alertMessage(1, $message);
				$this->viewUserSetting();
			}
		}
	}

	/**
	 * overview: Post user inputs to set or save safety question
	 *
	 * @POST $check
	 * @POST $safetyQuestion
	 * @param  int 	$id 	user id
	 * @return redirected page
	 */
	public function postSettingSafetyQuestion($id) {
		$check = $this->input->post('checkCustomize');

		if (!$check) {
			// if not check the customize safety question
			$this->form_validation->set_rules('safetyQuestion', 'Safety Question', 'trim|required|xss_clean');
			$this->form_validation->set_rules('answer', 'Answer', 'trim|required|xss_clean');

			if ($this->form_validation->run() == false) {
				$message = lang('con.usm04');
				$this->alertMessage(2, $message);
				$this->viewUserSetting();
			} else {
				$safetyQuestion = $this->input->post('safetyQuestion');
				$answer = $this->input->post('answer');

				$data = array('safetyQuestion' => $safetyQuestion, 'answer' => $answer);

				$this->user_functions->editUser($id, $data);

				$today = date("Y-m-d H:i:s");

				$this->saveAction('user_management', 'User Settings', "User " . $this->authentication->getUsername() . " had changed/updated Secret Question & Secret Answer");

				$message = lang('con.usm29');
				$this->alertMessage(1, $message);
				$this->viewUserSetting();
			}
		} else {
			$this->form_validation->set_rules('csafetyQuestion', 'Safety Question', 'trim|required|xss_clean');
			$this->form_validation->set_rules('answer', 'Answer', 'trim|required|xss_clean');

			if ($this->form_validation->run() == false) {
				$message = lang('con.usm04');
				$this->alertMessage(2, $message);
				$this->viewUserSetting();
			} else {
				$csafetyQuestion = $this->input->post('csafetyQuestion');
				$answer = $this->input->post('answer');

				$data = array('safetyQuestion' => $csafetyQuestion, 'answer' => $answer);

				$this->user_functions->editUser($id, $data);

				$today = date("Y-m-d H:i:s");

				$this->saveAction('user_management', 'User Settings', "User " . $this->authentication->getUsername() . " had changed/updated Secret Question & Secret Answer");

				$message = lang('con.usm29');
				$this->alertMessage(1, $message);
				$this->viewUserSetting();
			}
		}
	}

	/**
	 * overview: Post user inputs to filter the details
	 *
	 * @POST $username
	 * @POST $realname
	 * @POST $department
	 * @POST $position
	 * @POST $role
	 * @POST $login_ip
	 * @POST $last_login_time
	 * @POST $last_logout_time
	 * @POST $create_time
	 * @POST $create_by
	 * @POST $status
	 * @return	redirected page
	 */
	public function postFilter() {
		$username = $this->input->post('username') != '' ? $this->input->post('username') : null;
		$realname = $this->input->post('realname') != '' ? $this->input->post('realname') : null;
		$department = $this->input->post('department') != '' ? $this->input->post('department') : null;
		$position = $this->input->post('position') != '' ? $this->input->post('position') : null;
		$role = $this->input->post('role') != '' ? $this->input->post('role') : null;
		//$online_status = $this->input->post('online_status') != '' ? $this->input->post('online_status') : null;
		$login_ip = $this->input->post('login_ip') != '' ? $this->input->post('login_ip') : null;
		$last_login_time = $this->input->post('last_login_time') != '' ? $this->input->post('last_login_time') : null;
		$last_logout_time = $this->input->post('last_logout_time') != '' ? $this->input->post('last_logout_time') : null;
		$create_time = $this->input->post('create_time') != '' ? $this->input->post('create_time') : null;
		$create_by = $this->input->post('create_by') != '' ? $this->input->post('create_by') : null;
		$status = $this->input->post('status') != '' ? $this->input->post('status') : null;

		$um_data = array(
			"um_username" => $username,
			"um_realname" => $realname,
			"um_department" => $department,
			"um_position" => $position,
			"um_role" => $role,
			"um_create_by" => $create_by,
			"um_login_ip" => $login_ip,
			"um_status" => $status,
		);
		$this->session->set_userdata($um_data);

		$filters = array(
			'username' => $username,
			'realname' => $realname,
			'department' => $department,
			'position' => $position,
			'roleId' => $role,
			'lastLoginIp' => $login_ip,
			'lastLoginTime' => $last_login_time,
			'lastLogoutTime' => $last_logout_time,
			'createTime' => $create_time,
			'createPerson' => $create_by,
			'status' => $status,
		);

		$this->loadTemplate('User Management', '', '', 'system');

		if ($this->session->userdata('u_last_logout_time') == '' && $this->session->userdata('u_create_time') == '' && $this->session->userdata('u_create_by') == '') {
			$last_logout_time = "unchecked";
			$create_time = "unchecked";
			$create_by = "unchecked";

			$data = array(
				'u_last_logout_time' => $last_logout_time,
				'u_create_time' => $create_time,
				'u_create_by' => $create_by,
			);
			$this->session->set_userdata($data);
		}

		$number_player_list = '';
		$sort_by = '';
		$in = '';

		if ($this->session->userdata('u_number_player_list')) {
			$number_player_list = $this->session->userdata('u_number_player_list');
		} else {
			$number_player_list = 5;
		}

		if ($this->session->userdata('u_sort_by')) {
			$sort_by = $this->session->userdata('u_sort_by');
		} else {
			$sort_by = 'username';
		}

		if ($this->session->userdata('u_in')) {
			$in = $this->session->userdata('u_in');
		} else {
			$in = 'asc';
		}

		$user_id = $this->authentication->getUserId();

		$hasRolesAccess = $this->permissions->checkPermissions('admin_manage_user_roles');

		$data['export_report_permission'] = $this->permissions->checkPermissions('export_view_users_report');

		$data['userroles'] = $this->user_functions->getAllUserRole();
		$data['roles'] = $this->rolesfunctions->getAllRolesByUser($user_id, null, null, $hasRolesAccess);

		$users = $this->user_functions->findByFilters($filters, null, null, null, $sort_by, $in, $hasRolesAccess);
		$data['count_all'] = empty($users) ? 0 : count($users);
		// $data['count_all'] = count($this->user_functions->findByFilters($filters, null, null, $sort_by, $in));
		$config['base_url'] = "javascript:get_user_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 5;
		$config['num_links'] = 2;
		$config['first_tag_open'] = $config['last_tag_open'] = $config['next_tag_open'] = $config['prev_tag_open'] = $config['num_tag_open'] = '<li>';
		$config['first_tag_close'] = $config['last_tag_close'] = $config['next_tag_close'] = $config['prev_tag_close'] = $config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$data['user_group'] = $this->users->getAllAdminUsers();

		$this->pagination->initialize($config);

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);

		$data['users'] = array();

		// -- Add created_by to fix PHP error in view users page | OGP-9254
		if(!empty($users)) {
			foreach($users as $key => $user) {
				$user['created_by'] = $this->user_functions->getUserById($user['createPerson'])['username'];
				array_push($data['users'], $user);
			}
		}


		$data['currentUser'] = $user_id;

		$data['const_locked'] = 0;
        $data['const_unlocked'] = 1;
		$data['admin_user_id'] = $user_id;

		$this->template->write_view('main_content', 'system_management/view_users', $data);
		$this->template->render();

	}

	/**
	 * overview: checks if what type of action the user clicked
	 *
	 * @POST $submit_type
	 * @return	redirected page
	 */
	public function typeOfAction() {
        $action = $this->input->post('submit_type');
		switch ($action) {
		// case 'Approve':
		// 	$this->postStatus(1);
		// 	break;

		case 'Lock':
			$this->postStatus(2);
			break;

		case 'Unlock':
			$this->postStatus(3);
			break;

		case 'Delete':
			$this->postDeleteUser();
			break;

		case 'Search':
			$this->postFilter();
			break;

		default:
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang("Wrong action"));
			$this->index();
			break;
		}
	}

	/**
	 * overview: sort Users
	 *
	 * @param   string 	$sortName
	 * @param   string 	$order
	 * @return	load template
	 */
	public function sortUsersBy($sortName, $order) {
		if (!$this->permissions->checkPermissions('admin_manage_user_roles')) {
			$this->error_access();
		} else {
			$user_id = $this->authentication->getUserId();
			$user = $this->user_functions->searchUser($user_id);

			$data['userroles'] = $this->user_functions->getAllUserRole();

			$data['count_all'] = count($this->user_functions->getAllUsers($user_id, null, null, null, null, $user['roleId']));
			$config['base_url'] = "javascript:get_user_pages(";
			$config['total_rows'] = $data['count_all'];
			$config['per_page'] = 5;
			$config['num_links'] = 2;
			$config['first_tag_open'] = $config['last_tag_open'] = $config['next_tag_open'] = $config['prev_tag_open'] = $config['num_tag_open'] = '<li>';
			$config['first_tag_close'] = $config['last_tag_close'] = $config['next_tag_close'] = $config['prev_tag_close'] = $config['num_tag_close'] = '</li>';
			$config['cur_tag_open'] = "<li><span><b>";
			$config['cur_tag_close'] = "</b></span></li>";

			$this->pagination->initialize($config);

			$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);

			$data['users'] = $this->user_functions->getAllUsers($user_id, null, null, $sortName, $order, $user['roleId']);

			$data['currentUser'] = $user_id;
			$data['filter'] = ($order == 'ASC') ? 'DESC' : 'ASC';

			$this->load->view('system_management/view_user_sort', $data);
		}
	}

	/**
	 * overview : view currency
	 *
	 * detail: lists all currency
	 * @return load template
	 *
	 */
	public function viewCurrency() {
		if (!$this->permissions->checkPermissions('currency_setting')) {
			$this->error_access();
		} else {
			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}
			// sets the history for breadcrumbs
			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active'));
			}

			$this->history->setHistory('header_system.system_word26', 'user_management/viewCurrency');

			$sort = "currencyId";

			$user_id = $this->authentication->getUserId();
			$data['count_all'] = count($this->user_functions->getAllCurrency($sort, null, null));
			$config['base_url'] = "javascript:get_currency_pages(";
			$config['total_rows'] = $data['count_all'];
			$config['per_page'] = 5;
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
			$data['currency'] = $this->user_functions->getAllCurrency($sort, null, null);
			$data['active_currency'] = $this->user_functions->getActiveCurrency();
			$data['const_currency_active'] = self::CURRENCY_ACTIVE;
			$data['is_disabled_action'] = false;
			if(!empty($data['active_currency']) && $this->utils->isEnabledMDB()){
				// unset($data['currency']);
				$data['currency']['config'] = $data['active_currency'];
				$data['currency']['config']['status'] = self::CURRENCY_ACTIVE;
				$data['currency']['config']['updatedOn'] = null;
				$data['is_disabled_action'] = true;
			}

			$this->loadTemplate('User Management', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/view_currency', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : get currency pages
	 *
	 * detail: lists all currency
	 * @param  string 	$segment
	 * @return load template
	 *
	 */
	public function get_currency_pages($segment = "") {
		if (!$this->permissions->checkPermissions('role')) {
			$this->error_access();
		} else {
			$sort = "currencyId";
			$user_id = $this->authentication->getUserId();
			$data['count_all'] = count($this->user_functions->getAllCurrency($sort, null, null));
			$config['base_url'] = "javascript:get_currency_pages(";
			$config['total_rows'] = $data['count_all'];
			$config['per_page'] = 5;
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
			$data['currency'] = $this->user_functions->getAllCurrency($sort, null, $segment);
			$data['active_currency'] = $this->user_functions->getActiveCurrency();

			$this->load->view('system_management/view_currency_pages', $data);
		}
	}

	/**
	 * overview : change currency status
	 *
	 * @param  int 		$currency_id 	currency id
	 * @param  string 	$status
	 * @return void
	 */
	public function changeCurrencyStatus($currency_id) {
		$config_active_currency = $this->utils->getActiveCurrencyDBFormatOnMDB();
		if(!empty($config_active_currency) && $this->utils->isEnabledMDB()){
			$this->alertMessage(self::MESSAGE_TYPE_WARNING, "Have existing currency set up on config. Not available!");
			redirect(BASEURL . 'user_management/viewCurrency');
		}

		$currency = $this->user_functions->getCurrencyById($currency_id);
		$status = $currency['status'] == self::CURRENCY_INACTIVE ? self::CURRENCY_ACTIVE : self::CURRENCY_INACTIVE;

		$data = array(
			'status' => $status,
			'updatedOn' => date('Y-m-d H:i:s'),
		);

		$this->user_functions->updateCurrency([
		    'status' => self::CURRENCY_INACTIVE
        ]);
		$this->user_functions->updateCurrency($data, $currency_id);

		$this->saveAction('user_management', 'Edit Currrency', "Currency " . $currency['currencyCode'] . " has been edited by " . $this->authentication->getUsername());

		$message = $currency['currencyCode'] . " - " . $currency['currencyName'] . ' ' . lang('con.usm30') . ($status == self::CURRENCY_INACTIVE ? lang('system.word86') : lang('system.word87'));
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		redirect(BASEURL . 'user_management/viewCurrency');
	}

	/**
	 * overview : sort currency
	 *
	 * detail: sort currency order
	 * @param  string 	$sort
	 * @return load template
	 */
	public function sortCurrency($sort) {
		if (!$this->permissions->checkPermissions('role')) {
			$this->error_access();
		} else {

			$user_id = $this->authentication->getUserId();
			$data['count_all'] = count($this->user_functions->getAllCurrency($sort, null, null));
			$config['base_url'] = "javascript:get_currency_pages(";
			$config['total_rows'] = $data['count_all'];
			$config['per_page'] = 5;
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
			$data['currency'] = $this->user_functions->getAllCurrency($sort, null, null);
			$data['active_currency'] = $this->user_functions->getActiveCurrency();

			$this->load->view('system_management/view_currency_pages', $data);
		}
	}

	/**
	 * overview : search currency
	 *
	 * detail: filter currency lists
	 * @param  string 	$search
	 * @return load template
	 */
	public function searchCurrency($search = '') {
		if (!$this->permissions->checkPermissions('role')) {
			$this->error_access();
		} else {
			$user_id = $this->authentication->getUserId();
			$data['count_all'] = count($this->user_functions->getSearchCurrency($search, null, null));
			$config['base_url'] = "javascript:get_currency_pages(";
			$config['total_rows'] = $data['count_all'];
			$config['per_page'] = 5;
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
			$data['currency'] = $this->user_functions->getSearchCurrency($search, null, null);
			$data['active_currency'] = $this->user_functions->getActiveCurrency();

			$this->load->view('system_management/view_currency_pages', $data);
		}
	}

	/**
	 * overview : get the currency details
	 *
	 * @param  int $currency_id currency id
	 * @return json
	 */
	public function getCurrencyDetails($currency_id) {
		echo json_encode($this->user_functions->getCurrencyDetails($currency_id));
	}

	/**
	 * overview : action currency
	 *
	 * detail: update currency details
	 * @POST $currencyId
	 * @POST $currencyCode
	 * @POST $currencyName
	 * @return void
	 */
	public function actionCurrency() {

		if (!empty($this->input->post('currencyId'))) {
			$currency = $this->user_functions->getCurrencyById($this->input->post('currencyId'));

			if ($currency['currencyName'] != $this->input->post('currencyName')) {
				$this->form_validation->set_rules('currencyName', 'Currency name', 'trim|required|xss_clean|is_unique[currency.currencyName]');
			} elseif ($currency['currencyShortName'] != $this->input->post('currencyShortName')) {
                $this->form_validation->set_rules('currencyShortName', 'Currency short name', 'trim|required|xss_clean');
            } elseif ($currency['currencyCode'] != $this->input->post('currencyCode')) {
                $this->form_validation->set_rules('currencyCode', 'Currency code', 'trim|required|xss_clean|max_length[3]|is_unique[currency.currencyCode]');
            } elseif ($currency['currencySymbol'] != $this->input->post('currencySymbol')) {
				$this->form_validation->set_rules('currencySymbol', 'Currency Symbol', 'trim|required|xss_clean|max_length[3]|is_unique[currency.currencySymbol]');
			} else {
                $this->form_validation->set_rules('currencyName', 'Currency name', 'trim|required|xss_clean');
                $this->form_validation->set_rules('currencyShortName', 'Currency short name', 'trim|required|xss_clean');
                $this->form_validation->set_rules('currencyCode', 'Currency code', 'trim|required|xss_clean|max_length[3]');
				$this->form_validation->set_rules('currencySymbol', 'Currency Symbol', 'trim|required|xss_clean|max_length[3]');
			}
		} else {
            $this->form_validation->set_rules('currencyName', 'Currency name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('currencyShortName', 'Currency short name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('currencyCode', 'Currency code', 'trim|required|xss_clean|max_length[3]');
			$this->form_validation->set_rules('currencySymbol', 'Currency Symbol', 'trim|required|xss_clean|max_length[3]');
		}

		if ($this->form_validation->run() == false) {
			$this->viewCurrency();
		} else {
			if (!empty($this->input->post('currencyId'))) {
				$data = array(
                    'currencyName' => ucwords($this->input->post('currencyName')),
                    'currencyShortName' => ucwords($this->input->post('currencyShortName')),
                    'currencyCode' => strtoupper($this->input->post('currencyCode')),
					'currencySymbol' => $this->input->post('currencySymbol'),
					'updatedOn' => $this->utils->getNowForMysql(),
				);
				$this->user_functions->updateCurrency($data, $this->input->post('currencyId'));

				$this->saveAction('user_management', 'Edit Currrency', "Currency " . strtoupper($this->input->post('currencyCode')) . " has been edited by " . $this->authentication->getUsername());

				$message = lang('con.usm31') . strtoupper($this->input->post('currencyCode')) . " - " . ucwords($this->input->post('currencyName')) . " " . lang('con.usm32');

				$this->alertMessage(1, $message);
				redirect(BASEURL . 'user_management/viewCurrency');
			} else {
				$data = array(
                    'currencyName' => ucwords($this->input->post('currencyName')),
                    'currencyShortName' => ucwords($this->input->post('currencyShortName')),
                    'currencySymbol' => $this->input->post('currencySymbol'),
                    'createdOn' => $this->utils->getNowForMysql(),
                    'updatedOn' => $this->utils->getNowForMysql(),
                    'status' => self::CURRENCY_INACTIVE, // default disable
                    'currencyCode' => strtoupper($this->input->post('currencyCode')),
				);

				$this->user_functions->addCurrency($data);

				$this->saveAction('user_management', 'Add Currrency', "Currency " . strtoupper($this->input->post('currencyCode')) . " has been added by " . $this->authentication->getUsername());

				$message = lang('con.usm31') . strtoupper($this->input->post('currencyCode')) . " - " . ucwords($this->input->post('currencyName')) . " " . lang('con.usm33');

				$this->alertMessage(1, $message);
				redirect(BASEURL . 'user_management/viewCurrency');
			}
		}
	}

	/**
	 * overview : delete the selected currency
	 *
	 * @POST currency
	 * @return void
	 */
	public function deleteSelectedCurrency() {
		$currency = $this->input->post('currency');
		$today = date("Y-m-d H:i:s");
		$currency_codes = '';

		if ($currency != '') {
			foreach ($currency as $currencyId) {
				$currency_codes = $currency_codes . $this->user_functions->getCurrencyById($currencyId)['currencyCode'] . ', ';
				$this->user_functions->deleteCurrency($currencyId);
			}

			$this->saveAction('user_management', 'Delete Currrency', "Currency " . $currency_codes . " has been deleted by " . $this->authentication->getUsername());

			$message = lang('con.usm34');
			$this->alertMessage(1, $message); //will set and send message to the user
			$this->viewCurrency();
		} else {
			$message = lang('con.usm35');
			$this->alertMessage(2, $message);
			$this->viewCurrency();
		}
	}

	/**
	 * overview : delete the selected currency
	 *
	 * @param  int 	$currency_id 	currency id
	 * @return void
	 */
	public function deleteCurrency($currency_id) {
		$currency_code = $this->user_functions->getCurrencyById($currency_id)['currencyCode'];
		$this->user_functions->deleteCurrency($currency_id);

		$this->saveAction('user_management', 'Delete Currrency', "Currency " . $currency_code . " has been deleted by " . $this->authentication->getUsername());

		$message = lang('con.usm36');
		$this->alertMessage(1, $message); //will set and send message to the user
		$this->viewCurrency();
	}

	/**
	 * overview : delete crumb
	 *
	 * @param  int $key
	 * @return void
	 */
	public function deleteCrumb($key) {
		echo $this->history->delete($key);
	}

	/**
	 * overview : view duplicate account
	 *
	 * detail: View of Duplicate Account Setting
	 * @return	rendered Template with array of data
	 */
	public function viewDuplicateAccount() {
		if (!$this->permissions->checkPermissions('duplicate_account_setting')) {
			$this->error_access();
		} else {
			$this->loadTemplate('User Management', '', '', 'system');

			$condition = $this->duplicate_account->getDupEnableColumnCondition();
			$data['items'] = $this->duplicate_account->getAllItems($condition);

			$this->template->write_view('main_content', 'system_management/view_duplicate_account_setting', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : Save modifieed Duplicate Account Setting
	 *
	 * @return void
	 */
	public function saveDuplicateAccountSetting() {
		if (!$this->permissions->checkPermissions('duplicate_account_setting')) {
			$this->error_access();
		} else {
			$duplicate_account_setting = $this->duplicate_account->getAllItems();

			foreach ($duplicate_account_setting as $key => $value) {
				$rate_exact = (empty($this->input->post($value['id'] . "_rate_exact"))) ? '0' : $this->input->post($value['id'] . "_rate_exact");
				$rate_similar = (empty($this->input->post($value['id'] . "_rate_similar"))) ? '0' : $this->input->post($value['id'] . "_rate_similar");

				$data = array(
					'rate_exact' => $rate_exact,
					'rate_similar' => $rate_similar,
					'status' => ($rate_exact == '0' && $rate_similar == '0') ? '0' : '1',
				);
				$this->duplicate_account->saveDuplicateAccountSetting($data, $value['id']);
			}

			$data = array(
				'username' => $this->authentication->getUsername(),
				'management' => 'System Management',
				'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
				'action' => 'Edit Duplicate Account Settings',
				'description' => "User " . $this->authentication->getUsername() . " edited Duplicate Account Setting",
				'logDate' => date("Y-m-d H:i:s"),
				'status' => '0',
			);
			$this->report_functions->recordAction($data);

			$message = lang('con.usm38');
			$this->alertMessage(1, $message);

			redirect(BASEURL . 'user_management/viewDuplicateAccount');
		}
	}

	/* API Settings */

	/**
	 * overview : view api settings
	 *
	 * detail: display page for all the vip api settings
	 * @param  int 	$type
	 * @return load template
	 *
	 */
	public function viewAPISettings($type = null) {
		$this->loadTemplate('User Management', '', '', 'system');

		$data['type'] = $type;
		$data['games'] = $this->player_manager->getAllGames();

		$this->template->write_view('main_content', 'system_management/api_settings/view_api_settings', $data);
		$this->template->render();
	}

	/**
	 * overview : change api setting
	 *
	 * detail: get the api setting to load to page content
	 * @param  int 	$type type
	 * @return load template
	 */
	public function changeAPISettings($type) {
		if ($type == 1) {
			$data['type'] = $type;
			$data['settings'] = $this->user_functions->getAPISettings($type);

			$this->load->view('system_management/api_settings/ajax_pt_settings', $data);
		} elseif ($type == 2) {
			$data['type'] = $type;
			$data['settings'] = $this->user_functions->getAPISettings($type);

			$this->load->view('system_management/api_settings/ajax_ag_settings', $data);
		}
	}

	/**
	 * overview : edit api setting
	 *
	 * detail: update API setting details
	 * @param  int 	$type type
	 * @return load template
	 */
	public function editAPISettings($type) {
		$this->loadTemplate('User Management', '', '', 'system');

		$data['games'] = $this->player_manager->getAllGames();
		$data['type'] = $type;
		$data['settings'] = $this->user_functions->getAPISettings($type);

		if ($type == 1) {
			$this->template->write_view('main_content', 'system_management/api_settings/edit_pt_settings', $data);
		} elseif ($type == 2) {
			$this->template->write_view('main_content', 'system_management/api_settings/edit_ag_settings', $data);
		}

		$this->template->render();
	}

	/**
	 * overview: save API setting details
	 *
	 * @param  int 	$type
	 * @return void
	 */
	public function saveAPISettings($type) {
		if ($type == 1) {
			$this->form_validation->set_rules('api_url', lang('sys.api01'), 'trim|xss_clean|required');
			$this->form_validation->set_rules('api_admin_name', lang('sys.api04'), 'trim|xss_clean|required');
			$this->form_validation->set_rules('api_kiosk_name', lang('sys.api05'), 'trim|xss_clean|required');

			$this->form_validation->set_rules('api_entity_key', lang('sys.api02'), 'trim|xss_clean|required');
			$this->form_validation->set_rules('api_cert_key_path', lang('sys.api03'), 'trim|xss_clean|callback_checkIfKeyEmpty');
			$this->form_validation->set_rules('api_cert_pem_path', lang('sys.api08'), 'trim|xss_clean|callback_checkIfPemEmpty');
			$this->form_validation->set_rules('api_external_tran_id_deposit', lang('sys.api06'), 'trim|xss_clean|required');
			$this->form_validation->set_rules('api_external_tran_id_withdrawal', lang('sys.api07'), 'trim|xss_clean|required');
		} else if ($type == 2) {
			$this->form_validation->set_rules('api_url', lang('sys.api10'), 'trim|xss_clean|required');
			$this->form_validation->set_rules('api_admin_name', lang('sys.api11'), 'trim|xss_clean|required');
			$this->form_validation->set_rules('api_kiosk_name', lang('sys.api12'), 'trim|xss_clean|required');

			$this->form_validation->set_rules('api_ftp_server', lang('sys.api13'), 'trim|xss_clean|required');
			$this->form_validation->set_rules('api_ftp_username', lang('sys.api14'), 'trim|xss_clean|required');
			$this->form_validation->set_rules('api_ftp_password', lang('sys.api15'), 'trim|xss_clean|required');
			$this->form_validation->set_rules('api_ftp_server_file', lang('sys.api16'), 'trim|xss_clean|required');
		}

		if ($this->form_validation->run() == false) {
			$this->editAPISettings($type);
		} else {
			$settings = $this->user_functions->getAPISettings($type);

			if ($type == 1) {
				$message = $this->savePTSettings($settings);
			} else if ($type == 2) {
				$message = $this->saveAGSettings($settings);
			}

			$this->alertMessage('1', $message);
			redirect(BASEURL . 'user_management/viewAPISettings/' . $type);
		}
	}

	/**
	 * overview : check if key empty
	 *
	 * @return bool
	 */
	public function checkIfKeyEmpty() {
		if ($_FILES['api_cert_key_path']['name'] == null) {
			$this->form_validation->set_message('checkIfKeyEmpty', 'The Certificate Key field is required');

			return false;
		}

		return true;
	}

	/**
	 * overview : check if pem empty
	 *
	 * @return bool
	 */
	public function checkIfPemEmpty() {
		if ($_FILES['api_cert_pem_path']['name'] == null) {
			$this->form_validation->set_message('checkIfPemEmpty', 'The Certificate Pem field is required');

			return false;
		}

		return true;
	}

	/**
	 * overview: save PT settings data
	 *
	 * @param  array 	$settings
	 * @return string
	 *
	 */
	public function savePTSettings($settings) {
		$api_cert_key_path = $this->upload('api_cert_key_path');
		$api_cert_pem_path = $this->upload('api_cert_pem_path');

		$data = array(
			'apiId' => 1,
			'apiURL' => $this->input->post('api_url'),
			'apiEntityKey' => $this->input->post('api_entity_key'),
			'apiCertKeyPath' => $api_cert_key_path['full_path'],
			'apiCertPemPath' => $api_cert_pem_path['full_path'],
			'apiAdminName' => $this->input->post('api_admin_name'),
			'apiKioskName' => $this->input->post('api_kiosk_name'),
			'apiExternalTranIdDeposit' => $this->input->post('api_external_tran_id_deposit'),
			'apiExternalTranIdWithdrawal' => $this->input->post('api_external_tran_id_withdrawal'),
		);

		if ($settings != null) {
			$data['updateOn'] = date("Y-m-d H:i:s");

			$this->user_functions->editAPISettings($data, $settings['apiSettingsId']);
			$message = "Successfully edited api settings";
		} else {
			$data['createdOn'] = date("Y-m-d H:i:s");

			$this->user_functions->saveAPISettings($data);
			$message = "Successfully saved api settings";
		}

		return $message;
	}

	/**
	 * overview: upload file to app
	 *
	 * @param  string 	$file_name
	 * @return void
	 */
	public function upload($file_name) {
		$path = realpath(APPPATH);

		$config['upload_path'] = $path;
		$config['allowed_types'] = '*';
		$config['max_size'] = $this->utils->getMaxUploadSizeByte();
		$config['remove_spaces'] = true;
		$config['overwrite'] = true;
		$config['max_width'] = '';
		$config['max_height'] = '';
		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		if (!$this->upload->do_upload($file_name)) {
			$error = array('error' => $this->upload->display_errors());
			return $error;
		} else {
			$file = $this->upload->data();

			return $file;
		}
	}

	/**
	 * overview: save AG Settings data
	 *
	 * @param  array 	$setting
	 * @return string
	 */
	public function saveAGSettings($settings) {
		$data = array(
			'apiId' => 2,
			'apiURL' => $this->input->post('api_url'),
			'apiAdminName' => $this->input->post('api_admin_name'),
			'apiKioskName' => $this->input->post('api_kiosk_name'),
			'apiFTPServer' => $this->input->post('api_ftp_server'),
			'apiFTPUsername' => $this->input->post('api_ftp_username'),
			'apiFTPPassword' => $this->input->post('api_ftp_password'),
			'apiFTPServerFile' => $this->input->post('api_ftp_server_file'),
		);

		if ($settings != null) {
			$data['updateOn'] = date("Y-m-d H:i:s");

			$this->user_functions->editAPISettings($data, $settings['apiSettingsId']);
			$message = "Successfully edited api settings";
		} else {
			$data['createdOn'] = date("Y-m-d H:i:s");

			$this->user_functions->saveAPISettings($data);
			$message = "Successfully saved api settings";
		}

		return $message;
	}

	/**
	 * overview: get all user names
	 *
	 * @return json
	 */
	public function getAllUsernames() {

		$this->load->model('users');
		$users = $this->users->getAllUsernames();
		//Convert fast std object to array;
		$data['users'] = json_decode(json_encode($users), true);
		$arr = array('status' => 'success', 'data' => $data);
		echo json_encode($arr);
	}

	/**
	 * overview : kickout user
	 *
	 * @param  int 	$userId 	user id
	 * @return void
	 */
	public function kickout($userId) {
		$this->load->model('users');
		$this->users->kickoutAdminuser($userId);
		$message = lang('con.plm69') . ' ' . lang('player.ol01');
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		redirect('/user_management/viewUser/' . $userId);
	}
	/* end of API Settings */

	/**
	 * Controller for Lottery Backoffice Role Binding
	 *
	 * @uses	MODEL: users
	 * @uses	LIB: lottery_bo_roles
	 *
	 * @return	none
	 */
	public function lottery_bo_role_binding() {
		if(!$this->permissions->checkPermissions('lottery_bo_role_binding'))
			return $this->error_access();

		$this->load->model(['users']);
		$this->load->library(['lottery_bo_roles']);
		$sbe_user_id = $this->authentication->getUserId();
		if (!$this->users->isAdminUser($sbe_user_id)) {
			$this->error_access();
			return;
		}

		$data = [];
		$roles = [];
		$api_roles = $this->lottery_bo_roles->roles_fetch();
		$sbe_superuser = [ 'superadmin', 'admin' ];
		$reserved_sbe_users = array_merge($sbe_superuser, [ 'master' ]);
		$sbe_username = $this->authentication->getUsername();
		$sbe_user_level = $this->lottery_bo_user_level($sbe_username, $sbe_user_id, $sbe_superuser);

		// Get bound users
		if(!empty($api_roles)) {
			foreach ($api_roles as $username => $role) {
				$priv_str = $role['pv'];
				$user_id = $this->users->getIdByUsername($username);
				// Skip illegal users
				if (empty($user_id)) continue;
				// Skip current SBE user
				if ($user_id == $sbe_user_id) continue;
				// OGP-6087: Skip reserved SBE users
				if (in_array($username, $reserved_sbe_users)) continue;

				$row_user_level = $this->lottery_bo_user_level($username, $user_id, $sbe_superuser);

				$roles[] = [
					'username'	=> $username ,
					'user_id'	=> $user_id ,
					'priv'		=> $this->lottery_bo_roles->priv_string_to_ar($priv_str) ,
					'level'		=> $row_user_level ,
					'selectable' => ($sbe_user_level > $row_user_level) && $user_id != $sbe_user_id
				];
			}
		}

		// Get available users for new bindings
		$avail_users = $this->users->getAllUserNamesNotDeleted();
		foreach ($avail_users as $key => $user) {
			// Skip users already linked
			if (isset($api_roles[$user['username']]) && intval($api_roles[$user['username']]) != 0) {
				unset($avail_users[$key]);
			}
			// OBP-6087: Skip reserved SBE users
			if (in_array($user['username'], $reserved_sbe_users)) {
				unset($avail_users[$key]);
			}
		}

		$data['roles_raw'] = $api_roles;
		$data['roles'] = $roles;
		$data['avail_users'] = $avail_users;
		$data['showraw'] = !empty($this->input->get('showraw'));


		$data['sbe_superuser'] = $sbe_superuser;
		$data['sbe_username'] = $sbe_username;
		$data['sbe_user_level'] = $sbe_user_level;

		$this->loadTemplate('System Management', '', '', 'system');
		$this->template->write_view('main_content', 'system_management/view_lottery_bo_role_binding', $data);
		$this->template->render();
	}

	protected function lottery_bo_user_level($username, $user_id, $sbe_superuser) {
		return in_array($username, $sbe_superuser) ? 2 :
			( $this->users->isAdminUser($user_id) ? 1 : 0 );
	}

	/**
	 * Ajax endpoint for role privilege update, Lottery Backoffice Role Binding
	 *
	 * @uses	POST: user_id		== adminusers.userId
	 * @uses	POST: update_priv	preset privilege type 1-4
	 *       		1 = admin, 2 = games, 3 = marketing, 4 = finances
	 *
	 * @return	JSON	[ "status", "code", "mesg", "string"]
	 */
	public function lottery_bo_binding_update() {
		$this->load->model(['users']);
		$this->load->library(['lottery_bo_roles']);
		$ret = [ 'status' => false , 'code' => 32, 'mesg' => 'exec_incomplete', 'result' => null ];
		try {
			$user_id = intval($this->input->post('user_id'));
			$priv = $this->input->post('update_priv');

			$current_operator_user_id = $this->authentication->getUserId();
			if (!$this->users->isAdminUser($current_operator_user_id)) {
				throw new Exception(lang('LBO Binding: No permission'), 1);
				return;
			}

			if (empty($user_id)) {
				throw new Exception(lang('LBO Binding: User ID missing'), 2);
			}

			if (empty($priv)) {
				throw new Exception(lang('LBO Binding: Privileges missing'), 3);
			}

			$username = $this->users->getUsernameById($user_id);
			if (empty($username)) {
				throw new Exception(lang('LBO Binding: Invalid user ID'), 4);
			}

			$passwd = $this->users->getPasswordPlain($user_id);
			if (empty($passwd)) {
				throw new Exception(lang('LBO Binding: Cannot access password, please login as user again'), 5);
			}

			$priv_str = $this->lottery_bo_roles->priv_ar_to_string($priv);

			$update_res = $this->lottery_bo_roles->update_or_add_account($username, $passwd, $priv_str);

			$ret = [ 'status' => true, 'code' => 0, 'mesg' => null, 'result' => [ $username, $priv_str ] ];
		}
		catch (Exception $ex) {
			$ret['code'] = $ex->getCode();
			$ret['mesg'] = $ex->getMessage();
		}
		finally {
			$this->returnJsonResult($ret);

		}
	}

	/**
	 * Ajax endpoint for role adding, Lottery Backoffice Role Binding
	 *
	 * @uses	POST: username		== adminusers.userId
	 * @uses	POST: priv			4-digit privilege string 'wxyz'.  See lottery_bo_binding_update().
	 *
	 * @return	JSON	[ "status", "code", "mesg", "string"]
	 */
	public function lottery_bo_binding_add() {
		$this->load->model(['users']);
		$this->load->library(['lottery_bo_roles']);
		$ret = [ 'status' => false , 'code' => 32, 'mesg' => 'exec_incomplete', 'result' => null ];
		try {
			$username = $this->input->post('username');
			$priv = $this->input->post('priv');

			$current_operator_user_id = $this->authentication->getUserId();
			if (!$this->users->isAdminUser($current_operator_user_id)) {
				throw new Exception(lang('LBO Binding: No permission'), 1);
				return;
			}

			if (empty($username)) {
				throw new Exception(lang('LBO Binding: User ID missing'), 2);
			}

			if (empty($priv)) {
				throw new Exception(lang('LBO Binding: Privileges missing'), 3);
			}

			$user_id = $this->users->getIdByUsername($username);
			if (empty($user_id)) {
				throw new Exception(lang('LBO Binding: Invalid user ID'), 4);
			}

			$passwd = $this->users->getPasswordPlain($user_id);
			if (empty($passwd)) {
				throw new Exception(lang('LBO Binding: Cannot access password, please login as user again'), 5);
			}

			$priv_str = $this->lottery_bo_roles->priv_ar_to_string($priv);

			$add_res = $this->lottery_bo_roles->update_or_add_account($username, $passwd, $priv_str, 'add');

			$this->utils->debug_log('add_res', $add_res);
			if ($add_res['code'] != 0) {
				throw new Exception($add_res['mesg'], $add_res['code']);
			}

			$ret = [ 'status' => true, 'code' => 0, 'mesg' => null, 'result' => [ $username, $priv_str ] ];
		}
		catch (Exception $ex) {
			$ret['code'] = $ex->getCode();
			$ret['mesg'] = $ex->getMessage();
		}
		finally {
			$this->returnJsonResult($ret);
		}
	}

	/**
	 * Ajax endpoint for role deleting, Lottery Backoffice Role Binding
	 *
	 * @uses	POST: username		== adminusers.userId
	 * @uses	POST: priv			4-digit privilege string 'wxyz'.  See lottery_bo_binding_update().
	 *
	 * @return	JSON	[ "status", "code", "mesg", "string"]
	 */
	public function lottery_bo_binding_delete() {
		$this->load->model(['users']);
		$this->load->library(['lottery_bo_roles']);
		$ret = [ 'status' => false , 'code' => 32, 'mesg' => 'exec_incomplete', 'result' => null ];
		try {
			$current_operator_user_id = $this->authentication->getUserId();

			if (!$this->users->isAdminUser($current_operator_user_id)) {
				throw new Exception(lang('LBO Binding: No permission'), 1);
				return;
			}
			$usernames = $this->input->post('usernames');

			if (!is_array($usernames) || count($usernames) == 0) {
				throw new Exception(lang('LBO Binding: usernames missing'), 6);
			}

			$uname_valid = [];
			$uname_invalid = [];
			foreach ($usernames as $username) {
				$user_id = $this->users->getIdByUsername($username);

				if (empty($user_id)) {
					$uname_invalid[] = $username;
					continue;
				}

				$uname_valid[] = $username;
			}

			if (count($uname_valid) == 0) {
				throw new Exception(lang('LBO Binding: All usernames are invalid'), 7);
			}
			$update_res = $this->lottery_bo_roles->delete_account($uname_valid);

			$ret['mesg'] = null;
			$ret['result'] = $update_res['mesg'];
			if ($update_res['code'] == 0) {
				$ret['code'] = 0;
				$ret['status'] = true;
			}
			else {
				$ret['code'] = 16;
				$ret['status'] = false;
			}
		}
		catch (Exception $ex) {
			$ret['code'] = $ex->getCode();
			$ret['mesg'] = $ex->getMessage();
		}
		finally {
			$this->returnJsonResult($ret);
		}
	}

	public function download_user_logs($log_id) {
		$result=['success'=>false, 'message'=>lang('Not found')];
		if(!empty($log_id)){

			$this->load->model(['users']);

			$rlt=$this->users->getDataInfoTableField($log_id);

			if(!empty($rlt)){
				$result['success']=true;
				$result['message']=null;
				$result['content']=$rlt;
			}
		}
		$this->returnJsonResult($result);
	}

	public function sync_user_to_mdb($userId){
		if (!$this->permissions->checkPermissions('admin_manage_user_roles') || empty($userId)) {
			return $this->error_access();
		}

		$rlt=null;
		$this->load->model(['users']);
		$username=$this->users->getUsernameById($userId);
		$success=$this->syncUserCurrentToMDBWithLock($userId, $username, false, $rlt);

		if(!$success){
			$errKeys=[];
			foreach ($rlt as $dbKey => $dbRlt) {
				if(!$dbRlt['success']){
					$errKeys[]=$dbKey;
				}
			}
			$errorMessage=implode(',', $errKeys);
		    $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sync User Failed').': '.$errorMessage);
		}else{
		    $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Sync User Successfully'));
		}

		redirect('/user_management/viewUser/'.$userId);

	}

	public function login_as_user($userId){
		//permission and t1 admin
		if ((!$this->permissions->checkPermissions('admin_manage_user_roles') && !$this->users->isT1Admin($this->authentication->getUsername()))
				|| empty($userId)) {
			return $this->error_access();
		}

		//record history
		$username=$this->users->getUsernameById($userId);

		if (!empty($username)) {

			$this->authentication->logout();

			$this->session->reinit();

			$rlt=$this->authentication->set_login_user($username);

			redirect('/');
		} else {
			$this->error_access();
		}
	}

	/**
	 *
	 * overview : View of OTP setting page
	 *
	 * @return rendered Template with array of data
	 */
	public function otp_settings() {
		//check permission
		$loggedUserId=$this->authentication->getUserId();
		if(empty($loggedUserId)){
			redirect('/auth/login');
			return;
		}
		if(!$this->utils->isEnabledFeature('enable_otp_on_adminusers')){
			return $this->error_access();
		}
		$this->load->model(['users']);
		$user=$this->users->getUserById($loggedUserId);
		$data=['user'=>$user];

		$this->loadTemplate('User Management', '', '', 'system');
		$this->template->write_view('main_content', 'system_management/otp_settings', $data);
		$this->template->render();
	}

	/**
	 * disable_otp
	 * @return json
	 */
	public function disable_otp() {
		//check permission
		$loggedUserId=$this->authentication->getUserId();
		if(empty($loggedUserId)){
			$result=['success'=>false, 'message'=>lang('No permission')];
			return $this->returnJsonResult($result);
		}
		$this->load->model(['users']);
		$code=$this->input->post('code');
		$user=$this->users->getUserById($loggedUserId);
		$secret=$user['otp_secret'];
		$rlt=$this->users->validateCodeAndDisableOTPById($loggedUserId, $secret, $code);
		if($rlt['success']){
			$username = $this->users->getUsernameById($loggedUserId);
			$this->syncUserCurrentToMDBWithLock($loggedUserId, $username, false);
		}
		return $this->returnJsonResult($rlt);
	}

	/**
	 * init otp secret
	 * @return json
	 */
	public function init_otp_secret() {
		//check permission
		$loggedUserId=$this->authentication->getUserId();
		if(empty($loggedUserId)){
			$result=['success'=>false, 'message'=>lang('No permission')];
			return $this->returnJsonResult($result);
		}
		$this->load->model(['users']);
		$rlt=$this->users->initOTPById($loggedUserId);
		$result=['success'=>true, 'result'=>$rlt];
		return $this->returnJsonResult($result);
	}
	/**
	 * validate_and_enable_otp
	 * @return json
	 */
	public function validate_and_enable_otp() {
		//check permission
		$loggedUserId=$this->authentication->getUserId();
		if(empty($loggedUserId)){
			$result=['success'=>false, 'message'=>lang('No permission')];
			return $this->returnJsonResult($result);
		}
		$secret=$this->input->post('secret');
		$code=$this->input->post('code');
		$this->load->model(['users']);
		$rlt=$this->users->validateCodeAndEnableOTPById($loggedUserId, $secret, $code);

		if($rlt['success']){
			$username = $this->users->getUsernameById($loggedUserId);
			$this->syncUserCurrentToMDBWithLock($loggedUserId, $username, false);
		}

		return $this->returnJsonResult($rlt);
	}

	public function reset_2fa($userId){
		$loggedUserId=$this->authentication->getUserId();
		//check permission and can't reset self
		if (!$this->permissions->checkPermissions('reset_otp_secret_for_adminusers')
				|| empty($userId) || !$this->permissions->checkPermissions('view_admin_users')
				|| $loggedUserId==$userId) {
			$result=['success'=>false, 'message'=>lang('No permission')];
			return $this->returnJsonResult($result);
		}

		$this->load->model(['users']);
		$success=$this->users->disableOTPById($userId);
		$message=lang('Reset 2FA Successfully');
		if(!$success){
			$message=lang('Reset 2FA failed');
		}else{
			$username = $this->users->getUsernameById($userId);
			$this->syncUserCurrentToMDBWithLock($userId, $username, false);
		}
		return $this->returnJsonResult(['success'=>$success, 'message'=>$message]);
	}
}

/* End of file user_management.php */
/* Location: ./application/controllers/user_management.php */
