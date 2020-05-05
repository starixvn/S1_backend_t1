<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_on_agin_game_logs_201611271202 extends CI_Migration {

	public function up() {
		$this->load->model(['player_model']);

		$this->player_model->addIndex('ag_game_logs', 'idx_recalcutime', 'recalcutime');
		$this->player_model->addIndex('ag_game_logs', 'idx_playername', 'playername');
		$this->player_model->addIndex('agin_game_logs', 'idx_recalcutime', 'recalcutime');
		$this->player_model->addIndex('agin_game_logs', 'idx_external_uniqueid', 'external_uniqueid');
		$this->player_model->addIndex('agbbin_game_logs', 'idx_recalcutime', 'recalcutime');
		$this->player_model->addIndex('agbbin_game_logs', 'idx_external_uniqueid', 'external_uniqueid');
		$this->player_model->addIndex('agshaba_game_logs', 'idx_recalcutime', 'recalcutime');
		$this->player_model->addIndex('agshaba_game_logs', 'idx_gamecode', 'gamecode');
		$this->player_model->addIndex('agshaba_game_logs', 'idx_playername', 'playername');
		$this->player_model->addIndex('agshaba_game_logs', 'idx_external_uniqueid', 'external_uniqueid');

		$this->db->query('create unique index idx_uniqueid on agin_game_logs(uniqueid)');
		$this->db->query('create unique index idx_uniqueid on agbbin_game_logs(uniqueid)');
		$this->db->query('create unique index idx_uniqueid on agshaba_game_logs(uniqueid)');

	}

	public function down() {
		$this->load->model(['player_model']);

		$this->player_model->dropIndex('ag_game_logs', 'idx_recalcutime');
		$this->player_model->dropIndex('ag_game_logs', 'idx_playername');
		$this->player_model->dropIndex('agin_game_logs', 'idx_recalcutime');
		$this->player_model->dropIndex('agin_game_logs', 'idx_external_uniqueid');
		$this->player_model->dropIndex('agbbin_game_logs', 'idx_recalcutime');
		$this->player_model->dropIndex('agbbin_game_logs', 'idx_external_uniqueid');
		$this->player_model->dropIndex('agshaba_game_logs', 'idx_recalcutime');
		$this->player_model->dropIndex('agshaba_game_logs', 'idx_gamecode');
		$this->player_model->dropIndex('agshaba_game_logs', 'idx_playername');
		$this->player_model->dropIndex('agshaba_game_logs', 'idx_external_uniqueid');

		$this->db->query('drop index idx_uniqueid on agin_game_logs');
		$this->db->query('drop index idx_uniqueid on agbbin_game_logs');
		$this->db->query('drop index idx_uniqueid on agshaba_game_logs');

	}
}
