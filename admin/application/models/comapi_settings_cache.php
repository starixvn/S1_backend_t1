<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

require_once dirname(__FILE__) . '/base_model.php';

class Comapi_settings_cache extends BaseModel {

	protected $table_main			= 'comapi_settings_cache';
	protected $fg_api_baseurl		= 'http://admin.gamegateway.t1t.games/game_description/getFrontendGames';
	protected $fg_api_cache_prefix	= 'LISTGAME_FEGAME';

	protected $new_fgapi_baseurl	= '/pub/get_frontend_games';


	protected $CACHE_SEP = ':';
	protected $cache_lifetime_master = 259200;

	const SETTINGS_PREFIX_LISTGAME = 'LISTGAME_CF';
	const SETTINGS_GAME_PROVIDERS_CONCEAL = 'GAME_PROVIDERS_CONCEAL';

	public $last_error = [ 'code' => null, 'mesg' => null ];

	function __construct() {
		parent::__construct();
		// $this->table = (object) $this->table;
	}

	/**
	 * Determine query URL with given args for frontend games A{}
	 * @param	int		$platform_id	same as system ID in list of Game API
	 * @param	string	$game_type		game_type_type reported by FG API.
	 * @return	array 	[ query_url, cache_key ]
	 */
	protected function fg_query_url($platform_id, $game_type) {
		$query_path = [ $this->fg_api_baseurl ];
		$cache_path = [ $this->fg_api_cache_prefix ];
		if (!empty($platform_id)) {
			$query_path[] = $platform_id;
			$cache_path[] = $platform_id;
		}

		if (!empty($game_type)) {
			$query_path[] = $game_type;
			$cache_path[] = $game_type;
		}

		$query_url = implode('/', $query_path);
		$cache_key = implode($this->CACHE_SEP, $cache_path);

		return [ $query_url, $cache_key ];
	}

	/**
	 * Set property last_error
	 * @param	Exception	$ex		The exception object caught in a try/catch block
	 * @return  none
	 */
	// protected function last_error_set($ex) {
	// 	$this->last_error = [ 'code' => $ex->getCode(), 'mesg' => $ex->getMessage() ];
	// }

	/**
	 * Clear property last_error
	 * @return	none
	 */
	// protected function last_error_reset() {
	// 	$this->last_error = [ 'code' => null, 'mesg' => null ];
	// }

	/**
	 * Returns frontend game API query result
	 * @param	int		$platform_id	same as system ID in list of Game API
	 * @param	string	$game_type		game_type_type reported by FG API.
	 * @return	mixed	decrypted json array
	//  */
	// public function fg_api_query($platform_id = null, $game_type = null) {
	// 	try {
	// 		list($query_url, $cache_key) = $this->fg_query_url($platform_id, $game_type);
	// 		if ($this->cache_exists($cache_key) && !$this->cache_too_old($cache_key)) {
	// 			$cache_item = $this->cache_get($cache_key);
	// 			$resp = $cache_item['response'];
	// 			// $this->utils->debug_log('fg_api_query', 'resp', $resp);
	// 			$resp_decrypted = json_decode($resp, 'as array');
	// 			return $resp_decrypted;
	// 		}

	// 		$resp = file_get_contents($query_url);
	// 		$resp_decrypted = json_decode($resp, 'as array');
	// 		$resp_clean = json_encode($resp_decrypted);
	// 		$this->cache_set($cache_key, $query_url, $resp_clean);
	// 		return $resp_decrypted;
	// 	}
	// 	catch (Exception $ex) {
	// 		$this->last_error_set($ex);
	// 		return null;
	// 	}
	// }

	// public function fg_api_query_type($platform_id, $game_type) {
	// 	try {
	// 		$res_platform = $this->fg_api_query($platform_id);

	// 		if (isset($res_platform['Game List'])) {
	// 			$game_list = [];
	// 			foreach ($res_platform['Game List'] as $key => $row) {
	// 				if ($row['game_type_code'] == $game_type) {
	// 					$game_list[] = $row;
	// 				}
	// 			}
	// 			$res_platform['Game List'] = $game_list;
	// 		}

	// 		return $res_platform;

	// 	}
	// 	catch (Exception $ex) {
	// 		$this->last_error_set($ex);
	// 		return null;
	// 	}
	// }

