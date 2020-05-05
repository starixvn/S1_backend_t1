<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Total_operator_game_month
 *
 * General behaviors include :
 *
 * * Get last sync day
 * * Unique already exist
 * * Sync operator game month
 * * Get operator record per year
 *
 * @category Game Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Total_operator_game_month extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "total_operator_game_month";

	/**
	 * overview : get last sync month
	 *
	 * @return string
	 */
	public function getLastSyncMonth() {
		$this->db->order_by('month desc');
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'month');
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
	 * overview : sync operator game per month
	 *
	 * @param string $data
	 * @return array
	 */
	function syncToOperatorGameMonth($data) {
		if ($this->isUniqueIdAlreadyExists($data['uniqueid'])) {
			$this->db->where('uniqueid', $data['uniqueid']);
			return $this->db->update($this->tableName, $data);
		} else {
			return $this->db->insert($this->tableName, $data);
		}
	}

	/**
	 * overview : get operator record per year
	 *
	 * @param datetime $dateFrom
	 * @param datetime $dateTo
	 * @return boolean
	 */
	function getOperatorRecordPerYear($dateFrom, $dateTo) {
		$this->db->select("game_platform_id, game_type_id, game_description_id, substr(convert(month,CHAR),1,4) as year,sum(betting_amount) as betting_amount,sum(result_amount) as result_amount", false);
		$this->db->where("substr(convert(month,CHAR),1,4) >=", $dateFrom);
		$this->db->where("substr(convert(month,CHAR),1,4) <=", $dateTo);
		$this->db->group_by(array("game_platform_id", "game_type_id", "game_description_id", "substr(convert(month,CHAR),0,4)"));
		$qry = $this->db->get($this->tableName);
		return $this->getMultipleRow($qry);
		// $qry = $this->db->query("SELECT * FROM $this->tableName
		// 							WHERE updated_at >= '" . $dateFrom . "'
		// 							AND updated_at <= '" . $dateTo . "'
		// 						");
		// return $this->getMultipleRow($qry);
	}
}

///END OF FILE///////
