<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_for_agency_to_functions_201606131021 extends CI_Migration {

	public function up() {
		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// $this->roles->initFunction('agency_player_report', 'Agency Player Report', 138, 116);
		// $this->roles->initFunction('agency_game_report', 'Agency Game Report', 139, 116);
		// $this->roles->initFunction('assign_player_agent', 'Assign Player Agent', 140, 116);
		// $this->roles->initFunction('agency_player_deposit', 'Agency Player Deposit', 141, 116);
		// $this->roles->initFunction('agency_player_withdraw', 'Agency Player Withdraw', 142, 116);

		// $succ = $this->roles->endTransWithSucc();
		// if (!$succ) {
		// 	throw new Exception('migrate failed');
		// }
	}

	public function down() {
		// $this->roles->deleteFunction(138);
		// $this->roles->deleteFunction(139);
		// $this->roles->deleteFunction(140);
		// $this->roles->deleteFunction(141);
		// $this->roles->deleteFunction(142);
	}

}