	/**
	 * Inserts or updates given cache item
	 * @param	string	$cache_key	The cache key
	 * @param	string	$url       	The query URL
	 * @param	JSON	$resp		Response text
	 * @return	none
	 */
	public function cache_set($cache_key, $url, $resp, $info = null) {
		$dataset = [
			'name'		=> $cache_key ,
			'is_cache'	=> true ,
			'info'		=> is_scalar($info) ? $info : json_encode($info) ,
			'url'		=> $url ,
			'response'	=> $resp ,
			'last_update' => $this->utils->getNowForMysql()
		];

		if ($this->cache_exists($cache_key)) {
			// cache exists, update
			$this->db->where('name', $cache_key)
				->update($this->table_main, $dataset);
		}
		else {
			// cache does not exist, insert
			$this->db->insert($this->table_main, $dataset);
		}
	}

	public function settings_set($settings_key, $info, $value, $response = null) {
		$dataset = [
			'name'		=> $settings_key ,
			'is_cache'	=> false ,
			'info'		=> is_scalar($info) ? $info : json_encode($info) ,
			'value'		=> $value ,
			'response'	=> $response ,
			'last_update' => $this->utils->getNowForMysql()
		];

		if ($this->cache_exists($settings_key)) {
			// cache exists, update
			$this->db->where('name', $settings_key)
				->update($this->table_main, $dataset);
		}
		else {
			// cache does not exist, insert
			$this->db->insert($this->table_main, $dataset);
		}
	}

	/**
	 * Check if given cache_key exists
	 * @param	string	$cache_key	The cache key
	 * @return	bool	true if exists; false otherwise
	 */
	public function cache_exists($cache_key) {
		$this->db->from($this->table_main)
			->where('name', $cache_key);

		$res = $this->runExistsResult();

		return $res;
	}

	/**
	 * Check if cache item is too old.
	 * @param	string	$cache_key	The cache key
	 * @return	bool	true if too old; false if not
	 */
	public function cache_too_old($cache_key) {
		$this->db->from($this->table_main)
			->select('is_cache')
			->select('TIMESTAMPDIFF(SECOND, last_update, NOW()) AS fix_age')
			->where('name', $cache_key);

		$res = $this->runOneRow();

		$cache_lifetime = $this->cache_lifetime_master;

		return ($res->is_cache && $res->fix_age > $cache_lifetime);
	}

	/**
	 * Returns cache item specified by given cache_key
	 * @param  	string	$cache_key	The cache key
	 * @return	array 	Row array from table
	 */
	public function cache_get($cache_key) {
		$this->db->from($this->table_main)
			->where('name', $cache_key);

		$res = $this->runOneRowArray();

		return $res;
	}

	public function settings_get($settings_key) {
		$this->db->from($this->table_main)
			->where('name', $settings_key);


		$res = $this->runOneRowArray();
		// $this->utils->debug_log('settings_get', 'settings_key', $settings_key, 'entry', $res);
		if (!empty($res)) {
			$res['value'] = json_decode($res['value'], 'as array');
		}

		return $res;
	}

	public function settings_group_get($settings_prefix) {
		$this->db->from($this->table_main)
			->like('name', $settings_prefix, 'after');

		$res = $this->runMultipleRowArray();

		return $res;
	}

	public function settings_get_list_game($lg_key) {
		$combined_key = strtoupper(self::SETTINGS_PREFIX_LISTGAME . $this->CACHE_SEP . $lg_key);
		$res = $this->settings_get($combined_key);
        if(is_array($res) && array_key_exists('value', $res) && $res !== null) {
            return $res['value'];
        }
        return null;
	}

	public function game_player_center_launch_url($platform_id, $url) {
		$launch_url = $this->utils->getSystemUrl('player', $url);
		$gp_https = $this->settings_get_list_game('GAME_PROVIDERS_USE_HTTPS');
		if (isset($gp_https[$platform_id]) && preg_match('/^http:/', $launch_url)) {
			$launch_url = str_replace('http', 'https', $launch_url);
		}

		return $launch_url;
	}

	protected $REDIS_DEFAULT_LIFETIME = 300;

	/**
	 * Save key-value pair to cache
	 * @param	string	$key	Key of cache
	 * @param	mixed	$val	Value to cache
	 * @return	none
	 */
	protected function redis_save($key, $val, $ttl = null) {
		if (empty($ttl)) {
			$ttl = $this->REDIS_DEFAULT_LIFETIME;
		}
		$json = json_encode($val);
		$this->utils->writeRedis($key, $json, $ttl);
	}

