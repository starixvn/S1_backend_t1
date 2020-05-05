<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

require_once dirname(__FILE__) . '/base_model.php';

/**
 * Model for OLE777 reward system wager/userinfo operations
 *
 * @version	1.0		Initially built with Codeigniter db driver
 * @version 1.1 	1st rewritten with PDO,		12/27/2018 - 1/04/2019
 * @version 1.2 	2nd rewritten with MySQLi,	 1/10/2019
 *
 * @author		Rupert Chen <rupert.php.tw@tripleonetech.net>
 * @copyright	Tripleonetech Inc 2018-19
 */
class Ole_reward_model extends BaseModel {
	protected $table_users	= 'UserInfo';
	protected $table_wager	= 'WagerData';
	protected $table_local_wager	= 'ole777_reward_wagers';
	protected $table_local_syncs	= 'ole777_reward_syncs';
	protected $db7;

	// protected $json_sbe_to_ole = '{"58":"1","888":"5","72":"6","20":"7","26":"8","6":"12","1":{"8":"13","*":14},"8":"16","42":"18","*":"999"}';
	// protected $json_ole_to_text = '{"1":"Oneworks Sportsbook","2":"EA Live Casino","5":"Betsoft Slots","6":"AG Live Casino","7":"OPUS Live Casino","8":"KG","10":"Oneworks Live Casino","12":"MG Slots","13":"Playtech Slots","14":"Playtech Live Casino","16":"BBIN Live Casino","17":"EAN2 Live Casino","18":"QTECH","19":"Df Star","20":"Og","999":"Others"}';

	protected $json_ole_to_text = null, $json_sbe_to_ole = null;
	protected $sbe2ole = null, $ole2text = null;

	protected $TEST_MODE = true;

	const SYNC_SYSTEM_MARKER = '10000101';
	const SYNC_FLAG_FULL_SYNC = 1073741824;

	function __construct() {
		parent::__construct();
		$this->ole7_conf = $this->utils->getConfig('ole777_reward_conf');
		$this->db_conn_ole777();
		$this->setup_ole_product_id_map();
		$this->ole7_conf = $this->utils->getConfig('ole777_reward_conf');
		if (isset($this->ole7_conf['test_mode']) && $this->ole7_conf['test_mode'] == false) {
			$this->TEST_MODE = false;
		}
	}

	public function insert_marker_full_sync($marker_time = null) {
		$marker_time = empty($marker_time) ? date('c') : date('c', strtotime($marker_time));
		$insertset = [
			'Date'					=> self::SYNC_SYSTEM_MARKER ,
			'ProductID'				=> self::SYNC_FLAG_FULL_SYNC ,
			'confirmed_for_sync'	=> null ,
			'sync_datetime'			=> $marker_time ,
			'notes'					=> "Initial sync committed at {$marker_time}"
		];
		$this->db->insert($this->table_local_syncs, $insertset);
	}

	public function check_marker_full_sync() {
		$this->db->from($this->table_local_syncs)
			->where([ 'Date' => self::SYNC_SYSTEM_MARKER ,
				'ProductID' => self::SYNC_FLAG_FULL_SYNC ])
			->select([ 'sync_datetime', 'notes' ])
		;

		$res = $this->db->get()->first_row('array');

		$res['exists'] = !empty($res);

		return $res;
	}

	/**
	 * Connect to remote OLE777 database by creds specified in config.
	 * Rewritten:	1/04/2019	with PDO
	 * 				1/10/2019 	with MySQLi
	 * @return	bool	true if db is ready, false otherwise.
	 */
	public function db_conn_ole777() {
		$db7cf = $this->ole7_conf['db'];
		if (empty($db7cf)) {
			$this->utils->debug_log(__METHOD__, 'db not ready, aborting');
			return false;
		}

		if (!empty($this->db7)) {
			$this->utils->debug_log(__METHOD__, 'db seems already loaded');
			return true;
		}

		// $this->db7 = $this->load->database($db7cf, true);
		// try {
		// 	$db7_connstr = "mysql:dbname={$db7cf['database']};host={$db7cf['hostname']}";
		// 	// $this->db7 = new PDO($db7_connstr, $db7cf['username'], $db7cf['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
		// 	$this->db7 = new PDO($db7_connstr, $db7cf['username'], $db7cf['password']);
		// } catch (PDOException $ex) {
		// 	$this->utils->debug_log(__METHOD__, "DB7 connection failure: {$ex->getMessage()}");
		// }

		$this->db7 = new mysqli($db7cf['hostname'], $db7cf['username'], $db7cf['password'], $db7cf['database']);

		if ($this->db7->connect_error) {
			$this->utils->debug_log(__METHOD__, "DB7 connection failure",  [ 'errno' => $this->db7->connect_errno, 'error' => $this->db7->connect_error ]);
		}

		// Workaround for UTF-8 player RealNames
		$this->db7->query('SET NAMES UTF8');


		$this->utils->debug_log(__METHOD__, 'db7 host info', $this->db7->host_info);
		return true;

	}

