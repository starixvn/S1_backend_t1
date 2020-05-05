<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}
class Lottery_bo_roles {

	protected $api;
	protected $ret;
	protected $platform_code = T1LOTTERY_API;
	protected $debug = false;
	protected $roles;

	public function __construct() {
		$this->CI = & get_instance();
		$this->CI->load->library(['authentication']);
		$this->CI->load->model(['users']);
		$this->load_api();
		if ($this->api_ready()) {
			$this->roles_fetch();
		}
	}

	protected function default_return() {
		return [ 'code' => 255, 'mesg' => 'exec_incomplete', 'result' => null ];
	}

	protected function exception_to_return($ex, $ret) {
		$ret['code'] = $ex->getCode();
		$ret['mesg'] = $ex->getMessage();
		return $ret;
	}

	public function load_api() {
		$ret = [ 'code' => 255, 'mesg' => 'exec_incomplete' ];
		try {
			$this->api = $this->CI->utils->loadExternalSystemLibObject(T1LOTTERY_API);

			if (empty($this->api)) {
				throw new Exception(lang('LBO Binding: Cannot load Lottery API'), 0x100);
			}

			$ret = [ 'code' => 0, 'mesg' => 'successful' ];
		}
		catch (Exception $ex) {
			// $ret = [ 'code' => $ex->getCode(), 'mesg' => $ex->getMessage() ];

			$ret = $this->exception_to_return($ex, $ret);
		}
		finally {
			return $ret;
		}
	}

	protected function api_ready()
		{ return !empty($this->api); }

	public function roles_fetch($refresh = false) {
		if (!$this->api_ready()) {
			return null;
		}
		if (empty($this->roles) || $refresh) {
			$this->api->loadSystemInfo();
			$this->roles = $this->api->getSystemInfo('roles');
		}
		// $this->CI->utils->debug_log('Lottery_bo_roles::roles_fetch', $api_roles);
		if ($this->debug) { $this->CI->utils->debug_log('roles_fetch', $refresh ? 'refreshing' : 'keep', $this->roles); }
		if (empty($this->roles)) {
			$this->roles = [];
		}

		return $this->roles;
	}

	/**
	 * Combine privilege object into 4-digit priv vector
	 * @param	array 	$ar		The privilege assoc array
	 * @return	string	4-dit priv vector
	 */
	public function priv_ar_to_string($ar) {
		// $fields = [ 'marketing', 'games', 'finance', 'admin' ];
		// $priv_str = '';

		// // if admin privilege is other than 0 => skip other privileges
		// if (isset($ar['admin']) && $ar['admin'] > 0) {
		// 	$priv_str = '000' . $ar['admin'];
		// }
		// // or combine them by the sequence as $fields
		// else {
		// 	foreach ($fields as $field) {
		// 		$priv_str .= isset($ar[$field]) ? $ar[$field] : '0';
		// 	}
		// }
		// return $priv_str;

		$fields = [ 'marketing' => 3, 'games' => 2, 'finance' => 4, 'admin' => 1 ];
		foreach ($fields as $f => $v) {
			if ($ar[$f] > 0) {
				return $v;
			}
		}

		return 0;
	}

	public function priv_string_to_ar($priv_str) {
		$priv = intval($priv_str, 10);
		// OGP-7584: The old $priv_vector is no more
		// $priv_vector = [
		// 	'marketing'	=> intval($priv / 1000) ,
		// 	'games'		=> intval($priv / 100) % 10 ,
		// 	'finance'	=> intval($priv / 10) % 10 ,
		// 	'admin'		=> $priv % 10
		// ];
		$priv_vector = [
			'marketing'	=> $priv == 3 ,
			'games'		=> $priv == 2 ,
			'finance'	=> $priv == 4 ,
			'admin'		=> $priv == 1
		];

		return $priv_vector;
	}

	protected function add_prefix($username) {
		$prefix = $this->api->getSystemInfo('prefix_for_bo_username');
		$username_full = "{$prefix}_{$username}";
		return $username_full;
	}