	/**
	 * Loads value from cache
	 * @param	string	$key	Key of cache
	 * @return	mixed	Cached value, or null if cache is expired
	 */
	protected function redis_load($key) {
		$json = $this->utils->readRedis($key);
		$val = json_decode($json, 'as_array');

		return $val;
	}

	/**
	 * Clear a key in cache; sets value to null, ttl to 1s
	 * @param	string	$key	Key of cache
	 * @return	none
	 */
	protected function redis_clear($key) {
		$this->utils->writeRedis($key, null, 1);
	}

	/**
	 * Get active game APIs on system, using no fgapi
	 * Supersedes new_fgapi_list_game_platforms()
	 * OGP-13359
	 * @return	array 	Array of [ id, system_name, system_code ]
	 */
	public function new_real_list_game_platforms() {
		$this->load->model([ 'external_system' ]);

		$active_game_apis = $this->external_system->getAllActiveSytemGameApi();

		$ret = $this->utils->array_select_fields($active_game_apis, [ 'id', 'system_name', 'system_code' ]);

		return $ret;
	}

	/**
	 * @deprecated 	DISUSED; Superseded by new_real_list_game_platforms()
	 * Returns list of configured game platforms on site
	 * @param	bool	$refresh	true to refresh, otherwise use cache if possible.  Default false.
	 * @return	array  	array of [ id, system_name, system_code ]
	 */
	// public function new_fgapi_list_game_platforms($refresh = false) {
	// 	$cache_key = 'comapi_fgapi_game_platforms';

	// 	$gplist = $this->redis_load($cache_key);
	// 	// $this->utils->debug_log(__METHOD__, 'gplist from cache', $gplist);
	// 	if (empty($gplist) || $refresh == true) {
	// 		$this->load->library('game_list_lib');
	// 		$glres = $this->game_list_lib->getFrontEndGames();

	// 		$gplist = [];
	// 		foreach ($glres['available_game_providers'] as $row) {
	// 			$gplist[] = [
	// 				'id'			=> $row['game_platform_id'] ,
	// 				'system_name'	=> $row['complete_name'] ,
	// 				'system_code'	=> $row['game_provider']
	// 			];
	// 		}

	// 		$gp_conceal = $this->settings_get_list_game('GAME_PROVIDERS_CONCEAL');

	// 		$this->utils->debug_log('game platforms', $gplist, 'gp_conceal', $gp_conceal);

	// 		foreach ($gplist as $key => & $row) {
	// 			if (isset($gp_conceal[$row['id']])) {
	// 				unset($gplist[$key]);
	// 			}
	// 		}

	// 		uasort($gplist, function($a, $b) {
	// 			return $a['id'] > $b['id'] ? 1 : -1;
	// 		});

	// 		$this->redis_save($cache_key, $gplist);
	// 	}

	// 	$gplist_clean = array_values($gplist);

	// 	return $gplist_clean;
	// } // End function new_fgapi_list_game_platforms()

	protected function new_fgapi_platform_id_valid($platform_id) {
		$platform_id_valid = false;
		// $gplist = $this->new_fgapi_list_game_platforms();
		$gplist = $this->new_real_list_game_platforms();
		if (is_array($gplist)) {
			$platform_id_match = array_filter($gplist, function ($el) use ($platform_id, & $platform_id_valid) {
				return $el['id'] == $platform_id;
			});
			$platform_id_valid = !empty($platform_id_match);
		}

		return $platform_id_valid;
	}

