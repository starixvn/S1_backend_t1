<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Total_operator_game_hour
 *
 * General behaviors include :
 *
 * * Get last sync hour
 * * Unique already exist
 * * Sync operator game hour
 * * Get operator record per day
 * * Get first/last record date time
 *
 * @category Game Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Total_operator_game_hour extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "total_operator_game_hour";

	/**
	 * overview : get last sync hour
	 *
	 * @return string
	 */
	public function getLastSyncHour() {
		$this->db->order_by('date_hour desc');
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'hour');
	}

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
	 * overview : sync operator game hour
	 *
	 * @param string $data
	 * @return array
	 */
	function syncToOperatorGameHour($data) {
		if ($this->isUniqueIdAlreadyExists($data['uniqueid'])) {
			$this->db->where('uniqueid', $data['uniqueid']);
			return $this->db->update($this->tableName, $data);
		} else {
			return $this->db->insert($this->tableName, $data);
		}
	}

	/**
	 * overview : get operator record per day
	 *
	 * @param datetime $dateFrom
	 * @param datetime $dateTo
	 * @return boolean
	 */
	function getOperatorRecordPerDay($dateFrom, $dateTo) {
		$this->db->select("game_platform_id, game_type_id, game_description_id, date,sum(betting_amount) as betting_amount,sum(result_amount) as result_amount", false);
		$this->db->where("date >=", $dateFrom);
		$this->db->where("date <=", $dateTo);
		$this->db->group_by(array("game_platform_id", "game_type_id", "game_description_id", "date"));
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
		$this->db->order_by('date_hour asc');
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'date');
	}

	/**
	 * overview : get last record date time
	 *
	 * @return datetime
	 */
	public function getLastRecordDateTime() {
		$this->db->order_by('date_hour desc');
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'date');
	}
}

///END OF FILE///////
