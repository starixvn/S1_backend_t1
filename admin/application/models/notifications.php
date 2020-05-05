<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

class Notifications extends BaseModel {

	protected $tableName = 'notifications';

	protected $idField = 'id';

	public function __construct() {
		parent::__construct();
	}

	public function getLists(){

		$qobj = $this->db->get($this->tableName);

		return $qobj->result_array();

	}

	public function insert( $data ){

		return $this->db->insert( $this->tableName, $data );

	}

	public function get_record( $id ){

		$qobj = $this->db->where('id', $id)
						 ->get($this->tableName);

		return $qobj->row();

	}

	public function delete( $id ){

		return $this->db->where('id', $id)
						->delete($this->tableName);

	}

}

/* End of file notifications.php */
/* Location: ./application/models/notifications.php */