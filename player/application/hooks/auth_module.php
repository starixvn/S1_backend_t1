<?php

class Auth_module {

	private $CI;

	public function __construct() {
		$this->CI = &get_instance();
	}

	private function goToLoginPage() {
		redirect('/iframe/auth/login');
	}

	private function setLangByPlayer() {
		$this->CI->load->library(['authentication']);
		$this->CI->authentication->initiateLang();
	}

	private function isPublic($ctrlName, $funcName) {
		$result = false;
		$publicSettings = $this->CI->config->item('public_controller_function');
		foreach ($publicSettings as $pubCtrlName => $funcs) {
			if ($ctrlName == $pubCtrlName) {
				//check
				if ($funcs == '*' || in_array($funcName, $funcs)) {
					$result = true;
					break;
				}
			}
		}

		// $this->CI->utils->debug_log('isPublic', $result);
		return $result;
	}

	private function isProtectd($ctrlName, $funcName) {
		$result = false;
		$protectedSettings = $this->CI->config->item('protected_controller_function');
		foreach ($protectedSettings as $protectedCtrlName => $funcs) {
			if ($ctrlName == $protectedCtrlName) {
				//check
				if ($funcs == '*' || in_array($funcName, $funcs)) {
					$result = true;
					break;
				}
			}
		}

		// $this->CI->utils->debug_log('isProtectd', $result);
		return $result;

	}

	private function isLogged() {
		$this->CI->load->library(['authentication']);
		$isloggedIn = $this->CI->authentication->isLoggedIn();
		// if (!$isloggedIn && ($token = $this->CI->input->cookie('remember_me'))) {
		// 	$this->CI->load->model('player_login_token');
		// 	$player_id = $this->CI->player_login_token->getPlayerId($token);
		// 	$username = $this->CI->player_model->getUsernameById($player_id);
		// 	$password = $this->CI->player_model->getPasswordById($player_id);
		// 	$this->CI->authentication->login($username, $password);
		// 	$isloggedIn = $this->CI->authentication->isLoggedIn();
		// }
		return $isloggedIn;
	}

	public function index() {

		if ($this->checkSessionTimeout($next)) {
			$this->CI->utils->nocache();
			if (!empty($next)) {
				redirect($next);
			}
			return;
		}

		$this->CI->load->library(['session', 'authentication']);

		$ctrlName = $this->CI->uri->segment(1);
		$funcName = $this->CI->uri->segment(2);

		// $this->CI->utils->debug_log('ctrlName', $ctrlName, ' funcName', $funcName, 'islogged', $this->isLogged());
		$isProtectd = $this->isProtectd($ctrlName, $funcName);
		$isLogged = $this->isLogged();

		if ($isProtectd) {
			//only check protect url
			$this->CI->load->model(['country_rules']);
			$ip = $this->CI->utils->getIP(); // '180.232.133.50'; PHILIPPINES
			$isSiteBlock = $this->CI->country_rules->getBlockedStatus($ip, 'blocked_www_m');
			if ($isSiteBlock) {
				$this->CI->utils->debug_log('blocked: ' . $ip, $isSiteBlock);
                list($city, $countryName) = $this->CI->utils->getIpCityAndCountry($ip);
				$block_page_url = $this->CI->country_rules->getBlockedPageUrl($countryName, $city);
				if (empty($block_page_url)) {
					show_error('blocked', 403);
				} else {
					redirect($block_page_url);
				}
				$this->CI->utils->nocache();
				return false;
			}
		}

		if ($isProtectd && !$this->isPublic($ctrlName, $funcName) && !$isLogged) {
			$this->goToLoginPage();
		}

		if ($isProtectd && $isLogged) {
			//set lang
			$this->setLangByPlayer();
		}

		$this->CI->utils->nocache();

	}

	public function checkSessionTimeout(&$next = null) {

		$check = false;

		$uri = $_SERVER['REQUEST_URI']; //$this->CI->uri->uri_string();
		$uriArr = explode('/', $uri);

		//no session
		// log_message('debug','processSessionTimeout', ['uri'=>$uri, 'uriArr'=>$uriArr]);

		if (count($uriArr) >= 5) {
			$keyUri = $uriArr[2];
			if (strpos($keyUri, 'check_player_session_timeout') !== false) {
				$check = true;
				//check if session exists
				$sessionId = $uriArr[4];
				$playerId = $uriArr[3];
				$this->CI->load->model(['player_model']);
				$is_timeout = $this->CI->player_model->isPlayerSessionTimeout($sessionId);
				// log_message('debug','get timeout',['sessionid'=> $sessionId,'is_timeout'=> $is_timeout]);

				$addOrigin = true;
				$result = ['success' => true, 'is_timeout' => $is_timeout];

				$txt = json_encode($result);

				$this->CI->output->set_content_type('application/json')->set_output($txt);
				if ($addOrigin) {
					header("Access-Control-Allow-Origin: *");
					header("Access-Control-Allow-Methods: GET, POST");
					header("Access-Control-Expose-Headers: Access-Control-Allow-Origin");
					header("Access-Control-Allow-Credentials: true");
				}

				// header('Content-type: image/gif');
				// # The transparent, beacon image
				// echo chr(71) . chr(73) . chr(70) . chr(56) . chr(57) . chr(97) .
				// chr(1) . chr(0) . chr(1) . chr(0) . chr(128) . chr(0) .
				// chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) .
				// chr(33) . chr(249) . chr(4) . chr(1) . chr(0) . chr(0) .
				// chr(0) . chr(0) . chr(44) . chr(0) . chr(0) . chr(0) . chr(0) .
				// chr(1) . chr(0) . chr(1) . chr(0) . chr(0) . chr(2) . chr(2) .
				// chr(68) . chr(1) . chr(0) . chr(59);

			}
		}

		return $check;
	}

}