<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once 'phpass-0.1/PasswordHash.php';

/**
 * Authentication for player
 *
 * Authentication library for FHI OG System
 *
 * @package     Authentication
 * @author      Johann Merle
 * @version     1.0.0
 *
 * @property PlayerCenterBaseController $ci
 */

class Authentication {
	private $error = array();

	const STATUS_ACTIVATED = '1';
	const STATUS_NOT_ACTIVATED = '0';

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array('session', 'language_function', 'player_functions', 'email_setting', 'salt'));
		$this->ci->load->model(array('player','player_model'));

		$this->initiateLang();
		$this->checkSession();

		$this->utils=$this->ci->utils;
		// $this->ci->utils->debug_log('init authentication');
	}

	/**
	 * initiate Language
	 *
	 * @return  void
	 */
	public function initiateLang() {
		$language = $this->ci->language_function->getCurrentLanguage();
		$langCode = $this->ci->language_function->getLanguageCode($language);
		$language = strtolower($this->ci->language_function->getLanguage($language));
		$this->ci->lang->load($langCode, $language);

        $custom_lang = config_item('custom_lang');
        if((FALSE !== $custom_lang) && (file_exists(APPPATH . 'language/custom/' . $custom_lang . '/' . $language . '/custom_lang.php'))){
            $this->ci->lang->load('custom', 'custom/' . $custom_lang . '/' . $language);
        }

		return $language;
	}

	public function checkSession() {
		if (empty($this->ci->session->userdata('player_id'))) //check if player is login
		{
			return;
		}

		$now=$this->ci->utils->getNowForMysql();

		//check LAST_ACTIVITY
		$updateLastActivity=true;
		$lastActivitySession=$this->ci->session->userdata('LAST_ACTIVITY');
		if(!empty($lastActivitySession)){
			$record_activity_timeout_seconds=$this->ci->utils->getConfig('record_activity_timeout_seconds');
			//timeout
			$updateLastActivity=$this->ci->utils->moreThanTimeout($lastActivitySession, $now, $record_activity_timeout_seconds);
		}

		$player_id = $this->ci->session->userdata('player_id'); //get player_id from session

		// $result = $this->ci->player_functions->getPlayerById($player_id); //get player data

		// $this->ci->utils->debug_log('LAST_ACTIVITY', $this->ci->session->userdata('LAST_ACTIVITY'), 'result[lastActivityTime]', $result['lastActivityTime'], 'strtotime(date(Y-m-d H:i:s))', strtotime(date('Y-m-d H:i:s')));
		// if ($this->ci->session->userdata('LAST_ACTIVITY') != null && $this->ci->session->userdata('LAST_ACTIVITY') != $result['lastActivityTime']) { //check if last activity is same in database
		// 	$this->logout(); //logout from site
		// }
		// if ($this->ci->session->userdata('LAST_ACTIVITY') != null && (strtotime(date('Y-m-d H:i:s')) - strtotime($this->ci->session->userdata('LAST_ACTIVITY')) > 1800)) {
		// 	//check if last activity is >= 30 minutes ago
		// 	$this->logout(); //logout from site

		// 	// last request was more than 30 minutes ago
		// 	//$this->ci->session->unset();     // unset $_SESSION variable for the run-time
		// 	//$this->ci->session->session_destroy();   // destroy session data in storage
		// }

		//save to session last activity time
		$this->ci->session->set_userdata('LAST_ACTIVITY', $now);

		// $this->ci->utils->debug_log('updateLastActivity', $updateLastActivity, 'record_activity_timeout_seconds', $record_activity_timeout_seconds);

		if($updateLastActivity){
			//save to db last activity time
			$this->ci->player_model->updateLastActivity($player_id, $this->ci->session->userdata('LAST_ACTIVITY'), null, null, null);
		}
		// $data = array('lastActivityTime' => $this->ci->session->userdata('LAST_ACTIVITY'));
		// $this->ci->player_functions->editPlayer($data, $player_id);
	}

	public function validateOnly($password){

		$this->ci->load->model(['player_model']);
		$player=$this->ci->player_model->getPlayerArrayById($this->getPlayerId());

		return $this->ci->salt->decrypt($player['password'], $this->ci->config->item('DESKEY_OG')) == $password;
	}

	/**
	 *
	 * login by player info , load from player table
	 *
	 * @param  array $playerInfo
	 * @return bool
	 */
	public function loginByPlayerInfo($playerInfo, $allow_clear_session=true) {
		$username= $playerInfo['username'];
		$password= $this->ci->salt->decrypt($playerInfo['password'], $this->ci->config->item('DESKEY_OG'));
		return $this->login($username, $password, $allow_clear_session);
	}

	/**
	 * Login player on the site. Return TRUE if login is successful
	 * (player exists and password is correct), otherwise FALSE.
	 *
	 * @param   string  (username)
	 * @param   string  (password)
	 * @param   bool
	 * @return  bool
	 */
	public function login($login, $password, $allow_clear_session=true) {
        $this->ci->load->library(['player_notification_library']);

		$this->ci->utils->debug_log('login username:'.$login);

        $this->ci->session->set_userdata('login_from_admin', '0');

		if ((strlen($login) > 0) AND (strlen($password) > 0)) {

			if (!is_null($player = $this->ci->player->getPlayerByLogin($login))) {
				// login ok

				if ($player->status == 0 && !$this->ci->player_model->isDeleted($player->playerId)) {
					// normal account ok

					$empty_password=empty($player->password);
					if($empty_password && !empty($this->ci->utils->getConfig('external_login_api_class'))){
						//try external login
						$message=null;
						$logged=$this->ci->utils->login_external($player->playerId, $player->username, $password, $message);
						$this->ci->utils->debug_log('external login username:'.$player->username, $logged, $message);
					}else{
						$logged=$this->ci->salt->decrypt($player->password, $this->ci->config->item('DESKEY_OG')) == $password;
					}

					// Does password match hash in database?
					if ($logged) {

						$this->ci->load->model(array('operatorglobalsettings', 'player_model', 'external_system', 'game_provider_auth'));

						if($empty_password){
							//update empty password
							$this->ci->player_model->resetPassword($player->playerId, [
								'password'=>$this->ci->salt->encrypt($password, $this->ci->config->item('DESKEY_OG'))
								]);
							$this->ci->game_provider_auth->updateEmptyPassword($player->playerId, $password);
						}

						# OG-1927 IF SINGLE PLAYER SESSION
						$single_player_session=$this->ci->operatorglobalsettings->getSettingBooleanValue('single_player_session');
						$this->utils->debug_log('single_player_session is:'.$single_player_session);
						if ($single_player_session && $allow_clear_session) {
                            $this->ci->load->library(array('player_library'));
                            $kickedPlayer = $this->ci->player_library->kickPlayer($player->playerId);
                            if($kickedPlayer){
                                $this->ci->player_library->kickPlayerGamePlatform($player->username, $player->playerId);
                            }
						}

						if ($this->ci->session->userdata('CREATED') == null) {
							$this->ci->session->set_userdata('CREATED', time());
						}

                        $this->ci->player_model->updatePlayerOnlineStatus($player->playerId, Player_model::PLAYER_ONLINE);
                        $lastActivity = $this->ci->player_model->getLastActivity($player->playerId);
						$this->ci->player_model->updateLoginInfo($player->playerId, $this->ci->input->ip_address(), $this->ci->utils->getNowForMysql());
						$token = $this->ci->common_token->getPlayerToken($player->playerId);
						$language=$this->ci->language_function->langStrToInt($this->ci->player_model->getLanguageFromPlayer($player->playerId));

                        if ($language) {
							$this->ci->session->set_userdata(array(
								'lang' => $language,
							));
							$this->ci->language_function->setCurrentLanguage($language);
						}

						$this->ci->session->set_userdata(array(
							'player_id' => $player->playerId,
							'username' => $player->username,
							'status' => self::STATUS_ACTIVATED,
							'player_login_token' => $token,
						));

						if(!empty($lastActivity)){
                            list($city, $countryName) = $this->ci->utils->getIpCityAndCountry($lastActivity->lastLoginIp);
                            $this->ci->player_notification_library->success($lastActivity->playerId, Player_notification::SOURCE_TYPE_LAST_LOGIN, 'player_notify_success_last_login_title', [
                                'player_notify_success_last_login_message',
                                $lastActivity->lastLoginTime,
                                $lastActivity->lastLoginIp,
                                $countryName,
                                $city
                            ]);
                        }

						$this->ci->session->updateLoginId('player_id', $player->playerId);

						return TRUE;

					} else {
						// fail - wrong password
						$this->error = array('password' => lang('con.04')); //'incorrect password'

                        //added by jhunel.php 8-19-2017
                        //blocked account for multiple incorrect password consecutively during log in
                        if($this->ci->operatorglobalsettings->getSettingBooleanValue('player_login_failed_attempt_blocked')){
                            $max_login_attempt = (int)$this->ci->operatorglobalsettings->getSettingIntValue('player_login_failed_attempt_times');
                            $totalWrongLoginAttempt = (int)$this->ci->player_model->getPlayerTotalWrongLoginAttempt($player->playerId);
                            $updateAttempt = $totalWrongLoginAttempt + 1;
                            if($updateAttempt >= $max_login_attempt){
                                $this->ci->player_model->updatePlayer($player->playerId, [
                                    'blocked' => Player_model::STATUS_BLOCKED_FAILED_LOGIN_ATTEMPT,
                                    'blocked_status_last_update' => $this->ci->utils->getNowForMysql(),
                                    'failed_login_attempt_timeout_until' => $this->ci->utils->getNowForMysql(),
                                ]);
                            }
                            $this->ci->player_model->updatePlayerTotalWrongLoginAttempt($player->playerId, $updateAttempt);
                        }
                    }
				} else {
					// fail - locked account
					$today = date("Y-m-d H:i:s");

					if (strtotime($today) > strtotime($player->lockedStart) && strtotime($today) < strtotime($player->lockedEnd)) {
						$days = floor((strtotime($player->lockedEnd) - strtotime($today)) / (60 * 60 * 24));
						if ($days == 0) {
							$days = '1';
						}

						$this->error = array('login' => "You're account has been locked for " . $days . " day/s, You're not allowed to login");
					} else {

						$data = array(
							'lockedStart' => '0000-00-00 00:00:00',
							'lockedEnd' => '0000-00-00 00:00:00',
							'status' => self::STATUS_NOT_ACTIVATED,
						);

						$this->ci->player->editPlayer($data, $player->playerId);

						// Does password match hash in database?
						if ($this->ci->salt->decrypt($player->password, $this->ci->config->item('DESKEY_OG')) == $password) {

                            $lastActivity = $this->ci->player_model->getLastActivity($player->playerId);
							$token=$this->ci->player_model->updateLoginInfo($player->playerId,  $this->ci->input->ip_address(), $this->ci->utils->getNowForMysql());

							$language=$this->ci->language_function->langStrToInt($this->ci->player_model->getLanguageFromPlayer($player->playerId));

							if ($language) {
								$this->ci->session->set_userdata(array(
									'lang' => $language,
								));
							}

							$this->ci->session->set_userdata(array(
								'player_id' => $player->playerId,
								'username' => $player->username,
								'status' => self::STATUS_ACTIVATED,
								'player_login_token' => $token,
							));

                            if(!empty($lastActivity)){
                                list($city, $countryName) = $this->ci->utils->getIpCityAndCountry($lastActivity->lastLoginIp);
                                $this->ci->player_notification_library->success($lastActivity->playerId, Player_notification::SOURCE_TYPE_LAST_LOGIN, 'player_notify_success_last_login_title', [
                                    'player_notify_success_last_login_message',
                                    $lastActivity->lastLoginTime,
                                    $lastActivity->lastLoginIp,
                                    $countryName,
                                    $city
                                ]);
                            }

							return TRUE;

						} else {
							// fail - wrong password
							$this->error = array('password' => lang('con.04'));
						}
					}
				}
			} else {
				// fail - wrong login
				$this->error = array('login' => lang('con.04'));
			}
		}

		return FALSE;
	}

    public function login_from_admin($login, $password, $allow_clear_session=true){
        if(!$this->login($login, $password, $allow_clear_session)){
            $this->ci->session->set_userdata('login_from_admin', '0');
            return FALSE;
        }

        $this->ci->session->set_userdata('login_from_admin', '1');

        return TRUE;
    }

    public function login_from_mdb($username, $password, &$message){
    	$allow_clear_session=true;
        if(!$this->login($username, $password, $allow_clear_session)){
            // $this->ci->session->set_userdata('login_from_mdb', '0');
            if(isset($this->error['login'])){
            	$message=$this->error['login'];
            }elseif(isset($this->error['password'])){
            	$message=$this->error['password'];
            }else{
            	$message=lang('Login failed');
            }
            return FALSE;
        }

        // $this->ci->session->set_userdata('login_from_mdb', '1');

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
	 * Logout player from the site
	 * OGP-15798: add argument player_id for invocation from player center API
	 *
	 * @param	int		$player_id		player.playerId, OPTIONAL
	 *                         only used when called from Api_common::logout()
	 * @param	bool	$from_comapi	Explicitly specifies call from Api_common
	 * @see		Api_common::logout()
	 *
	 * @return  array
	 */
	public function logout($player_id = null, $from_comapi = false) {
        $this->ci->load->library(array('player_library'));

        // Fetch player_id first if not supplied in args
        if (empty($player_id)) {
        	$player_id = $this->getPlayerId();
        }

		$this->ci->utils->debug_log('player/Authentication::logout()', [ 'playerid' => $player_id, 'is_called_from_api_common' => $from_comapi ]);

        $result = [
            'success' => TRUE,
            'redirect_url' => $this->ci->operatorglobalsettings->getPlayerCenterLogoutRedirectUrl()
        ];

        // Return success if already not logged in
        if(!$this->isLoggedIn()){
            return $result;
        }

        // Return success if player_id empty
        if(empty($player_id)){
            return $result;
        }

        // Go with logout procedures:

        // - Clear session
        $this->ci->session->updateLoginId('player_id', '');
        $this->ci->session->sess_destroy();

        // - Update player activity, online status
        $this->ci->player_model->updateLastActivity($player_id, $this->ci->utils->getNowForMysql(), null, $this->ci->utils->getNowForMysql(), null, $this->ci->utils->getIp());
        $this->ci->player_model->updatePlayerOnlineStatus($player_id, Player_model::PLAYER_OFFLINE);

        // - Kick player out of all game platforms
        $player_username = $this->ci->player_model->getUsernameById($player_id);
        $this->ci->player_library->kickPlayerGamePlatform($player_id, $player_username);

		// $this->ci->load->helper('cookie');
		// delete_cookie('remember_me');

        return $result;
	}

	/**
	 * Check if player logged in.
	 *
	 * @param   bool
	 * @return  bool
	 */
	public function isLoggedIn($activated = TRUE) {
		return $this->ci->session->userdata('status') === ($activated ? self::STATUS_ACTIVATED : self::STATUS_NOT_ACTIVATED);
	}

    public function isLoggedInFromAdmin(){
        return ($this->isLoggedIn() && ((int)$this->ci->session->userdata('login_from_admin') === 1)) ? TRUE : FALSE;
    }

	public function getUserId() {
		return null;
	}

	/**
	 * Get player_id
	 *
	 * @return  string
	 */
	public function getPlayerId() {
		return $this->ci->session->userdata('player_id');
	}

	public function getPlayerToken() {
		$playerId=$this->getPlayerId();
		if(!empty($playerId)){
	        $this->ci->load->model(array('common_token'));
	        return $this->ci->common_token->getPlayerToken($playerId);
		}

		return null;

		// return $this->ci->session->userdata('player_login_token');
	}

	/**
	 * Get username
	 *
	 * @return  string
	 */
	public function getUsername() {
		return $this->ci->session->userdata('username');
	}

	/**
	 * Get session_id
	 *
	 * @return  string
	 */
	public function getSessionId() {
		return $this->ci->session->userdata('session_id');
	}

	/**
	 * Get error message.
	 * Can be invoked after any failed operation such as login or register.
	 *
	 * @return  array
	 */
	public function get_error_message() {
		return $this->error;
	}

	/**
	 * get player VIP Group level
	 * @return  string
	 */
	public function getPlayerMembership() {
		$player_id = $this->ci->session->userdata('player_id'); //get player_id from session
		$result = $this->ci->player_functions->getPlayerById($player_id);
		return lang($result['levelName']);

	}

	public function isProtectedRoute(){
	    global $RTR;
	    $config = config_item('public_controller_function');

	    $result = TRUE;

        $segments = $this->ci->uri->segments;

        $namespace = NULL;
        $class  = strtolower($RTR->fetch_class());
        $method = strtolower($RTR->fetch_method());
        if(!empty($segments) && (strtolower($segments[1]) != $class)){
            $namespace = strtolower($segments[1]);
        }

        if(isset($config[$class])){
            $result = in_array($method, $config[$class]) ? FALSE : $result;
        }

        return $result;
    }

}

/* End of file fe_authentication.php */
/* Location: ./application/libraries/fe_authentication.php */