<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * MOVE TO friendreferrallevel.php
 *
 * friendreferrallevel
 *
 * @author	Spencer.Kuo
 */

require_once dirname(__FILE__) . '/base_model.php';

class Friend_referral_level extends BaseModel {
	protected $tableName = 'friend_referral_level';

	function __construct() {
		parent::__construct();
	}

	/**
	 * get All Vip Player Commission
	 * @return array
	 */
	public function getAllFriendReferralLevel() {
		$this->db->from($this->tableName);
		$this->db->order_by('min_betting', 'asc');
		return $this->runMultipleRowArray();
	}

	/**
	 * get Vip Player Commission By Id
	 * @param  int $id
	 * @return array
	 */
	public function getFriendReferralLevelById($id) {
		return $this->getOneRowArrayById($id);
	}

	/**
	 * Inserts data to friend_referral_level
	 * moved to group_level
	 *
	 * @param	array
	 * @return	boolean
	 */
	public function addFriendReferralLevel($data) {
		$this->db->insert('friend_referral_level', $data);
	}

	/**
	 * edit data to friend_referral_level
	 * @param $data array
	 * @param $id
	 */
	public function editFriendReferralLevel($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('friend_referral_level', $data);
	}


	/**
	 * delete friend_referral_level
	 *
	 * @return	$array
	 */
	public function deleteFriendReferralLevel($id) {
		$this->db->where('id', $id);
		$this->db->delete('friend_referral_level');
	}
}

/* End of file friend_referral_level.php */
/* Location: ./application/models/friend_referral_level.php */
