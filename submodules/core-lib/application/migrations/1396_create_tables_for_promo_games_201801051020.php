<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_tables_for_promo_games_201801051020 extends CI_Migration {
	private $tableNames = [
		'promo_game_deploy_channels' ,
		'promo_game_game_to_channel' ,
		'promo_game_games' ,
		'promo_game_gametypes' ,
		'promo_game_player_game_history' ,
		'promo_game_player_to_games' ,
		'promo_game_prizes' ,
		'promo_game_promorule_to_games' ,
		'promo_game_resources' ,
		'promo_game_themes'
	];

	private $now;

	private function create_table_promo_game_deploy_channels() {
		$tableName = 'promo_game_deploy_channels';

		if (!$this->db->table_exists($tableName)) {
			$fields = [
				'id'			=> [ 'type' => 'INT'		, 'null' => false		, 'auto_increment' => true ] ,
				'channel'		=> [ 'type' => 'VARCHAR'	, 'constraint' => '32'	, 'null' => true] ,
				'enabled'		=> [ 'type' => 'TINYINT'	, 'null' => false		, 'default' => 1 ] ,
				'last_update'	=> [ 'type' => 'TIMESTAMP'	, 'null' => true ]
			];

			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($tableName);

			$channels = [
				[ 'channel' => 'Android App', 'enabled' => 1, 'last_update' => $this->now ] ,
				[ 'channel' => 'iOS App'	, 'enabled' => 1, 'last_update' => $this->now ] ,
				[ 'channel' => 'Static Site', 'enabled' => 0, 'last_update' => $this->now ] ,
				[ 'channel' => 'Mobile Site', 'enabled' => 0, 'last_update' => $this->now ]
			];

			foreach ($channels as $channel) {
				$this->db->insert($tableName, $channel);
			}
		}


	}

	private function create_table_promo_game_game_to_channel() {
		$tableName = 'promo_game_game_to_channel';

		if (!$this->db->table_exists($tableName)) {
			$fields = [
				'id'			=> [ 'type' => 'INT'	, 'null' => false	, 'auto_increment' => true ] ,
				'game_id'		=> [ 'type' => 'INT'	, 'null' => false ] ,
				'channel_id'	=> [ 'type' => 'INT'	, 'null' => false ] ,
				'updated_at'	=> [ 'type' => 'TIMESTAMP'	, 'null' => true ]
			];

			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($tableName);
		}
	}

	private function create_table_promo_game_games() {
		$tableName = 'promo_game_games';

		if (!$this->db->table_exists($tableName)) {
			$fields = [
				'id'			=> [ 'type' => 'INT'	, 'null' => false	, 'auto_increment' => true ] ,
				'gamename'		=> [ 'type' => 'VARCHAR', 'constraint' => '32'	, 'null' => true] ,
				'gametype_id'	=> [ 'type' => 'INT'	, 'null' => false ] ,
				'theme_id'		=> [ 'type' => 'INT'	, 'null' => false ] ,
				'desc'			=> [ 'type' => 'TEXT'	, 'null' => true ] ,
				'status'		=> [ 'type' => 'ENUM("enabled","disabled")'	, 'null' => false, 'default' => 'enabled'  ] ,
				'updated_at'	=> [ 'type' => 'TIMESTAMP'	, 'null' => true ] ,
				'created_at'	=> [ 'type' => 'TIMESTAMP'	, 'null' => true ] ,
				'deleted_at'	=> [ 'type' => 'TIMESTAMP'	, 'null' => true ] ,
				'updated_by'	=> [ 'type' => 'INT'	, 'null' => true ] ,
				'created_by'	=> [ 'type' => 'INT'	, 'null' => true ] ,
				'deleted_by'	=> [ 'type' => 'INT'	, 'null' => true ] ,
			];

			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($tableName);
		}
	}

	private function create_table_promo_game_gametypes() {
		$tableName = 'promo_game_gametypes';

		if (!$this->db->table_exists($tableName)) {
			$fields = [
				'id'			=> [ 'type' => 'INT'		, 'null' => false		, 'auto_increment' => true ] ,
				'gametype'		=> [ 'type' => 'VARCHAR'	, 'constraint' => '32'	, 'null' => true] ,
				'enabled'		=> [ 'type' => 'TINYINT'	, 'null' => false		, 'default' => 1 ] ,
				'last_update'	=> [ 'type' => 'TIMESTAMP'	, 'null' => true ]
			];

			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($tableName);

			$channels = [
				[ 'gametype' => 'scratchcard'	, 'enabled' => 1, 'last_update' => $this->now ] ,
				[ 'gametype' => 'luckywheel'	, 'enabled' => 1, 'last_update' => $this->now ] ,
				[ 'gametype' => 'puzzlebox'		, 'enabled' => 1, 'last_update' => $this->now ] ,
				[ 'gametype' => 'redenvelope'	, 'enabled' => 1, 'last_update' => $this->now ]
			];

			foreach ($channels as $channel) {
				$this->db->insert($tableName, $channel);
			}
		}
	}

	private function create_table_promo_game_player_game_history() {
		$tableName = 'promo_game_player_game_history';

		if (!$this->db->table_exists($tableName)) {
			$fields = [
				'id'			=> [ 'type' => 'INT'	, 'null' => false	, 'auto_increment' => true ] ,
				'status'		=> [ 'type' => 'ENUM("started","closed")'	, 'null' => false, 'default' => 'started'  ] ,
				'player_id'		=> [ 'type' => 'INT'	, 'null' => false ] ,
				'game_id'		=> [ 'type' => 'INT'	, 'null' => false ] ,
				'promorule_id'	=> [ 'type' => 'INT'	, 'null' => false ] ,
				'bonus_type'	=> [ 'type' => 'VARCHAR', 'constraint' => '32'	, 'null' => true] ,
				'bonus_amount'	=> [ 'type' => 'DECIMAL', 'constraint' => '9,2'	, 'null' => true] ,
				'game_config'	=> [ 'type' => 'TEXT'	, 'null' => true ] ,
				'external_request_id'	=> [ 'type' => 'VARCHAR', 'constraint' => '48'	, 'null' => true] ,
				'request_promotion_id'	=> [ 'type' => 'VARCHAR', 'constraint' => '48'	, 'null' => true] ,
				'updated_at'	=> [ 'type' => 'TIMESTAMP'	, 'null' => true ] ,
				'created_at'	=> [ 'type' => 'TIMESTAMP'	, 'null' => true ] ,
			];

			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($tableName);
		}
	}


	private function create_table_promo_game_player_to_games() {
		$tableName = 'promo_game_player_to_games';

		if (!$this->db->table_exists($tableName)) {
			$fields = [
				'id'			=> [ 'type' => 'INT'	, 'null' => false	, 'auto_increment' => true ] ,
				'status'		=> [ 'type' => 'ENUM("enabled","disabled")'	, 'null' => false, 'default' => 'enabled'  ] ,
				'player_id'		=> [ 'type' => 'INT'	, 'null' => false ] ,
				'game_id'		=> [ 'type' => 'INT'	, 'null' => false ] ,
				'promorule_id'	=> [ 'type' => 'INT'	, 'null' => false ] ,
				'gametype_id'	=> [ 'type' => 'INT'	, 'null' => false ] ,
				'play_rounds'	=> [ 'type' => 'INT'	, 'null' => true ] ,
				'updated_at'	=> [ 'type' => 'TIMESTAMP'	, 'null' => true ] ,
				'created_at'	=> [ 'type' => 'TIMESTAMP'	, 'null' => true ] ,
			];

			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($tableName);
		}
	}

	private function create_table_promo_game_prizes() {
		$tableName = 'promo_game_prizes';

		if (!$this->db->table_exists($tableName)) {
			$fields = [
				'id'			=> [ 'type' => 'INT'	, 'null' => false	, 'auto_increment' => true ] ,
				'game_id'		=> [ 'type' => 'INT'	, 'null' => false ] ,
				'sort'			=> [ 'type' => 'INT'	, 'null' => false ] ,
				'title'			=> [ 'type' => 'VARCHAR', 'constraint' => '32'	, 'null' => true] ,
				'prize_type'	=> [ 'type' => "ENUM('cash', 'vip_exp', 'nothing')"	, 'null' => true ] ,
				'amount'		=> [ 'type' => 'DECIMAL', 'constraint' => '9,2'	, 'null' => true] ,
				'prob'			=> [ 'type' => 'DECIMAL', 'constraint' => '6,3'	, 'null' => true] ,
				'message'		=> [ 'type' => 'TEXT'	, 'null' => true ] ,
				'updated_at'	=> [ 'type' => 'TIMESTAMP'	, 'null' => true ]
			];

			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($tableName);
		}
	}

	private function create_table_promo_game_promorule_to_games() {
		$tableName = 'promo_game_promorule_to_games';

		if (!$this->db->table_exists($tableName)) {
			$fields = [
				'id'			=> [ 'type' => 'INT'	, 'null' => false	, 'auto_increment' => true ] ,
				'promorule_id'	=> [ 'type' => 'INT'	, 'null' => false ] ,
				'game_id'		=> [ 'type' => 'INT'	, 'null' => false ] ,
				'play_rounds'	=> [ 'type' => 'INT'	, 'null' => false ] ,
				'budget_cash'	=> [ 'type' => 'DECIMAL', 'constraint' => '9,2'	, 'null' => true] ,
				'budget_vipexp'	=> [ 'type' => 'INT'	, 'null' => false ] ,
				'updated_at'	=> [ 'type' => 'TIMESTAMP'	, 'null' => true ]
			];

			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($tableName);
		}
	}

	private function create_table_promo_game_resources() {
		$tableName = 'promo_game_resources';

		if (!$this->db->table_exists($tableName)) {
			$fields = [
				'id'			=> [ 'type' => 'INT'	, 'null' => false	, 'auto_increment' => true ] ,
				'game_id'		=> [ 'type' => 'INT'	, 'null' => true ] ,
				'gametype_id'	=> [ 'type' => 'INT'	, 'null' => true ] ,
				'theme_id'		=> [ 'type' => 'INT'	, 'null' => true ] ,
				'res_name'		=> [ 'type' => 'VARCHAR', 'constraint' => '32'	, 'null' => false] ,
				'index'			=> [ 'type' => 'INT'	, 'null' => true ] ,
				'value'			=> [ 'type' => 'VARCHAR', 'constraint' => '255'	, 'null' => true] ,
				'type'			=> [ 'type' => "ENUM('url-image', 'others')"	, 'null' => false	, 'default' => 'url-image'] ,
				'updated_at'	=> [ 'type' => 'TIMESTAMP'	, 'null' => true ]
			];

			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->add_key(['gametype_id','theme_id','game_id']);
			$this->dbforge->create_table($tableName);

			$rows_json = '[{"game_id":"0","gametype_id":"2","theme_id":"6","res_name":"skin","index":null,"value":"wheel\/newyear\/newyear_roulette.png","type":"url-image"},{"game_id":"0","gametype_id":"2","theme_id":"6","res_name":"bg","index":null,"value":"wheel\/newyear\/newyear_skin.png","type":"url-image"},{"game_id":"0","gametype_id":"1","theme_id":"4","res_name":"masking","index":null,"value":"\/scratch\/winter\/card_winter_cover.png","type":"url-image"},{"game_id":"0","gametype_id":"1","theme_id":"4","res_name":"bg","index":null,"value":"\/scratch\/winter\/card_winter_empty.png","type":"url-image"},{"game_id":"0","gametype_id":"1","theme_id":"6","res_name":"masking","index":null,"value":"\/scratch\/newyear\/card_newyear_cover.png","type":"url-image"},{"game_id":"0","gametype_id":"1","theme_id":null,"res_name":"prize","index":"0","value":"\/scratch\/coin.png","type":"url-image"},{"game_id":"0","gametype_id":"1","theme_id":null,"res_name":"prize","index":"1","value":"\/scratch\/vipexp.png","type":"url-image"},{"game_id":"0","gametype_id":"1","theme_id":null,"res_name":"prize","index":"2","value":"\/scratch\/box_newyear.png","type":"url-image"},{"game_id":"0","gametype_id":"2","theme_id":"6","res_name":"arrow","index":null,"value":"wheel\/newyear\/newyear_arrow.png","type":"url-image"},{"game_id":"0","gametype_id":"2","theme_id":"6","res_name":"button","index":null,"value":"wheel\/newyear\/newyear_button.png","type":"url-image"},{"game_id":"0","gametype_id":"2","theme_id":"4","res_name":"arrow","index":null,"value":"wheel\/winter\/winter_arrow.png","type":"url-image"},{"game_id":"0","gametype_id":"2","theme_id":"4","res_name":"button","index":null,"value":"wheel\/winter\/winter_button.png","type":"url-image"},{"game_id":"0","gametype_id":"2","theme_id":"4","res_name":"skin","index":null,"value":"wheel\/winter\/winter_roulette.png","type":"url-image"},{"game_id":"0","gametype_id":"2","theme_id":"4","res_name":"bg","index":null,"value":"wheel\/winter\/winter_skin.png","type":"url-image"},{"game_id":"0","gametype_id":"1","theme_id":"6","res_name":"bg","index":null,"value":"\/scratch\/newyear\/card_newyear_empty.png","type":"url-image"}]';

			$rows = json_decode($rows_json, 'as_array');
			foreach ($rows as $row) {
				$this->db->insert($tableName, $row);
			}
		}

	}

	private function create_table_promo_game_themes() {
		$tableName = 'promo_game_themes';

		if (!$this->db->table_exists($tableName)) {
			$fields = [
				'id'			=> [ 'type' => 'INT'		, 'null' => false		, 'auto_increment' => true ] ,
				'theme'			=> [ 'type' => 'VARCHAR'	, 'constraint' => '32'	, 'null' => true] ,
				'enabled'		=> [ 'type' => 'TINYINT'	, 'null' => false		, 'default' => 1 ] ,
				'last_update'	=> [ 'type' => 'TIMESTAMP'	, 'null' => true ]
			];

			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($tableName);

			$channels = [
				[ 'theme' => 'spring', 'enabled' => 1, 'last_update' => $this->now ] ,
				[ 'theme' => 'summer', 'enabled' => 1, 'last_update' => $this->now ] ,
				[ 'theme' => 'autumn', 'enabled' => 1, 'last_update' => $this->now ] ,
				[ 'theme' => 'winter', 'enabled' => 1, 'last_update' => $this->now ] ,
				[ 'theme' => 'xmas'	, 'enabled' => 1, 'last_update' => $this->now ] ,
				[ 'theme' => 'new_year', 'enabled' => 1, 'last_update' => $this->now ]
			];

			foreach ($channels as $channel) {
				$this->db->insert($tableName, $channel);
			}
		}


	}

	public function up() {
		$this->load->library("utils");
 		$this->now = $this->utils->getNowForMysql();

		$this->create_table_promo_game_deploy_channels();
		$this->create_table_promo_game_game_to_channel();
		$this->create_table_promo_game_games();
		$this->create_table_promo_game_gametypes();
		$this->create_table_promo_game_player_game_history();
		$this->create_table_promo_game_player_to_games();
		$this->create_table_promo_game_prizes();
		$this->create_table_promo_game_promorule_to_games();
		$this->create_table_promo_game_resources();
		$this->create_table_promo_game_themes();

	}

	public function down() {
		$this->dbforge->drop_table('promo_game_themes');
		$this->dbforge->drop_table('promo_game_resources');
		$this->dbforge->drop_table('promo_game_promorule_to_games');
		$this->dbforge->drop_table('promo_game_prizes');
		$this->dbforge->drop_table('promo_game_player_to_games');
		$this->dbforge->drop_table('promo_game_player_game_history');
		$this->dbforge->drop_table('promo_game_gametypes');
		$this->dbforge->drop_table('promo_game_games');
		$this->dbforge->drop_table('promo_game_game_to_channel');
		$this->dbforge->drop_table('promo_game_deploy_channels');
	}
}

