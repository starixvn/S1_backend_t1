<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

require_once dirname(__FILE__) . '/base_model.php';

/**
 * Promo_games
 *
 */
class Promo_games extends BaseModel {

	const RATIO = 1000;
	const SCRATCH_CARD	= 1;
	const LUCKY_WHEEL	= 2;
	const PUZZLE_BOX	= 3;
	const RED_ENVELOPE	= 4;

	protected $table_main = 'promo_game_games';
	protected $table = [
		'deploy_channels'	=> 'promo_game_deploy_channels' ,
		'games'				=> 'promo_game_games' ,
		'game_to_channel'	=> 'promo_game_game_to_channel' ,
		'gametypes'			=> 'promo_game_gametypes' ,
		'player_history'	=> 'promo_game_player_game_history' ,
		'player_to_games'	=> 'promo_game_player_to_games' ,
		'prizes'			=> 'promo_game_prizes' ,
		'promorule_to_games'=> 'promo_game_promorule_to_games' ,
		'resources'			=> 'promo_game_resources' ,
		'themes'			=> 'promo_game_themes' ,
	];
	protected $basedir_resources = "/resources/images/bonus_games/";

	function __construct() {
		parent::__construct();
		$this->table = (object) $this->table;
	}

	/**
	 * Gets combined bonus game entries for bonus game listing
	 * @param	none
	 * @used-by	Marketing_management::bonusGameSettings()
	 * @uses	model: promo_games
	 * @uses	db table: promo_game_games, promo_game_promorule_to_games
	 * @uses	db table: promorules, adminusers
	 * @uses	db table: promo_game_{gametypes,themes}
	 *
	 * @return array 	Array of combined row arrays for all non-deleted bonus games
	 */
	public function get_bonus_games_for_listing() {
		$this->db
			->select('G.id AS promo_game_id, TP.gametype, gamename, desc, TH.theme, G.status')
			->select("GROUP_CONCAT(PR.promorulesId, '||', PR.promoName separator ';;') AS promos", false)
			->select('if (U.username IS NULL, U2.username, U.username) AS updated_by', false)
			->select('if (G.updated_at is null, G.created_at, G.updated_at) as updated_at', false)
			->from("{$this->table->games} AS G")
			->join("{$this->table->gametypes} AS TP", 'G.gametype_id = TP.id')
			->join("{$this->table->themes} AS TH", 'G.theme_id = TH.id')
			->join("{$this->table->promorule_to_games} AS PG", 'G.id = PG.game_id', 'left')
			->join("promorules AS PR", 'PG.promorule_id = PR.promorulesId', 'left')
			->join("adminusers AS U", 'G.updated_by = U.userId', 'left')
			->join("adminusers AS U2", 'G.created_by = U2.userId', 'left')
			->where('G.deleted_at IS NULL', null, false)
			->where('PR.deleted_flag IS NULL', null, false)
			->group_by('G.id')
			->order_by('G.id', 'ASC')
		;

		$res = $this->runMultipleRowArray();

		if (empty($res)) {
			return $res;
		}

		foreach ($res as & $row) {
			$promo_ar = [];
			$promo_items = '';
			if (empty($row['promos'])) {
				$row['promo_ar'] = $promo_ar;
				$row['promo_items'] = $promo_items;
				continue;
			}
			$promos = explode(';;', $row['promos']);
			foreach ($promos as $promo_packed) {
				$promo = explode('||', $promo_packed);
				$promo_ar[] = [ 'id' => $promo[0], 'name' => $promo[1] ];
				$promo_items .= "{$promo[0]}:{$promo[1]}\n";
			}
			$row['promo_ar'] = $promo_ar;
			$row['promo_items'] = $promo_items;
		}

		// $this->utils->debug_log('get_bonus_games_for_listing', 'sql', $this->db->last_query());

		return $res;
	}

	public function get_bonus_game_by_id($game_id) {
		$this->db
			->select('G.id AS promo_game_id, gametype_id, gamename, desc, theme_id')
			->from("{$this->table->games} AS G")
			->where('G.id', $game_id);
		$game_row = $this->runOneRowArray();

		$this->db->from($this->table->prizes)->where('game_id', $game_id)->order_by('sort', 'asc');
		$game_prizes = $this->runMultipleRowArray();

		$this->db->select('channel_id')->from($this->table->game_to_channel)->where('game_id', $game_id);
		$rows = $this->runMultipleRowArray();
		$deploy_channels = $this->convertArrayRowsToArray($rows, 'channel_id');

		// $resources = $this->get_game_resources_by_game_id_or_type($game_id);

		$game_row['prizes'] = $game_prizes;
		$game_row['deploy_channels'] = $deploy_channels;
		// $game_row['resources'] = $resources;

		return $game_row;
	}

