<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Boomingseamless_game_logs extends Base_game_logs_model {

	## RESPONSE RESULT CODES
	const WALLET_ADD_METHOD = 'WALLET_ADD_METHOD';
	const WALLET_SUBTRACT_METHOD = 'WALLET_SUBTRACT_METHOD';
	const RECEIVE_CALLBACK = 'RECEIVE_CALLBACK/BONUS';
	const RECEIVE_ROLLBACK = 'RECEIVE_ROLLBACK';
	const DO_PLAYER_BET = 'DO_PLAYER_BET';
	const DO_PLAYER_CANCEL_BET = 'DO_PLAYER_CANCEL_BET';
	const QUERY_PLAYER_BALANCE = 'PROCESS_QUERY_PLAYER_BALANCE';
	const SYNC_ORIGINAL = 'PROCESS_SYNC_ORIGINAL';
	const ERROR_OCCURED = 'ERROR_OCCURED';
	const PROCESS_SUCCESS = true;
	const PROCESS_FAIL = false;
	## --------------------------------------------------------------------------
	

	## BET STATUS IF 2 MEANS THE RESULT AMOUNT IS NOT YET SYNC ON PLAYER's WALLET
	const BET_SYNC_ORIGINAL = 2;
	const BET_SETTLED = 1;
	const BET_UNSETTLED = 0;
	## --------------------------------------------------------------------------

	function __construct() {
		parent::__construct();
	}

	public $tableName = '';
	
	public function getGameLogStatistics($dateFrom, $dateTo) {
	}

	public function checkIfDataExists($data) {
        $this->db->select('*');
        $this->db->from($this->tableName);
        $this->db->where('session_id', $data['session_id']);
        $this->db->where('round', $data['round']);
        $this->db->where('external_uniqueid', $data['external_uniqueid']);
        // To check if the data is settled because we cant trigger rollback twice
        if ($data['check_rollback']) {
	        $this->db->where('bet_status', self::BET_SETTLED);
        }
		return $this->runOneRowArray();
	}

	public function updateGameLogsByExternalUniqueId($external_uniqueid, $data) {
		$this->db->where('external_uniqueid', $external_uniqueid);
		$this->db->set($data);
		return $this->runAnyUpdate($this->tableName);
	}
}

///END OF FILE///////
