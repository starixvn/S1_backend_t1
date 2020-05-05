<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';

/**
 * General behaviors include
 * * List of VIP group manager
 * * Exporting VIP group manage list through excel
 * * Adding group manager
 * * Modifying VIP group manager details
 * * Increasing/Decreasing group level count
 *
 * @category Player Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Vipsetting_Management extends BaseController {

	const ACTION_MANAGEMENT_TITLE = 'VIP Setting Management';

	const UPGRADE_SCHEDULE = ['daily' => 1, 'weekly' => 2, 'monthly' => 3, 'yearly' => 4];

	// deprecated in new vip upgrade/downgrade feature
	const UPGRADE_SETTING = ['upgrade_only' => 1, 'upgrade_and_downgrade' => 2];

	const UPGRADE_ACTIVE = 1;

	const DOWNGRADE_SCHEDULE = [ 'daily' => 1, 'weekly' => 2, 'monthly' => 3, 'yearly' => 4 ];

	const DOWNGRADE_SETTING = [ 'downgrade_only' => 1, 'upgrade_and_downgrade' => 2];

	const DOWNGRADE_ACTIVE = 1;

	const UPGRADE = 1;
	const DOWNGRADE = 2;

	const ERROR_COUNT = 0;

	function __construct() {
		parent::__construct();

		$this->load->helper(array('date_helper', 'url'));
		$this->load->library(array('form_validation', 'template', 'pagination', 'permissions', 'report_functions', 'payment_manager', 'depositpromo_manager'));
		$this->load->model(array('group_level'));
		$this->permissions->checkSettings();
		$this->permissions->setPermissions();
	}

	/**
	 * Loads template for view based on regions in
	 * config > template.php
	 *
	 */
	private function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->add_js('resources/js/player_management/vipsetting_management.js');
		$this->template->add_css('resources/css/vipsetting_management/style.css');
		$this->template->add_js('resources/js/jquery.numeric.min.js');
		//$this->template->add_js('resources/js/datatables.min.js');
		//$this->template->add_js('resources/js/jquery.dataTables.min.js');
		//$this->template->add_js('resources/js/dataTables.responsive.min.js');

		$this->template->add_css('resources/css/general/style.css');
		//$this->template->add_css('resources/css/jquery.dataTables.css');
		//$this->template->add_css('resources/css/dataTables.responsive.css');
		$this->template->add_css('resources/css/datatables.min.css');
		$this->template->add_js('resources/js/datatables.min.js');
		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('userId', $this->authentication->getUserId());
	}

	/**
	 * Shows Error message if user can't access the page
	 *
	 * @return	rendered Template
	 */
	private function error_access() {
		$this->loadTemplate(lang('Player Management'), '', '', 'player');
		$playerUrl = $this->utils->activePlayerSidebar();
		$data['redirect'] = $playerUrl;

		$message = lang('con.vsm01');
		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		$this->template->write_view('main_content', 'error_page', $data);
		$this->template->render();
	}

	/**
	 * Index Page for vip setting management
	 *
	 */
	public function index() {
		redirect('vipsetting_management/vipGroupSettingList');
	}

	/**
	 * View page for VIP Player
	 *
	 * @return	redered template
	 */
	public function vipGroupSettingList() {
		if (!$this->permissions->checkPermissions('vip_group_setting')) {
			$this->error_access();
		} else {
			$this->load->model(array('group_level', 'operatorglobalsettings'));

			$sort = "vipSettingId";
			$vipSettingList = $this->group_level->getVIPSettingList($sort, null, null);
			$data['data'] = $this->utils->stripHtmlTagsOfArray($vipSettingList);
			// $data['data'] = $this->group_level->getVIPSettingList($sort, null, null);
			$data['operator_setting'] = json_decode($this->operatorglobalsettings->getSettingValue("vip_welcome_text"));
			$this->loadTemplate('VIP Setting Management', '', '', 'player');
			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->add_css('resources/css/tutorial/tutorial.css');
			$this->template->write_view('main_content', 'player_management/vipsetting/view_vip_setting_list', $data);
			$this->template->render();
		}
	}

	/**
	 * export report to excel
	 *
	 *
	 * @return	excel format
	 */
	public function exportToExcel() {

		$this->load->library('excel');
		$this->load->model(array('group_level'));

		$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Exported VIP Group List', "User " . $this->authentication->getUsername() . " exported VIP Group List");
		$result = $this->group_level->getVIPSettingListToExport();

		//$this->excel->to_excel($result, 'vipgrouplist-excel');
		$d = new DateTime();
		$this->utils->create_excel($result, 'vipgrouplist_' . $d->format('Y_m_d_H_i_s') . '_' . rand(1, 999));
	}

	public function get_vipgroupsetting_pages($segment) {
		$this->load->model(array('group_level'));
		$sort = "groupName";

		$data['count_all'] = count($this->group_level->getVIPSettingList($sort, null, null));
		$config['base_url'] = "javascript:get_vipgroupsetting_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 10;
		$config['num_links'] = 2;

		$config['first_tag_open'] = '<li>';
		$config['last_tag_open'] = '<li>';
		$config['next_tag_open'] = '<li>';
		$config['prev_tag_open'] = '<li>';
		$config['num_tag_open'] = '<li>';

		$config['first_tag_close'] = '</li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_close'] = '</li>';
		$config['num_tag_close'] = '</li>';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$this->pagination->initialize($config);

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['data'] = $this->group_level->getVIPSettingList($sort, $config['per_page'], $segment);

		$this->load->view('player_management/vipsetting/ajax_view_vip_setting_list', $data);
	}

	/**
	 * add vip group
	 *
	 * @return	rendered template
	 */
	public function addVipGroup() {
		//modify by jerbey 5-19-2017
		$vip_default_cover = $this->input->post('vip_default_cover');
		$image = !empty($_FILES['vip_cover']) ? $_FILES['vip_cover'] : null;
		$image_file_uploaded = null;
		$path = VIPCOVERPATH;
		$config = array(
			'allowed_types' => "jpg|jpeg|png|gif",
			'max_size' => $this->utils->getMaxUploadSizeByte(),
			'overwrite' => true,
			'remove_spaces' => true,
			'upload_path' => $path,
		);

		if (!$vip_default_cover) {
			$this->utils->debug_log('---image data--- post', json_encode($image));
			if (!empty($image) && $image['size'] != 0) {
				$this->load->library('multiple_image_uploader');
				$response = $this->multiple_image_uploader->do_multiple_uploads($image, $path, $config);
				if ($response['status'] == "fail") {
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, $response['message']);
				} else {
					$image_file_uploaded = $response['filename'][0];
				}
			}
		}
		//endmodify

		$this->form_validation->set_rules('groupName', 'Group Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('groupLevelCount', 'Group Level Count', 'trim|required|xss_clean|is_numeric');
		$this->form_validation->set_rules('groupDescription', 'Group Description', 'trim|required|xss_clean');

		if ($this->form_validation->run() == false) {
			$message = lang('con.vsm03');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		} else {

			$groupName = $this->utils->stripHTMLtags($this->input->post('groupName'));
			$groupLevelCount = $this->input->post('groupLevelCount');
			$groupDescription = $this->utils->stripHTMLtags($this->input->post('groupDescription'));
			$vipsettingId = $this->input->post('vipsettingId');
			$today = $this->utils->getNowForMysql();
			$vipGroupId = $this->input->post('vipGroupId');
			$can_be_self_join_in = $this->input->post('can_be_self_join_in');
			$image_name = $this->input->post('image_name');

			$this->load->model(array('group_level'));

			if ($vipGroupId != '') {

				$data = array(
					'groupName' => ucfirst($groupName),
					'groupDescription' => $groupDescription,
					'updatedBy' => $this->authentication->getUserId(),
					'updatedOn' => $today,
					'can_be_self_join_in' => $can_be_self_join_in ? 1 : 0,
					'image' => ($vip_default_cover) ? "default_vip_cover.jpeg" : ((!empty($_FILES['vip_cover']['name'])) ? $image_file_uploaded : $image_name),
				);
				$this->group_level->editVIPGroup($data, $vipGroupId);
				$message = lang('con.vsm04') . " <b>" . lang($groupName) . "</b> " . lang('con.vsm05');

				// $data = array(
				// 	'username' => $this->authentication->getUsername(),
				// 	'management' => 'Edit VIP Setting Management',
				// 	'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
				// 	'action' => 'Edit VIP Group Name: ' . $vipGroupId,
				// 	'description' => "User " . $this->authentication->getUsername() . " edit new vip group id: " . $vipGroupId,
				// 	'logDate' => $today,
				// 	'status' => 0,
				// );

				$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Edit VIP Group Name',
					"User " . $this->authentication->getUsername() . " edit new vip group id: " . $vipGroupId);
			} else {
				$isGroupNameExist = $this->group_level->getVipGroupName($this->input->post('groupName'));
				if ($isGroupNameExist) {
					$message = lang('con.vsm06');
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				} elseif ($this->input->post('groupLevelCount') <= 0) {
					$message = lang('con.vsm07');
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				} else {
					$data = array(
						'groupName' => $groupName,
						'groupLevelCount' => $groupLevelCount,
						'groupDescription' => $groupDescription,
						'createdBy' => $this->authentication->getUserId(),
						'createdOn' => $today,
						'status' => 'active',
						'can_be_self_join_in' => $can_be_self_join_in ? 1 : 0,
						'image' => ($vip_default_cover) ? "default_vip_cover.jpeg" : ((!empty($_FILES['vip_cover']['name'])) ? $image_file_uploaded : $image_name),
					);
					$this->group_level->addVIPGroup($data);
					$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Add VIP Group Name',
						"User " . $this->authentication->getUsername() . " edit new vip group id: " . $vipGroupId);
					$message = lang('con.vsm04') . " <b>" . lang($groupName) . "</b> " . lang('con.vsm08');
				}
			}

			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		}

		redirect('vipsetting_management/vipGroupSettingList');
	}

	/**
	 * Add vip group level
	 * @param 	int
	 * @return	loaded view page
	 */
	public function increaseVipGroupLevel($vipSettingId) {
		$this->group_level->increaseVIPGroupLevel($vipSettingId);
		redirect('/vipsetting_management/viewVipGroupRules/' . $vipSettingId);
	}

	/**
	 * Delete vip group level
	 * @param 	int
	 * @return	loaded view page
	 */
	public function decreaseVipGroupLevel($vipSettingId) {
		$result = $this->group_level->decreaseVipGroupLevel($vipSettingId);
		redirect('/vipsetting_management/viewVipGroupRules/' . $vipSettingId);
	}

	/**
	 * Edit vip group level
	 * @param 	int
	 * @return	loaded view page
	 */
	public function editVipGroupLevel($vipgrouplevelId) {
        if (!$this->permissions->checkPermissions('vip_group_setting')) {
            $this->error_access();
            return;
        }

		$data['showGameTree'] = $this->config->item('show_particular_game_in_tree');

		$this->load->model(array('group_level', 'game_description_model', 'promorules'));
		// $this->load->model('game');

		$data['vipgrouplevelId'] = $vipgrouplevelId;

		$data['data'] = $this->utils->stripHtmlTagsOfArray($this->group_level->getVipGroupLevelDetails($vipgrouplevelId));

		if (!empty($data['data']['vip_upgrade_id'])) {
			$data['data']['upgrade_setting'] = $this->group_level->getUpgradeSettingById($data['data']['vip_upgrade_id']);
		}
		if (!empty($data['data']['period_up_down_2'])) {
			$data['data']['period_up_down_2'] = json_decode($data['data']['period_up_down_2'], true);
		}
		if (!empty($data['data']['vip_downgrade_id'])) {
			$data['data']['downgrade_setting'] = $this->group_level->getUpgradeSettingById($data['data']['vip_downgrade_id']);
		}
		if(!empty($data['data']['period_down'])) {
			$data['data']['period_down'] = json_decode($data['data']['period_down'], true);
		}

		$highestGroupLevelsId = $this->group_level->getHighestGroupLevelsId();
		$groupLevels = $this->group_level->getGroupLevels($highestGroupLevelsId);
		$data['highestGroupLevelsId'] = $highestGroupLevelsId;

		$data['groupLevels'] = $groupLevels;
		// $data['vipsettingGames'] = $this->group_level->getVipSettingAllowedGame($vipgrouplevelId);

		if ($this->utils->isEnabledFeature('only_manually_add_active_promotion')) {
			$data['promoCms'] = $this->promorules->getAvailablePromoCMSList();
		} else {
			$data['promoCms'] = $this->promorules->getAllPromoCMSList();
		}

		// $data['vipSettingGameProvider'] = $this->group_level->getVipSettingGamesProvider($vipgrouplevelId);
		// $data['vipsettingGamesType'] = $this->group_level->getVipSettingsGamesType($vipgrouplevelId);
		// $data['cashbackAllowedGame'] = $this->depositpromo_manager->getCashbackAllowedGame($vipgrouplevelId);
		// $data['cashbackBonusPerGame'] = $this->group_level->getCashbackBonusPerGame($data['data']['vipsettingcashbackruleId']);
		//echo "<pre>";print_r($data);exit;
		// $data['gameTree'] = $this->group_level->getGameTreeForGroupLevel($vipgrouplevelId);

		$this->loadTemplate('VIP Setting Management', '', '', 'player');
		$this->template->write_view('sidebar', 'player_management/sidebar');
		$this->template->add_css('resources/css/collapse-style.css');
		$this->template->add_css('resources/css/jquery-checktree.css');

		$this->addBoxDialogToTemplate();

		$this->addJsTreeToTemplate();

		$this->template->write_view('main_content', 'player_management/vipsetting/view_editvipgrouplevel_rules', $data);
		$this->template->render();

	}
	// public function getLevelCopySample() {
	// 	$vipLevel = $this->input->post('vipLevel');
	// 	$vipSettingId = $this->input->post('highestGroupLevelsId');
	// 	$vipGroupLevelSetting = $this->group_level->getVipGroupLevelSetting($vipSettingId, $vipLevel);
	// 	$cashbackAllowedGame = $this->depositpromo_manager->getCashbackAllowedGame($vipSettingId);
	// 	$arr = array('status' => 'success', 'vipGroupLevelSetting' => $vipGroupLevelSetting, 'cashbackAllowedGame' => $cashbackAllowedGame);
	// 	echo json_encode($arr);

	// }

	/**
	 * Delete VIP group level
	 *
	 * @param 	vipgrouplevelId
	 * @return	redirect
	 */
	public function deleteVIPGroupLevel($vipgrouplevelId) {
		// if(!$this->permissions->checkPermissions('delete_vipgrouplevel')){
		// 	$this->error_access();
		// } else {

		if (!$this->permissions->checkPermissions('vip_group_setting')) {
			$this->error_access();
		}

		$this->group_level->deletevipgrouplevel($vipgrouplevelId);

		$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Delete VIP Group', "User " . $this->authentication->getUsername() . " deleted vip group level");

		$message = lang('con.vsm09');
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		redirect('vipsetting_management/viewVipGroupRules/' . $vipgrouplevelId, 'refresh');
		//}
	}

	/**
	 * view vip group rules
	 *
	 * @param 	vipgrouplevelId
	 * @return	rendered template
	 */
	public function viewVipGroupRules($vipgrouplevelId) {
		if (!$this->permissions->checkPermissions('vip_group_setting')) {
			$this->error_access();
		} else {
			$this->loadTemplate('VIP Setting Management', '', '', 'player');
			$data['data'] = $this->utils->stripHtmlTagsOfArray($this->group_level->getVIPGroupRules($vipgrouplevelId));
			$data['vipSettingId'] = $vipgrouplevelId;

			$settingData = $this->group_level->getVIPGroupRules($vipgrouplevelId);

			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/vipsetting/view_vip_setting_rules', $data);
			$this->template->render();
		}
	}

	/**
	 * sort vip group
	 *
	 * @param 	sort
	 * @return	void
	 */
	public function sortVipgroup($sort) {
		$data['count_all'] = count($this->group_level->getVIPSettingList($sort, null, null));
		$config['base_url'] = "javascript:get_vipgroupsetting_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 10;
		$config['num_links'] = 2;

		$config['first_tag_open'] = '<li>';
		$config['last_tag_open'] = '<li>';
		$config['next_tag_open'] = '<li>';
		$config['prev_tag_open'] = '<li>';
		$config['num_tag_open'] = '<li>';

		$config['first_tag_close'] = '</li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_close'] = '</li>';
		$config['num_tag_close'] = '</li>';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$this->pagination->initialize($config);

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['data'] = $this->group_level->getVIPSettingList($sort, $config['per_page'], null);

		$this->load->view('player_management/vipsetting/ajax_view_vip_setting_list', $data);
	}

	/**
	 * activate vip group
	 *
	 * @param 	vipsettingId
	 * @param 	status
	 * @return	redirect
	 */
	public function activateVIPGroup($vipsettingId, $status) {
		$data['vipsettingId'] = $vipsettingId;
		$data['status'] = $status;

		$this->group_level->activateVIPGroup($data);

		// $data = array(
		// 	'username' => $this->authentication->getUsername(),
		// 	'management' => 'VIP Setting Management',
		// 	'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
		// 	'action' => 'Update status of vip group id:' . $vipsettingId . 'to status:' . $status,
		// 	'description' => "User " . $this->authentication->getUsername() . " edit vip group status to " . $status,
		// 	'logDate' => date("Y-m-d H:i:s"),
		// 	'status' => 0,
		// );

		// $this->report_functions->recordAction($data);

		$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Update status of vip group', "User " . $this->authentication->getUsername() . " edit vip group status to " . $status);

		redirect('vipsetting_management/vipGroupSettingList');
	}

	/**
	 * Delete get vip group details
	 *
	 * @param 	int
	 * @return	redirect
	 */
	public function getVIPGroupDetails($vipsetting_id) {
		echo json_encode($this->group_level->getVIPGroupDetails($vipsetting_id));
	}

	/**
	 * Delete vip group
	 *
	 * @param 	int
	 * @return	redirect
	 */
	public function deleteVIPGroup($vipsettingId) {
		$this->group_level->DeleteVIPGroup($vipsettingId);

		// $data = array(
		// 	'username' => $this->authentication->getUsername(),
		// 	'management' => 'VIP Setting Management',
		// 	'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
		// 	'action' => 'Delete vip group id:' . $vipsettingId,
		// 	'description' => "User " . $this->authentication->getUsername() . " delete vip group id: " . $vipsettingId,
		// 	'logDate' => date("Y-m-d H:i:s"),
		// 	'status' => 0,
		// );

		// $this->report_functions->recordAction($data);

		$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Delete vip group', "User " . $this->authentication->getUsername() . " delete vip group id: " . $vipsettingId);

		$message = lang('con.vsm10');
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		redirect('vipsetting_management/vipGroupSettingList');
	}

	/**
	 * Delete vip group
	 *
	 * @param 	int
	 * @return	redirect
	 */
	public function fakeDeleteVIPGroup($vipsettingId) {
		$this->group_level->fakeDeleteVIPGroup($vipsettingId);

		// $data = array(
		// 	'username' => $this->authentication->getUsername(),
		// 	'management' => 'VIP Setting Management',
		// 	'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
		// 	'action' => 'Delete vip group id:' . $vipsettingId,
		// 	'description' => "User " . $this->authentication->getUsername() . " delete vip group id: " . $vipsettingId,
		// 	'logDate' => date("Y-m-d H:i:s"),
		// 	'status' => 0,
		// );

		// $this->report_functions->recordAction($data);

		$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Delete vip group', "User " . $this->authentication->getUsername() . " delete vip group id: " . $vipsettingId);

		$message = lang('con.vsm10');
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		redirect('vipsetting_management/vipGroupSettingList');
	}

	/**
	 * Delete vip setting
	 *
	 * @param 	int
	 * @return	redirect
	 */
	public function deleteSelectedVip() {
		$vipgroup = $this->input->post('vipgroup');
		$today = date("Y-m-d H:i:s");

		if (!empty($vipgroup)) {
			$this->load->model(['group_level']);
			foreach ($vipgroup as $vipgroupId) {
				$this->group_level->deleteVIPGroup($vipgroupId);
			}

			$message = lang('con.vsm11');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message); //will set and send message to the user
			redirect('vipsetting_management/vipGroupSettingList');
		} else {
			$message = lang('con.vsm12');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			redirect('vipsetting_management/vipGroupSettingList');
		}
	}

	/**
	 * edit group level item
	 * the end user and add a player
	 *
	 * @return	redirect page
	 */
	public function editVipGroupLevelItemSetting() {
		if (!$this->permissions->checkPermissions('vip_group_setting')) {
			$this->error_access();
		} else {
			$this->form_validation->set_rules('vipLevelName', 'Level Name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('minDeposit', 'Min Deposit', 'trim|required|xss_clean|is_numeric');
			$this->form_validation->set_rules('maxDeposit', 'Max Deposit', 'trim|required|xss_clean|is_numeric');
			$this->form_validation->set_rules('dailyMaxWithdrawal', 'Max Daily Withdrawal', 'trim|required|xss_clean|is_numeric');
			$this->form_validation->set_rules('max_withdraw_per_transaction', 'Max Withdrawal Per Transaction', 'trim|required|xss_clean|is_numeric');
            $this->form_validation->set_rules('min_withdrawal_per_transaction', 'Min Withdrawal Per Transaction', 'trim|xss_clean|is_numeric');

			$this->form_validation->set_rules('upgradeAmount', 'Amount for Upgrade', 'trim|xss_clean|is_numeric');
			$this->form_validation->set_rules('downgradeAmount', 'Amount for Downgrade', 'trim|xss_clean|is_numeric');

			$this->form_validation->set_rules('bonusModeCashback', 'Cashback Bonus Mode', 'trim|xss_clean');
			$this->form_validation->set_rules('bonusModeDeposit', 'Deposit Bonus Mode', 'trim|xss_clean');
			$this->form_validation->set_rules('firstTimeDepositBonusOption', 'First Time Deposit Option', 'trim|xss_clean');
			$this->form_validation->set_rules('firstTimeDepositBonus', 'First Time Deposit Bonus', 'trim|xss_clean|is_numeric');
			$this->form_validation->set_rules('firstTimeDepositBonusUpTo', 'First Time Deposit Bonus Up To', 'trim|xss_clean|is_numeric');
			$this->form_validation->set_rules('firstTimeDepositWithdrawCondition', 'First Time Withdrawal Condition', 'trim|xss_clean|is_numeric');
			$this->form_validation->set_rules('succeedingDepositBonusOption', 'Succeeding Deposit Bonus Option', 'trim|xss_clean');
			$this->form_validation->set_rules('succeedingDepositBonus', 'Succeeding Deposit Bonus', 'trim|xss_clean|is_numeric');
			$this->form_validation->set_rules('succeedingDepositBonusUpTo', 'Succeeding Deposit Bonus Up To', 'trim|xss_clean|is_numeric');
			$this->form_validation->set_rules('succeedingDepositWithdrawCondition', 'Succeeding Deposit Withdraw Condition', 'trim|xss_clean|is_numeric');
			$this->form_validation->set_rules('cashbackBonusPercentage', 'Bonus Percentage', 'trim|xss_clean|is_numeric');
			$this->form_validation->set_rules('maxCashbackBonus', 'Max Bonus', 'trim|xss_clean|is_numeric');
			$this->form_validation->set_rules('maxDailyCashbackBonus', 'Daily Max Bonus', 'trim|xss_clean|is_numeric');
			$this->form_validation->set_rules('withdraw_times_limit', 'Max Withdraw Times', 'trim|required|xss_clean|is_numeric');
			$this->form_validation->set_rules('max_withdrawal_non_deposit_player', 'Max withdrawal amount for non-deposit player', 'trim|xss_clean|is_numeric');

			$this->form_validation->set_rules('betAmountConvertionRate', 'Bet Amount Convertion Rate', 'trim|xss_clean');
			$this->form_validation->set_rules('depositAmountConvertionRate', 'Deposit Amount Convertion Rate', 'trim|xss_clean');

			#OGP-1568
			#	$guaranteed_period_number = $this->input->post('guaranteed_period_number');
			#	$guaranteed_period_total_deposit = $this->input->post('guaranteed_period_total_deposit');

			// $this->form_validation->set_rules('guaranteed_period_number', 'Guaranteed Period Number', 'trim|required|xss_clean|is_numberic');
			// $this->form_validation->set_rules('guaranteed_period_total_deposit', 'Guaranteed Period Total Deposit', 'trim|required|xss_clean|is_numberic');

			$this->form_validation->set_rules('birthdayBonusAmount', 'Birthday Bonus Amount', 'trim|xss_clean|is_numeric');
			$vipsettingcashbackruleId = $this->input->post('vipsettingcashbackruleId');

			if ($this->form_validation->run() == false) {
				$message = validation_errors(); //lang('con.vsm13');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

				redirect('vipsetting_management/editVipGroupLevel/' . $vipsettingcashbackruleId);
			} else {

				$this->utils->debug_log('----editVipGroupLevelItemSetting--- post', $this->input->post());
				$this->load->model(['promorules']);

			$additional_message=null;
			$adminUserId=$this->authentication->getUserId();
			$success=$this->group_level->lockAndTrans(Utils::GLOBAL_LOCK_ACTION_VIP_CASHBACK, 0,
					function() use($adminUserId, $vipsettingcashbackruleId, &$additional_message){

				$birthday_bonus_amount_expiration_period = null;
				$success=true;
				// level upgrade setting
				$upgradeSetting = $this->input->post('upgradeSetting');
				$upgradeLevelId = null;
				if ($upgradeSetting) {
					if ($upgradeSetting == 1) {
						$upgradeLevelId = $this->input->post('upgradeOnly');
					} else if ($upgradeSetting == 2) {
						$upgradeLevelId = $this->input->post('upgradeDowngrade');
					}
				}
				#OGP-1568
				$downgradeSetting = $this->input->post('downgradeSetting');
				$downgradeLevelId = null;
				if ($downgradeSetting) {
					if ($downgradeSetting == 3) {
						$downgradeLevelId = $this->input->post('downgradeOnly');
					}
				}

				$schedData = array();

				// period setting
				$periodSched = $this->input->post('upgradeSched');
				if ($periodSched == 1) {
					$schedData = array('daily' => $this->input->post('daily'));
				} elseif ($periodSched == 2) {
					$schedData = array('weekly' => $this->input->post('weekly'));
				} elseif ($periodSched == 3) {
					$schedData = array('monthly' => $this->input->post('monthly'));
				} elseif ($periodSched == 4) {
					$schedData = array('yearly' => $this->input->post('yearly'));
				}

				$hourlyCheckUpgrade = $this->input->post('hourlyCheckUpgrade');
				if($hourlyCheckUpgrade == '1') {
					$schedData['hourly'] = true;
				}

				#OGP-1568
				$schedDownData = array();
				$periodDownSched = $this->input->post('downgradeSched');
				if ($periodDownSched == 1) {
					$schedDownData = array('daily' => $this->input->post('down_daily'));
				} elseif ($periodDownSched == 2) {
					$schedDownData = array('weekly' => $this->input->post('down_weekly'));
				} elseif ($periodDownSched == 3) {
					$schedDownData = array('monthly' => $this->input->post('down_monthly'));
				} elseif ($periodDownSched == 4) {
					$schedDownData = array('yearly' => $this->input->post('down_yearly'));
				}

				$vipLevelName = $this->input->post('vipLevelName');
				$minDeposit = $this->input->post('minDeposit');
				$maxDeposit = $this->input->post('maxDeposit');
				$dailyMaxWithdrawal = $this->input->post('dailyMaxWithdrawal');
				$withdraw_times_limit = $this->input->post('withdraw_times_limit');
				$max_withdrawal_per_transaction = $this->input->post('max_withdraw_per_transaction');
                $min_withdrawal_per_transaction = $this->input->post('min_withdrawal_per_transaction');
				$max_withdrawal_non_deposit_player = $this->input->post('max_withdrawal_non_deposit_player');
				$can_cashback = $this->input->post('can_cashback') ? $this->input->post('can_cashback') : 'false';
				$promo_cms_id = $this->input->post('promo_cms_id');
				$promo_rule_id = $this->promorules->getPromorulesIdByPromoCmsId($promo_cms_id);
				#OGP-1568
				$downgrade_promo_cms_id = $this->input->post('downgrade_promo_cms_id');
				$downgrade_promo_rule_id = $this->promorules->getPromorulesIdByPromoCmsId($downgrade_promo_cms_id);

				// $gameProviderId = $this->input->post('gameProviderId');
				$upgradeAmount = $this->input->post('upgradeAmount');
				$downgradeAmount = $this->input->post('downgradeAmount');
				$minimumMonthlyDeposit = $this->input->post('minimumMonthlyDeposit');
				$periodUpDown = $this->input->post('periodUpdown');

				$cashback_period = $this->input->post('cashback_period');

				$bonus_mode_cashback = $this->input->post('bonusModeCashback') ?: null;
				$bonus_mode_deposit = $this->input->post('bonusModeDeposit') ?: null;
				$birthday_mode_deposit = $this->input->post('bonusModeBirthday') ?: null;

				$bet_convert_rate = $this->input->post('betAmountConvertionRate') ?: null;
				$deposit_convert_rate = $this->input->post('depositAmountConvertionRate') ?: null;
				$winning_convert_rate = $this->input->post('winningAmountConvertionRate') ?: null;
				$losing_convert_rate = $this->input->post('losingAmountConvertionRate') ?: null;

				if ($bonus_mode_cashback == self::BONUS_MODE_ENABLE) {
					$bonus_mode_cashback = self::BONUS_MODE_ENABLE;
					$cashback_percentage = $this->input->post('cashbackBonusPercentage') ?: null;
					$cashback_maxbonus = $this->input->post('maxCashbackBonus') ?: null;
					$cashback_daily_maxbonus = $this->input->post('maxDailyCashbackBonus') ?: null;
				} else {
					$bonus_mode_cashback = self::BONUS_MODE_DISABLE;
					$cashback_percentage = null;
					$cashback_maxbonus = null;
					$cashback_daily_maxbonus = null;
				}

				if ($bonus_mode_deposit == self::BONUS_MODE_ENABLE) {
					$firsttime_dep_type = $this->input->post('firstTimeDepositBonusOption') ?: null;
					$firsttime_dep_bonus = $this->input->post('firstTimeDepositBonus') ?: null;
					$firsttime_dep_percentage_upto = $this->input->post('firstTimeDepositBonusUpTo') ?: null;
					$firsttime_dep_withdraw_condition = $this->input->post('firstTimeDepositWithdrawCondition');
					$succeeding_dep_type = $this->input->post('succeedingDepositBonusOption') ?: null;
					$succeeding_dep_bonus = $this->input->post('succeedingDepositBonus') ?: null;
					$succeeding_dep_percentage_upto = $this->input->post('succeedingDepositBonusUpTo') ?: null;
					$succeeding_dep_withdraw_condition = $this->input->post('succeedingDepositWithdrawCondition');
					$bonus_mode_deposit = self::BONUS_MODE_ENABLE;
				} else {
					$firsttime_dep_type = null;
					$firsttime_dep_bonus = null;
					$firsttime_dep_percentage_upto = null;
					$firsttime_dep_withdraw_condition = null;
					$succeeding_dep_type = null;
					$succeeding_dep_bonus = null;
					$succeeding_dep_percentage_upto = null;
					$succeeding_dep_withdraw_condition = null;
					$bonus_mode_deposit = self::BONUS_MODE_DISABLE;
				}

				if ($birthday_mode_deposit == self::BONUS_MODE_ENABLE) {
					$birthday_bonus_amount = $this->input->post('birthdayBonusAmount') ?: null;
					// $birthday_bonus_amount_expiration_period = $this->input->post('bonusExpirationPeriod') ?: null;
					$birthday_bonus_withdraw_condition = $this->input->post('birthdayBonusWithdrawCondition') ?: null;
				} else {
					$birthday_bonus_amount = null;
					$birthday_bonus_amount_expiration_period = null;
					$birthday_mode_deposit = self::BONUS_MODE_DISABLE;
					$birthday_bonus_withdraw_condition = null;
				}

				$one_withdraw_only = $this->input->post('one_withdraw_only') == 'true';
                $overwrite_minimum_withdraw_setting = $this->input->post('overwrite_minimum_withdraw_setting') ? $this->input->post('overwrite_minimum_withdraw_setting') : 'false';

				$this->load->model(array('group_level'));

				// $this->group_level->startTrans();

				// $showGameTree = $this->config->item('show_particular_game_in_tree');
				$this->load->model('game_description_model');

				$showGameTree = $this->config->item('show_particular_game_in_tree');
				$this->utils->debug_log('showGameTree', $showGameTree);

				if(!$this->utils->isEnabledFeature('enable_isolated_vip_game_tree_view')) {

					$enabled_edit_game_tree=$this->input->post('enabled_edit_game_tree')=='true';

					if($enabled_edit_game_tree){
						list($gamePlatformList, $gameTypeList, $gameDescList) = $this->processSubmitGameTreeWithNumber();
						// if (!empty($gamesAptList)) {
						$this->utils->debug_log('vipsettingcashbackruleId', $vipsettingcashbackruleId);
						if($gamePlatformList===null){
							//wrong submit
							$this->utils->error_log('wrong submit', $gamePlatformList, $gameTypeList, $gameDescList);
							$additional_message=lang('Saved cashback game tree failed, because submit wrong format');
							return false;
						}

						if(count($gamePlatformList)<=0 || count($gameTypeList)<=0){

							$this->utils->error_log('empty game tree on vip', $gamePlatformList, $gameTypeList);
							$additional_message=lang('Saved cashback game tree failed');
							return false;

						}else{
							$selected_game_tree = $this->input->post('selected_game_tree');
							$diffList=[];
							$rlt = $this->group_level->batchAddCashbackPercentage($vipsettingcashbackruleId,
								$gamePlatformList, $gameTypeList, $gameDescList, $adminUserId, $selected_game_tree, $diffList);
							$this->utils->debug_log('batchAddCashbackPercentage', $rlt, $diffList);
							if(!$rlt){
								$additional_message=lang('Saved cashback game tree failed');
								return $rlt;
							}
							$additional_message=lang('Saved cashback settings');
							//show diff list
							if(!empty($diffList)){
								if(!empty($diffList['deleted_game_platform'])){
									//search system code
									$this->load->model(['external_system']);
									$langList=$this->external_system->searchSystemCodeByList($diffList['deleted_game_platform']);
									sort($langList);
									$additional_message.=' | '.lang('Deleted').' '.implode(',', $langList);
								}
								if(!empty($diffList['deleted_game_type'])){
									//search game type lang
									$this->load->model(['game_type_model']);
									$langList=$this->game_type_model->searchGameTypeByList($diffList['deleted_game_type']);
									sort($langList);
									$additional_message.=' | '.lang('Deleted').' '.implode(',', $langList);
								}

								$this->utils->debug_log('additional_message for batchAddCashbackPercentage', $additional_message);
							}
						}
					}else{
						$this->utils->debug_log('ignore save game tree');
						$additional_message=lang('Cashback settings was not changed');
					}

				}

				// }


				$vipSettingId = $this->input->post('vipSettingId');
				$vipsettingcashbackbonuspergameId = $this->input->post('vipsettingcashbackbonuspergameId');
				$today = date("Y-m-d H:i:s");

				// $changedRule = $this->group_level->isChangedRule($vipsettingcashbackruleId, $cashback_percentage, $cashback_maxbonus);
				#OGP-1568
				// $guaranteed_period_number = $this->input->post('guaranteed_period_number');
				// $guaranteed_period_total_deposit = $this->input->post('guaranteed_period_total_deposit');
				$guaranteed_downgrade_period_number = $this->input->post('guaranteed_downgrade_period_number');
				$guaranteed_downgrade_period_total_deposit = $this->input->post('guaranteed_downgrade_period_total_deposit');

				//updates bonus rule
				$bonusRule = array(
					'vipLevelName' => $vipLevelName,
					'vipsettingcashbackruleId' => $vipsettingcashbackruleId,
					'minDeposit' => $minDeposit,
					'maxDeposit' => $maxDeposit,
					'dailyMaxWithdrawal' => $dailyMaxWithdrawal,
					'max_withdraw_per_transaction' => $max_withdrawal_per_transaction,
					'upgradeAmount' => $upgradeAmount,
					'minimumMonthlyDeposit' => $minimumMonthlyDeposit,
					'period_up_down' => $periodUpDown,
					'downgradeAmount' => $downgradeAmount,
					'bonus_mode_cashback' => $bonus_mode_cashback,
					'bonus_mode_deposit' => $bonus_mode_deposit,
					'firsttime_dep_type' => $firsttime_dep_type,
					'firsttime_dep_bonus' => $firsttime_dep_bonus,
					'firsttime_dep_percentage_upto' => $firsttime_dep_percentage_upto,
					'firsttime_dep_withdraw_condition' => $firsttime_dep_withdraw_condition,
					'succeeding_dep_type' => $succeeding_dep_type,
					'succeeding_dep_bonus' => $succeeding_dep_bonus,
					'succeeding_dep_percentage_upto' => $succeeding_dep_percentage_upto,
					'succeeding_dep_withdraw_condition' => $succeeding_dep_withdraw_condition,
					'cashback_percentage' => $cashback_percentage,
					'cashback_maxbonus' => $cashback_maxbonus,
					'cashback_daily_maxbonus' => $cashback_daily_maxbonus,
					'withdraw_times_limit' => $withdraw_times_limit,
					'bet_convert_rate' => $bet_convert_rate,
					'winning_convert_rate' => $winning_convert_rate,
					'losing_convert_rate' => $losing_convert_rate,
					'deposit_convert_rate' => $deposit_convert_rate,
					'one_withdraw_only' => $one_withdraw_only,
					'promo_cms_id' => $promo_cms_id,
					'promo_rule_id' => $promo_rule_id,
					'vip_upgrade_id' => $upgradeLevelId,
					'period_up_down_2' => json_encode($schedData),
					'vip_downgrade_id' => $downgradeLevelId,
					'period_down' => json_encode($schedDownData),
					'max_withdrawal_non_deposit_player' => $max_withdrawal_non_deposit_player,
					'downgrade_promo_cms_id' => $downgrade_promo_cms_id,
					'downgrade_promo_rule_id' => $downgrade_promo_rule_id,
					// 'guaranteed_period_number' => $guaranteed_period_number,
					// 'guaranteed_period_total_deposit' => $guaranteed_period_total_deposit,
					'guaranteed_downgrade_period_number' => $guaranteed_downgrade_period_number,
					'guaranteed_downgrade_period_total_deposit' => $guaranteed_downgrade_period_total_deposit,
					'bonus_mode_birthday' => $birthday_mode_deposit,
					'birthday_bonus_amount' => $birthday_bonus_amount,
					'birthday_bonus_expiration_datetime' => $birthday_bonus_amount_expiration_period,
					'birthday_bonus_withdraw_condition' => $birthday_bonus_withdraw_condition,
					'cashback_period' => $cashback_period,
					'can_cashback' => $can_cashback,
                    'min_withdrawal_per_transaction' => $min_withdrawal_per_transaction,
                    'overwrite_minimum_withdraw_setting' => $overwrite_minimum_withdraw_setting
				);

				$this->utils->debug_log('----editVipGroupLevelItemSetting---', $bonusRule);

				$this->group_level->editVipGroupBonusRule($bonusRule);

				$this->utils->debug_log('editVipGroupBonusRule');

				$this->group_level->updateAllVipGroupBonusPerGame($vipsettingcashbackruleId, $cashback_percentage, $cashback_maxbonus);

				$this->utils->debug_log('updateAllVipGroupBonusPerGame');
				//updates vip group setting
				$vipGroupSettingDetails = array(
					'updatedOn' => $today,
					'updatedBy' => $this->authentication->getUserId(),
				);

				$this->group_level->editVIPGroup($vipGroupSettingDetails, $vipSettingId);
				$this->utils->debug_log('editVIPGroup');

				// $this->group_level->endTrans();

				$this->utils->debug_log('commit trans after editVIPGroup');

				return $success;

			});

			// if ($this->group_level->isErrorInTrans()) {
			if(!$success){
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('save.failed').' '.$additional_message);
			} else {
				$message = lang('con.vsm15');
				$this->alertMessage(self::MESSAGE_TYPE_WARNING, $message.' '.$additional_message);
			}
			redirect('vipsetting_management/editVipGroupLevel/' . $vipsettingcashbackruleId);

			}
		}
	}

	public function setVipGameCashbackPercentage($ajax_request=true) {
		// if (!empty($gamesAptList)) {
		$vipsettingcashbackruleId = $this->input->post('vipsettingcashbackruleId');
		$this->utils->debug_log('vipsettingcashbackruleId', $vipsettingcashbackruleId);
		$additional_message=null;
		// $rlt = $this->group_level->batchAddCashbackAllowedGames($vipsettingcashbackruleId, $cashback_percentage, $cashback_maxbonus, $gamesAptList);
		$rlt=$this->group_level->lockAndTrans(Utils::GLOBAL_LOCK_ACTION_VIP_CASHBACK, 0,
				function() use($vipsettingcashbackruleId, &$additional_message){

			$adminUserId=$this->authentication->getUserId();
			$this->utils->debug_log('==========================enter setVipGameCashbackPercentage');
			list($gamePlatformList, $gameTypeList, $gameDescList) = $this->processSubmitGameTreeWithNumber();
			$this->utils->debug_log('================setVipGameCashbackPercentage gamePlatformList', $gamePlatformList);
			$this->utils->debug_log('================setVipGameCashbackPercentage gameTypeList', $gameTypeList);
			$this->utils->debug_log('================setVipGameCashbackPercentage gameDescList', $gameDescList);

			if($gamePlatformList===null){
				//wrong submit
				$this->utils->error_log('wrong submit', $gamePlatformList, $gameTypeList, $gameDescList);
				$additional_message=lang('Saved cashback game tree failed, because submit wrong format');
				return false;
			}

			if(count($gamePlatformList)<=0 || count($gameTypeList)<=0){

				$this->utils->error_log('empty game tree on vip', $gamePlatformList, $gameTypeList);
				$additional_message=lang('Saved cashback game tree failed');
				return false;
			}
			$selected_game_tree = $this->input->post('selected_game_tree');
			$diffList=[];
			$rlt = $this->group_level->batchAddCashbackPercentage($vipsettingcashbackruleId,
				$gamePlatformList, $gameTypeList, $gameDescList, $adminUserId, $selected_game_tree, $diffList);
			$this->utils->debug_log('batchAddCashbackPercentage', $rlt, $diffList);
			if(!$rlt){
				$additional_message=lang('Saved cashback game tree failed');
				return $rlt;
			}
			$additional_message=lang('Saved cashback settings');
			//show diff list
			if(!empty($diffList)){
				if(!empty($diffList['deleted_game_platform'])){
					//search system code
					$this->load->model(['external_system']);
					$langList=$this->external_system->searchSystemCodeByList($diffList['deleted_game_platform']);
					sort($langList);
					$additional_message.=' | '.lang('Deleted').' '.implode(',', $langList);
				}
				if(!empty($diffList['deleted_game_type'])){
					//search game type lang
					$this->load->model(['game_type_model']);
					$langList=$this->game_type_model->searchGameTypeByList($diffList['deleted_game_type']);
					sort($langList);
					$additional_message.=' | '.lang('Deleted').' '.implode(',', $langList);
				}

				$this->utils->debug_log('additional_message for batchAddCashbackPercentage', $additional_message);
			}
			return $rlt;
		});

		$this->utils->debug_log('batchAddCashbackPercentage', $rlt);
		if(!$ajax_request) {
			return $rlt;
		}

		if($rlt) {
			// echo json_encode(array("success"=> true, "message"=> "Already successfully updated selected games and their cashback percentage."));
			$result=["success"=> true, "message"=> lang('con.vsm15').' '.$additional_message];
			$this->returnJsonResult($result);
			// $message = "Already successfully updated selected games and their cashback percentage.";
			// $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		}else {
			// echo json_encode(array("success"=> false, "message"=> lang('save.failed')));
			$result=["success"=> false, "message"=> lang('save.failed').' '.$additional_message];
			$this->returnJsonResult($result);
			// $messsage = "Failed to update selected games and their cashback percentage.";
			// $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		}
	}

	/**
	 * search group level
	 *
	 *
	 * @return	redirect page
	 */
	public function searchVipGroupList($search = '') {
		$data['count_all'] = count($this->group_level->searchVipGroupList($search, null, null));
		$config['base_url'] = "javascript:get_vipgroupsetting_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 10;
		$config['num_links'] = 2;

		$config['first_tag_open'] = '<li>';
		$config['last_tag_open'] = '<li>';
		$config['next_tag_open'] = '<li>';
		$config['prev_tag_open'] = '<li>';
		$config['num_tag_open'] = '<li>';

		$config['first_tag_close'] = '</li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_close'] = '</li>';
		$config['num_tag_close'] = '</li>';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$this->pagination->initialize($config);

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['data'] = $this->group_level->searchVipGroupList($search, $config['per_page'], null);

		//export report permission checking
		if (!$this->permissions->checkPermissions('export_report')) {
			$data['export_report_permission'] = FALSE;
		} else {
			$data['export_report_permission'] = TRUE;
		}

		$this->load->view('player_management/vipsetting/ajax_view_vip_setting_list', $data);
	}

	/**
	 *
	 * pay all cashback
	 *
	 * @return redirect back cashback report
	 */
	public function pay_all_cashback() {
		if (!$this->permissions->checkPermissions('manually_pay_cashback')) {
			return $this->error_access();
		}

		$data['conditions'] = $this->safeLoadParams(array(
			'by_date_from' => $this->utils->getTodayForMysql(),
			'by_date_to' => $this->utils->getTodayForMysql(),
			'by_username' => '',
			'by_player_level' => '',
			'by_amount_greater_than' => '',
			'by_amount_less_than' => '',
			'by_paid_flag' => '',
			'enable_date' => 'false',
		));
		$this->load->model(['group_level']);
		$this->load->library(['lib_queue', 'language_function', 'authentication']);
		$callerType=Queue_result::CALLER_TYPE_ADMIN;
		$caller=$this->authentication->getUserId();
		$state=null;
		$lang=$this->language_function->getCurrentLanguage();
		//add pay time
		$cashBackSettings=$this->group_level->getCashbackSettings();
		$dateStr=$this->utils->getTodayForMysql().' ' . $cashBackSettings->payTimeHour . ':00';
		//run queue
		$token=$this->lib_queue->addRemotePayCashbackDaily($dateStr, $callerType, $caller, $state, $lang);

	    if (!empty($token)) {
	        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Create pay cashback job successfully'));
			return redirect('/system_management/common_queue/'.$token);
	    } else {
	        $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Create pay cashback job failed'));
			redirect('/report_management/cashback_report');
	    }

		// $this->load->model(['report_model', 'group_level']);
		// $this->load->library('data_tables');
		// //get report condition
		// $input = $data['conditions'];

		// if ($input['by_paid_flag'] == Group_level::DB_TRUE) {
		// 	//can't pay paid records
		// 	$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Cannot pay this condition, please change flag to Not pay'));
		// } else {
		// 	//search by conditions
		// 	//convert to selected_id_value
		// 	$selected_id_array = $this->group_level->searchUnpaidCashbackByCondition($input);

		// 	if (!empty($selected_id_array)) {

		// 		$success = false;
		// 		//lock action
		// 		$lockedKey = null;
		// 		$lock_it = $this->utils->lockResourceBy('', Utils::LOCK_ACTION_MANUALLY_PAY_CASHBACK, $lockedKey);
		// 		if ($lock_it) {
		// 			try {
		// 				$success = $this->group_level->manuallyPayCashbackByIdArr($selected_id_array);
		// 			} finally {
		// 				$rlt = $this->utils->releaseResourceBy('', Utils::LOCK_ACTION_MANUALLY_PAY_CASHBACK, $lockedKey);
		// 			}
		// 		} else {
		// 			$this->utils->debug_log('pay cashback failed, lock failed');
		// 		}

		// 		if ($success) {
		// 			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Pay cashback successfully'));
		// 		} else {
		// 			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Pay cashback failed'));
		// 		}
		// 	} else {
		// 		$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Do not find any records'));
		// 	}
		// }

		// //go back report
		// // $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Pay cashback successfully'));
		// redirect('/report_management/cashback_report?' . http_build_query($data['conditions']));

	}

	/**
	 *
	 * pay selected cashback
	 *
	 * @deprecated
	 *
	 * @return redirect back cashback report
	 */
	public function pay_selected() {
		// if (!$this->permissions->checkPermissions('manually_pay_cashback')) {
		// 	return $this->error_access();
		// }

		// $redirect_url = $this->input->post('redirect_url');

		// $selected_id_value = $this->input->post("selected_id_value");
		// $this->load->model(['group_level']);

		// $selected_id_array = !empty($selected_id_value) ? explode(',', $selected_id_value) : null;

		// if (!empty($selected_id_array)) {

		// 	$success = false;
		// 	//only lock action, because manuallyPayCashbackByIdArr still have another lock and trans
		// 	$lockedKey = null;
		// 	$lock_it = $this->utils->lockResourceBy('', Utils::LOCK_ACTION_MANUALLY_PAY_CASHBACK, $lockedKey);
		// 	if ($lock_it) {
		// 		try {
		// 			$success = $this->group_level->manuallyPayCashbackByIdArr($selected_id_array);
		// 		} finally {
		// 			$rlt = $this->utils->releaseResourceBy('', Utils::LOCK_ACTION_MANUALLY_PAY_CASHBACK, $lockedKey);
		// 		}
		// 	} else {
		// 		$this->utils->debug_log('pay cashback failed, lock failed');
		// 	}

		// 	if ($success) {
		// 		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Pay cashback successfully'));
		// 	} else {
		// 		$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Pay cashback failed'));

		// 	}

		// } else {
		// 	$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Do not find any records'));
		// }

		// redirect($redirect_url);
	}

	/**
	 *
	 * manually calc cashback
	 *
	 * @return redirect back cashback report
	 */
	public function calc_cashback() {
		if (!$this->permissions->checkPermissions('manually_calculate_cashback')) {
			return $this->error_access();
		}

		//only 4 conditions
		$conditions = $this->safeLoadParams(array(
			'by_date_from' => $this->utils->getTodayForMysql(),
			'by_date_to' => $this->utils->getTodayForMysql(),
			'by_username' => '',
			// 'by_player_level' => '',
			// 'by_amount_greater_than' => '',
			// 'by_amount_less_than' => '',
			// 'by_paid_flag' => '',
			// 'enable_date' => 'false',
		));
		$conditions['enable_date'] = 'true';

		$this->load->model(['report_model', 'group_level', 'player_model']);
		$this->load->library(['data_tables', 'player_cashback_library']);

		$start = $conditions['by_date_from'];
		$end = $conditions['by_date_to'];
		$playerId = null;
		if (empty($conditions['by_username'])) {
			$playerId = $this->player_model->getPlayerIdByUsername($conditions['by_username']);
		}
		$controller = $this;
		$lock_info = ['lock_type' => Utils::LOCK_ACTION_MANUALLY_PAY_CASHBACK, 'lock_id' => ''];
		//try call
		$has_success_calc = false;
		$success = $this->utils->loopDateStartEnd($start, $end, '+1 day', $lock_info,
			function ($from, $to) use ($controller, $playerId, $has_success_calc) {
				$date = $controller->utils->formatDateForMysql($from);
				$result_cnt = $controller->player_cashback_library->manuallyCalculateTotalCashback($date, $playerId);
				if($result_cnt > 0) {
					$has_success_calc = true;
				}
				return $has_success_calc;
			});

		//lock action
		// $lockedKey=null;
		// $lock_it = $this->utils->lockResourceBy('', Utils::LOCK_ACTION_MANUALLY_PAY_CASHBACK, $lockedKey);
		// if ($lock_it) {
		// 	try {
		// 		$playerId=$this->player_model->getPlayerIdByUsername($conditions['by_username']);
		// 		$success=$this->group_level->totalCashbackManually($conditions['by_date_from'],
		// 			$conditions['by_date_to'], $playerId);
		// 	} finally {
		// 		$rlt = $this->utils->releaseResourceBy('', Utils::LOCK_ACTION_MANUALLY_PAY_CASHBACK, $lockedKey);
		// 	}
		// }else{
		// 	$this->utils->debug_log('pay cashback failed, lock failed');
		// }

		if ($success) {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Calculate cashback successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Calculate cashback failed'));
		}

		redirect('/report_management/cashback_report?' . http_build_query($conditions));
	}

	/***
		 * Level Upgrade Setting
	*/

	public function saveUpgradeSetting() {

		// conjunction variable referring to "or" "and"

		$conjunction = $this->input->post('conjunction');
		$formula = $this->input->post('formula');
		$accumulation = $this->input->post('accumulation');
		$upgrade_id = $this->input->post('upgrade_id');

		$resultJson = array();

		$count = 0;
		foreach ($formula as $key => $val) {
			$operator = $this->getOperator($val[0]);

			if (sizeof($formula) > 1) {
				$count++;

				$conjunctionKeyOperator = 'operator_' . $count;

				if ($count >= 2) {
					$conjunctionKey = $count - 2;
					$resultJson[$conjunctionKeyOperator] = $conjunction[$conjunctionKey];
				}
				$resultJson[$key] = [$operator, $val[1]];
			} else {
				$resultJson[$key] = [$operator, $val[1]]; // if selected 1 option only
			}
		}

		$data['setting_name'] = $this->input->post('settingName');
		$data['description'] = $this->input->post('description');
		$data['status'] = self::UPGRADE_ACTIVE;
		$data['level_upgrade'] = $this->input->post('levelUpgrade');
		$data['formula'] = json_encode($resultJson);
		$data['accumulation'] = $accumulation;
		$data['created_at'] = $this->utils->getNowForMysql();
		if ($upgrade_id) {
			$data['upgrade_id'] = $upgrade_id;
		}
		$this->group_level->addUpgradeLevelSetting($data);
	}

	public function upgradeLevelSetting() {
		echo json_encode(array('aaData' => $this->group_level->upgradeLevelSetting()));
	}

	public function deleteUpgradeLevelSetting() {
		$this->group_level->deleteUpgradeLevelSetting($this->input->post('id'));
	}

	public function enableDisableSetting() {
		$this->group_level->enableDisableSetting($this->input->post('id'), $this->input->post('status'));
	}

	public function upDownTemplateList() {
		$data = $this->group_level->upDownTemplateList();
		// echo json_encode($data);
		$this->returnJsonResult($data);
	}

	public function getOperator($num) {
		$operator = '';
		if ($num == 1) {
			$operator = '>=';
		}
		if ($num == 2) {
			$operator = '<=';
		}
		if ($num == 3) {
			$operator = '>';
		}
		if ($num == 4) {
			$operator = '<';
		}
		return $operator;
	}

	public function uploadVipLevelbadge() {
		$set_default_badge = $this->input->post('set_default_badge');
		$vipLevelId = $this->input->post('vipLevelId');
		$badge = "vip-badge.png";
		$this->load->model('vipsetting');
		$image = isset($_FILES['vipbadge']) ? $_FILES['vipbadge'] : null;
		$path = $this->utils->getVipBadgePath();
		$config = array(
			'allowed_types' => "jpg|jpeg|png|gif",
			'max_size' => $this->utils->getMaxUploadSizeByte(),
			'overwrite' => true,
			'remove_spaces' => true,
			'upload_path' => $path,
		);
		if (!$set_default_badge) {
			if (!empty($image)) {
				$this->load->library('multiple_image_uploader');
				$response = $this->multiple_image_uploader->do_multiple_uploads($image, $path, $config);
				if ($response['status'] == "success") {
					$result = $this->vipsetting->updateVipLevelBadge($vipLevelId, $response['filename'][0]);
					if ($result == 1 || $result == 0) {
						$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Successfully uploaded.'));
					} else {
						$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('An error occurred and the upload failed.'));
					}
				} else {
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, $response['message']);
				}
			}
		} else {
			$result = $this->vipsetting->updateVipLevelBadge($vipLevelId, $badge);
			if ($result == 1 || $result == 0) {
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Successfully uploaded.'));
			} else {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('An error occurred and the upload failed.'));
			}
		}
		redirect('/vipsetting_management/editVipGroupLevel/' . $vipLevelId);
	}

	public function updateVipWelcomeText() {
		$main = $this->input->post('main');
		$sub = $this->input->post('sub');
		$b1 = $this->input->post('b1');
		$b2 = $this->input->post('b2');
		$b3 = $this->input->post('b3');
		$b4 = $this->input->post('b4');
		$this->load->model(array('group_level', 'operatorglobalsettings'));
		$operator_setting = $this->operatorglobalsettings->getOperatorGlobalSetting("vip_welcome_text");
		$data['operator_setting'] = json_decode($operator_setting[0]['value'], true);
		$val = json_encode(array(
			'main' => $main,
			'sub' => $sub,
			'b1' => $b1,
			'b2' => $b2,
			'b3' => $b3,
			'b4' => $b4,
		));
		$result = $this->operatorglobalsettings->putSetting("vip_welcome_text", $val, $field = 'value');
		echo $result;
		/*// print_r($data['operator_setting']);
			        foreach ($data['operator_setting'] as $key => $os) {

			        	$array[$key] = $os;
		*/
		// echo ($array);
		//var_dump($array);
		//var_dump($data['operator_setting']);
	}
}

/* End of file player_management.php */
/* Location: ./application/controllers/player_management.php */
