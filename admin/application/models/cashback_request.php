<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Cashback_request
 *
 * General behaviors include :
 * *
 *
 * @category Marketing
 * @version 1.0.0
 * @copyright 2013-2016 TripleOneTech
 */
class Cashback_request extends BaseModel {

    protected $tableName = 'cashback_request';
    protected $idField = 'id';

	# STATUS
	const APPROVED = 1;
	const DECLINED = 2;
	const PENDING = 3;

    public function __construct() {
        parent::__construct();
    }

    public function getPendingCashbackRequestCount($playerId){
		$this->db->select('COUNT(id) as count');
		$this->db->from($this->tableName);
		$this->db->where('player_id', $playerId);
		$this->db->where('status', self::PENDING);
		$count = $this->runOneRowOneField('count');

		return $count;
	}

	public function getLastCashbackRequestByStatus($playerId, $status){
		$this->db->select('request_datetime,request_amount,status');
		$this->db->from($this->tableName);
		$this->db->where('player_id', $playerId);
		$this->db->where('status', $status);
		$this->db->order_by('request_datetime', 'desc');
		$raw = $this->runOneRow();

		return $raw;
	}

	public function getLastPendingCashbackRequest($playerId){
		return $this->getLastCashbackRequestByStatus($playerId, self::PENDING);
	}

	public function getLastApprovedCashbackRequest($playerId){
		return $this->getLastCashbackRequestByStatus($playerId, self::APPROVED);
	}

	/**
	 * 1. get Cashback Time Interval end time from last pending request
	 * 2. get Cashback Time Interval start time from last approved request time + 1 sec
	 * 3. If there is no approved cashback request, use account register created time as start time
	 *
	 * @param $playerId
	 * @return array
	 */
	public function getCashbackTimeStart($playerId){

		$start_time_from_auto_cashback = $this->getTimeStartFromAutoCashback();
		$last_approved_cashback_request = $this->getLastApprovedCashbackRequest($playerId);

		if(!empty($last_approved_cashback_request)){
			$last_request_datetime = $last_approved_cashback_request->request_datetime;

			$time_start = new DateTime($last_request_datetime);
			$time_start->add(new DateInterval('PT1S'));
		}else{
			$this->load->model(array('player_model'));
			$playerObj=$this->player_model->getPlayerArrayById($playerId);
			$time_start = new DateTime($playerObj['createdOn']);
		}

		if($time_start < $start_time_from_auto_cashback){
			$time_start = $start_time_from_auto_cashback;
		}

		$time_start = $time_start->format('Y-m-d H:i:s');

		return $time_start;
	}

	/**
	 * get start time from auto cashback
	 *
	 * @return DateTime
	 */
	public function getTimeStartFromAutoCashback(){
		$this->load->model(['group_level']);
		$cashbackSettings = $this->group_level->getCashbackSettings();

		$toHour = $cashbackSettings->toHour;
		$date = date('Y-m-d');
		$time_start = "{$date} {$toHour}:00:01";
		$time_start_from_auto_cashback = new DateTime($time_start);

		return $time_start_from_auto_cashback;
	}

	public function getSumOfCashbackRequestAmountDuringAutoPeriod($player_id){
		$this->load->model(['group_level']);
		$cashbackSettings = $this->group_level->getCashbackSettings();
		$fromHour = $cashbackSettings->fromHour;
		$toHour = $cashbackSettings->toHour;

		$yesterday = date('Y-m-d', strtotime("-1 days"));
		$time_start = "{$yesterday} {$fromHour}:00:00";

		$date = date('Y-m-d');
		$time_end = "{$date} {$toHour}:59:59";

		//TODO I think cashback_request needs a datetime column to record the start time of request
		$this->db->select('SUM(request_amount) as sum_of_request_amount');
		$this->db->from($this->tableName);
		$this->db->where('player_id', $player_id);
		$this->db->where('request_datetime >=', $time_start);
		$this->db->where('request_datetime <=', $time_end);
		$sum_of_request_amount = $this->runOneRowOneField('sum_of_request_amount');

		return $sum_of_request_amount;
	}

