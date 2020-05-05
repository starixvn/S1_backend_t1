<?php
require_once dirname(__FILE__) . '/base_model.php';

require_once dirname(__FILE__) . '/modules/cashback_model_module.php';

/**
 * General behaviors include :
 * * get group level for a certain player
 * * adding vip group record
 * * create vip cashback game rule
 * * get all available games
 * * get VIP setting game provider
 * * create VIP cashback bonus per each game
 * * get VIP setting list to export
 * * add level to a certain player
 * * adjust level for a certain player
 * * write player level history for a certain level
 * * sync player level history by batch
 * * sync player level history
 * * add player level history game details
 * * get game allowed game level rules
 * * update all vip group bonus per each game
 * * delete cashback allowed game type of a certain cashback rule setting
 * * get vip group level details
 *
 * VIP group includes many levels
 *
 * VIP level has settings: cashback, instant deposit bonus
 *
 * vipsetting is group
 *
 * vipsettingcashbackrule is level
 *
 * playerlevel is player with level
 *
 * vipsetting.vipSettingId=vipsettingcashbackrule.vipSettingId
 * vipsettingcashbackrule.vipsettingcashbackruleId=playerlevel.playerGroupId
 * playerlevel.playerId=player.playerId
 *
 * MOVED playerLevel to player.levelId , levelName and groupName
 *
 *
 * @category Player
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Group_level extends BaseModel {

	const DEPOSIT_FIRST = 1;
	const DEPOSIT_SUCCEEDING = 2;
	const BONUS_MODE_ENABLE = 1;
	const BONUS_MODE_DISABLE = 0;
	const BONUS_TYPE_FIXAMOUNT = 1;
	const BONUS_TYPE_BYPERCENTAGE = 2;

	const UPGRADE_SETTING_ENABLE = 1;
	const UPGRADE_SETTING_DISABLE = 2;

	const UPGRADE_ONLY = 1;
	const UPGRADE_DOWNGRADE = 2;
	const DOWNGRADE_ONLY = 3;

	const RECORD_UPGRADE = 1;
	const RECORD_DOWNGRADE = 2;
	const RECORD_SPECIFICGRADE = 3;

	const GRADE_FAILED = 0;
	const GRADE_SUCCESS = 1;

	const REQUEST_TYPE_AUTO_GRADE = 1;
	const REQUEST_TYPE_MANUAL_GRADE = 2;
	const REQUEST_TYPE_SPECIFIC_GRADE = 3;

	// cashback types
	const NORMAL_CASHBACK = 1;
	const FRIEND_REFERRAL_CASHBACK = 2;

	//for cashback_request
	const CASHBACK_STATE_REQUEST = 1;
	const CASHBACK_STATE_APPROVED = 2;
	const CASHBACK_STATE_PAID = 3;
	const CASHBACK_STATE_DECLINED = 4;

	const CASHBACK_PERIOD_SETTING_DAILY = 1;
	const CASHBACK_PERIOD_SETTING_WEEKLY = 2;

	const CASHBACK_SETTINGS_NAME = 'cashback_settings';
	const LAST_UPDATE_CASHBACK_NAME= 'last_batch_update_cashback_time';


	const SYSTEM_FEATURE_CASHBACK= 'disable_auto_add_cash_back';

	protected $groupTable = 'vipsetting';
	protected $groupId = 'vipSettingId';

	protected $levelTable = 'vipsettingcashbackrule';
	protected $levelId = 'vipsettingcashbackruleId';
	protected $levelName = 'vipLevelName';
	protected $levelGroupId = 'vipSettingId';

	protected $playerTable = 'player';
	protected $playerLevelId = 'levelId';
	protected $playerLevelName = 'levelName';

	//move to playerLevel to player.levelId and levelName
	protected $playerlevelTable = 'playerlevel';
	protected $plLevelId = 'playerGroupId';

	//@deprecated move to group_level_cashback_percentage_history
	protected $levelGamePlatformTable = 'vipsettingcashbackbonuspergame';
	protected $levelGamePlatformId = 'gameType';
	protected $levelGamePlatformLevelId = 'vipsettingcashbackruleId';

	//@deprecated move to group_level_cashback_percentage_history
	protected $levelAllowedGameTable = 'vipsetting_cashback_game';
	protected $levelAllowedGameLevelId = 'vipsetting_cashbackrule_id';
	//@deprecated move to group_level_cashback_percentage_history
	protected $playerLevelHistoryTable = 'player_level_history';
	protected $plHistoryGameDetailsTable = 'player_level_history_game_api_details';
	protected $plHistoryAllowedGamesTable = 'player_level_history_allowed_games';

	protected $table_agency_game_rolling_comm = 'agency_game_rolling_comm';

	private $grade_record = [];

	const DEFAULT_GAME_PLATFORM = PT_API;

	use cashback_model_module;

	function __construct() {
		parent::__construct();
	}

	/**
	 * detail: get group level for a certain player
	 *
	 * @param int $playerId player playerId
	 * @return array
	 */
	public function getPlayerGroupLevel($playerId) {
		$this->db->select('levelId')->from('player');
		$this->db->where('playerId', $playerId);

		$query = $this->db->get();
		$rows = $this->getMultipleRow($query);
		return $this->convertRowsToArray($rows, 'levelId');

	}

	/**
	 * detail: adding vip group record
	 *
	 * @param array $data
	 * @retrurn Boolean
	 */
	public function addVIPGroup($data) {
		$this->db->insert($this->groupTable, $data);

		$vipdata['vipsettingId'] = $this->db->insert_id();

		//echo 'this id: '.$id;exit();
		for ($i = 1; $i < $data['groupLevelCount'] + 1; $i++) {
			$vipdata['minDeposit'] = 100 * $i;
			$vipdata['maxDeposit'] = 1000 * $i;
			$vipdata['dailyMaxWithdrawal'] = 10000 * $i;
			$vipdata['vipLevel'] = $i;
			$vipdata['vipLevelName'] = 'Level Name ' . $i;

			$this->createVipCashbackGameRule($vipdata);
		}
	}

	/**
	 * detail: create vip cashback game rule
	 *
	 * @param array $data
	 * @return Boolean
	 */
	public function createVipCashbackGameRule($data) {
		$this->db->insert($this->levelTable, $data);
		// $lastInsertId = $this->db->insert_id();
		// $vipdata['vipsettingcashbackruleId'] = $lastInsertId;
		//$vipdata['percentage'] = 10;
		//$vipdata['maxBonus'] = 1000;
		// $vipdata['gameType'] = 1;
		// $this->createVipCashbackBonusPerGame($vipdata);

		// $availableGame = $this->getAllAvailableGame();
		// foreach ($availableGame as $key) {
		// 	$game_data['vipsetting_cashbackrule_id'] = $lastInsertId;
		// 	$game_data['game_description_id'] = $key['id'];
		// 	$this->addCashbackAllowedGame($game_data);
		// }
	}

	/**
	 * detail: get all available games
	 *
	 * @return array
	 */
	// public function getAllAvailableGame() {
	// 	$this->db->select('id')->from('game_description');
	// 	$query = $this->db->get();
	// 	return $query->result_array();
	// }

	/**
	 * detail: add Cashback allowed game
	 *
	 * @param array $game_data
	 * @return Boolean
	 */
	public function addCashbackAllowedGame($game_data) {
		// 	$this->db->insert('vipsetting_cashback_game', $game_data);
	}

	/**
	 * detail: get VIP setting allowed game
	 *
	 * @param int $vipsettingcashbackruleId vipsettingcashbackrule vipsettingcashbackruleId
	 * @return array
	 */
	public function getVipSettingAllowedGame($vipsettingcashbackruleId) {
		// $this->db->select('vipsetting_cashback_game.game_description_id')->from('vipsetting_cashback_game');
		// $this->db->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = vipsetting_cashback_game.vipsetting_cashbackrule_id', 'left');
		// $this->db->where('vipsettingcashbackrule.vipsettingcashbackruleId', $vipsettingcashbackruleId);

		// $query = $this->db->get();
		// if ($query->num_rows() > 0) {
		// 	foreach ($query->result_array() as $row) {
		// 		$data[] = $row['game_description_id'];
		// 	}
		// 	return $data;
		// }
		// return array();
	}

	/**
	 * detail: get VIP setting game provider
	 *
	 * @param int $vipsettingcashbackruleId vipsetting_cashback_game vipsetting_cashbackrule_id
	 * @return array
	 */
	public function getVipSettingGamesProvider($vipsettingcashbackruleId) {
		// $this->db->select('game_description.game_platform_id')->from('vipsetting_cashback_game');
		// $this->db->join('game_description', 'game_description.id = vipsetting_cashback_game.game_description_id', 'left');
		// $this->db->where('vipsetting_cashback_game.vipsetting_cashbackrule_id', $vipsettingcashbackruleId);
		// $this->db->group_by('game_description.game_platform_id');
		// $query = $this->db->get();
		// if ($query->num_rows() > 0) {
		// 	foreach ($query->result_array() as $row) {
		// 		$data[] = $row['game_platform_id'];
		// 	}
		// 	return $data;
		// }
		// return array();
	}

	/**
	 * detail: get vip setting game typed
	 *
	 * @param int $vipsettingcashbackruleId vipsetting_cashback_game vipsetting_cashbackrule_id
	 * @return array
	 */
	public function getVipSettingsGamesType($vipsettingcashbackruleId) {
		// $this->db->select('game_description.game_type_id')->from('vipsetting_cashback_game');
		// $this->db->join('game_description', 'game_description.id = vipsetting_cashback_game.game_description_id', 'left');
		// $this->db->where('vipsetting_cashback_game.vipsetting_cashbackrule_id', $vipsettingcashbackruleId);
		// $this->db->group_by('game_description.game_type_id');
		// $query = $this->db->get();
		// if ($query->num_rows() > 0) {
		// 	foreach ($query->result_array() as $row) {
		// 		$data[] = $row['game_type_id'];
		// 	}
		// 	return $data;
		// }
		// return false;
	}

	/**
	 * detail: create VIP cashback bonus per each game
	 *
	 * @param array $data
	 * @return Boolean
	 */
	public function createVipCashbackBonusPerGame($data) {
		// $this->db->insert($this->levelGamePlatformTable, $data);
	}

	// public function editVIPGroup($data, $vipsettingId) {
	// 	$this->db->where('vipsettingId', $vipsettingId);
	// 	$this->db->update('vipsetting', $data);
	// 	//TODO write to player level history
	// }

	/**
	 * detail: get VIP Setting Lists
	 *
	 * @param string $sort
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return array or Boolean
	 */
	public function getVIPSettingList($sort, $limit, $offset) {
		if ($limit != null) {
			// $limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			// $offset = "OFFSET " . $offset;
		} else {
			$offset = null;
		}
		if ($offset == 'undefined') {
			$offset = null;
		}
		$this->db->select('vipsetting.*, admin1.username AS createdBy, admin2.username AS updatedBy', false)
			->from($this->groupTable)
			->join('adminusers as admin1', 'admin1.userId = vipsetting.createdBy', 'left')
			->join('adminusers AS admin2', 'admin2.userId = vipsetting.updatedBy', 'left')
			->order_by($this->groupTable . "." . $sort, 'asc')
			->where('vipsetting.deleted <> 1');
		if ($limit) {
			$this->db->limit($limit, $offset);
		}

		// $query = $this->db->query("SELECT vipsetting.*, admin1.username AS createdBy, admin2.username AS updatedBy
		// 	FROM vipsetting
		// 	LEFT JOIN adminusers AS admin1
		// 	ON admin1.userId = vipsetting.createdBy
		// 	LEFT JOIN adminusers AS admin2
		// 	ON admin2.userId = vipsetting.updatedBy
		// 	ORDER BY vipsetting." . $sort . " ASC
		// 	$limit
		// 	$offset
		// ");

		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				//$row['createdOn'] = mdate('%M %d, %Y - %h:%i:%s %A', strtotime($row['createdOn']));
				/*$row['createdOn'] = mdate('%d-%M-%Y - %h:%i:%s %A', strtotime($row['createdOn']));
				if ($row['updatedOn'] != null) {
					$row['updatedOn'] = mdate('%M %d, %Y - %h:%i:%s %A', strtotime($row['updatedOn']));
				}*/
				$data[] = $row;
			}
			return $data;
		}
		return false;

	}

	/**
	 * detail: get VIP setting list to export
	 *
	 * @return array
	 */
	public function getVIPSettingListToExport() {
		$this->db->select('vipsetting.groupName,
						   vipsetting.groupLevelCount,
						   vipsetting.groupDescription,
						   vipsetting.createdOn,
						   adminusers.userName as createdBy,
						   vipsetting.updatedOn,
						   vipsetting.status,
						   adminusers.userName as updatedBy
							', false)->from('vipsetting')
			->join('adminusers', 'adminusers.userId = vipsetting.createdBy')
			->where('vipsetting.deleted <> 1');

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->result();
		}
		return false;
	}

	/**
	 * detail: get VIP Group name
	 *
	 * @param string $group_name
	 * @return array
	 */
	public function getVipGroupName($group_name) {

		$this->db->from($this->groupTable)->where('groupName', $group_name);
		$query = $this->db->get();
		// $query = $this->db->query("SELECT * FROM vipsetting WHERE groupName = '" . $group_name . "'");

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	/**
	 * detail: get level name of a certain level
	 *
	 * @param int $levelId vipsettingcashbackrule level id
	 *
	 * @return string or Boolean
	 */
	public function getLevelName($levelId) {
		$this->db->select($this->levelName);
		$this->db->where($this->levelId, $levelId);
		$query = $this->db->get($this->levelTable);
		$row = $query->row_array();
		return $row ? $row[$this->levelName] : false;
	}

	/**
	 * detail: add level to a certain player
	 *
	 * @param int $playerId
	 * @param int $levelId
	 * @return int
	 */
	public function addPlayerLevel($playerId, $levelId = null) {

		$levelId = empty($this->utils->getConfig('default_level_id'))? 1 : $this->utils->getConfig('default_level_id');
		$playerLevelId = $this->insertData($this->playerlevelTable, array(
			'playerId' => $playerId,
			'playerGroupId' => $levelId,
		));

		// $this->writePlayerLevelHistory($playerId, $levelId, null); # write to history
		$this->updatePlayerTable($playerId, $levelId);

		return $playerLevelId;
	}

	/**
	 * detail: adjust level for a certain player
	 *
	 * @param int $playerId
	 * @param int $newLevelId
	 * @return Boolean
	 */
	public function adjustPlayerLevel($playerId, $newLevelId) {
		if ($newLevelId <= 0) {
			throw new Exception("newLevelId({$newLevelId}) is invalid");
		}

		$this->db->from($this->playerlevelTable)->where('playerId', $playerId);
		if ($this->runExistsResult()) {
			$this->db->where('playerId', $playerId);
			$this->db->update($this->playerlevelTable, array('playerGroupId' => $newLevelId));
		} else {
			$data = array('playerId' => $playerId, 'playerGroupId' => $newLevelId);
			$this->db->insert($this->playerlevelTable, $data);
		}

		return $this->updatePlayerTable($playerId, $newLevelId);
	}

	/**
	 * detail: adjust level for batch players
	 *
	 * @param int $playerId
	 * @param int $newLevelId
	 * @return Boolean
	 */
	public function batchAdjustPlayerLevel($playerId, $newLevelId) {

		$result = array();

		$this->db->from($this->playerlevelTable)->where('playerId', $playerId);
		$this->db->where('playerId', $playerId);
		$this->db->update($this->playerlevelTable, array('playerGroupId' => $newLevelId));

		$result['updatePlayerlevel'] = true;
		if ($this->db->_error_message()) {
			$result['updatePlayerlevel'] = false;
		}

		$result['updatePlayer'] = false;
		if ($this->updatePlayerTable($playerId, $newLevelId)) {
			$result['updatePlayer'] = true;
		}

		return $result;

	}

	/**
	 * detail: get level id for a certain player
	 *
	 * @param string $playerId
	 * @return int or string
	 */
	public function getPlayerLevelId($playerId) {
		$this->load->model(array('player_model'));
		$player = $this->player_model->getPlayerById($playerId);

		if ($player) {
			return $player->levelId;
		}
		return null;
	}

	/**
	 * detail: MOVED TO adjustPlayerLevel
	 *
	 * @param int $playerId
	 * @param array $data
	 * @return Boolean
	 */
	public function changePlayerLevel($playerId, $data) {
		$newLevelId = $data[$this->plLevelId];
		return $this->adjustPlayerLevel($playerId, $newLevelId);

		// $newLevelId = $data[$this->plLevelId];
		// $oldLevelId = $this->getPlayerGroupLevel($playerId)[0]; # TODO: REMOVE INDEX
		// $this->db->update($this->playerlevelTable, $data, ['playerId' => $playerId]);
		// $this->writePlayerLevelHistory($playerId, $newLevelId, $oldLevelId);
		// $this->updatePlayerTable($playerId, $newLevelId);
	}

	// public function getAllPlayersByLevelId($levelId) {
	// 	$this->db->select('playerId,levelId')->from('player')->where('levelId', $levelId);
	// 	$rows = $this->runMultipleRow();
	// 	return $rows;
	// }

	/**
	 * detail: write player level history for a certain level
	 *
	 * @deprecated move to group_level_cashback_percentage_history
	 *
	 * @param int $levelId
	 * @return array
	 */
	// public function writePlayerLevelHistoryByLevelChanged($levelId) {
	// $this->utils->debug_log('writePlayerLevelHistoryByLevelChanged', 'levelId', $levelId);
	// //get all players for this level
	// $this->db->select('playerId,levelId')->from('player')->where('levelId', $levelId);
	// $players = $this->runMultipleRow();
	// // $players = $this->getAllPlayersByLevelId($levelId);
	// //write history for each players
	// if (!empty($players)) {
	// 	$batchPlayers = array_chunk($players, self::SPLIT_NUMBER);
	// 	$rule = $this->getCashbackRule($levelId);
	// 	$cashback_percentage = $rule->cashback_percentage;
	// 	$cashback_maxbonus = $rule->cashback_maxbonus;

	// 	$this->utils->debug_log('write player', count($players), 'batch', count($batchPlayers));
	// 	foreach ($batchPlayers as $oneBatchPlayers) {
	// 		$this->batchSyncPlayerLevelHistory($oneBatchPlayers, $levelId, $cashback_percentage, $cashback_maxbonus);
	// 		// $this->writePlayerLevelHistory($player->playerId, $levelId, $player->levelId);
	// 	}
	// }

	// }

	/**
	 * detail: get id from the given array
	 *
	 * @param array $oneBatchPlayers
	 * @return array
	 */
	protected function getIdArr($oneBatchPlayers) {
		$rlt = array();
		if (!empty($oneBatchPlayers)) {
			foreach ($oneBatchPlayers as $player) {
				$rlt[] = $player->playerId;
			}
		}
		return $rlt;
	}

	/**
	 * detail: sync player level history by batch
	 *
	 * @deprecated move to group_level_cashback_percentage_history
	 *
	 * @param array $oneBatchplayers
	 * @param int $levelId
	 * @return array
	 */
	// public function batchSyncPlayerLevelHistory($oneBatchPlayers, $levelId,
	// 	$cashback_percentage, $cashback_maxbonus) {

	// if (empty($oneBatchPlayers)) {
	// 	return null;
	// }
	// // $this->utils->debug_log('playerId', $playerId, 'levelId', $levelId, 'oldLevelId', $oldLevelId, $cashback_percentage, $cashback_maxbonus);
	// $this->utils->markProfilerStart('sync_player_level_history');
	// $this->utils->debug_log('search history', count($oneBatchPlayers), 'levelId', $levelId);

	// $historyDate = $this->utils->getTodayForMysql();

	// $this->db->select('id,player_id')->from($this->playerLevelHistoryTable)
	// 	->where('history_date', $historyDate);

	// if (count($oneBatchPlayers) == 1) {
	// 	$this->db->where('player_id', $oneBatchPlayers[0]->playerId);
	// } else {
	// 	$this->db->where_in('player_id', $this->getIdArr($oneBatchPlayers));
	// }

	// $rows = $this->runMultipleRow();
	// $updatePlayerIds = array();
	// // $insertPlayers = $oneBatchPlayers;
	// $updateData = array();
	// if (!empty($rows)) {
	// 	foreach ($rows as $row) {
	// 		$updatePlayerIds[] = $row->player_id;
	// 		$updateData[] = array(
	// 			'id' => $row->id,
	// 			'cashback_percentage' => $cashback_percentage,
	// 			'cashback_maxbonus' => $cashback_maxbonus,
	// 			'updated_at' => $this->utils->getNowForMysql(),
	// 		);
	// 	}
	// }
	// //update
	// if (!empty($updateData)) {
	// 	$this->db->update_batch($this->playerLevelHistoryTable, $updateData, 'id');
	// 	$this->utils->debug_log('update rows', $this->db->affected_rows(), 'updateData', count($updateData));
	// }
	// $insertData = array();
	// foreach ($oneBatchPlayers as $onePlayer) {
	// 	if (!in_array($onePlayer->playerId, $updatePlayerIds)) {
	// 		$insertData[] = array(
	// 			'player_id' => $onePlayer->playerId,
	// 			'level_id' => $levelId,
	// 			'old_level_id' => $onePlayer->levelId,
	// 			'created_at' => $this->utils->getNowForMysql(),
	// 			'status' => self::STATUS_NORMAL,
	// 			'history_date' => $this->utils->getTodayForMysql(),
	// 			'cashback_percentage' => $cashback_percentage,
	// 			'cashback_maxbonus' => $cashback_maxbonus,
	// 			'updated_at' => $this->utils->getNowForMysql(),
	// 		);
	// 	}
	// }
	// //insert
	// if (!empty($insertData)) {
	// 	$this->db->insert_batch($this->playerLevelHistoryTable, $insertData);
	// 	$this->utils->debug_log('insert rows', $this->db->affected_rows(), 'insertData', count($insertData));
	// }
	// $this->utils->markProfilerEndAndPrint('sync_player_level_history');

	// $historyId = $this->runOneRowOneField('id');
	// if ($historyId) {
	// 	//update this date
	// 	$this->db->set('cashback_percentage', $cashback_percentage)
	// 		->set('cashback_maxbonus', $cashback_maxbonus)
	// 		->set('updated_at', $this->utils->getNowForMysql())
	// 		->where('id', $historyId);
	// 	$this->db->update($this->playerLevelHistoryTable);
	// } else {
	// 	$data = array('player_id' => $playerId, 'level_id' => $levelId, 'old_level_id' => $oldLevelId,
	// 		'created_at' => $this->utils->getNowForMysql(), 'status' => self::STATUS_NORMAL,
	// 		'history_date' => $this->utils->getTodayForMysql(), 'cashback_percentage' => $cashback_percentage,
	// 		'cashback_maxbonus' => $cashback_maxbonus, 'updated_at' => $this->utils->getNowForMysql());
	// 	$this->db->insert($this->playerLevelHistoryTable, $data);
	// 	$historyId = $this->db->insert_id();
	// }
	// return $historyId;
	// }

	/**
	 * detail: sync player level history
	 *
	 * @deprecated move to group_level_cashback_percentage_history
	 *
	 * @param int $playerId
	 * @param int $levelId
	 * @param int $oldLevelId
	 * @param string $cashback_percentage
	 * @param string $cashback_maxbonus
	 *
	 * @return int
	 */
	// public function syncPlayerLevelHistory($playerId, $levelId, $oldLevelId, $cashback_percentage, $cashback_maxbonus) {

	// $this->utils->debug_log('playerId', $playerId, 'levelId', $levelId, 'oldLevelId', $oldLevelId, $cashback_percentage, $cashback_maxbonus);

	// $historyDate = $this->utils->getTodayForMysql();

	// $this->db->select('id')->from($this->playerLevelHistoryTable)
	// 	->where('history_date', $historyDate)
	// 	->where('player_id', $playerId);
	// $historyId = $this->runOneRowOneField('id');
	// if ($historyId) {
	// 	//update this date
	// 	$this->db->set('cashback_percentage', $cashback_percentage)
	// 		->set('cashback_maxbonus', $cashback_maxbonus)
	// 		->set('updated_at', $this->utils->getNowForMysql())
	// 		->set('level_id', $levelId)
	// 		->where('id', $historyId);
	// 	$this->db->update($this->playerLevelHistoryTable);
	// } else {
	// 	$data = array('player_id' => $playerId, 'level_id' => $levelId, 'old_level_id' => $oldLevelId,
	// 		'created_at' => $this->utils->getNowForMysql(), 'status' => self::STATUS_NORMAL,
	// 		'history_date' => $this->utils->getTodayForMysql(), 'cashback_percentage' => $cashback_percentage,
	// 		'cashback_maxbonus' => $cashback_maxbonus, 'updated_at' => $this->utils->getNowForMysql());
	// 	$this->db->insert($this->playerLevelHistoryTable, $data);
	// 	$historyId = $this->db->insert_id();
	// }
	// return $historyId;
	// }

	/**
	 * detail: get cash back rule
	 *
	 * @param int $levelId
	 * @return array
	 */
	public function getCashbackRule($levelId) {
		$this->db->from($this->levelTable)->where($this->levelId, $levelId);

		return $this->runOneRow();
	}

	/**
	 * detail: write player level history
	 *
	 * @deprecated move to group_level_cashback_percentage_history
	 *
	 * @param int $playerId
	 * @param int $levelId
	 * @param int $oldLevelId
	 *
	 * @return int or string
	 */
	// public function writePlayerLevelHistory($playerId, $levelId, $oldLevelId) {
	// $this->utils->debug_log('playerId', $playerId, 'levelId', $levelId, 'oldLevelId', $oldLevelId);

	// $rule = $this->getCashbackRule($levelId);
	// if ($playerId && $levelId && $rule) {
	// 	//load from old level id
	// 	$cashback_percentage = $rule->cashback_percentage;
	// 	$cashback_maxbonus = $rule->cashback_maxbonus;
	// 	//copy vipsettingcashbackrule, vipsettingcashbackbonuspergame, vipsetting_cashback_game
	// 	//to player_level_history, player_level_history_game_api_details, player_level_history_allowed_games
	// 	$historyId = $this->syncPlayerLevelHistory($playerId, $levelId, $oldLevelId, $cashback_percentage, $cashback_maxbonus);

	// 	// $this->addPlayerLevelHistoryGameDetails($historyId, $levelId, $cashback_percentage, $cashback_maxbonus);
	// 	// $this->addPlayerLevelHistoryAllowedGame($historyId, $levelId, $cashback_percentage, $cashback_maxbonus);
	// 	return $historyId;
	// }
	// return null;
	// }

	/**
	 * detail: add player level history game details
	 *
	 * @deprecated move to group_level_cashback_percentage_history
	 *
	 * @param int $historyId
	 * @param int $levelId
	 * @param string $cashback_percentage
	 * @param string $cashback_maxbonus
	 *
	 * @return Boolean
	 */
	// public function addPlayerLevelHistoryGameDetails($historyId, $levelId, $cashback_percentage, $cashback_maxbonus) {
	// $ids = array();
	// $rows = $this->getGamePlatformLevelRules($levelId);
	// if (!empty($rows)) {

	// 	$this->utils->debug_log('delete', $this->plHistoryGameDetailsTable, $historyId);
	// 	//delete first
	// 	$this->db->delete($this->plHistoryGameDetailsTable, array('history_id' => $historyId));

	// 	$this->utils->debug_log('add', count($rows));

	// 	$data = array();
	// 	foreach ($rows as $row) {
	// 		$data[] = array('game_platform_id' => $row->gameType, 'history_id' => $historyId,
	// 			'max_bonus' => $cashback_maxbonus, 'percentage' => $cashback_percentage,
	// 			'status' => self::STATUS_NORMAL);

	// 		// $this->db->insert($this->plHistoryGameDetailsTable, $data);

	// 		// $ids[] = $this->db->insert_id();
	// 	}

	// 	$this->db->insert_batch($this->plHistoryGameDetailsTable, $data);
	// }

	// return $ids;
	// }

	/**
	 * detail: get game platform level rules
	 *
	 * @param int $levelId
	 * @return array
	 */
	// public function getGamePlatformLevelRules($levelId) {
	// 	//levelId=vipsettingcashbackruleId

	// 	$this->db->from($this->levelGamePlatformTable)
	// 		->where($this->levelGamePlatformLevelId, $levelId);
	// 	return $this->runMultipleRow();
	// }

	// public function addPlayerLevelHistoryAllowedGame($historyId, $levelId, $cashback_percentage, $cashback_maxbonus) {
	// 	// $ids = array();
	// 	$rows = $this->getAllowedGameLevelRules($levelId);
	// 	if (!empty($rows)) {

	// 		$this->utils->debug_log('delete', $this->plHistoryGameDetailsTable, $historyId);
	// 		//delete first
	// 		$this->db->delete($this->plHistoryAllowedGamesTable, array('history_id' => $historyId));

	// 		$this->utils->debug_log('add', count($rows));

	// 		$data = array();
	// 		foreach ($rows as $row) {
	// 			$data[] = array('game_description_id' => $row->game_description_id, 'history_id' => $historyId,
	// 				'max_bonus' => $row->maxBonus, 'percentage' => $row->percentage,
	// 				'status' => self::STATUS_NORMAL);

	// 			// $this->db->insert($this->plHistoryAllowedGamesTable, $data);

	// 			// $ids[] = $this->db->insert_id();
	// 		}

	// 		$this->db->insert_batch($this->plHistoryAllowedGamesTable, $data);
	// 	}

	// 	// return $ids;
	// }

	/**
	 * detail: get game allowed game level rules
	 *
	 * @param int $levelId
	 * @return array
	 */
	public function getAllowedGameLevelRules($levelId) {
		//levelId=levelAllowedGameTable

		$this->db->from('group_level_cashback_game_description')
			->where('vipsetting_cashbackrule_id', $levelId);

		// $this->db->from($this->levelAllowedGameTable)
		// 	->where($this->levelAllowedGameLevelId, $levelId);
		return $this->runMultipleRow();
	}

	# OG-732 add levelId, levelName to player table
	# FOR PRIVATE USE ONLY

	/**
	 * detail: update player table record
	 *
	 * @param int $playerId player playerId
	 * @param int $levelId player levelId
	 *
	 * @return Boolean
	 */
	private function updatePlayerTable($playerId, $levelId) {

		$groupLevel = $this->getVipGroupLevelDetails($levelId);
		$groupName = $groupLevel['groupName'];
		$levelName = $groupLevel['vipLevelName'];

		$this->db->update('player', [
			'levelId' => $levelId,
			'groupName' => $groupName,
			'levelName' => $levelName,
		], [
			'playerId' => $playerId,
		]);

		if ($this->db->_error_message()) {
			return false;
		}

		return true;
	}

	/**
	 * @param int $levelId
	 * @param string $bonusPercentage
	 * @param string $maxBonus
	 *
	 * @return array
	 */
	public function isChangedRule($levelId, $bonusPercentage, $maxBonus) {
		// $result = false;
		// $this->db->from($this->levelGamePlatformTable)->where('gameType', self::DEFAULT_GAME_PLATFORM);

		// $row = $this->runOneRow();
		// if ($row) {
		// 	$result = $bonusPercentage != $row->percentage || $maxBonus != $row->maxBonus;
		// }

		// return $result;
	}

	/**
	 * detail: update VIP group bonus rules
	 *
	 * @param array $data
	 * @return array
	 */
	public function editVipGroupBonusRule($data) {
		$flag = $this->editData('vipsettingcashbackrule',
			array('vipsettingcashbackruleId' => $data['vipsettingcashbackruleId']),
			$data);

		if ($flag) {
			$this->editData('player',
				array('levelId' => $data['vipsettingcashbackruleId']),
				array('levelName' => $data['vipLevelName']));
		}
	}

	/**
	 * detail: update all vip group bonus per each game
	 *
	 * @param int $levelId
	 * @param string $bonusPercentage
	 * @param string $maxBonus
	 *
	 * @return array
	 */
	public function updateAllVipGroupBonusPerGame($levelId, $bonusPercentage, $maxBonus) {
		//update all game platform
		// return $this->editData('vipsettingcashbackbonuspergame',
		// 	array($this->levelGamePlatformLevelId => $levelId),
		// 	array('percentage' => $bonusPercentage, 'maxBonus' => $maxBonus, 'allowStatus' => self::OLD_STATUS_ACTIVE));
	}

	/**
	 * detail: update VIP group bonus per game
	 *
	 * @parama array $data
	 * @return array
	 */
	public function editVipGroupBonusPerGame($data) {
		// return $this->editData('vipsettingcashbackbonuspergame',
		// 	array('vipsettingcashbackbonuspergameId' => $data['vipsettingcashbackbonuspergameId']),
		// 	$data);
	}

	/**
	 * detail: update VIP group
	 *
	 * @param array $data
	 * @param int $vipsettingId
	 *
	 * @return array
	 */
	public function editVIPGroup($data, $vipsettingId) {
		$flag = $this->editData('vipsetting',
			array('vipsettingId' => $vipsettingId),
			$data);

		if ($flag && isset($data['groupName'])) {
			$this->editData('player',
				array('levelId' => $vipsettingId),
				array('groupName' => $data['groupName']));
		}
	}

	/**
	 * detail: check if vip setting is exists
	 *
	 * @param int $vipsettingId
	 * @rerturn Boolean
	 */
	// public function isVipSettingIdExists($vipsettingId) {
	// $this->db->select('vipsetting_cashbackrule_id')->where('vipsetting_cashbackrule_id', $vipsettingId);
	// $query = $this->db->get('vipsetting_cashback_game');

	// if ($query->num_rows() > 0) {
	// 	return TRUE;
	// } else {
	// 	return FALSE;
	// }
	// }

	/**
	 * detail: delete cashback allowed game type of a certain cashback rule setting
	 *
	 * @param int $vipSettingCashbackruleId vipsetting_cashback_game vipsetting_cashbackrule_id
	 * @return Boolean
	 */
	// public function deleteCashbackAllowedGameType($vipSettingCashbackruleId) {
	// $this->db->where('vipsetting_cashbackrule_id', $vipSettingCashbackruleId);
	// $this->db->delete('vipsetting_cashback_game');
	// }

	/**
	 * detail: add cashback allowed game
	 *
	 * @param array $data
	 * @return Boolean
	 */
	public function addCashbackAllowedGames($data) {
		// 	return $this->insertData('vipsetting_cashback_game', $data);
	}

	/**
	 * detail: add cashback allowed games per batch
	 *
	 * @param int $vipsettingcashbackruleId
	 * @param string $cashback_percentage
	 * @param string $cashback_maxbonus
	 * @param string $gameAptList
	 *
	 * @return Boolean
	 */
	// public function batchAddCashbackAllowedGames($vipsettingcashbackruleId, $cashback_percentage, $cashback_maxbonus, $gamesAptList) {

	// 	$this->deleteCashbackAllowedGameType($vipsettingcashbackruleId);
	// 	$cnt = 0;
	// 	$data = array();
	// 	$this->utils->debug_log('gamesAptList count', count($gamesAptList));
	// 	foreach ($gamesAptList as $gameDescriptionId) {

	// 		$data[] = array(
	// 			'vipsetting_cashbackrule_id' => $vipsettingcashbackruleId,
	// 			'game_description_id' => $gameDescriptionId['id'],
	// 			'percentage' => $cashback_percentage,
	// 			'game_platform_percentage' => $gameDescriptionId['game_platform_number'],
	// 			'game_type_percentage' => $gameDescriptionId['game_type_number'],
	// 			'game_desc_percentage' => $gameDescriptionId['game_desc_number'],
	// 			'maxBonus' => $cashback_maxbonus,
	// 		);
	// 		if ($cnt >= 500) {
	// 			//insert and clean
	// 			$this->db->insert_batch($this->levelAllowedGameTable, $data);
	// 			$data = array();
	// 			$cnt = 0;
	// 		}
	// 		$cnt++;
	// 	}
	// 	if (!empty($data)) {
	// 		$this->db->insert_batch($this->levelAllowedGameTable, $data);
	// 	}
	// }

	/**
	 * detail: remove agaency game rolling
	 *
	 * @param int $agentId
	 * @return Boolean
	 */
	public function delete_agency_game_rolling_comm($agent_id) {
		$this->db->where('agent_id', $agent_id);
		$this->db->delete('agency_game_rolling_comm');
	}

	/**
	 * detail: add agency game rolling com by batch
	 *
	 * @param int $agent_id
	 * @param array $gamesAptList
	 *
	 * @return Boolean
	 */
	public function batch_add_agency_game_rolling_comm($agent_id, $gamesAptList) {

		$this->delete_agency_game_rolling_comm($agent_id);
		$cnt = 0;
		$data = array();
		$this->utils->debug_log('gamesAptList count', count($gamesAptList));
		foreach ($gamesAptList as $gameDescriptionId) {

			$data[] = array(
				'agent_id' => $agent_id,
				'game_description_id' => $gameDescriptionId['id'],
				'game_platform_percentage' => $gameDescriptionId['game_platform_number'],
				'game_type_percentage' => $gameDescriptionId['game_type_number'],
				'game_desc_percentage' => $gameDescriptionId['game_desc_number'],
			);
			if ($cnt >= 500) {
				//insert and clean
				$this->db->insert_batch($this->table_agency_game_rolling_comm, $data);
				$data = array();
				$cnt = 0;
			}
			$cnt++;
		}
		if (!empty($data)) {
			$this->db->insert_batch($this->table_agency_game_rolling_comm, $data);
		}
	}
	// add agency rolling comm settings }}}1

	// public function getAllPlayerGameLevelInfo() {
	// $this->db->select('vipsetting_cashback_game.game_description_id , ')
	// 	->from($this->levelAllowedGameTable)->join($this->);

	// }

	// public function getPlayerFirstRateMap() {
	// 	$this->db->select('player_id, min(concat(history_date," ", cashback_percentage," ",cashback_maxbonus )) as cashback_info', false)
	// 		->from($this->playerLevelHistoryTable)->group_by('player_id');
	// 	$rows = $this->runMultipleRow();
	// 	$data = array();
	// 	if (!empty($rows)) {
	// 		foreach ($rows as $row) {
	// 			$row->cashback_info;
	// 			$data[$row->player_id]=;
	// 		}
	// 	}
	// 	return $data;
	// }

	// public function getAllHistoryList() {

	// get betting total
	// $playerBetByDate = $this->getPlayerBetByDate();
	// //$this->utils->debug_log('getPlayerBetGroupByDate', $playerBetGroupByDate);

	// // init variables
	// $row = [];
	// // load models
	// $this->load->model(array('player_model'));
	// $this->load->model(array('transactions'));
	// $this->load->model(array('users'));
	// $this->load->model(array('player'));

	// // get superadmin
	// $superadmin = $this->users->getSuperAdmin();

	// if ($playerBetByDate) {
	// 	foreach ($playerBetByDate as $pbbd) {

	// 		// get first rate
	// 		$playerFirstRate = $this->getPlayerFirstRate($pbbd->player_id, $pbbd->game_description_id);

	// 		// set rate = first rate || 0 (dafault)
	// 		if ($playerFirstRate) {
	// 			$rate = $playerFirstRate[0]->percentage;
	// 			$max_bonus = $playerFirstRate[0]->max_bonus;
	// 		} else {
	// 			$rate = 0;
	// 			$max_bonus = 0;
	// 		}

	// 		// get daily rate
	// 		$playerDailyRate = $this->getPlayerDailyRate($pbbd->player_id, $pbbd->date, $pbbd->game_description_id);
	// 		//$this->utils->debug_log('defaultCashback', $cashback);

	// 		if ($playerDailyRate) {
	// 			$rate = $playerDailyRate[0]->percentage;
	// 			$max_bonus = $playerDailyRate[0]->max_bonus;
	// 		}

	// 		$pbbd->percentage = $rate;
	// 		$pbbd->cashback_amount = $pbbd->betting_total * ($rate / 100);
	// 		$pbbd->total_date = $pbbd->date;
	// 		$pbbd->max_bonus = $max_bonus;

	// 		if ($pbbd->cashback_amount > 0) {
	// 			$row[] = $pbbd;

	// 			// get before balance
	// 			$pbbd->from_id = $superadmin->userId;

	// 			$transact = $this->transactions->checkAutoCashbackTransaction($pbbd->player_id, $pbbd->total_date);
	// 			$curDate = date('Y-m-d') . ' 00:00:00';
	// 			if (!$transact && ($pbbd->total_date < $curDate)) {
	// 				$this->transactions->insertAutoCashbackTransaction($pbbd);
	// 			}
	// 		}

	// 	}
	// }

	// return $row;

	/*$this->db->select('plh.id as history_id, plh.player_id as player_id, plhag.game_de  scription_id as game_description_id, ' .
		' plhag.percentage as percentage, plhag.max_bonus as max_bonus, tpgd.betting_amount, ' .
		' tpgd.betting_amount*plhag.percentage/100 as cashback_amount, tpgd.date as total_date, gd.game_platform_id', false)
		->from($this->playerLevelHistoryTable . ' as plh')
		->join($this->plHistoryAllowedGamesTable . ' as plhag', 'plh.id=plhag.history_id')
		->join('total_player_game_day as tpgd', 'tpgd.player_id=plh.player_id and tpgd.game_description_id=plhag.game_description_id')
		->join('game_description as gd', 'gd.id=plhag.game_description_id', 'left')
		->where('plhag.status', self::STATUS_NORMAL)
		->where('plhag.percentage >', 0)
		->group_by('DATE_FORMAT(plh.created_at,"%Y-%m-%d")', false);
	*/

	// player_level_history as plh
	// player_level_history_allowed_games as plhag
	// total_player_game_day as tpgd
	// game_description as gd

// 		$sql = <<<EOD
	// SELECT plh.id as history_id, plh.player_id as player_id, plhag.game_description_id as game_description_id, plhag.percentage as percentage, plhag.max_bonus as max_bonus, gd.game_platform_id ,DATE_FORMAT(plh.created_at, "%Y-%m-%d") as total_date, sum(tpgd.betting_amount) as cashback_amount
	//         FROM (`player_level_history` as plh)
	//         JOIN `player_level_history_allowed_games` as plhag ON `plh`.`id`=`plhag`.`history_id`
	//         LEFT JOIN total_player_game_day as tpgd ON tpgd.player_id=plh.player_id
	//         LEFT JOIN `game_description` as gd ON `gd`.`id`=`plhag`.`game_description_id`
	//         WHERE `plhag`.`status` = 1 AND `plhag`.`percentage` > 0
	//         GROUP BY DATE_FORMAT(plh.created_at, "%Y-%m-%d")
	// EOD;

// 		$qry = $this->db->query($sql);
	// 		return $this->getMultipleRow($qry);
	// }

	/**
	 * detail: get first rate for a certain players
	 *
	 * @param int $player_id player_level_history player_id
	 * @param int $game_description_id
	 *
	 * @return array
	 */
// 	public function getPlayerFirstRate($player_id, $game_description_id) {
	// // 		$query = <<<EOD
	// 		// SELECT plh.history_date, plhag.percentage, plhag.max_bonus
	// 		//      FROM player_level_history as plh JOIN player_level_history_allowed_games as plhag ON plh.id = plhag.history_id
	// 		//      WHERE plh.player_id = $player_id
	// 		//      AND plhag.game_description_id = ?
	// 		//      ORDER BY plh.history_date ASC
	// 		//      LIMIT 1
	// 		// EOD;
	// 		//filter by vipsetting_cashback_game
	// 		if ($game_description_id) {
	// 			$sql = <<<EOD
	// SELECT plh.id, plh.history_date, plh.cashback_percentage, plh.cashback_maxbonus
	// FROM player_level_history as plh JOIN vipsetting_cashback_game ON plh.level_id = vipsetting_cashback_game.vipsetting_cashbackrule_id
	// WHERE plh.player_id = ?
	// AND vipsetting_cashback_game.game_description_id = ?
	// ORDER BY plh.history_date ASC
	// LIMIT 1
	// EOD;
	// 			$qry = $this->db->query($sql, array($player_id, $game_description_id));

// 			return $this->getOneRow($qry);

// 		} else {
	// 			$sql = <<<EOD
	// SELECT plh.id, plh.history_date, plh.cashback_percentage, plh.cashback_maxbonus
	// FROM player_level_history as plh
	// WHERE plh.player_id = ?
	// ORDER BY plh.history_date ASC
	// LIMIT 1
	// EOD;

// 			$qry = $this->db->query($sql, array($player_id));

// 			return $this->getOneRow($qry);
	// 		}

// 	}

	/**
	 * detail: get player daily rate
	 *
	 * @param int $player_id player_level_history player_id
	 * @param string $date
	 * @param int $game_description_id
	 *
	 * @return array
	 */
// 	public function getPlayerDailyRate($player_id, $date, $game_description_id) {
	// 		if ($game_description_id) {

// 			$sql = <<<EOD
	// SELECT plh.id, plh.history_date, plh.cashback_percentage, plh.cashback_maxbonus
	// FROM player_level_history as plh JOIN vipsetting_cashback_game ON plh.level_id = vipsetting_cashback_game.vipsetting_cashbackrule_id
	// WHERE plh.player_id = ?
	// AND plh.history_date <= ?
	// AND vipsetting_cashback_game.game_description_id = ?
	// ORDER BY plh.history_date DESC
	// LIMIT 1
	// EOD;

// 			$qry = $this->db->query($sql, array($player_id, $date, $game_description_id));

// 			return $this->getOneRow($qry);
	// 		} else {
	// 			$sql = <<<EOD
	// SELECT plh.id, plh.history_date, plh.cashback_percentage, plh.cashback_maxbonus
	// FROM player_level_history as plh
	// WHERE plh.player_id = ?
	// AND plh.history_date <= ?
	// ORDER BY plh.history_date DESC
	// LIMIT 1
	// EOD;

// 			$qry = $this->db->query($sql, array($player_id, $date));

// 			return $this->getOneRow($qry);

// 		}

// 		// if ($result->num_rows > 0) {
	// 		// 	return $result->result();
	// 		// } else {
	// 		// 	return 0;
	// 		// }
	// 	}

	/**
	 * detail: get information from a level history
	 *
	 * @param array $row
	 * @return array
	 */
	// public function getInfoFromLevelHistory($row) {
	// 	$rate = null;
	// 	$max_bonus = null;
	// 	$history_id = null;
	// 	if ($row) {
	// 		$rate = $row->cashback_percentage;
	// 		$max_bonus = $row->cashback_maxbonus;
	// 		$history_id = $row->id;
	// 	}
	// 	return array($rate, $max_bonus, $history_id);
	// }

	/**
	 * detail: set paid of a certain cashback
	 *
	 * @param int $id
	 * @return int
	 */
	public function setPaidFlag($id, $paid_amount) {
		//set paid_flag to true
		$this->db->where(['id' => $id])->update('total_cashback_player_game_daily', array(
			'paid_flag' => self::DB_TRUE,
			'paid_date' => $this->utils->getNowForMysql(),
			'paid_amount' => $paid_amount,
		));
		return $this->db->affected_rows() > 0;
	}

	/**
	 * @param int $palyerId
	 * @param string $totalDate
	 * @param string $paid_amount
	 *
	 * @return int
	 */
	public function setPaidFlagByPlayerAndDate($playerId, $totalDate, $paid_amount, $withdraw_condition_amount) {
		$this->db->where('player_id', $playerId)->where('total_date', $totalDate)
			->update('total_cashback_player_game_daily', array(
				'paid_flag' => self::DB_TRUE,
				'paid_date' => $this->utils->getNowForMysql(),
				'paid_amount' => $paid_amount,
				'withdraw_condition_amount' => $withdraw_condition_amount,
			));
		return $this->db->affected_rows() > 0;
	}

	/**
	 * detail: get max bonus for a certain player id
	 *
	 * @param int $playerId
	 * @return string
	 */
	public function getMaxbonusByPlayerId($playerId) {
		$this->db->select('cashback_maxbonus')->from('vipsettingcashbackrule')
			->join('player', 'vipsettingcashbackrule.vipsettingcashbackruleId=player.levelId')
			->where('player.playerId', $playerId);

		return $this->runOneRowOneField('cashback_maxbonus');
	}

	// public function lessThanMaxbonus($playerId, $currentAmount, $commonMaxBonus) {
	// 	//check max bonus, 0=limitless
	// 	$maxbonus = $this->getMaxbonusByPlayerId($playerId);

	// 	if (!empty($maxbonus)) {
	// 		//sum cashback from transactions
	// 		$this->load->model('transactions');
	// 		$cashback = $this->transactions->sumCashback($playerId);
	// 		$this->utils->debug_log('cashback', $cashback, 'currentAmount', $currentAmount, 'maxbonus', $maxbonus);
	// 		return $cashback + $currentAmount < $maxbonus;
	// 	}
	// 	return true;
	// }

	/**
	 * get from vip level
	 * @param  int $playerId
	 * @param  double $commonMaxBonus max bonus from common cashback rules
	 * @return double daily max bonus
	 */
	public function getDailyMaxbonusByPlayerId($playerId, $commonMaxBonus) {
		$this->db->select('cashback_daily_maxbonus')->from('vipsettingcashbackrule')
			->join('player', 'vipsettingcashbackrule.vipsettingcashbackruleId=player.levelId')
			->where('player.playerId', $playerId);

		$dailyMaxBonus= $this->runOneRowOneField('cashback_daily_maxbonus');

		if (empty($dailyMaxBonus)) {
			//try get it from common rules
			$dailyMaxBonus=$commonMaxBonus;
		}

		return $dailyMaxBonus;
	}

	// public function lessThanDailyMaxbonus($playerId, $currentAmount, $commonMaxBonus) {
	// 	//check max bonus, 0=limitless
	// 	$maxbonus = $this->getDailyMaxbonusByPlayerId($playerId);

	// 	if (empty($maxbonus)) {
	// 		//try get it from common rules
	// 		$maxbonus=$commonMaxBonus;
	// 	}

	// 	if (!empty($maxbonus)) {
	// 		//sum cashback from transactions
	// 		// $this->load->model('transactions');
	// 		// $cashback = $this->transactions->sumCashback($playerId);
	// 		$this->utils->debug_log('daily max', 'currentAmount', $currentAmount, 'maxbonus', $maxbonus);
	// 		return $currentAmount <= $maxbonus;
	// 	}
	// 	return true;
	// }

	public function processMaxBonus($playerId, $currentAmount, $commonMaxBonus) {
		$amount=$currentAmount;

		$dailyMaxBonus = $this->getDailyMaxbonusByPlayerId($playerId, $commonMaxBonus);

		if (!empty($dailyMaxBonus)) {
			$this->utils->debug_log('check daily max bons playerId', $playerId, 'amount', $amount, 'dailyMaxBonus', $dailyMaxBonus);
			if($amount > $dailyMaxBonus){
				//minus
				$amount=$this->utils->roundCurrency($dailyMaxBonus);
			}
		}

		$totalMaxBonus = $this->getMaxbonusByPlayerId($playerId);

		if (!empty($totalMaxBonus)) {
			//sum cashback from transactions
			$this->load->model('transactions');
			$cashback = $this->transactions->sumCashback($playerId);
			$this->utils->debug_log('check max bonus playerId', $playerId, 'cashback', $cashback, 'amount', $amount, 'totalMaxBonus', $totalMaxBonus);
			if($cashback + $amount > $totalMaxBonus){

				//minus
				$amount=$this->utils->roundCurrency($totalMaxBonus-$cashback);
			}
		}

		return $amount;
	}

	/**
	 * detail: check if the cashback is already paid
	 *
	 * @param int $playerId
	 * @param string $totalDate
	 *
	 * @return Boolean
	 */
	public function isPaidCashback($playerId, $totalDate) {
		$this->db->where('player_id', $playerId)->where('total_date', $totalDate)
			->where('paid_flag', self::DB_TRUE)->from('total_cashback_player_game_daily');

		return $this->runExistsResult();
	}

	/**
	 * only from config as default value
	 * @return int
	 */
	public function getMiniCashbackAmount() {
		return $this->utils->getConfig('min_cashback_amount');
	}

	protected function payRows($rows, $min_cashback_amount, $withdraw_condition_bet_times, $superadmin, $dry_run,
			&$lockFailedRows=[], $debug_mode=false){

		$commonSettings=(array) $this->getCashbackSettings();
		$commonMaxBonus=$commonSettings['max_cashback_amount'];

		foreach ($rows as $row) {
			$message = null;

			$playerId=$row->player_id;
			//player id
			// $allFinished=$this->withdraw_condition->isAllFinishedPlayerPromotion($row->player_id);

			$controller = $this;
			$rlt = $this->lockAndTransForPlayerBalance($playerId, function()
				use ($controller, &$message, $playerId, $commonMaxBonus, $row, $debug_mode, $dry_run,
					$withdraw_condition_bet_times, $min_cashback_amount, $superadmin) {
				$success=true;
				//it maybe update wallet, that's why put it inside lock
				$allFinished = $this->withdraw_condition->autoCheckWithdrawConditionAndMoveBigWallet($playerId, $message,
					null, true, false);

				$this->utils->debug_log('check withdraw condition on pay cashback allFinished',
					$allFinished, $message, $row->total_date, $row->player_id, $row->amount);

				if ($allFinished) {

					$old_amount=$row->amount;
					//try fix with max bonus
					$row->amount=$this->processMaxBonus($row->player_id, $row->amount, $commonMaxBonus);
                $row->withdraw_condition_amount = $this->utils->roundCurrencyForShow($row->amount * $withdraw_condition_bet_times);
					$this->utils->debug_log('processMaxBonus', $old_amount, $row->amount);

					// $this->lessThanMaxbonus($row->player_id, $row->amount);
					if ($row->amount >= $min_cashback_amount && $row->amount>0){
						// && $this->lessThanDailyMaxbonus($row->player_id, $row->amount, $commonMaxBonus) && $this->lessThanMaxbonus($row->player_id, $row->amount, $commonMaxBonus)) {

						if ($this->utils->isEnabledFeature('refresh_player_balance_before_pay_cashback')) {
							$this->utils->resetBalance($row->player_id);
						}

						$this->utils->debug_log('try pay cashback', $row->total_date, $row->player_id, $row->amount);
						//check paid flag again
						if (!$controller->isPaidCashback($row->player_id, $row->total_date)) {
							if ($debug_mode) {
								//print log and return
								$controller->utils->debug_log('pay cashback debug mode, locked player:' . $row->player_id . ' amount:' . $row->amount, 'date', $row->total_date);
								return true;
							}

							$tranId = $controller->transactions->insertAutoCashbackTransaction($row->player_id, $row->amount, $superadmin->userId, $row->total_date);
							if ($tranId) {
								//update flag
								$controller->setPaidFlagByPlayerAndDate($row->player_id, $row->total_date, $row->amount, $row->withdraw_condition_amount);
								//add withdraw condition
								if ($row->withdraw_condition_amount > 0) {
									//write to withdraw condition
									$controller->withdraw_condition->createWithdrawConditionForCashback($tranId,
										$row->withdraw_condition_amount, $withdraw_condition_bet_times, $row->player_id, $row->amount);
								}
							}else{
								$success=false;
							}
						} else {
							$controller->utils->error_log('already paid player id', $row->player_id, 'total date', $row->total_date);
						}
					} else {
						$this->utils->debug_log('ignore cashback', $row->total_date, $row->player_id, $row->amount, 'mini', $min_cashback_amount);
					}

				} else {
					$this->utils->debug_log('allFinished is false ignore', $allFinished, 'player id', $row->player_id);
				}
				//trans result
				return $success;
			});

			if (!$rlt) {
				$lockFailedRows[]=$row;
				$this->utils->error_log('pay cashback failed, transaction failed, player id', $row->player_id, 'amount', $row->amount, 'date', $row->total_date);
			}

		} //foreach ($rows as $row) {

	}

	/**
	 * detail: pay cashback
	 *
	 * @param string $date
	 * @param int $playerId
	 *
	 * @return array
	 */
	public function payCashback($date, $playerId = null, $debug_mode = false) {
		$result = true;

		$this->utils->debug_log('pay start', $date, $playerId, 'debug_mode', $debug_mode);
		$this->load->model(array('transactions', 'withdraw_condition'));

		$cashbackSettings = $this->getCashbackSettings();
		$withdraw_condition_bet_times = isset($cashbackSettings->withdraw_condition) ? $cashbackSettings->withdraw_condition : 0;
		$superadmin = $this->users->getSuperAdmin();

		$this->db->select('sum(amount) as amount, sum(withdraw_condition_amount) as withdraw_condition_amount,  total_date, player_id', false)->from('total_cashback_player_game_daily')
			->where('total_date', $date)->where('paid_flag', self::DB_FALSE);
		if (!empty($playerId)) {
			$this->db->where('player_id', $playerId);
		}
		$this->db->group_by('player_id,total_date');

		$rows = $this->runMultipleRow();

		$this->utils->debug_log('pay cashback total', count($rows));
		$min_cashback_amount = $cashbackSettings->min_cashback_amount; //$this->getMiniCashbackAmount();
		$lockFailedRows=[];
		$dry_run = false;
		if (!empty($rows)) {

			$this->payRows($rows, $min_cashback_amount, $withdraw_condition_bet_times, $superadmin, $dry_run, $lockFailedRows, $debug_mode);

		} else {
			$this->utils->debug_log('cashback is empty');
		}

		while (!empty($lockFailedRows)) {

			$this->utils->debug_log('try pay cashback again', $lockFailedRows);
			sleep(2);
			$lockFailedAgainRows=[];
			$this->payRows($lockFailedRows, $min_cashback_amount,$withdraw_condition_bet_times, $superadmin, $dry_run, $lockFailedAgainRows);

			$this->utils->debug_log('try pay cashback again again', $lockFailedAgainRows);

			$lockFailedRows=$lockFailedAgainRows;

		}

		return $result;
	}

	/**
	 * pay cashback by id array
	 *
	 * @param $selected_id_array array of id
	 * @param $force
	 * @param $debug_mode
	 *
	 */
	// public function manuallyPayCashbackByIdArr($selected_id_array, $force = false, $debug_mode = false) {
	// 	$result = true;

	// 	$this->utils->debug_log('pay by id', $selected_id_array, 'debug_mode', $debug_mode);
	// 	$this->load->model(array('transactions', 'withdraw_condition'));

	// 	$cashbackSettings = $this->getCashbackSettings();
	// 	$withdraw_condition_bet_times = isset($cashbackSettings->withdraw_condition) ? $cashbackSettings->withdraw_condition : 0;
	// 	$superadmin = $this->users->getSuperAdmin();

	// 	$rows = null;
	// 	if (is_array($selected_id_array) && !empty($selected_id_array)) {
	// 		$this->db->select('amount, withdraw_condition_amount,  total_date, player_id, id, paid_flag')
	// 			->from('total_cashback_player_game_daily')
	// 			->where('paid_flag', self::DB_FALSE)->where_in('id', $selected_id_array);

	// 		$rows = $this->runMultipleRow();
	// 	}

	// 	$commonSettings=(array) $this->getCashbackSettings();
	// 	$commonMaxBonus=$commonSettings['max_cashback_amount'];

	// 	$this->utils->debug_log('pay cashback total', count($rows));
	// 	if (!empty($rows)) {
	// 		$min_cashback_amount = $cashbackSettings->min_cashback_amount;// $this->getMiniCashbackAmount();
	// 		foreach ($rows as $row) {
	// 			//player id
	// 			// $allFinished=$this->withdraw_condition->isAllFinishedPlayerPromotion($row->player_id);
	// 			$allFinished = $this->withdraw_condition->autoCheckWithdrawConditionAndMoveBigWallet($row->player_id, $message,
	// 				null, true, false);

	// 			$this->utils->debug_log('check withdraw condition on pay cashback allFinished', $allFinished, $message, $row->total_date, $row->player_id, $row->amount);

	// 			if ($allFinished || $force) {

	// 				// $this->lessThanMaxbonus($row->player_id, $row->amount);
	// 				// if ($row->amount >= $min_cashback_amount &&
	// 				//manually pay , don't check min cashback

	// 				$old_amount=$row->amount;
	// 				//try fix with max bonus
	// 				$row->amount=$this->processMaxBonus($row->player_id, $row->amount, $commonMaxBonus);
	// 				$this->utils->debug_log('processMaxBonus', $old_amount, $row->amount);

	// 				if ($row->amount >0){ // $this->lessThanMaxbonus($row->player_id, $row->amount)) {

	// 					$lockedKey = null;
	// 					$lock_it = $this->utils->lockResourceBy($row->player_id, Utils::LOCK_ACTION_BALANCE, $lockedKey);
	// 					// $lock_it = $this->utils->lockActionById($row->player_id, Utils::LOCK_ACTION_BALANCE);
	// 					if (!$lock_it) {
	// 						$this->utils->debug_log('pay cashback failed, lock failed, player id', $row->player_id, 'amount', $row->amount, 'date', $row->total_date);
	// 						continue;
	// 					}

	// 					try {
	// 						$this->utils->debug_log('try pay cashback', $row->total_date, $row->player_id, $row->amount);
	// 						$this->startTrans();
	// 						try {

	// 							//check paid flag again
	// 							if ($row->paid_flag == '0') {

	// 								if ($debug_mode) {
	// 									//print log and return
	// 									$this->utils->debug_log('pay cashback debug mode, locked player:' . $row->player_id . ' amount:' . $row->amount, 'date', $row->total_date);
	// 									continue;
	// 								}

	// 								// $this->utils->debug_log('check existsAutoCashbackTransaction', $row->player_id, $row->total_date);
	// 								// if (!$row->paid_flag) {
	// 								// $this->utils->debug_log('insertAutoCashbackTransaction', $row->player_id, $row->amount, $row->total_date);
	// 								$tranId = $this->transactions->insertAutoCashbackTransaction($row->player_id, $row->amount, $superadmin->userId, $row->total_date);
	// 								if ($tranId) {
	// 									//update flag and amount
	// 									$this->setPaidFlag($row->id, $row->amount);
	// 									//add withdraw condition
	// 									if ($row->withdraw_condition_amount > 0) {
	// 										//write to withdraw condition
	// 										$this->withdraw_condition->createWithdrawConditionForCashback($tranId,
	// 											$row->withdraw_condition_amount, $withdraw_condition_bet_times, $row->player_id, $row->amount);
	// 									}
	// 								}
	// 								// }

	// 							} else {
	// 								$this->utils->error_log('already paid player id', $row->player_id, 'total date', $row->total_date);
	// 							}

	// 						} finally {
	// 							$rlt = $this->endTransWithSucc();
	// 							if (!$rlt) {
	// 								$this->utils->debug_log('pay cashback failed, transaction failed, player id', $row->player_id, 'amount', $row->amount, 'date', $row->total_date);
	// 							}
	// 						}
	// 					} finally {
	// 						$rlt = $this->utils->releaseResourceBy($row->player_id, Utils::LOCK_ACTION_BALANCE, $lockedKey);
	// 						// $rlt = $this->utils->releaseActionById($row->player_id, Utils::LOCK_ACTION_BALANCE);
	// 					}
	// 				} else {
	// 					$this->utils->debug_log('ignore cashback', $row->total_date, $row->player_id, $row->amount, 'mini', $min_cashback_amount);
	// 				}

	// 			} else {
	// 				$this->utils->debug_log('allFinished is false ignore', $allFinished, 'player id', $row->player_id);
	// 			}
	// 		}
	// 	} else {
	// 		$this->utils->debug_log('cashback is empty');
	// 		$result = false;
	// 	}
	// 	return $result;
	// }

	/**
	 * detail: get bet of a certain player by date range
	 *
	 * @param string $startDateTime
	 * @param string $endDateTime
	 *
	 * @return array
	 */
	public function getPlayerBetThruDate($startDateTime, $endDateTime) {
		$this->load->model(array('game_logs'));

		$sql = <<<EOD
SELECT tpgh.player_id, sum(tpgh.bet_amount) as betting_total, tpgh.game_description_id, tpgh.game_platform_id
  FROM game_logs as tpgh
  join game_description as gd on tpgh.game_description_id=gd.id and gd.no_cash_back!=1
where date_format(tpgh.end_at, '%Y-%m-%d%H')>=? and date_format(tpgh.end_at, '%Y-%m-%d%H')<=?
and flag=?
group by tpgh.player_id, tpgh.game_description_id, tpgh.game_platform_id
EOD;
		$qry = $this->db->query($sql, array($startDateTime, $endDateTime, Game_logs::FLAG_GAME));
		return $this->getMultipleRow($qry);
	}

	// public function isGameLogsHasBets() {

	// 	$query = $this->db->get('game_logs');
	// 	if ($query->num_rows() > 0) {
	// 		return true;
	// 	} else {
	// 		return false;
	// 	}
	// }

	/**
	 * detail: check if the certain game level is allowed
	 *
	 * @param int $levelId
	 * @param int $gameDescriptionId
	 * @return array
	 */
	public function isAllowedGameInLevel($levelId, $gameDescriptionId) {
		//empty means every game
		$result = empty($gameDescriptionId);
		if (!$result) {

			$this->db->from('group_level_cashback_game_description')->where('vipsetting_cashbackrule_id', $levelId)
				->where('game_description_id', $gameDescriptionId);

			//search vipsetting_cashback_game
			// $this->db->from($this->levelAllowedGameTable)->where($this->levelAllowedGameLevelId, $levelId)
			// 	->where('game_description_id', $gameDescriptionId);
			$result = $this->runExistsResult();
			// $this->utils->printLastSQL();
		}
		return $result;
	}

	/**
	 *
	 * priority: game description > game type > game platform > level
	 *
	 * @param  int $levelId                   [description]
	 * @param  int $gamePlatformId            [description]
	 * @param  int $gameTypeId                [description]
	 * @param  int $gameDescriptionId         [description]
	 * @param  int $level_cashback_percentage [description]
	 * @return float
	 */
	// public function getCashbackRateInfoInLevel($levelId, $gamePlatformId, $gameTypeId, $gameDescriptionId, $level_cashback_percentage) {

	// 	$result=null;

	// 	if (!empty($result)) {

	// 		$this->db->select('percentage')->from('group_level_cashback_game_description')
	// 		    ->where('vipsetting_cashbackrule_id', $levelId)
	// 			->where('game_description_id', $gameDescriptionId);
	// 		$result = $this->runOneRowOneField('percentage');
	// 		$this->utils->debug_log('try game description '.$gameDescriptionId, $result);
	// 	}

	// 	if (!empty($result)) {

	// 		$this->db->select('percentage')->from('group_level_cashback_game_type')
	// 		    ->where('vipsetting_cashbackrule_id', $levelId)
	// 			->where('game_type_id', $gameTypeId);
	// 		$result = $this->runOneRowOneField('percentage');
	// 		$this->utils->debug_log('try game type '.$gameTypeId, $result);

	// 	}

	// 	if (!empty($result)) {

	// 		$this->db->select('percentage')->from('group_level_cashback_game_description')
	// 		    ->where('vipsetting_cashbackrule_id', $levelId)
	// 			->where('game_type_id', $gameTypeId);
	// 		$result = $this->runOneRowOneField('percentage');
	// 		$this->utils->debug_log('try game type '.$gameTypeId, $result);

	// 	}

	// 	$result = $cashback_percentage;

	// 	return $result;

	// }

	/**
	 *
	 * priority: game description > game type > game platform > level
	 *
	 * @param  int $player_id           [description]
	 * @param  int $levelId             [description]
	 * @param  int $game_platform_id    [description]
	 * @param  int $game_type_id        [description]
	 * @param  int $game_description_id [description]
	 * @param  int $extra_info          [description]
	 * @return object      cashback_percentage, cashback_maxbonus, id, level_id
	 */
	public function getPlayerRateFromLevel($player_id, $levelId, $game_platform_id, $game_type_id, $game_description_id, $extra_info = null) {
		$row = null;

		if(isset($extra_info['levelCashbackMap']) && !empty($extra_info['levelCashbackMap'])){
            $row = $this->getCashbackRuleFromLevelCashbackMap(@$extra_info['levelCashbackMap'], $levelId, $game_platform_id, $game_type_id, $game_description_id, FALSE);
        }

		$this->utils->debug_log('player id:' . $player_id . ' level:' . $levelId . ' row', $row);

		if (empty($row)) {
			//if no any vip level settings, try common settings
			$commonRules = [];
			$playerSumBetAmount = 0;

			if (!empty($extra_info)) {
				if (isset($extra_info['commonRules'])) {
					$commonRules = $extra_info['commonRules'];
				}
				if (isset($extra_info['mapPlayerBet'])) {
					$mapPlayerBet = $extra_info['mapPlayerBet'];
					if (!empty($mapPlayerBet)) {
						if (isset($mapPlayerBet[$player_id])) {
							$playerSumBetAmount = $mapPlayerBet[$player_id];
						}
					}
				}
			}

			if (!empty($commonRules) && !empty($playerSumBetAmount)) {
				//search on common rules
				$row = $this->getPercentageByCommonRules($player_id, $game_description_id, $playerSumBetAmount, $commonRules);
				$this->utils->debug_log('use cashback common rules', $row, $player_id, 'game descripton:'.$game_description_id, $playerSumBetAmount);
			}
		}

		return $row;
	}

	public function getCashbackRuleFromLevelCashbackMap($levelCashbackMap, $levelId, $game_platform_id, $game_type_id, $game_description_id, $allowed_empty_percentage = FALSE){
	    $row = null;

        //priority: game description > game type > game platform > level
        if (!isset($levelCashbackMap[$levelId])) {
            return $row;
        }

        //only available on game description
        $available_on_level = isset($levelCashbackMap[$levelId]['game_description'][$game_description_id]);

        if (!$available_on_level) {
            $this->utils->debug_log('level:' . $levelId . ' not available', $game_platform_id, $game_type_id, $game_description_id);
            return $row;
        }

        $row = (object) ['cashback_percentage' => $levelCashbackMap[$levelId]['cashback_percentage'],
            'cashback_maxbonus' => $levelCashbackMap[$levelId]['cashback_maxbonus'],
            'id' => $levelId, 'level_id' => $levelId];

        if (isset($levelCashbackMap[$levelId]['game_platform'][$game_platform_id])) {
            $row->cashback_percentage = ($levelCashbackMap[$levelId]['game_platform'][$game_platform_id] > 0) ? $levelCashbackMap[$levelId]['game_platform'][$game_platform_id] : $row->cashback_percentage;
        }

        if (isset($levelCashbackMap[$levelId]['game_type'][$game_type_id])) {
            $row->cashback_percentage = ($levelCashbackMap[$levelId]['game_type'][$game_type_id] > 0) ? $levelCashbackMap[$levelId]['game_type'][$game_type_id] : $row->cashback_percentage;
        }

        if (isset($levelCashbackMap[$levelId]['game_description'][$game_description_id])) {
            $row->cashback_percentage = ($levelCashbackMap[$levelId]['game_description'][$game_description_id] > 0) ? $levelCashbackMap[$levelId]['game_description'][$game_description_id] : $row->cashback_percentage;
        }

        if($allowed_empty_percentage){
            return $row;
        }else{
            return ($row->cashback_percentage > 0) ? $row : NULL;
        }
    }

	// public function getAllowedGameRateInLevel($levelId, $gameDescriptionId, $cashback_percentage) {
	// 	//empty means every game
	// 	$result = $cashback_percentage;
	// 	if (!empty($gameDescriptionId)) {
	// 		//search vipsetting_cashback_game
	// 		$this->db->from($this->levelAllowedGameTable)->where($this->levelAllowedGameLevelId, $levelId)
	// 			->where('game_description_id', $gameDescriptionId);
	// 		$row = $this->runOneRow();
	// 		// $this->utils->printLastSQL();
	// 		$this->utils->debug_log('get cashback row', (array) $row, 'cashback_percentage', $cashback_percentage);
	// 		if (!empty($row)) {
	// 			//get percentage
	// 			//order by
	// 			if (!empty($row->game_desc_percentage)) {
	// 				$result = $row->game_desc_percentage;
	// 			} elseif (!empty($row->game_type_percentage)) {
	// 				$result = $row->game_type_percentage;
	// 			} elseif (!empty($row->game_platform_percentage)) {
	// 				$result = $row->game_platform_percentage;
	// 			}
	// 			// $this->utils->printLastSQL();
	// 		} else {
	// 			//disabled
	// 			$result = null;
	// 		}
	// 	}
	// 	return $result;
	// }

	/**
	 * detail: betting amount from total_player_game_day cahsback rate from player_level_history to total_cashback_player_game_daily
	 *
     * @deprecated
     *
	 * @param string $date
	 * @param int $playerId
	 *
	 * @return double
	 */
	public function totalCashbackDaily($date, $playerId = null) {
		//calc betting amount
		//total_player_game_day
		// $rows = $this->getAllPlayerGameLevelInfo();
		// foreach ($rows as $row) {

		// }
		//write to total_cashback_player_game_daily
		//by history
		// $rows = $this->getAllHistoryList();
		$startHour = $this->config->item('cashback_start_hour');
		$endHour = $this->config->item('cashback_end_hour');

		return $this->totalCashback($date, $startHour, $endHour, $playerId);

	}

	/**
	 * detail: daily total cashback using setting
	 *
     * @deprecated
     * @see Player_cashback_library::calculateDailyTotalCashbackBySettings
     *
	 * @param object $cashBackSettings
	 * @param string $date
	 * @param int $playerId
	 *
	 * @return double
	 */
	public function totalCashbackDailyBySettings($cashBackSettings, $date, $playerId = null) {
		//calc betting amount
		//total_player_game_day
		// $rows = $this->getAllPlayerGameLevelInfo();
		// foreach ($rows as $row) {

		// }
		//write to total_cashback_player_game_daily
		//by history
		// $rows = $this->getAllHistoryList();
		// $startHour = $this->config->item('cashback_start_hour');
		// $endHour = $this->config->item('cashback_end_hour');
		$startHour = $cashBackSettings->fromHour;
		$endHour = $cashBackSettings->toHour;
		$withdraw_condition_bet_times = isset($cashBackSettings->withdraw_condition) ? $cashBackSettings->withdraw_condition : 0;

		return $this->totalCashback($date, $startHour, $endHour, $playerId, $withdraw_condition_bet_times);
	}

    public function totalCashbackDailyFriendReferralBySettings($cashBackSettings, $date, $playerId = null) {
        $startHour = $cashBackSettings->fromHour;
        $endHour = $cashBackSettings->toHour;
        $withdraw_condition_bet_times = isset($cashBackSettings->withdraw_condition) ? $cashBackSettings->withdraw_condition : 0;
        $start_date = null;
        $end_date = null;
        $result = false;
        return $this->totalCashbackFriendReferral($date, $startHour, $endHour, $playerId, $withdraw_condition_bet_times, $result, $start_date, $end_date, self::FRIEND_REFERRAL_CASHBACK, false);
    }

	function getDayCashbackSetting($cashbackWeeklySetting) {
		$day = '';
		if ($cashbackWeeklySetting == 1) {
			$day = 'Monday';
		} elseif ($cashbackWeeklySetting == 2) {
			$day = 'Tuesday';
		} elseif ($cashbackWeeklySetting == 3) {
			$day = 'Wednesday';
		} elseif ($cashbackWeeklySetting == 4) {
			$day = 'Thursday';
		} elseif ($cashbackWeeklySetting == 5) {
			$day = 'Friday';
		} elseif ($cashbackWeeklySetting == 6) {
			$day = 'Saturday';
		} elseif ($cashbackWeeklySetting == 7) {
			$day = 'Sunday';
		}
		return $day;
	}

    /**
     * @deprecated
     * @see Player_cashback_library::calculateWeeklyTotalCashbackBySettings()
     *
     * @param      $cashBackSettings
     * @param      $date
     * @param null $playerId
     *
     * @return bool|int|void
     */
	public function totalCashbackPeriodBySettings($cashBackSettings, $date, $playerId = null) {

		if(!$this->utils->isEnabledFeature('enabled_weekly_cashback')){
			return;
		}

		$startHour = $cashBackSettings->fromHour;
		$endHour = $cashBackSettings->toHour;
		$withdraw_condition_bet_times = isset($cashBackSettings->withdraw_condition) ? $cashBackSettings->withdraw_condition : 0;
		$weekly = $cashBackSettings->weekly;

		$startDate = date("Y-m-d", strtotime("last week monday"));
		$endDate = date("Y-m-d", strtotime("last week sunday"));
		$result = false;

		// if found today is the day in cashback setting then calculate last week cash back
		$cashback_day = $this->getDayCashbackSetting($weekly);
		$current_day = date('l', strtotime($date));

		if ($cashback_day == $current_day) {
			return $this->totalCashback($date, $startHour, $endHour, $playerId, $withdraw_condition_bet_times, $result, $startDate, $endDate);
		} else {
			return false;
		}
	}

	/**
	 * manually calc cashback
     *
     * @deprecated
	 * @param  string $startDate [description]
	 * @param  string $endDate   [description]
	 * @param  int $playerId  null or id
	 * @return bool
	 */
	public function totalCashbackManually($date, $playerId = null) {

		$cashBackSettings = $this->getCashbackSettings();

		$startHour = $cashBackSettings->fromHour;
		$endHour = $cashBackSettings->toHour;
		$withdraw_condition_bet_times = isset($cashBackSettings->withdraw_condition) ? $cashBackSettings->withdraw_condition : 0;

		//one by one
		//from start date to end date
		//$success = false;
		return $this->totalCashback($date, $startHour, $endHour, $playerId, $withdraw_condition_bet_times, $success);
	}

	/**
	 * detail: get daily cashback id
	 *
	 * @param int $player_id
	 * @param int $game_description_id
	 * @param string $total_date
	 *
	 * @return int
	 */
	public function getCashbackDailyId($player_id, $game_description_id, $total_date, $cashback_type) {
		//player id, game description id, total date
		$this->db->select('id')->from('total_cashback_player_game_daily')
			->where('total_date', $total_date)
			->where('player_id', $player_id)
			->where('game_description_id', $game_description_id);

		if ($cashback_type == self::FRIEND_REFERRAL_CASHBACK) {
			$this->db->where('cashback_type', $cashback_type);
		}

		return $this->runOneRowOneField('id');
	}

    public function getCashbackDailayIdFromUniqueId($unique_id) {
        //player id, game description id, total date
        $this->db->select('id')
            ->from('total_cashback_player_game_daily')
            ->where('uniqueid', $unique_id);

        return $this->runOneRowOneField('id');
    }

	/**
	 * detail: get cashback id
	 *
	 * @param int $player_id
	 * @param int $game_description_id
	 * @param string $time_start
	 * @param string $time_end
	 *
	 * @return int
	 */
	public function getCashbackId($player_id, $game_description_id, $time_start, $time_end) {
		//player id, game description id, total date
		$this->db->select('id')
			->from('total_cashback_player_game')
			->where('time_start', $time_start)
			->where('time_end', $time_end)
			->where('player_id', $player_id)
			->where('game_description_id', $game_description_id);

		return $this->runOneRowOneField('id');
	}

	/**
	 * detail: sync daily cashback
	 *
	 * @param int $player_id
	 * @param int $game_platform_id
	 * @param int $game_description_id
	 * @param string $total_date
	 * @param double $cashback_amount
	 * @param int $history_id
	 * @param int $game_type_id
	 * @param int $level_id
	 * @param string $cashback_percentage
	 * @param double $bet_amount
	 * @param double $withdrawal_condition_amount
	 *
	 * @return Boolean
	 */
	public function syncCashbackDaily($player_id, $game_platform_id, $game_description_id, $total_date,
		$cashback_amount, $history_id, $game_type_id, $level_id, $cashback_percentage, $bet_amount,
		$withdraw_condition_amount = null, $max_bonus = 0, $original_bet_amount = 0, $cashback_type = 1 ,$invited_player_id = null, $uniqueid = '') {
		//get id by unique key: player_id, game_description_id, total_date
        if(!empty($uniqueid)){
            $id = $this->getCashbackDailayIdFromUniqueId($uniqueid);
        }else{
            $id = $this->getCashbackDailyId($player_id, $game_description_id, $total_date, $cashback_type);
        }
        if(empty($uniqueid)){
        	$uniqueid=null;
        }

		#echo 'cashback type======================='.$cashback_type.'<br/>';
		#var_dump($id);

		//set same data for update/insert
		$data = array('player_id' => $player_id,
			'game_description_id' => $game_description_id,
			'game_type_id' => $game_type_id,
			'game_platform_id' => $game_platform_id,
			'total_date' => $total_date,
			'amount' => $this->utils->formatCurrencyNumber($cashback_amount),
			'history_id' => $history_id,
			'level_id' => $level_id,
			'updated_at' => $this->utils->getNowForMysql(),
			'cashback_percentage' => $cashback_percentage,
			'bet_amount' => $this->utils->formatCurrencyNumber($bet_amount),
			'withdraw_condition_amount' => $this->utils->formatCurrencyNumber($withdraw_condition_amount),
			'max_bonus' => $max_bonus,
			'original_bet_amount' => $this->utils->formatCurrencyNumber($original_bet_amount),
			'cashback_type' => $cashback_type,
			'invited_player_id' => $invited_player_id,
			'uniqueid' => $uniqueid
		);

		$this->db->set($data);
		if ($id) {
			//add where condition for update
			$this->db->where('id', $id);
			$this->db->update('total_cashback_player_game_daily');
		} else {
			$this->db->insert('total_cashback_player_game_daily');
		}
	}

	/**
	 * detail: sync cashback
	 *
	 * @param int $player_id
	 * @param int $cashback_request_id
	 * @param int $game_platform_id
	 * @param int $game_description_id
	 * @param string $time_start
	 * @param string $time_end
	 * @param double $cashback_amount
	 * @param int $history_id
	 * @param int $game_type_id
	 * @param int $level_id
	 * @param string $cashback_percentage
	 * @param double $bet_amount
	 * @param double $withdraw_condition_amount
	 * @param int $max_bonus
	 * @param int $original_bet_amount
	 */
	public function syncCashbackByTime($player_id, $cashback_request_id, $game_platform_id, $game_description_id,
		$time_start, $time_end, $cashback_amount, $history_id, $game_type_id, $level_id,
		$cashback_percentage, $bet_amount, $withdraw_condition_amount = null,
		$max_bonus = 0, $original_bet_amount) {

		//get id by unique key: player_id, game_description_id, total_date
		$id = $this->getCashbackId($player_id, $game_description_id, $time_start, $time_end);

		//set same data for update/insert
		$data = array(
			'game_platform_id' => $game_platform_id,
			'game_description_id' => $game_description_id,
			'player_id' => $player_id,
			'cashback_request_id' => $cashback_request_id,
			'amount' => $cashback_amount,
			'time_start' => $time_start,
			'time_end' => $time_end,
			'history_id' => $history_id,
			'game_type_id' => $game_type_id,
			'level_id' => $level_id,
			'cashback_percentage' => $cashback_percentage,
			'bet_amount' => $bet_amount,
			'withdraw_condition_amount' => $withdraw_condition_amount,
			'max_bonus' => $max_bonus,
			'original_bet_amount' => $original_bet_amount,
			'created_at' => $this->utils->getNowForMysql(),
			'updated_at' => $this->utils->getNowForMysql(),
		);

		if ($id) {
			unset($data['created_at']);
			$this->db->set($data);

			//add where condition for update
			$this->db->where('id', $id);
			$this->db->update('total_cashback_player_game');
		} else {
			$this->db->set($data);
			$this->db->insert('total_cashback_player_game');
		}
	}

	/**
	 * detail: increase vip group level
	 *
	 * @param int $vipSettingId
	 * @return void
	 */
	public function increaseVipGroupLevel($vipSettingId) {
		$this->startTrans();

		# INCREASE GROUP LEVEL COUNT
		$this->db->set('groupLevelCount', 'groupLevelCount + 1', false);
		$this->db->where('vipSettingId', $vipSettingId);
		$this->db->update('vipsetting');

		# ADD LEVEL
		$vip_group = $this->getVIPGroupDetails($vipSettingId);
		$vipLevel = $vip_group[0]['groupLevelCount'];
		$this->createVipCashbackGameRule([
			'vipsettingId' => $vipSettingId,
			'minDeposit' => 100 * $vipLevel,
			'maxDeposit' => 1000 * $vipLevel,
			'dailyMaxWithdrawal' => 10000 * $vipLevel,
			'vipLevel' => $vipLevel,
			'vipLevelName' => 'Level Name ' . $vipLevel,
		]);

		$this->endTrans();

		return $this->succInTrans();
	}

	/**
	 * detail: decrease vip group level
	 *
	 * @param int $vipSettingId
	 * @return void
	 */
	public function decreaseVipGroupLevel($vipSettingId) {
		$this->startTrans();

		# DECREASE GROUP LEVEL COUNT
		$this->db->set('groupLevelCount', 'groupLevelCount - 1', false);
		$this->db->where('vipSettingId', $vipSettingId);
		$this->db->where('groupLevelCount !=', 1); # VIP GROUP CANNOT HAVE ZERO LEVEL
		$this->db->update('vipsetting');

		if ($this->db->affected_rows()) {
			# DELETE LEVEL
			$this->db->select('vipsettingcashbackruleId');
			$this->db->from('vipsettingcashbackrule');
			$this->db->where('vipSettingId', $vipSettingId);
			$this->db->order_by('vipLevel', 'DESC');
			$this->db->limit(1);
			$row = $this->db->get()->row_array();
			if ($row) {
				$this->db->delete('vipsettingcashbackrule', ['vipsettingcashbackruleId' => $row['vipsettingcashbackruleId']]);
				// $this->db->delete('vipsettingcashbackbonuspergame', ['vipsettingcashbackruleId' => $row['vipsettingcashbackruleId']]);
				// $this->db->delete('vipsetting_cashback_game', ['vipsetting_cashbackrule_id' => $row['vipsettingcashbackruleId']]);
				// $this->db->trans_commit(); # COMMIT IF DELETE IS SUCCESSFUL
				// return true;
			}
		}

		$this->endTrans();
		return $this->succInTrans();
	}

	/**
	 * detail: get vip group level details
	 *
	 * @param int $vipgrouplevelId
	 * @return array
	 */
	public function getVipGroupLevelDetails($vipgrouplevelId) {

		//$query = $this->db->query("SELECT * FROM vipsettingcashbackrule WHERE vipsettingcashbackruleId = '" . $vipgrouplevelId . "'");
		$this->db->select('vipsettingcashbackrule.*,vipsetting.groupName')->from('vipsettingcashbackrule');
		$this->db->join('vipsetting', 'vipsettingcashbackrule.vipSettingId = vipsetting.vipSettingId');
		$this->db->where('vipsettingcashbackrule.vipsettingcashbackruleId', $vipgrouplevelId);
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * detail: get cashback bonus per game
	 *
	 * @param int $vipsettingcashbackruleId
	 * @return array
	 */
	public function getCashbackBonusPerGame($vipsettingcashbackruleId) {

		// $query = $this->db->query("SELECT vipsettingcashbackbonuspergame.*, game.game FROM vipsettingcashbackbonuspergame
		// 						   LEFT JOIN game
		// 						   ON game.gameId = vipsettingcashbackbonuspergame.gameType
		// 						   WHERE vipsettingcashbackruleId = ?", $vipsettingcashbackruleId);

		// if (!$query->result_array()) {
		// 	return false;
		// } else {
		// 	return $query->result_array();
		// }
	}

	/**
	 * detail: remove vip group level
	 *
	 * @param int $vipgrouplevelId
	 * @return Boolean
	 */
	public function deletevipgrouplevel($vipgrouplevelId) {
		$this->db->where('vipSettingId', $vipgrouplevelId);
		$this->db->delete('vipsettingcashbackrule');
	}

	/**
	 * detail: get vip group rules
	 *
	 * @param int $vipgroupId
	 * @return array or Boolean
	 */
	public function getVIPGroupRules($vipgroupId) {
		$this->db->select('vipsettingcashbackrule.*,vipsetting.groupName')->from('vipsettingcashbackrule')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId');
		$this->db->where('vipsetting.vipSettingId', $vipgroupId);

		$query = $this->db->get();
		$cnt = 0;
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
				if ($row['vipsettingcashbackruleId']) {
					$data[$cnt]['vipsettingcashbackgamerule'] = $this->getVIPGroupGamesRule($row['vipsettingcashbackruleId']);
				}
				$cnt++;
			}

			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * detail: activate vip group
	 *
	 * @param array $data
	 * @return Boolean
	 */
	public function activateVIPGroup($data) {
		$this->db->where('vipsettingId', $data['vipsettingId']);
		$this->db->update('vipsetting', $data);
	}

	/**
	 * detail: get vip group details
	 *
	 * @param int $vipsettingId
	 * @return array or Boolean
	 */
	public function getVIPGroupDetails($vipsettingId) {
		$this->db->from('vipsetting');
		$this->db->where('vipsettingId', $vipsettingId);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			// foreach ($query->result_array() as $row) {
			// 	$data[] = $row;
			// }
			//var_dump($data);exit();
			return $query->result_array();
		}
		return false;
	}

	/**
	 * detail: get vip group levels
	 *
	 * @param int $vipsettingId
	 * @return array or Boolean
	 */
	public function getVIPGroupLevels($vipsettingId) {
		$this->db->from('vipsettingcashbackrule');
		$this->db->where('vipSettingId', $vipsettingId);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	/**
	 * detail: get vip group details
	 *
	 * @param int $vipsettingId
	 * @return array or Boolean
	 */
	public function getVIPGroupDetail($vipsettingId) {
		$this->db->from('vipsetting');
		$this->db->where('vipsettingId', $vipsettingId);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->row_array();
		}
		return false;
	}

	/**
	 * detail: get vip group upgrade details
	 *
	 * @param int $vipsettingId
	 * @return array or Boolean
	 */
	public function getVIPGroupUpgradeDetails($upgradeId) {
		$this->db->from('vip_upgrade_setting');
		$this->db->where('upgrade_id', $upgradeId);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->row_array();
		}
		return false;
	}

	/**
	 * detail: remove vip group
	 *
	 * @param int $vipsettingId
	 * @return void
	 */
	public function deleteVIPGroup($vipsettingId) {
		$this->startTrans();

		$default_level_id = $this->utils->getConfig('default_level_id');

		$playerIds = $this->getPlayerIdsByVipSettingId($vipsettingId);
		foreach ($playerIds as $playerId) {
			$this->adjustPlayerLevel($playerId, $default_level_id);
		}

		$this->deleteVIPGroupItem($vipsettingId);

		$vipsettingcashbackruleIds = $this->getVipGroupItemRules($vipsettingId);
		if ($vipsettingcashbackruleIds && is_array($vipsettingcashbackruleIds) && !empty($vipsettingcashbackruleIds)) {
			foreach ($vipsettingcashbackruleIds as $vipsettingcashbackrule) {
				$this->db->delete('vipsettingcashbackrule', array('vipsettingcashbackruleId' => $vipsettingcashbackrule['vipsettingcashbackruleId']));
				// $this->db->delete('vipsettingcashbackbonuspergame', array('vipsettingcashbackruleId' => $vipsettingcashbackrule['vipsettingcashbackruleId']));
				// $this->db->delete('vipsetting_cashback_game', array('vipsetting_cashbackrule_id' => $vipsettingcashbackrule['vipsettingcashbackruleId']));
			}
		}

		$this->endTrans();

		return $this->succInTrans();
	}

	/**
	 * detail: soft delete vip group
	 *
	 * @param int $vipsettingId
	 * @return void
	 */
	public function fakeDeleteVIPGroup($vipsettingId) {

		$default_level_id = $this->utils->getConfig('default_level_id');

		$playerIds = $this->getPlayerIdsByVipSettingId($vipsettingId);
		foreach ($playerIds as $playerId) {
			$this->adjustPlayerLevel($playerId, $default_level_id);
		}

		$data = array(
			'deleted' => 1,
		);

		$this->db->set('deleted', 1);
		$this->db->set('note', 'groupName', false);
		$this->db->set('groupName', random_string());
		$this->db->where('vipsettingId', $vipsettingId);
		$this->db->update('vipsetting');

		$vipsettingcashbackruleIds = $this->getVipGroupItemRules($vipsettingId);
		foreach ($vipsettingcashbackruleIds as $vipsettingcashbackrule) {
			//update deleted 1 instead to delete
			$this->db->where('vipsettingcashbackruleId', $vipsettingcashbackrule['vipsettingcashbackruleId']);
			$this->db->update('vipsettingcashbackrule', $data);
			//update deleted 1 instead to delete
			// $this->db->where('vipsettingcashbackruleId', $vipsettingcashbackrule['vipsettingcashbackruleId']);
			// $this->db->update('vipsettingcashbackbonuspergame', $data);
			//update deleted 1 instead to delete
			// $this->db->where('vipsetting_cashbackrule_id', $vipsettingcashbackrule['vipsettingcashbackruleId']);
			// $this->db->update('vipsetting_cashback_game', $data);
		}

		$this->endTrans();

		return $this->succInTrans();
	}

	/**
	 * detail: get the vip group rules
	 *
	 * @param int $vipsettingId
	 * @return array
	 */
	public function getVipGroupItemRules($vipsettingId) {
		$this->db->select('vipsettingcashbackruleId')->from('vipsettingcashbackrule');
		$this->db->where('vipSettingId', $vipsettingId);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * detail: remove vip group
	 *
	 * @param int $vipsettingId
	 * @return Boolean
	 */
	public function deleteVIPGroupItem($vipsettingId) {
		$this->db->where('vipsettingId', $vipsettingId);
		$this->db->delete('vipsetting');
	}

	/**
	 * detail: update level for a certain player
	 *
	 * @param int $playerGroupId
	 * @return Boolean
	 */
	public function updatePlayerLevel($playerGroupId) {
		$newPlayerLevel = array('playerGroupId' => 1);
		$this->db->where('playerGroupId', $playerGroupId);
		$this->db->update('playerlevel', $newPlayerLevel);
	}

	/**
	 * detail: search vip group lists
	 *
	 * @param array $search
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return array
	 */
	public function searchVipGroupList($search, $limit, $offset) {

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$query = $this->db->query("SELECT vipsetting.*, admin1.username AS createdBy, admin2.username AS updatedBy
			FROM vipsetting
			LEFT JOIN adminusers AS admin1
			ON admin1.userId = vipsetting.createdBy
			LEFT JOIN adminusers AS admin2
			ON admin2.userId = vipsetting.updatedBy
			WHERE vipsetting.groupName = ?
			$limit
			$offset
		", $search);
		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * detail: get vip group game rule
	 *
	 * @param int $vipsettingcashbackruleId
	 * @return array or Boolean
	 */
	public function getVIPGroupGamesRule($vipsettingcashbackruleId) {
		// $this->db->select('vipsettingcashbackbonuspergame.*,game.game')
		// 	->from('vipsettingcashbackbonuspergame')
		// 	->join('game', 'game.gameId = vipsettingcashbackbonuspergame.gameType');
		// $this->db->where('vipsettingcashbackruleId', $vipsettingcashbackruleId);
		// $this->db->where('allowStatus', '0');

		// $query = $this->db->get();

		// if ($query->num_rows() > 0) {
		// 	foreach ($query->result_array() as $row) {
		// 		$data[] = $row;
		// 	}
		// 	//var_dump($data);exit();
		// 	return $data;
		// }
		// return false;
	}

	public function getAllPlayerLevelsForSelect() {
		$this->db->select('vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipsettingcashbackruleId,
						   vipsetting.vipSettingId,
						   vipsetting.groupName,
						   vipsettingcashbackrule.vipLevelName', FALSE)
			->from('vipsettingcashbackrule')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->where('vipsetting.deleted', 0);
		$this->db->order_by('groupName', 'ASC');
		$this->db->order_by('vipLevelName', 'ASC');
		$query = $this->db->get();
		$rows = $query->result_array();
		foreach ($rows as &$row) {
			$row['groupLevelName'] = lang($row['groupName']) . '|' . lang($row['vipLevelName']);
		}
		return $rows;
	}

	/**
	 * Get All PlayerLevels Dropdown.
	 *
	 * @param string $emptyWording The Wording of empty value,default.
	 * @return array
	 */
	public function getAllPlayerLevelsDropdown($emptyWording = '') {
		$this->db->select('vipsettingcashbackrule.vipsettingcashbackruleId,
                           vipsettingcashbackrule.vipLevelName,
						   vipsetting.vipSettingId,
						   vipsetting.groupName')
			->from('vipsettingcashbackrule')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$this->db->where('vipsetting.deleted', 0);
		$this->db->order_by('groupName', 'ASC');
		$this->db->order_by('vipLevelName', 'ASC');
		$query = $this->db->get();
		$rows = $query->result_array();
		$query->free_result();
		foreach ($rows as &$row) {
			$row['groupLevelName'] = lang($row['groupName']) . ' - ' . lang($row['vipLevelName']);
		}
		if($emptyWording == ''){
			$emptyWording = lang('player.08');
		}
		$empty = array('' => $emptyWording);
		$list = array_column($rows, 'groupLevelName', 'vipsettingcashbackruleId');

		return $empty + $list;
	} // EOF getAllPlayerLevelsDropdown

	/**
	 * detail: Will get all player Levels
	 *
	 * @return array
	 */
	public function getAllPlayerLevels() {
		$this->db->select('vipsettingcashbackrule.*,
						   vipsetting.vipSettingId,
						   vipsetting.groupLevelCount,
						   vipsetting.groupName')
			->from('vipsettingcashbackrule')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$this->db->where('vipsetting.deleted', 0);
		$this->db->order_by('vipsettingcashbackrule.vipsettingcashbackruleId', 'ASC');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * detail: Will get all player Levels
	 *
	 * @param Boolean $showGameTree
	 * @return array
	 */
	public function getGroupPlayerLevels($showGameTree = true) {
		$groupPlayerLvl = $this->getAllPlayerLevels();
		$playerLvlTree = array();
		foreach ($groupPlayerLvl as $gpl) {
			$groupId = $gpl['vipSettingId'];
			if (!array_key_exists($groupId, $playerLvlTree)) {
				$playerLvlTree[$groupId] = array('groupName' => $gpl['groupName'], 'playerLvlTree' => array());

				$playerLvlTree[$groupId]['playerLvlTree'][] =
				array('playerLevelId' => $gpl['vipsettingcashbackruleId'],
					'playerLevelName' => $gpl['vipLevelName'],
					'playerLevel' => $gpl['vipLevel']);
			} else {
				$playerLvlTree[$groupId]['playerLvlTree'][] =
				array('playerLevelId' => $gpl['vipsettingcashbackruleId'],
					'playerLevelName' => $gpl['vipLevelName'],
					'playerLevel' => $gpl['vipLevel']);
			}
		}

		return $playerLvlTree;
	}

	/**
	 * detail: get selected promo rule
	 *
	 * @param int $promoruleId
	 * @return array
	 */
	public function getSelectedPromoRuleGroup($promoruleId) {
		$this->db->select('vipSettingId')->from('promorulesallowedplayerlevel');
		$this->db->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = promorulesallowedplayerlevel.playerLevel', 'left');
		$this->db->where('promorulesallowedplayerlevel.promoruleId', $promoruleId);
		$this->db->group_by('vipSettingId');
		$query = $this->db->get();
		$rows = $query->result();
		$arr = [];
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$arr[] = $row->vipSettingId;
			}
		}
		return $arr;
	}

	/**
	 * detail: Will get all member group
	 *
	 * @return array
	 */
	public function getAllMemberGroup() {
		$this->db->select('vipsetting.groupName,vipsetting.vipSettingId')
			->from('vipsetting');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * detail: Will get player deposit rule
	 *
	 * @param int $playerId playerlevel playerId
	 * @return array
	 */
	public function getPlayerDepositRule($playerId) {
		// $playerLevel = $this->getPlayerGroupLevel($playerId);
		//var_dump($playerLevel[0]['playerGroupId']);exit();
		$this->db->select('vipsettingcashbackrule.minDeposit,vipsettingcashbackrule.maxDeposit')
			->from('playerlevel')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left');
		$this->db->where('playerlevel.playerId', $playerId);
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * detail: Will get player withdraw rule
	 *
	 * @param int $playerId playerlevel playerId
	 * @return array
	 */
	public function getPlayerWithdrawRule($playerId) {
		$this->db->select('vipsettingcashbackrule.dailyMaxWithdrawal, vipsettingcashbackrule.max_withdraw_per_transaction, vipsettingcashbackrule.max_withdrawal_non_deposit_player, vipsettingcashbackrule.withdraw_times_limit, vipsettingcashbackrule.min_withdrawal_per_transaction, vipsettingcashbackrule.overwrite_minimum_withdraw_setting')
			->from('playerlevel')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left');
		$this->db->where('playerlevel.playerId', $playerId);
		$query = $this->db->get();
        if ($query->num_rows() < 1) {
        	$this->utils->debug_log('getPlayerWithdrawRule SQL', $this->db->last_query());
        }
		return $query->result_array();
	}

	/**
	 * detail: get all levels
	 *
	 * @return array
	 */
	public function getAllLevels() {
		$this->db->from($this->levelTable);
		return $this->runMultipleRow();
	}

	/**
	 * detail: get highest group level
	 *
	 * @return array
	 */
	public function getHighestGroupLevelsId() {

		$sql = "SELECT  MAX(groupLevelCount) AS highest_count  FROM vipsetting";
		$query = $this->db->query($sql);
		$highest_count = $query->row_array()['highest_count'];

		$sql = "SELECT  vipSettingId  FROM vipsetting WHERE groupLevelCount = ? ";
		$query = $this->db->query($sql, array($highest_count));

		return $query->row_array()['vipSettingId'];

	}

	/**
	 * detail: get group levels
	 *
	 * @param int $vipSettingId
	 * @return array
	 */
	public function getGroupLevels($vipSettingId) {
		$sql = "SELECT vipLevel, vipLevelName  FROM vipsettingcashbackrule WHERE vipSettingId = ?";
		$query = $this->db->query($sql, array($vipSettingId));
		return $query->result_array();
	}

	/**
	 * detail: get current group level count
	 *
	 * @param int $vipSettingId
	 * @return array
	 */
	public function getGroupCurrLevelCount($vipSettingId) {
		$sql = "SELECT groupLevelCount FROM vipsetting WHERE vipSettingId = ?";
		$query = $this->db->query($sql, array($vipSettingId));
		return $query->row_array()['groupLevelCount'];

	}

	/**
	 * detail: get VIP group level setting
	 *
	 * @param int vipSettingId
	 * @param string $vipLevel
	 * @return array
	 */
	public function getVipGroupLevelSetting($vipSettingId, $vipLevel) {
		$sql = "SELECT * FROM vipsettingcashbackrule WHERE vipSettingId = ? AND vipLevel = ? ";
		$query = $this->db->query($sql, array($vipSettingId, $vipLevel));
		return $query->row_array();

	}

	/**
	 * detail: get VIP group level setting
	 *
	 * @param int vipSettingId
	 * @param string $vipLevel
	 * @return array
	 */
	public function checkSystemFeatureCashBack() {
        $sQuerySelect = "SELECT enabled FROM system_features WHERE name = ?";
		$query = $this->db->query($sQuerySelect, [self::SYSTEM_FEATURE_CASHBACK]);
		return $query->row_array();
	}

	/**
	 * detail: allow game description to all
	 *
	 * @param int $gameDescId
	 * @return Boolean
	 */
	public function allowGameDescToAll($gameDescId) {

		if($this->utils->getConfig('ignore_auto_add_new_game_to_vip_cashback')){
			$this->utils->debug_log('ignore this game', $gameDescId);
			return;
		}

		//add game desc and game type and game platform to group level

		$this->load->model(array('game_type_model', 'game_description_model'));

		$isCheckedCashBackFeature = $this->checkSystemFeatureCashBack();
		if(isset($isCheckedCashBackFeature) && $isCheckedCashBackFeature['enabled'] === '1') {
			return true;
		}
		$autoAddCashback = $this->game_type_model->isGameTypeSetToAutoAddCashback($gameDescId);
		#if system feature is disabled dont add to cashback
		if ($autoAddCashback) {
			$gameDesc = $this->game_description_model->getGameDescription($gameDescId);

			$this->db->from($this->levelTable);
			$rows = $this->runMultipleRowArray();
			foreach ($rows as $row) {
				//if not exist
				$this->db->from('group_level_cashback_game_platform')
					->where('vipsetting_cashbackrule_id', $row[$this->levelId])
					->where('game_platform_id', $gameDesc->game_platform_id);

				if (!$this->runExistsResult()) {
					//add to group_level_cashback_game_platform
					$data = [
						'vipsetting_cashbackrule_id' => $row[$this->levelId],
						'game_platform_id' => $gameDesc->game_platform_id,
						'percentage' => 0,
						'status' => self::DB_TRUE,
						'updated_at' => $this->utils->getNowForMysql(),
					];
					$this->insertData('group_level_cashback_game_platform', $data);
				} else {
					$this->utils->debug_log('not new game platform', $gameDesc->game_platform_id);
				}

				//if not exist
				$this->db->from('group_level_cashback_game_type')
					->where('vipsetting_cashbackrule_id', $row[$this->levelId])
					->where('game_type_id', $gameDesc->game_type_id);

				if (!$this->runExistsResult()) {
					//add to group_level_cashback_game_type
					$data = [
						'vipsetting_cashbackrule_id' => $row[$this->levelId],
						'game_type_id' => $gameDesc->game_type_id,
						'percentage' => 0,
						'status' => self::DB_TRUE,
						'updated_at' => $this->utils->getNowForMysql(),
					];
					$this->insertData('group_level_cashback_game_type', $data);
				} else {
					$this->utils->debug_log('not new game type', $gameDesc->game_type_id);
				}

				//if not exist
				$this->db->from('group_level_cashback_game_description')
					->where('vipsetting_cashbackrule_id', $row[$this->levelId])
					->where('game_description_id', $gameDesc->id);

				if (!$this->runExistsResult()) {
					//add to group_level_cashback_game_description
					$data = [
						'vipsetting_cashbackrule_id' => $row[$this->levelId],
						'game_description_id' => $gameDesc->id,
						'percentage' => 0,
						'status' => self::DB_TRUE,
						'updated_at' => $this->utils->getNowForMysql(),
					];
					$this->insertData('group_level_cashback_game_description', $data);
				} else {
					$this->utils->debug_log('not new game description', $gameDesc->id);
				}
			}
		}

		//check all group level
		// $this->db->from($this->levelTable);
		// $rules = $this->runMultipleRow();

		// $ruleIdArr = array();
		// if (!empty($rules)) {
		// 	foreach ($rules as $rule) {
		// 		$ruleIdArr[] = $rule->vipsettingcashbackruleId;
		// 	}
		// }

		// if (!empty($ruleIdArr)) {
		// 	//add to vipsetting_cashback_game
		// 	$this->db->from($this->levelAllowedGameTable)->where('game_description_id', $gameDescId);
		// 	if (!$this->runExistsResult()) {

		// 		$autoAddCashback = $this->game_type_model->isGameTypeSetToAutoAddCashback($gameDescId);
		// 		if($autoAddCashback) {
		// 			$data = array();
		// 			//new game description
		// 			foreach ($ruleIdArr as $ruleId) {
		// 				$data[] = array($this->levelAllowedGameLevelId => $ruleId,
		// 						'game_description_id' => $gameDescId, 'percentage' => 0, 'maxBonus' => 0);
		// 			}
		// 			$rlt = $this->db->insert_batch($this->levelAllowedGameTable, $data);
		// 			$this->utils->debug_log('insert game desc to group level', $rlt);
		// 		}

		// 	} else {
		// 		$this->utils->debug_log('not new game description', $gameDescId);
		// 	}
		// }

		return true;
	}

	/**
	 * detail: get cashback settings
	 *
	 * @return Object
	 */
	public function getCashbackSettings() {
		$this->load->library(['player_cashback_library']);
		$this->load->model(array('operatorglobalsettings'));

		$settingsStr = $this->operatorglobalsettings->getSettingValueWithoutCache(self::CASHBACK_SETTINGS_NAME);

		$cashbackSettings = (object) array();
		if (!empty($settingsStr)) {
			$cashbackSettings = json_decode($settingsStr);
			if (empty($cashbackSettings)) {
				$cashbackSettings = (object) array();
			}
		}

        $cashbackSettings->common_cashback_rules_mode = (isset($cashbackSettings->common_cashback_rules_mode)) ? $cashbackSettings->common_cashback_rules_mode : Player_cashback_library::COMMON_CASHBACK_RULES_MODE_BY_SINGLE;
		if(!$this->utils->isEnabledFeature('enabled_cashback_of_multiple_range')){
            $cashbackSettings->common_cashback_rules_mode = Player_cashback_library::COMMON_CASHBACK_RULES_MODE_BY_SINGLE;
        }

		// if (!isset($cashbackSettings->daysAgo)) {
		// 	$cashbackSettings->daysAgo = $this->config->item('cashback_days_ago');
		// }

		// if (!isset($cashbackSettings->fromHour)) {
		// 	$cashbackSettings->fromHour = $this->config->item('cashback_start_hour');
		// }
		$cashbackSettings->fromHour = str_pad(intval($cashbackSettings->fromHour), 2, '0', STR_PAD_LEFT);

		// if (!isset($cashbackSettings->toHour)) {
		// 	$cashbackSettings->toHour = $this->config->item('cashback_end_hour');
		// }
		$cashbackSettings->toHour = str_pad(intval($cashbackSettings->toHour), 2, '0', STR_PAD_LEFT);

		// if (!isset($cashbackSettings->payTimeHour)) {
		// 	$cashbackSettings->payTimeHour = $this->config->item('pay_time_hour');
		// }
		if(strlen($cashbackSettings->payTimeHour)!=5){
			$arr = explode(':', $cashbackSettings->payTimeHour);
			$hour = str_pad(intval($arr[0]), 2, '0', STR_PAD_LEFT);
			$minute = '00';
			if (count($arr) > 1) {
				$minute = str_pad(intval($arr[1]), 2, '0', STR_PAD_LEFT);
			}
			$cashbackSettings->payTimeHour = $hour . ':' . $minute;
		}

		$cashbackSettings->payLastUpdate = $this->getUpdateCashbackLastTime();
		// if (!isset($cashbackSettings->payLastUpdate)) {
		// 	$cashbackSettings->payLastUpdate = null;// $this->utils->getNowForMysql();
		// 	// $this->updateCashbackLastTime($cashbackSettings->payLastUpdate);
		// }

		if(!isset($cashbackSettings->min_cashback_amount)){
			$cashbackSettings->min_cashback_amount=$this->getMiniCashbackAmount();
		}

		return $cashbackSettings;
	}

	/**
	 * detail: update cashback last time
	 *
	 * @param string $dateTimeStr
	 * @return Boolean
	 */
	public function updateCashbackLastTime($dateTimeStr) {
		// $cashbackSettings = $this->getCashbackSettings();

		// $newArr = (array) $cashbackSettings;
		// $newArr['payLastUpdate'] = $dateTimeStr;
		// // $newArr = array(
		// // 	'daysAgo' => $cashbackSettings->daysAgo,
		// // 	'fromHour' => $cashbackSettings->fromHour,
		// // 	'toHour' => $cashbackSettings->toHour,
		// // 	'payTimeHour' => $cashbackSettings->payTimeHour,
		// // 	'payLastUpdate' => $dateTimeStr,
		// // );

		// $newCashbackSettings = json_encode($newArr);
		// return $this->operatorglobalsettings->putSetting(self::CASHBACK_SETTINGS_NAME, $newCashbackSettings);

		return $this->operatorglobalsettings->putSetting(self::LAST_UPDATE_CASHBACK_NAME, $dateTimeStr);
	}

	public function getUpdateCashbackLastTime(){
		return $this->operatorglobalsettings->getSettingValueWithoutCache(self::LAST_UPDATE_CASHBACK_NAME);
	}

	/**
	 * detail: get player by vip setting id
	 *
	 * @param int $vipSettingId
	 * @return array
	 */
	public function getPlayerIdsByVipSettingId($vipSettingId) {
		$this->db->distinct();
		$this->db->select('player.playerId');
		$this->db->from('player');
		$this->db->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left');
		$this->db->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = player.levelId OR vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left');
		$this->db->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId');
		$this->db->where('vipsetting.vipSettingId', $vipSettingId);
		$query = $this->db->get();
		return array_column($query->result_array(), 'playerId');
	}

	/**
	 * detail: get Allowed game ids for a cetain player
	 *
	 * @param int $playerId
	 * @return array
	 */
	public function getAllowedGameIdArr($playerId) {
		$levelId = $this->getPlayerLevelId($playerId);
		$rows = $this->getAllowedGameLevelRules($levelId);
		$arr = array();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$arr[] = $row->game_description_id;
			}
		}
		return $arr;
	}

	public function getAllowedGameIdKV($playerId) {
        $levelId = $this->getPlayerLevelId($playerId);
        $rows = $this->getAllowedGameLevelRules($levelId);
        $arr = array();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $arr[$row->game_description_id] = $row->game_description_id;
            }
        }
        return $arr;
    }

	/**
	 * detail: get player withdrawal rule
	 *
	 * @param int $playerId
	 * @return array
	 */
	public function getPlayerWithdrawalRule($playerId) {
		// $playerLevel = $this->getPlayerGroupLevel($playerId);
		//var_dump($playerLevel[0]['playerGroupId']);exit();
		$this->db->select('vipsettingcashbackrule.dailyMaxWithdrawal, vipsettingcashbackrule.withdraw_times_limit')
			->from('player')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = player.levelId', 'left');
		$this->db->where('player.playerId', $playerId);
		$query = $this->db->get();
		// return $query->result_array();
		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	/**
	 * detail: get group level lists
	 *
	 * @return array
	 */
	public function getGroupLevelListKV() {
		$this->db->select('vipsettingcashbackrule.vipsettingcashbackruleId, vipsettingcashbackrule.vipLevelName, vipsetting.groupName')
			->from($this->levelTable)->join('vipsetting', 'vipsetting.vipSettingId=vipsettingcashbackrule.vipSettingId')
			->order_by('vipsettingcashbackrule.vipSettingId, vipsettingcashbackrule.vipLevel');
		$rows = $this->runMultipleRow();
		$result = array('' => lang('N/A'));
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$result[$row->vipsettingcashbackruleId] = lang($row->groupName) . ' - ' . lang($row->vipLevelName);
			}
		}

		return $result;
	}

	/**
	 * detail: get group level lists
	 *
	 * @return array
	 */
	public function getGroupLevelList() {
		$result = [];
		$qry = $this->db->get($this->levelTable);
		$rlt = $qry->result_array();
		foreach ($rlt as $row) {
			$result[$row['vipsettingcashbackruleId']] = $row;
		}
		return $result;
	}

	/**
	 * detail: get VIP level id
	 *
	 * @param int $vipSettingId
	 * @param string $vipLevelNumber
	 * @return int
	 */
	public function getVipLevelIdByLevelNumber($vipSettingId, $vipLevelNumber) {
		$this->db->from($this->levelTable)->where('vipSettingId', $vipSettingId)->where('vipLevel', $vipLevelNumber);
		$row = $this->runOneRow();
		$levelId = null;
		if (!empty($row)) {
			$levelId = $row->vipsettingcashbackruleId;
		}
		return $levelId;
	}

	/**
	 * detail: update level up/down by batch
	 *
	 * @param string $fromDatatime
	 * @param string $toDatetime
	 *
	 * @return array
	 */
	public function batchUpDownLevel($fromDatetime, $toDatetime) {

		$this->load->model(array('total_player_game_day', 'player_model', 'transactions'));
		$result = array();

		$this->utils->debug_log('fromDatetime', $fromDatetime, 'toDatetime', $toDatetime);

		$gameLogData = $this->total_player_game_day->groupTotalBetsWinsLossGroupByPlayers($fromDatetime, $toDatetime);

		$allPlayerLevels = $this->getAllPlayerLevels(); // gets all player levels
		// $eee = var_export($gameLogData, true);
		// log_message('error', $eee);
		$levelMap = array();
		foreach ($allPlayerLevels as $lvl) {
			$levelMap[$lvl['vipsettingcashbackruleId']] = $lvl;
		}

		$this->utils->debug_log($gameLogData, 'all levels', count($levelMap));

		foreach ($gameLogData as $row) {
			// $playerLevel = $this->player_manager->getPlayerLevel($row['player_id']); // gets the player's level details
			// $vipSettingDetails = $this->player_manager->getVIPGroupDetails($playerLevel['vipSettingId']); // gets the vip setting detail joined by player
			$player = $this->player_model->getPlayerById($row['player_id']);
			// $this->utils->debug_log($row, (array) $player);
			if (!isset($levelMap[$player->levelId])) {
				//ignore
				$this->utils->error_log('ignore player', $player->playerId, $player->username);
				continue;
			}

			$this->startTrans();

			$playerLevel = $levelMap[$player->levelId];
			$this->utils->debug_log('upgradeAmount', $playerLevel['upgradeAmount'], 'downgradeAmount', $playerLevel['downgradeAmount']);

			if ($playerLevel['upgradeAmount'] > 0) {

				// Check upgrade amount if >= to total betting amount
				if ($row['total_bet'] >= $playerLevel['upgradeAmount'] && $playerLevel['vipLevel'] < $playerLevel['groupLevelCount']) {

					// $playerGroupId = $playerLevel['vipsettingcashbackruleId'] + 1;
					// $newVipLevel = $playerLevel['vipLevel'] + 1;
					//find by vipLevel
					$newVipLevelId = $this->getVipLevelIdByLevelNumber($playerLevel['vipSettingId'], $playerLevel['vipLevel'] + 1);
					if (!empty($newVipLevelId)) {
						$this->adjustPlayerLevel($row['player_id'], $newVipLevelId);

						$result[] = $this->utils->debug_log('upgraded', $player->username, $playerLevel['vipLevel'], 'to', $newVipLevelId);
					} else {

						$this->utils->error_log('upgrade failed', $player->username, $playerLevel['vipLevel'], 'to', $newVipLevelId);
					}
					// $this->player_manager->changePlayerLevel($row['player_id'], $data1);

					// $data2 = array(
					// 	'player_level_id' => $playerLevel['playerlevelId'],
					// 	'player_Id' => $row['player_id'],
					// 	'compared_amount' => $playerLevel['upgradeAmount'],
					// 	'player_amount' => $row['total_betting_amount'],
					// 	'created_date' => date('Y-m-d H:i:s'),
					// 	'status' => 1,
					// 	'note' => 'Upgrade from ' . $playerLevel['groupName'] . ' ' . $playerLevel['vipLevel'] . ' to ' . $playerLevel['groupName'] . ' ' . $newVipLevel,
					// );
					// $this->player_manager->addPlayerLevelLog($data2);
				} else {
					$this->utils->debug_log('ignore upgrade', $player->username, $row['total_bet'], $playerLevel['upgradeAmount']);
				}
			} else {
				$this->utils->debug_log('ignore level upgrade', $playerLevel['vipsettingcashbackruleId'], $playerLevel['upgradeAmount']);
			}

			if ($playerLevel['downgradeAmount'] > 0) {

				// downgrade by deposit on group level
				$deposit = $this->transactions->getTotalDepositWithdrawalBonusCashbackByPlayers($player->playerId, $fromDatetime, $toDatetime);
				if ($deposit[0] <= $playerLevel['downgradeAmount'] && $playerLevel['vipLevel'] != '1') {
					$newVipLevelId = $this->getVipLevelIdByLevelNumber($playerLevel['vipSettingId'], $playerLevel['vipLevel'] - 1);
					if (!empty($newVipLevelId)) {
						$this->adjustPlayerLevel($row['player_id'], $newVipLevelId);
						$result[] = $this->utils->debug_log('downgraded', $player->username, $playerLevel['vipLevel'], 'to', $newVipLevelId);
					} else {
						$this->utils->error_log('downgrade failed', $player->username, $playerLevel['vipLevel'], 'to', $newVipLevelId);
					}
				} else {
					$this->utils->debug_log('ignore downgrade', $player->username, $deposit[0], $playerLevel['downgradeAmount']);
				}

				// downgrade by bet amount on group level
				if ($row['total_bet'] <= $playerLevel['downgradeAmount'] && $playerLevel['vipLevel'] != '1') {
					// Check downgrade amount if <= to total betting amount / Default Player Group does not have level downgrading
					// if ($playerLevel['vipLevel'] > $vipSettingDetails[0]['groupLevelCount']) {
					// $playerGroupId = $playerLevel['vipsettingcashbackruleId'] - 1;
					// $newVipLevel = $playerLevel['vipLevel'] - 1;
					// $newVipLevelId = $this->getVipLevelIdByLevelNumber($playerLevel['vipSettingId'], $playerLevel['vipLevel'] + 1);
					$newVipLevelId = $this->getVipLevelIdByLevelNumber($playerLevel['vipSettingId'], $playerLevel['vipLevel'] - 1);
					if (!empty($newVipLevelId)) {

						$this->adjustPlayerLevel($row['player_id'], $newVipLevelId);
						// $this->player_manager->changePlayerLevel($row['player_id'], $data1);

						$result[] = $this->utils->debug_log('downgraded', $player->username, $playerLevel['vipLevel'], 'to', $newVipLevelId);
					} else {

						$this->utils->error_log('downgrade failed', $player->username, $playerLevel['vipLevel'], 'to', $newVipLevelId);
					}

					// $data2 = array(
					// 	'player_level_id' => $playerLevel['playerlevelId'],
					// 	'player_Id' => $row['player_id'],
					// 	'compared_amount' => $playerLevel['downgradeAmount'],
					// 	'player_amount' => $row['total_betting_amount'],
					// 	'created_date' => date('Y-m-d H:i:s'),
					// 	'status' => 1,
					// 	'note' => 'Downgrade from ' . $playerLevel['groupName'] . ' ' . $playerLevel['vipLevel'] . ' to ' . $playerLevel['groupName'] . ' ' . $newVipLevel,
					// );
					// $this->player_manager->addPlayerLevelLog($data2);
					// }
				} else {
					$this->utils->debug_log('ignore downgrade', $player->username, $row['total_bet'], $playerLevel['downgradeAmount']);
				}
			} else {
				$this->utils->debug_log('ignore level downgrade', $playerLevel['vipsettingcashbackruleId'], $playerLevel['downgradeAmount']);
			}

			$succ = $this->endTransWithSucc();
			if (!$succ) {
				$this->utils->error_log('process up/down level failed', $player->username, 'total bet', $row['total_bet'], 'up', $playerLevel['upgradeAmount'], 'down', $playerLevel['downgradeAmount']);
			}
		}

		return $result;

	}

	/**
	 * For new Features of VIP
	 */
	public function batchUpDownLevelUpgrade($playerIds, $manual_batch = false, $check_hourly = false, $time_exec_begin = null) {
		if($check_hourly === true) {
			$this->utils->debug_log('batchUpDownLevelUpgrade trigger hourly cheack. Start time: ', date('Y-m-d H:i:s'));
		}
		$this->load->model(array('total_player_game_day', 'player_model', 'transactions'));

		// gets all player levels
		$allPlayerLevels = $this->getAllPlayerLevels();
		$levelMap = array();
		foreach ($allPlayerLevels as $lvl) {
			$levelMap[$lvl['vipsettingcashbackruleId']] = $lvl;
		}

		$result = array();

		$isBatchUpgrade = false;

		// batch upgrade
		$totalPlayerUpgrade = 0;
		if (is_array($playerIds)) {
			$isBatchUpgrade = true;
			if ($manual_batch) {
				$isBatchUpgrade = !$manual_batch;
			}
			foreach ($playerIds as $player) {
				$playerId = $player->playerId;
				$this->setGradeRecord(['start_time' => date('Y-m-d H:i:s')]);
				$result = $this->playerLevelAdjust($playerId, $levelMap, $isBatchUpgrade, $check_hourly, $time_exec_begin);
				if(isset($result['is_player_upgrade'])) {
					if ($result['is_player_upgrade']) {
						$totalPlayerUpgrade++;
					}
				}
			}
		} else {
			// manual upgrade
			$playerId = $playerIds;
			$this->setGradeRecord(['start_time' => date('Y-m-d H:i:s')]);
			$result = $this->playerLevelAdjust($playerId, $levelMap, $isBatchUpgrade, $check_hourly, $time_exec_begin);
			if(isset($result['is_player_upgrade'])) {
				if ($result['is_player_upgrade']) {
					$totalPlayerUpgrade++;
				}
			}
		}
		$result['totalPlayerUpgrade'] = $totalPlayerUpgrade;
		return $result;
	}

	#OGP-1568
	public function batchUpDownLevelDowngrade($playerIds, $manual_batch = false) {
		$this->load->model(array('total_player_game_day', 'player_model', 'transactions'));

		// gets all player levels
		$allPlayerLevels = $this->getAllPlayerLevels();
		$levelMap = array();
		foreach ($allPlayerLevels as $lvl) {
			$levelMap[$lvl['vipsettingcashbackruleId']] = $lvl;
		}

		$result = array();

		$isBatchDowngrade = false;

		// batch downgrade
		$totalPlayerDowngrade = 0;
		if(is_array($playerIds)) {
			$isBatchDowngrade = true;
			if ($manual_batch)
				$isBatchDowngrade = !$manual_batch;
			foreach($playerIds as $player) {
				$playerId = $player->playerId;
				$result = $this->playerLevelAdjustDowngrade($playerId, $levelMap, $isBatchDowngrade);

				if (isset($result['is_player_downgrade'])) {
					$totalPlayerDowngrade++;
				}
			}
			$this->utils->debug_log('Total player downgrade '.$totalPlayerDowngrade);
		} else {
			// manual downgrade
			$playerId = $playerIds;
			$result = $this->playerLevelAdjustDowngrade($playerId, $levelMap, $isBatchDowngrade);
			if (isset($result['is_player_downgrade'])) {
				$totalPlayerDowngrade++;
			}

			$this->utils->debug_log('manual downgrade player '.$playerId);
		}
		$result['totalPlayerDowngrade'] = $totalPlayerDowngrade;

		return $result;
	}

    public function getScheduleDateRange($schedule, $subNumber = 1, $isBatch, $setHourly = false, $time_exec_begin = null) {
        $dateRange = array();

        $fromDatetime = null;
        $toDatetime = null;

        // OGP-8276: Use time of job execution instead of execution time of method
       	$currentDate = empty($time_exec_begin) ? new DateTime() : new DateTime($time_exec_begin);
       	$fromDateTime = clone $currentDate;
        $toDateTime = clone $currentDate;
        // $fromDateTime = new DateTime();
        // $toDateTime = new DateTime();
		// $currentHour = $fromDateTime->format('H');
		$currentHour = $currentDate->format('H');

        $subEndNumber = 1;
        if ($subNumber > 1) {
        	$subEndNumber = 2;
        }

        // Scheduled daily
        $periodType = 'daily';
        if(isset($schedule['daily'])) {
	        $periodType = 'daily';
	        if($setHourly) {	//it means this vip level allows hourly upgrating
				if($currentHour == '00') {	//hour=00, use last period
					$fromDatetime = $fromDateTime->modify('-'.$subNumber.' day')->format('Y-m-d 00:00:00');
				}
				else {
					$fromDatetime = $fromDateTime->format('Y-m-d 00:00:00');
				}
			}
	        else {
				$fromDatetime = $fromDateTime->modify('-'.$subNumber.' day')->format('Y-m-d 00:00:00');
	        }
        	$toDatetime = $toDateTime->modify('-'.$subEndNumber.' day')->format('Y-m-d 23:59:59');
        // Scheduled weekly
        } else if(isset($schedule['weekly'])) {
	        $periodType = 'weekly';
        	$currentWeekNum = $currentDate->format('N');
        	if ($currentWeekNum == $schedule['weekly'] || ($isBatch == false)) {
				$fromDatetime = $fromDateTime->modify('-'.$subNumber.' week')->modify('this week monday')->format('Y-m-d 00:00:00');
				$toDatetime = $toDateTime->modify('-'.$subEndNumber.' week')->modify('this week sunday')->format('Y-m-d 23:59:59');
        	}
			if($setHourly) {
				// $fromDateTime = new DateTime();
				if( ($currentHour == '00') && ($currentWeekNum == '1') ) {	//week=1 (Monday) and hour=00, use last period
					$fromDatetime = $fromDateTime->modify('-'.$subNumber.' week')->modify('this week monday')->format('Y-m-d 00:00:00');
					$toDatetime = $toDateTime->modify('-'.$subEndNumber.' week')->modify('this week sunday')->format('Y-m-d 23:59:59');
				}
				else {
					$fromDatetime = $fromDateTime->modify('this week monday')->format('Y-m-d 00:00:00');
					$toDatetime = $toDateTime->modify('-'.$subEndNumber.' week')->modify('this week sunday')->format('Y-m-d 23:59:59');
				}
			}
		// Scheduled monthly
        } else if(isset($schedule['monthly'])) {
	        $periodType = 'monthly';
        	$currentDayNum = $currentDate->format('d');
        	if ((int)$currentDayNum == (int)$schedule['monthly'] || ($isBatch == false)) {
	        	$fromDatetime = $fromDateTime->modify('-'.$subNumber.' month')->modify('first day of this month')->format('Y-m-d 00:00:00');
	        	$toDatetime = $toDateTime->modify('-'.$subEndNumber.' month')->modify('last day of this month')->format('Y-m-d 23:59:59');
        	}
			if($setHourly) {
				// $fromDateTime = new DateTime();
				if( ($currentHour == '00') && ($currentDayNum == '1') ) {	//date=1 and hour=00, use last period
					$fromDatetime = $fromDateTime->modify('-'.$subNumber.' month')->modify('first day of this month')->format('Y-m-d 00:00:00');
					$toDatetime = $toDateTime->modify('-'.$subEndNumber.' month')->modify('last day of this month')->format('Y-m-d 23:59:59');
				}
				else {
					$fromDatetime = $fromDateTime->modify('first day of this month')->format('Y-m-d 00:00:00');
					// $fromDatetime = $fromDateTime->modify("-{$subNumber} month")->modify('first day of this month')->format('Y-m-d 00:00:00');
					$toDatetime = $toDateTime->modify('-'.$subEndNumber.' month')->modify('last day of this month')->format('Y-m-d 23:59:59');
				}
			}
		// Scheduled yearly
        } else if(isset($schedule['yearly'])) {
	        $periodType = 'yearly';
			$yearLySettingNum = sprintf('%02d', $yearly).'-01';
			$currentMonthDate = $currentDate->format('m-d');
            if ($currentMonthDate == $yearLySettingNum || ($isBatch == false)) {
                $fromDatetime = $fromDateTime->modify('-'.$subNumber.' year')->format('Y-01-01 00:00:00');
                $toDatetime = $toDateTime->modify('-'.$subEndNumber.' year')->format('Y-12-31 23:59:59');
            }
        }
        if(!empty($schedule)) {
            $dateRange = array( 'periodType' => $periodType, 'baseTime' => $currentDate, 'dateFrom' => $fromDatetime, 'dateTo' => $toDatetime);
        }

        return $dateRange;
    }

	public function playerLevelAdjust($playerId, $levelMap, $isBatchUpgrade, $checkHourly=false, $time_exec_begin = null) {
		$this->load->library(array('authentication', 'player_notification_library'));
		$this->load->model(array('total_player_game_day', 'player_model', 'transactions'));
		$this->setGradeRecord(['pgrm_start_time' => date('Y-m-d H:i:s')]);

		// display count of player who upgrade and downgrade
		$upgradeCount = 0;
		$isPlayerUpgrade = false;
		#OGP-1568
		//$downgradeCount = 0;

		$result = array();

		$player = $this->player_model->getPlayerById($playerId);
		$setHourly = false;

		// make sure vip upgrade exist
        if (!array_key_exists($player->levelId, $levelMap)){
            $result = array('error' => 'VIP Level is not exist');
            $this->utils->debug_log('upgrade VIP Level is not exist', $playerId);

            return $result;
        }

        $playerLevel = $levelMap[$player->levelId]; // get player level details

        $playerVIP = $playerLevel['groupName'] . ' - ' . $playerLevel['vipLevelName'];

        if (empty($playerLevel['period_up_down_2'])){
            $result = array('error' => 'Cannot upgrade. No schedule is set');
            $this->utils->debug_log('Cannot upgrade, No schedule is set', $playerId);

            return $result;
        }

        $schedule = json_decode($playerLevel['period_up_down_2'], true);
        $setHourly = isset($schedule['hourly']) ? $schedule['hourly'] : false;

        $isAccumulation = false;
        if (!empty($playerLevel['vip_upgrade_id'])) {
            $setting = $this->getSettingData($playerLevel['vip_upgrade_id']);
            if (!empty($setting)) {
                if ((int)$setting['accumulation'] == 1) {
                    $isAccumulation = true;
                }
            }
        }

        #OGP-1568
        // $upgradeSched = $this->getUpgradeSchedule($schedule);
        $upgradeSched = $this->getScheduleDateRange($schedule, 1, $isBatchUpgrade, $setHourly, $time_exec_begin);
        $this->utils->debug_log('---- upgrade schedule ---- params: playerId, isBatchUpgrade, setHourly, upgradeSched', $playerId, $isBatchUpgrade, $setHourly, $upgradeSched);
        if (empty($upgradeSched['dateFrom']) || empty($upgradeSched['dateTo'])) {
            $result = array('error' => 'Check schedule. Player upgrade still processing!');
            $this->utils->debug_log('Check schedule. Player upgrade still processing!', $playerId);

            return $result;
        }

        #GOP-1568
        if ($isAccumulation) {
            //from player register date to now
            $register_date = new DateTime($player->createdOn);
            // $now = new DateTime();
            $now = new DateTime($time_exec_begin);
            $fromDatetime = $register_date->format('Y-m-d H:i:s');
            $toDatetime = $now->format('Y-m-d H:i:s');
            $previousFromDatetime = $fromDatetime;
            $previousToDatetime = $toDatetime;
        } else {
            $fromDatetime = $upgradeSched['dateFrom'];
            $toDatetime = $upgradeSched['dateTo'];
            if($setHourly === true) {
                // $now = new DateTime();
                $now = new DateTime($time_exec_begin);
                $toDatetime = $now->format('Y-m-d H:i:s');
            }
            $periodType = $upgradeSched['periodType'];
            $previousSched = $this->getScheduleDateRange($schedule, $playerLevel['guaranteed_period_number'], $isBatchUpgrade, false, $time_exec_begin);
            $previousFromDatetime = $previousSched['dateFrom'];
            $previousToDatetime = $previousSched['dateTo'];
        }

        $this->utils->debug_log('-------- upgrade get deposit amount from to ------', $playerId, $fromDatetime, $toDatetime);
        $gameLogData = $this->total_player_game_day->getPlayerTotalBetWinLoss($playerId, $fromDatetime, $toDatetime);
        $transaction = $this->transactions->getTotalDepositWithdrawalBonusCashbackByPlayers($playerId, $fromDatetime, $toDatetime);

        $deposit = $transaction[0];

        #OGP-1568 get Guaranteed level deposit condition(month before last month)
        // $previousDeposit = 0;
        // if (!$isAccumulation) {
        // 	$this->utils->debug_log('-------- upgrade get previous deposit amount from to ------', $playerId, $previousFromDatetime, $previousToDatetime);
        // 	list($previousDeposit) = $this->transactions->getTotalDepositWithdrawalBonusCashbackByPlayers($playerId, $previousFromDatetime, $previousToDatetime);
        // }
        // $this->utils->debug_log('-------- upgrade deposit amount ---------', $playerId, $deposit, $previousDeposit);
        if (empty($gameLogData) && empty($deposit)) {
            $result = array('error' => 'No player deposit or bet data');
            $this->utils->debug_log('upgrade No player deposit or bet data', $playerId);

            return $result;
        }

        $vipSettingId = $playerLevel['vip_upgrade_id'];

        if (empty($vipSettingId)) {
            $result = array('error' => $playerVIP . ' upgrade setting is not set');
            $this->utils->debug_log('upgrade setting is not set', $playerId, $playerVIP);

            return $result;
        }

        $setting = $this->getSettingData($vipSettingId);

        if (empty($setting)) {
            $result = array('error' => $playerVIP . ' upgrade setting detail is not set');
            $this->utils->debug_log('upgrade setting detail is not set', $playerId, $playerVIP);

            return $result;

        }

        // VIP Multiple Upgrade
        $this->utils->debug_log('================playerLevelAdjust checkHourly flag', $checkHourly);
        $vips = $this->getAllVIPLevels($playerLevel['vipSettingId'], $playerLevel['vipLevel'], $checkHourly);
        $this->utils->debug_log('================playerLevelAdjust getAllVIPLevels as array', $vips);

        if(is_null($vips)) {
			$result = array('error' => 'Cannot get vips with parameters. vipSettingId: '.$playerLevel['vipSettingId']. ', vipLevel: '.$playerLevel['vipLevel'].', checkHourly:'. $checkHourly);
			$this->utils->debug_log('Cannot get vips with parameters. ', 'vipSettingId: '.$playerLevel['vipSettingId'], 'vipLevel: '.$playerLevel['vipLevel'], 'checkHourly:'. $checkHourly);

			return $result;
        }

        foreach ($vips as $vip) {
            if ($this->utils->isEnabledFeature('disable_player_multiple_upgrade')) {
                if ($upgradeCount >= 1) {
                    break;
                }
            }

            $formula = json_decode($vip['formula'], true);
            $this->utils->debug_log('-------- upgrade generateUpDownFormula-------', $playerId, $formula, $gameLogData['total_bet'], $deposit, $gameLogData['total_loss'], $gameLogData['total_win']);

            $execCheckConditionMet = true;

            if($checkHourly === true) {
                $period_up_down_2 = json_decode($vip['period_up_down_2'], true);
                if(!isset($period_up_down_2['hourly'])) {
                    $execCheckConditionMet = false;
                }
                else if($period_up_down_2['hourly'] != true) {
                    $execCheckConditionMet = false;
                }
            }
            $isConditionMet = $this->generateUpDownFormula($formula, $gameLogData['total_bet'], $deposit, $gameLogData['total_loss'], $gameLogData['total_win']);

            if($execCheckConditionMet === false) {
                $this->utils->debug_log('================directly set isConditionMet = false because this vip ip not using hourly check', $vip);
                $isConditionMet = false;
            }
            #OGP-1568 check Guaranteed level
            // if (!empty($playerLevel['guaranteed_period_total_deposit'])) {
            // 	if (((float)$deposit + (float)$previousDeposit) < (float)$playerLevel['guaranteed_period_total_deposit']) {
            // 		$this->utils->debug_log('upgrade guaranteed condition check : ', 'Player '.$playerId.' - '.$username, 'guaranteed deposit is not met : total '.$playerLevel['guaranteed_period_number'].' period of '.$periodType.' deposit ' . ($deposit + $previousDeposit) . ' is not met guaranteed deposit ' . $playerLevel['guaranteed_period_total_deposit'], 'deposit', $deposit, 'previousDeposit', $previousDeposit);
            // 		$isConditionMet = false;
            // 	}
            // }
            $this->utils->debug_log('upgrade isConditionMet', $playerId, $isConditionMet);

            //get current level promo cms id
            $VipLevel = $this->getVipLevel($vip['vipsettingcashbackruleId']);
            // Multiple upgrade
            $newVipLevelId = $this->getVipLevelIdByLevelNumber($vip['vipSettingId'], $vip['vipLevel'] + 1);
            $newVipData = (isset($levelMap[$newVipLevelId])) ? $levelMap[$newVipLevelId] : NULL;

            $this->utils->debug_log('================playerLevelAdjust adjustPlayerLevel origin vip id', $VipLevel);
            $this->utils->debug_log('================playerLevelAdjust adjustPlayerLevel new vip id', $newVipLevelId);

            if (!$isConditionMet) {
                if ($upgradeCount <= 0) {
                    $result = array('error' => 'Upgrade Condition not met');
                    $this->utils->debug_log('upgrade Condition not met', $playerId);
                }

                $success_grade = $this->utils->getConfig('get_only_success_grade_report');
                if (!$success_grade) {	# set status to GRADE_FAILED if not meet upgrade condition
                    $this->setGradeRecord([
                        'player_id'  => $playerId,
                        'level_from' => $vip['vipLevel'],
                        'level_to'   => !empty($newVipLevelId) ? $vip['vipLevel'] + 1 : 0 ,
                        'period_start_time' => $fromDatetime,
                        'period_end_time'   => $toDatetime,
                        'newvipId'                   => $newVipLevelId,
                        'vipsettingId'               => $vip['vipSettingId'],
                        'vipupgradesettingId'        => $playerLevel['vip_upgrade_id'],
                        'vipsettingcashbackruleId'   => $playerLevel['vipsettingcashbackruleId'],
                        'vipupgradesettinginfo'      => json_encode($setting),
                        'vipsettingcashbackruleinfo' => json_encode($playerLevel),
                        'pgrm_end_time' => date('Y-m-d H:i:s'),
                        'updated_by'    => $this->authentication->getUserId(),
                        'status'        => self::GRADE_FAILED
                    ]);
                    $this->gradeRecode();
                }

                return $result;
            }

            $this->utils->debug_log('upgrade newVipLevelId', $playerId, $newVipLevelId);
            if (empty($newVipLevelId)) {
                $result = array('error' => 'Upgrade Failed');
                $this->utils->debug_log('upgrade failed', $playerId);

                return $result;
            }

            $this->adjustPlayerLevel($playerId, $newVipLevelId);
            $upgradeCount++;
            $isPlayerUpgrade = true;
            $result = array('success' => 'Player level successfully upgrade');
            $this->utils->debug_log('upgrade Player level successfully upgrade', $playerId);
            $this->setGradeRecord([
                'player_id'  => $playerId,
                'level_from' => $vip['vipLevel'],
                'level_to'   => !empty($newVipLevelId) ? $vip['vipLevel'] + 1 : 0 ,
                'period_start_time' => $fromDatetime,
                'period_end_time'   => $toDatetime,
                'newvipId'                   => $newVipLevelId,
                'vipsettingId'               => $vip['vipSettingId'],
                'vipupgradesettingId'        => $playerLevel['vip_upgrade_id'],
                'vipsettingcashbackruleId'   => $playerLevel['vipsettingcashbackruleId'],
                'vipupgradesettinginfo'      => json_encode($setting),
                'vipsettingcashbackruleinfo' => json_encode($playerLevel),
                'pgrm_end_time' => date('Y-m-d H:i:s'),
                'updated_by'    => $this->authentication->getUserId(),
                'status'        => self::GRADE_SUCCESS
            ]);
            $this->gradeRecode();

            $this->CI->player_notification_library->success($playerId, Player_notification::SOURCE_TYPE_VIP_UPGRADE, [
                'player_notify_success_vip_upgrade_title',
                date('Y-m-d H:i:s'),
                $player->groupName,
                $player->levelName,
                ($newVipData) ? $newVipData['groupName'] : NULL,
                ($newVipData) ? $newVipData['vipLevelName'] : NULL,
                $this->CI->utils->getPlayerHistoryUrl('promoHistory')
            ], [
                'player_notify_success_vip_upgrade_message',
                date('Y-m-d H:i:s'),
                $player->groupName,
                $player->levelName,
                ($newVipData) ? $newVipData['groupName'] : NULL,
                ($newVipData) ? $newVipData['vipLevelName'] : NULL,
                $this->CI->utils->getPlayerHistoryUrl('promoHistory')
            ]);

            #OGP-1568 todo : upgrade bonus
            //if level up promo rule is set
            if (!empty($VipLevel['promo_cms_id'])) {
                $promo_cms_id = $VipLevel['promo_cms_id'];
                $this->utils->debug_log('upgrade Player have promo', $playerId, $promo_cms_id);
                $this->load->model(array('promorules', 'player_promo'));

                $promorule = $this->promorules->getPromoruleByPromoCms($promo_cms_id);
                $promorulesId = $promorule['promorulesId'];
                if (!empty($playerId) && !empty($promorule)) {
                    if($this->player_promo->isDeclinedForever($playerId, $promorulesId)){
                        $this->utils->debug_log('upgrade promo bonus declined forever', 'Sorry, promo application has been declined', $playerId);
                    } else {
                        $extra_info=null;
                        $this->utils->debug_log('upgrade request promo', $playerId, $promorule, $promo_cms_id, $extra_info);
                        $playerPromoId = $this->promorules->requestPromo($playerId, $promorule, 0, $promo_cms_id, null, null, true, null, 'player upgrade bonus', $extra_info, false);

                        #OGP-14055 add vip up and Downgrade level addPlayerPromoRequestBy
                        if(!empty($playerPromoId)){
							$adminUserId = $this->authentication->getUserId()?$this->authentication->getUserId():Users::SUPER_ADMIN_ID;
		                    $this->player_promo->addPlayerPromoRequestBy($playerPromoId, $adminUserId, null);
		                }

                        if ($promorule['bonusReleaseToPlayer'] == Promorules::BONUS_RELEASE_TO_PLAYER_AUTO) {
                            $this->utils->debug_log('upgrade approve promo', $playerId, $promorule, $promo_cms_id, $playerPromoId, $extra_info);
                            $this->promorules->approvePromo($playerId, $promorule, $promo_cms_id, 1, null, null, $playerPromoId, $extra_info, 'auto approve by auto upgrade', false);
                        }
                        // $lockedKey=null;
                        // $lock_it = $this->utils->lockResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
                        // $this->utils->debug_log('upgrade promo bonus 1','lock promo', $playerId, 'promorulesId', $promorulesId, $lock_it);
                        // $success = $lock_it;
                        // if ($lock_it) {
                        // 	try
                        // 	{
                        // 		list($success, $message) = $this->promorules->triggerPromotionFromManualPlayer($playerId, $promorule, $promo_cms_id, false, null, $extra_info);
                        // 		$this->utils->debug_log('upgrade promo bonus', $success, $message);
                        // 		$transSucc = $this->endTransWithSucc();
                        // 		$success = $success && $transSucc;
                        // 	}
                        // 	finally
                        // 	{
                        // 		$rlt = $this->releasePlayerBalanceResource($playerId, $lockedKey);
                        // 	}
                        // 	if($success && isset($extra_info['releaseToSubWallet']) && !empty($extra_info['releaseToSubWallet'])){
                        // 		$releaseToSubWallet=$extra_info['releaseToSubWallet'];
                        // 		$ignore_promotion_check=true;
                        // 		$this->utils->debug_log('upgrade promo bonus', 'releaseToSubWallet player:'.$playerId, $releaseToSubWallet);
                        // 		//transfer to subwallet
                        // 		$bonusResult=$this->utils->transferWallet($releaseToSubWallet['playerId'],
                        // 			$releaseToSubWallet['playerName'],
                        // 			$releaseToSubWallet['transfer_from'], $releaseToSubWallet['transfer_to'],
                        // 			$releaseToSubWallet['playerBonusAmount'], $releaseToSubWallet['adminId'],
                        // 			$releaseToSubWallet['walletType'], $releaseToSubWallet['originTransferAmount'],
                        // 			$ignore_promotion_check);

                        // 		//move transfer amount to real_for_bonus

                        // 		if(!$bonusResult['success']){
                        // 			$this->utils->error_log('upgrade promo bonus', 'transfer bonus failed', $bonusResult );
                        // 		}
                        // 	}
                        // }
                    }
                }
            } else {
                $this->utils->debug_log('upgrade promo bonus', 'no promo selected', $playerId);
            }
        }

        #OGP-1568
        // if ($setting['level_upgrade'] == self::UPGRADE_ONLY) {
        // 	//continue;
        // }

        // if ($setting['level_upgrade'] == self::UPGRADE_DOWNGRADE) {

        // 	// If condition is not met then downgrade
        // 	if (!$isConditionMet) {
        // 		$newVipLevelId = $this->getVipLevelIdByLevelNumber($playerLevel['vipSettingId'], $playerLevel['vipLevel'] - 1);

        // 		if (!empty($newVipLevelId)) {

        // 			// Get previous setting if condition not meet then downgrade
        // 			$isPreviousConditionMet = $this->getVIPUpgradeSetting($newVipLevelId,
        // 				$gameLogData['total_bet'], $deposit, $gameLogData['total_loss'], $gameLogData['total_win']);

        // 			if (!$isPreviousConditionMet) {
        // 				$this->adjustPlayerLevel($playerId, $newVipLevelId);
        // 				$result = array('error' => 'Player does not meet condition. Player level downgrade');
        // 				$downgradeCount++;
        // 			} else {
        // 				$result = array('success' => 'Keep Player Level');
        // 			}
        // 		} else {
        // 			$result = array('error' => 'Downgrade Failed');
        // 		}
        // 	}
        // }

		// if ($isBatchUpgrade) {
		$result['is_player_upgrade'] = $isPlayerUpgrade;
			#OGP-1568
			// $result['total_player_downgrade'] = $downgradeCount;
		// }

		return $result;
	}

	#OGP-1568
	public function playerLevelAdjustDowngrade($playerId, $levelMap, $isBatchDowngrade) {
		$this->load->library(array('authentication'));
		$this->load->model(array('total_player_game_day', 'player_model', 'transactions'));
		$this->setGradeRecord(['pgrm_start_time' => date('Y-m-d H:i:s')]);
		$downgradeCount = 0;
		$isPlayerDowngrade = false;

		$result = array();

		$player = $this->player_model->getPlayerById($playerId);
		$this->utils->debug_log('player level id', $player->levelId);
		// make sure vip upgrade exist
		if(array_key_exists($player->levelId, $levelMap) ) {
			$username = $player->username;

			$playerLevel = $levelMap[$player->levelId];		// get player level details

			$playerVIP = $playerLevel['groupName'].' - '.$playerLevel['vipLevelName'];

			if(!empty($playerLevel['period_down'])) {
				$schedule = json_decode($playerLevel['period_down'], true);

				$isAccumulation = false;
				if (!empty($playerLevel['vip_downgrade_id'])) {
					$setting = $this->getSettingData($playerLevel['vip_downgrade_id']);
					if (!empty($setting)) {
						if ((int)$setting['accumulation'] == 1) {
							$isAccumulation = true;
						}
					}
				}

				$upgradeSched = $this->getScheduleDateRange($schedule, 1, $isBatchDowngrade);

				if ($isAccumulation) {
					//from player register date to now
					$register_date = new DateTime($player->createdOn);
					$now = new DateTime();
					$fromDatetime = $register_date->format('Y-m-d H:i:s');
                    $toDatetime = $now->format('Y-m-d H:i:s');
                    $previousFromDatetime = $fromDatetime;
                    $previousToDatetime = $toDatetime;
				} else {
	                if (!empty($upgradeSched['dateFrom']) && !empty($upgradeSched['dateTo']) ) {
	                    $fromDatetime = $upgradeSched['dateFrom'];
	                    $toDatetime = $upgradeSched['dateTo'];
	                    $periodType = $upgradeSched['periodType'];
	                    $previousSched = $this->getScheduleDateRange($schedule, $playerLevel['guaranteed_downgrade_period_number'], $isBatchDowngrade);
	                    $previousFromDatetime = $previousSched['dateFrom'];
	                    $previousToDatetime = $previousSched['dateTo'];
	                } else {
						$this->utils->debug_log($playerVIP.' schedule is not set. player', $playerId, ' - ', $username);
	                    return array('error' => 'Check schedule. Player downgrade still processing!');
	                }
            	}

				$this->utils->debug_log('-------- downgrade get deposit amount from to ------', $playerId, $fromDatetime, $toDatetime);
				$gameLogData = $this->total_player_game_day->getPlayerTotalBetWinLoss($playerId, $fromDatetime, $toDatetime);

				$deposit = 0;
				list($deposit) = $this->transactions->getTotalDepositWithdrawalBonusCashbackByPlayers($playerId,$fromDatetime,$toDatetime);

				$previousDeposit = 0;
				if (!$isAccumulation) {
					$this->utils->debug_log('-------- downgrade get previous deposit amount from to ------', $playerId, $previousFromDatetime, $previousToDatetime);
					list($previousDeposit) = $this->transactions->getTotalDepositWithdrawalBonusCashbackByPlayers($playerId, $previousFromDatetime, $previousToDatetime);
				}

				$this->utils->debug_log('-------- downgrade deposit amount ---------', $playerId, $deposit, $previousDeposit);

				if(!empty($gameLogData) || !empty($deposit)) {

					$vipSettingId = $playerLevel['vip_downgrade_id'];
					if(!empty($vipSettingId)) {

						// as per leo for multiple upgrade don't consider schedule. just only the current player level setting
						$setting = $this->getSettingData($vipSettingId);
						if(!empty($setting)) {

							// VIP Multiple Upgrade
							$vips = $this->getAllVIPDowngradeLevels($playerLevel['vipSettingId'], $playerLevel['vipLevel']);

							$downgrade = 0;

							foreach($vips as $vip) {
								$formula = json_decode($vip['formula'], true);
								$this->utils->debug_log('-------- downgrade generateUpDownFormula-------', $playerId, $formula, $gameLogData['total_bet'], $deposit, $gameLogData['total_loss'], $gameLogData['total_win']);
								$isConditionMet = $this->generateUpDownFormula($formula, $gameLogData['total_bet'], $deposit, $gameLogData['total_loss'], $gameLogData['total_win']);
								//guaranteed condition check
								if (!empty($playerLevel['guaranteed_downgrade_period_total_deposit'])) {
									if (((float)$deposit + (float)$previousDeposit) < (float)$playerLevel['guaranteed_downgrade_period_total_deposit']) {
										$this->utils->debug_log('downgrade guaranteed condition check : ', 'vip formula', $vip['formula'], 'Player '.$playerId.' - '.$username, 'total '.$playerLevel['guaranteed_downgrade_period_number'].' period of '.$periodType.' deposit ' . ($deposit + $previousDeposit) . ' is not met guaranteed deposit ' . $playerLevel['guaranteed_downgrade_period_total_deposit'], 'deposit', $deposit, 'previousDeposit', $previousDeposit, 'downgrade player level');
										$isConditionMet = true;
									}
								}

								$this->utils->debug_log('downgrade isConditionMet', $playerId, $isConditionMet);

								//get current level promo cms id
								$VipLevel = $this->getVipLevel($vip['vipsettingcashbackruleId']);
								$newVipLevelId = $this->getVipLevelIdByLevelNumber($vip['vipSettingId'], $vip['vipLevel'] - 1);

								if($isConditionMet) {
									$downgrade++;
									if($vip['level_upgrade'] == self::DOWNGRADE_ONLY) {
										if($downgrade == 1 ) { // 1 single downgrade
											// If condition is not met then downgrade
											$this->utils->debug_log('downgrade', true, $vip['vipSettingId'], $vip['vipLevel'] - 1);
											$this->utils->debug_log('downgrade newVipLevelId', $playerId, $newVipLevelId);
											if (!empty($newVipLevelId)) {
												$this->adjustPlayerLevel($playerId, $newVipLevelId);
												$result = array('error' => 'Player does not meet condition. Player level downgrade');
												$this->utils->debug_log('downgrade Player level successfully downgrade', $playerId);
												$isPlayerDowngrade = true;
												$downgradeCount++;
												$this->setGradeRecord([
													'player_id'  => $playerId,
													'level_from' => $vip['vipLevel'],
													'level_to'   => !empty($newVipLevelId) ? $vip['vipLevel'] - 1 : 0 ,
													'period_start_time' => $fromDatetime,
													'period_end_time'   => $toDatetime,
													'newvipId'                   => $newVipLevelId,
													'vipsettingId'               => $vip['vipSettingId'],
													'vipupgradesettingId'        => $playerLevel['vip_downgrade_id'],
													'vipsettingcashbackruleId'   => $playerLevel['vipsettingcashbackruleId'],
													'vipupgradesettinginfo'      => json_encode($setting),
													'vipsettingcashbackruleinfo' => json_encode($playerLevel),
													'pgrm_end_time' => date('Y-m-d H:i:s'),
													'updated_by'    => $this->authentication->getUserId(),
													'status'        => self::GRADE_SUCCESS
												]);
												$this->gradeRecode();
												if (!empty($VipLevel['downgrade_promo_cms_id'])) {
													$downgrade_promo_cms_id = $VipLevel['downgrade_promo_cms_id'];
													$this->utils->debug_log('downgrade Player have promo', $playerId, $downgrade_promo_cms_id);
													$this->load->model(array('promorules', 'player_promo'));

													$promorule = $this->promorules->getPromoruleByPromoCms($downgrade_promo_cms_id);
													$promorulesId = $promorule['promorulesId'];
													if (!empty($playerId) && !empty($promorule)) {
														$this->utils->debug_log('playerId', $playerId, 'promorule', $promorule);
														if($this->player_promo->isDeclinedForever($playerId, $promorulesId)){
															$this->utils->debug_log('downgrade promo bonus declined forever', 'Sorry, promo application has been declined', $playerId);
														} else {
															$extra_info=null;
															$this->utils->debug_log('downgrade request promo', $playerId, $promorule, $downgrade_promo_cms_id, $extra_info);
															$playerPromoId = $this->promorules->requestPromo($playerId, $promorule, 0, $downgrade_promo_cms_id, null, null, true, null, 'player downgrade bonus', $extra_info, false);

															#OGP-14055 add vip up and Downgrade level addPlayerPromoRequestBy
									                        if(!empty($playerPromoId)){
																$adminUserId = $this->authentication->getUserId()?$this->authentication->getUserId():Users::SUPER_ADMIN_ID;
											                    $this->player_promo->addPlayerPromoRequestBy($playerPromoId, $adminUserId, null);
											                }

															if ($promorule['bonusReleaseToPlayer'] == Promorules::BONUS_RELEASE_TO_PLAYER_AUTO) {
																$this->utils->debug_log('downgrade approve promo', $playerId, $promorule, $downgrade_promo_cms_id, $playerPromoId, $extra_info);
																$this->promorules->approvePromo($playerId, $promorule, $downgrade_promo_cms_id, 1, null, null, $playerPromoId, $extra_info, 'auto approve by auto downgrade', false);
															}
															// $lockedKey=null;
															// $lock_it = $this->utils->lockResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
															// $this->utils->debug_log('downgrade promo bonus 1','lock promo', $playerId, 'promorulesId', $promorulesId, $lock_it);
															// $success = $lock_it;
															// if ($lock_it) {
															// 	try
															// 	{
															// 		list($success, $message) = $this->promorules->triggerPromotionFromManualPlayer($playerId, $promorule, $downgrade_promo_cms_id, false, null, $extra_info);
															// 		$this->utils->debug_log('downgrade promo bonus', $success, $message);
															// 		$transSucc = $this->endTransWithSucc();
															// 		$success = $success && $transSucc;
															// 	}
															// 	finally
															// 	{
															// 		$rlt = $this->releasePlayerBalanceResource($playerId, $lockedKey);
															// 	}
															// 	if($success && isset($extra_info['releaseToSubWallet']) && !empty($extra_info['releaseToSubWallet'])){
															// 		$releaseToSubWallet=$extra_info['releaseToSubWallet'];
															// 		$ignore_promotion_check=true;
															// 		$this->utils->debug_log('downgrade promo bonus', 'releaseToSubWallet player:'.$playerId, $releaseToSubWallet);
															// 		//transfer to subwallet
															// 		$bonusResult=$this->utils->transferWallet($releaseToSubWallet['playerId'],
															// 			$releaseToSubWallet['playerName'],
															// 			$releaseToSubWallet['transfer_from'], $releaseToSubWallet['transfer_to'],
															// 			$releaseToSubWallet['playerBonusAmount'], $releaseToSubWallet['adminId'],
															// 			$releaseToSubWallet['walletType'], $releaseToSubWallet['originTransferAmount'],
															// 			$ignore_promotion_check);

															// 		//move transfer amount to real_for_bonus

															// 		if(!$bonusResult['success']){
															// 			$this->utils->error_log('downgrade promo bonus', 'transfer bonus failed', $bonusResult );
															// 		}
															// 	}
															// }
														}
													}
												}
											} else {
												$result = array('error' => 'Downgrade Failed');
												$this->utils->debug_log('downgrade failed', $playerId);
											}
										}
									} else {
										$result = array('success' => 'Setting is not Downgrade Only, Keep Player Level ---'.$playerId);
										$this->utils->debug_log('Setting is not Downgrade Only, Keep Player Level ---', $playerId);
									}
								} else {
									$result = array('error' => 'Player Downgrade condition is not met, Keep Player Level');
									$this->utils->debug_log('Setting is not Downgrade Only, Keep Player Level ---', $playerId);

									$success_grade = $this->utils->getConfig('get_only_success_grade_report');
									if (!$success_grade) {    # set status to GRADE_FAILED if not meet upgrade condition
										$this->setGradeRecord([
												'player_id'  => $playerId,
												'level_from' => $vip['vipLevel'],
												'level_to'   => !empty($newVipLevelId) ? $vip['vipLevel'] - 1 : 0 ,
												'period_start_time' => $fromDatetime,
												'period_end_time'   => $toDatetime,
												'newvipId'                   => $newVipLevelId,
												'vipsettingId'               => $vip['vipSettingId'],
												'vipupgradesettingId'        => $playerLevel['vip_downgrade_id'],
												'vipsettingcashbackruleId'   => $playerLevel['vipsettingcashbackruleId'],
												'vipupgradesettinginfo'      => json_encode($setting),
												'vipsettingcashbackruleinfo' => json_encode($playerLevel),
												'pgrm_end_time' => date('Y-m-d H:i:s'),
												'updated_by'    => $this->authentication->getUserId(),
												'status'        => self::GRADE_FAILED
										]);
										$this->gradeRecode();
									}
									break;
								}
							}
						} else {
							$result = array('error' => $playerVIP.' downgrade setting is not set');
							$this->utils->debug_log($playerVIP.' downgrade setting is not set', $playerId);
						}
					} else {
						$result = array('error' => $playerVIP.' downgrade setting is not set');
						$this->utils->debug_log($playerVIP.' downgrade setting is not set. player', $playerId, ' - ', $username);
					}
				} else {
					$result = array('error' => 'No player deposit or bet data', $playerId);
					$this->utils->debug_log('No deposit or bet data', 'player '.$playerId.' - '.$username, $playerVIP );
				}
			} else {
				$result = array('error' => 'Cannot downgrade. No schedule is set');
				$this->utils->debug_log($playerVIP.' schedule is not set. player', $playerId, ' - ', $username);
			}
		} else {
			$result = array('error' => 'VIP Level is not exist', $playerId);
		}

		// if($isBatchDowngrade) {
		$result['is_player_downgrade'] = $isPlayerDowngrade;
		// }

		return $result;
	}

	public function getAllVIPLevels($vipSettingId, $vipLevelId, $checkHourly=false) {
		$this->db->select('vipsettingcashbackrule.vipSettingId');
		$this->db->select('vipsettingcashbackrule.vipLevel');
		$this->db->select('vipsettingcashbackrule.vipLevelName');
		$this->db->select('vipsettingcashbackrule.vipsettingcashbackruleId');
		$this->db->select('vipsettingcashbackrule.period_up_down_2');
		$this->db->select('vip_upgrade_setting.formula');
		$this->db->select('vip_upgrade_setting.level_upgrade');
		$this->db->from('vipsettingcashbackrule');
		$this->db->join('vip_upgrade_setting', 'vip_upgrade_setting.upgrade_id=vipsettingcashbackrule.vip_upgrade_id');
		$this->db->where('vipsettingcashbackrule.vipSettingId', $vipSettingId);
		$this->db->where('vipsettingcashbackrule.vipLevel >=', $vipLevelId);
		if($checkHourly===true) {
			$this->db->like('vipsettingcashbackrule.period_up_down_2', '"hourly":true}');
		}

		$setting = $this->runMultipleRowArray();

		return $setting;
	}

	public function getAllVIPDowngradeLevels($vipSettingId, $vipLevelId) {
		$this->db->select('vipsettingcashbackrule.vipSettingId');
		$this->db->select('vipsettingcashbackrule.vipLevel');
		$this->db->select('vipsettingcashbackrule.vipLevelName');
		$this->db->select('vipsettingcashbackrule.vipsettingcashbackruleId');
		$this->db->select('vip_upgrade_setting.formula');
		$this->db->select('vip_upgrade_setting.level_upgrade');
		$this->db->from('vipsettingcashbackrule');
		$this->db->join('vip_upgrade_setting', 'vip_upgrade_setting.upgrade_id=vipsettingcashbackrule.vip_downgrade_id');
		$this->db->where('vipsettingcashbackrule.vipSettingId', $vipSettingId);
		$this->db->where('vipsettingcashbackrule.vipLevel <=', $vipLevelId);
		$this->db->order_by('vipsettingcashbackrule.vipLevel', 'desc');

		$setting = $this->runMultipleRowArray();

		return $setting;
	}

	public function getVipLevel($upgradeSettingId) {
		$this->db->select('vipsettingcashbackrule.vipSettingId');
		$this->db->select('vipsettingcashbackrule.vipLevel');
		$this->db->select('vipsettingcashbackrule.vipLevelName');
		$this->db->select('vipsettingcashbackrule.promo_cms_id');
		$this->db->select('vipsettingcashbackrule.downgrade_promo_cms_id');
		$this->db->select('vipsettingcashbackrule.guaranteed_period_number');
		$this->db->select('vipsettingcashbackrule.guaranteed_period_total_deposit');
		$this->db->select('vipsettingcashbackrule.guaranteed_downgrade_period_number');
		$this->db->select('vipsettingcashbackrule.guaranteed_downgrade_period_total_deposit');
		$this->db->select('vip_upgrade_setting.formula');
		$this->db->select('vip_upgrade_setting.level_upgrade');
		$this->db->from('vipsettingcashbackrule');
		$this->db->join('vip_upgrade_setting', 'vip_upgrade_setting.upgrade_id=vipsettingcashbackrule.vip_upgrade_id or vip_upgrade_setting.upgrade_id=vipsettingcashbackrule.vip_downgrade_id');
		$this->db->where('vipsettingcashbackrule.vipsettingcashbackruleId', $upgradeSettingId);

		return $this->db->get()->row_array();
	}


	public function getPlayerBetAmtForNextLvl($playerId, $period, $accumulation = 0) {
		$schedule = json_decode($period, true);
		if(!empty($schedule)){
			list($fromDatetime, $toDatetime) = $this->getPlayerUpgradeScheduleRange(array_keys($schedule)[0]);
			if ((int)$accumulation == 1) {
				$this->load->model(['player_model']);
				$playerDetails = $this->player_model->getPlayerDetailsById($playerId);
				$now = new DateTime();
				$fromDatetime = $playerDetails->createdOn;
				$toDatetime = $now->format('Y-m-d H:i:s');
			}
			$gameLogData = $this->total_player_game_day->getPlayerTotalBetWinLoss($playerId, $fromDatetime, $toDatetime);
			return $gameLogData['total_bet'];
		}
		return null;
	}

	/*
		*	This will return date range for type of schedule
		*
	*/
	public function getPlayerUpgradeScheduleRange($type, $ofSpecificTime = null) {

		switch ($type) {
		case 'daily':
			$fromDatetime = date('Y-m-d 00:00:00');
			$toDatetime = date('Y-m-d 23:59:59');
			break;

		case 'weekly':
			$fromDatetime = date("Y-m-d 00:00:00", strtotime("last week monday"));
			$toDatetime = date("Y-m-d 23:59:59", strtotime("last week sunday"));
			break;

		case 'monthly':
			$fromDatetime = date("Y-m-d 00:00:00", strtotime("first day of previous month"));
			$toDatetime = date("Y-m-d 23:59:59", strtotime("last day of previous month"));
			break;

		case 'yearly':
			$fromDatetime = date("Y-01-01 00:00:00", strtotime("-1 year"));
			$toDatetime = date("Y-12-31 23:59:59", strtotime("-1 year"));
			break;
		}
		return array($fromDatetime, $toDatetime);
	}

	public function getUpgradeSchedule($schedule, $ignoreCurrentDate = null) {

		$dateRange = array();

		$fromDatetime = null;
		$toDatetime = null;
		$sched = '';

		if (isset($schedule['daily'])) {

			$fromDatetime = date('Y-m-d 00:00:00');
			$toDatetime = date('Y-m-d 23:59:59');
			$sched = 'daily';

		} else if (isset($schedule['weekly']) ) {

			// get last week if day setting is set today
			$currentDay = date('l');
			$currentDayNum = date('N', strtotime($currentDay));
			if ($currentDayNum == $schedule['weekly'] || ($ignoreCurrentDate == true)) {
				$fromDatetime = date("Y-m-d 00:00:00", strtotime("last week monday"));
				$toDatetime = date("Y-m-d 23:59:59", strtotime("last week sunday"));
				if ($ignoreCurrentDate) {
					$fromDatetime = date('Y-m-d 00:00:00', strtotime('this week monday'));
					$toDatetime = date("Y-m-d 23:59:59", strtotime("this week sunday"));
				}
			}
			$sched = 'weekly';

		} else if (isset($schedule['monthly']) ) {
			// check if current day of month is same as setting
			$currentDayNum = date('d');
			if ($currentDayNum == $schedule['monthly'] || ($ignoreCurrentDate == true)) {
				$fromDatetime = date("Y-m-d 00:00:00", strtotime("first day of previous month"));
				$toDatetime = date("Y-m-d 23:59:59", strtotime("last day of previous month"));
				if ($ignoreCurrentDate) {
					$fromDatetime = date('Y-m-d 00:00:00', strtotime('first day of this month'));
					$toDatetime = date("Y-m-d 23:59:59", strtotime("last day of this month"));
				}
			}
			$sched = 'monthly';

		} else if (isset($schedule['yearly'])  ) {
			// check if current date is first day of month
			$yearLySettingNum = sprintf('%02d', $schedule['yearly']) . '-01';
			$currentMonthDate = date('m-d');
			if ($currentMonthDate == $yearLySettingNum || ($ignoreCurrentDate == true)) {
				$fromDatetime = date("Y-01-01 00:00:00", strtotime("-1 year"));
				$toDatetime = date("Y-12-31 23:59:59", strtotime("-1 year"));
				if ($ignoreCurrentDate) {
					$fromDatetime = date('Y-01-01 00:00:00');
					$toDatetime = date("Y-12-31 23:59:59");
				}
			}
			$sched = 'yearly';
		}

		if (!empty($schedule)) {
			$dateRange = array('dateFrom' => $fromDatetime, 'dateTo' => $toDatetime, 'sched' => $sched);
		}
		return $dateRange;
	}

	public function getVIPUpgradeSetting($upgradeSettingId, $bet, $deposit, $loss, $win) {

		$this->db->select('vip_upgrade_setting.formula');
		$this->db->from('vipsettingcashbackrule');
		$this->db->join('vip_upgrade_setting', 'vip_upgrade_setting.upgrade_id=vipsettingcashbackrule.vip_upgrade_id');
		$this->db->where('vipsettingcashbackrule.vipsettingcashbackruleId', $upgradeSettingId);

		$setting = $this->db->get()->row_array();

		if (!empty($setting['formula'])) {
			$formula = json_decode($setting['formula'], true);
			return $this->generateUpDownFormula($formula, $bet, $deposit, $loss, $win);
		} else {
			return true; // don't downgrade if setting is empty
		}
	}

	/**
	 * Generate formula
	 *
	 * return boolean
	 */
	public function generateUpDownFormula($formula, $bet, $deposit, $loss, $win) {
		$result = $logs = '';
		$option = 0;

		foreach ($formula as $key => $val) {
			if (is_array($val)) {
				if ($key == 'bet_amount') {
					$option = $bet;
				} elseif ($key == 'deposit_amount') {
					$option = $deposit;
				} elseif ($key == 'loss_amount') {
					$option = $loss;
				} elseif ($key == 'win_amount') {
					$option = $win;
				}
				$conjunction = $val[0];
				$amount = $val[1];
				$result .= $option . ' ' . $conjunction . ' ' . $amount;
				$logs .= $key . ' ' . $conjunction . ' ' . $amount;
			} else {
				if (strpos($key, 'operator') !== false) {
					$result .= ' ' . $val . ' ';
					$logs .= ' ' . $val . ' ';
				}
			}
		}

		// $this->utils->debug_log('formula for vip is', $logs);

		$condition = "return " . $result . ";"; //	make conditional statement out of string

		$this->setGradeRecord([
			'remark' => json_encode([
				'rule' => $logs,
				'rlt'  => $result,
			])
		]);

		return eval($condition) ? true : false;
	}

	/**
	 * detail: check if player is can withdraw once
	 *
	 * @param int $playerId
	 * @return array
	 */
	public function isOneWithdrawOnly($playerId) {
		$this->db->select('vipsettingcashbackrule.one_withdraw_only')->from($this->levelTable)
			->join('player', 'player.levelId=vipsettingcashbackrule.vipsettingcashbackruleId')
			->where('player.playerId', $playerId);

		return $this->runOneRowOneField('one_withdraw_only');

	}

	/**
	 * Check if a vip class/group (specified by $vipsettingcashbackruleId) is allowed to perform cashback
	 *
	 * @param	int		$vipsettingcashbackruleId	self-explanatory
	 * @return	bool
	 */
	public function isAllowedCashback($vipsettingcashbackruleId) {
		$this->db
			->select('vipsettingcashbackrule.can_cashback')
			->from($this->levelTable)
			->where('vipsettingcashbackruleId', $vipsettingcashbackruleId);

		// return $this->runOneRowOneField('can_cashback');
		$raw = $this->runOneRowOneField('can_cashback');

		// As can_cashback is defined enum, type of $raw will be string
		// Convert the return to bool here
		$ret = $raw == 'true' ? true : false;

		return $ret;
	}

	/**
	 * detail: get vip setting game type and description
	 *
	 *
	 * @see MOVE TO getCashbackPercentage
	 *
	 * @param int $vipsettingcashbackruleId
	 * @return array
	 */
	public function getVipSettingsGamesTypeAndGameDesc($vipsettingcashbackruleId) {
		$this->db->select('game_description.game_platform_id,game_description.game_type_id, game_description.id as game_description_id, vipsetting_cashback_game.percentage, vipsetting_cashback_game.game_platform_percentage, vipsetting_cashback_game.game_type_percentage, vipsetting_cashback_game.game_desc_percentage')
			->from('vipsetting_cashback_game')
			->join('game_description', 'game_description.id = vipsetting_cashback_game.game_description_id', 'left')
			->where('vipsetting_cashback_game.vipsetting_cashbackrule_id', $vipsettingcashbackruleId);
		// $this->db->group_by('game_description.game_type_id');
		$rows = $this->runMultipleRowArray();

		$gamePlatformList = array();
		$gameTypeList = array();
		$gameDescList = array();

		if (!empty($rows)) {
			foreach ($rows as $row) {
				if (!empty($row['game_platform_percentage'])) {
					$gamePlatformList[$row['game_platform_id']] = $row['game_platform_percentage'];
				}
				if (!empty($row['game_type_percentage'])) {
					$gameTypeList[$row['game_type_id']] = $row['game_type_percentage'];
				}
				if (!empty($row['game_desc_percentage'])) {
					$gameDescList[$row['game_description_id']] = $row['game_desc_percentage'];
				}
			}

			// $gamePlatformList = array_unique($gamePlatformList);
			// $gameTypeList = array_unique($gameTypeList);
			// $gameDescList = array_unique($gameDescList);
		}

		return array($gamePlatformList, $gameTypeList, $gameDescList);
	}

	/**
	 * detail: get game tree for a certain group level
	 *
	 * @param int $vipgrouplevelId
	 * @return void
	 */
	public function getGameTreeForGroupLevel($vipgrouplevelId, $filterColumn=array()) {
		$this->load->model(array('game_description_model'));

		list($gamePlatformList, $gameTypeList, $gameDescList) = $this->getCashbackPercentageMap($vipgrouplevelId);

		$showGameDescTree = $this->config->item('show_particular_game_in_tree');

		return $this->game_description_model->getGameTreeArray($gamePlatformList, $gameTypeList, $gameDescList, true, $showGameDescTree, $filterColumn);

	}

	public function getGameTreeForGroupLevel2($vipgrouplevelId) {
		$this->load->model(array('game_description_model'));

		list($gamePlatformList, $gameTypeList, $gameDescList) = $this->getCashbackPercentageMap($vipgrouplevelId);

		$showGameDescTree = $this->config->item('show_particular_game_in_tree');

		return $this->game_description_model->getGameTreeArray2($gamePlatformList, $gameTypeList, $gameDescList, true, $showGameDescTree);

	}

	/**
	 * detail: tree for agency game rolling comm setting
	 *
	 * @param int $agent_id
	 * @return array
	 */
	public function get_agency_game_rolling_comm_and_desc($agent_id) {
		$this->db->select('game_description.game_platform_id,game_description.game_type_id, game_description.id as game_description_id, agency_game_rolling_comm.game_platform_percentage, agency_game_rolling_comm.game_type_percentage, agency_game_rolling_comm.game_desc_percentage')
			->from('agency_game_rolling_comm')
			->join('game_description', 'game_description.id = agency_game_rolling_comm.game_description_id', 'left')
			->where('agency_game_rolling_comm.agent_id', $agent_id);
		// $this->db->group_by('game_description.game_type_id');
		$rows = $this->runMultipleRowArray();

		$gamePlatformList = array();
		$gameTypeList = array();
		$gameDescList = array();

		if (!empty($rows)) {
			foreach ($rows as $row) {
				$gamePlatformList[$row['game_platform_id']] = $row['game_platform_percentage'];
				$gameTypeList[$row['game_type_id']] = $row['game_type_percentage'];
				$gameDescList[$row['game_description_id']] = $row['game_desc_percentage'];
			}

			// $gamePlatformList = array_unique($gamePlatformList);
			// $gameTypeList = array_unique($gameTypeList);
			// $gameDescList = array_unique($gameDescList);
		}

		return array($gamePlatformList, $gameTypeList, $gameDescList);
	}

	/**
	 * detail: get agency game rolling comm tree
	 *
	 * @param int $agent_id
	 * @return void
	 */
	public function get_agency_game_rolling_comm_tree($agent_id) {
		$this->load->model(array('game_description_model'));

		list($gamePlatformList, $gameTypeList, $gameDescList) = $this->get_agency_game_rolling_comm_and_desc($agent_id);

		$showGameDescTree = $this->config->item('show_particular_game_in_tree');

		return $this->game_description_model->getGameTreeArray($gamePlatformList, $gameTypeList, $gameDescList, true, $showGameDescTree);

	}

	/**
	 * detail: get bet for a certain player base on the date range
	 *
	 * @param string $date
	 * @param string $startHour
	 * @param string $endHour
	 * @param int $playerId
	 *
	 * @return array
	 */
	public function getPlayerBetByDate($date, $startHour, $endHour, $playerId = null, $start_date = null, $end_date = null, $playerIds = null, $exclude_game_platform_list = NULL) {
        $this->load->model(array('game_logs'));

        if (!empty($start_date) && !empty($end_date)) {
            $lastDate = str_replace('-', '', $start_date);
            $date = str_replace('-', '', $end_date);
        } else {
            $lastDate = $this->utils->getLastDay($date);

            if (intval($endHour) == 23) {
                //all yesterday
                $date = $lastDate;
            }

            $date = str_replace('-', '', $date);
            $lastDate = str_replace('-', '', $lastDate);
        }
        $this->utils->debug_log(__METHOD__ . '(): start', $lastDate . $startHour, 'end', $date . $endHour);

		$playerQry = '';

		if (!empty($playerIds)) {
			$ids = implode(',', $playerIds);
			$playerQry .= ' and player_id IN ('.$ids.')';
		} else {
			if ($playerId) {
				$playerId = intval($playerId);
				$playerQry .= ' and player_id=' . $playerId;
			}
		}

        $bind_query = '';
		$bind_values = [];
		// $bind_values = array($lastDate . $startHour, $date . $endHour);

		// cashback_start_hour to cashback_end_hour
		if (!$this->utils->getConfig('use_total_hour')) {
            if(!empty($exclude_game_platform_list)){
                $bind_query .= " and (";
                $bind_query .= "(tpgh.game_platform_id NOT IN (".implode(',', $exclude_game_platform_list).") and (date_format(tpgh.end_at, '%Y%m%d%H')>=? and date_format(tpgh.end_at, '%Y%m%d%H')<=?))";
                $bind_values[] = $lastDate . $startHour;
                $bind_values[] = $date . $endHour;

                $bind_query .= ")";
            }else{
                $bind_query .= " and (date_format(tpgh.end_at, '%Y%m%d%H')>=? and date_format(tpgh.end_at, '%Y%m%d%H')<=?)";
                $bind_values[] = $lastDate . $startHour;
                $bind_values[] = $date . $endHour;
            }

			$flag = Game_logs::FLAG_GAME;
			$sql = <<<EOD
SELECT tpgh.player_id, player.levelId, sum(tpgh.bet_amount) as betting_total, tpgh.game_description_id, tpgh.game_type_id, tpgh.game_platform_id
  FROM game_logs as tpgh
  join game_description as gd on tpgh.game_description_id=gd.id and gd.no_cash_back!=1
  join player on player.playerId=tpgh.player_id
where flag=$flag
and player.disabled_cashback!=1
{$playerQry}
{$bind_query}
group by tpgh.player_id, tpgh.game_description_id, tpgh.game_platform_id
EOD;
		} else {
            if(!empty($exclude_game_platform_list)){
                $bind_query .= " and (";
                $bind_query .= "(tpgh.game_platform_id NOT IN (".implode(',', $exclude_game_platform_list).") and (date_hour>=? and date_hour<=?))";
                $bind_values[] = $lastDate . $startHour;
                $bind_values[] = $date . $endHour;

                $bind_query .= ")";
            }else{
                $bind_query .= " and (date_hour>=? and date_hour<=?)";
                $bind_values[] = $lastDate . $startHour;
                $bind_values[] = $date . $endHour;
            }

			//use total_player_game_hour
			$sql = <<<EOD
SELECT tpgh.player_id, player.levelId, sum(tpgh.betting_amount) as betting_total, tpgh.game_description_id, tpgh.game_type_id, tpgh.game_platform_id
  FROM total_player_game_hour as tpgh
  join game_description as gd on tpgh.game_description_id=gd.id and gd.no_cash_back!=1
  join player on player.playerId=tpgh.player_id
where player.disabled_cashback!=1
{$playerQry}
{$bind_query}
group by tpgh.player_id, tpgh.game_description_id, tpgh.game_platform_id
EOD;
		}

		$qry = $this->db->query($sql, $bind_values);

		// $this->utils->printLastSQL();

		return $this->getMultipleRow($qry);
	}

    public function getPlayerBetBySettledDate($date, $startHour, $endHour, $playerId = null, $start_date = null, $end_date = null, $playerIds = null, $include_game_platform_list = NULL) {
        $this->load->model(array('game_logs'));

        if (!empty($start_date) && !empty($end_date)) {
            $lastDate = str_replace('-', '', $start_date);
            $date = str_replace('-', '', $end_date);
        } else {
            $lastDate = $this->utils->getLastDay($date);

            if (intval($endHour) == 23) {
                //all yesterday
                $date = $lastDate;
            }

            $date = str_replace('-', '', $date);
            $lastDate = str_replace('-', '', $lastDate);
        }
        $this->utils->debug_log(__METHOD__ . '(): start', $lastDate . $startHour, 'end', $date . $endHour);

        $playerQry = '';

        if (!empty($playerIds)) {
            $ids = implode(',', $playerIds);
            $playerQry .= ' and player_id IN ('.$ids.')';
        } else {
            if ($playerId) {
                $playerId = intval($playerId);
                $playerQry .= ' and player_id=' . $playerId;
            }
        }

        $bind_query = '';
        $bind_values = [];
        // $bind_values = array($lastDate . $startHour, $date . $endHour);

        // cashback_start_hour to cashback_end_hour
        if(empty($include_game_platform_list)){
            return NULL;
        }

        $bind_query .= " and (";

        $bind_query .= "(tpgh.game_platform_id IN (".implode(',', $include_game_platform_list).") and (date_format(tpgh.updated_at, '%Y%m%d%H')>=? and date_format(tpgh.updated_at, '%Y%m%d%H')<=?))";
        $bind_values[] = $lastDate . $startHour;
        $bind_values[] = $date . $endHour;

        $bind_query .= ")";

        $flag = Game_logs::FLAG_GAME;
        $sql = <<<EOD
SELECT tpgh.player_id, player.levelId, SUM(CASE WHEN tpgh.is_parlay = 1 THEN tpgh.bet_amount ELSE ABS(tpgh.result_amount) END) as betting_total, tpgh.game_description_id, tpgh.game_type_id, tpgh.game_platform_id, tpgh.is_parlay
  FROM game_logs as tpgh
  join game_description as gd on tpgh.game_description_id=gd.id and gd.no_cash_back!=1
  join player on player.playerId=tpgh.player_id
where flag=$flag
and player.disabled_cashback!=1
and tpgh.result_amount <> 0
{$playerQry}
{$bind_query}
group by tpgh.player_id, tpgh.game_description_id, tpgh.game_platform_id, tpgh.is_parlay
EOD;

        $qry = $this->db->query($sql, $bind_values);

        // $this->utils->printLastSQL();

        return $this->getMultipleRow($qry);
    }

	/**
	 * detail: get bet for a certain player base on the date range
	 *
	 * @param string $date
	 * @param string $startHour
	 * @param string $endHour
	 * @param int $playerId
	 *
	 * @return array
	 */
	public function sumPlayerBetByDate($date, $startHour, $endHour, $playerId = null, $start_date = null, $end_date = null, $playerIds = null) {
        $this->load->model(array('game_logs'));

        if (!empty($start_date) && !empty($end_date)) {
            $lastDate = str_replace('-', '', $start_date);
            $date = str_replace('-', '', $end_date);
        } else {
            $lastDate = $this->utils->getLastDay($date);

            if (intval($endHour) == 23) {
                //all yesterday
                $date = $lastDate;
            }
            $date = str_replace('-', '', $date);
            $lastDate = str_replace('-', '', $lastDate);
        }
        $this->utils->debug_log(__METHOD__ . '(): start', $lastDate . $startHour, 'end', $date . $endHour);

		$playerQry = '';
		if (!empty($playerIds)) {
			$ids = implode(',', $playerIds);
			$playerQry .= ' and player_id IN ('.$ids.')';
		} else {
			if ($playerId) {
				$playerId = intval($playerId);
				$playerQry .= ' and player_id=' . $playerId;
			}
		}

        $bind_query = '';
        $bind_values = [];

		// cashback_start_hour to cashback_end_hour
		if (!$this->utils->getConfig('use_total_hour')) {
            $bind_query .= " and (date_format(tpgh.end_at, '%Y%m%d%H')>=? and date_format(tpgh.end_at, '%Y%m%d%H')<=?)";
            $bind_values[] = $lastDate . $startHour;
            $bind_values[] = $date . $endHour;

			$flag = Game_logs::FLAG_GAME;
			$sql = <<<EOD
SELECT tpgh.player_id, sum(tpgh.bet_amount) as betting_total
  FROM game_logs as tpgh
  join game_description as gd on tpgh.game_description_id=gd.id and gd.no_cash_back!=1
  join player on player.playerId=tpgh.player_id
where flag=$flag
and player.disabled_cashback!=1
{$playerQry}
{$bind_query}
group by tpgh.player_id
EOD;
		} else {
            $bind_query .= " and (date_hour>=? and date_hour<=?)";
            $bind_values[] = $lastDate . $startHour;
            $bind_values[] = $date . $endHour;

            //use total_player_game_hour
			$sql = <<<EOD
SELECT tpgh.player_id, sum(tpgh.betting_amount) as betting_total
  FROM total_player_game_hour as tpgh
  join game_description as gd on tpgh.game_description_id=gd.id and gd.no_cash_back!=1
  join player on player.playerId=tpgh.player_id
where player.disabled_cashback!=1
{$playerQry}
{$bind_query}
group by tpgh.player_id
EOD;
		}
		// $this->utils->debug_log($sql);

// 		$sql = <<<EOD
		// SELECT tpgh.player_id, sum(tpgh.betting_amount) as betting_total, tpgh.game_description_id, tpgh.game_platform_id
		//   FROM total_player_game_hour as tpgh
		//   join game_description as gd on tpgh.game_description_id=gd.id and gd.no_cash_back!=1
		// where concat(tpgh.date, lpad(tpgh.hour,2,'0'))>=? and concat(tpgh.date, lpad(tpgh.hour,2,'0'))<=?
		// group by tpgh.player_id, tpgh.game_description_id, tpgh.game_platform_id
		// EOD;

		// $startHour = $this->config->item('cashback_start_hour');
		// $endHour = $this->config->item('cashback_end_hour');
		$qry = $this->db->query($sql, $bind_values);

		// $this->utils->printLastSQL();

		$map = [];
		$rows = $this->getMultipleRow($qry);
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$player_id = $row->player_id;
				$betting_total = $row->betting_total;
				$map[$player_id] = $betting_total;
			}
		}

		return $map;

	}

	public function calculatePlayerBetTotal($player_bet_by_date, $player_bet_by_settled_date){
	    $map = [];

        if(!empty($player_bet_by_date)){
            foreach($player_bet_by_date as $pbbd){
                $player_id = $pbbd->player_id;
                $betting_total = $pbbd->betting_total;

                $map[$player_id] = (isset($map[$player_id])) ? $map[$player_id] + $betting_total : $betting_total;
            }
        }

        if(!empty($player_bet_by_settled_date)){
            foreach($player_bet_by_settled_date as $pbbd){
                $player_id = $pbbd->player_id;
                $betting_total = $pbbd->betting_total;

                $map[$player_id] = (isset($map[$player_id])) ? $map[$player_id] + $betting_total : $betting_total;
            }
        }

	    return $map;
    }

	/**
	 * detail: get bet for a certain player base on the date range
	 *
	 * @param string $timeStart
	 * @param string $timeEnd
	 * @param int $playerId
	 *
	 * @return array
	 */
	public function sumPlayerBetByTime($timeStart, $timeEnd, $playerId = null) {
		$playerQry = '';
		if ($playerId) {
			$playerId = intval($playerId);
			$playerQry .= ' and player_id=' . $playerId;
		}

		$this->load->model(array('game_logs'));

		//disabled_cashback will disable player cashback
		// cashback_start_hour to cashback_end_hour
		$flag = Game_logs::FLAG_GAME;
		$sql = <<<EOD
			SELECT tpgh.player_id, sum(tpgh.bet_amount) AS betting_total
			FROM game_logs AS tpgh
				JOIN game_description AS gd
					ON tpgh.game_description_id=gd.id AND gd.no_cash_back!=1
				JOIN player
					ON player.playerId=tpgh.player_id
			WHERE tpgh.end_at>=?
				AND tpgh.end_at<=?
				AND flag={$flag}
				AND player.disabled_cashback!=1
				{$playerQry}
			GROUP BY tpgh.player_id
EOD;

		$qry = $this->db->query($sql, array($timeStart, $timeEnd));

		// $this->utils->printLastSQL();

		$map = [];
		$rows = $this->getMultipleRow($qry);
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$player_id = $row->player_id;
				$betting_total = $row->betting_total;
				$map[$player_id] = $betting_total;
			}
		}

		return $map;

	}

	/**
	 * search percentage from commonrules
	 *
	 * order:
	 * 1. search game rules
	 * 2. if empty, get common rule
	 *
	 * @param  int $player_id
	 * @param  int $game_description_id
	 * @param  double $playerSumBetAmount
	 * @param  array [rule id => [min_bet_amount, max_bet_amount,default_percentage, game_rules=>[game_description_id=>game_rule]]]
	 * @return ['cashback_percentage'=>0, 'cashback_maxbonus'=>null, 'id'=>0, 'level_id'=>0]
	 */
	public function getPercentageByCommonRules($player_id, $game_description_id, $playerSumBetAmount, $commonRules) {
		$rateRow = (object) ['cashback_percentage' => 0, 'cashback_maxbonus' => null, 'id' => 0, 'level_id' => 0];

		$settings = $commonRules['settings'];
		$rules = $commonRules['rules'];

		$rateRow->cashback_maxbonus = @$settings['max_cashback_amount'];

		if (!empty($rules) && !empty($playerSumBetAmount)) {
			foreach ($rules as $ruleId => $ruleContent) {
				$min_bet_amount = $ruleContent['min_bet_amount'];
				$max_bet_amount = $ruleContent['max_bet_amount'];
				if ($playerSumBetAmount >= $min_bet_amount && $playerSumBetAmount < $max_bet_amount) {
					//use this
					$this->utils->debug_log('try search cashback rule', $ruleId, $playerSumBetAmount, $min_bet_amount, $max_bet_amount);
					//order is game_desc_percentage > game_type_percentage > game_platform_percentage > default_percentage
					$cashback_percentage = $ruleContent['default_percentage'];
					$found = false;
					if (!empty($ruleContent['game_rules'])) {
						if (isset($ruleContent['game_rules'][$game_description_id])) {
							$gameRuleRow = $ruleContent['game_rules'][$game_description_id];
							if (!empty($gameRuleRow)) {
								//order by
								if (!empty($gameRuleRow['game_desc_percentage'])) {
									$cashback_percentage = $gameRuleRow['game_desc_percentage'];
								} elseif (!empty($gameRuleRow['game_type_percentage'])) {
									$cashback_percentage = $gameRuleRow['game_type_percentage'];
								} elseif (!empty($gameRuleRow['game_platform_percentage'])) {
									$cashback_percentage = $gameRuleRow['game_platform_percentage'];
								}
								$found = true;
								$this->utils->debug_log('found cashback rule', $gameRuleRow);
							}
						}
					}
					if ($found) {
						$rateRow->cashback_percentage = $cashback_percentage;
						$rateRow->cashback_maxbonus = @$settings['max_cashback_amount'];
						break;
					}
				}
			}
		}
		return $rateRow;
	}

	/**
	 *
	 * @return array rule id => game rules
	 */
	public function getCommonCashbackRules() {
		$commonRules = ['settings' => (array) $this->getCashbackSettings()];
		$map = [];
		//load from cashback common rules
		$this->db->from('common_cashback_rules')->where('status', self::STATUS_NORMAL);
		$rows = $this->runMultipleRowArray();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$rule_id = $row['id'];
				$this->db->from('common_cashback_game_rules')->where('rule_id', $row['id']);
				$gameRules = $this->runMultipleRowArray();

				$gameRulesMap = [];
				if (!empty($gameRules)) {
					foreach ($gameRules as $gameRuleRow) {
						$gameRulesMap[$gameRuleRow['game_description_id']] = $gameRuleRow;
					}
				}
				$row['game_rules'] = $gameRulesMap;

				$map[$rule_id] = $row;

			}
		}
		$commonRules['rules'] = $map;

		return $commonRules;
	}

	/**
	 * detail: get player rate from a certain history
	 *
	 * @param int $player_id
	 * @param string $date
	 * @param int $game_description_id
	 * @param array $extra_info includes bet data, common rules
	 *
	 * @return array
	 */
	public function getPlayerRateFromHistory($player_id, $date = null, $game_description_id = null, $extra_info = null) {
// 		$this->db->from('player_level_history')->where('player_id', $player_id);

// 		if (!empty($date)) {
		// 			$this->db->where('history_date <=', $date);
		// 		}
		// 		$this->db->order_by('history_date', 'desc')->limit(1);

// // 		$sql = <<<EOD
		// 		// SELECT plh.id, plh.history_date, plh.cashback_percentage, plh.cashback_maxbonus
		// 		// FROM player_level_history as plh
		// 		// WHERE plh.player_id = ?
		// 		// ORDER BY plh.history_date ASC
		// 		// LIMIT 1
		// 		// EOD;

// 		// $qry = $this->db->query($sql, array($player_id));

// 		$row = $this->runOneRow();
		// 		// $this->utils->debug_log('get history', $row);
		// 		if (!empty($row) && !empty($game_description_id)) {

// 			$rate = $this->getAllowedGameLevelRules($row->level_id, $game_description_id, $row->cashback_percentage);
		// 			if (empty($rate)) {
		// 				$this->utils->debug_log('cancel history', $row);
		// 				$row = null;
		// 			} else {
		// 				$this->utils->debug_log('enable game description', $game_description_id, $rate);
		// 				$row->cashback_percentage = $rate;
		// 			}

// 			// if (!$this->isAllowedGameInLevel($row->level_id, $game_description_id)) {
		// 			// 	$this->utils->debug_log('cancel history', $row);
		// 			// 	//not allowed so cancel it
		// 			// 	$row = null;
		// 			// }
		// 		}

// 		if(empty($row)){
		// 			//if no any vip level settings, try common settings
		// 			$commonRules=[];
		// 			$playerSumBetAmount=0;

// 			if(!empty($extra_info)){
		// 				if(isset($extra_info['commonRules'])){
		// 					$commonRules=$extra_info['commonRules'];
		// 				}
		// 				if(isset($extra_info['mapPlayerBet'])){
		// 					$mapPlayerBet=$extra_info['mapPlayerBet'];
		// 					if(!empty($mapPlayerBet)){
		// 						if(isset($mapPlayerBet[$player_id])){
		// 							$playerSumBetAmount=$mapPlayerBet[$player_id];
		// 						}
		// 					}
		// 				}
		// 			}

// 			if(!empty($commonRules) && !empty($playerSumBetAmount)){
		// 				//search on common rules
		// 				$row=$this->getPercentageByCommonRules($player_id, $game_description_id, $playerSumBetAmount, $commonRules);
		// 				$this->utils->debug_log('use cashback common rules', $row, $player_id, $game_description_id, $playerSumBetAmount);
		// 			}
		// 		}

// 		return $row;
	}

	public function isNoCashbackBonusForNonDepositPlayer() {
		$this->load->model(array('operatorglobalsettings', 'cashback_settings'));

		$cashbackSettings = $this->operatorglobalsettings->getSettingJson(self::CASHBACK_SETTINGS_NAME);

		$no_cashback_bonus_for_non_deposit_player = @$cashbackSettings['no_cashback_bonus_for_non_deposit_player'];
		//default is false
		return !!$no_cashback_bonus_for_non_deposit_player;
	}

	/**
	 * detail: get total cashback
	 *
	 * @param string $date
	 * @param string $startHour
	 * @param string $endHour
	 * @param int $playerId
	 * @param int $withdraw_condition_bet_times
	 *
	 * @return int
	 */
	public function totalCashback($date, $startHour, $endHour, $playerId = null, $withdraw_condition_bet_times = 0, &$result = false, $start_date = null, $end_date = null) {
        $this->load->model(array('player_model', 'transactions', 'users', 'game_description_model', 'withdraw_condition'));

        $use_settled_time_apis = $this->CI->utils->getConfig('api_array_when_calc_cashback_by_settled_time');

		$playerBetByDate = $this->getPlayerBetByDate($date, $startHour, $endHour, $playerId, $start_date, $end_date, NULL, $use_settled_time_apis);

        $playerBetBySettledDate = $this->getPlayerBetBySettledDate($date, $startHour, $endHour, $playerId, $start_date, $end_date, NULL, $use_settled_time_apis);

        $this->utils->debug_log(__METHOD__ . '(): ', $date, $startHour, $endHour, $playerId, 'getPlayerBetByDate count', count($playerBetByDate), 'playerBetBySettledDate count', count($playerBetBySettledDate));

        $cnt = 0;

        if (empty($playerBetByDate) && empty($playerBetBySettledDate)) {
            return $cnt;
        }

		// $mapPlayerBet = $this->sumPlayerBetByDate($date, $startHour, $endHour, $playerId, $start_date, $end_date);
        $mapPlayerBet = $this->calculatePlayerBetTotal($playerBetByDate, $playerBetBySettledDate);

		$this->utils->debug_log('sumPlayerBetByDate', $date, $startHour, $endHour, $playerId, 'count', count($mapPlayerBet));

		// init variables
		// $row = [];
		// load models
		// $this->load->model(array('player'));
		$unknownGames = $this->game_description_model->getUnknownGameList();

		$commonRules = $this->getCommonCashbackRules();

		$extra_info = [
			'mapPlayerBet' => $mapPlayerBet,
			'commonRules' => $commonRules,
		];
		// get superadmin
		// $superadmin = $this->users->getSuperAdmin();

		$isNoCashbackBonusForNonDepositPlayer = $this->isNoCashbackBonusForNonDepositPlayer();

        $always_enable_unknown_games_on_callback = $this->getConfig('always_enable_unknown_games_on_callback');

        $extra_info['levelCashbackMap'] = $this->getFullCashbackPercentageMap();

        $wc_amount_map=[];
//         if ($this->utils->isEnabledFeature('auto_deduct_withdraw_condition_from_bet')) {
        //get total available player withdraw condition by cashback date
        $wc_amount_map = $this->withdraw_condition->getAllPlayersAvailableAmountOnWithdrawConditionByCashbackSettings(
            $date, $startHour, $endHour, $start_date, $end_date, $playerId);
        // $player_deducted_amt_index = array();
//			}

        //          if($this->utils->isEnabledFeature('disable_cashback_if_not_finish_withdraw_condition')){
        // 	$wc_amount_map = $this->withdraw_condition->getAllPlayersAvailableAmountOnWithdrawConditionByCashbackSettings($date, $startHour, $endHour);
        // }

        $all_player_bet = [
            $playerBetByDate,
            $playerBetBySettledDate
        ];

        foreach($all_player_bet as $player_bet_amount_list){
            if(empty($player_bet_amount_list)) continue;

            foreach ($player_bet_amount_list as $pbbd) {
                $game_platform_id = $pbbd->game_platform_id;
                $game_description_id = $pbbd->game_description_id;
                $game_type_id = $pbbd->game_type_id;
                //always sum unknown games
                // if ($always_enable_unknown_games_on_callback && in_array($game_description_id, $unknownGames)) {
                // 	$game_description_id = null;
                // }

                // $playerFirstRate = $this->getPlayerRateFromHistory($pbbd->player_id);

                // beting amount and allowed game
                // if ($pbbd->betting_total > 0 && $playerFirstRate) {
                if ($pbbd->betting_total <= 0) {
                    continue;
                }

                $rate = 0; // $playerFirstRate->cashback_percentage;
                $max_bonus = 0; //$playerFirstRate->cashback_maxbonus;
                $history_id = null; // $playerFirstRate->id;
                $player_id = $pbbd->player_id;
                $levelId = $pbbd->levelId;

                if ($isNoCashbackBonusForNonDepositPlayer) {
                    //should check deposit
                    $playerObj = $this->player_model->getPlayerArrayById($player_id);
                    if ($playerObj['totalDepositAmount'] <= 0) {
                        $this->utils->debug_log('ignore player ' . $player_id . ' for none deposit');
                        continue;
                    }
                }

                // if($this->utils->isEnabledFeature('disable_cashback_if_not_finish_withdraw_condition')){
                // 	if (array_key_exists($player_id , $wc_amount_map)){
                // 		if($wc_amount_map[$player_id] > 0 ){
                // 			$this->utils->debug_log('ignore player '.$player_id.' for not_finished_withdraw_condition');
                // 			continue;
                // 		}
                // 	}
                // }

                // $game_description_id = $pbbd->game_description_id;
                // if (in_array($game_description_id, $unknownGames)) {
                // 	$game_description_id = null;
                // }

                // get daily rate
                $playerDailyRate = $this->getPlayerRateFromLevel($player_id, $levelId,
                    $game_platform_id, $game_type_id, $game_description_id, $extra_info);
                // $playerDailyRate = $this->getPlayerRateFromHistory($pbbd->player_id, $date, $game_description_id, $extra_info);
                // $playerDailyRate = $this->getPlayerDailyRate($pbbd->player_id, $date, $game_description_id);
                //$this->utils->debug_log('defaultCashback', $cashback);

                if ($playerDailyRate) {
                    $rate = $playerDailyRate->cashback_percentage;
                    $max_bonus = $playerDailyRate->cashback_maxbonus;
                    $history_id = $playerDailyRate->id;
                    $level_id = $playerDailyRate->level_id;
                }
                $this->utils->debug_log('process player:'.$player_id.', rate:'.$rate);

                //only for exist cashback rate
                if ($rate <= 0) {
                    continue;
                }

                $this->utils->debug_log('final rate', $rate, 'player_id', $player_id, 'game_description_id', $game_description_id,
                    'history_id', $history_id, 'level_id', $level_id, 'betting', $pbbd->betting_total, 'betting*rate', $pbbd->betting_total * ($rate / 100));

                // $pbbd->percentage = $rate;

                $game_platform_id = $pbbd->game_platform_id;
                $game_description_id = $pbbd->game_description_id;
                $game_type_id = $pbbd->game_type_id;
                $total_date = $date;

                $original_bet_amount = $this->utils->roundCurrencyForShow($pbbd->betting_total);

                // if ($this->utils->isEnabledFeature('auto_deduct_withdraw_condition_from_bet')) {

                $this->auto_deduct_withdraw_condition_from_bet($player_id, $wc_amount_map, $pbbd);

                // if (array_key_exists($player_id, $wc_amount_map)) {

                // 	# if not exist  create empty array to prevent overriding the values
                // 	if (!array_key_exists($player_id, $player_deducted_amt_index)) {
                // 		$player_deducted_amt_index[$player_id] = array();
                // 	}

                // 	#if player_deducted_index is empty
                // 	if (empty($player_deducted_amt_index[$player_id])) {

                // 		$current_wc_total = $wc_amount_map[$player_id][0];

                // 		if ($current_wc_total < $pbbd->betting_total) {
                // 			$pbbd->betting_total = $pbbd->betting_total - $current_wc_total;
                // 		} else {
                // 			$pbbd->betting_total = 0;
                // 		}
                // 		$this->utils->debug_log('auto_deduct_withdraw_condition_from_bet_' . $player_id, 'game_description_id', $game_description_id, 'original_bet_amount', $original_bet_amount, 'betTotal - current_wc_total', $pbbd->betting_total, 'current_wc_total', $current_wc_total, 'bet-index-count', 1);
                // 		#store the index and the first withdraw condition amount deducted
                // 		array_push($player_deducted_amt_index[$player_id], array('index' => 0, 'current_wc_total' => $current_wc_total));

                // 	} else {

                // 		# get the last index
                // 		$last_index = count($player_deducted_amt_index[$player_id]) - 1;
                // 		$last_wc_total = $player_deducted_amt_index[$player_id][$last_index]['current_wc_total'];

                // 		if (!isset($wc_amount_map[$player_id][$last_index + 1])) {
                // 			$wc_amount_map[$player_id][$last_index + 1] = 0;
                // 		}

                // 		$current_wc_total = $wc_amount_map[$player_id][$last_index + 1] + $last_wc_total;

                // 		if ($current_wc_total < $pbbd->betting_total) {
                // 			$pbbd->betting_total = $pbbd->betting_total - $current_wc_total;
                // 		} else {
                // 			$pbbd->betting_total = 0;
                // 		}

                // 		$this->utils->debug_log('auto_deduct_withdraw_condition_from_bet_' . $player_id, 'game_description_id', $game_description_id, 'original_bet_amount', $original_bet_amount, 'betTotal - current_wc_total', $pbbd->betting_total, 'current_wc_total', $current_wc_total, 'bet-index-count', $last_index + 2);
                // 		#store the index and the succeeding withdraw condition amount deducted
                // 		array_push($player_deducted_amt_index[$player_id], array('index' => $last_index, 'current_wc_total' => $current_wc_total));

                // 	}
                // }
                // }
                $cashback_amount = $this->utils->roundCurrencyForShow($pbbd->betting_total * ($rate / 100));
                $withdraw_condition_amount = $this->utils->roundCurrencyForShow($cashback_amount * $withdraw_condition_bet_times);

                $cnt++;
                $this->syncCashbackDaily($player_id, $game_platform_id, $game_description_id, $total_date,
                    $cashback_amount, $history_id, $game_type_id, $level_id, $rate,
                    $this->utils->roundCurrencyForShow($pbbd->betting_total),
                    $withdraw_condition_amount, $max_bonus, $original_bet_amount);
            } ///ddd
        }

		// $result = true;

		return $cnt;
	}

	public function totalCashbackFriendReferral($date, $startHour, $endHour, $playerId = null, $withdraw_condition_bet_times = 0, &$result = false,
												$start_date = null, $end_date = null, $cashback_type = 1, $useWhiteListReferral = false) {

		$this->load->model(array('player_model', 'transactions', 'users', 'game_description_model',
				'withdraw_condition','friend_referral_settings', 'player_friend_referral'));

		$playerIds = array();

        $use_settled_time_apis = $this->CI->utils->getConfig('api_array_when_calc_cashback_by_settled_time');

        $playerBetByDate = $this->getPlayerBetByDate($date, $startHour, $endHour, $playerId, $start_date, $end_date, $playerIds, $use_settled_time_apis);

        $playerBetBySettledDate = $this->getPlayerBetBySettledDate($date, $startHour, $endHour, $playerId, $start_date, $end_date, NULL, $use_settled_time_apis);

		$this->utils->debug_log('getPlayerBetByDate', $date, $startHour, $endHour, $playerId, 'getPlayerBetByDate count', count($playerBetByDate), 'playerBetBySettledDate count', count($playerBetBySettledDate));

        $cnt = 0;

        if (empty($playerBetByDate) && empty($playerBetBySettledDate)) {
            return $cnt;
        }

		//$mapPlayerBet = $this->sumPlayerBetByDate($date, $startHour, $endHour, $playerId, $start_date, $end_date, $playerIds);
        $mapPlayerBet = $this->calculatePlayerBetTotal($playerBetByDate, $playerBetBySettledDate);

		$this->utils->debug_log('sumPlayerBetByDate', $date, $startHour, $endHour, $playerId, 'count', count($mapPlayerBet));

		$commonRules = $this->getCommonCashbackRules();

		$extra_info = [
				'mapPlayerBet' => $mapPlayerBet,
				'commonRules' => $commonRules,
		];
		$isNoCashbackBonusForNonDepositPlayer = $this->isNoCashbackBonusForNonDepositPlayer();

        $extra_info['levelCashbackMap'] = $this->getFullCashbackPercentageMap(); # get game percentage

        $wc_amount_map=[];
        $wc_amount_map = $this->withdraw_condition->getAllPlayersAvailableAmountOnWithdrawConditionByCashbackSettings(
            $date, $startHour, $endHour, $start_date, $end_date, $playerId);

        $all_player_bet = [
            $playerBetByDate,
            $playerBetBySettledDate
        ];

        foreach($all_player_bet as $player_bet_amount_list){
            if(empty($player_bet_amount_list)) continue;

            foreach ($player_bet_amount_list as $pbbd) {
                $game_platform_id = $pbbd->game_platform_id;
                $game_description_id = $pbbd->game_description_id;
                $game_type_id = $pbbd->game_type_id;

                $this->utils->debug_log('total bet', $pbbd->betting_total, "player id : $pbbd->player_id");

                if ($pbbd->betting_total <= 0) {
                    continue;
                }

                $rate = 0;
                $max_bonus = 0;
                $history_id = null;
                $player_id = $pbbd->player_id;
                $level_id = $pbbd->levelId;

                // make sure player was referred to recieve referral cashback
                // get referrer by invited player id
                $referrer_id = $this->player_friend_referral->getReferrerByInvitedPlayerId($player_id);
                if(!$referrer_id) {
                    $this->utils->debug_log('player was not invited or refer by other', "player id : $player_id");
                    continue;
                }

                if ($isNoCashbackBonusForNonDepositPlayer) {
                    $playerObj = $this->player_model->getPlayerArrayById($referrer_id);
                    if ($playerObj['totalDepositAmount'] <= 0) {
                        $this->utils->debug_log('no cashback bonus for non deposit player', "player id : $referrer_id");
                        continue;
                    }
                }

                $playerWhiteIds = array();
                $allowed_player_for_referral_cashback = $this->utils->getConfig('allowed_player_for_referral_cashback');
                if (!empty($allowed_player_for_referral_cashback)) {
                    $this->utils->debug_log("======WHITELIST REFERRAL ENABLED============");

                    $player = $this->player_model->getPlayerIdsByUsernames($allowed_player_for_referral_cashback);
                    if (!empty($player)) {
                        foreach ($player as $key => $value) {
                            array_push($playerWhiteIds, $value['playerId']);
                        }
                    }

                    // only whitelist player can recieve cashback
                    if (!in_array($referrer_id, $playerWhiteIds)) {
                        $this->utils->debug_log('only whitelist can recieve cashback', "player id : $referrer_id");
                        continue;
                    }
                }

                $referralSetting = $this->friend_referral_settings->getFriendReferralSettings();
                if ($referralSetting['cashback_rate'] > 0) {
                    $rate = $referralSetting['cashback_rate'];
                }

//					// get daily rate
//					$playerDailyRate = $this->getPlayerRateFromLevel($player_id, $levelId,
//							$game_platform_id, $game_type_id, $game_description_id, $extra_info);
//
//					if ($playerDailyRate) {
//						$rate = $playerDailyRate->cashback_percentage;
//						$max_bonus = $playerDailyRate->cashback_maxbonus;
//						$history_id = $playerDailyRate->id;
//						$level_id = $playerDailyRate->level_id;
//					}

                if ($rate <= 0) {
                    continue;
                }

                $this->utils->debug_log('final rate', $rate, 'player_id', $player_id, 'game_description_id', $game_description_id,
                    'history_id', $history_id, 'level_id', $level_id, 'betting', $pbbd->betting_total, 'betting*rate', $pbbd->betting_total * ($rate / 100));

                $game_platform_id = $pbbd->game_platform_id;
                $game_description_id = $pbbd->game_description_id;
                $game_type_id = $pbbd->game_type_id;
                $total_date = $date;

                $original_bet_amount = $this->utils->roundCurrencyForShow($pbbd->betting_total);

                #$this->auto_deduct_withdraw_condition_from_bet($player_id, $wc_amount_map, $pbbd);

                $cashback_amount = $this->utils->roundCurrencyForShow($pbbd->betting_total * ($rate / 100));
                $withdraw_condition_amount = $this->utils->roundCurrencyForShow($cashback_amount * $withdraw_condition_bet_times);

                $cnt++;
                $this->syncCashbackDaily($referrer_id, $game_platform_id, $game_description_id, $total_date,
                    $cashback_amount, $history_id, $game_type_id, $level_id, $rate,
                    $this->utils->roundCurrencyForShow($pbbd->betting_total),
                    $withdraw_condition_amount, $max_bonus, $original_bet_amount, $cashback_type, $player_id);
            }
        }

		return $cnt;
	}

	public function auto_deduct_withdraw_condition_from_bet($player_id, &$wc_amount_map, &$pbbd) {
		if (array_key_exists($player_id, $wc_amount_map) &&
				$this->utils->compareResultCurrency($wc_amount_map[$player_id], '>', 0) ){

			$original_bet_amount = $this->utils->roundCurrencyForShow($pbbd->betting_total);

			//minus wc amount from original
			$current_wc_total = $wc_amount_map[$player_id];

            if($current_wc_total < $pbbd->betting_total){
                $pbbd->betting_total = $pbbd->betting_total - $current_wc_total;
                //clear wc amount
                $wc_amount_map[$player_id] = 0;
            }else{
                //minus wc amount, prevent double
                $wc_amount_map[$player_id] = $wc_amount_map[$player_id] - $pbbd->betting_total;
                $pbbd->betting_total = 0;
            }

			$this->utils->debug_log('auto_deduct_withdraw_condition_from_bet_' . $player_id,
				'game_description_id', $pbbd->game_description_id,
				'original_bet_amount', $original_bet_amount, 'betTotal - current_wc_total', $pbbd->betting_total,
				'current_wc_total', $current_wc_total, 'wc_amount_map[$player_id]', $wc_amount_map[$player_id]);

		}
	}

	/**
	 * detail: generate last level history
	 *
	 * @return array
	 */
