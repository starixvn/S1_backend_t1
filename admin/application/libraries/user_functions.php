<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * User_Functions
 *
 * User_Functions library for FHI OG System
 *
 * @package		User_Functions
 * @author		Rendell Nuñez
 * @version		1.0.0
 */

class User_Functions {
	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array('session'));
		$this->ci->load->model(array('users'));
	}

	/**
	 * Adds user to the database
	 *
	 * @return	boolean
	 */
	function addUsers($data) {
		$hasher = new PasswordHash('8', TRUE);
		$data['password'] = $hasher->HashPassword($data['password']);

		$result = $this->ci->users->insertUser($data);
		return $result;
	}

	/**
	 * Gets all users
	 *
	 * @return	array
	 */
	function getAllUsers($user_id, $limit, $offset, $sort_name, $order, $roleId, $hasRolesAccess=null) {
		$result = $this->ci->users->selectAllUsers($user_id, $limit, $offset, $sort_name, $order, $roleId, $hasRolesAccess);
		return $result;
	}

	/**
	 * Changes the status of a user
	 *
	 * @return	void
	 */
	function changeStatus($id, $data) {
		$result = $this->ci->users->updateStatus($id, $data);
		return $result;
	}

	/**
	 * Resets Password of a user
	 *
	 * @return	boolean
	 */
	function resetPassword($id, $username, $data) {
		$hasher = new PasswordHash('8', TRUE);
		$data['password'] = $hasher->HashPassword($data['password']);
		$this->ci->users->updatePassword($id, $username, $data);

	}

	/**
	 * Checks if user is already existing
	 *
	 * @return	boolean
	 */
	function checkUserExist($username) {
		$result = $this->ci->users->selectUserExist($username);
		return $result;
	}

	function checkUserIfDeleted($username) {
		$result = $this->ci->users->selectUserDeleted($username);
		return $result;
	}

	/**
	 * Checks if user is already existing
	 *
	 * @return	boolean
	 */
	function checkPassword($id, $opassword) {
		$hasher = new PasswordHash('8', TRUE);
		$user = $this->searchUser($id);
		$password = $user['password'];

		if ($hasher->CheckPassword($opassword, $password)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Checks if user is already existing
	 *
	 * @return	boolean
	 */
	function checkEmailExist($email) {
		$result = $this->ci->users->selectEmailExist($email);
		return $result;
	}

	/**
	 * Search user
	 *
	 * @return	boolean
	 */
	function searchUser($userId) {
		$result = $this->ci->users->selectUser($userId);
		return $result;
	}

	/**
	 * Deletes User Account
	 *
	 * @return	void
	 */
	// function deleteUser($userId) {
	// 	$result = $this->ci->users->deleteUser($userId);
	// 	return $result;
	// }

	/**
	 * Fake Deletes User Account
	 *
	 * @return	void
	 */
	function fakeDeleteUser($userId) {
		$result = $this->ci->users->fakeDeleteUser($userId);
		return $result;
	}

	/**
	 * Edit User Account
	 *
	 * @return	void
	 */
	function editUser($userId, $data) {
		$result = $this->ci->users->updateUser($userId, $data);
		return $result;
	}

	/**
	 * Gets all users
	 *
	 * @return	array
	 */
	function getUserById($id) {
		$result = $this->ci->users->selectUsersById($id);
		return $result;
	}

	/**
	 * Gets all department
	 *
	 * @return	array
	 */
	function getAllDepartment() {
		$result = $this->ci->users->selectAllDepartment();
		return $result;
	}

	/**
	 * Search user
	 *
	 * @return	boolean
	 */
	function findUser($username) {
		$result = $this->ci->users->selectLikeUser($username);
		return $result;
	}

	/**
	 * Search user
	 *
	 * @return	boolean
	 */
	function getUsername($username) {
		$result = $this->ci->users->selectUsername($username);
		return $result;
	}

	/**
	 * Search user
	 *
	 * @return	boolean
	 */
	function addUserRole($data) {
		$result = $this->ci->users->addUserRole($data);
		return $result;
	}

	/**
	 * Search user
	 *
	 * @return	boolean
	 */
	function getUserRole($user_id) {
		$result = $this->ci->users->selectUserRole($user_id);
		return $result;
	}

	/**
	 * Search user
	 *
	 * @return	boolean
	 */
	function getRoleId($user_id) {
		$result = $this->ci->users->selectRoleId($user_id);
		return $result;
	}

	/**
	 * Search user
	 *
	 * @return	boolean
	 */
	function getAllUserRole() {
		$result = $this->ci->users->selectAllUserRole();
		return $result;
	}

	/**
	 * Search user
	 *
	 * @return	boolean
	 */
	function getAllNewUsers() {
		$result = $this->ci->users->selectAllNewUser();
		return $result;
	}

	/**
	 * Search user
	 *
	 * @return	boolean
	 */
	function getCheckUserRoles($id) {
		$result = $this->ci->users->selectUserRole($id);
		return $result;
	}

	/**
	 * Search user
	 *
	 * @return	boolean
	 */
	function updateUserRole($id, $data) {
		$result = $this->ci->users->updateUserRole($id, $data);
		return $result;
	}

	/**
	 * get random password
	 *
	 * @return	string
	 */
	public function randomizer($username) {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
			. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. '0123456789!@#$%^&*()'
			. $username); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$randomPassword = '';
		foreach (array_rand($seed, 9) as $k) {
			$randomPassword .= $seed[$k];
		}

		return $randomPassword;
	}

	/**
	 * check if user is locked
	 *
	 * @return	boolean
	 */
	function checkIfUserIsLocked($user_id) {
		$user = $this->getUserById($user_id);

		if ($user['status'] != 1) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * find users base on filters
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return	array
	 */
	function findByFilters($filters, $limit, $offset, $sort, $in, $hasRoleAccess=null) {
		$result = $this->ci->users->findByFilters($filters, $limit, $offset, $sort, $in, $hasRoleAccess);
		return $result;
	}

	/**
	 * get userId if its using or child of roleId
	 *
	 * @param	int
	 * @return 	array
	 */
	function getUserIdByRoleId($role_id) {
		$result = $this->ci->users->getUserIdByRoleId($role_id);
		return $result;
	}

	/**
	 * get userId if its using or child of roleId
	 *
	 * @param	int
	 * @return 	array
	 */
	function getAllCurrency($sort, $limit, $offset) {
		$result = $this->ci->users->getAllCurrency($sort, $limit, $offset);
		return $result;
	}

	/**
	 * get userId if its using or child of roleId
	 *
	 * @param	int
	 * @return 	array
	 */
	function getActiveCurrency() {
		$result = $this->ci->users->getActiveCurrency();
		return $result;
	}

	/**
	 * get userId if its using or child of roleId
	 *
	 * @param	int
	 * @return 	array
	 */
	function getCurrencyById($currency_id) {
		$result = $this->ci->users->getCurrencyById($currency_id);
		return $result;
	}

	/**
	 * get userId if its using or child of roleId
	 *
	 * @param	int
	 * @return 	array
	 */
	function updateCurrency($data, $currency_id = NULL) {
		$result = $this->ci->users->updateCurrency($data, $currency_id);
		return $result;
	}

	/**
	 * get userId if its using or child of roleId
	 *
	 * @param	int
	 * @return 	array
	 */
	function getSearchCurrency($search, $limit, $offset) {
		$result = $this->ci->users->getSearchCurrency($search, $limit, $offset);
		return $result;
	}

	/**
	 * get userId if its using or child of roleId
	 *
	 * @param	int
	 * @return 	array
	 */
	function getCurrencyDetails($currency_id) {
		$result = $this->ci->users->getCurrencyDetails($currency_id);
		return $result;
	}

	/**
	 * get userId if its using or child of roleId
	 *
	 * @param	int
	 * @return 	array
	 */
	function addCurrency($data) {
		$result = $this->ci->users->addCurrency($data);
		return $result;
	}

	/**
	 * get userId if its using or child of roleId
	 *
	 * @param	int
	 * @return 	array
	 */
	function deleteCurrency($currency_id) {
		$result = $this->ci->users->deleteCurrency($currency_id);
		return $result;
	}

	/**
	 * reset approved withdrawal amount in adminusers
	 *
	 * @param	int
	 * @return 	array
	 */
	function resetApprovedWithdrawal($data) {
		$result = $this->ci->users->resetApprovedWithdrawal($data);
		return $result;
	}

	/* API Settings */

	/**
	 * get API settings
	 *
	 * @param 	int
	 * @return 	array
	 */
	function getAPISettings($api_id) {
		return $this->ci->users->getAPISettings($api_id);
	}

	/**
	 * edit API settings
	 *
	 * @param 	array
	 * @param 	int
	 * @return 	array
	 */
	function editAPISettings($data, $api_id) {
		$this->ci->users->editAPISettings($data, $api_id);
	}

	/**
	 * add API settings
	 *
	 * @param 	int
	 * @return 	array
	 */
	function saveAPISettings($data) {
		$this->ci->users->saveAPISettings($data);
	}

	/* end of API Settings */

    function getUserIdsByStatus($status){
        return $this->ci->users->getUserIdsByStatus($status);
    }

}

/* End of file user_functions.php */
/* Location: ./application/libraries/user_functions.php */