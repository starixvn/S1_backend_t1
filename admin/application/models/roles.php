<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * Roles
 *
 * This model represents roles data. It operates the following tables:
 * - roles
 * - rolefunctions
 * - functions
 * - userroles
 *
 * General Behavior
 * * Add/edit/delete/lock roles
 * * Manages roles
 * * Add/edit/delete role functions
 * * Manages role functions
 * * Add/edit/delete role functions giving
 * * Manages role functions giving
 * * Add/delete genealogy
 * * Manages Genealogy
 *
 * @category System Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Roles extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	const ROLE_MANAGEMENT_FUNCTION=2;
	const BANK_INFO_CONTROL_FUNC = 33; # duplicate permission
    const ROLE_WITHDRAW_PENDING_REQUEST = 343;
    const ROLE_WITHDRAW_PENDING_REVIEW = 344;
    const ROLE_APPROVE_DECLINE_WITHDRAW = 106;
    const ROLE_VERIFY_PHONE_AND_EMAIL_INFO = 158;
    const ROLE_VERIFY_PLAYER_CONTACT_NUMBER = 377;
    const ROLE_VERIFY_PLAYER_EMAIL = 376;
    const ROLE_VIP_MANAGEMENT_MAIN_PARENT = 313;
    const ROLE_MARKETING_MANAGEMENT_MAIN_PARENT = 59;
    const ROLE_VIP_SETTINGS = 314;
    const ROLE_VIP_REBATE_RULES_TEMPLATE = 323;
    const ROLE_VIP_REQUEST_LIST = 331;
    const ROLE_VIP_REBATE_VALUES = 329;
    const ROLE_VIP_REBATE_VALUES_EXPORT = 330;
    const ROLE_EMAIL_SETTING = 7;
    const ROLE_MANUALLY_PAY_CASHBACK = 170;
    const ROLE_VIP_GROUP_SETTING = 17;
	const ROLE_ADJUST_PLAYER_ACCOUNT_VERIFY_STATUS = 428;
	const ROLE_ADJUST_NEWSLETTER_SUBSCRIPTION_STATUS = 448;

	/**
	 * overview : Get role by user_id in userroles table
	 *
	 * @param	int 	$user_id
	 * @return	array
	 */
	public function getRoleByUserId($user_id) {
		if (!empty($user_id)) {
			$sql = "SELECT ur.*, r.roleName FROM userroles as ur"
				. " INNER JOIN roles as r on ur.roleId = r.roleId"
				. " WHERE userId = ?";

			$query = $this->db->query($sql, array($user_id));

			return $query->row_array();
		}
		return null;
	}

	/**
	 * overview : Get all functions in functions table
	 *
	 * @return	array
	 */
	/*public function getAllFunctions()
		{

		$query = $this->db->query("SELECT * FROM functions ORDER BY sort ASC");

		return $query->result_array();
	*/

	/**
	 * overview : get all functions giving
	 * detail : Get all functions base on functions giving table in functions table
	 * @param	int 	$role_id
	 * @return	array
	 */
	public function getAllFunctionsGiving($role_id) {

		$sql = "SELECT f.* FROM functions as f"
			. " INNER JOIN rolefunctions_giving as rfg ON f.funcId = rfg.funcId"
			. " WHERE rfg.roleId = ? AND f.status = " . self::DB_TRUE
			. " ORDER BY if(f.parentId=0, f.funcId, f.parentId), f.sort ASC";

		$query = $this->db->query($sql, array($role_id));

		return $query->result_array();
	}

	/**
	 * overview : get functions parent id
	 * detail : get all functions from functions table
	 * @param  int 		$func_id
	 * @return array
	 */
	public function getFunctionsParentId($func_id) {

		$sql = "SELECT * FROM functions where funcId = ? ORDER BY sort ASC";

		$query = $this->db->query($sql, array($func_id));

		return $query->row_array();
	}

	/**
	 * overview : get all roles
	 *
	 * detail : Get all roles in roles table
	 * @param   int 	$role_id
	 * @param   int 	$limit
	 * @param   int 	$offset
	 * @param   int 	$user_id
	 * @return	array
	 */
	public function getAllRoles($role_id, $limit, $offset, $user_id, $hasRolesAccess = null) {
		$where = null;

		if ($limit != NULL) {
			$limit = "LIMIT $limit";
		}

		if ($offset != NULL && $offset != 'undefined') {
			$offset = "OFFSET $offset";
		} else {
			$offset = "";
		}


		if ($role_id == 1) {
			$where = " OR r.roleId = '1'";
		}

		/*$query = $this->db->query("SELECT r.*, g.gene, ro.roleName as createRoleName FROM
			roles as r INNER JOIN
			genealogy as g ON
			r.roleId = g.roleId LEFT JOIN
			userroles as ur ON
			r.roleId = ur.roleId LEFT OUTER JOIN
			roles as ro ON
			ro.roleId = ur.roleId where
			g.gene = '" . $role_id . "'
			$where
			$limit
			$offset
		*/

		if($role_id == 1 || $hasRolesAccess){
			$sql = "SELECT roles.*, 0 as gene
			FROM roles
			$limit
			$offset"
			;
		}else {
			// or g.roleId = $role_id
			$sql = "SELECT distinct r.*, g.gene FROM
			roles as r INNER JOIN
			genealogy as g ON
			r.roleId = g.roleId
			where g.gene = $role_id
			$where
			$limit
			$offset";
		}


		$query = $this->db->query($sql, array($role_id));

		return $query->result_array();
	}

	/**
	 * overview : retrieve all roles
	 *
	 * @return array
	 */
	public function retrieveAllRoles() {
		return $this->db->order_by('roleName')->get('roles')->result_array();
	}

	/**
	 * overview : check if role exists
	 *
	 * @param  string 	$role_name
	 * @param  int 		$roleId
	 * @return bool
	 */
	public function checkIfRoleExists($role_name, $roleId = null) {
		$sql = "SELECT * FROM roles WHERE roleName = ?";
		$params = array($role_name);

		if ($roleId) {
			$sql .= ' and roleId!= ?';
			$params[] = $roleId;
		}

		$query = $this->db->query($sql, $params);

		$result = $query->row_array();

		if (!$result) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * overview : add roles in roles table
	 *
	 * @param   array 	$data
	 * @return	array
	 */
	public function addRoles($data) {
		$this->db->insert('roles', $data);

		$query = $this->db->get_where('roles', array('roleName' => $data['roleName']));

		return $query->row_array();
	}

	/**
	 * overview : add functions in rolefunctions table
	 *
	 * @param   int    $role_id
	 * @param   array  $data
	 * @return	array
	 */
	public function addRoleFunctions($data, $role_id) {
		$functions = explode(',', $data);

		$functions = array_unique($functions);

		foreach ($functions as $value) {
			$array = array(
				'roleId' => $role_id,
				'funcId' => $value,
			);

			$this->db->insert('rolefunctions', $array);
		}
	}

	/**
	 * overview : add role functions giving
	 *
	 * detail : add functions in rolefunctions_giving table
	 * @param   int    $role_id
	 * @param   array  $data
	 * @return	array
	 */
	public function addRoleFunctionsGiving($data, $role_id) {
		$functions = explode(',', $data);

		foreach ($functions as $value) {
			$array = array(
				'roleId' => $role_id,
				'funcId' => $value,
			);

			$this->db->insert('rolefunctions_giving', $array);
		}
	}

	/**
	 * overview : get roles by id
	 *
	 * @param   int   $role_id
	 * @return	array
	 */
	public function getRolesById($role_id) {

		$sql = "SELECT * FROM roles where roleId = ?";

		$query = $this->db->query($sql, array($role_id));

		return $query->row_array();
	}

	/**
	 * overview : get role functions by id
	 *
	 * @param   int   $role_id
	 * @return	array
	 */
	public function getRoleFunctionsById($role_id) {

		$sql = "SELECT rf.*, f.funcCode, f.parentId FROM rolefunctions as rf"
			. " INNER JOIN functions as f ON rf.funcId = f.funcId"
			. " WHERE roleId = ?"
			. " ORDER BY f.sort ASC";

		$query = $this->db->query($sql, array($role_id));

		return $query->result_array();
	}

	/**
	 * overview : get role functinos giving by id
	 *
	 * @param   int   $role_id
	 * @return	array
	 */
	public function getRoleFunctionsGivingById($role_id) {

		$sql = "SELECT rfg.*, f.funcCode FROM rolefunctions_giving as rfg"
			. " INNER JOIN functions as f ON rfg.funcId = f.funcId"
			. " WHERE roleId = ?";

		$query = $this->db->query($sql, array($role_id));

		return $query->result_array();
	}

	/**
	 * overview : edit roles in roles table
	 *
	 * @param   array $data
	 * @param   int   $role_id
	 * @param   void
	 */
	public function editRoles($data, $role_id) {
		$this->db->where('roleId', $role_id);
		$this->db->update('roles', $data);
	}

	/**
	 * overview : delete rolefunctions in rolefunctions table
	 *
	 * @param   int  $role_id
	 * @return  void
	 */
	public function deleteRoleFunctions($role_id) {
		$this->db->delete('rolefunctions', array('roleId' => $role_id));
	}

	/**
	 * overview : delete rolefunctions_giving in rolefunctions_giving table
	 *
	 * @param   int   $role_id
	 * @return  void
	 */
	public function deleteRoleFunctionsGiving($role_id) {
		$this->db->delete('rolefunctions_giving', array('roleId' => $role_id));
	}

	/**
	 * overview : delete rolefunctions in rolefunctions table
	 *
	 * @param   int 	$role_id
	 * @return  void
	 */
	public function deleteUserRoles($role_id) {
		$this->db->delete('userroles', array('roleId' => $role_id));
	}

	/**
	 * delete rolefunctions in rolefunctions table
	 *
	 * @param   int   $role_id
	 * @return  void
	 */
	public function deleteRoles($role_id) {
		$this->db->delete('roles', array('roleId' => $role_id));
	}

	/**
	 * search roles in roles table
	 *
	 * @param   int 	$role_id
	 * @param   string 	$search
	 * @param   int 	$limit
	 * @param   int 	$offset
	 * @return  array
	 */
	public function searchRole($role_id, $search, $limit, $offset) {
		if ($limit != NULL) {
			$limit = "LIMIT $limit";
		}

		if ($offset != NULL && $offset != 'undefined') {
			$offset = "OFFSET $offset";
		}


		$sql = "SELECT r.*, g.gene, au.realname, ro.roleName as createRoleName FROM
        	roles as r INNER JOIN
        	genealogy as g ON
        	r.roleId = g.roleId INNER JOIN
        	adminusers as au ON
        	r.createPerson = au.userId LEFT JOIN
        	userroles as ur ON
        	r.createPerson = ur.userId LEFT OUTER JOIN
        	roles as ro ON
        	ro.roleId = ur.roleId where
        	g.gene = ?
        	AND r.roleName LIKE '%?%'
        	$limit
        	$offset";

		$query = $this->db->query($sql, array($role_id, $search));

		return $query->result_array();
	}

	/**
	 * overview : count users using roles
	 *
	 * detail : count user using roles from userroles table
	 * @param 	int   $role_id
	 * @return 	int
	 */
	public function countUsersUsingRoles($role_id) {

		// $sql = "SELECT COUNT(roleId) as total FROM `userroles` where roleId = ?";
		$sql = "SELECT
				    COUNT(`userroles`.`roleId`) as total
				FROM
				    `userroles`
				    INNER JOIN `adminusers`
				        ON (`userroles`.`userId` = `adminusers`.`userId`)
				WHERE (`userroles`.`roleId` = ?
				    AND `adminusers`.`deleted` <> 1)";

		$query = $this->db->query($sql, array($role_id));
		return $query->row_array();
	}

	/**
	 * overview : lock roles
	 *
	 * detail : lock rolefunctions in rolefunctions table
	 * @param   int  $role_id
	 * @param   int  $lock
	 * @return  void
	 */
	public function lockRoles($role_id, $lock) {
		$data = array(
			'status' => $lock,
		);

		$this->db->where('roleId', $role_id);
		$this->db->update('roles', $data);
	}

	/**
	 * overview : get roleId or User from userroles
	 *
	 * @param    int   	$user_id
	 * @return   string
	 */
	public function getRoleIdOfUser($user_id) {

		$sql = "SELECT roleId FROM userroles WHERE userId = ?";

		$query = $this->db->query($sql, array($user_id));

		$result = $query->row_array();

		return $result['roleId'];
	}

	/**
	 * overview : get max generation of User role id
	 *
	 * @param    int   	$role_id
	 * @return   string
	 */
	public function getMaxGenerationOfA($role_id) {

		$sql = "SELECT MAX(generation) as generation_max FROM genealogy WHERE roleId = ?";

		$query = $this->db->query($sql, array($role_id));

		$result = $query->row_array();

		return $result['generation_max'];
	}

	/**
	 * overview : get generation of User role id
	 *
	 * @param    int    $role_id
	 * @return   array
	 */
	public function getGenerationOfA($role_id) {

		$sql = "SELECT gene FROM genealogy WHERE roleId = ? ORDER BY gene ASC";

		$query = $this->db->query($sql, array($role_id));

		return $query->result_array();
	}

	/**
	 * overview : add generation in genealogy table
	 *
	 * @param   int   $role_id
	 * @param   int   $generationOfB
	 * @param   int   $geneOfB is user id
	 * @return	array
	 */
	public function addToGenealogy($role_id, $generationOfB, $geneOfB) {
		$array = array(
			'roleId' => $role_id,
			'generation' => $generationOfB,
			'gene' => $geneOfB,
		);

		$this->db->insert('genealogy', $array);
	}

	/**
	 * overview : delete role in genealogy table
	 *
	 * @param   int   $role_id
	 * @return  void
	 */
	public function deleteGenealogy($role_id) {
		$this->db->delete('genealogy', array('roleId' => $role_id));
	}

	/**
	 * overview : get roleid of child of current role
	 *
	 * @param   int   $role_id
	 * @return  array
	 */
	public function getRoleIdByRoleId($role_id) {
		$sql = "SELECT g.roleId FROM roles as r INNER JOIN genealogy as g ON r.roleId = g.gene WHERE r.roleId = ?";
		$query = $this->db->query($sql, array($role_id));
		return $query->result_array();
	}

	/**
	 * overview : add new functions to function table
	 *
	 * @param  string   $funcCode
	 * @param  string  	$funcName
	 * @param  int  	$funcId
	 * @param  int 		$parentId
	 * @param  boolean  $addToAllAdmin
	 * @return insert data to function table
	 */
	private function initFunction($funcCode, $funcName, $funcId, $parentId, $addToAllAdmin = false, $default_enabled=false) {
		//function
		$sql = "SELECT funcCode FROM functions where funcId = ?";
		$query = $this->db->query($sql, array($funcId));
		$result = $query->row_array();

		if (!$result) {
			$data = array(
				'funcId' => $funcId,
				'funcName' => $funcName,
				'parentId' => $parentId,
				'funcCode' => $funcCode,
				'createTime' => $this->utils->getNowForMysql(),
				'sort' => $funcId,
                'status' => self::DB_TRUE
			);

			$this->db->insert('functions', $data);
		}

		# Find superAdminRoleIds
		$superAdminRoleIds = array();
		if ($addToAllAdmin) {
			$query = $this->db->get_where("roles", array("isAdmin" => 1));
			foreach ($query->result() as $row) {
				$superAdminRoleIds[] = $row->roleId;
			}
		}
		//admin
		$superAdminRoleIds[] = 1;
		//unique
		$superAdminRoleIds = array_unique($superAdminRoleIds);

		// $this->db->trans_start();
		foreach ($superAdminRoleIds as $superAdminRoleId) {
			//rolefunctions
			$sql = "SELECT roleId,funcId FROM rolefunctions where roleId = ? and funcId = ?";
			$query = $this->db->query($sql, array($superAdminRoleId, $funcId));
			$result = $query->row_array();

			if (empty($result)) {
				$this->db->insert('rolefunctions', array('roleId' => $superAdminRoleId, 'funcId' => $funcId));
			}

			//rolefunctions_giving
			$sql = "SELECT roleId,funcId FROM rolefunctions_giving where roleId = ? and funcId = ?";
			$query = $this->db->query($sql, array($superAdminRoleId, $funcId));
			$result = $query->row_array();

			if (empty($result)) {
				$this->db->insert('rolefunctions_giving', array('roleId' => $superAdminRoleId, 'funcId' => $funcId));
			}
		}

		if($default_enabled){
			$this->db->from("roles");//->where('status', 0);
			$rows=$this->runMultipleRowArray();

			foreach ($rows as $row) {
				$roleId=$row['roleId'];
				//$sql = "SELECT roleId,funcId rolefunctions rolefunctions_giving where roleId = ? and funcId = ?";
				$this->db->from('rolefunctions')->where('roleId', $roleId)
					->where('funcId', $funcId);
				$id=$this->runOneRowOneField('id');
				if(empty($id)){
					//insert
					$this->db->insert('rolefunctions', array('roleId' => $roleId, 'funcId' => $funcId));
				}

				// $this->db->from('rolefunctions_giving')->where('roleId', $roleId)
				// 	->where('funcId', $funcId);
				// $id=$this->runOneRowOneField('id');
				// if(empty($id)){
				// 	//insert
				// 	$this->db->insert('rolefunctions_giving', array('roleId' => $roleId, 'funcId' => $funcId));
				// }

			}

		}
		// $this->db->trans_complete();
	}

	public function set_default_enabled($funcId){

		$this->db->from("roles");//->where('status', 0);
		$rows=$this->runMultipleRowArray();

		foreach ($rows as $row) {
			$roleId=$row['roleId'];
			//$sql = "SELECT roleId,funcId rolefunctions rolefunctions_giving where roleId = ? and funcId = ?";
			$this->db->from('rolefunctions')->where('roleId', $roleId)
				->where('funcId', $funcId);
			$id=$this->runOneRowOneField('id');
			if(empty($id)){
				//insert
				$this->db->insert('rolefunctions', array('roleId' => $roleId, 'funcId' => $funcId));
			}

			$this->db->from('rolefunctions_giving')->where('roleId', $roleId)
				->where('funcId', $funcId);
			$id=$this->runOneRowOneField('id');
			if(empty($id)){
				//insert
				$this->db->insert('rolefunctions_giving', array('roleId' => $roleId, 'funcId' => $funcId));
			}

		}

	}

	/**
	 * overview : delete function
	 *
	 * @param  int 	$funcId
	 * @return void
	 */
	private function deleteFunction($funcId) {
		$this->db->delete('functions', array('funcId' => $funcId));
		$this->db->delete('rolefunctions', array('funcId' => $funcId));
		$this->db->delete('rolefunctions_giving', array('funcId' => $funcId));

	}

	/**
	 * overview : get functions by user id
	 *
	 * @param  int    $adminUserId
	 * @return array
	 */
	public function getFunctionsByUserId($adminUserId) {
		// $this->load->model(array('rolesfunctions'));

		$role_id = $this->getRoleIdOfUser($adminUserId);

		$rolesfunctions = $this->getRoleFunctionsById($role_id);
		$functions = array();

		foreach ($rolesfunctions as $value) {
			array_push($functions, $value['funcCode']);
		}

		return $functions;
	}

	/**
	 * overview : get roles by func id
	 *
	 * @param  int   $funcId
	 * @return array
	 */
	public function getRolesByFuncId($funcId) {
		$this->db->from('rolefunctions')->where('funcId', $funcId);
		$rows = $this->runMultipleRowArray();
		if (!empty($rows)) {
			$arr = [];
			foreach ($rows as $row) {
				$arr[] = $row['roleId'];
			}
			return $arr;
		}

		return null;
	}

	/**
	 * overview : get roles by giving func id
	 *
	 * @param  int   $funcId
	 * @return array
	 */
	public function getRolesByGivingFuncId($funcId) {
		$this->db->from('rolefunctions_giving')->where('funcId', $funcId);
		$rows = $this->runMultipleRowArray();
		if (!empty($rows)) {
			$arr = [];
			foreach ($rows as $row) {
				$arr[] = $row['roleId'];
			}
			return $arr;
		}

		return null;
	}

	/**
	 * overview : check if role id is admin role
	 *
	 * @param  int    $roleId
	 * @return boolean
	 */
	public function isAdminRole($roleId) {
		if ($roleId) {
			$this->db->from('roles')->where(["isAdmin" => 1, "roleId" => $roleId]);
			return $this->runExistsResult();
		} else {
			return false;
		}
	}

	public function syncAllFunctions(){
		//load permissions.json

		$jsonFile=APPPATH.'config/permissions.json';

		$json=file_get_contents($jsonFile);
		$permissions=$this->utils->decodeJson($json);
		$now=$this->utils->getNowForMysql();

		if(empty($permissions)){
			throw new Exception('wrong permissions file');
		}
		foreach ($permissions as $perm) {
			//search by id
			$this->db->select('funcId')->from('functions')->where('funcId', $perm['funcId']);
			$default_enabled= array_key_exists('default_enabled', $perm) ? $perm['default_enabled'] : false;
			if(!$this->runExistsResult()){
				// $perm['status']=self::DB_TRUE;
				// $perm['sort']=$perm['funcId'];
				// $perm['createTime']=$now;
				// //insert
				// $this->insertData('functions', $perm);
				$funcCode=$perm['funcCode'];
				$funcName=$perm['funcName'];
				$funcId=$perm['funcId'];
				$parentId=$perm['parentId'];
				$addToAllAdmin=true;
				// $default_enabled= @$perm['default_enabled'];

				// $this->utils->debug_log('funcId', $funcId, $default_enabled);
				$this->initFunction($funcCode, $funcName, $funcId, $parentId, $addToAllAdmin, $default_enabled);
			}else{

				//update code and name
				$this->db->where('funcId', $perm['funcId'])->set('parentId', $perm['parentId'])
				    ->set('funcCode', $perm['funcCode'])->set('funcName', $perm['funcName'])
                    ->set('status', self::DB_TRUE)
				    ->update('functions');

			}

			if($default_enabled && $this->utils->isEnabledFeature('set_enabled_permission_all')){
				$funcId=$perm['funcId'];
				$this->set_default_enabled($funcId);
			}

		}

		$syncStandardRoles = $this->syncStandardRoles();

		$this->syncVerificationPermissionsToRoles();

		$this->syncGivingVerificationPermissionsToRoles();

		return $syncStandardRoles;

	}

	public function syncFunctions($roleId, $funcCodes, $addGiving, $isAdmin){
		if(!empty($funcCodes)){

			if(!$isAdmin){
				//delete all
				$this->db->from('rolefunctions')->where('roleId', $roleId)->delete();
				$this->db->from('rolefunctions_giving')->where('roleId', $roleId)->delete();
			}

			foreach ($funcCodes as $funcCode) {
				$this->db->from('functions')->where('funcCode', $funcCode);
				$funcId=$this->runOneRowOneField('funcId');

				if(!empty($funcId)){
					//rolefunctions
					$sql = "SELECT roleId,funcId FROM rolefunctions where roleId = ? and funcId = ?";
					$rows = $this->runRawSelectSQLArray($sql, array($roleId, $funcId));
					// $result = $query->row_array();

					if (empty($rows)) {
						$this->db->insert('rolefunctions', array('roleId' => $roleId, 'funcId' => $funcId));
					}

					if($addGiving){

						//rolefunctions_giving
						$sql = "SELECT roleId,funcId FROM rolefunctions_giving where roleId = ? and funcId = ?";
						$rows = $this->runRawSelectSQLArray($sql, array($roleId, $funcId));
						// $result = $query->row_array();

						if (empty($rows)) {
							$this->db->insert('rolefunctions_giving', array('roleId' => $roleId, 'funcId' => $funcId));
						}

					}
				}else{
					$this->utils->debug_log('donot find function code', $funcCode);
				}
			}
		}else{
			$this->utils->debug_log('empty funcCodes role id:'.$roleId);
		}
	}

	public function getAllFuncCodes(){
		$this->db->select('funcCode')->from('functions');
		$rows=$this->runMultipleRowArray();

		return $this->convertArrayRowsToArray($rows, 'funcCode');
	}

	public function insertNewRole($roleInfo){
		//roleId, roleName, createPerson, isAdmin, functions
		$data=[
			'roleId'=> $roleInfo['roleId'],
			'roleName'=> $roleInfo['roleName'],
			'createPerson'=> $roleInfo['createPerson'],
			'isAdmin'=> $roleInfo['isAdmin'],
			'createTime'=> $this->utils->getNowForMysql(),
			'status'=> self::OLD_STATUS_ACTIVE,
		];

		return $this->insertData('roles', $data);
	}

	public function updateRole($roleInfo){
		//roleId, roleName, createPerson, isAdmin, functions
		$data=[
			'roleName'=> $roleInfo['roleName'],
			'createPerson'=> $roleInfo['createPerson'],
			'isAdmin'=> $roleInfo['isAdmin'],
			'createTime'=> $this->utils->getNowForMysql(),
			'status'=> self::OLD_STATUS_ACTIVE,
		];

		$this->db->where('roleId', $roleInfo['roleId'])->set($data);

		return $this->runAnyUpdate('roles');

	}

	public function hashPassword($original_password){
		require_once APPPATH . 'libraries/phpass-0.1/PasswordHash.php';
		$hasher = new PasswordHash('8', TRUE);
		return $hasher->HashPassword($original_password);
	}

	public function syncUser($roleId, $userInfo, $random_password=false, &$pass=null){
		if(!empty($userInfo)){
			$username=$userInfo['username'];
			$this->db->select('userId')->from('adminusers')->where('username', $username);
			$id=$this->runOneRowOneField('userId');
			if(empty($id)){

				$password=null;
				if($random_password){
					$pass=random_string('alnum', 8);
					$password=$this->hashPassword($pass);
				}else{
					$password=$this->hashPassword($userInfo['password']);
				}

				$data=[
					'username'=>$username,
					'password'=> $this->hashPassword($userInfo['password']),
					'realname'=> $userInfo['realname'],
					'department'=> $userInfo['department'],
					'position'=> $userInfo['position'],
					'email'=> $userInfo['email'],
					'position'=> $userInfo['position'],
					'createTime'=> $this->utils->getNowForMysql(),
					'createPerson'=>1,
					'status'=>self::DB_TRUE,
					'maxWidAmt'=>$userInfo['maxWidAmt'],
					'deleted'=>0,
				];

				//insert user
				return $this->insertData('adminusers', $data);

			}else{
				$this->utils->debug_log('user exists', $username);

				if($random_password){
					$pass=random_string('alnum', 8);
					$password=$this->hashPassword($pass);
					if($userInfo['isPassChangeable']){
						//it will update password only
						$this->db->where('userId', $id)->set('password', $password);
						$this->runAnyUpdate('adminusers');
					}
				}

				return $id;
				// $data=[
				// 	'username'=>$username,
				// 	'password'=> $this->hashPassword(),
				// 	'realname'=> $userInfo['realname'],
				// 	'department'=> $userInfo['department'],
				// 	'position'=> $userInfo['position'],
				// 	'email'=> $userInfo['email'],
				// 	'position'=> $userInfo['position'],
				// 	'createTime'=> $this->utils->getNowForMysql(),
				// 	'createPerson'=>1,
				// 	'status'=>self::DB_TRUE,
				// 	'maxWidAmt'=>$userInfo['maxWidAmt'],
				// 	'deleted'=>0,
				// ];
				// //update user
				// $this->db->where('userId', $id)->set($data);
				// $this->runAnyUpdate('adminusers');
			}
		}

	}

	public function syncStandardRoles($random_password=false, &$pass_list=[]){
		$jsonFile=APPPATH.'config/standard_roles.json';

		$json=file_get_contents($jsonFile);
		$roles=$this->utils->decodeJson($json);
		$now=$this->utils->getNowForMysql();

		if(empty($roles)){
			throw new Exception('wrong roles file');
		}

		$pass_list=[];

		foreach ($roles as $roleName => &$roleInfo) {
			//check role name first
			$roleId=$roleInfo['roleId'];
			$roleInfo['roleName']=$roleName;
			//check by id
			$this->db->select('roleId')->from('roles')->where('roleId', $roleId);
			$rId=$this->runOneRowOneField('roleId');
			if(empty($rId)){
				//insert it
				$this->insertNewRole($roleInfo);
			}else{
				//update it
				$this->updateRole($roleInfo);
			}

			if($roleInfo['isAdmin']=='1'){
				$funcCodes=$this->getAllFuncCodes();
			}else{
				$funcCodes=$roleInfo['functions'];
			}

			$this->syncFunctions($roleId, $funcCodes, $roleInfo['addGiving'], $roleInfo['isAdmin']);

            # fix missing users in viewUsers page
          	$genealogyTbl = 'genealogy';
          	$this->db->select('genealogyId')->from($genealogyTbl)->where('roleId', $roleId);
          	$genealogyId = $this->runOneRowOneField('genealogyId');

          	$data=[
              	'generation' => 1,
              	'gene'=> $roleId,
              	'roleId'=> $roleId
          	];

          	if(empty($genealogyId)){
          		$this->insertData($genealogyTbl, $data);
          	}else{
          	 	$this->updateData('genealogyId', $genealogyId, $genealogyTbl, $data);
          	}

          	if(isset($roleInfo['users'])){
				//add users
          		$users=$roleInfo['users'];
          		foreach ($users as $userInfo) {

          			$userId = $this->syncUser($roleId, $userInfo, $random_password, $pass);
          			if($pass && $userInfo['isPassChangeable']){
          				$pass_list[$userInfo['username']]=$pass;
          			}

          			if(!empty($userId)){
          				$userRolesTbl = 'userroles';
          				$this->db->select('id')->from($userRolesTbl)->where('roleId', $roleId)->where('userId', $userId);
          				$userRoleId=$this->runOneRowOneField('id');
          				$data=[
          				'userId'=> $userId,
          				'roleId'=> $roleId
          				];
          				if(empty($userRoleId)){
          					$this->insertData($userRolesTbl, $data);
          				}else{
          					$this->updateData('id', $userRoleId, $userRolesTbl, $data);
          				}
          			}
          		}
          	}
		}

        $this->syncStandardRolesToExistingUsers();

		return true;
	}

     public function syncStandardRolesToExistingUsers(){

        // Admin Role Management function id = 300;
        // Role Management function id = 2;
        $func_ids_arr = [300,2];
        #get userid and roleids with the roles below
        $this->db->select('userroles.*,au.username,roles.roleName');
        $this->db->join("userroles","userroles.userId = au.userId");
        $this->db->join("rolefunctions","rolefunctions.roleId = userroles.roleId");
        $this->db->join("functions","functions.funcid = rolefunctions.funcid");
        $this->db->join("roles","roles.roleId = userroles.roleId");
        $this->db->where_in("functions.funcId", $func_ids_arr);
        $this->db->group_by('userroles.userId');
        $result = $this->db->get('adminusers as au');

        $users = $result->result_array();
        #end

        $viewAdminUsersFunctionId = $this->db->select("funcId")->where("funcCode","view_admin_users")->get("functions");
        $viewAdminUsersFunctionId = $viewAdminUsersFunctionId->row('funcId');

        $rolesFunctionTbl = 'rolefunctions';
        $rolesFunctionGivingTbl = 'rolefunctions_giving';

        #sync to roles func table
        foreach ($users as $key => $userData) {
            $roleid = $userData['roleId'];

            $this->db->select('id')->from($rolesFunctionTbl)->where('roleId', $roleid)->where('funcId', $viewAdminUsersFunctionId);
            $roleFunctionId=$this->runOneRowOneField('id');

            $data=[
                'roleId'=> $roleid,
                'funcId'=> $viewAdminUsersFunctionId
            ];

            #sync to rolefunctions table
            if(empty($roleFunctionId)){
                $this->insertData($rolesFunctionTbl, $data);
            }

            $this->db->select('id')->from($rolesFunctionGivingTbl)->where('roleId', $roleid)->where('funcId', $viewAdminUsersFunctionId);
            $roleFunctionGivingId=$this->runOneRowOneField('id');

            #sync to rolefunctions_giving table
            if(empty($roleFunctionGivingId)){
                $this->insertData($rolesFunctionGivingTbl, $data);
            }

        }

        # fix missing users in viewUsers page
        $genealogyTbl = 'genealogy';
        $this->db->select('genealogyId')->from($genealogyTbl)->where('roleId', $viewAdminUsersFunctionId);
        $genealogyId = $this->runOneRowOneField('genealogyId');

        $data=[
            'generation' => 1,
            'gene'=> $viewAdminUsersFunctionId,
            'roleId'=> $viewAdminUsersFunctionId
        ];

        if(empty($genealogyId)){
            $this->insertData($genealogyTbl, $data);
        }else{
            $this->updateData('genealogyId', $genealogyId, $genealogyTbl, $data);
        }

    }

	public function addRoleWithFunction($role_name, $isAdmin, $loggedRoleId, $createPerson, $funcIds, $funcGivingIds, &$error,
			$funcParentIds, $funcParentGiveIds, &$newRoleId=null) {
		$success=true;
		//update role info
		$data=[
			'roleName' => $role_name,
			'isAdmin' => $isAdmin,
			'createTime' => $this->utils->getNowForMysql(),
			'createPerson' => $createPerson,
			'status' => self::OLD_STATUS_ACTIVE,
		];
		// $this->db->insert('roles', $data);
		$newRoleId=$this->insertData('roles', $data);

		// $this->addGenealogy($newRoleId, $loggedRoleId);
		//gene is role
		$geneOfB = $loggedRoleId;//instead of using role_id i changed it to user_id to avoid conflict displaying of data
		//level
		$maxGenerationOfA = $this->getMaxGenerationOfA($geneOfB);
		//level of generation
		$generationOfB = $maxGenerationOfA + 1;
		// $generationOfAList = $this->getGenerationOfA($geneOfB);
		$sql = "SELECT gene FROM genealogy WHERE roleId = ? ORDER BY generation ASC";
		$query = $this->db->query($sql, array($geneOfB));
		$generationOfAList= $query->result_array();

		$data = array(
			'roleId' => $newRoleId,
			'generation' => $generationOfB,
			'gene' => $geneOfB,
		);

		$this->db->insert('genealogy', $data);
		for ($i = ($maxGenerationOfA - 2); $i >= 0; $i--) {
			$data = array(
				'roleId' => $newRoleId,
				'generation' => ($i + 2),
				'gene' => $generationOfAList[$i]['gene'],
			);

			$this->db->insert('genealogy', $data);
			// $this->addToGenealogy($newRoleId, ($i + 2), $generationOfA[$i]['gene']);
		}

		// $this->addToGenealogy($role_id, $generationOfB, $geneOfB);

		// $data = array(
		// 	'roleId' => $role_id,
		// 	'generation' => $generationOfB,
		// 	'gene' => $geneOfB,
		// );
		// $this->db->insert('genealogy', $data);

		$success=$this->grantFunctionToRole($newRoleId, $funcIds, $funcGivingIds, null, $funcParentIds, $funcParentGiveIds);

		return $success;

	}

	public function updateRoleWithFunction($role_id, $role_name, $isAdmin, $funcIds, $funcGivingIds, &$error, $visibleFuncIds, $funcParentIds, $funcParentGiveIds) {
		$success=true;
		//update role info
		$data=[
			'roleName'=>$role_name,
			'isAdmin'=>$isAdmin,
		];
		$this->db->where('roleId', $role_id);
		$this->db->update('roles', $data);
		$success=$this->grantFunctionToRole($role_id, $funcIds, $funcGivingIds, $visibleFuncIds, $funcParentIds, $funcParentGiveIds);

		return $success;
	}

	public function grantFunctionToRole($role_id, $funcIds, $funcGivingIds, $visibleFuncIds=null, $funcParentIds=null, $funcParentGiveIds=null){
		$success=true;

        $previousFunctions = $this->getRoleFunctionsById($role_id);
        $previousFuncIds = array_column($previousFunctions, 'funcId');

        $unselectedFuncIds = []; //latest un-selected function ids

        if(in_array(self::ROLE_WITHDRAW_PENDING_REQUEST, $funcIds) && in_array(self::ROLE_WITHDRAW_PENDING_REVIEW, $funcIds) && !in_array(self::ROLE_APPROVE_DECLINE_WITHDRAW, $funcIds)){
            array_push($funcIds, self::ROLE_APPROVE_DECLINE_WITHDRAW);
        }
        if(in_array(self::ROLE_APPROVE_DECLINE_WITHDRAW, $funcIds) && !in_array(self::ROLE_WITHDRAW_PENDING_REQUEST, $funcIds)){
            array_push($funcIds, self::ROLE_WITHDRAW_PENDING_REQUEST);
        }
        if(in_array(self::ROLE_APPROVE_DECLINE_WITHDRAW, $funcIds) && !in_array(self::ROLE_WITHDRAW_PENDING_REVIEW, $funcIds)){
            array_push($funcIds, self::ROLE_WITHDRAW_PENDING_REVIEW);
        }

        $selectedParentIds = explode(',', $funcParentIds);
        foreach($selectedParentIds as $parent){
            if(!empty($parent) && !in_array($parent, $funcIds)){
                array_push($funcIds, $parent);
            }
        }
        $selectedFuncIds = array_unique($funcIds); //latest selected function ids
        $unselectedParentIds = array();
        if(!empty($visibleFuncIds)){
            foreach($visibleFuncIds as $vis){ //to get the unselected functions
                if(!in_array($vis, $selectedFuncIds)  ){
                    if(in_array($vis, array_column($previousFunctions, 'parentId'))){ // check if parent function
                        $unselectedParentIds[] = $vis;
                    }else{
                        $unselectedFuncIds[] = $vis;
                    }
                }
            }
            foreach ($previousFunctions as $val) {
                if(!in_array($val['funcId'], $unselectedFuncIds) && in_array($val['parentId'], $unselectedParentIds)){
                    if (($key = array_search($val['parentId'], $unselectedParentIds)) !== false) {
                        unset($unselectedParentIds[$key]);
                    }
                }
            }
            $unselectedFuncIds = array_merge($unselectedParentIds, $unselectedFuncIds);
        }

        /*$this->utils->debug_log('the $previousFuncIds ---->', $previousFuncIds);
        $this->utils->debug_log('the $unselectedParent ---->', $unselectedParentIds);
        $this->utils->debug_log('the $selectedParentIds ---->', $selectedParentIds);
        $this->utils->debug_log('the $selectedFuncIds ---->', $selectedFuncIds);
        $this->utils->debug_log('the $unselectedFuncIds ---->', $unselectedFuncIds);*/

        foreach ($selectedFuncIds as $sel){ // insert new selected function
            if(!in_array($sel, $previousFuncIds)){
                $data = array(
                    'roleId' => $role_id,
                    'funcId' => $sel,
                );
                $this->db->insert('rolefunctions', $data);
            }
        }
        foreach ($unselectedFuncIds as $uns){ // delete the not selected function
            if(in_array($uns, $previousFuncIds)){
                $data = array(
                    'roleId' => $role_id,
                    'funcId' => $uns,
                );
                $this->db->delete('rolefunctions', $data);
            }
        }
		//process functions
		/*$this->db->delete('rolefunctions', array('roleId' => $role_id));
		$this->db->delete('rolefunctions_giving', array('roleId' => $role_id));

		//append parent id to func id
		$this->db->distinct()->select('parentId')->from('functions')->where_in('funcId', $funcIds);
		$parentIds=$this->runMultipleRowArray();
		if(!empty($parentIds)){
			foreach($parentIds as $row){
				$funcIds[]= $row['parentId'];
			}
		}

		//unique function id
		$funcIds=array_unique($funcIds);

		foreach ($funcIds as $value) {
			$data = array(
				'roleId' => $role_id,
				'funcId' => $value,
			);

			$this->db->insert('rolefunctions', $data);
		}*/

		if($this->checkIfHasPermissionRole($funcIds) && !empty($funcGivingIds)){

            $previousFuncGivIds = array_column($this->getAllFunctionsGiving($role_id), 'funcId');
            $unselectedFuncGivIds = [];

            if(in_array(self::ROLE_WITHDRAW_PENDING_REQUEST, $funcGivingIds) || in_array(self::ROLE_WITHDRAW_PENDING_REVIEW, $funcGivingIds) && !in_array(self::ROLE_APPROVE_DECLINE_WITHDRAW, $funcGivingIds)){
                array_push($funcGivingIds, self::ROLE_APPROVE_DECLINE_WITHDRAW);
            }
            if(in_array(self::ROLE_APPROVE_DECLINE_WITHDRAW, $funcGivingIds) && !in_array(self::ROLE_WITHDRAW_PENDING_REQUEST, $funcGivingIds)){
                array_push($funcGivingIds, self::ROLE_WITHDRAW_PENDING_REQUEST);
            }
            if(in_array(self::ROLE_APPROVE_DECLINE_WITHDRAW, $funcGivingIds) && !in_array(self::ROLE_WITHDRAW_PENDING_REVIEW, $funcGivingIds)){
                array_push($funcGivingIds, self::ROLE_WITHDRAW_PENDING_REVIEW);
            }

            $selectedParentIdsGiving = explode(',', $funcParentGiveIds);
            foreach($selectedParentIdsGiving as $parent){
                if(!empty($parent) && !in_array($parent, $funcGivingIds)){
                    array_push($funcGivingIds, $parent);
                }
            }
            $selectedFuncGivIds = array_unique($funcGivingIds);

            foreach($previousFuncGivIds as $prev){
                if(!in_array($prev, $selectedFuncGivIds)){
                    $unselectedFuncGivIds[] = $prev;
                }
            }
            /*$this->utils->debug_log('the $previousFuncGivIds ---->', $previousFuncGivIds);
            $this->utils->debug_log('the $selectedParentIdsGiving ---->', $selectedParentIdsGiving);
            $this->utils->debug_log('the $selectedFuncGivIds ---->', $selectedFuncGivIds);
            $this->utils->debug_log('the $unselectedFuncGivIds ---->', $unselectedFuncGivIds);*/

			//append parent id to func id
			/*$this->db->distinct()->select('parentId')->from('functions')->where_in('funcId', $funcGivingIds);
			$parentIds=$this->runMultipleRowArray();
			if(!empty($parentIds)){
				foreach($parentIds as $row){
					$funcGivingIds[]= $row['parentId'];
				}
			}

			//unique function id
			$funcGivingIds=array_unique($funcGivingIds);

			foreach ($funcGivingIds as $value) {
				$data = array(
					'roleId' => $role_id,
					'funcId' => $value,
				);

				$this->db->insert('rolefunctions_giving', $data);
			}*/

            foreach ($selectedFuncGivIds as $sel){
                if(!in_array($sel, $previousFuncGivIds)){
                    $data = array(
                        'roleId' => $role_id,
                        'funcId' => $sel,
                    );
                    $this->db->insert('rolefunctions_giving', $data);
                }
            }
            foreach ($unselectedFuncGivIds as $uns){
                if(in_array($uns, $previousFuncGivIds)){
                    $data = array(
                        'roleId' => $role_id,
                        'funcId' => $uns,
                    );
                    $this->db->delete('rolefunctions_giving', $data);
                }
            }
		}

		return $success;
	}

	public function checkIfHasPermissionRole($functions) {

		foreach ($functions as $value) {
			//role management id
			if ($value == self::ROLE_MANAGEMENT_FUNCTION) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Grant permissions VERIFY_PHONE_INFO and VERIFY_EMAIL_INFO to roles that
	 * currently has access to VERIFY_PHONE_AND_EMAIL_INFO permission.
	 *
	 * @return boolean
	 * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
	 */
	public function syncVerificationPermissionsToRoles(){


		// -- get roles that has the original permission
		$this->startTrans();

		$roles_with_verification_permission = $this->getRolesByFuncId(self::ROLE_VERIFY_PHONE_AND_EMAIL_INFO);

		$data = array();

		foreach ($roles_with_verification_permission as $role_key => $role_id) {

			// -- get all permissions of each role
			$rolesfunctions = $this->getRoleFunctionsById($role_id);
			$functions = array();

			// -- get function codes of the roles
			foreach ($rolesfunctions as $value) {
				array_push($functions, $value['funcId']);
			}

			// -- if the role has no permission to verify phone info, grant it
			if(!in_array(self::ROLE_VERIFY_PLAYER_CONTACT_NUMBER, $functions))
			{
				$data[] = array(
                    'roleId' => $role_id,
                    'funcId' => self::ROLE_VERIFY_PLAYER_CONTACT_NUMBER,
                );
			}

			// -- if the role has no permission to verify email info, grant it
			if(!in_array(self::ROLE_VERIFY_PLAYER_EMAIL, $functions))
			{
				$data[] = array(
                    'roleId' => $role_id,
                    'funcId' => self::ROLE_VERIFY_PLAYER_EMAIL,
                );
			}
		}

		if(!empty($data))
				$this->db->insert_batch('rolefunctions', $data);

		return $this->endTransWithSucc();
	}

	/**
	 * Grant to give permissions VERIFY_PHONE_INFO and VERIFY_EMAIL_INFO to roles that
	 * currently has access to give VERIFY_PHONE_AND_EMAIL_INFO permission.
	 *
	 * @return boolean
	 * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
	 */
	public function syncGivingVerificationPermissionsToRoles()
	{

		/**
		 * Allow giving permission VERIFY_PHONE_INFO and VERIFY_EMAIL_INFO to roles
		 * who can give permission to VERIFY_PHONE_AND_EMAIL_INFO
		 */

		$this->startTrans();

		$roles_with_giving_verification_permission = $this->getRolesByGivingFuncId(self::ROLE_VERIFY_PHONE_AND_EMAIL_INFO);

		$data = array();

		foreach ($roles_with_giving_verification_permission as $role_key => $role_id) {

			// -- get all permissions of each role
			$rolesfunctions = $this->getRoleFunctionsGivingById($role_id);
			$functions = array();

			// -- get giving function codes of per role
			foreach ($rolesfunctions as $value) {
				array_push($functions, $value['funcId']);
			}

			// -- if the role can not give permission to verify phone info, grant it
			if(!in_array(self::ROLE_VERIFY_PLAYER_CONTACT_NUMBER, $functions))
			{
				$data[] = array(
                    'roleId' => $role_id,
                    'funcId' => self::ROLE_VERIFY_PLAYER_CONTACT_NUMBER,
                );
			}

			// -- if the role can not give permission to verify email info, grant it
			if(!in_array(self::ROLE_VERIFY_PLAYER_EMAIL, $functions))
			{
				$data[] = array(
                    'roleId' => $role_id,
                    'funcId' => self::ROLE_VERIFY_PLAYER_EMAIL,
                );
			}

		}

		if(!empty($data))
			$this->db->insert_batch('rolefunctions_giving', $data);

		return $this->endTransWithSucc();
	}

	public function getRoleMap() {
		$roleMap=[];
		$this->db->select('roleId,roleName')->from('roles');
		$rows=$this->runMultipleRowArrayUnbuffered();
		if(!empty($rows)){
			foreach ($rows as $row) {
				$roleMap[$row['roleName']]=$row['roleId'];
			}
		}
		unset($rows);
		return $roleMap;
	}

	public function getRoleNameById($role_id) {
		$this->db->select('roleName')->from('roles')->where('roleId', $role_id);
		return $this->runOneRowOneField('roleName');
	}

	public function getRoleIdByName($roleName) {
		$this->db->select('roleId')->from('roles')->where('roleName', $roleName);
		return $this->runOneRowOneField('roleId');
	}

}

/* End of file roles.php */
/* Location: ./application/models/roles.php */
