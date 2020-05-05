<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';

/**
 * General behaviors include :
 * * Setting message for user
 * * Set current language
 * * Player login
 * * Load template
 * * change currency
 *
 * @category Player Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Auth extends BaseController {
	function __construct() {
		parent::__construct();

		$this->load->helper('url');
		$this->load->library(array('permissions', 'form_validation'));

		$this->permissions->setPermissions();
	}

	public function index() {
		//echo BASEURL;
		redirect("auth/login");
	}

	/**
	 * set language
	 *
	 * @return	rendered Template with array of data
	 */
	public function setCurrentLanguage($language) {
		/*$data = array('currentLanguage'=> $language);*/
		$this->language_function->setCurrentLanguage($language);
		/*$this->session->set_userdata('login_lan', $language);*/
        $lang=Language_function::ISO2_LANG[$language];
		//set lang cookies
        $this->load->library(['session']);
        $this->session->setLanguageCookie($lang);
		redirect('auth/login');
	}

	/**
	 * Login user on the site
	 *
	 * @return void
	 */
	public function login($session_timeout = '0') {
		$this->load->model(['static_site', 'users']);
		$this->load->library(['ip_manager', 'user_functions']);
		$lang_int = $this->language_function->getCurrentLanguage();
		$lang     = $this->language_function->getLanguage($lang_int);
		$langISO2 = Language_function::ISO2_LANG[$lang_int];
		//set lang cookies
        $this->load->library(['session']);
        $this->session->setLanguageCookie($langISO2);

        $sbe_contact_info = $this->utils->getConfig('sbe_contact_info');
		$data['company_title']   = $this->static_site->getDefaultCompanyTitle($lang);
		$data['company_title']   = (isset($sbe_contact_info['company_title'])) ? $sbe_contact_info['company_title'] : $this->static_site->getDefaultCompanyTitle($lang);
		$data['contact_website'] = (isset($sbe_contact_info['website'])) ? $sbe_contact_info['website'] : NULL;
		$data['contact_skype']   = (isset($sbe_contact_info['skype'])) ? $sbe_contact_info['skype'] : NULL;
		$data['contact_email']   = (isset($sbe_contact_info['email'])) ? $sbe_contact_info['email'] : NULL;
		$data['logo_icon'] = 'og-login-logo2.png';
		$data['availableCurrencyList']  =$this->utils->getAvailableCurrencyList();
		$data['activeCurrencyKeyOnMDB'] =$this->utils->getActiveCurrencyKeyOnMDB();

		if ($this->authentication->isLoggedIn()) {
			if ($this->user_functions->checkIfUserIsLocked($this->authentication->getUserId())) {
				$data['errors']['password'] = lang('con.a01');
				$data['disabled'] = '';

				$this->authentication->logout();

				$this->load->view('login', $data);
			} else {
				redirect('home');
			}
		} else {
			$isUserIpAllowed = true;
			$this->load->model(['operatorglobalsettings', 'ip']);

			if ($this->operatorglobalsettings->getSettingValue('ip_rules') == 'true') {
				$ipAllowed=$this->ip->checkIfIpAllowedForAdmin();
				$this->utils->debug_log('checkIfIpAllowedForAdmin', ['ipAllowed'=>$ipAllowed,
					'getIpListFromXForwardedFor'=>$this->input->getIpListFromXForwardedFor(),
					'getRemoteAddr'=>$this->input->getRemoteAddr(),
					'getXRealipRemoteAddr'=>$this->input->getXRealipRemoteAddr(),
				]);
				if (!$ipAllowed) {
					//if ip is not allowed
					$data['errors']['password'] = lang('con.a02');
					$data['disabled'] = 'disabled';
					$isUserIpAllowed = false;
				}
			}

			if ($isUserIpAllowed) {
				$this->form_validation->set_rules('login', 'Login', 'trim|required|xss_clean');
				$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
				$this->form_validation->set_rules('remember', 'Remember me', 'integer');

				$login=$this->input->post('login');
				if($this->utils->isEnabledFeature('enable_otp_on_adminusers') &&
						$this->users->isEnabledOTPByUsername($login)){
					$this->form_validation->set_rules('otp_code', lang('2FA Code'), 'trim|required|xss_clean|callback_validate_otp_code');
				}

				$data['errors'] = array();

				if ($this->form_validation->run()) {

					$username = $this->input->post('login');
					$checker  = $this->user_functions->checkUserIfDeleted($username);
					if($checker){
						$data['errors']['password'] = lang('con.a01');
						$data['disabled'] = '';
					}else{
						$password = $this->input->post('password');
						if ($this->authentication->login($username, $password)) {
							$this->users->setPasswordPlainForCurrentUser($password);
							return redirect('/');
						} else {
							$errors = $this->authentication->get_error_message(); // fail
							foreach ($errors as $k => $v) {$data['errors'][$k] = $v;} //$data['errors'][$k] = $this->lang->line($v);
						}
					}
				}

				$data['disabled'] = '';
			}

			if ($session_timeout == '1') {
				$show_message = array(
					'result' => 'warning',
					'message' => lang('session.timeout'),
				);
				$this->session->set_userdata($show_message);
			}

			$this->load->view('login', $data);
		}
	}

	public function validate_otp_code($val) {
		$this->utils->debug_log('validate_otp_code', $val);

		$this->load->model('users');
		$success = true;

		$login=$this->input->post('login');
		$code=$val;

		$rlt= $this->users->validateOTPCodeByUsername($login, $code);
		$this->utils->debug_log('validate otp code', $login, $code);
		$success=$rlt['success'];
		if(!$success){
			$this->form_validation->set_message('validate_otp_code', lang('Wrong 2FA Code'));
		}
		return $success;
	}

	public function testConfig() {
		$this->ip_manager->checkIfIpAllowed();
		//$this->ip_manager->createConfigXML();
	}

	/**
	 * Logout user
	 *
	 * @return void
	 */
	public function logout() {
		$this->authentication->logout();
		redirect('/');
	}

	public function login_by_token($token){
		$success=false;
		$this->load->library(['authentication']);
		$this->load->model(array('common_token', 'users'));
		$adminId = $this->common_token->getAdminUserIdByToken($token);
		if(!empty($adminId)){
			$username=$this->users->getUsernameById($adminId);
			if(!empty($username)){
				$rlt=$this->authentication->set_login_user($username);
			}
		}

		$this->utils->debug_log('login by token', $token, $username, $adminId);

		return redirect('/');
	}

	// public function register() {
	// 	$this->load->view('register');
	// }

	public function change_active_currency(){
		//make sure we set session
		$this->load->library(['session']);

		$currencyKey=$this->input->get(Multiple_db::__OG_TARGET_DB);
		$result=['success'=>false];
		//validate currency
		if($currencyKey==Multiple_db::SUPER_TARGET_DB || $this->utils->isAvailableCurrencyKey($currencyKey)){
			$_multiple_db=Multiple_db::getSingletonInstance();
			$_multiple_db->init($currencyKey);
			$_multiple_db->rememberActiveTargetDB();

			$result['success']=true;
		}else{
			$result['message']=lang('not available currency');
		}

		$this->returnJsonResult($result);
	}

	/**
	 * SSO over MDB
	 * @param  string $currencyKey
	 *
	 */
	public function change_active_currency_for_logged($currencyKey) {
		$result=['success'=>false];
		$this->load->library(['authentication']);
		//still old db
		$loggedUserId=$this->authentication->getUserId();
		$loggedUsername=$this->authentication->getUsername();

		$this->utils->debug_log('loggedUserId', $loggedUserId, 'loggedUsername', $loggedUsername, 'ci db', $this->db->getOgTargetDB());
		if(empty($loggedUserId)){
			$result['message']=lang('session timeout, please relogin');
			return $this->returnJsonResult($result);
		}

		// $currencyKey=$this->input->get(Multiple_db::__OG_TARGET_DB);
		//validate currency
		if($currencyKey==Multiple_db::SUPER_TARGET_DB || $this->utils->isAvailableCurrencyKey($currencyKey)){
			$_multiple_db=Multiple_db::getSingletonInstance();
			$_multiple_db->switchCIDatabase($currencyKey);
			// $_multiple_db->rememberActiveTargetDB();
			// $_multiple_db->switchCIDatabaseToActiveTargetDB();
			//init session from target db
			// $this->session->reinit();

			$message=null;
			$result['success']=$this->authentication->login_from_mdb($loggedUserId, $loggedUsername, $message);
			if(!$result['success']){
				$result['message']=$message;
			}else{
				$uri=$this->getUriFromReferer();
				$list=$this->utils->getConfig('switch_multiple_currency_white_list');
				if(!$this->matchUriInList($uri, $list)){
					$result['redirect_url']='/';
				}
			}
		}else{
			$result['message']=lang('not available currency');
		}

		return $this->returnJsonResult($result);

	}

	public function test_ip_allow(){
		$this->load->model(['ip']);
		$ipAllowed=$this->ip->checkIfIpAllowedForAdmin();

		$result=[
			'ipAllowed'=>$ipAllowed,
			'getIpListFromXForwardedFor'=>$this->input->getIpListFromXForwardedFor(),
			'getRemoteAddr'=>$this->input->getRemoteAddr(),
			'getXRealipRemoteAddr'=>$this->input->getXRealipRemoteAddr(),
			// '$_SERVER'=>$_SERVER,
		];
		$this->utils->debug_log('checkIfIpAllowedForAdmin', $result);

		$this->returnJsonResult($result);
	}

}

/* End of file auth.php */
/* Location: ./application/controllers/auth.php */
