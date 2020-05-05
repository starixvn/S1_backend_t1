<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_functions_201603010533 extends CI_Migration {

	public function up() {
		// $this->db->trans_start();

		// //add tag_a_member
		// $data = array(
		// 	'funcId' => 98,
		// 	'funcName' => 'Edit Affiliate Domain Name',
		// 	'parentId' => '48',
		// 	'funcCode' => 'edit_affiliate_domain_name',
		// 	'sort' => '98',
		// );

		// $this->db->insert('functions', $data);

		// //add super admin permission
		// $this->db->insert('rolefunctions', array('roleId' => 1, 'funcId' => 98));
		// $this->db->insert('rolefunctions_giving', array('roleId' => 1, 'funcId' => 98));

		// $this->db->trans_complete();
	}

	public function down() {
		// $this->db->delete('functions', array('funcCode' => 'edit_affiliate_domain_name'));
		// $this->db->delete('rolefunctions', array('roleId' => 1, 'funcId' => 98));
		// $this->db->delete('rolefunctions_giving', array('roleId' => 1, 'funcId' => 98));
	}
}