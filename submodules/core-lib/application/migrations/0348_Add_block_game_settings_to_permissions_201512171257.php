<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_block_game_settings_to_permissions_201512171257 extends CI_Migration {

	// private $func_id = 92;

	public function up() {

		// $this->db->trans_start();

		// $this->db->insert('functions', array(
		// 	'funcId' => $this->func_id,
		// 	'funcName' => 'Block Game Setting',
		// 	'parentId' => 59,
		// 	'funcCode' => 'block_game_setting',
		// 	'sort' => $this->func_id,
		// 	'createTime' => $this->utils->getNowForMysql(),
		// ));

		// $this->db->insert('rolefunctions_giving', array(
		// 	'roleId' => 1,
		// 	'funcId' => $this->func_id,
		// ));

		// $this->db->insert('rolefunctions', array(
		// 	'roleId' => 1,
		// 	'funcId' => $this->func_id,
		// ));

		// $this->db->trans_complete();
	}

	public function down() {
		// $this->db->trans_start();
		// $this->db->delete('rolefunctions_giving', array('funcId' => $this->func_id));
		// $this->db->delete('rolefunctions', array('funcId' => $this->func_id));
		// $this->db->delete('functions', array('funcId' => $this->func_id));
		// $this->db->trans_complete();
	}
}