	/**
	 * Get elements for game editing (gametypes, themes, deploy_channels) altogether
	 *
	 * @see	Promo_games::_get_bonus_game_edit_elem()
	 * @return [type] [description]
	 */
	public function get_bonus_game_edit_elems() {
		$gametypes = $this->_get_bonus_game_elem_as_tuples($this->table->gametypes, 'gametype', 'need_translate');
		// $themes = $this->_get_bonus_game_elem_as_kv($this->table->themes, 'theme', 'need_translate');
		$themes = $this->_get_bonus_game_elem_as_tuples($this->table->themes, 'theme', 'need_translate');
		$deploy_channels = $this->_get_bonus_game_elem_as_tuples($this->table->deploy_channels, 'channel', 'need_translate');

		$elems = [
			'gametypes'			=> $gametypes ,
			'themes'			=> $themes ,
			'deploy_channels'	=> $deploy_channels
		];

		return $elems;
	}

	/**
	 * Workhorse method for get_bonus_game_edit_elems()
	 *
	 * @param	string	$table			name of db table
	 * @param	string	$field			name of field
	 * @param	bool	$need_translate true then the value will be looked up in language file
	 * @see	Promo_games::get_bonus_game_edit_elems()
	 *
	 * @return	array	array of [ value => translate ] pairs
	 */
	// protected function _get_bonus_game_elem_as_kv($table, $field, $need_translate = false) {
	// 	$this->db->select($field)->from($table)->order_by('id', 'asc');
	// 	$rows = $this->runMultipleRowArray();
	// 	$elem_ar = $this->convertArrayRowsToKV($rows, $field, $field, $need_translate);

	// 	return $elem_ar;
	// }

	protected function _get_bonus_game_elem_as_tuples($table, $field, $need_translate = false) {
		$this->db->select("id, {$field}, enabled")->from($table)->order_by('id', 'asc');
		$items = $this->runMultipleRowArray();
		if (empty($items)) {
			return $items;
		}
		foreach ($items as & $item) {
			$item["{$field}_text"] = lang($item[$field]);
		}

		return $items;
	}

	protected function _update($table, $updateset, $id, $id_column = 'id') {
		$this->db->where($id_column, $id)
			->update($table, $updateset);

		if ($this->db->_error_message()) {
			return false;
		}
		return true;
	}

	public function test_inspect() {
		$res = [];
		$terms = [
			'player_history' => [ 'limit' => 5 , 'json_decode' => 'game_config' ] ,
			'resources' => [ 'order_by' => [ 'gametype_id|asc', 'theme_id|asc', 'res_name|asc' ] ]
		];

		$this->db->from('common_tokens')
			->order_by('id', 'desc')->limit(5)
		;
		$res['tokens'] = $this->runMultipleRowArray();

		foreach ($this->table as $alias => $db_table) {
			$this->db->from($db_table);
			if (isset($terms[$alias])) {
				$term = $terms[$alias];
				if (isset($term['limit'])) {
					$this->db->limit($terms[$alias]['limit']);
				}
				if (isset($term['order_by'])) {
					foreach ($term['order_by'] as $ord) {
						$ordpair = explode('|', $ord);
						$this->db->order_by($ordpair[0], $ordpair[1]);
					}
				}
				else {
					$this->db->order_by('id', 'desc');
				}
			}
			else {
				$this->db->order_by('id', 'asc');
			}

			$res[$alias] = $this->runMultipleRowArray();

			if (!is_array($res[$alias]) || count($res[$alias]) == 0) {
				continue;
			}
			foreach ($res[$alias] as & $row) {
				if (isset($terms[$alias])) {
					$term = $terms[$alias];
					if (isset($term['json_decode'])) {
						$j_field = $term['json_decode'];
						$row[$j_field] = print_r(json_decode($row[$j_field]), 1);
					}
				}
			}
		}

		return $res;
	}

	public function resources_add_extended_info($res_arr) {
		$ret = $res_arr;
		foreach ($ret as & $row) {
			$full_path = null;
			if ($row['type'] == 'url-image') {
				$full_path = $this->get_full_url_for_resource($row['value']);
			}
			$row['fullpath'] = $full_path;
		}

		return $ret;
	}

	public function update_game($game_id, $updateset_game) {
		$res = $this->_update($this->table->games, $updateset_game, $game_id);

		return $res;
	}

	public function update_deploy_channel($game_id, $channels) {
		$this->from($this->table->game_to_channel)
			->where('game_id', $game_id)
			->delete();

		$insert_set = [];
		if (is_array($channels) && count($channels) > 0) {
			foreach ($channels as $channel) {
				$insert_set[] = [
					'game_id'		=> $game_id ,
					'channel_id'	=> $channel ,
					'updated_at'	=> $this->utils->getNowForMysql()
				];
			}

			$this->db->insert_batch($this->table->game_to_channel, $insert_set);
		}

		if ($this->db->_error_message()) {
			return false;
		}
		return true;
	}