	/**
	 * Returns game lobby links or game types for given game platform
	 * @param	int		$platform_id	Game platform ID
	 * @param	bool	$refresh		true to refresh, otherwise use cache if possible.  Default false.
	 * @return	array 	array of [ success, code, mesg, result ]
	 */
	public function new_fgapi_list_games_by_platform($platform_id, $refresh = false, $lang = null) {
		$CACHE_PREFIX = 'comapi_fgapi_game_type_';
		$cache_key = "{$CACHE_PREFIX}{$platform_id}_{$lang}";

		try {
			$cached_content = $this->redis_load($cache_key);
			// $this->utils->debug_log(__METHOD__, 'cached_content', $cached_content);
			if (!empty($cached_content) && $refresh == false) {
				$res = $cached_content;
				throw new Exception('Using cached result', -1);
			}

			if ($this->new_fgapi_platform_id_valid($platform_id) == false) {
				$res = [
					'game_lobby' => false ,
					'game_types' => null
				];

				throw new Exception('Platform ID invalid', Api_common::CODE_LG_PLATFORM_ID_INVALID);
			}

			$this->load->library('game_list_lib');
			$gl_res = $this->game_list_lib->getFrontEndGames($platform_id, null, 'all');

			$gl_note = $this->utils->safeGetArray($gl_res, 'Note');

			// If game platform has lobby
			if (strpos($gl_note, 'game lobby') !== false) {
				$res = [
					'game_lobby' => true ,
					'game_types' => $gl_res['game_launch_url']
				];

				throw new Exception('Game platform has lobby', -2);
			}

			// No data at all
			if (empty($gl_res['game_list'])) {
				$res = [
					'game_lobby' => false ,
					'game_types' => null
				];

				throw new Exception('Platform does not support game list', Api_common::CODE_LG_GAMETYPE_QUERY_NOT_SUPPORTED);
			}

			// Or no lobby
			$game_types = [];
			foreach ($gl_res['game_list'] as $g) {
				$g_type = $g['game_type_code'];
				if (!isset($game_types[$g_type])) {
					$game_types[$g_type] = 1;
				}
			}

			$game_types = array_keys($game_types);

			if (count($game_types) == 1 && empty(reset($game_types))) {
				$game_types = [ 'all' ];
			}

			$res = [
				'game_lobby' => false ,
				'game_types' => $game_types ,
				'game_types_lang' => $this->game_types_to_lang_text($game_types, $lang)
			];

			throw new Exception('Game list fetched successfully', -3);
		}
		catch (Exception $ex) {
			// $this->utils->debug_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'mesg' => $ex->getMessage() ]);
			$ex_code = $ex->getCode();
			$success = $ex_code <= 0;
			if ($success) {
				$this->redis_save($cache_key, $res);
			}

			$retval = [
				'success'	=> $success ,
				'code'		=> $ex_code ,
				'mesg'		=> $ex->getMessage() ,
				'result'	=> $res
			];
		}
		finally {
			return $retval;
		}

	} // End of function new_fgapi_list_games_by_platform()

	/**
	 * Translates array of game_types to assoc array of localized lang text
	 * OGP-13545
	 * @see		new_fgapi_list_games_by_platform()
	 * @param	array 	$game_types		array of game_types
	 * @return	array 	assoc array of [ game_type => game_type_lang_text ]
	 */
	protected function game_types_to_lang_text($game_types, $force_lang = null) {
		$gt_map = [
			"all"				=> 'game_cats.all'		,
			"arcade"			=> 'tags.Arcade'		,
			"card_games"		=> 'tags.CardGames'		,
			"fishing_game"		=> 'tags.FishingGame'	,
			"fixed_odds"		=> 'tags.FixedOdds'		,
			"live_dealer"		=> 'tags.LiveDealer'	,
			"lottery"			=> 'tags.Lottery'		,
			"others"			=> 'Others'				,
			"progressives"		=> 'tags.Progressives'	,
			"slots"				=> 'tags.Slots'			,
			"table_and_cards"	=> 'tags.TableandCards'	,
			"table_games"		=> 'tags.TableGames'	,
			"unknown"			=> 'tags.Unknown'		,
			"video_poker"		=> 'cms.videopokers'	,
      		"scratch_card"		=> 'scratchcard'		,
		];

		$ret = [];
		foreach ($game_types as $gt) {
			$gt_key = isset($gt_map[$gt]) ? $gt_map[$gt] : $gt;
			$ret[$gt] = lang($gt_key, $force_lang);
		}

		return $ret;
	}

	/**
	 * Translates a single game_type string to localized lang text
	 * OGP-13545
	 * OBSOLETE
	 * @param	string	$game_type	game_type
	 * @return	string
	 */
	// protected function _game_type_to_lang_text($game_type, $force_lang = 2) {
	// }

