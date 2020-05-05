<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Total_operator_game_minute
 *
 * General behaviors include :
 *
 * * Get last sync hour
 * * Unique already exist
 * * Sync operator game hour
 * * Get operator record per minute
 * * Get first/last record date time
 *
 * @category Game Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Total_operator_game_minute extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "total_operator_game_minute";

	/**
	 * overview : check if unique id already exist
	 *
	 * @param string $uniqueId
	 * @return boolean
	 */
	function isUniqueIdAlreadyExists($uniqueId) {
		$qry = $this->db->get_where($this->tableName, array('uniqueid' => $uniqueId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * overview : sync operator game minute
	 *
	 * @param string $data
	 * @return array
	 */
	function syncToOperatorGameMinute($data) {
		if ($this->isUniqueIdAlreadyExists($data['uniqueid'])) {
			$this->db->where('uniqueid', $data['uniqueid']);
			return $this->db->update($this->tableName, $data);
		} else {
			return $this->db->insert($this->tableName, $data);
		}
	}

	/**
	 * overview : get operator record per hour
	 *
	 * @param datetime $dateTimeFrom
	 * @param datetime $dateTimeTo
	 * @return boolean
	 */
	function getOperatorRecordPerHour($dateTimeFrom, $dateTimeTo) {
		$this->db->select("game_platform_id, game_type_id, game_description_id, date,sum(betting_amount) as betting_amount,sum(result_amount) as result_amount", false);
		//$this->db->where("date >=", $dateFrom);
		//$this->db->where("date <=", $dateTo);
		if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
			$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeFrom));
			$toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeTo));

			$this->db->where('date_minute >=', $fromDateMinuteStr);
			$this->db->where('date_minute <=', $toDateMinuteStr);
		}
		$this->db->group_by(array("game_platform_id", "game_type_id", "game_description_id", "date", "hour"));
		$qry = $this->db->get($this->tableName);
		return $this->getMultipleRow($qry);
		// $qry = $this->db->query("SELECT * FROM $this->tableName
		// 							WHERE date >= '" . $dateFrom . "'
		// 							AND date <= '" . $dateTo . "'
		// 						");
		// return $this->getMultipleRow($qry);
	}

	/**
	 * overview : get first record date time
	 *
	 * @return datetime
	 */
	public function getFirstRecordDateTime() {
		$this->db->order_by('date_minute asc');
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'date');
	}

	/**
	 * overview : get last record date time
	 *
	 * @return datetime
	 */
	public function getLastRecordDateTime() {
		$this->db->order_by('date_minute desc');
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'date');
	}
}

///END OF FILE///////