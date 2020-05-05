<?php

if (!class_exists('BaseController')) {

	require_once dirname(__FILE__) . '/modules/lock_app_module.php';

    /**
     * Class BaseController
     *
     * @property CI_Input $input
     * @property CI_Output $output
     * @property CI_URI $uri
     * @property CI_Lang $lang
     * @property CI_Session $session
     * @property CI_Config $config
     * @property Utils $utils
     * @property Language_function $language_function
     * @property Operatorglobalsettings $operatorglobalsettings
     */
	class BaseController extends CI_Controller {

		use lock_app_module;

		public $_app_prefix;

		public function __construct() {
			parent::__construct();

	        $this->_app_prefix=$this->utils->getAppPrefix();

			$this->CI = $this;
			// $this->utils->setAppNameToMonitor();

			if ($this->uri->segment(2) == 'check_player_session_timeout') {
				//ignore
				$this->utils->debug_log('try ignore ', $this->uri->segment(2));
			} else {
				$this->loadSiteVars();
			}

			// if ($this->utils->isEnabledClockwork() && !$this->input->is_cli_request()) {
			// 	$GLOBALS['EXT']->_call_hook('pre_controller_constructor');
			// }

			$this->double_submit_protection=$this->utils->getConfig('double_submit_protection');
			$this->double_submit_session_name=$this->utils->getConfig('double_submit_session_name');
			$this->double_submit_post_token_name=$this->utils->getConfig('double_submit_post_token_name');

			// } else {
			// 	Clockwork\Support\CodeIgniter\Hook::disable();
			// }

			// if($this->utils->isPlayerSubProject()){
			// 	$this->load->library(['session', 'authentication']);
			// }
			$this->setCurrencyForAllSubProject();
		}

		public function setCurrencyForAllSubProject(){
			if($this->utils->isEnabledMDB()){
		        $this->load->vars('active_currency_key', $this->utils->isEnabledMDB() ? $this->utils->getActiveCurrencyKeyOnMDB() : null);
		        $this->load->vars('available_currency_list', $this->utils->getAvailableCurrencyList());

		        $logged=false;
				$addItemAll=true;
				$class='form-control input-sm';
		        if($this->utils->isAdminSubProject()){
					$addItemAll=true;
					$this->load->library(['authentication']);
					$logged=$this->authentication->isLoggedIn();
					$class='form-control input-sm';
		        }elseif($this->utils->isAffSubProject()){
					$addItemAll=true;
					$logged=!empty($this->getAffUsernameFromSession()) &&
						!empty($this->getAffIdFromSession());
					$class='form-control input-sm';
		        }elseif($this->utils->isAgencySubProject()){
					$addItemAll=true;
		        	$logged=$this->isLoggedAgency();
					$class='form-control input-sm';
		        }elseif($this->utils->isPlayerSubProject()){
					$addItemAll=false;
					$this->load->library(['authentication']);
					$logged=$this->authentication->isLoggedIn();
					$class='currency_list';
		        }

				$this->load->vars('currency_select_html', $this->utils->buildCurrencySelectHtml($logged, $addItemAll, $class));

			}else{
		        $this->load->vars('active_currency_key', null);
		        $this->load->vars('available_currency_list', null);
		        $this->load->vars('currency_select_html', null);
			}


		}

		public function getAffIdFromSession() {
			return $this->session->userdata('affiliateId');
		}

		public function getAffUsernameFromSession() {
			return $this->session->userdata('affiliateUsername');
		}

		const MSG_CODE_LOGIN_FAILED = 'login_failed';
		const MSG_CODE_LOGIN_SUCCESSFULLY = 'login_successfully';
		const MSG_CODE_CAPTCHA_FAILED = 'captcha_failed';
		const MSG_CODE_LOGIN_PT_FAILED = 'login_pt_failed';
		const MSG_CODE_LOGIN_PT_WRONG_REGION = 'login_pt_wrong_region';
		const MSG_CODE_SMS_CODE_FAILED = 'sms_code_failed';
		const MSG_CODE_LOGIN_BLOCKED_FAILED = 'login_blocked';
		const MSG_CODE_LOGIN_CHECK_INPUT = 'login_check_input';
		const MSG_CODE_LOGIN_SELFEXCLUSION = 'selfexclusion';
		const MSG_CODE_LOGIN_ACCOUNT_WRONG  = 'login_account_password';
		const MSG_CODE_LOGIN_PASSWORD_WRONG = 'login_wrong_password';

		const MESSAGE_TYPE_SUCCESS = 1;
		const MESSAGE_TYPE_ERROR = 2;
		const MESSAGE_TYPE_WARNING = 3;

		const GAME_UNBLOCK = '0';
		const GAME_FROZEN = '1';
		const GAME_BLOCK = '2';

		const BONUS_MODE_ENABLE = 1;
		const BONUS_MODE_DISABLE = 0;
		const BONUS_TYPE_FIXAMOUNT = 1;
		const BONUS_TYPE_BYPERCENTAGE = 2;
		const LANG_EN = 1;
		const LANG_CN = 2;
		const LANG_ID = 3;
		const LANG_VN = 4;
		const LANG_KR = 5;
		const LANG_TH = 6;
		const TRUE = 1;
		const FALSE = 0;
		const FIRST_CHILD_INDEX = 0;

		const R1 = 'R1';
		const R2 = 'R2';
		const R3 = 'R3';
		const R4 = 'R4';
		const R5 = 'R5';
		const R6 = 'R6';
		const R7 = 'R7';
		const R8 = 'R8';
		const RC = 'RC';//risk score

		//Kingrich Scheduler Status
		const PENDING = 1;
		const ONGOING = 2;
		const PAUSED = 3;
		const STOPPED = 4;
		const DONE = 5;

		private function loadSiteVars() {
			// $this->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));
			$this->load->library(array('language_function', 'session'));
			$currentLanguage = $this->language_function->getCurrentLanguageName();

			# cache site vars for better performance
			# Note: this doesn't affect login page, whose controller does not inherit BaseController
			$key = 'site_vars_' . $currentLanguage;

            if(!$this->utils->isEnabledFeature('force_refresh_cache')){
                $siteVars = $this->utils->getTextFromCache($key);

                $siteVars = (is_array($siteVars)) ? $siteVars : @json_decode($siteVars, TRUE);
            }

			if (empty($siteVars)) {
				$this->load->model('static_site');
				$siteVars['company_title'] = $this->static_site->getDefaultCompanyTitle($currentLanguage);
				$siteVars['contact_skype'] = $this->static_site->getDefaultContactSkype($currentLanguage);
				$siteVars['contact_email'] = $this->static_site->getDefaultContactEmail($currentLanguage);
				$siteVars['logo_icon'] = $this->static_site->getDefaultLogoUrl();

				$static_site = $this->static_site->getSiteByName('default');
				$siteVars['favicon'] = $static_site->fav_icon_filepath;

				$siteVars['player_center_css'] = $static_site->player_center_css;
				$siteVars['admin_css'] = $static_site->admin_css;
				$siteVars['aff_css'] = $static_site->aff_css;
				// $this->cache->save('site_vars_' . $currentLanguage, $siteVars);
				$this->utils->saveTextToCache($key, $siteVars);
				// } else {
				// 	$siteVars = $this->cache->get('site_vars_' . $currentLanguage);
			}

			# make the site vars accessible from template
			$this->load->vars($siteVars);
		}

		protected function goPlayerLogin() {
			redirect($this->utils->getPlayerLoginUrl());
		}

		protected function goPlayerRegister() {
			redirect($this->getPlayerRegisterUri());
		}

		protected function getPlayerRegisterUri() {
			return $this->config->item('player_register_uri');
		}

		protected function goPlayerHome() {
			redirect(site_url($this->utils->getPlayerHomeUrl()));
		}

		protected function goWebsiteHome() {
			redirect($this->utils->getSystemUrl('www'));
		}

		protected function goCasino() {
			redirect(site_url('iframe_module/iframe_casino'));
		}

		protected function goActiveAccount($playerId) {
            redirect(site_url('player_center/iframe_activate'));
		}

		protected function goMakeDeposit($type = '', $bankTypeId = null, $payment_account_id = NULL) {
		    if($this->utils->is_mobile()){
                $uri = 'iframe_module/iframe_makeDeposit/' . $type;
                if (!empty($bankTypeId)) {
                    $uri .= '/' . $bankTypeId;
                }
            }else{
		        $uri ='/player_center2/deposit/auto_payment/' . $payment_account_id;
            }
			redirect(site_url($uri));
		}

		protected function goViewCashier() {
			redirect(site_url('iframe_module/iframe_viewCashier'));
		}

		protected function goMessages() {
			redirect($this->utils->getPlayerMessageUrl());
		}

		protected function goPlayerSettings($playerId) {
			// git issue #1053: add anchor so that page jumps directly to main-content
			redirect(site_url("iframe_module/iframe_playerSettings/{$playerId}#main-content"));
			// redirect(site_url('iframe_module/iframe_playerSettings/' . $playerId));
		}

		protected function goBankDetails() {
			redirect(site_url('iframe_module/iframe_bankDetails'));
		}

		/**
		 * Response BadRequest with simple TEXT
		 *
		 * @param boolean $addOrigin Header add about Origin settings or not.
		 * @param string $origin The domain for Allow-Origin.
		 * @param string $appendText Append for check from line no.
		 * @return void
		 */
		protected function returnBadRequest($addOrigin = false, $origin = "*", $appendText = '') {
			return $this->returnErrorStatus('400', $addOrigin, $origin, lang('Bad Request'). $appendText);
		}// EOF returnBadRequest

		protected function getOriginFromHeader() {
			$origin = array_key_exists('HTTP_ORIGIN', $_SERVER) ? @$_SERVER['HTTP_ORIGIN'] : '';
			if (empty($origin)) {
				//try referer
				$http_referer = '';
				if (isset($_SERVER['HTTP_REFERER'])) {
					$http_referer = @$_SERVER['HTTP_REFERER'];
					if (empty($http_referer)) {
						$http_referer = '';
					}
				}

				$referer = $http_referer;
				if (!empty($referer)) {
					$arr = parse_url($referer);
					$origin = @$arr['scheme'] . '://' . @$arr['host'] . (array_key_exists('port', $arr) ? ':' . $arr['port'] : '');
				}
			}
			return $origin;
		}

		protected function getAvailableOrigin() {
			$safeOriginList = $this->config->item('safe_origin_list');
			// $this->utils->debug_log('safeOriginList', $safeOriginList);

			$origin = $this->getOriginFromHeader();
			if (!in_array($origin, $safeOriginList)) {
				$origin = '*';
			}
			return $origin;
		}

		protected function addOriginHeader($origin) {
			if ($origin == '*') {
				header("Access-Control-Allow-Origin: " . $this->getAvailableOrigin());
			} else {
				header("Access-Control-Allow-Origin: " . $origin);
			}
		}

		/**
		 * return error status
		 * @param  string  $code
		 * @param  boolean $addOrigin
		 * @param  string  $origin
		 * @param  string  $text
		 * @return boolean
		 */
		protected function returnErrorStatus($code = '400', $addOrigin = false, $origin = "*", $text = null) {
			$this->output->set_status_header($code)->append_output($text);
			if ($addOrigin) {
				$this->addOriginHeader($origin);
				header("Access-Control-Allow-Methods: GET, POST");
				header("Access-Control-Expose-Headers: Access-Control-Allow-Origin");
				header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
				header("Access-Control-Allow-Credentials: true");
			}
			return true;
		}

		public $internal_json_result=false;
		public $_json_result_array=null;

		protected function enableInternalJsonResult(){
			$this->internal_json_result=true;
			return $this->internal_json_result;
		}

		protected function disableInternalJsonResult(){
			$this->internal_json_result=false;
			return $this->internal_json_result;
		}

		protected function getInternalJsonResult(){
			return $this->_json_result_array;
		}

		/**
		 * return json result
		 * @param  array  $result
		 * @param  boolean $addOrigin
		 * @param  string  $origin
		 * @param  boolean $pretty
		 * @return output header and json
		 */
		protected function returnJsonResult($result, $addOrigin = true, $origin = "*", $pretty = false) {
			if($this->internal_json_result){
				$this->_json_result_array=$result;
				return true;
			}

			// $this->utils->sendDebugbarDataInHeaders();
			if ($pretty) {
				$txt = json_encode($result, JSON_PRETTY_PRINT);
			} else {
				$txt = json_encode($result);
			}

			$this->output->set_content_type('application/json')->set_output($txt);
			if ($addOrigin) {
				$this->addOriginHeader($origin);
				header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
				header("Access-Control-Expose-Headers: X-Requested-With, Access-Control-Allow-Origin");
				header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
				header("Access-Control-Allow-Credentials: true");
			}

			return true;
		}

		/**
		 * return jsonp
		 * @param  array  $result
		 * @param  string  $callBack
		 * @param  boolean $addOrigin
		 * @param  string  $origin
		 * @return boolean
		 */
		protected function returnJsonpResult($result, $callBack = null,  $addOrigin = true, $origin = "*") {
			if($this->internal_json_result){
				$this->_json_result_array=$result;
				return true;
			}

			$callback = $callBack ? $callBack : $this->input->get('callback');
			$this->output->set_content_type('application/x-javascript')->set_output($callback . "(" . json_encode($result) . ")");
			if ($addOrigin) {
				$this->addOriginHeader($origin);
				header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
				header("Access-Control-Expose-Headers: X-Requested-With, Access-Control-Allow-Origin");
				header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
				header("Access-Control-Allow-Credentials: true");
			}

			return true;
		}

		/**
		 * return js
		 * @param  string $js
		 * @return boolean
		 */
		protected function returnJS($js) {
			$this->output->set_content_type('application/x-javascript')->set_output($js);
			return true;
		}

		/**
		 * return regular text
		 * @param  string $msg
		 * @return boolean
		 */
		public function returnText($msg) {
			$this->output->append_output($msg);
			return true;
		}

		/**
		 * return xml
		 * @param  string $xml
		 * @return boolean
		 */
		public function returnXml($xml) {
			$this->output->set_content_type('text/xml')->set_output($xml);
			return true;
		}

        protected function returnCommon($status_code, $message, $data = NULL, $redirect_uri = '/', $return_type = NULL){
            $result = [];
            switch($status_code){
                case self::MESSAGE_TYPE_ERROR:
                    $result['status'] = 'error';
                    break;
                case self::MESSAGE_TYPE_WARNING:
                    $result['status'] = 'warning';
                    break;
                case self::MESSAGE_TYPE_SUCCESS:
                default:
                    $result['status'] = 'success';
            }
            $result['message'] = $message;
            $result['data'] = $data;

		    if(isset($_REQUEST['callback'])){
		        if($_REQUEST['callback'] === 'iframe_callback' && isset($_REQUEST['act'])){
                    $result['act'] = $_REQUEST['act'];
                    $view_data = ['result' => str_replace("'", "\\'", json_encode($result, JSON_HEX_QUOT | JSON_HEX_TAG)), 'origin' => $this->getAvailableOrigin()];

                    return $this->load->view('player/iframe_api_callback', $view_data);
                }else{
                    return $this->returnJsonpResult($result);
                }
            }else if($this->input->is_ajax_request() || ($return_type === 'json')){
                return $this->returnJsonResult($result);
            }else{
                $this->alertMessage($status_code, $message);
                redirect($redirect_uri);
                return;
            }
        }

		protected function getInputGetAndPost() {
			$flds = $this->input->get();
			$post = $this->input->post();

			if (empty($flds)) {
				$flds = array();
			}
			if (empty($post)) {
				$post = array();
			}

			if (!empty($post)) {
				$flds = array_merge($flds, $post);
			}

			//load query string
			// $queryString = @$_SERVER['QUERY_STRING'];
			// if (!empty($queryString)) {
			// 	parse_str($queryString, $qryArr);
			// 	if (!empty($qryArr)) {
			// 		$fld = array_merge($flds, $qryArr);
			// 	}
			// }
			return $flds;
		}

		protected function getQueryString() {
			$qryArr = null;
			//load query string
			$queryString = @$_SERVER['QUERY_STRING'];
			if (!empty($queryString)) {
				parse_str($queryString, $qryArr);
			}
			return $qryArr;
		}

		protected function goPaymentResultPage($systemId, $orderId, $resultCallback) {

			$message = !empty($resultCallback['message']) ? $resultCallback['message'] : '';
			//put message to flash
			$this->session->set_flashdata('message', $message);
			$this->session->set_flashdata('next_url', $resultCallback['next_url']);
			//redirect to page, so don't happen another submit when refresh page
			if ($resultCallback['success']) {
				redirect('/callback/show_success/' . $systemId . '/' . $orderId);
			} else {
				redirect('/callback/show_error/' . $systemId . '/' . $orderId);
			}

		}

		protected function goPayment($systemId, $amount, $playerId = null, $playerPromoId = null, $bankId = null, $orderId = null) {
			redirect($this->getPaymentUrl($systemId, $amount, $playerId, $playerPromoId, $bankId, $orderId));
		}

		protected function getPaymentUrl($systemId, $amount, $playerId = null, $playerPromoId = null, $bankId = null, $orderId = null) {
			$enabledSecondUrl = true;
			return $this->utils->getPaymentUrl('', $systemId, $amount, $playerId, $playerPromoId, $enabledSecondUrl, $bankId, $orderId);
		}

		// protected function getRepayUrl($systemId, $orderId) {
		// 	return '/redirect/repay/' . $systemId . '/' . $orderId;
		// }

		protected function getPlayerMobileLoginUrl($gamePlatformId) {
			return '/iframe/auth/loginGamePlatform/' . $gamePlatformId;
		}

		protected function getImageLoader() {
			return $this->utils->imageUrl('ajax-loader.gif');
		}

		protected function getPostData($fieldNames) {
			$data = array();
			if (!empty($fieldNames)) {
				foreach ($fieldNames as $n) {
					$data[$n] = $this->input->post($n);
				}
			}
			return $data;
		}

		protected function recordAction($management, $action, $description) {
			$this->utils->recordAction($management, $action, $description);
		}

		protected function saveAction($management, $action, $description = null) {
			$this->utils->recordAction($management, $action, $description);
		}

		/**
		 * Display Alert Message
		 * @param  int  $type   If type is success,error or warning.
		 * @param  string  $message Display in alert.
		 * @param  boolean $stay Check if want to stay on page or redirect in designated sidebar if length is 1.
		 * @return void
		 * Updated by: Frans Eric Dela Cruz (frans.php.ph) 10-23-2018
		 */
		protected function alertMessage($type, $message) {
			$type = intval($type);
			switch ($type) {
			case self::MESSAGE_TYPE_SUCCESS:
				$show_message = array(
					'result' => 'success',
					'message' => $message,
				);
				$this->session->set_userdata($show_message);
				break;

			case self::MESSAGE_TYPE_ERROR:
				$show_message = array(
					'result' => 'danger',
					'message' => $message,
				);
				$this->session->set_userdata($show_message);
				break;

			case self::MESSAGE_TYPE_WARNING:
				$show_message = array(
					'result' => 'warning',
					'message' => $message,
				);
				$this->session->set_userdata($show_message);
				break;
			}
		}

		protected function isPostMethod() {
			//check REQUEST_METHOD
			if (isset($_SERVER['REQUEST_METHOD'])) {
				return strtolower(@$_SERVER['REQUEST_METHOD']) == 'post';
			}
			return false;
		}

		protected function isGetMethod() {
			//check REQUEST_METHOD
			if (isset($_SERVER['REQUEST_METHOD'])) {
				return strtolower(@$_SERVER['REQUEST_METHOD']) == 'get';
			}
			return false;
		}

		/**
		 * Save http Request
		 *
		 * @return  rendered Template
		 */
		protected function saveHttpRequest($player_id, $type) {
			$this->utils->saveHttpRequest($player_id, $type);
		}

		/**
		 * PLEASE USE saveHttpRequest
		 */
		// protected function saveHttpOnRequest($player_id, $data, $type) {
		// 	$this->utils->saveHttpOnRequest($player_id, $data, $type);
		// }

		protected function getHttpOnRequest() {
			$this->utils->getHttpOnRequest();
		}

		protected function getSession($key) {
			return $this->session->userdata($key);
		}

		protected function putSession($key, $value) {
			$this->session->set_userdata($key, $value);
		}

		protected function initSidebar() {
			if (($this->getSession('sidebar_status') == NULL)) {
				$this->putSession(array('sidebar_status' => 'active'), NULL);
			}
		}

		protected function getPostAndPutSessionIfNotEmpty($name) {
			$value = $this->input->post($name);
			if (!empty($value)) {
				//write to session
				$this->putSession($name, $value);
			}
			return $value;
		}

		protected function setLangByPlayer() {
			$this->load->library(array('authentication'));
			$langCode = $this->authentication->initiateLang();
			$this->utils->debug_log('language', $langCode);
		}

		protected function getDeskeyOG() {
			return $this->config->item('DESKEY_OG');
		}

		protected function goPlayerPromotions() {
			redirect('/iframe_module/iframe_promos');
		}

		protected function goPlayerMyPromotions($redirect = null) {
			if ($this->utils->getPlayerCenterTemplate() == 'iframe') {
				redirect('/iframe_module/my_promo');
            } elseif ($this->utils->getPlayerCenterTemplate() == 'player_dashboard3') {
                redirect('/player_center/dashboard');
            } elseif ($this->utils->getPlayerCenterTemplate() == 'stable_center2') {
                redirect('/player_center2/promotion');
            } else {
				redirect('/player_center/my_promo');
			}
		}

		protected function getLoggedPlayerId() {
			if (!isset($this->authentication)) {
				$this->load->library(array('authentication'));
			}
			return $this->authentication->getPlayerId();
		}

		protected function getPlayerDomain() {
			// preg_match("/(?<=http:\/\/).*(?=\/)/", $this->utils->getBaseUrlWithHost(), $serverNameMatchArr);
			$thisDomain = $this->utils->getHttpHost(); // $serverNameMatchArr[0];
			// if ($this->config->item('enable_multi_site')) {
			// 	$refDomain = $this->getHostByRef();
			// 	foreach ($this->config->item('site_list') as $value) {
			// 		if ($refDomain == $value) {
			// 			$thisDomain = 'player.' . $refDomain;
			// 		}
			// 	}
			// }
			return $thisDomain;
		}

		// protected function getHostByRef() {
		// 	$http_referer = '';
		// 	if (isset($_SERVER['HTTP_REFERER'])) {
		// 		$http_referer = @$_SERVER['HTTP_REFERER'];
		// 		if (empty($http_referer)) {
		// 			$http_referer = '';
		// 		}
		// 	}

		// 	preg_match("/((\w+\.){1,2}|staging\.(\w+\.){1,2})(com|net|org|la|local|club|info|biz|co|online|link)(?!\w)/", $http_referer, $thisDomainMatchArr);
		// 	$thisDomainMatch = preg_replace("/^www\.|^player\./", '', @$thisDomainMatchArr[0]);
		// 	return $thisDomainMatch;
		// }

		protected function getTrackingCodeByRef() {
			$code = null;
			$http_referer = '';
			if (isset($_SERVER['HTTP_REFERER'])) {
				$http_referer = @$_SERVER['HTTP_REFERER'];
				if (empty($http_referer)) {
					$http_referer = '';
				}
			}

			$url = $http_referer;
			if (!empty($url)) {
				$query_str = parse_url($url, PHP_URL_QUERY);
				parse_str($query_str, $query_params);
				if (!$query_params) {
					$seg = explode("/", $url);
					if (isset($seg[3]) && isset($seg[4]) && @$seg[3] == 'aff') {
						$code = @$seg[4];
					}
				} else {
					if (isset($query_params['aff'])) {
						$code = $query_params['aff'];
					}
					if (isset($query_params['code']) && empty($code)) {
						$code = $query_params['code'];
					}
					if (isset($query_params['aid']) && empty($code)) {
						$code = $query_params['aid'];
					}
				}
			}

			return $code;
		}

		protected function makeFrontUrl() {
			$serverName = 'www.' . $this->getMainDomain();
			if ($serverName != "default") {
				$serverName = $this->utils->getConfig('site_domain');
			}
			// echo $serverName;exit;
			return $serverName;
		}

		protected function getMainDomain() {
			// preg_match("/(?<=http:\/\/).*(?=\/)/", $this->utils->getBaseUrlWithHost(), $serverNameMatchArr);
			$host = $this->utils->getHttpHost();
			return preg_replace("/^(player|aff|admin)\./", '', $host);
		}

		protected function checkAffDomain() {
			$code = null;
			// $this->utils->debug_log('aff domain', @$_SERVER['HTTP_REFERER']);
			$http_referer = '';
			if (isset($_SERVER['HTTP_REFERER'])) {
				$http_referer = @$_SERVER['HTTP_REFERER'];
				if (empty($http_referer)) {
					//use current domain
					$http_referer = @$_SERVER['HTTP_HOST'];
					if (!empty($http_referer)) {
						$http_referer = 'http://' . $http_referer;
					}
				}
			}

			if (!empty($http_referer)) {
				$affdomain = parse_url($http_referer, PHP_URL_HOST);
				if (!empty($affdomain)) {
					$this->utils->debug_log('affdomain', $affdomain);

					// $affdomain = $this->getMainDomain();
					$this->load->model('affiliatemodel');
					$code = $this->affiliatemodel->getTrackingCodeFromAffDomain($affdomain);

					$this->utils->debug_log('code', $code);
				}
			}

			if (empty($code)) {
				/*$http_host = @$_SERVER['HTTP_HOST'];
				$this->utils->debug_log('try current domain -------->', $http_host);*/

                $http_host = $this->getMainDomain();
				$this->load->model('affiliatemodel');
				$code = $this->affiliatemodel->getTrackingCodeFromAffDomain('www.'.$http_host);
			}
			return $code;
		}

		/**
		 * tracking_code.og.local
		 * www.og.local/aff/tracking_code
		 * www.og.local/aff.html?aff=tracking_code or www.og.local/aff.html?code=tracking_code
		 */
		public function getTrackingCode() {

			$tracking_code_priority_levels = $this->utils->getConfig('tracking_code_priority_levels');
			$trackingCode = null;
			$trackingCodeInRef = $this->getTrackingCodeByRef();
			$trackingCodeInDomain = $this->checkAffDomain();
			$trackingCodeInSession = $this->utils->getTrackingCodeFromSession(); // $this->session->userdata('tracking_code');
			$this->utils->debug_log('tracking_code on ' . current_url(), $trackingCodeInRef, $trackingCodeInDomain, $trackingCodeInSession);

			foreach ($tracking_code_priority_levels as $tracking_code_priority_level) {

				switch ($tracking_code_priority_level) {

				case 'trackingCodeInRef':
					$trackingCode = $trackingCodeInRef;
					break;

				case 'trackingCodeInDomain':
					$trackingCode = $trackingCodeInDomain;
					break;

				case 'trackingCodeInSession':
					$trackingCode = $trackingCodeInSession; // $this->session->userdata('tracking_code');
					// $trackingCode = $this->session->userdata('tracking_code');
					break;

				}

				if (!empty($trackingCode)) {
					break;
				}

			}

			return !empty($trackingCode) ? $trackingCode : '';
		}

		// protected function getTrackingSourceCodeByDomain() {
		// 	$code = null;
		// 	// $this->utils->debug_log('aff domain', @$_SERVER['HTTP_REFERER']);
		// 	$http_referer = '';
		// 	if (isset($_SERVER['HTTP_REFERER'])) {
		// 		$http_referer = @$_SERVER['HTTP_REFERER'];
		// 		if (empty($http_referer)) {
		// 			$http_referer = '';
		// 		}
		// 	}
		// 	if (!empty($http_referer)) {
		// 		$affdomain = parse_url($http_referer, PHP_URL_HOST);
		// 		if (!empty($affdomain)) {
		// 			$this->utils->debug_log('affdomain', $affdomain);

		// 			// $affdomain = $this->getMainDomain();
		// 			$this->load->model('affiliatemodel');
		// 			if($this->affiliatemodel->isAffDomain($affdomain)){

		// 			}

		// 			// $this->utils->debug_log('code', $code);
		// 		}
		// 	}
		// 	return $code;
		// }

		protected function getTrackingSourceCodeByRef() {
			$code = null;
			$http_referer = '';
			if (isset($_SERVER['HTTP_REFERER'])) {
				$http_referer = @$_SERVER['HTTP_REFERER'];
				if (empty($http_referer)) {
					$http_referer = '';
				}
			}

			$url = $http_referer;
			if (!empty($url)) {
				$query_str = parse_url($url, PHP_URL_QUERY);
				parse_str($query_str, $query_params);
				if (!$query_params) {
					$seg = explode("/", $url);
					if (isset($seg[3]) && isset($seg[4]) && @$seg[3] == 'aff') {
						$code = isset($seg[5]) ? @$seg[5] : null;
					}
				} else {
					if (isset($query_params['source'])) {
						$code = $query_params['source'];
					}
				}
			}

			return $code;
		}

		public function getTrackingSourceCode() {
			$trackingSourceCode = false;
			$trackingSourceCodeInRef = $this->getTrackingSourceCodeByRef();
			// $trackingSourceCodeInDomain = $this->getTrackingSourceCodeByDomain();
			$trackingSourceCodeInSession = $this->session->userdata('tracking_source_code');
			$this->utils->debug_log('tracking_source_code', $trackingSourceCodeInRef, $trackingSourceCodeInSession);
			// domain > ref > session
			// if ($trackingCodeInDomain) {
			// 	$trackingCode = $trackingCodeInDomain;
			// } else
			if ($trackingSourceCodeInRef) {
				$trackingSourceCode = $trackingSourceCodeInRef;
			} else if ($trackingSourceCodeInSession) {
				$trackingSourceCode = $trackingSourceCodeInSession;
			}
			if ($trackingSourceCode == false) {$trackingSourceCode = '';}
			return $trackingSourceCode;
		}

		protected function getPrefixUsernameOfPlayer($trackingCode = null) {
			$this->load->model(['affiliatemodel']);
			$prefixOfPlayer = null;
			if (!empty($trackingCode)) {
				$prefixOfPlayer = $this->affiliatemodel->getPrefixByTrackingCode($trackingCode);
			}
			if (empty($prefixOfPlayer)) {
				$prefixOfPlayer = $this->utils->getConfig('main_prefix_of_player');
			}

			return $prefixOfPlayer;
		}

		public function is_exist($value, $table_column) {
			list($table, $column) = explode('.', $table_column);
			$this->db->from($table);
			$this->db->where($column, $value);
			if ($this->db->count_all_results() === 0) {
				if (isset($this->form_validation)) {
					$this->form_validation->set_message('is_exist', '%s <b>' . $value . '</b> does not exist');
				}
				return FALSE;
			} else {
				return TRUE;
			}
		}

		protected function writeSyncId($key, $syncId) {
			$syncFile = $this->oghome . '/application/logs/sync_id_' . $key;
			file_put_contents($syncFile, $syncId);
		}

		public function getNowForMysql() {
			return $this->utils->getNowForMysql();
		}

		protected function redirect($url, $mode) {
			redirect($url, $mode);
		}

		protected function safeGetParam($name, $defaultVal = null, $isBool = false) {
			//$val = null;
			//if ($this->input->get($name) !== '') {
			//if ($this->input->get($name)) {
			$val = $this->input->get($name);
			if ($val !== null && $val !== false && !is_array($val)) {
				$val = strip_tags($val);
			}
			//}
			// $this->utils->debug_log('SAFEGETPARAM', $name, 'val', $val, $val===null);
			if ($val === null || $val === false) {
// && $this->input->post($name)) {
				$val = $this->input->post($name);
				if ($val !== null && $val !== false && !is_array($val)) {
					$val = strip_tags($val);
				}
			}
			//$this->utils->debug_log($name, 'val', $val, $val===null);
			if ($isBool) {
				$val = $val == 'true' || $val == '1' || $val=='on';
			} else {
				if ($val === null || $val === false) {
					$val = $defaultVal;
				}
			}
			//$this->utils->debug_log($val);

			return $val;
		}

		protected function safeLoadParams($prepParams) {
			$params = array();
			if (!empty($prepParams)) {
				foreach ($prepParams as $key => $value) {
					$params[$key] = $this->safeGetParam($key, $value);
				}
			}
			//$this->utils->debug_log($params);

			return $params;
		}

		protected function getReadOnlyDB() {
			$this->load->model('users');
			return $this->users->getReadOnlyDB();
		}

		protected function getSecondReadDB() {
			$this->load->model('users');
			return $this->users->getSecondReadDB();
		}

		// protected function isDefaultOpenSearchPanel() {
		// 	return $this->config->item('default_open_search_panel');
		// }

		protected function getSessionAffId() {
			return $this->session->userdata('affiliateId');
		}

		public function getAdminToken($adminUserId) {
			$this->load->model(array('common_token'));
			$token = $this->common_token->getAdminUserToken($adminUserId);
			return $token;
		}

		public function getAdminTokenByCurrency($adminUserId, $currency){
            $token = $this->getAdminToken($adminUserId);
            if($this->utils->isEnabledMDB() && !$this->utils->isActiveCurrency($currency)){
                //if currency is not same with active currency
                $this->load->model(array('common_token'));
                //load currency
                $sourceDB=strtolower($currency);
                $this->common_token->runAnyOnSingleMDBWithTrans($sourceDB,
                        function($db, &$result) use($adminUserId){
                    $token = $this->common_token->getAdminUserToken($adminUserId, $db);
                    $success=!empty($token);
                    if($success){
                        $result=$token;
                    }else{
                    	$result=null;
                    }
                    return $success;
                }, $token);
            }
			return $token;
		}

		public function validateAdminToken($adminToken, $requireFunc) {
			$this->load->model(array('common_token', 'roles'));
			$adminUserId = $this->common_token->getAdminUserIdByToken($adminToken);
			if ($adminUserId) {
				$functions = $this->roles->getFunctionsByUserId($adminUserId);
				return in_array($requireFunc, $functions);
			}
			return false;
		}

		public function startEvent($title, $desc) {
			$this->utils->startEvent($title, $desc);
		}
		public function endEvent($title) {
			$this->utils->endEvent($title);
		}

		public function loadSubmitGameTreeWithNumber($showGameTree) {
			$gameNumberList = [];
			list($gamePlatformList, $gameTypeList, $gameDescList) = $this->loadSubmitGameTree();
			if (!$showGameTree) {
				if (!empty($gameTypeList)) {
					$mapGameType = array();
					$typeIds = array();
					foreach ($gameTypeList as $gameTypeInfo) {
						$mapGameType[$gameTypeInfo['id']] = $gameTypeInfo;
						$typeIds[] = $gameTypeInfo['id'];
					}
					$gameDescList = $this->game_description_model->getGameDescInfoListByGameTypes($typeIds);
					foreach ($gameDescList as $gameDescId) {
						$gameNumberList[] = array('id' => $gameDescId['id'],
							'game_type_id' => $gameDescId['game_type_id'],
							'game_platform_id' => $gameDescId['game_platform_id'],
							'game_desc_number' => $mapGameType[$gameDescId['id']]['game_type_number'],
							'game_type_number' => $mapGameType[$gameDescId['id']]['game_type_number'],
							'game_platform_number' => $mapGameType[$gameDescId['id']]['game_platform_number'],
						);
					}
				}
			} else {
				if (!empty($gameDescList)) {
					$gameNumberList = $gameDescList;
				}
			}
			return $gameNumberList;
		}

		public function loadSubmitGameTree() {
			$gamePlatformList = array();
			$gameTypeList = array();
			$gameDescList = array();
			$selectedIds = $this->input->post('selected_game_tree');
			//$this->utils->debug_log('SELECTED_GAME_TREE', $selectedIds);
			$idArr = explode(',', $selectedIds);
			$this->utils->debug_log('idArr', count($idArr), 'selected_game_tree', $selectedIds);
			foreach ($idArr as $id) {
				//extract id
				$arr = explode('_', $id);
				//$this->utils->debug_log('arr', $arr);
				$gamePlatformId = null;
				$gameTypeId = null;
				$gameDescId = null;
				if (count($arr) >= 2) {
					$gamePlatformId = $arr[1];
					if (count($arr) >= 4) {
						$gameTypeId = $arr[3];
						if (count($arr) >= 6) {
							$gameDescId = $arr[5];
						}
					}
				}
				if (!empty($gameDescId)) {
					$gameDescList[$gameDescId] = array('id' => $gameDescId,
						'game_type_id' => $gameTypeId,
						'game_platform_id' => $gamePlatformId,
						'game_desc_number' => $this->input->post('per_' . $id),
						'game_type_number' => $this->input->post('per_gp_' . $gamePlatformId . '_gt_' . $gameTypeId),
						'game_platform_number' => $this->input->post('per_gp_' . $gamePlatformId));
				} elseif (!empty($gameTypeId)) {
					$gameTypeList[$gameTypeId] = array('id' => $gameTypeId,
						'game_platform_id' => $gamePlatformId,
						'game_type_number' => $this->input->post('per_' . $id),
						'game_platform_number' => $this->input->post('per_gp_' . $gamePlatformId),
					);
				} elseif (!empty($gamePlatformId)) {
					$gamePlatformList[$gamePlatformId] = array('id' => $gamePlatformId,
						'game_platform_number' => $this->input->post('per_' . $id));
				}
			}
			$this->utils->debug_log('gamePlatformList', count($gamePlatformList), 'gameTypeList', count($gameTypeList), 'gameDescList', count($gameDescList));

			return array($gamePlatformList, $gameTypeList, $gameDescList);
		}

		public function getParametersByStarts($starts) {
			$fields = $this->input->post();
			$params = [];
			foreach ($fields as $fldName => $fldValue) {
				if ($this->utils->startsWith($fldName, $starts)) {
					$params[$fldName] = $fldValue;
				}
			}
			return $params;
		}

		/**
		 *
		 *	percentage is 0 : inherit
		 *	missing row : disabled
		 *
		 * @return array
		 */
		public function processSubmitGameTreeWithNumber() {
			$gamePlatformList = array();
			$gameTypeList = array();
			$gameDescList = array();

			// $fields=$this->getParametersByStarts('per_gp_');

			$selectedIds = $this->input->post('selected_game_tree');
			//$this->utils->debug_log('SELECTED_GAME_TREE', $selectedIds);
			$idArr = explode(',', $selectedIds);
			$this->utils->debug_log('===================idArr', count($idArr), 'selected_game_tree', $selectedIds);
			//validate
			if(!$this->validateGameTreeInput($idArr)){
				return [null, null, null];
			}

			foreach ($idArr as $id) {
				$arr = explode('_', $id);
				//$this->utils->debug_log('arr', $arr);
				$gamePlatformId = null;
				$gameTypeId = null;
				$gameDescId = null;

				if (count($arr) >= 6) {

					$gameDescId = $arr[5];
					$gameDescList[$gameDescId] = floatval($this->input->post('per_' . $id));
					$gameTypeId = $arr[3];
					$gamePlatformId = $arr[1];
					if (!isset($gameTypeList[$gameTypeId])) {
						$gameTypeList[$gameTypeId] = floatval($this->input->post('per_gp_' . $gamePlatformId . '_gt_' . $gameTypeId));
					}
					if (!isset($gamePlatformList[$gamePlatformId])) {
						$gamePlatformList[$gamePlatformId] = floatval($this->input->post('per_gp_' . $gamePlatformId));
					}

				} elseif (count($arr) >= 4) {

					$gameTypeId = $arr[3];
					$gameTypeList[$gameTypeId] = floatval($this->input->post('per_' . $id));
					$gamePlatformId = $arr[1];
					if (!isset($gamePlatformList[$gamePlatformId])) {
						$gamePlatformList[$gamePlatformId] = floatval($this->input->post('per_gp_' . $gamePlatformId));
					}

				} elseif (count($arr) >= 2) {

					$gamePlatformId = $arr[1];
					$gamePlatformList[$gamePlatformId] = floatval($this->input->post('per_' . $id));

				}
			}

			return array($gamePlatformList, $gameTypeList, $gameDescList);
		}

		public function validateGameTreeInput($idArr){
			$result=false;
			$selected_game_tree_count=intval($this->input->post('selected_game_tree_count'));

			if($selected_game_tree_count == count($idArr)){
				$result=true;
			}else{
				$this->utils->error_log('validateGameTreeInput failed', $selected_game_tree_count, count($idArr));
			}
			return $result;
		}

		public function addJsTreeToTemplate() {
			$this->template->add_css('resources/third_party/jstree/themes/default/style.min.css');
			$this->template->add_js('resources/third_party/jstree/jstree.min.js');
			$this->template->add_js('resources/third_party/jstree/jstree_plugin_input_number.js');
			$this->template->add_js('resources/third_party/jstree/jstree_table.js');
		}

		public function addBoxDialogToTemplate() {
			$this->template->add_css('resources/third_party/bootstrap-dialog/css/bootstrap-dialog.min.css');
			$this->template->add_js('resources/third_party/bootstrap-dialog/js/bootstrap-dialog.min.js');
			// $this->template->add_css('resources/third_party/lobibox/css/lobibox.min.css');
			// $this->template->add_js('resources/third_party/lobibox/js/lobibox.min.js');
		}

		public function loadDefaultTemplate($jsArr, $cssArr, $vars, $sidebar, $main_content, $data = null, $render = false) {

			if (!empty($jsArr)) {
				foreach ($jsArr as $jsUri) {
					$this->template->add_js($jsUri);
				}
			}

			if (!empty($cssArr)) {
				foreach ($cssArr as $cssUri) {
					$this->template->add_css($cssUri);
				}
			}

			if (!empty($sidebar)) {
				$this->template->write_view('sidebar', $sidebar);
			}

			if (!empty($vars)) {
				foreach ($vars as $k => $v) {
					$this->template->write($k, $v);
				}
			}

			if (!empty($main_content)) {
				$this->template->write_view('main_content', $main_content, $data);
			}

			if (!empty($render)) {
				$this->template->render();
			}

		}

		public function showErrorAccess($title, $sidebar, $activenav) {
			$message = lang('con.usm01');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			$this->loadDefaultTemplate(null, null, array('title' => $title), $sidebar, null, null, true);
		}

		public function showErrorAccessJS($title) {
			$this->returnJS($title);
		}

		public function showErrorAccessJson($title) {
			$this->returnJsonResult($title);
		}

		public function loadThirdPartyToTemplate($thirdParty, $tmpl = null) {
			if (empty($tmpl)) {
				$tmpl = $this->template;
			}
			if ($thirdParty == 'datatables') {
				$tmpl->add_css($this->utils->thirdpartyUrl('datatables/datatables.min.css'));
				$tmpl->add_js($this->utils->thirdpartyUrl('datatables/datatables.min.js'));
			}
		}

		public function filterInput($fields, $method = 'post') {
			$inputFields = $this->input->post();
			if ($method == 'get') {
				$inputFields = $this->input->get();
			}

			$fldKV = array();

			foreach ($fields as $key => $fldType) {
				$val = isset($inputFields[$key]) ? $inputFields[$key] : null;
				if ($fldType == 'int') {
					$val = intval($val);
				} else if ($fldType == 'double') {
					$val = doubleval($val);
				} else if ($fldType == 'bool') {
					$val = $val == 'true' || $val == 'on';
				} else if ($fldType == 'array_int') {
					if (!is_array($val)) {
						$val = array(intval($val));
					} else {
						foreach ($val as &$v) {
							$v = intval($v);
						}
					}
				} else if ($fldType == 'array_double') {
					if (!is_array($val)) {
						$val = array(doubleval($val));
					} else {
						foreach ($val as &$v) {
							$v = doubleval($v);
						}
					}
				}
				$fldKV[$key] = $val;
			}

			return $fldKV;
		}

		public function emptyOrArray($arr) {
			if (empty($arr)) {
				return [];
			}
			return $arr;
		}

		public function getUploadPath() {
			return $this->utils->getUploadPath();
		}

		public function isValidApiKey($api_key) {
			$key = $this->utils->getConfig('pub_api_key');
			return !empty($api_key) && !empty($key) && $api_key == $key;
		}

		public function isValidPlayerToken($playerToken, $playerId){

			$this->load->model(['common_token']);
			return $this->common_token->isTokenValid($playerId, $playerToken);

		}

		public function getValidationLang() {
			$lang_arr = [];
			$lang_key = [
				'errorTitle', 'requiredFields', 'badTime', 'badEmail', 'badTelephone', 'badSecurityAnswer', 'badDate', 'lengthBadStart', 'lengthBadEnd',
				'lengthTooLongStart', 'lengthTooShortStart', 'notConfirmed', 'badDomain', 'badUrl', 'badCustomVal', 'andSpaces', 'badInt', 'badSecurityNumber',
				'badUKVatAnswer', 'badStrength', 'badNumberOfSelectedOptionsStart', 'badNumberOfSelectedOptionsEnd', 'badAlphaNumeric', 'badAlphaNumericExtra',
				'wrongFileSize', 'wrongFileType', 'groupCheckedRangeStart', 'groupCheckedTooFewStart', 'groupCheckedTooManyStart', 'groupCheckedEnd', 'badCreditCard',
				'badCVV', 'wrongFileDim', 'imageTooTall', 'imageTooWide', 'imageTooSmall', 'min', 'max', 'imageRatioNotAccepted',
			];
			//genereate validation lang by current session
			foreach ($lang_key as $key) {
				$lang_arr[$key] = lang('validation.' . $key);
			}

			return $lang_arr;
		}

		public function appendFormValidationJs($template) {
			//load language
			$lang_arr = $this->getValidationLang();
			$lang_str = json_encode($lang_arr);
			$js = <<<EOD
var validation_language={$lang_str};
EOD;

			$template->add_js($js, 'embed');
			$template->add_js('resources/third_party/bower_components/jquery-form-validator/form-validator/jquery.form-validator.min.js');

		}

		public function getContactPermissions() {

			$this->load->library(['permissions']);

			$permissions = [
				'player_contact_information_email' => $this->permissions->checkPermissions('player_basic_info') && $this->permissions->checkPermissions('player_contact_information_email'),
				'player_contact_information_contact_number' => $this->permissions->checkPermissions('player_basic_info') && $this->permissions->checkPermissions('player_contact_information_contact_number'),
				'player_contact_information_im_accounts' => $this->permissions->checkPermissions('player_basic_info') && $this->permissions->checkPermissions('player_contact_information_im_accounts'),
				'view_player_detail_contactinfo_em' => $this->permissions->checkPermissions('view_player_detail_contactinfo_em'),
				'view_player_detail_contactinfo_im' => $this->permissions->checkPermissions('view_player_detail_contactinfo_im'),
				'view_player_detail_contactinfo_cn' => $this->permissions->checkPermissions('view_player_detail_contactinfo_cn'),
				'verify_player_email' => $this->permissions->checkPermissions('verify_player_email'),
				'verify_player_contact_number' => $this->permissions->checkPermissions('verify_player_contact_number'),
				'player_basic_info' => $this->permissions->checkPermissions('player_basic_info'),
				'telesales_call' => $this->permissions->checkPermissions('telesales_call'),
				'reset_player_login_password' => $this->permissions->checkPermissions('reset_player_login_password'),
				'player_verification_question' => $this->permissions->checkPermissions('player_verification_question'),
				'player_verification_questions_answer' => $this->permissions->checkPermissions('player_verification_questions_answer'),
			];

			return $permissions;
		}

		public function setTrackingCodeToSession($tracking_code) {
			if (!empty($tracking_code)) {
				$this->session->set_userdata('tracking_code', $tracking_code);
				$this->load->helper('cookie');
				set_cookie('_og_tracking_code', $tracking_code, 86400 * 30);

				return true;
			}

			return false;
		}

		public function isLoggedAdminUser() {
			if (!isset($this->authentication)) {
				$this->load->library(array('authentication'));
			}
			return !empty($this->authentication->getUserId());
		}

		public function setLanguageBySubDomain() {
			if ($this->utils->getConfig('set_language_by_subdomain')) {
				$subdomain = explode('.', $this->utils->getSystemHost('player'));
				$subdomainInitial = $subdomain[1];
				switch ($subdomainInitial) {
				case 'cn':
					$this->language_function->setCurrentLanguage(self::LANG_CN);
					$this->lang->is_loaded = array();
					$this->lang->language = array();
					$this->lang->load('main', 'chinese');
					break;

				case 'id':
					$this->language_function->setCurrentLanguage(self::LANG_ID);
					$this->lang->is_loaded = array();
					$this->lang->language = array();
					$this->lang->load('main', 'indonesian');
					break;

				case 'vn':
					$this->language_function->setCurrentLanguage(self::LANG_VN);
					$this->lang->is_loaded = array();
					$this->lang->language = array();
					$this->lang->load('main', 'vietnamese');
					break;
				}
			}
		}

		public function setLanguageBySubDomainPub() {
			$subdomain = explode('.', $this->utils->getSystemHost('player'));
			$subdomainInitial = $subdomain[1];
			switch ($subdomainInitial) {
			case 'cn':
				return 'chinese';
				break;

			case 'id':
				return 'indonesian';
				break;

			case 'vn':
				return 'vietnamese';
				break;

			default:
				return 'english';
				break;
			}
		}

		public function isLoggedAgency(&$agent_id = null, &$agent_name = null) {

			$agent_id = $this->session->userdata('agent_id');
			$agent_name = $this->session->userdata('agent_name');

			return !empty($agent_id) && !empty($agent_name);
		}

		public function clearSmsTime() {

			$this->session->unset_userdata('last_sms_time');

		}

		/**
		 * Convert all balances to the specified wallet ($transfer_to)
		 *
		 * @param int $player_id
		 * @param string $playerName
		 * @param int $transfer_to
		 * @param int $user_id
		 * @param [type] $walletType
		 * @param [type] $originTransferAmount
		 * @param boolean $ignore_promotion_check
		 * @return array
		 */
		protected function _transferAllWallet($player_id, $playerName, $transfer_to,
			$user_id = null, $walletType = null, $originTransferAmount = null, $ignore_promotion_check = false, $ignoreSingleWalletSwitch = false) {

			$result = [
				'success' => false,
				'message' => 'Single-wallet switch no enabled',
				'wallets' => [],
			];

			$this->load->model('player_preference');

			// OGP-2700:
			//   Required: feature 'enabled_single_wallet_switch',	override: $ignoreSingleWalletSwitch
			//   Required: player_preference 'auto_transfer',		override: feature 'enable_player_prefs_auto_transfer'
			//
			//   TRUTH TABLE
			//   ignoreSingleWalletSwitch			1	1	0	0
			//   enabled_single_wallet_switch		1	0	1	0
			//   result_1							1	1	1	0
			//   ==========
			//   !enable_player_prefs_auto_transfer	1	1	0	0	(Defaults to true when player pref disabled)
			//   auto_transfer						1	0	1	0
			//   result_2							1	1	1	0
			//   ==========
			//   result_1 && result_2				1	1	1	0
			//
			if (
					( $ignoreSingleWalletSwitch || $this->utils->isAllowedAutoTransferOnFeature() )
					&&
					( !$this->utils->isEnabledFeature('enable_player_prefs_auto_transfer') || $this->player_preference->isAutoTransferOnGameLaunch($player_id) )
			   ) {
				$result = $this->utils->transferAllWallet($player_id, $playerName, $transfer_to, $user_id, $walletType, $originTransferAmount, $ignore_promotion_check);

				if (FALSE === $result || (!isset($result['success']) && !$result['success'])) {
					$this->utils->error_log('transferAllWallet', $result);
				}
			}
			else if ( !$this->player_preference->isAutoTransferOnGameLaunch($player_id) ) {
				$result['message'] = 'Auto_transfer disabled in player preference';
			}

			$this->utils->debug_log('BaseController::_transferAllWallet', $result['message']);

			return $result;
		}

		protected function _transferBackToMainWallet($player_id, $playerName){

			if($this->utils->isEnabledFeature('enabled_single_wallet_switch')
				|| $this->utils->isEnabledFeature('always_auto_transfer_if_only_one_game')){

				return $this->utils->transferAllSubWalletToMain($player_id, $playerName);

			}

			return true;

		}

		/**
		 * set export management report
		 *
		 * @param   $exportType str
		 * @param   $managementType str
		 * @return  redirect
		 */
		public function setExportReportManagementType($exportType, $managementType) {
			$data = array(
				'username' => $this->authentication->getUsername(),
				'management' => $managementType,
				'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
				'action' => 'Exported CSM Promo List',
				'description' => "User " . $this->authentication->getUsername() . " exported: " . $exportType,
				'logDate' => $this->utils->getNowForMysql(),
				'status' => self::FALSE,
			);

			$this->report_functions->recordAction($data);
			$this->load->model(array('cms', 'shopping_center'));

			$d = new DateTime();
			$result = $this->shopping_center->getExcelReport();
			$this->utils->create_excel($result, $exportType . '_' . $d->format('Y_m_d_H_i_s') . '_' . rand(1, 999));
		}

		/**
		 * common export report
		 *
		 * @param   $type str
		 * @return  redirect
		 */
		public function commonExportToExcel($type) {
			switch ($type) {
			case 'shopmanageritemlist':
				$this->setExportReportManagementType($type, "Marketing Management");
				break;

			default:
				# code...
				break;
			}
		}

		// public function runAsyncCommandLine($func, $paramArr=[]){

		// 	$cmd= dirname(__FILE__).'/../../shell/noroot_command.sh';
		// 	raw_debug_log($cmd);

		// 	$token=random_string('uuid');
		// 	//convert to realpath
		// 	$cmd='nohup /bin/bash '.realpath($cmd).' '.$func;
		// 	foreach ($paramArr as $param) {
		// 		if(!empty($param)){
		// 			$cmd.=' "'.$param.'" ';
		// 		}
		// 	}

		// 	$cmd.=' 2>&1 > /tmp/job_'
		// 		.$func.'_'.$token.'.log &';

		// 	raw_debug_log('full cmd', $cmd);

  //           raw_debug_log(pclose(popen($cmd, 'r')));

		// 	return $cmd;

		// }

		//==Agent tracking code===========================================
		public function setAgentTrackingCodeToSession($tracking_code) {

			return $this->utils->setAgentTrackingCodeToSession($tracking_code);

		}

		public function getAgentTrackingCodeFromSession() {
			return $this->utils->getAgentTrackingCodeFromSession();
		}

		public function clearTrackingCode(){
			$this->load->helper('cookie');
			$this->session->unset_userdata('tracking_code');
			delete_cookie('_og_tracking_code');
		}

		/**
		 * tracking_code.og.local
		 * www.og.local/aff/tracking_code
		 * www.og.local/aff.html?aff=tracking_code or www.og.local/aff.html?code=tracking_code
		 */
		public function getAgentTrackingCode() {

			$tracking_code_priority_levels = $this->utils->getConfig('tracking_code_priority_levels');
			$trackingCode = null;
			$trackingCodeInRef = $this->getAgentTrackingCodeByRef();
			$trackingCodeInDomain = $this->getAgentTrackingCodeFromDomain();
			$trackingCodeInSession = $this->getAgentTrackingCodeFromSession(); // $this->session->userdata('tracking_code');
			$this->utils->debug_log('agent tracking_code on ' . current_url(), $trackingCodeInRef, $trackingCodeInDomain, $trackingCodeInSession);

			foreach ($tracking_code_priority_levels as $tracking_code_priority_level) {

				switch ($tracking_code_priority_level) {

				case 'trackingCodeInRef':
					$trackingCode = $trackingCodeInRef;
					break;

				case 'trackingCodeInDomain':
					$trackingCode = $trackingCodeInDomain;
					break;

				case 'trackingCodeInSession':
					$trackingCode = $trackingCodeInSession; // $this->session->userdata('tracking_code');
					// $trackingCode = $this->session->userdata('tracking_code');
					break;

				}

				if (!empty($trackingCode)) {
					break;
				}

			}

			return !empty($trackingCode) ? $trackingCode : '';
		}

		protected function getAgentTrackingCodeByRef() {
			$code = null;
			$http_referer = '';
			if (isset($_SERVER['HTTP_REFERER'])) {
				$http_referer = @$_SERVER['HTTP_REFERER'];
				if (empty($http_referer)) {
					$http_referer = '';
				}
			}

			$url = $http_referer;
			if (!empty($url)) {
				$query_str = parse_url($url, PHP_URL_QUERY);
				parse_str($query_str, $query_params);
				if (!$query_params) {
					$seg = explode("/", $url);
					if (isset($seg[3]) && isset($seg[4]) && (@$seg[3] == 'agent' || @$seg[3] == 'ag')) {
						$code = @$seg[4];
					}
				} else {
					if (isset($query_params['agent'])) {
						$code = $query_params['agent'];
					}
					if (isset($query_params['ag']) && empty($code)) {
						$code = $query_params['ag'];
					}
					if (isset($query_params['agcode']) && empty($code)) {
						$code = $query_params['agcode'];
					}
				}
			}

			return $code;
		}

		protected function getAgentTrackingCodeFromDomain() {
			$code = null;
			// $this->utils->debug_log('aff domain', @$_SERVER['HTTP_REFERER']);
			$http_referer = '';
			if (isset($_SERVER['HTTP_REFERER'])) {
				$http_referer = @$_SERVER['HTTP_REFERER'];
				if (empty($http_referer)) {
					//use current domain
					$http_referer = @$_SERVER['HTTP_HOST'];
					if (!empty($http_referer)) {
						$http_referer = 'http://' . $http_referer;
					}
				}
			}

			if (!empty($http_referer)) {
				$agent_domain = parse_url($http_referer, PHP_URL_HOST);
				if (!empty($agent_domain)) {
					$this->utils->debug_log('agent_domain', $agent_domain);

					// $agent_domain = $this->getMainDomain();
					$this->load->model(['agency_model']);
					$code = $this->agency_model->get_tracking_code_from_agent_domain($agent_domain);

					$this->utils->debug_log('agent code '.$code.' from domain:'.$agent_domain);
				}
			}

			if (empty($code)) {
				$http_host = @$_SERVER['HTTP_HOST'];
				$this->utils->debug_log('try current domain', $http_host);

				$this->load->model('agency_model');
				$code = $this->agency_model->get_tracking_code_from_agent_domain($http_host);
				$this->utils->debug_log('agent code '.$code.' from urrent domain:'.$http_host);
			}
			return $code;
		}

		protected function getAgentTrackingSourceCodeByRef() {
			$code = null;
			$http_referer = '';
			if (isset($_SERVER['HTTP_REFERER'])) {
				$http_referer = @$_SERVER['HTTP_REFERER'];
				if (empty($http_referer)) {
					$http_referer = '';
				}
			}

			$url = $http_referer;
			if (!empty($url)) {
				$query_str = parse_url($url, PHP_URL_QUERY);
				parse_str($query_str, $query_params);
				if (!$query_params) {
					$seg = explode("/", $url);
					if (isset($seg[3]) && isset($seg[4]) && @$seg[3] == 'ag') {
						$code = isset($seg[5]) ? @$seg[5] : null;
					}
				} else {
					if (isset($query_params['ags'])) {
						$code = $query_params['ags'];
					}
					if (isset($query_params['agent_source'])) {
						$code = $query_params['agent_source'];
					}
				}
			}

			return $code;
		}

		public function getAgentTrackingSourceCode() {
			$trackingSourceCode = false;
			$trackingSourceCodeInRef = $this->getAgentTrackingSourceCodeByRef();
			// $trackingSourceCodeInDomain = $this->getTrackingSourceCodeByDomain();
			$trackingSourceCodeInSession = $this->session->userdata('agent_tracking_source_code');
			$this->utils->debug_log('agent_tracking_source_code', $trackingSourceCodeInRef, $trackingSourceCodeInSession);
			// domain > ref > session
			// if ($trackingCodeInDomain) {
			// 	$trackingCode = $trackingCodeInDomain;
			// } else
			if ($trackingSourceCodeInRef) {
				$trackingSourceCode = $trackingSourceCodeInRef;
			} else if ($trackingSourceCodeInSession) {
				$trackingSourceCode = $trackingSourceCodeInSession;
			}
			if ($trackingSourceCode == false) {$trackingSourceCode = '';}
			return $trackingSourceCode;
		}

		public function generateDoubleSubmitToken($player_id){

			return $this->_app_prefix.'-'.$this->double_submit_session_name.'-'.$player_id;

		}

		/**
		 *
		 * @param  string $player_id
		 * @return bool true means no error
		 */
		public function verifyAndResetDoubleSubmit($player_id){

			if($this->double_submit_protection && !empty($player_id)){
				//check session
				$post_token=$this->input->post($this->double_submit_post_token_name);
				$session_token=null;
				$rlt=$this->localLockOnlyWithResult($this->double_submit_post_token_name, $player_id, function(&$error)
				use(&$session_token, $post_token, $player_id){

					$session_token=$this->getDoubleSubmitToken($player_id);
					$token=null;
					$success=$this->resetDoubleSumitToken($player_id, $token);

					$this->utils->debug_log('writeLockRedis', $token, $success, $post_token, $session_token);

					return $success && $post_token==$session_token;
				});

				// $success=$post_token==$session_token;

				// $token=random_string('sha1');
				// $key=$this->generateDoubleSubmitToken();
				// $try_lock=$this->local_web_redis->writeLockRedis($key, $token, $this->utils->getConfig('double_submit_token_timeout'));

				// $this->utils->debug_log('writeLockRedis', $key, $token, $success, $try_lock, $post_token, $session_token);
				// $this->session->set_userdata($this->double_submit_session_name, random_string('sha1'));
				return $rlt['success'];
			}

			return true;

		}

		public function resetDoubleSumitToken($player_id, &$token){
			$key=$this->generateDoubleSubmitToken($player_id);
			$token=random_string('sha1');

			$local_lock_server=$this->utils->getLocalLockServer();
			return $local_lock_server->writeRedis($key, $token, $this->utils->getConfig('double_submit_token_timeout'));

		}

		public function initDoubleSubmitAndReturnHiddenField($player_id, &$token=null){
			if($this->double_submit_protection){

				$token=null;
				$success=$this->resetDoubleSumitToken($player_id, $token);

				// $token=random_string('sha1');
				// $key=$this->generateDoubleSubmitToken();
				// $this->local_web_redis->writeRedis($key, $token, $this->utils->getConfig('double_submit_token_timeout'));
				// $this->session->set_userdata($this->double_submit_session_name, $token);

				return '<input type="hidden" id="'.$this->double_submit_post_token_name.'" name="'.$this->double_submit_post_token_name.'" value="'.$token.'">';
			}

			return null;
		}

		public function getDoubleSubmitToken($player_id){
			$local_lock_server=$this->utils->getLocalLockServer();
			return $local_lock_server->readRedis($this->generateDoubleSubmitToken($player_id));
		}

		//====check double sumit for admin=============================================
		public function generateDoubleSubmitTokenForAdmin($user_id){

			return $this->_app_prefix.'-'.$this->double_submit_session_name.'-'.$user_id;

		}

		public function resetDoubleSumitTokenForAdmin($user_id, &$token){
			$key=$this->generateDoubleSubmitTokenForAdmin($user_id);
			$token=random_string('sha1');

			$local_lock_server=$this->utils->getLocalLockServer();
			return $local_lock_server->writeRedis($key, $token, $this->utils->getConfig('double_submit_token_timeout'));

		}

		public function initDoubleSubmitAndReturnHiddenFieldForAdmin($user_id, &$token=null){
			if($this->double_submit_protection){

				$token=null;
				$success=$this->resetDoubleSumitTokenForAdmin($user_id, $token);

				return '<input type="hidden" id="'.$this->double_submit_post_token_name.'" name="'.$this->double_submit_post_token_name.'" value="'.$token.'">';
			}

			return null;
		}

		public function getDoubleSubmitTokenForAdmin($user_id){
			$local_lock_server=$this->utils->getLocalLockServer();
			return $local_lock_server->readRedis($this->generateDoubleSubmitTokenForAdmin($user_id));
		}

		public function verifyAndResetDoubleSubmitForAdmin($user_id){

			if($this->double_submit_protection && !empty($user_id)){
				//check session
				$post_token=$this->input->post($this->double_submit_post_token_name);
				$session_token=null;
				$rlt=$this->localLockOnlyWithResult($this->double_submit_post_token_name, $user_id, function(&$error)
				use(&$session_token, $post_token, $user_id){

					$session_token=$this->getDoubleSubmitTokenForAdmin($user_id);
					$token=null;
					$success=$this->resetDoubleSumitTokenForAdmin($user_id, $token);

					$this->utils->debug_log('writeLockRedis', $token, $success, $post_token, $session_token);

					return $success && $post_token==$session_token;
				});

				return $rlt['success'];
			}

			return true;

		}

		public function checkPermission($permissionName){
			if (!$this->permissions->checkPermissions($permissionName)) {
				$this->error_access();
			}
		}

		public function getKeyForRefreshBalanceCacheFlag($playerId, $apiId=null){
			$key='_refresh_balance_'.$playerId;
			if(!empty($apiId)){
				$key.='_'.$apiId;
			}

			return $key;
		}

		public function getRefreshBalanceCacheFlag($playerId, $apiId=null){

			$key=$this->getKeyForRefreshBalanceCacheFlag($playerId, $apiId);

			$arr=$this->utils->getJsonFromCache($key);

			if($arr===false){
				$arr=['cached'=>true];
				$ttl=3;
				//save flag
				$this->utils->saveJsonToCache($key, $arr, $ttl);
			}


			return $arr;
		}

		public function saveUploadFileToRemote($uploadFieldName, $types, &$filepath, &$message){

			$uploadCsvFilepath=$this->utils->getSharingUploadPath('/upload_temp_csv');

			if(!is_writable($uploadCsvFilepath)){
				//error
				$message=lang('Upload dir failed');
		        return false;
			}

			if($this->isValidUploadFileType($uploadFieldName, $types)){
				$dt=new DateTime();
				$config=[
		             'upload_path' => $uploadCsvFilepath,
		             'allowed_types' => '*',
		             'file_name' => $uploadFieldName.'_'.$dt->format('YmdHis').'_'.random_string().'.csv',
		             'overwrite' => true,
		             'max_size' => $this->utils->getConfig('max_upload_size_byte'),
		        ];
		        $this->load->library('upload', $config);
		        //update config
		        $this->upload->initialize($config);

		        if ($this->upload->do_upload($uploadFieldName)) {
		        	//check
		        	$filepath=$uploadCsvFilepath.'/'.$this->upload->file_name;
		        	return true;
		        }else{
					$message=lang('Upload CSV failed').' '.$uploadFieldName;
			        return false;
		        }
			}else{
				$message=lang('Invalid file type on').' '.$uploadFieldName;
				return false;
			}
		}

		public function isValidUploadFileType($uploadFieldName, $types){
			$filename=@$_FILES[$uploadFieldName]['name'];
			$file_ext=pathinfo($filename, PATHINFO_EXTENSION);

			return in_array($file_ext, $types);
		}

		public function existsUploadField($uploadFieldName){
			return !empty($_FILES[$uploadFieldName]) && isset($_FILES[$uploadFieldName]['size']) && $_FILES[$uploadFieldName]['size']>0;
		}

		//===mdb==========================================================
	    protected function syncAgentCurrentToMDB($agent_id, $insertOnly=false, &$rlt=null){
	    	if(!$this->utils->isEnabledMDB()){
	    		return true;
	    	}

	        $this->load->model(['multiple_db_model']);
	        $rlt=$this->multiple_db_model->syncAgencyFromCurrentToOtherMDB($agent_id, $insertOnly);
	        $this->utils->debug_log('syncAgencyFromCurrentToOtherMDB :'.$agent_id, $rlt);
	        $success=false;
	        if(!empty($rlt)){
	            foreach ($rlt as $key => $dbRlt) {
	                $success=$dbRlt['success'];
	                if(!$success){
	                    break;
	                }
	            }
	        }
	        return $success;
	    }

	    protected function syncAffCurrentToMDB($affId, $insertOnly=false, &$rlt=null){
	    	if(!$this->utils->isEnabledMDB()){
	    		return true;
	    	}

	        $this->load->model(['multiple_db_model']);
	        $rlt=$this->multiple_db_model->syncAffFromCurrentToOtherMDB($affId, $insertOnly);
	        $this->utils->debug_log('syncAffFromCurrentToOtherMDB :'.$affId, $rlt);
	        $success=false;
	        if(!empty($rlt)){
	            foreach ($rlt as $key => $dbRlt) {
	                $success=$dbRlt['success'];
	                if(!$success){
	                    break;
	                }
	            }
	        }
	        return $success;
	    }

	    protected function syncUserCurrentToMDB($userId, $insertOnly=false, &$rlt=null){
	    	if(!$this->utils->isEnabledMDB()){
	    		return true;
	    	}

	        $this->load->model(['multiple_db_model']);
	        $rlt=$this->multiple_db_model->syncUserFromCurrentToOtherMDB($userId, $insertOnly);
	        $this->utils->debug_log('syncUserFromCurrentToOtherMDB :'.$userId, $rlt);
	        $success=false;
	        if(!empty($rlt)){
	            foreach ($rlt as $key => $dbRlt) {
	                $success=$dbRlt['success'];
	                if(!$success){
	                    break;
	                }
	            }
	        }
	        return $success;
	    }

	    protected function syncRoleCurrentToMDB($roleId, $insertOnly=false, &$rlt=null){
	    	if(!$this->utils->isEnabledMDB()){
	    		return true;
	    	}

	        $this->load->model(['multiple_db_model']);
	        $rlt=$this->multiple_db_model->syncRoleFromCurrentToOtherMDB($roleId, $insertOnly);
	        $this->utils->debug_log('syncRoleFromCurrentToOtherMDB :'.$roleId, $rlt);
	        $success=false;
	        if(!empty($rlt)){
	            foreach ($rlt as $key => $dbRlt) {
	                $success=$dbRlt['success'];
	                if(!$success){
	                    break;
	                }
	            }
	        }
	        return $success;
	    }

	    protected function syncPlayerCurrentToMDB($playerId, $insertOnly=false, &$rlt=null){
	    	if(!$this->utils->isEnabledMDB()){
	    		return true;
	    	}

	        $this->load->model(['multiple_db_model']);
	        $rlt=$this->multiple_db_model->syncPlayerFromCurrentToOtherMDB($playerId, $insertOnly);
	        $this->utils->debug_log('syncPlayerFromCurrentToOtherMDB :'.$playerId, $rlt);
	        $success=false;
	        if(!empty($rlt)){
	            foreach ($rlt as $key => $dbRlt) {
	                $success=$dbRlt['success'];
	                if(!$success){
	                    break;
	                }
	            }
	        }
	        return $success;
	    }

	    protected function syncPlayerRegSettingsCurrentToMDB($type, &$rlt=null){
	    	if(!$this->utils->isEnabledMDB()){
	    		return true;
	    	}

	        $this->load->model(['multiple_db_model', 'marketing']);
	        $rlt=$this->multiple_db_model->syncPlayerRegSettingsFromCurrentToOtherMDB($type);
	        $this->utils->debug_log('syncPlayerRegSettingsFromCurrentToOtherMDB :'.$type, $rlt);
	        $success=false;
	        if(!empty($rlt)){
	            foreach ($rlt as $key => $dbRlt) {
	                $success=$dbRlt['success'];
	                if(!$success){
	                    break;
	                }
	            }
	        }
	        return $success;
	    }
	    protected function syncAffiliateRegSettingsCurrentToMDB($type, &$rlt=null){
	    	if(!$this->utils->isEnabledMDB()){
	    		return true;
	    	}

	        $this->load->model(['multiple_db_model', 'marketing']);
	        $rlt=$this->multiple_db_model->syncAffiliateRegSettingsFromCurrentToOtherMDB($type);
	        $this->utils->debug_log('syncAffiliateRegSettingsFromCurrentToOtherMDB :'.$type, $rlt);
	        $success=false;
	        if(!empty($rlt)){
	            foreach ($rlt as $key => $dbRlt) {
	                $success=$dbRlt['success'];
	                if(!$success){
	                    break;
	                }
	            }
	        }
	        return $success;
	    }

	    protected function syncAgentCurrentToMDBWithLock($agent_id, $username, $insert_only=false, &$rlt=null){
	    	if(!$this->utils->isEnabledMDB()){
	    		return true;
	    	}

			return $this->utils->globalLockAgencyRegistration($username, function ()
					use ($agent_id, $insert_only, &$rlt) {
				return $this->syncAgentCurrentToMDB($agent_id, $insert_only, $rlt);
			});
	    }
	    protected function syncAffCurrentToMDBWithLock($affId, $username, $insert_only=false, &$rlt=null){
	    	if(!$this->utils->isEnabledMDB()){
	    		return true;
	    	}

			return $this->utils->globalLockAffiliateRegistration($username, function ()
					use ($affId, $insert_only, &$rlt) {
				return $this->syncAffCurrentToMDB($affId, $insert_only, $rlt);
			});
	    }
	    protected function syncUserCurrentToMDBWithLock($userId, $username, $insert_only=false, &$rlt=null){
	    	if(!$this->utils->isEnabledMDB()){
	    		return true;
	    	}

			return $this->utils->globalLockUserRegistration($username, function ()
					use ($userId, $insert_only, &$rlt) {
				return $this->syncUserCurrentToMDB($userId, $insert_only, $rlt);
			});
	    }
	    protected function syncRoleCurrentToMDBWithLock($roleId, $rolename, $insert_only=false, &$rlt=null){
	    	if(!$this->utils->isEnabledMDB()){
	    		return true;
	    	}

			return $this->utils->globalLockRoleRegistration($rolename, function ()
					use ($roleId, $insert_only, &$rlt) {
				return $this->syncRoleCurrentToMDB($roleId, $insert_only, $rlt);
			});
	    }
	    protected function syncPlayerCurrentToMDBWithLock($player_id, $username, $insert_only=false, &$rlt=null){
	    	if(!$this->utils->isEnabledMDB()){
	    		return true;
	    	}
	    	//sync by id , so lock by id
			return $this->utils->globalLockPlayerRegistration($player_id, function ()
					use ($player_id, $insert_only, &$rlt) {
				return $this->syncPlayerCurrentToMDB($player_id, $insert_only, $rlt);
			});
	    }
	    protected function syncPlayerRegSettingsCurrentToMDBWithLock($type, &$rlt=null){
	    	if(!$this->utils->isEnabledMDB()){
	    		return true;
	    	}

			return $this->utils->globalLockRegistrationSettings($type, function ()
					use ($type, &$rlt) {
				return $this->syncPlayerRegSettingsCurrentToMDB($type, $rlt);
			});
	    }
	    protected function syncAffiliateRegSettingsCurrentToMDBWithLock($type, &$rlt=null){
	    	if(!$this->utils->isEnabledMDB()){
	    		return true;
	    	}

			return $this->utils->globalLockRegistrationSettings($type, function ()
					use ($type, &$rlt) {
				return $this->syncAffiliateRegSettingsCurrentToMDB($type, $rlt);
			});
	    }

	    protected function appendActiveDBToUrl(&$url){
	    	$this->utils->appendActiveDBToUrl($url);
	    }

	    protected function getUriFromReferer(){
	    	$url=!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	    	if(!empty($url)){
	    		return $this->getUriFromAnyUrl($url);
	    	}else{
	    		return '/';
	    	}
	    }

	    protected function getUriFromCurrentUrl(){
	    	$uri=!empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
	    	return $uri;
	    }

	    protected function getUriFromAnyUrl($url){
	    	$arr=parse_url($url);
	    	$path='/';
	    	if(isset($arr['path'])){
	    		$path=$arr['path'];
	    	}
	    	$uri=rtrim($path,'/');

	    	return $uri;
	    }
		//===mdb==========================================================

		/**
		 * scan in list, if match uri, allow * or ?
		 * @param  string $uri
		 * @param  array $list
		 * @return boolean
		 */
		protected function matchUriInList($uri, $list){
			$success=true;
			if(!empty($list) && !empty($uri)){
				$success=false;
				foreach ($list as $pattern) {
					$uri=strtolower($uri);$pattern=strtolower($pattern);
					$success=fnmatch($pattern, $uri);
					// $this->utils->debug_log('matchUriInList, pattern: '.$pattern.', uri: '.$uri, $success);
					if($success){
						//match anyone
						break;
					}
				}
			}
			// $this->utils->debug_log('uri: '.$uri.', list: ', $list, 'success', $success);
			return $success;
		}

	    protected function _refreshBalanceFromApiAndUpdateSubWallet($apiId, $api, $playerName, $playerId){

	    	if($this->utils->isEnabledFeature('refresh_balance_when_launch_game')){

		    	$balanceResult=$api->queryPlayerBalance($playerName);
		    	$this->utils->debug_log('query api balance: '.$playerName, $apiId, $balanceResult);

		    	if(!empty($balanceResult)){
					if($balanceResult['success'] && isset($balanceResult['balance'])){
						$this->load->model(['wallet_model']);
						//compare current balance
						$currentBalance=$this->wallet_model->getSubWalletTotalOnBigWalletByPlayer($playerId, $apiId);

						if($this->utils->compareResultFloat($currentBalance, '=', $balanceResult['balance'])){
							//ignore
					    	$this->utils->debug_log('ignore update because same balance : '.$playerName, $currentBalance, $balanceResult);
						}else{
							$this->lockAndTransForPlayerBalance($playerId, function ()
								use ($playerId, $playerName, $balanceResult, $apiId) {

						    	$this->utils->debug_log('save api balance: '.$playerId, $apiId, $playerName, $balanceResult);
								//save
								return $this->wallet_model->refreshSubWalletOnBigWallet($playerId, $apiId, $balanceResult['balance']);
							});

							// $actionType, $playerId, $affId, $transactionId, $amount, $saleOrderId = null, $playerPromoId = null,
							// $subWalletId = null, $walletAccountId = null, $gamePlatformId = null)
							$this->wallet_model->recordPlayerAfterActionWalletBalanceHistory(Wallet_model::BALANCE_ACTION_REFRESH, $playerId, null, -1, 0, null, null, $apiId, null, $apiId);

						}
					}
		    	}

	    	}

	    }

	    protected function triggerRegisterEvent($playerId){
			if(empty($playerId)){
				return null;
			}

			$callerType=Queue_result::CALLER_TYPE_PLAYER;
			$caller=$playerId;

			$this->load->library(['lib_queue']);
			$this->load->model(['queue_result']);

			$token=$this->lib_queue->triggerAsyncRemoteRegisterEvent(Queue_result::EVENT_REGISTER_AFTER_DB_TRANS, $playerId, $callerType, $caller);
	    }

		protected function triggerGenerateCommandEvent($command, $commandParams=null, $is_blocked=false){
			$this->load->library(array('authentication'));
			$userId = $this->authentication->getUserId();

			$callerType=Queue_result::CALLER_TYPE_ADMIN;
			$caller=$userId;

			$this->load->library(['lib_queue']);
			$this->load->model(['queue_result']);

			$token=$this->lib_queue->triggerAsyncRemoteGenerateCommandEvent(Queue_result::EVENT_GENERATE_COMMAND, $command, $commandParams, $is_blocked, $callerType, $caller);
		}

	    /**
	     * triggerDepositEvent
	     * @param  int $orderId
	     * @param  int $transId
	     * @param  int $systemId
	     * @param  int $userId
	     * @return string $token
	     */
	    protected function triggerDepositEvent($playerId, $orderId, $transId, $systemId, $paymentAccountId, $userId){
			if(!$this->utils->getConfig('enabled_remote_async_event')){
				return null;
			}

	    	if(empty($orderId)){
	    		return null;
	    	}
	    	if(empty($playerId)){
	    		return null;
	    	}
    		$callerType=Queue_result::CALLER_TYPE_SYSTEM;
    		$caller=Queue_result::SYSTEM_UNKNOWN;
	    	if(!empty($systemId)){
	    		$callerType=Queue_result::CALLER_TYPE_SYSTEM;
	    		$caller=$systemId;
	    	}else if(!empty($userId)){
	    		$callerType=Queue_result::CALLER_TYPE_ADMIN;
	    		$caller=$userId;
	    	}
			$this->load->library(['lib_queue']);
			$this->load->model(['queue_result', 'transactions']);
			if(empty($transId)){
				$transId=$this->transactions->getTransIdBySaleOrderId($orderId);
			}
			if(empty($paymentAccountId)){
				$paymentAccountId=$this->transactions->getPaymentAccountIdBySaleOrderId($orderId);
			}

			$token=$this->lib_queue->triggerAsyncRemoteDepositEvent(Queue_result::EVENT_DEPOSIT_AFTER_DB_TRANS,
				$playerId, $orderId, $transId, $paymentAccountId, $callerType, $caller);
			$this->utils->debug_log('deposit event EVENT_DEPOSIT_AFTER_DB_TRANS', $token);

			return $token;
	    }

	    /**
	     * trigger withdrawal event
	     * @param  int $playerId
	     * @param  int $walletAccountId
	     * @param  int $transId
	     * @param  int $systemId
	     * @param  int $userId
	     * @return string $token
	     */
	    protected function triggerWithdrawalEvent($playerId, $walletAccountId, $transId, $systemId, $userId){
			if(!$this->utils->getConfig('enabled_remote_async_event')){
				return null;
			}

	    	if(empty($walletAccountId)){
	    		return null;
	    	}
	    	if(empty($playerId)){
	    		return null;
	    	}
    		$callerType=Queue_result::CALLER_TYPE_SYSTEM;
    		$caller=Queue_result::SYSTEM_UNKNOWN;
	    	if(!empty($systemId)){
	    		$callerType=Queue_result::CALLER_TYPE_SYSTEM;
	    		$caller=$systemId;
	    	}else if(!empty($userId)){
	    		$callerType=Queue_result::CALLER_TYPE_ADMIN;
	    		$caller=$userId;
	    	}
			$this->load->library(['lib_queue']);
			$this->load->model(['queue_result', 'wallet_model']);
			if(empty($transId)){
				$transId=$this->wallet_model->getTransIdByWalletAccountId($walletAccountId);
			}

			$token=$this->lib_queue->triggerAsyncRemoteWithdrawalEvent(Queue_result::EVENT_WITHDRAWAL_AFTER_DB_TRANS,
				$playerId, $walletAccountId, $transId, $callerType, $caller);
			$this->utils->debug_log('deposit event EVENT_WITHDRAWAL_AFTER_DB_TRANS', $token);

			return $token;
	    }

	    protected function isAgencyReadonlySubaccountLogged(){
			$readonly_sub_account=$this->session->userdata('readonly_sub_account');
			$readonlyLogged=$this->utils->isEnabledFeature('enabled_readonly_agency') && !empty($readonly_sub_account);
			if(!$this->utils->isEnabledFeature('enabled_readonly_agency') && !empty($readonly_sub_account)){
				//no feature but readonly in session
				//logout
				$this->session->sess_destroy();
				redirect('/');
				return true;
			}

			return 	$readonlyLogged;
	    }

	    protected function isAgencySubProject(){
	    	return $this->utils->isAgencySubProject();
	    }

	    protected function isAffSubProject(){
	    	return $this->utils->isAffSubProject();
	    }

	    protected function isPlayerSubProject(){
	    	return $this->utils->isPlayerSubProject();
	    }

	    protected function isAdminSubProject(){
	    	return $this->utils->isAdminSubProject();
		}

		protected function stripHTMLtags($str, $remove_html=true, $doHtmlentities=false)
		{
			return $this->utils->stripHTMLtags($str, $remove_html, $doHtmlentities);
		}

	}

}
