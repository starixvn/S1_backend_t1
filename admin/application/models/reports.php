<?php

class Reports extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	/**
	 * Will save to cronlogs
	 *
	 * @return  array
	 */
	function saveToCronLogs($data) {
		$this->db->insert('cronlogs', $data);
	}

	function getAllLogs($limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$query = $this->db->query("SELECT * FROM logs ORDER BY logId DESC $limit $offset");

		$result = $query->result_array();
		if (!$result) {
			return false;
		} else {
			return $result;
		}
	}

	function getDistinct($column) {
		$this->db->distinct();
		$this->db->select($column);
		$this->db->order_by($column);
		return $this->db->get('logs')->result_array();
	}

	function recordAction($data) {
		if ($data['status'] === 0) {
			$data['status'] = '0';
		}
		
		if (isset($data['params']) || empty($data['params'])) {
			unset($data['params']);
		}

		$this->db->insert('logs', $data);
	}

	/**
	 * Will get player report
	 *
	 * @param   sort_by array
	 * @param   limit int
	 * @param   offset int
	 * @return  array
	 */
	public function getPlayersReport($sortBy, $limit, $offset = 0) {

		$this->db->select('player.playerId,player.username,player.email,playerdetails.registrationIp,player_runtime.lastLoginIp,
			player_runtime.lastLoginTime,player_runtime.lastLogoutTime,player.createdOn,player.blocked,
			playerdetails.gender,
			playeraccount.totalBalanceAmount,
			tag.tagName,
			playertag.taggerId,
			adminusers.username AS taggedBy,
			vipsettingcashbackrule.vipLevel,
			vipsettingcashbackrule.vipLevelName,
			vipsetting.groupName
			')
		->from('player')
		->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
		->join('playeraccount', 'playeraccount.playerId = player.playerId', 'left')
		->join('player_runtime', 'player_runtime.playerId = player.playerId', 'left')
		->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
		->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
		->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
		->join('playertag', 'playertag.playerId = player.playerId', 'left')
		->join('tag', 'tag.tagId = playertag.playerTagId', 'left')
		->join('adminusers', 'adminusers.userId = playertag.taggerId', 'left');

		if (isset($sortBy['orderByReport'])) {
			switch ($sortBy['orderByReport']) {
				case 'userName':$this->db->order_by('player.username', $sortBy['sortBySortby']);
				break;
				case 'playerLevel':$this->db->order_by('vipsetting.groupName', $sortBy['orderByReport']);
				$this->db->order_by('vipsettingcashbackrule.vipLevel', $sortBy['sortBySortby']);
				break;
				case 'gender':$this->db->order_by('playerdetails.gender', $sortBy['sortBySortby']);
				break;
				case 'lastLoginTime':$this->db->order_by('player_runtime.lastLoginTime', $sortBy['sortBySortby']);
				break;
				case 'lastLogoutTime':$this->db->order_by('player_runtime.lastLogoutTime', $sortBy['sortBySortby']);
				break;
				case 'joinedOn':$this->db->order_by('player.createdOn', $sortBy['sortBySortby']);
				break;
				case 'balanceAmount':$this->db->order_by('playeraccount.totalBalanceAmount', $sortBy['sortBySortby']);
				break;
				case '':
				$this->db->order_by('player.username', $sortBy['sortBySortby']);
				break;
				break;
			}
		} else {
			$this->db->order_by('player.username', 'userName', $sortBy['sortBySortby']);
		}

		$this->db->where('playeraccount.type', 'wallet');
		//var_dump($sortBy['sortByGender']);exit();
		isset($sortBy['sortByUsername']) == TRUE ? $sortBy['sortByUsername'] == '' ? '' : $this->db->where('player.username', $sortBy['sortByUsername']) : '';
		isset($sortBy['sortByPlayerLevel']) == TRUE ? $sortBy['sortByPlayerLevel'] == '' ? '' : $this->db->where('vipsettingcashbackrule.vipsettingcashbackruleId', $sortBy['sortByPlayerLevel']) : '';
		isset($sortBy['sortByGender']) == TRUE ? $sortBy['sortByGender'] == '' ? '' : $this->db->where('playerdetails.gender', $sortBy['sortByGender']) : '';
		isset($sortBy['sortByTag']) == TRUE ? $sortBy['sortByTag'] == '' ? '' : $this->db->where('tag.tagId', $sortBy['sortByTag']) : '';
		isset($sortBy['sortByBalanceAmountLessThan']) == TRUE ? $sortBy['sortByBalanceAmountLessThan'] == '' ? '' : $this->db->where('playeraccount.totalBalanceAmount <=', $sortBy['sortByBalanceAmountLessThan']) : '';
		isset($sortBy['sortByBalanceAmountGreaterThan']) == TRUE ? $sortBy['sortByBalanceAmountGreaterThan'] == '' ? '' : $this->db->where('playeraccount.totalBalanceAmount >=', $sortBy['sortByBalanceAmountGreaterThan']) : '';
		isset($sortBy['sortBySignUpPeriodFrom']) == TRUE ? $sortBy['sortBySignUpPeriodFrom'] == '' ? '' : $this->db->where('player.createdOn >=', $sortBy['sortBySignUpPeriodFrom'] . ' 00:00:00') : '';
		isset($sortBy['sortBySignUpPeriodTo']) == TRUE ? $sortBy['sortBySignUpPeriodTo'] == '' ? '' : $this->db->where('player.createdOn <=', $sortBy['sortBySignUpPeriodTo'] . ' 23:59:59') : '';

		isset($sortBy['sortByLastLoginFrom']) == TRUE ? $sortBy['sortByLastLoginFrom'] == '' ? '' : $this->db->where('player_runtime.lastLoginTime >=', $sortBy['sortByLastLoginFrom'] . ' 00:00:00') : '';
		isset($sortBy['sortByLastLoginTo']) == TRUE ? $sortBy['sortByLastLoginTo'] == '' ? '' : $this->db->where('player_runtime.lastLoginTime <=', $sortBy['sortByLastLoginTo'] . ' 23:59:59') : '';

		isset($sortBy['sortByLastLogoutFrom']) == TRUE ? $sortBy['sortByLastLogoutFrom'] == '' ? '' : $this->db->where('player_runtime.lastLogoutTime >=', $sortBy['sortByLastLogoutFrom'] . ' 00:00:00') : '';
		isset($sortBy['sortByLastLogoutTo']) == TRUE ? $sortBy['sortByLastLogoutTo'] == '' ? '' : $this->db->where('player_runtime.lastLogoutTime <=', $sortBy['sortByLastLogoutTo'] . ' 23:59:59') : '';

		$this->db->limit($limit, $offset);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			// foreach ($query->result_array() as $row) {
			// 	$row['createdOn'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['createdOn']));
			// 	$row['lastLoginTime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['lastLoginTime']));
			// 	$row['lastLogoutTime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['lastLogoutTime']));
			// 	$data[] = $row;
			// }
			//var_dump($data);exit();
			// return $data;
			return $query->result_array();
		}
		return false;
	}

	/**
	 * get all player report list
	 *
	 * @return  array
	 */
	public function getPlayerReportListToExport() {
		$search = array(
			'period' => $this->session->userdata('period'),
			'start_date' => $this->session->userdata('start_date'),
			'end_date' => $this->session->userdata('end_date'),
			'depamt1' => $this->session->userdata('depamt1'),
			'depamt2' => $this->session->userdata('depamt2'),
			'widamt1' => $this->session->userdata('widamt1'),
			'widamt2' => $this->session->userdata('widamt2'),
			'status' => $this->session->userdata('report_player_status'),
			'playerlevel' => $this->session->userdata('report_player_level'),
			'username' => $this->session->userdata('report_player_username'),
			);

		$date = date('Y-m-d', strtotime('-1 day'));

		$segment = $this->getUriSegment(2);

		$where = null;

		if (!empty($search['start_date']) && ($segment != 'viewPlayerReportToday' && $segment != 'viewPlayerReportDaily' && $segment != 'viewPlayerReportWeekly')) {
			$where = "AND pr.date BETWEEN '" . $search['start_date'] . "' AND '" . $search['end_date'] . "'";
		} elseif ($search['period'] == '') {
			$where = "AND pr.date BETWEEN '" . $date . "' AND '" . $date . "'";
		}

		if ($search['status'] != null) {
			$where .= " AND p.status = '" . $search['status'] . "'";
		}

		if ($search['playerlevel'] != null) {
			$where .= " AND vipcbr.vipsettingcashbackruleId = '" . $search['playerlevel'] . "'";
		}

		if ($search['username'] != null) {
			$where .= " AND p.username LIKE '%" . $search['username'] . "%'";
		}

		if ($search['depamt1'] != null) {
			$where .= " AND pr.total_deposit <= '" . $search['depamt1'] . "'";
		}

		if ($search['depamt2'] != null) {
			$where .= " AND pr.total_deposit >= '" . $search['depamt2'] . "'";
		}

		if ($search['widamt1'] != null) {
			$where .= " AND pr.total_withdrawal <= '" . $search['widamt1'] . "'";
		}

		if ($search['widamt2'] != null) {
			$where .= " AND pr.total_withdrawal >= '" . $search['widamt2'] . "'";
		}

		$query = "SELECT pr.*, p.username, CONCAT(pd.firstname, ' ', pd.lastname) as realname,
		p.email, pd.registrationIp, pd.gender, p.registered_by,
		p.lastLoginIp, p.lastLoginTime, p.lastLogoutTime, p.createdOn,
		CONCAT(vipst.groupName, ' ', vipcbr.vipLevel) as playerlevel,
		pa.totalBalanceAmount as mainwallet, pa2.totalBalanceAmount as ptwallet, pa3.totalBalanceAmount as agwallet
		FROM player_report as pr
		LEFT JOIN player as p ON pr.playerId = p.playerId
		LEFT JOIN playerdetails as pd ON p.playerId = pd.playerId
		LEFT JOIN playerlevel as pl ON p.playerId = pl.playerId
		LEFT JOIN vipsettingcashbackrule as vipcbr ON vipcbr.vipsettingcashbackruleId = pl.playerGroupId
		LEFT JOIN vipsetting as vipst ON vipst.vipSettingId = vipcbr.vipSettingId
		LEFT JOIN playeraccount as pa ON p.playerId = pa.playerId
		LEFT OUTER JOIN playeraccount as pa2 ON p.playerId = pa2.playerId
		LEFT OUTER JOIN playeraccount as pa3 ON p.playerId = pa3.playerId
		WHERE pa.type = 'wallet'
		AND pa2.type = 'subwallet' AND pa2.typeId = '1'
		AND pa3.type = 'subwallet' AND pa3.typeId = '2'
		";

		$order = "ORDER BY pr.playerReportId DESC";

		$run = $this->db->query("$query $where $order");
		/*$result = $run->result_array();*/

		return $run;
	}

	/**
	 * Will get promotion report
	 *
	 * @param   sort_by array
	 * @param   limit int
	 * @param   offset int
	 * @return  array
	 */
	public function getPromotionReport($sortBy) {
		$this->db->select('playerpromo.promorulesId,
			playerpromo.dateApply,
			playerpromo.bonusAmount,
			playerpromo.promoStatus AS bonusStatus,
			promorules.promoName,
			player.playerId,player.username,
			vipsettingcashbackrule.vipLevel,
			vipsettingcashbackrule.vipLevelName,
			vipsetting.groupName
			')
		->from('playerpromo')
		->join('player', 'player.playerId = playerpromo.playerId', 'left')
		->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
		->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
		->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
		->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
		->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');

		if (isset($sortBy['orderByReport'])) {
			switch ($sortBy['orderByReport']) {
				case 'userName':$this->db->order_by('player.username', $sortBy['sortBySortby']);
				break;
				case 'playerLevel':$this->db->order_by('vipsetting.groupName', $sortBy['orderByReport']);
				$this->db->order_by('vipsettingcashbackrule.vipLevel', $sortBy['sortBySortby']);
				break;
				case 'promotionType':$this->db->order_by('playerpromo.promorulesId', $sortBy['sortBySortby']);
				break;
				case 'dateJoined':$this->db->order_by('playerpromo.dateApply', $sortBy['sortBySortby']);
				break;
				case 'playerPromoStatus':$this->db->order_by('playerpromo.status', $sortBy['sortBySortby']);
				break;
				case 'joinedOn':$this->db->order_by('player.createdOn', $sortBy['sortBySortby']);
				break;
				case 'balanceAmount':$this->db->order_by('playeraccount.totalBalanceAmount', $sortBy['sortBySortby']);
				break;
				case '':
				$this->db->order_by('player.username', $sortBy['sortBySortby']);
				break;
				break;
			}
		} else {
			$this->db->order_by('player.username', 'userName', $sortBy['sortBySortby']);
		}

		//var_dump($sortBy['sortByGender']);exit();
		isset($sortBy['sortByUsername']) == TRUE ? $sortBy['sortByUsername'] == '' ? '' : $this->db->where('player.username', $sortBy['sortByUsername']) : '';
		isset($sortBy['sortByPlayerLevel']) == TRUE ? $sortBy['sortByPlayerLevel'] == '' ? '' : $this->db->where('vipsettingcashbackrule.vipsettingcashbackruleId', $sortBy['sortByPlayerLevel']) : '';
		isset($sortBy['sortByPromotionType']) == TRUE ? $sortBy['sortByPromotionType'] == '' ? '' : $this->db->where('playerpromo.promorulesId', $sortBy['sortByPromotionType']) : '';

		if ($sortBy['sortByPromoStatus'] == 0) {
			isset($sortBy['sortByPromoStatus']) == TRUE ? $sortBy['sortByPromoStatus'] == '' ? '' : $this->db->where('playerpromo.promostatus', '0') : '';
		} else {
			isset($sortBy['sortByPromoStatus']) == TRUE ? $sortBy['sortByPromoStatus'] == '' ? '' : $this->db->where('playerpromo.promostatus >', '0') : '';
		}

		isset($sortBy['sortByBonusAmountLessThan']) == TRUE ? $sortBy['sortByBonusAmountLessThan'] == '' ? '' : $this->db->where('playerpromo.bonusAmount <=', $sortBy['sortByBonusAmountLessThan']) : '';
		isset($sortBy['sortByBonusAmountGreaterThan']) == TRUE ? $sortBy['sortByBonusAmountGreaterThan'] == '' ? '' : $this->db->where('playerpromo.bonusAmount >=', $sortBy['sortByBonusAmountGreaterThan']) : '';
		isset($sortBy['sortByBonusPeriodJoinedFrom']) == TRUE ? $sortBy['sortByBonusPeriodJoinedFrom'] == '' ? '' : $this->db->where('playerpromo.dateJoined >=', $sortBy['sortByBonusPeriodJoinedFrom'] . ' 00:00:00') : '';
		isset($sortBy['sortByBonusPeriodJoinedTo']) == TRUE ? $sortBy['sortByBonusPeriodJoinedTo'] == '' ? '' : $this->db->where('playerpromo.dateJoined <=', $sortBy['sortByBonusPeriodJoinedTo'] . ' 23:59:59') : '';

		// $this->db->limit($limit, $offset);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['dateApply'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dateApply']));
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get paymentt report
	 *
	 * @param   sort_by array
	 * @param   limit int
	 * @param   offset int
	 * @return  array
	 */
	public function getPaymentsReport($params) {

		$sql = <<<EOD
SELECT * FROM
(SELECT
	player.username `username`,
	CONCAT(player.groupName, ' ', player.levelName) `member_level`,
	playerlevel.playerGroupId `playerGroupId`,
	'withdrawal' as `transaction`,
	walletaccount.dwStatus as `status`,
	walletaccount.amount as `amount`,
	walletaccount.dwMethod as `payment_method`,
	adminusers.username as `processed_by`,
	walletaccount.processDatetime as `process_time`,
	walletaccount.notes as `notes`
FROM
	walletaccount
LEFT JOIN
	adminusers ON adminusers.userId = walletaccount.processedBy
LEFT JOIN
	playeraccount ON playeraccount.playerAccountId = walletaccount.playerAccountId
LEFT JOIN
	player ON player.playerId = playeraccount.playerId
LEFT JOIN
	playerlevel ON playerlevel.playerId = player.playerId

UNION

SELECT
	player.username `username`,
	CONCAT(player.groupName, ' ', player.levelName) `member_level`,
	playerlevel.playerGroupId `playerGroupId`,
	'deposit' as `transaction`,
	sale_orders.status as `status`,
	sale_orders.amount as `amount`,
	payment_account.flag as `payment_method`,
	adminusers.username as `processed_by`,
	sale_orders.process_time as `process_time`,
	sale_orders.notes as `notes`
FROM
	sale_orders
LEFT JOIN
	player ON player.playerId = sale_orders.player_id
LEFT JOIN
	payment_account ON payment_account.id = sale_orders.payment_account_id
LEFT JOIN
	adminusers ON adminusers.userId = sale_orders.processed_by
LEFT JOIN
	playerlevel ON playerlevel.playerId = player.playerId) a
EOD;

		$where = array();
		$values = array();

  		if ($params['dateRangeValueStart'] != null) {
  			$where[] = 'process_time >= ?';
  			$values[] = $params['dateRangeValueStart'];
  		}

  		if ($params['dateRangeValueEnd'] != null) {
  			$where[] = 'process_time <= ?';
  			$values[] = $params['dateRangeValueEnd'];

  		}

  		if ($params['paymentReportsortByUsername'] != null) {
  			$where[] = 'username = ?';
  			$values[] = $params['paymentReportsortByUsername'];
  		}

  		if ($params['paymentReportSortByPlayerLevel'] != null) {
  			$where[] = 'playerGroupId = ?';
  			$values[] = $params['paymentReportSortByPlayerLevel'];
  		}

  		if ($params['paymentReportSortByTransaction'] != null) {
  			$where[] = 'transaction = ?';
  			$values[] = $params['paymentReportSortByTransaction'];
  		}

  		if ($params['paymentReportSortByTransactionStatus'] != null) {
  			switch ($params['paymentReportSortByTransactionStatus']) {
  				case 'approved':
  					$where[] = "status IN(?,?,?)";
  					$values[] = 'approved';
  					$values[] = Sale_order::STATUS_BROWSER_CALLBACK;
  					$values[] = Sale_order::STATUS_SETTLED;
  					break;
  				case 'declined':
  					$where[] = "status IN(?,?,?)";
  					$values[] = 'declined';
  					$values[] = Sale_order::STATUS_DECLINED;
  					$values[] = Sale_order::STATUS_FAILED;
  					break;
  				case 'cancelled':
  					$where[] = "status IN(?,?)";
  					$values[] = 'cancelled';
  					$values[] = Sale_order::STATUS_CANCELLED;
  					break;
  				default:
  					break;
  			}
  		}

  		if ($params['paymentReportSortByDWAmountGreaterThan'] != null) {
  			$where[] = 'amount >= ?';
  			$values[] = $params['paymentReportSortByDWAmountGreaterThan'];
  		}

  		if ($params['paymentReportSortByDWAmountLessThan'] != null) {
  			$where[] = 'amount <= ?';
  			$values[] = $params['paymentReportSortByDWAmountLessThan'];
  		}

  		if ($where) {
  			$sql  .= ' WHERE ' . implode(' AND ', $where);
  		}

		$query = $this->db->query($sql, $values);
		return $query->result_array();
	}

	/*
	public function getPaymentsReport($sortBy) {
		$this->db->select('walletaccount.amount,walletaccount.processedBy,walletaccount.processDatetime,walletaccount.notes,walletaccount.dwMethod,
			walletaccount.dwStatus,walletaccount.dwDatetime,walletaccount.transactionType,
			playeraccount.totalBalanceAmount,
			player.username,playerdetails.gender,adminusers.username AS adminName,
			vipsettingcashbackrule.vipLevel,
			vipsettingcashbackrule.vipLevelName,
			vipsetting.groupName
			')
		->from('walletaccount')
		->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId', 'left')
		->join('player', 'player.playerId = playeraccount.playerId', 'left')
		->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
		->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
		->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
		->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
		->join('playertag', 'playertag.playerId = player.playerId', 'left')
		->join('adminusers', 'adminusers.userId = walletaccount.processedBy', 'left');

		if (isset($sortBy['paymentReportOrderByReport'])) {
			switch ($sortBy['paymentReportOrderByReport']) {
				case 'userName':$this->db->order_by('player.username', $sortBy['paymentReportSortBySortby']);
				break;
				case 'playerLevel':$this->db->order_by('vipsetting.groupName', $sortBy['orderByReport']);
				$this->db->order_by('vipsettingcashbackrule.vipLevel', $sortBy['sortBySortby']);
				break;
				case 'gender':$this->db->order_by('playerdetails.gender', $sortBy['paymentReportSortBySortby']);
				break;
				case 'depositAmount':$this->db->order_by('walletaccount.amount', $sortBy['paymentReportSortBySortby']);
				break;
				case 'processedOn':$this->db->order_by('walletaccount.processDatetime', $sortBy['paymentReportSortBySortby']);
				break;
				case '':
				$this->db->order_by('player.username', $sortBy['paymentReportSortBySortby']);
				break;
				break;
			}
		} else {
			$this->db->order_by('player.username', 'userName', $sortBy['paymentReportSortBySortby']);
		}

		// //var_dump($sortBy['sortByGender']);exit();
		isset($sortBy['paymentReportsortByUsername']) == TRUE ? $sortBy['paymentReportsortByUsername'] == '' ? '' : $this->db->where('player.username', $sortBy['paymentReportsortByUsername']) : '';
		isset($sortBy['paymentReportSortByPlayerLevel']) == TRUE ? $sortBy['paymentReportSortByPlayerLevel'] == '' ? '' : $this->db->where('vipsettingcashbackrule.vipsettingcashbackruleId', $sortBy['paymentReportSortByPlayerLevel']) : '';
		isset($sortBy['paymentReportSortByTransaction']) == TRUE ? $sortBy['paymentReportSortByTransaction'] == '' ? '' : $this->db->where('walletaccount.transactionType', $sortBy['paymentReportSortByTransaction']) : '';
		isset($sortBy['paymentReportSortByTransactionStatus']) == TRUE ? $sortBy['paymentReportSortByTransactionStatus'] == '' ? '' : $this->db->where('walletaccount.dwStatus', $sortBy['paymentReportSortByTransactionStatus']) : '';
		isset($sortBy['paymentReportSortByDWAmountLessThan']) == TRUE ? $sortBy['paymentReportSortByDWAmountLessThan'] == '' ? '' : $this->db->where('walletaccount.amount <=', $sortBy['paymentReportSortByDWAmountLessThan']) : '';
		isset($sortBy['paymentReportSortByDWAmountGreaterThan']) == TRUE ? $sortBy['paymentReportSortByDWAmountGreaterThan'] == '' ? '' : $this->db->where('walletaccount.amount >=', $sortBy['paymentReportSortByDWAmountGreaterThan']) : '';
		isset($sortBy['paymentReportSortByDateRangeValueStart']) == TRUE ? $sortBy['paymentReportSortByDateRangeValueStart'] == '' ? '' : $this->db->where('walletaccount.processDatetime >=', $sortBy['paymentReportSortByDateRangeValueStart'] . ' 00:00:00') : '';
		isset($sortBy['paymentReportSortByDateRangeValueEnd']) == TRUE ? $sortBy['paymentReportSortByDateRangeValueEnd'] == '' ? '' : $this->db->where('walletaccount.processDatetime <=', $sortBy['paymentReportSortByDateRangeValueEnd'] . ' 23:59:59') : '';
		//isset($sortBy['paymentReportSortByOnly1stDeposit']) == TRUE ? $sortBy['paymentReportSortByOnly1stDeposit'] == '' ? '' : $this->db->where('walletaccount.processDatetime <=',$sortBy['paymentReportSortByOnly1stDeposit'].' 23:59:59'): '';
		// $this->db->limit($limit, $offset);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['processDatetime'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['processDatetime']));
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}
	*/

	/**
	 * Get payment report list
	 *
	 * @return  $array
	 */
	public function getPaymentReportListToExport() {
		$this->db->select('walletaccount.amount,walletaccount.processedBy,walletaccount.processDatetime,walletaccount.notes,
			walletaccount.dwStatus,walletaccount.dwDatetime,walletaccount.transactionType,
			playeraccount.totalBalanceAmount,
			player.username,playerdetails.gender,adminusers.username AS adminName,
			vipsettingcashbackrule.vipLevel,
			vipsettingcashbackrule.vipLevelName,
			vipsetting.groupName,
			paymentmethod.paymentMethodName
			')
		->from('walletaccount')
		->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId', 'left')
		->join('player', 'player.playerId = playeraccount.playerId', 'left')
		->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
		->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
		->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
		->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
		->join('playertag', 'playertag.playerId = player.playerId', 'left')
		->join('adminusers', 'adminusers.userId = walletaccount.processedBy', 'left')
		->join('paymentmethod', 'paymentmethod.paymentMethodId = walletaccount.dwMethod', 'left');

		$query = $this->db->get();
		return $query;
	}

	/**
	 * Get promotion report list
	 *
	 * @return  $array
	 */
	public function getPromotionReportListToExport() {
		$this->db->select('playerpromo.playerpromoId ,
			playerpromo.dateApply,
			playerpromo.bonusAmount,
			promorules.promoName,
			player.username,
			vipsettingcashbackrule.vipLevel AS groupLevel,
			vipsettingcashbackrule.vipLevelName AS levelName,
			vipsetting.groupName
			')
		->from('playerpromo')
		->join('player', 'player.playerId = playerpromo.playerId', 'left')
		->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
		->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
		->join('promorules', 'promorules.promorulesId = playerpromo.playerpromoId', 'left')
		->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
		->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');

		$query = $this->db->get();
		return $query;
	}

	/**
	 * Get logs report list
	 *
	 * @return  $array
	 */
	public function getLogsReportListToExport() {
		$this->db->select('username,
			management,
			userRole,
			action,
			description,
			logDate,
			')
		->from('logs');
		$this->db->order_by('logDate', 'asc');
		$query = $this->db->get();
		return $query;
	}

	/**
	 * get all summary report list
	 *
	 * @return  array
	 */
	public function getSummaryReportListToExport() {
		$start_date = $this->session->userdata('start_date');
		$end_date = $this->session->userdata('end_date');

		$query = "SELECT * FROM summary_report";

		if (!empty($start_date)) {
			$query .= " WHERE createdOn BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
		}

		$run = $this->db->query("$query ORDER BY summaryId DESC");

		return $run;
	}

	/**
	 * get all income report list
	 *
	 * @return  array
	 */
	public function getIncomeReportListToExport() {
		$this->db->select('*')
		->from('income_report');
		$this->db->order_by('start_date', 'asc');
		$query = $this->db->get();
		return $query;
	}

	/**
	 * get all game report list
	 *
	 * @return  array
	 */
	public function getGameReportListToExport() {
		$search = array(
			'period' => $this->session->userdata('period'),
			'start_date' => $this->session->userdata('start_date'),
			'end_date' => $this->session->userdata('end_date'),
			'betamt1' => $this->session->userdata('betamt1'),
			'betamt2' => $this->session->userdata('betamt2'),
			'lossamt1' => $this->session->userdata('lossamt1'),
			'lossamt2' => $this->session->userdata('lossamt2'),
			'winamt1' => $this->session->userdata('winamt1'),
			'winamt2' => $this->session->userdata('winamt2'),
			'earnamt1' => $this->session->userdata('earnamt1'),
			'earnamt2' => $this->session->userdata('earnamt2'),
			'playerlevel' => $this->session->userdata('report_game_level'),
			'username' => $this->session->userdata('report_game_username'),
			);

		$date = date('Y-m-d', strtotime('-1 day'));

		$segment = $this->getUriSegment(2);

		$where = null;

		if (!empty($search['start_date']) && ($segment != 'viewGamesReportToday' && $segment != 'viewGamesReportDaily' && $segment != 'viewGamesReportWeekly')) {
			$where = "WHERE gr.date BETWEEN '" . $search['start_date'] . "' AND '" . $search['end_date'] . "'";
		} elseif ($search['period'] == '') {
			$where = "WHERE gr.date BETWEEN '" . $date . "' AND '" . $date . "'";
		}

		if ($search['betamt1'] != null) {
			$where .= " AND gr.total_bets <= '" . $search['betamt1'] . "'";
		}

		if ($search['betamt2'] != null) {
			$where .= " AND gr.total_bets >= '" . $search['betamt2'] . "'";
		}

		if ($search['lossamt1'] != null) {
			$where .= " AND gr.total_loss <= '" . $search['lossamt1'] . "'";
		}

		if ($search['lossamt2'] != null) {
			$where .= " AND gr.total_loss >= '" . $search['lossamt2'] . "'";
		}

		if ($search['winamt1'] != null) {
			$where .= " AND gr.total_wins <= '" . $search['winamt1'] . "'";
		}

		if ($search['winamt2'] != null) {
			$where .= " AND gr.total_wins >= '" . $search['winamt2'] . "'";
		}

		if ($search['earnamt1'] != null) {
			$where .= " AND gr.total_earned <= '" . $search['earnamt1'] . "'";
		}

		if ($search['earnamt2'] != null) {
			$where .= " AND gr.total_earned >= '" . $search['earnamt2'] . "'";
		}

		if ($search['playerlevel'] != null) {
			$where .= " AND vipcbr.vipsettingcashbackruleId = '" . $search['playerlevel'] . "'";
		}

		if ($search['username'] != null) {
			$where .= " AND p.username LIKE '%" . $search['username'] . "%'";
		}

		$query = "SELECT gr.*, p.username, CONCAT(pd.firstname, ' ', pd.lastname) as realname,
		p.lastLoginIp, p.lastLoginTime, p.lastLogoutTime,
		CONCAT(vipst.groupName, ' ', vipcbr.vipLevel) as playerlevel
		FROM games_report as gr
		LEFT JOIN player as p ON gr.playerId = p.playerId
		LEFT JOIN playerdetails as pd ON p.playerId = pd.playerId
		LEFT JOIN playerlevel as pl ON p.playerId = pl.playerId
		LEFT JOIN vipsettingcashbackrule as vipcbr ON vipcbr.vipsettingcashbackruleId = pl.playerGroupId
		LEFT JOIN vipsetting as vipst ON vipst.vipSettingId = vipcbr.vipSettingId
		";

		$order = "ORDER BY gr.gameReportId DESC";

		$run = $this->db->query("$query $where $order");

		return $run;
	}

	/**
	 * Get pt game api report list
	 *
	 * @return  $array
	 */
	public function getPTGameAPIReportListToExport() {
		$this->db->select('issuereportptapi.issueReportPtApiId,
			issuereportptapi.reportType,
			issuereportptapi.errorReturn,
			issuereportptapi.description,
			issuereportptapi.apiCallSyntax,
			issuereportptapi.errorTimeStamp,
			issuereportptapi.status,
			player.username
			')
		->from('issuereportptapi')
		->join('player', 'player.playerId = issuereportptapi.playerId');
		$this->db->order_by('issuereportptapi.errorTimeStamp', 'asc');
		$query = $this->db->get();
		return $query;
	}

	/**
	 * get new registered players
	 *
	 * @return  array
	 */
	public function getNewRegisteredPlayers($start_date, $end_date) {
		$query = $this->db->query("SELECT p.* FROM player as p LEFT JOIN playeraccount as pa ON p.playerId = pa.playerId WHERE pa.type = 'wallet' AND pa.typeOfPlayer = 'real' AND createdOn BETWEEN '" . $start_date . "' AND '" . $end_date . "'");

		return $query->result_array();
	}

	/**
	 * get registered players
	 *
	 * @return  array
	 */
	public function getRegisteredPlayers() {
		$query = $this->db->query("SELECT p.* FROM player as p LEFT JOIN playeraccount as pa ON p.playerId = pa.playerId WHERE pa.type = 'wallet' AND pa.typeOfPlayer = 'real'");

		return $query->result_array();
	}

	/**
	 * get deposit players
	 *
	 * @return  array
	 */
	public function getDepositPlayers($start_date, $end_date) {
		$query = $this->db->query("SELECT wa.* FROM walletaccount as wa WHERE wa.transactionType = 'deposit' AND wa.dwStatus = 'approved' AND thirdpartyPaymentMethod = '' AND processDatetime BETWEEN '" . $start_date . "' AND '" . $end_date . "'");

		return $query->result_array();
	}

	/**
	 * get third party deposit players
	 *
	 * @return  array
	 */
	public function getThirdPartyDepositPlayers($start_date, $end_date) {
		$query = $this->db->query("SELECT wa.* FROM walletaccount as wa WHERE wa.transactionType = 'deposit' AND wa.dwStatus = 'approved' AND thirdpartyPaymentMethod IN ('manual', 'auto') AND processDatetime BETWEEN '" . $start_date . "' AND '" . $end_date . "'");

		return $query->result_array();
	}

	/**
	 * get withdrawal players
	 *
	 * @return  array
	 */
	public function getWithdrawalPlayers($start_date, $end_date) {
		$query = $this->db->query("SELECT wa.* FROM walletaccount as wa WHERE wa.transactionType = 'withdrawal' AND wa.dwStatus = 'approved' AND processDatetime BETWEEN '" . $start_date . "' AND '" . $end_date . "'");

		return $query->result_array();
	}

	/**
	 * get first deposit players
	 *
	 * @return  array
	 */
	public function getFirstDepositPlayers($start_date, $end_date) {
		$query = $this->db->query("SELECT wa.* FROM walletaccount as wa LEFT JOIN playeraccount as pa ON wa.playerAccountId = pa.playerAccountId WHERE pa.type = 'wallet' AND pa.typeOfPlayer = 'real' AND wa.transactionType = 'deposit' AND wa.dwStatus = 'approved' AND wa.dwCount = '1' AND wa.processDatetime BETWEEN '" . $start_date . "' AND '" . $end_date . "'");

		return $query->result_array();
	}

	/**
	 * get first deposit amount
	 *
	 * @return  array
	 */
	public function getFirstDepositAmount($player_id, $start_date, $end_date) {
		$query = $this->db->query("SELECT SUM(amount) as amount FROM walletaccount as wa
			LEFT JOIN playeraccount as pa
			ON wa.playerAccountId = pa.playerAccountId
			WHERE wa.transactionType = 'deposit'
			AND wa.dwStatus = 'approved'
			AND wa.dwCount = '1'
			AND pa.playerId = '" . $player_id . "'
			AND processDatetime BETWEEN '" . $start_date . "' AND '" . $end_date . "'
			");

		$result = $query->row_array();

		if ($result['amount'] == null) {
			return 0;
		} else {
			return $result['amount'];
		}
	}

	/**
	 * get second deposit players
	 *
	 * @return  array
	 */
	public function getSecondDepositPlayers($start_date, $end_date) {
		$query = $this->db->query("SELECT wa.* FROM walletaccount as wa LEFT JOIN playeraccount as pa ON wa.playerAccountId = pa.playerAccountId WHERE pa.type = 'wallet' AND pa.typeOfPlayer = 'real' AND wa.transactionType = 'deposit' AND wa.dwStatus = 'approved' AND wa.dwCount = '2' AND processDatetime BETWEEN '" . $start_date . "' AND '" . $end_date . "'");

		return $query->result_array();
	}

	/**
	 * get second deposit amount
	 *
	 * @return  array
	 */
	public function getSecondDepositAmount($player_id, $start_date, $end_date) {
		$query = $this->db->query("SELECT SUM(amount) as amount FROM walletaccount as wa
			LEFT JOIN playeraccount as pa
			ON wa.playerAccountId = pa.playerAccountId
			WHERE wa.transactionType = 'deposit'
			AND wa.dwStatus = 'approved'
			AND wa.dwCount = '2'
			AND pa.playerId = '" . $player_id . "'
			AND processDatetime BETWEEN '" . $start_date . "' AND '" . $end_date . "'
			");

		$result = $query->row_array();

		if ($result['amount'] == null) {
			return 0;
		} else {
			return $result['amount'];
		}
	}

	/**
	 * get total deposit amount
	 *
	 * @return  array
	 */
	public function getTotalDeposit($start_date, $end_date) {
		$query = $this->db->query("SELECT SUM(amount) as amount FROM walletaccount as wa
			LEFT JOIN playeraccount as pa
			ON wa.playerAccountId = pa.playerAccountId
			WHERE pa.type = 'wallet'
			AND pa.typeOfPlayer = 'real'
			AND wa.transactionType = 'deposit'
			AND wa.dwStatus = 'approved'
			AND processDatetime BETWEEN '" . $start_date . "' AND '" . $end_date . "'
			");

		$result = $query->row_array();

		if ($result['amount'] == null) {
			return 0;
		} else {
			return $result['amount'];
		}
	}

	/**
	 * get total withdrawal amount
	 *
	 * @return  array
	 */
	public function getTotalWithdrawal($start_date, $end_date) {
		$query = $this->db->query("SELECT SUM(amount) as amount FROM walletaccount as wa
			LEFT JOIN playeraccount as pa
			ON wa.playerAccountId = pa.playerAccountId
			WHERE pa.type = 'wallet'
			AND pa.typeOfPlayer = 'real'
			AND wa.transactionType = 'withdrawal'
			AND wa.dwStatus = 'approved'
			AND processDatetime BETWEEN '" . $start_date . "' AND '" . $end_date . "'
			");

		$result = $query->row_array();

		if ($result['amount'] == null) {
			return 0;
		} else {
			return $result['amount'];
		}
	}

	/**
	 * get total pt gross income
	 *
	 * @return  array
	 */
	public function getPTGrossIncome($start_date, $end_date) {
		$query = $this->db->query("SELECT SUM(gar.wins) as total_wins, SUM(netloss) as total_loss FROM gameapirecord as gar
			LEFT JOIN player as p
			ON gar.playerName = p.username
			LEFT JOIN playeraccount as pa
			ON p.playerId = pa.playerId
			WHERE gar.apitype = 1
			AND gar.betTime BETWEEN '" . $start_date . "' AND '" . $end_date . "'
			AND pa.type = 'wallet'
			AND pa.typeOfPlayer = 'real'
			");

		$result = $query->row_array();

		if ($result != null) {
			$res = $result['total_wins'] - $result['total_loss'];
			return $res;
		} else {
			return 0;
		}
	}

	/**
	 * get total ag gross income
	 *
	 * @return  array
	 */
	public function getAGGrossIncome($start_date, $end_date) {
		$query = $this->db->query("SELECT (SELECT SUM(gar.netAmount) FROM gameapirecord as gar
			LEFT JOIN player as p ON gar.playerName = p.username
			LEFT JOIN playeraccount as pa
			ON p.playerId = pa.playerId
			WHERE gar.netAmount > 0
			AND pa.type = 'wallet'
			AND pa.typeOfPlayer = 'real'
			AND apitype = 2
			AND betTime BETWEEN '" . $start_date . "' AND '" . $end_date . "'
			AND dataType IN ('BR', 'EBR')) as total_wins,
		(SELECT SUM(gar.netAmount) FROM gameapirecord as gar
			LEFT JOIN player as p
			ON gar.playerName = p.username
			LEFT JOIN playeraccount as pa
			ON p.playerId = pa.playerId
			WHERE gar.netAmount < 0
			AND pa.type = 'wallet'
			AND pa.typeOfPlayer = 'real'
			AND apitype = 2
			AND betTime BETWEEN '" . $start_date . "' AND '" . $end_date . "'
			AND dataType IN ('BR', 'EBR')) as total_loss
		FROM gameapirecord
		WHERE apitype = 2
		AND betTime BETWEEN '" . $start_date . "' AND '" . $end_date . "'
		");

		$result = $query->row_array();

		if ($result != null) {
			$res = $result['total_wins'] - $result['total_loss'];
			return $res;
		} else {
			return 0;
		}
	}

	/**
	 * get total deposits
	 *
	 * @return  array
	 */
	public function getDepositAmount($player_id, $start_date, $end_date) {
		$query = $this->db->query("SELECT SUM(amount) as amount FROM walletaccount as wa
			LEFT JOIN playeraccount as pa
			ON wa.playerAccountId = pa.playerAccountId
			WHERE wa.transactionType = 'deposit'
			AND wa.dwStatus = 'approved'
			AND pa.playerId = '" . $player_id . "'
			AND processDatetime BETWEEN '" . $start_date . "' AND '" . $end_date . "'
			");

		$result = $query->row_array();

		if ($result['amount'] == null) {
			return 0;
		} else {
			return $result['amount'];
		}
	}

	/**
	 * get total withdrawals
	 *
	 * @return  array
	 */
	public function getWithdrawalAmount($player_id, $start_date, $end_date) {
		$query = $this->db->query("SELECT SUM(amount) as amount FROM walletaccount as wa
			LEFT JOIN playeraccount as pa
			ON wa.playerAccountId = pa.playerAccountId
			WHERE wa.transactionType = 'withdrawal'
			AND wa.dwStatus = 'approved'
			AND pa.playerId = '" . $player_id . "'
			AND processDatetime BETWEEN '" . $start_date . "' AND '" . $end_date . "'
			");

		$result = $query->row_array();

		if ($result['amount'] == null) {
			return 0;
		} else {
			return $result['amount'];
		}
	}

	/**
	 * get total third party deposits
	 *
	 * @return  array
	 */
	public function getThirdPartyDepositAmount($start_date, $end_date) {
		$query = $this->db->query("SELECT SUM(amount) as amount FROM walletaccount as wa WHERE wa.transactionType = 'deposit' AND wa.dwStatus = 'approved' AND thirdpartyPaymentMethod IN ('auto', 'manual') AND processDatetime BETWEEN '" . $start_date . "' AND '" . $end_date . "'");

		$result = $query->row_array();

		if ($result['amount'] == null) {
			return 0;
		} else {
			return $result['amount'];
		}
	}

	/**
	 * get total friend referral bonus
	 *
	 * @return  array
	 */
	public function getTotalFriendReferralBonus($start_date, $end_date) {
		$query = $this->db->query("SELECT SUM(pfr.amount) as amount FROM playerfriendreferraldetails as pfr
			LEFT JOIN playeraccount as pa
			ON pfr.referralId = pa.playerId
			WHERE pfr.transactionDatetime BETWEEN '" . $start_date . "' AND '" . $end_date . "'
			AND pa.type = 'wallet'
			AND pa.typeOfPlayer = 'real'
			");

		$result = $query->row_array();

		if ($result['amount'] == null) {
			return 0;
		} else {
			return $result['amount'];
		}
	}

	/**
	 * get total cashback bonus
	 *
	 * @return  array
	 */
	public function getTotalCashbackBonus($start_date, $end_date) {
		$query = $this->db->query("SELECT SUM(pc.amount) as amount FROM playercashback as pc
			LEFT JOIN playeraccount as pa
			ON pc.playerId = pa.playerId
			WHERE receivedOn BETWEEN '" . $start_date . "' AND '" . $end_date . "'
			AND pa.type = 'wallet'
			AND pa.typeOfPlayer = 'real'
			");

		$result = $query->row_array();

		if ($result['amount'] == null) {
			return 0;
		} else {
			return $result['amount'];
		}
	}

	/**
	 * get total promo bonus
	 *
	 * @return  array
	 */
	public function getTotalPromoBonus($start_date, $end_date) {
		$query = $this->db->query("SELECT SUM(pdp.bonusAmount) as amount FROM playerpromo as pdp
			LEFT JOIN playeraccount as pa
			ON pdp.playerId = pa.playerId
			WHERE transactionStatus = '1'
			AND approvedDate BETWEEN '" . $start_date . "' AND '" . $end_date . "'
			AND pa.type = 'wallet'
			AND pa.typeOfPlayer = 'real'
			");

		$result = $query->row_array();

		if ($result['amount'] == null) {
			return 0;
		} else {
			return $result['amount'];
		}
	}

	/**
	 * get total transaction fee
	 *
	 * @return  array
	 */
	public function getTotalTransactionFee($start_date, $end_date) {
		$query = $this->db->query("SELECT SUM(lbd.transactionFee) as total_transaction_fee
			FROM localbankdepositdetails as lbd
			LEFT JOIN walletaccount as wa
			ON lbd.walletAccountId = wa.walletAccountId
			LEFT JOIN playeraccount as pa
			ON pa.playerAccountId = wa.playerAccountId
			WHERE wa.processDatetime BETWEEN '" . $start_date . "' AND '" . $end_date . "'
			AND wa.transactionType = 'deposit'
			AND wa.dwStatus = 'approved'
			AND pa.typeOfPlayer = 'real'
			");

		$result = $query->row_array();

		if ($result['total_transaction_fee'] != null) {
			return $result['total_transaction_fee'];
		} else {
			return 0;
		}
	}

	/**
	 * get Online Players
	 *
	 * @return  array
	 */
	public function getOnlinePlayers($start_date, $end_date) {
		$query = $this->db->query("SELECT p.* FROM player as p WHERE lastLoginTime BETWEEN '" . $start_date . "' AND '" . $end_date . "'");

		return $query->result_array();
	}

	/**
	 * insert summary report
	 *
	 * @return  array
	 */
	public function insertSummaryReport($data) {
		$this->db->insert('summary_report', $data);
	}

	/**
	 * insert player report
	 *
	 * @return  array
	 */
	public function insertPlayerReport($data) {
		$this->db->insert('player_report', $data);
	}

	/**
	 * insert income report
	 *
	 * @return  array
	 */
	public function insertIncomeReport($data) {
		$this->db->insert('income_report', $data);
	}

	/**
	 * insert games report
	 *
	 * @return  array
	 */
	public function insertGamesReport($data) {
		$this->db->insert('games_report', $data);
	}

	/**
	 * get summary report
	 *
	 * @param  array
	 * @return  array
	 */
	public function getSummaryReport() {
		$data = array(
			'start_date' => $this->session->userdata('start_date'),
			'end_date' => $this->session->userdata('end_date'),
			'newRegisteredPlayer' => $this->session->userdata('new_registered_players'),
			'registeredPlayer' => $this->session->userdata('total_registered_players'),
			'firstDepositPlayer' => $this->session->userdata('first_dep_players'),
			'secondDepositPlayer' => $this->session->userdata('second_dep_players'),
			'totalDepositAmount' => $this->session->userdata('total_dep_amt'),
			'totalDepositAmount_range' => $this->session->userdata('total_dep_amt_range'),
			'totalWithdrawalAmount' => $this->session->userdata('total_wid_amt'),
			'totalWithdrawalAmount_range' => $this->session->userdata('total_wid_amt_range'),
			'ptGrossIncome' => $this->session->userdata('pt_gross_income'),
			'ptGrossIncome_range' => $this->session->userdata('pt_gross_income_range'),
			'agGrossIncome' => $this->session->userdata('ag_gross_income'),
			'agGrossIncome_range' => $this->session->userdata('ag_gross_income_range'),
			'totalGrossIncome' => $this->session->userdata('total_gross_income'),
			'totalGrossIncome_range' => $this->session->userdata('total_gross_income_range'),
			'totalBonus' => $this->session->userdata('bonus'),
			'totalBonus_range' => $this->session->userdata('bonus_range'),
			'cashbackBonus' => $this->session->userdata('cashback'),
			'cashbackBonus_range' => $this->session->userdata('cashback_range'),
			'netIncome' => $this->session->userdata('game_net_income'),
			'netIncome_range' => $this->session->userdata('game_net_income_range'),
			);

$search = null;

if (!empty($data)) {
	foreach ($data as $key => $value) {
		if ($key == 'start_date' && $value != '') {
			$search[$key] = "createdOn BETWEEN '" . $data['start_date'] . " 00:00:00' AND '" . $data['end_date'] . " 23:59:59'";
		} elseif ($key == 'end_date') {
			continue;
		} elseif ($value == 'on' && ($key == 'newRegisteredPlayer' || $key == 'registeredPlayer' || $key == 'firstDepositPlayer' || $key == 'secondDepositPlayer')) {
			$search[$key] = "$key != 0";
		} elseif (strpos($key, 'range') == false && $value != '') {
			if ($data[$key . '_range'] == 1) {
				$search[$key] = "$key >= " . $value;
			} else {
				$search[$key] = "$key <= " . $value;
			}

		}
	}
}

$query = "SELECT * FROM summary_report";

if (count($search) > 0) {
	$query .= " WHERE " . implode(' AND ', $search);
}

$run = $this->db->query("$query");

$result = $run->result_array();

if (!$result) {
	return false;
} else {
	return $result;
}
}

	/**
	 * get PT total bets
	 *
	 * @return  array
	 */
	public function getPTTotalBets($start_date, $end_date) {
		$query = $this->db->query("SELECT SUM(gar.bets) as bets FROM gameapirecord as gar WHERE gar.betTime BETWEEN '" . $start_date . "' AND '" . $end_date . "'");

		$result = $query->row_array();

		if ($result['bets'] == null) {
			return 0;
		} else {
			return $result['bets'];
		}
	}

	/**
	 * get PT total earn
	 *
	 * @return  array
	 */
	public function getPTTotalEarn($start_date, $end_date) {
		$query = $this->db->query("SELECT SUM(gar.netAmount) as earnings FROM gameapirecord as gar WHERE gar.betTime BETWEEN '" . $start_date . "' AND '" . $end_date . "'");

		$result = $query->row_array();

		if ($result['earnings'] == null) {
			return 0;
		} else {
			return $result['earnings'];
		}
	}

	/**
	 * get AG total bets
	 *
	 * @return  array
	 */
	public function getAGTotalBets($start_date, $end_date) {
		$query = $this->db->query("SELECT SUM(gar.betAmount) as bets FROM gameapirecord as gar WHERE gar.betTime BETWEEN '" . $start_date . "' AND '" . $end_date . "' AND dataType IN('BR', 'EBR')");

		$result = $query->row_array();

		if ($result['bets'] == null) {
			return 0;
		} else {
			return $result['bets'];
		}
	}

	/**
	 * get AG total earn
	 *
	 * @return  array
	 */
	public function getAGTotalEarn($start_date, $end_date) {
		$query = $this->db->query("SELECT SUM(gar.netAmount) as earnings FROM gameapirecord as gar WHERE gar.betTime BETWEEN '" . $start_date . "' AND '" . $end_date . "' AND dataType IN('BR', 'EBR')");

		$result = $query->row_array();

		if ($result['earnings'] == null) {
			return 0;
		} else {
			return $result['earnings'];
		}
	}

	/**
	 * get total registered players
	 *
	 * @return  array
	 */
	public function getTotalRegisteredPlayers() {
		$query = $this->db->query("SELECT COUNT(playerId) as registered_players FROM player WHERE verify != ''");

		$result = $query->row_array();

		if ($result['registered_players'] == null) {
			return 0;
		} else {
			return $result['registered_players'];
		}
	}

	/**
	 * get total mass players
	 *
	 * @return  array
	 */
	public function getTotalMassPlayers() {
		$query = $this->db->query("SELECT COUNT(playerId) as mass_players FROM player WHERE verify = ''");

		$result = $query->row_array();

		if ($result['mass_players'] == null) {
			return 0;
		} else {
			return $result['mass_players'];
		}
	}

	/**
	 * get total online players
	 *
	 * @return  array
	 */
	public function getTotalOnlinePlayers() {
		$query = $this->db->query("SELECT COUNT(playerId) as online_players FROM player WHERE online = '0'");

		$result = $query->row_array();

		if ($result['online_players'] == null) {
			return 0;
		} else {
			return $result['online_players'];
		}
	}

	/**
	 * get total deposit players
	 *
	 * @return  array
	 */
	public function getTotalDepositPlayers() {
		$query = $this->db->query("SELECT COUNT(walletAccountId) as deposit_players FROM walletaccount
			WHERE transactionType = 'deposit'
			AND dwStatus = 'approved'
			");

		$result = $query->row_array();

		if ($result['deposit_players'] == null) {
			return 0;
		} else {
			return $result['deposit_players'];
		}
	}

	/**
	 * get total deposit amount
	 *
	 * @return  array
	 */
	public function getTotalDepositAmount() {
		$query = $this->db->query("SELECT SUM(amount) as deposit_amount FROM walletaccount
			WHERE transactionType = 'deposit'
			AND dwStatus = 'approved'
			");

		$result = $query->row_array();

		if ($result['deposit_amount'] == null) {
			return 0;
		} else {
			return $result['deposit_amount'];
		}
	}

	/**
	 * get total withdrawal players
	 *
	 * @return  array
	 */
	public function getTotalWithdrawalPlayers() {
		$query = $this->db->query("SELECT COUNT(walletAccountId) as withdrawal_players FROM walletaccount
			WHERE transactionType = 'withdrawal'
			AND dwStatus = 'approved'
			");

		$result = $query->row_array();

		if ($result['withdrawal_players'] == null) {
			return 0;
		} else {
			return $result['withdrawal_players'];
		}
	}

	/**
	 * get total withdrawal amount
	 *
	 * @return  array
	 */
	public function getTotalWithdrawalAmount() {
		$query = $this->db->query("SELECT SUM(amount) as withdrawal_amount FROM walletaccount
			WHERE transactionType = 'withdrawal'
			AND dwStatus = 'approved'
			");

		$result = $query->row_array();

		if ($result['withdrawal_amount'] == null) {
			return 0;
		} else {
			return $result['withdrawal_amount'];
		}
	}

	/**
	 * get total bonus
	 *
	 * @return  array
	 */
	public function getTotalBonus() {
		$query1 = $this->db->query("SELECT SUM(amount) as friendreferral FROM playerfriendreferraldetails");
		$query2 = $this->db->query("SELECT SUM(amount) as cashback FROM playercashback ");
		$query3 = $this->db->query("SELECT SUM(bonusAmount) as promo FROM playerpromo WHERE transactionStatus = '1'");

		$friendreferral = $query1->row_array();
		$cashback = $query2->row_array();
		$promo = $query3->row_array();

		$total_bonus = $friendreferral['friendreferral'] + $cashback['cashback'] + $promo['promo'];

		if ($total_bonus == null) {
			return 0;
		} else {
			return $total_bonus;
		}
	}

	/**
	 * get total bets
	 *
	 * @return  array
	 */
	public function getTotalBets() {
		$query = $this->db->query("SELECT (SELECT SUM(gar.bets) FROM gameapirecord as gar WHERE gar.apitype = '1') + (SELECT SUM(gar.betAmount) FROM gameapirecord as gar WHERE gar.apitype = '2' AND dataType IN ('BR', 'EBR')) as total_bets
			FROM gameapirecord as gar
			LEFT JOIN player as p
			ON p.username = gar.playerName
			");

		$result = $query->row_array();

		if ($result['total_bets'] == null) {
			return 0;
		} else {
			return $result['total_bets'];
		}
	}

	/* Player Report */

	function getUriSegments() {
		return explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
	}

	function getUriSegment($n) {
		$segs = $this->getUriSegments();
		return count($segs) > 0 && count($segs) >= ($n - 1) ? $segs[$n] : '';
	}

	/**
	 * get player report
	 *
	 * @param   array
	 * @param   int
	 * @param   int
	 * @return  array
	 */
	public function getPlayerReport($start_date, $end_date) {
		$search = array(
			'period' => $this->session->userdata('period'),
			'start_date' => $this->session->userdata('start_date'),
			'end_date' => $this->session->userdata('end_date'),
			'depamt1' => $this->session->userdata('depamt1'),
			'depamt2' => $this->session->userdata('depamt2'),
			'widamt1' => $this->session->userdata('widamt1'),
			'widamt2' => $this->session->userdata('widamt2'),
			'status' => $this->session->userdata('report_player_status'),
			'playerlevel' => $this->session->userdata('report_player_level'),
			'username' => $this->session->userdata('report_player_username'),
			);

		$date = date('Y-m-d', strtotime('-1 day'));

		$segment = $this->getUriSegment(2);

		$where = null;

		if (!empty($search['start_date']) && ($segment != 'viewPlayerReportToday' && $segment != 'viewPlayerReportDaily' && $segment != 'viewPlayerReportWeekly')) {
			$where = "AND pr.date BETWEEN '" . $search['start_date'] . "' AND '" . $search['end_date'] . "'";
		} elseif (!empty($start_date)) {
			$where = "AND pr.date BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
		} elseif ($search['period'] == '') {
			$where = "AND pr.date BETWEEN '" . $date . "' AND '" . $date . "'";
		}

		/*if(!empty($search['start_date']) && $search['type_date'] == 'registration date') {
		$where .= " AND a.createdOn BETWEEN '" . $search['start_date'] . "' AND '" . $search['end_date'] . "'";
		} else if(!empty($start_date) && $search['type_date'] == 'login date') {
		$where .= " AND a.lastLogin BETWEEN '" . $search['start_date'] . "' AND '" . $search['end_date'] . "'";
	}*/

	if ($search['status'] != null) {
		$where .= " AND p.status = '" . $search['status'] . "'";
	}

	if ($search['playerlevel'] != null) {
		$where .= " AND vipcbr.vipsettingcashbackruleId = '" . $search['playerlevel'] . "'";
	}

	if ($search['username'] != null) {
		$where .= " AND p.username LIKE '%" . $search['username'] . "%'";
	}

	if ($search['depamt1'] != null) {
		$where .= " AND pr.total_deposit <= '" . $search['depamt1'] . "'";
	}

	if ($search['depamt2'] != null) {
		$where .= " AND pr.total_deposit >= '" . $search['depamt2'] . "'";
	}

	if ($search['widamt1'] != null) {
		$where .= " AND pr.total_withdrawal <= '" . $search['widamt1'] . "'";
	}

	if ($search['widamt2'] != null) {
		$where .= " AND pr.total_withdrawal >= '" . $search['widamt2'] . "'";
	}

	$query = "SELECT pr.*, p.username, CONCAT(pd.firstname, ' ', pd.lastname) as realname,
	p.email, pd.registrationIp, pd.gender, p.registered_by,
	p.lastLoginIp, p.lastLoginTime, p.lastLogoutTime, p.createdOn,
	CONCAT(vipst.groupName, ' ', vipcbr.vipLevel) as playerlevel,
	pa.totalBalanceAmount as mainwallet, pa2.totalBalanceAmount as ptwallet, pa3.totalBalanceAmount as agwallet
	FROM player_report as pr
	LEFT JOIN player as p ON pr.playerId = p.playerId
	LEFT JOIN playerdetails as pd ON p.playerId = pd.playerId
	LEFT JOIN playerlevel as pl ON p.playerId = pl.playerId
	LEFT JOIN vipsettingcashbackrule as vipcbr ON vipcbr.vipsettingcashbackruleId = pl.playerGroupId
	LEFT JOIN vipsetting as vipst ON vipst.vipSettingId = vipcbr.vipSettingId
	LEFT JOIN playeraccount as pa ON p.playerId = pa.playerId
	LEFT OUTER JOIN playeraccount as pa2 ON p.playerId = pa2.playerId
	LEFT OUTER JOIN playeraccount as pa3 ON p.playerId = pa3.playerId
	WHERE pa.type = 'wallet'
	AND pa2.type = 'subwallet' AND pa2.typeId = '1'
	AND pa3.type = 'subwallet' AND pa3.typeId = '2'
	";

	$order = "ORDER BY pr.playerReportId DESC";

	/*echo $query . " " . $where;*/

	$run = $this->db->query("$query $where $order");
	$result = $run->result_array();

		/*echo "<pre>";
		print_r($result);
		echo "</pre>";
		exit();*/

		return $result;
	}

	/**
	 * get player report
	 *
	 * @param   array
	 * @param   int
	 * @param   int
	 * @return  array
	 */
	public function getRegisteredPlayerToday($start_date, $end_date) {
		$where = "AND p.createdOn BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
		$where .= "AND pr.date BETWEEN '" . $start_date . "' AND '" . $end_date . "'";

		$query = "SELECT pr.*, p.username, CONCAT(pd.firstname, ' ', pd.lastname) as realname,
		p.email, pd.registrationIp, pd.gender, p.registered_by,
		p.lastLoginIp, p.lastLoginTime, p.lastLogoutTime, p.createdOn,
		CONCAT(vipst.groupName, ' ', vipcbr.vipLevel) as playerlevel,
		pa.totalBalanceAmount as mainwallet
		FROM player_report as pr
		LEFT JOIN player as p ON pr.playerId = p.playerId
		LEFT JOIN playerdetails as pd ON p.playerId = pd.playerId
		LEFT JOIN playerlevel as pl ON p.playerId = pl.playerId
		LEFT JOIN vipsettingcashbackrule as vipcbr ON vipcbr.vipsettingcashbackruleId = pl.playerGroupId
		LEFT JOIN vipsetting as vipst ON vipst.vipSettingId = vipcbr.vipSettingId
		LEFT JOIN playeraccount as pa ON p.playerId = pa.playerId
		WHERE pa.type = 'wallet'
		";

		$order = "ORDER BY pr.playerReportId DESC";

		$run = $this->db->query("$query $where $order");
		$query_result = $run->result_array();

		$result = array();

		foreach ($query_result as $key => $value) {
			$query = $this->db->query("SELECT * FROM playeraccount where playerId = '" . $value['playerId'] . "' and type = 'subwallet'");

			$res = $query->result_array();

			foreach ($res as $key => $val) {
				if ($val['typeId'] == '1') {
					$value['ptwallet'] = $val['totalBalanceAmount'];
				} else if ($val['typeId'] == '2') {
					$value['agwallet'] = $val['totalBalanceAmount'];
				}

			}

			array_push($result, $value);
		}

		return $result;
	}

	/* end of Player Report */

	/* Games Report */

	/**
	 * get games report
	 *
	 * @return  array
	 */
	// public function getGamesReport($start_date, $end_date) {
	// 	$search = array(
	// 		'period' => $this->session->userdata('period'),
	// 		'start_date' => $this->session->userdata('start_date'),
	// 		'end_date' => $this->session->userdata('end_date'),
	// 		'betamt1' => $this->session->userdata('betamt1'),
	// 		'betamt2' => $this->session->userdata('betamt2'),
	// 		'lossamt1' => $this->session->userdata('lossamt1'),
	// 		'lossamt2' => $this->session->userdata('lossamt2'),
	// 		'winamt1' => $this->session->userdata('winamt1'),
	// 		'winamt2' => $this->session->userdata('winamt2'),
	// 		'earnamt1' => $this->session->userdata('earnamt1'),
	// 		'earnamt2' => $this->session->userdata('earnamt2'),
	// 		'playerlevel' => $this->session->userdata('report_game_level'),
	// 		'username' => $this->session->userdata('report_game_username'),
	// 	);

	// 	$date = date('Y-m-d', strtotime('-1 day'));

	// 	$segment = $this->getUriSegment(2);

	// 	$where = null;

	// 	if (!empty($search['start_date']) && ($segment != 'viewGamesReportToday' && $segment != 'viewGamesReportDaily' && $segment != 'viewGamesReportWeekly')) {
	// 		$where = "WHERE gr.date BETWEEN '" . $search['start_date'] . "' AND '" . $search['end_date'] . "'";
	// 	} elseif (!empty($start_date)) {
	// 		$where = "WHERE gr.date BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
	// 	} elseif ($search['period'] == '') {
	// 		$where = "WHERE gr.date BETWEEN '" . $date . "' AND '" . $date . "'";
	// 	}

	// 	if ($search['betamt1'] != null) {
	// 		$where .= " AND gr.total_bets <= '" . $search['betamt1'] . "'";
	// 	}

	// 	if ($search['betamt2'] != null) {
	// 		$where .= " AND gr.total_bets >= '" . $search['betamt2'] . "'";
	// 	}

	// 	if ($search['lossamt1'] != null) {
	// 		$where .= " AND gr.total_loss <= '" . $search['lossamt1'] . "'";
	// 	}

	// 	if ($search['lossamt2'] != null) {
	// 		$where .= " AND gr.total_loss >= '" . $search['lossamt2'] . "'";
	// 	}

	// 	if ($search['winamt1'] != null) {
	// 		$where .= " AND gr.total_wins <= '" . $search['winamt1'] . "'";
	// 	}

	// 	if ($search['winamt2'] != null) {
	// 		$where .= " AND gr.total_wins >= '" . $search['winamt2'] . "'";
	// 	}

	// 	if ($search['earnamt1'] != null) {
	// 		$where .= " AND gr.total_earned <= '" . $search['earnamt1'] . "'";
	// 	}

	// 	if ($search['earnamt2'] != null) {
	// 		$where .= " AND gr.total_earned >= '" . $search['earnamt2'] . "'";
	// 	}

	// 	if ($search['playerlevel'] != null) {
	// 		$where .= " AND vipcbr.vipsettingcashbackruleId = '" . $search['playerlevel'] . "'";
	// 	}

	// 	if ($search['username'] != null) {
	// 		$where .= " AND p.username LIKE '%" . $search['username'] . "%'";
	// 	}

	// 	$query = "SELECT gr.*, p.username, CONCAT(pd.firstname, ' ', pd.lastname) as realname,
 //            p.lastLoginIp, p.lastLoginTime, p.lastLogoutTime,
 //            CONCAT(vipst.groupName, ' ', vipcbr.vipLevel) as playerlevel
 //            FROM games_report as gr
 //            LEFT JOIN player as p ON gr.playerId = p.playerId
 //            LEFT JOIN playerdetails as pd ON p.playerId = pd.playerId
 //            LEFT JOIN playerlevel as pl ON p.playerId = pl.playerId
 //            LEFT JOIN vipsettingcashbackrule as vipcbr ON vipcbr.vipsettingcashbackruleId = pl.playerGroupId
 //            LEFT JOIN vipsetting as vipst ON vipst.vipSettingId = vipcbr.vipSettingId
 //        ";

	// 	$order = "ORDER BY gr.gameReportId DESC";

	// 	/*echo $query . " " . $where;*/

	// 	$run = $this->db->query("$query $where $order");
	// 	$result = $run->result_array();

	// 	return $result;
	// }



















	public function getGamesReport($data = null, $group_by = 'game_logs.game_description_id') {

	}














































	/* end of Games Report */

	/* Income Report */

	/**
	 * get income report
	 *
	 * @param   array
	 * @param   int
	 * @param   int
	 * @return  array
	 */
	public function getIncomeReport($start_date, $end_date) {
		$search = array(
			'period' => $this->session->userdata('period'),
			'start_date' => $this->session->userdata('start_date'),
			'end_date' => $this->session->userdata('end_date'),
			'depamt1' => $this->session->userdata('depamt1'),
			'depamt2' => $this->session->userdata('depamt2'),
			'widamt1' => $this->session->userdata('widamt1'),
			'widamt2' => $this->session->userdata('widamt2'),
			'status' => $this->session->userdata('report_income_status'),
			'playerlevel' => $this->session->userdata('report_income_level'),
			'username' => $this->session->userdata('report_income_username'),
			);

		$date = date('Y-m-d', strtotime('-1 day'));

		$segment = $this->getUriSegment(2);

		$where = null;

		if (!empty($search['start_date']) && ($segment != 'viewIncomeReportToday' && $segment != 'viewIncomeReportDaily' && $segment != 'viewIncomeReportWeekly')) {
			$where = "AND ir.date BETWEEN '" . $search['start_date'] . "' AND '" . $search['end_date'] . "'";
		} elseif (!empty($start_date)) {
			$where = "AND ir.date BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
		} elseif ($search['period'] == '') {
			$where = "AND ir.date BETWEEN '" . $date . "' AND '" . $date . "'";
		}

		/*if(!empty($search['start_date']) && $search['type_date'] == 'registration date') {
		$where .= " AND a.createdOn BETWEEN '" . $search['start_date'] . "' AND '" . $search['end_date'] . "'";
		} else if(!empty($start_date) && $search['type_date'] == 'login date') {
		$where .= " AND a.lastLogin BETWEEN '" . $search['start_date'] . "' AND '" . $search['end_date'] . "'";
	}*/

	if ($search['status'] != null) {
		$where .= " AND p.status = '" . $search['status'] . "'";
	}

	if ($search['playerlevel'] != null) {
		$where .= " AND vipcbr.vipsettingcashbackruleId = '" . $search['playerlevel'] . "'";
	}

	if ($search['username'] != null) {
		$where .= " AND p.username LIKE '%" . $search['username'] . "%'";
	}

	if ($search['depamt1'] != null) {
		$where .= " AND ir.total_deposit <= '" . $search['depamt1'] . "'";
	}

	if ($search['depamt2'] != null) {
		$where .= " AND ir.total_deposit >= '" . $search['depamt2'] . "'";
	}

	if ($search['widamt1'] != null) {
		$where .= " AND ir.total_withdrawal <= '" . $search['widamt1'] . "'";
	}

	if ($search['widamt2'] != null) {
		$where .= " AND ir.total_withdrawal >= '" . $search['widamt2'] . "'";
	}

	$query = "SELECT ir.*, p.username, CONCAT(pd.firstname, ' ', pd.lastname) as realname,
	p.email, pd.registrationIp, pd.gender, p.registered_by,
	p.lastLoginIp, p.lastLoginTime, p.lastLogoutTime, p.createdOn,
	CONCAT(vipst.groupName, ' ', vipcbr.vipLevel) as playerlevel,
	pa.totalBalanceAmount as mainwallet, pa2.totalBalanceAmount as ptwallet, pa3.totalBalanceAmount as agwallet
	FROM income_report as ir
	LEFT JOIN player as p ON ir.playerId = p.playerId
	LEFT JOIN playerdetails as pd ON p.playerId = pd.playerId
	LEFT JOIN playerlevel as pl ON p.playerId = pl.playerId
	LEFT JOIN vipsettingcashbackrule as vipcbr ON vipcbr.vipsettingcashbackruleId = pl.playerGroupId
	LEFT JOIN vipsetting as vipst ON vipst.vipSettingId = vipcbr.vipSettingId
	LEFT JOIN playeraccount as pa ON p.playerId = pa.playerId
	LEFT OUTER JOIN playeraccount as pa2 ON p.playerId = pa2.playerId
	LEFT OUTER JOIN playeraccount as pa3 ON p.playerId = pa3.playerId
	WHERE pa.type = 'wallet'
	AND pa2.type = 'subwallet' AND pa2.typeId = '1'
	AND pa3.type = 'subwallet' AND pa3.typeId = '2'
	";

	$order = "ORDER BY ir.incomeReportId DESC";

	/*echo $query . " " . $where;*/

	$run = $this->db->query("$query $where $order");
	$result = $run->result_array();

		/*echo "<pre>";
		print_r($result);
		echo "</pre>";
		exit();*/

		return $result;
	}

	/* end of Income Report */

	/**
	 * insert API player data per day
	 *
	 * @param  array
	 * @return void
	 */
	public function insertAPIData($data) {
		$this->db->insert('gameapirecord', $data);
	}

	/**
	 * update API player data per day
	 *
	 * @param  array
	 * @return void
	 */
	public function updateAPIData($id, $data) {
		$this->db->where('gameapirecordId', $id);
		$this->db->update('gameapirecord', $data);
	}

	/**
	 * check PT player data per day
	 *
	 * @param  array
	 * @return void
	 */
	public function checkPTPlayerPerDay($player_name, $bet_time) {
		$query = $this->db->query("SELECT * FROM gameapirecord
			WHERE playername = '" . $player_name . "'
			AND gamedate = '" . $bet_time . "'
			AND apitype = '1'
			");

		$result = $query->row_array();

		if (!empty($result)) {
			return $result['gameapirecordId'];
		} else {
			return null;
		}
	}

	/* Summary Report */

	/**
	 * get player report
	 *
	 * @param   array
	 * @param   int
	 * @param   int
	 * @return  array
	 */
	public function getNewRegisteredPlayer($start_date, $end_date) {
		$where = "AND p.createdOn BETWEEN '" . $start_date . "' AND '" . $end_date . "'";

		$query = "SELECT p.*,CONCAT(pd.firstname, ' ', pd.lastname) as realname,
		pd.registrationIp, pd.gender,
		CONCAT(vipst.groupName, ' ', vipcbr.vipLevel) as playerlevel,
		pa.totalBalanceAmount as mainwallet
		FROM player as p
		LEFT JOIN playerdetails as pd ON p.playerId = pd.playerId
		LEFT JOIN playerlevel as pl ON p.playerId = pl.playerId
		LEFT JOIN vipsettingcashbackrule as vipcbr ON vipcbr.vipsettingcashbackruleId = pl.playerGroupId
		LEFT JOIN vipsetting as vipst ON vipst.vipSettingId = vipcbr.vipSettingId
		LEFT JOIN playeraccount as pa ON p.playerId = pa.playerId
		WHERE pa.type = 'wallet' AND pa.typeOfPlayer = 'real'
		";

		$order = "ORDER BY p.playerId DESC";

		$run = $this->db->query("$query $where $order");
		$query_result = $run->result_array();

		$result = array();

		foreach ($query_result as $key => $value) {
			$query = $this->db->query("SELECT * FROM playeraccount where playerId = '" . $value['playerId'] . "' and type = 'subwallet'");

			$res = $query->result_array();

			foreach ($res as $key => $val) {
				if ($val['typeId'] == '1') {
					$value['ptwallet'] = $val['totalBalanceAmount'];
				} else if ($val['typeId'] == '2') {
					$value['agwallet'] = $val['totalBalanceAmount'];
				}

			}

			array_push($result, $value);
		}

		return $result;
	}

	/**
	 * get player report
	 *
	 * @param   array
	 * @param   int
	 * @param   int
	 * @return  array
	 */
	public function getRegisteredPlayer($date) {
		$where = "AND p.createdOn <= '" . $date . " 23:59:59'";

		$query = "SELECT p.*,CONCAT(pd.firstname, ' ', pd.lastname) as realname,
		pd.registrationIp, pd.gender,
		CONCAT(vipst.groupName, ' ', vipcbr.vipLevel) as playerlevel,
		pa.totalBalanceAmount as mainwallet
		FROM player as p
		LEFT JOIN playerdetails as pd ON p.playerId = pd.playerId
		LEFT JOIN playerlevel as pl ON p.playerId = pl.playerId
		LEFT JOIN vipsettingcashbackrule as vipcbr ON vipcbr.vipsettingcashbackruleId = pl.playerGroupId
		LEFT JOIN vipsetting as vipst ON vipst.vipSettingId = vipcbr.vipSettingId
		LEFT JOIN playeraccount as pa ON p.playerId = pa.playerId
		WHERE pa.type = 'wallet' AND pa.typeOfPlayer = 'real'
		";

		$order = "ORDER BY p.playerId DESC";

		$run = $this->db->query("$query $where $order");
		$query_result = $run->result_array();

		$result = array();

		foreach ($query_result as $key => $value) {
			$query = $this->db->query("SELECT * FROM playeraccount where playerId = '" . $value['playerId'] . "' and type = 'subwallet'");

			$res = $query->result_array();

			foreach ($res as $key => $val) {
				if ($val['typeId'] == '1') {
					$value['ptwallet'] = $val['totalBalanceAmount'];
				} else if ($val['typeId'] == '2') {
					$value['agwallet'] = $val['totalBalanceAmount'];
				}

			}

			array_push($result, $value);
		}

		return $result;
	}

	/**
	 * get cashback player
	 *
	 * @param   date
	 * @param   date
	 * @return  array
	 */
	public function getCashbackPlayer($date) {
		$where = "WHERE pc.receivedOn BETWEEN '" . $date . " 00:00:00' AND '" . $date . " 23:59:59'";

		$query = "SELECT DISTINCT p.*,CONCAT(pd.firstname, ' ', pd.lastname) as realname,
		pd.registrationIp,
		CONCAT(vipst.groupName, ' ', vipcbr.vipLevel) as playerlevel
		FROM player as p
		LEFT JOIN playerdetails as pd ON p.playerId = pd.playerId
		LEFT JOIN playerlevel as pl ON p.playerId = pl.playerId
		LEFT JOIN vipsettingcashbackrule as vipcbr ON vipcbr.vipsettingcashbackruleId = pl.playerGroupId
		LEFT JOIN vipsetting as vipst ON vipst.vipSettingId = vipcbr.vipSettingId
		LEFT JOIN playercashback as pc ON p.playerId = pc.playerId
		";

		$order = "ORDER BY p.playerId DESC";

		$run = $this->db->query("$query $where $order");
		$query_result = $run->result_array();

		$result = array();

		foreach ($query_result as $key => $value) {
			$query = $this->db->query("SELECT SUM(pc.amount) as amount, pc.receivedOn FROM playercashback as pc $where AND pc.playerId = '" . $value['playerId'] . "'");
			$res = $query->row_array();

			$value['amount'] = $res['amount'];
			$value['receivedOn'] = $res['receivedOn'];
			array_push($result, $value);
		}

		return $result;
	}

	/**
	 * get cashback player
	 *
	 * @param   date
	 * @param   date
	 * @return  array
	 */
	public function getBonusPlayer($date) {
		$where = "WHERE (pdp.approvedDate BETWEEN '" . $date . " 00:00:00' AND '" . $date . " 23:59:59' AND pdp.transactionStatus = '1')";
		$where .= "OR pfrd.transactionDatetime BETWEEN '" . $date . " 00:00:00' AND '" . $date . " 23:59:59'";

		$query = "SELECT DISTINCT p.*,CONCAT(pd.firstname, ' ', pd.lastname) as realname,
		pd.registrationIp,
		CONCAT(vipst.groupName, ' ', vipcbr.vipLevel) as playerlevel
		FROM player as p
		LEFT JOIN playerdetails as pd ON p.playerId = pd.playerId
		LEFT JOIN playerlevel as pl ON p.playerId = pl.playerId
		LEFT JOIN vipsettingcashbackrule as vipcbr ON vipcbr.vipsettingcashbackruleId = pl.playerGroupId
		LEFT JOIN vipsetting as vipst ON vipst.vipSettingId = vipcbr.vipSettingId
		LEFT JOIN playerpromo as pdp ON p.playerId = pdp.playerId
		LEFT JOIN playerfriendreferraldetails as pfrd ON p.playerId = pfrd.referralId
		";

		$order = "ORDER BY p.playerId DESC";

		$run = $this->db->query("$query $where $order");
		$query_result = $run->result_array();

		$result = array();

		foreach ($query_result as $key => $value) {
			$query1 = $this->db->query("SELECT SUM(pdp.bonusAmount) as amount, pdp.approvedDate FROM playerpromo as pdp WHERE pdp.dateProcessed BETWEEN '" . $date . " 00:00:00' AND '" . $date . " 23:59:59' AND pdp.transactionStatus = '1' AND pdp.playerId = '" . $value['playerId'] . "'");
			$res1 = $query1->row_array();

			$query2 = $this->db->query("SELECT SUM(pfrd.amount) as amount, pfrd.transactionDatetime FROM playerfriendreferraldetails as pfrd WHERE pfrd.transactionDatetime BETWEEN '" . $date . " 00:00:00' AND '" . $date . " 23:59:59' AND pfrd.referralId = '" . $value['playerId'] . "'");
			$res2 = $query2->row_array();

			$value['amount'] = $res1['amount'] + $res2['amount'];
			$value['approvedDate'] = ($res1['approvedDate'] == null) ? $res2['transactionDatetime'] : $res1['approvedDate'];
			array_push($result, $value);
		}

		return $result;
	}

/**
 * record exported excel files
 * @param   array
 */
public function recordTransactionReport($data) {
	$this->db->insert('report_logs', $data);
}

/**
 * get exported excel list
 *
 * @return  json
 */
public function getExportedTransactionsReport(){


	$requestData= $_REQUEST;
	$columns = array(
// datatable column index  => database column name
		0 =>'created_at',
		1 => 'filepath',
		);
// getting total number records without any search
	$sql = "SELECT created_at,filepath";
	$sql.=" FROM report_logs";
	$query = $this->db->query($sql);
	$totalData = $query->num_rows();
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows

if( !empty($requestData['search']['value']) ) {
	// if there is a search parameter
	$sql = "SELECT created_at,filepath ";
	$sql.=" FROM report_logs";
	$sql.=" WHERE created_at LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR filepath LIKE '".$requestData['search']['value']."%' ";
	$query = $this->db->query($sql);
	$totalFiltered = $query->num_rows();
	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	$query = $this->db->query($sql);

} else {
	$sql = "SELECT created_at,filepath";
	$sql.=" FROM report_logs";
	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	$query = $this->db->query($sql);

}
$data = array();

foreach($query->result_array() as $row){
	$nestedData=array();
	$nestedData[] = $row["created_at"];
	$nestedData[] ='<a href="/report_management/downloadTransactionReport/'.$row["filepath"].'">'.$row["filepath"].'</a>' ;
	$data[] = $nestedData;
}

$json_data = array(
	"draw"            => intval( $requestData['draw'] ),
	"recordsTotal"    => intval( $totalData ),
	"recordsFiltered" => intval( $totalFiltered ),
	"data"            => $data
	);
echo json_encode($json_data);
}


















/* end of Summary Report */
}