<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ggpoker_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "ggpoker_game_logs";

	/**
	 * overview : check if refNo already exist
	 *
	 * @param  int		$refNo
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($dateNow,$player_id) {
		$qry = $this->db->get_where($this->tableName, array(
			'external_uniqueid' => $dateNow,
			'player_id' => $player_id,
		));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * overview : update game logs
	 *
	 * @param  array	$data
	 *
	 * @return boolean
	 */
	function updateGameLogs($data) {
		$this->db->where('external_uniqueid', $data['external_uniqueid'])
					->where('player_id', $data['player_id']);
		return $this->db->update($this->tableName, $data);
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {
		$select = 'ggpoker_game_logs.player_id,
				  ggpoker_game_logs.username,
				  ggpoker_game_logs.external_uniqueid,
				  ggpoker_game_logs.downline_id,
				  ggpoker_game_logs.game_name,
				  ggpoker_game_logs.net_revenue,
				  ggpoker_game_logs.profit_and_loss,
				  ggpoker_game_logs.converted_profit_and_loss AS result_amount,
				  ggpoker_game_logs.response_result_id,
				  ggpoker_game_logs.game_date,
				  ggpoker_game_logs.created_at,
                  ggpoker_game_logs.updated_at,
                  ggpoker_game_logs.converted_rake_or_fee,
                  ggpoker_game_logs.converted_profit_and_loss_poker,
                  ggpoker_game_logs.converted_profit_and_loss_side_game,
                  ggpoker_game_logs.converted_fish_buffet_reward,
                  ggpoker_game_logs.converted_network_give_away,
                  ggpoker_game_logs.converted_network_paid,
                  ggpoker_game_logs.converted_brand_promotion,
                  ggpoker_game_logs.converted_tournament_over_lay,
                  ggpoker_game_logs.updated_at,
				  game_description.id AS game_description_id,
				  game_description.game_name AS game,
				  game_description.game_code,
				  game_description.game_type_id,
				  game_description.void_bet,
				  game_type.game_type';

		$this->db->select($select,false);
		$this->db->from('ggpoker_game_logs');
		$this->db->join('game_description', 'game_description.game_code = "GG Poker"  AND game_description.game_platform_id = "'.GGPOKER_GAME_API.'" AND game_description.void_bet != 1', 'LEFT');
		$this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
		$this->db->where('ggpoker_game_logs.game_date >= "'.$dateFrom.'" AND ggpoker_game_logs.game_date <= "' . $dateTo . '"');
		$qobj = $this->db->get();
		// echo $this->db->last_query();exit();
		return $qobj->result_array();
	}

}

///END OF FILE///////