<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_game_type_role_function_2016003102230 extends CI_Migration {

	public function up() {
		// $this->db->trans_start();

		// //function
		// $sql = "SELECT funcCode FROM functions where funcCode = 'game_type'";
		// $query = $this->db->query($sql);
		// $result = $query->row_array();

		// if (!$result) {
		// 	$data = array(
		// 		'funcId' => 100,
		// 		'funcName' => 'Game Type',
		// 		'parentId' => '1',
		// 		'funcCode' => 'game_type',
		// 		'sort' => '12',
		// 	);

		// 	$this->db->insert('functions', $data);
		// }

		// //rolefunctions
		// $sql = "SELECT roleId,funcId FROM rolefunctions where roleId = 1 and funcId = 100";
		// $query = $this->db->query($sql);
		// $result = $query->row_array();

		// if (!$result) {
		// 	$this->db->insert('rolefunctions', array('roleId' => 1, 'funcId' => 100));
		// }

		// //rolefunctions_giving
		// $sql = "SELECT roleId,funcId FROM rolefunctions_giving where roleId = 1 and funcId = 100";
		// $query = $this->db->query($sql);
		// $result = $query->row_array();

		// if (!$result) {
		// 	$this->db->insert('rolefunctions_giving', array('roleId' => 1, 'funcId' => 100));
		// }

		// $this->db->trans_complete();
	}

	public function down() {
		// $this->db->delete('functions', array('funcCode' => 'game_type'));
		// $this->db->delete('rolefunctions', array('roleId' => 1, 'funcId' => 100));
		// $this->db->delete('rolefunctions_giving', array('roleId' => 1, 'funcId' => 100));
	}
}
