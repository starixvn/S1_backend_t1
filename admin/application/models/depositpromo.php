<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * Depositpromo
 *
 * This model represents Depositpromo.
 *
 * @author	ASRII
 */

class Depositpromo extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	/**
	 * Will get all depositpromo
	 *
	 * @return array
	 */

	public function getAllDepositPromo($sort, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}
		$qry = "SELECT promorules.*,admin1.username AS createdBy, admin2.username AS updatedBy
			FROM promorules
			LEFT JOIN adminusers AS admin1
			ON admin1.userId = promorules.createdBy
			LEFT JOIN adminusers AS admin2
			ON admin2.userId = promorules.updatedBy
			WHERE promorules.deleted_flag IS NULL
			ORDER BY promorules." . $sort . " ASC
			$limit
			$offset";
		$query = $this->db->query("$qry");

		$cnt = 0;
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['createdOn'] = mdate('%M %d, %Y - %h:%i:%s %A', strtotime($row['createdOn']));
				if ($row['updatedOn'] != null) {
					$row['updatedOn'] = mdate('%M %d, %Y - %h:%i:%s %A', strtotime($row['updatedOn']));
				}
				$data[] = $row;
				$data[$cnt]['depositPromoPlayerLevelLimit'] = $this->getDepositPromoPlayerLevelLimit($row['promorulesId']);
				$cnt++;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will search DepositPromo
	 *
	 * @return 	array
	 */
	public function searchDepositPromoList($search, $limit, $offset) {

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}
		$qry = "SELECT promorules.*,admin1.username AS createdBy, admin2.username AS updatedBy
					FROM promorules
					LEFT JOIN adminusers AS admin1
					ON admin1.userId = promorules.createdBy
					LEFT JOIN adminusers AS admin2
					ON admin2.userId = promorules.updatedBy
					WHERE promorules.promoName = '{$search}'
					AND promorules.deleted_flag IS NULL
					ORDER BY promorules.promorulesId ASC
					$limit
					$offset";
		if ($search != '') {
			$query = $this->db->query("$qry");
		} else {
			$qry = "SELECT promorules.*,admin1.username AS createdBy, admin2.username AS updatedBy
					FROM promorules
					LEFT JOIN adminusers AS admin1
					ON admin1.userId = promorules.createdBy
					LEFT JOIN adminusers AS admin2
					ON admin2.userId = promorules.updatedBy
					WHERE promorules.deleted_flag IS NULL
					ORDER BY promorules.promorulesId ASC
					$limit
					$offset";
			$query = $this->db->query("$qry");
		}

		$cnt = 0;
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['createdOn'] = mdate('%M %d, %Y - %h:%i:%s %A', strtotime($row['createdOn']));
				if ($row['updatedOn'] != null) {
					$row['updatedOn'] = mdate('%M %d, %Y - %h:%i:%s %A', strtotime($row['updatedOn']));
				}
				$data[] = $row;
				$data[$cnt]['depositPromoPlayerLevelLimit'] = $this->getDepositPromoPlayerLevelLimit($row['promorulesId']);
				$cnt++;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Get depositpromo setting List
	 *
	 * @return	$array
	 */
	public function getDepositPromoListToExport() {
		$this->db->select('*')->from('promorules')
			->join('adminusers as admin1', 'admin1.userId = promorules.createdBy')
			->join('adminusers as admin2', 'admin2.userId = promorules.createdBy')
			// git issue #1371
			->where('deleted_flag IS NULL', null);

		$query = $this->db->get();
		return $query;
	}

	/**
	 * Will get getDepositPromoLastRankOrder
	 *
	 * @return array
	 */

	public function getDepositPromoLastRankOrder() {
		$this->db->select('accountOrder')->from('promorules');
		$this->db->where('deleted_flag IS NULL', null);
		$this->db->order_by('promorules.accountOrder', 'desc');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * Will get getDepositPromoLastRankOrder
	 *
	 * @return array
	 */

	public function getAllDepositPromoName() {
		$this->db->select('promorulesId,promoName')->from('promorules');
		$this->db->where('deleted_flag IS NULL', null);
		$this->db->order_by('promorules.promorulesId', 'desc');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * Will get getDepositPromoBackupLastRankOrder
	 *
	 * @return array
	 */

	public function getDepositPromoBackupLastRankOrder() {
		$this->db->select('accountOrder')->from('depositpromobackup');
		$this->db->order_by('depositpromobackup.accountOrder', 'desc');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * Will get getPlayerGroup
	 *
	 * @return array
	 */

	public function getPlayerGroup() {
		$this->db->select('vipSettingId,groupName')->from('vipsetting');
		$this->db->order_by('vipsetting.groupName', 'ASC');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * Will get all player Levels
	 *
	 * @return array
	 */

	public function getAllPlayerLevels() {
		$this->db->select('vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsettingcashbackrule.vipsettingcashbackruleId,
						   vipsetting.vipSettingId,
						   vipsetting.groupName')
			->from('vipsettingcashbackrule')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$this->db->order_by('vipsettingcashbackrule.vipsettingcashbackruleId', 'ASC');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * get getDepositPromoDetails
	 *
	 * @param	int
	 * @return 	array
	 */
	public function getDepositPromoDetails($depositPromoId) {
		$this->db->select('promorules.*')->from('promorules');
		$this->db->where('promorrulesId', $depositPromoId);
		$this->db->where('deleted_flag IS NULL', null);
		$query = $this->db->get();

		$cnt = 0;
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['promoPeriodStart'] = mdate('%Y-%m-%d', strtotime($row['promoPeriodStart']));
				$row['promoPeriodEnd'] = mdate('%Y-%m-%d', strtotime($row['promoPeriodEnd']));
				$data[] = $row;
				$data[$cnt]['depositPromoPlayerLevelLimit'] = $this->getDepositPromoPlayerLevelLimit($row['promorulesId']);
				$cnt++;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get deposit/withdrawal details
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function getDepositPromoPlayerLevelLimit($depositPromoId) {
		$this->db->select('vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName')
			->from('promorulesallowedplayerlevel')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = promorulesallowedplayerlevel.playerLevelId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$this->db->where('promorulesallowedplayerlevel.promoruleId', $depositPromoId);

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * Will add DepositPromo account
	 *
	 * @param $data array
	 * @return array
	 */
	public function addDepositPromo($data, $playerLevels) {
		$this->db->insert('promorules', $data);
		$depositPromoId = $this->db->insert_id();
		foreach ($playerLevels as $key) {
			$depositPromoPlayerLevelsLimit['depositpromoId'] = $depositPromoId;
			$depositPromoPlayerLevelsLimit['playerLevelId'] = $key; //link to vipsettingcashbackrule table (vipsettingcashbackruleId)
			$this->addDepositPromoPlayerLevelsLimit($depositPromoPlayerLevelsLimit);
		}
	}

	/**
	 * edit vip group
	 *
	 * @return	$array
	 */
	public function editDepositPromo($data, $playerLevels, $depositPromoId) {
		$this->db->where('promorulesId', $depositPromoId);
		$this->db->update('promorules', $data);

		if ($playerLevels != null) {
			//clear record
			$this->deleteDepositPromoPlayerLevelLimit($depositPromoId);

			//add new record
			foreach ($playerLevels as $key) {
				$depositPromoPlayerLevelsLimit['depositpromoId'] = $depositPromoId;
				$depositPromoPlayerLevelsLimit['playerLevelId'] = $key; //link to vipsettingcashbackrule table (vipsettingcashbackruleId)
				$this->addDepositPromoPlayerLevelsLimit($depositPromoPlayerLevelsLimit);
			}
		}
	}

	/**
	 * Will add DepositPromo
	 *
	 * @param $data array
	 * @return array
	 */

	public function addDepositPromoPlayerLevelsLimit($data) {
		$this->db->insert('promorulesallowedplayerlevel', $data);
	}

	/**
	 * Will delete DepositPromo
	 *
	 * @param 	int
	 */
	public function deleteDepositPromos($depositpromoId) {
		// git issue #1371, implement soft delete for promos
		$updateset = ['deleted_flag' => 1];
		$where = "promorulesId = '" . $depositpromoId . "'";
		$this->db->where($where);
		$this->db->update('promorules', $updateset);
	}

	/**
	 * Will delete DepositPromo
	 *
	 * @param 	int
	 */
	public function deletePromoRules($promorulesId) {
		// git issue #1371, implement soft delete for promos
		$updateset = ['deleted_flag' => 1];
		$where = "promorulesId = '" . $promorulesId . "'";
		$this->db->where($where);
		$this->db->update('promorules', $updateset);
		$this->clearPromoItems($promorulesId);
	}

	/**
	 * Will delete DepositPromo player level limit
	 *
	 * @param 	int
	 */
	public function deleteDepositPromoPlayerLevelLimit($depositPromoId) {
		$where = "promoruleId = '" . $depositPromoId . "'";
		$this->db->where($where);
		$this->db->delete('promorulesallowedplayerlevel');
	}

	/**
	 * delete DepositPromo item
	 *
	 * @return	$array
	 */
	public function deleteDepositPromoItem($depositpromoId) {
		// git issue #1371, convert to soft deletion
		$updateset = ['deleted_flag' => 1];
		$this->db->where('promorulesId', $depositpromoId);
		$this->db->update('promorules', $updateset);

		//delete deposit player level limit on depositpromoplayerlevellimit table
		$data = $this->getDepositPromoPlayerLevelLimitItem($depositpromoId);
		//var_dump($data);exit();
		if (!empty($data)) {
			foreach ($data as $key => $value) {
				$this->deleteDepositPromoLevelRuleItems($value['depositPromoPlayerLevelLimitId']);
				//must update player record who joined this group (playerpromo table)
			}
		}

		// update promo connection with promocmssetting - removed in git issue #1371
		// - This operation cuts promocms-promorules mapping, which is to be keeped in soft deletion scheme
		// $data = $this->getPromoCmsSettingPromoLink($depositpromoId);
		// if (!empty($data)) {
		// 	foreach ($data as $key => $value) {
		// 		$this->updatePromoCmsSettingPromoLink($value['promoCmsSettingId']);
		// 	}
		// }
	}

	/**
	 * updatePromoCmsSettingPromoLink
	 *
	 * @return	$array
	 */
	public function updatePromoCmsSettingPromoLink($promoCmsSettingId) {
		$data['promoId'] = null;
		$this->db->where('promoCmsSettingId', $promoCmsSettingId);
		$this->db->update('promocmssetting', $data);
	}

	/**
	 * get getDepositPromoPlayerLevelLimitItem
	 *
	 * @return	$array
	 */
	public function getPromoCmsSettingPromoLink($depositPromoId) {
		$this->db->select('promoCmsSettingId')->from('promocmssetting');
		$this->db->where('promoId', $depositPromoId);
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
	 * get getDepositPromoPlayerLevelLimitItem
	 *
	 * @return	$array
	 */
	public function getDepositPromoPlayerLevelLimitItem($depositPromoId) {
		$this->db->select('promorulesallowedplayerlevel')->from('depositpromoplayerlevellimit');
		$this->db->where('promoruleId', $depositPromoId);
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
	public function deleteDepositPromoLevelRuleItems($depositPromoPlayerLevelLimitId) {
		$this->db->where('promorulesallowedplayerlevelId', $depositPromoPlayerLevelLimitId);
		$this->db->delete('promorulesallowedplayerlevel');
	}

	/**
	 * activate DepositPromo item
	 *
	 * @return	$array
	 */
	public function activateDepositPromo($data) {
		$this->db->where('promorulesId', $data['depositpromoId']);
		$this->db->update('promorules', $data);
	}

	/**
	 * activatePromoRule
	 *
	 * @return	$array
	 */
	public function activatePromoRule($data) {
		$this->db->where('promorulesId', $data['promorulesId']);
		$this->db->update('promorules', $data);
	}

	/**
	 * check if promocode already exists
	 *
	 * @return Bool - TRUE or FALSE
	 */

	public function isPromoCodeExists($promocode) {
		$this->db->select('promoCode')->where('promoCode', $promocode);
		$this->db->where('deleted_flag IS NULL', null);
		$query = $this->db->get('promorules');

		if ($query->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * check if promo name already exists
	 *
	 * @return Bool - TRUE or FALSE
	 */

	public function isPromoNameExists($promoName) {
		$this->db->select('promoName')->where('promoName', $promoName);
		$this->db->where('deleted_flag IS NULL', null);
		$query = $this->db->get('promorules');

		if ($query->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * getPromoType
	 *
	 * @return	$array
	 */
	public function getPromoType() {
		$qry = "SELECT promotype.*,admin1.username AS createdBy, admin2.username AS updatedBy
			FROM promotype
			LEFT JOIN adminusers AS admin1
			ON admin1.userId = promotype.createdBy
			LEFT JOIN adminusers AS admin2
			ON admin2.userId = promotype.updatedBy
			ORDER BY promotype.promotypeId ASC";
		$query = $this->db->query("$qry");

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['createdOn'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['createdOn']));
				$row['updatedOn'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['updatedOn']));
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	/**
	 * getPromoType
	 *
	 * @return	$array
	 */
	public function addPromoType($promotypedata) {
		$this->db->insert('promotype', $promotypedata);
	}

	/**
	 * getPromoType
	 *
	 * @return	$array
	 */
	public function addPromoRulesGameType($data) {
		//var_dump($data);exit();
		$this->db->insert('promorulesgametype', $data);
	}

	/**
	 * addPromoRuleAllowedPlayerLevel
	 *
	 * @return	$array
	 */
	public function addPromoRuleAllowedPlayerLevel($data) {
		$this->db->insert('promorulesallowedplayerlevel', $data);
	}

	/**
	 * addGameRequirements
	 *
	 * @return	$array
	 */
	public function addGameRequirements($data) {
		$this->db->insert('promorulesgamebetrule', $data);
	}

	/**
	 * addPromoRules
	 *
	 * @return	$array
	 */
	public function addPromoRules($data) {
		$this->db->insert('promorules', $data);

		//checker
		if ($this->db->affected_rows() == '1') {
			//return TRUE;
			return $this->db->insert_id();
		}

		return FALSE;
	}

	/**
	 * editPromoRules
	 *
	 * @return	$array
	 */
	public function editPromoRules($data) {
		$this->db->where('promorulesId', $data['promorulesId']);
		$this->db->update('promorules', $data);
	}

	/**
	 * getPromoTypeDetails
	 *
	 * @return	$array
	 */
	public function getPromoTypeDetails($promotypeId) {
		$this->db->select('promotypeId, promotypeOrder, promoTypeName, promoTypeDesc, isUseToPromoManager, promoIcon')->from('promotype');
		$this->db->where('promotypeId', $promotypeId);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	/**
	 * getPromoRuleDetails
	 *
	 * @return	$array
	 */
	public function getPromoRuleDetails($promoruleId) {
		$this->db->select('promorulesId,
			 			   promoName,
			 			   promoCategory,
			 			   promoCode,
			 			   promoDesc
							')->from('promorules');
		$this->db->where('promorulesId', $promoruleId);
		$this->db->where('deleted_flag IS NULL', null);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	/**
	 * viewPromoRuleDetails
	 *
	 * @return	$array
	 */
	public function viewPromoRuleDetails($promoruleId) {
		$this->db->select('promorules.*,
						   promotype.promoTypeName,
						   admin1.userName as createdBy,
						   admin2.userName as updatedBy')->from('promorules');
		$this->db->join("promotype", "promotype.promotypeId = promorules.promoCategory");
		$this->db->join('adminusers as admin1', 'admin1.userId = promorules.createdBy', 'left');
		$this->db->join('adminusers as admin2', 'admin2.userId = promorules.updatedBy', 'left');
		$this->db->where('promorulesId', $promoruleId);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				// $row['applicationPeriodStart'] = mdate('%Y-%m-%dT%h:%i', strtotime($row['applicationPeriodStart']));
				//$row['applicationPeriodEnd'] = mdate('%Y-%m-%dT%h:%i', strtotime($row['applicationPeriodStart']));
				// $row['createdOn'] = mdate('%M %d, %Y ', strtotime($row['createdOn']));
				$row['playerLevels'] = $this->getAllowedPlayerLevels($row['promorulesId']);
				$row['gameType'] = $this->getAllowedGameType($row['promorulesId']);
				$row['gameBetCondition'] = $this->getGameBetCondition($row['promorulesId']);
				// $row['gameRecordStartDate'] = mdate('%Y-%m-%dT%h:%i', strtotime($row['gameRecordStartDate']));
				// $row['gameRecordEndDate'] = mdate('%Y-%m-%dT%h:%i', strtotime($row['gameRecordEndDate']));
				// $row['hide_date'] = mdate('%Y-%m-%dT%h:%i', strtotime($row['hide_date']));
				// if ($row['updatedOn'] != null) {
				// 	$row['updatedOn'] = mdate('%M %d, %Y - %h:%i:%s %A', strtotime($row['updatedOn']));
				// }

				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * game bet condition
	 *
	 * @return	$array
	 */
	public function getGameBetCondition($promoruleId) {
		$this->db->select('game_description.game_name as gameName,game_description.game_code as gameCode,promorulesgamebetrule.betrequirement,game.game')->from('promorulesgamebetrule');
		$this->db->join('game_description', 'game_description.id = promorulesgamebetrule.game_description_id', 'left');
		$this->db->join('game', 'game.gameId = game_description.game_platform_id', 'left');
		$this->db->where('promorulesgamebetrule.promoruleId', $promoruleId);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['gameName'] = lang($row['gameName']);
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * getAllowedGameType
	 *
	 * @return	$array
	 */
	public function getAllowedGameType($promoruleId) {
		$this->db->select('game.game')->from('promorulesgametype');
		$this->db->join('game', 'game.gameId = promorulesgametype.gameType', 'left');
		$this->db->where('promorulesgametype.promoruleId', $promoruleId);
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
	 * getAllPromoApplication
	 * MOVED TO  promorules
	 * @return	$array
	 */
	public function getAllPromoApplication($cancelStatus = '') {
		$this->db->select('playerpromo.playerpromoId,
						   playerpromo.promoStatus,
						   playerpromo.transactionStatus,
						   playerpromo.bonusAmount,
						   playerpromo.dateApply,
						   playerpromo.dateProcessed,
						   playerpromo.dateCancelled,
						   playerpromo.dateCancelDeclined,
						   playerpromo.cancelRequestDate,
						   playerpromo.declinedCancelReason,
						   playerpromo.declinedApplicationReason,
						   playerpromo.cancelRequestStatus,
						   playerpromo.verificationStatus,
						   adminusers.username AS processedBy,
						   player.username,
						   player.playerId,
						   promorules.promoName,
						   promorules.nonDepositPromoType,
						   promorules.bonusReleaseToPlayer,
						   promorules.promorulesId,
						   promorules.promoType,
						   promocmssetting.promoCmsSettingId,
						   promocmssetting.promoName as promoTitle,
						   vipsettingcashbackrule.vipLevelName,
						   vipsettingcashbackrule.vipLevel,
						   vipsetting.groupName
						   ')->from('playerpromo');
		$this->db->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left');
		$this->db->join('player', 'player.playerId = playerpromo.playerId', 'left');
		$this->db->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left');
		$this->db->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left');
		$this->db->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$this->db->join('promocmssetting', 'promocmssetting.promoCmsSettingId = playerpromo.promoCmsSettingId', 'left');
		$this->db->join('adminusers', 'adminusers.userId = playerpromo.processedBy', 'left');
		if ($cancelStatus) {
			$requestStatus = array(1);
			$this->db->where_in('playerpromo.cancelRequestStatus', $requestStatus);
		} else {
			$this->db->where_in('playerpromo.transactionStatus', 0);
		}
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['dateApply'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dateApply']));
				if ($row['dateProcessed']) {
					$row['dateProcessed'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dateProcessed']));
				}
				if ($row['dateCancelled']) {
					$row['dateCancelled'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dateCancelled']));
				}
				if ($row['cancelRequestDate']) {
					$row['cancelRequestDate'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['cancelRequestDate']));
				}
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * getAllPromoPlayer
	 *
	 * @return	$array
	 */
	public function getAllPromoPlayer() {
		$this->db->select('playerpromo.playerpromoId,
						   playerpromo.promoStatus,
						   playerpromo.transactionStatus,
						   playerpromo.bonusAmount,
						   playerpromo.dateApply,
						   playerpromo.dateProcessed,
						   playerpromo.dateCancelled,
						   playerpromo.cancelRequestDate,
						   playerpromo.declinedCancelReason,
						   playerpromo.declinedApplicationReason,
						   playerpromo.cancelRequestStatus,
						   adminusers.username AS processedBy,
						   player.username,
						   player.playerId,
						   promorules.promoName,
						   promorules.promorulesId,
						   promocmssetting.promoCmsSettingId,
						   promocmssetting.promoName as promoTitle,
						   vipsettingcashbackrule.vipLevelName,
						   vipsettingcashbackrule.vipLevel,
						   vipsetting.groupName
						   ')->from('playerpromo');
		$this->db->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left');
		$this->db->join('player', 'player.playerId = playerpromo.playerId', 'left');
		$this->db->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left');
		$this->db->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left');
		$this->db->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$this->db->join('promocmssetting', 'promocmssetting.promoCmsSettingId = playerpromo.promoCmsSettingId', 'left');
		$this->db->join('adminusers', 'adminusers.userId = playerpromo.processedBy', 'left');

		$this->db->where_in('playerpromo.transactionStatus', 1);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->result_array();
			// foreach ($query->result_array() as $row) {
			// 	$row['dateApply'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dateApply']));
			// 	if ($row['dateProcessed']) {
			// 		$row['dateProcessed'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dateProcessed']));
			// 	}
			// 	if ($row['dateCancelled']) {
			// 		$row['dateCancelled'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dateCancelled']));
			// 	}
			// 	if ($row['cancelRequestDate']) {
			// 		$row['cancelRequestDate'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['cancelRequestDate']));
			// 	}
			// 	$data[] = $row;
			// }
			//var_dump($data);exit();
			// return $data;
		}
		return false;
	}

	/**
	 * getPromoCancelSetup
	 *
	 * @return	$array
	 */
	public function getPromoCancelSetup() {
		$this->db->select('value')->from('operator_settings');
		$this->db->where('operator_settings.name', 'promo_cancellation_setting');
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * setupPromoCancellation
	 *
	 * @return	$array
	 */
	public function setupPromoCancellation($data) {
		$this->db->where('name', $data['name']);
		$this->db->update('operator_settings', $data);
	}

	/**
	 * getAllowedPlayerLevels
	 *
	 * @return	$array
	 */
	public function getAllowedPlayerLevels($promoruleId) {
		$this->db->select('vipsettingcashbackrule.vipsettingcashbackruleId,vipsettingcashbackrule.vipLevelName,vipsetting.groupName')->from('promorulesallowedplayerlevel');
		$this->db->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = promorulesallowedplayerlevel.playerLevel', 'left');
		$this->db->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$this->db->where('promoruleId', $promoruleId);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	/**
	 * getPromoRuleLevels
	 *
	 * @return	$array
	 */
	public function getPromoRuleLevels($promoruleId) {
		$this->db->select('vipsettingcashbackrule.vipsettingcashbackruleId')->from('promorulesallowedplayerlevel');
		$this->db->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = promorulesallowedplayerlevel.playerLevel', 'left');
		$this->db->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$this->db->where('promoruleId', $promoruleId);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row['vipsettingcashbackruleId'];
			}
			return $data;
		}
		return false;
	}

	/**
	 * getAllPromoRuleLevels
	 *
	 * @return	$array
	 */
	public function getAllPromoRuleLevels() {
		$this->db->select('vipsettingcashbackruleId')->from('vipsettingcashbackrule');
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row['vipsettingcashbackruleId'];
			}
			return $data;
		}
		return false;
	}

	/**
	 * getAllGameProvider
	 *
	 * @return	$array
	 */
	public function getAllGameProvider() {
		$this->db->select('game_platform_id')->from('game_type');
		$this->db->group_by('game_platform_id');
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row['game_platform_id'];
			}
			return $data;
		}
		return false;
	}

	/**
	 * getAllGames
	 *
	 * @return	$array
	 */
	public function getAllGames($gameType = null) {
		$this->db->select('id as game_description_id')->from('game_description');
		$this->db->where('game_description.game_code != ', 'unknown');
		if ($gameType) {
			$this->db->where('game_description.game_platform_id', $gameType);
		}
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row['game_description_id'];
			}
			return $data;
		}
		return false;
	}

	/**
	 * getPromoRuleGames
	 *
	 * @return	$array
	 */
	public function getPromoRuleGamesType($promoruleId) {
		$this->db->select('promorulesgamebetrule.game_description_id,game_description.game_type_id')->from('promorulesgamebetrule');
		$this->db->join('game_description', 'game_description.id = promorulesgamebetrule.game_description_id', 'left');
		$this->db->join('game_type', 'game_type.id = game_description.game_type_id', 'left');
		$this->db->where('promoruleId', $promoruleId);
		$this->db->group_by('game_description.game_type_id');
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row['game_type_id'];
			}
			return $data;
		}
		return false;
	}

	/**
	 * getPromoRuleGames
	 *
	 * @return	$array
	 */
	public function getPromoRuleGames($promoruleId) {
		$this->db->select('promorulesgamebetrule.game_description_id')->from('promorulesgamebetrule');
		$this->db->join('game_description', 'game_description.id = promorulesgamebetrule.game_description_id', 'left');
		$this->db->join('game_type', 'game_type.id = game_description.game_type_id', 'left');
		$this->db->where('promoruleId', $promoruleId);

		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row['game_description_id'];
			}
			return $data;
		}
		return false;
	}

	/**
	 * getPromoRuleGamesProvider
	 *
	 * @return	$array
	 */
	public function getPromoRuleGamesProvider($promoruleId) {
		$this->db->select('game_type.game_platform_id')->from('promorulesgamebetrule');
		$this->db->join('game_description', 'game_description.id = promorulesgamebetrule.game_description_id', 'left');
		$this->db->join('game_type', 'game_type.id = game_description.game_type_id', 'left');
		$this->db->where('promoruleId', $promoruleId);
		$this->db->group_by('game_type.game_platform_id');
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row['game_platform_id'];
			}
			return $data;
		}
		return false;
	}

	/**
	 * getAllGameType
	 *
	 * @return	$array
	 */
	public function getAllGameType() {
		$this->db->select('id as game_type_id')->from('game_type');
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row['game_type_id'];
			}
			return $data;
		}
		return false;
	}

	/**
	 * clearPromoItems
	 *
	 * @param $promotypeid - int
	 */

	public function clearPromoItems($promorulesId) {
		$this->db->delete('promorulesallowedplayerlevel', array('promoruleId' => $promorulesId));
		$this->db->delete('promorulesgamebetrule', array('promoruleId' => $promorulesId));
		$this->db->delete('promorulesgametype', array('promoruleId' => $promorulesId));

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * editPromoType
	 *
	 * @return	$array
	 */
	public function editPromoType($data) {
		$this->db->where('promotypeId', $data['promoTypeId']);
		$this->db->update('promotype', $data);
	}

	/**
	 * processPromoApplication
	 *
	 * @return	$array
	 */
	// public function processPromoApplication($data) {
	// 	$this->db->where('playerpromoId', $data['playerpromoId']);
	// 	$this->db->update('playerpromo', $data);
	// }

	/**
	 * deletePromoType
	 *
	 * @param $promotypeid - int
	 */

	public function deletePromoType($promotypeid) {
		$this->db->delete('promotype', array('promotypeId' => $promotypeid));

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * fakeDeletePromoType
	 *
	 * @param $promotypeid - int
	 */

	public function fakeDeletePromoType($promotypeid) {
		$data = array(
           'deleted' => 1,
        );
		$this->db->where('promotypeId', $promotypeid);
		$this->db->update('promotype', $data);

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * getGameProvider
	 *
	 * @return	$array
	 */
	public function getGameProvider() {
		$this->db->select('*')->from('game');
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	/**
	 * getAllPromoRule
	 *
	 * @return	$array
	 */
	public function getAllPromoRule() {
		$this->db->select('promorules.*,
						   promotype.promoTypeName,
						   admin1.userName as createdBy,
						   admin2.userName as updatedBy')->from('promorules');
		$this->db->join("promotype", "promotype.promotypeId = promorules.promoCategory");
		$this->db->join('adminusers as admin1', 'admin1.userId = promorules.createdBy', 'left');
		$this->db->join('adminusers as admin2', 'admin2.userId = promorules.updatedBy', 'left');
		$this->db->where('promorules.deleted_flag IS NULL', null);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			// foreach ($query->result_array() as $row) {
			// 	$row['createdOn'] = $row['createdOn'];
			// 	$row['updatedOn'] = $row['updatedOn'];
			// 	$row['applicationPeriodStart'] = $row['applicationPeriodStart'];
			// 	$row['applicationPeriodEnd'] = $row['applicationPeriodEnd'];

			// 	$data[] = $row;
			// }
			// return $data;

			return $query->result_array();
		}
		return false;
	}

	/**
	 * getGameType
	 *
	 * @return	$array
	 */
	public function getGameType($game_platform_id) {
		$this->db->select('game_type.id as catId,game_description.id,game_description.game_type_id,game_type.game_type as gameType,game_type.game_type_lang as gameTypeLang,game_type.game_platform_id as gameTypeId')->from('game_description');
		$this->db->join('game_type', 'game_type.id = game_description.game_type_id', 'left');
		$this->db->where('game_description.game_platform_id', $game_platform_id);
		$this->db->group_by('game_description.game_type_id');
		$this->db->where('game_type.game_type !=', 'unknown');
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	/**
	 * get registration fields
	 *
	 * @param string
	 * @return array
	 */
	function getPromoRulesId($playerpromoId) {
		$query = $this->db->query("SELECT promorulesId FROM playerpromo
			WHERE playerpromoId = '" . $playerpromoId . "'
		");

		return $query->row_array();
	}

	/**
	 * getPTGameType
	 *
	 * @return	$array
	 */
	public function getPlatformGames($gamePlatformId) {
		$this->db->select('id,game_type_id as gameType,game_name as gameName,game_code,game_description.game_platform_id as gameTypeId')->from('game_description');
		$this->db->where('game_platform_id', $gamePlatformId);
		$this->db->where('game_code !=', 'unknown');
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	/**
	 * Will save player withdrawal condition
	 *
	 * @param $conditionData array
	 * @return null
	 */
	public function savePlayerWithdrawalCondition($conditionData) {
		$this->db->insert('withdraw_conditions', $conditionData);
	}

	/**
	 * add cashback allowed games
	 * MOVED TO group_level
	 * @return	$array
	 */
	// public function addCashbackAllowedGames($data) {
	// 	$this->db->insert('vipsetting_cashback_game', $data);
	// }

	/**
	 * check if vipsettingid already exists
	 * MOVED TO group_level
	 * @return Bool - TRUE or FALSE
	 */

	// public function isVipSettingIdExists($vipsettingId) {
	// 	$this->db->select('vipsetting_cashbackrule_id')->where('vipsetting_cashbackrule_id', $vipsettingId);
	// 	$query = $this->db->get('vipsetting_cashback_game');

	// 	if ($query->num_rows() > 0) {
	// 		return TRUE;
	// 	} else {
	// 		return FALSE;
	// 	}
	// }

	/**
	 * Will delete cashback allowed game type
	 * MOVED TO group_level
	 * @param 	int
	 */
	// public function deleteCashbackAllowedGameType($vipSettingCashbackruleId) {
	// 	$where = "vipsetting_cashbackrule_id = '" . $vipSettingCashbackruleId . "'";
	// 	$this->db->where($where);
	// 	$this->db->delete('vipsetting_cashback_game');
	// }

	/**
	 * Will get allowed game
	 *
	 * @param 	int
	 */
	// public function getCashbackAllowedGame($vipSettingCashbackruleId) {
	// 	$this->db->select('game_description.game_name,game.game')->from('vipsetting_cashback_game');
	// 	$this->db->join('game_description', 'game_description.id = vipsetting_cashback_game.game_description_id');
	// 	$this->db->join('game', 'game.gameId = game_description.game_platform_id');
	// 	$this->db->where('vipsetting_cashbackrule_id', $vipSettingCashbackruleId);
	// 	$query = $this->db->get();
	// 	return $query->result_array();
	// }

	/**
	 * getPromoBonusReleaseRule
	 *
	 * @param int
	 * @return array
	 */
	function getPromoBonusReleaseRule($promorulesId) {
		$this->db->where('promorulesId', $promorulesId);
		$query = $this->db->select("bonusReleaseRule,bonusAmount,depositPercentage,gameRequiredBet")->from('promorules');
		$query = $this->db->get();
		return $query->row_array();
	}
}

/* End of file depositpromo.php */
/* Location: ./application/models/depositpromo.php */
