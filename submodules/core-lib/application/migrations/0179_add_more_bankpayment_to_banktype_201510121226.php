<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_more_bankpayment_to_banktype_201510121226 extends CI_Migration {

	private $tableName = 'banktype';

	public function up() {

		$this->load->model(array('users'));

		$superAdmin = $this->users->getSuperAdmin();

		$this->db->insert_batch($this->tableName, array(
			array(
				'bankName' => 'bank_type_alipay',
				'createdOn' => $this->utils->getNowForMysql(),
				'updatedOn' => $this->utils->getNowForMysql(),
				'status' => 'active',
				'createdBy' => $superAdmin->userId,
				'updatedBy' => $superAdmin->userId,
			),
			array(
				'bankName' => 'bank_type_wechat',
				'createdOn' => $this->utils->getNowForMysql(),
				'updatedOn' => $this->utils->getNowForMysql(),
				'status' => 'active',
				'createdBy' => $superAdmin->userId,
				'updatedBy' => $superAdmin->userId,
			),
		));

		$this->db->where('createdOn is null', null, false);
		$this->db->update($this->tableName, array(
			'createdOn' => $this->utils->getNowForMysql(),
			'updatedOn' => $this->utils->getNowForMysql(),
			'createdBy' => $superAdmin->userId,
			'updatedBy' => $superAdmin->userId,
		));

	}

	public function down() {
		// $this->dbforge->drop_table('');
		//delete bank_type_alipay and bank_type_wechat
		$this->db->where_in('bankName', array('bank_type_alipay', 'bank_type_wechat'));
		$this->db->delete($this->tableName);
	}
}