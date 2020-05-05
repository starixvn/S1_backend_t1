<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * Users
 *
 * This model represents user authentication data. It operates the following tables:
 * - adminusers
 *
 * General Behavior
 * * Add/Update/Delete User
 * * Manages Users
 * * Manages Currency
 * * Add/update/delete Currency
 * * Manages User's Roles
 * * Selects all Deparment
 * * Get/Set Current Language
 * * Reset Approved Withdrawal
 * * Get/Edit/Save API Settings
 * * Manages Super admin
 * * Manages Admins
 * * Manages Message
 *
 * @package	Authentication
 */

class Users extends BaseModel {

	protected $tableName = 'adminusers';

	const SUPER_ADMIN_NAME = 'admin';
	const SUPER_ADMIN_ID = 1;
	const ADMIN = 2;

	const ALL_USERS = 4;

	const FILTER_EMPTY_DATA = 1;
	const ONLY_EMPTY_DATA = 2;

	function __construct() {
		parent::__construct();
	}

	/**
	 * overview : get user by login
	 *
	 * detail : Get user record by login (username)
	 * @param	string 	$login
	 * @return	object
	 */
	function getUserByLogin($login) {
		$this->db->where('username', $login);

		$query = $this->db->get('adminusers');
		if ($query->num_rows() == 1) {
			return $query->row();
		}

		return NULL;
	}

	/**
	 * overview : update login information
	 *
	 * detail : Update user login info, such as IP-address or login time, and clear previously generated (but not activated) passwords.
	 * @param	int 	$user_id
	 * @param	bool 	$record_ip
	 * @param	bool 	$record_time
	 * @param   string 	$session
	 * @return	void
	 */
	function updateLoginInfo($user_id, $record_ip, $record_time, $session = '') {
		if ($record_ip) {
			$this->db->set('lastLoginIp', $this->input->ip_address());
		}

		if ($record_time) {
			$this->db->set('lastLoginTime', date('Y-m-d H:i:s'));
		}

		if ($session) {
			$this->db->set('session', $session);
		}

		$this->db->set('session_id', $this->session->userdata('session_id'));

		$this->db->where('userId', $user_id);
		$this->db->update('adminusers');
		//new login token
		$this->load->model(array('admin_login_token'));
		list($loginTokenId, $token) = $this->admin_login_token->newLoginToken($user_id);
		return $token;
	}

	/**
	 * overview : set logout
	 *
	 * detail : Update user login info, such as logout time.
	 * @param	int 	$user_id
	 * @param	array 	$data
	 * @return	void
	 */
	function setLogout($user_id, $data) {
		$this->db->where('userId', $user_id);
		$this->db->update('adminusers', $data);
	}

