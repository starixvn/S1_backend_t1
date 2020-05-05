<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Thirdpartyaccount
 *
 * This model represents payment. It operates the following tables:
 * - payment deposit and withdrawal
 *
 * @author	ASRII
 */

class Thirdpartyaccount extends CI_Model
{
	function __construct() {
		parent::__construct();
	}

	/**
       * Will get all otc payment method
       *
       * @return array
       */

	public function getAllThirdPartyPaymentMethodAccount($sort, $limit, $offset) {
		if($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$query = $this->db->query("SELECT thirdpartypaymentmethodaccount.*,admin1.username AS createdBy, admin2.username AS updatedBy
			FROM thirdpartypaymentmethodaccount
			LEFT JOIN adminusers AS admin1
			ON admin1.userId = thirdpartypaymentmethodaccount.createdBy
			LEFT JOIN adminusers AS admin2
			ON admin2.userId = thirdpartypaymentmethodaccount.updatedBy
			ORDER BY thirdpartypaymentmethodaccount." . $sort . " ASC
			$limit
			$offset
		");

		$cnt = 0;
		if ($query->num_rows() > 0) {
				foreach ($query->result_array() as $row)
				{
					$row['createdOn'] = mdate('%M %d, %Y - %h:%i:%s %A',strtotime($row['createdOn']));
					if($row['updatedOn']!=null){
						$row['updatedOn'] = mdate('%M %d, %Y - %h:%i:%s %A',strtotime($row['updatedOn']));
					}
					$row['totalDeposit'] = $this->calculateBankDeposit($row['thirdpartypaymentmethodaccountId']);
					$data[] = $row;
					$data[$cnt]['thirdPartyAccountPlayerLevelLimit'] = $this->getThirdPartyAccountPlayerLevelLimit($row['thirdpartypaymentmethodaccountId']);
					$cnt++;
				}
				// var_dump($data);exit();
				return $data;
			}
			return false;
	}

	/**
	 * Will search thirdParty account
	 *
	 * @return 	array
	 */
	public function searchThirdPartyAccountList($search, $limit, $offset) {

		if($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		if($search!=''){
			$query = $this->db->query("SELECT thirdpartypaymentmethodaccount.*,admin1.username AS createdBy, admin2.username AS updatedBy
					FROM thirdpartypaymentmethodaccount
					LEFT JOIN adminusers AS admin1
					ON admin1.userId = thirdpartypaymentmethodaccount.createdBy
					LEFT JOIN adminusers AS admin2
					ON admin2.userId = thirdpartypaymentmethodaccount.updatedBy
					WHERE thirdpartypaymentmethodaccount.thirdPartyName = '" . $search . "'
					ORDER BY thirdpartypaymentmethodaccount.thirdpartypaymentmethodaccountId ASC
					$limit
					$offset
			");
		}else{
			$query = $this->db->query("SELECT thirdpartypaymentmethodaccount.*,admin1.username AS createdBy, admin2.username AS updatedBy
					FROM thirdpartypaymentmethodaccount
					LEFT JOIN adminusers AS admin1
					ON admin1.userId = thirdpartypaymentmethodaccount.createdBy
					LEFT JOIN adminusers AS admin2
					ON admin2.userId = thirdpartypaymentmethodaccount.updatedBy
					ORDER BY thirdpartypaymentmethodaccount.thirdpartypaymentmethodaccountId ASC
					$limit
					$offset
			");
		}


		$cnt = 0;
		if ($query->num_rows() > 0) {
				foreach ($query->result_array() as $row)
				{
					$row['createdOn'] = mdate('%M %d, %Y - %h:%i:%s %A',strtotime($row['createdOn']));
					if($row['updatedOn']!=null){
						$row['updatedOn'] = mdate('%M %d, %Y - %h:%i:%s %A',strtotime($row['updatedOn']));
					}
					$data[] = $row;
					$data[$cnt]['thirdPartyAccountPlayerLevelLimit'] = $this->getThirdPartyAccountPlayerLevelLimit($row['thirdpartypaymentmethodaccountId']);
					$cnt++;
				}
				//var_dump($data);exit();
				return $data;
			}
			return false;
	}

	/**
	 * Get thirdPartyaccount setting List
	 *
	 * @return	$array
	 */
	public function getThirdPartyAccountListToExport() {
		$this->db->select('
						   thirdpartypaymentmethodaccount.thirdPartyName,
						   thirdpartypaymentmethodaccount.thirdpartyAccountName,
						   thirdpartypaymentmethodaccount.thirdpartyAccount,
						   thirdpartypaymentmethodaccount.description,
						   thirdpartypaymentmethodaccount.dailyMaxDepositAmount,
						   adminusers.userName as createdBy
							')->from('thirdpartypaymentmethodaccount')
		->join('adminusers','adminusers.userId = thirdpartypaymentmethodaccount.createdBy');

		$query = $this->db->get();
		return $query;
	}

	/**
       * Will get getThirdPartyAccountLastRankOrder
       *
       * @return array
       */

	public function getThirdPartyAccountLastRankOrder() {
		$this->db->select('accountOrder')->from('thirdpartypaymentmethodaccount');
		$this->db->order_by('thirdpartypaymentmethodaccount.accountOrder', 'desc');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
       * Will get getThirdPartyAccountBackupLastRankOrder
       *
       * @return array
       */

	public function getThirdPartyAccountBackupLastRankOrder() {
		$this->db->select('accountOrder')->from('thirdpartyaccountbackup');
		$this->db->order_by('thirdpartyaccountbackup.accountOrder', 'desc');
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
						   vipsettingcashbackrule.vipsettingcashbackruleId,
						   vipsetting.vipSettingId,
						   vipsetting.groupName')
				->from('vipsettingcashbackrule')
				->join('vipsetting','vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId','left');
		$this->db->order_by('vipsettingcashbackrule.vipsettingcashbackruleId', 'ASC');
		$query = $this->db->get();
		return $query->result_array();
	}


	/**
	 * get getThirdPartyAccountDetails
	 *
	 * @param	int
	 * @return 	array
	 */
	public function getThirdPartyAccountDetails($thirdPartyAccountId) {
		$this->db->select('thirdpartypaymentmethodaccount.*')->from('thirdpartypaymentmethodaccount');
		$this->db->order_by('thirdpartypaymentmethodaccount.accountOrder', 'asc');
		$this->db->where('thirdpartypaymentmethodaccountId', $thirdPartyAccountId);
		$query = $this->db->get();

		$cnt = 0;
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row)
			{
				$data[] = $row;
				$data[$cnt]['thirdPartyAccountPlayerLevelLimit'] = $this->getThirdPartyAccountPlayerLevelLimit($row['thirdpartypaymentmethodaccountId']);
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

	public function getThirdPartyPaymentMethodDetails($id) {
		$this->db->select('*')->from('thirdpartypaymentmethodaccount');
		$this->db->where('thirdpartypaymentmethodaccountId', $id);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
				foreach ($query->result_array() as $row)
				{
					$data[] = $row;
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

	public function getThirdPartyAccountPlayerLevelLimit($thirdPartyAccountId) {
		$this->db->select('vipsettingcashbackrule.vipsettingcashbackruleId,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName')
		->from('thirdpartyaccountplayerlevellimit')
		->join('vipsettingcashbackrule','vipsettingcashbackrule.vipsettingcashbackruleId = thirdpartyaccountplayerlevellimit.playerLevelId','left')
		->join('vipsetting','vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId','left');
		$this->db->where('thirdpartyaccountplayerlevellimit.thirdPartyAccountId', $thirdPartyAccountId);

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
       * Will add thirdParty account
       *
	   * @param $data array
       * @return array
       */

	public function addThirdPartyAccount($data,$playerLevels) {
		$this->db->insert('thirdpartypaymentmethodaccount', $data);
		$thirdPartyAccountId = $this->db->insert_id();
		foreach ($playerLevels as $key) {
			$thirdPartyAccountPlayerLevelsLimit['thirdPartyAccountId'] = $thirdPartyAccountId;
			$thirdPartyAccountPlayerLevelsLimit['playerLevelId'] = $key; //link to vipsettingcashbackrule table (vipsettingcashbackruleId)
			$this->addThirdPartyAccountPlayerLevelsLimit($thirdPartyAccountPlayerLevelsLimit);
		}
	}

	/**
	 * edit vip group
	 *
	 * @return	$array
	 */
	public function editThirdPartyAccount($data, $playerLevels, $thirdPartyAccountId) {
		$this->db->where('thirdpartypaymentmethodaccountId', $thirdPartyAccountId);
		$this->db->update('thirdpartypaymentmethodaccount', $data);

		if($playerLevels!=null){
			//clear record
			$this->deleteThirdPartyAccountPlayerLevelLimit($thirdPartyAccountId);

			//add new record
			foreach ($playerLevels as $key) {
				$thirdPartyAccountPlayerLevelsLimit['thirdPartyAccountId'] = $thirdPartyAccountId;
				$thirdPartyAccountPlayerLevelsLimit['playerLevelId'] = $key; //link to vipsettingcashbackrule table (vipsettingcashbackruleId)
				$this->addThirdPartyAccountPlayerLevelsLimit($thirdPartyAccountPlayerLevelsLimit);
			}
		}
	}

	/**
       * Will add thirdParty account
       *
	   * @param $data array
       * @return array
       */

	public function addThirdPartyAccountPlayerLevelsLimit($data) {
		$this->db->insert('thirdpartyaccountplayerlevellimit', $data);
	}


	/**
	 * Will delete thirdparty accounts
	 *
	 * @param 	int
	 */
	public function deleteThirdPartyAccounts($thirdpartypaymentmethodaccountId) {
		$where = "thirdpartypaymentmethodaccountId = '" . $thirdpartypaymentmethodaccountId . "'";
		$this->db->where($where);
		$this->db->delete('thirdpartypaymentmethodaccount');
	}

	/**
	 * Will delete thirdparty account player level limit
	 *
	 * @param 	int
	 */
	public function deleteThirdPartyAccountPlayerLevelLimit($thirdPartyAccountId) {
		$where = "thirdPartyAccountId = '" . $thirdPartyAccountId . "'";
		$this->db->where($where);
		$this->db->delete('thirdpartyaccountplayerlevellimit');
	}

	/**
	 * delete thirdparty account item
	 *
	 * @return	$array
	 */
	public function deleteThirdPartyAccountItem($thirdpartypaymentmethodaccountId) {
		$this->db->where('thirdpartypaymentmethodaccountId', $thirdpartypaymentmethodaccountId);
		$this->db->delete('thirdpartypaymentmethodaccount');
	}

	/**
	 * activate thirdparty account item
	 *
	 * @return	$array
	 */
	public function activateThirdPartyAccount($data) {
		$this->db->where('thirdpartypaymentmethodaccountId', $data['thirdpartypaymentmethodaccountId']);
		$this->db->update('thirdpartypaymentmethodaccount', $data);
	}

	/**
	 * calculate manual third party account
	 *
	 * @return	$array
	 */
	function calculateBankDeposit($thirdpartypaymentmethodaccountId) {
    	$this->db->select('SUM(walletaccount.amount) AS totalBankDeposit')->from('walletaccount')
    	->join('manualthirdpartydepositdetails','manualthirdpartydepositdetails.walletAccountId = walletaccount.walletAccountId');
		$this->db->where('manualthirdpartydepositdetails.thirdpartypaymentmethodaccountId', $thirdpartypaymentmethodaccountId);
		$this->db->where('walletaccount.transactionType', 'deposit');
		$this->db->where('walletaccount.dwStatus', 'approved');

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
				foreach ($query->result_array() as $row)
				{
					$data[] = $row;
				}
				//var_dump($data);exit();
				return $data;
			}
			return false;
    }



}

/* End of file payment.php */
/* Location: ./application/models/payment.php */
