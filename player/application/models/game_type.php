<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 *
 * @deprecated moved to game_type_model
 */
class Game_type extends BaseModel {

	public function __construct() {
		parent::__construct();
	}

	protected $tableName = "game_type";

	/**
	 * get game type id
	 *
	 * @param 	str gameType
	 * @return 	int
	 */
	public function getGameTypeId($gameType) {
		$qry = $this->db->get_where($this->tableName, array('game_type' => $gameType));
		return $this->getOneRowOneField($qry, 'id');
	}

	# TODO(KAISER): DOCS
	public function getGameTypeList($criteria = array(), $orderby = 'order_id', $direction = 'asc') {
		return $this->db->from($this->tableName)
			->where('game_type !=', 'unknown')
			->where('game_type !=', 'Sidegames')
			->where('game_type !=', 'Live Games')
			->where($criteria)
			->order_by($orderby, $direction)
			->get()
			->result_array();
	}

}

///END OF FILE///////
