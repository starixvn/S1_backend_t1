<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_popup_template_to_static_sites extends CI_Migration {

	private $tableName = 'static_sites';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'popup_template' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		));
		//update template
		$popup_template = <<<EOD
<div id="<%- popupId %>" style="background-color: #fff;border-radius: 10px 10px 10px 10px;box-shadow: 0 0 25px 5px #999;color: #111;display: none;padding: 10px;margin-top:20px;">
    <span class="_og_popup_close" style="background-color: #2b91af;color: #fff;cursor: pointer;display: inline-block;text-align: center;text-decoration: none;border-radius: 10px 10px 10px 10px;box-shadow: none;font: bold 25px sans-serif;padding: 6px 6px 2px;position: absolute;right: -6px;top: -6px;height: 30px;width:30px">X</span>
    <div class="_og_popup_iframe_content" ></div>
</div>
EOD;

		$login_template = <<<EOD
<form id="<%- formId %>" action="<%- ui.loginUrl %>" method="POST" target="<%- ui.loginIframeName %>">
<input name="login" type="text" class="ui-input fn-left J-verify" placeholder="<%- langText.form_field_username %>">
<input name="password" type="password" class="ui-input fn-left J-verify" placeholder="<%- langText.form_field_password %>">
<input type="submit" value="<%- langText.button_login %>" class="fn-left ui-btn ui-btn-red J-submit">
<a class="fn-left ui-btn ui-btn-brown J-regist-btn _player_register" href="javascript:void(0);"><%- langText.form_register %></a>
<input type="hidden" name="act" value="<%- act %>">
</form>
<iframe name="<%- ui.loginIframeName %>" id="<%- ui.loginIframeName %>" width="0" height="0" border="0" style="display:none;border:0px;width:0px;height:0px;"></iframe>
EOD;

		$data = array(
			'popup_template' => $popup_template,
			'login_template' => $login_template,
		);
		$this->db->where('site_name', 'default');
		$this->db->update($this->tableName, $data);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'popup_template');
	}
}