	public function update_prizes($game_id, $prizes) {
		// Re-pack post array
		$update_group = [];
		if (is_array($prizes) && count($prizes) > 0) {
			foreach ($prizes as $key => $val_pack) {
				// Reset each array of values into
				$values = array_values($val_pack);
				foreach ($values as $rec_id => $val) {
					if (!isset($update_group[$rec_id])) {
						$update_group[$rec_id] = [
							'game_id'		=> $game_id ,
							'updated_at'	=> $this->utils->getNowForMysql()
						];
					}
					$update_group[$rec_id][$key] = $val;
				}
			}
		}

		// update/delete/insert by re-packed array
		$i = 0;
		foreach ($update_group as $update_item) {
			++$i;
			$prize_id = $update_item['prize_id'];
			$remove_flag = $update_item['remove_flag'];
			unset($update_item['prize_id']);
			unset($update_item['remove_flag']);

			if (empty($update_item['title'])) {
				$update_item['title'] = lang('prize') . " {$i}";
			}
			$update_item['sort'] = $i;

			if (!empty($prize_id)) {
				// Item has prize_id => existing prize
				if (empty($remove_flag)) {
					// Update item
					$this->db->where('id', $prize_id)
						->update($this->table->prizes, $update_item);
				}
				else {
					// Delete item
					$this->db->from($this->table->prizes)
						->where('id', $prize_id)->delete();
				}
			}
			else {
				// Item has no prize_id => new prize
				if (empty($remove_flag)) {
					// Insert new prize
					$this->db->insert($this->table->prizes, $update_item);
				}
				else {
					// Do not insert for delete_flag true (do nothing)
				}
			}
		}

		if ($this->db->_error_message()) {
			return false;
		}

		return true;
	}

	public function create_game($insertset_game) {
		$this->db->insert($this->table->games, $insertset_game);

		try {
			$game_id = null;
			if ($this->db->_error_message()) {
				throw new Exception("Error inserting game: {$this->db->_error_message()}");
			}

			$game_id = $this->db->insert_id();

			return $game_id;
		}
		catch (Exception $ex) {
			$this->utils->debug_log('Promo_games::create_game()', $ex->getMessage());
			return -1;
		}
	}

	public function remove_game($game_id) {
		try {
			$this->db->where('id', $game_id)
				->set('deleted_at', $this->utils->getNowForMysql())
				->set('deleted_by', $this->authentication->getUserId())
				->update($this->table->games);
			if ($this->db->_error_message()) {
				throw new Exception("db Error: {$this->db->_error_message()}");
			}
			if ($this->db->affected_rows() < 1) {
				throw new Exception("Affected rows: {$this->db->affected_rows()}");
			}

			return true;
		}
		catch (Expection $ex) {
			$this->utils->debug_log('remove_game', $ex->getMessage());
			return false;
		}
	}

	/*
	public function remove_prize($game_id, $prize_id) {
		try {
			$this->db->from($this->table->prizes)
				->where('id', $prize_id)->where('game_id', $game_id);
			$ver_res = $this->runExistsResult();

			if (!$ver_res) {
				throw new Exception("Malformed prize_id {$prize_id} for game_id {$game_id}");
			}

			$this->db->from($this->table->prizes)
				->where('id', $prize_id)->where('game_id', $game_id)
				->delete();
			if ($this->db->_error_message()) {
				throw new Exception("db Error: {$this->db->_error_message()}");
			}
			if ($this->db->affected_rows() < 1) {
				throw new Exception("Affected rows: {$this->db->affected_rows()}");
			}

			return true;
		}
		catch (Exception $ex) {
			$this->utils->debug_log('remove_prize', $ex->getMessage());
			return false;
		}
	}
	*/

	public function enable_disable_game($game_id, $operation) {
		try {
			if (!in_array($operation, ['enable', 'disable'])) {
				throw new Exception('Malformed value for operation');
			}

			$updateset = ['status' => 'enabled'];
			if ($operation == 'disable') {
				$updateset = ['status' => 'disabled'];
			}
			$this->db->where('id', $game_id)
				->update($this->table->games, $updateset);

			if ($this->db->_error_message()) {
				throw new Exception("db Error: {$this->db->_error_message()}");
			}
			if ($this->db->affected_rows() < 1) {
				throw new Exception("Affected rows: {$this->db->affected_rows()}");
			}

			return true;
		}
		catch (Exception $ex) {
			$this->utils->debug_log('*able_prize', $ex->getMessage());
			return false;
		}
	}

	/**
	 * Returns dataset for 'Bonus Game' select on 'Promo Rules Setting' page
	 *
	 * @return	JSON	JSON object of
	 *                   { (id) => { id (int), gamename (string), prizes (object) }}
	 *                   where prizes: object of
	 *                   { (prize_type) => prize_type (string), amount (decimal string) }
	 */
	public function get_avail_games_for_promorules() {
		$this->db->select('G.id, gamename, prize_type, sum(P.amount) as amount')
			->from("{$this->table->games} AS G")
			->join("{$this->table->prizes} AS P", "G.id = P.game_id", 'left')
			->where('G.deleted_at IS NULL', false, false)
			->where('G.deleted_by IS NULL', false, false)
			->where('G.status', 'enabled')
			->group_by('P.game_id, P.prize_type')
		;

		$res_raw = $this->runMultipleRowArray();

		if (!is_array($res_raw) || count($res_raw) == 0) {
			return $res_raw;
		}

		$res = [];
		foreach ($res_raw as $row) {
			$id = $row['id'];
			if (!isset($res[$id])) {
				$res[$id] = [
					'id'		=> $id ,
					'gamename'	=> $row['gamename'] ,
					'prizes'	=> [
						'cash'		=> [ 'has_prize' => false] ,
						'vip_exp'	=> [ 'has_prize' => false] ,
					]
				];
			}

			$prize_type = $row['prize_type'];
			if ($prize_type == 'nothing') continue;

			$res[$id]['prizes'][$prize_type] = [
				'has_prize'		=> $row['amount'] > 0
			];
		}

		return $res;
	}

