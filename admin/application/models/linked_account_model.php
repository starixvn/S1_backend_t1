<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 *
 * @category Linked Account
 * @version 1.8.10
 * @author Asrii
 * @copyright 2013-2017 TripleOneTech
 */
class Linked_account_model extends BaseModel {

	const SEARCH_TYPE_EXACT_USERNAME = 1;
	const SEARCH_TYPE_SIMILAR_USERNAME = 2;
	const ARRAY_FIRST_CHILD = 0;
	protected $tableName = 'linked_accounts';

	public function __construct() {
		parent::__construct();
	}

	/**
	 * overview : Save linked account with remarks
	 *
	 * details : save linked accounts of player
	 *
	 * @param int $data	data that needs to save
	 *
	 */
	public function addLinkedAccounts($data) {
		$result = false;
		if(!empty($data)){
			$this->db->insert($this->tableName, $data);
			$result = $this->db->insert_id();
		}

		return $result;
	}

	/**
	 * overview : search linked accounts
	 *
	 * @param int $linkId
	 */
	public function getLinkedAccount($search,$isAddSearchUsername=false){
		$this->db->select("la.id,
						   la.username,
						   la.link_id,
						   la.link_datetime,
						   la.remarks,
						   player.blocked,
						   player.playerId,
						   player.status
						   ")->from("linked_accounts as la");
		$this->db->join("player","player.username = la.username","left");

		if(!empty($search)){
			if($search['search_type'] == self::SEARCH_TYPE_EXACT_USERNAME){
				$this->db->where("la.username",$search["username"]);
			}elseif($search['search_type'] == self::SEARCH_TYPE_SIMILAR_USERNAME){
				$this->db->like('la.username', $search["username"], 'both');
			}
			if($search['link_datetime']) $this->db->where("la.link_datetime",$search["link_datetime"]);
		}
		$result = $this->runMultipleRowArray();

		if(!empty($result)){
			foreach ($result as $key => $value) {
				if($value['link_id']){
					$result[$key]['linked_accounts'] = $this->getLinkedAccountsByLinkId($value['link_id'],$value['username']);
				}
			}
		}
		if($isAddSearchUsername){
			$result['playerUsername'] = $search["username"];
		}
		return $result;
	}

	/**
	 * overview : get linked account details
	 *
	 * @param string $linkId - random string
	 * @param int $username
	 */
	private function getLinkedAccountsByLinkId($linkId,$username,$withActionColumn=true){
		$this->db->select("la.id,
						   la.username,
						   la.link_datetime,
						   la.remarks,
						   player.blocked,
						   player.email,
						   player.playerId,
						   player.status,
						   player.lastLoginIp as last_login_ip
						   ")->from("linked_accounts as la");
		$this->db->join("player","player.username = la.username","left");
		$this->db->where("la.link_id",$linkId);
		$this->db->where("la.username != ",$username);
		$result = $this->runMultipleRowArray();

		if(!empty($result) && $withActionColumn){
			foreach ($result as $key => $value) {
				$linkedAccountId = "\"".$value['id']."\"";
				$linkedAccountUsername = "\"".$value['username']."\"";
				$result[$key]['action_edit_remarks'] = "<a href='javascript:;' onclick='showEditLinkedAccountModal(".$linkedAccountId.")' data-toggle='tooltip' data-placement='top' title='".lang('Edit')."' data-original-title='".lang('Edit')."'><i class='glyphicon glyphicon-edit'></i></a>";

				$result[$key]['action_delete_remarks'] = "<a href='javascript:;' id='deleteLinkedAccountBtn' onclick='deleteLinkedAccountById(".$linkedAccountId.",".$linkedAccountUsername.")' data-toggle='tooltip' data-placement='top' title='".lang('Delete')."' data-original-title='".lang('Delete')."'><i class='glyphicon glyphicon-trash'></i></a>";
			}
		}elseif(!empty($result) && is_array($result)){
			#this is used in excel report, removing this 2 columns (id,playerId)
			foreach ($result as $key => $value) {
				unset($result[$key]['id']);
				unset($result[$key]['playerId']);
			}
		}
		return $result;
	}

	/**
	 * overview : get player linked account details
	 *
	 * @param int $id
	 */
	public function getPlayerLinkedAccountDetailsById($id){
		$this->db->select("la.id,
						   la.username,
						   la.remarks,
						   ")->from("linked_accounts as la");
		$this->db->where("la.id",$id);
		$result = $this->runOneRowArray();
		return $result;
	}

	/**
	 * overview : get player linked account link id
	 *
	 * @param int $id
	 */
	public function getPlayerLinkedAccountLinkIdByUsername($username){
		$this->db->select("la.link_id")->from("linked_accounts as la");
		$this->db->where("la.username",$username);
		$result = $this->runOneRowArray();
		return $result;
	}

	/**
	 * overview : update player linked account remarks
	 *
	 * @param array $data
	 */
	public function updatePlayerLinkedAccountRemarks($data) {
		$this->db->where('id', $data['id']);
		$this->db->update($this->tableName, $data);
		return $this->db->affected_rows();
	}


	/**
	 * overview : delete player linked account
	 *
	 * @param array $data
	 */
	public function deletePlayerLinkedAccountById($linkedAccountId) {
		$this->db->delete($this->tableName, array('id' => $linkedAccountId));
	}

	/**
	 * overview : get available players
	 *
	 * @param string $username
	 * @return null
	 */
	public function getAvailablePlayers($username = null,$playerIds=null) {
		$this->db->from('player')->where('status', self::OLD_STATUS_ACTIVE);
		if($playerIds) $this->db->where_not_in('playerId',$playerIds);

		if ($username) {
			$this->db->like('username', $username, 'after');
		}
		$result = $this->runMultipleRow();
		return $result;
	}

	public function getLinkedAccountByUsername($username){
		$this->db->select('link_id')->from($this->tableName)->where("username",$username);
		$result = $this->runOneRowArray();
		return $result;
	}

	public function getLinkAccountsPlayerIds($linkId){
		$this->db->select('player.playerId')->from($this->tableName . " as la");
		$this->db->join('player','player.username = la.username','left');
		$this->db->where('la.link_id',$linkId);
		$playerIds = $this->runMultipleRowArray();
		return $playerIds;
	}

	/**
	 * overview : search linked accounts
	 *
	 * @param int $linkId
	 */
	public function getLinkedAccountCsvReport($search){
		$this->db->select("
						   la.link_id,
						   la.username,
						   la.link_datetime,
						   ")->from("linked_accounts as la");
		$this->db->join("player","player.username = la.username","left");

		if(!empty($search)){
			if($search['search_type'] == self::SEARCH_TYPE_EXACT_USERNAME){
				$this->db->where("la.username",$search["username"]);
			}elseif($search['search_type'] == self::SEARCH_TYPE_SIMILAR_USERNAME){
				$this->db->like('la.username', $search["username"], 'both');
			}
			if($search['link_datetime']) $this->db->where("la.link_datetime",$search["link_datetime"]);
		}
		$result = $this->runMultipleRowArray();

		if(!empty($result)){
			foreach ($result as $key => $value) {
				if($value['link_id']){
					$result[$key]['linked_accounts_cnt'] = count($this->getLinkedAccountsByLinkId($value['link_id'],$value['username'],false));
					$result[$key]['linked_accounts'] = json_encode($this->getLinkedAccountsByLinkId($value['link_id'],$value['username'],false));
				}
			}
		}
		 return $result;
	}


	public function isLinkAccountExists($username){
		$this->db->select("la.link_id")->from("linked_accounts as la");
		$this->db->where("la.username",$username);
		$success = $this->runExistsResult();
		return $success;
	}

	public function updateLinkedAccounts(){
		$this->db->where('username', $data['username']);
		return $this->runUpdate($data);
	}


	public function updateOldLinkIdToNewLinkId($oldLinkId,$data){
		$this->db->where('link_id', $oldLinkId);
		return $this->runUpdate($data);
	}

	public function isPlayerAcctWasLinkedAccountAlready($username,$linkId){
		$this->db->select("la.link_id")->from("linked_accounts as la");
		$this->db->where("la.username",$username);
		$this->db->where("la.link_id",$linkId);
		$success = $this->runExistsResult();
		return $success;
	}
}