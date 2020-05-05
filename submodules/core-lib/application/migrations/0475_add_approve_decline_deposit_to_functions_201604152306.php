<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_approve_decline_deposit_to_functions_201604152306 extends CI_Migration {

	// const FUNC_ID = 105;
	// const PARENT_ID = 72;
	// const FUNC_CODE = 'approve_decline_deposit';
	// const FUNC_NAME = 'Approve/Decline Deposit';

	public function up() {
		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// $this->roles->initFunction(self::FUNC_CODE, self::FUNC_NAME, self::FUNC_ID, self::PARENT_ID);

		// $this->roles->initFunction('approve_decline_withdraw', 'Approve/Decline Withdraw', 106, self::PARENT_ID);

		// $succ = $this->roles->endTransWithSucc();
		// if (!$succ) {
		// 	throw new Exception('migrate failed');
		// }
	}

	public function down() {
		// $this->load->model(array('roles'));
		// $this->roles->deleteFunction(self::FUNC_ID);
		// $this->roles->deleteFunction(106);
	}

}

///END OF FILE////