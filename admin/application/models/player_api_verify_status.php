<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * Transaction_notes
 *
 * This model represents notes attached to transactions e.g. withdrawal.
 *
 */
class Player_api_verify_status extends BaseModel {
	protected $tableName = 'player_api_verify_status';

	const API_RESPOSE_SUCCESS = 1;
	const API_RESPOSE_FAIL    = 2;
	const API_UNKNOWN         = 3;
	const NO_VERFY_REQUIRED   = 4;

	public function __construct() {
		parent::__construct();
	}

	# $transaction can be 'withdrawal' or 'deposit'
	# $params can contain the following:
	# before_status
	# after_status
	public function add($player_id, $status) {
		if(empty($player_id)) {
			throw new Exception("can't find player id");
		}

		if(!empty($status)){
			$data['player_id'] = $player_id;
			$data['status'] = $status;
			$data['created_at'] = $this->utils->getNowForMysql();
			$data['updated_at'] = $this->utils->getNowForMysql();

			return $this->insertRow($data);
		}
		return false;
	}

	/**
     * detail: update the status base on player_id
     * @param int $player_id
     * @return array
     */
    public function updateApiStatusByPlayerId($player_id, $status) {
    	if(!empty($status)){
    		$this->db->where('player_id', $player_id);
			$this->db->update($this->tableName, array('status' => $status));
    	}
    }

	/**
	 * detail: get status
	 *
	 * @param int player_id
	 * @return array
	 */
	public function getApiStatusByPlayerId($player_id) {
		$this->db->select('status')->from($this->tableName)->where('player_id', $player_id);
		return $this->runOneRowOneField('status');
	}

	public function batchInsertDataFromPlayer(){
		$this->db->select('playerId,dispatch_account_level_id');
		$this->db->from('player');

		$query = $this->runMultipleRow();

		$sql = $this->db->last_query();
		$this->utils->debug_log('========================get player sql ' . $sql);

		$register_options = $this->CI->utils->getConfig('register_event_xinyan_api');
		$new_dispatch_account_level = $register_options['assign_members_in_specific_dispatc_level'];

		$data     = [];
		$id       = '';
		$level_id = '';

		foreach ($query as $value) {
			$id          = $value->playerId;
			$level_id	 = $value->dispatch_account_level_id;

			switch ($level_id) {
				case $new_dispatch_account_level:
					$status = self::API_RESPOSE_FAIL;
					break;
				default:
					$status = self::API_UNKNOWN;
					break;
			}

			$str = array(
				'player_id'   => $id,
				'status'     => $status,
				'created_at' => $this->utils->getNowForMysql(),
				'updated_at' => $this->utils->getNowForMysql()
			);
			$data[] = $str;
		}
		$this->startTrans();
		$this->db->insert_batch($this->tableName, $data);
		$success = $this->endTransWithSucc();

		$sql2 = $this->db->last_query();
		$this->utils->debug_log('========================get player sql2 ' . $sql2);
		return $success;
	}
}
