<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_player_center_sidebar_system_features_201709070930 extends CI_Migration {

	public function up() {
		$this->db->trans_start();
		$this->db->insert('system_features', array(
			'name' => 'player_center_sidebar_transfer',
			'enabled' => 1
		));
		$this->db->insert('system_features', array(
			'name' => 'player_center_sidebar_deposit',
			'enabled' => 1
		));
		$this->db->insert('system_features', array(
			'name' => 'player_center_sidebar_message',
			'enabled' => 1
		));
		$this->db->trans_complete();
	}

	public function down() {
		$this->db->trans_start();
		$this->db->delete('system_features', array('name' => 'player_center_sidebar_transfer'));
		$this->db->delete('system_features', array('name' => 'player_center_sidebar_deposit'));
		$this->db->delete('system_features', array('name' => 'player_center_sidebar_message'));
		$this->db->trans_complete();
	}
}