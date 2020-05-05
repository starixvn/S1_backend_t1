<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * Player
 *
 * DON'T ADD NEW FUNCTION
 * MOVE TO player_model.php
 *
 * This model represents ip data. It operates the following tables:
 * - player
 * - playerdetails
 *
 * @author	Johann Merle
 */

class Player extends BaseModel {

    const PLAYERACCOUNT_TYPE_AFFILIATE = 'affiliate';
    const DEFAULT_PLAYERACCOUNT_BATCHPASSWORD = 0;
    const DEFAULT_PLAYERACCOUNT_STATUS = 0;
    const DEFAULT_PLAYERGAME_STATUS = 0;
    const DEFAULT_PLAYERGAME_BLOCKED = 0;
    const DEFAULT_AFFILIATE_EARNINGS_STATUS = 0;


    public function __construct() {
		parent::__construct();
	}

	protected $tableName = 'player';

	public function getPlayerByLogin($login) {
		$this->db->where('username', $login);

		$query = $this->db->get('player');
		if ($query->num_rows() == 1) {
			return $query->row();
		}

		return NULL;
	}

	/**
	 * Update user login info, such as IP-address or login time, and
	 * clear previously generated (but not activated) passwords.
	 *
	 * @param	int
	 * @param	bool
	 * @param	bool
	 * @return	void
	 */
	public function updateLoginInfo($player_id, $record_ip, $record_time) {
		// if ($record_ip) {
		// 	$this->db->set('lastLoginIp', $this->input->ip_address());
		// }

		// if ($record_time) {
		// 	$this->db->set('lastLoginTime', date('Y-m-d H:i:s'));
		// }

		// $this->db->where('playerId', $player_id);
		// $this->db->update('player');
		return null;
	}

	/**
	 * Will get all players
	 * move to player_model
	 *
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function getAllPlayers($sort_by, $in, $limit, $offset) {
		$where = '';

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		// if ($sort_by == 'active') {
		// 	$where = " AND p.status = '0'";
		// 	$sort_by = 'username';
		// }

		// if ($sort_by == 'inactive') {
		// 	$where = " AND p.status = '1'";
		// 	$sort_by = 'username';
		// }

		// $query = $this->db->query("SELECT p.*, pd.*,vipcbr.vipLevel,vipcbr.vipLevelName,vipst.groupName, pa.typeOfPlayer, pa.type, pa.typeId, t.tagName, p.status
		// 	FROM player AS p
		// 	LEFT JOIN playeraccount AS pa ON p.playerId = pa.playerId
		// 	LEFT JOIN playerdetails AS pd ON p.playerId = pd.playerId
		// 	LEFT JOIN playertag AS pt ON p.playerId = pt.playerId
		// 	LEFT JOIN playerlevel as pl on pl.playerId = p.playerId
		// 	LEFT JOIN vipsettingcashbackrule as vipcbr on vipcbr.vipsettingcashbackruleId = pl.playerGroupId
		// 	LEFT JOIN vipsetting as vipst on vipst.vipSettingId = vipcbr.vipSettingId
		// 	LEFT JOIN tag AS t ON pt.tagId = t.tagId
		// 	WHERE pa.type = 'wallet' $where
		// ");

		$this->db->select('p.*, pd.*,vipcbr.vipLevel,vipcbr.vipLevelName,vipst.groupName, pa.typeOfPlayer, pa.type, pa.typeId, t.tagName, p.status');
		$this->db->from('player AS p');
		$this->db->join('playeraccount AS pa', 'p.playerId = pa.playerId', 'left');
		$this->db->join('playerdetails AS pd', 'p.playerId = pd.playerId', 'left');
		$this->db->join('playertag AS pt', 'p.playerId = pt.playerId', 'left');
		$this->db->join('playerlevel as pl', 'p.playerId = pl.playerId', 'left');
		$this->db->join('vipsettingcashbackrule as vipcbr', 'vipcbr.vipsettingcashbackruleId = pl.playerGroupId', 'left');
		$this->db->join('vipsetting as vipst', 'vipst.vipSettingId = vipcbr.vipSettingId', 'left');
		$this->db->join('tag AS t', 'pt.tagId = t.tagId', 'left');
		$this->db->where('pa.type', 'wallet');

		if ($sort_by == 'active') {
			$this->db->where('p.status', '0');
		}

		if ($sort_by == 'inactive') {
			$this->db->where('p.status', '1');
		}

		$query = $this->db->get();

		if (!$query->result_array()) {
			return false;
		} else {
			$result = $query->result_array();
			$return = array();

			foreach ($result as $key => $value) {
				$result_query = $this->db->query("SELECT playerAccountId FROM playeraccount WHERE playerId = '" . $value['playerId'] . "' AND type = 'batch'");

				$res = $result_query->row_array();

				if (!empty($res)) {
					$value['playertype'] = $res['playerAccountId'];
				} else {
					$value['playertype'] = '';
				}

				array_push($return, $value);
			}

			/*echo "<pre>";
				print_r($return);
				echo "</pre>";
			*/

