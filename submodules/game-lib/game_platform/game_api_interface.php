<?php
/**
 * Defines general behavior of game API classes.
 *
 * General behaviors include:
 * * Create Player
 * * Check Player Information
 * * Get Password
 * * Change Password
 * * Block/Unblock Player
 * * Deposit to game
 * * Withdraw from game
 * * Login/Logout
 * * Update Player Information
 * * Check Player Balance
 * * Check Player Daily Balance
 * * Check Game Records
 * * Check Login Status
 * * Check Transaction
 * * Check Forward Game
 * * Synchronize Game Records
 * * Synchronize Original Game Logs
 * * Synchronize Lost and Found
 * * Synchronize Covert Result To database
 * * Synchronize and Merge Interface to Game logs
 * * Synchronize  Total Stats
 * * Check Batch Create Player
 * * Batch Check Player Information
 * * Batch Block Player
 * * Batch Unblock Player
 * * Batch Deposit To Game
 * * Batch Withdraw from Game
 * * Batch Login
 * * Batch Logout
 * * Batch Update Player Information
 * * Batch Check Player Balance
 * * Batch Total Betting amount
 * * Batch Query Transaction
 * * Check if player exist
 * * check if player is blocked
 * * check login token
 * * Reset Player
 *
 * The functions implemented by child class:
 * * Populating game form parameters
 * * Handling callbacks
 *
 * @deprecated 2.0
 *
 *
 * @see Redirect redirect to game page
 *
 * @category Game_platform
 * @version 1.8.10 
 * @copyright 2013-2016 TripleOneTech
 */
interface Game_api_interface {

