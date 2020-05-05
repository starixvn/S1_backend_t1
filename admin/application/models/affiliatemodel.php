<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 *
 * * Get affiliates data
 * * Get/add/edit/delete payments data
 * * Banner details
 * * Get monthly earnings
 * * Decline/approve payment
 * * Statistics
 * * Payment details
 * * Wallet details
 *
 * @category Affiliate Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Affiliatemodel extends BaseModel {

	function __construct() {
		parent::__construct();
        $this->load->model('game_logs');
	}

	protected $tableName = 'affiliates';

	const INCOME_CONFIG_TYPE_BET_WIN = 1;
	const INCOME_CONFIG_TYPE_DEPOSIT_WITHDRAWAL = 2;
	const INCOME_CONFIG_TYPE_BET_WIN_TOTALCOMMISSION = 3;
	const INCOME_CONFIG_TYPE_BET_WIN_ACTIVEPLAYERCOMMISSION = 4;
	const INCOME_CONFIG_TYPE_BET_WIN_ACTIVEPLAYERCOMMISSIONBYGAMEPLATFORM = 5;

	const OPTION_AFFILIATE_DEFAULT_TERMS = 'affiliate_default_terms';
	const OPTION_SUB_AFFILIATE_DEFAULT_TERMS = 'sub_affiliate_default_terms';

	const REGISTERED_BY_IMPORTER = 'importer';
	const REGISTERED_BY_WEBSITE = 'website';
	const REGISTERED_BY_MASS_ACCOUNT = 'mass_account';

	const ACTIVE_DOMAIN = '0';

	const STATUS_DELETED = 2;

	const STATUS_WITHDRAW_REQUEST = 1;
	const STATUS_WITHDRAW_APPROVED = 2;
	const STATUS_WITHDRAW_DECLINED = 3;

	const PARENT_AFFILIATE = 0;

	const TRACKING_TYPE_CODE = 1;
	const TRACKING_TYPE_DOMAIN = 2;
	const TRACKING_TYPE_SOURCE_CODE = 3;

	//affiliate domain visibility options
	const HIDDEN_TO_AFFILIATES = 0;
	const SHOW_TO_ALL_AFFILIATES = 1;
	const SHOW_TO_SELECTED_AFFILIATES = 2;

	const DEPOSIT_TO_AFFILIATE = 20;

	const DEFAULT_AFFILIATE_SETTINGS = [
		"baseIncomeConfig" => self::INCOME_CONFIG_TYPE_BET_WIN,
		"level_master" => 50,
		"admin_fee" => 0,
		"transaction_fee" => 0,
		"bonus_fee" => 0,
		"cashback_fee" => 100,
		"minimumPayAmount" => 0,
		"paymentSchedule" => -1,
		"paymentDay" => 1,
		"autoTransferToWallet" => false,
		"autoTransferToLockedWallet" => false,
		// "terms_type"=> "option1",
		"totalactiveplayer" => 10,
		"minimumBetting" => 1000,
		"minimumBettingTimes" => 5,
		"minimumDeposit" => 100,
		"provider" => [], // api id
		"provider_betting_amount" => [], // api=>amount
		"manual_open" => true,
		"sub_link" => true,
		"sub_level" => 10,
		"sub_levels" => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
		"platform_shares" => [],
		"enable_commission_by_tier" => false,
		"commission_tier_settings" => [],
		"tier_provider" => [],
        "enable_transaction_fee" => false,
        "split_transaction_fee" => false,
        "transaction_deposit_fee" => 0,
        "transaction_withdrawal_fee" => 0,
    ];

	//'{"baseIncomeConfig": "2",
	//"level_master":"50",
	//"minimumPayAmount": "0",
	//"paymentDay": "1",
	//"admin_fee": "0",
	//"transaction_fee": "0",
	//"bonus_fee": "0",
	//"cashback_fee": "100"}';

	/**
	 * overview : get all affiliates from affiliate table
	 *
	 * @param	string	$username
	 * @param	string	$encryptedPassword
	 * @return	array
	 */
	public function login($username, $encryptedPassword, $password=null, &$message=null) {

		if(empty($username) || empty($encryptedPassword)){
			return null;
		}
		$result=null;
		$this->db->from('affiliates')->where('username', $username);
		$row=$this->runOneRowArray();

		$this->utils->debug_log('load username: '.$username, $row);

		if(!empty($row)){
			//found aff
			//check password
			if(empty($row['password'])){
				//if empty password in db
				$affiliateId=$row['affiliateId'];
				if(empty($password)){
					$password=$this->utils->decodePassword($encryptedPassword);
				}
				//try call external api
				$logged=$this->utils->login_external_for_affiliate($affiliateId, $username, $password, $message);
				if($logged){
					//update affiliate password
					$this->db->set('password', $encryptedPassword)->where('affiliateId', $affiliateId);
					$this->runAnyUpdate('affiliates');
					$this->utils->debug_log('update affiliate password: '.$affiliateId, $encryptedPassword, $password);
					$result=$row;
				}
			}else if($row['password']==$encryptedPassword){
				$result=$row;
			}else{
				$message='Password Not Match';
			}
		}

		return $result;
	}

	public function readonly_login($affiliate_username, $readonly_username, $password) {
		$sql = "SELECT a.*, aroa.username username FROM affiliates a JOIN affiliate_read_only_account aroa ON aroa.affiliate_id = a.affiliateId where a.username = ? and aroa.username = ? and aroa.password = ?";
		$query = $this->db->query($sql, array($affiliate_username, $readonly_username, $password));
		return $query->row_array();
	}

	/**
	 * overview : get all affiliates from affiliate table
     * @param array	$data
	 * @return	array
	 */
	public function getAllAffiliates($data = null) {
        $this->db->select('*')->from($this->tableName)
            ->where('deleted_at IS NULL');
//            ->where('status != 2');
        if(!empty($data)){
            $this->db->where($data);
        }
        $query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * overview : get all affiliates from affiliate table
	 *
	 * @return	array
	 */
	public function getAllParentAffiliates() {
		$this->db->select('affiliateId,username')->from($this->tableName);
		$this->db->where('parentId', self::PARENT_AFFILIATE);
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * overview : get affiliate by affiliateId from affiliate table
	 *
	 * @param	int		$affiliate_id
	 * @return	array
	 */
	public function getAffiliateById($affiliate_id) {
		$this->db->from('affiliates')->where('affiliateId', $affiliate_id)->where('deleted_at is null');
		return $this->runOneRowArray();
	}

	/**
	 * overview : get affiliate payment by affiliateid from affiliate payment table
	 * @param $affiliate_id
	 * @return array
	 */
	public function getAffiliatePaymentById($affiliate_id) {
		$sql = "SELECT affiliatepayment.*, IFNULL(banktype.bankName, affiliatepayment.bankName) as bankName FROM affiliatepayment LEFT JOIN banktype ON affiliatepayment.bankName = banktype.bankTypeId WHERE affiliatepayment.affiliateId = ?";

		$query = $this->db->query($sql, array($affiliate_id));

		return $query->result_array();
	}

	/**
	 * overview : get affiliate game by affiliateId from affiliategame table
	 *
	 * @param	int		$affiliate_id
	 * @return	array
	 */
	public function getAffiliateGameById($affiliate_id) {
		$sql = "SELECT * FROM affiliategame WHERE affiliateId = ?";

		$query = $this->db->query($sql, array($affiliate_id));

		return $query->result_array();
	}

	/**
	 * overview : add affiliate to affiliate table
	 *
	 * @param	array	$affiliate
	 * @return	int
	 */
	public function addAffiliate($affiliate) {
		// $this->db->insert('affiliates', $affiliate);

		// $query = $this->db->query("SELECT affiliateId FROM affiliates ORDER BY affiliateId DESC");

		// $result = $query->row_array();

		// return $result['affiliateId'];

		$affId = $this->insertData('affiliates', $affiliate);
		$this->updateLevelNumber($affId);
		$this->incCountSub($affiliate['parentId']);

		return $affId;
	}

	/**
	 * overview : add affiliate payout options to affiliatepayout table
	 *
	 * @param	array	$affiliatepayout
	 * @return	int
	 */
	public function addAffiliatePayout($affiliatepayout) {
		$this->db->insert('affiliatepayout', $affiliatepayout);

		$query = $this->db->query("SELECT affiliatePayoutId FROM affiliatepayout ORDER BY affiliatePayoutId DESC");

		$result = $query->row_array();

		return $result['affiliatePayoutId'];
	}

	/**
	 * overview : get registered fields
	 *
	 * @param  type	$type
	 * @return array
	 */
	public function getRegisteredFields($type) {
		$sql = "SELECT * FROM registration_fields WHERE type = ?";

		$query = $this->db->query($sql, array($type));

		return $query->result_array();
	}

	/**
	 * overview : edit affiliates by affiliateId to affiliates table
	 *
	 * @param	array	$data
	 * @param	int		$affiliate_id
	 */
	public function editAffiliates($data, $affiliate_id) {
		$this->db->where('affiliateId', $affiliate_id);
		$this->db->update('affiliates', $data);
	}

	/**
	 * overview : get all payment method of affiliate
	 *
	 * @param	int		$affiliate_id
	 * @return	int
	 */
	public function getPaymentById($affiliate_id) {
		$sql = "SELECT affiliatepayment.*, IFNULL(banktype.bankName, affiliatepayment.bankName) as bankName FROM affiliatepayment LEFT JOIN banktype ON affiliatepayment.bankName = banktype.bankTypeId WHERE affiliatepayment.affiliateId = ?";

		$query = $this->db->query($sql, array($affiliate_id));

		return $query->result_array();
	}

	/**
	 * overview : add payment
	 *
	 * @param	array	$data
	 */
	public function addPayment($data) {
		$this->db->insert('affiliatepayment', $data);
	}

	/**
	 * overview : edit payment
	 *
	 * @param	int	 $data
	 * @param	int	 $payment_id
	 */
	public function editPayment($data, $payment_id) {
		$this->db->where('affiliatePaymentId', $payment_id);
		$this->db->update('affiliatepayment', $data);
	}

	/**
	 * overview : delete payment bank info
	 *
	 * @param	int
	 */
	public function deletePaymentInfo($payment_id) {
		$this->db->where('affiliatePaymentId', $payment_id);
		$this->db->delete('affiliatepayment');
	}

	/**
	 * get all payment method of affiliate
	 *
	 * @param	int
	 * @param	int
	 */
	public function getPaymentByPaymentId($affiliate_payment_id) {
		$sql = "SELECT * FROM affiliatepayment WHERE affiliatePaymentId = ?";

		$query = $this->db->query($sql, array($affiliate_payment_id));

		return $query->row_array();
	}

	/**
	 * overview : get all banner details from banner table
	 *
	 * @param	array	$data
	 * @param	int		$limit
	 * @param	int		$offset
	 * @return	array
	 */
	const ACTIVE = 0;
	public function getAllBanner($data, $limit, $offset) {
		$search = null;
		$sortby = null;
		$desc_order = null;

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		if (!empty($data['sort'])) {
			$sortby = 'ORDER BY ' . $data['sort'];

			if (!empty($data['desc'])) {
				if ($data['desc'] == 'desc') {
					$desc_order = "DESC";
				} else {
					$desc_order = "ASC";
				}
			}
		}

		if (is_array($data)) {
			foreach ($data as $key => $value) {
				if ($key == 'sign_time_period' && $value != '') {
					if ($value == 'week') {
						$search[$key] = "b.createdOn >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
					} elseif ($value == 'month') {
						$search[$key] = "b.createdOn >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
					} elseif ($value == 'past') {
						$search[$key] = "b.createdOn >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)";
					}
				} elseif ($key == 'signup_range' && $value != '') {
					$search[$key] = "b.createdOn BETWEEN $value";
				} elseif ($key == 'status' && $value != null) {
					if ($value == 'active') {
						$search[$key] = "b.status = '0'";
					} elseif ($value == 'inactive') {
						$search[$key] = "b.status = '1'";
					} else {
						$search[$key] = "b.status = '2'";
					}

				} elseif ($key == 'sort' || $key == 'desc') {
					continue;
				} elseif ($value != null) {
					$search[$key] = "a.$key LIKE '%" . $value . "%'";
				}
			}
		}

		$query = "SELECT *, (b.width * b.height) as size  FROM banner as b" . " WHERE status = '" . self::ACTIVE . "'";

		if (count($search) > 0) {
			$query .= " WHERE " . implode(' AND ', $search);
		}
		$run = $this->db->query("$query $sortby $desc_order $limit $offset");
		// $this->utils->printLastSQL();
		return $run->result_array();
	}

	/**
	 * overview : get all players under affiliate in players table
	 *
	 * @param	int		$affiliate_id
	 * @param	date	$date_from
	 * @param	date	$date_to
	 * @return	array
	 */
	public function getAllPlayersUnderAffiliate($affiliate_id, $date_from, $date_to) {
		$this->db->select('player.*')->from('player')
			// ->join('playeraccount', 'playeraccount.playerId=player.playerId and playeraccount.type="affiliate"', 'left')
			->where('player.affiliateId', $affiliate_id);

		if (!empty($date_from) && !empty($date_to)) {
			$this->db->where('player.createdOn >=', $date_from)->where('player.createdOn <=', $date_to);
		}
//var_dump($this->runMultipleRowArray()); exit();
		return $this->runMultipleRowArray();

		// $where = null;

		// if (!empty($date_from) && !empty($date_to)) {
		// 	$where = "AND p.createdOn BETWEEN '" . $date_from . "' AND '" . $date_to . "'";
		// }

		// $sql = "SELECT p.*, pa.playerAccountId FROM playeraccount as pa
		// 	LEFT JOIN player as p
		// 	ON pa.playerId = p.playerId
		// 	where pa.type = ? AND pa.typeId = ?
		// 	$where";

		// $query = $this->db->query($sql, array('affiliate', $affiliate_id));

		// return $query->result_array();
	}

	/**
	 * overview : get all deposit of players under affiliate in walletaccount table
	 *
	 * @param	int		$affiliate_id
	 * @param	date	$date_from
	 * @param	date	$date_to
	 * @return	array
	 */
	public function getAllPlayersDepositUnderAffiliate($affiliate_id, $date_from, $date_to) {
		$where = null;

		if (!empty($date_from)) {
			$where = "AND wa.processDatetime BETWEEN '" . $date_from . "' AND '" . $date_to . "'";
		}

		$players = $this->getPlayersAccountId($affiliate_id);

		if (empty($players)) {
			return array('amount' => '', 'count' => 0);
		}

		$sql = "SELECT SUM(wa.amount) as amount, COUNT(wa.walletAccountId) as count FROM playeraccount as pa
			LEFT JOIN walletaccount as wa
			ON pa.playerAccountId = wa.playerAccountId
			where pa.playerId IN ($players)
			AND wa.dwStatus = ?
			AND wa.transactionType = ?
			AND pa.type = ?
			$where";

		$query = $this->db->query($sql, array('approved', 'deposit', 'wallet'));

		$result = $query->row_array();

		return $result;
	}

	/**
	 * overview : get all withdraw of players under affiliate in walletaccount table
	 *
	 * @param	int		$affiliate_id
	 * @param	date	$date_from
	 * @param	date	$date_to
	 * @return	array
	 */
	public function getAllPlayersWithdrawUnderAffiliate($affiliate_id, $date_from, $date_to) {
		$where = null;

		if (!empty($date_from)) {
			$where = "AND wa.processDatetime BETWEEN '" . $date_from . "' AND '" . $date_to . "'";
		}

		$players = $this->getPlayersAccountId($affiliate_id);

		if (empty($players)) {
			return 0;
		}

		$sql = "SELECT SUM(wa.amount) as amount FROM playeraccount as pa
			LEFT JOIN walletaccount as wa
			ON pa.playerAccountId = wa.playerAccountId
			where pa.playerId IN ($players)
			AND wa.dwStatus = ?
			AND wa.transactionType = ?
			AND pa.type = ?
			$where";

		$query = $this->db->query($sql, array('approved', 'withdrawal', 'wallet'));

		$result = $query->row_array();

		return $result['amount'];
	}

	/**
	 * overview : get all players and return their accountid
	 *
	 * @param  int	$affiliate_id
	 * @return	string
	 */
	public function getPlayersAccountId($affiliate_id) {
		$players = $this->getAllPlayersUnderAffiliate($affiliate_id, null, null);
		$count = 0;
		$player_id = null;

		foreach ($players as $key => $value) {
			if ($count == 0) {
				$player_id = "'" . $value['playerId'] . "'";
			} else {
				$player_id .= ", '" . $value['playerId'] . "'";
			}
			$count++;
		}

		return $player_id;
	}

	const DOMAIN_STATUS_ENABLED = '0';
	const DOMAIN_STATUS_DISABLED = '1';

	/**
	 * overview : get all domains from domain table
	 *
	 * @return	array
	 */
	public function getAllDomain() {
		$query = $this->db
			->select(array('domain.*', 'COUNT(affiliate_domain.domainId) affiliates'))
			->join('affiliate_domain', 'affiliate_domain.domainId = domain.domainId', 'left')
			->where('status', self::DOMAIN_STATUS_ENABLED)
			->group_by('domain.domainId')
			->get('domain');
		return $query->result_array();
	}

	/**
	 * overview : get all uri segments
	 *
	 * @return array
	 */
	function getUriSegments() {
		return explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
	}

	function getUriSegment($n) {
		$segs = $this->getUriSegments();
		return count($segs) > 0 && count($segs) >= ($n - 1) ? $segs[$n] : '';
	}

	/**
	 * overview : get traffic stats base on conditions
	 *
	 * @param	date	$start_date
	 * @param	date	$end_date
	 * @return	array
	 */
	public function getTrafficStats($start_date, $end_date) {
		$search = array(
			'start_date' => $this->session->userdata('start_date'),
			'end_date' => $this->session->userdata('end_date'),
			'username' => $this->session->userdata('username'),
			'type_date' => $this->session->userdata('type_date'),
		);

		$segment = $this->getUriSegment(2);

		$where = "WHERE ts.affiliateId = '" . $this->session->userdata('affiliateId') . "'";

		if (!empty($search['start_date']) && ($segment != 'viewTrafficStatisticsToday' && $segment != 'viewTrafficStatisticsDaily' && $segment != 'viewTrafficStatisticsWeekly')) {
			$where .= " AND ts.date BETWEEN '" . $search['start_date'] . "' AND '" . $search['end_date'] . "'";
		} elseif (!empty($start_date)) {
			$where .= " AND ts.date BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
		}

		if (!empty($search['start_date']) && $search['type_date'] == 'registration date') {
			$where .= " AND a.createdOn BETWEEN '" . $search['start_date'] . "' AND '" . $search['end_date'] . "'";
		} else if (!empty($start_date) && $search['type_date'] == 'login date') {
			$where .= " AND a.lastLogin BETWEEN '" . $search['start_date'] . "' AND '" . $search['end_date'] . "'";
		}

		if ($search['username'] != null) {
			$where .= " AND p.username LIKE '%" . $search['username'] . "%'";
		}

		$query = "SELECT ts.*, p.username, CONCAT(pd.firstname, ' ', pd.lastname) as realname, pd.country as location, pd.registrationIP as ip_address FROM traffic_stats as ts
			LEFT JOIN player as p
			ON ts.playerId = p.playerId
			LEFT JOIN playerdetails as pd
			ON p.playerId = pd.playerId
			LEFT JOIN affiliates as a
			ON ts.affiliateId = a.affiliateId
			$where
			ORDER BY trafficId DESC
		";

		$run = $this->db->query("$query");

		return $run->result_array();
	}

	/**
	 * overview : insert traffic stats
	 *
	 * @param	array	$data
	 * @return	void
	 */
	public function insertTrafficStats($data) {
		$this->db->insert('traffic_stats', $data);
	}

	/**
	 * overview : get players by traffic id
	 *
	 * @param	int		$traffic_id
	 * @return	string
	 */
	public function getTrafficById($traffic_id) {
		$sql = "SELECT * FROM traffic_stats WHERE trafficId = ?";

		$query = $this->db->query($sql, array($traffic_id));

		return $query->row_array();
	}

	/**
	 * overview : get player details
	 *
	 * @param	int		$player_id
	 * @return	string
	 */
	public function getPlayers($player_id) {
		$sql = "SELECT p.username, p.createdOn, p.lastLoginTime,
			(SELECT wa.dwDateTime FROM playeraccount as pa LEFT JOIN walletaccount as wa ON pa.playerAccountId = wa.playerAccountId where pa.playerId = '$player_id' AND wa.dwStatus = 'approved' AND wa.transactionType = 'deposit' AND pa.type = 'wallet' ORDER BY wa.dwDateTime ASC LIMIT 1) as first_deposit_date,
			(SELECT SUM(wa.amount) FROM playeraccount as pa LEFT JOIN walletaccount as wa ON pa.playerAccountId = wa.playerAccountId where pa.playerId = '$player_id' AND wa.dwStatus = 'approved' AND wa.transactionType = 'deposit' AND pa.type = 'wallet') as deposit_amount,
			(SELECT SUM(wa.amount) FROM playeraccount as pa LEFT JOIN walletaccount as wa ON pa.playerAccountId = wa.playerAccountId where pa.playerId = '$player_id' AND wa.dwStatus = 'approved' AND wa.transactionType = 'withdrawal' AND pa.type = 'wallet') as withdrawal_amount,
			(SELECT SUM(gar.bets) FROM gameapirecord as gar WHERE gar.playerName = p.username AND gar.apitype = '1') + (SELECT SUM(gar.betAmount) FROM gameapirecord as gar WHERE gar.playerName = p.username AND gar.apitype = '2' AND dataType IN ('BR', 'EBR')) as bets,
			(SELECT SUM(gar.wins) FROM gameapirecord as gar WHERE gar.playerName = p.username AND gar.apitype = '1') + (SELECT SUM(gar.netAmount) FROM gameapirecord as gar WHERE gar.playerName = p.username AND gar.apitype = '2' AND dataType IN ('BR', 'EBR') AND gar.netAmount > 0) as wins,
			FROM player as p
 			WHERE p.playerId = ?";

		$query = $this->db->query($sql, array($player_id));

		return $query->row_array();
	}

	/**
	 * overview : get players deposit
	 *
	 * @param	date	$start_date
	 * @param	date	$end_date
	 * @return	array
	 */
	public function getPlayersDeposit($start_date, $end_date) {
		$sql = "SELECT DISTINCT p.username, p.createdOn, p.lastLoginTime,
			(SELECT wa.processDatetime FROM walletaccount as wa WHERE wa.playerAccountId = pa.playerAccountId ORDER BY createdOn ASC LIMIT 1) as first_deposit_date,
			(SELECT SUM(wa.amount) FROM walletaccount as wa WHERE wa.playerAccountId = pa.playerAccountId AND wa.processDatetime BETWEEN ? AND ?) as deposit_amount
			FROM walletaccount as wa
			LEFT JOIN playeraccount as pa
			ON wa.playerAccountId = pa.playerAccountId
			LEFT JOIN player as p
			ON pa.playerId = p.playerId
			WHERE wa.dwStatus = ?
			AND wa.transactionType = ?
			AND pa.type = ?
			AND wa.processDatetime BETWEEN ? AND ?";

		$query = $this->db->query($sql, array($start_date, $end_date, 'approved', 'deposit', 'wallet', $start_date, $end_date));

		/*echo "<pre>";
			print_r($query->result_array());
			echo "</pre>";
		*/

		return $query->result_array();
	}

	/**
	 * overview : get monthly earnings base on conditions
	 *
	 * @param	array	$start_date
	 * @param	int		$end_date
	 * @param	int
	 * @return	array
	 */
	public function getEarnings($start_date, $end_date) {
		$where = "";

		if (!empty($start_date)) {
			$where .= " AND me.createdOn BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
		}

		$sql = "SELECT me.* FROM affiliatemonthlyearnings as me
			WHERE me.affiliateId = ?
			AND me.status = ?
			$where
			ORDER BY me.affiliateMonthlyEarningsId ASC";

		$query = $this->db->query($sql, array($this->session->userdata('affiliateId'), '1'));

		return $query->result_array();
	}

	/**
	 * overview : get payment history base on conditions
	 *
	 * @param	array	$data
	 * @return	array
	 */
	public function getPaymentHistory($data) {
		$search = null;

		if (is_array($data)) {
			foreach ($data as $key => $value) {
				if ($key == 'sign_time_period' && $value != '') {
					if ($value == 'week') {
						$search[$key] = "p.createdOn >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
					} elseif ($value == 'month') {
						$search[$key] = "p.createdOn >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
					} elseif ($value == 'past') {
						$search[$key] = "p.createdOn >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)";
					}
				} elseif ($key == 'signup_range' && $value != '') {
					$search[$key] = "p.createdOn BETWEEN $value";
				} elseif ($key == 'status' && $value != null) {
					if ($value == 'approve') {
						$search[$key] = "p.status = '2'";
					} elseif ($value == 'cancel') {
						$search[$key] = "p.status = '4'";
					} else {
						$search[$key] = "p.status = '3'";
					}

				} elseif ($key == 'sort' || $key == 'desc') {
					continue;
				} elseif ($key == 'affiliateId') {
					$search[$key] = "p.$key ='" . $value . "'";
				} elseif ($value != null) {
					$search[$key] = "a.$key LIKE '%" . $value . "%'";
				}
			}
		}

		$query = "SELECT p.*, ap.accountNumber FROM affiliatepaymenthistory as p LEFT JOIN affiliatepayment as ap ON p.affiliatePaymentId = ap.affiliatePaymentId";

		if (count($search) > 0) {
			$query .= " WHERE " . implode(' AND ', $search);
			$query .= " AND p.status NOT IN ('0', '1')";
		} else {
			$query .= " WHERE p.status NOT IN ('0', '1')";
		}

		$run = $this->db->query("$query");

		return $run->result_array();
	}

	/**
	 * overview : get tracking code base on affiliateId
	 *
	 * @param	int		$affiliate_id
	 * @return	array
	 */
	public function getTrackingCodeByAffiliateId($affiliate_id) {
		$sql = "SELECT * FROM affiliates where affiliateId = ?";

		$query = $this->db->query($sql, array($affiliate_id));

		$result = $query->row_array();

		return $result['trackingCode'];
	}

	/**
	 * overview : check if trackingCode is unique
	 *
	 * @param	string	$trackingCode
	 * @return	bool
	 */
	public function checkTrackingCode($trackingCode) {
		$sql = "SELECT * FROM affiliates where trackingCode = ?";

		$query = $this->db->query($sql, array($trackingCode));

		$result = $query->row_array();

		if (!empty($result)) {
			return true;
		}

		return false;
	}

	/**
	 * overview : get email in email table
	 *
	 * @return	array
	 */
	public function getEmail() {
		$query = $this->db->query("SELECT * FROM email");

		return $query->row_array();
	}

	/**
	 * overview : get currency in currency table
	 *
     * @deprecated marked by elvis.php.tw
	 * @return	array
	 */
	public function getCurrency() {
		$sql = "SELECT * FROM currency WHERE status = ?";

		$query = $this->db->query($sql, array(0));

		$result = $query->row_array();

		return $result['currencyCode'];
	}

	/**
	 * overview : get available balance in monthly earnings
	 *
	 * @param 	int		$affiliate_id
	 * @return	array
	 */
	public function getAvailableBalance($affiliate_id) {
		$sql = "SELECT SUM(approved) as amount FROM affiliatemonthlyearnings WHERE affiliateId = ?";

		$request = $this->db->query($sql, array($affiliate_id));

		$result1 = $request->row_array();

		$sql = "SELECT SUM(amount) as amount FROM affiliatepaymenthistory WHERE affiliateId = ? AND status IN ('0', '1', '2', '3')";

		$approved = $this->db->query($sql, array($affiliate_id));

		$result2 = $approved->row_array();

		$balance = $result1['amount'] - $result2['amount'];

		return $balance;
	}

	/**
	 * overview : get pending payment request in payment history
	 *
	 * @param 	int		$affiliate_id
	 * @return	array
	 */
	public function getPendingPayment($affiliate_id) {
		$sql = "SELECT SUM(amount) as amount FROM affiliatepaymenthistory WHERE affiliateId = ? AND status IN ('0', '1')";

		$approved = $this->db->query($sql, array($affiliate_id));

		$result = $approved->row_array();

		return $result['amount'];
	}

	/**
	 * overview : get payment method in affiliate payment
	 *
	 * @param 	int		$affiliate_id
	 * @return	array
	 */
	public function getPaymentMethod($affiliate_id) {
		$sql = "SELECT affiliatepayment.*, IFNULL(banktype.bankName, affiliatepayment.bankName) as bankName FROM affiliatepayment LEFT JOIN banktype ON affiliatepayment.bankName = banktype.bankTypeId WHERE affiliatepayment.affiliateId = ? AND affiliatepayment.status = ?";

		$query = $this->db->query($sql, array($affiliate_id, '0'));

		return $query->result_array();
	}

	/**
	 * overview : add requests to payment history
	 *
	 * @param	array	$data
	 * @return	array
	 */
	public function addRequests($data) {
		$this->db->insert('affiliatepaymenthistory', $data);
	}

	/**
	 * overview : get payment requests
	 *
	 * @param 	int 	$affiliate_id
	 * @return	array
	 */
	public function getPaymentRequests($affiliate_id) {
		$sql = "SELECT aph.*, ap.accountNumber FROM affiliatepaymenthistory as aph
			LEFT JOIN affiliatepayment as ap
			ON aph.affiliatePaymentId = ap.affiliatePaymentId
			WHERE aph.affiliateId = ?
			AND aph.status IN ('0', '1')";

		$query = $this->db->query($sql, array($affiliate_id));

		return $query->result_array();
	}

	/**
	 * overview : cancel requests to payment history
	 *
	 * @param	array	$data
	 * @param	int		$request_id
	 * @return	void
	 */
	public function cancelRequests($data, $request_id) {
		$this->db->where('affiliatePaymentHistoryId', $request_id);
		$this->db->update('affiliatepaymenthistory', $data);
	}

	/**
	 * overview : get all sub affiliates
	 * @param	int $affiliate_id
     * @param datetime $date_from
     * @param datetime $date_to
	 * @return  array
	 */
	public function getAllSubAffiliates($affiliate_id, $date_from = null, $date_to = null) {

		if (is_array($affiliate_id)) {
			$this->db->where_in('parentId', $affiliate_id);
		} else {
			$this->db->where('parentId', $affiliate_id);
		}
        if(!empty($date_from) && !empty($date_to)){
            $this->db->where('createdOn >=', $date_from)->where('createdOn <=', $date_to);
        }

		$this->db->from('affiliates')->where('status !=', self::STATUS_DELETED);

		return $this->runMultipleRowArray();
	}

	/**
	 * overview : get sub affiliates
	 *
	 * @param int $affiliate_id
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getSubAffiliates($affiliate_id, $limit = 0, $offset = 10) {
		$offset = ($limit + 1) * $offset - 1;
		$sql = "SELECT * FROM affiliates
                WHERE parentId = ?
                AND status <= ?
                LIMIT  " . $limit . " , " . $offset;
		$num_rows = $this->db->query($sql, array($affiliate_id, '2'))->num_rows();
		if ($num_rows == 1) {
			return array($this->db->query($sql, array($affiliate_id, '2'))->row_array());
		} else {
			return $this->db->query($sql, array($affiliate_id, '2'))->result_array();
		}

	}

	/**
	 * overview : get affiliate by tracking code
	 *
	 * @param  string $code
	 * @return int
	 */
	public function getAffiliateIdByTrackingCode($code) {
		$this->db->where('trackingCode', $code)->from('affiliates');

		return $this->runOneRowOneField('affiliateId');
	}

	public function getAllPlatformEarningsById($affiliate_id) {
		$this->db->select('*');
		$this->db->select('(game_platform_revenue - game_platform_gross_revenue) as game_platform_fee');
		$this->db->select('(game_platform_admin_fee+game_platform_bonus_fee+game_platform_cashback_fee+game_platform_transaction_fee) as game_platform_total_fee');
		$this->db->from('affiliate_game_platform_earnings');
		$this->db->where('affiliate_id', $affiliate_id);

		$query = $this->db->get();

		return $query->result_array();
	}

	public function getAllDailyEarningsById($affiliate_id) {
		$this->db->from('aff_daily_earnings');
		$this->db->select('id');
		$this->db->select('commission_amount as amount');
		$this->db->select('gross_revenue as gross_net');
		$this->db->select('bonus_fee');
		$this->db->select('transaction_fee');
		$this->db->select('cashback_fee as cashback');
		$this->db->select('admin_fee as admin_fee');
		$this->db->select('net_revenue as net');
		$this->db->select('commission_percentage as rate_for_affiliate');
		$this->db->select('affiliate_id');
		$this->db->select('date');
		$this->db->select('updated_at as updated_at');
		$this->db->select('type');
		$this->db->select('paid_flag');
		$this->db->select('updated_by as processed_by');
		$this->db->select('note');
		$this->db->select('manual_flag');
		$this->db->select('active_players as active_players');
		$this->db->select('total_players as count_players');
		$this->db->select('platform_fee');
		$this->db->where('affiliate_id', $affiliate_id);

		$query = $this->db->get();

		return $query->result();
	}

	/**
	 * overview : get all monthly earnings by id
	 *
	 * @param $affiliate_id
	 * @return array|null
	 */
	public function getAllMonthlyEarningsById($affiliate_id) {
		if ($this->utils->getConfig('use_old_affiliate_commission_formula')) {
			$this->db->select('*');
			$this->db->select('year_month as date');
			$this->db->select('0 as commission_from_sub_affiliates');
			$this->db->select('amount as total_commission');
			$this->db->from('monthly_earnings');
		} else {
			$this->db->from('aff_monthly_earnings');
			$this->db->select('id');
			$this->db->select('commission_amount as amount');
			$this->db->select('commission_from_sub_affiliates');
			$this->db->select('total_commission');
			$this->db->select('gross_revenue as gross_net');
			$this->db->select('bonus_fee');
			$this->db->select('transaction_fee');
			$this->db->select('cashback_fee as cashback');
			$this->db->select('admin_fee as admin_fee');
			$this->db->select('net_revenue as net');
			$this->db->select('commission_percentage as rate_for_affiliate');
			$this->db->select('affiliate_id');
			$this->db->select('year_month as date');
			$this->db->select('updated_at as updated_at');
			$this->db->select('type');
			$this->db->select('paid_flag');
			$this->db->select('updated_by as processed_by');
			$this->db->select('note');
			$this->db->select('manual_flag');
			$this->db->select('active_players as active_players');
			$this->db->select('total_players as count_players');
			$this->db->select('platform_fee');
			$this->db->select('adjustment_notes');
			$this->db->select('total_fee, total_net_revenue, commission_amount_by_tier, commission_amount_breakdown');
		}

		$this->db->where('affiliate_id', $affiliate_id);

		$result = $this->db->get();

		if ($result->num_rows() > 0) {
			// $defaultAllAffiliateSettings = $this->getDefaultAllAffiliateSettings();
			// $affiliate_terms = $this->getAffiliateTermsById($affiliate_id, $defaultAllAffiliateSettings);
			$affiliate_terms = $this->getAffTermsSettings($affiliate_id);

			$data = array();
			foreach ($result->result() as $e) {
				$players_id = array();
				$players_id = $this->getAllPlayersUnderAffiliateId($e->affiliate_id);

				list($start_date, $end_date) = $this->utils->getMonthRange($e->date);

				if ($this->utils->getConfig('use_old_affiliate_commission_formula')) {
					$e->count_players = count($players_id);
					$e->active_players = count($this->filterActivePlayersById($affiliate_terms, $players_id, $start_date, $end_date));
				}

				$data[] = $e;
			}

			return $data;
		} else {
			return null;
		}
	}

	/**
	 * overview : get all affiliates under affiliate id
	 *
	 * @param int	$affiliate_id
	 * @param date  $date_from
	 * @param date  $date_to
	 * @return array
	 */
	public function getAllPlayersUnderAffiliateId($affiliate_id = null, $date_from = null, $date_to = null) {

		$this->db->select('playerId')->from('player')->where('affiliateId IS NOT NULL');

		if ($affiliate_id) {
			if (is_array($affiliate_id)) {
				$this->db->where_in('affiliateId', $affiliate_id);
			} else {
				$this->db->where('affiliateId', $affiliate_id);
			}
		}

		if ($date_from) {
			$this->db->where('createdOn >=', $date_from);
		}

		if ($date_to) {
			$this->db->where('createdOn <=', $date_to);
		}

		$rows = $this->runMultipleRowArray();

		return $rows ? array_column($rows, 'playerId') : array();

	}

	/**
	 * overview : filter active players by id
	 *
	 * @param string $affiliate_terms
	 * @param id 	 $players_ids
	 * @param date   $start_date
	 * @param date 	 $end_date
	 * @param string $use_total
	 * @return array
	 */
	public function filterActivePlayersById($affiliate_terms, $players_ids, $start_date, $end_date, $use_total = 'hour') {
		# INITIALIZE MODEL
		$this->load->model(array('player_model'));

		# INITIALIZE PLAYER ID CONTAINER
		$active_players = array();

		# INITIALIZE YEAR MONTH
		// $year = date('Y');
		// $month = date('m');

		// if (!empty($yearmonth)) {
		// 	$year = substr($yearmonth, 0, 4);
		// 	$month = substr($yearmonth, 4, 6);
		// }

		# VALIDATE PLAYERS_ID
		if (count($players_ids)) {
			# TODO: CHECK ALL PLAYERS_ID
			foreach ($players_ids as $key => $value) {
				if ($this->player_model->isActivePlayer($affiliate_terms, $value, $start_date, $end_date, $use_total)) {
					array_push($active_players, $value);
				}
			}
		}

		return $active_players;
	}

	/**
	 * overview : filter active players by id provider
	 *
	 * @param 	string 		$affiliate_terms
	 * @param 	array		$players_ids
	 * @param 	date		$start_date
	 * @param 	date		$end_date
	 * @param 	int			$providers_id
	 * @param 	int			$count
	 * @return array
	 */
	public function filterActivePlayersByIdByProvider($affiliate_terms, $players_ids, $start_date, $end_date, $providers_id, $count) {
		# INITIALIZE MODEL
		$this->load->model(array('player_model'));

		# INITIALIZE PLAYER ID CONTAINER
		$active_players = array();

		# INITIALIZE YEAR MONTH
		// $year = date('Y');
		// $month = date('m');

		// if (!empty($yearmonth)) {
		// 	$year = substr($yearmonth, 0, 4);
		// 	$month = substr($yearmonth, 4, 6);
		// }

		# VALIDATE PLAYERS_ID
		if (count($players_ids)) {
			# CHECK ALL PLAYERS_ID
			foreach ($players_ids as $key => $value) {
				if ($this->player_model->isActivePlayerByProvider($affiliate_terms, $value, $start_date, $end_date, $providers_id)) {
					array_push($active_players, $value);
				}
			}
		}

		if (count($active_players) >= $count) {
			return $active_players;
		} else {
			//if not enough , just ignore
			$this->utils->debug_log('filterActivePlayersByIdByProvider', $active_players, 'count', $count);
			return [];
		}

	}

	/**
	 * overview : get default affiliate terms
	 *
	 * @return null|string
	 */
	public function getDefaultAffiliateTerms() {
		$this->db->where('name', 'affiliate_default_terms');
		$result = $this->db->get('operator_settings');

		if ($result->num_rows() > 0) {
			$result = $result->result();
			return $result[0]->value;
		} else {
			return null;
		}

	}

	/**
	 * overview : get default sub affiliate terms
	 */
	public function getDefaultSubAffiliateTerms() {
		$this->load->model(array('operatorglobalsettings'));
		$val = $this->operatorglobalsettings->getSettingValue('sub_affiliate_default_terms');

		if (empty($val)) {
			$val = $this->utils->encodeJson($this->utils->getConfig('sub_affiliate_default_terms'));
		}
		//try fix levels
		$subterms = $this->utils->decodeJson($val);

		$sub_levels = @$subterms['terms']['sub_levels'];
		$sub_level = @$subterms['terms']['sub_level'];
		$subAffiliateLevels = $this->utils->getConfig('subAffiliateLevels');
		if ($sub_level < $subAffiliateLevels || count($sub_levels) < $subAffiliateLevels) {
			$sub_level = $subAffiliateLevels;
			if (!is_array($sub_levels)) {
				$sub_levels = array();
			}
			$sub_levels = array_pad($sub_levels, $subAffiliateLevels, 0);
			$subterms['terms']['sub_levels'] = $sub_levels;
			$subterms['terms']['sub_level'] = $sub_level;
			$val = $this->utils->encodeJson($subterms);
		}

		return $val;
		// $this->db->where('name', 'sub_affiliate_default_terms');
		// $result = $this->db->get('operator_settings');

		// if ($result->num_rows() > 0) {
		// 	$result = $result->result();
		// 	return $result[0]->value;
		// } else {
		// 	return null;
		// }

	}

	/**
	 * overview : get affiliate settings
	 *
	 * @return null|string
	 */
	public function getAffiliateSettings() {
		$this->db->where('name', 'affiliate_settings');
		$result = $this->db->get('operator_settings');

		if ($result->num_rows() > 0) {
			$result = $result->result();
			return $result[0]->value;
		} else {
			return null;
		}

	}

	/**
	 * overview : get affiliate statistics
	 *
	 * @param int	$affiliate_id
	 * @param date  $start_date
	 * @param date  $end_date
	 * @return array
	 */
	public function getStatistics($affiliate_id, $start_date = null, $end_date = null) {
		# GET PLAYER MODEL
		$this->load->model(array('player_model', 'transactions', 'game_logs', 'total_player_game_minute', 'wallet_model'));

		if (empty($start_date)) {
			$start_date = date('Y-m-d') . ' 00:00:00';
		}

		if (empty($end_date)) {
			$end_date = date('Y-m-d') . ' 23:59:59';
		}

		$statistics = [];

		# GET LIST OF PLAYERS
		//get all
		$players = $this->getAllPlayersUnderAffiliate($affiliate_id, null, null);
		// $this->utils->printLastSQL();
		// $start_date = $start_date . ' 00:00:00';
		// $end_date = $end_date . ' 59:99:99';
		if (!empty($players)) {
			$use_total_minute = $this->utils->getConfig('use_total_minute');

			foreach ($players as $p) {
				// $player_id = [];
				// $player_id[] = $p['playerId'];

				$player = [];
				$player['playerId'] = $p['playerId'];
				$player['username'] = $p['username'];
				$totalBalancesAmount = $this->wallet_model->getTotalBalance($p['playerId']);
				$player['totalBalancesAmount'] = $this->utils->formatCurrencyNoSym($totalBalancesAmount);
				//from transactions
				list($totalDeposit, $totalWithdrawal, $totalBonus, $totalCashback) =
				$this->transactions->getTotalDepositWithdrawalBonusCashbackByPlayers(
					$p['playerId'], $start_date, $end_date);
				// $this->utils->printLastSQL();
				//should format
				$player['totalDeposit'] = $this->utils->formatCurrencyNoSym($totalDeposit);
				$player['totalWithdraw'] = $this->utils->formatCurrencyNoSym($totalWithdrawal);
				$player['totalBonus'] = $this->utils->formatCurrencyNoSym($totalBonus);
				$player['totalCashback'] = $this->utils->formatCurrencyNoSym($totalCashback);

				// if ($use_total_minute) {
				// 	list($totalBets, $totalWins, $totalLoss)
				// 	= $this->total_player_game_minute->getPlayerTotalBetsWinsLossByDatetime(
				// 		$p['playerId'], $start_date, $end_date);
				// 	// $this->utils->printLastSQL();
				// } else {
				list($totalBets, $totalWins, $totalLoss) = $this->game_logs->getPlayerTotalBetsWinsLossByDatetime(
					$p['playerId'], $start_date, $end_date);
				// }

				$player['totalBets'] = $this->utils->formatCurrencyNoSym($totalBets);
				$player['totalWins'] = $this->utils->formatCurrencyNoSym($totalWins);
				$player['totalLoss'] = $this->utils->formatCurrencyNoSym($totalLoss);

				// $player['totalDeposit'] = $this->player_model->getPlayersTotalDeposit($player_id, $start_date, $end_date);
				// $player['totalWithdraw'] = $this->player_model->getPlayersTotalWithdraw($player_id, $start_date, $end_date);
				// $player['totalBets'] = $this->player_model->getPlayersTotalBets($player_id, $start_date, $end_date);
				// $player['totalWins'] = $this->player_model->getPlayersTotalWin($player_id, $start_date, $end_date);
				// $player['totalLoss'] = $this->player_model->getPlayersTotalLoss($player_id, $start_date, $end_date);
				// $player['totalBonus'] = $this->player_model->getPlayersTotalBonus($player_id, $start_date, $end_date);

				$statistics[] = $player;
			}
		}

		return $statistics;
	}

	/**
	 * overview : get all affiliates under affiliate (subaffiliate) in affiliates table
	 *
	 * @param	int		$affiliate_id
	 * @param	date	$date_from
	 * @param	date	$date_to
	 * @return	array
	 */
	public function getAllAffiliatesUnderAffiliate($affiliate_id, $date_from = null, $date_to = null) {
		$where = null;

        if(!empty($date_from) && !empty($date_to)){
             $where = "AND createdOn >= '{$date_from}' AND createdOn <= '{$date_to}'";
        }elseif (!empty($date_from)) {
			$where = "AND createdOn <= '" . $date_to . "'";
		}
		$qSubAffiliates = <<<EOD
SELECT *, CONCAT(firstname, ' ', lastname) as realname
	FROM affiliates
	where parentId = $affiliate_id
	$where
	and status!=?
EOD;
		$query = $this->db->query($qSubAffiliates, [self::STATUS_DELETED]);
		return $query->result_array();
	}

	/**
	 * overview : get daily statistics
	 *
	 * @param date $start_date
	 * @param date $end_date
	 * @return array
	 */
	public function getDailyStatistics($start_date = null, $end_date = null) {
		$aff_stats = $this->getStatistics($start_date, $end_date);

		$result = array();
		$data = array();

		$date = null;

		foreach ($aff_stats as $key => $value) {
			$results = array();

			if ($date != $value['date']) {
				$date = $value['date'];
				$aff_count = 0;

				$pt_bet = 0;
				$ag_bet = 0;
				$total_bet = 0;

				$pt_win = 0;
				$ag_win = 0;
				$total_win = 0;

				$pt_loss = 0;
				$ag_loss = 0;
				$total_loss = 0;

				$total_net_gaming = 0;
				$total_bonus = 0;

				$results = $this->search($aff_stats, 'date', $value['date']);

				foreach ($results as $key => $value) {
					$aff_count++;

					$pt_bet += $value['pt_bet'];
					$ag_bet += $value['ag_bet'];
					$total_bet += $value['total_bet'];

					$pt_win += $value['pt_win'];
					$ag_win += $value['ag_win'];
					$total_win += $value['total_win'];

					$pt_loss += $value['pt_loss'];
					$ag_loss += $value['ag_loss'];
					$total_loss += $value['total_loss'];

					$total_net_gaming += $value['total_net_gaming'];
					$total_bonus += $value['total_bonus'];
					//$total_affiliates = $value['total_affiliate'];
				}

				$data = array(
					'pt_bet' => $pt_bet,
					'ag_bet' => $ag_bet,
					'total_bet' => $total_bet,
					'pt_win' => $pt_win,
					'ag_win' => $ag_win,
					'total_win' => $total_win,
					'pt_loss' => $pt_loss,
					'ag_loss' => $ag_loss,
					'total_loss' => $total_loss,
					'total_net_gaming' => $total_net_gaming,
					'total_bonus' => $total_bonus,
					'total_affiliates' => $aff_count,
					'date' => $date,
				);
				array_push($result, $data);
			}
		}

		return $result;
	}

	/**
	 * overview : get trackingCode by affDomain
	 *
	 * @param  string	$affdomain
	 * @return string
	 */
	public function getTrackingCodeFromAffDomain($affdomain) {
		if($this->utils->isEnabledFeature('match_wild_char_on_affiliate_domain')){
			return $this->getTrackingCodeByMatchAffDomain($affdomain);
		}

		$this->db->select('trackingCode');
		$query = $this->db->get_where('affiliates', array('affdomain' => $affdomain), 1);
		$res = $query->row_array();
		if (isset($res['trackingCode'])) {
			return $res['trackingCode'];
		} else {
			//try search aff_tracking_link
			$this->db->select('aff_id')->from('aff_tracking_link')->where('tracking_domain', $affdomain)->where('tracking_type', self::TRACKING_TYPE_DOMAIN);
			$aff_id = $this->runOneRowOneField('aff_id');
			if ($aff_id) {
				$this->db->select('trackingCode')->from('affiliates')->where('affiliateId', $aff_id);
				$trackingCode = $this->runOneRowOneField('trackingCode');
				return $trackingCode;
			}
		}
		return false;
	}

	/**
	 * overview : get sub affiliates ids
	 *
	 * @param $ids
	 * @param date $start_date
	 * @param date $end_date
	 * @return null|string
	 */
	public function getSubAffiliatesIds($ids, $start_date = null, $end_date = null) {
		$sub_id = array();
		if ($start_date) {
			$this->db->where('createdOn >', $start_date);
		}

		if ($end_date) {
			$this->db->where('createdOn <', $end_date);
		}

		$this->db->where_in('parentId', $ids);
		$result = $this->db->get('affiliates');

		if ($result->num_rows() > 0) {
			foreach ($result->result() as $r) {
				array_push($sub_id, $r->affiliateId);
			}
			return implode(',', $sub_id);
		} else {
			return null;
		}
	}

	/**
	 * overview : update affiliate balance
	 *
	 * @param	array	$data
	 * @param	int		$affiliate_id
	 * @return	array
	 */
	public function updateAffiliateBalance($data, $affiliate_id) {
		$this->db->where('affiliateId', $affiliate_id);
		$this->db->update('affiliates', $data);
		return true;
	}

	/**
	 * overview : get affiliate settings object
	 *
	 * @return null|object
	 */
	public function getAffiliateSettingsObject() {
		$this->load->model(array('operatorglobalsettings'));
		$val = $this->operatorglobalsettings->getSettingValue('affiliate_settings');

		if (!empty($val)) {
			return $this->utils->decodeJson($val);
		}

		return null;
	}

	/**
	 * overview : get default sub affiliate terms object
	 *
	 * @return null|object
	 */
	public function getDefaultSubAffiliateTermsObject() {
		$this->load->model(array('operatorglobalsettings'));
		$val = $this->operatorglobalsettings->getSettingValue('sub_affiliate_default_terms');

		if (!empty($val)) {
			return $this->utils->decodeJson($val);
		}

		return null;
	}

	/**
	 * overview : get default affiliate terms object
	 *
	 * @return null|object
	 */
	public function getDefaultAffiliateTermsObject() {
		$this->load->model(array('operatorglobalsettings'));
		$val = $this->operatorglobalsettings->getSettingValue('affiliate_default_terms');

		if (!empty($val)) {
			return $this->utils->decodeJson($val);
		}

		return null;
	}

	/**
	 * overview : get all active affiliates
	 * @param bool|true $onlyMaster
	 * @param bool|false $orderByUsername
	 * @return null
	 */
	public function getAllActivtedAffiliates($onlyMaster = true, $orderByUsername = false) {
		$this->db->from($this->tableName)->where('status', self::OLD_STATUS_ACTIVE);
		if ($onlyMaster) {
			$this->db->where('ifnull(parentId,0)=0', null, false);
		}
		if ($orderByUsername) {
			$this->db->order_by('username');
		}
		return $this->runMultipleRowArray();
	}

	// public function getDefaultAllAffiliateSettings() {
	// 	$this->load->model(array('operatorglobalsettings'));

	// 	// $config_affiliate_settings = json_decode($this->getConfig('default_affiliate_settings'));
	// 	$config_affiliate_settings =self::DEFAULT_AFFILIATE_SETTINGS;

	// 	$val = $this->operatorglobalsettings->getSettingValue('affiliate_settings');
	// 	$default_affiliate_settings = $config_affiliate_settings;
	// 	if (!empty($val)) {
	// 		$default_affiliate_settings = $this->utils->decodeJson($val);
	// 		//{"baseIncomeConfig": "2","level_master":"50","minimumPayAmount": "0","paymentDay": "1","cashback_fee": "100"}
	// 		//check items
	// 		if (!isset($default_affiliate_settings['baseIncomeConfig'])) {
	// 			$default_affiliate_settings['baseIncomeConfig'] = $config_affiliate_settings->baseIncomeConfig;
	// 		}
	// 		if (!isset($default_affiliate_settings['level_master'])) {
	// 			$default_affiliate_settings['level_master'] = $config_affiliate_settings['level_master'];
	// 		}
	// 		if (!isset($default_affiliate_settings['minimumPayAmount'])) {
	// 			$default_affiliate_settings['minimumPayAmount'] = $config_affiliate_settings['minimumPayAmount'];
	// 		}
	// 		if (!isset($default_affiliate_settings['paymentDay'])) {
	// 			$default_affiliate_settings['paymentDay'] = $config_affiliate_settings['paymentDay'];
	// 		}
	// 		if (!isset($default_affiliate_settings['admin_fee'])) {
	// 			$default_affiliate_settings['admin_fee'] = $config_affiliate_settings['admin_fee'];
	// 		}
	// 		if (!isset($default_affiliate_settings['transaction_fee'])) {
	// 			$default_affiliate_settings['transaction_fee'] = $config_affiliate_settings['transaction_fee'];
	// 		}
	// 		if (!isset($default_affiliate_settings['bonus_fee'])) {
	// 			$default_affiliate_settings['bonus_fee'] = $config_affiliate_settings['bonus_fee'];
	// 		}
	// 		if (!isset($default_affiliate_settings['cashback_fee'])) {
	// 			$default_affiliate_settings['cashback_fee'] = $config_affiliate_settings['cashback_fee'];
	// 		}
	// 	}

	// 	$config_affiliate_default_terms = $this->utils->decodeJson($this->getConfig('affiliate_default_terms'));
	// 	$val = $this->operatorglobalsettings->getSettingValue('affiliate_default_terms');
	// 	$affiliate_default_terms = $config_affiliate_default_terms;
	// 	if (!empty($val)) {
	// 		$affiliate_default_terms = $this->utils->decodeJson($val);
	// 	}

	// 	$config_sub_affiliate_default_terms = $this->utils->decodeJson($this->getConfig('sub_affiliate_default_terms'));
	// 	$val = $this->operatorglobalsettings->getSettingValue('sub_affiliate_default_terms');
	// 	$sub_affiliate_default_terms = $config_sub_affiliate_default_terms;
	// 	if (!empty($val)) {
	// 		$sub_affiliate_default_terms = $this->utils->decodeJson($val);
	// 	}

	// 	$result=[
	// 		'default_affiliate_settings' => $default_affiliate_settings,
	// 		'affiliate_default_terms' => $affiliate_default_terms,
	// 		'sub_affiliate_default_terms' => $sub_affiliate_default_terms];

	// 	return $result;
	// }

	// public function getAffiliateTermsById($affiliate_id, $defaultAllAffiliateSettings = null) {
	// 	if (empty($defaultAllAffiliateSettings)) {
	// 		$defaultAllAffiliateSettings = $this->getDefaultAllAffiliateSettings();
	// 	}

	// 	$affiliate_terms = $defaultAllAffiliateSettings['affiliate_default_terms']['terms'];

	// 	$this->db->from('affiliate_terms')->where('affiliateId', $affiliate_id)
	// 		->where('optionType', self::OPTION_AFFILIATE_DEFAULT_TERMS);

	// 	$row = $this->runOneRowArray();

	// 	if (!empty($row)) {
	// 		$val = $row['optionValue'];
	// 		if (!empty($val)) {
	// 			$jsonObj = $this->utils->decodeJson($val);
	// 			if (!empty($jsonObj) && isset($jsonObj['terms'])) {
	// 				$affiliate_terms = $jsonObj['terms'];

	// 				// GENERAL SETTINGS
	// 				if (!isset($affiliate_terms['minimumBetting'])) {
	// 					$affiliate_terms['minimumBetting'] = $defaultAllAffiliateSettings['affiliate_default_terms']['terms']['minimumBetting'];
	// 				}
	// 				if (!isset($affiliate_terms['minimumDeposit'])) {
	// 					$affiliate_terms['minimumDeposit'] = $defaultAllAffiliateSettings['affiliate_default_terms']['terms']['minimumDeposit'];
	// 				}
	// 			}
	// 		}
	// 	}

	// 	return $affiliate_terms;

	// 	// if (!empty($affiliate_term)) {
	// 	// 	$affiliate_terms = json_decode($affiliate_term)->terms;

	// 	// 	// GENERAL SETTINGS
	// 	// 	$affiliate_terms->minimumBetting = $defaultAllAffiliateSettings->affiliate_default_terms->terms->minimumBetting;
	// 	// 	$affiliate_terms->minimumDeposit = $defaultAllAffiliateSettings->affiliate_default_terms->terms->minimumDeposit;
	// 	// } else {
	// 	// }

	// 	// $this->db->where('affiliateId', $affiliate_id);
	// 	// $this->db->where('optionType', 'affiliate_default_terms');
	// 	// $result = $this->db->get('affiliate_terms');

	// 	// if ($result->num_rows() > 0) {
	// 	// 	$result = $result->result();
	// 	// 	return $result[0]->optionValue;
	// 	// } else {
	// 	// 	return null;
	// 	// }

	// }

	// public function getSubAffiliateTermsById($affiliate_id, $defaultAllAffiliateSettings = null) {
	// 	if (empty($defaultAllAffiliateSettings)) {
	// 		$defaultAllAffiliateSettings = $this->getDefaultAllAffiliateSettings();
	// 	}

	// 	$sub_affiliate_terms = $defaultAllAffiliateSettings['sub_affiliate_default_terms']['terms'];

	// 	$this->db->from('affiliate_terms')->where('affiliateId', $affiliate_id)
	// 		->where('optionType', self::OPTION_SUB_AFFILIATE_DEFAULT_TERMS);

	// 	$row = $this->runOneRowArray();

	// 	if (!empty($row)) {
	// 		$val = $row['optionValue'];
	// 		if (!empty($val)) {
	// 			$jsonObj = $this->utils->decodeJson($val);
	// 			if (!empty($jsonObj) && isset($jsonObj['terms'])) {
	// 				$sub_affiliate_terms = $jsonObj['terms'];

	// 				// GENERAL SETTINGS
	// 				// if (!isset($sub_affiliate_terms->minimumBetting)) {
	// 				$sub_affiliate_terms['sub_level'] = $defaultAllAffiliateSettings['sub_affiliate_default_terms']['terms']['sub_level'];
	// 				// }
	// 				// if (!isset($sub_affiliate_terms->minimumDeposit)) {
	// 				$sub_affiliate_terms['sub_levels'] = $defaultAllAffiliateSettings['sub_affiliate_default_terms']['terms']['sub_levels'];
	// 				// }
	// 			}
	// 		}
	// 	}

	// 	if (!isset($sub_affiliate_terms['level_master'])) {
	// 		$sub_affiliate_terms['level_master'] = $defaultAllAffiliateSettings['default_affiliate_settings']['level_master'];
	// 	}

	// 	return $sub_affiliate_terms;

	// 	// $this->db->where('affiliateId', $affiliate_id);
	// 	// $this->db->where('optionType', 'sub_affiliate_default_terms');
	// 	// $result = $this->db->get('affiliate_terms');

	// 	// // var_dump($affiliate_id); die();

	// 	// if ($result->num_rows() > 0) {
	// 	// 	$result = $result->result();
	// 	// 	return $result[0]->optionValue;
	// 	// } else {
	// 	// 	return null;
	// 	// }

	// }

	/**
	 * overview :  search for affiliates
	 *
	 * @param string $username
	 * @param bool|true $onlyActive
	 * @return null
	 */
	public function searchAffiliates($username = null, $onlyActive = true) {
		$this->db->from($this->tableName);
		if ($onlyActive) {
			$this->db->where('status', self::OLD_STATUS_ACTIVE);
		}
		if (!empty($username)) {
			$this->db->like('username', $username);
		}
        $this->db->where('deleted_at is null');

		return $this->runMultipleRowArray();
	}

	/**
	 * get affiliate levels in affiliates table
	 *
	 * @param	int		$affiliate_id
	 * @param	date	$start_date
	 * @param	date	$end_date
	 * @param	bool	$parent
	 * @return	array
	 */
	public function getAffiliateUpLevels($affiliate_id, $start_date, $end_date, $parent = false) {
		$affiliate = [];

		$this->db->where('affiliateId', $affiliate_id);
		$a = $this->db->get('affiliates');

		if ($a->num_rows() > 0) {
			$a = $a->row_array();
			$affiliate[] = $a;

			$parent_id = $a['parentId'];

			while ($parent_id != 0) {
				$parent = $this->getAffiliateParent($parent_id);
				$parent_id = $parent['parentId'];
				$affiliate[] = $parent;
			}
		}
		$this->utils->debug_log('affiliate_id', $affiliate_id, 'affiliate', $affiliate);
		return array_reverse($affiliate);
	}

	/**
	 * overview : count affiliate up levels
	 *
	 * @param $affiliate_id
	 * @return int
	 */
	public function countAffiliateUpLevels($affiliate_id) {
		$cnt = 0;

		$this->db->where('affiliateId', $affiliate_id);
		$a = $this->db->get('affiliates');
		if ($a->num_rows() > 0) {
			$a = $a->row_array();
			$affiliate[] = $a;
			$parent_id = $a['parentId'];

			while ($parent_id != 0) {
				$parent = $this->getAffiliateParent($parent_id);
				$parent_id = $parent['parentId'];
				// $affiliate[] = $parent;
				$cnt++;
			}
		}
		$this->utils->debug_log('affiliate_id', $affiliate_id, 'cnt', $cnt);
		return $cnt;
	}

	/**
	 * get affiliate parent
	 *
	 * @param $affiliate_id
	 * @return array
	 */
	public function getAffiliateParent($affiliate_id) {
		$this->db->where('affiliateId', $affiliate_id);
		$a = $this->db->get('affiliates');

		if ($a->num_rows() > 0) {
			$a = $a->row_array();
			return $a;
		}
	}

	/**
	 * overview : get Affiliate Statistics
	 *
	 * @param	array	$start_date
	 * @param	int		$end_date
	 * @param	int		$username
	 * @param	int		$limit
	 * @param	int		$offset
	 * @return 	array
	 */
	public function getAffiliateStatistics($start_date, $end_date, $username = null, $limit = null, $offset = null) {

		# GET PLAYER MODEL
		$this->load->model(array('total_player_game_minute', 'game_logs', 'transactions'));

		// if(!$start_date) $start_date = date('Y-m-d');
		// if(!$end_date) $end_date = date('Y-m-d');

		// $start_date = $start_date . ' 00:00:00';
		// $end_date = $end_date . ' 59:99:99';

		$statistics = [];

		# GET LIST OF AFFILIATES
		// $where = array("createdOn <=" => $end_date);

		// if (!empty($username)) {
		// 	$where['username'] = $username;
		// }
		$affiliates = $this->searchAffiliates($username);

		// $this->utils->debug_log('affiliates', $affiliates);
		// var_dump($affiliates); die();
		$use_total_minute = $this->utils->getConfig('use_total_minute');

		foreach ($affiliates as $a) {
			$aff = array();
			$aff[] = $a['username'];
			$aff[] = $a['firstname'] . ' ' . $a['lastname'];

			# GET AFFILIATE LEVEL
			$aff[] = $this->countAffiliateUpLevels($a['affiliateId']);

			# GET LIST OF SUB-AFFILIATES OF AN AFFILIATE
			$aff[] = count($this->getAllAffiliatesUnderAffiliate($a['affiliateId'], $start_date, $end_date));

			# GET LIST OF PLAYERS UNDER AFFILIATE
			$players = $this->getAllPlayersUnderAffiliateId($a['affiliateId']);
			// $this->utils->debug_log('players', count($players));
			$aff[] = count($players);

			//from transactions
			list($totalDeposit, $totalWithdrawal, $totalBonus, $totalCashback) =
			$this->transactions->getTotalDepositWithdrawalBonusCashbackByPlayers($players, $start_date, $end_date);
			// list($totalBets, $totalWins, $totalLoss) =
			// $this->total_player_game_minute->getTotalBetsWinsLossByPlayers($players, $start_date, $end_date);
			// if ($use_total_minute) {
			// 	list($totalBets, $totalWins, $totalLoss) = $this->total_player_game_minute->getTotalBetsWinsLossByPlayers(
			// 		$players, $start_date, $end_date);
			// 	// $this->utils->printLastSQL();
			// } else {
			list($totalBets, $totalWins, $totalLoss) = $this->game_logs->getTotalBetsWinsLossByPlayers(
				$players, $start_date, $end_date);
			// }

			$aff[] = $this->utils->formatCurrencyNoSym($totalBets);
			$aff[] = $this->utils->formatCurrencyNoSym($totalWins);
			$aff[] = $this->utils->formatCurrencyNoSym($totalLoss);
			$aff[] = $this->utils->formatCurrencyNoSym($totalCashback);
			$aff[] = $this->utils->formatCurrencyNoSym($totalBonus);
			$aff[] = $this->utils->formatCurrencyNoSym($totalDeposit);
			$aff[] = $this->utils->formatCurrencyNoSym($totalWithdrawal);

			// $aff['income'] = $aff['loss'] - $aff['win'];

			// # GET TOTAL BET
			// $aff['bets'] += $this->player_model->getPlayersTotalBets($players, $start_date, $end_date);

			// # GET TOTAL WIN
			// $aff['win'] += $this->player_model->getPlayersTotalWin($players, $start_date, $end_date);

			// # GET TOTAL LOSE
			// $aff['loss'] += $this->player_model->getPlayersTotalLoss($players, $start_date, $end_date);

			// # GET TOTAL BONUS
			// $aff['bonus'] += $this->player_model->getPlayersTotalBonus($players, $start_date, $end_date);

			// # CALCULATE NET INCOME
			// $aff['income'] = $aff['win'] - $aff['loss'];

			$aff[] = $a['location'];
			$aff[] = $a['ip_address'];

			$statistics[] = $aff;
		}

		// $total = count($statistics);
		// $statistics = array(
		// 	'draw' => '',
		// 	'recordsTotal' => $total,
		// 	'recordsFiltered' => $total,
		// 	'data' => $statistics,
		// );

		// echo '<pre>'; print_r($statistics); die();

		return $statistics;
	}

	/**
	 * overview : check if enabled credit
	 *
	 * @param 	int	$affiliateId
	 * @return	bool
	 */
	public function isEnabledCredit($affiliateId) {
		$this->db->from('affiliates')->where('affiliateId', $affiliateId);
		return $this->runOneRowOneField('balance') > 0;
	}

	public function importAffiliate($externalId, $username, $password, $trackingCode, $createdOn,
		$firstname, $lastname, $status, $extra = null, $affShare=null) {

		if (empty($username)) {
			$message = "Empty username: [$username]";
			return false;
		}

		$this->load->library(array('salt'));
		$data = array(
			'externalId' => $externalId,
			'username' => $username,
			'password' => empty($password) ? '' : $this->salt->encrypt($password, $this->getDeskeyOG()),
			'trackingCode' => $trackingCode,
			'createdOn' => $createdOn,
			'firstname' => $firstname,
			'lastname' => $lastname,
			'status' => $status,
			'registered_by' => self::REGISTERED_BY_IMPORTER,
		);

		//process extra
		if(empty($extra['lastLogin'])){
			// $this->utils->debug_log('unset lastLogin');
			unset($extra['lastLogin']);
		}
		if(empty($extra['lastLoginIp'])){
			unset($extra['lastLoginIp']);
		}

		if (!empty($extra)) {
			$data = array_merge($data, $extra);
		}

		$affId=null;
		//check username or externalId first
		if (!empty($externalId)) {
			$this->db->from($this->tableName)->where('externalId', $externalId);
			$affId = $this->runOneRowOneField('affiliateId');
		} else {
			$this->db->from($this->tableName)->where('username', $username);
			$affId = $this->runOneRowOneField('affiliateId');
		}

		if (!empty($affId)) {
			$data=[];
			//update
			if(!empty($firstname)){
				$data['firstname']=$firstname;
			}
			if(!empty($lastname)){
				$data['lastname']=$lastname;
			}
			if (!empty($extra)) {
				$data = array_merge($data, $extra);
			}
			if(!empty($data)){
				$this->db->set($data)->where('affiliateId', $affId);
				$this->runAnyUpdate('affiliates');
			}
			if(!empty($affShare)){
				// $settings=$this->getAffTermsSettings($affId);
				$fldKV=['level_master'=>$affShare];
				$mode='operator_settings';
				$this->mergeToAffiliateSettings($fldKV, $affId, $mode);
			}

			// $this->utils->debug_log('ignore username', $username);
			//ignore
			return $affId;
		}

		// $this->db->set($data);
		return $this->insertData($this->tableName, $data);
	}


	/**
	 * overview : get username map
	 *
	 * @return array
	 */
	public function getUsernameMap() {
		$this->db->from($this->tableName);
		$affMap = array();
		$rows = $this->runMultipleRow();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$affMap[$row->username] = $row->affiliateId;
			}
		}

		return $affMap;
	}

	/**
	 * overview : get external id map
	 *
	 * @return array
	 */
	public function getExternalIdMap() {
		$this->db->from($this->tableName);
		$affMap = array();
		$rows = $this->runMultipleRow();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$affMap[$row->externalId] = $row->affiliateId;
			}
		}

		return $affMap;
	}

	/**
	 * overview : get affiliate password
	 *
	 * @param 	int	$affiliateId
	 * @return array
	 */
	public function getAffPassword($affiliateId) {
		$this->db->select('password,username')->from($this->tableName);
		$this->db->where('affiliateId', $affiliateId);
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * overview : get affiliate domain
	 *
	 * @return array
	 */
	public function getAffDomain() {
		$this->db->select('domainId,domainName')->from('domain');
		$this->db->where('status', self::ACTIVE_DOMAIN);
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * overview : get public information by id
	 *
	 * @param int	$id
	 * @return null
	 */
	public function getPublicInfoById($id) {
		$this->db->select('username,department,email')->from('affiliates')->where('userId', $id);

		return $this->getOneRow();
	}

	/**
	 * overview : search affiliate session
	 *
	 * @param 	int	$affiliateId
	 * @return array
	 */
	public function searchAffSession($affiliateId) {
		if($this->utils->getConfig('sess_use_database')){
			// $this->load->library(array('session'));
			$this->db->from('ci_aff_sessions');
			$sessions = array();
			$rows = $this->runMultipleRow();
			foreach ($rows as $row) {
				$user_data = $row->user_data;
				if (!empty($user_data)) {
					$data = $this->utils->unserializeSession($user_data);
					if (!empty($data) && isset($data['affiliateId']) && $data['affiliateId'] == $affiliateId) {
						$sessions[] = $row->session_id;
					}
				}
			}
			return $sessions;
		}else{
			$specialSessionTable='ci_aff_sessions';
			return $this->searchSessionIdByObjectIdOnRedis($specialSessionTable, $affiliateId);
		}
	}

	/**
	 * overview : kickout affiliate user
	 *
	 * @param int	$affiliateId
	 * @return bool
	 */
	public function kickoutAffuser($affiliateId) {
		if($this->utils->getConfig('sess_use_database')){
			$sessions = $this->searchAffSession($affiliateId);
			if (!empty($sessions)) {
				$this->db->where_in('session_id', $sessions)->delete('ci_aff_sessions');
				return $this->db->affected_rows();
			}
		}else{
			//clear redis
			$specialSessionTable='ci_aff_sessions';
			$this->deleteSessionsByObjectIdOnRedis($specialSessionTable, $affiliateId);
		}

		return true;
	}

	/**
	 * overview : get credit balance
	 *
	 * @param int	$affId
	 * @return null
	 */
	public function getCreditBalance($affId) {
		$this->db->from('affiliates')->where('affiliateId', $affId);
		return $this->runOneRowOneField('balance');
	}

	/**
	 * overview : get player id array by affiliate username
	 *
	 * @param  string	$affiliate_username
	 * @return array
	 */
	public function getPlayerIdArrayByAffiliateUsername($affiliate_username) {
		$this->db->distinct()->select('player.playerId')->from('player')
			->join('affiliates', 'affiliates.affiliateId=player.affiliateId')
			->where('affiliates.username', $affiliate_username);

		// $result = [];
		$rows = $this->runMultipleRow();
		// if (!empty($rows)) {
		// 	foreach ($rows as $row) {
		// 		$result[] = $row->playerId;
		// 	}
		// }

		return $this->convertRowsToArray($rows, 'playerId');
	}

	/**
	 * overview : get affiliate id by username
	 *
	 * @param string $affiliate_username
	 * @return null
	 */
	public function getAffiliateIdByUsername($affiliate_username) {
		$this->db->select('affiliateId')->from('affiliates')->where('username', $affiliate_username);
		return $this->runOneRowOneField('affiliateId');
	}

	/**
	 * overview : delete affiliates
	 *
	 * @param  array $affIdArr
	 * @return bool
	 */
	// public function deleteAffiliates($affIdArr) {
	// 	if (is_array($affIdArr)) {
    //         foreach ($affIdArr as $affId) {
    //             $data = $this->getAffiliateById($affId);

    //             $this->db->set('status', self::STATUS_DELETED);
    //             $this->db->set('email', base64_encode($data['email']));
    //             $this->db->set('deleted_at', $this->utils->getNowForMysql());
    //             $this->db->where('affiliateId', $affId);
    //             return $this->runAnyUpdate('affiliates');
    //         }

	// 	} else {
    //         $data = $this->getAffiliateById($affIdArr);

    //         $this->db->set('email', base64_encode($data['email']));
    //         $this->db->set('deleted_at', $this->utils->getNowForMysql());
    //         $this->db->where('affiliateId', $affIdArr);
    //         return $this->runAnyUpdate('affiliates');
	// 	}

	// }

	/**
	 * overview : search all affiliates from affiliate table
	 *
	 * @param	int		$limit
	 * @param	int		$offset
	 * @param	array	$data
	 * @return	array
	 */
	public function searchAllAffiliates($limit, $offset, $data) {
		$search = array();
		$sortby = null;
		$desc_order = null;

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		if (isset($data)) {
			foreach ($data as $key => $value) {
				if ($key == 'signup_range' && $value != '') {
					$search[$key] = "a.createdOn BETWEEN $value";
				} elseif ($key == 'status' && $value != null) {
					if ($value == 'active') {
						$search[$key] = "a.status = " . self::OLD_STATUS_ACTIVE;
					} elseif ($value == 'inactive') {
						$search[$key] = "a.status = " . self::OLD_STATUS_INACTIVE;
					} elseif ($value == 'deleted') {
						$search[$key] = "a.status = " . self::STATUS_DELETED;
					}

				} elseif ($key == 'game' && !empty($value)) {
					$search[$key] = "ag.$key = '" . $value . "'";
				} elseif ($key == 'parentId' && !empty($value)) {
					$search[$key] = "a.$key = '" . $value . "'";
				} elseif ($value != null) {
					$search[$key] = "a.$key LIKE '%" . $value . "%'";
				}
			}
		}

		if (!isset($data['status']) || $data['status'] == null) {
			$search['status'] = "a.status != " . self::STATUS_DELETED;
		}
		// $query = "SELECT a.*, ats.tagName,"
		// . " (SELECT SUM(approved) FROM affiliatemonthlyearnings as ame WHERE ame.affiliateId = a.affiliateId) as approved,"
		// . " (SELECT SUM(amount) FROM affiliatepaymenthistory as aph WHERE aph.affiliateId = a.affiliateId AND aph.status IN ('0', '1', '2', '3')) as deduct_amt"
		// . " FROM affiliates as a"
		// . " LEFT JOIN affiliatetag as at"
		// . " ON a.affiliateId = at.affiliateId"
		// . " LEFT JOIN affiliatetaglist as ats"
		// . " ON at.tagId = ats.tagId";

		// if (count($search) > 0) {
		// 	$query .= " WHERE " . implode(' AND ', $search);
		// }

		// $run = $this->db->query("$query $limit $offset");

		// return $run->result_array();

		$q = <<<EOD
SELECT a.*, (SELECT b.username FROM affiliates as b where a.parentId = b.affiliateId) AS parent, ats.tagName
FROM affiliates as a
LEFT JOIN affiliatetag as at
ON a.affiliateId = at.affiliateId
LEFT JOIN affiliatetaglist as ats
ON at.tagId = ats.tagId
EOD;

		if (count($search) > 0) {
			$q .= " WHERE " . implode(' AND ', $search);
		}

		// $this->utils->debug_log('search affiliate sql', $q);
		$query = $this->db->query($q);

		return $query->result_array();
	}

	/**
	 * overview : update count
	 */
	public function fixCountOfAll() {
		$affList = $this->searchAllAffiliates(null, null, null);

		foreach ($affList as $aff) {
			$affId = $aff['affiliateId'];
			$level = $this->countAffiliateUpLevels($affId);
			$countSub = count($this->getAllAffiliatesUnderAffiliate($affId, null, null));
			$players = $this->getAllPlayersUnderAffiliateId($affId);
			$countPlayer = count($players);
			$this->db->set('levelNumber', $level)->set('countSub', $countSub)
				->set('countPlayer', $countPlayer)->where('affiliateId', $affId);

			$this->db->update('affiliates');
		}

	}

	/**
	 * overview : update level number
	 *
	 * @param int	$affId
	 * @return bool
	 */
	public function updateLevelNumber($affId) {
		if (!empty($affId)) {
			$level = $this->countAffiliateUpLevels($affId);
			$this->db->set('levelNumber', $level)->where('affiliateId', $affId);

			return $this->runAnyUpdate('affiliates');
		}
		return false;
	}

	/**
	 * overview : count player
	 * @param  int $affId
	 * @return bool
	 */
	public function incCountPlayer($affId) {
		if (!empty($affId)) {

			$this->db->set('countPlayer', 'countPlayer+1', false)->where('affiliateId', $affId);

			return $this->runAnyUpdate('affiliates');
		}
		return false;
	}

	/**
	 * overview : count sub
	 * @param  int $affId
	 * @return bool
	 */
	public function incCountSub($affId) {
		if (!empty($affId)) {
			$this->db->set('countSub', 'countSub+1', false)->where('affiliateId', $affId);

			return $this->runAnyUpdate('affiliates');
		}
		return false;
	}

	/**
	 * overview : update total information
	 *
	 * @param int	$affId
	 * @param date	$start_date
	 * @param date	$end_date
	 * @return bool
	 */
	public function updateTotalInfo($affId, $start_date, $end_date) {
		$this->load->model(array('total_player_game_minute', 'game_logs', 'transactions'));

		$use_total_minute = $this->utils->getConfig('use_total_minute');

		$players = $this->getAllPlayersUnderAffiliateId($affId);
		// $this->utils->debug_log('players', count($players));
		$total_players = count($players);

		//from transactions
		list($totalDeposit, $totalWithdrawal, $totalBonus, $totalCashback) =
		$this->transactions->getTotalDepositWithdrawalBonusCashbackByPlayers($players, $start_date, $end_date);
		// list($totalBets, $totalWins, $totalLoss) =
		// $this->total_player_game_minute->getTotalBetsWinsLossByPlayers($players, $start_date, $end_date);
		// if ($use_total_minute) {
		// 	list($totalBets, $totalWins, $totalLoss) = $this->total_player_game_minute->getTotalBetsWinsLossByPlayers(
		// 		$players, $start_date, $end_date);
		// 	// $this->utils->printLastSQL();
		// } else {
		list($totalBets, $totalWins, $totalLoss) = $this->game_logs->getTotalBetsWinsLossByPlayers(
			$players, $start_date, $end_date);
		// }

		$total_bet = $this->utils->formatCurrencyNoSym($totalBets);
		$total_win = $this->utils->formatCurrencyNoSym($totalWins);
		$total_loss = $this->utils->formatCurrencyNoSym($totalLoss);
		$total_cashback = $this->utils->formatCurrencyNoSym($totalCashback);
		$total_bonus = $this->utils->formatCurrencyNoSym($totalBonus);
		$total_deposit = $this->utils->formatCurrencyNoSym($totalDeposit);
		$total_withdraw = $this->utils->formatCurrencyNoSym($totalWithdrawal);

		$this->set(array(
			'totalPlayerBet' => $total_bet,
			'totalPlayerWin' => $total_win,
			'totalPlayerLoss' => $total_loss,
			'totalPlayerDeposit' => $total_deposit,
			'totalPlayerWithdraw' => $total_withdraw,
			'totalPlayerCashback' => $total_cashback,
			'totalPlayerBonus' => $total_bonus,
		))->where('affiliateId', $affId);

		return $this->runAnyUpdate('affiliates');
	}

	/**
	 * overview : will randomize alphanumeric and special characters
	 *
	 * @param 	string	$name
	 * @return	string
	 */
	public function randomizer($name) {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
			. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. '0123456789' //!@#$%^&*()
			 . $name); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$randomPassword = '';
		foreach (array_rand($seed, 8) as $k) {
			$randomPassword .= $seed[$k];
		}

		return $randomPassword;
	}

	/**
	 * overview : belong to affiliate username
	 *
	 * @param int		$playerId
	 * @param string	$affUsername
	 * @return bool
	 */
	public function belongToAffUsername($playerId, $affUsername) {
		$this->db->from('player')->join('affiliates', 'player.affiliateId=affiliates.affiliateId')
			->where('affiliates.username', $affUsername)
			->where('player.playerId', $playerId);

		return $this->runExistsResult();
	}

	/**
	 * overview : get affiliate by username
	 *
	 * @param  string	$affiliate_username
	 * @return null
	 */
	public function getAffiliateByUsername($affiliate_username) {
		$this->db->from('affiliates')->where('username', $affiliate_username);
		return $this->runOneRow();
	}

	/**
	 * overview : get main wallet by affiliate id
	 * @param 	int		$affId
	 * @return null|string
	 */
	public function getMainWallet($affId) {
		$this->db->from('affiliates')->where('affiliateId', $affId);
		return $this->runOneRowOneField('wallet_balance');
	}

	/**
	 * overview : get balance wallet
	 * @param  int	$affId
	 * @return null|string
	 */
	public function getBalanceWallet($affId) {
		$this->db->from('affiliates')->where('affiliateId', $affId);
		return $this->runOneRowOneField('wallet_hold');
	}

	/**
	 * overview : increment main wallet
	 * @param int	$affId
	 * @param int	$incAmount
	 * @return bool
	 */
	public function incMainWallet($affId, $incAmount) {
		if ($affId && $incAmount > 0) {
            if(!$this->isResourceInsideLock($affId, Utils::LOCK_ACTION_AFF_BALANCE)){
                return false;
            }
			$this->db->set('wallet_balance', 'wallet_balance+' . $incAmount, false);
			$this->db->where('affiliateId', $affId);
			$this->db->update($this->tableName);

			return true;
		}

		return false;
	}

	/**
	 * overview : decrement main wallet
	 * @param int	$affId
	 * @param int	$decAmount
	 * @return bool
	 */
	public function decBalanceWallet($affId, $decAmount) {
		if ($affId && $decAmount > 0) {
            if(!$this->isResourceInsideLock($affId, Utils::LOCK_ACTION_AFF_BALANCE)){
                return false;
            }

			$this->db->set('wallet_hold', 'wallet_hold-' . $decAmount, false);
			$this->db->where('affiliateId', $affId);
			$this->db->update($this->tableName);

			return true;
		}

		return false;
	}

	/**
	 * overview : decrement main wallet
	 * @param int	$affId
	 * @param $decAmount
	 * @return bool
	 */
	public function decMainWallet($affId, $decAmount) {
		if ($affId && $decAmount > 0) {
            if(!$this->isResourceInsideLock($affId, Utils::LOCK_ACTION_AFF_BALANCE)){
                return false;
            }

			$this->db->set('wallet_balance', 'wallet_balance-' . $decAmount, false);
			$this->db->where('affiliateId', $affId);
			$this->db->update($this->tableName);

			return true;
		}

		return false;
	}

	/**
	 * overview : increment credit balance
	 * @param int	$affId
	 * @param int	$incAmount
	 * @return bool
	 */
	public function incCreditBalance($affId, $incAmount) {
		if ($affId && $incAmount > 0) {
			$this->db->set('balance', 'balance+' . $incAmount, false);
			$this->db->where('affiliateId', $affId);
			$this->db->update($this->tableName);

			return true;
		}

		return false;
	}

	/**
	 * overview : decrement credit balance
	 * @param int	$affId
	 * @param $decAmount
	 * @return bool
	 */
	public function decCreditBalance($affId, $decAmount) {
		if ($affId && $decAmount > 0) {
			$this->db->set('balance', 'balance-' . $decAmount, false);
			$this->db->where('affiliateId', $affId);
			$this->db->update($this->tableName);

			return true;
		}

		return false;
	}

	/**
	 * overview : get parent affiliate
	 *
	 * @param bool|true $add_blank
	 * @return array
	 */
	public function getParentAffKV($add_blank = true) {
		$list = $this->getParentAffList();
		$kvList = array();
		if ($add_blank) {
			$kvList[''] = '------' . lang('N/A') . '------';
		}
		if (!empty($list)) {
			foreach ($list as $aff) {
				$kvList[$aff['affiliateId']] = $aff['username'];
			}
		}
		return $kvList;
	}

	/**
	 * overview : get parent affiliate list
	 *
	 * @return array
	 */
	public function getParentAffList() {
		$this->db->from('affiliates')->where('countSub >', 0);
		return $this->runMultipleRowArray();
	}

	/**
	 * overview : get active tags
	 *
	 * @return bool
	 */
	public function getActiveTags() {

		$query = $this->db->query("SELECT * FROM affiliatetaglist as atl left join adminusers as au on atl.createBy = au.userId"); /*WHERE atl.status = '0'*/

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	public function getActiveTagsKV(){
	    $result = false;
	    $tags_list = $this->getActiveTags();

	    if(empty($tags_list)){
	        return $result;
        }

	    $temp_tag_list = [];
	    foreach ($tags_list as $tag){
            $temp_tag_list[$tag['tagId']] = $tag['tagName'];
        }

        $result = $temp_tag_list;

	    return $result;
    }

    public function getAffiliateTag($affiliate_id, $only_tag_id = FALSE) {
        $sql = "SELECT * FROM affiliatetag WHERE affiliateId = ? ";

        $query = $this->db->query($sql, array($affiliate_id));

        if (!$query->result_array()) {
            return false;
        } else {
            if(FALSE === $only_tag_id){
                return $query->result_array();
            }else{
                $results = $query->result_array();
                $tagIds = [];
                foreach($results as $result){
                    $tagIds[] = $result['tagId'];
                }

                return $tagIds;
            }
        }
    }

	/**
	 * overview : freeze wallet balance
	 * @param int		$affId
	 * @param double	$amount
	 * @return bool
	 */
	public function freezeWalletBalance($affId, $amount) {
		$this->decMainWallet($affId, $amount);
		$this->db->set('frozen', 'frozen+' . $amount, false)->where('affiliateId', $affId);
		return $this->runAnyUpdate('affiliates');
	}

	/**
	 * overview : add withdraw request
	 *
	 * @param int	$affId
	 * @param array $paymentMethod
	 * @param $amount
	 * @return mixed
	 */
	public function addWithdrawRequest($affId, $paymentMethod, $amount) {
		//check balance
		// $available_balance = $this->getMainWalletBalance($affId);
		// if ($this->utils->compareResultFloat($amount, '>=', $available_balance)) {

		$data = array(
			'affiliateId' => $affId,
			'paymentMethod' => $paymentMethod['paymentMethod'],
			'amount' => $amount,
			'fee' => 0,
			'status' => self::STATUS_WITHDRAW_REQUEST,
			'affiliatePaymentId' => $paymentMethod['affiliatePaymentId'],
			'createdOn' => $this->utils->getNowForMysql(),
			'updatedOn' => $this->utils->getNowForMysql(),
		);

		$id = $this->insertData('affiliatepaymenthistory', $data);
		// }

		//frozen
		$success = $this->freezeWalletBalance($affId, $amount) && $id;
		return $id;
	}

	/**
	 * overview : get payment history list
	 *
	 * @param 	int 	$affId
	 * @return  null
	 */
	public function getPaymentHistoryList($affId) {
		$this->db->from('affiliatepaymenthistory')->where('affiliateId', $affId);

		return $this->runMultipleRowArray();
	}

	/**
	 * overview : get search payment
	 *
	 * @param int   $limit
	 * @param int   $offset
	 * @param array $data
	 * @return array
	 */
	public function getSearchPayment($limit, $offset, $data) {

		$search = array();
		$sortby = null;
		$desc_order = null;

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		foreach ($data as $key => $value) {
			if ($key == 'request_range' && $value != '') {
				$search[$key] = "p.createdOn BETWEEN $value";
			} elseif ($key == 'status' && $value != null) {
				$search[$key] = "p.status = " . $value;
			} elseif ($key == 'username') {
				$search[$key] = "a.$key LIKE '%" . $value . "%'";
			} elseif ($value != null) {
				$search[$key] = "a.$key = '" . $value . "'";
			}
		}

		$query = <<<EOD
SELECT p.*, a.username, ap.accountNumber , ap.bankName, adminusers.username as adminuser
FROM affiliatepaymenthistory as p
LEFT JOIN affiliates as a ON p.affiliateId = a.affiliateId
LEFT JOIN affiliatepayment as ap ON p.affiliatePaymentId = ap.affiliatePaymentId
LEFT JOIN adminusers ON p.processedBy = adminusers.userId

EOD;

		if (count($search) > 0) {
			$query .= " WHERE " . implode(' AND ', $search);
		}

		$run = $this->db->query("$query $limit $offset");
		return $run->result_array();
	}

	/**
	 * overview get status list KV
	 *
	 * @return array
	 */
	public function getStatusListKV() {
		return array(
			'' => lang("N/A"),
			self::STATUS_WITHDRAW_REQUEST => lang('Request'),
			self::STATUS_WITHDRAW_APPROVED => lang('Approved'),
			self::STATUS_WITHDRAW_DECLINED => lang('Declined'),
		);
	}

	/**
	 * overview : decline payment
	 *
	 * @param int	 $history_id
	 * @param string $reason
	 * @param int	 $adminUserId
	 * @return bool
	 */
	public function declinePayment($history_id, $reason, $adminUserId) {
		$this->utils->debug_log('history_id', $history_id, 'reason', $reason, 'adminUserId', $adminUserId);
		$success = false;
		$this->db->from('affiliatepaymenthistory')->where('affiliatePaymentHistoryId', $history_id);
		$row = $this->runOneRow();
		if ($row && $row->status==self::STATUS_WITHDRAW_REQUEST) {

			$affId = $row->affiliateId;
			$amount = $row->amount;

			$this->db->set('reason', $reason)
				->set('updatedOn', $this->utils->getNowForMysql())
				->set('processedOn', $this->utils->getNowForMysql())
				->set('processedBy', $adminUserId)
				->set('status', self::STATUS_WITHDRAW_DECLINED)
				->where('affiliatePaymentHistoryId', $history_id)
				->where('status', self::STATUS_WITHDRAW_REQUEST);
			$success = $this->runAnyUpdate('affiliatepaymenthistory');
			if ($success) {
				//put frozen back
				$this->db->set('frozen', 'frozen-' . $amount, false)
					->set('wallet_balance', 'wallet_balance+' . $amount, false)
					->where('affiliateId', $affId);
				$success = $this->runAnyUpdate('affiliates');
			}

		}
		return $success;
	}

	/**
	 * overview : approved payment
	 * @param int	 $history_id
	 * @param string $reason
	 * @param int	 $adminUserId
	 * @return bool
	 */
	public function approvePayment($history_id, $reason, $adminUserId) {
		$this->utils->debug_log('history_id', $history_id, 'reason', $reason, 'adminUserId', $adminUserId);
		$success = false;
		$this->db->from('affiliatepaymenthistory')->where('affiliatePaymentHistoryId', $history_id);
		$row = $this->runOneRow();
		if ($row && $row->status==self::STATUS_WITHDRAW_REQUEST) {

			$affId = $row->affiliateId;
			$amount = $row->amount;

			$this->db->set('reason', $reason)
				->set('updatedOn', $this->utils->getNowForMysql())
				->set('processedOn', $this->utils->getNowForMysql())
				->set('processedBy', $adminUserId)
				->set('status', self::STATUS_WITHDRAW_APPROVED)
				->where('affiliatePaymentHistoryId', $history_id)
				->where('status', self::STATUS_WITHDRAW_REQUEST);
			$success = $this->runAnyUpdate('affiliatepaymenthistory');
			if ($success) {
				$success = $this->transactions->withdrawFromAffFrozen($affId, $amount,
					$adminUserId . ' approve [' . $history_id . '] ' . $amount, $adminUserId);
				//clear frozen
				// $this->db->set('frozen', 'frozen-' . $amount, false)->where('affiliateId', $affId);
				// $success = $this->runAnyUpdate('affiliates');
			} else {
				$this->utils->debug_log('update affiliatepaymenthistory failed');
			}

		} else {
			$this->utils->debug_log('donot find affiliatepaymenthistory', $history_id);
		}
		return $success;
	}

	/**
	 * overview : get balance details
	 *
	 * @param 	int	$affId
	 * @return 	array
	 */
	public function getBalanceDetails($affId) {
		$this->db->from('affiliates')->where('affiliateId', $affId);
		$row = $this->runOneRow();
		$frozen = $row->frozen;
		$main = $row->wallet_balance;
		$wallet_hold = $row->wallet_hold;

		return array('main_wallet' => $main, 'frozen' => $frozen, 'hold' => $wallet_hold,
			'total_balance' => $main + $frozen + $wallet_hold);
	}

	/**
	 * overview : get username by id
	 *
	 * @param 	int	$affiliateId
	 * @return 	null
	 */
	public function getUsernameById($affiliateId) {
		$this->db->from('affiliates')->where('affiliateId', $affiliateId);
		return $this->runOneRowOneField('username');
	}

	/**
	 * overview : existing affiliate domain
	 *
	 * @param $affId
	 * @param $affDomain
	 * @param null $affTrackingId
	 * @return bool
	 */
	public function existsAffDomain($affId, $affDomain, $affTrackingId = null) {
		$this->db->from('affiliates')->where('affdomain', $affDomain);
		if (!empty($affId)) {
			$this->db->where('affiliateId !=', $affId);
		}
		$success = $this->runExistsResult();
		if (!$success) {
			$success = $this->existsAdditionalAffdomain($affDomain, $affTrackingId);
		}
		return $success;
	}

	/**
	 * overview : get affiliate game information and game description
	 *
	 * @param int $affiliateId
	 * @return array
	 */
	public function getAffiliateGameInfoAndGameDesc($affiliateId = null) {
		if (empty($affiliateId)) {
			$affiliateId = 0;
		}
		$this->db->from('affiliate_term_games')->where('affiliate_id', $affiliateId);
		$rows = $this->runMultipleRowArray();

		$gamePlatformList = array();
		$gameTypeList = array();
		$gameDescList = array();

		if (!empty($rows)) {

			foreach ($rows as $row) {
				$gamePlatformList[$row['game_platform_id']] = $row['game_platform_percentage'];
				$gameTypeList[$row['game_type_id']] = $row['game_type_percentage'];
				$gameDescList[$row['game_description_id']] = $row['game_desc_percentage'];
			}
		}

		return array($gamePlatformList, $gameTypeList, $gameDescList);
	}

	/**
	 * overview : get game tree for affiliate
	 *
	 * @param int $affiliateId
	 * @return array
	 */
	public function getGameTreeForAffiliate($affiliateId = null) {
		$this->load->model(array('game_description_model'));

		$gamePlatformList = array();
		$gameTypeList = array();
		$gameDescList = array();
		list($gamePlatformList, $gameTypeList, $gameDescList) = $this->getAffiliateGameInfoAndGameDesc($affiliateId);

		$showGameDescTree = $this->config->item('show_particular_game_in_tree');

		return $this->game_description_model->getGameTreeArray($gamePlatformList, $gameTypeList, $gameDescList, true, $showGameDescTree);

	}

	/**
	 * add by spencer.kuo 2017.05.10
	 */
	public function getGameTreeForVipAffiliateLevel2($affilliate_level_settings) {
		$this->load->model(array('game_description_model'));
		$this->utils->debug_log('affilliate_level_settings', var_export($affilliate_level_settings, true));
		$gamePlatformList = array();
		$gameTypeList = array();
		$gameDescList = array();
		if (!empty($affilliate_level_settings['selected_game_tree']))
			foreach ($affilliate_level_settings['selected_game_tree'] as $game_id => $percent) {
				if (substr($game_id, 0, 3) == 'gp_') {
					$gamePlatformList[substr($game_id, 3, strlen($game_id) - 3)] = $percent;
				} else {
					$gameTypeList[$game_id] = $percent;
				}
			}
		$this->utils->debug_log('gamePlatformList', var_export($gamePlatformList, true));
		$this->utils->debug_log('gameTypeList', var_export($gameTypeList, true));
		return $this->game_description_model->getGameTreeArray2($gamePlatformList, $gameTypeList, $gameDescList, true, false);
	}


	/**
	 * overview : delete affiliate games
	 *
	 * @param null $affiliateId
	 */
	public function deleteAffiliateGames($affiliateId = null) {
		if (empty($affiliateId)) {
			$affiliateId = 0;
		}

		$this->db->delete('affiliate_term_games', array('affiliate_id' => $affiliateId));
	}

	/**
	 * overview : batch add affiliate games
	 *
	 * @param array $gameNumberList
	 * @param int $affiliateId
	 * @return bool
	 */
	public function batchAddAffiliateGames($gameNumberList, $affiliateId = null) {
		$success = true;
		if (empty($affiliateId)) {
			$affiliateId = 0;
		}

		$this->deleteAffiliateGames($affiliateId);
		$cnt = 0;
		$data = array();
		$this->utils->debug_log('gameNumberList count', count($gameNumberList), 'affiliateId', $affiliateId);
		if (!empty($gameNumberList)) {

			foreach ($gameNumberList as $gameDescriptionId) {

				$data[] = array(
					'affiliate_id' => $affiliateId,
					'game_description_id' => $gameDescriptionId['id'],
					'game_type_id' => $gameDescriptionId['game_type_id'],
					'game_platform_id' => $gameDescriptionId['game_platform_id'],
					'game_platform_percentage' => $gameDescriptionId['game_platform_number'],
					'game_type_percentage' => $gameDescriptionId['game_type_number'],
					'game_desc_percentage' => $gameDescriptionId['game_desc_number'],
				);
				if ($cnt >= 500) {
					//insert and clean
					$this->db->insert_batch('affiliate_term_games', $data);
					$data = array();
					$cnt = 0;
				}
				$cnt++;
			}

			if (!empty($data)) {
				$this->db->insert_batch('affiliate_term_games', $data);
			}
		}

		return $success;
	}

	/**
	 * overview : save tracking code
	 *
	 * @param 	int	$affId
	 * @param 	int	$adminUserId
	 * @return bool
	 */
	public function recordTrackingCode($affId, $adminUserId = null) {
		$this->db->from('affiliates')->where('affiliateId', $affId);
		$row = $this->runOneRowArray();
		if (!empty($row) && !empty($row['trackingCode'])) {

			$data = [
				'affiliate_id' => $affId,
				'user_id' => $adminUserId,
				'tracking_code' => $row['trackingCode'],
				'created_at' => $this->utils->getNowForMysql(),
			];
			return $this->insertData('aff_tracking_code_history', $data);
		}
		return false;
	}

	/**
	 * overview : update tracking code
	 *
	 * @param int		$affId
	 * @param string	$trackingCode
	 * @param int		 $adminUserId
	 * @return bool
	 */
	public function updateTrackingCode($affId, $trackingCode, $adminUserId = null) {
		if (!empty($affId) && !empty($trackingCode)) {
			$this->recordTrackingCode($affId, $adminUserId);
			//record old tracking code
			$this->db->set('trackingCode', $trackingCode)
				->set('updatedOn', $this->utils->getNowForMysql())
				->where('affiliateId', $affId);
			return $this->runAnyUpdate('affiliates');
		} else {
			return false;
		}
	}

// '{"terms": {"terms_type": "option1","totalactiveplayer": "10","minimumBetting": "1000","minimumDeposit": "100","provider": [1]}}'
	// {"terms": {"terms_type": "allow","sub_allowed":"manual","manual_open":"manual","sub_link":"link","sub_level":"10","sub_levels":[25,2,1,0,0,0,0,0,0,0]}}
	// {"baseIncomeConfig": "1","level_master":"50","minimumPayAmount": "0","autoTransferToWallet": false,"paymentDay": "1","cashback_fee": "100"}

	/**
	 * overview : merge settings
	 *
	 * @param bool $force
	 * @return bool
	 */
	public function mergeSettings($force = flase) {

		$this->load->model(array('operatorglobalsettings'));

		$settings = self::DEFAULT_AFFILIATE_SETTINGS;

		$settings['sub_level'] = $this->utils->getConfig('subAffiliateLevels');
		$settings['sub_levels'] = array_fill(0, $settings['sub_level'], 0);

		// "affiliate_default_terms"
		// "sub_affiliate_default_terms";
		// affiliate_settings
		$terms = $this->operatorglobalsettings->getSettingJson('affiliate_default_terms');
		$sub_terms = $this->operatorglobalsettings->getSettingJson('sub_affiliate_default_terms');
		$affiliate_settings = $this->operatorglobalsettings->getSettingJson('affiliate_settings');
		if (!empty($terms)) {
			if (isset($terms['terms']['totalactiveplayer'])) {
				$settings['totalactiveplayer'] = $terms['terms']['totalactiveplayer'];
			}
			if (isset($terms['terms']['minimumBetting'])) {
				$settings['minimumBetting'] = $terms['terms']['minimumBetting'];
			}
			if (isset($terms['terms']['minimumDeposit'])) {
				$settings['minimumDeposit'] = $terms['terms']['minimumDeposit'];
			}
			if (isset($terms['terms']['provider'])) {
				$settings['provider'] = $terms['terms']['provider'];
			}
		}
		if (!empty($sub_terms)) {
			if (isset($sub_terms['terms']['manual_open'])) {
				$settings['manual_open'] = !!$sub_terms['terms']['manual_open'];
			}
			if (isset($sub_terms['terms']['sub_link'])) {
				$settings['sub_link'] = !!$sub_terms['terms']['sub_link'];
			}
			if (isset($sub_terms['terms']['sub_level']) && $sub_terms['terms']['sub_level'] > 0) {
				$settings['sub_level'] = intval($sub_terms['terms']['sub_level']);
			}
			if (isset($sub_terms['terms']['sub_levels']) && is_array($sub_terms['terms']['sub_levels'])) {
				$settings['sub_levels'] = $sub_terms['terms']['sub_levels'];
			}
		}

		$settings = $this->fixSubLevel($settings);

		$rlt = true;
		$jsonArr = $this->operatorglobalsettings->getSettingJson('affiliate_common_settings', 'template');
		$notFound = empty($jsonArr);
		$this->utils->debug_log('force', $force, 'notFound', $notFound);
		if ($force || $notFound) {
			if ($notFound) {
				$rlt = $this->operatorglobalsettings->insertSettingJson('affiliate_common_settings',
					$settings, 'template');
			} else {
				$rlt = $this->operatorglobalsettings->putSettingJson('affiliate_common_settings',
					$settings, 'template');
			}
		}

		return $rlt;
	}

	/**
	 * overview : merge affiliate term settings
	 *
	 * @param bool|false $force
	 * @return bool
	 */
	public function mergeAffTermSettings($force = false) {
		$rlt = true;

		$this->db->distinct()->select('affiliateId')->from('affiliate_terms');

		$rows = $this->runMultipleRowArray();

		if (!empty($rows)) {
			foreach ($rows as $row) {
				$affId = $row['affiliateId'];
				$this->db->from('affiliate_terms')->where('affiliateId', $affId);
				$settingRows = $this->runMultipleRowArray();
				if (!empty($settingRows)) {
					$affSettings = $this->getDefaultAffSettings();

					$affDefaultTerms = null;
					$subAffDefaultTerms = null;
					$affAllSettings = null;
					//{"terms": {"terms_type": "option1","totalactiveplayer": "10","minimumBetting": "","minimumDeposit": "","level_master": "50","provider": []}}
					//{"terms":{"terms_type":"allow","manual_open":"manual","sub_link":"link","level_master":false,"sub_level":5,"sub_levels":["20","2","1","0","0"]}}
					foreach ($settingRows as $setting) {
						if ($setting['optionType'] == self::AFF_TERMS_AFFILIATE_DEFAULT_TERMS) {
							$val = $setting['optionValue'];
							$affDefaultTerms = $this->utils->decodeJson($val);

						}
						if ($setting['optionType'] == self::AFF_TERMS_SUB_AFFILIATE_DEFAULT_TERMS) {
							$val = $setting['optionValue'];
							$subAffDefaultTerms = $this->utils->decodeJson($val);

						}

						if ($setting['optionType'] == self::AFF_TERMS_ALL_SETTINGS) {
							$val = $setting['optionValue'];
							$affAllSettings = $this->utils->decodeJson($val);

						}
					}
					if (!empty($affDefaultTerms)) {
						if (isset($affDefaultTerms['terms']['totalactiveplayer'])) {
							$affSettings['totalactiveplayer'] = $affDefaultTerms['terms']['totalactiveplayer'];
						}
						if (isset($affDefaultTerms['terms']['minimumBetting'])) {
							$affSettings['minimumBetting'] = $affDefaultTerms['terms']['minimumBetting'];
						}
						if (isset($affDefaultTerms['terms']['minimumDeposit'])) {
							$affSettings['minimumDeposit'] = $affDefaultTerms['terms']['minimumDeposit'];
						}
						if (isset($affDefaultTerms['terms']['provider'])) {
							$affSettings['provider'] = $affDefaultTerms['terms']['provider'];
						}

					}
					if (!empty($subAffDefaultTerms)) {
						if (isset($subAffDefaultTerms['terms']['manual_open'])) {
							$affSettings['manual_open'] = !!$subAffDefaultTerms['terms']['manual_open'];
						}
						if (isset($subAffDefaultTerms['terms']['sub_link'])) {
							$affSettings['sub_link'] = !!$subAffDefaultTerms['terms']['sub_link'];
						}
						if (isset($subAffDefaultTerms['terms']['sub_level']) && $subAffDefaultTerms['terms']['sub_level'] > 0) {
							$affSettings['sub_level'] = intval($subAffDefaultTerms['terms']['sub_level']);
						}
						if (isset($subAffDefaultTerms['terms']['sub_levels']) && is_array($subAffDefaultTerms['terms']['sub_levels'])) {
							$affSettings['sub_levels'] = $subAffDefaultTerms['terms']['sub_levels'];
						}

					}
					if (!empty($affAllSettings)) {
						$affSettings = $affAllSettings;
					}

					$affSettings = $this->fixSubLevel($affSettings);

					$this->db->where('affiliateId', $affId)->where('optionType', self::AFF_TERMS_ALL_SETTINGS)
						->delete('affiliate_terms');

					//insert it
					$data = [
						'affiliateId' => $affId,
						'optionType' => self::AFF_TERMS_ALL_SETTINGS,
						'optionValue' => $this->utils->encodeJson($affSettings),
					];

					$rlt = $this->insertData('affiliate_terms', $data);

				}
			}
		}

		return $rlt;
	}

	const AFF_TERMS_ALL_SETTINGS = 'all_settings';
	const AFF_TERMS_AFFILIATE_DEFAULT_TERMS = "affiliate_default_terms";
	const AFF_TERMS_SUB_AFFILIATE_DEFAULT_TERMS = "sub_affiliate_default_terms";

	/**
	 * get affiliate term settings
	 *
	 * @param 	int	$affId
	 * @return null
	 */
	public function getAffTermsSettings($affId) {

		if (empty($affId)) {
			return null;
		}

		$this->db->from('affiliate_terms');
		$this->db->where('affiliateId', $affId);
		$this->db->where('optionType', self::AFF_TERMS_ALL_SETTINGS);

		$row = $this->runOneRowArray();

		$defaultAffSettings=$this->getDefaultAffSettings();

		if (empty($row)) {
			//insert it
			$data = [
				'affiliateId' => $affId,
				'optionType' => self::AFF_TERMS_ALL_SETTINGS,
				'optionValue' => json_encode($defaultAffSettings),
			];

			$this->insertData('affiliate_terms', $data);

			$this->db->from('affiliate_terms')->where('affiliateId', $affId)
				->where('optionType', self::AFF_TERMS_ALL_SETTINGS);

			$row = $this->runOneRowArray();
		}

		if ($row) {
			$settings = $this->utils->decodeJson($row['optionValue']);
		}

		if (empty($settings)) {
			$settings = self::DEFAULT_AFFILIATE_SETTINGS;
		}

		/**
		 * add by spencer.kuo
		 */
		$backup_settings = $settings;
		/** end */
		if(!$this->utils->isEnabledFeature('individual_affiliate_term')){
			$setback=['baseIncomeConfig', 'admin_fee', 'transaction_fee', 'bonus_fee', 'cashback_fee',
				'minimumPayAmount', 'paymentDay', 'autoTransferToWallet', 'autoTransferToLockedWallet',
				'totalactiveplayer'];
			foreach ($setback as $item_name) {
				$settings[$item_name]=@$defaultAffSettings[$item_name];
			}
		}

		/**
		 * add by spencer.kuo
		 */
		if ($this->utils->isEnabledFeature('switch_to_ibetg_commission')) {
			$settings['baseIncomeConfig'] = @$backup_settings['baseIncomeConfig'];
		}
		/** end */
		return $this->mergeDefaultAffiliateSettings($settings);
	}

	// 	"baseIncomeConfig" => self::INCOME_CONFIG_TYPE_BET_WIN,
	// 	"level_master" => 50,
	// 	"admin_fee" => 0,
	// 	"transaction_fee" => 0,
	// 	"bonus_fee" => 0,
	// 	"cashback_fee" => 100,
	// 	"minimumPayAmount" => 0,
	// 	"paymentDay" => 1,
	// 	"autoTransferToWallet" => false,
	// 	"autoTransferToLockedWallet" => false,
	// 	// "terms_type"=> "option1",
	// 	"totalactiveplayer" => 10,
	// 	"minimumBetting" => 1000,
	// 	"minimumDeposit" => 100,
	// 	"provider" => [], // api id
	// 	'provider_betting_amount'
	// 	"manual_open" => true,
	// 	"sub_link" => true,
	// 	"sub_level" => 10,
	// 	"sub_levels" => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],

	const IGNORE_INDIVIDUAL_TERM = ['baseIncomeConfig', 'admin_fee', 'transaction_fee', 'bonus_fee', 'cashback_fee',
		'minimumPayAmount', 'paymentDay', 'autoTransferToWallet', 'autoTransferToLockedWallet', 'sub_level', 'sub_levels'];

	/**
	 * overview : merge default affiliate settings
	 *
	 * @param $settings
	 * @return array
	 */
	public function mergeDefaultAffiliateSettings($settings) {
		$defaultSettings = $this->getDefaultAffSettings();
		// $emptySettings = self::DEFAULT_AFFILIATE_SETTINGS;
		foreach ($settings as $key => $value) {
			$ignore = false;
			if (!$this->utils->isEnabledFeature('individual_affiliate_term')) {
				//ignore term
				foreach (self::IGNORE_INDIVIDUAL_TERM as $term_key) {
					if ($term_key == $key) {
						$ignore = true;
						break;
					}
				}
			} else {
				$ignore = $key == 'sub_level';
			}
			/**
			 * add by spencer.kuo
			 */
			if ($this->utils->isEnabledFeature('switch_to_ibetg_commission')) {
				if ($key == 'baseIncomeConfig') $ignore = false;
			}
			/** end */
			//ignore sub_levels
			if (!$ignore) {
				$defaultSettings[$key] = $value;
			}
		}

		return $this->fixTermSettings($settings);

		// foreach ($defaultSettings['sub_levels'] as $key => $value) {
		// 	if( isset($settings['sub_levels'][$key]) ){
		// 		$defaultSettings['sub_levels'][$key]=$settings['sub_levels'][$key];
		// 	}
		// }

		// return $defaultSettings;
	}

	/**
	 * overview : get default affiliate settings
	 *
	 * @return array
	 */
	public function getDefaultAffSettings() {
		$this->load->model(array('operatorglobalsettings'));
		//load from affiliate_common_settings
		$jsonArr = $this->operatorglobalsettings->getSettingJson('affiliate_common_settings', 'template');

		//should fix sub level
		$jsonArr = $this->fixDefaultAffTermSettings($jsonArr);

		return $jsonArr;
	}

	/**
	 * get empty term settings
	 *
	 * @return array
	 */
	public function getEmptyTermSettings() {
		$emptySettings = self::DEFAULT_AFFILIATE_SETTINGS;
		$this->load->model(['external_system']);
		// $provider_betting_amount=$emptySettings['provider_betting_amount'];
		//fill by game api
		$games = $this->external_system->getAllActiveSytemGameApi();
		foreach ($games as $g) {
			$emptySettings['provider_betting_amount'][$g['id']] = 0.0;
		}

		return $emptySettings;
	}

	/**
	 * overview : fix default affiliate term settings
	 *
	 * @param array $settings
	 * @return array
	 */
	public function fixDefaultAffTermSettings($settings) {
		$defSettings = $this->getEmptyTermSettings();

		foreach ($settings as $key => $value) {
			if (isset($defSettings[$key])) {
				$defSettings[$key] = $value;
			}
		}

		return $this->fixTermSettings($defSettings);
	}

	/**
	 * overview : fix term settings
	 *
	 * @param 	array	$settings
	 * @return 	array
	 */
	public function fixTermSettings($settings) {
		$settings = $this->fixSubLevel($settings);
		$settings = $this->fixGameProvider($settings);
		return $settings;
	}

	/**
	 * overview : fix game provider
	 *
	 * @param 	array	$settings
	 * @return 	array
	 */
	public function fixGameProvider($settings) {
		$this->load->model(['external_system']);
		// $provider_betting_amount=$emptySettings['provider_betting_amount'];
		//fill by game api
		$games = $this->external_system->getAllActiveSytemGameApi();
		foreach ($games as $g) {
			if (!isset($settings['provider_betting_amount'][$g['id']])) {
				$settings['provider_betting_amount'][$g['id']] = 0;
			}
		}
		return $settings;
	}

	/**
	 * overview : fix sub level
	 *
	 * @param 	array	$settings
	 * @return 	array
	 */
	public function fixSubLevel($settings) {
		$max_sub_level = $this->utils->getConfig('subAffiliateLevels');
		// if(!isset($settings['sub_level'])){
		$settings['sub_level'] = $max_sub_level;
		// }
		//pad sub_levels by sub_level
		if (!isset($settings['sub_levels']) || empty($settings['sub_levels'])) {
			$settings['sub_levels'] = array_fill(0, $max_sub_level, 0);
		} else {
			if (count($settings['sub_levels']) > $max_sub_level) {
				$settings['sub_levels'] = array_slice($settings['sub_levels'], 0, $max_sub_level);
			} else if (count($settings['sub_levels']) < $max_sub_level) {
				$settings['sub_levels'] = array_pad($settings['sub_levels'], $max_sub_level, 0);
			}
		}

		return $settings;
	}

	/**
	 * overview : fix sub level for all settings
	 *
	 * @return bool
	 */
	public function fixSubLevelForAllSettings() {
		$success = true;

		$commonSettings = $this->operatorglobalsettings->getSettingJson('affiliate_common_settings', 'template');
		$commonSettings = $this->fixSubLevel($commonSettings);
		//save back
		$success = $this->operatorglobalsettings->putSettingJson('affiliate_common_settings',
			$settings, 'template');

		$this->db->from('affiliate_terms')->where('optionType', self::AFF_TERMS_ALL_SETTINGS);
		$rows = $this->runMultipleRowArray();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$settings = $this->utils->decodeJson($row['optionValue']);
				$settings = $this->fixSubLevel($settings);

				$this->db->set('optionValue', $this->utils->encodeJson($settings))->where('id', $row['id']);

				$success = $success && $this->runAnyUpdate('affiliate_terms');

			}
		}

		return $success;
	}

	/**
	 * overview : merge to affiliate common settings
	 *
	 * @param 	array	$settings
	 * @return  array
	 */
	public function mergeToAffiliateCommonSettings($settings, $mode = null) {
		$defSettings = $this->getDefaultAffSettings();
		$newSettings = array_merge($defSettings, $settings);

		// foreach ($settings as $key => $value) {
		// 	if (isset($defSettings[$key])) {
		// 		$defSettings[$key] = $value;
		// 	}
		// }

		$success = $this->operatorglobalsettings->putSettingJson('affiliate_common_settings', $newSettings, 'template');

		# GET ALL UNTOUCHED AFFILIATES
		// if ($mode != 'operator_settings') {
			$this->db->select('affiliateId')->from('affiliate_terms')->where($mode, 0);
			$affiliates = $this->runMultipleRowArray();
			$affiliateIds = array_column($affiliates, 'affiliateId');
			array_walk($affiliateIds, function ($affiliateId) use ($settings) {
				$this->mergeToAffiliateSettings($settings, $affiliateId);
			});
		// }

		return $success;
	}

	/**
	 * overview : merge affiliate settings
	 *
	 * @param array $settings
	 * @param int	$affId
	 * @return bool
	 */
	public function mergeToAffiliateSettings($settings, $affId, $mode = null) {
		$defSettings = $this->getDefaultAffSettings();

		$affSettings = $this->getAffTermsSettings($affId);
		//merge aff to default
		foreach ($affSettings as $key => $value) {
			if (isset($defSettings[$key])) {
				$defSettings[$key] = $value;
			}
		}
		//merge input settings to default
		foreach ($settings as $key => $value) {
			if (isset($defSettings[$key])) {
				$defSettings[$key] = $value;
			}
		}
//		$this->utils->debug_log('defSettings', $defSettings, 'affSettings', $affSettings, 'settings', $settings);

		//update
		if ($mode && in_array($mode, array('operator_settings', 'commission_setup', 'sub_affiliate_settings'))) {
			$this->db->set($mode, 1);
		}

		$this->db->where('affiliateId', $affId)->where('optionType', self::AFF_TERMS_ALL_SETTINGS)
			->set('optionValue', $this->utils->encodeJson($defSettings));
		$success = $this->runAnyUpdate('affiliate_terms');

		return $success;
	}

	/**
	 * overview : update affiliate domain
	 *
	 * @param int $affId
	 * @param string $affdomain
	 * @return bool
	 */
	public function updateAffdomain($affId, $affdomain) {
		$this->db->set('affdomain', $affdomain)->where('affiliateId', $affId);

		return $this->runAnyUpdate('affiliates');
	}

	/**
	 * overview : check if dedicated affiliate domain exist
	 *
	 * @param string $dediaffdomain
	 * @return bool
	 */
	public function existsDedicatedAffdomain($dediaffdomain) {
		$this->db->from('affiliates')
			->where('affiliates.affdomain =', $dediaffdomain)
			->where('affiliates.deleted_at is null', null, false);

		return $this->runExistsResult();
	}

	/**
	 * overview : import affiliate tracking code and domain
	 */
	public function importAffiliateTrackingCodeAndDomain() {
		$this->db->select('affiliateId, trackingCode, affdomain')->from('affiliates')
			->where('trackingCode is not null', null, false)->or_where('affdomain is not null', null, false);
		$rows = $this->runMultipleRowArray();

		$this->utils->printLastSQL();
		foreach ($rows as $row) {
			$affId = $row['affiliateId'];
			$trackingCode = $row['trackingCode'];
			$affdomain = $row['affdomain'];

			if (!empty($trackingCode)) {
				//ignore duplicate
				$this->db->select('aff_id')->from('aff_tracking_link')->where('tracking_code', $trackingCode);
				if ($this->runExistsResult()) {

					$this->utils->error_log('ignore duplicate tracking code', $affId, $trackingCode);

				} else {

					//insert
					$data = [
						'aff_id' => $affId,
						'tracking_code' => $trackingCode,
						'tracking_type' => self::TRACKING_TYPE_CODE,
						'created_at' => $this->utils->getNowForMysql(),
						'updated_at' => $this->utils->getNowForMysql(),
					];
					$this->insertData('aff_tracking_link', $data);

				}
			}

			if (!empty($affdomain)) {
				$this->db->select('aff_id')->from('aff_tracking_link')->where('tracking_domain', $affdomain);
				if ($this->runExistsResult()) {

					$this->utils->error_log('ignore duplicate tracking domain', $affId, $affdomain);

				} else {
					//insert
					$data = [
						'aff_id' => $affId,
						'tracking_domain' => $affdomain,
						'tracking_type' => self::TRACKING_TYPE_DOMAIN,
						'created_at' => $this->utils->getNowForMysql(),
						'updated_at' => $this->utils->getNowForMysql(),
					];

					$this->insertData('aff_tracking_link', $data);
				}
			}
		}
	}

	/**
	 * overview : check if sub affiliate is available
	 *
	 * @param int	$affId
	 * @return bool
	 */
	public function isAvailableSubAffiliate($affId) {
		$available = false;
		if ($affId) {
			$commonSettings = $this->getAffTermsSettings($affId);
			$available = $commonSettings['manual_open'] == true || $commonSettings['sub_link'] == true;
		}
		return $available;
	}

	/**
	 * overview : get additional domain list
	 *
	 * @param  int		$affId
	 * @return array	null
	 */
	public function getAdditionalDomainList($affId) {
		$this->db->from('aff_tracking_link')->where('aff_id', $affId)->where('tracking_type', self::TRACKING_TYPE_DOMAIN);
		$this->db->where('aff_tracking_link.deleted_at is null', null, false);
//		$this->ignoreDeleted('deleted_at');

		$rows = $this->runMultipleRowArray();
		return $rows;
	}

	/**
	 * overview : check if additional affiliate domain exist
	 *
	 * @param string $affdomain
	 * @param null $affTrackingId
	 * @return bool
	 */
	public function existsAdditionalAffdomain($affdomain, $affTrackingId = null) {
		//ignore self and deleted
		$this->db->from('aff_tracking_link')->where('tracking_domain', $affdomain)->where('tracking_type', self::TRACKING_TYPE_DOMAIN);
		if ($affTrackingId) {
			$this->db->where('id !=', $affTrackingId);
		}
		$this->db->where('aff_tracking_link.deleted_at is null', null, false);
//		$this->ignoreDeleted('deleted_at');

		return $this->runExistsResult();
	}

	/**
	 * overview : update additional affiliate domain
	 *
	 * @param int		$affTrackingId
	 * @param string	$affdomain
	 * @return bool
	 */
	public function updateAdditionalAffdomain($affTrackingId, $affdomain) {
		$this->db->set('tracking_domain', $affdomain)->where('id', $affTrackingId);

		return $this->runAnyUpdate('aff_tracking_link');
	}

	/**
	 * overview : new additional affiliate domain
	 * @param int	$affId
	 * @param int	$affdomain
	 * @return mixed
	 */
	public function newAdditionalAffdomain($affId, $affdomain) {
		$data = [
			'tracking_domain' => $affdomain,
			'aff_id' => $affId,
			'tracking_type' => self::TRACKING_TYPE_DOMAIN,
			'created_at' => $this->utils->getNowForMysql(),
			'updated_at' => $this->utils->getNowForMysql(),
		];

		return $this->insertData('aff_tracking_link', $data);
	}

	/**
	 * overview : remove additional affiliate domain
	 *
	 * @param $affTrackingId
	 * @return bool
	 */
	public function removeAdditionalAffdomain($affTrackingId) {
		$this->db->set('deleted_at', $this->utils->getNowForMysql())
		  ->set('tracking_domain', 'concat_ws("-","deleted",id,tracking_domain)',false)
		  ->where('id', $affTrackingId);

		return $this->runAnyUpdate('aff_tracking_link');
	}

	/**
	 * overview : get source code list
	 *
	 * @param $affId
	 * @return null
	 */
	public function getSourceCodeList($affId) {
		$this->db->from('aff_tracking_link')->where('aff_id', $affId)->where('tracking_type', self::TRACKING_TYPE_SOURCE_CODE);
		$this->db->where('aff_tracking_link.deleted_at is null', null, false);
//		$this->ignoreDeleted('deleted_at');

		$rows = $this->runMultipleRowArray();
		return $rows;
	}

	/**
	 * overview : update source code
	 *
	 * @param $affTrackingId
	 * @param $sourceCode
	 * @return bool
	 */
	public function updateSourceCode($affTrackingId, $sourceCode, $remarks = null) {
		$this->db->set('tracking_source_code', $sourceCode)
            ->set('remarks', $remarks)
            ->where('id', $affTrackingId);

		return $this->runAnyUpdate('aff_tracking_link');

	}

	/**
	 * overview : new source code
	 *
	 * @param string $sourceCode
	 * @return mixed
	 */
	public function newSourceCode($affId, $sourceCode, $remarks = null) {
		$data = [
			'tracking_source_code' => $sourceCode,
			'aff_id' => $affId,
			'tracking_type' => self::TRACKING_TYPE_SOURCE_CODE,
            'remarks' => $remarks,
			'created_at' => $this->utils->getNowForMysql(),
			'updated_at' => $this->utils->getNowForMysql(),
		];

		return $this->insertData('aff_tracking_link', $data);

	}

	/**
	 * overview : check if source code exist
	 *
	 * @param $affId
	 * @param $sourceCode
	 * @param null $affTrackingId
	 * @return bool
	 */
	public function existsSourceCode($affId, $sourceCode, $affTrackingId = null) {
		//ignore self and deleted
		$this->db->from('aff_tracking_link')->where('tracking_source_code', $sourceCode)
			->where('tracking_type', self::TRACKING_TYPE_SOURCE_CODE)
			->where('aff_id', $affId);
		if ($affTrackingId) {
			$this->db->where('id !=', $affTrackingId);
		}
		$this->db->where('aff_tracking_link.deleted_at is null', null, false);
//		$this->ignoreDeleted('deleted_at');

		return $this->runExistsResult();

	}

	/**
	 * overview : remove source code
	 *
	 * @param  int	$affTrackingId
	 * @return bool
	 */
	public function removeSourceCode($affTrackingId) {
		$this->db->set('deleted_at', $this->utils->getNowForMysql())->where('id', $affTrackingId);

		return $this->runAnyUpdate('aff_tracking_link');
	}

	/**
	 * overview : delete banner
	 *
	 * @param int	$banner_id
	 */
	public function deleteBanner($banner_id) {
		$this->db->where('bannerId', $banner_id)->set('status', self::STATUS_DELETED)->set('bannerName', random_string());
		$this->runAnyUpdate('banner');
	}

	/**
	 * overview : get search banner
	 *
	 * @param array $data
	 * @return array
	 */
	public function getSearchBanner($data) {
		$search = array();
		$sortby = null;
		$desc_order = null;

		// if ($limit != null) {
		// 	$limit = "LIMIT " . $limit;
		// }

		// if ($offset != null && $offset != 'undefined') {
		// 	$offset = "OFFSET " . $offset;
		// } else {
		// 	$offset = ' ';
		// }

		foreach ($data as $key => $value) {
			if ($key == 'sign_time_period' && $value != '') {
				if ($value == 'week') {
					$search[$key] = "b.createdOn >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
				} elseif ($value == 'month') {
					$search[$key] = "b.createdOn >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
				} elseif ($value == 'past') {
					$search[$key] = "b.createdOn >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)";
				}
			} elseif ($key == 'signup_range' && $value != '') {
				$search[$key] = "b.createdOn BETWEEN $value";
			} elseif ($key == 'status' && $value != null) {
				if ($value == 'active') {
					$search[$key] = "b.status = '0'";
				} elseif ($value == 'inactive') {
					$search[$key] = "b.status = '1'";
				}

			}

		}
		$query = "SELECT b.* FROM banner as b  where status!= '".self::STATUS_DELETED."' AND deleted_at IS NULL";

		if (count($search) > 0) {
			$query .= ' AND '.implode(' AND ', $search);
		}

		$run = $this->db->query($query);
		return $run->result_array();
	}

	/**
	 * overview : get affiliate by tracking code
	 *
	 * @param  string $code
	 * @return array
	 */
	public function getAffByTrackingCode($code) {
		$this->db->where('trackingCode', $code)->from('affiliates');
		$aff = $this->runOneRowArray();

		return $aff;

	}

	/**
	 * overview :get banner url by id
	 *
	 * @param $bannerId
	 * @return null|string
	 */
	public function getBannerUrlById($bannerId) {
		if (!empty($bannerId)) {
			$aff_banner_url = $this->utils->getConfig('aff_banner_url');
			if (empty($aff_banner_url)) {
				$aff_banner_url = $this->utils->getSystemUrl('player') . '/upload/banner';
				$this->utils->addSuffixOnMDB($aff_banner_url);
			}

			$types = ['gif', 'jpg', 'png', 'jpeg'];
			$ext = 'jpg';
			$upload = rtrim(realpath($this->utils->getUploadPath()).'/banner', '/');
			$this->utils->addSuffixOnMDB($upload);
			foreach ($types as $type) {
				$path = $upload . '/' . $bannerId . '.' . $type;
				if (file_exists($path)) {
					$ext = $type;
					break;
				}
			}

			return $aff_banner_url . '/' . $bannerId . '.' . $ext;
		}

		return null;
	}

	/**
	 * overview : get internal banner url by id
	 *
	 * @param  int $bannerId
	 * @return null|string
	 */
	public function getInternalBannerUrlById($bannerId) {
		if (!empty($bannerId)) {

			$types = ['gif', 'jpg', 'png', 'jpeg'];
			$ext = 'jpg';
			$upload = rtrim(realpath($this->utils->getUploadPath()).'/banner', '/');
			$this->utils->addSuffixOnMDB($upload);
			foreach ($types as $type) {
				$path = $upload . '/' . $bannerId . '.' . $type;
				if (file_exists($path)) {
					$ext = $type;
					break;
				}
			}

			return '/banner/' . $bannerId . '.' . $ext;
		}

		return null;
	}

	/**
	 * overview : add banner
	 *
	 * @param  int $banner
	 * @return mixed
	 */
	public function addBanner($banner) {
		return $this->insertData('banner', $banner);
	}

	/**
	 * overview : update banner
	 *
	 * @param array		$data
	 * @param int		$banner_id
	 * @return bool
	 */
	public function editBanner($data, $banner_id) {
		$this->db->where('bannerId', $banner_id)->set($data);
		// $this->db->update('banner', $data);
		return $this->runAnyUpdate('banner');
	}

	/**
	 * overview : soft delete banner
	 *
	 * @param array		$data
	 * @param int		$banner_id
	 * @return bool
	 */
	public function softDeleteBanner($data,$banner_id){
		$this->db->where('bannerId',$banner_id)->set($data);
		return $this->runAnyUpdate('banner');
	}

	/**
	 * overview : check if ba
	 *
	 * @param $banner_name
	 * @return bool
	 */
	public function existsBannerByName($banner_name) {
		$this->db->from('banner')->where('bannerName', $banner_name);

		return $this->runExistsResult();
	}

	/**
	 * overview : get banner local path by id
	 *
	 * @param $bannerId
	 * @return string
	 */
	public function getBannerLocalPathById($bannerId) {
		// $this->db->from('banner')->where('bannerName', $banner_name)->where('status !=', self::STATUS_DELETED)->limit(1);
		// $row=$this->runOneRowArray();
		// $path=null;
		// if($row){

		// }

		$types = ['gif', 'jpg', 'png', 'jpeg'];
		$upload = rtrim(realpath($this->utils->getUploadPath()).'/banner', '/');
		$this->utils->addSuffixOnMDB($upload);
		foreach ($types as $type) {
			$path = $upload . '/' . $bannerId . '.' . $type;
			if (file_exists($path)) {
				break;
			}
		}

		return $path;
	}

	/**
	 * overview : get banner details
	 *
	 * @param $banner_id
	 * @return array
	 */
	public function getBannerDetails($banner_id) {
		$this->db->select('*')->from('banner');
		$this->db->where('bannerId', $banner_id);

		$row = $this->runOneRowArray();
		if ($row) {
			$row['bannerName'] = htmlspecialchars_decode($row['bannerName']);
			$row['bannerURL'] = site_url('/affiliate_management/get_banner/' . $row['bannerId']);
		}

		return $row;
	}

	public $all_terms = null;

	public function get_affiliate_settings($affiliate_id, $enabled_single = TRUE) {
		$affiliates_settings = $this->getAllAffiliateTerm($enabled_single);
		return isset($affiliates_settings[$affiliate_id]) ? $affiliates_settings[$affiliate_id] : $affiliates_settings[-1];
	}

	/**
	 * overview : get all affiliate term
	 *
	 * @param bool|true $enabled_single
	 * @return array
	 */
	public function getAllAffiliateTerm($enabled_single = true) {

		if ($enabled_single && isset($this->all_terms) && ! empty($this->all_terms)) {
			return $this->all_terms;
		}

		$affiliate_terms = array(-1 => $this->getDefaultAffSettings());

		$query = $this->db->select('affiliateId')
			->get('affiliates');

		$affiliates = $query->result_array();

		foreach ($affiliates as $affiliate) {
			$affiliate_terms[$affiliate['affiliateId']] = $this->getAffTermsSettings($affiliate['affiliateId']);
		}

		$this->all_terms = $affiliate_terms;

		return $affiliate_terms;
	}

	/**
	 * overview : get all allowed parent affiliates
	 *
	 * @return array
	 */
	public function getAllAllowedParentAffiliates() {
		$this->db->select('affiliateId,username')->from($this->tableName)
			->where('status !=', self::STATUS_DELETED)
			->order_by('username');
		// $this->db->where('parentId', self::PARENT_AFFILIATE);
		$query = $this->db->get();
		return $query->result_array();
	}

	# KAISER
	public function getAffiliateHierarchy($id = NULL, $params = array()) {

		$this->db->select('affiliateId');
		$this->db->select('parentId');
		$this->db->select('username');
		$this->db->from('affiliates');
		if ($params) {
			$this->db->where($params);
		}
		$query = $this->db->get();

		$affiliates = array_column($query->result_array(), null, 'affiliateId');

		foreach ($affiliates as $affiliateId => &$affiliate) {
			$parentId = @$affiliate['parentId'];
			if ($parentId) {
				$affiliates[$parentId]['sub_affiliates'][] = &$affiliate;
			}
		}

		return isset($affiliates[$id]) ? $affiliates[$id] : array_values(array_filter($affiliates, function (&$affiliate) {
			$result = @$affiliate['parentId'] == 0;
			unset($affiliate['parentId']);
			return $result;
		}));

	}

	/**
	 *
	 * returns all downline affiliate ids including the passed affiliate id
	 *
	 * @param  int $id affiliate id
	 * @param  array  $params The param of $this->db->where().
	 * @return array
	 */
	public function includeAllDownlineAffiliateIds($id = NULL, $params = array()) {
		$downlineAffiliates = array($id);
		// OGP-12294: original condition ( if ($id) ) works when $id < 0
		// the case is invalid and return is unexpected (returns all players)
		if ($id > 0) {
		// if ($id) {
            if($this->utils->isEnabledFeature('enable_affiliate_downline_by_level')){
                $affiliate_settings = $this->get_affiliate_settings($id);
                $sub_settings = $affiliate_settings['sub_levels'];
                $sub_level = 0;
                if(count($sub_settings) > 0){
                    foreach($sub_settings as $key => $val){
                        if($val > 0){
                            $sub_level = $key+1;
                        }
                    }
                    if($sub_level > 0){

                        $hierarchy = $this->getAffiliateHierarchy($id, $params);
                        if(isset($hierarchy['sub_affiliates'])){
                            $hierarchy = $this->levelUp($hierarchy['sub_affiliates'], 1, $sub_level);

                            array_walk_recursive($hierarchy, function ($val, $key) use (&$downlineAffiliates) {
                                if ($key == 'affiliateId') {
                                    $downlineAffiliates[] = $val;
                                }
                            });
                            $downlineAffiliates = array_unique($downlineAffiliates);
                        }
                    }
                }

            }else {

                $hierarchy = $this->getAffiliateHierarchy($id, $params);
                array_walk_recursive($hierarchy, function ($val, $key) use (&$downlineAffiliates) {
                    if ($key == 'affiliateId') {
                        $downlineAffiliates[] = $val;
                    }
                });
                $downlineAffiliates = array_unique($downlineAffiliates);
            }
		}
		return $downlineAffiliates;
	}

    function levelUp(&$array, $level = 1, $sub_level){

        foreach($array as $key2 => &$value) {
            if(is_array($value)) {
                if(isset($value['affiliateId'])) {
                    if($level <= $sub_level ){
                        $value['level'] = $level;
                    }else{
                        $value = [];
                    }
                    $this->levelUp($value, $level + 1, $sub_level);
                }else{
                    $this->levelUp($value, $level, $sub_level);
                }
            }
        }

        return $array;
    }


	# KAISER
	public function includeAllDownlinePlayerIds($affiliateId = NULL, $params = array()) {
		$downlineAffiliates = is_array($affiliateId) ? $affiliateId : $this->includeAllDownlineAffiliateIds($affiliateId, $params);
        $downlinePlayers = $this->getAllPlayersUnderAffiliateId($downlineAffiliates,
            isset($params['createdOn >=']) ? $params['createdOn >='] : NULL,
            isset($params['createdOn <=']) ? $params['createdOn <='] : NULL);
		return $downlinePlayers;
	}

	public function getAllPlayerIdByAffiliateId($affiliate_id) {

		$this->db->select('player.*')->from('player')
			->where('player.affiliateId', $affiliate_id);

		$rows = $this->runMultipleRowArray();
		$idArr = [];
		if (!empty($rows)) {

			foreach ($rows as $row) {
				$idArr[] = $row['playerId'];
			}
		}

		return $idArr;
	}

	/*
		 * Git Issue: #107
		 * Modified by: Asrii
		 * Modified datetime: 2016-10-24 02:31:00
		 * Modification Description: Added domain list visibility for affiliates downline
	*/
	public function getAffiliateDomain($affiliateId) {

		$this->db->select('*');
		$this->db->from('domain');
		$this->db->where('domain.status', self::DOMAIN_STATUS_ENABLED);
		$this->db->where('domain.show_to_affiliate', self::SHOW_TO_ALL_AFFILIATES);
		$allAffiliates = $this->runMultipleRowArray();

		$this->db->select('*');
		$this->db->from('domain');
		$this->db->join('affiliate_domain', 'affiliate_domain.domainId = domain.domainId');
		$this->db->where('domain.status', self::DOMAIN_STATUS_ENABLED);
		$this->db->where('domain.show_to_affiliate', self::SHOW_TO_SELECTED_AFFILIATES);
		$this->db->where('affiliate_domain.affiliateId', $affiliateId);
		$selectedAffiliates = $this->runMultipleRowArray();

		$this->db->select('*');
		$this->db->from('domain');
		$this->db->join('affiliate_domain', 'affiliate_domain.domainId = domain.domainId');
		$this->db->join('affiliates', 'affiliates.parentId = affiliate_domain.affiliateId');
		$this->db->where('domain.status', self::DOMAIN_STATUS_ENABLED);
		$this->db->where('domain.show_to_affiliate', self::SHOW_TO_SELECTED_AFFILIATES);
		$this->db->where('affiliates.affiliateId', $affiliateId);
		$withSelectedParents = $this->runMultipleRowArray();

		$rows = array_merge(($allAffiliates ?: array()), ($selectedAffiliates ?: array()), ($withSelectedParents ?: array()));
		$this->utils->debug_log('GETAFFILIATEDOMAIN >------------------------> ', json_encode($rows));
		return $rows;
	}

	/**
	 * overview : existing prefix
	 *
	 * @param $affId
	 * @param string $prefix_for_player
	 * @return bool
	 */
	public function existsPrefix($affId, $prefix_for_player, $affTrackingId = null) {
		$this->db->from('affiliates')->where('prefix_for_player', $prefix_for_player);
		$this->db->where('affiliateId !=', $affId);
		$success = $this->runExistsResult();
		return $success;
	}

	/**
	 * overview : get prefix_of_player by trackingCode
	 *
	 * @param string
	 * @return prefix_of_player or null
	 */
	public function getPrefixByTrackingCode($trackingCode) {
		$this->db->select('prefix_of_player')->from('affiliates')->where('trackingCode', $trackingCode);

		return $this->runOneRowOneField('prefix_of_player');
	}

	/**
	 * generate affdomain
	 * @return string affdomain
	 */
	public function generateAffdomain(){
		$affdomain=null;
		$auto_domain_pattern=$this->utils->getConfig('auto_domain_pattern');

		if(!empty($auto_domain_pattern)){

			$this->load->model(['operatorglobalsettings']);
			$last_affdomain_number=$this->operatorglobalsettings->getSettingIntValue('last_affdomain_number');
			if($last_affdomain_number<=0){
				$last_affdomain_number=$this->utils->getConfig('auto_domain_start');
			}

			while(true){

				$last_affdomain_number++;

				$affdomain=str_replace('{AFFDOMAIN}', $last_affdomain_number,$auto_domain_pattern);

				$this->utils->debug_log('generate affdomain', $last_affdomain_number, $affdomain);

				$this->db->select('affiliateId')->from('affiliates')->where('affdomain', $affdomain);
				if(!$this->runExistsResult()){
					break;
				}

			}

		}

		return $affdomain;
	}

	/**
	 * overview : get active affiliate
	 *
	 * @param $affiliateId
	 */
	public function active($affiliateId) {
		$data = array(
			'status' => self::OLD_STATUS_ACTIVE,
			'updatedOn'=>$this->utils->getNowForMysql(),
		);

		$this->db->select('prefix_of_player, affdomain')->from('affiliates')->where('affiliateId', $affiliateId);

		$row=$this->runOneRowArray();
		$prefix_of_player=$row['prefix_of_player'];
		$affdomain=$row['affdomain'];

		// $prefix_of_player=$this->runOneRowOneField('prefix_of_player');
		if(empty($prefix_of_player)){
			if(!empty($this->utils->getConfig('main_prefix_of_player'))){
				$prefix_of_player=$this->generateRandomPrefix();
				if(empty($prefix_of_player)){
					$prefix_of_player=$this->utils->getConfig('main_prefix_of_player');
				}

				if(!empty($prefix_of_player)){
					$data['prefix_of_player']=$prefix_of_player;
				}
			}
		}

		if(empty($affdomain)){
			$auto_generate_domain=$this->utils->getConfig('auto_generate_domain');
			if($auto_generate_domain){
				$affdomain=$this->generateAffdomain();

				if(!empty($affdomain)){
					$data['affdomain']=$affdomain;

					$arr=explode('.', $affdomain);
					if(count($arr)>0){
						//first sub domain
						$sub_domain=$arr[0];
						//update tracking code
						$data['trackingCode']=$sub_domain;
					}
				}

			}
		}

		$this->db->where('affiliateId', $affiliateId)->set($data);
		return $this->runAnyUpdate('affiliates');
	}

	/**
	 * overview : get inactive affiliate
	 *
	 * @param $affiliateId
	 */
	public function inactive($affiliateId) {
		$data = array(
			'status' => self::OLD_STATUS_INACTIVE,
			'updatedOn'=>$this->utils->getNowForMysql(),
		);
		$this->db->where('affiliateId', $affiliateId);
		return $this->db->update('affiliates', $data);
	}

	/**
	 * generate prefix
	 *
	 * @return string prefix
	 */
	public function generateRandomPrefix(){
		$first_char=['a','b','c','d','e','f','g','h','j','k','m','n','p','q','r','s','t','u','v','w','x','y','z'];
		$second_char=['1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','g','h','j','k','m','n','p','q','r','s','t','u','v','w','x','y','z'];
		$first_index=0;
		$second_index=0;

		//load all
		$this->db->select('prefix_of_player')->from('affiliates')->where('prefix_of_player is not null',null,false);
		$rows=$this->runMultipleRowArray();
		$existsPrefixArr=[];
		foreach ($rows as $row) {
			if(!empty($row['prefix_of_player'])){
				$existsPrefixArr[]=$row['prefix_of_player'];
			}
		}

		$prefixOfPlayer=null;
		while($first_index<count($first_char)){
			//build one
			$prefix=$first_char[$first_index].$second_char[$second_index];

			if(in_array($prefix, $existsPrefixArr)){
				$second_index++;
				if($second_index>=count($second_char)){
					//move next
					$first_index++;
					$second_index=0;
				}
				continue;
			}

			$this->db->select('affiliateId')->from('affiliates')->where('prefix_of_player', $prefix);
			if(!$this->runExistsResult()){
				$prefixOfPlayer=$prefix;
				break;
			}

			$second_index++;
			if($second_index>=count($second_char)){
				//move next
				$first_index++;
				$second_index=0;
			}

		}

		return $prefixOfPlayer;

	}


	public function getAffiliateRequestCount($type, $dateFrom, $dateTo) {

		switch ($type) {
			case 'request':
				$status = self::STATUS_WITHDRAW_REQUEST;
			break;
			case 'approved':
				$status = self::STATUS_WITHDRAW_APPROVED;
			break;
			case 'declined':
				$status = self::STATUS_WITHDRAW_DECLINED;
			break;
			default:
			     $status = self::STATUS_WITHDRAW_REQUEST;
		}

		$sql = "SELECT COUNT(*) AS count_ FROM affiliatepaymenthistory WHERE STATUS = ? AND  createdOn >= ? AND createdOn <= ? " ;

        $count = 0 ;

		$query =  $this->db->query($sql,array($status,$dateFrom,$dateTo));
		$row = $query->row_array();

		if (isset($row) && !empty($row)){
	    	$count = $row['count_'];
		}

		return $count;

	}

	/**
	 * overview : get affiliate notes
	 *
	 * @param  int	$affiliate_id
	 * @return bool
	 */
	public function getAffiliateNotes($affiliate_id) {

		$query = $this->db->query("SELECT pn.*, au.username FROM affiliatenotes as pn LEFT JOIN adminusers AS au ON pn.userId = au.userId WHERE pn.affiliateId = ?", $affiliate_id);

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

		/**
	 * @param  $affiliateId
	 * @param  $userId
	 * @param  $notes
	 * @return array
	 */
	public function addAffiliateNote($affiliateId, $userId, $notes) {
		// return $this->db->insert('playernotes', $data);
		$data=[
    		'affiliateId' => $affiliateId,
    		'userId' => $userId,
    		'notes' => $notes,
    		'createdOn' => $this->utils->getNowForMysql(),
		];
		return $this->insertData('affiliatenotes', $data);

	}

    /**
     * @param  $affiliateId
     * @return array
    */
    public function subaffiliatesreport($affiliateId) {
        $affiliates = $this->affiliatemodel->getAllSubAffiliates($this->affiliatemodel->includeAllDownlineAffiliateIds($affiliateId));
        $return_values = array();

        if (sizeof($affiliates) > 0) {
            $current_month = date("m");
            $current_year = date("Y");

            foreach ($affiliates as $affiliate) {
                $playerRecords = $this->getAllPlayersUnderAffiliate($affiliate['affiliateId'], NULL, NULL);
                $depositedPlayer = 0;
                $totalNetReveneu = 0;
                $totalPlayerCount = count($playerRecords);

                foreach ($playerRecords as $playerRecord) {
                    // $totalNetReveneu += $playerRecord['totalDepositAmount'];

                    if ($playerRecord['totalDepositAmount'] != 0) {
                        $depositedPlayer += 1;
                    }
                }

                $totalNetReveneu = $this->getMonthlyReveneu($affiliate['affiliateId'], $current_year, $current_month);

                $return_values[$affiliate['affiliateId']]['username'] = $affiliate['username'];
                $return_values[$affiliate['affiliateId']]['totalPlayerCount'] = $totalPlayerCount;
                $return_values[$affiliate['affiliateId']]['depositedPlayer'] = $depositedPlayer;
                $return_values[$affiliate['affiliateId']]['totalNetReveneu'] = $totalNetReveneu;
            }
        }

        return $return_values;
    }

    /**
     * @param $affiliateId
     * @param $currentYear
     * @param $currentMonth
     * @return first row only of records
    */
    public function getMonthlyReveneu($affiliateId, $currentYear, $currentMonth) {
        $year_month = $currentYear . $currentMonth;

        $this->db->select('net_revenue');
        $this->db->where('affiliate_id', $affiliateId);
        $this->db->where('year_month', $year_month);
        $result = $this->db->get('aff_monthly_earnings');
        if ($result->num_rows == 0) {
            return 0;
        } elseif ($result->num_rows == 1) {
            return $result->row('net_revenue');
        } elseif ($result->num_rows > 1) {
            return 0;
        }
    }

	/**
	 * match domain , use wild char `*`
	 * @param  string $affdomain
	 * @return tracking code or false
	 */
	public function getTrackingCodeByMatchAffDomain($affdomain) {

		//get all domain
		$domains=$this->getDomainListForAff();

		$this->utils->debug_log('domains', $domains);

		//compare domain
		if(in_array(strtolower($affdomain), array_map('strtolower', array_keys($domains)))) {
			//direct match
			return $domains[$affdomain];
		}else{
			foreach ($domains as $domainSetting => $code) {
				//compare with *
				if(strpos(strtolower($domainSetting), '*')!==FALSE &&
					fnmatch(strtolower($domainSetting), strtolower($affdomain), FNM_CASEFOLD)){
					return $code;
				}
			}
		}

		// $this->db->select('trackingCode');
		// $query = $this->db->get_where('affiliates', array('affdomain' => $affdomain), 1);
		// $res = $query->row_array();
		// if (isset($res['trackingCode'])) {
		// 	return $res['trackingCode'];
		// } else {
		// 	//try search aff_tracking_link
		// 	$this->db->select('aff_id')->from('aff_tracking_link')->where('tracking_domain', $affdomain)->where('tracking_type', self::TRACKING_TYPE_DOMAIN);
		// 	$aff_id = $this->runOneRowOneField('aff_id');
		// 	if ($aff_id) {
		// 		$this->db->select('trackingCode')->from('affiliates')->where('affiliateId', $aff_id);
		// 		$trackingCode = $this->runOneRowOneField('trackingCode');
		// 		return $trackingCode;
		// 	}
		// }

		return false;
	}

	/**
	 *
	 * @return map domain => code
	 */
	public function getDomainListForAff(){
		$domains=[];
		$this->db->select('aff_tracking_link.tracking_domain, affiliates.trackingCode ')->from('aff_tracking_link')
			->join('affiliates', 'affiliates.affiliateId=aff_tracking_link.aff_id')
		    ->where('tracking_type', self::TRACKING_TYPE_DOMAIN)
			->where('aff_tracking_link.deleted_at is null', null, false);
//		$this->ignoreDeleted('deleted_at');

		$rows = $this->runMultipleRowArray();
		if(!empty($rows)){
			foreach ($rows as $row) {
				$domains[$row['tracking_domain']]=$row['trackingCode'];
			}
		}

		$this->db->select('affdomain, trackingCode')->from('affiliates')
		  ->where('affdomain is not null and affdomain!=""', null, false);

		$rows = $this->runMultipleRowArray();
		if(!empty($rows)){
			foreach ($rows as $row) {
				$domains[$row['affdomain']]=$row['trackingCode'];
			}
		}

		return $domains;
	}

	/**
	 * @param  string $affiliateId
	 * @return array
	 */
	// public function getAffiliateRegisteredPLayer($affiliateId,$request,$enable_date){
	// 	$array = array('affiliateId' => $affiliateId, 'registered_by' => "website");
	// 	$date_params = $request['extra_search'];
	// 	if(!$enable_date == null){
	// 		foreach ($date_params as $date_param) {
	// 			if($date_param['name']  == 'by_date_from'){
	// 				$array['createdOn >='] = $date_param['value'];
	// 			}
	// 			if($date_param['name']  == 'by_date_to'){
	// 				$array['createdOn <='] = $date_param['value'];
	// 			}
	// 		}
	// 	}

	// 	$data = $this->db->select('playerId')->from('player')
	// 	    ->where($array)
	// 	    	->or_where('registered_by','mobile');
	// 	return $this->runMultipleRowArray();
	// }


	public function getAffiliateRegisteredPLayer($affiliateId,$request,$enable_date){
		$array = array('affiliateId' => $affiliateId, 'registered_by' => "website");
		$date_params = $request['extra_search'];
		if(!$enable_date == null){
			foreach ($date_params as $date_param) {
				if($date_param['name']  == 'by_date_from'){
					$array['createdOn >='] = $date_param['value'];
				}
				if($date_param['name']  == 'by_date_to'){
					$array['createdOn <='] = $date_param['value'];
				}
			}
		}

		$data = $this->db->select('playerId')->from('player')
		    ->where($array)
		    	->or_where('registered_by','mobile');
		return $this->runMultipleRowArray();
	}

	/**
	 * @param  string $affiliateId
	 * @return array
	 */
	public function getAffiliateDepositedPLayer($affiliateId, $date_from = null, $date_to = null){
		$players = 	$this->affiliatemodel->getAllPlayersUnderAffiliateId($affiliateId);
		$this->db->distinct('to_id');
		$this->db->select('to_id')->from('transactions');
		//$this->db->where('to_id', $affiliateId);
		$this->db->where('transaction_type', 1);
		if (!empty($date_from) && !empty($date_to)) {
			$this->db->where('created_at >=', $date_from)->where('created_at <=', $date_to);
		}
		$depositors = $this->db->get();
		$result = [];
		foreach ($depositors->result_array() as $depositor) {
			if(in_array($depositor['to_id'], $players)){
				$result[] = $depositor['to_id'];
			}
		}

		return $result;
	}

	public function updateAffSettings($affId, $affSettings){

		$this->db->where('affiliateId', $affId)->where('optionType', self::AFF_TERMS_ALL_SETTINGS)
			->set('optionValue', $this->utils->encodeJson($affSettings));
		return $this->runAnyUpdate('affiliate_terms');

	}

	public function reset_game_shares(){

		$affiliates_settings 	= $this->getAllAffiliateTerm();
		$this->utils->debug_log('get it', count($affiliates_settings));
		if(!empty($affiliates_settings)){

			foreach ($affiliates_settings as $aff_id=>$aff_setting) {

				// $this->utils->debug_log('next aff id', $aff_id);

				$platform_shares=$aff_setting['platform_shares'];
				if(!empty($platform_shares)){
					$should_reset=true;

					foreach ($platform_shares as $game_platform => $share) {
						if(!empty($game_platform) && $share>0){
							//any >0
							$should_reset=false;
						}
					}
					if($should_reset){
						//set empty then save back
						$aff_setting['platform_shares']=[];
						if($aff_id==-1){
							//it's default
							//save default
							$success = $this->operatorglobalsettings->putSettingJson('affiliate_common_settings',
								$aff_setting, 'template');

						}else{
							$success=$this->updateAffSettings($aff_id, $aff_setting);
						}

						$this->utils->debug_log('reset it', $aff_id, $aff_setting);
					}else{
						// $this->utils->debug_log('ignore it', $aff_id);
					}
				}
			}
		}

	}

	public function getAffiliateMap() {
		$this->db->from("affiliates");
		$rows=$this->runMultipleRowArray();

		$affMap=[];
		foreach ($rows as $row) {
			$affMap[$row['username']]=$row['affiliateId'];
		}

		return $affMap;
	}


	/**
     *  get all sub affiliiates of given affiliate
     *
     *  @param  int parent_id
     *  @return ids for sub affiliates
     */
	public function get_sub_affiliate_ids_by_parent_id($parent_id, $start_date = null, $end_date = null) {
	  	if ($start_date) {
	  		$this->db->where('createdOn >=', $start_date);
	  	}
	  	if ($end_date) {
	  		$this->db->where('createdOn <=', $end_date);
	  	}
	  	if (is_array($parent_id)) {
	  		$this->db->where_in('parentId', $parent_id);
	  	} else {
	  		$this->db->where('parentId', $parent_id);
	  	}

	  	$result = $this->db->get('affiliates');
	  	$sub_id = array();
	  	if ($result->num_rows() > 0) {
	  		foreach ($result->result() as $r) {
	  			array_push($sub_id, $r->affiliateId);
	  		}
	  	}
	  	return $sub_id;
	}

	public function is_upline($affiliate_id, $parent_id) {

	  	$this->db->select('affiliateId');
	  	$this->db->select('parentId');
	  	$this->db->from('affiliates');
	  	$query = $this->db->get();

	  	$affiliates = array_column($query->result_array(), null, 'affiliateId');

	  	do {

	  		if (isset($affiliates[$affiliate_id])) {
	  			$affiliate_id = $affiliates[$affiliate_id]['parentId'];
	  		} else return FALSE;

	  		if ($affiliate_id == $parent_id) {
	  			return TRUE;
	  		}

	  	} while (TRUE);
	}

	public function getUsernames($affIdArr) {

		if(!empty($affIdArr)){

			$this->db->from('affiliates')->where_in('affiliateId', $affIdArr);

			$rows=$this->runMultipleRowArray();
			$usernames=[];

			if(!empty($rows)){
				foreach ($rows as $row) {

					$usernames[]=$row['username'];

				}
			}

			return $usernames;
		}

		return null;
	}

	public function getReadonlyAccountById($id) {
		$this->db->from('affiliate_read_only_account')->where('id', $id);
		return $this->runOneRowArray();
	}

	public function getReadonlyAccounts($affiliate_id) {
		$this->db->from('affiliate_read_only_account')->where('affiliate_id', $affiliate_id);
		return $this->runMultipleRowArray();
	}

	public function editAffiliate($data, $affiliateId) {
		$this->db->set($data);
		$this->db->where('affiliateId', $affiliateId);
        $this->db->update($this->tableName);
	}

	/**
	 * get affiliate by username
	 * @param  string $affiliate_username
	 * @return array
	 */
	public function getAffiliateInfoByUsername($affiliate_username) {
		return $this->getOneRowArrayByField('affiliates', 'username', $affiliate_username);
	}

	public function getAffiliateInfoByReadonlyUsername($readonly_username) {
		$this->db->select('affiliates.*, affiliate_read_only_account.username as readonly_username')->from('affiliates')
		    ->join('affiliate_read_only_account', 'affiliate_read_only_account.affiliate_id=affiliates.affiliateId')
		    ->where('affiliate_read_only_account.username', $readonly_username);
		return $this->runOneRowArray();
	}

    public function getAffCommissionTierSettings($affiliate_id = null){
        $this->db->from('affiliate_comm_tier_settings')
            ->where('deleted_at IS NULL');
        if(!empty($affiliate_id)){
            $this->db->where('affiliate_id', $affiliate_id);
        }else{
            $this->db->where('affiliate_id IS NULL');
        }
        return $this->runMultipleRowArray();
    }

    public function addEditAffCommTierSettings($data, $id = null){
        $data = array_merge($data, array(
            'updated_at' => date("Y-m-d H:i:s")
        ));
        if(!empty($id)){
            $this->db->set($data);
            $this->db->where('id', $id);
            $this->db->update('affiliate_comm_tier_settings');
        }else{
            $data = array_merge($data, array(
                'created_at' => date("Y-m-d H:i:s")
            ));
            $this->insertData('affiliate_comm_tier_settings', $data);
        }

        return $this->db->trans_status();
    }

    public function deleteTierSettings($id){
        $data = array('deleted_at' => date("Y-m-d H:i:s"));
        $this->db->set($data)
            ->where('id', $id)
            ->update('affiliate_comm_tier_settings');

        return $this->db->trans_status();
    }

    public function getPreviousNegativeNetRevenue($affiliate_id, $year_month){
        $this->db->select('negative_net_revenue');
        $this->db->where('affiliate_id', $affiliate_id);
        $this->db->where('year_month', $year_month);
        $result = $this->db->get('aff_monthly_earnings');
        if ($result->num_rows == 0) {
            return 0;
        } else{
            return $result->row('negative_net_revenue');
        }
    }

    /**
     * Date source for comapi_core_aff::aff_reportSubAffs()
     * Converted from report_module_aff::get_subaffiliate_reports()
     * OGP-17088
     * @param	int			$aff_id		== affiliate.affiliateId
     * @param	datetime	$date_from	Start date of query
     * @param	datetime	$date_to	End date of query
     * @param	int			$offset		Offset for paging
     * @param	int			$limit		Limit for paging
     * @return	array 		array of [ total_row_count:int , rows:array ]
     */
    public function get_subaffiliate_reports($aff_id, $date_from = null, $date_to = null, $offset = null, $limit = null) {
    	$tgp_date_from	= empty($date_from)	? null : date('YmdH', strtotime($date_from));
    	$tgp_date_to	= empty($date_to)	? null : date('YmdH', strtotime($date_to));

    	$sub_aff_ids = $this->affiliatemodel->includeAllDownlineAffiliateIds($aff_id);
    	$sub_aff_ids = array_diff($sub_aff_ids, [ $aff_id ]);

    	$total_row_count = count($sub_aff_ids);
    	$sub_aff_ids_clause = implode(',', $sub_aff_ids);

    	if ($total_row_count == 0) {
    		return [ 'total_row_count' => $total_row_count, 'rows' => [] ];
    	}

    	if (empty($limit)) {
    		$limit = 50;
    	}

    	$tgp_date_from	= null;
    	$tgp_date_to	= null;
    	$game_table = 'total_player_game_hour';
    	if (!empty($date_from) && !empty($date_to)) {
    		$tgp_date_from	= date('YmdH', strtotime($date_from));
    		$tgp_date_to	= date('YmdH', strtotime($date_to));
    		if (strtotime($date_to) - strtotime($date_from) > 3600 * 24) {
    			$game_table = 'total_player_game_day';
    		}
    	}

    	$this->db
    		->from("{$this->tableName} AS A")
    		->join('player AS P', 'P.affiliateId = A.affiliateId', 'left')
    		->select([
    			'A.username' ,
    			'COUNT(distinct P.playerId) AS total_players' ,
    			'COUNT(DISTINCT IF(T.transaction_type=1, P.playerId, NULL)) AS deposited_players' ,
				'SUM(IF(T.transaction_type=1, T.amount, 0)) AS total_deposit',
				'SUM(IF(T.transaction_type=2, T.amount, 0)) AS total_withdrawal',
				'SUM(CASE T.transaction_type
					WHEN 9 THEN T.amount WHEN 14 THEN T.amount WHEN 15 THEN T.amount
					WHEN 10 THEN -T.amount
					ELSE 0 END) AS total_bonus' ,
				'SUM(CASE T.transaction_type
					WHEN 13 THEN T.amount WHEN 30 THEN T.amount
					ELSE 0 END) AS total_cashback' ,
				'TGP.total_loss AS total_loss' ,
				'TGP.total_wins AS total_wins'
    		])
    		->where("A.affiliateId IN ( $sub_aff_ids_clause )")
    		->group_by('A.affiliateId')
    		->order_by('A.username', 'asc')
    		->limit($limit, $offset)
    	;

    	if (empty($tgp_date_from) || empty($tgp_date_to)) {
    		$this->db
	    		->join('transactions AS T', "T.status = 1 AND T.to_Type = 2 AND T.to_id = P.playerId", 'left')
	    		->join("(
	    			SELECT player_id, SUM(loss_amount) AS total_loss, SUM(win_amount) AS total_wins
	    			FROM total_player_game_hour
	    			GROUP BY player_id
	    		) AS TGP", 'TGP.player_id = P.playerId', 'left')
	    	;
    	}
    	else {
    		$this->db
	    		->join('transactions AS T', "T.status = 1 AND T.to_Type = 2 AND T.to_id = P.playerId AND T.created_at BETWEEN '{$date_from}' AND '{$date_to}'", 'left')
	    		->join("(
	    			SELECT player_id, SUM(loss_amount) AS total_loss, SUM(win_amount) AS total_wins
	    			FROM total_player_game_hour
	    			WHERE date_hour BETWEEN '{$tgp_date_from}' AND '{$tgp_date_to}'
	    			GROUP BY player_id
	    		) AS TGP", 'TGP.player_id = P.playerId', 'left')
	    	;
    	}

    	$rows = $this->runMultipleRowArray();

    	$this->utils->printLastSQL();

    	foreach ($rows as & $row) {
    		foreach ($row as $key => & $field) {
    			if ($key == 'username') { continue; }
    			$field = round(floatval($field), 2);
    		}
    	}

    	return [ 'total_row_count' => $total_row_count, 'rows' => $rows ];

    } // end function get_subaffiliate_reports()

}
