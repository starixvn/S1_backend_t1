<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_function_support_ticket_201608212321 extends CI_Migration {
	const PARENT_ID = 37;
	const FUNC_ID = 165;
	const FUNC_NAME = 'Support Ticket';
	const FUNC_CODE = 'support_ticket';

	public function up() {
		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// $this->roles->initFunction(self::FUNC_CODE, self::FUNC_NAME, self::FUNC_ID, self::PARENT_ID, true);

		// $succ = $this->roles->endTransWithSucc();
		// if (!$succ) {
		// 	throw new Exception('migrate failed');
		// }
	}

	public function down() {
		// $this->load->model(array('roles'));
		// $this->roles->deleteFunction(self::FUNC_ID);
	}

}

////END OF FILE////