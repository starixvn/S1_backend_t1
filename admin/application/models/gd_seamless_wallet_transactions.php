<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Gd_seamless_wallet_transactions extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}
	
	protected $tableName = "gd_seamless_wallet_transactions";



	/**
	 * overview : check if external_uniqueid already exist
	 *
	 * @param  int		$external_uniqueid
	 *
	 * @return boolean
	 */
	public function isTransactionExist($external_uniqueid) {
		$this->db->from($this->tableName)
			->where('external_uniqueid', $external_uniqueid);
		return $this->runExistsResult();
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {
		return null;
	}

}

///END OF FILE///////