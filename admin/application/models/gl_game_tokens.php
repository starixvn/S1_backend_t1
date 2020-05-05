<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Gl_game_tokens, model for GL_API (Global Lottery API)
 * Wrapper of table gl_api_tokens
 * Supports reverse-validation API endpoint for GL_API
 */
class Gl_game_tokens extends BaseModel {

	const login_token_expiry	= 43200;	// Planned token expiry, in seconds

	const tx_token_redis_ttl	= 90;		// REDIS time-to-live, in seconds

	const TOKEN_TYPE_LOGIN = 'lg';
	const TOKEN_TYPE_RECHARGE = 'rc';
	const TOKEN_TYPE_WITHDRAW = 'wd';

	const DEMO_TOKEN = '00000000demotoken_lg';

	const REDIS_HEADER = 'game_gl';

	function __construct() {
		parent::__construct();
	}

	protected $table = "gl_game_tokens";

	public function create_token_login($player_id, $demo_mode = false) {
		return $this->create_token(self::TOKEN_TYPE_LOGIN, [ 'player_id' => $player_id ], $demo_mode);
	}

	public function create_token_recharge($player_id, $secure_id, $amount) {
		$payload_ar = [ 'secure_id' => $secure_id, 'player_id' => $player_id, 'amount' => $amount ];
		return $this->create_token(self::TOKEN_TYPE_RECHARGE, $payload_ar);
	}

	public function create_token_withdraw($player_id, $secure_id, $amount) {
		$payload_ar = [ 'secure_id' => $secure_id, 'player_id' => $player_id, 'amount' => $amount ];
		return $this->create_token(self::TOKEN_TYPE_WITHDRAW, $payload_ar);
	}

	protected function generate_redis_key($token) {
		$redis_key = self::REDIS_HEADER . "_{$token}";

		return $redis_key;
	}

	protected function create_token($type, $payload_ar, $demo_mode = false) {
		$payload = json_encode($payload_ar);
		$token = ($demo_mode == true) ? self::DEMO_TOKEN : $this->generate_token($type);

		$dataset = [
			'token'		=> $token ,
			'active'	=> 1 ,
			'type'		=> $type ,
			'payload'	=> $payload ,
			'created_at'=> $this->utils->getNowForMysql()
		];


		$redis_key = $this->generate_redis_key($token);
		$this->utils->writeRedis($redis_key, $payload, self::tx_token_redis_ttl);
		// $redis_item = $this->utils->readRedis($redis_key);
		// $this->utils->debug_log(__METHOD__, 'test redis writing', [ 'redis_key' => $redis_key, 'redis_item' => $redis_item ]);

		$this->db->insert($this->table, $dataset);

		$this->utils->debug_log(__METHOD__, [ 'type' => $type, 'payload_ar' => $payload_ar, 'demo_mode' => $demo_mode, 'sql' => $this->db->last_query() ]);

		$aff_rows = $this->db->affected_rows();
		$ret = [ 'aff_rows' => $aff_rows ];
		if ($aff_rows > 0) {
			$ret['token'] = $token;
		}

		return $ret;
	}

	public function get_active_tokens_by_player_id($player_id, $type = self::TOKEN_TYPE_LOGIN) {
		return $this->_get_active_tokens_by_player_id(false, $player_id, $type);
	}

	public function count_active_tokens_by_player_id($player_id, $type = self::TOKEN_TYPE_LOGIN) {
		return $this->_get_active_tokens_by_player_id('count', $player_id, $type);
	}

	protected function _get_active_tokens_by_player_id($count_only, $player_id, $type = self::TOKEN_TYPE_LOGIN) {
		$this->db->from($this->table)
			->where("payload->>'$.player_id' = '{$player_id}'", null, false)
			->where('type', $type)
			->where('active !=', 0)
			->order_by('created_at', 'desc')
		;

		if ($count_only){
			$res = $this->db->count_all_results();
		}
		else {
			$res = $this->runMultipleRowArray();
		}

		return $res;
	}

	public function set_token_inactive($id) {
		$updateset = [ 'active' => 0 ];
		$this->db->where('id', $id)
			->update($this->table, $updateset);

		return $this->db->affected_rows();
	}

	/**
	 * Generate token
	 * @param	string	$type	Token type.  Please use class const TOKEN_TYPE_* if possible.
	 * @return	string	token	in the format "{hexstr:40}|{$type}"
	 */
	protected function generate_token($type) {
		$hash_plain = sprintf('%08x:%08x:%s', time(), mt_rand(0x1000000, 0xfffffff), $type);
		$hash = sha1($hash_plain);
		$token = sprintf('%s_%s', $hash, $type);

		return $token;
	}

