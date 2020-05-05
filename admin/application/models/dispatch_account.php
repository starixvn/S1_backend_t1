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
class Dispatch_account extends BaseModel {

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

	protected $dispatch_account_group_table = 'dispatch_account_group';
	protected $groupId = 'vipSettingId';

	protected $dispatch_account_level_table = 'dispatch_account_level';
	protected $levelId = 'vipsettingcashbackruleId';
	protected $levelName = 'vipLevelName';
	protected $levelGroupId = 'vipSettingId';

	protected $dispatch_account_level_payment_account_table = 'dispatch_account_level_payment_account';

	protected $payment_account_table = 'payment_account';

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
	 * detail: get dispatch account group list
	 *
	 * @param string $sort
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return array or Boolean
	 */
	public function getDispatchAccountGroupList($sort, $limit, $offset) {
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
		$this->db->select('*')
			->from($this->dispatch_account_group_table)
			->order_by($sort, 'asc')
			->where('status', 1);
		if ($limit) {
			$this->db->limit($limit, $offset);
		}

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
	 * detail: get vip group details
	 *
	 * @param int $vipsettingId
	 * @return array or Boolean
	 */
	public function getDispatchAccountGroupDetails($group_id) {
		$this->db->from($this->dispatch_account_group_table);
		$this->db->where('id', $group_id);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->row_array();
		}
		return false;
	}

	/**
	 * detail: adding dispatch account group record
	 *
	 * @param array $data
	 * @retrurn Boolean
	 */
	public function addDispatchAccountGroup($data) {
		$this->utils->debug_log('============addDispatchAccountGroup dispatch_account_group data', $data);
		$dispatch_account_group['group_name'] = $data['group_name'];
		$dispatch_account_group['group_level_count'] = $data['group_level_count'];
		$dispatch_account_group['group_description'] = $data['group_description'];
		$dispatch_account_group['created_at'] = $data['created_at'];
		$dispatch_account_group['updated_at'] = $data['updated_at'];

		$this->utils->debug_log('============addDispatchAccountGroup dispatch_account_group', $dispatch_account_group);

		$result_add_group = $this->db->insert($this->dispatch_account_group_table, $dispatch_account_group);
		$result_add_level = false;

		if($result_add_group) {
			$group_id = $this->db->insert_id();
			$this->utils->debug_log('============addDispatchAccountGroup succeeded. insert id is: ', $group_id);

			$dispatch_account_level['group_id'] = $group_id;
			// this level is for reset.
			$reset_level = array(
				"group_id" => $group_id,
				"level_name" => 'Reset Level',
				"level_order" => 0,
				"created_at" => $data['created_at'],
				"updated_at" => $data['updated_at'],
				"level_observation_period" => $data['level_observation_period']
			);

			$result_add_reset_level = $this->addDispatchAccountLevel($reset_level);
			if(!$result_add_reset_level) {
				$this->utils->debug_log('============model addDispatchAccountLevel failed. result_add_reset_level is: ', $result_add_reset_level);
				return false;
			}

			for ($i = 1; $i < $data['group_level_count'] + 1; $i++) {
				$dispatch_account_level['group_id'] = $group_id;
				$dispatch_account_level['level_name'] = 'Level Name ' . $i;
				$dispatch_account_level['level_order'] = $i;
				$dispatch_account_level['level_member_limit'] = $data['level_member_limit'];
				$dispatch_account_level['level_single_max_deposit'] = $data['level_single_max_deposit'];
				$dispatch_account_level['level_total_deposit'] = $data['level_total_deposit'];
				$dispatch_account_level['level_deposit_count'] = $data['level_deposit_count'];
				$dispatch_account_level['level_total_withdraw'] = $data['level_total_withdraw'];
				$dispatch_account_level['level_withdraw_count'] = $data['level_withdraw_count'];
				$dispatch_account_level['created_at'] = $data['created_at'];
				$dispatch_account_level['updated_at'] = $data['updated_at'];
				$dispatch_account_level['level_observation_period'] = $data['level_observation_period'];

				$this->utils->debug_log('============enter model dispatch_account_level', $dispatch_account_level);

				$result_add_each_level = $this->addDispatchAccountLevel($dispatch_account_level);
				if(!$result_add_each_level) {
					$this->utils->debug_log('============model addDispatchAccountLevel failed. result_add_each_level is: ', $result_add_each_level);
					return false;
				}
			}
			$result_add_level = true;
		}
		else {
			$this->utils->debug_log('============model addDispatchAccountGroup failed. result_add_group is: ', $result_add_group);
		}

		return $result_add_group && $result_add_level;
	}

