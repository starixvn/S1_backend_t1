<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/allowed_withdrawal_status.php';
require_once dirname(__FILE__) . '/kyc_status.php';

/**
 * system management
 */
class System_management extends BaseController {
	use allowed_withdrawal_status;
	use kyc_status;

	CONST PLAYER_CENTER_LANGUAGE_OPERATORSETTING_NAME = 'player_center_language';
	CONST ACTION_MANAGEMENT_TITLE = 'System Management';
	function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->library(array('permissions', 'form_validation', 'template', 'data_tables', 'Multiple_image_uploader'));

		$this->permissions->checkSettings();
		$this->permissions->setPermissions();
	}

	const SETTINGS_LIST=[
		'approve_transfer_to_main'=>[
			'note'=>'Allow admin user to approve transfer to main',
			'description_json' => [
				"type" => "checkbox",
				"default_value" => false, "label_lang" => "Approve Transfer To Main",
			],
		],
		'approve_transfer_from_main'=>[
			'note'=>'Allow admin user to approve transfer from main',
			'description_json' => [
				"type" => "checkbox",
				"default_value" => false, "label_lang" => "Approve Transfer From Main",
			],
		],
	];

	const SETTINGS_CASHIER_CENTER_NAME_LIST = array(
        'deposit_process',
        // 'approve_transfer_to_main',
        // 'approve_transfer_from_main',
        // OGP-14009 5.3 Please deprecate these functions if the functions have no effect on the system after code review.
    );

	const SETTINGS_PLAYER_CENTER_NAME_LIST = [
        'limit_of_single_ip_registrations_per_day',
        'player_auto_lock',
        'player_auto_lock_time_limit',
        'player_auto_lock_password_failed_attempt',
        'player_center_logout_action',
        'birthday_option',
        'announcement_option',
        'single_player_session',
        'player_center_enabled_language',
        'deposit_successful_action',
        'registered_success_popup',
        'player_center_currency_display_format',
        'player_center_notification',
        'player_center_notification_check_interval',
        'player_center_notification_source_type',
		'sms_api_list',
    ];

    const SETTINGS_PREFERENCE_NAME_LIST = [];

    /**
	 * Will redirect to another sidebar if the permission was disabled
	 *
	 * Created by Mark Andrew Mendoza (andrew.php.ph)
	 */
    private function error_redirection($from = 'system'){
		$systemUrl = $this->utils->activeSystemSidebar();
		$reportUrl = $this->utils->activeReportSidebar();
		$playerUrl = $this->utils->activePlayerSidebar();

		if($from == 'system')
			$data['redirect'] = $systemUrl;

		elseif($from == 'report')
			$data['redirect'] = $reportUrl;

		else
			$data['redirect'] = $playerUrl;

		$message = lang('con.vsm01');
		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);


		$this->template->write_view('main_content', 'error_page', $data);
		$this->template->render();
	}

	/**
	 * Controller for system_settings pages
	 * OGP-14821: Separated to subpages smart_backend and player_center
	 * @param	string	$subpage	valid values: 'smart_backend' | 'player_center'.  Defaults to 'smart_backend'.
	 *
	 * @link 	system_management/system_settings/smart_backend
	 * @link 	system_management/system_settings/player_center
	 *
	 * @see		views/system_management/sys_settings_smart_backend	(view)
	 * @see		views/system_management/sys_settings_player_center	(view)
	 * @see		resources/js/system_management/system_management.js	(js)
	 * @see		System_management::save_system_settings()		(post endpoint)
	 * @see		System_management::upload_sbe_logo()			(post endpoint)
	 *
	 * @return	Rendered view
	 */
	public function system_settings($subpage = 'smart_backend') {
		$data = array('title' => lang('System Settings'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'system');

		switch ($subpage) {
			case 'player_center' :
				if (!$this->permissions->checkPermissions('system_settings_player_center') ) {
					return $this->error_redirection();
				}
				break;
			case 'smart_backend' :
				if (!$this->permissions->checkPermissions('system_settings_smart_backend') ) {
					return $this->error_redirection();
				}
				break;
			default :
				redirect('system_management/system_settings/smart_backend');
				return;
		}

		$this->load->model(array('operatorglobalsettings','static_site'));
		$logoSettings = $this->operatorglobalsettings->getSettingJson('sys_default_logo');
		$adminAutoLogoutSetting = $this->operatorglobalsettings->getSettingJson('admin_sess_expire');
		$this->utils->debug_log('======================== adminAutoLogoutSetting:' . json_encode($adminAutoLogoutSetting) );

		if (!$this->utils->isUploadedLogoExist() || !$this->utils->isLogoOperatorSettingsExist() || !$this->utils->isLogoSetOnDB()) {
			$data['useSysDefault'] = 'checked';
		} else {
			$data['useSysDefault'] =  $logoSettings['use_sys_default'] ? 'checked' : '';
		}

		$data['enable_admin_auto_logout'] = $adminAutoLogoutSetting['enable'];
		$data['auto_logout_sess_expiration'] = $adminAutoLogoutSetting['sess_expiration']/60;

	    $data['logo_image'] = $data['useSysDefault'] == 'checked' ? $this->utils->getDefaultLogoUrl() : $this->utils->getUploadedSysLogoUrl();
        $data['playerCenterLanguage'] = $this->getPlayerCenterLanguage();

		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$render = true;

		$this->addBoxDialogToTemplate();

		$this->load->library(array('sms/sms_sender'));
		$data['smsBalances'] = $this->sms_sender->getSmsBalances();

		switch ($subpage) {
			case 'player_center' :
				$data['settings'] = [
		            'player_center' => [
		                'name' => lang('Player Center'),
		                'options' => $this->operatorglobalsettings->getSystemSettings(self::SETTINGS_PLAYER_CENTER_NAME_LIST)
		            ],
		            'cashier' => [
		                'name' => lang('Cashier Center'),
		                'options' => $this->operatorglobalsettings->getSystemSettings(self::SETTINGS_CASHIER_CENTER_NAME_LIST)
		            ],
		        ];
				$view_filename = "system_management/sys_settings_player_center";
				break;
			case 'smart_backend' : default :
				$data['settings'] = [
		           // 'preference' => [
		           //     'name' => lang('System Settings'),
		           //     'options' => $this->operatorglobalsettings->getSystemSettings(self::SETTINGS_PREFERENCE_NAME_LIST)
		           // ],
		            'cron_jobs' => [
		                'name' => lang('sys_settings_cron_jobs'),
		                'options' => $this->operatorglobalsettings->getSystemSettings($this->operatorglobalsettings->getOperatorNameListForAllCronJobs())
		            ],
		        ];
				$view_filename = "system_management/sys_settings_smart_backend";
				break;
		}


		$this->loadDefaultTemplate([ 'resources/js/system_management/system_management.js' ] ,
			array('resources/css/general/style.css'),
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			$view_filename, $data, $render);
	}

	/**
	 * Post endpoint for /system_management/system_settings/{smart_backend,player_center}
	 * Saves system settings
	 * OGP-15126 fixed bug when saving system settings
	 *
	 * @see		OGP-15126, OGP-14821
	 * @see		System_management::system_settings()
	 * @return	none
	 */
	public function save_system_settings() {
		$from_url = $this->input->server('HTTP_REFERER');
		$from_path = parse_url($from_url, PHP_URL_PATH);
		$from_path_parts = explode('/', $from_path);
		/**
		 * $subpage: last part of $from_path
		 * should be any of [ 'player_center', 'smart_backend' ]
		 */
		$subpage = end($from_path_parts);

		// $this->utils->debug_log(__METHOD__, ['subpage' => $subpage]);

		$this->load->model(array('operatorglobalsettings'));

		$this->startTrans();
		$settings = array();

		// If from .../system_settings/smart_backend
		if ($subpage == 'smart_backend') {
			// Update preferences (empty now)
			foreach (self::SETTINGS_PREFERENCE_NAME_LIST as $name) {
				$settings[$name] = $this->input->post('value_' . $name);
			}
			// Update cronjobs
			foreach ($this->operatorglobalsettings->getOperatorNameListForAllCronJobs() as $name) {
				$settings[$name] = $this->input->post('value_' . $name);
			}
		}

		// If from .../system_settings/player_center
		if ($subpage == 'player_center') {
			// Update cashier center settings
			foreach (self::SETTINGS_CASHIER_CENTER_NAME_LIST as $name) {
				$settings[$name] = $this->input->post('value_' . $name);
			}
			// Update player center settings
			foreach (self::SETTINGS_PLAYER_CENTER_NAME_LIST as $name) {
				$settings[$name] = $this->input->post('value_' . $name);
			}
		}

		// $this->utils->debug_log(__METHOD__, ['settings' => $settings]);

		if (!empty($settings)) {
			$this->operatorglobalsettings->saveSettings($settings);
		}

		//$this->saveAction($management, $action, $description);

		$success = $this->endTransWithSucc();
		if ($success) {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save system settings successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
		}

		redirect($from_path);
		// redirect('system_management/system_settings');
	}

	public function save_operator_settings(){
		//only admin
		if (!$this->permissions->checkPermissions('system_settings')) {
			$this->returnErrorStatus();
			// $this->showErrorAccess(lang('System Message'),'system_management/sidebar','system_message');
		}

		$success=$this->utils->putOperatorSetting('sms_registration_template', $this->input->post('sms_registration_template'));
		$message=null;
		//update player_center_template, validate dir
		$dir=dirname(__FILE__).'/../../../player/application/views/'.$this->input->post('player_center_template');
		if(is_dir($dir)){
			$success=$success && $this->utils->putOperatorSetting('player_center_template', $this->input->post('player_center_template'));
		}else{
			$message=lang('Wrong template of player center');
			$success=false;
		}

		if($success){
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, !empty($message) ? $message : lang('error.default.db.message'));
		}

		redirect('system_management/system_settings');
	}

	public function save_system_message(){
		//only admin
		if (!$this->permissions->checkPermissions('system_settings')) {
			$this->returnErrorStatus();
			// $this->showErrorAccess(lang('System Message'),'system_management/sidebar','system_message');
		}

		$result=['success'=>true];

		$msg=$this->input->post('msg');
		if(!empty($msg)){
			$this->load->model(array('users'));
			$server_name = $this->utils->getConfig('server_name');

			$msg=json_decode($msg,true);

			$content = $msg['content'];
			$options = @$msg['options'];
			$servers = $msg['servers'];
			$user = $msg['user'];

			if (in_array($server_name, $servers)) {
				$result['success']=!!$this->users->writeUnreadAdminMessage($user, $content, $options);
			} else {
				$this->utils->debug_log('drop message', $servers, 'user', $user);
			}

		}

		$this->returnJsonResult($result);
	}

	public function get_system_messages(){

		$this->load->view('system_management/system_messages', $data);
	}

	public function system_features(){
		$data = array('title' => lang('System Features'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'system');

		if (!$this->permissions->checkPermissions('system_features')) {
			return $this->error_redirection();
		}

		$this->load->model(array('system_feature'));

		$data['system_feature'] = $this->system_feature->get();

		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$render = true;

		$this->loadDefaultTemplate([ 'resources/js/system_management/system_management.js' ] ,
			array('resources/css/general/style.css'),
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			'system_management/system_features', $data, $render);
	}

	function saveSystemFeatures(){
		if (!$this->permissions->checkPermissions('system_features')) {
			return $this->error_redirection();
		}

		$this->load->model(array('system_feature'));
		$featureList = $this->input->post('enabled');

		$success=$this->lockAndTrans(Utils::LOCK_ACTION_SYSTEM_FEATURE, 0, function()
			use($featureList){
			$succ=true;
			if( ! empty( $featureList ) ){
				foreach ($featureList as $idx => $feature) {
					$succ=$this->system_feature->updateFeaturesWithoutClearCache(
						$feature['id'], ['enabled' => $feature['enabled']]);
					if(!$succ){
						return $succ;
					}
				}
			}
			return $succ;
		});
		$result=['success'=>$success];

		if ($success) {
			$this->utils->deleteCache();
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save system features successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
		}

		$this->returnJsonResult($result);
	}

	/**
	 * overview: upload file to app then set as system logo
	 *
	 * @param  none
	 * @return void
	 */
	public function upload_sbe_logo() {
		$default_dir_logo = $this->utils->default_upload_sub_dir_for_logo;

		$this->load->model(array('operatorglobalsettings'));
		// Use default logo
		if ($this->input->post('setDefaultLogo')) {
			$this->setToDefaultLogo();
			redirect('system_management/system_settings');
			return;
		}

	    $sysLogoValue = $this->utils->getSysLogoSettingFromOperatorSettings();

		// If no file to upload
		if (trim($_FILES["fileToUpload"]["name"])  == "") {

	    	// Check file name setup in the operatorssetting.
	    	if (trim($sysLogoValue['filename']) == "") {
	    		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, "Please upload a logo first.");
	    		redirect('system_management/system_settings');
	    		return;
	    	}

	    	// Check if file exist in directory
	    	// $file_loc = $_SERVER['DOCUMENT_ROOT'] . '/resources/images/' . $sysLogoValue['path'] . $sysLogoValue['filename'];
	    	$file_loc = $this->utils->getUploadedSysLogoRealPath();

			if (!file_exists($file_loc)) {
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, "Please upload a logo first.");
	    		redirect('system_management/system_settings');
	    		return;
			}

			$sysLogoValue['use_sys_default'] = false;
			$this->operatorglobalsettings->putSettingJson($settingName, $sysLogoValue);
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, "System logo set successfully.");
			redirect('system_management/system_settings');
			return;
		}

		/** Upload file and set as default logo */
		// $target_dir = "{$this->utils->getUploadPath()}/{$default_dir_logo}";
		$target_dir = $this->utils->getSysLogoUploadPath();

		if (!file_exists($target_dir)) {
			$this->utils->debug_log('Creating logo upload dir', $target_dir);
			// Make sure directory priv is 777:
			// (1) Store umask (2) set umask 000 (3) create dir (4) restore umask
			$old_umask = umask(0);
			mkdir($target_dir, 0777 , 'recursive');
			umask($old_umask);

			if (!file_exists($target_dir)) {

				$this->utils->error_log("error creating logo upload dir", $target_dir);
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, "Error creating logo upload directory $target_dir");
				return;
			}
		}
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

		$uploadOk = 1;
		$message = "";

		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		// Check if image file is a actual image or fake image
	    if(!getimagesize($_FILES["fileToUpload"]["tmp_name"])) {
	        $message = "File is not an image.";
	        $uploadOk = 0;
	    }

		// Check file size
		if ($_FILES["fileToUpload"]["size"] > 30000) {
		    $message = "Sorry, your file is too large.";
		    $uploadOk = 0;
		}
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
		    $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		    $uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
		    $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		// if everything is ok, try to upload file
		} else {
		    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
		    	$settingName = $this->utils->getDefaultLogoSettingName();
		    	$sysLogoValue = $this->operatorglobalsettings->getSettingJson($settingName);
		    	$fileName = $_FILES["fileToUpload"]["name"];

		    	$settingValue = array('path' => $default_dir_logo, 'filename' => $fileName, 'use_sys_default' => false);
		    	// If sys_default_log value doesn't exist
		    	if (!count($sysLogoValue)) {
		    		// Insert setting
		    		$this->operatorglobalsettings->insertSettingJson($settingName, $settingValue);
				} else {
					// Update setting
					$this->operatorglobalsettings->putSettingJson($settingName, $settingValue);
				}

		        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, "System logo set successfully.");
		    } else {
		    	$this->alertMessage(self::MESSAGE_TYPE_ERROR, "Sorry, there was an error uploading your file.");
		    }
		}
		redirect('system_management/system_settings');
	}

	public function testFunction() {
		$sysLogoValue['filename'] = $fileName;
		$sysLogoValue['use_sys_default'] = false;
		$this->operatorglobalsettings->putSettingJson($settingName, $sysLogoValue);
	}

	/**
	 * overview: Set systen logo to default
	 *
	 */
	public function setToDefaultLogo() {
		$settingName = $this->utils->getDefaultLogoSettingName();
		$sysLogoValue = $this->operatorglobalsettings->getSettingJson($settingName);
		$sysLogoValue['use_sys_default'] = true;
		$this->operatorglobalsettings->putSettingJson($settingName, $sysLogoValue);
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, "Logo set to system default.");
	}

	public function setPlayerCenterDefaultLanguage() {
		$this->load->model(array('operatorglobalsettings'));
		$this->utils->initialiazeDefaultPlayerCenterLanguage();

		$selectedLanguage = $this->input->post('rdbLanguage');
		$isHideLangOpt = $this->input->post('chkHideLangOption');

		if (!isset($selectedLanguage) || empty($selectedLanguage)) {
			$selectedLanguage = '0';
		}

		if (!isset($isHideLangOpt)) {
			$isHideLangOpt = false;
		}

		$langSetupValue = $this->operatorglobalsettings->getSettingJson(self::PLAYER_CENTER_LANGUAGE_OPERATORSETTING_NAME);
		$langSetupValue['language'] = $selectedLanguage;
		$langSetupValue['hide_lang'] = $isHideLangOpt;

		$this->operatorglobalsettings->putSettingJson(self::PLAYER_CENTER_LANGUAGE_OPERATORSETTING_NAME, $langSetupValue);

		if ($this->endTransWithSucc()) {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save system settings successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
		}

		redirect('system_management/system_settings');
	}

	public function getPlayerCenterLanguage() {
		$this->load->model(array('operatorglobalsettings'));
		$langSetup = $this->operatorglobalsettings->getSettingJson(self::PLAYER_CENTER_LANGUAGE_OPERATORSETTING_NAME);

		if (!isset($langSetup)) {
			$this->utils->initialiazeDefaultPlayerCenterLanguage();
			$langSetup = $this->operatorglobalsettings->getSettingJson(self::PLAYER_CENTER_LANGUAGE_OPERATORSETTING_NAME);
		}

		return $langSetup;
	}

	public function setupNewPlayerCenter($setup_type = "manual") {
		if (!$this->permissions->checkPermissions('system_settings')) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['system_settings']);
		}

		# Load models
		$this->load->model(array('system_feature'));

		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$render = true;

		# Prepare view data
		$data = array(
			'title' => lang('Setup New Player Center'),
			'sidebar' => 'system_management/sidebar',
			'activenav' => 'system_settings',
			'setupType' => $setup_type
		);

		// initial_player_center_features
		$data['system_feature'] = $this->system_feature->get();

		$this->loadDefaultTemplate(array(),
			array('resources/css/general/style.css'),
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
				'system_management/setup_new_player_center', $data, $render);
	}

	public function setNewPlayeCenterTemplate(){
		//update player_center_template, validate dir
		$result=['success'=>true];
		$success = false;

		$dir=dirname(__FILE__).'/../../../player/application/views/'.$this->utils->getConfig('new_player_center_default_template');
		if(is_dir($dir)){
			$success = $this->utils->putOperatorSetting('player_center_template', $this->utils->getConfig('new_player_center_default_template'));
		} else {
			$message=lang('Wrong template of player center');
		}

		if(!$success){
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, !empty($message) ? $message : lang('error.default.db.message'));
			redirect('system_management/system_settings');
			$result=['success'=>false];
		}

		$this->returnJsonResult($result);
	}

	public function setNewPlayeCenterLanguage() {
		$this->load->model(array('operatorglobalsettings'));
		$selectedLanguage = $this->input->post('language');

		$result=['success'=>true];
		$success = false;

		if (!isset($selectedLanguage) || empty($selectedLanguage)) {
			$selectedLanguage = '0';
		}
		$langSetupValue = $this->operatorglobalsettings->getSettingJson(self::PLAYER_CENTER_LANGUAGE_OPERATORSETTING_NAME);
		$langSetupValue['language'] = $selectedLanguage;
		$langSetupValue['hide_lang'] = false;

		$success = $this->operatorglobalsettings->putSettingJson(self::PLAYER_CENTER_LANGUAGE_OPERATORSETTING_NAME, $langSetupValue);

		if(!$success){
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, !empty($message) ? $message : lang('error.default.db.message'));
			$result=['success'=>false];
		}

		$this->returnJsonResult($result);
	}

	public function upload_player_logo() {

		if ($this->input->post('setDefaultPlayerLogo')) {
			$this->operatorglobalsettings->syncSettingJson("player_center_logo", null, 'value');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, "Logo set to player default.");
			redirect('system_management/system_settings');
			return;
		}

		$file_header_logo = isset($_FILES['fileToUpload']) ? $_FILES['fileToUpload'] : null;
		$path_logo_image =$this->utils->getLogoTemplatePath();
		$path_logo_image=rtrim($path_logo_image, '/');
		$file_name = 'playercenter_header_logo_'.strtotime(today);
		$this->load->model(array('operatorglobalsettings'));

		if(empty($file_header_logo['size'][0])) {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('player.mp14'));
				redirect('system_management/system_settings');
				return;
		} else {
			 	$config_logo_image = array(
		            'allowed_types' => 'png',//array("jpg","jpeg","png","gif", "PNG"),
		            'max_size'      => $this->utils->getMaxUploadSizeByte(),
		            'overwrite'     => true,
		            'remove_spaces' => true,
		            'upload_path'   => $path_logo_image,
		        );

		        $file_to_upload[] = array(
					"file_details" => $file_header_logo,
					"upload_path" => $path_logo_image,
					"config_header" => $config_logo_image,
					"file_name" => $file_name,
				);

		        foreach ($file_to_upload as $key => $value) {
		        	$response = $this->multiple_image_uploader->do_multiple_uploads($value['file_details'], $value['upload_path'], $value['config_header'], $value['file_name']);
			        if($response['status'] == "fail" ) {
			        	$this->alertMessage(self::MESSAGE_TYPE_ERROR, $response['message']);
			        	redirect('system_management/system_settings');
						return;
			        }
		        }

		        $this->operatorglobalsettings->syncSettingJson("player_center_logo", $file_name, 'value');
		        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, "Player logo set successfully.");
		        redirect('system_management/system_settings');
		}
	}

	public function save_cms_version(){
		//only admin
		if (!$this->permissions->checkPermissions('system_settings')) {
			$this->returnErrorStatus();
			redirect('cms_management/playerCenterSettings');
			return;
		}
		$cms_version = $this->input->post('cms_version');

		if ($cms_version) {
			$this->operatorglobalsettings->syncSettingJson("cms_version", $cms_version , 'value');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, "CMS version successfully update.");
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('player.mp14'));
		}

		redirect('system_management/system_settings');
	}

	public function upload_player_favicon() {

		if ($this->input->post('setDefaultPlayerFavicon')) {
			$this->operatorglobalsettings->syncSettingJson("player_center_favicon", null, 'value');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, "Favicon set to player default.");
			redirect('system_management/system_settings');
			return;
		}

		$file_header_favicon = isset($_FILES['fileToUpload']) ? $_FILES['fileToUpload'] : null;
		$path_favicon_image =$this->utils->getFaviconTemplatePath();
		$path_favicon_image=rtrim($path_favicon_image, '/');
		$file_name = 'favicon_'.strtotime(today);
		$this->load->model(array('operatorglobalsettings'));

		if(empty($file_header_favicon['size'][0])) {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('player.mp14'));
				redirect('system_management/system_settings');
				return;
		} else {
			 	$config_logo_image = array(
		            'allowed_types' => 'ico',//array("jpg","jpeg","png","gif", "PNG"),
		            'max_size'      => $this->utils->getMaxUploadSizeByte(),
		            'overwrite'     => true,
		            'remove_spaces' => true,
		            'upload_path'   => $path_favicon_image,
		        );

		        $file_to_upload[] = array(
					"file_details" => $file_header_favicon,
					"upload_path" => $path_favicon_image,
					"config_header" => $config_logo_image,
					"file_name" => $file_name,
				);

		        foreach ($file_to_upload as $key => $value) {
		        	$response = $this->multiple_image_uploader->do_multiple_uploads($value['file_details'], $value['upload_path'], $value['config_header'], $value['file_name']);
			        if($response['status'] == "fail" ) {
			        	$this->alertMessage(self::MESSAGE_TYPE_ERROR, $response['message']);
			        	redirect('system_management/system_settings');
						return;
			        }
		        }

		        $this->operatorglobalsettings->syncSettingJson("player_center_favicon", $file_name, 'value');
		        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, "Player favicon set successfully.");
		        redirect('system_management/system_settings');
		}
	}

	public function save_player_center_title(){
		//only admin
		if (!$this->permissions->checkPermissions('system_settings')) {
			$this->returnErrorStatus();
			redirect('system_management/system_settings');
			return;
		}
		$player_center_title = $this->input->post('player_center_title');

		if ($player_center_title) {
			$this->operatorglobalsettings->syncSettingJson("player_center_title", $player_center_title , 'value');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, "Player Center title successfully update.");
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('player.mp14'));
		}

		redirect('system_management/system_settings');
	}

	public function post_sync_cashback(){
		$data = array('title' => lang('Sync Cashback'), 'sidebar' => 'system_management/sidebar', 'activenav' => 'sync_game_logs');

		if (!$this->permissions->checkPermissions('manually_calculate_cashback') ||
			!$this->permissions->checkPermissions('dev_functions') ||
			!$this->users->isT1User($this->authentication->getUsername())) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$cashback_date=$this->input->post('cashback_date');

		$this->utils->debug_log('post_sync_cashback cashback_date', $cashback_date);

		$success = true;
		$dry_run = $this->input->post('dry_run')=='true';
		$dry_run = $dry_run ? 'true' : 'false';

		//run command
		$is_blocked=false;
		$cmd = $this->utils->generateCommandLine('sync_cashback', [$dry_run, $cashback_date], $is_blocked);

		raw_debug_log($cmd, pclose(popen($cmd, 'r')));

		//go back
	    if ($success) {
	        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Running cashback').'... '.$cashback_date.' '.($dry_run=='true' ? lang('Dry Run') : ''));
	    } else {
	        $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
	    }

		redirect('/system_management/other_functions');
	}

	public function post_sync_game_logs(){
		$data = array('title' => lang('Sync Game Logs'), 'sidebar' => 'player_management/sidebar', 'activenav' => 'sync_game_logs');

		if (!$this->permissions->checkPermissions('sync_game_logs')) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$by_date_from_str = $this->input->post('by_date_from');
		$by_date_to_str   = $this->input->post('by_date_to');

		//THERE'S A RULE FOR SYNC, DON'T CHANGE IT
		list($min_datetime_str, $max_datetime_str)=$this->utils->getSyncDateRule();
		$this->utils->debug_log('min/max date rule', $min_datetime_str, $max_datetime_str);

		$success = true;
		$msg = null;
		$min_datetime_obj = new DateTime($min_datetime_str);
		$max_datetime_obj = new DateTime($max_datetime_str);
		$by_date_from_obj = new DateTime($by_date_from_str);
		$by_date_to_obj   = new DateTime($by_date_to_str);

		if ($by_date_from_obj < $min_datetime_obj) {
			//wrong start time
			$msg = lang('min datetime is') . $min_datetime_str;
			$success = false;

		}
		if ($by_date_to_obj > $max_datetime_obj) {
			//wrong datetime
			$msg = lang('max datetime is') . $max_datetime_str;
			$success = false;
		}

		//go back
	    if (!$success) {
	       $this->alertMessage(self::MESSAGE_TYPE_ERROR, $msg);
	       return redirect('/system_management/other_functions');
	    }

		$by_game_platform_id = !empty($this->input->post('by_game_platform_id')) ? $this->input->post('by_game_platform_id') : '';
		$playerName = !empty($this->input->post('playerName')) ? $this->input->post('playerName') : '';
		$this->utils->debug_log('by_date_from', $by_date_from_obj, 'by_date_to', $by_date_to_obj, 'by_game_platform_id', $by_game_platform_id);

		$dry_run       = $this->input->post('dry_run')=='true';
		$merge_only    = $this->input->post('merge_only')=='true';
		$only_original = $this->input->post('only_original')=='true';

		$this->load->library(['lib_queue', 'language_function']);
		$this->load->model(['queue_result']);

		//run command
		$caller = $this->authentication->getUserId();
		$state = null;
		$timelimit = 30; //minutes
		$lang = $this->language_function->getCurrentLanguage();
		$token = $this->lib_queue->addRemoteSyncGameLogsJob($by_date_from_str, $by_date_to_str, $by_game_platform_id,
			Queue_result::CALLER_TYPE_ADMIN, $caller, $state, $lang, $playerName, $dry_run, $timelimit,
			$merge_only, $only_original);

		//goto queue page
		redirect('/system_management/common_queue/'.$token);
	}

	public function post_calculate_aff_earnings() {
		$data = array('title' => lang('Sync Game Logs'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'sync_game_logs');

		if (!$this->permissions->checkPermissions('dev_functions') || !$this->users->isT1User($this->authentication->getUsername())) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$startDate = $this->input->post('startDate');
		$endDate = $this->input->post('endDate');
		$username = $this->input->post('username');

		$this->utils->debug_log('startDate', $startDate, 'endDate', $endDate, 'username', $username);

		$success = TRUE;

		$dry_run = $this->input->post('dry_run') == 'true';
		$dry_run = $dry_run ? 'true' : 'false';

		$this->load->library(['lib_queue', 'language_function']);
		$this->load->model(['queue_result']);

		$caller = $this->authentication->getUserId();
		$state 	= null;
		$lang 	= $this->language_function->getCurrentLanguage();

		$token 	= $this->lib_queue->addCalculateAffEarnings($startDate, $endDate, $username, Queue_result::CALLER_TYPE_ADMIN, $caller, $state, $lang, $dry_run);
		redirect('/system_management/common_queue/' . $token);
	}

	public function post_rebuild_games_total(){
		$data = array('title' => lang('Dev Functions'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'dev_functions');

		if (!$this->permissions->checkPermissions('sync_game_logs') || !$this->users->isT1User($this->authentication->getUsername())) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$by_date_from=$this->input->post('by_date_from');
		$by_date_to=$this->input->post('by_date_to');
		$rebuild_hour=$this->input->post('rebuild_hour')=='true';
		$rebuild_minute=$this->input->post('rebuild_minute')=='true';
		$rebuild_hour = $rebuild_hour ? 'true' : 'false';
		$rebuild_minute = $rebuild_minute ? 'true' : 'false';

		$lock_rebuild_reports_range= $this->utils->getConfig('lock_rebuild_reports_range');

		if(!empty($lock_rebuild_reports_range)){
			$from = $this->utils->formatDateForMysql(new DateTime($by_date_from));
			$to = $this->utils->formatDateForMysql(new DateTime($by_date_to));
			if($this->utils->isRebuildReportDateLocked($lock_rebuild_reports_range,$from,$to,$rlt)){
				$this->utils->error_log(sprintf(lang("Regenarate All Report has lock - should not be equal or older than  %s  "),$rlt['cutoff_day']));
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, sprintf(lang("Regenarate All Report has lock - should not be equal or older than  %s  "),$rlt['cutoff_day']));
				return redirect('/system_management/other_functions');
			}
		}

		$this->utils->debug_log('post_rebuild_games_total','by_date_from', $by_date_from, 'by_date_to', $by_date_to, 'rebuild_hour',$rebuild_hour, 'rebuild_minute' , $rebuild_minute);

		$success=true;
		$dry_run=$this->input->post('dry_run')=='true';

		$dry_run= $dry_run ? 'true' : 'false';

		$this->load->library(['lib_queue', 'language_function']);
		$this->load->model(['queue_result']);
		$caller=$this->authentication->getUserId();
		$state=null;
		$lang=$this->language_function->getCurrentLanguage();
		//$commandFunc = 'do_rebuild_games_total_job';
		$funcName = 'rebuild_games_total';
		$callerType = Queue_result::CALLER_TYPE_ADMIN;
		$systemId = Queue_result::SYSTEM_UNKNOWN;

		$params = ['endDateTimeStr' => $by_date_to,
			'fromDateTimeStr' => $by_date_from,
			'rebuild_hour' => $rebuild_hour,
			'rebuild_minute' => $rebuild_minute,
			'dry_run'=>$dry_run,
			];


		//$token = $this->lib_queue->addAnyJob($commandFunc, $funcName, $params, $callerType, $caller, $state, $lang, $systemId);
		//redirect('/system_management/common_queue/' . $token);

		$token=$this->lib_queue->commonAddRemoteJob($systemId,$funcName, $params, $callerType, $caller, $state, $lang);
		//goto queue page
		redirect('/system_management/common_queue/'.$token);
	}

	public function post_batch_sync_balance(){
		$data = array(
			'title' => lang('Dev Functions'),
			'sidebar' => 'system_management/sidebar',
			'activenav' => 'dev_functions'
		);

		// -- Check if user has permission
		if (!$this->permissions->checkPermissions('dev_functions') || !$this->users->isT1User($this->authentication->getUsername())) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$params = array(
			'mode' 	 		=> $this->input->post('mode') ?: 'available',
			'dry_run' 	 	=> $this->input->post('dry_run') ? 'true' : 'false',
			'max_number' 	=> $this->input->post('max_number') ?: '10',
			'apiId' 	 	=> $this->input->post('apiId') ?: '',
		);

		$this->utils->debug_log('post_batch_sync_balance','mode', $params['mode'], 'dry_run', $params['dry_run'], 'max_number', $params['max_number'], 'apiId', $params['apiId'] );

		$success = true;

		$this->load->library(['lib_queue', 'language_function']);
		$this->load->model(['queue_result']);

		$caller = $this->authentication->getUserId();
		$state 	= null;
		$lang 	= $this->language_function->getCurrentLanguage();

		$funcName 	= 'batch_sync_balance_by';
		$callerType = Queue_result::CALLER_TYPE_ADMIN;
		$systemId 	= Queue_result::SYSTEM_UNKNOWN;

		$token = $this->lib_queue->addRemoteBatchSyncBalanceByJob($systemId, $funcName, $params, $callerType, $caller, $state, $lang);

		redirect('/system_management/common_queue/'.$token);
	}

	/**
	 * View remote log file content
	 *
	 * @param  string $token
	 * @param  string $logfile
	 * @return HTML template
	 */
	public function view_remote_log($token,$logfile){

		$data = array('title' => lang('View Remote Log'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'dev_functions');

		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();

		// -- Check if user has permission
		if (!$this->permissions->checkPermissions('dev_functions') || !$this->users->isT1User($this->authentication->getUsername()) || empty($token) || empty($logfile)) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$this->load->model('queue_result');
		$queue_result = $this->queue_result->getResult($token);

		// -- check if token really exists
		if(empty($queue_result))
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);

		// -- get file path
		$logfile = rtrim($logfile,'.log') . '.log';
		$remote_log_file_path = $this->utils->getSharingUploadPath('/remote_logs');
		$file_path = rtrim($remote_log_file_path, '/').'/'.$logfile;
		$file_content = '';

		// -- retrieve file content
		$file_size = @filesize($file_path);
		$file_size_limit = $this->utils->getConfig('remote_log_viewing_file_size_limit');

		if($file_size !== FALSE && $file_size > $file_size_limit){
			$data['filesize_limit'] = $file_size_limit;
		}
		else{
			// -- retrieve file content
			$file_content = @file_get_contents($file_path);

			if($file_content === FALSE) {
				$this->utils->error_log('FAILED TO OPEN FILE: '.$file_path);

				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Remote log file does not exist.'));
				return $this->loadDefaultTemplate(null, null, array('title' => $data['title']), $data['sidebar'], null, null, true);
			}
		}

		// -- prepare data
		$data['file_content'] = $file_content;
		$data['logfile'] = $logfile;
		$data['token'] = $token;


		$this->loadDefaultTemplate(
			['resources/js/highlight.pack.js'],
			['resources/css/general/style.css',
			'resources/css/hljs.tomorrow.css'],
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			'includes/parse_remote_log', $data, true);

		// }
	}

	public function other_functions(){
		$data = array('title' => lang('Dev Functions'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'system');

		if (!$this->permissions->checkPermissions('dev_functions') || !$this->users->isT1User($this->authentication->getUsername())) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$this->load->model(array('operatorglobalsettings','external_system', 'marketing', 'transactions'));
		$this->load->library(['lib_queue', 'language_function']);

		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$render = true;

		$this->addBoxDialogToTemplate();

		$by_date_to = $this->utils->getNowForMysql();
		$by_date_from = (new DateTime($by_date_to))->modify('-1 hour')->format('Y-m-d H').':00:00';

		$lock_rebuild_reports_range = $this->utils->getConfig('lock_rebuild_reports_range');
		if(!empty($lock_rebuild_reports_range)){
			$data['lock_rebuild_reports_range'] = $this->utils->isRebuildReportDateLocked($lock_rebuild_reports_range,null,null,$rlt,true);
		}else{
			$data['lock_rebuild_reports_range'] = null;
		}
		$data['by_date_from']  = $by_date_from;
		$data['by_date_to']    = $by_date_to;
		$data['cashback_date'] = null;
		$data['playerName']    = null;
		//show queue server
		$data['queue_server_info'] = $this->utils->getConfig('rabbitmq_server');
        $data['redis_channel_key'] = $this->lib_queue->getRabbitmqQueueKey();


        // -- get all game API from DB
        $all_apis = $this->external_system->getAllGameApisWithSystemNameAndStatus() ?: array();

        // -- get all ignored APIs via config
		$ignore_apis = $this->utils->getConfig('og_sync_ignore_api') ?: array();

		// -- get all APIs that are currently under maintenance
        $maintenance_apis = array_column($this->external_system->getGameApiMaintenanceOrPauseSyncing('maintenance_mode'), 'id');

        // -- get all paused syncing APIs via DB
        $pauseSync_apis = array_column($this->external_system->getGameApiMaintenanceOrPauseSyncing('pause_sync'), 'id');

    	// -- get all active java synced apis
    	$java_synced_apis = array_column($this->external_system->getAllApiActivelySyncedThruJava(), 'id');


    	foreach ($all_apis as $all_api_key => &$API) {
    		$API['sync_status'] = null;
    		$is_java = false;

    		// -- Set all Java APIs
    		if(in_array($API['id'], $java_synced_apis)){
    			$is_java = true;
    			$API['sync_status'] = lang('Java');
    		}

    		// -- Check if api PHP syncing is paused or not
    		if( !in_array($API['id'], $ignore_apis) &&
				!in_array($API['id'], $maintenance_apis) &&
				!in_array($API['id'], $pauseSync_apis) &&
				$API['status'] == External_system::STATUS_NORMAL) {
				if($is_java)
					$API['sync_status'] = lang('Java & PHP');
				else
					$API['sync_status'] = lang('PHP');
			} elseif (!$is_java){
				$API['sync_status'] = lang('Paused');
			}
    	}


        $data['api_with_sync_method'] = $all_apis;

        $dlu_time = $this->transactions->getDashboardLastUpdateTime();
        $data = array_merge($data, $dlu_time);

		$this->loadDefaultTemplate(
			['resources/js/highlight.pack.js',
			'resources/js/datatables.min.js'],
			['resources/css/general/style.css',
			'resources/css/hljs.tomorrow.css',
			'resources/css/datatables.min.css'],
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			'system_management/view_other_functions', $data, $render);
	}

	public function view_task_list(){
		$data = array('title' => lang('Task List'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'system');

		if (!$this->permissions->checkPermissions('view_task_list')) {
            return $this->error_redirection();
        }

        $userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$render = true;

		$start_today = date("Y-m-d") . ' 00:00:00';
		$end_today = date("Y-m-d") . ' 23:59:59';
		$data['conditions'] = $this->safeLoadParams(array(
			'datetime_from' => $this->utils->getTodayForMysql().' '.Utils::FIRST_TIME,
			'datetime_to' => $this->utils->getTodayForMysql().' '.Utils::LAST_TIME,
			'username' => '',
		));

		$data['export_report_permission']=!!$this->permissions->checkPermissions('export_task_list_report');

		$this->addBoxDialogToTemplate();

		$this->loadDefaultTemplate([
				'resources/js/datatables.min.js',
			],
			['resources/css/general/style.css',
			'resources/css/datatables.min.css'],
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			'system_management/view_task_list', $data, $render);
	}

	public function common_queue_syncgamelogs($token){
		$data = array('title' => lang('Task Progress'), 'sidebar' => 'player_management/sidebar',
			'activenav' => 'view_task_list');
		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$render = true;

		if (empty($userId)) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$this->load->model(['queue_result']);
		$data['result_token']=$token;
		$affId = $this->getSessionAffId();
		if(!empty($affId)){
			$this->template->write_view('nav_right', 'affiliate/navigation');
		}
		$this->loadDefaultTemplate(array(),
			array('resources/css/general/style.css'),
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			'includes/game_logs_task_progress', $data, $render);
	}

	public function common_queue($token){
		$data = array('title' => lang('Task Progress'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'view_task_list');
		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$render = true;

		if (empty($userId)) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$this->load->model(['queue_result']);

		$data['result_token']=$token;

		$affId = $this->getSessionAffId();
		if(!empty($affId)){
			$this->template->write_view('nav_right', 'affiliate/navigation');
		}

		//load function template
		$data['result_template']='common_task_result.php';
		$data['func_name']=null;
		//load queue first
		$qRlt=$this->queue_result->getResult($token);
		$data['is_remote_export'] = substr($qRlt['func_name'], 0, 6) == 'remote';
		$data['is_remote_sync_game_logs'] = strpos($qRlt['func_name'], 'sync_game_logs');
		if(!empty($qRlt)){
			//check file exists
			$viewfile=$qRlt['func_name'].'_result.php';
			if(file_exists(APPPATH.'/views/includes/'.$viewfile)){
				$data['result_template']=$viewfile;
			}

			$data['func_name']=lang($qRlt['func_name']);
		}else{
			$this->utils->error_log('not found token', $token);
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$this->loadDefaultTemplate(
			['resources/js/highlight.pack.js'],
			['resources/css/general/style.css',
			'resources/css/hljs.tomorrow.css'],
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			'includes/task_progress', $data, $render);
	}

	public function check_queue($token){
		$rlt=['success'=>true, 'done'=>false];
		$this->load->model(['queue_result']);
		$queue_result=$this->queue_result->getResult($token);
		$rlt['queue_result']=$this->utils->decodeJson($queue_result['result']);
		$rlt['full_params']=$this->utils->decodeJson($queue_result['full_params']);
		$rlt['final_result']=$this->utils->decodeJson($queue_result['final_result']);

		$log_server_link=$this->utils->getConfig('log_server_link');

		// if(!empty($rlt['queue_result']) && is_array($rlt['queue_result'])){
			//add link to request id
			// foreach ($rlt['queue_result'] as &$qRlt) {
			// 	if(isset($qRlt['request_id']) && !empty($qRlt['request_id'])){
			// 		$link=str_replace('{{request_id}}', $qRlt['request_id'], $log_server_link);
			// 		$qRlt['request_id']=["link"=>$link, "id"=>$qRlt['request_id']];
			// 	}
			// }
		// }

		$rlt['done']=isset($queue_result['status']) ? $queue_result['status']==Queue_result::STATUS_DONE : false;

		$paramsAndResult=['full_params'=>$rlt['full_params'], 'result'=>$rlt['queue_result'], 'final_result'=>$rlt['final_result']];

		$rlt['queue_original_result']=json_encode($paramsAndResult,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
		$rlt['status']=@$queue_result['status'];
		$this->returnJsonResult($rlt);
	}

	public function view_resp_result(){
		$data = array('title' => lang('Response Result'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'view_resp_result');

		if (!$this->permissions->checkPermissions('view_resp_result') || !$this->users->isT1User($this->authentication->getUsername())) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$render = true;

		$start_today = date("Y-m-d") . ' 00:00:00';
		$end_today = date("Y-m-d") . ' 23:59:59';
		$data['conditions'] = $this->safeLoadParams(array(
			'datetime_from' => $this->utils->getTodayForMysql().' '.Utils::FIRST_TIME,
			'datetime_to' => $this->utils->getTodayForMysql().' '.Utils::LAST_TIME,
			'api_id' => '',
			'method'=>'',
			'order_id'=>'',
			'result_id'=>'',
			'mobile'=>'',
			'email'=>'',
			'username'=>'',
            'flag'=>'',
			'no_query_balance'=>'',
		));

		$data['conditions']['show_sync_data']=$this->safeGetParam('show_sync_data', true, true);

		$data['conditions']['show_gamegateway_api']=$this->safeGetParam('show_gamegateway_api', true, true);

		$this->utils->debug_log($data['conditions']);

		$data['apiMap'] = $this->utils->getAllSystemMap();
		$data['apiMap']['game'] = $this->utils->getGameSystemMap();
		$data['apiMap']['payment'] = $this->utils->getPaymentSystemMap();
		//This is not used anymore.
		$data['export_report_permission']=!!$this->permissions->checkPermissions('export_report');

		asort($data['apiMap']['game']);
		asort($data['apiMap']['payment']);

		$this->addBoxDialogToTemplate();

		$this->loadDefaultTemplate([
				'resources/js/datatables.min.js',
			],
			['resources/css/general/style.css',
			'resources/css/datatables.min.css'],
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			'system_management/view_resp_result', $data, $render);
	}

	public function view_sms_report(){
		$data = array('title' => lang('SMS Report'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'system');

		if (!$this->permissions->checkPermissions('view_sms_report')) {
			return $this->error_redirection();
		}

		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$render = true;

		$start_today = date("Y-m-d") . ' 00:00:00';
		$end_today = date("Y-m-d") . ' 23:59:59';
		$data['conditions'] = $this->safeLoadParams(array(
			'datetime_from' => $this->utils->getTodayForMysql().' '.Utils::FIRST_TIME,
			'datetime_to' => $this->utils->getTodayForMysql().' '.Utils::LAST_TIME,
			'mobile'=>'',
            'flag'=>'',
		));


		// This is not used anymore.
		$data['export_report_permission']=!!$this->permissions->checkPermissions('export_report');

		$this->addBoxDialogToTemplate();

		$this->loadDefaultTemplate([
				'resources/js/datatables.min.js',
			],
			['resources/css/general/style.css',
			'resources/css/datatables.min.css'],
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			'system_management/view_sms_report', $data, $render);
	}

	public function view_smtp_api_report(){
		$data = array('title' => lang('SMTP API Report'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'system');

		if (!$this->permissions->checkPermissions('view_smtp_api_report')) {
			return $this->error_redirection();
		}

		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$render = true;

		$start_today = date("Y-m-d") . ' 00:00:00';
		$end_today = date("Y-m-d") . ' 23:59:59';
		$data['conditions'] = $this->safeLoadParams(array(
			'datetime_from' => $this->utils->getTodayForMysql().' '.Utils::FIRST_TIME,
			'datetime_to' => $this->utils->getTodayForMysql().' '.Utils::LAST_TIME,
			'username'=>'',
			'email'=>'',
            'flag'=>'',
		));


		$data['export_report_permission']=!!$this->permissions->checkPermissions('export_report');

		$this->addBoxDialogToTemplate();

		$this->loadDefaultTemplate([
				'resources/js/datatables.min.js',
				'resources/js/system_management/system_management.js' ,
			],
			['resources/css/general/style.css',
			'resources/css/datatables.min.css'],
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			'system_management/view_smtp_report', $data, $render);
	}

	public function response_result_detail(){

		if (!$this->permissions->checkAnyPermissions(['view_resp_result', 'transfer_request']) ) {
			return show_error('No permission', 403);
		}

		$result=['success'=>false, 'message'=>lang('Not found')];
		$id=$this->input->get('id');
		if(!empty($id)){

			$this->load->model(['response_result']);

			$rlt=$this->response_result->getRespResultByTableField($id);
			$rlt['original_content']=json_encode(json_decode(@$rlt['content']), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR);
			$rlt['resp_file_id']=$id;

			$result['success']=true;
			$result['message']=null;
			$result['response_result']=$rlt;
		}

		$this->returnJsonResult($result);
	}

	public function risk_score_setting(){

		$data = array('title' => lang('risk score'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'system');

		if (!$this->permissions->checkPermissions('risk_score_settings')) {
			return $this->error_redirection();
		}

		$this->load->model(array('risk_score_model'));

		$data['risk_score'] = $this->risk_score_model->getRiskScoreCategory();
		//var_dump($data['risk_score_category']);die();
		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$render = true;

		$this->loadDefaultTemplate(
			array(
				'resources/js/highlight.pack.js',
				'resources/js/ace/ace.js',
				'resources/js/ace/mode-json.js',
				'resources/js/ace/theme-tomorrow.js'
			),
			array(
				'resources/css/general/style.css',
				'resources/css/hljs.tomorrow.css'
			),
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			'system_management/view_risk_score', $data, $render);
	}

	public function update_risk_score_setting(){
		$input = $this->input->post();
		$this->load->model(array('risk_score_model'));

		foreach ($input as $key => $value) {
			$data['rules'] = $value;
				$this->risk_score_model->updateRiksScoreByCategoryName($key,$data);
		}

		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save system settings successfully'));
	}

	public function kyc_setting(){

		$data = array('title' => lang('KYC'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'system');

		if (!$this->permissions->checkPermissions('kyc_settings')) {
			return $this->error_redirection();
		}

		$this->load->model(array('kyc_status_model','risk_score_model'));

		$kyc_list = $this->kyc_status_model->getAllKycStatus();
		$risk_score_info = $this->risk_score_model->getRiskScoreInfo(self::RC);
		$risk_score_list = json_decode($risk_score_info['rules'],true);

		$data['kyc_list'] = $kyc_list;
		$data['risk_score_list'] = $risk_score_list;
		$data['target_function'] = $this->getTargetFunctionList();
		$data['renderChart'] = $this->render_kyc_riskscore_chart();
		//echo "<pre>";print_r($data['renderChart']);die();
		$lang = $this->language_function->getCurrentLanguage();

		if(!empty($data['kyc_list'])){
			foreach ($data['kyc_list'] as $key => $value) {
				switch ($lang) {
					case 1:
						$data['kyc_list'][$key]['description'] = $value['description_english'];
						break;
					case 2:
						$data['kyc_list'][$key]['description'] = $value['description_chinese'];
						break;
					case 3:
						$data['kyc_list'][$key]['description'] = $value['description_indonesian'];
						break;
					case 4:
						$data['kyc_list'][$key]['description'] = $value['description_vietnamese'];
						break;
					default:
						$data['kyc_list'][$key]['description'] = $value['description_english'];
						break;
				}
			}
		}


		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$render = true;

		$this->loadDefaultTemplate(
			array(
				'resources/js/system_management/kyc_settings.js'
			),
			array(
				'resources/css/general/style.css',
				'resources/css/hljs.tomorrow.css'
			),
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			'system_management/view_kyc', $data, $render);
	}

	public function post_adjust_game_report(){

		$data = array('title' => lang('Sync Game Logs'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'sync_game_logs');

		$password=$this->input->post('password');

		//permission and only allow superadmin
		if (!$this->permissions->checkPermissions('sync_game_logs') || !$this->authentication->isSuperAdmin()
				|| $password!=$this->utils->getConfig('password_adjust_game_report')) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$this->load->model(['total_player_game_minute']);

		//insert into
		$success=$this->dbtransOnly(function() use(&$message){

			$success=false;
			$adjust_year_month=$this->input->post('adjust_year_month');
			$adjust_content=$this->input->post('adjust_content');

			$message='adjust_year_month: '.$adjust_year_month.', adjust_content:'.$adjust_content;


			//format: username,game_description_id,bet,result
			$adjust_arr=str_getcsv($adjust_content, "\n");
			$adjust_totals=[];
			foreach ($adjust_arr as $row) {
				$arr=str_getcsv($row, ',');

				$this->utils->debug_log('load content', $arr, $row);
				$adjust_totals[]=[
					'username'=>$arr[0],
					'game_description_id'=>$arr[1],
					'bet'=>$arr[2],
					'result'=>$arr[3],
				];
			}

			if(!empty($adjust_year_month) && !empty($adjust_content)){
				//insert into totals
				$success=$this->total_player_game_minute->adjust_month_data($adjust_year_month, $adjust_totals);
			}

			return $success;

		});

		//go back
	    if ($success) {
	        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Adjust Game Report').' '.$message);
	    } else {
	        $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message').' '.$message);
	    }

		redirect('/system_management/other_functions');
	}

	public function post_clear_memory_cache(){

		$data = array('title' => lang('Clear memory cache'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'dev_functions');

		if (!$this->permissions->checkPermissions('dev_functions') || !$this->users->isT1User($this->authentication->getUsername())) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$success=$this->utils->deleteCache();

	    if ($success) {
	        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Clear cache successfully'));
	    } else {
	        $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Clear failed').' '.$resp);
	    }

		redirect('/system_management/other_functions');
	}

	/**
	 * overview : view withdrawal declined category
	 *
	 * detail: view the lists withdrawal declined category
	 * @return load template
	 */
	public function view_withdrawal_declined_category() {
		$data = array('title' => lang('Withdrawal Declined Category'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'system');
		if (!$this->permissions->checkPermissions('view_withdrawal_declined_category')) {

			$this->error_redirection();
		} else {
			$this->load->model(array('roles'));
			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active', 'system_crumb' => 'active'));
			}

			$data['managements'] = $this->reports->getDistinct('management');
			$data['roles'] = $this->roles->retrieveAllRoles();

			$userId = $this->authentication->getUserId();
			$username = $this->authentication->getUsername();
			$render = true;

			$this->loadDefaultTemplate(
			array('resources/js/datatables.min.js','resources/js/system_management/common_category.js'),
			array('resources/css/general/style.css','resources/css/datatables.min.css'),
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			'system_management/view_withdrawal_declined_category', $data, $render);
		}
	}

	public function add_update_category(){
		$input = $this->input->post();
		$this->load->model(array('common_category'));
		$data = array();
		$response = array();
		if(!empty($input)){
			if(isset($input['category_name'])){
				$data['category_name'] = '_json:'.json_encode($input['category_name']);
			}
			if(isset($input['category_type'])){
				$data['category_type'] = $input['category_type'];
			}
			if(isset($input['order_by'])){
				$data['order_by'] = $input['order_by'];
			}
			if(isset($input['category_id'])){
				$data['id'] = $input['category_id'];
			}

			$queryReponse = $this->common_category->addUpdateCategory($data);
			if($queryReponse){
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('sys.gt25'));

			} else {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('sys.gt26'));
			}
		}
		$url = "";
		if(isset($input['category_type'])){
			switch ($input['category_type']) {
				case common_category::CATEGORY_WITHRAWAL_DECLINED:
					$url = 'view_withdrawal_declined_category';
					if($queryReponse){
						$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Add / Update Withdrawal Declined Category', "User " . $this->authentication->getUsername() . " add / update Withdrawal Declined Category");
					}
					break;
				case common_category::CATEGORY_ADJUSTMENT :
					$url = 'view_adjustment_category';
					if($queryReponse){
						$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Add / Update Adjustment Category', "User " . $this->authentication->getUsername() . " add / update Adjustment Category");
					}
					break;
				default:
					$url = 'view_withdrawal_declined_category';
					break;
			}
		} else {
			$url = 'view_withdrawal_declined_category';
		}
		redirect('/system_management/'.$url);
	}

	public function getCategoryById(){
		$input = $this->input->post();
		$response = null;
		if(!empty($input)) {
			if(isset($input['id'])){
				$this->load->model(array('common_category'));
				$response = $this->common_category->getCategoryInfoById($input['id']);
			}
		}
		if ($this->input->is_ajax_request()) {
			$this->returnJsonResult($response);
			return;
		}

		return $response;
	}

	public function updateStatusCategoryById(){
		$input = $this->input->post();
		$response = null;
		if(!empty($input)) {
			if(isset($input['id'])){
				$this->load->model(array('common_category'));
				$queryReponse = $this->common_category->updateCategoryById($input);

				if($queryReponse){
					switch ($input['status']) {
						case common_category::CATEGORY_STATUS_INACTIVE:
							$message = lang('Category status succesfully change to inactive!');
							break;
						case common_category::CATEGORY_STATUS_ACTIVE:
							$message = lang('Category status succesfully change to active!');
							break;
						case common_category::CATEGORY_STATUS_DELETED:
							$message = lang('Successfully Deleted!');
							break;
					}
					$response = array('status' => 'error', 'msg' => $message);
				} else {
					$response = array('status' => 'error', 'msg' => lang('sys.gt26'));
				}
			}
		}
		if ($this->input->is_ajax_request()) {
			$this->returnJsonResult($response);
			return;
		}

		return $response;
	}

	/**
	 * overview : view adjustment category
	 *
	 * detail: view the lists adjustment category
	 * @return load template
	 */
	public function view_adjustment_category() {
		$data = array('title' => lang('Adjustment Category'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'system');
		if (!$this->permissions->checkPermissions('view_adjustment_category')) {

			$this->error_redirection();
		} else {
			$this->load->model(array('roles'));
			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active', 'system_crumb' => 'active'));
			}

			// -- OGP-10383 | i will comment this line; this is no longer used for view adjustment category
			//$data['managements'] = $this->reports->getDistinct('management');
			$data['roles'] = $this->roles->retrieveAllRoles();

			$userId = $this->authentication->getUserId();
			$username = $this->authentication->getUsername();
			$render = true;

			$this->loadDefaultTemplate(
			array('resources/js/datatables.min.js','resources/js/system_management/common_category.js'),
			array('resources/css/general/style.css','resources/css/datatables.min.css'),
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			'system_management/view_adjustment_category', $data, $render);
		}
	}

	public function download_response_result($id){

		if (!$this->permissions->checkAnyPermissions(['view_resp_result', 'transfer_request']) ) {
			return show_error('No permission', 403);
		}

		//only allow admin
		if(!$this->users->isT1Admin($this->authentication->getUsername())){
			return show_error('No permission', 403);
		}

		$result=['success'=>false, 'message'=>lang('Not found')];
		// $id=$this->input->get('id');
		if(!empty($id)){

			$this->load->model(['response_result']);

			$rlt=$this->response_result->getRespFileInfoByTableField($id);

			// $rlt['original_content']=json_encode(json_decode(@$rlt['content']), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR);
			//load response result file
			// $rlt['original_content']= !empty($rlt['filepath']) ? $this->utils->loadResponseFile($rlt['filepath']) : $rlt['status_text'];

			// if(!empty($rlt['original_content'])){
			// 	$rlt['original_content']=htmlspecialchars($rlt['original_content']);
			// }

			if(!empty($rlt)){
				$result['success']=true;
				$result['message']=null;
				// $result['response_result']=$rlt;

				$result['content']=$rlt['content'];
			}

		}

		$this->returnJsonResult($result);
	}

	/**
	 * Get the Goder and Decoded Data
	 * @param string $decodeStr The string maybe the following formats,
	 * - http query string
	 * - json string
	 * - xml string
	 * @return array $return The formats,
	 * - $return['data'] srting The Data after decode.
	 * - $return['coder'] string The Coder for decode, ex: parse_str, json_decode and simplexml_load_string.
	 *
	 */
	public function getCoderAndDecodeDataFromEncodeStr($decodeStr){
		$return = array();
		$decodeData = NULL;
		// $b64DecodeStr = base64_decode( $decode_content['_RAW_POST'] );
		if( empty($decodeData) ){
			$json_decode = json_decode($decodeStr);
			if( ! empty($json_decode) ){
				$decodeData = $decodeData;
				$coder = 'json_decode';
			}
		}

		if( empty($decodeData) ){
			$xml = simplexml_load_string($decodeStr);
			if( ! empty($xml) ){
				$decodeData = $decodeStr;
				$coder = 'simplexml_load_string';
			}
		}

		if( empty($decodeData) ){
			parse_str($decodeStr, $parse_str);
			if( ! isset($parse_str) ){
				$decodeData = $decodeStr;
				$coder = 'parse_str';
			}
		}

		$return['data'] = $decodeData;
		$return['coder'] = $coder;
		return $return;
	} // EOF getCoderAndDecodeDataFromEncodeStr
	/**
	 * Get MINE for httpCall().
	 * @param string $coder Ref.to result['coder'] of getCoderAndDecodeDataFromEncodeStr().
	 * @param string $data Ref.to result['data'] of getCoderAndDecodeDataFromEncodeStr().
	 * @return string $Mine The Mine-type for httpCall().
	 */
	public function getMineFromCoderAndData($coder, $data){
		$Mine = NULL;
		switch ($coder){
			case 'parse_str':{
				$Mine = \Httpful\Mime::FORM;
				break;
			}
			case 'json_decode':{
				$Mine = \Httpful\Mime::FORM;
				break;
			}
			case 'simplexml_load_string':{
				$Mine = \Httpful\Mime::FORM;
				break;
			}
		}
		return $Mine;
	} // EOF getMineFromCoderAndData

	/**
	 * Resend the request by resp_XXX.id
	 *
	 * OGP-12954 在 payment api callback request 內加上可重複發送的機制
	 *
	 * @param string $respId The table name and resp_XXX.id, ex: "resp_20190703.22"
	 * @return void Echo Json String for tip something, result/view uri after resend at admin.
	 */
	public function resend_response_content($respId = NULL){
		$status = '0'; // 0: false , 1:true.
		$allow_resend = false;

		$isSourceSaleOrderNotFind = true; // Not Foind Source SaleOrder with $respId.
		$isRespIdEmpty = true; // $respId is empty?
		$isloadExternalSystemLib = false; // Check loadExternalSystemLib is loaded.
		$isBuildOrderForResend = false; //

		#check permission
		if (!$this->permissions->checkAnyPermissions(['view_resp_result', 'transfer_request']) ) {
			return show_error('No permission', 403);
		}
		if(!$this->users->isT1Admin($this->authentication->getUsername())){
			return show_error('No permission', 403);
		}

		$this->load->model(['sale_order', 'sale_orders_status_history', 'sale_orders_notes']);
		// like function autoDeposit3rdParty  NOooooo...
		// Resend like that form 3rd payment provider after callback .

		if(!empty($respId)){
			$isRespIdEmpty = false;
		}
		$request = $this->getInputGetAndPost();
		if( ! empty( $request['respId'] ) ){
			$respId = $request['respId'];
			$isRespIdEmpty = false;
		}


		if( ! $isRespIdEmpty ){
			$respFileInfoByTableField = $this->response_result->getRespFileInfoByTableField($respId);

			$jsonStr = json_encode(array());
			if( ! empty($respFileInfoByTableField['content']['content']) ){
				$jsonStr = $respFileInfoByTableField['content']['content'];
			}
			if( ! empty($respFileInfoByTableField['content']['resultText']) ){
				$jsonStr = $respFileInfoByTableField['content']['resultText'];
			}
			$decode_content = json_decode($jsonStr, true); // json_decode( json_decode(resp_20190712.content).resultText )

			$data['response_result_id'] = $respFileInfoByTableField['response_result_id'];
			// $data['request_api'] = $respFileInfoByTableField['content']['request_api'];

			// $data['response_result_id'] = '222'; // @todo test
			// $respFileInfoByTableField['content']['related_id2'] = 'D146418550384'; // @todo test

			$responseResultInfo = $this->response_result->getResponseResultInfoById($data['response_result_id']);
			// Get sale_order from secure_id
			$saleOrder = (array)$this->sale_order->getSaleOrderBySecureId($respFileInfoByTableField['content']['related_id2']);

			if( ! empty($saleOrder) ){
				$isSourceSaleOrderNotFind = false;
			}

			/// Clone New sale_order for resend
			// loading ExternalSystemLib
			$controller = $this;
			try {
				list($loaded, $apiClassName) = $controller->utils->loadExternalSystemLib( $saleOrder['system_id'] );
			} catch (Exception $e) {
				$this->utils->error_log('loadExternalSystemLib error, system_id='. $saleOrder['system_id']. ', e: ', $e);
				$loaded = 0;
			}

			if($loaded){
				$isloadExternalSystemLib = true;
			}

			$this->utils->debug_log('loadExternalSystemLib.loaded:', $loaded);
			$this->utils->debug_log('loadExternalSystemLib.apiClassName:', $apiClassName);
			if( ! $isloadExternalSystemLib ){
				$msg = 'loading ExternalSystemLib Failed.';
			}else{
				$api = $this->$apiClassName;

				if(false){ // New order for resend
					$player_id = $saleOrder['player_id'];
					$deposit_amount = $saleOrder['amount'];
					$player_promo_id = NULL; // default
					if( ! empty($saleOrder['player_promo_id'])){
						$player_promo_id = ['player_promo_id'];
					}
					$extra_info_order = json_encode( array() );
					if( ! empty($respFileInfoByTableField['content']['content']) ){
						$extra_info_order = $respFileInfoByTableField['content']['content'];
					}
					$sub_wallet_id = $saleOrder['sub_wallet_id'];
					$group_level_id = $saleOrder['group_level_id'];
					$player_deposit_reference_no = $saleOrder['player_deposit_reference_no'];
					$promo_info = NULL; // default
					if( ! empty($saleOrder['player_promo_id'])){
						$promo_info = $saleOrder['player_promo_id'];
					}
					$deposit_time = NULL; // default
					if( $saleOrder['player_deposit_time'] != '0000-00-00 00:00:00' ){
						$deposit_time = $saleOrder['player_deposit_time'];
					}
					/// Build order for resend - ref. to autoDeposit3rdParty().
					// $orderId for resend
					$orderId = $api->createSaleOrder( $player_id // #1
						, $deposit_amount // #2
						, $player_promo_id // #3
						, $extra_info_order // #4
						, $sub_wallet_id // #5
						, $group_level_id // #6
						, $this->utils->is_mobile() // #7
						, $player_deposit_reference_no // #8
						, $deposit_time // #9
						, $promo_info // #10
					);
				}else{
					// resend under the same order.
					$orderId = $saleOrder['id'];
				}

				if( !empty($orderId) ){
					$isBuildOrderForResend = true; // pass assign source order.
				}
			}

			if( ! $isSourceSaleOrderNotFind
				&& $isBuildOrderForResend
			){
				$actionlog = 'Resend form '. $saleOrder['secure_id']; // $id = source orderId.
				$this->sale_orders_notes->add($actionlog, Users::SUPER_ADMIN_ID, Sale_orders_notes::ACTION_LOG, $orderId);
				if( ! empty($saleOrder['direct_pay_extra_info'])){
					$this->sale_order->updateSaleOrderDirectPayExtraInfoById($orderId, $saleOrder['direct_pay_extra_info']);
				}
				$this->sale_orders_status_history->createSaleOrderStatusHistory($orderId,Sale_order::DEPOSIT_STATUS_RESEND_CALLBACK);
			}

			if( $isBuildOrderForResend
				&& $isloadExternalSystemLib
			){
				// CURL
				// $decode_content['_REQUEST']
				// $decode_content['_SERVER']['REQUEST_METHOD']
				// $url = $api->getNotifyUrl($orderId); // Fatal Error (E_ERROR): Call to private method Abstract_payment_api_yangpay::getNotifyUrl() from context 'System_management'
				$uri = '/callback/process/' . $api->getPlatformCode(). '/'. $orderId;
				if( ! empty($saleOrder['secure_id']) ){ // append source saleOrder.secure_id
					$uri .= '/?reSendBySecureId='. $saleOrder['secure_id']; // append resend info.
				}

				$url = $api->getServerCallbackUrl($uri);
				$mine = \Httpful\Mime::FORM;
				$params = array();
				$reSendType = NULL;
				if( ! empty($decode_content['_REQUEST']) ){
					$params = array_merge($params, $decode_content['_REQUEST']) ;
				}
				$method = $decode_content['_SERVER']['REQUEST_METHOD'];
				if( ! empty($decode_content['_RAW_POST']) ){

					$b64DecodeStr = base64_decode( $decode_content['_RAW_POST'] );
					$resultCoderAndData = $this->getCoderAndDecodeDataFromEncodeStr($b64DecodeStr);
					$mine = $this->getMineFromCoderAndData($resultCoderAndData['coder'], $resultCoderAndData['data']);
					$params = $resultCoderAndData['data']; // give up $params array, just send json data.
					$reSendType = $resultCoderAndData['coder'];
				}
				if( ! empty($decode_content['_RAW_POST_XML_JSON']) ){
					$b64DecodeStr = base64_decode( $decode_content['_RAW_POST_XML_JSON'] );
					$resultCoderAndData = $this->getCoderAndDecodeDataFromEncodeStr($b64DecodeStr);
					$mine = $this->getMineFromCoderAndData($resultCoderAndData['coder'], $resultCoderAndData['data']);
					$params = $resultCoderAndData['data']; // give up $params array, just send xml data.
					$reSendType = $resultCoderAndData['coder'];
				}

				$response = $api->resendHttpCall($orderId, $url, $params, $method, $mine, $reSendType); // Ref. to Abstract_payment_api::resendHttpCall
				if( ! empty($response) ){
					$msg = 'success';
					$status = '1';
				}
			} // EOF if( $isBuildOrderForResend && $isloadExternalSystemLib )
// print_r('<pre>$response:');
// print_r($response->raw_body );
// print_r('</pre>');
		} // EOF if( ! $isRespIdEmpty )

		$respJson = array();
		$respJson['status'] = $status; // 1 or 0 , completed / failed
		// $respJson['respFileInfoByTableField'] = $respFileInfoByTableField; // for debug
		// $respJson['responseResultInfo'] = $responseResultInfo; // for debug
		$respJson['decode_content'] = $decode_content;
		if( ! empty($saleOrder) ){
			// $isSourceSaleOrderNotFind = false;

			// patch for parse error
			unset($saleOrder['direct_pay_extra_info']);
			unset($saleOrder['payment_type_name']);
			unset($respJson['decode_content']['_SERVER']);

			if( ! empty($method) ){ // loading ExternalSystemLib Failed.
				$respJson['resentMethod'] = $method;
			}
			if( ! empty($url) ){ // loading ExternalSystemLib Failed.
				$respJson['resentUrl'] = $url;
			}
			if( ! empty($params) ){ // loading ExternalSystemLib Failed.
				$respJson['resentParams'] = $params;
			}
			$respJson['sourceSaleOrder'] = $saleOrder; // Source SaleOrder
		}

		if( ! empty($response) ){
			$respJson['response'] = $response->raw_body;
		}
		$respJson['msg'] = $msg;

		$this->utils->error_log('resend_response_content.respJson:', $respJson);
		$this->returnJsonResult($respJson);

	} // EOF resend_response_content

	/**
	 * Show Response Content By resp_XXX.id
	 *
	 * OGP-12954 在 payment api callback request 內加上可重複發送的機制
	 *
	 * @param string $id The table name and resp_XXX.id, ex: "resp_20190703.22"
	 */
	public function show_response_content($id){
		$this->load->model(['response_result']);

		$data = array('title' => lang('Response Content'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'view_resp_result');

		#check permission
		if (!$this->permissions->checkAnyPermissions(['view_resp_result', 'transfer_request']) ) {
			return show_error('No permission', 403);
		}
		if(!$this->users->isT1Admin($this->authentication->getUsername())){
			return show_error('No permission', 403);
		}
		if(!empty($id)){ // OGP-12954 新增「重發」按鈕。（ request_api = deposit ）
			$respFileInfoByTableField = $this->response_result->getRespFileInfoByTableField($id);
			$dcodeResultText = $this->getDecodeResultTextByRespId($id);

			if(!empty($respFileInfoByTableField)){
				$result = json_decode($respFileInfoByTableField['content']['resultText'] , true);
				if($result == NULL){
					$data['error'] = lang('Result Text not found');
				}
				else{
					$raw = []; #to check if callback are same
					$content = [];
					$data['fail_msg'] = '';
					$data['error_msg'] = '';
			        foreach ($result as $key => $value) {
			        	if($value == NULL || empty($value) || in_array($value, $raw, true)){
			        		continue;
			        	} elseif($key == 'fail_msg'){
			            	$data['fail_msg'] = $value;
			            } elseif(strpos($key,'error_msg') === 0){
			                $data['error_msg'] .= '<li>'.$value.'</li>';
			            } elseif($key == 'url'){
			            	$content[$key] = $value;
			            } elseif($key == 'content' || $key == '_RAW_POST' || $key == '_RAW_POST_XML_JSON'){
			            	if($key == '_RAW_POST' || $key == '_RAW_POST_XML_JSON'){
			            		$raw[$key] = $value;
			            		$value = base64_decode($value);
							}

			            	if(is_array($value)){
								$content[$key] = htmlentities(json_encode($value, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR));
			            	}
	                        else if(is_null(json_decode($value))){
	                            $content[$key] = htmlentities($value);
	                        }
	                        else{
	                            $content[$key] = htmlentities(json_encode(json_decode($value), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR));
							}
			            } elseif($key == 'params' || $key == 'callbackExtraInfo' || $key == '_REQUEST'){
			            	$raw[$key] = $value;
			            	if(is_array($value)){
								$content[$key] = htmlentities(json_encode($value, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR));
			            	} else {
			            		$content[$key] = $value;
			            	}
			            } elseif($key == '_SERVER'){
			                $data['server'] = $value;
			            }
			        }
					$data['content'] = $content;
				}
				if( ! empty($dcodeResultText) ){
					$data['dcodeResultText'] = $dcodeResultText;
				}
				// Condition for display resend-panel
				$data['request_api'] = $respFileInfoByTableField['content']['request_api'];
				$data['respIdStr'] = $id;
			}
			else{
				$data['error'] = lang('ID Not found');
			}
		}

		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$render = true;

		$this->loadDefaultTemplate(['resources/js/datatables.min.js',],
			['resources/css/general/style.css','resources/css/datatables.min.css'],
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			    'system_management/show_response_content', $data, $render);
	} // EOF show_response_content

	/**
	 * Get the Content of Callback from 3rd-party payment while response_results.request_api="deposit".
	 * @param string $respId The table name and resp_XXX.id, ex: "resp_20190703.22"
	 * @return object $decode_content The formats,
	 * - $decode_content['_REQUEST'] For resend params.
	 * - $decode_content['_SERVER'] For resend METHOD, Ref. to $decode_content['_SERVER']['REQUEST_METHOD']
	 * - $decode_content['_RAW_POST'] For handle resend, with httpCall().
	 * - $decode_content['_RAW_POST_XML_JSON'] For handle resend, with httpCall().
	 *
	 */
	public function getDecodeResultTextByRespId($respId){
		$this->load->model(['response_result']);
		$respFileInfoByTableField = $this->response_result->getRespFileInfoByTableField($respId);
			$jsonStr = json_encode(array());
			if( ! empty($respFileInfoByTableField['content']['content']) ){
				$jsonStr = $respFileInfoByTableField['content']['content'];
			}
			if( ! empty($respFileInfoByTableField['content']['resultText']) ){
				$jsonStr = $respFileInfoByTableField['content']['resultText'];
			}
			$decode_content = json_decode($jsonStr, true);
			return $decode_content;
	} // EOF getDecodeResultTextByRespId


    public function show_sms_content($id){
        $data = array('title' => lang('SMS Content'), 'sidebar' => 'system_management/sidebar',
            'activenav' => 'view_sms_result');

        #check permission
        if (!$this->permissions->checkPermissions('view_sms_report')) {
            return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
        }

        if(!empty($id)){
            $this->load->model(['response_result']);
            $result = $this->response_result->getRespFileInfoByTableField($id);
            if(!empty($result)){
                $result = json_decode($result['content']['resultText'] , true);
                if($result == NULL){
                    $data['error'] = lang('Result Text not found');
                }
                else{
                    $data['result'] = $result;
                }
            }
            else{
                $data['error'] = lang('ID Not found');
            }
        }

        $userId = $this->authentication->getUserId();
        $username = $this->authentication->getUsername();
        $render = true;
        $this->loadDefaultTemplate(['resources/js/datatables.min.js',],
            ['resources/css/general/style.css','resources/css/datatables.min.css'],
            array('title' => $data['title'],
                'activenav' => $data['activenav'],
                'userId' => $userId,
                'username' => $username), $data['sidebar'],
            'system_management/show_sms_content', $data, $render);
    }

	public function show_smtp_api_content($id){
		$data = array('title' => lang('SMTP API Content'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'view_smtp_api_result');

		#check permission
		if (!$this->permissions->checkPermissions('view_smtp_api_report')) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		if(!empty($id)){
			$this->load->model(['response_result']);
			$result = $this->response_result->getRespFileInfoByTableField($id);
			if(!empty($result)){
				$result = json_decode($result['content']['resultText'] , true);
				if($result == NULL){
					$data['error'] = lang('Result Text not found');
				}
				else{
					$data['result'] = $result;
				}
			}
			else{
				$data['error'] = lang('ID Not found');
			}
		}

		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$render = true;
		$this->loadDefaultTemplate(['resources/js/datatables.min.js',],
			['resources/css/general/style.css','resources/css/datatables.min.css'],
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			'system_management/show_smtp_content', $data, $render);
	}

    public function game_wallet_settings(){
        $data = array('title' => lang('Game Wallet Settings'), 'sidebar' => 'system_management/sidebar',
            'activenav' => 'system');

        #check permission
        if (!$this->permissions->checkPermissions('game_wallet_settings')) {
            return $this->error_redirection();
        }

        $data['game_wallet_settings'] = $this->operatorglobalsettings->getGameWalletSettings();

        $userId = $this->authentication->getUserId();
        $username = $this->authentication->getUsername();
        $render = true;
        $this->loadDefaultTemplate([ 'resources/js/system_management/system_management.js' ] ,
            ['resources/css/general/style.css'],
            array('title' => $data['title'],
                'activenav' => $data['activenav'],
                'userId' => $userId,
                'username' => $username), $data['sidebar'],
            'system_management/game_wallet_settings', $data, $render);
    }

    public function save_game_wallet_settings(){
        $data = array('title' => lang('Game Wallet Settings'), 'sidebar' => 'system_management/sidebar',
            'activenav' => 'game_wallet_settings');

        if (!$this->permissions->checkPermissions('game_wallet_settings')) {
            return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
        }

        $game_wallet_settings = json_decode($this->input->post('game_wallet_settings'), TRUE);

        if ($game_wallet_settings) {
            $this->operatorglobalsettings->syncSettingJson("game_wallet_settings", $game_wallet_settings , 'template');
            return $this->returnCommon(self::MESSAGE_TYPE_SUCCESS, "Game Wallet Settings successfully update.");
        } else {
            return $this->returnCommon(self::MESSAGE_TYPE_ERROR, lang('player.mp14'));
        }
    }

	public function run_task_again($oldToken){
		$data = array('title' => lang('Run task again'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'view_other_functions');

		if (!$this->permissions->checkPermissions('dev_functions') || !$this->users->isT1User($this->authentication->getUsername())) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}
		$token=null;
		$oldResult=$this->queue_result->getResult($oldToken);
		if(!empty($oldResult)){
			$token=$oldToken;

			//func name starts with remote
			if(substr($oldResult['func_name'], 0, 6)=='remote'){

				//try copy queue job and run again
				$token=$this->queue_result->runTaskAgain($oldResult, $oldToken);

				if(empty($token)){
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Run Task Again Failed'));
					$token=$oldToken;
					// return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);

				}else{
					$this->load->library(['lib_queue']);
					$this->lib_queue->addJobToRabbitMQ($token);
				}
			}else{
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Only Allow Remote Task to restart'));
			}

		}else{
			$this->utils->error_log('not found token', $oldToken);
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		redirect('/system_management/common_queue/'.$token);
	}

	public function post_debug_queue(){
		$data = array('title' => lang('Debug queue'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'dev_functions');

		if (!$this->permissions->checkPermissions('dev_functions') || !$this->users->isT1User($this->authentication->getUsername())) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$this->load->library(['lib_queue']);

		$callerType=Queue_result::CALLER_TYPE_ADMIN;
		$caller=$this->authentication->getUserId();
		$state=null;
		$lang=$this->language_function->getCurrentLanguage();
		$token=$this->lib_queue->addRemoteDebugQueue($callerType, $caller, $state, $lang);
		// $token=$this->lib_queue->triggerAsyncRemoteDebugEvent($callerType, $caller, $state, $lang);

	    if (!empty($token)) {

	        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Debug queue successfully'));
			return redirect('/system_management/common_queue/'.$token);

	    } else {
	        $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Debug queue failed').' '.$resp);
	    }

		redirect('/system_management/other_functions');
	}

	public function post_debug_async_event(){

		$data = array('title' => lang('Debug queue'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'dev_functions');

		if (!$this->permissions->checkPermissions('dev_functions') || !$this->users->isT1User($this->authentication->getUsername())) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$this->load->library(['lib_queue']);

		$callerType=Queue_result::CALLER_TYPE_ADMIN;
		$caller=$this->authentication->getUserId();
		$state=null;
		$lang=$this->language_function->getCurrentLanguage();
		// $token=$this->lib_queue->addRemoteDebugQueue($callerType, $caller, $state, $lang);
		$token=$this->lib_queue->triggerAsyncRemoteDebugEvent($callerType, $caller, $state, $lang);

	    if (!empty($token)) {

	        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Debug event successfully'));
			return redirect('/system_management/common_queue/'.$token);

	    } else {
	        $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Debug event failed').' '.$resp);
	    }

		redirect('/system_management/other_functions');
	}

	/**
	 * run remote job for sync t1 gamegateway
	 *
	 */
	public function post_sync_t1_gamegateway(){

		$data = array('title' => lang('Dev Functions'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'dev_functions');

		if (!$this->permissions->checkPermissions('sync_game_logs') || !$this->users->isT1User($this->authentication->getUsername())) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$by_date_from=$this->input->post('by_date_from');
		$by_date_to=$this->input->post('by_date_to');
		$playerName=$this->input->post('playerName');

		$this->utils->debug_log('post_sync_t1_gamegateway','by_date_from', $by_date_from, 'by_date_to', $by_date_to, 'playerName', $playerName);

		$success=true;

		$this->load->library(['lib_queue', 'language_function']);
		$this->load->model(['queue_result']);
		$caller=$this->authentication->getUserId();
		$state=null;
		$lang=$this->language_function->getCurrentLanguage();
		//$commandFunc = 'do_rebuild_games_total_job';
		$funcName = 'sync_t1_gamegateway';
		$callerType = Queue_result::CALLER_TYPE_ADMIN;
		$systemId = Queue_result::SYSTEM_UNKNOWN;

		$params = [
			'date_to' => $by_date_to,
			'date_from' => $by_date_from,
			'playerName' => $playerName,
		];

		$token=$this->lib_queue->commonAddRemoteJob($systemId,$funcName, $params, $callerType, $caller, $state, $lang);
		//goto queue page
		redirect('/system_management/common_queue/'.$token);
	}

	public function manage_currency(){
		$data = array('title' => lang('Manage Currency'), 'sidebar' => 'currency_management/sidebar',
			'activenav' => 'currency');

		if (!$this->permissions->checkPermissions('manage_currency')) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$this->load->model(array('operatorglobalsettings'));
		$this->load->library(['lib_queue', 'language_function']);

		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$render=true;
		$this->addBoxDialogToTemplate();

		$data['conditions']=[
			'player_username'=>$this->input->get('player_username'),
			'admin_username'=>$this->input->get('admin_username'),
			'affiliate_username'=>$this->input->get('affiliate_username'),
			'agency_username'=>$this->input->get('agency_username'),
		];

		$this->loadDefaultTemplate(array(),
			array('resources/css/general/style.css'),
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			'currency_management/manage_currency', $data, $render);
	}

	public function preview_manage_currency(){
		$data = array('title' => lang('Manage Currency'), 'sidebar' => 'currency_management/sidebar',
			'activenav' => 'currency');

		if (!$this->permissions->checkPermissions('manage_currency')) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$this->load->model(['player_model', 'users', 'affiliatemodel', 'agency_model']);

		$data['conditions']=[
			'player_username'=>$this->input->post('player_username'),
			'admin_username'=>$this->input->post('admin_username'),
			'affiliate_username'=>$this->input->post('affiliate_username'),
			'agency_username'=>$this->input->post('agency_username'),
		];

		$data['player_id']=null;
		$data['admin_id']=null;
		$data['affiliate_id']=null;
		$data['agency_id']=null;
		$data['error_message']=[];

		//search and confirm username
		if(!empty($data['conditions']['player_username'])){
			$player_id=$this->player_model->getPlayerIdByUsername($data['conditions']['player_username']);
			if(empty($player_id)){
				$data['error_message'][]=lang('Not found player username');
			}else{
				$data['player_id']=$player_id;
			}
		}
		if(!empty($data['conditions']['admin_username'])){
			$admin_id=$this->users->getIdByUsername($data['conditions']['admin_username']);
			if(empty($admin_id)){
				$data['error_message'][]=lang('Not found admin username');
			}else{
				$data['admin_id']=$admin_id;
			}
		}
		if(!empty($data['conditions']['affiliate_username'])){
			$affiliate_id=$this->affiliatemodel->getAffiliateIdByUsername($data['conditions']['affiliate_username']);
			if(empty($affiliate_id)){
				$data['error_message'][]=lang('Not found affiliate username');
			}else{
				$data['affiliate_id']=$affiliate_id;
			}
		}
		if(!empty($data['conditions']['agency_username'])){
			$agency_id=$this->agency_model->getAgentIdByUsername($data['conditions']['agency_username']);
			if(empty($agency_id)){
				$data['error_message'][]=lang('Not found agency username');
			}else{
				$data['agency_id']=$agency_id;
			}
		}
		$data['enable_currency_arr']=$this->input->post('enable_currency');

		if(empty($data['player_id']) && empty($data['admin_id']) && empty($data['affiliate_id']) && empty($data['agency_id'])){
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('please input any username'));
			return redirect('/system_management/manage_currency');
		}

		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$render=true;
		$this->addBoxDialogToTemplate();

		$this->loadDefaultTemplate(array(),
			array('resources/css/general/style.css'),
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			'currency_management/preview_manage_currency', $data, $render);
	}

	public function post_manage_currency(){
		$data = array('title' => lang('Manage Currency'), 'sidebar' => 'currency_management/sidebar',
			'activenav' => 'currency');

		if (!$this->permissions->checkPermissions('manage_currency')) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$this->load->model(['multiple_db_model']);
        $enable_currency_arr=$this->input->post('enable_currency');

        if(empty($enable_currency_arr)){
        	//error
        	return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
        }

        $player_id=$this->input->post('player_id');
        $admin_id=$this->input->post('admin_id');
        $affiliate_id=$this->input->post('affiliate_id');
        $agency_id=$this->input->post('agency_id');

        $disableCurrencyKeyList=[];
        $enableCurrencyKeyList=[];
        $availableCurrencyList=$this->utils->getAvailableCurrencyList();
        foreach ($availableCurrencyList as $currencyKey=>$currencyInfo) {
        	if(!in_array($currencyKey, $enable_currency_arr)){
        		$disableCurrencyKeyList[]=$currencyKey;
        	}else{
        		$enableCurrencyKeyList[]=$currencyKey;
        	}
        }

        $db=$this->multiple_db_model->getSuperDBFromMDB();
        $result=null;
        $success=$this->multiple_db_model->runDBTransOnly($db, $result, function($db, &$result)
        		use($disableCurrencyKeyList, $enableCurrencyKeyList, $player_id, $admin_id, $affiliate_id, $agency_id){

        	$success=false;
        	if(!empty($player_id)){
        		$id=$player_id;
        		$type=Multiple_db_model::ID_TYPE_PLAYER_ID;
	        	$success=$this->multiple_db_model->enableDisableCurrencyOnDB($id, $type,
	        		$enableCurrencyKeyList, $disableCurrencyKeyList, $db);
        	}
        	if(!empty($admin_id)){
        		$id=$admin_id;
        		$type=Multiple_db_model::ID_TYPE_USER_ID;
	        	$success=$this->multiple_db_model->enableDisableCurrencyOnSuper($id, $type,
	        		$enableCurrencyKeyList, $disableCurrencyKeyList, $db);
        	}
        	if(!empty($affiliate_id)){
        		$id=$affiliate_id;
        		$type=Multiple_db_model::ID_TYPE_AFFILIATE_ID;
	        	$success=$this->multiple_db_model->enableDisableCurrencyOnSuper($id, $type,
	        		$enableCurrencyKeyList, $disableCurrencyKeyList, $db);
        	}
        	if(!empty($agency_id)){
        		$id=$agency_id;
        		$type=Multiple_db_model::ID_TYPE_AGENCY_ID;
	        	$success=$this->multiple_db_model->enableDisableCurrencyOnSuper($id, $type,
	        		$enableCurrencyKeyList, $disableCurrencyKeyList, $db);
        	}
        	return $success;

        });

        if($success){
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save currency permission successfully'));
        }else{
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save currency permission failed'));
        }

		return redirect('/system_management/manage_currency');
	}

	public function sync_player_reg_setting_to_mdb(){
		$data = array('title' => lang('Sync Player Registration Settings'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'view_registration_setting');

		if (!$this->permissions->checkPermissions('registration_setting')) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$this->load->model(['multiple_db_model', 'marketing']);
		$type=Marketing::TYPE_PLAYER_REGISTRATION;
		$rlt=null;
		$success=$this->syncPlayerRegSettingsCurrentToMDBWithLock($type, $rlt);

		if($success){
		    $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Sync Player Successfully'));
		}else{
			$errKeys=[];
			foreach ($rlt as $dbKey => $dbRlt) {
				if(!$dbRlt['success']){
					$errKeys[]=$dbKey;
				}
			}
			$errorMessage=implode(',', $errKeys);
		    $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sync Player Failed').': '.$errorMessage);
		}
		redirect('/marketing_management/viewRegistrationSettings');
	}

	public function sync_aff_reg_setting_to_mdb(){
		$data = array('title' => lang('Sync Affiliate Registration Settings'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'view_registration_setting');

		if (!$this->permissions->checkPermissions('registration_setting')) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$this->load->model(['multiple_db_model', 'marketing']);
		$type=Marketing::TYPE_AFFILIATE_REGISTRATION;
		$rlt=null;
		$success=$this->syncAffiliateRegSettingsCurrentToMDBWithLock($type, $rlt);

		if($success){
		    $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Sync Affiliate Successfully'));
		}else{
			$errKeys=[];
			foreach ($rlt as $dbKey => $dbRlt) {
				if(!$dbRlt['success']){
					$errKeys[]=$dbKey;
				}
			}
			$errorMessage=implode(',', $errKeys);
		    $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sync Affiliate Failed').': '.$errorMessage);
		}
		redirect('/marketing_management/viewRegistrationSettings');
	}

	public function post_sync_mdb(){
		$callerType = Queue_result::CALLER_TYPE_ADMIN;
		$caller=$this->authentication->getUserId();
		$state=null;
		$lang=$this->language_function->getCurrentLanguage();

		$playerUsername=$this->input->post('playerUsername');
		$agencyUsername=$this->input->post('agencyUsername');
		$affiliateUsername=$this->input->post('affiliateUsername');
		$adminUsername=$this->input->post('adminUsername');
		$roleName=$this->input->post('roleName');
		$regSettingType=$this->input->post('regSettingType');

		$targetIdArr=[];
		if(!empty($playerUsername)){
			$this->load->model(['player_model']);
			$playerId=$this->player_model->getPlayerIdByUsername($playerUsername);
			if(!empty($playerId)){
				$targetIdArr['player_id']=$playerId;
				$targetIdArr['player_lock_unique_name']=$playerUsername;
			}
		}
		if(!empty($agencyUsername)){
			$this->load->model(['agency_model']);
			$agentId=$this->agency_model->getAgentIdByUsername($agencyUsername);
			if(!empty($agentId)){
				$targetIdArr['agent_id']=$agentId;
				$targetIdArr['agent_lock_unique_name']=$agencyUsername;
			}
		}
		if(!empty($affiliateUsername)){
			$this->load->model(['affiliatemodel']);
			$affiliateId=$this->affiliatemodel->getAffiliateIdByUsername($affiliateUsername);
			if(!empty($affiliateId)){
				$targetIdArr['affiliate_id']=$affiliateId;
				$targetIdArr['affiliate_lock_unique_name']=$affiliateUsername;
			}
		}
		if(!empty($adminUsername)){
			$this->load->model(['users']);
			$userId=$this->users->getIdByUsername($adminUsername);
			if(!empty($userId)){
				$targetIdArr['admin_user_id']=$userId;
				$targetIdArr['admin_user_lock_unique_name']=$adminUsername;
			}
		}
		if(!empty($roleName)){
			$this->load->model(['roles']);
			$roleId=$this->roles->getRoleIdByName($roleName);
			if(!empty($userId)){
				$targetIdArr['role_id']=$roleId;
				$targetIdArr['role_lock_unique_name']=$roleName;
			}
		}
		$message=null;
		$this->utils->debug_log('triggerRemoteSyncMDBEvent', $targetIdArr, $callerType, $caller, $state, $lang, $message);
		$this->load->library(['lib_queue']);
		$token=$this->lib_queue->triggerAsyncRemoteSyncMDBEvent($targetIdArr, $callerType, $caller, $state, $lang, $message);
	    if (!empty($token)) {
	        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Add job to queue successfully'));
			return redirect('/system_management/common_queue/'.$token);
	    } else {
	    	if(empty($message)){
	    		$message=lang('Add job to queue failed');
	    	}
	        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			return redirect('/system_management/other_functions');
	    }
	}

	public function regenerate_all_report(){
		$data = array('title' => lang('Dev Functions'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'dev_functions');

		$by_date_from=$this->input->post('by_date_from');
		$by_date_to=$this->input->post('by_date_to');

		$lock_rebuild_reports_range= $this->utils->getConfig('lock_rebuild_reports_range');

        if(!empty($lock_rebuild_reports_range)){
        	$from = $this->utils->formatDateForMysql(new DateTime($by_date_from));
        	$to = $this->utils->formatDateForMysql(new DateTime($by_date_to));
            if($this->utils->isRebuildReportDateLocked($lock_rebuild_reports_range,$from,$to,$rlt)){
                $this->utils->error_log(sprintf(lang("Regenarate All Report has lock - should not be equal or older than  %s  "),$rlt['cutoff_day']));
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, sprintf(lang("Regenarate All Report has lock - should not be equal or older than  %s  "),$rlt['cutoff_day']));
                return redirect('/system_management/other_functions');
            }
        }

		$this->utils->debug_log('regenerate_all_report','by_date_from', $by_date_from, 'by_date_to', $by_date_to);

		$success=true;
		$this->load->library(['lib_queue', 'language_function']);
		$this->load->model(['queue_result']);
		$caller=$this->authentication->getUserId();
		$state=null;
		$lang=$this->language_function->getCurrentLanguage();
		$funcName = 'regenerate_all_report';
		$callerType = Queue_result::CALLER_TYPE_ADMIN;
		$systemId = Queue_result::SYSTEM_UNKNOWN;

		$params = ['endDateTimeStr' => $by_date_from,
		'fromDateTimeStr' => $by_date_to
		];

		$token=$this->lib_queue->commonAddRemoteJob($systemId,$funcName, $params, $callerType, $caller, $state, $lang);
	        //goto queue page
		redirect('/system_management/common_queue/'.$token);
	}

	public function batch_move_deposit_note_to_sale_orders_notes(){
		$data = array('title' => lang('Batch move deposit note to sale_orders_notes'), 'sidebar' => 'system_management/sidebar', 'activenav' => 'dev_functions');
		if (!$this->permissions->checkPermissions('dev_functions')) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$is_blocked = false;
		$this->triggerGenerateCommandEvent('batchCopyDataFromSaleOrdersAndTransactionNotes', [], $is_blocked);

		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Please check later.'));
		redirect('/system_management/other_functions');
	}

	public function batch_move_withdrawal_note_to_walletaccount_notes(){
		$data = array('title' => lang('Batch move deposit note to walletaccount_notes'), 'sidebar' => 'system_management/sidebar', 'activenav' => 'dev_functions');
		if (!$this->permissions->checkPermissions('dev_functions')) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
		}

		$is_blocked = false;
		$this->triggerGenerateCommandEvent('batchCopyDataFromWalletaccountAndTransactionNotes', [], $is_blocked);

		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Please check later.'));
		redirect('/system_management/other_functions');
	}

	public function post_update_admin_dashboard() {
		$date_range_to		= $this->input->post('date_range_to', 1);
		$date_range_from	= $this->input->post('date_range_from', 1);
		$date_base			= $this->input->post('date_base', 1);
		$date_disp			= $this->input->post('date_disp', 1);

		$dates = [
			'date_range_to'		=> $date_range_to ,
			'date_range_from'	=> $date_range_from ,
			'date_base'			=> $date_base ,
			'date_disp'			=> $date_disp
		];

		$this->load->model([ 'transactions' ]);
		$this->transactions->syncDashboard($dates);

		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('devfunc.admin_dashboard_updated'));
		redirect('/system_management/other_functions');
	}

	public function setting_admin_auto_logout() {
		$enable_admin_auto_logout = $this->input->post('enable_admin_auto_logout');
		$auto_logout_sess_expiration = $this->input->post('auto_logout_sess_expiration');
		if( $enable_admin_auto_logout && ($auto_logout_sess_expiration<2||$auto_logout_sess_expiration>720) ) {
			$auto_logout_sess_expiration = $this->utils->getConfig('default_auto_logout_sess_expiration');
		}
		$settingName = 'admin_sess_expire';
		$sysValue = $this->operatorglobalsettings->getSettingJson($settingName);

		$settingValue = array('enable' => $enable_admin_auto_logout=='on'? 1:0, 'sess_expiration' => $auto_logout_sess_expiration*60);

		if (!count($sysValue)) {
			// Insert setting
			$this->operatorglobalsettings->insertSettingJson($settingName, $settingValue);
		} else {
			// Update setting
			$this->operatorglobalsettings->putSettingJson($settingName, $settingValue);
		}
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, "Auto logout update successfully.");
		redirect('system_management/system_settings');
	}

	public function view_user_backendapi($targetUserId){

		if (!$this->utils->isEnabledFeature('enabled_backendapi') || !$this->permissions->checkPermissions('view_backendapi_keys')) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('No permission'));
			return redirect('/user_management/viewUser/'.$targetUserId);
		}

		$data = array('title' => lang('View Backend API'), 'sidebar' => 'system_management/sidebar',
			'activenav' => 'view_user_backendapi');

		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$render = true;

		if(empty($targetUserId)){
			//error
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Not found user'));
			return redirect('/user_management/viewUser/'.$targetUserId);
		}
		$this->load->model(['users']);
		$keys=$this->users->getKeysByUserId($targetUserId);
		if(empty($keys)){
			//error
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Not found user'));
			return redirect('/user_management/viewUser/'.$targetUserId);
		}
		$data['keys']=$keys;
		$data['targetUserId']=$targetUserId;

		$this->loadDefaultTemplate(['resources/js/datatables.min.js',],
			['resources/css/general/style.css','resources/css/datatables.min.css'],
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			'system_management/view_user_backendapi', $data, $render);
	}

	public function regenerate_user_backendapi($targetUserId){
		if (!$this->utils->isEnabledFeature('enabled_backendapi') || !$this->permissions->checkPermissions('regenerate_backendapi_keys')) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('No permission'));
			return $this->returnJsonResult(['success'=>false, 'error'=>lang('No permission')]);
		}

		$this->load->model(['users']);
		$success=$this->users->generateKeys($targetUserId);
		$result=['success'=>$success];

		$this->returnJsonResult($result);
	}

}

/////END OF FILE//////////////////