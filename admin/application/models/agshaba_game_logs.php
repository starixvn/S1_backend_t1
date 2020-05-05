<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Agshaba_game_logs extends Base_game_logs_model
{
    public function __construct()
    {
        parent::__construct();
    }

    protected $tableName = 'agshaba_game_logs';

	public function getGameLogStatistics($dateFrom, $dateTo) {
		return $this->getAGSHABAGameLogStatistics($dateFrom, $dateTo);
	}

	public function getGameLogStatisticsByIds($ids) {
		return $this->getAGSHABAGameLogStatistics(null, null, $ids);
	}

    public function getAGSHABAGameLogStatistics($dateFrom, $dateTo, $ids=null)
    {

        //use fields: player_id, playername, start_at, end_at, sceneId, gamecode, billno,
        //result, bet_amount, result_amount, after_balance, external_uniqueid, response_result_id
        //game_code, game_type, game, game_description_id, game_type_id, platformtype, flag

        $sql = <<<EOD
SELECT
AGSHABA.id as id,
AGSHABA.datatype,
AGSHABA.gamecode,
AGSHABA.platformtype,
AGSHABA.result,
AGSHABA.external_uniqueid,
AGSHABA.response_result_id,
AGSHABA.billno,
AGSHABA.billno sceneId,
AGSHABA.bettime as start_at,
AGSHABA.bettime as end_at,
AGSHABA.netamount AS result_amount,
AGSHABA.gamecode as game_code,
AGSHABA.gametype as game_type,
AGSHABA.gametype as game,
AGSHABA.validbetamount AS bet_amount,
AGSHABA.betamount AS real_bet_amount,
AGSHABA.after_amount AS after_balance,
AGSHABA.playername AS playername,
AGSHABA.flag as flag,
AGSHABA.remark as remark,
game_provider_auth.player_id,
GD.game_type_id,
GD.id as game_description_id

FROM agshaba_game_logs AS AGSHABA

LEFT JOIN
game_description as GD
 ON  AGSHABA.gametype  = GD.game_code and GD.game_platform_id = ?

JOIN
game_provider_auth
ON
AGSHABA.playername =  game_provider_auth.login_name  AND game_provider_auth.game_provider_id = ?


EOD;

	    $data = array(
	        AGSHABA_API,
	        AGSHABA_API,
	    );

		if(!empty($ids)){

			$idStr=implode(',', $ids);
	        $sql.=' WHERE AGSHABA.id IN ('.$idStr.')';

		}else{

			$sql.=' WHERE AGSHABA.bettime >= ? AND AGSHABA.bettime <= ?';

			$data[]=$dateFrom;
	        $data[]=$dateTo;

		}

        $query = $this->db->query($sql, $data);

        return $this->getMultipleRow($query);
    }

}

///END OF FILE///////
