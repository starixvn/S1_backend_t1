<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_export_all_username_to_functions_permissions_20160519 extends CI_Migration {

	// private $exportAllUsername = 114;

	public function up() {
		// $this->db->trans_start();

		// $this->db->insert('functions', array(
		// 	'funcId' 		=> $this->exportAllUsername,
		// 	'funcName' 		=> 'Export All Username',
		// 	'parentId' 		=> 15,
		// 	'funcCode' 		=> 'export_all_username',
		// 	'sort' 			=> 114,
		// 	'createTime' 	=> $this->utils->getNowForMysql(),
		// ));

		// $this->db->insert('rolefunctions_giving', array(
		// 	'roleId' => 1,
		// 	'funcId' => $this->exportAllUsername,
		// ));

		// $this->db->insert('rolefunctions', array(
		// 	'roleId' => 1,
		// 	'funcId' => $this->exportAllUsername,
		// ));

		// $this->db->trans_complete();
	}

	public function down() {
		// $this->db->trans_start();

		// $this->db->delete('rolefunctions_giving', array('funcId' => $this->exportAllUsername));
		// $this->db->delete('rolefunctions', array('funcId' => $this->exportAllUsername));
		// $this->db->delete('functions', array('funcId' => $this->exportAllUsername));

		// $this->db->trans_complete();
	}
}