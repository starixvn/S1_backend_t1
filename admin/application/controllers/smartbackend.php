<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

/**
 *
 * @deprecated
 *
 */
class Smartbackend extends BaseController {
	//get links
	const URL_LOGIN = 1;
	const URL_LOGOUT = 2;
	const URL_DASHBOARD_UPDATE_INFO = 3;
	const URL_DASHBOARD_MAKE_DEPOSIT = 4;
	const URL_DASHBOARD_MAKE_WITHDRAW = 5;
	const URL_DASHBOARD_BANK_INFO = 6;
	const URL_DASHBOARD_PLAYER_PROMO = 7;
	const URL_DASHBOARD_PROMO_LIST = 8;
	const URL_DASHBOARD_RESET_PASSWORD = 9;
	const URL_DASHBOARD_VIEW_REPORT = 10;
	const URL_DASHBOARD_PLAY_GAMES = 11;
	//player info
	const PLAYER_PERSONALINFO = 1;
	const PLAYER_MAINWALLET = 2;
	const PLAYER_SUBWALLET = 3;

	function __construct() {
		parent::__construct();
		$this->load->library('authentication');
	}

	public function pub_js() {
		$lib = $this->utils->getSmartbackendLib(array('id' => $playerId));
		$js = <<<EOF
		$lib
EOF;
		$this->returnJS($js);
	}

	/**
	 * Will get url link
	 *
	 * @param urlType int
	 * @param secretKey str
	 *
	 * @return json
	 */
	public function get_url($urlType, $secretKey) {
		if (!$this->verify_secret_key($secretKey)) {
			return $this->returnJsonResult(array("error" => "Invalid Key!"));
		}

		switch ($urlType) {
		case self::URL_LOGIN:$url = site_url('iframe_module/iframe_login');
			break;
		case self::URL_LOGOUT:$url = site_url('iframe_module/iframe_logout');
			break;
		case self::URL_DASHBOARD_UPDATE_INFO:$url = site_url('iframe_module/iframe_playerSettings');
			break;
		case self::URL_DASHBOARD_MAKE_DEPOSIT:$url = site_url('iframe_module/iframe_makeDeposit');
			break;
		case self::URL_DASHBOARD_MAKE_WITHDRAW:$url = site_url('iframe_module/iframe_viewWithdraw');
			break;
		case self::URL_DASHBOARD_BANK_INFO:$url = site_url('iframe_module/iframe_bankDetails');
			break;
		case self::URL_DASHBOARD_PLAYER_PROMO:$url = site_url('iframe_module/iframe_myPromo');
			break;
		case self::URL_DASHBOARD_PROMO_LIST:$url = site_url('iframe_module/iframe_promos');
			break;
		case self::URL_DASHBOARD_RESET_PASSWORD:$url = site_url('iframe_module/iframe_changePassword');
			break;
		case self::URL_DASHBOARD_VIEW_REPORT:$url = site_url('/player_center2/report');
			break;
		case self::URL_DASHBOARD_PLAY_GAMES:$url = site_url('');
			break;
		default:$url = site_url('');
			break;
		}
		return $this->returnJsonResult(array("url" => $url));
	}

	/**
	 * Will verify secret key
	 *
	 * @param secreKey str
	 *
	 * @return boolean
	 */
	private function verify_secret_key($key) {
		if ($key != $this->utils->getConfig('smartbackend_rest_key')) {
			return false;
		}
		return true;
	}

	/**
	 * Will get player info
	 *
	 * @param infoType int
	 * @param playerId date
	 * @param secretKey str
	 *
	 * @return json
	 */
	public function get_player_info($infoType, $playerId, $secretKey) {
		if (!$this->verify_secret_key($secretKey)) {
			return $this->returnJsonResult(array("error" => "Invalid Key!"));
		}

		switch ($infoType) {
		case self::PLAYER_PERSONALINFO:
			$playerInfo = $this->utils->get_player_info($playerId);
			return $this->returnJsonResult($playerInfo);
			break;

		case self::PLAYER_MAINWALLET:
			$mainWallet = $this->utils->get_main_wallet($playerId)['totalBalanceAmount'];
			return $this->returnJsonResult(array("main_wallet" => $mainWallet));
			break;

		case self::PLAYER_SUBWALLET:
			$subWallet = $this->utils->get_sub_wallet($playerId);
			return $this->returnJsonResult($subWallet);
			break;

		default:
			return $this->returnJsonResult(array("warning" => "Invalid Param [info_type]!"));
			break;
		}
	}

	/**
	 * Will get player balance
	 *
	 * @return json
	 */
	public function get_player_balance() {
		$playerId = $this->authentication->getPlayerId();
		if (!empty($playerId)) {
			$this->load->model(array('wallet_model'));
			$balDetails = $this->wallet_model->getBalanceDetails($playerId);
			return $this->returnJsonpResult($balDetails);

			// $mainWallet = $this->utils->get_main_wallet($playerId);
			// $subWallet = $this->utils->get_sub_wallet($playerId);
			// $frozen = $this->utils->get_player_frozen_amount($playerId);

			// return $this->returnJsonpResult(
			// 	array(
			// 		"main_wallet" => $mainWallet,
			// 		"sub_wallet" => $subWallet,
			// 		"frozen" => $frozen,
			// 		"total_balance"=>$totalBalance,
			// 	)
			// );
		} else {
			return $this->returnJsonpResult(null);
		}

	}

