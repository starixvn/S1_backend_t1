<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Tcg_game_logs_result extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "tcg_game_draw_results";


	/**
	 * overview : get agin game draw result by numero
	 *
	 * @param  int		$numero
	 *
	 * @return row
	 */
	public function getWinningGameDrawResult($numero, $game_code) {
		$qry = $this->db->get_where($this->tableName, array('numero' => $numero, 'game_code' => $game_code));
		return $this->getOneRowOneField($qry,'win_no');
    }
    
    public function getGameLogStatistics($dateFrom, $dateTo) {	
	}
}

///END OF FILE///////
