<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Third Party Account Manager
 *
 * Payment Manager library for FHI OG System
 *
 * @package		ThirdParty Account Manager
 * @author		ASRII
 * @version		1.0.0
 */

class Thirdpartyaccount_manager
{
	private $error = array();

	function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->library(array(''));
		$this->ci->load->model(array('thirdpartyaccount'));
	}

	/**
	 * Gets All Over the counter Payment Method
	 *
	 * @return	array
	 */
	public function getAllThirdPartyPaymentMethodAccount($sort, $limit, $offset) {
		return $this->ci->thirdpartyaccount->getAllThirdPartyPaymentMethodAccount($sort, $limit, $offset);
	}

	/**
	 * get all thirdparty account setting
	 *
	 * @return 	array
	 */
	public function getThirdPartyAccountListToExport() {
		return $this->ci->thirdpartyaccount->getThirdPartyAccountListToExport();
	}


	/**
	 * Gets thirdParty account last rank order
	 *
	 * @return	array
	 */
	public function getThirdPartyAccountLastRankOrder() {
		return $this->ci->thirdpartyaccount->getThirdPartyAccountLastRankOrder();
	}

	/**
	 * Gets thirdparty account last rank order
	 *
	 * @return	array
	 */
	public function getThirdPartyAccountBackupLastRankOrder() {
		return $this->ci->thirdpartyaccount->getThirdPartyAccountBackupLastRankOrder();
	}

	/**
	 * Gets All Over the counter Payment Method
	 *
	 * @return	array
	 */
	public function getPaymentMethodDetails($id1,$id) {
		return $this->ci->thirdpartyaccount->getPaymentMethodDetails($id,$id);
	}

	/**
	 * Gets player group
	 *
	 * @return	array
	 */
	public function getPlayerGroup() {
		return $this->ci->thirdpartyaccount->getPlayerGroup();
	}

	/**
	 * Gets players level
	 *
	 * @return	array
	 */
	public function getAllPlayerLevels() {
		return $this->ci->thirdpartyaccount->getAllPlayerLevels();
	}

	/**
	 * Add thirdparty account Settings
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function addThirdPartyAccount($data,$playerLevels) {
		return $this->ci->thirdpartyaccount->addThirdPartyAccount($data,$playerLevels);
	}

	/**
	 * Edit thirdparty account Settings
	 *
	 * @return	$array
	 */
	public function editThirdPartyAccount($data, $playerLevels, $vipsetting_id) {
		return $this->ci->thirdpartyaccount->editThirdPartyAccount($data, $playerLevels, $vipsetting_id);
	}

	/**
	 * Get thirdparty account details
	 *
	 * @return	$array
	 */
	public function getThirdPartyAccountDetails($thirdpartyaccountId) {
		return $this->ci->thirdpartyaccount->getThirdPartyAccountDetails($thirdpartyaccountId);
	}

	/**
	 * Will addThirdPartyAccountPlayerLevelsLimit
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function addThirdPartyAccountPlayerLevelsLimit($data) {
		return $this->ci->thirdpartyaccount->addThirdPartyAccountPlayerLevelsLimit($data);
	}

	/**
	 * Delete ThirdParty accounts
	 *
	 * @return	$array
	 */
	public function deleteThirdPartyAccounts($thirdpartyAccountId) {
		return $this->ci->thirdpartyaccount->deleteThirdPartyAccounts($thirdpartyAccountId);
	}

	/**
	 * Get ThirdParty account item
	 *
	 * @return	$array
	 */
	public function deleteThirdPartyAccountItem($thirdpartyAccountId) {
		return $this->ci->thirdpartyaccount->deleteThirdPartyAccountItem($thirdpartyAccountId);
	}

	/**
	 * Will activate thirdparty account
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function activateThirdPartyAccount($data) {
		return $this->ci->thirdpartyaccount->activateThirdPartyAccount($data);
	}

	/**
	 * search thirdparty account
	 *
	 * @return 	array
	 */
	public function searchThirdPartyAccountList($search, $limit, $offset) {
		return $this->ci->thirdpartyaccount->searchThirdPartyAccountList($search, $limit, $offset);
	}

}

/* End of file thirdpartyaccount_manager.php */
/* Location: ./application/libraries/thirdpartyaccount_manager.php */
