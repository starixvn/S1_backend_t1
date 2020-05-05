<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';

require_once dirname(__FILE__) . '/modules/player_log_module.php';
require_once dirname(__FILE__) . '/modules/wallet_module.php';
require_once dirname(__FILE__) . '/kyc_status.php';
require_once dirname(__FILE__) . '/risk_score.php';
require_once dirname(__FILE__) . '/allowed_withdrawal_status.php';
require_once dirname(__FILE__) . '/modules/linked_account_module.php';
require_once dirname(__FILE__) . '/modules/player_cashback_module.php';

/**
 * Defines general behavior of game API classes.
 *
 * General behaviors include:
 * * View Player List
 * * Loading player template
 * * Searching of player
 * * Add Player
 * * Adjust Player Level
 * * Posting Note
 * * Tagging Player
 * * List of Black list Players
 * * Lock/Unlock Player
 * * Getting all player levels
 * * Message History
 * * Payment History
 * * Creating Game provider account
 * * Add/update/delete Tags
 * * Filtered Tags Lists
 * * Able to delete record by batch
 * *
 *
 * @see Redirect redirect to searchAllPlayer page
 *
 * @category player_management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 *
 * @property Player_responsible_gaming_library $player_responsible_gaming_library
 */
class Player_Management extends BaseController {

	use player_log_module;
	use wallet_module;
	use kyc_status;
	use risk_score;
	use allowed_withdrawal_status;
	use linked_account_module;
	use player_cashback_module;

	const ACTION_MANAGEMENT_TITLE = 'Player Management';
	const DEFAULT_VALUE_VERIFIED_EMAIL = 0;
	const DEFAULT_VALUE_VERIFIED_PHONE = 0;
	const zero_total = 0;

	const A = 1;
	const B = 2;
	const C = 3;
	const D = 4;

	const player_no_attached_document = "player_no_attached_document";
	const player_depositor = "player_depositor";
	const player_identity_verification = "player_identity_verification";
	const player_valid_documents = "player_valid_documents";
	const player_valid_identity_and_proof_of_address = "player_valid_identity_and_proof_of_address";

	const Settled = 5;

	const DEFAULT_BANKACCOUNT_LENGTH = 120;

    /* @var Player_responsible_gaming_library $player_responsible_gaming_library */
    public $player_responsible_gaming_library;

	function __construct() {
		parent::__construct();
		$this->load->helper(array('date_helper', 'player_helper', 'url'));
		$this->load->library(array('authentication', 'form_validation', 'template', 'pagination', 'permissions', 'player_manager', 'report_functions', 'payment_manager', 'cs_manager', 'salt', 'email', 'game_platform/game_platform_manager', 'game_functions', 'sms/sms_sender', 'gbg_api','player_responsible_gaming_library', 'xinyanapi'));
		$this->load->library('excel');
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
		$this->template->add_js('resources/js/player_management/player_management.js');
		$this->template->add_js('resources/js/datatables.min.js');
		$this->template->add_css('resources/css/general/style.css');
		$this->template->add_css('resources/css/general/fontawesome/build.css');
		$this->template->add_css('resources/css/datatables.min.css');
		$this->template->add_js('resources/js/select2.min.js');
		$this->template->add_css('resources/css/select2.min.css');
		$this->template->add_css('resources/css/player_management/style.css');
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
	private function error_access() {
		$this->loadTemplate('Player Management', '', '', 'player');
		$playerUrl = $this->utils->activePlayerSidebar();
		$data['redirect'] = $playerUrl;

		$message = lang('con.plm01');
		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		$this->template->write_view('main_content', 'error_page', $data);
		$this->template->render();
	}

	/**
	 * overview : index page for player management
	 *
	 * detail : redirect to method viewAllPlayer
	 *
	 */
	public function index() {
		redirect('player_management/viewAllPlayer');
	}

	/**
	 * overview : list of players
	 *
	 * detail : @return	rendered Template
	 *
	 * @param null $offset
	 * @param null $limit
	 */
	public function viewAllPlayer($offset = null, $limit = null) {
		return redirect('player_management/searchAllPlayer?search_reg_date=on');
	}

	/**
	 * overview : search all players
	 *
	 * details : display player information
	 *
	 */
	public function searchAllPlayer() {
		if (!$this->permissions->checkPermissions('player_list')) {
			$this->error_access();
		} else {
		$this->load->model(array('external_system', 'affiliatemodel', 'agency_model', 'group_level', 'promorules', 'transactions'));

		$data['levels'] = $this->group_level->getAllPlayerLevelsDropdown( lang('All') );
		$only_master = false;
		$ordered_by_name = true;
		$data['affiliates'] = $this->affiliatemodel->getAllActivtedAffiliates($only_master, $ordered_by_name);
		$data['agents'] = $this->agency_model->get_active_agents($only_master, $ordered_by_name);
		$data['promo'] = $this->promorules->getAllPromoInfo();
		$data['game_platforms'] = $this->external_system->getAllActiveSytemGameApi();
		$data['selected_tags'] =$a= $this->input->get_post('tag_list');
		$data['tags'] =$b= $this->player->getAllTagsOnly();

		$this->load->library('affiliate_manager');


		$tags = $this->affiliate_manager->getAllTags('tagName', null, null);
		$tags_kv=[];
		if(!empty($tags)){
			foreach ($tags as $tag) {
				$tags_kv[$tag['tagId']] = $tag['tagName'];
			}
		}
		$data['affiliate_tags'][''] = lang('All Affiliate Tags');
		$data['affiliate_tags'] += $tags_kv;

		$dashboard = $this->transactions->getDashboard();
		$dashboard_stats = $this->utils->array_select_fields($dashboard, [ 'total_all_balance_include_subwallet', 'total_bet_amount_all_time', 'total_deposit_amount_all_time', 'total_withdraw_amount_all_time' ]);
		$data = array_merge($data, $dashboard_stats);

		$error = $this->session->flashdata('error_message');
		if ($error) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $error);
		}

		/// Patch for OGP-14711 : Modify new layout of the "Player List": SBE_Player > All Player
		// Default: a. enable checkbox. b. current date.
		$start_today = date("Y-m-d") . ' 00:00:00';
		$end_today = date("Y-m-d") . ' 23:59:59';
		$search_reg_date_default = 'on'; // Default: a. enable checkbox.

		$legal_age = $this->utils->getConfig('legal_age') ?: 18;

		$with_year_default = '0'; // 1:with year , 0:without year.
		$dateForamtted = 'm-d';
		$dob_default = date($dateForamtted, strtotime("-{$legal_age} years"));


		$conditionsDefault = array(
			'registration_date_from' => $start_today,
			'registration_date_to' => $end_today,
            'last_login_date_from' => $start_today,
			'last_login_date_to' => $end_today,
			'search_by' => '2',
			'reg_web_search_by' => '2', // Patch for OGP-14711, Registered Website, Remove radio button "Similar", "Exact" and set the searching as exact search.
			'username' => '',
            'id_card_number' => '',
			'game_username' => '',
			'search_reg_date' => $search_reg_date_default,
			'search_last_log_date' => '',
			'player_level' => '',
			'registration_website' => '',
			'registered_by' => '',
			'friend_referral_code' => '',
			'im_account' => '',
			'city' => '',
			'residentCountry' => '',
			'deposit' => '',
			'blocked' => '',
			'first_name' => '',
			'last_name' => '',
			'email' => '',
			'contactNumber' => '',
			'ip_address' => '',
			'device' => '',
			'affiliate' => '',
			'phone_status' => '',
			'email_status' => '',
			'tag_list' => array(),
			'blocked_gaming_networks' => '',
			'promoCode' => '',
			'promo' => '',
			'wallet_order' => '',
			'no_affiliate' => '',
			'aff_include_all_downlines' => '',
			'gspa' => '',
			'wallet_amount_from' => '',
			'wallet_amount_to' => '',
			'agent_name' => '',
			'own_downline_or_agency_line' => 'own_downline', //own_downline or agency_line
			'include_all_downlines' => '',
			'created_on_agency' => 'on',
			'affiliate_tag_id' => '',
			'player_bank_account_number' => '',
			'lastLoginIp' => '',
			'allowed_withdrawal_status' => '',
			'referred_by' => '',
			'deposit_count' => '', // deposit input
			'deposit' => '', // deposit select
			// dob
			'with_year' => $with_year_default, // 1:with year , 0:without year.
			'dob_from' => $dob_default ,
			'dob_to' => $dob_default ,
			/// Patch for OGP-15254 - remove checkbox of DOB
			'fields_search_dob' => '' , // for catch request.
			'fields_search_dob_without_year' => '' , // for catch request.
			// total_balance
			'total_balance_more_than' => '',
			'total_balance_less_than' => '',
		);
		$data['conditionsDefault'] = $conditionsDefault;

		/// Patch for OGP-15254 - remove checkbox of DOB
		$params = $this->safeLoadParams($conditionsDefault);
		if( ! empty( $params['fields_search_dob']) ){

			// fields_search_dob_without_year convert to dob_from and dob_to
			// fields_search_dob convert to dob_from and dob_to
			if( ! empty($params['with_year']) ){
				// without year
				$fields_search_dob = explode(' ', $params['fields_search_dob']);
				$params['dob_from'] = $fields_search_dob[0];
				$params['dob_to'] = $fields_search_dob[count($fields_search_dob) -1 ];
			}else{
				$fields_search_dob_without_year = explode(' ', $params['fields_search_dob_without_year']);
				$params['dob_from'] = $fields_search_dob_without_year[0];
				$params['dob_to'] = $fields_search_dob_without_year[count($fields_search_dob_without_year) -1 ];
			}
		}

		$data['conditions'] = $params;

		$this->safeGetParam('search_by', '1');

		$this->utils->debug_log('search_by', var_export($this->input->post('search_by'), true), var_export($this->safeGetParam('search_by', '1'), true));
		$this->utils->debug_log('conditions', $data['conditions']);

		/// Patch for OGP-13367 "All Player" search function need to allow search DOB without year
		$data['isValidDate4WithYear'] = null; // default
		if( $data['conditions']['dob_to'] ){
			$data['isValidDate4WithYear'] = ! self::verifyDate($data['conditions']['dob_to'], 'm-d');
		}
		$data['setWithYear4Default'] = false; // switch WithoutYear/WithYear maybe default.
		if( ! empty($this->safeGetParam('fields_search_dob')) ){ /// Patch for OGP-15254 - remove checkbox of DOB
			$data['setWithYear4Default'] = $data['isValidDate4WithYear']; // setup after search.
		}
		$this->template->add_css('resources/css/select2.min.css');
		$this->template->add_js('resources/js/select2.full.min.js');
		$this->template->add_css('resources/floating_scroll/jquery.floatingscroll.css');
		$this->template->add_js('resources/floating_scroll/jquery.floatingscroll.js');
		$this->template->add_css('resources/third_party/bootstrap-multiselect-master/dist/css/bootstrap-multiselect.css');
        $this->template->add_js('resources/third_party/bootstrap-multiselect-master/dist/js/bootstrap-multiselect.js');
		$this->template->add_js('resources/js/bootstrap-notify.min.js');
		$this->loadTemplate('Player Management', '', '', 'player');
		$this->template->write_view('sidebar', 'player_management/sidebar');
		$this->template->write_view('main_content', 'player_management/player_list', $data);
		$this->template->render();
		}
	} // EOF searchAllPlayer

	/**
	 * Verify valid date using PHP's DateTime class
	 *
	 * Ref. to https://stackoverflow.com/a/14505065
	 * @param string $date The date string.
	 * @param string $format The date format for param, $date.
	 * @param boolean $strict True for valid date, else false.
	 */
	static public function verifyDate($date, $format = 'm/d/Y', $strict = true) {
		$dateTime = DateTime::createFromFormat($format, $date);
		if ($strict) {
			$errors = DateTime::getLastErrors();
			if (!empty($errors['warning_count'])) {
				return false;
			}
		}
		return $dateTime !== false;
	} // EOF verifyDate


	# END PLAYER LIST ##############################

	/**
	 * overview : export username
	 */
	public function exportUsernames() {
		if (!$this->permissions->checkPermissions('export_all_username')) {
			$this->error_access();
		} else {
			ini_set('memory_limit', -1);
			$params = array(
				'deleted_at' => null,
			);
			$data = $this->player_model->getAllUsernames($params);
			$data = implode("\n", array_column($data, 'username'));
			$this->output->set_content_type('application/csv')
				->set_output($data);
		}
	}

	public function testDepositHistory() {
		$this->load->model('player_model');
		$this->player_model->getDepositHistory();
	}

	/**
	 * setPlayerListColumn
	 *
	 * @return	rendered Template
	 */
	public function setPlayerListColumn() {
		//for columns
		$name = "checked";
		$level = "checked";
		$email = "checked";
		$country = "unchecked";
		$last_login_time = "checked";
		$tag = "checked";
		$status_col = "checked";
		$registered_on = "checked";
		$registered_by = "checked";

		$data = array(
			'name' => $name,
			'level' => $level,
			'email' => $email,
			'country' => $country,
			'last_login_time' => $last_login_time,
			'tag' => $tag,
			'status_col' => $status_col,
			'registered_on' => $registered_on,
			'registered_by' => $registered_by,
		);
		$this->session->set_userdata($data);
	}

	/**
	 * View Page for Player Details
	 *
	 * @return	rendered Template
	 */
	public function viewPlayerDetails() {
		if (!$this->permissions->checkPermissions('player_list')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Player Management', '', '', 'player');
			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/view_player_details');
			$this->template->render();
		}
	}

	/**
	 * View Page for Add Player
	 *
	 * @return	rendered Template
	 */
	public function viewAddPlayer() {
		if (!$this->permissions->checkPermissions('add_player')) {
			$this->error_access();
		} else {
			$username = '';
			$data['hiddenPassword'] = $this->user_functions->randomizer($username);
			$this->loadTemplate('Player Management', '', '', 'player');
			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/view_add_player', $data);
			$this->template->render();
		}
	}

	/**
	 * Validates and verifies inputs from
	 * the end user and add a player
	 *
	 * @return	redirect page
	 */
	public function postAddPlayer() {
		if (!$this->permissions->checkPermissions('add_player')) {
			$this->error_access();
		} else {
			$checkRandomPassword = $this->input->post('randomPassword');
			$tags = $this->input->post('tags');
			$password = '';

			if (!$checkRandomPassword) {
				$this->form_validation->set_rules('password', lang('player.mp07'), 'trim|required|xss_clean|max_length[34]');
				$this->form_validation->set_rules('cpassword', lang('reg.07'), 'trim|required|xss_clean|max_length[34]|matches[password]');
				$password = $this->input->post('password');
			} else {
				$password = $this->input->post('hiddenPassword');
			}

			$this->form_validation->set_rules('username', lang('reg.03'), 'trim|required|xss_clean|max_length[20]');
			$this->form_validation->set_rules('realname', lang('sys.vu19'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('email', lang('lang.email'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('gender', lang('player.57'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('birthday', lang('player.17'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('phone', lang('a_reg.21'), 'trim|xss_clean');
			$this->form_validation->set_rules('mobile_phone', lang('a_reg.21'), 'trim|xss_clean');
			$this->form_validation->set_rules('tag_description', lang('player.tm04'), 'trim|xss_clean');

			if ($this->form_validation->run() == false) {
				$message = lang('con.plm02');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				$this->viewAddPlayer();
			} else {
				$username = $this->input->post('username');
				$email = $this->input->post('email');

				$checkUsernameExist = $this->player_manager->checkUsernameExist($username);
				$checkEmailExist = $this->player_manager->checkEmailExist($email);

				if ($checkUsernameExist) {
					$message = lang('con.plm03') . " <b>" . $username . "</b> " . lang('con.plm04');
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
					redirect('player_management/viewAddPlayer');
				} elseif ($checkEmailExist) {
					$message = lang('con.plm05') . " <b>" . $email . "</b> " . lang('con.plm06');
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
					redirect('player_management/viewAddPlayer');
				} else {
					$realname = $this->input->post('realname');
					$gender = $this->input->post('gender');
					$birthday = $this->input->post('birthday');
					$phone = $this->input->post('phone');
					$mobile_phone = $this->input->post('mobile_phone');
					$today = date("Y-m-d H:i:s");

					$data = array(
						'username' => $username,
						'email' => $email,
						'password' => $password,
						'createdOn' => $today,
					);

					$this->player_manager->insertPlayer($data);

					$player = $this->player_manager->checkUsernameExist($username);

					$data = array(
						'playerId' => $player['playerId'],
						'realName' => $realname,
						'gender' => $gender,
						'birthday' => $birthday,
						'phone' => $phone,
						'mobilePhone' => $mobile_phone,
					);

					$this->player_manager->insertPlayerDetails($data);

					$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Add Player', "User " . $this->authentication->getUsername() . " added new Player Account");

					$message = lang('con.plm07') . " <b>" . $username . "</b> " . lang('con.plm08');
					$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
					$this->index();
				}
			}
		}
	}

	/**
	 * overview : player overview
	 *
	 * detail : load player overview including deposit information
	 *
	 * @param int $player_id
	 */
	public function overview($player_id) {
		$data['playerId'] = $player_id;
		$data['tag'] = $this->player_manager->getPlayerTag($player_id);
		$data['player'] = $this->player_manager->getPlayerById($player_id);
		$data['total_deposits'] = $this->player_manager->getPlayerTotalDeposits($data['player']['playerAccountId']);

		$this->load->view('player_management/overview', $data);
	}

	/**
	 * overview : adjust player overview
	 *
	 * detail : load view for adjust player level
	 *
	 * @param int $player_id
	 */
	public function adjustplayerlevel($player_id) {
		$data['playerId'] = $player_id;
		$data['player'] = $this->player_manager->getPlayerById($player_id);
		$data['total_deposits'] = $this->player_manager->getPlayerTotalDeposits($data['player']['playerAccountId']);
		$data['playerCurrentLevel'] = $this->player_manager->getPlayerCurrentLevel($player_id);
		$data['allLevels'] = $this->player_manager->getAllPlayerLevels();
		$this->load->view('player_management/adjustplayerlevel', $data);
	}

	/**
	 * overview : player detail
	 *
	 * detail : will load view for player detail
	 *
	 * @param int $player_id
	 */
	public function playerDetail($player_id) {
		$data['playerId'] = $player_id;
		$data['player'] = $this->player_manager->getPlayerById($player_id);

		$this->load->view('player_management/player_detail', $data);
	}

	/**
	 * overview : player account detail
	 *
	 * detail : will load view for account detail
	 *
	 * @param int $player_id
	 */
	public function accountDetail($player_id) {
		$data['playerId'] = $player_id;
		$data['player'] = $this->player_manager->getPlayerAccount($player_id);
		$data['ranking_settings'] = $this->player_manager->getRankingLevelSettingsDetail($data['player']['playerLevel']);
		$data['subwallet'] = $this->payment_manager->getAllPlayerAccountByPlayerId($player_id);

		$this->load->view('player_management/account_detail', $data);
	}

	/**
	 * overview : system detail
	 *
	 * detail : will load view for system detail
	 *
	 * @param $player_id
	 */
	public function systemDetail($player_id) {
		$data['playerId'] = $player_id;
		$data['player'] = $this->player_manager->getPlayerById($player_id);
		$data['friend_referral'] = $this->player_manager->getReferralByPlayerId($player_id);

		$this->load->view('player_management/system_detail', $data);
	}

	/**
	 * overview : notes
	 *
	 * detail : will load view for notes
	 *
	 * @param int $player_id
	 */
	public function notes($player_id) {
		$data['playerId'] = $player_id;
		$data['player'] = $this->player_manager->getPlayerNotes($player_id);

		$this->load->view('player_management/notes', $data);
	}

	/**
	 * overview : add notes
	 *
	 * details : will load view for add notes
	 *
	 * @param int $player_id
	 */
	public function addNotes($player_id) {
		if (!$this->permissions->checkPermissions('add_notes')) {
			$this->error_access();
		} else {
			$data['playerId'] = $player_id;

			$this->load->view('player_management/add_note', $data);
		}
	}

	/**
	 * overview : update note
	 *
	 * detail : will load view for edit note
	 *
	 * @param string $note_id
	 * @param int $player_id
	 */
	public function editNote($note_id, $player_id) {
		if (!$this->permissions->checkPermissions('edit_notes')) {
			$this->error_access();
		} else {
			$data['playerId'] = $player_id;
			$data['note'] = $this->player_manager->getNoteById($note_id);

			$this->load->view('player_management/edit_note', $data);
		}
	}

	/**
	 * overview : post add note
	 *
	 * detail : validates and verifies input of the end user and add a note
	 *
	 * @param int $player_id player id
	 */
	public function postAddNote($player_id) {
		if (!$this->permissions->checkPermissions('edit_notes')) {
			$this->error_access();
		} else {
			$this->form_validation->set_rules('note', lang('sys.gd11'), 'trim|required|xss_clean');

			if ($this->form_validation->run() == false) {
				$message = lang('con.plm09');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			} else {
				$note = $this->input->post('note');
				$user_id = $this->authentication->getUserId();
				$today = date("Y-m-d H:i:s");

				$data = array(
					'playerId' => $player_id,
					'notes' => $note,
					'userId' => $user_id,
					'createdOn' => $today,
					'updatedOn' => $today,
				);

				$this->player_manager->insertPlayerNote($data);

				$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Add Note for Player', "User " . $this->authentication->getUsername() . " has added new note to player");

				$message = lang('con.plm10');
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			}
			redirect('player_management/viewAllPlayer');
		}
	}

	/**
	 * overview :delete note
	 *
	 * detail : validates and verifies inputs of the end user and delete a note
	 *
	 * @param int $player_id 		player id
	 * @param string $note_id		note id
	 */
	public function deleteNote($player_id, $note_id) {
		if (!$this->permissions->checkPermissions('delete_notes')) {
			$this->error_access();
		} else {
			$note = $this->player_manager->getNoteById($note_id);
			$user_id = $this->authentication->getUserId();

			if ($user_id != $note['userId']) {
				$message = lang('con.plm58');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			} else {
				$this->player_manager->deleteNote($user_id, $note_id);

				$message = lang('con.plm11');
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			}

			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Delete Note for Player', "User " . $this->authentication->getUsername() . " has deleted note to player");

			redirect('player_management/viewAllPlayer');
		}
	}

	/**
	 * overview : edit note
	 *
	 * detail : validates and verifies inputs of the end user and edit a note
	 *
	 * @param int $player_id 		player id
	 * @param string $note_id		note id
	 */
	public function postEditNote($player_id, $note_id) {
		if (!$this->permissions->checkPermissions('edit_notes')) {
			$this->error_access();
		} else {
			$note = $this->player_manager->getNoteById($note_id);
			$user_id = $this->authentication->getUserId();

			$this->form_validation->set_rules('note', lang('sys.gd11'), 'trim|required|xss_clean');

			if ($this->form_validation->run() == false) {
				$message = lang('con.plm02');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			} else {
				if ($user_id != $note['userId']) {
					$message = lang('con.plm12');
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				} else {

					$note = $this->input->post('note');
					$user_id = $this->authentication->getUserId();
					$today = date("Y-m-d H:i:s");

					$data = array(
						'notes' => $note,
						'updatedOn' => $today,
					);

					$this->player_manager->editNote($user_id, $note_id, $data);

					$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Edit Note for Player', "User " . $this->authentication->getUsername() . " has edited note to player");

					$message = lang('con.plm13');
					$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
				}
			}
			redirect('player_management/viewAllPlayer');
		}
	}

	/**
	 * overview : player tag
	 *
	 * detail : will load view for tag
	 *
	 * @param $player_id	player_id
	 * @param null $page
	 */
	public function playerTag($player_id, $page = null) {
		if (!$this->permissions->checkPermissions('tag_player')) {
			$this->error_access();
		} else {
			$data['playerId'] = $player_id;
			$data['player_tag'] = $this->player_manager->getPlayerTag($player_id);
			$data['tags'] = $this->player_manager->getAllTags();
			$data['page'] = $page;
			$data['check'] = $this->player_manager->getPlayerTag($player_id);
			$data['player'] = $this->player_manager->getPlayerById($player_id);
			$this->load->view('player_management/player_tag', $data);
		}
	}

	/**
	 * overview : edit tag
	 *
	 * detail : validates and verifies input of the end user and edit a tag
	 *
	 * @param string $player_id		player id
	 * @param null $page
	 */
	public function postEditTag($player_id, $page = null) {
		if (!$this->permissions->checkPermissions('tag_player')) {
			$this->error_access();
		} else {
			if (empty($this->input->post('remove_tag'))) {
				$this->form_validation->set_rules('tags', lang('aff.al25'), 'trim|required|xss_clean');
			} else {
				$this->form_validation->set_rules('remove_tag', lang('aff.a150'), 'trim|required|xss_clean');
			}

			if ($this->form_validation->run() == false) {
				$message = lang('con.plm14');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				$this->viewAllPlayer();
			} else {
				if (empty($this->input->post('remove_tag'))) {
					$tags = $this->input->post('tags');
					$user_id = $this->authentication->getUserId();
					$today = date("Y-m-d H:i:s");

					$check = $this->player_manager->getPlayerTag($player_id);
					if ($tags == 'Others') {
						$this->form_validation->set_rules('specified_tag', lang('player.41'), 'trim|required|xss_clean');
						$this->form_validation->set_rules('description', lang('player.41'), 'trim|required|xss_clean');

						if ($this->form_validation->run() == false) {
							$message = lang('con.plm15');
							$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
						} else {
							$specified_tag = $this->input->post('specified_tag');
							$description = $this->input->post('description');

							$isTagExist = $this->player_manager->getPlayerTagByName($specified_tag);
							if ($isTagExist) {
								$message = lang('con.plm16');
								$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
							} else {
								$data = array(
									'tagName' => ucfirst($specified_tag),
									'tagDescription' => $description,
									'createBy' => $user_id,
									'status' => 1,
									'createdOn' => $today,
									'updatedOn' => $today,
								);

								$this->player_manager->insertTag($data);

								$tag = $this->player_manager->getPlayerTagByName($specified_tag);
								if (!$check) {
									$data = array(
										'playerId' => $player_id,
										'taggerId' => $user_id,
										'tagId' => $tag['tagId'],
										'status' => 1,
										'createdOn' => $today,
										'updatedOn' => $today,
									);

									$this->player_manager->insertPlayerTag($data);

									$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Edit Tag for Player', "User " . $this->authentication->getUsername() . " has edited Tag to player");

									$message = lang('con.plm17');
									$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
								} else {
									$data = array(
										'tagId' => $tag['tagId'],
										'updatedOn' => $today,
									);
									$this->player_manager->changeTag($check['playerId'], $data);

									$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Edit Tag for Player', "User " . $this->authentication->getUsername() . " has edited Tag to player");

									$message = lang('con.plm17');
									$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
								}
							}
						}
					} elseif (!$check) {
						$data = array(
							'playerId' => $player_id,
							'taggerId' => $user_id,
							'tagId' => $tags,
							'status' => 1,
							'createdOn' => $today,
							'updatedOn' => $today,
						);

						$this->player_manager->insertPlayerTag($data);

						$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Edit Tag for Player', "User " . $this->authentication->getUsername() . " has edited Tag to player");

						$message = lang('con.plm17');
						$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
					} else {
						$data = array(
							'tagId' => $tags,
							'updatedOn' => $today,
						);
						$this->player_manager->changeTag($check['playerId'], $data);

						$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Edit Tag for Player', "User " . $this->authentication->getUsername() . " has edited Tag to player");

						$message = lang('con.plm17');
						$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
					}

					$this->savePlayerUpdateLog($player_id, lang('player.tl04') . ' ' . lang('lang.player'), $this->authentication->getUsername()); // Add log in playerupdatehistory
				} else {
					$this->player_manager->deletePlayerTagByPlayerId($player_id);
					$message = lang('con.plm18');
					$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);

					$this->savePlayerUpdateLog($player_id, lang('player.tp10'), $this->authentication->getUsername()); // Add log in playerupdatehistory
				}

				switch ($page) {
				case 'vipplayer':
					redirect('player_management/vipPlayer', 'refresh');
					break;

				case 'taggedlist':
					redirect('player_management/taggedlist', 'refresh');
					break;

				default:
					redirect('player_management/viewAllPlayer', 'refresh');
					break;
				}

			}
		}
	}

	/**
	 * overview : tagged player list
	 *
	 * detail : view page for black list
	 */
	public function taggedlist() {
		if (!$this->permissions->checkPermissions(['taggedlist','tag_player'])) {
			$this->error_access();
		} else {
			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			$this->loadTemplate('Player Management', '', '', 'player');

			$tags_all = $this->player->getAllTags();
			$tags = [];
			if(!empty($tags_all)){
				foreach ($tags_all as $tag) {
					$tags[$tag['tagId']] = $tag['tagName'];
				}
			}

			$data['tags']  = $tags;
			// $data['today'] = date("Y-m-d H:i:s");
			$data['date_to']   = date('c', strtotime('today 23:59:59'));
			$data['date_from'] = date('c', strtotime('-6 day 00:00'));
			$data['last_update_to']   = date('c', strtotime('today 23:59:59'));
			$data['last_update_from'] = date('c', strtotime('-6 day 00:00'));

			$search_tag = $this->input->get('tag');
			$search_reg_date = $this->input->get('search_reg_date');
			$search_reg_date = (empty($search_reg_date)) ? 'true' : strtolower($search_reg_date);

			$data['search_tag'] = $search_tag;
			$data['search_reg_date'] = $search_reg_date;

			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/view_tagged_list', $data);
			$this->template->render();

		}
	}

	/**
	 * overview : post blacklist
	 *
	 * detail : sorting of black list
	 */
	public function postBlackSortPage() {
		$black_sort_by = $this->input->post('sort_by');
		$this->session->set_userdata('black_sort_by', $black_sort_by);

		$black_in = $this->input->post('in');
		$this->session->set_userdata('black_in', $black_in);

		$black_number_player_list = $this->input->post('number_player_list');
		$this->session->set_userdata('black_number_player_list', $black_number_player_list);

		redirect('player_management/taggedlist');
	}

	/**
	 * overview : post blacklist
	 *
	 * detail : change columns for blacklist
	 */
	public function postBlackChangeColumns() {
		$black_name = $this->input->post('name') ? "checked" : "unchecked";
		$black_level = $this->input->post('level') ? "checked" : "unchecked";
		$black_email = $this->input->post('email') ? "checked" : "unchecked";
		$black_country = $this->input->post('country') ? "checked" : "unchecked";
		$black_last_login_time = $this->input->post('last_login_time') ? "checked" : "unchecked";
		$black_tag = $this->input->post('tag') ? "checked" : "unchecked";
		$black_status_col = $this->input->post('status_col') ? "checked" : "unchecked";
		$black_registered_on = $this->input->post('registered_on') ? "checked" : "unchecked";

		$data = array(
			'black_name' => $black_name,
			'black_level' => $black_level,
			'black_email' => $black_email,
			'black_country' => $black_country,
			'black_last_login_time' => $black_last_login_time,
			'black_tag' => $black_tag,
			'black_status_col' => $black_status_col,
			'black_registered_on' => $black_registered_on,
		);
		$this->session->set_userdata($data);
		redirect('player_management/taggedlist');
	}

	/**
	 * overview : get blacklist pages
	 *
	 * detail : pagination of page of black list
	 *
	 * @param string $segment	segmet
	 */
	public function getBlacklistPages($segment = '') {
		$black_number_player_list = '';
		$black_sort_by = '';
		$black_in = '';

		if ($this->session->userdata('black_number_player_list')) {
			$black_number_player_list = $this->session->userdata('black_number_player_list');
		} else {
			$black_number_player_list = 5;
		}

		if ($this->session->userdata('black_sort_by')) {
			$black_sort_by = $this->session->userdata('black_sort_by');
		} else {
			$black_sort_by = 'createdOn';
		}

		if ($this->session->userdata('black_in')) {
			$black_in = $this->session->userdata('black_in');
		} else {
			$black_in = 'desc';
		}

		$this->loadTemplate('Player Management', '', '', 'player');

		$data['count_all'] = count($this->player_manager->getBlacklist($black_sort_by, $black_in, null, null));
		$config['base_url'] = "javascript:get_blacklist_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = $black_number_player_list;
		$config['num_links'] = '1';

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
		$data['players'] = $this->player_manager->getBlacklist($black_sort_by, $black_in, $config['per_page'], $segment);

		$data['current_page'] = floor(($segment / $config['per_page']) + 1);
		$data['today'] = date("Y-m-d H:i:s");

		$data['games'] = $this->player_manager->getAllGames();

		$this->load->view('player_management/ajax_player_list_blacklist', $data);
	}

	/**
	 * overview : friend referal monthly earning report
	 * detail : list and searching of player friend referral monthly earning report
	 * add by spencer.kuo
	 */
	public function viewFriendReferralMonthlyEarnings() {
		$this->load->model(array('player_earning'));

		// OGP-10782 This is not being used anymore
		// if (!$this->permissions->checkPermissions('export_report')) {
		// 	$data['export_report_permission'] = FALSE;
		// } else {
		// 	$data['export_report_permission'] = TRUE;
		// }

		$data['conditions'] = $this->safeLoadParams(array(
			'year_month' => '',
			'player_username' => '',
			'paid_flag' => '',
		));
		$data['flag_list'] = array(
			'' => '------' . lang('N/A') . '------',
			true => lang('Paid'),
			false => lang('Unpaid'),
		);
		$data['year_month_list'] = $this->player_earning->getYearMonthListToNow();
		$this->loadTemplate('Player Management', '', '', 'player');
		$this->template->write_view('sidebar', 'player_management/sidebar');
		$this->template->write_view('main_content', 'player_management/earning/view_friend_referral_monthly_earning', $data);
		$this->template->render();
	}

	/**
	 * overview : friend referal
	 *
	 * detail : list and searching of user friend referral
	 *
	 */
	public function friendReferral($player_id = null) {
		if (!$this->permissions->checkPermissions('friend_referral_player')) {
			$this->error_access();
		} else {
			$this->load->model(array('roles', 'player_model'));
			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active', 'system_crumb' => 'active'));
			}

			$data = array();
			if (!empty($player_id)) {
				$data['player'] = $this->player_model->getPlayerArrayById($player_id);
			}



			$this->template->add_js('resources/js/bootstrap-confirmation.js');
			$this->loadTemplate('Player Management', '', '', 'player');
			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/friend_referral', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : check referred player by player
	 *
	 * detail : loaded view for referals
	 *
	 * @param int $player_id	player id
	 */
	public function referred($player_id) {
		if (!$this->permissions->checkPermissions('friend_referral')) {
			$this->error_access();
		} else {
			$data['player'] = $this->player_manager->getPlayerById($player_id);

			$data['referred'] = $this->player_manager->getReferralByPlayerId($player_id);

			$this->load->view('player_management/referred', $data);
		}
	}

	/**
	 * overview : batch create
	 *
	 * detail : add, updating, deleting and list of batch create
	 */
	public function accountProcess() {
		if (!$this->permissions->checkPermissions('account_process')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Player Management', '', '', 'player');

			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			$data['batch'] = $this->player_manager->getBatchAccount(null, null);

			$this->template->add_js('resources/js/strength.min.js');
			$this->template->add_css('resources/css/strength.css');
			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/view_account_process_list', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : get account proccess pages
	 *
	 * detail : pagination page of account process
	 *
	 * @param string $segment	segment
	 */
	public function getAccountProcessPages($segment = '') {
		$data['count_all'] = count($this->player_manager->getBatchAccount(null, null));
		$config['base_url'] = "javascript:get_account_process_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '5';
		$config['num_links'] = '1';

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
		$data['batch'] = $this->player_manager->getBatchAccount($config['per_page'], $segment);

		$this->load->view('player_management/ajax_account_process_list', $data);
	}

	/**
	 * overview : account process
	 *
	 * detail : search page of account process
	 *
	 * @param string $search	string
	 */
	public function searchAccountProcessList($search = '') {
		$data['count_all'] = count($this->player_manager->searchAccountProcessList($search, null, null));
		$config['base_url'] = "javascript:get_account_process_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '5';
		$config['num_links'] = '1';

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
		$data['batch'] = $this->player_manager->searchAccountProcessList($search, $config['per_page'], null);

		$this->load->view('player_management/ajax_account_process_list', $data);
	}

	/**
	 * overview : account process list
	 *
	 * detail : sort page for account process
	 *
	 * @param $sort sort
	 */
	public function sortAccountProcessList($sort) {
		$data['count_all'] = count($this->player_manager->sortAccountProcessList($sort, null, null));
		$config['base_url'] = "javascript:get_account_process_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '5';
		$config['num_links'] = '1';

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
		$data['batch'] = $this->player_manager->sortAccountProcessList($sort, $config['per_page'], null);

		$this->load->view('player_management/ajax_account_process_list', $data);
	}

	/**
	 * overview : add account process
	 *
	 * detail : load view for add account process
	 *
	 */
	public function addAccountProcess() {
		$type_code = $this->player_manager->getBatchCode();
		$data = array();
		if (!empty($type_code)) {
			$x = explode('-', $type_code['typeCode']);
			$data['type_code'] = $x['0'] . '-' . sprintf("%06s", ($x['1'] + 1));
		} else {
			$data['type_code'] = "OG-000001";
		}
		$this->load->view('player_management/ajax_add_account_process', $data);
	}

	/**
	 * overview : verify account process
	 *
	 * detail : validates and verifies input of the end user and will add batch account
	 */
	public function verifyAddAccountProcess() {
		$this->load->library(array('agency_library'));

		$username = $this->input->post('username');
		$this->utils->debug_log('verifyAddAccountProcess: name = ', $username);

		$checkBatchExist = $this->player_model->checkBatchExist($username);

		if ($checkBatchExist) {
			$message = lang('con.plm21') . " " . $username . ". " . lang('con.plm22');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			return redirect('player_management/accountProcess');
		}

		//add lockAndTrans for registration
		//modified by jhunel.php.ph
		if($this->utils->isEnabledFeature('enable_username_cross_site_checking')){
			//global lock
			$add_prefix = false;
			$anyid = 0;
		} else {
			//not global lock
			$add_prefix = true;
			$anyid = random_string('numeric', 5);
		}
		$controller = $this;
		$playerId=null;
		$player_data = $this->input->post();

		if($this->utils->isEnabledMDB()){
			//lock all
			$this->utils->globalLockPlayerRegistration('', function () use ($player_data) {
				$playerIdArr = (array) $this->player_model->register($player_data,true);
				$success=!empty($playerIdArr);
				if($success){
					foreach ($playerIdArr as $playerId) {
						//sync
						$rlt=$this->syncPlayerCurrentToMDB($playerId, false);
						$this->utils->debug_log('syncPlayerCurrentToMDB', $rlt);
					}
				}
				return $success;
			});
		}else{
			$this->lockAndTrans(Utils::GLOBAL_LOCK_ACTION_REGISTRATION, $anyid, function () use ($controller ,&$playerId) {
						$playerId = $controller->player_model->register($this->input->post(), true);
						return (!empty($playerId)) ? true : false;
					},$add_prefix);
		}

		// record action in agency log {
		$agent_name = $this->input->post('agent_name');
		$count = $this->input->post('count');
		$log_params = array(
			'action' => 'create_players',
			'link_url' => site_url('agency_management/agent_add_players'),
			'done_by' => $this->authentication->getUsername(),
			'done_to' => $username,
			'details' => 'Create ' . $count . ' players under agent ' . $agent_name,
		);
		$this->agency_library->save_action($log_params);
		// record action in agency log }

		$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Add Mass Player Account', "User " . $this->authentication->getUsername() . " has added mass account of Player");

		$message = lang('con.plm23') . " " . $this->input->post('count') . " " . lang('con.plm24');
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		redirect("player_management/searchAllPlayer", "refresh");
	}

	/**
	 * overview : account process
	 *
	 * detail : edit page for account process
	 *
	 * @param int $batch_id		batch_id
	 */
	public function editAccountProcess($batch_id) {
		$data['batch'] = $this->player_manager->getBatchByPlayerBatchId($batch_id);
		$this->load->view('player_management/ajax_edit_account_process', $data);
	}

	/**
	 * overview : verify account process
	 *
	 * detail : validates and verifies input of the end user and will edit account
	 *
	 * @param int $batch_id		batch_id
	 */
	public function verifyEditAccountProcess($batch_id) {
		if (!$this->permissions->checkPermissions('account_process')) {
			$this->error_access();
		} else {
			$name = $this->input->post('name');
			$description = $this->input->post('description');

			$data = array(
				'name' => $name,
				'description' => $description,
				'updatedOn' => date('Y-m-d H:i:s'),
			);

			$this->player_manager->editAccountBatch($data, $batch_id);

			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Edit Mass Player Account', "User " . $this->authentication->getUsername() . " has edited mass account of Player");

			$message = lang('con.plm25');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			redirect(BASEURL . 'player_management/accountProcess', 'refresh');
		}
	}

	/**
	 * overview : delete account process id
	 *
	 * detail : deleting player batch, then redirect to pagination page of player list
	 *
	 * @param int $batch_id		batch_id
	 */
	public function deleteAccountProcess($batch_id) {
		// if (!$this->permissions->checkPermissions('delete_account_batch_process')) {
		// 	$this->error_access();
		// } else {
		$players = $this->player_manager->viewPlayerByBatchId($batch_id, null, null);
		$type = "batch";

		$this->player_manager->deletePlayerBatch($batch_id);
		$this->player_manager->deletePlayerAccountBatch($players);
		$this->player_manager->deletePlayerDetailsBatch($players);
		$this->player_manager->deletePlayerByBatch($players);

		$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Delete Batch Account Player', "User " . $this->authentication->getUsername() . " has delete batch account of Player");

		$message = lang('con.plm26');
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		redirect('player_management/accountProcess', 'refresh');
		// }
	}

	/**
	 * overview : account process
	 *
	 * detail : view page of account process
	 *
	 * @param $id		batch id
	 */
	public function viewAccountProcess($id) {

		$data['count_all'] = count($this->player_manager->viewPlayerByBatchId($id, null, null));
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
		$data['batch_account'] = $this->player_manager->viewPlayerByBatchId($id, $config['per_page'], null);
		$data['batch_id'] = $id;

		$this->load->view('player_management/ajax_view_account_process', $data);
	}

	/**
	 * overview : get account process list
	 *
	 * detail : pagination page of account process
	 *
	 * @param $segment		segment
	 * @param int $id		batch_id
	 */
	public function getAccountProcessListPages($segment, $id) {
		$data['count_all'] = count($this->player_manager->viewPlayerByBatchId($id, null, null));
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
		$data['batch_account'] = $this->player_manager->viewPlayerByBatchId($id, $config['per_page'], $segment);
		$data['batch_id'] = $id;

		$this->load->view('player_management/ajax_view_account_process_list', $data);
	}

	/**
	 * overview : account process details
	 *
	 * detail : view page of edit account details
	 *
	 * @param int $player_id	player_id
	 */
	public function editAccountProcessDetails($player_id) {
		if (!$this->permissions->checkPermissions('edit_player')) {
			$this->error_access();
		} else {
			$data['player'] = $this->player_manager->getBatchAccountByPlayerId($player_id);

			$this->load->view('player_management/ajax_edit_account_process_details', $data);
		}
	}

	/**
	 * overview : verify account process details
	 *
	 * details : validates and verifies inputs pf the end user and will edit the account
	 *
	 * @param $player_id	player_id
	 * @param $type			type
	 */
	public function verifyEditAccountProcessDetails($player_id, $type) {
		if (!$this->permissions->checkPermissions('edit_player')) {
			$this->error_access();
		} else {
			$data['player'] = $this->player_manager->getBatchAccountByPlayerId($player_id);

			$username = $this->input->post('username');
			$password = $this->input->post('password');

			if ($username == $data['player']['username'] && $password == $data['player']['batchPassword']) {
				redirect('player_management/accountProcess', 'refresh');
			}

			$checkUsernameExist = $this->player_manager->checkUsernameExist($username);

			if ($checkUsernameExist && $data['player']['username'] != $username) {
				$message = lang('con.plm03') . ": " . $username . ", " . lang('con.plm27');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				redirect('player_management/accountProcess', 'refresh');
			}

			/*$hasher = new PasswordHash('8', TRUE);
			$hash_password = $hasher->HashPassword($password);*/
			$hash_password = $this->salt->encrypt($password, $this->getDeskeyOG());

			$data = array(
				'username' => $username,
				'password' => $hash_password,
			);

			$this->player_manager->editPlayer($data, $player_id);

			$data = array(
				'batchPassword' => $password,
			);

			$this->player_manager->editPlayerAccount($data, $player_id, $type);

			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Edit Account Process Player', "User " . $this->authentication->getUsername() . " has edit account process of Player");

			$message = lang('con.plm28');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			//redirect(BASEURL . 'player_management/accountProcess', 'refresh');
		}
	}

	/**
	 * overview : delete account process detail
	 *
	 * detail : will delete account process
	 *
	 * @param int $player_id
	 * @param int $batch_id
	 * @param int $type
	 */
	public function deleteAccountProcessDetails($player_id, $batch_id, $type) {
		if (!$this->permissions->checkPermissions('delete_player')) {
			$this->error_access();
		} else {
			$batch = $this->player_manager->getBatchByPlayerBatchId($batch_id);
			$count = $batch['count'] - 1;

			$data = array(
				'count' => $count,
			);

			$this->player_manager->editAccountBatch($data, $batch_id);
			$this->player_manager->deletePlayerAccountPlayer($player_id, $type, $batch_id);
			$this->player_manager->deletePlayerDetails($player_id);
			$this->player_manager->deletePlayer($player_id);

			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Delete Account from Batch Player', "User " . $this->authentication->getUsername() . " has delete account from batch of Player");

			$message = lang('con.plm29');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			redirect('player_management/accountProcess', 'refresh');
		}
	}

	public function load_block_player_view($playerId) {
		$this->load->model(array('player', 'player_model', 'operatorglobalsettings'));

		$allTags = $this->player->getAllTagsOnly();
		if($this->utils->isEnabledFeature('add_suspended_status')) {
			$data['tags'] = $allTags;
		} else {
			$data['tags'] = [];

			$blockedPlayerTag = json_decode($this->operatorglobalsettings->getSettingJson('blocked_player_tag'), true);
			if (!empty($allTags) && !empty($blockedPlayerTag)) {
				foreach ($allTags as $value) {
					if (in_array($value['tagId'], $blockedPlayerTag)) {
						$data['tags'][] = $value;
					}
				}
			}
		}

		$data['player'] = $this->player_model->getPlayer(array('playerId' => $playerId));
		$this->load->view('player_management/block_player', $data);
	}

	/**
	 * overview : lock player
	 *
	 * detail : will lock/unlock player
	 *
	 * @param int $player_id	player_id
	 * @param int $status		status
	 * @param string $page		page
	 */
	public function lockPlayer($player_id, $status, $page) {
		if (!$this->permissions->checkPermissions('lock_player')) {
			$this->error_access();
		} else {
			$player = $this->player_manager->getPlayerById($player_id);
			$action = "";
			if ($status == 0) {
				$this->form_validation->set_rules('locked_period', lang('player.lp03'), 'trim|required|xss_clean');
				if ($this->input->post('locked_period') == 'specify') {
					$this->form_validation->set_rules('start_date_locked', lang('player.lp09'), 'trim|required|xss_clean');
					$this->form_validation->set_rules('end_date_locked', lang('player.lp10'), 'trim|required|xss_clean');
				}

				if ($this->form_validation->run() == false) {
					$message = lang('con.plm02');
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				} else {
					$locked_period = $this->input->post('locked_period');
					$end = "";
					$today = date("Y-m-d H:i:s");

					if ($locked_period == 'specify') {
						$start = $this->input->post('start_date_locked');
						$end = $this->input->post('end_date_locked');

						if ($start > $end) {
							$message = lang('con.plm30');
							$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
						} else {
							$data = array(
								'lockedStart' => $start,
								'lockedEnd' => date('Y-m-d', strtotime($end)) . ' 23:59:59',
								'status' => '1',
							);

							$this->player_manager->changePlayerStatus($player_id, $data);
				            $this->load->library(['player_library']);
				            $kickedPlayer = $this->player_library->kickPlayer($player_id);
							// $this->player_manager->logoutPlayer($player['username']);
							$message = lang('con.plm31') . " " . $player['username'] . " " . lang('con.plm32');
							$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
							$action = "locked";

							$this->savePlayerUpdateLog($player_id, lang('player.ap02') . ' ' . lang('player.80') . ' (' . $start . ') ' . lang('player.81') . ' (' . $end . ')', $this->authentication->getUsername()); // Add log in playerupdatehistory
						}
					} else {
						switch ($locked_period) {

						case '0':$end = date("Y-m-d H:i:s", strtotime('+1 day'));
							break;
						case '1':$end = date("Y-m-d H:i:s", strtotime('+1 week'));
							break;
						case '2':$end = date("Y-m-d H:i:s", strtotime('+1 month'));
							break;
						case '3':$end = date("Y-m-d H:i:s", strtotime('+1 year'));
							break;

						default:$message = lang('con.plm33');
							break;

						}

						$data = array(
							'lockedStart' => $today,
							'lockedEnd' => date('Y-m-d', strtotime($end)) . ' 23:59:59',
							'status' => '1',
						);

						$this->player_manager->changePlayerStatus($player_id, $data);
						$this->load->library(['player_library']);
			            $kickedPlayer = $this->player_library->kickPlayer($player_id);
						// $this->player_manager->logoutPlayer($player['username']);
						$action = "locked";
						$message = lang('con.plm31') . " " . $player['username'] . " " . lang('con.plm32');
						$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);

						$this->savePlayerUpdateLog($player_id, lang('player.ap02') . ' ' . lang('player.80') . ' (' . $today . ') ' . lang('player.81') . ' (' . $end . ')', $this->authentication->getUsername()); // Add log in playerupdatehistory
					}
				}
			} else {
				$data = array(
					'lockedStart' => '0000-00-00 00:00:00',
					'lockedEnd' => '0000-00-00 00:00:00',
					'status' => '0',
				);

				$this->player_manager->changePlayerStatus($player_id, $data);
				$message = lang('con.plm31') . " " . $player['username'] . " " . lang('con.plm34');
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
				$action = "unlocked";

				$this->savePlayerUpdateLog($player_id, lang('con.i08') . ' ' . lang('lang.player'), $this->authentication->getUsername()); // Add log in playerupdatehistory
			}

			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Lock/Unlock', "User " . $this->authentication->getUsername() . " has " . $action . " player '" . $player['username'] . "'");

			switch ($page) {
			case 'vipplayer':
				redirect('player_management/vipPlayer', 'refresh');
				break;

			case 'taggedlist':
				redirect('player_management/taggedlist', 'refresh');
				break;

			default:
				redirect('player_management/viewAllPlayer', 'refresh');
				break;
			}
		}
	}

	/**
	 * overview : unblock player
	 *
	 * detail : will unlock player
	 *
	 * @param int $playerId 	player_id
	 */
	public function unblockPlayer($playerId, $isAjax = false) {
		$this->load->model('player_model', 'player', 'operatorglobalsettings');
		$this->player_model->updatePlayer($playerId, array(
			'blocked' => 0,
			'blocked_status_last_update' => $this->utils->getNowForMysql()
		));

		$tagged = $this->player->getPlayerTags($playerId);
		$blockedPlayerTag = json_decode($this->operatorglobalsettings->getSettingJson('blocked_player_tag'));
		$totalWrongLoginAttempt = $this->player_model->getPlayerTotalWrongLoginAttempt($playerId);

		if($this->operatorglobalsettings->getSettingBooleanValue('player_login_failed_attempt_blocked') && ($totalWrongLoginAttempt != null) ) {
			if((int)$totalWrongLoginAttempt >= $this->operatorglobalsettings->getSettingIntValue('player_login_failed_attempt_times')){
				$this->player_model->updatePlayerTotalWrongLoginAttempt($playerId);
			}
		}

		$this->savePlayerUpdateLog($playerId, lang('role.25') . ' - ' .
			lang('adjustmenthistory.title.beforeadjustment') . ' (' . lang('tool.pm08') . ') ' .
			lang('adjustmenthistory.title.afteradjustment') . ' (' . lang('tool.pm09') . ') ', $this->authentication->getUsername()); // Add log in playerupdatehistory

		$this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('member.log.unblock.website'), "User " . $this->authentication->getUsername() . " has adjusted player '" . $playerId . "'");

		if (!empty($tagged) && !empty($blockedPlayerTag)) {
			foreach ($tagged as $playerTag) {
				if (in_array($playerTag['tagId'], (array) $blockedPlayerTag)) {
					$this->player->removePlayerTag($playerTag['playerTagId']);
				}
			}
		}

		$message = lang('member.message.success.unblocked');
		if ($isAjax) {
			//echo json_encode(array('message' => $message));
            $this->returnJsonResult(array('message' => $message));
        } else {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			redirect('/player_management/userInformation/' . $playerId, 'refresh');
		}
	}

    /**
     * overview : unblock player
     *
     * detail : will unlock player
     *
     * @param int $playerId 	player_id
     */
    public function unclosePlayer($playerId, $isAjax = false) {
        $this->load->model('player_model', 'player', 'operatorglobalsettings');
        $this->player_model->updatePlayer($playerId, array(
            'blockedUntil' => 0,
        ));

        $tagged = $this->player->getPlayerTags($playerId);
        $blockedPlayerTag = json_decode($this->operatorglobalsettings->getSettingJson('blocked_player_tag'));
        $totalWrongLoginAttempt = $this->player_model->getPlayerTotalWrongLoginAttempt($playerId);

        if($this->operatorglobalsettings->getSettingBooleanValue('player_login_failed_attempt_blocked') && ($totalWrongLoginAttempt != null) ) {
            if((int)$totalWrongLoginAttempt >= $this->operatorglobalsettings->getSettingIntValue('player_login_failed_attempt_times')){
                $this->player_model->updatePlayerTotalWrongLoginAttempt($playerId);
            }
        }

        $this->savePlayerUpdateLog($playerId, lang('role.25') . ' - ' .
            lang('adjustmenthistory.title.beforeadjustment') . ' (' . lang('tool.close') . ') ' .
            lang('adjustmenthistory.title.afteradjustment') . ' (' . lang('tool.unclose') . ') ', $this->authentication->getUsername()); // Add log in playerupdatehistory

        $this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('member.log.unblock.website'), "User " . $this->authentication->getUsername() . " has adjusted player '" . $playerId . "'");

        if (!empty($tagged) && !empty($blockedPlayerTag)) {
            foreach ($tagged as $playerTag) {
                if (in_array($playerTag['tagId'], $blockedPlayerTag)) {
                    $this->player->removePlayerTag($playerTag['playerTagId']);
                }
            }
        }

        $message = lang('member.message.success.unclose');
        if ($isAjax) {
            //echo json_encode(array('message' => $message));
            $this->returnJsonResult(array('message' => $message));
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
            redirect('/player_management/userInformation/' . $playerId, 'refresh');
        }
    }


	/**
	 * overview : block player
	 *
	 * detail : will block player
	 *
	 * @param int $playerId 	player_id
	 */
	public function blockPlayer($playerId, $isAjax = false) {
		$this->load->model('player_model', 'player');
        $this->load->library(array('player_library'));
		$admin_user_id  = $this->authentication->getUserId();
		$admin_username = $this->authentication->getUsername();

		$blockReason    = $this->input->post('optBlockingReason');
		$playerUsername = $this->player_model->getUsernameById($playerId);
		$playerTags     = $this->player_model->checkIfDuplicateTag($playerId, $blockReason);
		$today          = date("Y-m-d H:i:s");

		$this->player_model->updatePlayer($playerId, array(
			'blocked' => Player_model::BLOCK_STATUS,
			'blocked_status_last_update' => $this->utils->getNowForMysql()
		));

        $kickedPlayer = $this->player_library->kickPlayer($playerId);
        if($kickedPlayer){
            $this->player_library->kickPlayerGamePlatform($playerUsername, $playerId);
        }

		if (!empty($blockReason) && !$playerTags ) {
			$data = array(
				'playerId' => $playerId,
				'taggerId' => $admin_user_id,
				'tagId' => $blockReason,
				'status' => 1,
				'createdOn' => $today,
				'updatedOn' => $today,
			);

			$this->player_model->insertAndGetPlayerTag($data);
		}

		$this->savePlayerUpdateLog($playerId, lang('role.25') . ' - ' .
			lang('adjustmenthistory.title.beforeadjustment') . ' (' . lang('tool.pm09') . ') ' .
			lang('adjustmenthistory.title.afteradjustment') . ' (' . lang('tool.pm08') . ') ', $admin_username); // Add log in playerupdatehistory

		$this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('member.log.block.website'), "User " . $admin_username . " has adjusted player '" . $playerId . "'");

		$message = lang('member.message.success.blocked');
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		if ($isAjax) {
            $this->returnJsonResult(array('message' => $message));
        } else {
			redirect('/player_management/userInformation/' . $playerId, 'refresh');
		}
	}

    /**
     * overview : close player
     *
     * detail : will close player ,set player.
     *
     * @param int $playerId 	player_id
     */
    public function changePlayserStatus($playerId, $isAjax = false) {
        $this->load->model('player_model', 'player');
        $this->load->library(array('player_library'));
		$admin_user_id  = $this->authentication->getUserId();
		$admin_username = $this->authentication->getUsername();

		$playerStatus   = $this->input->post('playerStatus');
		$blockReason    = $this->input->post('optBlockingReason');
		$playerUsername = $this->player_model->getUsernameById($playerId);
		$playerTags     = $this->player->getPlayerTags($playerId, true);
        $today = date("Y-m-d H:i:s");

        switch($playerStatus){
            case "Block":
                $this->player_model->updatePlayer($playerId, array(
                	'blocked' => Player_model::BLOCK_STATUS,
                	'blocked_status_last_update' => $this->utils->getNowForMysql()
                ));

                $kickedPlayer = $this->player_library->kickPlayer($playerId);
                if($kickedPlayer){
                    $this->player_library->kickPlayerGamePlatform($playerUsername, $playerId);
                }
                break;
            case "Suspended":
                $this->player_model->updatePlayer($playerId, array(
                	'blocked' => Player_model::SUSPENDED_STATUS,
                	'blocked_status_last_update' => $this->utils->getNowForMysql()
                ));

                $kickedPlayer = $this->player_library->kickPlayer($playerId);
                if($kickedPlayer){
                    $this->player_library->kickPlayerGamePlatform($playerUsername, $playerId);
                }
                break;

            default:
                redirect('/player_management/userInformation/' . $playerId, 'refresh');
                break;
        }

        if (!empty($blockReason) && !in_array($blockReason, $playerTags)) {
            $data = array(
                'playerId' => $playerId,
                'taggerId' => $admin_user_id,
                'tagId' => $blockReason,
                'status' => 1,
                'createdOn' => $today,
                'updatedOn' => $today,
            );

            $this->player_model->insertAndGetPlayerTag($data);
        }

        $this->savePlayerUpdateLog($playerId, lang('role.25') . ' - ' .
            lang('adjustmenthistory.title.beforeadjustment') . ' (' . lang('tool.pm09') . ') ' .
            lang('adjustmenthistory.title.afteradjustment') . ' (' . lang('tool.pm08') . ') ', $admin_username); // Add log in playerupdatehistory

        $this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('member.log.block.website'), "User " . $admin_username . " has adjusted player '" . $playerId . "'");

        $message = lang('member.message.success.close');
        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
        if ($isAjax) {
            $this->returnJsonResult(array('message' => $message));
        } else {
            redirect('/player_management/userInformation/' . $playerId, 'refresh');
        }
    }

	/**
	 * overview : search player
	 *
	 * detail : search player with the given form parameters
	 *
	 */
	public function searchMain() {
		$signup_range = '';
		$age_range = '';
		$age_text = '';
		$period = '';
		$username = "";
		$referrer_id = "";
		$first_deposit_range = "";
		$second_deposit_range = "";

		if ($this->input->post('start_date') && $this->input->post('end_date')) {
			if ($this->input->post('start_date') < $this->input->post('end_date')) {
				$signup_range = "'" . $this->input->post('start_date') . "' AND '" . $this->input->post('end_date') . "'";
			} else {
				$message = lang('con.plm19');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			}
		} else {
			$period = $this->input->post('sign_time_period');
		}

		if ($this->input->post('age_from') && $this->input->post('age_to') && !$this->input->post('age_text')) {
			if ($this->input->post('age_from') < $this->input->post('age_to')) {
				$age_range = "'" . $this->input->post('age_from') . "' AND '" . $this->input->post('age_to') . "'";
			} else {
				$message = lang('con.plm20');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			}
		} else {
			$age_text = $this->input->post('age_text');
		}

		if ($this->input->post('username')) {
			if ($this->input->post('search_by') == 'LIKE') {
				$username = $this->input->post('search_by') . " '%" . $this->input->post('username') . "%' ";
			} else {
				$username = "= '" . $this->input->post('username') . "' ";
			}
		}

		if ($this->input->post('friend_referral_code')) {
			$referrer_id = $this->player_manager->getReferralByCode($this->input->post('friend_referral_code'))['playerId'];
		}

		$first_second_never = "";
		$first_second = "";
		$first_never = "";
		$second_never = "";
		$first = "";
		$second = "";
		$never = "";
		$deposit_range = "";

		if (!empty($this->input->post('first_deposit_date')) || !empty($this->input->post('second_deposit_date')) || !empty($this->input->post('never_deposited'))) {
			if (empty($this->input->post('from_date')) || empty($this->input->post('to_date'))) {
				$message = lang('player.80');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			} else {
				if (!empty($this->input->post('first_deposit_date')) && !empty($this->input->post('second_deposit_date')) && !empty($this->input->post('never_deposited'))) {
					$first_second_never = "'" . $this->input->post('from_date') . " 00:00:00' AND '" . $this->input->post('to_date') . " 23:59:59'";
				} elseif (!empty($this->input->post('first_deposit_date')) && !empty($this->input->post('second_deposit_date'))) {
					$first_second = "'" . $this->input->post('from_date') . " 00:00:00' AND '" . $this->input->post('to_date') . " 23:59:59'";
				} elseif (!empty($this->input->post('first_deposit_date')) && !empty($this->input->post('never_deposited'))) {
					$first_never = "'" . $this->input->post('from_date') . " 00:00:00' AND '" . $this->input->post('to_date') . " 23:59:59'";
				} elseif (!empty($this->input->post('second_deposit_date')) && !empty($this->input->post('never_deposited'))) {
					$second_never = "'" . $this->input->post('from_date') . " 00:00:00' AND '" . $this->input->post('to_date') . " 23:59:59'";
				} elseif (!empty($this->input->post('first_deposit_date'))) {
					$first = "'" . $this->input->post('from_date') . " 00:00:00' AND '" . $this->input->post('to_date') . " 23:59:59'";
				} elseif (!empty($this->input->post('second_deposit_date'))) {
					$second = "'" . $this->input->post('from_date') . " 00:00:00' AND '" . $this->input->post('to_date') . " 23:59:59'";
				} else {
					$never = "'" . $this->input->post('from_date') . " 00:00:00' AND '" . $this->input->post('to_date') . " 23:59:59'";
				}
			}
		}

		if (!empty($this->input->post('less_deposit_amount')) || !empty($this->input->post('greater_deposit_amount'))) {
			// if( (empty($this->input->post('start_first_deposit_date')) && empty($this->input->post('end_first_deposit_date'))) && (empty($this->input->post('start_second_deposit_date')) && empty($this->input->post('end_second_deposit_date'))) )  {
			// 	$message = lang('con.plm40');
			// 	$this->alertMessage(2, $message);
			// } else {
			// 	if($this->input->post('start_first_deposit_date') != null) {
			// 		if($this->input->post('start_first_deposit_date') < $this->input->post('end_first_deposit_date')) {
			// 			$first_deposit_range = "'" . $this->input->post('start_first_deposit_date') . " 00:00:00' AND '" . $this->input->post('end_first_deposit_date') . " 23:59:59'";
			// 		} else {
			// 			$message = lang('con.plm41');
			// 			$this->alertMessage(2, $message);
			// 		}
			// 	} else {
			// 		if($this->input->post('start_second_deposit_date') < $this->input->post('end_second_deposit_date')) {
			// 			$second_deposit_range = "'" . $this->input->post('start_second_deposit_date') . " 00:00:00' AND '" . $this->input->post('end_second_deposit_date') . " 23:59:59'";
			// 		} else {
			// 			$message = lang('con.plm42');
			// 			$this->alertMessage(2, $message);
			// 		}
			// 	}
			// }
			if (empty($this->input->post('less_deposit_amount')) || empty($this->input->post('greater_deposit_amount'))) {
				$message = lang('player.81');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			} else {
				if (empty($this->input->post('from_date')) || empty($this->input->post('to_date'))) {
					$deposit_range = "";
				} else {
					$deposit_range = "'" . $this->input->post('from_date') . " 00:00:00' AND '" . $this->input->post('to_date') . " 23:59:59'";
				}
			}
		}

		$search = array(
			'sign_time_period' => $period,
			'signup_range' => $signup_range,
			'username' => $username,
			'playerId' => $this->input->post('user_number'),
			'age_text' => $age_text,
			'age_range' => $age_range,
			'email' => $this->input->post('email'),
			'firstName' => $this->input->post('first_name'),
			'lastName' => $this->input->post('last_name'),
			'birthdate' => $this->input->post('birthdate'),
			'type' => $this->input->post('internal_accounts') == 'internal_accounts' ? 'batch' : '',
			'status' => $this->input->post('status'),
			'country' => $this->input->post('country'),
			'city' => $this->input->post('city'),
			'imAccount' => $this->input->post('im_account'),
			'qq' => $this->input->post('qq'),
			'tagId' => $this->input->post('tagged'),
			'registrationIP' => $this->input->post('ip_address'),
			'registrationWebsite' => $this->input->post('registration_website'),
			'referral_id' => $referrer_id,
			'tagId' => $this->input->post('tagged'),
			'playerLevel' => $this->input->post('player_level'),
			'blocked' => $this->input->post('only_blocked') == 'only_blocked' ? '1, 2' : '',
			'has_deposited' => $this->input->post('has_deposited') == 'has_deposited' ? 1 : '',
			'affiliate' => $this->input->post('affiliate'),
			'gameId' => $this->input->post('blocked_gaming_networks'),
			'less_deposit_amount' => $this->input->post("less_deposit_amount"),
			'greater_deposit_amount' => $this->input->post("greater_deposit_amount"),
			'first_deposit_range' => $first_deposit_range,
			'second_deposit_range' => $second_deposit_range,
			'never_deposited' => $this->input->post('never_deposited'),
			'promoId' => $this->input->post('promo'),
			'registered_by' => $this->input->post('registered_by'),
			'first_second_never' => $first_second_never,
			'first_second' => $first_second,
			'first_never' => $first_never,
			'second_never' => $second_never,
			'first' => $first,
			'second' => $second,
			'never' => $never,
			'deposit_range' => $deposit_range,
		);

		if (!array_filter($search)) {
			redirect('player_management/viewAllPlayer');
		} else {
			$number_player_list = '';
			$sort_by = '';
			$in = '';

			if ($this->session->userdata('number_player_list')) {
				$number_player_list = $this->session->userdata('number_player_list');
			} else {
				$number_player_list = 5;
			}

			if ($this->session->userdata('sort_by')) {
				$sort_by = $this->session->userdata('sort_by');
			} else {
				$sort_by = 'createdOn';
			}

			if ($this->session->userdata('in')) {
				$in = $this->session->userdata('in');
			} else {
				$in = 'desc';
			}

			$data['count_all'] = count($this->player_manager->populate($search, $sort_by, $in, null, null));
			$config['base_url'] = "javascript:get_player_pages(";
			$config['total_rows'] = $data['count_all'];
			$config['per_page'] = $number_player_list;
			$config['num_links'] = '1';

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
			$data['players'] = $this->player_manager->populate($search, $sort_by, $in, $config['per_page'], null);

			$data['current_page'] = floor(($this->uri->segment(3) / $config['per_page']) + 1);
			$data['today'] = date("Y-m-d H:i:s");

			$data['games'] = $this->player_manager->getAllGames();
			$data['allLevels'] = $this->player_manager->getAllPlayerLevels();
			$data['affiliates'] = $this->player_manager->getAllAffiliates();
			$data['tags'] = $this->player_manager->getAllTags();
			$data['promo'] = $this->player_manager->getAllPromo();

			$this->loadTemplate('Player Management', '', '', 'player');
			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/view_player_list', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : populate period of time
	 *
	 * detail : direct to view player list
	 *
	 * @param datetime $start_date		start_date
	 * @param datetime $end_date		end_date
	 */
	public function populatePeriodOfTime($start_date, $end_date) {
		$data['count_all'] = count($this->player_manager->populatePeriodOfTime($start_date, $end_date));
		$data['current_page'] = 1;
		$data['total_pages'] = 1;
		$data['players'] = $this->player_manager->populatePeriodOfTime($start_date, $end_date);
		$data['today'] = date("Y-m-d H:i:s");

		$this->loadTemplate('Player Management', '', '', 'player');
		$this->template->write_view('sidebar', 'player_management/sidebar');
		$this->template->write_view('main_content', 'player_management/view_player_list', $data);
		$this->template->render();
	}

	/**
	 * overview : populate username
	 *
	 * detail : direct to view player list
	 *
	 * @param string $username		username
	 */
	public function populateUsername($username) {
		$data['count_all'] = count($this->player_manager->populateUsername($username));
		$data['current_page'] = 1;
		$data['total_pages'] = 1;
		$data['players'] = $this->player_manager->populateUsername($username);
		$data['today'] = date("Y-m-d H:i:s");

		$this->loadTemplate('Player Management', '', '', 'player');
		$this->template->write_view('sidebar', 'player_management/sidebar');
		$this->template->write_view('main_content', 'player_management/view_player_list', $data);
		$this->template->render();
	}

	/**
	 * overview : post sort page
	 *
	 * detail : direct to view all player
	 *
	 */
	public function postSortPage() {
		$sort_by = $this->input->post('sort_by');
		$this->session->set_userdata('sort_by', $sort_by);

		$in = $this->input->post('in');
		$this->session->set_userdata('in', $in);

		$number_player_list = $this->input->post('number_player_list');
		$this->session->set_userdata('number_player_list', $number_player_list);

		redirect('player_management/viewAllPlayer');
	}

	/**
	 * overview : post sort page player
	 *
	 * detail : direct to view all player
	 *
	 */
	public function postSortPagePlayer() {
		$sort_by = $this->input->post('sort_by');
		$this->session->set_userdata('sort_by', $sort_by);

		$in = $this->input->post('in');
		$this->session->set_userdata('in', $in);

		$number_player_list = $this->input->post('number_player_list');
		$this->session->set_userdata('number_player_list', $number_player_list);

		redirect('player_management/viewAllPlayer');
	}

	/**
	 * overview : post change column
	 *
	 * detail : direct to view all player
	 *
	 */
	public function postChangeColumns() {
		$name = $this->input->post('name') ? "checked" : "unchecked";
		$level = $this->input->post('level') ? "checked" : "unchecked";
		$email = $this->input->post('email') ? "checked" : "unchecked";
		$country = $this->input->post('country') ? "checked" : "unchecked";
		$last_login_time = $this->input->post('last_login_time') ? "checked" : "unchecked";
		$tag = $this->input->post('tag') ? "checked" : "unchecked";
		$status_col = $this->input->post('status_col') ? "checked" : "unchecked";
		$registered_on = $this->input->post('registered_on') ? "checked" : "unchecked";
		$registered_by = $this->input->post('registered_by') ? "checked" : "unchecked";

		$data = array(
			'name' => $name,
			'level' => $level,
			'email' => $email,
			'country' => $country,
			'last_login_time' => $last_login_time,
			'tag' => $tag,
			'status_col' => $status_col,
			'registered_on' => $registered_on,
			'registered_by' => $registered_by,
		);
		$this->session->set_userdata($data);
		redirect('player_management/viewAllPlayer');
	}


    public function batchRecoverTransferCondition() {
        if (!$this->permissions->checkPermissions('cancel_member_transfer_condition')) {
            $this->error_access();
            return;
        }

        $ids = $this->input->post('transfer_condition_ids');
        # check if post data is array or not empty
        if (empty($ids) || !is_array($ids)) {
            return;
        }

        # check valid ids
        foreach ($ids as $key => $value) {
            $intValue = intval($value);

            if (empty($intValue)) {
                unset($ids[$key]);
            } else {
                $ids[$key] = $intValue;
            }
        }

        # final check if validated ids is not empty or array
        if (empty($ids) || !is_array($ids)) {
            return;
        }

        $this->load->model(array('transfer_condition'));
        $this->transfer_condition->updateStatus($ids, Transfer_condition::STATUS_ACTIVE);
    }

	/**
	 * overview : send batch message
	 *
	 * detail : $return json data
	 *
	 */
	public function sendBatchMessage() {

		// $this->load->model('CS');
		$this->load->model(array('internal_message'));
		$subject = $this->input->post('subject');
		$message = $this->input->post('message');
		$playerIds = $this->input->post('playerIds');
		if (!$subject OR !$message OR !$playerIds) {
			return;
		}
		$today = date('Y-m-d H:i:s');
		$userId = $this->authentication->getUserId();
		$sender = $this->authentication->getUsername();
		// $ticket_number = '';

		$spc_chkr = true;
		$this->startTrans();
		if ($playerIds && (trim($subject) != '') || (trim($message) != '')) {
			foreach ($playerIds as $playerId) {

				$this->internal_message->addNewMessageAdmin($userId, $playerId, $sender, $subject, $message);

				// do {
				// 	$ticket_number = 'Ticket#' . $this->cs_manager->generateTicket();
				// } while (!$this->checkTicketNumber($ticket_number));

				// $add_messages = array(
				// 	'playerId' => $playerId,
				// 	'adminId' => $userId,
				// 	'session' => $ticket_number,
				// 	'subject' => $subject,
				// 	'date' => $today,
				// 	'status' => '0',
				// );

				// $message_id = $this->cs->addMessages($add_messages);

				// $add_messages_details = array(
				// 	'messageId' => $message_id,
				// 	'sender' => $userId,
				// 	'recipient' => $playerId,
				// 	'message' => $message,
				// 	'date' => $today,
				// 	'status' => '0',
				// );
				// $this->cs->addMessagesDetails($add_messages_details);
			}
			$spc_chkr = false;
		}

		$succ = $this->endTransWithSucc();

		if (!$succ) {
			$arr = array('status' => 'failed', 'msg' => lang('sys.ga.erroccured'));
		}elseif ($spc_chkr == true) {
			$arr = array('status' => 'failed', 'msg' => lang('sys.ga.erroccured'));
		}else {
			$arr = array('status' => 'success', 'msg' => lang('mess.19'));
		}
		$this->returnJsonResult($arr);
	}

    /**
     * overview : send batch message
     *
     * detail : $return json data
     *
     */
    public function sendBatchSmsMessage() {
        $this->load->model(array('player_model'));

        $message   = $this->input->post('message');
        $playerIds = $this->input->post('playerIds');
        $label     = $this->input->post('label') ? $this->input->post('label') : null;
        if (!$message OR !$playerIds) {
            return;
        }

        $rows = $this->player_model->getPhoneNumbersByIds($playerIds);
        $usernames = [];
        $err = null;

        $totalSend = 0;
        $successSent = 0;
        if (!empty($rows)) {
            foreach ($rows as $row) {
                if (!empty($row['contactNumber'])) {
                    $totalSend++;
                    $sendSuccess = $this->utils->sendSmsByApi($row['contactNumber'], $message, $label);
                    if ($sendSuccess) {
                        $successSent++;
                    }
                    $usernames[] = $row['username'];
                } else {
                    $this->utils->error_log('phone number is empty');
                    $succ = false;
                    $err = lang('Phone number not found');
                }
            }

            $succ = ($successSent > 0 ? true : false);
        } else {
            $this->utils->error_log('phone number is empty');
            $succ = false;
            $err = lang('Phone number not found');
        }

        if (!$succ) {
            $arr = array('status' => 'failed', 'msg' => empty($err) ? lang('sys.ga.erroccured') : $err);
        } else {
            if ($totalSend > 1) {
                # This is a batch send
                $arr = array('status' => 'success', 'msg' => "[$successSent/$totalSend] " . lang('mess.19') . ': ' . implode(',', $usernames));
            } else {
                # Sending to only one number
                $arr = array('status' => 'success', 'msg' => lang('mess.19') . ': ' . implode(',', $usernames));
            }
        }

        $this->returnJsonResult($arr);
    }

	/**
	 * overview : check ticket number
	 *
	 * @param $ticket_number
	 * @return bool
	 */
	protected function checkTicketNumber($ticket_number) {
		$this->load->model('CS');
		$result = $this->cs->checkTicketNumber($ticket_number);

		if (!$result) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * overview : get player usernames
	 *
	 * detail : @return json data. show results and total count
	 */
	function getPlayerUsernames() {
		$this->load->model('player');
		$this->load->helper('security');
		$searchTerm = $this->input->get('q');
		list($result, $total) = $this->player->getPlayerUsernameSuggestions($searchTerm);
		$arr = array('items' => $result, 'total_count' => $total);
		// echo json_encode($arr);
		$this->returnJsonResult($arr);
	}

	/**
	 * overview : get player by username
	 *
	 * detail : search player by username
	 *
	 * @param string $username		username
	 */
	public function player($username) {
		$readOnlyDB = $this->getReadOnlyDB();
		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->model('Model_name', 'player');
		$playerId = $this->player->getPlayerIdByUsername($username);
		if ($playerId) {
			return $this->userInformation($playerId);
			// redirect("/player_management/userInformation/{$playerId}");
		} else {
			$this->session->set_flashdata('error_message', $username . ' ' . lang('player.uab10'));
			redirect('player_management/searchAllPlayer');
		}
	}

	public function userInformation($player_id, $tab = 1) {
		if($this->config->item('use_old_userinformation_page')){
			$this->old_userInformation($player_id);
		} else {
			$this->new_userInformation($player_id, $tab);
		}
	}

	/**
	 * overview : player information
	 *
	 * details : include player, signup, personal, contact, game and bank information
	 *
	 * @param int $player_id	player_id
	 */
	public function old_userInformation($player_id) {
		$this->utils->debug_log('withdraw_verification', $this->utils->getConfig('withdraw_verification'), 'permission', $this->permissions->checkPermissions('reset_password'));
		$this->load->library('data_tables');
		if (!$this->permissions->checkPermissions('player_list') || empty($player_id)) {
			$this->error_access();
		} else {
			$this->utils->startEvent('search_session', 'search player session');

			$this->load->model(array('player_model', 'player', 'playerbankdetails', 'payment_account', 'wallet_model', 'game_provider_auth', 'system_feature', 'player_kyc', 'point_transactions','linked_account_model','sale_order'));
			$this->load->library(['player_responsible_gaming_library']);

			$playerUsername = $this->player_model->getUsernameById($player_id);

			$data['isAGGameAccountDemoAccount'] = false;
			$ag_api = $this->utils->loadExternalSystemLibObject(AG_API);
			if ($ag_api && $this->utils->isEnabledFeature('create_ag_demo')) {
				$gameName = $this->game_provider_auth->getGameUsernameByPlayerUsername($playerUsername, AG_API);
				if (!empty($gameName)) {
					$agDemoAmoAccount = $this->game_provider_auth->isGameAccountDemoAccount($gameName, AG_API);
					if (!$agDemoAmoAccount) {
						$data['isAGGameAccountDemoAccount'] = true;
					}
				}
			}

			$data['isAGINGameAccountDemoAccount'] = false;
			$agin_api = $this->utils->loadExternalSystemLibObject(AGIN_API);
			if ($agin_api && $this->utils->isEnabledFeature('create_agin_demo')) {
				$gameNameAGIN = $this->game_provider_auth->getGameUsernameByPlayerUsername($playerUsername, AGIN_API);
				if (!empty($gameNameAGIN)) {
					$aginDemoAccount = $this->game_provider_auth->isGameAccountDemoAccount($gameNameAGIN, AGIN_API);
					if (!$aginDemoAccount) {
						$data['isAGINGameAccountDemoAccount'] = true;
					}
				}
			}

		    $success=$this->wallet_model->lockAndTransForPlayerBalance($player_id, function () use (
		        $player_id) {

				return $this->wallet_model->checkAndSyncAllWallets($player_id);
			});

			$data['player'] = $this->player_model->getPlayerInfoById($player_id);
			$this->utils->debug_log('load player', $data['player']['playerId']);
            $data['player_registrationIp']   = $this->player_model->registrationIP($player_id);

            $getUpdatedGroupAndLevel = $this->player_model->getPlayerCurrentLevel($player_id);
            if($getUpdatedGroupAndLevel){
                $data['player']['groupName'] = lang($getUpdatedGroupAndLevel[0]['groupName']);
                $data['player']['levelName'] = lang($getUpdatedGroupAndLevel[0]['vipLevelName']);
            }

			//update login logout info from runtime
			$this->load->library(array('salt'));
			$data['hide_password'] = $this->salt->decrypt($data['player']['password'], $this->getDeskeyOG());
			$data['admin_user_id'] = $this->authentication->getUserId();

			//online flag
			$data['playeronline'] = $data['player']['online'];//$this->player_model->existsOnlineSession($player_id);
			$this->utils->endEvent('search_session');

			$this->utils->startEvent('get_player', 'get player');
			$data['age'] = $data['player']['birthdate'] == 0 ? 0 : $this->player_manager->get_age($data['player']['birthdate']);
			$data['playeraccount'] = $this->player_manager->getPlayerAccount($player_id);
			$data['affiliate'] = $this->player_manager->getAffiliateOfPlayer($player_id);
			$data['agent'] = $this->player_manager->getAgentOfPlayer($player_id);
			$data['dispatch_account'] = $this->player_manager->getDispatchAccountOfPlayer($player_id);
			$data['refereePlayerId'] = $this->player_model->getRefereePlayerId($player_id);
			$data['refereePlayer'] = $this->player_model->getPlayerById($data['refereePlayerId']);
			$data['taggedStatus'] = $this->player->getPlayerTags($player_id, TRUE);
			$data['player_deleted_status'] = $this->player_model->isDeleted($player_id);
			$data['player_closed_status'] = $this->player_model->isClosed($player_id);
			$data['tag_list'] = $this->player->getAllTagsOnly();
			$this->utils->endEvent('get_player');

			$this->utils->startEvent('get_paymentaccounts', 'get_paymentaccounts');
			$data['paymentaccounts'] = array();
			if ($this->permissions->checkPermissions('available_payment_account_for_player')) {
				$data['paymentaccounts'] = $this->payment_account->getAvailableAccount($player_id, null, null, true);
			}
			$this->utils->endEvent('get_paymentaccounts');

			$this->utils->startEvent('refer_and_responsible', 'refer_and_responsible');
			$referrer = $this->player_manager->getCodeofReferrer($player_id);
			$data['referred_by_code'] = empty($referrer) ? null : $referrer['referrer_code'];
			$data['referred_by_id'] = empty($referrer) ? null : $referrer['referrer_id'];
			$data['player_id'] = $player_id;
			$referrals = $this->player_manager->getAllReferralByPlayerId($player_id);
			$data['referral_count'] = ($referrals == false) ? 0 : count($referrals);

            $this->player_responsible_gaming_library->fetchResponsibleGamingDataForSBE($player_id);

			$this->utils->endEvent('refer_and_responsible');

			$this->utils->startEvent('deposit_bank_games', 'deposit_bank_games');
			$average = "";
			if (!empty($data['total_deposits']['totalDeposit']) && !empty($data['total_deposits']['totalNumberOfDeposit'])) {
				$average = ($data['total_deposits']['totalDeposit'] / $data['total_deposits']['totalNumberOfDeposit']);
			}
			$data['average_deposits'] = $average ? $average : '0';

			$average = "";
			if (!empty($data['total_withdrawal']['totalWithdrawal']) && !empty($data['total_withdrawal']['totalNumberOfWithdrawal'])) {
				$average = ($data['total_withdrawal']['totalWithdrawal'] / $data['total_withdrawal']['totalNumberOfWithdrawal']);
			}
			$data['average_withdrawals'] = $average ? $average : '0';

            $bankdetails = $this->playerbankdetails->getNotDeletedBankInfoList($player_id);
            $data['deposit_bankdetails'] = $bankdetails['deposit'];
            $data['withdrawal_bankdetails'] = $bankdetails['withdrawal'];


			$data['manually_calc_cashback_on_admin'] = 'true';

			$linkedAccounts = $this->getLinkedAccountDetails($playerUsername);
			$data['player_username'] = $playerUsername;
			$data['linked_accounts'] = !empty($linkedAccounts) ? $linkedAccounts[self::FIRST_CHILD_INDEX]['linked_accounts'] : null;

			$this->utils->endEvent('deposit_bank_games');

			$this->utils->startEvent('game_logs', 'game_logs');
			$this->load->model('game_logs');
			$game_data = array(
				'total_bet_count' => 0,
				'total_bet_ave' => 0,
				'total_bet_sum' => 0,
				'total_gain_count' => 0,
				'total_gain_percent' => 0,
				'total_gain_ave' => 0,
				'total_gain_sum' => 0,
				'total_loss_count' => 0,
				'total_loss_percent' => 0,
				'total_loss_ave' => 0,
				'total_loss_sum' => 0,
				'total_gain_loss_count' => 0,
				'total_gain_loss_sum' => 0,
			);

			$game_platforms = $this->game_provider_auth->getGamePlatforms($player_id);
			$game_logs = $this->game_logs->getSummary($player_id);
			foreach ($game_platforms as &$game_platform) {
				$game_platform_id = $game_platform['id'];
				if (isset($game_logs[$game_platform_id])) {
					$game_platform = array_merge($game_platform, $game_logs[$game_platform_id]);
					$game_data['total_bet_sum'] += $game_logs[$game_platform_id]['bet']['sum'];
					$game_data['total_bet_count'] += $game_logs[$game_platform_id]['bet']['count'];
					$game_data['total_gain_sum'] += $game_logs[$game_platform_id]['gain']['sum'];
					$game_data['total_gain_count'] += $game_logs[$game_platform_id]['gain']['count'];
					$game_data['total_loss_sum'] += $game_logs[$game_platform_id]['loss']['sum'];
					$game_data['total_loss_count'] += $game_logs[$game_platform_id]['loss']['count'];
					$game_data['total_gain_loss_sum'] += $game_logs[$game_platform_id]['gain_loss']['sum'];
					$game_data['total_gain_loss_count'] += $game_logs[$game_platform_id]['gain_loss']['count'];
				}
			}

			$game_data['total_bet_ave'] = $game_data['total_bet_count'] ? $game_data['total_bet_sum'] / $game_data['total_bet_count'] : 0;
			$game_data['total_gain_ave'] = $game_data['total_gain_count'] ? $game_data['total_gain_sum'] / $game_data['total_gain_count'] : 0;
			$game_data['total_gain_percent'] = $game_data['total_gain_count'] ? (($game_data['total_gain_count'] / $game_data['total_bet_count']) * 100) : 0;
			$game_data['total_loss_ave'] = $game_data['total_loss_count'] ? $game_data['total_loss_sum'] / $game_data['total_loss_count'] : 0;
			$game_data['total_loss_percent'] = $game_data['total_loss_count'] ? (($game_data['total_loss_count'] / $game_data['total_bet_count']) * 100) : 0;
			if($this->utils->getConfig('use_total_hour')){
				$game_data['total_result_percent'] = $game_data['total_bet_sum'] ? (($game_data['total_gain_loss_sum'] / $game_data['total_bet_sum']) * 100) : 0;
			}
			$game_data['game_platforms'] = $game_platforms;
			$game_data['admin_user_id'] = $this->session->userdata('user_id');

			$data['game_data'] = $game_data;

			if($this->utils->isEnabledFeature('show_kyc_status') || $this->utils->isEnabledFeature('show_risk_score')) {
				$this->utils->deleteCache(); # Clears cache so player info page gets latest KYC and risk data
			}

			if ($this->utils->isEnabledFeature('show_kyc_status')) {
				$data['kyc_status'] = $this->getPlayerCurrentStatus($player_id);
				$data['kyc_level'] = $this->player_kyc->getPlayerCurrentKycLevel($player_id);
			}

			if ($this->utils->isEnabledFeature('show_pep_status') && $this->utils->isEnabledFeature('show_risk_score')) {
				$data['pep_status'] = $this->check_risk_player_PEP($player_id);
			}

			if ($this->utils->isEnabledFeature('show_c6_status') && $this->utils->isEnabledFeature('show_risk_score')) {
				$data['c6_status'] = $this->check_risk_player_c6($player_id);
			}

			if ($this->utils->isEnabledFeature('show_risk_score')) {
				$data['risk_score'] = $this->generate_total_risk_score($player_id);
				$data['risk_level']	= $this->risk_score_model->getPlayerCurrentRiskLevel($player_id);
			}

			if ($this->utils->isEnabledFeature('show_allowed_withdrawal_status') && $this->utils->isEnabledFeature('show_risk_score') && $this->utils->isEnabledFeature('show_kyc_status')) {
				$data['allowed_withdrawal_status'] = ($this->generate_allowed_withdrawal_status($player_id)) ? lang('Yes') : lang('No');
			}

			$totalPoints = $this->point_transactions->getPlayerTotalPoints($player_id);
			$playerTotalPoints = 0;
			if (!empty($totalPoints)) {
				$playerTotalPoints = array_sum(array_column($totalPoints, 'points'));
			}
			$data['available_points'] = $playerTotalPoints;

			$this->utils->endEvent('game_logs');

			$this->utils->startEvent('template_render', 'template_render');

			$this->load->model(array('withdraw_condition', 'payment', 'transfer_condition'));

			// -- Load communication preference
			if($this->permissions->checkPermissions('player_communication_preference') && $this->utils->isEnabledFeature('enable_communication_preferences')) {
				$this->load->model('communication_preference_model');

				$data['current_comm_pref'] = $this->communication_preference_model->getCurrentPreferences($player_id);
				$data['config_comm_pref'] = $this->utils->getConfig('communication_preferences');
			}
        	$data['tags'] = $this->player->getAllTagsOnly();

			$this->template->add_css('resources/third_party/bootstrap-multiselect-master/dist/css/bootstrap-multiselect.css');
			$this->template->add_js('resources/third_party/bootstrap-multiselect-master/dist/js/bootstrap-multiselect.js');
			$this->template->add_js('resources/js/bootstrap-notify.min.js');
			$this->template->add_js('resources/js/highlight.pack.js');
			$this->template->add_js('resources/js/ace/ace.js');
			$this->template->add_js('resources/js/ace/mode-json.js');
			$this->template->add_css('resources/css/hljs.tomorrow.css');
			$this->template->add_js('resources/js/json2.min.js');
			$this->template->add_js('resources/js/select2.min.js');
			$this->template->add_css('resources/css/select2.min.css');

			$this->wallet_model->recordPlayerAfterActionWalletBalanceHistory(Wallet_model::BALANCE_ACTION_REFRESH, $player_id, null, -1, 0, null, null, null, null, null);

			$this->loadTemplate(lang('Player Management'), '', '', 'player');
			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/view_user_information', $data);
			$this->template->render();
			$this->utils->endEvent('template_render');
		}
	}
	//-----------------------------------------------old userInformation----------------------------------------------------------
		/**
		 * overview : get all tags
		 *
		 * detail : @return json_data for checking tag status
		 *
		 * @param int $player_id	player_id
		 */
		public function getAllTags($player_id) {
			$this->load->model(array('player'));
			$tags = $this->player->getAllTagsOnly();
			$tag_status = $this->player->getPlayerTags($player_id, TRUE);

			$arr = array('status' => 'success', 'tags' => $tags, 'tagStatus' => $tag_status);
			$this->returnJsonResult($arr);
		}

		/**
		 * overview : tag player
		 *
		 * detail : selecting tags in tagging player
		 */
		public function tagPlayer() {
			$this->load->model(array('player','player_model'));
			$tagIds = $this->input->post('tagId');
			$today = date("Y-m-d H:i:s");
			$player_id = $this->input->post('playerId');
			$tagged = $this->player->getPlayerTags($player_id);
			if (FALSE === $tagged) {
				$tagged = [];
			}

			$user_id = $this->authentication->getUserId();
			#if tagId is not equal to zero
			if ($tagIds) {

				#delete not exists the tag id
				$playerTaggedIds = [];
	            $playerUnTaggedIds = [];
	            $playerUnTagged = [];
	            $latestTag = '';
	            $unlinkTag = '';

	            foreach ($tagged as $playerTag) {
	                if (in_array($playerTag['tagId'], $tagIds)) {
	                    $playerTaggedIds[] = $playerTag['tagId'];
	                    continue;
	                } else {
	                    $playerUnTaggedIds[] = $playerTag['playerTagId'];
	                    $playerUnTagged[] = $playerTag;
	                }
	            }

				foreach ($tagIds as $tagId) {
					if (in_array($tagId, $playerTaggedIds)) {
						continue;
					}
					$data = array(
						'playerId' => $player_id,
						'taggerId' => $user_id,
						'tagId' => $tagId,
						'createdOn' => $today,
						'updatedOn' => $today,
						'status' => 1,
					);
					$newTag = $this->player_model->insertAndGetPlayerTag($data);
					$this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('adjustmenthistory.title.beforeadjustment') . ' (' . lang('player.tp03') . ') ' .
						lang('adjustmenthistory.title.afteradjustment') . ' (' . $newTag . ') ',
	                    "User " . $this->authentication->getUsername() . " has adjusted player '" . $player_id . "'");

	                $newTagIds[] =  $data['tagId'];
	                $this->utils->debug_log('the $newTagIds ---->', $newTagIds);
				}

	            if(!empty($newTagIds)){
	                $playerNewTags = $this->player->getTagDetails($newTagIds);
	                $oldTag = '';
	                if(empty($tagged)){
	                    $oldTag = lang('player.tp03');
	                }else{
	                    foreach($tagged as $val) {
	                        $oldTag .= " <span class='tag label label-info' style='background-color:".$val['tagColor']."'>".$val['tagName']."</span>";
	                    }
	                }

	                foreach($playerNewTags as $res){
	                    $latestTag .= " <span class='tag label label-info' style='background-color:".$res['tagColor']."'>".$res['tagName']."</span>";
	                }
	                $this->savePlayerUpdateLog($player_id, lang('player.26') . ' - ' .
	                    lang('adjustmenthistory.title.beforeadjustment') . ' (' . $oldTag . ' ) ' .
	                    lang('adjustmenthistory.title.afteradjustment') . ' ( added ' . $latestTag . ') ',
	                    $this->authentication->getUsername());
	            }


	            if(!empty($playerUnTagged)){
	                $tagged = $this->player->getPlayerTags($player_id);
	                $this->player->removePlayerTag($playerUnTaggedIds);
	                $oldTag = '';
	                foreach($tagged as $val) {
	                    $oldTag .= " <span class='tag label label-info' style='background-color:".$val['tagColor']."'>".$val['tagName']."</span>";
	                }
	                foreach($playerUnTagged as $res){
	                    $unlinkTag .= " <span class='tag label label-info' style='background-color:".$res['tagColor']."'>".$res['tagName']."</span>";
	                }
	                $date = date("Y-m-d H:i:s");
	                $today = date("Y-m-d H:i:s", strtotime($date) + 1);
	                $this->savePlayerUpdateLog($player_id, lang('player.26') . ' - ' .
	                    lang('adjustmenthistory.title.beforeadjustment') . ' (' . $oldTag . ' ) ' .
	                    lang('adjustmenthistory.title.afteradjustment') . ' ( removed ' . $unlinkTag . ') ',
	                    $this->authentication->getUsername(), $today);
	            }

				$this->showTagSuccess($this->player->getPlayerTags($player_id, TRUE));
			} else {
				# if tagId is zero update to no tag
				#delete player tag
				$newTag = $this->player->deleteAndGetPlayerTag($player_id);

				$source_tag = [];
				foreach ($tagged as $playerTag) {
					$source_tag[] = $playerTag['tagName'];
				}

				$this->savePlayerUpdateLog($player_id, lang('player.26') . ' - ' .
					lang('adjustmenthistory.title.beforeadjustment') . ' (' . implode(',', $source_tag) . ') ' .
					lang('adjustmenthistory.title.afteradjustment') . ' (' . lang('player.tp10') . ') ', $this->authentication->getUsername()); // Add log in playerupdatehistory

				$this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('adjustmenthistory.title.beforeadjustment') . ' (' . implode(',', $source_tag) . ') ' .
					lang('adjustmenthistory.title.afteradjustment') . ' (' . lang('player.tp10') . ') ', "User " . $this->authentication->getUsername() . " has adjusted player '" . $player_id . "'");

				if ($newTag) {
					$this->showTagSuccess($newTag);
				} else {
					$this->showTagError();
				}
			}
		}

		/**
		 * overview : show tag success
		 *
		 * @param $newTagStatus
		 */
		protected function showTagSuccess($newTagStatus) {
			$arr = array('status' => 'success', 'tagStatus' => $newTagStatus);
			$this->returnJsonResult($arr);
		}

		/**
		 * overview : show tag success
		 *
		 * @param $newTagStatus
		 */
		protected function showTagError() {
			$arr = array('status' => 'failed', 'msg' => "Error occured");
			$this->returnJsonResult($arr);
		}


		/**
		 * overview : adding player affiliate reference
		 */
		public function addPlayerAffiliateRef() {

			$playerId = $this->input->post('playerId');
			$affiliateId = $this->input->post('affiliateId');
			$data['affUsername'] = $this->player_manager->addAffiliateToPlayer($playerId, $affiliateId);
			$this->savePlayerUpdateLog($playerId, lang('player.ui71') . ' - ' .
				lang('adjustmenthistory.title.beforeadjustment') . ' (' . lang('affiliate.no.affiliate') . ') ' .
				lang('adjustmenthistory.title.afteradjustment') . ' (' . $data['affUsername'] . ') ', $this->authentication->getUsername()); // Add log in playerupdatehistory

			//sync
			$username=$this->player_model->getUsernameById($playerId);
			$this->syncPlayerCurrentToMDBWithLock($playerId, $username, false);

			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('player.ui71') . ' : ' . lang('player.ufr01') . '(' . $playerId . ') - ' .
				lang('adjustmenthistory.title.beforeadjustment') . ' (' . lang('affiliate.no.affiliate') . ') ' .
				lang('adjustmenthistory.title.afteradjustment') . ' (' . $data['affUsername'] . ') ', "User " . $this->authentication->getUsername() . " has adjusted player '" . $playerId . "'");

			$arr = array('status' => 'success', 'data' => $data);
			//echo json_encode($arr);
			$this->returnJsonResult($arr);
		}

		/**
		 * overview : get all player levels
		 *
		 * detail : @return json data for player level details
		 *
		 * @param int $player_id	player_id
		 */
		public function getAllPlayerLevels($player_id) {
			$this->load->model(array('player_model'));
			$player_levels = $this->player_model->getAllPlayerLevels();
			array_walk($player_levels, function(&$row){
				$row['groupName'] = lang($row['groupName']);
				$row['vipLevelName'] = lang($row['vipLevelName']);
			});

			$current_player_level = $this->player_model->getPlayerCurrentLevel($player_id);
			array_walk($current_player_level, function(&$row){
				$row['groupName'] = lang($row['groupName']);
				$row['vipLevelName'] = lang($row['vipLevelName']);
			});

			$arr = array('status' => 'success', 'playerLevels' => $player_levels, 'currenPlayerLevel' => $current_player_level);
			$this->returnJsonResult($arr);
			// echo json_encode($arr);
		}

		/**
		 * overview : adjust player level
		 *
		 * detail :  @return json data for player level details
		 *
		 */
		public function doAdjustPlayerLevelThruAjax() {
			# LOAD
			$this->load->model(array('group_level'));

			# GET PARAMS
			$newPlayerLevel = $this->input->post('newPlayerLevel');
			$playerId = $this->input->post('playerId');

			$this->group_level->startTrans();
			$oldlevel = $this->player_manager->getPlayerLevel($playerId);
			$this->group_level->adjustPlayerLevel($playerId, $newPlayerLevel);
			$level = $this->player_manager->getPlayerLevel($playerId);
			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('player.46'), "User " . $this->authentication->getUsername() . " has adjusted player '" . $playerId . "'");
			$this->savePlayerUpdateLog($playerId, lang('player.46') . ' - ' .
				lang('adjustmenthistory.title.beforeadjustment') . ' (' . lang($oldlevel['groupName']) . ' - ' . lang($oldlevel['vipLevelName']) . ') ' .
				lang('adjustmenthistory.title.afteradjustment') . ' (' . lang($level['groupName']) . ' - ' . lang($level['vipLevelName']) . ') ', $this->authentication->getUsername()); // Add log in playerupdatehistory
			$this->group_level->endTrans();

			$this->group_level->setGradeRecord([
				'player_id' => $playerId,
				'request_type'  => Group_level::REQUEST_TYPE_SPECIFIC_GRADE,
				'request_grade' => Group_level::RECORD_SPECIFICGRADE,
				'updated_by'    => $this->authentication->getUserId(),
				'newvipId'      => $newPlayerLevel,
				'vipsettingId'  => $oldlevel['vipSettingId'],
				'vipsettingcashbackruleId' => $oldlevel['playerGroupId'],
				'level_from' => $oldlevel['vipLevel'],
				'level_to'   => $level['vipLevel'],
				'request_time'  => date('Y-m-d H:i:s'),
				'pgrm_start_time' => date('Y-m-d H:i:s'),
				'pgrm_end_time'   => date('Y-m-d H:i:s'),
				'status'          => Group_level::GRADE_SUCCESS
			]);
			$this->group_level->gradeRecode(false);

			if ($this->group_level->isErrorInTrans()) {
				$arr = array('status' => 'error', 'msg' => "Error occured");
	            $this->returnJsonResult($arr);
			} else {
				$current_player_level = $this->player_model->getPlayerCurrentLevel($playerId);
	            array_walk($current_player_level, function(&$row){
	                $row['groupName'] = lang($row['groupName']);
	                $row['vipLevelName'] = lang($row['vipLevelName']);
	            });
				$arr = array('status' => 'success', 'currenPlayerLevel' => $current_player_level);
	            $this->returnJsonResult($arr);
			}
		}

		/**
		 * overview : get all dispatch account levels
		 *
		 * detail : @return json data for dispatch account level details
		 *
		 * @param int $player_id	player_id
		 */
		public function getAllDispatchAccountLevels($player_id) {
			$this->load->model(array('player_model'));
			$dispatch_account_levels = $this->player_model->getAllDispatchAaccountLevels();
			array_walk($dispatch_account_levels, function(&$row){
				$row['group_name'] = lang($row['group_name']);
				$row['level_name'] = lang($row['level_name']);
			});

			$current_disparch_account_level = $this->player_model->getCurrentDispatchAccountLevel($player_id);
			array_walk($current_disparch_account_level, function(&$row){
				$row['group_name'] = lang($row['group_name']);
				$row['level_name'] = lang($row['level_name']);
			});

			$arr = array('status' => 'success', 'dispatch_account_levels' => $dispatch_account_levels, 'current_disparch_account_level' => $current_disparch_account_level);
			$this->returnJsonResult($arr);
		}

		/**
		 * overview : adjust player level
		 *
		 * detail :  @return json data for player level details
		 *
		 */
		public function doAdjustDispatchAccountLevelThruAjax() {

			# LOAD
			$this->load->model(array('player_model'));

			# GET PARAMS
			$new_dispatch_account_level = $this->input->post('new_dispatch_account_level');
			$playe_id = $this->input->post('playe_id');

			$this->player_model->startTrans();
			$oldlevel = $this->player_manager->getPlayerDispatchAccountLevel($playe_id);
			$this->player_model->adjustDispatchAccountLevel($playe_id, $new_dispatch_account_level);
			$level = $this->player_manager->getPlayerDispatchAccountLevel($playe_id);
			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('player.100'), "User " . $this->authentication->getUsername() . " has adjusted dispatch account of player '" . $playe_id . "'");
			$this->savePlayerUpdateLog($playe_id, lang('player.100') . ' - ' .
				lang('adjustmenthistory.title.beforeadjustment') . ' (' . lang($oldlevel['group_name']) . ' - ' . $oldlevel['level_name'] . ') ' .
				lang('adjustmenthistory.title.afteradjustment') . ' (' . lang($level['group_name']) . ' - ' . $level['level_name'] . ') ', $this->authentication->getUsername()); // Add log in playerupdatehistory
			$this->player_model->endTrans();

			if ($this->player_model->isErrorInTrans()) {
				$arr = array('status' => 'error', 'msg' => "Error occured");
	            $this->returnJsonResult($arr);
			} else {
				$current_disparch_account_level = $this->player_model->getCurrentDispatchAccountLevel($playe_id);
	            array_walk($current_disparch_account_level, function(&$row){
					$row['group_name'] = lang($row['group_name']);
					$row['level_name'] = lang($row['level_name']);
	            });
				$arr = array('status' => 'success', 'current_disparch_account_level' => $current_disparch_account_level);
	            $this->returnJsonResult($arr);
			}
		}

	    /**
	     * overview : adjust parent agent
	     *
	     * details : will load view to adjust player agent
	     *
	     * @param $player_id	player_id
	     */
	    public function adjustParentAgent($player_id) {
	        if (!$this->permissions->checkPermissions('assign_player_under_agent')) {
	            $this->error_access();
	        } else {
	            $this->load->model('agency_model');
	            $this->loadTemplate('Player Management', '', '', 'player');

	            $player_detail = $this->player_manager->getPlayerById($player_id);
	            $data['player'] = $player_detail;
	            $data['agents'] = array_column($this->agency_model->get_active_agents(false, true), 'agent_name', 'agent_id');
	            $data['agent'] = '';
	            if (!empty($player_detail['agent_id'])) {
	                $this->load->model(array('agency_model'));
	                $agent_detail = $this->agency_model->get_agent_by_id($player_detail['agent_id']);
	                $data['agent'] = $agent_detail['agent_name'];
	            }
	            $this->load->view('player_management/adjust_player_agent', $data);
	        }
	    }

		/**
		 * overview : reset parent agent
		 *
		 * details : will load redirect to user information
		 *
		 * @param $player_id	player_id
		 */
		public function playerResetParentAgent($player_id) {
			$player = $this->player_manager->getPlayerById($player_id);
			$agent_id = $this->input->post('agent_id');

			$data = array(
				'agent_id' => $agent_id,
				# When player agent is set, unlink its affiliate
				'affiliateId' => null,
			);

			$this->player_manager->setPlayerAgent($data, $player_id);

			$this->savePlayerUpdateLog($player_id, lang('Set Parent Agent'), $this->authentication->getUsername()); // Add log in playerupdatehistory

	        $username=$player['username'];
	    	//sync
			$this->syncPlayerCurrentToMDBWithLock($player_id, $username, false);

			$message = lang('con.plm31') . " <b>" . $username . " </b> " . lang('Parent Agent is reset') . ".";
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			redirect("player_management/userInformation/" . $player_id);
		}

        /**
         * overview : modify player personal information
         *
         * detail : will load view to editing player personal information
         *
         * @param int $player_id    player_id
         */
        public function editPlayerPersonalInfo($player_id) {
            if (!$this->permissions->checkPermissions('edit_player_personal_information')) {
                $this->error_access();
            } else {
                $data['player'] = $this->player_model->getPlayerInfoById($player_id);
                $data['age'] = $data['player']['birthdate'] == 0 ? 0 : $this->player_manager->get_age($data['player']['birthdate']);
                $data['secretQuestion'] = $this->checkSecQuestionSelected($data['player']['secretQuestion']);

                $this->template->add_css('resources/third_party/bootstrap-datepicker/1.7.0/bootstrap-datepicker.css');
                $this->template->add_js('resources/third_party/bootstrap-datepicker/1.7.0/bootstrap-datepicker.js');
                $this->loadTemplate('Player Management', '', '', 'player');
                $this->template->write_view('sidebar', 'player_management/sidebar');
                $this->template->write_view('main_content', 'player_management/edit_player_personal_information', $data);
                $this->template->render();
            }
        }

		/**
		 * overview : save player information
		 *
		 * detail : will load view to userinformation after saving players data
		 *
		 * @param int $player_id	player_id
		 */
		public function savePlayerPersonalInfo_old($player_id) {
			if (!$this->permissions->checkPermissions('edit_player_personal_information')) {
				return $this->error_access();
			}

			# OG-699 - when admin user editing member info, don't use verification of which field have to present , only verify format and the format limits have to be same as player register.
			$this->form_validation->set_rules('first_name', lang('player.04'), 'trim|xss_clean|required');
			$this->form_validation->set_rules('last_name', lang('player.05'), 'trim|xss_clean');
			$this->form_validation->set_rules('gender', lang('player.57'), 'trim|xss_clean');
			$this->form_validation->set_rules('birthdate', lang('player.17'), 'trim|xss_clean');
			$this->form_validation->set_rules('birthplace', lang('player.58'), 'trim|xss_clean');
			$this->form_validation->set_rules('address', lang('player.59'), 'trim|xss_clean');
			$this->form_validation->set_rules('city', lang('player.19'), 'trim|xss_clean');
			$this->form_validation->set_rules('country', lang('player.20'), 'trim|xss_clean');
			$this->form_validation->set_rules('nationality', lang('player.61'), 'trim|xss_clean');
			$this->form_validation->set_rules('language', lang('player.62'), 'trim|xss_clean');
			$this->form_validation->set_rules('email', lang('pi.6'), 'trim|xss_clean|valid_email');
			$this->form_validation->set_rules('imAccount', lang('player.64'), 'trim|xss_clean');
			$this->form_validation->set_rules('imAccount2', lang('player.65'), 'trim|xss_clean');
			$this->form_validation->set_rules('imAccount3', lang('player.101'), 'trim|xss_clean');
			$this->form_validation->set_rules('secretQuestion', lang('player.66'), 'trim|xss_clean');
			$this->form_validation->set_rules('secretAnswer', lang('player.77'), 'trim|xss_clean');
			$this->form_validation->set_rules('imAccount', lang('player.65'), 'trim|xss_clean');
			$this->form_validation->set_rules('imAccount2', lang('player.65'), 'trim|xss_clean');

			$contactNumber = $this->input->post('contactNumber');
			$check_contactNumber_with_PlayerContactNumber = $this->player_model->checkPhoneNumberIfCorrect($player_id, $contactNumber);
			if ($check_contactNumber_with_PlayerContactNumber){
				$this->form_validation->set_rules('contactNumber', lang('player.63'), 'trim|xss_clean');
			}else{
				//contactNumber value different
				//need check checkContactExist
				$this->form_validation->set_rules('contactNumber', lang('player.63'), 'trim|xss_clean|callback_check_contact');
			}

			if ($this->form_validation->run() == false) {
				$message = validation_errors();
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message); //will set and send message to the user
				$this->editPlayerPersonalInfo($player_id);
			} else {
				$email = $this->input->post('email');
				$playerdetails = array(
					'firstName'       => $this->input->post('first_name'),
					'lastName'        => $this->input->post('last_name'),
					'gender'          => $this->input->post('gender'),
					'birthdate'       => $this->input->post('birthdate'),
					'birthplace'      => $this->input->post('birthplace'),
					'zipcode'         => $this->input->post('zipcode'),
					'region'          => $this->input->post('region'),
					'city'            => $this->input->post('city'),
					'address'         => $this->input->post('address'),
					'address2'        => $this->input->post('address2'),
					'residentCountry' => $this->input->post('country'),
					'citizenship'     => $this->input->post('nationality'),
					'language'        => $this->input->post('language'),
				);

				$player = array(
					'secretQuestion' => $this->input->post('secretQuestion'),
					'secretAnswer' => $this->input->post('secretAnswer'),
				);

				//check permission for contact
				if ($this->permissions->checkPermissions('player_contact_information_contact_number')) {
					$playerdetails['contactNumber'] = $this->input->post('contactNumber');
				}
				if ($this->permissions->checkPermissions('player_contact_information_im_accounts')) {
					$playerdetails['imAccount'] = $this->input->post('imAccount');
					$playerdetails['imAccount2'] = $this->input->post('imAccount2');
					$playerdetails['imAccount3'] = $this->input->post('imAccount3');
				}
				if ($this->permissions->checkPermissions('player_contact_information_email')) {
					$player['email'] = $this->input->post('email');
				}

				if($this->utils->getConfig('multiple_currency_enabled')) {
					$player['currency'] = $this->input->post('currency');
				}

				$old_data = $this->player_manager->getPlayerById($player_id);
				$username=$old_data['username'];
				if ($this->permissions->checkPermissions('player_contact_information_email')) {
					if ($email != $old_data['email'] && !empty($email)) {
						$checkIfEmailExist = $this->player_manager->checkEmailExist($email);

						if ($checkIfEmailExist) {
							$this->alertMessage(self::MESSAGE_TYPE_ERROR, $email . ' ' . lang('con.plm06')); //will set and send message to the user
							redirect("player_management/editPlayerPersonalInfo/" . $player_id);
						}
					}
				}

				$checkContactModified = $this->checkContactModified($old_data, array_merge($player, $playerdetails));

				if ($checkContactModified) {
					if (isset($checkContactModified['email'])) {
						$player['verified_email'] = self::DEFAULT_VALUE_VERIFIED_EMAIL;
					}
					if (isset($checkContactModified['contactNumber'])) {
						$player['verified_phone'] = self::DEFAULT_VALUE_VERIFIED_PHONE;
					}
				}

				$modifiedFields = $this->checkModifiedFields($player_id, array_merge($player, $playerdetails));

				#OGP-13922 if xinyan status = 1 and edit player name or contactNumber, update status to enable the xinyan validation button
				if($this->utils->isEnabledFeature('enable_show_trigger_XinyanApi_validation_btn')) {
					$this->load->model(array('player_model','player_api_verify_status'));

					$status           = $this->player_api_verify_status->getApiStatusByPlayerId($player_id);
					$playerDetails 	  = $this->CI->player_model->getPlayerDetails($player_id);
					$getcontactNumber = $this->player_model->getPlayerContactNumber($player_id);
					$getPlayerName    = $playerDetails[0]['firstName'];
					$playerNmae       = $this->input->post('first_name');

					if($status == player_api_verify_status::API_RESPOSE_SUCCESS){
						if($contactNumber != $getcontactNumber || $playerNmae != $getPlayerName){
							$this->player_api_verify_status->updateApiStatusByPlayerId($player_id, player_api_verify_status::API_UNKNOWN);
							$this->CI->utils->debug_log('===========================update xinyan status success by admin');
						}
					}
				}

				$this->player_manager->editPlayer($player, $player_id);
				$this->player_manager->editPlayerDetails($playerdetails, $player_id);

				$this->savePlayerUpdateLog($player_id, lang('lang.edit') . ' ' . lang('lang.playerinfo') . ' (' . $modifiedFields . ')', $this->authentication->getUsername()); // Add log in playerupdatehistory
				//sync lock
				$this->syncPlayerCurrentToMDBWithLock($player_id, $username, false);

				$this->load->library(array('agency_library'));
				$log_params = array(
					'action' => 'modify_player',
					'link_url' => site_url('player_management/editPlayerPersonalInfo') . '/' . $player_id,
					'done_by' => $this->authentication->getUsername(),
					'done_to' => $username,
					'details' => 'Edit player personal info: ' . $modifiedFields,
				);
				$this->agency_library->save_action($log_params);

				$message = lang('con.plm61');
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message); //will set and send message to the user
				redirect("player_management/userInformation/" . $player_id);
			}
		}

		/**
		 * overview : check contact field is modified
		 *
		 * @param $player_id	player_id
		 * @param $new_data
		 * @return string
		 */
		public function checkContactModified($old_data, $new_data) {
			$diff = array_diff_assoc($new_data, $old_data);
			return $diff;

			foreach ($diff as $key => $value) {
				$changes[lang('reg.fields.' . $key) ?: $key] = [
					'old' => $old_data[$key],
					'new' => $new_data[$key],
				];
			}
			return $diff;
		}
	//-----------------------------------------------old userInformation----------------------------------------------------------


    public function new_userInformation($player_id, $tab = 1) {
        if (!$this->permissions->checkPermissions('player_list') || empty($player_id)) {
            $this->error_access();
        } else {
            $data['tab']    = $tab;
            $data['player'] = $this->player_model->getPlayerInfoById($player_id);

            if ($this->utils->isEnabledFeature('show_pep_status') && $this->utils->isEnabledFeature('show_risk_score')) {
                $data['pep_status'] = $this->check_risk_player_PEP($player_id);
            }

            if ($this->utils->isEnabledFeature('show_c6_status') && $this->utils->isEnabledFeature('show_risk_score')) {
                $data['c6_status'] = $this->check_risk_player_c6($player_id);
            }

            if ($this->utils->isEnabledFeature('show_risk_score')) {
                $data['risk_score'] = $this->generate_total_risk_score($player_id);
                $data['risk_level'] = $this->risk_score_model->getPlayerCurrentRiskLevel($player_id);
            }

            if ($this->utils->isEnabledFeature('show_kyc_status')) {
                $data['kyc_status'] = $this->getPlayerCurrentStatus($player_id);
                $data['kyc_level'] = $this->player_kyc->getPlayerCurrentKycLevel($player_id);
            }

            if ($this->utils->isEnabledFeature('show_allowed_withdrawal_status') && $this->utils->isEnabledFeature('show_risk_score') && $this->utils->isEnabledFeature('show_kyc_status')) {
                $data['allowed_withdrawal_status'] = ($this->generate_allowed_withdrawal_status($player_id)) ? lang('Yes') : lang('No');
            }

        	$data['tags'] = $this->player->getAllTagsOnly();

            $this->template->add_css('resources/third_party/bootstrap-multiselect-master/dist/css/bootstrap-multiselect.css');
            $this->template->add_js('resources/third_party/bootstrap-multiselect-master/dist/js/bootstrap-multiselect.js');

            $this->template->add_css('resources/third_party/bootstrap-tagsinput/2.3.2/dist/bootstrap-tagsinput.css');
            $this->template->add_js('resources/third_party/bootstrap-tagsinput/2.3.2/dist/bootstrap-tagsinput.min.js');

            $this->template->add_css('resources/css/player_management/player_information.css');
            $this->template->add_js('resources/js/player_management/view_user_information.js');

        	$this->template->add_css('resources/third_party/bootstrap-datepicker/1.7.0/bootstrap-datepicker.css');
        	$this->template->add_js('resources/third_party/bootstrap-datepicker/1.7.0/bootstrap-datepicker.js');
        	$this->template->add_js('resources/third_party/bootstrap-datepicker/1.7.0/locales/bootstrap-datepicker.en-US.js');
        	$this->template->add_js('resources/third_party/bootstrap-datepicker/1.7.0/locales/bootstrap-datepicker.zh-CN.js');
        	$this->template->add_js('resources/third_party/bootstrap-datepicker/1.7.0/locales/bootstrap-datepicker.id.js');
        	$this->template->add_js('resources/third_party/bootstrap-datepicker/1.7.0/locales/bootstrap-datepicker.ko.js');
        	$this->template->add_js('resources/third_party/bootstrap-datepicker/1.7.0/locales/bootstrap-datepicker.th.js');
        	$this->template->add_js('resources/third_party/bootstrap-datepicker/1.7.0/locales/bootstrap-datepicker.vi.js');

			$this->loadTemplate(lang('Player Management'), '', '', 'player');

			$this->template->add_js('resources/js/dataTables.responsive.min.js');
			$this->template->add_css('resources/css/dataTables.responsive.css');

            $this->template->write_view('sidebar', 'player_management/sidebar');
            $this->template->write_view('main_content', 'player_management/user_information/view_user_information', $data);
            $this->template->render();
        }
    }
    //-----------------------------------------------userInformation----------------------------------------------------------
        public function triggerRegisterEventForXinyanApi($playerId){
            $this->utils->debug_log('============================triggerRegisterEventForXinyanApi',$playerId);

            $this->load->model(array('player_model','player_api_verify_status'));

            if(!empty($playerId)){
                $player = $this->player_model->getPlayerArrayById($playerId);
                $playerDetial = $this->player_model->getPlayerDetails($playerId);
            }

            $register_options = $this->utils->getConfig('register_event_xinyan_api');
            $new_dispatch_account_level = $register_options['assign_members_in_specific_dispatc_level'];
            $username = $player['username'];
            $response = $this->xinyanapi->submitToXinyanApi($playerId,$player,$playerDetial);;

            if(!isset($response['success'])){

                $this->player_api_verify_status->updateApiStatusByPlayerId($playerId, player_api_verify_status::API_UNKNOWN);
                $this->CI->utils->debug_log('==============submitToXinyanApi no response');
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Xinyang is not responding'));

            }
            else if($response['success'] && $response['data']['code'] == '0'){

                $this->player_api_verify_status->updateApiStatusByPlayerId($playerId, player_api_verify_status::API_RESPOSE_SUCCESS);
                $default_dispatch_account_level_id = ($this->CI->config->item('default_dispatch_account_level_id')) ? $this->CI->config->item('default_dispatch_account_level_id') : 1;
                $oldlevel = $this->player_model->getPlayerDispatchAccountLevel($playerId);
                if($oldlevel['dispatch_account_level_id'] == $new_dispatch_account_level){
                    $this->xinyanapi->assignToDispatchAccount($playerId, $username, $default_dispatch_account_level_id);
                }
                $this->CI->utils->debug_log('==============submitToXinyanApi response verify success', $response);
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Member Verified Successfully'));

            }else{

                $this->player_api_verify_status->updateApiStatusByPlayerId($playerId, player_api_verify_status::API_RESPOSE_FAIL);
                $this->CI->utils->debug_log('==============submitToXinyanApi response verify failed and assing to new Dispatch Account group', $response);
                $this->xinyanapi->assignToDispatchAccount($playerId, $username, $new_dispatch_account_level);
                $this->alertMessage(self::MESSAGE_TYPE_WARNING, lang('Verification Failed and Member been Assigned in XinYan Group'));

            }
            redirect('player_management/userInformation/' . $playerId);
        }

        public function getXinyanApiStatus($playerId){
            $this->load->model(array('player_api_verify_status'));
            $status = $this->player_api_verify_status->getApiStatusByPlayerId($playerId);
            $response['success'] = false;

            if(isset($status)){
                $response = [
                    'success' => true,
                    'status' => $status
                ];
                return $this->returnJsonResult($response);
            }else{
                return $this->returnJsonResult($response);
            }
        }
    //-----------------------------------------------userInformation----------------------------------------------------------


    public function changeUserInfoTab($player_id, $tab = 1) {
        if (!$this->permissions->checkPermissions('player_list') || empty($player_id)) {
            $this->error_access();
        } else {
            switch ($tab) {
                case 1:
                    $this->ajaxSignupInfo($player_id);
                    break;
                case 2:
	                if ($this->permissions->checkPermissions('player_basic_info')) {
	                    $this->ajaxBasicInfo($player_id);
	                }
                    break;
                case 3:
                    if (false && $this->utils->isEnabledFeature('show_upload_documents') && $this->permissions->checkPermissions('kyc_attached_documents')) {
                        $this->ajaxKycAttach($player_id);
                    }
                    break;
                case 4:
                    if ($this->utils->isEnabledFeature('responsible_gaming') && $this->permissions->checkPermissions('responsible_gaming_info')) {
                        $this->ajaxResponsibleGaming($player_id);
                    }
                    break;
                case 5:
                    $this->ajaxFinInfo($player_id);
                    break;
                case 6:
                    $this->ajaxWithdrawalCondition($player_id);
                    break;
                case 7:
	                if ($this->utils->isEnabledFeature('enabled_transfer_condition')) {
	                    $this->ajaxTransferCondition($player_id);
	                }
                    break;
                case 8:
                    $this->ajaxAccountInfo($player_id);
                    break;
                case 9:
                    $this->ajaxGameInfo($player_id);
                    break;
                case 10:
                    $this->ajaxPlayerLog($player_id);
                    break;
                default:
                    break;
            }
        }
    }

    public function ajaxSignupInfo($player_id) {
        if (!$this->permissions->checkPermissions('player_list') || empty($player_id)) {
            $this->error_access();
        } else {
            $data['player'] = $this->player_model->getPlayerInfoById($player_id);
            $player_viplevel         = $this->player_model->getPlayerCurrentLevel($player_id);
            $player_dispatch_account = $this->player_model->getCurrentDispatchAccountLevel($player_id);
            $player_registrationIp   = $this->player_model->registrationIP($player_id);
            $player_affiliate        = $this->player_manager->getAffiliateOfPlayer($player_id);
            $player_agent            = $this->player_model->getAgentByPlayerId($player_id);
            $player_tags             = $this->player_model->getPlayerTags($player_id, TRUE);

            $data['player_viplevel']         = !empty($player_viplevel) ? $player_viplevel[0] : NULL;
            $data['player_dispatch_account'] = !empty($player_dispatch_account) ? $player_dispatch_account[0] : NULL;
            $data['player_registrationIp']   = !empty($player_registrationIp) ? $player_registrationIp : NULL;
            $data['player_affiliate']        = !empty($player_affiliate) ? $player_affiliate : NULL;
            $data['player_agent']            = !empty($player_agent) ? $player_agent['agent_name']: NULL;
            $data['player_tags']             = !empty($player_tags) ? $player_tags : NULL;

            $data['refereePlayerId'] = $this->player_model->getRefereePlayerId($player_id);
            $data['refereePlayer']   = $this->player_model->getPlayerById($data['refereePlayerId']);

            $data['all_vip_levels']      = $this->player_model->getAllPlayerLevels();
            $data['all_dispatch_levels'] = $this->player_model->getAllDispatchAaccountLevels();
            $data['all_tag_list']        = $this->player->getAllTagsOnly();

            if(empty($data['refereePlayerId']) && empty($data['player_affiliate']) && empty($data['player_agent'])){
                $this->load->model(array('affiliate','agency_model'));

                $affiliates = $this->affiliate->getAffiliates();
                $data['all_affiliates'] = array_column(is_array($affiliates) ? $affiliates : array(), 'username', 'affiliateId');

                $agents = $this->agency_model->get_active_agents(false, true);
                $data['all_agents'] = array_column(is_array($agents) ? $agents : array(), 'agent_name', 'agent_id');
            }

            $referrals = $this->player_manager->getAllReferralByPlayerId($player_id);
            $data['referral_count']  = ($referrals == false) ? 0 : count($referrals);

            $this->load->view('player_management/user_information/ajax_signup_info', $data);
        }
    }
    //-----------------------------------------------ajaxSignupInfo----------------------------------------------------------
        public function manuallyUpgradeLevel($player_id) {
            $this->load->model(array('group_level'));

            $this->group_level->setGradeRecord([
                'request_type'  => Group_level::REQUEST_TYPE_MANUAL_GRADE,
                'request_grade' => Group_level::RECORD_UPGRADE,
                'request_time'  => date('Y-m-d H:i:s')
            ]);

            $result = $this->group_level->batchUpDownLevelUpgrade($player_id, false);

            if (!empty($result['success'])) {
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $result['success']);
            } else {
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $result['error']);
            }

            redirect('player_management/userInformation/' . $player_id);
        }

        public function manuallyDowngradeLevel($player_id) {
            $this->load->model(array('group_level'));

            $this->group_level->setGradeRecord([
                'request_type'  => Group_level::REQUEST_TYPE_MANUAL_GRADE,
                'request_grade' => Group_level::RECORD_DOWNGRADE,
                'request_time'  => date('Y-m-d H:i:s')
            ]);

            $result = $this->group_level->batchUpDownLevelDowngrade($player_id, false);

            if (!empty($result['success'])) {
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $result['success']);
            } else {
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $result['error']);
            }

            redirect('player_management/userInformation/' . $player_id);
        }

        /**
         * overview : login as player
         *
         * @param int $player_id    player_id
         */
        public function login_as_player($player_id) {
            if($this->utils->isEnabledFeature('strictly_cannot_login_player_when_block')){
                if ($this->player_model->isBlocked($player_id)) {
                    $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('player.blocked'));
                    redirect('/player_management/userInformation/'.$player_id);
                }
            }

            if (!$this->permissions->checkPermissions('login_as_player')) {
                $this->error_access();
            } else {
                $adminUserId = $this->authentication->getUserId();

                $token = $this->getAdminToken($adminUserId);
                if ($token) {
                    $theTargetCurrency = $this->utils->getActiveTargetDB();
                    $preString = 'player';

                    // currency subdomains
                    $auto_domain_switcher_group_list=config_item('auto_domain_switcher_group_list');
                    $domain_to_currency_key=config_item('domain_to_currency_key');
                    if( $this->utils->isEnabledMDB()
                        && (    ! empty($auto_domain_switcher_group_list)
                                || ! empty($domain_to_currency_key)
                        )
                    ){

                        if( method_exists($this->utils,'autoSwitchDomainByDB') ){ // only in mdb branch.
                            /// for og_super, All currency.
                            // try switch to Target Currency, empty for go to default Currency
                            $domain=$this->utils->getHttpHost();
                            $autoSwitchDomain = $this->utils->autoSwitchDomainByDB($domain, $theTargetCurrency);
                            if( is_null($autoSwitchDomain) ){// go to default Currency
                                $playerInfo = $this->player_model->getPlayerArrayById($player_id);
                                $theTargetCurrency = $playerInfo['currency'];
                            }
						}
						if( ! empty($theTargetCurrency) ){
							/// for Currency Domain.
							$preString .= '.'. $theTargetCurrency;
						}
                    }
                    $url = $this->utils->getSystemUrl($preString, '/iframe/auth/login_from_admin/' . $token . '/' . $player_id);
                    $this->utils->appendDBToUrl($url, $theTargetCurrency); // self::appendActiveDBToUrl() ref.
                    $this->utils->debug_log('login_as_player url', $url);
                    redirect($url);
                } else {
                    $this->error_access();
                }
            }
        }

        public function kickPlayer($player_id) {
        	$this->load->model(['common_token']);
            $this->load->library(['player_library']);

            $username = $this->player_model->getUsernameById($player_id);

            $kickedPlayer = $this->player_library->kickPlayer($player_id);
            if($kickedPlayer){
                $this->player_library->kickPlayerGamePlatform($player_id, $username);
                //remove current player token
                $this->common_token->kickoutByPlayerId($player_id);
            }

            $this->savePlayerUpdateLog(
                $player_id,
                lang('role.27') . ' - ' . lang('adjustmenthistory.title.beforeadjustment') . ' (' . lang('lang.logIn') . ') ' .
                lang('adjustmenthistory.title.afteradjustment') . ' (' . lang('player.ol03') . ' - ' . lang('player.ol01') . ') ',
                $this->authentication->getUsername()
            );

            $this->syncPlayerCurrentToMDBWithLock($player_id, $username, false);

            $message = lang('con.plm69') . ' ' . lang('player.ol01');
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
            redirect('player_management/userInformation/' . $player_id);
        }

        public function generateReferralCode($player_id) {
            $adminId = $this->authentication->getUserId();

            // -- Check if user is not logged in
            if(empty($adminId)){

                $response = [
                    'status' => BaseController::MESSAGE_TYPE_ERROR,
                    'message' => lang('pay.bt.error.occured')
                ];

                return $this->returnJsonResult($response);

            }

            // -- Retrieve player information based on player ID
            $this->load->model('player_model');
            $player = $this->player_model->getPlayerInfoById($player_id);

            // -- Validate player's existence
            if(!$player){

                $response = [
                    'status' => BaseController::MESSAGE_TYPE_ERROR,
                    'message' => lang('Player does not exist')
                ];

                return $this->returnJsonResult($response);

            }

            // -- Check if selected player has no invitation/referral code
            if($player['invitationCode'] == "0" || trim($player['invitationCode']) == ""){

                $referral_code = $this->player_model->generateReferralCodePerPlayer($player_id);

                $response = [
                        'status' => BaseController::MESSAGE_TYPE_SUCCESS,
                        'message' => lang('referral_code_successfully_generated'),
                        'data' => $referral_code
                    ];

                if(!$referral_code){
                    $response = [
                        'status' => BaseController::MESSAGE_TYPE_ERROR,
                        'message' => lang('error.default.db.message')
                    ];
                }

                return $this->returnJsonResult($response);
            }

            // -- Return error if the player already has an invitation code
            $response = [
                'status' => BaseController::MESSAGE_TYPE_ERROR,
                'message' => lang('referral_code_exists')
            ];

            return $this->returnJsonResult($response);
        }

        public function adjustVipLevelThruAjax() {
            $playerId = $this->input->post('playerId');
            $newPlayerLevel = $this->input->post('newPlayerLevel');

            $this->load->model(array('group_level'));
            $this->group_level->startTrans();
                $oldlevel = $this->player_manager->getPlayerLevel($playerId);
                $this->group_level->adjustPlayerLevel($playerId, $newPlayerLevel);
                $level    = $this->player_manager->getPlayerLevel($playerId);
                $this->saveAction(
                    self::ACTION_MANAGEMENT_TITLE,
                    lang('player.46'),
                    "User " . $this->authentication->getUsername() . " has adjusted vip level of player '" . $playerId . "'"
                );
                $this->savePlayerUpdateLog(
                    $playerId,
                    lang('player.46') . ' - ' . lang('adjustmenthistory.title.beforeadjustment') . ' (' . lang($oldlevel['groupName']) . ' - ' . lang($oldlevel['vipLevelName']) . ') ' .
                    lang('adjustmenthistory.title.afteradjustment') . ' (' . lang($level['groupName']) . ' - ' . lang($level['vipLevelName']) . ') ',
                    $this->authentication->getUsername()
                );
            $this->group_level->endTrans();

            $this->group_level->setGradeRecord([
                'player_id' => $playerId,
                'request_type'  => Group_level::REQUEST_TYPE_SPECIFIC_GRADE,
                'request_grade' => Group_level::RECORD_SPECIFICGRADE,
                'updated_by'    => $this->authentication->getUserId(),
                'newvipId'      => $newPlayerLevel,
                'vipsettingId'  => $oldlevel['vipSettingId'],
                'vipsettingcashbackruleId' => $oldlevel['playerGroupId'],
                'level_from' => $oldlevel['vipLevel'],
                'level_to'   => $level['vipLevel'],
                'request_time'  => date('Y-m-d H:i:s'),
                'pgrm_start_time' => date('Y-m-d H:i:s'),
                'pgrm_end_time'   => date('Y-m-d H:i:s'),
                'status'          => Group_level::GRADE_SUCCESS
            ]);
            $this->group_level->gradeRecode(false);

            if ($this->group_level->isErrorInTrans()) {
                $arr = array('status' => 'error', 'message' => lang('text.error'));
            } else {
                $current_player_level = $this->player_model->getPlayerCurrentLevel($playerId);
                array_walk($current_player_level, function(&$row){
                    $row['groupName'] = lang($row['groupName']);
                    $row['vipLevelName'] = lang($row['vipLevelName']);
                });
                $arr = array('status' => 'success', 'current_player_level' => $current_player_level);
            }

            $this->returnJsonResult($arr);
        }

        public function adjustDispatchAccountLevelThruAjax() {
            $playerId         = $this->input->post('playerId');
            $newDispatchLevel = $this->input->post('newDispatchLevel');

            $this->player_model->startTrans();
                $oldlevel = $this->player_manager->getPlayerDispatchAccountLevel($playerId);
                $this->player_model->adjustDispatchAccountLevel($playerId, $newDispatchLevel);
                $level    = $this->player_manager->getPlayerDispatchAccountLevel($playerId);

                $this->saveAction(
                    self::ACTION_MANAGEMENT_TITLE,
                    lang('player.100'),
                    "User " . $this->authentication->getUsername() . " has adjusted dispatch account level of player '" . $playerId . "'"
                );
                $this->savePlayerUpdateLog(
                    $playerId,
                    lang('player.100').' - '.lang('adjustmenthistory.title.beforeadjustment').' ('.lang($oldlevel['group_name']).' - '.$oldlevel['level_name'].') '.
                    lang('adjustmenthistory.title.afteradjustment').' ('.lang($level['group_name']).' - '.$level['level_name'].') ',
                    $this->authentication->getUsername()); // Add log in playerupdatehistory
            $this->player_model->endTrans();

            if ($this->player_model->isErrorInTrans()) {
                $arr = array('status' => 'error', 'message' => lang('text.error'));
            } else {
                $current_dispatch_account_level = $this->player_model->getCurrentDispatchAccountLevel($playerId);
                array_walk($current_dispatch_account_level, function(&$row) {
                    $row['group_name'] = lang($row['group_name']);
                    $row['level_name'] = lang($row['level_name']);
                });
                $arr = array('status' => 'success', 'current_dispatch_account_level' => $current_dispatch_account_level);
            }

            $this->returnJsonResult($arr);
        }

        public function assignPlayerAffiliateThruAjax() {
            $playerId    = $this->input->post('playerId');
            $affiliateId = $this->input->post('affiliateId');

            $current_affiliate = $this->player_manager->addAffiliateToPlayer($playerId, $affiliateId);
            $this->savePlayerUpdateLog(
                $playerId,
                lang('player.ui71').' - '.lang('adjustmenthistory.title.beforeadjustment').' ('.lang('affiliate.no.affiliate').') '.
                lang('adjustmenthistory.title.afteradjustment') . ' (' . $current_affiliate . ') ',
                $this->authentication->getUsername()
            );

            $username = $this->player_model->getUsernameById($playerId);
            $this->syncPlayerCurrentToMDBWithLock($playerId, $username, false);

            $this->saveAction(
                self::ACTION_MANAGEMENT_TITLE,
                lang('player.ui71') . ' : ' . lang('player.ufr01') . '(' . $playerId . ') - ' . lang('adjustmenthistory.title.afteradjustment') . ' (' . $current_affiliate . ') ',
                "User " . $this->authentication->getUsername() . " has adjusted player '" . $playerId . "'"
            );

            $arr = array('status' => 'success', 'current_affiliate' => $current_affiliate);

            $this->returnJsonResult($arr);
        }

        public function assignPlayerAgentThruAjax() {
            $playerId = $this->input->post('playerId');
            $agentId  = $this->input->post('agentId');
			$data = array('agent_id' => $agentId);

            $this->player_manager->setPlayerAgent($data, $playerId);
            $current_agent = $this->player_model->getAgentByPlayerId($playerId);
            $this->savePlayerUpdateLog($playerId, lang('Set Parent Agent'), $this->authentication->getUsername());

            $username = $this->player_model->getUsernameById($playerId);
            $this->syncPlayerCurrentToMDBWithLock($playerId, $username, false);

            $this->saveAction(
                self::ACTION_MANAGEMENT_TITLE,
                lang('Add Agent') . ' : ' . lang('player.ufr01') . '(' . $playerId . ') - ' . lang('adjustmenthistory.title.afteradjustment') . ' (' . $current_agent['agent_name'] . ') ',
                "User " . $this->authentication->getUsername() . " has adjusted player '" . $playerId . "'"
            );

            $arr = array('status' => 'success', 'current_agent' => $current_agent['agent_name']);

            $this->returnJsonResult($arr);
        }

        public function adjustPlayerTaglThruAjax() {
            $playerId = $this->input->post('playerId');
            $tagIds   = $this->input->post('tagId');

            $tagged = $this->player_model->getPlayerTags($playerId);
            if ($tagged === false) {
                $tagged = [];
            }

            $user_id   = $this->authentication->getUserId();
            $user_name = $this->authentication->getUsername();
            $today     = date("Y-m-d H:i:s");

            #if tagId is not equal to zero
            if ($tagIds) {
                #delete not exists the tag id
                $playerTaggedIds = [];
                $playerUnTaggedIds = [];
                $playerUnTagged = [];
                $latestTag = '';
                $unlinkTag = '';

                foreach ($tagged as $playerTag) {
                    if (in_array($playerTag['tagId'], $tagIds)) {
                        $playerTaggedIds[] = $playerTag['tagId'];
                        continue;
                    } else {
                        $playerUnTaggedIds[] = $playerTag['playerTagId'];
                        $playerUnTagged[] = $playerTag;
                    }
                }

                foreach ($tagIds as $tagId) {
                    if (in_array($tagId, $playerTaggedIds)) {
                        continue;
                    }
                    $data = array(
                        'playerId' => $playerId,
                        'taggerId' => $user_id,
                        'tagId' => $tagId,
                        'createdOn' => $today,
                        'updatedOn' => $today,
                        'status' => 1,
                    );
                    $newTag = $this->player_model->insertAndGetPlayerTag($data);
                    $this->saveAction(
                        self::ACTION_MANAGEMENT_TITLE,
                        lang('adjustmenthistory.title.beforeadjustment') . ' (' . lang('player.tp03') . ') ' . lang('adjustmenthistory.title.afteradjustment') . ' (' . $newTag . ') ',
                        "User " . $user_name . " has adjusted player '" . $playerId . "'"
                    );

                    $newTagIds[] =  $data['tagId'];
                    $this->utils->debug_log('the $newTagIds ---->', $newTagIds);
                }

                if(!empty($newTagIds)){
                    $playerNewTags = $this->player->getTagDetails($newTagIds);
                    $oldTag = '';
                    if(empty($tagged)){
                        $oldTag = lang('player.tp03');
                    }else{
                        foreach($tagged as $val) {
                            $oldTag .= " <span class='tag label label-info' style='background-color:".$val['tagColor']."'>".$val['tagName']."</span>";
                        }
                    }

                    foreach($playerNewTags as $res){
                        $latestTag .= " <span class='tag label label-info' style='background-color:".$res['tagColor']."'>".$res['tagName']."</span>";
                    }
                    $this->savePlayerUpdateLog($playerId, lang('player.26') . ' - ' .
                        lang('adjustmenthistory.title.beforeadjustment') . ' (' . $oldTag . ' ) ' .
                        lang('adjustmenthistory.title.afteradjustment') . ' ( added ' . $latestTag . ') ',
                        $user_name
                    );
                }

                if(!empty($playerUnTagged)){
                    $tagged = $this->player_model->getPlayerTags($playerId);
                    $this->player->removePlayerTag($playerUnTaggedIds);
                    $oldTag = '';
                    foreach($tagged as $val) {
                        $oldTag .= " <span class='tag label label-info' style='background-color:".$val['tagColor']."'>".$val['tagName']."</span>";
                    }
                    foreach($playerUnTagged as $res){
                        $unlinkTag .= " <span class='tag label label-info' style='background-color:".$res['tagColor']."'>".$res['tagName']."</span>";
                    }
                    $this->savePlayerUpdateLog(
                        $playerId,
                        lang('player.26').' - '.lang('adjustmenthistory.title.beforeadjustment').' ('.$oldTag.' ) '.lang('adjustmenthistory.title.afteradjustment').' ( removed '.$unlinkTag.') ',
                        $user_name,
                        $today
                    );
                }

                $arr = array('status' => 'success', 'currentPlayerTags' => player_tagged_list($playerId));
            } else {
                # if tagId is zero update to no tag
                #delete player tag
                $newTag = $this->player->deleteAndGetPlayerTag($playerId);

                $source_tag = [];
                foreach ($tagged as $playerTag) {
                    $source_tag[] = $playerTag['tagName'];
                }

                $this->savePlayerUpdateLog(
                    $playerId,
                    lang('player.26').' - '.lang('adjustmenthistory.title.beforeadjustment').' ('.implode(',', $source_tag). ') '.lang('adjustmenthistory.title.afteradjustment').' ('.lang('player.tp10').') ',
                    $user_name
                );

                $this->saveAction(
                    self::ACTION_MANAGEMENT_TITLE,
                    lang('adjustmenthistory.title.beforeadjustment').' ('.implode(',', $source_tag).') '.lang('adjustmenthistory.title.afteradjustment').' ('.lang('player.tp10').') ',
                    "User ".$user_name." has adjusted player '".$playerId."'"
                );

                if ($newTag) {
                    $arr = array('status' => 'empty', 'message' => lang('Select Tag'));
                } else {
                    $arr = array('status' => 'error', 'message' => lang('text.error'));
                }
            }
            $this->returnJsonResult($arr);
        }

        public function updatePlayerDetailsVerification($status, $player_id){
            $data = array(
                'manual_verification' => $status
            );
            try{
                $old_data = $this->player->getPlayerById($player_id);

                $this->player_manager->editPlayerDetails($data, $player_id);
                $message = lang('con.plm61');

                if(!empty($old_data)){
                    if(isset($old_data['manual_verification'])){
                        if($old_data['manual_verification']){
                        $old = lang('Verified');
                        } else {
                            $old = lang('Not Verified');
                        }

                        if($status){
                            $new = lang('Verified');
                        } else {
                            $new = lang('Not Verified');
                        }

                        $modifiedFields = "<ul><li><code>Old: {$old}</code><br><code>New: {$new}</code></li>";

                        $this->savePlayerUpdateLog($player_id, lang('lang.edit') . ' ' . lang('Verified') . ' ' . lang('Player Status') . ' (' . $modifiedFields . ')', $this->authentication->getUsername());
                    }
                }

                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
            }catch (Exception $e) {
                $message = lang('Error on saving!');
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                return $e->getMessage();
            }
        }

        public function updatePlayerNewsletterSubscription($status, $player_id){
            $data = array(
                'newsletter_subscription' => $status
            );
            try{
                $old_data = $this->player_model->getPlayerById($player_id);

                $this->player_model->updatePlayer($player_id, $data);
                $message = lang('con.plm61');

                if(!empty($old_data)){
                    if(isset($old_data->newsletter_subscription)){
                        if($old_data->newsletter_subscription){
                            $old = lang('Subscribed');
                        } else {
                            $old = lang('Unsubscribed');
                        }

                        if($status){
                            $new = lang('Subscribed');
                        } else {
                            $new = lang('Unsubscribed');
                        }

                        $modifiedFields = "<ul><li><code>Old: {$old}</code><br><code>New: {$new}</code></li>";

                        $this->savePlayerUpdateLog($player_id, lang('lang.edit') . ' ' . lang('Newsletter Subscription') . ' ' . lang('Player Status') . ' (' . $modifiedFields . ')', $this->authentication->getUsername());
                    }
                }
                $this->returnJsonResult($arr);
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
            }catch (Exception $e) {
                $message = lang('Error on saving!');
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                return $e->getMessage();
            }
        }
    //-----------------------------------------------ajaxSignupInfo----------------------------------------------------------


    public function ajaxBasicInfo($player_id) {
        if (!$this->permissions->checkPermissions('player_basic_info') || empty($player_id)) {
            $this->error_access();
        } else {
            $this->load->model('communication_preference_model');
            $data['player'] = $this->player_model->getPlayerInfoById($player_id);

            $birthdate = $data['player']['birthdate'];
            $data['player']['age'] = empty($birthdate) ? NULL : DateTime::createFromFormat('Y-m-d', $birthdate)->diff(new DateTime('now'))->y;

            $this->player_responsible_gaming_library->fetchResponsibleGamingDataForSBE($player_id);
            $data['current_comm_pref'] = $this->communication_preference_model->getCurrentPreferences($player_id);
            $data['full_address_in_one_row'] = $this->operatorglobalsettings->getSettingJson('full_address_in_one_row');

            $this->load->view('player_management/user_information/ajax_basic_info', $data);
        }
    }
    //-----------------------------------------------ajaxBasicInfo----------------------------------------------------------
        public function savePlayerPersonalInfo($player_id) {
            if (!$this->permissions->checkPermissions('player_basic_info') || !$this->permissions->checkPermissions('edit_player_personal_information')) {
                return $this->error_access();
            } else {
                $this->form_validation->set_rules('lastName', lang('player.05'), 'strip_tags|trim|xss_clean|max_length[48]|callback_only_characters');
                $this->form_validation->set_rules('firstName', lang('player.04'), 'strip_tags|trim|xss_clean|max_length[48]|callback_only_characters');
                $this->form_validation->set_rules('gender', lang('player.57'), 'strip_tags|trim|xss_clean');
                $this->form_validation->set_rules('birthdate', lang('player.17'), 'strip_tags|trim|xss_clean'); //
                $this->form_validation->set_rules('birthplace', lang('player.58'), 'strip_tags|trim|xss_clean|max_length[120]|callback_only_characters');
                $this->form_validation->set_rules('citizenship', lang('player.61'), 'strip_tags|trim|xss_clean');
                $this->form_validation->set_rules('player_language', lang('player.62'), 'strip_tags|trim|xss_clean'); //
                $this->form_validation->set_rules('zipcode', lang('player.60'), 'strip_tags|trim|xss_clean|max_length[36]');
                $this->form_validation->set_rules('residentCountry', lang('player.20'), 'strip_tags|trim|xss_clean');
                $this->form_validation->set_rules('region', lang('a_reg.37.placeholder'), 'strip_tags|trim|xss_clean|max_length[120]');
                $this->form_validation->set_rules('city', lang('a_reg.36.placeholder'), 'strip_tags|trim|xss_clean|max_length[120]');
                $this->form_validation->set_rules('address', lang('a_reg.43.placeholder'), 'strip_tags|trim|xss_clean|max_length[120]');
                $this->form_validation->set_rules('address2', lang('a_reg.44.placeholder'), 'strip_tags|trim|xss_clean|max_length[120]'); //
                $this->form_validation->set_rules('secretQuestion', lang('player.66'), 'strip_tags|trim|xss_clean');
                $this->form_validation->set_rules('secretAnswer', lang('player.77'), 'strip_tags|trim|xss_clean|max_length[36]');

                //set form validation language
                $this->form_validation->set_message('max_length', lang('formvalidation.max_length'));

                if ($this->form_validation->run() == false) {
                    $message = $this->form_validation->error_array();
                    $result = array('status' => 'failed', 'message' => $message);
                    $this->returnJsonResult($result);
                } else {
                    if($this->utils->isEnabledFeature('enable_show_trigger_XinyanApi_validation_btn')) {
                        $this->load->model('player_api_verify_status');

                        $status         = $this->player_api_verify_status->getApiStatusByPlayerId($player_id);
                        $origin_details = $this->player_model->getPlayerDetails($player_id);

                        #OGP-13922 if xinyan status = 1 and edit player first name, update status to enable the xinyan validation button
                        if($status == player_api_verify_status::API_RESPOSE_SUCCESS){
                            if($this->input->post('firstName') != $origin_details[0]['firstName']){

                                $this->player_api_verify_status->updateApiStatusByPlayerId($player_id, player_api_verify_status::API_UNKNOWN);
                                $this->CI->utils->debug_log('===========================update xinyan status success by admin');
                            }
                        }
                    }

                    $playerdetails = array(
                        'lastName'        => $this->input->post('lastName'),
                        'firstName'       => $this->input->post('firstName'),
                        'gender'          => $this->input->post('gender'),
                        'birthdate'       => $this->input->post('birthdate'),
                        'birthplace'      => $this->input->post('birthplace'),
                        'citizenship'     => $this->input->post('citizenship'),
                        'language'        => $this->input->post('player_language'),
                        'zipcode'         => $this->input->post('zipcode'),
                        'residentCountry' => $this->input->post('residentCountry'),
                        'region'          => $this->input->post('region'),
                        'city'            => $this->input->post('city'),
                        'address'         => $this->input->post('address'),
                        'address2'        => $this->input->post('address2'),
                    );

                    $player = array();
                    if($this->permissions->checkPermissions('player_verification_question')){
                        $player['secretQuestion'] = $this->input->post('secretQuestion');
                    }
                    if($this->permissions->checkPermissions('player_verification_questions_answer')){
                        $player['secretAnswer'] = $this->input->post('secretAnswer');
                    }

                    $modifiedFields = $this->checkModifiedFields($player_id, array_merge($player, $playerdetails));

                    if(!empty($player)){
                        $this->player_manager->editPlayer($player, $player_id);
                    }
                    $this->player_manager->editPlayerDetails($playerdetails, $player_id);

                    $this->savePlayerUpdateLog(
                        $player_id,
                        lang('lang.edit') . ' ' . lang('lang.playerinfo') . ' (' . $modifiedFields . ')',
                        $this->authentication->getUsername()
                    );

                    //sync lock
                    $username = $this->player_model->getUsernameById($player_id);
                    $this->syncPlayerCurrentToMDBWithLock($player_id, $username, false);

                    // record action in agency log {
                    $this->load->library(array('agency_library'));
                    $log_params = array(
                        'action'   => 'modify_player',
                        'link_url' => site_url('player_management/userInformation') . '/' . $player_id,
                        'done_by'  => $this->authentication->getUsername(),
                        'done_to'  => $username,
                        'details'  => 'Edit player personal info: ' . $modifiedFields,
                    );
                    $this->agency_library->save_action($log_params);
                    // record action in agency log }

                    $result = array('status' => 'success');
                    $this->returnJsonResult($result);
                }
            }
        }

        public function savePlayerContactInfo($player_id) {
            if (!$this->permissions->checkPermissions('player_basic_info') || !$this->permissions->checkPermissions('edit_player_contact_information')) {
                return $this->error_access();
            } else {
                $origin = $this->player_model->getPlayerInfoById($player_id);

                $email         = $this->input->post('email');
                $contactNumber = $this->input->post('contactNumber');

                $diff_email         = ($email != $origin['email']);
                $diff_contactNumber = ($contactNumber != $origin['contactNumber']);

                if($diff_email){
                    $this->form_validation->set_rules('email', lang('pi.6'), 'strip_tags|trim|xss_clean|valid_email|callback_check_email');
                } else {
                    $this->form_validation->set_rules('email', lang('pi.6'), 'strip_tags|trim|xss_clean|valid_email');
                }
                $this->form_validation->set_rules('dialing_code', lang('Dialing Code'), 'strip_tags|trim|xss_clean');
                if($diff_contactNumber){
                    $this->form_validation->set_rules('contactNumber', lang('player.63'), 'strip_tags|trim|xss_clean|is_natural_no_zero|callback_check_contact');
                } else {
                    $this->form_validation->set_rules('contactNumber', lang('player.63'), 'strip_tags|trim|xss_clean|is_natural_no_zero');
                }
                $this->form_validation->set_rules('imAccount', lang('player.64'), 'strip_tags|trim|xss_clean');
                $this->form_validation->set_rules('imAccount2', lang('player.65'), 'strip_tags|trim|xss_clean');
                $this->form_validation->set_rules('imAccount3', lang('player.101'), 'strip_tags|trim|xss_clean');

                $this->form_validation->set_message('max_length', lang('formvalidation.max_length'));
                $this->form_validation->set_message('valid_email', lang('formvalidation.valid_email'));
                $this->form_validation->set_message('is_natural_no_zero', lang('formvalidation.is_numeric'));
                if ($this->form_validation->run() == false) {
                    $message = $this->form_validation->error_array();
                    $result = array('status' => 'failed', 'message' => $message);
                    $this->returnJsonResult($result);
                } else {
                    if($this->utils->isEnabledFeature('enable_show_trigger_XinyanApi_validation_btn')) {
                        $this->load->model('player_api_verify_status');
                        $status = $this->player_api_verify_status->getApiStatusByPlayerId($player_id);

                        #OGP-13922 if xinyan status = 1 and edit player contactNumber, update status to enable the xinyan validation button
                        if($status == player_api_verify_status::API_RESPOSE_SUCCESS){
                            if($diff_contactNumber){
                                $this->player_api_verify_status->updateApiStatusByPlayerId($player_id, player_api_verify_status::API_UNKNOWN);
                                $this->CI->utils->debug_log('===========================update xinyan status success by admin');
                            }
                        }
                    }

                    $player = array();
                    if($this->permissions->checkPermissions('player_contact_information_email')){
                        $player['email'] = $email;
                        if($diff_email){ # reset verify status when modified
                            $player['verified_email'] = self::DEFAULT_VALUE_VERIFIED_EMAIL;
                        }
                    }

                    $playerdetails = array(
                        "dialing_code" => $this->input->post('dialing_code'),
                    );
                    if($this->permissions->checkPermissions('player_contact_information_contact_number')){
                        $playerdetails['contactNumber'] = $contactNumber;
                        if($diff_contactNumber){ # reset verify status when modified
                            $player['verified_phone'] = self::DEFAULT_VALUE_VERIFIED_PHONE;
                        }
                    }
                    if($this->permissions->checkPermissions('player_contact_information_im_accounts')){
                        $playerdetails['imAccount']  = $this->input->post('imAccount');
                        $playerdetails['imAccount2'] = $this->input->post('imAccount2');
                        $playerdetails['imAccount3'] = $this->input->post('imAccount3');
                    }

                    $modifiedFields = $this->checkModifiedFields($player_id, array_merge($player, $playerdetails));

                    if(!empty($player)){
                        $this->player_manager->editPlayer($player, $player_id);
                    }
                    $this->player_manager->editPlayerDetails($playerdetails, $player_id);

                    $this->savePlayerUpdateLog(
                        $player_id,
                        lang('lang.edit') . ' ' . lang('lang.playerinfo') . ' (' . $modifiedFields . ')',
                        $this->authentication->getUsername()
                    );
                    //sync lock
                    $this->syncPlayerCurrentToMDBWithLock($player_id, $origin['username'], false);

                    // record action in agency log {
                    $this->load->library(array('agency_library'));
                    $log_params = array(
                        'action'   => 'modify_player',
                        'link_url' => site_url('player_management/userInformation') . '/' . $player_id,
                        'done_by'  => $this->authentication->getUsername(),
                        'done_to'  => $origin['username'],
                        'details'  => 'Edit player contact info: ' . $modifiedFields,
                    );
                    $this->agency_library->save_action($log_params);
                    // record action in agency log }

                    $result = array('status' => 'success');
                    $this->returnJsonResult($result);
                }
            }
        }

        public function only_characters($str) {
            if (!empty($str) && preg_match('/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\0-9]/', $str)) {
                $this->form_validation->set_message('only_characters', lang('formvalidation.only_characters'));
                return false;
            } else {
                return true;
            }
        }

        public function check_contact($contact_number) {
            if (!empty($contact_number)) {
                $result = !$this->player_model->checkContactExist($contact_number);
                if (!$result) {
                    $this->form_validation->set_message('check_contact', "" . $contact_number . " %s " . lang('con.usm07'));
                }
                return $result;
            } else {
                return true;
            }
        }

        public function check_email($email) {
            if (!empty($email)) {
                $result = !$this->player_manager->checkEmailExist($email);
                if (!$result) {
                    $this->form_validation->set_message('check_email', "" . $email . " %s " . lang('con.usm07'));
                }
                return $result;
            } else {
                return true;
            }
        }

        /**
         * Updates communication preference of the player
         *
         * @return JSON Saving status
         * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
         */
        public function updateCommunicationPreference(){
            if (!$this->permissions->checkPermissions('edit_player_communication_preference')) {
                $this->error_access();
            } else {
                if(!$this->input->post()){
                    $ajax_response['status'] = false;
                    $this->returnJsonResult($result);
                    return;
                }

                $this->load->model('communication_preference_model');

                $player_id = $this->input->post('player_id');
                $data = $this->input->post();

                // -- Get changes on player's communication preferences
                $changes = $this->communication_preference_model->getCommunicationPreferenceChanges($data);
                unset($data['player_id']);
                unset($data['notes']);

                // -- Update player's preference
                $update_preferences = $this->communication_preference_model->updatePlayerCommunicationPreference($player_id, $data);

                $result = ['status' => 'success', 'message' => lang('sys.gd25')];

                if(!$update_preferences){
                    $result= ['status' => 'error', 'message' => lang('save.failed')];
                }

                // -- Get admin user ID
                $adminId = $this->authentication->getUserId();

                // -- save new log
                $this->communication_preference_model->saveNewLog($player_id, $changes, $adminId, Communication_preference_model::PLATFORM_SBE, $this->input->post('notes'));
                $this->returnJsonResult($result);
            }
        }

        /**
         * overview : Verified Email status
         *
         * @param int $playerId player_id
         * @param int $status
         */
        public function updateEmailStatusToVerified($playerId) {
            if (!$this->permissions->checkPermissions('verify_player_email')) {
                $this->error_access();
            } else {
                $this->load->model(['player_model']);

                $this->player_model->updateEmailStatusToVerified($playerId);
                $username = $this->player_model->getUsernameById($playerId);
                $this->syncPlayerCurrentToMDBWithLock($playerId, $username, false);

                // -- Add player update history
                $this->savePlayerUpdateLog($playerId, lang('Email verified by SBE user:') . ' ' . $this->authentication->getUsername(), $this->authentication->getUsername());

                #sending email
                $this->load->library(['email_manager']);
                $template = $this->email_manager->template('player', 'player_verify_email_success', array('player_id' => $playerId));
                $template_enabled = $template->getIsEnableByTemplateName();
                if($template_enabled['enable']){
                    $email = $this->player->getPlayerById($playerId)['email'];
                    $template->sendingEmail($email, Queue_result::CALLER_TYPE_ADMIN, $this->authentication->getUserId());
                }

                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Set email status to verified successfully'));
                redirect('player_management/userInformation/' . $playerId . '/2');
            }
        }

        public function sendEmailVerification($playerId) {
            if (!$this->permissions->checkPermissions('verify_player_email')) {
                $this->error_access();
            } else {

                #sending email
                $this->load->library(['email_manager']);
                $template = $this->email_manager->template('player', 'player_verify_email', array('player_id' => $playerId));
                $template_enabled = $template->getIsEnableByTemplateName();
                if($template_enabled['enable']) {
                    $email = $this->player->getPlayerById($playerId)['email'];
                    $template->sendingEmail($email, Queue_result::CALLER_TYPE_ADMIN, $this->authentication->getUserId());
                    /// Sync verify link (email_verify_exptime of player) for enabledMDB.
                    $rlt=null;
                    $insertOnly=false;
                    $this->syncPlayerCurrentToMDB($playerId, $insertOnly, $rlt);

                    $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('reg.64') . $email);
                } else {
                    $this->alertMessage(self::MESSAGE_TYPE_WARNING, $template_enabled['message']);
                }

                redirect('player_management/userInformation/' . $playerId . '/2');
            }
        }

        /**
         * overview : Verified phone status
         *
         * @param int $playerId player_id
         * @param int $status
         */
        public function updatePhoneStatusToVerified($playerId) {
            $this->load->model(['player_model']);

            $this->player_model->updatePhoneStatusToVerified($playerId);
            $username=$this->player_model->getUsernameById($playerId);
            $this->syncPlayerCurrentToMDBWithLock($playerId, $username, false);

            // -- Add player update history
            $this->savePlayerUpdateLog($playerId, lang('Phone verified by SBE user:') . ' ' . $this->authentication->getUsername(), $this->authentication->getUsername());

            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Set phone status to verified successfully'));
            redirect('player_management/userInformation/' . $playerId . '/2');
        }

        public function sendSMSVerification($playerId) {
            $this->load->model(array('sms_verification', 'player_model'));

            $codeCount = $this->sms_verification->getVerificationCodeCountPastMinute();

            if($codeCount > $this->config->item('sms_global_max_per_minute')) {
                $this->utils->error_log("Sent [$codeCount] SMS in the past minute, exceeded config max [".$this->config->item('sms_global_max_per_minute')."]");
                $this->returnJsonResult(array('success' => false, 'message' => lang('SMS process is currently busy. Please wait.')));
                return;
            }

            $sessionId = $this->session->userdata('session_id');
            $playerDetails = $this->player_model->getPlayerDetailsById($playerId);
            $mobileNumber = $playerDetails->contactNumber;

            $code = $this->sms_verification->getVerificationCode($playerId, $sessionId, $mobileNumber);
            $useSmsApi = $this->sms_sender->getSmsApiName();
            $msg = $this->utils->createSmsContent($code, $useSmsApi);


            if ($this->sms_sender->send($mobileNumber, $msg, $useSmsApi)) {
                $this->session->set_userdata('last_sms_time', time());
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('SMS sent'));
                redirect('player_management/userInformation/' . $playerId . '/2');
            } else {
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('SMS failed') .': '.$this->sms_sender->getLastError());
                redirect('player_management/userInformation/' . $playerId . '/2');
            }
        }
    //-----------------------------------------------ajaxBasicInfo----------------------------------------------------------


    public function ajaxResponsibleGaming($player_id) {
        if (!$this->utils->isEnabledFeature('responsible_gaming') || !$this->permissions->checkPermissions('responsible_gaming_info') || empty($player_id)) {
            $this->error_access();
        } else {
            $this->player_responsible_gaming_library->fetchResponsibleGamingDataForSBE($player_id);
            $this->load->view('player_management/user_information/ajax_responsible_gaming');
        }
    }
    //-----------------------------------------------ajaxResponsibleGaming----------------------------------------------------------
        /**
         * overview : post selfexclusion player responsible gaming
         *
         * detail : redirect to user information after cancellation process
         *
         * @param int $playerId
         * @param int $type
         */
        public function postSelfExecResponsibleGaming($playerId, $period_cnt) {
            $this->load->model(array('responsible_gaming'));

            $adminId = $this->authentication->getUserId();
            $period_cnt = is_null($period_cnt) ? 0 : intval($period_cnt);
            $type = empty($period_cnt) ? Responsible_gaming::SELF_EXCLUSION_PERMANENT : Responsible_gaming::SELF_EXCLUSION_TEMPORARY;

            if(empty($adminId)){
                $message = lang('pay.bt.error.occured');
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                return redirect('player_management/userInformation/' . $playerId . '/4');
            }

            if(empty($playerId)){
                $message = lang('pay.bt.error.occured');
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                return redirect('player_management/userInformation/' . $playerId . '/4');
            }

            if($type == Responsible_gaming::SELF_EXCLUSION_TEMPORARY){
                $result = $this->player_responsible_gaming_library->RequestSelfExclusionTemporary($playerId, $period_cnt, $adminId);
            }else{
                $result = $this->player_responsible_gaming_library->RequestSelfExclusionPermanent($playerId, $period_cnt, $adminId);
            }

            if($result){
                $status = BaseController::MESSAGE_TYPE_SUCCESS;
                $message = lang('You\'ve successfully sent request!');
            }else{
                $status = BaseController::MESSAGE_TYPE_ERROR;
                $message = lang('error.default.db.message');
            }

            $this->alertMessage($status, $message);
            return redirect('player_management/userInformation/' . $playerId . '/4');
        }

        /**
         * overview : post cool off player responsible gaming
         *
         * detail : redirect to user information after cancellation process
         *
         * @param int $playerId
         * @param int $type
         */
        public function postCooloffResponsibleGaming($playerId, $period_cnt) {
            $adminId = $this->authentication->getUserId();
            $period_cnt = is_null($period_cnt) ? 0 : intval($period_cnt);

            if(empty($adminId)){
                $message = lang('pay.bt.error.occured');
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                return redirect('player_management/userInformation/' . $playerId . '/4');
            }

            if(empty($playerId)){
                $message = lang('pay.bt.error.occured');
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                return redirect('player_management/userInformation/' . $playerId . '/4');
            }

            $result = $this->player_responsible_gaming_library->RequestCoolOff($playerId, $period_cnt, $adminId);

            if($result){
                $status = BaseController::MESSAGE_TYPE_SUCCESS;
                $message = lang('You\'ve successfully sent request!');
            }else{
                $status = BaseController::MESSAGE_TYPE_ERROR;
                $message = lang('error.default.db.message');
            }

            $this->alertMessage($status, $message);
            return redirect('player_management/userInformation/' . $playerId . '/4');
        }

        /**
         * overview : post deposit limit player responsible gaming
         *
         * detail : redirect to user information after cancellation process
         *
         * @param int $playerId
         * @param int $type
         */
        public function postDepositlimitResponsibleGaming($playerId, $period_cnt,$amount) {
            $adminId = $this->authentication->getUserId();
            $amount = intval($amount);

            if(empty($adminId)){
                $message = lang('pay.bt.error.occured');
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                return redirect('player_management/userInformation/' . $playerId . '/4');
            }

            if($amount < 0){
                $message = lang('pay.finalAmtPlayerReceiveStatus');
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                return redirect('player_management/userInformation/' . $playerId . '/4');
            }

            $result = $this->player_responsible_gaming_library->RequestDepositLimit($playerId, $amount, $period_cnt, $adminId);

            if(!is_array($result)){
                $result = [
                    'status' => BaseController::MESSAGE_TYPE_ERROR,
                    'message' => lang('error.default.db.message')
                ];
            }

            return $this->returnCommon($result['status'], $result['message'], NULL, '/player_management/userInformation/' . $playerId . '/4');
        }

        /**
         * overview : post wagering limit player responsible gaming
         *
         * detail : redirect to user information after cancellation process
         *
         * @param int $playerId
         * @param int $type
         */
        public function postWageringlimitResponsibleGaming($playerId, $period_cnt, $amount) {
            $adminId = $this->authentication->getUserId();
            $amount= intval($amount);

            if(empty($adminId)){
                $message = lang('pay.bt.error.occured');
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                return redirect('player_management/userInformation/' . $playerId . '/4');
            }

            if($amount < 0){
                $message = lang('pay.finalAmtPlayerReceiveStatus');
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                return redirect('player_management/userInformation/' . $playerId . '/4');
            }

            $result = $this->player_responsible_gaming_library->RequestWageringLimit($playerId, $amount, $period_cnt, $adminId);

            if(!is_array($result)){
                $result = [
                    'status' => BaseController::MESSAGE_TYPE_ERROR,
                    'message' => lang('error.default.db.message')
                ];
            }

            return $this->returnCommon($result['status'], $result['message'], NULL, '/player_management/userInformation/' . $playerId . '/4');
        }

        /**
         * overview : cancel player responsible gaming
         *
         * detail : redirect to user information after cancellation process
         *
         */
        public function cancelPlayerResponsibleGaming(){
            $this->load->model(array('responsible_gaming','responsible_gaming_history'));
            $playerId = $this->input->post('player_id');
            $type = $this->input->post('cancel_type');
            $notes = $this->input->post('notes');

            $allow_type = [Responsible_gaming::SELF_EXCLUSION_TEMPORARY,
                Responsible_gaming::SELF_EXCLUSION_PERMANENT,
                Responsible_gaming::COOLING_OFF,
                Responsible_gaming::LOSS_LIMITS,
                Responsible_gaming::DEPOSIT_LIMITS,
                Responsible_gaming::WAGERING_LIMITS];

            if(!in_array($type,$allow_type)){
                $status = self::MESSAGE_TYPE_ERROR;
                $message = lang('Cancel Failed!');
                return $this->returnCommon($status, $message);
            }

            $availableResponsibleGamingData = $this->responsible_gaming->getAvailableResponsibleGamingData($playerId,$type,[Responsible_gaming::STATUS_REQUEST,Responsible_gaming::STATUS_APPROVED,Responsible_gaming::STATUS_COOLING_OFF]);
            $this->utils->debug_log('======================================$availableResponsibleGamingData==========================================',$availableResponsibleGamingData);
            if(empty($availableResponsibleGamingData)){
                $status = self::MESSAGE_TYPE_ERROR;
                $message = lang('Cancel Failed!');
                return $this->returnCommon($status, $message);
            }

            $status = self::MESSAGE_TYPE_SUCCESS;
            $message = NULL;

            if(in_array($type,[Responsible_gaming::DEPOSIT_LIMITS,Responsible_gaming::WAGERING_LIMITS])){
                foreach($availableResponsibleGamingData as $responsible_gaming){
                    $this->responsible_gaming_history->addResponsibleGamingManualCanceledRecord($responsible_gaming->id, $responsible_gaming->status, $notes);
                    if(!$this->responsible_gaming->setPlayerResponsibleGamingToCancel($responsible_gaming->id, $responsible_gaming->player_id, $notes)){
                        $this->utils->debug_log('======================================$availableResponsibleGamingData==Failed========================================',$responsible_gaming->id);
                        $status = self::MESSAGE_TYPE_ERROR;
                        $message = lang('Cancel Failed!');
                        return $this->returnCommon($status, $message);
                    }
                    $this->utils->unblockPlayerInGameAndWebsite($playerId);
                }
            }

            if(in_array($type,[Responsible_gaming::SELF_EXCLUSION_TEMPORARY,Responsible_gaming::SELF_EXCLUSION_PERMANENT,Responsible_gaming::COOLING_OFF,Responsible_gaming::LOSS_LIMITS])){
                $responsible_gaming = $availableResponsibleGamingData['0'];
                $this->responsible_gaming_history->addResponsibleGamingManualCanceledRecord($responsible_gaming->id, $responsible_gaming->status, $notes);
                if(!$this->responsible_gaming->setPlayerResponsibleGamingToCancel($responsible_gaming->id, $responsible_gaming->player_id, $notes)) {
                    $this->utils->debug_log('======================================$availableResponsibleGamingData==Failed========================================',$responsible_gaming->id);
                    $status = self::MESSAGE_TYPE_ERROR;
                    $message = lang('Cancel Failed!');
                    return $this->returnCommon($status, $message);
                }
                $this->utils->unblockPlayerInGameAndWebsite($playerId);
            }

            $message = lang('You\'ve successfully cancelled!');
            return $this->returnCommon($status, $message);
        }

        /**
         * overview : Expire Temp Self Exclusion's Cooling Off Player
         * detail : redirect to user information after expire process
         */
        public function expireCoolingOffPlayer(){
            $this->load->model(array('responsible_gaming','responsible_gaming_history'));
            $this->load->library(array('player_responsible_gaming_library'));

            $playerId = $this->input->post('player_id');
            $type = $this->input->post('cancel_type');
            $notes = $this->input->post('notes');
            $rsp_status = $this->input->post('rsp_status');

            if($type != Responsible_gaming::SELF_EXCLUSION_TEMPORARY){
                return $this->returnCommon(self::MESSAGE_TYPE_ERROR, lang('Wrong Type!'));
            }

            if($rsp_status != Responsible_gaming::STATUS_COOLING_OFF){
                return $this->returnCommon(self::MESSAGE_TYPE_ERROR, lang('Wrong Status!'));
            }

            $result = $this->player_responsible_gaming_library->ExpireSelfExclusionTemporaryCoolingOffPlayer($playerId, Responsible_gaming::SELF_EXCLUSION_TEMPORARY, Responsible_gaming::STATUS_COOLING_OFF, $notes);
            return $this->returnCommon($result['status'], $result['message']);
        }
    //-----------------------------------------------ajaxResponsibleGaming----------------------------------------------------------

    public function ajaxFinInfo($player_id) {
        if (!$this->permissions->checkPermissions('player_list') || empty($player_id)) {
            $this->error_access();
        } else {
            $this->load->model(array('playerbankdetails'));
            $data['player'] = $this->player_model->getPlayerInfoById($player_id);
            $bankdetails = $this->playerbankdetails->getNotDeletedBankInfoList($player_id);
            $data['deposit_bankdetails'] = $bankdetails['deposit'];
            $data['withdrawal_bankdetails'] = $bankdetails['withdrawal'];


            $this->load->view('player_management/user_information/ajax_fin_info', $data);
        }
    }
    //-----------------------------------------------ajaxFinInfo----------------------------------------------------------
        public function playerBankInfoSetToVerified($bank_details_id, $player_id) {
            if(!$this->permissions->checkPermissions('set_financial_account_to_verified')){
                $this->error_access();
            } else {
                $this->load->model(['playerbankdetails']);

                $data = ['verified' => 1];
                $this->player_manager->updatePlayerBankDetails($data, $bank_details_id);

                $bank_detail = $this->playerbankdetails->getBankDetailsById($bank_details_id);
                $bank_message = lang($bank_detail['bankName']).' - '.$bank_detail['bankAccountFullName'].'('.$bank_detail['bankAccountNumber'].')';
                $this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Financial Account Status', $this->authentication->getUsername() . " " . lang('con.plm76') . " " . $bank_message);

                $message = lang('player.ui07') . ": " . lang('con.plm76');
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);

                redirect('player_management/userInformation/' . $player_id . '/5');
            }
        }

        /**
         * overview : changes bank info set defaul
         *
         * @param $bank_details_id
         * @param $is_default
         * @param $player_id
         * @param null $type
         */
        public function playerBankInfoSetDefault($bank_details_id, $is_default, $player_id, $type = null) {
            $data = array(
                'isDefault' => $is_default,
            );

            $currentDefaultBank = $this->player_model->getPlayerCurrentDefaultBankAccount($player_id, $type) ?: lang('cashier.32');
            //var_dump($currentDefaultBank);exit();
            $this->player_manager->updatePlayerBankDetails($data, $bank_details_id);

            $this->load->model('player_model');
            $newDefaultBank = lang('player.ui56');
            if ($is_default) {
                $newDefaultBank = $this->player_model->getPlayerBankAccountName($bank_details_id);
            }

            $this->savePlayerUpdateLog($player_id, lang('cashier.110') . ' ' . lang('con.bnk03') . ' - ' .
                lang('adjustmenthistory.title.beforeadjustment') . ' (' . $currentDefaultBank . ') ' .
                lang('adjustmenthistory.title.afteradjustment') . ' (' . $newDefaultBank . ') ', $this->authentication->getUsername()); // Add log in playerupdatehistory

            if ($is_default == 0) {
                $this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('player.ui07'), $this->authentication->getUsername() . " " . lang('con.plm63'));
                $message = lang('player.ui07') . ": " . lang('con.plm63');
            } else {
                $this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('con.bnk03'), $this->authentication->getUsername() . " " . lang('con.plm62'));
                $message = lang('player.ui07') . ": " . lang('con.plm62');
            }
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);

            redirect('player_management/userInformation/' . $player_id . '/5');
        }

        /**
         * overview : changes bank information status
         *
         * @param int $bank_details_id  bank_details_id
         * @param int $status           status
         * @param int $player_id        player_id
         */
        public function playerBankInfoChangeStatus($bank_details_id, $status, $player_id) {
            $data = array(
                'status' => $status,
            );

            $this->player_manager->updatePlayerBankDetails($data, $bank_details_id);

            // saves changes to bank history
            $bankdetails = $this->player_manager->getPlayerBankDetails($bank_details_id);

            $changes = array(
                'playerBankDetailsId' => $bank_details_id,
                'changes' => ($status == 0) ? lang($bankdetails['bankName']) . ' ' . lang('player.ut09') . ': [' . lang('adjustmenthistory.title.beforeadjustment') . '] ' . lang('player.tl09') . ' [' . lang('adjustmenthistory.title.afteradjustment') . '] ' . lang('player.tl08') : lang($bankdetails['bankName']) . ' ' . lang('player.ut09') . ': [' . lang('adjustmenthistory.title.beforeadjustment') . '] ' . lang('player.tl08') . ' [' . lang('adjustmenthistory.title.beforeadjustment') . '] ' . lang('player.tl09'),
                'createdOn' => date("Y-m-d H:i:s"),
                'operator' => $this->authentication->getUsername(),
            );
            $this->payment_manager->saveBankHistoryByPlayer($changes);

            if ($status == 0) {
                $this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('player.ui07'), $this->authentication->getUsername() . lang('con.plm64'));
                $message = lang('player.ui07') . ": " . lang('con.plm64');
            } else {
                $this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('con.bnk03'), $this->authentication->getUsername() . lang('con.plm65'));
                $message = lang('player.ui07') . ": " . lang('con.plm65');
            }
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);

            redirect('player_management/userInformation/' . $player_id . '/5');
        }

        /**
         * overview : remove player bank information
         *
         * @param int $bank_details_id  bank_details_id
         * @param int $player_id        player_id
         */
        public function deletePlayerBankInfo($bank_details_id, $player_id) {

            $this->load->model(['playerbankdetails']);

            $bank_detail = $this->playerbankdetails->getBankDetailsById($bank_details_id);
            $deleted_bank_message = lang($bank_detail['bankName']).' - '.$bank_detail['bankAccountFullName'].'('.$bank_detail['bankAccountNumber'].')';
            $message = lang('player.ui07') . ": " . lang('con.plm66') . " <i>" . $deleted_bank_message . "</i>";

            $this->playerbankdetails->deletePlayerBankInfo($bank_details_id);
            $this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('con.bnk03'), $this->authentication->getUsername() . " " . lang('con.plm66') . " " . $deleted_bank_message);
            $this->savePlayerUpdateLog($player_id, lang('con.bnk09') . ' : '.$deleted_bank_message, $this->authentication->getUsername()); // Add log in playerupdatehistory
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);

            redirect('player_management/userInformation/' . $player_id . '/5');
        }

        private function getAvailableBankTypes($type = NULL) {
        	$this->load->model(['financial_account_setting', 'playerbankdetails']);
			$bankTypeList = json_decode(json_encode($this->banktype->getBankTypes()), true);
        	$available_bankTypes=[];

	        foreach($bankTypeList as $bankType){
	            if($bankType['payment_type_flag'] == Financial_account_setting::PAYMENT_TYPE_FLAG_API){
	                continue;
	            }

	            if($type == Playerbankdetails::WITHDRAWAL_BANK && $bankType['enabled_withdrawal']){
	                $available_bankTypes[] = $bankType;
	            } elseif($type == Playerbankdetails::DEPOSIT_BANK && $bankType['enabled_deposit']){
	                $available_bankTypes[] = $bankType;
	            } elseif($bankType['enabled_deposit'] || $bankType['enabled_withdrawal']) {
	            	$available_bankTypes[] = $bankType;
	            }
	        }

            array_walk($available_bankTypes, function (&$available_bankTypes) {
                $available_bankTypes['bankName'] = lang($available_bankTypes['bankName']);
            });

            $available_bankTypes = array_column($available_bankTypes, 'bankTypeId', 'bankName');
            ksort($available_bankTypes);

	        return $available_bankTypes;
        }

        /**
         * overview : add player bank informatoin
         *
         * @param int $player_id    player_id
         * @param $type
         */
        public function addPlayerBankInfo($player_id, $type) {
			$userId=$this->authentication->getUserId();
            $data['bank_types'] = $this->getAvailableBankTypes($type);
            $data['dw_bank'] = $type;
            $data['player_id'] = $player_id;
            $data['double_submit_hidden_field'] = $this->initDoubleSubmitAndReturnHiddenFieldForAdmin($userId);

            $this->load->view('player_management/ajax_ui_add_bank', $data);
        }

        /**
         * overview : verify add bank information
         *
         * @return  string
         */
        public function verifyAddPlayerBankInfo() {
            $this->form_validation->set_rules('bank_type_id', lang('player.ui35'), 'trim|xss_clean|required');
            $this->form_validation->set_rules('bank_full_name', lang('cashier.68'), 'trim|xss_clean');
            $this->form_validation->set_rules('bank_account_number', lang('cashier.68'), 'trim|xss_clean|required');
            $this->form_validation->set_rules('bank_address', lang('player.ui38'), 'trim|xss_clean');
            $this->form_validation->set_rules('province', lang('player.ui52'), 'trim|xss_clean');
            $this->form_validation->set_rules('city', lang('player.ui53'), 'trim|xss_clean');
            $this->form_validation->set_rules('branch', lang('player.ui54'), 'trim|xss_clean');
            $this->form_validation->set_rules('verified', lang('player.ui54'), 'trim|xss_clean');
            $result = array(); //json result to return response
            $bank_account_number = preg_replace('/\s(?=)/', '',$this->input->post('bank_account_number'));
            $userId=$this->authentication->getUserId();

            if(!$this->verifyAndResetDoubleSubmitForAdmin($userId)){
				$message = lang('Please refresh and try, and donot allow double submit');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				return;
			}

            if(strlen($bank_account_number) > self::DEFAULT_BANKACCOUNT_LENGTH) {
                $result['success'] = false;
                $result['reason'] = lang('bankinfo.acctNo.error.maximum');
                $this->returnJsonResult($result);
                return;
            }
            if (!preg_match('/^[\pL\pM\pN]+$/u', $bank_account_number) > 0) {
                $result['success'] = false;
                $result['reason'] = lang('bankinfo.acctNo.error.punctuationless');
                $this->returnJsonResult($result);
                return;
            }

            $player_id      = $this->input->post('player_id');
            $dw_bank        = $this->input->post('dw_bank');
            $bank_type_id   = $this->input->post('bank_type_id');
            $bank_full_name = $this->input->post('bank_full_name');
            $bank_address   = $this->input->post('bank_address');
            $province       = $this->input->post('province');
            $city           = $this->input->post('city');
            $branch         = $this->input->post('branch');
            $verified       = $this->input->post('verified');

            $this->load->model(['player', 'playerbankdetails']);
            $accountCanUse = $this->playerbankdetails->validate_bank_account_number($player_id, $bank_account_number, $dw_bank);
            if($accountCanUse == false) {
                $result['success'] = false;
                $result['reason'] = lang('bankinfo.acctNo.error.duplicated');
                $this->returnJsonResult($result);
                return;
            }

            if (!$this->permissions->checkPermissions('set_financial_account_to_verified')){
                $verified = 0;
            }

            if ($this->form_validation->run() == false) {
                $this->addPlayerBankInfo($player_id, $dw_bank);
            } else {
                $data = array(
                    'bankTypeId'          => $bank_type_id,
                    'playerId'            => $player_id,
                    'bankAccountFullName' => ($bank_full_name == null) ? '' : $bank_full_name,
                    'bankAccountNumber'   => $bank_account_number,
                    'bankAddress'         => ($bank_address == null) ? '' : $bank_address,
                    'province'            => ($province == null) ? '' : $province,
                    'city'                => ($city == null) ? '' : $city,
                    'branch'              => ($branch == null) ? '' : $branch,
                    'verified'            => ($verified == null) ? 0 : $verified,
                    'isRemember'          => 1,
                    'dwBank'              => $dw_bank,
                    'createdOn'           => date('Y-m-d H:i:s'),
                    'status'              => '0',
                );

                $bank_details_id=null;
                $this->playerbankdetails->dbtransOnly(function()
                    use($data, $dw_bank, $player_id, &$bank_details_id){

                    $bank_details_id=$this->playerbankdetails->addPlayerBankDetailByAdmin($data);

                    if ($dw_bank) {
                        $accountType = lang('adjustmenthistory.adjustmenttype.2');
                    } else {
                        $accountType = lang('adjustmenthistory.adjustmenttype.1');
                    }
                    $this->savePlayerUpdateLog($player_id, lang('cashier.103') . ' - ' . $accountType, $this->authentication->getUsername()); // Add log in playerupdatehistory

                    return !empty($bank_details_id);
                });

                $this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('con.bnk03'), $this->authentication->getUsername() . " " . lang('con.plm68'));
                $message = lang('player.ui07') . ": " . lang('con.plm68');

                //redirect('player_management/userInformation/' . $player_id . '/5');
                $result['success'] = true;
                return  $this->returnJsonResult($result);
            }
        }

        /**
         * overview : edit bank info
         *
         * @return  string
         */
        public function editPlayerBankInfo($bank_details_id) {
            $data['bank_types'] = $this->getAvailableBankTypes($type);
            $data['bank_details'] = $this->player_manager->getPlayerBankDetails($bank_details_id);

            $this->load->view('player_management/ajax_ui_edit_bank', $data);
        }

        /**
         * overview : verify edit bank info
         *
         * @return  string
         */
        public function verifyEditPlayerBankInfo() {
            $this->form_validation->set_rules('bank_type_id', lang('player.ui35'), 'trim|xss_clean|required');
            $this->form_validation->set_rules('bank_full_name', lang('cashier.68'), 'trim|xss_clean');
            $this->form_validation->set_rules('bank_account_number', lang('cashier.69'), 'trim|xss_clean|required');
            $this->form_validation->set_rules('bank_address', lang('player.ui38'), 'trim|xss_clean');
            $this->form_validation->set_rules('province', lang('player.ui52'), 'trim|xss_clean');
            $this->form_validation->set_rules('city', lang('player.ui52'), 'trim|xss_clean');
            $this->form_validation->set_rules('branch', lang('player.ui54'), 'trim|xss_clean');

            $bank_details_id = $this->input->post('bank_details_id');
            $player_id = $this->input->post('player_id');
            $bank_name = $this->input->post('bankname');

            $bank_type_id = $this->input->post('bank_type_id');
            $bank_full_name = $this->input->post('bank_full_name');
            $bank_account_number = $this->input->post('bank_account_number');
            $bank_address = $this->input->post('bank_address');
            $province = $this->input->post('province');
            $city = $this->input->post('city');
            $branch = $this->input->post('branch');

            if ($this->form_validation->run() == false) {
                $this->editPlayerBankInfo($bank_details_id);
            } else {
                $data = array(
                    'bankTypeId' => $bank_type_id,
                    'bankAccountFullName' => ($bank_full_name == null) ? '' : $bank_full_name,
                    'bankAccountNumber' => $bank_account_number,
                    'bankAddress' => ($bank_address == null) ? '' : $bank_address,
                    'province' => ($province == null) ? '' : $province,
                    'city' => ($city == null) ? '' : $city,
                    'branch' => ($branch == null) ? '' : $branch,
                    'updatedOn' => date('Y-m-d H:i:s'),
                );

                // saves changes to bank history
                $origbank = $this->player_manager->getPlayerBankDetails($bank_details_id);
                $modifiedFields = $this->checkBankChanges($origbank, $data);
                $changes = array(
                    'playerBankDetailsId' => $bank_details_id,
                    'changes' => lang('lang.edit') . ' ' . lang('player.ui07') . ' (' . $modifiedFields . ')',
                    'createdOn' => date("Y-m-d H:i:s"),
                    'operator' => $this->authentication->getUsername(),
                );
                $this->payment_manager->saveBankHistoryByPlayer($changes);

                $this->player_manager->updatePlayerBankDetails($data, $bank_details_id);

                $this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('con.bnk03'), $this->authentication->getUsername() . lang('con.plm67') . " " . $bank_name);
                $message = lang('player.ui07') . ": " . lang('con.plm67') . " <i>" . $bank_name . "</i>";
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);

                redirect('player_management/userInformation/' . $player_id . '/5');
            }
        }
    //-----------------------------------------------ajaxFinInfo----------------------------------------------------------

    public function ajaxWithdrawalCondition($player_id) {
        if (!$this->permissions->checkPermissions('player_list') || empty($player_id)) {
            $this->error_access();
        } else {
            $data['player'] = $this->player_model->getPlayerInfoById($player_id);
            $promoRulesConfig = $this->utils->getConfig('promotion_rules');
            $data['enabled_show_withdraw_condition_detail_betting'] = $promoRulesConfig['enabled_show_withdraw_condition_detail_betting'];

            $this->load->view('player_management/user_information/ajax_withdrawal_condition', $data);
        }
    }
    //-----------------------------------------------ajaxWithdrawalCondition----------------------------------------------------------
        /**
         * @param int $playerId     player_id
         */
        public function check_withdraw_condition($playerId) {
            $this->load->model(['withdraw_condition']);
            $message = null;
            $controller = $this;
            $success = $this->lockAndTransForPlayerBalance($playerId, function () use ($controller, $playerId, &$message) {

                $success = $controller->withdraw_condition->autoCheckWithdrawConditionAndMoveBigWallet($playerId, $message);

                $controller->utils->recordAction('Withdraw Condition', 'Manually Check Withdraw Condition', $message);

                return $success;
            });

            if ($success) {
                if (empty($message)) {
                    $message = lang('Checked all withdraw condition');
                } else {
                    //replace enter
                    $this->utils->debug_log($message);
                    $message = str_replace("\n", "<br>\n", $message);
                }
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
            } else {
                if (empty($message)) {
                    $message = lang('error.default.db.message');
                } else {
                    //replace enter
                    $this->utils->debug_log($message);
                    $message = str_replace("\n", "<br>\n", $message);
                }
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            }

            redirect('/player_management/userInformation/' . $playerId);
        }

        /**
         * overview : cancel withdrawal condition
         *
         * detail : @return status for cancellation of withdrawal
         *
         * @param int $player_id    player id
         */
        public function cancelWithdrawalCondition() {
            $this->load->model(array('withdraw_condition', 'transaction_notes'));

            $forCancelIds = '';
            //implode problem solution
            if ($this->input->post('forCancelIds')) {
                $forCancelIds_arr = $this->input->post('forCancelIds');
                $forCancelIds = implode(",", $forCancelIds_arr);
            }

            $reasonToCancel = $this->input->post('reasonToCancel');
            $player_id = $this->input->post('playerId');
            $cancelManualStatus = $this->input->post('cancelManualStatus');

            $this->utils->debug_log($forCancelIds, $reasonToCancel, $player_id);

            if (!$forCancelIds && !$reasonToCancel) {
                $arr = array('status' => 'failed');
                $this->returnJsonResult($arr);
                return;
            }
            $ids = $this->input->post("forCancelIds");

            $this->withdraw_condition->startTrans();

            $withdrawal_condition = $this->withdraw_condition->cancelWithdrawalCondition($ids, $cancelManualStatus);

            if ($withdrawal_condition) {

                $management = 'Player Management';
                $action = 'Cancel Withdrawal Condition';
                $description = 'IDs: ' . $forCancelIds . ' of PlayerId: ' . $player_id . ' Reason: ' . $reasonToCancel;

                $adminUserId = $this->authentication->getUserId();

                $_datas = array();
                $transaction = 'cancel withdraw condition';

                foreach ($ids as $key => $id) {
                    $_datas[] = array(
                        'note' => $reasonToCancel,
                        'admin_user_id' => $adminUserId,
                        'create_date' => $this->utils->getNowForMysql(),
                        'transaction' => $transaction,
                        'transaction_id' => $id,
                    );
                }

                $addNotes = $this->transaction_notes->addByBatch($_datas);
                $succ = $this->withdraw_condition->endTransWithSucc();

                $this->utils->recordAction($management, $action, $description);

                if ($succ) {
                    $result = array('status' => 'success');
                } else {
                    $result = array('status' => 'failed');
                }
            } else {
                $this->utils->error_log('cancel withdraw condition failed, rollback');
                $this->withdraw_condition->rollbackTrans();

                $result = array('status' => 'failed');
            }

            $this->utils->debug_log('cancelWithdrawalCondition result', $result);
            $this->returnJsonResult($result);
        }
    //-----------------------------------------------ajaxWithdrawalCondition----------------------------------------------------------

    /**
     * overview : show withdrawal condition
     *
     * detail : @return status and withdrawal condition data
     *
     * @param int $player_id    player id
     */
    public function getWithdrawalCondition($player_id) {
        $this->load->model(array('withdraw_condition'));
        $withdrawalCondition = $this->withdraw_condition->getPlayerWithdrawalCondition($player_id);

        if($this->utils->isEnabledFeature('disabled_withdraw_condition_share_betting_amount')){
            $totalRequiredBet = 0;
            $totalPlayerBet = 0;

            if(isset($withdrawalCondition['totalRequiredBet'])){
                $totalRequiredBet = $withdrawalCondition['totalRequiredBet'];
                unset($withdrawalCondition['totalRequiredBet']);
            }

            if(isset($withdrawalCondition['totalPlayerBet'])){
                $totalPlayerBet = $withdrawalCondition['totalPlayerBet'];
                unset($withdrawalCondition['totalPlayerBet']);
            }

            $arr = array(
                'status' => 'success',
                'withdrawalCondition' => $withdrawalCondition,
                'totalRequiredBet' => $totalRequiredBet,
                'totalPlayerBet' => $totalPlayerBet,
            );
        }else{
            $arr = array(
                'status' => 'success',
                'withdrawalCondition' => $withdrawalCondition,
            );
        }

        $this->returnJsonResult($arr);
    }

    /**
     * Computes for total required bet, current total bet, and unfinished bet amount pf player
     * based on his withdrawal conditions.
     *
     * @param  string $playerId Player ID
     * @return Array/JSON       Computed / summarized withdrawal conditions
     * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
     */
    public function computeWithdrawalCondition($player_id) {
        $this->load->model(array('withdraw_condition'));
        $computation = $this->withdraw_condition->computePlayerWithdrawalConditions($player_id);

        $arr = array(
            'status' => 'success',
            'summarizedWithdrawalCondition' => $computation,
        );

        $this->returnJsonResult($arr);
    }




    public function ajaxTransferCondition($player_id) {
        if (!$this->utils->isEnabledFeature('enabled_transfer_condition') || !$this->permissions->checkPermissions('player_list') || empty($player_id)) {
            $this->error_access();
        } else {
    		$this->load->model('transfer_condition');
            $data['player'] = $this->player_model->getPlayerInfoById($player_id);

            $this->load->view('player_management/user_information/ajax_transfer_condition', $data);
        }
    }
    //-----------------------------------------------ajaxTransferCondition----------------------------------------------------------
        /**
         * overview : show transfer condition
         *
         * detail : @return status and transfer condition data
         *
         * @param int $player_id    player id
         * @return Array/JSON
         * @author curtis.php.tw <curtis.php.tw@tripleonetech.net>
         */
        public function getTransferCondition($player_id) {
            $this->load->model(array('transfer_condition'));
            $transferCondition = $this->transfer_condition->getPlayerTransferCondition($player_id);

            $arr = array(
                'status' => 'success',
                'transferCondition' => $transferCondition,
            );

            $this->returnJsonResult($arr);
        }

        /**
         * overview : cancel transfer condition
         * detail : @return status for cancellation of transfer condition
         */
        public function cancelTransferCondition() {
            $this->load->model(array('withdraw_condition', 'transaction_notes', 'transfer_condition'));

            $ids = $this->input->post("forCancelIds") ? $this->input->post("forCancelIds") : '';
            $reasonToCancel = $this->input->post('reasonToCancel');

            if(empty($ids)){
                $error_message = 'cancel transfer condition failed, empty cancel ids';
                $this->utils->error_log($error_message);
                $result = array('status' => 'failed', 'error_message' => $error_message);
                return $this->returnJsonResult($result);
            }

            if(empty($reasonToCancel)){
                $error_message = 'cancel transfer condition failed, empty cancel reason';
                $this->utils->error_log($error_message);
                $result = array('status' => 'failed', 'error_message' => $error_message);
                return $this->returnJsonResult($result);
            }

            //implode problem solution
            $forCancelIds = implode(",", $ids);

            $player_id = $this->input->post('playerId');
            $cancelManualStatus = $this->input->post('cancelManualStatus');
            $this->utils->debug_log("Canceled Transfer Condition Ids [". $forCancelIds ."], Reason [". $reasonToCancel ."], Player_id [". $player_id ."]");


            $this->transfer_condition->startTrans();
            $transfer_condition = $this->transfer_condition->cancelTransferCondition($ids, $cancelManualStatus);

            if (!$transfer_condition) {
                $this->withdraw_condition->rollbackTrans();

                $error_message = 'cancel transfer condition failed, rollback';
                $this->utils->error_log($error_message);
                $result = array('status' => 'failed', 'error_message' => $error_message);

                return $this->returnJsonResult($result);
            }

            $_datas = array();
            foreach ($ids as $key => $id) {
                $_datas[] = array(
                    'note' => $reasonToCancel,
                    'admin_user_id' => $this->authentication->getUserId(),
                    'create_date' => $this->utils->getNowForMysql(),
                    'transaction' => 'cancel Transfer condition id ['.$id.']',
                    'transaction_id' => $id,
                );
            }

            $addNotes = $this->transaction_notes->addByBatch($_datas);
            $succ = $this->transfer_condition->endTransWithSucc();

            $management = 'Player Management';
            $action = 'Cancel Transfer Condition';
            $description = 'IDs: ' . $forCancelIds . ' of PlayerId: ' . $player_id . ' Reason: ' . $reasonToCancel;
            $this->utils->recordAction($management, $action, $description);

            if ($succ) {
                $result = array('status' => 'success');
            } else {
                $result = array('status' => 'failed', 'error_message' => 'Error!');
            }

            $this->utils->debug_log('result', $result);
            $this->returnJsonResult($result);
        }
    //-----------------------------------------------ajaxTransferCondition----------------------------------------------------------

    public function ajaxAccountInfo($player_id) {
        if (!$this->permissions->checkPermissions('player_list') || empty($player_id)) {
            $this->error_access();
        } else {
            $this->load->model(['payment_account', 'point_transactions']);
            $data['player'] = $this->player_model->getPlayerInfoById($player_id);

            $totalPoints = $this->point_transactions->getPlayerTotalPoints($player_id);
            $playerTotalPoints = 0;
            if (!empty($totalPoints)) {
                $playerTotalPoints = array_sum(array_column($totalPoints, 'points'));
            }
            $data['available_points'] = $playerTotalPoints;
            $data['paymentaccounts'] = $this->payment_account->getAvailableAccount($player_id, null, null, true);

            $this->load->view('player_management/user_information/ajax_account_info', $data);
        }
    }
    //-----------------------------------------------ajaxAccountInfo----------------------------------------------------------
        /**
         * overview : reset player balance
         * @param int $player_id    player_id
         */
        public function resetbalance($player_id) {
            $success = true;
            $manager = $this->utils->loadGameManager();
            $rlt = $manager->queryBalanceOnAllPlatformsByPlayerId($player_id);

            $this->utils->debug_log('refresh balance: '.$player_id, $rlt);

            if (!empty($rlt)) {
                $this->load->model(array('player_model', 'wallet_model', 'daily_balance', 'game_logs', 'game_provider_auth'));

                $controller = $this;
                $success = $this->lockAndTransForPlayerBalance($player_id, function () use ($controller, $player_id, $rlt) {

                    $apiArray = $controller->utils->getApiListByBalanceInGameLog();
                    foreach ($rlt as $systemId => $val) {
                        if ($val['success']) {
                            $balance = $val['balance'];

                            $api = $controller->utils->loadExternalSystemLibObject($systemId);

                            if ($api->isSeamLessGame()) {

                                $this->utils->debug_log('SEAMLESS START');

                                $playerUsername = $this->player_model->getUsernameById($player_id);

                                $result = $api->queryPlayerBalance($playerUsername);

                                if ($result['success']) {
                                    $balance = $result['balance'];
                                }

                                $this->utils->debug_log('SEAMLESS END', $playerUsername, $balance);

                            }

                            if ( ! $api->isSeamLessGame()) {
                                $api->updatePlayerSubwalletBalanceWithoutLock($player_id, $balance);
                            }

                            //only for balance_in_game_logs
                            if (in_array($systemId, $apiArray)) {
                                $afterBalance = $balance;
                                $amount = 0;
                                $gameUsername = $controller->game_provider_auth->getGameUsernameByPlayerId($player_id, $systemId);
                                $respResultId = null;
                                $transType = Game_logs::TRANS_TYPE_SUB_WALLET_TO_MAIN_WALLET;
                                $created_at = null;

                                //insert to game logs
                                $id = $controller->game_logs->insertGameTransaction($systemId, $player_id, $gameUsername,
                                    $afterBalance, $amount, $respResultId, $transType, $created_at);

                                $controller->utils->debug_log('insert game transaction because reset balance',
                                    $systemId, $player_id, 'balance', $afterBalance, $amount, $transType, 'id', $id);
                            }
                        }
                    }
                    //only record one
                    $controller->wallet_model->recordPlayerAfterActionWalletBalanceHistory(Wallet_model::BALANCE_ACTION_REFRESH,
                        $player_id, null, -1, 0, null, null, null, null, null);

                    return true;
                });
            }

            if ($success) {
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('report.log06'));
            } else {
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('report.log07'));
            }
            redirect('/player_management/userInformation/' . $player_id . '/8');
        }

        /**
         * overview : move all to real
         *
         * detail : move all to real on main wallet
         *
         * @param int $playerId     player_id
         */
        public function move_all_to_real($playerId) {
            $enabled_move_all_to_real = $this->permissions->checkPermissions('enabled_move_all_to_real');
            if (!$enabled_move_all_to_real || empty($playerId)) {
                return $this->error_access();
            }

            $this->load->model(['wallet_model']);
            $controller = $this;
            $message = null;

            $success = $this->lockAndTransForPlayerBalance($playerId, function () use ($controller, $playerId, &$message) {
                return $this->wallet_model->moveAllToRealOnMainWallet($playerId);
            });

            if ($success) {
                if (empty($message)) {
                    $message = lang('Move all to real wallet successfully');
                }
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
            } else {
                if (empty($message)) {
                    $message = lang('error.default.db.message');
                }
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            }

            redirect('/player_management/userInformation/' . $playerId . '/8');
        }

        public function move_pending_to_real($playerId) {
            $enabled_move_all_to_real = $this->permissions->checkPermissions('enabled_move_all_to_real');
            if (!$enabled_move_all_to_real || empty($playerId)) {
                return $this->error_access();
            }

            $this->load->model(['wallet_model']);
            $controller = $this;
            $message = null;

            $success = $this->lockAndTransForPlayerBalance($playerId, function () use ($controller, $playerId, &$message) {
                return $this->wallet_model->returnFrozenToMainOnBigWallet($playerId);
            });

            if ($success) {
                if (empty($message)) {
                    $message = lang('Move pending to real on main wallet successfully');
                }
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
            } else {
                if (empty($message)) {
                    $message = lang('error.default.db.message');
                }
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            }

            redirect('/player_management/userInformation/' . $playerId . '/8');
        }

        /**
         * overview : display account information
         *
         * @param int $player_id    player id
         */
        public function getAccountInformation($player_id) {
            $this->load->model(['wallet_model', 'player_friend_referral']);

            $balanceDetails = $this->wallet_model->getBalanceDetails($player_id);
            $mainwallet = ['totalBalanceAmount' => $balanceDetails['main_wallet']];

            $playeraccount              = $this->player_manager->getPlayerAccount($player_id);
            $first_last_deposit         = $this->player_manager->getPlayerFirstLastApprovedTransaction($player_id, Transactions::DEPOSIT);
            $first_last_withdraw        = $this->player_manager->getPlayerFirstLastApprovedTransaction($player_id, Transactions::WITHDRAWAL);

            $total_promo_bonus          = $this->player_model->getMemberTotalBonus($player_id, Transactions::ADD_BONUS)['totalBonus'];
            $total_deposit_bonus        = $this->player_model->getMemberTotalBonus($player_id, Transactions::MEMBER_GROUP_DEPOSIT_BONUS)['totalBonus'];
            $total_cashback_bonus       = $this->player_model->getPlayerTotalCashback($player_id);
            $total_referral_bonus       = $this->player_friend_referral->getTotalReferralBonusByPlayerId($player_id);
            $total_deducted_promo_bonus = $this->player_model->getMemberTotalBonus($player_id, Transactions::SUBTRACT_BONUS)['totalBonus'];
            $total_bonus_received       = ($total_promo_bonus + $total_deposit_bonus + $total_cashback_bonus + $total_referral_bonus) - $total_deducted_promo_bonus;

            $total_deposits = array(
                'totalDeposit'         => $this->transactions->getPlayerTotalDeposits($player_id),
                'totalNumberOfDeposit' => $this->transactions->getTransactionCount(
                    array(
                        'to_id'            => $player_id,
                        'to_type'          => Transactions::PLAYER,
                        'transaction_type' => Transactions::DEPOSIT,
                        'status'           => Transactions::APPROVED,
                    )
                ),
            );

            $total_withdrawal = array(
                'totalWithdrawal'         => $this->transactions->getPlayerTotalWithdrawals($player_id),
                'totalNumberOfWithdrawal' => $this->transactions->getTransactionCount(
                    array(
                        'to_id'            => $player_id,
                        'to_type'          => Transactions::PLAYER,
                        'transaction_type' => Transactions::WITHDRAWAL,
                        'status'           => Transactions::APPROVED,
                    )
                ),
            );

            $average = "";
            if (!empty($total_deposits['totalDeposit']) && !empty($total_deposits['totalNumberOfDeposit'])) {
                $average = ($total_deposits['totalDeposit'] / $total_deposits['totalNumberOfDeposit']);
            }
            $average_deposits = $average ? $average : '0';

            $average = "";
            if (!empty($total_withdrawal['totalWithdrawal']) && !empty($total_withdrawal['totalNumberOfWithdrawal'])) {
                $average = ($total_withdrawal['totalWithdrawal'] / $total_withdrawal['totalNumberOfWithdrawal']);
            }
            $average_withdrawals = $average ? $average : '0';

            $arr = array(
                'status'                => 'success',
                'player'                => $this->player_manager->getPlayerById($player_id),
                'frozen'                => $balanceDetails['frozen'],
                'playerAccount'         => $playeraccount,
                'mainWallet'            => $mainwallet,
                'subWallet'             => $balanceDetails['sub_wallet'],
                'totalDeposits'         => $total_deposits,
                'totalWithdrawal'       => $total_withdrawal,
                'firstLastDeposit'      => $first_last_deposit,
                'firstLastWithdraw'     => $first_last_withdraw,
                'totalPromoBonus'       => $total_promo_bonus,
                'totalDepositBonus'     => $total_deposit_bonus,
                'totalCashbackBonus'    => $total_cashback_bonus,
                'totalReferralBonus'    => $total_referral_bonus,
                'totalBonusReceived'    => $total_bonus_received,
                'averageDeposits'       => $average_deposits,
                'averageWithdrawals'    => $average_withdrawals,
            );
            $this->output->set_content_type('application/json')->set_output(json_encode($arr));
        }
    //-----------------------------------------------ajaxAccountInfo----------------------------------------------------------

    public function ajaxGameInfo($player_id) {
        if (!$this->permissions->checkPermissions('player_list') || empty($player_id)) {
            $this->error_access();
        } else {
            $data['player'] = $this->player_model->getPlayerInfoById($player_id);

            $this->load->model('game_logs');
            $game_data = array(
                'total_bet_count'       => 0,
                'total_bet_ave'         => 0,
                'total_bet_sum'         => 0,
                'total_gain_count'      => 0,
                'total_gain_percent'    => 0,
                'total_gain_ave'        => 0,
                'total_gain_sum'        => 0,
                'total_loss_count'      => 0,
                'total_loss_percent'    => 0,
                'total_loss_ave'        => 0,
                'total_loss_sum'        => 0,
                'total_gain_loss_count' => 0,
                'total_gain_loss_sum'   => 0,
            );

            $game_platforms = $this->game_provider_auth->getGamePlatforms($player_id);
            $game_logs = $this->game_logs->getSummary($player_id);

            foreach ($game_platforms as &$game_platform) {
                $game_platform_id = $game_platform['id'];
                if (isset($game_logs[$game_platform_id])) {
                    $game_platform = array_merge($game_platform, $game_logs[$game_platform_id]);
                    $game_data['total_bet_sum']         += $game_logs[$game_platform_id]['bet']['sum'];
                    $game_data['total_bet_count']       += $game_logs[$game_platform_id]['bet']['count'];
                    $game_data['total_gain_sum']        += $game_logs[$game_platform_id]['gain']['sum'];
                    $game_data['total_gain_count']      += $game_logs[$game_platform_id]['gain']['count'];
                    $game_data['total_loss_sum']        += $game_logs[$game_platform_id]['loss']['sum'];
                    $game_data['total_loss_count']      += $game_logs[$game_platform_id]['loss']['count'];
                    $game_data['total_gain_loss_sum']   += $game_logs[$game_platform_id]['gain_loss']['sum'];
                    $game_data['total_gain_loss_count'] += $game_logs[$game_platform_id]['gain_loss']['count'];
                }
            }

            $game_data['total_bet_ave']      = $game_data['total_bet_count'] ? $game_data['total_bet_sum'] / $game_data['total_bet_count'] : 0;
            $game_data['total_gain_ave']     = $game_data['total_gain_count'] ? $game_data['total_gain_sum'] / $game_data['total_gain_count'] : 0;
            $game_data['total_gain_percent'] = $game_data['total_gain_count'] ? (($game_data['total_gain_count'] / $game_data['total_bet_count']) * 100) : 0;
            $game_data['total_loss_ave']     = $game_data['total_loss_count'] ? $game_data['total_loss_sum'] / $game_data['total_loss_count'] : 0;
            $game_data['total_loss_percent'] = $game_data['total_loss_count'] ? (($game_data['total_loss_count'] / $game_data['total_bet_count']) * 100) : 0;
            if($this->utils->getConfig('use_total_hour')){
                $game_data['total_result_percent'] = $game_data['total_bet_sum'] ? (($game_data['total_gain_loss_sum'] / $game_data['total_bet_sum']) * 100) : 0;
            }
            $game_data['game_platforms'] = $game_platforms;
            $game_data['admin_user_id']  = $this->session->userdata('user_id');

            $data['game_data'] = $game_data;
            $data['isAGGameAccountDemoAccount'] = false;
            $ag_api = $this->utils->loadExternalSystemLibObject(AG_API);
            if ($ag_api && $this->utils->isEnabledFeature('create_ag_demo')) {
                $gameName = $this->game_provider_auth->getGameUsernameByPlayerUsername($data['player']['username'], AG_API);
                if (!empty($gameName)) {
                    $agDemoAmoAccount = $this->game_provider_auth->isGameAccountDemoAccount($gameName, AG_API);
                    if (!$agDemoAmoAccount) {
                        $data['isAGGameAccountDemoAccount'] = true;
                    }
                }
            }

            $data['isAGINGameAccountDemoAccount'] = false;
            $agin_api = $this->utils->loadExternalSystemLibObject(AGIN_API);
            if ($agin_api && $this->utils->isEnabledFeature('create_agin_demo')) {
                $gameNameAGIN = $this->game_provider_auth->getGameUsernameByPlayerUsername($data['player']['username'], AGIN_API);
                if (!empty($gameNameAGIN)) {
                    $aginDemoAccount = $this->game_provider_auth->isGameAccountDemoAccount($gameNameAGIN, AGIN_API);
                    if (!$aginDemoAccount) {
                        $data['isAGINGameAccountDemoAccount'] = true;
                    }
                }
            }

            $this->load->view('player_management/user_information/ajax_game_info', $data);
        }
    }
    //-----------------------------------------------ajaxGameInfo----------------------------------------------------------
        /**
         * overview : create game provider account
         *
         * @param $playerId
         * @param $gameId
         * @param bool|false $isDemoFlag
         */
        public function createGameProviderAccount($playerId, $gameId, $isDemoFlag = false) {
            $this->load->model(array('player_model', 'wallet_model','external_system', 'game_provider_auth'));
            $player = $this->player_model->getPlayerById($playerId);
            $playerName = $player->username;
            $password = $this->salt->decrypt($player->password, $this->getDeskeyOG());

            # COPIED FROM  Game_platform_manager->createPlayerOnAllPlatforms();
            $api = $this->utils->loadExternalSystemLibObject($gameId);
            $api->getPlayerToken($playerId);

            if(empty($password)){
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Password is empty'));
                redirect('/player_management/userInformation/' . $playerId . '/9');
                return ;
            }else{
                //update empty password
                $this->game_provider_auth->updateEmptyPassword($playerId, $password);
            }

            if (!empty($api)) {

                # before create player check first if game was to maintenance
                $isMaintenance = $this->external_system->isGameApiMaintenance($gameId);
                if ($isMaintenance) {
                    $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Game is under maintenance'));
                    redirect('/player_management/userInformation/' . $playerId . '/9');
                }
                $create_result=null;
                $success=$this->wallet_model->lockAndTransForPlayerBalance($playerId, function ()
                use ($playerId, $playerName, $gameId, $password, $isDemoFlag, $api, &$create_result) {

                    $success=true;
                    $create_result = $api->createPlayer($playerName, $playerId, $password, null,
                        ['is_demo_flag' => $isDemoFlag, 'ip'=>$this->utils->getIP()]);

                    if ((isset($create_result['success']) && $create_result['success']) || (isset($create_result['user_exists']) && $create_result['user_exists'])) {
                        $api->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE, $isDemoFlag);
                        # check sub-wallet first
                        # if don't exist then create it
                        if (!$this->wallet_model->getSubWalletBy($playerId, $gameId)) {
                            $this->wallet_model->insertSubWallet($playerId, $gameId, 0);
                        }
                    } else {
                        $success=false;
                    }

                    $this->savePlayerUpdateLog($playerId, lang('reg.47') . ' - ' .
                        lang('adjustmenthistory.title.beforeadjustment') . ' (' . lang('member.noAccount') . ') ' .
                        lang('adjustmenthistory.title.afteradjustment') . ' (' . lang('reg.47') . ' - ' . $api->getPlatformCode() . ') ', $this->authentication->getUsername()); // Add log in playerupdatehistory
                    return $success;
                });

                if(!$success){
                    $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('report.log07'));
                    return redirect('/player_management/userInformation/' . $playerId . '/9');
                }

                # after create/check player, we should sync wallet of player in playeraccount
                # NOTE: syncSubWallet also invokes insertSubWallet
                $isRegisteredFlag = $this->game_provider_auth->isRegisterd($playerId, $gameId);
                if ($isRegisteredFlag) {
                    $balance_result = $api->queryPlayerBalance($playerName);
                    if (isset($balance_result['balance']) && $balance_result['balance']) {
                        $balance = $balance_result['balance'];
                        //lock with trans
                        $api->updatePlayerSubwalletBalance($playerId, $balance);
                    }
                }

            }
            if (isset($create_result['success']) && $create_result['success']) {
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('report.log06'));
                redirect('/player_management/userInformation/' . $playerId . '/9', 'refresh');
            } else {
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('report.log07'));
                redirect('/player_management/userInformation/' . $playerId . '/9', 'refresh');
            }
        }

        /**
         * overview : refresh all games
         *
         * details : query all ggame to check if player exists
         *
         * @param int $player_id            player_id
         */
        public function refreshAllGames($player_id) {

            $playerName = $this->player_model->getUsernameById($player_id);

            # 1 Query all game api
            $api_list = $this->utils->getAllCurrentGameSystemList();

            foreach ($api_list as $game_platform_id) {
                $api = $this->utils->loadExternalSystemLibObject($game_platform_id);
                if ($api) {

                    $rlt=$api->isPlayerExist($playerName);

                    # check if player exists and check if player blocked
                    $game_api_result = $rlt['success'] && @$rlt['exists'] && $api->isBlocked($playerName);

                    # refresh db flag by game api result
                    $this->CI->game_provider_auth->updateBlockStatusInDB($player_id, $game_platform_id, $game_api_result);
                }
            }

            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('report.log06'));

            redirect('/player_management/userInformation/' . $player_id . '/9');
        }

        /**
         * overview : reset player
         *
         * @param int $player_id            player_id
         * @param int $game_platform_id     game_platform_id
         */
        public function reset_player($player_id, $game_platform_id) {
            $api = $this->utils->loadExternalSystemLibObject($game_platform_id);
            if ($api) {

                $username = $this->player_model->getUsernameById($player_id);
                $password = $this->player_model->getPasswordByUsername($username);

                if (!empty($password) && !empty($username)) {
                    $success = true;

                    $rlt = $api->isPlayerExist($username);
                    $success = $rlt['success'];
                    if ($success && !$rlt['exists']) {
                        $rlt = $api->createPlayer($username, $player_id, $password, null,
                            ['ip'=>$this->utils->getIP()]);
                        $success = $success && $rlt['success'];
                    }
                    $this->utils->debug_log('isPlayerExist', $rlt);

                    # OG-1401 Run resetPlayer, unblockPlayer, syncPassword
                    if ($success) {
                        $rlt = $api->resetPlayer($username);
                        $success = $success && $rlt['success'];
                    }
                    $this->utils->debug_log('resetPlayer', $rlt);

                    if ($success) {
                        $rlt = $api->unblockPlayer($username);
                        $success = $success && $rlt['success'];
                    }
                    $this->utils->debug_log('unblockPlayer', $rlt);

                    if ($success) {
                        $rlt = $api->changePassword($username, $password, $password);
                        $success = $success && $rlt['success'];
                    }
                    $this->utils->debug_log('changePassword', $rlt);

                    if ($success) {
                        $rlt = $api->logout($username);
                        $success = $success && $rlt['success'];
                    }
                    $this->utils->debug_log('logout', $rlt);
                    if ($success) {
                        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('report.log06'));
                    } else {
                        $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('report.log07'));
                    }
                } else {
                    $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('report.log07'));
                }

            } else {
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('report.log07'));
            }

            redirect('/player_management/userInformation/' . $player_id . '/9');
        }

        /**
         * overview : sync password
         *
         * @param int $player_id            player_id
         * @param int $game_platform_id     game_platform_id
         */
        public function syncPassword($player_id, $game_platform_id) {
            $api = $this->utils->loadExternalSystemLibObject($game_platform_id);
            if ($api) {

                $username = $this->player_model->getUsernameById($player_id);
                $password = $this->player_model->getPasswordByUsername($username);

                if (!empty($password)) {
                    # API UPDATES THE game_provider_auth ALREADY IF NECESSARY
                    $this->load->model('game_provider_auth');
                    $oldPassword = $this->game_provider_auth->getPasswordByPlayerId($player_id, $game_platform_id);
                    $rtn = $api->changePassword($username, $oldPassword, $password);
                    if ($rtn['success'] && !@$rtn['unimplemented']) {
                        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('report.log06'));
                    } else {
                        if(@$rtn['unimplemented']){
                            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('report.log06'));
                            $this->CI->utils->debug_log('Change password is unimplemented GAME PROVIDER', $game_platform_id);
                        }else{
                            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('report.log07'));
                        }
                    }
                } else {
                    $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('report.log07'));
                }
            } else {
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('report.log07'));
            }

            redirect('/player_management/userInformation/' . $player_id . '/9');
        }

        /**
         * overview : update player gameinfo
         *
         * @param int $player_id            player_id
         * @param int $game_platform_id     game_platform_id
         */
        public function update_game_info_view($player_id, $game_platform_id, $system_code){
            $data['playerId'] = $player_id;
            $data['game_platform_id'] = $game_platform_id;
            $data['system_code'] = urldecode($system_code);
            $player_info = $this->player_model->getPlayerInfoById($player_id);
            $api  = $this->utils->loadExternalSystemLibObject($game_platform_id);
            $params = $params = array(
                "parameter" => "Value",
            );
            if($game_platform_id == ONEWORKS_API){
                $oneworks_player_max_transfer = $api->getSystemInfo('oneworks_player_max_transfer');
                $oneworks_player_min_transfer = $api->getSystemInfo('oneworks_player_min_transfer');
                $oneworks_vendor_id = $api->getSystemInfo('oneworks_vendor_id');
                $vendor_member_id = $api->getGameUsernameByPlayerUsername($player_info['username']);
                $params = array(
                    "vendor_id" => $oneworks_vendor_id,
                    "vendor_member_id" => $vendor_member_id,
                    "firstname" => $vendor_member_id,
                    "lastname" => $vendor_member_id,
                    "maxtransfer" => $oneworks_player_max_transfer,
                    "mintransfer" => $oneworks_player_min_transfer,
                );
            }

            $sbobet_api_list = array(SBOBET_API, SBOBETGAME_API, SBOBETV2_GAME_API, SBOBETGAME_IDR_B1_API, SBOBETGAME_THB_B1_API,SBOBETGAME_USD_B1_API, SBOBETGAME_VND_B1_API, SBOBETGAME_MYR_B1_API);
            if (in_array($game_platform_id, $sbobet_api_list)){
                $params = array(
                    "min" => $api->minimum_bet,
                    "max" => $api->maximum_bet,
                    "maxPerMatch" => $api->maxPerMatch,
                    "casinoTableLimit" => $api->casino_table_limit,
                );
            }
            $data['json'] = json_encode($params,JSON_PRETTY_PRINT);

            $this->template->add_js('resources/js/highlight.pack.js');
            $this->template->add_js('resources/js/ace/ace.js');
            $this->template->add_js('resources/js/ace/mode-json.js');
            $this->template->add_css('resources/css/hljs.tomorrow.css');
            $this->loadTemplate(lang('Player Management'), '', '', 'player');
            $this->template->write_view('sidebar', 'player_management/sidebar');
            $this->template->write_view('main_content', 'player_management/view_user_update_game_information',$data);
            $this->template->render();
            $this->utils->endEvent('template_render');
        }

        /**
         * overview : block game provider account
         *
         * @param int $playerId     player_id
         * @param int $gameId       game_id
         */
        public function blockGameProviderAccount($playerId, $gameId) {
            $this->load->model('player_model');
            $player = $this->player_model->getPlayerById($playerId);
            $playerName = $player->username;
            $api = $this->utils->loadExternalSystemLibObject($gameId);
            $api->blockPlayer($playerName);
            redirect('/player_management/userInformation/' . $playerId . '/9', 'refresh');
        }

        /**
         * overview : unblock game provider account
         *
         * @param int $playerId     player_id
         * @param int $gameId       game_id
         */
        public function unblockGameProviderAccount($playerId, $gameId) {
            $this->load->model('player_model');
            $player = $this->player_model->getPlayerById($playerId);
            $playerName = $player->username;
            $api = $this->utils->loadExternalSystemLibObject($gameId);
            $api->unblockPlayer($playerName);
            redirect('/player_management/userInformation/' . $playerId . '/9', 'refresh');
        }

        /**
         * overview : Set bet member setting
         *
         * @param int $playerId     player_id
         * @param int $gameId       game_id
         */
        public function setMemberBetSetting($playerId, $gameId) {
            if (!$this->permissions->checkPermissions('set_player_bet_limit_for_api')) {
                $this->error_access();
                return;
            }

            $sports_book = array(ONEWORKS_API,IBC_API);
            if (in_array($gameId, $sports_book)){
                return $this->update_oneworks_game_bet_limit_view($playerId, $gameId);
            }
            if (in_array($gameId, [NTTECH_V2_API,NTTECH_V2_IDR_B1_API,NTTECH_V2_CNY_B1_API,NTTECH_V2_THB_B1_API,NTTECH_V2_USD_B1_API,NTTECH_V2_VND_B1_API,NTTECH_V2_MYR_B1_API])){
                return $this->update_nttech_game_bet_limit_view($playerId, $gameId);
            }
            if (in_array($gameId, [OGPLUS_API])) {
            	return $this->update_ogplus_game_bet_limit_view($playerId, $gameId);
            }

            $sbobet_api_list = array(SBOBET_API, SBOBETGAME_API, SBOBETV2_GAME_API, SBOBETGAME_IDR_B1_API, SBOBETGAME_THB_B1_API,SBOBETGAME_USD_B1_API, SBOBETGAME_VND_B1_API, SBOBETGAME_MYR_B1_API);
            if (in_array($gameId, $sbobet_api_list)){
                return $this->update_sbobet_game_bet_limit_view($playerId, $gameId);
            }
            $this->load->model('player_model');
            $player = $this->player_model->getPlayerById($playerId);
            $playerName = $player->username;
            $api = $this->utils->loadExternalSystemLibObject($gameId);
            $rlt = $api->setMemberBetSetting($playerName);
            $success = $rlt['success'];
            if($success) {
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('report.log06'));
            } else {
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('report.log07'));
            }
            redirect('/player_management/userInformation/' . $playerId . '#game_form', 'refresh');
        }
    //-----------------------------------------------ajaxGameInfo----------------------------------------------------------

	/**
	 * Players Logs UI via ajax
	 *
	 * @param [type] $player_id
	 * @return void
	 */
    public function ajaxPlayerLog($player_id) {

		$this->load->model(array('game_logs', 'sale_order'));

        $data['player'] = $this->player_model->getPlayerInfoById($player_id);
		$data['player_id'] = $data['player']['playerId'];

		// @todo pre-load UI for [data-load] options.
		// $data['view'] = [];
		// $data['view']['duplicate_account_modal'] = $this->load->view('report_management/duplicate_account_modal', null, true);
		// $data['view']['ajax_ui_sale_order'] = $this->load->view('player_management/ajax_ui_sale_order', null, true);


        $this->load->view('player_management/user_information/ajax_player_log', $data);
    }
	//-----------------------------------------------ajaxPlayerLog----------------------------------------------------------
    //-----------------------------------------------ajaxPlayerLog----------------------------------------------------------

	/**
	 * overview : responsible gaming setting
	 *
	 * detail : list of responsible gaming operator settings
	 */
	public function responsibleGamingSetting() {
		if (!$this->permissions->checkPermissions('responsible_gaming_setting')) {
			$this->error_access();
		} else {
			$this->load->model('operatorglobalsettings');
			$data['respGameData']['self_exclusion_approval_day_cnt'] = $this->operatorglobalsettings->getSettingValue('self_exclusion_approval_day_cnt');
            $data['respGameData']['self_exclusion_cooling_off_day_cnt'] = $this->operatorglobalsettings->getSettingValue('self_exclusion_cooling_off_day_cnt');
			$data['respGameData']['cool_off_approval_day_cnt'] = $this->operatorglobalsettings->getSettingValue('cool_off_approval_day_cnt');
			$data['respGameData']['deposit_limit_approval_day_cnt'] = $this->operatorglobalsettings->getSettingValue('deposit_limit_approval_day_cnt');
			$data['respGameData']['loss_limit_approval_day_cnt'] = $this->operatorglobalsettings->getSettingValue('loss_limit_approval_day_cnt');
			$data['respGameData']['player_reactication_day_cnt'] = $this->operatorglobalsettings->getSettingValue('player_reactication_day_cnt');
            $data['respGameData']['automatic_reopen_temp_self_exclusion_account'] = $this->operatorglobalsettings->getSettingValue('automatic_reopen_temp_self_exclusion_account');
            $data['respGameData']['disable_and_hide_wagering_limits'] = $this->operatorglobalsettings->getSettingValue('disable_and_hide_wagering_limits');

			$this->loadTemplate('Player Management', '', '', 'player');
			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/responsible_gaming_operator_settings', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : post responsible gaming setting
	 *
	 * detail : list of responsible gaming operator settings
	 */
	public function postResponsibleGamingSetting() {
		$this->load->model('operatorglobalsettings');
		$self_exclusion_txt = (int)$this->input->post('self_exclusion_txt');
        $self_exclusion_cooling_off_txt = (int)$this->input->post('self_exclusion_cooling_off_txt');
		$cool_off_txt = (int)$this->input->post('cool_off_txt');
		$deposit_limit_txt = (int)$this->input->post('deposit_limit_txt');
		$loss_limit_txt = (int)$this->input->post('loss_limit_txt');
		$reactivation_txt = (int)$this->input->post('reactivation_txt');
        $reopen_temp_self_exclusion_account_txt = (int)$this->input->post('reopen_temp_self_exclusion_account_txt');
        $disable_and_hide_wagering_limits_txt = (int)$this->input->post('disable_and_hide_wagering_limits_txt');

        $this->operatorglobalsettings->putSetting('self_exclusion_approval_day_cnt', $self_exclusion_txt);
        $this->operatorglobalsettings->putSetting('self_exclusion_cooling_off_day_cnt', $self_exclusion_cooling_off_txt);
        $this->operatorglobalsettings->putSetting('cool_off_approval_day_cnt', $cool_off_txt);
        $this->operatorglobalsettings->putSetting('deposit_limit_approval_day_cnt', $deposit_limit_txt);
        $this->operatorglobalsettings->putSetting('loss_limit_approval_day_cnt', $loss_limit_txt);
        $this->operatorglobalsettings->putSetting('player_reactication_day_cnt', $reactivation_txt);
        $this->operatorglobalsettings->putSetting('automatic_reopen_temp_self_exclusion_account', $reopen_temp_self_exclusion_account_txt);
        $this->operatorglobalsettings->putSetting('disable_and_hide_wagering_limits', $disable_and_hide_wagering_limits_txt);

		$message = lang('Successfully updated the settings!');
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);

		$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Edit Responsible Gaming Settings', "User " . $this->authentication->getUsername() . " has successfully updated responsible gaming settings.");

		redirect('player_management/responsibleGamingSetting');
	}

    /**
     * overview : get all tags only
     *
     * detail : @return json_data for checking tag status
     *
     *
     */
    public function getAllTagsOnly() {
        $this->load->model(array('player'));
        $tags = $this->player->getAllTagsOnly();

		$this->returnJsonResult($tags);
    }

    /**
     * overview : tag multi player
     *
     * detail : add tags in responsible report
     */
    public function addTagMultiplayer(){
        $rtStatus['status']=1;
        $request = $this->input->post();
        $tagPlayerIdArr = $request['tagPlayerId'];
        $tagVal = $request['tagVal'];
        if(is_array($tagPlayerIdArr)){
            if(count($tagPlayerIdArr)>0){

                $this->load->model(array('player'));
                $user_id = $this->authentication->getUserId();
                $today = date("Y-m-d H:i:s");
                $insflg = 0;
                foreach($tagPlayerIdArr as $playerId){
                    //get multi tag"s"
                    $tagged = $this->player->getPlayerTags($playerId);
                    if (FALSE === $tagged) {
                        $keptagArr = [];
                        $insflg=1;
                    }else{
                        foreach($tagged as $playerTag){
                            $keptagArr[]= $playerTag['tagId'];
                        }

                        if (in_array($tagVal, $keptagArr)) {
                            continue;
                        }else{
                            $insflg=1;
                        }
                    }

                    if($insflg){
                        $data = array(
                            'playerId' => $playerId,
                            'taggerId' => $user_id,
                            'tagId' => $tagVal,
                            'createdOn' => $today,
                            'updatedOn' => $today,
                            'status' => 1,

                        );

                        $this->player->insertAndGetPlayerTag($data);
                        $rtStatus['status']=1;
                    }
                }
            }
        }

		$this->returnJsonResult($rtStatus);
    }

	/**
	 * overview : refresh account information
	 *
	 * detail : will load view to account information
	 *
	 * @param int $player_id	player_id
	 */
	public function refreshAccountInfo($player_id) {
		$this->player_manager->updateBalances($player_id);

		$data['playeraccount'] = $this->player_manager->getPlayerAccount($player_id);

		$data['total_deposits'] = $this->player_manager->getPlayerTotalDeposits($data['playeraccount']['playerAccountId']);
		$data['total_withdrawal'] = $this->player_manager->getPlayerTotalWithdrawal($data['playeraccount']['playerAccountId']);
		$data['mainwallet'] = $this->player_manager->getMainWallet($player_id);
		$data['subwallet'] = $this->payment_manager->getAllPlayerAccountByPlayerId($player_id);
		$data['total_deposit_bonus'] = $this->player->getTotalBonus($player_id);
		$data['total_cashback_bonus'] = $this->player->getTotalCashbackBonus($player_id);
		$data['total_referral_bonus'] = $this->player->getTotalReferralBonus($player_id);

		$average = "";
		if (!empty($data['total_deposits']['totalDeposit']) && !empty($data['total_deposits']['totalNumberOfDeposit'])) {
			$average = ($data['total_deposits']['totalDeposit'] / $data['total_deposits']['totalNumberOfDeposit']);
		}
		$data['average_deposits'] = $average ? $average : '0';

		$average = "";
		if (!empty($data['total_withdrawal']['totalWithdrawal']) && !empty($data['total_withdrawal']['totalNumberOfWithdrawal'])) {
			$average = ($data['total_withdrawal']['totalWithdrawal'] / $data['total_withdrawal']['totalNumberOfWithdrawal']);
		}
		$data['average_withdrawals'] = $average ? $average : '0';

		$this->load->view('player_management/ajax_account_information', $data);
	}

    /**
     * overview : check the selected security question for its right translation
     * @param string $data
     * @return string
     **/
    public function checkSecQuestionSelected($data){
        $this->load->helper('language');
        $options = ['reg.37', 'reg.38', 'reg.39', 'reg.40', 'reg.41'];
        $languages = [
            language_function::INT_LANG_ENGLISH,
            language_function::INT_LANG_CHINESE,
            language_function::INT_LANG_INDONESIAN,
            language_function::INT_LANG_VIETNAMESE,
            language_function::INT_LANG_KOREAN,
            language_function::INT_LANG_THAI
        ];

        foreach($languages as $key){
            foreach($options as $val){
                $res = lang($val);
                if($res == lang($data)){
                    return $val;
                }
            }
        }
        return null;
    }

	/**
	 * overview : check modified fields
	 *
	 * @param $player_id	player_id
	 * @param $new_data
	 * @return string
	 */
	public function checkModifiedFields($player_id, $new_data) {
		$old_data = $this->player_model->getPlayerInfoById($player_id);
		$diff = array_diff_assoc($new_data, $old_data);

		foreach ($diff as $key => $value) {
			$changes[lang('reg.fields.' . $key) ?: $key] = [
				'old' => $old_data[$key],
				'new' => $new_data[$key],
			];
		}

        $output = '<ul>';
        if(!empty($changes)){
            ksort($changes);
            if ($changes && is_array($changes) && !empty($changes)) {
                foreach ($changes as $key => $value) {
                    $output .= "<li>{$key}:<br><code>Old: {$value['old']}</code><br><code>New: {$value['new']}</code></li>";
                }
            }
        }
        $output .= '</ul>';

		return $output;
	}

	/**
	 * overview : sign up information
	 *
	 * details : will load view to player signup information
	 *
	 * @param $player_id	player_id
	 */
	public function viewSignupInformation($player_id) {
		$data['player'] = $this->player_manager->getPlayerById($player_id);
		$data['today'] = date("Y-m-d H:i:s");
		$data['title'] = "Signup Information";

		$this->load->view('player_management/pl_signup_information', $data);
	}

	/**
	 * overview : personal information information
	 *
	 * details : will load view to player personal information
	 *
	 * @param $player_id	player_id
	 */
	public function viewPersonalInformation($player_id) {
		$data['player'] = $this->player_manager->getPlayerById($player_id);
		$data['age'] = $data['player']['birthdate'] == 0 ? 0 : $this->player_manager->get_age($data['player']['birthdate']);
		$data['today'] = date("Y-m-d H:i:s");

		$this->load->view('player_management/pl_personal_information', $data);
	}

	/**
	 * overview : balance information information
	 *
	 * details : will load view to player balance information
	 *
	 * @param $player_id	player_id
	 */
	public function viewBalanceInformation($player_id) {
		$data['player'] = $this->player_manager->getPlayerById($player_id);
		$data['playeraccount'] = $this->player_manager->getPlayerAccount($player_id);
		$data['total_deposits'] = $this->player_manager->getPlayerTotalDeposits($data['playeraccount']['playerAccountId']);
		$data['total_withdrawal'] = $this->player_manager->getPlayerTotalWithdrawal($data['playeraccount']['playerAccountId']);
		$data['mainwallet'] = $this->player_manager->getMainWallet($player_id);
		$data['subwallet'] = $this->payment_manager->getAllPlayerAccountByPlayerId($player_id);
		$average = "";
		if (!empty($data['total_deposits']['totalDeposit']) && !empty($data['total_deposits']['totalNumberOfDeposit'])) {
			$average = ($data['total_deposits']['totalDeposit'] / $data['total_deposits']['totalNumberOfDeposit']);
		}
		$data['average_deposits'] = $average ? $average : '0';

		$data['today'] = date("Y-m-d H:i:s");

		$this->load->view('player_management/pl_balance_information', $data);
	}

	/**
	 * overview : bonus information information
	 *
	 * details : will load view to player bonus information
	 *
	 * @param $player_id	player_id
	 */
	public function viewBonusInformation($player_id) {
		$data['player'] = $this->player_manager->getPlayerById($player_id);
		$data['playeraccount'] = $this->player_manager->getPlayerAccount($player_id);
		$data['today'] = date("Y-m-d H:i:s");

		$this->load->view('player_management/pl_bonus_information', $data);
	}

	/**
	 * overview : account information information
	 *
	 * details : will load view to player account information
	 *
	 * @param $player_id	player_id
	 */
	public function viewAccountInformation($player_id) {
		$data['player'] = $this->player_manager->getPlayerById($player_id);
		$data['playeraccount'] = $this->player_manager->getPlayerAccount($player_id);
		$data['games'] = $this->player_manager->getAllGames();
		$data['totalDeposit'] = $this->player_manager->getPlayerTotalDeposits($data['playeraccount']['playerAccountId'])['totalDeposit'];
		$data['totalWithdrawal'] = $this->player_manager->getPlayerTotalWithdrawal($data['playeraccount']['playerAccountId'])['totalWithdrawal'];
		$data['blocked_games'] = $this->player_manager->getPlayerBlockedGames($player_id);
		$data['hiddenPassword'] = $this->user_functions->randomizer($data['player']['username']); //will get randomized password when the page loads
		$data['tag'] = $this->player_manager->getPlayerTag($player_id);
		$data['today'] = date("Y-m-d H:i:s");

		$this->load->view('player_management/pl_account_information', $data);
	}


	/**
	 * overview : player deposit
	 *
	 * detail : load view to player_deposit after transaction
	 *
	 * @param int $player_id	player_id
	 */
	public function player_deposit($player_id) {
		if (!$this->permissions->checkPermissions('agency_player_deposit')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Player Management', '', '', 'player');

			$player_detail = $this->player_manager->getPlayerById($player_id);
			$data['player'] = $player_detail;
			$data['player_balance'] = $this->wallet_model->getMainWalletBalance($player_id);
			$data['agent'] = array();
			if (!empty($player_detail['agent_id'])) {
				$this->load->model(array('agency_model'));
				$data['agent'] = $this->agency_model->get_agent_by_id($player_detail['agent_id']);
			}
			$this->load->view('player_management/player_deposit', $data);
		}
	} // player_deposit }}}2

	/**
	 * overview : redirect to player information
	 *
	 * @param $player_id
	 */
	public function player_verify_deposit($player_id) {
		redirect("player_management/userInformation/" . $player_id);
	}

	/**
	 * overview : log transaction on player deposit
	 *
	 * detail : record credit transaction when deposit 'credit' for a player
	 *
	 * @param int $player_id			player_id
	 * @param string $player_name		player_name
	 * @param double $before_balance	balance
	 * @param int $agent_id				agent_id
	 * @param string $agent_name		agent_name
	 * @param double $amount			amount
	 */
	public function record_transaction_on_player_deposit($player_id, $player_name, $before_balance, $agent_id, $agent_name, $amount) {
		$total_balance = $this->wallet_model->getTotalBalance($player_id);
		$data = array(
			'transaction_type' => Transactions::FROM_AGENT_TO_PLAYER,
			'amount' => $amount,
			'from_type' => Transactions::AGENT,
			'from_id' => $agent_id,
			'from_username' => $agent_name,
			'to_type' => Transactions::PLAYER,
			'to_id' => $player_id,
			'to_username' => $player_name,
			'note' => lang('Credit transfer') . ' ' . lang('from agent to player'),
			'before_balance' => $before_balance,
			'after_balance' => $before_balance + $amount,
			'sub_wallet_id' => Wallet_model::MAIN_WALLET_ID,
			'total_before_balance' => $total_balance - $amount,
		);
		$this->load->model(array('transactions'));
		$transaction_id = $this->transactions->add_new_transaction($data);
		return $transaction_id;
	} // record_transaction_on_player_deposit  }}}2
	// player_withdraw {{{2

	/**
	 * overview : withdraw player
	 *
	 * detail : will load view to player withdraw
	 *
	 * @param $player_id	player_ud
	 */
	public function player_withdraw($player_id) {
		if (!$this->permissions->checkPermissions('agency_player_withdraw')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Player Management', '', '', 'player');

			$player_detail = $this->player_manager->getPlayerById($player_id);
			$data['player'] = $player_detail;
			$data['player_balance'] = $this->wallet_model->getMainWalletBalance($player_id);
			$data['agent'] = array();
			if (!empty($player_detail['agent_id'])) {
				$this->load->model(array('agency_model'));
				$data['agent'] = $this->agency_model->get_agent_by_id($player_detail['agent_id']);
			}
			$this->load->view('player_management/player_withdraw', $data);
		}
	} // player_withdraw }}}2

	/**
	 * overview : verifiy player withdraw
	 *
	 * detail : will redirect user information method
	 *
	 * @param $player_id	player_ud
	 */
	public function player_verify_withdraw($player_id) {
		redirect("player_management/userInformation/" . $player_id);
	}

	/**
	 * overview : log transaction on player deposit
	 *
	 * detail : record credit transaction when deposit 'credit' for a player
	 *
	 * @param int $player_id			player_id
	 * @param string $player_name		player_name
	 * @param double $before_balance	balance
	 * @param int $agent_id				agent_id
	 * @param string $agent_name		agent_name
	 * @param double $amount			amount
	 */
	public function record_transaction_on_player_withdraw($player_id, $player_name, $before_balance, $agent_id, $agent_name, $amount) {
		$total_balance = $this->wallet_model->getTotalBalance($player_id);
		$data = array(
			'transaction_type' => Transactions::FROM_PLAYER_TO_AGENT,
			'amount' => $amount,
			'from_type' => Transactions::PLAYER,
			'from_id' => $player_id,
			'from_username' => $player_name,
			'to_type' => Transactions::AGENT,
			'to_id' => $agent_id,
			'to_username' => $agent_name,
			'note' => lang('Credit transfer') . ' ' . lang('from player to agent'),
			'before_balance' => $before_balance,
			'after_balance' => $before_balance - $amount,
			'sub_wallet_id' => Wallet_model::MAIN_WALLET_ID,
			'total_before_balance' => $total_balance + $amount,
		);
		$this->load->model(array('transactions'));
		$transaction_id = $this->transactions->add_new_transaction($data);
		return $transaction_id;
	} // record_transaction_on_player_withdraw  }}}2
	// agency }}}1

	/**
	 * overview : reset password
	 *
	 * detail : will load view into reset password
	 *
	 * @param int $player_id	player_id
	 */
	public function resetPassword($player_id) {
		if (!$this->permissions->checkPermissions('reset_player_login_password')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Player Management', '', '', 'player');
			$data['player'] = $this->player_manager->getPlayerById($player_id);
			$this->load->view('player_management/reset_password', $data);
		}
	}

	/**
	 * overview : generate MD5 password
	 */
	public function getGeneratePassword() {
		//echo substr(str_shuffle(MD5(microtime())), 0, 12);
		$this->returnText(substr(str_shuffle(MD5(microtime())), 0, 12));
	}

	/**
	 * overview : reset player password
	 *
	 * detail : this will save player update logs and send email to player
	 *
	 * @param int $player_id	player_id
	 */
	public function playerResetPassword($player_id) {
		$this->form_validation->set_rules('password', lang('forgot.13') . ' ' . lang('player.56'), 'trim|required|xss_clean');
		if ($this->form_validation->run() == false) {
			$this->resetPassword($player_id);
		} else {
			$this->load->model(array('external_system', 'game_provider_auth', 'player_model', 'operatorglobalsettings'));

			$player = $this->player_manager->getPlayerById($player_id);
			$username=$player['username'];
			$newPassword = $this->input->post('password');
			$data = array(
				'password' => $newPassword,
			);

			// save player password history
			$this->player_model->insertPasswordHistory($player_id, Player_model::RESET_PASSWORD, $this->utils->encodePassword($newPassword));

			$this->player_manager->resetPassword($data, $player_id);

			$this->syncPlayerCurrentToMDBWithLock($player_id, $username, false);

			//call api
			$gameApis = $this->utils->getAllCurrentGameSystemList();
			foreach ($gameApis as $apiId) {
				$api = $this->utils->loadExternalSystemLibObject($apiId);
				$oldPassword = $this->game_provider_auth->getPasswordByPlayerId($player_id, $apiId);
				$api->changePassword($player['username'], $oldPassword, $newPassword);
			}

			#sending email
			$this->load->library(['email_manager']);
	        $template = $this->email_manager->template('player', 'player_change_login_password_successfully', array('player_id' => $player_id, 'new_login_password' => $newPassword));
	        $template_enabled = $template->getIsEnableByTemplateName();
	        if($template_enabled['enable']){
	        	$template->sendingEmail($player['email'], Queue_result::CALLER_TYPE_ADMIN, $this->authentication->getUserId());
	        }

			$this->savePlayerUpdateLog($player_id, lang('system.word8'), $this->authentication->getUsername()); // Add log in playerupdatehistory

			$message = lang('con.plm31') . " <b>" . $player['username'] . " </b> " . lang('con.plm43');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			redirect("player_management/userInformation/" . $player_id);
		}
	}

	/**
	 * overview : reset withdrawal password
	 *
	 * detail : will load view into reset password
	 *
	 * @param int $player_id	player_id
	 */
	public function resetWithdrawalPassword($player_id) {
		if (!$this->permissions->checkPermissions('reset_players_withdrawal_password')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Player Management', '', '', 'player');
			$data['player'] = $this->player_manager->getPlayerById($player_id);

			$this->load->view('player_management/reset_withdrawal_password', $data);
		}
	}

	/**
	 * overview : reset player password
	 *
	 * detail : this will save player update logs and send email to player
	 *
	 * @param int $player_id	player_id
	 */
	public function playerResetWithdrawalPassword($player_id) {
		$this->load->model('player_model');

		if (!$this->permissions->checkPermissions('reset_players_withdrawal_password')) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('con.plm01'), true);
			redirect("player_management/userInformation/" . $player_id);
		}

		$this->form_validation->set_rules('password', lang('forgot.13') . ' ' . lang('player.56'), 'trim|required|xss_clean');
		if ($this->form_validation->run() == false) {
			$this->resetWithdrawalPassword($player_id);
		} else {

			$player = $this->player_manager->getPlayerById($player_id);
			$new_withdrawal_password = $this->input->post('password');

			$this->player_model->resetPassword($player_id, array('withdraw_password' => $new_withdrawal_password));
			$this->syncPlayerCurrentToMDBWithLock($player_id, $player['username'], false);

			#sending email
			$this->load->library(['email_manager']);
	        $template = $this->email_manager->template('player', 'player_change_withdrawal_password_successfully', array('player_id' => $player_id, 'new_withdrawal_password' => $new_withdrawal_password));
	        $template_enabled = $template->getIsEnableByTemplateName();
	        if($template_enabled['enable']){
	        	$template->sendingEmail($player['email'], Queue_result::CALLER_TYPE_ADMIN, $this->authentication->getUserId());
	        }

			$this->savePlayerUpdateLog($player_id, lang('Withdraw Reset Password'), $this->authentication->getUsername());

			$message = lang('con.plm31') . " <b>" . $player['username'] . " </b> " . lang('Withdraw Reset Success');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message, true);
			redirect("player_management/userInformation/" . $player_id);
		}
	}

	/**
	 * overview : call back for confirm password
	 * @return	bool
	 */
	public function checkIfPasswordMatch() {
		//require_once(APPPATH . 'libraries/phpass-0.1/PasswordHash.php');
		//$hasher = new PasswordHash('8', TRUE);
		$player_id = $this->input->post('player_id');

		$result = $this->player_manager->getPlayerById($player_id);

		$new_password = $this->input->post('hiddenPassword');
		$confirm_new_password = $this->input->post('confirm');

		//if($hasher->CheckPassword($new_password, $result['password'])) {
		if ($this->salt->decrypt($result['password'], $this->getDeskeyOG()) == $new_password) {
			$this->form_validation->set_message('checkIfPasswordMatch', "New Password is the same as Old Password.");
			return false;
		} else if ($new_password != $confirm_new_password) {
			$this->form_validation->set_message('checkIfPasswordMatch', "Confirm New Password didn't match.");
			return false;
		}

		return true;
	}

	/**
	 * overview : view other information
	 *
	 * detail : will load view in player other information
	 *
	 * @param int $player_id	player id
	 */
	public function viewOtherInformation($player_id) {
		$data['player'] = $this->player_manager->getPlayerById($player_id);
		$data['tag'] = $this->player_manager->getPlayerTag($player_id);
		$data['today'] = date("Y-m-d H:i:s");

		$this->load->view('player_management/pl_other_information', $data);
	}

	/**
	 * overview : view sent messages
	 *
	 * @param int $player_id	player_id
	 */
	public function viewSentMessages($player_id) {
		$data['player'] = $this->player_manager->getPlayerById($player_id);
		$data['playeraccount'] = $this->player_manager->getPlayerAccount($player_id);
		$data['today'] = date("Y-m-d H:i:s");

		$this->load->view('player_management/pl_sent_messages', $data);
	}

	/**
	 * overview : check if player is active
	 *
	 * @param int $player_id	player_id
	 */
	public function isActivePlayer($player_id) {
		return $this->player_manager->isActivePlayer($player_id);
	}

	/**
	 * overview : friend referral settings
	 */
	public function friendReferralSettings() {
		$this->loadTemplate('Player Management', '', '', 'player');
		$this->template->write_view('sidebar', 'player_management/sidebar');
		$this->template->write_view('main_content', 'player_management/friend_referral_settings');
		$this->template->render();
	}

	/**
	 * overview : player tag management
	 *
	 * detail : add updating, deleting and listing of tags
	 */
	public function playerTagManagement() {
		$this->load->model(array('system_feature'));

		if (($this->session->userdata('sidebar_status') == NULL)) {
			$this->session->set_userdata(array('sidebar_status' => 'active'));
		}

		if (!$this->permissions->checkPermissions('taggedlist')) {
			$this->error_access();
		} else {
			$sort = "tagId";
			$data['tags'] = $this->utils->stripHtmlTagsOfArray($this->player_manager->getTags($sort, null, null));
			$this->loadTemplate('Player Management', '', '', 'player');
			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/player_tag_management', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : save tag details
	 *
	 * detail : player tag other options
	 *
	 */
	public function actionPlayerTagOtherOptions() {
		$this->load->model(array('system_feature', 'operatorglobalsettings'));

		$this->form_validation->set_rules('playerTagName', lang('player.tm02'), 'trim|required|xss_clean');
		$this->form_validation->set_rules('tagDescription', lang('player.tm04'), 'trim|required|xss_clean');
		$this->form_validation->set_rules('tagColor', lang('player.tm09'), 'trim|required|xss_clean');

		if ($this->form_validation->run() == false) {
			$message = validation_errors();
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		} else {
			$isTagExist = $this->player_manager->getPlayerTagByName($this->input->post('playerTagName'));
			if ($isTagExist && !$this->input->post('tagId')) {
				$message = lang('con.plm16');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			} else {
				$tagName              = $this->utils->stripHTMLtags($this->input->post('playerTagName'));
				$tagDescription       = $this->utils->stripHTMLtags($this->input->post('tagDescription'));
				$tagColor             = $this->input->post('tagColor');
				$tagId                = $this->input->post('tagId');
				$today                = date("Y-m-d H:i:s");
				$blockedPlayerTag     = $this->input->post('chkPlayerBlockTag');
				$blockedPlayerTagList = json_decode($this->operatorglobalsettings->getSettingJson('blocked_player_tag'), true);

				if (empty($blockedPlayerTagList)) {
					$blockedPlayerTagList = [];
				}

				if ($tagId) {
					$data = array(
						'tagName' => ucfirst($tagName),
						'tagDescription' => $tagDescription,
						'tagColor' => $tagColor,
						'updatedOn' => $today,
						'status' => 0,
					);

					if(!empty($blockedPlayerTag)){
                        if(in_array((int)$tagId, $blockedPlayerTagList)){
                            $pos = array_search((int)$tagId, $blockedPlayerTagList);
                            unset($blockedPlayerTagList[$pos]);
                        }else{
                            array_push($blockedPlayerTagList, (int)$tagId);
                        }
                        $this->operatorglobalsettings->syncSettingJson("blocked_player_tag", json_encode($blockedPlayerTagList), 'value');
                    }else{
                        if(in_array((int)$tagId, $blockedPlayerTagList)){
                            $pos = array_search((int)$tagId, $blockedPlayerTagList);
                            unset($blockedPlayerTagList[$pos]);
                            $this->operatorglobalsettings->syncSettingJson("blocked_player_tag", json_encode($blockedPlayerTagList), 'value');
                        }
                    }

					$this->player_manager->editTag($data, $tagId);
					$message = lang('con.plm45') . " <b>" . $tagName . "</b> " . lang('con.plm46');
					$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Edit Tag', "User " . $this->authentication->getUsername() . " has successfully edited " . ucfirst($tagName) . ".");
				} else {
					$data = array(
						'tagName' => ucfirst($tagName),
						'tagDescription' => $tagDescription,
						'tagColor' => $tagColor,
						'createBy' => $this->session->userdata('user_id'),
						'createdOn' => $today,
						'updatedOn' => $today,
						'status' => 0,
					);

					$tagId = $this->player_manager->insertTag($data);

					# add to operator setting if tag is used for player blocking
					if (!empty($blockedPlayerTag) && !empty($tagId)) {
						if (!in_array((int)$tagId, $blockedPlayerTagList)){
							array_push($blockedPlayerTagList, (int)$tagId);
							$this->operatorglobalsettings->syncSettingJson("blocked_player_tag", json_encode($blockedPlayerTagList), 'value');
						}
					}

					$message = lang('con.plm45') . " <b>" . $tagName . "</b> " . lang('Successfully added tag');
					$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Edit Tag', "User " . $this->authentication->getUsername() . " has successfully added " . ucfirst($tagName) . ".");
				}

				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			}
		}


		redirect('player_management/playerTagManagement');
	}

	/**
	 * overview : get tag pages
	 *
	 * @param string $segment
	 */
	public function get_tag_pages($segment = "") {
		$sort = "tagId";

		$data['count_all'] = count($this->player_manager->getTags($sort, null, null));
		$config['base_url'] = "javascript:get_tag_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 5;
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
		$data['tags'] = $this->player_manager->getTags($sort, $config['per_page'], $segment);
		// $data['active_tag'] = $this->user_functions->getActiveTag();

		$this->load->view('player_management/player_tag_management_pages', $data);
	}

	/**
	 * overview : sort tags
	 *
	 * @param $sort
	 */
	public function sortTag($sort) {
		$data['count_all'] = count($this->player_manager->getTags($sort, null, null));
		$config['base_url'] = "javascript:get_tag_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 5;
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
		$data['tags'] = $this->player_manager->getTags($sort, $config['per_page'], null);

		$this->load->view('player_management/player_tag_management_pages', $data);
	}

	/**
	 * overview : sort vip groups
	 *
	 * @param $sort
	 */
	public function sortVipgroup($sort) {
		$data['count_all'] = count($this->player_manager->getVIPSettingList($sort, null, null));
		$config['base_url'] = "javascript:get_vipgroupsetting_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 5;
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
		$data['data'] = $this->player_manager->getVIPSettingList($sort, $config['per_page'], null);

		$this->load->view('player_management/vipsetting/ajax_view_vip_setting_list', $data);
	}

	/**
	 * overview : activate vip group
	 *
	 * @param $vipsettingId		vipsetting_id
	 * @param $status			status
	 */
	public function activateVIPGroup($vipsettingId, $status) {
		$data['vipsettingId'] = $vipsettingId;
		$data['status'] = $status;

		$this->player_manager->activateVIPGroup($data);

		redirect('player_management/vipPlayerSettingList');
	}

	/**
	 * overview : search tag
	 *
	 * @param string $search
	 */
	public function searchTag($search = '') {
		$data['count_all'] = count($this->player_manager->getSearchTag($search, null, null));
		$config['base_url'] = "javascript:get_tag_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 5;
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
		$data['tags'] = $this->player_manager->getSearchTag($search, $config['per_page'], null);

		$this->load->view('player_management/player_tag_management_pages', $data);
	}

	/**
	 * overview : tag details
	 *
	 * @param int $tag_id		tag_id
	 */
	public function getTagDetails($tag_id) {
		$this->load->model(array('operatorglobalsettings'));
		$blockedPlayerTag = json_decode($this->operatorglobalsettings->getSettingJson('blocked_player_tag'), true);
		$tagDetails = $this->player_manager->getTagDetails($tag_id);

		if (!empty($tagDetails) && !empty($blockedPlayerTag) && in_array($tagDetails[0]['tagId'], $blockedPlayerTag)) {
			$tagDetails[0]['blockedPlayerTag'] = true;
		}

		$this->returnJsonResult($tagDetails);
	}

	/**
	 * overview : vip group detials
	 *
	 * @param int $vipsetting_id	vipsetting_id
	 */
	public function getVIPGroupDetails($vipsetting_id) {
		//echo json_encode($this->player_manager->getVIPGroupDetails($vipsetting_id));
		$this->returnJsonResult($this->player_manager->getVIPGroupDetails($vipsetting_id));
	}

	/**
	 * Delete tag
	 *
	 * @param 	int
	 * @return	redirect
	 */
	public function deleteTag($tag_id) {
		$this->player_manager->deletePlayerTag($tag_id);
		$this->player_manager->deleteTag($tag_id);

		$message = lang('con.plm47');
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Delete Tag', "User " . $this->authentication->getUsername() . " has successfully deleted tag.");
		redirect('player_management/playerTagManagement');
	}

	/**
	 * overview : delete chat history
	 *
	 * @return redirect
	 */
	public function deleteSelectedTag() {
		$tag = $this->input->post('tag');
		$today = date("Y-m-d H:i:s");
		$tags = '';

		if ($tag != '') {
			foreach ($tag as $tagId) {
				$this->player_manager->deletePlayerTag($tagId);
				$this->player_manager->deleteTag($tagId);
			}

			$message = lang('con.plm48');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message); //will set and send message to the user
			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Delete Tags', "User " . $this->authentication->getUsername() . " has successfully deleted tags.");
			redirect('player_management/playerTagManagement');
		} else {
			$message = lang('con.plm49');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			redirect('player_management/playerTagManagement');
		}
	}

	/**
	 * overview : block player in game
	 *
	 * @param int $player_id	player_id
	 * @param $page
	 */
	public function blockPlayerInGame($player_id, $page) {
		$data['player_game'] = $this->player_manager->getPlayerGame($player_id);
		$data['player'] = $this->player_manager->getPlayerById($player_id);
		$data['games'] = $this->player_manager->getAllGames();
		//$this->load->view('player_management/block_player_game', $data);
		$this->load->view('player_management/block_player_in_period', $data);
	}

	/**
	 * overview : block and unblock player in game
	 *
	 * details : change player game blocked, update player game logs
	 */
	public function blockUnblockFreezePlayerInGame() {
		if ($this->input->post('period') == 'frozen') {
			$this->form_validation->set_rules('start_date', lang('player.ap09'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('end_date', lang('player.ap10'), 'trim|required|xss_clean');
		}
		$this->form_validation->set_rules('game', lang('player.ap11'), 'required|xss_clean');
		$this->form_validation->set_rules('period', lang('player.ap06'), 'trim|required|xss_clean');

		if (!$this->form_validation->run()) {
			$message = lang('con.plm51');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			redirect('player_management/viewAllPlayer', 'refresh');
		}

		$period = $this->input->post('period');
		$today = date("Y-m-d H:i:s");
		$user_id = $this->authentication->getUserId();

		if ($period == 'frozen') {
			$start = $this->input->post('start_date');
			$end = $this->input->post('end_date');

			if ($start >= $end) {
				$message = lang('con.plm52');
			} else {
				foreach (explode(', ', $this->input->post('players')) as $player_id) {
					foreach ($this->input->post('game') as $game_id) {
						$game_provider = $this->player_manager->getGameById($game_id);
						$data = array(
							'blockedStart' => $start,
							'blockedEnd' => date('Y-m-d', strtotime($end)) . ' 23:59:59',
							'blocked' => self::GAME_FROZEN,
						);

						$this->player_manager->changePlayerGameBlocked($player_id, $game_id, $data);
						$this->savePlayerUpdateLog($player_id, lang('player.ap03') . ' (' . $game_provider['game'] . ') ' . lang('player.80') . ' ' . $start . ' ' . lang('player.81') . ' ' . $end, $this->authentication->getUsername()); // Add log in playerupdatehistory
					}
				}
				$message = lang('con.plm53');
			}
		} else {
			foreach (explode(', ', $this->input->post('players')) as $player_id) {
				foreach ($this->input->post('game') as $game_id) {
					$game_provider = $this->player_manager->getGameById($game_id);
					$data = array(
						'blockedStart' => '0000-00-00',
						'blockedEnd' => '0000-00-00',
					);
					$data['blocked'] = $period == 'block' ? self::GAME_BLOCK : self::GAME_UNBLOCK;

					$this->player_manager->changePlayerGameBlocked($player_id, $game_id, $data);
					$this->savePlayerUpdateLog($player_id, lang('player.ap03') . ' (' . $game_provider['game'] . ') ' . lang('player.ap07'), $this->authentication->getUsername()); // Add log in playerupdatehistory
				}
			}
			$message = $period == 'block' ? lang('con.plm54') : lang('con.plm70');

		}
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		redirect('player_management/viewAllPlayer', 'refresh');
	}

	/**
	 * overview : tag description
	 *
	 * detail : return json data
	 *
	 * @param int $tag_id	tag_id
	 */
	public function getTagDescription($tag_id) {
		//echo json_encode($this->player_manager->getTagDescription($tag_id));
		$this->returnJsonResult($this->player_manager->getTagDescription($tag_id));
	}

	/**
	 * overview : lock player
	 *
	 * detail : will load view in locked player
	 *
	 * @param int $player_id player_id
	 * @param $page
	 */
	public function lockedPlayer($player_id, $page) {
		$data['player_id'] = $player_id;
		$data['player'] = $this->player_manager->getPlayerById($player_id);
		$data['page'] = $page;

		$this->load->view('player_management/locked_player', $data);
	}

	/**
	 * overview : block player in period
	 *
	 * detail : get player game by game id, load view in block player in period
	 *
	 * @param int $player_id	player_id
	 * @param int $game_id		game_id
	 * @param $page
	 */
	public function blockPlayerInPeriod($player_id, $game_id, $page) {
		$data['player_id'] = $player_id;
		$data['player'] = $this->player_manager->getPlayerById($player_id);
		$data['page'] = $page;
		$data['player_game'] = $this->player_manager->getPlayerGameByGameId($player_id, $game_id);
		$this->load->view('player_management/block_player_in_period', $data);
	}

	/**
	 * overview : check selected players
	 *
	 * detail : get player details in game, tags and all player levels
	 */
	public function selectedPlayers() {
		if ($this->input->post('players')) {
			$players = $this->input->post('players');

			if (is_string($this->input->post('players'))) {
				$players = explode(', ', $this->input->post('players'));
			}

			$player_ids = implode(', ', $players);

			$data['player_ids'] = $player_ids;
			$data['players'] = $this->player_manager->getSelectedPlayers($player_ids);
			$data['games'] = $this->player_manager->getAllGames();
			$data['tags'] = $this->player_manager->getAllTags();
			$data['level'] = $this->player_manager->getAllPlayerLevels();

			$this->loadTemplate('Player Management', '', '', 'player');
			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/action_page', $data);
			$this->template->render();
		} else {
			$message = lang('con.plm50');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			redirect('player_management/viewAllPlayer');
		}
	}

	public function actionType() {
		$this->form_validation->set_rules('action_type', lang('player.sd08'), 'trim|required|xss_clean');

		if ($this->input->post('action_type') == 'blocked') {
			$this->form_validation->set_rules('period', lang('player.ap06'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('game', lang('player.ap11'), 'required|xss_clean');
		} elseif ($this->input->post('action_type') == 'locked') {
			$this->form_validation->set_rules('period', lang('player.ap06'), 'trim|required|xss_clean');
		} elseif ($this->input->post('action_type') == 'tag') {
			$this->form_validation->set_rules('tags', lang('player.tl07'), 'trim|required|xss_clean');
		} elseif ($this->input->post('action_type') == 'level') {
			$this->form_validation->set_rules('level', lang('lang.level'), 'trim|required|xss_clean');
		}

		if ($this->input->post('period') == 'frozen') {
			$this->form_validation->set_rules('start_date', lang('player.ap09'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('end_date', lang('player.ap10'), 'trim|required|xss_clean');
		}

		if ($this->form_validation->run() == false) {
			$message = lang('con.plm51');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			$this->selectedPlayers();
		} else {
			$action = $this->input->post('action_type');
			$period = $this->input->post('period');
			$today = date("Y-m-d H:i:s");
			$user_id = $this->authentication->getUserId();

			if ($action == 'blocked') {
				// if BLOCKED
				if ($period == 'frozen') {
					$start = $this->input->post('start_date');
					$end = $this->input->post('end_date');

					if ($start >= $end) {
						$message = lang('con.plm52');
						$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
						$this->selectedPlayers();
					} else {
						foreach (explode(', ', $this->input->post('players')) as $player_id) {
							foreach ($this->input->post('game') as $game_id) {
								$game_provider = $this->player_manager->getGameById($game_id);
								$data = array(
									'blockedStart' => $start,
									'blockedEnd' => date('Y-m-d', strtotime($end)) . ' 23:59:59',
									'blocked' => self::GAME_FROZEN,
								);

								$this->player_manager->changePlayerGameBlocked($player_id, $game_id, $data);
								$this->savePlayerUpdateLog($player_id, lang('player.ap03') . ' (' . $game_provider['game'] . ') ' . lang('player.80') . ' ' . $start . ' ' . lang('player.81') . ' ' . $end, $this->authentication->getUsername()); // Add log in playerupdatehistory
							}
						}

						$message = lang('con.plm53');
						$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
						$this->selectedPlayers();
					}
				} else {
					foreach (explode(', ', $this->input->post('players')) as $player_id) {
						foreach ($this->input->post('game') as $game_id) {
							$game_provider = $this->player_manager->getGameById($game_id);
							$data = array(
								'blockedStart' => '0000-00-00',
								'blockedEnd' => '0000-00-00',
							);
							$data['blocked'] = $period == 'always' ? self::GAME_BLOCK : self::GAME_UNBLOCK;
							$this->player_manager->changePlayerGameBlocked($player_id, $game_id, $data);
							$this->savePlayerUpdateLog($player_id, lang('player.ap03') . ' (' . $game_provider['game'] . ') ' . lang('player.ap07'), $this->authentication->getUsername()); // Add log in playerupdatehistory
						}
					}

					$message = $period == 'always	' ? lang('con.plm54') : lang('con.plm70');
					$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
					$this->selectedPlayers();
				}
			} elseif ($action == 'locked') {
				// if LOCKED
				if ($period == 'frozen') {
					$start = $this->input->post('start_date');
					$end = $this->input->post('end_date');

					if ($start >= $end) {
						$message = lang('con.plm52');
						$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
						$this->selectedPlayers();
					} else {
						foreach (explode(', ', $this->input->post('players')) as $player_id) {
							$data = array(
								'lockedStart' => $start,
								'lockedEnd' => date('Y-m-d', strtotime($end)) . ' 0000-00-00 00:00:00',
								'status' => '1',
							);

							$this->player_manager->changePlayerStatus($player_id, $data);
							$this->savePlayerUpdateLog($player_id, lang('player.ap02') . ' ' . lang('player.80') . ' ' . $start . ' ' . lang('player.81') . ' ' . $end, $this->authentication->getUsername()); // Add log in playerupdatehistory
						}

						$message = lang('con.plm55');
						$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
						$this->selectedPlayers();
					}
				} else {
					foreach (explode(', ', $this->input->post('players')) as $player_id) {
						$data = array(
							'lockedStart' => '0000-00-00',
							'lockedEnd' => '0000-00-00',
							'status' => '2',
						);

						$this->player_manager->changePlayerStatus($player_id, $data);
						$this->savePlayerUpdateLog($player_id, lang('player.ap02') . ' ' . lang('player.ap07'), $this->authentication->getUsername()); // Add log in playerupdatehistory
					}

					$message = lang('con.plm56');
					$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
					$this->selectedPlayers();
				}
			} elseif ($action == 'tag') {
				// if TAG
				$tags = $this->input->post('tags');
				foreach (explode(', ', $this->input->post('players')) as $player_id) {
					$check = $this->player_manager->getPlayerTag($player_id);

					if (!$check) {
						$data = array(
							'playerId' => $player_id,
							'taggerId' => $user_id,
							'tagId' => $tags,
							'status' => 1,
							'createdOn' => $today,
							'updatedOn' => $today,
						);

						$this->player_manager->insertPlayerTag($data);
					} else {
						$data = array(
							'tagId' => $tags,
							'updatedOn' => $today,
						);
						$this->player_manager->changeTag($check['playerId'], $data);
					}
				}

				$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Edit Tag for Player', "User " . $this->authentication->getUsername() . " has edited Tag to player");

				$this->savePlayerUpdateLog($player_id, lang('player.48'), $this->authentication->getUsername()); // Add log in playerupdatehistory

				$message = lang('con.plm17');
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
				$this->selectedPlayers();
			} elseif ($action == 'level') {
				//adjust player level
				$this->load->model(array('group_level'));

				$level = $this->input->post('level');
				foreach (explode(', ', $this->input->post('players')) as $player_id) {
					// $data = array(
					// 	'playerGroupId' => $level,
					// );
					// $this->player_manager->changePlayerLevel($player_id, $data);
					$this->group_level->adjustPlayerLevel($player_id, $level);
					$this->savePlayerUpdateLog($player_id, lang('player.46'), $this->authentication->getUsername()); // Add log in playerupdatehistory
				}

				$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Adjust Player Level', "User " . $this->authentication->getUsername() . " adjusted player levels");

				$message = lang('con.plm59');
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
				$this->selectedPlayers();
			}

		}
	}

	/**
	 * overview : message history
	 *
	 * @param int $player_id	player_id
	 */
	public function messageHistory($player_id) {
		$data['chat_history'] = $this->cs_manager->getPlayerMessageHistory($player_id);

		$this->load->view('player_management/ajax_ui_chat', $data);
	}

	/**
	 * overview : payment history
	 *
	 * @param $player_id
	 */
	public function paymentHistory($player_id) {
		$data['payment_history'] = $this->payment_manager->getPaymentHistoryByPlayer($player_id, null, null);

		$this->load->view('player_management/ajax_ui_payment', $data);
	}

	/**
	 * overview : cashback history
	 *
	 * @param $player_id
	 */
	public function cashbackHistory($player_id) {
		$data['cashback_history'] = $this->player_manager->getCashbackHistory($player_id, null, null);

		$this->load->view('player_management/ajax_ui_cashback', $data);
	}

	/**
	 * overview : balance adjustment history
	 *
	 * @param $player_id
	 */
	public function balanceAdjustmentHistory($player_id) {
		$data['balance_adjustment_history'] = $this->player_manager->getBalanceAdjustment($player_id, null, null);

		$this->load->view('player_management/ajax_ui_adjust_balance', $data);
	}

	/**
	 * overview : save log on player update history
	 *
	 * @param int $player_id	player_id
	 * @param $changes
	 * @param $updatedBy
	 */
	public function savePlayerUpdateLog($player_id, $changes, $updatedBy, $today = null) {

		$data = array(
			'playerId' => $player_id,
			'changes' => $changes,
			'createdOn' => empty($today) ? date('Y-m-d H:i:s') : $today,
			'operator' => $updatedBy,
		);
		$this->player_manager->addPlayerInfoUpdates($player_id, $data);
	}

	/**
	 * overview : clear player bonus
	 *
	 * @param int $playerDepositPromoId	promo_id
	 */
	public function clearPlayerBonus($playerDepositPromoId) {
		if ($playerDepositPromoId != 0) {
			$promoData = array('status' => 2, //cleared
			);

			$this->payment_manager->clearPlayerDepositBonus($playerDepositPromoId, $promoData);

			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Forget Bonus', "Player " . $this->authentication->getUsername() . " has forget bonus for player");
			$message = lang('con.plm57');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		}
	}

	/**
	 * overview : change the sesson for the hideable sidebar
	 *
	 * @param $status
	 */
	public function changeSidebarStatus($status) {
		if ($status == 'active' || $status == '') {
			$this->session->set_userdata(array('sidebar_status' => 'active'));
		} else {
			$this->session->set_userdata(array('sidebar_status' => 'inactive'));
		}
		// echo $this->session->userdata('sidebar_status');
		$this->returnText($this->session->userdata('sidebar_status'));
	}

	/**
	 * overview : checks fields modified on player bank info
	 *
	 * @param 	array $origbank
	 * @param 	array $data
	 * @return	string
	 */
	public function checkBankChanges($origbank, $data) {
		$array = null;

		$array .= $origbank['bankTypeId'] != $data['bankTypeId'] ? '[' . lang('adjustmenthistory.title.beforeadjustment') . '] ' . lang('player.ui35') . ':' . $origbank['bankTypeId'] . ' [' . lang('adjustmenthistory.title.afteradjustment') . '] ' . lang('player.ui35') . ':' . $data['bankTypeId'] . ', ' : '';
		$array .= $origbank['bankAccountNumber'] != $data['bankAccountNumber'] ? '[' . lang('adjustmenthistory.title.beforeadjustment') . '] ' . lang('cashier.69') . ':' . $origbank['bankAccountNumber'] . ' [' . lang('adjustmenthistory.title.afteradjustment') . '] ' . lang('cashier.69') . ':' . $data['bankAccountNumber'] . ', ' : '';
		$array .= $origbank['bankAccountFullName'] != $data['bankAccountFullName'] ? '[' . lang('adjustmenthistory.title.beforeadjustment') . '] ' . lang('cashier.68') . ':' . $origbank['bankAccountFullName'] . ' [' . lang('adjustmenthistory.title.afteradjustment') . '] ' . lang('cashier.68') . ':' . $data['bankAccountFullName'] . ', ' : '';
		$array .= $origbank['province'] != $data['province'] ? '[' . lang('adjustmenthistory.title.beforeadjustment') . '] ' . lang('cashier.70') . ':' . $origbank['province'] . ' [' . lang('adjustmenthistory.title.afteradjustment') . '] ' . lang('cashier.70') . ':' . $data['province'] . ', ' : '';
		$array .= $origbank['city'] != $data['city'] ? '[' . lang('adjustmenthistory.title.beforeadjustment') . '] ' . lang('cashier.71') . ':' . $origbank['city'] . ' [' . lang('adjustmenthistory.title.afteradjustment') . '] ' . lang('cashier.71') . ':' . $data['city'] . ', ' : '';
		$array .= $origbank['branch'] != $data['branch'] ? '[' . lang('adjustmenthistory.title.beforeadjustment') . '] ' . lang('cashier.72') . ':' . $origbank['branch'] . ' [' . lang('adjustmenthistory.title.afteradjustment') . '] ' . lang('cashier.72') . ':' . $data['branch'] . ', ' : '';
		//$array .= $origbank['bank_address'] != $data['bank_address'] ? lang('cashier.ui38') . $data['bank_address'] . ', ' : '';

		return $modifiedField = empty($array) ? '' : substr($array, 0, -2);
	}

	/**
	 * overview : view page of Online Player List
	 *
	 * @return	rendered Template
	 */
	public function viewOnlinePlayerList() {
		if ($this->permissions->checkPermissions('online_player_list')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Player Management', '', '', 'player');

			$data['games'] = $this->player_manager->getAllGames();

			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/online_player/view_player_list', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : change online list
	 *
	 * @param $type
	 * @return	rendered Template
	 */
	public function changeOnlineList($type) {
		if ($type == 0) {
			$data['online_players'] = $this->player_manager->getOnlinePlayers();
			$data['type'] = $type;
		} elseif ($type == 1) {
			$data['online_players'] = array();
			$data['type'] = $type;
		} elseif ($type == 2) {
			$data['online_players'] = array();
			$data['type'] = $type;
		}

		$this->load->view('player_management/online_player/ajax_player_list', $data);
	}

	/**
	 * overview : player bet limit
	 *
	 * @param int $game_platform_id		game_platform_id
	 * @param int $player_id			player_id
	 * @param null $game_id				game_id
	 */
	public function playerBetLimit($game_platform_id, $player_id, $game_id = NULL) {

		# TODO: WHERE TO GET GAME_ID

		$this->load->model('external_system');

		if (!$this->external_system->isGameApiActive($game_platform_id)) {
			die(lang('goto_game.sysMaintenance'));
		}

		$player = $this->player_model->getPlayerById($player_id);
		$api = $this->utils->loadExternalSystemLibObject($game_platform_id);

		$result = $api->getBetLimit($player->username, $game_id);

		$platform_name = $this->external_system->getNameById($game_platform_id);

		$data = array(
			'game_platform_id' => $game_platform_id,
			'player_id' => $player_id,
			'platform_name' => $platform_name,
		);
		$this->loadTemplate($platform_name . ' ' . lang('Bet Limits'), '', '', 'player');
		$this->template->write_view('sidebar', 'player_management/sidebar');
		$this->template->write_view('main_content', 'player_management/player_bet_limit/' . $game_platform_id, $data);
		$this->template->render();
	}

	/**
	 * overview : update player bet limit
	 *
	 * @param int $game_platform_id		game_platform_id
	 * @param int $player_id			player_id
	 */
	public function updatePlayerBetLimit($game_platform_id, $player_id) {
		$player = $this->player_model->getPlayerById($player_id);
		$api = $this->utils->loadExternalSystemLibObject($game_platform_id);
		$params = $this->input->post();
		$params = array_filter($params);
		$result = $api->updateBetLimit($player->username, $params);
		if ($result['success']) {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Player bet limit has been updated successfully!'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sorry! Player bet limit update has failed.'));
		}
		redirect('/player_management/playerBetLimit/' . $game_platform_id . '/' . $player_id);
	}

	/**
	 * overview : check if bank account number exist
	 *
	 * @param $bankAccountId	bank_acount_id
	 */
	public function isPlayerBankAccountNumberExists($bankAccountId, $bankTypeId, $bankType) {
		$this->load->model('player');
		$this->returnJsonResult($this->player->isPlayerBankAccountNumberExists(@$bankAccountId, @$bankTypeId, @$bankType));
	}

	/**
	 * overview : check if player is onlne
	 *
	 * @param int $gameId		game_id
	 * @param string $username	username
	 * @param int $playerId		player_id
	 */
	public function is_online($gameId, $username, $playerId) {
		$this->output->set_content_type('text/html');
		if ($this->player_manager->checkIfOnline($username, $gameId) == 1) {
			$this->output->set_output(lang('lang.yes'));
			$this->output->append_output('<a href="' . site_url('player_management/kickPlayer/') . $username . '/' . $gameId . '/' . $playerId . '" class="btn btn-xs btn-primary pull-right">' . lang('player.ol03') . '</a>');
		} else {
			$this->output->set_output(lang('lang.no'));
		}
	}

	/**
	 * overview : reset pt
	 * @param int $player_id	player_id
	 */
	public function resetpt($player_id) {
		//load pt api
		// $this->load->model('player_model');
		// $this->player_model->updatePlayer($playerId, array(
		// 	'blocked' => 0,
		// ));
		$success = true;
		$api = $this->utils->loadExternalSystemLibObject(PT_API);
		if ($api) {
			$username = $this->player_model->getUsernameById($player_id);
			$password = $this->player_model->getPasswordByUsername($username);

			# OG-1401 Run resetPlayer, unblockPlayer, syncPassword
			if ($success) {
				$rlt = $api->resetPlayer($username);
				$success = $success && $rlt['success'];
			}

			if ($success) {
				$rlt = $api->unblockPlayer($username);
				$success = $success && $rlt['success'];
			}

			if ($success) {
				$rlt = $api->changePassword($username, $password, $password);
				$success = $success && $rlt['success'];
			}

			if ($success) {
				$rlt = $api->logout($username);
				$success = $success && $rlt['success'];
			}
		}

		if ($success) {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('report.log06'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('report.log07'));
		}

		redirect('/player_management/userInformation/' . $player_id);
	}

	public function refreshPlayerBalance($player_id) {
		$this->load->model(array('wallet_model', 'daily_balance', 'game_logs', 'game_provider_auth'));

		$player_wallet = $this->wallet_model->getBigWalletByPlayerId($player_id);

		$success = true;
		$controller = $this;
		if (!empty($player_wallet)) {
			if( isset($player_wallet['sub']) ) {
				$sub_wallets = $player_wallet['sub'];
				foreach($sub_wallets as $system_id => $val) {
					$balance_in_db = $val['total_nofrozen'];
					if ($balance_in_db > 0) {
						$this->lockAndTrans(Utils::LOCK_ACTION_BALANCE, $player_id, function () use ($controller, $player_id, $system_id, $balance_in_db) {
							$api = $this->utils->loadExternalSystemLibObject($system_id);
							$player_name = $this->player_model->getUsernameById($player_id);

							$result = $api->queryPlayerBalance($player_name);
							$success = false;
							if (isset($result['success']) && $result['success'] && isset($result['balance'])) {
								$balance_in_game = $result['balance'];

								if( $balance_in_game != $balance_in_db ) {
									$success = $api->updatePlayerSubwalletBalanceWithoutLock($player_id, $balance_in_game);
								}
							}
							return $success;
						});
					}
				}
			}
		}

		$result['message']  = $success ? lang('report.log06') : lang('report.log07');
		$result['status'] = $success;
		$this->returnJsonResult($result);
	}

	/**
	 * overview : get player id
	 *
	 * @param int $player_name	player_name
	 */
	public function getPlayerId($player_name) {
		$this->load->model('player');
		$this->returnText($this->player->getPlayerIdByUsername($player_name));
	}

	/**
	 * overview : revert broken game
	 *
	 * @param int $player_id			player_id
	 * @param int $game_platform_id		game_platform_id
	 */
	public function revertBrokenGame($player_id) {
		$username = $this->player_model->getUsernameById($player_id);
		$api = $this->utils->loadExternalSystemLibObject(PT_API);
		$api->revertBrokenGame($username);
		redirect('/player_management/userInformation/' . $player_id);
	}

	/**
	 * overview : list of player notes
	 *
	 * @param int $player_id	player id
	 */
	public function player_notes($player_id) {
		$this->load->model(['player_model']);
		$user_id = $this->authentication->getUserId();
		$data = array(
			'user_id' => $user_id,
			'player_id' => $player_id,
		);
		$data['notes'] = $this->player_model->getPlayerNotes($player_id);
		$this->load->view('player_management/player_notes', $data);
	}

	/**
	 * overview : add player notes
	 *
	 * @param int $player_id	player id
	 */
	public function add_player_notes($player_id) {

		$result['success'] = false;

		$this->load->model(['player_model']);
		$user_id = $this->authentication->getUserId();
		$notes = $this->input->post('notes');

		if ($notes) {

			$result['success'] = $this->player_model->addPlayerNote($player_id, $user_id, $notes);

			if ($result['success']) {
				$username=$this->player_model->getUsernameById($player_id);
				//sync
				$this->syncPlayerCurrentToMDBWithLock($player_id, $username, false);

				$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Add Note for Player', "User " . $this->authentication->getUsername() . " has added new note to player");
			}

		}

		$this->returnJsonResult($result);
	}

	/**
	 * overview : edit player notes
	 * @param int $note_id	note id
	 * @param int $player_id	player id
	 */
	public function edit_player_notes($note_id, $player_id) {

		$result['success'] = false;

		$this->load->model(['player_model']);
		$user_id = $this->authentication->getUserId();
		$new_notes = $this->input->post('new_notes');

		if ($new_notes) {

			$result['success'] = $this->player_model->editPlayerNote($note_id, $new_notes);

			if ($result['success']) {
				$username=$this->player_model->getUsernameById($player_id);
				//sync
				$this->syncPlayerCurrentToMDBWithLock($player_id, $username, false);

				$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Edit Note for Player', "User " . $this->authentication->getUsername() . " has edited note to player " . $username);
			}

		}

		$this->returnJsonResult($result);
	}

	/**
	 * overview : remove player note
	 *
	 * @param int $note_id	$note_id id
	 * @param int $player_id id
	 */
	public function remove_player_note($note_id, $player_id) {

		$result['success'] = false;

		$note = $this->player_manager->getNoteById($note_id);
		$user_id = $this->authentication->getUserId();

		if ($user_id == $note['userId']) {

			$result['success'] = $this->player_model->deleteNote($note_id);

			if ($result['success']) {
				$username=$this->player_model->getUsernameById($player_id);
				//sync
				$this->syncPlayerCurrentToMDBWithLock($player_id, $username, false);

				$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Delete Note for Player', "User " . $this->authentication->getUsername() . " has deleted note to player");
			}

		}

		$this->returnJsonResult($result);
	}

	/**
	 * Batch tag player, featured in player information/player's logs/duplicate account tab
	 * @uses	numeric string	POST.tagId				ID of the tag
	 * @uses	string			POST.playerIDs			playerID concated by commas
	 * @uses	numeric string	POST.subject_player_id	playerID of the subject player
	 *
	 * @return	JSON	[ status, result ]
	 */
	public function tagPlayerByBatch() {
		$retval = [ 'success' => false, 'message' => 'execution_incomplete', 'result' => null ];

		$tagID = $this->input->post('tagId');
		$playerIDs = $this->input->post('playerIDs');
		$playerIDs = explode(',', $playerIDs);

		// OGP-3380: Also include subject player
		$subject_player_id = $this->input->post('subject_player_id');
		$playerIDs[] = $subject_player_id;

		$today = date("Y-m-d H:i:s");
		$user_id = $this->authentication->getUserId();

		try {

			if (empty($playerIDs)) {
				throw new Exception('playerIDs empty');
			}

			$bat_results = [];
			$players = [];
			foreach ($playerIDs as $id) {
				$this->utils->debug_log('------------------------------------------',$id);
				$tagged = $this->player_model->checkIfPlayerIsTagged($id, $tagID);
				$bat_result = [ 'op' => null, 'stat' => null, 'mesg' => null ];

				if ($tagID <= 0) {
					// Remove all tags
					$delres = $this->__deletePlayerTag($id, $tagged);
					$bat_result = [
						'op'	=> 'delete' ,
						'stat' 	=> $delres === true ? 'success' : 'error' ,
						'mesg'	=> $delres === true ? null : $delres
					];
				} else {
					// OGP-3380: allow multiple tags (no updating)
					if ($tagged) {
						$bat_result = [ 'op' => 'skipped' , 'stat' => 'success' , 'mesg' => 'already has this tag' ];
						// $bat_result = [ 'op' => 'insert' , 'stat' => 'success' , 'mesg' => null ];
					}
					else {
						$insres = $this->__insertNewTags($id, $user_id, $tagID, $today);
						$bat_result = [
							'op'	=> 'insert' ,
							'stat' 	=> $insres === true ? 'success' : 'error' ,
							'mesg'	=> $insres === true ? null : $insres
						];
					}
				}
				$player_username = $this->player_model->getUsernameById($id);

				$bat_results[$id] = [ 'player' => $player_username , 'result' => $bat_result ];
			}

			// Experiment use only, always commen it out
			// throw new Exception('Test exception');

			$result = [ 'batch_res' => $bat_results ];
			$retval = [ 'success' => true, 'message' => null, 'result' => $result ];
		}
		catch (Exception $e) {
			$retval = [ 'success' => false, 'message' => $e->getMessage(), 'result' => null ];
		}
		finally {
			$this->returnJsonResult($retval);
		}
	}

	/**
	 * overview : update tags when user is not yet tag
	 *
	 * @param string $player_id		player_id
	 * @param string $tagID			tag_id
	 * @param string $today			today
	 * @param string $user_id		user_id
	 * @param string $tagged		tagged
	 * @return bool|string
	 */
	function __updateTags($player_id = '', $tagID = '', $today = '', $user_id = '', $tagged = '') {
		$this->load->model('player');

		$data = array(
			'taggerId' => $user_id,
			'tagId' => $tagID,
			'updatedOn' => $today,
			'status' => 1,

		);

		$newTag = $this->player->updatePlayerTag($data, $player_id);

		try {

			if (!$newTag) {
				throw new Exception(false);
			}

			$this->savePlayerUpdateLog($player_id, lang('player.26') . ' - ' .
				lang('adjustmenthistory.title.beforeadjustment') . ' (' . $tagged . ') ' .
				lang('adjustmenthistory.title.afteradjustment') . ' (' . $newTag . ') ', $this->authentication->getUsername()); // Add log in playerupdatehistory

			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('adjustmenthistory.title.beforeadjustment') . ' (' . $tagged . ') ' .
				lang('adjustmenthistory.title.afteradjustment') . ' (' . $newTag . ') ', "User " . $this->authentication->getUsername() . " has adjusted player '" . $player_id . "'");

			return true;

		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * overview : add new tags
	 *
	 * @param string $player_id		player_id
	 * @param string $user_id		user_id
	 * @param string $tagId			tag_id
	 * @param string $today			today
	 * @return bool|string
	 */
	function __insertNewTags($player_id = '', $user_id = '', $tagId = '', $today = '') {
		$this->load->model('player');

		$data = array(
			'playerId' => $player_id,
			'taggerId' => $user_id,
			'tagId' => $tagId,
			'createdOn' => $today,
			'updatedOn' => $today,
			'status' => 1,
		);

		$newTag = $this->player->insertAndGetPlayerTag($data);

		try {

			if (!$newTag) {
				throw new Exception(false);
			}

			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('adjustmenthistory.title.beforeadjustment') . ' (' . lang('player.tp03') . ') ' .
				lang('adjustmenthistory.title.afteradjustment') . ' (' . $newTag . ') ', "User " . $this->authentication->getUsername() . " has adjusted player '" . $player_id . "'");

			$this->savePlayerUpdateLog($player_id, lang('player.26') . ' - ' .
				lang('adjustmenthistory.title.beforeadjustment') . ' (' . lang('player.tp03') . ') ' .
				lang('adjustmenthistory.title.afteradjustment') . ' (' . $newTag . ') ', $this->authentication->getUsername()); // Add log in playerupdatehistory

			return true;
		} catch (Exception $e) {
            return $e->getMessage();
		}
	}

	/**
	 * overview : delete player tagged
	 *
	 * @param string $player_id		player_id
	 * @param string $tagged		tagged
	 * @return bool|string
	 */
	protected function __deletePlayerTag($player_id = '', $tagged = '') {
		$newTag = $this->player->deleteAndGetPlayerTag($player_id);

		try {

			if (!$newTag) {
				throw new Exception(false);
			}

			$this->savePlayerUpdateLog($player_id, lang('player.26') . ' - ' .
				lang('adjustmenthistory.title.beforeadjustment') . ' (' . $tagged . ') ' .
				lang('adjustmenthistory.title.afteradjustment') . ' (' . lang('player.tp10') . ') ', $this->authentication->getUsername()); // Add log in playerupdatehistory

			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, lang('adjustmenthistory.title.beforeadjustment') . ' (' . $tagged . ') ' .
				lang('adjustmenthistory.title.afteradjustment') . ' (' . lang('player.tp10') . ') ', "User " . $this->authentication->getUsername() . " has adjusted player '" . $player_id . "'");

			return true;

		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * overview : set cashback status
	 *
	 * @param int $playerId	player_id
	 * @param int $status
	 */
	public function set_cashback_status($playerId, $status) {
		if (!$this->permissions->checkPermissions('disable_cashback') || empty($playerId)) {
			return $this->error_access();
		}

		$this->load->model(['player_model']);
		$updateType = "Disable Cashback";

		if ($status == 'enable') {
			$this->player_model->enableCashbackByPlayerId($playerId);
			$updateType = lang('Enable Cashback');
		} else {
			$this->player_model->disableCashbackByPlayerId($playerId);
		}

		$this->savePlayerUpdateLog($playerId, $updateType, $this->authentication->getUsername());
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Set cashback status successfully'));

		redirect('player_management/userInformation/' . $playerId);

	}

	/**
	 * overview : set promotion status
	 *
	 * @param int $playerId	player_id
	 * @param int $status
	 */
	public function set_promotion_status($playerId, $status) {
		if (!$this->permissions->checkPermissions('disable_promotion') || empty($playerId)) {
			return $this->error_access();
		}

		$player = $this->player_model->getPlayerUsername($playerId);

		$this->load->model(['player_model']);
		$updateType = lang("Disable Promotion");
		if ($status == 'enable') {
			$this->player_model->enablePromotionByPlayerId($playerId);
			$updateType = lang('Enable Promotion');
		} else {
			$this->player_model->disablePromotionByPlayerId($playerId);
		}

		$this->savePlayerUpdateLog($playerId, $updateType, $this->authentication->getUsername());
		$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Promotion Status',
				"User " . "<b>".$this->authentication->getUsername()."</b> " . $status." promotion of player " . $player['username']);

		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Set promotion status successfully'));
		redirect('player_management/userInformation/' . $playerId);
	}

	/**
	 * overview : player attached image overview
	 *
	 * detail : load player attached image
	 *
	 * @param int $player_id
	 */
	public function player_attach_document($player_id) {
		if (!$this->permissions->checkPermissions('kyc_attached_documents')) {
			$data['permission_verified'] = lang('con.plm01');
			$this->load->view('player_management/player_attach_document', $data);
		} else {
			$this->load->model(array('player_attached_proof_file_model','player_kyc','player_model'));
			$data['playerId'] = $player_id;
            $playerDetails = $this->player_model->getPlayerDetails($player_id);
            $data['id_card_number'] = (isset($playerDetails[0]) && !empty($playerDetails[0]['id_card_number'])) ? $playerDetails[0]['id_card_number'] : '';
			$data['current_kyc_status'] = $this->getPlayerCurrentStatus($player_id);
			$data['current_kyc_level'] = $this->player_kyc->getPlayerCurrentKycLevel($player_id);
			$data['allowed_withdrawal_status'] = ($this->generate_allowed_withdrawal_status($player_id)) ? lang('Yes') : lang('No');
			$data['attachment_info'] = $this->player_attached_proof_file_model->getPlayerAttachmentInfoList($player_id);
            $data['limit_of_upload_attachment'] = $this->utils->getConfig('kyc_limit_of_upload_attachment');
			$this->load->view('player_management/player_attach_document', $data);
		}
	}

	public function friend_referrial_commision_manual_adjustment($friend_referrial_monthly_id, $total_commission) {
		//$data['affiliateId'] = $affiliateId;
		$data['friend_referrial_monthly_id'] = $friend_referrial_monthly_id;
		$data['total_commission'] = (float) $total_commission;
		$this->load->view('player_management/earning/player_friend_referrial_commision_manual_adjustment', $data);
	}

	public function updateTotalOfFriendReferrialMonthlyCommission($friend_referrial_monthly_id) {
		$this->load->model('player_earning');
		$amount = $this->input->post('total_amount');
		$this->player_earnings->updateTotalOfFriendReferrialMonthlyCommission($friend_referrial_monthly_id, $amount);
		redirect('player_management/viewFriendReferralMonthlyEarnings', 'refresh');
	}

	public function transfer_all($yearmonth = null) {
		$this->load->model(array('player_earning'));
		//$min_amount = $this->affiliate_earnings->getMinimumMonthlyPayAmountSetting();

		$succ = $this->player_earning->transferAllEarningsToWallet($yearmonth);

		if ($succ) {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Transfer successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Transfer failed'));
		}

		redirect('player_management/viewFriendReferralMonthlyEarnings');
	}

	public function transfer_one($earningid) {
		$this->load->model(array('player_earning'));

		$succ = $this->player_earning->transferToMainWalletById($earningid);

		if ($succ) {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Transfer successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Transfer failed'));
		}

		redirect($this->agent->referrer());
	}

	public function transfer_selected() {
		$earningids = $this->input->post('earningids');
		$this->load->model(array('player_earning'));
		foreach ($earningids as $earningidi) {
			$succ = $this->player_earning->transferToMainWalletById($earningidi);
			if ($succ) {
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Transfer successfully'));
			} else {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Transfer failed'));
			}
		}

		return $this->agent->referrer();
	}

	/**
	 * view page for vip player commission setup
	 * add by spencer.kuo 2017.05.10
	 * start
	 */
	public function FriendReferralLevelSetup() {
		$this->load->model(array('friend_referral_level'));
		$data['data'] = $this->friend_referral_level->getAllFriendReferralLevel();
		for ($i = 0; $i < count($data['data']); $i++) {
			$selected_game_tree = json_decode($data['data'][$i]['selected_game_tree'], true);
			$selectedGamePlatformArr = array();
			$selectedGameTypeInfoArr = array();
			foreach ($selected_game_tree as $game_id => $percent) {
				if (substr($game_id, 0, 3) == 'gp_') {
					$selectedGamePlatformArr[substr($game_id, 3, strlen($game_id) - 3)] = $percent;
				} else {
					$selectedGameTypeInfoArr[$game_id] = $percent;
				}
			}
			$result = $this->getFriendReferralLevelTree($selectedGamePlatformArr, $selectedGameTypeInfoArr, true);
			$data['data'][$i]['selected_game_tree'] = json_encode($result);
		}
		$this->loadTemplate('VIP Setting Management', '', '', 'player');
		$this->template->write_view('sidebar', 'player_management/sidebar');
		$this->template->write_view('main_content', 'player_management/earning/view_friendreferrallevelsetup', $data);
		$this->template->render();
	}

	public function getFriendReferralLevelTree($selectedGamePlatformArr = null, $selectedGameTypeInfoArr = null, $percentage = true) {
		$this->load->model(array('friend_referral_level', 'game_type_model'));
		if (empty($selectedGamePlatformArr)) {
			$selectedGamePlatformArr = array();
		}

		if (empty($selectedGameTypeInfoArr)) {
			$selectedGameTypeInfoArr = array();
		}

		$gameApiList = $this->external_system->getAllActiveSytemGameApi();
		if (!empty($gameApiList)) {
			foreach ($gameApiList as $row) {
				$number = null;
				if (isset($selectedGamePlatformArr[$row['id']])) {
					$number = $selectedGamePlatformArr[$row['id']];
				}
				if (empty($number)) {
					$number = null;
				}
				$gameApiNode = array('id' => 'gp_' . $row['id'], 'text' => $row['system_code'],
					'state' => ["checked" => array_key_exists($row['id'], $selectedGamePlatformArr), "opened" => false],
					'set_number' => true, 'number' => $number, 'percentage' => $percentage);
				//load game type
				$gameTypeList = $this->game_type_model->getGameTypeListByGamePlatformId($row['id']);
				if (!empty($gameTypeList)) {
					foreach ($gameTypeList as $gameType) {
						$number = null;
						if (isset($selectedGameTypeInfoArr[$gameType['id']])) {
							$number = $selectedGameTypeInfoArr[$gameType['id']];
						}
						if (empty($number)) {
							$number = null;
						}
						$gameTypeNode = array('id' => $gameType['id'], 'text' => lang($gameType['game_type_lang']),
							'state' => ["checked" => array_key_exists($gameType['id'], $selectedGameTypeInfoArr), "opened" => false],
							'set_number' => true, 'number' => $number, 'percentage' => $percentage);
						$gameApiNode['children'][] = $gameTypeNode;
					}
				}
				$result[] = $gameApiNode;
			}
		}
		return $result;
	}

	public function addEditFriendReferralLevel($id = null) {
		$this->load->model(array('friend_referral_level'));
		$data['setting'] = array();
		if (!empty($id)) {
			$data['setting'] = $this->friend_referral_level->getFriendReferralLevelById($id);
			$selected_game_tree = json_decode($data['setting']['selected_game_tree'], true);
			$selectedGamePlatformArr = array();
			$selectedGameTypeInfoArr = array();
			foreach ($selected_game_tree as $game_id => $percent) {
				if (substr($game_id, 0, 3) == 'gp_') {
					$selectedGamePlatformArr[substr($game_id, 3, strlen($game_id) - 3)] = $percent;
				} else {
					$selectedGameTypeInfoArr[$game_id] = $percent;
				}
			}
			$result = $this->getFriendReferralLevelTree($selectedGamePlatformArr, $selectedGameTypeInfoArr, true);
			$data['selected_game_tree'] = json_encode($result);
		} else {
			$selectedGamePlatformArr = array();
			$selectedGameTypeInfoArr = array();
			$result = $this->getFriendReferralLevelTree($selectedGamePlatformArr, $selectedGameTypeInfoArr, true);
			$data['selected_game_tree'] = json_encode($result);
		}
		$this->loadTemplate('VIP Setting Management', '', '', 'player');
		$this->addBoxDialogToTemplate();

		$this->addJsTreeToTemplate();
		$this->template->write_view('sidebar', 'player_management/sidebar');
		$this->template->write_view('main_content', 'player_management/earning/view_addeditfriendreferrallevel', $data);
		$this->template->render();
	}

	public function saveFriendReferralLevel() {
		$this->load->model(array('friend_referral_level'));
		$post = $this->input->post();
		$selected_game = explode(',', $post['selected_game_tree']);
		$setting = Array();
		foreach ($selected_game as $game_id) {
			if (!empty($post['per_' . $game_id])) {
				$setting[$game_id] = $post['per_' . $game_id];
			}

			unset($post['per_' . $game_id]);
		}
		$post['selected_game_tree'] = json_encode($setting);
		$id = $post['id'];
		unset($post['id']);
		unset($post['submit']);

		if (empty($id)) {
			$this->friend_referral_level->addFriendReferralLevel($post);
		} else {
			$this->friend_referral_level->editFriendReferralLevel($post, $id);
		}

		redirect('player_management/FriendReferralLevelSetup');
	}

	/**
	 * overview : set withdrawal status
	 *
	 * @param int $playerId	player_id
	 * @param int $status
	 */
	public function set_withdrawal_status($playerId, $status) {
		if (!$this->permissions->checkPermissions('disable_player_withdrawal') || empty($playerId)) {
			return $this->error_access();
		}
		$player = $this->player_model->getPlayerUsername($playerId);

		$this->load->model(['player_model']);
		$updateType = lang("Disable Player Withdrawal");
		if ($status == 'enable') {
			$this->player_model->enableWithdrawalByPlayerId($playerId);
			$updateType = lang("Enable Player Withdrawal");
		} else {
			$this->player_model->disableWithdrawalByPlayerId($playerId);
		}

		$this->savePlayerUpdateLog($playerId, $updateType, $this->authentication->getUsername());
		$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Withdrawal Status',
				"User " . "<b>".$this->authentication->getUsername()."</b> " . $status." withdrawal of player " . $player['username']);

		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Set withdrawal status successfully'));
		redirect('player_management/userInformation/' . $playerId);
	}

    /**
     * overview : save_savestate_column
     *
     */
    public function save_savestate_column() {

        $this->load->model(['player_model']);

        $data['saveStateName'] = $this->input->post('saveStateName');
        $data['column'] = $this->input->post('column');

        //Get saveStateColumn
        $saveStateColumn = $this->utils->getUniversalSaveState($data['saveStateName']);

        if($saveStateColumn['columnhidenumber'] != ''){
            $data['column'] = $saveStateColumn['columnhidenumber'].','.$data['column'];
        }

        if ($saveStateColumn['isdatatableExist']){
            $result = $this->player_model->save_savestate_column($data);
        }else{
            $result = $this->player_model->insert_savestate_column($data);
        }

        return $this->returnJsonResult($data);
    }

    /**
     * overview : delete_savestate_column
     *
     */
    public function delete_savestate_column() {

        $this->load->model(['player_model']);

        $data['saveStateName'] = $this->input->post('saveStateName');
        $data['column'] = $this->input->post('column');

        //Get saveStateColumn
        $saveStateColumn = $this->utils->getUniversalSaveState($data['saveStateName']);
        if($saveStateColumn['columnhidenumber']){
            $arrColumn = explode(',',$saveStateColumn['columnhidenumber']);
            $Arraydifference = array_diff($arrColumn, array($data['column']));
            if ($Arraydifference == 0){
                $data['column'] = '';
            }else{
                $data['column'] = implode(',',$Arraydifference);
            }

        }
        $result = $this->player_model->save_savestate_column($data);

        return $this->returnJsonResult($data);
    }

	public function ManualSubtractBalanceTagManagement() {
		if (!$this->permissions->checkPermissions('manual_subtract_balance_tag')) {
			$this->error_access();
		} else {
			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}
			$this->loadTemplate('Player Management', '', '', 'player');
			$data['tags'] = $this->utils->stripHtmlTagsOfArray($this->player_manager->getAllManualSubtractBalanceTags());
			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/view_manual_subtract_balance_tag_list', $data);
			$this->template->render();
		}
	}

	public function getManualSubtractBalanceTagDetails($id) {
		//echo json_encode($this->player_manager->getManualSubtractBalanceTagDetails($id));
		$this->returnJsonResult($this->player_manager->getManualSubtractBalanceTagDetails($id));
	}

	public function postManualSubtractBalanceTag() {
		$this->form_validation->set_rules('adjust_tag_name', 'Tag Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('adjust_tag_description', 'Tag Description', 'trim|required|xss_clean');

		if ($this->form_validation->run() == false) {
			$message = lang('con.plm44');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		} else {
			$isTagExist = $this->player_manager->getManualSubtractBalanceTagByName($this->input->post('adjust_tag_name'));
			if ($isTagExist && !$this->input->post('id')) {
				$message = lang('con.plm16');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			} else {
				$adjust_tag_name = $this->utils->stripHTMLtags($this->input->post('adjust_tag_name'));
				$adjust_tag_description = $this->utils->stripHTMLtags($this->input->post('adjust_tag_description'));
				$id = $this->input->post('id');
				$today = date("Y-m-d H:i:s");

				if ($id) {
					$data = array(
						'adjust_tag_name' => ucfirst($adjust_tag_name),
						'adjust_tag_description' => $adjust_tag_description,
						'updated_at' => $today,
						'status' => 0,
					);

					$this->player_manager->editManualSubtractBalanceTag($data, $id);
					$message = lang('con.plm45') . " <b>" . $adjust_tag_name . "</b> " . lang('con.plm46');
					$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Edit Manual Subtract Balance Tag', "User " . $this->authentication->getUsername() . " has successfully edited " . ucfirst($adjust_tag_name) . ".");
				} else {
					$data = array(
						'adjust_tag_name' => ucfirst($adjust_tag_name),
						'adjust_tag_description' => $adjust_tag_description,
						'createBy' => $this->session->userdata('user_id'),
						'created_at' => $today,
						'updated_at' => $today,
						'status' => 0,
					);

					$this->player_manager->insertManualSubtractBalanceTag($data);
					$message = lang('con.plm45') . " <b>" . $adjust_tag_name . "</b> " . lang('successfully added tag');
					$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Edit Manual Subtract Balance Tag', "User " . $this->authentication->getUsername() . " has successfully added " . ucfirst($adjust_tag_name) . ".");
				}

				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			}
		}

		redirect('player_management/ManualSubtractBalanceTagManagement');
	}

	public function deleteSelectedManualSubtractBalanceTag() {
		$tag = $this->input->post('tag');
		$today = date("Y-m-d H:i:s");
		$tags = '';

		if ($tag != '') {
			foreach ($tag as $id) {
				$this->player_manager->deleteTransactionsTag($id);
				$this->player_manager->deleteManualSubtractBalanceTag($id);
			}

			$message = lang('con.plm48');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message); //will set and send message to the user
			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Delete Manual Subtract Balance Tags', "User " . $this->authentication->getUsername() . " has successfully deleted tags.");
			redirect('player_management/ManualSubtractBalanceTagManagement');
		} else {
			$message = lang('con.plm49');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			redirect('player_management/ManualSubtractBalanceTagManagement');
		}
	}

	public function deleteManualSubtractBalanceTag($id) {
		$this->player_manager->deleteTransactionsTag($id);
		$this->player_manager->deleteManualSubtractBalanceTag($id);

		$message = lang('con.plm47');
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Delete Manual Subtract Balance Tag', "User " . $this->authentication->getUsername() . " has successfully deleted tag.");
		redirect('player_management/ManualSubtractBalanceTagManagement');
	}

    /**
     * KYC IMAGE UPLOAD
     *
     * kyc的狀態 是在kyc_status.php修改的
     * 但是kyc的 player picture,是在playerdetails.proof_filename這個欄位內的json中記錄
     * 但這個json內的status,應該是不被使用的
     */
	public function uploadKYCPlayerImage($playerId){
		if (!$this->permissions->checkPermissions('update_attached_documents')) {
			$this->error_access();
		} else {
			$this->load->model(array('player_attached_proof_file_model','kyc_status_model'));
			$this->load->library(['player_security_library','player_library']);

			$verificationInfo = $this->kyc_status_model->get_verification_info($playerId);

            $tag = $this->input->post('tag');
            $image = isset($_FILES['txtImage']) ? $_FILES['txtImage'] : null;
            $remarks = $this->input->post('remarks');
            $comments = $this->input->post('comments');

            if(empty($tag)){
                $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('notify.67'), NULL, '/player_management/userInformation/' . $playerId);
            }

            switch ($tag){
                case BaseModel::Verification_Photo_ID:
                    $id_card_number = $this->input->post('id_card_number');
                    if(isset($id_card_number) && !empty($id_card_number)){
                        $this->load->library(['player_library']);
                        $playerdetails['id_card_number'] = $id_card_number;
                        $modifiedFields = $this->player_library->checkModifiedFields($playerId, $playerdetails);
                        $this->player_library->editPlayerDetails($playerdetails, $playerId);
                        $this->player_library->savePlayerUpdateLog($playerId, lang('lang.edit') . ' ' . lang('lang.playerinfo') . ' (' . $modifiedFields . ')', $this->authentication->getUsername()); // Add log in playerupdatehistory
                    }
                    $response = $this->player_security_library->request_upload_realname_verification($playerId, $tag, $image, $remarks, $comments);
                    break;
                case BaseModel::Verification_Adress:
                    $response = $this->player_security_library->request_upload_address($playerId, $tag, $image, $remarks, $comments);
                    break;
                case BaseModel::Verification_Income:
                    $response = $this->player_security_library->request_upload_deposit_withdrawal($playerId, $tag, $image, $remarks, $comments);
                    break;
                case BaseModel::Verification_Deposit_Withrawal:
                    $response = $this->player_security_library->request_upload_income($playerId, $tag, $image, $remarks, $comments);
                    break;
                default:
                    $response['status'] = "success";
                    break;

            }


			if(!empty($response)){
				$this->alertMessage($response['msg_type'], $response['msg']);
				if($response['status'] == "success"){

					if(!empty($image) || !empty($remarks) || !empty($comments)){
						$logs = array();

                        if(isset($image)){
                            if(!empty($image['name'][0]) && !empty($image['tmp_name'][0])){
                                $logs[] = array(
                                    "action" => lang('role.238'),
                                    "description" => lang('role.238') . ' - '. lang('Successfully uploaded.') . ' - ' .lang('Image Document').' '.lang('upload_tag_'.$tag)
                                );
                            }
                        }

                        if(isset($remarks) && isset($verificationInfo[$tag])){
                            if(!empty($verificationInfo[$tag])){
                                foreach ($verificationInfo[$tag] as $key => $value) {
                                    if($key != $remarks){
                                        $logs[] = array(
                                            "action" => lang('role.238'),
                                            "description" => lang('role.238') . ' - ' .lang('adjustmenthistory.title.beforeadjustment') . ' (' . $key . ') ' .lang('adjustmenthistory.title.afteradjustment') . ' (' . $remarks . ') '.'in '.lang('upload_tag_'.$tag).' '.lang('Remarks')
                                        );
                                    }
                                }
                            }
                        }

                        if(isset($comments) && isset($verificationInfo[$tag])){
                            if(!empty($verificationInfo[$tag])){
                                foreach ($verificationInfo[$tag] as $key => $value) {
                                    if($value['comments'] != $comments){
                                        $logs[] = array(
                                            "action" => lang('role.238'),
                                            "description" => lang('role.238') . ' - ' .lang('adjustmenthistory.title.beforeadjustment') . ' (' . $value['comments'] . ') ' .lang('adjustmenthistory.title.afteradjustment') . ' (' . $comments . ') '.'in '.lang('upload_tag_'.$tag).' '.lang('lang.comments')
                                        );
                                    }
                                }
                            }
                        }

						if(!empty($logs)){
							foreach ($logs as $key => $value) {
								// Add log in playerupdatehistory
								$playerInfo = $this->player_model->getPlayerDetailsTagsById($playerId);
								if(!empty($playerInfo)){
									$this->savePlayerUpdateLog($playerId, $value['description'], $this->authentication->getUsername());
									$this->saveAction(self::ACTION_MANAGEMENT_TITLE, $value['action'], "User " . $this->authentication->getUsername() . $value['description'] ." player '" . $playerInfo['username'] . "'");
								}
							}
						}
					}
				}
			}

			$this->load->model('player_attached_proof_file_model');
			// -- update attached_file_status table for new attchment status history
			$this->player_attached_proof_file_model->saveAttachedFileStatusHistory($playerId);

			redirect('/player_management/userInformation/' . $playerId);
		}
	}

	public function delKYCPlayerImage(){
		if (!$this->permissions->checkPermissions('update_attached_documents')) {
			$this->error_access();

			$response = array(
				'status' => 'error',
				'msg' => lang('con.plm01'),
				'msg_type' => self::MESSAGE_TYPE_ERROR
			);

			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult($response);
				return;
			}

		} else {
			$this->load->model(array('player_attached_proof_file_model'));
			$this->load->library('player_security_library');
			$playerId = $this->input->post('playerId');
			$tag = $this->input->post('tag');
			$data = [
				'picId' => $this->input->post('picId'),
				'playerId' => $playerId,
			];

			$response = $this->player_security_library->remove_kyc_player_proof_document($data);

			if(!empty($response)){
				$this->alertMessage($response['msg_type'], $response['msg']);
				if($response['status'] == "success"){
					if(!empty($tag)){
							$action = lang('role.238');
							$description = lang('role.238') . ' - '. lang('Image successfully deleted!') . ' - ' .lang('Image Document').' '.lang('upload_tag_'.$tag);

							$playerInfo = $this->player_model->getPlayerDetailsTagsById($playerId);
							if(!empty($playerInfo)){
								$this->savePlayerUpdateLog($playerId, $description, $this->authentication->getUsername());
								$this->saveAction(self::ACTION_MANAGEMENT_TITLE, $action, "User " . $this->authentication->getUsername() . $description ." player '" . $playerInfo['username'] . "'");
							}
					}
				}

				if ($this->input->is_ajax_request()) {
					$this->returnJsonResult($response);
					return;
				}
			}

			redirect('/player_management/userInformation/' . $playerId);
		}
	}
	/* KYC IMAGE UPLOAD end */

	public function broadcast_message(){
        if (!$this->permissions->checkPermissions('send_message_to_all')) {
            return $this->error_access();
        }

        $this->load->library(['player_message_library']);

		$this->load->model(array('internal_message'));
        $sender = $this->input->post('broadcast_message_sender');
        $subject = $this->input->post('broadcast_message_subject');
        $message = $this->input->post('broadcast_message_body');
		// $playerIds = $this->input->post('playerIds');
		if (!$subject OR !$message) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('sys.ga.erroccured'));
			redirect('/player_management/searchAllPlayer');
			return;
		}

		$this->load->library(['lib_queue']);
		$this->load->model(['queue_result']);

		$request=$this->input->post('json_search');
		$request=$this->utils->decodeJson($request);

		$userId = $this->authentication->getUserId();
		$sender = (empty($sender)) ? $this->player_message_library->getDefaultAdminSenderName() : $sender;
        $sender = (empty($sender)) ? $this->authentication->getUsername() : $sender;
        $systemId = Queue_result::SYSTEM_UNKNOWN;
		$callerType=Queue_result::CALLER_TYPE_ADMIN;
		$caller=0;
		$state='';
		$funcName='broadcast_message';
		$params=['search_query'=>$request, 'message_subject'=>$subject, 'message_body'=>$message,
			'userId'=>$userId, 'sender'=>$sender];

		$this->load->library(['language_function']);
		$lang=$this->language_function->getCurrentLanguage();

		$token=$this->lib_queue->addBroadcastMessageJob($systemId, $funcName, $params, $callerType, $caller, $state, $lang);
        $link=site_url('/system_management/common_queue/'.$token);

		redirect($link);
	}

	public function updateGameInfo($playerId, $gameId){
		$infos = null;
		$this->load->model('player_model');
		$system_code = $this->input->post("system_code");
		$info_params = $this->input->post("info_params");
		if(!empty($info_params)){
			$infos = (array)json_decode($info_params);
		}
		$player = $this->player_model->getPlayerById($playerId);
		$playerName = $player->username;
		$api = $this->utils->loadExternalSystemLibObject($gameId);
		$result = $api->updatePlayerInfo($playerName,$infos);
		if($result['success']){
			$message = $system_code." ".lang('con.plm46');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		}
		else{
			$message = $system_code." ".lang('Update Failed');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		}
		redirect('/player_management/userInformation/' . $playerId . '#game_form', 'refresh');
	}

	public function updateAdditionalInfo($playerId, $gameId){
		$addis = null;
		$this->load->model('player_model');
		$this->load->model('game_provider_auth');
		$system_code = $this->input->post("system_code");
		$additional_params = $this->input->post("additional_params");
		if(!empty($additional_params)){
			$addis = $additional_params;
		}
		$player = $this->player_model->getPlayerById($playerId);
		$playerName = $player->username;
		$result = $this->game_provider_auth->addGameAdditionalInfo($playerName, $addis, $gameId);
		if($result){
			$message = $system_code." ".lang('con.plm46');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		}
		else{
			$message = $system_code." ".lang('Update Failed');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		}
		redirect('/player_management/userInformation/' . $playerId . '#game_form', 'refresh');
	}

	public function updateVisibilitystatus(){
		if (!$this->permissions->checkPermissions('update_attached_documents')) {
			$this->error_access();

			$response = array(
				'status' => 'error',
				'msg' => lang('con.plm01'),
				'msg_type' => self::MESSAGE_TYPE_ERROR
			);

			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult($response);
				return;
			}

		} else {
			$this->load->model(array('player_attached_proof_file_model'));
			$playerId = $this->input->post('playerId');
			$action = $this->input->post('action');
			$picId = $this->input->post('picId');
			$tag = $this->input->post('tag');
			$data = [
				'action' => $action,
				'picId' => $picId,
				'playerId' => $playerId,
			];

			$response = $this->player_attached_proof_file_model->change_visibility_proof_document($data);

			if(!empty($response)){
				$this->alertMessage($response['msg_type'], $response['msg']);
				if($response['status'] == "success"){
					if(!empty($tag)){
							$action = lang('role.238');
							$description = lang('role.238') . ' - '. lang('Image successfully update!') . ' - ' .lang('Image Document').' '.lang('upload_tag_'.$tag);

							// $playerInfo = $this->player->getPlayerById($playerId);
							$playerInfo = $this->player_model->getPlayerDetailsTagsById($playerId);
							if(!empty($playerInfo)){
								$this->savePlayerUpdateLog($playerId, $description, $this->authentication->getUsername());

								$this->saveAction(self::ACTION_MANAGEMENT_TITLE, $action, "User " . $this->authentication->getUsername() . $description ." player '" . $playerInfo['username'] . "'");
							}
					}
				}

				if ($this->input->is_ajax_request()) {
					$this->returnJsonResult($response);
					return;
				}
			}

			redirect('/player_management/userInformation/' . $playerId);
		}
	}

	/**
	 * overview : auto generate player with given date range
	 *
	 * detail : add player
	 */
	public function accountAutoProcess() {
		if (!$this->permissions->checkPermissions('upload_batch_player')) {
			$this->error_access();
		} else {

			$this->loadTemplate('Player Management', '', '', 'player');

			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			$data['batch'] = $this->player_manager->getBatchAccount(null, null);

			$this->template->add_js('resources/js/strength.min.js');
			$this->template->add_css('resources/css/strength.css');
			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/view_account_auto_process_list', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : verify account auto process
	 *
	 * detail : validates and verifies input of the end user and will add auto account
	 */
	public function verifyAddAccountAutoProcess() {
		if (!$this->permissions->checkPermissions('upload_batch_player')) {
			$this->error_access();
		} else {
			$this->load->library(array('agency_library'));
			/*if(!$this->permissions->checkPermissions('add_account_batch_process')){
				$this->error_access();
			*/
			$this->load->helper('url');
			$input = $this->input->post();

			if (!isset($_FILES['import']['tmp_name'])) {
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('File not found!'));
					redirect("player_management/accountAutoProcess");
			} else {

				$date_from = strtotime($input['by_date_from']);
				$date_to = strtotime($input['by_date_to']);

				$response_data = array();
				$player_data = array();
				$text = file_get_contents($_FILES['import']['tmp_name']);
				$lines = preg_split('/\n|\r\n?/', $text);

				if (empty($lines)) {
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Format Error!'));
					redirect("player_management/accountAutoProcess");
				} else {

					$keys = array_shift($lines);
					$array_list = explode(',',$keys);
					if(!empty($array_list)){

						// output headers so that the file is downloaded rather than displayed
						header('Content-Type: text/csv; charset=utf-8');
						header('Content-Disposition: attachment; filename=data.csv');

						// create a file pointer connected to the output stream
						$output = fopen('php://output', 'w');

						// output the column headings
						$header_file = array('Username', 'Status', 'Player ID');

						if(isset($input['by_currency'])){
							array_push($header_file, 'Currency');
						}

						fputcsv($output, $header_file);

						foreach ($array_list as $key => $value) {
							$checkValue = $this->validateAlphaNum($value);
							if (!$checkValue) {
								continue;
							}
							$ran_date = mt_rand($date_from,$date_to);
							if(!empty($value)) {
								if($this->player_model->usernameExist($value)) {
									$playerId = $this->player_model->getPlayerIdByPlayerName($value);
									$playerInfo = $this->player_model->getPlayerArrayById($playerId);
									if(empty($playerId)){
										$playerId = 0;
										$currency = null;
									} else {
										if(!isset($input['by_currency'])) {
											$currency = null;
										} else {
											$currency = $playerInfo['currency'];
										}
									}

									array_push($response_data, array($value,'Existed',$playerId,$currency));
									fputcsv($output, array($value, 'Existed',$playerId,$currency));
								} else {
									$player_data =  array(
										# Player
										'username' 			=> strtolower(str_replace(' ', '', $value)),
										'password'			=> $input['password'],
										'createdOn'			=> date("Y-m-d H:i:s",$ran_date),
									);

									if( ! empty($input['registered_by']) ){ // Patch for OGP-15155
										$player_data['registered_by'] = $input['registered_by'];
									}

									if($this->utils->isEnabledFeature('enable_username_cross_site_checking')){
									//global lock
										$add_prefix = false;
										$anyid = 0;
									} else {
										//not global lock
										$add_prefix = true;
										$anyid = random_string('numeric', 5);
									}
									$controller = $this;
									$this->lockAndTrans(Utils::GLOBAL_LOCK_ACTION_REGISTRATION, $anyid, function () use ($controller, &$player_data,&$playerId,$input) {

											$playerId = $controller->player_model->register($player_data,false,false,true);
											if (!empty($playerId)) {
												if(isset($input['by_currency'])){
													$data = array(
														'currency' => $input['by_currency'],
													);
													$controller->player_model->updatePlayer($playerId,$data);
												}
												return true;
											} else {
												return false;
											}
											//return (!empty($playerId)) ? true : false;
										},$add_prefix);
									$playerInfo = $this->player_model->getPlayerArrayById($playerId);
									if(empty($playerInfo)){
										$playerId = 0;
										$currency = null;
									} else {
										if(!isset($input['by_currency'])) {
											$currency = null;
										} else {
											$currency = $playerInfo['currency'];
										}
									}


									array_push($response_data, array($value,'Added',$playerId,$currency));
									fputcsv($output, array($value, 'Added',$playerId,$currency));
								}

							}
						}

						fclose($output);

				}

				$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Upload Mass Generate Player Account', "User " . $this->authentication->getUsername() . " has added mass auto account of Player");
				}
			}
		}
	}

	/**
     * Validate that an attribute contains only alpha-numeric characters.
     *
     * @param  mixed   $value
     * @return bool
     */
	public function validateAlphaNum($value) {
        if (! is_string($value) && ! is_numeric($value)) {
            return false;
		}

        return preg_match('/^[\pL\pM\pN]+$/u', $value) > 0;
    }

	/**
	 * Displays player linked accounts based on player username
	 *
	 * @param  string $username      Player Username
	 * @return view                   Display
	 */
	public function showLinkedAccountsModal($username) {
		$data = array();
		if (!$this->permissions->checkPermissions('linked_account') && !$this->utils->isEnabledFeature('linked_account')) {
			echo lang('con.plm01');
		} else {

			$linkedAccounts = $this->getLinkedAccountDetails($username);
			$data['linked_accounts'] = !empty($linkedAccounts) ? $linkedAccounts[self::FIRST_CHILD_INDEX]['linked_accounts'] : null;
			//die('<pre>'.print_r($data['linked_accounts'],true));
			$this->load->view('player_management/linked_account/link_accounts_details_on_modal', $data);
		}
	}

	/**
	 * Displays player IP history in modal
	 *
	 * @param  string $player_id      Player ID
	 * @return view                   Display
	 */
	public function showIPHistoryModal($player_id) {
		$data = array();
		$this->db->select('http_request.ip, http_request.type, http_request.referrer, http_request.device, http_request.createdat');
		$this->db->from('http_request');
		$this->db->where('http_request.playerId = '.$player_id);
		$this->db->order_by('http_request.id','desc');
		$query = $this->db->get()->result_array();
		$data['ip_history'] = !empty($query) ? $query : null;
		//die('<pre>'.print_r($data['ip_history'],true));
		$this->load->view('player_management/ip_history/ip_history_on_modal', $data);
	}

	public function import_players(){
		if (!$this->permissions->checkPermissions('account_process')) {
			$this->error_access();
			return;
		}

		$this->loadTemplate('Player Management', '', '', 'player');

		if (($this->session->userdata('sidebar_status') == NULL)) {
			$this->session->set_userdata(array('sidebar_status' => 'active'));
		}

		$data=['default_importer'=>$this->utils->getConfig('default_importer')];

		// $this->utils->debug_log($data);

		// $this->template->add_js('resources/js/strength.min.js');
		// $this->template->add_css('resources/css/strength.css');
		$this->template->write_view('sidebar', 'player_management/sidebar');
		$this->template->write_view('main_content', 'player_management/view_import_players', $data);
		$this->template->render();
	}

	private function getUploadCSVFilePath(){
		return $this->utils->getSharingUploadPath('/upload_temp_csv');
	}

	private function processFileOrText($uploadFieldName, $textFieldName, &$filepath, &$message){
		if($this->existsUploadField($uploadFieldName)){
			$filepath='';
			$msg='';
			//check file type
			if($this->saveUploadFileToRemote($uploadFieldName, ['csv'], $filepath, $msg)){
				//get $filepath

			}else{
		        $message=lang('Upload csv file failed').', '.$msg;
				return false;
			}
		}else{
			//try import_aff_text
			$import_aff_text=$this->input->post($textFieldName);
			if(!empty($import_aff_text)){
				//save it to file
				$dt=new DateTime();
				$uploadCsvFilepath=$this->getUploadCSVFilePath();
				$filepath=$uploadCsvFilepath.'/'.$uploadFieldName.'_'.$dt->format('YmdHis').'_'.random_string().'.csv';
				if(file_put_contents($filepath, $import_aff_text)===false){
			        $message=lang('Save text failed').', '.$textFieldName;
					return false;
				}
			}
		}

		$this->utils->debug_log($uploadFieldName.' or '.$textFieldName.' to '.$filepath);

		return true;
	}

	public function post_import_players($next){
		//send it to remote
		if (!$this->permissions->checkPermissions('account_process')
				|| !$this->users->isT1Admin($this->authentication->getUsername())) {
			$this->error_access();
			return;
		}

		if($next=='preview'){
			$importer_formatter=$this->input->post('importer_formatter');

			$data=[
				'importer_formatter'=>$importer_formatter,
			];

			//process file or text and show summary
			$filepath='';
			$message='';
			$summary=null;
			if(!$this->processFileOrText('import_player_csv_file', 'import_player_text', $filepath, $message)){
		        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				return redirect('/player_management/import_players');
			}
			//player is required csv/text
			if(!empty($filepath)){
				//process file
				//check format
				if(!$this->player_model->validPlayerCSV($importer_formatter, $filepath, $summary, $message)){
			        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
					return redirect('/player_management/import_players');
				}
			}else{
				$message=lang('Lost player info');
				//error
		        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				return redirect('/player_management/import_players');
			}
			$data['validPlayerCSV']=$summary;
			$data['import_player_csv_file']=empty($filepath) ? null : basename($filepath);

			$filepath='';
			$message='';
			$summary=null;
			if(!$this->processFileOrText('import_aff_csv_file', 'import_aff_text', $filepath, $message)){
		        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				return redirect('/player_management/import_players');
			}
			//ignore aff csv/text
			if(!empty($filepath)){
				//process file
				//check format
				if(!$this->player_model->validAffCSV($importer_formatter, $filepath, $summary, $message)){
			        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
					return redirect('/player_management/import_players');
				}
			}
			$data['validAffCSV']=$summary;
			$data['import_aff_csv_file']=empty($filepath) ? null : basename($filepath);

			$filepath='';
			$message='';
			$summary=null;
			if(!$this->processFileOrText('import_aff_contact_csv_file', 'import_aff_contact_text', $filepath, $message)){
		        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				return redirect('/player_management/import_players');
			}
			//ignore aff contact csv/text
			if(!empty($filepath)){
				//process file
				//check format
				if(!$this->player_model->validAffContactCSV($importer_formatter, $filepath, $summary, $message)){
			        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
					return redirect('/player_management/import_players');
				}
			}
			$data['validAffContactCSV']=$summary;
			$data['import_aff_contact_csv_file']=empty($filepath) ? null : basename($filepath);

			$filepath='';
			$message='';
			$summary=null;
			if(!$this->processFileOrText('import_player_contact_csv_file', 'import_player_contact_text', $filepath, $message)){
		        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				return redirect('/player_management/import_players');
			}
			//ignore player contact csv/text
			if(!empty($filepath)){
				//process file
				//check format
				if(!$this->player_model->validPlayerContactCSV($importer_formatter, $filepath, $summary, $message)){
			        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
					return redirect('/player_management/import_players');
				}
			}
			$data['validPlayerContactCSV']=$summary;
			$data['import_player_contact_csv_file']=empty($filepath) ? null : basename($filepath);

			$filepath='';
			$message='';
			$summary=null;
			if(!$this->processFileOrText('import_player_bank_csv_file', 'import_player_bank_text', $filepath, $message)){
		        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				return redirect('/player_management/import_players');
			}
			//ignore player bank csv/text
			if(!empty($filepath)){
				//process file
				//check format
				if(!$this->player_model->validPlayerBankCSV($importer_formatter, $filepath, $summary, $message)){
			        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
					return redirect('/player_management/import_players');
				}
			}
			$data['validPlayerBankCSV']=$summary;
			$data['import_player_bank_csv_file']=empty($filepath) ? null : basename($filepath);

			$filepath='';
			$message='';
			$summary=null;
			if(!$this->processFileOrText('import_agency_csv_file', 'import_agency_text', $filepath, $message)){
		        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				return redirect('/player_management/import_players');
			}
			//ignore agency csv/text
			if(!empty($filepath)){
				//process file
				//check format
				if(!$this->player_model->validAgencyCSV($importer_formatter, $filepath, $summary, $message)){
			        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
					return redirect('/player_management/import_players');
				}
			}
			$data['validAgencyCSV']=$summary;
			$data['import_agency_csv_file']=empty($filepath) ? null : basename($filepath);

			$filepath='';
			$message='';
			$summary=null;
			if(!$this->processFileOrText('import_agency_contact_csv_file', 'import_agency_contact_text', $filepath, $message)){
		        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				return redirect('/player_management/import_players');
			}
			//ignore aff contact csv/text
			if(!empty($filepath)){
				//process file
				//check format
				if(!$this->player_model->validAgencyContactCSV($importer_formatter, $filepath, $summary, $message)){
			        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
					return redirect('/player_management/import_players');
				}
			}
			$data['validAgencyContactCSV']=$summary;
			$data['import_agency_contact_csv_file']=empty($filepath) ? null : basename($filepath);

			$this->loadTemplate('Player Management', '', '', 'player');

			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			$this->utils->debug_log('preview data', $data);

			// $this->template->add_js('resources/js/strength.min.js');
			// $this->template->add_css('resources/css/strength.css');
			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/view_import_players_preview', $data);
			$this->template->render();
			return;

		}else if($next=='start'){
			//load files
			$import_player_csv_file=$this->input->post('import_player_csv_file');
			$import_aff_csv_file=$this->input->post('import_aff_csv_file');
			$import_aff_contact_csv_file=$this->input->post('import_aff_contact_csv_file');
			$import_player_contact_csv_file=$this->input->post('import_player_contact_csv_file');
			$import_player_bank_csv_file=$this->input->post('import_player_bank_csv_file');
			$import_agency_csv_file=$this->input->post('import_agency_csv_file');
			$import_agency_contact_csv_file=$this->input->post('import_agency_contact_csv_file');

			$importer_formatter=$this->input->post('importer_formatter');

			if(empty($import_player_csv_file)){
				$message=lang('Lost player info');
				//error
		        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				return redirect('/player_management/import_players');
			}

			$files=[
				'import_player_csv_file'=>$import_player_csv_file,
				'import_aff_csv_file'=>$import_aff_csv_file,
				'import_aff_contact_csv_file'=>$import_aff_contact_csv_file,
				'import_player_contact_csv_file'=>$import_player_contact_csv_file,
				'import_player_bank_csv_file'=>$import_player_bank_csv_file,
				'import_agency_csv_file'=>$import_agency_csv_file,
				'import_agency_contact_csv_file'=>$import_agency_contact_csv_file,
			];
			$this->load->library(['lib_queue']);
			$callerType=Queue_result::CALLER_TYPE_ADMIN;
			$caller=$this->authentication->getUserId();
			$state=null;
			$lang=$this->language_function->getCurrentLanguage();
			//save csv file
			$token=$this->lib_queue->addRemoteImportPlayers($files, $importer_formatter, $callerType, $caller, $state, $lang);

		    if (!empty($token)) {

		        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Create importing job successfully'));
				return redirect('/system_management/common_queue/'.$token);

		    } else {
		        $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Create importing job failed'));
				redirect('/player_management/import_players');
		    }
		}

        $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Import job failed, wrong step').': '.$next);
		redirect('/player_management/import_players');
	}

	public function sync_player_to_mdb($playerId){
		if (!$this->permissions->checkPermissions('player_list') || empty($playerId)) {
			return $this->error_access();
		}

		$rlt=null;
		$success=false;
		$this->load->model(['player_model']);
		$username=$this->player_model->getUsernameById($playerId);
		if(!empty($username)){
			$success=$this->syncPlayerCurrentToMDBWithLock($playerId, $username, false, $rlt);
		}

		if(!$success){
			$errKeys=[];
			foreach ($rlt as $dbKey => $dbRlt) {
				if(!$dbRlt['success']){
					$errKeys[]=$dbKey;
				}
			}
			$errorMessage=implode(',', $errKeys);
		    $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sync Player Failed').': '.$errorMessage);
		}else{
		    $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Sync Player Successfully'));
		}

		redirect('/player_management/userInformation/'.$playerId);
	}

    public function update_nttech_game_bet_limit_view($player_id, $game_id){
    	if (!$this->permissions->checkPermissions('set_player_bet_limit_for_api')) {
			$this->error_access();
		} else {
			$data['player_id'] = $player_id;
			$data['game_platform_id'] = $game_id;
			# LOAD GAME API
			$api = $this->utils->loadExternalSystemLibObject($game_id);
			$player_info = $this->player_model->getPlayerInfoById($player_id);
			$data['system_code'] = $api->getSystemInfo('system_code');
			$data['username'] = $player_info['username'];
			$this->template->add_js('resources/js/highlight.pack.js');
			$this->template->add_js('resources/js/ace/ace.js');
			$this->template->add_js('resources/js/ace/mode-json.js');
			$this->template->add_css('resources/css/hljs.tomorrow.css');
			$this->loadTemplate(lang('Player Management'), '', '', 'player');
			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/view_user_update_nttech_game_bet_limit',$data);
			$this->template->render();
			$this->utils->endEvent('template_render');
		}
    }

    public function update_sbobet_game_bet_limit_view($player_id, $game_id){
    	if (!$this->permissions->checkPermissions('set_player_bet_limit_for_api')) {
			$this->error_access();
		} else {
			$data['player_id'] = $player_id;
			$data['game_platform_id'] = $game_id;
			# LOAD GAME API
			$api = $this->utils->loadExternalSystemLibObject($game_id);
			$player_info = $this->player_model->getPlayerInfoById($player_id);
			$response = $api->getMemberBetSetting($player_info['username']);

			$json_data = json_encode($response['result']);
			if(isset($response['result']['betsetting'])){
				$json_data = json_encode($response['result']['betsetting']);
			}
			if(isset($response['result']['betSettings'])){
				$json_data = json_encode($response['result']['betSettings']);
			}

			array_walk($api->getAllSportType(),function($value, $key) use (&$sport_array){
				$sport_array[] = array("id" => $key, "text" => $value);
			});

			array_walk($api->getAllSportType(),function($value, $key) use (&$market_array){
				$market_array[] = array("id" => $key, "text" => $value);
			});


			$data['system_code'] = $api->getSystemInfo('system_code');
			$data['username'] = $player_info['username'];
			$data['json'] = $json_data;
			// $data['sport_types'] = $api->getAllSportType();
			$data['sport_types'] = json_encode($sport_array);
			// $data['market_types'] = $api->getAllMarketType();
			$data['market_types'] = json_encode($market_array);
			$this->template->add_js('resources/js/highlight.pack.js');
			$this->template->add_js('resources/js/ace/ace.js');
			$this->template->add_js('resources/js/ace/mode-json.js');
			$this->template->add_js('resources/js/select2.full.min.js');
			$this->template->add_css('resources/css/select2.min.css');
			$this->template->add_css('resources/css/hljs.tomorrow.css');
			$this->loadTemplate(lang('Player Management'), '', '', 'player');
			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/view_user_update_sbobet_game_bet_limit',$data);
			$this->template->render();
			$this->utils->endEvent('template_render');
		}
    }

    public function update_sbobet_game_bet_limit($playerId, $gameId){
		// $infos = null;
		$this->load->model('player_model');
		$system_code = $this->input->post("system_code");
		$json_params = $this->input->post("json_params");

		if(!empty($json_params)){
			$infos = json_decode($json_params,true);
		}

		$infos = array_map("unserialize", array_unique(array_map("serialize", $infos)));
		$player = $this->player_model->getPlayerById($playerId);
		$playerName = $player->username;
		$api = $this->utils->loadExternalSystemLibObject($gameId);
		$result = $api->setMemberBetSetting($playerName,$infos);

		if($result['success']){
			$message = $system_code." ".lang('con.plm46');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		}
		else{
			$message = $system_code." ".lang('Update Failed');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		}
		redirect('/player_management/userInformation/' . $playerId . '/9', 'refresh');
	}

    public function update_oneworks_game_bet_limit_view($player_id, $game_id){
    	if (!$this->permissions->checkPermissions('set_player_bet_limit_for_api')) {
			$this->error_access();
		} else {
			$player_bet_setting = array();
			$data['playerId'] = $player_id;
			$data['game_platform_id'] = $game_id;
			# LOAD GAME API
			$api = $this->utils->loadExternalSystemLibObject($game_id);
			$player_info = $this->player_model->getPlayerInfoById($player_id);
			$data['system_code'] = $api->getSystemInfo('system_code');
			$response = $api->getMemberBetSetting($player_info['username']);
			$sports  = $api->getSports();
			$sports2 = array();
			$required_ids = array("1","2","3","5","8","10","11","99","99MP");//required base on docs
			if($game_id == IBC_API){
				$response['result'] = $response;
			}
			if($response['success'] && $response['result']['error_code'] == 0){
				$results = $response['result']['Data'];
				if(!empty($results)){
					foreach ($results as $key => $value) {
						$bet_data = array(
							"id" => $value['sport_type'],
							"sport" => $api->getSports($value['sport_type']),
							"min_bet" => $value['min_bet'],
							"max_bet" => $value['max_bet'],
							"max_bet_per_match" => $value['max_bet_per_match'],
						);
						$bet_data['max_bet_per_ball'] = isset($value['max_bet_per_ball']) ? $value['max_bet_per_ball'] : 0;
						$bet_data['required'] = in_array($value['sport_type'],$required_ids) ? "Y" : "N";
						$player_bet_setting[] = $bet_data;
						unset($bet_data);
					}
				}
			}

			if(!empty($sports)){
				foreach ($sports as $key => $value) {
					$sports_data = array(
						"id" => $key,
						"text" => $value,
					);
					$sports2[] = $sports_data;
					unset($sports_data);
				}
			}
			$data['username'] = $player_info['username'];
			$data['player_bet_setting'] = json_encode($player_bet_setting);
			$data['sports'] = json_encode($sports2);
			// $data['response_success'] = false;
			// $data['response_message'] = "Under Maintenance";
			$data['response_success'] = $response['success'];
			$data['response_message'] = $response['result']['message'];
			$this->template->add_js('resources/js/highlight.pack.js');
			$this->template->add_js('resources/js/ace/ace.js');
			$this->template->add_js('resources/js/ace/mode-json.js');
			$this->template->add_css('resources/css/hljs.tomorrow.css');
			$this->loadTemplate(lang('Player Management'), '', '', 'player');
			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/view_user_update_oneworks_game_bet_limit',$data);
			$this->template->render();
			$this->utils->endEvent('template_render');
		}
	}

	public function update_ogplus_game_bet_limit_view($player_id, $game_id) {
		if (!$this->permissions->checkPermissions('set_player_bet_limit_for_api')) {
			$this->error_access();
		} else {

			$player_bet_setting = array();
			$data['player_id'] = $player_id;
			$data['game_platform_id'] = $game_id;
			# LOAD GAME API
			$api = $this->utils->loadExternalSystemLibObject($game_id);
			$player_info = $this->player_model->getPlayerInfoById($player_id);
			$data['system_code'] = $api->getSystemInfo('system_code');
			$data['username'] = $player_info['username'];
			$this->template->add_js('resources/js/highlight.pack.js');
			$this->template->add_js('resources/js/ace/ace.js');
			$this->template->add_js('resources/js/ace/mode-json.js');
			$this->template->add_css('resources/css/hljs.tomorrow.css');
			$this->loadTemplate(lang('Player Management'), '', '', 'player');
			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/view_user_update_ogplus_game_bet_limit', $data);
			$this->template->render();
			$this->utils->endEvent('template_render');
		}

	}

	public function get_noteid_Info($note_id) {
		$result['success'] = false;
		$note = $this->player_manager->getNoteById($note_id);

		if($note_id == $note['noteId']){
			$result['success'] = $note_id;
		}

		return $this->returnJsonResult($result);
	}
}
