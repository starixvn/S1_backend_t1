<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_transfer_request_to_functions_permissions_20160523 extends CI_Migration {

	// private $transfer_request = 115;

	public function up() {
		// $this->db->trans_start();

		// $this->db->insert('functions', array(
		// 	'funcId' 		=> $this->transfer_request,
		// 	'funcName' 		=> 'Transfer Request',
		// 	'parentId' 		=> 72,
		// 	'funcCode' 		=> 'transfer_request',
		// 	'sort' 			=> 115,
		// 	'createTime' 	=> $this->utils->getNowForMysql(),
		// ));

		// $this->db->insert('rolefunctions_giving', array(
		// 	'roleId' => 1,
		// 	'funcId' => $this->transfer_request,
		// ));

		// $this->db->insert('rolefunctions', array(
		// 	'roleId' => 1,
		// 	'funcId' => $this->transfer_request,
		// ));

		// $this->db->trans_complete();
	}

	public function down() {
		// $this->db->trans_start();

		// $this->db->delete('rolefunctions_giving', array('funcId' => $this->transfer_request));
		// $this->db->delete('rolefunctions', array('funcId' => $this->transfer_request));
		// $this->db->delete('functions', array('funcId' => $this->transfer_request));

		// $this->db->trans_complete();
	}
}