	protected function api_deleteBackOfficeUser($username) {
		$ret = $this->default_return();
		$username_full = $this->add_prefix($username);

		$api_res = $this->api->deleteBackOfficeUser($username_full);

		$this->CI->utils->debug_log("delete invoking", $username_full, 'result', $api_res);

		$success = $api_res['success'];
		unset($api_res['success']);
		unset($api_res['response_result_id']);
		if ($success == false) {
			$error = '';
			if (isset($api_res['error'])) { $error = json_encode($api_res['error']); }
			else if (isset($api_res['errorMessage'])) { $error = $api_res['errorMessage']; }

			$ret = [ 'code' =>  ($api_res['code'] ?: -1) , 'mesg' => "api_error: $error" , 'result' => $api_res ];
		}
		else {
			$ret = [ 'code' =>  0, 'mesg' => null, 'result' => $api_res ];
		}

		return $ret;
	}

	protected function _api_updateOrCreateBackOfficeUser($op, $username, $passwd, $priv, $realname=null) {
		$ret = $this->default_return();
		$username_full = $this->add_prefix($username);

		$op_full = "{$op}BackOfficeUser";
		switch ($op) {
			case 'create' :
				$api_res = $this->api->createBackOfficeUser($username_full, $passwd, $priv);
				break;
			case 'update' :
				$api_res = $this->api->updateBackOfficeUser($username_full, $passwd, $priv);
				break;
			default :
				$ret = [ 'code' => 128 , 'mesg' => 'unknown_op', 'result' => null ];
				break;
		}
		$this->CI->utils->debug_log("{$op_full} invoking", $username_full, $passwd, $priv);
		$this->CI->utils->debug_log("{$op_full} result", $api_res);

		$success = $api_res['success'];
		unset($api_res['success']);
		unset($api_res['response_result_id']);
		if ($success == false) {
			$error = '';
			if (isset($api_res['error'])) { $error = json_encode($api_res['error']); }
			else if (isset($api_res['errorMessage'])) { $error = $api_res['errorMessage']; }

			$ret = [ 'code' =>  $api_res['code'], 'mesg' => "api_error: $error" , 'result' => $api_res ];
		}
		else {
			$ret = [ 'code' =>  0, 'mesg' => null, 'result' => $api_res ];
		}

		return $ret;
	}

	protected function api_createBackOfficeUser($bo_username, $password, $role, $realname=null) {
		return $this->_api_updateOrCreateBackOfficeUser('create', $bo_username, $password, $role, $realname);
	}

	protected function api_updateBackOfficeUser($bo_username, $password, $role, $realname=null) {
		return $this->_api_updateOrCreateBackOfficeUser('update', $bo_username, $password, $role, $realname);
	}

	public function update_password_for_account($username, $passwd) {
		$ret = $this->default_return();
		try {

			$roles = $this->roles_fetch('refresh');

			if (!$this->api_ready()) {
				throw new Exception(lang('LBO Binding: Cannot load Lottery API'), 0x100);
			}

			$priv = $this->roles_get_priv($username);

			// update existing user of privilege
			$this->CI->utils->debug_log('api_updateBackOfficeUser', 'change passwd', [ $username, $passwd, $priv ]);
			$upd_res = $this->api_updateBackOfficeUser($username, $passwd, $priv);
			$ret['result'] = $upd_res['result'];
			$this->CI->utils->debug_log('api_updateBackOfficeUser', 'change passwd result', $ret);
			if ($upd_res['code'] != 0) {
				$this->CI->utils->debug_log('update_Bind: Error occurs when api update user');
				throw new Exception(lang('LBO Binding: Lottery API fails when updating binding'), 0x114);
			}

			$this->roles_update_password_crc_by_stored_plain($username);

			$ret['code'] = 0;
			$ret['mesg'] = null;
		}
		catch (Exception $ex) {
			$ret = $this->exception_to_return($ex, $ret);
		}
		finally {
			return $ret;
		}
	}

