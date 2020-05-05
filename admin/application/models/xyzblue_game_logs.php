<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Xyzblue_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "xyzblue_game_logs";

	/**
	 * overview : check if roundid already exist
	 *
	 * @param  int		$roundid
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($roundid) {
		$qry = $this->db->get_where($this->tableName, array('roundid' => $roundid));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	function getGameCode($game_name) {
		$qry = $this->db->get_where('game_description', array('game_platform_id' => XYZBLUE_API,
															'attributes' => $game_name
																));
		return $this->getOneRow($qry);
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {

		$select = 'xyzblue_game_logs.PlayerId,
				  xyzblue_game_logs.UserName,
				  xyzblue_game_logs.external_uniqueid,
				  xyzblue_game_logs.startdate AS game_date,
				  xyzblue_game_logs.response_result_id,
				  xyzblue_game_logs.winamount AS result_amount,
				  xyzblue_game_logs.amount AS BetAmount,
				  xyzblue_game_logs.roundid,
				  xyzblue_game_logs.startdate,
				  xyzblue_game_logs.enddate,
				  game_description.id AS game_description_id,
				  game_description.game_name AS game,
				  game_description.game_code,
				  game_description.game_type_id,
				  game_description.void_bet,
				  game_type.game_type';

		$this->db->select($select,false);
		$this->db->from('xyzblue_game_logs');
		$this->db->join('game_description', 'xyzblue_game_logs.gamecode = game_description.game_code AND game_description.game_platform_id = "'.XYZBLUE_API.'" AND game_description.void_bet != 1', 'LEFT');
		$this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
		$this->db->where('xyzblue_game_logs.startdate >= "'.$dateFrom.'" AND xyzblue_game_logs.enddate <= "' . $dateTo . '"');
		$qobj = $this->db->get();

		return $qobj->result_array();
	}

}

///END OF FILE///////