// 	public function generate_last_level_history(){
	// 		$this->runRawUpdateInsertSQL('drop table IF EXISTS tmp_last_level_history');

// 		$sql=<<<EOD
	// create table tmp_last_level_history
	// select player_id, max(history_date) as history_date
	// from player_level_history
	// group by player_id
	// EOD;

// 		$this->runRawUpdateInsertSQL($sql);

// 		$this->runRawUpdateInsertSQL('create index idx_player_id_history_date on tmp_last_level_history(player_id,history_date)');

// 	}

	/**
	 * detail: validate last level history
	 *
	 * @return array
	 */
// 	public function validate_last_level_history(){

// 		$sql=<<<EOD
	// select player_level_history.player_id,player_level_history.history_date,player_level_history.level_id,player.levelId
	// from tmp_last_level_history
	// join player_level_history on tmp_last_level_history.player_id=player_level_history.player_id and tmp_last_level_history.history_date=player_level_history.history_date
	// join player on tmp_last_level_history.player_id=player.playerId
	// where player_level_history.level_id!=player.levelId
	// EOD;

// 		$rows=$this->runRawSelectSQLArray($sql);

// 		$rlt=array();
	// 		if(!empty($rows)){
	// 			foreach ($rows as $row) {
	// 				// self::SPLIT_NUMBER
	// 				$this->db->set('level_id',$row['levelId'])
	// 					->where('player_id',$row['player_id'])
	// 					->where('history_date',$row['history_date'])
	// 					;