	/**
	 * Bulk insert prepared userinfo records
	 * @param	array 	$userinfo	Prepared userinfo records.
	 * @see		Ole_reward_model::full_update_prepare_userinfo()
	 * @see		ole_reward_lib::userinfo_full_update()
	 * @return	none
	 */
	// public function userinfo_full_update($userinfo) {
	// 	$this->db7->insert_batch($this->table_users, $userinfo);
	// }

	/**
	 * Insert prepared userinfo records, row by row
	 * @param	array 	$userinfo	Prepared userinfo records.
	 * @see		Ole_reward_model::daily_update_prepare_userinfo()
	 * @see		ole_reward_lib::userinfo_daily_update()
	 * @return	array	[ inserted (array), updated (array) ]
	 *                each consists of id/username tuples.
	 */
	public function userinfo_daily_update($userinfo) {
		if (empty($userinfo)) {
			$mesg = 'No new player activities in recent day.  Stopping.';
			$this->utils->debug_log(__METHOD__, $mesg);
			return [ 'mesg' => $mesg ];
		}
		$ures = [ 'inserted' => [], 'updated' => [] ];
		foreach ($userinfo as $row) {
			$userId = $row['UserID'];

			$userId_exists = $this->userinfo_exists($userId);
			if ($userId_exists) {
				$this->utils->debug_log(__METHOD__, 'UserID exists, updating', $userId, $row);
				unset($row['UserID']);
				// $this->db7
				// 	->where('UserID', $userId)
				// 	->update($this->table_users, $row)
				// ;
				$this->db7_update($this->table_users, [ 'UserID' => $userId ], $row);
				$ures['updated'][] = [ 'id' => $userId, 'username' => $row['UserCode'] ];
			}
			else {
				$this->utils->debug_log(__METHOD__, 'UserID not found, inserting', $userId, $row);
				// $this->db7->insert($this->table_users, $row);
				$this->db7_insert($this->table_users, $row);
				$ures['inserted'][] = [ 'id' => $userId, 'username' => $row['UserCode'] ];
			}
		}
		return $ures;
	}

	public function userinfo_exists($userId) {
		// $this->db7->from($this->table_users)
		// 	->select('count(*) as num')
		// 	->where([ 'UserID' => $userId ]);
		// $res = $this->db7->get()->first_row('array');
		// $num = $res['num'];
		// return $num > 0;
		$sql_exist = "SELECT COUNT(*) AS num FROM {$this->table_users}
			WHERE `UserID` = '$userId'
		";
		$res = $this->db7_query_scalar($sql_exist, 'num');
		$this->utils->debug_log(__METHOD__, 'sql', $sql_exist, 'res', $res);
		return $res;
	}

	public function insert_into_local_wagers($insertset) {
		if (empty($insertset)) { return; }
		$this->db->insert_batch($this->table_local_wager, $insertset);
	}

	public function insert_into_local_syncs($insertset) {
		if (empty($insertset)) { return; }
		$this->db->insert_batch($this->table_local_syncs, $insertset);
	}

	public function local_syncs_get_by_id($id) {
		$this->from($this->table_local_syncs)
			->where([ 'id' => $id ])
		;

		$res = $this->runOneRowArray();

		return $res;
	}

	public function local_syncs_update_by_id($id, $updateset) {
		$this->db->where([ 'id' => $id ])
			->update($this->table_local_syncs, $updateset)
		;

		return $this->db->affected_rows();
	}

