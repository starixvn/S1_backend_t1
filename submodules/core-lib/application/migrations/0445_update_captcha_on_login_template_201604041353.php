<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_captcha_on_login_template_201604041353 extends CI_Migration {

	public function up() {

		$sql = <<<EOD
update static_sites
set login_template="
<form id='<%- formId %>' action='<%- ui.loginUrl %>' method='POST' target='<%- ui.loginIframeName %>'>
<span><%- default_prefix_for_username %></span>
<input name='login' type='text' class='ui-input fn-left J-verify' placeholder='<%- langText.form_field_username %>'>
<input name='password' type='password' class='ui-input fn-left J-verify' placeholder='<%- langText.form_field_password %>'>

<% if ( ui.captchaFlag ){ %>
<input type='text' name='captcha' placeholder='<%- langText.label_captcha %>' class='ui-input fn-left J-verify _captcha_input' required style='width:40px'>
<% } %>

<input type='submit' value='<%- langText.button_login %>' class='fn-left ui-btn ui-btn-red J-submit'>
<a class='fn-left ui-btn ui-btn-brown J-regist-btn _player_register' href='javascript:void(0);'><%- langText.form_register %></a>
<input type='hidden' name='act' value='<%- act %>'>

</form>
<iframe name='<%- ui.loginIframeName %>' id='<%- ui.loginIframeName %>' width='0' height='0' border='0' style='display:none;border:0px;width:0px;height:0px;'></iframe>
"
where site_name = 'default';

EOD;

		$this->db->query($sql);
	}

	public function down() {
	}
}
