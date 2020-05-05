<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_for_common_cashback_game_rules_201610290451 extends CI_Migration {

	public function up() {
		$this->load->model(['player_model']);
		$this->player_model->addIndex('common_cashback_game_rules', 'idx_unique_game_rule', 'rule_id, game_description_id');
		// $this->db->query('create unique index idx_cbgr_rule_id on common_cashback_game_rules(rule_id)');
		// $this->db->query('create index idx_cbgr_game_description_id on common_cashback_game_rules(game_description_id)');
	}

	public function down() {
		$this->load->model(['player_model']);
		$this->player_model->dropIndex('common_cashback_game_rules', 'idx_unique_game_rule');
		// $this->db->query('drop index idx_cbgr_rule_id on common_cashback_game_rules');
		// $this->db->query('drop index idx_cbgr_game_description_id on common_cashback_game_rules');
	}
}
