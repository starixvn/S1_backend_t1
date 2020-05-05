<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Genesis_seamless_transactions extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}
	const IS_FAILED = 1;
	// protected $tableName = "genesis_seamless_transactions";
	 
	public function init($tableName) {
        $this->tableName = $tableName;
    }

	public function isRowIdAlreadyExists($request_id) {
		$qry = $this->db->get_where($this->tableName, array('request_id' => $request_id));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	public function isTxIdAlreadyExists($txId) {
		$this->db->from($this->tableName)
			->where('txId', $txId);
		return $this->runExistsResult();
	}

	public function isDebitTxIdAlreadyExists($txId, $action) {
		$this->db->from($this->tableName)
			->where('debitTxId', $txId)
				->where('action', $action);
		return $this->runExistsResult();
	}

	public function getFailedTransaction($bet_id) {
		$qry = $this->db->get_where($this->tableName, array('bet_id' => $bet_id, 'is_failed' => self::IS_FAILED));
		return $this->getOneRow($qry);
	}

	public function getTransaction($txId, $action, $status) {
		$qry = $this->db->get_where($this->tableName, array('txId' => $txId, 'action' => $action, 'status' => $status));
		return $this->getOneRow($qry);
	}

	public function getDebitInfo($txId, $action) {
		$qry = $this->db->select("playerId, gameUsername, amount")
                    ->from($this->tableName)
                    ->where("txId",$txId)
                    ->where("action",$action)
                    ->get();

         return $qry->row_array();
	}

	public function updateTransactionStatus($txId, $action, $status) {
        $data = [
            "updated_at" => $this->utils->getNowForMysql(),
            "status" => $status,
        ];

        $this->db->where("txId",$txId)
        		->where("action",$action)
                ->update($this->tableName,$data);
        
        return $this->db->affected_rows();
    }

	public function getGameLogStatistics($dateFrom, $dateTo) {
		return null;
	}
}

///END OF FILE///////