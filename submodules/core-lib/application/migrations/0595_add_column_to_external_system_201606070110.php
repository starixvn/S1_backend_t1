<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_external_system_201606070110 extends CI_Migration {

	public function up() {
		$fields = array(
			'allow_deposit_withdraw' => array(
				'type' => 'INT',
				'constraint' => 2,
				'null' => true,
			),
		);
		$this->dbforge->add_column('external_system', $fields);
		$this->dbforge->add_column('external_system_list', $fields);

		# Set all payment API to value 1 (allow deposit only)
		$data = array(
            'allow_deposit_withdraw' => 1,
        );

		$this->db->where('local_path', 'payment');
		$this->db->update('external_system', $data);
		$this->db->where('local_path', 'payment');
		$this->db->update('external_system_list', $data);

		# Set 24k payment API to value 2 (allow withdraw only)
		# At this time, 24K is the only gateway supporting withdraw
		$data = array(
            'allow_deposit_withdraw' => 2,
        );

		$this->db->where('id', PAY24K_PAYMENT_API);
		$this->db->update('external_system', $data);
		$this->db->where('id', PAY24K_PAYMENT_API);
		$this->db->update('external_system_list', $data);
	}

	public function down() {
		$this->dbforge->drop_column('external_system', 'allow_deposit_withdraw');
		$this->dbforge->drop_column('external_system_list', 'allow_deposit_withdraw');
	}
}