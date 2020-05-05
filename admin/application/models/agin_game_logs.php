<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Agin_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "agin_game_logs";
	const BUY_FISH_RECORD = 4;
	const AG_SPORTS_PLATFORM_TYPE = 'SBTA';
	public function getGameLogStatistics($dateFrom, $dateTo) {
		$aginData = $this->getAGINGameLogStatistics($dateFrom, $dateTo);
		$agsportsData = $this->getAGSPORTSGameLogStatistics($dateFrom, $dateTo);
		$aginData = !empty($aginData)?$aginData:[];
		$agsportsData = !empty($agsportsData)?$agsportsData:[];

		return array_merge($aginData,$agsportsData);
	}

	public function getGameLogStatisticsByIds($ids) {
		$aginData = $this->getAGINGameLogStatistics(null, null, $ids);
		$agsportsData = $this->getAGSPORTSGameLogStatistics(null, null, $ids);
		$aginData = !empty($aginData)?$aginData:[];
		$agsportsData = !empty($agsportsData)?$agsportsData:[];

		return array_merge($aginData,$agsportsData);
	}

	function getAGINGameLogStatistics($dateFrom, $dateTo, $ids=null) {
        //use fields: player_id, playername, start_at, end_at, sceneId, gamecode, billno,
        //result, bet_amount, result_amount, after_balance, external_uniqueid, response_result_id
        //game_code, game_type, game, game_description_id, game_type_id, platformtype, flag

		$sql = <<<EOD
SELECT
AGIN.id as id,
AGIN.billno,
AGIN.result,
AGIN.datatype,
AGIN.gamecode,
AGIN.sceneId,
AGIN.platformtype,
AGIN.external_uniqueid,
AGIN.response_result_id,
AGIN.bettime AS start_at,
AGIN.recalcutime,
AGIN.SceneEndTime,
IFNULL(AGIN.recalcutime, AGIN.bettime) AS end_at,
AGIN.validbetamount AS bet_amount,
AGIN.betamount AS real_bet_amount,
AGIN.netamount AS result_amount,
AGIN.gametype AS game_code,
AGIN.platformtype AS game_type,
AGIN.gametype AS game,
AGIN.beforecredit + AGIN.netamount AS after_balance,
AGIN.playername AS playername,
AGIN.flag AS flag,
AGIN.gametype,
AGIN.playtype,
AGIN.remark,
AGIN.extra,
AGIN.transferType,
AGIN.fishIdStart,
AGIN.fishIdEnd,
AGIN.beforecredit,
AGIN.subbillno,

game_provider_auth.player_id,
GD.game_type_id,
GD.id AS game_description_id

FROM agin_game_logs AS AGIN
LEFT JOIN game_description AS GD
ON AGIN.gametype = GD.external_game_id AND GD.game_platform_id = ?
JOIN game_provider_auth
ON AGIN.playername = game_provider_auth.login_name AND game_provider_auth.game_provider_id = ?

EOD;

	    $data = array(
	        AGIN_API,
	        AGIN_API,
	    );

		if(!empty($ids)){

			$idStr=implode(',', $ids);
	        $sql.=' WHERE AGIN.id IN ('.$idStr.')';

		}else{

			$sql.=' WHERE AGIN.bettime >= ? AND AGIN.bettime <= ? ';

			$data[]=$dateFrom;
	        $data[]=$dateTo;

		}

		# Ignore AG SPORTS 
		$sql.= ' AND AGIN.platformtype <> ?';
		$data[]=self::AG_SPORTS_PLATFORM_TYPE;

		$query = $this->db->query($sql,$data);
		return $this->getMultipleRow($query);

	}

	function getAGSPORTSGameLogStatistics($dateFrom, $dateTo, $ids=null) {
        //use fields: player_id, playername, start_at, end_at, sceneId, gamecode, billno,
        //result, bet_amount, result_amount, after_balance, external_uniqueid, response_result_id
        //game_code, game_type, game, game_description_id, game_type_id, platformtype, flag

		$sql = <<<EOD
SELECT
AGIN.id as id,
AGIN.billno,
AGIN.result,
AGIN.datatype,
AGIN.gamecode,
AGIN.sceneId,
AGIN.platformtype,
AGIN.external_uniqueid,
AGIN.response_result_id,
AGIN.bettime AS start_at,
AGIN.recalcutime,
AGIN.SceneEndTime,
IFNULL(AGIN.recalcutime, AGIN.bettime) AS end_at,
AGIN.recalcutime updated_at,
AGIN.validbetamount AS bet_amount,
AGIN.betamount AS real_bet_amount,
AGIN.netamount AS result_amount,
AGIN.gametype AS game_code,
AGIN.platformtype AS game_type,
AGIN.gametype AS game,
AGIN.beforecredit + AGIN.netamount AS after_balance,
AGIN.playername AS playername,
AGIN.flag AS flag,
AGIN.gametype,
AGIN.playtype,
AGIN.remark,
AGIN.extra,
AGIN.transferType,
AGIN.fishIdStart,
AGIN.fishIdEnd,
AGIN.beforecredit,
AGIN.cancelReason,
AGIN.subbillno,

game_provider_auth.player_id,
GD.game_type_id,
GD.id AS game_description_id

FROM agin_game_logs AS AGIN
LEFT JOIN game_description AS GD
ON AGIN.gametype = GD.external_game_id AND GD.game_platform_id = ?
JOIN game_provider_auth
ON AGIN.playername = game_provider_auth.login_name AND game_provider_auth.game_provider_id = ?

EOD;

	    $data = array(
	        AGIN_API,
	        AGIN_API,
	    );

		if(!empty($ids)){

			$idStr=implode(',', $ids);
	        $sql.=' WHERE AGIN.id IN ('.$idStr.')';

		}else{

			$sql.=' WHERE (AGIN.bettime >= ? AND AGIN.bettime <= ?) or (AGIN.recalcutime >= ? AND AGIN.recalcutime <= ?) ';

			$data[]=$dateFrom;
	        $data[]=$dateTo;

			$data[]=$dateFrom;
	        $data[]=$dateTo;

		}

		# only AG SPORTS 
		$sql.= ' AND AGIN.platformtype = ?';
		$data[]=self::AG_SPORTS_PLATFORM_TYPE;

		$query = $this->db->query($sql,$data);
		return $this->getMultipleRow($query);

	}

	public function getExistingGameCode($roundIds) {
		if(!empty($roundIds)){
			$this->db->select('id, billno, gamecode, playername')->from($this->tableName)->where_in('gamecode', $roundIds);
			return $this->runMultipleRowArray();
		}

		return [];
    }

    public function getBuyFishData($fishIdStart,$fishIdEnd){
		$sql = <<<EOD
SELECT
AGIN.transferAmount,
AGIN.transferType,
AGIN.fishIdStart,
AGIN.fishIdEnd
FROM agin_game_logs AS AGIN
WHERE ?
BETWEEN 
AGIN.fishIdStart 
AND
AGIN.fishIdEnd
AND
AGIN.transferType = ?

EOD;
	$data = array(
	        (int)$fishIdStart,
	        self::BUY_FISH_RECORD,
	    );
	$query = $this->db->query($sql,$data);
		return $this->getOneRow($query);
    }


}


///END OF FILE///////
