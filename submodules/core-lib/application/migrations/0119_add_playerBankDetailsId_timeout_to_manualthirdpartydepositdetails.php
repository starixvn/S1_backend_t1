<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_playerBankDetailsId_timeout_to_manualthirdpartydepositdetails extends CI_Migration {

	public function up() {
		$fields = array(
			'playerBankDetailsId' => array(
				'type' => 'INT',
				'null' => true,
			),
			'timeout' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('manualthirdpartydepositdetails', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('manualthirdpartydepositdetails', 'playerBankDetailsId');
		$this->dbforge->drop_column('manualthirdpartydepositdetails', 'timeout');
		// $this->db->query('DELETE * FROM `manualthirdpartydepositdetails');
	}
}