	/**
	 * detail: adding dispatch account level record
	 *
	 * @param array $data
	 * @return Boolean
	 */
	public function addDispatchAccountLevel($data, $return_id=false) {
		$result_add = $this->db->insert($this->dispatch_account_level_table, $data);
		if(!$return_id) {
			return $result_add;
		}
		return $this->db->insert_id() ?: false;
	}

	/**
	 * detail: update dispatch account group
	 *
	 * @param array $data
	 * @param int $group_id
	 *
	 * @return array
	 */
	public function editDispatchAccountGroup($data, $group_id) {
		$result_edit = $this->editData($this->dispatch_account_group_table,
			array('id' => $group_id),
			$data);

		if ($result_edit && isset($data['group_name'])) {
			// $this->editData('player',
			// 	array('level_id' => $group_id),
			// 	array('group_name' => $data['group_name']));
		}

		return $result_edit;
	}

	/**
	 * detail: soft delete vip group
	 *
	 * @param int $vipsettingId
	 * @return void
	 */
	public function setDispatchAccountGroupDisabled($group_id, $level_id_list=array()) {
		$default_dispatch_account_group_id = $this->utils->getConfig('default_dispatch_account_group_id');
		$default_dispatch_account_level_id = $this->utils->getConfig('default_dispatch_account_level_id');

		$this->startTrans();

		# DECREASE GROUP LEVEL COUNT
		$this->db->set('status', '0', false);
		$this->db->where('id', $group_id);
		$this->db->where('id !=', $default_dispatch_account_group_id); # VIP GROUP CANNOT HAVE ZERO LEVEL
		$this->db->update($this->dispatch_account_group_table);

		if ($this->db->affected_rows() && !empty($level_id_list)) {
			# DELETE LEVEL
			$this->db->set('status', '0', false);
			$this->db->where_in('id', $level_id_list);
			$this->db->update($this->dispatch_account_level_table);
		}

		$this->endTrans();
		return $this->succInTrans();
	}

	/**
	 * detail: get dispatch account level
	 *
	 * @param int $group_id
	 * @return array or Boolean
	 */
	public function getDispatchAccountLevelListByGroupId($group_id=null) {
		$this->db->select('dispatch_account_level.*,dispatch_account_group.group_name')->from('dispatch_account_level');
		$this->db->join('dispatch_account_group', 'dispatch_account_group.id = dispatch_account_level.group_id');
		if(!is_null($group_id)){
			$this->db->where('dispatch_account_group.id', $group_id);
		}
		$this->db->where('dispatch_account_level.status', '1');
		$this->db->order_by('dispatch_account_level.level_order', 'asc');

		$query = $this->db->get();
		$data = $query->result_array();
		$result = array();

		$cnt = 0;
		if ($query->num_rows() > 0) {
			foreach ($data as $row) {
				$this->db->select('count(playerId) player_count')->from('player')->where('dispatch_account_level_id', $row['id']);
				$row['player_count'] = $this->runOneRowOneField('player_count');
				$result[] = $row;
			}

			return $result;
		}
		return false;
	}

	/**
	 * detail: get last dispatch account level in the group
	 *
	 * @param int $group_id
	 * @return array or Boolean
	 */
	public function getLastDispatchAccountLevelByGroupId($group_id) {
		$all_list = $this->getDispatchAccountLevelListByGroupId($group_id);
		if($all_list != false) {
			$count = count($all_list);
			return $all_list[$count - 1];
		}
		return false;
	}

