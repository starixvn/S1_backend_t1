<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Transactions_Report_to_functions_permissions_201512121627 extends CI_Migration {

		// private $transactions_report_id = 91;

	public function up() {

		// $this->db->trans_start();


		// $this->db->insert('functions', array(
		// 	'funcId' => $this->transactions_report_id,
		// 	'funcName' => 'Transactions Report',
		// 	'parentId' => 40,
		// 	'funcCode' => 'transactions_report',
		// 	'sort' => $this->transactions_report_id,
		// 	'createTime' => $this->utils->getNowForMysql(),
		// ));

		// $this->db->insert('rolefunctions_giving', array(
		// 	'roleId' => 1,
		// 	'funcId' => $this->transactions_report_id,
		// ));

		// $this->db->insert('rolefunctions', array(
		// 	'roleId' => 1,
		// 	'funcId' => $this->transactions_report_id,
		// ));

		// $this->db->trans_complete();
	}

	public function down() {
		// $this->db->trans_start();
		// $this->db->delete('rolefunctions_giving', array('funcId' => $this->transactions_report_id));
		// $this->db->delete('rolefunctions', array('funcId' => $this->transactions_report_id));
		// $this->db->delete('functions', array('funcId' => $this->transactions_report_id));
		// $this->db->trans_complete();
	}
}