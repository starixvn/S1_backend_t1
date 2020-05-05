<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Lebo_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "lebo_game_logs";

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
            $ext_uniq_id  = $row['key_id'].$row['game_code'].$row['uno'].$row['order_time'];
            if( !empty($ext_uniq_id) ){
                $arr[] = $ext_uniq_id;
            }
        }
		$this->db->select('external_uniqueid')->from($this->tableName)
			->where('settlement_flag', '1') // only pick settled game logs
			->where_in('external_uniqueid', $arr);
		$existsRow = $this->runMultipleRow();

		// $this->utils->printLastSQL();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->external_uniqueid;
			}
			$availableRows = array();
			foreach ($rows as $row) {
                $ext_uniq_id  = $row['key_id'].$row['game_code'].$row['uno'].$row['order_time'];
                
				if (!in_array($ext_uniq_id, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
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
LEBO.id as lebo_id,
LEBO.game_code,
LEBO.key_id AS external_uniqueid,
LEBO.uno,
LEBO.period_num,
LEBO.bet_content,
LEBO.odds,
LEBO.bet_amount,
LEBO.bet_result,
LEBO.order_time,
LEBO.settlement_flag,
LEBO.external_uniqueid,
LEBO.response_result_id,
game_provider_auth.player_id,
GD.game_type_id,
GT.game_type,
GD.game_name as game,
GD.id as game_description_id

FROM lebo_game_logs AS LEBO

LEFT JOIN game_description AS GD
ON  LEBO.game_code  = GD.game_code and GD.game_platform_id = ?

LEFT JOIN game_type AS GT
ON GD.game_type_id = GT.id

JOIN game_provider_auth
ON LEBO.uno = game_provider_auth.login_name AND game_provider_auth.game_provider_id = ?
EOD;

        $data = array(
                LEBO_GAME_API,
                LEBO_GAME_API,
                );


        $sql.=' WHERE LEBO.order_time >= ? AND LEBO.order_time <= ?';

        $data[] = (new Datetime($dateFrom))->getTimestamp();
        $data[] = (new Datetime($dateTo))->getTimestamp();

        $query = $this->db->query($sql, $data);

        return $this->getMultipleRowArray($query);
    }
}

///END OF FILE///////