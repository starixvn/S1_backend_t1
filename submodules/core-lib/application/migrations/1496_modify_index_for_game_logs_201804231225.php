<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_index_for_game_logs_201804231225 extends CI_Migration {

	public function up() {
		//modify column
		$sql="drop index index_external_uniqueid ON vr_game_logs";
		$this->db->query($sql);

		$sql="alter table vr_game_logs add UNIQUE index index_external_uniqueid (external_uniqueid)";
		$this->db->query($sql);

		$sql="alter table ibc_game_logs add UNIQUE index idx_external_uniqueid (external_uniqueid)";
		$this->db->query($sql);

		$sql="drop index index_external_uniqueid ON fishinggame_game_logs";
		$this->db->query($sql);

		$sql="alter table fishinggame_game_logs add UNIQUE index index_external_uniqueid (external_uniqueid)";
		$this->db->query($sql);

		$sql="alter table pinnacle_game_logs add UNIQUE index idx_external_uniqueid (external_uniqueid)";
		$this->db->query($sql);

		$sql="drop index idx_external_uniqueid ON game_logs_unsettle";
		$this->db->query($sql);

		$sql="alter table game_logs_unsettle add UNIQUE index idx_external_uniqueid (game_platform_id, external_uniqueid)";
		$this->db->query($sql);

		$sql="drop index idx_game_logs_external_uniqueid ON game_logs";
		$this->db->query($sql);

		$sql="alter table game_logs add UNIQUE index idx_game_logs_external_uniqueid (game_platform_id, external_uniqueid)";
		$this->db->query($sql);

	}

	public function down() {

		$sql="drop index idx_external_uniqueid ON ibc_game_logs";
		$this->db->query($sql);

		$sql="drop index idx_external_uniqueid ON pinnacle_game_logs";
		$this->db->query($sql);

	}
}