// 				$success=$this->runAnyUpdate('player_level_history');

// 				$rlt[$row['player_id'].'-'.$row['history_date'].'-'.$row['level_id']]=$success;
	// 			}
	// 		}

// 		return $rlt;
	// 	}

	public function getAllCanJoinIn() {
		$this->db->select('vipsetting.vipSettingId');
		$this->db->select('vipsettingcashbackrule.vipsettingcashbackruleId');
		$this->db->select('vipsetting.groupName');
		$this->db->select('vipsettingcashbackrule.vipLevelName');
		$this->db->select('vipsetting.groupDescription');
		$this->db->from('vipsetting');
		$this->db->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipSettingId = vipsetting.vipSettingId', 'left');
		$this->db->where('vipsetting.can_be_self_join_in', self::DB_TRUE);
		$this->db->order_by('vipsetting.groupName', 'asc');
		$this->db->order_by('vipsettingcashbackrule.vipLevelName', 'asc');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * search unpaid cashback by report condition
	 *
	 * @param  array $input condition from report
	 * @return array
	 */
	public function searchUnpaidCashbackByCondition($input) {

		$this->db->select('total_cashback_player_game_daily.id')->from('total_cashback_player_game_daily')
			->join('player', 'total_cashback_player_game_daily.player_id = player.playerId');

		if (isset($input['by_amount_less_than']) && $input['by_amount_less_than'] !== null && $input['by_amount_less_than'] !== '') {
			$this->db->where("total_cashback_player_game_daily.amount <=", $input['by_amount_less_than']);
		}

		if (isset($input['by_amount_greater_than']) && $input['by_amount_greater_than'] !== null && $input['by_amount_greater_than'] !== '') {
			$this->db->where("total_cashback_player_game_daily.amount <=", $input['by_amount_greater_than']);
		}

		if (isset($input['by_player_level']) && !empty($input['by_player_level'])) {
			$this->db->where("player.levelId", $input['by_player_level']);
		}

		if (isset($input['by_paid_flag']) && !empty($input['by_paid_flag'])) {
			$this->db->where("total_cashback_player_game_daily.paid_flag", $input['by_paid_flag']);
		}

		if (isset($input['enable_date']) && $input['enable_date'] == 'true') {

			if (isset($input['by_date_from'])) {
				$this->db->where("total_cashback_player_game_daily.total_date >=", $input['by_date_from']);
			}

			if (isset($input['by_date_to'])) {
				$this->db->where("total_cashback_player_game_daily.total_date <=", $input['by_date_to']);
			}
		}

		//convert memberUsername to player id
		// $this->utils->debug_log('memberUsername', $input['memberUsername']);

		if (isset($input['by_username']) && !empty($input['by_username'])) {
			$this->db->like("player.username", $input['by_username']);
		}

		return $this->convertArrayRowsToArray($this->runMultipleRowArray(), 'id');
	}

	/**
	 * Get player maximum withdrawal per transaction
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerMaxWithdrawalPerTransaction($playerId) {
		$this->db->select('vipsettingcashbackrule.max_withdraw_per_transaction, vipsettingcashbackrule.max_withdrawal_non_deposit_player')
			->from('playerlevel')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left');
		$this->db->where('playerlevel.playerId', $playerId);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function convertToNewCashbackPercentage() {
		//vipsetting_cashback_game to group_level_cashback_game_platform , group_level_cashback_game_type , group_level_cashback_game_description

		$this->db->select('vipsettingcashbackruleId')->from($this->levelTable);
		$vipLevelList = $this->runMultipleRowArray();

		foreach ($vipLevelList as $level) {

			$levelId = $level['vipsettingcashbackruleId'];

			// $result = $this->group_level->getGameTreeForGroupLevel($levelId);

			$self = $this;
			//write tree to new table
			$this->dbtransOnly(function () use ($self, $levelId) {

				list($gamePlatformList, $gameTypeList, $gameDescList) =
				$self->getVipSettingsGamesTypeAndGameDesc($levelId);

				$self->deleteCashbackPercentage($levelId);
				// $self->db->delete('group_level_cashback_game_platform',
				// 	['vipsetting_cashbackrule_id'=>$levelId]);

				// $self->db->delete('group_level_cashback_game_type',
				// 	['vipsetting_cashbackrule_id'=>$levelId]);

				// $self->db->delete('group_level_cashback_game_description',
				// 	['vipsetting_cashbackrule_id'=>$levelId]);

				if (!empty($gamePlatformList)) {

					$gamePlatformData = [];
					foreach ($gamePlatformList as $gamePlatformId => $percentage) {
						$gamePlatformData[] = [
							'vipsetting_cashbackrule_id' => $levelId,
							'game_platform_id' => $gamePlatformId,
							'percentage' => $percentage,
							'status' => self::DB_TRUE,
							'updated_at' => $self->utils->getNowForMysql(),
						];
					}
					$this->db->insert_batch('group_level_cashback_game_platform', $gamePlatformData);

				}

				if (!empty($gameTypeList)) {

					$gameTypeData = [];
					foreach ($gameTypeList as $gameTypeId => $percentage) {
						$gameTypeData[] = [
							'vipsetting_cashbackrule_id' => $levelId,
							'game_type_id' => $gameTypeId,
							'percentage' => $percentage,
							'status' => self::DB_TRUE,
							'updated_at' => $self->utils->getNowForMysql(),
						];
					}
					$this->db->insert_batch('group_level_cashback_game_type', $gameTypeData);

				}

				if (!empty($gameDescList)) {

					$gameDescData = [];
					foreach ($gameDescList as $gameDescId => $percentage) {
						$gameDescData[] = [
							'vipsetting_cashbackrule_id' => $levelId,
							'game_description_id' => $gameDescId,
							'percentage' => $percentage,
							'status' => self::DB_TRUE,
							'updated_at' => $self->utils->getNowForMysql(),
						];
					}
					$this->db->insert_batch('group_level_cashback_game_description', $gameDescData);

				}

				return true;

			});

		}

	}

	public function deleteCashbackPercentage($levelId) {

		$success = true;

		$this->db->delete('group_level_cashback_game_platform',
			['vipsetting_cashbackrule_id' => $levelId]);

		$this->db->delete('group_level_cashback_game_type',
			['vipsetting_cashbackrule_id' => $levelId]);

		$this->db->delete('group_level_cashback_game_description',
			['vipsetting_cashbackrule_id' => $levelId]);

		return $success;
	}

	public function getFullCashbackPercentageMap() {

		$levelCashbackMap = [];

		//ignore unchecked cashback level
		$this->db->from($this->levelTable)->where('bonus_mode_cashback', self::DB_TRUE);
        $this->utils->debug_log('bonus_mode_cashback query ----------> ', $this->db->_compile_select());
		$rows = $this->runMultipleRowArray();
		// $levelCashbackMap=$this->buildMap($rows, $this->levelId, 'cashback_maxbonus');
		$levelIdArr=[];
		if(!empty($rows)) {
			foreach ($rows as $row) {
				$levelCashbackMap[$row[$this->levelId]] = [
					'cashback_maxbonus' => floatval($row['cashback_maxbonus']),
					// 'bonus_mode_cashback'=>floatval($row['bonus_mode_cashback']),
					'cashback_percentage' => floatval($row['cashback_percentage']),
				];

				$levelIdArr[]=$row[$this->levelId];
			}

            $this->db->from('group_level_cashback_game_platform')
                ->where_in('group_level_cashback_game_platform.vipsetting_cashbackrule_id', $levelIdArr);
            $rows = $this->runMultipleRowArray();

            if(!empty($rows)) {
                foreach ($rows as $row) {

                    if (!isset($levelCashbackMap[$row['vipsetting_cashbackrule_id']])) {
                        $levelCashbackMap[$row['vipsetting_cashbackrule_id']] = [];
                    }
                    if (!isset($levelCashbackMap[$row['vipsetting_cashbackrule_id']]['game_platform'])) {
                        $levelCashbackMap[$row['vipsetting_cashbackrule_id']]['game_platform'] = [];
                    }

                    $levelCashbackMap[$row['vipsetting_cashbackrule_id']]['game_platform'][$row['game_platform_id']] = floatval($row['percentage']);
                }
            }
            unset($rows);

            $this->db->from('group_level_cashback_game_type')
                ->where_in('group_level_cashback_game_type.vipsetting_cashbackrule_id', $levelIdArr);
            $rows = $this->runMultipleRowArray();

            if(!empty($rows)) {
                foreach ($rows as $row) {
                    if (!isset($levelCashbackMap[$row['vipsetting_cashbackrule_id']])) {
                        $levelCashbackMap[$row['vipsetting_cashbackrule_id']] = [];
                    }
                    if (!isset($levelCashbackMap[$row['vipsetting_cashbackrule_id']]['game_type'])) {
                        $levelCashbackMap[$row['vipsetting_cashbackrule_id']]['game_type'] = [];
                    }

                    $levelCashbackMap[$row['vipsetting_cashbackrule_id']]['game_type'][$row['game_type_id']] = floatval($row['percentage']);
                }
            }
            unset($rows);

			$this->getLevelGameDescription($levelIdArr, $levelCashbackMap);

            // $this->db->from('group_level_cashback_game_description')
            //     ->where_in('group_level_cashback_game_description.vipsetting_cashbackrule_id', $levelIdArr);
            // $rows = $this->runMultipleRowArray();

            // if(!empty($rows)) {
            //     foreach ($rows as $row) {
            //         if (!isset($levelCashbackMap[$row['vipsetting_cashbackrule_id']])) {
            //             $levelCashbackMap[$row['vipsetting_cashbackrule_id']] = [];
            //         }
            //         if (!isset($levelCashbackMap[$row['vipsetting_cashbackrule_id']]['game_description'])) {
            //             $levelCashbackMap[$row['vipsetting_cashbackrule_id']]['game_description'] = [];
            //         }

            //         $levelCashbackMap[$row['vipsetting_cashbackrule_id']]['game_description'][$row['game_description_id']] = floatval($row['percentage']);
            //     }
            // }
		}
		return $levelCashbackMap;
	}

	public function getLevelGameDescription($levelIdArr, &$levelCashbackMap){
		$this->db->select('vipsetting_cashbackrule_id,game_description_id,percentage')->from('group_level_cashback_game_description')
		    ->where_in('group_level_cashback_game_description.vipsetting_cashbackrule_id', $levelIdArr);
		$rows = $this->runMultipleRowArrayUnbuffered();

		$this->utils->debug_log('process getLevelGameDescription run sql', count($rows));

		if(!empty($rows)) {
			foreach ($rows as $row) {
				if (!isset($levelCashbackMap[$row['vipsetting_cashbackrule_id']])) {
					$levelCashbackMap[$row['vipsetting_cashbackrule_id']] = [];
				}
				if (!isset($levelCashbackMap[$row['vipsetting_cashbackrule_id']]['game_description'])) {
					$levelCashbackMap[$row['vipsetting_cashbackrule_id']]['game_description'] = [];
				}

				$levelCashbackMap[$row['vipsetting_cashbackrule_id']]['game_description'][$row['game_description_id']] = floatval($row['percentage']);
			}
			unset($rows);
		}
	}

	/**
	 *
	 * batch add cashback
	 *
	 * @param  int $levelId
	 * @param  array $gamePlatformList
	 * @param  array $gameTypeList
	 * @param  array $gameDescList
	 * @return boolean
	 */
	public function batchAddCashbackPercentage($levelId, $gamePlatformList, $gameTypeList, $gameDescList, $adminUserId=null, $postTree=null, &$diffList=[]) {

		$success = true;

		$this->utils->debug_log('gamePlatformList count', count($gamePlatformList), 'gameTypeList count', count($gameTypeList), 'gameDescList count', count($gameDescList));

		$this->utils->debug_log($gamePlatformList, $gameTypeList, $gameDescList);

		if (!$this->backupCashbackPercentage($levelId, $adminUserId, $postTree)) {
			$this->utils->error_log('backupCashbackPercentage failed', $levelId);
		}

		$this->generateCashbackDiffList($levelId, $gamePlatformList, $gameTypeList, $diffList);

		$this->deleteCashbackPercentage($levelId);

		$data = [];
		foreach ($gamePlatformList as $gamePlatformId => $percentage) {

			$data[] = [
				'vipsetting_cashbackrule_id' => $levelId,
				'game_platform_id' => $gamePlatformId,
				'percentage' => $percentage,
				'status' => self::DB_TRUE,
				'updated_at' => $this->utils->getNowForMysql(),
			];

		}

		$this->batchInsertWithLimit('group_level_cashback_game_platform', $data);

		$data = [];
		foreach ($gameTypeList as $gameTypeId => $percentage) {

			$data[] = [
				'vipsetting_cashbackrule_id' => $levelId,
				'game_type_id' => $gameTypeId,
				'percentage' => $percentage,
				'status' => self::DB_TRUE,
				'updated_at' => $this->utils->getNowForMysql(),
			];

		}

		$this->batchInsertWithLimit('group_level_cashback_game_type', $data);

		$data = [];
		foreach ($gameDescList as $gameDescId => $percentage) {

			$data[] = [
				'vipsetting_cashbackrule_id' => $levelId,
				'game_description_id' => $gameDescId,
				'percentage' => $percentage,
				'status' => self::DB_TRUE,
				'updated_at' => $this->utils->getNowForMysql(),
			];

		}

		$this->batchInsertWithLimit('group_level_cashback_game_description', $data);

		return $success;
	}

	public function getVipGroupList() {
		$this->db->select('vipsettingId,groupName')
			->from('vipsetting')->where('vipsetting.status', 'active')
			->where('vipsetting.deleted !=', 1)
			->order_by('groupName', 'ASC');

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	public function getCashbackPayoutSetting() {
		$this->db->select('*')->from('vipgrouppayoutsetting');

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['setTime'] = mdate('%h:%i:%s %A', strtotime($row['setTime']));
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	public function getPlayerGroup() {
		$this->db->select('vipSettingId,groupName')->from('vipsetting')->where('vipsetting.status', 'active');
		$this->db->order_by('vipsetting.groupName', 'ASC');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function getAllCanJoinInGroup() {
// this reuturn only 1st level
		$this->db->select('vipsetting.vipSettingId');
		$this->db->select('vipsettingcashbackrule.vipsettingcashbackruleId');
		$this->db->select('vipsetting.groupName');
		$this->db->select('vipsettingcashbackrule.vipLevelName');
		$this->db->select('vipsetting.groupDescription');
		$this->db->from('vipsetting');
		$this->db->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipSettingId = vipsetting.vipSettingId', 'left');
		$this->db->where('vipsetting.can_be_self_join_in', self::DB_TRUE);
		$this->db->where('vipsettingcashbackrule.vipLevel', '1');
		$this->db->where('vipsetting.deleted', '0');
		$this->db->order_by('vipsetting.groupName', 'asc');
		$this->db->order_by('vipsettingcashbackrule.vipLevelName', 'asc');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function getPlayerGroupLevelInfo($playerId) {
		$this->db->select('player.levelId, vipsettingcashbackrule.vipLevelName, vipsetting.groupName, vipsetting.vipSettingId')
			->from('player')
			->join('vipsettingcashbackrule', 'player.levelId=vipsettingcashbackrule.vipsettingcashbackruleId')
			->join('vipsetting', 'vipsetting.vipSettingId=vipsettingcashbackrule.vipSettingId');
		$this->db->where('playerId', $playerId);

		return $this->runOneRowArray();
	}

	public function addUpgradeLevelSetting($data) {
		if (isset($data['upgrade_id'])) {
			$this->db->where('upgrade_id', $data['upgrade_id']);
			$this->db->update('vip_upgrade_setting', $data);
		} else {
			$this->db->insert('vip_upgrade_setting', $data);
		}
	}

	public function upgradeLevelSetting($upgrade_id = '') {
		$this->db->select('*');
		$this->db->from('vip_upgrade_setting');

		if (!empty($upgrade_id)) {
			$this->db->where('upgrade_id', $upgrade_id);
		}

		$data = $this->db->get()->result();
		return $data;
	}

	public function deleteUpgradeLevelSetting($upgradeSettingId) {
		if ($upgradeSettingId) {
			$this->db->where('upgrade_id', $upgradeSettingId);
			$this->db->delete('vip_upgrade_setting');
		}
	}

	public function enableDisableSetting($upgradeSettingId, $status = '') {
		if ($status == self::UPGRADE_SETTING_ENABLE) {
			$this->db->set('status', self::UPGRADE_SETTING_DISABLE);
		} else {
			$this->db->set('status', self::UPGRADE_SETTING_ENABLE);
		}
		$this->db->where('upgrade_id', $upgradeSettingId);
		$this->db->update('vip_upgrade_setting');
	}

	public function upDownTemplateList() {
		$this->db->from('vip_upgrade_setting');
		$this->db->where('status', self::UPGRADE_SETTING_ENABLE);
		$data = $this->db->get()->result();
		return $data;
	}

	public function getUpgradeSettingById($upgradeSettingId) {
		$this->db->from('vip_upgrade_setting');
		$this->db->where('upgrade_id', $upgradeSettingId);
		$data = $this->db->get()->row();

		return $data ? $data->level_upgrade : '';
	}

	public function getSettingData($upgradeSettingId) {
		$this->db->select();
		$this->db->from('vip_upgrade_setting');
		$this->db->where('upgrade_id', $upgradeSettingId);
		$setting = $this->db->get()->row_array();
		return $setting;
	}

	/**
	 * Convert Points
	 */
	public function convertToPoints($fromDatetime, $toDatetime) {
		$this->load->library(array('authentication'));
		$this->load->model(array('total_player_game_minute', 'player_model', 'transactions', 'point_transactions'));

		$result = array();

		$winPoints = $lossPoints = 0;

		$adminUserId = $this->authentication->getUserId();

		$gameLogData = $this->total_player_game_minute->getTotalBetsWinsLossByAllPlayers($fromDatetime, $toDatetime);

		$allPlayerLevels = $this->getAllPlayerLevels(); // gets all player levels
		$levelMap = array();

		foreach ($allPlayerLevels as $lvl) {
			$levelMap[$lvl['vipsettingcashbackruleId']] = $lvl;
		}

		if (!empty($gameLogData)) {
			foreach ($gameLogData as $row) {
				$player = $this->player_model->getPlayerById($row['player_id']);

				if (!isset($levelMap[$player->levelId])) {
					$this->utils->error_log('ignore player', $player->playerId, $player->username);
					continue;
				}

				$playerTotalPoints = $this->player_model->getPlayerPoints($player->playerId);

				$playerLevel = $levelMap[$player->levelId];

				$betConvertRate = $playerLevel['bet_convert_rate'];
				$winConvertRate = $playerLevel['winning_convert_rate'];
				$lossConvertRate = $playerLevel['losing_convert_rate'];

				// if bet rate is not empty then convert
				if (!empty($betConvertRate)) {
					$betPoints = $row['total_bet'] * $betConvertRate / 100; // convert bet points

					$this->point_transactions->createPointTransaction(
						$adminUserId,
						$player->playerId,
						$betPoints, // points
						$playerTotalPoints, // before point balance
						$betPoints + $playerTotalPoints, // new points balance ( before points + points )
						'', // sales order id
						'', // sales order promo id
						Point_transactions::BET_POINT);

					if (!empty($winConvertRate)) {
						$winPoints = $row['total_win'] * $winConvertRate / 100; // convert win points

						$this->point_transactions->createPointTransaction(
							$adminUserId,
							$player->playerId,
							$winPoints, // points
							$playerTotalPoints + $betPoints, // before point balance
							$winPoints + $betPoints + $playerTotalPoints, // new points balance ( before points + points )
							'', // sales order id
							'', // sales order promo id
							Point_transactions::WIN_POINT);
					}

					if (!empty($lossConvertRate)) {
						$lossPoints = $row['total_loss'] * $lossConvertRate / 100; // convert loss points

						$this->point_transactions->createPointTransaction(
							$adminUserId,
							$player->playerId,
							$lossPoints, // points
							$playerTotalPoints + $winPoints + $betPoints, // before point balance
							$winPoints + $betPoints + $lossPoints + $playerTotalPoints, // new points balance ( before points + points )
							'', // sales order id
							'', // sales order promo id
							Point_transactions::LOSS_POINT);
					}

					$overAllPoints = $playerTotalPoints + $betPoints + $winPoints + $lossPoints;

					// update player point balance
					$this->player_model->updatePlayerPointBalance($player->playerId, $overAllPoints);

				} else {
					$this->utils->debug_log('no points for ' . $player->username);
				}
			}
		}

		return $result;
	}

	public function getNextLevel($vipgrouplevelId) {

		$this->db->select('vipsettingcashbackrule.*,vipsetting.groupName')->from('vipsettingcashbackrule');
		$this->db->join('vipsetting', 'vipsettingcashbackrule.vipSettingId = vipsetting.vipSettingId');
		$this->db->where('vipsettingcashbackrule.vipsettingcashbackruleId >', $vipgrouplevelId);
		$this->db->limit(1);

		$query = $this->db->get();
		return $query->row_array();

	}

	public function getVIPTopLevel($vipSettingId, $vipLevel) {

		$this->db->select('*');
		$this->db->from('vipsettingcashbackrule');
		$this->db->where('vipSettingId', $vipSettingId);
		$this->db->where('vipLevel', $vipLevel);

		return $this->db->get()->row();

	}

	/**
	 * List formula
	 *
	 * return boolean
	 */
	public function displayPlayerFormulaForUpgrade($formula) {
		$result = '';
		$option = 0;

		foreach ($formula as $key => $val) {
			if (is_array($val)) {
				if ($key == 'bet_amount') {
					$option = lang('Bet');
				} elseif ($key == 'deposit_amount') {
					$option = lang('Deposit');
				} elseif ($key == 'loss_amount') {
					$option = lang('Loss');
				} elseif ($key == 'win_amount') {
					$option = lang('Win');
				}
				$conjunction = $val[0];
				$amount = $val[1];
				//$result .= $option . ' ' . $conjunction . ' ' . $this->utils->formatCurrency($amount);
				$result .= $option .$conjunction . $amount;
			} else {
				if (strpos($key, 'operator') !== false) {
					$result .= ' ' . lang($val) . ' ';
				}
			}
		}
		return $result;
	}


	public function getPlayerUpgradePercentage($playerId, $formula, $period) {

		$schedule = json_decode($period, true);

		$total_deposit = 0;
		$total_bet = 0;
		$total_loss = 0;
		$total_win = 0;

		$upgradeSched = $this->getUpgradeSchedule($schedule, $ignoreDate = true);
		if (isset($formula['accumulation'])) {
			if ((int)$formula['accumulation'] == 1) {
				$now = new DateTime();
				$this->load->model(['player_model']);
				$playerDetail = $this->player_model->getPlayerInfoDetailById($playerId);
				$playerCreatedOn = $playerDetail['createdOn'];
				$upgradeSched['dateForm'] = $playerCreatedOn;
				$upgradeSched['dateTo'] = $now->format('Y-m-d H:i:s');
			}
		}
		if (!empty($upgradeSched['dateFrom']) && !empty($upgradeSched['dateTo'])) {
			$fromDatetime = $upgradeSched['dateFrom'];
			$toDatetime = $upgradeSched['dateTo'];

			$gameLogData = $this->total_player_game_day->getPlayerTotalBetWinLoss($playerId, $fromDatetime, $toDatetime);

			$total_deposit = $this->transactions->getTotalDepositWithdrawalBonusCashbackByPlayers($playerId, $fromDatetime, $toDatetime)[0];
			$total_bet = $gameLogData['total_bet'];
			$total_win = $gameLogData['total_win'];
			$total_loss = $gameLogData['total_loss'];
		}

		$playerData = array();

		foreach ($formula as $key => $val) {
			if (is_array($val)) {
				if ($key == 'bet_amount') {
					array_push($playerData, array(
						'name' => lang('Bet'),
						'total' => $total_bet,
						'condition' => $val[1],
						'percentage_rate' => $this->group_level->playerRate($total_bet, $val[1])
					));
				} elseif ($key == 'deposit_amount') {
					array_push($playerData, array(
						'name' => lang('Deposit'),
						'total' => $total_deposit,
						'condition' => $val[1],
						'percentage_rate' => $this->group_level->playerRate($total_deposit, $val[1])
					));
				} elseif ($key == 'loss_amount') {
					array_push($playerData, array(
						'name' => lang('Loss'),
						'total' => $total_loss,
						'condition' => $val[1],
						'percentage_rate' => $this->group_level->playerRate($total_loss, $val[1])
					));
				} elseif ($key == 'win_amount') {
					array_push($playerData, array(
						'name' => lang('Win'),
						'total' => $total_win,
						'condition' => $val[1],
						'percentage_rate' => $this->group_level->playerRate($total_win, $val[1])
					));
				}
			}
		}

		return $playerData;
	}

	public function playerRate($playerTotalAmount, $vipConditionAmount) {
		$percentage = $vipConditionAmount==0 ? 0 : round(($playerTotalAmount / $vipConditionAmount * 100));
		if($percentage >= 100) {
			return 100;
			// return 95; // need to wait auto upgrade. depends on schedule(daily, weekly, monthly, yearly) that is set in VIP Setting
		}
		return $percentage;
	}

	/**
	 * get if vip show is done
	 *
	 * @param string
	 * @return array
	 */
	function isVIPShowDone($player_id) {
		$query = $this->db->query("SELECT is_vip_show_done FROM player WHERE playerId = '" . $player_id . "'");

		return $query->row_array();
	}

	/**
	 * set if vip show is done 1:0
	 *
	 * @param string
	 * @return array
	 */
	function setIsVIPShowDone($data) {
		$this->db->set("is_vip_show_done", $data['is_vip_show_done']);
		$this->db->where('playerId', $data['playerId']);

		return $this->db->update('player');
	}

	/***
	 * VIP Cashback Period
	 */

	// temporary add constant here for easy tracking
	const VIP_CASHBACK_DAILY = 0;
	const VIP_CASHBACK_WEEKLY = 1;

	function getWeeklyPeriodInCashbackRule() {
		$this->db->select('vipsettingcashbackrule.vipsettingcashbackruleId');
		$this->db->select('vipsettingcashbackrule.vipLevelName');
		$this->db->select('vipsettingcashbackrule.vipSettingId');
		$this->db->select('vipsettingcashbackrule.cashback_period');
		$this->db->select('vipsetting.groupName');
		$this->db->from('vipsettingcashbackrule');
		$this->db->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$this->db->where('vipsettingcashbackrule.cashback_period >=', self::VIP_CASHBACK_WEEKLY);

		return $this->runMultipleRowArray();
	}

	function processWeeklyCashbackinVIP($currentDate, $cashbackSettings, $vipCashbackWeeklySettings) {

		foreach($vipCashbackWeeklySettings as $setting) {
			$day =  $setting['cashback_period'];   // 1-Mon, 2-Tues, 3-Wed, etc..

			$cashbackDay = $this->getDayCashbackSetting($day);
			$currentDay = date('l', strtotime($currentDate));

			// check if current day and cashback day is match (then proccess cashback)
			if($cashbackDay == $currentDay) {
				$this->totalCashbackWeeklySettings($cashbackSettings, $currentDate, null);
			} else {
				$this->utils->debug_log('VIP '. $setting['groupName'].'-'.$setting['vipLevelName'].' cashback still proccessing', "CurrentDay:$currentDay", "CashbackDay:$cashbackDay");
			}
		}
	}

	function totalCashbackWeeklySettings($cashBackSettings, $date, $playerId = null) {

		$startHour = $cashBackSettings->fromHour;
		$endHour = $cashBackSettings->toHour;
		$withdraw_condition_bet_times = isset($cashBackSettings->withdraw_condition) ? $cashBackSettings->withdraw_condition : 0;

		$startDate = date("Y-m-d", strtotime("last week monday"));
		$endDate = date("Y-m-d", strtotime("last week sunday"));

		return $this->totalCashbackWeekly($date, $startHour, $endHour, $playerId, $withdraw_condition_bet_times, $result, $startDate, $endDate);
	}

	function totalCashbackWeekly($date, $startHour, $endHour, $playerId = null,
								 $withdraw_condition_bet_times = 0, &$result = false, $start_date = null, $end_date = null) {

        $use_settled_time_apis = $this->CI->utils->getConfig('api_array_when_calc_cashback_by_settled_time');

		$playerBetByDate = $this->getPlayerBetByDate($date, $startHour, $endHour, $playerId, $start_date, $end_date, NULL, $use_settled_time_apis);

        $playerBetBySettledDate = $this->getPlayerBetBySettledDate($date, $startHour, $endHour, $playerId, $start_date, $end_date, NULL, $use_settled_time_apis);

        $this->utils->debug_log(__METHOD__ . '(): ', $date, $startHour, $endHour, $playerId, 'getPlayerBetByDate count', count($playerBetByDate), 'playerBetBySettledDate count', count($playerBetBySettledDate));

        $cnt = 0;

        if (empty($playerBetByDate) && empty($playerBetBySettledDate)) {
            return $cnt;
        }

		// $mapPlayerBet = $this->sumPlayerBetByDate($date, $startHour, $endHour, $playerId, $start_date, $end_date);
        $mapPlayerBet = $this->calculatePlayerBetTotal($playerBetByDate, $playerBetBySettledDate);

		$commonRules = $this->getCommonCashbackRules();

		$extra_info = ['mapPlayerBet' => $mapPlayerBet, 'commonRules' => $commonRules,];

		$wc_amount_map = array();
		$player_deducted_amt_index = array();

		$isNoCashbackBonusForNonDepositPlayer = $this->isNoCashbackBonusForNonDepositPlayer();

        $extra_info['levelCashbackMap'] = $this->getFullCashbackPercentageMap();

        // if ($this->utils->isEnabledFeature('auto_deduct_withdraw_condition_from_bet')) {
        //get total available player withdraw condition by cashback date
        $wc_amount_map = $this->withdraw_condition->getAllPlayersAvailableAmountOnWithdrawConditionByCashbackSettings($date, $startHour, $endHour, $start_date, $end_date);
        // }

        $all_player_bet = [
            $playerBetByDate,
            $playerBetBySettledDate
        ];

        foreach($all_player_bet as $player_bet_amount_list){
            if(empty($player_bet_amount_list)) continue;

            foreach ($player_bet_amount_list as $pbbd) {
                $rate = 0;
                $max_bonus = 0;
                $history_id = null;

                $game_platform_id = $pbbd->game_platform_id;
                $game_description_id = $pbbd->game_description_id;
                $game_type_id = $pbbd->game_type_id;
                $level_id = $pbbd->levelId;
                $player_id = $pbbd->player_id;

                $isPlayerCanCashback = $this->checkPlayerVIPCashbackWeekly($player_id, $date);

                if ($pbbd->betting_total <= 0) {
                    continue;
                }

                // no cashback bonus for non deposit player
                if ($isNoCashbackBonusForNonDepositPlayer) {
                    $playerObj = $this->player_model->getPlayerArrayById($player_id);
                    if ($playerObj['totalDepositAmount'] <= 0) {
                        $this->utils->debug_log('ignore player ' . $player_id . ' for none deposit');
                        continue;
                    }
                }

                // check if player can cashback depends on player vip cashback period
                if (!$isPlayerCanCashback) {
                    $this->utils->debug_log('ignore player ' . $player_id . ' for weekly cashback');
                    continue;
                }

                $playerRate = $this->getPlayerRateFromLevel($player_id, $level_id, $game_platform_id, $game_type_id, $game_description_id, $extra_info);

                if ($playerRate) {
                    $rate = $playerRate->cashback_percentage;
                    $max_bonus = $playerRate->cashback_maxbonus;
                    $history_id = $playerRate->id;
                    $level_id = $playerRate->level_id;
                }

                //only for exist cashback rate
                if ($rate <= 0) {
                    continue;
                }

                $this->utils->debug_log('final rate', $rate, 'player_id', $player_id, 'game_description_id', $game_description_id,
                    'history_id', $history_id, 'level_id', $level_id, 'betting', $pbbd->betting_total, 'betting*rate', $pbbd->betting_total * ($rate / 100));

                $total_date = $date;
                $original_bet_amount = $this->utils->roundCurrencyForShow($pbbd->betting_total);

                // if ($this->utils->isEnabledFeature('auto_deduct_withdraw_condition_from_bet')) {

                $this->auto_deduct_withdraw_condition_from_bet($player_id, $wc_amount_map, $pbbd);

                // if (array_key_exists($player_id, $wc_amount_map)) {

                // 	# if not exist  create empty array to prevent overriding the values
                // 	if (!array_key_exists($player_id, $player_deducted_amt_index)) {
                // 		$player_deducted_amt_index[$player_id] = array();
                // 	}

                // 	#if player_deducted_index is empty
                // 	if (empty($player_deducted_amt_index[$player_id])) {

                // 		$current_wc_total = $wc_amount_map[$player_id][0];

                // 		if ($current_wc_total < $pbbd->betting_total) {
                // 			$pbbd->betting_total = $pbbd->betting_total - $current_wc_total;
                // 		} else {
                // 			$pbbd->betting_total = 0;
                // 		}
                // 		$this->utils->debug_log('auto_deduct_withdraw_condition_from_bet_' . $player_id, 'game_description_id', $game_description_id, 'original_bet_amount', $original_bet_amount, 'betTotal - current_wc_total', $pbbd->betting_total, 'current_wc_total', $current_wc_total, 'bet-index-count', 1);
                // 		#store the index and the first withdraw condition amount deducted
                // 		array_push($player_deducted_amt_index[$player_id], array('index' => 0, 'current_wc_total' => $current_wc_total));

                // 	} else {

                // 		# get the last index
                // 		$last_index = count($player_deducted_amt_index[$player_id]) - 1;
                // 		$last_wc_total = $player_deducted_amt_index[$player_id][$last_index]['current_wc_total'];

                // 		if (!isset($wc_amount_map[$player_id][$last_index + 1])) {
                // 			$wc_amount_map[$player_id][$last_index + 1] = 0;
                // 		}

                // 		$current_wc_total = $wc_amount_map[$player_id][$last_index + 1] + $last_wc_total;

                // 		if ($current_wc_total < $pbbd->betting_total) {
                // 			$pbbd->betting_total = $pbbd->betting_total - $current_wc_total;
                // 		} else {
                // 			$pbbd->betting_total = 0;
                // 		}

                // 		$this->utils->debug_log('auto_deduct_withdraw_condition_from_bet_' . $player_id, 'game_description_id', $game_description_id, 'original_bet_amount', $original_bet_amount, 'betTotal - current_wc_total', $pbbd->betting_total, 'current_wc_total', $current_wc_total, 'bet-index-count', $last_index + 2);
                // 		#store the index and the succeeding withdraw condition amount deducted
                // 		array_push($player_deducted_amt_index[$player_id], array('index' => $last_index, 'current_wc_total' => $current_wc_total));

                // 	}
                // }
                // }

                $cashback_amount = $this->utils->roundCurrencyForShow($pbbd->betting_total * ($rate / 100));

                $withdraw_condition_amount = $this->utils->roundCurrencyForShow($cashback_amount * $withdraw_condition_bet_times);

                $cnt++;

                $this->syncCashbackDaily($player_id, $game_platform_id, $game_description_id, $total_date,
                    $cashback_amount, $history_id, $game_type_id, $level_id, $rate,
                    $this->utils->roundCurrencyForShow($pbbd->betting_total),
                    $withdraw_condition_amount, $max_bonus, $original_bet_amount, self::NORMAL_CASHBACK);
            }
        }

		return $cnt;
	}

	function checkPlayerVIPCashbackWeekly($playerId, $currentDate) {

		$playerCashback = $this->checkPlayerCashbackPeriod($playerId);

		$currentDay = date('l', strtotime($currentDate));

		if (!empty($playerCashback)) {
			$period = $playerCashback['cashback_period'];

			if ($period != self::VIP_CASHBACK_DAILY) {
				$cashbackDay = $this->getDayCashbackSetting($period);
				if ($currentDay == $cashbackDay) {
					return true;
				}
			}
		}
		return false;
	}

	public function checkPlayerCashbackPeriod($playerId) {
		$this->db->select('vipsettingcashbackrule.vipLevel');
		$this->db->select('vipsettingcashbackrule.vipLevelName');
		$this->db->select('vipsettingcashbackrule.vipsettingcashbackruleId');
		$this->db->select('vipsettingcashbackrule.vipSettingId');
		$this->db->select('vipsettingcashbackrule.cashback_period');
		$this->db->select('vipsetting.groupName');
		$this->db->from('playerlevel');
		$this->db->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left');
		$this->db->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$this->db->where('playerlevel.playerId', $playerId);

		return $this->runOneRowArray();
	}

	public function getLevelById($levelId) {
		// $this->db->select($this->levelName);
		$this->db->from($this->levelTable)->where($this->levelId, $levelId);

		return $this->runOneRowArray();
		// $query = $this->db->get($this->levelTable);
		// $row = $query->row_array();
		// return $row ? $row[$this->levelName] : false;
	}

	public function getGroupById($groupId){
		$this->db->from($this->groupTable)->where($this->groupId, $groupId);

		return $this->runOneRowArray();
	}

	public function setGradeRecord($arr) {
		$paramsStandard = $this->getGradeRecordParamStandard();
		if (is_array($arr)) {
			foreach ($arr as $key => $val) {
				if (in_array($key, $paramsStandard)) {
					$this->grade_record[$key] = $val;
				}
			}
		}
	}

	public function gradeRecode($validate = true) {
		$isInsert = true;
		$paramsStandard = $this->getGradeRecordParamStandard();
		foreach ($paramsStandard as $val) {
			if ($validate && !isset($this->grade_record[$val])) {
				$isInsert = false;
			}
		}
		if ($isInsert) {
			$this->db->insert('vip_grade_report', $this->grade_record);
		}
	}

	public function getGradeRecordParamStandard() {
		return [
			'player_id', 'request_type', 'request_time', 'request_grade', 'level_from', 'level_to',
			'period_start_time', 'period_end_time', 'pgrm_start_time', 'pgrm_end_time', 'remark', 'vipsettingId',
			'vipsettingcashbackruleId', 'vipupgradesettingId','vipupgradesettinginfo', 'newvipId',
			'vipsettingcashbackruleinfo', 'status', 'updated_by'
		];
	}

	/**
	 * queryFirstGradeRecordId
	 * @param  string $type RECORD_UPGRADE or RECORD_DOWNGRADE
	 * @param  string $fromDate
	 * @param  string $toDate
	 * @param  string $fromLevelId
	 * @param  string $toLevelId
	 * @return int
	 */
	public function queryFirstGradeRecordIdBy($playerId, $type, $fromDate, $toDate, $fromLevelId, $toLevelId){

		$this->db->select('id')->from('vip_grade_report')
			->where('player_id', $playerId)
			->where('pgrm_end_time >=', $fromDate)
			->where('pgrm_end_time <=', $toDate)
			->where('vipsettingcashbackruleId', $fromLevelId)
			->where('newvipId', $toLevelId)
			->where('status', self::GRADE_SUCCESS)
			->order_by('pgrm_end_time', 'asc')
			->limit(1);

		return $this->runOneRowOneField('id');
	}

	public function queryLastGradeRecordRowBy($playerId, $fromDate, $toDate){

		$this->db->from('vip_grade_report')
			->where('player_id', $playerId)
			->where('pgrm_end_time >=', $fromDate)
			->where('pgrm_end_time <=', $toDate)
			->where('status', self::GRADE_SUCCESS)
			->order_by('pgrm_end_time', 'desc')
			->order_by('id', 'desc')
			->limit(1);

		return $this->runOneRowArray();
	}

	public function convertTypeToString($t){
		switch ($t) {
			case self::NORMAL_CASHBACK:
				return lang('Normal');
				break;
			case self::FRIEND_REFERRAL_CASHBACK:
				return lang('Referral');
				break;
		}
		return lang('Normal');

	}

    /**
     * Retain failed upgrade and downgrade data for the specified number of days
     * The default number of days is seven days
     *
     * @param integer
     * @return boolean
     */
    function retainFailedDataForSpecificDay($days=7)
    {

        $days = (is_int($days) ? $days : $this->utils->getConfig('retain_vip_grade_report_failed_data_for_specific_days'));

        if ($days > 0) {
            $date = date('Y-m-d', strtotime("-$days days"));
            $this->where('request_time <=', $date);
        }

        $this->db->where('status', 0);
        return $this->db->delete('vip_grade_report');
    }

    /**
     *
     * from $levelId down to first level
     * high level to low level
     * @param  int $levelId
     * @return array high level to low level
     *
     */
    public function getLevelListOnSameGroupFrom($levelId){
    	$levelIdList=[];
    	$this->db->from($this->levelTable)
    	    ->where($this->levelId, $levelId);
    	$row=$this->runOneRowArray();
    	if(!empty($row)){
	    	$groupId=$row[$this->groupId];
	    	$vipLevel=$row['vipLevel'];
    		//same group id and <=level id
    		$this->db->from($this->levelTable)->where($this->groupId, $groupId)
				->where('vipLevel <=', $vipLevel)->order_by('vipLevel', 'desc');
			$rows=$this->runMultipleRowArray();
			if(!empty($rows)){
				foreach ($rows as $row) {
					$levelIdList[]=$row[$this->levelId];
				}
			}
    	}

    	return $levelIdList;
    }

}

// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of group_level.php
///END OF FILE//////////
