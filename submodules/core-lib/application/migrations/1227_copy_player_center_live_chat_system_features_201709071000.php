<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_copy_player_center_live_chat_system_features_201709071000 extends CI_Migration {

	# Copies values from enable_player_center_live_chat to player_center_sidebar_message
	# enable_player_center_live_chat will not be used any more
	public function up() {
		$query = $this->db->query("SELECT enabled FROM system_features WHERE name = 'enable_player_center_live_chat'");
		$rows = $query->result_array();
		if(count($rows) == 0) {
			return;
		}

		$this->db->trans_start();
		$this->db->where(array('name' => 'player_center_sidebar_message'));
		$this->db->update('system_features', array('enabled' => $rows[0]['enabled']));
		$this->db->trans_complete();
	}

	public function down() {
		# Nothing to recover
	}
}