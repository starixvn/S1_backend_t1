<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_new_player_tutorial_201705112119 extends CI_Migration {

	public function up() {
		$fields = array(
			'code' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'title' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'position' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
		);

		$this->dbforge->add_column('new_player_tutorial', $fields);

		$datas = [
			"1" =>array(
				"id" => "1",
				'icon' => "user.svg",
				"code" => "overview-profile",
				"title" => "Profile",
				"position" => "right",
			),
			"2" =>array(
				"id" => "2",
				'icon' => "coins.svg",
				"code" => "overview-points",
				"title" => "Points",
				"position" => "right",
			),
			"3" =>array(
				"id" => "3",
				'icon' => "coins.svg",
				"code" => "overview-rewards",
				"title" => "Rewards",
				"position" => "left",
			),
			"4" =>array(
				"id" => "4",
				'icon' => "coins.svg",
				"code" => "fundManagement",
				"title" => "Fund Management",
				"position" => "left",
			),
			
		]; 

		$this->db->update_batch('new_player_tutorial', $datas, 'id');
	}

	public function down() {
		$this->dbforge->drop_column('new_player_tutorial', 'code');
        $this->dbforge->drop_column('new_player_tutorial', 'title');
        $this->dbforge->drop_column('new_player_tutorial', 'position');
	}
}