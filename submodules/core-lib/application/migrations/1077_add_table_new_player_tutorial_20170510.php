<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_new_player_tutorial_20170510 extends CI_Migration {

	private $tableName = 'new_player_tutorial';

	public function up() {
		$fields = array(
			"id" => array(
				'type' => 'INT',
				'constraint' => 3,
				'auto_increment' => true,
			),
			'step' => array(
				'type' => 'INT',
				'constraint' => 3,
				'null' => false,
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => 45,
				'null' => false,
			),
			'description' => array(
				'type' => 'VARCHAR',
				'constraint' => 500,
				'null' => false,
			),
			'icon' => array(
				'type' => 'VARCHAR',
				'constraint' => 45,
			),
			'status' => array(
				'type' => 'INT',
				'constraint' => 8,
			),
			'created_on' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'created_by' => array(
				'type' => 'VARCHAR',
				'constraint' => 45,
				'null' => false,
			),
			'updated_on' => array(
				'type' => 'DATETIME',
			),
			'updated_by' => array(
				'type' => 'VARCHAR',
				'constraint' => 45,
				'null' => false,
			),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->tableName);

		$this->load->library("utils");
		$now = $this->utils->getNowForMysql();
		$data = [
			"tutorial_1" =>array(
				"step" => "1",
				"name" => "tutorial.profile_overview",
				"description" => "This is to view the Profile overview of the Player",
				"icon" => "default_promo_cms_1.jpg",
				"status" => "0",
			),
			"tutorial_2" => array(
				"step" => "2",
				"name" => "tutorial.points_overview",
				"description" => "This is to view the Points overview of the Player",
				"icon" => "default_promo_cms_1.jpg",
				"status" => "0",
				),
			"tutorial_3" => array(
				"step" => "3",
				"name" => "tutorial.rewards_overview",
				"description" => "This is to view the Rewards overview of the Player",
				"icon" => "default_promo_cms_1.jpg",
				"status" => "0",
				),
			"tutorial_4" => array(
				"step" => "4",
				"name" => "tutorial.fund_overview",
				"description" => "This is to view the Fund overview of the Player",
				"icon" => "default_promo_cms_1.jpg",
				"status" => "0",
				),
		];

		foreach ($data as $tutorials) {
			// $tutorials["created_on"] = $now;
			// $tutorials["created_by"] = "admin";
			// $tutorials["updated_on"] = $now;
			// $tutorials["updated_by"] = "admin";

			$this->db->insert($this->tableName, $tutorials);
		}
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}