	/**
	 * @param int $cashback_request_id
	 * @return null
	 */
	public function getCashbackRequestById($cashback_request_id){

		$this->db->select('*');
		$this->db->from($this->tableName);
		$this->db->where('id', $cashback_request_id);
		$cashback_request = $this->runOneRow();

		return $cashback_request;
	}

	public function approveCashbackRequest($cashback_request_id, $admin_user_id){
		$cashback_request = $this->getCashbackRequestById($cashback_request_id);

		$this->load->model(array('transactions'));

		$this->utils->debug_log('=========start cashback transactions.============================');
		$result_transactions = $this->transactions->createCashbackTransaction($cashback_request, $admin_user_id);

		if($result_transactions){
			$this->utils->debug_log('=========approve cashback request============================');
			$approve_data = array(
				'processed_by' => $admin_user_id,
				'processed_datetime' => $this->utils->getNowForMysql(),
				'status' => self::APPROVED,
			);

			$this->db->where('id', $cashback_request_id);
			$this->db->update($this->tableName, $approve_data);

			$this->utils->debug_log('=========approve cashback request successfully.============================');

			return true;
		}

		$this->utils->debug_log('=========approve cashback request fail for some error.============================');
		return false;

	}

	public function declineCashbackRequest($cashback_request_id, $adminUserId, $notes){
		// Monthly earnings table
		$decline_data = array(
			'processed_by' => $adminUserId,
			'processed_datetime' => $this->utils->getNowForMysql(),
			'status' => self::DECLINED,
			'notes' => $notes,
		);

		$this->db->where('id', $cashback_request_id);
		$this->db->update($this->tableName, $decline_data);
	}

	public function checkPermissionForCashback($player_id){
		$this->load->model(array('group_level'));

		$player_level_id = $this->group_level->getPlayerLevelId($player_id);

		//player level_id must in vip level ids be able to cashback
		$can_user_cashback = $this->group_level->isAllowedCashback($player_level_id);

		return $can_user_cashback;
	}

	public function getCashbackRequestRecords($request, $player_id = null){

		$this->load->library(array('data_tables'));

		$i = 0;

		$where = array();
		$values = array();

		$columns = array(
			array(
				'dt' => $i++,
				'select' => 'cashback_request.id',
				'alias' => 'cashback_request_id',
			),
			array(
				'dt' => $i++,
				'select' => 'cashback_request.request_datetime',
				'alias' => 'request_datetime',
			),
			array(
				'dt' => $i++,
				'select' => 'cashback_request.request_amount',
				'alias' => 'request_amount',
			),
			array(
				'dt' => $i++,
				'select' => 'cashback_request.status',
				'alias' => 'status',
				'formatter' => function ($data, $row) {
					switch ($data){
						case Cashback_request::APPROVED:
							return '<span class="label label-success full-width"><strong>APPROVED</strong></span>';
						case Cashback_request::DECLINED:
							return '<span class="label label-danger full-width"><strong>DECLINED</strong></span>';
						case Cashback_request::PENDING:
							return '<span class="label label-primary full-width"><strong>PENDING</strong></span>';
						default:
							return '<span class="label label-warning full-width"><strong>UNKNOWN</strong></span>';
					}
				},
			),
			array(
				'dt' => $i++,
				'select' => 'cashback_request.processed_datetime',
				'alias' => 'processed_datetime',
			),
			array(
				'dt' => $i++,
				'select' => 'cashback_request.notes',
				'alias' => 'notes',
			),
			array(
				'dt' => $i++,
				'select' => 'cashback_request.created_at',
				'alias' => 'created_at',
			),

		);

		$table = 'cashback_request';
		$joins = array(
			'player' => "player.playerId = cashback_request.player_id",
		);

		$where[] = "cashback_request.player_id = ?";
		$values[] = $player_id;

		if (isset($request['dateRangeValueStart'])) {
			$where[] = "cashback_request.request_datetime >= ?";
			$values[] = $request['dateRangeValueStart'];
		}

		if (isset($request['dateRangeValueEnd'])) {
			$where[] = "cashback_request.request_datetime <= ?";
			$values[] = $request['dateRangeValueEnd'];
		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);

		return $result;
	}

}
