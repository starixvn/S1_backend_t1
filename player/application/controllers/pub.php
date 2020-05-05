<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/modules/promo_api_module.php';
require_once dirname(__FILE__) . '/modules/pub_api_module.php';

/**
 * Class Pub
 *
 * General behaviors include :
 *
 * * Get language
 * * Live chat
 * * Game API PT Config
 * * Announcement
 * * Refresh session
 * * Banner
 *
 * @category Player Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Pub extends BaseController {

	use promo_api_module;
	use pub_api_module;

	public function __construct() {
		parent::__construct();

		$this->load->library(array('language_function'));

		$language = $this->input->get('lang', true);

		// TEMPLATE MARKDB
		$this->view_template = $this->utils->getPlayerCenterTemplate();

		switch ($language) {

		case 'en':
			$this->language_function->setCurrentLanguage(1);
			$this->lang->is_loaded = array();
			$this->lang->language = array();
			$this->lang->load('main', 'english');
			// $this->config->set_item('language', 'english');
			break;

		case 'zh-cn':
			$this->language_function->setCurrentLanguage(2);
			$this->lang->is_loaded = array();
			$this->lang->language = array();
			$this->lang->load('main', 'chinese');
			// $this->config->set_item('language', 'chinese');
			break;

		}
	}

    protected function loadTemplate($params = array()) {
        $this->template->set_template($this->utils->getPlayerCenterTemplate(FALSE));

        foreach($params as $metaKey => $metaValue){
            $this->template->write($metaKey, $metaValue);
        }
    }

	/**
	 * overview : get current language
	 *
	 * @param  int 	$langKey
	 * @return int
	 */
	private function getLang($langKey) {
		return lang($langKey);
	}

	private $_apiPT;
	/**
	 * overview : get game api PT config
	 *
	 * @param  int 	$key
	 * @return bool
	 */
	private function getGameApiPTConfig($key) {
		if (!isset($this->_apiPT)) {
			//load game api, but if API not available, will return null
			$this->_apiPT = $this->utils->loadExternalSystemLibObject(PT_API);
		}
		return isset($this->_apiPT) ? $this->_apiPT->getSystemInfo($key) : '';
	}

	/**
	 * overview : go to announcement
	 *
	 * @param  [type] $file
	 * @return  redirect
	 */
	public function go_announcement($file) {
		$site = $this->utils->getSystemUrl('www'); //$this->makeFrontUrl();
		redirect(trim_slashes($site) . '/' . $file);
	}

	/**
	 * overview : site
	 * @param  string $site_name
	 * @return redirect
	 */
	public function site($site_name) {
		// log_message('debug', 'site name:' . $site_name);
		//redirect to site
		//load site
		$this->load->model('static_site');
		// $site = $this->static_site->getSiteById($site_id);
		$site = $this->static_site->getSiteByName($site_name);
		// log_message('error', 'name:' . $site_name . ' to site url:' . $site->site_url);
		redirect($site->site_url);
	}

	/**
	 * overview : refresh current session
	 *
	 * @param  int $type
	 * @param  int $id
	 * @param  int $sessionId
	 */
	public function refresh_session($type, $id, $sessionId) {

		$this->config->set_item('app_debug_log', APPPATH . 'logs/refresh_session.log');

		$this->load->library(array('session'));
		$currSessionId = $this->session->userdata('session_id');
		if (empty($currSessionId)) {
			$currSessionId = 0;
		}
		$playerId = $this->authentication->getPlayerId();

		//type = admin or player
		//id = admin id or player id
		//session id
		//return empty gif
		//
		$debug_session_lost = $this->config->item('debug_session_lost');
		if ($debug_session_lost) {
			$matchStr = ($sessionId == $currSessionId ? 'match_session' : 'not_match_session');
			$matchIdStr = ($playerId == $id ? 'match_id' : 'not_match_id');
			$this->utils->debug_log($type . ' id', $id, 'sessionId', $sessionId, 'current session id', $currSessionId, 'current player id', $playerId, $matchStr, $matchIdStr);
		}
		header('Content-type: image/gif');
		# The transparent, beacon image
		echo chr(71) . chr(73) . chr(70) . chr(56) . chr(57) . chr(97) .
		chr(1) . chr(0) . chr(1) . chr(0) . chr(128) . chr(0) .
		chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) .
		chr(33) . chr(249) . chr(4) . chr(1) . chr(0) . chr(0) .
		chr(0) . chr(0) . chr(44) . chr(0) . chr(0) . chr(0) . chr(0) .
		chr(1) . chr(0) . chr(1) . chr(0) . chr(0) . chr(2) . chr(2) .
		chr(68) . chr(1) . chr(0) . chr(59);
	}

	/**
	 * overview : goto trial pt game
	 *
	 * @param  int 		$gameCode
	 * @return string
	 */
	public function goto_trial_ptgame($gameCode) {
		$api_play_pt = $this->getGameApiPTConfig('API_PLAY');
		$currentLang = $this->config->item('default_language');
		$ptLang = 'zh-cn';
		//lang for PT
		if ($currentLang == 'english') {
			$ptLang = 'en';
		} else {
			$ptLang = 'zh-cn';
		}
		redirect($api_play_pt . '?language=' . $ptLang . '&affiliates=1&nolobby=1&game=' . $gameCode . '&mode=offline');
	}

	# START ANNOUNCEMENT ###############################################################################################################################################

	/**
	 * overview : announcement
	 *
	 * @return load template
	 */
	public function announcement($return_type = 'html') {
        $this->load->model('cms_model');
        $url_www = $this->utils->getSystemUrl("www");

        // Cross-Origin Resource Sharing Header
        header("Access-Control-Allow-Origin: {$url_www}");
        header('Access-Control-Allow-Methods: GET, POST');
//		header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept');

        $is_mobile = $this->utils->is_mobile();

        $announcements = $this->cms_model->getAllNews(NULL, NULL, 'date desc');

        switch($return_type){
            case 'jsonp':
                $announcements = (empty($announcements)) ? [] : $announcements;
                return $this->returnJsonpResult($announcements);
                break;
            case 'json':
                $announcements = (empty($announcements)) ? [] : $announcements;
                return $this->returnJsonResult($announcements);
                break;
            case 'html':
            default:
                $data['announcement_style'] = $this->config->item('ANNOUCEMENT_STYLE');
                if($is_mobile){
                    //append mobile
                    $data['announcement_style'] .= $this->config->item('ANNOUCEMENT_STYLE_MOBILE');
                }
                $data['announcements'] = $announcements;
                $this->load->view('/resources/common/announcement/view_all_announcement', $data);
        }
	}

	/**
	 * overview : announcement popup
	 *
	 * @return load template
	 */
	public function announcement_popup() {
		// load cms model
		$this->load->model('cms_model');
		// get all announcements
		$data['announcements'] = $this->cms_model->getAllNews(null, null, 'date desc');
		// load the view
        $this->loadTemplate();
		$this->load->view('/resources/common/announcement/view_announcement_popup', $data);
	}

	public function announcement_popup_list($list = null, $lang = "en") {

		$viewList = ['nav_list', 'list'];

		if (($list == null) || !in_array($list, $viewList)) {
			show_404(); return;
		}

		$this->load->model('cms_model');
		$condition = ['language' => $lang];
		$data['cms_category_list'] = $this->cms_model->getAllNewsCategory(null, null, null, $condition);
		$data['cms_list'] = $this->cms_model->getAllNews(null, null, 'date desc');

        $this->loadTemplate();
		$this->load->view('/resources/common/announcement/view_announcement_popup_' . $list, $data);
	}

	# END ANNOUNCEMENT #################################################################################################################################################

	/**
	 * overview : live chat add link
	 *
	 * @param  string $theme
	 * @param  string $department
	 * @param  int $survey_id
	 * @param  string $site_name
	 * @return array
	 */
	public function live_chat_add_link($theme=null, $department=null, $survey_id=null, $site_name = 'default') {
		$onlylink=true;
		return $this->live_chat_js($theme, $department, $survey_id, $site_name, $onlylink);
	}

	/**
	 * overview : live chat add
	 *
	 * @param  string $theme
	 * @param  string $department
	 * @param  int $survey_id
	 * @param  string $site_name
	 * @return array
	 */
	public function live_chat($theme=null, $department=null, $survey_id=null, $site_name = 'default') {
		return $this->live_chat_js($theme, $department, $survey_id, $site_name);
	}

	/**
	 * overview : live chas js
	 *
	 * @param  string  $theme
	 * @param  string  $department
	 * @param  int  $survey_id
	 * @param  string  $site_name
	 * @param  boolean $onlylink
	 * @return array
	 */
	public function live_chat_js($theme=null, $department=null, $survey_id=null, $site_name = 'default', $onlylink = false) {
		$this->load->model(array('static_site'));
		$this->load->library(array('authentication'));

		$site = $this->static_site->getSiteByName($site_name);
		$logged = $this->authentication->isLoggedIn(); // ? 'true' : 'false';
		$siteLang = $site->lang; //english or chinese
		$playerId = 0;
		$username = $this->authentication->getUsername();

		if ($logged) {
			$this->authentication->initiateLang();
			$playerId = $this->authentication->getPlayerId();
		} else {
			$this->language_function->setCurrentLanguage($this->language_function->langStrToInt($siteLang));
			//set language
			$this->utils->loadLanguage($siteLang);
		}

		// $key = $this->utils->getConfig('live_chat_encrypt_key1');
		// $add_key = $this->utils->getConfig('live_chat_encrypt_key2');
		// $username = $playerUsername; // . rand(1, 10000);
		$linkurl=$this->utils->getSystemUrl('admin');
		if (empty($username)) {
			$username = 'Guest' . random_string();
		}else{
			$linkurl = $this->utils->getSystemUrl('admin') . '/player_management/player/' . $username;
		}
		// $usernameEncrypted = lhSecurity::encryptBase64($username, $key, $add_key);

		require_once __DIR__.'/../libraries/lib_livechat.php';

		// $this->load->library(array('lib_livechat'));
		$userInfo = array('username' => $username, 'linkurl' => $linkurl);
		$chat_options = $this->utils->getConfig('live_chat');
		$chat_options['www_chat_options']['onlylink']=$onlylink; //overwrite
		$js=Lib_livechat::getChatJs($chat_options, $userInfo);

		$this->returnJS($js);

	}

	/*
	 * http://player.og.local/pub/live_chat_link
	 *
	 *
	 * lang = english or chinese
	 */
	/**
	 * overview : live chat link
	 *
	 * @param  int $lang
	 * @param  string $theme
	 * @param  string $department
	 * @param  int $survey_id
	 * @return string
	 */
	public function live_chat_link($lang=null, $theme=null, $department=null, $survey_id=null) {
		$this->load->model(array('static_site'));
		$this->load->library(array('authentication'));

		if($lang=='false' || $lang=='null'){
			$lang=null;
		}
		if($theme=='false' || $theme=='null'){
			$theme=null;
		}
		if($department=='false' || $department=='null'){
			$department=null;
		}
		if($survey_id=='false' || $survey_id=='null'){
			$survey_id=null;
		}

		// $site = $this->static_site->getSiteByName($site_name);
		$logged = $this->authentication->isLoggedIn(); // ? 'true' : 'false';
		$siteLang = $lang;// $site->lang; //english or chinese
		$playerId = 0;
		$username = $this->authentication->getUsername();

		if ($logged) {
			$this->authentication->initiateLang();
			$playerId = $this->authentication->getPlayerId();
		} else {
			$this->language_function->setCurrentLanguage($this->language_function->langStrToInt($siteLang));
			//set language
			$this->utils->loadLanguage($siteLang);
		}

		// $key = $this->utils->getConfig('live_chat_encrypt_key1');
		// $add_key = $this->utils->getConfig('live_chat_encrypt_key2');
		// $username = $playerUsername; // . rand(1, 10000);
		$linkurl=$this->utils->getSystemUrl('admin');
		if (empty($username)) {
			$username = 'Guest' . random_string();
		}else{
			$linkurl = $this->utils->getSystemUrl('admin') . '/player_management/player/' . $username;
		}
		// $usernameEncrypted = lhSecurity::encryptBase64($username, $key, $add_key);

		require_once __DIR__.'/../libraries/lib_livechat.php';

		// $this->load->library(array('lib_livechat'));
		$userInfo = array('username' => $username, 'linkurl' => $linkurl);
		$chat_options = $this->utils->getConfig('live_chat');
		$options=$chat_options['www_chat_options'];

		if(!empty($theme)){
			$options['theme']=$theme;
		}
		if(!empty($department)){
			$options['department']=$department;
		}
		if(!empty($survey_id)){
			$options['survey_id']=$survey_id;
		}
		if(!empty($lang)){
			$options['lang']=$lang=='chinese' ? 'chn' : 'eng' ;
		}

		$link=Lib_livechat::getStandaloneChatLink($chat_options, $userInfo);

		redirect($link);
	}

	/**
	 * overview : banner
	 *
	 * @param  int $bannerId
	 * @param  string $trackingCode
	 * @param  string $trackingSourceCode
	 * @return string
	 */
	public function banner($bannerId, $trackingCode=null, $trackingSourceCode=null){

		$this->load->model(['http_request', 'affiliatemodel']);

		//write banner
		// $this->load->library(array('authentication'));
		// $logged = isset($this->authentication) ? $this->authentication->isLoggedIn() : false; // ? 'true' : 'false';

		// $playerId=null;
		// if($logged){
		// 	if(method_exists($this->authentication, 'getPlayerId')){
		// 		// $this->authentication->initiateLang();
		// 		$playerId = $this->authentication->getPlayerId();
		// 	}
		// }

		//write to affiliate_traffic_stats
		$visitId=$this->http_request->recordVisitBanner($bannerId, $trackingCode, $trackingSourceCode);

		if(empty($visitId)){
			$this->utils->error_log('record banner faild', $bannerId, $trackingCode, $trackingSourceCode);
		}

		//set to session
		$this->session->set_userdata('visit_record_id', $visitId);

		//return banner url
		// $bannerUrl=$this->affiliatemodel->getBannerUrlById($bannerId);

		$bannerUrl=$this->affiliatemodel->getInternalBannerUrlById($bannerId);
		$this->utils->debug_log('bannerUrl', $bannerUrl);
		// redirect($bannerUrl);
		$this->utils->sendFilesHeader($bannerUrl);
	}

	/**
	 * overview : new player is applying for bonus
	 *
	 * @return array
	 */
	public function newPlayerIsApplyingForBonus(){

	  	$promo_code = $this->input->post('promo_code');
    	$this->session->set_userdata('newPlayerAppliedPromoCode', $promo_code );
        $this->returnJsonResult(array('success' => true, 'msg' => 'ok'));


    }

	/**
	 * overview : check player session timeout
	 *
	 * @return void
	 * @see auth_module->check_player_session_timeout()
	 */
	public function check_player_session_timeout(){
		//$this->returnJsonResult(['success'=>false]);
	}

	/**
	 * overview : redirects to  og local
	 *
	 * @param  string $trackingCode
	 * @param  string $trackingSourceCode
	 * @return redirect
	 */
	public function aff($trackingCode, $trackingSourceCode=null){
		if(empty($trackingCode)){
			$trackingCode = $this->getTrackingCode();
		}

		if(empty($trackingSourceCode)){
			$trackingSourceCode = $this->getTrackingSourceCode();
		}
		$this->session->set_userdata('tracking_source_code', $trackingSourceCode);

		$this->utils->debug_log('trackingCode', $trackingCode, 'trackingSourceCode', $trackingSourceCode);

		redirect($this->utils->getSystemUrl('www').'?code='.$trackingCode.'&source='.$trackingSourceCode);
	}

	public function variables(){

		$player_id=$this->authentication->getPlayerId();

		$is_mobile=$this->utils->is_mobile();
		if($is_mobile){
			$homePageUrl=$this->utils->getSystemUrl('m');
		}else{
			$homePageUrl=$this->utils->getSystemUrl('www');
		}

		$variables=[
			'base_url' => site_url(),
			'player_id' => $player_id ? $player_id : '',
			'is_mobile' => $is_mobile,
			'sessionLimitMessage' => lang('You have reached the total number of minutes you have set in the session limit.'),
			'logoutMessage' => lang('You will be automatically logout after 1 minute'),
			'enabled_responsible_gaming'=>$this->utils->isEnabledFeature('responsible_gaming'),
			'session_limit_type' => 5,
			'session_limit_status_active' => 2,
			'session_limit_status_expired' => 5,
			'sessionLimitTimer' => '',
			'homePageUrl'=>$homePageUrl,
			'public_lang'=>[
				'datetime_picker'=>[
					"separator" => strtolower(lang('player.81')),
					"applyLabel" => lang('lang.apply'),
					"cancelLabel" => lang('lang.clear'),
					"fromLabel" => lang('player.80'),
					"toLabel" => lang('player.81'),
					"customRangeLabel" => lang('lang.custom'),
					"daysOfWeek" => $this->utils->decodeJson(lang('daysOfWeek')),
					"monthNames" => $this->utils->decodeJson(lang('monthNames')),
				],
				'confirm.delete'=>lang("confirm.delete"),
			],
			'csrf_token'=>[
				'enabled'=>$this->utils->getConfig('csrf_protection'),
				'name'=>$this->security->get_csrf_token_name(),
			 	'hash'=>$this->security->get_csrf_hash(),
			],
		];

		$js='';
		foreach ($variables as $key => $value) {
			$js.='var '.$key.'='.$this->utils->encodeJson($value).";\n";
		}

		$js.="var datetime_ranges = {"
               	."'".lang('dt.yesterday')."': [moment().subtract(1,'days').startOf('day'), moment().subtract(1,'days').endOf('day')],"
               	."'".lang('dt.lastweek')."': [moment().subtract(1,'weeks').startOf('isoWeek'), moment().subtract(1,'weeks').endOf('isoWeek')],"
               	."'".lang('dt.lastmonth')."': [moment().subtract(1,'months').startOf('month'), moment().subtract(1,'months').endOf('month')],"
               	."'".lang('dt.lastyear')."': [moment().subtract(1,'years').startOf('year'), moment().subtract(1,'years').endOf('year')],"
				."'".lang('lang.today')."':[moment().startOf('day'), moment().endOf('day')],"
				."'".lang('cms.thisWeek')."':[moment().startOf('isoweek').toDate(), moment().endOf('isoweek').toDate()],"
				."'".lang('cms.thisMonth')."':[moment().startOf('month'), moment().endOf('day')],"
				."'".lang('cms.thisYear')."':[moment().startOf('year'), moment().endOf('day')]};";

		$currSessionId = $this->session->userdata('session_id') ?: 0;

		$js.=$this->utils->getPlayerSessionTimeout($player_id, $currSessionId)."\n";

		$this->returnJS($js);

	}

	public function check_block_site_status(){
		$this->load->model(['country_rules']);

		$ip = $this->utils->getIP(); // '180.232.133.50'; PHILIPPINES
		$isSiteBlock = $this->country_rules->getBlockedStatus($ip, 'blocked_www_m');

        list($city, $countryName) = $this->utils->getIpCityAndCountry($ip);
        $block_page_url = $this->country_rules->getBlockedPageUrl($countryName, $city);

		$this->returnJsonpResult(['success'=>true, 'blocked'=> $isSiteBlock, 'block_page_url'=>$block_page_url ]);
	}

	public function send_exists_sms_verification_captcha($mobileNumber) {
		//check mobile number exist
		$this->load->model(['player_model']);

		if($this->player_model->checkContactExist($mobileNumber)){

			return $this->send_sms_verification_captcha($mobileNumber);

		}else{

			return $this->returnJsonpResult(array('success' => false, 'message' => lang("Mobile number doesn't exist")));

		}

	}

	public function send_sms_verification_captcha($mobileNumber) {

		if($this->utils->getConfig('enabled_registration_captcha') && $this->operatorglobalsettings->getSettingIntValue('captcha_registration')) {

			$captcha=$this->input->get('captcha');
			if(empty($captcha)){
				$captcha=$this->input->post('captcha');
			}

			$this->utils->debug_log('captcha', $captcha, 'passed_captcha', $this->session->userdata('passed_captcha'));

			if(!(empty($captcha) && $this->session->userdata('passed_captcha'))){
				$this->load->library('captcha/securimage');
				$securimage = new Securimage();

				$this->utils->debug_log('check captcha session',$this->session->userdata('auth_flag'), $this->session->all_userdata(), $captcha);

				$rlt = $securimage->check($captcha);

				$this->utils->debug_log('send_sms_verification_captcha captcha result', $rlt);
				if(!$rlt){
					$this->returnJsonpResult(array('success' => false, 'message' => lang('error.captcha')));
					return ;
				}

				$this->session->set_userdata('passed_captcha', true);

			}

			$this->utils->debug_log('captcha', $captcha, 'passed_captcha', $this->session->userdata('passed_captcha'));
		}

		return	$this->send_sms_verification($mobileNumber);
	}

	/**
	 * overview : iframe register send sms verification
	 *
	 * @param string $mobileNumber
	 */
	public function send_sms_verification($mobileNumber = null, $enabled_mode='any') {
		$this->load->library(array('session', 'sms/sms_sender', 'authentication'));
		$this->load->model(['sms_verification', 'player_model']);

		$fields=$this->getInputGetAndPost();
		$player_id = $this->authentication->getPlayerId();

		if(empty($mobileNumber)){
			$mobileNumber=@$fields['contact_number'];
		}

		if ($this->utils->getConfig('disabled_sms')) {
			return $this->returnJsonpResult(array('success' => false, 'message' => lang('Disabled SMS')));
		}

		if (empty($mobileNumber)) {
			# Try to get mobile number from player profile if not supplied
			$this->load->library(array('player_functions'));
			if(!empty($player_id)){
				$player = $this->player_functions->getPlayerById($player_id);
				$mobileNumber = $player['contactNumber'];
			}
			if (empty($mobileNumber)) {
				$this->returnJsonpResult(array('success' => false, 'message' => lang('No contact number available')));
				return;
			}
		}

		if($enabled_mode=='only_exists'){
			if(!$this->player_model->checkContactExist($mobileNumber)){
				$this->returnJsonpResult(array('success' => false, 'message' => lang("Mobile number doesn't exist")));
				return;
			}
		}else if($enabled_mode=='only_new'){
			if($this->player_model->checkContactExist($mobileNumber)){
				$this->returnJsonpResult(array('success' => false, 'message' => lang('Mobile number already be registered, please try login')));
				return;
			}
		}

		$sessionId = $this->session->userdata('session_id');
		$lastSmsTime = $this->session->userdata('last_sms_time');
		$smsCooldownTime = $this->config->item('sms_cooldown_time');
		$smsCooldownTimePerIP = $this->config->item('sms_cooldown_time_per_ip');

		# Should not send SMS without valid session ID
		if(!$sessionId) {
			$this->returnJsonpResult(array('success' => false, 'message' => lang('Unknown error')));
			return;
		}

		# This check ensures for a given session (i.e. session ID), SMS cannot be sent again within the cooldown period
		if ($lastSmsTime && time() - $lastSmsTime <= $smsCooldownTime) {
			$this->returnJsonpResult(array('success' => false, 'message' => lang('You are sending SMS too frequently. Please wait.')));
			return;
		}

		// check ip cool down time

		$codeCount = $this->sms_verification->getVerificationCodeCountPastMinute();
		if($codeCount > $this->config->item('sms_global_max_per_minute')) {
			$this->utils->error_log("Sent [$codeCount] SMS in the past minute, exceeded config max [".$this->config->item('sms_global_max_per_minute')."]");
			$this->returnJsonpResult(array('success' => false, 'message' => lang('SMS process is currently busy. Please wait.')));
			return;
		}

		$code = $this->sms_verification->getVerificationCode($player_id, $sessionId, $mobileNumber);
        $useSmsApi = $this->sms_sender->getSmsApiName();
        $msg = $this->utils->createSmsContent($code, $useSmsApi);

		if ($this->utils->isEnabledFeature('enabled_send_sms_use_queue_server')) {
			$this->load->model('queue_result');
			$this->load->library('lib_queue');

			$mobileNum = $mobileNumber;
			$content = $msg;
			$callerType = Queue_result::CALLER_TYPE_PLAYER;
			$caller = $player_id;
			$state = null;

			$this->lib_queue->addRemoteSMSJob($mobileNum, $content, $callerType, $caller, $state);
			$this->returnJsonpResult(array('success' => true));

		} else {

			if ($this->sms_sender->send($mobileNumber, $msg, $useSmsApi)) {
				$this->session->set_userdata('last_sms_time', time());
				$this->returnJsonpResult(array('success' => true));
			} else {
				$this->returnJsonpResult(array('success' => false, 'message' => $this->sms_sender->getLastError()));
			}

		}
	}

	/**
	 * overview : player main js
	 *
	 * detail : <script data-main="//og.local/pub/default/player_main" src="//og.local/resources/js/require.js" async='true'></script>
	 * 			http://og.local/pub/player_main_js/default
	 * @param  string $site_name
	 * @return array
	 */
	public function player_main_js($site_name, $embed='false', $random_number='') {
        $this->load->library(array('player_main_js_library'));

		//$js=$this->generate_pub_main_js($site_name, $embed=='true');
		$js=$this->player_main_js_library->generate_static_scripts($site_name);

        return $this->returnJS($js);

	}

	/**
	 * overview : player main js
	 *
	 * detail : <script data-main="//og.local/pub/default/player_main" src="//og.local/resources/js/require.js" async='true'></script>
	 * 			http://og.local/pub/player_main_js/default
     *
     * @deprecated
     * @see Player_main_js_library::generate_static_scripts()
	 * @param  string $site_name
	 * @return array
	 */
	protected function generate_pub_main_js($site_name, $embed=false) {
        //load site
        $this->load->model(array('static_site', 'common_token', 'queue_result', 'internal_message', 'operatorglobalsettings','wallet_model','player','affiliatemodel', 'country_rules', 'cms_model'));
        $this->load->library(array('language_function', 'authentication', 'session'));

        // $this->utils->debug_log('print cookie', $this->input->cookie('og_player'), $this->session->userdata('session_id'));

        // $this->utils->recordFullIP();

        $ip = $this->utils->getIP();
        $isSiteBlock = $this->country_rules->getBlockedStatus($ip, 'blocked_www_m');
        list($city, $countryName) = $this->utils->getIpCityAndCountry($ip);
        $block_page_url = $this->country_rules->getBlockedPageUrl($countryName, $city);

        $this->utils->debug_log('isSiteBlock', $isSiteBlock, 'ip', $ip, 'block_page_url', $block_page_url);

        // check aff domain get tracking code or null
        $trackingCode = $this->getTrackingCode();
        if(!empty($trackingCode)){
            $this->setTrackingCodeToSession($trackingCode);
        }
        // $this->session->set_userdata('tracking_code', $trackingCode);
        $trackingSourceCode = $this->getTrackingSourceCode();
        $this->session->set_userdata('tracking_source_code', $trackingSourceCode);

        // check aff domain get tracking code or null
        $trackingCode = $this->getAgentTrackingCode();
        if(!empty($trackingCode)){
            $this->setAgentTrackingCodeToSession($trackingCode);
        }
        // $this->session->set_userdata('tracking_code', $trackingCode);
        $agentTrackingSourceCode = $this->getAgentTrackingSourceCode();
        $this->session->set_userdata('agent_tracking_source_code', $agentTrackingSourceCode);

        $prefixOfPlayer=$this->affiliatemodel->getPrefixByTrackingCode($trackingCode);

        $paramLang=$this->input->get('lang');

        //convert en
        $paramLang=$this->language_function->convertHtmlLang($paramLang);
        // $site = $this->static_site->getSiteById($site_id);
        // $this->utils->debug_log('try load site_name:'.$site_name);
        $site = $this->static_site->getSiteByName($site_name);
        // $this->utils->debug_log('try load site',$site, $site_name);
        // $this->utils->printLastSQL();
        $logged = $this->authentication->isLoggedIn(); // ? 'true' : 'false';
        if(empty($paramLang)){
            $siteLang = $site->lang; //english or chinese
            // $this->utils->debug_log('<----------------------------------set by site lang--------------------------->', $siteLang);
        }else{
            $siteLang = $paramLang;
        }

        if($this->utils->getConfig('set_language_by_subdomain')){
            $siteLang = $this->setLanguageBySubDomainPub();
        }
        // $this->utils->debug_log('<----------------------------------set site lang by template--------------------------->', $siteLang);

        // $this->utils->debug_log('<----------------------------------set by site lang--------------------------->', $siteLang);
        // $this->utils->debug_log('<---------------------------------ParamLang--------------------------->', $paramLang);

        $playerId = 0;
        $messageCount = 0;
        $session_id = $this->session->userdata('session_id');
        if (empty($session_id)) {
            $session_id = 0;
        }
        if ($logged) {
            $playerId = $this->authentication->getPlayerId();
            $messageCount = $this->internal_message->countPlayerUnreadMessages($playerId);
            // $this->utils->debug_log('set by session', $this->language_function->getCurrentLanguage());

            $walletInfo = $this->utils->getSimpleBigWallet($playerId);

            $this->session->unset_userdata('httpHeaderInfo');

            if(!empty($paramLang)){
                $this->language_function->setCurrentLanguage($this->language_function->langStrToInt($paramLang));
                //set language
                $this->utils->loadLanguage($paramLang, 'main', true);
            }else{
                $this->utils->initiateLang();
            }
        } else {
            if(!empty($paramLang)){
                $this->language_function->setCurrentLanguage($this->language_function->langStrToInt($siteLang));
                //focusset language
                $this->utils->loadLanguage($siteLang, 'main', true);
            }else {
                $this->utils->initiateLang();
            }
            $walletInfo = array();

            $this->utils->debug_log('<----------------- Player not login site lang ----------->', $siteLang);
        }

        $refresh_session_url = $this->utils->site_url_with_host('/player/' . $playerId . '/' . $session_id . '/refresh_session.gif');

        // $this->utils->debug_log('session lang', $this->session->userdata('lang'));

        $api_play_pt = $this->getGameApiPTConfig('API_PLAY_PT');
        $host = $this->getPlayerDomain(); // @$_SERVER['HTTP_HOST'];
        $websocket_server_host = $this->config->item('websocket_server_host');
        $apiBaseUrl = "//" . $host . "/async";
        $logoutUrl = "//" . $host . '/iframe_module/iframe_logout';
        $loginUrl = "//" . $host . '/iframe_module/iframe_login';
        $loginWindowUrl = "//" . $host . '/iframe/auth/login';
        $captchaUrlWithRand = "//" . $host . '/iframe/auth/captcha/static_site?' . random_string('alnum');
        $captchaUrl = "//" . $host . '/iframe/auth/captcha/static_site';
        $is_captcha_on = $this->operatorglobalsettings->getSettingIntValue('captcha_registration') && $this->config->item('captcha_login') ? true : false;
        $is_captcha_on_reg = $this->operatorglobalsettings->getSettingJson('registration_captcha_enabled') ? true : false;
        $is_captcha_on_login = $this->operatorglobalsettings->getSettingJson('login_captcha_enabled') ? true : false;

        $default_prefix_for_username = $this->config->item('default_prefix_for_username');

        $assetBaseUrl = '//' . $host . "/resources/player";
        // $apiBaseUrl = "//" . $host . "/async";
        $debugLog = $this->utils->isDebugMode(); // ? 'true' : 'false';
        $enabled_web_push = $this->utils->getConfig('enabled_web_push');
        $playerUsername = $this->authentication->getUsername();
        $VIP_group = $this->authentication->getPlayerMembership();
        $playerId = $this->authentication->getPlayerId();
        $playerInfo = $this->utils->get_player_info($playerId);
        // $token = $this->authentication->getPlayerToken();

        $token = null;
        if(!empty($playerId)){
            $token = $this->common_token->getPlayerToken($playerId);
            // $this->load->helper('cookie');
            // set_cookie('_og_token', $token, $this->utils->getConfig('token_timeout'));
        }

        $pt_jackpot_ticker_js = $this->getGameApiPTConfig('pt_jackpot_ticker_js');
        $pt_casino = $this->getGameApiPTConfig('pt_casino');
        $pt_ticker_server = $this->getGameApiPTConfig('pt_ticker_server');
        $pt_currency = $this->getGameApiPTConfig('pt_currency');

        $origin = "*";
        // $styleClass = json_encode(array(
        // 	'username' => 'ui-input fn-left J-verify',
        // 	'password' => 'ui-input fn-left J-verify',
        // 	'login' => 'fn-left ui-btn ui-btn-red J-submit',
        // 	'register' => 'fn-left ui-btn ui-btn-brown J-regist-btn',
        // ));
        $currentLang = $this->language_function->getCurrentLanguage();
        $defaultErrorMessage = lang("error.default.message");
        $view_template=$this->utils->getPlayerCenterTemplate(false);

        $total_balance = isset($walletInfo['total_balance']['balance'])?$walletInfo['total_balance']['balance']:0;
        $total_withfrozen = isset($walletInfo['total_withfrozen'])?$this->utils->formatCurrencyNoSym($walletInfo['total_withfrozen']):lang('text.loading');

        $enabled_check_frondend_block_status=$this->utils->isEnabledFeature('enabled_check_frondend_block_status');
        $enabled_switch_to_mobile_on_www=$this->utils->isEnabledFeature('enabled_switch_to_mobile_on_www');

        $disable_account_transfer_when_balance_check_fails = (int) $this->utils->isEnabledFeature('disable_account_transfer_when_balance_check_fails');

        $player_live_chat_script = $this->utils->getConfig('player_live_chat_script');

        $refreshInternalMessageTimeInterval = ($this->config->item('get_new_msg_interval')) ? $this->config->item('get_new_msg_interval') : 30000;

        $playercenter_logo = $this->utils->getPlayerCenterLogoURL();

        $img_dir = $this->utils->getSystemUrl("player").'/'. $this->utils->getPlayerCenterTemplate().'/img';

        $main_host = $this->utils->getMainHostName();

        $variables = [
            'cms_version' => $this->utils->getCmsVersion(),
            'block_status' => $isSiteBlock,
            'block_page_url' => $block_page_url,
            'enabled_check_frondend_block_status' => $enabled_check_frondend_block_status,
            'enabled_switch_to_mobile_on_www' => $enabled_switch_to_mobile_on_www,
            'enabled_auto_switch_to_mobile_on_www' => $this->utils->isEnabledFeature('enabled_auto_switch_to_mobile_on_www'),
            'enabled_switch_to_mobile_dir_on_www' => $this->utils->isEnabledFeature('enabled_switch_to_mobile_dir_on_www'),
            'enabled_switch_www_to_https' => $this->utils->isEnabledFeature('enabled_switch_www_to_https'),
            // 'redirect_block_status' => $this->utils->getConfig('common_block_page_url'),
            'host' => $host,
            'trackingCode' => $trackingCode,
            'trackingSourceCode' => $trackingSourceCode,
            'prefixOfPlayer' => $prefixOfPlayer,
            'websocket_server' => $websocket_server_host,
            'assetBaseUrl' => $assetBaseUrl,
            'apiBaseUrl' => $apiBaseUrl,
            'apiPlayPT' => $api_play_pt,
            'pt_jackpot_ticker_js' => $pt_jackpot_ticker_js,
            'pt_casino' => $pt_casino,
            'pt_ticker_server' => $pt_ticker_server,
            'pt_currency' => $pt_currency,
            'origin' => $origin,
            'logged' => $logged,
            'debugLog' => $debugLog,
            'enabled_web_push' => $enabled_web_push,
            'siteLang' => $siteLang,
            'currentLang' => $currentLang,
            'token' => $token,
            'role' => 'player',
            'playerId' => $playerId,
            'playerUsername' => $playerUsername,
            'VIP_group' => $VIP_group,
            'default_prefix_for_username' => $default_prefix_for_username,
            'view_template' => $view_template,
            'walletInfo' => $walletInfo,
            'liveChatUsed' => $this->utils->getCurrentLiveChatUsed(),
            'online_players_count' => 0, //$this->player->getOnlinePlayers(),
            'player_live_chat_script' => $player_live_chat_script,
            'is_mobile' => $this->utils->is_mobile(),
            'popup_window_on_player_center_for_mobile' => $this->utils->isEnabledFeature('popup_window_on_player_center_for_mobile'),
            'popup_deposit_after_login' => $this->utils->getConfig('popup_deposit_after_login'),
            'donot_auto_redirect_to_https_list' => $this->utils->getConfig('donot_auto_redirect_to_https_list'),
            'auto_redirect_to_https_list' => $this->utils->getConfig('auto_redirect_to_https_list'),
            'adjust_domain_to_wwww' => $this->utils->isEnabledFeature('adjust_domain_to_wwww'),
            'refresh_balance_interval_millisecond' => $this->utils->getConfig('refresh_balance_interval_millisecond'),
            'auto_refresh_cold_down_time_milliseconds'=>$this->utils->getConfig('auto_refresh_cold_down_time_milliseconds'),
            'currency' => $this->utils->getCurrentCurrency(),
            'currency_display_order' => $this->utils->getConfig('display_currency_order'),
            'currency_display_options' => $this->utils->getDefaultPlayerCenterCurrencyDisplayOptions(),
            'ui' => [
                'logoutUrl' => $logoutUrl,
                'loginUrl' => $loginUrl,
                'loginWindowUrl' => $loginWindowUrl,
                'enabled_refresh_balance_on_player' => $this->utils->isEnabledFeature('auto_refresh_balance_on_cashier'),
                'disable_account_transfer_when_balance_check_fails' => $disable_account_transfer_when_balance_check_fails,
                'captchaUrlWithRand' => $captchaUrlWithRand,
                'captchaUrl' => $captchaUrl,
                'captchaFlag' => $is_captcha_on,
                'captchaFlagReg' => $is_captcha_on_reg,
                'captchaFlagLogin' => $is_captcha_on_login,
                'loginIframeName' => '_login_iframe',
                'logoutIframeName' => '_logout_iframe',
                'loginContainer' => '#_player_login_area',
                'playerInfoContainer' => '#_player_info_area',
                'playerRegisterContainer' => '._player_register_area',
                'playerPromoContainer' => '#_promo_area',
                'ptGameType' => '.ptgame-titles',
                'ptGame' => '.products',
                'VIP_group' => $VIP_group,
                'messageCount' => $messageCount,
                'total_balance' => $total_balance,
                'total_hasfrozen' => $total_withfrozen,
                'firstName' => $playerInfo['firstName'],
                'withdraw_password' => empty($playerInfo['withdraw_password']) ? '' : '1',
                'hosts' => [
                    'www' => $this->utils->getSystemUrl("www"),
                    'player' => $this->utils->getSystemUrl("player"),
                    'm' => $this->utils->getSystemUrl("m")
                ],
            ],
            'main_host' => $main_host,
            'hosts' => [
                'www' => "www." . $main_host,
                'player' => "player." . $main_host,
                'm' => "m." . $main_host,
                'aff' => "aff." . $main_host,
                'agency' => "agency." . $main_host
            ],
            'urls' => [
                'www' => $this->utils->getSystemUrl("www"),
                'player' => $this->utils->getSystemUrl("player"),
                'm' => $this->utils->getSystemUrl("m"),
                'aff' => $this->utils->getSystemUrl("aff"),
                'agency' => $this->utils->getSystemUrl("agency")
            ],
            'defaultErrorMessage' => $defaultErrorMessage,
            'langText' => $this->utils->generate_lang_text_array(),
            'templates' => [
                'login_template' => $site->login_template,
                'logged_template' => $site->logged_template,
                'pt_game_type_template' => $site->pt_game_type_template,
                'pt_game_template' => $site->pt_game_template,
                'playercenter_logo' => $playercenter_logo,
                'img_dir' => $img_dir,
                'player_active_profile_picture' => $this->utils->getPlayerActiveProfilePicture($playerId),
            ],
            'server_datetime' => $this->utils->generate_date_time_format(),
            'css' => [
                't1t-ui' => rawurlencode($this->utils->getFileFromCache(APPPATH . '/../public/resources/player/ui/t1t-ui.min.css')),
                'material' => rawurlencode($this->utils->getFileFromCache(APPPATH . '/../public/resources/player/material.css')),
                'snackbar' => rawurlencode($this->utils->getFileFromCache(APPPATH . '/../public/resources/player/snackbar.min.css'))
            ]
        ];

        $jsPath = [];

        $name_module = '';

        $requirejs = '';
        $requirejs_config = [
            "baseUrl" => $assetBaseUrl,
	        "waitSeconds" => $this->config->item('loadjs_timeout')
        ];
        $_sbejquery = '';
        $require_self = '';

        $t1t_ui = 'return {};';
        if($embed){
            $name_module = "player_main";
            $require_self = "requirejs(['{$name_module}']);";

            $requirejs = $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/require.js');

            $_sbejquery = $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/jquery-1.11.3.min.js');

            $t1t_ui = $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/ui/t1t-ui.min.js');
		}else{
            $jsPath['json']='json3.min';
            $jsPath['jquery']='jquery-1.11.3.min';
            $jsPath['underscore']='underscore-min';
            $jsPath['snackbar']='snackbar.min';

            $requirejs_config['map'] = [
                "*" => ['jquery' => 'jquery-private'],
                "jquery-private" => ['jquery' => 'jquery']
            ];
        }

		//js path
        $requirejs_config['paths'] = $jsPath;

        $preloadJS = '';
        $preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/jquery.ba-postmessage.min.js');
        $preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/jquery.bpopup.min.js');
		// $preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/swfobject.js');
		// $preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/web_socket.js');
		// $preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/web_push.js');
		if($embed){
			$preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/json_require.js');
			// if(!$use_external_jquery){
				// $preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/jquery-1.11.3.min.js');
				// $preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/jquery-private.js');
			// }
			$preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/underscore-min.js');
			$preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/snackbar_require.js');
			$preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/tooltipsy.min.js');
		}
		$preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/cookies.min.js');

        $preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/event.js');
        $preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/utils.js');
		$preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/render-ui.js');
		$preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/call_api.js');
		$preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/smartbackend.js');

        $extensions_preloadJS = '';
        $extensions = [];
        /* Extension modules ---------------------------------- Start */
        $extensions[] = 'announcement';
        $extensions_preloadJS = $extensions_preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/addons/announcement.js');
        $variables['announcement'] = $this->cms_model->getAllNews(NULL, NULL, 'date desc');
        $variables['announcement_option'] = $this->operatorglobalsettings->getSettingIntValue('announcement_option');
        $variables['auto_popup_announcements_on_the_first_visit'] = $this->utils->isEnabledFeature('auto_popup_announcements_on_the_first_visit');

        $extensions[] = 'player';
        $extensions_preloadJS = $extensions_preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/addons/player.js');
        $variables['player_center'] =[
            'player_auto_lock' => $this->CI->operatorglobalsettings->getSettingIntValue('player_auto_lock', 0),
            'player_auto_lock_time_limit' => $this->CI->operatorglobalsettings->getSettingIntValue('player_auto_lock_time_limit', 600),
            'player_auto_lock_password_failed_attempt' => $this->CI->operatorglobalsettings->getSettingIntValue('player_auto_lock_password_failed_attempt', $this->CI->utils->getUploadConfig('player_auto_lock_password_failed_attempt')),
            'locale' => [
                'player_auto_lock_window_header' => lang('player_auto_lock_window_header'),
                'player_auto_lock_window_submit' => lang('player_auto_lock_window_submit')
            ]
        ];

        $extensions[] = 'player_wallet';
        $extensions_preloadJS = $extensions_preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/addons/player_wallet.js');

        $extensions[] = 'player_message';
        $extensions_preloadJS = $extensions_preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/addons/player_message.js');
        $variables['message'] =[
            'enabled_refresh_message_on_player' => $this->utils->isEnabledFeature('enabled_refresh_message_on_player'),
            'refreshInternalMessageUrl' => $this->utils->getSystemUrl("player", '/async/get_unread_messages'),
            'refreshInternalMessageTimeInterval' => ($this->config->item('get_new_msg_interval')) ? $this->config->item('get_new_msg_interval') : 30000
        ];

        if($this->utils->isEnabledFeature('player_main_js_enable_game_preloader')){
        	$lottery_sdk_url=null;
			$t1lotteryApi=$this->utils->loadExternalSystemLibObject(T1LOTTERY_API);
			if(!empty($t1lotteryApi)){
	        	$rlt=$t1lotteryApi->queryForwardSDK();
		        if($rlt['success'] && !empty($rlt['url'])){
		        	$lottery_sdk_url=$rlt['url'];
		        }

	            $extensions[] = 'game_preloader';
	            $extensions_preloadJS = $extensions_preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/addons/game_preloader.js');
	            $variables['game_preloader'] = [
	                'lottery' => [
	                    'lottery_play_url' => $this->utils->getSystemUrl('player', '/player_center/goto_t1lottery/'),
	                    'lottery_sdk_url' => $lottery_sdk_url,
	                ]
	            ];
        	}else{
        		$this->utils->debug_log('cannot load t1lottery api');
        	}
        }
        /* Extension modules ---------------------------------- End */

		//load all queue results for current player
		// $resultList = json_encode($this->queue_result->getResultListByCaller(Queue_result::CALLER_TYPE_PLAYER, $playerId));
		// $utilsJs = $this->get_utils_js();

		// json : convert json between string and json object
		// underscore: utils and template
		// popup: modal dialog
		// snackbar: tooltip, popup message https://github.com/FezVrasta/snackbarjs
		// jqueryMessage: post message between iframe and parent
		// web_socket, swfobject, WebSocketMain.swf: web socket
		// web_push: push from server
		//
		$ping_time = $this->config->item('ping_time');

		$ping = $this->utils->getPing($ping_time, $refresh_session_url);

        $requirejs_config_str = json_encode($requirejs_config);

        $variables_str = json_encode($variables);

		$js = <<<EOF
{$requirejs}

requirejs.config({$requirejs_config_str});

/**
 * Changed by Elvis Chen<elvis.chen@tripleonetech.net> at 2017/05/14
 *
 * To avoid overwriting already loaded jquery
 */
define('_sbejquery', function(require, exports, module){
	{$_sbejquery}
});

define('t1t-ui', function(require, exports, module){
	{$t1t_ui}
});
require(['t1t-ui']);

define('variables',['_sbejquery'], function($){
	return {$variables_str};
});

{$preloadJS}

{$extensions_preloadJS}

define('{$name_module}', ['smartbackend'], function(_export_smartbackend){
    _export_smartbackend.init(function(){
        $(document).ready(function(){
            var $ = _export_smartbackend.$;
            var variables = _export_smartbackend.variables;
            var utils = _export_smartbackend.utils;
            var renderUI = _export_smartbackend.renderUI;
            var callApi = _export_smartbackend.callApi;

            {$ping}

            utils.events.trigger($.Event('run.t1t.smartbackend', {
                "repeat_trigger": false
            }), _export_smartbackend);

            utils.safelog('main done');
        });
    });
});

(function(){
    {$require_self}
})();

EOF;

		return $js;
	}

	/**
	 *
	 * @param  int $orderId
	 * @return json [success=>, error=>]
	 */
	public function query_order_status($orderId){

		$result=['success'=>false];

		if(!empty($orderId)){

			$this->load->model(['sale_order']);
			$status=$this->sale_order->getStatus($orderId);

			$result['return_url']=$this->utils->getPlayerHomeUrl();
			$result['order_status']= $status==Sale_order::STATUS_SETTLED ? 'settled' : 'others';
			$result['success']=true;

		}

		$this->returnJsonResult($result);
	}

	public function ag($trackingCode, $trackingSourceCode=null){
		if(empty($trackingCode)){
			$trackingCode = $this->getAgentTrackingCode();
		}

		if(empty($trackingCode)) {

			return redirect('/');

		}

		if(empty($trackingSourceCode)){
			$trackingSourceCode = $this->getAgentTrackingSourceCode();
		}

		$this->setAgentTrackingCodeToSession($trackingCode);
		$this->utils->setAgentTrackingSourceCodeToSession($trackingSourceCode);

		$this->utils->debug_log('trackingCode', $trackingCode, 'trackingSourceCode', $trackingSourceCode);
		//write it into
		//register as default
		$redirect_agent_tracking_link_to=$this->utils->getConfig('redirect_agent_tracking_link_to');
		if($redirect_agent_tracking_link_to=='register'){

			redirect('/player_center/iframe_register/_null/_null/'.$trackingCode.'/'.$trackingSourceCode);

		}else if($redirect_agent_tracking_link_to=='www'){

			redirect($this->utils->getSystemUrl('www').'?ag='.$trackingCode.'&ags='.$trackingSourceCode);

		}else if($redirect_agent_tracking_link_to=='player_center'){

			redirect('/');

		}else{

			redirect($redirect_agent_tracking_link_to);

		}
	}

	public function get_player_token(){

		$this->load->library(array('authentication'));

		$playerId = $this->authentication->getPlayerId();

		$token=null;
		if(!empty($playerId)){
			$token = $this->common_token->getPlayerToken($playerId);
			// $this->load->helper('cookie');
			// set_cookie('_og_token', $token, $this->utils->getConfig('token_timeout'));
		}

		$this->returnText($token);

	}


	public function test_502(){

    	show_error('test 502', 502);

	}


	/**
	 * overview : get game api HB jackpots
	 *
	 */
	public function getHabaneroJackpots() {
		//load game api, but if API not available, will return null
		$api = $this->utils->loadExternalSystemLibObject(HB_API);
		$result = $api->getJackpots();
		return !empty($api)?$this->returnJsonResult($result):null;
	}

	public function check_block_js(){
		$this->load->model(array('country_rules'));

		// $this->utils->debug_log('print cookie', $this->input->cookie('og_player'), $this->session->userdata('session_id'));

		// $this->utils->recordFullIP();

        $request_id = $this->utils->getRequestId();
        $cms_version = $this->utils->getCmsVersion();

        $ip = $this->utils->getIP();
        list($city, $countryName) = $this->utils->getIpCityAndCountry($ip);
        $isSiteBlock = $this->country_rules->getBlockedStatus($ip, 'blocked_www_m');
        $block_page_url = $this->country_rules->getBlockedPageUrl($countryName, $city);

        $this->utils->debug_log('isSiteBlock', $isSiteBlock, 'ip', $ip, 'countryName', $countryName, 'city', $city, 'block_page_url', $block_page_url);

        $js = "// {$request_id} - {$cms_version} - locale - {$countryName} - {$city}" . "\r\n";

        if($isSiteBlock){
            $js .= "window.location.href = '" . $block_page_url . "';\r\n";
        }

        $this->returnJS($js);
	}

	/**
	 *
	 * preprocess js
	 * 1. any block ip or county to block url
	 * 2. www or invalid subdomain -> m , if in https white list, then always to https
	 * 3. invalid subdomain -> www, if in https white list, then always to https
	 * 4. if in https white list, then try to https in js, double check https in js
	 *
	 * @param  string $random_number just for disable cache
	 * @return js
	 */
	public function preprocess_js($random_number){
		$this->load->model(array('country_rules'));

		// $this->utils->debug_log('print cookie', $this->input->cookie('og_player'), $this->session->userdata('session_id'));

		// $this->utils->recordFullIP();

        $request_id = $this->utils->getRequestId();
        $cms_version = $this->utils->getCmsVersion();

        $targetUrl=null;

        $ip = $this->utils->getIP();
        list($city, $countryName) = $this->utils->getIpCityAndCountry($ip);
        $isSiteBlock = $this->country_rules->getBlockedStatus($ip, 'blocked_www_m');
        $block_page_url = $this->country_rules->getBlockedPageUrl($countryName, $city);

        $this->utils->debug_log('isSiteBlock', $isSiteBlock, 'ip', $ip, 'countryName', $countryName, 'city', $city, 'block_page_url', $block_page_url);
		if($isSiteBlock){
			$targetUrl=$block_page_url;
		}

        $enabled_auto_switch_to_mobile_on_www = $this->utils->isEnabledFeature('enabled_auto_switch_to_mobile_on_www');
        // $enabled_switch_to_mobile_dir_on_www = $this->utils->isEnabledFeature('enabled_switch_to_mobile_dir_on_www');
        $enabled_switch_www_to_https = $this->utils->isEnabledFeature('enabled_switch_www_to_https');
		$adjust_domain_to_wwww = $this->CI->utils->isEnabledFeature('adjust_domain_to_wwww');

	    $preprocessUtilsJs=<<<EOD
var _sbe_preprocess_utils_class={

	getAffAgQueryString: function(){

		if(this.trackingInfo['code']=='' && this.trackingInfo['source']=='' && this.trackingInfo['ag']=='' && this.trackingInfo['ags']==''){
			return '';
		}

		return 'code='+this.trackingInfo['code']+'&source='+this.trackingInfo['source']
			+'&ag='+this.trackingInfo['ag']+'&ags='+this.trackingInfo['ags'];
	},

	goToMobile: function(forceToHttps){

		//show html part
		if(typeof _show_goto_mobile_tip =='function'){
			_show_goto_mobile_tip();
		}

		//go to domain
		var currentHost = window.location.host;
		var arr=currentHost.split('.');

		if(arr[0]=='m'){
		    return;
		}

		arr[0]='m';
		var protocol=window.location.protocol;
		if(forceToHttps){
			protocol='https:';
		}
		var url= protocol+'//'+arr.join('.');
        var affAgQueryStr=this.getAffAgQueryString();
		if(affAgQueryStr!=''){
        	url=url+'?'+affAgQueryStr;
		}

		if(!this.debugPause(url)){
			return;
		}

		window.location.href=url;
	},

    goToWWW: function(forceToHttps){

        var currentHost = window.location.host;

		var protocol=window.location.protocol;
		if(forceToHttps){
			protocol='https:';
		}
        //invalid sub-domain
        var url= protocol+'//www.'+currentHost;
        var affAgQueryStr=this.getAffAgQueryString();
		if(affAgQueryStr!=''){
        	url=url+'?'+affAgQueryStr;
		}

		if(!this.debugPause(url)){
			return;
		}

        window.location.href=url;
    },

	getCookie: function(c_name){
		if (document.cookie.length > 0) {
			c_start = document.cookie.indexOf(c_name + "=");
			if (c_start != -1) {
				c_start = c_start + c_name.length + 1;
				c_end = document.cookie.indexOf(";", c_start);
				if (c_end == -1) {
					c_end = document.cookie.length;
				}
				return unescape(document.cookie.substring(c_start, c_end));
			}
		}
		return "";
    },
    goHttps: function(){
    	//check https
    	if(window.location.protocol=='http:'){
    		var url=window.location.href;
    		url='https'+url.substr(4, url.length);
    		//redirect to https

			if(!this.debugPause(url)){
				return;
			}
    		window.location.href=url;
    	}
    },
    debugPause: function(url){
    	if(this.debugJS){
    		console.log(url);
    		if(confirm('Are you sure? '+url)){
    			return true;
    		}
	    	return false;
    	}
    	return true;
    },
    debugJS: false,
    trackingInfo: {'code':'', 'source':'', 'ag':'', 'ags':''}
}

var _sbe_preprocess_utils=Object.create(_sbe_preprocess_utils_class);


EOD;

		if($this->utils->getConfig('debug_preprocess_js')){
			$preprocessUtilsJs.="\n\n _sbe_preprocess_utils.debugJS=true; \n\n";
		}
		//prepare aff/code/aid(affiliate tracking code) source(aff source code),
		//ag/agent/agcode(agency tracking code), ags/agent_source(agent source code),

		$affTrackingCode=$this->getTrackingCodeByRef();
		$affSourceCode=$this->getTrackingSourceCodeByRef();
		$agTrackingCode=$this->getAgentTrackingCodeByRef();
		$agSourceCode=$this->getAgentTrackingSourceCodeByRef();

		if(empty($affTrackingCode)){
			$affTrackingCode='';
		}
		if(empty($affSourceCode)){
			$affSourceCode='';
		}
		if(empty($agTrackingCode)){
			$agTrackingCode='';
		}
		if(empty($agSourceCode)){
			$agSourceCode='';
		}

		$trackingJs='_sbe_preprocess_utils.trackingInfo='.$this->utils->encodeJson([
			'code'=>$affTrackingCode, 'source'=>$affSourceCode,
			'ag'=>$agTrackingCode, 'ags'=>$agSourceCode]).";\n\n";

	    $is_mobile=$this->utils->is_mobile();

        $isRedirectToMobile = false;
        if($enabled_auto_switch_to_mobile_on_www){
	        if($is_mobile){
		        //get domain
		        $host= !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
		        if(!empty($host)){
			        $firstSubDomain=explode('.', $host)[0];
			        //valid domains
					$prefixArr = $this->utils->getConfig('prefix_website_list');
					$found=false;
					foreach ($prefixArr as $p) {
						if ($this->utils->startsWith($host, $p.'.')) {
							$found=true;
							break;
						}
					}
					//invalid or www
					if(!$found || $firstSubDomain=='www'){
						$isRedirectToMobile=true;
					}

			        // $is_not_on_m= explode('.', $host)[0]!='m';
			        // if($is_not_on_m){
			        // 	$isRedirectToMobile=true;
			        // }
		        }
	        }
        }

        $isRedirectToHttps=false;
        if($enabled_switch_www_to_https){
	        $is_https = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
	        if(!$is_https){
	            $auto_redirect_to_https_list = $this->utils->getConfig('auto_redirect_to_https_list');

	            if(!empty($auto_redirect_to_https_list)){
	                $current_www = $this->utils->getSystemHost('www');
	                $current_player = $this->utils->getSystemHost('player');

	                $isRedirectToHttps = in_array($current_www, $auto_redirect_to_https_list) || in_array($current_player, $auto_redirect_to_https_list);
	            }
	        }
        }

        $isRedirectToWWW=false;
        if($adjust_domain_to_wwww){
	        $host= !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
	        if(!empty($host)){
		        $firstSubDomain=explode('.', $host)[0];
		        //valid domains
				$prefixArr = $this->utils->getConfig('prefix_website_list');
				$found=false;
				foreach ($prefixArr as $p) {
					if ($this->utils->startsWith($host, $p.'.')) {
						$found=true;
						break;
					}
				}
				if(!$found){
					$isRedirectToWWW=true;
				}
	        }
        }

        $js = "// {$request_id} - {$cms_version} - locale - {$countryName} - {$city} - isRedirectToMobile:".($isRedirectToMobile ? 'true' : 'false')
        	.', isSiteBlock:'.($isSiteBlock ? 'true' : 'false')
        	.', isRedirectToWWW:'.($isRedirectToWWW ? 'true' : 'false')
        	.', isRedirectToHttps:'.($isRedirectToHttps ? 'true' : 'false')
        	."\n\n";

        if($isSiteBlock){
            $js .= "\n\nwindow.location.href = '" . $targetUrl . "';\n\n";
        }else if($isRedirectToMobile){
	        $js .= $preprocessUtilsJs."\n\n".$trackingJs."\n\n _sbe_preprocess_utils.goToMobile(".($isRedirectToHttps ? 'true' : 'false')."); \n\n";
        }else if($isRedirectToWWW){
        	$js .= $preprocessUtilsJs."\n\n".$trackingJs."\n\n _sbe_preprocess_utils.goToWWW(".($isRedirectToHttps ? 'true' : 'false')."); \n\n";
        }else if($isRedirectToHttps){
        	$js .= $preprocessUtilsJs."\n\n".$trackingJs."\n\n _sbe_preprocess_utils.goHttps(); \n\n";
        }

        $this->returnJS($js);

        unset($js);
        unset($preprocessUtilsJs);
	}


    /**
     * [get_frontend_games generate available games game link
     *  How to use this function:
     *      - player.<client_site>/pub/get_front_end_games/<game_platform_id>/<game_type_code>
     *      - sample url: http://player.gamegateway.t1t.games/pub/get_frontend_games/1
     *      - when game_platform_id is empty, this function will show the available game provider with its details and you can use any game_platform_id value as parameter
     *      - as for the game type code, if you put a random text it will show the possible game type codes and you can use it as parameter
     *    System Feature: allow_generate_inactive_game_api_game_lists
     *      - this function have feature that only able the user to view the available game links with current active game api only
     *
     *  Fields description:
     *  'game_type_code' - can be use to determine which game type should be displayed
     *  'game_name' - full game name of the game
     *  'game_name_en' - english game name
     *  'game_name_cn' - chinese game name
     *  'game_name_indo' - indonesian game name
     *  'game_name_vn' - vietnamese game name
     *  'game_name_kr' - korean game name
     *  'provider_name' - Game provider full name
     *  'game_id_desktop' - game launch code for desktop
     *  'game_id_mobile' - game launch code for mobile
     *  'in_flash' - distinction if game is launchable on web only
     *  'in_html5' - distinction if game is launchable on mobile and web
     *  'mobile_enabled' - distinction if game is launchable on mobile only
     *  'downloadable' - distinction if game is downloadable
     *  'available_on_android' - distinction if game is launchable on android web version
     *  'available_on_ios' - distinction if game is launchable on ios web version
     *  'note' - note about the game
     *  'status' - status if game is enabled or not
     *  'top_game_order' - game order of the game most likely for top games
     *  'enabled_freespin' - distinction if game have free spin or not
     *  'sub_game_provider' - name of the game provider
     *  'flag_new_game' - status of the game if new or not
     *  'flag_show_in_site' - status of the game if can be viewable in site
     *  'progressive' - status if the game is/have jackpot or not
     *  'game_launch_url' - this is the available game links for the game
     *  'game_launch_code_other_settings' - this fields contains the other details for game link
     *  ]
     * @param  [type] $game_platform_id [description]
     * @param  [type] $game_type_code   [description]
     * @return [type]                   [description]
     */
    public function get_frontend_games($game_platform_id = null,$game_type_code = null, $game_platform = "all"){
    	if($this->isPostMethod()){
    		$this->output->set_header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        	$this->output->set_header('Access-Control-Allow-Credentials: true');
        	$this->output->set_content_type('application/json');
    	}
    	$request = $this->isPostMethod() ? file_get_contents('php://input') : null;

        $this->load->library('game_list_lib');
        $data = $this->game_list_lib->getFrontEndGames($game_platform_id, $game_type_code, $game_platform, $request);

        # OUTPUT
        $this->returnJsonResult($data);
    }

    public function generate_genesis_promo_register_link(){
		$game_platform_id = GENESISM4_GAME_API;
		# LOAD GAME API

		$player_id = $this->authentication->getPlayerId();
		$player_name = $this->authentication->getUsername();

		# if not login
        if (!$this->authentication->isLoggedIn()) {
            $this->goPlayerLogin();
        }

		$api = $this->utils->loadExternalSystemLibObject($game_platform_id);

		# CHECK PLAYER IF EXIST
		$player = $api->isPlayerExist($player_name);
		# IF NOT CREATE PLAYER
		if (isset($player['exists']) && !$player['exists'] && $player['success']==true) {
			if(!is_null($player['exists'])){
				# GET PLAYER
				$player = $this->player_model->getPlayer(array('playerId' => $player_id));
				# DECRYPT PASSWORD
				$decryptedPwd = $this->salt->decrypt($player['password'], $this->getDeskeyOG());
				# CREATE PLAYER
				$player = $api->createPlayer($player['username'], $player_id, $decryptedPwd, NULL);

				if ($player['success']) {
					$api->updateRegisterFlag($player_id, Abstract_game_api::FLAG_TRUE);
				}
			}
		}

		#get system language
		$extra['language'] = $this->language_function->getCurrentLanguage();
		$promo_link = $api->generatePromoLink($player_name,$extra);
		redirect($promo_link);
    }

    public function generate_mg_promo_register_link($is_mobile = null){
    	if (empty($is_mobile)) {
			$is_mobile = $this->utils->is_mobile();
		} else {
			$is_mobile = $is_mobile == 'true';
		}

    	$game_platform_id = MG_API;
		# LOAD GAME API

		$player_id = $this->authentication->getPlayerId();
		$player_name = $this->authentication->getUsername();

		# if not login
        if (!$this->authentication->isLoggedIn()) {
            $this->goPlayerLogin();
        }

        $api = $this->utils->loadExternalSystemLibObject($game_platform_id);

        # CHECK PLAYER IF EXIST
		$player = $api->isPlayerExist($player_name);
		# IF NOT CREATE PLAYER
		if (isset($player['exists']) && !$player['exists'] && $player['success']==true) {
			if(!is_null($player['exists'])){
				# GET PLAYER
				$player = $this->player_model->getPlayer(array('playerId' => $player_id));
				# DECRYPT PASSWORD
				$decryptedPwd = $this->salt->decrypt($player['password'], $this->getDeskeyOG());
				# CREATE PLAYER
				$player = $api->createPlayer($player['username'], $player_id, $decryptedPwd, NULL);

				if ($player['success']) {
					$api->updateRegisterFlag($player_id, Abstract_game_api::FLAG_TRUE);
				}
			}
		}

		#get system language
		$extra['language'] = $this->language_function->getCurrentLanguage();
		$extra['is_mobile'] = $is_mobile;
		$promo_link = $api->generatePromoLink($player_name,$extra);
		redirect($promo_link);
    }
}

///END OF FILE/////////////////