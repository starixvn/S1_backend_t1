<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class External_common_tokens extends BaseModel {

	const T_ACTIVE = "ACTIVE";
	const T_INACTIVE = "INACTIVE";
	const T_ERROR = "ERROR";

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "external_common_tokens";

	public function getExternalToken($player_id, $game_platform_id) {
		$this->db->from($this->tableName);
		$this->db->where('player_id', $player_id);
		$this->db->where('game_platform_id', $game_platform_id);
		return $this->runOneRowOneField('token');
	}

	public function addPlayerToken($player_id, $token, $gamePlatformId) {
		$this->db->from($this->tableName)->where('player_id', $player_id)->where('game_platform_id', $gamePlatformId);
		$exists = $this->runExistsResult();

		$data = array(
			"player_id" => $player_id,
			"token" => $token,
			"game_platform_id" => $gamePlatformId,
			"created_at" => $this->getNowForMysql(),
			"status" => self::T_ACTIVE
		);
		if (!$exists) {
			$this->db->insert($this->tableName, $data);
		} else {
			$this->db->update($this->tableName, $data);
		}
	}

	public function getPlayerIdByExternalToken($token, $game_platform_id) {
		$this->db->from($this->tableName);
		$this->db->where('token', $token);
		$this->db->where('game_platform_id', $game_platform_id);
		return $this->runOneRowOneField('player_id');
	}

	public function setPlayerToken($player_id, $token, $gamePlatformId) {

		$this->db->from($this->tableName)->where('player_id', $player_id)->where('game_platform_id', $gamePlatformId);
		$exists = $this->runExistsResult();

		if (!$exists) {
			$data = array(
				"player_id" => $player_id,
				"token" => $token,
				"game_platform_id" => $gamePlatformId,
				"created_at" => $this->getNowForMysql()
			);
			$this->db->insert($this->tableName, $data);
		} else {
			$this->db->set('token', $token);
			$this->db->where('player_id', $player_id);
			$this->db->where('game_platform_id', $gamePlatformId);
			$this->db->update($this->tableName);
		}
	}

	public function updatePlayerExternalTokenStatus($playerId, $token, $status){

		$update_data = array(
			'status' => $status
		);

		$this->db->where('player_id', $playerId);
		$this->db->where('token', $token);

		$this->db->update($this->tableName, $update_data);

		return $this->db->affected_rows() > 0;
	}

}

///END OF FILE///////
