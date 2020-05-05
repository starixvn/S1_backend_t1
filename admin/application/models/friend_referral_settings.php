<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * overview : Class Friend_referral_settings
 *
 * General behaviors include :
 *
 * * Get friend referral settings
 * * Save friend referral settings
 *
 * @category Player Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Friend_referral_settings extends BaseModel {

	const TABLE_NAME = 'friendreferralsettings';

	/**
	 * overview : Friend_referral_settings constructor.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * overview : get friend referral settings
	 *
	 * @return array
	 */
	public function getFriendReferralSettings() {
		$query = $this->db->get(self::TABLE_NAME, array(
			'status' => 0,
		));
		return $query->row_array();
	}

	/**
	 * overview : save friend referral settings
	 *
	 * @param  array	$data
	 * @return int
	 */
	public function saveFriendReferralSettings($data) {
		$result = 0;
		if ($friend_referral_settings = $this->getFriendReferralSettings()) {
			$result = $this->db->update(self::TABLE_NAME, $data, array(
				'friendReferralSettingsId' => $friend_referral_settings['friendReferralSettingsId'],
			));
		} else {
			$result = $this->db->insert(self::TABLE_NAME, $data);
		}
		return $result;
	}
}

///END OF FILE