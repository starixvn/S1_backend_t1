<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once 'phpass-0.1/PasswordHash.php';

// define('STATUS_ACTIVATED', '1');
// define('STATUS_NOT_ACTIVATED', '0');

/**
 * Authentication
 *
 * Authentication library for FHI OG System
 *
 * @package		Authentication
 * @author		Johann Merle
 * @version		1.0.0
 */

class Authentication {
	private $error = array();

	const STATUS_ACTIVATED = '1';
	const STATUS_NOT_ACTIVATED = '0';

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array('session', 'language_function', 'history', 'email_setting'));
		$this->ci->load->model(array('users'));

		$this->initiateLang();

		/*date_default_timezone_set('Asia/Manila');*/
	}

	/**
	 * initiate Language
	 *
	 * @return  void
	 */
	public function initiateLang() {
		$lang = $this->ci->language_function->getCurrentLanguage();
		$langCode = $this->ci->language_function->getLanguageCode($lang);
		$language = $this->ci->language_function->getLanguage($lang);
		$this->ci->lang->load($langCode, $language);

		$custom_lange = config_item('custom_lang');
        if((FALSE !== $custom_lange) && (file_exists(APPPATH . 'language/custom/' . $custom_lange . '/' . $language . '/custom_lang.php'))){
            $this->ci->lang->load('custom', 'custom/' . $custom_lange . '/' . $language);
        }

		// $currentUserSessionId = $this->ci->users->getAdminCurrentSession($this->getUserId());
		// //var_dump($this->ci->session->userdata('sessionId'));exit();
		// if (!empty($currentUserSessionId['session'])) {
		// 	if ($this->ci->session->userdata('sessionId') != $currentUserSessionId['session']) {
		// 		$this->logout();
		// 	}
		// }
	}

	public function set_login_user($login) {

		$user = $this->ci->users->getUserByLogin($login);
		if(!empty($user)){
			// if($this->ci->utils->isEnabledFeature('only_allow_one_for_adminuser') &&
			// 		$login!='superadmin'){
			// 	//kickout other
			// 	$rlt=$this->ci->users->kickoutAdminuserByUsername($login);
			// 	if(!$rlt){
			// 		$this->ci->utils->error_log('kickout adminuser failed', $login);
			// 	}
			// }

			// password ok
			$session_id = $this->randomizer($user->username . $user->userId);
			$token = $this->ci->users->updateLoginInfo($user->userId, TRUE, TRUE, $session_id);

			$this->ci->session->set_userdata(array(
				'user_id' => $user->userId,
				'username' => $user->username,
				'status' => self::STATUS_ACTIVATED,
				'sessionId' => $session_id,
				'admin_login_token' => $token,
			));

			$this->ci->session->updateLoginId('admin_id', $user->userId);
		}

		return TRUE;
	}

	/**
	 * Login user on the site. Return TRUE if login is successful
	 * (user exists and password is correct), otherwise FALSE.
	 *
	 * @param	string	(username)
	 * @param	string  (password)
	 * @param	bool
	 * @return	bool
	 */
	public function login($login, $password, $remember_me = '') {
        // $this->ci->session->set_userdata('login_from_admin', '0');

		if ((strlen($login) > 0) AND (strlen($password) > 0)) {

			if (!is_null($user = $this->ci->users->getUserByLogin($login))) {
				// login ok

				// Does password match hash in database?
				$hasher = new PasswordHash('8', TRUE);

				if ($user->deleted=='0' && $hasher->CheckPassword($password, $user->password)) {
					if($this->ci->utils->isEnabledFeature('only_allow_one_for_adminuser') &&
							!$this->ci->users->isT1User($login) ){
						//kickout other
						$rlt=$this->ci->users->kickoutAdminuserByUsername($login);
						if(!$rlt){
							$this->ci->utils->error_log('kickout adminuser failed', $login);
						}
					}

					// password ok
					$session_id = $this->randomizer($user->username . $user->userId);
					$token = $this->ci->users->updateLoginInfo($user->userId, TRUE, TRUE, $session_id);

					$userdata = array(
						'user_id' => $user->userId,
						'username' => $user->username,
						'status' => self::STATUS_ACTIVATED,
						'sessionId' => $session_id,
						'admin_login_token' => $token,
					);

					if( ! empty($remember_me) ) $userdata['remember_me'] = self::STATUS_ACTIVATED;

					$this->ci->session->set_userdata($userdata);

					$this->ci->session->updateLoginId('admin_id', $user->userId);

					return TRUE;

				} else {
					// fail - wrong password
					$this->error = array('password' => 'incorrect password');
				}
			} else {
				// fail - wrong login
				$this->error = array('login' => 'incorrect login details');
			}
		}

		return FALSE;
	}

    public function login_from_admin($login, $password, $remember_me=''){
    //     if(!$this->login($login, $password, $remember_me)){
    //         $this->ci->session->set_userdata('login_from_admin', '0');
    //         return FALSE;
    //     }

    //     $this->ci->session->set_userdata('login_from_admin', '1');

        return TRUE;
    }

	# For a logged in user, validate his password is entered correctly
	# Used in cases like validating password before withdrawal
	public function validatePassword($password) {
		if (!is_null($user = $this->ci->users->getUserByLogin($this->getUsername()))) {
			$hasher = new PasswordHash('8', TRUE);
			return $hasher->CheckPassword($password, $user->password);
		}
		return false;
	}

	/**
	 * Logout user from the site
	 *
	 * @return	void
	 */
	public function logout() {
		$this->ci->utils->debug_log('logout userid', $this->getUserId());

		$data = array(
			'lastLogoutTime' => date("Y-m-d H:i:s"),
		);

		$this->ci->users->setLogout($this->getUserId(), $data);

		$this->ci->session->updateLoginId('admin_id', '');

		$this->ci->session->set_userdata(array('user_id' => '', 'username' => '', 'status' => '', 'remember_me' => ''));

		$this->ci->session->sess_destroy();
	}

	/**
	 * Check if user logged in.
	 *
	 * @param	bool
	 * @return	bool
	 */
	public function isLoggedIn($activated = TRUE) {
		return $this->ci->session->userdata('status') === ($activated ? self::STATUS_ACTIVATED : self::STATUS_NOT_ACTIVATED);
	}

    public function isLoggedInFromAdmin(){
        return TRUE;
    }

	/**
	 * Get user_id
	 *
	 * @return	string
	 */
	public function getUserId() {
		return $this->ci->session->userdata('user_id');
	}

	public function getAdminToken() {
		return $this->ci->session->userdata('admin_login_token');
	}

	/**
	 * Get username
	 *
	 * @return	string
	 */
	public function getUsername() {
		return $this->ci->session->userdata('username');
	}

	/**
	 * Get error message.
	 * Can be invoked after any failed operation such as login or register.
	 *
	 * @return	string
	 */
	public function get_error_message() {
		return $this->error;
	}

	/**
	 * Will randomize alphanumeric and special characters
	 *
	 * @param 	string
	 * @return	string
	 */
	public function randomizer($name) {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
			. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. '0123456789!@#$%^&*()'
			. $name); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$randomPassword = '';
		foreach (array_rand($seed, 9) as $k) {
			$randomPassword .= $seed[$k];
		}

		return $randomPassword;
	}

	public function getPlayerId(){
		return null;
	}

	public function getPlayerToken() {
		return null;
	}

	public function isSuperAdmin(){
		return $this->getUsername()=='superadmin';
	}

	public function login_from_token($token, &$message){
		$success=false;
		if(!empty($token)){
			$this->ci->load->model(['common_token', 'users']);
			$adminUserId=$this->ci->common_token->getAdminUserIdByToken($token);
			$username=$this->ci->users->getUsernameById($adminUserId);
			return $this->login_from_mdb($adminUserId, $username, $message);
		}

		return $success;
	}

	/**
	 * login to active target db
	 * @param  int $loggedUserId
	 * @param  string $loggedUsername
	 * @param  string &$message
	 * @return boolean
	 */
	public function login_from_mdb($loggedUserId, $loggedUsername, &$message){
		$this->ci->load->model(['users']);

		if(!empty($loggedUsername)){
			$user = $this->ci->users->getUserByUsername($loggedUsername);
			$sidebar_status = $this->ci->session->userdata('sidebar_status');
			//auto sync ?
			if(!empty($user)){
				$this->ci->utils->debug_log('login_from_mdb '.$loggedUsername.' active db', $this->ci->utils->getActiveTargetDB(), 'ci db', $this->ci->db->getOgTargetDB(),
					'session id', $this->ci->session->getSessionId());
				// password ok
				$session_id = $this->randomizer($user['username'] . $user['userId']);
				$token = $this->ci->users->updateLoginInfo($user['userId'], TRUE, TRUE, $session_id);

				//init session from target db
				$this->ci->session->reinit();

				$this->ci->utils->debug_log('after init session id', $this->ci->session->getSessionId());

				$userdata = array(
					'user_id' => $user['userId'],
					'username' => $user['username'],
					'status' => self::STATUS_ACTIVATED,
					'sessionId' => $session_id,
					'admin_login_token' => $token,
					'sidebar_status' => $sidebar_status,
					// 'login_from_mdb' => '1',
				);

				$this->ci->session->set_userdata($userdata);

				$this->ci->session->updateLoginId('admin_id', $user['userId']);

		        return true;
			}
		}

        // $this->ci->session->set_userdata('login_from_mdb', '0');
        $message=lang('cannot find username');
        return false;
	}

}

/* End of file authentication.php */
/* Location: ./application/libraries/authentication.php */