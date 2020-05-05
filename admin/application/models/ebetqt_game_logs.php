<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ebetqt_game_logs extends Base_game_logs_model {

	public function __construct() {
		parent::__construct();
	}

	protected $tableName = "ebetqt_game_logs";

	public function syncGameLogs($data) {
		$id = $this->getIdByUniqueid($data['uniqueid']);

		if( ! empty($id)){
			$ret = $this->updateGameLog($id, $data);
			// return $this->db->update($this->tableName, $data);
		}else{
			$ret = $this->insertGameLogs($data);
			// return $this->db->insert($this->tableName, $data);
		}

		return $ret;
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {

		$sql = <<<EOD
SELECT
	ebetqt_game_logs.external_uniqueid,
	ebetqt_game_logs.playerId,
    player.username as player_username,
    ebetqt_game_logs.gameId game,
    ebetqt_game_logs.gameCategory game_type,
	ebetqt_game_logs.totalBet bet_amount,
    ebetqt_game_logs.totalPayout win_amount,
    ebetqt_game_logs.initiated start_at,
    ebetqt_game_logs.completed end_at,
    ebetqt_game_logs.response_result_id,
    gd.id as game_description_id,
    gd.game_type_id
FROM
	ebetqt_game_logs

LEFT JOIN game_description as gd ON ebetqt_game_logs.gameId = gd.external_game_id and gd.game_platform_id=?

JOIN
	player ON player.playerId = ebetqt_game_logs.playerId
WHERE
	ebetqt_game_logs.completed >= ? AND
	ebetqt_game_logs.completed <= ?
ORDER BY
	ebetqt_game_logs.completed ASC
EOD;

		$query = $this->db->query($sql, array(
            EBET_QT_API,
			$dateFrom,
			$dateTo,
		));

		$rows = $query->result_array();

		return $rows;
	}

}
///END OF FILE///////
