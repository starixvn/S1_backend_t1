<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class asia_gaming_game_logs extends Base_game_logs_model
{
    const PROVIDER_CODE = 'AG';

    protected $tableName;

    public function __construct()
    {
        parent::__construct();
        
        $this->tableName = 'asia_gaming_game_logs';
    }

    public function getAvailableRows($rows)
    {

        $this->db->select('ugsbetid')->from($this->tableName)->where_in('ugsbetid', array_column($rows, 'ugsbetid'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'ugsbetid');
            $availableRows = array();
            foreach ($rows as $row) {
                $uniqueId = $row['ugsbetid'];
                if (!in_array($uniqueId, $existsId)) {
                    $availableRows[] = $row;
                }
            }
        } else {
            $availableRows = $rows;
        }

        return $availableRows;
    }

    public function getExistingRows($rows)
    {

        $this->db->select('ugsbetid')->from($this->tableName)->where_in('ugsbetid', array_column($rows, 'ugsbetid'));
        $existsRow = $this->runMultipleRowArray();
        $existingRows = array();
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'ugsbetid');
            foreach ($rows as $row) {
                $uniqueId = $row['ugsbetid'];
                if (in_array($uniqueId, $existsId)) {
                    $existingRows[] = $row;
                }
            }
        }
        return $existingRows;
    }

    public function updateGameLogs($data)
    {
        $this->db->where('ugsbetid', $data['ugsbetid']);
        return $this->db->update($this->tableName, $data);
    }

    public function isRowIdAlreadyExists($ugsbetid)
    {
        $qry = $this->db->get_where($this->tableName, array('ugsbetid' => $ugsbetid));
        if ($this->getOneRow($qry) == null) {
            return false;
        } else {
            return true;
        }
    }

    public function getGameLogStats($dateFrom, $dateTo, $table_name, $game_platform_id)
    {
        $this->tableName = $table_name;

        $sql = <<<EOD
            SELECT
              original_gamelogs.userid,
              original_gamelogs.gameid,
              original_gamelogs.gamename,
              original_gamelogs.external_uniqueid,
              original_gamelogs.beton,
              original_gamelogs.betclosedon,
              original_gamelogs.winloss AS result_amount,
              original_gamelogs.validbet AS valid_bet_amount,
              original_gamelogs.bettype,
              original_gamelogs.playtype,
              original_gamelogs.match_detail,
              ABS(original_gamelogs.riskamt) AS bet_amount,
              original_gamelogs.response_result_id,
              original_gamelogs.postbal AS after_balance,
              original_gamelogs.roundid AS round_id,
              game_provider_auth.player_id,
              game_description.id AS game_description_id,
              game_description.game_name AS game,
              game_description.game_code,
              game_description.game_type_id,
              game_description.void_bet,
              game_type.game_type
            FROM
              {$this->tableName} AS original_gamelogs
              LEFT JOIN game_description
                ON (
                  original_gamelogs.gameid = game_description.external_game_id
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              LEFT JOIN game_type
                ON (
                  game_description.game_type_id = game_type.id
                )
              JOIN `game_provider_auth`
                ON (
                  `original_gamelogs`.`userid` = `game_provider_auth`.`login_name`
                  AND `game_provider_auth`.`game_provider_id` = ?
                )
            WHERE (
                original_gamelogs.betclosedon >= ?
                AND original_gamelogs.betclosedon <= ?
                AND original_gamelogs.gameprovidercode = ?
              )
EOD;

        $query = $this->db->query($sql, array(
            $game_platform_id,
            $game_platform_id,
            $dateFrom,
            $dateTo,
            self::PROVIDER_CODE,
        ));
        return $this->getMultipleRow($query);
    }

    public function getGameLogStatistics($dateFrom, $dateTo)
    {
    }
}

///END OF FILE///////
