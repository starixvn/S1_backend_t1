<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Bankaccount
 *
 * This model represents payment. It operates the following tables:
 * - payment deposit and withdrawal
 *
 * @author	ASRII
 */

class Bankaccount extends CI_Model
{
	function __construct() {
		parent::__construct();
	}

	/**
       * Will get all otc payment method
       *
       * @return array
       */

	public function getAllOtcPaymentMethod($sort = null, $limit = null, $offset = null) {
		$this->db->select('otcpaymentmethod.*, admin1.username AS createdBy, admin2.username AS updatedBy');
		$this->db->from('otcpaymentmethod');
		$this->db->join('adminusers AS admin1', 'admin1.userId = otcpaymentmethod.createdBy', 'left');
		$this->db->join('adminusers AS admin2', 'admin2.userId = otcpaymentmethod.updatedBy', 'left');
		if ($sort) {
			$this->db->order_by($sort);
		}
		if ($limit) {
			$this->db->limit($limit, $offset);
		}
		$query = $this->db->get();
		$list = $query->result_array();
		foreach ($list as &$list_item) {
			$list_item['totalDeposit'] = $this->calculateBankDeposit($list_item['otcPaymentMethodId']);
			$list_item['bankAccountPlayerLevelLimit'] = $this->getBankAccountPlayerLevelLimit($list_item['otcPaymentMethodId']);
		}
		return $list;
	}

	/**
	 * Will search bank account
	 *
	 * @return 	array
	 */
	public function searchBankAccountList($search = null, $limit = null, $offset = null) {
		$this->db->select('otcpaymentmethod.*, admin1.username AS createdBy, admin2.username AS updatedBy');
		$this->db->from('otcpaymentmethod');
		$this->db->join('adminusers AS admin1', 'admin1.userId = otcpaymentmethod.createdBy', 'left');
		$this->db->join('adminusers AS admin2', 'admin2.userId = otcpaymentmethod.updatedBy', 'left');
		if ($search) {
			$this->db->where('bankName', $search);
		}
		$this->db->order_by('otcPaymentMethodId');
		if ($limit) {
			$this->db->limit($limit, $offset);
		}
		$query = $this->db->get();
		$list = $query->result_array();
		foreach ($list as &$list_item) {
			$list_item['bankAccountPlayerLevelLimit'] = $this->getBankAccountPlayerLevelLimit($list_item['otcPaymentMethodId']);
		}
			return $list;
	}

