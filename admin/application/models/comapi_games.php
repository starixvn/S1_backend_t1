<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

require_once dirname(__FILE__) . '/base_model.php';

class Comapi_games extends BaseModel {

	const EVENT_GAME_START	= 'game_start';
	const EVENT_GAME_RESULT	= 'game_result';
	const EVENT_GAME_ENDED	= 'game_ended';

	const SETTING_PLAYER_DAILY_SIGNIN_LIMIT	= 'player_daily_signin_limit';
	const SETTING_GAME_SESS_TIMEOUT_SEC		= 'game_sess_timeout_sec';
	const SETTING_GAME_WITHDRAW_BET_TIMES	= 'game_withdraw_bet_times';

	const GAME_SESS_NOT_OPEN			= 1;
	const GAME_SESS_MISMATCH_SITE		= 2;
	const GAME_SESS_MISMATCH_GAME_CODE	= 3;
	const GAME_SESS_MISMATCH_PLAYER_ID	= 4;
	const GAME_SESS_CORRUPT				= 5;
	const GAME_SESS_TIMEOUT				= 6;

	const EVENTS_GET_DEFAULT_LIMIT		= 10;

	const GAME_DEFAULT_PROMO_ID			= 'comapi_games';
	const GAME_DEFAULT_WITHDRAW_BET_TIMES = 5.0;

	protected $game_event_order_defined = [ self::EVENT_GAME_START, self::EVENT_GAME_RESULT, self::EVENT_GAME_ENDED ];
	protected $game_event_order;

	protected $table_main = 'comapi_games';



	protected $table = [
		'games'				=> 'comapi_games' ,
		'settings'			=> 'comapi_game_settings' ,
		'events'			=> 'comapi_game_events'
	];

	function __construct() {
		parent::__construct();
		$this->table = (object) $this->table;
		$this->game_event_order = array_flip($this->game_event_order_defined);
	}

	public function settings_get($site, $game_code, $key) {
		$this->db->from($this->table->settings)
			->where('site', $site)
			->where('game_code', $game_code)
			->where('key', $key)
		;

		$res = $this->runOneRowOneField('value');

		$res = json_decode($res);

		return $res;
	}

	public function settings_get_all($site, $game_code = null, $key = null) {
		$this->db->from($this->table->settings)
			->where('site', $site)
		;
		if (!empty($game_code)) { $this->db->where('game_code', $game_code); }
		if (!empty($key)) { $this->db->where('key', $key); }

		$res = $this->runMultipleRowArray();

		if (!empty($res)) {
			foreach ($res as & $row) {
				$row['value'] = json_decode($row['value']);
			}
		}

		return $res;
	}

	public function settings_set($site, $game_code, $key, $value) {
		$val_json = json_encode($value);
		$dataset = [
			'value' => $val_json,
			'created_at' => $this->utils->getNowForMysql()
		];
		$this->db->where('site', $site)
			->where('game_code', $game_code)
			->where('key', $key)
		;
		$oldvalue = $this->settings_get($site, $game_code, $key);
		if (empty($oldvalue)) {
			$dataset = array_merge($dataset, [
				'site' 		=> $site ,
				'game_code' => $game_code ,
				'key'		=> $key
			]);
			$this->db->insert($this->table->settings, $dataset);
		}
		else {
			$this->db->update($this->table->settings, $dataset);
		}

		return $this->db->affected_rows();
	}

	public function settings_remove($site, $game_code = null, $key = null) {
		$this->db->where('site', $site)
		;
		if (!empty($game_code)) { $this->db->where('game_code', $game_code); }
		if (!empty($key)) { $this->db->where('key', $key); }
		$this->db->delete($this->table->settings);
	}


	public function events_log($site, $game_code, $player_id, $event, $game_sess_id = null, $data = null, $datetime = null) {
		$insertset = [
			'site'			=> $site ,
			'game_code'		=> $game_code ,
			'player_id'		=> $player_id ,
			'event'			=> $event ,
			'game_sess_id'	=> $game_sess_id ,
			'data'			=> empty($data) ? $data : json_encode($data) ,
			'created_at'	=> empty($datetime) ? $this->utils->getNowForMysql() : $datetime
		];

		$this->db->insert($this->table->events, $insertset);

		return $this->db->affected_rows();
	}

