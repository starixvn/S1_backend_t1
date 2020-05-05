<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_login_template_on_static_sites_201510201133 extends CI_Migration {

	private $tableName = 'static_sites';

	public function up() {
		$tmpl = <<<EOD
<form id="<%- formId %>" action="<%- ui.loginUrl %>" method="POST" target="<%- ui.loginIframeName %>">
<span><%- default_prefix_for_username %>
<input name="login" type="text" class="ui-input fn-left J-verify" placeholder="<%- langText.form_field_username %>">
<input name="password" type="password" class="ui-input fn-left J-verify" placeholder="<%- langText.form_field_password %>">
<input type="submit" value="<%- langText.button_login %>" class="fn-left ui-btn ui-btn-red J-submit">
<a class="fn-left ui-btn ui-btn-brown J-regist-btn _player_register" href="javascript:void(0);"><%- langText.form_register %></a>
<input type="hidden" name="act" value="<%- act %>">
</form>
<iframe name="<%- ui.loginIframeName %>" id="<%- ui.loginIframeName %>" width="0" height="0" border="0" style="display:none;border:0px;width:0px;height:0px;"></iframe>
EOD;

		$data = array('login_template' => $tmpl);
		$this->db->where('site_name', 'default');
		$this->db->update($this->tableName, $data);
	}

	public function down() {
		$tmpl = <<<EOD
<form id="<%- formId %>" action="<%- ui.loginUrl %>" method="POST" target="<%- ui.loginIframeName %>">
<input name="login" type="text" class="ui-input fn-left J-verify" placeholder="<%- langText.form_field_username %>">
<input name="password" type="password" class="ui-input fn-left J-verify" placeholder="<%- langText.form_field_password %>">
<input type="submit" value="<%- langText.button_login %>" class="fn-left ui-btn ui-btn-red J-submit">
<a class="fn-left ui-btn ui-btn-brown J-regist-btn _player_register" href="javascript:void(0);"><%- langText.form_register %></a>
<input type="hidden" name="act" value="<%- act %>">
</form>
<iframe name="<%- ui.loginIframeName %>" id="<%- ui.loginIframeName %>" width="0" height="0" border="0" style="display:none;border:0px;width:0px;height:0px;"></iframe>
EOD;
		$data = array('login_template' => $tmpl);
		$this->db->where('site_name', 'default');
		$this->db->update($this->tableName, $data);
	}
}
///END OF FILE/////////////