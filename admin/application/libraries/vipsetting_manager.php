<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * VIP setting Manager
 *
 * VIP setting Manager library for FHI OG System
 *
 * @deprecated move to group_level
 *
 * @package		Vip Setting Manager
 * @author		ASRII
 * @version		1.0.0
 */
class Vipsetting_manager
{
	private $error = array();

	function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->library(array(''));
		$this->ci->load->model(array('vipsetting'));
	}

	/**
	 * Will create new tag using the parameter data
	 *
	 * @param 	array
	 */
	public function addVIPGroup($data) {
		$this->ci->vipsetting->addVIPGroup($data);
	}

	/**
	 * Will create new VIP Group Level using the parameter data
	 *
	 * @param 	array
	 */
	public function increaseVIPGroupLevel($vipSettingId) {
		$this->ci->vipsetting->increaseVipGroupLevel($vipSettingId);
	}

	/**
	 * Will create new VIP Group Level using the parameter data
	 *
	 * @param 	array
	 */
	public function decreaseVipGroupLevel($vipSettingId) {
		return $this->ci->vipsetting->decreaseVipGroupLevel($vipSettingId);
	}

	/**
	 * Will get vip group name
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function getVipGroupName($group_name) {
		return $this->ci->vipsetting->getVipGroupName($group_name);
	}

	/**
	 * Will get vip group level details
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function getVipGroupLevelDetails($vipgrouplevelId) {
		return $this->ci->vipsetting->getVipGroupLevelDetails($vipgrouplevelId);
	}

	/**
	 * Will get cashback payout setting
	 *
	 * @return 	array
	 */
	public function getCashbackPayoutSetting() {
		return $this->ci->vipsetting->getCashbackPayoutSetting();
	}

	/**
	 * getCashbackPayoutTimeSetting
	 *
	 * @return 	array
	 */
	public function getCashbackPayoutTimeSetting() {
		return $this->ci->vipsetting->getCashbackPayoutTimeSetting();
	}

	/**
	 * Will activate group
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function activateVIPGroup($data) {
		return $this->ci->vipsetting->activateVIPGroup($data);
	}

	/**
	 * Will activate group
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function getVIPGroupRules($vipgroupId) {
		return $this->ci->vipsetting->getVIPGroupRules($vipgroupId);
	}

	/**
	 * Will delete vip group level
	 *
	 * @param vipgrouplevelid
	 */
	public function deletevipgrouplevel($vipgrouplevelId) {
		$this->ci->vipsetting->deletevipgrouplevel($vipgrouplevelId);
	}

	/**
	 * get all vip settings
	 *
	 * @return 	array
	 */
	public function getVIPSettingList($sort, $limit, $offset) {
		return $this->ci->vipsetting->getVIPSettingList($sort, $limit, $offset);
	}

	/**
	 * get all vip group list
	 *
	 * @return 	array
	 */
	public function getVipGroupList() {
		return $this->ci->vipsetting->getVipGroupList();
	}

	/**
	 * search vip settings
	 *
	 * @return 	array
	 */
	public function searchVipGroupList($search, $limit, $offset) {
		return $this->ci->vipsetting->searchVipGroupList($search, $limit, $offset);
	}

	/**
	 * get all vip settings
	 *
	 * @return 	array
	 */
	public function getVIPSettingListToExport() {
		return $this->ci->vipsetting->getVIPSettingListToExport();
	}

	/**
	 * Get Ranking List
	 *
	 * @return	$array
	 */
	public function getVIPGroupDetails($vipSettingId) {
		return $this->ci->vipsetting->getVIPGroupDetails($vipSettingId);
	}

	/**
	 * Edit vip group
	 *
	 * @return	$array
	 */
	public function editVIPGroup($data, $vipsetting_id) {
		return $this->ci->vipsetting->editVIPGroup($data, $vipsetting_id);
	}

	/**
	 * Edit cashback payout period
	 *
	 * @return	$array
	 */
	public function editCashbackPeriodSetting($data, $vipGroupPayoutSettingId) {
		return $this->ci->vipsetting->editCashbackPeriodSetting($data, $vipGroupPayoutSettingId);
	}

	/**
	 * Edit vip group bonus rule
	 *
	 * @return	$array
	 */
	public function editVipGroupBonusRule($data) {
		return $this->ci->vipsetting->editVipGroupBonusRule($data);
	}

	/**
	 * Edit vip group bonus rule
	 *
	 * @return	$array
	 */
	public function editVipGroupBonusPerGame($data) {
		return $this->ci->vipsetting->editVipGroupBonusPerGame($data);
	}

	/**
	 * Delete VIP Group
	 *
	 * @return	$array
	 */
	public function deleteVIPGroup($vipsettingId) {
		return $this->ci->vipsetting->deleteVIPGroup($vipsettingId);
	}

	/**
	 * Get VIP Group item
	 *
	 * @return	$array
	 */
	public function deleteVIPGroupItem($vipsettingId) {
		return $this->ci->vipsetting->deleteVIPGroupItem($vipsettingId);
	}

	/**
	 * Will get cashback bonus per game
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function getCashbackBonusPerGame($vipsettingcashbackruleId) {
		return $this->ci->vipsetting->getCashbackBonusPerGame($vipsettingcashbackruleId);
	}

}

/* End of file vipsetting_manager.php */
/* Location: ./application/libraries/vipsetting_manager.php */
