<?php
require_once dirname(__FILE__) . '/base_model.php';

class Country_whitelist_model extends BaseModel {

	protected $tableName = 'country_whitelist';

	function __construct() {
		parent::__construct();
	}

	public function country_whitelisted($game_platform_id, $country) {
		return $this->db->where(array(
			'game_platform_id' => $game_platform_id,
			'country' => $country,
		))->count_all_results($this->tableName) != 0;
	}

}