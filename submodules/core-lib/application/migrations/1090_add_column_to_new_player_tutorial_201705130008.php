<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_new_player_tutorial_201705130008 extends CI_Migration {

	public function up() {
		$this->dbforge->drop_column('new_player_tutorial', 'name');

		$fields = array(
			'name_english' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
			),
			'name_chinese' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
			),
		);

		$this->dbforge->add_column('new_player_tutorial', $fields);

		$datas = [
			"1" =>array(
				"id" => "1",
				'name_english' => "Profile Overview",
				"name_chinese" => "简介概述",
			),
			"2" =>array(
				"id" => "2",
				'name_english' => "Points Overview",
				"name_chinese" => "要点概述",
			),
			"3" =>array(
				"id" => "3",
				'name_english' => "Rewards Overview",
				"name_chinese" => "奖励概览",
			),
			"4" =>array(
				"id" => "4",
				'name_english' => "Fund Overview",
				"name_chinese" => "资金概览",
			),
			
		]; 

		$this->db->update_batch('new_player_tutorial', $datas, 'id');
	}

	public function down() {
		$fields = array(
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
			),
		);

		$this->dbforge->add_column('new_player_tutorial', $fields);

        $this->dbforge->drop_column('new_player_tutorial', 'name_english');
        $this->dbforge->drop_column('new_player_tutorial', 'name_chinese');
	}
}