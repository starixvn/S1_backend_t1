<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_cashback_daily_to_functions_permissions_20160322 extends CI_Migration {

	// var $cashback_daily = 101;

	public function up() {

		// $this->db->trans_start();

		// $this->db->insert('functions', array(
		// 	'funcId' => $this->cashback_daily,
		// 	'funcName' => 'Cashback Daily',
		// 	'parentId' => 59,
		// 	'funcCode' => 'cashback_daily',
		// 	'sort' => $this->cashback_daily,
		// 	'createTime' => $this->utils->getNowForMysql(),
		// ));

		// $this->db->insert('rolefunctions_giving', array(
		// 	'roleId' => 1,
		// 	'funcId' => $this->cashback_daily,
		// ));

		// $this->db->insert('rolefunctions', array(
		// 	'roleId' => 1,
		// 	'funcId' => $this->cashback_daily,
		// ));

		// $this->db->trans_complete();
	}

	public function down() {
		// $this->db->trans_start();

		// $this->db->delete('rolefunctions_giving', array('funcId' => $this->cashback_daily));
		// $this->db->delete('rolefunctions', array('funcId' => $this->cashback_daily));
		// $this->db->delete('functions', array('funcId' => $this->cashback_daily));

		// $this->db->trans_complete();
	}
}