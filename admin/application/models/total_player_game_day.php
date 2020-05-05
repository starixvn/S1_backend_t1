<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Total_player_game_day
 *
 * General behaviors include :
 *
 * * Get last sync day
 * * Sync total player game per day
 * * Get first/last record
 * * Get total bets win and loss by player
 * * Get player game summary
 *
 * @category Game Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Total_player_game_day extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "total_player_game_day";

	/**
	 * overview : get last sync day
	 *
	 * @return string
	 */
	public function getLastSyncDay() {
		$this->db->order_by('date desc');
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
	 * overview : sync to total player game day
	 *
	 * @param string $data
	 * @return array
	 */
	function syncToTotalPlayerGameDay($data) {
		if ($this->isUniqueIdAlreadyExists($data['uniqueid'])) {
			$this->db->where('uniqueid', $data['uniqueid']);
			return $this->db->update($this->tableName, $data);
		} else {
			return $this->db->insert($this->tableName, $data);

		}
	}

	/**
	 * overview : get all record per month of all player
	 *
	 * @param datetime $dateFrom
	 * @param datetime $dateTo
	 * @return boolean
	 */
	function getAllRecordPerMonthOfAllPlayer($dateFrom, $dateTo) {
		$this->db->select("player_id, game_platform_id, game_type_id, game_description_id, date_format(date,'%Y%m') as month,sum(betting_amount) as betting_amount,sum(result_amount) as result_amount", false);
		$this->db->where("date_format(date,'%Y%m') >=", $dateFrom);
		$this->db->where("date_format(date,'%Y%m') <=", $dateTo);
		$this->db->group_by(array("player_id", "game_platform_id", "game_type_id", "game_description_id", "date_format(date,'%Y%m')"));
		$qry = $this->db->get($this->tableName);
		return $this->getMultipleRow($qry);

		// $qry = $this->db->query("SELECT * FROM $this->tableName
		// 							WHERE date >= '" . $dateFrom . "'
		// 							AND date <= '" . $dateTo . "'
		// 						");
		// return $this->getMultipleRow($qry);
	}
	// function getAllRecordPerMonthOfAllPlayer($dateFrom, $dateTo, $month) {
	// 	$qry = $this->db->query("SELECT * FROM $this->tableName
	// 								WHERE date >= '" . $dateFrom . "'
	// 								AND date <= '" . $dateTo . "'
	// 								AND MONTH(date) = '" . $month . "'
	// 								OR MONTH(date) = '" . $month . "'
	// 							");
	// 	return $this->getMultipleRow($qry);
	// }

	/**
	 * overview : get all record per month of player
	 *
	 * @param datetime	$dateFrom
	 * @param datetime	$dateTo
	 * @param int		$playerId
	 * @return array
	 */
	function getAllRecordPerMonthOfPlayer($dateFrom, $dateTo, $playerId) {
		$this->db->select("player_id, game_platform_id, game_type_id, game_description_id, date_format(date,'%Y%m') as month,sum(betting_amount) as betting_amount,sum(result_amount) as result_amount", false);
		$this->db->where("date_format(date,'%Y%m') >=", $dateFrom);
		$this->db->where("date_format(date,'%Y%m') <=", $dateTo);
		$this->db->where("player_id", $playerId);
		$this->db->group_by(array("player_id", "game_platform_id", "game_type_id", "game_description_id", "date_format(date,'%Y%m')"));
		$qry = $this->db->get($this->tableName);
		return $this->getMultipleRow($qry);
		// $qry = $this->db->query("SELECT * FROM $this->tableName
		// 							WHERE date >= '" . $dateFrom . "'
		// 							AND date <= '" . $dateTo . "'
		// 							AND player_id <= '" . $playerId . "'
		// 						");
		// return $this->getMultipleRow($qry);
	}
	// function getAllRecordPerMonthOfPlayer($dateFrom, $dateTo, $month, $playerName) {
	// 	$qry = $this->db->query("SELECT * FROM $this->tableName
	// 								WHERE date >= '" . $dateFrom . "'
	// 								AND date <= '" . $dateTo . "'
	// 								AND player_username <= '" . $playerName . "'
	// 								AND MONTH(date) = '" . $month . "'
	// 								OR MONTH(date) = '" . $month . "'
	// 							");
	// 	return $this->getMultipleRow($qry);
	// }

	/**
	 * overview : get first record date time
	 *
	 * @return datetime
	 */
	public function getFirstRecordDateTime() {
		$this->db->order_by('date asc');
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'date');
	}

	/**
	 * overview : get last record date time
	 *
	 * @return datetime
	 */
	public function getLastRecordDateTime() {
		$this->db->order_by('date desc');
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'date');
	}

	public function syncSimplePlayerGameReportDaily(\DateTime $from, \DateTime $to, $playerId = null){
		$now=$this->utils->getNowForMysql();

		$fromDateStr = $this->utils->formatDateForMysql($from);
		$toDateStr = $this->utils->formatDateForMysql($to);
        $currencyKey=$this->utils->getActiveCurrencyKey();

        $params=[$fromDateStr, $toDateStr];
		$playerIdSql = null;
		if (!empty($playerId)) {
			$playerId=intval($playerId);
			$this->db->where('total_player_game_day.player_id', $playerId);
			$playerIdSql = ' and total_player_game_day.player_id=?';
			$params[]=$playerId;
		}

		$this->utils->info_log('try delete rows from player_report_simple_game_daily', $params);

		$this->db->where('total_date >=', $fromDateStr)
			->where('total_date <=', $toDateStr);
		$this->db->delete('player_report_simple_game_daily');
		$t=time();
		$sql=<<<EOD
select
total_player_game_day.player_id, player.username, game_provider_auth.login_name as game_username,
total_player_game_day.game_platform_id, total_player_game_day.game_type_id,
case when sum(betting_amount) is null then 0 else round(sum(betting_amount),4) end as betting_amount,
case when sum(real_betting_amount) is null then 0 else round(sum(real_betting_amount),4) end as real_betting_amount,
case when sum(result_amount) is null then 0 else round(sum(result_amount),4) end as result_amount,
case when sum(win_amount) is null then 0 else round(sum(win_amount),4) end as win_amount,
case when sum(loss_amount) is null then 0 else round(sum(loss_amount),4) end as loss_amount,
`date` as total_date, '$now' as created_at, '$now' as updated_at, '$currencyKey' as currency_key,
concat(total_player_game_day.player_id, '_', total_player_game_day.game_platform_id, '_', total_player_game_day.game_type_id, '_', `date`) as uniqueid,
player.agent_id
from total_player_game_day
join player on player.playerId=total_player_game_day.player_id
left join game_provider_auth on game_provider_auth.player_id=total_player_game_day.player_id
  and game_provider_auth.game_provider_id=total_player_game_day.game_platform_id
where `date` >= ?
and `date` <= ?
{$playerIdSql}
group by total_player_game_day.player_id, total_player_game_day.game_platform_id, total_player_game_day.game_type_id, `date`
EOD;

		$rows=$this->runRawSelectSQLArray($sql, $params);
		$this->utils->info_log('get rows from total_player_game_day', count($rows), $params, 'cost', (time()-$t));
		$t=time();
		$cnt=0;
		$limit=100;
		$success=$this->runBatchInsertWithLimit($this->db, 'player_report_simple_game_daily', $rows, $limit, $cnt);
		unset($rows);
		$this->utils->info_log('insert into player_report_simple_game_daily', $cnt, $params, 'cost', (time()-$t));

		return $success;

// 		$sql=<<<EOD
// insert into player_report_simple_game_daily(
// player_id, username,
// game_platform_id, game_type_id,
// betting_amount, real_betting_amount, result_amount, win_amount, loss_amount,
// total_date, created_at, updated_at,
// currency_key, uniqueid
// )
// select
// player_id, username,
// game_platform_id, game_type_id,
// round(sum(betting_amount),4) as betting_amount, round(sum(real_betting_amount),4) as real_betting_amount,
// round(sum(result_amount),4) as result_amount, round(sum(win_amount),4) as win_amount, round(sum(loss_amount),4) as loss_amount,
// `date`, '$now', '$now',
// '$currencyKey', concat(player_id, '_', game_platform_id, '_', game_type_id, '_', `date`)
// from total_player_game_day
// join player on player.playerId=total_player_game_day.player_id
// where `date` >= ?
// and `date` <= ?
// {$playerIdSql}
// group by player_id, game_platform_id, game_type_id, `date`

// EOD;

// 		return $this->runRawUpdateInsertSQL($sql, $params);

	}

	/**
	 * overview : sync
	 * @param DateTime 	$from
	 * @param DateTime 	$to
	 * @param int		$playerId
	 * @return array
	 */
	public function sync(\DateTime $from, \DateTime $to, $playerId = null) {

		// $fromStr = $from->format('Y-m-d H') . ':00:00';
		// $toStr = $to->format('Y-m-d H') . ':59:59';

		$fromStr = $this->utils->formatDateForMysql($from);
		$toStr = $this->utils->formatDateForMysql($to);
		$fromDateStr = $this->utils->formatDateForMysql($from);
		$toDateStr = $this->utils->formatDateForMysql($to);

		$playerIdSql = null;
		if (!empty($playerId)) {
			$this->db->where('player_id', $playerId);
			$playerIdSql = ' and player_id=' . $this->db->escape($playerId);
		}

		$this->db->where('date >=', $fromDateStr)
			->where('date <=', $toDateStr);
		$this->db->delete('total_player_game_day');

		$now=$this->utils->getNowForMysql();
		// $this->utils->printLastSQL();
		$params=[$fromStr, $toStr];
		$t=time();
		$sql = <<<EOD
select
player_id, round(sum(betting_amount),4) as betting_amount, round(sum(real_betting_amount),4) as real_betting_amount,round(sum(result_amount),4) as result_amount,
round(sum(win_amount),4) as win_amount, round(sum(loss_amount),4) as loss_amount,
date, '$now' as updated_at, round(sum(bet_for_cashback),4) as bet_for_cashback,
game_platform_id, game_type_id, game_description_id,
concat(player_id, '_', game_platform_id, '_', game_type_id, '_', game_description_id, '_',date) as uniqueid

from total_player_game_hour
where date >= ?
and date <= ?
{$playerIdSql}
group by player_id, game_platform_id, game_type_id, game_description_id, date
EOD;
		$rows=$this->runRawSelectSQLArray($sql, $params);
		$this->utils->info_log('get rows from total_player_game_hour', count($rows), $params, 'cost', (time()-$t));
		$t=time();
		$cnt=0;
		$limit=100;
		$success=$this->runBatchInsertWithLimit($this->db, 'total_player_game_day', $rows, $limit, $cnt);
		unset($rows);
		$this->utils->info_log('insert into total_player_game_day', $cnt, 'cost', $params, (time()-$t));

// 		$sql = <<<EOD
// insert into total_player_game_day(
// player_id, betting_amount, real_betting_amount, result_amount, win_amount, loss_amount,
// date, updated_at, bet_for_cashback,
// game_platform_id, game_type_id, game_description_id,
// uniqueid
// )

// select
// player_id, round(sum(betting_amount),4) as betting_amount, round(sum(real_betting_amount),4) as real_betting_amount,round(sum(result_amount),4) as result_amount,
// round(sum(win_amount),4) as win_amount, round(sum(loss_amount),4) as loss_amount,
// date, '$now', round(sum(bet_for_cashback),4),
// game_platform_id, game_type_id, game_description_id,
// concat(player_id, '_', game_platform_id, '_', game_type_id, '_', game_description_id, '_',date)

// from total_player_game_hour
// where date >= ?
// and date <= ?
// {$playerIdSql}
// group by player_id, game_platform_id, game_type_id, game_description_id, date
// EOD;

// 		$qry = $this->db->query($sql, array($fromStr, $toStr));

		// $this->utils->printLastSQL();

		// $cnt=$this->db->affected_rows();

		$this->syncSimplePlayerGameReportDaily($from, $to, $playerId);
		// $this->utils->printLastSQL();
		// $this->utils->debug_log('cntPlayerReport', $cntPlayerReport, $this->db->getOgTargetDB());

		return $success;
	}

	/**
	 * overview : get total bets wins loss by player
	 *
	 * @param int		$playerIds
	 * @param datetime	$dateTimeFrom
	 * @param datetime	$dateTimeTo
	 * @param int		$gamePlatformId
	 * @return array
	 */
	public function getTotalBetsWinsLossByPlayers($playerIds, $dateTimeFrom, $dateTimeTo, $gamePlatformId = null) {
		$totalBet = 0;
		$totalWin = 0;
		$totalLoss = 0;

		if (!empty($playerIds)) {

			$this->db->select_sum('betting_amount', 'total_bet')
				->select_sum('IF(result_amount < 0, ABS(result_amount) , 0)', 'total_loss')
				->select_sum('IF(result_amount > 0, result_amount , 0)', 'total_win')
				->from('total_player_game_day')
			;

			if (count($playerIds) == 1) {
				$this->db->where('player_id', $playerIds[0]);
			} else {
				$this->db->where_in('player_id', $playerIds);
			}

			if (!empty($gamePlatformId)) {
				$this->db->where('game_platform_id', $gamePlatformId);
			}
			if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
				$fromStr = $this->utils->formatDateForMysql(new DateTime($dateTimeFrom));
				$toStr = $this->utils->formatDateForMysql(new DateTime($dateTimeTo));

				$this->db->where('date >=', $fromStr);
				$this->db->where('date <=', $toStr);
			}

			$row = $this->runOneRow();
			$totalBet = $row->total_bet;
			$totalWin = $row->total_win;
			$totalLoss = $row->total_loss;
			// $this->utils->printLastSQL();
		}
		return array($totalBet, $totalWin, $totalLoss, $totalBet);
	}

	/**
	 * overview : get player total bets by date time
	 *
	 * @param int		$playerId
	 * @param datetime	$dateTimeFrom
	 * @param datetime	$dateTimeTo
	 * @param int		$gamePlatformId
	 * @return array
	 */
	public function getPlayerTotalBetsWinsLossByDatetime($playerId, $dateTimeFrom, $dateTimeTo, $gamePlatformId = null) {
		return $this->getTotalBetsWinsLossByPlayers(array($playerId), $dateTimeFrom, $dateTimeTo, $gamePlatformId);
	}

	/**
	 * overview : group total bets wins loss
	 *
	 * @param datetime	$dateTimeFrom
	 * @param datetime	$dateTimeTo
	 * @return array
	 */
	public function groupTotalBetsWinsLossGroupByPlayers($dateTimeFrom, $dateTimeTo) {
		$result = array();

		$totalBet = 0;
		$totalWin = 0;
		$totalLoss = 0;

		$this->db->select_sum('betting_amount', 'total_bet')
			->select_sum('loss_amount', 'total_loss')
			->select_sum('win_amount', 'total_win')
			->select('player_id')
			->from('total_player_game_day')
			->group_by('player_id')
		;

		if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
			$fromDateMinuteStr = $this->utils->formatDateForMysql(new DateTime($dateTimeFrom));
			$toDateMinuteStr = $this->utils->formatDateForMysql(new DateTime($dateTimeTo));

			$this->db->where('date >=', $fromDateMinuteStr);
			$this->db->where('date <=', $toDateMinuteStr);
		}

		$rows = $this->runMultipleRowArray();
		// $this->utils->printLastSQL();

		// if (!empty($rows)) {
		// 	foreach ($rows as $row) {
		// 		$totalBet = $row->total_bet;
		// 		$totalWin = $row->total_win;
		// 		$totalLoss = $row->total_loss;
		// 		$result[$row->player_id] = array($totalBet, $totalWin, $totalLoss);
		// 	}
		// }

		return $rows;
	}

	/**
	 * overview : sum operators bet wins loss by datetime
	 *
	 * @param datetime	$dateTimeFrom
	 * @param datetime	$dateTimeTo
	 * @param int		$gamePlatformId
	 * @param int		$promoruleId
	 * @param int		$db
	 * @return array
	 */
	public function sumOperatorBetsWinsLossByDatetime($dateTimeFrom, $dateTimeTo, $gamePlatformId = null, $promoruleId = null, $db = null, $selected_tag = null) {

		list($totalBet, $totalWin, $totalLoss) = $this->getTotalBetsWinsLoss($dateTimeFrom, $dateTimeTo, $gamePlatformId, $db, $selected_tag);

		return array($totalBet, $totalWin, $totalLoss);

	}

	/**
	 * overview : get total bets wins loss
	 *
	 * @param datetime 	 $dateTimeFrom
	 * @param datetime	 $dateTimeTo
	 * @param int		 $gamePlatformId
	 * @param int		 $db
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
			->from('total_player_game_day')
			->join('player', 'player.playerId = total_player_game_day.player_id');
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
			$fromStr = $this->utils->formatDateForMysql(new DateTime($dateTimeFrom));
			$toStr = $this->utils->formatDateForMysql(new DateTime($dateTimeTo));

			$db->where('date >=', $fromStr);
			$db->where('date <=', $toStr);
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

	/**
	 * overview : get total bets wins loss group by name
	 *
	 * @param int		$playerIds
	 * @param datetime	$dateTimeFrom
	 * @param datetime	$dateTimeTo
	 * @return array
	 */
	public function getTotalBetsWinsLossGroupByGamePlatformByPlayers($playerIds, $dateTimeFrom, $dateTimeTo) {
		$result = array();

		$totalBet = 0;
		$totalWin = 0;
		$totalLoss = 0;

		if (!empty($playerIds)) {

			$this->db->select_sum('betting_amount', 'total_bet')
				->select_sum('loss_amount', 'total_loss')
				->select_sum('win_amount', 'total_win')
				->select('game_platform_id')
				->from('total_player_game_day')
				->group_by('game_platform_id')
			;

			if (is_array($playerIds)) {
				$this->db->where_in('player_id', $playerIds);
			} else {
				$this->db->where('player_id', $playerIds);
			}

			if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
				$fromStr = $this->utils->formatDateForMysql(new DateTime($dateTimeFrom));
				$toStr = $this->utils->formatDateForMysql(new DateTime($dateTimeTo));

				$this->db->where('date >=', $fromStr);
				$this->db->where('date <=', $toStr);
			}

			$rows = $this->runMultipleRow();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					$totalBet = $row->total_bet;
					$totalWin = $row->total_win;
					$totalLoss = $row->total_loss;
					$result[$row->game_platform_id] = array($totalBet, $totalWin, $totalLoss);
				}
			}
			// $this->utils->printLastSQL();
		}
		return $result;
	}

	/**
	 * overview : get summary
	 *
	 * @param $player_id
	 * @return bool
	 */
	public function getSummary($player_id) {

		$this->db->select('game_platform_id id');
		$this->db->select_sum('betting_amount', 'bet_sum');
		$this->db->select('count(betting_amount) bet_count', false);
		$this->db->select_sum('(CASE WHEN result_amount >= 0 THEN result_amount ELSE 0 END)', 'gain_sum');
		$this->db->select_sum('(result_amount >= 0)', 'gain_count');
		$this->db->select_sum('(CASE WHEN result_amount < 0 THEN (0 - result_amount) ELSE 0 END)', 'loss_sum');
		$this->db->select_sum('(result_amount < 0)', 'loss_count');
		$this->db->select_sum('result_amount', 'gain_loss_sum');
		$this->db->select('count(result_amount) gain_loss_count', false);
		$this->db->from('total_player_game_day');
		$this->db->where('player_id', $player_id);
		$this->db->group_by('game_platform_id');
		$query = $this->db->get();
		$result = $query->result_array();
		$list = array();
		foreach ($result as $row) {
			$list[$row['id']] = array(
				'bet' => array(
					'sum' => $row['bet_sum'],
					'count' => $row['bet_count'],
					'ave' => $row['bet_count'] ? $row['bet_sum'] / $row['bet_count'] : 0,
				),
				'gain' => array(
					'sum' => $row['gain_sum'],
					'count' => $row['gain_count'],
					'percent' => $row['bet_count'] ? (($row['gain_count'] / $row['bet_count']) * 100) : 0,
					'ave' => $row['gain_count'] ? $row['gain_sum'] / $row['gain_count'] : 0,
				),
				'loss' => array(
					'sum' => $row['loss_sum'],
					'count' => $row['loss_count'],
					'percent' => $row['bet_count'] ? (($row['loss_count'] / $row['bet_count']) * 100) : 0,
					'ave' => $row['loss_count'] ? $row['loss_sum'] / $row['loss_count'] : 0,

				),
				'gain_loss' => array(
					'sum' => $row['gain_loss_sum'],
					'count' => $row['gain_loss_count'],
				),
			);
		}
		return $list ?: false;
	}

	public function getTodayActivePlayers($player_ids = null, $adate = null){
		// OGP-15002 add date argument
		$today = !empty($adate) ? $adate : $this->utils->getTodayForMysql();

		$query = $this->db->select("id")->from("total_player_game_day")->where("date", $today);
            if($player_ids){
                $query->where_in('player_id', $player_ids);
            }
         $query->group_by("player_id")->join("player as p", "p.playerId = total_player_game_day.player_id");

		$query = $query->get();
		$this->utils->debug_log($this->db->last_query());
		$countPlayerActiveToday = "0";
		foreach ($query->result_array() as $key) {
			$countPlayerActiveToday ++;
		}

		return $countPlayerActiveToday;

	}

	public function getActivePlayersCountByDate($start, $end = '', $player_ids = null){
		if (empty($end)) { $end = $start; }

		$query = $this->db->select("id")
				 ->from("total_player_game_day")
				 ->where("date >=", $start)
				 ->where("date <=", $end);
            if($player_ids){
                $query->where_in('player_id', $player_ids);
            }
				 $query->join("player as p", "p.playerId = total_player_game_day.player_id")->group_by("player_id");

        $this->utils->debug_log('the query ------>', $query->_compile_select());
        $query = $query->get();
		$countPlayerActive = "0";
		foreach ($query->result_array() as $key) {
			$countPlayerActive += 1;
		}
		return $countPlayerActive;

	}

    public function getActivePlayersCountByDateGroupByPlayerId($start, $end = '', $player_ids = null){
        if (empty($end)) { $end = $start; }

        $query = $this->db->select("id")
            ->from("total_player_game_day")
            ->where("date >=", $start)
            ->where("date <=", $end);
        if($player_ids){
            $query->where_in('player_id', $player_ids);
        }
        $query->group_by("player_id")
            ->join("player as p", "p.playerId = total_player_game_day.player_id");

        $query = $query->get();

        return count($query->result_array());

    }

    public function getActivePlayerCountPerPlayerId( $start = '', $end = '', $query = '' ){

        $this->db->select('player_id, game_platform_id, date as created_at');
        $this->db->from($this->tableName);
        $this->db->join("player as p", "p.playerId = total_player_game_day.player_id");

        if( ! empty( $start ) && ! empty($end) ){
            $this->db->where('date BETWEEN "' . $start . '" AND "' . $end .'"');
        }

        $where = 'id > 0';
        $where .= ' OR betting_amount > 0';

        $this->db->where('(' . $where . ')');
        $group_by = 'game_platform_id, date, player_id';

        $this->db->group_by($group_by);
        $this->db->order_by('date');

        $result_from_db = $this->db->get();

        $cnt_active_player_per_game_provider = array();
        $list_of_players = array();
        $list_of_game_platform_id = array();

        #count the active player per game provider
        foreach ($result_from_db->result_array() as $key => $value) {

            #check player if already in the list of players per game provider
            if(in_array($value['player_id'], $list_of_players)) continue;
            if($query['view_type'] != 'daily'){
                @$cnt_active_player_per_game_provider[$value['created_at']][$value['game_platform_id']][$value['player_id']] = $value['player_id'];
            }else{
                @$cnt_active_player_per_game_provider[$value['created_at']][$value['game_platform_id']] += 1;
            }
            #insert player per game provider if not yet in the list
            $list_of_players[$value['created_at']][$value['game_platform_id']][$value['player_id']] = $value['player_id'];
        }

        #finalized the result
        $result = array();
        foreach ($result_from_db->result_array() as $key => $value) {

            #group the result per game provider only
            if(in_array($value['game_platform_id'], $list_of_game_platform_id)) continue;
            $list_of_game_platform_id[$value['created_at']][$value['game_platform_id']] = $cnt_active_player_per_game_provider[$value['created_at']][$value['game_platform_id']];

            $result[$key]['created_at']       = $value['created_at'];
            $result[$key]['active_players']   = $cnt_active_player_per_game_provider[$value['created_at']][$value['game_platform_id']];
            $result[$key]['game_platform_id'] = $value['game_platform_id'];;
        }

        return $result;

    }

    public function getActivePlayerCountMonthly( $start = '', $end = '', $query = '' ){

        $start = (new DateTime($start))->modify('first day of this month');
		$end = (new DateTime($end));
		$end->modify('+1 day');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);

        $months = array();

        foreach ($period as $dt) {
            $months[$dt->format("F Y")]['first_date_of_month'] = $dt->format("Y-m-d");
            $months[$dt->format("F Y")]['end_date_of_month'] = $dt->format("Y-m-t");
        }

        $data_per_month = array();
        $result = array();
        foreach ($months as $month => $date_of_month) {
            $this->db->select('player_id, game_platform_id');
            $this->db->from($this->tableName);
            $this->db->join("player as p", "p.playerId = total_player_game_day.player_id");

            if( ! empty( $start ) && ! empty($end) ){
                $this->db->where('date BETWEEN "' . $date_of_month['first_date_of_month'] . '" AND "' . $date_of_month['end_date_of_month'] .'"');
            }

            $where = 'id > 0';
            $where .= ' OR betting_amount > 0';

            $this->db->where('(' . $where . ')');
            $group_by = 'game_platform_id, date, player_id';

            $this->db->group_by($group_by);
            $this->db->order_by('date');
            $result_from_db = $this->db->get();

            $data_per_month[$month] = $result_from_db->result_array();
            // $result[$month]['val'] = $this->getActivePlayersCountByDate($date_of_month['first_date_of_month'], $date_of_month['end_date_of_month']);

            $list_of_active_players_by_date = $this->total_player_game_day->getActivePlayersByDate($date_of_month['first_date_of_month'], $date_of_month['end_date_of_month']);
            $result[$month]['val'] = count($list_of_active_players_by_date);
        }

        $cnt_active_player_per_game_provider = array();
        $list_of_players = array();

        #count the active player per game provider
        foreach ($data_per_month as $month => $per_players_data) {

            foreach ($per_players_data as $key => $value) {
                #check player if already in the list of players per game provider
                if(in_array($value['player_id'], $list_of_players)) continue;
                @$cnt_active_player_per_game_provider[$month][$value['game_platform_id']] += 1;

                #insert player per game provider if not yet in the list
                $list_of_players[$month][$value['game_platform_id']][$value['player_id']] = $value['player_id'];
            }

        }

        #Finalize the result
        foreach ($list_of_players as $months => $game_providers) {
            foreach ($game_providers as $game_provider_id => $player_ids) {
                $result[$months][$game_provider_id] = count($player_ids);
            }
        }

        // print_r($result);
        // print_r($list_of_players);

        // exit();
        return $result;

    }

	public function getActivePlayerCount( $start = '', $end = '', $query = '' ){

		$this->db->select('COUNT(player_id) AS active_players, 	game_platform_id, date as created_at');
		$this->db->from($this->tableName);

		if( ! empty( $start ) && ! empty($end) ){
			$this->db->where('date BETWEEN "' . $start . '" AND "' . $end .'"');
		}

		$where = 'id > 0';
		$where .= ' OR betting_amount > 0';

		// if( isset($query['filtered']) && in_array('bet', $query['filtered']) ) $where .= ' OR betting_amount > 0';
		// if( isset($query['filtered']) && in_array('withdrawal', $query['filtered']) ) $where .= ' OR transactions.transaction_type > 2';
		// if( isset($query['filtered']) && in_array('deposit', $query['filtered']) ) $where .= ' OR transactions.transaction_type > 1';

 		$this->db->where('(' . $where . ')');
		$group_by = 'game_platform_id, date';

		$this->db->group_by($group_by);
		$this->db->order_by('date');

		$query = $this->db->get();
		return $query->result_array();

	}

	public function getActivePlayersByDate($start,$end,$game_platform_id = null){

		$this->db->select("tpgd.id as id, p.username as username, tpgd.date as created_at, tpgd.game_platform_id as game_platform_id, tpgd.player_id as player_id")
				 ->from("total_player_game_day tpgd")
				 ->join("player as p", "p.playerId = tpgd.player_id")
				 ->where("date >=", $start)
				 ->where("date <=", $end);
        if(!empty($game_platform_id)){
    		$this->db->where("game_platform_id", $game_platform_id);
        }

		$this->db->group_by("player_id");

		$query = $this->db->get();

		return $query->result_array();

	}

	public function getActivePlayersByDateGamePlatformId($player_id,$game_platform_id,$start,$end){
		$today = $this->utils->getTodayForMysql();

		$this->db->select("count(id)")
				 ->from("total_player_game_day tpgd")
				 ->join("player as p", "p.playerId = tpgd.player_id")
				 ->where("date >=", $start)
				 ->where("date <=", $end)
				 ->where("player_id", $player_id)
				 ->where("game_platform_id", $game_platform_id)
				 ->group_by("player_id")
				 ->group_by("game_platform_id");

		$query = $this->db->get();

		return $query->result_array();

	}

	public function getPlayerTotalBetWinLoss($player_id, $dateTimeFrom, $dateTimeTo) {
		$this->db->select_sum('betting_amount', 'total_bet')
				->select_sum('loss_amount', 'total_loss')
				->select_sum('win_amount', 'total_win')
				->select('player_id')
				->from('total_player_game_day')
				->group_by('player_id')
				->where("player_id", $player_id)
		;

		if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
			$fromDateMinuteStr = $this->utils->formatDateForMysql(new DateTime($dateTimeFrom));
			$toDateMinuteStr = $this->utils->formatDateForMysql(new DateTime($dateTimeTo));

			$this->db->where('date >=', $fromDateMinuteStr);
			$this->db->where('date <=', $toDateMinuteStr);
		}

		$rows = $this->runOneRowArray();

		if(empty($rows)) {
			$rows['total_bet'] = 0;
			$rows['total_loss'] = 0;
			$rows['total_win'] = 0;
		}

		return $rows;
	}

	public function queryBetPlayerUsername($startDay, $game_platform_id){

		//query from total_player_game_day
		$this->db->distinct()->select('player.username')->from('total_player_game_day')
			->join('player', 'player.playerId=total_player_game_day.player_id')
		    ->where('game_platform_id', $game_platform_id)
		    ->where('date >=', $startDay)
		    ;

		return $this->runMultipleRowArrayUnbuffered();

	}

	/**
	 * Return [ date, total_bet, payout ] tuples of given date interval
	 *
	 * @param	datestring	$date_from	Start of date interval
	 * @param	datestring	$date_to	End of date interval
	 * @param	bool		$return_grand_sum	Return by-day aggregation if false
	 *                                (default) or grandtotal if true
	 * @return	array 		array of [ date, total_bet, payout ]
	 */
	public function get_bet_payout_by_date_interval($date_from, $date_to, $return_grand_sum = false) {
		$this->db->from($this->tableName)
			->select('date')
			->select('SUM(betting_amount) AS amount_bet', false)
			->select('SUM(loss_amount - win_amount) AS gross_profit', false)
			->where("date BETWEEN '$date_from' AND '$date_to'", null, false)
		;

		if (!$return_grand_sum) {
			$this->db->group_by('date');

		}
		// else {
		// 	$res = $this->runOneRowArray();
		// 	$this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());
		// 	return $res;
		// }
		$res = $this->runMultipleRowArray();

		// $this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());
		return $res;
	}

}

///END OF FILE///////
