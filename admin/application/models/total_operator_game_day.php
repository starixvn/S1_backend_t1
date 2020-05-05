<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Total_operator_game_day
 *
 * General behaviors include :
 *
 * * Get last sync day
 * * Unique already exist
 * * Sync operator game day
 * * Get operator record per month
 *
 * @category Game Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Total_operator_game_day extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "total_operator_game_day";

	/**
	 * overview : get last sync day
	 *
	 * @return string
	 */
	public function getLastSyncDay() {
		$this->db->order_by('date desc');
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'date');
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
	 * overview : sync operator game day
	 *
	 * @param string $data
	 * @return array
	 */
	function syncToOperatorGameDay($data) {
		if ($this->isUniqueIdAlreadyExists($data['uniqueid'])) {
			$this->db->where('uniqueid', $data['uniqueid']);
			return $this->db->update($this->tableName, $data);
		} else {
			return $this->db->insert($this->tableName, $data);
		}
	}

	/**
	 * overview : get operator record per month
	 *
	 * @param datetime $dateFrom
	 * @param datetime $dateTo
	 * @return boolean
	 */
	function getOperatorRecordPerMonth($dateFrom, $dateTo) {
		$this->db->select("game_platform_id, game_type_id, game_description_id, date_format(date,'%Y%m') as month,sum(betting_amount) as betting_amount,sum(result_amount) as result_amount", false);
		$this->db->where("date_format(date,'%Y%m') >=", $dateFrom);
		$this->db->where("date_format(date,'%Y%m') <=", $dateTo);
		$this->db->group_by(array("game_platform_id", "game_type_id", "game_description_id", "date_format(date,'%Y%m')"));
		$qry = $this->db->get($this->tableName);
		return $this->getMultipleRow($qry);
		// $qry = $this->db->query("SELECT * FROM $this->tableName
		// 							WHERE date >= '" . $dateFrom . "'
		// 							AND date <= '" . $dateTo . "'
		// 						");
		// return $this->getMultipleRow($qry);
	}
}

///END OF FILE///////
