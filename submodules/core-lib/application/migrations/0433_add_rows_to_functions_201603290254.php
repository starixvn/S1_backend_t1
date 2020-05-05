<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_functions_201603290254 extends CI_Migration {

	public function up() {
		// $this->db->trans_start();

		// //function
		// $sql = "SELECT funcCode FROM functions where funcCode = 'affiliate_contact_info'";
		// $query = $this->db->query($sql);
		// $result = $query->row_array();

		// if (!$result) {
		// 	$data = array(
		// 		'funcId' => 102,
		// 		'funcName' => 'Affiliate Contact Information',
		// 		'parentId' => '48',
		// 		'funcCode' => 'affiliate_contact_info',
		// 		'sort' => '102',
		// 	);

		// 	$this->db->insert('functions', $data);
		// }

		// //rolefunctions
		// $sql = "SELECT roleId,funcId FROM rolefunctions where roleId = 1 and funcId = 102";
		// $query = $this->db->query($sql);
		// $result = $query->row_array();

		// if (!$result) {
		// 	$this->db->insert('rolefunctions', array('roleId' => 1, 'funcId' => 102));
		// }

		// //rolefunctions_giving
		// $sql = "SELECT roleId,funcId FROM rolefunctions_giving where roleId = 1 and funcId = 102";
		// $query = $this->db->query($sql);
		// $result = $query->row_array();

		// if (!$result) {
		// 	$this->db->insert('rolefunctions_giving', array('roleId' => 1, 'funcId' => 102));
		// }

		// $this->db->trans_complete();
	}

	public function down() {
		// $this->db->delete('functions', array('funcCode' => 'affiliate_contact_info'));
		// $this->db->delete('rolefunctions', array('roleId' => 1, 'funcId' => 102));
		// $this->db->delete('rolefunctions_giving', array('roleId' => 1, 'funcId' => 102));
	}
}