<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_primary_key_and_change_int200_20161010 extends CI_Migration {
	
	public function up() {

		$this->db->query('ALTER TABLE migrations ADD PRIMARY KEY (version) ');
		$this->db->query('ALTER TABLE notification_setting ADD PRIMARY KEY (notification_id) ');

		$fields = array(
        		'today_deposit_sum' => array(
		                'type' => 'double',
		        ),
		);

		$this->dbforge->modify_column('admin_dashboard', $fields);

	}

	public function down() {

		$this->db->query('ALTER TABLE migrations DROP PRIMARY KEY');
		$this->db->query('ALTER TABLE notification_setting DROP PRIMARY KEY');

		$fields = array(
        		'today_deposit_sum' => array(
		                'type' => 'int(200)',
		        ),
		);

		$this->dbforge->modify_column('admin_dashboard', $fields);
	}
}
