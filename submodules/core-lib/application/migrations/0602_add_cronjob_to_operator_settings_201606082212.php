<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_cronjob_to_operator_settings_201606082212 extends CI_Migration {

	public function up() {
		// $this->load->model(array('operatorglobalsettings'));

		// $this->operatorglobalsettings->startTrans();

		// $this->operatorglobalsettings->addAllCronJobs();

		// $this->operatorglobalsettings->endTransWithSucc();
	}

	public function down() {
		// $this->load->model(array('operatorglobalsettings'));

		// $this->operatorglobalsettings->startTrans();

		// $this->operatorglobalsettings->deleteAllCronJobs();

		// $this->operatorglobalsettings->endTransWithSucc();
	}
}