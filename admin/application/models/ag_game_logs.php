<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

/**
 * Class AG_game_logs.
 *
 * General behaviors include :
 *
 * * Add API game logs
 * * Check if row id already exist
 * * Get last version key
 * * Update game logs
 * * Get game logs statistics
 * * Get available rows
 *
 * @category Game Model
 *
 * @version 1.8.10
 *
 * @copyright 2013-2016 TripleOneTech
 */
class Ag_game_logs extends Base_game_logs_model
{
    public function __construct()
    {
        parent::__construct();
    }

    protected $tableName = 'ag_game_logs';

	public function getGameLogStatistics($dateFrom, $dateTo) {
		return $this->getAGGameLogStatistics($dateFrom, $dateTo);
	}

	public function getGameLogStatisticsByIds($ids) {
		return $this->getAGGameLogStatistics(null, null, $ids);
	}

	/**
	 * overview : get ag game log statistics.
	 *
	 * @param datetime $dateFrom
	 * @param datetime $dateTo
	 *
	 * @return array
	 */
	public function getAGGameLogStatistics($dateFrom, $dateTo, $ids=null)
	{

        //use fields: player_id, playername, start_at, end_at, sceneId, gamecode, billno,
        //result, bet_amount, result_amount, after_balance, external_uniqueid, response_result_id
        //game_code, game_type, game, game_description_id, game_type_id, platformtype, flag

	    $sql = <<<EOD
SELECT
AG.sceneId,
AG.platformtype,
AG.billno,
AG.result,
AG.datatype,
AG.gamecode,
AG.external_uniqueid,
AG.response_result_id,
AG.bettime start_at,
AG.bettime end_at,

AG.netamount AS result_amount,
AG.validbetamount AS bet_amount,
AG.betamount AS real_bet_amount,
AG.gametype AS game_code,
AG.platformtype AS game_type,
AG.gametype AS game,
AG.beforecredit + AG.netamount AS after_balance,
AG.playername AS playername,
AG.flag AS flag,
game_provider_auth.player_id,
GD.game_type_id,
GD.id AS game_description_id

FROM ag_game_logs AS AG

LEFT JOIN game_description AS GD
ON AG.gametype = GD.game_code AND GD.game_platform_id = ?
JOIN game_provider_auth
ON AG.playername = game_provider_auth.login_name AND game_provider_auth.game_provider_id = ?
EOD;

	    $data = array(
	        AG_API,
	        AG_API,
	    );

		if(!empty($ids)){

			$idStr=implode(',', $ids);
	        $sql.=' WHERE AG.id IN ('.$idStr.')';

		}else{

			$sql.=' WHERE AG.bettime >= ? AND AG.bettime <= ?';

			$data[]=$dateFrom;
	        $data[]=$dateTo;

		}

	    $query = $this->db->query($sql, $data);

	    return $this->getMultipleRow($query);
	}

}
///END OF FILE///////