	/**
	 * Gets the single game related to specified promo rule, when preparing edit form for promo rule
	 * Game-to-promorule: one-to-many relationship; one game - multiple promorules
	 * @param	int		$promorulesId	the promorulesId as in table promorules
	 * @uses	db table: promo_game_promorule_to_games
	 * @used-by	promorules_management_module::editPromoRule()
	 *
	 * @return	array 	row array of promo_game_promorule_to_games
	 */
	public function get_promorule_game($promorulesId, $with_game_entry = null) {
		$this->db->from("{$this->table->promorule_to_games} AS PG")
			->where('promorule_id', $promorulesId);

		if (!empty($with_game_entry)) {
			$this->db->join("{$this->table->games} AS G", "PG.game_id = G.id")
				->select('PG.*, G.gamename, G.gametype_id, G.theme_id, G.desc')
			;
		}

		$res = $this->runOneRowArray();

		return $res;
	}

	/**
	 * Creates or updates promorule-game relationship when saving promo rule
	 * Game-to-promorule: one-to-many relationship; one game - multiple promorules
	 * @param	int		$promorulesId	the promorulesId as in table promorules
	 * @uses	db table: promo_game_promorule_to_games
	 * @used-by	promorules_management_module::preparePromo()
	 *
	 * @return	insert id (int) or existing id (int) on success; bool false otherwise
	 */
	public function create_or_update_promorule_game($entry) {
		$promorule_id = $entry['promorule_id'];

		$existing_row = $this->get_promorule_game($promorule_id);

		$this->utils->debug_log('existing_row', $existing_row);
		$this->utils->debug_log('last-query', $this->db->last_query());

		if (empty($existing_row)) {
			$this->db->insert($this->table->promorule_to_games, $entry);
			$id = $this->db->insert_id();
		}
		else {
			$this->db->where('promorule_id', $promorule_id)
				->update($this->table->promorule_to_games, $entry);
			$id = $existing_row['id'];
		}

		if ($this->db->_error_message()) {
			return false;
		}
		return $id;
	}

	/**
	 * Removes promorule-game relationship for given promo rule
	 * 	when promo rule no more uses promo game in bonus release condition
	 * @param	int		$promorulesId	the promorulesId as in table promorules
	 * @uses	db table: promo_game_promorule_to_games
	 * @used-by	promorules_management_module::preparePromo()
	 *
	 * @return	count of affected rows (int) on success; bool false otherwise
	 */
	public function remove_promorule_game($promorule_id) {
		$this->db->from($this->table->promorule_to_games)
			->where('promorule_id', $promorule_id)
			->delete();

		if ($this->db->_error_message()) {
			return false;
		}
		return $this->db->affected_rows();
	}


	// public function get_promorule_game_by_promorule_id($promorule_id) {
	// 	$this->db->from($this->table->promorule_to_games)
	// 		->where('promorule_id', $promorule_id);

	// 	$res = $this->runOneRowArray();

	// 	return $res;
	// }

	/**
	 * Get available playable games for player
	 * Invoked by t1t API request_play_game_list()
	 *
	 * @param	int		$player_id	== player.id
	 * @param	string	$format		any of ('old', 'full', 'default'), defaults to 'default'.
	 * @uses	db table: promo_game_player_to_games		(The main table)
	 * @uses	db table: promo_game_games, promo_game_gametypes, promorules, promocmssettings
	 *
	 * @return	array 	simple array of gametype strings (if $simp == true), or array of tuples
	 */
	public function get_avail_games_for_player($player_id, $format = 'default') {
		$this->utils->debug_log('get_avail_games_for_player', 'format', $format);
		$this->db->from("{$this->table->player_to_games} AS PG")
			->join("{$this->table->gametypes} AS T", "PG.gametype_id = T.id", 'left')
			->join("{$this->table->games} AS G", "PG.game_id = G.id", 'left')
			->where('player_id', $player_id)
			->where('play_rounds >', 0)
			->where('G.status <>', 'disabled')
			->order_by('T.gametype ASC, PG.id ASC')
		;

		switch ($format) {
			case 'short' :
				// As in old API specs provided by Polo.app
				$this->db->select('T.gametype')->group_by('T.gametype');
				$rows = $this->runMultipleRowArray();
				$res = $this->convertArrayRowsToArray($rows, 'gametype');
				break;
			case 'full' :
				$this->db
					->join("promocmssetting AS PC", "PG.promorules_id = PC.promoId", 'left')
					->join("promorules AS PR", "PG.promorules_id = PR.promorulesId", 'left')
					->select('PG.id AS player_to_game_id, PG.game_id, G.gamename, T.gametype, PG.play_rounds')
					->select('IF(PC.promoName IS NULL, PR.promoName, PC.promoName) AS promo_name', false)
					->select('PR.promorulesId, PC.promoCmsSettingId', false)
				;
				$res = $this->runMultipleRowArray();
				break;
			case 'default' :
			default :
				$this->db
					->join("{$this->table->resources} AS R", "G.gametype_id = R.gametype_id AND R.res_name = 'icon' ", 'left')
					->select('PG.id AS player_game_id, G.gamename, T.gametype, R.value AS game_icon_url')
				;
				$res = $this->runMultipleRowArray();
				if (!is_array($res) || count($res) == 0) {
					break;
				}
				foreach ($res as & $row) {
					$row['game_icon_url'] = $this->get_full_url_for_resource($row['game_icon_url']);
				}
				break;
		}


		return $res;
	}

