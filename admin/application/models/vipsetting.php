<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * MOVE TO group_level.php
 *
 * vipsetting
 *
 * This model represents ip data. It operates the following tables:
 * - vipsetting
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
 * @author	ASRII
 */
require_once dirname(__FILE__) . '/base_model.php';

class Vipsetting extends BaseModel {
	const DEPOSIT_FIRST = 1;
	const DEPOSIT_SUCCEEDING = 2;
	const BONUS_MODE_ENABLE = 1;
	const BONUS_MODE_DISABLE = 0;
	const BONUS_TYPE_FIXAMOUNT = 1;
	const BONUS_TYPE_BYPERCENTAGE = 2;

	protected $tableName = 'vipsetting';
	protected $tableVipGroupPayout = 'vipgrouppayoutsetting';
	protected $tableCashbackGame = 'vipsetting_cashback_game';
	protected $tableCashbackBonusPerGame = 'vipsettingcashbackbonuspergame';
	protected $tableCashbackRule = 'vipsettingcashbackrule';

	function __construct() {
		parent::__construct();
	}

	/**
	 * Inserts data to vipsetting
	 * moved to group_level
	 *
	 * @param	array
	 * @return	boolean
	 */
	public function addVIPGroup($data) {
		$this->db->insert('vipsetting', $data);

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
	 * Inserts data to createVipCashbackGameRule
	 *
	 * @param	array
	 * @return	boolean
	 */
	public function createVipCashbackGameRule($data) {
		$this->db->insert('vipsettingcashbackrule', $data);
		$lastInsertId = $this->db->insert_id();
		$vipdata['vipsettingcashbackruleId'] = $lastInsertId;
		$vipdata['percentage'] = 10;
		$vipdata['maxBonus'] = 1000;
		$vipdata['gameType'] = 1;
		$this->createVipCashbackBonusPerGame($vipdata);

		$availableGame = $this->getAllAvailableGame();
		foreach ($availableGame as $key) {
			$game_data['vipsetting_cashbackrule_id'] = $lastInsertId;
			$game_data['game_description_id'] = $key['id'];
			$this->addCashbackAllowedGame($game_data);
		}
	}

	/**
	 * Inserts data to vipsetting
	 *
	 * @param	array
	 * @return	boolean
	 */
	public function addCashbackAllowedGame($game_data) {
		$this->db->insert('vipsetting_cashback_game', $game_data);
	}

	/**
	 * getAllAvailableGame
	 *
	 * @param	int
	 * @return 	array
	 */
	public function getAllAvailableGame() {
		$this->db->select('id')->from('game_description');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function getAllCanJoinIn() {
		$this->db->select('vipsetting.vipSettingId');
		$this->db->select('vipsettingcashbackrule.vipsettingcashbackruleId');
		$this->db->select('vipsetting.groupName');
		$this->db->select('vipsettingcashbackrule.vipLevelName');
		$this->db->select('vipsetting.groupDescription');
		$this->db->from('vipsetting');
		$this->db->join('vipsettingcashbackrule','vipsettingcashbackrule.vipSettingId = vipsetting.vipSettingId','left');
		$this->db->where('vipsetting.can_be_self_join_in', self::DB_TRUE);
		$this->db->order_by('vipsetting.groupName', 'asc');
		$this->db->order_by('vipsettingcashbackrule.vipLevelName', 'asc');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function getAllCanJoinInGroup() {// this reuturn only 1st level
		$this->db->select('vipsetting.vipSettingId');
		$this->db->select('vipsettingcashbackrule.vipsettingcashbackruleId');
		$this->db->select('vipsetting.groupName');
		$this->db->select('vipsettingcashbackrule.vipLevelName');
		$this->db->select('vipsetting.groupDescription');
		$this->db->from('vipsetting');
		$this->db->join('vipsettingcashbackrule','vipsettingcashbackrule.vipSettingId = vipsetting.vipSettingId','left');
		$this->db->where('vipsetting.can_be_self_join_in', self::DB_TRUE);
		$this->db->where('vipsettingcashbackrule.vipLevel','1');
		$this->db->where('vipsetting.deleted','0');
		$this->db->order_by('vipsetting.groupName', 'asc');
		$this->db->order_by('vipsettingcashbackrule.vipLevelName', 'asc');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * Inserts data to createVipCashbackBonusPerGame
	 *
	 * @param	array
	 * @return	boolean
	 */
	public function createVipCashbackBonusPerGame($data) {
		$this->db->insert('vipsettingcashbackbonuspergame', $data);
	}

	/**
	 * get data to getVIPGroupRules
	 *
	 * @param	array
	 * @return	boolean
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
	 * get data to get cash back payout setting
	 *
	 * @return	array
	 */
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

	/**
	 * getCashbackPayoutTimeSetting
	 *
	 * @return	array
	 */
	public function getCashbackPayoutTimeSetting() {
		$this->db->select('setTime')->from('vipgrouppayoutsetting');

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['setTime'] = mdate('%H:%i:%s', strtotime($row['setTime']));
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * get data to getVIPGroupRules
	 *
	 * @param	array
	 * @return	boolean
	 */
	public function getVIPGroupGamesRule($vipsettingcashbackruleId) {
		$this->db->select('vipsettingcashbackbonuspergame.*,game.game')
			->from('vipsettingcashbackbonuspergame')
			->join('game', 'game.gameId = vipsettingcashbackbonuspergame.gameType');
		$this->db->where('vipsettingcashbackruleId', $vipsettingcashbackruleId);
		$this->db->where('allowStatus', '0');

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
	 * Will get tag based on the name of the tag
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function getVipGroupName($group_name) {

		$query = $this->db->query("SELECT * FROM vipsetting WHERE groupName = '" . $group_name . "'");

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	/**
	 * Will get tag based on the name of the tag
	 *
	 * @param 	string
	 * @return 	array
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
	 * Will delete vip group level
	 *
	 * @param 	int
	 */
	public function deletevipgrouplevel($vipgrouplevelId) {
		$this->db->where('vipsettingcashbackruleId', $vipgrouplevelId);
		$this->db->delete('vipsettingcashbackrule');
	}

	/**
	 * Will delete vip group level
	 *
	 * @param 	int
	 */
	public function increaseVipGroupLevel($vipSettingId) {
		$this->db->trans_off();
		$this->db->trans_start();
		{
			# INCREASE GROUP LEVEL COUNT
			$this->db->set('groupLevelCount', 'groupLevelCount + 1', false);
			$this->db->where('vipSettingId', $vipSettingId);
			$this->db->update('vipsetting');

			# ADD LEVEL
			$vip_group = $this->getVIPGroupDetails($vipSettingId);
			$vipLevel = $vip_group[0]['groupLevelCount'];
			$this->vipsetting->createVipCashbackGameRule([
				'vipsettingId' => $vipSettingId,
				'minDeposit' => 100 * $vipLevel,
				'maxDeposit' => 1000 * $vipLevel,
				'dailyMaxWithdrawal' => 10000 * $vipLevel,
				'vipLevel' => $vipLevel,
				'vipLevelName' => 'Level Name ' . $vipLevel,
			]);
		}
		$this->db->trans_complete();
	}

	/**
	 * Will delete vip group level
	 *
	 * @param 	int
	 */
	public function decreaseVipGroupLevel($vipSettingId) {
		$this->db->trans_off();
		$this->db->trans_begin();

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
				$this->db->delete('vipsettingcashbackbonuspergame', ['vipsettingcashbackruleId' => $row['vipsettingcashbackruleId']]);
				$this->db->delete('vipsetting_cashback_game', ['vipsetting_cashbackrule_id' => $row['vipsettingcashbackruleId']]);
				$this->db->trans_commit(); # COMMIT IF DELETE IS SUCCESSFUL
				return true;
			}
		}
		$this->db->trans_rollback();
		return false;
	}

	/**
	 * get all affiliates
	 *
	 * @return 	array
	 */
	public function getRankingGroupOfPlayer($player_level) {
		$query = $this->db->query("SELECT rankingLevelGroup
						   			FROM rankinglevelsetting
						   			INNER JOIN adminusers ON adminusers.`userId` = rankinglevelsetting.`setBy`
						  			WHERE rankinglevelsetting.`status` = 0
						  			AND rankinglevelsetting.rankingLevelSettingId = '" . $player_level . "'");
		return $query->row_array();
	}

	/**
	 * get vipsettingId if its using or child of roleId
	 *
	 * @param	int
	 * @return 	array
	 */
	public function getVIPGroupDetails($vipsettingId) {
		$this->db->select('*')->from('vipsetting');
		$this->db->where('vipsettingId', $vipsettingId);

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
	 * activate vip group
	 *
	 * @return	$array
	 */
	public function activateVIPGroup($data) {
		$this->db->where('vipsettingId', $data['vipsettingId']);
		$this->db->update('vipsetting', $data);
	}

	/**
	 * edit vip group
	 *
	 * @return	$array
	 */
	public function editVIPGroup($data, $vipsettingId) {
		$this->db->where('vipsettingId', $vipsettingId);
		$this->db->update('vipsetting', $data);
	}

	/**
	 * edit cashback payout setting
	 *
	 * @return	$array
	 */
	public function editCashbackPeriodSetting($data, $vipGroupPayoutSettingId) {
		$this->db->where('vipGroupPayoutSettingId', $vipGroupPayoutSettingId);
		$this->db->update('vipgrouppayoutsetting', $data);
	}

	/**
	 * edit vip group bonus rule
	 * MOVED TO group_level
	 * @return	$array
	 */
	public function editVipGroupBonusRule($data) {
		$this->db->where('vipsettingcashbackruleId', $data['vipsettingcashbackruleId']);
		$this->db->update('vipsettingcashbackrule', $data);
	}

	/**
	 * edit vip group bonus per game
	 * MOVED TO group_level
	 * @return	$array
	 */
	public function editVipGroupBonusPerGame($data) {
		$this->db->where('vipsettingcashbackbonuspergameId', $data['vipsettingcashbackbonuspergameId']);
		$this->db->update('vipsettingcashbackbonuspergame', $data);
	}

	/**
	 * delete vip group
	 *
	 * @return	$array
	 */
	public function deleteVIPGroup($vipsettingId) {
		$this->db->where('vipsettingId', $vipsettingId);
		$this->db->delete('vipsetting');

		//$this->deleteVIPGroupLevelItems($vipsettingId);

		$data = $this->getVipGroupItemRules($vipsettingId);
		foreach ($data as $key => $value) {
			$this->deleteVIPGroupLeveRulelItems($value['vipsettingcashbackruleId']);
			$this->updatePlayerLevel($value['vipsettingcashbackruleId']);
		}
		if ($this->db->affected_rows() == '1') {
			$this->deleteVIPGroupLevelItems($vipsettingId);
		}
	}

	/**
	 * update player levels
	 *
	 * @return	$array
	 */
	public function updatePlayerLevel($playerGroupId) {
		$newPlayerLevel = array('playerGroupId' => 1);
		$this->db->where('playerGroupId', $playerGroupId);
		$this->db->update('playerlevel', $newPlayerLevel);
	}

	/**
	 * get getVipGroupItemRules
	 *
	 * @return	$array
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
	 * delete deleteVIPGroupLevelItems
	 *
	 * @return	$array
	 */
	public function deleteVIPGroupLeveRulelItems($vipsettingcashbackruleId) {
		$this->db->where('vipsettingcashbackruleId', $vipsettingcashbackruleId);
		$this->db->delete('vipsettingcashbackbonuspergame');
	}

	/**
	 * delete deleteVIPGroupLevelItems
	 *
	 * @return	$array
	 */
	public function deleteVIPGroupLevelItems($vipsettingId) {
		$this->db->where('vipSettingId', $vipsettingId);
		$this->db->delete('vipsettingcashbackrule');
	}

	/**
	 * delete vip group item
	 *
	 * @return	$array
	 */
	public function deleteVIPGroupItem($vipsettingId) {
		$this->db->where('vipsettingId', $vipsettingId);
		$this->db->delete('vipsetting');
	}

	/**
	 * Get VIP setting List
	 *
	 * @return	$array
	 */
	public function getVIPSettingList($sort, $limit, $offset) {
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
			ORDER BY vipsetting." . $sort . " ASC
			$limit
			$offset
		");

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['createdOn'] = mdate('%M %d, %Y - %h:%i:%s %A', strtotime($row['createdOn']));
				if ($row['updatedOn'] != null) {
					$row['updatedOn'] = mdate('%M %d, %Y - %h:%i:%s %A', strtotime($row['updatedOn']));
				}
				$data[] = $row;
			}
			return $data;
		}
		return false;

	}

	/**
	 * Get VIP setting List
	 *
	 * @return	$array
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
							')->from('vipsetting')
			->join('adminusers', 'adminusers.userId = vipsetting.createdBy');

		$query = $this->db->get();
		return $query;
	}

	/**
	 * Will search vip group list
	 *
	 * @return 	array
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
			WHERE vipsetting.groupName = '" . $search . "'
			$limit
			$offset
		");
		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * Will get tag based on the name of the tag
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function getCashbackBonusPerGame($vipsettingcashbackruleId) {

		$query = $this->db->query("SELECT vipsettingcashbackbonuspergame.*, game.game FROM vipsettingcashbackbonuspergame
								   LEFT JOIN game
								   ON game.gameId = vipsettingcashbackbonuspergame.gameType
								   WHERE vipsettingcashbackruleId = ?", $vipsettingcashbackruleId);

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * Will get vip group list
	 *
	 * @param $limit
	 * @param $offset
	 * @return array
	 */
	public function getVipGroupList() {
		$this->db->select('vipsettingId,groupName')
			->from('vipsetting')->where('vipsetting.status', 'active')
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

	public function getPlayerGroup() {
		$this->db->select('vipSettingId,groupName')->from('vipsetting')->where('vipsetting.status', 'active');
		$this->db->order_by('vipsetting.groupName', 'ASC');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function getAllPlayerLevels() {
		$this->db->select('vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipsettingcashbackruleId,
						   vipsetting.vipSettingId,
						   vipsetting.groupName,
						   CONCAT_WS(\' - \', vipsetting.groupName,vipsettingcashbackrule.vipLevelName) groupLevelName', FALSE)
			->from('vipsettingcashbackrule')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->where('vipsetting.deleted', 0);
		$this->db->order_by('groupLevelName', 'ASC');
		$query = $this->db->get();
		return $query->result_array();
	}


	public function selectVipsettingcashbackrule(){
		$today = date("Y-m-d H:i:s");
		$sql = 'SELECT * FROM vipsettingcashbackrule';
		$query = $this->db->query($sql);
		return array(
				'total' => $query->num_rows(),
				'data' => $query->result_array()
			);
	}

	public function getAllvipsetting(){
		$sql = "SELECT * FROM vipsetting";
		return $this->db->query($sql)->result_array();
	}

	public function getAllvipgrouppayoutsetting(){
		$sql = "SELECT * FROM vipgrouppayoutsetting";
		return $this->db->query($sql)->result_array();
	}

	public function getAllvipsetting_cashback_game(){
		$sql = "SELECT * FROM vipsetting_cashback_game";
		return $this->db->query($sql)->result_array();
	}

	public function getAllvipsettingcashbackbonuspergame(){
		$sql = "SELECT * FROM vipsettingcashbackbonuspergame";
		return $this->db->query($sql)->result_array();
	}

	public function getAllvipsettingcashbackrule(){
		$sql = "SELECT * FROM vipsettingcashbackrule";
		return $this->db->query($sql)->result_array();
	}

	public function addRecord($data) {
		$sql = "SET FOREIGN_KEY_CHECKS = 0";
		$this->db->query($sql);
		$return = $this->db->insert($this->tableName, $data);
		$sql = "SET FOREIGN_KEY_CHECKS = 1";
		$this->db->query($sql);
		return $return;
	}

	public function addVipGroupPayout($data) {
		$sql = "SET FOREIGN_KEY_CHECKS = 0";
		$this->db->query($sql);
		$return = $this->db->insert($this->tableVipGroupPayout, $data);
		$sql = "SET FOREIGN_KEY_CHECKS = 1";
		$this->db->query($sql);
		return $return;
	}

	public function addCashbackGame($data) {
		$sql = "SET FOREIGN_KEY_CHECKS = 0";
		$this->db->query($sql);
		$return = $this->db->insert($this->tableCashbackGame, $data);
		$sql = "SET FOREIGN_KEY_CHECKS = 1";
		$this->db->query($sql);
		return $return;
	}

	public function addCashbackBonusPerGame($data) {
		$sql = "SET FOREIGN_KEY_CHECKS = 0";
		$this->db->query($sql);
		$return = $this->db->insert($this->tableCashbackBonusPerGame, $data);
		$sql = "SET FOREIGN_KEY_CHECKS = 1";
		$this->db->query($sql);
		return $return;
	}

	public function addCashbackRule($data) {
		$sql = "SET FOREIGN_KEY_CHECKS = 0";
		$this->db->query($sql);
		$return = $this->db->insert($this->tableCashbackRule, $data);
		$sql = "SET FOREIGN_KEY_CHECKS = 1";
		$this->db->query($sql);
		return $return;
	}

	public function truncateTablesSync($secret_key){
		if($secret_key=='Ch0wK1ing&M@ng!n@s@l'){
			$sql = "SET FOREIGN_KEY_CHECKS = 0";
			$this->db->query($sql);
			$this->db->truncate($this->tableName);
			$this->db->truncate($this->tableVipGroupPayout);
			$this->db->truncate($this->tableCashbackGame);
			$this->db->truncate($this->tableCashbackBonusPerGame);
			$this->db->truncate($this->tableCashbackRule);
			$sql = "SET FOREIGN_KEY_CHECKS = 1";
			$this->db->query($sql);

			return array('success'=>1);
		}
		return array('success'=>0);
	}

    /**
     * Will update vip level badge
     *
     * @param $vipLevelId
     * @param $badgeFilename
     */
    public function updateVipLevelBadge($vipLevelId,$badgeFilename){
        $updateData=array("badge"=>$badgeFilename);
        $this->db->where("vipsettingcashbackruleId",$vipLevelId);
        $this->db->update($this->tableCashbackRule,$updateData);
        //$data = $this->db->get();

        if ($this->db->_error_message()) {
            return FALSE; // Or do whatever you gotta do here to raise an error
        } else {
             return $this->db->affected_rows();
        }
    }

}

/* End of file vipsetting.php */
/* Location: ./application/models/vipsetting.php */
