<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Agbbin_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "agbbin_game_logs";

	public function getGameLogStatistics($dateFrom, $dateTo) {
		return $this->getAGBBINGameLogStatistics($dateFrom, $dateTo);
	}

	public function getGameLogStatisticsByIds($ids) {
		return $this->getAGBBINGameLogStatistics(null, null, $ids);
	}

	function getAGBBINGameLogStatistics($dateFrom, $dateTo, $ids=null) {

        //use fields: player_id, playername, start_at, end_at, sceneId, gamecode, billno,
        //result, bet_amount, result_amount, after_balance, external_uniqueid, response_result_id
        //game_code, game_type, game, game_description_id, game_type_id, platformtype, flag

		$sql = <<<EOD
SELECT
AGBBIN.billno,
AGBBIN.billno sceneId,
0 after_balance,
AGBBIN.platformtype,
AGBBIN.datatype,
AGBBIN.result,
AGBBIN.gamecode,
AGBBIN.external_uniqueid,
AGBBIN.uniqueid,
AGBBIN.response_result_id,
AGBBIN.bettime start_at,
AGBBIN.bettime end_at,
AGBBIN.validbetamount AS bet_amount,
AGBBIN.betamount AS real_bet_amount,
AGBBIN.netamount AS result_amount,
AGBBIN.gametype as game_code,
AGBBIN.remark as game_type,
AGBBIN.gametype as game,
AGBBIN.playername AS playername,
AGBBIN.flag as flag,
game_provider_auth.player_id,
GD.game_type_id,
GD.id as game_description_id

FROM agbbin_game_logs AS AGBBIN

LEFT JOIN
game_description as GD
ON AGBBIN.gametype = GD.external_game_id and GD.game_platform_id = ?

JOIN
game_provider_auth
ON
AGBBIN.playername =  game_provider_auth.login_name  AND game_provider_auth.game_provider_id = ?

EOD;


	    $data = array(
	        AGBBIN_API,
	        AGBBIN_API,
	    );

		if(!empty($ids)){

			$idStr=implode(',', $ids);
	        $sql.=' WHERE AGBBIN.id IN ('.$idStr.')';

		}else{

			$sql.=' WHERE AGBBIN.bettime >= ? AND AGBBIN.bettime <= ?';

			$data[]=$dateFrom;
	        $data[]=$dateTo;

		}

		// $this->utils->debug_log($sql, $dateFrom, $dateTo);
		$query = $this->db->query($sql, $data);
		//	echo $this->db->last_query();
		return $this->getMultipleRow($query);
	}


}//end class

///END OF FILE///////
