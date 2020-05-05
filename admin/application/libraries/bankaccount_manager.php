<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Bank Account Manager
 *
 * Payment Manager library for FHI OG System
 *
 * @package		Bank Account Manager
 * @author		ASRII
 * @version		1.0.0
 */

class Bankaccount_manager
{
	private $error = array();

	function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->library(array(''));
		$this->ci->load->model(array('bankaccount'));
	}

	/**
	 * Gets All Over the counter Payment Method
	 *
	 * @return	array
	 */
	public function getAllOtcPaymentMethod($sort, $limit, $offset) {
		return $this->ci->bankaccount->getAllOtcPaymentMethod($sort, $limit, $offset);
	}

	/**
	 * get all bank account setting
	 *
	 * @return 	array
	 */
	public function getBankAccountListToExport() {
		return $this->ci->bankaccount->getBankAccountListToExport();
	}


	/**
	 * Gets bank account last rank order
	 *
	 * @return	array
	 */
	public function getBankAccountLastRankOrder() {
		return $this->ci->bankaccount->getBankAccountLastRankOrder();
	}

	/**
	 * Gets bank account last rank order
	 *
	 * @return	array
	 */
	public function getBankAccountBackupLastRankOrder() {
		return $this->ci->bankaccount->getBankAccountBackupLastRankOrder();
	}

	/**
	 * Gets All Over the counter Payment Method
	 *
	 * @return	array
	 */
	public function getPaymentMethodDetails($id1,$id) {
		return $this->ci->bankaccount->getPaymentMethodDetails($id,$id);
	}

	/**
	 * Gets player group
	 *
	 * @return	array
	 */
	public function getPlayerGroup() {
		return $this->ci->bankaccount->getPlayerGroup();
	}

	/**
	 * Gets players level
	 *
	 * @return	array
	 */
	public function getAllPlayerLevels() {
		return $this->ci->bankaccount->getAllPlayerLevels();
	}

	/**
	 * Add bank account Settings
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function addBankAccount($data,$playerLevels) {
		return $this->ci->bankaccount->addBankAccount($data,$playerLevels);
	}

	/**
	 * Edit bank account Settings
	 *
	 * @return	$array
	 */
	public function editBankAccount($data, $playerLevels, $vipsetting_id) {
		return $this->ci->bankaccount->editBankAccount($data, $playerLevels, $vipsetting_id);
	}

	/**
	 * Get bank account details
	 *
	 * @return	$array
	 */
	public function getBankAccountDetails($bankaccountId) {
		return $this->ci->bankaccount->getBankAccountDetails($bankaccountId);
	}

	/**
	 * Will addBankAccountPlayerLevelsLimit
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function addBankAccountPlayerLevelsLimit($data) {
		return $this->ci->bankaccount->addBankAccountPlayerLevelsLimit($data);
	}

	/**
	 * Delete Bank accounts
	 *
	 * @return	$array
	 */
	public function deleteBankAccounts($bankAccountId) {
		return $this->ci->bankaccount->deleteBankAccounts($bankAccountId);
	}

	/**
	 * Get Bank account item
	 *
	 * @return	$array
	 */
	public function deleteBankAccountItem($bankAccountId) {
		return $this->ci->bankaccount->deleteBankAccountItem($bankAccountId);
	}

	/**
	 * Will activate bank account
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function activateBankAccount($data) {
		return $this->ci->bankaccount->activateBankAccount($data);
	}

	/**
	 * search bank account
	 *
	 * @return 	array
	 */
	public function searchBankAccountList($search, $limit, $offset) {
		return $this->ci->bankaccount->searchBankAccountList($search, $limit, $offset);
	}

}

/* End of file bankaccount_manager.php */
/* Location: ./application/libraries/bankaccount_manager.php */