	protected function new_fgapi_game_type_valid($platform_id, $game_type) {
		$gt_res = $this->new_fgapi_list_games_by_platform($platform_id);

		$this->utils->debug_log(__METHOD__, 'gt_res', $gt_res);

		if ($gt_res['success'] == false) {
			return [ 'valid' => false, 'code' => $gt_res['code'], 'mesg' => $gt_res['mesg'] ];
		}
		else if ($gt_res['result']['game_lobby'] == true) {
			return [ 'valid' => false, 'code' => Api_common::CODE_LG_GAME_PLATFORM_USES_LOBBY, 'mesg' => 'Game platform has lobby, use listGamesByPlatform for lobby links instead' ];
		}
		else {
			$game_types = $gt_res['result']['game_types'];
			// Only one type, or type = null for all games
			if (count($game_types) == 1) {
				return [ 'valid' => true, 'code' => 0, 'game_type_null' => true ];
			}
			// multiple types, game_type hits
			else if (in_array($game_type, $game_types)) {
				return [ 'valid' => true, 'code' => 0, 'game_type_null' => false ];
			}
			// multiple types, game_type not hit
			else {
				return [ 'valid' => false, 'code' => Api_common::CODE_LG_GAMETYPE_INVALID, 'mesg' => 'Game_type value invalid' ];
			}
		}

		return false;
	}

	/**
	 * Return games by given platform and game type
	 * @param	int		$platform_id	Game platform ID
	 * @param	string	$game_type		Game type
	 * @param	bool	$refresh		true to refresh, otherwise use cache if possible.  Default false.
	 * @return	array 	array of games
	 */
	public function new_fgapi_list_games_by_platform_gametype($platform_id, $game_type = null, $refresh = false, $limit = 50, $offset = 0) {
		try {
			if ($this->new_fgapi_platform_id_valid($platform_id) == false) {
				$res = [ 'games' => null ];
				throw new Exception('Platform ID invalid', Api_common::CODE_LG_PLATFORM_ID_INVALID);
			}

			$game_type_check = $this->new_fgapi_game_type_valid($platform_id, $game_type);
			if ($game_type_check['valid'] == false) {
				throw new Exception($game_type_check['mesg'], $game_type_check['code']);
			}

			if ($game_type_check['game_type_null'] == true) {
				$game_type = null;
			}

			$this->load->library('game_list_lib');
			$gl_res = $this->game_list_lib->getFrontEndGames($platform_id, $game_type, 'all');
			$game_list = $gl_res['game_list'];

			$game_list_output = [];
			foreach ($game_list as $gl_row) {
				 $row = $this->utils->array_select_fields($gl_row, [ 'game_type_code', 'game_name', 'game_name_cn', 'in_flash', 'in_html5', 'game_launch_url', 'image_path', 'provider_name' ]);

				 if (is_array($row['game_launch_url'])) {
				 	unset($row['game_launch_url']['sample']);
				 }

				 if ($this->utils->getConfig('comapi_listgame_images_use_rel_path')) {
				 	$row['image_path'] = $this->convert_image_url_to_relative($row['image_path']);
				 }

				 $game_list_output[] = $row;
			}

			$game_count_total = count($game_list_output);
			$game_list_output = array_slice($game_list_output, $offset, $limit);

			$result = [
				'game_count_total' => $game_count_total,
				'game_list' => $game_list_output
			];

			$list_begin = $offset + 1;
			$list_end = $offset + $limit;
			if ($list_end > $game_count_total) { $list_end = $game_count_total; }

			$retval = [
				'success'	=> true ,
				'code'		=> 0 ,
				'mesg'		=> "Game list fetched successfully.  Listing {$list_begin} - {$list_end} of {$game_count_total} games." ,
				'result'	=> $result
			];
		}
		catch (Exception $ex) {
			$retval = [
				'success'	=> false ,
				'code'		=> $ex->getCode() ,
				'mesg'		=> $ex->getMessage() ,
				'result'	=> null
			];
		}
		finally {
			return $retval;
		}
	} // End function new_fgapi_list_games_by_platform_gametype()

	/**
	 * Converts URL's in image_path to relative path
	 * @param	array 	$image_path		image_path in each item returned by frontend game API, assoc array of [ 'lang' => 'url' ]
	 * @return	array
	 */
	protected function convert_image_url_to_relative($image_path) {
		$url_prefix = $this->utils->getConfig('comapi_listgame_images_use_rel_path_prefix');
		foreach ($image_path as $key => & $im_url) {
			$im_url = preg_replace('/^(https?:\/\/(\w+\.)+\w+)/', $url_prefix, $im_url);
		}

		return $image_path;
	}

} // End of class Comapi_settings_cache