	/**
	 * Will get jsonp
	 *
	 * @return jsonp
	 */
	public function get_jsonp($arr = null, $callback = null) {
		return $this->returnJsonpResult($arr, $callback);
	}

	/**
	 * Will get player bet info
	 *
	 * @param type int
	 * @param dateTimeFrom date
	 * @param dateTimeTo date
	 * @param gamePlatformId int
	 * @param gameDescriptionId int
	 * @param secretKey str
	 *
	 * @return json
	 */
	public function get_player_bet($type, $dateTimeFrom, $dateTimeTo, $gamePlatformId, $gameDescriptionId, $secretKey) {
		if (!$this->verify_secret_key($secretKey)) {
			return $this->returnJsonResult(array("error" => "Invalid Key!"));
		}

		$playerBetInfo = $this->utils->get_players_betinfo($type, $dateTimeFrom, $dateTimeTo, $gamePlatformId, $gameDescriptionId);
		return $this->returnJsonResult($playerBetInfo);
	}

	/**
	 * Will get top ten players
	 *
	 * @param gameCode int
	 * @param gameDescriptionId int
	 *
	 * @return json
	 */
	public function get_top_ten_win_players($gameDescriptionId = null, $gameCode = null, $gamePlatformId = null) {
		$this->load->model(array('total_player_game_hour', 'game_description_model'));

		if ($gameDescriptionId == 'null') {
			$gameDescriptionId = null;
		}
		if ($gameCode == 'null') {
			$gameCode = null;
		}
		if ($gamePlatformId == 'null') {
			$gamePlatformId = null;
		}

		if (empty($gameDescriptionId)) {
			if (!empty($gameCode) && !empty($gamePlatformId)) {
				$gameDescriptionId = $this->game_description_model->getGameDescriptionIdByGameCode($gameCode, $gamePlatformId);
			}
		}
		// if (empty($gameDescriptionId)) {
		// 	return null;
		// }
		$topInfo = $this->CI->total_player_game_hour->getTopTenWinPlayers($gameDescriptionId);

		// $topInfo = $this->utils->get_top_ten_win_players($gameDescriptionId, $gameCode, $gamePlatformId);
		// $this->utils->printLastSQL();
		return $this->returnJsonpResult($topInfo);
	}

	/**
	 * Will get monthly top players by gameCode
	 *
	 * @param gameCode int
	 *
	 * @return json
	 */
	public function get_monthly_top_win_players($gamePlatformId = null, $resultLimit = null, $yearMonth = null) {
		$this->load->model(array('total_player_game_month'));
		$topInfo = $this->CI->total_player_game_month->getMonthlyTopWinPlayers($gamePlatformId, $resultLimit, $yearMonth);
		return $this->returnJsonpResult($topInfo);
	}

	/**
	 * Will get top ten players for last 7 days
	 *
	 * @param gameCode int
	 * @param gameDescriptionId int
	 * @return json
	 */
	public function get_newest_ten_win_players($gameDescriptionId = null, $gameCode = null, $gamePlatformId = null) {
		$this->load->model(array('total_player_game_hour', 'game_description_model'));
		if ($gameDescriptionId == 'null') {
			$gameDescriptionId = null;
		}
		if ($gameCode == 'null') {
			$gameCode = null;
		}
		if ($gamePlatformId == 'null') {
			$gamePlatformId = null;
		}

		if (empty($gameDescriptionId)) {
			if (!empty($gameCode) && !empty($gamePlatformId)) {
				$gameDescriptionId = $this->game_description_model->getGameDescriptionIdByGameCode($gameCode, $gamePlatformId);
			}
		}
		// if (empty($gameDescriptionId)) {
		// 	return null;
		// }
		$info = $this->total_player_game_hour->getNewestTenWinPlayers($gameDescriptionId);

		// $info = $this->utils->get_newest_ten_win_players($gameDescriptionId, $gameCode, $gamePlatformId);
		// $this->utils->printLastSQL();
		return $this->returnJsonpResult($info);
	}

	public function get_log_info() {
		$this->load->library(array('language_function'));
		// $this->utils->debug_log('sesssion status', $this->session->userdata('status'));
		$playerId = null;
		$playerUsername = null;
		$lang = null;
		if ($this->authentication->isLoggedIn()) {

			$playerId = $this->authentication->getPlayerId();
			$playerUsername = $this->authentication->getUsername();
			$currentLang = $this->language_function->getCurrentLanguage(); //  $this->session->userdata('currentLanguage');

			//lang for PT
			if ($currentLang == '1') {
				$lang = 'en';
			} else {
				$lang = 'zh-cn';
			}
		}
		// $this->load->model(array('player_model'));

		$this->returnJsonpResult(array('username' => $playerUsername, 'lang' => $lang));
	}

