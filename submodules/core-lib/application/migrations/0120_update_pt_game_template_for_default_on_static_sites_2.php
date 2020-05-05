<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_pt_game_template_for_default_on_static_sites_2 extends CI_Migration {

	private $tableName = 'static_sites';

	public function up() {
		$tmpl = <<<EOD
<% _.each( game_descriptions, function( listItem ) { %>
	<li class="product">
		<div class="product-img"><img src="<%- listItem.game_image %>" /></div>
		<div class="product-name mt10"><%= listItem.game_name %></div>
		<div class="fn-clear mt10">
			<a class="fn-left ui-btn real" href="<%- playerServerUri%>/iframe_module/goto_ptgame/default/<%- listItem.game_code%>/real" data-mode="real" data-code="<%- listItem.game_code %>" target='_blank'>进入游戏</a>
			<% if (listItem.game_type_id !== '7' || listItem.offline_enabled === '1') { %>
			<a class="fn-left ui-btn ml10 trial" href="<%- playerServerUri%>/pub/goto_trial_ptgame/<%- listItem.game_code%>" data-mode="trial" data-code="<%- listItem.game_code %>">免费试玩</a>
			<% } %>
		</div>
	</li>
<% }); %>
EOD;

		$data = array('pt_game_template' => $tmpl);
		$this->db->where('site_name', 'default');
		$this->db->update($this->tableName, $data);
	}

	public function down() {
	}
}
///END OF FILE/////////////