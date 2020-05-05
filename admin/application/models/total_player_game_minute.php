<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Total_player_game_minute
 *
 * General behaviors include :
 *
 * * Get last sync hour
 * * Sync total player game per minute
 * * Get first/last record
 * * Get total bets win and loss by player
 * * Get player game summary
 *
 * @category Game Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Total_player_game_minute extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "total_player_game_minute";

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
	 * overview : sync to total player game minute
	 *
	 * @param string $data
	 * @return array
	 */
	function syncToTotalPlayerGameMinute($data) {
		if ($this->isUniqueIdAlreadyExists($data['uniqueid'])) {
			$this->db->where('uniqueid', $data['uniqueid']);
			return $this->db->update($this->tableName, $data);
		} else {
			return $this->db->insert($this->tableName, $data);
		}
	}

	/**
	 * overview : get all record per minute of all player
	 *
	 * @param datetime $dateFrom
	 * @param datetime $dateTo
	 * @return boolean
	 */
	function getAllRecordPerDayOfAllPlayer($dateFrom, $dateTo) {
		$this->db->select("player_id, game_platform_id, game_type_id, game_description_id, date,sum(betting_amount) as betting_amount,sum(result_amount) as result_amount", false);
		$this->db->where("date >=", $dateFrom);
		$this->db->where("date <=", $dateTo);
		$this->db->group_by(array("player_id", "game_platform_id", "game_type_id", "game_description_id", "date"));
		$qry = $this->db->get($this->tableName);
		return $this->getMultipleRow($qry);
	}

	/**
	 * overview : get all record per day of player
	 *
	 * @param datetime	$dateFrom
	 * @param datetime	$dateTo
	 * @param int		$playerId
	 * @return array
	 */
	function getAllRecordPerDayOfPlayer($dateFrom, $dateTo, $playerId) {
		$this->db->select("player_id, game_platform_id, game_type_id, game_description_id, date,sum(betting_amount) as betting_amount,sum(result_amount) as result_amount", false);
		$this->db->where("date >=", $dateFrom);
		$this->db->where("date <=", $dateTo);
		$this->db->where("player_id", $playerId);
		$this->db->group_by(array("player_id", "game_platform_id", "game_type_id", "game_description_id", "date"));
		$qry = $this->db->get($this->tableName);
		return $this->getMultipleRow($qry);
	}

	/**
	 * overview : get operator record per minute
	 *
	 * @param datetime	$dateFrom
	 * @param datetime	$dateTo
	 * @return null
	 */
	public function getOperatorRecordPerMinute($dateFrom, $dateTo) {
		$this->db->select("game_platform_id, game_type_id, game_description_id, date,hour,minute,sum(betting_amount) as betting_amount,sum(result_amount) as result_amount", false);
		//$this->db->where("concat(date,' ',lpad(hour,2,'0')) >=", $dateFrom);
		//$this->db->where("concat(date,' ',lpad(hour,2,'0')) <=", $dateTo);
		$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateFrom));
		$toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTo));

		$this->db->where('date_minute >=', $fromDateMinuteStr);
		$this->db->where('date_minute <=', $toDateMinuteStr);
		$this->db->group_by(array("game_platform_id", "game_type_id", "game_description_id", "date", "hour", "minute"));
		$qry = $this->db->get($this->tableName);

		// $this->utils->debug_log($this->db->last_query());
		return $this->getMultipleRow($qry);
	}

	/**
	 * overview : get first record date time
	 *
	 * @return datetime
	 */
	public function getFirstRecordDateTime() {
		$this->db->order_by('date_minute asc');
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'date');
	}

	/**
	 * overview : get last record date time
	 *
	 * @return datetime
	 */
	public function getLastRecordDateTime() {
		$this->db->order_by('date_minute desc');
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'date');
	}

	/**
	 * overview : get player total bets
	 * @param int		$playerId
	 * @param datetime	$gameRecordStartDate
	 * @param datetime	$gameRecordEndDate
	 * @param array $gameList
	 * @return array
	 */
	public function getPlayerTotalBet($playerId, $gameRecordStartDate, $gameRecordEndDate, $gameList = null) {
		//var_dump($playerName);exit();
		$this->db->select('sum(betting_amount) as currentTotalBet')->from($this->tableName);
		$this->db->where('player_id', $playerId);
		//$this->db->where('CONCAT(date," ",hour,":00:00") >=', $gameRecordStartDate);
		//$this->db->where('CONCAT(date," ",hour,":59:59") <=', $gameRecordEndDate);
		$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($gameRecordStartDate));
		$toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($gameRecordEndDate));

		$this->db->where('date_minute >=', $fromDateMinuteStr);
		$this->db->where('date_minute <=', $toDateMinuteStr);

		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * overview : get player total loss
	 *
	 * @param int		$playerId
	 * @param datetime	$gameRecordStartDate
	 * @param datetime	$gameRecordEndDate
	 * @param array		$gameList
	 * @return array
	 */
	public function getPlayerTotalLoss($playerId, $gameRecordStartDate, $gameRecordEndDate, $gameList = null) {
		//var_dump($playerName);exit();
		$this->db->select('sum(if(betting_amount<0, betting_amount, 0)) as currentTotalLoss', false)->from($this->tableName);
		$this->db->where('player_id', $playerId);
		//$this->db->where('CONCAT(date," ",hour,":00:00") >=', $gameRecordStartDate);
		//$this->db->where('CONCAT(date," ",hour,":59:59") <=', $gameRecordEndDate);
		$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($gameRecordStartDate));
		$toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($gameRecordEndDate));

		$this->db->where('date_minute >=', $fromDateMinuteStr);
		$this->db->where('date_minute <=', $toDateMinuteStr);

		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * overview : get player total win
	 *
	 * @param int		$playerId
	 * @param datetime	$gameRecordStartDate
	 * @param datetime	$gameRecordEndDate
	 * @param array		$gameList
	 * @return array
	 */
	public function getPlayerTotalWin($playerId, $gameRecordStartDate, $gameRecordEndDate, $gameList = null) {
		//var_dump($playerName);exit();
		$this->db->select('sum(if(betting_amount>0, betting_amount, 0)) as currentTotalWin', false)->from($this->tableName);
		$this->db->where('player_id', $playerId);
		//$this->db->where('CONCAT(date," ",hour,":00:00") >=', $gameRecordStartDate);
		//$this->db->where('CONCAT(date," ",hour,":59:59") <=', $gameRecordEndDate);
		$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($gameRecordStartDate));
		$toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($gameRecordEndDate));

		$this->db->where('date_minute >=', $fromDateMinuteStr);
		$this->db->where('date_minute <=', $toDateMinuteStr);

		$query = $this->db->get();
		return $query->row_array();
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

		$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql($from);
		$toDateMinuteStr = $this->utils->formatDateMinuteForMysql($to);
		$fromStr = $from->format('Y-m-d H:i') . ':00';
		$toStr = $to->format('Y-m-d H:i') . ':59';

		$playerIdSql = null;
		if (!empty($playerId)) {
			$this->db->where('player_id', $playerId);
			$playerIdSql = ' and player_id=' . $this->db->escape($playerId);
		}

		$this->db->where('date_minute >=', $fromDateMinuteStr)
			->where('date_minute <=', $toDateMinuteStr);
		$this->db->delete('total_player_game_minute');

		$now=$this->utils->getNowForMysql();
		// $this->utils->printLastSQL();
		$t=time();
		$params=array($fromStr, $toStr);
		$sql = <<<EOD
