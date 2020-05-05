<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Opus_sportsbook_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "opus_sportsbook_game_logs";

	function getGameLogStatistics($dateFrom, $dateTo) {

		$this->db->select('player.playerId as player_id');
		$this->db->select('player.username as player_username');
		$this->db->select('opus_sportsbook_game_logs.member_id as game_username');
		$this->db->select('opus_sportsbook_game_logs.bettypename as game_name');
		$this->db->select('opus_sportsbook_game_logs.sportname as game_type_name');
		// $this->db->select('opus_sportsbook_game_logs.gameid as external_game_id');
		$this->db->select('opus_sportsbook_game_logs.external_uniqueid');
		$this->db->select('opus_sportsbook_game_logs.transaction_time as start_at');
		$this->db->select('opus_sportsbook_game_logs.last_update as end_at');
		$this->db->select('opus_sportsbook_game_logs.stake as bet_amount');
		$this->db->select('opus_sportsbook_game_logs.winlost_amount as result_amount');
		$this->db->select('opus_sportsbook_game_logs.response_result_id');
		$this->db->from($this->tableName);
		$this->db->join('game_provider_auth', 'opus_sportsbook_game_logs.member_id = game_provider_auth.login_name AND game_provider_auth.game_provider_id = ' . OPUS_SPORTSBOOK_API);
		$this->db->join('player', 'game_provider_auth.player_id = player.playerId');

		if ($dateFrom) {
			$this->db->where('opus_sportsbook_game_logs.transaction_time >=', $dateFrom);
		}

		if ($dateTo) {
			$this->db->where('opus_sportsbook_game_logs.transaction_time <=', $dateTo);
		}

		return $this->runMultipleRow();
	}

}

///END OF FILE///////
