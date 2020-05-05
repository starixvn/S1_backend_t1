<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_delete_declined_promotion_functions_201607261347 extends CI_Migration {
	// const PARENT_ID = 59;
	// const FUNC_ID = 164;
	// const FUNC_NAME = 'Allow to declined forever promotion';
	// const FUNC_CODE = 'allow_to_delete_declined_promotion';

	public function up() {
		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// $this->roles->initFunction(self::FUNC_CODE, self::FUNC_NAME, self::FUNC_ID, self::PARENT_ID);

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