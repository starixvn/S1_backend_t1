<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_game_template_to_static_sites extends CI_Migration {

	private $tableName = 'static_sites';

	public function up() {

		$fields = array(
			'pt_game_type_template' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'pt_game_template' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);

		//update default site

		$pt_game_type_template = <<<EOD
<% _.each( game_types, function( listItem ) { %>
	<li<% if ( listItem.active ){ %> class="curr"<% } %>>
		<a href="<%- playerServerUri %>/game_description/index/<%- listItem.game_platform_id %>/<%- listItem.id %>"><%- listItem.game_type %></a>
	</li>
<% }); %>
EOD;

		$pt_game_template = <<<EOD
<% _.each( game_descriptions, function( listItem ) { %>
	<li class="product">
		<div class="product-img"><img src="<%- listItem.game_image %>" /></div>
		<div class="product-name mt10"><%= listItem.game_name %></div>
		<div class="fn-clear mt10">
			<a class="fn-left ui-btn real" href="javascript:;" data-mode="real" data-code="<%- listItem.game_code %>">进入游戏</a>
			<a class="fn-left ui-btn ml10 trial" href="http://cache.download.banner.drunkenmonkey88.com/flash/24/casino_drunkenmonkey88/launchcasino.html?language=&affiliates=1&nolobby=1&game=<%- listItem.game_code %>&mode=offline" data-mode="trial" data-code="<%- listItem.game_code %>">免费试玩</a>
		</div>
	</li>
<% }); %>
EOD;

		$data = array(
			'pt_game_type_template' => $pt_game_type_template,
			'pt_game_template' => $pt_game_template,
		);
		$this->db->where('site_name', 'default');
		$this->db->update($this->tableName, $data);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'pt_game_type_template');
		$this->dbforge->drop_column($this->tableName, 'pt_game_template');
	}
}