	public function local_syncs_toggle($id) {
		try {
			$row = $this->local_syncs_get_by_id($id);

			if (empty($row)) {
				throw new Exception('Wager record does not exist');
			}

			if (!empty($row['sync_datetime'])) {
				throw new Exception('Wager record was synced, cannot alter its confirmation', 1);
			}

			$updateset = [ 'confirmed_for_sync' => !($row['confirmed_for_sync']) ];

			$this->local_syncs_update_by_id($id, $updateset);

			$res = [ 'success' => true, 'result' => !($row['confirmed_for_sync']), 'mesg' => null ];
		}
		catch (Exception $ex) {
			$res = [ 'success' => false, 'result' => null, 'mesg' => $ex->getMessage() ];
		}
		finally {
			return $res;
		}
	}

	public function get_local_sync_list($request, $is_export = false) {
		$readOnlyDB = $this->getReadOnlyDB();
		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->data_tables->is_export = $is_export;

		$request['draw'] = '';
		// Show only top 2000 players (That should be enough)
		$request['start'] = 0;
		$request['length'] = 2000;
		$external_order = 'date ASC, ProductID ASC';

		$input 		= $this->data_tables->extra_search($request);

		$this->utils->debug_log(__METHOD__, 'input', $input, 'request', $request);

		$arg_date_from	= $input['date_from'];
		$date_from = date('Ymd', strtotime($arg_date_from));
		$table 		= 'ole777_reward_syncs';
		$joins		= [];
		$where		= [ "date = '$date_from'" ];
		$values		= [];
		$group_by	= [];
		$having		= [];

		$columns = [
			[ 'alias' => 'id', 'select' => 'id'] ,
			[ 'dt' => 0 , 'alias' => 'Date'			, 'select' => 'Date' ] ,
			[ 'dt' => 1 , 'alias' => 'Product'		, 'select' => 'ProductID' ,
				'formatter' => function ($d, $row) {
					return $this->ole_product_id_to_text($d, 'product');
			}] ,
			[ 'dt' => 2 , 'alias' => 'GameType'		, 'select' => 'ProductID' ,
				'formatter' => function ($d, $row) {
					return $this->ole_product_id_to_text($d, 'cat');
			}] ,
			[ 'dt' => 3 , 'alias' => 'WagerCount'	, 'select' => 'WagerCount' ,
				'formatter' => function ($d, $row) {
					return number_format($d, 0);
			}],
			[ 'dt' => 4 , 'alias' => 'BetAmount'	, 'select' => 'BetAmount' ,
				'formatter' => 'currencyFormatter' ],
			[ 'dt' => 5 , 'alias' => 'EffectiveAmount'	, 'select' => 'EffectiveAmount' ,
				'formatter' => 'currencyFormatter' ],
			[ 'dt' => 6 , 'alias' => 'WinLoss'		, 'select' => 'WinLoss' ,
				'formatter' => function ($d, $row) {
					$color = floatval($d) > 0 ? 'green' : 'red';
					$figure = sprintf("%.2f", $d);
					return "<span style='color: {$color}'>{$figure}</span>";
			}],
			[ 'dt' => 7 , 'alias' => 'sync_confirm'	, 'select' => 'confirmed_for_sync' ,
				'formatter' => function ($d, $row) {
					$checked = empty($d) ? '' : 'checked="1"';
					$disabled = empty($row['sync_datetime']) ? '' : 'disabled="1"';
					return "<input type='checkbox' class='sync_conf' data-rowid='{$row['id']}' data-dateprod='{$row['Date']}-{$row['Product']}' data-syncdt='{$row['sync_datetime']}' {$checked} {$disabled} onchange='conf_enqueue(this)' />";
			}] ,
			[ 'dt' => 8 , 'alias' => 'sync_datetime'	, 'select' => 'sync_datetime' ,
				'formatter' => function ($d, $row) {
					return empty($d) ? lang('Not synced yet') :
						"<span style='color:gray;'>" . lang('Synced') . "</span>";
			}]
		];

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having, $external_order);