			return $return;
		}
	}

	/**
	 * Will get all player Levels
	 *
	 * @return array
	 */

	public function getAllPlayerLevels() {
		$this->load->model(['group_level']);
		$this->db->select('vipsettingcashbackrule.vipLevel,
			vipsettingcashbackrule.vipLevelName,
			vipsettingcashbackrule.vipsettingcashbackruleId,
			vipsettingcashbackrule.upgradeAmount,
			vipsettingcashbackrule.downgradeAmount,
			vipsetting.vipSettingId,
			vipsetting.groupName,')
			->from('vipsettingcashbackrule')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left'); #
		$this->db->where('vipsetting.deleted', Group_level::DB_FALSE);
		$this->db->order_by('vipsettingcashbackrule.vipsettingcashbackruleId', 'ASC');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * Will get all player Levels
	 *
	 * @return array
	 */

	public function getPlayerCurrentLevel($playerId) {
		$this->db->select('vipsettingcashbackrule.vipLevel,
			vipsettingcashbackrule.vipLevelName,
			vipsettingcashbackrule.vipsettingcashbackruleId,
			vipsetting.vipSettingId,
			vipsetting.groupName')
			->from('playerlevel')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$this->db->where('playerlevel.playerId', $playerId);
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * Will get player given the Id
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerById($player_id) {
		$this->db->select('p.*, t.*, pd.*, pa.*, p.playerId as playerId, p.status as playerStatus, p.createdOn as playerCreatedOn, t.createdOn as tagCreatedOn, t.status as tagStatus, pa.status as playerAccountStatus');
		$this->db->from('player as p');
		$this->db->join('playerdetails as pd', 'p.playerId = pd.playerId', 'left');
		$this->db->join('playeraccount as pa', 'p.playerId = pa.playerId', 'left');
		$this->db->join('playertag as pt', 'p.playerId = pt.playerId', 'left');
		$this->db->join('tag as t', 'pt.tagId = t.tagId', 'left');
		$this->db->where('pa.type', 'wallet');
		$this->db->where('p.playerId', $player_id);

		$query = $this->db->get();

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	/**
	 * Will get player username
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerContactInfo($player_id) {
		// $query = $this->db->query("SELECT p.secretQuestion, p.secretAnswer,
		// 		pd.contactNumber,pd.imAccount,pd.imAccountType,
		// 		pd.imAccount2,pd.imAccountType2,
		// 		pd.qq, pd.preferredContact,
		// 		pd.phone
		// 	FROM player AS p
		// 	LEFT JOIN playerdetails AS pd ON p.playerId = pd.playerId
		// 	WHERE p.playerId = '" . $player_id . "'");

		$this->db->select('p.secretQuestion, p.secretAnswer, pd.contactNumber,pd.imAccount,pd.imAccountType, pd.imAccount2,pd.imAccountType2, pd.qq, pd.preferredContact, pd.phone');
		$this->db->from('player as p');
		$this->db->join('playerdetails AS pd', 'p.playerId = pd.playerId', 'left');
		$this->db->where('p.playerId', $player_id);

		$query = $this->db->get();

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	/**
	 * get all player ids and usernames
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getAllPlayerUsernames($status = 'active') {
		$this->db->select('p.playerId, p.username');
		if ($status == 'active') {
			$this->db->where('p.status', '0');
		} else if ($status == 'inactive') {
			$this->db->where('p.status', '1');
		}
		$this->db->order_by('p.username', 'asc');
		$this->db->from('player as p');

		$query = $this->db->get();

        $result = $query->result_array();

        return $result;
	}

	/**
	 * Will get player username
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerUsername($player_id) {
		$this->db->select('username');
		$this->db->from('player as p');
		$this->db->where('p.playerId', $player_id);

		$query = $this->db->get();

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	/**
	 * Will get player account given the Id
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerAccount($player_id) {
		// $query = $this->db->query("SELECT DISTINCT pa.*, p.*, wa.* FROM player AS p INNER JOIN playeraccount AS pa ON p.playerId = pa.playerId LEFT JOIN walletaccount AS wa ON pa.playeraccountid = wa.playeraccountid
		// 	LEFT JOIN walletaccountdetails AS wad ON wa.walletaccountId = wad.walletaccountId WHERE p.playerId = '" . $player_id . "' AND pa.type = 'wallet'");

		$this->db->select('pa.*, p.*');
		$this->db->from('player as p');
		$this->db->join('playeraccount as pa', 'p.playerId = pa.playerId', 'inner');
		//$this->db->join('walletaccount as wa', 'pa.playerAccountid = wa.playeraccountid', 'left');
		//$this->db->join('walletaccountdetails as wad', 'wa.walletaccountId = wad.walletaccountId', 'left');
		$this->db->where('p.playerId', $player_id);
		$this->db->where('pa.type', 'wallet');
		$this->db->distinct();

		$query = $this->db->get();

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	/**
	 * Will get player bank details given the Id
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerBankDetailsById($player_id) {
		// $query = $this->db->query("SELECT pbd.*, bt.bankName from playerbankdetails as pbd
		// 	LEFT JOIN banktype as bt
		// 	ON pbd.bankTypeId = bt.bankTypeId
		// 	WHERE playerId = '" . $player_id . "'
		// ");

		$this->db->select('pbd.*, bt.bankName');
		$this->db->from('playerbankdetails as pbd');
		$this->db->join('banktype as bt', 'pbd.bankTypeId = bt.bankTypeId', 'left');
		$this->db->where('playerId', $player_id);
		$this->db->where('(pbd.status !=', self::STATUS_DELETED);
		$this->db->or_where('pbd.status IS NULL)', null, false);
		//var_dump($this->db->last_query());exit();
		$query = $this->db->get();

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * Will get player account given the Id
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getNonApprovedWithdrawal($player_id, $limit, $offset) {

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = '';
		}

		// $query = $this->db->query("SELECT * FROM player AS p
		// 	INNER JOIN playeraccount AS pa ON p.playerId = pa.playerId
		// 	LEFT JOIN walletaccount AS wa ON pa.playeraccountid = wa.playeraccountid
		// 	LEFT JOIN walletaccountdetails AS wad ON wa.walletaccountId = wad.walletaccountId
		// 	WHERE p.playerId = '" . $player_id . "'
		// 	AND pa.type = 'wallet'
		// 	AND dwStatus = 'declined'
		// 	AND wa.status = 0
		// 	$limit $offset");

		$this->db->select('*');
		$this->db->from('player as p');
		$this->db->join('playeraccount as pa', 'p.playerId = pa.playerId', 'inner');
		$this->db->join('walletaccount as wa', 'pa.playeraccountid = wa.playeraccountid', 'left');
		$this->db->join('walletaccountdetails as wad', 'wa.walletaccountId = wad.walletaccountId', 'left');
		$this->db->where('pa.type', 'wallet');
		$this->db->where('dwStatus', 'declined');
		$this->db->where('wa.status', '0');
		$this->db->where('p.playerId', $player_id);

		$query = $this->db->get();

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * Will get note given the Id
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerNotes($player_id) {
		$this->db->select('pn.*, au.username');
		$this->db->from('player as p');
		$this->db->join('playernotes as pn', 'p.playerId = pn.playerId', 'inner');
		$this->db->join('adminusers as au', 'pn.userId = au.userId', 'inner');
		$this->db->where('p.playerId', $player_id);
		$this->db->order_by('createdOn', 'desc');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * Inserts data to database
	 *
	 * @param	array
	 * @return	boolean
	 */
	public function insertPlayer($data) {
		$this->db->insert('player', $data);
		$playerId = $this->db->insert_id();

		/* OLD
			 * playerLevel['playerId'] = $playerId;
			 * $playerLevel['playerGroupId'] = 1;
			 * $this->group_level->insertPlayerLevel($playerLevel);
		*/

		# NEW
		$this->load->model(['group_level']);
		$levelId = empty($this->utils->getConfig('default_level_id'))? 1 : $this->utils->getConfig('default_level_id');
		$this->group_level->addPlayerLevel($playerId, $levelId);
		return $playerId;
	}

	/**
	 * Inserts data to database
	 *
	 * @param	array
	 * @return	boolean
	 */
	public function resetPassword($data, $player_id) {
		$this->db->where('playerId', $player_id);
		$this->db->update('player', $data);
	}

	/**
	 * Inserts data to database
	 *
	 * @param	array
	 * @return	boolean
	 */
	public function setPlayerAgent($data, $player_id) {
		$this->db->where('playerId', $player_id);
		$this->db->update('player', $data);
	}

	/**
	 * Inserts data to database
	 *
	 * @param	array
	 * @return	boolean
	 */
	public function insertPlayerAccount($data) {
		$this->db->insert('playeraccount', $data);
	}

	/**
	 * Inserts data to database
	 *
	 * @param	array
	 * @return	boolean
	 */
	public function insertPlayerDetails($data) {
		$this->db->insert('playerdetails', $data);
	}

	/**
	 * Inserts data to database
	 *
	 * @param	array
	 * @return	boolean
	 */
	public function insertPlayerTag($data) {
		$this->db->insert('playertag', $data);
	}

	public function insertAndGetPlayerTag($data) {
		try {
			$this->db->insert('playertag', $data);

			if ($this->db->_error_message()) {
				throw new Exception($this->db->_error_message());
			} else {
				//New tag
				return $this->checkIfPlayerIsTagged($data['playerId'], $data['tagId']);
			}

		} catch (Exception $e) {
			return FALSE;
		}
	}

	/**
	 * Updates Player Tag
	 *
	 * @param	array
	 * @param   int
	 * @return	string
	 */
	public function updatePlayerTag($data, $playerId) {

		try {
			$this->db->where('playerId', $playerId);
			$this->db->update('playertag', $data);

			if ($this->db->_error_message()) {
				throw new Exception($this->db->_error_message());
			} else {
				//New tag
				$newTag = $this->checkIfPlayerIsTagged($playerId, $data['tagId']);
				if ($newTag) {
					return $newTag;
				} else {
					return FALSE;
				}
			}

		} catch (Exception $e) {
			return FALSE;
		}
	}

	public function removePlayerTag($playerTagId) {
		try {
            if(is_array($playerTagId)){
                $this->db->where_in('playerTagId', $playerTagId);
            }else{
                $this->db->where('playerTagId', $playerTagId);
            }
            $this->db->delete('playertag');

			if ($this->db->_error_message()) {
				return FALSE;
			} else {
				return TRUE;
			}

		} catch (Exception $e) {
			return FALSE;
		}
	}

	public function deleteAndGetPlayerTag($playerId) {
		try {

			$this->db->where('playerId', $playerId);
			$this->db->delete('playertag');

			if ($this->db->_error_message()) {
				throw new Exception($this->db->_error_message());
			} else {
				return lang('player.ui73');
			}

		} catch (Exception $e) {
			return FALSE;
		}
	}

	/**
	 * Inserts data to database
	 *
	 * @param	array
	 * @return	boolean
	 */
	public function insertTag($data) {
		$this->db->insert('tag', $data);
		return $this->db->insert_id();
	}

	/**
	 * Inserts data to vipsetting
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

		$vipdata['vipsettingcashbackruleId'] = $this->db->insert_id();

		for ($i = 1; $i < 2; $i++) {
			$vipdata['percentage'] = 10 * $i;
			$vipdata['maxBonus'] = 1000 * $i;
			$vipdata['gameType'] = $i;
			$this->createVipCashbackBonusPerGame($vipdata);
		}
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
		$this->db->select('*')->from('vipsettingcashbackrule');
		$this->db->where('vipSettingId', $vipgroupId);

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

	public function checkUsernameExist($username) {
		$this->db->select('*')->from('player');
		$this->db->where('username', $username);

		$query = $this->db->get();

		return $query->row_array();
	}

	public function checkUsernameLikeExist($username) {
		$this->db->select('*')->from('player');
		$this->db->like('username', $username);

		$query = $this->db->get();

		return $query->row_array();
	}

	public function checkEmailExist($email) {
		$this->db->select('*')->from('player');
		$this->db->where('email', $email);

		$query = $this->db->get();

		return $query->row_array();
	}

	/**
	 * Inserts data to database
	 *
	 * @param	array
	 * @return	boolean
	 */
	public function insertPlayerNote($data) {
		return $this->db->insert('playernotes', $data);
	}

	/**
	 * Delete data to database
	 *
	 * @param	array
	 * @return	boolean
	 */
	public function deleteNote($user_id, $note_id) {
		$where = "userId = '" . $user_id . "' AND noteId = '" . $note_id . "'";
		$this->db->where($where);
		return $this->db->delete('playernotes');
	}

	/**
	 * Edit data to database
	 *
	 * @param	array
	 * @return	boolean
	 */
	public function editNote($user_id, $note_id, $data) {
		$where = "userId = '" . $user_id . "' AND noteId = '" . $note_id . "'";
		$this->db->where($where);
		$this->db->update('playernotes', $data);
	}

	/**
	 * Will get note based on the passed parameter
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getNoteById($note_id) {
		$this->db->select('*')->from('playernotes');
		$this->db->where('noteId', $note_id);

		$query = $this->db->get();

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
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

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		// if ($type == 'vip') {
		// 	$type = "AND rankingLevelGroup = 'VIP'";
		// } elseif ($type == 'blacklist') {
		// 	$type = "AND blocked != 0";
		// } else {
		// 	$type = "";
		// }

		// $query = $this->db->query("SELECT p.*, t.tagName, rls.*
		// 	FROM player as p
		// 	INNER JOIN rankinglevelsetting as rls on p.playerLevel = rls.rankingLevelSettingId
		// 	LEFT JOIN playertag as pt on p.playerId = pt.playerId
		// 	LEFT JOIN tag as t on pt.tagId = t.tagId
		// 	where username LIKE
		// 	'%" . $search . "%'
		// 	AND pa.type = 'wallet'
		// 	$type
		// 	$limit
		// 	$offset
		// ");

		$this->db->select('p.*, t.tagName, rls.*');
		$this->db->from('player as p');
		$this->db->join('rankinglevelsetting as rls', 'p.playerLevel = rls.rankingLevelSettingId', 'inner');
		$this->db->join('playertag as pt', 'p.playerId = pt.playerId', 'left');
		$this->db->join('tag as t', 'pt.tagId = t.tagId', 'left');
		$this->db->like('username', $search);
		$this->db->where('pa.type', 'wallet');

		if ($type == 'vip') {
			$this->db->where('rankingLevelGroup', 'VIP');
		} elseif ($type == 'blacklist') {
			$this->db->where('blocked !=', '0');
		}

		$query = $this->db->get();

		return $query->result_array();
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
		// $query = $this->db->query("SELECT pfr.*, p.*, pb.username as inviter, pb.invitationCode as inviterCode
		// 	FROM player AS p
		// 	INNER JOIN playerfriendreferral AS pfr ON p.playerId = pfr.invitedPlayerId
		// 	LEFT OUTER JOIN player AS pb
		// 		ON pfr.playerId = pb.playerId
		// 	INNER JOIN playeraccount as pa ON p.playerId = pa.playerId
		// 	WHERE pa.type = 'wallet'
		// 	AND pb.playerId = '" . $player_id . "'
		// ");

		$this->db->select('pfr.*, p.*, pb.username as inviter, pb.invitationCode as inviterCode');
		$this->db->from('player as p');
		$this->db->join('playerfriendreferral as pfr', 'p.playerId = pfr.invitedPlayerId', 'inner');
		$this->db->join('player as pb', 'pfr.playerId = pb.playerId', 'left');
		$this->db->join('playeraccount as pa', 'p.playerId = pa.playerId', 'inner');
		$this->db->where('pa.type', 'wallet');
		$this->db->where('pb.playerId', $player_id);

		$query = $this->db->get();

		return $query->result_array();
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
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		// $query = $this->db->query("SELECT p.*, t.tagName, rls.* FROM player as p
		// 	INNER JOIN rankinglevelsetting as rls on p.playerLevel = rls.rankingLeveSettingId
		// 	LEFT JOIN playertag as pt on p.playerId = pt.playerId
		// 	LEFT JOIN tag as t on pt.tagId = t.tagId
		// 	WHERE pa.type = 'wallet'
		// 	$type ORDER BY $sort ASC
		// 	$limit
		// 	$offset
		// ");

		$this->db->select('p.*, t.tagName, rls.*');
		$this->db->from('player as p');
		$this->db->join('rankinglevelsetting as rls', 'p.playerLevel = rls.rankingLeveSettingId', 'inner');
		$this->db->join('playertag as pt', 'p.playerId = pt.playerId', 'left');
		$this->db->join('tag as t', 'pt.tagId = t.tagId', 'left');
		$this->db->where('pa.type', 'wallet');

		if ($type == 'vip') {
			$this->db->where('rankingLevelGroup', 'VIP');
		} elseif ($type == 'blacklist') {
			$this->db->where('blocked !=', '0');
		}

		$this->db->order_by($sort, 'asc');

		$query = $this->db->get();

		return $query->result_array();
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
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		// $query = $this->db->query("SELECT DISTINCT p.* FROM player AS p
		// 	INNER JOIN playerfriendreferral AS pfr ON p.playerId = pfr.playerId
		// 	INNER JOIN playeraccount as pa on p.playerId = pa.playerId
		// 	WHERE pa.type = 'wallet'
		// 	$type ORDER BY $sort ASC
		// 	$limit
		// 	$offset
		// ");

		$this->db->select('p.*');
		$this->db->from('player as p');
		$this->db->join('playerfriendreferral as pfr', 'p.playerId = pfr.playerId', 'inner');
		$this->db->join('playeraccount as pa', 'p.playerId = pa.playerId', 'inner');
		$this->db->where('pa.type', 'wallet');

		if ($type == 'vip') {
			$this->db->where('rankingLevelGroup', 'VIP');
		} elseif ($type == 'blacklist') {
			$this->db->where('blocked !=', '0');
		}

		$this->db->order_by($sort, 'asc');

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * Will get all vip players
	 *
	 * @param 	int
	 * @param 	int
	 * @return	array
	 */
	public function getVIPPlayers($sort_by, $in, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		// $query = $this->db->query("SELECT p.*, pd.*, pa.typeOfPlayer, pa.type, pa.typeId, t.tagName, CONCAT(rls.rankingLevelGroup, ' ', rls.rankingLevel) AS level, p.status
		// 	FROM player AS p
		// 	INNER JOIN rankinglevelsetting as rls on p.playerLevel = rls.rankingLevelSettingId
		// 	LEFT JOIN playeraccount AS pa ON p.playerId = pa.playerId
		// 	LEFT JOIN playerdetails AS pd ON p.playerId = pd.playerId
		// 	LEFT JOIN playertag AS pt ON p.playerId = pt.playerId
		// 	LEFT JOIN tag AS t ON pt.tagId = t.tagId
		// 	WHERE rankingLevelGroup = 'VIP' AND pa.type = 'wallet' $where
		// 	ORDER BY $sort_by $in $limit $offset
		// ");

		$this->db->select('p.*, pd.*, pa.typeOfPlayer, pa.type, pa.typeId, t.tagName, p.status');
		$this->db->from('player as p');
		// $this->db->join('rankinglevelsetting as rls', 'p.playerLevel = rls.rankingLevelSettingId', 'inner');
		$this->db->join('playeraccount as pa', 'p.playerId = pa.playerId', 'left');
		$this->db->join('playerdetails as pd', 'p.playerId = pd.playerId', 'left');
		$this->db->join('playertag as pt', 'p.playerId = pt.playerId', 'left');
		$this->db->join('tag as t', 'pt.tagId = t.tagId', 'left');
		$this->db->where('rankingLevelGroup', 'VIP');
		$this->db->where('pa.type', 'wallet');

		if ($sort_by == 'active') {
			$this->db->where('p.status', '0');
			$sort_by = 'username';
		}

		if ($sort_by == 'inactive') {
			$this->db->where('p.status', '1');
			$sort_by = 'username';
		}

		$this->db->order_by($sort_by, $in);

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * Will change player status based on the passed parameters
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	public function populateVIP($data, $sort_by, $in, $limit, $offset) {
		$internal_accounts = '';
		$search = array();

		foreach ($data as $key => $value) {
			if ($key == 'playerId' && $value != '') {
				$search[$key] = "p.$key = '" . $value . "'";
			} elseif ($key == 'sign_time_period' && $value != '') {
				if ($value == 'week') {
					$search[$key] = "p.createdOn >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
				} elseif ($value == 'month') {
					$search[$key] = "p.createdOn >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
				} elseif ($value == 'past') {
					$search[$key] = "p.createdOn >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)";
				}
			} elseif ($key == 'signup_range' && $value != '') {
				$search[$key] = "p.createdOn BETWEEN $value";
			} elseif ($key == 'age_range' && $value != '') {
				$search[$key] = "YEAR(CURDATE())-YEAR(birthdate) BETWEEN $value";
			} elseif ($key == 'age_text' && $value != '') {
				$search[$key] = "YEAR(CURDATE())-YEAR(birthdate) = $value";
			} elseif ($key == 'type' && $value != '') {
				$search[$key] = "$key = '" . $value . "'";
			} elseif ($key == 'blocked' && $value != null) {
				$search[$key] = "p.$key = '" . $value . "'";
			} elseif ($key == 'has_deposited' && $value != null) {
				$search[$key] = "wa.dwStatus = 'approved' AND wa.transactionType = 'deposit'";
			} elseif ($key == 'status' && $value != null) {
				$search[$key] = "p.$key = '" . $value . "'";
			} elseif ($key == 'gameId' && $value != null) {
				$search[$key] = "pg.$key = '" . $value . "' AND pg.blocked = '1'";
			} elseif ($key == 'affiliate' && $value != null) {
				$search[$key] = "pa.type = 'affiliate' AND pa.typeId = '" . $value . "'";
			} elseif ($value != null) {
				$search[$key] = "$key LIKE '%" . $value . "%'";
			}
		}

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$query = "SELECT DISTINCT p.*, pd.*, pa.*, t.tagName, YEAR(CURDATE())-YEAR(birthdate) AS age, CONCAT(rls.rankingLevelGroup, ' ', rls.rankingLevel) AS level, pg.*, p.status FROM player AS p INNER JOIN rankingLevelSetting as rls on p.playerLevel = rls.rankingLevelSettingId LEFT JOIN playerdetails AS pd ON p.playerId = pd.playerId LEFT JOIN playeraccount AS pa ON p.playerId = pa.playerId LEFT JOIN walletaccount AS wa ON pa.playerAccountId = wa.playerAccountId LEFT JOIN playergame AS pg ON p.playerId = pg.playerId LEFT JOIN playertag AS pt ON p.playerId = pt.playerId LEFT JOIN tag AS t ON pt.tagId = t.tagId ";

		if (count($search) > 0) {
			$query .= "WHERE " . implode(' OR ', $search);
		}

		$query .= " AND rankingLevelGroup = 'VIP' AND pa.type = 'wallet'";

		$query .= " ORDER BY $sort_by $in $limit $offset";

		$result = $this->db->query($query);

		if (!$result->result_array()) {
			return false;
		} else {
			return $result->result_array();
		}
	}

	/**
	 * Will change player status based on the passed parameters
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	public function populateBlack($data, $sort_by, $in, $limit, $offset) {
		$internal_accounts = '';
		$search = array();

		foreach ($data as $key => $value) {
			if ($key == 'playerId' && $value != '') {
				$search[$key] = "p.$key = '" . $value . "'";
			} elseif ($key == 'signup_range' && $value != '') {
				$search[$key] = "pt.createdOn BETWEEN $value";
			} elseif ($value != null) {
				$search[$key] = "pt.$key = '" . $value . "'";
			}
		}

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$query = "SELECT DISTINCT p.*, pd.*, t.tagId, t.tagName, vipst.groupName, vipcbr.vipLevel, p.status, pt.createdOn as tagged_at
		FROM player AS p
		LEFT JOIN playerdetails AS pd ON p.playerId = pd.playerId
		LEFT JOIN playerlevel AS pl ON p.playerId = pl.playerId
		LEFT JOIN vipsettingcashbackrule as vipcbr on vipcbr.vipsettingcashbackruleId = pl.playerGroupId
		LEFT JOIN vipsetting as vipst on vipst.vipSettingId = vipcbr.vipSettingId
		LEFT JOIN playeraccount AS pa ON p.playerId = pa.playerId
		LEFT JOIN playertag AS pt ON p.playerId = pt.playerId
		LEFT JOIN tag AS t ON pt.tagId = t.tagId
		";

		if (count($search) > 0) {
			$query .= "WHERE " . implode(' AND ', $search);
		}

		$query .= " AND pa.type = 'wallet'";

		$query .= " ORDER BY $sort_by $in $limit $offset";

		$result = $this->db->query($query);

		if (!$result->result_array()) {
			return false;
		} else {
			return $result->result_array();
		}
	}

	/**
	 * Will get all blacklist players
	 *
	 * @param 	int
	 * @param 	int
	 * @return	array
	 */
	public function getBlacklist($sort_by, $in, $limit, $offset) {

		// $query = $this->db->query("SELECT p.*, pd.*,vipcbr.vipLevel,vipcbr.vipLevelName,vipst.groupName, pa.typeOfPlayer, pa.type, pa.typeId, t.tagName, p.status
		// 	FROM player AS p
		// 	LEFT JOIN playeraccount AS pa ON p.playerId = pa.playerId
		// 	LEFT JOIN playerdetails AS pd ON p.playerId = pd.playerId
		// 	LEFT JOIN playerlevel as pl on p.playerId = pl.playerId
		// 	LEFT JOIN playertag AS pt ON p.playerId = pt.playerId
		// 	LEFT JOIN vipsettingcashbackrule as vipcbr on vipcbr.vipsettingcashbackruleId = pl.playerGroupId
		// 	LEFT JOIN vipsetting as vipst on vipst.vipSettingId = vipcbr.vipSettingId
		// 	LEFT JOIN tag AS t ON pt.tagId = t.tagId WHERE pa.type = 'wallet' $where
		// 	ORDER BY $sort_by $in $limit $offset
		// ");

		$this->db->select('p.*, pd.*,vipcbr.vipLevel,vipcbr.vipLevelName,vipst.groupName, pa.typeOfPlayer, pa.type, pa.typeId, t.tagId, t.tagName, p.status,pt.createdOn as tagged_at');
		$this->db->from('player as p');
		$this->db->join('playeraccount as pa', 'p.playerId = pa.playerId', 'left');
		$this->db->join('playerdetails AS pd', 'p.playerId = pd.playerId', 'left');
		$this->db->join('playerlevel as pl', 'p.playerId = pl.playerId', 'left');
		$this->db->join('playertag as pt', 'p.playerId = pt.playerId', 'left');
		$this->db->join('vipsettingcashbackrule as vipcbr', 'vipcbr.vipsettingcashbackruleId = pl.playerGroupId', 'left');
		$this->db->join('vipsetting as vipst', 'vipst.vipSettingId = vipcbr.vipSettingId', 'left');
		$this->db->join('tag as t', 'pt.tagId = t.tagId', 'left');
		$this->db->where('pa.type', 'wallet');
		$this->db->where('pt.tagId !=', '0');

		if ($sort_by == 'active') {
			$this->db->where('p.status', '0');
			$sort_by = 'username';
		}

		if ($sort_by == 'inactive') {
			$this->db->where('p.status', '1');
			$sort_by = 'username';
		}

		$this->db->order_by($sort_by, $in);

        if (!empty($limit)) {
            if (!empty($offset)) {
                $this->db->limit($limit,$offset);
            }else{
                $this->db->limit($limit);
            }
        }

		$query = $this->db->get();

		return $query->result_array();
	}

	public function checkIfPlayerIsTagged($playerId, $tagId = NULL) {
		$sql = "SELECT T.tagId, T.tagName FROM playertag AS PT LEFT JOIN tag AS T ON T.tagId = PT.tagId WHERE PT.playerId = ? ";

		$where = array($playerId);
		if(NULL !== $tagId){
			$sql .= "AND PT.tagId = ?";
			$where[] = $tagId;
		}
		$query = $this->db->query($sql, $where);
		if(empty($query)){
			return FALSE;
		}

		$res = $query->result_array();

		$count = count($res);
		if ($count > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Will get all affiliate players
	 *
	 * @param 	int
	 * @param 	int
	 * @return	array
	 */
	public function getBatchAccount($limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		// $query = $this->db->query("SELECT * FROM batch
		// 	 $limit
		// 	 $offset
		//  ");

		$this->db->select('*');
		$this->db->from('batch');

		$query = $this->db->get();

		return $query->result_array();
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
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		// $query = $this->db->query("SELECT * FROM
		// 	batch where
		// 	name LIKE
		// 	'%" . $search . "%'
		// 	$limit
		// 	$offset
		// ");

		$this->db->select('*');
		$this->db->from('batch');
		$this->db->like('name', $search);

		$query = $this->db->get();

		return $query->result_array();
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
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		// $query = $this->db->query("SELECT * FROM
		// 	batch ORDER BY $sort ASC
		// 	$limit
		// 	$offset
		// ");

		$this->db->select('*');
		$this->db->from('batch');
		$this->db->order_by($sort, 'asc');

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * Will get affiliate code based on the passed parameter
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getBatchCode() {
		// $query = $this->db->query("SELECT typeCode FROM batch ORDER BY batchId DESC");

		$this->db->select('typeCode');
		$this->db->from('batch');
		$this->db->order_by('batchId', 'desc');

		$query = $this->db->get();

		return $query->row_array();
	}

	/**
	 * Will add players affiliate based on the passed parameter
	 *
	 * @param 	array
	 * @return	array
	 */
	public function addBatchPlayer($data) {
		$this->db->insert('batch', $data);

		// $query = $this->db->query("SELECT batchId FROM batch ORDER BY batchId DESC");

		$this->db->select('batchId');
		$this->db->from('batch');
		$this->db->order_by('batchId', 'desc');

		$query = $this->db->get();

		return $query->row_array();
	}

	/**
	 * Will check if batch is already existing
	 *
	 * @param 	string
	 * @return	array
	 */
	public function checkBatchExist($name) {
		$query = $this->db->query("SELECT * FROM player where username REGEXP '^" . $name . "[0-9]+$'");

		if (!empty($query->row_array())) {
			return TRUE;
		} else {
			return FALSE;
		}
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
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$sql = "SELECT p.*, pa.*, b.batchId, b.name, b.count, b.typecode FROM player AS p
		INNER JOIN playeraccount AS pa ON p.playerId = pa.playerId
		INNER JOIN batch AS b ON pa.typeId = b.batchId
		WHERE pa.type = ? AND b.batchId = ? ";

		$query = $this->db->query($sql, array('batch', $batch_id));

		return $query->result_array();
	}

	/**
	 * Will get player using the passed parameter
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getBatchAccountByPlayerId($player_id) {
		$sql = "SELECT * FROM player AS p INNER JOIN playeraccount AS pa ON p.playerId = pa.playerId WHERE p.playerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		return $query->row_array();
	}

	/**
	 * Will get affiliate based on affiliate id
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getBatchByPlayerBatchId($batch_id) {
		$sql = "SELECT * FROM batch WHERE batchId = ? ";

		$query = $this->db->query($sql, array($batch_id));

		return $query->row_array();
	}

	/**
	 * Will edit player affiliate based on passed parameters
	 *
	 * @param 	array
	 * @param 	int
	 * @return	array
	 */
	public function editAccountBatch($data, $batch_id) {
		$this->db->where('batchId', $batch_id);
		$this->db->update('batch', $data);
	}

	/**
	 * Will delete player affiliate based on passed parameter
	 *
	 * @param 	int
	 */
	public function deletePlayerBatch($batch_id) {
		$this->db->where('batchId', $batch_id);
		$this->db->delete('batch');
	}

	/**
	 * Will delete player affiliate based on passed parameter
	 *
	 * @param 	int
	 */
	public function deletePlayerDetails($player_id) {
		$this->db->where('playerId', $player_id);
		$this->db->delete('playerdetails');
	}

	/**
	 * Will delete player affiliate based on passed parameter
	 *
	 * @param 	int
	 */
	// public function deletePlayerAccountBatch($type, $type_id) {
	// 	$where = "type = '" . $type . "' AND typeId = '" . $type_id. "' AND typeOfPlayer = 'demo' OR typeOfPlayer = 'affiliate'";
	// 	$this->db->where($where);
	public function deletePlayerAccountBatch($player_id) {
		$this->db->where('playerId', $player_id);
		$this->db->delete('playeraccount');
	}

	/**
	 * Will delete player affiliate based on passed parameter
	 *
	 * @param 	int
	 */
	public function deletePlayerAccountPlayer($player_id, $type, $type_id) {
		//$where = "playerId = '" . $player_id . "' AND type = '" . $type . "' AND typeId = '" . $type_id. "'";
		$this->db->where('playerId', $player_id);
		$this->db->delete('playeraccount');
	}

	/**
	 * Will edit player affiliate based on passed parameters
	 *
	 * @param 	int
	 */
	public function deletePlayerByPlayerAffiliateId($player_affiliate_id) {
		$this->db->where('playerAffiliateId', $player_affiliate_id);
		$this->db->delete('player');
	}

	/**
	 * Will get player using the passed parameter
	 *
	 * @param 	int $player_id
	 * @param 	array|string $selectFields Default '*',or array for key-value, fieldname-fieldvalue
	 *
	 * @return	array
	 */
	public function getPlayerByPlayerId($player_id, $selectFields = null) {
		if( empty($selectFields) ){ // for default
			$selectFields = '*';
		}
		$selectFieldArray = [];
		if(is_array($selectFields)){
			foreach($selectFields as $fieldname => $fieldAlias){
				$selectFieldArray[] = $fieldname. ' as '. $fieldAlias;
			}
			$selectFieldStr = implode(' , ', $selectFieldArray);
		}else{
			$selectFieldStr = $selectFields;
		}

		$sql = "SELECT $selectFieldStr FROM player WHERE playerId = ? ";
		$this->utils->debug_log('sql', $sql);
		$query = $this->db->query($sql, array($player_id));
		$row = $query->row_array();
		$query->free_result(); // free $query.
		return $row;
	} // EOF getPlayerByPlayerId

	/**
	 * Will edit player using the passed parameter
	 *
	 * @param 	array
	 * @param 	int
	 */
	public function editPlayer($data, $player_id) {
		$this->db->where('playerId', $player_id);
		$this->db->update('player', $data);
	}

	/**
	 * Will edit player deatils using the passed parameter
	 *
	 * @param 	array
	 * @param 	int
	 */
	public function editPlayerDetails($data, $player_id) {
		$this->db->where('playerId', $player_id);
		$this->db->update('playerdetails', $data);
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
		$where = "playerId = '" . $player_id . "' AND type = '" . $type . "' AND typeId = '" . $type_id . "'";
		$this->db->where($where);
		$this->db->update('playeraccount', $data);
	}

	/**
	 * Will delete player using the passed parameter
	 *
	 * @param 	int
	 */
	public function deletePlayer($player_id) {
		$this->db->where('playerId', $player_id);
		$this->db->delete('player');
	}

	/**
	 * Will get all tags
	 *
	 * @return 	array
	 */
	public function getAllTags() {
		$query = $this->db->query("SELECT * FROM tag AS t LEFT JOIN adminusers AS au ON t.createBy = au.userId");

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}
	/**
	 * Will get all tags Only
	 *
	 * @return 	array
	 */
	public function getAllTagsOnly() {
		$query = $this->db->query("SELECT * FROM tag ");

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * Will get tag based on the player id
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getPlayerTag($player_id) {
		$sql = "SELECT * FROM playertag AS pt INNER JOIN tag AS t ON pt.tagId = t.tagId WHERE pt.playerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	/**
	 * Will get tag based on the player id
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getPlayerTags($player_id, $only_tag_id = FALSE) {
		$sql = "SELECT * FROM playertag AS pt INNER JOIN tag AS t ON pt.tagId = t.tagId WHERE pt.playerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		if (!$query->result_array()) {
			return false;
		} else {
			if(FALSE === $only_tag_id){
				return $query->result_array();
			}else{
				$results = $query->result_array();
				$tagIds = [];
				foreach($results as $result){
					$tagIds[] = $result['tagId'];
				}

				return $tagIds;
			}
		}
	}


	/**
	 * Will get tag based on the name of the tag
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function getPlayerTagByName($tag_name) {
		$sql = "SELECT * FROM tag WHERE tagName = ? ";

		$query = $this->db->query($sql, array($tag_name));

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
	public function getVipGroupName($group_name) {
		$sql = $this->db->query("SELECT * FROM vipsetting WHERE groupName = ? ");

		$query = $this->db->query($sql, array($group_name));

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
		$sql = "SELECT * FROM vipsettingcashbackrule WHERE vipsettingcashbackruleId = ? ";

		$query = $this->db->query($sql, array($vipgrouplevelId));

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
	public function getCashbackBonusPerGame($vipsettingcashbackruleId) {
		$sql = "SELECT * FROM vipsettingcashbackbonuspergame WHERE vipsettingcashbackruleId = ? ";

		$query = $this->db->query($sql, array($vipsettingcashbackruleId));

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	/**
	 * Will get tag (first row) based on the id
	 *
	 * @param 	int The tagId of tag.
	 * @param &object The query, for get all rows with foreach ($query->result() as $row){...}
	 * @return 	array The first row.
	 *
	 */
	public function getPlayerTagById($tag_id, &$query) {
		$sql = "SELECT * FROM playertag AS pt INNER JOIN tag AS t ON pt.tagId = t.tagId WHERE pt.tagId = ? ";

		$query = $this->db->query($sql, array($tag_id));

		if ($query->num_rows() > 0)
		{
			if (!$query->row_array()) {
				return false;
			} else {
				return $query->row_array();
			}
		}else{
			return false;
		}
	}

	/**
	 * Will change the player's tag based on the passed parameters
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	public function changeTag($player_id, $data) {

		$this->db->where('playerId', $player_id);
		$this->db->update('playertag', $data);
	}

	/**
	 * Will change the player's tag based on the passed parameters
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getReferralByPlayerId($player_id) {
		$sql = "SELECT pb.username AS inviter
		FROM player AS p
		INNER JOIN playerfriendreferral AS pfr ON p.playerId = pfr.invitedPlayerId
		LEFT OUTER JOIN player AS pb
		ON pfr.playerId = pb.playerId
		WHERE p.playerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	/**
	 * get all referral of player
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getAllReferralByPlayerId($player_id) {
		$sql = "SELECT * FROM playerfriendreferral WHERE playerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * Will change the player's tag based on the passed parameters
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getReferralByCode($referral_code) {
		$sql = "SELECT playerId, username FROM player WHERE invitationCode = ? ";

		$query = $this->db->query($sql, array($referral_code));

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	/**
	 * Will change the player's tag based on the passed parameters
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getReferredPlayer($player_id) {
		$sql = "SELECT * FROM  playerfriendreferral AS pfr
		INNER JOIN  player AS p ON pfr.invitedPlayerId = p.playerId
		LEFT JOIN playerdetails AS pd on p.playerId = pd.playerId
		WHERE pfr.invitedPlayerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	// /**
	//  * Will change the player's tag based on the passed parameters
	//  *
	//  * @param int $player_id The player.playerId
	//  * @param array|string $selectFields Default '*',or array for key-value, fieldname-fieldvalue
	//  * @return array|boolean $row The row, key-value:fieldname-value. no data is false.
	//  */
	// public function getReferredPlayer($player_id, $selectFields = null) {
	// 	$this->load->model(['player_friend_referral']);
	//
	// 	if( empty($selectFields) ){ // for default
	// 		// $selectFields['affiliates.username'] = 'username';
	// 		$selectFields = '*';
	// 	}
	//
	// 	$selectFieldArray = [];
	// 	if(is_array($selectFields)){
	// 		foreach($selectFields as $fieldname => $fieldAlias){
	// 			$selectFieldArray[] = $fieldname. ' as '. $fieldAlias;
	// 		}
	// 		$selectFieldStr = implode(' , ', $selectFieldArray);
	// 	}else{
	// 		$selectFieldStr = $selectFields;
	// 	}
	//
	// 	$sql = "SELECT $selectFieldStr FROM  playerfriendreferral AS pfr
	// 	INNER JOIN  player AS p ON pfr.invitedPlayerId = p.playerId
	// 	LEFT JOIN playerdetails AS pd on p.playerId = pd.playerId
	// 	WHERE pfr.invitedPlayerId = ?
	// 	AND pfr.status = ?";
	//
	// 	$query = $this->db->query($sql, array($player_id, Player_friend_referral::STATUS_NORMAL));
	// 	$row = $query->row_array();
	// 	$query->free_result(); // $query 物件將不再使用了
	//
	// 	if (!$row) {
	// 		return false;
	// 	} else {
	// 		return $row;
	// 	}
	// }

	/**
	 * Will block the players based on the passed parameters
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	public function blockPlayer($player_id, $data) {
		$this->db->where('playerId', $player_id);
		$this->db->update('player', $data);
	}

	/**
	 * Will get the chat history
	 *
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function getChatHistory($limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$query = $this->db->query("SELECT * FROM `playerchat` GROUP BY
			session ORDER BY
			chatId ASC
			$limit
			$offset
			");

		return $query->result_array();
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
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$sql = "SELECT * FROM `playerchat` WHERE sender LIKE ? OR recepient LIKE ? GROUP BY session $limit $offset";

		$query = $this->db->query($sql, array('%' . $search . '%', '%' . $search . '%'));

		return $query->result_array();
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
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$query = $this->db->query("SELECT * FROM `playerchat` GROUP BY session ORDER BY $sort ASC $limit $offset");

		return $query->result_array();
	}

	/**
	 * Will get chat history based on the session
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getChatHistoryByPlayerId($session, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$sql = "SELECT * FROM `playerchat` where session = ? $limit $offset";

		$query = $this->db->query($sql, array($session));

		return $query->result_array();
	}

	/**
	 * Will get chat history based on the session
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getChatHistoryById($player_id, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$sql = "SELECT * FROM `playerchat` WHERE playerId = ? GROUP BY session ORDER BY chatId ASC $limit $offset";

		$query = $this->db->query($sql, array($player_id));

		return $query->result_array();
	}

	/**
	 * Will delete chat history based on the session
	 *
	 * @param 	int
	 */
	public function deleteChatHistory($session) {
		$where = "session = '" . $session . "'";
		$this->db->where($where);
		$this->db->delete('playerchat');
	}

	/**
	 * Will delete vip group level
	 *
	 * @param 	int
	 */
	public function deletevipgrouplevel($vipgrouplevelId) {
		$where = "vipsettingcashbackruleId = '" . $vipgrouplevelId . "'";
		$this->db->where($where);
		$this->db->delete('vipsettingcashbackrule');
	}

	/**
	 * Will get all game history
	 *
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function getAllGameHistory($limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$query = $this->db->query("SELECT *, (SELECT game FROM game AS g WHERE g.gameId = pgh.gameId) AS game
			FROM player AS p
			INNER JOIN playergamehistory AS pgh
			ON p.playerId = pgh.playerId
			WHERE pgh.status = '1'
			$limit
			$offset
			");

		return $query->result_array();
	}

	/**
	 * Will get all game history
	 *
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function getGameHistoryByPlayerId($player_id) {
		$sql = "SELECT * FROM player AS p INNER JOIN playergamehistory AS pgh ON p.playerId = pgh.playerId INNER JOIN playergamehistorydetails AS pghd ON pgh.gameHistoryId = pghd.gameHistoryId where p.playerid = ? ";

		$query = $this->db->query($sql, array($player_id));

		return $query->result_array();
	}

	/**
	 * Will get all game history
	 *
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function getActivePlayersByGameHistory($player_id) {
		$sql = "SELECT * FROM player AS p INNER JOIN playergamehistory AS pgh ON p.playerId = pgh.playerId INNER JOIN playergamehistorydetails AS pghd ON pgh.gameHistoryId = pghd.gameHistoryId where p.playerid = ? ORDER BY historyDetailsId DESC";

		$query = $this->db->query($sql, array($player_id));

		return $query->row_array();
	}

	/**
	 * Will get game history based on id
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getGameHistoryById($game_history_id, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$sql = "SELECT pghd.*, (SELECT p.username FROM player as p where p.playerId = pgh.playerId) as username
		FROM playergamehistorydetails as pghd
		INNER JOIN playergamehistory as pgh ON pghd.gameHistoryId = pgh.gameHistoryId
		WHERE pghd.gameHistoryId = ?
		$limit
		$offset";

		$query = $this->db->query($sql, array($game_history_id));

		return $query->result_array();
	}

	/**
	 * Will change the player's tag based on the passed parameters
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getPlayerTotalBalance($player_id) {
		$sql_deposit = "SELECT SUM(wa.amount) AS deposit
		FROM playeraccount AS pa
		LEFT JOIN walletaccount AS wa
		ON pa.playeraccountid = wa.playeraccountid
		LEFT JOIN walletaccountdetails AS wad
		ON wa.walletaccountid = wad.walletaccountid
		WHERE pa.playerid = ? AND dwStatus = ? AND transactionType = ? AND wa.status = ? ";

		$query_deposit = $this->db->query($sql_deposit, array($player_id, 'approved', 'deposit', '0'));

		$sql_withdrawal = "SELECT SUM(wa.amount) AS withdrawal
		FROM playeraccount AS pa
		LEFT JOIN walletaccount AS wa
		ON pa.playeraccountid = wa.playeraccountid
		LEFT JOIN walletaccountdetails AS wad
		ON wa.walletaccountid = wad.walletaccountid
		WHERE pa.playerid = ? AND dwStatus = ? AND transactionType = ? AND wa.status = ? ";

		$query_withdrawal = $this->db->query($sql_deposit, array($player_id, 'approved', 'withdrawal', '0'));

		$deposit = $query_deposit->row_array();
		$withdrawal = $query_withdrawal->row_array();

		$totalBalance = $deposit['deposit'] - $withdrawal['withdrawal'];

		if (!$totalBalance) {
			return false;
		} else {
			return $totalBalance;
		}
	}

	/**
	 * Will get game history based on id
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function searchGameHistory($search, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$sql = "SELECT *, (SELECT game FROM game as g where g.gameId = pgh.gameId) as game
		FROM player AS p INNER JOIN
		playergamehistory AS pgh
		ON p.playerId = pgh.playerId
		where pgh.status = ?
		AND p.username LIKE ?
		$limit
		$offset";

		$query = $this->db->query($sql, array('1', '%' . $search . '%'));

		return $query->result_array();
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
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$query = $this->db->query("SELECT *, (SELECT game FROM game as g where g.gameId = pgh.gameId) as game
			FROM player AS p INNER JOIN
			playergamehistory AS pgh
			ON p.playerId = pgh.playerId
			where pgh.status = '1'
			ORDER BY $sort ASC
			$limit
			$offset
			");

		return $query->result_array();
	}

	/**
	 * Will change player status based on the passed parameters
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	public function changePlayerStatus($player_id, $data) {
		$this->db->where('playerId', $player_id);
		$this->db->update('player', $data);
	}

	/**
	 * Will change player status based on the passed parameters
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	public function populatePeriodOfTime($start_date, $end_date) {
		$sql = "SELECT p.*, t.tagName FROM player AS p LEFT JOIN playertag AS pt ON p.playerId = pt.playerId LEFT JOIN tag AS t ON pt.tagId = t.tagId WHERE p.createdOn >= ? AND p.createdOn <= ? ";

		$query = $this->db->query($sql, array($start_date, $end_date));

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * Will change player status based on the passed parameters
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	public function populateUsername($username) {
		$sql = "SELECT p.*, pd.*, t.tagName FROM player AS p INNER JOIN playerdetails AS pd ON p.playerId = pd.playerId LEFT JOIN playertag AS pt ON p.playerId = pt.playerId LEFT JOIN tag AS t ON pt.tagId = t.tagId WHERE p.username LIKE ? ";

		$query = $this->db->query($sql, array('%' . $username . '%'));

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * searchAllPlayer
	 * move to player_model
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	public function searchAllPlayer($search, $sort_by, $in, $limit, $offset) {
		//var_dump($sort_by);exit();
		$this->db->distinct();
		$this->db->select('player.playerId,
			player.username,
			player.email,
			player_runtime.lastLoginTime,
			player_runtime.lastLoginIp,
			player.createdOn,
			player.registered_by,
			player.status,
			player.invitationCode,
			playeraccount.type,
			playeraccount.typeId,
			playeraccount.typeOfPlayer,
			playerdetails.firstName,
			playerdetails.lastName,
			playerdetails.registrationIP,
			playerdetails.imAccount,
			playerdetails.imAccount2,
			playerdetails.city,
			playerdetails.country,
			vipsettingcashbackrule.vipLevel,
			vipsettingcashbackrule.vipLevelName,
			vipsetting.groupName,
			tag.tagName, ', FALSE)
			->from('player');
		$this->db->join('playeraccount', 'playeraccount.playerId = player.playerId', 'left');
		if ($search['deposit_amount'] != '') {
			//var_dump($search['deposit_amount']);exit();
			$this->db->join('walletaccount', 'walletaccount.playerAccountId = playeraccount.playerAccountId', 'left');
		}
		$this->db->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left');
		$this->db->join('player_runtime', 'player_runtime.playerId = player.playerId', 'left');
		$this->db->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left');
		$this->db->join('playerpromo', 'playerpromo.playerId = player.playerId', 'left');
		$this->db->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left');
		$this->db->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$this->db->join('playertag', 'playertag.playerId = player.playerId', 'left');
		$this->db->join('playergame', 'playergame.playerId = player.playerId', 'left');
		$this->db->join('tag', 'tag.tagId = playertag.tagId', 'left');

		// $this->db->order_by('player.username',$sort_by);

		if ($search['search_by'] == 2) {
			$search['username'] == '' ? '' : $this->db->where('player.username', $search['username']);
		} elseif ($search['search_by'] == 1) {
			$search['username'] == '' ? '' : $this->db->like('player.username', $search['username']);
		}

		$search['signup_startdate'] == '' ? '' : $this->db->where('player.createdOn >= ', $search['signup_startdate'] . ' 00:00:00');
		$search['signup_enddate'] == '' ? '' : $this->db->where('player.createdOn <= ', $search['signup_enddate'] . ' 23:59:59');
		$search['firstName'] == '' ? '' : $this->db->where('playerdetails.firstName', $search['firstName']);
		$search['lastName'] == '' ? '' : $this->db->where('playerdetails.lastName', $search['lastName']);
		$search['email'] == '' ? '' : $this->db->where('player.email', $search['email']);
		$search['registrationWebsite'] == '' ? '' : $this->db->where('playerdetails.registrationWebsite', $search['registrationWebsite']);
		$search['city'] == '' ? '' : $this->db->where('playerdetails.city', $search['city']);
		$search['country'] == '' ? '' : $this->db->where('playerdetails.country', $search['country']);
		$imSearch = "playerdetails.imAccount = '" . $search['imAccount'] . "' OR playerdetails.imAccount2 = '" . $search['imAccount'] . "'";
		$search['imAccount'] == '' ? '' : $this->db->where($imSearch);
		$search['status'] == '' ? '' : $this->db->where('player.status', $search['status']);
		$search['registered_by'] == '' ? '' : $this->db->where('player.registered_by', $search['registered_by']);
		$search['registrationIP'] == '' ? '' : $this->db->where('playerdetails.registrationIP', $search['registrationIP']);
		$search['tagId'] == '' ? '' : $this->db->where('playertag.playerTagId', $search['tagId']);
		$search['referral_id'] == '' ? '' : $this->db->where('player.invitationCode', $search['referral_id']);

		if ($search['blocked'] != '') {
			$this->db->where('playergame.gameId', $search['blocked']);
			$this->db->where('playergame.blocked', '2');
		}

		$search['playerLevel'] == '' ? '' : $this->db->where('playerlevel.playerGroupId', $search['playerLevel']);

		if ($search['affiliate'] != '') {
			$this->db->where('playeraccount.type', 'affiliate');
			$this->db->where('playeraccount.typeId', $search['affiliate']);
		}

		$search['promoId'] == '' ? '' : $this->db->where('playerpromo.promorulesId', $search['promoId']);
		$search['account_type'] == '' ? '' : $this->db->where('playeraccount.typeOfPlayer', $search['account_type']);

		if ($search['deposit_order'] == 1) {
			if ($search['deposit_amount_type'] == 1) {
				$search['deposit_amount'] == '' ? '' : $this->db->where('walletaccount.amount < ', $search['deposit_amount']);
			} elseif ($search['deposit_amount_type'] == 2) {
				$search['deposit_amount'] == '' ? '' : $this->db->where('walletaccount.amount > ', $search['deposit_amount']);
			}
			$this->db->where('walletaccount.dwCount', 1);

			$this->db->where('walletaccount.processDatetime >= ', $search['deposit_date_from'] . ' 00:00:00');
			$this->db->where('walletaccount.processDatetime <= ', $search['deposit_date_to'] . ' 23:59:59');
		} elseif ($search['deposit_order'] == 2) {
			if ($search['deposit_amount_type'] == 1) {
				$search['deposit_amount'] == '' ? '' : $this->db->where('walletaccount.amount < ', $search['deposit_amount']);
			} elseif ($search['deposit_amount_type'] == 2) {
				$search['deposit_amount'] == '' ? '' : $this->db->where('walletaccount.amount > ', $search['deposit_amount']);
			}
			$this->db->where('walletaccount.dwCount', 2);
			$this->db->where('walletaccount.walletType', 'Main');
			$this->db->where('walletaccount.dwStatus', 'approved');
			$this->db->where('walletaccount.processDatetime >= ', $search['deposit_date_from'] . ' 00:00:00');
			$this->db->where('walletaccount.processDatetime <= ', $search['deposit_date_to'] . ' 23:59:59');
		} elseif ($search['deposit_order'] == 3) {
			$search['deposit_date_from'] == '' ? '' : $this->db->where('walletaccount.processDatetime > ', $search['deposit_date_from'] . ' 00:00:00');
			$search['deposit_date_to'] == '' ? '' : $this->db->where('walletaccount.processDatetime < ', $search['deposit_date_to'] . ' 23:59:59');
			$this->db->where('walletaccount.walletaccountId', null);
			$this->db->join('walletaccount', 'walletaccount.playerAccountId = playeraccount.playerAccountId', 'left');
		} elseif ($search['deposit_order'] == 4) {
			$search['deposit_date_from'] == '' ? '' : $this->db->where('walletaccount.processDatetime > ', $search['deposit_date_from'] . ' 00:00:00');
			$search['deposit_date_to'] == '' ? '' : $this->db->where('walletaccount.processDatetime < ', $search['deposit_date_to'] . ' 23:59:59');
			if ($search['deposit_amount_type'] == 1) {
				$this->db->where('walletaccount.amount <', $search['deposit_amount']);
			} elseif ($search['deposit_amount_type'] == 2) {
				$this->db->where('walletaccount.amount >', $search['deposit_amount']);
			}
			$this->db->where('walletaccount.walletaccountId is not null', null, false);
			$this->db->join('walletaccount', 'walletaccount.playerAccountId = playeraccount.playerAccountId', 'left');
		}

		if ($search['wallet_order'] == 1) {
			//var_dump($search['wallet_order']);exit();
			if ($search['wallet_amount_type'] == 1) {
				$search['wallet_amount'] == '' ? '' : $this->db->where('playeraccount.totalBalanceAmount < ', $search['wallet_amount']);
			} elseif ($search['wallet_amount_type'] == 2) {
				$search['wallet_amount'] == '' ? '' : $this->db->where('playeraccount.totalBalanceAmount >', $search['wallet_amount']);
			}
			$this->db->where('playeraccount.typeId', 0);
			$this->db->where('playeraccount.type', 'wallet');

		} elseif ($search['wallet_order'] == 2) {
			//var_dump($search['wallet_order']);exit();
			if ($search['wallet_amount_type'] == 1) {
				$search['wallet_amount'] == '' ? '' : $this->db->where('playeraccount.totalBalanceAmount < ', $search['wallet_amount']);
			} elseif ($search['wallet_amount_type'] == 2) {
				$search['wallet_amount'] == '' ? '' : $this->db->where('playeraccount.totalBalanceAmount >', $search['wallet_amount']);
			}
			$this->db->where('playeraccount.typeId', 1);
			$this->db->where('playeraccount.type', 'subwallet');
		} elseif ($search['wallet_order'] == 3) {
			//var_dump($search['wallet_order']);exit();
			if ($search['wallet_amount_type'] == 1) {
				$search['wallet_amount'] == '' ? '' : $this->db->where('playeraccount.totalBalanceAmount < ', $search['wallet_amount']);
			} elseif ($search['wallet_amount_type'] == 2) {
				$search['wallet_amount'] == '' ? '' : $this->db->where('playeraccount.totalBalanceAmount >', $search['wallet_amount']);
			}
			$this->db->where('playeraccount.typeId', 2);
			$this->db->where('playeraccount.type', 'subwallet');
		} else {
			$this->db->where('playeraccount.type', 'wallet');
		}

		// if($search['first_deposited'] == TRUE && $search['second_deposited'] == ''){
		// 	if($search['deposit_amount_type'] == 1){
		// 		$this->db->where('walletaccount.amount < ', $search['deposit_amount']);
		// 	}elseif($search['deposit_amount_type'] == 2){
		// 		$this->db->where('walletaccount.amount > ', $search['deposit_amount']);
		// 	}
		// 	$this->db->where('walletaccount.dwCount', 1);
		// }
		// elseif($search['first_deposited'] == '' && $search['second_deposited'] == TRUE){
		// 	if($search['deposit_amount_type'] == 1){
		// 		$this->db->where('walletaccount.amount < ', $search['deposit_amount']);
		// 	}elseif($search['deposit_amount_type'] == 2){
		// 		$this->db->where('walletaccount.amount > ', $search['deposit_amount']);
		// 	}
		// 	$this->db->where('walletaccount.dwCount', 2);
		// 	//$this->db->where('walletaccount.walletType', 'Main');
		// 	//$this->db->where('walletaccount.dwStatus', 'approved');
		// }
		// elseif($search['first_deposited'] == TRUE && $search['second_deposited'] == TRUE){
		// 	if($search['deposit_amount_type'] == 1){
		// 		$this->db->where('walletaccount.amount < ', $search['deposit_amount']);
		// 	}elseif($search['deposit_amount_type'] == 2){
		// 		$this->db->where('walletaccount.amount > ', $search['deposit_amount']);
		// 	}
		// 	$depositCnt = array('1','2');
		// 	$this->db->where_in('walletaccount.dwCount', $depositCnt);
		// }
		// isset($sortBy['paymentReportSortByPlayerLevel']) == TRUE ? $sortBy['paymentReportSortByPlayerLevel'] == '' ? '' : $this->db->where('vipsettingcashbackrule.vipsettingcashbackruleId', $sortBy['paymentReportSortByPlayerLevel']) : '';
		// isset($sortBy['paymentReportSortByTransaction']) == TRUE ? $sortBy['paymentReportSortByTransaction'] == '' ? '' : $this->db->where('walletaccount.transactionType', $sortBy['paymentReportSortByTransaction']) : '';
		// isset($sortBy['paymentReportSortByTransactionStatus']) == TRUE ? $sortBy['paymentReportSortByTransactionStatus'] == '' ? '' : $this->db->where('walletaccount.dwStatus', $sortBy['paymentReportSortByTransactionStatus']) : '';
		// isset($sortBy['paymentReportSortByDWAmountLessThan']) == TRUE ? $sortBy['paymentReportSortByDWAmountLessThan'] == '' ? '' : $this->db->where('walletaccount.amount <=',$sortBy['paymentReportSortByDWAmountLessThan']): '';
		// isset($sortBy['paymentReportSortByDWAmountGreaterThan']) == TRUE ? $sortBy['paymentReportSortByDWAmountGreaterThan'] == '' ? '' : $this->db->where('walletaccount.amount >=',$sortBy['paymentReportSortByDWAmountGreaterThan']): '';
		// isset($sortBy['paymentReportSortByDateRangeValueStart']) == TRUE ? $sortBy['paymentReportSortByDateRangeValueStart'] == '' ? '' : $this->db->where('walletaccount.processDatetime >=',$sortBy['paymentReportSortByDateRangeValueStart'].' 00:00:00'): '';
		// isset($sortBy['paymentReportSortByDateRangeValueEnd']) == TRUE ? $sortBy['paymentReportSortByDateRangeValueEnd'] == '' ? '' : $this->db->where('walletaccount.processDatetime <=',$sortBy['paymentReportSortByDateRangeValueEnd'].' 23:59:59'): '';
		// //isset($sortBy['paymentReportSortByOnly1stDeposit']) == TRUE ? $sortBy['paymentReportSortByOnly1stDeposit'] == '' ? '' : $this->db->where('walletaccount.processDatetime <=',$sortBy['paymentReportSortByOnly1stDeposit'].' 23:59:59'): '';

		// $this->db->limit($limit, $offset);
		$query = $this->db->get();
		// $this->utils->printLastSQL();
		if ($query->num_rows() > 0) {
			return $query->result_array();
			// foreach ($query->result_array() as $row) {
			// 	$row['lastLoginTime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['lastLoginTime']));
			// 	$row['createdOn'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['createdOn']));
			// 	$data[] = $row;
			// }
			// //var_dump($data);exit();
			// return $data;
		}
		return false;
	}

	/**
	 * Will change player status based on the passed parameters
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	public function populate($data, $sort_by, $in, $limit, $offset) {
		$main_search = array();
		$friend_referral = '';
		$search = array();
		$wallet = array();
		$promo = array();

		foreach ($data as $key => $value) {
			if ($key == 'playerId' && $value != '') {
				$search[$key] = "p.$key = '" . $value . "'";
			} elseif ($key == 'sign_time_period' && $value != '') {
				if ($value == 'week') {
					$search[$key] = "p.createdOn >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
				} elseif ($value == 'month') {
					$search[$key] = "p.createdOn >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
				} elseif ($value == 'past') {
					$search[$key] = "p.createdOn >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)";
				}
			} elseif ($key == 'signup_range' && $value != '') {
				$search[$key] = "p.createdOn BETWEEN $value";
			} elseif ($key == 'age_range' && $value != '') {
				$search[$key] = "YEAR(CURDATE())-YEAR(birthdate) BETWEEN $value";
			} elseif ($key == 'age_text' && $value != '') {
				$search[$key] = "YEAR(CURDATE())-YEAR(birthdate) = $value";
			} elseif ($key == 'birthdate' && $value != '') {
				$search[$key] = "pd.$key = '" . $value . "'";
			} elseif ($key == 'type' && $value != '') {
				$search[$key] = "$key = '" . $value . "'";
			} elseif ($key == 'blocked' && $value != null) {
				$search[$key] = "p.$key = '" . $value . "'";
			} elseif ($key == 'has_deposited' && $value != null) {
				$search[$key] = "wa.dwStatus = 'approved' AND wa.transactionType = 'deposit'";
			} elseif ($key == 'username' && $value != null) {
				$search[$key] = "p.username $value";
			} elseif ($key == 'tagId' && $value != null) {
				$search[$key] = "pt.$key = '" . $value . "'";
			} elseif ($key == 'status' && $value != null) {
				$search[$key] = "p.$key = '" . $value . "'";
			} elseif ($key == 'playerLevel' && $value != null) {
				$search[$key] = "vipcbr.vipsettingcashbackruleId = '" . $value . "'";
			} elseif ($key == 'gameId' && $value != null) {
				$search[$key] = "pg.$key = '" . $value . "' AND pg.blocked IN ('1', '2')";
			} elseif ($key == 'promoId' && $value != null) {
				$search[$key] = "pdp.$key = '" . $value . "' AND pdp.promoStatus = 0";
			} elseif ($key == 'referral_id' && $value != null) {
				$friend_referral = $value;
			} elseif (($key == 'qq' || $key == 'imAccount') && $value != null) {
				$search[$key] = "imAccount LIKE '%" . $value . "%' OR imAccount2 LIKE '%" . $value . "%'";
			} elseif (($key == 'less_deposit_amount' || $key == 'greater_deposit_amount' || $key == 'first_deposit_range' || $key == 'second_deposit_range' || $key == 'never_deposited') && $value != null) {
				$wallet[$key] = $value;
			} elseif ($key == 'affiliate') {
				if ($value != null) {
					$search[$key] = "pa.type = 'affiliate' AND pa.typeId = '" . $value . "'";
				} else {
					$search[$key] = "pa.type = 'wallet'";
				}
			} elseif ($key == 'registered_by' && $value != null) {
				$search[$key] = "p." . $key . " = '" . $value . "'";
			} elseif ($value != null) {
				$search[$key] = "$key LIKE '%" . $value . "%'";
			}
		}

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}
		// print_r($search);exit();
		if (!empty($wallet)) {
			$never_deposited = (isset($wallet['never_deposited'])) ? $wallet['never_deposited'] : null;
			$less_deposit_amount = (isset($wallet['less_deposit_amount'])) ? $wallet['less_deposit_amount'] : null;
			$greater_deposit_amount = (isset($wallet['greater_deposit_amount'])) ? $wallet['greater_deposit_amount'] : null;
			$first_deposit_range = (isset($wallet['first_deposit_range'])) ? $wallet['first_deposit_range'] : null;
			$second_deposit_range = (isset($wallet['second_deposit_range'])) ? $wallet['second_deposit_range'] : null;

			$query = "SELECT pa.playerAccountId, p.*, pd.*,vipcbr.vipLevel,vipcbr.vipLevelName,vipst.groupName, pa.typeOfPlayer, pa.type, pa.typeId, t.tagName, p.status
			FROM player as p
			LEFT JOIN playeraccount AS pa ON p.playerId = pa.playerId
			/*LEFT JOIN playergame AS pg ON pa.playerId = pg.playerId*/
			LEFT JOIN playerdetails AS pd ON pa.playerId = pd.playerId
			LEFT JOIN playerlevel as pl on pa.playerId = pl.playerId
			LEFT JOIN playertag AS pt ON pa.playerId = pt.playerId
			LEFT JOIN vipsettingcashbackrule as vipcbr on vipcbr.vipsettingcashbackruleId = pl.playerGroupId
			LEFT JOIN vipsetting as vipst on vipst.vipSettingId = vipcbr.vipSettingId
			LEFT JOIN tag AS t ON pt.tagId = t.tagId
			";

			if (count($search) > 0) {
				$query .= "WHERE " . implode(' AND ', $search);
			}

			//$query .= " AND pa.type = 'wallet'";

			$query .= " ORDER BY $sort_by $in $limit $offset";

			$result = $this->db->query($query);
			$check = 0;

			foreach ($result->result_array() as $rows) {
				if ($never_deposited != null) {
					$query = $this->db->query("SELECT * FROM walletaccount WHERE playerAccountId = '" . $rows['playerAccountId'] . "' AND transactionType = 'deposit' AND dwStatus = 'approved'");
					$res = $query->row_array();

					if (empty($res)) {
						array_push($main_search, $rows);
					}
				} elseif ($rows['playerAccountId'] == null) {
					continue;
				} else {
					if ($first_deposit_range != null) {
						$query = $this->db->query("SELECT amount FROM walletaccount WHERE playerAccountId = '" . $rows['playerAccountId'] . "' AND processDatetime BETWEEN " . $first_deposit_range . " AND dwCount = '1' AND transactionType = 'deposit' AND dwStatus = 'approved'");
						$res = $query->row_array();

						if (empty($res)) {
							continue;
						} else {
							if ($less_deposit_amount != null && $greater_deposit_amount != null) {
								if ($res['amount'] < $less_deposit_amount && $res['amount'] > $greater_deposit_amount && $res['amount'] != 0) {
									array_push($main_search, $rows);
									$check = 1;
								}
							} else {
								if ($less_deposit_amount != null) {
									if ($res['amount'] < $less_deposit_amount && $res['amount'] != 0) {
										array_push($main_search, $rows);
										$check = 1;
									}
								} else {
									if ($res['amount'] > $greater_deposit_amount && $res['amount'] != 0) {
										array_push($main_search, $rows);
										$check = 1;
									}
								}
							}
						}
					}

					if ($second_deposit_range != null && $check == 0) {
						$query = $this->db->query("SELECT amount FROM walletaccount WHERE playerAccountId = '" . $rows['playerAccountId'] . "' AND processDatetime BETWEEN " . $second_deposit_range . " AND dwCount = '2' AND transactionType = 'deposit' AND dwStatus = 'approved'");
						$res = $query->row_array();

						if (empty($res)) {
							continue;
						} else {
							if ($less_deposit_amount != null && $greater_deposit_amount != null) {
								if ($res['amount'] < $less_deposit_amount && $res['amount'] > $greater_deposit_amount) {
									array_push($main_search, $rows);
									$check = 1;
								}
							} else {
								if ($less_deposit_amount != null) {
									if ($res['amount'] < $less_deposit_amount) {
										array_push($main_search, $rows);
										$check = 1;
									}
								} else {
									if ($res['amount'] > $greater_deposit_amount) {
										array_push($main_search, $rows);
										$check = 1;
									}
								}
							}
						}
					}
				}
			}
		} else if (!empty($friend_referral)) {
			$queryFriendReferral = "SELECT p.*, pd.*,vipcbr.vipLevel,vipcbr.vipLevelName,vipst.groupName, pa.typeOfPlayer, pa.type, pa.typeId, t.tagName, p.status
			FROM playerfriendreferral AS pfr
			LEFT JOIN player AS p ON pfr.invitedPlayerId = p.playerId
			LEFT JOIN playeraccount AS pa ON pfr.playerId = pa.playerId
			/*LEFT JOIN playergame AS pg ON pfr.playerId = pg.playerId*/
			LEFT JOIN playerdetails AS pd ON pfr.playerId = pd.playerId
			LEFT JOIN playerlevel as pl on pfr.playerId = pl.playerId
			LEFT JOIN playertag AS pt ON pfr.playerId = pt.playerId
			LEFT JOIN vipsettingcashbackrule as vipcbr on vipcbr.vipsettingcashbackruleId = pl.playerGroupId
			LEFT JOIN vipsetting as vipst on vipst.vipSettingId = vipcbr.vipSettingId
			LEFT JOIN tag AS t ON pt.tagId = t.tagId
			";

			if (count($search) > 0) {
				$queryFriendReferral .= "WHERE " . implode(' AND ', $search);
			}

			$queryFriendReferral .= " ORDER BY $sort_by $in $limit $offset";

			$result = $this->db->query($queryFriendReferral);

			foreach ($result->result_array() as $rows) {
				array_push($main_search, $rows);
			}
		} else {
			$query = "SELECT DISTINCT p.*, pd.*,vipcbr.vipLevel,vipcbr.vipLevelName,vipst.groupName, pa.typeOfPlayer, pa.type, pa.typeId, t.tagName, p.status
			FROM player AS p
			LEFT JOIN playeraccount AS pa ON p.playerId = pa.playerId
			LEFT JOIN playergame AS pg ON p.playerId = pg.playerId
			LEFT JOIN playerdetails AS pd ON p.playerId = pd.playerId
			LEFT JOIN playerlevel as pl on p.playerId = pl.playerId
			LEFT JOIN playertag AS pt ON p.playerId = pt.playerId
			LEFT JOIN vipsettingcashbackrule as vipcbr on vipcbr.vipsettingcashbackruleId = pl.playerGroupId
			LEFT JOIN vipsetting as vipst on vipst.vipSettingId = vipcbr.vipSettingId
			LEFT JOIN tag AS t ON pt.tagId = t.tagId
			LEFT JOIN playerpromo as pdp ON p.playerId = pdp.playerId
			";

			if (count($search) > 0) {
				$query .= "WHERE " . implode(' AND ', $search);
			}

			//$query .= " AND pa.type = 'wallet'";

			$query .= " ORDER BY $sort_by $in $limit $offset";

			$query = $this->db->query($query);

			foreach ($query->result_array() as $rows) {
				array_push($main_search, $rows);
			}
		}

		/*echo "<pre>";
			print_r($main_search);
			echo "</pre>";
			exit();
		*/
		if (!$main_search) {
			return false;
		} else {
			return $main_search;
		}
	}

	/**
	 * get all affiliates
	 *
	 * @return 	array
	 */
	public function getAllAffiliates() {
		$query = $this->db->query("SELECT * from affiliates where status IN ('0', '2') ORDER BY affiliateId ASC");

		return $query->result_array();
	}

	/**
	 * getPlayerTotalDeposits
	 *
	 * @return 	array
	 */
	public function getPlayerTotalDeposits($player_account_id) {
		$this->load->model(array('sale_order'));
		$sql = "SELECT SUM(amount) as totalDeposit, COUNT(*) as totalNumberOfDeposit FROM sale_orders WHERE wallet_id = ? AND status = ?";
		$query = $this->db->query($sql, array($player_account_id, Sale_order::STATUS_SETTLED));
		return $query->row_array();
	}

	/**
	 * getPlayerTotalWithdrawal
	 *
	 * @return 	array
	 */
	public function getPlayerTotalWithdrawal($player_account_id) {
		$sql = "SELECT SUM(amount) as totalWithdrawal, COUNT(*) as totalNumberOfWithdrawal FROM walletaccount WHERE playerAccountId = ? AND transactionType = ?  AND dwStatus = ? ";

		$query = $this->db->query($sql, array($player_account_id, 'withdrawal', 'approved'));

		return $query->row_array();
	}

	/**
	 * getAllGames
	 *
	 * @return 	array
	 */
	public function getAllGames() {
		$query = $this->db->query("SELECT * FROM game");
		return $query->result_array();
	}

	/**
	 * get all affiliates
	 *
	 * @return 	array
	 */
	public function getAllPlayersWithReferral($limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$query = $this->db->query("SELECT DISTINCT p.* FROM player AS p
			INNER JOIN playerfriendreferral AS pfr ON p.playerId = pfr.playerId
			INNER JOIN playeraccount as pa on p.playerId = pa.playerId where pa.type = 'wallet' $limit $offset");

		return $query->result_array();
	}

	/**
	 * get all affiliates
	 *
	 * @return 	array
	 */
	// public function getRankingSettings() {
	// 	$query = $this->db->query("SELECT rankinglevelsetting.*, CONCAT(rankinglevelsetting.rankingLevelGroup,rankinglevelsetting.rankingLevel) AS rankinglevelname,adminusers.username AS setByName
	// 					   			FROM rankinglevelsetting
	// 					   			INNER JOIN adminusers ON adminusers.`userId` = rankinglevelsetting.`setBy`
	// 					  			WHERE rankinglevelsetting.`status` = 0");
	// 	return $query->result_array();
	// }

	/**
	 * get all affiliates
	 *
	 * @return 	array
	 */
	// public function getRankingGroupOfPlayer($player_level) {
	// 	$query = $this->db->query("SELECT rankingLevelGroup
	// 					   			FROM rankinglevelsetting
	// 					   			INNER JOIN adminusers ON adminusers.`userId` = rankinglevelsetting.`setBy`
	// 					  			WHERE rankinglevelsetting.`status` = 0
	// 					  			AND rankinglevelsetting.rankingLevelSettingId = '". $player_level ."'");
	// 	return $query->row_array();
	// }

	/**
	 * get all affiliates
	 *
	 * @return 	array
	 */
	public function getFriendReferralSettings() {
		$query = $this->db->query("SELECT *
			FROM friendreferralsettings
			WHERE status = 0");
		return $query->row_array();
	}

	/**
	 * get all affiliates
	 *
	 * @return 	array
	 */
	public function createFriendReferralSettings($data) {
		$this->db->insert('friendreferralsettings', $data);
	}

	/**
	 * get all affiliates
	 *
	 * @return 	array
	 */
	public function saveFriendReferralSettings($data, $friend_referral_settings_id) {
		$this->db->where('friendReferralSettingsId', $friend_referral_settings_id);
		$this->db->update('friendreferralsettings', $data);
	}

	/**
	 * get all games
	 *
	 * @return 	array
	 */
	public function getGames() {
		$query = $this->db->query("SELECT * FROM game");

		return $query->result_array();
	}

	/**
	 * get userId if its using or child of roleId
	 *
	 * @param	int
	 * @return 	array
	 */
	public function getTagDetails($tag_id) {
		$this->db->select('*')->from('tag');
        if(is_array($tag_id)){
            $this->db->where_in('tagId', $tag_id);
        }else{
            $this->db->where('tagId', $tag_id);
        }

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

	public function editTag($data, $tag_id) {
		$this->db->where('tagId', $tag_id);
		$this->db->update('tag', $data);
	}

	public function activateVIPGroup($data) {
		$this->db->where('vipsettingId', $data['vipsettingId']);
		$this->db->update('vipsetting', $data);
	}

	public function editVIPGroup($data, $vipsettingId) {
		$this->db->where('vipsettingId', $vipsettingId);
		$this->db->update('vipsetting', $data);
	}

	public function deletePlayerTag($tag_id) {
		$this->db->where('tagId', $tag_id);
		$this->db->delete('playertag');
	}

	public function deletePlayerTagByPlayerId($player_id) {
		$this->db->where('playerId', $player_id);
		$this->db->delete('playertag');
	}

	public function deleteVIPGroup($vipsettingId) {
		$this->db->where('vipsettingId', $vipsettingId);
		$this->db->delete('vipsetting');
	}

	public function deleteTag($tag_id) {
		$this->db->where('tagId', $tag_id);
		$this->db->delete('tag');
	}

	public function deleteVIPGroupItem($vipsettingId) {
		$this->db->where('vipsettingId', $vipsettingId);
		$this->db->delete('vipsetting');
	}

	/**
	 * get all games
	 *
	 * @return 	array
	 */
	public function getPlayerGameProfile($player_id) {
		$sql = "SELECT * FROM playergameprofile where playerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		return $query->result_array();
	}

	/**
	 * Will change player status based on the passed parameters
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	public function changePlayerGameBlocked($player_id, $game, $data) {
		$where = "playerId = '" . $player_id . "' AND gameId = '" . $game . "'";
		$this->db->where($where);
		$this->db->update('playergame', $data);
	}

	/**
	 * get userId if its using or child of roleId
	 *
	 * @param	int
	 * @return 	array
	 */
	public function getTagDescription($tag_id) {
		$this->db->select('tagDescription')->from('tag');
		$this->db->where('tagId', $tag_id);

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
	 * Will get all tags
	 *
	 * @return 	array
	 */
	public function getTags($sort, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$query = $this->db->query("SELECT * FROM tag as t left join adminusers as au on t.createBy = au.userId ORDER BY $sort $limit $offset");

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * Will get all tags
	 *
	 * @return 	array
	 */
	public function getSearchTag($search, $limit, $offset) {

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$sql = "SELECT * FROM tag as t left join adminusers as au on t.createBy = au.userId where tagName = ? $limit $offset";

		$query = $this->db->query($sql, array($search));

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	public function getPlayerGame($player_id) {
		$sql = "SELECT * FROM playergame as pg inner join game as g on pg.gameId = g.gameId where playerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * get all affiliates
	 *
	 * @return 	array
	 */
	public function getAllReferredPlayers($sort, $limit, $offset) {
		$sortby = null;
		$in = null;

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		if (!empty($sort)) {
			$sortby = 'ORDER BY ' . $sort['sort'];
			$in = $sort['in'];
		}

		$query = $this->db->query("SELECT pfr.*, p.*, pb.username as inviter, pb.invitationCode as inviterCode
			FROM player AS p
			INNER JOIN playerfriendreferral AS pfr ON p.playerId = pfr.invitedPlayerId
			LEFT OUTER JOIN player AS pb
			ON pfr.playerId = pb.playerId
			INNER JOIN playeraccount as pa ON p.playerId = pa.playerId
			WHERE pa.type = 'wallet'
			$sortby $in $limit $offset");

		return $query->result_array();
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

		$query = $this->db->query("SELECT vipsetting.*, adminusers.username AS createdBy
			FROM vipsetting
			LEFT JOIN adminusers
			ON adminusers.userId = vipsetting.createdBy
			ORDER BY vipsetting." . $sort . " ASC
			$limit
			$offset
			");

		/*$this->db->select('vipsetting.*,
			adminusers.username AS createdBy
			')
			->from('vipsetting')
			->join('adminusers', 'adminusers.userId = vipsetting.createdBy','left')
			->order_by('vipsetting.'.$sort,'asc');

			$this->db->limit($limit, $offset);

		*/

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['createdOn'] = mdate('%M %d, %Y - %h:%i:%s %A', strtotime($row['createdOn']));
				$data[] = $row;
			}
			//var_dump($data);exit();
			/*echo "<pre>";
			print_r($data);
			echo "</pre>";
			exit();*/
			return $data;
		}
		return false;

	}

	/**
	 * Get VIP setting List
	 *
	 * @return	$array
	 */
	public function getVIPSettingListToExport($sort, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$query = $this->db->query("SELECT vipsetting.*, adminusers.username AS createdBy
			FROM vipsetting
			LEFT JOIN adminusers
			ON adminusers.userId = vipsetting.createdBy
			ORDER BY vipsetting." . $sort . " ASC
			$limit
			$offset
			");

		return $query;
	}

	public function getGameById($game_id) {
		$sql = "SELECT * FROM game where gameId = ? ";

		$query = $this->db->query($sql, array($game_id));

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	public function getPlayerGameByGameId($player_id, $game_id) {
		$sql = "SELECT * FROM playergame as pg inner join game as g on pg.gameId = g.gameId where g.gameId = ? AND pg.playerId = ? ";

		$query = $this->db->query($sql, array($game_id, $player_id));

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	public function getPlayerBlockedGames($player_id) {
		$sql = "SELECT * FROM playergame as pg inner join game as g on pg.gameId = g.gameId where pg.playerId = ? AND blocked IN ('1, 2,6,7') ";

		$query = $this->db->query($sql, array($player_id));

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * Will get player level of user
	 *
	 * @return  array
	 */
	// public function getPlayerLevels() {
	// 	$normal = $this->db->query("SELECT rankingLevelSettingId, MAX(rankingLevel) AS maxLevel  FROM rankinglevelsetting WHERE rankingLevelGroup = 'Normal' ORDER BY rankingLevel ASC");
	// 	$normal = $normal->row_array();

	// 	$vip = $this->db->query("SELECT rankingLevelSettingId, MAX(rankingLevel) AS maxLevel FROM rankinglevelsetting WHERE rankingLevelGroup = 'VIP' ORDER BY rankingLevel ASC");
	// 	$vip = $vip->row_array();

	// 	$result = array(
	// 			'normal' => $normal['rankingLevelSettingId'],
	// 			'vip' => $vip['rankingLevelSettingId'],
	// 			'normalMaxLevel' => $normal['maxLevel'],
	// 			'vipMaxLevel' => $vip['maxLevel']
	// 		);
	// 	return $result;
	// }

	public function getMainWallet($player_id) {
		$sql = "SELECT * FROM playeraccount where playerId = ? AND type = ? ";

		$query = $this->db->query($sql, array($player_id, 'wallet'));

		return $result = $query->row_array();
	}

	/**
	 * get email in email table
	 *
	 * @return	array
	 */
	// public function isReferredBy($player_id) {
	// 	$sql = "SELECT pfr.*, pfrd.* FROM playerfriendreferral as pfr INNER JOIN playerfriendreferraldetails as pfrd on pfr.referralId = pfrd.referralId where invitedPlayerId = ? AND pfrd.status = ? ";

	// 	$query = $this->db->query($sql, array($player_id, '0'));

	// 	return $query->row_array();
	// }

	/**
	 * get email in email table
	 *
	 * @return	array
	 */
	public function getSelectedPlayers($player_ids) {
		$sql = "SELECT p.*, CONCAT(vs.groupName, ' ', vscr.vipLevel) AS level
		FROM player AS p
		LEFT JOIN playerlevel as pl
		ON p.playerId = pl.playerId
		LEFT JOIN vipsettingcashbackrule as vscr
		ON pl.playerGroupId = vscr.vipsettingcashbackruleId
		LEFT JOIN vipsetting as vs
		ON vs.vipSettingId = vscr.vipSettingId
		WHERE p.playerId IN ($player_ids)";

		$query = $this->db->query($sql);

		return $query->result_array();
	}

	/**
	 * get player affiliate
	 *
	 * @param  int $player_id The player.playerId
	 * @return null|string The username of affiliates table or null for not found.
	 */
	public function getAffiliateOfPlayer($player_id) {
		$sql = "SELECT affiliates.username FROM affiliates join player
		ON affiliates.affiliateId = player.affiliateId
		WHERE player.playerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		$result = $query->row_array();

		/// Patch for OGP-14868 Affiliate doesn't display on the player information page
		$query->free_result(); // free $query

		if (empty($result)) {
			return null;
		}

		return $result['username'];

	} // EOF getAffiliateOfPlayer

	/**
	 *  overview : get all players under given agent
	 *
	 *  @param  int $agent_id
	 *  @return array
	 */
	public function get_players_by_agent_id($agent_id) {
		$sql = "SELECT * FROM player WHERE agent_id = ?";
		return $this->db->query($sql, array($agent_id))->result_array();
	}

	// get_player_ids_by_agent_ids {{{2
	/**
	 *  return all players under given agents
	 *
	 *  @param  array/int agent_id(s)
	 *  @return array all records of players
	 */
	public function get_player_ids_by_agent_ids($agent_ids) {
		$this->db->select('playerId');

		if (is_array($agent_ids)) {
			$this->db->where_in('agent_id', $agent_ids);
		} else {
			$this->db->where('agent_id', $agent_ids);
		}

		$result = $this->db->get('player');

		$rows = $this->getMultipleRowArray($result);

		$ids = array();
		if (!empty($rows)) {
			foreach ($rows as $rec) {
				$ids[] = $rec['playerId'];
			}
		}

		return $ids;
	} // get_player_ids_by_agent_ids  }}}2

    public function get_players_by_agent_ids($agent_ids) {
        $this->db->select('playerId, rolling_comm');

        if (is_array($agent_ids)) {
            $this->db->where_in('agent_id', $agent_ids);
        } else {
            $this->db->where('agent_id', $agent_ids);
        }

        $result = $this->db->get('player');

        $rows = $this->getMultipleRowArray($result);

        return $rows;
    }

	/**
	 * get player parent agent name
	 *
	 * @param int $player_id The player.playerId
	 * @param array|string $selectFields Default '*',or array for key-value, fieldname-fieldvalue
	 * @return array|boolean $row The row, key-value:fieldname-value. no data is false.
	 */
	public function getAgentOfPlayer($player_id, $selectFields = null) {
		if( empty($selectFields) ){ // for default
			$selectFields['agency_agents.agent_name'] = 'agent_name';
		}

		$selectFieldArray = [];
		foreach($selectFields as $fieldname => $fieldAlias){
			$selectFieldArray[] = $fieldname. ' as '. $fieldAlias;
		}
		$selectFieldStr = implode(' , ', $selectFieldArray);

		$sql = "SELECT $selectFieldStr FROM agency_agents join player
		ON agency_agents.agent_id = player.agent_id
		WHERE player.playerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		$result = $query->row_array();


		$query->free_result(); // $query 物件將不再使用了

		if (empty($result)) {
			return null;
		}

		if( count($selectFields) > 1 ){
			return $result;
		}else{
			$onlyOne = ''; // get the sepc. field name.
			foreach($selectFields as $key => $value){
				$onlyOne = $value;
			}

			$this->utils->debug_log('onlyOne:::',$onlyOne);
			return $result[$onlyOne];
		}

	} // EOF getAgentOfPlayer

	/**
	 * get player parent agent name
	 *
	 * @param  int
	 * @return string
	 */
	public function getDispatchAccountOfPlayer($player_id) {
		$sql = "SELECT dispatch_account_level.level_name, dispatch_account_group.group_name
		FROM dispatch_account_level
		join player	ON dispatch_account_level.id = player.dispatch_account_level_id
		join dispatch_account_group ON dispatch_account_level.group_id = dispatch_account_group.id
		WHERE player.playerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		$result = $query->row_array();

		if (empty($result)) {
			return null;
		}

		return $result['group_name'] . '-'. $result['level_name'];
	}

	/**
	 * get Total Bonus
	 *
	 * @param  int
	 * @return double
	 */
	public function getTotalBonus($player_id) {
		$sql = "SELECT SUM(bonusAmount) as total_bonus FROM playerpromo
		WHERE playerId = ?
		AND transactionStatus IN ('1, 2, 3') ";

		$query = $this->db->query($sql, array($player_id));

		$result = $query->row_array();

		if (empty($result)) {
			return 0;
		}

		return $result['total_bonus'];
	}

	/**
	 * get Total Cashback Bonus
	 *
	 * @param  int
	 * @return double
	 */
	public function getTotalCashbackBonus($player_id) {
		$sql = "SELECT SUM(amount) as total_bonus FROM playercashback WHERE playerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		$result = $query->row_array();

		if (empty($result)) {
			return 0;
		}

		return $result['total_bonus'];
	}

	/**
	 * get Total Referral Bonus
	 *
	 * @param  int
	 * @return double
	 */
	public function getTotalReferralBonus($player_id) {
		# CAUTION!
		# `playerfriendreferraldetails` table is no longer used, this is definatly need to fix.
		$sql = "SELECT SUM(pfrd.amount) as total_bonus FROM playerfriendreferraldetails as pfrd
		LEFT JOIN playerfriendreferral as pfr
		ON pfrd.referralId = pfr.referralId
		WHERE pfrd.status = 1 AND pfr.playerId = ?";

		$query = $this->db->query($sql, array($player_id));

		$result = $query->row_array();

		if (empty($result)) {
			return 0;
		}

		return $result['total_bonus'];
	}

	/**
	 * get Game Provider
	 *
	 * @param  int
	 * @return string
	 */
	public function getGameProvider($player_id) {
		$sql = "SELECT g.gameId, g.game FROM player as p
		LEFT JOIN playerlevel as pl
		ON p.playerId = pl.playerId
		LEFT JOIN vipsettingcashbackrule as vscr
		ON pl.playerGroupId = vscr.vipsettingcashbackruleId
		LEFT JOIN vipsettingcashbackbonuspergame as vscbpg
		ON vscr.vipsettingcashbackruleId = vscbpg.vipsettingcashbackruleId
		LEFT JOIN game as g
		ON vscbpg.gameType = g.gameId
		WHERE p.playerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		$result = $query->result_array();

		if (empty($result)) {
			return null;
		}

		return $result;
	}

	/**
	 * get API Details
	 *
	 * @param  int
	 * @return array
	 */
	public function getAPIDetails($player_id) {
		$sql1 = "SELECT username FROM player WHERE playerId = ? ";

		$query1 = $this->db->query($sql1, array($player_id));

		$res = $query1->row_array();
		$username = $res['username'];

		$sql = "SELECT gar.*, g.game FROM gameapirecord as gar
		LEFT JOIN game as g
		ON gar.apitype = g.gameId
		WHERE playerName = ? ";

		$query = $this->db->query($sql, array($username));

		return $query->result_array();
	}

	/**
	 * get all deposit promo
	 *
	 * @param  int
	 * @return array
	 */
	public function getAllPromo() {
		$query = $this->db->query("SELECT promorules.*,admin1.username AS createdBy, admin2.username AS updatedBy
			FROM promorules
			LEFT JOIN adminusers AS admin1
			ON admin1.userId = promorules.createdBy
			LEFT JOIN adminusers AS admin2
			ON admin2.userId = promorules.updatedBy
			");

		$cnt = 0;
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['createdOn'] = mdate('%M %d, %Y - %h:%i:%s %A', strtotime($row['createdOn']));
				if ($row['updatedOn'] != null) {
					$row['updatedOn'] = mdate('%M %d, %Y - %h:%i:%s %A', strtotime($row['updatedOn']));
				}
				$data[] = $row;
				$data[$cnt]['promorulesallowedplayerlevel'] = $this->getDepositPromoPlayerLevelLimit($row['promorulesId']);
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
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = promorulesallowedplayerlevel.playerLevel', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$this->db->where('promorulesallowedplayerlevel.promoruleId', $depositPromoId);

		$query = $this->db->get();

		return $query->result_array();
	}

	/*
		 * Will get getCashbackHistory
		 *
		 * @param 	$walletId int
		 * @return	$array
	*/
	public function getCashbackHistory($playerId, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$sql = "SELECT * FROM playercashback WHERE playerId = ? $limit $offset";

		$query = $this->db->query($sql, array($playerId));

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['receivedOn'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['receivedOn']));
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return FALSE;
	}

	/**
	 * get adjust balance history
	 *
	 * @return	array
	 */
	public function getBalanceAdjustment($player_id, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$sql = "SELECT bah.*, g.game as wallet, au.username as adjust FROM balanceadjustmenthistory as bah
		LEFT JOIN game as g ON bah.walletType = g.gameId
		LEFT JOIN adminusers as au ON bah.adjustedBy = au.userId
		WHERE playerId = ?
		$limit $offset";

		$query = $this->db->query($sql, array($player_id));

		return $query->result_array();
	}

	/**
	 * get players and game data
	 *
	 * @return	array
	 */
	public function getPlayers() {
		$query = $this->db->query("SELECT playerId, username FROM player");

		return $query->result_array();
	}

	/**
	 * get API Details
	 *
	 * @param  int
	 * @return array
	 */
	public function getAPIDetailsPerGame($player_id, $game_id) {
		$sql1 = "SELECT username FROM player WHERE playerId = ? ";

		$query1 = $this->db->query($sql1, array($player_id));

		$res = $query1->row_array();
		$username = $res['username'];

		$sql = "SELECT gar.*, g.game FROM gameapirecord as gar
		LEFT JOIN game as g
		ON gar.apitype = g.gameId
		WHERE playerName = ? AND game_type = ? ";

		$query = $this->db->query($sql, array($username, $game_id));

		return $query->row_array();
	}

	/**
	 * get all player friend referral
	 *
	 * @return	array
	 */
	public function getAllPlayerFriendReferral() {
		$query = $this->db->query("SELECT * FROM playerfriendreferral");

		return $query->result_array();
	}

	/**
	 * get all player friend referral
	 *
	 * @return	array
	 */
	public function getAllPlayerByUsername() {
		$query = $this->db->query("SELECT username, playerId FROM player");

		return $query->result_array();
	}

	/**
	 * get all player bets, deposits
	 *
	 * @return	array
	 */
	public function getPlayerBetsDeposits($player_id) {
		$sql = "SELECT p.playerId,
		(SELECT SUM(gar.bets) FROM gameapirecord as gar WHERE gar.playerName = p.username AND gar.apitype = '1') + (SELECT SUM(gar.betAmount) FROM gameapirecord as gar WHERE gar.playerName = p.username AND gar.apitype = '2' AND dataType IN ('BR', 'EBR')) as bets,
		(SELECT SUM(amount) FROM walletaccount as wa LEFT JOIN playeraccount as pa ON wa.playerAccountId = pa.playerAccountId WHERE wa.transactionType = 'deposit' AND wa.dwStatus = 'approved' AND pa.playerId = p.playerId) as deposits
		FROM player as p
		WHERE p.playerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		return $query->row_array();
	}

	/**
	 * get all player pt bets
	 *
	 * @return	array
	 */
	public function getPlayerPTBets($playerName, $dateJoined) {
		$this->db->select('SUM(gameapirecord.bets) as ptTotalBets
			')
			->from('player')
			->join('gameapirecord', 'gameapirecord.playerName = player.username', 'left');
		$this->db->where('gameapirecord.playerName', $playerName);
		$this->db->where('gameapirecord.betTime >= ', $dateJoined);
		$this->db->where('gameapirecord.betTime <= ', date('Y-m-d H:i:s'));
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
	 * get all player pt bets
	 *
	 * @return	array
	 */
	public function getPlayerAGBets($playerName, $dateJoined) {
		$this->db->select('SUM(gameapirecord.betAmount) as agTotalBets
			')
			->from('player')
			->join('gameapirecord', 'gameapirecord.playerName = player.username', 'left');
		$this->db->where('gameapirecord.playerName', $playerName);
		$this->db->where('gameapirecord.betTime >= ', $dateJoined);
		$this->db->where('gameapirecord.betTime <= ', date('Y-m-d H:i:s'));
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
	 * Will create player referral details based on the passed parameters
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	// public function createPlayerReferralDetails($data) {
	// 	$this->db->insert('playerfriendreferraldetails', $data);
	// }

	/**
	 * Will get all player deposit promo
	 *
	 * @param   array
	 */
	public function getPlayerDepositPromo() {
		//$query = $this->db->query("SELECT * FROM playerdepositpromo WHERE withdrawalStatus = '0' AND promoStatus = '0'");
		$this->db->select('playerpromo.*, player.username
			')
			->from('playerpromo')
			->join('player', 'player.playerId = playerpromo.playerId', 'left');
		$this->db->where('playerpromo.withdrawalStatus', 0);
		$this->db->where('playerpromo.promoStatus', 0);
		$this->db->where('playerpromo.transactionStatus', 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * Will get player deposit promo by player id
	 *
	 * @param   de
	 */
	public function getPlayerDepositPromoById($player_id, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$sql = "SELECT pp.*, pr.promoName FROM playerpromo as pp
		LEFT JOIN promorules as pr
		ON pp.promorulesId = pr.promorulesId
		WHERE pp.playerId = ?
		$limit
		$offset ";

		$query = $this->db->query($sql, array($player_id));

		return $query->result_array();
	}

	/**
	 * update player deposit promo
	 *
	 * @param   array
	 */
	public function updatePlayerDepositPromo($data, $player_deposit_promo_id) {
		$this->db->where('playerpromoId', $player_deposit_promo_id);
		$this->db->update('playerpromo', $data);
	}

	/**
	 * Will get player deposit by player id
	 *
	 * @param   array
	 */
	public function getPlayerDepositHistory($player_id, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$sql = "SELECT wa.* FROM walletaccount as wa
		LEFT JOIN playeraccount as pa
		ON wa.playerAccountId = pa.playerAccountId
		WHERE pa.playerId = ?
		AND transactionType = ?
		$limit
		$offset ";

		$query = $this->db->query($sql, array($player_id, 'deposit'));

		return $query->result_array();
	}

	/**
	 * Will get player withdrawal by player id
	 *
	 * @param   array
	 */
	public function getPlayerWithdrawalHistory($player_id, $limit, $offset, $date_range=null) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		if($date_range !=null) {
			$range = " AND dwDateTime BETWEEN NOW() - INTERVAL $date_range DAY AND NOW() ";
		} else {
			$range = ' ';
		}

		$sql = "SELECT wa.* FROM walletaccount as wa
		LEFT JOIN playeraccount as pa
		ON wa.playerAccountId = pa.playerAccountId
		WHERE pa.playerId = ?
		AND transactionType = ?
		$range
		$limit
		$offset ";

		$query = $this->db->query($sql, array($player_id, 'withdrawal'));

		return $query->result_array();
	}

	/**
	 * create player game
	 *
	 * @param   array
	 */
	public function createPlayerGame($data) {
		$this->db->insert('playergame', $data);
	}

	/**
	 * get AG Record by player name
	 *
	 * @param   array
	 */
	public function getAllAGRecordByPlayerName($player_name) {
		$sql = "SELECT * FROM gameapirecord WHERE playerName = ? AND dataType IN ('BR', 'EBR') AND apitype = '2' ";

		$query = $this->db->query($sql, array($player_name));

		return $query->result_array();
	}

	/**
	 * get AG Record by player name
	 *
	 * @param   array
	 */
	public function getAllAGGameRecordByPlayerName($player_name) {
		$sql = "SELECT * FROM gameapirecord WHERE playerName = ? AND dataType IN ('BR', 'EBR') ";

		$query = $this->db->query($sql, array($player_name));

		return $query->result_array();
	}

	/**
	 * get lock players
	 *
	 * @param   array
	 */
	public function getLockPlayers() {
		$query = $this->db->query("SELECT * FROM player WHERE status IN ('1')");

		return $query->result_array();
	}

	/**
	 * get block players
	 *
	 * @param   array
	 */
	public function getBlockPlayers() {
		$query = $this->db->query("SELECT * FROM playergame WHERE blocked IN ('1')");

		return $query->result_array();
	}

	/**
	 * edit player game
	 *
	 * @param   array
	 */
	public function editPlayerGame($data, $player_game_id) {
		$this->db->where('playerGameId', $player_game_id);
		$this->db->update('playergame', $data);
	}

	/**
	 * get promo join that is not yet expired
	 *
	 * @param   array
	 */
	public function getPromoJoin() {
		$query = $this->db->query("SELECT * FROM playerpromo WHERE promoStatus IN ('0')");

		return $query->result_array();
	}

	/**
	 * edit player deposit promo
	 *
	 * @param   array
	 */
	public function editPlayerDepositPromo($data, $player_deposit_promo_id) {
		$this->db->where('playerpromoId', $player_deposit_promo_id);
		$this->db->update('playerpromo', $data);
	}

	/**
	 * get promo that is not yet expired
	 *
	 * @param   array
	 */
	public function getPromo() {
		$query = $this->db->query("SELECT * FROM promorules WHERE status IN ('0')");

		return $query->result_array();
	}

	/**
	 * edit deposit promo
	 *
	 * @param   array
	 */
	public function editDepositPromo($data, $deposit_promo_id) {
		$this->db->where('promorulesId', $deposit_promo_id);
		$this->db->update('promorules', $data);
	}

	/**
	 * get pt bets by date
	 *
	 * @return	array
	 */
	public function getPTBets($player_name, $start_date, $end_date) {
		$sql = "SELECT player_name, bets as total_bets FROM gameapirecord
		WHERE betTime >= ? AND betTime <= ?
		AND playerName = ? AND apitype = ? ";

		$query = $this->db->query($sql, array($start_date, $end_date, $player_name, '1'));

		return $query->row_array();
	}

	/**
	 * get ag bets by date
	 *
	 * @return	array
	 */
	public function getAGBets($username, $start_date, $end_date) {
		$sql = "SELECT SUM(betAmount) as total_bets FROM gameapirecord
		WHERE betTime >= ? AND betTime <= ?
		AND playerName = ? AND dataType IN ('EBR', 'BR')
		AND apitype = '2' ";

		$query = $this->db->query($sql, array($start_date, $end_date, $player_name));

		$result = $query->row_array();

		if (empty($result['total_bets'])) {
			return null;
		} else {
			return $result;
		}
	}

	/**
	 * Will get code of the player who referrer the current player.
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getReferrerId($player_id) {
		$sql = "SELECT playerId FROM playerfriendreferral
		WHERE invitedPlayerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		$result = $query->row_array();

		if (empty($result)) {
			return 0;
		} else {
			return $result['playerId'];
		}
	}

	/**
	 * Will get code of the player who has referral code in registration.
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getRefereePlayerId($player_id) {
		$sql = "SELECT * FROM player
		WHERE playerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		$result = $query->row_array();

		if (empty($result)) {
			return 0;
		} else {
			return $result['refereePlayerId'];
		}
	}

	/**
	 * update playerbankdetails
	 *
	 * @param 	int
	 * @return	array
	 */
	public function updatePlayerBankDetails($bank_details, $bank_details_id) {
		$this->db->where('playerBankDetailsId', $bank_details_id);
		$this->db->update('playerbankdetails', $bank_details);
	}

	/**
	 * delete bank info
	 *
	 * @return	string
	 */
	const STATUS_DELETED = 2;
	public function deletePlayerBankInfo($bank_details_id) {
		$this->db->where('playerBankDetailsId', $bank_details_id);
		//$this->db->delete('playerbankdetails');

		$this->db->update('playerbankdetails', array('status' => self::STATUS_DELETED));
	}

	/**
	 * Will get player bank details given the bankDetailsId
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerBankDetails($bank_details_id) {
		$sql = "SELECT pbd.*, bt.bankName from playerbankdetails as pbd
		LEFT JOIN banktype as bt
		ON pbd.bankTypeId = bt.bankTypeId
		WHERE playerBankDetailsId = ? ";

		$query = $this->db->query($sql, array($bank_details_id));

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	/**
	 * Will get bank in banktype
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getBankType() {
		$query = $this->db->query("SELECT * FROM banktype");

		return $query->result_array();
	}

	/**
	 * Will add bank info in playerbankdetails
	 *
	 * @param 	int
	 * @return	array
	 */
	public function addPlayerBankDetails($data) {
		$this->db->insert('playerbankdetails', $data);
	}

	/**
	 * Will get player mainwallet balance
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getMainWalletBalance($player_id) {
		$sql = "SELECT SUM(totalBalanceAmount) AS totalBalanceAmount FROM playeraccount WHERE TYPE IN ('wallet') AND playerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	/**
	 * Will get player subwallet balance
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getSubWalletBalance($player_id) {
		$sql = "SELECT totalBalanceAmount AS totalBalanceAmount,typeId FROM playeraccount WHERE TYPE IN ('subwallet') AND playerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
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
	 * get email in email table
	 *
	 * @return	array
	 */
	public function getEmail() {
		$query = $this->db->query("SELECT * FROM email");

		return $query->row_array();
	}

	/* Online Players */

	/**
	 * get online players in website
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getOnlinePlayers() {
		$query = $this->db->query("SELECT p.*, pd.firstname, pd.lastname, CONCAT(vipst.groupName, ' ', vipcbr.vipLevel) as level
			FROM player as p
			LEFT JOIN playerdetails as pd ON p.playerId = pd.playerId
			LEFT JOIN playerlevel as pl ON pl.playerId = p.playerId
			LEFT JOIN vipsettingcashbackrule as vipcbr ON vipcbr.vipsettingcashbackruleId = pl.playerGroupId
			LEFT JOIN vipsetting as vipst ON vipst.vipSettingId = vipcbr.vipSettingId
			WHERE online = '0'
			");

		return $query->result_array();
	}

	/**
	 * get API online players in website
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getAPIOnlinePlayers($api_result) {
		$result = array();

		if (is_array($api_result)) {
			foreach ($api_result as $key => $value) {
				$sql = "SELECT p.*, pd.firstname, pd.lastname, CONCAT(vipst.groupName, ' ', vipcbr.vipLevel) as level
				FROM player as p
				LEFT JOIN playerdetails as pd ON p.playerId = pd.playerId
				LEFT JOIN playerlevel as pl ON pl.playerId = p.playerId
				LEFT JOIN vipsettingcashbackrule as vipcbr ON vipcbr.vipsettingcashbackruleId = pl.playerGroupId
				LEFT JOIN vipsetting as vipst ON vipst.vipSettingId = vipcbr.vipSettingId
				WHERE p.username = ? ";

				$query = $this->db->query($sql, array($value['PLAYERNAME']));

				$res = $query->row_array();
				if (isset($value['LASTLOGINDATE'])) {
					$res['lastLoginTime'] = $value['LASTLOGINDATE'];
				}

				array_push($result, $res);
			}
		}

		return $result;
	}

	/**
	 * get player by username
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerByUsername($player_name) {
		$sql = "SELECT * FROM player WHERE username = ? ";

		$query = $this->db->query($sql, array($player_name));

		return $query->row_array();
	}

	/**
	 * get player's email verification
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerVerifiedEmail($playerId) {
		$sql = "SELECT * FROM playerpromo
		WHERE playerId = ?
		AND verificationStatus = 0 ";

		$query = $this->db->query($sql, array($playerId));

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	/**
	 * update player's email verification status
	 *
	 * @param 	int
	 * @return	array
	 */
	public function editVerificationStatus($playerId, $data) {
		$this->db->where('playerId', $playerId);
		$this->db->update('playerpromo', $data);
	}

	/**
	 * gets all game log data
	 *
	 * @return 	array
	 */
	public function getPlayerAllGameLogData() {
		$query = $this->db->query("SELECT game_logs.id, game.game, game_description.game_name, game_type.game_type_lang, game_logs.bet_amount, game_logs.result_amount, game_logs.end_at, game_logs.after_balance, game_logs.player_username
			FROM game_logs
			INNER JOIN game
			ON game_logs.game_platform_id=game.gameid
			INNER JOIN game_type
			ON game_logs.game_type_id=game_type.id
			INNER JOIN game_description
			ON game_logs.game_description_id=game_description.id
			ORDER BY id DESC
			LIMIT 100"); // change to 100

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * gets specific game log data based on date
	 * moved to game_logs.php
	 * @param 	string
	 * @return 	array
	 */
	public function getSpecificGameLogData($username = null, $game = null, $start_date = null, $end_date = null, $game_code = null) {

		$this->db->select('game_logs.id, game.game, game_description.game_name, game_type.game_type_lang, game_logs.bet_amount, game_logs.result_amount, game_logs.end_at, game_logs.after_balance, game_logs.player_username');
		$this->db->from('game_logs');
		$this->db->join('game', 'game_logs.game_platform_id=game.gameid');
		$this->db->join('game_type', 'game_logs.game_type_id=game_type.id');
		$this->db->join('game_description', 'game_logs.game_description_id=game_description.id');
		$this->db->order_by('end_at', 'DESC');

		if ($start_date) {
			$this->db->where('end_at >=', $start_date);
		}

		if ($end_date) {
			$this->db->where('end_at <=', $end_date);
		}

		if ($username) {
			$this->db->where('player_username', $username);
		}

		if ($game) {
			$this->db->where('game_logs.game_platform_id', $game);
		}

		if ($game_code) {
			$this->db->where('game_logs.game_code', $game_code);
		}

		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * gets all game log data and compute total betting amount of player
	 *
	 * @return 	array
	 */
	public function getPlayerGameLog($currentMOnth, $nextMonth, $currentYear, $nextYear) {
		$sql = "SELECT player_id, player_username, SUM(bet_amount) AS 'total_betting_amount' FROM game_logs WHERE end_at >= ? AND end_at <= ? GROUP BY player_id ORDER BY player_id";

		if (!empty($nextYear)) {
			$query = $this->db->query($sql, array($currentYear . "-" . $currentMOnth . "-01 00:00:00'", $nextYear . "-" . $nextMonth . "-01 00:00:00'"));
		} else {
			$query = $this->db->query($sql, array($currentYear . "-" . $currentMOnth . "-01 00:00:00'", $currentYear . "-" . $nextMonth . "-01 00:00:00'"));
		}

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * get player's level
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getPlayerLevel($player_id) {
		$sql = "SELECT pl.playerlevelId, pl.playerGroupId, vs.vipSettingId, vs.groupName, vscbr.vipsettingcashbackruleId, vscbr.vipLevel, vscbr.upgradeAmount, vscbr.downgradeAmount, vscbr.vipLevelName
		FROM playerlevel AS pl
		LEFT JOIN vipsettingcashbackrule AS vscbr ON pl.playerGroupId = vscbr.vipsettingcashbackruleId
		LEFT JOIN vipsetting AS vs ON vscbr.vipSettingId = vs.vipSettingId
		WHERE playerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		return $query->row_array();
	}

	/**
	 * Add new log when player's level is automatically changed
	 *
	 * @param 	array
	 * @return 	array
	 */
	public function addPlayerLevelLog($data) {
		$this->db->insert('player_level_log', $data);
	}

	/**
	 * get player's id
	 *
	 * @param 	str playerName
	 * @return 	array
	 */
	public function getPlayerIdByPlayerName($playerName) {
		$sql = "SELECT playerId FROM player WHERE username = ? ";

		$query = $this->db->query($sql, array($playerName));

		return $query->row_array();
	}

	/* end of Online Players */

	/**
	 *
	 * moved to sale_order->approveSaleOrder
	 */
	// public function saveMainWalletBalance($order) {

	// 	$this->load->library('transactions_library');

	// 	log_message('error', '============save to main wallet========' . $order->id . " status:" . $order->status . " player:" . $order->player_id . " amount:" . $order->amount);

	// 	$player_id = $order->player_id;
	// 	$amount = floatval($order->amount);
	// 	$amount = abs($amount);

	// 	$this->db->trans_off();
	// 	$this->db->trans_begin();

	// 	# GET BEFORE BALANCE
	// 	$before_balance = $this->getMainWalletBalance($player_id)['totalBalanceAmount'];
	// 	$before_balance = floatval($before_balance);

	// 	# UPDATE MAIN WALLET
	// 	$this->db->set('totalBalanceAmount', 'totalBalanceAmount + ' . $amount, false);
	// 	$this->db->where(['playerId' => $player_id, 'type' => 'wallet']);
	// 	$this->db->update('playeraccount');

	// 	# GET AFTER BALANCE
	// 	$after_balance = $this->getMainWalletBalance($player_id)['totalBalanceAmount'];
	// 	$after_balance = floatval($after_balance);

	// 	# VALIDATE
	// 	$success = $amount === floatval($after_balance - $before_balance);

	// 	if ($success) {

	// 		$transaction = $this->transactions_library->saveTransaction([
	// 			'amount' => $amount,
	// 			'transaction_type' => $order->payment_kind,
	// 			'from_id' => $player_id,
	// 			'from_type' => 2,
	// 			'to_id' => 1,
	// 			'to_type' => 1,
	// 			'external_transaction_id' => $order->external_order_id,
	// 			'response_result_id' => $order->response_result_id,
	// 			'note' => $order->notes,
	// 			'before_balance' => $before_balance,
	// 			'after_balance' => $after_balance,
	// 			'status' => 1,
	// 			'flag' => 2,
	// 			'created_at' => $this->utils->getNowForMysql(),
	// 		]);

	// 		if (!$transaction) {
	// 			log_message('error', 'Save transaction failed');
	// 			$success = false;
	// 		}

	// 	} else {
	// 		log_message('error', 'deposit_amount !== (after_balance - before_balance)');
	// 	}

	// 	if ($success) {
	// 		$this->db->trans_commit();
	// 	} else {
	// 		$this->db->trans_rollback();
	// 	}

	// 	return $success ? $transaction : false;
	// }

	/**
	 * getAllActivePlayer
	 *
	 * @return 	array
	 */
	public function getAllActivePlayer() {
		$query = $this->db->query("SELECT playerId FROM player");
		return $query->result_array();
	}

	/**
	 * get email template for promotion
	 *
	 * @param 	string
	 * @return	array
	 */
	public function getEmailTemplatePromo($name) {
		$sql = "SELECT * FROM operator_settings WHERE name = ? ";

		$query = $this->db->query($sql, array($name));

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	public function isPlayerBankAccountNumberExists($bankAccountNumber) {
		$where = "bankAccountNumber = '" . $bankAccountNumber . "'";
		$this->db->select('bankAccountNumber')->where($where);
		$query = $this->db->get('playerbankdetails');

		if ($query->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	const ACTIVE_BANK_ACCOUNT = 1;
	public function isPlayerBankAccountExists($playerId, $bankAccountNumber, $bankTypeId, $dwBank) {

		$this->db->select('bankAccountNumber')
		         ->where('playerId', $playerId)
		         ->where('bankAccountNumber', $bankAccountNumber)
		         ->where('bankTypeId', $bankTypeId)
		         ->where('dwBank', $dwBank);
		$query = $this->db->get('playerbankdetails');

		if ($query->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Will get player promo active
	 *
	 * @param 	playerId int
	 * @param 	type int 0-active promo, 1-promo history
	 * @return	array
	 */
	public function getPlayerActivePromoDetails($playerId, $type = 0) {
		$this->db->select('promorules.promorulesId,
			promorules.promoName,
			promorules.promoCode,
			promorules.promoType,
			promorules.promoType,
			promorules.nonDepositPromoType,
			promorules.withdrawRequirementConditionType,
			promorules.withdrawRequirementBetCntCondition,
			playerpromo.playerpromoId,
			playerpromo.bonusAmount,
			playerpromo.dateProcessed,
			playerpromo.depositAmount,
			player.username,
			player.playerId
			')
			->from('playerpromo')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->join('player', 'player.playerId = playerpromo.playerId', 'left');
		if ($type == 0) {
			$this->db->where('playerpromo.promoStatus', 0); //0-active

		} else {
			$where1 = "(playerpromo.promoStatus='0' or playerpromo.promoStatus='1' or playerpromo.promoStatus='2' or playerpromo.promoStatus='3')";
			$this->db->where($where1);
			$where2 = "(playerpromo.cancelRequestStatus='1' or playerpromo.cancelRequestStatus='2' or playerpromo.cancelRequestStatus='3')";
			$this->db->or_where($where2);
		}
		$this->db->where('playerpromo.playerId', $playerId);
		$this->db->where('playerpromo.transactionStatus', 1); //approved status

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				//check if deposit promo and non deposit promo (email,registration,mobile)
				if (($row['promoType'] == 1 && $row['nonDepositPromoType'] < 4) || ($row['promoType'] == 0)) {
					$row['currentBet'] = $this->getPlayerCurrentBet($row['username'], $row['dateProcessed'], $row['promorulesId'], $row['playerId']);
				} else {
					$row['currentBet'] = $this->getPlayerCurrentBet($row['username'], $row['dateProcessed']);
				}
				$row['currentBet'] = $row['currentBet'] == null || !$row['currentBet'] ? array(array('totalBetAmount' => 0)) : $row['currentBet'];
				$row['dateProcessed'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dateProcessed']));
				$row['promoName'] == null ? $row['promoName'] = '' : $row['promoName'];
				$row['promoCode'] == null ? $row['promoCode'] = '' : $row['promoCode'];
				$data[] = $row;
			}
			//var_dump($data);exit();
			log_message('error', var_export($data, true));
			return $data;
		}
		return false;
	}

	/**
	 * Will get player promo current bet
	 *
	 * @param 	playerName str
	 * @param 	dateJoined datetime
	 * @return	array
	 */
	public function getPlayerCurrentBet($playerName, $dateJoined, $promoId = null, $playerId = null) {
		if ($promoId) {
			$playerGames = $this->getPlayerGames($promoId);
			if (!empty($playerGames)) {

				$dateJoined = date("Y-m-d", strtotime($dateJoined));
				$now = date('Y-m-d');

				$sql = "SELECT SUM(betting_amount) as totalBetAmount
				FROM total_player_game_day
				WHERE date >= ?
				AND date <= ?
				AND player_id = ?
				AND game_description_id IN (" . implode(',', $playerGames) . ")";

				$qry = $this->db->query($sql, array($dateJoined, $now, $playerId));

				if ($qry && $qry->num_rows() > 0) {
					return $qry->row_array();
				}
			}
		} else {
			$this->db->select('SUM(betting_amount) as totalBetAmount')->from('total_player_game_day');
			$this->db->where('date >=', $dateJoined);
			$this->db->where('date <=', date('Y-m-d H:i:s'));
			$this->db->where('player_id', $playerId);
			$query = $this->db->get();
			if ($query->num_rows() > 0) {
				foreach ($query->result_array() as $row) {
					$row['totalBetAmount'] == null ? $row['totalBetAmount'] = 0 : $row['totalBetAmount'] = $row['totalBetAmount'];
					$data[] = $row;
				}
			}

			log_message('error', var_export($data, true));
			return $data;
		}

	}

	/**
	 * Add new log when player's info is updated
	 *
	 * @param 	array
	 * @return 	array
	 */
	public function addPlayerInfoUpdates($playerId, $data) {
		$this->db->insert('playerupdatehistory', $data);
	}

	public function getSubwalletAccount($playerAccountId) {
		$this->db->from('playeraccount')->limit(1);
		$this->db->join('game', 'game.gameId = playeraccount.typeId', 'left');
		$this->db->where('playeraccount.type', 'subwallet');
		$this->db->where('playeraccount.playerAccountId', $playerAccountId);
		$item = $this->db->get()->row_array();
		return $item;
	}

	public function getPlayerIdByUsername($username) {
		if (!empty($username)) {
			$this->db->select('playerId');
			$this->db->where('username', $username);
			$qry = $this->db->get($this->tableName);
			return $this->getOneRowOneField($qry, 'playerId');
		}
		return null;
	}

	public function incTotalDepositCount($playerId) {
		$this->db->set("total_deposit_count", "total_deposit_count+1", false);
		$this->db->update($this->tableName);
	}

	public function incApprovedDepositCount($playerId) {
		$this->db->set("approved_deposit_count", "approved_deposit_count+1", false);
		$this->db->update($this->tableName);
	}

	public function incDeclinedDepositCount($playerId) {
		$this->db->set("declined_deposit_count", "declined_deposit_count+1", false);
		$this->db->update($this->tableName);
	}

	public function getPlayerName($playerId) {
		$this->db->select('playerdetails.firstName,playerdetails.lastName');
		$this->db->where($this->tableName . '.playerId', $playerId);
		$this->db->join('playerdetails', 'playerdetails.playerId = ' . $this->tableName . '.playerId', 'left');
		$qry = $this->db->get($this->tableName);
		return $this->getOneRow($qry);
	}

	public function checkUsernameIfExist($username) {

		$sql = "SELECT playerId as id , username FROM player WHERE username = ? ";

		$q = $this->db->query($sql, array($username));

		$results = $q->result();

		if ($q->num_rows() > 0) {
			$results['isExist'] = TRUE;
			return $results;
		} else {
			return FALSE;
		}
	}

	public function selectCiPlayerSessions() {
		// $sql = 'SELECT * FROM ci_player_sessions';
		// $query = $this->db->query($sql);
		// return array(
		// 	'total' => $query->num_rows(),
		// 	'data' => $query->result_array(),
		// );
	}

	/**
	 * add bank details by deposit/for async.php
	 *
	 * @return	array
	 */
	public function addBankDetailsByDeposit($data) {
		if (!array_key_exists('createdOn', $data)) {
			//add "now"
			$data['createdOn'] = $this->utils->getNowForMysql();
		}
		if (!array_key_exists('updatedOn', $data)) {
			//add "now"
			$data['updatedOn'] = $this->utils->getNowForMysql();
		}
		$this->db->insert('playerbankdetails', $data);
		return $playerbankdetailsId = $this->db->insert_id();
	}

	/**
	 * Will get player account given the Id
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getPlayerAccountByPlayerIdOnly($player_id) {
		$query = $this->db->select('playerAccountId')
						  ->from('player as p')
						  ->join('playeraccount as pa', 'p.playerId = pa.playerId')
						  ->where('p.playerId', $player_id)
						  ->where('type', 'wallet')
						  ->get();

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	/**
	 * Provide drop-down data source for player username input field using select2 plugin.
	 *
	 * @param  String  $input The username input
	 * @param  integer $max   Max number of items returned
	 * @return Array [$items, $total_count]
	 */
	public function getPlayerUsernameSuggestions($input, $max = 10) {
		$this->db->select('playerId')
				    ->from($this->tableName)
				    ->like('username', $input, 'after');
		$count = $this->db->count_all_results();
		$query = $this->db->select('playerId as id, username')
				                ->from($this->tableName)
				                ->like('username', $input, 'after')
				                ->order_by('username')
		                        ->limit($max)->get();
		return [$query->result_array(), $count];
	}

	/**
	 * detail: get the affiliate of the certain player
	 *
	 * @param int $playerId player id
	 * @param array $selectFields The select clause of SQL, key-val as fieldname-fieldvalue. ex,
	 * - $selectFields[affiliates.username] = 'affiliate_name'
	 * - $selectFields[affiliates.affiliateId] = 'affiliate_id'
	 *
	 * @return string|array If count($selectFields) eq. 1, the return the field value.if more then 1 and return array.
	 */

	public function getPlayerAffiliateUsername( $playerId, $selectFields = null){
		if( empty($selectFields) ){ // for default
			$selectFields['affiliates.username'] = 'username';
		}

		$selectFieldArray = [];
		foreach($selectFields as $fieldname => $fieldAlias){
			$selectFieldArray[] = $fieldname. ' as '. $fieldAlias;
		}
		$selectFieldStr = implode(' , ', $selectFieldArray);

		$query = $this->db->select($selectFieldStr)
						 ->from($this->tableName)
						 ->where('playerId', $playerId)
						 ->join('affiliates', 'affiliates.affiliateId = player.affiliateId', 'LEFT')
						 ->get();
		$row = $query->row_array();
		$query->free_result(); // free $query

		if( count($selectFields) > 1 ){
			return $row;
		}else{
			$onlyOne = ''; // get the sepc. field name.
			foreach($selectFields as $key => $value){
				$onlyOne = $value;
			}
			return $row[$onlyOne];
		}

	} // EOF getPlayerAffiliateUsername

	/**
	 * Get playerId , levelId, createdOn by Player level id
	 *
	 * @param int $levelId
	 * @param datetime  $dateTimeFrom
	 * @param datetime  $dateTimeTo
	 * @return array
	 */

	public function getAllPlayersByLevelId($levelId,$dateTimeFrom ,$dateTimeTo,$isFromTheStart){

		$this->utils->debug_log('BATCH ADJUST PLAYERL LEVEL PARAM',$levelId,$dateTimeFrom ,$dateTimeTo,$isFromTheStart);

		$this->db->select('playerid, levelId, createdOn');

            //only within
		if($dateTimeFrom == 0  &&  $isFromTheStart  ){
			$this->utils->debug_log('FROM THE START');
			$this->db->where('createdOn < ',$dateTimeTo);
		}else{
			if($dateTimeFrom != null &&  $dateTimeTo != null){
				$this->utils->debug_log('WITH RANGE');
				$this->db->where('createdOn < ',$dateTimeTo);
				$this->db->where('createdOn > ',$dateTimeFrom);
			}
		}

        #this is for,example we dont know the current level of the players(at different levels)
        #we just only need date of their registration
		if($levelId > 0){
			$this->db->where('levelId',$levelId);
		}


		$query = $this->db->get($this->tableName);
		return $query->result_array();

	}

	/**
	* get userId if its using or child of roleId
	*
	* @param	int
	* @return 	array
	*/
	public function getActiveCurrency() {
		if($this->utils->isEnabledMDB()){
			return $this->utils->getActiveCurrencyDBFormatOnMDB();
		}

		$query = $this->db->query("SELECT * FROM currency where status = 0");

		return $query->row_array();
	}

	/**
	 * Will get all tags
	 *
	 * @return 	array
	 */
	public function getAllManualSubtractBalanceTags() {
		$query = $this->db->query("SELECT * FROM manual_subtract_balance_tag msbt LEFT JOIN adminusers AS au ON msbt.createBy = au.userId");

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	public function getManualSubtractBalanceTagDetails($id) {
		$this->db->select('*')->from('manual_subtract_balance_tag');
		$this->db->where('id', $id);

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

	public function getManualSubtractBalanceTagByName($adjust_tag_name) {
		$sql = "SELECT * FROM manual_subtract_balance_tag WHERE adjust_tag_name = ? ";

		$query = $this->db->query($sql, array($adjust_tag_name));

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	public function insertManualSubtractBalanceTag($data) {
		$this->db->insert('manual_subtract_balance_tag', $data);
	}

	public function editManualSubtractBalanceTag($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('manual_subtract_balance_tag', $data);
	}

	public function insertTransactinosTag($data) {
		$this->db->insert('transactions_tag', $data);
	}

	public function deleteTransactionsTag($id) {
		$this->db->where('msbt_id', $id);
		$this->db->delete('transactions_tag');
	}

	public function deleteManualSubtractBalanceTag($id) {
		$this->db->where('id', $id);
		$this->db->delete('manual_subtract_balance_tag');
	}

	public function countNewPlayer($lastViewedNewPlayerDateTime) {
		$sql = "SELECT playerId FROM player WHERE createdOn > ? ";
		$query = $this->db->query($sql, array($lastViewedNewPlayerDateTime));

		if (!$query->result()) {
			return 0;
		}

		return count($query->result());
	}

	public function getBlockStatus($playerId){
	    //block status
        //block =0 active
        //block =1 block
        //block =5 suspended
		//block =8 Failed login attempts

        $this->db->select('blocked');
        $this->db->from('player');
        $this->db->where('player.playerId', $playerId);
        return $this->runOneRowOneField('blocked');


    }

	public function isAutoMachineUser($name) {
		return in_array($name, $this->config->item('is_auto_machine_user_list'));
	}

    public function excludePlayerByTags($tagIds){
        $qq = $this->db->select('playerId')
            ->from('playertag')
            ->where_in('tagId', $tagIds)
            ->get();
        return $qq->result_array();
    }

    public function getPlayerCountryByPlayerId($player_id) {
    	$this->db->select('country');
    	$this->db->from('playerdetails');
    	$this->db->where('playerId', $player_id);
    	return $this->runOneRowOneField('country');
    }

	/**
	 * get player's dispatch account level
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getPlayerDispatchAccountLevel($player_id) {
		$sql = "SELECT player.dispatch_account_level_id, dispatch_account_level.group_id, dispatch_account_group.group_name, dispatch_account_level.level_name
		FROM player
		LEFT JOIN dispatch_account_level ON player.dispatch_account_level_id = dispatch_account_level.id
		LEFT JOIN dispatch_account_group ON dispatch_account_level.group_id = dispatch_account_group.id
		WHERE player.playerId = ? ";

		$query = $this->db->query($sql, array($player_id));

		return $query->row_array();
	}

    /**
     * OG-659
     * Adds affiliate to player without referral code
     *
     *
     *
     */
    public function addAffiliateToPlayer($playerId, $affiliateId) {

        $affiliate = $this->CI->affiliate->getAffiliateById($affiliateId);
        $current_time = $this->CI->utils->getNowForMysql();
        $games = $this->CI->external_system->getAllActiveSytemGameApi();

        //update the affiliate id
        $this->CI->player_model->updateAffiliateId($playerId, $affiliateId);

        $playerAccount = array(
            'playerId' => $playerId,
            'currency' => $affiliate['currency'],
            'typeOfPlayer' => 'real',
            'type' => self::PLAYERACCOUNT_TYPE_AFFILIATE,
            'typeId' => $affiliate['affiliateId'],
            'batchPassword' => self::DEFAULT_PLAYERACCOUNT_BATCHPASSWORD,
            'status' => self::DEFAULT_PLAYERACCOUNT_STATUS,
        );
        $this->CI->player_model->insertPlayerAccount($playerAccount);

        // get affiliate options
        $affiliateOptions = $this->CI->affiliate->getAffiliateTermsOptions($affiliateId);

        // if affiliate already changed their default
        if ($affiliateOptions) {
            foreach ($affiliateOptions as $aff) {
                $this->CI->affiliate->addAffiliateEarnings(array(
                    'affiliateId' => $affiliate['affiliateId'],
                    'gameId' => $aff['gameId'],
                    'type' => 'registration',
                    'amount' => $aff['optionsValue'],
                    'currency' => $affiliate['currency'],
                    'date' => $current_time,
                    'status' => self::DEFAULT_AFFILIATE_EARNINGS_STATUS,
                ));
            }
        } else {
            foreach ($games as $game) {
                $default_opt = $this->CI->affiliate->getAffiliateDefaultOptionsByGameId($game['id']);

                foreach ($default_opt as $aff) {
                    $this->CI->affiliate->addAffiliateEarnings(array(
                        'affiliateId' => $affiliate['affiliateId'],
                        'gameId' => $aff['gameId'],
                        'type' => 'registration',
                        'amount' => $aff['optionsValue'],
                        'currency' => $affiliate['currency'],
                        'date' => $current_time,
                        'status' => self::DEFAULT_AFFILIATE_EARNINGS_STATUS,
                    ));

                }
            }

        } //else end

        return $affiliate['username'];
    }
}

/* End of file player.php */
/* Location: ./application/models/player.php */
