<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

class Gsbbin_game_logs extends BaseModel {

    function __construct() {
        parent::__construct();
    }

    protected $tableName = "gsbbin_game_logs";

    const FLAG_FINISHED = 1;
    const FLAG_UNFINISHED = 2;

    public function insertBBINGameLogs($data) {
        return $this->insertGameData($data);
    }

    public function insertGameData($data) {
        return $this->db->insert($this->tableName, $data);
    }

    function isRowIdAlreadyExists($rowId) {
        $qry = $this->db->get_where($this->tableName, array('external_uniqueid' => $rowId));
        if ($this->getOneRow($qry) == null) {
            return false;
        } else {
            return true;
        }
    }

    function updateGameData($data) {
        $this->db->where('external_uniqueid', $data['external_uniqueid']);
        return $this->db->update($this->tableName, $data);
    }

    function getBBINGameLogStatistics($dateFrom, $dateTo) {

        $sql = <<<EOD
SELECT bbin.username,
bbin.external_uniqueid,
bbin.wagers_date,
bbin.game_type,
bbin.result,
bbin.bet_amount,
bbin.payoff,
bbin.currency,
bbin.commisionable,
bbin.response_result_id,
bbin.game_kind,

game_provider_auth.player_id,

gd.id as game_description_id,
gd.game_name as game,
gd.game_code as game_code,
gd.game_type_id,
gd.void_bet as void_bet

FROM gsbbin_game_logs as bbin
left JOIN game_description as gd ON bbin.game_type = gd.external_game_id and gd.game_platform_id=?
JOIN game_provider_auth ON bbin.username = game_provider_auth.login_name and game_provider_auth.game_provider_id=?
WHERE
wagers_date >= ? AND wagers_date <= ?
and flag=?
EOD;

        $query = $this->db->query($sql, array(
            GSBBIN_API,
            GSBBIN_API,
            $dateFrom,
            $dateTo,
            self::FLAG_FINISHED,
        ));

        return $this->getMultipleRow($query);
    }

    public function getAvailableRows($rows) {
        $maxRowId = null;
        $arr = array();

        foreach ($rows as $row) {
            $arr[] = $row['WagersID'];
        }
        $this->db->select('wagers_id')->from($this->tableName)
            ->where_in('wagers_id', $arr);
        $existsRow = $this->runMultipleRow();

        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array();
            foreach ($existsRow as $row) {
                $existsId[] = $row->wagers_id;
            }
            $availableRows = array();
            foreach ($rows as $row) {
                if ($maxRowId == null || $row['WagersID'] > $maxRowId) {
                    $maxRowId = $row['WagersID'];
                }

                if (!in_array($row['WagersID'], $existsId)) {
                    $availableRows[] = $row;
                }
            }
        } else {
            $availableRows = $rows;
            foreach ($rows as $row) {
                if ($maxRowId == null || $row['WagersID'] > $maxRowId) {
                    $maxRowId = $row['WagersID'];
                }
            }
        }

        return array($availableRows, $maxRowId);
    }

    public function sync($data) {
        if ($this->isRowIdAlreadyExists($data['external_uniqueid'])) {
            $this->updateGameData($data);
        } else {
            $this->insertGameData($data);
        }
    }
}