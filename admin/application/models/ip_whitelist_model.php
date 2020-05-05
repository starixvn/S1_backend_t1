<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Ip_whitelist_model
 *
 * General Behavior
 *
 * * Count all ip that is white listed
 *
 * @category System Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Ip_whitelist_model extends BaseModel {

	protected $tableName = 'ip_whitelist';

	function __construct() {
		parent::__construct();
	}

	/**
	 * overview : ip whitelisted
	 *
	 * detail : count all ip that is whitelisted
	 * @param  int    	$game_platform_id 
	 * @param  string 	$ip_address       
	 * @return array                
	 */
	public function ip_whitelisted($game_platform_id, $ip_address) {
		return $this->db->where(array(
			'game_platform_id' => $game_platform_id,
			'ip_address' => $ip_address,
		))->count_all_results($this->tableName) != 0;
	}

}