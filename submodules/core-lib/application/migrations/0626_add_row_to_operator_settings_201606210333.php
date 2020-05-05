<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_row_to_operator_settings_201606210333 extends CI_Migration {
	private $tableName = 'operator_settings';
	public function up() {
		// $this->db->insert($this->tableName, array('name' => 'self_exclusion_approval_day_cnt','value' => '2','note' =>'Number of days will the system approve self exclusion request'));
		// $this->db->insert($this->tableName, array('name' => 'cool_off_approval_day_cnt','value' => '2','note' =>'Number of days will the system approve cool off request'));
		// $this->db->insert($this->tableName, array('name' => 'deposit_limit_approval_day_cnt','value' => '1','note' =>'Number of days will the system approve deposit limit request'));
		// $this->db->insert($this->tableName, array('name' => 'loss_limit_approval_day_cnt','value' => '1','note' =>'Number of days will the system approve loss limit request'));
		// $this->db->insert($this->tableName, array('name' => 'player_reactication_day_cnt','value' => '1','note' =>'Number of days will the system reactivates the player after block in the game and website'));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('name' => 'self_exclusion_approval_day_cnt'));
		// $this->db->delete($this->tableName, array('name' => 'cool_off_approval_day_cnt'));
		// $this->db->delete($this->tableName, array('name' => 'deposit_limit_approval_day_cnt'));
		// $this->db->delete($this->tableName, array('name' => 'loss_limit_approval_day_cnt'));
		// $this->db->delete($this->tableName, array('name' => 'player_reactication_day_cnt'));
	}
}