insert into total_player_game_minute(
player_id, betting_amount, real_betting_amount, result_amount, win_amount, loss_amount, minute,
hour, date, updated_at, bet_for_cashback ,
date_minute, game_platform_id, game_type_id, game_description_id,
uniqueid
)

select
player_id, round(sum(bet_amount), 4) as betting_amount, round(sum(real_betting_amount), 4) as real_betting_amount, round(sum(result_amount), 4) as result_amount,
round(sum(win_amount), 4) as win_amount, round(sum(loss_amount), 4) as loss_amount, DATE_FORMAT(end_at,  '%i'),
DATE_FORMAT(end_at,'%H') as hour, DATE_FORMAT(end_at,'%Y-%m-%d') as date, '$now' as updated_at, round(sum(bet_for_cashback), 4),
DATE_FORMAT(end_at,'%Y%m%d%H%i'), game_platform_id, game_type_id, game_description_id,
concat(player_id, '_', game_platform_id, '_', game_type_id, '_', game_description_id, '_',DATE_FORMAT(end_at,'%Y-%m-%d'), '_', DATE_FORMAT(end_at,'%H:%i'))

from game_logs
where end_at >= ?
and end_at <= ?
and flag=1
and !(bet_amount=0 and result_amount=0)
{$playerIdSql}
group by player_id, game_platform_id, game_type_id, game_description_id, DATE_FORMAT(end_at,'%Y%m%d%H%i')
EOD;

		$qry = $this->db->query($sql, $params);

		// $this->utils->printLastSQL();

		$cnt=$this->db->affected_rows();
		$this->utils->info_log('insert into total_player_game_minute', $cnt, $params, 'cost', (time()-$t));
		return $cnt;
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
	public function getTotalBetsWinsLossByPlayers($playerIds, $dateTimeFrom, $dateTimeTo, $gamePlatformId = null, $promorulesId=null) {
		$totalBet = 0;
		$totalWin = 0;
		$totalLoss = 0;

		if (!empty($playerIds)) {

			$this->db->select_sum('betting_amount', 'total_bet')
				->select_sum('loss_amount', 'total_loss')
				->select_sum('win_amount', 'total_win')
				->from('total_player_game_minute')
			;

			if (is_array($playerIds)) {
				$this->db->where_in('player_id', $playerIds);
			} else {
				$this->db->where('player_id', $playerIds);
			}

			if (!empty($gamePlatformId)) {
				if (is_array($gamePlatformId)) {
					$this->db->where_in('game_platform_id', $gamePlatformId);
				} else {
					$this->db->where('game_platform_id', $gamePlatformId);
				}
			}
			if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
				$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeFrom));
				$toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeTo));

				$this->db->where('date_minute >=', $fromDateMinuteStr);
				$this->db->where('date_minute <=', $toDateMinuteStr);
			}
			if(!empty($promorulesId)){
				//FIXME add limit
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
	 * overview : get total bets wins loss by agents
	 *
	 * @param array		$agentIds
	 * @param datetime	$dateTimeFrom
	 * @param datetime	$dateTimeTo
	 * @param int		$gamePlatformId
	 * @param int		$promorulesId
	 * @return array
	 */
	public function getTotalBetsWinsLossByAgents($agentIds, $dateTimeFrom, $dateTimeTo, $gamePlatformId = null, $promorulesId=null) {

		$db=$this->getReadOnlyDB();

		$totalBet = 0;
		$totalWin = 0;
		$totalLoss = 0;

		if (!empty($agentIds)) {

			$db->select_sum('betting_amount', 'total_bet')
				->select_sum('loss_amount', 'total_loss')
				->select_sum('win_amount', 'total_win')
				->from('total_player_game_minute')
				->join('player', 'player.playerId = total_player_game_minute.player_id')
			;

			if (is_array($agentIds)) {
				$db->where_in('agent_id', $agentIds);
			} else {
				$db->where('agent_id', $agentIds);
			}

			if (!empty($gamePlatformId)) {
				if (is_array($gamePlatformId)) {
					$db->where_in('game_platform_id', $gamePlatformId);
				} else {
					$db->where('game_platform_id', $gamePlatformId);
				}
			}
			if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
				$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeFrom));
				$toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeTo));

				$db->where('date_minute >=', $fromDateMinuteStr);
				$db->where('date_minute <=', $toDateMinuteStr);
			}
			if(!empty($promorulesId)){
				//add limit
			}

			$row = $this->runOneRow($db);
			$totalBet = $row->total_bet;
			$totalWin = $row->total_win;
			$totalLoss = $row->total_loss;
			$this->utils->debug_log($db->last_query());
			// $this->utils->printLastSQL();
		}
		return array($totalBet, $totalWin, $totalLoss);
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
	 * overview : sum amount
	 *
	 * @param  datetime	$datehour
	 * @return array
	 */
	public function sumAmount($dateminute) {
		$this->db->select_sum('betting_amount')->select_sum('result_amount')
			->select_sum('loss_amount')->select_sum('win_amount')
			->from($this->tableName)
			->where('date_minute', $dateminute);

		$row = $this->runOneRow();
		$bet_amount = 0;
		$result_amount = 0;
		$win_amount = 0;
		$loss_amount = 0;
		if (!empty($row)) {
			$bet_amount = $row->betting_amount;
			$result_amount = $row->result_amount;
			$win_amount = $row->win_amount;
			$loss_amount = $row->loss_amount;
		}
		return array(floatval($bet_amount), floatval($result_amount), floatval($win_amount), floatval($loss_amount));
	}

	/**
	 * overview : get top ten win players
	 *
	 * @param int	$gameDescriptionId
	 * @return array
	 */
	public function getTopTenWinPlayers($gameDescriptionId) {
		$this->load->model(array('player_model', 'game_description_model'));
		$dateFrom = new DateTime('7 days ago');
		$dateTimeFrom = $dateFrom->format('YmdHi');
		$dateTo = new DateTime();
		$dateTimeTo = $dateTo->format('YmdHi');
		$min_win_amount_for_top10 = $this->utils->getConfig('min_win_amount_for_top10');

		$this->db->select('player_id, game_description_id');
		$this->db->select_sum('win_amount', 'total_win_amount');
		$this->db->from('total_player_game_minute');
		$this->db->join('game_description', 'game_description.id = total_player_game_minute.game_description_id');
		$this->db->join('game_type', 'game_type.id = total_player_game_minute.game_type_id');
		$this->db->group_by(array('player_id', 'game_description_id'));
		$this->db->having('total_win_amount >=', $min_win_amount_for_top10);
		$this->db->order_by('total_win_amount', 'desc');
		$this->db->limit(10);

		# OG-1578 hide unknown game in get_top_ten_win_players
		$this->db->where('game_description.game_code !=', 'unknown');
		$this->db->where('game_type.game_type !=', 'unknown');

		if (!empty($gameDescriptionId)) {
			$this->db->where('game_description_id', $gameDescriptionId);
		}

		if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
			$this->db->where('date_minute >=', $dateTimeFrom);
			$this->db->where('date_minute <=', $dateTimeTo);
		}

		$qry = $this->db->get();

		// 		$sql = <<<EOD
		// SELECT fq.`game_description_id`, MAX(CONCAT(LPAD(fq.total_win_amount,5,'0'),'_',fq.username)) as username FROM (SELECT `player_id` AS username, `game_description_id`, SUM(`win_amount`) AS total_win_amount
		// FROM (`total_player_game_minute`)
		// WHERE win_amount IS NOT NULL
		// AND date_minute >= {$dateTimeFrom}
		// AND date_minute <= {$dateTimeTo}
		// and
		// GROUP BY `player_id`, `game_description_id`)
		//  fq
		// GROUP BY `game_description_id`
		// ORDER BY `total_win_amount` desc
		// EOD;
		// 		$qry = $this->db->query($sql);
		// $cnt = 0;
		$data = array();
		if ($qry && $qry->num_rows() > 0) {
			foreach ($qry->result_array() as $row) {
				$row['total_win_amount'] = floatval($row['total_win_amount']);
				$row['username'] = $this->player_model->getUsernameById($row['player_id']);
				$gameDesc = $this->game_description_model->getGameDescription($row['game_description_id']);
				$row['game_name'] = '';
				$row['game_code'] = '';
				if (!empty($gameDesc)) {
					$row['game_name'] = $this->game_description_model->getGameName($gameDesc);
					$row['game_code'] = $gameDesc->game_code;
				}
				// if ($row['game_code'] != 'unknown') {
				// 	if ($cnt < 10) {
				$data[] = $row;
				// 	} else {
				// 		break;
				// 	}
				// 	$cnt++;
				// }
			}
		}
		return $data;
	}

	/**
	 * overview : get newest ten win players
	 *
	 * @param int	$gameDescriptionId
	 * @return array
	 */
	public function getNewestTenWinPlayers($gameDescriptionId) {
		$min_win_amount = $this->utils->getConfig('min_win_amount_for_newest');

		$this->load->model('player_model');
		$this->db->select('player_id as username,result_amount');
		$this->db->where('result_amount >', $min_win_amount);
		if (!empty($gameDescriptionId)) {
			$this->db->where('game_description_id', $gameDescriptionId);
		}
		// $this->db->where('flag', self::FLAG_GAME);
		$this->db->order_by('date_minute', 'desc');
		$this->db->limit(10);

		$qry = $this->db->get($this->tableName);

		$data = array();
		if ($qry && $qry->num_rows() > 0) {
			foreach ($qry->result_array() as $row) {
				$row['username'] = $this->player_model->getUsernameById($row['player_id']);
				$data[] = $row;
			}
			return $data;
		}
	}

	/**
	 * overview : get result by players
	 *
	 * @param int		$playerIds
	 * @param datetime	$dateTimeFrom
	 * @param datetime	$dateTimeTo
	 * @param int		$gamePlatformId
	 * @return int
	 */
	public function getResultByPlayers($playerIds, $dateTimeFrom, $dateTimeTo, $gamePlatformId = null, $gameTypeId = null) {
		$totalResult = 0;

		if (!empty($playerIds)) {

			$this->db->select_sum('result_amount', 'total_result')
				->from('total_player_game_minute');

			if (is_array($playerIds)) {
				$this->db->where_in('player_id', $playerIds);
			} else {
				$this->db->where('player_id', $playerIds);
			}

			if (!empty($gamePlatformId)) {
				if (is_array($gamePlatformId)) {
					$this->db->where_in('game_platform_id', $gamePlatformId);
				} else {
					$this->db->where('game_platform_id', $gamePlatformId);
				}
			}

            if (!empty($gameTypeId)) {
                if (is_array($gameTypeId)) {
                    $this->db->where_in('game_type_id', $gameTypeId);
                } else {
                    $this->db->where('game_type_id', $gameTypeId);
                }
            }

			if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
				$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeFrom));
				$toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeTo));

				$this->db->where('date_minute >=', $fromDateMinuteStr);
				$this->db->where('date_minute <=', $toDateMinuteStr);
			}

			$row = $this->runOneRow();
			$totalResult = $row->total_result;
			// $this->utils->printLastSQL();
		}
		return $totalResult;
	}

	/**
	 * overview : total player betting amount
	 *
	 * @param int		$playerId
	 * @param datetime	$dateTimeFrom
	 * @param datetime	$dateTimeTo
	 * @param int		$promoId
	 * @return array
	 */
	public function totalPlayerBettingAmount($playerId, $dateTimeFrom, $dateTimeTo = null, $promoId = null) {

		$playerGames = null;
		if ($promoId) {
			$this->load->model(array('promorules'));
			$playerGames = $this->promorules->getPlayerGames($promoId);
		}

		$this->db->select_sum('betting_amount', 'totalBetAmount')
			->from('total_player_game_minute')
			->where('player_id', $playerId);

		if (empty($dateTimeTo)) {
			$dateTimeTo = $this->utils->getNowForMysql();
		}
		$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeFrom));
		$toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeTo));

		$this->db->where('date_minute >=', $fromDateMinuteStr)->where('date_minute <=', $toDateMinuteStr);

		if ($promoId) {
			// $playerGames = $this->getPlayerGames($promoId);
			if (!empty($playerGames)) {
				$this->db->where_in('game_description_id', $playerGames);
			}
		}

		$qry = $this->db->get();
		$rows = array(array('totalBetAmount' => 0));
		if ($qry->num_rows() > 0) {
			$rows = $qry->result_array();
		}

		return $rows;

	}

	/**
	 * overview : total player betting amount with limit by vip
	 *
	 * @param int		$playerId
	 * @param datetime	$dateTimeFrom
	 * @param datetime	$dateTimeTo
	 * @param array		$gameDescIdArr
	 * @return array
	 */
	public function totalPlayerBettingAmountWithLimitByVIP($playerId, $dateTimeFrom, $dateTimeTo = null, $gameDescIdArr = null) {

		// $playerGames = null;
		// if ($promoId) {
		// 	$this->load->model(array('promorules'));
		// 	$playerGames = $this->promorules->getPlayerGames($promoId);
		// }

		$this->db->select_sum('bet_for_cashback', 'totalBetAmount')
			->from('total_player_game_minute')
			->where('player_id', $playerId);

		if (empty($dateTimeTo)) {
			$dateTimeTo = $this->utils->getNowForMysql();
		}
		$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeFrom));
		$toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeTo));

		$this->db->where('date_minute >=', $fromDateMinuteStr)->where('date_minute <=', $toDateMinuteStr);

		if (!empty($gameDescIdArr)) {
			$this->db->where_in('game_description_id', $gameDescIdArr);
		}

		$qry = $this->db->get();

		$this->utils->debug_log('totalPlayerBettingAmountWithLimitByVIP', $playerId);
		// $this->utils->printLastSQL();

		// $rows = array(array('totalBetAmount' => 0));
		// if ($qry->num_rows() > 0) {
		// 	$rows = $qry->result_array();
		// }

		// return $rows;

		$betting_amount=0;
		// $rows = array(array('totalBetAmount' => 0));
		if ($qry->num_rows() > 0) {
			// $rows = $qry->result_array();
			$row=$qry->row_array();
			$betting_amount=$row['totalBetAmount'];
		}

		// return $rows;
		return $betting_amount;

	}

    public function getPlayerTotalGameLogsByDate($playerId, $dateTimeFrom, $dateTimeTo = null, $gameDescIdArr = null) {
        $result = [];
        $betting_amount = 0;
        $date_minute = null;

        if(empty($playerId)){
            return false;
        }

        $this->db->select('*')
            ->from('total_player_game_minute')
            ->where('player_id', $playerId);

        if (empty($dateTimeTo)) {
            $dateTimeTo = $this->utils->getNowForMysql();
        }
        $fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeFrom));
        $toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeTo));

        $this->db->where('date_minute >=', $fromDateMinuteStr)->where('date_minute <=', $toDateMinuteStr);

        if (!empty($gameDescIdArr)) {
            $this->db->where_in('game_description_id', $gameDescIdArr);
        }
        $this->db->order_by('date_minute asc');

        $rows = $this->runMultipleRow();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $betting_amount = $row->betting_amount;
                $date_minute = $row->date_minute;
                $result[] = [
                    'game_description_id'=> $row->game_description_id,
                    'betting_amount' => $betting_amount,
                    'date_minute' => $date_minute,
                    'used' => false,
                ];
            }
        }

        return $result;

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
				->from('total_player_game_minute')
				->group_by('game_platform_id')
			;

			if (is_array($playerIds)) {
				$this->db->where_in('player_id', $playerIds);
			} else {
				$this->db->where('player_id', $playerIds);
			}

			if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
				$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeFrom));
				$toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeTo));

				$this->db->where('date_minute >=', $fromDateMinuteStr);
				$this->db->where('date_minute <=', $toDateMinuteStr);
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
	 * overview : sum operators bets wins loss by datetime
	 *
	 * @param datetime	$dateTimeFrom
	 * @param datetime	$dateTimeTo
	 * @param int		$gamePlatformId
	 * @param int		$promoruleId
	 * @param null $db
	 * @return array
	 */
	public function sumOperatorBetsWinsLossByDatetime($dateTimeFrom, $dateTimeTo, $gamePlatformId = null, $promoruleId = null, $db = null) {

		list($totalBet, $totalWin, $totalLoss) = $this->getTotalBetsWinsLoss($dateTimeFrom, $dateTimeTo, $gamePlatformId, $db);

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
	public function getTotalBetsWinsLoss($dateTimeFrom = null, $dateTimeTo = null, $gamePlatformId = null, $db = null) {
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
			->from('total_player_game_minute')
			->join('player', 'player.playerId = total_player_game_minute.player_id')
			->where('player.deleted_at IS NULL')
		;

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
			$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeFrom));
			$toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeTo));

			$db->where('date_minute >=', $fromDateMinuteStr);
			$db->where('date_minute <=', $toDateMinuteStr);
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

	public function getWinners($min_amount, $dateTimeFrom = NULL, $dateTimeTo = NULL) {

		if ($dateTimeFrom) {
			$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeFrom));
			$this->db->where('total_player_game_minute.date_minute >=', $fromDateMinuteStr);
		}

		if ($dateTimeTo) {
			$toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeTo));
			$this->db->where('total_player_game_minute.date_minute <=', $toDateMinuteStr);
		}

		$query = $this->db->select('player.username')
			->select_sum('(win_amount - loss_amount)', 'amount')
			->from('total_player_game_minute')
			->join('player', 'player.playerId = total_player_game_minute.player_id')
			->group_by('total_player_game_minute.player_id')
			->order_by('amount', 'desc')
			->having('amount >=', $min_amount)
			->get();

		return $query->result_array();

	}

	/**
	 * overview : get player current bet
	 *
	 * @param int		$playerId
	 * @param datetime	$dateTimeFrom
	 * @param datetime	$dateTimeTo
	 * @param int		$promoId
	 * @return array
	 */
	public function getPlayerCurrentBetByGamePlatformId($playerId, $gamePlatformId, $dateTimeFrom, $dateTimeTo = null, $promoId = null) {
		$totalBetAmount = 0;
		if ($dateTimeFrom != null) {

			if ($dateTimeTo == null) {
				$dateTimeTo = $this->utils->getNowForMysql();
			}

			$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeFrom));
			$toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeTo));

			$this->db->select('sum(betting_amount) as totalBetAmount', false)
				->from('total_player_game_minute')
				->where('game_platform_id', $gamePlatformId)
				->where('date_minute >=', $fromDateMinuteStr)
				->where('date_minute <=', $toDateMinuteStr)
				->where('player_id', $playerId)
			;

			if ($promoId) {
				$playerGames = $this->getPlayerGames($promoId);
				$this->db->where_in('game_description_id', $playerGames);
			}

			$totalBetAmount = $this->runOneRowOneField('totalBetAmount');

			// $this->utils->printLastSQL();

		}
		return $totalBetAmount;

		// $qry = $this->db->get();
		// $rows = array(array('totalBetAmount' => 0));
		// if ($qry->num_rows() > 0) {
		// 	$rows = $qry->result_array();
		// }

		// return $rows;
	}

	/**
	 * overview : get player current bet
	 *
	 * @param int		$playerId
	 * @param datetime	$dateTimeFrom
	 * @param datetime	$dateTimeTo
	 * @param int		$promoId
	 * @return array
	 */
	public function getPlayerCurrentBet($playerId, $dateTimeFrom, $dateTimeTo = null, $promoId = null) {
		$totalBetAmount = 0;
		if ($dateTimeFrom != null) {

			if ($dateTimeTo == null) {
				$dateTimeTo = $this->utils->getNowForMysql();
			}

			$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeFrom));
			$toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeTo));

			$this->db->select('sum(betting_amount) as totalBetAmount', false)
				->from('total_player_game_minute')
				->where('date_minute >=', $fromDateMinuteStr)
				->where('date_minute <=', $toDateMinuteStr)
				->where('player_id', $playerId)
			;

			if ($promoId) {
				$playerGames = $this->getPlayerGames($promoId);
				$this->db->where_in('game_description_id', $playerGames);
			}

			$totalBetAmount = $this->runOneRowOneField('totalBetAmount');

			// $this->utils->printLastSQL();

		}

		return $totalBetAmount;

		// $qry = $this->db->get();
		// $rows = array(array('totalBetAmount' => 0));
		// if ($qry->num_rows() > 0) {
		// 	$rows = $qry->result_array();
		// }

		// return $rows;
	}

	public function getPlayerCurrentBetByPlatform($playerId, $dateTimeFrom, $dateTimeTo = null, $promoId = null, $game_platform = null){
        $totalBetAmount = 0;
        if ($dateTimeFrom != null) {

            if ($dateTimeTo == null) {
                $dateTimeTo = $this->utils->getNowForMysql();
            }

            $fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeFrom));
            $toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeTo));

            $this->db->select('sum(betting_amount) as totalBetAmount', false)
                ->from('total_player_game_minute')
                ->where('date_minute >=', $fromDateMinuteStr)
                ->where('date_minute <=', $toDateMinuteStr)
                ->where('player_id', $playerId)
            ;

            if ($promoId) {
                $playerGames = $this->getPlayerGames($promoId);
                $this->db->where_in('game_description_id', $playerGames);
            }
            if ($game_platform) {
                $this->db->where_in('game_platform_id', (is_array($game_platform)) ? implode(',', $game_platform) : $game_platform);
            }

            $totalBetAmount = $this->runOneRowOneField('totalBetAmount');

            // $this->utils->printLastSQL();

        }

        return $totalBetAmount;
    }

	/**
	 * overview : get player current bet
	 *
	 * @param int		$playerId
	 * @param datetime	$dateTimeFrom
	 * @param datetime	$dateTimeTo
	 * @param int		$promoId
	 * @param int		$subWallet
	 * @return array
	 */
	public function sumBettingAmountBySubWallet($playerId, $dateTimeFrom, $dateTimeTo = null, $promoId = null, $subWallet=null) {

		$totalBetAmount = 0;
		if ($dateTimeFrom != null) {

			if ($dateTimeTo == null) {
				$dateTimeTo = $this->utils->getNowForMysql();
			}

			$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeFrom));
			$toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeTo));

			$this->db->select('sum(betting_amount) as totalBetAmount', false)
				->from('total_player_game_minute')
				->where('date_minute >=', $fromDateMinuteStr)
				->where('date_minute <=', $toDateMinuteStr)
				->where('player_id', $playerId)
			;

			if ($promoId) {
				$playerGames = $this->getPlayerGames($promoId);
				$this->db->where_in('game_description_id', $playerGames);
			}

			if($subWallet){
				$this->db->where('game_platform_id', $subWallet);
			}

			$totalBetAmount = $this->runOneRowOneField('totalBetAmount');

			// $this->utils->printLastSQL();

		}
		return $totalBetAmount;

	}

    public function get_player_bet_info($player_id, $start_date = null, $end_date = null) {
		$this->db->select_sum('betting_amount', 'total_bets');
		$this->db->select_sum('real_betting_amount', 'total_real_bets');
		$this->db->select_sum('(CASE WHEN result_amount < 0 THEN betting_amount ELSE 0 END)', 'lost_bets');
		$this->db->select_sum('(CASE WHEN result_amount = 0 THEN betting_amount ELSE 0 END)', 'tie_bets');
		$this->db->select_sum('(CASE WHEN result_amount > 0 THEN betting_amount ELSE 0 END)', 'win_bets');
		$this->db->from('total_player_game_minute');

		$this->db->where('player_id', $player_id);

		if ($start_date) {
			$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($start_date));
			$this->db->where('date_minute >=', $fromDateMinuteStr);
		}
		if ($end_date) {
			$toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($end_date));
			$this->db->where('date_minute <= ', $toDateMinuteStr);
		}

		return $this->runOneRowArray();
		// $query = $this->db->get();
		// $result = $query->result_array();

  //       return $result;
    }


	/**
	 * overview : get total bet win loss of all players
	 *
	 * @param int		$playerIds
	 * @param datetime	$dateTimeFrom
	 * @param datetime	$dateTimeTo
	 * @param int		$gamePlatformId
	 * @return array
	 */
	public function getTotalBetsWinsLossByAllPlayers($dateTimeFrom, $dateTimeTo) {
		$this->db->select_sum('betting_amount', 'total_bet')
				->select_sum('loss_amount', 'total_loss')
				->select_sum('win_amount', 'total_win')
				->select('player_id')
				->from('total_player_game_minute')
				->group_by('player_id');

		if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
			$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeFrom));
			$toDateMinuteStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTimeTo));

			$this->db->where('date_minute >=', $fromDateMinuteStr);
			$this->db->where('date_minute <=', $toDateMinuteStr);
		}

		$rows = $this->runMultipleRowArray();

		return $rows;
	}

	public function getPlayerListLastHour($platformId){
		$dateTimeFrom=new DateTime('-1 hour');
		$dateTimeTo=new DateTime();
		$fromDateMinuteStr = $this->utils->formatDateMinuteForMysql($dateTimeFrom);
		$toDateMinuteStr = $this->utils->formatDateMinuteForMysql($dateTimeTo);

		$this->db->distinct()->select('player_id')->from('total_player_game_minute')
			->where('game_platform_id', $platformId)
			->where('date_minute >=', $fromDateMinuteStr)->where('date_minute <=', $toDateMinuteStr);

		$rows = $this->runMultipleRowArray();
		return empty($rows) ? array() : array_column($rows, 'player_id');
	}

	public function adjust_month_data($adjust_year_month, $adjust_totals){

		$this->load->model(['player_model', 'game_description_model']);

		$year=substr($adjust_year_month, 0, 4);
		$month=substr($adjust_year_month, 4, 2);

		foreach ($adjust_totals as $adjust_info) {
			$playerId=$this->player_model->getPlayerIdByUsername($adjust_info['username']);

			$minute='00';
			$hour='00';
			$day='28';
			$date=$year.'-'.$month.'-'.$day;
			$date_minute=$year.$month.$day.$hour.$minute;

			$game_description_id=$adjust_info['game_description_id'];
			$gameDesc=$this->game_description_model->getGameDescriptionById($game_description_id);
			$game_platform_id=$gameDesc['game_platform_id'];
			$game_type_id=$gameDesc['game_type_id'];

			$uniqueid=$playerId.'_'.$game_platform_id.'_'.$game_type_id.'_'.$game_description_id.'_'.
				$year.'-'.$month.'-'.$day.'_'.$hour.':'.$minute;

			$data=[
				'player_id'=>$playerId,
				'betting_amount'=>$adjust_info['bet'],
				'real_betting_amount'=>$adjust_info['bet'],
				'result_amount'=>$adjust_info['result'],
				'win_amount'=>$adjust_info['result']>0 ? $adjust_info['result'] : 0,
				'loss_amount'=>$adjust_info['result']<0 ? abs($adjust_info['result']) : 0,
				'minute'=>$minute,
				'date'=>$date,
				'updated_at'=>$this->utils->getNowForMysql(),
				'date_minute'=>$date_minute,
				'game_platform_id'=>$game_platform_id,
				'game_type_id'=>$game_type_id,
				'game_description_id'=>$game_description_id,
				'uniqueid'=>$uniqueid,
			];

			$id=$this->insertData('total_player_game_minute', $data);

		}

		return true;

	}

}

///END OF FILE///////