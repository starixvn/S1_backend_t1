<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Player Manager
 *
 * Player Manager library for FHI OG System
 *
 * @package		Player Manager
 * @author		Johann Merle
 * @version		1.0.0
 */

class Player_manager {
	private $error = array();

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array('salt', 'game_pt_api', 'game_ag_api', 'game_platform/game_platform_manager'));
		$this->ci->load->model(array('player', 'gameapi', 'transactions'));
	}

	/**
	 * Will get all players
	 *
	 * @param 	int
	 * @param 	int
	 * @return	array
	 */
	public function getAllPlayers($sort_by, $in, $limit, $offset) {
		return $this->ci->player->getAllPlayers($sort_by, $in, $limit, $offset);
	}

	/**
	 * Will get all players
	 *
	 * @param 	int
	 * @param 	int
	 * @return	array
	 */
	public function searchAllPlayer($search_by, $sort_by, $in, $limit, $offset) {
		return $this->ci->player->searchAllPlayer($search_by, $sort_by, $in, $limit, $offset);
	}

	/**
	 * Will get player given the Id
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerById($player_id) {
		return $this->ci->player->getPlayerById($player_id);
	}

	/**
	 * Will get player given the Id
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerByUsername($player_name) {
		return $this->ci->player->getPlayerByUsername($player_name);
	}

	/**
	 * Will get player given the Id
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerIdByUsername($player_name) {
		return $this->ci->player->getPlayerIdByUsername($player_name);
	}

	/**
	 * Will get all player by username
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getAllPlayerByUsername() {
		return $this->ci->player->getAllPlayerByUsername();
	}

	/**
	 * Will get player contact info given the Id
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerContactInfo($player_id) {
		return $this->ci->player->getPlayerContactInfo($player_id);
	}

	/**
	 * Will get player account given the Id
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerAccount($player_id) {
		return $this->ci->player->getPlayerAccount($player_id);
	}

	/**
	 * Will get player bank details given the Id
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerBankDetailsById($player_id) {
		return $this->ci->player->getPlayerBankDetailsById($player_id);
	}

	/**
	 * Gets players level
	 *
	 * @return	array
	 */
	public function getAllPlayerLevels() {
		return $this->ci->player->getAllPlayerLevels();
	}

	/**
	 * Gets players level
	 *
	 * @return	array
	 */
	public function getPlayerCurrentLevel($playerId) {
		return $this->ci->player->getPlayerCurrentLevel($playerId);
	}

	/**
	 * Will get player account given the Id
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getNonApprovedWithdrawal($player_id, $limit, $offset) {
		return $this->ci->player->getNonApprovedWithdrawal($player_id, $limit, $offset);
	}

	/**
	 * Will get note given the Id
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerNotes($player_id) {
		return $this->ci->player->getPlayerNotes($player_id);
	}

	/**
	 * Will create new player using the parameter data
	 * MOVED TO player_model
	 * @param 	array
	 */
	public function insertPlayer($data) {
		/*$hasher = new PasswordHash('8', TRUE);
		$data['password'] = $hasher->HashPassword($data['password']);*/
		// $data['password'] = $this->ci->salt->encrypt($data['password'], DESKEY_OG);

		// $this->ci->player->insertPlayer($data);
		$this->ci->load->model(array('player_model'));
		$this->ci->player_model->insertPlayer($data);
	}

	/**
	 * Will create new player details using the parameter data
	 *
	 * @param 	array
	 */
	public function insertPlayerDetails($data) {
		$this->ci->player->insertPlayerDetails($data);
	}

	/**
	 * Will create new tag using the parameter data
	 *
	 * @param 	array
	 */
	public function insertTag($data) {
		return $this->ci->player->insertTag($data);
	}

	/**
	 * Will create new tag using the parameter data
	 *
	 * @param 	array
	 */
	public function addVIPGroup($data) {
		$this->ci->player->addVIPGroup($data);
	}

	/**
	 * Will tag the player using the parameter data
	 *
	 * @param 	array
	 */
	public function insertPlayerTag($data) {
		$this->ci->player->insertPlayerTag($data);
	}

	/**
	 * Will check if the username is already existing
	 *
	 * @param 	string
	 * @return	array
	 */
	public function checkUsernameExist($username) {
		$result = $this->ci->player->checkUsernameExist($username);
		return $result;
	}

	/**
	 * Will check if the email is already existing
	 *
	 * @param 	string
	 * @return	array
	 */
	public function checkEmailExist($email) {
		$result = $this->ci->player->checkEmailExist($email);
		return $result;
	}

	/**
	 * Will create note for player using the parameter data
	 *
	 * @param 	array
	 */
	public function insertPlayerNote($data) {
		return $this->ci->player->insertPlayerNote($data);
	}

	/**
	 * Will delete note based on the parameters
	 *
	 * @param 	int
	 * @param 	int
	 */
	public function deleteNote($user_id, $note_id) {
		return $this->ci->player->deleteNote($user_id, $note_id);
	}

	/**
	 * Will edit note based on the parameters
	 *
	 * @param 	int
	 * @param 	int
	 * @param 	array
	 */
	public function editNote($user_id, $note_id, $data) {
		$this->ci->player->editNote($user_id, $note_id, $data);
	}

	/**
	 * Will get note based on the passed parameter
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getNoteById($note_id) {
		return $this->ci->player->getNoteById($note_id);
	}

	/**
	 * Will search players based on the passed parameters
	 *
	 * @param 	string
	 * @param 	int
	 * @param 	int
	 * @param 	string
	 * @return	array
	 */
	public function searchPlayerList($search, $limit, $offset, $type) {
		return $this->ci->player->searchPlayerList($search, $limit, $offset, $type);
	}

	/**
	 * Will search players based on the passed parameters
	 *
	 * @param 	string
	 * @param 	int
	 * @param 	int
	 * @param 	string
	 * @return	array
	 */
	public function searchPlayerReferralList($player_id) {
		return $this->ci->player->searchPlayerReferralList($player_id);
	}

	/**
	 * Will sort players based on the passed parameters
	 *
	 * @param 	string
	 * @param 	int
	 * @param 	int
	 * @param 	string
	 * @return	array
	 */
	public function sortPlayerList($sort, $limit, $offset, $type) {
		return $this->ci->player->sortPlayerList($sort, $limit, $offset, $type);
	}

	/**
	 * Will sort players based on the passed parameters
	 *
	 * @param 	string
	 * @param 	int
	 * @param 	int
	 * @param 	string
	 * @return	array
	 */
	public function sortPlayerReferralList($sort, $limit, $offset, $type) {
		return $this->ci->player->sortPlayerReferralList($sort, $limit, $offset, $type);
	}

	/**
	 * Will get all vip players
	 *
	 * @param 	int
	 * @param 	int
	 * @return	array
	 */
	public function getVIPPlayers($sort_by, $in, $limit, $offset) {
		return $this->ci->player->getVIPPlayers($sort_by, $in, $limit, $offset);
	}

	/**
	 * Will change player status based on the passed parameters
	 *
	 * @param 	array
	 * @return 	array
	 */
	public function populateVIP($data, $sort_by, $in, $limit, $offset) {
		return $this->ci->player->populateVIP($data, $sort_by, $in, $limit, $offset);
	}

	/**
	 * Will get all blacklist players
	 *
	 * @param 	int
	 * @param 	int
	 * @return	array
	 */
	public function getBlacklist($sort_by, $in, $limit, $offset) {
		return $this->ci->player->getBlacklist($sort_by, $in, $limit, $offset);
	}

	/**
	 * Will change player status based on the passed parameters
	 *
	 * @param 	array
	 * @return 	array
	 */
	public function populateBlack($data, $sort_by, $in, $limit, $offset) {
		return $this->ci->player->populateBlack($data, $sort_by, $in, $limit, $offset);
	}

	/**
	 * Will get all affiliate players
	 *
	 * @param 	int
	 * @param 	int
	 * @return	array
	 */
	public function getBatchAccount($limit, $offset) {
		return $this->ci->player->getBatchAccount($limit, $offset);
	}

	/**
	 * Will search account process players based on the passed parameter
	 *
	 * @param 	string
	 * @param 	int
	 * @param 	int
	 * @return	array
	 */
	public function searchAccountProcessList($search, $limit, $offset) {
		return $this->ci->player->searchAccountProcessList($search, $limit, $offset);
	}

	/**
	 * Will sort account process players based on the passed parameter
	 *
	 * @param 	string
	 * @param 	int
	 * @param 	int
	 * @return	array
	 */
	public function sortAccountProcessList($sort, $limit, $offset) {
		return $this->ci->player->sortAccountProcessList($sort, $limit, $offset);
	}

	/**
	 * Will get affiliate code based on the passed parameter
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getBatchCode() {
		return $this->ci->player->getBatchCode();
	}

	/**
	 * Will add players affiliate based on the passed parameter
	 *
	 * @param 	array
	 * @return	array
	 */
	public function addBatchPlayer($data) {
		return $this->ci->player->addBatchPlayer($data);
	}

	/**
	 * Will check if batch is already existing
	 *
	 * @param 	string
	 * @return	array
	 */
	public function checkBatchExist($name) {
		return $this->ci->player->checkBatchExist($name);
	}

	/**
	 * insert player account details in player account table
	 *
	 * @param 	array
	 * @return	void
	 */
	public function insertPlayerAccount($player_account) {
		$this->ci->player->insertPlayerAccount($player_account);
	}

	/**
	 * get all games
	 *
	 * @return	array
	 */
	public function getGames() {
		return $this->ci->player->getGames();
	}

	/**
	 * Will randomize alphanumeric and special characters
	 *
	 * @param 	string
	 * @return	string
	 */
	public function randomizer($name) {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
			. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. '0123456789!@#$%^&*()'
			. $name); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$randomPassword = '';
		foreach (array_rand($seed, 9) as $k) {
			$randomPassword .= $seed[$k];
		}

		return $randomPassword;
	}

	/**
	 * Will randomize alphanumeric and special characters
	 *
	 * @param 	string
	 * @return	string
	 */
	public function generatePromoCode() {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
			. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. '0123456789'); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$generatePromoCode = '';
		foreach (array_rand($seed, 7) as $k) {
			$generatePromoCode .= $seed[$k];
		}

		return $generatePromoCode;
	}

	/**
	 * Will randomize alphanumeric and special characters
	 *
	 * @param 	string
	 * @return	string
	 */
	public function resetPassword($data, $player_id) {
		/*$hasher = new PasswordHash('8', TRUE);
		$data['password'] = $hasher->HashPassword($data['password']);*/
		$data['password'] = $this->ci->salt->encrypt($data['password'], $this->ci->config->item('DESKEY_OG'));

		$this->ci->player->resetPassword($data, $player_id);
	}

	/**
	 * set agent for a given player
	 *
	 * @param 	string
	 * @return	string
	 */
	public function setPlayerAgent($data, $player_id) {
		$this->ci->player->setPlayerAgent($data, $player_id);
	}

	/**
	 * Will get player based on affiliate code
	 *
	 * @param 	int
	 * @param 	int
	 * @param 	int
	 * @return	array
	 */
	public function viewPlayerByBatchId($batch_id, $limit, $offset) {
		return $this->ci->player->viewPlayerByBatchId($batch_id, $limit, $offset);
	}

	/**
	 * Will get affiliate based on affiliate id
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getBatchByPlayerBatchId($batch_id) {
		return $this->ci->player->getBatchByPlayerBatchId($batch_id);
	}

	/**
	 * Will edit player affiliate based on passed parameters
	 *
	 * @param 	array
	 * @param 	int
	 * @return	array
	 */
	public function editAccountBatch($data, $batch_id) {
		return $this->ci->player->editAccountBatch($data, $batch_id);
	}

	/**
	 * Will delete player affiliate based on passed parameter
	 *
	 * @param 	int
	 */
	public function deletePlayerBatch($batch_id) {
		$this->ci->player->deletePlayerBatch($batch_id);
	}

	/**
	 * Will delete player affiliate based on passed parameter
	 *
	 * @param 	int
	 */
	public function deletePlayerAccountBatch($players) {
		foreach ($players as $player) {
			$this->ci->player->deletePlayerAccountBatch($player['playerId']);
		}
	}

	/**
	 * Will delete player affiliate based on passed parameter
	 *
	 * @param 	int
	 */
	public function deletePlayerAccountPlayer($player_id, $type, $type_id) {
		$this->ci->player->deletePlayerAccountPlayer($player_id, $type, $type_id);
	}

	/**
	 * Will edit player affiliate based on passed parameters
	 *
	 * @param 	int
	 */
	public function deletePlayerByBatch($players) {
		foreach ($players as $player) {
			$this->ci->player->deletePlayer($player['playerId']);
		}
	}

	/**
	 * Will edit player affiliate based on passed parameters
	 *
	 * @param 	int
	 */
	public function deletePlayerDetailsBatch($players) {
		foreach ($players as $player) {
			$this->ci->player->deletePlayerDetails($player['playerId']);
		}
	}

	/**
	 * Will edit player affiliate based on passed parameters
	 *
	 * @param 	int
	 */
	public function deletePlayerDetails($player_id) {
		$this->ci->player->deletePlayerDetails($player_id);
	}

	/**
	 * Will get player using the passed parameter
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerByPlayerId($player_id) {
		return $this->ci->player->getPlayerByPlayerId($player_id);
	}

	/**
	 * Will get player using the passed parameter
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getBatchAccountByPlayerId($player_id) {
		return $this->ci->player->getBatchAccountByPlayerId($player_id);
	}

	/**
	 * Will edit player using the passed parameter
	 *
	 * @param 	array
	 * @param 	int
	 */
	public function editPlayer($data, $player_id) {
		$this->ci->player->editPlayer($data, $player_id);
	}

	/**
	 * Will edit player details using the passed parameter
	 *
	 * @param 	array
	 * @param 	int
	 */
	public function editPlayerDetails($data, $player_id) {
		$this->ci->player->editPlayerDetails($data, $player_id);
	}

	/**
	 * Will edit player using the passed parameter
	 *
	 * @param 	array
	 * @param 	int
	 */
	public function editPlayerAccount($data, $player_id, $type) {
		$this->ci->player->editPlayerAccount($data, $player_id, $type);
	}

	/**
	 * Will update player balances
	 *
	 * @param 	int
	 */
	public function updateBalances($player_id) {
		$player = $this->getPlayerById($player_id);
		$result = $this->ci->game_platform_manager->queryBalanceOnAllPlatforms($player['username']);

		foreach ($result as $key => $value) {
			$data = array(
				'totalBalanceAmount' => $value['balance'],
			);
			$this->updatePlayerBalances($player_id, 'subwallet', $key, $data);
		}
	}

	/**
	 * Will update player balances
	 *
	 * @param 	int
	 * @param 	string
	 * @param 	int
	 * @param 	array
	 * @param 	int
	 */
	public function updatePlayerBalances($player_id, $type, $type_id, $data) {
		$this->ci->player->updatePlayerBalances($player_id, $type, $type_id, $data);
	}

	/**
	 * Will delete player using the passed parameter
	 *
	 * @param 	int
	 */
	public function deletePlayer($player_id) {
		$this->ci->player->deletePlayer($player_id);
	}

	/**
	 * Will get all tags
	 *
	 * @return 	array
	 */
	public function getAllTags() {
		return $this->ci->player->getAllTags();
	}

	/**
	 * Will get tag based on the player id
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getPlayerTag($player_id) {
		return $this->ci->player->getPlayerTag($player_id);
	}

	/**
	 * Will get tag based on the id
	 *
	 * @param 	int
	 * @param &object The query, for get all rows with foreach ($query->result() as $row){...}
	 * @return 	array
	 */
	public function getPlayerTagById($tag_id, &$query) {
		return $this->ci->player->getPlayerTagById($tag_id, $query);
	}

	/**
	 * Will change the player's tag based on the passed parameters
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	public function changeTag($player_id, $data) {
		return $this->ci->player->changeTag($player_id, $data);
	}

	/**
	 * Will change the player's tag based on the passed parameters
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	public function getSearchTag($search, $limit, $offset) {
		return $this->ci->player->getSearchTag($search, $limit, $offset);
	}

	/**
	 * Will get tag based on the name of the tag
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function getPlayerTagByName($tag_name) {
		return $this->ci->player->getPlayerTagByName($tag_name);
	}

	/**
	 * Will get vip group name
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function getVipGroupName($group_name) {
		return $this->ci->player->getVipGroupName($group_name);
	}

	/**
	 * Will get vip group level details
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function getVipGroupLevelDetails($vipgrouplevelId) {
		return $this->ci->player->getVipGroupLevelDetails($vipgrouplevelId);
	}

	/**
	 * Will get cashback bonus per game
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function getCashbackBonusPerGame($vipsettingcashbackruleId) {
		return $this->ci->player->getCashbackBonusPerGame($vipsettingcashbackruleId);
	}

	/**
	 * Will activate group
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function activateVIPGroup($data) {
		return $this->ci->player->activateVIPGroup($data);
	}

	/**
	 * Will activate group
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function getVIPGroupRules($vipgroupId) {
		return $this->ci->player->getVIPGroupRules($vipgroupId);
	}

	/**
	 * Will change the player's tag based on the passed parameters
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getReferralByPlayerId($player_id) {
		return $this->ci->player->getReferralByPlayerId($player_id);
	}

	/**
	 * get all referral of player
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getAllReferralByPlayerId($player_id) {
		return $this->ci->player->getAllReferralByPlayerId($player_id);
	}

	/**
	 * Will change the player's tag based on the passed parameters
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getReferredPlayer($player_id) {
		return $this->ci->player->getReferredPlayer($player_id);
	}

	/**
	 * Will block the players based on the passed parameters
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	public function blockPlayer($player_id, $data) {
		return $this->ci->player->blockPlayer($player_id, $data);
	}

	/**
	 * Will get the chat history
	 *
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function getChatHistory($limit, $offset) {
		return $this->ci->player->getChatHistory($limit, $offset);
	}

	/**
	 * Will search chat history based on the passed parameter
	 *
	 * @param 	string
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function searchChatHistoryList($search, $limit, $offset) {
		return $this->ci->player->searchChatHistoryList($search, $limit, $offset);
	}

	/**
	 * Will sort chat history based on the passed parameter
	 *
	 * @param 	string
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function sortChatHistoryList($sort, $limit, $offset) {
		return $this->ci->player->sortChatHistoryList($sort, $limit, $offset);
	}

	/**
	 * Will get chat history based on the session
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getChatHistoryByPlayerId($session, $limit, $offset) {
		return $this->ci->player->getChatHistoryByPlayerId($session, $limit, $offset);
	}

	/**
	 * Will get chat history based on the session
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getChatHistoryById($player_id, $limit, $offset) {
		return $this->ci->player->getChatHistoryById($player_id, $limit, $offset);
	}

	/**
	 * Will delete chat history based on the session
	 *
	 * @param 	int
	 */
	public function deleteChatHistory($session) {
		$this->ci->player->deleteChatHistory($session);
	}

	/**
	 * Will delete vip group level
	 *
	 * @param vipgrouplevelid
	 */
	public function deletevipgrouplevel($vipgrouplevelId) {
		$this->ci->player->deletevipgrouplevel($vipgrouplevelId);
	}

	/**
	 * Will get game history based on id
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getGameHistoryById($game_history_id, $limit, $offset) {
		return $this->ci->player->getGameHistoryById($game_history_id, $limit, $offset);
	}

	/**
	 * Will get game history based on id
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getPlayerTotalBalance($player_id) {
		return $this->ci->player->getPlayerTotalBalance($player_id);
	}

	/**
	 * Will get game history based on id
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getGameHistoryByPlayerId($player_id) {
		return $this->ci->player->getGameHistoryByPlayerId($player_id);
	}

	/**
	 * Will get all game history
	 *
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function getAllGameHistory($limit, $offset) {
		return $this->ci->player->getAllGameHistory($limit, $offset);
	}

	/**
	 * Will search game history based on the passed parameter
	 *
	 * @param 	string
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function searchGameHistory($search, $limit, $offset) {
		return $this->ci->player->searchGameHistory($search, $limit, $offset);
	}

	/**
	 * Will sort game history based on the passed parameter
	 *
	 * @param 	string
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function sortGameHistory($sort, $limit, $offset) {
		return $this->ci->player->sortGameHistory($sort, $limit, $offset);
	}

	/**
	 * Will change player status based on the passed parameters
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	public function changePlayerStatus($player_id, $data) {
		return $this->ci->player->changePlayerStatus($player_id, $data);
	}

	/**
	 * Will change player status based on the passed parameters
	 *
	 * MOVED TO group_level.adjustPlayerLevel
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	public function changePlayerLevel($player_id, $data) {
		$this->ci->load->model(array('group_level'));
		return $this->ci->group_level->adjustPlayerLevel($player_id, $data['playerGroupId']);
	}

	/**
	 * Will change player status based on the passed parameters
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	public function populatePeriodOfTime($start_date, $end_date) {
		return $this->ci->player->populatePeriodOfTime($start_date, $end_date);
	}

	/**
	 * Will change player status based on the passed parameters
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	public function populateUsername($username) {
		return $this->ci->player->populateUsername($username);
	}

	/**
	 * Will change player status based on the passed parameters
	 *
	 * @param 	array
	 * @return 	array
	 */
	public function populate($data, $sort_by, $in, $limit, $offset) {
		return $this->ci->player->populate($data, $sort_by, $in, $limit, $offset);
	}

	function get_age($birth_date) {
		return floor((time() - strtotime($birth_date)) / 31556926);
	}

	/**
	 * get all affiliates
	 *
	 * @return 	array
	 */
	public function getAllAffiliates() {
		return $this->ci->player->getAllAffiliates();
	}

	/**
	 * get all affiliates
	 *
	 * @return 	array
	 */
	public function getPlayerTotalDeposits($player_account_id) {
		return $this->ci->player->getPlayerTotalDeposits($player_account_id);
	}

	/**
	 * get all affiliates
	 *
	 * @return 	array
	 */
	public function getPlayerTotalWithdrawal($player_account_id) {
		return $this->ci->player->getPlayerTotalWithdrawal($player_account_id);
	}

	/**
	 * get all affiliates
	 *
	 * @return 	array
	 */
	public function getPlayerNumberOfDeposits($player_account_id) {
		return $this->ci->player->getPlayerNumberOfDeposits($player_account_id);
	}

	public function isActivePlayer($player_id) {
		$player_game = $this->ci->player->getActivePlayersByGameHistory($player_id);
		$last_game_played = strtotime($player_game['gameEnd']);
		$today = strtotime(date('Y-m-d H:i:s'));

		$datediff = abs($today - $last_game_played);
		$days = floor($datediff / (60 * 60 * 24));
		echo $days;
		if ($days < 60) {
			return true;
		}

		return false;
	}

	/**
	 * get all affiliates
	 *
	 * @return 	array
	 */
	public function getAllGames() {
		return $this->ci->player->getAllGames();
	}

	/**
	 * get all players with referral
	 *
	 * @return 	array
	 */
	public function getAllPlayersWithReferral($limit, $offset) {
		return $this->ci->player->getAllPlayersWithReferral($limit, $offset);
	}

	/**
	 * get all vip settings
	 *
	 * @return 	array
	 */
	public function getVIPSettingList($sort, $limit, $offset) {
		return $this->ci->player->getVIPSettingList($sort, $limit, $offset);
	}

	/**
	 * get all vip settings
	 *
	 * @return 	array
	 */
	public function getVIPSettingListToExport($sort, $limit, $offset) {
		return $this->ci->player->getVIPSettingListToExport($sort, $limit, $offset);
	}

	/**
	 * get all vip settings
	 *
	 * @return 	array
	 */
	/*public function getRankingSettings() {
		return $this->ci->player->getRankingSettings();
	*/

	/**
	 * get all ranking group
	 *
	 * @return 	array
	 */
	public function getRankingGroupOfPlayer($player_level) {
		return $this->ci->player->getRankingGroupOfPlayer($player_level);
	}

	/**
	 * get friend referral settings
	 *
	 * @return 	array
	 */
	public function getFriendReferralSettings() {
		return $this->ci->player->getFriendReferralSettings();
	}

	/**
	 * get create friend referral settings
	 *
	 * @return 	array
	 */
	public function createFriendReferralSettings($data) {
		return $this->ci->player->createFriendReferralSettings($data);
	}

	/**
	 * save friend referral settings
	 *
	 * @return 	array
	 */
	public function saveFriendReferralSettings($data, $friend_referral_settings_id) {
		return $this->ci->player->saveFriendReferralSettings($data, $friend_referral_settings_id);
	}

	/**
	 * get player username
	 *
	 * @return 	mixed	[ username => (string) ] array when successful; false when query returns no data.
	 */
	public function getPlayerUsername($playerId) {
		return $this->ci->player->getPlayerUsername($playerId);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function getPlayerBlockedGames($player_id) {
		return $this->ci->player->getPlayerBlockedGames($player_id);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function getTags($sort, $limit, $offset) {
		return $this->ci->player->getTags($sort, $limit, $offset);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function getTagDetails($tag_id) {
		return $this->ci->player->getTagDetails($tag_id);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function getVIPGroupDetails($vipSettingId) {
		return $this->ci->player->getVIPGroupDetails($vipSettingId);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function getTagDescription($tag_id) {
		return $this->ci->player->getTagDescription($tag_id);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function editTag($data, $tag_id) {
		return $this->ci->player->editTag($data, $tag_id);
	}

	/**
	 * Edit vip group
	 *
	 * @return	$array
	 */
	public function editVIPGroup($data, $vipsetting_id) {
		return $this->ci->player->editVIPGroup($data, $vipsetting_id);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function deletePlayerTag($tag_id) {
		return $this->ci->player->deletePlayerTag($tag_id);
	}

	/**
	 * Delete VIP Group
	 *
	 * @return	$array
	 */
	public function deleteVIPGroup($vipsettingId) {
		return $this->ci->player->deleteVIPGroup($vipsettingId);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function deleteTag($tag_id) {
		return $this->ci->player->deleteTag($tag_id);
	}

	/**
	 * Get VIP Group item
	 *
	 * @return	$array
	 */
	public function deleteVIPGroupItem($vipsettingId) {
		return $this->ci->player->deleteVIPGroupItem($vipsettingId);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function getAllReferredPlayers($sort, $limit, $offset) {
		return $this->ci->player->getAllReferredPlayers($sort, $limit, $offset);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function isRankingAlreadyExists($rankingLevelGroup, $rankingLevel) {
		return $this->ci->player->isRankingAlreadyExists($rankingLevelGroup, $rankingLevel);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function addRankingLevelSetting($rankingLeveldata) {
		return $this->ci->player->addRankingLevelSetting($rankingLeveldata);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function getRankingLevelSettingsDetail($rankingLevelId) {
		return $this->ci->player->getRankingLevelSettingsDetail($rankingLevelId);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function editRankingLevelSetting($id, $rankingLeveldata) {
		return $this->ci->player->editRankingLevelSetting($id, $rankingLeveldata);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function deleteRankingLevelSetting($id) {
		return $this->ci->player->deleteRankingLevelSetting($id);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function getPlayerGame($player_id) {
		return $this->ci->player->getPlayerGame($player_id);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function getPlayerGameByGameId($player_id, $game_id) {
		return $this->ci->player->getPlayerGameByGameId($player_id, $game_id);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function getGameById($game_id) {
		return $this->ci->player->getGameById($game_id);
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function changePlayerGameBlocked($player_id, $game_id, $data) {
		return $this->ci->player->changePlayerGameBlocked($player_id, $game_id, $data);
	}

	/**
	 * Will get player level of user
	 *
	 * @return  array
	 */
	public function getPlayerLevels() {
		return $this->ci->player->getPlayerLevels();
	}

	/**
	 * Will get player level of user
	 *
	 * @return  array
	 */
	public function getMainWallet($player_id) {
		return $this->ci->player->getMainWallet($player_id);
	}

	/**
	 * Will get player level of user
	 *
	 * @return  array
	 */
	// public function isReferredBy($player_id) {
	// 	return $this->ci->player->isReferredBy($player_id);
	// }

	/**
	 * Will get player level of user
	 *
	 * @return  array
	 */
	public function getReferralByCode($referral_code) {
		return $this->ci->player->getReferralByCode($referral_code);
	}

	/**
	 * Will get player level of user
	 *
	 * @return  array
	 */
	public function getSelectedPlayers($player_ids) {
		return $this->ci->player->getSelectedPlayers($player_ids);
	}

	/**
	 * Will get player level of user
	 *
	 * @return  array
	 */
	public function deletePlayerTagByPlayerId($player_id) {
		return $this->ci->player->deletePlayerTagByPlayerId($player_id);
	}

	public function brandExcel() {
		$this->load->library('excel');
		$result = $this->config_model->getBrandsForExcel();
		$this->excel->to_excel($result, 'brands-excel');
	}

	/**
	 * get player affiliate
	 *
	 * @param  int
	 * @return string
	 */
	public function getAffiliateOfPlayer($player_id) {
		return $this->ci->player->getAffiliateOfPlayer($player_id);
	}

	/**
	 * get player's parent agent name
	 *
	 * @param  int
	 * @return string
	 */
	public function getAgentOfPlayer($player_id) {
		return $this->ci->player->getAgentOfPlayer($player_id);
	}

	/**
	 * get player's dispatch account name
	 *
	 * @param  int
	 * @return string
	 */
	public function getDispatchAccountOfPlayer($player_id) {
		return $this->ci->player->getDispatchAccountOfPlayer($player_id);
	}

	/**
	 * get Total Bonus
	 *
	 * @param  int
	 * @return double
	 */
	public function getTotalBonus($player_id) {
		return $this->ci->player->getTotalBonus($player_id);
	}

	/**
	 * get Game Provider
	 *
	 * @param  int
	 * @return string
	 */
	public function getGameProvider($player_id) {
		return $this->ci->player->getGameProvider($player_id);
	}

	/**
	 * get API Details
	 *
	 * @param  int
	 * @return array
	 */
	public function getAPIDetails($player_id) {
		return $this->ci->player->getAPIDetails($player_id);
	}

	/**
	 * get all deposit promo
	 *
	 * @param  int
	 * @return array
	 */
	public function getAllPromo() {
		return $this->ci->player->getAllPromo();
	}

	/**
	 * getCashbackHistory
	 *
	 * @return  array
	 */
	public function getCashbackHistory($playerId, $limit, $offset) {
		return $this->ci->player->getCashbackHistory($playerId, $limit, $offset);
	}

	/**
	 * generateReferralCode for player
	 *
	 * @return  array
	 */
	public function generateReferralCode($player_id) {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
			. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. '0123456789'); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$referral_code = '';
		foreach (array_rand($seed, 5) as $k) {
			$referral_code .= $seed[$k];
		}

		return $player_id . $referral_code . "OG";
	}

	/**
	 * get friendreferralbyplayer
	 *
	 * @return	array
	 */
	public function getPlayerReferral($player_id, $limit, $dateJoined) {
		return $this->ci->player->getPlayerReferral($player_id, $limit, $dateJoined);
	}

	/**
	 * get adjust balance history
	 *
	 * @return	array
	 */
	public function getBalanceAdjustment($player_id, $limit, $offset) {
		return $this->ci->player->getBalanceAdjustment($player_id, $limit, $offset);
	}

	/**
	 * get players and game data
	 *
	 * @return	array
	 */
	public function getPlayers() {
		return $this->ci->player->getPlayers();
	}

	/**
	 * get all player friend referral
	 *
	 * @return	array
	 */
	public function getAllPlayerFriendReferral() {
		return $this->ci->player->getAllPlayerFriendReferral();
	}

	/**
	 * get all player bets, deposits
	 *
	 * @return	array
	 */
	public function getPlayerBetsDeposits($player_id) {
		return $this->ci->player->getPlayerBetsDeposits($player_id);
	}

	/**
	 * get all player bets, deposits
	 *
	 * @return	array
	 */
	public function getPlayerPTBets($playerName, $dateJoined) {
		return $this->ci->player->getPlayerPTBets($playerName, $dateJoined);
	}

	/**
	 * get all player bets, deposits
	 *
	 * @return	array
	 */
	public function getPlayerAGBets($playerName, $dateJoined) {
		return $this->ci->player->getPlayerAGBets($playerName, $dateJoined);
	}

	/**
	 * Will create player referral details based on the passed parameters
	 *
	 * @param   array
	 */
	// public function createPlayerReferralDetails($data) {
	// 	$this->ci->player->createPlayerReferralDetails($data);
	// }

	/**
	 * Will get all player deposit promo
	 *
	 * @param   array
	 */
	public function getPlayerDepositPromo() {
		return $this->ci->player->getPlayerDepositPromo();
	}

	/**
	 * Will get player deposit promo by player id
	 *
	 * @param   array
	 */
	public function getPlayerDepositPromoById($player_id, $limit, $offset) {
		return $this->ci->player->getPlayerDepositPromoById($player_id, $limit, $offset);
	}

	/**
	 * update player deposit promo
	 *
	 * @param   array
	 */
	public function updatePlayerDepositPromo($data, $player_deposit_promo_id) {
		$this->ci->player->updatePlayerDepositPromo($data, $player_deposit_promo_id);
	}

	/**
	 * Will get player deposit by player id
	 *
	 * @param   array
	 */
	public function getPlayerDepositHistory($player_id, $limit, $offset) {
		return $this->ci->player->getPlayerDepositHistory($player_id, $limit, $offset);
	}

	/**
	 * Will get player withdrawal by player id
	 *
	 * @param   array
	 */
	public function getPlayerWithdrawalHistory($player_id, $limit, $offset) {
		return $this->ci->player->getPlayerWithdrawalHistory($player_id, $limit, $offset);
	}

	/**
	 * create player game
	 *
	 * @param   array
	 */
	public function createPlayerGame($data) {
		return $this->ci->player->createPlayerGame($data);
	}

	/**
	 * get AG Record by player name
	 *
	 * @param   array
	 */
	public function getAllAGRecordByPlayerName($player_name) {
		return $this->ci->player->getAllAGRecordByPlayerName($player_name);
	}

	/**
	 * get AG Record by player name
	 *
	 * @param   array
	 */
	public function getAllAGGameRecordByPlayerName($player_name) {
		return $this->ci->player->getAllAGGameRecordByPlayerName($player_name);
	}

	/**
	 * get lock players
	 *
	 * @param   array
	 */
	public function getLockPlayers() {
		return $this->ci->player->getLockPlayers();
	}

	/**
	 * get block players
	 *
	 * @param   array
	 */
	public function getBlockPlayers() {
		return $this->ci->player->getBlockPlayers();
	}

	/**
	 * edit player game
	 *
	 * @param   array
	 */
	public function editPlayerGame($data, $player_game_id) {
		$this->ci->player->editPlayerGame($data, $player_game_id);
	}

	/**
	 * get promo join that is not yet expired
	 *
	 * @param   array
	 */
	public function getPromoJoin() {
		return $this->ci->player->getPromoJoin();
	}

	/**
	 * edit player deposit promo
	 *
	 * @param   array
	 */
	public function editPlayerDepositPromo($data, $player_deposit_promo_id) {
		$this->ci->player->editPlayerDepositPromo($data, $player_deposit_promo_id);
	}

	/**
	 * get promo that is not yet expired
	 *
	 * @param   array
	 */
	public function getPromo() {
		return $this->ci->player->getPromo();
	}

	/**
	 * edit deposit promo
	 *
	 * @param   array
	 */
	public function editDepositPromo($data, $deposit_promo_id) {
		$this->ci->player->editDepositPromo($data, $deposit_promo_id);
	}

	/**
	 * Will get player password given the Id and api type id
	 *
	 * @param   int
	 * @param   int
	 * @return  array
	 */
	public function getPlayerPassword($player_id, $api_type) {
		$result = $this->ci->gameapi->getPlayerPassword($player_id, $api_type);
		return $result;
	}

	/**
	 * Generate string of random characters
	 *
	 * @param integer $length  Length of the string to generate
	 * @param integer $lower   Include lower case characters
	 * @param integer $upper   Include uppercase characters
	 * @param integer $nums    Include numbers
	 * @param integer $special Include special characters
	 * @return string
	 */
	function getRandomCharString($length, $lower = true, $upper = true, $nums = true, $special = false) {
		$pool_lower = 'abcdefghijklmopqrstuvwxyz';
		$pool_upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$pool_nums = '0123456789';
		$pool_special = '!$%^&*+#~/|';

		$pool = '';
		$res = '';

		if ($lower === true) {
			$pool .= $pool_lower;
		}
		if ($upper === true) {
			$pool .= $pool_upper;
		}
		if ($nums === true) {
			$pool .= $pool_nums;
		}
		if ($special === true) {
			$pool .= $pool_special;
		}

		if (($length < 0) || ($length == 0)) {
			return $res;
		}

		srand((double) microtime() * 1000000);

		for ($i = 0; $i < $length; $i++) {
			$charidx = rand() % strlen($pool);
			$char = substr($pool, $charidx, 1);
			$res .= $char;
		}

		return $res;
	}

	/**
	 * get pt bets by date
	 *
	 * @return	array
	 */
	public function getPTBets($start_date, $end_date) {
		$players = $this->getPlayers();
		$players_and_bets = array();

		foreach ($players as $key => $value) {
			$result = $this->ci->player->getPTBets($value['username'], $start_date, $end_date);

			if (!empty($result)) {
				$result['game_type'] = '1';
				array_push($players_and_bets, $result);
			}
		}

		/*echo "<pre>";
			print_r($players_and_bets);
			echo "</pre>";
		*/

		return $players_and_bets;
	}

	/**
	 * get ag bets by date
	 *
	 * @return	array
	 */
	public function getAGBets($start_date, $end_date) {
		$players = $this->getPlayers();
		$players_and_bets = array();

		foreach ($players as $key => $value) {
			$result = $this->ci->player->getAGBets($value['username'], $start_date, $end_date);

			if (!empty($result)) {
				$result['game_type'] = '2';
				$result['playerId'] = $value['playerId'];
				array_push($players_and_bets, $result);
			}
		}

		return $players_and_bets;
	}

	/**
	 * Will get code of the player who referrer the current player.
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getCodeofReferrer($player_id) {
		$referrer_id = $this->ci->player->getReferrerId($player_id);

		if ($referrer_id != 0) {
			$data['referrer_id'] = $referrer_id;

			$player = $this->ci->player->getPlayerById($referrer_id);

			$referrer_code = $player['invitationCode'];
			$data['referrer_code'] = $referrer_code;

			return $data;
		}

		return null;
	}

	/**
	 * Will get the id of the player who referred the current player.
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getRefereePlayerId($player_id) {
		$referrer_id = $this->ci->player->getRefereePlayerId($player_id);

		return $referrer_id;
	}

	/**
	 * Will check if multidimensional array consists of value and key your looking
	 *
	 * @param 	int
	 * @return	array
	 */
	public function checkIfValueExists($array, $key, $val) {
		foreach ($array as $item) {
			if (isset($item[$key]) && $item[$key] == $val) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Will get if multidimensional array consists of value and key your looking
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getIfValueExists($array, $key, $val) {
		$result = array();

		foreach ($array as $item) {
			if (isset($item[$key]) && $item[$key] == $val) {
				array_push($result, $item);
			}
		}

		return $result;
	}

	/**
	 * update playerbankdetails
	 *
	 * @param 	int
	 * @return	array
	 */
	public function updatePlayerBankDetails($bank_details, $bank_details_id) {
		$this->ci->player->updatePlayerBankDetails($bank_details, $bank_details_id);
	}

	/**
	 * delete bank info
	 *
	 * @return	string
	 */
	public function deletePlayerBankInfo($bank_details_id) {
		$this->ci->player->deletePlayerBankInfo($bank_details_id);
	}

	/**
	 * Will get player bank details given the bankDetailsId
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerBankDetails($bank_details_id) {
		return $this->ci->player->getPlayerBankDetails($bank_details_id);
	}

	/**
	 * Will get bank in banktype
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getBankType() {
		return $this->ci->player->getBankType();
	}

	/**
	 * Will add bank info in playerbankdetails
	 *
	 * @param 	int
	 * @return	array
	 */
	public function addPlayerBankDetails($data) {
		$this->ci->player->addPlayerBankDetails($data);
	}

	/**
	 * Will get player main wallet balance
	 *
	 * @param   int
	 * @return  array
	 */
	public function getMainWalletBalance($player_id) {
		return $this->ci->player->getMainWalletBalance($player_id);
	}

	/**
	 * Will get player sub wallet balance
	 *
	 * @param   int
	 * @return  array
	 */
	public function getSubWalletBalance($player_id) {
		return $this->ci->player->getSubWalletBalance($player_id);
	}

	/**
	 * processPromoApplication
	 *
	 * @param   data array
	 * @return  array
	 */
	// public function processPromoApplication($playerpromodata) {
	// 	$this->ci->player->processPromoApplication($playerpromodata);
	// }

	/**
	 * get email in email table
	 *
	 * @return  array
	 */
	public function getEmail() {
		return $this->ci->player->getEmail();
	}

	/* Online Players */

	/**
	 * get online players in website
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getOnlinePlayers() {
		return $this->ci->player->getOnlinePlayers();
	}

	/**
	 * get API online players in website
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getAPIOnlinePlayers($api_result) {
		return $this->ci->player->getAPIOnlinePlayers($api_result);
	}

	/**
	 * kick player
	 *
	 * @return	rendered Template
	 */
	public function checkIfOnline($player_name, $type) {
		if ($type == 1) {
			$ret = $this->ci->game_pt_api->isPlayerOnline($player_name);
			$result = isset($ret['result']) ? $ret['result'] : false;
		} else if ($type == 2) {
			//$result = $this->ci->game_ag_api->isPlayerOnline($player_name)['result'];
			$result = 0;
		}

		return $result;
	}

	/**
	 * logout player in website
	 *
	 * @return	rendered Template
	 */
	public function logoutPlayer($player_name) {
		// $result = $this->ci->player->getPlayerByUsername($player_name); //get player details

		// if (!empty($result)) {

		// 	# TODO: get from PLAYER config
		// 	// if ($this->ci->config->item('sess_use_database')) {
		// 	// $session_table = $this->ci->config->item('sess_table_name');
		// 	$session_table = 'ci_player_sessions';
		// 	$session_id = $result['session_id'];
		// 	$this->ci->db->delete($session_table, ['session_id' => $session_id]);
		// 	// }

		// 	$data = array(
		// 		'online' => '1', //set online as 1
		// 		'lastActivityTime' => date('Y-m-d H:i:s', strtotime('-1 day', strtotime($result['lastActivityTime']))), //set last activity as 1 day ago
		// 		'lastLogoutTime' => date('Y-m-d H:i:s'),
		// 		'session_id' => null,
		// 	);

		// 	$this->ci->player->editPlayer($data, $result['playerId']); //update player info
		// }
	}

	/**
	 * time elapsed in human readable form.
	 *
	 * @return	rendered Template
	 */
	public function time_elapsed_A($secs) {
		$bit = array(
			'y' => $secs / 31556926 % 12,
			'w' => $secs / 604800 % 52,
			'd' => $secs / 86400 % 7,
			'h' => $secs / 3600 % 24,
			'm' => $secs / 60 % 60,
			's' => $secs % 60,
		);

		foreach ($bit as $k => $v) {
			if ($v > 0) {
				$ret[] = $v . $k;
			}
		}

		$ret[] = 'ago.';

		return join(' ', $ret);
	}

	/**
	 * time elapsed in human readable form.
	 *
	 * @return	rendered Template
	 */
	public function time_elapsed_B($secs) {
		$bit = array(
			' year' => $secs / 31556926 % 12,
			' week' => $secs / 604800 % 52,
			' day' => $secs / 86400 % 7,
			' hour' => $secs / 3600 % 24,
			' minute' => $secs / 60 % 60,
			' second' => $secs % 60,
		);

		foreach ($bit as $k => $v) {
			if ($v > 1) {
				$ret[] = $v . $k . 's';
			}

			if ($v == 1) {
				$ret[] = $v . $k;
			}

		}
		array_splice($ret, count($ret) - 1, 0, 'and');
		$ret[] = 'ago.';

		return join(' ', $ret);
	}

	/**
	 * Gets all the game logs data
	 *
	 * @return 	array
	 */
	public function getPlayerAllGameLogData() {
		return $this->ci->player->getPlayerAllGameLogData();
	}

	/**
	 * Gets all the game logs data and computes every player's total betting amount
	 *
	 * @return 	array
	 */
	public function getPlayerGameLog($currentMOnth, $nextMonth, $currentYear, $nextYear) {
		return $this->ci->player->getPlayerGameLog($currentMOnth, $nextMonth, $currentYear, $nextYear);
	}

	/**
	 * Get player's level
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getPlayerLevel($player_id) {
		return $this->ci->player->getPlayerLevel($player_id);
	}

	/**
	 * Add new log when player's level is automatically changed
	 *
	 * @param 	array
	 * @return 	array
	 */
	public function addPlayerLevelLog($data) {
		return $this->ci->player->addPlayerLevelLog($data);
	}

	/**
	 * check if bankaccount record exists
	 *
	 * @return
	 */
	function isPlayerBankAccountNumberExists($bankAccountId) {
		return $this->ci->player->isPlayerBankAccountNumberExists($bankAccountId);
		//save pw to player table
	}

	/**
	 * getPlayerActivePromoDetails
	 *
	 * @return
	 */
	function getPlayerActivePromoDetails($playerId) {
		return $this->ci->player->getPlayerActivePromoDetails($playerId);
		//save pw to player table
	}

	/**
	 * Add new log when player's info is updated
	 *
	 * @param 	array
	 * @return 	array
	 */
	public function addPlayerInfoUpdates($playerId, $data) {
		return $this->ci->player->addPlayerInfoUpdates($playerId, $data);
	}

	public function getSubwalletAccount($playerAccountId) {
		return $this->ci->player->getSubwalletAccount($playerAccountId);
	}

	# OG-693
	# Add some fields(first/last deposit/withdrawal date time, currency) to Account Information
	# Approved status only
	public function getPlayerFirstLastApprovedTransaction($player_id, $transaction_type) {
		$player_criteria = "((from_id = {$player_id} AND from_type = " . Transactions::PLAYER . ") OR (to_id = {$player_id} AND to_type = " . Transactions::PLAYER . '))';
		$transaction_criteria['transaction_type'] = $transaction_type;
		$transaction_criteria['status'] = Transactions::APPROVED;
		return $this->ci->transactions->getTransactionMinMax('created_at', $transaction_criteria, $player_criteria);
	}

	/**
	 * Adds affiliate to player without referral code
	 *
	 *
	 *
	 */
	public function addAffiliateToPlayer($playerId, $affiliateId) {
		return $this->ci->player->addAffiliateToPlayer($playerId, $affiliateId);
	}

	/* end of Online Players */

	#OGP-148 Need to specify "Balance Type" while Manually Adjust Balance
	public function getAllManualSubtractBalanceTags() {
		return $this->ci->player->getAllManualSubtractBalanceTags();
	}

	public function getManualSubtractBalanceTagDetails($id) {
		return $this->ci->player->getManualSubtractBalanceTagDetails($id);
	}

	public function getManualSubtractBalanceTagByName($adjust_tag_name) {
		return $this->ci->player->getManualSubtractBalanceTagByName($adjust_tag_name);
	}

	public function insertManualSubtractBalanceTag($data) {
		$this->ci->player->insertManualSubtractBalanceTag($data);
	}

	public function editManualSubtractBalanceTag($data, $id) {
		return $this->ci->player->editManualSubtractBalanceTag($data, $id);
	}

	public function insertTransactionsTag($data) {
		return $this->ci->player->insertTransactinosTag($data);
	}
	public function deleteTransactionsTag($id) {
		return $this->ci->player->deleteTransactionsTag($id);
	}

	public function deleteManualSubtractBalanceTag($id) {
		return $this->ci->player->deleteManualSubtractBalanceTag($id);
	}

	/**
	 * Get player's dispatch account level
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getPlayerDispatchAccountLevel($player_id) {
		return $this->ci->player->getPlayerDispatchAccountLevel($player_id);
	}
}

/* End of file player_manager.php */
/* Location: ./application/libraries/player_manager.php */
