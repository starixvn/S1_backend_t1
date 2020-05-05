<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 *
 * * Start sync data
 * * Update success and failed sync
 *
 * @category sync_status_model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Sync_status_model extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	const STATUS_PROCESSING = 1;
	const STATUS_SUCCESS = 2;
	const STATUS_FAILED = 3;

	protected $tableName = "sync_status";

	/**
	 * overview : start sync data
	 *
	 * @param $gamePlatformId
	 * @param $func
	 * @param $fromDate
	 * @param $toDate
	 * @param null $playerUsername
	 * @param null $playerId
	 * @param null $startAt
	 * @param null $note
	 * @return mixed
	 */
	public function startSync($gamePlatformId, $func, $fromDate, $toDate, $playerUsername = null, $playerId = null, $startAt = null, $note = null) {
		//insert and return id
		if (empty($startAt)) {
			$startAt = $this->utils->getNowForMysql();
		}
		if (empty($playerId) && !empty($playerUsername)) {
			//load id from username
			$this->load->model('player_model');
			$playerId = $this->player_model->getPlayerIdByUsername($playerUsername);
		}
		$row = array('game_platform_id' => $gamePlatformId,
			'status' => self::STATUS_PROCESSING,
			'func' => $func,
			'from_date' => $fromDate,
			'to_date' => $toDate,
			'player_id' => $playerId,
			'start_at' => $startAt,
			'note' => $note,
		);
		return $this->insertRow($row);
	}

	/**
	 * overview : failed sync
	 * @param $syncId
	 * @param null $response_result_id
	 * @param null $endAt
	 * @param null $note
	 * @return bool
	 */
	public function failedSync($syncId, $response_result_id = null, $endAt = null, $note = null) {
		return $this->endSync($syncId, $response_result_id, self::STATUS_FAILED, $endAt, $note);
	}

	/**
	 * overview : sucessfully sync
	 *
	 * @param $syncId
	 * @param null $response_result_id
	 * @param null $endAt
	 * @param null $note
	 * @return bool
	 */
	public function successfullySync($syncId, $response_result_id = null, $endAt = null, $note = null) {
		return $this->endSync($syncId, $response_result_id, self::STATUS_SUCCESS, $endAt, $note);
	}

	/**
	 * overview : update sync data
	 * @param $syncId
	 * @param $response_result_id
	 * @param $status
	 * @param null $endAt
	 * @param null $note
	 * @return bool
	 */
	public function endSync($syncId, $response_result_id, $status, $endAt = null, $note = null) {
		if (empty($endAt)) {
			$endAt = $this->utils->getNowForMysql();
		}
		$succ = false;

		if (!empty($syncId)) {
			//update sync status
			$this->db->set('response_result_id', $response_result_id)
				->set('end_at', $endAt)
				->set('status', $status)
				->set('note', $note)
				->where('id', $syncId);
			$succ = $this->runAnyUpdate($this->tableName);
		}

		return $succ;
	}

}
