<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_manually_add_bonus_to_functions_permissions_201610251512 extends CI_Migration {

	public function up() {
		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// $this->roles->initFunction('manually_add_bonus', 'Manually Add Bonus', 172, 59, true);

		// $succ = $this->roles->endTransWithSucc();
		// if (!$succ) {
		// 	throw new Exception('migrate failed');
		// }
	}

	public function down() {
		// $this->load->model(array('roles'));
		// $this->roles->deleteFunction(172);
	}

}