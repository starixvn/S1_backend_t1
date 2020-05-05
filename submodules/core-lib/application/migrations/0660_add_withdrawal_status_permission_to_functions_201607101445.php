<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_withdrawal_status_permission_to_functions_201607101445 extends CI_Migration {

	// const FUNC_ID = 148;  # Current largest + 1
	// const PARENT_ID = 72; # Payment Management
	// const FUNC_CODE = 'manage_API_withdrawal_status';
	// const FUNC_NAME = 'Manage API withdrawal status';

	# This function defines whether one can manage the orders with wait_API status

	public function up() {
		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// $this->roles->initFunction('approve_withdrawal_to_CS0', 'Approve withdrawal to Custom Stage 1', self::FUNC_ID, self::PARENT_ID);
		// $this->roles->initFunction('approve_withdrawal_to_CS1', 'Approve withdrawal to Custom Stage 2', self::FUNC_ID + 1, self::PARENT_ID);
		// $this->roles->initFunction('approve_withdrawal_to_CS2', 'Approve withdrawal to Custom Stage 3', self::FUNC_ID + 2, self::PARENT_ID);
		// $this->roles->initFunction('approve_withdrawal_to_CS3', 'Approve withdrawal to Custom Stage 4', self::FUNC_ID + 3, self::PARENT_ID);
		// $this->roles->initFunction('approve_withdrawal_to_CS4', 'Approve withdrawal to Custom Stage 5', self::FUNC_ID + 4, self::PARENT_ID);
		// $this->roles->initFunction('approve_withdrawal_to_CS5', 'Approve withdrawal to Custom Stage 6', self::FUNC_ID + 5, self::PARENT_ID);
		// $this->roles->initFunction('approve_withdrawal_to_payProc', 'Approve withdrawal to Payment Processing stage', self::FUNC_ID + 6, self::PARENT_ID);

		// $succ = $this->roles->endTransWithSucc();
		// if (!$succ) {
		// 	throw new Exception('migrate failed');
		// }
	}

	public function down() {
		// $this->load->model(array('roles'));
		// $this->roles->deleteFunction(self::FUNC_ID);
		// $this->roles->deleteFunction(self::FUNC_ID + 1);
		// $this->roles->deleteFunction(self::FUNC_ID + 2);
		// $this->roles->deleteFunction(self::FUNC_ID + 3);
		// $this->roles->deleteFunction(self::FUNC_ID + 4);
		// $this->roles->deleteFunction(self::FUNC_ID + 5);
		// $this->roles->deleteFunction(self::FUNC_ID + 6);
	}

}