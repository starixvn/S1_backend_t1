<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Admin_login_token extends BaseModel {

	function __construct() {
		parent::__construct();
		$this->load->helper('string');
	}

	protected $tableName = "admin_login_token";

	/**
	 * new login token
	 *
	 * @param 	int playerId
	 * @return 	int last insert id
	 */
	public function newLoginToken($adminId) {
		$token = random_string('unique');
		$this->db->insert($this->tableName, array("admin_id" => $adminId, "token" => $token, "created_at" => $this->getNowForMysql()));
		return array($this->db->insert_id(), $token);
	}
}

///END OF FILE///////