	public function get_full_url_for_resource($value) {
		$protocol = 'http';
		$host = $this->input->server('HTTP_HOST');
		$host = preg_replace("/^player\./i", 'admin.', $host);
		$dir_image_base = "{$protocol}://{$host}{$this->basedir_resources}";

		$fullurl = $dir_image_base . $value;

		return $fullurl;
	}

	public function get_game_resources_by_gametype_and_theme($gametype_id, $theme_id) {
		$this->db->from($this->table->resources)
			->where('gametype_id', $gametype_id)
			->where('theme_id', $theme_id)
			->or_where('theme_id', 0)
			->or_where('theme_id IS NULL', false, false)
			->where('game_id', 0)
		;

		$rows = $this->runMultipleRowArray();

		$this->utils->debug_log('promo_games::get_game_res', [ 'query' => $this->db->last_query() ] );

		$protocol = 'http';
		// $dir_image_base = "{$protocol}://{$this->input->server('HTTP_HOST')}/resources/images/bonus_games/";
		// $dir_image_base = "{$protocol}://{$this->input->server('HTTP_HOST')}{$this->basedir_resources}";

		$res = [];
		if (!is_array($rows) || count($rows) == 0) {
			return $res;
		}

		foreach ($rows as $row) {
			$res_name	= $row['res_name'];
			$value		= $row['value'];

			$out = print_r($res, 1);

			if (is_null($row['index'])) {
				// $res[$row['res_name']] = $dir_image_base . $value;
				$res[$row['res_name']] = $this->get_full_url_for_resource($value);
			}
			else {
				if (!isset($res[$res_name])) {
					$res[$res_name] = [];
				}
				// $res[$res_name][$row['index']] = $dir_image_base . $value;
				$res[$res_name][$row['index']] = $this->get_full_url_for_resource($value);
			}
		}

		$failsafe_check = [ 'masking', 'bg', 'skin', 'arrow', 'button', 'bg' ];

		foreach ($failsafe_check as $fs_field) {
			if (!isset($res[$fs_field]))
				{ $res[$fs_field] = ''; }
		}

		return $res;
	}

	public function resources_export_json() {
		$this->db
			->select('game_id , gametype_id, theme_id, res_name, index, value, type')
			->from($this->table->resources)
			->order_by('gametype_id, theme_id, res_name, index')
		;

		$rows = $this->runMultipleRowArray();

		$ret = json_encode($rows);

		return $ret;
	}

	public function resources_import_json($arg) {
		// $imp_ar = json_decode($arg);
		foreach ($arg as $row) {
			$this->db->insert($this->table->resources, $row);
		}

		return true;
	}

	/**
	 * Returns request promotion ID
	 * @used_by	t1t_game_module::t1t_game_request_bonus()
	 * @return	string	Currently 32-digit hex string, generated using php uniqid()
	 */
	public function generate_request_promotion_id() {
		// return sprintf('%08x%04x', time(), mt_rand(0, 0xffff));
		return _REQUEST_ID;
	}

