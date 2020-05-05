<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Total_player_game_year
 *
 * General behaviors include :
 *
 * * Get last sync month
 * * Sync total player game per year
 * * Get first/last record
 * * Get total bets win and loss by player
 * * Get player game summary
 *
 * @category Game Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Total_player_game_year extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "total_player_game_year";

	/**
	 * overview : get last sync year
	 *
	 * @return string
	 */
	public function getLastSyncYear() {
		$this->db->order_by('year desc');
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
	 * overview : sync to total player game year
	 *
	 * @param string $data
	 * @return array
	 */
	function syncToTotalPlayerGameYear($data) {
		if ($this->isUniqueIdAlreadyExists($data['uniqueid'])) {
			$this->db->where('uniqueid', $data['uniqueid']);
			return $this->db->update($this->tableName, $data);
		} else {
			return $this->db->insert($this->tableName, $data);

		}
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

		$fromStr = $from->format('Y');
		$toStr = $to->format('Y');

		// $fromStr = $this->utils->formatDateForMysql($from);
		// $toStr = $this->utils->formatDateForMysql($to);
		// $fromDateStr = $this->utils->formatDateForMysql($from);
		// $toDateStr = $this->utils->formatDateForMysql($to);

		$playerIdSql = null;
		if (!empty($playerId)) {
			$this->db->where('player_id', $playerId);
			$playerIdSql = ' and player_id=' . $this->db->escape($playerId);
		}

		$this->db->where('year >=', $fromStr)
			->where('year <=', $toStr);
		$this->db->delete('total_player_game_year');

		$now=$this->utils->getNowForMysql();
		// $this->utils->printLastSQL();
		$params=[$fromStr, $toStr];
// 		$t=time();
// 		$sql = <<<EOD
// select
// player_id, round(sum(betting_amount),4) as betting_amount, round(sum(real_betting_amount),4) as real_betting_amount, round(sum(result_amount),4) as result_amount,
// round(sum(win_amount),4) as win_amount, round(sum(loss_amount),4) as loss_amount,
// substr(convert(month,CHAR),1,4) as year, '$now' as updated_at, round(sum(bet_for_cashback),4) as bet_for_cashback,
// game_platform_id, game_type_id, game_description_id,
// concat(player_id, '_', game_platform_id, '_', game_type_id, '_', game_description_id, '_',substr(convert(month,CHAR),1,4)) as uniqueid

// from total_player_game_month
// where substr(convert(month,CHAR),1,4) >= ?
// and substr(convert(month,CHAR),1,4) <= ?
// {$playerIdSql}
// group by player_id, game_platform_id, game_type_id, game_description_id, substr(convert(month,CHAR),1,4)
// EOD;
// 		$rows=$this->runRawSelectSQLArray($sql, $params);
// 		$this->utils->debug_log('get rows from total_player_game_month', count($rows), 'cost', (time()-$t));
// 		$t=time();
// 		$cnt=0;
// 		$limit=500;
// 		$success=$this->runBatchInsertWithLimit($this->db, 'total_player_game_year', $rows, $limit, $cnt);
// 		unset($rows);
// 		$this->utils->debug_log('insert into total_player_game_year', $cnt, 'cost', (time()-$t));

// 		return $success;

		$t=time();
		$sql = <<<EOD
insert into total_player_game_year(
player_id, betting_amount, real_betting_amount, result_amount, win_amount, loss_amount,
year, updated_at, bet_for_cashback,
game_platform_id, game_type_id, game_description_id,
uniqueid
)

select
player_id, sum(betting_amount) as betting_amount, sum(real_betting_amount) as real_betting_amount, sum(result_amount) as result_amount,
sum(win_amount) as win_amount, sum(loss_amount) as loss_amount,
substr(convert(month,CHAR),1,4), '$now', sum(bet_for_cashback),
game_platform_id, game_type_id, game_description_id,
concat(player_id, '_', game_platform_id, '_', game_type_id, '_', game_description_id, '_',substr(convert(month,CHAR),1,4))

from total_player_game_month
where substr(convert(month,CHAR),1,4) >= ?
and substr(convert(month,CHAR),1,4) <= ?
{$playerIdSql}
group by player_id, game_platform_id, game_type_id, game_description_id, substr(convert(month,CHAR),1,4)
EOD;

		$this->db->query($sql, $params);
		// $this->utils->printLastSQL();
		$cnt=$this->db->affected_rows();
		$this->utils->info_log('insert into total_player_game_year', $cnt, $params, 'cost', (time()-$t));

		return $cnt;

	}

	/**
	 * overview : sum operators bets wins loss by datetime
	 *
	 * @param datetime	$dateTimeFrom
	 * @param datetime	$dateTimeTo
	 * @param int		$gamePlatformId
	 * @param int		$promoruleId
	 * @param null $db
     * @param array $selected_tag
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
     * @param array $selected_tag
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
			->from('total_player_game_year')
			->join('player', 'player.playerId = total_player_game_year.player_id AND player.deleted_at IS NULL');
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
			$fromStr = $this->utils->formatYearForMysql(new DateTime($dateTimeFrom));
			$toStr = $this->utils->formatYearForMysql(new DateTime($dateTimeTo));

			$db->where('year >=', $fromStr);
			$db->where('year <=', $toStr);
		}
		//get one row
        $this->utils->debug_log('the bet win loss ---->', $db->_compile_select());
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