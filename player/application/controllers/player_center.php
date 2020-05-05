<?php

require_once dirname(__FILE__) . '/PlayerBaseController.php';

require_once dirname(__FILE__) . '/modules/promo_module.php';
require_once dirname(__FILE__) . '/modules/promo_cms_module.php';
require_once dirname(__FILE__) . '/modules/gotogame_module.php';
require_once dirname(__FILE__) . '/modules/player_auth_module.php';
require_once dirname(__FILE__) . '/modules/player_password_module.php';
require_once dirname(__FILE__) . '/modules/player_deposit_module.php';
require_once dirname(__FILE__) . '/modules/player_withdraw_module.php';
require_once dirname(__FILE__) . '/modules/player_bank_module.php';
require_once dirname(__FILE__) . '/allowed_withdrawal_kyc_risk_score.php';
require_once dirname(__FILE__) . '/modules/shopping_center_module.php';
require_once dirname(__FILE__) . '/modules/player_profile.php';
require_once dirname(__FILE__) . '/modules/player_cashback_module.php';
require_once dirname(__FILE__) . '/modules/player_center_redirect.php';

class Player_center extends PlayerBaseController {

	protected $view_template;

	CONST PROFILE_PICTURE_PATH = '/player/profile_picture';

	function __construct() {
		parent::__construct();

		$this->load->library(array('form_validation', 'authentication', 'language_function', 'player_functions', 'cms_function', 'template', 'pagination', 'api_functions', 'salt', 'cs_manager', 'email_setting', 'og_utility', 'game_platform/game_platform_manager', 'duplicate_account', 'affiliate_process'));

		$language = $this->input->get('lang', true);
		$this->view_template = $this->utils->getPlayerCenterTemplate();

		switch ($language) {
		case 'en':
			$this->language_function->setCurrentLanguage(1);
			$this->lang->is_loaded = array();
			$this->lang->language = array();
			$this->lang->load('main', 'english');
			break;

		case 'zh-cn':
			$this->language_function->setCurrentLanguage(2);
			$this->lang->is_loaded = array();
			$this->lang->language = array();
			$this->lang->load('main', 'chinese');
			break;
		}

		$this->load->helper('url');
		$this->load->model(array('http_request', 'player', 'wallet_model', 'operatorglobalsettings', 'registration_setting','player_model' ));
	}

	use promo_module;
	use promo_cms_module;
	use gotogame_module;
	use player_auth_module;
	use player_password_module;
	use player_deposit_module;
	use player_withdraw_module;
	use player_bank_module;
	use allowed_withdrawal_kyc_risk_score;
	use shopping_center_module;

	use player_profile;
	use player_cashback_module;
	use player_center_redirect;

	const FORBIDDEN_NAMES = ['admin', 'moderator', 'hoster', 'administrator', 'mod'];
	const MIN_USERNAME_LENGTH = 4;
	const MAX_USERNAME_LENGTH = 9;
	const MIN_PASSWORD_LENGTH = 4;
	const MAX_PASSWORD_LENGTH = 12;

	const FIRST_AVAILABLE_DEPOSIT = 0;
	const MESSAGE_CODE_NO_DEPOSIT = '1';
	const MESSAGE_CODE_NOT_AVAILABLE = '2';

	const zero_total = 0;
	const R1 = 1;
	const R2 = 2;
	const R3 = 3;
	const R4 = 4;
	const R5 = 5;
	const R6 = 6;
	const A = 1;
	const B = 2;
	const C = 3;
	const D = 4;
	const Settled = 5;

	function index() {
		redirect('player_center/iframe_home');
	}

	function iframe_viewCashier() {
		redirect('/player_center/dashboard');
	}

	/**
	 * Loads template for view based on regions in
	 * config > template.php
	 *
	 */
	private function loadTemplate($title = '', $description = '', $keywords = '', $activenav = '') {
		$this->template->set_template($this->utils->getPlayerCenterTemplate(false));

		# scripts that was originally written in template
		$base_path = base_url() . $this->utils->getPlayerCenterTemplate(false);
        $this->template->add_js('/resources/third_party/jquery-wizard/0.0.7/jquery.wizard.min.js');
		$this->template->add_js('/common/js/player_center/registered-autoplay-procedure.js');

		if ($this->config->item('override_title_value')) {
			$title = $this->config->item('override_title_value');
		}

        $this->template->append_function_title($title);
		$this->template->write('skin', 'template1.css');
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('player_id', $this->authentication->getPlayerId());
		$this->template->write('isLogged', $this->authentication->isLoggedIn());
		$this->template->write('isLogged', $this->authentication->isLoggedIn());

		$data['data'] = $this->player_functions->getPlayerMainWallet($this->authentication->getPlayerId());
		$this->template->write('mainwallet', $data['data']['mainwallet']['totalBalanceAmount']);
	}

	private function getRandomSequence() {
		$seed = str_split('0123456789123456'); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$randomNum = '';
		foreach (array_rand($seed, 16) as $k) {
			$randomNum .= $seed[$k];
		}

		return $randomNum;
	}

	////for HTML Template///////////////////////////////////////////////////////////