	/**
	 * Draw one from available prizes by the result of a fair dice as reward
	 * Details:
	 * 	- A int-valued, (100 * self::RATIO) faced dice is emulated with mt_rand
	 * 		(php implementation of Mersenne Twister)
	 *  - A table of intervals will be built with 'prob' field of each prize
	 *  	multiplied by self::RATIO, forcing all intervals int-bounded
	 *  - After casting the dice, the value is matched against the table of intervals
	 *  	If the value falls in one of the intervals, corresponding prize would be returned as the reward.
	 *  - If no hit, the method would return null -
	 *  	* If sum of 'prob' field of all $prizes items is not 100.00, null
	 *  	return could be seen as a 'no-hit' condition, but this is discouraged.
	 *  	* Operator should plan their games with prob field of prizes summing
	 *  	up to exactly 100.00, as the bonus game setting page implies.
	 *
	 * @param	array 	$prizes		array of prizes, each of them should contain a 'prob' field
	 * @uses	const: self::RATIO, exaggeration ratio of prob value for each prize
	 * @return	mixed	a member of $prizes array, or null (error)
	 */
	public function fair_draw($prizes) {
		$debuglevel = 2;
		// Construct table of intervals
		$hit_interval = [];
		$prev_round = 0;
		foreach ($prizes as $key => $prize) {
			$prob = intval($prize['prob'] * self::RATIO);
			$lbound = $prev_round + 1;
			$rbound = $lbound + $prob - 1;
			$hit_interval[$key] = [ 'lbound' => $lbound, 'round' => $rbound ];
			$prev_round = $rbound;
		}
		if ($debuglevel > 1) $this->utils->debug_log('fair_draw', 'table of intervals', $hit_interval);

		// Cast the dice
		$dice_min = 1;
		$dice_max = 100 * self::RATIO;
		$dice_val = mt_rand($dice_min, $dice_max);
		if ($debuglevel > 0) $this->utils->debug_log('fair_draw', 'dice value', $dice_val, 'dice range', [ $dice_min, $dice_max ]);

		$hit_id = -1;
		foreach ($hit_interval as $prize_id => $intr) {
			if ($dice_val >= $intr['lbound'] && $dice_val <= $intr['round']) {
				if ($debuglevel > 0) $this->utils->debug_log('fair_draw', 'hit_interval', $intr);
				$hit_id = $prize_id;
				break;
			}
		}

		if ($hit_id == -1) {
			if ($debuglevel > 0) $this->utils->debug_log('fair_draw', 'error: no interval hit');
			return null;
		}
		else {
			return $prizes[$hit_id];
		}
	}

	/**
	 * Insert into promo_games_player_game_history
	 * @param	array 	$history_entry
	 * @return	mixed	insert_id (int) on success; false on failure.
	 */
	public function create_history_entry($history_entry) {
		$this->db->insert($this->table->player_history, $history_entry);

		if ($this->db->_error_message()) {
			return false;
		}
		else {
			return $this->db->insert_id();
		}
	}

	public function remove_history_entry_between($begin, $end) {
		if ($begin > $end) {
			$tmp = $end;
			$end = $begin;
			$begin = $tmp;
		}
		$this->db->from($this->table->player_history)
			->where('id >=', $begin)
			->where('id <=', $end)
			->delete();

		if ($this->db->_error_message()) {
			return false;
		}
		else {
			return $this->db->affected_rows();
		}
	}

	/**
	 * Reads a row from table promo_game_player_to_games by id.
	 * @param	int		$id			= promo_game_player_to_games.id
	 * @return	array 	row array of table promo_game_player_to_games
	 */
	public function get_player_game_by_id($id) {
		$this->db->from($this->table->player_to_games)
			->where('id', $id)
		;

		$res = $this->runOneRowArray();
		if ($this->db->_error_message()) {
			return false;
		}
		else {
			return $res;
		}
	}

	/**
	 * Grant play rounds to player for game specified in promorule_to_games table
	 * @param	int		$promorule_id	== promorules.promorulesId
	 * @param	int		$player_id		== player.playerId
	 * @return	mixed	-1 if cannot comply; false if insert failed; int on success.
	 */
	public function player_game_grant_from_promorule($promorule_id, $player_id) {
		$promorule_game = $this->get_promorule_game($promorule_id, 'with_game_entry');

		if (empty($promorule_game)) {
			return -1;
		}

		$new_p2g_entry = [
			'status'		=> 'enabled' ,
			'player_id'		=> $player_id ,
			'game_id'		=> $promorule_game['game_id'] ,
			'promorule_id'	=> $promorule_id ,
			'gametype_id'	=> $promorule_game['gametype_id'] ,
			'play_rounds'	=> $promorule_game['play_rounds'] ,
			'created_at'	=> $this->utils->getNowForMysql()
		];

		$res = $this->create_player_to_game_entry($new_p2g_entry);

		return $res;
	}

	// public function player_game_get_play_rounds($player_id, $game_id, $promorule_id) {
	// 	$this->db->from($this->table->player_to_games)
	// 		->where('player_id', $player_id)
	// 		->where('game_id', $game_id)
	// 		->where('promorule_id', $promorule_id)
	// 	;

	// 	$pgentry = $this->runOneRowOneField('play_rounds');

	// 	return $pgentry;
	// }

	public function player_game_decrease_play_rounds($player_game_id) {
		// $play_rounds = $this->player_game_get_play_rounds($player_id, $game_id, $promorule_id);
		$player_game_entry = $this->get_player_game_by_id($player_game_id);
		$play_rounds = $player_game_entry['play_rounds'];

		if ($play_rounds <= 0) {
			return -1;
		}

		$this->db->where('id', $player_game_id)
			->set('play_rounds', 'play_rounds - 1', false)
			->update($this->table->player_to_games)
		;

		if ($this->db->_error_message()) {
			return false;
		}
		else {
			return $this->db->affected_rows();
		}
	}

