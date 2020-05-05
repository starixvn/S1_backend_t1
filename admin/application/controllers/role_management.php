<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

/**
 * Role Management
 *
 * Role Management Controller for FHI OG System
 *
 * Defines general behavior of game API classes.
 *
 * General behaviors include:
 * * Search Roles
 * * * Checks roles if exist
 * * Add/update/delete Roles
 * * Displays All created Roles
 * * Lock and Unlocks created roles
 *
 * @see Redirect redirect to viewTermsSetup page
 *
 * @category role_management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Role_management extends BaseController {
	function __construct() {
		parent::__construct();

		$this->load->helper('url');
		$this->load->library(array('permissions', 'form_validation', 'template', 'pagination', 'report_functions'));
		// $this->load->model(array(''));

		$this->permissions->checkSettings();
		$this->permissions->setPermissions();
	}

	const MANAGEMENT_NAME = 'role_management';

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
	// 	);

	// 	$this->report_functions->recordAction($data);
	// }

	/**
	 * overview : set message for users
	 *
	 * @param	int 	$type
	 * @param   string 	$message
	 * @return  array 	set session user data
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
	 * @param string 	$title
	 * @param string 	$description
	 * @param string 	$keywords
	 * @param string 	$activenav
	 */
	private function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->add_js('resources/js/system_management/role_management.js');

		// $this->template->add_js('resources/js/jquery-1.11.1.min.js');
		//$this->template->add_js('resources/js/jquery.dataTables.min.js');
		//$this->template->add_js('resources/js/dataTables.responsive.min.js');
		$this->template->add_js('resources/js/datatables.min.js');

		$this->template->add_css('resources/css/general/style.css');
		// $this->template->add_css('resources/css/jquery.dataTables.css');
		// $this->template->add_css('resources/css/dataTables.responsive.css');
		$this->template->add_css('resources/css/datatables.min.css');

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
	 * @return  rendered template
	 */
	private function error_access() {
		$this->loadTemplate('Role Management', '', '', 'system');
		$systemUrl = $this->utils->activeSystemSidebar();
		$data['redirect'] = $systemUrl;

		$message = lang('con.rom01');
		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		$this->template->write_view('main_content', 'error_page', $data);
		$this->template->render();
	}

	/**
	 * overview
	 *
	 * detail: Index Page of Role Management
	 * @return	void
	 */
	public function index() {
		redirect('role_management/viewRoles');
	}

	/**
	 * overview : add role
	 *
	 * @return  rendered template
	 */
	public function addRole() {

		if($this->utils->isEnabledFeature('use_role_permission_management_v2')){
			return $this->addRoleV2();
		}

		if (!$this->permissions->checkPermissions('admin_manage_user_roles') && !$this->permissions->checkPermissions('role')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Role Management', '', '', 'system');
			$role_id = $this->permissions->getRoleId();

			$data['functions'] = $this->rolesfunctions->getAllFunctionsGiving($role_id);
			$this->template->write_view('main_content', 'system_management/add_role', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : add role
	 *
	 * @return  rendered template
	 */
	public function addRoleV2() {
		if (!$this->permissions->checkPermissions('admin_manage_user_roles') && !$this->permissions->checkPermissions('role')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Role Management', '', '', 'system');
			$role_id = $this->permissions->getRoleId();

			$data['functions'] = $this->rolesfunctions->getAllFunctionsGiving($role_id);
			$data['parent_functions'] = array();

			// -- Get parent functions
			foreach ($data['functions'] as $key => $function) {
				if($function['parentId'] == "0" && $function['funcId'] != Roles::ROLE_EMAIL_SETTING)
					array_push($data['parent_functions'], $function);
			}

			// -- force parent ID of functions under vip sub parents to main vip management funcID
			$data['vip_sub_parents'] = array(Roles::ROLE_VIP_SETTINGS, Roles::ROLE_VIP_REBATE_RULES_TEMPLATE, Roles::ROLE_VIP_REQUEST_LIST, Roles::ROLE_VIP_REBATE_VALUES);

			foreach ($data['functions'] as $key => &$function) {
				if(in_array($function['parentId'], $data['vip_sub_parents']))
					$function['parentId'] = Roles::ROLE_VIP_MANAGEMENT_MAIN_PARENT;
			}

			// -- OGP-10277: force parent ID of some permission to main VIP Management
			$force_to_vip_management = array(Roles::ROLE_VIP_GROUP_SETTING);
			$this->forceChangingOfParentPermission($data['functions'], Roles::ROLE_VIP_MANAGEMENT_MAIN_PARENT, $force_to_vip_management);

			// -- OGP-10277: force parent ID of some permission to main Marketing Management
			$force_to_marketing_management = array(Roles::ROLE_MANUALLY_PAY_CASHBACK);
			$this->forceChangingOfParentPermission($data['functions'], Roles::ROLE_MARKETING_MANAGEMENT_MAIN_PARENT, $force_to_marketing_management);

			$data['functions'] = $this->rearrangeFunctionsByParentFunction($data['parent_functions'], $data['functions']);
			
			$this->template->write_view('main_content', 'system_management/add_role_V2', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : verify add role
	 *
	 * @return void
	 */
	public function verifyAddRole() {
		$this->form_validation->set_rules('role_name', 'Role Name', 'trim|required|xss_clean|callback_checkIfRoleExists');
		$this->form_validation->set_rules('functions', 'Functions', 'callback_checkifFunctionsHasCheck');

		if ($this->form_validation->run() == FALSE) {
			$this->addRole(); //redirect to addRole
		} else {

			$role_name = $this->input->post('role_name');
			$controller=$this;
			$error=null;
			$newRoleId=null;
			$success=$this->utils->globalLockRoleRegistration($role_name, function()
					use($controller, &$error, &$newRoleId, $role_name){

	            $funcParentIds = $this->input->post('functions_parent');
	            $funcParentGiveIds = $this->input->post('functions_parent_giving');

	            $funcIds = $this->input->post('functions'); //will get all functions checked
				// $today = date("Y-m-d H:i:s"); //will get the date based on the httpd.conf's timezone.
				$funcGivingIds = $this->input->post('functions_giving'); //will get all functions checked
				$createPerson = $this->authentication->getUserName() . " (" . $this->permissions->getRoleName() . ")";
				$user_id = $this->authentication->getUserId();
				$loggedRoleId=$this->users->getRoleIdByUserId($user_id);

				$isAdmin=false;
				if ($this->permissions->canAssignSuperAdmin()) {
					$isAdmin=$this->input->post('isAdmin');
				}

				$this->load->model(['roles', 'multiple_db_model']);

				$success=$this->dbtransOnly(function() use($controller, $loggedRoleId, $createPerson, $role_name, $isAdmin, $funcIds,
						$funcGivingIds, &$error, $funcParentIds, $funcParentGiveIds, &$newRoleId){

					return $controller->roles->addRoleWithFunction($role_name, $isAdmin, $loggedRoleId, $createPerson, $funcIds, $funcGivingIds, $error,
						$funcParentIds, $funcParentGiveIds, $newRoleId);

				});

				if($success){
					$this->syncRoleCurrentToMDB($newRoleId, false);
				}

				return $success;
			});

			// $data = array(
			// 	'roleName' => $role_name,
			// 	'createTime' => $today,
			// 	'createPerson' => $this->authentication->getUserName() . " (" . $this->permissions->getRoleName() . ")",
			// 	'status' => '0',
			// );
			// $this->load->model(['users']);

			// $user_id = $this->authentication->getUserId();

			// # Only modify the isAdmin field when permitted user is logged in
			// if ($this->permissions->canAssignSuperAdmin()) {
			// 	$data['isAdmin'] = $this->input->post('isAdmin');
			// }

			// $roles = $this->rolesfunctions->addRoles($data); //will add the new roles to record
			// $parent_ids = $this->rolesfunctions->getFunctionsParentId($functions);

			// $functions = $functions . ',' . $parent_ids;
			// $loggedRoleId=$this->users->getRoleIdByUserId($user_id);
			// $this->rolesfunctions->addGenealogy($roles['roleId'], $loggedRoleId);
			// $this->rolesfunctions->addRoleFunctions($functions, $roles['roleId']);

			// if ($this->checkIfHasPermissionRole($functions)) {
			// 	if ($this->input->post('functions_giving')) {
			// 		$functions_giving = implode(",", $this->input->post('functions_giving')); //will get all functions checked
			// 		$parent_ids_giving = $this->rolesfunctions->getFunctionsParentId($functions_giving);
			// 		$functions_giving = $functions_giving . ',' . $parent_ids_giving;
			// 		$this->rolesfunctions->addRoleFunctionsGiving($functions_giving, $roles['roleId']);
			// 	}

			// 	$today = date("Y-m-d H:i:s");

			// 	$this->saveAction(self::MANAGEMENT_NAME, 'Add Role', "User " . $this->authentication->getUsername() . " added a new role");
			// }

			if($success){
				$message = lang('con.rom02') . $role_name;
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message); //will set and send message to the user
			}else{
				$message = !empty($error) ? $error : lang('error.default.message');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message); //will set and send message to the user
			}

			redirect('/role_management/viewRoles'); //redirect to viewRoles
		}
	}

	/**
	 * overview : check if role address exists
	 *
	 * @param  string 	$role
	 * @return bool
	 */
	public function checkIfRoleExists($role) {
		$this->load->model(['roles']);
		$roleId=$this->input->post('roleId');
		$result = $this->roles->checkIfRoleExists($role, $roleId);

		if ($result) {
			$this->form_validation->set_message('checkIfRoleExists', 'The %s already exists');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	 * overview : check if atleast one functions has been selected
	 *
	 * @return bool
	 */
	public function checkifFunctionsHasCheck() {
		$functions = $this->input->post('functions');

		if (empty($functions)) {
			$this->form_validation->set_message('checkifFunctionsHasCheck', 'Please select at least one %s.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	 * overview : check if role management usage is given to the role
	 *
	 * @param  array 	$functions
	 * @return bool
	 */
	public function checkIfHasPermissionRole($functions) {
		$functions = explode(',', $functions);

		foreach ($functions as $value) {
			//role management id
			if ($value == 2) {
				return true;
			}
		}

		return false;
	}

	/**
	 * overview : check role
	 *
	 * @param int 	$role_id
	 * @return  rendered template
	 */
	public function checkRole($role_id) {

		if($this->utils->isEnabledFeature('use_role_permission_management_v2')){
			return $this->checkRoleV2($role_id);
		}

		if (!$this->permissions->checkPermissions('admin_manage_user_roles') && !$this->permissions->checkPermissions('role')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Role Management', '', '', 'system');
			$role_id_give = $this->permissions->getRoleId();

			$data['functions'] = $this->rolesfunctions->getAllFunctionsGiving($role_id_give);

			$data['roles'] = $this->rolesfunctions->getRolesById($role_id);

			$data['rolesfunctions'] = $this->rolesfunctions->getRolesFunctionsIdById($role_id);
			$data['rolesfunctions_giving'] = $this->rolesfunctions->getRolesFunctionsGivingIdById($role_id);

			$this->template->write_view('main_content', 'system_management/check_role', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : check role
	 *
	 * @param int 	$role_id
	 * @return  rendered template
	 */
	public function checkRoleV2($role_id) {
		if (!$this->permissions->checkPermissions('admin_manage_user_roles') && !$this->permissions->checkPermissions('role')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Role Management', '', '', 'system');
			$role_id_give = $this->permissions->getRoleId();

			$data['functions'] = $this->rolesfunctions->getAllFunctionsGiving($role_id_give);

			$data['parent_functions'] = array();
			
			// -- Get parent functions
			foreach ($data['functions'] as $key => $function) {
				if($function['parentId'] == "0"  && $function['funcId'] != Roles::ROLE_EMAIL_SETTING)
					array_push($data['parent_functions'], $function);
			}

			$data['roles'] = $this->rolesfunctions->getRolesById($role_id);

			$data['rolesfunctions'] = $this->rolesfunctions->getRolesFunctionsIdById($role_id);
			$data['rolesfunctions_giving'] = $this->rolesfunctions->getRolesFunctionsGivingIdById($role_id);

			// -- force parent ID of functions under vip sub parents to main vip management funcID
			$data['vip_sub_parents'] = array(Roles::ROLE_VIP_SETTINGS, Roles::ROLE_VIP_REBATE_RULES_TEMPLATE, Roles::ROLE_VIP_REQUEST_LIST, Roles::ROLE_VIP_REBATE_VALUES);

			foreach ($data['functions'] as $key => &$function) {
				if(in_array($function['parentId'], $data['vip_sub_parents']))
					$function['parentId'] = Roles::ROLE_VIP_MANAGEMENT_MAIN_PARENT;
			}

			// -- OGP-10277: force parent ID of some permission to main VIP Management
			$force_to_vip_management = array(Roles::ROLE_VIP_GROUP_SETTING);
			$this->forceChangingOfParentPermission($data['functions'], Roles::ROLE_VIP_MANAGEMENT_MAIN_PARENT, $force_to_vip_management);

			// -- OGP-10277: force parent ID of some permission to main Marketing Management
			$force_to_marketing_management = array(Roles::ROLE_MANUALLY_PAY_CASHBACK);
			$this->forceChangingOfParentPermission($data['functions'], Roles::ROLE_MARKETING_MANAGEMENT_MAIN_PARENT, $force_to_marketing_management);

			$data['functions'] = $this->rearrangeFunctionsByParentFunction($data['parent_functions'], $data['functions']);

			$this->template->write_view('main_content', 'system_management/check_role_V2', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : edit role
	 *
	 * @param int 	$role_id
	 * @return  rendered template
	 */
	public function editRole($role_id) {
		if($this->utils->isEnabledFeature('use_role_permission_management_v2')){
			return $this->editRoleV2($role_id);
		}

		if (!$this->permissions->checkPermissions('admin_manage_user_roles') && !$this->permissions->checkPermissions('role')) {
			$this->error_access();
		} else {
			$this->load->model('roles');
			$role = $this->roles->getRolesById($role_id);

			$this->loadTemplate('Role Management', '', '', 'system');
			$role_id_give = $this->permissions->getRoleId();

			$data['functions'] = $this->rolesfunctions->getAllFunctionsGiving($role_id_give);
			// $this->utils->debug_log($data['functions']);
			$data['isAdmin'] = $role['isAdmin'];
			$data['roles'] = $this->rolesfunctions->getRolesById($role_id);

			$data['isAdminRole'] = $this->roles->isAdminRole($this->permissions->getRoleId());
			$data['rolesfunctions'] = $this->rolesfunctions->getRolesFunctionsIdById($role_id);
			$data['rolesfunctions_giving'] = $this->rolesfunctions->getRolesFunctionsGivingIdById($role_id);
			$data['const_status_disabled'] = BaseModel::STATUS_DISABLED;
			$this->template->write_view('main_content', 'system_management/edit_role', $data);
			$this->template->render();
		}
	}



	/**
	 * overview : edit role
	 *
	 * @param int 	$role_id
	 * @return  rendered template
	 */
	public function editRoleV2($role_id) {
		if (!$this->permissions->checkPermissions('admin_manage_user_roles') && !$this->permissions->checkPermissions('role')) {
			$this->error_access();
		} else {
			$this->load->model('roles');
			$role = $this->roles->getRolesById($role_id);

			$this->loadTemplate('Role Management', '', '', 'system');
			$role_id_give = $this->permissions->getRoleId();

			$data['functions'] = $this->rolesfunctions->getAllFunctionsGiving($role_id_give);
			$data['parent_functions'] = array();
			
			// -- Get parent functions
			foreach ($data['functions'] as $key => $function) {
				if($function['parentId'] == "0"  && $function['funcId'] != Roles::ROLE_EMAIL_SETTING)
					array_push($data['parent_functions'], $function);
			}

			$data['isAdmin'] = $role['isAdmin'];
			$data['roles'] = $this->rolesfunctions->getRolesById($role_id);

			$data['isAdminRole'] = $this->roles->isAdminRole($this->permissions->getRoleId());
			$data['rolesfunctions'] = $this->rolesfunctions->getRolesFunctionsIdById($role_id);
			$data['rolesfunctions_giving'] = $this->rolesfunctions->getRolesFunctionsGivingIdById($role_id);
			$data['const_status_disabled'] = BaseModel::STATUS_DISABLED;

			// -- force parent ID of functions under vip sub parents to main vip management funcID
			$data['vip_sub_parents'] = array(Roles::ROLE_VIP_SETTINGS, Roles::ROLE_VIP_REBATE_RULES_TEMPLATE, Roles::ROLE_VIP_REQUEST_LIST, Roles::ROLE_VIP_REBATE_VALUES);

			foreach ($data['functions'] as $key => &$function) {
				if(in_array($function['parentId'], $data['vip_sub_parents']))
					$function['parentId'] = Roles::ROLE_VIP_MANAGEMENT_MAIN_PARENT;
			}

			// -- OGP-10277: force parent ID of some permission to main VIP Management
			$force_to_vip_management = array(Roles::ROLE_VIP_GROUP_SETTING);
			$this->forceChangingOfParentPermission($data['functions'], Roles::ROLE_VIP_MANAGEMENT_MAIN_PARENT, $force_to_vip_management);

			// -- OGP-10277: force parent ID of some permission to main Marketing Management
			$force_to_marketing_management = array(Roles::ROLE_MANUALLY_PAY_CASHBACK);
			$this->forceChangingOfParentPermission($data['functions'], Roles::ROLE_MARKETING_MANAGEMENT_MAIN_PARENT, $force_to_marketing_management);

			$data['functions'] = $this->rearrangeFunctionsByParentFunction($data['parent_functions'], $data['functions']);
			 

			$this->template->write_view('main_content', 'system_management/edit_role_V2', $data);
			$this->template->render();
		}
	}

	/**
	 * Force changing of parent permissions for specified permissions
	 * 
	 * @param  array &$all_functions
	 * @param  int $new_parent_id       
	 * @param  array $subject_permissions 
	 * @return void                      
	 */
	private function forceChangingOfParentPermission(&$all_functions, $new_parent_id, $subject_permissions){
		foreach ($all_functions as $key => &$function) {
			if(in_array($function['funcId'], $subject_permissions))
				$function['parentId'] = $new_parent_id;
		}
	}

	/**
	 * This method rearranges the functions based in their parent.
	 * This is used for role management v2
	 * 
	 * @param  array $parent_functions
	 * @param  array $all_functions    
	 * @return array                   
	 */
	private function rearrangeFunctionsByParentFunction($parent_functions, $all_functions){
		$functions_tmp = array();
		$used_functions_tmp = array();

		// -- arrange functions based on their parent functions
		foreach ($parent_functions as $parent_key => $parent_function) {

			if(!in_array($parent_function['funcId'], $used_functions_tmp)){
				array_push($functions_tmp, $parent_function);
				array_push($used_functions_tmp, $parent_function['funcId']);
			}

			foreach ($all_functions as $function_key => $function) {

				if(!in_array($function['funcId'], $used_functions_tmp) && $function['parentId'] == $parent_function['funcId']){
					array_push($functions_tmp, $function);
					array_push($used_functions_tmp, $function['funcId']);
				}

			}
		}

		return $functions_tmp;
	}

	/**
	 * overview : post edit role
	 *
	 * @param  int 		$role_id
	 * @return array 	saves edited role
	 */
	public function verifyEditRole($role_id) {
		if (!$this->permissions->checkPermissions('admin_manage_user_roles') && !$this->permissions->checkPermissions('role')) {
			return $this->error_access();
		}
		$this->load->model(['roles','multiple_db_model']);
		if($this->utils->isEnabledFeature('only_admin_modified_role'))	{
			if (!$this->roles->isAdminRole($this->permissions->getRoleId())) {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Only admin/superadmin only can modified the role.'));
				redirect('role_management/viewRoles');
			}
		}

		$this->form_validation->set_rules('role_name', 'Role Name', 'trim|required|xss_clean|callback_checkIfRoleExists');
		$this->form_validation->set_rules('functions', 'Functions', 'callback_checkifFunctionsHasCheck');
		$this->form_validation->set_rules('functions_giving', 'Functions to Give', 'callback_checkifFunctionsGivingHasCheck');

        $role_id_give = $this->permissions->getRoleId();
        $visibleFunc = $this->rolesfunctions->getAllFunctionsGiving($role_id_give);
        $visibleFuncIds = array_column($visibleFunc, 'funcId');

        /*$visibleFuncGiv = $this->rolesfunctions->getAllFunctionsGiving($role_id_give);
        $visibleFuncGivIds = array_column($visibleFuncGiv, 'funcId');*/

		if ($this->form_validation->run() == FALSE) {
			$this->editRole($role_id); //redirect to checkRole
		} else {

			$role_name = $this->input->post('role_name');
            $funcParentIds = $this->input->post('functions_parent');
            $funcParentGiveIds = $this->input->post('functions_parent_giving');

			$funcIds = $this->input->post('functions'); //will get all functions checked
			// $today = date("Y-m-d H:i:s"); //will get the date based on the httpd.conf's timezone.
			$funcGivingIds = $this->input->post('functions_giving'); //will get all functions checked

			$isAdmin=false;
			if ($this->permissions->canAssignSuperAdmin()) {
				$isAdmin=$this->input->post('isAdmin');
			}

			$this->load->model(['roles']);
			// $this->roles->startTrans();
			$controller=$this;
			$error=null;
			$success=$this->dbtransOnly(function() use($controller, $role_id, $role_name, $isAdmin, $funcIds, $funcGivingIds, &$error, $visibleFuncIds, $funcParentIds, $funcParentGiveIds){
				return $controller->roles->updateRoleWithFunction($role_id, $role_name, $isAdmin, $funcIds, $funcGivingIds, $error, $visibleFuncIds, $funcParentIds, $funcParentGiveIds);
			});

			if($success){
				$this->syncRoleCurrentToMDBWithLock($role_id, $role_name, false);
			}

			// $data = array(
			// 	'roleName' => $role_name,
			// );

			# Only modify the isAdmin field when permitted user is logged in
			// if ($this->permissions->canAssignSuperAdmin()) {
			// 	$data['isAdmin'] = $this->input->post('isAdmin');
			// }

			// $this->rolesfunctions->editRoles($data, $role_id); //will edit the roles
			// $this->rolesfunctions->deleteRoleFunctions($role_id); // will delete existing rolefunctions in rolefunctions table
			// $this->rolesfunctions->deleteRoleFunctionsGiving($role_id); // will delete existing rolefunctions_giving in rolefunctions table

			// $funcs=$this->roles->getRoleFunctionsById($role_id);
			// $this->utils->debug_log('role func', $funcs);

			// $parent_ids = $this->rolesfunctions->getFunctionsParentId($functions);

			// $functions = $functions . ',' . $parent_ids;

			// $this->rolesfunctions->addRoleFunctions($functions, $role_id);

			// if ($this->checkIfHasPermissionRole($functions)) {
			// 	@$functions_giving = implode(",", $this->input->post('functions_giving')); //will get all functions checked
			// 	$parent_ids_giving = $this->rolesfunctions->getFunctionsParentId($functions_giving);
			// 	$functions_giving = $functions_giving . ',' . $parent_ids_giving;

			// 	$this->rolesfunctions->addRoleFunctionsGiving($functions_giving, $role_id);
			// }

			// $success=$this->roles->endTransWithSucc();
			$this->saveAction(self::MANAGEMENT_NAME, 'Edit Role', "User " . $this->authentication->getUsername() . " edited role " . $role_name);
			if($success){
				$message = lang('con.rom03') . $role_name;
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message); //will set and send message to the user
			}else{
				$message = !empty($error) ? $error : lang('error.default.message');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message); //will set and send message to the user
			}

			redirect('role_management/viewRoles'); //redirect to viewRoles
		}
	}

	/**
	 * overview : view roles
	 *
	 * detail : call add role function and get all functions from functions table
	 * @return  rendered template
	 */
	public function viewRoles() {
		if (!$this->permissions->checkPermissions('admin_manage_user_roles') && !$this->permissions->checkPermissions('role')) {
			$this->error_access();
		} else {
			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}
			// sets the history for breadcrumbs
			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active'));
			}

			$this->history->setHistory('header_system.system_word25', 'user_management/viewRoles');

			$this->loadTemplate('Role Management', '', '', 'system');
			$this->load->model(['users','roles']);
			$user_id = $this->authentication->getUserId();
			$roleId=$this->users->getRoleIdByUserId($user_id);

			$hasRolesAccess = $this->permissions->checkPermissions('admin_manage_user_roles');

			$data['roles'] = $this->roles->getAllRoles($roleId, null, null, $user_id, $hasRolesAccess);

			foreach ($data['roles'] as &$role) {

				$role['user_count'] = $this->rolesfunctions->countUsersUsingRoles($role['roleId']);
				$role['role_count'] = $this->rolesfunctions->countActiveRolesByThisRoleId($role['roleId']);
			}

			$this->template->write_view('main_content', 'system_management/view_role', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : get roles pages
	 *
	 * @param  string 	$segment
	 * @return array
	 */
	public function getRolesPages($segment = '') {
		$user_id = $this->authentication->getUserId();

		$data['count_all'] = count($this->rolesfunctions->getAllRolesByUser($user_id, null, null));
		$config['base_url'] = "javascript:get_roles_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '5';
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

		$data['roles'] = $this->rolesfunctions->getAllRolesByUser($user_id, $config['per_page'], $segment);

		$this->load->view('system_management/view_role', $data);
	}

	/**
	 * overview : check action
	 *
	 * detail : pass data to other function
	 * @return array, int
	 */
	public function checkAction() {
		$type_of_action = $this->input->post('type_of_action');
		if ($this->input->post('role')) {
			$roles = implode(",", $this->input->post('role'));

			switch ($type_of_action) {
			case 'Delete':
				$this->deleteRole($roles);
				break;

			case 'Lock':
				$this->lockRole($roles, '1');
				break;

			case 'Unlock':
				$this->lockRole($roles, '0');
				break;

			default:
				# code...
				break;
			}
		} else {
			$message = lang('con.rom04');
			$this->alertMessage(2, $message);
			redirect('role_management/viewRoles'); //redirect to viewRoles
		}
	}

	/**
	 * overview : delete role
	 *
	 * detail : get param data from checkAction
	 * @param	array 	$roles
	 * @return  array
	 */
	public function deleteRole($roles) {
		if (!$this->permissions->checkPermissions('admin_manage_user_roles') && !$this->permissions->checkPermissions('role')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Role Management', '', '', 'system');

			$user_role_id = $this->permissions->getRoleId();
			$user_role_name = $this->permissions->getRoleName();
			$success_role_ids = array();
			$fail_role_ids = array(
				'user_and_role' => array(),
				'users' => array(),
				'roles' => array(),
			);

			$roles = explode(',', $roles);

			foreach ($roles as $role_id) {
				if ($role_id != $user_role_id) {
					$result = $this->rolesfunctions->getRolesById($role_id);

					$deleteUsersUnderThisRole = $this->deleteUsersUnderThisRole($role_id);
					$deleteRolesUnderThisRole = $this->deleteRolesUnderThisRole($role_id);
					if ($deleteUsersUnderThisRole && $deleteRolesUnderThisRole) {
						$this->rolesfunctions->deleteGenealogy($role_id);
						$this->rolesfunctions->deleteRoleFunctions($role_id);
						$this->rolesfunctions->deleteRoleFunctionsGiving($role_id);
						$this->rolesfunctions->deleteUserRoles($role_id);
						$this->rolesfunctions->deleteRoles($role_id);

						array_push($success_role_ids, $result['roleName']);
					} else {
						//For set message
						if(!$deleteUsersUnderThisRole && !$deleteRolesUnderThisRole)
						{
							array_push($fail_role_ids['user_and_role'], $result['roleName']);

						}else if(!$deleteUsersUnderThisRole){

							array_push($fail_role_ids['users'], $result['roleName']);

						}else if(!$deleteRolesUnderThisRole){

							array_push($fail_role_ids['roles'], $result['roleName']);
						}
					}
				}
			}

			$this->saveAction(self::MANAGEMENT_NAME, 'Delete Role', "User " . $this->authentication->getUsername() . " deleted the selected role ");

			$message = $this->setDeleteMessages($success_role_ids, $fail_role_ids);

			$this->alertMessage($message['type'], $message['message']); //will set and send message to the user
			redirect('role_management/viewRoles'); //redirect to viewRoles
		}
	}

	/**
	 * overivew : set delete messages
	 *
	 * @param  array 	$success_role_ids
	 * @param  array 	$fail_role_ids
	 * @return array
	 */
	private function setDeleteMessages($success_role_ids, $fail_role_ids) {
		if (!empty($success_role_ids)) {

			$success_roles = implode(', ', $success_role_ids);
			$data['message'] = sprintf(lang('role.success_delete'), $success_roles);
			$data['type'] = 1;

		}

		if (count($fail_role_ids['user_and_role']) || count($fail_role_ids['users']) || count($fail_role_ids['roles'])) {

			$data['message'] .= "";
			$data['type'] = 2;

			if (!empty($data['message'])) {
				$data['message'] .= "</br>";
				$data['type'] = 3;
			}

			if(count($fail_role_ids['user_and_role'])) $data['message'] .=  sprintf(lang('role.err_del_fail'), implode(', ', $fail_role_ids['user_and_role'])) . '<br/>';
			if(count($fail_role_ids['users'])) $data['message'] .=  sprintf(lang('role.err_del_fail_users'), implode(', ', $fail_role_ids['users'])) . '<br/>';
			if(count($fail_role_ids['roles'])) $data['message'] .=  sprintf(lang('role.err_del_fail_roles'), implode(', ', $fail_role_ids['roles'])) . '<br/>';

		}

		return $data;
	}

	/**
	 * overview : delete user if its using or child of roleId
	 *
	 * @param	int 	$role_id
	 * @return  bool
	 */
	private function deleteUsersUnderThisRole($role_id) {
		$user_ids = $this->user_functions->getUserIdByRoleId($role_id);

		/*foreach ($user_ids as $user_id) {
			$this->user_functions->deleteUser($user_id['userId']);
		*/

		if (empty($user_ids)) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * overview : delete role if its using or child of roleId
	 *
	 * @param	int 	$role_id
	 * @return  bool
	 */
	private function deleteRolesUnderThisRole($role_id) {
		$role_ids = $this->rolesfunctions->getRoleIdByRoleId($role_id);

		/*foreach ($role_ids as $role_id) {
			$this->rolesfunctions->deleteGenealogy($role_id);
			$this->rolesfunctions->deleteRoleFunctions($role_id);
			$this->rolesfunctions->deleteRoleFunctionsGiving($role_id);
			$this->rolesfunctions->deleteUserRoles($role_id);
			$this->rolesfunctions->deleteRoles($role_id);
		*/

		if (empty($role_ids)) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * overview : lock role
	 *
	 * @param	array 	$roles
	 * @param	int 	$lock
	 * @return  array
	 */
	public function lockRole($roles, $lock) {
		if (!$this->permissions->checkPermissions('admin_manage_user_roles') && !$this->permissions->checkPermissions('role')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Role Management', '', '', 'system');

			$user_role_id = $this->permissions->getRoleId();
			$user_role_name = $this->permissions->getRoleName();

			$roles = explode(',', $roles);
			foreach ($roles as $role_id) {
				if ($role_id != $user_role_id) {
					$this->rolesfunctions->lockRoles($role_id, $lock);
					$this->lockUsersUnderThisRole($role_id, $lock);
					$this->lockRolesUnderThisRole($role_id, $lock);
				}
			}

			$this->saveAction(self::MANAGEMENT_NAME, 'Lock/Unlock Role', "User " . $this->authentication->getUsername() . " changed the status of selected role ");

			$message = lang('con.rom05');
			$this->alertMessage(1, $message); //will set and send message to the user

			redirect('role_management/viewRoles'); //redirect to viewRoles
		}
	}

	/**
	 * overview : lock user if its using or child of roleId
	 *
	 * @param  int 	$role_id
	 * @param  int 	$lock
	 * @return array
	 */
	private function lockUsersUnderThisRole($role_id, $lock) {
		$user_ids = $this->user_functions->getUserIdByRoleId($role_id);

		if ($lock == 1) {
			// if type is locked
			$data = array('status' => 2);
		} else {
			$data = array('status' => 1);
		}

		foreach ($user_ids as $user_id) {
			$this->user_functions->changeStatus($user_id['userId'], $data);
		}
	}

	/**
	 * overview : lock roles if its child of roleId
	 *
	 * @param  int 	$role_id
	 * @param  int 	$lock
	 * @return array
	 */
	private function lockRolesUnderThisRole($role_id, $lock) {
		$role_ids = $this->rolesfunctions->getRoleIdByRoleId($role_id);

		foreach ($role_ids as $role_id) {
			$this->rolesfunctions->lockRoles($role_id['roleId'], $lock);
		}
	}

	/**
	 * overview : search role
	 *
	 * @return  rendered template
	 */
	public function searchRole() {
		$this->form_validation->set_rules('search', 'Search', 'trim|required|xss_clean');

		if ($this->form_validation->run() == FALSE) {
			$this->viewRoles(); //redirect to viewRoles
		} else {
			$this->loadTemplate('Role Management', '', '', 'system');

			$user_id = $this->authentication->getUserId();

			$search = $this->input->post('search');

			$data['count_all'] = count($this->rolesfunctions->searchRole($user_id, $search, null, null));
			$config['base_url'] = "javascript:get_search_roles_pages('" . $search . "', ";
			$config['total_rows'] = $data['count_all'];
			$config['per_page'] = '3';
			$config['num_links'] = '2';
			$config['first_tag_open'] = $config['last_tag_open'] = $config['next_tag_open'] = $config['prev_tag_open'] = $config['num_tag_open'] = '<li>';
			$config['first_tag_close'] = $config['last_tag_close'] = $config['next_tag_close'] = $config['prev_tag_close'] = $config['num_tag_close'] = '</li>';
			$config['cur_tag_open'] = "<li><span><b>";
			$config['cur_tag_close'] = "</b></span></li>";
			$this->pagination->initialize($config);

			$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);

			$data['roles'] = $this->rolesfunctions->searchRole($user_id, $search, $config['per_page'], null);

			$this->template->write_view('main_content', 'system_management/view_role', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : get search roles pages
	 *
	 * detail : search role pagination function
	 * @param  string 	$search
	 * @param  string 	$segment
	 * @return array
	 */
	public function getSearchRolesPages($search, $segment) {
		$data['count_all'] = count($this->rolesfunctions->searchRole($search, null, null));
		$config['base_url'] = "javascript:get_search_roles_pages('" . $search . "', ";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '3';
		$config['num_links'] = '2';
		$config['first_tag_open'] = $config['last_tag_open'] = $config['next_tag_open'] = $config['prev_tag_open'] = $config['num_tag_open'] = '<li>';
		$config['first_tag_close'] = $config['last_tag_close'] = $config['next_tag_close'] = $config['prev_tag_close'] = $config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";
		$this->pagination->initialize($config);

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);

		$data['roles'] = $this->rolesfunctions->searchRole($search, $config['per_page'], $segment);
		$this->load->view('system_management/view_role', $data);
	}

	public function sync_role_to_mdb($roleId){
		if ((!$this->permissions->checkPermissions('admin_manage_user_roles') && !$this->permissions->checkPermissions('role')) || empty($roleId)) {
			return $this->error_access();
		}

		$rlt=null;
		$this->load->model(['roles']);
		$roleName=$this->roles->getRoleNameById($roleId);
		$success=$this->syncRoleCurrentToMDBWithLock($roleId, $roleName, false, $rlt);

		if(!$success){
			$errKeys=[];
			foreach ($rlt as $dbKey => $dbRlt) {
				if(!$dbRlt['success']){
					$errKeys[]=$dbKey;
				}
			}
			$errorMessage=implode(',', $errKeys);
		    $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sync Role Failed').': '.$errorMessage);
		}else{
		    $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Sync Role Successfully'));
		}

		redirect('/role_management/viewRoles');

	}

}

/* End of file role_management.php */
/* Location: ./application/controllers/role_management.php */
