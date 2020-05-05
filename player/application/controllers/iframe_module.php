<?php

require_once dirname(__FILE__) . '/BaseController.php';

require_once dirname(__FILE__) . '/modules/promo_module.php';
require_once dirname(__FILE__) . '/modules/gotogame_module.php';
require_once dirname(__FILE__) . '/modules/player_auth_module.php';
require_once dirname(__FILE__) . '/modules/player_password_module.php';
require_once dirname(__FILE__) . '/modules/player_deposit_module.php';
require_once dirname(__FILE__) . '/modules/player_withdraw_module.php';
require_once dirname(__FILE__) . '/modules/player_bank_module.php';
require_once dirname(__FILE__) . '/allowed_withdrawal_kyc_risk_score.php';
require_once dirname(__FILE__) . '/modules/player_profile.php';
require_once dirname(__FILE__) . '/modules/contact_us.php';

/**
 * Defines general behavior of game API classes.
 *
 * General behaviors include:
 * * Player Details
 * * Modify Player Details
 * * Customize language (english/chinese)
 * * Loading of player templates
 * * Setting Rules for updating player
 * * Verifying Money Transfer
 * * Transferring Main wallet to Game wallet.
 * * Deposit Game wallet to Main wallet
 * * View Reports
 * * Deposit History
 * * Withdrawal History
 *
 * @see Redirect redirect to player page
 *
 * @category iframe_module
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Iframe_module extends BaseController {

	function __construct() {
		parent::__construct();

		$this->load->helper('url');
		$this->load->library(array('form_validation', 'authentication', 'language_function', 'player_functions', 'cms_function', 'template', 'pagination', 'api_functions', 'salt', 'cs_manager', 'email_setting', 'og_utility', 'game_platform/game_platform_manager', 'duplicate_account', 'affiliate_process'));
		$this->load->model(array('http_request', 'player'));

		/**
		 * detail: this block of code is applicable for mobile
		 * version design, "if there is another design for mobile for the said
		 * player center, need to add in the config
		 * $config['view_template_extra'] =  array('mobile');"
		 * to tell the system that there is another design for mobile
		 * and forwarded to the said template/design
		 */
		$this->setLanguageBySubDomain();

		$this->view_template = $this->utils->getPlayerCenterTemplate();
	}

	use promo_module;
	use gotogame_module;
	use player_auth_module;
	use player_password_module;
	use player_deposit_module;
	use player_withdraw_module;
	use player_bank_module;
	use allowed_withdrawal_kyc_risk_score;
	use player_profile;
	use contact_us;

	const FORBIDDEN_NAMES = ['admin', 'moderator', 'hoster', 'administrator', 'mod'];
	const MIN_USERNAME_LENGTH = 4;
	const MAX_USERNAME_LENGTH = 9;
	const MIN_PASSWORD_LENGTH = 4;
	const MAX_PASSWORD_LENGTH = 12;

	const FIRST_AVAILABLE_DEPOSIT = 0;
	const MESSAGE_CODE_NO_DEPOSIT = '1';
	const MESSAGE_CODE_NOT_AVAILABLE = '2';

	// for kyc risk
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

	function index() {
		redirect('iframe_module/iframe_home');
	}

	/**
	 * Loads template for view based on regions in
	 * config > template.php
	 *
	 */
	private function loadTemplate($title = '', $description = '', $keywords = '', $activenav = '') {
		$this->template->set_template($this->utils->getPlayerCenterTemplate(false));
		$this->template->add_js('resources/js/player/player.js');
		$this->template->add_js('resources/js/bootstrap.min.js');
		$this->template->write('skin', 'template1.css');
		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('player_id', $this->authentication->getPlayerId());
		$this->template->write('isLogged', $this->authentication->isLoggedIn());

		if ($this->config->item('responsible_gaming')) {
			$this->load->model('responsible_gaming');
			$timeReminder = $this->responsible_gaming->getData($this->authentication->getPlayerId(), Responsible_gaming::TIMER_REMINDERS, Responsible_gaming::STATUS_APPROVED);
			$this->template->write('timeReminders', @reset($timeReminder)->period_cnt);
		}

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

		if ($this->player_functions->checkRegisteredFieldsIfRequired('Language') == 0) {
			$this->form_validation->set_rules('language', 'Language', 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('language', 'Language', 'trim|xss_clean');
		}

		if ($this->player_functions->checkRegisteredFieldsIfRequired('Contact Number') == 0) {
			$this->form_validation->set_rules('contact_number', 'Contact Number', 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('contact_number', 'Contact Number', 'trim|xss_clean');
		}

		if ($this->player_functions->checkRegisteredFieldsIfRequired('Instant Message 1') == 0) {
			$this->form_validation->set_rules('im_type', 'IM 1', 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('im_type', 'IM 1', 'trim|xss_clean');
		}

		if ($this->player_functions->checkRegisteredFieldsIfRequired('Instant Message 2') == 0) {
			$this->form_validation->set_rules('im_type2', 'IM 2', 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('im_type2', 'IM 2', 'trim|xss_clean');
		}

		if ($this->player_functions->checkRegisteredFieldsIfRequired('Nationality') == 0) {
			$this->form_validation->set_rules('citizenship', 'Nationality', 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('citizenship', 'Nationality', 'trim|xss_clean');
		}

		if ($this->player_functions->checkRegisteredFieldsIfRequired('BirthPlace') == 0) {
			$this->form_validation->set_rules('birthplace', 'Birthplace', 'trim|required|xss_clean');
		} else {
			$this->form_validation->set_rules('birthplace', 'Birthplace', 'trim|xss_clean');
		}

		if (!empty($this->input->post('im_type'))) {
			if ($this->input->post('im_type') == 'QQ') {
				$this->form_validation->set_rules('im_account', 'IM Account 1', 'trim|required|xss_clean|numeric');
			} elseif ($this->input->post('im_type') == 'Skype' || $this->input->post('im_type') == 'MSN') {
				$this->form_validation->set_rules('im_account', 'IM Account 1', 'trim|required|xss_clean');
			} else {
				$this->form_validation->set_rules('im_account', 'IM Account 1', 'trim|required|xss_clean');
			}
		}

		if (!empty($this->input->post('im_type2'))) {
			if ($this->input->post('im_type2') == 'QQ') {
				$this->form_validation->set_rules('im_account2', 'IM Account 2', 'trim|required|xss_clean|numeric');
			} elseif ($this->input->post('im_type2') == 'Skype' || $this->input->post('im_type2') == 'MSN') {
				$this->form_validation->set_rules('im_account2', 'IM Account 2', 'trim|required|xss_clean');
			} else {
				$this->form_validation->set_rules('im_account2', 'IM Account 2', 'trim|required|xss_clean');
			}
		}
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

		foreach ($diff as $key => $value) {
			$changes[lang('reg.fields.' . $key) ?: $key] = [
				'old' => $old_data[$key],
				'new' => $new_data[$key],
			];
		}

		$output = '<ul>';

		if (!empty($changes)) {
			ksort($changes);

			foreach ($changes as $key => $value) {
				$output .= "<li>{$key}:<br><code>Old: {$value['old']}</code><br><code>New: {$value['new']}</code></li>";
			}
		}
		$output .= '</ul>';
		// $output = json_encode($changes, JSON_PRETTY_PRINT);

		return $output;
	}

	/**
	 * view cashier
	 *
	 *
	 * @return rendered template
	 */
	public function iframe_viewCashier() {
		// if (!$this->authentication->isLoggedIn()) {
		// 	$this->goPlayerLogin();
		// } else {
		$this->load->model(array('static_site', 'player_model', 'external_system',
			'wallet_model', 'promorules', 'transactions', 'total_player_game_hour', 'system_feature'));
		$player_id = $this->authentication->getPlayerId();
		$this->session->set_userdata('playerId', $player_id);

		$this->utils->startEvent('query data', 'data for render cashier');

		$data['pendingBalance'] = $this->player_model->getPlayerPendingBalance($player_id);
		$data['player'] = $this->player_functions->getPlayerById($player_id);

        $subwallet=null;
        $success=$this->wallet_model->lockAndTransForPlayerBalance($player_id, function () use (
            $player_id, &$subwallet) {

            $subwallet = $this->wallet_model->getAllPlayerAccountByPlayerId($player_id);
            return !empty($subwallet);
        });

		$data['subwallet'] = $subwallet;
		$data['totalBalance'] = $this->wallet_model->getTotalBalance($player_id);
		$data['imageLoaderUrl'] = $this->getImageLoader();
		$data['game'] = $this->external_system->getAllActiveSytemGameApi();
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$data['search_list'] = 0;

		$data['profilePicture'] = $this->setProfilePicture();
		$data['profileProgress'] = $this->getProfileProgress();

		// OGP-810
		$data['show_tag_for_unavailable_deposit_accounts'] = (int) $this->system_feature->isEnabledFeature('show_tag_for_unavailable_deposit_accounts');

		// $data['responsible_gaming'] = $this->operatorglobalsettings->getSettingValue('responsible_gaming');

		// $this->utils->eventInfo('query data info');

		$this->utils->endEvent('query data');

		$data['available_rescue_promotion'] = false; // $this->utils->getConfig('enabled_rescue_promotion');

		// if ($data['available_rescue_promotion']) {
		// 	$data['rescue_promo'] = $this->promorules->getFirstNonDepositPromoByType(Promorules::NON_DEPOSIT_PROMO_TYPE_RESCUE);

		// 	if (!empty($data['rescue_promo'])) {
		// 		$fromDatetime = $this->utils->getLastFromDatetime(array(Utils::FROM_TYPE_PLAYER_REG_DATE, Utils::FROM_TYPE_LAST_WITHDRAW, Utils::FROM_TYPE_LAST_SAME_PROMO),
		// 			$player_id, $data['rescue_promo']['promorulesId']);
		// 		$depositAmount = $this->transactions->getFirstDepositAmount($player_id, $fromDatetime);
		// 		$promorule = $this->promorules->viewPromoRuleDetails($data['rescue_promo']['promorulesId']);
		// 		$bonusAmount = $this->promorules->getBonusAmount($promorule, $depositAmount, $player_id, $errorMessageLang);
		// 		$resultAmount = $this->total_player_game_hour->getResultByPlayers(
		// 			$player_id, $fromDatetime, $this->utils->getNowForMysql());
		// 		$data['available_rescue_promotion'] = $this->utils->compareResultFloat($data['totalBalance'], '<=', $this->utils->getConfig('rescue_promotion_amount'))
		// 		&& $resultAmount < 0 && $bonusAmount > 0;
		// 	} else {
		// 		$data['available_rescue_promotion'] = false;
		// 	}
		// }
		//and exist deposit
		// $data['available_rescue_promotion'] = $this->utils->getConfig('enabled_rescue_promotion')
		// 	&& $available_rescue_promotion;
		$data['site']=$this->utils->getSystemUrl('www');

		if($this->utils->is_mobile()){
	  		$data['site'] = str_replace("www", "m", $data['site']);
		}

		$this->loadTemplate(lang('Cashier'), '', '', 'wallet');
		$this->template->add_js('resources/js/highlight.pack.js');
		$this->template->add_css('resources/css/hljs.tomorrow.css');
		$this->template->add_js('resources/js/json2.min.js');
		$this->template->add_js('resources/js/datatables.min.js');
		$this->template->add_css('resources/css/datatables.min.css');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/view_cashier', $data);
		$this->template->render();
		// }
	}

	public function getSubWalletBalance() {
		$player_id = $this->authentication->getPlayerId();
		echo json_encode($this->player_functions->getAllPlayerAccountByPlayerId($player_id));
	}

	const MAIN_WALLET_ID = 0;
	public function verifyMoneyTransfer($player_id, $next = null) {

		$this->load->model(array('player_model', 'external_system', 'transactions', 'wallet_model'));

		$player_id = $this->authentication->getPlayerId();
		$playerName = $this->player_model->getUsernameById($player_id);

		$url = $this->input->post("redirect_url");
		$this->utils->debug_log("input redirect_url : " . $url);

		$this->form_validation->set_rules('transfer_from', 'Transfer From', 'trim|xss_clean|required');
		$this->form_validation->set_rules('transfer_to', 'Transfer To', 'trim|xss_clean|required|callback_checkTransfer');
		$this->form_validation->set_rules('amount', 'Amount', 'trim|xss_clean|required|numeric');

		if ($this->form_validation->run() == false) {
			$message = lang('notify.61'); //"transaction failed"
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			if (!empty($url)) {
				redirect(site_url($url));
			} else {
				$this->gotoCashier($next);
			}
		} else {
			$transfer_from = $this->input->post('transfer_from');
			//it's platform id
			$transfer_to = $this->input->post('transfer_to');

			$gamePlatformId = null;
			$lock_type = null;
			if ($transfer_to != self::MAIN_WALLET_ID) {
				$gamePlatformId = $transfer_to;
				$lock_type = 'main_to_sub';
			} else {
				$gamePlatformId = $transfer_from;
				$lock_type = 'sub_to_main';
			}

			$this->load->model(array('external_system', 'player_model'));

			if ($transfer_to == AGENCY_API || $transfer_from == AGENCY_API) {
				if (!$this->player_model->isUnderAgent($player_id)) {
					$message = lang('Transaction Failed! Main Wallet can\'t transfer to Agency Wallet.');
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
					return $this->gotoCashier($next);
				}
			}

			// if ($transfer_to == AGENCY_API) {
			// 	$message = lang('Transaction Failed! Main Wallet can\'t transfer to Agency Wallet.');
			// 	$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			// 	$this->gotoCashier($next);
			// 	return;
			// }

			if (!$this->external_system->isGameApiActive($gamePlatformId)) {
				$message = lang('notify.51'); //"transaction failed"
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				if (!empty($url)) {
					redirect(site_url($url));
				} else {
					$this->gotoCashier($next);
				}
				return;
			}

			if ($lock_type == 'sub_to_main' && !$this->player_model->isEnabledTransfer($player_id)) {
				$message = lang('notify.51'); //"transaction failed"
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				if (!empty($url)) {
					redirect(site_url($url));
				} else {
					$this->gotoCashier($next);
				}
				return;
			}

			$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);

			//check subwallet account first
			$api->quickCheckAccount($player_id);

			$amount = round($this->input->post('amount'), 2);
            $amount = round($amount, 0);
			$result = $this->utils->transferWallet($player_id, $playerName, $transfer_from, $transfer_to, $amount);
			$this->utils->debug_log('result of transfer wallet', $result);

			$transferTransId = isset($result['transferTransId']) ? $result['transferTransId'] : null;
			if ($result['success'] && !empty($transferTransId)) {

				$this->triggerTransferPromotion($player_id, $amount, $transfer_from, $transfer_to, $transferTransId, $result);

			} else {
				$this->utils->debug_log('transfer failed so donot check promotion', $result);
			}

			if ($result['success']) {
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $result['message']);
			} else {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $result['message']);
			}

			$this->utils->debug_log('result', $result);

			// $result = array('success' => false);
			// // lock this kind of transaction for this player
			// // $trans_key = $player_id . '-' . $gamePlatformId . '-' . $lock_type;
			// // lock it
			// // $lock_it = $this->player_model->transGetLock($trans_key);
			// $lock_it = $this->lockTransferSubwallet($player_id, $gamePlatformId, $lock_type);
			// try {
			// 	$this->utils->debug_log('lockTransferSubwallet', $player_id, $gamePlatformId, $lock_type, $lock_it, 'check amount', $this->checkAmount());
			// 	if ($lock_it) {
			// 		if ($this->checkAmount()) {
			// 			$this->startTrans();

			// 			//game API
			// 			if ($api) {
			// 				if ($transfer_to == Wallet_model::MAIN_WALLET_ID) {
			// 					// if ($transactionType == Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET) {
			// 					$result = $api->withdrawFromGame($playerName, $amount);

			// 				} else if ($transfer_from == Wallet_model::MAIN_WALLET_ID) {
			// 					// } else if ($transactionType == Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET) {
			// 					//TODO LOGOUT ?
			// 					$result = $api->depositToGame($playerName, $amount);
			// 				}
			// 			}

			// 			if ($result['success']) {
			// 				// $currentplayerbalance = !$result['currentplayerbalance'] ? 0 : $result['currentplayerbalance'];

			// 				if ($this->wallet_model->transferWalletAmount($gamePlatformId, $player_id, $transfer_from, $transfer_to, $amount, @$result['external_transaction_id'])) {
			// 					$message = lang('notify.50');
			// 					$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);

			// 				} else {
			// 					$this->utils->debug_log('transfer failed on db', $gamePlatformId, $player_id, $transfer_from, $transfer_to, $amount);
			// 					$message = lang('notify.51');
			// 					$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

			// 				}

			// 			} else {
			// 				$this->utils->debug_log('transfer failed', $result);
			// 				//$message .= "Your transfer request hasn't been sent!";
			// 				$message = lang('notify.51');
			// 				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			// 			}

			// 			$success = $this->endTransWithSucc();
			// 			if (!$success) {
			// 				$message = lang('notify.61'); //"transaction failed"
			// 				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			// 			}

			// 		} else {
			// 			$message = lang('notify.61'); //"transaction failed"
			// 			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			// 		}
			// 	} else {
			// 		$message = lang('notify.61'); //"transaction failed"
			// 		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			// 	}

			// } finally {
			// 	// release it
			// 	$rlt = $this->releaseTransferSubwallet($player_id, $gamePlatformId, $lock_type);
			// 	// $rlt = $this->player_model->transReleaseLock($trans_key);
			// 	$this->utils->debug_log('releaseTransferSubwallet', $player_id, $gamePlatformId, $lock_type, $rlt);
			// }
			if (!empty($url)) {
				redirect(site_url($url));
			} else {
				$this->gotoCashier($next);
			}

			// $this->transferProcess($player_id, $gamePlatformId, $transfer_from, $transfer_to, $amount);
		}
		// }
	}

	private function gotoCashier($type) {
		$this->goViewCashier();
	}

	/**
	 * useless
	 */
	public function depositFromPlayerAccount($player_account_to, $amount, $new_balance) {
		return false;
		// $controller = $this;

		// 	$playerAccountId = $player_account_from['playerAccountId'];

		//       $balance = $this->player_functions->getBalanceByPlayerAccountId($playerAccountId);

		// if ($player_account_to['type'] == 'wallet') {
		// 	$data = array(
		// 		'totalBalanceAmount' => $balance + $amount,
		// 	);
		// } else {
		// 	$data = array(
		// 		'totalBalanceAmount' => (double) $new_balance,
		// 	);
		// }

		// //$this->player_functions->setPlayerNewBalAmountByPlayerAccountId($player_account_to['playerAccountId'], $data);
		// $this->lockAndTrans(Utils::LOCK_ACTION_BALANCE, $playerAccountId, function () use ($controller, $playerAccountId,$data) {
		// 	 $controller->player_functions->setPlayerNewBalAmountByPlayerAccountId( $playerAccountId, $data);
		// });
	}

	/**
	 * useless
	 */
	public function withdrawFromPlayerAccount($player_account_from, $amount, $new_balance) {
		return false;
		// $controller = $this;

		// $playerAccountId = $player_account_from['playerAccountId'];

		// $balance = $this->player_functions->getBalanceByPlayerAccountId($playerAccountId);

		// if ($player_account_from['type'] == 'wallet') {
		// 	$data = array(
		// 		'totalBalanceAmount' => $balance - $amount,
		// 	);
		// } else {
		// 	$data = array(
		// 		'totalBalanceAmount' => (double) $new_balance,
		// 	);
		// }

		// //$this->player_functions->setPlayerNewBalAmountByPlayerAccountId($player_account_from['playerAccountId'], $data);
		// $this->lockAndTrans(Utils::LOCK_ACTION_BALANCE, $playerAccountId, function () use ($controller, $playerAccountId, $data) {
		// 	$controller->player_functions->setPlayerNewBalAmountByPlayerAccountId($playerAccountId, $data);
		// });

	}

	public function checkTransfer() {
		$transfer_from = $this->input->post('transfer_from');
		$transfer_to = $this->input->post('transfer_to');

		if ($transfer_from == $transfer_to) {
			//$this->form_validation->set_message('checkTransfer', "Make sure that transfer from is different from transfer to.");
			//$this->form_validation->set_message('checkTransfer', lang('notify.52'));
			$message = lang('notify.52');
			$this->alertMessage(1, $message);
			return false;
		} else if ($transfer_from != 0 && $transfer_to != 0) {
			//$this->form_validation->set_message('checkTransfer', "Cannot Transfer money from sub wallet to another sub wallet.");
			$message = lang('notify.53');
			$this->alertMessage(1, $message);
			$this->form_validation->set_message('checkTransfer', '' /*lang('notify.53')*/);
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
			// if ($amount > $player_account['totalBalanceAmount']) {
			//$this->form_validation->set_message('checkAmount', "Transfer of money should not be greater than the balance of your wallet.");
			$message = lang('notify.54');
			$this->alertMessage(1, $message);
			$this->form_validation->set_message('checkAmount', '' /*lang('notify.54')*/);
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
	public function searchCashier() {
		// if (!$this->authentication->isLoggedIn()) {
		// 	$this->goPlayerLogin();
		// } else {
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
				$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/view_report', $data);
				$this->template->render();
			}
		}
		// }
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

		$data['count_all'] = $this->player_model->getAllWithdrawsWLimit($player_id, null, null, $search, true);


		// $config['base_url'] = "javascript:withdrawalDepositHistory(";
		// $config['total_rows'] = $data['count_all'];
		// $config['per_page'] = '5';
		// $config['num_links'] = '1';

		// $config['full_tag_open'] = '<ul class="pagination">';
		// $config['full_tag_close'] = '</ul>';
		// $config['first_link'] = false;
		// $config['last_link'] = false;
		// $config['first_tag_open'] = '<li>';
		// $config['first_tag_close'] = '</li>';
		// $config['prev_link'] = '&laquo';
		// $config['prev_tag_open'] = '<li class="prev">';
		// $config['prev_tag_close'] = '</li>';
		// $config['next_link'] = '&raquo';
		// $config['next_tag_open'] = '<li>';
		// $config['next_tag_close'] = '</li>';
		// $config['last_tag_open'] = '<li>';
		// $config['last_tag_close'] = '</li>';
		// $config['cur_tag_open'] = '<li class="active"><a href="#">';
		// $config['cur_tag_close'] = '</a></li>';
		// $config['num_tag_open'] = '<li>';
		// $config['num_tag_close'] = '</li>';

		// $config['cur_tag_open'] = "<li><span><b>";
		// $config['cur_tag_close'] = "</b></span></li>";

		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '5';
		$config['num_links'] = '1';
	    $config['base_url'] = "javascript:withdrawalDepositHistory('%s')";
		$config['callback_link'] = true;

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
		$config['anchor_class'] = 'class="my-pagination" ';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$config['next_link'] = lang('Next Page');
		$config['prev_link'] = lang('Prev Page');
		$config['last_link'] = lang('Last Page');
		$config['first_link'] = lang('First Page');

		$this->pagination->initialize($config);
		$data['create_links'] = $this->pagination->create_links();
		// $this->utils->debug_log('depositHistory count_all', $data['count_all']);
		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['withdraws'] = $this->player_model->getAllWithdrawsWLimit($player_id, $config['per_page'], $segment, $search);

		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/member_withdraw_history', $data);
	}

	public function depositHistory($segment, $from = null, $to = null) {
		$player_id = $this->authentication->getPlayerId();

		$this->load->model(array('player_model'));
		$search = array(
			'from' => urldecode($from),
			'to' => urldecode($to),
		);
		$data['count_all'] = $this->player_model->getAllDepositsWLimit($player_id, null, null, $search, true);

		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '5';
		$config['num_links'] = '1';
	    $config['base_url'] = "javascript:depositHistory('%s')";
		$config['callback_link'] = true;

		$config['first_tag_open'] = '<li class="page_first">';
		$config['last_tag_open'] = '<li class="page_last">';
		$config['next_tag_open'] = '<li class="page_next">';
		$config['prev_tag_open'] = '<li class="page_preview">';
		$config['num_tag_open'] = '<li class="page_number">';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_close'] = '</li>';
		$config['num_tag_close'] = '</li>';
		$config['anchor_class'] = 'class="my-pagination" ';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$config['next_link'] = lang('Next Page');
		$config['prev_link'] = lang('Prev Page');
		$config['last_link'] = lang('Last Page');
		$config['first_link'] = lang('First Page');


		$this->pagination->initialize($config);
		$data['create_links'] = $this->pagination->create_links();

		// $this->utils->debug_log('depositHistory count_all', $data['count_all']);
		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['deposits'] = $this->player_model->getAllDepositsWLimit($player_id, $config['per_page'], $segment, $search);

		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_deposit_history', $data);
	}

	public function pointsHistory($segment, $from = null, $to = null) {
		$player_id = $this->authentication->getPlayerId();

		$this->load->model(array('point_transactions'));
		$search = array(
			'from' => urldecode($from),
			'to' => urldecode($to),
		);

		$data['count_all'] = $this->point_transactions->pointsHistory($player_id, null, null, $search, true);

		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '5';
		$config['num_links'] = '1';
	    $config['base_url'] = "javascript:pointsHistory('%s')";
		$config['callback_link'] = true;

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
		$config['anchor_class'] = 'class="my-pagination" ';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$config['next_link'] = lang('Next Page');
		$config['prev_link'] = lang('Prev Page');
		$config['last_link'] = lang('Last Page');
		$config['first_link'] = lang('First Page');

		// $config['total_rows'] = $data['count_all'];
		// $config['per_page'] = '5';
		// $config['num_links'] = '1';
	 //    $config['base_url'] = "javascript:pointsHistory('%s')";
		// $config['callback_link'] = true;

		// $config['first_tag_open'] = '<li>';
		// $config['last_tag_open'] = '<li>';
		// $config['next_tag_open'] = '<li>';
		// $config['prev_tag_open'] = '<li>';
		// $config['num_tag_open'] = '<li>';
		// $config['first_tag_close'] = '</li>';
		// $config['last_tag_close'] = '</li>';
		// $config['next_tag_close'] = '</li>';
		// $config['prev_tag_close'] = '</li>';
		// $config['num_tag_close'] = '</li>';
		// $config['anchor_class'] = 'class="my-pagination" ';
		// $config['cur_tag_open'] = "<li><span><b>";
		// $config['cur_tag_close'] = "</b></span></li>";


		$this->pagination->initialize($config);

		// $this->utils->debug_log('depositHistory count_all', $data['count_all']);
		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['points'] = $this->point_transactions->pointsHistory($player_id, $config['per_page'], $segment, $search);

		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_points_history', $data);
	}

	/**
	 * over view : get point history
	 * @return view
	 * add by spencer.kuo 2017.04.26
	 */
	public function iframe_point_history() {
		$player_id = $this->authentication->getPlayerId();

		$this->load->model(array('point_transactions'));

		$page = 0;

		$search = $this->input->post();
		if (empty($search)) {
			$search = array(
				'from' => date('Y-m-d 00:00:00'),
				'to' => date('Y-m-d 23:59:59'),
			);
		} else {
			if (isset($search["page"])){
				$page = $search["page"];
				unset($search["page"]);
			}
		}

		$data['from'] = $search['from'];
		$data['to'] = $search['to'];
		$data['page'] = $page;

		$record_count = $this->point_transactions->pointsHistory($player_id, null, null, $search, true);

		$config['base_url'] = site_url() . '/iframe_module/iframe_pointhistory';
		$config['total_rows'] = $record_count;
		$config['first_link'] = false;
		$config['last_link'] = false;
		$config['prev_link'] = '&lt;';
		$config['next_link'] = '&gt;';
		$config['full_tag_open'] = '<div id="pageNav" class="pagejump" pagenum="1" pagesize="10">';
		$config['full_tag_close'] = '</div>';
		$config['cur_tag_open'] = '<span id="page_num" class="pageNavEle">';
		$config['cur_tag_close'] = '</span>';
		$config['num_tag_open'] = '<span id="page_num" class="pageNavEle">';
		$config['num_tag_close'] = '</span>';
		$config['prev_tag_open'] = '<span id="prev_button" class="pageNavEle">';
		$config['prev_tag_close'] = '</span>';
		$config['next_tag_open'] = '<span id="next_button" class="pageNavEle">';
		$config['next_tag_close'] = '</span>';
		$config['per_page'] = 10;

		$this->pagination->initialize($config);

		$data['create_links'] = $this->pagination->create_links();

		//if( $this->uri->segment(3) ) {
		//	$page = $this->uri->segment(3);
		//}

		$data['points'] = $this->point_transactions->pointsHistory($player_id, $config['per_page'], $page, $search);
		$totalpoints = $this->point_transactions->pointTotal($player_id);
		if (empty($totalpoints))
			$totalpoints = 0;
		$data['totalpoints'] = $totalpoints;
		$this->loadTemplate(lang('Points'), '', '', '');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/view_points_history', $data);
		$this->template->render();
	}

	public function withdrawalHistory($segment, $from, $to) {
		$player_id = $this->authentication->getPlayerId();

		$search = array(
			'from' => urldecode($from),
			'to' => urldecode($to),
		);
		$this->load->model(array('wallet_model', 'operatorglobalsettings'));
		$data['count_all'] = count($this->wallet_model->getAllWithdrawalsWLimit($player_id, null, null, $search, true));
		// $this->utils->debug_log('withdrawalHistory : count_all by array : ', $this->wallet_model->getAllWithdrawalsWLimit($player_id, null, null, $search, true));
		// $config['base_url'] = "javascript:withdrawalHistory(";
		// $config['total_rows'] = $data['count_all'];
		// $config['per_page'] = '5';
		// $config['num_links'] = '1';

		// $config['first_tag_open'] = '<li>';
		// $config['last_tag_open'] = '<li>';
		// $config['next_tag_open'] = '<li>';
		// $config['prev_tag_open'] = '<li>';
		// $config['num_tag_open'] = '<li>';

		// $config['first_tag_close'] = '</li>';
		// $config['last_tag_close'] = '</li>';
		// $config['next_tag_close'] = '</li>';
		// $config['prev_tag_close'] = '</li>';
		// $config['num_tag_close'] = '</li>';

		// $config['cur_tag_open'] = "<li><span><b>";
		// $config['cur_tag_close'] = "</b></span></li>";

		// $config['next_link'] = lang('Next Page');
		// $config['prev_link'] = lang('Prev Page');
		// $config['last_link'] = lang('Last Page');
		// $config['first_link'] = lang('First Page');

		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '5';
		$config['num_links'] = '1';
	    $config['base_url'] = "javascript:withdrawalHistory('%s')";
		$config['callback_link'] = true;

		$config['first_tag_open'] = '<li class="page_first">';
		$config['last_tag_open'] = '<li class="page_last">';
		$config['next_tag_open'] = '<li class="page_next">';
		$config['prev_tag_open'] = '<li class="page_preview">';
		$config['num_tag_open'] = '<li class="page_number">';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_close'] = '</li>';
		$config['num_tag_close'] = '</li>';
		$config['anchor_class'] = 'class="my-pagination" ';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$config['next_link'] = lang('Next Page');
		$config['prev_link'] = lang('Prev Page');
		$config['last_link'] = lang('Last Page');
		$config['first_link'] = lang('First Page');

		$this->pagination->initialize($config);

		$data['create_links'] = $this->pagination->create_links();

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['withdrawals'] = $this->wallet_model->getAllWithdrawalsWLimit($player_id, $config['per_page'], $segment, $search);
		$data['operatorglobalsettings'] = $this->operatorglobalsettings;
		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_withdrawal_history', $data);
	}

	public function cashbackHistory($segment, $from, $to) {
		$this->load->model(array('transactions'));
		$player_id = $this->authentication->getPlayerId();

		$search = array(
			'from' => urldecode($from),
			'to' => urldecode($to),
		);

		$data['count_all'] = $this->transactions->getCashbackHistoryWLimit($player_id, null, null, $search, true);

		// $config['base_url'] = "javascript:cashbackHistory(";
		// $config['total_rows'] = $data['count_all'];
		// $config['per_page'] = '5';
		// $config['num_links'] = '1';

		// //config for bootstrap pagination class integration
		// $config['full_tag_open'] = '<ul class="pagination">';
		// $config['full_tag_close'] = '</ul>';
		// $config['first_link'] = false;
		// $config['last_link'] = false;
		// $config['first_tag_open'] = '<li>';
		// $config['first_tag_close'] = '</li>';
		// $config['prev_link'] = '&laquo';
		// $config['prev_tag_open'] = '<li class="prev">';
		// $config['prev_tag_close'] = '</li>';
		// $config['next_link'] = '&raquo';
		// $config['next_tag_open'] = '<li>';
		// $config['next_tag_close'] = '</li>';
		// $config['last_tag_open'] = '<li>';
		// $config['last_tag_close'] = '</li>';
		// $config['cur_tag_open'] = '<li class="active"><a href="#">';
		// $config['cur_tag_close'] = '</a></li>';
		// $config['num_tag_open'] = '<li>';
		// $config['num_tag_close'] = '</li>';
		// $config['next_link'] = lang('Next Page');
		// $config['prev_link'] = lang('Prev Page');
		// $config['last_link'] = lang('Last Page');
		// $config['first_link'] = lang('First Page');

		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '5';
		$config['num_links'] = '1';
	    $config['base_url'] = "javascript:cashbackHistory('%s')";
		$config['callback_link'] = true;

		$config['first_tag_open'] = '<li class="page_first">';
		$config['last_tag_open'] = '<li class="page_last">';
		$config['next_tag_open'] = '<li class="page_next">';
		$config['prev_tag_open'] = '<li class="page_preview">';
		$config['num_tag_open'] = '<li class="page_number">';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_close'] = '</li>';
		$config['num_tag_close'] = '</li>';
		$config['anchor_class'] = 'class="my-pagination" ';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$config['next_link'] = lang('Next Page');
		$config['prev_link'] = lang('Prev Page');
		$config['last_link'] = lang('Last Page');
		$config['first_link'] = lang('First Page');

		$this->pagination->initialize($config);

		$data['create_links'] = $this->pagination->create_links();

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['cashbackHistory'] = $this->transactions->getCashbackHistoryWLimit($player_id, $config['per_page'], $segment, $search);

		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_cashback_history', $data);
	}

	public function transferHistory($segment, $from, $to) {
		$player_id = $this->authentication->getPlayerId();

		$search = array(
			'from' => urldecode($from),
			'to' => urldecode($to),
		);
		$this->load->model(array('transactions'));
		$data['count_all'] = count($this->transactions->getAllTransferHistoryByPlayerIdWLimit($player_id, null, null, $search));

		// $config['base_url'] = "javascript:transferHistory(";
		// $config['total_rows'] = $data['count_all'];
		// $config['per_page'] = '5';
		// $config['num_links'] = '1';

		// $config['first_tag_open'] = '<li>';
		// $config['last_tag_open'] = '<li>';
		// $config['next_tag_open'] = '<li>';
		// $config['prev_tag_open'] = '<li>';
		// $config['num_tag_open'] = '<li>';

		// $config['first_tag_close'] = '</li>';
		// $config['last_tag_close'] = '</li>';
		// $config['next_tag_close'] = '</li>';
		// $config['prev_tag_close'] = '</li>';
		// $config['num_tag_close'] = '</li>';

		// $config['cur_tag_open'] = "<li><span><b>";
		// $config['cur_tag_close'] = "</b></span></li>";

		// $config['next_link'] = lang('Next Page');
		// $config['prev_link'] = lang('Prev Page');
		// $config['last_link'] = lang('Last Page');
		// $config['first_link'] = lang('First Page');

		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '5';
		$config['num_links'] = '1';
	    $config['base_url'] = "javascript:transferHistory('%s')";
		$config['callback_link'] = true;

		$config['first_tag_open'] = '<li class="page_first">';
		$config['last_tag_open'] = '<li class="page_last">';
		$config['next_tag_open'] = '<li class="page_next">';
		$config['prev_tag_open'] = '<li class="page_preview">';
		$config['num_tag_open'] = '<li class="page_number">';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_close'] = '</li>';
		$config['num_tag_close'] = '</li>';
		$config['anchor_class'] = 'class="my-pagination" ';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$config['next_link'] = lang('Next Page');
		$config['prev_link'] = lang('Prev Page');
		$config['last_link'] = lang('Last Page');
		$config['first_link'] = lang('First Page');

		$this->pagination->initialize($config);

		$data['create_links'] = $this->pagination->create_links();

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['transfer_history'] = $this->transactions->getAllTransferHistoryByPlayerIdWLimit($player_id, $config['per_page'], $segment, $search);
		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_transfer_history', $data);
	}

	public function balanceAdjustmentHistory($segment, $from, $to) {
		$player_id = $this->authentication->getPlayerId();

		$search = array(
			'from' => urldecode($from),
			'to' => urldecode($to),
		);

		$this->load->model(array('transactions'));
		$transHistory = $this->transactions->getPlayerAdjustmentHistoryWLimit($player_id, null, null, $search);

		$data['count_all'] = count($transHistory);

		// $config['base_url'] = "javascript:balanceAdjustmentHistory(";
		// $config['total_rows'] = $data['count_all'];
		// $config['per_page'] = '5';
		// $config['num_links'] = '1';

		// $config['first_tag_open'] = '<li>';
		// $config['last_tag_open'] = '<li>';
		// $config['next_tag_open'] = '<li>';
		// $config['prev_tag_open'] = '<li>';
		// $config['num_tag_open'] = '<li>';

		// $config['first_tag_close'] = '</li>';
		// $config['last_tag_close'] = '</li>';
		// $config['next_tag_close'] = '</li>';
		// $config['prev_tag_close'] = '</li>';
		// $config['num_tag_close'] = '</li>';

		// $config['cur_tag_open'] = "<li><span><b>";
		// $config['cur_tag_close'] = "</b></span></li>";

		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '5';
		$config['num_links'] = '1';
	    $config['base_url'] = "javascript:balanceAdjustmentHistory('%s')";
		$config['callback_link'] = true;

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
		$config['anchor_class'] = 'class="my-pagination" ';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$config['next_link'] = lang('Next Page');
		$config['prev_link'] = lang('Prev Page');
		$config['last_link'] = lang('Last Page');
		$config['first_link'] = lang('First Page');

		$this->pagination->initialize($config);

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['balanceAdjustmentHistory'] = $transHistory;

		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_player_transactions', $data);
	}

	const MANUAL = 1;
	const AUTO = 2;
	const LOCAL = 3;
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

	/**
	 *
	 * <script data-main="//og.local/pub/default/player_main" src="//og.local/resources/js/require.js" async='true'></script>
	 * main player js
	 *
	 * @param string site_name
	 */
	public function player_iframe_js() {

		# Load the config for the game platform api
		$apiPT = $this->utils->loadExternalSystemLibObject(PT_API);

		$api_play_pt = '';
		if ($apiPT) {
			$api_play_pt = $apiPT->getSystemInfo('API_PLAY_PT');
		}
		// $host = $this->config->item('player_server_host'); // @$_SERVER['HTTP_HOST'];
		$logged = true;
		$host = $this->config->item('player_server_host'); // @$_SERVER['HTTP_HOST'];
		$websocket_server_host = $this->config->item('websocket_server_host');

		$apiBaseUrl = site_url("/async");

		// $assetBaseUrl = $site->asset_url . "/resources/player";
		$assetBaseUrl = site_url("/resources/player");
		$debugLog = $this->utils->isDebugMode(); // ? 'true' : 'false';
		$enabled_web_push = $this->utils->getConfig('enabled_web_push');
		$playerUsername = $this->authentication->getUsername();
		$playerId = $this->authentication->getPlayerId();
		$token = $this->authentication->getPlayerToken();

		$origin = "*";

		$defaultErrorMessage = lang("error.default.message");

		$variables = json_encode(array('host' => $host,
			'websocket_server' => $websocket_server_host,
			'assetBaseUrl' => $assetBaseUrl,
			'apiBaseUrl' => $apiBaseUrl,
			'apiPlayPT' => $api_play_pt,
			'origin' => $origin,
			'logged' => $logged,
			'debugLog' => $debugLog,
			'enabled_web_push' => $enabled_web_push,
			// 'siteLang' => $siteLang,
			'token' => $token,
			'role' => 'player',
			'playerId' => $playerId,
			'playerUsername' => $playerUsername,
			'ui' => array(
				// 'logoutUrl' => $logoutUrl,
				// 'loginUrl' => $loginUrl,
				// 'loginIframeName' => '_login_iframe',
				// 'loginContainer' => '#_player_login_area',
				// 'playerInfoContainer' => '#_player_info_area',
				// 'playerRegisterContainer' => '._player_register_area',
				// 'playerPromoContainer' => '#_promo_area',
				// 'ptGameType' => '.ptgame-titles',
				// 'ptGame' => '.products',
			),
			'defaultErrorMessage' => $defaultErrorMessage,
			'langText' => array('button_login' => lang("lang.logIn"),
				'form_field_username' => lang('form.field.username'),
				'form_field_password' => lang('form.field.password'),
				'form_register' => lang('lang.register'),
				'header_trial_game' => lang('header.trial_game'),
				'button_logout' => lang('header.logout'),
				'header_memcenter' => lang('header.memcenter'),
				'header_deposit' => lang('header.deposit'),
				'header_withdrawal' => lang('header.withdrawal'),
				'header_mainwallet' => lang('header.mainwallet'),
				'header_information' => lang('header.information'),
			),
			// 'templates' => array(
			// 	'login_template' => $site->login_template,
			// 	'logged_template' => $site->logged_template,
			// 	'pt_game_type_template' => $site->pt_game_type_template,
			// 	'pt_game_template' => $site->pt_game_template,
			// ),
		), JSON_PRETTY_PRINT
		);

		//js path
		$paths = json_encode(array(
			'json' => 'json3.min',
			'jquery' => 'jquery-1.11.3.min',
			'underscore' => 'underscore-min',
			'snackbar' => 'snackbar.min',
			// 'domReady' => 'domReady',
			// 'popup' => 'jquery.bpopup',
			// 'jqueryMessage' => 'jquery.ba-postmessage',
		)
		);

		$preloadJS = $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/utils.js');
		$preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/swfobject.js');
		$preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/web_socket.js');
		$preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/web_push.js');
		$preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/render-ui.js');
		$preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/domReady.js');
		$preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/call_api.js');
		// $preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/jquery-1.11.3.min.js');
		$preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/jquery-private.js');
		$preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/jquery.ba-postmessage.js');
		$preloadJS = $preloadJS . "\n" . $this->utils->getFileFromCache(APPPATH . '/../public/resources/player/jquery.bpopup.js');

		$loadjs_timeout = $this->config->item('loadjs_timeout');

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
		$js = <<<EOF
require.config({
	baseUrl:'{$assetBaseUrl}',
	paths: {$paths},
	waitSeconds: {$loadjs_timeout},
	map: {
		//private jquery, never conflict with public jquery
		'*': { 'jquery': 'jquery-private' },
		'jquery-private': { 'jquery': 'jquery' }
	}
});
//dynamic variables: login status or any needed variables
define('variables',['jquery'], function($){
	return {$variables};
});

{$preloadJS}

define(['jquery', 'utils', 'web_push', 'call_api','variables', 'iframe_player'], function($, utils, webPush, callApi,variables, iframe_player){
	utils.init();
	// utils.initMessage();
	//create login box
	// renderUI.init();
	//call api
	callApi.init();
	//init webPush
	if(variables.enabled_web_push){
		webPush.init();
	}

	iframe_player.init();
	utils.safelog('iframe main done');
});

EOF;

		$this->returnJS($js);

	}

	function terms_and_conditions() {
		$this->loadTemplate('Terms and Conditions', '', '', 'home');

		$this->template->write_view('main_content', 'online/view_terms_and_conditions');
		$this->template->render();
	}

	const TEMPORARY_DEPOSIT_FOLDER_NAME = 'deposit_temp';
	const TEMP_PLAYER_UPLOAD_FOLDER_NAME = 'temp_player_upload_';
	const UPLOADED_DEPOSIT_SLIPS_FOLDER = 'deposit_slips';

	public function uploadDepositSlip() {

		$uploader = $this->utils->loadSimpleAjaxUploader();
		$uploader->sizeLimit = 10485760000;
		$uploader->allowedExtensions = array('jpg');
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
		}
		$this->returnJsonResult(array('success' => true, 'imageName' => $preferredImageName, 'image_path' => site_url() . 'upload/' . $depositTemp . '/' . $dirname . '/'));
	}

	public function removeImage($image_name) {
		$this->session->unset_userdata('player_deposit_image');
		$playerId = $this->authentication->getPlayerId();
		$upload_dir = $this->utils->getUploadPath();
		$depositTemp = self::TEMPORARY_DEPOSIT_FOLDER_NAME;
		$dirname = self::TEMP_PLAYER_UPLOAD_FOLDER_NAME . $playerId;
		$imageFile = $upload_dir . $depositTemp . '/' . $dirname . '/' . $image_name;
		if (!unlink($imageFile)) {
			$this->returnJsonResult(array('success' => false, 'msg' => 'unable to delete file'));
		} else {
			$this->returnJsonResult(array('success' => true, 'msg' => 'deleted'));
		}
	}

	public function transferBankslipImage() {
		$playerId = $this->authentication->getPlayerId();
		$upload_dir = $this->utils->getUploadPath();
		$depositTemp = self::TEMPORARY_DEPOSIT_FOLDER_NAME;
		$dirname = self::TEMP_PLAYER_UPLOAD_FOLDER_NAME . $playerId;

		$currentUploadImg = $this->session->userdata('player_deposit_image');

		if (!empty($currentUploadImg)) {
			$tempImgPath = $upload_dir . $depositTemp . '/' . $dirname . '/' . $currentUploadImg;
			$depositSlipDir = self::UPLOADED_DEPOSIT_SLIPS_FOLDER;
            $this->utils->addSuffixOnMDB($depositSlipDir);

			if (!file_exists($upload_dir . '/' . $depositSlipDir)) {
				mkdir($upload_dir . '/' . $depositSlipDir, 0777, true);
			}

			$this->session->unset_userdata('player_deposit_image');

			if (!copy($tempImgPath, $upload_dir . $depositSlipDir . '/' . $currentUploadImg)) {
				///	echo $upload_dir.$depositSlipDir.'/';
				throw new Exception(lang('Image not transferred successfully'));
			}

		}
	}

	public function emptyPlayerTempUploadFolder() {
		$playerId = $this->authentication->getPlayerId();
		$upload_dir = $this->utils->getUploadPath();
		$depositTemp = self::TEMPORARY_DEPOSIT_FOLDER_NAME;
		$dirname = self::TEMP_PLAYER_UPLOAD_FOLDER_NAME . $playerId;

		foreach (glob($upload_dir . $depositTemp . '/' . $dirname . "/*.*") as $filename) {
			if (is_file($filename)) {
				unlink($filename);
			}
		}

		$dir = $upload_dir . $depositTemp . '/' . $dirname;
		if (file_exists($dir)) {
			@rmdir($upload_dir . $depositTemp . '/' . $dirname);
		}

	}

	/**
	 * Personal Information Update History
	 * @author kaiser.dapar 2015-09-07
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
	 * transfer form
	 */
	public function transfer() {
		$this->load->model(array('static_site', 'player_model', 'external_system',
			'wallet_model', 'transactions'));
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
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/transfer', $data);
		$this->template->render();
	}

	public function playerGameHistory($dateFrom) {
		if (empty($dateFrom) || !isset($dateFrom)) {
			$dateFrom = date('Y-m-d');
		}

		$player_id = $this->authentication->getPlayerId();
		$this->load->model(array('game_logs'));
		$data['playerGamesHistoryByDay'] = $this->game_logs->getPlayerTotalGamesHistoryByDay($player_id, $dateFrom)['data'];

		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_game_history', $data);
	}

	public function playerGameHistoryV2($segment, $from = null , $to = null) {
		$this->load->model(array('game_logs','report_model'));
		$player_id = $this->authentication->getPlayerId();
		$search = null;
		if ($from && $to) {
			$search = array(
				'from' => urldecode($from),
				'to' => urldecode($to),
			);
		}

		$data['count_all'] = $this->game_logs->playerGamesHistoryWLimit($player_id, null, null, $search, true);

		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '5';
		$config['num_links'] = '1';
	    $config['base_url'] = "javascript:playerGameHistoryV2('%s')";
		$config['callback_link'] = true;

		$config['first_tag_open'] = '<li class="page_first">';
		$config['last_tag_open'] = '<li class="page_last">';
		$config['next_tag_open'] = '<li class="page_next">';
		$config['prev_tag_open'] = '<li class="page_preview">';
		$config['num_tag_open'] = '<li class="page_number">';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_close'] = '</li>';
		$config['num_tag_close'] = '</li>';
		$config['anchor_class'] = 'class="my-pagination" ';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$config['next_link'] = lang('Next Page');
		$config['prev_link'] = lang('Prev Page');
		$config['last_link'] = lang('Last Page');
		$config['first_link'] = lang('First Page');
		//$this->utils->debug_log('===============playerGameHistoryV2 count_all===========', $data['count_all']);
		$this->pagination->initialize($config);

		$data['create_links'] = $this->pagination->create_links();

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);

		$data['gameHistory'] = $this->game_logs->playerGamesHistoryWLimit($player_id, $config['per_page'], $segment, $search);
		$data['gameHistoryNoLimit'] = $this->game_logs->playerGamesHistoryWLimit($player_id, null, null, $search);

		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_game_history', $data);
	}

	public function getPlayerReferral($segment, $from = null , $to = null) {
		$this->load->model(array('player_model'));
		$input = $this->input->post();
		$player_id = $this->authentication->getPlayerId();

		$search = null;
		if ($from && $to) {
			$search = array(
				'from' => urldecode($from),
				'to' => urldecode($to),
			);
		}

		$data['count_all'] = $this->player_model->getPlayerReferralWLimit($player_id, null, null, $search, true);

		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '5';
		$config['num_links'] = '1';
	    $config['base_url'] = "javascript:getPlayerReferral('%s')";
		$config['callback_link'] = true;

		$config['first_tag_open'] = '<li class="page_first">';
		$config['last_tag_open'] = '<li class="page_last">';
		$config['next_tag_open'] = '<li class="page_next">';
		$config['prev_tag_open'] = '<li class="page_preview">';
		$config['num_tag_open'] = '<li class="page_number">';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_close'] = '</li>';
		$config['num_tag_close'] = '</li>';
		$config['anchor_class'] = 'class="my-pagination" ';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$config['next_link'] = lang('Next Page');
		$config['prev_link'] = lang('Prev Page');
		$config['last_link'] = lang('Last Page');
		$config['first_link'] = lang('First Page');
		//$this->utils->debug_log('===============getPlayerReferral count_all===========', $data['count_all']);
		$this->pagination->initialize($config);

		$data['create_links'] = $this->pagination->create_links();

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);

		$data['friendReferralHistory'] = $this->player_model->getPlayerReferralWLimit($player_id, $config['per_page'], $segment, $search);

		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_friend_referral', $data);

	}
}