<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behavoirs include :
 *
 * * Get game providate data by player info
 * * Sync and get all game platform
 * * Updating external account, password, block status
 * * Validate and get player token
 * * Save game authentication
 * * Sync game accounts
 * * Block and unblock players in game
 *
 * @category Game Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Game_provider_auth extends BaseModel {

	protected $tableName = 'game_provider_auth';

	const SOURCE_REGISTER = 1;
	const SOURCE_BATCH = 2;
	const API_NOT_REGISTERED = 0;
	const IS_BLOCKED = 1;
	const IS_UNBLOCKED = 0;

	const RANDOM_STRING_ALPHA = 'alpha';
	const RANDOM_STRING_ALNUM = 'alnum';

	const MAIN_WALLET = 0;
	const GPA_DUPLICATE_PREFIX_REASON = 'Duplicate prefix wallet adjustment';


	function __construct() {
		parent::__construct();
	}

	/**
	 * @param $loginName
	 * @param $gamePlatformId
	 * @return null|string
	 */
	function getPasswordByLoginName($loginName, $gamePlatformId) {
		$qry = $this->db->get_where($this->tableName, array('login_name' => strtolower($loginName), "game_provider_id" => $gamePlatformId, "status" => self::STATUS_NORMAL));
		return $this->getOneRowOneField($qry, 'password');
	}

	/**
	 * overview : get password by player id
	 *
	 * @param $playerId
	 * @param $gamePlatformId
	 * @return null|string
	 */
	function getPasswordByPlayerId($playerId, $gamePlatformId) {
		$qry = $this->db->get_where($this->tableName, array('player_id' => $playerId, "game_provider_id" => $gamePlatformId,
			"status" => self::STATUS_NORMAL));
		return $this->getOneRowOneField($qry, 'password');
	}

	/**
	 * overview : get password by uuid
	 *
	 * @param $password
	 * @param $gamePlatformId
	 * @return string
	 */
	function getPasswordByUuid($password, $gamePlatformId) {
		$qry = $this->db->get_where($this->tableName, array('password' => $password, "game_provider_id" => $gamePlatformId,
			"status" => self::STATUS_NORMAL));
		return $this->getOneRowOneField($qry, 'login_name');
	}

	/**
	 * overview : save player password
	 *
	 * @param $player
	 * @param $gamePlatformId
	 * @return array
	 */
	function savePasswordForPlayer($player, $gamePlatformId, $extra=null) {
		if(empty($player['is_demo_flag'])){
			$player['is_demo_flag']='0';
		}
		// $prefixForce=isset($extra['prefix']) ? $extra['prefix'] : null;

		//check first
		$this->db->select('id')->from($this->tableName)->where('player_id', $player['id'])
			->where('game_provider_id', $gamePlatformId);
		$id=$this->runOneRowOneField('id');
		if (!empty($id)) {
			$gameUsername=$this->convertUsernameToGame($player['username'], $gamePlatformId, $player['id']);
			$data=[
				'login_name' => $gameUsername,
				'password' => $player['password'],
				'source' => $player['source'],
				'is_demo_flag' => $player['is_demo_flag'],
                'agent_id' => @$player['agent_id'],
				'sma_id' => @$player['sma_id'],
				'last_sync_at' => $this->utils->getNowForMysql(),
			];
			$this->db->set($data)->where('id', $id);
			return $this->runAnyUpdate($this->tableName);
		} else {
			$gameUsername=$this->convertUsernameToGame($player['username'], $gamePlatformId, $player['id']);

			if(!empty($extra['fix_username_limit'])) {
				if (isset($extra['minimum_user_length']) && isset($extra['maximum_user_length']) ) {
					$min_user_length = $extra['minimum_user_length'];
					$max_user_length = $extra['maximum_user_length'];

					$force_lowercase = (isset($extra['force_lowercase']) && $extra['force_lowercase']) ? true : false;

					$usernameLen = strlen($extra['prefix'] . $player['username']);
					if($usernameLen < $min_user_length || $usernameLen > $max_user_length) {
						$gameUsername = $this->generateRandomGameAccount($extra['prefix'], $gamePlatformId, self::RANDOM_STRING_ALPHA, $extra['default_fix_name_length'], $force_lowercase);
					}
					// FOR SOME API THAT HAVE STRICT ON USERNAME WITH DELIMITER
					if(isset($extra['check_username_only']) && $extra['check_username_only']){
						$usernameLen = strlen($player['username']);
						// If min_user_length = 0 then the API dont have restriction on minimum user length only on maximum user length
						if ($usernameLen < $min_user_length && $min_user_length != 0) {
							$gameUsername = $this->generateRandomGameAccount($extra['prefix'], $gamePlatformId, self::RANDOM_STRING_ALPHA, $min_user_length, $force_lowercase);
						} else if ($usernameLen > $max_user_length) {
							$gameUsername = $this->generateRandomGameAccount($extra['prefix'], $gamePlatformId, self::RANDOM_STRING_ALPHA, $max_user_length, $force_lowercase);
						}

						// FOR SOME API THAT HAVE STRING ON USERNAME COUNT TOGETHER WITH PREFIX
						if(isset($extra['strict_username_with_prefix_length']) && $extra['strict_username_with_prefix_length']) {
							if (strlen($extra['prefix'] . $player['username']) > $max_user_length) {
								$gameUsername = $this->generateRandomGameAccount($extra['prefix'], $gamePlatformId, self::RANDOM_STRING_ALNUM, $max_user_length - strlen($extra['prefix']), $force_lowercase);
							}
						}
					}
				}
			}

			$password = $player['password'];

			//OGP-16840
			$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
			if($api){
				$gamePasswordPrefix = $api->getPlayerGamePasswordPrefix();
				if($gamePasswordPrefix){
					$password = $gamePasswordPrefix.$password;
				}
			}

			$data=[
				'player_id' => $player['id'],
				'game_provider_id' => $gamePlatformId,
				'login_name' => $gameUsername,
				'password' => $password,
				'status' => self::STATUS_NORMAL,
				'source' => $player['source'],
				'is_demo_flag' => $player['is_demo_flag'],
                'agent_id' => @$player['agent_id'],
				'sma_id' => @$player['sma_id'],
				'created_at' => $this->utils->getNowForMysql(),
			];
			return $this->insertData($this->tableName, $data);
		}
	}

	public function checkGameAccountIfExist($gameAccount, $gamePlatformId) {
        $this->db->select('id');
        $this->db->from($this->tableName);
        $this->db->where('login_name', $gameAccount);
        $this->db->where('game_provider_id', $gamePlatformId);
    	return $this->runExistsResult();
	}

	public function getRandomGameAccount($game_account_prefix, $random_rules, $count) {
		return $game_account_prefix . random_string($random_rules, $count);
	}

	public function generateRandomGameAccount($game_account_prefix, $gamePlatformId, $random_rules, $count, $force_lowercase = false) {
		$gameUsername = $this->getRandomGameAccount($game_account_prefix, $random_rules, $count);
		if ($force_lowercase) {
			$gameUsername = strtolower($gameUsername);
		}
		$isExists = $this->checkGameAccountIfExist($gameUsername, $gamePlatformId);
		if ($isExists) {
			return $this->generateRandomGameAccount($game_account_prefix, $random_rules, $count);
		}
		return $gameUsername;
	}

	/**
	 * overview : convert username to game
	 *
	 * @param $username
	 * @param $gamePlatformId
	 * @return string
	 */
	protected function convertUsernameToGame($username, $gamePlatformId, $playerId=null) {
		$gameApi = $this->utils->loadExternalSystemLibObject($gamePlatformId);
		if(!empty($gameApi)) {
			return $gameApi->convertUsernameToGame($username);
		}
		return $username;
	}

	/**
	 * overview : save password for player on all game platforms
	 *
	 * @param $player
	 */
	function savePasswordForPlayerOnAllGamePlatforms($player) {
		// $this->load->library('game_platform/game_platform_manager');
		$list = $this->utils->getAllCurrentGameSystemList();

		foreach ($list as $gamePlatformId) {
			// foreach (Game_platform_manager::API_MAPS as $gamePlatformId => $value) {
			$username = $player['username'];
			$player['username'] = $this->convertUsernameToGame($username, $gamePlatformId, $player['id']);

			//save all game platform
			$this->savePasswordForPlayer($player, $gamePlatformId);
			//save back
			$player['username'] = $username;
		}
	}

	/**
	 * overview : sync all game platform
	 *
	 * @param $playerId
	 * @param $username
	 * @param $password
	 */
	public function syncAllGamePlatform($playerId, $username, $password) {
		$this->load->model(['player_model']);
		$player=$this->player_model->getPlayerArrayById($playerId);
		$player = array('id' => $playerId, 'username' => $username, 'password' => $password,
			'source' => self::SOURCE_BATCH, 'is_demo_flag'=>false, 'agent_id'=>@$player['agent_id']);
		$this->savePasswordForPlayerOnAllGamePlatforms($player);
	}

	public function syncGamePlatformLoginName($playerId, $username, $platformId) {
		#$username = $this->convertUsernameToGame($username, $platformId);
		$this->db->where('player_id', $playerId);
		$this->db->where('game_provider_id', $platformId);
		$this->db->update($this->tableName, array('login_name'=>$username));
		return $this->db->affected_rows();
	}

	/**
	 * overview : update register flag
	 *
	 * @param $playerId
	 * @param $platformCode
	 * @param $data
	 */
	function updateRegisterFlag($playerId, $platformCode, $data) {
		$this->db->where('player_id', $playerId);
		$this->db->where('game_provider_id', $platformCode);
		$this->db->set($data);

		return $this->runAnyUpdate($this->tableName);
	}

	/**
	 * overview : update external account id for player
	 *
	 * @param $playerId
	 * @param $platformCode
	 * @param $data
	 */
	function updateExternalAccountIdForPlayer($playerId, $platformCode, $data) {
		$this->db->where('player_id', $playerId);
		$this->db->where('game_provider_id', $platformCode);
		$this->db->update($this->tableName, $data);
	}

	/**
	 * overview : get game providers
	 *
	 * @param $playerId
	 * @return array
	 */
	function getGameProviders($playerId) {
		$this->db->select('game.gameId, game.game, game_provider_auth.status');
		$this->db->from($this->tableName);
		$this->db->join('game', 'game.gameId = game_provider_auth.game_provider_id', 'left');
		$this->db->where('player_id', $playerId);
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * overview : update status of game provider
	 *
	 * @param $player_id
	 * @param $game_provider_id
	 */
	function toggleGameProvider($player_id, $game_provider_id) {
		$this->db->set('status', 'CASE
			WHEN status = 1 THEN 2
			WHEN status = 2 THEN 1
       	END', false);
		$this->db->where('player_id', $player_id);
		$this->db->where('game_provider_id', $game_provider_id);
		$this->db->update($this->tableName);
	}

	/**
	 * overview : get player id by external account id
	 *
	 * @param $externalAccountId
	 * @param $platformId
	 * @return null|int
	 */
	function getPlayerIdByExternalAccountId($externalAccountId, $platformId) {
		if (!empty($externalAccountId)) {
			$this->db->select('player_id');
			$this->db->where('external_account_id', $externalAccountId);
			$this->db->where('game_provider_id', $platformId);
			$qry = $this->db->get($this->tableName);
			return $this->getOneRowOneField($qry, 'player_id');
		}
		return null;
	}

	/**
	 * overview : get game username by player id
	 *
	 * @param $playerId
	 * @param $platformId
	 * @return null|string
	 */
	function getGameUsernameByPlayerId($playerId, $platformId) {
		if (!empty($playerId)) {
			// $this->db->select('external_account_id');
			// $this->db->where('login_name', $playerName);
			// $this->db->where('game_provider_id', $platformId);
			$this->getOrCreateLoginInfoByPlayerId($playerId, $platformId);

			$this->db->select('login_name')->from($this->tableName)
				->where('game_provider_auth.player_id', $playerId)
				->where('game_provider_auth.game_provider_id', $platformId);
				// ->where('game_provider_auth.register','1');
			$qry = $this->db->get();
			return $this->getOneRowOneField($qry, 'login_name');
		}
		return null;
	}

	/**
	 * overview : get game username by player username
	 *
	 * @param $playerUsername
	 * @param $platformId
	 * @return null|array
	 */
	function getGameUsernameByPlayerUsername($playerUsername, $platformId) {
		if (!empty($playerUsername)) {
			$this->load->model('player_model');
			$playerId = $this->player_model->getPlayerIdByUsername($playerUsername);

			return $this->getGameUsernameByPlayerId($playerId, $platformId);

			// $this->db->select('external_account_id');
			// $this->db->where('login_name', $playerName);
			// $this->db->where('game_provider_id', $platformId);

			// $this->getOrCreateLoginInfoByPlayerId($playerId);

			// $this->db->select('login_name')->from($this->tableName)
			// 	->join('player', 'player.playerId=game_provider_auth.player_id')
			// 	->where('player.username', $playerUsername)
			// 	->where('game_provider_auth.game_provider_id', $platformId);
			// $qry = $this->db->get();
			// return $this->getOneRowOneField($qry, 'login_name');
		}
		return null;
	}

	/**
	 * overview : check if game account is demo account
	 * @param $gameUsername
	 * @param $platformId
	 * @return bool
	 */
	function isGameAccountDemoAccount($gameUsername, $platformId) {
		$this->db->select('is_demo_flag')->from($this->tableName);
		$this->db->where('login_name', strtolower($gameUsername));
		$this->db->where('game_provider_id', $platformId);
		$qry = $this->db->get();
		$result = $this->getOneRow($qry);
		if (!empty($result)) {
			if ($result->is_demo_flag) {
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * overview : get player information by game username
	 *
	 * @param $gameUsername
	 * @param $platformId
	 * @return null
	 */
	function getPlayerInfoByGameUsername($gameUsername, $platformId) {
		if (!empty($gameUsername) && !empty($platformId)) {
			$this->db->select('player.username, player.password')->from($this->tableName)
				->join('player', 'player.playerId=game_provider_auth.player_id');
			$this->db->where('login_name', strtolower($gameUsername));
			$this->db->where('game_provider_id', $platformId);
			$qry = $this->db->get();
			return $this->getOneRow($qry);
		}
		return null;
	}

	/**
	 * overview : get player usrname by game username
	 *
	 * @param $gameUsername
	 * @param $platformId
	 * @return null|string
	 */
	function getPlayerUsernameByGameUsername($gameUsername, $platformId) {
		if (!empty($gameUsername) && !empty($platformId)) {
			$this->db->select('player.username')->from($this->tableName)
				->join('player', 'player.playerId=game_provider_auth.player_id');
			$this->db->where('login_name', strtolower($gameUsername));
			$this->db->where('game_provider_id', $platformId);
			$qry = $this->db->get();
			return $this->getOneRowOneField($qry, 'username');
		}
		return null;
	}

	/**
	 * overview : get external account by player username
	 *
	 * @param $playerUsername
	 * @param $platformId
	 * @return null|int
	 */
	function getExternalAccountIdByPlayerUsername($playerUsername, $platformId) {
		if (!empty($playerUsername)) {
			// $this->db->select('external_account_id');
			// $this->db->where('login_name', $playerName);
			// $this->db->where('game_provider_id', $platformId);

			$this->db->select('external_account_id')->from($this->tableName)
				->join('player', 'player.playerId=game_provider_auth.player_id')
				->where('player.username', $playerUsername)
				->where('game_provider_auth.game_provider_id', $platformId);
			$qry = $this->db->get();
			return $this->getOneRowOneField($qry, 'external_account_id');
		}
		return null;
	}

	/**
	 * overview : get player id by player name
	 *
	 * @param $gameUsername
	 * @param $platformId
	 * 
	 * @return mixed
	 */
	function getPlayerIdByPlayerName($gameUsername, $platformId) {
		if (!empty($gameUsername)) {

			$this->db->select('player_id');
			$this->db->where('login_name', strtolower($gameUsername));
			$this->db->where('game_provider_id', $platformId);
			$qry = $this->db->get($this->tableName);

			return $this->getOneRowOneField($qry, 'player_id');
		}
		return null;
	}

	/**
	 * overview : update password for player
	 *
	 * @param $playerId
	 * @param $password
	 * @param $gamePlatformId
	 * @return mixed
	 */
	public function updatePasswordForPlayer($playerId, $password, $gamePlatformId) {
		$this->db->where('player_id', $playerId)->where('game_provider_id', $gamePlatformId);
		$this->db->set(array('password' => $password));
		return $this->runAnyUpdate($this->tableName);
	}

	public function updateUsernameForPlayer($playerId, $username, $gamePlatformId) {
		$this->db->where('player_id', $playerId)->where('game_provider_id', $gamePlatformId);
		return $this->db->update($this->tableName, array('login_name' => $username));
	}

	/**
	 * overview : update block status
	 *
	 * @param $playerId
	 * @param $gamePlatformId
	 * @param $block
	 * @return bool
	 */
	public function updateBlockStatusInDB($playerId, $gamePlatformId, $block) {
		$this->utils->debug_log('playerId', $playerId, 'gamePlatformId', $gamePlatformId, 'block', $block);
		$this->db->where('player_id', $playerId)->where('game_provider_id', $gamePlatformId);
		// return $this->db->update($this->tableName, array('is_blocked' => $block));
		// var_dump(array('is_blocked' => ($block ? 1 : 0)));exit();
		return $this->runUpdate(array('is_blocked' => ($block ? 1 : 0)));
	}

	public function isBlockedUsernameInDB($playerId, $gamePlatformId) {
		$this->db->select('is_blocked')->from($this->tableName)
			->where('player_id', $playerId)->where('game_provider_id', $gamePlatformId);

		return $this->runOneRowOneField('is_blocked') == '1';
	}

	/**
	 * overview : get player by game username and password
	 *
	 * @param $gameUsername
	 * @param $gamePassword
	 * @param $gamePlatformId
	 * @return null
	 */
	function getPlayerByGameUsernameAndPassword($gameUsername, $gamePassword, $gamePlatformId) {
		$this->load->library(array('salt'));

		$this->db->select('player.username, player.password')->from($this->tableName)->join('player', 'player.playerId=game_provider_auth.player_id')
			->where('login_name', strtolower($gameUsername))
			->where(array('game_provider_id' => $gamePlatformId,
				'game_provider_auth.password' => $gamePassword));

		$player = $this->runOneRow();

		// $this->utils->debug_log('player', $player);
		if ($player) {
			$player->password = $this->salt->decrypt($player->password, $this->config->item('DESKEY_OG'));
		}

		return $player;
	}

	/**
	 * overview : get player by game username
	 *
	 * @param $gameUsername
	 * @param $gamePlatformId
	 * @return null
	 */
	function getPlayerByGameUsername($gameUsername, $gamePlatformId) {
		$this->load->library(array('salt'));

		$this->db->select('player.username, player.password')->from($this->tableName)->join('player', 'player.playerId=game_provider_auth.player_id')
			->where('login_name', strtolower($gameUsername))
			->where('game_provider_id', $gamePlatformId);

		$player = $this->runOneRow();

		// $this->utils->printLastSQL();
		$this->utils->debug_log('player', $player);
		if ($player) {
			$player->password = $this->salt->decrypt($player->password, $this->config->item('DESKEY_OG'));
		}

		return $player;
	}

	/**
	 * overview : get login information by player id
	 *
	 * @param $playerId
	 * @param $gamePlatformId
	 * @return null
	 */
	function getLoginInfoByPlayerId($playerId, $gamePlatformId) {
		$this->db->from($this->tableName)->where(
			array('player_id' => $playerId, "game_provider_id" => $gamePlatformId,
				"status" => self::STATUS_NORMAL)
		);

		// $qry = $this->db->get_where($this->tableName, array('player_id' => $playerId, "game_provider_id" => $gamePlatformId,
		// 	"status" => self::STATUS_NORMAL));

		return $this->runOneRow();
	}

	/**
	 * overview : get game platforms
	 *
	 * @param $player_id
	 * @return mixed
	 *
	 * Updated By Frans Eric Dela Cruz (frans.php.ph) 10-24-2018
	 */
	public function getGamePlatforms($player_id) {
		// $this->db->update($this->tableName, array(
		// 	'is_blocked' => self::STATUS_NORMAL,
		// 	'blockedStart' => null,
		// 	'blockedEnd' => null,
		// ), array(
		// 	'is_blocked' => self::STATUS_DISABLED,
		// 	'blockedEnd' => $this->utils->getNowForMysql(),
		// ));
		$this->db->select(array(
			'external_system.id id',
			'external_system.system_code system_code',
			"{$this->tableName}.id playerGameId",
			"{$this->tableName}.is_blocked blocked",
			"{$this->tableName}.blockedStart blockedStart",
			"{$this->tableName}.blockedEnd blockedEnd",
			"{$this->tableName}.last_sync_at last_sync_at",
			"{$this->tableName}.status status",
			"{$this->tableName}.register register",
			"{$this->tableName}.login_name login_name",
			"{$this->tableName}.is_demo_flag",
		));
		$this->db->from('external_system');
		$this->db->join($this->tableName, "external_system.id = {$this->tableName}.game_provider_id AND {$this->tableName}.player_id = {$player_id}", 'left');
		$this->db->where('external_system.system_type', SYSTEM_GAME_API);

		if (!$this->utils->isEnabledFeature('show_inactive_subwallet_in_balance_adjustment')) $this->db->where('external_system.status',  self::STATUS_NORMAL); // get active only
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * overiew : get all game username
	 *
	 * @param $gamePlatformId
	 * @param bool|false $activeOnly
	 * @return array
	 */
	public function getAllGameUsernames($gamePlatformId, $activeOnly = false) {
		$this->db->select('game_provider_auth.player_id,game_provider_auth.login_name')->from($this->tableName)
			->where('game_provider_auth.game_provider_id', $gamePlatformId)
			->where('game_provider_auth.status', self::STATUS_NORMAL);

		if ($activeOnly) {
			//join player
			$this->db->join('player', 'player.playerId=game_provider_auth.player_id')
				->where('player.active_status', self::DB_TRUE);
		}

		$usernames = array();
		$rows = $this->runMultipleRow();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$usernames[] = $row->login_name;
			}
		}

		return $usernames;
	}

	/**
	 * overiew : get all game username that already registered in game provider
	 *
	 * @param $gamePlatformId
	 * @return array
	 */
	public function getAllGameRegisteredUsernames($gamePlatformId) {
		$this->db->select('game_provider_auth.player_id,game_provider_auth.login_name')->from($this->tableName)
			->where('game_provider_auth.game_provider_id', $gamePlatformId)
			->where('game_provider_auth.register', self::DB_TRUE)
			->where('game_provider_auth.status', self::STATUS_NORMAL);

		$usernames = array();
		$rows = $this->runMultipleRow();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$usernames[] = $row->login_name;
			}
		}

		return $usernames;
	}

	/**
	 * overview : validate token
	 *
	 * @param $token
	 * @return bool|null
	 */
	public function validateToken($token) {
		$this->db->select('login_name')->from($this->tableName)
			->where('password', $token);
		$qry = $this->db->get();
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return $this->getOneRow($qry);
		}
	}

	/**
	 * overview : get player token
	 *
	 * @param $token
	 * @return bool|null
	 */
	public function getPlayerToken($token) {
		$this->db->select('login_name')->from($this->tableName)
			->where('password', $token);
		$qry = $this->db->get();
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return $this->getOneRow($qry);
		}
	}

	/**
	 * overview : get no prefix
	 *
	 * @return null|array
	 */
	public function getNoPrefix() {
		$apis = $this->utils->getApiExistsPrefix();

		if (!empty($apis)) {
			$this->db->from($this->tableName);
			$apiCond = '';
			foreach ($apis as $platformCode => $platformApi) {
				$prefix = $platformApi->getSystemInfo('prefix_for_username');
				$cond = ' (game_provider_id=' . $platformCode . ' and login_name not like "' . $prefix . '%")';
				if (empty($apiCond)) {
					$apiCond = $cond;
				} else {
					$apiCond .= ' or ' . $cond;
				}
			}
			$this->db->where($apiCond, null, false);

			return $this->runMultipleRow();
		}

		return null;

	}

	/**
	 * overview : check if sync balance is enabled by username
	 * @param $gamePlatformId
	 * @param $username
	 * @return bool
	 */
	public function isEnabledSyncBalanceByUsername($gamePlatformId, $username) {
		$this->load->model(array('player_model'));
		$playerId = $this->player_model->getPlayerIdByUsername($username);
		return $this->isEnabledSyncBalance($gamePlatformId, $playerId);
	}

	/**
	 * overview : check if sync balance is enabled by player id
	 *
	 * @param $gamePlatformId
	 * @param $playerId
	 * @return bool
	 */
	public function isEnabledSyncBalance($gamePlatformId, $playerId) {
		$this->db->from($this->tableName)->where(
			array('player_id' => $playerId, "game_provider_id" => $gamePlatformId,
				"status" => self::STATUS_NORMAL)
		);

		return $this->runOneRowOneField('enabled_sync_balance') == self::DB_TRUE;
	}

	/**
	 * overview : save game authentication
	 *
	 * @param $player
	 * @param $gamePlatformId
	 * @param int $source
	 * @return bool
	 */
	public function saveCreateGameAuth($player, $gamePlatformId, $source = self::SOURCE_REGISTER) {
		//check first
		$this->db->from($this->tableName)->where('player_id', $player->playerId)
			->where('game_provider_id', $gamePlatformId);
		if ($this->runExistsResult()) {
			// $this->db->where('player_id', $player['id'])->where('game_provider_id', $gamePlatformId);
			// return $this->db->update($this->tableName, array('login_name' => strtolower($player['username']),
			// 	'password' => $player['password'], 'source' => $player['source']));
			return true;
		} else {
			$this->load->library(array('salt'));
			$this->load->model(array('agency_model'));

			$password = $this->salt->decrypt($player->password, $this->getDeskeyOG());

			//OGP-16840
			$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
			if($api){
				$gamePasswordPrefix = $api->getPlayerGamePasswordPrefix();
				if($gamePasswordPrefix){
					$password = $gamePasswordPrefix.$password;
				}
			}

			$gameUsername = $this->convertUsernameToGame($player->username, $gamePlatformId, $player->playerId);
			// $gameUsername = $api->convertUsernameToGame(strtolower($player->username));

//			$this->utils->debug_log("======================player", $player);
//			$this->utils->debug_log("======================agent_id", $player->agent_id);

			$root_agent_id = $this->agency_model->getRootAgencyByAgentId($player->agent_id);

//			$this->utils->debug_log("======================root_agent_id", $root_agent_id);

			return $this->insertData($this->tableName,
				array(
					'player_id' => $player->playerId,
					'game_provider_id' => $gamePlatformId,
					'login_name' => $gameUsername,
					'password' => $password,
					'status' => self::STATUS_NORMAL,
					// 'register' => self::DB_FALSE,
					'source' => $source,
					'agent_id' => $player->agent_id,
					'sma_id' => $root_agent_id
				)
			);

		}

	}

	/**
	 * overview : safe sync game platforms
	 *
	 * @return mixed
	 */
	public function safeSyncGamePlatforms() {
		$sql = <<<EOD
select player_id as playerId, group_concat(game_provider_id) as gameIds from game_provider_auth
group by player_id
EOD;

		$cnt = 0;
		$failedIds = array();
		$rows = $this->runRawSelectSQL($sql);
		$gameApiArr = $this->utils->getAllCurrentGameSystemList();
		foreach ($rows as $row) {
			$playerId = $row->playerId;
			$gameIdStr = $row->gameIds;
			$gameIdArr = explode(',', $gameIdStr);
			$diffArr = array_diff($gameApiArr, $gameIdArr);
			if (!empty($diffArr)) {
				$this->db->from('player')->where('playerId', $playerId);
				$player = $this->runOneRow();

				foreach ($diffArr as $gamePlatformId) {
					if ($this->saveCreateGameAuth($player, $gamePlatformId, self::SOURCE_BATCH)) {
						$cnt++;
					} else {
						$failedIds[] = $playerId . '-' . $gamePlatformId;
					}
				}
			}
		}

		return $this->utils->debug_log("total player count", count($rows),
			"count insert game auth", $cnt, "failedIds", $failedIds);

	}

	/**
	 * overview : get or create login information by player id
	 *
	 * @param $playerId
	 * @param $gamePlatformId
	 * @return array
	 */
	function getOrCreateLoginInfoByPlayerId($playerId, $gamePlatformId) {
		// $this->db->from($this->tableName)->where(
		// 	array('player_id' => $playerId, "game_provider_id" => $gamePlatformId,
		// 		"status" => self::STATUS_NORMAL)
		// );
		// $info = $this->runOneRow();
		$info = $this->getLoginInfoByPlayerId($playerId, $gamePlatformId);
		if (!$info && $playerId && $gamePlatformId!=TCG_API) {
			$this->db->from('player')->where('playerId', $playerId);
			$player = $this->runOneRow();
			//create
			$this->saveCreateGameAuth($player, $gamePlatformId);
			$info = $this->getLoginInfoByPlayerId($playerId, $gamePlatformId);
		}

		// $this->utils->debug_log('Game Provider Auth: ', $info);
		// $qry = $this->db->get_where($this->tableName, array('player_id' => $playerId, "game_provider_id" => $gamePlatformId,
		// 	"status" => self::STATUS_NORMAL));

		return $info;
	}

	/**
	 * overview : check if player is registered
	 *
	 * @param $playerId
	 * @param $platformId
	 * @return bool
	 */
	public function isRegisterd($playerId, $platformId) {
		$this->db->from('game_provider_auth')->where('player_id', $playerId)
			->where('game_provider_id', $platformId);

		return $this->runOneRowOneField('register') == self::DB_TRUE;
	}

	public function isRegisterdByEntry($entry){
        return $entry['register'] == self::DB_TRUE;
    }

	/**
	 * overview : get all unregister account
	 *
	 * @return null
	 */
	public function getUnregisterAccount() {
		$this->db->from('game_provider_auth')->where('register', self::DB_FALSE);
		return $this->runMultipleRow();
	}

	/**
	 * overview : set register
	 * @param $playerId
	 * @param $platformCode
	 * @param $registered
	 */
	function setRegisterFlag($playerId, $platformCode, $registered) {
		// $this->db->where('player_id', $playerId);
		// $this->db->where('game_provider_id', $platformCode);
		// $this->db->update($this->tableName, $data);

		return $this->updateRegisterFlag($playerId, $platformCode, array('register' => $registered));
	}

	/**
	 * overview : save create game auth
	 *
	 * @param $gameUsername
	 * @param $playerId
	 * @param $encryptedPassword
	 * @param $gamePlatformId
	 * @param int $source
	 * @return null
	 */
	public function safeSaveCreateGameAuth($gameUsername, $playerId, $encryptedPassword, $gamePlatformId, $source = self::SOURCE_REGISTER) {
		//check first
		$this->db->from($this->tableName)->where('player_id', $playerId)
			->where('game_provider_id', $gamePlatformId);
		$id = $this->runOneRowOneField('id');
		if (!empty($id)) {
			// $this->db->where('player_id', $player['id'])->where('game_provider_id', $gamePlatformId);
			// return $this->db->update($this->tableName, array('login_name' => strtolower($player['username']),
			// 	'password' => $player['password'], 'source' => $player['source']));
			return $id;
		} else {
			$this->load->model(['player_model']);
			$this->load->library(array('salt'));
			$password = $this->salt->decrypt($encryptedPassword, $this->getDeskeyOG());

			//OGP-16840
			$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
			if($api){
				$gamePasswordPrefix = $api->getPlayerGamePasswordPrefix();
				if($gamePasswordPrefix){
					$password = $gamePasswordPrefix.$password;
				}
			}

			$player=$this->player_model->getPlayerArrayById($playerId);

			return $this->insertData($this->tableName, array('player_id' => $playerId,
				'game_provider_id' => $gamePlatformId, 'login_name' => strtolower($gameUsername), 'password' => $password,
				'status' => self::STATUS_NORMAL, 'source' => $source,'agent_id' => $player['agent_id']));

		}

	}

	/**
	 * overview : sync game account
	 *
	 * @param $playerId
	 * @param $gameUsername
	 * @param $password
	 * @param $gamePlatformId
	 * @param $register
	 * @param int $source
	 * @return array
	 */
	function syncGameAccount($playerId, $gameUsername, $password, $gamePlatformId, $register, $source = self::SOURCE_BATCH) {
		// $this->load->library(array('salt'));
		// $password = $this->salt->decrypt($encryptedPassword, $this->getDeskeyOG());
		$this->load->model(['player_model']);
		$player=$this->player_model->getPlayerArrayById($playerId);

		//check first
		$this->db->from($this->tableName)->where('player_id', $playerId)
			->where('game_provider_id', $gamePlatformId);
		// $this->utils->debug_log("==============================", $gameUsername, 'register', $register);
		if ($this->runExistsResult()) {
			$this->db->where('player_id', $playerId)->where('game_provider_id', $gamePlatformId);
			return $this->db->update($this->tableName, array('login_name' => strtolower($gameUsername), 'password' => $password,
				'status' => self::STATUS_NORMAL, 'register' => $register, 'source' => $source, 'agent_id' => $player['agent_id']));

		} else {
			//OGP-16840
			$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
			if($api){
				$gamePasswordPrefix = $api->getPlayerGamePasswordPrefix();
				if($gamePasswordPrefix){
					$password = $gamePasswordPrefix.$password;
				}
			}

			return $this->db->insert($this->tableName, array('player_id' => $playerId,
				'game_provider_id' => $gamePlatformId, 'login_name' => strtolower($gameUsername), 'password' => $password,
				'status' => self::STATUS_NORMAL, 'register' => $register, 'source' => $source, 'agent_id' => $player['agent_id']));

		}

	}

	/**
	 * overview : login game provider auth
	 * @param $gameUsername
	 * @param $password
	 * @param $gamePlatformId
	 * @return bool
	 */
	function loginGameProviderAuth($gameUsername, $password, $gamePlatformId) {

		$passwordInDB = $this->getPasswordByLoginName($gameUsername, $gamePlatformId);
		$this->utils->debug_log('gameUsername', $gameUsername, 'password', $password);
		if (!empty($passwordInDB)) {
			return $passwordInDB == $password;
		}

		return false;
	}

	/**
	 * overview : get game platform id
	 *
	 * @param $playerId
	 * @param $gamePlatformId
	 * @return array
	 */
	function getByPlayerIdGamePlatformId($playerId, $gamePlatformId) {
		$this->db->from('game_provider_auth')->where('player_id', $playerId)->where('game_provider_id', $gamePlatformId);

		return $this->runOneRowArray();
	}

	/**
	 * overview : get all account by game platform
	 *
	 * @param $game_platform_id
	 * @return array
	 */
	public function getAllAccountsByGamePlatform($game_platform_id) {
		$this->db->select('game_provider_auth.id, game_provider_auth.login_name game_username, game_provider_auth.player_id, player.username player_username,player.total_real player_total_balance')
			->from('game_provider_auth')->join('player', 'player.playerId=game_provider_auth.player_id')
			->where('game_provider_id', $game_platform_id);

		return $this->runMultipleRowArray();
	}

	/**
	 * overview : rebuild game account name
	 *
	 * @param $id
	 * @param $newGameAccount
	 * @param $oldGameAccount
	 * @return bool
	 */
	public function rebuildGameAccountName($id, $newGameAccount, $oldGameAccount) {
		$this->db->set('notes', $oldGameAccount)->set('login_name', $newGameAccount)->where('id', $id);

		return $this->runAnyUpdate('game_provider_auth');
	}

	/**
	 * overview : block players game account
	 *
	 * @param $playerIdArr
	 * @param null $gamePlatformId
	 * @return bool
	 */
	public function blockPlayersGameAccount($playerIdArr, $gamePlatformId = null) {
		if(empty($playerIdArr)){
			return true;
		}

		if (!is_array($playerIdArr)) {
			$playerIdArr = [$playerIdArr];
		}

		$this->db->set('is_blocked', 1)->where_in('player_id', $playerIdArr);

		$success = $this->runAnyUpdate('game_provider_auth');

		return $success;
	}

	/**
	 * overview : unblock player game account
	 *
	 * @param $playerIdArr
	 * @param null $gamePlatformId
	 * @return bool
	 */
	public function unblockPlayersGameAccount($playerIdArr, $gamePlatformId = null) {
		if(empty($playerIdArr)){
			return true;
		}

		if (!is_array($playerIdArr)) {
			$playerIdArr = [$playerIdArr];
		}

		$this->db->set('is_blocked', 0)->where_in('player_id', $playerIdArr);

		$success = $this->runAnyUpdate('game_provider_auth');

		return $success;

	}

	public function exportPassword($gamePlatformId){

		$this->db->select('login_name, password')->from('game_provider_auth')
		  ->where('game_provider_id', $gamePlatformId);

		$rows=$this->runMultipleRowArray();

		return $rows;
	}

	/**
	 * overview : check if player is online on the game
	 *
	 * @param $gamePlatformId
	 * @param $playerId
	 * @return bool
	 */
	public function isPlayerStatusOnline($gamePlatformId, $playerId) {
		$this->db->from($this->tableName)->where(array('player_id' => $playerId, "game_provider_id" => $gamePlatformId));
		return $this->runOneRowOneField('is_online') == self::DB_TRUE;
	}

	/**
	 * overview : Set player online on the game
	 *
	 * @param $gamePlatformId
	 * @param $playerId
	 * @return bool
	 */
	public function setPlayerStatusOnline($gamePlatformId, $playerId) {
		$this->db->set('is_online', 1)->where(array('player_id' => $playerId, "game_provider_id" => $gamePlatformId));
		return $this->runAnyUpdate('game_provider_auth');
	}

	/**
	 * overview :Set player offline on the game
	 *
	 * @param $gamePlatformId
	 * @param $playerId
	 * @return bool
	 */
	public function setPlayerStatusLoggedOff($gamePlatformId, $playerId) {
		$this->db->set('is_online', 0)->where(array('player_id' => $playerId, "game_provider_id" => $gamePlatformId));
		return $this->runAnyUpdate('game_provider_auth');
	}

	public function getAllGameAccountsByPlayerId($playerId){
		$this->db->from('game_provider_auth')->where(['player_id'=>$playerId]);

		return $this->runMultipleRowArray();
	}

	public function getAllGameAccountsKVByPlayerId($playerId){
	    $all_game_accounts = $this->getAllGameAccountsByPlayerId($playerId);

	    if(empty($all_game_accounts)){
	        return [];
        }

        $result = [];
	    foreach($all_game_accounts as $entry){
	        $result[$entry['game_provider_id']] = $entry;
        }

        return $result;
    }

	public function getRegisteredAccount() {
		$this->db->from('game_provider_auth')->where('register', self::DB_TRUE)->order_by('id desc');
		return $this->runMultipleRow();
	}

	public function getAllGameAccounts($max_id=null) {
		$this->db->from('game_provider_auth')->order_by('id desc');
		if(!empty($max_id)){
			$this->db->where('id <=', $max_id);
		}
		return $this->runMultipleRow();
	}

	function addPrefixInGameProviderAuth($playerId, $platformCode, $prefix) {
		$success=true;
		$this->db->from('game_provider_auth')->where('player_id', $playerId)
			->where('game_provider_id', $platformCode);

		$row=$this->runOneRowArray();
		if(!empty($row) && strpos($row['login_name'], $prefix)!==0){
			$login_name=$prefix.$row['login_name'];
			$this->db->set('login_name', $login_name)
				->where('id', $row['id']);
			$success=$this->runAnyUpdate($this->tableName);
		}

		return $success;
	}

	public function getPlayerMapFromLoginNameArr($platformCode, $playerNameArr){
		$map=[];
		$this->db->select('player_id, login_name')->from('game_provider_auth')->where('game_provider_id', $platformCode)
		    ->where_in('login_name', $playerNameArr);

		$rows=$this->runMultipleRowArray();

		// $this->utils->printLastSQL();
		if(!empty($rows)){
			// $this->utils->debug_log('getPlayerMapFromLoginNameArr', $rows);
			foreach ($rows as $row) {
				$map[$row['login_name']]=$row['player_id'];
			}
		}

		return $map;
	}

	public function getPlayerListByPlatformCode($platformCode){
		$this->db->select('player.username')->from('game_provider_auth')
			->join('player', 'player.playerId=game_provider_auth.player_id');
		$this->db->where('game_provider_id', $platformCode);
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * update t1Lottery additional info
	 * @param  int $playerId
	 * @param  string $agent_tracking_code
	 * @param  string $agent_tracking_source_code
	 * @return bool $success
	 */
	public function updateT1LotteryAdditionalInfo($playerId, $agent_tracking_code, $agent_tracking_source_code){

		$success=true;

		$gamePlatformId=T1LOTTERY_API;
		//try load api
		$api=$this->utils->loadExternalSystemLibObject($gamePlatformId);
		if($api){
			$this->load->model(['player_model','agency_model']);
			$playerInfo=$this->player_model->getPlayerArrayById($playerId);
			//exists
			$gameUsername=$api->convertUsernameToGame($playerInfo['username']);
			$encryptedPassword=$playerInfo['password'];
			//create game provider auth first
			$success=$this->safeSaveCreateGameAuth($gameUsername, $playerId, $encryptedPassword, $gamePlatformId);

			if($success){

		        $additionalInfo = $this->agency_model->getT1LotteryAdditionalInfo($agent_tracking_code, $agent_tracking_source_code);
		        if(!empty($additionalInfo)){
		        	//update
		        	$this->db->set('additional', json_encode($additionalInfo))->where('game_provider_id', $gamePlatformId)
		        		->where('player_id', $playerId);
		        	$success=$this->runAnyUpdate('game_provider_auth');
		        }

			}

		}

		return $success;
	}

	/**
	 * get additional info
	 * @param  int $playerId
	 * @param  int $gamePlatformId
	 * @return array null or json array
	 */
    public function getAdditionalInfo($playerId, $gamePlatformId){

    	$this->db->select('additional')->from('game_provider_auth')->where('player_id', $playerId)->where('game_provider_id', $gamePlatformId);
    	$val=$this->runOneRowOneField('additional');
    	if(!empty($val)){
    		$val=$this->utils->decodeJson($val);
    	}else{
    		$val=null;
    	}
    	return $val;

    }

    public function batchGetPlayerIdByGameUsernames(array $batchGameUsername){
    	$usernamePlayerIdMap=[];
		$keyStr='"'.implode('", "', $batchGameUsername ).'"';

		$sql=<<<EOD
select player_id, concat(game_provider_id, "-", login_name) as auth_key
from game_provider_auth
where concat(game_provider_id, "-", login_name) in ({$keyStr})
EOD;

		// $this->utils->debug_log($sql);

		$rows=$this->runRawSelectSQLArrayUnbuffered($sql);
		if(!empty($rows)){
    		foreach ($rows as $row) {
    			$usernamePlayerIdMap[$row['auth_key']]=$row['player_id'];
    		}
		}
    	return $usernamePlayerIdMap;

    }

    public function addGameAdditionalInfo($playerName, $addis, $gameId){
    	$success = true;
    	$this->db->set('additional', $addis)->from($this->tableName)->where('game_provider_id ', $gameId)->where('login_name ', $playerName);
    	$success=$this->runAnyUpdate('game_provider_auth');
    	return $success;
    }

    public function getPlayerGamePlatform($playerId){
    	$this->db->select('game_provider_id as id, status');
		$this->db->from($this->tableName);
		$this->db->where('player_id', $playerId);
		$query = $this->db->get();
		return $query->result_array();
    }

    public function updateEmptyPassword($playerId, $plainPassword){

    	if(!empty($playerId) && !empty($plainPassword)){

	    	$sql=<<<EOD

update game_provider_auth
set password=?
where player_id=? and password=''

EOD;

			return $this->runRawUpdateInsertSQL($sql, [$plainPassword, $playerId]);

    	}

    	return false;

    }

	public function syncGameAccountForAgency($agentId, $playerId, $username, $password, $gamePlatformId){
		$success=true;
		$this->load->model(['agency_model']);
		$sma_id = $this->agency_model->getRootAgencyByAgentId($agentId);
		$success=false;
		// get available game list
        $playerPrefix=$this->agency_model->getPlayerPrefixByAgentId($agentId);
		// $prefixMap=$this->agency_model->getPrefixMapForGameAccount($agentId);
		// foreach ($prefixMap as $gamePlatformId => $apiInfo) {
			$data=[
				'id'=>$playerId,
				'username'=>$username,
				'password'=>$password,
				'source' => self::SOURCE_REGISTER,
				'is_demo_flag' => self::DB_FALSE,
				'agent_id' => $agentId,
				'sma_id' => $sma_id,
			];
			$extra=[
				// 'prefix'=>$apiInfo['prefix'],
				'prefix'=>$playerPrefix,
			];
			$success=$this->savePasswordForPlayer($data, $gamePlatformId, $extra);
			// if(!$success){
			// 	$this->utils->error_log('savePasswordForPlayer failed', $data, $gamePlatformId, $extra);
			// 	break;
			// }
		// }

		return $success;
	}

	public function getMultipleGameUsernameBy($playerUsername, array $apis, $db=null){
		if(empty($db)){
			$db=$this->db;
		}
		if(empty($playerUsername) || empty($apis)){
			return null;
		}
		$db->select('playerId')->from('player')->where('username', $playerUsername);
		$playerId=$this->runOneRowOneField('playerId', $db);
		if(empty($playerId)){
			return null;
		}

		$db->select('login_name, game_provider_id')
		  ->from('game_provider_auth')->where_in('game_provider_id', $apis)
		  ->where('player_id', $playerId);
		return $this->runMultipleRowArray($db);

	}

	function getUsernameByGameUsername($gameUsername, $gamePlatformId) {
		if (!empty($gameUsername) && !empty($gamePlatformId)) {
			$this->db->select('player.username')
				->from('game_provider_auth')
				->join('player', 'game_provider_auth.player_id=player.playerId')
				->where('game_provider_auth.login_name', strtolower($gameUsername))
				->where('game_provider_auth.game_provider_id', $gamePlatformId);
			return $this->runOneRowOneField('username');
		}
		return null;
	}

	public function getGameAccountById($id) {
		$this->db->select('game_provider_auth.game_provider_id, game_provider_auth.login_name, game_provider_auth.player_id, player.username, game_provider_auth.password');
		$this->db->from('game_provider_auth');
		$this->db->join('player', 'game_provider_auth.player_id=player.playerId');
		$this->db->where('game_provider_auth.id', $id);
		$query = $this->db->get();
		return $query->result_array();
	}
}

/////end of file///////