	public function get_login_creds_by_token($token_user, $demo_mode, $demo_username) {
		if (!$this->is_token_active($token_user, self::TOKEN_TYPE_LOGIN)) {
			return null;
		}

		$isTester = false;

		if ($token_user == self::DEMO_TOKEN) {
			$isTester = true;
			$username = $demo_username;
			$player_id = 0;
		}
		else {
			$row = $this->get_by_token($token_user, self::TOKEN_TYPE_LOGIN);

			if (empty($row)) {
				return null;
			}

			$payload = json_decode($row['payload'], 'array');
			$player_id = $payload['player_id'];

			$this->load->model('player_model');
			$username = $this->player_model->getUsernameById($player_id);

			$isTester = $demo_mode;
		}

		$this->utils->debug_log(__METHOD__, [ 'isTester' => $isTester, 'username' => $username, 'player_id' => $player_id ]);

		$creds = [
			'thirdpartyUid'	=> $username ,
			'nickname'		=> $username ,
			'currency'		=> 'CNY' ,
			'userLang'		=> 'zh-cn' ,
			'country'		=> 'CN' ,
			'lastIp'		=> $this->player_model->getPlayerLogInIp($player_id) ,
			'isTester'		=> $isTester ,
			'player_id'		=> $player_id
		];

		return $creds;
	}

	public function is_token_active($token, $type = null) {
		$token_row = $this->get_by_token($token, $type);

		$res = $token_row['active'] == true;

		return $res;
	}

	public function deactivate_token($token, $type = null) {
		$updateset = [ 'active' => 0 ];

		$this->db->where('token', $token);
		if (!empty($type)) {
			$this->db->where('type', $type);
		}

		$this->db->update($this->table, $updateset);

		$aff_rows = $this->db->affected_rows();
		$ret = [ 'aff_rows' => $aff_rows ];

		return $ret;
	}

	public function token_exists($token, $type = null) {
		$this->db->from($this->table)->where('token', $token);
		if (!empty($type)) {
			$this->db->where('type', $type);
		}

		$cnt = $this->db->count_all_results();

		return $cnt > 0;
	}

	public function get_by_token($token, $type = null) {
		$this->db->from($this->table)->where('token', $token);
		if (!empty($type)) {
			$this->db->where('type', $type);
		}

		$row = $this->runOneRowArray();

		return $row;
	}

	public function get_tx_creds_by_redis($type, $token) {
		$redis_key = $this->generate_redis_key($token);
		$redis_item = $this->utils->readRedis($redis_key);

		$this->utils->debug_log(__METHOD__, [ 'redis_key' => $redis_key, 'redis_item' => $redis_item ]);

		$payload = null;
		if (!empty($redis_item)) {
			$payload = json_decode($redis_item, 'array');
			// Invalidate token
			$this->utils->writeRedis($redis_key, null, self::tx_token_redis_ttl);
		}

		return $payload;
	}

	public function get_tx_creds_by_token($type, $token) {
		$this->utils->debug_log(__METHOD__, [ 'type' => $type, 'token' => $token ]);

		$this->db->from($this->table)
			->where('token', $token)
			->where('type', $type)
		;

		$row = $this->runOneRowArray();

		if (!$row) {
			return null;
		}

		$payload = json_decode($row['payload'], 'array');
		$player_id = $payload['player_id'];

		$this->deactivate_token($token);

		$creds = $payload;

		return $creds;
	}

	public function list_tokens($player_id = null, $limit = 10) {
		if ($limit <= 0) { $limit = 30; }

		$this->db->from($this->table)
			->order_by('id', 'desc')
			// ->order_by('active', 'desc')
			// ->order_by('created_at', 'desc')
			// ->order_by("payload->>'$.player_id'", 'asc')
			->limit($limit);

		if (!empty($player_id)) {
			$this->db->where("payload->>'$.player_id' = '{$player_id}'", null, false);
		}

		$res = $this->runMultipleRowArray();

		return $res;
	}

	public function del_tokens($id_min, $id_max) {
		$this->utils->debug_log(__METHOD__, 'invoking', ['class' => $this->router->class, 'method' => $this->router->method]);
		$this->db->where("id BETWEEN {$id_min} AND {$id_max}", null, false);
		$this->db->delete($this->table);

		$aff_rows = $this->db->affected_rows();

		return $aff_rows;
	}


} // End class Gl_api_model

///END OF FILE///////
