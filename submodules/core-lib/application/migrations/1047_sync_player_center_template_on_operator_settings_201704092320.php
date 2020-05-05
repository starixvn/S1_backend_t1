<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_sync_player_center_template_on_operator_settings_201704092320 extends CI_Migration {

	public function up() {
		$this->load->model(['operatorglobalsettings']);
		if(method_exists($this->operatorglobalsettings, 'copyTemplateSettingToDB')){
			$this->operatorglobalsettings->copyTemplateSettingToDB();
		}
	}

	public function down() {
		# no rollback action required
	}
}