	/**
	 * Converts game_type_id to corresponding string by table promo_game_gametypes
	 * @param	int		$game_type_id		== promo_game_gametypes.id
	 * @return	string	The gametype string, or false if id unknown.
	 */
	public function game_type_to_string($game_type_id) {
		$this->db->from($this->table->gametypes)
			->where('id', $game_type_id)
			->select('gametype')
		;

		$res = $this->runOneRowOneField('gametype');
		if ($this->db->_error_message()) {
			return false;
		}
		else {
			return $res;
		}
	}

	/**
	 * Check if external_request_id is already present in table promo_game_player_game_history
	 * @param	int 	$external_request_id	the external request id provided by app
	 * @return	bool	true if present; false if not.
	 */
	public function is_external_request_id_present($external_request_id) {
		$this->db->from($this->table->player_history)
			->where('external_request_id', $external_request_id)
			->select('created_at')
		;

		$res = $this->runOneRowOneField('created_at');

		if (!empty($res)) {
			return true;
		}
		return false;
	}

	/**
	 * Create entry in table bonus_game_player_to_games
	 * @param	array 	$entry		Prepared row array for insertion
	 * @return	mixed	insert id (int) if successful; bool false otherwise.
	 */
	public function create_player_to_game_entry($entry) {
		$entry['created_at'] = $this->utils->getNowForMysql();
		$entry['updated_at'] = $this->utils->getNowForMysql();
		$this->db->insert($this->table->player_to_games, $entry);

		if ($this->db->_error_message()) {
			return false;
		}
		else {
			return $this->db->insert_id();
		}
	}

	/**
	 * Remove entry in table bonus_game_player_to_games
	 * @param  [type] $id_ar [description]
	 * @return [type]        [description]
	 */
	public function remove_player_to_game_entry($id_ar) {
		$this->db->from($this->table->player_to_games)
			->where_in('id', $id_ar)
			->delete();

		if ($this->db->_error_message()) {
			return false;
		}
		else {
			return $this->db->affected_rows();
		}
	}

	public function create_resources_entry($entry) {
		$entry['updated_at'] = $this->utils->getNowForMysql();
		$this->db->insert($this->table->resources, $entry);

		if ($this->db->_error_message()) {
			return false;
		}
		else {
			return $this->db->insert_id();
		}
	}

	public function remove_resources_entry($id_ar) {
		$this->db->from($this->table->resources)
			->where_in('id', $id_ar)
			->delete();

		if ($this->db->_error_message()) {
			return false;
		}
		else {
			return $this->db->affected_rows();
		}
	}

	public function update_resources_entry($id, $updateset) {
		foreach ($updateset as $key => & $val) {
			if ($val === '') { $val = null; }
		}

		$updateset['updated_at'] = $this->utils->getNowForMysql();

		$this->db->where('id', $id)
			->update($this->table->resources, $updateset);

		if ($this->db->_error_message()) {
			return false;
		}
		else {
			return $this->db->affected_rows();
		}
	}



	/**
	 * Read player game history entries for API response
	 * @param	int		$player_id	the playerId
	 * @param	int		$type		1 for closed games; 2 for unclosed games; other values for all games
	 * @return	array of row arrays
	 */
	public function get_player_history_list($player_id, $type, $count = 10) {
		$this->db->from("{$this->table->player_history} AS H")
			->join("{$this->table->games} AS G", 'H.game_id = G.id', 'left')
			->join("{$this->table->gametypes} AS T", 'G.gametype_id = T.id', 'left')
			->join("{$this->table->player_to_games} AS PG", 'H.player_to_game_id = PG.id', 'left')
			->select("T.gametype AS game_type")
			->select("IF(H.status <> 'started' OR PG.play_rounds IS NULL OR PG.play_rounds <= 0, true, false) AS is_done", false)
			->select("request_promotion_id, external_request_id, game_config AS game_config_json")
			->select("NULL AS game_para", false)
			->where('H.player_id', $player_id)
			->order_by('H.id', 'desc')
			->limit($count)
		;

		switch ($type) {
			case 1 :
				$this->db->where("IF(H.status <> 'started' OR PG.play_rounds IS NULL OR PG.play_rounds <= 0, true, false) = true", false, false);
				break;
			case 2 :
				$this->db->where("IF(H.status <> 'started' OR PG.play_rounds IS NULL OR PG.play_rounds <= 0, true, false) = false", false, false);
				break;
			default:
				break;
		}

		$res = $this->runMultipleRowArray();

		$this->utils->debug_log('get_player_history_list', $this->db->last_query());

		if (!is_array($res) || count($res) == 0) {
			return $res;
		}

		foreach ($res as & $row) {
			$ret_json = json_decode($row['game_config_json']);

			$row['game_config'] = $ret_json;
			if (property_exists($ret_json, 'game_config')) {
				$row['game_config'] = $ret_json->game_config;
			}

			$row['background_skin_url'] = property_exists($ret_json, 'background_skin_url') ? $ret_json->background_skin_url : null;

			$row['background_animation_type'] = property_exists($ret_json, 'background_animation_type') ? $ret_json->background_animation_type : null;

			unset($row['game_config_json']);
		}

		return $res;
	}

