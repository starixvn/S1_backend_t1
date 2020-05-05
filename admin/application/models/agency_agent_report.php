<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Agency_agent_report
 *
 * General behaviors include :
 *
 * * Get last sync hour
 * * Sync Agency Agent's Report per hour
 *
 * @category Agency Agent Summary Report
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Agency_agent_report extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "agency_agent_report_hourly";

	/**
	 * overview : sync
	 *
	 * @param DateTime 	$from
	 * @param DateTime 	$to
	 * @param int		$playerId
	 * @return array
	 */
	public function sync(\DateTime $from, \DateTime $to, $playerId = null) {

		$fromDateHourStr = $this->utils->formatDateHourForMysql($from);
		$toDateHourStr = $this->utils->formatDateHourForMysql($to);
		$fromStr = $from->format('Y-m-d H:') . '00:00';
		$toStr = $to->format('Y-m-d H:') . '59:59';

		$playerIdSql = null;
		if (!empty($playerId)) {
			$this->db->where('player_id', $playerId);
			$playerIdSql = ' and player_id=' . $this->db->escape($playerId);
		}

		#Clear Old Records
		$this->db->where('date_hour >=', $fromDateHourStr)
			->where('date_hour <=', $toDateHourStr);
		$this->db->delete($this->tableName);
		// $this->utils->printLastSQL();
		
		$now=$this->utils->getNowForMysql();
		$t=time();
		$transTypeDeposit = (string) Transactions::DEPOSIT;
		$transTypeWithdrawal = (string) Transactions::WITHDRAWAL;
		$transStatusApproved = (string) Transactions::APPROVED;
		$transType = (string) Transactions::PLAYER;
		$sql = <<<EOD
insert into agency_agent_report_hourly(
agent_id, 
agent_name,
currency_key, 
agent_total_deposit, 
agent_total_withdrawal, 
agent_total_bet,
agent_total_bet_for_cashback,
agent_total_real_betting_amount,
agent_total_win, 
agent_total_loss,
agent_net_gaming, 
date_hour,
summary_date,
updated_at,
unique_key
)

SELECT
agents.agent_id,
agents.agent_name,
agents.currency,

(SELECT COALESCE(SUM(transactions.amount),0)
FROM agency_agents as agag
LEFT JOIN player ON player.agent_id = agag.agent_id
LEFT JOIN transactions ON transactions.to_id = player.playerId
WHERE transactions.to_type = {$transType}
AND transactions.transaction_type = {$transTypeDeposit}
AND transactions.status = {$transStatusApproved}
AND agag.agent_id = agents.agent_id
AND transactions.created_at >= "{$fromStr}"
AND transactions.created_at <= "{$toStr}"
) as agent_total_deposit,

(SELECT COALESCE(SUM(transactions.amount),0)
FROM agency_agents as agag
LEFT JOIN player ON player.agent_id = agag.agent_id
LEFT JOIN transactions ON transactions.to_id = player.playerId
WHERE  transactions.to_type = {$transType}
AND transactions.transaction_type = {$transTypeWithdrawal}
AND transactions.status = {$transStatusApproved}
AND agag.agent_id = agents.agent_id
AND transactions.created_at >= "{$fromStr}"
AND transactions.created_at <= "{$toStr}"
) as agent_total_withdrawal,

round(sum(bet_amount), 4) as agent_total_bet,
round(sum(bet_for_cashback), 4) as agent_total_bet_for_cashback,
round(sum(real_betting_amount), 4) as agent_total_real_betting_amount,
round(sum(win_amount), 4) as agent_total_win, 
round(sum(loss_amount), 4) as agent_total_loss,
round(sum(result_amount), 4) as agent_net_gaming,

DATE_FORMAT(end_at,'%Y%m%d%H') as date_hour,
DATE_FORMAT(end_at,'%Y-%m-%d') as summary_date,
'$now' as updated_at,
concat(agents.agent_id ,'_', DATE_FORMAT(end_at,'%Y%m%d%H%i%s')) as unique_key
FROM game_logs
LEFT JOIN player ON player.playerId = game_logs.player_id
JOIN agency_agents as agents ON player.agent_id = agents.agent_id

where end_at >= ?
and end_at <= ?
and flag=1
and !(bet_amount=0 and result_amount=0)
{$playerIdSql}
group by 
agents.agent_id,
DATE_FORMAT(end_at,'%Y%m%d%H')
EOD;

		$params=array($fromStr, $toStr);
		$qry = $this->db->query($sql, $params);
		$cnt=$this->db->affected_rows();
		$this->utils->printLastSQL();

		$cnt=$this->db->affected_rows();
		$this->utils->info_log('insert into agency_agency_reports', $cnt, $params, 'cost', (time()-$t));
		return $cnt;
	}

	/**
	 * overview : get agent's total bets, wins, and loss
	 * 			  get agent's total deposit and withdrawal
	 *
	 * @param array		$agentIds
	 * @param datetime	$dateTimeFrom
	 * @param datetime	$dateTimeTo
	 * @param int		$gamePlatformId
	 * @param int		$promorulesId
	 * @return array
	 */
	public function getAgentsSummaryReport($agentIds, $dateTimeFrom, $dateTimeTo) {
		$db=$this->getReadOnlyDB();
		if (!empty($agentIds))
		{
			$db->select_sum('agent_total_bet', 'total_bet')
				->select_sum('agent_total_loss', 'total_loss')
				->select_sum('agent_total_win', 'total_win')
				->select_sum('agent_net_gaming', 'total_result')
				->select_sum('agent_total_deposit', 'total_deposit')
				->select_sum('agent_total_withdrawal', 'total_withdrawal')
				->from($this->tableName);

			if (is_array($agentIds)){
				$db->where_in('agent_id', $agentIds);
			}else{
				$db->where('agent_id', $agentIds);
			}
			
			if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
				$fromDateHourStr = $this->utils->formatDateHourForMysql(new DateTime($dateTimeFrom));
				$toDateMinuteStr = $this->utils->formatDateHourForMysql(new DateTime($dateTimeTo));
				$db->where('date_hour >=', $fromDateHourStr);
				$db->where('date_hour <=', $toDateMinuteStr);
			}

			$row = $this->runOneRow($db);
			$this->utils->printLastSQL();
		}
		return [
				$row->total_bet ?: 0,
				$row->total_win ?: 0,
				$row->total_loss ?: 0,
				$row->total_deposit ?: 0,
				$row->total_withdrawal ?: 0,
			   ];
	}
}

///END OF FILE///////