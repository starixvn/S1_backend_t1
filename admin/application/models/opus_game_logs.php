<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Opus_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "opus_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertOpusGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($rowId) {
		$qry = $this->db->get_where($this->tableName, array('row_id' => $rowId));
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
	function updateGameLogs($data) {
		$this->db->where('row_id', $data['row_id']);
		return $this->db->update($this->tableName, $data);
	}

	function getOpusGameLogStatistics($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT

game_provider_auth.player_id,

opus.game_code,
opus.game_detail,
opus.game_category,
opus.trans_id,
opus.bet,
opus.win,
opus.balance_end,
opus.trans_datetime,
opus.bet_record_id,
opus.game_platform,
opus.response_result_id,
opus.trans_datetime start_at,
opus.stamp_date end_at,
opus.external_uniqueid,
opus.member_code,
opus.player_hand as player_placed_bet,
opus.vendor,
opus.bet_status,
opus.game_result,
opus.player_hand,

gd.game_type_id,
gd.id AS game_description_id,
gd.game_code AS gameshortcode,
gd.game_name AS game

FROM opus_game_logs AS opus
JOIN game_provider_auth ON SUBSTRING(opus.member_code,4) = game_provider_auth.login_name AND game_provider_auth.game_provider_id = ?
LEFT JOIN game_description AS gd ON opus.game_code = gd.external_game_id AND gd.game_platform_id = ? AND opus.vendor = gd.sub_game_provider
WHERE opus.stamp_date >= ? AND opus.stamp_date <= ?
EOD;

		$query = $this->db->query($sql, array(
			OPUS_API,
			OPUS_API,
			$dateFrom,
			$dateTo,
		));

		return $this->getMultipleRow($query);
	}

	public function getAvailableRows($rows) {
		$arr = array();
		foreach ($rows as $row) {
			$uniqueId = $row['bet_record_id'];
			$arr[] = $uniqueId;
		}

		$this->db->select('bet_record_id')->from($this->tableName)->where_in('bet_record_id', $arr);
		$existsRow = $this->runMultipleRow();

		// $this->utils->printLastSQL();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->bet_record_id;
			}
			$availableRows = array();
			foreach ($rows as $row) {
				$uniqueId = $row['bet_record_id'];
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
