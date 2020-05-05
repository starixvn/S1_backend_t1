<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 *
 * NEVER USE
 *
 * MOVE TO common_token
 */
class Player_login_token extends BaseModel {

	function __construct() {
		parent::__construct();
		$this->load->helper('string');
	}

	protected $tableName = "player_login_token";

	/**
	 * new login token
	 *
	 * @param 	int playerId
	 * @return 	int last insert id
	 */
	public function newLoginToken($playerId) {
		$token = random_string('unique');
		$this->db->insert($this->tableName, array("player_id" => $playerId, "token" => $token, "created_at" => $this->getNowForMysql()));
		return array($this->db->insert_id(), $token);
	}
	
	public function getPlayerId($token) {
		$query = $this->db->where('token', $token)->get($this->tableName);
			$row = $query->row();
		$player_id = $row->player_id;
		$query = $this->db->where('player_id', $player_id)->order_by('id', 'desc')->limit(1)->get($this->tableName);
		$row = $query->row();
		return $row->token == $token ? $player_id : NULL;
	}
}

///END OF FILE///////