	/**
	 * @param string playerName
	 * @param int playerId
	 * @param string password
	 * @param string email
	 * @param array extra 'source'=> Game_provider_auth::SOURCE_REGISTER or SOURCE_BATCH
	 * @return array ("success"=>boolean, "playerInfo"=>)
	 */
	public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null);
	/**
	 * @param string playerName
	 * @return array ("success"=>boolean, "password"=>)
	 */
	public function queryPlayerInfo($playerName);
	/**
	 * @param string playerName
	 * @return array ("success"=>boolean, "password"=>)
	 */
	public function getPassword($playerName);
	/**
	 * @param string playerName
	 * @param string oldPassword
	 * @param string newPassword
	 * @return array ("success"=>boolean)
	 */
	public function changePassword($playerName, $oldPassword, $newPassword);
	/**
	 * @param string playerName
	 * @return array ("success"=>boolean)
	 */
	public function blockPlayer($playerName);
	/**
	 * @param string playerName
	 * @return array ("success"=>boolean)
	 */
	public function unblockPlayer($playerName);
	/**
	 * @param string playerName
	 * @param double amount
	 * @return array ("success"=>boolean, 'external_transaction_id'=>string)
	 */
	public function depositToGame($playerName, $amount, $transfer_secure_id=null);
	/**
	 * @param string playerName
	 * @param double amount
	 * @return array ("success"=>boolean, 'external_transaction_id'=>string)
	 */
	public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null);
	/**
	 * @param string playerName
	 * @param string password
	 * @return array ("success"=>boolean)
	 */
	public function login($playerName, $password = null);
	/**
	 * @param string playerName
	 * @param string password
	 * @return array ("success"=>boolean)
	 */
	public function logout($playerName, $password = null);
	/**
	 * @param string playerName
	 * @param array infos
	 * @return array ("success"=>boolean)
	 */
	public function updatePlayerInfo($playerName, $infos);
	/**
	 * @param string playerName
	 * @param DateTime dateFrom
	 * @param DateTime dateTo
	 * @return array ("success"=>boolean, "balance"=>)
	 */
	public function queryPlayerBalance($playerName);
	/**
	 * @param string playerName
	 * @param DateTime dateFrom
	 * @param DateTime dateTo
	 * @return array ("success"=>boolean, "balanceList"=>array(date=>balance))
	 */
	public function queryPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null);
	/**
	 * @param DateTime dateFrom
	 * @param DateTime dateTo
	 * @param string playerName
	 * @return array ("success"=>boolean, "gameRecords"=>array(row of "game_logs"))
	 */
	public function queryGameRecords($dateFrom, $dateTo, $playerName = null);
	/**
	 * @param string playerName
	 * @return array ("success"=>boolean, "loginStatus"=>)
	 */
	public function checkLoginStatus($playerName);
	/**
	 * @param string playerName
	 * @param DateTime dateFrom
	 * @param DateTime dateTo
	 * @return array ("success"=>boolean, "bettingAmount"=>)
	 */
	// public function totalBettingAmount($playerName, $dateFrom, $dateTo);
	/**
	 * @param string transactionId
	 * @param array extra name=>value, currency=>
	 * @return array ("success"=>boolean, "transactionInfo"=>)
	 */
	public function queryTransaction($transactionId, $extra);
	/**
	 * @param string playerName
	 * @param array extra gameType=>,lang=>, currency=> ...
	 * @return array ("success"=>boolean, "url"=>string, "params"=>array)
	 */
	public function queryForwardGame($playerName, $extra);

	/**
	 * @return array ("success"=>boolean)
	 */
	public function syncGameRecords($dateTimeFrom, $dateTimeTo, $playerName = null, $gameName = null);

	public function syncOriginalGameLogs($token);
	public function syncLostAndFound($token);
	/**
	 * convert original result to db
	 *
	 * @param string token to get syncInfo
	 */
	public function syncConvertResultToDB($token);
	/**
	 * merge to game logs
	 *
	 * @param string token to get syncInfo
	 */
	public function syncMergeToGameLogs($token);
	/**
	 * total stats
	 *
	 * @param string token to get syncInfo
	 */
	public function syncTotalStats($token);
	/**
	 * only for sync function
	 *
	 */
	public function putValueToSyncInfo($token, $key, $value);
	public function getValueFromSyncInfo($token, $key);
	public function saveSyncInfoByToken($token, $dateTimeFrom = null, $dateTimeTo = null, $playerName = null, $gameName = null);
	public function clearSyncInfo($token);

	/**
	 * @return array ("success"=>boolean)
	 */
	public function syncBalance($dateTimeFrom, $dateTimeTo, $playerName = null, $gameName = null);

	/**
	 * @param string playerName
	 * @param array params
	 * @return array ("success"=>boolean, ...)
	 */
	public function callApi($apiName, $params);

	/**
	 * @param array playerInfos array of playerName=>playerInfo
	 * @return array ("success"=>boolean, "playerCreated"=>array(playerName=>boolean))
	 */
	public function batchCreatePlayer($playerInfos);
	/**
	 * @param array playerNames
	 * @return array ("success"=>boolean, "playerInfos"=>array(playerName=>playerInfo))
	 */
	public function batchQueryPlayerInfo($playerNames);
	/**
	 * @param array playerNames
	 * @return array ("success"=>boolean, "playerBlocked"=>array(playerName=>boolean))
	 */
	public function batchBlockPlayer($playerNames);
	/**
	 * @param array playerNames
	 * @return array ("success"=>boolean, "playerUnblocked"=>array(playerName=>boolean))
	 */
	public function batchUnblockPlayer($playerNames);
	/**
	 * @param array playerDepositInfos array(playerName=>amount)
	 * @return array ("success"=>boolean, "playerDeposited"=>array(playerName=>boolean))
	 */
	public function batchDepositToGame($playerDepositInfos);
	/**
	 * @param array playerWithdrawInfos array(playerName=>amount)
	 * @return array ("success"=>boolean, "playerWithdrawed"=>array(playerName=>boolean))
	 */
	public function batchWithdrawFromGame($playerWithdrawInfos);
	/**
	 * @param array playerNames
	 * @return array ("success"=>boolean, "playerLoggedIn"=>array(playerName=>boolean))
	 */
	public function batchLogin($playerNames);
	/**
	 * @param array playerNames
	 * @return array ("success"=>boolean, "playerLoggedOut"=>array(playerName=>boolean))
	 */
	public function batchLogout($playerNames);
	/**
	 * @param array playerNames
	 * @return array ("success"=>boolean, "playerUpdated"=>array(playerName=>boolean))
	 */
	public function batchUpdatePlayerInfo($playerInfos);
	/**
	 * @param string playerName
	 * @return array ("success"=>boolean, "balances"=>array(playerName=>balance))
	 */
	public function batchQueryPlayerBalance($playerNames, $syncId = null);
	/**
	 * @param string playerName
	 * @return array ("success"=>boolean, "totalBettingAmount"=>array(playerName=>bettingAmount))
	 */
	public function batchTotalBettingAmount($playerNames);
	/**
	 * @param string playerName
	 * @return array ("success"=>boolean, "transactions"=>array(transactionId=>transactionInfo))
	 */
	public function batchQueryTransaction($transactionIds);
	/**
	 * @param string playerName
	 * @return array ("success"=>boolean, "exist"=>boolean)
	 */
	public function isPlayerExist($playerName);
	/**
	 *
	 * check block status from db
	 *
	 */
	public function isBlocked($playerUsername);

	public function checkLoginToken($playerName, $token);

	public function resetPlayer($playerName);
}