	public function update_or_add_account($username, $passwd, $priv, $add_mode = null) {
		$ret = $this->default_return();
		try {

			$roles = $this->roles_fetch('refresh');

			if (!empty($roles) && !isset($roles[$username]) && empty($add_mode)) {
				throw new Exception(lang('LBO Binding: User name unknown'), 0x111);
			}

			if (!$this->api_ready()) {
				throw new Exception(lang('LBO Binding: Cannot load Lottery API'), 0x100);
			}

			if (!empty($add_mode)) {
				// Add binding
				// Try adding bo user first
				$add_res = $this->api_createBackOfficeUser($username, $passwd, $priv);
				$this->CI->utils->debug_log('add_Bind: api create user', [ $username, $passwd, $priv ]);
				$ret['result'] = $add_res['result'];
				$this->CI->utils->debug_log('add_Bind: api create user result', $add_res);
				switch ($add_res['code']) {
					case 0 :
						// Add op successful
						$this->CI->utils->debug_log('add_Bind: api create user successful');
						break;
					case 23 :
						// Account alreay exists, continue with updating
						$this->CI->utils->debug_log('add_Bind: Account exists, continue with api update user');
						// update existing user of privilege
						$upd_res = $this->api_updateBackOfficeUser($username, $passwd, $priv);
						$ret['result'] = $upd_res['result'];
						if ($upd_res['code'] != 0) {
							$this->CI->utils->debug_log("add_Bind", "Error occurs when api update user", 'upd_res', $upd_res);
							throw new Exception(lang("LBO Binding: Lottery API fails when adding binding") . " ({$upd_res['code']})", 0x112);
						}
						break;
					default :
						$this->CI->utils->debug_log("add_Bind", "Error occurs when api create user", 'add_res', $add_res);
						throw new Exception(lang("LBO Binding: Lottery API fails when adding binding") . " ({$add_res['code']})", 0x113);
						break;
				}
			}

			if (empty($add_mode)) {
				// update existing user of privilege
				$upd_res = $this->api_updateBackOfficeUser($username, $passwd, $priv);
				$ret['result'] = $upd_res['result'];
				if ($upd_res['code'] != 0) {
					$this->CI->utils->debug_log('update_Bind: Error occurs when api update user');
					throw new Exception(lang("LBO Binding: Lottery API fails when updating binding") . " ({$upd_res['code']})", 0x114);
				}
			}

			$this->roles_update_priv($username, $priv);
			$this->roles_update_password_crc_by_stored_plain($username);

			$ret['code'] = 0;
			$ret['mesg'] = null;
		}
		catch (Exception $ex) {
			$ret = $this->exception_to_return($ex, $ret);
		}
		finally {
			return $ret;
		}
	}

	public function delete_account($usernames) {
		$ret = $this->default_return();
		try {

			if (!$this->api_ready()) {
				throw new Exception(lang('LBO Binding: Cannot load Lottery API'), 0x100);
			}

			$errors = [];
			$roles = $this->roles_fetch('refresh');
			if (empty($roles) || count($roles) == 0) {
				throw new Exception(lang('LBO Binding: Cannot delete, roles empty'), 0x121);
			}

			$this->CI->utils->debug_log('usernames', $usernames);
			foreach ($usernames as $uname) {
				$this->CI->utils->debug_log('Lottery_bo_roles::delete_account', 'roles', $roles, 'uname', $uname);
				if (!$this->roles_user_exists($uname)) {
					$errors[] = [ 'username' => $uname, 'mesg' => 'username_not_found_in_roles' ];
					continue;
				}

				$del_res = $this->api_deleteBackOfficeUser($uname);

				if ($del_res['code'] == 0) {
					unset($roles[$uname]);
				}
				else {
					$errors[] = $del_res['mesg'];
				}
			}

			$this->roles_update($roles);

			if (count($errors) > 0) {
				$ret['status'] = false;
				$ret['code'] = 1;
				$ret['mesg'] = $errors;
			}
			else {
				$ret['status'] = true;
				$ret['code'] = 0;
				$ret['mesg'] = null;
			}
		}
		catch (Exception $ex) {
			$ret = $this->exception_to_return($ex, $ret);
		}
		finally {
			return $ret;
		}
	}

	/**
	 * Setpasswordplain wrapper
	 * @param	string	$password_plain		The password plaintext
	 *
	 * @return	true if success; false if there is no user currently logged in
	 */
	public function setPasswordPlain($password_plain) {
		$user_id = $this->CI->authentication->getUserId();
		if (empty($user_id)) return false;
		$username = $this->CI->users->getUsernameById($user_id);

		$this->CI->users->setPasswordPlain($user_id, $password_plain);
		if ($this->roles_user_exists_by_user_id($user_id) && !$this->roles_by_userid_does_password_match($user_id, $password_plain)) {
			$this->update_password_for_account($username, $password_plain);
		}
		return true;
	}

