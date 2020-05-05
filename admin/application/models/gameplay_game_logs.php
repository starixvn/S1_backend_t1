<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Gameplay_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "gameplay_game_logs";
	protected $ctxmGameLogsTable = "ctxm_game_logs";
	protected $sbtechGameLogsTable = "sbtech_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertGameplayGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertCtxmGameLogs($data) {
		return $this->db->insert($this->ctxmGameLogsTable, $data);
	}

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertSbtechGameLogs($data) {
		return $this->db->insert($this->sbtechGameLogsTable, $data);
	}

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	function updateGameLogs($data) {
		$this->db->where('external_uniqueid', strval($data['external_uniqueid']));
		return $this->db->update($this->tableName, $data);
	}

	function getGameplayGameLogStatistics($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT
  gameplay.user_id,
  gameplay.bet,
  gameplay.trans_date,
  gameplay.winlose,
  gameplay.game_code AS gameshortcode,
  gameplay.game_type AS gp_game_type,
  gameplay.external_uniqueid,
  gameplay.response_result_id,
  game_description.game_code,
  game_description.game_type_id,
  game_description.game_name,
  game_description.id AS game_description_id,
  gt.game_type
FROM
  gameplay_game_logs AS gameplay
  LEFT JOIN game_description
    ON gameplay.game_code = game_description.game_code
    AND game_description.game_platform_id = ?
  LEFT JOIN game_type AS gt
    ON game_description.game_type_id = gt.id
  JOIN game_provider_auth
    ON gameplay.user_id = game_provider_auth.login_name
    AND game_provider_id = ?
WHERE gameplay.trans_date >= ?
  AND gameplay.trans_date <= ?
  AND gameplay.game_provider IS NULL
EOD;

		$query = $this->db->query($sql, array(
			GAMEPLAY_API,
			GAMEPLAY_API,
			$dateFrom,
			$dateTo,
		));
		return $this->getMultipleRow($query);
	}

	function getCtxmGameLogStatistics($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT ctxm.user_id,
ctxm.trans_id,
ctxm.trans_date,
ctxm.game_type,
ctxm.game_provider,
ctxm.bet,
ctxm.winlose,
ctxm.balance,
ctxm.external_uniqueid,
ctxm.response_result_id,
game_description.game_code,
game_description.game_type_id,
game_description.game_name,
game_description.id as game_description_id,
gt.game_type
FROM ctxm_game_logs as ctxm
LEFT JOIN game_description ON ctxm.game_type = game_description.english_name and game_description.game_platform_id=?
LEFT JOIN game_type as gt ON game_description.game_type_id = gt.id
LEFT JOIN game_provider_auth ON ctxm.user_id = game_provider_auth.login_name and game_provider_id=?
WHERE
trans_date >= ? AND trans_date <= ?
EOD;
		$query = $this->db->query($sql, array(
			GAMEPLAY_API,
			GAMEPLAY_API,
			$dateFrom,
			$dateTo,
		));

		return $this->getMultipleRow($query);
	}

	function getSbtechGameLogStatistics($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT sbtech.id,
sbtech.merchantCustomerId AS user_id,
sbtech.creationDate AS trans_date,
sbtech.stake AS bet,
sbtech.pl AS winlose,
sbtech.external_uniqueid,
sbtech.response_result_id,
sbtech.branchName,
sbtech.leagueId as gameshortcode,
sbtech.eventTypeId,
sbtech.eventTypeName,
game_description.game_code,
game_description.game_type_id,
game_description.game_name,
game_description.id as game_description_id,
gt.game_type
FROM sbtech_game_logs AS sbtech
LEFT JOIN game_description ON sbtech.leagueId = game_description.game_code and game_description.game_platform_id=?
LEFT JOIN game_type as gt ON game_description.game_type_id = gt.id
LEFT JOIN game_provider_auth ON sbtech.merchantCustomerId = game_provider_auth.login_name AND game_provider_id=?
WHERE
creationDate >= ? AND creationDate <= ?
EOD;
		$query = $this->db->query($sql, array(
			GAMEPLAY_API,
			GAMEPLAY_API,
			$dateFrom,
			$dateTo,
		));

		return $this->getMultipleRow($query);
	}

	function getGameLogStatisticsForSubproviders($dateFrom, $dateTo, $gameType){
        $sql = <<<EOD
SELECT
  gameplay.user_id,
  gameplay.bet,
  gameplay.trans_date,
  gameplay.winlose,
  gameplay.game_code AS gameshortcode,
  gameplay.external_uniqueid,
  gameplay.response_result_id,
  game_description.game_code,
  game_description.game_type_id,
  gameplay.game_type as game_name,
  game_description.id AS game_description_id,
  gt.game_type
FROM
  gameplay_game_logs AS gameplay
  LEFT JOIN game_description
    ON gameplay.game_type = game_description.note
    AND game_description.game_platform_id = ?
  LEFT JOIN game_type AS gt
    ON game_description.game_type_id = gt.id
  JOIN game_provider_auth
    ON gameplay.user_id = game_provider_auth.login_name
    AND game_provider_id = ?
WHERE gameplay.trans_date >= ?
  AND gameplay.trans_date <= ?
  AND gameplay.game_provider = ?
EOD;

        $query = $this->db->query($sql, array(
            GAMEPLAY_API,
            GAMEPLAY_API,
            $dateFrom,
            $dateTo,
            $gameType,
        ));

        return $this->getMultipleRow($query);
    }

    function getGameLogStatisticsForKeno($dateFrom, $dateTo,$sub_game_provider){

        $select = "gameplay.user_id,
                   gameplay.bet,
                   gameplay.timeBet,
                   gameplay.winAmount,
                   gameplay.external_uniqueid,
                   gameplay.response_result_id,
                   gameplay.areaName as gameshortcode,
                   gameplay.game_provider,
                   gameplay.keno_status,
                   gd.game_code,
                   gd.game_name,
                   gd.game_type_id,
                   gd.id AS game_description_id,
                   gt.game_type";

        $this->db->distinct('gameplay.betno');
        $this->db->select($select,false);
        $this->db->from("gameplay_game_logs AS gameplay");
        $this->db->join("game_description as gd","gameplay.areaName = gd.external_game_id","left");
        $this->db->join("game_type as gt","gd.game_type_id = gt.id","left");
        $this->db->join("game_provider_auth","game_provider_auth.login_name = gameplay.user_id");
        $this->db->where('gameplay.timeBet >=',$dateFrom);
        $this->db->where('gameplay.timeBet <=',$dateTo);
        $this->db->where('gameplay.game_provider <=',$sub_game_provider);
        $query = $this->db->get();

		return $query->result_array();
	}

    /**
     * overview : check if refNo already exist
     *
     * @param  int      $refNo
     *
     * @return boolean
     */
    public function isRowIdAlreadyExists($external_uniqueid) {
        $qry = $this->db->get_where($this->tableName, array('external_uniqueid' => strval($external_uniqueid)));
        if ($this->getOneRow($qry) == null) {
            return false;
        } else {
            return true;
        }
    }

	public function getAvailableRows($rows) {
		$arr = array();
		foreach ($rows as $row) {
			if (isset($row['bet_id'])) {
				$uniqueId = $row['bet_id'];
			} elseif (isset($row['trxId'])) {
				$uniqueId = $row['trxId'];
			}
			$arr[] = strval($uniqueId);
		}

		$this->db->select('external_uniqueid')->from($this->tableName)->where_in('external_uniqueid', $arr);
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
				if (isset($row['bet_id'])) {
					$uniqueId = $row['bet_id'];
				} elseif (isset($row['trxId'])) {
					$uniqueId = $row['trxId'];
				}
				if (!in_array($uniqueId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			//add all
			$availableRows = $rows;
		}
		return $availableRows;
	}

	public function getSlotAvailableRows($rows) {
		$arr = array();
		foreach ($rows as $row) {
			// $this->utils->debug_log('print row', $row);
			if (isset($row['operationCode'])) {
				$uniqueId = $row['operationCode'];
				$arr[] = strval($uniqueId);
			}
		}
		$this->db->select('external_uniqueid')->from($this->tableName)->where_in('external_uniqueid', $arr);
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
				$uniqueId = $row['operationCode'];
				if (!in_array($uniqueId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			//add all
			$availableRows = $rows;
		}

		return $availableRows;
	}

	public function getCtxmAvailableRows($rows) {
		$arr = array();
		foreach ($rows as $row) {
			$uniqueId = $row['trans_id'];
			$arr[] = strval($uniqueId);
		}
		$this->db->select('external_uniqueid')->from($this->ctxmGameLogsTable)->where_in('external_uniqueid', $arr);
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
				$uniqueId = $row['trans_id'];
				if (!in_array($uniqueId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			//add all
			$availableRows = $rows;
		}

		return $availableRows;
	}

	public function getSbtechAvailableRows($rows,$isSingleRow=false) {
		$arr = array();
		if($isSingleRow){
			$uniqueId = $rows['rowId'];
			$arr[] = strval($uniqueId);
		}else{
			foreach ($rows as $row) {
				$uniqueId = $row['rowId'];
				$arr[] = strval($uniqueId);
			}
		}
		$this->db->select('external_uniqueid')->from($this->sbtechGameLogsTable)->where_in('external_uniqueid', $arr);
		$existsRow = $this->runMultipleRow();
		// $this->utils->printLastSQL();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->external_uniqueid;
			}
			$availableRows = array();
			if($isSingleRow){
				$uniqueId = $rows['rowId'];
				if (!in_array($uniqueId, $existsId)) {
					$availableRows[] = $row;
				}
			}else{
				foreach ($rows as $row) {
					$uniqueId = $row['rowId'];
					if (!in_array($uniqueId, $existsId)) {
						$availableRows[] = $row;
					}
				}
			}
		} else {
			//add all
			$availableRows = $rows;
		}

		return $availableRows;
	}

	public function getGameplayerSubproviderAvailableRows($rows) {
		$arr = array();
		foreach ($rows as $row) {
			$uniqueId = isset($row['@attributes'])?$row['@attributes']['trans_id']:$row['trans_id'];
			$arr[] = strval($uniqueId);
		}

		$this->db->select('external_uniqueid')->from($this->tableName)->where_in('external_uniqueid', $arr);
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
				$uniqueId = isset($row['@attributes'])?$row['@attributes']['trans_id']:$row['trans_id'];
				if (!in_array($uniqueId, $existsId)) {
					$availableRows[] = isset($row['@attributes'])?$row['@attributes']:$row;
				}
			}
		} else {
			//add all
			$availableRows = $rows;
		}

		return $availableRows;
	}

    public function getGameplayKenoAvailableRows($rows) {
        $arr = array();
        foreach ($rows as $row) {
            $uniqueId = $row['betNo'];
            $arr[] = strval($uniqueId);
        }

        $this->db->select('external_uniqueid')->from($this->tableName)->where_in('external_uniqueid', $arr);
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
                $uniqueId =$row['betNo'];
                if (!in_array($uniqueId, $existsId)) {
                    $availableRows[] = $row;
                }
            }
        } else {
            //add all
            $availableRows = $rows;
        }

        return $availableRows;
    }

	public function getSubproviderUnknownGametype($unknownType){
		$query = $this->db->query("SELECT id,game_type FROM game_type WHERE game_platform_id = ? AND  game_type_code = ?", array(
			GAMEPLAY_API,
			$unknownType
		));

		return $this->getOneRow($query);
	}

	public function getGameplayLiveAvailableRows($rows) {
		if(!empty($rows)){
			$this->db->select('external_uniqueid')->from($this->tableName)->where_in('external_uniqueid', array_column($rows, 'external_uniqueid'));
			$existsRow = $this->runMultipleRowArray();
			$availableRows = null;
			if (!empty($existsRow)) {
				$existsId = array_column($existsRow, 'external_uniqueid');
				$availableRows = array();
				foreach ($rows as $row) {
					$external_id = $row['external_uniqueid'];
					if (!in_array($external_id, $existsId)) {
						$availableRows[] = $row;
					}
				}
			} else {
				$availableRows = $rows;
			}
		}else {
			$availableRows = $rows;
		}
		return $availableRows;
	}
	
	public function getGenericGameplayAvailableRows($rows) {
		$arr = array();
		foreach ($rows as $row) {
			// $this->utils->debug_log('print row', $row);
			if (isset($row['bet_id'])) {
				$uniqueId = $row['bet_id'];
				$arr[] = strval($uniqueId);
			}
		}
		$this->db->select('external_uniqueid')->from($this->tableName)->where_in('external_uniqueid', $arr);
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
				$uniqueId = $row['bet_id'];
				if (!in_array($uniqueId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			//add all
			$availableRows = $rows;
		}

		return $availableRows;
	}
}

///END OF FILE///////