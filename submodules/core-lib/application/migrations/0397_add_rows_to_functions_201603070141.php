<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_functions_201603070141 extends CI_Migration {

	public function up() {
		// $this->db->trans_start();

		// //function
		// $sql = "SELECT funcCode FROM functions where funcCode = 'edit_affiliate_domain_name'";
		// $query = $this->db->query($sql);
		// $result = $query->row_array();

		// if (!$result) {
		// 	$data = array(
		// 		'funcId' => 98,
		// 		'funcName' => 'Edit Affiliate Domain Name',
		// 		'parentId' => '48',
		// 		'funcCode' => 'edit_affiliate_domain_name',
		// 		'sort' => '98',
		// 	);

		// 	$this->db->insert('functions', $data);
		// }

		// //rolefunctions
		// $sql = "SELECT roleId,funcId FROM rolefunctions where roleId = 1 and funcId = 98";
		// $query = $this->db->query($sql);
		// $result = $query->row_array();

		// if (!$result) {
		// 	$this->db->insert('rolefunctions', array('roleId' => 1, 'funcId' => 98));
		// }

		// //rolefunctions_giving
		// $sql = "SELECT roleId,funcId FROM rolefunctions_giving where roleId = 1 and funcId = 98";
		// $query = $this->db->query($sql);
		// $result = $query->row_array();

		// if (!$result) {
		// 	$this->db->insert('rolefunctions_giving', array('roleId' => 1, 'funcId' => 98));
		// }

		// $this->db->trans_complete();
	}

	public function down() {
		// $this->db->delete('functions', array('funcCode' => 'edit_affiliate_domain_name'));
		// $this->db->delete('rolefunctions', array('roleId' => 1, 'funcId' => 98));
		// $this->db->delete('rolefunctions_giving', array('roleId' => 1, 'funcId' => 98));
	}
}