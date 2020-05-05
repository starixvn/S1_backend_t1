<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Xhtdlottery_game_logs extends BaseModel {
	
	const PRIMARY_KEY = 'bet_id';

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "xhtdlottery_game_logs";

	public function insertXhtdGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	public function syncToXhtdGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	public function isUniqueIdAlreadyExists($uniqueId) {
		$qry = $this->db->get_where($this->tableName, array(self::PRIMARY_KEY => $uniqueId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	public function getXhtdGameLogStatistics($dateFrom, $dateTo) {

		$sql = <<<EOD
SELECT 
	gd.id as game_description_id, 
	gd.game_type_id,
	gd.void_bet as void_bet,
    game_provider_auth.login_name as gameUsername,
    xhtd.external_uniqueid,
    xhtd.response_result_id,
    xhtd.result,
    xhtd.money,
    xhtd.time,
    xhtd.game_id as game
   
FROM 
	xhtdlottery_game_logs as xhtd
LEFT JOIN 
	game_description as gd ON xhtd.game_id = gd.external_game_id COLLATE utf8_unicode_ci and gd.void_bet!=1 and gd.game_platform_id = ?
JOIN 
	game_provider_auth ON xhtd.user = game_provider_auth.login_name COLLATE utf8_unicode_ci AND game_provider_auth.game_provider_id = ?
WHERE
	xhtd.time >= ? AND xhtd.time <= ?
EOD;
	
		$query = $this->db->query($sql, array(
			XHTDLOTTERY_API,
			XHTDLOTTERY_API,
			$dateFrom,
			$dateTo,
		));

		return $this->getMultipleRow($query);
	}

	public function getAvailableRows($rows) {
		
		$this->db->select(self::PRIMARY_KEY)
                 ->from($this->tableName)
                 ->where_in(self::PRIMARY_KEY, array_column($rows, 'betId'));
		
		$existsRow = $this->runMultipleRowArray();

		$availableRows = null;
		if ( ! empty($existsRow)) {
			$existsId = array_column($existsRow, self::PRIMARY_KEY);
			$availableRows = array();
			foreach ($rows as $row) {
				$uniqueId = $row['betId'];
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