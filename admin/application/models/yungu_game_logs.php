<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Yungu_game_logs extends Base_game_logs_model {

    function __construct() {
        parent::__construct();
    }

    protected $tableName = "yungu_game_logs";

    /**
     * @param rowId int
     *
     * @return boolean
     */
    function isRowIdAlreadyExists($rowId) {
        $qry = $this->db->get_where($this->tableName, array('external_uniqueid' => $rowId));
        if ($this->getOneRow($qry) == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param data array
     *
     * @return boolean
     */
    function updateGameData($data) {
        $this->db->where('external_uniqueid', $data['external_uniqueid']);
        return $this->db->update($this->tableName, $data);
    }

    public function getAvailableRows($rows) {
        $maxRowId = null;
        $arr = array("-1"); // make sure array not empty

        foreach ($rows as $row) {
            $betId = trim( strval($row['betId']) );
            if( !empty($betId) ){
                $arr[] = $row['betId'];
            }
        }
        $this->db->select('betId')->from($this->tableName)
            ->where('status', '2') // only pick settled game logs
            ->where_in('betId', $arr);
        $existsRow = $this->runMultipleRow();

        // $this->utils->printLastSQL();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array();
            foreach ($existsRow as $row) {
                $existsId[] = $row->betId;
            }
            $availableRows = array();
            foreach ($rows as $row) {
                if ($maxRowId == null || $row['betId'] > $maxRowId) {
                    $maxRowId = $row['betId'];
                }

                if (!in_array($row['betId'], $existsId)) {
                    $availableRows[] = $row;
                }
            }
        } else {
            $availableRows = $rows;
            foreach ($rows as $row) {
                if ($maxRowId == null || $row['betId'] > $maxRowId) {
                    $maxRowId = $row['betId'];
                }
            }
        }

        return array($availableRows, $maxRowId);
    }

    public function sync($data) {
        if ($this->isRowIdAlreadyExists($data['external_uniqueid'])) {
            $this->updateGameData($data);
        } else {
            $this->insertGameLogs($data);
        }
    }

    public function getGameLogStatistics($dateFrom, $dateTo){
        $sql = <<<EOD
SELECT
YUNGU.id as yungu_id,
YUNGU.gameId,
YUNGU.betId,
YUNGU.user,
YUNGU.phaseNum,
YUNGU.betType,
YUNGU.money,
YUNGU.status,
YUNGU.result,
YUNGU.time,
YUNGU.external_uniqueid,
YUNGU.response_result_id,
game_provider_auth.player_id,
GD.game_type_id,
GD.game_code,
GT.game_type,
GD.game_name as game,
GD.id as game_description_id

FROM yungu_game_logs AS YUNGU

LEFT JOIN game_description AS GD
ON  YUNGU.gameId = GD.game_code and GD.game_platform_id = ?

LEFT JOIN game_type AS GT
ON GD.game_type_id = GT.id

JOIN game_provider_auth
ON YUNGU.user = game_provider_auth.login_name AND game_provider_auth.game_provider_id = ?
EOD;

        $data = array(
                YUNGU_GAME_API,
                YUNGU_GAME_API,
                );


        $sql.=' WHERE YUNGU.time >= ? AND YUNGU.time <= ? ';

        $data[] = $dateFrom;
        $data[] = $dateTo;

        $query = $this->db->query($sql, $data);

        return $this->getMultipleRowArray($query);
    }
}

///END OF FILE///////