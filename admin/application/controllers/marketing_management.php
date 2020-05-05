<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 *
 * General behaviors include
 * * can add bonus to main wallet
 * * can add Manual add balance to main wallet
 * * can add Manual subtract balance to main wallet
 * * can add Cashback to main wallet
 * * List all the Game history records
 * * Set/update the Current Cashback Period
 * * Set/update the Friend referrals
 * * Can add Promo Categories
 * * Add/update/delete promos
 * * able to activate/deactivate the promos for public availability
 * * lists for transaction of promos used
 *
 * @category Marketing Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 *
 */

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/modules/player_promo_management_module.php';
require_once dirname(__FILE__) . '/modules/promorules_management_module.php';
require_once dirname(__FILE__) . '/modules/promo_module.php';
require_once dirname(__FILE__) . '/modules/cashback_module.php';
require_once dirname(__FILE__) . '/modules/promo_cms_module.php';
require_once dirname(__FILE__) . '/modules/shopping_center_module.php';
require_once dirname(__FILE__) . '/modules/marketing_bonus_game_module.php';

/**
 * Class Marketing_management
 *
 *
 * @property Player_cashback_library $player_cashback_library
 */
class Marketing_management extends BaseController {

	const MANAGEMENT_TITLE = 'Marketing Management';
	const BLOCK_GAME_PLATFORM = 1;
	const UNBLOCK_GAME_PLATFORM = 0;
	const BLOCKUNBLOCK_ALL_GAME_PLATFORM = 'all';

	// OGP-16730: Moved to model Registration_setting
	// const PASSWORD_MINIMUM_LENGTH = 6;
	// const PASSWORD_MAXIMUM_LENGTH = 20;

	const AGE_LIMIT = array(18 ,21 );

	use player_promo_management_module;
	use promorules_management_module;
	use promo_module;
	use cashback_module;
	use promo_cms_module;
	use marketing_bonus_game_module;
	use shopping_center_module;


	function __construct() {
		parent::__construct();
		$this->load->helper(array('date_helper', 'url'));
		$this->load->library(array('form_validation', 'authentication', 'utils', 'template', 'pagination', 'permissions', 'report_functions', 'player_manager', 'depositpromo_manager', 'marketing_manager', 'payment_manager'));
		$this->load->library('excel');
		$this->load->model(['player', 'cms_model']);
		$this->permissions->checkSettings();
		$this->permissions->setPermissions();
	}

	/**
	 * overview : template loading
	 *
	 * detail : load all javascript/css resources, customize head contents
	 *
	 * @param string $title
	 * @param string $description
	 * @param string $keywords
	 * @param string $activenav
	 */
	private function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->add_js('resources/js/chosen.jquery.min.js');
		$this->template->add_js('resources/js/marketing_management/marketing_management.js');
		$this->template->add_js('resources/js/jquery.numeric.min.js');

		$this->template->add_js('resources/js/cms_management/cms_management.js');
		# JS
		$this->template->add_js('resources/js/summernote.min.js');
		$this->template->add_js('resources/js/marketing_management/marketing_management.js');
		$this->template->add_css('resources/css/general/style.css');
		$this->template->add_css('resources/css/chosen.min.css');
		$this->template->add_css('resources/css/summernote.css');
		$this->loadThirdPartyToTemplate('datatables');

