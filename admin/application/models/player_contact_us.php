<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 *
 * * Get kyc Status
 * * Get player kyc Status
 * * Get/insert/update/ player kyc status
 *
 * @category Player KYC Status
 * @version 1.8.10
 * @author Jhunel L. Ebero
 * @copyright 2013-2017 TripleOneTech
 */
class player_contact_us extends BaseModel {
	public function __construct() {
		parent::__construct();
	}

	protected $tableName = 'player_contact_us';

	/**
	 * @author Jhunel L. Ebero
	 * overview : Save Player Contact Us
	 *
	 * details : It's applicable for contact us form in website / outside application range
	 *
	 * @param int $data	data that needs to save
	 *
	 */
	public function addPlayerContactUs($data) {
		$response = false;
		if(!empty($data)){
			$this->db->insert($this->tableName, $data);
			$response = $this->db->insert_id();
		}
		
		return $response;
	}

}