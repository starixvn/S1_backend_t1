<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_template_to_static_sites extends CI_Migration {

	private $tableName = 'static_sites';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'login_template' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'logged_template' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		));
		//add key
		$this->db->query('create unique index idx_static_sites_site_name on ' . $this->tableName . '(site_name)');
		// $this->dbforge->add_key('site_name');
		//update template
		$this->db->where('site_name', 'default');
		$login_template = <<<EOD
<form id="<%- formId %>" action="<%- ui.loginUrl %>" method="POST" target="<%- ui.loginIframeName %>">
<input name="login" type="text" class="ui-input fn-left J-verify" placeholder="<%- langText.form_field_username %>">
<input name="password" type="password" class="ui-input fn-left J-verify" placeholder="<%- langText.form_field_password %>">
<input type="submit" value="<%- langText.button_login %>" class="fn-left ui-btn ui-btn-red J-submit">
<a class="fn-left ui-btn ui-btn-brown J-regist-btn _player_register" href="javascript:void(0);"><%- langText.form_register %></a>
<a class="fn-left ui-btn ui-btn-brown J-regist-btn _player_trial_game" href="javascript:void(0);"><%- langText.header_trial_game %></a>
<input type="hidden" name="act" value="<%- act %>">
</form>
<iframe name="<%- ui.loginIframeName %>" id="<%- ui.loginIframeName %>" width="0" height="0" border="0" style="display:none;border:0px;width:0px;height:0px;"></iframe>
EOD;

		$logged_template = <<<EOD
<div class="login-member fn-left"><i class="icon icon-member"></i><a href="javascript:void(0)" class='_player_username'><%- playerName %></a></div>
<div class="login-menu fn-left fn-clear">
<a href="javascript:void(0)" class='_player_memcenter'><%- langText.header_memcenter %></a>
<a href="javascript:void(0)" class='_player_deposit'><%- langText.header_deposit %></a>
<a href="javascript:void(0)" class='_player_withdrawal'><%- langText.header_withdrawal %></a>
<a href="javascript:void(0)" class='_player_information'><%- langText.header_information %></a></div>
<a class="ui-btn ui-btn-logout fn-left _player_logout" href="javascript:void(0)" ><%- langText.button_logout %></a>
<iframe name="<%- ui.logoutIframeName %>" id="<%- ui.logoutIframeName %>" width="0" height="0" border="0" style="display:none;border:0px;width:0px;height:0px;"></iframe>
EOD;

/*<a href="javascript:void(0)" class='_player_mainwallet'><%- langText.header_mainwallet %></a>*/

		$data = array(
			'login_template' => $login_template,
			'logged_template' => $logged_template,
		);
		$this->db->update($this->tableName, $data);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'login_template');
		$this->dbforge->drop_column($this->tableName, 'logged_template');
		$this->db->query('drop index idx_static_sites_site_name on ' . $this->tableName);
	}
}