	/**
	 * Returns game events by given conditions
	 * @param  string	$site			The site ident
	 * @param  int		$game_code   	The game code
	 * @param  int		$player_id   	playerId
	 * @param  string	$event       	Event.  Suggested to use self::EVENT_* constants.
	 * @param  string	$game_sess_id	Game session ID
	 * @param  mixed	$fields			Fields
	 * @param  int		$offset
	 * @param  int		$limit
	 * @param  string	$date_from
	 * @param  string	$date_to
	 * @param  mixed	$order_by
	 * @return array
	 */
	public function events_get($site, $game_code, $player_id,
		$event = null, $game_sess_id = null, $fields = '',
		$offset = -1, $limit = -1, $date_from = null, $date_to = null,
		$order_by = null) {

		// Basic search conditions
		$this->db->from($this->table->events)
			->select($fields)
			->where('site', $site)
			->where('game_code', $game_code)
			->where('player_id', $player_id)
		;
		// Apply event, game_sess_id if available
		if (!empty($event))			{ $this->db->where('event', $event); }
		if (!empty($game_sess_id))	{ $this->db->where('game_sess_id', $game_sess_id); }

		// Apply offset if available
		if ($offset > -1) { $this->db->offset($offset); }

		// Apply dates if available
		if (!empty($date_from) && !empty($date_to)) {
			$this->db->where("created_at BETWEEN '$date_from' AND '$date_to'", null, null);
		}

		// Apply limit or use default
		if ($limit > 0)	{
			$this->db->limit($limit);
		}
		else {
			$this->db->limit(self::EVENTS_GET_DEFAULT_LIMIT);
		}

		// Apply order_by or use default
		if (!empty($order_by)) {
			$this->db->order_by($order_by);
		}
		else {
			$this->db->order_by('created_at DESC');
		}

		// The main event
		$res = $this->runMultipleRowArray();

		$this->utils->debug_log('Comapi_games::events_get', ['sql' => $this->db->last_query() ]);

		// Decode json in data fields
		if (isset($res[0]['data'])) {
			foreach ($res as & $row) {
				$row['data'] = json_decode($row['data']);
			}
		}

		return $res;
	}

	public function events_count_today($site, $game_code, $player_id, $event, $game_sess_id = null) {
		$today = $this->utils->getTodayStringRange();
		return $this->events_count($site, $game_code, $player_id, $event, $game_sess_id, $today[0], $today[1]);
	}

	public function events_count($site, $game_code, $player_id, $event, $game_sess_id, $datetime_start, $datetime_end) {
		$this->db->from($this->table->events)
			->where('site', $site)
			->where('game_code', $game_code)
			->where('player_id', $player_id)
			->where('event', $event)
			->where("created_at BETWEEN '$datetime_start' AND '$datetime_end'", null, null)
			->select(' count(*) as event_count')
		;

		if (!empty($game_sess_id)) {
			$this->db->where('game_sess_id', $game_sess_id);
		}

		$res = $this->runOneRowOneField('event_count');

		return $res;
	}

	public function events_get_by_game_sess_id($game_sess_id) {
		$this->db->from($this->table->events)
			->where('game_sess_id', $game_sess_id)
		;

		$res = $this->runMultipleRowArray();

		return $res;
	}

	public function game_sess_id_generate($site, $game_code, $player_id) {
		$sess_id = sprintf('%07x-%4s-%05x-%4s-%07x-%12s',
			crc32($site) % 0xf000000 + intval($game_code),
			$this->utils->generateRandomCode(4) ,
			$player_id,
			$this->utils->generateRandomCode(4) ,
			time() % 0x10000000,
			$this->utils->generateRandomCode(12)
		);

		return $sess_id;
	}

	// public function game_sess_id_valid_and_open($game_sess_id, $site, $game_code, $player_id) {
	// 	$rows = $this->events_get_by_game_sess_id();
	// 	try {
	// 		// Check if all sess entries belong to same site/game/player tuple
	// 		if (count($rows) > 1) {
	// 			$corrupt = false;
	// 			$check_0 = "{$rows[0]['site']}-{$rows[0]['game_code']}-{$rows[0]['player_id']}";
	// 			for ($i = 1; $i < count($rows); ++$i) {
	// 				$check_i = "{$rows[$i]['site']}-{$rows[$i]['game_code']}-{$rows[$i]['player_id']}";
	// 				if ($check_i != $check_0) {
	// 					$corrupt = true;
	// 					break;
	// 				}
	// 			}
	// 			if ($corrupt == true) {
	// 				throw new Exception('game_sess_id corrupt', 1);
	// 			}
	// 		}

	// 		// Now that the sess integrity is secured
	// 		$row0 = $rows[0];

	// 		if ($row['site'] != $site)				{ throw new Exception('Site mismatch', 2); }
	// 		if ($row['game_code'] != $game_code)	{ throw new Exception('game_code mismatch', 3); }
	// 		if ($row['player_id'] != $player_id)	{ throw new Exception('player_id mismatch', 4); }

	// 		// Now that the sess belongs to specified site/game/player tuple
	// 		$states = array_column($rows, 'event');
	// 		if (in_array(self::EVENT_GAME_ENDED, $states) || in_array(self::EVENT_GAME_RESULT, $states)) {
	// 			throw new Exception('Game has ended', 5);
	// 		}
	// 	}
	// 	catch(Exception $ex) {

	// 	}
	// 	finally {

	// 	}

	// }

