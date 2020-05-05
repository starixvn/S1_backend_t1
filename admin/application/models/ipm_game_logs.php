<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ipm_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "ipm_game_logs";

	// public function insertIpmGameLogs($data) {
	// 	return $this->db->insert($this->tableName, $data);
	// }

	// public function getRecord($uniqueid){
	// 	$this->db->select('id')->from($this->tableName)->where('uniqueid', $uniqueid);
	// 	return $this->runOneRowOneField('id');
	// }

	// public function syncToIpmGameLogs($data) {
	// 	$id=$this->getRecordId($data['uniqueid']);
	// 	if(!empty($id)){
	// 		$this->db->set($data);
	// 		return $this->runAnyUpdate($this->tableName);
	// 		// return $this->db->update($this->tableName, $data);
	// 	}else{
	// 		return $this->insertData($this->tableName, $data);
	// 		// return $this->db->insert($this->tableName, $data);
	// 	}
	// }

	// public function isUniqueIdAlreadyExists($uniqueId) {
	// 	$qry = $this->db->get_where($this->tableName, array('uniqueId' => $uniqueId));
	// 	if ($this->getOneRow($qry) == null) {
	// 		return false;
	// 	} else {
	// 		return true;
	// 	}
	// }

	public function getGameLogStatistics($dateFrom, $dateTo) {

		$sql = <<<EOD
SELECT
ipm.external_uniqueid,
SUBSTRING_INDEX(ipm.memberCode, '_', -1) as username,
ipm.gameshortcode,
ipm.response_result_id,
ipm.betTime as start_at,
ipm.betTime as end_at,
ipm.betAmt as bet,
ipm.result,
ipm.matchId,
ipm.oddsType,
ipm.odds,
ipm.sportsName as game,
ipm.BTStatus as BTStatus,
ipm.BTBuyBack,
ipm.betId,
ipm.settled,
ipm.betCancelled, 
game_provider_auth.player_id,
gd.id as game_description_id,
IF(ipm.ParlayBetDetails IS NOT NULL, 'parlay', ipm.sportsName) as game_code,
gd.game_type_id,
gd.void_bet as void_bet
FROM
ipm_game_logs as ipm
LEFT JOIN
game_description as gd ON gd.game_code = ipm.sportsName and gd.void_bet!=1 and gd.game_platform_id = ?
JOIN
game_provider_auth ON SUBSTRING_INDEX(ipm.memberCode, '_', -1) = game_provider_auth.login_name COLLATE utf8_unicode_ci AND game_provider_auth.game_provider_id = ?
WHERE
ipm.betTime >= ? AND ipm.betTime <= ?
and BTStatus in (?, ?, ?)
EOD;

		$query = $this->db->query($sql, array(
			SPORTSBOOK_API,
			SPORTSBOOK_API,
			$dateFrom,
			$dateTo,
			'Settled','Accepted','Pending'
		));

		return $this->getMultipleRow($query) ? : array();
	}

	public function importRaw($startDateStr, $endDateStr){
		$row=0;
		$this->db->from('ipm_raw_game_logs')->where('bet_time >=', $startDateStr)
			->where('bet_time <=', $endDateStr)->where('`Main Ticket`="True"', null, false);

		$rows=$this->runMultipleRowArray();
		if(!empty($rows)){
			$modify='+12 hours';
			foreach ($rows as $row) {
				$bet_no=$row['Bet No'];
				//sync by bet_no
				$this->db->select('id')->from('ipm_game_logs')->where('betId', $bet_no);
				$id=$this->runOneRowOneField('id');
				if(empty($id)){
					//insert
					$data=[
						'betId'=>$row['Bet No'],
					    'betTime'=>$this->utils->modifyDateTime($row['bet_time'], $modify) ,
					    'memberCode'=>$row['Member Code'],
					    'sportsName'=>$row['Sport Name'],
					    'matchID'=>$row['Match No'],
					    'leagueName'=>$row['League Name'],
					    'homeTeam'=>$row['Team Name Home'],
					    'awayTeam'=>$row['Team Name Away'],
					    // 'favouriteTeamFlag'=>$row['Stake Bet On'],
					    'betType'=>$row['Stake Type'],
					    'selection'=>$row['Stake Bet On'],
					    // 'handicap'=>$row[''],
					    'oddsType'=>$row['Odds Type'],
					    'odds'=>$row['Stake Odds'],
					    'currency'=>'RMB',
					    'betAmt'=>$row['Bet Amt ActualF'],
					    'result'=>$row['Stake Return AmtF'],
					    'HTHomeScore'=>$row['ScoreHome1stHalf'],
					    'HTAwayScore'=>$row['ScoreAway1stHalf'],
					    'FTHomeScore'=>$row['Score Home'],
					    'FTAwayScore'=>$row['Score Away'],
					    'BetHomeScore'=>$row['Bet Score Home'],
					    'BetAwayScore'=>$row['Bet Score Away'],
					    'settled'=>'1',// $row[''],
					    'betCancelled'=>$row['BTCancel'],
					    'bettingMethod'=>$row['Bet Type'],
					    'BTStatus'=>$row['BTPaid']=='1' ? 'Settled' : 'Pending',
					    // 'BTComission'=>$row[''],
					    'uniqueid'=>$row['Bet No'],
					    'external_uniqueid'=>$row['Bet No'],
					    'gameshortcode'=>$row['Sport Name'].$row['Stake Type'],
					    'BTBuyBack'=>$row['BTPaidAmount'] > 0 ? $row['BTPaidAmount'] : null,
					    // 'ParlayBetDetails'=>$row[''],
					];

					$this->insertData('ipm_game_logs', $data);
				}else{
					$data=[
					    'betAmt'=>$row['Bet Amt ActualF'],
					    'result'=>$row['Stake Return AmtF'],
					    'HTHomeScore'=>$row['ScoreHome1stHalf'],
					    'HTAwayScore'=>$row['ScoreAway1stHalf'],
					    'FTHomeScore'=>$row['Score Home'],
					    'FTAwayScore'=>$row['Score Away'],
					    'BetHomeScore'=>$row['Bet Score Home'],
					    'BetAwayScore'=>$row['Bet Score Away'],
					    'betCancelled'=>$row['BTCancel'],
					    'BTStatus'=>$row['BTPaid']=='1' ? 'Settled' : 'Pending',
					    'BTBuyBack'=>$row['BTPaidAmount'] > 0 ? $row['BTPaidAmount'] : null,
					];
					//update
					$this->db->where('id', $id)
						->set($data);
					$this->runAnyUpdate('ipm_game_logs');
				}
				$row++;
			}
		}

		return $row;
	}

	// public function getAvailableRows($rows) {
	// 	$this->db->select('uniqueid')->from($this->tableName)->where_in('uniqueid', array_column($rows, 'uniqueid'));
	// 	$existsRow = $this->runMultipleRowArray();
	// 	$availableRows = null;
	// 	if (!empty($existsRow)) {
	// 		$existsId = array_column($existsRow, 'uniqueid');
	// 		$availableRows = array();
	// 		foreach ($rows as $row) {
	// 			$uniqueId = $row['uniqueid'];
	// 			if (!in_array($uniqueId, $existsId)) {
	// 				$availableRows[] = $row;
	// 			}
	// 		}
	// 	} else {
	// 		$availableRows = $rows;
	// 	}
	// 	return $availableRows;
	// }

}

///END OF FILE///////