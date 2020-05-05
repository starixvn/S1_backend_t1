<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_new_deposit_withdraw_to_functions_permissions_20151125 extends CI_Migration {

	// var $deposit_id = 89;
	// var $withdrawal_id = 90;

	public function up() {

		// $this->db->trans_start();


		// $this->db->insert('functions', array(
		// 	'funcId' => $this->deposit_id,
		// 	'funcName' => 'New Deposit',
		// 	'parentId' => 72,
		// 	'funcCode' => 'new_deposit',
		// 	'sort' => $this->deposit_id,
		// 	'createTime' => $this->utils->getNowForMysql(),
		// ));

		// $this->db->insert('rolefunctions_giving', array(
		// 	'roleId' => 1,
		// 	'funcId' => $this->deposit_id,
		// ));

		// $this->db->insert('rolefunctions', array(
		// 	'roleId' => 1,
		// 	'funcId' => $this->deposit_id,
		// ));

		// $this->db->insert('functions', array(
		// 	'funcId' => $this->withdrawal_id,
		// 	'funcName' => 'New Withdrawal',
		// 	'parentId' => 72,
		// 	'funcCode' => 'new_withdrawal',
		// 	'sort' => $this->withdrawal_id,
		// 	'createTime' => $this->utils->getNowForMysql(),
		// ));

		// $this->db->insert('rolefunctions_giving', array(
		// 	'roleId' => 1,
		// 	'funcId' => $this->withdrawal_id,
		// ));

		// $this->db->insert('rolefunctions', array(
		// 	'roleId' => 1,
		// 	'funcId' => $this->withdrawal_id,
		// ));

		// $this->db->trans_complete();
	}

	public function down() {
		// $this->db->trans_start();

		// $this->db->delete('rolefunctions_giving', array('funcId' => $this->deposit_id));
		// $this->db->delete('rolefunctions', array('funcId' => $this->deposit_id));
		// $this->db->delete('functions', array('funcId' => $this->deposit_id));

		// $this->db->delete('rolefunctions_giving', array('funcId' => $this->withdrawal_id));
		// $this->db->delete('rolefunctions', array('funcId' => $this->withdrawal_id));
		// $this->db->delete('functions', array('funcId' => $this->withdrawal_id));

		// $this->db->trans_complete();
	}
}