	/**
	 * add favorite game to player
	 *
	 * @param gameDescriptionId int
	 *
	 * @return json
	 */
	public function add_favorite_game_to_player($gameDescriptionId) {
		if (!$this->utils->check_if_valid_game_desc_id($gameDescriptionId)) {
			return false;
		}
		$playerId = $this->authentication->getPlayerId();
		if (!empty($playerId)) {
			return $this->returnJsonpResult($this->utils->add_favorite_game_to_player($gameDescriptionId, $playerId));
		} else {
			return $this->returnJsonpResult(null);
		}
	}

	/**
	 * remove favorite game from player
	 *
	 * @param gameDescriptionId int
	 *
	 * @return json
	 */
	public function remove_favorite_game_from_player($gameDescriptionId) {
		if (!$this->utils->check_if_valid_game_desc_id($gameDescriptionId)) {
			return false;
		}
		$playerId = $this->authentication->getPlayerId();
		if (!empty($playerId)) {
			return $this->returnJsonpResult($this->utils->remove_favorite_game_from_player($gameDescriptionId, $playerId));
		} else {
			return $this->returnJsonpResult(null);
		}
	}

	/**
	 * add favorite game code to player
	 *
	 * @param gameCode int
	 *
	 * @return json
	 */
	public function add_favorite_game_code_to_player($gameCode) {
		if (!$this->utils->check_if_valid_game_code($gameCode)) {
			return $this->returnJsonpResult(false);
		}
		$playerId = $this->authentication->getPlayerId();
		if (!empty($playerId)) {
			return $this->returnJsonpResult($this->utils->add_favorite_game_code_to_player($gameCode, $playerId));
		} else {
			return $this->returnJsonpResult(false);
		}
	}

	/**
	 * remove favorite game code from player
	 *
	 * @param gameDescriptionId int
	 *
	 * @return json
	 */
	public function remove_favorite_game_code_from_player($gameCode) {
		if (!$this->utils->check_if_valid_game_code($gameCode)) {
			return $this->returnJsonpResult(false);
		}
		$playerId = $this->authentication->getPlayerId();
		if (!empty($playerId)) {
			return $this->returnJsonpResult($this->utils->remove_favorite_game_code_from_player($gameCode, $playerId));
		} else {
			return $this->returnJsonpResult(false);
		}
	}

	/**
	 * will get all player's favorite games
	 *
	 * @param gameCode int
	 *
	 * @return json
	 */
	public function get_player_favorite_games() {
		$playerId = $this->authentication->getPlayerId();
		if (!empty($playerId)) {
			return $this->returnJsonpResult($this->utils->get_player_favorite_games($playerId));
		} else {
			return $this->returnJsonpResult(false);
		}
	}

	public function is_received_bonus() {
		$playerId = $this->authentication->getPlayerId();
		if (!empty($playerId)) {
			$this->load->model(array('random_bonus_history'));
			$bonusExistsToday = $this->random_bonus_history->isPlayerBonusExistsTodayForBonusModeCounting($playerId);
			return $this->returnJsonpResult($bonusExistsToday);
		} else {
			return $this->returnJsonpResult(false);
		}
	}

	public function count_random_bonus($promo_category_id = null) {
		$this->load->model(array('transactions'));
		return $this->returnJsonpResult($this->transactions->countRandomBonus($promo_category_id));

	}
	/**
	 * date format: yyyy-mm-dd
	 * http://og.local/smartbackend/query_bet_amount/afftest007?member=test002&start_date=2016-04-01&end_date=2016-04-02
	 */
	public function query_bet_amount($aff_username) {
		$start_date = $this->input->get('start_date');
		$end_date = $this->input->get('end_date');
		$playerUsername = $this->input->get('member');
		$gamePlatformId = $this->input->get('gamePlatformId');
		$this->load->model(array('affiliatemodel', 'player_model'));
		if (empty($aff_username) || empty($playerUsername) || empty($start_date) || empty($end_date)) {
			return show_error('Bad Request', 400);
			// $belong_to=false;
		}
		$aff = $this->affiliatemodel->getAffiliateByUsername($aff_username);
		$player = $this->player_model->getPlayerByUsername($playerUsername);
		if (empty($aff) || empty($player)) {
			// return show_error('Bad Request', 400);
			$belong_to = false;
		} else {
			$belong_to = $aff->affiliateId == $player->affiliateId;
		}

		$result = array('belong_to' => $belong_to, 'belong_to_str' => $belong_to ? 'true' : 'false');
		$bet_amount = 0;
		if ($belong_to) {
			$dateTimeFrom = $start_date . ' 00:00:00';
			$dateTimeTo = $end_date . ' 23:59:59';
			$this->load->model(array('game_logs'));
			//return betting amount
			list($totalBet, $totalWin, $totalLoss) = $this->game_logs->sumBetsWinsLossByDatetime($player->playerId, $dateTimeFrom, $dateTimeTo, $gamePlatformId);
			$result['bet_amount'] = floatval($totalBet);
			$bet_amount = $this->utils->roundCurrencyForShow($totalBet);
		}

		// return $this->returnJsonpResult(($belong_to ? 'OK' : 'NO') . '||' . $bet_amount);
		return $this->returnJsonpResult($result);

	}
}