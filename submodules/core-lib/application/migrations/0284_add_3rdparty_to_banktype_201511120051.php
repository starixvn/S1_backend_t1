<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_3rdparty_to_banktype_201511120051 extends CI_Migration {

	private $tableName = 'banktype';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'external_system_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		));
		$this->db->query('create index idx_system_id on banktype(external_system_id)');

		// $this->load->model(array('users', 'external_system'));
		// $superAdmin = $this->users->getSuperAdmin();

		//insert all 3rd party
		// $this->external_system->syncToBanktype($superAdmin->userId);

	}

	public function down() {
		//delete bank_type_alipay and bank_type_wechat
		// $this->db->where_in('bankName', array('bank_type_alipay', 'bank_type_wechat'));
		// $this->db->delete($this->tableName);
		$this->db->query('delete from banktype where external_system_id is not null');
		$this->db->query('drop index idx_system_id on banktype');
		$this->dbforge->drop_column($this->tableName, 'external_system_id');
	}
}