		$this->addJsTreeToTemplate();
		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('userId', $this->authentication->getUserId());
	}

	/**
	 * overview : error access
	 *
	 * detail : show error message if user can't access the page
	 */
	private function error_access($from = 'marketing') {
		$this->loadTemplate('Marketing Management', '', '', 'player');
		$marketingUrl = $this->utils->activeMarketingSidebar();
		$reportUrl = $this->utils->activeReportSidebar();
		$systemUrl = $this->utils->activeSystemSidebar();

		if($from == 'marketing'){
			$data['redirect'] = $marketingUrl;
		}

		elseif($from == 'system'){
			$data['redirect'] = $systemUrl;
		}

		else{
			$data['redirect'] = $reportUrl;
		}

		$message = lang('con.vsm01');
		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		$this->template->write_view('main_content', 'error_page', $data);
		$this->template->render();
	}

	/**
	 * overview : index page for player management
	 *
	 * detail : vipsetting_management/vipGroupSettingList
	 *
	 */
	public function index() {
		redirect('vipsetting_management/vipGroupSettingList');
	}

	/**
	 * overview : cashback payout setting
	 *
	 * detail : view page for cashback payout
	 */
	public function cashbackPayoutSetting() {

		if (!$this->permissions->checkPermissions('cashback_setting')) {
			$this->error_access();
		} else {

			$this->load->model(['operatorglobalsettings', 'group_level', 'cashback_settings']);
			$this->load->library(['player_cashback_library']);

			$cashBackSettings = $this->group_level->getCashbackSettings(); // $this->operatorglobalsettings->getSettingValue('cashback_settings');

			$data['cashBackSettings'] = $this->changeCbLastUpdateFormat($cashBackSettings);

			#Get and Store Cashback last update, for preventing lost during editCashbackPeriodSetting
			// $cbPayLastUpdate = $cashBackSettings->payLastUpdate;
			// $this->session->set_userdata('cbPayLastUpdate_', $cbPayLastUpdate);
			// $data['cashback'] = $this->group_level->getCashbackPayoutSetting();

			$data['common_cashback_rule'] = $this->cashback_settings->getCommonCashbackRule();

			$data['enabled_weekly_cashback'] = $this->utils->isEnabledFeature('enabled_weekly_cashback');

			$this->loadTemplate('Current Cashback Period Setting', '', '', 'marketing');
            $this->template->add_js('resources/js/marketing_management/common_cashback_rules.js?v=2');
			$this->template->write_view('sidebar', 'marketing_management/sidebar');
			$this->template->write_view('main_content', 'player_management/vipsetting/view_cashback_payoutsetting_rules', $data);
			$this->template->render();
		}
	}

	public function getCommonCashbackRuleByMultipleRangeRules(){
        if (!$this->permissions->checkPermissions('cashback_setting')) {
            return $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('con.vsm01'));
        }

        $this->load->library(['player_cashback_library']);
        return $this->returnCommon(BaseController::MESSAGE_TYPE_SUCCESS, NULL, $this->player_cashback_library->common_cashback_multiple_rules->getTemplateRulesWithTree());
    }

    public function saveCommonCashbackRuleByMultipleRangeSettings(){
        if (!$this->permissions->checkPermissions('cashback_setting')) {
            return $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('con.vsm01'));
        }

        $this->load->library(['player_cashback_library']);

        $tpl_id = $this->input->get_post('tpl_id');
        $type = $this->input->get_post('type');
        $type_map_id = $this->input->get_post('type_map_id');
        $enabled_cashback = !!$this->input->get_post('enabled_cashback');

        $result = $this->player_cashback_library->common_cashback_multiple_rules->saveSettings($tpl_id, $type, $type_map_id, $enabled_cashback);
        if($result){
            $status = BaseController::MESSAGE_TYPE_SUCCESS;
            $message = NULL;
        }else{
            $status = BaseController::MESSAGE_TYPE_ERROR;
            $message = lang('save.failed');
        }

        return $this->returnCommon($status, $message);
    }

    public function createCommonCashbackRuleByMultipleRangeRule(){
        if (!$this->permissions->checkPermissions('cashback_setting')) {
            return $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('con.vsm01'));
        }

        $this->load->library(['player_cashback_library']);

        $tpl_id = $this->input->get_post('tpl_id');
        $type = $this->input->get_post('type');
        $type_map_id = $this->input->get_post('type_map_id');
        $min_bet_amount = $this->input->get_post('min_bet_amount');
        $max_bet_amount = $this->input->get_post('max_bet_amount');
        $cashback_percentage = $this->input->get_post('cashback_percentage');
        $max_cashback_amount = $this->input->get_post('max_cashback_amount');

        $result = $this->player_cashback_library->common_cashback_multiple_rules->createRule($tpl_id, $type, $type_map_id, $min_bet_amount, $max_bet_amount, $cashback_percentage, $max_cashback_amount);
        if($result){
            $status = BaseController::MESSAGE_TYPE_SUCCESS;
            $message = NULL;
        }else{
            $status = BaseController::MESSAGE_TYPE_ERROR;
            $message = lang('save.failed');;
        }

        return $this->returnCommon($status, $message);
    }

    public function updateCommonCashbackRuleByMultipleRangeRule(){
        if (!$this->permissions->checkPermissions('cashback_setting')) {
            return $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('con.vsm01'));
        }

        $this->load->library(['player_cashback_library']);

        $rule_id = $this->input->get_post('rule_id');
        $min_bet_amount = $this->input->get_post('min_bet_amount');
        $max_bet_amount = $this->input->get_post('max_bet_amount');
        $cashback_percentage = $this->input->get_post('cashback_percentage');
        $max_cashback_amount = $this->input->get_post('max_cashback_amount');

        $result = $this->player_cashback_library->common_cashback_multiple_rules->updateRule($rule_id, $min_bet_amount, $max_bet_amount, $cashback_percentage, $max_cashback_amount);
        if($result){
            $status = BaseController::MESSAGE_TYPE_SUCCESS;
            $message = NULL;
        }else{
            $status = BaseController::MESSAGE_TYPE_ERROR;
            $message = lang('save.failed');
        }

        return $this->returnCommon($status, $message);
    }

    public function deleteCommonCashbackRuleByMultipleRangeRule(){
        if (!$this->permissions->checkPermissions('cashback_setting')) {
            return $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('con.vsm01'));
        }

        $this->load->library(['player_cashback_library']);

        $rule_id = $this->input->get_post('rule_id');

        $result = $this->player_cashback_library->common_cashback_multiple_rules->deleteRule($rule_id);
        if($result){
            $status = BaseController::MESSAGE_TYPE_SUCCESS;
            $message = NULL;
        }else{
            $status = BaseController::MESSAGE_TYPE_ERROR;
            $message = lang('save.failed');
        }

        return $this->returnCommon($status, $message);
    }

	/**
	 * overview : change update format
	 *
	 * @param $cashBackSettings
	 * @return string
	 */
	protected function changeCbLastUpdateFormat($cashBackSettings) {
		// $arr = json_decode($cashBackSettings);
		$arr=$cashBackSettings;
		$newArr = array(
            'common_cashback_rules_mode' => $arr->common_cashback_rules_mode,
            // 'daysAgo' => $arr->daysAgo,
			'fromHour' => $arr->fromHour,
			'toHour' => $arr->toHour,
			'payTimeHour' => $arr->payTimeHour,
			//'calcLastUpdate' => $arr->calcLastUpdate . '      &nbsp;&nbsp;(<i>' . $this->utils->getTimeAgo($arr->calcLastUpdate) . '</i>)',
			'payLastUpdate' => $arr->payLastUpdate . '      &nbsp;&nbsp;(<i>' . $this->utils->getTimeAgo($arr->payLastUpdate) . '</i>)',
			'withdraw_condition' => isset($arr->withdraw_condition) ? $arr->withdraw_condition : '',
			'min_cashback_amount' => isset($arr->min_cashback_amount) ? $arr->min_cashback_amount : '',
			'max_cashback_amount' => isset($arr->max_cashback_amount) ? $arr->max_cashback_amount : '',
			'no_cashback_bonus_for_non_deposit_player' => isset($arr->no_cashback_bonus_for_non_deposit_player) ? $arr->no_cashback_bonus_for_non_deposit_player : 0,
			'weekly' => isset($arr->weekly) ? $arr->weekly : '',
			'period' => isset($arr->period) ? $arr->period : ''
		);

		return json_encode($newArr);
	}

	const CASHBACK_SETTINGS_NAME = 'cashback_settings';

	/**
	 * overview : edit cashback period setting
	 *
	 * detail : view page for cashback payout setting
	 *
	 * @return	redered template
	 */
	public function editCashbackPeriodSetting() {
		if (!$this->permissions->checkPermissions('cashback_setting')) {
			$this->error_access();
		} else {

			$this->load->model(array('Operatorglobalsettings', 'cashback_settings'));

			$this->db->trans_start();

			// try daily, weekly, monthly
			$newCashbackSettings = array(
			    'common_cashback_rules_mode' => $this->input->post('common_cashback_rules_mode'),
				// 'daysAgo' => 1, //$this->input->post('daysAgo'),
				'fromHour' => $this->input->post('fromHour'),
				'toHour' => $this->input->post('toHour'),
				'payTimeHour' => $this->input->post('payTimeHour'),
				//'calcLastUpdate' => $this->session->userdata('cbCalcLastUpdate_'),
				// 'payLastUpdate' => $this->session->userdata('cbPayLastUpdate_'),
				'min_cashback_amount' => $this->input->post('min_cashback_amount'),
				'max_cashback_amount' => $this->input->post('max_cashback_amount'),
				'withdraw_condition' => $this->input->post('withdraw_condition'),
				'no_cashback_bonus_for_non_deposit_player' => $this->input->post('no_cashback_bonus_for_non_deposit_player'),
				'weekly' => $this->input->post('weekly'),
				'period' => $this->input->post('period')
			);

			# Update the Settings
			$this->Operatorglobalsettings->putSetting(self::CASHBACK_SETTINGS_NAME, json_encode($newCashbackSettings));
			# Get the latest Settings
			// $cashbackSettings = $this->Operatorglobalsettings->getSettingValue(self::CASHBACK_SETTINGS_NAME);
			$cashbackSettings = $this->group_level->getCashbackSettings();
			$today = date("Y-m-d H:i:s");
			$data = array(
				'username' => $this->authentication->getUsername(),
				'management' => 'CashBack Period Setting Management',
				'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
				'action' => 'Edit Cashback Period Setting',
				'description' => "User " . $this->authentication->getUsername() . " edit cashback payout setting",
				'logDate' => $today,
				'status' => '0',
			);

			$this->report_functions->recordAction($data);

			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				$arr = array(
					'status' => 'error',
					'msg' => 'Error Occured',
				);
			} else {
				// $commonCashbackGameRulesList = $this->cashback_settings->getCommonCashbackGameRule();
				// $this->utils->debug_log('commonCashbackGameRulesList >========================> ', $commonCashbackGameRulesList);
				$arr = array(
					'status' => 'success',
					'cashbackSettings' => $this->changeCbLastUpdateFormat($cashbackSettings),
					// 'commonCashbackGameRulesList' => json_encode($commonCashbackGameRulesList),
				);
				$this->returnJsonResult($arr);
				// echo json_encode($arr);
			}

		}
	}

	public function getCashbackGameRules() {
		$this->load->model(array('cashback_settings'));
		$commonCashbackGameRulesList = $this->cashback_settings->getCommonCashbackGameRule();
		// echo json_encode($commonCashbackGameRulesList);

		$this->returnJsonResult($commonCashbackGameRulesList);
	}

	/**
	 * overview : promo setting list
	 *
	 * detail : view page for CMS setting list
	 *
	 * @return  redered template
	 */
	public function promoSettingList() {
		if (!$this->permissions->checkPermissions('promocms')) {
			$this->error_access();
		} else {
			$this->load->model(array('promorules', 'promo_type'));
			$sort = "promoName";
			$data['promoList'] = $this->promorules->getPromoSettingList($sort, null, null);
			$data['promorules'] = $this->promorules->getUsablePromorules(true, true, $data['promoList']);
			$data['promoCategoryList'] = $this->promo_type->getPromoTypeAllowedToPromoManager();
			$data['systemLanguages'] = $this->language_function->getAllSystemLanguages();

			//export report permission checking
			if (!$this->permissions->checkPermissions('export_report')) {
				$data['export_report_permission'] = FALSE;
			} else {
				$data['export_report_permission'] = TRUE;
			}

			$this->loadTemplate('Marketing Management', '', '', 'marketing');
			$this->template->write_view('sidebar', 'marketing_management/sidebar');

            $this->template->add_css('resources/css/promo_cms_management/promo_cms_management.css');
            $this->template->write_view('main_content', 'cms_management/promotion/cms_promo_list', $data);

			$this->template->render();
		}
	}

	const STATUS_ACTIVE = 0;
	const STATUS_INACTIVE = 1;

    /**
     * overview : delete selected promo rule
     *
     * @return	redirect
     */
	public function deletePromorule($promoruleId){
        $this->load->model(['promo_games', 'promorules']);

        $data = array(
            'username' => $this->authentication->getUsername(),
            'management' => 'CMS Promo Setting Management',
            'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
            'action' => 'Delete promorule item id:' . $promoruleId,
            'description' => "User " . $this->authentication->getUsername() . " deleted promorule id: " . $promoruleId,
            'logDate' => $this->utils->getNowForMysql(),
            'status' => '0',
        );

        //check has promo manager by promorule id
        $promocmsItem = $this->promorules->getPromoCmsByPromoruleId($promoruleId);
        if (!empty($promocmsItem)) {
            $rlt = ['success' => false];
            $data['description'] .= ', delete failed, cause promorule still have promocmssseting.';
            $this->report_functions->recordAction($data);
            return $this->returnJsonResult($rlt);
        }

        $this->depositpromo_manager->deletePromoRules($promoruleId);

        // OGP-3381: also delete promorule-to-bonus game relationship items
        $this->promo_games->remove_promorule_game($promoruleId);

        $this->report_functions->recordAction($data);

        $rlt = ['success' => true];
        $message = lang('cms.deletePromoRuleSuccessMsg');
        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);

        return $this->returnJsonResult($rlt);
    }

	/**
	 * overview : delete selected promo rules
	 *
	 * @return	redirect
	 */
	public function deleteSelectedPromoRule() {
		$promorule = $this->input->post('promorule');
		$promorule = json_decode($promorule, true);
		$today = date("Y-m-d H:i:s");
        $rlt = ['success'=>true];
		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'Marketing Promo Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Delete Selected Deposit Promo',
			'description' => "User " . $this->authentication->getUsername() . " deleted selected promo rule.",
			'logDate' => $today,
			'status' => '0',
		);

		$this->report_functions->recordAction($data);
		if ($promorule != '') {
            $this->load->model(['promo_games', 'promorules']);
            $num_locked = 0;
            $num_success = 0;
            $num_promocms = 0;

            foreach ($promorule as $promoruleId) {
                $promocmsItem = $this->promorules->getPromoCmsByPromoruleId($promoruleId);
                if (!empty($promocmsItem)) {
                    ++$num_promocms;
                    continue;
                }

                if ($this->promorules->isLocked($promoruleId)) {
                    ++$num_locked;
                    continue;
                }
                $this->depositpromo_manager->deletePromoRules($promoruleId);
                //$this->depositpromo_manager->deleteDepositPromoItem($depositpromoId);

                // OGP-3381: also delete promorule-to-bonus game relationship items
                $this->promo_games->remove_promorule_game($promoruleId);
                ++$num_success;
            }

			if ($num_locked == count($promorule)) {
				// All locked
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('cms.deletePromoRule_all_fail'));
			} else if ($num_success == count($promorule)) {
				// All deleted
				$message = lang('cms.deletePromoRuleSuccessMsg');
				$this->alertMessage(1, $message); //will set and send message to the user
			} else {
				// Some deleted, Some have promocms
                $search = ['{num_locked}', '{num_promocms}'];
                $replace = [$num_locked, $num_promocms];
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, str_replace($search, $replace, lang('cms.deletePromoRule_some_fail')));
			}
		} else {
			$message = lang('cms.deletePromoRuleSuccessMsg2');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		}
		return $this->returnJsonResult($rlt);
	}

	/**
	 * overview : delete VIP group level
	 *
	 * @param 	int $promoruleId	promoruleId
	 * @return	redirect
	 */
	public function deletePromoRuleItem($promoruleId) {
		$this->depositpromo_manager->deletePromoRules($promoruleId);

		$today = date("Y-m-d H:i:s");
		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'Marketing Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Deleted Promo Rule item id: ' . $promoruleId,
			'description' => "User " . $this->authentication->getUsername() . " deleted promo rule.",
			'logDate' => $today,
			'status' => '0',
		);

		$this->report_functions->recordAction($data);

		$message = lang('cms.deletePromoRuleSuccessMsg');
		$this->alertMessage(1, $message);
		redirect('marketing_management/promoRuleManager', 'refresh');
	}

	/**
	 * overview : promo type manager
	 *
	 * @return	rendered template
	 */
	public function promoTypeManager() {
		$this->loadTemplate('Marketing Management', '', '', 'marketing');
		$this->template->write_view('sidebar', 'marketing_management/sidebar');

        if (!$this->permissions->checkPermissions('promo_category_setting')){
            $this->error_access();

            return;
        }

		if (!$this->permissions->checkPermissions('export_report')) {
			$data['export_report_permission'] = FALSE;
		} else {
			$data['export_report_permission'] = TRUE;
		}

		$this->load->model(array('promorules'));

		$data['promoType'] = $this->promorules->getPromoType();

		$this->template->write_view('main_content', 'marketing_management/promorules/promotype_manager', $data);
		$this->template->render();
	}

	/**
	 * overview : marketing settings
	 *
	 * @return	rendered template
	 */
	public function marketingSettings() {

		// -- OGP-10783
		show_404();

		if (!$this->permissions->checkPermissions('marketing_setting')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Marketing Management', '', '', 'marketing');
			$this->template->write_view('sidebar', 'marketing_management/sidebar');

			if (!$this->permissions->checkPermissions('export_report')) {
				$data['export_report_permission'] = FALSE;
			} else {
				$data['export_report_permission'] = TRUE;
			}

			$this->template->write_view('main_content', 'marketing_management/promorules/marketing_settings');
			$this->template->render();
		}
	}

	/**
	 * overview : promoCancellationSetup
	 *
	 * @return	rendered template
	 */
	public function promoCancellationSetup() {
		$this->loadTemplate('Promo Cancellation Setup', '', '', 'marketing');

		$data['cancelsetup'] = $this->depositpromo_manager->getPromoCancelSetup();
		$this->template->write_view('sidebar', 'marketing_management/sidebar');
		$this->template->write_view('main_content', 'marketing_management/promorules/promocancellation_setup', $data);
		$this->template->render();
	}

	/**
	 * overview : setup promo cancellation
	 *
	 *
	 * @return	redered template
	 */
	public function setupPromoCancellation() {
		$setupdata = array(
			'value' => $this->input->post('setup'),
			'name' => 'promo_cancellation_setting',
		);
		$this->depositpromo_manager->setupPromoCancellation($setupdata);
		$this->saveAction(self::MANAGEMENT_TITLE, 'Setup Promo Cancellation', "User " . $this->authentication->getUsername() . " has successfully setup promo cancellaton setting.");

		$message = lang('cms.cancelSetupMsg');
		$this->alertMessage(1, $message);
		redirect('marketing_management/promoCancellationSetup');
	}

	/**
	 * overview : add promo type
	 *
	 * @return	rendered template
	 */
	public function addPromoType() {
		$this->load->model('promo_type');

		$icon_file_name = null;
		if (!empty($_FILES['filPromoCatIcon'])) {
			# upload bank icon
			$upload_response = $this->promo_type->uploadPromoCategoryIcon($_FILES['filPromoCatIcon']);

			if ($upload_response['status'] == 'success' && isset($upload_response['fileName'])) {
				if (!empty($upload_response['fileName'])) {
					$icon_file_name = $upload_response['fileName'];
				}
			}
		}
        $promoTypeOrder = $this->input->post("promoTypeOrderId");
        $nextOrder = $this->promo_type->getNextOrder();
		if(!isset($promoTypeOrder) || empty($promoTypeOrder)){
            $promoTypeOrder = $nextOrder;
        }

		$promotypedata = array(
			'promoTypeName' => $this->input->post("promoTypeName"),
			'promoTypeOrder' => $promoTypeOrder,
			'promoTypeDesc' => $this->input->post("promoTypeDesc"),
			'isUseToPromoManager' => $this->input->post("useToPromoManager"),
			'promoIcon' => $icon_file_name,
			'createdBy' => $this->authentication->getUserId(),
			'createdOn' => $this->utils->getNowForMysql(),
			'status' => 0,
		);

		$res = $this->promo_type->addPromoType($promotypedata);

		if ($res) {
			$this->saveAction(self::MANAGEMENT_TITLE, 'Added Promo Type', "User " . $this->authentication->getUsername() . " has successfully added promo type.");
			redirect('marketing_management/promoTypeManager');
		}
	}

	/**
	 * overview : get promo type details
	 *
	 * @param int promoTypeId	promo_type_id
	 * @return	array
	 */
	public function getPromoTypeDetails($promoTypeId) {
		$result = $this->depositpromo_manager->getPromoTypeDetails($promoTypeId);
		$result[0]['icon_path'] = $this->utils->getPromoCategoryIcon($result[0]['promoIcon']);
		$this->returnJsonResult($result);
	}

	/**
	 * overview : view promo rule details
	 *
	 * @param int $promoRuleId	promo_rule_id
	 * @return	array
	 */
	public function viewPromoRuleDetails($promoRuleId) {
		$this->load->model(array('promorules'));
		$this->returnJsonResult($this->promorules->viewPromoRuleDetails($promoRuleId));
	}

	/**
	 * overview : get promo rule by promo_rule_id
	 *
	 * @param int $promoRuleId	promo_rule_id
	 * @return	array
	 */
	public function getPromoruleByPromoCms($promoRuleId) {
		$this->load->model(array('promorules'));
		$this->returnJsonResult($this->promorules->viewPromoRuleDetails($promoRuleId));
	}

	/**
	 * overview : modify promo type
	 *
	 * @return	redered template
	 */
	public function editPromoType() {
		$promotypedata = array(
			'promoTypeId' => $this->input->post("promoTypeId"),
            'promoTypeOrder' => $this->input->post("editpromoTypeOrderId"),
			'promoTypeName' => $this->input->post("editpromoTypeName"),
			'promoTypeDesc' => $this->input->post("editpromoTypeDesc"),
			'isUseToPromoManager' => $this->input->post("editUseToPromoManager"),
			'updatedBy' => $this->authentication->getUserId(),
			'updatedOn' => $this->utils->getNowForMysql(),
			'status' => 0,
		);

		if (!empty($_FILES['filEditPromoCatIcon'])) {
			# upload bank icon
            $this->load->model(['promo_type']);
			$upload_response = $this->promo_type->uploadPromoCategoryIcon($_FILES['filEditPromoCatIcon']);

			if ($upload_response['status'] == 'success' && isset($upload_response['fileName'])) {
				if (!empty($upload_response['fileName'])) {
					$promotypedata['promoIcon'] = $upload_response['fileName'];
				}
			}
		}

		$this->depositpromo_manager->editPromoType($promotypedata);
		$this->saveAction(self::MANAGEMENT_TITLE, 'Edit Promo Type', "User " . $this->authentication->getUsername() . " has successfully edited promo type.");
		redirect('marketing_management/promoTypeManager');
	}

	public function removePromoCatIcon(){
	    $result = ['status' => false, 'message' => null];

	    $promoTypeId = $this->input->post('promoTypeId');

	    if(empty($promoTypeId)){
	        $result['message'] = lang('Empty') . ' ' . lang('sys.pay.systemid');
            return $this->returnJsonResult($result);
        }

	    $this->load->model(['promo_type']);
        $result = $this->promo_type->removePromoCategoryIcon($promoTypeId);

        return $this->returnJsonResult($result);
    }

	/**
	 * overview : delete promo type
	 *
	 * @param int $id	promo_id
	 * @return	array
	 */
	public function deletePromoType($id) {
		if ($this->depositpromo_manager->deletePromoType($id)) {
			$message = lang('cms.promoTypeDeleteMsg');
			$this->alertMessage(1, $message);
			$message = lang('con.plm48');
			$this->saveAction(self::MANAGEMENT_TITLE, 'Delete Promo Type', "User " . $this->authentication->getUsername() . " has successfully deleted promo type.");
			redirect('marketing_management/promoTypeManager');
		}
	}

	/**
	 * overview : fake delete of promo type
	 *
	 * @param int $id -  ranking_id
	 * @return Bool - TRUE or FALSE
	 */
	public function fakeDeletePromoType($id) {
		$this->load->model('promorules');
		$promoRules = $this->promorules->getPromoRulesList();

		# check if category has existing promo rule
		if (in_array($id, array_column($promoRules, 'promoCategory'))) {
			$message = lang("Can't delete!! This categories have an existing promo rules:");
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			redirect('marketing_management/promoTypeManager');
		}

		if ($this->depositpromo_manager->fakeDeletePromoType($id)) {
			$message = lang('cms.promoTypeDeleteMsg');
			$this->alertMessage(1, $message);
			$message = lang('con.plm48');
			$this->saveAction(self::MANAGEMENT_TITLE, 'Delete Promo Type', "User " . $this->authentication->getUsername() . " has successfully deleted promo type.");
			redirect('marketing_management/promoTypeManager');
		}
	}

	/**
	 * overview : delete selected promo type
	 *
	 * @return	redirect
	 */
	public function deleteSelectedPromoType() {
		$this->load->model('promorules');
		$promoType = $this->input->post('promoType');
		$checkPromoCategoryIsExist = $this->promorules->getPromoRulesList();
		$promoTypes = $this->promorules->getPromoType();

		$data = [];
		foreach ($checkPromoCategoryIsExist as $key) {
			for ($i = 0; $i < count($promoType); $i++) {
				foreach ($promoTypes as $promoCategory) {
					if ($promoCategory['promotypeId'] == $promoType[$i]) {
						($promoType[$i] == $key['promoCategory']) ? $data[] = lang($promoCategory['promoTypeName']) : null;
					}
				}
			}
		}

		$message = lang("Can't delete!! This categories have an existing promo rules:");
		if ($data) {
			$unique = array_unique($data);
			$promoNameExist = implode(", ", $unique);
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message . "" . $promoNameExist);
			redirect('marketing_management/promoTypeManager');
		}

		$today = date('Y-m-d H:i:s');
		$promoTypes = '';
		if ($promoType != '') {
			foreach ($promoType as $promoTypeId) {
			    $this->load->model(['promo_type']);
                $this->promo_type->removePromoCategoryIcon($promoTypeId);
				$this->depositpromo_manager->deletePromoType($promoTypeId);
			}

			$message = lang('cms.promoTypeSelectedDeleteMsg');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			$this->saveAction(self::MANAGEMENT_TITLE, 'Delete Promo Type', 'User ' . $this->authentication->getUsername() . ' has successfully deleted promo type.');
			redirect('marketing_management/promoTypeManager');
		} else {
			$message = lang('cms.promoTypeNoSelectedDeleteMsg');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			redirect('marketing_management/promoTypeManager');
		}
	}

	/**
	 * overview : checker setting for duplicate player account
	 *
	 * @return	redered template
	 */
	public function duplicatePlayerAccountCheckerSetting() {
		$this->loadTemplate('Marketing Management', '', '', 'marketing');
		$this->template->write_view('sidebar', 'marketing_management/sidebar');

		if (!$this->permissions->checkPermissions('export_report')) {
			$data['export_report_permission'] = FALSE;
		} else {
			$data['export_report_permission'] = TRUE;
		}

		$data['promoType'] = $this->depositpromo_manager->getPromoType();

		$this->template->write_view('main_content', 'marketing_management/duplicateaccount/view_duplicateaccountsetting', $data);
		$this->template->render();
	}

	/**
	 * overview : active promo rules
	 *
	 * @param 	int promoruleId		promorulesId
	 * @param 	int status			statis
	 * @return	redirect
	 */
	public function activatePromoRule($promoruleId, $status) {
		$data['promorulesId'] = $promoruleId;
		$data['status'] = $status;
		$data['updatedOn'] = date("Y-m-d H:i:s");
		$data['updatedBy'] = $this->authentication->getUserId();

		$this->depositpromo_manager->activatePromoRule($data);

		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'Marketing Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Update status of deposit promo id: ' . $promoruleId . 'to status:' . $status,
			'description' => "User " . $this->authentication->getUsername() . " edit deposit promo status to " . $status,
			'logDate' => date("Y-m-d H:i:s"),
			'status' => '0',
		);

		$this->report_functions->recordAction($data);

		$message = lang('cms.updatePromoStatusMsg');
		$this->alertMessage(1, $message);

		redirect('marketing_management/promoRuleManager');
	}

	/**
	 * overview : Enable edit promo rules
	 *
	 * @param 	int promoruleId		promorulesId
	 * @param 	int enable_edit		enable_edit
	 * @return	redirect
	 */
	public function enableEditPromoRule($promoruleId, $enable_edit) {
		if (!$this->permissions->checkPermissions('allow_to_enable_edit_promo_rules')) {
			$this->error_access();
		} else {
			$data['promorulesId'] = $promoruleId;
			$data['enable_edit'] = $enable_edit;
			$data['updatedOn'] = date("Y-m-d H:i:s");
			$data['updatedBy'] = $this->authentication->getUserId();

			$this->depositpromo_manager->activatePromoRule($data);

			$data = array(
				'username' => $this->authentication->getUsername(),
				'management' => 'Marketing Management',
				'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
				'action' => 'Update enable edit of deposit promo id: ' . $promoruleId . 'to :' . $enable_edit,
				'description' => "User " . $this->authentication->getUsername() . " edit deposit promo enable edit to " . $enable_edit,
				'logDate' => date("Y-m-d H:i:s"),
				'status' => '0',
			);

			$this->report_functions->recordAction($data);

			$message = lang('cms.updatePromoStatusMsg');
			$this->alertMessage(1, $message);

			redirect('marketing_management/promoRuleManager');
		}
	}

	/**
	 * overview : view registration settings
	 *
	 * @param	$type
	 * @return	rendered Template
	 */
	public function viewRegistrationSettings($type = '1', $offLiveChat = false, $from = 'system') {
		if (!$this->permissions->checkPermissions('registration_setting')) {
			$this->error_access($from);
		} else {

			$this->load->model('registration_setting');
			$this->loadTemplate('System Management', '', '', 'system');
			$this->template->add_js('resources/js/player_management/player_management.js');
			$data['type'] = $type;
            $data['offChat'] = $offLiveChat;
			$this->load->model('operatorglobalsettings');
			$data['captcha_setting'] = $this->operatorglobalsettings->getSettingIntValue('captcha_registration');

			$this->template->write_view('sidebar', 'system_management/sidebar');
			$this->template->write_view('main_content', 'marketing_management/registration_setting/view_registration_settings', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : switch captcha
	 *
	 * @param $switch
	 */
	public function switchCaptcha($module, $switch) {
		$this->operatorglobalsettings->syncSettingJson("{$module}_captcha_enabled", $switch, 'value');
		$switch = $switch ? 'ON' : 'OFF';
		$this->saveAction('System Management', 'Set captcha setting to ' . $switch, 'User ' . $this->authentication->getUsername());
		$this->alertMessage(1, lang('sys.captchaMessage') . ' ' . $switch);
		if ($module == 'login') {
			$this->session->set_userdata('viewPlayerRegistrationSettings', 'login');
		}
		redirect("marketing_management/viewRegistrationSettings#{$module}");
	}

	/**
	 * overview : change registration settings
	 *
	 * @param $type
	 * @return	rendered Template
	 */
	public function changeRegistrationSettings($type) {
		$data['registration_fields'] = $this->marketing_manager->getRegisteredFields($type);
		$data['type'] = $type;
		$this->load->model('operatorglobalsettings');
		$passwordRecoverySetting = $this->operatorglobalsettings->getSettingIntValue('password_recovery_options');
		$data['password_recovery_option_1'] = $passwordRecoverySetting & 1;
		$data['password_recovery_option_2'] = $passwordRecoverySetting & 2;
		$data['password_recovery_option_3'] = $passwordRecoverySetting & 4;

		if ($type == '1') {
            $data['registration_captcha_enabled']     = $this->operatorglobalsettings->getSettingJson('registration_captcha_enabled');
            $data['login_captcha_enabled']            = $this->operatorglobalsettings->getSettingJson('login_captcha_enabled');
            $data['login_after_registration_enabled'] = $this->operatorglobalsettings->getSettingJson('login_after_registration_enabled');
            $data['remember_password_enabled']        = $this->operatorglobalsettings->getSettingJson('remember_password_enabled');
            $data['forget_password_enabled']          = $this->operatorglobalsettings->getSettingJson('forget_password_enabled');
			$data['restrict_password_enabled'] = $this->utils->isRestrictPasswordEnabled();
			$data['restrict_username_enabled'] = $this->utils->isRestrictUsernameEnabled();

			if($this->utils->isEnabledFeature('enable_pep_gbg_api_authentication') && $this->utils->isEnabledFeature('show_pep_authentication')) {
				$data['generate_pep_gbg_auth_after_registration_enabled'] = $this->operatorglobalsettings->getSettingJson('generate_pep_gbg_auth_after_registration_enabled');
			}

			if($this->utils->isEnabledFeature('enable_c6_acuris_api_authentication') && $this->utils->isEnabledFeature('show_c6_authentication')) {
				$data['generate_c6_acuris_auth_after_registration_enabled'] = $this->operatorglobalsettings->getSettingJson('generate_c6_acuris_auth_after_registration_enabled');
			}

			//check password minimum and maximum length
			$data['password_min_max'] = $this->utils->isPasswordMinMaxEnabled();
			$data['min_password'] = !empty($data['password_min_max']['min']) ? $data['password_min_max']['min'] : Registration_setting::PASSWORD_MINIMUM_LENGTH;
			$data['max_password'] = !empty($data['password_min_max']['max']) ? $data['password_min_max']['max'] : Registration_setting::PASSWORD_MAXIMUM_LENGTH;
			$data['password_min_max_enabled'] = (array_key_exists('password_min_max_enabled', $data['password_min_max']) ? $data['password_min_max']['password_min_max_enabled'] : false);

            $data['multi_rows_address_columns'] = ['city','address','address2'];
            $data['full_address_in_one_row'] = $this->operatorglobalsettings->getSettingJson('full_address_in_one_row');


			// player login failed attempt settings
            $data['player_login_failed_attempt_blocked'] = $this->operatorglobalsettings->getSettingBooleanValue('player_login_failed_attempt_blocked');
            $data['player_login_failed_attempt_times'] = $this->operatorglobalsettings->getSettingIntValue('player_login_failed_attempt_times');
            $data['player_login_failed_attempt_reset_timeout'] = $this->operatorglobalsettings->getSettingIntValue('player_login_failed_attempt_reset_timeout');

			$data['subtype'] = $this->session->userdata('viewPlayerRegistrationSettings') ?: 'registration';
			$this->session->unset_userdata('viewPlayerRegistrationSettings');
			$this->load->view('marketing_management/registration_setting/ajax_player_registration_settings', $data);
		} else if ($type == '2') {
			$this->load->view('marketing_management/registration_setting/ajax_affiliate_registration_settings', $data);
		}
	}

	/**
	 * overview : save registration settings
	 *
	 * @param $type
	 * @return	rendered Template
	 */
	public function saveRegistrationSettings($type) {
		if (!$this->permissions->checkPermissions('registration_setting')) {
			$this->error_access();
		} else {
            $full_address_in_one_row = $this->input->post('full_address_in_one_row');
            $multi_rows_address_columns = ['city','address','address2'];

			$registration_fields = $this->marketing_manager->getRegisteredFields($type);
			foreach ($registration_fields as $registration_field) {
				$data = array(
					'visible' => ($this->input->post($registration_field['registrationFieldId'] . "_visible") == 'on') ? '0' : '1',
					'required' => ($this->input->post($registration_field['registrationFieldId'] . "_required") == 'on') ? '0' : '1',
					'updatedOn' => date("Y-m-d H:i:s"),
				);

                if(in_array($registration_field['alias'], $multi_rows_address_columns) && $full_address_in_one_row){
                    $data['visible'] = '1'; # 1 = invisible
                    $data['required'] = '1'; # 1 = unrequired
                }
				$this->marketing_manager->saveRegistrationSettings($data, $registration_field['registrationFieldId']);
			}

			$data = array(
				'username' => $this->authentication->getUsername(),
				'management' => 'Marketing Management',
				'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
				'action' => 'Edit Registration Settings',
				'description' => "User " . $this->authentication->getUsername() . " edited " . $type . " registration setting",
				'logDate' => date("Y-m-d H:i:s"),
				'status' => '0',
			);
			$this->report_functions->recordAction($data);

			$this->operatorglobalsettings->syncSettingJson('login_after_registration_enabled', $this->input->post('login_after_registration_enabled'));
			$this->operatorglobalsettings->syncSettingJson('restrict_username_enabled', $this->input->post('restrict_username'));
            $this->operatorglobalsettings->syncSettingJson('set_password_min_max','');
            if(!empty($this->input->post('set_min_max_password'))) {
                $password_data = array(
                    "min" => $this->input->post('min_password'),
                    "max" => $this->input->post('max_password'),
                );
                $this->operatorglobalsettings->syncSettingJson('set_password_min_max', $password_data);
            }
            $this->operatorglobalsettings->syncSettingJson('full_address_in_one_row', $full_address_in_one_row);


			if($this->utils->isEnabledFeature('enable_pep_gbg_api_authentication') && $this->utils->isEnabledFeature('show_pep_authentication')) {
				$this->operatorglobalsettings->syncSettingJson('generate_pep_gbg_auth_after_registration_enabled', $this->input->post('generate_pep_gbg_auth_after_registration_enabled'));
			}

			if($this->utils->isEnabledFeature('enable_c6_acuris_api_authentication') && $this->utils->isEnabledFeature('show_c6_authentication')) {
				$this->operatorglobalsettings->syncSettingJson('generate_c6_acuris_auth_after_registration_enabled', $this->input->post('generate_c6_acuris_auth_after_registration_enabled'));
			}
			$message = lang('con.m03');
			$this->alertMessage(1, $message);

			redirect('marketing_management/viewRegistrationSettings/' . $type);
		}
	}

    public function setRegistrationSettingsToDefaultOrder() {
        # patch data for OGP-15507
        $data = [];
        $data['field_name'] = 'City';
        $data['alias'] = 'city';
        $this->marketing_manager->saveRegistrationSettings($data, 36);
        $data['field_name'] = 'Region';
        $data['alias'] = 'region';
        $this->marketing_manager->saveRegistrationSettings($data, 37);
        $data['field_name'] = 'Address';
        $data['alias'] = 'address';
        $this->marketing_manager->saveRegistrationSettings($data, 43);
        $data['field_name'] = 'Address2';
        $data['alias'] = 'address2';
        $this->marketing_manager->saveRegistrationSettings($data, 44);

        $data = [];
        # originally does't have alias, add alias back
        $data['alias'] = 'sms_verification_code';
        $this->marketing_manager->saveRegistrationSettings($data, 34);
        $data['alias'] = 'terms';
        $this->marketing_manager->saveRegistrationSettings($data, 31);

        # patch data for OGP-16158
        $order_list = $this->utils->getConfig('registration_fields_default_order');
        $registration_fields = $this->marketing_manager->getRegisteredFields(1);
        foreach ($order_list as $key => $alias) {
            $data = [];
            foreach ($registration_fields as $registration_field) {
                if($alias == $registration_field['alias']){
                    $data['field_order'] = $key;
                    $this->marketing_manager->saveRegistrationSettings($data, $registration_field['registrationFieldId']);
                }
            }
        }

        redirect('marketing_management/viewRegistrationSettings/');
    }


	public function saveAccountSettings() {
        $full_address_in_one_row = $this->input->post('full_address_in_one_row');
        $multi_rows_address_columns = ['city','address','address2'];

        $registration_fields = $this->marketing_manager->getRegisteredFields(1);
		foreach ($registration_fields as $registration_field) {
			$data = array(
				'account_visible'  => ($this->input->post($registration_field['registrationFieldId'] . "_visible")  == 'on') ? '0' : '1',
				'account_required' => ($this->input->post($registration_field['registrationFieldId'] . "_required") == 'on') ? '0' : '1',
				'account_edit'     => ($this->input->post($registration_field['registrationFieldId'] . "_edit")     == 'on') ? '0' : '1',
				'updatedOn' => date("Y-m-d H:i:s"),
			);

            if(in_array($registration_field['alias'], $multi_rows_address_columns) && $full_address_in_one_row){
                $data['account_visible'] = '1'; # 1 = invisible
                $data['account_required'] = '1'; # 1 = unrequired
                $data['account_edit'] = '1';
            }
			$this->marketing_manager->saveRegistrationSettings($data, $registration_field['registrationFieldId']);
		}

        $this->operatorglobalsettings->syncSettingJson('full_address_in_one_row', $full_address_in_one_row);
		$this->report_functions->recordAction(array(
			'username' => $this->authentication->getUsername(),
			'management' => 'Marketing Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Edit Account Settings',
			'description' => "User " . $this->authentication->getUsername() . " edited player account setting",
			'logDate' => date("Y-m-d H:i:s"),
			'status' => '0',
		));

		$this->session->set_userdata('viewPlayerRegistrationSettings', 'account');

		$message = lang('con.m03');
		$this->alertMessage(1, $message);

		redirect('marketing_management/viewRegistrationSettings/1#account');
	}

	public function saveLoginSettings() {
		$this->operatorglobalsettings->syncSettingJson('remember_password_enabled', $this->input->post('remember_password_enabled'));
		$this->operatorglobalsettings->syncSettingJson('forget_password_enabled', $this->input->post('forget_password_enabled'));

        // save password recovery options
        $passwordRecoverySetting =
            $this->input->post('password_recovery_option_1') * 1 +
            $this->input->post('password_recovery_option_2') * 2 +
            $this->input->post('password_recovery_option_3') * 4;
        $this->operatorglobalsettings->putSetting('password_recovery_options', (int)$passwordRecoverySetting);

        // player login failed attempt settings
        $this->operatorglobalsettings->putSetting('player_login_failed_attempt_blocked', !!$this->input->post('player_login_failed_attempt_blocked'));
        $this->operatorglobalsettings->putSetting('player_login_failed_attempt_times', (int)$this->input->post('player_login_failed_attempt_times'));
        $this->operatorglobalsettings->putSetting('player_login_failed_attempt_reset_timeout', (int)$this->input->post('player_login_failed_attempt_reset_timeout'));

		$this->report_functions->recordAction(array(
			'username' => $this->authentication->getUsername(),
			'management' => 'System Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Edit Player Login Settings',
			'description' => "User " . $this->authentication->getUsername() . " edited player login setting",
			'logDate' => date("Y-m-d H:i:s"),
			'status' => '0',
		));

		$this->session->set_userdata('viewPlayerRegistrationSettings', 'login');

		$message = lang('con.m03');
		$this->alertMessage(1, $message);

		redirect('marketing_management/viewRegistrationSettings/1#login');
	}

	/**
	 * overview : view game logs
	 *
	 * @param string $from
	 * @return	rendered Template
	 */
	public function viewGameLogs($from = 'marketing') {
		if (!$this->permissions->checkPermissions('report_gamelogs')) {
			// if (!$this->permissions->checkPermissions('gamelogs')) {
			$this->error_access($from);
		} else {
			if ($this->utils->isEnabledFeature('enable_gamelogs_v2')) {
				$this->viewGameLogsV2();
			} else {
				if ($from == 'report') {
					$activenav = 'report';
				} else {
					$activenav = 'marketing';
				}
				$data['showGameTree'] = $this->config->item('show_particular_game_in_tree');

				$this->loadTemplate(lang('Marketing Management'), '', '', $activenav);
				$this->load->model(array('game_type_model', 'game_logs', 'external_system', 'player_model'));

				// $data['game_date_from'] = $this->input->get('game_date_from');
				// $data['game_date_to'] = $this->input->get('game_date_to');
				// $data['game_description_id'] = $this->input->get('game_description_id');
				// $data['game_type_id'] = $this->input->get('game_type_id');

				// $data['username'] = '';
				// $data['game_provider'] = '';
				// $data['game_code'] = '';
				$data['game_platforms'] = $this->external_system->getAllActiveSytemGameApi();
				$data['game_types'] = $this->game_type_model->getGameTypesForDisplay();
				$data['player_levels'] = $this->player_model->getAllPlayerLevels();

				if (!$this->permissions->checkPermissions('export_game_logs')) {
					$data['export_report_permission'] = FALSE;
				} else {
					$data['export_report_permission'] = TRUE;
				}

				$data['conditions'] = $this->safeLoadParams(array(
					//last one hour
					'by_date_type' => Game_logs::DATE_TYPES['settled'],
					'by_date_from' => date('Y-m-d H:i:s', strtotime('-1 hour')),
					'by_date_to' => $this->utils->getTodayForMysql() . ' 23:59:59',
					'by_username' => '',
					'by_username_match_mode' => '2',
					'by_affiliate' => '',
					'by_group_level' => '',
					'by_game_code' => '',
					'by_game_platform_id' => '',
					'by_game_flag' => '',
					'by_amount_from' => '',
					'by_amount_to' => '',
					'by_bet_amount_from' => '',
					'by_bet_amount_to' => '',
					// 'by_no_affiliate' => '',
					'game_type_id' => '',
					'game_description_id' => '',
					'round_no' => '',

					'timezone' => '',
					'agency_username' => '',
					'by_bet_type' => '1',
                    'by_free_spin' => ''
				));

				$data['date_types'] =  Game_logs::DATE_TYPES;

				if ($this->utils->getConfig('game_logs_report_date_range_restriction') || $this->utils->getConfig('player_game_history_date_range_restriction')){
					// -- Dates cannot be empty
					if($data['conditions']['by_date_from'] == "")
						$data['conditions']['by_date_from'] = date('Y-m-d H:i:s', strtotime('-1 hour'));

					if($data['conditions']['by_date_to'] == "")
						$data['conditions']['by_date_to'] = $this->utils->getTodayForMysql() . ' 23:59:59';
				}

				$data['conditions']['by_no_affiliate'] = $this->safeGetParam('by_no_affiliate', true, true);

				// $this->utils->debug_log('conditions', $data['conditions'], 'by_game_flag', $data['conditions']['by_game_flag']);

				if ($from == 'report') {
					$this->template->write_view('sidebar', 'report_management/sidebar', ['active' => 'view_game_logs']);
				} else {
					$this->template->write_view('sidebar', 'marketing_management/sidebar', ['active' => 'view_game_logs']);
				}
				$this->template->add_css('resources/css/cardlist.css');
				$this->template->add_css('resources/css/collapse-style.css');
				$this->template->add_css('resources/css/jquery-checktree.css');
				$this->template->add_css('resources/floating_scroll/jquery.floatingscroll.css');
				$this->template->add_js('resources/floating_scroll/jquery.floatingscroll.js');
				$this->addJsTreeToTemplate();
				$this->template->write_view('main_content', 'marketing_management/view_game_logs', $data);
				$this->template->render();
			}
		}
	}

	/**
	 * overview : view game logs v2
	 *
	 * @param string $from
	 * @return	rendered Template
	 */
	public function viewGameLogsV2($from = 'marketing') {
		if (!$this->permissions->checkPermissions('report_gamelogs')) {
			// if (!$this->permissions->checkPermissions('gamelogs')) {
			$this->error_access();
		} else {
			if ($from == 'report') {
				$activenav = 'report';
			} else {
				$activenav = 'marketing';
			}
			$data['showGameTree'] = $this->config->item('show_particular_game_in_tree');

			$this->loadTemplate(lang('Marketing Management'), '', '', $activenav);
			$this->load->model(array('game_type_model', 'game_logs', 'external_system', 'player_model', 'kingrich_api_logs'));

			// $data['game_date_from'] = $this->input->get('game_date_from');
			// $data['game_date_to'] = $this->input->get('game_date_to');
			// $data['game_description_id'] = $this->input->get('game_description_id');
			// $data['game_type_id'] = $this->input->get('game_type_id');

			// $data['username'] = '';
			// $data['game_provider'] = '';
			// $data['game_code'] = '';
			$data['game_platforms'] = $this->external_system->getAllActiveSytemGameApi();
			$this->utils->debug_log('game_platforms count =====> ', count($data['game_platforms']));

			if (!$this->permissions->checkPermissions('export_game_logs_report')) {
				$data['export_report_permission'] = FALSE;
			} else {
				$data['export_report_permission'] = TRUE;
			}

			$data['conditions'] = $this->safeLoadParams(array(
				//last one hour
				'by_date_from' => date('Y-m-d H:i:s', strtotime('-1 hour')),
				'by_date_to' => $this->utils->getTodayForMysql() . ' 23:59:59',
				'by_username' => '',
				'by_username_match_mode' => '2',
				'by_affiliate' => '',
				'by_group_level' => '',
				'by_game_code' => '',
				'by_game_platform_id' => '',
				'by_game_flag' => '',
				'by_amount_from' => '',
				'by_amount_to' => '',
				'by_bet_amount_from' => '',
				'by_bet_amount_to' => '',
				'by_debit_amount_from' => '',
				'by_debit_amount_to' => '',
				'by_credit_amount_from' => '',
				'by_credit_amount_to' => '',
				// 'by_no_affiliate' => '',
				'game_type_id' => '',
				'game_description_id' => '',
				'round_no' => '',
				'by_player_type' => '',
				'by_game_type_globalcom' => '',
				'submitted_status' => '',
				'batch_transaction_id_filter' => '',
				'timezone' => '',
				'agency_username' => '',
				'by_bet_type' => '1',
				'for_data_api' => '',
                'by_free_spin' => '',
                'by_kingrich_currency_branding' => ''
			));

			$data['conditions']['by_no_affiliate'] = $this->safeGetParam('by_no_affiliate', true, true);

			$data['kingrich_currency_branding'] = $this->config->item('kingrich_currency_branding');

			// $this->utils->debug_log('conditions', $data['conditions'], 'by_game_flag', $data['conditions']['by_game_flag']);

			if ($from == 'report') {
				$this->template->write_view('sidebar', 'report_management/sidebar', ['active' => 'view_game_logs']);
			} else {
				$this->template->write_view('sidebar', 'marketing_management/sidebar', ['active' => 'view_game_logs']);
			}
			$this->template->add_css('resources/css/collapse-style.css');
			$this->template->add_css('resources/css/jquery-checktree.css');
			$this->template->add_css('resources/floating_scroll/jquery.floatingscroll.css');
			$this->template->add_js('resources/floating_scroll/jquery.floatingscroll.js');
			$this->addJsTreeToTemplate();
			$this->template->write_view('main_content', 'marketing_management/view_game_logs_v2', $data);
			$this->template->render();
		}
	}

	/**
	 * overview: view API response log from game logs
	 *
	 * @return rendered templete
	 */
	public function kingrich_api_response_logs($from = 'marketing'){
		if (!$this->permissions->checkPermissions('report_gamelogs')) {
			// if (!$this->permissions->checkPermissions('gamelogs')) {
			$this->error_access();
		} else {
			if ($from == 'report') {
				$activenav = 'report';
			} else {
				$activenav = 'marketing';
			}
			$data['showGameTree'] = $this->config->item('show_particular_game_in_tree');

			$this->loadTemplate(lang('Marketing Management'), '', '', $activenav);
			$this->load->model(array('game_type_model', 'game_logs', 'external_system', 'player_model', 'kingrich_api_logs'));

			// OGP-10782 This is not being used anymore
			// if (!$this->permissions->checkPermissions('export_report')) {
			// 	$data['export_report_permission'] = FALSE;
			// } else {
			// 	$data['export_report_permission'] = TRUE;
			// }
			if ($from == 'report') {
				$this->template->write_view('sidebar', 'report_management/sidebar', ['active' => 'view_game_logs']);
			} else {
				$this->template->write_view('sidebar', 'marketing_management/sidebar', ['active' => 'view_game_logs']);
			}
			$this->template->add_css('resources/css/collapse-style.css');
			$this->template->add_css('resources/css/jquery-checktree.css');
			$this->template->add_css('resources/floating_scroll/jquery.floatingscroll.css');
			$this->template->add_js('resources/floating_scroll/jquery.floatingscroll.js');
			$this->addJsTreeToTemplate();
			$this->template->write_view('main_content', 'marketing_management/kingrich/kingrich_api_response_logs', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : view specific game log
	 *
	 * @return	rendered Template
	 */
	public function searchGameLog() {
		if (!$this->permissions->checkPermissions('report_gamelogs')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Marketing Management', '', '', 'marketing');

			$this->load->model(array('game_logs'));

			$data['username'] = $this->input->post('username');
			$data['game_provider'] = $this->input->post('game_provider');
			$data['game_code'] = $this->input->post('game_code');
			$data['dateRangeValue'] = $this->input->post('dateRangeValue');
			$data['dateRangeValueStart'] = $this->input->post('dateRangeValueStart');
			$data['dateRangeValueEnd'] = $this->input->post('dateRangeValueEnd');
			$data['games'] = $this->player_manager->getAllGames();
			$data['game_history'] = $this->game_logs->getSpecificGameLogData($data['username'], $data['game_provider'], $data['dateRangeValueStart'], $data['dateRangeValueEnd'], $data['game_code']);

			$this->template->write_view('sidebar', 'marketing_management/sidebar');
			$this->template->write_view('main_content', 'marketing_management/view_game_logs', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : promo checkers
	 */
	public function testPromoChecker() {
		$this->load->library('promo_library');
		$this->promo_library->checkPromoForHiding();
	}

	/**
	 * overview : settings for friend referral
	 *
	 * detail : save friend referral settings
	 */
	public function friend_referral_settings() {
		if (!$this->permissions->checkPermissions('friend_referral_setting')) {
			$this->error_access();
		} else {

			$this->load->model('friend_referral_settings');

			$this->form_validation->set_rules('bet', 'Bet', 'trim|required|xss_clean');
			$this->form_validation->set_rules('deposit', 'Deposit', 'trim|required|xss_clean');
			$this->form_validation->set_rules('bonus_amount', 'Bonus Amount', 'trim|required|xss_clean');
			$this->form_validation->set_rules('withdraw_condition', 'Withdraw Condition', 'trim|required|xss_clean');
			$this->form_validation->set_rules('promo_cms_id', 'Promo ID', 'trim|xss_clean');
			if($this->utils->isEnabledFeature('enable_friend_referral_cashback')){
				$this->form_validation->set_rules('cashback_rate', 'Cashback Rate', 'trim|required|xss_clean');
			}

			if ($this->form_validation->run()) {

				$bet = preg_replace('#,#', '', $this->input->post('bet'));
				$deposit = preg_replace('#,#', '', $this->input->post('deposit'));
				$bonus_amount = preg_replace('#,#', '', $this->input->post('bonus_amount'));
				$cashback_rate = preg_replace('#,#', '', $this->input->post('cashback_rate'));
				$withdraw_condition = preg_replace('#,#', '', $this->input->post('withdraw_condition'));
				$promo_id = null;
				if ($this->utils->isEnabledFeature('bind_promorules_to_friend_referral')) {
					$promo_id = $this->input->post('promo_cms_id');
				}

				$this->friend_referral_settings->saveFriendReferralSettings(array(
					'ruleInBet' => $bet,
					'ruleInDeposit' => $deposit,
					'bonusAmount' => $bonus_amount,
					'cashback_rate' => $cashback_rate,
					'withdrawalCondition' => $withdraw_condition,
					'updatedOn' => $this->utils->getNowForMysql(),
					'status' => 0,
					'promo_id' => $promo_id,
				));

				$message = lang('save.success');
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);

			}

			$data['friend_referral_settings'] = $this->friend_referral_settings->getFriendReferralSettings();
			$data['promoCms'] = $this->promorules->getAvailablePromoCMSList();

			$this->loadTemplate('Marketing Management', '', '', 'marketing');
			$this->template->write_view('sidebar', 'marketing_management/sidebar');
			$this->template->write_view('main_content', 'marketing_management/friend_referral_settings', $data);
			$this->template->render();

		}
	}

	/**
	 * overview : balance adjustment
	 *
	 * detail : get all active system game api
	 */
	public function batchBalanceAdjustment() {
		if (!$this->permissions->checkPermissions('batch_adjust_balance')) {
			$this->error_access();
		} else {
			$this->load->model(array('external_system', 'game_provider_auth', 'player_model', 'payment'));
			$data = array();
			$data['game_platforms'] = $this->external_system->getAllActiveSytemGameApi();

			$this->loadTemplate('Marketing Management', '', '', 'marketing');
			$this->template->write_view('sidebar', 'marketing_management/sidebar');
			$this->template->write_view('main_content', 'marketing_management/batch_balance_adjustment', $data);
			$this->template->render();
		}
	}

	public function freeround() {
		$this->load->library('isoftbet_free_round');
		$this->loadTemplate('Free Round Package Management', '', '', 'marketing');

		$operator_id = 0;

		$state = ($this->input->get('state')) ?: null;

		$freespin = $this->isoftbet_free_round->freerounds($state, null, null, "-1");

		$data = array();
		$data['freespin'] = $freespin->response->freerounds;

		$this->template->write_view('sidebar', 'marketing_management/sidebar');
		$this->template->write_view('main_content', 'marketing_management/freespin/lists', $data);
		$this->template->render();
	}

	public function addFreeround() {

		if ($this->input->post()) {
			return $this->_submitFreeround();
		}

		$this->load->model('game_description_model');

		$this->template->write_view('sidebar', 'marketing_management/sidebar');
		$this->template->add_css('resources/css/collapse-style.css');
		$this->template->add_css('resources/css/jquery-checktree.css');
		$this->template->add_js('resources/js/ace/ace.js');
		$this->template->add_js('resources/js/ace/mode-javascript.js');
		$this->template->add_js('resources/js/ace/theme-tomorrow.js');
		$this->template->add_js('resources/js/jquery-checktree.js');
		$this->template->add_js('resources/js/select2.min.js');
		$this->template->add_css('resources/css/select2.min.css');

		$data = array();

		$data['players'] = $this->player->getAllPlayers(null, null, null, null);
		$data['games'] = $this->game_description_model->getFreeSpinGame();

		// echo "<pre>";
		// print_r($data['games']);
		// exit;

		$this->loadTemplate('Free Round Package Management', '', '', 'marketing');
		$this->template->write_view('main_content', 'marketing_management/freespin/form', $data);
		$this->template->render();

	}

	function _submitFreeround() {

		try {

			$this->load->library('isoftbet_free_round');

			$name = $this->input->post('name');
			$operator_id = 0;
			$games = explode(',', $this->input->post('games'));
			$lines = $this->input->post('line');
			$line_bet = $this->input->post('line_bet');
			$supplier = "1";
			$player_ids = explode(',', $this->input->post('player_ids'));
			$limit_per_player = $this->input->post('limit_per_player');
			$promo_code = $this->input->post('promo_code');
			$max_players = $this->input->post('player_limit');
			$coins = $this->input->post('coins');

			$start_date = '';

			if (!$this->input->post('start_in_five_min')) {
				$start_date = date('Y-m-d H:i:s', strtotime($this->input->post('start_date')));
			}

			$end_date = '';
			if ($this->input->post('end_date')) {
				$end_date = date('Y-m-d H:i:s', strtotime($this->input->post('end_date')));
			}

			$duration_relative = ($this->input->post('relative_duration')) ? $this->input->post('relative_duration') : '';
			$open_for_all = 0;

			$result = $this->isoftbet_free_round->freerounds_create($name, $operator_id, $games, $supplier, $lines, $line_bet, $player_ids, $limit_per_player, $promo_code, $max_players, $start_date, $end_date, $duration_relative, $coins, $open_for_all);

			if (isset($result->error)) {
				throw new Exception($result->error_message);
			}

			$this->alertMessage(1, lang('sys.gd27'));

		} catch (Exception $e) {

			$this->alertMessage(2, $e->getMessage());

		}

		redirect('marketing_management/freeround');
	}

	public function freeroundPlayers($fround_id = '') {

		$this->loadTemplate('Free Round Package Management', '', '', 'marketing');

		$this->load->library('isoftbet_free_round');

		$data = array();

		$players = $this->isoftbet_free_round->players($fround_id);

		$data['fround_id'] = $fround_id;
		$data['players'] = $players->response->players;

		$this->template->write_view('sidebar', 'marketing_management/sidebar');
		$this->template->write_view('main_content', 'marketing_management/freespin/player_lists', $data);
		$this->template->render();
	}

	public function addFreeroundPlayer($fround_id = '') {

		if ($this->input->post()) {
			return $this->_freeroundPlayerSubmit();
		}

		$this->loadTemplate('Free Round Package Management', '', '', 'marketing');

		$this->load->library('isoftbet_free_round');

		$data = array();

		$data['fround_id'] = $fround_id;
		$data['players'] = $this->player->getAllPlayers(null, null, null, null);

		$this->template->write_view('sidebar', 'marketing_management/sidebar');
		$this->template->write_view('main_content', 'marketing_management/freespin/player_form', $data);
		$this->template->render();
	}

	public function _freeroundPlayerSubmit() {

		try {

			$this->load->library('isoftbet_free_round');

			$fround_id = $this->input->post('fround_id');
			$player_ids = $this->input->post('player_ids');
			$promo_code = $this->input->post('promo_code');

			$result = $this->isoftbet_free_round->players_register($fround_id, $player_ids, $promo_code);

			if (isset($result->error)) {
				throw new Exception($result->error_message);
			}

			$this->alertMessage(1, lang('sys.gd27'));

		} catch (Exception $e) {

			$this->alertMessage(2, $e->getMessage());

		}

		redirect('marketing_management/freeroundPlayers/' . $fround_id);
	}

	public function removeFreeroundPlayer() {

		try {

			$this->load->library('isoftbet_free_round');

			$fround_id = $this->input->post('fround_id');
			$player_ids = $this->input->post('player_id');

			if (!is_array($player_ids)) {
				$player_ids = explode(',', $player_ids);
			}

			$result = $this->isoftbet_free_round->players_remove($fround_id, $player_ids);

			if (isset($result->error)) {
				throw new Exception($result->error_message);
			}

			$this->alertMessage(1, lang('sys.gd27'));

		} catch (Exception $e) {

			$this->alertMessage(2, $e->getMessage());

		}

		return true;
	}

	public function freeroundCoins($fround_id = '') {

		$this->loadTemplate('Free Round Package Management', '', '', 'marketing');

		$this->load->library('isoftbet_free_round');

		$data = array();

		$currencies = $this->isoftbet_free_round->currencies($fround_id);

		$data['fround_id'] = $fround_id;
		$data['currencies'] = $currencies->response->coins;

		$this->template->write_view('sidebar', 'marketing_management/sidebar');
		$this->template->write_view('main_content', 'marketing_management/freespin/currency_lists', $data);
		$this->template->render();
	}

	public function addFreeroundCoins($fround_id = '') {
		if ($this->input->post()) {
			return $this->_freeroundCoinsSubmit();
		}

		$this->loadTemplate('Free Round Package Management', '', '', 'marketing');
		$this->load->model('game_description_model');
		$this->load->library('isoftbet_free_round');

		$data = array();

		$data['fround_id'] = $fround_id;
		$data['players'] = $this->player->getAllPlayers(null, null, null, null);
		$data['games'] = $this->game_description_model->getFreeSpinGame();

		$this->template->write_view('sidebar', 'marketing_management/sidebar');
		$this->template->write_view('main_content', 'marketing_management/freespin/coins_form', $data);
		$this->template->render();
	}

	public function _freeroundCoinsSubmit() {
		try {

			$this->load->library('isoftbet_free_round');

			$fround_id = $this->input->post('fround_id');

			$coins = array();

			foreach ($this->input->post('coins') as $key => $value) {
				$game_code = $key;
				$_coins = array();
				foreach ($value as $key => $value) {
					$_coins[] = array(
						'coin_value' => $value['coin_value'],
						'currency' => $value['currency'],
						'game_id' => $game_code,
					);
				}
				$coins[] = $_coins;
			}

			$result = $this->isoftbet_free_round->currencies_add($fround_id, $coins);

			if (isset($result->error)) {
				throw new Exception($result->error_message);
			}

			$this->alertMessage(1, lang('sys.gd27'));

		} catch (Exception $e) {

			$this->alertMessage(2, $e->getMessage());

		}

		redirect('marketing_management/freeroundCoins/' . $fround_id);
	}

	public function dryrun_promo($cmsPromoId){
		$data = array('title' => lang('Marketing Management'), 'sidebar' => 'marketing_management/sidebar',
			'activenav' => 'marketing_management');

		$userId = $this->authentication->getUserId();
		// $username = $this->authentication->getUsername();
		$render = true;

		// $this->addBoxDialogToTemplate();
		$this->load->model(['promorules', 'player_model']);
		$deposit_amount=$this->input->post('deposit_amount');
		$player_username=$this->input->post('player_username');
		$playerId=null;
		if(!empty($player_username)){

			$playerId=$this->player_model->getPlayerIdByUsername($player_username);

		}
		$promorule=$this->promorules->getPromoruleByPromoCms($cmsPromoId);
		$preapplication=false;
		$playerPromoId=null;
		$triggerEvent='manual_admin';
		$dry_run=true;

		$mock=$this->utils->getConfig('promotion_mock');

		// $mock=[
		// 	'get_game_result_amount'=>$this->input->post('get_game_result_amount'),
		// 	'current_player_total_balance'=>$this->input->post('current_player_total_balance'),
		// 	'times_released_bonus_on_this_promo_today'=>$this->input->post('times_released_bonus_on_this_promo_today'),
		// 	'sum_deposit_amount'=>$this->input->post('sum_deposit_amount'),
		// ];

		$notnull_mock=[];

		foreach ($mock as $key => &$value) {

			$post_value=$this->input->post($key);
			//maybe value will be 0
			if($post_value!==FALSE && $post_value!==null && $value!==''){
				$value=$post_value;
			}

			if($value!==FALSE && $value!==null && $value!==''){
				$notnull_mock[$key]=$value;
			}
		}

		$promo_rule_class_mock=$this->input->post('promo_rule_class_mock');
		if(!empty($promo_rule_class_mock)){
			$notnull_mock['promo_rule_class_mock']=$this->utils->decodeJson($promo_rule_class_mock);
		}
		$data['promo_rule_class_mock']=$promo_rule_class_mock;

		$extra_info=['debug_log'=>'', 'mock'=>$notnull_mock];

		$success=false;
		$message=null;
		$errorMessageLang=null;
		$playerBonusAmount=null;
		if(!empty($playerId)){
			list($success, $message)=$this->promorules->checkAndProcessPromotion($playerId, $promorule, $cmsPromoId,
				$preapplication, $playerPromoId, $extra_info, $triggerEvent, $dry_run);
			$message=lang(strip_tags($message));

			//calc bonus
			if($success){
				$playerBonusAmount = $this->promorules->getBonusAmount($promorule, $deposit_amount, $playerId, $errorMessageLang, $extra_info, $dry_run);
			}
		}

		$data['cmsPromoId']=$cmsPromoId;
		$data['deposit_amount']=$deposit_amount;
		$data['player_username']=$player_username;
		$data['mock']=$mock;
		//dry run promo rule
		$data['result']=[
			'cms_promo_id'=>$cmsPromoId,
			'success'=> $success,
			'message'=> $message,
			'errorMessageLang'=>$errorMessageLang,
			'playerBonusAmount'=>$playerBonusAmount,
		];
		// 'cms id:'.$cmsPromoId.', username:'.$player_username.', success:'.($success ? 'true' : 'false').', message:'.lang($message);
		$data['debug_log']=$extra_info['debug_log'];
		$data['runtime_mock']=$notnull_mock;

		// $settings_name_list = array('approve_transfer_to_main', 'approve_transfer_from_main', 'min_withdraw');

		// $this->utils->debug_log('load settings', 'data', $data);

		// $this->utils->error_log('test error log', $data);

		// $this->utils->info_log('test info log', $data);

		// $data['settings'] = $this->operatorglobalsettings->getSystemSettings($settingNameList);

		// $this->load->library(array('sms/sms_sender'));
		// $data['smsBalances'] = $this->sms_sender->getSmsBalances();
		// $this->utils->debug_log('load settings', $data['settings']);

		$this->loadDefaultTemplate(array(
			'resources/js/ace/ace.js','resources/js/ace/mode-javascript.js',
			'resources/js/ace/theme-tomorrow.js','resources/js/ace-helper.js',
			),
			array('resources/css/general/style.css'),
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				), $data['sidebar'],
			'marketing_management/test_promo', $data, $render);
	}

    /**
     * manage cashback request
     */
    public function manage_cashback_request() {
        if (!$this->permissions->checkPermissions('cashback_request')) {
            $this->error_access();
        } else {
            $this->loadTemplate('Marketing Management', '', '', 'marketing');

            $username = $this->input->get('username') ? $this->input->get('username') : '';
            $cashback_request_type = $this->input->get('cashback_request_type') ? $this->input->get('cashback_request_type') : '';
            $status = $this->input->get('status') ? $this->input->get('status') : '';
            $parent_id = $this->input->get('parent_id') ? $this->input->get('parent_id') : '';
            $request_amount_from = $this->input->get('request_amount_from') ? $this->input->get('request_amount_from') : '';
            $request_amount_to = $this->input->get('request_amount_to') ? $this->input->get('request_amount_to') : '';
            $starttime_from = $this->input->get('starttime_from') ? $this->input->get('starttime_from') : '';
            $starttime_to = $this->input->get('starttime_to') ? $this->input->get('starttime_to') : '';

            $date_from = $this->input->get('date_from');
            $date_to = $this->input->get('date_to');
            if(empty($date_from)) $date_from = date('Y-m-d', strtotime('-1 day')).' 00:00:00';
            if(empty($date_to)) $date_to = $this->utils->getTodayForMysql() . ' 23:59:59';

            $conditions=$this->safeLoadParams([
                'username'=>$username,
                'cashback_request_type'=>$cashback_request_type,
                'status'=>$status,
                'parent_id'=>$parent_id,
                'request_amount_from'=>$request_amount_from,
                'request_amount_to'=>$request_amount_to,
                'starttime_from'=>$starttime_from,
                'starttime_to'=>$starttime_to,
                //yesterday to now
                'date_from' => $date_from,
                'date_to' => $date_to,
            ]);
            $data=['conditions'=>$conditions];
            $this->template->write_view('sidebar', 'marketing_management/sidebar');
            $this->template->write_view('main_content', 'marketing_management/cashback/manage_cashback_request', $data);
            $this->template->render();
        }
    }

	public function viewCashbackRequestDetail($cashback_request_id, $username){

		$this->load->model(array('total_cashback_player_game'));
		$result = $this->total_cashback_player_game->getDataByCashbackRequestId($cashback_request_id);

		$data['count_all'] = count($result);
		$config['base_url'] = "javascript:get_account_process_list_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '5';
		$config['num_links'] = '2';
		$config['uri_segment'] = '3';

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
		$data['total_cashback_player_game'] = !empty($result) ? $result : [];
		$data['cashback_request_id'] = $cashback_request_id;
		$data['username'] = $username;

		$this->load->view('marketing_management/ajax_view_cashback_request', $data);
	}

	public function approveCashbackRequest($cashback_request_id) {

        if (!$this->permissions->checkPermissions('cashback_request')) {
            return $this->error_access();
        }

		$this->load->model(array('cashback_request'));

		$success=false;
		$message=null;

		if(!empty($cashback_request_id)){

			$cashback_request=$this->cashback_request->getCashbackRequestById($cashback_request_id);
			if(!empty($cashback_request)){
				$playerId=$cashback_request->player_id;
				if(!empty($playerId)){

					$controller=$this;
					$adminUserId = $this->authentication->getUserId();
					$success=$this->lockAndTransForPlayerBalance($playerId, function()
							use($controller, $cashback_request_id, $adminUserId, &$message){

						return $controller->cashback_request->approveCashbackRequest($cashback_request_id, $adminUserId, $message);

					});

				}
			}
		}

		if($success){

			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Cashback is paid'));

		} else {

			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Pay cashback failed').' '.$message);

		}

		redirect('marketing_management/manage_cashback_request');
	}

	public function approveSelectedCashbackRequest() {

        if (!$this->permissions->checkPermissions('cashback_request')) {
            return $this->error_access();
        }

		$redirect_url = $this->input->post('redirect_url');
		$selectd_cashback_request_ids = $this->input->post("selected_id_value");

		$adminUserId = $this->authentication->getUserId();
		$controller=$this;
		$this->load->model(array('cashback_request'));

		$cashback_request_ids = explode(",", $selectd_cashback_request_ids);

		if(!empty($cashback_request_ids)){
			foreach ($cashback_request_ids as $cashback_request_id){
				$cashback_request=$this->cashback_request->getCashbackRequestById($cashback_request_id);
				if(!empty($cashback_request)){
					$playerId=$cashback_request->player_id;
					if(!empty($playerId)){
						// $adminUserId = $this->authentication->getUserId();
						$success=$this->lockAndTransForPlayerBalance($playerId, function()
								use($controller, $cashback_request_id, $adminUserId, &$message){

							return $controller->cashback_request->approveCashbackRequest($cashback_request_id, $adminUserId, $message);

						});

						if(!$success){
							$this->utils->debug_log('Pay cashback failed :'.$cashback_request_id, $message);
						// 	$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Pay cashback failed').' '.$message);
						}
					}else{
						$this->utils->debug_log('not found player id in :'.$cashback_request_id);
					}
				}else{
					$this->utils->debug_log('not found cashback request:'.$cashback_request_id);
				}
				// $this->cashback_request->approveCashbackRequest($cashback_request_id, $adminUserId);
			}
		}

		redirect($redirect_url);
	}

	public function declineCashbackRequest() {
        if (!$this->permissions->checkPermissions('cashback_request')) {
            return $this->error_access();
        }

		$declineCashbackRequestId = $this->input->post('declineCashbackRequestId');
		$reasonToCancel = $this->input->post('reasonToCancel');

		$adminUserId = $this->authentication->getUserId();

		$this->load->model(array('cashback_request'));
		$this->cashback_request->declineCashbackRequest($declineCashbackRequestId, $adminUserId, $reasonToCancel);

		redirect('marketing_management/manage_cashback_request');
	}

	public function queryBetDetail($game_platform_id, $playerId) {
		$this->load->model(array('player','game_provider_auth'));
		$api = $this->utils->loadExternalSystemLibObject($game_platform_id);
		$betDetailLink = '';
		if (method_exists($api, 'queryBetDetailLink')) {
			if ((int)$game_platform_id == MG_API) {
				$getPlayerGameHistoryURL = array();
				$gameProviderInfo = $this->game_provider_auth->getByPlayerIdGamePlatformId($playerId, $game_platform_id);
				$getPlayerGameHistoryURL = $api->queryBetDetailLink($gameProviderInfo['login_name'], null, Array('password'=> $gameProviderInfo['password']));
				if ($getPlayerGameHistoryURL && $getPlayerGameHistoryURL['success']) {
					$betDetailLink = $getPlayerGameHistoryURL['url'];
				}
			} elseif ((int)$game_platform_id == QT_API) {
				$player = $this->player->getPlayerById($playerId);
				$getPlayerGameHistoryURL = $api->queryBetDetailLink($player['username'], null, null);
				if ($getPlayerGameHistoryURL && $getPlayerGameHistoryURL['success']) {
					$betDetailLink = $getPlayerGameHistoryURL['url'];
				}
			}
		}
		$this->utils->debug_log('betDetailLink ==================>',$betDetailLink);
		redirect($betDetailLink);
	}

	/**
	 * Promo bonus game manager
	 * OGP-3381 (main)/3558 (subtask)
	 * @uses	model: promo_game
	 *
	 * @return	none
	 */
	public function bonusGameSettings($game_id = -1) {
		if (!$this->utils->isEnabledFeature('bonus_games__enable_bonus_game_settings') || !$this->permissions->checkPermissions('bonus_game_settings')) {
			$this->error_access();
			return;
		}

		$this->loadTemplate('Marketing Management', '', '', 'marketing');
		$this->template->write_view('sidebar', 'marketing_management/sidebar');

		$this->load->model(['promo_games']);

		$data['game'] = [
			'id' => null,  'gametype_id' => null, 'gamename' => null, 'desc' => null, 'theme_id' => null, 'deploy_channels' => [] ,
			'prizes' => null
		];
		// Edit game
		if ($game_id > 0) {
			$data['game'] = $this->promo_games->get_bonus_game_by_id($game_id);
		}

		$data['game_id'] = $game_id;

		$data['bonus_games'] = $this->promo_games->get_bonus_games_for_listing();
		$data['elems'] = $this->promo_games->get_bonus_game_edit_elems();


		$this->template->write_view('main_content', 'marketing_management/promorules/promo_game_manager', $data);
		$this->template->render();
	}

	public function bonusGameOps($operation = null, $game_id = null, $extra = null) {
		$this->load->model(['promo_games']);
		try {
			switch ($operation) {
				case 'add_edit' :
					$game_id = $this->input->post('game_id');
					$fields = $this->input->post();
					if (empty($game_id) || $game_id < 1) {
						$this->bonusGameOps_add($fields);
					}
					else {
						$this->bonusGameOps_edit($game_id, $fields);
					}
					break;

				case 'remove' :
					if ($game_id <= 0) {
						throw new Exception('Illegal game_id');
					}
					$remove_res = $this->promo_games->remove_game($game_id);
					if (!$remove_res) {
						throw new Exception("Error removing game #$game_id", 3);
					}
					throw new Exception("Game #$game_id successfully removed", 1003);
					break;

				case 'enable' :
					if (empty($game_id)) {
						throw new Exception('Illegal game_id for enable operation', 6);
					}
					$enab_res = $this->promo_games->enable_disable_game($game_id, 'enable');
					if (!$enab_res) {
						throw new Exception("Error enabling game #{$game_id}", 7);
					}
					throw new Exception("Successfully enabled game #{$game_id}", 1008);
					break;

				case 'disable' :
					if (empty($game_id)) {
						throw new Exception('Illegal game_id for disable operation', 8);
					}
					$enab_res = $this->promo_games->enable_disable_game($game_id, 'disable');
					if (!$enab_res) {
						throw new Exception("Error disabling game #{$game_id}", 9);
					}
					throw new Exception("Successfully disabled game #{$game_id}", 1010);
					break;
				/*
				case 'remove_prize' :
					if (empty($game_id) || empty($extra)) {
						throw new Exception('Illegal game_id or prize_id when removing prize', 4);
					}
					$remove_prize_res = $this->promo_games->remove_prize($game_id, $extra);
					if (!$remove_prize_res) {
						throw new Exception("Error removing prize #{$extra} for game #{$game_id}", 5);
					}

					$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, "Successfully removed prize #{$extra} for game #{$game_id}");
					return redirect("marketing_management/bonusGameSettings/{$game_id}");
					break;
				*/

				default :
					throw new Exception('Illegal operation');
			}
		}
		catch (Exception $ex) {
			if ($ex->getCode() < 1000) {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $ex->getMessage());
			}
			else {
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $ex->getMessage());
			}
		}
		finally {
			if ($operation == 'add_edit' && !empty($game_id)) {
				redirect("marketing_management/bonusGameSettings/{$game_id}");
			}
			else {
				redirect('marketing_management/bonusGameSettings');
			}
			return;
		}
	}

	protected function bonusGameOps_add($fields) {
		$this->load->model(['promo_games']);
		$insertset_game = [
			'gametype_id'	=> $fields['gametype_id'] ,
			'gamename'		=> $fields['gamename'] ,
			'theme_id'		=> $fields['theme_id'] ,
			'desc'			=> $fields['description'] ,
			'status'		=> 'enabled' ,
			'created_by'	=> $this->authentication->getUserId() ,
			'created_at'	=> $this->utils->getNowForMysql()
		];

		$game_id = $this->promo_games->create_game($insertset_game);

		if ($game_id < 1) {
			$this->utils->debug_log('bonusGameOps_add(): Error inserting game');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, "Error creating bonus game");
			return;
		}

		$res_channels = $this->promo_games->update_deploy_channel($game_id, $this->input->post('deploy_channel'));

		$res_prizes = $this->promo_games->update_prizes($game_id, $this->input->post('prize'));

		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, "Bonus Game #{$game_id} was created successfully.");

		return;
	}

	protected function bonusGameOps_edit($game_id, $fields) {
		$this->load->model(['promo_games']);
		// $this->deb($fields);
		$updateset_game = [
			'gametype_id'	=> $fields['gametype_id'] ,
			'gamename'		=> $fields['gamename'] ,
			'theme_id'		=> $fields['theme_id'] ,
			'desc'			=> $fields['description'] ,
			'updated_by'	=> $this->authentication->getUserId() ,
			'updated_at'	=> $this->utils->getNowForMysql()
		];

		$res_games = $this->promo_games->update_game($game_id, $updateset_game);

		$res_channels = $this->promo_games->update_deploy_channel($game_id, $this->input->post('deploy_channel'));

		$res_prizes = $this->promo_games->update_prizes($game_id, $this->input->post('prize'));

		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, "Bonus Game #{$game_id} was successfully edited.");

		return;
	}

	public function bonusGames_get_avail_games_for_promorules() {
		$this->load->model(['promo_games']);
		$ret = [ 'success' => false, 'mesg' => 'exec_incomplete', 'result' => null ];
		try {
			$data = $this->promo_games->get_avail_games_for_promorules();

			$ret = [ 'success' => true, 'mesg' => null, 'result' => $data ];
		}
		catch (Exception $ex) {
			$ret = ['success' => false, 'mesg' => $ex->getMessage(), 'result' => null ];
		}
		finally {
			$this->returnJsonResult($ret);
		}
	}

	public function import_game_logs() {
        if ( ! $this->permissions->checkPermissions('import_game_logs')) {
            $this->error_access();
        } else {

			set_time_limit(0);

			$this->load->helper('url');

			if ( ! isset($_FILES['import']['tmp_name'])) {
				show_error('File not found');
			}

			$handle = fopen($_FILES['import']['tmp_name'], "r");
			$keys = fgetcsv($handle);

			foreach ($keys as &$key) {
				$key = url_title($key,'_',TRUE);
			}

			$lines = array();
			while (($values = fgetcsv($handle)) !== FALSE) {
				$lines[] = $values;
		   	}

			$rows = array();
			foreach ($lines as $values) {
				$rows[] = array_combine($keys, $values);
			}

			if (empty($rows)) {
				show_error('FORMAT ERROR: 2');
			}

			$import_currency = $this->input->post('import_currency');
			$response_result_id = date('YmdHis') . random_string('numeric',2);
			$import_game_logs = array_map(function($row) use ($response_result_id,$import_currency) {
				$row['external_uniqueid'] 	= $row['round_no'];
				//$row['external_uniqueid'] 	= md5(serialize($row));
				$row['game_name'] 			= (mb_detect_encoding($row['game_name']) == "UTF-8" ? $row['game_name'] : iconv( "Windows-1252", "UTF-8", $row['game_name']));
				$row['date'] 				= date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $row['date'])));
				$row['bet_time'] 			= date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $row['bet_time'])));
				$row['response_result_id'] 	= $response_result_id;
				$row['created_at'] 			= date('Y-m-d H:i:s');
				$row['bet_type'] 			= (!empty($import_currency)? $import_currency : $this->utils->getCurrentCurrency()['currency_code']);
				return $row;
			}, $rows);

			if (empty($import_game_logs)) {
				show_error('FORMAT ERROR');
			}

			// -- Check the records if currency has been set
			$out_of_currency = array();
			if(!empty($import_game_logs) && !empty($import_currency)) {
				$this->load->model('player_model');
				$player_usernames = array_unique(array_column($import_game_logs, 'player_username'));
				$players = $this->player_model->getPlayersByUserNameAndCurrency($player_usernames, $import_currency);
				foreach ($import_game_logs as $key => $import_game_log) {
					if (!in_array($import_game_log['player_username'], $players)) { //check if players
						array_push($out_of_currency, $import_game_log['round_no']);
						unset($import_game_logs[$key]);
					}
				}
			}

			// -- Check for records that have bet time outside the provided date range
			$date_from = $this->input->post('date_from');
			$date_to = $this->input->post('date_to');
			$out_of_range = array();

			if($this->input->post('date_from') && $this->input->post('date_to')) {
				if(!empty($import_game_logs)) {
					foreach ($import_game_logs as $key => $import_game_log) {
						$bet_time = date('Y-m-d', strtotime($import_game_log['bet_time']));
						$date_from = date('Y-m-d', strtotime($date_from));
						$date_to = date('Y-m-d', strtotime($date_to));

						if($bet_time < $date_from || $bet_time > $date_to){
							array_push($out_of_range, $import_game_log['round_no']);
							unset($import_game_logs[$key]);
						}
					}
				}
			}


			// -- Check for duplicate round IDs within the file
			$in_file_duplicates = array();
			$used_round_nos = array();
			$results = array();
			$in_system_duplicates = array();
			if(!empty($import_game_logs)) {
				foreach ($import_game_logs as $key => $import_game_log) {
					if(!in_array($import_game_log['round_no'], $used_round_nos)){
						$used_round_nos[] = $import_game_log['round_no'];
					}
					else{
						/**
						 * In occurancre of duplicate round_nos within the file,
						 * record all duplicate round no, then remove it from the game logs
						 * that are to be imported.
						 */
						$in_file_duplicates[] = $import_game_log['round_no'];
						unset($import_game_logs[$key]);
					}
				}
			}

			// -- Check if round ID already exists in the database
			if(!empty($used_round_nos)){
				$this->db->select('external_uniqueid');
				$this->db->from('game_logs');
				$this->db->where_in('external_uniqueid', $used_round_nos);
				$query = $this->db->get();
	        	$results = $query->result_array();
        	}
        	/**
			 * In occurancre of duplicate round_nos within the system,
			 * record all duplicate round no, then remove it from the game logs
			 * that are to be imported.
			 */
        	if( !empty($results)){
        		foreach ($results as $key => $result) {
        			array_push($in_system_duplicates, $result['external_uniqueid']);
        		}
        	}

        	if(!empty($in_system_duplicates)){
        		foreach ($import_game_logs as $key => $import_game_log) {
        			if(in_array($import_game_log['round_no'], $in_system_duplicates))
        				unset($import_game_logs[$key]);
        		}
        	}

        	// -- if there's no duplicate Round ID, proceed with the process.
        	$dates = array_column($import_game_logs, 'bet_time');

        	$start_date = null;
			$end_date = null;
			$result = array();
        	if(!empty($import_game_logs)){


				$this->utils->debug_log('total', count($import_game_logs));
				$this->utils->debug_log('unique', count(array_unique(array_column($import_game_logs, 'external_uniqueid'))));
				$this->db->insert_batch('import_game_logs', $import_game_logs, true);
				$this->utils->debug_log('inserted', $this->db->affected_rows());
				$this->db->update_batch('import_game_logs', $import_game_logs, 'external_uniqueid');
				$this->utils->debug_log('updated', $this->db->affected_rows());

				// $this->load->library('language_function');
				// $this->load->library('lib_queue');
				// $this->load->model('queue_result');

				$start_date = min($dates);
				$end_date = max($dates);
				// $caller = $this->authentication->getUserId();
				// $state = null;
				// $lang = $this->language_function->getCurrentLanguage();
				// $playerName = '';
				// $dry_run = 'false';
				// $timelimit = 30; //minutes
				// $merge_only = TRUE;
				// $token = $this->lib_queue->addSyncGameLogsJob($start_date, $end_date, DUMMY_GAME_API,
				// 	Queue_result::CALLER_TYPE_ADMIN, $caller, $state, $lang, $playerName, $dry_run, $timelimit, $merge_only);

				// redirect('/system_management/common_queue_syncgamelogs/'.$token);

				$api = $this->utils->loadExternalSystemLibObject(DUMMY_GAME_API);

				if ( ! $api) {
					show_error('Game API not found!');
				}

				$dateTimeFrom = new DateTime($start_date);
				$dateTimeTo = new DateTime($end_date);

				$api->syncInfo[$response_result_id] = array('dateTimeFrom' => $dateTimeFrom, 'dateTimeTo' => $dateTimeTo, 'response_result_id' => $response_result_id);
				$result = $api->syncMergeToGameLogs($response_result_id);
        	}


			$output = array(
				'response_result_id' 	=> $response_result_id,
				'from' 					=> $start_date,
				'to' 					=> $end_date,
				'result' 				=> !empty($result)? $result : 'N/A',
				'Out of Currency'       => array(
					'currency'  => $import_currency,
					'count'	    => count($out_of_currency),
					'round_ids'	=> empty($out_of_currency) ? 'N/A' : $out_of_currency,
				),
				'Out of Date Range'		=> array(
					'date_from' => $date_from ?: 'N/A',
					'date_to'	=> $date_to ?: 'N/A',
					'count'	=> count($out_of_range),
					'round_ids'	=> empty($out_of_range) ? 'N/A' : $out_of_range,
				),
				'Duplicate Round IDs'	=> array(
					'Within the file'	=> array(
						'count'	=> count($in_file_duplicates),
						'round_ids' => empty($in_file_duplicates) ? 'N/A' : $in_file_duplicates,
					),
					'Within the system'	=> array(
						'count'	=> count($in_system_duplicates),
						'round_ids' => empty($in_system_duplicates) ? 'N/A' : $in_system_duplicates,
					)
				)
			);

			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));

		}
	}

    public function ole777_wager_sync() {
        if (!$this->permissions->checkPermissions('ole777_wager_sync')) {
            $this->error_access();
            return;
        }

        $this->loadTemplate('Marketing Management', '', '', 'marketing');

        $date_from = $this->input->get('date_from');
        // $date_to = $this->input->get('date_to');

        if(empty($date_from)) $date_from = date('Y-m-d', strtotime('0 day'));
        // if(empty($date_to)) $date_to = $this->utils->getTodayForMysql() . ' 23:59:59';

        $conditions=$this->safeLoadParams([
            // 'starttime_from'=>$starttime_from,
            // 'starttime_to'=>$starttime_to,
            //yesterday to now
            'date_from' => $date_from,
            // 'date_to' => $date_to,
        ]);
        $data=['conditions'=>$conditions];
        $this->template->write_view('sidebar', 'marketing_management/sidebar');
        $this->template->write_view('main_content', 'marketing_management/ole777_wager_sync', $data);
        $this->template->render();
    }

    public function ole777_wager_sync_confirm() {
    	$id = $this->input->post('id');
    	$this->load->model('ole_reward_model');
    	$res = $this->ole_reward_model->local_syncs_toggle($id);

    	$this->returnJsonResult($res);
    }

	public function ole777_wager_rebuild() {
		$date_src = $this->input->get('date');
		$date = date('Y-m-d', strtotime($date_src));
		$dateymd = date('Ymd', strtotime($date_src));
		$res = [
			'successs'	=> false ,
			'code'		=> -127 ,
			'message'	=> '',
			'result'	=> [ 'date' => $date ]
		];
		try {
			$this->load->library(['ole_reward_lib']);

			$res_rem	= $this->ole_reward_lib->wager_interval_remove($dateymd, $dateymd, true);

			$res_calc	= $this->ole_reward_lib->build_daily_wagerdata($date, true);

			$res['code']	= 0;
			$res['message']	= "Wagers of {$date} is successfully rebuilt.";
			$res['success']	= true;
		}
		catch (Exception $ex) {
			$res['code']	= $ex->getCode();
			$res['message']	= $ex->getMessage();
		}
		finally {
			$this->returnJsonResult($res);
		}
	}

    /**
	 * overview: view summary report of kingrich from game logs
	 *
	 * @return rendered templete
	 */
	public function kingrich_summary_report($from = 'marketing'){
		if (!$this->permissions->checkPermissions('report_gamelogs')) {
			// if (!$this->permissions->checkPermissions('gamelogs')) {
			$this->error_access();
		} else {
			if ($from == 'report') {
				$activenav = 'report';
			} else {
				$activenav = 'marketing';
			}
			$data['showGameTree'] = $this->config->item('show_particular_game_in_tree');
			$data['kingrich_currency_branding'] = $this->utils->getConfig('kingrich_currency_branding');

			$this->loadTemplate(lang('Marketing Management'), '', '', $activenav);
			$this->load->model(array('game_type_model', 'game_logs', 'external_system', 'player_model', 'kingrich_api_logs'));

			// OGP-10782 This is not being used anymore
			// if (!$this->permissions->checkPermissions('export_report')) {
			// 	$data['export_report_permission'] = FALSE;
			// } else {
			// 	$data['export_report_permission'] = TRUE;
			// }
			if ($from == 'report') {
				$this->template->write_view('sidebar', 'report_management/sidebar', ['active' => 'view_game_logs']);
			} else {
				$this->template->write_view('sidebar', 'marketing_management/sidebar', ['active' => 'view_game_logs']);
			}
			$this->template->add_css('resources/css/collapse-style.css');
			$this->template->add_css('resources/css/jquery-checktree.css');
			$this->template->add_css('resources/floating_scroll/jquery.floatingscroll.css');
			$this->template->add_js('resources/floating_scroll/jquery.floatingscroll.js');
			$this->addJsTreeToTemplate();
			$this->template->write_view('main_content', 'marketing_management/kingrich/kingrich_summary_report', $data);
			$this->template->render();
		}
	}

	public function playcheck($username) {
		$missing_in_sbe 		= ['total' => 0];
		$mismatched 			= ['total' => 0];
		$missing_in_playcheck 	= ['total' => 0];

		$this->load->model(array('player_model','game_provider_auth'));

        $row = $this->player_model->getPlayerByUsername($username);
        if ( ! empty($row)) {
            $playerId = $row->playerId;
        }

        $gameUsername = $this->game_provider_auth->getGameUsernameByPlayerId($playerId, MG_QUICKFIRE_API);

		$this->load->library('http_utils', [
			'cookie' => "/tmp/playcheck.{$playerId}.cookie",
		]);

		$response = $this->http_utils->curl('https://playcheck22.gameassists.co.uk/playcheck/default.aspx', [
			'accounttype' => NULL,
			'adminuser' => 'kgvip.com',
			'appmode' => 'OperatorPlayCheckView',
			'clienttypeid' => NULL,
			'fbusername' => NULL,
			'hidelogin' => NULL,
			'password' => '9NgMSTZmAw',
			'serverid' => '22925',
			'ssologintype' => NULL,
			'tokentype' => NULL,
			'ul' => NULL,
			'userid' => NULL,
			'username' => $gameUsername,
			'usertoken' => NULL,
			'usertype' => NULL,
			'transactionid' => NULL,
			'returnUrl' => NULL,
			'launchtoken' => NULL,
			'accesstoken' => NULL,
		], TRUE);

		$this->http_utils->curl('https://playcheck22.gameassists.co.uk/Playcheck/Home/OperatorListSessions/100/0');
		$rows = $this->http_utils->queryXpath('//table[@id="dataTable"]/tbody/tr');

		$game_logs = [];
		foreach ($rows as $row) {

			$columns = $row->getElementsByTagName('td');
			$url = $columns->item(0)->getElementsByTagName('a')->item(0)->getAttribute('href');
			$game_id = explode('/', $url);
			$game_id = intval(end($game_id));
			$game_logs[] = [
				'game_id' => $game_id,
				'bet_amount' 	 => floatval(str_replace(',','',str_replace('$','',$columns->item(5)->textContent))),
				'win_amount' 	 => floatval(str_replace(',','',str_replace('$','',$columns->item(6)->textContent))),
				'result_amount' => floatval(str_replace(',','',str_replace('$','',$columns->item(7)->textContent))),
			];

		}

		$game_logs = array_column($game_logs, NULL, 'game_id');

		$this->load->model('game_logs');
		$start_date = date('Y-m-d 00:00:00', strtotime('-100 days'));

		$this->db->select('bet_amount, win_amount, result_amount, bet_details');
		$this->db->from('game_logs');
		$this->db->where('game_platform_id', MG_QUICKFIRE_API);
		$this->db->where('flag', Game_logs::FLAG_GAME);
		$this->db->where('player_id', $playerId);

		$this->db->where("end_at >=", $start_date);
		$this->db->where("end_at <=", date('Y-m-d 23:59:59'));

		$query = $this->db->get();

		$rows = array_map(function($row) {

			$bet_details = json_decode($row['bet_details'], TRUE);

			$row = array_merge($bet_details, $row);

			unset($row['bet_details'], $row['Created At']);

			$row['game_id'] = intval($row['game_id']);

			return $row;

		}, $query->result_array());

		$rows = array_column($rows, NULL, 'game_id');

		foreach ($rows as $game_id => $row) {

			if ( ! isset($game_logs[$game_id])) {

				$missing_in_playcheck['data'][] = $row;
				$missing_in_playcheck['total'] += $row['result_amount'];

			}

		}

		foreach ($game_logs as $game_id => $game_log) {

			if ( ! isset($rows[$game_id])) {

				$missing_in_sbe['data'][] = $game_log;
				$missing_in_sbe['total'] += $game_log['result_amount'];

			} else {

				$row = $rows[$game_id];

				if ($game_log['bet_amount'] != $row['bet_amount'] || $game_log['result_amount'] != $row['result_amount']) {
					$difference = $game_log['result_amount'] - $row['result_amount'];
					$mismatched['data'][] = [
						'difference' => $difference,
						'sbe' => $row,
						'playcheck' => $game_log,
					];
					$mismatched['total'] += $difference;
				}

			}

		}

		echo "<pre>";
		print_r([
			'playcheck' => [
				'count' => count($game_logs),
				'bet_amount' => array_sum(array_column($game_logs, 'bet_amount')),
				'win_amount' => array_sum(array_column($game_logs, 'win_amount')),
				'result_amount' => array_sum(array_column($game_logs, 'result_amount')),
			],
			'sbe' 		=> [
				'count' => count($rows),
				'bet_amount' => array_sum(array_column($rows, 'bet_amount')),
				'win_amount' => array_sum(array_column($rows, 'win_amount')),
				'result_amount' => array_sum(array_column($rows, 'result_amount')),
			],
			'missing_in_playcheck' => $missing_in_playcheck,
			'mismatched' => $mismatched,
			'missing_in_sbe' => $missing_in_sbe,
		]);
		echo "</pre>";
		die();
	}

	/**
	 * overview: view data sending scheduler of kingrich to PAGCOR/Global Com API from game logs
	 *
	 * @return rendered templete
	 */
	public function kingrich_scheduler($from = 'marketing'){
		if (!$this->permissions->checkPermissions('view_kingrich_data_scheduler')) {
			$this->error_access();
		} else {
			if ($from == 'report') {
				$activenav = 'report';
			} else {
				$activenav = 'marketing';
			}

			$this->load->model(array('kingrich_send_data_scheduler'));

			$data['kingrich_currency_branding'] = $this->utils->getConfig('kingrich_currency_branding');
			$data['kingrich_scheduler_status'] = $this->utils->getConfig('kingrich_scheduler_status');
			$data['total_active_schedule'] = $this->kingrich_send_data_scheduler->getTotalActiveSchedule();
			$data['conditions'] = $this->safeLoadParams(array(
				'by_date_from' => $this->utils->getTodayForMysql() . ' 00:00:00',
				'by_date_to' => $this->utils->getTodayForMysql() . ' 23:59:59',
				'by_currency' => '',
				'by_status' => ''
			));

			$this->loadTemplate(lang('Kingrich Scheduler'), '', '', $activenav);
			$this->load->model(array('kingrich_api_logs'));

			if ($from == 'report') {
				$this->template->write_view('sidebar', 'report_management/sidebar', ['active' => 'view_game_logs']);
			} else {
				$this->template->write_view('sidebar', 'marketing_management/sidebar', ['active' => 'view_game_logs']);
			}

			$this->template->add_css('/resources/third_party/font-awesome/v5/css/all.min.css');
			$this->addJsTreeToTemplate();
			$this->template->write_view('main_content', 'marketing_management/kingrich/kingrich_scheduler', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : Add New Item in Kingrich Scheduler
	 *
	 * detail : Add New Item in Kingrich Scheduler in Modal Mode
	 *
	 * @param N/A
	 */
	public function kingrich_load_add_item($schedule_id = null) {
		if (!$this->permissions->checkPermissions('add_kingrich_data_scheduler')) {
			$this->error_access();
		}
		$this->load->model(array('kingrich_send_data_scheduler'));
		$response = $this->kingrich_send_data_scheduler->getScheduler_data($schedule_id);

		$data['conditions'] = $this->safeLoadParams(array(
			'date_from' => (empty($schedule_id) && !isset($response['date_from'])) ? $this->utils->getTodayForMysql() . ' 00:00:00' : $response['date_from'],
			'date_to' => (empty($schedule_id) && !isset($response['date_to'])) ? $this->utils->getTodayForMysql() . ' 23:59:59' : $response['date_to'],
			'currency' => (empty($schedule_id) && !isset($response['currency'])) ? null : $response['currency']
		));
		$data['schedule_id'] = ($schedule_id) ? : null;

		$data['kingrich_currency_branding'] = $this->utils->getConfig('kingrich_currency_branding');
		$this->load->view('marketing_management/kingrich/includes/kingrich_add_item_modal', $data);
	}

	/**
	 * overview : Submit New Item in Kingrich Scheduler
	 *
	 * detail : Submit New Item in Kingrich Scheduler
	 *
	 * @param N/A
	 */
	public function kingrich_submit_add_scheduler($schedule_id = null) {
		if(empty($schedule_id)){
			if (!$this->permissions->checkPermissions('add_kingrich_data_scheduler')) {
				$this->error_access();
			}
		} else {
			if (!$this->permissions->checkPermissions('edit_kingrich_data_scheduler')) {
				$this->error_access();
			}
		}

		$this->load->model(array('kingrich_send_data_scheduler'));

			$insert_data = [
				'id' 			=> $schedule_id,
				'date_from'		=> $this->input->get_post('date_from') ,
				'date_to'		=> $this->input->get_post('date_to') ,
				'currency'		=> ($this->input->get_post('currency')) ? : null,
				'status'		=> self::PENDING,
				'created_by'	=> $this->authentication->getUsername() ,
				'created_at'	=> $this->utils->getCurrentDatetime(),
				'updated_at'	=> $this->utils->getCurrentDatetime()
			];

			if(empty($schedule_id)){
				unset($insert_data['updated_at']);
			} else {
				unset($insert_data['created_at']);
				unset($insert_data['created_by']);
			}
			$response = $this->kingrich_send_data_scheduler->insertUpdateRecord($insert_data);


		if($response){
			if(empty($schedule_id)){
				$this->saveAction(self::MANAGEMENT_TITLE, 'Added Send Data Scheduler', "User " . $this->authentication->getUsername() . " has successfully added new schedule for kingrich data sendout. Scheduler ID: ". $response);
				$this->alertMessage(1, lang('New schedule successfully added!'));
			} else {
				$this->saveAction(self::MANAGEMENT_TITLE, 'Update Send Data Scheduler', "User " . $this->authentication->getUsername() . " has successfully update schedule for kingrich data sendout. Scheduler ID: ". $schedule_id ." ." );
				$this->alertMessage(1, lang('Schedule successfully update!'));
			}

		} else {
			$this->alertMessage(2, lang('Failed to add/update schedule!'));
		}
		redirect('marketing_management/kingrich_scheduler');
	}

	/**
	 * overview : Update status Item in Kingrich Scheduler
	 *
	 * detail : Update status Item in Kingrich Scheduler
	 *
	 * @param N/A
	 */
	public function kingrich_update_status_scheduler($scheduler_id = null,$status_to = null) {
		if (!$this->permissions->checkPermissions('edit_kingrich_data_scheduler')) {
			$this->error_access();
		}

		$this->load->model(array('kingrich_send_data_scheduler'));
		$kingrich_scheduler_status = $this->utils->getConfig('kingrich_scheduler_status');
		$from_status = null;
		$current_info = $this->kingrich_send_data_scheduler->getScheduler_data($scheduler_id);

		if(!empty($current_info)){
			if(isset($current_info['status'])){
				$from_status = $kingrich_scheduler_status[$current_info['status']]['label'];
			}
		}

		$data = [
			'id'			=> $scheduler_id ,
			'status'		=> $status_to ,
		];

		$response = $this->kingrich_send_data_scheduler->updateStatus($data);

		if($response){
			$this->saveAction(self::MANAGEMENT_TITLE, 'Update Scheduler Status', "User " . $this->authentication->getUsername() . " has successfully update schedule for kingrich data sendout. Scheduler ID: ". $scheduler_id ." . From: ". $from_status ." To: ". $kingrich_scheduler_status[$status_to]['label'] ." .");
			$response = array(
				'status' => 'success',
				'msg' => lang("Successfully Update!!!"),
			);
			$this->alertMessage(1, lang('Update status schedule successfully!'));
		} else {
			$response = array(
				'status' => 'error',
				'msg' => 'Error Occured',
			);
			$this->alertMessage(2, lang('Failed to update schedule status!'));
		}

		if ($this->input->is_ajax_request()) {
			$this->returnJsonResult($response);
			return;
		} else {
			redirect('marketing_management/kingrich_scheduler');
		}
	}

	/**
	 * overview : Add New Item in Kingrich Scheduler
	 *
	 * detail : Add New Item in Kingrich Scheduler in Modal Mode
	 *
	 * @param N/A
	 */
	public function kingrich_load_scheduler_logs($schedule_id = null) {

		$this->load->model(array('kingrich_scheduler_logs'));
		$data['scheduler_logs'] = $this->kingrich_scheduler_logs->getRecordsBySchedulerId($schedule_id);
		$data['grand_total'] = $this->kingrich_scheduler_logs->getGrandTotalBySchedulerId($schedule_id);
		$data['schedule_id'] = ($schedule_id) ? : null;

		$data['kingrich_currency_branding'] = $this->utils->getConfig('kingrich_currency_branding');
		$this->loadTemplate(lang('Marketing Management'), '', '', '');
		$this->load->view('marketing_management/kingrich/includes/kingrich_scheduler_logs', $data);
	}
}

/* End of file marketing_management.php */
/* Location: ./application/controllers/marketing_management.php */