	/**
	 * overview : inser user
	 *
	 * detail : Inserts data to database
	 * @param	array
	 * @return	boolean
	 */
	public function insertUser($data) {
		$result = $this->db->insert('adminusers', $data);

		if (!$result) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * overview : select all user from database
	 *
	 * detail : select all user which is not deleted
	 * @param  int 	$user_id
	 * @param  int 	$limit
	 * @param  int 	$offset
	 * @param  int 	$sort_name
	 * @param  int 	$order
	 * @param  int 	$roleId
	 * @return array
	 */
	public function selectAllUsers($user_id, $limit, $offset, $sort_name, $order, $roleId, $hasRolesAccess) {

		if ($hasRolesAccess) {
			$user_id = self::SUPER_ADMIN_ID;
			$user = $this->user_functions->searchUser($user_id);
			$roleId = $user['roleId'];
		}

        $admin_usernames = $this->utils->getConfig("default_admin_usernames");
        $this->utils->debug_log("default_admin_usernames ================>", $admin_usernames);
        $data = [];

        foreach ($admin_usernames as $admin_username) {
            $data[$admin_username] = $this->selectUserExist($admin_username);
        }

        $admin_ids = array_column($data, "userId");

        $this->db->select("a.*, a.username AS creator, a.createPerson, d.roleName, d.roleId");
        $this->db->distinct();
        $this->db->from("adminusers AS a");
        $this->db->join("userroles AS c","c.userId = a.userId","INNER");
        $this->db->join("roles AS d","c.roleId = d.roleId","INNER");
		$this->db->join("genealogy AS g","d.roleId = g.roleId","INNER JOIN");
        $this->db->where("a.deleted",0);

        #if the user is admin show all user
        if ( ! in_array($user_id, $admin_ids)) {
            $this->db->where("a.createPerson",$user_id);
            // $this->db->where("g.gene",$roleId);
        }

        if ($limit != NULL) {
            if (!empty($offset)) {
                $this->db->limit($limit, $offset);
            }else{
                $this->db->limit($limit);
            }
        }

        if ($sort_name != NULL && $sort_name != 'undefined') {
            $this->db->order_by($sort_name);
        }

        $query = $this->db->get();

		$result = $query->result_array();

		return $result;
	}

	/**
	 * overview : update status
	 *
	 * detail : Update Status of a User
	 * @param  int 		$id
	 * @param  array 	$data
	 * @return void
	 */
	public function updateStatus($id, $data) {

		$this->db->where('userId', $id);
		$this->db->update('adminusers', $data);
	}

	/**
	 * overview : update password
	 *
	 * detail : Update Password of a User
	 * @param  int 		$int
	 * @param  string 	$username
	 * @param  array 	$data
	 * @return void
	 */
	public function updatePassword($id, $username, $data) {
		$this->db->set($data)->where('userId', $id);
		$this->runAnyUpdate('adminusers');
	}

	/**
	 * overview : select user exist
	 *
	 * detail : check if user exist
	 * @param  string 	$username
	 * @return void
	 */
	public function selectUserExist($username) {

		$this->db->from('adminusers')->where('deleted', '0')->where('username', $username);

        return $this->runOneRowArray();
	}

	/**
	 * overview : select user deleted
	 *
	 * detail : check user if deleted
	 * @param  string 	$username
	 * @return array
	 */
	public function selectUserDeleted($username) {
		$this->db->from('adminusers')->where('deleted', '1')->where('username', $username);

		return $this->runOneRowArray();
	}

	/**
	 * overview : select email exist
	 *
	 * detail : check if user's email exist
	 * @param  string $email
	 * @return bool
	 */
	public function selectEmailExist($email) {
		$this->db->from('adminusers')->where('email', $email);

		return $this->runOneRowArray();
	}

	/**
	 * overview : fake delete user
	 *
	 * detail : soft delete user
	 * @param  int 	$userid
	 * @return void
	 */
	public function fakeDeleteUser($userId) {
		$sql = <<<EOD
UPDATE adminusers SET deleted = '1', note=concat(note,' deleted ',username), username=? WHERE userId = ?
EOD;
        $this->runRawUpdateInsertSQL($sql, ['del:'.$this->utils->getNowForMysql(), $userId]);
    }

	/**
	 * overview : select user
	 *
	 * detail : Select user from the database
	 * @param  int 	 $userid
	 * @return array
	 */
	public function selectUser($userId) {
		$query = $this->db->query("SELECT a.*, b.username AS creator, r.* FROM adminusers AS a LEFT OUTER JOIN adminusers AS b ON a.createPerson = b.userId INNER JOIN userroles as ur ON a.userId = ur.userId INNER JOIN roles as r ON ur.roleId = r.roleId WHERE a.userId = '" . intval($userId) . "'");
		$result = $query->row_array();

		if (!$result) {
			return false;
		} else {
			return $result;
		}
	}

	/**
	 * overview : Update Status of a User
	 *
	 * @param  int 		$id
	 * @param  array 	$data
	 * @return void
	 */
	public function updateUser($id, $data) {
		$this->db->where('userId', $id);
		$this->db->update('adminusers', $data);

		return true;
	}

	/**
	 * overview : Update Status of a User
	 *
	 * @param  int 		$id
	 * @param  array 	$data
	 * @return void
	 */
	public function updateUserRole($id, $data) {
		$this->db->where('userId', $id);
		$this->db->update('userroles', $data);

		return true;
	}

	/**
	 * overview : Select all users from the database
	 *
	 * @param  int 		$id
	 * @param  bool 	$is_affiliate
	 * @return array
	 */
	public function selectUsersById($id, $is_affiliate = false) {
		if (!$is_affiliate) {
			$query = $this->db->query("SELECT * FROM adminusers WHERE userId = '" . intval($id) . "'");
		} else {
			$query = $this->db->query("SELECT * FROM affiliates WHERE affiliateId = '" . intval($id) . "'");
		}

		$result = $query->row_array();
		if (!$result) {
			return false;
		} else {
			return $result;
		}
	}
	/**
	 * overview : Select all user logs
	 *
	 * @param  string 	$username
	 * @return array
	 */
	public function selectUserLogs($username) {
		$query = $this->db->query("SELECT * FROM logs WHERE username = '" . $username . "'");
		$result = $query->result_array();
		if (!$result) {
			return false;
		} else {
			return $result;
		}
	}

	/**
	 * overview : select all department
	 *
	 * detail : select all department and groups duplicate deparment
	 * @return array
	 */
	public function selectAllDepartment() {
		$query = $this->db->query("SELECT DISTINCT department FROM adminusers");
		$result = $query->result_array();
		if (!$result) {
			return false;
		} else {
			return $result;
		}
	}

	/**
	 * overview : select like user
	 *
	 * detail : select user by username where is the username is similar the inputed username.
	 * @param  string 	$username
	 * @return array
	 */
	public function selectLikeUser($username) {
		$query = $this->db->query("SELECT	a.*, b.username AS creator FROM adminusers AS a LEFT OUTER JOIN adminusers AS b ON a.createPerson = b.userId WHERE a.username LIKE '%" . $username . "%' ORDER BY userId");
		$result = $query->result_array();
		return $result;
	}

	/**
	 * overview : select all username
	 *
	 * detail : select admin user by username where is username is exactly the same in inputed username.
	 * @param  string 	$username
	 * @return array
	 */
	public function selectUsername($username) {
		$query = $this->db->query("SELECT * FROM adminusers WHERE username = '" . $username . "'");
		$result = $query->row_array();
		if (!$result) {
			return false;
		} else {
			return $result;
		}
	}

	/**
	 * overview : add user role
	 *
	 * @param  array 	$data
	 * @return void
	 */
	public function addUserRole($data) {
		$result = $this->db->insert('userroles', $data);

	}

	/**
	 * overview : select user role
	 *
	 * detail : select user role by user id
	 * @param  int 	 $user_id
	 * @return array
	 */
	public function selectUserRole($user_id) {
		$query = $this->db->query("SELECT * FROM userroles WHERE userId = '" . intval($user_id) . "'");
		$result = $query->row_array();
		if (!$result) {
			return false;
		} else {
			return $result;
		}
	}

	/**
	 * overview : select role id
	 *
	 * @param  int 	 $user_id
	 * @return array
	 */
	public function selectRoleId($user_id) {
		$query = $this->db->query("SELECT roleId FROM userroles WHERE userId = '" . intval($user_id) . "'");
		$result = $query->row_array();
		if (!$result) {
			return false;
		} else {
			return $result;
		}
	}

	/**
	 * overview : select all user role
	 *
	 * @return array
	 */
	public function selectAllUserRole() {
		$query = $this->db->query("SELECT * FROM userroles");
		$result = $query->result_array();
		return $result;
	}

	/**
	 * overview : select all new user
	 *
	 * @return array
	 */
	public function selectAllNewUser() {
		$query = $this->db->query("SELECT	a.*, b.username AS creator FROM adminusers AS a LEFT OUTER JOIN adminusers AS b ON a.createPerson = b.userId WHERE a.status = 0");
		$result = $query->result_array();
		return $result;
	}

	/**
	 * overview : find by filters
	 *
	 * @param  array $data
	 * @param  int   $limit
	 * @param  int   $offset
	 * @param  int   $sort_by
	 * @param  int   $in
	 * @return array
	 */
	public function findByFilters($data, $limit, $offset, $sort_by, $in, $hasRoleAccess=null) { #cuet
		$search = array();
		$ctr = 0;

		foreach ($data as $key => $value) {
			if ($key == 'roleId' && $value != null) {
				$search[$key] = "d.roleId = '" . $value . "'";
			} elseif ($key == 'username' && $value != null) {
				$search[$key] = "a.username LIKE '%" . $value . "%'";
			} elseif ($key == 'realname' && $value != null) {
				$search[$key] = "a.realname LIKE '%" . $value . "%'";
			} elseif ($key == 'createPerson' && $value != null) {
				$search[$key] = "a.createPerson = '" . $value . "'";
			} elseif ($key == 'department' && $value != null) {
				$search[$key] = "a.department LIKE '%" . $value . "%'";
			} elseif ($key == 'position' && $value != null) {
				$search[$key] = "a.position LIKE '%" . $value . "%'";
			} elseif ($key == 'lastLoginIp' && $value != null) {
				$search[$key] = "a.lastLoginIp LIKE '%" . $value . "%'";
			} elseif ($key == 'status' && $value != null ) {
				if($value != self::ALL_USERS) {
					$search[$key] = "a.status = '" . $value . "'";
				}
			} elseif ($value != null) {
				$search[$key] = "$key LIKE '%" . $value . "%'";
			}
		}

        $admin_usernames = $this->utils->getConfig("default_admin_usernames");
        $this->utils->debug_log("default_admin_usernames ================>", $admin_usernames);
        $data = [];

        foreach ($admin_usernames as $admin_username) {
            $data[$admin_username] = $this->selectUserExist($admin_username);
        }

		$user_id = $hasRoleAccess ? self::SUPER_ADMIN_ID :  $this->authentication->getUserId();

        $admin_ids = array_column($data, "userId");

        $this->db->select("a.*, a.username AS creator, d.roleName, d.roleId");
        $this->db->from("adminusers AS a");
        $this->db->join("userroles AS c","c.userId = a.userId","INNER");
        $this->db->join("roles AS d","c.roleId = d.roleId","INNER");
		$this->db->where("a.deleted", 0);

		#if the user is admin show all user
        if ( ! in_array($user_id, $admin_ids)) {
            $this->db->where("a.createPerson",$user_id);
			// $this->db->where("g.gene",$roleId);
        }

		if (count($search) > 0) {
            $this->db->where(implode(' AND ', $search));
		}

        if (!empty($sort_by)) {
            if (!empty($in)) {
                $this->db->order_by($sort_by,$in);
            }else{
                $this->db->order_by($sort_by);
            }
        }
        if ($limit != NULL) {
            if ($offset != NULL && $offset != 'undefined') {
                $this->db->limit($limit,$offset);
            }else{
                $this->db->limit($limit);
            }
        }

        $result = $this->db->get();

		if (!$result->result_array()) {
			return false;
		} else {
			return $result->result_array();
		}
	}

	/**
	 * overview : get user id by role id
	 *
	 * detail : get userId if its using or child of roleId
	 * @param	int 	$role_id
	 * @return 	array
	 */
	public function getUserIdByRoleId($role_id) {

		$query = $this->db->query("SELECT ur.userId FROM
			userroles AS ur INNER JOIN
			roles AS r ON
			ur.roleId = r.roleId INNER JOIN
			genealogy AS g ON
			r.roleId = g.roleId
			JOIN adminusers AS a ON
     		ur.userId = a.userId
     		WHERE g.roleId = '" . $role_id . "'
			AND a.deleted <> 1;
		");

		return $query->result_array();
	}

	/**
	 * overview : get all currency
	 *
	 * @param  int $sort
	 * @param  int $limit
	 * @param  int $offset
	 * @return array
	 */
	public function getAllCurrency($sort, $limit, $offset) {

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = '';
		}

		$query = $this->db->query("SELECT * FROM currency ORDER BY $sort ASC $limit $offset");

		return $query->result_array();
	}

	/**
	 * overview : get active currency
	 *
	 * @return 	array
	 */
	public function getActiveCurrency() {
		if($this->utils->isEnabledMDB()){
			return $this->utils->getActiveCurrencyDBFormatOnMDB();
		}

		$query = $this->db->query("SELECT * FROM currency where status = 0");

		return $query->row_array();
	}

	/**
	 * overview : get currency by id
	 *
	 * @param	int 	$currency_id
	 * @return 	array
	 */
	public function getCurrencyById($currency_id) {
		if($this->utils->isEnabledMDB()){
			return $this->utils->getActiveCurrencyDBFormatOnMDB();
		}

		$query = $this->db->query("SELECT * FROM currency where currencyId = '" . $currency_id . "'");

		return $query->row_array();
	}

	/**
	 * overview : get currency details
	 *
	 * @param	int 	$currency_id
	 * @return 	array
	 */
	public function getCurrencyDetails($currency_id) {
		$this->db->select('*')->from('currency');
		$this->db->where('currencyId', $currency_id);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * overview : update currency
	 *
	 * @param	int 	$currency_id
	 * @param   array 	$data
	 * @return 	void
	 */
	public function updateCurrency($data, $currency_id = NULL) {
	    if(!empty($currency_id)){
            $this->db->where('currencyid', $currency_id);
        }
		$this->db->update('currency', $data);
	}

	/**
	 * overview : search currency
	 *
	 * @param	string 	$search
	 * @param	int 	$limit
	 * @param	int 	$offset
	 * @return 	array
	 */
	public function getSearchCurrency($search, $limit, $offset) {

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = '';
		}

		$query = $this->db->query("SELECT * FROM currency where currencyCode = '" . $search . "' || currencyName = '" . $search . "' $limit $offset");

		return $query->result_array();
	}

	/**
	 * overview : add currency
	 *
	 * @param	array $data
	 * @return 	void
	 */
	public function addCurrency($data) {
		$this->db->insert('currency', $data);
	}

	/**
	 * overview : delete currency
	 *
	 * @param	int 	$currency_id
	 * @return 	void
	 */
	public function deleteCurrency($currency_id) {
		$this->db->where('currencyId', $currency_id);
		$this->db->delete('currency');
	}

	/**
	 * overview : get current language
	 *
	 * @return 	array
	 */
	public function getCurrentLanguage() {

		$query = $this->db->query("SELECT currentLanguage FROM languagesetting");

		return $query->row_array();
	}

	/**
	 * overview : set current language
	 *
	 * @return void
	 */
	public function setCurrentLanguage($data) {
		$this->db->where('languagesettingId', 1);
		$this->db->update('languagesetting', $data);
	}

	/**
	 * overview : get admin current session
	 *
	 * detail : check admin current session
	 * @param	int 	userId
	 * @return 	array
	 */
	public function getAdminCurrentSession($userId) {

		$query = $this->db->select("session")->from('adminusers');
		$this->db->where('adminusers.userId', $userId);
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * overview : reset approved withdrawal amount in adminusers
	 *
	 * @param	array $data
	 * @return 	array
	 */
	public function resetApprovedWithdrawal($data) {
		$this->db->set($data);
		return $this->runAnyUpdate('adminusers');
	}

	/* API Settings */

	/**
	 * overview : get API settings
	 *
	 * @param 	int 	$api_id
	 * @return 	array
	 */
	function getAPISettings($api_id) {
		$query = $this->db->query("SELECT g.*, api.* FROM api_settings as api
			LEFT JOIN game as g ON api.apiId = g.gameId
			WHERE api.apiId = '" . $api_id . "'
		");

		return $query->row_array();
	}

	/**
	 * overview : edit API settings
	 *
	 * @param 	array $data
	 * @param 	int   $api_id
	 * @return 	void
	 */
	function editAPISettings($data, $api_id) {
		$this->db->where('apiSettingsId', $api_id);
		$this->db->update('api_settings', $data);
	}

	/**
	 * overview : save API settings
	 *
	 * @param 	arry $data
	 * @return 	void
	 */
	function saveAPISettings($data) {
		$this->db->insert('api_settings', $data);
	}

	/* end of API Settings */

	/**
	 * overview : get superadmin
	 *
	 * @return array
	 */
	public function getSuperAdmin() {
		$this->db->from($this->tableName)->where('username', self::SUPER_ADMIN_NAME);
		return $this->runOneRow();
	}

	/**
	 * overview : get superadmin id
	 *
	 * @return int
	 */
	public function getSuperAdminId() {
		$admin = $this->getSuperAdmin();
		return $admin->userId;
	}

	/**
	 * overview : get all usernames in adminusers
	 *
	 * @return array
	 */
	public function getAllUsernames() {

		$sql = "SELECT userId, username FROM adminusers	";

		return $this->db->query($sql)->result_array();

	}

	/**
	 * overview : inc approved withdrawal amount
	 *
	 * detail : update approved withdrawal ammount for adminusers
	 * @param  int 		$adminUserId
	 * @param  double 	$amount
	 * @return  array
	 */
	public function incApprovedWithdrawalAmount($adminUserId, $amount) {
		$this->db->set('approvedWidAmt', 'approvedWidAmt+' . $amount, false)
			->where('userId', $adminUserId)
			->update($this->tableName);
		return $this->db->affected_rows();
	}

	/**
	 * overview : check username if Exist
	 *
	 * @param  string 	$username
	 * @return array
	 */
	public function checkUsernameIfExist($username) {

		$sql = "SELECT userId as id , username FROM adminusers WHERE username = ? ";

		$q = $this->db->query($sql, array($username));

		$results = $q->result();

		if ($q->num_rows() > 0) {
			$results['isExist'] = TRUE;
			return $results;
		} else {
			return FALSE;
		}
	}

	/**
	 * overview : select newest Admin Users
	 *
	 * @param  double 	$rows
	 * @return array
	 */
	public function selectNewestAdminUsers($rows) {
		$today = date("Y-m-d H:i:s");
		$sql = 'SELECT * FROM adminusers  WHERE createTime <= ?  order by createTime DESC LIMIT ? ';
		$query = $this->db->query($sql, array($today, $rows));
		return array(
			'total' => $query->num_rows(),
			'data' => $query->result_array(),
		);
	}

	/**
	 * overview : select Ci Admin Sessions
	 *
	 * detail : check codeigniter admin sessions
	 * @return array
	 */
	public function selectCiAdminSessions() {
		// $sql = 'SELECT * FROM ci_admin_sessions';
		// $query = $this->db->query($sql);
		// return array(
		// 	'total' => $query->num_rows(),
		// 	'data' => $query->result_array(),
		// );
	}

	/**
	 * overview : get public info by id
	 *
	 * @param  int 		$id
	 * @return array
	 */
	public function getPublicInfoById($id) {
		$this->db->select('username,department,email')->from('adminusers')->where('userId', $id);

		return $this->runOneRow();
	}

	/**
	 * overview : search admin session
	 *
	 * @param  int 		$user_id
	 * @return array
	 */
	public function searchAdminSession($user_id) {
		if($this->utils->getConfig('sess_use_database')){
			// $this->load->library(array('session'));
			$this->db->from('ci_admin_sessions');
			$sessions = array();
			$rows = $this->runMultipleRow();
			foreach ($rows as $row) {
				$user_data = $row->user_data;
				if (!empty($user_data)) {
					$data = $this->utils->unserializeSession($user_data);
					if (!empty($data) && isset($data['user_id']) && $data['user_id'] == $user_id) {
						$sessions[] = $row->session_id;
					}
				}
			}
			return $sessions;
		}else{
			$specialSessionTable='ci_admin_sessions';
			return $this->searchSessionIdByObjectIdOnRedis($specialSessionTable, $user_id);
		}
	}

	/**
	 * overview : kickout admin user
	 *
	 * @param  int 		$user_id
	 * @return bool
	 */
	public function kickoutAdminuser($user_id) {
		if($this->utils->getConfig('sess_use_database')){
			$sessions = $this->searchAdminSession($user_id);
			if (!empty($sessions)) {
				$this->db->where_in('session_id', $sessions)->delete('ci_admin_sessions');
				return $this->db->affected_rows();
			}
		}else{
			//clear redis
			$specialSessionTable='ci_admin_sessions';
			$this->deleteSessionsByObjectIdOnRedis($specialSessionTable, $user_id);
		}

		return true;
	}

	/**
	 * overview : kickout admin user
	 *
	 * @param  string 		$username
	 * @return bool
	 */
	public function kickoutAdminuserByUsername($username){
		$user_id= $this->getUserIdByUsername($username);
		if(!empty($user_id)){
			return $this->kickoutAdminuser($user_id);
		}
		return true;
	}

	/**
	 *
	 * get user id by username
	 *
	 * @param string $username
	 * @return user id or null
	 */
	public function getUserIdByUsername($username){
		$this->db->select('userId')->from('adminusers')->where('username', $username);
		return $this->runOneRowOneField('userId');
	}

	/**
	 *
	 * get user username by id
	 *
	 * @param in $id
	 * @return user username or null
	 */
	public function getUserUsernameByid($id){
		$this->db->select('username')->from('adminusers')->where('userId', $id);
		return $this->runOneRowOneField('username');
	}

	/**
	 * overview : get user info by id
	 *
	 * @param  int 		$userId
	 * @return array
	 */
	public function getUserInfoById($userId) {
		$query = $this->db->query("SELECT a.*, b.username AS creator, r.roleName, r.roleId FROM adminusers AS a LEFT OUTER JOIN adminusers AS b ON a.createPerson = b.userId INNER JOIN userroles as ur ON a.userId = ur.userId INNER JOIN roles as r ON ur.roleId = r.roleId WHERE a.userId = ?", array($userId));
		$result = $query->row_array();

		if (!$result) {
			return false;
		} else {
			return $result;
		}
	}

	const STATUS_UNREAD = 3;
	const STATUS_READ = 4;

	/**
	 * overview : get username by id
	 *
	 * @param  int 		$userId
	 * @return array
	 */
	public function getUsernameById($userId) {
		$this->db->select('username')->from('adminusers')->where('userId', $userId);
		return $this->runOneRowOneField('username');
	}

	/**
	 * overview : write unread admin message
	 *
	 * @param  string 	$user
	 * @param  string 	$content
	 * @param  string 	$options
	 * @return insert data to database
	 */
	public function writeUnreadAdminMessage($user, $content, $options) {
		$data = ['content' => $content,
			'options' => $options,
			'from_username' => $user,
			'status' => self::STATUS_UNREAD,
			'created_at' => $this->utils->getNowForMysql(),
			'updated_at' => $this->utils->getNowForMysql(),
		];

		return $this->insertData('admin_messages', $data);
	}

	/**
	 * overview : read admin message
	 *
	 * @param  int 		$id
	 * @return array
	 */
	public function readAdminMessage($id) {
		$msg = $this->runOneRowArrayById($id, 'admin_messages', 'id');
		if (!empty($msg)) {
			$this->db->set('status', self::STATUS_READ)->set('updated_at', $this->utils->getNowForMysql())->where('id', $id);
			$rlt = $this->runAnyUpdate('admin_messages');
		}
		return $msg;
	}

	/**
	 * overview : count unread admin message
	 *
	 * @return int
	 */
	public function countUnreadAdminMessage() {
		$this->db->select('count(id) as cnt', false)->from('admin_messages')
			->where('status', self::STATUS_UNREAD)->where('deleted_at is null', null, false);

		return $this->runOneRowOneField('cnt');
	}

	/**
	 * overview : get all admin message
	 *
	 * @param  integer 	$limit
	 * @return array
	 */
	public function getAllAdminMessage($limit = 10) {
		$this->db->from('admin_messages')->order_by('created_at', 'desc')->limit($limit);
		return $this->runMultipleRowArray();
	}

	/**
	 * overview : get id by username
	 *
	 * @param  string 		$username
	 * @return array
	 */
	public function getIdByUsername($username) {
		$this->db->select('userId')->from('adminusers')->where('username', $username);
		return $this->runOneRowOneField('userId');
	}

	/**
	 * overview : get role id
	 *
	 * @param  int 	 $user_id
	 * @return role id
	 */
	public function getRoleIdByUserId($user_id) {
		$this->db->select('roleId')->from('userroles')->where('userId', $user_id);
		return $this->runOneRowOneField('roleId');
	}

	public function isAdminUser($userId) {
		if ($userId) {
			$this->db->select('roles.roleId')->from('roles')->join('userroles', 'userroles.roleId=roles.roleId')
			    ->where(["roles.isAdmin" => 1, "userroles.userId" => $userId]);
			return $this->runExistsResult();
		} else {
			return false;
		}
	}


	/**
	 * Encryption parameters for column Adminuser.password_plain
	 * @var	string	$openssl_cipher		OpenSSL cipher method, defaults to AES-256-CBC
	 * @var string	$openssl_iv			OpenSSL cipher initial vector
	 * @var string	$openssl_key		OpenSSL cipher key
	 */
	const openssl_cipher	= 'AES-256-CBC';
	const openssl_iv		= 's6Su4yAeS1&Vj5&*';
	const openssl_key		= 'g7,uVv7+11km9IJl-VFlcbUbTzmNt5Iq';


	/**
	 * Encrypts plaintext into base64-encoded crypttext by OpenSSL
	 * @param	string	$plaintext	The plaintext
	 * @return	string	crypt text (base64 encoded)
	 */
	protected function openssl_encrypt($plaintext) {
		return base64_encode(openssl_encrypt($plaintext, self::openssl_cipher, self::openssl_key, 0, self::openssl_iv));
	}

	/**
	 * Decrypts base64-encoded cryptetext to plaintext by OpenSSL
	 * @param  string	$crypttext	crypt text (base64 encoded)
	 * @return string	the plaintext
	 */
	protected function openssl_decrypt($crypttext) {
		return openssl_decrypt(base64_decode($crypttext), self::openssl_cipher, self::openssl_key, 0, self::openssl_iv);
	}

	/**
	 * Sets password plaintext for adminuser by userId
	 * @param	int		$id				= adminusers.userId
	 * @param	string	$password_plain	The password plaintext [description]
	 *
	 * @return	none
	 */
	public function setPasswordPlain($id, $password_plain) {
		$password_crypt = $this->openssl_encrypt($password_plain);
		// base64_encode(openssl_encrypt($password_plain, self::openssl_cipher, self::openssl_key, 0, self::openssl_iv));
		$this->db->set([ 'password_plain' => $password_crypt ])
			->where('userId', $id);
		$this->runAnyUpdate('adminusers');
	}

	/**
	 * Sets password plaintext for current adminuser
	 * @param	string	$password_plain	The password plaintext [description]
	 *
	 * @return	none
	 */
	public function setPasswordPlainForCurrentUser($password_plain) {
		$CI = & get_instance();
		$CI->load->library(['authentication', 'utils']);

		if ($CI->utils->isAnyEnabledApi([ T1LOTTERY_API ])) {
			$CI->load->library(['lottery_bo_roles']);
			$CI->lottery_bo_roles->setPasswordPlain($password_plain);
		}
		else {
			$user_id = $CI->authentication->getUserId();
			$this->setPasswordPlain($user_id, $password_plain);
		}
	}

	/**
	 * Gets password plaintext for adminuser by userId
	 * @param	int		$id				= adminusers.userId
	 *
	 * @return	string	The password plaintext
	 */
	public function getPasswordPlain($id) {
		$user = $this->selectUser($id);

		if (!$user) {
			return $user;
		}

		$password_crypt = $user['password_plain'];
		$password_plain = $this->openssl_decrypt($password_crypt);
		// openssl_decrypt(base64_decode($password_crypt), self::openssl_cipher, self::openssl_key, 0, self::openssl_iv);
		return $password_plain;
	}

	public function getAllUserNamesNotDeleted() {
		$this->db->select('userId, username')
			->from('adminusers')->where('deleted', 0)
			->order_by('username', 'asc');

		$rows = $this->runMultipleRowArray();

		return $rows;
	}

	public function isT1Admin($username){
		$exists=false;
		$t1admin_username_list=$this->utils->getConfig('t1admin_username_list');
		if(!empty($t1admin_username_list)){
			$exists=in_array($username, $t1admin_username_list);
		}

		return $exists;
	}

	public function isT1User($username){
		$exists=false;
		$t1_username_list=$this->utils->getConfig('t1_username_list');
		if(!empty($t1_username_list)){
			$exists=in_array($username, $t1_username_list);
		}

		return $exists;
	}

	public function isAuthorizedViewPaymentSecretUsers($username) {
		$exists=false;
		$authorized_view_payment_secret_list=$this->utils->getConfig('authorized_view_payment_secret_list');
		if(!empty($authorized_view_payment_secret_list)){
			$exists=in_array($username, $authorized_view_payment_secret_list);
			if(!$exists){
				$exists=in_array("all_user", $authorized_view_payment_secret_list);
			}
		}

		return $exists;
	}

	public function getDataInfoTableField($id){

		$row=$this->getDataByLogId($id);

		if(!empty($row)){
			$row  = json_decode($row, true);
		}
		return $row;
	}

	public function getDataByLogId($id){
		$this->db->select('extra')->from('logs')->where('logId', $id);
		return $this->runOneRowOneField('extra');
	}

    public function getUserIdsByStatus($status){
        $this->db->select('userId')->from('adminusers')->where('status', $status);
        $query = $this->db->get();
        if ($query && $query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $data[] = $row['userId'];
            }
            return $data;
        }
        return false;
    }

	/**
	 * get user by username
	 * @param  string $username
	 * @return array
	 */
	function getUserByUsername($username) {
		$this->db->from('adminusers')->where('username', $username)->limit(1);
		return $this->runOneRowArray();
	}

	public function getAllAdminUsers(){
		$this->db->select('username, userId')->from('adminusers')->where('deleted', 0);
		return $this->runMultipleRowArray();
	}

	public function hashPassword($original_password){
		require_once APPPATH . 'libraries/phpass-0.1/PasswordHash.php';
		$hasher = new PasswordHash('8', TRUE);
		return $hasher->HashPassword($original_password);
	}

	public function resetAdminPassword(){
		return $this->resetPasswordByUsername('admin');
	}

	public function resetSuperAdminPassword(){
		return $this->resetPasswordByUsername('superadmin');
	}

	public function resetPasswordByUsername($username){
		$pass=random_string('alnum', 8);
		$password=$this->hashPassword($pass);
		//it will update password only
		$this->db->where('username', $username)->set('password', $password);
		if($this->runAnyUpdate('adminusers')){
			return $pass;
		}

		return null;
	}

	public function syncUserRole($id, $data) {
		$success=false;
		$this->db->select('id')->from('userroles')->where('userId', $id);
		$userroleId=$this->runOneRowOneField('id');
		$this->utils->debug_log('query userroleId', $userroleId);
		if(!empty($userroleId)){
			$this->db->where('id', $userroleId)->set($data);
			$success=$this->runAnyUpdate('userroles');
		}else{
			$this->utils->debug_log('insert userroles', $data);
			$success=$this->insertData('userroles', $data);
		}

		return $success;
	}

	public function getUserById($userId){
		$this->db->from('adminusers')->where('userId', $userId);
		return $this->runOneRowArray();
	}

	public function disableOTPById($userId){
		$this->db->where('userId', $userId)
			->set('otp_secret', null);
		return $this->runAnyUpdate('adminusers');
	}

	public function updateOTPById($userId, $secret){
		$this->db->where('userId', $userId)
			->set('otp_secret', $secret);
		return $this->runAnyUpdate('adminusers');
	}

	public function initOTPById($userId){
		$api=$this->utils->loadOTPApi();
		$api->initAdminUser($userId);
		$result=$api->initCodeInfo();
		// $secret=$result['secret'];
		return $result;
	}

	public function validateOTPCode($userId, $secret, $code){
		$api=$this->utils->loadOTPApi();
		$api->initAdminUser($userId);
		$rlt= $api->validateCode($secret, $code);
		return $rlt;
	}

	public function validateOTPCodeByUserId($userId, $code){
		$secret=$this->getOTPSecretByUserId($userId);
		return $this->validateOTPCode($userId, $secret, $code);
	}

	public function validateOTPCodeByUsername($username, $code){
		$user=$this->getUserByUsername($username);
		$userId=$user['userId'];
		$secret=$user['otp_secret'];
		return $this->validateOTPCode($userId, $secret, $code);
	}

	public function validateCodeAndDisableOTPById($userId, $secret, $code){
		if(empty($secret) || empty($code)){
			return ['success'=>false, 'message'=>lang('Empty secret or code')];
		}
		$rlt= $this->validateOTPCode($userId, $secret, $code);
		if($rlt['success']){
			$succ=$this->disableOTPById($userId);
			if(!$succ){
				$rlt['success']=$succ;
				$rlt['message']=lang('Update 2FA failed');
			}
		}
		return $rlt;
	}

	public function validateCodeAndEnableOTPById($userId, $secret, $code){
		if(empty($secret) || empty($code)){
			return ['success'=>false, 'message'=>lang('Empty secret or code')];
		}
		$rlt= $this->validateOTPCode($userId, $secret, $code);
		if($rlt['success']){
			$succ=$this->updateOTPById($userId, $secret);
			if(!$succ){
				$rlt['success']=$succ;
				$rlt['message']=lang('Update 2FA failed');
			}
		}
		return $rlt;
	}

	public function getOTPSecretByUserId($userId){
		$this->db->select('otp_secret')->from('adminusers')->where('userId', $userId);
		return $this->runOneRowOneField('otp_secret');
	}

	public function isEnabledOTPByUsername($username){
		$this->db->select('otp_secret')->from('adminusers')->where('username', $username);
		$otp_secret=$this->runOneRowOneField('otp_secret');

		return !empty($otp_secret);
	}

	/**
	 * generateKeys
	 * @param  int $userId
	 * @return int number of affected
	 */
	public function generateKeys($userId){
        $sign_key=random_string('md5');
        $secure_key=random_string('md5');
        $this->db->set('sign_key', $sign_key)
        	->set('secure_key', $secure_key)
            ->where('userId', $userId);

        return $this->runAnyUpdateWithResult($this->tableName);
	}

	/**
	 * getKeysByUserId
	 * @param  int $userId
	 * @return array [$secure_key, $sign_key]
	 */
	public function getKeysByUserId($userId){
		$secure_key=null;
		$sign_key=null;
		$success=false;
		$this->db->select('secure_key, sign_key')->from($this->tableName)->where('userId', $userId);
		$row=$this->runOneRowArray();
		if(!empty($row)){
			$secure_key=$row['secure_key'];
			$sign_key=$row['sign_key'];
			return ['secure_key'=>$secure_key, 'sign_key'=>$sign_key];
		}

		return null;
	}

	private $permissionCache=[];

	/**
	 * generatePermissionList
	 * @param  int $userId
	 * @return array
	 */
	public function generatePermissionList($userId){
		if(array_key_exists($userId, $this->permissionCache) && !empty($this->permissionCache[$userId])){
			return $this->permissionCache[$userId];
		}
		$this->db->select('userroles.roleId')->from('userroles')
		    ->where('userroles.userId', $userId);
		$roleId=$this->runOneRowOneField('roleId');
		if(!empty($roleId)){
			$this->db->select('functions.funcCode')->from('functions')
				->join('rolefunctions', 'rolefunctions.funcId=functions.funcId')
				->where('rolefunctions.roleId', $roleId);
			$funcList=$this->runMultipleRowOneFieldArray('funcCode');
			$this->permissionCache[$userId]=$funcList;
			return $funcList;
		}

		return null;
	}

	/**
	 * granted all permissions
	 * @param  array  $permissions funcCode array
	 * @return boolean $granted
	 */
	public function checkAllPermissions($userId, array $permissions){
		$granted=false;
		if(!empty($userId) && !empty($permissions)){
			$funcList=$this->generatePermissionList($userId);
			foreach ($permissions as $funcCode) {
				$granted=in_array($funcCode, $funcList);
				//found any false
				if(!$granted){
					break;
				}
			}
		}
		return $granted;
	}

	/**
	 * granted any of permissions
	 * @param  int $userId
	 * @param  array  $permissions
	 * @return boolean $granted
	 */
	public function checkAnyPermissions($userId, array $permissions){
		$granted=false;
		if(!empty($userId) && !empty($permissions)){
			$funcList=$this->generatePermissionList($userId);
			foreach ($permissions as $funcCode) {
				$granted=in_array($funcCode, $funcList);
				//found any granted
				if($granted){
					break;
				}
			}
		}
		return $granted;
	}

}

/* End of file users.php */
/* Location: ./application/models/users.php */