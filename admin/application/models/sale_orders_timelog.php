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
class Sale_orders_timelog extends BaseModel {
	protected $tableName = 'sale_orders_timelog';

	const ADMIN_USER  = 1;
	const PLAYER_USER = 2;

	public function __construct() {
		parent::__construct();
	}

	# $add withdrawal time log
	# $params can contain the following:
	# before_status
	# after_status
	public function add($saleOrderId, $create_type, $created_by, $params = array()) {

		$data['sale_order_id'] = $saleOrderId;
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
	public function getWalletAccountTimeLogBysaleOrderId($saleOrderId, $status = null) {
		$this->db->select($this->tableName.'.*');
		$this->db->from($this->tableName);
		$this->db->where('sale_order_id', $saleOrderId);

		if (!empty($status)) {
			$this->db->where('after_status', $status);
			$this->db->order_by('create_date','desc');
			$row = $this->runOneRowArray();
			return $row;
		}

		$this->db->order_by('id');

		return $this->runMultipleRowArray();
	}

	public function batchInsertDataToSaleOrdersTimelogFromSaleOrders(){
		$this->db->select('id,created_at,status');
		$this->db->from('sale_orders');

		$query = $this->runMultipleRow();

		$sql = $this->db->last_query();
		$this->utils->debug_log('========================get player sql ' . $sql);

		$data          = [];
		$id            = '';
		$created_at    = '';
		$status        = '';
		$before_status = '';
		$after_status  = '';

		foreach ($query as $value) {
			$id          = $value->id;
			$created_at	 = $value->created_at;
			$status      = $value->status;

				switch ($status) {
					case Sale_order::STATUS_SETTLED:

						$before_status = Sale_order::STATUS_PROCESSING;
						$after_status  = $status;
						break;

					case Sale_order::STATUS_DECLINED:

						$before_status = Sale_order::STATUS_PROCESSING;
						$after_status  = $status;
						break;

					default:
						$before_status = Sale_order::STATUS_PROCESSING;
						$after_status  = null;
						break;
				}

			$str = array(
			'sale_order_id'   => $id,
			'create_date'     => $created_at,
			'create_type'     => self::ADMIN_USER,
			'created_by'      => null,
			'before_status'   => $before_status,
			'after_status'	  => $after_status
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
