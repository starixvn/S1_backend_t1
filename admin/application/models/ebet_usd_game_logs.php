<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ebet_usd_game_logs extends Base_game_logs_model {

    function __construct() {
        parent::__construct();
    }

    protected $tableName = "ebet_usd_game_logs";

    public function insertEbetGameLogs($data) {
        return $this->db->insert($this->tableName, $data);
    }

    public function syncToEbetGameLogs($data) {
        return $this->db->insert($this->tableName, $data);
    }

    public function isUniqueIdAlreadyExists($uniqueId) {
        $qry = $this->db->get_where($this->tableName, array('GameCode' => $uniqueId));
        if ($this->getOneRow($qry) == null) {
            return false;
        } else {
            return true;
        }
    }

    public function getGameLogStatistics($dateFrom, $dateTo){

        return $this->getebetGameLogStatistics($dateFrom, $dateTo);

    }

    public function getebetGameLogStatistics($dateFrom, $dateTo) {

        $sql = <<<EOD
SELECT
    ebet.id as id,
	ebet.external_uniqueid,
	ebet.username,
	ebet.gameshortcode,
	ebet.response_result_id,
	ebet.createTime as start_at,
	ebet.payoutTime as end_at,
	ebet.validBet as bet,
	ebet.realBet,
	ebet.payout as result,
    ebet.roundNo,
    ebet.gameType,
    ebet.judgeResult,
	ebet.betMap,
    ebet.niuniuWithHoldingTotal,
    ebet.niuniuWithHoldingDetail,
    ebet.niuniuResult,
	game_provider_auth.player_id,
	gd.id as game_description_id,
	gd.game_name as game,
	gd.game_code as game_code,
	gd.game_type_id,
	gd.void_bet as void_bet
FROM
	ebet_usd_game_logs as ebet
LEFT JOIN
	game_description as gd ON gd.game_code = ebet.gameshortcode and gd.void_bet!=1 and gd.game_platform_id = ?
JOIN
	game_provider_auth ON ebet.username = game_provider_auth.login_name AND game_provider_auth.game_provider_id = ?
WHERE
	ebet.payoutTime >= ? AND ebet.payoutTime <= ?
EOD;

        $query = $this->db->query($sql, array(
            EBET_USD_API,
            EBET_USD_API,
            $dateFrom,
            $dateTo,
        ));

        return $this->getMultipleRow($query) ? : array();
    }

    public function getAvailableRows($rows) {
        $this->db->select('uniqueid')->from($this->tableName)->where_in('uniqueid', array_column($rows, 'uniqueid'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'uniqueid');
            $availableRows = array();
            foreach ($rows as $row) {
                $uniqueId = $row['uniqueid'];
                if (!in_array($uniqueId, $existsId)) {
                    $availableRows[] = $row;
                }
            }
        } else {
            $availableRows = $rows;
        }
        return $availableRows;
    }

}

///END OF FILE///////