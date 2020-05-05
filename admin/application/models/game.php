<?php

class Game extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	function getGameTree() {
		return $this->db
			->select('game_description.id')
			->select('game_description.game_name')
			->select('game_description.english_name')
			->select('game_description.game_platform_id')
			->select('game_description.game_type_id')
			->select('external_system.system_code')
			->select('game_type.game_type_lang')
			->select('game_type.game_type')
			->from('game_description')
			->join('external_system', 'external_system.id = game_description.game_platform_id')
			->join('game_type', 'game_type.id = game_description.game_type_id')
			->where('game_description.game_code !=', 'unknown')
			->where('game_type.game_type !=', 'unknown')
			->order_by('external_system.id')
			->order_by('game_type.id')
			->order_by('game_description.id')
			->get()
			->result();
	}

	function getGameHistory($player_id, $game_type) {
		$query = $this->db->query("SELECT * FROM playergamehistory where playerId = '" . $player_id . "' AND gameType = '" . $game_type . "' ORDER BY gamehistoryid DESC");
		$result = $query->row_array();
		if (!$result) {
			return false;
		} else {
			return $result;
		}
	}

	function insertGame($data) {
		$result = $this->db->insert('playergamehistory', $data);
	}

	function insertGameDetails($data) {
		$result = $this->db->insert('playergamehistorydetails', $data);
	}

	function updateCurrentMoney($data, $player_account_id, $player_id) {
		$where = "playerId = '" . $player_id . "' AND playerAccountId = '" . $player_account_id . "'";
		$result = $this->db->update('playeraccounthistory', $data);
	}

	public function getGames() {
		$qry = $this->db->get('game');

		return $qry->result();
	}

	/**
	 * Will change player status based on the passed parameters
	 *
	 * @param 	int
	 * @param 	array
	 * @return 	array
	 */
	public function changePlayerGameBlocked($player_id, $game, $data) {
		$where = "playerId = '" . $player_id . "' AND gameId = '" . $game . "'";
		$this->db->where($where);
		$this->db->update('playergame', $data);
	}

	public function getPlayerGame($player_id) {
		$sql = "SELECT * FROM playergame as pg inner join game as g on pg.gameId = g.gameId where playerId = ? ";
		$query = $this->db->query($sql, array($player_id));
		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	public function getPlayerBlockedGames($player_id) {
		$sql = "SELECT * FROM playergame as pg inner join game as g on pg.gameId = g.gameId where pg.playerId = ? AND blocked IN ('1','2') ";

		$query = $this->db->query($sql, array($player_id));

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

}