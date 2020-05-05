<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_force_create_game_account_to_function_201606081821 extends CI_Migration {

	public function up() {

		// $this->load->model(array('roles'));
		// $this->roles->initFunction('force_create_game_account', 'Force Create Game Account', 137, 15, true);
	}

	public function down() {

		// $this->load->model(array('roles'));
		// $this->roles->deleteFunction(137);
	}
}