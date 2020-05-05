<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Total_player_game_month
 *
 * General behaviors include :
 *
 * * Get last sync month
 * * Sync total player game per month
 * * Get first/last record
 * * Get total bets win and loss by player
 * * Get player game summary
 *
 * @category Game Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Total_player_game_month extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "total_player_game_month";

	/**
	 * overview : get last sync month
	 *
	 * @return string
	 */
	public function getLastSyncMonth() {
		$this->db->order_by('month desc');
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'date');
	}

	/**
	 * overview : check if unique id already exist
	 *
	 * @param string $uniqueId
	 * @return boolean
	 */
	function isUniqueIdAlreadyExists($uniqueId) {
		$qry = $this->db->get_where($this->tableName, array('uniqueid' => $uniqueId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * overview : sync to total player game month
	 *
	 * @param string $data
	 * @return array
	 */
	function syncToTotalPlayerGameMonth($data) {
		if ($this->isUniqueIdAlreadyExists($data['uniqueid'])) {
			$this->db->where('uniqueid', $data['uniqueid']);
			return $this->db->update($this->tableName, $data);
		} else {
			return $this->db->insert($this->tableName, $data);

		}
	}

	/**
	 * overview : get all record per year of all player
	 *
	 * @param datetime $dateFrom
	 * @param datetime $dateTo
	 * @return boolean
	 */
	function getAllRecordPerYearOfAllPlayer($dateFrom, $dateTo) {
		$this->db->select("player_id, game_platform_id, game_type_id, game_description_id, substr(convert(month,CHAR),1,4) as year,sum(betting_amount) as betting_amount,sum(result_amount) as result_amount", false);
		$this->db->where("substr(convert(month,CHAR),1,4) >=", $dateFrom);
		$this->db->where("substr(convert(month,CHAR),1,4) <=", $dateTo);
		$this->db->group_by(array("player_id", "game_platform_id", "game_type_id", "game_description_id", "substr(convert(month,CHAR),0,4)"));
		$qry = $this->db->get($this->tableName);
		return $this->getMultipleRow($qry);
		// $qry = $this->db->query("SELECT * FROM $this->tableName
		// 							WHERE updated_at >= '" . $dateFrom . "'
		// 							AND updated_at <= '" . $dateTo . "'
		// 						");
		// return $this->getMultipleRow($qry);
	}

	/**
	 * overview : get all record per day of player
	 *
	 * @param datetime	$dateFrom
	 * @param datetime	$dateTo
	 * @param int		$playerId
	 * @return array
	 */
	function getAllRecordPerYearOfPlayer($dateFrom, $dateTo, $playerId) {
		$this->db->select("player_id, game_platform_id, game_type_id, game_description_id, substr(convert(month,CHAR),1,4) as year,sum(betting_amount) as betting_amount,sum(result_amount) as result_amount", false);
		$this->db->where("substr(convert(month,CHAR),1,4) >=", $dateFrom);
		$this->db->where("substr(convert(month,CHAR),1,4) <=", $dateTo);
		$this->db->where("player_id", $playerId);
		$this->db->group_by(array("player_id", "game_platform_id", "game_type_id", "game_description_id", "substr(convert(month,CHAR),0,4)"));
		$qry = $this->db->get($this->tableName);
		return $this->getMultipleRow($qry);
		// $qry = $this->db->query("SELECT * FROM $this->tableName
		// 							WHERE updated_at >= '" . $dateFrom . "'
		// 							AND updated_at <= '" . $dateTo . "'
		// 							AND player_id <= '" . $playerId . "'
		// 						");
		// return $this->getMultipleRow($qry);
	}

	/**
	 * overview : sum game logs by player
	 *
	 * @param int		$playerId
	 * @param datetime  $year
	 * @param datetime  $month
	 * @return double
	 */
	public function sumGameLogsByPlayer($playerId, $year = null, $month = null) {
		//default month is this month
		// if (empty($year) || empty($month)) {
		// 	list($year, $month) = $this->utils->getThisYearThisMonth();
		// }
		$this->db->select_sum('betting_amount')->from($this->tableName)
			->where('player_id', $playerId);
		if (!empty($year) && !empty($month)) {
			$this->db->where('month', $this->utils->getStringYearMonth($year, $month));
		}
		return $this->runOneRowOneField('betting_amount');
	}

	/**
	 * overview : sum game logs by player
	 *
	 * @param int		$playerId
	 * @param datetime  $year
	 * @param datetime  $month
	 * @param int		$providersId
	 * @return double
	 */
	public function sumGameLogsByPlayerByProvider($playerId, $year = null, $month = null, $providersId) {
		$this->db->select_sum('betting_amount')->from($this->tableName)
			->where('player_id', $playerId)
			->where_in('game_platform_id', $providersId);
		if (!empty($year) && !empty($month)) {
			$this->db->where('month', $this->utils->getStringYearMonth($year, $month));
		}
		return $this->runOneRowOneField('betting_amount');
	}

	/**
	 * overview : sync
	 *
	 * @param DateTime 	$from
	 * @param DateTime 	$to
	 * @param int		$playerId
	 * @return array
	 */
	public function sync(\DateTime $from, \DateTime $to, $playerId = null) {

		$fromStr = $from->format('Ym');
		$toStr = $to->format('Ym');

		// $fromStr = $this->utils->formatDateForMysql($from);
		// $toStr = $this->utils->formatDateForMysql($to);
		// $fromDateStr = $this->utils->formatDateForMysql($from);
		// $toDateStr = $this->utils->formatDateForMysql($to);

		$playerIdSql = null;
		if (!empty($playerId)) {
			$this->db->where('player_id', $playerId);
			$playerIdSql = ' and player_id=' . $this->db->escape($playerId);
		}

		$this->db->where('month >=', $fromStr)
			->where('month <=', $toStr);
		$this->db->delete('total_player_game_month');

		$now=$this->utils->getNowForMysql();
		// $this->utils->printLastSQL();
		$params=[$fromStr, $toStr];
		$t=time();
		$sql = <<<EOD
select
player_id, round(sum(betting_amount),4) as betting_amount, round(sum(real_betting_amount),4) as real_betting_amount, round(sum(result_amount),4) as result_amount,
round(sum(win_amount),4) as win_amount, round(sum(loss_amount),4) as loss_amount,
DATE_FORMAT(date,'%Y%m') as month, '$now' as updated_at, round(sum(bet_for_cashback),4) as bet_for_cashback,
game_platform_id, game_type_id, game_description_id,
concat(player_id, '_', game_platform_id, '_', game_type_id, '_', game_description_id, '_',DATE_FORMAT(date,'%Y%m')) as uniqueid

from total_player_game_day
where DATE_FORMAT(date,'%Y%m') >= ?
and DATE_FORMAT(date,'%Y%m') <= ?
{$playerIdSql}
group by player_id, game_platform_id, game_type_id, game_description_id, DATE_FORMAT(date,'%Y%m')
EOD;
		$rows=$this->runRawSelectSQLArray($sql, $params);
		$this->utils->info_log('get rows from total_player_game_day', count($rows), 'cost', $params, (time()-$t));
		$t=time();
		$cnt=0;
		$limit=500;
		$success=$this->runBatchInsertWithLimit($this->db, 'total_player_game_month', $rows, $limit, $cnt);
		unset($rows);
		$this->utils->info_log('insert into total_player_game_month', $cnt, 'cost', $params, (time()-$t));

		return $success;

// 		$sql = <<<EOD
// insert into total_player_game_month(
// player_id, betting_amount, real_betting_amount, result_amount, win_amount, loss_amount,
// month, updated_at, bet_for_cashback,
// game_platform_id, game_type_id, game_description_id,
// uniqueid
// )

// select
// player_id, round(sum(betting_amount),4) as betting_amount, round(sum(real_betting_amount),4) as real_betting_amount, round(sum(result_amount),4) as result_amount,
// round(sum(win_amount),4) as win_amount, round(sum(loss_amount),4) as loss_amount,
// DATE_FORMAT(date,'%Y%m'), '$now', round(sum(bet_for_cashback),4),
// game_platform_id, game_type_id, game_description_id,
// concat(player_id, '_', game_platform_id, '_', game_type_id, '_', game_description_id, '_',DATE_FORMAT(date,'%Y%m'))

// from total_player_game_day
// where DATE_FORMAT(date,'%Y%m') >= ?
// and DATE_FORMAT(date,'%Y%m') <= ?
// {$playerIdSql}
// group by player_id, game_platform_id, game_type_id, game_description_id, DATE_FORMAT(date,'%Y%m')
// EOD;

// 		$qry = $this->db->query($sql, array($fromStr, $toStr));

// 		// $this->utils->printLastSQL();

// 		return $this->db->affected_rows();

	}

	/**
	 * overview: get monthly top win players
	 * @param int $gamePlatformId
	 * @param int $resultLimit
	 * @param datetime $yearMonth
	 *
	 * @return array
	 */
	function getMonthlyTopWinPlayers($gamePlatformId = null, $resultLimit = null, $yearMonth = null) {
		$this->db->select('t.month, t.player_id, p.gameName, sum(t.win_amount) as total_win_amount, t.game_platform_id as platform_id, e.system_code');
		$this->db->from('total_player_game_month t');

		$this->db->join('player p', 't.player_id = p.playerId', 'left');
		$this->db->join('external_system e', 't.game_platform_id = e.id', 'left');

		if ($gamePlatformId && $gamePlatformId != 'null') {
			$this->db->where('game_platform_id', $gamePlatformId);
		}

		if ($yearMonth && $yearMonth != 'null') {
			$this->db->where('month', $yearMonth);
		}

		$this->db->group_by("t.player_id, t.game_platform_id, t.month");
		$this->db->order_by('month', 'desc');
		$this->db->order_by('total_win_amount', 'desc');

		if ($resultLimit && $resultLimit != 'null') {
			$this->db->limit($resultLimit);
		}

		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * overview : sum operators bets wins loss by datetime
	 *
	 * @param datetime	$dateTimeFrom
	 * @param datetime	$dateTimeTo
	 * @param int		$gamePlatformId
	 * @param int		$promoruleId
	 * @param null $db
	 * @return array
	 */
	public function sumOperatorBetsWinsLossByDatetime($dateTimeFrom, $dateTimeTo, $gamePlatformId = null, $promoruleId = null, $db = null, $selected_tag = null) {

		list($totalBet, $totalWin, $totalLoss) = $this->getTotalBetsWinsLoss($dateTimeFrom, $dateTimeTo, $gamePlatformId, $db, $selected_tag);

		return array($totalBet, $totalWin, $totalLoss);

	}

	/**
	 * overview : get total bets wins and loss
	 *
	 * @param datetime	$dateTimeFrom
	 * @param datetime	$dateTimeTo
	 * @param int		$gamePlatformId
	 * @param null $db
	 * @return array
	 */
	public function getTotalBetsWinsLoss($dateTimeFrom = null, $dateTimeTo = null, $gamePlatformId = null, $db = null, $selected_tag = null) {
		$totalBet = 0;
		$totalWin = 0;
		$totalLoss = 0;

		$this->utils->debug_log('dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo, 'gamePlatformId', $gamePlatformId);
		if ($db == null) {
			$db = $this->db;
		}

		// if (!empty($playerIds)) {

		$db->select_sum('betting_amount', 'total_bet')
			->select_sum('loss_amount', 'total_loss')
			->select_sum('win_amount', 'total_win')
			->from('total_player_game_month')
			->join('player', 'player.playerId = total_player_game_month.player_id');
        if(!empty($selected_tag)){
            $db->join('playertag', 'playertag.playerId = player.playerId', 'left');
            $db->where('(playertag.tagId NOT IN ('.implode(',', $selected_tag).") OR playertag.tagId is NULL)");
        }
        $db->where('player.deleted_at IS NULL');


		// if (is_array($playerIds)) {
		// 	$this->db->where_in('player_id', $playerIds);
		// } else {
		// 	$this->db->where('player_id', $playerIds);
		// }

		if (!empty($gamePlatformId)) {
			if (is_array($gamePlatformId)) {
				$db->where_in('game_platform_id', $gamePlatformId);
			} else {
				$db->where('game_platform_id', $gamePlatformId);
			}
		}
		if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
			$fromStr = $this->utils->formatYearMonthForMysql(new DateTime($dateTimeFrom));
			$toStr = $this->utils->formatYearMonthForMysql(new DateTime($dateTimeTo));

			$db->where('month >=', $fromStr);
			$db->where('month <=', $toStr);
		}
		//get one row
		$qry = $db->get();
		$row = null;
		if ($qry && $qry->num_rows() > 0) {
			$row = $qry->row();
		}

		if ($row) {

			// $row = $this->runOneRow();
			$totalBet = $row->total_bet;
			$totalWin = $row->total_win;
			$totalLoss = $row->total_loss;
			// $this->utils->printLastSQL();
			// }
		}
		return array($totalBet, $totalWin, $totalLoss);
	}

}

///END OF FILE///////
