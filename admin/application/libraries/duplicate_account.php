<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Duplicate Account
 *
 * Duplicate Account library for FHI OG System
 *
 * @package		Duplicate Account
 * @author		Johann Merle
 * @version		1.0.0
 */

class Duplicate_account {

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array('utils'));
		$this->ci->load->model(array('duplicate_account_setting'));
	}

	private $search = array('username', 'password', 'firstName', 'lastName', 'phone', 'email', 'address', 'city');
	private $complete = array('username', 'password', 'firstName', 'lastName', 'phone', 'email', 'address', 'city', 'ip');
	// private $search = array('username', 'password', 'firstName', 'lastName', 'phone', 'email', 'country', 'address', 'city');
	// private $complete = array('username', 'password', 'firstName', 'lastName', 'phone', 'email', 'country', 'address', 'city', 'ip', 'cookie', 'referrer', 'user_agent');

	/**
	 * get player details
	 *
	 * @param	int
	 * @param	string
	 * @return	array
	 */
	public function getPlayerDetails($player_id) {
		return $this->ci->duplicate_account_setting->getPlayerDetails($player_id);
	}

	/**
	 * get player details
	 *
	 * @param	int
	 * @return	array
	 */
	public function getHTTPRequestById($http_request_id) {
		return $this->ci->duplicate_account_setting->getHTTPRequestById($http_request_id);
	}

	/**
	 * get player details
	 *
	 * @param	int
	 * @param	int
	 * @return	array
	 */
	public function getHTTPRequestByPlayerId($playerId, $type) {
		return $this->ci->duplicate_account_setting->getHTTPRequestType($playerId, $type);
	}

	/**
	 * get http request type and language
	 *
	 * @param	int
	 * @return	array
	 */
	public function getHttpLang($type) {
		$get_type = '';
		switch ($type) {
		case 1:
			$get_type = lang('sys.regIP');
			break;
		case 2:
			$get_type = lang('sys.lastLoginIP');
			break;
		case 3:
			$get_type = lang('sys.dpstIP');
			break;
		case 4:
			$get_type = lang('sys.withdrwIP');
			break;
		case 5:
			$get_type = lang('sys.transMainToSubIP');
			break;
		case 6:
			$get_type = lang('sys.transSubToMainIP');
			break;
		default:
			break;
		}
		return $get_type;
	}

	/**
	 * search duplicate account
	 *
	 * fields:
	 * username 	| <1 letter match
	 * password		| precisely match
	 * real name 	| <1 letter match
	 * mobile		| precisely match
	 * email		| <1 letter match
	 * country		| precisely match
	 * address		| precisely match
	 * IP			| precisely match by sub-network, compare 3 ip segments, like xxx.yyy.zzz | 2 scores write IP, cookies, referer, user-agent to db when Registration, Last Login, Deposit, Withdraw, Transfer From Main Wallet to Sub Wallet,  Transfer From Sub Wallet to Main Wallet
	 * cookies		| precisely match, google analysis id, baidu analysis id | extract id from cookies
	 * referer		| precisely match
	 * device(user agent)	| precisely match, match OS, browser, PC or mobile or MAC | extract fields from user-agent
	 * bank account number 	| don't allow duplicate bank account number | check it on add bank account number
	 *
	 * @param	int
	 * @return	array
	 */
	public function scanDuplicateAccount($player_id) {
		$player = $this->ci->duplicate_account_setting->getPlayerDetails($player_id); //get player details;
		$duplicates = array();

		foreach ($this->search as $key => $value) {
			if ($player[$value] != null || $player[$value] != "") {
				// if field data of player is empty no need to search
				$result = $this->ci->duplicate_account_setting->searchForDuplicates($value, $player[$value], $player_id);
				$duplicates[$value] = $result;
			}
		}

		$duplicates['ip'] = $this->processHttpRequest($player_id, 'ip');
		// $duplicates['cookie'] = $this->processHttpRequest($player_id, 'cookie');
		// $duplicates['referrer'] = $this->processHttpRequest($player_id, 'referrer');
		// $duplicates['user_agent'] = $this->processHttpRequest($player_id, 'user_agent');

		return $duplicates;
	}

	/**
	 * process http_request
	 *
	 * @param	int
	 * @param	string
	 * @return	array
	 */
	public function processHttpRequest($player_id, $request) {
		$player_http_request = $this->ci->duplicate_account_setting->getHTTPRequest($player_id);
		$list_by_type = array();

		foreach ($player_http_request as $key => $value) {
			if (!isset($list_by_type[$value['type']])) {
				//checking if request type is created in array or not
				$list_by_type[$value['type']] = array();
			}
			array_push($list_by_type[$value['type']], $value[$request]);
		}

		$duplicates = array();
		foreach ($list_by_type as $key => $value) {
			$duplicates[$key] = $this->ci->duplicate_account_setting->searchHttpRequest($player_id, $request, $key, $value);
		}

		return $duplicates;
	}

	/**
	 * make one list of duplicate players
	 *
	 * @param	array
	 * @param	int
	 * @return	array
	 */
	public function listOfDuplicates($duplicates, $player_id) {
		$player = $this->ci->duplicate_account_setting->getPlayerDetails($player_id); //get player details;

		$list = array();
		$result = array();
		$rating = '';
		$field_value = '';

		foreach ($this->complete as $value1) {
			if (isset($duplicates[$value1])) {
				foreach ($duplicates[$value1] as $key => $value2) {

					if ($value1 == 'ip') {
						// if ($value1 == 'ip' || $value1 == 'cookie' || $value1 == 'referrer' || $value1 == 'user_agent') {
						foreach ($value2 as $value3) {

							$id1 = $value3['playerId'];
							if (isset($result[$id1])) {
								$result[$id1][$value1][$key] = array($value3['id'], $this->getRating($value1, 'rate_exact'), 'rate_exact'); // array(id, rating value, type of rating)
							} else {
								$result[$id1][$key] = array($value1 => array($value3['id'], $this->getRating($value1, 'rate_exact'), 'rate_exact'));
							}
						}
					}

					if (isset($value2['playerId'])) {
						$id2 = $value2['playerId'];
						$fname = isset($value2['firstName']) ? $value2['firstName'] : '';
						$lname = isset($value2['lastName']) ? $value2['lastName'] : '';
						$player_realname = $player['firstName'] . ' ' . $player['lastName'];
						$value_realname = $fname . ' ' . $lname;

						switch ($value1) {
						case 'username':
							$rating = 'rate_similar';
							$field_value = $value2['username'];
							break;
						case 'password':
							$rating = 'rate_exact';
							$field_value = lang('player.56');
							break;
						case 'firstName':
						case 'lastName':
							if ($player_realname == $value_realname) {
								$rating = 'rate_exact';
							} else {
								$rating = 'rate_similar';
							}
							$field_value = $value_realname;
							break;
						case 'phone':
							$rating = 'rate_exact';
							$field_value = $value2['phone'];
							break;
						case 'email':
							$rating = 'rate_similar';
							$field_value = $value2['email'];
							break;
						// case 'country':
						// 	$rating = 'rate_exact';
						// 	$field_value = $value2['country'];
						// 	break;
						case 'address':
							$rating = 'rate_exact';
							$field_value = $value2['address'];
							break;
						case 'city':
							$rating = 'rate_exact';
							$field_value = $value2['city'];
							break;
						default:
							break;
						}

						$field = ($value1 == 'firstName' || $value1 == 'lastName') ? 'realname' : $value1;

						if (isset($result[$id2])) {
							$result[$id2][$field] = array($field_value, $this->getRating($field, $rating), $rating); // array(value of field, rating value, type of rating)
						} else {
							$result[$id2] = array($field => array($field_value, $this->getRating($field, $rating), $rating));
						}
					}
				}
			}
		}

		return $result;
	}

	/**
	 * get rating of item
	 *
	 * @param	array
	 * @return	array
	 */
	public function getRating($item, $key) {

		switch ($item) {
		case 'username':
			$item_id = duplicate_account_setting::ITEM_USERNAME;
			break;

		case 'password':
			$item_id = duplicate_account_setting::ITEM_PASSWORD;
			break;

		case 'realname':
			$item_id = duplicate_account_setting::ITEM_REAL_NAME;
			break;

		case 'phone':
			$item_id = duplicate_account_setting::ITEM_MOBILE;
			break;

		case 'email':
			$item_id = duplicate_account_setting::ITEM_EMAIL;
			break;

		// case 'country':
		// 	$item_id = duplicate_account_setting::ITEM_COUNTRY;
		// 	break;

		case 'address':
			$item_id = duplicate_account_setting::ITEM_ADDRESS;
			break;

		case 'city':
			$item_id = duplicate_account_setting::ITEM_CITY;
			break;

		case 'ip':
			$item_id = duplicate_account_setting::ITEM_IP;
			break;

		// case 'cookie':
		// 	$item_id = duplicate_account_setting::ITEM_COOKIES;
		// 	break;

		// case 'referrer':
		// 	$item_id = duplicate_account_setting::ITEM_REFERER;
		// 	break;

		// case 'user_agent':
		// 	$item_id = duplicate_account_setting::ITEM_DEVICE;
		// 	break;

		default:
			$item_id = 0;
			break;
		}

		$rate = $this->ci->duplicate_account_setting->getDuplicateAccountSetting($item_id);
		return isset($rate[$key]) ? @$rate[$key] : null;
	}

	/**
	 * existing HTTP Request
	 *
	 * @param	array
	 * @return	bool
	 */
	public function existingHttpRequest($data) {
		$result = $this->ci->duplicate_account_setting->getHttpRequestByData($data);

		if (empty($result)) {
			return 1;
		}

		return 0;
	}

	/**
	 * insert HTTP Request
	 *
	 * @param	array
	 * @return	void
	 */
	public function insertHttpRequest($data) {
		$this->ci->duplicate_account_setting->insertHttpRequest($data);
	}

	/**
	 * get all items
	 *
	 *
	 * @return 	array
	 */
	public function getAllItems($condition = []) {
		return $this->ci->duplicate_account_setting->getAllItems($condition);
	}

	/**
	 * save modifieed Duplicate Account Setting
	 *
	 *
	 * @return 	array
	 */
	public function saveDuplicateAccountSetting($data, $id) {
		return $this->ci->duplicate_account_setting->saveDuplicateAccountSetting($data, $id);
	}

	// Added by William Zhang. 2016-04

	const MIN_MATCH_LEVEL = 3;
	// considered fields in table player
	private $search1 = array('username', 'password', 'email');
	// considered fields in table playerdetails
	private $search2 = array('firstName', 'lastName', 'phone', 'address', 'city');
	// considered fields in table http_request
	private $search3 = array('ip', 'cookie', 'referrer', 'device');

	/**
	 * create duplicate accounts info in JSON
	 *
	 * @param array player info
	 * @return array for all Duplicate Accounts Info
	 */
	public function getDuplicateAccountsJSON($playerId, $report = '') {
		//$this->utils->debug_log($playerId);
		$player = $this->ci->duplicate_account_setting->getPlayerDetails($playerId);
		//$this->utils->debug_log('In getDuplicateAccountsJSON. player_id = ', $playerId, ', Player details: ', $player);

		// array for dup recs from player, playerdetails, and http_request
		$dupData = $this->getDuplicateAccountsRawData($player);

		$dupInfo = $this->getDuplicateAccountsInfo($player, $dupData);

		// only select fields to be displayed on tab page as Memeber's Log
		$dupJSON = $this->selectFieldsJSON($dupInfo, $report);

		return $dupJSON;
	}
	/**
	 * Select fields to be displayed
	 *
	 * @param array for all fields in JSON
	 * @return array only considered fields
	 */
	private function selectFieldsJSON($dupInfo, $report = '') {
		//too slow so remove it
		// $fields = array('username', 'realname', 'email', 'phone', 'city', 'ip' ,'loginIP','totalRate');
		$fields = array('username', 'realname', 'email', 'phone', 'city', 'ip' ,'totalRate');

		if( empty($report) ) array_unshift( $fields, 'action' );

		$ips = array('ip1', 'ip2', 'ip3', 'ip4', 'ip5', 'ip6');

		$dupJSON = array();

		$dupAcc = $dupInfo[0];
		$matchInfo = $dupInfo[1];

		foreach ($dupAcc as $id => $rec) {
			if ($rec['totalRate'] == 0) {
				continue;
			}

			$item = array();
			$i = 0;
			foreach ($fields as $key) {
				$item[$i] = '';
				if ($key != 'ip') {
					if (isset($rec[$key])) {
						$item[$i] .= $rec[$key];
					}
					if (isset($matchInfo[$id][$key])) {
						$item[$i] .= "(" . $matchInfo[$id][$key] . ")";
					}
				} else {
					foreach ($ips as $ip) {
						if (isset($rec[$ip]) && $rec[$ip] != '') {
							$item[$i] .= $rec[$ip];
							if (isset($matchInfo[$id][$ip])) {
								$item[$i] .= "(" . $matchInfo[$id][$ip] . ")";
								break;
							}
						}
					}
				}
				$i++;
			}
			$dupJSON[] = $item;
		}

		return $dupJSON;
	}

	/**
	 * create duplicate accounts info in JSON
	 *
	 * @param array player info
	 * @return array for all Duplicate Accounts Info
	 */
	public function getAllDuplicateAccountsJSON($playerId) {
		$player = $this->ci->duplicate_account_setting->getPlayerDetails($playerId);
		//$this->utils->debug_log('In getAllDuplicateAccountsJSON. player_id = ', $player_id, ', player: ', $player);

		// array for dup recs from player, playerdetails, and http_request
		$dupData = $this->getDuplicateAccountsRawData($player);

		$dupInfo = $this->getDuplicateAccountsInfo($player, $dupData);

		$dupJSON = $this->transArrayToJSON($dupInfo);

		return $dupJSON;
	}

	/**
	 * transform dup info array into JSON format
	 *
	 * @param array for dup info and dup rates
	 * @return array in JSON
	 */
	private function transArrayToJSON($dupInfo) {
		//$keyArray = array("username", "realname", "password", "email", "phone", "address", "city",
		//	"cookie", "referrer", "device", "ip1", "ip2", "ip3", "ip4", "ip5", "ip6", "totalRate");
        $keyArray = array("username", "totalRate", "ip1", "ip2", "ip3", "ip4", "ip5", "ip6",
            "realname", "password", "email", "phone", "address", "city", "cookie", "referrer", "device");
		$dupJSON = array();

		$dupAcc = $dupInfo[0];
		$matchInfo = $dupInfo[1];
		foreach ($dupAcc as $id => $rec) {
			if ($rec['totalRate'] == 0) {
				continue;
			}
			$item = array();
			$i = 0;
			foreach ($keyArray as $key) {
				$item[$i] = '';
				if (isset($rec[$key])) {
					$item[$i] .= $rec[$key];
				}
				if (isset($matchInfo[$id][$key])) {
					$item[$i] .= "(" . $matchInfo[$id][$key] . ")";
				}
				$i++;
			}
			$dupJSON[] = $item;
		}

		return $dupJSON;
	}

	/**
	 * create duplicate accounts info as array
	 *
	 * @param array player info
	 * @return array for all Duplicate Accounts Info
	 */
	public function getAllDuplicateAccounts($playerId) {
		$player = $this->ci->duplicate_account_setting->getPlayerDetails($playerId);

		// array for dup recs from player, playerdetails, and http_request
		$dupData = $this->getDuplicateAccountsRawData($player);

		$dupInfo = $this->getDuplicateAccountsInfo($player, $dupData);

		return $dupInfo;
	}

	/**
	 * get all duplicate accounts for given player id
	 *
	 * @param int   player id
	 * @return array Duplicate account info
	 */
	private function getDuplicateAccountsRawData($player) {

		$dupData = array();
		// fetch data for all possible duplicate accounts
		//this function call possible duplicate record in player table with tracking if the user is exempted for duplicate record report
		$dupData["player"] = $this->ci->duplicate_account_setting->getDupRecsFromTable($player, 'player', $this->search1);

		//this function call possible duplicate record in playerDetails table with tracking if the user is exempted for duplicate record report
		$dupData["playerdetails"] = $this->ci->duplicate_account_setting->getDupRecsFromTable($player, 'playerdetails', $this->search2);
		/*
			         * dupData["http_request"], which is very different from dupData["player"] and dupData["playerdetails"],
			         * may be empty or have 2 elements in which the 1st one is info for current player
			         * while the 2nd one is an array for duplicate accounts
		*/

		$dupData["http_request"] = $this->ci->duplicate_account_setting->getDupRecsFromHttpRequest(
		$player['playerId'], $this->search3);
		return $dupData;
	}

	/**
	 * create duplicate accounts info
	 *
	 * @param array player info
	 * @param array records from different tables
	 * @return array for all Duplicate Accounts Info
	 */
	private function getDuplicateAccountsInfo($player, $dupData) {

		$this->ci->load->model('player');

		// get duplicate rate settings
		$rateValue = $this->ci->duplicate_account_setting->getDupRateSetting();

		// Categrise dup info by playerId
		$dupAcc = array();
		$matchRate = array();

		foreach ($dupData["player"] as $rec) {
			$id = $rec["playerId"];
			if (!isset($dupAcc[$id])) {
				$dupAcc[$id] = array();
				$matchRate[$id] = array();
			}

			$action = '<input type="checkbox" id="tags" name="set_tag[]" value="' . $id . '" />';
			$dupAcc[$id]['action'] = $action;

			//too slow so remove it
			// $dupAcc[$id]['loginIP'] = $this->ci->duplicate_account_setting->getHTTPRequestLogin($id);

			$affiliate = $this->ci->player->getPlayerAffiliateUsername( $id );
			$playerAffiliate = ( ! empty( $affiliate ) ) ? ' (' . $affiliate . ')' : '';

			foreach ($this->search1 as $field) {
				$field_value = $rec[$field];
				if ($field_value != null) {
					$field_value = trim($field_value);
				}
				// if field data of player is empty no need to search
				if ($field_value == null || $field_value == "") {
					continue;
				}

				$field_value = ( $field == "username" ) ? '<a href="/player_management/userInformation/' . $id . '" target="_blank">' . $field_value . $playerAffiliate . '</a>' : $field_value;
				$dupAcc[$id][$field] = $field_value;

				if ($field == "username" || $field == "email") {
					if (levenshtein($field_value, $player[$field]) <= $this::MIN_MATCH_LEVEL) {
						$matchRate[$id][$field] = $rateValue[$field]["rate_similar"];
					}
				} else if ($field_value == $player[$field]) {
					$matchRate[$id][$field] = $rateValue[$field]["rate_exact"];
				}
			}
		}

		$realName = $player['firstName'] . ' ' . $player['lastName'];

		foreach ($dupData["playerdetails"] as $rec) {
			$id = $rec["playerId"];

			if (!isset($dupAcc[$id])) {
				$dupAcc[$id] = array();
				$matchRate[$id] = array();
			}

			$action = '<input type="checkbox" id="tags" name="set_tag[]" value="' . $id . '" />';
			$dupAcc[$id]['action'] = $action;

			$affiliate = $this->ci->player->getPlayerAffiliateUsername( $id );
			$playerAffiliate = ( ! empty( $affiliate ) ) ? ' (' . $affiliate . ')' : '';

			foreach ($this->search2 as $field) {
				$field_value = $rec[$field];
				if ($field_value != null) {
					$field_value = trim($field_value);
				}
				// if field data of player is empty no need to search
				if ($field == "firstName" || $field == "lastName" ||
					$field_value == null || $field_value == "") {
					continue;
				}

				$field_value = ( $field == "username" ) ? '<a href="/player_management/userInformation/' . $id . '" target="_blank">' . $field_value . $playerAffiliate . '</a>' : $field_value;
				$dupAcc[$id][$field] = $field_value;

				if ($field_value == $player[$field]) {
					$matchRate[$id][$field] = $rateValue[$field]["rate_exact"];
				}
			}
			$thisRealName = $rec['firstName'] . ' ' . $rec['lastName'];
			$dupAcc[$id]['realname'] = $thisRealName;
			if ($thisRealName == $realName) {
				$matchRate[$id]["realname"] = $rateValue["realname"]["rate_exact"];
			} else if (levenshtein($thisRealName, $realName) <= $this::MIN_MATCH_LEVEL) {
				$matchRate[$id]["realname"] = $rateValue["realname"]["rate_similar"];
			}

			if (!isset($dupAcc[$id]["username"])) {
				$thisPlayer = $this->ci->duplicate_account_setting->getPlayerDetails($id);
				foreach ($this->search1 as $field) {
					$value = ( $field == "username" ) ? '<a href="/player_management/userInformation/' . $id . '" target="_blank">' . @$thisPlayer[$field] . $playerAffiliate . '</a>' : @$thisPlayer[$field];
					$dupAcc[$id][$field] = $value;
				}
			}
		}

		if (count($dupData["http_request"]) > 0) {
			$playerHttp = $dupData["http_request"][0];
			$dupDataHttp = $dupData["http_request"][1];

			foreach ($dupDataHttp as $rec) {
				$id = $rec["playerId"];
				if (!isset($dupAcc[$id])) {
					$dupAcc[$id] = array();
					$matchRate[$id] = array();
				}

				$action = '<input type="checkbox" id="tags" name="set_tag[]" value="' . $id . '" />';
				$dupAcc[$id]['action'] = $action;

				$type = $rec["type"];
				foreach ($this->search3 as $field) {
					$field_value = $rec[$field];
					if ($field_value != null) {
						$field_value = trim($field_value);
					}
					// if field data of player is empty no need to search
					if ($field_value == null || $field_value == "") {
						continue;
					}
					$keyStr = $field;
					if ($keyStr == "ip") {
						$keyStr .= $type;
					}
					foreach ($playerHttp[$type] as $playerRec) {
						if ($field_value == $playerRec[$field]) {
							$dupAcc[$id][$keyStr] = $field_value;
							$matchRate[$id][$keyStr] = $rateValue[$field]["rate_exact"];
						}
					}
				}
				if (!isset($dupAcc[$id]["username"])) {

					$affiliate = $this->ci->player->getPlayerAffiliateUsername( $id );
					$playerAffiliate = ( ! empty( $affiliate ) ) ? ' (' . $affiliate . ')' : '';

					$thisPlayer = $this->ci->duplicate_account_setting->getPlayerDetails($id);
					foreach ($this->search1 as $field) {
						$value = '';

						if( isset($thisPlayer[$field]) ){

							$value = ( $field == "username" ) ? '<a href="/player_management/userInformation/' . $id . '" target="_blank">' . $thisPlayer[$field] . $playerAffiliate . '</a>' : $thisPlayer[$field];

						}

						$dupAcc[$id][$field] = $value;
						// $dupAcc[$id][$field] = @$thisPlayer[$field];
					}
				}
			}
		}

		// calculate total duplicate rate
		foreach ($dupAcc as $id => $rec) {
			if(!empty($rec['username'])){
				if (!isset($matchRate[$id])) {
					// error!!!
				}
				$matchInfo = $matchRate[$id];

				$totalRate = 0;

				foreach ($rec as $field => $fieldValue) {
					if ((!isset($matchInfo[$field])) || $matchInfo[$field] == "") {
						continue;
					}
					$totalRate += $matchInfo[$field];
				}
				// It is possible that totalRate = 0
				// because we fetch data from DB tables using LIKE clause
				// while we give rates using levenshtein()
				$dupAcc[$id]['totalRate'] = $totalRate;
			}
		}
		$dupInfo = array();
		$dupInfo[0] = $dupAcc;
		$dupInfo[1] = $matchRate;

		return $dupInfo;
	}

	public function getDupEnableColumnCondition()
    {
        $condition = [];
        $dupConditionColumn = Duplicate_account_setting::DUP_CORRESPOND_CONDITION_NAME;
        $dupConditionColumnEnabled = $this->ci->config->item('duplicate_account_info_enalbed_condition');

        foreach ($dupConditionColumn as $key => $name)
        {
            if (in_array($name, $dupConditionColumnEnabled)) {
                $condition[] = $key;
            }
        }

        return [
            'where_in' => ['item_id', $condition]
        ];
    }
}
