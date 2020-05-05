<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Agmg_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "agmg_game_logs";
	
	public function getGameLogStatistics($dateFrom, $dateTo) {
		$agmgData = $this->getAGMGGameLogStatistics($dateFrom, $dateTo);
		$agmgData = !empty($agmgData)?$agmgData:[];

		return $agmgData;
	}

	public function getGameLogStatisticsByIds($ids) {
		$agmgData = $this->getAGMGGameLogStatistics(null, null, $ids);
		$agmgData = !empty($agmgData)?$agmgData:[];

		return $agmgData;
	}

	function getAGMGGameLogStatistics($dateFrom, $dateTo, $ids=null) {

        //use fields: player_id, playername, start_at, end_at, sceneId, gamecode, billno,
        //result, bet_amount, result_amount, after_balance, external_uniqueid, response_result_id
        //game_code, game_type, game, game_description_id, game_type_id, platformtype, flag

		$sql = <<<EOD
SELECT
AGMG.billno,
AGMG.billno sceneId,
0 after_balance,
AGMG.platformtype,
AGMG.datatype,
AGMG.result,
AGMG.gamecode,
AGMG.external_uniqueid,
AGMG.uniqueid,
AGMG.response_result_id,
AGMG.bettime start_at,
AGMG.bettime end_at,
AGMG.validbetamount AS bet_amount,
AGMG.betamount AS real_bet_amount,
AGMG.netamount AS result_amount,
AGMG.gametype as game_code,
AGMG.remark as game_type,
AGMG.gametype as game,
AGMG.playername AS playername,
AGMG.flag as flag,
game_provider_auth.player_id,
GD.game_type_id,
GD.id as game_description_id

FROM agmg_game_logs AS AGMG

LEFT JOIN
game_description as GD
ON AGMG.gametype = GD.external_game_id and GD.game_platform_id = ?

JOIN
game_provider_auth
ON
AGMG.playername =  game_provider_auth.login_name  AND game_provider_auth.game_provider_id = ?

EOD;


	    $data = array(
	        AGMG_API,
	        AGMG_API,
	    );

		if(!empty($ids)){

			$idStr=implode(',', $ids);
	        $sql.=' WHERE AGMG.id IN ('.$idStr.')';

		}else{

			$sql.=' WHERE AGMG.bettime >= ? AND AGMG.bettime <= ?';

			$data[]=$dateFrom;
	        $data[]=$dateTo;

		}

		// $this->utils->debug_log($sql, $dateFrom, $dateTo);
		$query = $this->db->query($sql, $data);
		//	echo $this->db->last_query();
		return $this->getMultipleRow($query);
	}


	public function getExistingGameCode($roundIds) {
		if(!empty($roundIds)){
			$this->db->select('id, billno, gamecode, playername')->from($this->tableName)->where_in('gamecode', $roundIds);
			return $this->runMultipleRowArray();
		}

		return [];
    }


}


///END OF FILE///////