	/**
	 * detail: get dispatch account group level details
	 *
	 * @param int $vipgrouplevelId
	 * @return array
	 */
	public function getDispatchAccountLevelDetailsById($level_id) {
		$this->db->select('dispatch_account_level.*,dispatch_account_group.group_name')->from('dispatch_account_level');
		$this->db->join('dispatch_account_group', 'dispatch_account_level.group_id = dispatch_account_group.id');
		$this->db->where('dispatch_account_level.id', $level_id);
		$query = $this->db->get();
		return $query->row_array();
	}

	public function getLevelPaymentAccountsByLevelId($level_id) {
		$this->db->from($this->dispatch_account_level_payment_account_table);
		$this->db->where('dispatch_account_level_id', $level_id);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return array();
	}

	public function deleteLevelPaymentAccountsByLevelId($level_id) {
		$this->db->where('dispatch_account_level_id', $level_id);
		$this->db->delete($this->dispatch_account_level_payment_account_table);

		if($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * detail: update dispatch account level
	 *
	 * @param array $data
	 * @param int $group_id
	 *
	 * @return array
	 */
	public function editDispatchAccountLevel($data, $level_id) {

		$this->startTrans();

		$old_payment_account_sets = $this->getLevelPaymentAccountsByLevelId($level_id);
		$this->utils->debug_log('============editDispatchAccountLevel old_payment_account_sets', $old_payment_account_sets);

		if(count($old_payment_account_sets) > 0) {
			$result_delete = $this->deleteLevelPaymentAccountsByLevelId($level_id);
			$this->utils->debug_log('============editDispatchAccountLevel result_delete', $result_delete);
		}

		$level_payment_account_sets = array();
		$each_level_account_data = array();
		if(count($data['payment_accounts']) > 0) {
			foreach ($data['payment_accounts'] as $payment_account_id) {
				$each_level_account_data['dispatch_account_level_id'] = $level_id;
				$each_level_account_data['payment_account_id'] = $payment_account_id;
				$level_payment_account_sets[] = $each_level_account_data;
			}
		}

		//batch insert payment_account_id that belongs to the level_id
        if(count($level_payment_account_sets) > 0){
            $result_add_batch = $this->db->insert_batch($this->dispatch_account_level_payment_account_table, $level_payment_account_sets);
            $this->utils->debug_log('============editDispatchAccountLevel result_add_batch', $result_add_batch);
        }

		unset($data['payment_accounts']);
		$result_edit = $this->editData($this->dispatch_account_level_table, array('id' => $level_id),$data);
		$this->utils->debug_log('============editDispatchAccountLevel result_edit', $result_edit);

		$this->endTrans();
		return $this->succInTrans();
	}

	/**
	 * copy dispatch account level and add
	 * @param 	int
	 * @return	loaded view page
	 */
	public function copyDispatchAccountLevelByGroupId($group_id, $level_id = null, $copy_mode = false) {
		$insert_id = false;
		$this->db->trans_off();
		$this->db->trans_start();
		{
			# INCREASE GROUP LEVEL COUNT
			$this->db->set('group_level_count', 'group_level_count + 1', false);
			$this->db->where('id', $group_id);
			$this->db->update($this->dispatch_account_group_table);

			# ADD LEVEL
			$dispatch_account_group = $this->getDispatchAccountGroupDetails($group_id);
			$group_level_count = $dispatch_account_group['group_level_count'];

			$source_dispatch_account_level = array();

			if(!$dispatch_account_group || $group_level_count <= 0) {
				$this->utils->debug_log('============copyDispatchAccountLevelByGroupId error. Group not exist or no level in this group', $dispatch_account_group);
				return false;
			}

			if(is_null($level_id)) {
				$source_dispatch_account_level = $this->getLastDispatchAccountLevelByGroupId($group_id);
			}
			else {
				$source_dispatch_account_level = $this->getDispatchAccountLevelDetailsById($level_id);
			}

			if(!$source_dispatch_account_level) {
				$this->utils->debug_log('============copyDispatchAccountLevelByGroupId error. Level not exist not in this group', 'group_id: '.$group_id. ', level_id: '.$level_id);
				return false;
			}

			$today = $this->utils->getNowForMysql();
			$new_level_name = $copy_mode ? $source_dispatch_account_level['level_name']. ' Copy On '.$today : 'Level Name ' . $group_level_count;
			$new_level_order = $source_dispatch_account_level['level_order'] + 1;

			$insert_id = $this->addDispatchAccountLevel([
				'group_id' => $group_id,
				'level_name' => $new_level_name,
				'level_order' => $new_level_order ,
				'level_member_limit' => $source_dispatch_account_level['level_member_limit'],
				'level_single_max_deposit' => $source_dispatch_account_level['level_single_max_deposit'],
				'level_total_deposit' => $source_dispatch_account_level['level_total_deposit'],
				'level_deposit_count' => $source_dispatch_account_level['level_deposit_count'],
				'level_total_withdraw' => $source_dispatch_account_level['level_total_withdraw'],
				'level_withdraw_count' => $source_dispatch_account_level['level_withdraw_count'],
				'created_at' => $today,
				'created_at' => $today
			], true);

			if($insert_id != false && $copy_mode) {
				$this->plusOneDispatchAccountLevelOrderByLevelIdAndLevelOrder($new_level_order, $insert_id, $group_id);
			}
		}
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
			return false;
		}
		return $insert_id;
	}

	/**
	 * detail: get the levels
	 *
	 * @param int $vipSettingId
	 * @return void
	 */
	public function plusOneDispatchAccountLevelOrderByLevelIdAndLevelOrder($level_order, $level_id, $group_id) {
		$this->startTrans();

		$this->db->set('level_order', 'level_order + 1', false);
		$this->db->where('level_order >=', $level_order);
		$this->db->where('id !=', $level_id);
		$this->db->where('group_id', $group_id);
		$this->db->update($this->dispatch_account_level_table);

		$this->endTrans();
		return $this->succInTrans();
	}

	/**
	 * detail: set the status of dispatch account level to be 0
	 *
	 * @param int $vipSettingId
	 * @return void
	 */
	public function setDispatchAccountLevelDisabledByLevelId($level_id, $group_id) {
		$this->startTrans();

		# DECREASE GROUP LEVEL COUNT
		$this->db->set('group_level_count', 'group_level_count - 1', false);
		$this->db->where('id', $group_id);
		$this->db->where('group_level_count !=', 1); # VIP GROUP CANNOT HAVE ZERO LEVEL
		$this->db->update($this->dispatch_account_group_table);

		if ($this->db->affected_rows()) {
			# DELETE LEVEL
			$this->db->set('status', '0', false);
			$this->db->where('id', $level_id);
			$this->db->update($this->dispatch_account_level_table);
		}

		$this->endTrans();
		return $this->succInTrans();
	}

	/**
	 * detail: check if there are players in the dispatch account level
	 *
	 * @param int $level_id
	 * @return boolean
	 */
	public function getPlayerListInLevelByLevelId($level_id) {
		$this->db->select('playerId, username')->from('player')->where('dispatch_account_level_id', $level_id);
		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * detail: get payment accounts by dispatch account level id
	 *
	 * @param int $level_id
	 * @return array
	 */
	public function getCorrespondPaymentAccountsByLevelId($level_id) {
		$this->db->select('payment_account.id,
						   payment_account.payment_account_name')
			->from($this->payment_account_table)
			->join('dispatch_account_level', 'dispatch_account_level.id = ' . $this->payment_account_table . '.player_level_id', 'left')
			->join('dispatch_account_group', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->join('payment_account', 'payment_account.id = ' . $this->payment_account_table . '.payment_account_id', 'left');
		$this->db->where($this->payment_account_table . '.player_level_id', $level_id);

		$query = $this->db->get();

		return $this->getMultipleRow($query);
	}

	public function getDispatchAccountGroupIdsWithLevelOrders() {
		$result = array();
		$all_level_list = $this->getDispatchAccountLevelListByGroupId();

		foreach ($all_level_list as $each_level) {
			$result[$each_level['group_id']][] = $each_level;
		}
		return $result;
	}

	public function getZeroOrderLevelByGroupId($group_id) {
		$this->db->select('dispatch_account_level.*')
			->from($this->dispatch_account_level_table)
			->where('group_id', $group_id)
			->where('level_order', '0');

		$query = $this->db->get();
		return $query->row_array();
	}
}

///END OF FILE//////////