		return $result;
	}



	public function sbe_id_to_ole_product_id($game_platform_id, $game_type_id) {
		if (!isset($this->sbe2ole[$game_platform_id])) {
			return $this->sbe2ole['*'];
		}

		$gpmatch = $this->sbe2ole[$game_platform_id];
		if (!is_array($gpmatch)) {
			return $gpmatch;
		}
		else if (isset($gpmatch[$game_type_id])) {
			return $gpmatch[$game_type_id];
		}
		else if (isset($gpmatch['*'])) {
			return $gpmatch['*'];
		}
		else {
			return $this->sbe2ole['*'];
		}
	}

	public function ole_product_id_to_text($product_id, $field) {
		if (isset($this->ole2text[$product_id])) {
			return lang(ucfirst($this->ole2text[$product_id][$field]));
		}
		return ucfirst($this->ole2text['999'][$field]);
	}

	public function wager_data_post_process($wager_data) {
		$prod_ids = [];
		$summ = [];
		if (empty($wager_data)) {
			return $wager_data;
		}
		foreach ($wager_data as & $row) {
			$row['ProductID'] = $this->sbe_id_to_ole_product_id($row['ProductID'], $row['game_tag']);
			unset($row['game_tag']);

			$prod_ids[] = $row['ProductID'];

			$row = $this->format_wager_row($row);
		}

		array_multisort($prod_ids, $wager_data);

		return $wager_data;
	}

	public function get_summ_data_from_local_wagers($date_sync) {
		$date = empty($date_sync) ? time() : strtotime($date_sync);
		$date_ident = date('Ymd', $date);
		$this->db->from($this->table_local_wager)
			->select([
				'Date',
				'ProductID',
				'SUM(wagerCount) AS wagerCount',
				'SUM(BetAmount) AS BetAmount',
				'SUM(EffectiveAmount) AS EffectiveAmount',
				'SUM(WinLoss) AS WinLoss'
			])
			->where([ 'Date' => $date_ident ])
			->group_by(['ProductID'])
			->order_by('ProductID')
		;

		$res = $this->runMultipleRowArray();

		$this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());

		return $res;
	}

	protected function format_wager_row($row) {
		$fields = [ 'BetAmount', 'EffectiveAmount', 'WinLoss' ];
		foreach ($fields as $field) {
			$row[$field] = sprintf('%.6f', $row[$field]);
		}

		return $row;
	}

	public function get_unsynced_summ_data_from_local_syncs($dt_start, $dt_end) {
		$date_start	= date('Ymd', $dt_start);
		$date_end	= date('Ymd', $dt_end);

		$this->db->from($this->table_local_syncs);

		if ($date_start == $date_end) {
			$this->db->where('date <=', $date_start);
		}
		else {
			$this->db->where("date BETWEEN '{$date_start}' AND '{$date_end}'", null, false);
		}

		$this->db
			->where('confirmed_for_sync', true)
			->where('sync_datetime IS NULL', null, false)
		;

		$res = $this->runMultipleRowArray();

		$this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());

		$this->utils->debug_log(__METHOD__, 'res', $res);

		return $res;
	}

	public function get_unsynced_wager_data_from_local_wager($summ_sync) {
		if (empty($summ_sync)) { return []; }
		$wager_rows = [];
		foreach ($summ_sync as $srow) {
			$this->from($this->table_local_wager)
				->where([ 'Date' => $srow['Date'], 'ProductID' => $srow['ProductID'] ])
			;

			$res = $this->runMultipleRowArray();
			// $this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());
			$wager_rows = array_merge($wager_rows, $res);
		}

		return $wager_rows;
	}

	/**
	 * Check if specific wager row exists in remote wager table
	 *   Determined by Date-ProductID-GameTypeID-UserCode tuple
	 * @param	array 	$row 	Row array of local wager table
	 * @return	true if exists; false otherwise
	 */
	public function wager_exists_row($row) {
		// $this->db7->from($this->table_wager)
			// ->select('count(*) as num')
			// ->where([ 'Date' => $row['Date'], 'ProductID' => $row['ProductID'] , 'GameTypeID' => $row['GameTypeID'], 'UserCode' => $row['UserCode'] ]);
		// $res = $this->db7->get()->first_row('array');
		$sql_exist = "SELECT COUNT(*) AS num FROM {$this->table_wager}
			WHERE Date = '{$row['Date']}'
				AND ProductID = '{$row['ProductID']}'
				AND GameTypeID = '{$row['GameTypeID']}'
				AND UserCode = '{$row['UserCode']}'
		";

		$res = $this->db7_query_scalar($sql_exist, 'num');
		$this->utils->debug_log(__METHOD__, 'res', $res);
		return $res;
	}

	/**
	 * SBE => OLE777 Reward wager sync db worker
	 *   Manager method: ::sync_wager_data_to_remote_wager()
	 * @param	array 		$wagers_sync	Wager records to sync
	 * @see		Ole_reward_model::sync_wager_data_to_remote_wager()
	 * @return	array 	Array of by-record insert results
	 *                      [ {wager_id} => true/false, ... ]
	 *                      true if record is inserted successfully, false otherwise
	 */
	public function insert_into_remote_wager($wagers_sync) {
		if (empty($wagers_sync)) { return []; }
		$ires = [];

		// Insert row-by-row
		foreach ($wagers_sync as & $row) {
			// TEST CODE; insert random name if UserCode empty;
			// Insert 'test_mode'=>false in config ole_read_conf section to suppress
			if ($this->TEST_MODE && empty($row['UserCode'])) {
				$row['UserCode'] = 'F_' . $this->utils->generateRandomCode(6);
			}

			// See if this record already exists in remote table
			if ($this->wager_exists_row($row)) {
				$this->utils->debug_log(__METHOD__, 'wager row already exists', $row);
				$ires[ $row['id'] ] = false;
				continue;
			}

			// Prepare insertset, remove redundant fields
			$insertset = $row;
			unset($insertset['id']);
			unset($insertset['sync_datetime']);
			unset($insertset['notes']);

			$ins_affected_rows = $this->db7_insert($this->table_wager, $insertset, 'rows');
			$this->utils->debug_log(__METHOD__, 'ins_affected_rows', $ins_affected_rows);
			// Note the format of insert results mentioned in header comments
			// $ires[ $row['id'] ] = $this->db7->affected_rows() < 1 ? false : true;
			$ires[ $row['id'] ] = $ins_affected_rows < 1 ? false : true;
		}

		return $ires;
	}


	/**
	 * SBE => OLE777 Reward wager sync handler
	 *   Front interface: Ole_reward_lib::wager_daily_update()
	 *   DB workers: ::get_unsynced_summ_data_from_local_syncs(), ::get_unsynced_wager_data_from_local_wager(), ::insert_into_remote_wager()
	 * @param	timestamp (int)	$dt_start	start time of sync
	 * @param	timestamp (int)	$dt_end		end time of sync
	 * @see		ole_reward_lib::wager_daily_update()
	 * @see		(local-db) Ole_reward_model::get_unsynced_summ_data_from_local_syncs()
	 * @see		(local-db) Ole_reward_model::get_unsynced_wager_data_from_local_wager()
	 * @see		(remote-db) Ole_reward_model::insert_into_remote_wager()
	 *
	 * @return	array 	Array of sync results
	 */
	public function sync_wager_data_to_remote_wager($dt_start, $dt_end) {
		// Log - mark start of sync
		$this->utils->debug_log(__METHOD__, 'ole777 wager sync starting');

		// Read sync summary items, then read wager items by summary items
		$summ_sync = $this->get_unsynced_summ_data_from_local_syncs($dt_start, $dt_end);
		$wagers_sync = $this->get_unsynced_wager_data_from_local_wager($summ_sync);

		// Exit of no wager records to sync
		if (empty($wagers_sync)) {
			$this->utils->debug_log(__METHOD__, 'ole777 wager sync stopped. Nothing to sync.');
			return;
		}

		$this->utils->debug_log(__METHOD__, sprintf("%d records from %d group(s) to sync", count($wagers_sync), count($summ_sync)));

		// Build sync group-item list: [ '{date}-{ProductID}' => [ {wager_id}, ... ] ]
		// Also, reverse lookup list: [ {wager_id} => '{date}-{ProductID}', ... ]
		$summ_sync_succ = [];
		$sync_lookup = [];
		foreach ($wagers_sync as $wrow) {
			$wsid = "{$wrow['Date']}-{$wrow['ProductID']}";
			$sync_lookup[$wrow['id']] = $wsid;
			if (!isset($summ_sync_succ[$wsid])) {
				$summ_sync_succ[$wsid] = [ $wrow['id'] => 1  ];
			}
			else {
				$summ_sync_succ[$wsid][$wrow['id']] = 1;
			}
		}

		// Really sync wagers to remote db
		$sync_op_time = date('c');
		$sync_res = $this->insert_into_remote_wager($wagers_sync);

		// By sync result, mark sync datetime on each sync item
		// And liminate items in group-item list if sync is successful
		$wids = [];
		foreach ($sync_res as $wid => $succ) {
			if ($succ == true) {
				unset($summ_sync_succ[$sync_lookup[$wid]][$wid]);
				$wids[] = $wid;
			}
			$updateset = $succ == true ? [ 'sync_datetime' => $sync_op_time, 'notes' => '' ] :
				[ 'notes' => "sync failed: $sync_op_time" ];
			$this->db->where([ 'id' => $wid ])
				->update($this->table_local_wager, $updateset);
		}
		if (!empty($wids)) {
			$this->utils->debug_log(__METHOD__, 'wager record(s) sync successful', [ 'id' => $wids ]);
		}

		// Inspect each sync group in group-item list
		// All items are eliminated => the group is successful synced
		// mark sync datetime for this sync group
		foreach ($summ_sync_succ as $skey => $sres) {
			$skeys = explode('-', $skey);
			$date = $skeys[0];
			$product_id = $skeys[1];
			if (empty($sres)) {
				$this->utils->debug_log(__METHOD__, 'wager group(s) sync successful', [ 'Date' => $date, 'ProductID' => $product_id]);
				$updateset = [ 'sync_datetime' => $sync_op_time, 'notes' => '' ];
			}
			else {
				$this->utils->debug_log(__METHOD__, 'wager group sync failed', ['Date' => $date, 'ProductID' => $product_id, 'failed_wager_id' => $sres ]);
				$updateset = [ 'notes' => "sync unsuccessful: $sync_op_time" ];
			}
			$this->db->where([ 'Date' => $date, 'ProductID' => $product_id ])
				->update($this->table_local_syncs, $updateset);
		}

		// Log - mark end of sync
		$this->utils->debug_log(__METHOD__, 'ole777 wager sync finished');

		return $summ_sync_succ;
	}

	// protected function format_wagerdata($wager) {
	// 	$fields = [ 'BetAmount', 'EffectiveAmount', 'WinLoss' ];
	// 	if (!is_array($wager) || empty($wager)) { return $wager; }
	// 	foreach ($wager as & $row) {
	// 		foreach ($fields as $field) {
	// 			$row[$field] = sprintf('%.6f', $row[$field]);
	// 		}
	// 	}

	// 	return $wager;
	// }

	protected function setup_ole_product_id_map() {
		$this->json_ole_to_text = <<< EOT
{"20":{"product":"OG","cat":"casino"},"52":{"product":"GPI","cat":"casino"},"58":{"product":"GPI","cat":"slot"},"61":{"product":"GPI","cat":"lottery"},"53":{"product":"All bet","cat":"casino"},"6":{"product":"AG","cat":"casino"},"65":{"product":"AG","cat":"fishing"},"16":{"product":"BBIN","cat":"casino"},"56":{"product":"BBIN","cat":"slot"},"64":{"product":"BBIN","cat":"fishing"},"5":{"product":"BETSOFT","cat":"slot"},"57":{"product":"DT","cat":"slot"},"54":{"product":"EBET","cat":"casino"},"67":{"product":"IDN","cat":"pojer"},"66":{"product":"Kaiyuan","cat":"poker"},"12":{"product":"MG","cat":"slot"},"1":{"product":"ONEWORKS","cat":"sport"},"60":{"product":"PG","cat":"slot"},"59":{"product":"PP","cat":"slot"},"14":{"product":"PLAYTECH","cat":"casino"},"13":{"product":"PLAYTECH","cat":"slot"},"18":{"product":"QTECH","cat":"slot"},"51":{"product":"SBO","cat":"sport"},"55":{"product":"SBO","cat":"casino"},"50":{"product":"SB tech","cat":"sport"},"62":{"product":"T1 Lottery","cat":"lottery"},"63":{"product":"TCG Lottery","cat":"lottery"},"999":{"product":"Others","cat":""},"*":{"product":"Others","cat":""}}
EOT;

		$this->json_sbe_to_ole = <<< EOT
{"183":20,"24":{"live_dealer":52,"slots":58,"lottery":61},"29":53,"72":{"fishing_game":65,"live_dealer":6},"8":{"live_dealer":16,"slots":56,"fishing_game":64},"888":5,"120":57,"53":54,"121":67,"714":66,"820":12,"58":1,"857":60,"232":59,"1":{"live_dealer":14,"slots":13},"42":18,"234":{"sports":51,"live_dealer":55},"484":50,"1004":62,"859":6,"*":"999"}
EOT;
		$this->sbe2ole = json_decode($this->json_sbe_to_ole, 'as_array');
		$this->ole2text = json_decode($this->json_ole_to_text, 'as_array');
	}

	public function get_wager_rows_by_dateymd($dateymd_from, $dateymd_to, $fields = [ 'id', 'Date' ]) {
		$this->db->from($this->table_local_wager)
			->select($fields)
			->where("Date BETWEEN '{$dateymd_from}' AND '{$dateymd_to}'", null, false)
			->order_by('Date', 'ASC')
		;

		$res = $this->runMultipleRowArray();

		// $this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());

		return $res;
	}

	public function get_wager_dates_within_dateymd($dateymd_from, $dateymd_to) {
		$this->db->from($this->table_local_wager)
			->select([
				'Date', 'COUNT(*) AS count'
			])
			->group_by('Date')
			->order_by('Date', 'ASC')
		;

		if (!empty($dateymd_from) && !empty($dateymd_to)) {
			$this->db->where("Date BETWEEN '{$dateymd_from}' AND '{$dateymd_to}'", null, false);
		}

		$res = $this->runMultipleRowArray();

		$this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());

		return $res;
	}

	public function get_sync_dates_within_dateymd($dateymd_from, $dateymd_to) {
		$this->db->from($this->table_local_syncs)
			->select([
				'Date', 'COUNT(*) AS count' , 'id', 'ProductID',
				'sum(if(confirmed_for_sync IS NOT NULL, 1, 0)) AS confirmed' ,
				'sum(if(sync_datetime IS NOT NULL, 1, 0)) AS synced'
			])

			->where("Date !=", self::SYNC_SYSTEM_MARKER)
			->where("ProductID !=", self::SYNC_FLAG_FULL_SYNC)
			->group_by('id')
			->order_by('Date', 'ASC')
		;

		if (!empty($dateymd_from) && !empty($dateymd_to)) {
			$this->db->where("Date BETWEEN '{$dateymd_from}' AND '{$dateymd_to}'", null, false);
		}

		$res = $this->runMultipleRowArray();

		// $this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());

		return $res;
	}

	public function shift_wagers($wager_dates, $offset) {
		return $this->shift_wagers_syncs_common('wagers', $wager_dates, $offset);
	}

	public function shift_syncs($wager_dates, $offset) {
		return $this->shift_wagers_syncs_common('syncs', $wager_dates, $offset);
	}

	protected function shift_wagers_syncs_common($mode, $dates, $offset) {
		$wtables = [
			'wagers'	=> $this->table_local_wager,
			'syncs'		=> $this->table_local_syncs
		];
		if (!isset($wtables[$mode])) { return -1; }
		$wtable = $wtables[$mode];

		if (empty($dates)) { return 0; }

		$update_sqls = [];
		$aff_rows_total = 0;
		foreach ($dates as $drow) {
			// Determine old/new dates
			$wd_old = $drow['Date'];
			$wy_old = intval(substr($wd_old, 0, 4));
			$wy_new = $wy_old - $offset;
			$wd_new = $wy_new . substr($wd_old, 4);

			$update_sql = "UPDATE {$wtable} SET `Date` = '$wd_new' WHERE `Date` = '$wd_old'";
			$this->db->query($update_sql);
			$aff_rows = $this->db->affected_rows();
			$update_sqls[] = [ 'old_date' => $wd_old, 'new_date' => $wd_new, 'sql' => $update_sql, 'aff_rows' => $aff_rows ];
			$aff_rows_total += $aff_rows;
		}

		$this->utils->debug_log(__METHOD__, "$mode shift results", $update_sqls);

		return $aff_rows_total;
	}

	public function remove_wagers($wager_dates) {
		return $this->remove_wagers_syncs_common('wagers', $wager_dates);
	}

	public function remove_syncs($wager_dates) {
		return $this->remove_wagers_syncs_common('syncs', $wager_dates);
	}

	protected function remove_wagers_syncs_common($mode, $dates) {
		$wtables = [
			'wagers'	=> $this->table_local_wager,
			'syncs'		=> $this->table_local_syncs
		];
		if (!isset($wtables[$mode])) { return -1; }
		$wtable = $wtables[$mode];

		if (empty($dates)) { return 0; }

		$remove_sqls = [];
		$aff_rows_total = 0;
		foreach ($dates as $drow) {
			$wd_old = $drow['Date'];

			$update_sql = "DELETE FROM {$wtable} WHERE `Date` = '$wd_old'";
			$this->db->query($update_sql);
			$aff_rows = $this->db->affected_rows();
			$remove_sqls[] = [ 'date' => $wd_old, 'sql' => $update_sql, 'aff_rows' => $aff_rows ];
			$aff_rows_total += $aff_rows;
		}

		$this->utils->debug_log(__METHOD__, "$mode delete results", $remove_sqls);

		return $aff_rows_total;
	}


	protected function db7_query($sql, $return_type = null) {
		// $query = $this->db7->prepare($sql);
		// $query->execute();
		// $res = $query->fetchAll(PDO::FETCH_ASSOC);

		$query = $this->db7->query($sql);

		$res = $query;
		if (!is_bool($query)) {
			$res = $query->fetch_all(MYSQLI_ASSOC);
		}

		switch ($return_type) {
			case 'insert_id' : case 'id' :
				// $res = $this->db7->lastInsertId();
				$res = $this->db7->insert_id;
				break;
			case 'affected_rows' : case 'rows' :
				// $res = $query->rowCount();
				$res = $this->db7->affected_rows;
				break;
			default :
		}

		return $res;
	}

	protected function db7_query_scalar($sql, $column_name) {
		$res = $this->db7_query($sql);

		$row0 = reset($res);
		$val = $row0[$column_name];
		return $val;
	}

	public function remote_wager_list() {
		$sql_list = "SELECT Date, COUNT(*) AS count
			FROM {$this->table_wager}
			GROUP BY Date
			ORDER BY Date DESC
			LIMIT 50
		";

		$res = $this->db7_query($sql_list);

		return $res;
	}

	public function remote_wager_full() {
		$sql_list = "SELECT *
			FROM {$this->table_wager}
			ORDER BY Date DESC
			LIMIT 50
		";

		$res = $this->db7_query($sql_list);

		return $res;
	}

	protected function db7_insert($table, $insertset, $return_type = 'id') {
		$sql_cols = implode(',', array_keys($insertset));
		$ins_vals = array_values($insertset);
		foreach ($ins_vals as & $v) { $v = "'{$v}'"; }
		$sql_vals = implode(',', $ins_vals);
		$sql_ins = "INSERT INTO `{$table}` ({$sql_cols}) VALUES ({$sql_vals})";

		$this->utils->debug_log(__METHOD__,  "sql_ins", $sql_ins);
		$ins_id = $this->db7_query($sql_ins, $return_type);
		return $ins_id;
	}

	protected function db7_update($table, $wheres, $updateset) {
		$where_pairs = [];
		foreach ($wheres as $k => $v) {
			$where_pairs[] = "`{$k}` = '{$v}'";
		}
		$where_clause = implode(' AND ', $where_pairs);

		$set_pairs = [];
		foreach ($updateset as $k => $v) {
			$set_pairs[] = "`{$k}` = '{$v}'";
		}
		$set_clause = implode(', ', $set_pairs);

		$sql_update = "UPDATE {$table} SET {$set_clause} WHERE {$where_clause}";

		$aff_rows = $this->db7_query($sql_update, 'rows');
		return $aff_rows;
	}

} // End of Ole_reward_model
