<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * OLE777 Reward System Sync library
 * Usage:
 * 	ole_reward_lib::userinfo_full_update()
 * 		Initial sync of userinfo.  Run once only.
 * 	ole_reward_lib::userinfo_daily_update()
 * 		Daily sync of usernfo.  Run everyday at 17:00.
 * 	ole_reward_lib::build_daily_wagerdata()
 * 		Build today's wager records.  Run everyday at 12:30.
 *  ole_reward_lib::wager_daily_update()
 *  	Perform today's wager sync; will sync everything checked with date <= today.
 *  	Run everyday at 17:10.
 */
class ole_reward_lib {

	protected $DEFAULT_INTERVAL_MIN = 'yesterday 12:00:00';
	protected $DEFAULT_INTERVAL_MAX = 'today 11:59:59';

	protected $db7;
	protected $ole7_conf;

	protected $map_iso_to_ole7_country;

	protected $TEST_MODE = true;

	public function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->model(['player_model', 'ole_reward_model', 'game_logs']);
		$this->ole7_conf = $this->ci->utils->getConfig('ole777_reward_conf');
		if (isset($this->ole7_conf['test_mode']) && $this->ole7_conf['test_mode'] == false) {
			$this->TEST_MODE = false;
		}

		$this->setup_map_iso_to_ole7_country();
	}

	/**
	 * Ole777 Reward wager sync entry point
	 *   Front interface for Ole_reward_model::sync_wager_data_to_remote()
	 *   Intended usage: ole_reward_lib::wager_daily_update()
	 *   	(Without parameters, will auto sync everything checked <= today)
	 * @param	datetime	$date_start	(Y-m-d, Optional) Start datetime of sync
	 * @param	datetime	$date_end	(Y-m-d, Optional) End datetime of sync
	 * @see		Ole_reward_model::sync_wager_data_to_remote_wage
	 * @return	array 		Sync results
	 *                      (See Ole_reward_model::sync_wager_data_to_remote_wager() for format)
	 */
	public function wager_daily_update($date_start = null, $date_end = null) {
		$date_start	= empty($date_start) ? time() : strtotime($date_start);
		$date_end	= empty($date_end) ? time() : strtotime($date_end);
		$sync_res = $this->ci->ole_reward_model->sync_wager_data_to_remote_wager($date_start, $date_end, 'live');

		return $sync_res;
	}

	/**
	 * Build daily wager data (for sync) and summary data (for SBE wedger sync page listing)
	 *   Intended usage:
	 *   	ole_reward_mode::build_daily_wagerdata({today})
	 *    	ole_reward_mode::build_daily_wagerdata()
	 * @param	datetime	$date_sync	Base date to built wager data by
	 * @param	bool		$live		If true, will insert into local wedger table and summary table
	 * @return	array 		[ wager_data, summary_data ]
	 */
	public function build_daily_wagerdata($date_sync = null, $live = true) {
		$dt_start	= date('c', strtotime("{$date_sync} -1 day 12:00:00"));
		$dt_end		= date('c', strtotime("{$date_sync} +0 day 11:59:59"));
		$this->ci->utils->debug_log(__METHOD__, [ 'date_sync' => $date_sync, 'dt_start' => $dt_start, 'dt_end' => $dt_end ]);

		$wager_data = $this->ci->game_logs->build_daily_wagerdata_for_ole777($dt_start, $dt_end);
		$wager_data = $this->ci->ole_reward_model->wager_data_post_process($wager_data);

		$this->ci->utils->debug_log(__METHOD__, 'wager_data', $wager_data);

		$summ_data = null;
		if ($live) {
			$this->ci->ole_reward_model->insert_into_local_wagers($wager_data);

			$summ_data = $this->ci->ole_reward_model->get_summ_data_from_local_wagers($date_sync);
			$this->ci->utils->debug_log(__METHOD__, 'summ_data', $summ_data);
			$this->ci->ole_reward_model->insert_into_local_syncs($summ_data);
		}

		return [ $wager_data, $summ_data ];
	}

	/**
	 * Shifts existing wager records (in ole777_reward_wagers) and sync records
	 * (in ole777_reward_syncs) within [ $dw_from, $dw_to] by $offset years backward
	 * @param	string	$dw_from	Start of interval (Ymd format)
	 * @param	string	$dw_to		End of interval (Ymd format)
	 * @param	int		$offset		offset, in years.  Note: positive means backwards.
	 *
	 * @return	array
	 */
	public function wager_interval_shift($dw_from, $dw_to, $offset) {
		$this->ci->utils->debug_log(__METHOD__, 'Starting', [ 'dw_from' => $dw_from, 'dw_to' => $dw_to, 'offset' => $offset ]);

		$res = [];

		// Shifting wagers
		$wager_dates = $this->ci->ole_reward_model->get_wager_dates_within_dateymd($dw_from, $dw_to);
		$this->ci->utils->debug_log(__METHOD__, 'wager_dates', $wager_dates);

		$res_w = $this->ci->ole_reward_model->shift_wagers($wager_dates, $offset);

		$this->ci->utils->debug_log(__METHOD__, 'wager dates shift ending', [ 'dw_from' => $dw_from, 'dw_to' => $dw_to, 'offset' => $offset, 'affected_rows' => $res_w ]);

		$res['wagers'] = [ 'dw_from' => $dw_from, 'dw_to' => $dw_to, 'offset' => $offset, 'affected_rows' => $res_w ];

		// Shifting syncs
		$sync_dates = $this->ci->ole_reward_model->get_sync_dates_within_dateymd($dw_from, $dw_to);
		$this->ci->utils->debug_log(__METHOD__, 'sync_dates', $sync_dates);

		$res_s = $this->ci->ole_reward_model->shift_syncs($sync_dates, $offset);

		$this->ci->utils->debug_log(__METHOD__, 'sync dates shift ending', [ 'dw_from' => $dw_from, 'dw_to' => $dw_to, 'offset' => $offset, 'affected_rows' => $res_s ]);

		$res['syncs'] = [ 'dw_from' => $dw_from, 'dw_to' => $dw_to, 'offset' => $offset, 'affected_rows' => $res_s ];

		return $res;

		// print_r($sync_dates);
	}

	/**
	 * Really deleted wagers of a given interval
	 * @param	string	$dw_from	Start of interval (Ymd format)
	 * @param	string	$dw_to		End of interval (Ymd format)
	 * @param	string	$live    [description]
	 * @return	array 	array of operation params and results
	 */
	public function wager_interval_remove($dw_from, $dw_to, $live = true) {
		$this->ci->utils->debug_log(__METHOD__, 'Starting', [ 'dw_from' => $dw_from, 'dw_to' => $dw_to,]);

		// Shifting wagers
		$wager_dates = $this->ci->ole_reward_model->get_wager_dates_within_dateymd($dw_from, $dw_to);
		$this->ci->utils->debug_log(__METHOD__, 'wager_dates', $wager_dates);

		$res_w = 0;
		if ($live) {
			$res_w = $this->ci->ole_reward_model->remove_wagers($wager_dates);
		}

		$this->ci->utils->debug_log(__METHOD__, 'wager delete ending', [ 'dw_from' => $dw_from, 'dw_to' => $dw_to, 'affected_rows' => $res_w ]);

		// Shifting syncs
		$sync_dates = $this->ci->ole_reward_model->get_sync_dates_within_dateymd($dw_from, $dw_to);

		$this->ci->utils->debug_log(__METHOD__, 'sync_dates', $sync_dates);

		$res_s = 0;
		if ($live) {
			$res_s = $this->ci->ole_reward_model->remove_syncs($sync_dates);
		}

		$this->ci->utils->debug_log(__METHOD__, 'sync delete ending', [ 'dw_from' => $dw_from, 'dw_to' => $dw_to, 'affected_rows' => $res_s ]);

		$res = [
			'dw_from'        => $dw_from,
			'dw_to'          => $dw_to ,
			'wagers_removed' => $res_w ,
			'syncs_removed'  => $res_s ,
			'live_mode'      => $live
		];

		return $res;
		// print_r($sync_dates);
	}

	public function wagers_sync_overview($dw_from = null, $dw_to = null) {
		$wres = $this->ci->ole_reward_model->get_wager_dates_within_dateymd($dw_from, $dw_to);
		$sres = $this->ci->ole_reward_model->get_sync_dates_within_dateymd($dw_from, $dw_to);

		$res = [ 'wagers' => $wres, 'syncs' => $sres ];
		// $this->ci->utils->debug_log(__METHOD__, 'wager/sync overview', $res);

		return $res;
	}

	/**
	 * Full userinfo update endpoint
	 * @return	array 	[ success (bool), mesg (text) ]
	 */
	public function userinfo_full_update() {
		$full_sync_marker = $this->ci->ole_reward_model->check_marker_full_sync();
		if ($full_sync_marker['exists']) {
			$this->ci->utils->debug_log(__METHOD__, 'Userinfo full sync has been executed before, exiting', $full_sync_marker);
			return [ 'success' => false, 'mesg' => "Userinfo full sync already executed on {$full_sync_marker['sync_datetime']}"];
		}

		$userinfo = $this->full_update_prepare_userinfo();
		$this->ci->utils->debug_log(__METHOD__, 'Userinfo full sync: syncing ' . count($userinfo) . ' players');

		// insert_batch not implemented now; use daily_update instead
		// $this->ci->ole_reward_model->userinfo_full_update($userinfo);
		$this->ci->ole_reward_model->userinfo_daily_update($userinfo);

		$this->ci->ole_reward_model->insert_marker_full_sync();
		$this->ci->utils->debug_log(__METHOD__, 'Userinfo full sync complete.');
		return [ 'success' => true, 'mesg' => "Userinfo full sync complete."];
	}

	/**
	 * Daily userinfo update endpoint
	 * @return	array	[ inserted (array), updated (array) ]
	 *                each consists of id/username tuples.
	 */
	public function userinfo_daily_update() {
		$userinfo = $this->daily_update_prepare_userinfo();
		$res = $this->ci->ole_reward_model->userinfo_daily_update($userinfo);

		return $res;
	}

	/**
	 * Get all player_id's of players that have logged in during daily interval
	 * of update
	 * @return [type] [description]
	 */
	public function daily_update_get_player_ids() {
		$dtime_min = strtotime($this->DEFAULT_INTERVAL_MIN);
		$dtime_max = strtotime($this->DEFAULT_INTERVAL_MAX);

		$now = time();

		if ($dtime_max > $now) {
			$this->ci->utils->debug_log(__METHOD__, 'Execution before end of daily interval', [ 'default' => [ $dtime_min, $dtime_max ], 'now' => $now ]);
		}

		$dtime_min_str = date('c', $dtime_min);
		$dtime_max_str = date('c', $dtime_max);

		$player_id_res = $this->ci->player_model->getPlayerIdsByLoginTime($dtime_min_str, $dtime_max_str);

		// $player_ids = $this->flatten_row_array($player_id_res, 'playerId');
		$player_ids = $player_id_res;
		$this->ci->utils->debug_log(__METHOD__, 'player_ids', $player_ids);

		return $player_ids;
	}

	public function full_update_get_player_ids() {
		$player_ids = $this->ci->player_model->getAvailPlayerIdAndLastLogin($this->TEST_MODE);
		$this->ci->utils->debug_log(__METHOD__, 'player_ids', $player_ids);
		// $player_ids = $this->flatten_row_array($player_id_res, 'playerId');

		return $player_ids;
	}

	public function daily_update_prepare_userinfo() {
		$player_ids = $this->daily_update_get_player_ids();
		$this->ci->utils->debug_log(__METHOD__, 'player_ids', $player_ids);
		$updateset = $this->prepare_userinfo($player_ids);

		return $updateset;
	}

	public function full_update_prepare_userinfo() {
		$player_ids = $this->full_update_get_player_ids();
		$insertset = $this->prepare_userinfo($player_ids);

		return $insertset;
	}

	protected function prepare_userinfo($player_ids) {
		$dataset = [];
		if (!empty($player_ids)){
			foreach ($player_ids as $row) {
				$player_id = $row['playerId'];
				$last_login_time = $row['lastLoginTime'];
				$row = $this->prepare_single_userinfo($player_id, $last_login_time);
				$dataset[] = $row;
			}
		}

		return $dataset;
	}

	public function prepare_single_userinfo($player_id, $last_login_time = null) {
		$player = $this->ci->player_model->getPlayerById($player_id);
		$player_details = $this->ci->player_model->getAllPlayerDetailsById($player_id);

		/**
		 * FIELD									FORMAT
		 * Birthday									yyyy-mm-dd
		 * RegisitrationDateFrom, LastLoginDate		yyyy-mm-dd HH:MM:SS ([+-]TZ:tz)
		 */
		$userinfo = [
			'UserID'		=> $player_id ,
			'UserCode'		=> $player->username,
			'RealName'		=> $player_details['firstName'] ?: "Player{$player_id}",
			'Birthday'		=> $player_details['birthdate'] ?: '1970-01-01',
			'CountryID'		=> $this->ole7_country_id($player_details),
			'CurrencyID'	=> $this->ole777_currency_id() ,
			'UserName'		=> $player->username,
			'RegisitrationDateFrom'	=> $this->dtformat($player->createdOn),
			'LastLoginDate'	=> $this->dtformat($last_login_time)
		];

		return $userinfo;
	}

	/**
	 * Converts player's country to Ole country code
	 * @param	array 	$player_details		Return of Player_model::getAllPlayerDetailsById()
	 * @return	int		Ole country code
	 */
	public function ole7_country_id($player_details) {
		// Read player country from playerdetails row provided
		$profile_country = $player_details['residentCountry'];

		if (!empty($profile_country)) {
			$ole7_country_id = $this->country_to_ole7_country_id($profile_country);
		}
		else {
			$reg_ip = $player_details['registrationIP'];
			list($city, $ip_country) = $this->ci->utils->getIpCityAndCountry($reg_ip);
			$ole7_country_id = $this->country_to_ole7_country_id($ip_country);
		}

		return $ole7_country_id;
	}

	/**
	 * Convert country name to Ole country code
	 * @param	string	$country	Name of country
	 * @return	int		Ole country code
	 */
	protected function country_to_ole7_country_id($country) {
		$map_country_to_iso = unserialize(COUNTRY_ISO2);
		$country_iso = strtoupper(isset($map_country_to_iso[$country]) ? $map_country_to_iso[$country] : $this->ole7_conf['defaults']['country']);
		$ole7_country_id = $this->map_iso_to_ole7_country[$country_iso]{0};

		return $ole7_country_id;
	}

	/**
	 * Converts currency string to Ole currency code
	 * @param	string	$currency	Name of Currency
	 * @return	int		Ole currency code
	 */
	protected function ole777_currency_id($currency = null) {
		$mapping_json = '{"cny":156,"rmb":156,"thb":764,"idr":360}';
		$mapping = json_decode($mapping_json, 'as_array');

		// Determine currency: use input first, then ole777_conf
		if (empty($currency)) {
			$currency = $this->ole7_conf['defaults']['currency'];
		}
		// Then SBE default
		if (empty($currency)) {
			$currency = $this->ci->utils->getDefaultCurrency();
		}
		$currency = strtolower($currency);

		// Drop back to rmb as fail-safe
		$currency_id = isset($mapping[$currency]) ? $mapping[$currency] : $mapping['rmb'];

		return $currency_id;
	}

	protected function dtformat($dtstring) {
		if (empty($dtstring)) {
			return $dtstring;
		}
		$dt = strtotime($dtstring);
		$dtstring = date('Y-m-d H:i:s O', $dt);

		return $dtstring;
	}

	/**
	 * Sets up mapping table of ISO country name to Ole country code
	 * @return	none
	 */
	protected function setup_map_iso_to_ole7_country() {
		$json_iso_to_ole7_country = <<<END
{"AD":["1",true],"AE":["2",true],"AF":["3",true],"AG":["4",true],"AI":["5",true],"AL":["6",true],"AM":["7",true],"AO":["8",true],"AQ":["9",true],"AR":["10",true],"AS":["11",true],"AT":["12",true],"AU":["13",true],"AW":["14",true],"AX":["15",true],"AZ":["16",true],"BA":["17",true],"BB":["18",true],"BD":["19",true],"BE":["20",true],"BF":["21",true],"BG":["22",true],"BH":["23",true],"BI":["24",true],"BJ":["25",true],"BL":["26",true],"BM":["27",true],"BN":["28",true],"BO":["29",true],"BQ":["30",true],"BR":["31",true],"BS":["32",true],"BT":["33",true],"BV":["34",true],"BW":["35",true],"BY":["36",true],"BZ":["37",true],"CA":["38",true],"CC":["39",true],"CD":["40",true],"CF":["41",true],"CG":["42",true],"CH":["43",true],"CI":["44",true],"CK":["45",true],"CL":["46",true],"CM":["47",true],"CN":["48",false],"CO":["49",true],"CR":["50",true],"CU":["51",true],"CV":["52",true],"CW":["53",true],"CX":["54",true],"CY":["55",true],"CZ":["56",true],"DE":["57",true],"DJ":["58",true],"DK":["59",true],"DM":["60",true],"DO":["61",true],"DZ":["62",true],"EC":["63",true],"EE":["64",true],"EG":["65",true],"EH":["66",true],"ER":["67",true],"ES":["68",true],"ET":["69",true],"FI":["70",true],"FJ":["71",true],"FK":["72",true],"FM":["73",true],"FO":["74",true],"FR":["75",true],"GA":["76",true],"GB":["77",true],"GD":["78",true],"GE":["79",true],"GF":["80",true],"GG":["81",true],"GH":["82",true],"GI":["83",true],"GL":["84",true],"GM":["85",true],"GN":["86",true],"GP":["87",true],"GQ":["88",true],"GR":["89",true],"GS":["90",true],"GT":["91",true],"GU":["92",true],"GW":["93",true],"GY":["94",true],"HK":["95",false],"HM":["96",true],"HN":["97",true],"HR":["98",true],"HT":["99",true],"HU":["100",true],"ID":["101",false],"IE":["102",true],"IL":["103",true],"IM":["104",true],"IN":["105",true],"IO":["106",true],"IQ":["107",true],"IR":["108",true],"IS":["109",true],"IT":["110",true],"JE":["111",true],"JM":["112",true],"JO":["113",true],"JP":["114",true],"KE":["115",true],"KG":["116",true],"KH":["117",true],"KI":["118",true],"KM":["119",true],"KN":["120",true],"KP":["121",true],"KR":["122",false],"KW":["123",true],"KY":["124",true],"KZ":["125",true],"LA":["126",true],"LB":["127",true],"LC":["128",true],"LI":["129",true],"LK":["130",true],"LR":["131",true],"LS":["132",true],"LT":["133",true],"LU":["134",true],"LV":["135",true],"LY":["136",true],"MA":["137",true],"MC":["138",true],"MD":["139",true],"ME":["140",true],"MF":["141",true],"MG":["142",true],"MH":["143",true],"MK":["144",true],"ML":["145",true],"MM":["146",true],"MN":["147",true],"MO":["148",false],"MP":["149",true],"MQ":["150",true],"MR":["151",true],"MS":["152",true],"MT":["153",true],"MU":["154",true],"MV":["155",true],"MW":["156",true],"MX":["157",true],"MY":["158",true],"MZ":["159",true],"NA":["160",true],"NC":["161",true],"NE":["162",true],"NF":["163",true],"NG":["164",true],"NI":["165",true],"NL":["166",true],"0":["167",true],"NP":["168",true],"NR":["169",true],"NU":["170",true],"NZ":["171",true],"OM":["172",true],"PA":["173",true],"PE":["174",true],"PF":["175",true],"PG":["176",true],"PH":["177",true],"PK":["178",true],"PL":["179",true],"PM":["180",true],"PN":["181",true],"PR":["182",true],"PS":["183",true],"PT":["184",true],"PW":["185",true],"PY":["186",true],"QA":["187",true],"RE":["188",true],"RO":["189",true],"RS":["190",true],"RU":["191",true],"RW":["192",true],"SA":["193",true],"SB":["194",true],"SC":["195",true],"SD":["196",true],"SE":["197",true],"SG":["198",true],"SH":["199",true],"SI":["200",true],"SJ":["201",true],"SK":["202",true],"SL":["203",true],"SM":["204",true],"SN":["205",true],"SO":["206",true],"SR":["207",true],"SS":["208",true],"ST":["209",true],"SV":["210",true],"SX":["211",true],"SY":["212",true],"SZ":["213",true],"TC":["214",true],"TD":["215",true],"TF":["216",true],"TG":["217",true],"TH":["218",false],"TJ":["219",true],"TK":["220",true],"TL":["221",true],"TM":["222",true],"TN":["223",true],"TO":["224",true],"TR":["225",true],"TT":["226",true],"TV":["227",true],"TW":["228",true],"TZ":["229",true],"UA":["230",true],"UG":["231",true],"UM":["232",true],"US":["233",true],"UY":["234",true],"UZ":["235",true],"VA":["236",true],"VC":["237",true],"VE":["238",true],"VG":["239",true],"VI":["240",true],"VN":["241",true],"VU":["242",true],"WF":["243",true],"WS":["244",true],"YE":["245",true],"YT":["246",true],"ZA":["247",true],"ZM":["248",true],"ZW":["249",true]}
END;
		$this->map_iso_to_ole7_country = json_decode($json_iso_to_ole7_country, 'as_array');
	}

}