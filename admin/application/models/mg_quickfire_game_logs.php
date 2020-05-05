<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Mg_quickfire_game_logs extends Base_game_logs_model {

	public function __construct() {
		parent::__construct();
	}

	protected $tableName = "mg_quickfire_game_logs";

	public function getGameLogStatistics($dateFrom, $dateTo) {

	}

	public function get_game_record($game_id) {
		return $this->db->where('gameid', $game_id)->get($this->tableName)->row_array();
	}

	public function get_bet_records($game_id) {
		return $this->db->where('gameid', $game_id)->where('playtype', 'bet')->get($this->tableName)->result_array();
	}

	public function get_refund_record_by_action_id($action_id) {
		return $this->db->where('actionid', $action_id)->where('playtype', 'refund')->get($this->tableName)->row_array();
	}

	public function get_bet_record_by_action_id($action_id) {
		return $this->db->where('actionid', $action_id)->where('playtype', 'bet')->get($this->tableName)->row_array();
	}
	
	public function get_record($system, $game_username, $gamereference, $gameid, $playtype, $actionid) {
		return $this->db->where('system', $system)
						->where('game_username', $game_username)
						->where('gamereference', $gamereference)
						->where('gameid', $gameid)
						->where('playtype', $playtype)
						->where('actionid', $actionid)
						->get($this->tableName)->row_array();
	}

	public function get_game_record_by_action_id($action_id) {
		return $this->db->where('external_uniqueid', $action_id)->get($this->tableName)->row_array();
	}

	public function get_game_record_by_external_uniqueid($external_uniqueid) {
		return $this->db->where('external_uniqueid', $external_uniqueid)->get($this->tableName)->row_array();
	}

	public function update_game_record_by_action_id($action_id, $data) {
		$this->db->update($this->tableName, $data, array('actionid' => $action_id));
	}

	public function get_game_records_by_game_id($game_id) {
		return $this->db->where('gameid', $game_id)->get($this->tableName)->result_array();
	}

	public function getDataForMerging($gamereference, $game_username, $gameid) {

		$this->db->select('system');
		$this->db->select('gamereference');
		$this->db->select('game_username');
		$this->db->select('gameid');

		$this->db->select_min('IFNULL(timestamp, created_at)','start_at');
		$this->db->select_max('IFNULL(timestamp, created_at)','end_at');
		$this->db->select_sum("IF(playtype = 'bet', amount, 0)",'bet_amount');
		$this->db->select_sum("IF(playtype = 'refund', amount, 0)",'refund_amount');
		$this->db->select_max('CONCAT_WS(\'|\',id,after_balance)','after_balance');
		$this->db->select("SUM(CASE playtype WHEN 'win' THEN amount WHEN 'progressivewin' THEN amount ELSE 0 END) as win_amount", false);
		$this->db->from('mg_quickfire_game_logs');

		$this->db->where('gamereference', $gamereference);
		$this->db->where('game_username', $game_username);
		$this->db->where('gameid', $gameid);

		$this->db->group_by('system');
		$this->db->group_by('gamereference');
		$this->db->group_by('game_username');
		$this->db->group_by('gameid');

		$query = $this->db->get();

		$row = $query->row_array();

		if (isset($row, $row['after_balance'])) {
			$row['after_balance'] = @explode('|', $row['after_balance'])[1] ? : 0;
		}

		return $row;

	}

}
///END OF FILE///////
