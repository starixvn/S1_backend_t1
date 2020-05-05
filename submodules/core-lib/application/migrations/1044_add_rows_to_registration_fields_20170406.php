<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_rows_to_registration_fields_20170406 extends CI_Migration {

	public function up() {
		$data = array(
			array(
				'registrationFieldId' => 38,
				'type' => '2',
				'field_name' => 'City',
				'alias' => 'city',
				'visible' => '1',
				'required' => '1',
				'updatedOn' => '2017-03-28 15:00:00',
				'can_be_required' => '0',
			),
			array(
				'registrationFieldId' => 39,
				'type' => '2',
				'field_name' => 'Address',
				'alias' => 'address',
				'visible' => '1',
				'required' => '1',
				'updatedOn' => '2017-03-28 15:00:00',
				'can_be_required' => '0',
			),
		);
		$this->db->insert_batch('registration_fields', $data);
	}

	public function down() {
		$this->db->where_in('field_name', array('City','Address'));
		$this->db->where('type', '2');
		$this->db->delete('registration_fields');
	}
}