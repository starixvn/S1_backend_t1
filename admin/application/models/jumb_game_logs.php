<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Jumb_game_logs extends BaseModel {

    function __construct() {
        parent::__construct();
    }

    protected $tableName = "jumb_game_logs";

    /**
     * @param data array
     *
     * @return boolean
     */
    public function insertJumbGameLogs($data) {
        return $this->db->insert($this->tableName, $data);
    }

    /**
     * @param rowId int
     *
     * @return boolean
     */
    function isRowIdAlreadyExists($rowId) {
        $qry = $this->db->get_where($this->tableName, array('gameid' => $rowId));
        if ($this->getOneRow($qry) == null) {
            return false;
        } else {
            return true;
        }
    }

    function updateJumbGameLogs($UPDATE,$WHERE) {

        $this->db->where('gameid', $WHERE['gameid']);
        return $this->db->update($this->tableName, $UPDATE);
    }

    public function getAvailableRows($rows) {
        $existsRow = array();
        if(!empty($rows)){
            $this->db->select('seqNo')->from($this->tableName)->where_in('seqNo', array_column($rows, 'seqNo'));
            $existsRow = $this->runMultipleRowArray();
        }

        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'seqNo');
            $availableRows = array();
            foreach ($rows as $row) {
                $TicketId = $row['seqNo'];
                if (!in_array($TicketId, $existsId)) {
                    $availableRows[] = $row;
                }
            }
        } else {
            $availableRows = $rows;
        }
        return $availableRows;
    }


    function getGameLogStatistics($dateFrom, $dateTo) {
        $sql = <<<EOD
            SELECT
              `jumb_game_logs`.`playerId`,
              `jumb_game_logs`.`username`,
              `jumb_game_logs`.`external_uniqueid`,
              `jumb_game_logs`.`gameDate` AS date_start,
              `jumb_game_logs`.`lastModifyTime` AS date_end,
              `jumb_game_logs`.`mtype` AS game_code,
              `jumb_game_logs`.`total` AS result_amount,
              `jumb_game_logs`.`bet` AS bet_amount,
              `jumb_game_logs`.`response_result_id`,
              `jumb_game_logs`.`seqNo`,
              `jumb_game_logs`.`afterBalance` as after_balance,
              `game_description`.`id`  AS game_description_id,
              `game_description`.`game_name` AS game,
              `game_description`.`game_type_id`,
              `game_description`.`void_bet`,
              `game_type`.`game_type`
            FROM
              `jumb_game_logs`
              LEFT JOIN `game_description`
                ON `jumb_game_logs`.`mtype`= `game_description`.`game_code`
              LEFT JOIN `game_type`
                ON `game_description`.`game_type_id` = `game_type`.`id`
            WHERE (
                `jumb_game_logs`.`gameDate` >= ?
                AND `jumb_game_logs`.`lastModifyTime` <= ?
                )
EOD;

        $data = array(
            $dateFrom,
            $dateTo
        );

        $query = $this->db->query($sql,$data);

        return $this->getMultipleRow($query);
    }

}

///END OF FILE///////

