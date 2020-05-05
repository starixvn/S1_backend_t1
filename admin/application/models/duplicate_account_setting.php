<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Duplicate
 *
 * This model represents duplicate data. It operates the following tables:
 * - duplicate_account_setting
 * - player
 * - http_request
 *
 * @author	Johann Merle
 */

class Duplicate_account_setting extends CI_Model {

	const ITEM_USERNAME = 1;
	const ITEM_PASSWORD = 2;
	const ITEM_REAL_NAME = 3;
	const ITEM_MOBILE = 4;
	const ITEM_EMAIL = 5;
	const ITEM_CITY = 6;
	const ITEM_COUNTRY = 7;
	const ITEM_ADDRESS = 8;
	const ITEM_COOKIES = 9;
	const ITEM_REFERER = 10;
	const ITEM_DEVICE = 11;
	const ITEM_IP = 12;
	const ITEM_LOGIN_IP = 13;

    const DUP_CORRESPOND_CONDITION_NAME = [
        self::ITEM_USERNAME  => 'username',
        self::ITEM_PASSWORD  => 'password',
        self::ITEM_REAL_NAME => 'realname',
        self::ITEM_MOBILE    => 'mobile',
        self::ITEM_EMAIL     => 'email',
        self::ITEM_CITY      => 'city',
        self::ITEM_COUNTRY   => 'country',
        self::ITEM_ADDRESS   => 'address',
        self::ITEM_COOKIES   => 'cookie',
        self::ITEM_REFERER   => 'referrer',
        self::ITEM_DEVICE    => 'device',
        self::ITEM_IP        => 'ip',
        self::ITEM_LOGIN_IP  => 'ip',
    ];

	function __construct() {
		parent::__construct();
	}

	/**
	 * get player data by playerId
	 *
	 * @param	int
	 * @return	array
	 */
	public function getPlayerDetails($player_id) {
		$this->db->select('p.*, pd.*');
		$this->db->from('player as p');
		$this->db->join('playerdetails as pd', 'pd.playerId = p.playerId', 'left');
		$this->db->where('p.playerId', $player_id);
        $this->db->where('pd.duplicate_record_exempted', 0);
		$this->db->order_by('p.playerId','desc');

		$query = $this->db->get();

		return $query->row_array();
	}

