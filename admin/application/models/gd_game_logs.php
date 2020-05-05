<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Gd_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "gd_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertGdGameLogs($data) {
		//var_dump($data);
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($rowId) {
		// $qry = $this->db->get_where($this->tableName, array('row_id' => $rowId));
		// if ($this->getOneRow($qry) == null) {
		// 	return false;
		// } else {
		// 	return true;
		// }
	}

    public function checkGameId($gameId,$betId,$gameUsername){
        #check current row bet id
        $this->db->select('id')
                 ->where('bet_id', $betId);
        $rowBetId = $this->db->get($this->tableName);
        $rowBetId = $rowBetId->row('id');

        #if current row bet id does not exist, check column `extra`
        if (empty($rowBetId)) {
            $this->db->select('id')
                     ->where('game_id', $gameId)
                     ->where('user_id', $gameUsername)
                     ->like('extra',$betId);
            $rowBetId = $this->db->get($this->tableName);
            $rowBetId = $rowBetId->row('id');
        }

        return !empty($rowBetId) ? true:null;
    }

    public function isGameIdAlreadyExist($gameId,$gameUsename){
        $this->db->select("extra")
                 ->where("game_id", $gameId)
                 ->where("user_id", $gameUsename);
        $result = $this->db->get($this->tableName);
        $data = $result->row_array();

        return $data;
    }

    /**
     * overview : update game logs
     *
     * @param  array    $data
     * @return boolean
     */
    public function updateGameLogs($data,$gameUsername) {
        $this->db->where('game_id', $data['game_id']);
        $this->db->where('user_id', $gameUsername);
        return $this->db->update($this->tableName, $data);
    }


	public function getAvailableRows($rows) {

		$availableRows = array();
		if(isset($rows)){
			foreach ($rows as $row) {
				if(isset($row['BetID'])){
					$sql = "SELECT bet_id FROM gd_game_logs WHERE  bet_id = ? ";
					$query = $this->db->query($sql, array($row['BetID']));
				    if($query->num_rows() == 0){
				    	array_push($availableRows, $row);
				    }
				}
			}
		}

		return $availableRows;
	}




function getGdGameLogStatistics($dateFrom, $dateTo) {



		$sql = <<<EOD
SELECT
gd.user_id as gameUsername,
gd.external_uniqueid,
gd.gameshortcode,
gd.bet_time,
gd.time,
gd.product_id,
gd.product_id as game_name,
gd.bet_amount as bet_amount,
gd.end_balance,
gd.win_loss,
gd.response_result_id,
gd.bet_time as start_at,
gd.bet_time as end_at,
gd.bet_arrays,
gd.extra,
gd.game_id,


gdesc.id as game_description_id,
gdesc.game_name as game,
gdesc.game_code as game_code,
gdesc.game_type_id

FROM
	gd_game_logs as gd
LEFT JOIN game_description as gdesc ON gd.product_id = REPLACE(gdesc.game_code,'RNG', '')  and gdesc.game_platform_id = ?
JOIN game_provider_auth ON gd.user_id =  game_provider_auth.login_name  AND game_provider_auth.game_provider_id = ?
WHERE
	gd.bet_time >= ? AND gd.bet_time <= ?

EOD;

	$data = array(
			GD_API,
			GD_API,
			$dateFrom,
			$dateTo
		);

	$query = $this->db->query($sql,$data);
 	 return $this->getMultipleRow($query);

	}







}

///END OF FILE///////

