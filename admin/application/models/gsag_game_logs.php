<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/base_model.php';

class Gsag_game_logs extends BaseModel {

	const TABLE = "gsag_game_logs";
	const KEY 	= 'billNo';

	public function insertGsagGameLogs($data) {
		return $this->db->insert(self::TABLE, $data);
	}

	public function syncToGsagGameLogs($data) {
		return $this->db->insert(self::TABLE, $data);
	}

	public function isUniqueIdAlreadyExists($uniqueId) {
		$qry = $this->db->get_where(self::TABLE, array(self::KEY => $uniqueId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	public function getGsagGameLogStatistics($dateFrom, $dateTo) {

		$sql = <<<EOD
SELECT 
	gd.id as game_description_id, 
	gd.game_name as game, 
	gd.game_code as game_code, 
	gd.game_type_id,
	gd.void_bet as void_bet,

	gsag.MemberId,
	gsag.betAmount,
	gsag.netAmount,
	gsag.datecreated,
	gsag.CasinoTypeName,
	gsag.billNo,
	gsag.dataType as game_type_str,
	gsag.gameType as game_name_str
FROM 
	gsag_game_logs as gsag
LEFT JOIN 
	game_description as gd ON gsag.gameType = gd.game_code COLLATE utf8_unicode_ci and gd.void_bet!=1 and gd.game_platform_id = ?
WHERE
	gsag.datecreated >= ? AND gsag.datecreated <= ?
EOD;
	
		$query = $this->db->query($sql, array(
			GSAG_API,
			$dateFrom,
			$dateTo,
		));
		return $this->getMultipleRow($query);
	}

	public function getAvailableRows($rows) {

		$this->db->select(self::KEY)
				 ->from(self::TABLE)
				 ->where_in(self::KEY, array_column($rows,self::KEY));

		$existsRow = $this->runMultipleRowArray();

		$availableRows = null;
		if ( ! empty($existsRow)) {
			$existsId = array_column($existsRow, self::KEY);
			$availableRows = array();
			foreach ($rows as $row) {
				$uniqueId = $row[self::KEY];
				if ( ! in_array($uniqueId, $existsId)) {
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