<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';

/*
 * Dummy game API
 */
class Game_api_dummy extends Abstract_game_api {
	public function getPlatformCode() {
		return DUMMY_GAME_API;
	}

	# -- Implementation of API functions --
	public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
        $this->utils->debug_log("Invoked in dummy game API", $playerName, $playerId, $password, $email, $extra);
        parent::createPlayer($playerName, $playerId, $password, $email, $extra);
        return $this->returnUnimplemented();
	}

	public function updatePlayerInfo($playerName, $infos) {
		$this->utils->debug_log("Invoked in dummy game API", $playerName, $infos);
		return $this->returnUnimplemented();
	}

	public function queryPlayerInfo($playerName) {
		$this->utils->debug_log("Invoked in dummy game API", $playerName);
		return $this->returnUnimplemented();
	}

	/**
	 * query player balance
	 *
	 * @author Elvis_Chen<elvis.chen@tripleonetech.net>
	 * @since 1.0.0 Elvis_Chen: Implement function
	 *
	 * @param string $playerName
	 * @return array
	 */
	public function queryPlayerBalance($playerName) {
		$this->utils->debug_log("Invoked in dummy game API", $playerName);

		$this->CI->load->model('player_model');
		$player = $this->CI->player_model->getPlayerByUsername($playerName);

		$subwallets = $this->CI->utils->get_sub_wallet($player->playerId);

		$result = $this->returnUnimplemented();

		foreach($subwallets as $subwallet){
			if($subwallet['game'] === $this->getSystemInfo('_system_code')){
				$result['success'] = true;
				$result['balance'] = $subwallet['totalBalanceAmount'];
			}
		}

		return $result;
	}

	public function depositToGame($playerName, $amount, $transfer_secure_id=null) {
		$this->utils->debug_log("Invoked in dummy game API", $playerName, $amount);
		return $this->returnUnimplemented();
	}

	public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null) {
		$this->utils->debug_log("Invoked in dummy game API", $playerName, $amount);
		return $this->returnUnimplemented();
	}

	public function login($playerName, $password = null) {
		$this->utils->debug_log("Invoked in dummy game API", $playerName, $password);
		return $this->returnUnimplemented();
	}

	public function queryForwardGame($playerName, $extra = array()) {
		$this->utils->debug_log("Invoked in dummy game API", $playerName, $extra);
		return $this->returnUnimplemented();
	}

	public function logout($playerName, $password = null) {
		$this->utils->debug_log("Invoked in dummy game API", $playerName, $password);
		return $this->returnUnimplemented();
	}

	public function checkLoginStatus($playerName) {
		$this->utils->debug_log("Invoked in dummy game API", $playerName);
		return $this->returnUnimplemented();
	}

	public function syncOriginalGameLogs($token = null) {
		$this->utils->debug_log("Invoked in dummy game API", $token);
		return $this->returnUnimplemented();
	}

	public function syncMergeToGameLogs($token = null) {
		$this->utils->debug_log("Invoked in dummy game API", $token);
		return $this->returnUnimplemented();
	}

	public function generateUrl($apiName, $params) {
		$this->utils->debug_log("Invoked in dummy game API", $apiName, $params);
		return $this->returnUnimplemented();
	}

	public function afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText = null, $extra = null, $resultObj = null) {
		$this->utils->debug_log("Invoked in dummy game API", $apiName, $params, $responseResultId, $resultText, $statusCode, $statusText, $extra, $resultObj);
		return $this->returnUnimplemented();
	}

	public function changePassword($playerName, $oldPassword, $newPassword) {
		$this->utils->debug_log("Invoked in dummy game API", $playerName, $oldPassword, $newPassword);
		return $this->returnUnimplemented();
	}
	public function queryPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null) {
		$this->utils->debug_log("Invoked in dummy game API", $playerName, $playerId, $dateFrom, $dateTo);
		return $this->returnUnimplemented();
	}
	public function queryGameRecords($dateFrom, $dateTo, $playerName = null) {
		$this->utils->debug_log("Invoked in dummy game API", $dateFrom, $dateTo, $playerName);
		return $this->returnUnimplemented();
	}
	public function totalBettingAmount($playerName, $dateFrom, $dateTo) {
		$this->utils->debug_log("Invoked in dummy game API", $playerName, $dateFrom, $dateTo);
		return $this->returnUnimplemented();
	}
	public function queryTransaction($transactionId, $extra) {
		$this->utils->debug_log("Invoked in dummy game API", $transactionId, $extra);
		return $this->returnUnimplemented();
	}
}
