<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class comapi_lib {

	const PROMO_SIMPLE	= 1;
	const PROMO_ALL		= 2;

	const RANDAL_FULL_LOWER		= 0x101;
	const RANDAL_FULL			= 0x102;
	const RANDAL_HEX			= 0x103;
	const RANDAL_NONHEX			= 0x104;
	const RANDAL_NONHEX_LOWER	= 0x105;

	const DEPCAT_MANUAL			= 0x01;
	const DEPCAT_AUTO			= 0x02;
	const DEPCAT_BOTH			= 0x03;

	const AVM_TYPE_1 = 'only_allow_duplicate_one_player';
	const AVM_TYPE_2 = 'only_allow_duplicate_one_player_any';
	const AVM_TYPE_3 = 'not_allow_duplicate_number';
	const AVM_TYPE_4 = 'not_allow_duplicate_number_on_same_banktype';
	const AVM_TYPE_5 = 'not_allow_duplicate_number_on_same_banktype_any';
	const AVM_TYPE_6 = 'allow_any_duplicate_number';

	const MAX_NUM_IMAGES_PER_DEPOSIT = 2;

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->model([ 'affiliatemodel' , 'affiliate' ]);
		$this->ci->load->library([ 'utils' ]);
	}

	/**
	 * Summarizes a given field in an array.  Required by manualWithdraw.
	 *
	 * @param	$arr	array	The array
	 * @param	$key	string	Key of the field
	 *
	 * @return	int		May return arbitrary int for the sum.  If $arr is not an array, return 0.
	 */
    public function multi_array_sum($arr,$key) {
		if (is_array($arr)) {
			$sum_no = 0;
			foreach($arr as $v){
			   $sum_no +=  $v[$key];
			 }
			return $sum_no;
		} else {
			return 0;
		}
	} // End of function multi_array_sum()

	/**
	 * Log API access in table 'logs'
	 * @param	array 	$response	API response
	 * @param	string	$req_ip		Request IP, as reported by Api_common::_getRequestIp()
	 * @return	none
	 */
	public function record_api_action($response, $req_ip) {
		$class = $this->ci->router->class;
		$method = $this->ci->router->method;
		$method_full = "{$class}::{$method}";
		if (in_array($method, Api_common::$record_api_action_excludes)) {
			$this->ci->utils->debug_log(__METHOD__, "Skipping API operation for method", $method_full);
			return;
		}
		$this->ci->utils->debug_log(__METHOD__, "Logging API operation for method", $method_full);

		// Determine username by token
		$this->ci->load->model([ 'common_token' ]);

		$token = $this->post_before_get('token');
		$username = '';
		if (!empty($token)) {
			$player = $this->ci->common_token->getPlayerInfoByToken($token);
			if (!empty($player)) {
				$username = $player['username'];
			}
		}
		if (empty($username)) {
			$username = $this->ci->input->post('username');
		}
		if (empty($username)) {
			$username = '(none)';
		}

		// Read http request details
		$http_req = $this->ci->utils->getHttpOnRequest();

		$data = [
			'user_agent'	=> $http_req['user_agent'] ,
			'cookie'		=> $http_req['cookie'] ,
		];

		// Collect all post/get input
		$post = $this->ci->input->post() ?: [];
		$get = $this->ci->input->get() ?: [];

		// Remove sensitive keys from post/get
		$mask_fields_rev = [ 'password', 'cpassword', 'hiddenPassword' ];
		$mask_fields = array_flip($mask_fields_rev);
		$post = array_diff_key($post, $mask_fields);
		$get = array_diff_key($get, $mask_fields);

		$params = [ 'post' => $post, 'get' => $get ];
		$extra = $params;
		$extra['response'] = $response;

		// [ 'username' => $username, 'params' => $params, 'data' => $data ]);

		$log_entry = [
			'username'		=> $username ,
			'management'	=> "Api_common:{$class}" ,
			'userRole'		=> '(player)' ,
			'action'		=> $method ,
			'description'	=> "Player Center API ($class)" ,
			'logDate'		=> date('c') ,
			'status'		=> '0' ,
			'ip'			=> $req_ip ,
			'referrer'		=> $http_req['referrer'] ,
			'uri'			=> current_url() ,
			'data'			=> json_encode($data) ,
			'extra'			=> json_encode($extra, JSON_PRETTY_PRINT) ,
			'params'		=> json_encode($params)
		];

		// $this->ci->utils->debug_log(__METHOD__, 'log_entry', $log_ent?ry);

		$this->ci->db->insert('logs', $log_entry);
	}

	/**
	 * Search and get a variable in POST, then GET
	 * POST is preferred; Will not check GET if the variable is in POST
	 * @param	string	$key	Name of the variable
	 * @return	string
	 */
	public function post_before_get($key) {
		$val = $this->ci->input->post($key);
		if (empty($val)) {
			$val = $this->ci->input->get($key);
		}

		return $val;
	}

	public function aff_check_cracking_code($trackingCode) {
		$result = $this->ci->affiliatemodel->checkTrackingCode($trackingCode);

		if ($result == false) {
			$this->is_unique_trackingCode = true;
		}
	} // End of function checkTrackingCode()

	public function aff_add_to_affiliates($affiliate_payout_id) {

		$this->ci->affiliatemodel->startTrans();

		// $hasher = new PasswordHash('8', TRUE);
		// $today = date("Y-m-d H:i:s");
		/*$trackingCode = $this->input->post('tracking_code');*/

		$is_unique_trackingCode = false;
		while (!$is_unique_trackingCode) {
			if ($this->ci->utils->isEnabledFeature('affiliate_tracking_code_numbers_only')) {
				// $this->ci->load->helper('string');
				// $trackingCode = random_string('numeric', 8);
				$trackingCode = $this->ci->utils->randomString(8);
			} else {
				$trackingCode = $this->ci->affiliatemodel->randomizer('trackingCode');
			}
			$is_unique_trackingCode = !$this->aff_check_cracking_code($trackingCode);
		}

		$ip_address = $this->ci->input->ip_address();
		$geolocation = $this->ci->utils->getGeoplugin($ip_address);

		// Determine parent_id from parent_username
		$parent_id = 0;
		$parent_username = $this->ci->input->post('parent_username');
		if (!empty($parent_username)) {
			$parent_username = preg_replace('/\W/', '', $parent_username);
			$parent_id = $this->ci->affiliatemodel->getAffiliateIdByUsername($parent_username);
		}
		// if ($this->ci->input->post('parentId') != NULL) {
		// 	$parentId = $this->input->post('parentId');
		// }
		// ($this->ci->input->post('lastname')) ? $lastname = $this->ci->input->post('lastname'): $lastname = "";
		$data = array(
			// 'parentId'	=> intval($this->ci->input->post('parent_id')) ,
			'parentId'	=> $parent_id ,
			'affiliatePayoutId'	=> $affiliate_payout_id			,
			'username'	=> $this->ci->input->post('username')	,
			'password'	=> $this->ci->salt->encrypt($this->ci->input->post('password'), $this->ci->config->item('DESKEY_OG'))	,
			'firstname'	=> $this->ci->input->post('firstname')	,
			'lastname'	=> $this->ci->input->post('lastname')	,
			'birthday'	=> $this->ci->input->post('birthday')	,
			'gender'	=> ucfirst($this->ci->input->post('gender'))		,
			'company'	=> $this->ci->input->post('company')	,
			'occupation'=> $this->ci->input->post('occupation')	,
			'email'		=> $this->ci->input->post('email')		,
			'city'		=> $this->ci->input->post('city')		,
			'address'	=> $this->ci->input->post('address')	,
			'zip'		=> $this->ci->input->post('zip')		,
			'state'		=> $this->ci->input->post('state')		,
			'country'	=> $this->ci->input->post('country')	,
			'mobile'	=> $this->ci->input->post('mobile')		,
			'phone'		=> $this->ci->input->post('phone')		,
			'im1'		=> $this->ci->input->post('im1')		,
			'imType1'	=> $this->ci->input->post('imtype1')	,
			'im2'		=> $this->ci->input->post('im2')		,
			'imType2'	=> $this->ci->input->post('imtype2')	,
			'modeOfContact'	=> strtolower($this->ci->input->post('mode_of_contact'))	,
			'website'	=> $this->ci->input->post('website')	,
			'currency'	=> $this->ci->input->post('currency')	,
			//'status'	=> (empty($trackingCode)) ? '1':'0'	,
			'status'	=> '1'	,
			'ip_address'=> $ip_address	,
			//'location'	=> $geolocation['geoplugin_city'].','.$geolocation['geoplugin_countryName']	,
			'location'	=> $geolocation['geoplugin_countryName']	,
			// 'createdOn'	=> $today	,
			'createdOn'	=> $this->ci->utils->getNowForMysql()	,
			'trackingCode'	=> $trackingCode	,
			'language'	=> $this->ci->input->post('language')	,
		);

		$affId = $this->ci->affiliatemodel->addAffiliate($data);

		$succ = $this->ci->affiliatemodel->endTransWithSucc();

		if (!$succ) {
			return null;
		}

		return $affId;
	}

	public function aff_reg_activation_message() {
		$contactTypeLabel = $this->ci->config->item('aff_contact_type_label');
		$contactType = $this->ci->config->item('aff_contact_type');

		$message = lang('con.23');
		$cont_tmpl = "%s: %s";
		if(!empty($contactTypeLabel) && !empty($contactType)) {
			$message .= sprintf("  %s %s: %s", lang('aff.aai93'), $contactTypeLabel, $contactType);
			if (!empty($this->ci->config->item('aff_contact_email'))) {
				$message .= sprintf("; %s %s.", lang('con.22'), $this->ci->config->item('aff_contact_email'));
			}
		}
		else if(!empty($this->ci->config->item('aff_contact_qq'))) {
			$message .= sprintf($cont_tmpl, lang('aff.login.contact.qq'), $this->ci->config->item('aff_contact_qq'));
		}
		else if(!empty($this->ci->config->item('aff_contact_email'))) {
			$message .= sprintf($cont_tmpl, lang('aff.login.contact.email'), $this->ci->config->item('aff_contact_email'));
		}
		else if(!empty($this->ci->config->item('aff_contact_skype'))) {
			$message .= sprintf($cont_tmpl, lang('aff.login.contact.skype'), $this->ci->config->item('aff_contact_skype'));
		}

		return $message;
	}

	public function aff_form_rules() {
		$this->ci->load->library([ 'form_validation' ]);

		// Set literal error messages (They are missing for unknown reason)
		// Note the "%n$s": CI vaildator undocumented error message params
		//   %1$s for field name, %2$s for rule param, %3$s for entered value
		$this->ci->form_validation->set_message('alpha_numeric', 'Only letters and numbers are allowed for %s');
		$this->ci->form_validation->set_message('min_length', '%1$s "%3$s" is too short, min length is %2$s places');
		$this->ci->form_validation->set_message('max_length', '%1$s "%3$s" is too long, max length is %2$s places');
		$this->ci->form_validation->set_message('is_unique', '%1$s "%3$s" is already in use');
		$this->ci->form_validation->set_message('valid_email', '%s format is invalid');

		// Validation rules following
		$this->ci->form_validation->set_rules('username', lang('aff.al10'), 'trim|required|min_length[5]|max_length[12]|alpha_numeric|is_unique[affiliates.username]');
		$this->ci->form_validation->set_rules('password', lang('reg.05'), 'trim|required|min_length[6]|max_length[12]');
		$this->ci->form_validation->set_rules('password_conf', lang('reg.07'), 'trim|required');
		$this->ci->form_validation->set_rules('email', lang('reg.a37'), 'trim|xss_clean|required|valid_email|is_unique[affiliates.email]');

		if ($this->aff_check_is_reg_field_required('First Name') == 0) {
			$this->ci->form_validation->set_rules('firstname', lang('aff.al14'), 'trim|required|xss_clean');
		} else {
			$this->ci->form_validation->set_rules('firstname', lang('aff.al14'), 'trim|xss_clean');
		}

		if ($this->aff_check_is_reg_field_required('Last Name') == 0) {
			$this->ci->form_validation->set_rules('lastname', lang('aff.al15'), 'trim|required|xss_clean');
		} else {
			$this->ci->form_validation->set_rules('lastname', lang('aff.al15'), 'trim|xss_clean');
		}

		if ($this->aff_check_is_reg_field_required('Birthday') == 0) {
			$this->ci->form_validation->set_rules('birthday', lang('aff.ai04'), 'trim|required|xss_clean');
		} else {
			$this->ci->form_validation->set_rules('birthday', lang('aff.ai04'), 'trim|xss_clean');
		}

		if ($this->aff_check_is_reg_field_required('Gender') == 0) {
			$this->ci->form_validation->set_rules('gender', lang('aff.ai05'), 'trim|required|xss_clean');
		} else {
			$this->ci->form_validation->set_rules('gender', lang('aff.ai05'), 'trim|xss_clean');
		}

		if ($this->aff_check_is_reg_field_required('Company') == 0) {
			$this->ci->form_validation->set_rules('company', lang('aff.ai06'), 'trim|required|xss_clean');
		} else {
			$this->ci->form_validation->set_rules('company', lang('aff.ai06'), 'trim|xss_clean');
		}

		if ($this->aff_check_is_reg_field_required('Occupation') == 0) {
			$this->ci->form_validation->set_rules('occupation', lang('aff.ai07'), 'trim|required|xss_clean');
		} else {
			$this->ci->form_validation->set_rules('occupation', lang('aff.ai07'), 'trim|xss_clean');
		}

		if ($this->aff_check_is_reg_field_required('Mobile Phone') == 0) {
			$this->ci->form_validation->set_rules('mobile', lang('reg.a54'), 'trim|required|xss_clean|numeric');
		} else {
			$this->ci->form_validation->set_rules('mobile', lang('reg.a54'), 'trim|xss_clean|numeric');
		}

		if ($this->aff_check_is_reg_field_required('Phone') == 0) {
			$this->ci->form_validation->set_rules('phone', lang('aff.ai15'), 'trim|required|xss_clean|numeric');
		} else {
			$this->ci->form_validation->set_rules('phone', lang('aff.ai15'), 'trim|xss_clean|numeric');
		}

		if ($this->aff_check_is_reg_field_required('City') == 0) {
			$this->ci->form_validation->set_rules('city', lang('reg.a19'), 'trim|required|xss_clean');
		} else {
			$this->ci->form_validation->set_rules('city', lang('reg.a19'), 'trim|xss_clean');
		}

		if ($this->aff_check_is_reg_field_required('Address') == 0) {
			$this->ci->form_validation->set_rules('address', lang('reg.a20'), 'trim|required|xss_clean');
		} else {
			$this->ci->form_validation->set_rules('address', lang('reg.a20'), 'trim|xss_clean');
		}

		if ($this->aff_check_is_reg_field_required('Zip Code') == 0) {
			$this->ci->form_validation->set_rules('zip', lang('reg.a21'), 'trim|required|xss_clean');
		} else {
			$this->ci->form_validation->set_rules('zip', lang('reg.a21'), 'trim|xss_clean');
		}

		if ($this->aff_check_is_reg_field_required('State') == 0) {
			$this->ci->form_validation->set_rules('state', lang('reg.a22'), 'trim|required|xss_clean');
		} else {
			$this->ci->form_validation->set_rules('state', lang('reg.a22'), 'trim|xss_clean');
		}

		if ($this->aff_check_is_reg_field_required('Country') == 0) {
			$this->ci->form_validation->set_rules('country', lang('reg.a23'), 'trim|required|xss_clean');
		} else {
			$this->ci->form_validation->set_rules('country', lang('reg.a23'), 'trim|xss_clean');
		}

		if ($this->aff_check_is_reg_field_required('Website') == 0) {
			$this->ci->form_validation->set_rules('website', lang('reg.a41'), 'trim|required|xss_clean');
		} else {
			$this->ci->form_validation->set_rules('website', lang('reg.a41'), 'trim|xss_clean');
		}

		if ($this->aff_check_is_reg_field_required('Instant Message 1') == 0) {
			$this->ci->form_validation->set_rules('imtype1', lang('reg.a30'), 'trim|xss_clean|required');
		} else {
			$this->ci->form_validation->set_rules('imtype1', lang('reg.a30'), 'trim|xss_clean');
		}

		if ($this->aff_check_is_reg_field_required('Instant Message 2') == 0) {
			$this->ci->form_validation->set_rules('imtype2', lang('reg.a35'), 'trim|xss_clean|required');
		} else {
			$this->ci->form_validation->set_rules('imtype2', lang('reg.a35'), 'trim|xss_clean');
		}

		if ($this->aff_check_is_reg_field_required('Language') == 0) {
			$this->ci->form_validation->set_rules('language', lang('ban.lang'), 'trim|required');
		} else {
			$this->ci->form_validation->set_rules('language', lang('ban.lang'), 'trim');
		}
	}

	public function aff_check_is_reg_field_required($field_name) {
		$reg_fields = $this->aff_get_reg_fields(2);

		foreach ($reg_fields as $key => $value) {
			if ($value['field_name'] == $field_name) {
				return $value['required'];
			}
		}

		return null;
	}

	protected function aff_get_reg_fields($type) {
		$this->ci->load->model('affiliatemodel');
		return $this->ci->affiliatemodel->getRegisteredFields(2);
	}

	public function aff_form_valid_error_to_array($fv_error_mesg) {
		$fv_err_ar = explode("\n", strip_tags($fv_error_mesg));
		$fv_err_ar = array_slice($fv_err_ar, 0, -1);
		$this->ci->utils->debug_log(__METHOD__, [ 'mesg' => $fv_error_mesg , 'ar' => $fv_err_ar ]);
		return $fv_err_ar;
	}

	/**
	 * Check values for following fields:
	 * 		password_conf, birthday (age >= 18), im[12], imtype[12]	(was checked with callback)
	 * 	 	gender, method_of_contact								(extra check for allowed values)
	 * 	 	parent_username											(existing affiliate only)
	 *
	 * @return	array 	[ exception_mesg, exception_code ]
	 */
	public function aff_reg_extra_checks() {
		try {
			$ex_mesg = '';
			$ex_code = 0;
			$extra = '';
			// Perform checks which was done by callback, for CI form validation callback fails across library
			$password = $this->ci->input->post('password');
			$password_conf = $this->ci->input->post('password_conf');
			if ($password != $password_conf) {
				throw new Exception("Password confirmation must match", Api_common::CODE_CA_PASSWORD_CONF_NOT_MATCH);
			}

			// Age check
			$birthday = $this->ci->input->post('birthday');
			if (!empty($birthday)) {
				$age = (time() - strtotime($birthday)) / 86400 / 365.25;
				if ($age < 18.0) {
					$extra = "dob: {$birthday}; age: {$age};";
					throw new Exception("Must be 18 or older to register", Api_common::CODE_CA_AGE_UNDER_18);
				}
			}

			// MOC check
			$moc = $this->ci->input->post('mode_of_contact');
			if (!empty($moc) && !in_array($moc, [ 'im', 'mobile', 'phone' ])) {
				$extra = "mode_of_contact: {$moc};";
				throw new Exception("Invalid value for mode_of_contact, please use any of im, mobile, phone", Api_common::CODE_CA_MOC_INVALID);
			}

			// gender check
			$gender = $this->ci->input->post('gender');
			if (!empty($gender) && !in_array(strtolower($gender), [ 'male', 'female' ])) {
				$extra = "gender: {$gender};";
				throw new Exception("Invalid value for gender, please use Male or Female", Api_common::CODE_CA_GENDER_INVALID);
			}

			// Parent username
			$parent_uname = $this->ci->input->post('parent_username');
			$parent_uname = preg_replace('/\W/', '', $parent_uname);
			if (!empty($parent_uname)) {
				$parent_id = $this->ci->affiliatemodel->getAffiliateIdByUsername($parent_uname);
				if (empty($parent_id)) {
					$extra = "parent_uname: {$parent_uname};";
					throw new Exception("Parent agent {$parent_uname} is not found", Api_common::CODE_CA_PARENT_AFF_NOT_FOUND);
				}
			}

			// im1/imtype1, im2/imtype2
			$im1 = $this->ci->input->post('im1');
			$im2 = $this->ci->input->post('im2');
			$imtype1 = $this->ci->input->post('imtype1');
			$imtype2 = $this->ci->input->post('imtype2');
			$this->ci->utils->debug_log(__METHOD__, [ 'im1' => $im1, 'im2' => $im1, 'imtype1' => $imtype1, 'imtype2' => $imtype2, ]);
			if (!preg_match("/^[\w.@-]*$/", $im1)) {
				throw new Exception("im1 contains invalid chars", Api_common::CODE_CA_IM_INVALID);
			}

			if (!preg_match("/^[\w.@-]*$/", $im2)) {
				throw new Exception("im2 contains invalid chars", Api_common::CODE_CA_IM_INVALID);
			}

			if (!preg_match("/^\w*$/u", $imtype1)) {
				throw new Exception("imtype1 contains invalid chars", Api_common::CODE_CA_IMTYPE_INVALID);
			}

			if (!preg_match("/^\w*$/u", $imtype2)) {
				throw new Exception("imtype2 contains invalid chars", Api_common::CODE_CA_IMTYPE_INVALID);
			}

			if (!empty($imtype1) && empty($im1)) {
				throw new Exception("Please fill im1", Api_common::CODE_CA_IM_ABSENT);
			}

			if (!empty($imtype2) && empty($im2)) {
				throw new Exception("Please fill im2", Api_common::CODE_CA_IM_ABSENT);
			}
		}
		catch (Exception $ex) {
			$ex_mesg = $ex->getMessage();
			$ex_code = $ex->getCode();
		}
		finally {
			return [ $ex_mesg, $ex_code, $extra ];
		}
	} // End function aff_reg_extra_checks

	/**
	 * DISUSED - only t1t_comapi_module_mobile_reg[12] uses
	 * which are not used by any live_stable clients
	 * @deprecated
	 * ---
	 * Sends validation SMS at register-time.  Worker routine for:
	 * 		% t1t_comapi_module_mobile_reg2::mobileRegSendSms()
	 * 		% t1t_comapi_module_mobile_reg1::mobileCreatePlayer()
	 * Usage note: Caller must provide either $player_id or $sessionId as identification:
	 * 		- In an unlogged-in scenario, like SMS registration:
	 * 			sessionId is required; use null as player_id
	 * 		- In an verify secnario, where player_id can be determined:
	 * 			player_id is required; for sessionId just use null, or provide actual sessionId if feasible
	 * Revisions:
	 *    OGP-7822	(Built, xcyl)
	 *    OGP-7983	(1st rev, xcyl)
	 *    OGP-8278	(ported to live_stable)
	 * @param	int			$player_id		== player.playerId (optional)
	 * @param	numerical	$mobile_number	The phone number
	 * @param 	int			$sessionId		== Session::userdata('session_id') (optional)
	 * @uses	libraries/sms/sms_sender
	 * @uses	models/sms_verification
	 *
	 * @return	Array		Standard return of [ 'code' (int), 'mesg' (string), 'mesg_debug' (string) ]
	 */
	// public function mobileCreatePlayer_send_sms($player_id, $mobile_number, $sessionId = null) {

	// 	$test_run = true;

	// 	$ret = [ 'code' => 0xa, 'mesg' => 'Unknown error', 'mesg_debug' => 'Execution incomplete' ];

	// 	try {
	// 		$this->ci->load->library([ 'session', 'sms/sms_sender' ]);
	// 		$this->ci->load->model([ 'sms_verification', 'player_model' ]);


	// 		if (!empty($player_id)) {
	// 			$player = $this->ci->player_model->getPlayerArrayById($player_id);

	// 			if (!is_array($player) || empty($player)) {
	// 				throw new Exception('Cannot fetch player, player_id invalid', 0x9);
	// 			}
	// 		}

	// 		if (!$test_run && $this->ci->utils->getConfig('disabled_sms')) {
	// 			throw new Exception('SMS disabled globally', 0x1);
	// 		}

	// 		// OGP-7983: Acquire sessionId from arguments
	// 		// $sessionId = $this->ci->session->userdata('session_id');
	// 		if (empty($sessionId)) {
	// 			$sessionId = Sms_verification::SESSION_ID_DEFAULT;
	// 		}

	// 		// Storing time of last send in session is also unreliable, but the alternative with redis is below.
	// 		$lastSmsTime = $this->ci->session->userdata('last_sms_time');
	// 		if (empty($lastSmsTime)){
	// 			$lastSmsTime = $this->ci->utils->readRedis($mobile_number.'_last_sms_time');
	// 		}
	// 		$smsCooldownTime = $this->ci->config->item('sms_cooldown_time');

	// 		// Should not send SMS without valid session ID or player_id
	// 		if (empty($sessionId) && empty($player_id)) {
	// 			throw new Exception('Internal error|Need either session ID or player ID', 0x3);
	// 		}

	// 		// Prevent sending SMS again within cooldown period
	// 		if ($lastSmsTime && time() - $lastSmsTime <= $smsCooldownTime) {
	// 			throw new Exception("Please wait at least {$smsCooldownTime} seconds before sending again", 0x4);
	// 		}

	// 		// Check if send count in last minute hits the limit
	// 		$codeCount = $this->ci->sms_verification->getVerificationCodeCountPastMinute();
	// 		$max_sms_per_min = (int) $this->ci->config->item('sms_global_max_per_minute');
	// 		if($max_sms_per_min > 0 && $codeCount > $max_sms_per_min) {
	// 			$this->ci->utils->error_log("Max count of SMS sent in past minute is hit ({$codeCount}/{$max_sms_per_min})");
	// 			throw new Exception("Max SMS count per minute ({$max_sms_per_min}) is hit", 0x5);
	// 		}

	// 		// Check if today's send count for a single number hits the limit
	// 		$numCount = $this->ci->sms_verification->getTodaySMSCountFor($mobile_number, Sms_verification::USAGE_COMAPI_MOBILE_REG);
	// 		$max_sms_per_day = (int) $this->ci->config->item('sms_max_per_num_per_day');
	// 		if($max_sms_per_day > 0 && $numCount >= $max_sms_per_day) {
	// 			$this->ci->utils->error_log("Max count of SMS sent today for single number is hit ({$numCount}/{$max_sms_per_day})");
	// 			throw new Exception("Max SMS count per day ({$max_sms_per_day}) is hit", 0x6);
	// 		}

	// 		// Generate code, build SMS contents
	// 		$code = $this->ci->sms_verification->getVerificationCode($player_id, $sessionId, $mobile_number, Sms_verification::USAGE_COMAPI_MOBILE_REG);
	// 	    $useSmsApi = $this->sms_sender->getSmsApiName();
	// 	    $msg = $this->ci->utils->createSmsContent($code, $useSmsApi);

	// 		if ($this->ci->utils->isEnabledFeature('enabled_send_sms_use_queue_server')) {
	// 			// Send message by queue server
	// 			$this->ci->load->model('queue_result');
	// 			$this->ci->load->library('lib_queue');

	// 			// $this->ci->lib_queue->addRemoteSMSJob($mobileNum, $content, $callerType, $caller, $state);
	// 			$this->ci->lib_queue->addRemoteSMSJob($mobile_number, $msg, Queue_result::CALLER_TYPE_PLAYER, $player_id, null);

	// 			$this->ci->session->set_userdata('last_sms_time', time());
	// 			$this->ci->utils->writeRedis($mobile_number.'_last_sms_time', time());
	// 		}
	// 		else {
	// 			// Or send it directly
	// 			if ($this->ci->sms_sender->send($mobile_number, $msg, $useSmsApi)) {
	// 				$this->ci->session->set_userdata('last_sms_time', time());
	// 				$this->ci->utils->writeRedis($mobile_number.'_last_sms_time', time());
	// 			} else {
	// 				$sms_api_error = $this->ci->sms_sender->getLastError();
	// 				throw new Exception("Error sending SMS|SMS API reports error: {$sms_api_error}", 0x8);
	// 			}
	// 		}

	// 		// Point of success - Only return success if execution reaches this point
	// 		$ret = [ 'code' => 0 , 'mesg' => 'SMS sent successfully', 'mesg_debug' => 'SMS sent successfully' ];
	// 	}
	// 	catch (Exception $ex) {
	// 		$mesg_ar = explode('|', $ex->getMessage());
	// 		$mesg = $mesg_ar[0];
	// 		$mesg_debug = count($mesg_ar) == 1 ? $mesg_ar[0] : $mesg_ar[1];
	// 		$ret = [ 'code' => $ex->getCode(), 'mesg' => $mesg, 'mesg_debug' => $mesg_debug ];
	// 	}
	// 	finally {
	// 		return $ret;
	// 	}
	// } // End of function registerSendSmsVerification()

	// public function comapi_send_sms($player_id, $mobile_number, $usage = Sms_verification::USAGE_COMAPI_SMS_VALIDATE, $session_id = Sms_verification::SESSION_ID_DEFAULT) {
	public function comapi_send_sms($player_id, $mobile_number, $usage = null, $session_id = null, $dialingCode = null) {

		$test_run = $this->ci->utils->getConfig('demo_mode');

		$ret = [ 'code' => 0xa, 'mesg' => 'Unknown error', 'mesg_debug' => 'Execution incomplete' ];

		try {
			$this->ci->load->library([ 'session', 'sms/sms_sender' ]);
			$this->ci->load->model([ 'sms_verification', 'player_model' ]);

			$player = $this->ci->player_model->getPlayerArrayById($player_id);

			// if (!$test_run && $this->ci->utils->getConfig('disabled_sms')) {
			// 	throw new Exception('SMS disabled globally', 0x1);
			// }

			if (empty($session_id)) {
				$session_id = Sms_verification::SESSION_ID_DEFAULT;
			}

			if (empty($usage)) {
				$usage = Sms_verification::USAGE_COMAPI_SMS_VALIDATE;
			}

			// Storing time of last send in session is also unreliable, but the alternative with redis is below.
			$lastSmsTime = $this->ci->session->userdata('last_sms_time');
			if (empty($lastSmsTime)){
				$lastSmsTime = $this->ci->utils->readRedis("{$mobile_number}_last_sms_time");
			}
			$smsCooldownTime = $this->ci->config->item('sms_cooldown_time');

			// Should not send SMS without valid session ID or player_id
			// if (empty($sessionId) && empty($player_id)) {
			// 	throw new Exception('Internal error|Need either session ID or player ID', 0x3);
			// }

			if ($this->ci->sms_verification->checkIPAndMobileLastTime($smsCooldownTime, $mobile_number)) {
				throw new Exception("Please wait at least {$smsCooldownTime} seconds before sending again", Api_common::CODE_SMSCOM_IP_OR_NUMBER_COOLDOWN_PERIOD);
			}

			// Prevent sending SMS again within cooldown period
			// if ($lastSmsTime && time() - $lastSmsTime <= $smsCooldownTime) {
			// 	throw new Exception("Please wait at least {$smsCooldownTime} seconds before sending again", 0x2);
			// }

			// Check if send count in last minute hits the limit
			$codeCount = $this->ci->sms_verification->getVerificationCodeCountPastMinute();
			$max_sms_per_min = (int) $this->ci->config->item('sms_global_max_per_minute');
			if($max_sms_per_min > 0 && $codeCount > $max_sms_per_min) {
				$this->ci->utils->error_log("Max count of SMS sent in past minute is hit ({$codeCount}/{$max_sms_per_min})");
				throw new Exception("Max SMS count per minute ({$codeCount}/{$max_sms_per_min}) is hit", Api_common::CODE_SMSCOM_SMS_LIMIT_PER_MINUTE_HIT);
			}

			// Check if today's send count for a single number hits the limit
			$numCount = $this->ci->sms_verification->getTodaySMSCountFor($mobile_number, $usage);
			$max_sms_per_day = (int) $this->ci->config->item('sms_max_per_num_per_day');
			if($max_sms_per_day > 0 && $numCount >= $max_sms_per_day) {
				$this->ci->utils->error_log("Max count of SMS sent today for single number is hit ({$numCount}/{$max_sms_per_day})");
				throw new Exception("Max SMS count per day ({$numCount}/{$max_sms_per_day}) is hit", Api_common::CODE_SMSCOM_SMS_LIMIT_PER_DAY_HIT);
			}

			// Generate code, build SMS contents
			// $code = $this->ci->sms_verification->getVerificationCode($player_id, Sms_verification::SESSION_ID_DEFAULT, $mobile_number, $usage);
		    $code = $this->ci->sms_verification->getVerificationCode($player_id, $session_id, $mobile_number, $usage);
		    $useSmsApi = $this->ci->sms_sender->getSmsApiName();
		    $msg = $this->ci->utils->createSmsContent($code, $useSmsApi);

		    if(!empty($player_id)){
		    	$playerContact = $this->ci->player_model->getPlayerInfoDetailById($player_id);
		    	$dialingCode = $playerContact['dialing_code'];
		    }

			$mobileNum = !empty($dialingCode)? $dialingCode.'|'.$mobile_number : $mobile_number;
			if ($this->ci->utils->isEnabledFeature('enabled_send_sms_use_queue_server')) {
				// Send message by queue server
				$this->ci->load->model('queue_result');
				$this->ci->load->library('lib_queue');

				// $this->ci->lib_queue->addRemoteSMSJob($mobileNum, $content, $callerType, $caller, $state);
				$this->ci->lib_queue->addRemoteSMSJob($mobileNum, $msg, Queue_result::CALLER_TYPE_PLAYER, $player_id, null);

				$this->ci->session->set_userdata('last_sms_time', time());
				$this->ci->utils->writeRedis($mobile_number.'_last_sms_time', time());
			}
			else {
				// Or send it directly
				if ($this->ci->sms_sender->send($mobileNum, $msg, $useSmsApi)) {
					$this->ci->session->set_userdata('last_sms_time', time());
					$this->ci->utils->writeRedis($mobile_number.'_last_sms_time', time());
				} else {
					$sms_api_error = $this->ci->sms_sender->getLastError();
					throw new Exception("Error sending SMS: {$sms_api_error}|SMS API reports error: {$sms_api_error}", Api_common::CODE_SMSCOM_3RD_PARTY_SMS_SERVICE_ERROR);
				}
			}

			// Point of success - Only return success if execution reaches this point
			$ret = [
				'code' => 0 ,
				'mesg' => 'SMS sent successfully',
				'mesg_debug' => 'SMS sent successfully'
			];
		}
		catch (Exception $ex) {
			$mesg_ar = explode('|', $ex->getMessage());
			$mesg = $mesg_ar[0];
			$mesg_debug = count($mesg_ar) == 1 ? $mesg_ar[0] : $mesg_ar[1];
			$ret = [
				'code' => $ex->getCode(),
				'mesg' => $mesg,
				'mesg_debug' => $mesg_debug
			];
		}
		finally {
			return $ret;
		}
	} // End of function comapi_send_sms()

	/**
	 * Extract some leading POST fields as signature
	 * @return	array 	5 leading post fields and crc32 of them combined as 'post_checksum'
	 */
	public function post_short() {
		// $post_l20 = array_slice($this->ci->input->post(), 0, 20, 'preserve_keys');
		$post = $this->ci->input->post();
		$post_lead = array_slice($this->ci->input->post(), 0, 5, 'preserve_keys');
		$post_csum = sprintf("%08x", crc32(json_encode($post)));
		$post_short = array_merge([ 'post_checksum' => $post_csum ], $post_lead);

		return $post_short;
	}

	/**
	 * Workhorse method for Api_common::login()
	 * Built in OGP-9059, modified in OGP-10035 to incorporate checks
	 * for self exclusion
	 * @param	string	$username	Player's username
	 * @param	string	$password	Player's password
	 * @see		Api_common::login()
	 *
	 * @return	JSON	Standard JSON return [ success, code, messsage, result ]
	 *                  result = [ playerName, playerId, token ] on success
	 */
	public function login_priv($username, $password) {
		try {
			$this->ci->load->model(['player_model']);
			$this->ci->load->library([ 'salt', 'language_function', 'player_library' ]);

			$extra_errors = null;

    		// Check empty username/password
    		if (empty($username)) {
    			throw new Exception(lang("Username invalid or empty"), Api_common::CODE_LOGIN_INVALID_USERNAME);
    		}

    		if (empty($password)) {
    			throw new Exception(lang("Password invalid or empty"), Api_common::CODE_LOGIN_INVALID_PASSWORD);
    		}

    		// Does player exist by this username?
    		$player_id = $this->ci->player_model->getPlayerIdByUsername($username);
    		if (empty($player_id)) {
    			throw new Exception(lang("Invalid player"), Api_common::CODE_LOGIN_INVALID_PLAYER);
    		}

			/**
	         * OGP-10475: Return of Player_library::login_by_password();
	         * [ 'success' (bool), 'errors' (array) ]
	         * Possible keys for array errors:
	         *   blocked
	         *   login
	         *   selfexclusion
	         *   password
	         */

			$login_res = $this->ci->player_library->login_by_password($username, $password);

 			$this->ci->utils->debug_log(__METHOD__, 'login_res', $login_res);

			if (!$login_res['success']) {
				$errors = $login_res['errors'];
				if (isset($errors['blocked'])) {
					$extra_errors = $errors['blocked'];
					throw new Exception(lang("Player is blocked"), Api_common::CODE_LOGIN_USER_IS_BLOCKED);
				}
				else if (isset($errors['login'])) {
					$extra_errors = $errors['login'];
					throw new Exception(lang("Login failed"), Api_common::CODE_LOGIN_LOGIN_FAILED);
				}
				else if (isset($errors['selfexclusion'])) {
					$extra_errors = $errors['selfexclusion'];
					throw new Exception(lang("Player is under self exclusion"), Api_common::CODE_LOGIN_USER_UNDER_SELF_EXCLUSION);
				}
				else if (isset($errors['password'])) {
					$extra_errors = $errors['password'];
					throw new Exception(lang("Password invalid or empty"), Api_common::CODE_LOGIN_INVALID_PASSWORD);
				}
				else {
					throw new Exception(lang("Login failed"), Api_common::CODE_LOGIN_LOGIN_FAILED);
				}
			}

			$this->ci->player_model->updateLoginInfo($player_id, $this->ci->input->ip_address(), $this->ci->utils->getNowForMysql());

			// Get player's available token (the default behavior)
			$token = $this->ci->common_token->getPlayerToken($player_id);

			// If 'single_player_session' is enabled: (OGP-13940)
			if ($this->ci->operatorglobalsettings->getSettingValue('single_player_session')) {
				// Dispose current token
				$this->ci->common_token->disableToken($token);
				// And generate a new one
				$token = $this->ci->common_token->getPlayerToken($player_id);
			}

			$language = $this->ci->player_model->getLanguageFromPlayer($player_id);

			if ($language) {
				$this->ci->language_function->setCurrentLanguage($language);
			}

			$ret = [
            	'code'		=> Api_common::CODE_SUCCESS ,
            	'mesg'		=> 'Player logged in',
            	'result'	=> [
            		'playerName'	=> $username ,
            		'playerId'		=> $player_id ,
            		'token'			=> $token
            	]
            ];

            $this->ci->utils->debug_log(__FUNCTION__, 'Response', $ret);
		}
		catch (Exception $ex) {
	    	$this->ci->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage(), 'extra_errors' => $extra_errors ]);
	    	$ret = [
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    		// 'result'	=> [ 'extra_error_mesg' => $extra_errors ]
	    	];
	    }
	    finally {
	    	return $ret;
	    }
	}

	public function player_balance_summary($player_id) {
		$subw_res = $this->available_subwallet_list($player_id);
		if ($subw_res['success'] == false) {
			return false;
		}
		$ret = $subw_res['result'];

		$subw_sum = 0.0;
		foreach ($ret['subwallets'] as $subw => $amount) {
			$subw_sum += $amount;
		}
		$ret['subwallet_total'] = $subw_sum;

		unset($ret['subwallets']);
		unset($ret['wallets']);
		unset($ret['success']);

		return $ret;
	}

	/**
	 * Returns balance of main wallet and sub wallets from bigwallet
	 * Step 1/2 in player balance update
	 * Ported from Async::available_subwallet_list()
	 *
	 * @param	int		$player_id	== player.playerId
	 * @param	bool	$ignore_0	Skip updating for wallets that hold $0
	 * @used-by	Api_common::queryPlayerBalance()
	 * @see		Async::available_subwallet_list()
	 *
	 * @return	[ success:bool, code:int, mesg:string, result:mixed ]
	 *          Where result = [
	 *          	mainwallet: decimal,	// mainwallet balance
	 *          	frozen:		decimal ,	// frozen balance
	 *          	subwallets: [ api_id => decimal ] , // balance of subwallets
	 *          	wallets:	[ api_id ]	// subwallets to update
	 *          ]
	 */
	public function available_subwallet_list($player_id, $ignore_0 = true) {
		try {
			$result = [];
			$ignore_0 = !$this->ci->utils->isEnabledFeature('force_refresh_all_subwallets') && $ignore_0;

			$this->ci->load->model([ 'wallet_model' ]);

			$wallets = $this->ci->wallet_model->getAvailableRefreshWalletListByPlayerId($player_id, $ignore_0);

			$this->ci->utils->debug_log(__METHOD__, 'subwallet-list', $wallets);

			if (!$wallets['success']) {
				throw new Exception('Error fetching list of subwallets', 3);
			}

			if (!isset($wallets['bigWallet']) || empty($wallets['bigWallet'])) {
				throw new Exception('No wallet found', 1);
			}

			$bigwallet = $wallets['bigWallet'];
			$subwallet = $bigwallet['sub'];
	        if (empty($subwallet)) {
	        	throw new Exception('No subwallet found', 2);
	        }

	        $sub_res = [];
	        foreach ($subwallet as $sub) {
	        	$sub_res[$sub['subwalletId']] = $sub['total'];
	        }

	        $result = [
	        	'mainwallet'	=> $bigwallet['main']['total_nofrozen'] ,
	        	'frozen'		=> $bigwallet['total_frozen'] ,
	        	'subwallets'	=> $sub_res ,
	        	// 'wallets'		=> array_keys($sub_res) ,
	        	'wallets'		=> [] ,
	        	'success'		=> true
	        ];

			$ret = [ 'success' => true, 'code' => 0, 'mesg' => '', 'result' => $result ];

		}
		catch (Exception $ex) {
			$ret = [ 'success' => false, 'code' => $ex->getCode(), 'mesg' => $ex->getMessage() ];
		}
		finally {
			return $ret;
		}

	} // End function available_subwallet_list()

	// public function available_subwallet_list0($player_id, $ignore_0 = true) {
	// 	try {
	// 		$result = [];
	// 		$ignore_0 = !$this->ci->utils->isEnabledFeature('force_refresh_all_subwallets') && $ignore_0;

	// 		$this->ci->load->model(['wallet_model']);

	//         $bigWallet=$this->ci->wallet_model->getBigWalletByPlayerId($player_id);
	//         if (empty($bigWallet)) {
	//         	throw new Exception('No wallet found', 1);
	//         }

	//         $sub=$bigWallet['sub'];
	//         if (empty($sub)) {
	//         	throw new Exception('No subwallet found', 2);
	//         }

	// 		$availableWallet = [];
	// 		foreach ($sub as $subWalletId => $subWallet) {
	// 			if (!empty($subWallet)) {
	// 				//don't ignore 0 or really >0
	// 				if (!$ignore_0 || $this->ci->utils->roundCurrencyForShow($subWallet['total_nofrozen'])>0 ){
	// 					$availableWallet[] = $subWalletId;
	// 				}
	// 			}
	// 		}

	// 		//add main wallet
	// 		$result['mainwallet'] = $this->ci->utils->formatCurrencyNumber($bigWallet['main']['total_nofrozen']);
	// 		$result['frozen']     = $this->ci->utils->formatCurrencyNumber($bigWallet['main']['frozen']);

	// 		$result['subwallets'] = [];

	// 		foreach ($bigWallet['sub'] as $apiId => $sub) {
	// 			$result['subwallets'][$apiId] = $sub['total_nofrozen'];
	// 		}

	// 		if (!empty($availableWallet)) {
	// 			$result['wallets'] = $availableWallet;
	// 			$result['success'] = true;
	// 		}
	// 		else {
	// 			$result['wallets'] = [];
	// 			$result['success'] = true;
	// 		}

	// 		$ret = [ 'success' => true, 'code' => 0, 'mesg' => '', 'result' => $result ];

	// 	}
	// 	catch (Exception $ex) {
	// 		$ret = [ 'success' => false, 'code' => $ex->getCode(), 'mesg' => $ex->getMessage() ];
	// 	}
	// 	finally {
	// 		return $ret;
	// 	}

	// } // End function available_subwallet_list0()

	/**
	 * Inquires game APIs to update game wallet balance
	 * Step 2/2 in player balance update
	 * Ported from Async::player_query_all_balance()
	 *
	 * @param	int		$api_id		Game API ID, member of array 'wallets' in return value of ::available_subwallet_list()
	 * @param	int		$playerId		== player.playerId
	 * @param	string	$playerUsername	== player.username
	 * @used-by	Api_common::queryPlayerBalance1()
	 * @see		Async::player_query_all_balance()
	 *
	 * @return	TBA
	 */
	public function player_query_all_balance($api_id, $playerId, $playerUsername) {
		$result = [];

		$manager = $this->ci->utils->loadGameManager();
		$this->ci->load->model(['player_model', 'wallet_model', 'game_provider_auth']);

		$balances = Array();
		if (!empty($api_id)) {
			$balances = $this->player_query_balance_by_platform($api_id, $playerId, $playerUsername);
            $this->ci->utils->debug_log(__FUNCTION__, 'player_query_balance_by_platform - balances', $balances);
		}
		// else {
		// 	//sync all balance
		// 	$balances = $manager->queryBalanceOnAllPlatforms($playerUsername);
		// 	$this->ci->utils->debug_log('queryBalanceOnAllPlatforms - balances', $balances);
		// }

		$game_platforms = $this->ci->game_provider_auth->getGamePlatforms($playerId);
		$this->ci->utils->debug_log(__FUNCTION__, 'player_query_all_balance - balances', $balances);
		// $this->ci->utils->debug_log(__FUNCTION__, 'player_query_all_balance - game_platforms', $game_platforms);
		$controller = $this;
		$subwallets = array();
		$frozen = 0;
		$mainWallet = 0;

		// Success:
		// - the api to check:	== result.success
		// - other apis:		== game_platform[k].register
		$success = [];
		if (!empty($balances)){
			foreach ($balances as $api_id => $api_res) {
				$success[$api_id] = $api_res['success'];
			}
		}

		foreach ($game_platforms as $gp) {
			if (!empty($gp['register'])) {
				$success[$gp['id']] = true;
			}
		}

		$this->ci->lockAndTransForPlayerBalance($playerId, function () use ($controller, $playerId, $balances, &$mainWallet, &$frozen, &$subwallets) {

			$bigWallet = $this->ci->wallet_model->getBigWalletByPlayerId($playerId);

			$frozen = $this->ci->utils->formatCurrencyNumber($bigWallet['main']['frozen']); //$this->ci->player_model->getPendingBalanceById($playerId);

			$mainWallet = $this->ci->utils->formatCurrencyNumber($bigWallet['main']['total_nofrozen']); //$this->ci->wallet_model->getMainWalletBalance($playerId);

			foreach ($bigWallet['sub'] as $apiId => $subWallet) {
				$subwallets[$apiId] = $controller->ci->utils->formatCurrencyNumber($subWallet['total_nofrozen']);
			}

			// $this->ci->utils->debug_log('lockAndTransForPlayerBalance - before balances', $playerId, $balances, $mainWallet, $frozen, $subwallets, $bigWallet);

			if (!empty($balances)) {
				foreach ($balances as $apiId => $apiRlt) {
					if ($apiRlt['success']) {
						$balance = $apiRlt['balance'];
						$subwallets[$apiId] = $controller->ci->utils->formatCurrencyNumber($balance);
						// 		$api = $controller->ci->utils->loadExternalSystemLibObject($apiId);
						// 		// $this->ci->utils->debug_log('apiId', $apiId, 'balance', $balance);
						// 		$api->updatePlayerSubwalletBalance($playerId, $balance);
					}
				}

				$controller->ci->wallet_model->updateSubWalletsOnBigWallet($playerId, $balances);

				if($this->isWalletUpdated($bigWallet, $subwallets)) {
					# Only record balance history when there is a change
					$controller->ci->wallet_model->recordPlayerAfterActionWalletBalanceHistory(Wallet_model::BALANCE_ACTION_REFRESH, $playerId, null, -1, 0, null, null, null, null, null);
				}
			}

			return true;

		}); // End of $this->ci->lockAndTransForPlayerBalance() closure

		$res = [
			'api_id'		=> $api_id,
			'mainwallet'    => $mainWallet,
			'frozen'        => $frozen,
			'subwallets'    => $subwallets ,
			'success'       => $success ,
			'subject_api'	=> [
				'api_id'			=> $api_id ,
				'success'			=> isset($success[$api_id]) && $success[$api_id] == true ,
				'subwallet_balance'	=> isset($subwallets[$api_id]) ? $subwallets[$api_id] : -32767
			]
		];

		$this->ci->utils->debug_log('player_query_all_balance - result', $res);

		$this->ci->utils->debug_log('player_query_all_balance ======================= End ===============', 'End');

		// Point of execution success
		// $ret = [ 'success' => true, 'code' => 0, 'mesg' => '', 'result' => $res ];
		return $res;

	} // End function player_query_all_balance()

	/**
	 * Really inquires game APIs to update game wallet balance
	 * Worker method for ::player_query_all_balance()
	 * Ported from Async::player_query_balance_by_platform()
	 *
	 * @param	int		$game_platform_id	Game API ID
	 * @param	int		$playerId			== player.playerId
	 * @param	string	$playerUsername		== player.username
	 * @used-by	Comapi_lib::player_query_all_balance()
	 * @see		Async::player_query_balance_by_platform()
	 *
	 * @return	array
	 */
	public function player_query_balance_by_platform($game_platform_id, $playerId, $playerUsername) {
		$result = [];
		try {
			$this->ci->load->model(array('player_model', 'wallet_model', 'game_provider_auth'));

			// $playerId = $this->ci->authentication->getPlayerId();
			// $playerUsername = $this->ci->authentication->getUsername();
			$api = $this->ci->utils->loadExternalSystemLibObject($game_platform_id);

			if (!$this->ci->external_system->isGameApiActive($api->getPlatformCode())) {
				throw new Exception("Game API {$game_platform_id} not active", 1);
			}

			// Check if player registered in game
			$isRegisteredFlag = $this->ci->game_provider_auth->isRegisterd($playerId, $api->getPlatformCode());
			$this->ci->utils->debug_log(__METHOD__, 'isRegisteredFlag', $isRegisteredFlag, $game_platform_id);

			// Check if player is blocked
			$isBlockedUsernameInDBFlag = $this->ci->game_provider_auth->isBlockedUsernameInDB($playerId, $api->getPlatformCode());
			$this->ci->utils->debug_log(__METHOD__, 'isBlockedUsernameInDBFlag', $isRegisteredFlag, $game_platform_id);

			// If player not registered
			if (!$isRegisteredFlag) {
				throw new Exception("Player not registered in game", 2);
			}
			// If player is blocked in DB
			if ($isBlockedUsernameInDBFlag) {
				throw new Exception("Player blocked in DB", 3);
			}

			$this->ci->utils->debug_log(__METHOD__, 'start query balance', $game_platform_id);
			$balance = $api->queryPlayerBalance($playerUsername);
			$this->ci->utils->debug_log(__METHOD__, 'balance', $balance, $game_platform_id);

			// Query successful, balance figure absent
			if ($balance['success'] && !isset($balance['balance'])) {
				throw new Exception("return success=true, but no balance, API: {$api->getPlatformCode()}");
			}

			// Query successful, balance figure present
			if ($balance['success'] && isset($balance['balance'])) {
				$result[$game_platform_id] = Array(
					'success' => $balance['success'],
					'balance' => $balance['balance'],
				);
			}
			else {
			// Query failed
				$result[$game_platform_id] = Array(
					'success' => $balance['success'],
					'balance' => 0,
				);
			}

		}
		catch (Exception $ex) {
			$this->ci->utils->debug_log(__FUNCTION__, $ex->getMessage());
			$result[$game_platform_id] = Array(
				'success' => false,
				'balance' => 0,
			);
		}
		finally {
			return $result;
		}

	} // End function player_query_balance_by_platform()

	/**
	 * Worker method for ::player_query_all_balance()
	 *
	 * @param	array 	$bigWallet			bigWallet data structure from query
	 * @param	array 	$updatedSubWallets	updatedSubwallets structure
	 * @see		Comapi_lib::player_query_all_balance()
	 *
	 * @return bool
	 */
	protected function isWalletUpdated($bigWallet, $updatedSubWallets) {
		foreach($updatedSubWallets as $apiId => $subWalletBalance) {
			$oldSubWalletBalance = $bigWallet['sub'][$apiId]['total'];
			if($this->ci->utils->compareFloat($oldSubWalletBalance, $subWalletBalance) != 0) {
				$this->ci->utils->debug_log("Wallet [$apiId] updated from [$oldSubWalletBalance] to [$subWalletBalance]");
				return true;
			}
		}
		$this->ci->utils->debug_log("Wallet has no update");
		return false;
	} // End function isWalletUpdated()

	/**
	 * Generate wallets_mapping for Api_common::queryPlayerBalance() by SBE way
	 * @param	array 	$subwallets		[ api_id => balance ] array
	 *
	 * @return	array 	[ api_id => [ currency, game ] ] array
	 */
	public function get_mapping_for_subwallets($subwallets) {
	    $currency = $this->ci->utils->getDefaultCurrency();
		$game_map = $this->ci->utils->getGameSystemMap();
		$this->ci->load->model([ 'external_system' ]);

		$wallets_mapping = [];
		foreach ($subwallets as $api_id => $balance) {
			$wallets_mapping[$api_id] = [
				'currency'	=> $currency ,
				'game'		=> $game_map[$api_id] ,
				'status'	=> $this->ci->external_system->isGameApiMaintenance($api_id) ? 'maintenance' : 'normal'
			];
		}

		return $wallets_mapping;
	} // End function get_mapping_for_subwallets()

	/**
	 * Get all or selected system features, workhorse for common API getSysFeature
	 *
	 * @param	array 	$feature_names	Array of features to return
	 * @see		Api_common::getSysFeatures()
	 *
	 * @return	array 	[ features_selected (array), features_log (str), features_mode (str)]
	 */
	public function gsf_get_sys_features($feature_names) {
		$this->ci->load->model('system_feature');

		// Prepare feature map
		// -- If cache not available, directly get from db
		if ($this->ci->utils->getConfig('disable_cache') != false) {
			$this->ci->utils->debug_log(__METHOD__, "Directly from db");
			$feature_map = [];
			$features_all = $this->ci->system_feature->get();
			foreach ($features_all as $f) {
				$feature_map[$f['name']] = $f['enabled'];
			}
		}
		// -- Or use existing cache mechanism
		else {
			$this->ci->utils->debug_log(__METHOD__, "Using cache");
			$this->ci->system_feature->saveAllFeaturesToCache();
			$feature_map = $this->ci->utils->getJsonFromCache(System_feature::ALL_FEATURES_JSON_KEY);
		}


		$features_log = null;
		$features_mode = null;
		// Tailor feature map with selected features
		if (!empty($feature_names) && !empty($feature_map)) {
			$features_selected = $this->ci->utils->array_select_fields($feature_map, $feature_names);
			$features_log = $features_selected;
			$features_mode = 'Selected';
		}
		// Or just keep all
		else {
			$features_selected = $feature_map;
			$features_log = 'Full featuresMap, skipped';
			$features_mode = 'All';
		}

		foreach ($features_selected as & $f) {
			$f = (bool) $f;
		}

		return [ $features_selected, $features_log, $features_mode ];
	} // End function gsf_get_sys_features()

	/**
	 * Get all registration settings from config
	 * Workhorse for common API getRegSettings
	 *
	 * @see		Api_common::getRegSettings()
	 *
	 * @return	array
	 */
	public function gsf_get_config_reg_settings() {
		$validators = $this->ci->utils->getConfig('player_validator');

		$settings = [
			'password_restricted_mode' => (bool) $this->ci->utils->isRestrictPasswordEnabled() ,
			'username_restricted_mode' => (bool) $this->ci->utils->isRestrictUsernameEnabled()
		];

		$res = [
			'settings' => $settings ,
			'validators' => $validators ,
		];

		return $res;
	} // End function gsf_get_config_reg_settings

	/**
	 * Get player registration settings from SBE registration settings
	 * Workhorse for common API getSbeRegSettings
	 *
	 * @see		Api_common::getSbeRegSettings()
	 *
	 * @return	array
	 */
	public function gsf_get_sbe_reg_settings() {
		$fields = [ 'field_name', 'alias', 'visible', 'required' ];
		$map_visible	= [ 0 => true, 1 => false ];
		$map_required	= [ 0 => true, 1 => false ];

		$this->ci->load->model('registration_setting');
		$exclusion = $this->ci->utils->getConfig('excluded_in_registration_settings');
		$reg_fields_bare = $this->ci->registration_setting->getRegistrationFieldsBare();

		$reg_fields = [];
		foreach ($reg_fields_bare as $key => & $rf) {
			if (in_array($rf['alias'], $exclusion)) { continue; }
			if (in_array($rf['registrationFieldId'], [ 54, 55, 56, 57, 58 ])) { continue; }
			$rf = $this->ci->utils->array_select_fields($rf, $fields);
			$rf['visible']	= isset($map_visible[intval($rf['visible'])]) ? $map_visible[intval($rf['visible'])] : null;
			$rf['required'] = isset($map_required[intval($rf['required'])]) ? $map_required[intval($rf['required'])] : null;
			$reg_fields[] = $rf;
		}

		$validators = $this->ci->utils->getConfig('player_validator');

		$username_lim = [
			'len_min'	=> $validators['username']['min'] ,
			'len_max'	=> $validators['username']['max'] ,
			'restricted_mode' => (bool) $this->ci->utils->isRestrictUsernameEnabled() ,
		];

		$passwd_limits = $this->ci->utils->isPasswordMinMaxEnabled();
		$passwd_lim = [
			'len_min'		=> !empty($passwd_limits['min']) ? $passwd_limits['min'] : Registration_setting::PASSWORD_MINIMUM_LENGTH ,
			'len_max'		=> !empty($passwd_limits['max']) ? $passwd_limits['max'] : Registration_setting::PASSWORD_MAXIMUM_LENGTH ,
			'len_custom'	=> array_key_exists('password_min_max_enabled', $passwd_limits) ? $passwd_limits['password_min_max_enabled'] : false ,
			'restricted_mode'	=> (bool) $this->ci->utils->isRestrictPasswordEnabled()
		];

		// unset($validators['password']['min']);
		// unset($validators['password']['max']);
		// unset($validators['username']['min']);
		// unset($validators['username']['max']);

		$res = [
			'fields' => $reg_fields ,
			'settings'		=> [
				'password' => $passwd_lim ,
				'username' => $username_lim
			] ,
			'validators' => $validators
		];

		return $res;
	} // End function gsf_get_sbe_reg_settings

	/**
	 * Fixes value of max deposit for payment accounts
	 * @param	decimal		$vip_max_deposit	vip_rule_max_deposit_trans reported by Payment_account::getAvailableDefaultCollectionAccount()
	 * @return	decimal
	 */
	public function depcat_fix_deposit_max($vip_max_deposit) {
		$vip_max_deposit = $vip_max_deposit <= 0 ? $vip_max_deposit = $this->ci->utils->getConfig('defaultMaxDepositDaily') : $vip_max_deposit;

		return $vip_max_deposit;
	}

	/**
	 * Fixes value of min deposit for payment accounts
	 * @param	decimal		$vip_min_deposit	vip_rule_min_deposit_trans reported by Payment_account::getAvailableDefaultCollectionAccount()
	 * @return	decimal
	 */
	public function depcat_fix_deposit_min($vip_min_deposit) {
		$vip_min_deposit = $vip_min_deposit <= 0 ? 0 : $vip_min_deposit;

		return $vip_min_deposit;
	}

    /**
     * Group available payments in categories, workhorse for Api_common::depositPaymentCategories()
     * Converted (ported) from player_center2/Deposit::deposit_category() and related view
     * OGP-13250
     * @param	int		$player_id		== player.playerId
     * @see		player_center2/Deposit::deposit_category()
     * @see		stable_center2/cashier/deposit/manual/deposit_category.php
     * @return	array 	assoc array:
     *                    > manual | auto
     *                    >>	(categories)
     *                    >>>		category_id
     *                    	  		category_text
     *                              list
     *                    >>>>		   (payment accounts)
     */
    public function depcat_deposit_paycats($player_id, $mobile = false) {
    	$this->ci->load->model('payment_account');

    	$payment_all_accounts = $this->ci->payment_account->getAvailableDefaultCollectionAccount($player_id, null, null, null, $mobile);

    	// **** Determine payment_manual_accounts
    	$payment_manual_accounts = ($payment_all_accounts[MANUAL_ONLINE_PAYMENT]['enabled']) ? $payment_all_accounts[MANUAL_ONLINE_PAYMENT]['list'] : [];
        if($payment_all_accounts[LOCAL_BANK_OFFLINE]['enabled']){
            foreach($payment_all_accounts[LOCAL_BANK_OFFLINE]['list'] as $payment_account){
                $payment_manual_accounts[] = $payment_account;
            }
        }

		$payment_manual_account_list = [];

		$show_deposit_bank_details = $this->ci->utils->isEnabledFeature('show_deposit_bank_details');

		// Default deposit min/max
		$payment_manual_min_deposit=$this->ci->utils->getConfig('defaultMinDepositDaily');
		$payment_manual_max_deposit=$this->ci->utils->getConfig('defaultMaxDepositDaily');

		foreach ($payment_manual_accounts as $payment_manual) {
		    $payment_manual_min_deposit = ($payment_manual_min_deposit === FALSE) ? $payment_manual->vip_rule_min_deposit_trans : $payment_manual_min_deposit;
		    $payment_manual_min_deposit = ($payment_manual->vip_rule_min_deposit_trans < $payment_manual_min_deposit) ? $payment_manual->vip_rule_min_deposit_trans : $payment_manual_min_deposit;
		    $payment_manual_max_deposit = ($payment_manual->vip_rule_max_deposit_trans > $payment_manual_max_deposit) ? $payment_manual->vip_rule_max_deposit_trans : $payment_manual_max_deposit;

		    $payment_account_data = [
		        'payment_account_link_num'	=> $payment_manual->id,
		        'bankTypeId'		=> $payment_manual->bankTypeId,
		        'bank_name_local'	=> lang($payment_manual->payment_type),
		        'minDeposit'		=> $this->depcat_fix_deposit_max($payment_manual->vip_rule_min_deposit_trans),
		        'maxDeposit'		=> $this->depcat_fix_deposit_min($payment_manual->vip_rule_max_deposit_trans),
		        // 'minDeposit_currency'		=> $this->ci->utils->formatCurrency($payment_manual->vip_rule_min_deposit_trans),
		        // 'maxDeposit_currency'		=> $this->ci->utils->formatCurrency($payment_manual->vip_rule_max_deposit_trans),
		        // 'flag'						=> $payment_manual->flag,
		        'second_category_flag'	=> $payment_manual->second_category_flag,
		        'order'				=> $payment_manual->payment_order
		    ];

		    // if($show_deposit_bank_details){
		    //     $payment_account_data['bankAccountName'] = $payment_manual->payment_account_name;
		    //     $payment_account_data['bankAccountNo'] = $payment_manual->payment_account_number;
		    //     $payment_account_data['bankCity'] = NULL;
		    //     $payment_account_data['branchName'] = $payment_manual->payment_branch_name;
		    // }

		    // OGP-14655: extra stats for manual account to tell coll accounts of same bank type but different holder/number
	        $payment_account_data['bank_acc_name'] = $payment_manual->payment_account_name;
	        $payment_account_data['bank_acc_no'] = $payment_manual->payment_account_number;
	        $payment_account_data['bank_branch'] = $payment_manual->payment_branch_name;

	        ksort($payment_account_data);

		    if ($payment_manual->flag == MANUAL_ONLINE_PAYMENT || $payment_manual->flag == LOCAL_BANK_OFFLINE) {
		        $payment_manual_account_list['cid-' . $payment_account_data['payment_account_link_num']] = $payment_account_data;
		    }
		}


		// **** Determine payment_auto_accounts
		$payment_auto_accounts = ($payment_all_accounts[AUTO_ONLINE_PAYMENT]['enabled']) ? $payment_all_accounts[AUTO_ONLINE_PAYMENT]['list'] : [];

		$payment_auto_account_list = [];

		foreach ($payment_auto_accounts as $payment_account) {
		    $payment_account_data = [
		        'payment_account_link_num'	=> $payment_account->id,
		        'bankTypeId'		=> $payment_account->bankTypeId,
		        'bank_name_local'	=> lang($payment_account->payment_type),
		        'minDeposit'		=> $this->depcat_fix_deposit_max($payment_account->vip_rule_min_deposit_trans),
		        'maxDeposit'		=> $this->depcat_fix_deposit_min($payment_account->vip_rule_max_deposit_trans),
		        // 'minDeposit_currency'		=> $this->ci->utils->formatCurrency($payment_account->vip_rule_min_deposit_trans),
		        // 'maxDeposit_currency'		=> $this->ci->utils->formatCurrency($payment_account->vip_rule_max_deposit_trans),
		        // 'flag' => $payment_account->flag,
		        'second_category_flag'	=> $payment_account->second_category_flag,
		        'order'				=> $payment_account->payment_order
		    ];

		    if ($show_deposit_bank_details) {
		        $payment_account_data['bankAccountName'] = $payment_account->payment_account_name;
		        $payment_account_data['bankAccountNo'] = $payment_account->payment_account_number;
		        $payment_account_data['bankCity'] = NULL;
		        $payment_account_data['branchName'] = $payment_account->payment_branch_name;
		    }

		    ksort($payment_account_data);

		    if ($payment_account->flag == AUTO_ONLINE_PAYMENT) {
		        $payment_auto_account_list['cid-' . $payment_account_data['payment_account_link_num']] = $payment_account_data;
		    }

		}

		$pay_cat_list_manu = $this->depcat_group_accounts_by_2nd_cat($payment_manual_account_list);
		$pay_cat_list_auto = $this->depcat_group_accounts_by_2nd_cat($payment_auto_account_list);

		return [
			'manual'	=> $pay_cat_list_manu ,
			'auto'		=> $pay_cat_list_auto
		];

    } // End function depcat_deposit_paycats()

    /**
     * Returns number of attachment uploaded for given (player, sale_order)
     * OGP-16735
     * @param	int		$player_id		== player.playerId
     * @param	string	$secure_id 		Secure ID of deposit order
     * @return	int
     */
    public function get_attach_count_by_sale_order_id($player_id, $sale_order_id) {
    	$this->ci->load->model([ 'player_attached_proof_file_model' ]);

    	$images_uploaded = $this->ci->player_attached_proof_file_model->getAttachementRecordInfo($player_id, null, null, null, null, $sale_order_id, true, false);

    	if (!is_array($images_uploaded)) {
    		return 0;
    	}

    	return count($images_uploaded);
    }

    /**
     * Group routine for Comapi_lib::depcat_deposit_paycats()
     * OGP-13250
     * @param	array 	$pay_acc_list	array of payment accounts
     * @see		Comapi_lib::depcat_deposit_paycats()
     * @return	array 	assoc array
     */
    protected function depcat_group_accounts_by_2nd_cat($pay_acc_list) {
    	$list2 = [];
    	foreach ($pay_acc_list as $payacc) {
    		$cat2_flag = $payacc['second_category_flag'];
    		// OGP-14655: Provide pay_acc_id as secondary ident for accounts
    		$payacc['pay_acc_id'] = $payacc['payment_account_link_num'];
    		// Conceal details
    		unset($payacc['second_category_flag']);
    		unset($payacc['payment_account_link_num']);

    		if (!isset($list2[$cat2_flag])) {
    			$list2[$cat2_flag] = [
    				'category_id'	=> $cat2_flag ,
    				'category_text'	=> $this->ci->utils->second_category_flag_to_text($cat2_flag) ,
    				'list'			=> [ $payacc ]
    			];
    		}
    		else {
    			$list2[$cat2_flag]['list'][] = $payacc;
    		}
    	}

    	$list2_flat = array_values($list2);

    	return $list2_flat;
    } // End function depcat_group_accounts_by_2nd_cat()

    /**
     * Returns plain flat array of payment accounts of given type (manual/auto)
     * @param	int		$player_id		== player.playerId
     * @param	int		$type			self::DEPCAT_MANUAL or self::DEPCAT_AUTO
     * @return	array 	array of objects (payment accounts)
     */
    protected function depcat_pay_account_list_flat($player_id, $type = self::DEPCAT_MANUAL, $mobile = false) {
    	$this->ci->load->model('payment_account');
    	$payment_all_accounts = $this->ci->payment_account->getAvailableDefaultCollectionAccount($player_id, null, null, null, $mobile);

    	if ($type == self::DEPCAT_MANUAL) {
	    	$pay_accs = $payment_manual_accounts = ($payment_all_accounts[MANUAL_ONLINE_PAYMENT]['enabled']) ? $payment_all_accounts[MANUAL_ONLINE_PAYMENT]['list'] : [];
	    	if($payment_all_accounts[LOCAL_BANK_OFFLINE]['enabled']){
	            foreach($payment_all_accounts[LOCAL_BANK_OFFLINE]['list'] as $payment_account){
	                $payment_manual_accounts[] = $payment_account;
	            }
	        }
    	}
    	else {
    		$pay_accs = $payment_all_accounts[AUTO_ONLINE_PAYMENT]['enabled'] ? $payment_all_accounts[AUTO_ONLINE_PAYMENT]['list'] : [];
    	}

    	return $pay_accs;
    } // End function depcat_pay_account_list_flat()

    /**
     * Return a payment account for given bankTypeId tuple (player_id, bankTypeId, bank_type)
     * OGP-13251
     * @param	int		$player_id		== player.playerId
     * @param  	int		$bankTypeId		bankTypeId
     * @param	int		$bank_type		Either Comapi_lib::DEPCAT_MANUAL or Comapi_lib::DEPCAT_AUTO
     * @return	bool
     */
    public function depcat_pay_account_by_type_bankTypeId($player_id, $bankTypeId, $bank_type = self::DEPCAT_MANUAL, $mobile = false) {
    	$pay_accs = $this->depcat_pay_account_list_flat($player_id, $bank_type, $mobile);

    	// If no payment accounts available, directly return null
    	if (empty($pay_accs)) { return null; }

    	// $this->ci->utils->debug_log(__METHOD__, 'pay_accs', $pay_accs);

    	$res = null;
    	foreach ($pay_accs as $pacc) {
    		// $this->ci->utils->debug_log(__METHOD__, 'bankTypeId', $bankTypeId, 'pacc->id', $pacc->bankTypeId);
    		if ($bankTypeId == $pacc->bankTypeId) {
    			$res = $pacc;
    			break;
    		}
    	}

    	return $res;
    } // end function depcat_pay_account_by_type_bankTypeId()

    /**
     * Returns manual deposit account info for given bankTypeId tuple (player_id, bankTypeId)
     * OGP-13251, Ported from ajax/Deposit::getPaymentAccountDetail()
     *
     * @param	int		$bankTypeId		== payment_account.bankTypeId
     * @param	int		$player_id		== player.playerId
     * @param	bool	$gen_secure_id	Generates a secure ID for return if true.  Defaults to true.
     * @see		ajax/Deposit::getPaymentAccountDetail()
     * @return	array
     */
    public function depcat_manu_account_info($bankTypeId, $player_id, $gen_secure_id = true, $pay_acc_id = null) {
    	try {
    		$this->ci->load->model([ 'payment_account', 'sale_order', 'banktype' ]);

    		if (empty($pay_acc_id)) {
    			$this->ci->utils->debug_log(__METHOD__, 'pay_acc_id empty, probing default pay_acc_id by bankTypeId');
		    	$pay_account = $this->depcat_pay_account_by_type_bankTypeId($player_id, $bankTypeId, self::DEPCAT_MANUAL);
		    	if (empty($pay_account)) {
		    		throw new Exception(lang('Payment account not available') . " (na1)($bankTypeId, $pay_acc_id)", Api_common::CODE_MDN_PAYMENT_ACCOUNT_NOT_ACCESSIBLE);
		    	}

		    	$pay_acc_id = $pay_account->id;
		    }

	    	$pay_acc = $this->ci->payment_account->getPaymentAccountWithVIPRule($pay_acc_id, $player_id);
	    	if (empty($pay_acc)) {
	    		throw new Exception(lang('Payment account not available') . " (na2)($bankTypeId, $pay_acc_id)", Api_common::CODE_MDN_PAYMENT_ACCOUNT_NOT_ACCESSIBLE);
	    	}

	    	$banktype = $this->ci->banktype->getBankTypeById($pay_acc->payment_type_id);
	    	// $this->ci->utils->debug_log(__METHOD__, 'banktype', $banktype);
	    	$is_alipay		= $this->ci->utils->isAlipay($banktype);
	    	$is_unionpay	= $this->ci->utils->isUnionpay($banktype);
	    	$is_wechat		= $this->ci->utils->isWechat($banktype);

	    	$pay_service = '';
	    	if ($is_wechat)			{ $pay_service = 'wechat'; }
	    	else if ($is_unionpay)	{ $pay_service = 'unionpay'; }
	    	// Much embarassing bug.  OGP-13918
	    	// else if ($is_wechat)	{ $pay_service = 'wechat'; }
	    	else if ($is_alipay)	{ $pay_service = 'alipay'; }

	    	// Generate secure ID
	    	$secure_id = $gen_secure_id ? $this->ci->sale_order->generateSecureId() : false;

	    	$pay_acc_return = [
	            'bankTypeId'		=> $pay_acc->bankTypeId,
	            'title'				=> sprintf('%s - %s', lang($pay_acc->payment_type), $pay_acc->payment_account_name) ,
	            'account_icon'		=> $pay_acc->account_icon_url ,
            	'account_image'		=> $pay_acc->account_image_url ,
            	'account_number'	=> $pay_acc->payment_account_number ,
            	'account_name'		=> $pay_acc->payment_account_name ,
            	'bank'				=> lang($pay_acc->payment_type) ,
            	'branch'			=> $pay_acc->payment_branch_name ,
            	'payment_account_id'	=> $pay_acc->payment_account_id,
            	'secure_id'			=> $secure_id ,
            	// 'is_alipay'			=> $is_alipay ,
            	// 'is_unionpay'		=> $is_unionpay ,
            	// 'is_wechat'			=> $is_wechat ,
            	'minDeposit'		=> $this->depcat_fix_deposit_max($pay_acc->vip_rule_min_deposit_trans) ,
            	'maxDeposit'		=> $this->depcat_fix_deposit_min($pay_acc->vip_rule_max_deposit_trans) ,
            	'pay_service'		=> $pay_service ,
	            // 'flag'	=> $pay_acc->flag,
	            // 'flag2'	=> $pay_acc->second_category_flag ,
	    	];

	    	$ret = [ 'code' => 0, 'mesg' => null, 'result' => $pay_acc_return ];
	    }
	    catch (Exception $ex) {
	    	$this->ci->utils->debug_log(__METHOD__, 'Exception', $ex->getCode(), $ex->getMessage());
	    	$ret = [ 'code' => $ex->getCode(), 'mesg' => $ex->getMessage(), 'result' => null ];
	    }
	    finally {
	    	return $ret;
    	}
    } // End function depcat_manu_account_info()

	/**
	 * Return only promos in categories enabled for promo manager
	 *
	 * @param	int		$player_id	== player.playerId
	 * @param	int		$offset		Offset, for paging
	 * @param	int		$limit		Limit (page length), for paging
	 * @param	int		$fields		Field mode
	 *
	 * @return	array
	 */
	public function get_promos_enabled_for_promo_manager($player_id, $offset = 0, $limit = 15, $fields = self::PROMO_SIMPLE, $is_deposit = false) {
        $this->ci->load->model([ 'promorules', 'player_model' ]);

        $minimal_field_set = [ 'promo_cms_id' , 'promoName' , 'promoDescription' , 'promoDetails' , 'status' , 'promo_code' , 'tag_as_new_flag', 'promo_category', 'promo_category_name', 'promo_check_player_allowed', 'promo_check_mesg', 'promo_image' ];

        // Check if player is disabled of promo
        $player = $this->ci->player_model->getPlayerById($player_id);

        $promos=[];
        if ($player->disabled_promotion == 1) {
            return $promos;
        }

        // Read promos for player
        $promos = $this->ci->utils->getPlayerPromo("allpromo", $player_id);

		if (!isset($promos['promo_list']) || empty($promos['promo_list'])) {
			return $promos;
		}
		$promos = $promos['promo_list'];

        // Remove promos whose lang != current language
        $current_lang = $this->ci->language_function->getCurrentLanguage();
		foreach ($promos as $pkey => & $pitem) {
			// Skip non-deposit promos if is_deposit == true
			if ($is_deposit && $pitem['promorule']['promoType'] != Promorules::PROMO_TYPE_DEPOSIT) {
			// if ($is_deposit && $pitem['promorule']['promoType'] != 0) {
				unset($promos[$pkey]);
				continue;
			}

			if (!isset($pitem['promorule']) || empty($pitem['promorule']) || !isset($pitem['promorule']['language']) || empty($pitem['promorule']['language'])) {
				continue;
			}

			if ($pitem['promorule']['language'] != $current_lang) {
				unset($promos[$pkey]);
				continue;
			}
		}

		// Get promo types that are applicable for players
        $promo_cats = $this->ci->utils->getAllPromoType();
        $promo_cats_kv = [];
        foreach ($promo_cats as $pcat) {
        	$promo_cats_kv[$pcat['id']] = $pcat['name'];
        }

        foreach ($promos as $pkey => & $pitem) {
        	// Exclude promos whose category not available to
        	$promo_cat = $pitem['promorule']['promoCategory'];
        	if (!isset($promo_cats_kv[$promo_cat])) {
        		unset($promos[$pkey]);
        		continue;
        	}
        	$pitem['promo_category'] = $promo_cat;
        	$pitem['promo_category_name'] = $promo_cats_kv[$promo_cat];
        	$pitem['promo_cms_id'] = $pitem['promoCmsSettingId'];
        	unset($pitem['promoCmsSettingId']);

        	// Promo image
        	$pitem['promo_image'] = $this->ci->utils->getPromoThumbnailsUrl($pitem['promoThumbnail'], false);
        	unset($pitem['promoThumbnail'], $pitem['is_default_banner_flag']);

        	// Check promo
        	if (!$is_deposit) {
	        	$extra = [];
	        	$promo_check_stat = $this->ci->promorules->checkOnlyPromotion($player_id, $pitem['promorule'], $pitem['promo_cms_id'], false, null, $extra);
	        	// $pitem['promo_check_stat'] = $promo_check_stat;
	        	$pitem['promo_check_player_allowed'] = $promo_check_stat[0];
	        	$pitem['promo_check_mesg'] = $promo_check_stat[0] ? null : lang($promo_check_stat[1]);
	        	// $pitem['promo_check_extra'] = $extra;
	        }
	        else {
	        	$pitem['promo_check_player_allowed'] = true;
	        	$pitem['promo_check_mesg'] = null;
	        }

	        // OGP-16575: add html_entity_decode() on promoDetails
	        $pitem['promoDetails'] = html_entity_decode($pitem['promoDetails']);

        	// Tailor fields for each promo
            switch ($fields) {
        		case self::PROMO_SIMPLE :
        			$pitem = $this->ci->utils->array_select_fields($pitem, $minimal_field_set);
        			break;

        		case self::PROMO_ALL :
            		break;
            }

            ksort($pitem);
        }
        $promos = array_merge($promos, []);
        $promos = array_slice($promos, $offset, $limit);

        $promo_ret = [ 'promos' => $promos ];
        if (!$is_deposit) {
        	$promo_ret = [ 'promos' => $promos, 'categories' => $promo_cats ];
        }

        return $promo_ret;

    } // End function get_promos_enabled_for_promo_manager()

	/**
	 * Return all promos, without any pre-checks
	 *
	 * @param	int		$offset		Offset, for paging
	 * @param	int		$limit		Limit (page length), for paging
	 * @param	int		$fields		Field mode
	 *
	 * @return	array
	 */
	public function get_promos_bare($offset = 0, $limit = 15, $fields = self::PROMO_SIMPLE, $is_deposit = false) {
        $this->ci->load->model([ 'promorules', 'player_model', 'cms_model' ]);

        $minimal_field_set = [ 'promo_cms_id' , 'promoName' , 'promoDescription' , 'promoDetails' , 'status' , 'promo_code' , 'tag_as_new_flag', 'promo_category', 'promo_category_name', 'promo_check_player_allowed', 'promo_check_mesg', 'promo_image' ];

        // Fetch all promos
        $promos = $this->ci->promorules->getAllPromo(null, null, null);

		// Get promo types that are applicable for players
        $promo_cats = $this->ci->utils->getAllPromoType();
        $promo_cats_kv = [];
        foreach ($promo_cats as $pcat) {
        	$promo_cats_kv[$pcat['id']] = $pcat['name'];
        }

        foreach ($promos as $pkey => & $pitem) {
        	// Exclude promos whose category not available to
    		$promo_rule = $this->ci->promorules->getPromorule($pitem['promoId']);
        	$promo_cat = $promo_rule['promoCategory'];
        	if (!isset($promo_cats_kv[$promo_cat])) {
        		unset($promos[$pkey]);
        		continue;
        	}

        	$pitem['promo_category'] = $promo_cat;
        	$pitem['promo_category_name'] = $promo_cats_kv[$promo_cat];
        	$pitem['promo_cms_id'] = $pitem['promoCmsSettingId'];
        	unset($pitem['promoCmsSettingId']);

        	// OGP-15994: Use alt details in promocmssettings.promo_multi_lang
        	if ($this->ci->utils->isEnabledFeature("enable_multi_lang_promo_manager")){
            	$promo_multi_lang = $this->ci->utils->promoItemMultiLangFields($pitem);

            	$pitem = array_merge($pitem, $promo_multi_lang);
            }

            // OGP-16575: add html_entity_decode() on promoDetails
	        $pitem['promoDetails'] = html_entity_decode($this->ci->cms_model->decodePromoDetailItem($pitem['promoDetails']));

        	// Promo image
        	// Suppress mobile template checking - OGP-14446
        	$pitem['promo_image'] = $this->ci->utils->getPromoThumbnailsUrl($pitem['promoThumbnail'], false);
        	unset($pitem['promoThumbnail'], $pitem['is_default_banner_flag']);

         	// Tailor fields for each promo
            switch ($fields) {
        		case self::PROMO_SIMPLE :
        			$pitem = $this->ci->utils->array_select_fields($pitem, $minimal_field_set);
        			break;

        		case self::PROMO_ALL :
            		break;
            }

            ksort($pitem);
        }
        $promos = array_merge($promos, []);
        $promos = array_slice($promos, $offset, $limit);

        $promo_ret = [ 'promos' => $promos ];
        if (!$is_deposit) {
        	$promo_ret = [ 'promos' => $promos, 'categories' => $promo_cats ];
        }

        return $promo_ret;

    } // End function get_promos_bare()

    /**
     * Ported from ajax/Deposit::_process_promo_rules()
     * OGP-13074
     *
     * @param	int		$playerId			== player.playerId
     * @param	int		$promo_cms_id		== promocmssettings.promocmssettingsId
     * @param	float	$transferAmount		The amount
     * @param	ref		&$error				Error container
     * @param	int		$subWalletId		Subwallet ID
     * @see		controllers/ajax/deposit.php
     *
     * @return	tuple	[ promo_cms_id, promo_rules_id, promorule ]
     */
	public function process_promo_rules($playerId, $promo_cms_id, $transferAmount, &$error = null, $subWalletId = null) {
		$this->ci->load->model(['promorules']);

		if (!empty($promo_cms_id)){

			list($promorule, $promoCmsSettingId) = $this->ci->promorules->getByCmsPromoCodeOrId($promo_cms_id);

			//simple check
			//check sub wallet only
			if ($this->ci->promorules->isTransferPromo($promorule)){

				//check
				if ($promorule['depositConditionNonFixedDepositAmount'] == Promorules::NON_FIXED_DEPOSIT_MIN_MAX) {
					if ($transferAmount >= $promorule['nonfixedDepositMinAmount'] && $transferAmount <= $promorule['nonfixedDepositMaxAmount']) {
					} else {
						$error = lang('notify.37');
						return [ null, null, null ];
					}
				}

				$trigger_wallets = $promorule['trigger_wallets'];
				$trigger_wallets_arr = [];
				if (!empty($trigger_wallets)) {
					$trigger_wallets_arr=explode(',',$trigger_wallets);
				}
				if (!in_array($subWalletId, $trigger_wallets_arr)) {
					$this->ci->utils->error_log('subWalletId should be ', $trigger_wallets_arr ,'current',$subWalletId);
					// $message = 'Only trigger on transfer right sub-wallet';
					$error = lang('Must choose correct sub-wallet');
					return [null, null, null];
				}

			}
			else if ($this->ci->promorules->isDepositPromo($promorule)) {
				//check
				if ($promorule['depositConditionNonFixedDepositAmount'] == Promorules::NON_FIXED_DEPOSIT_MIN_MAX) {
					if ($transferAmount >= $promorule['nonfixedDepositMinAmount'] && $transferAmount <= $promorule['nonfixedDepositMaxAmount']) {
					} else {
						$error = lang('notify.37');
						return [null, null, null];
					}
				}

			}

			return [ $promoCmsSettingId, $promorule['promorulesId'], $promorule ];
		}

		return [null, null, null];

	} // End function process_promo_rules()

    public function reset_password($player_id, $password, $des_key) {
    	$this->ci->load->library('salt');
    	$this->ci->load->model('player_model');

    	$updateset = [
    		'password' => $this->ci->salt->encrypt($password, $des_key) ,
    		'resetCode' => null ,
    		'resetExpire' => null
    	];

    	$this->ci->player_model->updatePlayer($player_id, $updateset);

    	return true;
    }

    /**
     * Checks if given bankDetailsId is player's valid withdraw account
     * @param	int		$player_id		== player.playerId
     * @param	int		$bankDetailsId	== playerbankdetails.playerbankdetailsId
     * @return boolean                [description]
     */
    public function is_valid_withdraw_account_for_player($player_id, $bankDetailsId) {
    	/**
    	 * Notes for playerbankdetails:
    	 * playerBankDetailsId		== $bankDetailsId here
    	 * playerId					== $player_id here
    	 * dwBank					'0' for deposit, '1' for withdraw
    	 * status					'0' (Playerbankdetails::STATUS_ACTIVE)  normal
    	 * 							'2' (Playerbankdetails::STATUS_DELETED) deleted
    	 * isDeleted				-- NOT USED, IT'S SPOOF --
    	 */
    	$this->ci->load->model([ 'playerbankdetails' ]);
    	$wd_accounts = $this->ci->playerbankdetails->getPlayerWithdrawalBankList($player_id);

    	foreach ($wd_accounts as $wda) {
    		if ($wda['playerBankDetailsId'] == $bankDetailsId && $wda['status'] == Playerbankdetails::STATUS_ACTIVE) {
    			$this->ci->utils->debug_log(__METHOD__, 'match found', [ 'player_id' => $player_id, 'bankDetailsId' => $bankDetailsId, 'acc-fullname' => $wda['bankAccountFullName'], 'acc-number' => $wda['bankAccountNumber'] ]);
    			return true;
    		}
    	}

    	$this->ci->utils->debug_log(__METHOD__, 'match not found', [ 'player_id' => $player_id, 'bankDetailsId' => $bankDetailsId ]);

    	return false;
    }


    /**
     * Return withdraw accounts for a given player
     * @param	int		$player_id		== player.playerId
     * @param	bool	$return_id_only	when true, returns array of playerBankDetailsId
     * @param	boolean $probe_only     when true, return the result instead of raise an execption
     * @return array when accounts are available; null when no account available
     *                    Note the null return for php 7.2 and above
     */
    public function player_withdraw_accounts($player_id, $return_id_only = false, $probe_only = false) {
    	$this->ci->load->model([ 'playerbankdetails' ]);

    	// Read player withdrawal banks
		$wd_accounts = $this->ci->playerbankdetails->getPlayerWithdrawalBankList($player_id);

		if (!$probe_only && empty($wd_accounts)) {
			throw new Exception('Player has no withdraw account configured so far.', Api_common::CODE_LPWA_NO_WITHDRAW_ACCOUNT);
		}

		// Select fields to present in result
		$reveal_fields_raw = [ 'playerBankDetailsId' , 'playerId' , 'bankTypeId' , 'bankAccountFullName' , 'bankAccountNumber' , 'city' , 'province' , 'branch' , 'isDefault' , 'isRemember' , 'dwBank' , 'updatedOn' , 'bankName' ];
		$reveal_fields = array_flip($reveal_fields_raw);

		if (empty($return_id_only)) {
			if (!empty($wd_accounts)) {
				$wd_sort_keys = [];
				foreach ($wd_accounts as & $wd_acc) {
					$wd_acc = array_intersect_key($wd_acc, $reveal_fields);
					$wd_sort_keys[]= $wd_acc['playerBankDetailsId'];
				}

				array_multisort($wd_accounts, $wd_sort_keys);
			}
			return $wd_accounts;
		}

		$wd_ids = [];
		if (!empty($wd_accounts)) {
			foreach ($wd_accounts as $wd_acc) {
				$wd_ids[] = $wd_acc['playerBankDetailsId'];
			}
		}

		return $wd_ids;

    }

    /**
     * Return withdraw/deposit accounts for a given player
     * @param	int		$player_id		== player.playerId
     * @param	bool	$return_id_only	when true, returns array of playerBankDetailsId
     * @param	boolean $probe_only     when true, return the result instead of raise an execption
     * @return array when accounts are available; null when no account available
     *                    Note the null return for php 7.2 and above
     */
    public function player_bank_accounts($player_id, $mode = 'deposit',$return_id_only = false, $probe_only = false) {
    	$this->ci->load->model([ 'playerbankdetails' ]);

    	// Read player withdrawal banks
    	if ($mode != 'deposit') {
			$player_accs = $this->ci->playerbankdetails->getPlayerWithdrawalBankList($player_id);
		}
		else {
			$player_accs = $this->ci->playerbankdetails->getDepositBankDetails($player_id);
		}

		if (!$probe_only && empty($player_accs)) {
			throw new Exception('Player has no withdraw account configured so far.', Api_common::CODE_LPWA_NO_WITHDRAW_ACCOUNT);
		}

		// Select fields to present in result
		$reveal_fields_raw = [ 'playerBankDetailsId' , 'playerId' , 'bankTypeId' , 'bankAccountFullName' , 'bankAccountNumber' , 'city' , 'province' , 'branch' , 'isDefault' , 'isRemember' , 'dwBank' , 'updatedOn' , 'bankName' ];
		$reveal_fields = array_flip($reveal_fields_raw);

		if (empty($return_id_only)) {
			if (!empty($player_accs)) {
				$wd_sort_keys = [];
				foreach ($player_accs as & $acc) {
					$acc = array_intersect_key($acc, $reveal_fields);
					$wd_sort_keys[]= $acc['playerBankDetailsId'];
				}

				array_multisort($player_accs, $wd_sort_keys);
			}
			return $player_accs;
		}

		$acc_ids = [];
		if (!empty($player_accs)) {
			foreach ($player_accs as $acc) {
				$acc_ids[] = $acc['playerBankDetailsId'];
			}
		}

		return $acc_ids;

    }

    /**
     * Check if given bank account is player's default (for deposit/withdrawal)
     * @param	int		$player_id		== player.playerId
     * @param	int		$bankDetailsId	== playerbankdetails.playerbankdetailsId
     * @param	string	$mode         	either of [ 'deposit', 'withdraw' ].  Defaults to deposit.
     * @return	boolean		true if the bank account is default; otherwise false.
     */
    public function is_player_default_account($player_id, $bankDetailsId, $mode = 'deposit') {
    	$player_accs = $this->player_bank_accounts($player_id, $mode);
    	$flag_default = false;
    	$flag_error = true;
    	foreach ($player_accs as $acc) {
    		if ($acc['playerBankDetailsId'] == $bankDetailsId) {
    			$flag_error = false;
    			$flag_default = $acc['isDefault'];
    			break;
    		}
    	}

    	$this->ci->utils->debug_log(__METHOD__, [ 'player_id' => $player_id, 'bankDetailsId' => $bankDetailsId, 'flag_error' => $flag_error, 'flag_default' => $flag_default ]);

    	return (!$flag_error && $flag_default);
    }

    /**
     * Generate random alphanumeric sequence
     * @param	int		$len	length
     * @param	int		$type	Use any of Comapi_lib::RANDAL_* constants
     * @return	string
     */
    public function rand_alnum($len, $type = self::RANDAL_HEX) {
    	$charsets = [
			self::RANDAL_FULL			=> '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
			self::RANDAL_FULL_LOWER		=> '0123456789abcdefghijklmnopqrstuvwxyz',
			self::RANDAL_HEX			=> '0123456789abcdef',
			self::RANDAL_NONHEX			=> 'ghijklmnopqrstuvwxyzGHIJKLMNOPQRSTUVWXYZ',
			self::RANDAL_NONHEX_LOWER	=> 'ghijklmnopqrstuvwxyz',
    	];
    	$charset = isset($charsets[$type]) ? $charsets[$type] : $charsets[self::RANDAL_FULL_LOWER];
    	$s = '';
    	for ($i = 0; $i < $len; ++$i) {
    		$s .= $charset[ mt_rand(0, strlen($charset) - 1) ];
    	}
    	return $s;
    }

    /**
     * Returns TUID, an imitation of UUID
     * @param	string	$prefix		Prefix string
     * @return	string
     */
    public function tuid($prefix = null) {
    	$parts = [
    		[ 'type' => self::RANDAL_NONHEX	, 'len' => 4	, 'rept' => 1 ] ,
    		[ 'type' => self::RANDAL_FULL	, 'len' => 4	, 'rept' => 1 ] ,
    		[ 'type' => self::RANDAL_FULL	, 'len' => 8	, 'rept' => 1 ]
    	];

    	$chunks = [];
    	foreach ($parts as $p) {
    		for ($i = 0; $i < $p['rept']; ++$i) {
	    		$chunks[] = $this->rand_alnum($p['len'], $p['type']);
	    	}
    	}
    	$chunks_str = $prefix . implode('-', $chunks);
    	return $chunks_str;
    }

    /**
     * Test if string loosely matches tuid format
     * @param	string	$s	String to test
     * @return	bool
     */
    public function tuid_match($s) {
    	$tuid_regex = '/^([A-Za-z0-9]{4}-){2}[A-Za-z0-9]{8}$/';
    	return preg_match($tuid_regex, $s);
    }

    /**
     * Ported from Ajax::postManualDeposit()
     * OGP-13326
     * @param	array 	$deposit_req	Deposit request as array
     * @return	arrau
     */
    public function comapi_manual_deposit($deposit_req) {
        $this->ci->utils->debug_log(__METHOD__, 'deposit_req', $deposit_req);
        $depositAmount			= $this->ci->utils->safeGetArray($deposit_req, 'amount');
        $payment_account_id		= $this->ci->utils->safeGetArray($deposit_req, 'payment_account_id');
		$playerBankDetailsId 	= $this->ci->utils->safeGetArray($deposit_req, 'playerBankDetailsId');
        $deposit_notes			= $this->ci->utils->safeGetArray($deposit_req, 'deposit_notes');
		$playerId 				= $deposit_req['player_id'];
		$defaultCurrency		= $this->ci->utils->getCurrentCurrency()['currency_code'];
		$secure_id 				= $this->ci->utils->safeGetArray($deposit_req, 'secure_id');
		$deposit_time 			= $this->ci->utils->safeGetArray($deposit_req, 'deposit_time');
		$deposit_time_out 		= $this->ci->utils->safeGetArray($deposit_req, "deposit_time_out");
        $depositor_name			= $this->ci->utils->isEnabledFeature('enable_manual_deposit_input_depositor_name') ? $this->ci->utils->safeGetArray($deposit_req, "firstName") : null;
        $dwIp					= $this->ci->input->ip_address();
        $geolocation			= $this->ci->utils->getGeoplugin($dwIp);
        $player_promo_id		= null;
        $sub_wallet_id			= $this->ci->utils->safeGetArray($deposit_req, "wallet_id");
        $group_level_id			= null;
        $depositDatetime		= $this->ci->utils->safeGetArray($deposit_req, "deposit_datetime");
        $mode_of_deposit		= $this->ci->utils->safeGetArray($deposit_req, "mode_of_deposit");
        $depositReferenceNo		= null;
        $pendingDepositWalletType = null;

		$promo_cms_id			= $this->ci->utils->safeGetArray($deposit_req, 'promo_cms_id');
		$is_mobile				= $this->ci->utils->safeGetArray($deposit_req, 'is_mobile');

		try{
	        if($this->ci->utils->isEnabledFeature('enable_manually_deposit_cool_down_time')){
	            $manually_deposit_cool_down_minutes=$this->ci->utils->getConfig('manually_deposit_cool_down_minutes');
	            //check cold down time
	            $lastOrder=$this->ci->sale_order->getLastUnfinishedManuallyDeposit($playerId);
	            if($lastOrder && !$this->ci->utils->isTimeoutNow($lastOrder['created_at'], $manually_deposit_cool_down_minutes) ){
	                //not reach cool down time
	                // $message = sprintf(lang('hint.manually.deposit.cool.down'), $this->ci->utils->getConfig('manually_deposit_cool_down_minutes'));
	                // return $this->returnJsonResult(array('status' => 'error', 'mesg' => $message));
	                throw new Exception( sprintf( lang('Cannot deposit in cool down period (%d) minutes'), $this->ci->utils->getConfig('manually_deposit_cool_down_minutes') ), Api_common::CODE_MDN_IN_COOL_DOWN_PERIOD );
	            }
	        }

	        $this->ci->load->model(array('sale_order', 'banktype'));
	        if($this->ci->utils->isEnabledFeature('responsible_gaming')){
	            $this->ci->load->library(array('player_responsible_gaming_library'));
	            //deposit limit hint
	            if($this->ci->player_responsible_gaming_library->inDepositLimits($playerId, $depositAmount)){
	                // return [
	                //     'status' => 'error',
	                //     'message' => lang('Deposit Limits Effect, cannot make deposit')
	                // ];
	                throw new Exception(lang('Cannot deposit because deposit limits is active'), Api_common::CODE_MDN_DEPOSIT_LIMITS_IN_EFFECT);
	            }
	        }

	        // if($this->ci->utils->isEnabledFeature('enable_deposit_upload_documents')){
	        //     $file1 = isset($_FILES['file1']) ? $_FILES['file1'] : null;
	        //     $file2 = isset($_FILES['file2']) ? $_FILES['file2'] : null;
	        //     $this->ci->utils->debug_log('=========== upload attached document exist ? file1 [ '.json_encode($file1).' ] , file2 [ '.json_encode($file2).' ] ');
	        // }

	        if($this->ci->utils->isEnabledFeature('only_allow_one_pending_deposit')){

	            $exists=$this->ci->sale_order->existsUnfinishedManuallyDeposit($playerId);
	            if($exists){
	                // $message = lang('Sorry, your last deposit request is not done, so you can not start new request');
	                // return $this->returnJsonResult(array('status' => 'error', 'mesg' => $message));
	                throw new Exception(lang('Cannot submit new deposits because last deposit not complete'), Api_common::CODE_MDN_LAST_DEPOSIT_NOT_COMPLETE);
	            }
	        }

			$error = null;
			$player_promo_id = null;
	        $depositAccountNo = null;
			// list($promo_cms_id, $promo_rules_id, $promorule) = $this->ci->_process_promo_rules($playerId, $promo_cms_id, $depositAmount, $error, $sub_wallet_id);
			list($promo_cms_id, $promo_rules_id, $promorule) = $this->process_promo_rules($playerId, $promo_cms_id, $depositAmount, $error, $sub_wallet_id);
	        $this->ci->utils->debug_log(__METHOD__, 'process_promo_rules result', ['promo_cms_id' => $promo_cms_id, 'promo_rules_id' => $promo_rules_id, 'error' => $error, 'sub_wallet_id' => $sub_wallet_id ]);
			$promo_info = [
                'promo_rules_id'    => $promo_rules_id ,
                'promo_cms_id'      => $promo_cms_id
            ];

			$payment_account = $this->ci->payment_account->getPaymentAccountWithVIPRule($payment_account_id, $playerId);
			if (empty($payment_account)) {
	            // return $this->returnJsonResult(array('status' => 'error', 'mesg' => sprintf(lang('gen.error.forbidden'), lang('pay.depmethod'))));
	            throw new Exception(lang('Payment not accessible'), Api_common::CODE_MDN_PAYMENT_ACCOUNT_NOT_ACCESSIBLE);
			}

	        // $payment_account_data = $this->_displayPaymentAccountData($payment_account, TRUE);

            #cryptocurrency deposit
            $banktype = $this->ci->banktype->getBankTypeById($payment_account->bankTypeId);
            if($this->ci->utils->isCryptoCurrency($banktype)){
                $cryptocurrency = $this->ci->utils->getCryptoCurrency($banktype);

                list($crypto, $rate) = $this->ci->utils->convertCryptoCurrency($depositAmount, $defaultCurrency, $cryptocurrency);

                $custom_deposit_rate = $this->config->item('custom_deposit_rate') ? $this->config->item('custom_deposit_rate') : 1;
                $crypto      = number_format($crypto * $custom_deposit_rate, 8, '.', '');
                $player_rate = number_format($rate   * $custom_deposit_rate, 8, '.', '');

                $depositAccountNo = $deposit_notes;
                $deposit_notes = 'Wallet Address: '.$deposit_notes;
                $deposit_notes .= ' | '.$cryptocurrency.': '.$crypto.' | Crypto Real Rate: '.$rate.' | Custom Deposit Rate: '.$custom_deposit_rate;
                $this->ci->utils->debug_log(__METHOD__, 'Cryptocurrency deposit_notes', $deposit_notes);
            }

            $minDeposit = $this->depcat_fix_deposit_max($payment_account->vip_rule_min_deposit_trans);
            $maxDeposit = $this->depcat_fix_deposit_min($payment_account->vip_rule_max_deposit_trans);
            $maxDepositDaily = 0;

            $deposit_order_args = [
                Sale_order::PAYMENT_KIND_DEPOSIT,
                $payment_account_id,
                $playerId,
                $depositAmount,
                $defaultCurrency,
                $player_promo_id,
                $dwIp,
                $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'],
                $playerBankDetailsId,
                null,
                null,
                $deposit_notes,
                Sale_order::STATUS_PROCESSING,
                $sub_wallet_id,
                $group_level_id,
                $depositDatetime,
                $depositReferenceNo,
                $pendingDepositWalletType,
                null,
                $is_mobile,
                $this->ci->utils->getNowForMysql(),
                $promo_info,
                $depositor_name,
                $depositAccountNo,
                Sale_order::PLAYER_DEPOSIT_METHOD_UNSPECIFIED,
                $secure_id,
                $deposit_time,
                $deposit_time_out,
                $mode_of_deposit
            ];


            if ($depositAmount <= 0) {
                throw new Exception(lang('Deposit amount must be greater than zero'), Api_common::CODE_MDN_AMOUNT_NOT_GREATER_THAN_ZERO);
            } else if ($depositAmount < $minDeposit) {
                throw new Exception(lang('Deposit amount less than minimum limit'), Api_common::CODE_MDN_AMOUNT_LESS_THAN_MINIMUM);
            } else if ($maxDeposit > 0) {
                if ($depositAmount > $maxDeposit) {
                    throw new Exception(lang('Deposit amount beyond maximum limit'), Api_common::CODE_MDN_AMOUNT_EXCEEDS_MAXIMUM);
                } elseif ($maxDepositDaily > 0) {
                    $playerTotalDailyDeposit = $this->ci->transactions->sumDepositAmountToday($playerId);
                    if (($playerTotalDailyDeposit + $depositAmount) >= $maxDepositDaily) {
                        $message = lang('notify.46');
                        throw new Exception(lang('Daily limit for deposit amount is hit'), Api_common::CODE_MDN_DAILY_DEPOSIT_LIMIT_HIT);
                    }
                }else{
                    // $mesg = lang('Thank you for your deposit Please check back again later.');
                    $mesg = lang('Thanks for your deposit.');

                    if (isset($crypto)) {
                        $show_player_msg = sprintf(lang('Please transfer: %s (Rate: %s)'), $crypto, $player_rate);
                        $mesg = $show_player_msg;
                        $reason = $show_player_msg;
                        $show_reason_to_player = 1;
                        $deposit_order_args[] = $show_player_msg;
                        $deposit_order_args[] = $show_reason_to_player;
                    }
                    $saleOrder = call_user_func_array([$this->ci->sale_order, 'createDepositOrder'], $deposit_order_args);

                    $message = [
                        "success" => true,
                        "order" => $saleOrder,
                        // "payment_account_data" => $payment_account_data,
                        "mesg" => $mesg
                    ];

                    // if($this->ci->utils->isEnabledFeature('enable_deposit_upload_documents')){
                    //     $this->upload_attached_document($file1,$saleOrder['id'],$playerId);
                    //     $this->upload_attached_document($file2,$saleOrder['id'],$playerId);
                    // }

                    return $message;
                }
            }else{
                $saleOrder = call_user_func_array([$this->sale_order, 'createDepositOrder'], $deposit_order_args);

                $message = [
                    "success" => true,
                    "order" => $saleOrder,
                    // "payment_account_data" => $payment_account_data,
                    "mesg" => lang('Thank you for your deposit Please check back again later.'),
                ];

                // if($this->ci->utils->isEnabledFeature('enable_deposit_upload_documents')){
                //     $this->upload_attached_document($file1,$saleOrder['id'],$playerId);
                //     $this->upload_attached_document($file2,$saleOrder['id'],$playerId);
                // }

                if (isset($crypto)) {
                    $message['mesg'] = sprintf(lang('Please transfer: %s , Rate: %s'), $crypto, $player_rate);
                }

                return $message;
            }
        }
        catch (Exception $ex) {
            return [
            	'success' => false, 'code' => $ex->getCode(), 'mesg' => $ex->getMessage()
            ];
        }

    } // End function comapi_manual_deposit()

    /**
     * Simplified version of comapi_manual_deposit()
     * Most checking removed; not to be used directly, USE WITH CAUTION
     * OGP-15167
     * @param	array 	$deposit_req	Deposit request as array
     * @return	arrau
     */
    public function comapi_deposit_bare($deposit_req) {
        $this->ci->utils->debug_log(__METHOD__, 'deposit_req', $deposit_req);
        $depositAmount			= $this->ci->utils->safeGetArray($deposit_req, 'amount');
        $payment_account_id		= $this->ci->utils->safeGetArray($deposit_req, 'payment_account_id');
		$playerBankDetailsId 	= $this->ci->utils->safeGetArray($deposit_req, 'playerBankDetailsId');
        $deposit_notes			= $this->ci->utils->safeGetArray($deposit_req, 'deposit_notes');
		$playerId 				= $deposit_req['player_id'];
		$defaultCurrency		= $this->ci->utils->getCurrentCurrency()['currency_code'];
		$secure_id 				= $this->ci->utils->safeGetArray($deposit_req, 'secure_id');
		$deposit_time 			= $this->ci->utils->safeGetArray($deposit_req, 'deposit_time');
		$deposit_time_out 		= $this->ci->utils->safeGetArray($deposit_req, "deposit_time_out");
        $depositor_name			= $this->ci->utils->isEnabledFeature('enable_manual_deposit_input_depositor_name') ? $this->ci->utils->safeGetArray($deposit_req, "firstName") : null;
        $dwIp					= $this->ci->input->ip_address();
        $geolocation			= $this->ci->utils->getGeoplugin($dwIp);
        $player_promo_id		= null;
        $sub_wallet_id			= $this->ci->utils->safeGetArray($deposit_req, "wallet_id");
        $group_level_id			= null;
        $depositDatetime		= $this->ci->utils->safeGetArray($deposit_req, "deposit_datetime");
        $mode_of_deposit		= $this->ci->utils->safeGetArray($deposit_req, "mode_of_deposit");
        $depositReferenceNo		= null;
        $pendingDepositWalletType = null;
        $external_notes			= $this->ci->utils->safeGetArray($deposit_req, "external_notes");

		$promo_cms_id			= $this->ci->utils->safeGetArray($deposit_req, 'promo_cms_id');
		$is_mobile				= $this->ci->utils->safeGetArray($deposit_req, 'is_mobile');

		try{
			// * Deposit cool down time - skip first

			// * responsible gaming - skip first

			// * allow only one deposit at a time - skip first

			// * promo operation - keep as a placeholder
			$error = null;
			$player_promo_id = null;
	        $depositAccountNo = null;

			list($promo_cms_id, $promo_rules_id, $promorule) = $this->process_promo_rules($playerId, $promo_cms_id, $depositAmount, $error, $sub_wallet_id);
	        $this->ci->utils->debug_log(__METHOD__, 'process_promo_rules result', ['promo_cms_id' => $promo_cms_id, 'promo_rules_id' => $promo_rules_id, 'error' => $error, 'sub_wallet_id' => $sub_wallet_id ]);
			$promo_info = [
                'promo_rules_id'    => $promo_rules_id ,
                'promo_cms_id'      => $promo_cms_id
            ];

            // * Checks for payment account - moved to Playercenterapi::deposit_create_and_approve()

            $payment_account = $this->ci->payment_account->getPaymentAccountWithVIPRule($payment_account_id, $playerId);

            $banktype = $this->ci->banktype->getBankTypeById($payment_account->bankTypeId);
            // * crypto support - removed

            // * min/max deposit amount - moved to Playercenterapi::deposit_create_and_approve()

            $deposit_order_args = [
                Sale_order::PAYMENT_KIND_DEPOSIT,
                $payment_account_id,
                $playerId,
                $depositAmount,
                $defaultCurrency,
                $player_promo_id,
                $dwIp,
                $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'],
                $playerBankDetailsId,
                null,
                null,
                $deposit_notes,
                Sale_order::STATUS_PROCESSING,
                $sub_wallet_id,
                $group_level_id,
                $depositDatetime,
                $depositReferenceNo,
                $pendingDepositWalletType,
                null,
                $is_mobile,
                $this->ci->utils->getNowForMysql(),
                $promo_info,
                $depositor_name,
                $depositAccountNo,
                Sale_order::PLAYER_DEPOSIT_METHOD_UNSPECIFIED,
                $secure_id,
                $deposit_time,
                $deposit_time_out,
                $mode_of_deposit,
                $external_notes ,
            ];


            // // * amount checking - moved to controller

            $mesg = lang('Thanks for your deposit.');

            // * crypto support - removed

            $saleOrder = call_user_func_array([$this->ci->sale_order, 'createDepositOrder'], $deposit_order_args);

            $deposit_res = [
                "success"	=> true,
                "order"		=> $saleOrder,
                "mesg"		=> $mesg
            ];

            return $deposit_res;

        }
        catch (Exception $ex) {
            return [
            	'success' => false, 'code' => $ex->getCode(), 'mesg' => $ex->getMessage()
            ];
        }

    } // End function comapi_manual_deposit_simple()

    /**
     * Build the failure reason for transferAllTo from Utils::transferWallet() return
     * Only supports failures due to transfer condition
     * OGP-17203
     * @param	array 	$tx_res		result field contents returned by transferWallet()
     * @return	array 	array of simplified transfer condition; otherwise false
     */
    public function tx_all_failure_reason_format($tx_res) {
    	$ret = null;
    	try {
    		if (!isset($tx_res['code'])) {
    			throw new Exception('tx_res.code absent', 0x1);
    		}

    		switch ($tx_res['code']) {
    			case Utils::XFERWALLET_XFER_CONDS_ACTIVE :
		    		// Skip if tx_cond not available
		    		if (!isset($tx_res['details']['tx_cond'])) {
		    			throw new Exception('tx_cond return malformed', 0x3);
		    		}

		    		$tx_cond = $tx_res['details']['tx_cond'];

		    		$ret = [
		    			'transfer_condition' => [
		    				'subwallets_disallowed' => $tx_cond['disallow_transfer_in_wallets_name'] ,
		    				'bet_required' => $tx_cond['conditionAmount'] ,
		    				'started_at' => $tx_cond['started_at']
		    			] ,
		    			// 'tx_cond' => $tx_cond
		    		];
		    		break;

		    	default :
		    		throw new Exception('Not implemented', 0x2);
		    		break;
		    }
    	}
    	catch (Exception $ex) {
    		$this->ci->utils->debug_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'mesg' => $ex->getMessage() ]);
    	}
    	finally {
            return $ret;
        }
    } // End function tx_all_to_failure_reason_format()

    /**
     * Fetches and converts settings for player bank accounts, group 'others'
     * Synopsis:
	 *	 financial_account_withdraw_account_limit
	 *	 financial_account_deposit_account_limit
	 *	 financial_account_max_deposit_account_number
	 *	 financial_account_max_withdraw_account_number
	 *	 financial_account_enable_deposit_bank
	 *	 financial_account_require_deposit_bank_account
	 *	 financial_account_can_be_withdraw_and_deposit
	 *	 financial_account_deposit_account_default_unverified
	 *	 financial_account_allow_edit
	 *	 financial_account_one_account_per_institution
	 *	 financial_account_allow_delete
	 *	 financial_account_withdraw_account_default_unverified
	 *	 bank_number_validator_mode
	 * all fields contain value object of { value, params, note }
     * @see		Operatorglobalsettings::FINANCIAL_ACCOUNT_SETTING_OTHERS_LIST
     * @see		SBE/payment_management/viewPlayerCenterFinancialAccountSettings
     * @return	JSON	Contains original fields in synopsis above and derived fields
     *                       account_max_count_withdraw (int), account_max_count_deposit (int).
     */
    public function fin_acc_rules_others() {
    	$this->ci->load->model(['operatorglobalsettings']);

    	$fro = $this->ci->operatorglobalsettings->getSystemSettings(Operatorglobalsettings::FINANCIAL_ACCOUNT_SETTING_OTHERS_LIST);

    	// Derived values
    	$fro['account_max_count_withdraw'] =
    		!empty($fro['financial_account_withdraw_account_limit']['value']) && $fro['financial_account_max_withdraw_account_number']['value'] > 0 ? $fro['financial_account_max_withdraw_account_number']['value'] : 0;
    	$fro['account_max_count_deposit'] =
    		!empty($fro['financial_account_deposit_account_limit']['value']) && $fro['financial_account_max_deposit_account_number']['value'] > 0 ? $fro['financial_account_max_deposit_account_number']['value'] : 0;

    	return $fro;
    }

    /**
     * Return the value of option financial_account_can_be_withdraw_and_deposit in player acc settings
     * @return	bool	true if an account can be used in both modes; false if not.
     */
    public function fin_acc_rules_same_acc_for_both_deposit_and_withdraw() {
    	$fro = $this->fin_acc_rules_others();
    	$fr_safbdw = $fro['financial_account_can_be_withdraw_and_deposit']['value'];

    	return $fr_safbdw;
    }

    /**
     * Fetches and converts settings for player bank accounts, group 'accounts'
     * Synopsis: (sample)
	 *  id: 1,
	 *	account_number_min_length: 16,
	 *	account_number_max_length: 19,
	 *	account_number_only_allow_numeric: 1,
	 *	account_name_allow_modify_by_players: 0,
	 *	field_show: 1,3 (string),
	 *	field_required: 1 (string)
	 * Field mapping for fields 'field_show', 'field_required':
	 *  1	?
	 *  2	phone
	 *  3	branch
	 *  4	province
	 *  4	city
	 *  5	bankAddress
     * @see		Financial_account_setting::getPlayerFinancialAccountRulesByPaymentAccountFlag()
     * @see		SBE/payment_management/viewPlayerCenterFinancialAccountSettings
     * @return	JSON	Contains original fields and two derived fields:
     *                       regex_accnum		regex for account number considering options
     *                       	[account_number_min_length], [account_number_max_length], [account_number_only_allow_numeric]
     *                       required_fields	list of fields converted from [field_required]
     */
    public function fin_acc_rules_acc() {
    	$this->ci->load->model(['financial_account_setting']);
    	$fra = $this->ci->financial_account_setting->getPlayerFinancialAccountRulesByPaymentAccountFlag(1);
    	// Char class
    	$opt_chars = $fra['account_number_only_allow_numeric'] ? '\d' : '\w';
    	// Length
    	$opt_len = '+';
    	if (!empty($fra['account_number_min_length'])) {
			$opt_len = sprintf("{%d,%d}", $fra['account_number_min_length'], $fra['account_number_max_length']);
    	}
    	$regex_accnum = "/^{$opt_chars}{$opt_len}$/";

    	$this->ci->utils->debug_log(__METHOD__, 'acc regex opts', [ 'num_only' => $fra['account_number_only_allow_numeric'] , 'len_min' => $fra['account_number_min_length'] , 'len_max' => $fra['account_number_max_length'], 'regex' => $regex_accnum ]);

    	// $fs_out = $fra;
    	$fra['regex_accnum'] = $regex_accnum;
    	$fra['format_accnum'] = sprintf("%s, %d - %d places", ($fra['account_number_only_allow_numeric'] ? 'Digits only' : 'Alphanumeric'), $fra['account_number_min_length'], $fra['account_number_max_length']);

    	// Required fields
    	$field_req_map = [
    		1 => '***' ,
    		2 => 'phone' ,
    		3 => 'branch' ,
    		4 => ['province', 'city'] ,
    		5 => 'bankAddress'
    	];

    	$fr_fields = [];
    	$frkey_ar = explode(',', $fra['field_required']);
    	// $this->ci->utils->debug_log(__METHOD__, 'frkey_ar', $frkey_ar);
    	foreach ($frkey_ar as $frkey) {
    		if (!isset($field_req_map[$frkey])) {
    			$this->ci->utils->debug_log(__METHOD__, 'Unmapped key in field_required', $frkey);
    			continue;
    		}
    		$fr_field = $field_req_map[$frkey];
    		if ($fr_field == '***') { continue; }
    		// $this->ci->utils->debug_log(__METHOD__, 'fr_field', $fr_field);
    		if (is_array($fr_field)) {
    			$fr_fields = array_merge($fr_fields, $fr_field);
    		}
    		else {
    			$fr_fields[] = $fr_field;
    		}
    	}

    	$fra['required_fields'] = $fr_fields;

    	return $fra;
    } // End function fin_acc_rules_acc()

} // End of class comapi_lib
