<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * save/get password for game platform
 *
 *
 */
class Gameapirecord extends BaseModel {

	protected $tableName = 'gameapirecord';

	function __construct() {
		parent::__construct();
	}

	/**
	 * @param string loginName
	 * @param datetime dateFrom
	 * @param datetime dateTo
	 *
	 * @return array
	 */

	function queryTotalBettingAmount($loginName, $dateTimeFrom, $dateTimeTo, $game) {
		$dateFrom = $dateTimeFrom->format('Y-m-d') . ' 00:00:00';
		$dateTo = $dateTimeTo->format('Y-m-d') . ' 23:59:59';
		$qry = $this->db->query("SELECT SUM(bet) AS bettingAmount
			FROM gameapirecord
			WHERE playername = '" . $loginName . "'
			AND gamedate >= '" . $dateFrom . "'
			AND gamedate <= '" . $dateTo . "'
			AND apitype = '" . $game . "' ");

		return $qry->row_array();
	}
}

/////end of file///////