	/**
	 * Check if game session is valid and open
	 * @param	string	$game_sess_id	The game session ID
	 * @param	string	$site        	The site ident
	 * @param	int		$game_code		The game code
	 * @param	int		$player_id		playerId
	 * @return 	array 	[ code, mesg ]
	 *                   code = 0 on success, otherwise failure
	 */
	public function game_sess_valid_and_open($game_sess_id, $site, $game_code, $player_id) {

		$ret = [ 'exec incomplete', 0xf ];

		try {

			$rows = $this->events_get_by_game_sess_id($game_sess_id);
			// As each game_sess_id is unique and each game should only has one game_start event
			// If a game_sess_id points to more than one events, it must be beyond game_start in this simplified scheme
			if (count($rows) > 1) {
				throw new Exception('Game session is not open', self::GAME_SESS_NOT_OPEN);
			}

			// Then start checking
			$row = $rows[0];
			$created_at = strtotime($row['created_at']);
			$age = time() - $created_at;

			// Check site/game/player tuple
			if ($row['site'] != $site)				{ throw new Exception('Site mismatch', self::GAME_SESS_MISMATCH_SITE); }
			if ($row['game_code'] != $game_code)	{ throw new Exception('game_code mismatch', self::GAME_SESS_MISMATCH_GAME_CODE); }
			if ($row['player_id'] != $player_id)	{ throw new Exception('player_id mismatch', self::GAME_SESS_MISMATCH_PLAYER_ID); }

			// Now that the sess belongs to specified site/game/player tuple
			if (!isset($this->game_event_order[$row['event']]) || $this->game_event_order[$row['event']] > $this->game_event_order[self::EVENT_GAME_START]) {
				throw new Exception('Session corrupt', self::GAME_SESS_CORRUPT);
			}

			// Check for timeout
			$game_sess_timeout = $this->settings_get($site, $game_code, Comapi_games::SETTING_GAME_SESS_TIMEOUT_SEC);
			if ($age > $game_sess_timeout) {
				throw new Exception('Session timeout', self::GAME_SESS_TIMEOUT);
			}

			$ret =  [ 'Game sess id valid and open', 0 ];
		}
		catch(Exception $ex) {
			$this->utils->debug_log('game_sess_id invalid', [ 'error' => [ $ex->getCode(), $ex->getMessage() ] , 'game_sess_id' => $game_sess_id, 'site-game-player' => "$site-$game_code-$player_id", 'created_at' => isset($row) ? $row['created_at'] : null, 'age' => isset($age) ? $age : null, 'timeout' => isset($game_sess_timeout) ? $game_sess_timeout : null ]);

			$ret = [ "Game sess id invalid", $ex->getCode() ];
			// Don't reveal too much details
			// $ret = [ "Game sess id invalid: {$ex->getMessage()}", $ex->getCode() ];
		}
		finally {
			return [ 'mesg' => $ret[0], 'code' => $ret[1] ];
		}

	}

	/**
	 * Check if game belongs to specified site
	 * @param	string	$site        	The site ident
	 * @param	int		$game_code		The game code
	 * @return	bool	true if game is valid and unique; false otherwise
	 */
	public function game_is_valid($site, $game_code) {
		$this->db->from($this->table->games)
			->where('site', $site)
			->where('game_code', $game_code)
			->select('count(*) as match_count')
		;

		$res = $this->runOneRowOneField('match_count');

		return ($res == 1) ? true : false;
	}

	/**
	 * Read details of a single game
	 * @param	string	$site        	The site ident
	 * @param	int		$game_code		The game code
	 * @return 	array 	row array of table comapi_games
	 */
	public function game_get_single($site, $game_code) {
		$this->db->from($this->table->games)
			->where('site', $site)
			->where('game_code', $game_code)
		;

		$res = $this->runOneRowArray();

		if (!empty($res)) {
			$res['notes'] = json_decode($res['notes']);
		}

		return $res;
	}

	public function game_get_all($site = null, $game_code = null) {
		$this->db->from($this->table->games);
		if (!empty($site)) { $this->db->where('site', $site); }
		if (!empty($game_code)) { $this->db->where('game_code', $game_code); }

		$res = $this->runMultipleRowArray();

		// $this->utils->debug_log('game_get', 'sql', $this->db->last_query());

		// $this->utils->debug_log('game_get', 'res', $res);

		if (!empty($res)) {
			foreach ($res as & $row) {
				$row['notes'] = json_decode($row['notes']);
			}
		}

		return $res;
	}

	/**
	 * Get game title from game_code
	 * @param	string	$site        	The site ident
	 * @param	int		$game_code		The game code
	 * @return	string	The game title
	 */
	public function game_get_title($site, $game_code) {
		$res = $this->game_get_single($site, $game_code);

		if (empty($res)) {
			return null;
		}

		return $res['title'];
	}

	public function game_remove_all($site, $game_code = null) {
		$this->db->where('site', $site);
		if (!empty($game_code)) { $this->db->where('game_code', $game_code); }

		$this->db->delete($this->table->games);
	}

	public function game_set($site, $game_code, $title, $notes) {
		$insertset = [
			'site' 		=> $site ,
			'game_code' => $game_code ,
			'title'		=> $title ,
			'notes'		=> json_encode($notes) ,
			'created_at'	=> $this->utils->getNowForMysql()
		];

		$this->db->insert($this->table->games, $insertset);
	}
}