	/**
	 * Get bankaccount setting List
	 *
	 * @return	$array
	 */
	public function getBankAccountListToExport() {
		$this->db->select('otcpaymentmethod.otcPaymentMethodId,
						   otcpaymentmethod.bankName,
						   otcpaymentmethod.branchName,
						   otcpaymentmethod.accountNumber,
						   otcpaymentmethod.accountName,
						   otcpaymentmethod.description,
						   otcpaymentmethod.dailyMaxDepositAmount,
						   adminusers.userName as createdBy
							')->from('otcpaymentmethod')
		->join('adminusers','adminusers.userId = otcpaymentmethod.createdBy');
		//$this->db->select('otcpaymentmethod.otcPaymentMethodId')->from('otcpaymentmethod');

		$query = $this->db->get();
		return $query;
	}

	/**
       * Will get getBankAccountLastRankOrder
       *
       * @return array
       */

	public function getBankAccountLastRankOrder() {
		$this->db->select('accountOrder')->from('otcpaymentmethod');
		$this->db->order_by('otcpaymentmethod.accountOrder', 'desc');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
       * Will get getBankAccountBackupLastRankOrder
       *
       * @return array
       */

	public function getBankAccountBackupLastRankOrder() {
		$this->db->select('accountOrder')->from('bankaccountbackup');
		$this->db->order_by('bankaccountbackup.accountOrder', 'desc');
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
	 * get getBankAccountDetails
	 *
	 * @param	int
	 * @return 	array
	 */
	public function getBankAccountDetails($bankAccountId) {
		$this->db->select('otcpaymentmethod.*')->from('otcpaymentmethod');
		$this->db->order_by('otcpaymentmethod.accountOrder', 'asc');
		$this->db->where('otcPaymentMethodId', $bankAccountId);
		$query = $this->db->get();

		$cnt = 0;
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row)
			{
				$data[] = $row;
				$data[$cnt]['bankAccountPlayerLevelLimit'] = $this->getBankAccountPlayerLevelLimit($row['otcPaymentMethodId']);
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

	public function getOTCPaymentMethodDetails($id) {
		$this->db->select('*')->from('otcpaymentmethod');
		$this->db->where('otcPaymentMethodId', $id);

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

	public function getBankAccountPlayerLevelLimit($bankAccountId) {
		$this->db->select('vipsettingcashbackrule.vipsettingcashbackruleId,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName')
		->from('bankaccountplayerlevellimit')
		->join('vipsettingcashbackrule','vipsettingcashbackrule.vipsettingcashbackruleId = bankaccountplayerlevellimit.playerLevelId','left')
		->join('vipsetting','vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId','left');
		$this->db->where('bankaccountplayerlevellimit.bankAccountId', $bankAccountId);

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
       * Will add bank account
       *
	   * @param $data array
       * @return array
       */

	public function addBankAccount($data,$playerLevels) {
		$this->db->insert('otcpaymentmethod', $data);
		$bankAccountId = $this->db->insert_id();
		foreach ($playerLevels as $key) {
			$bankAccountPlayerLevelsLimit['bankAccountId'] = $bankAccountId;
			$bankAccountPlayerLevelsLimit['playerLevelId'] = $key; //link to vipsettingcashbackrule table (vipsettingcashbackruleId)
			$this->addBankAccountPlayerLevelsLimit($bankAccountPlayerLevelsLimit);
		}
	}

	/**
	 * edit vip group
	 *
	 * @return	$array
	 */
	public function editBankAccount($data, $playerLevels, $bankAccountId) {
		$this->db->where('otcPaymentMethodId', $bankAccountId);
		$this->db->update('otcpaymentmethod', $data);

		if($playerLevels!=null){
			//clear record
			$this->deleteBankAccountPlayerLevelLimit($bankAccountId);

			//add new record
			foreach ($playerLevels as $key) {
				$bankAccountPlayerLevelsLimit['bankAccountId'] = $bankAccountId;
				$bankAccountPlayerLevelsLimit['playerLevelId'] = $key; //link to vipsettingcashbackrule table (vipsettingcashbackruleId)
				$this->addBankAccountPlayerLevelsLimit($bankAccountPlayerLevelsLimit);
			}
		}
	}

	/**
       * Will add bank account
       *
	   * @param $data array
       * @return array
       */

	public function addBankAccountPlayerLevelsLimit($data) {
		$this->db->insert('bankaccountplayerlevellimit', $data);
	}


	/**
	 * Will delete bank accounts
	 *
	 * @param 	int
	 */
	public function deleteBankAccounts($otcPaymentMethodId) {
		$where = "otcPaymentMethodId = '" . $otcPaymentMethodId . "'";
		$this->db->where($where);
		$this->db->delete('otcpaymentmethod');
	}

	/**
	 * Will delete bank account player level limit
	 *
	 * @param 	int
	 */
	public function deleteBankAccountPlayerLevelLimit($bankAccountId) {
		$where = "bankAccountId = '" . $bankAccountId . "'";
		$this->db->where($where);
		$this->db->delete('bankaccountplayerlevellimit');
	}

	/**
	 * delete bank account item
	 *
	 * @return	$array
	 */
	public function deleteBankAccountItem($otcPaymentMethodId) {
		$this->db->where('otcPaymentMethodId', $otcPaymentMethodId);
		$this->db->delete('otcpaymentmethod');
	}

	/**
	 * activate bank account item
	 *
	 * @return	$array
	 */
	public function activateBankAccount($data) {
		$this->db->where('otcPaymentMethodId', $data['otcPaymentMethodId']);
		$this->db->update('otcpaymentmethod', $data);
	}

	/**
	 * calculate bank account
	 *
	 * @return	$array
	 */
	function calculateBankDeposit($bankAccountId){
    	$this->db->select('SUM(walletaccount.amount) AS totalBankDeposit')->from('walletaccount')
    	->join('localbankdepositdetails','localbankdepositdetails.walletAccountId = walletaccount.walletAccountId');
		$this->db->where('localbankdepositdetails.bankAccountId', $bankAccountId);
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