	/**
	 * Read specific player history item by request_promotion_id
	 * @param	string	$request_promotion_id
	 * @return	array 	row array of promo_game_player_history
	 */
	public function get_player_history_by_req_id($request_promotion_id) {
		$this->db->from($this->table->player_history)
			->where('request_promotion_id', $request_promotion_id);

		$res = $this->runOneRowArray();

		return $res;
	}

	public function player_history_close_entry($id, $realize = null, $notes = null) {
		$updateset_close = [
			'status'     => $realize ? 'closed' : 'blocked' ,
			// 'status'     => 'closed' ,
			'updated_at' => $this->utils->getNowForMysql() ,
			'notes'      => $notes
		];

		if (!empty($realize)) {
			$updateset_close['realized_at'] = $this->utils->getNowForMysql();
		}

		return $this->_update($this->table->player_history, $updateset_close, $id);
	}

	// public function player_history_update_only_notes($id, $notes) {
	// 	$updateset = [
	// 		'updated_at' => $this->utils->getNowForMysql() ,
	// 		'notes'      => $notes
	// 	];

	// 	return $this->_update($this->table->player_history, $updateset, $id);
	// }

	/**
	 * Amount of all realized reward within a promorule/game combo (across players)
	 *
	 * @param	string	$prize_type		only 'cash', 'vip_exp' supported so far
	 * @param	int		$promorule_id	promorulesId
	 * @param	int		$game_id		game_id
	 * @return	decimal		Amount of all realized rewards
	 */
	public function get_sum_bonus_by_game_and_promorule($prize_type, $promorule_id, $game_id) {
		$this->db->from($this->table->player_history)
			->select("SUM(bonus_amount) AS amount")
			->where('status', 'closed')
			->where('bonus_type', $prize_type)
			->where('promorule_id', $promorule_id)
			->where('game_id', $game_id)
			// ->group_by('bonus_type, promorule_id, game_id')
		;

		$res = $this->runOneRowOneField('amount');

		return $res;
	}

	// public function player_history_reset_reward_to_nothing($player_history_id, $notes = null) {
 // 		if (empty($notes)) {
 // 			$notes = 'Reward blocked.';
 // 		}
	// 	$updateset_reset = [
	// 		'status' => 'blocked' ,
	// 		'notes'  => $notes ,
	// 		'bonus_type' => 'nothing' ,
	// 		'bonus_amount' => 0 ,
	// 		'updated_at' => $this->utils->getNowForMysql()
	// 	];

	// 	return $this->_update($this->table->player_history, $updateset_reset, $player_history_id);
	// }

	public function get_all_linked_promorules_for_select() {
		$this->db->from("{$this->table->promorule_to_games} AS PG")
			->join("promorules AS R", "PG.promorule_id = R.promorulesId")
			->select('PG.promorule_id AS id, R.promoName AS label')
			->group_by('PG.promorule_id')
			->order_by('promorule_id', 'desc')
		;

		$res = $this->runMultipleRowArray();

		return $res;
	}

	public function get_all_gametypes_for_select() {
		$this->db->from($this->table->gametypes)
			->select('id, gametype')
			->where('enabled', 1)
			->order_by('id', 'asc')
		;

		$res = $this->runMultipleRowArray();

		if (empty($res)) {
			return $res;
		}

		foreach ($res as & $row) {
			$row['gametype_text'] = lang($row['gametype']);
		}

		return $res;
	}

	public function get_all_bonus_types_for_select() {
		return [
			[ 'id' => 'cash'	, 'title' => lang('Cash Bonus') ] ,
			[ 'id' => 'vip_exp'	, 'title' => lang('VIP Experience') ] ,
			[ 'id' => 'nothing'	, 'title' => lang('Nothing') ] ,
		];
	}

	public function player_history_get_recent_winnings($count = 50) {
		$this->db->from("{$this->table->player_history} AS H")
			->join("{$this->table->games} AS G", "H.game_id = G.id", "left")
			->join("player AS P", "H.player_id = P.playerId", "left")
			->select('P.username, G.gamename, G.gametype_id, H.game_config')
			->where('H.status', 'closed')
			->where('H.bonus_amount >', '0')
			->order_by('IF(H.realized_at IS NULL, H.updated_at, realized_at )', 'desc', false)
			->limit($count)
		;

		$rows = $this->runMultipleRowArray();

		$messages = [];
		if (!empty($rows)) {
			foreach ($rows as & $row) {
				$game_config = json_decode($row['game_config'], 'as_array');
				if (isset($game_config['game_config'])) {
					$game_config = $game_config['game_config'];
				}
				$prize = null;
				switch ($row['gametype_id']) {
					case 1 : // scratchcard
						$prize = $game_config['list'][0]['reward_msg'];
						break;
					case 2 : // luckywheel
						$prize = $game_config['reward_msg'];
						break;
					default :
						break;
				}
				$messages[] = sprintf("%s %s %s %s %s",
					$row['username'], lang('in'), $row['gamename'], lang('won'), $prize);
			}
		}

		$res = [ 'message' => $messages ];

		return $res;
	}

	// =================================================================================

} // End class Promo_games

/* End of file promo_games.php */
/* Location: ./application/models/promo_games.php */