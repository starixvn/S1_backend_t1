<?php
if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

// class MY_Session extends CI_Session {

// 	public function __construct() {
// 		parent::__construct();
// 	}

	# UPDATE PLAYER WHEN SESSION EXPIRED OR INVALIDATED
	// function sess_destroy() {
	// 	$session_id = null;
	// 	if (!empty($this->userdata) && array_key_exists('session_id', $this->userdata)) {
	// 		$session_id = @$this->userdata['session_id'];
	// 	}
	// 	if (!empty($session_id)) {
	// 		$this->CI->db->where('session_id', $session_id);
	// 		$this->CI->db->update('adminusers', array(
	// 			'lastLogoutTime' => date("Y-m-d H:i:s"),
	// 			'session_id' => null,
	// 		));
	// 	}
	// 	parent::sess_destroy();
	// }

	// function _sess_gc() {
		// if ($this->sess_use_database != TRUE) {
		// 	return;
		// }

		// srand(time());
		// if ((rand() % 100) < $this->gc_probability) {
		// 	$expire = $this->now - $this->sess_expiration;

		// $sql = "UPDATE
		//                      adminusers
		//                  JOIN
		//                      {$this->sess_table_name}
		//                  ON
		//                      adminusers.session_id = {$this->sess_table_name}.session_id
		//                  SET
		//                      adminusers.lastLogoutTime = ?,
		//                      adminusers.session_id = NULL
		//                  WHERE
		//                      {$this->sess_table_name}.last_activity < ?";

		// $this->CI->db->query($sql, array(date("Y-m-d H:i:s"), $expire));

		// $this->CI->db->where("last_activity < {$expire}");
		// $this->CI->db->delete($this->sess_table_name);

		// 	log_message('debug', 'Session garbage collection performed:' . $expire . ' , now:' . $this->now . ' , sess_expiration:' . $this->sess_expiration);
		// }
	// }

// }