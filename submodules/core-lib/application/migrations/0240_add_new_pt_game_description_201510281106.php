<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_new_pt_game_description_201510281106 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {
		// //The Riches Of  Don Quixote
		// $this->db->insert($this->tableName, array(
		// 	'game_code' => 'donq', 'game_name' => "pt.donq", 'external_game_id' => "donq",
		// 	'game_platform_id' => PT_API, 'game_type_id' => 7,
		// ));
		// //Fei Long Zai Tian
		// $this->db->insert($this->tableName, array(
		// 	'game_code' => 'gtsflzt', 'game_name' => "pt.gtsflzt", 'external_game_id' => "gtsflzt",
		// 	'game_platform_id' => PT_API, 'game_type_id' => 7,
		// ));
		// //Fortune Jump
		// $this->db->insert($this->tableName, array(
		// 	'game_code' => 'gtsfj', 'game_name' => "pt.gtsfj", 'external_game_id' => "gtsfj",
		// 	'game_platform_id' => PT_API, 'game_type_id' => 7,
		// ));
		// //Cat in Vegas
		// $this->db->insert($this->tableName, array(
		// 	'game_code' => 'ctiv', 'game_name' => "pt.ctiv", 'external_game_id' => "ctiv",
		// 	'game_platform_id' => PT_API, 'game_type_id' => 7,
		// ));
		// //Fei Cui Gong Zhu
		// $this->db->insert($this->tableName, array(
		// 	'game_code' => 'fcgz', 'game_name' => "pt.fcgz", 'external_game_id' => "fcgz",
		// 	'game_platform_id' => PT_API, 'game_type_id' => 7,
		// ));
		// //Nian Nian You Yu
		// $this->db->insert($this->tableName, array(
		// 	'game_code' => 'nian_k', 'game_name' => "pt.nian_k", 'external_game_id' => "nian_k",
		// 	'game_platform_id' => PT_API, 'game_type_id' => 7,
		// ));
		// //Sinbad's Golden Voyage
		// $this->db->insert($this->tableName, array(
		// 	'game_code' => 'ashsbd', 'game_name' => "pt.ashsbd", 'external_game_id' => "ashsbd",
		// 	'game_platform_id' => PT_API, 'game_type_id' => 7,
		// ));
		// //Thai Temple
		// $this->db->insert($this->tableName, array(
		// 	'game_code' => 'thtk', 'game_name' => "pt.thtk", 'external_game_id' => "thtk",
		// 	'game_platform_id' => PT_API, 'game_type_id' => 7,
		// ));
		// //The Great Ming Empire
		// $this->db->insert($this->tableName, array(
		// 	'game_code' => 'gtsgme', 'game_name' => "pt.gtsgme", 'external_game_id' => "gtsgme",
		// 	'game_platform_id' => PT_API, 'game_type_id' => 7,
		// ));
	}

	public function down() {
		// $codes = array('donq', 'gtsflzt', 'gtsfj','ctiv','fcgz','nian_k','ashsbd','thtk','gtsgme');
		// $this->db->where_in('game_code', $codes);
		// $this->db->where('game_platform_id', PT_API);
		// $this->db->delete($this->tableName);
	}
}