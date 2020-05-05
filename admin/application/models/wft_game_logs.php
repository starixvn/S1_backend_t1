<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Wft_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "wft_game_logs";

	# Insert data while surpressing any error (mostly duplicated fetch id)
	public function add($data) {
		$db_debug = $this->db->db_debug; # save setting
		$this->db->db_debug = FALSE; # disable debugging for queries
		$result = $this->db->insert($this->tableName, $data);
		$this->db->db_debug = $db_debug; # restore setting
		return $result;
	}

	public function hasFetchId($fetchId) {
		$query = $this->db->get_where($this->tableName, array('fetch_id' => $fetchId));
		return $query->num_rows() > 0;
	}

	public function getGameLogs($startTime, $endTime) {
		$query = $this->db
					->where('trans_date >= ', $startTime)
					->where('trans_date < ', $endTime)
					->get($this->tableName);
		return $query->result();
	}
}
