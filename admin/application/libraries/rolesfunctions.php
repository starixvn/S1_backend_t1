<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * RolesFunctions
 *
 * RolesFunctions library for FHI OG System
 *
 * General Behavior
 * * Gets max generation of user role id
 * * Manages Genealogy
 * * Manages Roles
 * * Manages Role Functions
 * * Manages Role Functions Giving
 * * Add/Edit/Delete/Lock roles
 * * Get/Add/Delete Genealogy
 * * Add/Delete role functions
 * * Add/Delete role functions giving
 * * Counts users using roles
 * * Checks if role chosen has a permission for withdrawal list.
 *
 * @package		RolesFunctions
 * @version		1.0.0
 */

class RolesFunctions {
	private $error = array();
	private $roleId = null;
	private $list_roles = array();

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array(''));
		$this->ci->load->model(array('roles'));
	}

	/**
	 * overview : Get role by user_id in userroles table
	 *
	 * @param 	int 	$user_id
	 * @return	array
	 */
	public function getRoleByUserId($user_id) {
		return $this->ci->roles->getRoleByUserId($user_id);
	}

	/**
	 * get all functions from functions table
	 *
	 * @return  array
	 */
	/*public function getAllFunctions() {
		return $this->ci->roles->getAllFunctions();
	}*/

	/**
	 * overveiw : get all functions from rolefunctions_giving table
	 *
	 * @param  int 	$role_id
	 * @return  array
	 */
	public function getAllFunctionsGiving($role_id) {
		return $this->ci->roles->getAllFunctionsGiving($role_id);
	}

	/**
	 * overview : get all functions from functions table
	 *
	 * @param  array 	$functions
	 * @return  array
	 */
	public function getFunctionsParentId($functions) {
		$cur_parentid = null;
		$parent_ids = array();
		$cnt = 0;

		$functions = explode(',', $functions);

		foreach ($functions as $value) {
			/*$res = array_search($value, $functions);

				if (!empty($res)) { echo 'break';
					continue;
			*/
			if ($value) {
				$result = $this->ci->roles->getFunctionsParentId($value);

				if ($cur_parentid != $result['parentId'] && $result['parentId'] != 0) {
					$cur_parentid = $result['parentId'];
					$parent_ids[$cnt] = $result['parentId'];
					$cnt++;
				}
			}
		}

		$sub_parent_ids = $parent_ids;
		if (!empty($sub_parent_ids)) {
			foreach ($sub_parent_ids as $value) {
				$result = $this->ci->roles->getFunctionsParentId($value);

				if ($cur_parentid != $result['parentId'] && $result['parentId'] != 0) {
					$cur_parentid = $result['parentId'];
					array_push($parent_ids, $result['parentId']);
					$cnt++;
				}
			}

			$parentId = implode(',', $parent_ids);
			return $parentId;
		}
		return false;
	}

	/**
	 * overview : add Roles
	 *
	 * @param 	array 	$data
	 * @return  array
	 */
	public function addRoles($data) {
		return $this->ci->roles->addRoles($data);
	}

	/**
	 * overview : check if role exists
	 *
	 * @param  string 	$string
	 * @return bool
	 */
	public function checkIfRoleExists($role_name) {
		return $this->ci->roles->checkIfRoleExists($role_name);
	}

	/**
	 * overview : add all functions base on role id to rolesfunctions table
	 *
	 * @param 	array 	$functions
	 * @param 	int 	$role_id
	 */
	public function addRoleFunctions($functions, $role_id) {
		$this->ci->roles->addRoleFunctions($functions, $role_id);
	}

	/**
	 * overview : add all functions base on role id to rolesfunctions_giving table
	 *
	 * @param 	array 	$functions_givin
	 * @param 	int 	$role_id
	 */
	public function addRoleFunctionsGiving($functions_giving, $role_id) {
		$this->ci->roles->addRoleFunctionsGiving($functions_giving, $role_id);
	}

	/**
	 * overview : get all roles from roles table
	 *
	 * @param   int 	$user_id
	 * @param   int 	$limit
	 * @param   int 	$offset
	 * @return  array
	 */
	public function getAllRoles($user_id, $limit, $offset) {
		$role_id = $this->getRoleIdOfUser($user_id);

		return $this->ci->roles->getAllRoles($role_id, $limit, $offset, $user_id);//added user_id parameter (keir)
	}

	public function getAllRolesByUser($user_id, $limit, $offset, $hasRolesAccess=null) {
		$role_id = $this->getRoleIdOfUser($user_id);
		return $this->ci->roles->getAllRoles($role_id, $limit, $offset, $user_id, $hasRolesAccess);//added user_id parameter (keir)
	}

	/**
	 * overview : get roles by id from roles table
	 *
	 * @param  int 		$role_id
	 * @return  array
	 */
	public function getRolesById($role_id) {
		return $this->ci->roles->getRolesById($role_id);
	}

	/**
	 * overview : get roles functions name by id
	 *
	 * @param	int 	$role_id
	 * @return  array
	 */
	public function getRolesFunctionsById($role_id) {
		$rolesfunctions = $this->ci->roles->getRoleFunctionsById($role_id);
		$functions = array();

		foreach ($rolesfunctions as $value) {
			array_push($functions, $value['funcCode']);
		}

		return $functions;
	}

	/**
	 * overview : get roles functions id by id
	 *
	 * @param	int 	$role_id
	 * @return  array
	 */
	public function getRolesFunctionsIdById($role_id) {
		$rolesfunctions = $this->ci->roles->getRoleFunctionsById($role_id);
		$functions = array();

		foreach ($rolesfunctions as $value) {
			array_push($functions, $value['funcId']);
		}

		return $functions;
	}

	/**
	 * overview : get roles functions giving id by id
	 *
	 * @param	int 	$role_id
	 * @return  array
	 */
	public function getRolesFunctionsGivingIdById($role_id) {
		$rolesfunctions = $this->ci->roles->getRoleFunctionsGivingById($role_id);
		$functions = array();

		foreach ($rolesfunctions as $value) {
			array_push($functions, $value['funcId']);
		}

		return $functions;
	}

	/**
	 * overview : find if the function exists in pool array
	 *
	 * @param	array 	$rolesfunctions
	 * @param	int 	$function
	 * @return  bool
	 */
	public function findIfFunctionExists($rolesfunctions, $function) {
		foreach ($rolesfunctions as $value) {
			if ($value == $function) {
				return true;
			}
		}

		return false;
	}

	/**
	 * overview : edit roles from roles table
	 *
	 * @param 	array 	$data
	 * @param 	int 	$role_id
	 * @return  void
	 */
	public function editRoles($data, $role_id) {
		$this->ci->roles->editRoles($data, $role_id);
	}

	/**
	 * overview : delete rolefunctions from rolefunctions table
	 *
	 * @param 	int 	$role_id
	 * @return  void
	 */
	public function deleteRoleFunctions($role_id) {
		$this->ci->roles->deleteRoleFunctions($role_id);
	}

	/**
	 * overview : delete rolefunctions giving from rolefunctions table
	 *
	 * @param 	int 	$role_id
	 * @return  void
	 */
	public function deleteRoleFunctionsGiving($role_id) {
		$this->ci->roles->deleteRoleFunctionsGiving($role_id);
	}

	/**
	 * overview : delete userroles from userroles table
	 *
	 * @param 	int 	$role_id
	 * @return  void
	 */
	public function deleteUserRoles($role_id) {
		$this->ci->roles->deleteUserRoles($role_id);
	}

	/**
	 * overview : delete role from roles table
	 *
	 * @param 	int 	$role_id
	 * @return  void
	 */
	public function deleteRoles($role_id) {
		$this->ci->roles->deleteRoles($role_id);
	}

	/**
	 * overview : search role from roles table
	 *
	 * @param 	int 	$user_id
	 * @param 	string 	$search
	 * @param 	int 	$limit
	 * @param 	int 	$offset
	 * @return 	array
	 */
	public function searchRole($user_id, $search, $limit, $offset) {
		$role_id = $this->getRoleIdOfUser($user_id);

		return $this->ci->roles->searchRole($role_id, $search, $limit, $offset);
	}

	/**
	 * overview : count user using roles from userroles table
	 *
	 * @param 	int 	$role_id
	 * @return 	int
	 */
	public function countUsersUsingRoles($role_id) {
		$result = $this->ci->roles->countUsersUsingRoles($role_id);

		return $result['total'];
	}

	/**
	 * overview : lock roles to roles table
	 *
	 * @param 	int 	$lock
	 * @param 	int 	$role_id
	 * @return  void
	 */
	public function lockRoles($role_id, $lock) {
		$this->ci->roles->lockRoles($role_id, $lock);
	}

	/**
	 * overview : add generation to genealogy table
	 *
	 * @param 	int 	$user_id
	 * @param 	int 	$role_id
	 * @return  void
	 */
	public function addGenealogy($role_id, $user_id) {
		//$geneOfB = $this->getRoleIdOfUser($user_id);
		$geneOfB = $user_id;//instead of using role_id i changed it to user_id to avoid conflict displaying of data
		$maxGenerationOfA = $this->getMaxGenerationOfA($geneOfB);
		$generationOfB = $maxGenerationOfA + 1;
		$generationOfA = $this->getGenerationOfA($geneOfB);

		$this->addToGenealogy($role_id, $generationOfB, $geneOfB);

		for ($i = ($maxGenerationOfA - 2); $i >= 0; $i--) {
			$this->addToGenealogy($role_id, ($i + 2), $generationOfA[$i]['gene']);
		}
	}

	/**
	 * overview : process add genealogy to database table
	 *
	 * @param 	int 	$role_id
	 * @param 	int 	$generationOfB
	 * @param 	int 	$geneOfB
	 * @return  void
	 */
	private function addToGenealogy($role_id, $generationOfB, $geneOfB) {
		$this->ci->roles->addToGenealogy($role_id, $generationOfB, $geneOfB);
	}

	/**
	 * overview : get roleId or User from userroles
	 *
	 * @param    int 	$user_id
	 * @return   string
	 */
	private function getRoleIdOfUser($user_id) {
		return $this->ci->roles->getRoleIdOfUser($user_id);
	}

	/**
	 * overview : get max generation of User role id
	 *
	 * @param    int 	$role_id
	 * @return   array
	 */
	private function getMaxGenerationOfA($role_Id) {
		return $this->ci->roles->getMaxGenerationOfA($role_Id);
	}

	/**
	 * overview : get generation of User role id
	 *
	 * @param    int 	$role_id
	 * @return   string
	 */
	private function getGenerationOfA($role_Id) {
		return $this->ci->roles->getGenerationOfA($role_Id);
	}

	/**
	 * overview : delete role in genealogy table
	 *
	 * @param 	int
	 * @return  void
	 */
	public function deleteGenealogy($role_id) {
		$this->ci->roles->deleteGenealogy($role_id);
	}

	/**
	 * overview : get roleid of child of current role
	 *
	 * @param   int 	$role_id
	 * @return  array
	 */
	public function getRoleIdByRoleId($role_id) {
		return $this->ci->roles->getRoleIdByRoleId($role_id);
	}

	/**
	 * overview : check if role chosen has a permission for withdrawal list.
	 *
	 * @param   int 	$role_id
	 * @return  bool
	 */
	public function checkRole($roleId) {
		$role_permissions = $this->getRolesFunctionsById($roleId);

		$check = in_array("payment_withdrawal_list", $role_permissions);

		if ($check == 1) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * overview : count active roles under his role
	 * @param 	int 	$role_id
	 * @return 	int
	 */
	public function countActiveRolesByThisRoleId($role_id) {

		$under_role_ids   = $this->getRoleIdByRoleId($role_id);
		if (empty($under_role_ids)) return 0;
		
		$total = count(array_column($under_role_ids, 'roleId'));
		return $total;
	}

}

/* End of file rolesfunctions.php */
/* Location: ./application/libraries/rolesfunctions.php */