	/**
	 * Gets password plaintext for current adminuser
	 *
	 * @return	string	The password plaintext if success; false if no user logged in
	 */
	public function getPasswordPlain($user_id) {
		$user_id = $this->CI->authentication->getUserId();
		if (empty($user_id)) return false;
		return $this->CI->users->getPasswordPlain($user_id);
	}

	public function getPasswordPlainByUsername($username) {
		$user_id = $this->CI->users->getIdByUsername($username);
		return $this->getPasswordPlain($user_id);
	}

	public function roles_user_exists($username, $also_check_priv = false) {
		$res = array_key_exists($username, $this->roles);
		if ($also_check_priv) {
			$pv = $this->roles_get_priv($username);
			$res = $res && ( $pv > 0 );
		}
		return $res;
	}

	public function roles_user_exists_by_user_id($user_id, $also_check_priv = false) {
		$username = $this->CI->users->getUsernameById($user_id);
		return $this->roles_user_exists($username, $also_check_priv);
	}

	public function roles_get_user($username) {
		return $this->roles[$username];
	}

	public function roles_get_priv($username) {
		$user = $this->roles_get_user($username);
		return $user['pv'];
	}

	public function roles_get_password_crc($username) {
		$user = $this->roles_get_user($username);
		return $user['pc'];
	}

	public function roles_does_password_match($username, $password_to_match) {
		$crc_to_match = $this->crc($password_to_match);
		$current_crc = $this->roles_get_password_crc($username);
		if ($current_crc == $crc_to_match) {
			return true;
		}
		$this->CI->utils->debug_log('roles_does_password_match', 'password changed', $username, $password_to_match, $crc_to_match, $current_crc);
		return false;
	}

	public function roles_by_userid_does_password_match($user_id, $password_to_match) {
		$username = $this->CI->users->getUsernameById($user_id);

		return $this->roles_does_password_match($username, $password_to_match);
	}

	public function roles_by_userid_update_password_crc($user_id, $password_plain) {
		$username = $this->CI->users->getUsernameById($user_id);
		return $this->roles_update_password_crc($username, $password_plain);
	}

	public function roles_update_password_crc($username, $password_plain) {
		$roles = $this->roles_fetch('refresh');

		if ($this->debug)
			{ $this->CI->utils->debug_log('roles_update_password_crc', 'roles', $roles); }

		if ($this->roles_user_exists($username)) {
			$roles[$username]['pc'] = $this->crc($password_plain);
		}
		else {
			$roles[$username] = [ 'pc' => $this->crc($password_plain) ];
		}
		$this->roles_update($roles);
	}

	public function roles_update_password_crc_by_stored_plain($username) {
		$user_id = $this->CI->users->getIdByUsername($username);
		$password_plain = $this->getPasswordPlain($user_id);
		// if (empty($username)) { $x = $this->authentication->getUsername(); }
		$this->roles_update_password_crc($username, $password_plain);
	}

	public function roles_update_priv($username, $priv) {
		$roles = $this->roles_fetch('refresh');
		if ($this->debug)
			{ $this->CI->utils->debug_log('roles_update_priv', 'roles', $roles); }

		if ($this->roles_user_exists($username)) {
			$roles[$username]['pv'] = $priv;
		}
		else {
			$roles[$username] = [ 'pv' => $priv ];
		}

		$this->roles_update($roles);
	}

	public function roles_update($roles) {
		if ($this->debug) { $this->CI->utils->debug_log('roles_update', 'roles', $roles); }
		$extra_update = [ 'roles' => $roles ];
		$update_res = $this->api->updateExternalSystemExtraInfo($this->platform_code, $extra_update);
		if ($this->debug) { $this->CI->utils->debug_log('roles_update', 'result', $update_res); }
	}

	public function crc($s) {
		return hash('crc32b', $s);
	}


} // End class Lottery_usersync