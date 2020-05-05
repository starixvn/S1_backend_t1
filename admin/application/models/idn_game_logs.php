<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Idn_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "idn_game_logs";

	public function getAvailableRows($rows) {
		if(!isset($rows[0])){
			$rows = array($rows);
		}

		$this->db->select('transaction_no')->from($this->tableName)->where_in('transaction_no', array_column($rows, 'transaction_no'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'transaction_no');
			$availableRows = array();
			foreach ($rows as $row) {
				$transNo = $row['transaction_no'];
				if (!in_array($transNo, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
		}
		return $availableRows;
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {

		$select = 'idn_game_logs.playerId,
				  `idn_game_logs.username,
				  `idn_game_logs.external_uniqueid,
				  `idn_game_logs.idndate AS game_date,
				  `idn_game_logs.game AS game_code,
				  `idn_game_logs`.`response_result_id`,
				  `idn_game_logs`.`curr_amount` AS result_amount,
				  `idn_game_logs`.`periode` AS RoundID,
				  `idn_game_logs`.`curr_bet` AS BetAmount,
				  `idn_game_logs`.`amount` AS real_bet,
				  `idn_game_logs`.`status` AS status,
				  `idn_game_logs`.`total` AS after_balance,
				  `idn_game_logs`.`hand` AS hand,
				  `idn_game_logs`.`prize` AS prize,
				  `idn_game_logs`.`card` AS card,
				  `idn_game_logs`.`agent_comission`,
				  game_description`.`id`  AS game_description_id,
				  game_description.game_name AS game,
				  game_description.game_type_id,
				  game_description.void_bet,
				  game_type.game_type';

		$this->db->select($select);
		$this->db->from('idn_game_logs');
		$this->db->join('game_description', 'idn_game_logs.game = game_description.game_code AND game_description.game_platform_id = "'.IDN_API.'" AND game_description.void_bet != 1', 'LEFT');
		$this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
		$this->db->join('game_provider_auth', 'idn_game_logs.username = game_provider_auth.login_name AND game_provider_auth.game_provider_id = "'.IDN_API.'"', 'LEFT');
		$this->db->where('idn_game_logs.idndate >= "'.$dateFrom.'" AND idn_game_logs.idndate <= "' . $dateTo . '"');
		$qobj = $this->db->get();

		return $qobj->result_array();

	}

}

///END OF FILE///////