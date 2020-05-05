<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_deposit_list_to_functions_permissions_201509100957 extends CI_Migration {

	// private $depositListId = 85;

	public function up() {

		// $this->db->trans_start();

		// $this->db->query('create unique index idx_func_code on functions(funcCode)');

		// $this->db->insert('functions', array(
		// 	'funcId' => $this->depositListId,
		// 	'funcName' => 'Deposit List',
		// 	'parentId' => 47,
		// 	'funcCode' => 'deposit_list',
		// 	'sort' => 61,
		// 	'createTime' => $this->utils->getNowForMysql(),
		// ));

		// $this->db->insert('rolefunctions_giving', array(
		// 	'roleId' => 1,
		// 	'funcId' => $this->depositListId,
		// ));

		// $this->db->insert('rolefunctions', array(
		// 	'roleId' => 1,
		// 	'funcId' => $this->depositListId,
		// ));

		// $this->db->trans_complete();

		// if ($this->db->trans_status() === FALSE) {
		// 	throw new Exception('adding deposit list function was failed');
		// }
	}

	public function down() {
		// $this->db->trans_start();

		// $this->db->delete('rolefunctions_giving', array('funcId' => $this->depositListId));
		// $this->db->delete('rolefunctions', array('funcId' => $this->depositListId));
		// $this->db->delete('functions', array('funcId' => $this->depositListId));

		// $this->db->query('drop index idx_func_code on functions');

		// $this->db->trans_complete();

		// if ($this->db->trans_status() === FALSE) {
		// 	throw new Exception('deleting deposit list function was failed');
		// }

	}
}