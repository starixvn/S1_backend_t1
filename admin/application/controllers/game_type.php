<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';

/**
 * Game Type
 *
 * Game Type Controller for FHI OG System
 *
 * General behaviors include
 * * Load Template
 * * Display messages for users
 * * Display Game types
 * * Add/delete/update game types
 * * Get Game Platforms
 *
 * @category gametype_management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Game_Type extends BaseController {

	function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->library(array('permissions', 'form_validation', 'template', 'pagination', 'data_tables'));

		$this->permissions->checkSettings();
		$this->permissions->setPermissions();

		$this->load->model(array('game_type_model', 'external_system'));
	}

	/**
	 * overview : error access
	 *
	 * detail : show error message if user can't access the page
	 */
	private function error_access() {
		$this->loadTemplate('Game List Management', '', '', 'player');
		$systemUrl = $this->utils->activeSystemSidebar();
		$data['redirect'] = $systemUrl;

		$message = lang('con.plm01');
		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		$this->template->write_view('main_content', 'error_page', $data);
		$this->template->render();
	}
	
	
	/**
	 * overview : loads template
	 *
	 * detail : load all javascript/css resources, customize head contents
	 *
	 * @param string 	$title
	 * @param string 	$description
	 * @param string 	$keywords
	 * @param string 	$activenav
	 */
	protected function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->add_js('resources/js/game_type/game_type.js');
		$this->template->add_js('resources/js/datatables.min.js');
		$this->template->add_css('resources/css/general/style.css');
		$this->template->add_css('resources/css/datatables.min.css');
		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('userId', $this->authentication->getUserId());
		$this->template->write_view('sidebar', 'system_management/sidebar');
	}

	/**
	 * Show message for users
	 *
	 * @param   int 	$type
	 * @param   string 	$message
	 * @return  set session user data
	 */
	const MSG_SUCCESS = 1, MSG_DANGER = 2, MSG_WARNING = 3;
	protected function alertMessage($type, $message) {
		switch ($type) {
		case self::MSG_SUCCESS:
			$show_message = array(
				'result' => 'success',
				'message' => $message,
			);
			break;

		case self::MSG_DANGER:
			$show_message = array(
				'result' => 'danger',
				'message' => $message,
			);
			break;

		case self::MSG_WARNING:
			$show_message = array(
				'result' => 'warning',
				'message' => $message,
			);
			break;
		}
		$this->session->set_userdata($show_message);
	}

	/**
	 * overview : display game types
	 *
	 * detail : render template with array data
	 * @return void
	 */
	public function viewGameType() {
		if (!$this->permissions->checkPermissions('game_type')) {
			$this->error_access();
		} else {

			// highlight sidebar menu item
			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			// sets the history for breadcrumbs
			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active'));
			}

			$this->history->setHistory('header_system.system_word96', 'game_type/viewGameType');
			$data['gameTypes'] = json_decode(json_encode($this->game_type_model->getGameTypesForDisplay()), true);
			$data['game_platforms'] = $this->external_system->getAllActiveSytemGameApi();
			$this->template->add_css('resources/css/game_type/game_type.css');
			$this->loadTemplate('System Management - Game Type', '', '', 'system');
			$this->template->write_view('main_content', 'system_management/game_type', $data);
			$this->template->render();
		}
	}

	/**
	 * [viewGameTypeHistory show game type history]
	 * @return [type] [render view]
	 */
	public function viewGameTypeHistory() {
		if (!$this->permissions->checkPermissions('game_type_history')) {
			$this->error_access();
		} else {

			// highlight sidebar menu item
			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			// sets the history for breadcrumbs
			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active'));
			}

			$this->history->setHistory('header_system.system_word96', 'game_type/viewGameType');
			$data['gameTypes'] = json_decode(json_encode($this->game_type_model->getGameTypesForDisplay()), true);
			$data['game_platforms'] = $this->external_system->getAllActiveSytemGameApi();
			$this->template->add_css('resources/css/game_type/game_type.css');
			$this->loadTemplate('System Management - Game Type', '', '', 'game_type');
			$this->template->write_view('main_content', 'system_management/game_type_history', $data);
			$this->template->render();
		}
	}

	// -- AJAX actions --
	/**
	 * overview : get all game types
	 *
	 * @return : array
	 */
	public function getAllGameType() {
		# START DEFINE COLUMNS #################################################################################################################################################
		$request = $this->input->post();

		$result = $this->game_type_model->getAllGameType($request);

		$this->returnJsonResult($result);
	}
	
	/**
	 * [getAllGameTypeHistory ajax used by datatable]
	 * @return [type] [description]
	 */
	public function getAllGameTypeHistory() {
		# START DEFINE COLUMNS #################################################################################################################################################
		$request = $this->input->post();

		$result = $this->game_type_model->getAllGameTypeHistory($request);

		$this->returnJsonResult($result);
	}

	/**
	 * overview : get game platforms
	 *
	 * @return array
	 */
	public function getGamePlatforms() {
		$this->load->model('external_system');
		$game_apis = $this->external_system->getAllGameApis();
		$data['game_apis'] = json_decode(json_encode($game_apis), true);
		$jsonResult = array('status' => 'success', 'data' => $data);
		$this->returnJsonResult($jsonResult);
	}

	/**
	 * overview : get game type
	 *
	 * detail : get all game types by id
	 * @param  int  	$id
	 * @return array
	 */
	public function getGameType($id) {
		$gameTypeObj = $this->game_type_model->getGameTypeById($id);
		if ($gameTypeObj) {
			$jsonResult = array('status' => 'success', 'data' => $gameTypeObj);
		} else {
			$jsonResult = array('status' => 'fail', 'id' => $id);
		}
		$this->returnJsonResult($jsonResult);
	}

	/**
	 * overview : saves game type
	 *
	 * detail : save newly created game type
	 * @return array
	 */
	public function saveGameType() {
		if (!$this->permissions->checkPermissions('game_type')) {
			$this->error_access();
		} else {
			// Populate POST form data. (TODO: use ORM)
			$data = [
				'id'					=> $this->input->post('id'),
				'game_platform_id'		=> $this->input->post('game_platform_id'),
				'game_type'				=> $this->input->post('game_type'),
				'game_type_lang'		=> $this->input->post('game_type_lang'),
				'game_type_code'		=> $this->input->post('game_type_code'),
				'order_id'				=> $this->input->post('order_id'),
				'note'					=> $this->input->post('note'),
				'game_tag_id'			=> $this->input->post('game_tag_id'),
				'auto_add_new_game'		=> ($this->input->post('auto_add_new_game') != null) ? true:false,
				'auto_add_to_cashback'	=> ($this->input->post('auto_add_to_cashback') != null) ? true:false,
				'status'				=> ($this->input->post('status') != null) ? true:false,
				'flag_show_in_site'		=> ($this->input->post('flag_show_in_site') != null) ? true:false,
			];

			if ($data['id'] >= 0) {
			    $this->alertMessage(self::MSG_SUCCESS, lang('sys.ga.succsaved'));
				// Update existing record
				$updateStatus = $this->game_type_model->update($data);
				$jsonResult['status'] = $updateStatus ? 'success' : 'fail';
			} else {
				$this->utils->debug_log("saveGameType=======================",$data);
				// Insert new record
				$newRecordId = $this->game_type_model->create($data);
			    if ($newRecordId) {
			    	$this->alertMessage(self::MSG_SUCCESS, lang('sys.ga.succsaved'));
			    }else{
			    	$this->alertMessage(self::MSG_WARNING, lang('sys.gd26'));
			    }

				$jsonResult['status'] = $newRecordId > 0 ? 'success' : 'fail';
				$jsonResult['id'] = $newRecordId;
			}

			$this->utils->recordAction('edit_game_type', 'edit_game_type', "Game Type ");

			$this->returnJsonResult($jsonResult);
		}
	}

	public function getGameTypeByPlatformId($gamePlatformId) {
		$this->returnJsonResult($this->game_type_model->getTransGameTypeListByGamePlatformId($gamePlatformId));
	}

	public function deleteGameType($gameTypeId){
		if (!$this->permissions->checkPermissions('game_type')) {
			$this->error_access();
		} else {
			$this->load->model('game_type_model');
			$success = $this->game_type_model->deleteGameType($gameTypeId);
			if ($success) {
				$this->returnJsonResult(['status' => 'success']);
			}else{
				$this->returnJsonResult(['status' => 'fail']);
			}
		}
	}
	
	/**
	 * [getGameTypeHistoryById used by ajax to get available game types]
	 * @param  [int] $gameTypeId [description]
	 * @return [type]             [description]
	 */
	public function getGameTypeHistoryById($gameTypeId){
		if (!$this->permissions->checkPermissions('game_type')) {
			$this->error_access();
		} else {
			$int_fields = [
				'status',
				'flag_show_in_site',
				'auto_add_new_game',
				'related_game_type_id',
				'auto_add_to_cashback',
			];
			$this->load->model('game_type_model');
			$result = $this->game_type_model->getGameTypeHistoryById($gameTypeId);
			if ($result) {
				foreach ($result as $key => &$gameTypesHistory) {
					foreach ($gameTypesHistory as $key => &$value) {
						if (!isset($value)) 
							$value = "N/A";
						if (in_array($key, $int_fields)) {
							if (empty($value)) {
								$value = '<input disabled="disabled" type="checkbox" class="checkWhite user-success" />';
							}else{
								$value = '<input disabled="disabled" checked type="checkbox" class="checkWhite user-success" />';
							}
						}

						if ($key=="action") {
							$value = lang($value);
						}
					}
				}

				$this->returnJsonResult(['status' => 'success','gameTypesHistory' => $result]);
			}else{
				$this->returnJsonResult(['status' => 'fail']);
			}
		}
	}

}

/* End of file game_type.php */
/* Location: ./application/controllers/game_type.php*/
