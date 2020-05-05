<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

/**
 * General behaviors include :
 *
 * * Login/Logout
 * * Captcha validator
 * * Form validations
 * * Checking of IM1/IM2 type
 * * Add to affiliate payout
 * * Show earnings
 * * Activate/Deactivate/Delete Payment
 * * Payment history
 * * Balance list
 * * Add/activate/delete affiliate
 * * Transaction and game history
 *
 * @category Affiliate Controller
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 * *
 */
class Affiliate extends BaseController {
	const IS_AFFILIATE = true;
	const ACTION_NEW_DEPOSIT = 1;
	const ACTION_NEW_WITHDRAW = 2;
	const ACTION_TRANSFER_FROM_SW = 3;
	const ACTION_TRANSFER_TO_SW = 4;
	const EN_LANG = 1;
	const CN_LANG = 2;
	function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->library(array('template', 'form_validation', 'authentication', 'affiliate_manager', 'session', 'pagination', 'salt', 'email_setting'));

		$this->initiateLang();
		$this->setLanguageBySubDomain();
	}

	/**
	 * overview : initiate Language
	 *
	 * @return  void
	 */
	public function initiateLang() {
		$lang = $this->language_function->getCurrentLanguage();
		$langCode = $this->language_function->getLanguageCode($lang);
		$language = $this->language_function->getLanguage($lang);
		$this->lang->load($langCode, $language);
	}

	/**
	 * overview : change Language
	 *
	 * @return  void
	 */
	public function changeLanguage($language) {
		$this->session->set_userdata('afflang', $language);
		$arr = array('status' => 'success');

		echo json_encode($arr);
	}

	/**
	 * overview : get affiliate id from session
	 *
	 * @return int
	 */
	public function getAffIdFromSession() {
		return $this->session->userdata('affiliateId');
	}

	public function getAffUsernameFromSession() {
		return $this->session->userdata('affiliateUsername');
	}

	public function getAffLangFromSession(){
		return $this->session->userdata('afflang');
	}

	/**
	 * overview : loads template for view based on regions in
	 *
	 * detail : config > template.php
	 *
	 * @param $title
	 * @param $description
	 * @param $keywords
	 */
	protected function loadTemplate($title, $description, $keywords) {
		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);

		$this->template->add_js2($this->utils->jsUrl('affiliate.js'));
    	$this->template->add_js2($this->utils->thirdpartyUrl('numeral/numeral.min.js'));
		$this->utils->loadDatatables($this->template);
	}

	/**
	 * overview : check if affiliate is login
	 *
	 * @return	bool
	 */
	protected function checkLogin() {
        $affId = $this->getAffIdFromSession();
        $notDeleted = false;
        if(!empty($affId)){
            $res = $this->affiliatemodel->getAffiliateById($affId);
            $notDeleted = count($res) > 0;
        }
		return !empty($this->getAffUsernameFromSession()) && !empty($affId) && $notDeleted;
	}

	protected function checkSecondPassword(){
		if ($this->utils->isEnabledFeature('affiliate_second_password')) {
			$affiliate_id = $this->getSessionAffId();
			$affiliate = $this->affiliatemodel->getAffiliateById($affiliate_id);

			if (empty($affiliate['second_password'])) {
				redirect('/affiliate/modifySecondPassword', 'refresh');
			} else if( ! $this->isEnterSecondPasswordFromSession()) {
				redirect('/affiliate/second_password','refresh');
			}
		}
	}

	/**
	 * overview : index page of affiliate page
	 *0
	 * @return	void
	 */
	public function index() {
        return $this->signin();
	}

	/**
	 * overview : signin
	 */
	public function signin() {
		$this->loadTemplate(lang('Affiliate'), '', '');

		if (!$this->checkLogin()) {
			$data['availableCurrencyList']=$this->utils->getAvailableCurrencyList();
			$data['activeCurrencyKeyOnMDB']=$this->utils->getActiveCurrencyKeyOnMDB();
			$data['useCaptchaOnLogin'] = $this->config->item('captcha_login');
			$this->load->view($this->config->item('affiliate_view_template') . '/login', $data);
		} else {
			redirect('dashboard');
		}
	}

	/**
	 * overview : login
	 *
	 * @return	void
	 */
	public function login($adminToken = null, $affiliateId = null, $lang = null) {
		$username = '';
		$password = '';
		$autoLogin = false;
		if ($adminToken) {
			$autoLogin = $this->validateAdminToken($adminToken, 'login_as_aff');
		}
		$this->load->model('affiliatemodel');

		if (!$autoLogin) {
			if ($this->input->post()) {
				if ($this->config->item('captcha_login')) {
					$this->form_validation->set_rules('login_captcha', lang('label.captcha'), 'callback_check_captcha');
					if ($this->form_validation->run() == false) {
						$this->alertMessage(2, validation_errors()); // Wrong Captcha
						redirect('affiliate', 'refresh');
					}
				}
			}

			$username = $this->input->post('username');
			$password=$this->input->post('password');
			// if(!empty($password)){
			// 	//encrypted
			// 	$password = $this->salt->encrypt($password, $this->getDeskeyOG());
			// }
		} else {
			//check admin session first
			if ($affiliateId) {
				$aff = $this->affiliatemodel->getAffPassword($affiliateId);
				$username = $aff['username'];
				$password = $this->salt->decrypt($aff['password'], $this->getDeskeyOG());
				// $password = $this->salt->encrypt($pw, $this->getDeskeyOG());
			}
		}

		$readonly_params = array_filter(explode('_', $username));
		$is_readonly = count($readonly_params) > 1;

		$encryptedPassword = $this->utils->encodePassword($password);

		if ($is_readonly) {
			$affiliate_username = reset($readonly_params);
			$result = $this->affiliatemodel->readonly_login($affiliate_username, $username, $this->utils->encodePassword($this->input->post('password')));
		} else {
			$result = $this->affiliatemodel->login($username, $encryptedPassword, $password, $additionalMessage);
			if(!$result){
				$this->utils->error_log('login failed: '.$username, $password, $additionalMessage);
			}
		}

		if ($result) {

			if ($result['status'] == '1') {
				$message = lang('con.01');
				$this->alertMessage(1, $message); // Not yet activated
                $this->session->unset_userdata(array(
					'affiliateUsername' => null,
					'affiliateId' => null,
					'last_time_enter_second_password' => null,
					'next_uri' => null,
				));
				redirect('affiliate');
			} else if ($result['status'] == '2') {
				$message = lang('con.02');
				$this->alertMessage(1, $message); // Account frozen
				$this->session->unset_userdata(array(
                    'affiliateUsername' => null,
                    'affiliateId' => null,
                    'last_time_enter_second_password' => null,
                    'next_uri' => null,
                ));
				redirect('affiliate');
			} else {
				$language = ( $lang ) ? $lang : $this->input->post('language');
				$this->after_login_affiliate($result, $is_readonly, $language);

				$message = lang('con.03',$language);
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message); // Successful login
				redirect('affiliate/dashboard?lang=' . $lang);
			}
		}

		$message = lang('con.04');
		$this->alertMessage(2, $message); // Login Details Incorrect
		redirect('affiliate');
	}

	/**
	 * overview : logout
	 *
	 * @return	void
	 */
	public function logout() {
		$data = array(
			'lastLogout' => date('Y-m-d H:i:s'),
		);
		$this->affiliate_manager->editAffiliates($data, $this->session->userdata('affiliateId'));

		$this->session->updateLoginId('affiliate_id', '');

		$this->session->unset_userdata(array(
			'affiliateUsername' => null,
			'affiliateId' => null,
			'last_time_enter_second_password' => null,
			'next_uri' => null,
		));

		redirect('affiliate');
	}

	/**
	 * overview : captcha loader
	 *
	 * @return img
	 */
	public function captcha() {

		if ($this->config->item('captcha_login')) {
			$active = $this->config->item('si_active');
			$allsettings = array_merge($this->config->item($active), $this->config->item('si_general'));

			$this->load->library('captcha/securimage');
			$img = new Securimage($allsettings);
			$img->show($this->config->item('si_background'));
		}

	}
	/**
	 * overview : captcha validator
	 *
	 * @param string
	 * @return bool
	 */
	public function check_captcha($val) {
		$rlt = false;
		if ($this->config->item('captcha_login')) {
			//check captcha first
			$this->load->library('captcha/securimage');
			$securimage = new Securimage();

			$rlt = $securimage->check($this->input->post('login_captcha'));
			$this->form_validation->set_message('check_captcha', lang('error.captcha'));
		}
		return $rlt;
	}

	/**
	 * overview : registration page
	 *
	 * @return	void
	 */
	public function register($parentCode='', $showHeader = 'true') {
		$this->loadTemplate('Affiliate Register Page', '', '');
        $this->template->add_js($this->utils->thirdpartyUrl('bootstrap-select/1.12.4/bootstrap-select.min.js'));
        $this->template->add_css($this->utils->thirdpartyUrl('bootstrap-select/1.12.4/bootstrap-select.min.css'));

        // $this->session->set_userdata('afflang', self::CN_LANG);
		$data['curren'] = $this->affiliate_manager->getCurrency();

		$this->load->model('affiliatemodel');
		$parentId = 0;
		$code = NULL;
		if (!empty($parentCode)) {
			$code = $parentCode;
		} elseif ($this->checkAffDomain()) {
			$code = $this->checkAffDomain();
		}

        if ($code) {
			$parentId = $this->affiliatemodel->getAffiliateIdByTrackingCode($code);
            $this->setTrackingCodeToSession($code);
		}
		$data['trackingCode'] = $code;
		$data['parentId'] = $parentId;

		$segment = $this->uri->segment(2);
		$currenTemplate = $this->config->item('affiliate_view_template');

		if ($currenTemplate == 'affiliate') {

			if ($showHeader == 'true') {
				$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			}
		}

		$this->template->write_view('main_content', $this->config->item('affiliate_view_template') . '/register', $data);

		$this->template->render();
	}

	/**
	 * overview : registration form rules
	 *
	 * @return	void
	 */
	public function form_rules() {
		$this->form_validation->set_rules('username', lang('aff.al10'), 'trim|required|min_length[5]|max_length[12]|alpha_numeric|is_unique[affiliates.username]');
		$this->form_validation->set_rules('password', lang('reg.05'), 'trim|required|min_length[6]|max_length[12]');
		$this->form_validation->set_rules('confirm_password', lang('reg.07'), 'trim|required|callback_confirmPassword');

		if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Email Address') == 0) {
			$this->form_validation->set_rules('email', lang('reg.a37'), 'trim|xss_clean|required|valid_email|is_unique[affiliates.email]');
		} else {
			$this->form_validation->set_rules('email', lang('reg.a37'), 'trim|xss_clean|valid_email|is_unique[affiliates.email]');
		}

		if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('First Name') == 0) {
			$this->form_validation->set_rules('firstname', lang('aff.al14'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('firstname', lang('aff.al14'), 'trim|xss_clean');
		}

		if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Last Name') == 0) {
			$this->form_validation->set_rules('lastname', lang('aff.al15'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('lastname', lang('aff.al15'), 'trim|xss_clean');
        }

        if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Birthday') == 0) {
            if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Birthday') == 0) {
                $this->form_validation->set_rules('birthday', lang('aff.ai04'), 'trim|required|xss_clean|callback_checkAge');
            } else {
                $this->form_validation->set_rules('birthday', lang('aff.ai04'), 'trim|xss_clean|callback_checkAge');
            }
        }

		if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Gender') == 0) {
			$this->form_validation->set_rules('gender', lang('aff.ai05'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('gender', lang('aff.ai05'), 'trim|xss_clean');
		}

		if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Company') == 0) {
			$this->form_validation->set_rules('company', lang('aff.ai06'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('company', lang('aff.ai06'), 'trim|xss_clean');
		}

		if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Occupation') == 0) {
			$this->form_validation->set_rules('occupation', lang('aff.ai07'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('occupation', lang('aff.ai07'), 'trim|xss_clean');
		}

		if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Mobile Phone') == 0) {
			$this->form_validation->set_rules('mobile', lang('reg.a54'), 'trim|required|xss_clean|numeric');
		} else {
			$this->form_validation->set_rules('mobile', lang('reg.a54'), 'trim|xss_clean|numeric');
		}

		if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Phone') == 0) {
			$this->form_validation->set_rules('phone', lang('aff.ai15'), 'trim|required|xss_clean|numeric');
		} else {
			$this->form_validation->set_rules('phone', lang('aff.ai15'), 'trim|xss_clean|numeric');
		}

		if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('City') == 0) {
			$this->form_validation->set_rules('city', lang('reg.a19'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('city', lang('reg.a19'), 'trim|xss_clean');
		}

		if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Address') == 0) {
			$this->form_validation->set_rules('address', lang('reg.a20'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('address', lang('reg.a20'), 'trim|xss_clean');
		}

		if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Zip Code') == 0) {
			$this->form_validation->set_rules('zip', lang('reg.a21'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('zip', lang('reg.a21'), 'trim|xss_clean');
		}

		if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('State') == 0) {
			$this->form_validation->set_rules('state', lang('reg.a22'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('state', lang('reg.a22'), 'trim|xss_clean');
		}

		if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Country') == 0) {
			$this->form_validation->set_rules('country', lang('reg.a23'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('country', lang('reg.a23'), 'trim|xss_clean');
		}

		if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Website') == 0) {
			$this->form_validation->set_rules('website', lang('reg.a41'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('website', lang('reg.a41'), 'trim|xss_clean');
		}

		if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Instant Message 1') == 0) {
			$this->form_validation->set_rules('imtype1', lang('reg.a30'), 'trim|xss_clean|required|callback_checkIM1Type');
		} else {
			$this->form_validation->set_rules('imtype1', lang('reg.a30'), 'trim|xss_clean|callback_checkIM1Type');
		}

		if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Instant Message 2') == 0) {
			$this->form_validation->set_rules('imtype2', lang('reg.a35'), 'trim|xss_clean|required|callback_checkIM2Type');
		} else {
			$this->form_validation->set_rules('imtype2', lang('reg.a35'), 'trim|xss_clean|callback_checkIM2Type');
		}

		if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Language') == 0) {
			$this->form_validation->set_rules('language', lang('ban.lang'), 'trim|required');
		} else {
			$this->form_validation->set_rules('language', lang('ban.lang'), 'trim');
		}
	}

	/**
	 * overview : validate form through ajax
	 */
	public function validateThruAjax() {

		$this->form_validation->set_message('is_unique', lang('formvalidation.is_unique'));

		if ($this->input->post('username')) {
			$this->form_validation->set_rules('username', lang('aff.al10'), 'trim|required|min_length[5]|max_length[12]|alpha_numeric|is_unique[affiliates.username]');
		}
		if ($this->input->post('email')) {
			$this->form_validation->set_rules('email', lang('reg.a37'), 'trim|xss_clean|required|valid_email|is_unique[affiliates.email]');
		}
		if ($this->input->post('birthday')) {
			$this->form_validation->set_rules('birthday', lang('aff.ai04'), 'trim|required|xss_clean|callback_checkAge');
		}
		if ($this->input->post('mode_of_contact')) {
			$this->form_validation->set_rules('mode_of_contact', lang('reg.a36'), 'trim|xss_clean|required|callback_checkModeOfContact');
		}

		if ($this->form_validation->run() === false) {
			$arr = array('status' => 'error', 'msg' => validation_errors());
			$this->returnJsonResult($arr);
		} else {
			$arr = array('status' => 'success', 'msg' => "");
			$this->returnJsonResult($arr);
		}
	}

	/**
	 * overview : verify registration page
	 *
	 * @return	void
	 */
	public function verifyRegister() {
		$this->form_rules();
		$this->utils->debug_log('affiliate VERIFYREGISTER post', $this->input->post());

		if ($this->form_validation->run() == false) {
			$message = validation_errors();
			$this->utils->debug_log('affiliate VERIFYREGISTER error', $message);

			if ($this->uri->segment('3') == 'subaffiliate') {
				$this->addSubAffiliate();
			} else {
				$this->register();
			}
		} else {
			if (!class_exists('PasswordHash')) {
				require_once APPPATH . 'libraries/phpass-0.1/PasswordHash.php';
			}

			$username = $this->input->post('username');
			$affiliate_id = null;
			$success = $this->utils->globalLockAffiliateRegistration($username, function()
				use(&$affiliate_id, $username){
					$affiliate_id = $this->addToAffiliates($username);
					$success = !empty($affiliate_id);

					if($success){
						$this->syncAffCurrentToMDB($affiliate_id, true);
					}
					return $success;
				}
			);

			$lang = $this->session->userdata('afflang');

			if(!$success){
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sorry, save affiliate failed.'));
				if ($this->uri->segment('3') == 'subaffiliate') {
					redirect("affiliate/subaffiliates");
				} else {
					redirect("affiliate");
				}
				return ;
			}

			$affiliateDetails = $this->affiliate_manager->getAffiliateById($affiliate_id); // Affiliate's information
			$this->utils->debug_log('affiliateDetails', $affiliateDetails);

			if(!empty($affiliateDetails['email'])){
				#sending email
				$this->load->library(['email_manager']);
		        $template = $this->email_manager->template('affiliate', 'affiliate_registered_success', array('affiliate_id' => $affiliate_id));

		        $template_enabled = $template->getIsEnableByTemplateName(true);
		        if ($template_enabled['enable']) {
		        	$email = $affiliateDetails['email'];
		        	$template->sendingEmail($email, Queue_result::CALLER_TYPE_AFFILIATE, $affiliate_id);
		        }
			}

			$mode = '';
			$user  = '';
			if ($affiliateDetails['modeOfContact'] == "email") {
				$user = $affiliateDetails['email'];
				$mode = "Email";
			} elseif ($affiliateDetails['modeOfContact'] == "phone") {
				$user = $affiliateDetails['phone'];
				$mode = "Phone";
			} elseif ($affiliateDetails['modeOfContact'] == "mobile") {
				$user = $affiliateDetails['mobile'];
				$mode = "Mobile";
			} elseif ($affiliateDetails['modeOfContact'] == "im") {
				if ($affiliateDetails['imType1'] == "QQ") {
					$user = $affiliateDetails['im1'];
					$mode = "QQ";
				} elseif ($affiliateDetails['imType1'] == "Skype") {
					$user = $affiliateDetails['im1'];
					$mode = "Skype";
				} elseif ($affiliateDetails['imType1'] == "MSN") {
					$user = $affiliateDetails['im1'];
					$mode = "MSN";
				}
			}

			$contactTypeLabel = $this->config->item('aff_contact_type_label');
			$contactType = $this->config->item('aff_contact_type');
			$message = lang('con.23');
			if(!empty($this->config->item('aff_contact_qq'))) {
				$message .= '<br/><b>'.lang('aff.login.contact.qq').'</b>: '.$this->config->item('aff_contact_qq');
			}
			if(!empty($this->config->item('aff_contact_skype'))) {
				$message .= '<br/><b>'.lang('aff.login.contact.skype').'</b>: '.$this->config->item('aff_contact_skype');
			}
			if(!empty($this->config->item('aff_contact_wechat'))) {
				$message .= '<br/><b>'.lang('aff.login.contact.wechat').'</b>: '.$this->config->item('aff_contact_wechat');
			}
			if(!empty($this->config->item('aff_contact_email'))) {
				$message .= '<br/><b>'.lang('aff.login.contact.email').'</b>: '.$this->config->item('aff_contact_email');
			}
			if(!empty($this->config->item('aff_contact_whatsapp'))) {
				$message .= '<br/><b>'.lang('aff.login.contact.whatsapp').'</b>: '.$this->config->item('aff_contact_whatsapp');
			}
			if(!empty($contactTypeLabel) && !empty($contactType)) {
				$message = lang('con.23') . '<br><b>' . lang('aff.aai93') . '</b> ' . $contactTypeLabel . ': ' . $contactType . '<br><b>';
				if (!empty($this->config->item('aff_contact_email'))) {
					$message .= lang('con.22') . '</b> <a href="mailto:' . $this->config->item('aff_contact_email') . '" style="color: #fff;">' . $this->config->item('aff_contact_email') . '</a>';
				}
			}
			if($this->utils->getConfig('full_custom_affiliate_message_after_registration')){
				$message = lang('full_custom_affiliate_message_after_registration');
			}

            if (!$this->utils->isEnabledFeature('hide_affiliate_message_login_form')){
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message); //will set and send message to the user
            }

			if ($this->uri->segment('3') == 'subaffiliate') {
				redirect("affiliate/subaffiliates");
			} else {
				redirect("affiliate");
			}
		}
	}

	/**
	 * overview : callback check IM1
	 *
	 * @return	int
	 */
	public function checkIM1Type() {
		$im = $this->input->post('im1');
		$imtype = $this->input->post('imtype1');

        if ($im == null && $imtype != null) {
			$this->form_validation->set_message('checkIM1Type', lang('mod.provIm1'));
			return false;
		}

		return true;
	}

	/**
	 * overview : callback check IM2
	 * @return bool
	 */
	public function checkIM2Type() {
		$im = $this->input->post('im2');
		$imtype = $this->input->post('imtype2');

		if (preg_match('/[!#$%^&*()+=?.,~|{}:;<>`]/', $im) || preg_match("^/^", $im) || preg_match("^'^", $im) || preg_match('^"^', $im)) {
			$this->form_validation->set_message('checkIM2Type', "Only a-z, A-Z, 0-9, -, _, and @ is allowed.");
			return false;
		} else if ($im == null && $imtype != null) {
			$this->form_validation->set_message('checkIM2Type', lang('mod.provIm2'));
			return false;
		}

		return true;
	}

	/**
	 * overview : callback check mode of contact
	 *
	 * @return	bool
	 */
	public function checkModeOfContact() {
		$mobile = $this->input->post('mobile');
		$phone = $this->input->post('phone');
		$im1 = $this->input->post('im1');
		$im2 = $this->input->post('im2');

		$mode_of_contact = $this->input->post('mode_of_contact');

		if ($mode_of_contact == 'mobile' && $mobile == null) {
			$this->form_validation->set_message('checkModeOfContact', lang('mod.mobilePhoneAsContact'));
			return false;
		} else if ($mode_of_contact == 'phone' && $phone == null) {
			$this->form_validation->set_message('checkModeOfContact', lang('mod.phoneAsContact'));
			return false;
		} else if ($mode_of_contact == 'im' && ($im1 == null && $im2 == null)) {
			$this->form_validation->set_message('checkModeOfContact', lang('mod.instantMsgAsContact'));
			return false;
		}

		return true;
	}

	/**
	 * overview : callback check Age
	 *
	 * @return	bool
	 */
	public function checkAge() {
        $birthday = $this->input->post('birthday');
        $today = new DateTime();
        $birthday = new DateTime($birthday);
        $interval = $today->diff($birthday);
        $age = (int)$interval->format('%y');

        if ($age < 18) {
            $this->form_validation->set_message('checkAge', lang('mod.mustbe18YearsAbove'));
            return false;
        }

        return true;
	}

	/**
	 * overview : add to affiliate payout
	 *
	 * @return	int
	 */
	public function addToAffiliatePayout() {
		$affiliatepayout = array(
			'minimum' => '',
			'maximum' => '',
			'paymentOption' => '',
		);

		return $this->affiliate_manager->addAffiliatePayout($affiliatepayout);
	}

	protected $is_unique_trackingCode = false;

	/**
	 * overview : add to affiliates
	 *
	 * @param $affiliate_payout_id
	 * @return mixed
	 * @throws Exception
	 */
	protected function addToAffiliates($username) {
		$this->load->model(array('affiliatemodel'));

		$this->affiliatemodel->startTrans();

		$today = date("Y-m-d H:i:s");
		$is_unique_trackingCode = false;
		while (!$is_unique_trackingCode) {
			if ($this->utils->isEnabledFeature('affiliate_tracking_code_numbers_only')) {
				$this->load->helper('string');
				$trackingCode = random_string('numeric', 8);
			} else {
				$trackingCode = $this->affiliatemodel->randomizer('trackingCode');
			}
			$is_unique_trackingCode = !$this->checkTrackingCode($trackingCode);
		}

		$ip_address = $this->input->ip_address();
		$geolocation = $this->utils->getGeoplugin($ip_address);

		$parentId = 0;
		if ($this->input->post('parentId') != NULL) {
			$parentId = $this->input->post('parentId');
		}
		($this->input->post('lastname')) ? $lastname = $this->input->post('lastname'): $lastname = "";
		($this->input->post('firstname')) ? $firstname = $this->input->post('firstname'): $firstname = "";
		$data = array(
			'parentId' => $parentId,
			'affiliatePayoutId' => 0,
			'username' => $username,
			'password' => $this->salt->encrypt($this->input->post('password'), $this->getDeskeyOG()),
			'firstname' => $firstname,
			'lastname' => $lastname,
			'birthday' => $this->input->post('birthday'),
			'gender' => $this->input->post('gender'),
			'company' => $this->input->post('company'),
			'occupation' => $this->input->post('occupation'),
			'email' => $this->input->post('email') ?: '',
			'city' => $this->input->post('city'),
			'address' => $this->input->post('address'),
			'zip' => $this->input->post('zip'),
			'state' => $this->input->post('state'),
			'country' => $this->input->post('country'),
			'mobile' => $this->input->post('dialing_code').' '.$this->input->post('mobile'),
			'phone' => $this->input->post('dialing_code2').' '.$this->input->post('phone'),
			'im1' => $this->input->post('im1'),
			'imType1' => $this->input->post('imtype1'),
			'im2' => $this->input->post('im2'),
			'imType2' => $this->input->post('imtype2'),
			'modeOfContact' => $this->input->post('mode_of_contact'),
			'website' => $this->input->post('website'),
			'currency' => $this->input->post('currency'),
			'status' => '1',
			'ip_address' => $ip_address,
			'location' => $geolocation['geoplugin_countryName'],
			'createdOn' => $today,
			'trackingCode' => $trackingCode,
			'language' => $this->input->post('language'),
		);
		foreach ($data as $key => $value) {
			$data[$key] = $this->stripHTMLtags($value);
		}
		
		$affId = $this->affiliatemodel->addAffiliate($data);

		$succ = $this->affiliatemodel->endTransWithSucc();
		if (!$succ) {
			return null;
		}
		return $affId;
	}

	/**
	 * overview : check if trackingCode is unique
	 *
	 * @param	string
	 * @return	void
	 */
	public function checkTrackingCode($trackingCode) {
		$result = $this->affiliate_manager->checkTrackingCode($trackingCode);

		if ($result == false) {
			$this->is_unique_trackingCode = true;
		}
	}

	/**
	 * overview : confirm Password Validation Callback
	 *
	 * @return	bool
	 */
	public function confirmPassword() {
		$password = $this->input->post('password');
		$confirm_password = $this->input->post('confirm_password');

		if ($password != $confirm_password) {
			$this->form_validation->set_message('confirmPassword', "%s " . lang('mod.didntMatch') . "");
			return false;
		}

		return true;
	}

	/**
	 * overview : traffic statistics page
	 *
	 * @return	void
	 */
	public function viewTrafficStatistics() {
		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else {

			$data['title'] = lang('Affiliate Statistics');

			$data['conditions'] = $this->safeLoadParams(array(
				'by_date_from' => $this->utils->getTodayForMysql() . ' 00:00:00',
				'by_date_to' => $this->utils->getTodayForMysql() . ' 23:59:59',
				'by_username' => '',
				'show_game_platform' => false,
			));

			$data['conditions']['enable_date'] = $this->safeGetParam('enable_date', false, true);

			$affiliate_id = $this->getSessionAffId();
			$this->loadTemplate($data['title'], '', '');
			$this->template->add_js('resources/js/bootstrap-switch.min.js');
			$this->template->add_css('resources/css/bootstrap-switch.min.css');
			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'traffic_stats/view_traffic_stats', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : dashboard
	 */
	public function dashboard() {
		$this->affiliateDashboard();
	}

	/**
	 * overview : traffic statistics page
	 *
	 * @return	void
	 */
	public function affiliateDashboard() {
		if (!$this->checkLogin()) {
			redirect('affiliate');
		} else if ($this->utils->is_readonly()) {
			redirect('affiliate/traffic_stats');
		} else {
            $this->load->library(['affiliate_commission', 'affiliate_manager']);
			$this->load->model(array('player_model','affiliatemodel', 'total_player_game_day'));

			$affiliate_id = $this->session->userdata('affiliateId');
			$date 		  = date('Y-m-d');
			$start_date   = "{$date} 00:00:00";
			$end_date 	  = "{$date} 23:59:59";

			$params = array(
				'createdOn >=' => $start_date,
				'createdOn <=' => $end_date,
			);

			# GET DOWNLINE AFFILIATES
			$subaffiliates_id = $this->affiliatemodel->getAllAffiliatesUnderAffiliate($affiliate_id);
			$subaffiliates_id_today = $this->affiliatemodel->getAllAffiliatesUnderAffiliate($affiliate_id, $start_date, $end_date);

			# GET DOWNLINE PLAYERS
            if ($this->utils->isEnabledFeature('dashboard_count_direct_affiliate_player')) {
				$players_id = $this->affiliatemodel->getAllPlayersUnderAffiliateId($affiliate_id);
				$players_id_today = $this->affiliatemodel->getAllPlayersUnderAffiliateId($affiliate_id, $start_date, $end_date);
            } else {
                $all_subaffiliates_id = $this->affiliatemodel->includeAllDownlineAffiliateIds($affiliate_id);
				$players_id = $this->affiliatemodel->includeAllDownlinePlayerIds($all_subaffiliates_id);
				$players_id_today = $this->affiliatemodel->includeAllDownlinePlayerIds($all_subaffiliates_id, $params);
            }

            $data['is_login_behavior'] = ($this->session->flashdata('is_login_behavior')) ? 1 : 0;
            $data['bank_card_exists'] = ($this->affiliatemodel->getPaymentMethod($affiliate_id)) ? 1: 0;
			$data['earnings'] = $this->affiliatemodel->getAllMonthlyEarningsById($affiliate_id);
			$data['affiliate'] = $this->affiliatemodel->getAffiliateById($affiliate_id);
			$data['sublink'] = $this->utils->getSystemUrl('aff') . '/' . $this->config->item('aff_sub_affiliate_link') . '/'. $data['affiliate']['trackingCode'];
			$data['domain'] = $this->affiliatemodel->getAffiliateDomain($affiliate_id);
			$data['player_register_uri'] = $this->utils->getConfig('player_register_uri');
			$data['aff_additional_domain_list'] = $this->emptyOrArray($this->affiliatemodel->getAdditionalDomainList($affiliate_id));
			$data['commonSettings'] = $this->affiliatemodel->getAffTermsSettings($affiliate_id);
			$data['aff_source_code_list'] = $this->emptyOrArray($this->affiliatemodel->getSourceCodeList($affiliate_id));
			$first_domain = null;
			if (!empty($data['domain'])) {
				$first_domain = $data['domain'][0]['domainName'];
			}
			$data['first_domain'] = $first_domain;

			$data['total_players'] 				= count($players_id);
			$data['today_players_today'] 		= count($players_id_today);
			$data['total_deposit'] 				= $this->utils->roundCurrencyForShow($this->player_model->getPlayersTotalDeposit($players_id));
			$data['total_withdraw'] 			= $this->utils->roundCurrencyForShow($this->player_model->getPlayersTotalWithdraw($players_id));

			$data['total_subaffiliates'] 		= count($subaffiliates_id);
			$data['today_subaffiliates_today'] 	= count($subaffiliates_id_today);

            $data['today_deposit'] 		        = $this->utils->roundCurrencyForShow($this->player_model->getPlayersTotalDeposit($players_id, $date, $date));
            $data['today_withdraw'] 		    = $this->utils->roundCurrencyForShow($this->player_model->getPlayersTotalWithdraw($players_id, $date, $date));

            // if($this->utils->isEnabledFeature('enable_new_dashboard_statistics')){
            //     $data['active_players_today']       = count($players_id) > 0 ? $this->total_player_game_day->getTodayActivePlayers($players_id) : 0;
            //     $first_day_of_month                 = $this->utils->getFirstDateOfCurrentMonth();
            //     $data['active_players_this_month']  = count($players_id) > 0 ? $this->total_player_game_day->getActivePlayersCountByDateGroupByPlayerId($first_day_of_month, $date, $players_id) : 0;
            //     $data['this_month_deposit'] 		= $this->utils->roundCurrencyForShow($this->player_model->getPlayersTotalDeposit($players_id, $first_day_of_month, $date));
            //     $data['this_month_withdraw'] 		= $this->utils->roundCurrencyForShow($this->player_model->getPlayersTotalWithdraw($players_id, $first_day_of_month, $date));

            //     list($today_gross, $today_bonus, $today_transactions, $today_net_rev) =
            //         $this->affiliate_commission->get_grossRevenue_bonus_transaction_netRevenue($affiliate_id, $players_id, $start_date, $end_date);

            //     $start_date_of_month = "{$first_day_of_month} 00:00:00";
            //     list($this_month_gross, $this_month_bonus, $this_month_transactions, $this_month_net_rev) =
            //         $this->affiliate_commission->get_grossRevenue_bonus_transaction_netRevenue($affiliate_id, $players_id, $start_date_of_month, $end_date);

            //     $data['today_gross_rev']            = $today_gross;
            //     $data['this_month_gross_rev']       = $this_month_gross;

            //     $data['today_bonus']                = $today_bonus;
            //     $data['this_month_bonus']           = $this_month_bonus;

            //     $data['today_transaction_fee']      = $today_transactions;
            //     $data['this_month_transaction_fee'] = $this_month_transactions;

            //     $data['today_net_rev']              = $today_net_rev;
            //     $data['this_month_net_rev']         = $this_month_net_rev;
            // }

			$this->loadTemplate(lang('Affiliate'), '', '');
            $this->template->add_css('resources/css/dashboard.css');
			$this->template->add_js2($this->utils->thirdpartyUrl('amcharts/amcharts.js'));
			$this->template->add_js2($this->utils->thirdpartyUrl('amcharts/serial.js'));
			$this->template->add_js2($this->utils->thirdpartyUrl('amcharts/light.js'));
			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'affiliate/dashboard', $data);
			$this->template->render();
		}
	}

	/**
	 * Ajax loader for new aff dashboard, OGP-12683
	 * Generates dataset in 3 groups for new aff dashboard
	 * New aff dashboard: enabled by sys feature enable_new_dashboard_statistics
	 * @see		sys feature 'enable_new_dashboard_statistics'
	 * @see		aff/application/views/affiliate/dashboard_stats_new.php
	 * @param	int		$calc_group		1-3: for dashboard data groups
	 *                          		32767: clear session cache
	 * @return	json	data group when calc_group = 1-3;
	 *                  remains of session after clearing when calc_group = 32767
	 */
	public function aff_dashboard_new_ajax($calc_group = 1) {
		// Magic number for secret session clear function
		if ($calc_group == 32767) {
			$this->session->unset_userdata('aff_dashboard_pid_group');
			$this->session->unset_userdata('aff_dashboard_dset_part_1');
			$this->session->unset_userdata('aff_dashboard_dset_part_2');
			$this->session->unset_userdata('aff_dashboard_dset_part_3');

			$data = $this->session->all_userdata();

			$this->returnJsonResult($data);
			return;
		}

		$LIFETIME_DATASET = 14400;
		$SESS_KEY_DSET = 'aff_dashboard_dset_part_';
		$SESS_KEY_PIDG = 'aff_dashboard_pid_group';

		$req_id = $this->input->get('req_id');
		$force_refresh = $this->input->get('refresh');

		$affiliate_id = $this->session->userdata('affiliateId');
		$date 		  = date('Y-m-d');
		$start_date   = "{$date} 00:00:00";
		$end_date 	  = "{$date} 23:59:59";

		$ret = [ 'success' => false, 'result' => null ];

		$params = array(
			'createdOn >=' => $start_date,
			'createdOn <=' => $end_date,
		);

		$timing = [ 'pidg' => -1, 'calc' => -1 ];

		// Generating player_id group (playerIds under current affiliate and its all downlines)

		$timing['pidg'] = microtime(1);

		$pid_group = $this->session->userdata($SESS_KEY_PIDG);
		$pid_group_expiry = $this->utils->safeGetArray($pid_group, 'expiry');
		$pid_group_req_id = $this->utils->safeGetArray($pid_group, 'req_id');
		/**
		 * Use cache if:
		 * (1) req_id from req = req_id from sess
		 * (2) OR not force refresh
		 * (3) AND expiry > time()
		 */
		if ( $pid_group_req_id == $req_id || ( empty($force_refresh) && $pid_group_expiry > time() ) ) {
			$players_id			= $pid_group['players_id'];
			$players_id_today	= $pid_group['players_id_today'];
		}
		else {
		    if ($this->utils->isEnabledFeature('dashboard_count_direct_affiliate_player')) {
				$players_id = $this->affiliatemodel->getAllPlayersUnderAffiliateId($affiliate_id);
				$players_id_today = $this->affiliatemodel->getAllPlayersUnderAffiliateId($affiliate_id, $start_date, $end_date);
	        } else {
	            $all_subaffiliates_id = $this->affiliatemodel->includeAllDownlineAffiliateIds($affiliate_id);
				$players_id = $this->affiliatemodel->includeAllDownlinePlayerIds($all_subaffiliates_id);
				$players_id_today = $this->affiliatemodel->includeAllDownlinePlayerIds($all_subaffiliates_id, $params);
	        }

	        $pid_group = [
	        	'players_id'		=> $players_id ,
	        	'players_id_today'	=> $players_id_today ,
	        	'expiry'			=> time() + $LIFETIME_DATASET ,
	        	'req_id'			=> $req_id
	        ];

	        $this->session->set_userdata($SESS_KEY_PIDG, $pid_group);
		}

		$timing['pidg'] = number_format((microtime(1) - $timing['pidg']), 3);

		// -----

		$timing['calc'] = microtime(1);

		$SESS_KEY_DSET_GROUP = $SESS_KEY_DSET . $calc_group;

		$dset_part = $this->session->userdata($SESS_KEY_DSET_GROUP);
		$dset_part_expiry = $this->utils->safeGetArray($dset_part, 'expiry');

		// Use cache if !(force_fresh) AND !(expired)
		if (empty($force_refresh) && time() < $dset_part_expiry) {
			$res = $dset_part['res'];
		}
		else {
			$first_day_of_month = $this->utils->getFirstDateOfCurrentMonth();

			switch ($calc_group) {
				case 1 :
					$this->load->model([ 'total_player_game_day' ]);
					$res = [
						'active_players_today'		=> count($players_id) > 0 ? $this->total_player_game_day->getTodayActivePlayers($players_id) : 0 ,
			        	'active_players_this_month'	=> count($players_id) > 0 ? $this->total_player_game_day->getActivePlayersCountByDateGroupByPlayerId($first_day_of_month, $date, $players_id) : 0 ,
			        	'deposit_this_month'		=> $this->utils->roundCurrencyForShow($this->player_model->getPlayersTotalDeposit($players_id, $first_day_of_month, $date)) ,
			        	'withdraw_this_month'		=> $this->utils->roundCurrencyForShow($this->player_model->getPlayersTotalWithdraw($players_id, $first_day_of_month, $date))
			        ];

			        break;

			    case 2 :
			    	$this->load->library([ 'affiliate_commission' ]);
			        list($today_gross, $today_bonus, $today_transactions, $today_net_rev) =
			            $this->affiliate_commission->get_grossRevenue_bonus_transaction_netRevenue($affiliate_id, $players_id, $start_date, $end_date);
			        $res = [
			        	'gross_rev_today'		=> $this->utils->roundCurrencyForShow($today_gross) ,
			        	'bonus_today'			=> $this->utils->roundCurrencyForShow($today_bonus) ,
			        	'tx_fee_today'			=> $this->utils->roundCurrencyForShow($today_transactions) ,
			        	'net_rev_today'			=> $this->utils->roundCurrencyForShow($today_net_rev)
			        ];

			        break;

			    case 3 :
			    	$this->load->library([ 'affiliate_commission' ]);
			        $start_date_of_month = "{$first_day_of_month} 00:00:00";
			        list($this_month_gross, $this_month_bonus, $this_month_transactions, $this_month_net_rev) =
			            $this->affiliate_commission->get_grossRevenue_bonus_transaction_netRevenue($affiliate_id, $players_id, $start_date_of_month, $end_date);

			        $res = [
			        	'gross_rev_this_month'	=> $this->utils->roundCurrencyForShow($this_month_gross) ,
			        	'bonus_this_month'		=> $this->utils->roundCurrencyForShow($this_month_bonus) ,
			        	'tx_fee_this_month'		=> $this->utils->roundCurrencyForShow($this_month_transactions) ,
			        	'net_rev_this_month'	=> $this->utils->roundCurrencyForShow($this_month_net_rev)
			        ];

			        break;

			} // End switch ($calc_group)

			$dset_part = [ 'res' => $res , 'created' => time(), 'expiry' => time() + $LIFETIME_DATASET ];

			$this->session->set_userdata($SESS_KEY_DSET_GROUP, $dset_part);
		}

		$timing['calc'] = number_format((microtime(1) - $timing['calc']), 3);

		$ret = [ 'success' => true, 'result' => $res, 'timing' => $timing ];

		$this->returnJsonResult($ret);
	}

	/**
	 * overview : traffic statistics real players click
	 *
	 * @return	void
	 */
	public function players($traffic_id) {
		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else {
			$this->loadTemplate('Affiliate', '', '');

			$data['players'] = $this->affiliate_manager->getPlayers($traffic_id);

			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'traffic_stats/view_players', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : traffic statistics deposit players click
	 *
	 * @param $traffic_id
	 * @return	void
	 */
	public function depositPlayers($traffic_id) {
		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else {
			$this->loadTemplate('Affiliate', '', '');

			$data['players'] = $this->affiliate_manager->getPlayersDeposit($traffic_id);

			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'traffic_stats/view_deposits', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : monthly earnings page
	 *
	 * @return	void
	 */
	public function affiliateEarnings() {
		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else if ($this->utils->is_readonly()) {
			redirect('affiliate/traffic_stats');
		} else {

			$this->load->model(array('game_type_model', 'game_logs', 'external_system', 'player_model', 'affiliatemodel'));

			$affiliate_id = $this->session->userdata('affiliateId');
			if ($this->utils->isEnabledFeature('switch_to_affiliate_platform_earnings')) {
				// $data['earnings'] = $this->affiliatemodel->getAllPlatformEarningsById($affiliate_id);
			} else if ($this->utils->isEnabledFeature('switch_to_affiliate_daily_earnings')) {
				$data['earnings'] = $this->affiliatemodel->getAllDailyEarningsById($affiliate_id);
			} else {
                $data['commonSettings'] = $this->affiliatemodel->getDefaultAffSettings();
				$data['earnings'] = $this->affiliatemodel->getAllMonthlyEarningsById($affiliate_id);
			}

			$data['game_platforms'] = $this->external_system->getAllActiveSytemGameApi();
			$data['game_types'] = $this->game_type_model->getGameTypesForDisplay();
			$data['player_levels'] = $this->player_model->getAllPlayerLevels();

			$data['conditions'] = $this->safeLoadParams(array(
			    'affiliate_id' => $affiliate_id,
			    'start_date' => date('Y-m-d', strtotime('-1 day')),
			    'end_date' => date('Y-m-d', strtotime('-1 day')),
			    'game_platform_id' => array(),
			));

			$this->loadTemplate('Affiliate', '', '');
			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'earnings/view_earnings', $data);
			$this->template->render();
		}
	}

	public function aff_user_earnings_3(){
        $this->load->model(array('report_model'));

        $request = $this->input->post();
        $is_export = false;
        $result = $this->report_model->aff_user_earnings_3($request, $is_export);
        $this->returnJsonResult($result);
    }

	public function ip_history($player_id) {
		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else if ($this->utils->is_readonly()) {
			redirect('affiliate/traffic_stats');
		} else {

			$this->load->model('player_model');

			$affiliate_id = $this->getSessionAffId();

			$player = $this->player_model->getPlayerArrayById($player_id);

			if (isset($player['affiliateId']) && $player['affiliateId'] == $affiliate_id) {

				$data['player'] = $player;

				$this->loadTemplate('Affiliate', '', '');
				$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
				$this->template->write_view('main_content', 'earnings/ip_history', $data);
				$this->template->render();

			} else show_404();
		}
	}

	/**
	 * overview : display earnings page
	 *
	 * @return	void
	 */
	public function showEarnings() {
		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else {
			$period = $this->input->post('period');
			$start_date = $this->input->post('dateRangeValueStart');
			$end_date = $this->input->post('dateRangeValueEnd');
			$date_range_value = date("F j, Y", strtotime($start_date)) . ' - ' . date("F j, Y", strtotime($end_date));

			$this->session->set_userdata(array(
				'period' => $period,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'date_range_value' => $date_range_value,
			));

			if ($period == "daily") {
				$this->viewEarningsDaily($start_date, $end_date);
			} elseif ($period == "weekly") {
				$this->viewEarningsWeekly($start_date, $end_date);
			} elseif ($period == "monthly") {
				$this->viewEarningsMonthly($start_date, $end_date);
			} elseif ($period == "yearly") {
				$this->viewEarningsYearly($start_date, $end_date);
			} else {
				$this->viewEarningsToday($start_date);
			}
		}
	}

	/**
	 * overview : view Monthly Earnings Today
	 *
	 * @param 	int
	 * @return	redirect
	 */
	public function viewEarningsToday($date) {
		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else {
			$this->loadTemplate('Affiliate', '', '');

			if ($date != null) {
				$date = urldecode($date);
				$start_date = $date . " 00:00:00";
				$end_date = $date . " 23:59:59";

				$data['earnings'] = $this->affiliate_manager->getDailyEarnings($start_date, $end_date);
			} else {
				$data['earnings'] = $this->affiliate_manager->getEarnings();
			}

			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'earnings/view_earnings', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : monthly earnings daily page
	 *
	 * @return	void
	 */
	public function viewEarningsDaily($start_date, $end_date) {
		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else {
			$this->loadTemplate('Affiliate', '', '');

			$data['earnings'] = $this->affiliate_manager->getDailyEarnings($start_date, $end_date);

			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'earnings/view_earnings', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : monthly earnings weekly page
	 *
	 * @param $start_date
	 * @param $end_date
	 * @return	void
	 */
	public function viewEarningsWeekly($start_date, $end_date) {
		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else {
			$this->loadTemplate('Affiliate', '', '');

			$data['earnings'] = $this->affiliate_manager->getWeeklyEarnings($start_date, $end_date);

			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'earnings/view_weekly_earnings', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : monthly earnings monthly page
	 *
	 * @return	void
	 */
	public function viewEarningsMonthly($start_date, $end_date) {
		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else {
			$this->loadTemplate('Affiliate', '', '');

			$data['earnings'] = $this->affiliate_manager->getMonthlyEarnings($start_date, $end_date);

			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'earnings/view_monthly_earnings', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : monthly earnings yearly page
	 *
	 * @return	void
	 */
	public function viewEarningsYearly($start_date, $end_date) {
		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else {
			$this->loadTemplate('Affiliate', '', '');

			$data['earnings'] = $this->affiliate_manager->getYearlyEarnings($start_date, $end_date);

			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'earnings/view_yearly_earnings', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : modify password  page
	 *
	 * @return	void
	 */
	public function modifyPassword() {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			redirect('affiliate', 'refresh');
		} else {
			$this->loadTemplate('Affiliate - Settings', '', '');

			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'settings/modify_password');
			$this->template->render();
		}
	}

	/**
	 * overview : modify password  page
	 *
	 * @return	void
	 */
	public function modifySecondPassword() {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			redirect('affiliate', 'refresh');
		} else {

			$affiliate_id = $this->getSessionAffId();
			$affiliate = $this->affiliatemodel->getAffiliateById($affiliate_id);

			$data['new'] = empty($affiliate['second_password']);

			if ($data['new']) {
        		$referrer = $this->agent->referrer();
		        if ($referrer != base_url('/affiliate/modifySecondPassword')) {
		        	$this->session->set_userdata('next_uri', $referrer);
		        }
		        $data['next_uri'] = $this->session->userdata('next_uri');
			}

			$this->loadTemplate('Affiliate - Settings', '', '');
			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'settings/modify_second_password', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : verify change password
	 *
	 * @return	void
	 */
	public function verifyChangePassword() {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}

		$this->form_validation->set_rules('old_password', lang('mod.oldpass'), 'trim|required|xss_clean|callback_checkIfOldPasswordCorrect');
		$this->form_validation->set_rules('new_password', lang('mod.newpass'), 'trim|required|xss_clean');
		$this->form_validation->set_rules('confirm_new_password', lang('mod.confirmpass'), 'trim|required|xss_clean|callback_checkIfPasswordMatch');

		if ($this->form_validation->run() == false) {
			$this->modifyPassword();
		} else {

			$this->load->model(array('queue_result','operatorglobalsettings', 'affiliatemodel'));

			$affiliate_id = $this->session->userdata('affiliateId');
			$password = $this->salt->encrypt($this->input->post('new_password'), $this->getDeskeyOG());
			$affiliateDetails = $this->affiliate_manager->getAffiliateById($affiliate_id);

			$data = array(
				'password' => $password,
			);

			$this->affiliate_manager->editAffiliates($data, $affiliate_id);

			$username=$this->affiliatemodel->getUsernameById($affiliate_id);
			$this->syncAffCurrentToMDBWithLock($affiliate_id, $username, false);

			$body = "<html><body><p style='color:#222;font-size:13px;font-family:Verdana;'>" . lang('mod.emailMsgPassChg1') . " " . $affiliateDetails['lastname'] . " " . $affiliateDetails['firstname'] . "!</p><br/>
				<p style='color:#222;font-size:13px;font-family:Verdana;'>" . lang('mod.emailMsgPassChg2') . "</p>
				<p style='color:#222;font-size:13px;font-family:Verdana;'>" . lang('mod.emailMsgPassChg3') . ": <b>" . $this->input->post('new_password') . "</b></p>
				<p style='color:#222;font-size:13px;font-family:Verdana;'>" . lang('mod.emailMsgPassChg4') . "</p><br/>
				<p style='color:#222;font-size:13px;font-family:Verdana;'>" . lang('mod.emailMsgPassChg5') . "</p>
				<p style='color:#222;font-size:13px;font-family:Verdana;'>" . lang('mod.emailMsgPassChg6') . "</p><br/>
				<p style='color:rgb(57, 132, 198);font-size:13px;font-family:Verdana;'>" . lang('mod.emailMsgPassChg7') . "</p></body></html>";

			$this->utils->sendMail($affiliateDetails['email'], $this->operatorglobalsettings->getSettingValue('mail_from'), $this->operatorglobalsettings->getSettingValue('mail_from_email'),
				lang('mod.changepass'), $body, Queue_result::CALLER_TYPE_AFFILIATE, $affiliate_id);

			$message = lang('con.17');
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect("affiliate/modifyAccount", "refresh");
		}
	}

	/**
	 * overview : verify change password
	 *
	 * @return	void
	 */
	public function verifyChangeSecondPassword() {
		if ( ! $this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}

		$affiliate_id = $this->getSessionAffId();
		$affiliate = $this->affiliatemodel->getAffiliateById($affiliate_id);

		$old_password 			= $this->input->post('old_password');
		$new_password 			= $this->input->post('new_password');
		$confirm_new_password  	= $this->input->post('confirm_new_password');

		$old_password = $this->input->post('old_password');
		$old_password = $this->utils->encodePassword($old_password);

		if ($new_password == $confirm_new_password && (empty($affiliate['second_password']) || $affiliate['second_password'] == $old_password)) {

			$next_uri = $this->input->post('next_uri') ? : "affiliate/modifyAccount";

			$affiliate_id = $this->session->userdata('affiliateId');
			$data = array('second_password' => $this->utils->encodePassword($new_password));
			$this->affiliatemodel->editAffiliates($data, $affiliate_id);

			$username=$this->affiliatemodel->getUsernameById($affiliate_id);
			$this->syncAffCurrentToMDBWithLock($affiliate_id, $username, false);

			$message = lang('con.17');
			$this->alertMessage(1, $message);

			$this->setEnterSecondPasswordToSession();

			redirect($next_uri, "refresh");
		} else {
			$message = lang('con.04');
			$this->alertMessage(2, $message);
			redirect("affiliate/modifySecondPassword", "refresh");
		}
	}

	/**
	 * overview : callback for old password
	 *
	 * @return	bool
	 */
	public function checkIfOldPasswordCorrect() {
		$old_password = $this->salt->encrypt($this->input->post('old_password'), $this->getDeskeyOG());
		$affiliate_id = $this->session->userdata('affiliateId');

		$result = $this->affiliate_manager->getAffiliateById($affiliate_id);

		if ($old_password != $result['password']) {
			$this->form_validation->set_message('checkIfOldPasswordCorrect', lang('mod.oldPassNotMatch'));
			return false;
		}

		return true;
	}

	/**
	 * overview : call back for confirm password
	 *
	 * @return	bool
	 */
	public function checkIfPasswordMatch() {
		$affiliate_id = $this->session->userdata('affiliateId');

		$result = $this->affiliate_manager->getAffiliateById($affiliate_id);

		$new_password = $this->salt->encrypt($this->input->post('new_password'), $this->getDeskeyOG());
		$confirm_new_password = $this->input->post('confirm_new_password');

		if ($new_password == $result['password']) {
			$this->form_validation->set_message('checkIfPasswordMatch', lang('mod.newPassTheSameOld'));
			return false;
		} else if ($this->input->post('new_password') != $confirm_new_password) {
			$this->form_validation->set_message('checkIfPasswordMatch', lang('mod.confNewPassNotMatch'));
			return false;
		}

		return true;
	}

	/**
	 * overview : edit info
	 *
	 * @return	void
	 */
	public function modifyAccount() {
		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else {
			$this->loadTemplate(lang('Affiliate') . ' - ' . lang('Settings'), '', '');

			$affiliate_id = $this->session->userdata('affiliateId');
			$data['affiliate'] = $this->affiliate_manager->getAffiliateById($affiliate_id);
			$data['domain'] = $this->affiliate_manager->getAllDomain();
			$data['payment'] = $this->affiliate_manager->getPaymentById($affiliate_id);
			$data['player_register_uri'] = $this->config->item('player_register_uri');

			$this->load->model('affiliatemodel');
			// $data['operator_settings'] = $this->affiliatemodel->getAffiliateSettings();
			// $data['sub_affiliate_terms'] = $this->affiliatemodel->getDefaultSubAffiliateTerms();
			// $data['sub_affiliate_term'] = $this->affiliatemodel->getSubAffiliateTermsById($affiliate_id);
			$data['sublink'] = $this->utils->getSystemUrl('aff') . '/' . $this->config->item('aff_sub_affiliate_link') . '/';

			$data['commonSettings'] = $this->affiliatemodel->getAffTermsSettings($affiliate_id);
			$data['aff_additional_domain_list'] = $this->emptyOrArray($this->affiliatemodel->getAdditionalDomainList($affiliate_id));
			$data['aff_source_code_list'] = $this->emptyOrArray($this->affiliatemodel->getSourceCodeList($affiliate_id));
			$data['readonly_accounts'] = $this->affiliatemodel->getReadonlyAccounts($affiliate_id);
			$first_domain = null;
			if (!empty($data['domain'])) {
				$first_domain = $data['domain'][0]['domainName'];
			}
			$data['first_domain'] = $first_domain;

			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'settings/modify_account', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : edit information
	 *
	 * @return	void
	 */
	public function editInfo($affiliate_id) {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate');
		}
		$session_id = $this->session->userdata('affiliateId');
		if($session_id != $affiliate_id) {
			return redirect('affiliate');
		}

		$data['affiliate'] = $this->affiliate_manager->getAffiliateById($affiliate_id);

        $data['mobile_dailing_num'] = $data['phone_dailing_num'] = $data['mobile_num'] = $data['phone_num'] = '';
        if(!empty($data['affiliate']['mobile'])){
            $mobile_detail = explode(' ',$data['affiliate']['mobile']);
            $data['mobile_dailing_num'] = (isset($mobile_detail[0])) ? $mobile_detail[0] : '';
            $data['mobile_num'] = (isset($mobile_detail[1])) ? $mobile_detail[1] : '';
            if(count($mobile_detail) == 1){
                //if no dailing before
                $data['mobile_num'] = (isset($mobile_detail[0])) ? $mobile_detail[0] : '';
            }
        }

        if(!empty($data['affiliate']['phone'])) {
            $phone_detail = explode(' ', $data['affiliate']['phone']);
            $data['phone_dailing_num'] = (isset($phone_detail[0])) ? $phone_detail[0] : '';
            $data['phone_num'] = (isset($phone_detail[1])) ? $phone_detail[1] : '';
            if(count($phone_detail) == 1){
                //if no dailing before
                $data['phone_num'] = (isset($phone_detail[0])) ? $phone_detail[0] : '';
            }
        }

        $data['countryNumList'] = unserialize(COUNTRY_NUMBER_LIST_FULL);
        if(!empty($data['countryNumList'])){
            $data['frequentlyUsedCountryNumList'] = array(
                'China' => $data['countryNumList']['China'],
                'Thailand' => $data['countryNumList']['Thailand'],
                'Indonesia' => $data['countryNumList']['Indonesia'],
                'Vietnam' => $data['countryNumList']['Vietnam'],
                'Malaysia' => $data['countryNumList']['Malaysia'],
            );
        }

        $this->template->add_js($this->utils->thirdpartyUrl('bootstrap-select/1.12.4/bootstrap-select.min.js'));
        $this->template->add_css($this->utils->thirdpartyUrl('bootstrap-select/1.12.4/bootstrap-select.min.css'));

        $this->loadTemplate('Affiliate', '', '');
		$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
		$this->template->write_view('main_content', 'settings/edit_account', $data);
		$this->template->render();
	}

	/**
	 * overview : verify edit information
	 *
	 * @param $affiliate_id
	 */
	public function verifyEditInfo($affiliate_id) {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}

		// $this->form_validation->set_rules('firstname', lang('aff.al14'), 'trim|xss_clean|required');readonly
		// $this->form_validation->set_rules('lastname', lang('aff.al15'), 'trim|xss_clean|required');readonly
		$this->form_validation->set_rules('company', lang('aff.ai06'), 'trim|xss_clean');
		$this->form_validation->set_rules('occupation', lang('aff.ai07'), 'trim|xss_clean');
		// $this->form_validation->set_rules('birthday', lang('aff.ai04'), 'trim|xss_clean|required|callback_checkAge');readonly
		// $this->form_validation->set_rules('gender', lang('aff.ai05'), 'trim|xss_clean|required');readonly
		$this->form_validation->set_rules('city', lang('aff.ai09'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('address', lang('aff.ai10'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('zip', lang('aff.ai11'), 'trim|xss_clean');
		$this->form_validation->set_rules('state', lang('aff.ai12'), 'trim|xss_clean');
		$this->form_validation->set_rules('country', lang('aff.ai13'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('mobile', lang('reg.a54'), 'trim|xss_clean|numeric');
		$this->form_validation->set_rules('phone', lang('aff.ai15'), 'trim|xss_clean|numeric');
		// $this->form_validation->set_rules('im1', lang('aff.ai17'), 'trim|xss_clean|callback_checkIM1Type');readonly
		$this->form_validation->set_rules('imtype1', lang('aff.ai16'), 'trim|xss_clean');
		// $this->form_validation->set_rules('im2', lang('aff.ai19'), 'trim|xss_clean|callback_checkIM2Type');readonly
		$this->form_validation->set_rules('imtype2', lang('aff.ai18'), 'trim|xss_clean');
		$this->form_validation->set_rules('mode_of_contact', lang('aff.ai20'), 'trim|xss_clean|required|callback_checkModeOfContact');
		$this->form_validation->set_rules('website', lang('aff.ai21'), 'trim|xss_clean');
		$this->form_validation->set_rules('currency', lang('aff.aai92'), 'trim|xss_clean|required');

		if ($this->input->post('email') == $this->input->post('email_db')) {
			$this->form_validation->set_rules('email', lang('aff.ai83'), 'trim|xss_clean|required|valid_email');
		} else {
			$this->form_validation->set_rules('email', lang('aff.ai83'), 'trim|xss_clean|required|valid_email|is_unique[affiliates.email]');
		}

		if ($this->form_validation->run() == false) {
            $message = lang('con.aff51');
            $this->alertMessage(2, $message); //will set and send message to the user
			$this->editInfo($affiliate_id);
		} else {
			$data = array(
				// 'firstname' => $this->input->post('firstname'),readonly
				// 'lastname' => $this->input->post('lastname'),readonly
				'company' => $this->input->post('company'),
				'occupation' => $this->input->post('occupation'),
				// 'birthday' => $this->input->post('birthday'),readonly
				// 'gender' => $this->input->post('gender'),readonly
				'email' => $this->input->post('email'),
				'city' => $this->input->post('city'),
				'address' => $this->input->post('address'),
				'zip' => $this->input->post('zip'),
				'state' => $this->input->post('state'),
				'country' => $this->input->post('country'),
                'mobile' => !empty($this->input->post('mobile')) ? $this->input->post('mobile_dialing_code').' '.$this->input->post('mobile') : '',
                'phone' => !empty($this->input->post('phone')) ? $this->input->post('phone_dialing_code').' '.$this->input->post('phone') : '',
				//'im1' => $this->input->post('im1'),readonly
				'imType1' => $this->input->post('imtype1'),
				//'im2' => $this->input->post('im2'),readonly
				'imType2' => $this->input->post('imtype2'),
				'modeOfContact' => $this->input->post('mode_of_contact'),
				'website' => $this->input->post('website'),
				'currency' => $this->input->post('currency'),
			);

			$this->affiliate_manager->editAffiliates($data, $affiliate_id);
			$this->load->model(['affiliatemodel']);
			$username=$this->affiliatemodel->getUsernameById($affiliate_id);
			$this->syncAffCurrentToMDBWithLock($affiliate_id, $username, false);

			$message = lang('con.07');
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect("affiliate/modifyAccount", "refresh");
		}
	}

	/**
	 * overview : activate payment
	 *
	 * @return	void
	 */
	public function activatePayment($payment_id, $bank_name) {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			redirect('affiliate', 'refresh');
		} else {
			$data = array(
				'status' => '0',
				'updatedOn' => date('Y-m-d H:i:s'),
			);
			$this->affiliate_manager->editPayment($data, $payment_id);

			$message = lang('con.08') . ": " . str_replace("%20", " ", $bank_name);
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect('affiliate/modifyAccount');
		}
	}

	/**
	 * overview : deactivate payment
	 *
	 * @return	void
	 */
	public function deactivatePayment($payment_id, $bank_name) {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			redirect('affiliate', 'refresh');
		} else {
			$data = array(
				'status' => '1',
				'updatedOn' => date('Y-m-d H:i:s'),
			);
			$this->affiliate_manager->editPayment($data, $payment_id);

			$message = lang('con.09') . ": " . str_replace("%20", " ", $bank_name);
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect('affiliate/modifyAccount');
		}
	}

	/**
	 * overview : delete payment
	 *
	 * @return	void
	 */
	public function deletePayment($payment_id, $bank_name) {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			redirect('affiliate', 'refresh');
		} else {
			$this->affiliate_manager->deletePaymentInfo($payment_id);

			$message = lang('con.21') . ": " . str_replace("%20", " ", $bank_name);
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect('affiliate/modifyAccount');
		}
	}

	/**
	 * overview : add new payment account
	 *
	 * @return	void
	 */
	public function addNewAccount() {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}
		$this->load->model('banktype');
		$data = (array)$this->affiliate_manager->getBankTypes();

		$affId = $this->getAffIdFromSession();
		$result['affiliate'] = $this->affiliatemodel->getAffiliateById($affId);
		$result['banks'] = array();
		foreach ($data as $key => $row) {
			$result['banks'][] = array(
						"bankTypeId" => $row->bankTypeId,
						"bankName" => $row->bankName,
						'enabled_withdrawal' => $row->enabled_withdrawal,
						);
		}

		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else {
			$this->loadTemplate('Affiliate', '', '');

			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'settings/add_payment_account',$result);
			$this->template->render();
		}
	}

	/**
	 * overview : verify add new payment account
	 *
	 * @return	void
	 */
	public function verifyaddNewAccount() {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}
		$this->load->model('banktype');
		$lang = $this->language_function->getCurrentLanguage();
		$langCode = $this->language_function->getLanguageCode($lang);
		$language = $this->language_function->getLanguage($lang);
		$this->config->set_item('language', $language);
		$selected_bank = (array)$this->affiliate_manager->getBankTypeById($this->input->post('bank_name'));
		$this->form_validation->set_rules('bank_name', lang('pay.bankname'), 'trim|xss_clean|required');
		//$this->form_validation->set_rules('account_info', lang('aff.aai91'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('account_name', lang('aff.ai90'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('account_number', lang('pay.accnum'), 'trim|xss_clean|required|numeric|is_unique[affiliatepayment.accountNumber]');
		$data['payment'] = $this->affiliate_manager->getPaymentById($this->session->userdata('affiliateId'));
		if ($this->form_validation->run() == false) {
			$this->addNewAccount();
		} else {
			if(count($data['payment']) >= 10){
				$message = lang('Maximum payment account is reach!');
				$this->alertMessage(2, $message); //will set and send message to the user
				redirect("affiliate/modifyAccount", "refresh");
			}
			$data = array(
				'affiliateId' => $this->session->userdata('affiliateId'),
				'paymentMethod' => 'Wire Transfer',
				'bankName' => $selected_bank['bankName'],
				'accountInfo' => $this->input->post('account_info'),
				'accountName' => $this->input->post('account_name'),
				'accountNumber' => $this->input->post('account_number'),
				'createdOn' => date('Y-m-d H:i:s'),
				'updatedOn' => date('Y-m-d H:i:s'),
			);
			foreach ($data as $key => $value) {
				$data[$key] = $this->stripHTMLtags($value);
			}
			$this->affiliate_manager->addPayment($data);

			$message = lang('con.10');
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect("affiliate/modifyAccount", "refresh");
		}
	}

	/**
	 * overview : edit payment account
	 *
	 * @return	void
	 */
	public function editPayment($affiliate_payment_id) {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			redirect('affiliate', 'refresh');
		} else {
			$this->loadTemplate('Affiliate', '', '');

			$data['payment'] = $this->affiliate_manager->getPaymentByPaymentId($affiliate_payment_id);

			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'settings/edit_payment_account', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : verify add new payment account
	 *
	 * @param $affiliate_payment_id
	 * @return	void
	 */
	public function verifyEditPayment($affiliate_payment_id) {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}

		$this->form_validation->set_rules('bank_name', lang('pay.bankname'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('account_info', lang('aff.aai91'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('account_name', lang('aff.ai90'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('account_number', lang('pay.accnum'), 'trim|xss_clean|required|numeric|callback_checkAccountNumber');

		if ($this->form_validation->run() == false) {
			$this->editPayment($affiliate_payment_id);
		} else {
			$data = array(
				'affiliateId' => $this->session->userdata('affiliateId'),
				'paymentMethod' => 'Wire Transfer',
				'bankName' => $this->input->post('bank_name'),
				'accountInfo' => $this->input->post('account_info'),
				'accountName' => $this->input->post('account_name'),
				'accountNumber' => $this->input->post('account_number'),
				'updatedOn' => date('Y-m-d H:i:s'),
                'editCount' => ($this->input->post('edit_count') + 1),
			);
            foreach ($data as $key => $value) {
                $data[$key] = $this->stripHTMLtags($value);
            }

			$this->affiliate_manager->editPayment($data, $affiliate_payment_id);

			$message = lang('con.11');
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect("affiliate/modifyAccount", "refresh");
		}
	}

	/**
	 * overview : check account number
	 *
	 * @return bool
	 */
	public function checkAccountNumber() {
		$affiliate_payment_id = $this->input->post('affiliate_payment_id');
		$payment = $this->affiliate_manager->getPaymentByPaymentId($affiliate_payment_id);
		$account_number = $this->input->post('account_number');

		if ($payment['accountNumber'] != $account_number) {
			$this->form_validation->set_rules('account_number', lang('pay.accnum'), 'is_unique[affiliatepayment.accountNumber]');

			if ($this->form_validation->run() == false) {
				$this->form_validation->set_message('checkAccountNumber', "The " . lang('pay.accnum') . " field must contain a unique value.");
				return false;
			}
		}

		return true;
	}

	/**
	 * overview : payment history page
	 *
	 * @return	void
	 */
	public function paymentHistory() {
		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else if ($this->utils->is_readonly()) {
			redirect('affiliate/traffic_stats');
		} else {
			$this->loadTemplate(lang('Affiliate'), '', '');
			$this->load->model(array('transactions'));
			$affId = $this->getAffIdFromSession();
			// $data['payments'] = $this->affiliate_manager->getPaymentHistory(null);
			$data['transactions'] = $this->transactions->getAffTransactions($affId);

			$this->template->add_js('resources/js/highlight.pack.js');
			$this->template->add_css('resources/css/hljs.tomorrow.css');
			$this->template->add_js('resources/js/json2.min.js');
			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'payment/view_payment', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : display payment history page
	 *
	 * @return	void
	 */
	public function displayPaymentHistory() {
		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else {
			$this->loadTemplate('Affiliate', '', '');

			$signup_range = null;
			$period = null;

			if ($this->input->post('start_date') && $this->input->post('end_date') && $this->input->post('time_period') == 'specify') {
				if ($this->input->post('start_date') < $this->input->post('end_date')) {
					$signup_range = "'" . $this->input->post('start_date') . "' AND '" . $this->input->post('end_date') . "'";
				} else {
					$message = lang('con.06');
					$this->alertMessage(2, $message);
					return;
				}
			} else {
				$period = $this->input->post('time_period');
			}

			$search = array(
				'affiliateId' => $this->session->userdata('affiliateId'),
				'sign_time_period' => $period,
				'signup_range' => $signup_range,
				'status' => $this->input->post('status'),
			);

			$data['payments'] = $this->affiliate_manager->getPaymentHistory($search);

			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'payment/view_payment', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : banner settings page
	 *
	 * @return	void
	 */
	public function bannerLists() {
		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else {
			$this->loadTemplate('Affiliate', '', '');

			$this->load->model(['affiliatemodel']);

			$data['banner'] = $this->affiliatemodel->getSearchBanner([]);
			$data['domain'] = $this->affiliate_manager->getAllDomain();

			$this->session->unset_userdata(array(
				'start_date' => '',
				'end_date' => '',
				'date_range_value' => '',
			));

			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'banner/view_banner', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : search banner list
	 */
	public function searchBannerLists() {
		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else {
			$this->loadTemplate('Affiliate', '', '');

			$start_date = $this->input->post('dateRangeValueStart');
			$end_date = $this->input->post('dateRangeValueEnd');
			$date_range_value = date("F j, Y", strtotime($start_date)) . ' - ' . date("F j, Y", strtotime($end_date));

			$this->session->set_userdata(array(
				'start_date' => $start_date,
				'end_date' => $end_date,
				'date_range_value' => $date_range_value,
			));

			$signup_range = null;
			$period = null;

			if ($this->input->post('dateRangeValueStart') && $this->input->post('dateRangeValueEnd') && $this->input->post('time_period') == 'specify') {
				if ($this->input->post('dateRangeValueStart') < $this->input->post('dateRangeValueEnd')) {
					$signup_range = "'" . $this->input->post('dateRangeValueStart') . "' AND '" . $this->input->post('dateRangeValueEnd') . "'";
				} else {
					$message = lang('con.06');
					$this->alertMessage(2, $message);
					return;
				}
			} else {
				$period = $this->input->post('time_period');
			}

			$search = array(
				'sign_time_period' => $period,
				'signup_range' => $signup_range,
				'status' => $this->input->post('status'),
			);

			$data['banner'] = $this->affiliate_manager->getAllBanner($search, null, null);
			$data['domain'] = $this->affiliate_manager->getAllDomain();

			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'banner/view_banner', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : download banner
	 * @param $name
	 * @param $path
	 */
	public function downloadBanner($name, $path) {
		$this->load->helper('download');

		$image_name = rawurldecode($name);
		$image_path = APPPATH . '../public/resources/images/banner/' . $image_name;
		$this->utils->debug_log($image_path);
		// $mime = explode('.', $image_name);

		// header('Content-Type: image/' . $mime[1]);
		// header("Content-Disposition: attachment; filename=$image_name");
		force_download($image_name, file_get_contents($image_path));
	}

	/**
	 * overview : download banner
	 *
	 * @param $id
	 */
	public function download_banner($id){
		$this->load->helper(array('url', 'form', 'download'));

		// $this->load->helper('download');
		$this->load->model(['affiliatemodel']);
		$localPath=$this->affiliatemodel->getBannerLocalPathById($id);

		$data = file_get_contents($localPath);
		$ext = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
		$name = $id.'.'.$ext;

		force_download($name, $data);
	}

	/**
	 * overview : cashier page
	 *
	 * @return	void
	 */
	public function cashier() {
		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else if ($this->utils->is_readonly()) {
			redirect('affiliate/traffic_stats');
		} else {
			$this->loadTemplate(lang('Affiliate'), '', '');

			// $affiliate_id = $this->session->userdata('affiliateId');
			$affId = $this->getAffIdFromSession();
			// $data['requests'] = $this->affiliate_manager->getPaymentRequests($affiliate_id);

			$this->load->model(array('affiliatemodel', 'transactions'));
			$data['affiliate'] = $this->affiliatemodel->getAffiliateById($affId);
			$data['affiliateId'] = $affId;
			//$data['payment_histories'] = $this->affiliatemodel->getPaymentHistoryList($affId);
			$data['payment_histories'] = $this->transactions->getAffTransactions($affId);
			//echo "<pre>";print_r($data['payment_histories']);exit;
			$data['payment_methods'] = $this->affiliatemodel->getPaymentMethod($affId);

			$data['min_withdraw_amount'] = $this->utils->getConfig('aff_min_withdrawal') ? : 100; # default is 100

			$this->addBoxDialogToTemplate();

			$this->addJsTreeToTemplate();
			$this->template->add_js($this->utils->jsUrl('validator.min.js'));

			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'cashier/view_new_payment', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : modify payment account
	 *
	 * @return	void
	 */
	public function modifyPayment($affiliate_payment_id) {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			redirect('affiliate', 'refresh');
		} else {
			if ($affiliate_payment_id == 0) {
				$message = lang('con.16');
				$this->alertMessage(1, $message); //will set and send message to the user
				redirect("affiliate/cashier", "refresh");
			}

			$this->loadTemplate('Affiliate', '', '');

			$data['payment'] = $this->affiliate_manager->getPaymentByPaymentId($affiliate_payment_id);

			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'cashier/edit_payment_account', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : verify modify payment account
	 *
	 * @return	void
	 */
	public function verifyModifyPayment($affiliate_payment_id) {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}

		$this->form_validation->set_rules('bank_name', lang('pay.bankname'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('account_info', lang('aff.aai91'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('account_name', lang('aff.ai90'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('account_number', lang('pay.accnum'), 'trim|xss_clean|required|numeric');

		if ($this->form_validation->run() == false) {
			$this->editPayment($affiliate_payment_id);
		} else {
			$data = array(
				'affiliateId' => $this->session->userdata('affiliateId'),
				'paymentMethod' => 'Wire Transfer',
				'bankName' => $this->input->post('bank_name'),
				'accountInfo' => $this->input->post('account_info'),
				'accountName' => $this->input->post('account_name'),
				'accountNumber' => $this->input->post('account_number'),
				'updatedOn' => date('Y-m-d H:i:s'),
			);
            foreach ($data as $key => $value) {
                $data[$key] = $this->stripHTMLtags($value);
            }

			$this->affiliate_manager->editPayment($data, $affiliate_payment_id);

			$message = lang('con.11');
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect("affiliate/cashier", "refresh");
		}
	}

	/**
	 * overview : add new payment account
	 *
	 * @return	void
	 */
	public function addNewPayment() {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			redirect('affiliate', 'refresh');
		} else {

			$this->load->model('banktype');

			$affId = $this->getAffIdFromSession();
			$data['affiliate'] = $this->affiliatemodel->getAffiliateById($affId);

			$banks = (array) $this->affiliate_manager->getBankTypes();
			$data['banks'] = array();
			foreach ($banks as $key => $row) {
				$data['banks'][] = array(
							"bankTypeId" => $row->bankTypeId,
							"bankName" => $row->bankName,
							'enabled_withdrawal' => $row->enabled_withdrawal,
							);
			}

			$this->loadTemplate('Affiliate', '', '');

			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'cashier/add_payment_account', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : verify add new payment account
	 *
	 * @return	void
	 */
	public function verifyAddNewPayment() {

		$this->load->library('affiliate_manager');

		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}

		$this->form_validation->set_rules('bank_name', lang('pay.bankname'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('account_info', lang('aff.aai91'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('account_name', lang('aff.ai90'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('account_number', lang('pay.accnum'), 'trim|xss_clean|required|numeric');

		if ($this->form_validation->run() == false) {
			$this->addNewPayment();
		} else {
			$this->load->model(['banktype']);

			$selected_bank = $this->banktype->getBankTypeById($this->input->post('bank_name'));

			$data = array(
				'affiliateId' => $this->session->userdata('affiliateId'),
				'paymentMethod' => 'Wire Transfer',
				'bankName' => $selected_bank->bankName,
				'accountInfo' => $this->input->post('account_info'),
				'accountName' => $this->input->post('account_name'),
				'accountNumber' => $this->input->post('account_number'),
				'createdOn' => date('Y-m-d H:i:s'),
				'updatedOn' => date('Y-m-d H:i:s'),
			);
            foreach ($data as $key => $value) {
                $data[$key] = $this->stripHTMLtags($value);
            }

			$this->affiliate_manager->addPayment($data);

			$message = lang('con.10');
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect("affiliate/cashier");
		}
	}

	/**
	 * overview : add payment requests
	 *
	 * @return	void
	 */
	public function newRequests() {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}

		$this->form_validation->set_rules('payment_method', lang('aff.ai56'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('request_amount', lang('pay.reqamt'), 'trim|xss_clean|required|numeric|callback_checkRequestAmount');

		if ($this->form_validation->run() == false) {
			$this->cashier();
		} else {
			$this->load->model(array('affiliatemodel'));

			/*if($this->input->post('amount') <= 0) {
				$message = lang('con.12');
				$this->alertMessage(1, $message); //will set and send message to the user
				redirect("affiliate/cashier", "refresh");
			*/
			$affId = $this->getAffIdFromSession();
			$paymentMethodId = $this->input->post('payment_method');
			$amount = $this->input->post('request_amount');
			$payment = $this->affiliatemodel->getPaymentByPaymentId($paymentMethodId);

			$success = $this->lockAndTransForAffiliateBalance($affId, function ()
				use ($amount, $affId, $paymentMethodId, $payment) {

			// $lock_type = Utils::LOCK_ACTION_AFF_BALANCE;
			// $lock_it = $this->utils->lockActionById($affId, $lock_type);
			// $this->utils->debug_log('lock aff', $affId, 'amount', $amount);
			// try {
			// 	if ($lock_it) {

					// $data = array(
					// 	'affiliateId' => $affId,
					// 	'paymentMethod' => $payment['bankName'],
					// 	'amount' => $amount,
					// 	'fee' => 0,
					// 	'status' => '0',
					// 	'affiliatePaymentId' => $paymentMethodId,
					// 	'createdOn' => $this->utils->getNowForMysql(),
					// 	'updatedOn' => $this->utils->getNowForMysql(),
					// );
					// $this->affiliatemodel->startTrans();
					$bal = $this->affiliatemodel->getMainWallet($affId);

					if ($this->utils->compareResultFloat($bal, '>=', $amount)) {
						$success = $this->affiliatemodel->addWithdrawRequest($affId, $payment, $amount);
					} else {
						$this->utils->error_log('do not have enough balance', $affId, $amount, 'wallet balance', $bal);
						$success = false;
					}

					return $success;

					// $success = $this->affiliatemodel->endTransWithSucc() && $success;
			// 	} else {
			// 		$this->utils->error_log('lock aff failed', $affId, $amount);
			// 		$success = false;
			// 	}
			// } finally {
			// 	$rlt = $this->utils->releaseActionById($affId, $lock_type);
			// }
			});

			if ($success) {
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('New withdrawal has been successfully added'));
			} else {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
			}

			// $message = lang('con.13');
			// $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message); //will set and send message to the user
			redirect("affiliate/cashier");
		}
	}

	/**
	 * overview : callback check request amount
	 *
	 * @return	void
	 */
	public function checkRequestAmount() {
		$affId = $this->getAffIdFromSession();
		$request_amount = $this->input->post('request_amount');
		// $available_balance = $this->input->post('amount');
		$this->load->model(array('affiliatemodel'));
		$available_balance = $this->affiliatemodel->getMainWallet($affId);

		if ($request_amount == 0 || empty($request_amount)) {
			$this->form_validation->set_message('checkRequestAmount', lang('con.19'));
			return false;
		} else if ($request_amount > $available_balance) {
			$this->form_validation->set_message('checkRequestAmount', lang('con.18'));
			return false;
		}

		return true;
	}

	/**
	 * overview : cancel payment requests
	 *
	 * @return	void
	 */
	public function cancelRequests($request_id, $status) {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}

		if ($status != 0) {
			$message = lang('con.14');
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect("affiliate/cashier", "refresh");
		}

		$data = array(
			'status' => '4',
			'updatedOn' => date('Y-m-d H:i:s'),
		);
		$this->affiliate_manager->cancelRequests($data, $request_id);

		$message = lang('con.15');
		$this->alertMessage(1, $message); //will set and send message to the user
		redirect("affiliate/cashier", "refresh");
	}

	/**
	 * overview : get deskeyOG
	 */
	protected function getDeskeyOG() {
		return $this->config->item('DESKEY_OG');
	}

	/**
	 * overview : get sub affiliates
	 */
	public function subaffiliates() {
		if (!$this->checkLogin()) {
			redirect('affiliate', 'refresh');
		} else if ($this->utils->is_readonly()) {
			redirect('affiliate/traffic_stats');
		} else {
            $this->load->model('affiliate_earnings');
			$data = array();
            $data['year_month_list'] = $this->affiliate_earnings->getYearMonthListToNow_2();
            $data['conditions'] = $this->safeLoadParams(array(
                'year_month' 			=> '',
            ));
			$this->loadTemplate(lang('Affiliate'), '', '');
			$this->load->model('affiliatemodel');
			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
            $this->template->write_view('main_content', 'settings/subaffiliatesreport', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : add sub affiliate
	 */
	public function addSubAffiliate() {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}

		$data['curren'] = $this->affiliate_manager->getCurrency();
		$this->loadTemplate(lang('Affiliate'), '', '');
		$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
		$this->template->write_view('main_content', 'settings/add_subaffiliate', $data);
		$this->template->render();
	}

	/**
	 * overview : activate affiliate
	 *
	 * @return	void
	 */
	public function activateAffiliate($affiliate_id, $username) {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}

		$this->load->model(['affiliatemodel']);
		$this->affiliatemodel->active($affiliate_id);

		// $data = array(
		// 	'status' => '0',
		// 	'updatedOn' => date('Y-m-d H:i:s'),
		// );
		// $this->affiliate_manager->editAffiliates($data, $affiliate_id);

		$message = lang('con.aff52') . ": " . $username;
		$this->alertMessage(1, $message);

		redirect('affiliate/subaffiliates', 'refresh');
	}

	/**
	 * overview : delete affiliate
	 *
	 * @return	void
	 */
	// public function deleteAffiliate($affiliate_id, $username) {
	// 	$this->load->model(['affiliatemodel']);
	// 	if (!$this->checkLogin() || $this->utils->is_readonly()) {
	// 		return redirect('affiliate', 'refresh');
	// 	}

	// 	$this->affiliatemodel->deleteAffiliates($affiliate_id);

	// 	$message = lang('con.aff07') . ": " . $username;
	// 	$this->alertMessage(1, $message);

	// 	redirect('affiliate/subaffiliates', 'refresh');
	// }

	/**
	 * overview : freeze affiliate
	 *
	 * @return	void
	 */
	public function freezeAffiliate($affiliate_id, $username) {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}

		// $data = array(
		// 	'status' => '2',
		// 	'updatedOn' => date('Y-m-d H:i:s'),
		// );
		// $this->affiliate_manager->editAffiliates($data, $affiliate_id);

		$this->load->model(['affiliatemodel']);
		$this->affiliatemodel->inactive($affiliate_id);

		$message = lang('con.aff08') . ": " . $username;
		$this->alertMessage(1, $message);

		redirect('affiliate/subaffiliates', 'refresh');
	}

	/**
	 * overview : unfreeze affiliate
	 *
	 * @return	void
	 */
	public function unfreezeAffiliate($affiliate_id, $username) {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}

		$this->load->model(['affiliatemodel']);
		$this->affiliatemodel->active($affiliate_id);

		// $data = array(
		// 	'status' => '0',
		// 	'updatedOn' => date('Y-m-d H:i:s'),
		// );
		// $this->affiliate_manager->editAffiliates($data, $affiliate_id);

		$message = lang('con.aff09') . ": " . $username;
		$this->alertMessage(1, $message);

		redirect('affiliate/subaffiliates', 'refresh');
	}

	/**
	 * overview : get player list
	 */
	public function playersList() {
		if (!$this->checkLogin()) {
			return redirect('affiliate', 'refresh');
		}

		$this->checkSecondPassword();

		// LOAD MODEL
		$this->load->model(array('player_model', 'affiliatemodel', 'transactions'));

		// OGP-14933: Build search box for signup date
		$date_from	= $this->input->get('date_from'	, 1);
		$date_to	= $this->input->get('date_to'	, 1);
		$by_date 	= !empty($this->input->get('by_date'	, 1));

		// Use default value if absent (by redirect)
		if ( empty($date_from) && empty($date_to) && empty($by_date) ) {
			$today_begin	= date('Y-m-d H:i:s', strtotime('today 00:00'));
			$now			= date('Y-m-d H:i:s', strtotime('today 23:59:59'));
			redirect("/affiliate/playersList?date_from={$today_begin}&date_to={$now}&by_date=1");
			return;
		}

		// Pass along values for date searchbox to view
		$data['date_from']	= $date_from;
		$data['date_to']	= $date_to;
		$data['by_date']	= $by_date;

		$data['affiliateId'] = $this->getAffIdFromSession();
		if (!$by_date || empty($date_from) && empty($date_to)) {
			$players = $this->player_model->getPlayersByAffiliateId($data['affiliateId']);
		}
		else {
			$players = $this->player_model->getPlayersByAffIdAndDate($data['affiliateId'], $date_from, $date_to);
		}

		foreach ($players as &$player) {
			$player['online'] = $this->player_model->existsOnlineSession($player['playerId']);
			$player['deposit_count'] = $this->transactions->countDepositByPlayer($player['playerId']);
			$player['last_deposit'] = $this->transactions->getLastDepositDate($player['playerId']);
			$player['realName'] = trim(implode(' ', array($player['firstName'],$player['lastName'])));

			if ($this->utils->isEnabledFeature('masked_realname_on_affiliate')) {
				$player['realName'] = $this->utils->keepOnlyString($player['realName'], 4);
			}

		}
		$data['online_count'] = count(array_filter($players, function($player) {return $player['online'];}));
		// if ($this->utils->isHidePlayerContactOnAff()) {
		// 	foreach ($players as &$player) {
		// 		$player['email'] = '******';
		// 	}
		// }
		$data['players'] = $players;
		$data['enable_credit'] = $this->affiliatemodel->isEnabledCredit($data['affiliateId']);

		$this->loadTemplate('Affiliate', '', '');
		$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
		$this->template->write_view('main_content', 'players/list_players', $data);
		$this->template->render();
	}

	public function playersListReplaceForeachTest() {
		if (!$this->checkLogin()) {
			return redirect('affiliate', 'refresh');
		}

		$this->checkSecondPassword();

		// LOAD MODEL
		$this->load->model(array('player_model', 'affiliatemodel', 'transactions'));

		$data['affiliateId'] = $this->getAffIdFromSession();
		$players = $this->player_model->getPlayersByAffiliateId($data['affiliateId']);

		for($i=0; $i<count($players); $i++) {
			$players[$i]['online'] = $this->player_model->existsOnlineSession($players[$i]['playerId']);
			$players[$i]['deposit_count'] = '0';
			$players[$i]['last_deposit'] = 'N/A';
			$players[$i]['realName'] = trim(implode(' ', array($players[$i]['firstName'],$players[$i]['lastName'])));

			if ($this->utils->isEnabledFeature('masked_realname_on_affiliate')) {
				$players[$i]['realName'] = $this->utils->keepOnlyString($players[$i]['realName'], 4);
			}
		}

		$data['online_count'] = count(array_filter($players, function($player) {return $player['online'];}));
		// if ($this->utils->isHidePlayerContactOnAff()) {
		// 	foreach ($players as &$player) {
		// 		$player['email'] = '******';
		// 	}
		// }
		$data['players'] = $players;
		$data['enable_credit'] = $this->affiliatemodel->isEnabledCredit($data['affiliateId']);

		$this->loadTemplate('Affiliate', '', '');
		$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
		$this->template->write_view('main_content', 'players/list_players', $data);
		$this->template->render();
	}

	/**
	 * overview : get player list
	 */
	public function playersListWithotCountTransactionAndOnlineCountTest() {
		$this->utils->debug_log('============playersListWithotCountTransactionAndOnlineCountTest start');
		if (!$this->checkLogin()) {
			return redirect('affiliate', 'refresh');
		}

		$this->checkSecondPassword();

		// LOAD MODEL
		$this->load->model(array('player_model', 'affiliatemodel', 'transactions'));

		$data['affiliateId'] = $this->getAffIdFromSession();
		$this->utils->debug_log('============playersListWithotCountTransactionAndOnlineCountTest affiliateId', $data['affiliateId']);

		$players = $this->player_model->getPlayersByAffiliateId($data['affiliateId']);
		$this->utils->debug_log('============playersListWithotCountTransactionAndOnlineCountTest players count', count($players));

		for($i=0; $i<count($players); $i++) {
			$players[$i]['online'] = $this->player_model->existsOnlineSession($players[$i]['playerId']);
			$players[$i]['deposit_count'] = '0';
			$players[$i]['last_deposit'] = 'N/A';
			$players[$i]['realName'] = trim(implode(' ', array($players[$i]['firstName'],$players[$i]['lastName'])));

			if ($this->utils->isEnabledFeature('masked_realname_on_affiliate')) {
				$players[$i]['realName'] = $this->utils->keepOnlyString($players[$i]['realName'], 4);
			}
		}

		// foreach ($players as &$player) {
		// 	$player['online'] = $this->player_model->existsOnlineSession($player['playerId']);
		// 	$player['deposit_count'] = '0';
		// 	$player['last_deposit'] = 'N/A';
		// 	$player['realName'] = trim(implode(' ', array($player['firstName'],$player['lastName'])));

		// 	if ($this->utils->isEnabledFeature('masked_realname_on_affiliate')) {
		// 		$player['realName'] = $this->utils->keepOnlyString($player['realName'], 4);
		// 	}

		// }
		$data['online_count'] = '0';
		// if ($this->utils->isHidePlayerContactOnAff()) {
		// 	foreach ($players as &$player) {
		// 		$player['email'] = '******';
		// 	}
		// }
		$data['players'] = $players;
		$data['enable_credit'] = $this->affiliatemodel->isEnabledCredit($data['affiliateId']);

		$this->loadTemplate('Affiliate', '', '');
		$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
		$this->template->write_view('main_content', 'players/list_players', $data);
		$this->template->render();
	}

	/**
	 * overview : get player list
	 */
	public function playersListWithApprovedSaleOrdersCountButWithoutOnlineCountTest() {
		if (!$this->checkLogin()) {
			return redirect('affiliate', 'refresh');
		}

		$this->checkSecondPassword();

		// LOAD MODEL
		$this->load->model(array('player_model', 'affiliatemodel', 'sale_order'));

		$data['affiliateId'] = $this->getAffIdFromSession();
		$players = $this->player_model->getPlayersByAffiliateId($data['affiliateId']);

		for($i=0; $i<count($players); $i++) {
			$players[$i]['online'] = $this->player_model->existsOnlineSession($players[$i]['playerId']);
			$players[$i]['deposit_count'] = '0';
			$players[$i]['last_deposit'] = 'N/A';
			$players[$i]['realName'] = trim(implode(' ', array($players[$i]['firstName'],$players[$i]['lastName'])));

			if ($this->utils->isEnabledFeature('masked_realname_on_affiliate')) {
				$players[$i]['realName'] = $this->utils->keepOnlyString($players[$i]['realName'], 4);
			}
		}

		// foreach ($players as &$player) {
		// 	$player['online'] = $this->player_model->existsOnlineSession($player['playerId']);
		// 	$player['deposit_count'] = $this->sale_order->countSaleOrdersByPlayerId($player['playerId']);
		// 	$player['last_deposit'] = $this->sale_order->getLastSaleOrderUpdatedAt($player['playerId']);
		// 	$player['realName'] = trim(implode(' ', array($player['firstName'],$player['lastName'])));

		// 	if ($this->utils->isEnabledFeature('masked_realname_on_affiliate')) {
		// 		$player['realName'] = $this->utils->keepOnlyString($player['realName'], 4);
		// 	}

		// }
		$data['online_count'] = '0';
		// if ($this->utils->isHidePlayerContactOnAff()) {
		// 	foreach ($players as &$player) {
		// 		$player['email'] = '******';
		// 	}
		// }
		$data['players'] = $players;
		$data['enable_credit'] = $this->affiliatemodel->isEnabledCredit($data['affiliateId']);

		$this->loadTemplate('Affiliate', '', '');
		$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
		$this->template->write_view('main_content', 'players/list_players', $data);
		$this->template->render();
	}

	/**
	 * overview : view player by id
	 *
	 * @param $playerId
	 */
	public function viewPlayerById($playerId) {
		if (!$this->checkLogin()) {
			return redirect('affiliate', 'refresh');
		}

		$this->checkSecondPassword();

		if ($this->utils->getConfig('hide_player_info_on_aff')) {
			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			return $this->showErrorAccess(lang('Affiliate'), '', '');
		}

		$this->load->model(['player_model','affiliatemodel']);
		if ($this->isPlayerSecureId($playerId)) {
			$playerId = $this->player_model->getPlayerIdBySecureId($playerId);
		}

		$affiliateId = $this->getAffIdFromSession();
		if(empty($affiliateId)) {
			# affiliate ID should not be empty on this page
			# an empty affiliate ID can view all players under all affiliates, returning error here.
			return $this->showErrorAccess(lang('Affiliate'), '', '');
		}
		$downline_player_ids = $this->affiliatemodel->includeAllDownlinePlayerIds($affiliateId);

		if ( ! in_array($playerId, $downline_player_ids)) {
			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			return $this->showErrorAccess(lang('Affiliate'), '', '');
		}

		// LOAD MODEL
		$data['player_id'] = $playerId;
		$data['affiliateId'] = $affiliateId;

		# Get the name of direct affiliate
		$playerDetail = $this->player_model->getPlayerById($playerId);
		$directAffiliateId = $playerDetail->affiliateId;
		if($directAffiliateId) {
			$directAffiliateDetail = $this->affiliatemodel->getAffiliateById($directAffiliateId);
			$data['player_direct_affiliate'] = $directAffiliateDetail['username'];
		}

		$data['player_account_info'] = $this->player_model->getPlayerAccountInfo($playerId);
		$data['player_signup_info'] = $this->player_model->getPlayerSignupInfo($playerId);
		$data['player_signup_info']['typeOfPlayer'] = $this->player_model->getPlayerType($playerId);
		$data['enable_credit'] = $this->affiliatemodel->isEnabledCredit($affiliateId);

		$this->loadTemplate(lang('Affiliate'), '', '');
		$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
		$this->template->write_view('main_content', 'players/view_player', $data);
		$this->template->render();
	}

	/**
	 * overview : player action ( new deposit, withdraw, transfer from/to sw )
	 *
	 * @param $action
	 * @param $playerId
	 * @param $affiliateId
	 */
	public function playerAction($action, $playerId, $affiliateId) {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}

		$this->load->model(array('player_model', 'external_system'));
		switch ($action) {
		case self::ACTION_NEW_DEPOSIT:
			$data['transaction_title'] = lang('aff.action.newDeposit');
			$data['is_mainwallet'] = true;
			break;

		case self::ACTION_NEW_WITHDRAW:
			$data['transaction_title'] = lang('aff.action.newWithdrawal');
			$data['is_mainwallet'] = true;
			break;

		case self::ACTION_TRANSFER_FROM_SW:
			$data['transaction_title'] = lang('aff.action.transferFromSubwallet');
			$data['is_mainwallet'] = false;
			break;

		case self::ACTION_TRANSFER_TO_SW:
			$data['transaction_title'] = lang('aff.action.transferToSubwallet');
			$data['is_mainwallet'] = false;
			break;
		}

		$data['game_platforms'] = $this->external_system->getAllActiveSytemGameApi();
		$data['playerDetails'] = $this->player_model->getPlayersSubWalletBalance($playerId, $data['game_platforms']);
		$data['affiliate_details'] = $this->affiliate_manager->getAffiliateById($affiliateId); // Affiliate's information
		$data['transaction_type'] = $action;
		$data['player_account_info'] = $this->player_model->getPlayerAccountInfo($playerId);
		$data['player_signup_info'] = $this->player_model->getPlayerSignupInfo($playerId);
		$data['player_signup_info']['typeOfPlayer'] = $this->player_model->getPlayerType($playerId);
		$this->loadTemplate('Affiliate', '', '');
		$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
		$this->template->write_view('main_content', 'players/transact_player', $data);
		$this->template->render();
	}

	/**
	 * overview : process transaction
	 *
	 * @param $transaction_type
	 * OGP-15458 Remove action buttons from affiliate BO ,2019/12/12
	 */
	// public function processTransaction($transaction_type) {
	// 	if (!$this->checkLogin() || $this->utils->is_readonly()) {
	// 		return redirect('affiliate', 'refresh');
	// 	}

	// 	$this->load->model(array('wallet_model', 'player_model', 'transactions', 'users', 'payment', 'affiliatemodel', 'external_system'));

	// 	# EXTRACT POST PARAMETERS
	// 	extract($this->safeLoadParams(array(
	// 		'player_id' => NULL,
	// 		'sub_wallet_id' => Wallet_model::MAIN_WALLET_ID,
	// 		'amount' => 0,
	// 	)));

	// 	# INITIALIZE OTHER REQUIRED VARIABLES
	// 	$self = $this;
	// 	$success = false;
	// 	$current_timestamp = $this->utils->getNowForMysql();
	// 	$affiliate_id = $this->getAffIdFromSession();
	// 	$affiliate_username = $this->affiliatemodel->getUsernameById($affiliate_id);
	// 	$affiliate_balance = $this->affiliatemodel->getCreditBalance($affiliate_id);
	// 	$player_username = $this->player_model->getUsernameById($player_id);
	// 	$player_total_balance = $this->wallet_model->getTotalBalance($player_id);
	// 	$main_wallet_balance = $this->player_model->getMainWalletBalance($player_id);
	// 	$sub_wallet_name = $sub_wallet_id ? $this->external_system->getNameById($sub_wallet_id) . ' Subwallet' : 'Main Wallet';
	// 	$sub_wallet_balance = $sub_wallet_id ? $this->player_model->getPlayerSubWalletBalance($player_id, $sub_wallet_id) : $main_wallet_balance;

	// 	try {

	// 		# VALIDATE IF PLAYER BELONGS TO AFFILIATE
	// 		if ( ! isset($player_id) || ! $this->affiliatemodel->belongToAffUsername($player_id, $affiliate_username)) {
	// 			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
	// 			return $this->showErrorAccess(lang('Affiliate'), '', '');
	// 		}

	// 		# TRANSFER
	// 		switch ($transaction_type) {
	// 			case self::ACTION_NEW_DEPOSIT:

	// 				# VALIDATE AMOUNT
	// 				if ($amount > $affiliate_balance) throw new Exception(lang('aff.action.error.newDeposit'));

	// 				$success = $this->lockAndTrans(Utils::LOCK_ACTION_AFF_BALANCE, $affiliate_id, function () use ($self, $affiliate_id, $player_id, $amount) {
	// 					$affiliate_result = $this->affiliatemodel->decCreditBalance($affiliate_id, $amount);
	// 					$wallet_result = $this->wallet_model->incMainWallet($player_id, $amount);
	// 					return $affiliate_result && $wallet_result;
	// 				});

	// 				$action_name = 'Add';
	// 				$adjustment_type = Transactions::MANUAL_ADD_BALANCE;
	// 				$from_id = $affiliate_id;
	// 				$from_type = Transactions::AFFILIATE;
	// 				$to_id = $player_id;
	// 				$to_type = Transactions::PLAYER;
	// 				break;

	// 			case self::ACTION_NEW_WITHDRAW:

	// 				# VALIDATE AMOUNT
	// 				if ($amount > $sub_wallet_balance) throw new Exception(lang('aff.action.error.newWithdraw'));

	// 				$success = $this->lockAndTrans(Utils::LOCK_ACTION_AFF_BALANCE, $affiliate_id, function () use ($self, $affiliate_id, $player_id, $amount) {
	// 					$affiliate_result = $this->affiliatemodel->incCreditBalance($affiliate_id, $amount);
	// 					$wallet_result = $this->wallet_model->decMainWallet($player_id, $amount);
	// 					return $affiliate_result && $wallet_result;
	// 				});

	// 				$action_name = 'Subtract';
	// 				$adjustment_type = Transactions::MANUAL_SUBTRACT_BALANCE;
	// 				$from_id = $player_id;
	// 				$from_type = Transactions::PLAYER;
	// 				$to_id = $affiliate_id;
	// 				$to_type = Transactions::AFFILIATE;
	// 				break;

	// 			case self::ACTION_TRANSFER_FROM_SW:

	// 				# VALIDATE AMOUNT
	// 				if ($amount > $sub_wallet_balance) throw new Exception(lang('aff.action.error.subwallet.tranferfrom'));

	// 				$from_sub_wallet_id = $sub_wallet_id;
	// 				$to_sub_wallet_id = Wallet_model::MAIN_WALLET_ID;

	// 				$result = $this->utils->transferWallet($player_id, $player_username, $from_sub_wallet_id, $to_sub_wallet_id, $amount);
	// 				$success = isset($result['success']) && $result['success'];

	// 				break;

	// 			case self::ACTION_TRANSFER_TO_SW:

	// 				# VALIDATE AMOUNT
	// 				if ($amount > $main_wallet_balance) throw new Exception(lang('aff.action.error.subwallet.tranferto'));

	// 				$from_sub_wallet_id = Wallet_model::MAIN_WALLET_ID;
	// 				$to_sub_wallet_id = $sub_wallet_id;

	// 				$result = $this->utils->transferWallet($player_id, $player_username, $from_sub_wallet_id, $to_sub_wallet_id, $amount);
	// 				$success = isset($result['success']) && $result['success'];

	// 				break;
	// 		}

	// 		if ( ! $success) throw new Exception(lang('Transfer Failed'));

	// 		if (isset($adjustment_type, $from_id, $from_type, $to_id, $to_type)) {

	// 			$sub_wallet_balance_after = $sub_wallet_id ? $this->player_model->getPlayerSubWalletBalance($player_id, $sub_wallet_id) : $this->player_model->getMainWalletBalance($player_id);

	// 			$note = sprintf('%s <b>%s</b> to <b>%s</b>\'s <b>%s</b>(<b>%s</b> to <b>%s</b>) by <b>%s</b>',
	// 				$action_name,
	// 				number_format($amount, 2),
	// 				$player_username,
	// 				$sub_wallet_name,
	// 				number_format($sub_wallet_balance, 2),
	// 				number_format($sub_wallet_balance_after, 2),
	// 				$affiliate_username
	// 			);

	// 			$this->transactions->saveAffiliateTransaction(
	// 				$amount, #amount
	// 				$adjustment_type, #transaction_type
	// 				$from_id, #from_id
	// 				$from_type, #from_type
	// 				$to_id, #to_id
	// 				$to_type, #to_type
	// 				$sub_wallet_id, #sub_wallet_id
	// 				$note, #note
	// 				$sub_wallet_balance, #beforeBalance
	// 				$sub_wallet_balance_after, #afterBalance
	// 				$player_total_balance #totalBeforeBalance
	// 			);
	// 		}

	// 		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Transfer Success'));
	// 		redirect('/affiliate/playersList');
	// 	} catch (Exception $e) {
	// 		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $e->getMessage());
	// 		redirect('affiliate/playerAction/' . $transaction_type . '/' . $player_id . '/' . $affiliate_id);
	// 	}

	// }

	/**
	 * overview : get transaction history
	 *
	 * @param $playerId
	 */
	public function transactionHistory($playerId) {
		if (!$this->checkLogin()) {
			return redirect('affiliate', 'refresh');
		}

		$data['playerId'] = $playerId;
		$this->load->view('players/ajax_ui_transaction', $data);
	}

	/**
	 * overview : game history
	 *
	 * @param $playerId
	 */
	public function gamesHistory($playerId) {
		if (!$this->checkLogin()) {
			return redirect('affiliate', 'refresh');
		}

		$this->load->model('external_system');
		$data['game_platforms'] = $this->external_system->getAllGameApis();
		$data['playerId'] = $playerId;
		$this->load->view('players/ajax_ui_game', $data);
	}

	/**
	 * overview : create tracking code
	 *
	 * @param $affiliate_id
	 * @return	void
	 */
	public function createCode($affiliate_id) {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}

		$affiliate_id = $this->session->userdata('affiliateId');

		$this->form_validation->set_rules('tracking_code', lang('Tracking Code'), 'trim|xss_clean|required|min:5|max:8|' . ($this->utils->isEnabledFeature('affiliate_tracking_code_numbers_only') ? 'numeric' : 'alpha_numeric') . '|is_unique[affiliates.trackingCode]');
		$this->form_validation->set_message('is_unique', lang('formvalidation.is_unique'));
		$this->form_validation->set_message('numeric', lang('formvalidation.numeric'));
		if ($this->form_validation->run() == false) {

			$message = form_error('tracking_code');

			$this->alertMessage(2, $message);

			$this->modifyAccount();
		} else {
			// $data = array(
			// 	'trackingCode' => $this->input->post('tracking_code'),
			// 	'status' => '0',
			// 	'updatedOn' => date('Y-m-d H:i:s'),
			// );
			// $this->affiliate_manager->editAffiliates($data, $affiliate_id);

			$this->load->model(array('affiliatemodel'));

			$this->affiliatemodel->startTrans();

			$trackingCode = $this->input->post('tracking_code');
			$success = $this->affiliatemodel->updateTrackingCode($affiliate_id, $trackingCode);

			if ($success) {
				$success = $this->affiliatemodel->endTransWithSucc();
			} else {
				//rollback
				$this->affiliatemodel->rollbackTrans();
				$this->utils->error_log('rollback on create code', $affiliate_id);
			}

			if ($success) {
				$message = lang('con.aff14');
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			} else {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
			}

			redirect('affiliate/modifyAccount');
		}
	}

	/**
	 * overview : switch theme
	 *
	 * @param $theme
	 */
	public function switchTheme($theme) {
		$this->session->set_userdata('affiliate_theme', $theme);

		$referred_from = $this->session->userdata('current_url');
		redirect($referred_from, 'refresh');
	}

	/**
	 * overview : affiliate transfer balance to main
	 *
	 * @param null $amount
	 */
	public function affiliate_transfer_bal_to_main($amount = null) {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}

		$affId = $this->getAffIdFromSession();
		$this->load->model(array('transactions'));

		$self = $this;

		// $adminUserId = $this->authentication->getUserId();

		// $lock_type = Utils::LOCK_ACTION_AFF_BALANCE;

		$success = $this->lockAndTransForAffiliateBalance($affId, function () use ($self, $affId, $amount) {
			$success = $self->transactions->affTransferFromBalanceToMain($affId, $amount);
			return $success;
		});

		if ($success) {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Transfer successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
		}

		redirect("/affiliate/cashier");
	}

	/**
	 * overview : affiliate transfer balance from main
	 * @param null $amount
	 */
	public function affiliate_transfer_bal_from_main($amount = null) {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}

		$affId = $this->getAffIdFromSession();
		$this->load->model(array('transactions'));

		$self = $this;

		// $adminUserId = $this->authentication->getUserId();

		// $lock_type = Utils::LOCK_ACTION_AFF_BALANCE;

		$success = $this->lockAndTransForAffiliateBalance($affId, function () use ($self, $affId, $amount) {
			$success = $self->transactions->affTransferToBalanceFromMain($affId, $amount);
			return $success;
		});

		if ($success) {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Transfer successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
		}

		redirect("/affiliate/cashier");
	}

	/**
	 * overview : check if player's is secure
	 * @param $playerId
	 * @return bool
	 */
	public function isPlayerSecureId($playerId) {
		return substr($playerId, 0, 1) == 'P';
	}

	/**
	 * overview : get banner
	 * @param $id
	 */
	public function get_banner($id) {
		if (!$this->checkLogin()) {
			return redirect('affiliate', 'refresh');
		}

		// $this->load->helper('download');
		$this->load->model(['affiliatemodel']);
		$localPath = $this->affiliatemodel->getBannerLocalPathById($id);

		$data = file_get_contents($localPath);
		$ext = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
		// return force_download(basename($localPath), $data);
		// header('Content-Type: image/'.$ext);
		$this->output->set_content_type('image/' . $ext)->set_output($data);
	}

	/**
	 * overview : traffic stats
	 */
	public function traffic_stats() {

		$this->load->model(array('http_request'));
		if (!$this->checkLogin() || $this->utils->isEnabledFeature('aff_hide_traffic_stats')) {
			redirect('affiliate', 'refresh');
		} else {

			$data['title'] = lang('Traffic Statistics');
			$data['export_report_permission'] = true;

			$data['conditions'] = $this->safeLoadParams(array(
				'by_date_from' => $this->utils->getTodayForMysql() . ' 00:00:00',
				'by_date_to' => $this->utils->getTodayForMysql() . ' 23:59:59',
				'by_banner_name' => '',
				'by_tracking_code' => '',
				'by_tracking_source_code' => '',
				'by_type' => '',
				'registrationWebsite' => '',
				'remarks' => '',
			));

			$data['conditions']['enable_date'] = $this->safeGetParam('enable_date', false, true);

			$affiliate_id = $this->getSessionAffId();

			$this->loadTemplate($data['title'], '', '');
			// $this->template->add_js('resources/js/bootstrap-switch.min.js');
			// $this->template->add_css('resources/css/bootstrap-switch.min.css');
			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'traffic_stats/view_traffic_stats', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : get player stats
	 */
	public function player_stats() {
		if (!$this->checkLogin()) {
			redirect('affiliate');
		} else {

			$data['title'] = lang('Player Statistics');

			$data['conditions'] = $this->safeLoadParams(array(
				'by_date_from' => $this->utils->getTodayForMysql() . ' 00:00:00',
				'by_date_to' => $this->utils->getTodayForMysql() . ' 23:59:59',
				'by_username' => '',
				'show_game_platform' => false,
			));

			$data['conditions']['enable_date'] = $this->safeGetParam('enable_date', false, true);

			$affiliate_id = $this->getSessionAffId();
			// $start_date = $this->session->userdata('start_date');
			// $end_date = $this->session->userdata('end_date');

			// $data['stats'] = $this->affiliatemodel->getStatistics($affiliate_id, $start_date, $end_date);

			$this->loadTemplate($data['title'], '', '');
			// $this->template->add_js('resources/js/bootstrap-switch.min.js');
			// $this->template->add_css('resources/css/bootstrap-switch.min.css');
			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'players/player_stats', $data);
			$this->template->render();
		}
	}

	public function affiliate_player_report() {
        if (!$this->checkLogin()) {
            redirect('affiliate');
		} else if ($this->utils->is_readonly()) {
			redirect('affiliate/traffic_stats');
		} else {
        	$this->load->model('player_model');

            $data['allLevels'] = $this->player_model->getAllPlayerLevels();
            $data['affiliate_username'] = $this->input->get('affiliate_username');
            $data['date_from'] = $this->input->get('date_from');
            $data['date_to'] = $this->input->get('date_to');
            $data['realname'] = $this->input->get('realname');

            $this->loadTemplate(lang('Player Report'), '', '');
			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
            $this->template->write_view('main_content', 'affiliate/view_player_report', $data);
            $this->template->render();
        }
    }

    public function affiliate_game_history() {
        if (!$this->checkLogin()) {
            redirect('affiliate');
        } else {
            $this->load->model(array('game_type_model', 'game_logs', 'external_system', 'player_model'));

            $data['game_platforms'] = $this->external_system->getAllActiveSytemGameApi();
            $data['game_types'] = $this->game_type_model->getGameTypesForDisplay();
            $data['player_levels'] = $this->player_model->getAllPlayerLevels();

            $data['conditions'] = $this->safeLoadParams(array(
                'by_date_from' => $this->utils->getTodayForMysql(). ' 00:00:00',
                'by_date_to' => $this->utils->getTodayForMysql(). ' 23:59:59',
                'by_username' => $this->input->get('player_username'),
                'by_realname' => '',
                'by_group_level' => '',
                'by_game_platform_id' => '',
                'by_game_flag' => '',
                'by_amount_from' => '',
                'by_amount_to' => '',
                'by_bet_amount_from' => '',
                'by_bet_amount_to' => '',
                'by_round_number' => '',
                'game_type_id' => '',
                'game_description_id'=>'',
            ));

            $this->utils->debug_log('GAME_HISTORY conditions', $data['conditions']);

            $this->loadTemplate(lang('Game History'), '', '');
            $this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
            $this->template->write_view('main_content', 'affiliate/view_game_logs', $data);
            $this->template->render();
        }
    }

    public function affiliate_games_report() {
        if (!$this->checkLogin()) {
            //redirect('affiliate');
		} else if ($this->utils->is_readonly()) {
			redirect('affiliate/traffic_stats');
		} else {
            $this->load->model(array('game_type_model', 'game_logs', 'external_system', 'player_model'));

            // $data['game_platforms'] = $this->external_system->getAllActiveSytemGameApi();
            // $data['game_types'] = $this->game_type_model->getGameTypesForDisplay();
            // $data['player_levels'] = $this->player_model->getAllPlayerLevels();

            $start_today = date("Y-m-d") . ' 00:00:00';
			$end_today = date("Y-m-d") . ' 23:59:59';
			$data['conditions'] = $this->safeLoadParams(array(
				'date_from' => $this->utils->getTodayForMysql(),
				'hour_from' => '00',
				'date_to' => $this->utils->getTodayForMysql(),
				'hour_to' => '23',
				'datetime_from' => $this->utils->getTodayForMysql().' '.Utils::FIRST_TIME,
				'datetime_to' => $this->utils->getTodayForMysql().' '.Utils::LAST_TIME,
				'datetime_from_timezone' =>  $this->utils->getTodayForMysql().' '.Utils::FIRST_TIME,
				'datetime_to_timezone' =>  $this->utils->getTodayForMysql().' '.Utils::LAST_TIME,
				'total_bet_from' => '',
				'total_bet_to' => '',
				'total_loss_from' => '',
				'total_loss_to' => '',
				'total_gain_from' => '',
				'total_gain_to' => '',
				'group_by' => '',
				'username' => '',
				'agent_name' => '',
				'external_system' => '',
				'game_type' => '',
				'game_type_multiple' => '',
				'show_multiselect_filter' => '',
				'total_player' => '',
				'timezone' => ''
			));
			$data['export_report_permission'] = true;
			$data['game_apis_map'] = $this->utils->getGameSystemMap();
			$data['mulitple_select_game_map']= $this->game_type_model->getActiveGamePlatformGameTypes();
			$data['conditions']['search_unsettle_game']= $this->input->get('search_unsettle_game')=='true';

            $this->loadTemplate(lang('Game History'), '', '');
            $this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
            $this->template->write_view('main_content', 'affiliate/view_affiliate_games_report', $data);
            $this->template->render();
        }
    }

    public function affiliate_credit_transactions() {
        if (!$this->checkLogin()) {
            redirect('affiliate');
        } else {

            $data['agent_username'] = $this->input->get('agent_username');
            $data['player_username'] = $this->input->get('player_username');
            $data['date_from'] = $this->input->get('date_from');
            $data['date_to'] = $this->input->get('date_to');

            $this->loadTemplate(lang('Credit Transaction'), '', '');
            $this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
            $this->template->write_view('main_content', 'affiliate/credit_transactions', $data);
            $this->template->render();
        }
    }

	public function isEnterSecondPasswordFromSession() {
		$aff_timeout_second_password=$this->utils->getConfig('aff_timeout_second_password');
		$success = time() - intval($this->session->userdata('last_time_enter_second_password')) <= $aff_timeout_second_password;
		if ( ! $success) {
			//clear it if timeout
			$this->session->unset_userdata('last_time_enter_second_password');
		}
		return $success;
	}

	public function setEnterSecondPasswordToSession() {
		$this->session->set_userdata('last_time_enter_second_password', time());
	}

    public function second_password(){
    	//show second password
    	if (!$this->checkLogin()) {
            return redirect('affiliate');
        }

        //show second password
        $referrer = $this->agent->referrer();
        if ($referrer != base_url('/affiliate/second_password')) {
        	$this->session->set_userdata('next_uri', $referrer);
        }
        $data['next_uri'] = $this->session->userdata('next_uri');

        $this->loadTemplate(lang('Second Password'), '', '');
        $this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
        $this->template->write_view('main_content', 'affiliate/input_second_password', $data);
        $this->template->render();
    }

	public function verifySecondPassword() {
		$affiliate_id = $this->getSessionAffId();
		$affiliate = $this->affiliatemodel->getAffiliateById($affiliate_id);
		$second_password = $this->input->post('second_password');
		$second_password = $this->utils->encodePassword($second_password);
		$success = $affiliate['second_password'] == $second_password;
		if ($success) {
			$this->setEnterSecondPasswordToSession();
			$next_uri = $this->input->post('next_uri');
    		redirect($next_uri, 'refresh');
		} else {
			$message = lang('con.04');
			$this->alertMessage(2, $message); // Login Details Incorrect
			redirect('/affiliate/second_password');
		}
	}

	public function addReadOnlyAccount() {
		if ( ! $this->checkLogin()  || $this->utils->is_readonly()) {
			redirect('affiliate', 'refresh');
		} else {

			$affiliate_id = $this->getSessionAffId();

			$data['affiliate'] = $this->affiliate_manager->getAffiliateById($affiliate_id);

			$this->loadTemplate('Affiliate - Add Read-only Account', '', '');
			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'settings/add_read_only_account', $data);
			$this->template->render();
		}
	}

	public function verifyAddReadOnlyAccount() {

		if ( ! $this->checkLogin()  || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}

		$this->form_validation->set_rules('username', lang('Username'), 'trim|required|min_length[5]|max_length[12]|alpha_numeric|is_unique[affiliate_read_only_account.username]');
		$this->form_validation->set_rules('password', lang('Password'), 'trim|required|min_length[6]|max_length[12]');
		$this->form_validation->set_rules('confirm_password', lang('Confirm Password'), 'trim|required|matches[password]');

		if ($this->form_validation->run() == false) {
			return $this->addReadOnlyAccount();
		} else {

			$affiliate_id = $this->getSessionAffId();
			$affiliate = $this->affiliatemodel->getAffiliateById($affiliate_id);

			$username = $affiliate['username'] . '_' . $this->input->post('username');
			$password = $this->utils->encodePassword($this->input->post('password'));

			$this->db->insert('affiliate_read_only_account', array(
				'affiliate_id' => $affiliate_id,
				'username' => $username,
				'password' => $password,
				'created_at' => $this->utils->getNowForMysql(),
			));

			$username=$this->affiliatemodel->getUsernameById($affiliate_id);
			$this->syncAffCurrentToMDBWithLock($affiliate_id, $username, false);

			$message = lang('Add Read-only Account Successful');
			$this->alertMessage(1, $message);

			return redirect("affiliate/modifyAccount", "refresh");

		}
	}

	public function deleteReadOnlyAccount($id) {
		if ( ! $this->checkLogin()  || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		} else {

			$affiliate_id = $this->getSessionAffId();

			$this->db->delete('affiliate_read_only_account', array(
				'id' => $id,
				'affiliate_id' => $affiliate_id,
			));
			$this->load->model(['affiliatemodel']);
			$username=$this->affiliatemodel->getUsernameById($affiliate_id);
			$this->syncAffCurrentToMDBWithLock($affiliate_id, $username, false);

			$message = lang('Successfully Deleted!');
			$this->alertMessage(1, $message);
			redirect('affiliate/modifyAccount');

		}
	}

	public function changeReadOnlyAccountPassword($id) {
		if (!$this->checkLogin() || $this->utils->is_readonly()) {
			redirect('affiliate', 'refresh');
		} else {

			$affiliate_id = $this->getSessionAffId();
			$account = $this->affiliatemodel->getReadonlyAccountById($id);

			$data['account'] = $account;

			$this->loadTemplate('Affiliate - Change Read-only Account Password', '', '');
			$this->template->write_view('nav_right', $this->config->item('affiliate_view_template') . '/navigation');
			$this->template->write_view('main_content', 'settings/change_read_only_account_password', $data);
			$this->template->render();

		}
	}

	public function verifyChangeReadOnlyAccountPassword($id) {

		if ( ! $this->checkLogin()  || $this->utils->is_readonly()) {
			return redirect('affiliate', 'refresh');
		}

		$this->form_validation->set_rules('password', lang('Password'), 'trim|required|min_length[6]|max_length[12]');
		$this->form_validation->set_rules('confirm_password', lang('Confirm Password'), 'trim|required|matches[password]');

		if ($this->form_validation->run() == false) {
			return $this->changeReadOnlyAccountPassword($id);
		} else {
			$affiliate_id = $this->getSessionAffId();

			$password = $this->utils->encodePassword($this->input->post('password'));

			$this->db->update('affiliate_read_only_account', array('password' => $password, 'updated_at' => $this->utils->getNowForMysql()), array('id' => $id));

			$this->load->model(['affiliatemodel']);
			$username=$this->affiliatemodel->getUsernameById($affiliate_id);
			$this->syncAffCurrentToMDBWithLock($affiliate_id, $username, false);

			$message = lang('Change Read-only Account Password Successful');
			$this->alertMessage(1, $message);

			return redirect("affiliate/modifyAccount", "refresh");

		}
	}

	public function show_404() {
		$url = $this->utils->getConfig('aff_404_override');
		return empty($url) ? show_404() : redirect($this->utils->getSystemUrl('www',$url));
	}

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

	public function change_active_currency_for_logged($currencyKey) {
		$result=['success'=>false];
		$this->load->library(['authentication', 'session']);
		//still old db
		$loggedUserId=$this->getAffIdFromSession();
		$loggedUsername=$this->getAffUsernameFromSession();
		$language=$this->getAffLangFromSession();

		$this->utils->debug_log('loggedUserId', $loggedUserId, 'loggedUsername', $loggedUsername, 'ci db', $this->db->getOgTargetDB());
		if(empty($loggedUserId)){
			$result['message']=lang('session timeout, please relogin');
			return $this->returnJsonResult($result);
		}

		//validate currency
		if($currencyKey==Multiple_db::SUPER_TARGET_DB || $this->utils->isAvailableCurrencyKey($currencyKey)){
			$_multiple_db=Multiple_db::getSingletonInstance();
			$_multiple_db->switchCIDatabase($currencyKey);
			//init session from target db
			$this->session->reinit();

			$message=null;
			$is_readonly = strpos($loggedUsername, '_')!==false;
			if ($is_readonly) {
				$affiliate = $this->affiliatemodel->getAffiliateInfoByReadonlyUsername($loggedUsername);
				$affiliate['username']=$loggedUsername;
			}else{
				$affiliate=$this->affiliatemodel->getAffiliateInfoByUsername($loggedUsername);
			}

			$this->utils->debug_log('try load affiliate', $affiliate, $loggedUsername);

			$result['success']=!empty($affiliate);
			if($result['success']){

				$result['success']=$this->after_login_affiliate($affiliate, $is_readonly, $language);
				if(!$result['success']){
					$message=lang('Process login failed');
				}
			}else{
				$message=lang('Not found affiliate username').': '.$loggedUsername;
			}

		}else{
			$result['message']=lang('not available currency');
		}

		return $this->returnJsonResult($result);
	}

	/**
	 * after login, set session, update last login info
	 *
	 * @param  array $affiliate   affiliate info
	 * @param  boolean $is_readonly
	 * @param  string $language
	 * @return boolean
	 */
	private function after_login_affiliate($affiliate, $is_readonly, $language){

		$success=true;

		$this->load->model(['affiliatemodel']);

		# OGP-1184 limit aff backend account can only login with one device
		$this->db->where('affiliate_id', $affiliate['affiliateId'])->delete('ci_aff_sessions');
		# end OGP-1184

		$this->session->set_userdata(array(
			'affiliateUsername' => $affiliate['username'],
			'affiliateId' => $affiliate['affiliateId'],
			'affiliateTrackingCode' => $affiliate['trackingCode'],
			'afflang' => $language,
			'readonly' => $is_readonly,
			// 'afflang' => !empty($result['language']) ? $result['language'] == 'English' ? '1' : '2' : '1',
		));

		$this->session->updateLoginId('affiliate_id', $affiliate['affiliateId']);

        $this->session->set_flashdata('is_login_behavior', 1);

		$data = array(
			'lastLoginIp' => $this->utils->getIP(), //$_SERVER['SERVER_ADDR'],
			'lastLogin' => $this->utils->getNowForMysql(),
			// 'lastLogout' => date('Y-m-d H:i:s'),
		);
		$this->affiliatemodel->editAffiliates($data, $affiliate['affiliateId']);

		return $success;
	}

}

/* End of file affiliate.php */
/* Location: ./application/controllers/affiliate.php */
