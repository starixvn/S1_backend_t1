<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';
/**
 * Game Description
 *
 * Game Description Controller for FHI OG System
 *
 *
 *@author  ARIS
 */

class Game_Description extends BaseController {

	function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->library(array('form_validation', 'template', 'pagination','permissions'));


	}

	/**
	 * Will redirect to another sidebar if the permission was disabled
	 *
	 * Created by Mark Andrew Mendoza (andrew.php.ph)
	 */
	private function error_redirection(){
		$this->loadTemplate('Game List Management', '', '', 'system');
		$systemUrl = $this->utils->activeSystemSidebar();
		$data['redirect'] = $systemUrl;

		$message = lang('con.usm01');
		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		$this->template->write_view('main_content', 'error_page', $data);
		$this->template->render();
	}

	private function error_access() {
		$this->loadTemplate('Game List Management', '', '', 'system');
		$systemUrl = $this->utils->activeSystemSidebar();

		$message = lang('con.usm01');
		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		$this->template->write_view('main_content', 'error_page', []);
		$this->template->render();
	}

	/**
	 * View Game Description
	 *
	 * @return	rendered Template with array of data
	 */
	public function viewGameDescription() {
		$this->permissions->checkSettings();
		$this->permissions->setPermissions(); //will set the permission for the logged in user
		if (!$this->permissions->checkPermissions('game_description')) {
			$this->error_redirection();
		} else {

			$this->processViewGameDescription($data);
			// $data['gameDescriptions'] = $this->game_description_model->getGameDescriptionId(rtrim($gameCodes,","));
			// 
			// 
            $this->utils->recordAction('game_description', 'viewGameDescription', "View Game_Description");

			$this->template->add_js('resources/js/game_description/game_description_history.js');
			$this->template->add_js('resources/js/game_description/game_description.js?v1.0');
			$this->template->add_js('resources/js/bootstrap-notify.min.js');
			$this->template->add_js('resources/js/select2.min.js');
			$this->template->add_css('resources/css/select2.min.css');
			$this->template->add_css('resources/css/game_description/game_description.css');
			$this->loadTemplate('System Management', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/game_description', $data);

			$this->template->render();
		}
	}

	private function processViewGameDescription(&$data, $history = null){
		if ($history) {
			$checkPermisions = $this->permissions->checkPermissions('game_description_history');
		}else{
			$checkPermisions = $this->permissions->checkPermissions('game_description');
		}

		if ($checkPermisions) {
			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			// sets the history for breadcrumbs
			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active'));
			}

			$this->history->setHistory('header_system.system_word23', 'game_description/viewGameDescription');

			$loaded = $this->session->userdata('loaded');

			if ($loaded == NULL) {
				$data = array(
					'game_gmd' => TRUE,
					'game_type_gmd' => TRUE,
					'game_platform_id_gmd' => TRUE,
					'game_name_gmd' => TRUE,
					'game_code_gmd' => TRUE,
					'progressive_gmd' => TRUE,
					'note_gmd' => TRUE,
					'dlc_enabled_gmd' => TRUE,
					'flash_enabled_gmd' => TRUE,
					'offline_enabled_gmd' => TRUE,
					'mobile_enabled_gmd' => TRUE,
					'status_gmd' => TRUE,
					'flag_show_in_site_gmd' => TRUE,
					'no_cash_back_gmd' => TRUE,
					'void_bet_gmd' => TRUE,
					'game_order_gmd' => TRUE,
				);
			} else {
				$data = array(
					'game_gmd' => ($this->session->userdata('game_gmd')) ? TRUE : FALSE,
					'game_type_gmd' => ($this->session->userdata('game_type_gmd')) ? TRUE : FALSE,
					'game_platform_id_gmd' => ($this->session->userdata('game_platform_id_gmd')) ? TRUE : FALSE,
					'game_name_gmd' => ($this->session->userdata('game_name_gmd')) ? TRUE : FALSE,
					'game_code_gmd' => ($this->session->userdata('game_code_gmd')) ? TRUE : FALSE,
					'progressive_gmd' => ($this->session->userdata('progressive_gmd')) ? TRUE : FALSE,
					'note_gmd' => ($this->session->userdata('note_gmd')) ? TRUE : FALSE,
					'dlc_enabled_gmd' => ($this->session->userdata('dlc_enabled_gmd')) ? TRUE : FALSE,
					'flash_enabled_gmd' => ($this->session->userdata('flash_enabled_gmd')) ? TRUE : FALSE,
					'offline_enabled_gmd' => ($this->session->userdata('offline_enabled_gmd')) ? TRUE : FALSE,
					'mobile_enabled_gmd' => ($this->session->userdata('mobile_enabled_gmd')) ? TRUE : FALSE,
					'status_gmd' => ($this->session->userdata('status_gmd')) ? TRUE : FALSE,
					'flag_show_in_site_gmd' => ($this->session->userdata('flag_show_in_site_gmd')) ? TRUE : FALSE,
					'no_cash_back_gmd' => ($this->session->userdata('no_cash_back_gmd')) ? TRUE : FALSE,
					'void_bet_gmd' => ($this->session->userdata('void_bet_gmd')) ? TRUE : FALSE,
					'game_order_gmd' => ($this->session->userdata('game_order_gmd')) ? TRUE : FALSE,
				);
			}

			$this->session->set_userdata($data);

			$this->load->model(['game_description_model','external_system']);
			$gameCodeGet = $this->input->get('gameCode');
			$data['gameDescriptions']=null;
	        if ($this->utils->getConfig('show_non_active_game_api_game_list')) {
	            // $data['gameDescriptions'] = $this->game_description_model->getAllGameDescriptions();
	            $data['gameapis'] = $this->external_system->getAllGameApis();
	            $gameCodeCondition = empty($gameCodeGet)? Game_description_model::DB_FALSE:reset($gameCodeGet);
	        }else{
	            // $data['gameDescriptions'] = $this->game_description_model->getAllGameDescriptions(true);
	            $data['gameapis'] = $this->external_system->getAllActiveSytemGameApi();
	            $gameCodeCondition = json_encode($gameCodeGet);
	        }

			$data['filters'] = [
				lang('HTML5')=>'html_five_enabled',
				lang('Flash')=>'flash_enabled',
				lang('Mobile')=>'mobile_enabled',
				lang('Available on Desktop App')=>'dlc_enabled',
				lang('New Games')=>'flag_new_game',
				lang('Available on Android')=>'enabled_on_android',
				lang('Available on IOS')=>'enabled_on_ios',
				lang('Active Games')=>'status',
				lang('Disabled Games')=>'status-not',
				lang('Available on Website')=>'flag_show_in_site',
				lang('Not available on Website')=>'flag_show_in_site-not',
				lang('Available on Offline')=>'offline_enabled'
			];

			$gameCodesList=[];
			$gameCodes=[];
			$array =array();
	        foreach ($data['gameapis'] as $key => $values) {
	        	$gameCodes[] = $this->game_description_model->getGameByQuery('game_code','game_platform_id = ' . $values['id']);
	        	if(!empty($gameCodes) && isset($gameCodes[$key])){
		        	foreach ($gameCodes[$key] as $value) {
		        		$array[] = $value['game_code'];
		        	}
		        }
	        }
	        
			$data['gameCodes'] = array_unique($array);
			$data['conditions'] = $this->safeLoadParams(array(
				'gameName'=> '',
				'gameCode'=> '',
				'gameType'=> '',
				'gamePlatform'=> '',
				'gameStatus'=> '',
				'gameFlagShow'=> '',
				'gameId'=> '',

			));

			$data['conditions']['gameName']=$this->input->get('gameName');
			if(!empty($data['conditions']['gameName']) && !is_array($data['conditions']['gameName'])){
				$data['conditions']['gameName']=$data['conditions']['gameName'];
			}

			$data['conditions']['gameId']=$this->input->get('gameId');
			if(!empty($data['conditions']['gameId']) && !is_array($data['conditions']['gameId'])){
				$data['conditions']['gameId']=$data['conditions']['gameId'];
			}

			$data['conditions']['gameCode']=$gameCodeCondition;
			if(!empty($data['conditions']['gameCode'])){
				$data['conditions']['gameCode']=$data['conditions']['gameCode'];
			}

			$data['conditions']['gamePlatform']=$this->input->get('gamePlatform');
			if(!empty($data['conditions']['gamePlatform']) && !is_array($data['conditions']['gamePlatform'])){
				$data['conditions']['gamePlatform']=$data['conditions']['gamePlatform'];
			}

			$data['conditions']['gameType']=$this->input->get('gameType');
			if(!empty($data['conditions']['gameType']) && !is_array($data['conditions']['gameType'])){
				$data['conditions']['gameType']=$data['conditions']['gameType'];
			}

			$mGameStatus = $this->input->get('gameStatus');
			if($mGameStatus !== false){
				$data['conditions']['gameStatus'] = $mGameStatus;
			}

			$mGameFlagShow = $this->input->get('gameFlagShow');
			if($mGameFlagShow !== false){
				$data['conditions']['gameFlagShow'] = $mGameFlagShow;
			}

			$data['conditions']['filters']=json_encode($this->input->get('filters'));
			if(!empty($data['conditions']['filters']) && !is_array($data['conditions']['filters'])){
				$data['conditions']['filters']=$data['conditions']['filters'];
			}

			$this->utils->debug_log('conditions', $data['conditions']);
		}
	}

	/**
	 * View Game Description
	 *
	 * @return	rendered Template with array of data
	 */
	public function viewGameProviderAuth() {
		$this->permissions->checkSettings();
		$this->permissions->setPermissions(); //will set the permission for the logged in user
		if (!$this->permissions->checkPermissions('view_game_provider_auth_accounts')) {
			$this->error_redirection();
		} else {

			$this->processviewGameProviderAuth($data);
			// $data['gameDescriptions'] = $this->game_description_model->getGameDescriptionId(rtrim($gameCodes,","));
			// 
			// 
            $this->utils->recordAction('game_description', 'viewGameProviderAuth', "View game provider auth accounts");

			$this->template->add_js('resources/js/game_description/game_provider_auth.js?v1.0');
			$this->template->add_js('resources/js/bootstrap-notify.min.js');
			$this->template->add_js('resources/js/select2.min.js');
			$this->template->add_css('resources/css/select2.min.css');
			$this->template->add_css('resources/css/game_description/game_description.css');
			$this->loadTemplate('System Management', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/game_provider_auth_accounts', $data);

			$this->template->render();
		}
	}

	private function processviewGameProviderAuth(&$data, $history = null){
		$checkPermisions = $this->permissions->checkPermissions('view_game_provider_auth_accounts');

		if ($checkPermisions) {
			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			// sets the history for breadcrumbs
			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active'));
			}

			$this->history->setHistory('header_system.system_word23', 'game_description/viewGameProviderAuth');

			$this->load->model(['game_description_model','external_system']);
			$gameCodeGet = $this->input->get('gameCode');
			$data['gameDescriptions']=null;
	        if ($this->utils->getConfig('show_non_active_game_api_game_list')) {
	            // $data['gameDescriptions'] = $this->game_description_model->getAllGameDescriptions();
	            $data['gameapis'] = $this->external_system->getAllGameApis();
	            $gameCodeCondition = empty($gameCodeGet)? Game_description_model::DB_FALSE:reset($gameCodeGet);
	        }else{
	            // $data['gameDescriptions'] = $this->game_description_model->getAllGameDescriptions(true);
	            $data['gameapis'] = $this->external_system->getAllActiveSytemGameApi();
	            $gameCodeCondition = json_encode($gameCodeGet);
	        }

			$gameCodesList=[];
			$gameCodes=[];
			$array =array();
	        foreach ($data['gameapis'] as $key => $values) {
	        	$gameCodes[] = $this->game_description_model->getGameByQuery('game_code','game_platform_id = ' . $values['id']);
	        	if(!empty($gameCodes) && isset($gameCodes[$key])){
		        	foreach ($gameCodes[$key] as $value) {
		        		$array[] = $value['game_code'];
		        	}
		        }
	        }
	        
			$data['conditions'] = $this->safeLoadParams(array(
				'gameUsername'=> '',
				'gamePlatform'=> '',
			));

			$data['conditions']['gameUsername']=$this->input->get('gameUsername');
			if(!empty($data['conditions']['gameUsername']) && !is_array($data['conditions']['gameUsername'])){
				$data['conditions']['gameUsername']=$data['conditions']['gameUsername'];
			}

			$data['conditions']['gamePlatform']=$this->input->get('gamePlatform');
			if(!empty($data['conditions']['gamePlatform']) && !is_array($data['conditions']['gamePlatform'])){
				$data['conditions']['gamePlatform']=$data['conditions']['gamePlatform'];
			}

			$this->utils->debug_log('conditions', $data['conditions']);
		}
	}

	/**
	 * View Game Description
	 *
	 * @return	rendered Template with array of data
	 */
	public function viewGameDescriptionHistory() {
		$this->permissions->checkSettings();
		$this->permissions->setPermissions(); //will set the permission for the logged in user
		if (!$this->permissions->checkPermissions('game_description_history')) {
			$this->error_access();
		} else {
			$this->processViewGameDescription($data,true);

			$data['conditions']['action'] = $this->input->get('action');
			$data['actions'] = ["Add","Update","Delete"];

			$this->template->add_js('resources/js/game_description/game_description_history.js');
			$this->template->add_js('resources/js/bootstrap-notify.min.js');
			$this->template->add_js('resources/js/select2.min.js');
			$this->template->add_css('resources/css/select2.min.css');
			$this->template->add_css('resources/css/game_description/game_description.css');
			$this->loadTemplate('System Management', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/game_description_history', $data);
			$this->template->render();
		}
	}

	public function postChangeColumns() {

		$data = array(
			'game_gmd' => ($this->input->post('game')) ? TRUE : FALSE,
			'game_type_gmd' => ($this->input->post('game_type')) ? TRUE : FALSE,
			'game_platform_id_gmd' => ($this->input->post('game_platform_id')) ? TRUE : FALSE,
			'game_name_gmd' => ($this->input->post('game_name')) ? TRUE : FALSE,
			'game_code_gmd' => ($this->input->post('game_code[]')) ? TRUE : FALSE,
			'progressive_gmd' => ($this->input->post('progressive')) ? TRUE : FALSE,
			'note_gmd' => ($this->input->post('note')) ? TRUE : FALSE,
			'dlc_enabled_gmd' => ($this->input->post('dlc_enabled')) ? TRUE : FALSE,
			'flash_enabled_gmd' => ($this->input->post('flash_enabled')) ? TRUE : FALSE,
			'offline_enabled_gmd' => ($this->input->post('offline_enabled')) ? TRUE : FALSE,
			'mobile_enabled_gmd' => ($this->input->post('mobile_enabled')) ? TRUE : FALSE,
			'status_gmd' => ($this->input->post('status')) ? TRUE : FALSE,
			'flag_show_in_site_gmd' => ($this->input->post('flag_show_in_site')) ? TRUE : FALSE,
			'no_cash_back_gmd' => ($this->input->post('no_cash_back')) ? TRUE : FALSE,
			'void_bet_gmd' => ($this->input->post('void_bet')) ? TRUE : FALSE,
			'game_order_gmd' => ($this->input->post('game_order')) ? TRUE : FALSE,
		);

		$this->session->set_userdata($data);
		$this->session->set_userdata('loaded', 'loaded');
		redirect(BASEURL . 'game_description/viewGameDescription');
	}
/**
 * Loads template for view based on regions in
 * config > template.php
 *
 */
	private function loadTemplate($title, $description, $keywords, $activenav) {

		// $this->template->add_js('resources/js/game_description/game_description.js');
		//$this->template->add_js('resources/js/jquery.dataTables.min.js');
		//$this->template->add_js('resources/js/dataTables.responsive.min.js');
		$this->template->add_js('resources/js/datatables.min.js');

		$this->template->add_js('resources/js/highlight.pack.js');
		$this->template->add_js('resources/js/ace/ace.js');
		$this->template->add_js('resources/js/ace/mode-json.js');
		$this->template->add_js('resources/js/ace/theme-tomorrow.js');

		$this->template->add_css('resources/css/general/style.css');
		//$this->template->add_css('resources/css/jquery.dataTables.css');
		//$this->template->add_css('resources/css/dataTables.responsive.css');
		$this->template->add_css('resources/css/datatables.min.css');
		$this->template->add_css('resources/css/hljs.tomorrow.css');

		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('userId', $this->authentication->getUserId());
		$this->template->write_view('sidebar', 'system_management/sidebar');
	}

	public function getGamesAndGameTypes() {
		$this->load->model(['external_system','game_type_model','game_description_model']);
		$game_apis = $this->external_system->getAllGameApis();
		$gameTypes = $this->game_type_model->getGameTypes();
		$gameNames = $this->game_description_model->getAllGameNames();
		//Convert fast std object to array;
		$data['game_apis'] = json_decode(json_encode($game_apis), true);
		$data['gameTypes'] = json_decode(json_encode($gameTypes), true);
		$data['gameNames'] = json_decode(json_encode($gameNames), true);
		$arr = array('status' => 'success', 'data' => $data);
		echo json_encode($arr);
	}

	/**
	 * Load thru ajax
	 *
	 * @param 	int
	 * @return	loaded page
	 */
	public function editGameDescription($gameDescId) {
		$this->load->model(['external_system','game_type_model','game_description_model']);
		$game_apis = $this->external_system->getAllGameApis();
		// $gameNames = $this->game_description_model->getAllGameNames();
		$gameDescription = $this->game_description_model->getGameDescription($gameDescId);
		$gameTypes = $this->game_type_model->getGameTypeListByGamePlatformId($gameDescription->game_platform_id);
		if ($gameTypes) {
			foreach ($gameTypes as $key => &$gameType) {
				$gameType['game_type'] = lang($gameType['game_type']);
				$gameType['game_type_lang'] = lang($gameType['game_type_lang']);
			}

			//Convert fast std object to array;$gameDescription['game_platform_id']
			$data['game_apis'] = json_decode(json_encode($game_apis), true);
			$data['gameTypes'] = json_decode(json_encode($gameTypes), true);
			// $data['gameNames'] = json_decode(json_encode($gameNames), true);
			$data['gameDescription'] = json_decode(json_encode($gameDescription), true);

			$arr = array('status' => 'success', 'data' => $data);
		}else{
			$arr = array('status' => 'fail','reason'=>'no available game type');
		}

		echo json_encode($arr);
	}

	/**
	 * Adds the Game Description
	 *
	 *
	 */
	public function addGameDescription() {
		if (!$this->permissions->checkPermissions('game_description')) {
			$this->error_access();
		}else{
			$this->load->model('game_description_model');

			$data = array(
				'game_type_id' => $this->input->post('game_type_id'),
				'game_platform_id' => $this->input->post('game_platform_id'),
				'game_name' => $this->input->post('game_name'),
				'game_code' => $this->input->post('game_code'),
				'external_game_id' => $this->input->post('external_game_id'),
				'english_name' => $this->input->post('english_name'),
	            'progressive' => ($this->input->post('progressive')) ? TRUE : FALSE,
				'attributes' => $this->input->post('game_attributes'),
				'note' => $this->input->post('note'),
				'dlc_enabled' => ($this->input->post('dlc_enabled')) ? TRUE : FALSE,
				'flash_enabled' => ($this->input->post('flash_enabled')) ? TRUE : FALSE,
				'html_five_enabled' => ($this->input->post('html_five_enabled')) ? TRUE : FALSE,
				'offline_enabled' => ($this->input->post('offline_enabled')) ? TRUE : FALSE,
				'mobile_enabled' => ($this->input->post('mobile_enabled')) ? TRUE : FALSE,
				'enabled_on_ios' => ($this->input->post('enabled_on_ios')) ? TRUE : FALSE,
				'enabled_on_android' => ($this->input->post('enabled_on_android')) ? TRUE : FALSE,
				'status' => ($this->input->post('status')) ? TRUE : FALSE,
				'flag_show_in_site' => ($this->input->post('flag_show_in_site')) ? TRUE : FALSE,
				'flag_new_game' => ($this->input->post('flag_new_game')) ? TRUE : FALSE,
				'no_cash_back' => $this->input->post('no_cash_back'),
				'void_bet' => $this->input->post('void_bet'),
				'game_order' => $this->input->post('game_order'),
				'created_on' => $this->utils->getNowForMysql(),
			);

			if ($this->game_description_model->singleAddUpdateGame($data)) {
				$this->alertMessage(1, lang('sys.gd25'));
				$arr = array('status' => 'success');
				echo json_encode($arr);
			} else {
				$arr = array('status' => 'failed');
				echo json_encode($arr);
				$this->alertMessage(2, lang('sys.gd26'));
			}
		}
	}

	/**
	 * Updates the Game Description
	 *
	 *
	 */
	public function updateGameDescription() {
		if (!$this->permissions->checkPermissions('game_description')) {
			$this->error_access();
		}else{
			$this->load->model('game_description_model');
			$id = $this->input->post('gd_id');
			$data = array(
				'game_type_id' => $this->input->post('game_type_id'),
				'game_platform_id' => $this->input->post('game_platform_id'),
				'game_name' => $this->input->post('game_name'),
				'game_code' => $this->input->post('game_code'),
				'external_game_id' => $this->input->post('external_game_id'),
				'english_name' => $this->input->post('english_name'),
				'attributes' => $this->input->post('game_attributes'),
				'note' => $this->input->post('note'),
				'no_cash_back' => $this->input->post('no_cash_back'),
				'void_bet' => $this->input->post('void_bet'),
				'game_order' => $this->input->post('game_order'),
				'updated_at' => $this->utils->getNowForMysql(),
	            'progressive' => (isset($this->input->post()['progressive'])) ? Game_description_model::DB_TRUE : Game_description_model::DB_FALSE,
				'dlc_enabled' => (isset($this->input->post()['dlc_enabled'])) ? Game_description_model::DB_TRUE : Game_description_model::DB_FALSE,
				'flash_enabled' => (isset($this->input->post()['flash_enabled'])) ? Game_description_model::DB_TRUE : Game_description_model::DB_FALSE,
				'html_five_enabled' => (isset($this->input->post()['html_five_enabled'])) ? Game_description_model::DB_TRUE : Game_description_model::DB_FALSE,
				'offline_enabled' => (isset($this->input->post()['offline_enabled'])) ? Game_description_model::DB_TRUE : Game_description_model::DB_FALSE,
				'mobile_enabled' => (isset($this->input->post()['mobile_enabled'])) ? Game_description_model::DB_TRUE : Game_description_model::DB_FALSE,
				'enabled_on_ios' => (isset($this->input->post()['enabled_on_ios'])) ? Game_description_model::DB_TRUE : Game_description_model::DB_FALSE,
				'enabled_on_android' => (isset($this->input->post()['enabled_on_android'])) ? Game_description_model::DB_TRUE : Game_description_model::DB_FALSE,
				'void_bet' => (isset($this->input->post()['void_bet']) && $this->input->post()['void_bet']) ? Game_description_model::DB_TRUE : Game_description_model::DB_FALSE,
				// (Deprecated) separate updates for these fields
				// 'flag_new_game' => (isset($this->input->post()['flag_new_game'])) ? Game_description_model::DB_TRUE : Game_description_model::DB_FALSE,
				// 'status' => (isset($this->input->post()['status'])) ? Game_description_model::DB_TRUE : Game_description_model::DB_FALSE,
				// 'flag_show_in_site' => (isset($this->input->post()['flag_show_in_site'])) ? Game_description_model::DB_TRUE : Game_description_model::DB_FALSE,
				'locked_flag' => Game_description_model::DB_TRUE, # OGP-13029 (dont allow sync in gg prod)
			);


            $this->utils->recordAction('game_description', 'updateGameDescription', "Update Game_Description/game_code: ".$this->input->post('game_code'));

			if ($this->game_description_model->singleAddUpdateGame($data, $id)) {
		        $this->utils->debug_log("updateGameDescription =======>", $data);
			    $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('sys.ga.succsaved'));
				$arr = array('status' => 'success', 'msg' => lang('sys.gd32'));
				echo json_encode($arr);
			} else {
				$arr = array('status' => 'failed', 'msg' => lang('sys.gd26'));
				echo json_encode($arr);
				$this->alertMessage(self::MESSAGE_TYPE_FAILED, lang('sys.gd26'));
			}
		}
	}

	/**
	 * Deletes Game Description
	 *
	 *
	 */
	public function deleteGameDescription() {
		if (!$this->permissions->checkPermissions('game_description')) {
			$this->error_access();
		}else{
			$this->load->model('game_description_model');
			$ids = $this->input->post("forDeletes");
			// print_r($ids);exit;
			if ($this->game_description_model->singleAddUpdateGame([],$ids,'delete')) {
				$this->alertMessage(1, lang('sys.gd29'));
				$arr = array('status' => 'success');
				echo json_encode($arr);
			} else {
				$arr = array('status' => 'failed');
				echo json_encode($arr);
				$this->alertMessage(2, lang('sys.gd26'));
			}
		}
	}

	/**
	 * Deletes Game Provider Auth Username
	 */
	public function deleteAndCreateGameProviderAuthUsername() {
		if (!$this->permissions->checkPermissions('view_game_provider_auth_accounts')) {
			$this->error_access();
		} else {
			$this->load->model('game_provider_auth');
			$id = $this->input->post('id');

			$gpa_details = (array) $this->game_provider_auth->getGameAccountById($id)[0];
			$gpa_game_platform_id = $gpa_details['game_provider_id'];
			$gpa_game_username = $gpa_details['login_name'];
			$player_username = $gpa_details['username'];
			$player_id = $gpa_details['player_id'];
			$password = $gpa_details['password'];
			$user_id = $this->authentication->getUserId();

			// For TCG only as of 11/12/2019
			if ($gpa_game_platform_id != TCG_API) {
				$arr = array('status' => 'failed');
				echo json_encode($arr);
				$this->alertMessage(2, lang('This function is applicable for TCG only as of now'));
				return;
			}

			// No duplicate prefix occured, Normal prefix only
			if (substr($gpa_game_username, 0, 6) != 'ocnocn' || (substr($gpa_game_username, 0, 3) == 'ocn') && substr($gpa_game_username, 3, 3) != 'ocn') {
				$arr = array('status' => 'failed');
				echo json_encode($arr);
				$this->alertMessage(2, lang('sys.gp5'));
				return;
			}

			// Remove first OCN or first 3 characters
			if (substr($gpa_game_username, 0, 6) == 'ocnocn') {
				$new_gpa_game_username = substr($gpa_game_username, 3);
			}

			$api = $this->utils->loadExternalSystemLibObject($gpa_game_platform_id);
			$api_balance = $api->queryPlayerBalance($player_username);

            $this->utils->debug_log("GPA queryPlayerBalance ====> ", $api_balance);

            // For other API since some api returns array('success' => true/false, 'result' => 'queryPlayerBalance')
            // (isset($api_balance['success']) && $api_balance['success'])
            // 
            // This condition is for TCG only
            if ((isset($api_balance['success']) && !$api_balance['success'])) {
				$arr = array('status' => 'failed');
				echo json_encode($arr);
				$this->alertMessage(2, lang('sys.gd26'));
	            $this->utils->debug_log("GPA queryPlayerBalance GOT ERROR ====> ", $api_balance);
				return;
            } else {
            	$amount = $api_balance['balance'];
            	$from_id = $gpa_game_platform_id;
            	$to_id = Game_provider_auth::MAIN_WALLET;
            	$reason = Game_provider_auth::GPA_DUPLICATE_PREFIX_REASON;
				$walletAdjustmentResult = $this->utils->transferWallet($player_id, $player_username, $from_id, $to_id, $amount, $user_id, null, null, false, $reason, Transactions::MANUALLY_ADJUSTED);
	            $this->utils->debug_log("GPA Wallet adjustment result ====> ", $walletAdjustmentResult);

				$data['register'] = Abstract_game_api::FLAG_FALSE;
				$data['login_name'] = $new_gpa_game_username;
				$updateResult = $this->game_provider_auth->updateRegisterFlag($player_id, $gpa_game_platform_id, $data);
	            $this->utils->debug_log("GPA LOGIN_NAME AND REGISTER UPDATE ====> ", $updateResult);

	            $createPlayerResult = $api->createPlayer($player_username, $player_id, $password, null, null);
	            $this->utils->debug_log("GPA TCG createPlayer ====> ", $createPlayerResult);

	            if ($createPlayerResult['success']) {
					$this->alertMessage(1, lang('sys.gd32'));
					$arr = array('status' => 'success');
					echo json_encode($arr);
	            	return;
	            } else {
					$arr = array('status' => 'failed');
					echo json_encode($arr);
					$this->alertMessage(2, lang('sys.gd26'));
		            $this->utils->debug_log("GPA TCG createPlayer GOT ERROR ====> ", $createPlayerResult);
	            }
            }
		}
	}

	/**
	 * Updates Game Description Status
	 */
	public function updateGameDescriptionStatus() {
		if (!$this->permissions->checkPermissions('game_description')) {
			$this->error_access();
		}else{
			$this->load->model('game_description_model');
			$ids = $this->input->post("forDeactivate");
			$type = $this->input->post("type");
			$status = $this->input->post("status");

            $this->utils->recordAction('game_description', 'updateGameDescriptionStatus', "Update status|flag_show_in_site/game_id: ".$ids);

			if ($this->game_description_model->updateGameDescriptionStatus($ids,$type,$status)) {
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('save.success'));
				$arr = array('status' => 'success');
				echo json_encode($arr);
			} else {
				$arr = array('status' => 'failed');
				echo json_encode($arr);
				$this->alertMessage(self::MESSAGE_TYPE_FAILED, lang('save.failed'));
			}
		}
	}

	/**
	 * set message for users
	 *
	 * @param   int
	 * @param   string
	 * @return  set session user data
	 */
	public function alertMessage($type, $message) {

		switch ($type) {
		case '1':
			$show_message = array(
				'result' => 'success',
				'message' => $message,
			);
			$this->session->set_userdata($show_message);
			break;

		case '2':
			$show_message = array(
				'result' => 'danger',
				'message' => $message,
			);
			$this->session->set_userdata($show_message);
			break;

		case '3':
			$show_message = array(
				'result' => 'warning',
				'message' => $message,
			);
			$this->session->set_userdata($show_message);
			break;
		}
	}

	/**
	 * Activates No Cashback
	 * @param   int
	 */
	// public function activateNoCashback($id) {
	// 	if (!$this->permissions->checkPermissions('game_description')) {
	// 		$this->error_access();
	// 	}else{
	// 		$this->load->model('game_description_model');
	// 		if ($this->game_description_model->activateNoCashback($id)) {
	// 			$this->alertMessage(1, lang('sys.gd32'));

	// 		} else {
	// 			$this->alertMessage(2, lang('sys.gd26'));
	// 		}
	// 		redirect(BASEURL . 'game_description/viewGameDescription');
	// 	}
	// }

	/**
	 * Deactivates No Cashback
	 * @param   int
	 */
	// public function deactivateNoCashback($id) {
	// 	if (!$this->permissions->checkPermissions('game_description')) {
	// 		$this->error_access();
	// 	}else{
	// 			$this->load->model('game_description_model');
	// 		if ($this->game_description_model->deactivateNoCashback($id)) {
	// 			$this->alertMessage(1, lang('sys.gd32'));
	// 		} else {
	// 			$this->alertMessage(2, lang('sys.gd26'));
	// 		}
	// 		redirect(BASEURL . 'game_description/viewGameDescription');
	// 	}
	// }

	/**
	 * Activates Void Bet
	 * @param   int
	 */
	// public function activateVoidBet($id) {
	// 	if (!$this->permissions->checkPermissions('game_description')) {
	// 		$this->error_access();
	// 	}else{
	// 		$this->load->model('game_description_model');
	// 		if ($this->game_description_model->activateVoidBet($id)) {
	// 			$this->alertMessage(1, lang('sys.gd32'));
	// 		} else {
	// 			$this->alertMessage(2, lang('sys.gd26'));
	// 		}
	// 		redirect(BASEURL . 'game_description/viewGameDescription');
	// 	}
	// }

	/**
	 * Deactivates Void Bet
	 * @param   int
	 */
	// public function deactivateVoidBet($id) {
	// 	if (!$this->permissions->checkPermissions('game_description')) {
	// 		$this->error_access();
	// 	}else{
	// 		$this->load->model('game_description_model');
	// 		if ($this->game_description_model->deactivateVoidBet($id)) {
	// 			$this->alertMessage(1, lang('sys.gd32'));
	// 		} else {
	// 			$this->alertMessage(2, lang('sys.gd26'));
	// 		}
	// 		redirect(BASEURL . 'game_description/viewGameDescription');
	// 	}
	// }

	public function gameTypes($game_platform_id) {
		$api = $this->utils->loadExternalSystemLibObject($game_platform_id);
		$data = $api->getGameTypeList();
		# OUTPUT
		$this->output->set_header('Access-Control-Allow-Origin: *');
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data, JSON_PRETTY_PRINT));
	}

	public function allGames($game_platform_id, $game_type = null, $extra = null) {
		if ($extra != null) {
			if ($game_platform_id == QT_API) {
				if ($extra == 'mini') {
					$where = "attributes is not null";
				} elseif ($extra == 'nomini') {
					$where = "attributes is null";
				}
			}
		} else {
			$where = null;
		}

		$api = $this->utils->loadExternalSystemLibObject($game_platform_id);
		$data = $api->getGameList($game_type, $where);

		# TODO: PUT IN external_system->extra_info->game_image_directory

		// switch ($game_platform_id) {
		// case NT_API:
		// 	$data['p'] = $site_url . '/resources/images/games/nt';
		// 	break;

		// case MG_API:
		// 	$data['p'] = $site_url . '/resources/images/games/mg';
		// 	break;

		// case BBIN_API:
		// 	$data['p'] = $site_url . '/resources/images/games/bbin';
		// 	break;

		// case AG_API:
		// 	$data['p'] = $site_url . '/resources/images/games/';
		// 	break;

		// case ONE88_API:
		// 	$data['p'] = $site_url . '/resources/images/games/188';
		// 	break;

		// case LB_API:
		// 	$data['p'] = $site_url . '/resources/images/games/lb';
		// 	break;

		// case ONESGAME_API:
		// 	$data['p'] = $site_url . '/resources/images/games/onesgame';
		// 	break;

		// case GAMEPLAY_API:
		// 	$data['p'] = $site_url . '/resources/images/games/gameplay';
		// 	break;

		// case INTEPLAY_API:
		// 	$data['p'] = $site_url . '/resources/images/games/inteplay';
		// 	break;

		// case GSPT_API:
		// 	$data['p'] = $site_url . '/resources/images/games/gspt';
		// 	break;

		// case IBC_API:
		// 	$data['p'] = $site_url . '/resources/images/games/ibc';
		// 	break;

		// case OPUS_API:
		// 	$data['p'] = $site_url . '/resources/images/games/opus';
		// 	break;

		// case HB_API:
		// 	$data['p'] = 'https://app-test.insvr.com/img/Logo/300';
		// 	break;

		// default:
		// 	$data['p'] = $site_url . '/game_img/' . $game_platform_id;
		// 	break;
		// }

		# OUTPUT
		$this->output->set_header('Access-Control-Allow-Origin: *');
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data, JSON_PRETTY_PRINT));
	}

	public function allGamesByWhere($game_platform_id, $where_field, $where_val) {
		$where = array($where_field => $where_val);
		$api = $this->utils->loadExternalSystemLibObject($game_platform_id);
		$data = $api->getGameList(null, $where);
		# OUTPUT
		$this->output->set_header('Access-Control-Allow-Origin: *');
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data, JSON_PRETTY_PRINT));
	}

    public function viewNewGames(){
        $this->permissions->checkSettings();
        $this->permissions->setPermissions(); //will set the permission for the logged in user
        if (!$this->permissions->checkPermissions('game_description')) {
            $this->error_access();
        } else {
            if (($this->session->userdata('sidebar_status') == NULL)) {
                $this->session->set_userdata(array('sidebar_status' => 'active'));
            }

            // sets the history for breadcrumbs
            if (($this->session->userdata('well_crumbs') == NULL)) {
                $this->session->set_userdata(array('well_crumbs' => 'active'));
            }
            $this->load->model(['game_description_model']);
            $data['new_games_cnt'] = $this->game_description_model->getNewGamesCount();
            $this->history->setHistory('header_system.system_word23', 'game_description/viewNewGames');

            $this->template->add_css('resources/css/jquery-checktree.css');
            $this->template->add_js('resources/js/bootstrap-notify.min.js');
            $this->addBoxDialogToTemplate();
            $this->addJsTreeToTemplate();

            $this->loadTemplate('System Management', '', '', 'system');
            $this->template->write_view('main_content', 'system_management/game_description/new_games_from_gamegateway',$data);
            $this->template->render();
        }

    }

    public function getJsonActiveGameApis(){
        $data['gameapis'] = $this->external_system->getAllActiveSytemGameApi();
        return $this->returnJsonResult($data);
    }

    public function postBatchUpdateActiveGameList(){
    	if (!$this->permissions->checkPermissions('game_description')) {
			$this->error_access();
		}else{
	        $this->load->model(array('game_description_model'));
	        $this->load->library(array('history'));
	        $this->permissions->checkSettings();
	        $this->permissions->setPermissions();

	        $game_platform_id = !empty($this->input->post('game_platform_id')) ? $this->input->post('game_platform_id') : $this->input->post('game_platform_id_num');

	        $aResult = [];
	        $aGame_list = [];
	        $iCountResult = 0;
	        $aUpdateActiveGames = array();


	        $failed = false;
	        if (empty($game_platform_id)) {
	            $failed = true;
	            $message = lang('Game Platform field is Required!');
	        }
	        $available_ext = array("csv");
	        $available_mime_type = array("text/plain");
	        $file_name = new SplFileInfo($_FILES['games']['name']);
			$file_ext  = $file_name->getExtension();
	        if(in_array($file_ext, $available_ext) && in_array(mime_content_type($_FILES['games']['tmp_name']) , $available_mime_type)){
	        	$aGame_list = array_map('str_getcsv', file($_FILES['games']['tmp_name']));
	        	if(count($aGame_list[0]) > 1){
	        		$message = lang('Allowed 1 column in csv!');
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
					redirect('game_description/viewGameDescription');
	        	}
	        } else {
	        	$message = lang('Please put a csv file!');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				redirect('game_description/viewGameDescription');
	        }

            if(isset($message) === true) {
            	$aResult['message'] = $message;
            }


            array_walk($aGame_list, function(&$a) use ($aGame_list) {
              $a = array_combine($aGame_list[0], $a);
            });
            array_shift($aGame_list);

            $aGameCodesToUpdate = [];

            if(!empty($aGame_list)){
            	foreach ($aGame_list as $aGames => $aGame) {
	            	foreach ($aGame as $Game_Code => $Value) {
	            		array_push($aGameCodesToUpdate, $Value);
	            	}
	            }
	            $aUpdateActiveGames = $this->game_description_model->updateActiveGameList($game_platform_id,$aGameCodesToUpdate);
	        	$iCountResult = count($aUpdateActiveGames);
            }

	        $aResult['Count'] = $iCountResult;
	        $aResult['Updated_Games'] = $aUpdateActiveGames;

	        if (($this->session->userdata('sidebar_status') == NULL)) {
	            $this->session->set_userdata(array('sidebar_status' => 'active'));
	        }

	        // sets the history for breadcrumbs
	        if (($this->session->userdata('well_crumbs') == NULL)) {
	            $this->session->set_userdata(array('well_crumbs' => 'active'));
	        }

	        $this->history->setHistory('header_system.system_word23', 'game_description/viewGameDescription');

	        $loaded = $this->session->userdata('loaded');

	        $this->template->add_js('resources/js/bootstrap-notify.min.js');
	        $this->loadTemplate('System Management', '', '', 'system');
	        $this->template->write_view('main_content', 'system_management/game_description/batch_update_active_game_list_result', $aResult);
	        $this->template->render();
	    }
    }


    public function postBatchInsertUpdateGameList(){
    	if (!$this->permissions->checkPermissions('game_description')) {
			$this->error_access();
		}else{
	        $this->load->model(array('game_type_model','game_description_model'));
	        $this->load->library(array('history'));
	        $this->permissions->checkSettings();
	        $this->permissions->setPermissions();

	        $game_platform_id       = !empty($this->input->post('game_platform_id')) ? $this->input->post('game_platform_id') : $this->input->post('game_platform_id_num');
	        $update_multiple_client = $this->input->post('update_multiple_client');
	        $client_environment     = $this->input->post('client_environment');
	        $client_list_select_all = $this->input->post('client_list_select_all');
	        $client_list            = $this->input->post('client_list');

	        #get game tags
	        $this->game_tags = $this->game_type_model->getAllGameTags();
	        $this->game_type_code_list = [
	            'slots'           => ['slots','slot','slot_games','slot_game','video_slot','slot_machines'],
	            'table_games'     => ['table_games','table_game','table'],
	            'table_and_cards' => ['table_and_cards','table_and_cards','table_and'],
	            'card_games'      => ['card_games','card_games'],
	            'video_poker'     => ['video_poker','video_poker_game','video_poker_games'],
	            'gamble'          => ['gamble','gamble_game','gamble_games'],
	            'others'          => ['other','others','other_game','other_games','others_games'],
	            'fishing_game'    => ['fishing_game','fishing_games'],
	            'lottery'         => ['lottery','lottery_games','lottery_game',],
	            'live_dealer'     => ['live_dealer','live_casino','live_games','live_game'],
	            'e_sports'        => ['e_sports','e_sports_games','e_sports_game',],
	            'fixed_odds'      => ['fixed_odds','fixed_odds_games','fixed_odds_game',],
	            'arcade'          => ['arcade','arcade_games','arcade_game',],
	            'horce_racing'    => ['horce_racing','horce_racing_games','horce_racing_game',],
	            'progressives'    => ['progressive','progressives','progressives_games','progressives_game',],
	            'sports'          => ['sports','sports_game','sports_games',],
	            'unknown'         => ['unknown',],
	            'casino'          => ['casino','casino_games','casino_game',],
	            'video_poker'     => ['video_poker','video_poker_games','video_poker_game',],
	            'poker'           => ['poker','poker_games','poker_game',],
	            'mini_games'      => ['mini_games','mini_game',],
	            'soft_games'      => ['soft_games','soft_game',],
	            'scratch_card'    => ['scratch_card','scratch_cards','scratch_card_games','scratch_card_game',],
	        ];

	        $failed = false;
	        if (empty($game_platform_id)) {
	            $failed = true;
	            $message = lang('Game Platform field is Required!');
	        }

	        if($update_multiple_client){
	            if (empty($client_environment)) {
	                $failed = true;
	                $message = lang('Client Environment field is Required!');
	            }
	            if(empty($client_list_select_all)){
	                if(empty($client_list)){
	                    $failed = true;
	                    $message = lang('Client List field is Required!');
	                }
	            }
	        }

	        if ($game_platform_id != MG_API || !empty($game_platform_id)) {
	            #rearrange array from csv
	            if( is_array(file($_FILES['games']['tmp_name']))){
	                $game_list = array_map('str_getcsv', file($_FILES['games']['tmp_name']));
	            }else{
	                $failed = true;
	                $message = lang('Please put a csv file!');
	            }

	            if($failed){
	                $this->alertMessage(2, $message);
	                redirect(BASEURL . 'game_description/viewGameListSettings');
	            }

	            array_walk($game_list, function(&$a) use ($game_list) {
	              $a = array_combine($game_list[0], $a);
	            });
	            array_shift($game_list);
	            #done rearrange

	            $game_description = [];
	            $game_description_for_clients = [];
	            $missing_games = [];
	            $missing_games['game_types'] = [];
	            $missing_games['dont_have_game_type'] = [];
	            $cnt_missing_game_code = 0;
	            $cnt_missing_game_type = 0;
	            $cnt_games_dont_gave_game_type = 0;
	            $cnt = 0;

	            $external_game_id_list = [];
	            $duplicate_games = [];
	            foreach ($game_list as $key => $game) {
	                $cnt++;

	                if (in_array($game['External Game Id'], $external_game_id_list)) {
	                    $add_count = $cnt + 1;
	                    $current_duplicate_games = isset($game_description[$key]) ? $game_description[$key] :$game['External Game Id'];
	                    @$current_duplicate_games['row_number'] = $add_count;
	                    array_push($duplicate_games, $current_duplicate_games);
	                    continue;
	                }
	                array_push($external_game_id_list, $game['External Game Id']);

	                $game_description[$key] = $this->preparePerGameDetails($game, $game_platform_id);

	                #used for posting game list to clients
	                $game_description_for_clients[$key]              = $game_description[$key];
	                $game_description_for_clients[$key]['game_type'] = $game['Game Type'];

	                if(empty($game['Game Type'])){
	                    if( ! in_array($game['Game Name'], $missing_games['dont_have_game_type'])){
	                        array_push($missing_games['dont_have_game_type'], $game['Game Name']);
	                        $cnt_games_dont_gave_game_type++;
	                    }
	                    unset($game_description[$key]);#remove the game if the game type id is null
	                    unset($game_description_for_clients[$key]);#remove the game if the game type id is null
	                    // continue;
	                }

	                if(empty($game_description[$key]['game_type_id'])){
	                    if( ! in_array($game['Game Type'], $missing_games['game_types'])){
	                        array_push($missing_games['game_types'], $game['Game Type']);
	                        $this->utils->debug_log("missing game type ========>",$game_description[$key]);
	                        $cnt_missing_game_type++;
	                    }
	                    unset($game_description[$key]);#remove the game if the game type id is null
	                    // continue;
	                }

	                //add jackpots specially for PT (1)
	                if ( ! empty($game['Jackpot Codes'])) {
	                    $jackpot_codes = explode(",", $game['Jackpot Codes']);

	                    $jackpot_game_type = "Progressive" . $game['Game Type'];
	                    foreach ($jackpot_codes as $key => $jackpot_code) {
	                        $cnt++;
	                        $jackpot_code = trim($jackpot_code);
	                        $game_description[$jackpot_code] = $this->preparePerGameDetails($game, $game_platform_id, $jackpot_code);
	                    }
	                }

	                #check if game code is set
	                if(empty($game['Game Code'])){
	                    $missing_games['game_codes_for'][$key] = $game['Game Name'];#list all missing game types
	                    unset($game_description[$key]);#remove the game if the game type id is null
	                    $cnt_missing_game_code++;
	                }

	                if($cnt_missing_game_type > 0 || $cnt_missing_game_code > 0){
	                    continue;
	                }

	            }
	            // echo "<pre>";print_r($game_description);exit;
	            $comparedChanges = $this->checkGameUpdate($game_description);
	            // $sync_game_description_result = null;
	            $sync_game_description_result = $this->game_description_model->syncGameDescription($game_description,false,false,null,true);

	            $available_game_types = $this->game_type_model->getGameTypeListByGamePlatformId($game_platform_id);

	            $data = [
	                    'missing_games'                     => $missing_games,
	                    'sync_game_description_result'      => $sync_game_description_result,
	                    'list_of_Games_that_has_been_save'  => isset($sync_game_description_result['list_of_games']) ? $sync_game_description_result['list_of_games']:0,
	                    'total_count_of_games'              => ($cnt_missing_game_code + $cnt - array_sum($sync_game_description_result['Counts'])) + array_sum($sync_game_description_result['Counts']),
	                    'total_count_of_success_save_games' => array_sum($sync_game_description_result['Counts']),
	                    'total_added_games'                 => $sync_game_description_result['Counts']['insert'],
	                    'total_updated_games'               => $sync_game_description_result['Counts']['update'],
	                    'count_of_missing_game_type'        => $cnt_missing_game_type,
	                    'available_game_types'              => $available_game_types,
	                    'game_platform_name'                => $this->external_system->getNameById($game_platform_id),
	                    'total_count_of_failed_no_game_codes' => $cnt_missing_game_code,
	                    'total_count_of_failed_to_save_games' => 0,
	                    'total_count_of_games_dont_gave_game_type' => $cnt_games_dont_gave_game_type,
	                    'count_of_unsave_games_due_game_type_failure' => 0,
	                    'comparedChanges' =>$comparedChanges
	            ];

	            if(!empty($update_multiple_client)){
	                $urls = [];
	                if(!empty($client_list_select_all)){
	                    $clients = $this->utils->getConfig('list_of_client_to_be_updated');
	                    foreach ($clients as $key) {
	                        if($client_environment == "live"){
	                            $urls[$key]= 'admin.'.$key.'.t1t.games' ;
	                        }elseif ($client_environment == "staging") {
	                            $urls[$key]= 'admin.staging.'.$key.'.t1t.games' ;
	                        }
	                    }
	                }else{
	                    foreach ($client_list as $key) {
	                        if($client_environment == "live"){
	                            $urls[$key]= 'admin.'.$key.'.t1t.games' ;
	                        }elseif ($client_environment == "staging") {
	                            $urls[$key]= 'admin.staging.'.$key.'.t1t.games' ;
	                        }
	                    }
	                }
	                $data['client_update_map'] = $this->postGameListCURL($game_description_for_clients, $urls);
	            }

	        } else {

	            if($game_platform_id == MG_API || $game_platform_id == EBET_MG_API){
	                $message = 'Mg game list cannot be updated by batch';
	            }else{
	                $message = 'Please Select Game Platform';
	            }

	            $data['message']   =  $message;
	        }

	        if (($this->session->userdata('sidebar_status') == NULL)) {
	            $this->session->set_userdata(array('sidebar_status' => 'active'));
	        }

	        // sets the history for breadcrumbs
	        if (($this->session->userdata('well_crumbs') == NULL)) {
	            $this->session->set_userdata(array('well_crumbs' => 'active'));
	        }

	        $this->history->setHistory('header_system.system_word23', 'game_description/viewGameDescription');

	        $loaded = $this->session->userdata('loaded');

	        $this->template->add_js('resources/js/bootstrap-notify.min.js');
	        $this->loadTemplate('System Management', '', '', 'system');
	        $this->template->write_view('main_content', 'system_management/game_description/batch_insert_update_game_list_result', $data);
	        $this->template->render();
	    }
    }

    private function checkGameUpdate($gameList){
    	$gameListVs = [];
    	foreach ($gameList as $key => &$game) {

	    	$result = $this->game_description_model->getGameByQuery('*','game_platform_id ='.$game['game_platform_id'] . ' AND external_game_id="'.$game['external_game_id'] .'"');
	    	if (!empty($result)) {
	    		$this->game_description_model->processMd5FieldsSetFalseIfNotExist($game,Game_description_model::MAIN_GAME_ATTRIBUTES,Game_description_model::GAME_DESC_INT_FIELDS);
	    		$game['md5_fields'] = $this->game_description_model->generateMD5SumOneRow($game,Game_description_model::MAIN_GAME_ATTRIBUTES,Game_description_model::GAME_DESC_INT_FIELDS);
	    		$result = reset($result);
	    		// unset($game['updated_at']);
	    		// unset($game['game_name']);
	    		// unset($result['game_name']);
	    		// unset($result['english_name']);
	    		// unset($game['english_name']);
	    		// unset($game['note']);
	    		// unset($game['progressive']);
	    		// unset($result['progressive']);
	    		// unset($game['game_order']);
	    		// unset($result['game_order']);
	    		// unset($result['note']);
	    		// unset($result['updated_at']);

	    		if ($game['md5_fields'] != $result['md5_fields']) {
	    			unset($game['md5_fields'],$game['updated_at']);
	    			unset($result['id'],$result['md5_fields'],$result['created_on']);

	    			$new =  array_diff($game,$result);
					$old = array_diff($result,$game);
					foreach ($old as $key => $value) {
						if (isset($game[$key])) {
							$new[$key] = $game[$key];
						}else{
							if (!array_key_exists($key, $new)) {
								$new[$key] = 0;
							}
						}
					}

					foreach ($new as $key => $value) {
						if (isset($result[$key])) {
							$old[$key] = $result[$key];
						}else{
							if (!array_key_exists($key, $old))
								$old[$key] = 0;
						}
					}

					if (empty($new) && empty($old)) {
						continue;
					}else{
	    				$gameListVs[$game['external_game_id']]['game_name'] = $game['game_name'];
					}

		    		$gameListVs[$game['external_game_id']]['new'] = $new;
		    		$gameListVs[$game['external_game_id']]['old'] = $old;

		    		// $gameListVs[$game['external_game_id']] = $game;
	    		}
	    		unset($result);
	    	}
    	}
    	return $gameListVs;
    }

    private function preparePerGameDetails($game, $game_platform_id, $jackpot_code = null){

        $false = ['no','n','false','disabled','disable','not available','remove','removed','not available on mobile','not available on desktop','n/a'];
        if(empty($game['Game Code']) || in_array(strtolower($game['Game Code']), $false)) return false;

        $game['Game Name'] = trim(str_replace("'", "’", $game['Game Name']));
        $chinese    = !empty($game['Chinese'])    && !in_array(strtolower($game['Chinese']), $false)?    str_replace("'", "’", trim($game['Chinese'])) : $game['Game Name'];
        $indonesian = !empty($game['Indonesian']) && !in_array(strtolower($game['Indonesian']), $false)? str_replace("'", "’", trim($game['Indonesian'])) : $game['Game Name'];
        $vietnamese = !empty($game['Vietnamese']) && !in_array(strtolower($game['Vietnamese']), $false)? str_replace("'", "’", trim($game['Vietnamese'])) : $game['Game Name'];
        $korean     = !empty($game['Korean'])     && !in_array(strtolower($game['Korean']), $false)?     str_replace("'", "’", trim($game['Korean'])) : $game['Game Name'];

        $game['Game Code'] = trim($game['Game Code']);
        $game['External Game Id'] = trim($game['External Game Id']);

        if (!empty($jackpot_code)) {
            $game['Game Name'] = $game['Game Name'] ." (" . $jackpot_code . ")";
            $chinese = $chinese ." (" . $jackpot_code . ")";
            $indonesian = $indonesian ." (" . $jackpot_code . ")";
            $vietnamese = $vietnamese ." (" . $jackpot_code . ")";
            $korean = $korean ." (" . $jackpot_code . ")";
            $game['Game Code'] = $jackpot_code;
            $game['External Game Id'] = $jackpot_code;
        }

        $game_description = [
            'game_name'             => $this->processLanguagesToJson($game['Game Name'],$chinese,$indonesian,$vietnamese,$korean),
            'english_name'          => $game['Game Name'],
            'external_game_id'      => !empty($game['External Game Id']) ? $game['External Game Id'] : $game['Game Code'],
            'game_code'             => $game['Game Code'],
            'updated_at'            => $this->utils->getNowForMysql(),
            'game_platform_id'      => $game_platform_id,
            'attributes'            => !empty($game['Attributes'])?$game['Attributes']:null,
            'related_game_desc_id'  => !empty($game['Related Game Desc Id'])?$game['Related Game Desc Id']:null,
            'game_type_id'          => $this->processGameType($game_platform_id,$game['Game Type']),
            'clientid'              => !empty($game['Client Id'])?$game['Client Id']:null,
            'moduleid'              => !empty($game['Module Id'])?$game['Module Id']:null,
            'sub_game_provider'     => !empty($game['Sub Game Provider'])?$game['Sub Game Provider']:null,
            'game_order'            => !empty($game['Game Order'])?$game['Game Order']:null,
            'note'                  => !empty($game['Note'])?$game['Note']:null,
            'progressive'           => $this->checkGameDescriptionColumn($game['Progressive'])?$game['Progressive']:false,
            'enabled_freespin'      => $this->checkGameDescriptionColumn($game['Free Spin'],true),
            'mobile_enabled'        => $this->checkGameDescriptionColumn($game['Mobile'],true),
            'flash_enabled'         => $this->checkGameDescriptionColumn($game['Flash'],true),
            'dlc_enabled'           => $this->checkGameDescriptionColumn($game['Download Pc'],true),
            'offline_enabled'       => $this->checkGameDescriptionColumn($game['Offline'],true),
            'status'                => $this->checkGameDescriptionColumn($game['Status'],true),
            'flag_show_in_site'     => $this->checkGameDescriptionColumn($game['Flag']),
            'html_five_enabled'     => $this->checkGameDescriptionColumn($game['Html5']),
            'enabled_on_ios'        => $this->checkGameDescriptionColumn($game['IOS']),
            'enabled_on_android'    => $this->checkGameDescriptionColumn($game['Android']),
            'flag_new_game'         => ($jackpot_code) ? false: true,
            'release_date'			=> !empty($game['release_date'])?date("Y-m-d H:i:s", strtotime($game['release_date'])):null,
        ];

        return $game_description;
    }

    private function processLanguagesToJson($english,$chinese,$indonesian,$vietnamese,$korean){
        return '_json:{"1":"'.$english.'","2":"'.$chinese.'","3":"'.$indonesian.'","4":"'.$vietnamese.'","5":"'.$korean.'"}';
    }

    public function postGameListCURL($param,$urls){
        $client_update_map = [];
        foreach ($urls as $url) {

            $update_url = $url  . "/cli/sync/postSyncGameDescription";
            $game_list = json_encode($param);

            $game_list = openssl_encrypt($game_list, "AES-256-CBC", '123456123456', 0, '1234567890123456');
            $game_list = base64_encode($game_list);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $update_url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "game_list=".$game_list);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

            $response = curl_exec($ch);
            curl_close($ch);

            $client_update_map[$url] = json_decode($response,true);

        }

        return $client_update_map;

    }

    private function processGameType($game_platform_id,$game_type){
        $game_type_code = trim(strtolower(str_replace(" ", "_", $game_type)));
        $game_type_str = null;

        switch ($game_type_code) {
        	case 'yoplay':
        		$game_type_str = $extra['game_type_code'] = 'yoplay';
        		break;
        	case 'tip':
        		$game_type_str = $extra['game_type_code'] = 'tip';
        		break;

        	default:
        		foreach ($this->game_tags as $key => $game_tag) {
		            if (in_array($game_type_code, $this->game_type_code_list[$game_tag['tag_code']])) {
		                $game_type_str = $extra['game_type'] = $game_tag['tag_name'];
		                $extra['game_type_code'] = $game_tag['tag_code'];
		            }
		        }
        		break;
        }

        if ( ! empty($game_type_str)) {
            return $this->game_type_model->checkGameType($game_platform_id,$game_type_str,$extra);
        }else{
            $this->utils->debug_log("processGameType =============>",$game_type_code);
            return null;
        }
    }

    public function checkGameDescriptionColumn($column_value,$column = null){
        $true = ['yes','y','true','enabled','enable','available','html5','flash',1];
        $false = ['no','n','false','disabled','disable','not available','remove','removed','not available on mobile','not available on desktop','n/a',0];
        if(!empty($column_value) && in_array(strtolower($column_value), $true)){
            return true;
        }elseif(!empty($column_value) && in_array(strtolower($column_value), $false) || !empty($column)){
            return 0;
        }
        if (empty($column_value)) {
            return 0;
        }

    }

    /*
     * OBSOLETE
     */
    public function processNewGames(){
		$this->permissions->checkSettings();
    	if (!$this->permissions->checkPermissions('game_description')) {
			$this->error_access();
		}else{
	        $this->load->model(['game_description_model','game_type_model','external_system','cashback_settings','promorules']);

	        $showGameTree = $this->config->item('show_particular_game_in_tree');
	        $selected_games = $this->loadSubmitGameTree($showGameTree)[2];
	        $viewed_selected_new_games = $this->input->post('viewed_selected_new_games');
	        $viewed_all_new_games = $this->input->post('viewed_all_new_games');
	        $add_to_cashback = $this->input->post('add_to_cashback');
	        $add_to_promotion = $this->input->post('add_to_promotion');
	        $save_and_unread = $this->input->post('save_and_unread');
	        $batchAddCashbackGameRules = false;
	        $add_promotion_success = false;

	        // $game_list = [];

	        $all_new_games = $this->game_description_model->getNewGames();
	        $all_new_games = json_decode(json_encode($all_new_games),true);

	        #change flag of selected games to false
	        if ( ! empty($viewed_selected_new_games) && !empty($selected_games)) {

	            foreach ($selected_games as $game_detail) {
	                $this->game_description_model->update($game_detail['id'],array('flag_new_game' => 0));
	            }

	            $this->alertMessage(1, lang('All selected Games Has been viewed!!!'));
	            redirect('game_description/viewNewGames');
	        }
	        #end

	        #change flag of all games to false
	        if ( ! empty($viewed_all_new_games) && !empty($all_new_games)) {

	            foreach ($all_new_games as $game_detail) {
	                $this->game_description_model->update($game_detail['id'],array('flag_new_game' => 0));
	            }

	            $this->alertMessage(1, lang('All Games Has been unread!!!'));
	            redirect('game_description/viewNewGames');
	        }
	        #end

	        if(empty($selected_games)){
	            $this->alertMessage(3, lang('No games to be unread!!!'));
	            redirect('game_description/viewNewGames');
	        }

	        #currently only using selected games (not of all the new games)
	        if ( ! empty($selected_games)) {

	            if (empty($add_to_cashback) && empty($add_to_promotion)) {
	                $this->alertMessage(2, lang('Please select where to add!!!'));
	                redirect('game_description/viewNewGames');
	            }

	            // if ( ! empty($add_to_cashback)) {
	            //     $selected_games['dont_remove_rule_id'] = true;
	            //     $cashback_common_rules = $this->cashback_settings->getCommonCashbackRule();
	            //     #add to all common cashback rules
	            //     foreach ($cashback_common_rules as $key => $row) {
	            //         $batchAddCashbackGameRules[$key] = $this->cashback_settings->batchAddCashbackGameRules($row['id'], $row['default_percentage'], $selected_games);
	            //     }
	            //     if (!empty($batchAddCashbackGameRules)) {
	            //         $this->alertMessage(1, lang('The selected game has been added to cashback'));
	            //     }else{
	            //         $this->alertMessage(2, lang('Error adding game to Cashback'));
	            //     }
	            // }

	            if ( ! empty($add_to_promotion)) {
	                $promo_rules = $this->promorules->getAllPromoRule();
	                $add_promotion_success = [];
	                #add to promotion
	                foreach ($promo_rules as $key => $current_promo_rule) {
	                    $selected_game_platforms = $this->promorules->getPromoRuleGamesProvider($current_promo_rule['promorulesId']);

	                    #check if the provider is selected
	                    // if ( ! in_array($game_detail['game_platform_id'], $selected_game_platforms)) continue;
	                    $add_promotion_success[$current_promo_rule['promorulesId']] = $this->promorules->batchAddAllowedGames($current_promo_rule['promorulesId'], $selected_games);
	                }

	                if (!empty($add_promotion_success)) {
	                    $this->alertMessage(1, lang('The selected game has been added to promotion'));
	                }else{
	                    $this->alertMessage(2, lang('Error adding game to promotion'));
	                }
	            }

	            if ( ! empty($add_promotion_success) && ! empty($add_to_cashback)) {
	                $this->alertMessage(1, lang('The selected game has been added to promotion and cashback!'));
	            }

	            if ( ! empty($save_and_unread)) {
	                if ( ! empty($batchAddCashbackGameRules) || ! empty($add_promotion_success)) {
	                    foreach ($selected_games as $game_detail) {
	                        $this->game_description_model->update($game_detail['id'],array('flag_new_game' => 0));
	                    }
	                }
	            }

	        }else{
	            $this->alertMessage(2, lang('Please Select a Game!!!'));
	        }

	        redirect('game_description/viewNewGames');
	    }
    }


	public function viewGameListSettings(){
		$this->permissions->checkSettings();
		$this->permissions->setPermissions(); //will set the permission for the logged in user
		if (!$this->permissions->checkPermissions('game_description')) {
			$this->error_access();
		} else {
			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			// sets the history for breadcrumbs
			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active'));
			}

			$this->history->setHistory('header_system.system_word23', 'game_description/view_game_list_settings');

			$data['list_of_client'] = $this->utils->getConfig('list_of_client_to_be_updated');

			$loaded = $this->session->userdata('loaded');

			$data['gameapis'] = $this->external_system->getAllActiveSytemGameApi();
			// $this->template->add_js('resources/js/game_description/game_description.js');
			$this->template->add_js('resources/js/game_description/multiselect.min.js');
			$this->template->add_js('resources/js/bootstrap-notify.min.js');
			$this->loadTemplate('System Management', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/game_description/view_game_list_settings', $data);
			$this->template->render();
		}
	}

	public function viewGameLobby(){
		$this->permissions->checkSettings();
		$this->permissions->setPermissions(); //will set the permission for the logged in user
		if (!$this->permissions->checkPermissions('game_description')) {
			$this->error_access();
		} else {
			$this->load->model(['game_description_model','game_type_model']);
			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}
			// sets the history for breadcrumbs
			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active'));
			}

			$this->history->setHistory('header_system.system_word23', 'game_description/ViewGameLobby');

			$loaded = $this->session->userdata('loaded');

			$data['gameapis'] = $this->external_system->getAllActiveSytemGameApi();
			$data['games'] = $this->game_description_model->getAllGames();
			$data['game_types'] = json_decode(json_encode($this->game_type_model->getGameTypes()),true);

			$filtered_game_types = [];
			foreach ($data['game_types'] as $key => $game_type) {
				if (in_array($game_type['game_platform_id'], self::GAME_API_WITH_LOBBYS)) {
					$game_type['game_with_lobby'] = true;
					$filtered_game_types[$key] = $game_type;
				}else{
					$filtered_game_types[$key] = $game_type;
				}
			}
			$data['game_types'] = json_decode(json_encode($filtered_game_types));

			$template_path = $this->utils->getUploadPath() . "/game_lobby_template/tmpl/";
			$files_uploaded = glob($template_path.'template_*');
			$data['templates'] = array_map(function($file) {
				return preg_replace("#.*template_([^\.]*)\.tmpl.*#", '$1', $file);
			}, $files_uploaded);

			// echo "<pre>";print_r($data);exit;
			// $this->addJsTreeToTemplate();
			$this->template->add_js('resources/js/bootstrap-notify.min.js');
			$this->template->add_js('resources/js/game_description/fuelux.min.js');
			$this->loadTemplate('System Management', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/game_description/view_game_lobby', $data);
			$this->template->render();
		}
	}

	public function uploadGameLobbyTemplate(){
		$this->load->library('multiple_image_uploader');

		$template_name  = $this->input->post('template_name');
		$template_file  = isset($_FILES['template_file']) ? $_FILES['template_file'] : null;
		$template_image = isset($_FILES['template_image']) ? $_FILES['template_image'] : null;
		$template_css   = isset($_FILES['template_css']) ? $_FILES['template_css'] : null;

		$template_path_tmpl   = $this->utils->getUploadPath() . "/game_lobby_template/tmpl";
		$template_path_images = $this->utils->getUploadPath() . "/game_lobby_template/images";
		$template_path_css    = $this->utils->getUploadPath() . "/game_lobby_template/css";

		$files_tmpl   = glob(realpath($template_path_tmpl).'/*');
		$files_images = glob(realpath($template_path_images).'/*');
		$files_css    = glob(realpath($template_path_css).'/*');

		$image_name   = 'image_'. $template_name;
		$css_name     = 'css_'. $template_name;
		$tmpl_name    = 'template_'. $template_name;

		if(empty($template_file['size'][0]) && empty($template_image['size'][0]) && empty($template_name) && empty($template_css['size'][0]) ) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Please Upload A file!'));
		} else {
			$config_css_theme = array(
					'allowed_types' => 'tmpl',
					'max_size'      => 15000,
					'overwrite'     => true,
					'remove_spaces' => true,
					'upload_path'   => $template_path_tmpl,
			);

			$config_css_theme = array(
					'allowed_types' => 'tmpl',
					'max_size'      => 15000,
					'overwrite'     => true,
					'remove_spaces' => true,
					'upload_path'   => $template_path_tmpl,
			);

			$config_image = array(
					'allowed_types' => "jpg|jpeg|png|PNG",//array("jpg","jpeg","png","gif", "PNG"),
					'max_size'      => 15000,
					'overwrite'     => true,
					'remove_spaces' => true,
					'upload_path'   => $template_path_images,
			);

			$response_image = $this->multiple_image_uploader->do_multiple_uploads($template_image, $template_path_images, $config_image, $image_name);
			if($response_image['status'] == "fail" ) {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $response_image['message']);
			}

			$response_css = $this->multiple_image_uploader->do_multiple_uploads($template_css, $template_path_css, $config_css_theme, $css_name);
			if($response_css['status'] == "fail" ) {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $response_image['message']);
			}

			$response_tmpl = $this->multiple_image_uploader->do_multiple_uploads($template_file, $template_path_tmpl, $config_css_theme, $tmpl_name);
			if($response_tmpl['status'] == "fail" ) {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $response_image['message']);
			}

			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('New template have successfully added!!'));

		}

		redirect('game_description/viewGameListSettings');
	}

	public function postGameLobbyDetails(){
		$this->load->model('game_lobby_model');

		$game_lobby_id = $this->input->post('game_lobby_id');
		$template_name = $this->input->post('template_name');
		$list_of_game_ids = $this->input->post('list_of_game_ids');
		$list_of_game_type_ids = $this->input->post('list_of_game_type_ids');
		$list_of_game_platform_ids = $this->input->post('list_of_game_platform_ids');
		$filtered_game_type_ids = [];

		foreach ($list_of_game_type_ids as $key => $value) {
			if ( ! empty($value)) {
				$filtered_game_type_ids[$key] = $value;
			}
		}

		$data = [
				"template_name" => $template_name,
				"game_ids" => json_encode($list_of_game_ids),
				"game_type_ids" => json_encode($filtered_game_type_ids),
				"game_platform_ids" => json_encode($list_of_game_platform_ids),
		];

		if(empty($game_lobby_id)){
			$id = $this->game_lobby_model->insertGameLobbyTemplate($data);
		}else{
			$id = $game_lobby_id;
			$this->game_lobby_model->updateGameLobbyTemplate($game_lobby_id,$data);
		}

		return $this->returnJsonResult($id);
	}

	public function getGamelistPerGameProviders(){
		$this->load->model('game_description_model');
		$game_provider_ids = $this->input->post('game_provider_ids');
		$data['game_platform_ids'] = array_unique ($game_provider_ids);

		$game_provider_ids = implode(',', $data['game_platform_ids']);
		$this->utils->debug_log("game_provider_ids ==============>",$game_provider_ids);

		$data = $this->game_description_model->getGamelistPerGameProviders($game_provider_ids);

		return $this->returnJsonResult($data);

	}

    /**
     * [getAllGames description]
     * @param  [int] $game_platform_id [defined system type]
     * @param  [string] $not_include_unknown [not include unknown]
     * @return [json]                   [returns json encoded game list]
     */
	public function getAllGames($game_platform_id = null,$not_include_unknown = null){
		$this->load->model('game_description_model');
		$new_games_only = null;

		$game_platform_id = (strtolower($game_platform_id) == "all") ? null : $game_platform_id;

		$game_list = $this->game_description_model->getAllGames(true,$game_platform_id,$new_games_only,$not_include_unknown);

		if( ! empty($not_include_unknown)){
			$game_apis = [1 => 'PT', 2 => 'AG', 3 => 'AG_FTP', 6 => 'MG', 7 => 'NT', 8 => 'BBIN', 10 => 'LB', 11 => 'ONE88', 13 => 'GPI', 14 => 'WIN9777', 15 => 'OPUS_GAMING', 19 => 'INTEPLAY', 20 => 'OPUS', 21 => 'ONESGAME', 22 => 'GSPT', 23 => 'GSAG', 24 => 'GAMEPLAY', 25 => 'BBTECHGSPOT', 26 => 'KENOGAME', 29 => 'AB', 30 => 'BS', 31 => 'IBC', 32 => 'GD', 33 => 'SBT', 34 => 'LXN', 35 => 'XHTDLOTTERY', 36 => 'WFT', 37 => 'GSKENO', 38 => 'HB', 39 => 'IMPT', 42 => 'QT', 43 => 'TTG', 44 => 'CROWN', 49 => 'ENTWINE', 50 => 'GAMESOS', 51 => 'FG', 53 => 'EBET', 55 => 'LAPIS', 56 => 'ISB', 57 => 'VIVO', 58 => 'ONEWORKS', 59 => 'SPORTSBOOK', 61 => 'FISHINGGAME', 64 => 'IMSLOTS', 66 => 'AGBBIN', 67 => 'AGHG', 68 => 'AGPT', 69 => 'AGSHABA', 70 => 'SEVEN77', 71 => 'GSMG', 72 => 'AGIN', 73 => 'HRCC', 74 => 'AGENCY', 80 => 'EBET2', 81 => 'BETEAST', 83 => 'UC', 84 => 'OPUS_SPORTSBOOK', 85 => 'OPUS_KENO', 92 => 'KUMA', 93 => 'PLAYSTAR', 97 => 'EZUGI', 120 => 'DT', 121 => 'IDN', 154 => 'SA_GAMING', 165 => 'EBET_BBIN', 178 => 'MG_QUICKFIRE', 183 => 'OG', 187 => 'JUMB_GAMING', 188 => 'SBTECH_GAMING', 189 => 'LADDER_GAMING', 220 => 'SPADE_GAMING', 232 => 'PRAGMATICPLAY', 234 => 'WHITE_LABEL', 244 => 'PNG', 247 => 'ULTRAPLAY', 253 => 'PINNACLE', 258 => 'VR', 267 => 'EVOLUTION_GAMING', 270 => 'EBET_SPADE_GAMING', 274 => 'EBET_BBTECH', 275 => 'EBET_IMPT', 281 => 'EBET_MG', 282 => 'YOPLAY', 288 => 'GAMEPLAY_SBTECH', 300 => 'EBET_KUMA', 305 => 'EBET_QT', 307 => 'YUNGU_GAME', 308 => 'LEBO_GAME', 313 => 'GSBBIN', 314 => 'DG', 319 => 'IPM_V2_SPORTS', 320 => 'XYZBLUE', 321 => 'EBET_GGFISHING', 330 => 'FINANCE', 338 => 'LD_CASINO', 339 => 'HG', 343 => 'LD_LOTTERY', 354 => 'EXTREME_LIVE_GAMING', 355 => 'EBET_AG', 356 => 'EBET_OPUS', 368 => 'EBET_DT', 392 => 'IG', 394 => 'GGPOKER_GAME', 471 => 'GENESISM4_GAME', 484 => 'SBTECH', 508 => 'ISB_SEAMLESS', 558 => 'SUNCITY', 1001 => 'BETMASTER', 2000 => 'GAME_GATEWAY', 1002 => 'T1PT', 1003 => 'T1YOPLAY', 1004 => 'T1LOTTERY', 1005 => 'T1GG', 1006 => 'T1OG', 1007 => 'T1AB', 1008 => 'T1MG', 1009 => 'T1DG', 1010 => 'T1PNG', 1011 => 'T1PRAGMATICPLAY', 1012 => 'T1TTG', 1013 => 'T1AGIN', 1014 => 'T1EBET', 1015 => 'T1SPADE_GAMING', 1016 => 'T1HB', 1017 => 'T1EZUGI', 1018 => 'T1JUMB', 1019 => 'T1VR', 1020 => 'T1ISB', 1021 => 'T1LOTTERY_EXT', 1022 => 'T1UC', 1023 => 'T1DT', 1024 => 'T1BBIN', 1025 => 'T1IDN', 1026 => 'T1GD', 1027 => 'T1QT', 1028 => 'T1GGPOKER_GAME', 1029 => 'T1IPM_V2_SPORTS', 2001 => 'PT_KRW', ];

			$new_list = [];
			foreach (json_decode(json_encode($game_list),true) as $key => $game) {
				$new_list[$key] = $game;
				$new_list[$key]['game_provider'] = $game_apis[$game['game_platform_id']];
			}

			$game_list = [];
			$game_list = $new_list;
		}

		return $this->showJsonReturnMessage($game_list);
    }


    /**
     * [getAllActivesGames]
     * @param  [int] $game_platform_id [defined system type]
     * @param  [string] $not_include_unknown [not include unknown]
     * @return [json]                   [returns json encoded game list]
     */
	public function getAllActiveGames($game_platform_id = null, $new_games_only = null, $not_include_unknown = null, $processActiveGames = true){
		$this->load->model('game_description_model');

		$game_platform_id = (strtolower($game_platform_id) == "all") ? null : $game_platform_id;
		$game_list = $this->game_description_model->getAllGames(true,$game_platform_id,$new_games_only,$not_include_unknown,$processActiveGames);
		return $this->showJsonReturnMessage($game_list);
    }

    /**
     * [convertGameListToJson description]
     * @param  [int] $game_platform_id [defined system type]
     * @return [json]                   [returns json encoded game list]
     */
    public function convertGameListToJson($game_platform_id,$ignore_disabled_games = 'true'){
        $this->load->model('game_description_model');

        $where = "game_description.game_platform_id =".$game_platform_id ." AND game_description.status = ". Game_description_model::DB_TRUE . " and game_type_code != 'unknown'";
        if ($ignore_disabled_games != Game_description_model::STR_TRUE) {
        	$where = "game_description.game_platform_id =".$game_platform_id ." AND game_type_code != 'unknown'";
        }

        $join = [
        	'table' => 'game_type',
        	'condition' => 'game_type.id = game_description.game_type_id',
        ];

        $game_list = $this->game_description_model->getGameByQuery("game_description.*,game_type.game_type_code",$where,null,$join);

        foreach ($game_list as &$game) {
            unset($game['created_on'],$game['updated_at'],$game['id'],$game['game_type_id'],$game['game_type'],$game['game_tag_id'],$game['md5_fields'],$game['deleted_at'],$game['auto_sync_enable']);
        }

        return $this->showJsonReturnMessage($game_list);
    }

    /**
     * [showJsonReturnMessage description]
     * @param  [array] $result [compiled arrays]
     * @return [json]         [returns json encoded result]
     */
    private function showJsonReturnMessage($result){
        # OUTPUT
        $this->output->set_header('Access-Control-Allow-Origin: *');
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($result, JSON_PRETTY_PRINT));
    }

	/**
	 * Generate for game description map: like game description gd
	 *
	 * @return  arranged arrays ready for game descripion map
	 */
	public function generateGamesInArrayFormat($game_platform_id, $new_games_only = null){
		$this->load->model('game_description_model');
		$data = $this->game_description_model->getAllGames(true,$game_platform_id, $new_games_only);

		$game_list = json_decode(json_encode($data),true);

		$arranged_games = [];

		foreach ($game_list as $key => $game) {
			$game['game_platform_id']     = '$api_id,';
			$game['game_name']            = "'" . str_replace("'", "’", $game['game_name']) ."',";
			$game['english_name']         = "'" . str_replace("'", "’", $game['english_name']) ."',";
			$game['game_code']            = "'" . $game['game_code'] ."',";
			$game['external_game_id']     = "'" . $game['external_game_id'] ."',";

            if (!empty($game['related_game_desc_id']) && $game['related_game_desc_id'] != 0) { $game['related_game_desc_id'] = '$db_true,'; } else {unset($game['related_game_desc_id']);}
            if (!empty($game['no_cash_back'])         && $game['no_cash_back']         != 0) { $game['no_cash_back']         = '$db_true,'; } else {unset($game['no_cash_back']);}
            if (!empty($game['void_bet'])             && $game['void_bet']             != 0) { $game['void_bet']             = '$db_true,'; } else {unset($game['void_bet']);}
			if (!empty($game['dlc_enabled'])          && $game['dlc_enabled']          != 0) { $game['dlc_enabled']          = '$db_true,'; } else { $game['dlc_enabled']          = '$db_false,'; }
			if (!empty($game['progressive'])          && $game['progressive']          != 0) { $game['progressive']          = '$db_true,'; } else { $game['progressive']          = '$db_false,';}
			if (!empty($game['mobile_enabled'])       && $game['mobile_enabled']       != 0) { $game['mobile_enabled']       = '$db_true,'; } else { $game['mobile_enabled']       = '$db_false,';}
			if (!empty($game['html_five_enabled'])    && $game['html_five_enabled']    != 0) { $game['html_five_enabled']    = '$db_true,'; } else { $game['html_five_enabled']    = '$db_false,';}
			if (!empty($game['enabled_freespin'])     && $game['enabled_freespin']     != 0) { $game['enabled_freespin']     = '$db_true,'; } else { $game['enabled_freespin']     = '$db_false,';}
			if (!empty($game['enabled_on_android'])   && $game['enabled_on_android']   != 0) { $game['enabled_on_android']   = '$db_true,'; } else { $game['enabled_on_android']   = '$db_false,';}
			if (!empty($game['enabled_on_ios'])       && $game['enabled_on_ios']       != 0) { $game['enabled_on_ios']       = '$db_true,'; } else { $game['enabled_on_ios']       = '$db_false,';}
			if (!empty($game['offline_enabled'])      && $game['offline_enabled']      != 0) { $game['offline_enabled']      = '$db_true,'; } else { $game['offline_enabled']      = '$db_false,';}

			if (empty($game['attributes'])       ) { unset($game['attributes']);}         else { $game['attributes'] = "'" . $game['attributes'] ."',";}
			if (empty($game['sub_game_provider'])) { unset($game['sub_game_provider']);}  else { $game['sub_game_provider']    = "'" . $game['sub_game_provider'] ."',"; }
			if (empty($game['note'])             ) { unset($game['note']);}              else { $game['note'] = "'" . $game['note'] ."',";}
			if (empty($game['game_order'])       ) { unset($game['game_order']);}        else { $game['game_order'] = "'" . $game['game_order'] ."',";}
			if (empty($game['clientid'])         ) { unset($game['clientid']);}          else { $game['clientid'] = "'" . $game['clientid'] ."',";}
            if (empty($game['moduleid'])         ) { unset($game['moduleid']);}          else { $game['moduleid'] = "'" . $game['moduleid'] ."',";}
			if (empty($game['demo_link'])        ) { unset($game['demo_link']);}         else { $game['demo_link'] = "'" . $game['demo_link'] ."',";}

			if ($game['status']             == true) { unset($game['status']);}             else { $game['status']              = '$db_false,'; }
			if ($game['flag_show_in_site']  == true) { unset($game['flag_show_in_site']);}  else { $game['flag_show_in_site']   = '$db_false,'; }
			if ($game['flash_enabled']      == true) { unset($game['flash_enabled']);}      else { $game['flash_enabled']       = '$db_false,'; }
			$game['game_type_id']         = $this->getGameTypeVariable($game['game_type']);
			unset($game['id'],$game['flag_new_game'],$game['created_on'],$game['updated_at'],$game['game_type'],$game['game_type_code'],$game['game_tag_id']);

			$arranged_games[$key] = $game;

		}
		echo "<pre>";print_r($arranged_games);
	}

	public function getGameTypeVariable($game_type_str){
		$game_type_str = strtolower($game_type_str);
		if (strpos($game_type_str, "arcade"))      { return '$gameTypeCodeMaps[$game_type_code_arcade],'; }
		if (strpos($game_type_str, "mini"))        { return '$gameTypeCodeMaps[$game_type_code_mini_games],'; }
		if (strpos($game_type_str, "other"))       { return '$gameTypeCodeMaps[$game_type_code_others],'; }
		if (strpos($game_type_str, "fish"))        { return '$gameTypeCodeMaps[$game_type_code_fishing_game],'; }
		if (strpos($game_type_str, "table game"))  { return '$gameTypeCodeMaps[$game_type_code_table_game],'; }
		if (strpos($game_type_str, "table and"))   { return '$gameTypeCodeMaps[$game_type_code_table_and_cards],'; }
		if (strpos($game_type_str, "gamble"))      { return '$gameTypeCodeMaps[$game_type_code_gamble],'; }
		if (strpos($game_type_str, "video"))       { return '$gameTypeCodeMaps[$game_type_code_video_poker],'; }
		if (strpos($game_type_str, "poker"))       { return '$gameTypeCodeMaps[$game_type_code_poker],'; }
		if (strpos($game_type_str, "live"))        { return '$gameTypeCodeMaps[$game_type_code_live_dealer],'; }
		if (strpos($game_type_str, "fixed"))       { return '$gameTypeCodeMaps[$game_type_code_fixed_odds],'; }
		if (strpos($game_type_str, "horce"))       { return '$gameTypeCodeMaps[$game_type_code_horce_racing],'; }
		if (strpos($game_type_str, "unknown"))     { return '$gameTypeCodeMaps[$game_type_code_unknown],'; }
		if (strpos($game_type_str, "scratch"))     { return '$gameTypeCodeMaps[$game_type_code_scratchcards],'; }
		if (strpos($game_type_str, "card"))        { return '$gameTypeCodeMaps[$game_type_code_card_games],'; }
		if (strpos($game_type_str, "casino"))      { return '$gameTypeCodeMaps[$game_type_code_casino],'; }
		if (strpos($game_type_str, "progressive"))  { return '$gameTypeCodeMaps[$game_type_code_progressive_slot_games],'; }
		if (strpos($game_type_str, "slot"))        { return '$gameTypeCodeMaps[$game_type_code_slots],'; }
		if (strpos($game_type_str, "lottery") || strpos($game_type_str, "keno"))        { return '$gameTypeCodeMaps[$game_type_code_lottery],'; }
		if (strpos($game_type_str, "e sport") || strpos($game_type_str, "e-sport"))     { return '$gameTypeCodeMaps[$game_type_code_esports],'; }
		if (strpos($game_type_str, "sports"))        { return '$gameTypeCodeMaps[$game_type_code_sports],'; }
	}

    public function get_frontend_games($game_platform_id = null,$game_type_code = null, $game_platform = "all"){
        $this->load->library('game_list_lib');
        $data = $this->game_list_lib->getFrontEndGames($game_platform_id, $game_type_code, $game_platform);

        # OUTPUT
        $this->returnJsonResult($data);
    }

    public function getAllGameCodesByGamePlatformId($game_platform_id){
        $this->load->model('game_description_model');
        $gameCodes = $this->game_description_model->getGameByQuery('game_code','game_platform_id = ' . $game_platform_id);
        $gameCodes = array_unique(array_column($gameCodes, 'game_code'));
        # OUTPUT
        $this->returnJsonResult($gameCodes);
    }

    public function getGameDescriptionHistory($gameDescriptionId){
    	$this->permissions->checkSettings();
		$this->permissions->setPermissions();
    	if (!$this->permissions->checkPermissions('game_description')) {
			$this->error_access();
		} else {
	    	$this->load->model('game_description_model');
	    	$int_fields = [
				'flash_enabled',
				'status',
				'flag_show_in_site',
				'no_cash_back',
				'void_bet',
				'game_order',
				'related_game_desc_id',
				'dlc_enabled',
				'progressive',
				'enabled_freespin',
				'offline_enabled',
				'mobile_enabled',
				'enabled_on_android',
				'enabled_on_ios',
				'flag_new_game',
				'html_five_enabled',
			];

	    	$gameDescriptionsHistory = $this->game_description_model->getGameDescriptionHistory($gameDescriptionId);
	    	if ($gameDescriptionsHistory) {
	    		foreach ($gameDescriptionsHistory as $key => &$gameDescriptionHistory) {
	    			foreach ($gameDescriptionHistory as $key => &$value) {
	    				if (empty($value))
	    					$value = "N/A";

	    				if (in_array($key, $int_fields)) {
							if (empty($value)) {
								$value = '<input disabled="disabled" type="checkbox" class="checkWhite user-success" />';
							}else{
								$value = '<input disabled="disabled" checked type="checkbox" class="checkWhite user-success" />';
							}
						}
	    				if ($key=="game_type"||$key=="game_name") {
	    					$value = lang($value);
	    				}
	    			}
	    		}
	    		$result['gameDescriptionHistory'] = $gameDescriptionsHistory;
	    		$result['status'] = 'success';
	    	}else{
	    		$result['status'] = 'fail';
	    	}
	        $this->returnJsonResult($result);
	    }
    }

    /**
	 * Manual Sync Game list
	 *
	 * @return rendered Template game api list
	 */
	public function dev_manual_sync_gamelist_from_gategateway() {
		$this->permissions->checkSettings();
		$this->permissions->setPermissions(); //will set the permission for the logged in user
		if (!$this->permissions->checkPermissions('dev_manual_sync_gamelist')) {
			$this->error_redirection();
		} else {
			$data['gameApis'] = $this->external_system->getAllActiveSytemGameApi();
			$this->template->add_js('resources/js/bootstrap-notify.min.js');
			$this->template->add_js('resources/js/select2.min.js');
			$this->template->add_css('resources/css/select2.min.css');
			$this->template->add_css('resources/css/game_description/game_description.css');
			$this->loadTemplate('Game Description', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/game_description/dev_manual_sync_gamelist_from_gamegateway', $data);
			$this->template->render();
		}
	}

	/**
	 * Manual Sync Game list from json game list
	 *
	 * @return rendered Template game api list
	 */
	public function dev_manual_sync_gamelist_from_json() {
		$this->permissions->checkSettings();
		$this->permissions->setPermissions(); //will set the permission for the logged in user
		if (!$this->permissions->checkPermissions('dev_manual_sync_gamelist')) {
			$this->error_redirection();
		} else {
			$data['gameApis'] = $this->external_system->getAllActiveSytemGameApi();
			$this->template->add_js('resources/js/bootstrap-notify.min.js');
			$this->template->add_js('resources/js/select2.min.js');
			$this->template->add_css('resources/css/select2.min.css');
			$this->template->add_css('resources/css/game_description/game_description.css');
			$this->loadTemplate('Game Description', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/game_description/dev_manual_sync_gamelist_from_json', $data);
			$this->template->render();
		}
	}

    public function do_manual_sync_gamelist_from_gamegateway($game_platform_id=null,$is_update=false,$force_game_list_update=false){
		$this->load->library("game_list_lib");
		echo $this->game_list_lib->do_sync_game_list_from_gamegateway($game_platform_id,$is_update,$force_game_list_update);
	}

    public function do_manual_sync_active_gamelist_from_gamegateway($game_platform_id=null, $processActiveGames = true){
		$this->load->library("game_list_lib");
		echo $this->game_list_lib->do_sync_game_list_from_gamegateway($game_platform_id, $processActiveGames);
	}

	public function sync_gamelist_from_json($game_platform_id,$isEnabled=false){
		$this->load->library("game_list_lib");
		echo $this->game_list_lib->sync_gamelist_from_json($game_platform_id);
	}

	/*
     * activate newly added games
     */
    public function activate_newly_added_games()
    {
		$this->permissions->checkSettings();
    	if (!$this->permissions->checkPermissions('game_description')) {
			$this->error_access();
		}
		else{

	        $this->load->model(['game_description_model','game_type_model']);
	        $all_new_games = json_decode(json_encode($this->game_description_model->getNewGames()),true);

	        #change flag of all games to false and status to enable
	        if (!empty($all_new_games)) 
	        {
	            foreach ($all_new_games as $game_detail) 
	            {
	            	# records action
    				$this->utils->recordAction('game_list', 'activated_new_game', "Manual Activate Game ID: ".$game_detail['id']);

	                $this->game_description_model->activate_new_games_from_gamegateway($game_detail['id'],['id' => $game_detail['id'],'flag_new_game' => Game_description_model::DB_FALSE,'flag_show_in_site' => Game_description_model::DB_TRUE,'status' => Game_description_model::ENABLED_GAME,'updated_at'=>$this->utils->getNowForMysql()]);
	            }

	            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('All Newly Added Games Has been activated!!!'));
	            redirect('game_description/viewNewGames');
	        }
	    }
    }
}

/* End of file game_description.php */
/* Location: ./application/controllers/game_description.php*/