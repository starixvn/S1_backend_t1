<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * walletaccount_timelog
 *
 * This model represents notes attached to transactions e.g. withdrawal.
 *
 */
class walletaccount_timelog extends BaseModel {
	protected $tableName = 'walletaccount_timelog';

	const ADMIN_USER  = 1;
	const PLAYER_USER = 2;

	public function __construct() {
		parent::__construct();
	}

	# $add withdrawal time log
	# $params can contain the following:
	# before_status
	# after_status
	public function add($walletAccountId, $create_type, $created_by, $params = array()) {

		$data['walletAccountId'] = $walletAccountId;
		$data['create_date'] = $this->utils->getNowForMysql();
		$data['create_type'] = $create_type;
		$data['created_by'] = $created_by;

		$data = array_merge($data, $params);
		return $this->insertRow($data);
	}

	/**
	 * get row(s) by type, transaction id, status
	 * @param  string $transaction   type
	 * @param  int $transactionId id
	 * @return string
	 */
	public function getWalletAccountTimeLogByWalletAccountId($walletAccountId, $status = null) {
		$this->db->select($this->tableName.'.*');
		$this->db->from($this->tableName);
		$this->db->where('walletAccountId', $walletAccountId);

		if (!empty($status)) {
			$this->db->where('after_status', $status);
			$this->db->order_by('create_date','desc');
			$row = $this->runOneRowArray();
			return $row;
		}

		$this->db->order_by('id');

		return $this->runMultipleRowArray();
	}

	public function batchInsertDataFromWalletAccount(){
		$this->db->select('walletAccountId,dwDateTime,transactionType');
		$this->db->from('walletaccount');

		$query = $this->runMultipleRow();

		$sql = $this->db->last_query();
		$this->utils->debug_log('========================get player sql ' . $sql);

		$data       = [];
		$id         = '';
		$created_at = '';

		foreach ($query as $value) {
			$id          = $value->walletAccountId;
			$created_at	 = $value->dwDateTime;
			$type        = $value->transactionType;

			if($type == 'withdrawal'){

				$str = array(
				'walletAccountId' => $id,
				'create_date'     => $created_at,
				'create_type'     => self::ADMIN_USER,
				'created_by'      => null,
				'before_status '  => 'request'
				);
				$data[] = $str;
			}
		}
		$this->startTrans();
		$this->db->insert_batch($this->tableName, $data);
		$success = $this->endTransWithSucc();

		$sql2 = $this->db->last_query();
		$this->utils->debug_log('========================get player sql2 ' . $sql2);
		return $success;
	}

	public function batchInsertDataFromTransactionNotes(){
		$this->db->select('*');
		$this->db->from('transaction_notes');
		$this->db->where('transaction = "withdrawal"');
		$this->db->where('before_status != ""');
		$this->db->where('after_status != ""');

		$query = $this->runMultipleRow();

		$sql = $this->db->last_query();
		$this->utils->debug_log('========================get player sql ' . $sql);

		$data           = [];
		$created_at     = '';
		$type           = '';
		$id             = '';
		$created_by     = '';
		$before_status  = '';
		$after_status   = '';


		foreach ($query as $value) {

			$created_at    = $value->create_date;
			$type          = $value->transaction;
			$id            = $value->transaction_id;
			$created_by    = $value->admin_user_id;
			$before_status = $value->before_status;
			$after_status  = $value->after_status;


			if($type == 'withdrawal'){

				$str = array(
				'walletAccountId' => $id,
				'create_date'     => $created_at,
				'create_type'     => self::ADMIN_USER,
				'created_by'      => $created_by,
				'before_status'   => $before_status,
				'after_status'    => $after_status
				);
				$data[] = $str;
			}
		}
		$this->startTrans();
		$this->db->insert_batch($this->tableName, $data);
		$success = $this->endTransWithSucc();

		$sql2 = $this->db->last_query();
		$this->utils->debug_log('========================get player sql2 ' . $sql2);
		return $success;
	}

}
