<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Total_operator_game_year
 *
 * General behaviors include :
 *
 * * Get last sync day
 * * Unique already exist
 * * Sync operator game month
 *
 * @category Game Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Total_operator_game_year extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "total_operator_game_year";

	/**
	 * overview : get last sync year
	 *
	 * @return string
	 */
	public function getLastSyncYear() {
		$this->db->order_by('year desc');
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'year');
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
	 * overview : sync operator game per year
	 *
	 * @param string $data
	 * @return array
	 */
	function syncToOperatorGameYear($data) {
		if ($this->isUniqueIdAlreadyExists($data['uniqueid'])) {
			$this->db->where('uniqueid', $data['uniqueid']);
			return $this->db->update($this->tableName, $data);
		} else {
			return $this->db->insert($this->tableName, $data);
		}
	}

}

///END OF FILE///////
