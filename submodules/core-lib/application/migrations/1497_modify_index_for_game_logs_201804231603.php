<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_index_for_game_logs_201804231603 extends CI_Migration {

	public function up() {

		$sql="alter table game_logs_unsettle add index index_external_uniqueid (external_uniqueid)";
		$this->db->query($sql);

		$sql="alter table game_logs add index index_external_uniqueid (external_uniqueid)";
		$this->db->query($sql);

	}

	public function down() {

		$sql="drop index index_external_uniqueid ON game_logs_unsettle";
		$this->db->query($sql);

		$sql="drop index index_external_uniqueid ON game_logs";
		$this->db->query($sql);

	}

}