	/**
	 * get http_request data by playerId
	 *
	 * @param	int
	 * @return	array
	 */
	public function getHTTPRequest($player_id) {
		$this->db->select('*');
		$this->db->from('http_request');
		$this->db->where('playerId', $player_id);
		$this->db->distinct();
		$this->db->order_by('id', 'desc');

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * get http_request data by playerId
	 *
	 * @param	int
	 * @param	int
	 * @return	array
	 */
	public function getHTTPRequestType($player_id, $type) {
		$this->db->select('*');
		$this->db->from('http_request');
		$this->db->where('playerId', $player_id);
		$this->db->where('type', $type);

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * get http_request data by playerId
	 *
	 * @param	int
	 * @return	array
	 */
	public function getHTTPRequestById($http_request_id) {
		$this->db->select('*');
		$this->db->from('http_request');
		$this->db->where('id', $http_request_id);
		$this->db->distinct();

		$query = $this->db->get();

		return $query->row_array();
	}

	/**
	 * search http_request data by playerId, type and value
	 *
	 * @param	int
	 * @param	string
	 * @param	int
	 * @param	string
	 * @return	array
	 */
	public function searchHttpRequest($player_id, $field, $type, $value) {
		$where = null;

		// condition type
		if($type != 0) {
			$where .= " AND type = " . $type . "";
		}

		// field to search
		if(count($value) > 1) {
			$content = '';
			foreach ($value as $key=> $val){
				$content .= "'".$val."'";
				$content .=$key==(count($value)-1)?"":",";
			}
			$where .=" AND ".$field." IN (".$content.") ";
		} else {
			foreach ($value as $key => $val) {
				$where .= " AND hr." . $field . " = '" . $val . "'";
			}
		}

		$query = $this->db->query("SELECT DISTINCT hr.* FROM http_request as hr
			WHERE hr.playerId != '" . $player_id . "'
			$where
			GROUP BY playerId, ip, referrer, type
		");

		return $query->result_array();
	}

	/**
	 * get all items
	 *
	 *
	 * @return	array
	 */
	public function getAllItems($condition = []) {
		//$items = array(7, 9, 10, 11);

		$this->db->select('*');
		$this->db->from('duplicate_account_setting');
		//$this->db->where_not_in('id',$items);
        if ($condition) {
            if (isset($condition['where_in'])) {
                $c_in = $condition['where_in'];
                $c_in[1] = ($c_in[1]) ? : [0];
                $this->db->where_in($c_in[0], $c_in[1]);
            }
        }

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * search for duplicates
	 *
	 * @param	string
	 * @param	string
	 * @param	int
	 * @return	array
	 */
	public function searchForDuplicates($field, $field_value, $player_id) {
		$where = null;

		if ($field == "username" || $field == "firstName" || $field == "lastName" || $field == "email") {
			$where = $this->searchDuplicateByLike($field, $field_value);
		} else {
			$where = "WHERE " . $field . " = '" . $field_value . "'";
		}

		$query = $this->db->query("SELECT p.*, pd.* FROM player as p
			LEFT JOIN playerdetails as pd ON p.playerId = pd.playerId
			$where
		");

		$result = $query->result_array();

		$key = array_search($player_id, array_column($result, 'playerId')); // find in array the result with the same playerId
		unset($result[$key]); // remove from array the result with the same playerId
		$reindex_result = array_values($result); // reindex array

		return $reindex_result;
	}

	/**
	 * search for duplicates
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	private function searchDuplicateByLike($field, $field_value) {
		$where = "WHERE " . $field . " LIKE '%" . $field_value . "%'";

		$str_split = str_split($field_value);
		$word_count = count($str_split) - 1;
		$word = $str_split[0];

		for ($i=1; $i < $word_count ; $i++) {
			$word .= $str_split[$i];

			$where .= "OR " . $field . " LIKE '%" . $word . "%'";
		}

		return $where;
	}

	/**
	 * get HTTP Request by playerId and type
	 *
	 * @param	array
	 * @return	array
	 */
	public function getHttpRequestByData($data) {
		$this->db->select('*');
		$this->db->from('http_request');
		$this->db->where('playerId', $data['playerId']);
		$this->db->where('ip', $data['ip']);
		$this->db->where('referrer', $data['referrer']);
		$this->db->where('user_agent', $data['user_agent']);
		$this->db->where('os', $data['os']);
		$this->db->where('device', $data['device']);
		$this->db->where('is_mobile', $data['is_mobile']);
		$this->db->where('type', $data['type']);

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * insert HTTP Request
	 *
	 * @param	array
	 * @return	void
	 */
	public function insertHttpRequest($data) {
		$this->db->insert('http_request', $data);
	}

	/**
	 * save modified Duplicate Account Setting
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function saveDuplicateAccountSetting($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('duplicate_account_setting', $data);
	}

	/**
	 * get modified Duplicate Account Setting
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function getDuplicateAccountSetting($item) {
		$this->db->select('*');
		$this->db->from('duplicate_account_setting');
		$this->db->where('item_id', $item);

		$query = $this->db->get();

		return $query->row_array();
	}

    // Added by William Zhang. 2016-04
    /**
     * fetch all possible duplicate accounts from table player and playerdetails
     *
     * remove join operation on player and playerdetails. search the 2 tables
     * saperately instead.
     *
     * @param array
     * @param string array
     * @return array
     */
    public function getDupRecsFromTable($player, $tableName, $searchKeys) {
        // create duplicate array according to results
        $dupRecs = array();

        // create where clause
		$where = $this->createWhereClause($player, $searchKeys);

        // It is possible that all considered fields are empty in playerdetails.
        if ($where != ""){
            $playerId = $player['playerId'];
            $where = "WHERE (playerId != ". $playerId. " AND duplicate_record_exempted = 0) AND (".$where.")";
            $sqlStr = 'SELECT * FROM '. $tableName . '  ' . $where;
            $query = $this->db->query($sqlStr);
            $dupRecs = $query->result_array();
        }

        return $dupRecs;
    }

    /**
     * create where clause
     *
     * @param   array  curret player info
     * @param   array  considered fields; also column names in table
     * @return  String where clause
     */
    private function createWhereClause($player, $searchKeys) {
        $where = "";

        $i = 0;
        foreach ($searchKeys as $field) {
            $fieldValue = $player[$field];
            if ($fieldValue != null) {
                $fieldValue = trim($fieldValue);
            }

            // no need to search on this field if the field data is empty
            if($fieldValue != null && $fieldValue != "") {
                if ($i > 0) {
                    $where .= " OR ";
                }
                // similar match is done on the following 4 fields
                if ($field == "username" || $field == "firstName" || $field == "lastName" || $field == "email") {
                    $where .= $this->createWhereByLike($field, $fieldValue);
                } else {
                    // exact match otherwise
                    $where .= $field . " = '" . $fieldValue . "'";
                }
                $i++;
            }
        }
        return $where;
    }

    /**
     * create one like part of those in where clause
     *
     * @param	string
     * @param	string
     * @return	string
     */
    private function createWhereByLike($field, $fieldValue) {
        $where = " (";
        $where.= $field . " LIKE '%" . $fieldValue . "%'";
        $len = strlen($fieldValue);

        if ($len > 4) {
            $where .= " OR " . $field . " LIKE '%" . substr($fieldValue, 0, $len - 1) . "%'";
            $where .= " OR " . $field . " LIKE '%" . substr($fieldValue, 0, $len - 2) . "%'";
            $where .= " OR " . $field . " LIKE '%" . substr($fieldValue, 0, $len - 3) . "%'";
        }

        $where .= ")";

        return $where;
    }

    /**
     * get duplicate info from http_request
     *
     * duplicate info will be categrized by type which is absolutely not null!
     *
     * @param   int
     * @param   array
     * @return	array
     */
    public function getDupRecsFromHttpRequest($playerId, $searchKeys){
        $selectItems = array_merge($searchKeys, array('playerId', 'type'));

        $playerInfo = $this->getDistinctRecsHttpRequest($playerId, $selectItems);

        $dupRecs = array();

        if (count($playerInfo) > 0) {
            $listByType = array();
            foreach ($playerInfo as $rec) {
                $type = $rec['type'];
                //checking if request type is created in array or not
                if(!isset($listByType[$type])) {
                    $listByType[$type] = array();
                }
                //array_push($listByType[$type], $rec);
                $listByType[$type][] = $rec;
            }
            $dupRecs[0] = $listByType;

            // create where clause
            $where = $this->createWhereForHttpRequest($playerId, $searchKeys, $listByType);

            $this->db->select($selectItems);
            $this->db->from('http_request');
            $this->db->where($where);
            $this->db->distinct();
            $query = $this->db->get();
            $dupRecs[1] = $query->result_array();
        }

        /*
         * dupRecs may be empty or have 2 elements in which the 1st one is info for current player
         * while the 2nd one is for duplicate accounts
         */
        return $dupRecs;
    }

	/**
	 * get distinct records from http_request by playerId
	 *
	 * @param	int
	 * @return	array
	 */
    private function getDistinctRecsHttpRequest($playerId, $selectItems) {
        $this->db->select($selectItems);
        $this->db->from('http_request');
        $this->db->where('playerId', $playerId);
        $this->db->distinct();
        $query = $this->db->get();
        return $query->result_array();
	}

    /**
     * create where clause for duplicate account checking in http_request
     *
     * @param int
     * @param array
     * @param array
     * @return string
     */
    private function createWhereForHttpRequest($playerId, $searchKeys, $listByType) {
        // set considered fields in which 'ip' is the most important one
        //$searchFields = $searchKeys;
        // $searchFields = array('ip');
        $field='ip';

        $where = "playerId != ".$playerId;
        $where .= " AND (";

        $where .= "(";
        $i1 = 0;
        foreach ($listByType as $type=>$recs) {
            if ($i1 > 0){
                $where .= ") OR (";
            }
            $i1++;
            $where .= "type = ".$type." AND (";

            $valArr=[];
            foreach($recs as $rec){
                // foreach($searchFields as $field) {
                    $fieldValue = $rec[$field];
                    if ($fieldValue != null) {
                        $fieldValue = trim($fieldValue);
                    }
                    if ($fieldValue == null || $fieldValue == '') {
                        continue;
                    }

                    $valArr[]="'".$fieldValue."'";
                // }
            }
            if(!empty($valArr)){
                $valArr=array_unique($valArr);
                $where.=$field." in (".implode(',', $valArr).") ";
            }

            // $i2 = 0;
            // foreach($recs as $rec){
            //     foreach($searchFields as $field) {
            //         $fieldValue = $rec[$field];
            //         if ($fieldValue != null) {
            //             $fieldValue = trim($fieldValue);
            //         }
            //         if ($fieldValue == null || $fieldValue == '') {
            //             continue;
            //         }
            //         if ($i2 > 0) {
            //             $where .= " OR ";
            //         }
            //         $where .= $field ." = '" . $fieldValue . "'";
            //         $i2++;
            //     }
            // }
            $where .= ")";
        }
        $where .= ")";

        $where .= ")";

        return $where;
    }

    /**
     * get duplicate rate settings from duplicate_account_setting table
     * and re-index the values using search keys
     *
     * fields in table duplicate_account_setting
     * id   | item_id  | rate_exact | rate_similar | status |
     *
     * keys:
     * {'username', 'password', 'email',
     *  'realname', 'phone', 'address', 'city',
     *  'ip', 'cookie', 'referrer', 'device'}
     *
     * @return array for dup rates indexed via search keys
     */
    public function getDupRateSetting() {
		$this->db->select('*');
		$this->db->from('duplicate_account_setting');
		$query = $this->db->get();
        $rateRecs = $query->result_array();

        $rateInfo = array();
        foreach($rateRecs as $rec) {
            switch($rec["item_id"]) {
            case $this::ITEM_USERNAME:
                $rateInfo["username"] = array();
                $rateInfo["username"]["rate_exact"] = $rec["rate_exact"];
                $rateInfo["username"]["rate_similar"] = $rec["rate_similar"];
                break;
            case $this::ITEM_PASSWORD:
                $rateInfo["password"] = array();
                $rateInfo["password"]["rate_exact"] = $rec["rate_exact"];
                $rateInfo["password"]["rate_similar"] = $rec["rate_similar"];
                break;
            case $this::ITEM_EMAIL:
                $rateInfo["email"] = array();
                $rateInfo["email"]["rate_exact"] = $rec["rate_exact"];
                $rateInfo["email"]["rate_similar"] = $rec["rate_similar"];
                break;
            case $this::ITEM_REAL_NAME:
                $rateInfo["realname"] = array();
                $rateInfo["realname"]["rate_exact"] = $rec["rate_exact"];
                $rateInfo["realname"]["rate_similar"] = $rec["rate_similar"];
                break;
            case $this::ITEM_MOBILE:
                $rateInfo["phone"] = array();
                $rateInfo["phone"]["rate_exact"] = $rec["rate_exact"];
                $rateInfo["phone"]["rate_similar"] = $rec["rate_similar"];
                break;
            case $this::ITEM_CITY:
                $rateInfo["city"] = array();
                $rateInfo["city"]["rate_exact"] = $rec["rate_exact"];
                $rateInfo["city"]["rate_similar"] = $rec["rate_similar"];
                break;
            case $this::ITEM_COUNTRY:
                $rateInfo["country"] = array();
                $rateInfo["country"]["rate_exact"] = $rec["rate_exact"];
                $rateInfo["country"]["rate_similar"] = $rec["rate_similar"];
                break;
            case $this::ITEM_ADDRESS:
                $rateInfo["address"] = array();
                $rateInfo["address"]["rate_exact"] = $rec["rate_exact"];
                $rateInfo["address"]["rate_similar"] = $rec["rate_similar"];
                break;
            case $this::ITEM_IP:
                $rateInfo["ip"] = array();
                $rateInfo["ip"]["rate_exact"] = $rec["rate_exact"];
                $rateInfo["ip"]["rate_similar"] = $rec["rate_similar"];
                break;
            case $this::ITEM_COOKIES:
                $rateInfo["cookie"] = array();
                $rateInfo["cookie"]["rate_exact"] = $rec["rate_exact"];
                $rateInfo["cookie"]["rate_similar"] = $rec["rate_similar"];
                break;
            case $this::ITEM_DEVICE:
                $rateInfo["device"] = array();
                $rateInfo["device"]["rate_exact"] = $rec["rate_exact"];
                $rateInfo["device"]["rate_similar"] = $rec["rate_similar"];
                break;
            case $this::ITEM_REFERER:
                $rateInfo["referrer"] = array();
                $rateInfo["referrer"]["rate_exact"] = $rec["rate_exact"];
                $rateInfo["referrer"]["rate_similar"] = $rec["rate_similar"];
                break;
			case $this::ITEM_LOGIN_IP:
				$rateInfo["referrer"] = array();
				$rateInfo["referrer"]["rate_exact"] = $rec["rate_exact"];
				$rateInfo["referrer"]["rate_similar"] = $rec["rate_similar"];
				break;
            default:
                //error!!!
            }
        }
        return $rateInfo;
    }

    /**
     * get player info from table player
     *
     * @return array
     */
    public function getPlayerInfo($num = 0) {
        $this->db->select('*');
        $this->db->from('player');
        if ($num > 0) {
            $this->db->limit($num);
        }
        $query = $this->db->get();

        return $query->result_array();
    }

	public function getLoginItem($itemId) {
		$this->db->select('rate_exact');
		$this->db->from('duplicate_account_setting');
		$this->db->where('id', $itemId);
		$query = $this->db->get();

		$item = '';
		if($query->row()) {
			$item = '('.$query->row()->rate_exact.')';
		}
		return $item;
	}

	public function getHTTPRequestLogin($player_id) {
		$this->db->select('*');
		$this->db->from('http_request');
		$this->db->where('playerId', $player_id);
		$this->db->like('referrer', 'auth/login');
		$query = $this->db->get();

		$loginIp = '';
		if($query->num_rows()>0) {
			$loginIp = $query->row()->ip . $this->getLoginItem(self::ITEM_LOGIN_IP);
		}

		return $loginIp;
	}

}