	/**
	 * Set rules for updating player details
	 *
	 * @return  redirect page
	 */
	public function formRulesEditPlayer() {
	    $this->form_validation->set_message('required', lang('formvalidation.required'));
		$this->form_validation->set_message('max_length', lang('formvalidation.max_length'));

		if (isset($_POST["language"]) && $this->player_functions->checkAccountFieldsIfRequired('Language')) {
			$this->form_validation->set_rules('language', lang('Language'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('language', lang('Language'), 'trim|xss_clean');
		}

		if (isset($_POST["contact_number"]) && $this->player_functions->checkAccountFieldsIfRequired('Contact Number')) {
			if ($this->utils->isEnabledFeature('allow_player_same_number')) {
				$this->form_validation->set_rules('contact_number', lang('Contact Number'), 'trim|required|xss_clean');
			} else {
				$this->form_validation->set_rules('contact_number', lang('Contact Number'), 'trim|required|xss_clean|callback_check_contact');
			}
		} elseif ($this->utils->isEnabledFeature('allow_player_same_number')) {
			$this->form_validation->set_rules('contact_number', lang('Contact Number'), 'trim|xss_clean');
		} else {
			$this->form_validation->set_rules('contact_number', lang('Contact Number'), 'trim|xss_clean|callback_check_contact');
		}

        if (isset($_POST["dialing_code"]) && $this->player_functions->checkAccountFieldsIfRequired('Dialing Code')) {
            $this->form_validation->set_rules('dialing_code', lang('a_reg.50'), 'trim|xss_clean|required');
        } else {
            $this->form_validation->set_rules('dialing_code', lang('a_reg.50'), 'trim|xss_clean');
        }

        $im1 = $this->config->item('Instant Message 1', 'cust_non_lang_translation');
		if (isset($_POST["im_account"]) && $this->player_functions->checkAccountFieldsIfRequired('Instant Message 1')) {
			$this->form_validation->set_rules('im_account', (($im1) ? : lang('Instant Message 1')), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('im_account', (($im1) ? : lang('Instant Message 1')), 'trim|xss_clean');
		}

        $im2 = $this->config->item('Instant Message 2', 'cust_non_lang_translation');
		if (isset($_POST["im_account2"]) && $this->player_functions->checkAccountFieldsIfRequired('Instant Message 2')) {
			$this->form_validation->set_rules('im_account2', (($im2) ? : lang('Instant Message 2')), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('im_account2', (($im2) ? : lang('Instant Message 2')), 'trim|xss_clean');
		}

        $im3 = $this->config->item('Instant Message 3', 'cust_non_lang_translation');
		if (isset($_POST["im_account3"]) && $this->player_functions->checkAccountFieldsIfRequired('Instant Message 3')) {
			$this->form_validation->set_rules('im_account3', (($im3) ? : lang('Instant Message 3')), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('im_account3', (($im3) ? : lang('Instant Message 3')), 'trim|xss_clean');
		}

		if (isset($_POST["citizenship"]) && $this->player_functions->checkAccountFieldsIfRequired('Nationality')) {
			$this->form_validation->set_rules('citizenship', lang('player.61'), 'trim|required|xss_clean|callback_check_country');
		} else {
			$this->form_validation->set_rules('citizenship', lang('player.61'), 'trim|xss_clean|callback_check_country');
		}

		if (isset($_POST["birthplace"]) && $this->player_functions->checkAccountFieldsIfRequired('BirthPlace')) {
			$this->form_validation->set_rules('birthplace', lang('reg.24'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('birthplace', lang('reg.24'), 'trim|xss_clean');
		}

		if ( isset($_POST["name"]) && $this->player_functions->checkAccountFieldsIfRequired('First Name')) {
			$this->form_validation->set_rules('name', lang('First Name'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('name', lang('First Name'), 'trim|xss_clean');
		}

		if ( isset($_POST["lastname"]) && $this->player_functions->checkAccountFieldsIfRequired('Last Name')) {
			$this->form_validation->set_rules('lastname', lang('Last Name'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('lastname', lang('Last Name'), 'trim|xss_clean');
		}

		if ( isset($_POST["email"]) && $this->player_functions->checkAccountFieldsIfRequired('Email')) {
			$this->form_validation->set_rules('email', lang('Email'), 'trim|required|xss_clean|callback_isIncludeCnChar');
		} else if( isset($_POST["email"]) ) {
			$this->form_validation->set_rules('email', lang('Email'), 'trim|xss_clean|callback_isIncludeCnChar');
		} else {
			$this->form_validation->set_rules('email', lang('Email'), 'trim|xss_clean');
		}

		if ( isset($_POST["birthdate"]) && $this->player_functions->checkAccountFieldsIfRequired('Birthday')) {
			$this->form_validation->set_rules('birthdate', lang('Birthday'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('birthdate', lang('Birthday'), 'trim|xss_clean');
		}

		if ( isset($_POST["gender"]) && $this->player_functions->checkAccountFieldsIfRequired('Gender')) {
			$this->form_validation->set_rules('gender', lang('Gender'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('gender', lang('Gender'), 'trim|xss_clean');
		}

		if (isset($_POST["region"]) && $this->player_functions->checkAccountFieldsIfRequired('Region')) {
			$this->form_validation->set_rules('region', lang('a_reg.37.placeholder'), 'trim|max_length[120]|required|xss_clean');
		} else {
			$this->form_validation->set_rules('region', lang('a_reg.37.placeholder'), 'trim|max_length[120]|xss_clean');
		}

		if (isset($_POST["city"]) && $this->player_functions->checkAccountFieldsIfRequired('City')) {
			$this->form_validation->set_rules('city', lang('a_reg.36.placeholder'), 'trim|max_length[120]|required|xss_clean');
		} else {
			$this->form_validation->set_rules('city', lang('a_reg.36.placeholder'), 'trim|max_length[120]|xss_clean');
		}

		if (isset($_POST["address"]) && $this->player_functions->checkAccountFieldsIfRequired('Address')) {
			$this->form_validation->set_rules('address', lang('a_reg.43.placeholder'), 'trim|max_length[120]|required|xss_clean');
		} else {
			$this->form_validation->set_rules('address', lang('a_reg.43.placeholder'), 'trim|max_length[120]|xss_clean');
		}

		if (isset($_POST["address2"]) && $this->player_functions->checkAccountFieldsIfRequired('Address2')) {
			$this->form_validation->set_rules('address2', lang('a_reg.44.placeholder'), 'trim|max_length[120]|required|xss_clean');
		} else {
			$this->form_validation->set_rules('address2', lang('a_reg.44.placeholder'), 'trim|max_length[120]|xss_clean');
		}

        if (isset($_POST["residentCountry"]) && $this->player_functions->checkAccountFieldsIfRequired('Resident Country')) {
            $this->form_validation->set_rules('residentCountry', lang('a_reg.33'), 'trim|required|xss_clean|callback_check_country');
        } else {
            $this->form_validation->set_rules('residentCountry', lang('a_reg.33'), 'trim|xss_clean|callback_check_country');
        }

		if (isset($_POST["zipcode"]) && $this->player_functions->checkAccountFieldsIfRequired('Zip Code')) {
			$this->form_validation->set_rules('zipcode', lang('a_reg.48'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('zipcode', lang('a_reg.48'), 'trim|xss_clean');
		}

		if (isset($_POST["id_card_number"]) && $this->player_functions->checkAccountFieldsIfRequired('ID Card Number')) {
			$this->form_validation->set_rules('id_card_number', lang('a_reg.49'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('id_card_number', lang('a_reg.49'), 'trim|xss_clean');
		}

		if (isset($_POST["id_card_type"]) && $this->player_functions->checkAccountFieldsIfRequired('ID Card Type')) {
			$this->form_validation->set_rules('id_card_type', lang('a_reg.51'), 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('id_card_type', lang('a_reg.51'), 'trim|xss_clean');
		}
	}

	public function ajax_player_profile_item_rule($field) {
		switch ($field) {
			case 'language' :
				if ($this->player_functions->checkRegisteredFieldsIfRequired('Language') == 0) {
					$this->form_validation->set_rules('value', lang('Language'), 'trim|required|xss_clean');
				} else {
					$this->form_validation->set_rules('la', lang('Language'), 'trim|xss_clean');
				}
				break;

			case 'contact_number' :
				$this->form_validation->set_rules('contact_number', lang('Contact Number'), 'trim|xss_clean');
				break;

			case 'im_account' :
				if ($this->player_functions->checkRegisteredFieldsIfRequired('Instant Message 1') == 0) {
					$this->form_validation->set_rules('im_account', lang('Instant Message 1'), 'trim|required|xss_clean');
				} else {
					$this->form_validation->set_rules('im_account', lang('Instant Message 1'), 'trim|xss_clean');
				}
				break;

			case 'im_account2' :
				if ($this->player_functions->checkRegisteredFieldsIfRequired('Instant Message 2') == 0) {
					$this->form_validation->set_rules('im_account2', lang('Instant Message 2'), 'trim|required|xss_clean');
				} else {
					$this->form_validation->set_rules('im_account2', lang('Instant Message 2'), 'trim|xss_clean');
				}
				break;

			case 'im_account3' :
				if ($this->player_functions->checkRegisteredFieldsIfRequired('Instant Message 2') == 0) {
					$this->form_validation->set_rules('im_account2', lang('Instant Message 2'), 'trim|required|xss_clean');
				} else {
					$this->form_validation->set_rules('im_account2', lang('Instant Message 2'), 'trim|xss_clean');
				}
				break;

			case 'nationality' :
				if ($this->player_functions->checkRegisteredFieldsIfRequired('Nationality') == 0) {
					$this->form_validation->set_rules('citizenship', lang('Nationality'), 'trim|required|xss_clean');
				} else {
					$this->form_validation->set_rules('citizenship', lang('Nationality'), 'trim|xss_clean');
				}
				break;

			case 'birthplace' :
				if ($this->player_functions->checkRegisteredFieldsIfRequired('BirthPlace') == 0) {
					$this->form_validation->set_rules('birthplace', lang('Birthplace'), 'trim|required|xss_clean');
				} else {
					$this->form_validation->set_rules('birthplace', lang('Birthplace'), 'trim|xss_clean');
				}
				break;

			default :
		} // End of switch
	} // End of function ajax_player_profile_item_rule()

	/**
	 * view playere settings or details
	 * redirect to /player_center/dashboard#accountInformation when using desktop
	 *
	 * @return rendered template
	 */
	public function profile() {
		$player_id = $this->authentication->getPlayerId();

		$this->load->model(['player_model','kyc_status_model','communication_preference_model']);

		$data['current_promo'] = "";
		$data['player'] = $this->player_model->getPlayerInfoDetailById($player_id);

		$data['bank_details'] = $this->player_functions->getBankDetails($player_id);
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$data['default_prefix_for_username'] = $this->config->item('default_prefix_for_username');
		$data['directChangePassword'] = $this->utils->checkPlayerCanDirectlyChangePassword();
		if (!empty($this->session->userdata('promoCode'))) {
			$promo = $this->player_functions->checkPromoCodeExist($this->session->userdata('promoCode'));
			$data['current_promo'] = $this->player_functions->checkIfAlreadyGetPromo($player_id, $promo['promoId']);
		}
        $data['registration_fields'] = $this->registration_setting->getRegistrationFields();
        $data['full_address_in_one_row'] = $this->operatorglobalsettings->getSettingJson('full_address_in_one_row');
        $data['profile_field_req'] = $this->registration_setting->player_profile_field_requirement();

        $data['rule'] = $rule = $this->utils->getConfig('player_validator');
        $data['contactRule'] = $contactRule = isset($rule['contact_number']) ? $rule['contact_number']  : "" ;

        $data['id_card_number_verified'] = $this->kyc_status_model->player_valid_documents($player_id);
		//search http request last row
		$this->load->model(array('http_request'));
		$data['lastLoginRequest'] = $this->http_request->getLastLoginRequest($player_id);
        $data['current_preferences'] = $this->communication_preference_model->getCurrentPreferences($player_id);
        $data['config_prefs'] = $this->utils->getConfig('communication_preferences');

		$this->loadTemplate(lang('Account Information'), '', '', 'settings');
		//add validate
		$this->appendFormValidationJs($this->template);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/player/view_player_settings', $data);
		$this->template->render();
	}

	/**
	 * Update single item of player profile, for mobile player center
	 * @param	string		$field	name of field, corresponding to columns of table playerdetails
	 * @param	(string)	$value	value of field
	 * @return	none
	 */
	public function ajax_player_profile_item($field = null, $value = null) {
		$player_id	= $this->authentication->getPlayerId();

		if(empty($player_id)){
			return show_error('Please login', 403);
		}

		$player		= $this->player_functions->getPlayerById($player_id);
		$now		= date("Y-m-d H:i:s");
		// Prevent malicious args
		$field 		= strval($field);
		$field 		= preg_replace('/\W/', '', $field);

		$fields_all = [
			'residentCountry', 'country', 'region', 'city', 'address', 'address2',
			'language', 'citizenship', 'dialing_code', 'contactNumber', 'birthplace',
			'imAccount', 'imAccountType', 'imAccount2', 'imAccountType2',
			'imAccount3', 'imAccountType3',
			'firstName', 'lastName', 'gender', 'birthdate', 'email' , 'zipcode', 'id_card_number', 'id_card_type'];

		// Default status/message
		$status = 'incomplete';
		$message = 'execution incomplete';

		try {
			// Check for field name (avoid malformed form)
			if (!in_array($field, $fields_all)) {
				throw new Exception(lang('error.default.message'), 1);
			}

			$fields_address = ['region', 'city', 'address', 'address2'];
			if(in_array($field, $fields_address)){
				$this->form_validation->set_rules('value', $field, 'trim|xss_clean|max_length[120]');
			} else {
				$this->form_validation->set_rules('value', $field, 'trim|xss_clean');
			}

			if ($this->form_validation->run() == false) {
				$this->utils->debug_log($this->form_validation->error_string());
				$message = lang('notify.22');
				throw new Exception($message, 2);
			}

			$value = $this->input->post('value');

			// Set language
			if ($field == 'language') {
				$this->language_function->setCurrentLanguage($this->language_function->langStrToInt($value));
			}

            $data = [];
			$disable_edit = false;
			if ($field == 'email' && $player[$field]) {
                $disable_edit = $player['verified_email'];
            }

            if ($field == 'contactNumber' && $player[$field]) {
                $disable_edit = ($this->utils->isEnabledFeature('enabled_show_player_obfuscated_phone') || $player['verified_phone']);
            }

            if (in_array($field, ['imAccount', 'imAccount2', 'imAccount3']) && $player[$field]) {
                $disable_edit = ($this->utils->isEnabledFeature('enabled_show_player_obfuscated_im'));
            }

            if ($this->registration_setting->checkAccountInfoFieldAllowEdit($player, $field, $disable_edit)) {
                $data = [$field => $value];
            }

			$modifiedFields = $this->checkModifiedFields($player_id, $data);
			$this->savePlayerUpdateLog($player_id, lang('lang.edit') . ' ' . lang('lang.playerinfo') . ' (' . $modifiedFields . ')', $this->authentication->getUsername());

			// Set email (note: email is in table player, not in playerdetails, thus updated separately)
			if ($data && isset($data['email'])) {
				$this->player_functions->editPlayerEmail([ 'email' => $value ], $player_id);
				throw new Exception(lang('success'), 0);
			}

			if ($data) {
                $this->player_functions->editPlayerDetails($data, $player_id);

                // Updated player.updatedOn
                $this->player_functions->editPlayer(['updatedOn' => $now], $player_id);
            }

			$this->load->model(['player_model']);
			//sync
			$username=$this->player_model->getUsernameById($player_id);
			$this->syncPlayerCurrentToMDBWithLock($player_id, $username, false);

			$status = 'success';
			$mesg = lang('notify.24');

		}
		catch (Exception $ex) {
			if ($ex->getCode() != 0) {
				$status = 'error';
				$mesg = "{$ex->getMessage()} ({$ex->getCode()})";
			}
			else {
				$status = 'success';
				$mesg = $ex->getMessage();
			}
		}
		finally {
			$result = [ 'status' => $status, 'mesg' => $mesg ];
			$this->returnJsonResult($result);
		}
	}

	/**
	 * Personal Information Update History
	 * @author kaiser.dapar 2015-09-07
	 *
	 * @param 	int
	 * @param 	string
	 * @param 	datetime
	 * @param 	string
	 * @return	array
	 */
	public function savePlayerUpdateLog($player_id, $changes, $updatedBy) {
		$this->player_functions->savePlayerChanges([
			'playerId' => $player_id,
			'changes' => $changes,
			'createdOn' => date('Y-m-d H:i:s'),
			'operator' => $updatedBy,
		]);
	}

	/**
	 * Check modified fields on player info
	 *
	 * @param 	int
	 * @param 	array
	 * @return	string
	 */
	public function checkModifiedFields($player_id, $new_data) {
		$old_data = $this->player_functions->getPlayerById($player_id);

		$diff = array_diff_assoc($new_data, $old_data);

		$changes = array();
		foreach ($diff as $key => $value) {
			$changes[lang('reg.fields.' . $key) ?: $key] = [
				'old' => $old_data[$key],
				'new' => $new_data[$key],
			];
		}

		ksort($changes);

		$output = '<ul>';
		foreach ($changes as $key => $value) {
			$output .= "<li>{$key}:<br><code>Old: {$value['old']}</code><br><code>New: {$value['new']}</code></li>";
		}
		$output .= '</ul>';

		return $output;
	}

	public function sidebar() {
		if ($this->authentication->isLoggedIn()) {
			$this->load->model(array('static_site', 'player_model', 'external_system',
				'wallet_model'));
			$player_id = $this->authentication->getPlayerId();
			$data['player'] = $this->player_functions->getPlayerById($player_id);
			$data['subwallet'] = $this->player_functions->getAllPlayerAccountByPlayerId($player_id);
			$data['totalBalance'] = $this->wallet_model->getTotalBalance($player_id);
			$data['game'] = $this->external_system->getAllActiveSytemGameApi();
			$this->load->view('/olm/sidebar.php', $data);
		}
	}

	public function mobile_transfer() {
		$player_id = $this->authentication->getPlayerId();

		$data = [];

		$this->loadTemplate(lang('Transfer'), '', '', 'wallet');

		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/view_cashier', $data);
		$this->template->render();
	}

	public function home($navigation = NULL) {
        if ($this->utils->is_mobile()){
            switch(TRUE){
                case ($this->utils->isEnabledFeature('use_new_player_center_mobile_version')):
                    $this->menu($navigation);
                    break;
                case ($this->utils->isEnabledFeature('use_lottery_center_home_on_the_mobile_version')):
                    $this->lottery_dashboard($navigation);
                    break;
                default:
                    $this->menu($navigation);
                    break;
            }
        }else{
            switch(TRUE){
                case ($this->utils->isEnabledFeature('use_lottery_center_home_on_the_desktop_version')):
                    //redirect('player_center/lottery_dashboard');
                    $this->lottery_dashboard($navigation);
                    break;
                default:
                    $this->dashboard($navigation);
                    break;
            }
        }
	}

    /**
     * view cashier
     *
     *
     * @return rendered template
     */
	public function dashboard($navigation = NULL){
        $output = FALSE;

        if (!$this->authentication->isLoggedIn()) {
            //load login
            return $this->goPlayerLogin();

        } else {
            $this->preloadSharedVars();

            //only for pc version
            if($this->utils->isEnabledFeature('always_auto_transfer_if_only_one_game') && !$this->utils->is_mobile() && (empty($navigation) || $navigation == 'cashier')){

                $this->preloadDepositVars();
                return redirect('/player_center2/deposit');
            }

            $this->utils->debug_log("Start loading dashboard");

            $player_id = $this->authentication->getPlayerId();
            $this->session->set_userdata('playerId', $player_id);

            # Base info
            $this->utils->debug_log("Start loading base info...");
            $this->utils->startEvent("Load base info");
            $data['player_center_template'] = $this->utils->getPlayerCenterTemplate(false);
            $data['isLoggedIn'] = true;
            $data['loggedPlayerId'] = $player_id;
            $data['site'] = $this->utils->getSystemUrl('www');
            if ($this->utils->is_mobile()) {
                $data['site'] = str_replace("www", "m", $data['site']);
            }
            $data['currency'] = $this->utils->getCurrentCurrency();
            $data['current_controller'] = 'player_center';

            $this->load->model(array('static_site', 'player_model', 'external_system',
                'wallet_model', 'promorules', 'transactions', 'total_player_game_hour',
                'player_promo', 'Playerbankdetails', 'point_transactions', 'communication_preference_model',
                'group_level', 'operatorglobalsettings', 'player_preference', 'player','daily_currency'));
            $this->utils->endEvent("Load base info");

            # Player Info
            $this->utils->debug_log("Start loading player info...");
            $this->utils->startEvent("Load player info");
            $data['player'] = $this->player_model->getPlayerInfoDetailById($player_id);
            $data['realname'] = $data['player']['lastName'] . $data['player']['firstName'];
            $this->utils->endEvent("Load player info");

            # Wallet Info
            $this->utils->debug_log("Start loading wallet info...");
            $this->utils->startEvent("Load wallet info");
            $data['big_wallet'] = $this->wallet_model->getOrderBigWallet($player_id);
            $data['pendingBalance'] = (object) ['frozen' => $data['big_wallet']['main']['frozen']];
            $data['totalBalance'] = $data['big_wallet']['total'];
            $subwallets = $data['big_wallet']['sub'];
            $data['subwallets'] = $subwallets;
            $data['walletinfo'] = array(
                'mainWallet' => $data['big_wallet']['main']['total_nofrozen'],
                'frozen' => $data['big_wallet']['main']['frozen'],
                'subwallets' => $subwallets
            );
            $data['bigWallet'] = $data['big_wallet'];
            $data['playerBalance'] = $this->utils->getTotalDepositWithdrawalBonusCashbackByPlayers($data['loggedPlayerId']);

	        $subwallet=null;
	        $success=$this->wallet_model->lockAndTransForPlayerBalance($player_id, function () use (
	            $player_id, &$subwallet) {

	            $subwallet = $this->wallet_model->getAllPlayerAccountByPlayerId($player_id);
	            return !empty($subwallet);
	        });
            $data['game'] = $this->external_system->getAllActiveSytemGameApi();
            $data['subwallet'] = $subwallet;

            $data['subwalletsBalance'] = array();
            foreach ($data['bigWallet']['sub'] as $apiId => $subWallet) {
                $data['subwalletsBalance'][$apiId] = $subWallet['total_nofrozen'];
            }
            $data['game_daily_currency_rate'] = array();
            $default_currency = $this->config->item('default_currency');
            $game_with_fixed_currency = $this->config->item('game_with_fixed_currency');
            if(!empty($game_with_fixed_currency)){
            	foreach ($game_with_fixed_currency as $key => $game_currency) {
            		$rate = $this->daily_currency->getCurrentCurrencyRateWithNoGenerate($this->utils->getTodayForMysql(), $default_currency, $game_currency);
            		if(!empty($rate)){
            			$data['game_daily_currency_rate'][$key] =   round($rate,2);
            		}
            	}
            }
            $data['total_subwallet_balance'] = array_sum($data['subwalletsBalance']);
            $data['total_main_wallet_balance'] = $data['bigWallet']['main']['total_nofrozen'];
            $data['total_frozen'] = $data['bigWallet']['total_frozen'];
            $data['total_no_frozen'] = $data['total_main_wallet_balance'] + $data['total_subwallet_balance'];
            $this->utils->endEvent("Load wallet info");

            # VIP info
            $this->utils->debug_log("Start loading VIP info...");
            $this->utils->startEvent("Load VIP info");
            $sort = "vipSettingId";
            $data['vipList'] = array();
            $vipList = $this->group_level->getVIPSettingList($sort, null, null);
            foreach (array_slice($vipList,1) as $list) {
                if (strtolower($list['status']) == 'inactive' || $list['can_be_self_join_in'] == 0) {
                    continue;
                }
                $vipLevel = $this->group_level->getVIPGroupRules($list['vipSettingId']);
                $data['vipList'][] = array(
                    "vipSettingId" 		=> $list['vipSettingId'],
                    "groupDescription" 	=> $list['groupDescription'],
                    "groupName" 		=> lang($list['groupName']),
                    "is_player_choose_vip"  => $list['can_be_self_join_in'],
                    "level" 			=> $vipLevel[0]['vipsettingcashbackruleId'],
                    "levelName"			=> $vipLevel[0]['vipLevelName'],
                    'image'             => $list['image'],
                );
            }

            //Get the status of VIP Show is done
            $data['is_vip_show_done'] = $data['player']['is_vip_show_done'];
            $data['operator_setting'] = json_decode($this->operatorglobalsettings->getSettingValue("vip_welcome_text"));
            $this->utils->endEvent("Load VIP info");

            # Registered popup modal
            $data['is_registered_popup_success_done'] = $data['player']['is_registered_popup_success_done'];

            # Tutorial
            $data['is_tutorial_done'] = $data['player']['is_tutorial_done'];

            # Misc
            $this->utils->debug_log("Start loading misc info...");
            $this->utils->startEvent("Load misc info");
            $this->utils->startEvent("playercenter_logo");
            $this->utils->endEvent("playercenter_logo");
            $this->utils->startEvent("currentLang");
            $data['currentLang'] = $this->language_function->getCurrentLanguage();
            $this->utils->endEvent("currentLang");
            $this->utils->startEvent("imageLoaderUrl");
            $data['imageLoaderUrl'] = $this->getImageLoader();
            $this->utils->endEvent("imageLoaderUrl");
            $data['search_list'] = 0;
            $this->utils->startEvent("profileProgress");
            $data['profileProgress'] = $this->getProfileProgress();
            $this->utils->endEvent("profileProgress");
            $this->utils->startEvent("fields");
            $data['fields'] = $this->getVisibleFielsForPlayer();
            $data['registration_fields'] = $this->registration_setting->getRegistrationFields();
            $data['full_address_in_one_row'] = $this->operatorglobalsettings->getSettingJson('full_address_in_one_row');
            $data['rule'] = $rule = $this->utils->getConfig('player_validator');
            $data['contactRule'] = $contactRule = isset($rule['contact_number']) ? $rule['contact_number']  : "" ;
            $this->utils->endEvent("fields");
            $this->utils->startEvent("playerCenterLanguage");
            $data['playerCenterLanguage'] = $this->language_function->getCurrentLanguage();
            $this->utils->endEvent("playerCenterLanguage");
            $data['isSecure'] = $this->utils->isEnabledFeature('add_security_on_deposit_transaction');
            $data['isWithdrawPassEnabled']= $this->utils->isEnabledFeature('enabled_withdrawal_password');
            $data['showObfuscatedPhone']= $this->utils->isEnabledFeature('enabled_show_player_obfuscated_phone');
            $data['showObfuscatedBankAcctNo'] = $this->utils->isEnabledFeature('enabled_show_player_obfuscated_bank_acctno');
            $data['show_tag_for_unavailable_deposit_accounts'] = (int) $this->system_feature->isEnabledFeature('show_tag_for_unavailable_deposit_accounts');
            $data['disable_account_transfer_when_balance_check_fails'] = (int) $this->system_feature->isEnabledFeature('disable_account_transfer_when_balance_check_fails');
            $data['enable_auto_transfer'] = $this->player_preference->isAutoTransferOnGameLaunch($player_id);
            $data['double_submit_hidden_field']=$this->initDoubleSubmitAndReturnHiddenField($player_id);
            $data['current_preferences'] = $this->communication_preference_model->getCurrentPreferences($player_id);
            $data['config_prefs'] = $this->utils->getConfig('communication_preferences');

            $this->utils->endEvent("Load misc info");

            if (!$this->utils->isEnabledFeature('hidden_avater_upload')) {
                $data['profilePicture'] = $this->setProfilePicture();
            }

            $pass_on_data = $this->session->flashdata('pass_on_data');
            if (!empty($pass_on_data)) {
                $data = $pass_on_data;
            }

            $data['playerBankDetails'] = $this->utils->getPlayerBankDetails($data['loggedPlayerId']);
            $data['isBankInfoAdded'] = !empty($data['playerBankDetails']['deposit']) || !empty($data['playerBankDetails']['withdrawal']);
            $this->utils->startEvent("Load templates");
            $this->loadTemplate(lang('cashier.01'), '', '', 'wallet');

            $this->template->add_js('resources/js/highlight.pack.js');
            $this->template->add_css('resources/css/hljs.tomorrow.css');
            $this->template->add_js('resources/js/json2.min.js');
            $this->template->add_js('resources/js/datatables.min.js');
            $this->template->add_css('resources/css/datatables.min.css');

	        if($this->authentication->isLoggedIn()) {
	            $this->load->model(['agency_model']);
	            $agency_agent = $this->agency_model->get_agent_by_binding_player_id($player_id);
	            $this->load->vars(['player_binding_agency_agent' => $agency_agent]);
	            $is_stable_center2_template=$this->operatorglobalsettings->getSettingValue('player_center_template', 'stable_center2')=='stable_center2';
	            $this->load->vars(['show_agency_menu_in_nav' => !empty($agency_agent) && $is_stable_center2_template]);
	        }else{
	            $this->load->vars(['player_binding_agency_agent' => null]);
	            $this->load->vars(['show_agency_menu_in_nav' => false]);
	        }

            $template = $this->utils->getPlayerCenterTemplate() . '/cashier/view_cashier';
            $this->template->write_view('main_content', $template, $data);
            $output = $this->template->render(NULL, TRUE);
            $this->utils->endEvent("Load templates");
        }

        $this->returnText($output);
    }

	public function mobile_transfer_view($navigation = NULL,$platform_id = GGPOKER_GAME_API){
        $output = FALSE;
        $this->utils->debug_log('GGPOKER_GAME_API - TRANSFER PLAYERID: ', $this->authentication->getPlayerId());
        $this->utils->debug_log('GGPOKER_GAME_API - CHECK LOGIN: ', $this->authentication->isLoggedIn());
        if (!$this->authentication->isLoggedIn()) {
            return $this->goPlayerLogin();
        }

        // Redirect to / if not mobile
       	if (!$this->utils->is_mobile()) {
       		$this->utils->debug_log(__METHOD__, 'Non-mobile access denied, redirecting to /');
       		redirect('/');
       	}

        $this->preloadSharedVars();

        // Set the dedicated subwallet sued
        $game_api_id = $platform_id;

        $active_games = $this->utils->getGameSystemMap();

        if (!isset($active_games[$game_api_id])) {
        	$this->utils->debug_log(__METHOD__, "Game API {$game_api_id} not found or disabled, responding error access");
       		$this->alertMessage(self::MESSAGE_TYPE_ERROR, 'Not available');
			$this->logoutPlayer();
            return;
        }

        $this->utils->debug_log("Start loading dashboard");

        $player_id = $this->authentication->getPlayerId();
        $this->session->set_userdata('playerId', $player_id);

        # Base info
        $this->utils->debug_log("Start loading base info...");
        $this->utils->startEvent("Load base info");
        $data['player_center_template'] = $this->utils->getPlayerCenterTemplate(false);
        $data['isLoggedIn'] = true;
        $data['loggedPlayerId'] = $player_id;
        $data['site'] = $this->utils->getSystemUrl('www');
        if ($this->utils->is_mobile()) {
            $data['site'] = str_replace("www", "m", $data['site']);
        }
        $data['currency'] = $this->utils->getCurrentCurrency();
        $data['current_controller'] = 'player_center';

        $this->load->model(array('static_site', 'player_model', 'external_system',
            'wallet_model', 'promorules', 'transactions', 'total_player_game_hour',
            'player_promo', 'Playerbankdetails', 'point_transactions',
            'group_level', 'operatorglobalsettings', 'player_preference', 'player','daily_currency'));
        $this->utils->endEvent("Load base info");

        # Player Info
        $this->utils->debug_log("Start loading player info...");
        $this->utils->startEvent("Load player info");
        $data['player'] = $this->player_model->getPlayerInfoDetailById($player_id);
        $data['realname'] = $data['player']['lastName'] . $data['player']['firstName'];
        $this->utils->endEvent("Load player info");

        $data['custom_sub_wallet_id'] = $game_api_id;

        if (!$this->utils->isEnabledFeature('hidden_avater_upload')) {
            $data['profilePicture'] = $this->setProfilePicture();
        }

        $pass_on_data = $this->session->flashdata('pass_on_data');
        if (!empty($pass_on_data)) {
            $data = $pass_on_data;
        }
        $this->utils->startEvent("Load templates");
        $this->loadTemplate(lang('cashier.01'), '', '', 'wallet');

        $template = $this->utils->getPlayerCenterTemplate() . '/cashier/mobile_transfer_view';
        $this->template->write_view('main_content', $template, $data);
        $output = $this->template->render(NULL, TRUE);
        $this->utils->endEvent("Load templates");

        $this->returnText($output);
    }

	public function embed(){
        if(!$this->authentication->isLoggedIn()){
            //load login
            return $this->goPlayerLogin();
        }
        $this->preloadSharedVars();
        $this->preloadDepositVars();

        $this->utils->debug_log('enter embed ========> ', $this->uri->segments);

	    $segments = $this->uri->segments;
	    $embed_segments = [];
	    $find = FALSE;
	    foreach($segments as $segment){
	        if($segment === __FUNCTION__){
                $find = TRUE;
                continue;
            }
            if($find){
                $embed_segments[] = $segment;
            }
        }

        $embed_type = array_shift($embed_segments);

        switch($embed_type){
            case 'agency';
            	$playerId=$this->authentication->getPlayerId();
            	$token = $this->common_token->getPlayerToken($playerId);
                $url = $this->utils->getSystemUrl('agency', implode('/', $embed_segments)).'?login_by_player_token='.$token;
                $this->utils->debug_log('agency url', $url);
                break;
            case 'player';
            default:
                $url = $this->utils->getSystemUrl('player', implode('/', $embed_segments));
		        $url .= (empty($_SERVER['QUERY_STRING'])) ? '' : '?' . $_SERVER['QUERY_STRING'];
		        break;
        }

	    $data['url'] = $url;
	    $data['content_template'] = 'default_iframe.php';

        $this->loadTemplate();
        $this->template->write_view('main_content', '/resources/common/embedded', $data);
        $this->template->render();
    }

	public function getPlayerTotalCashbackBalance($playerId) {
		$this->load->model('transactions');
		$balance = $this->transactions->sumCashback($playerId);
		return $balance;
	}

	public function money_transfer() {
		if (!$this->authentication->isLoggedIn()) {
			$this->goPlayerLogin();
		} else {
			$this->load->model(array('static_site', 'player_model', 'external_system',
				'wallet_model', 'promorules', 'transactions', 'total_player_game_hour', 'player_promo'));
			$site = $this->utils->getSystemUrl('www');
			$player_id = $playerId;

			$this->session->set_userdata('playerId', $player_id);
			$data['pendingBalance'] = $this->player_model->getPlayerPendingBalance($player_id);
			$data['player'] = $this->player_functions->getPlayerById($player_id);
			$data['subwallet'] = $this->player_functions->getAllPlayerAccountByPlayerId($player_id);
			$data['totalBalance'] = $this->wallet_model->getTotalBalance($player_id);
			$data['cashbackwallet'] = $this->player_functions->getPlayerCashbackWalletBalance($player_id);
			$data['imageLoaderUrl'] = $this->getImageLoader();
			$data['game'] = $this->external_system->getAllActiveSytemGameApi();

			$data['currentLang'] = $this->language_function->getCurrentLanguage();
			$data['search_list'] = 0;
			$data['site'] = $site;

			$data['big_wallet'] = $this->wallet_model->getBigWalletAddOldFormat($player_id);

			$data['ordered_big_wallet'] = $this->wallet_model->getOrderBigWallet($player_id);

			$data['apiMap'] = $this->utils->getGameSystemMap();
			$data['wallet_lang'] = $this->utils->getWalletLang();

			$exists = $this->player_promo->existsUnfinishedPromoAndDonotAllowOthers($player_id);
			$this->utils->debug_log('existsUnfinishedPromoAndDonotAllowOthers', $exists);
			$data['avail_promo_list'] = null;
			$data['available_rescue_promotion'] = false;
			$data['avail_promocms_list'] = $this->promorules->getAvailPromoOnDeposit($player_id);

			$this->utils->verbose_log('avail_promocms_list', $data['avail_promocms_list']);
			$this->loadTemplate(lang('cashier.01'), '', '', 'wallet');
			$this->template->add_js('resources/js/highlight.pack.js');
			$this->template->add_css('resources/css/hljs.tomorrow.css');
			$this->template->add_js('resources/js/json2.min.js');
			$this->template->add_js('resources/js/datatables.min.js');
			$this->template->add_css('resources/css/datatables.min.css');
			$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/money_transfer', $data);
			$this->template->render();
		}
	}

	public function getSubWalletBalance() {
		$player_id = $this->authentication->getPlayerId();
		echo json_encode($this->player_functions->getAllPlayerAccountByPlayerId($player_id));
	}

	const MAIN_WALLET_ID = 0;
	public function verifyMoneyTransfer() {
		if (!$this->authentication->isLoggedIn()) {
			$message = lang('notify.61'); //"transaction failed"
			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult(array('status' => 'error', 'msg' => $message));
				return;
			}
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

			$this->goPlayerLogin();
            return;
		}

		$player_id = $this->authentication->getPlayerId();
		$playerName = $this->player_model->getUsernameById($player_id);
		$is_transfer_all = $this->input->post('is_transfer_all');

		# validate double submit
		if(!$this->verifyAndResetDoubleSubmit($player_id)){
			$message = lang('Please refresh and try, and donot allow double submit');
			if ($this->input->is_ajax_request()) {
                $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                return;
            }

            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            if (isset($inputs['from-module']) && $inputs['from-module'] == 'sidebar') {
                redirect('player_center/sidebar');
            } else {
                $this->iframe_viewCashier();
                return;
            }

			return;
		}


		$this->form_validation->set_rules('transfer_from', 'Transfer From', 'trim|xss_clean|required');

		if($is_transfer_all == "false") {
			$this->form_validation->set_rules('amount', 'Amount', 'trim|xss_clean|required|numeric');
		}

        $this->form_validation->set_rules('transfer_to', 'Transfer To', 'trim|xss_clean|required');

		$inputs = $this->input->post();

		if ($this->form_validation->run() == false) {
			$message = lang('notify.61'); //"transaction failed"
			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult(array('status' => 'error', 'msg' => $message));
				return;
			}
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			if (isset($inputs['from-module']) && $inputs['from-module'] == 'sidebar') {
				redirect('player_center/sidebar');
			} else {
				$this->iframe_viewCashier();
			}
            return;
		}

        $transfer_from = $this->input->post('transfer_from');
        $transfer_to = $this->input->post('transfer_to');
        $amount = $this->input->post('amount');

        $playerInfoDetail = $this->player_model->getPlayerInfoDetailById($player_id);
        $this->utils->debug_log('playerInfoDetail : ' . var_export($playerInfoDetail, true));
        $totalBalanceAmount = $playerInfoDetail['totalBalanceAmount'];


        if ($this->input->post('directTransfer')) {
            $amount = $totalBalanceAmount;

            $this->utils->debug_log('amount : ' . $amount);

            if (empty($amount) || $amount == 0) {
                $message = lang('Do not have enough available balance');

                if ($this->input->is_ajax_request()) {
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                    return;
                }

                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
                return;
            }
        }

        $result = $this->utils->verifyWalletTransfer($player_id, $playerName, $transfer_from, $transfer_to, $amount);

        if($result['status'] === 'error'){
            if ($this->input->is_ajax_request()) {
                $this->returnJsonResult(array('status' => 'error', 'msg' => $result['message']));
                return;
            }

            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $result['message']);
            $this->goViewCashier();
            return;
        }

        $promo_cms_id = $this->input->post('promo_cms_id');
        if($promo_cms_id){
            $playerPromoId = $this->CI->process_player_promo($player_id, $promo_cms_id);

            if ($playerPromoId == '-1') {
                //ignore promotion
                $this->utils->debug_log('ignore trigger playerPromoId', $playerPromoId);
            } else if (!empty($playerPromoId)) {
                $this->utils->debug_log('trigger by playerPromoId', $playerPromoId);
                //force
                $promo_result = [];
                $this->triggerTransferPromotion($player_id, $amount, $transfer_from, $transfer_to, $result['transferTransId'], $promo_result, $playerPromoId);
                $this->utils->debug_log('result', $promo_result);
            } else {
                $this->utils->debug_log('no any trigger');
            }
        }

        if ($this->input->is_ajax_request()) {
            $this->returnJsonResult(array('status' => 'success', 'msg' => $result['message']));
            return;
        }else{
        	$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $result['message']);
            if (isset($inputs['redirect'])) {
                redirect($inputs['redirect']);
            } elseif (isset($inputs['from-module']) && $inputs['from-module'] == 'sidebar') {
                redirect('player_center/sidebar');
            } else {
                $this->goViewCashier();
            }
        }
	}

	/**
	 * iframe_report
	 *
	 *
	 * @return rendered template
	 */
	public function transactions($loadView = null) {
		$player_id = $this->authentication->getPlayerId();

		$this->session->set_userdata('playerId', $player_id);
		$data['search_list'] = 0;
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$this->loadTemplate('Report', '', '', 'wallet');
		if ($loadView == null) {
			$loadView = "view_report";
		}
		$search_list = $this->input->post('search_list');
		$from = $this->input->post('from');
		$to = $this->input->post('to');
		$data['search_list'] = $search_list;
		$search = array(
			'from' => $from,
			'to' => $to,
		);
		$data['search'] = $search;
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/' . $loadView, $data);
		$this->template->render();
	}

	/**
	 * friend list
	 *
	 *
	 * @return rendered template
	 */
	public function friends() {
		$player_id = $this->authentication->getPlayerId();
		$this->session->set_userdata('playerId', $player_id);
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$data['friends'] = $this->player_functions->getReferralByPlayerId($player_id);
		$this->loadTemplate('Friends List', '', '', 'wallet');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/player/friends', $data);
		$this->template->render();
	}

	/**
	 * useless
	 */
	public function depositFromPlayerAccount($player_account_to, $amount, $new_balance) {
		return false;
	}

	/**
	 * useless
	 */
	public function withdrawFromPlayerAccount($player_account_from, $amount, $new_balance) {
		return false;
	}

	public function checkTransfer() {
		$transfer_from = $this->input->post('transfer_from');
		$transfer_to = $this->input->post('transfer_to');

		if ($transfer_from == $transfer_to) {
			$message = lang('notify.52');
			$this->alertMessage(1, $message);
			return false;
		} else if ($transfer_from != 0 && $transfer_to != 0) {
			$message = lang('notify.53');
			$this->alertMessage(1, $message);
			$this->form_validation->set_message('checkTransfer', '');
			return false;
		}

		return true;
	}

	public function checkAmount() {
		$transfer_from = $this->input->post('transfer_from');
		$amount = $this->input->post('amount');
		$player_id = $this->authentication->getPlayerId();

		$player_account = $this->player_functions->getPlayerAccountBySubWallet($player_id, $transfer_from);

		$this->utils->debug_log('transfer_from', $transfer_from, 'amount', $amount, 'player_id', $player_id, 'player_account', $player_account);
		if ($this->utils->compareResultCurrency($amount, '>', $player_account['totalBalanceAmount'])) {
			$message = lang('notify.54');
			$this->alertMessage(1, $message);
			$this->form_validation->set_message('checkAmount', '');
			return false;
		}

		return true;
	}

	/**
	 * view search cashier
	 *
	 *
	 * @return rendered template
	 */
	public function searchCashier($loadView = null, $hashtag = null) {
		$player_id = $this->authentication->getPlayerId();

		$this->form_validation->set_rules('from', 'From date', 'trim|xss_clean');
		$this->form_validation->set_rules('to', 'To date', 'trim|xss_clean');

		if (!empty($this->input->post('from')) && !empty($this->input->post('to'))) {
			$this->form_validation->set_rules('search_list', 'Search List', 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('search_list', 'Search List', 'trim|xss_clean');
		}

		if ($this->form_validation->run() == false) {
			$message = "Please fill the fields with valid inputs";
			$this->alertMessage(2, $message);
			$this->iframe_viewCashier();
		} else {
			$from = $this->input->post('from');
			$to = $this->input->post('to');
			$search_list = $this->input->post('search_list');

			if ($from > $to) {
				$message = "'From date' should be less than 'To date'";
				$this->alertMessage(2, $message);
				$this->goViewCashier();
			} else {
				$data = array();
				$search = array(
					'from' => $from,
					'to' => $to,
				);
				$data['search'] = $search;

				$this->session->set_userdata('search_from', $from);
				$this->session->set_userdata('search_to', $to);

				$data['player'] = $this->player_functions->getPlayerById($player_id);
				$data['subwallet'] = $this->player_functions->getAllPlayerAccountByPlayerId($player_id);
				$data['totalBalance'] = $this->player_functions->getPlayerTotalBalance($player_id)['totalBalance'];
				$data['cashbackwallet'] = $this->player_functions->getPlayerCashbackWalletBalance($player_id);
				$data['game'] = $this->player_functions->getGames();
				$data['search_list'] = $search_list;
				$data['currentLang'] = $this->language_function->getCurrentLanguage();
				$this->loadTemplate(lang('cashier.20'), '', '', 'wallet');
				if ($loadView == null) {
					$loadView = "view_report";
				}

				if (!empty($hashtag)) {
					$this->pass_data_on($data);
					redirect('/player_center/dashboard/index#' . $hashtag);
					return;
				}

				$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/' . $loadView, $data);
				$this->template->render();
			}
		}
	}

	protected function pass_data_on($data) {
		$flash_data = $this->session->flashdata('pass_on_data');
		$merged_data = $data;
		if (!empty($merge_data) && is_array($merge_data)) {
			$merged_data = array_merge($flash_data, $data);
		}

		$this->session->set_flashdata('pass_on_data', $merged_data);
	}

	public function withdrawalDepositHistory($segment, $from = null, $to = null) {
		$player_id = $this->authentication->getPlayerId();

		$this->load->model(array('player_model'));
		$search = null;
		if ($from && $to) {
			$search = array(
				'from' => urldecode($from),
				'to' => urldecode($to),
			);
		}

		$data['count_all'] = $this->player_model->getAllDepositsWLimit($player_id, null, null, $search, true);
		$config['base_url'] = "javascript:withdrawalDepositHistory(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '5';
		$config['num_links'] = '1';

		$config['full_tag_open'] = '<ul class="pagination">';
		$config['full_tag_close'] = '</ul>';
		$config['first_link'] = false;
		$config['last_link'] = false;
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['prev_link'] = '&laquo';
		$config['prev_tag_open'] = '<li class="prev">';
		$config['prev_tag_close'] = '</li>';
		$config['next_link'] = '&raquo';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$this->pagination->initialize($config);

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['deposits'] = $this->player_model->getAllDepositsWLimit($player_id, $config['per_page'], $segment, $search);
		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/member_deposit_history', $data);
	}

	public function friendReferralBonustHistory($from, $to) {
		$this->load->model(array('player_model'));
		$search = array(
			'from' => urldecode($from),
			'to' => urldecode($to),
		);
		$player_id = $this->authentication->getPlayerId();
		$data['friendReferralBonus'] = $this->player_model->getFriendReferralBonus($player_id, $search['from'], $search['to']);
		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_friendreferral_history', $data);
	}


	public function balanceAdjustmentHistory($segment, $from, $to) {
		$player_id = $this->authentication->getPlayerId();

		$search = array(
			'from' => urldecode($from),
			'to' => urldecode($to),
		);

		$this->load->model(array('transactions'));
		$transHistoryAll = $this->transactions->getPlayerAdjustmentHistoryWLimit($player_id, null, null, $search);

		$data['count_all'] = count($transHistoryAll);
		$config['base_url'] = "javascript:balanceAdjustmentHistory(";
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
		$config['next_link'] = lang('Next Page');
		$config['prev_link'] = lang('Prev Page');
		$config['last_link'] = lang('Last Page');
		$config['first_link'] = lang('First Page');

		$this->pagination->initialize($config);

		$transHistory = $this->transactions->getPlayerAdjustmentHistoryWLimit($player_id, $config['per_page'], $segment, $search);

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['balanceAdjustmentHistory'] = $transHistory;

		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_player_transactions', $data);
	}

	public function friendreferraldailyreport($segment = 0) {
		$player_id = $this->authentication->getPlayerId();

		$data['search_list'] = 8;

		$data['player_id'] = $player_id;

		$input = $this->input->post();

		$now = new DateTime();

		$data['search'] = array(
			'from' => isset($input['from']) ? $input['from'] : $now->format('Y-m-d 00:00:00'),
			'to' => isset($input['to']) ? $input['to'] : $now->format('Y-m-d 23:59:59'),
		);

		$this->load->model(array('player_earning'));

		$this->loadTemplate('Player Center', '', '', '');

		$this->template->add_js('resources/js/datatables.min.js');
		$this->template->add_js('resources/datatables/Buttons-1.1.0/js/buttons.html5.min.js');
		$this->template->add_js('resources/datatables/Buttons-1.1.0/js/buttons.flash.min.js');

		$this->template->add_css('resources/css/datatables.min.css');

		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/view_friend_referral_daily_report', $data);
		$this->template->render();
	}

	const MANUAL = MANUAL_ONLINE_PAYMENT;
	const AUTO = AUTO_ONLINE_PAYMENT;
	const LOCAL = LOCAL_BANK_OFFLINE;
	const CARD = 4;
	const MIN_ORDER = 9999;

	const WITHDRAWAL_ENABLED = 1;
	const WITHDRAWAL_DISABLED = 0;

	private $is_unique_ticketNumber = false;

	/**
	 * iframe_home
	 *
	 * @param   string
	 * @return  void
	 */
	public function iframe_home() {
		$this->goPlayerHome();
	}

	public function player_center_logout() {
        $result = $this->authentication->logout();

        if ($this->input->is_ajax_request()) {
            return;
        }

        redirect($result['redirect_url']);
	}

	public function playerGameHistory() {
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$this->load->model(array('external_system', 'game_logs'));

		$this->loadTemplate('Game History', '', '', '');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/player/game_history', $data);
		$this->template->render();
	}

	public function dtPlayerGameHistory() {
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$this->load->model(array('external_system', 'game_logs'));
		$data['game_platforms'] = $this->external_system->getAllActiveSytemGameApi();
		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/datatables/player_game_history', $data);
		$this->template->render();
	}

	public function dtTransfersHistory() {
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/datatables/player_transfer_history', $data);
		$this->template->render();
	}

	public function dtPlayerMoneyTransfer() {
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/datatables/player_money_transfer', $data);
		$this->template->render();
	}

	public function dtPlayerDeposits() {
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/datatables/player_deposit', $data);
		$this->template->render();
	}

	public function dtPlayerWithdrawalsfromWForm() {
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/datatables/player_withdrawal2', $data);
		$this->template->render();
	}

	public function dtPlayerWithdrawals() {
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/datatables/player_withdrawal', $data);
		$this->template->render();
	}

	public function dtPlayerCashbacks() {
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/datatables/player_cashback', $data);
		$this->template->render();
	}

	public function dtPlayerTransaction() {
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/datatables/player_transaction', $data);
		$this->template->render();
	}

	public function dtPlayerPromoHistory() {
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/datatables/player_promo_history', $data);
		$this->template->render();
	}

	public function dtPlayerBanksWithdrawalList() {
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/datatables/player_banks_withdrawal_list', $data);
		$this->template->render();
	}

	public function dtPlayerBanksDepositList() {
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/datatables/player_banks_deposit_list', $data);
		$this->template->render();
	}

	public function dtPlayerChatMessages() {
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/datatables/player_chat_messages', $data);
		$this->template->render();
	}

	public function dtPlayerActivePromotions() {
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/datatables/player_active_promotions', $data);
		$this->template->render();
	}

	public function dtPlayerAvailPromotions() {
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/datatables/player_avail_promotions', $data);
		$this->template->render();
	}

	public function dtPlayerEligiblePromotions() {
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/datatables/player_eligible_promotions', $data);
		$this->template->render();
	}

	public function dtPlayerMessageDetails($message_id) {
		$this->load->model(array('internal_message'));
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$data['chat_id'] = $message_id;
		$data['current_message_id'] = $message_id;
		$data['chat_status'] = $this->internal_message->getMessagesStatusByMessageId($message_id);
		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/datatables/player_message_details', $data);
		$this->template->render();
	}

	public function dtPlayerSendMessage() {
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/datatables/player_send_message', $data);
		$this->template->render();
	}

	public function dtPlayerAddEditBankDetails($dw_bank = '', $bank_details_id = '') {
		//$data['playerId']= $this->authentication->getPlayerId();

		$data['dw_bank'] = $dw_bank;
		$data['bank'] = $this->player_functions->getBankDetailsById($bank_details_id);
		$data['banks'] = $this->player_functions->getAllBankType();
		$data['bank_details_id'] = $bank_details_id;
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/player/add_edit_bank', $data);
		$this->template->render();
	}

	public function dtPlayerAddEditWithdrawalBankDetails($dw_bank = '', $bank_details_id = '') {

		$data['dw_bank'] = $dw_bank;
		$data['bank'] = $this->player_functions->getBankDetailsById($bank_details_id);
		$data['banks'] = $this->player_functions->getAllBankType();
		$data['bank_details_id'] = $bank_details_id;
		$playerId = $this->authentication->getPlayerId();
		$this->load->model('player_model');
		$player = $this->player_model->getPlayerDetailsById($playerId);
		$player = get_object_vars($player);
		$data['realname'] = $player['lastName'] . $player['firstName'];
		$data['currentLang'] = $this->language_function->getCurrentLanguage();

		$props = array('template' => 'template/' . $this->utils->getPlayerCenterTemplate() . '_datatable_template');
		$this->template->add_template('datatable', $props, TRUE);
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/player/add_edit_withdrawal_bank', $data);
		$this->template->render();
	}

	const TEMPORARY_DEPOSIT_FOLDER_NAME = 'deposit_temp';
	const TEMP_PLAYER_UPLOAD_FOLDER_NAME = 'temp_player_upload_';
	const UPLOADED_DEPOSIT_SLIPS_FOLDER = 'deposit_slips';

	public function uploadDepositSlip() {

		$uploader = $this->utils->loadSimpleAjaxUploader();
		$uploader->sizeLimit = 1000000;
		$uploader->allowedExtensions = array('jpg', 'png', 'jpeg');
		$playerId = $this->authentication->getPlayerId();
		$imageExt = $uploader->getExtension();
		$preferredImageName = 'player_deposit_' . $playerId . '_' . time() . '.' . $imageExt;
		$this->session->set_userdata('player_deposit_image', $preferredImageName);
		$uploader->newFileName = $preferredImageName;
		$upload_dir = $this->utils->getUploadPath();
		#TEMPORARY PLAYERS FOLDER UPLOAD
		$depositTemp = self::TEMPORARY_DEPOSIT_FOLDER_NAME;
		#CREATE PLAYER OWN TEMPORARY UPLOAD FOLDER
		$dirname = self::TEMP_PLAYER_UPLOAD_FOLDER_NAME . $playerId;

		#CREATE TEMPORARY FOLDER FOR ALL PLAYER
		if (!file_exists($upload_dir . $depositTemp)) {
			mkdir($upload_dir . $depositTemp, 0777);
		}
		#CREATE TEMPORARY FOLDER FOR PLAYER
		if (!file_exists($upload_dir . $depositTemp . '/' . $dirname)) {
			mkdir($upload_dir . $depositTemp . '/' . $dirname, 0777);
		}

		$result = $uploader->handleUpload($upload_dir . $depositTemp . '/' . $dirname);

		if (!$result) {
			$this->returnJsonResult(array('success' => false, 'msg' => $uploader->getErrorMsg()));
		} else {
			$this->returnJsonResult(array('success' => true, 'imageName' => $preferredImageName, 'image_path' => site_url() . 'upload/' . $depositTemp . '/' . $dirname . '/'));
		}
	}

	public function removeImage($image_name) {
		$this->session->unset_userdata('player_deposit_image');
		$playerId = $this->authentication->getPlayerId();
		$upload_dir = $this->utils->getUploadPath();
		$depositTemp = self::TEMPORARY_DEPOSIT_FOLDER_NAME;
		$dirname = self::TEMP_PLAYER_UPLOAD_FOLDER_NAME . $playerId;
		$imageFile = $upload_dir . $depositTemp . '/' . $dirname . '/' . $image_name;
		if (!unlink($imageFile)) {
			$this->returnJsonResult(array('success' => false, 'msg' => lang('Unable to delete file.')));
		} else {
			$this->returnJsonResult(array('success' => true, 'msg' => lang('Deleted')));
		}
	}

	public function transferBankslipImage() {
		$playerId = $this->authentication->getPlayerId();
		$upload_dir = $this->utils->getUploadPath();
		$depositTemp = self::TEMPORARY_DEPOSIT_FOLDER_NAME;
		$dirname = self::TEMP_PLAYER_UPLOAD_FOLDER_NAME . $playerId;

		$currentUploadImg = $this->session->userdata('player_deposit_image');

		if ($this->input->post('bank_slip')) {
			$tempImgPath = $upload_dir . $depositTemp . '/' . $dirname . '/' . $currentUploadImg;
			$depositSlipDir = self::UPLOADED_DEPOSIT_SLIPS_FOLDER;
            $this->utils->addSuffixOnMDB($depositSlipDir);

			if (!file_exists($upload_dir . '/' . $depositSlipDir)) {
				mkdir($upload_dir . '/' . $depositSlipDir, 0777, true);
			}

			$this->session->unset_userdata('player_deposit_image');

			if (!copy($tempImgPath, $upload_dir . $depositSlipDir . '/' . $currentUploadImg)) {
				//throw new Exception(lang('Image not transferred successfully'));
				$this->utils->debug_log($playerId . 'Deposit Image not transferred successfully');
			}
		}
	}

	public function emptyPlayerTempUploadFolder() {
		$playerId = $this->authentication->getPlayerId();
		$upload_dir = $this->utils->getUploadPath();
		$depositTemp = self::TEMPORARY_DEPOSIT_FOLDER_NAME;
		$dirname = self::TEMP_PLAYER_UPLOAD_FOLDER_NAME . $playerId;
		//echo $upload_dir.$depositTemp.'/'.$dirname;exit;
		foreach (glob($upload_dir . $depositTemp . '/' . $dirname . "/*.*") as $filename) {
			if (is_file($filename)) {
				unlink($filename);
			}
		}
		if (is_dir($upload_dir . $depositTemp . '/' . $dirname)) {
			rmdir($upload_dir . $depositTemp . '/' . $dirname);
		}
	}

	public function setPromoResultApplicationSession() {

		$status = $this->input->post('status');
		$msg = $this->input->post('msg');
		$this->session->set_userdata('currPromoApplyStatus', $status);
		$this->session->set_userdata('currPromoApplyMessage', $msg);
		$this->returnJsonResult(array('success' => true, 'msg' => 'ok'));
	}

	public function unsetCurrentApplyPromoSession() {

		$this->session->unset_userdata('currPromoApplyStatus');
		$this->session->unset_userdata('currPromoApplyMessage');
		$this->returnJsonResult(array('success' => true, 'msg' => 'ok'));
	}

	public function loadOneworksSportsbook() {
		$data['playerId'] = $this->authentication->getPlayerId();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$data['isLogged'] = $this->authentication->isLoggedIn();
		$data['playsite'] = $this->utils->getSystemUrl('www');
		$data['live_chat_used'] = $this->utils->getCurrentLiveChatUsed();
		$this->load->view('webet/sportsbook/oneworks_sportsbook', $data);
	}

	public function logoutPlayer() {
        $result = $this->authentication->logout();

        if ($this->input->is_ajax_request()) {
            return;
        }

        redirect($result['redirect_url']);
	}

	public function loadDesktopGame($gamePlatform, $playerName) {
		$data['ticket'] = $this->session->userdata('gamesos_ticket');
		$data['playerName'] = $playerName;
		#logout the user the ticket will only be used/ so they will load the login again

        $result = $this->authentication->logout();

		$this->load->view($this->utils->getPlayerCenterTemplate() . '/desktop_games/gamesos_desktop_view', $data);
	}

	public function desktop_contact_us($gamePlatform, $playerName) {
		$data['playerName'] = $playerName;
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$this->load->view($this->utils->getPlayerCenterTemplate() . '/desktop_games/desktop_contact_us', $data);
	}

	public function desktop_minicashier($gamePlatform, $playerName) {

		$playerId = $this->authentication->getPlayerId();
		$api = $this->utils->loadExternalSystemLibObject($gamePlatform);

		#just in case session is expired
		if (!$playerId) {
			$this->load->model(array('player'));
			$player = $this->player->getPlayerByUsername($playerName);
			$data['desktop_token'] = $api->getPlayerToken($player->playerId);
		} else {
			$data['desktop_token'] = $api->getPlayerToken($playerId);
		}

		$data['playerName'] = $playerName;
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$this->load->view($this->utils->getPlayerCenterTemplate() . '/desktop_games/desktop_minicashier_view', $data);
	}

	public function transferResult($type) {

		$playerId = $this->authentication->getPlayerId();
		$data['player'] = $this->player_model->getPlayerInfoDetailById($playerId);

		if ($type == "failed") {

			if ($this->input->is_ajax_request()) {
				$data['message'] = $this->input->post('message');
				$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/deposit_failed', $data);
				return;
			}

			$this->loadTemplate(lang('Transfer Failed'), '', '', '');
			$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/transfer_failed', $data);
			$this->template->render();

		} else {

			if ($this->input->is_ajax_request()) {
				$data['message'] = $this->input->post('message');
				$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/deposit_success', $data);
				return;
			}

			$this->loadTemplate(lang('Transfer Success'), '', '', '');
			$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/transfer_success', $data);
			$this->template->render();
		}
	}

	public function viewTransactions() {

		$player_id = $this->authentication->getPlayerId();
		$data['player'] = $this->player_model->getPlayerInfoDetailById($player_id);

		$this->load->model(array('external_system'));

		$data['game_provider'] = $this->external_system->getAllActiveSytemGameApi();

		$this->loadTemplate(lang('Transactions'), '', '', '');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/transactions', $data);
		$this->template->render();

	}

	/**
	 * show reports by report type
	 *
	 * @date 4/23/2017
	 */
	public function viewReports() {
		$report_type = $this->input->get('report_type');
		$allow_report_types = ['rebate','deposit', 'withdrawal', 'transfer', 'game', 'deposit_list', 'promoHistory', 'cashback_request', 'transfer_request', 'cashback_all_request', 'point','referralFriend'];

		list($transaction_type_value_mappings, $transaction_type_url_mappings, $report_name_mappings) = $this->_getTransactionMapping();

		if (in_array($report_type, $allow_report_types)) {
			$data['report_type'] = $report_type;
		} else {
			$data['report_type'] = 'deposit';
		}

		$data['transaction_type'] = $transaction_type_value_mappings[$data['report_type']];
		$data['target_url'] = site_url($transaction_type_url_mappings[$data['report_type']]);
		$data['api_url'] = site_url($transaction_type_url_mappings[$data['report_type']]);
		$data['report_title'] = $report_name_mappings[$data['report_type']];

		$player_id = $this->authentication->getPlayerId();
		$data['player'] = $this->player_model->getPlayerInfoDetailById($player_id);

		$this->load->model(array('external_system'));

		$data['game_provider'] = $this->external_system->getAllActiveSytemGameApi();
		$data['current_lang'] = $this->language_function->getCurrentLanguage();

		$this->loadTemplate(lang('Transactions'), '', '', '');

		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/reports', $data);
		$this->template->render();
	}

	public function viewTransferRequest($status) {
		$this->load->model(['wallet_model']);

		$data = [];
		switch ((int) $status) {
		case Wallet_model::STATUS_TRANSFER_SUCCESS:
			$data['status'] = Wallet_model::STATUS_TRANSFER_SUCCESS;
			break;
		case Wallet_model::STATUS_TRANSFER_FAILED:
			$data['status'] = Wallet_model::STATUS_TRANSFER_FAILED;
			break;
		case Wallet_model::STATUS_TRANSFER_REQUEST:
		default:
			$data['status'] = Wallet_model::STATUS_TRANSFER_REQUEST;
			break;
		}

		$data['base_url'] = '/player_center/viewTransferRequest';
		$data['api_url'] = '/api/player_transfer_request';

		$this->loadTemplate(lang('Transfer Request History'), '', '', '');

		$this->template->add_js('resources/js/datatables.min.js');
		$this->template->add_js('resources/datatables/Buttons-1.1.0/js/buttons.html5.min.js');
		$this->template->add_js('resources/datatables/Buttons-1.1.0/js/buttons.flash.min.js');

		$this->template->add_css('resources/css/datatables.min.css');

		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/view_player_transfer_request', $data);
		$this->template->render();
	}

	/**
	 * Get transaction type value mapping and transaction type url mapping for function viewReports use
	 *
	 * @date 4/23/2017
	 * @return array
	 */
	protected function _getTransactionMapping() {
		$transaction_type_value_mappings = [
			'rebate' => '',
			'deposit' => Transactions::DEPOSIT,
			'withdrawal' => Transactions::WITHDRAWAL,
			'transfer' => Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET . ',' . Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET,
			'game' => '',
			'deposit_list' => '',
			'promoHistory' => '',
			'cashback_request' => '',
			'transfer_request' => '',
			'cashback_all_request' => '',
			'point' => '',
			'referralFriend' =>'',
		];

		$transaction_type_url_mappings = [
			'rebate' => '/api/getRebateTransaction',
			'deposit' => 'api/DepositWalletTransaction',
			'withdrawal' => 'api/WithdrawWalletTransaction',
			'transfer' => 'api/transactions',
            'game' => '/ajax/account_history/player_games_history',
			'deposit_list' => 'api/getDepositsList',
			'promoHistory' => 'api/getPlayerPromoHistoryWLimitById',
			'cashback_request' => 'api/getCashbackRequestRecords',
			'transfer_request' => 'api/TransferWalletTransaction',
			'cashback_all_request' => 'iframe_module/allCashbackHistory',
			'point' => 'iframe_module/pointsHistory',
			'referralFriend' =>'api/getReferralFriend',
		];

		$report_name_mappings = [
			'rebate' => lang('player.ui45'),
			'deposit' => lang("cashier.24"),
			'withdrawal' => lang('cashier.31'),
			'transfer' => lang('pay.transactions'),
			'game' => lang('xpj.gamehistory'),
			'deposit_list' => 'DepositsList',
			'promoHistory' => lang('cashier.116'),
			'cashback_request' => lang('xpj.cashback'),
			'transfer_request' => lang('Transfer Request History'),
			'cashback_all_request' => lang('Cashback Report'),
			'point' => lang('Points'),
			'referralFriend' =>lang('player.friendReferralStatus'),
		];

		return [
			$transaction_type_value_mappings,
			$transaction_type_url_mappings,
			$report_name_mappings,
		];
	}

	public function deposit() {
		$this->load->model(['payment_account', 'playerbankdetails', 'banktype','sale_order']);

        if($this->utils->isEnabledFeature('enable_deposit_category_view')) {
            redirect('/player_center2/deposit/deposit_category');
        }

		$player_id = $this->authentication->getPlayerId();
		$data['player'] = $this->player_model->getPlayerInfoDetailById($player_id);

        if($this->utils->getPlayerStatus($player_id)==5){
                  redirect('/player_center/menu');

        }

		$depositRule = $this->group_level->getPlayerDepositRule($player_id);
		$data['depositRule'] = $depositRule;

		$depositRuleMinDeposit = isset($depositRule[0]['minDeposit']) ? $depositRule[0]['minDeposit'] : 0;  # TODO: REMOVE INDEX 0
		$depositRuleMaxDeposit = isset($depositRule[0]['maxDeposit']) ? $depositRule[0]['maxDeposit'] : $this->utils->getConfig('defaultMaxDepositDaily');  # TODO: REMOVE INDEX 0

		$data['depositRuleMinDeposit'] = $depositRuleMinDeposit;
		$data['depositRuleMaxDeposit'] = $depositRuleMaxDeposit;

		$depositMenuList = $this->banktype->getSpecialPaymentTypeList();

		$data['depositMenuList'] = $depositMenuList;

        $payment_all_accounts = $this->payment_account->getAvailableDefaultCollectionAccount($player_id);

        $payment_manual_accounts = ($payment_all_accounts[MANUAL_ONLINE_PAYMENT]['enabled']) ? $payment_all_accounts[MANUAL_ONLINE_PAYMENT]['list'] : [];
        if($payment_all_accounts[LOCAL_BANK_OFFLINE]['enabled']){
            foreach($payment_all_accounts[LOCAL_BANK_OFFLINE]['list'] as $payment_account){
                $payment_manual_accounts[] = $payment_account;
            }
        }
        $payment_auto_accounts = ($payment_all_accounts[AUTO_ONLINE_PAYMENT]['enabled']) ? $payment_all_accounts[AUTO_ONLINE_PAYMENT]['list'] : [];

        $payment_accounts = [];
        foreach($payment_manual_accounts as $payment_account){
            $payment_accounts[] = $payment_account;
        }
        foreach($payment_auto_accounts as $payment_account){
            $payment_accounts[] = $payment_account;
        }


		$data['payment_accounts'] = $payment_accounts;
		$data['payment_auto_accounts'] = $payment_auto_accounts;
		$data['payment_manual_accounts'] = $payment_manual_accounts;

        if( $this->utils->isEnabledFeature('enable_manual_deposit_realname')){
            $this->load->model(['player_model']);
            $playerDetailObject =  $this->player_model->getPlayerDetailsById($player_id);
            $firstName = $playerDetailObject->firstName;
            if (strlen($firstName)<=0){
                $data['firstNameflg']=0;
            }else{
                $data['firstNameflg']=1;
            }
            $data['firstName']=$firstName;
        }


        $this->utils->debug_log('depositRule MinDeposit MaxDeposit', $data['depositRuleMinDeposit'], $data['depositRuleMaxDeposit']);
		$this->loadTemplate(lang('Deposit'), '', '', '');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/deposit_form', $data);
		$this->template->render();
	}

	public function getDepositForm($flag = '', $bankTypeId = '') {
		$data = $this->auto_payment($flag, $bankTypeId);
		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_deposit_content', $data);
	}

	/**
	 * mobile menu
	 *
	 * @return player center main menu
	 */
	public function menu() {
		if (!$this->authentication->isLoggedIn()) {
            redirect($this->utils->getPlayerLoginUrl());
			return;
		}

        $player_id = $this->authentication->getPlayerId();

        $data['player'] = $this->player_model->getPlayerInfoDetailById($player_id);
		$data['big_wallet_simple'] = $this->utils->getSimpleBigWallet($player_id);

        $data['big_wallet'] = $this->wallet_model->getOrderBigWallet($player_id);
        $subwallets = $data['big_wallet']['sub'];
        $data['subwallets'] = $subwallets;
        $data['subwallet'] = $data['subwallets'];

        $bigWallet = $data['big_wallet'];
        $subwalletsBalance = array();
        foreach ($bigWallet['sub'] as $apiId => $subWallet) {
            $subwalletsBalance[$apiId] = $subWallet['total_nofrozen'];
        }
        $total_subwallet_balance = array_sum($subwalletsBalance);
        $total_main_wallet_balance = $bigWallet['main']['total_nofrozen'];
        $data['total_no_frozen'] = $total_main_wallet_balance + $total_subwallet_balance;


        $data['profilePicture'] = $this->setProfilePicture();

		# Registered popup modal
		$data['is_registered_popup_success_done'] = $data['player']['is_registered_popup_success_done'];

        $data['player_today_total_betting_amount'] = 0;
        if ($this->utils->isEnabledFeature('display_total_bet_amount_in_overview')){
            $this->load->model(array('game_logs'));

            $overview_config = $this->utils->getConfig('overview');

            $player_today_total_betting_platforms = (isset($overview_config['today_total_betting_platforms']) && !empty($overview_config['today_total_betting_platforms'])) ? $overview_config['today_total_betting_platforms'] : null;
            $data['player_today_total_betting_amount'] = $this->utils->formatCurrency($this->game_logs->getPlayerCurrentBetByPlatform($player_id, date("Y-m-d 00:00:00"), null, null, $player_today_total_betting_platforms));
        }

        if ($this->utils->is_mobile()) {
			$this->loadTemplate(lang('cashier.01'), '', '', 'menu');
			$this->template->set_homepage();
			$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/view_menu', $data);
			$this->template->render();
		} else {
			$this->dashboard();
		}
	}

	public function lottery_dashboard($navigation = NULL){
        if (!$this->authentication->isLoggedIn()) {
            return $this->goPlayerLogin();
        }
        $this->preloadSharedVars();

        $player_id = $this->authentication->getPlayerId();

        $data['content_template'] = 'default_iframe.php';
        $data['playerId'] = $player_id;
        $data['player'] = $this->player_model->getPlayerInfoDetailById($player_id);
        $data['big_wallet'] = $this->wallet_model->getOrderBigWallet($player_id);
        $subwallets = $data['big_wallet']['sub'];
        $data['subwallets'] = $subwallets;
        $data['subwallet'] = $data['subwallets'];

        $bigWallet = $data['big_wallet'];
        $subwalletsBalance = array();
        foreach ($bigWallet['sub'] as $apiId => $subWallet) {
            $subwalletsBalance[$apiId] = $subWallet['total_nofrozen'];
        }
        $total_subwallet_balance = array_sum($subwalletsBalance);
        $total_main_wallet_balance = $bigWallet['main']['total_nofrozen'];
        $total_balance = $bigWallet['main']['total_nofrozen'] + array_sum($subwalletsBalance) + $bigWallet['main']['frozen'];
        $total_frozen = $bigWallet['total_frozen'];
        $data['total_no_frozen'] = $total_main_wallet_balance + $total_subwallet_balance;
        $data['profilePicture'] = $this->setProfilePicture();

        # Registered popup modal
        $data['is_registered_popup_success_done'] = $data['player']['is_registered_popup_success_done'];

        $this->loadTemplate(lang('cashier.01'), '', '', '');

        $this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/dashboard/lottery_dashboard', $data);
        $this->template->render();
    }

	public function queryCredit() {
		$this->load->model(array('static_site', 'player_model', 'external_system', 'wallet_model', 'transactions'));
		$player_id = $this->authentication->getPlayerId();
		$this->session->set_userdata('playerId', $player_id);

		$data['pendingBalance'] = $this->player_model->getPlayerPendingBalance($player_id);
        $subwallet=null;
        $success=$this->wallet_model->lockAndTransForPlayerBalance($player_id, function () use (
            $player_id, &$subwallet) {

            $subwallet = $this->wallet_model->getAllPlayerAccountByPlayerId($player_id);
            return !empty($subwallet);
        });
		$data['subwallet'] = $subwallet;
		$data['totalBalance'] = $this->wallet_model->getTotalBalance($player_id);

		$this->loadTemplate('', '', '', '');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/queryCredit', $data);
		$this->template->render();
	}

	public function setDashboardSideBarSession($isMenu = "") {
		$this->session->set_userdata("dashboard_side_menu", $isMenu);
	}

	public function direct_menu($isMenu = "",$cashierTab = "") {
		$this->session->set_userdata("dashboard_side_menu", $isMenu);

		if($isMenu=='memberCenter' && $cashierTab=='deposit'){
			return redirect('/player_center2/deposit');
		}

		if($isMenu=='memberCenter' && (($cashierTab=='withdraw') || ($cashierTab=='withdrawal')) ){
			return redirect('/player_center2/withdraw');
		}

		$this->goPlayerSettings($this->authentication->getPlayerId());
	}

	public function getPlayerReferralById($playerId) {
		$input = $this->input->post();
		$request = null;
		if (isset($input['dateRangeValueStart'], $input['dateRangeValueEnd'])) {
			$request = "player.createdOn BETWEEN '".$input['dateRangeValueStart']." 00:00:00' AND '".$input['dateRangeValueEnd']." 23:59:59'";
		}
		$data = $this->player_model->getPlayerReferral($playerId,$request);

		foreach ($data as $key => $value) {
			$data[$key]['username'] = $value['username'];
			$data[$key]['createdOn'] = $value['createdOn'];
			$data[$key]['totalBettingAmount'] = '<strong>'.$this->utils->toCurrencyNumber($value['totalBettingAmount']).'</strong>';
			$data[$key]['amount'] = '<strong>'.$this->utils->toCurrencyNumber($value['amount']).'</strong>';
		}

		return $this->returnJsonResult($data);
	}

	/*
		@MPANUGAO
		Public Function to set tutorial (is_tutorial_done) true or false:0 or 1
		@return true or false
	*/
	public function setIsTutorialDone() {
		$data = array(
			'playerId' => $this->authentication->getPlayerId(),
			'is_tutorial_done' => 1,
		);
		$this->load->model('new_player_tutorial');
		$result['success'] = $this->new_player_tutorial->setIsTutorialDone($data);

		$this->returnJsonResult($result);
	}

	public function setIsRegisterPopUpDone() {
		$data = array(
			'playerId' => $this->authentication->getPlayerId(),
			'is_registered_popup_success_done' => 1,
		);
		$this->load->model('player_model');
		$result['success'] = $this->player_model->setIsRegisterPopUpDone($data);

		$this->returnJsonResult($result);
	}

	public function getBigWallet() {
		$playerId = $this->authentication->getPlayerId();

		$data=[];
		$this->load->model(['wallet_model', 'player_model', 'game_provider_auth']);
		$data =  $this->wallet_model->getBigWalletByPlayerId($playerId);

		$playerUsername=$this->player_model->getUsernameById($playerId);
		$manager = $this->utils->loadGameManager();
		$balances = $manager->queryBalanceOnAllPlatforms($playerUsername);
		foreach ($data['sub'] as $api_id => $sub) {
			$data['subwallet']=['typeId'=>$api_id, 'totalBalanceAmount'=>$sub['total_nofrozen'], 'frozen'=>$sub['frozen']];
		}

		$success = [];
		foreach ($balances as $api_id => $res) {
			$success[$api_id] = $res['success'];
		}
		$game_platforms = $this->game_provider_auth->getGamePlatforms($playerId);
		foreach ($game_platforms as $gp) {
			if (!empty($gp['register'])) {
				$success[$gp['id']] = true;
			}
		}

		$data['success'] = $success;

		$this->returnJsonResult($data);
	}

	public function ajax_lang($pram)
	{
		$this->returnText(lang(urldecode($pram)));
	}

	public function registration_success() {
		$this->loadTemplate(lang('cashier.01'), '', '', 'wallet');
		$this->template->add_js('resources/js/highlight.pack.js');
		$this->template->add_css('resources/css/hljs.tomorrow.css');
		$this->template->add_js('resources/js/json2.min.js');
		$this->template->add_js('resources/js/datatables.min.js');
		$this->template->add_css('resources/css/datatables.min.css');

		$template = $this->utils->getPlayerCenterTemplate() . '/auth/registration-success';
		$this->template->write_view('main_content', $template, $data);
		$this->template->render();
	}

	/*
		@MPANUGAO
		Public Function to set VIP Group (is_vip_show_done) true or false:0 or 1
		@return true or false
	*/
	public function setIsVIPShowDone() {
		$data = array(
			'playerId' => $this->authentication->getPlayerId(),
			'is_vip_show_done' => 1,
		);

		$this->load->model('group_level');
		$result['success'] = $this->group_level->setIsVIPShowDone($data);

		$this->returnJsonResult($result);

	}

	function unread_message_count() {

		$result = [ 'status' => 'error' , 'mesg' => lang('Error') . ' (-1)' , 'data' => null ];
		try {
			$player_id = $this->authentication->getPlayerId();
			$unread_count = $this->utils->unreadMessages($player_id);

			$result = [ 'status' => 'success' , 'mesg' => '' , 'data' => [ 'unread_count' => $unread_count ] ];
		}
		catch (Exception $ex) {
			$this->utils->debug_log('unread_message_count', 'error', $ex->getMessage());
		}
		finally {
			$this->returnJsonResult($result);
		}
	}

	public function show_404() {

		$url = $this->utils->getConfig('player_404_override');

		return empty($url) ? show_404() : redirect($this->utils->getSystemUrl('www',$url));
	}

	public function isIncludeCnChar(){
		$email = $this->input->post('email');
		if (!preg_match("|^[-_.0-9a-z]+@([-_0-9a-z][-_0-9a-z]+\.)+[a-z]{2,3}$|i",$email)) {
		    $this->form_validation->set_message('isIncludeCnChar', lang("formvalidation.valid_email_accHistory"));
		    return false;
		}
		return true;
	}
}
