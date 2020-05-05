<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_logged_template_on_static_sites_20150929 extends CI_Migration {

	private $tableName = 'static_sites';

	public function up() {
		$tmpl = <<<EOD
<div class="login-member fn-left"><i class="icon icon-member"></i><a href="javascript:void(0)" class='_player_username'><%- playerName %></a></div>
<div class="login-menu fn-left fn-clear">
<a href="javascript:void(0)" class='_player_memcenter'><%- langText.header_memcenter %></a>
<a href="javascript:void(0)" class='_player_deposit'><%- langText.header_deposit %></a>
<a href="javascript:void(0)" class='_player_withdrawal'><%- langText.header_withdrawal %></a>
<a href="javascript:void(0)" class='_player_information'><%- langText.header_information %></a></div>
<a class="ui-btn ui-btn-logout fn-left" href="<%- ui.logoutUrl %>" target="<%- ui.logoutIframeName %>"><%- langText.button_logout %></a>
<iframe name="<%- ui.logoutIframeName %>" id="<%- ui.logoutIframeName %>" width="0" height="0" border="0" style="display:none;border:0px;width:0px;height:0px;"></iframe>
EOD;

		$data = array('logged_template' => $tmpl);
		$this->db->where('site_name', 'default');
		$this->db->update($this->tableName, $data);
	}

	public function down() {
	}
}
///END OF FILE/////////////